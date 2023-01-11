<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_soc_c_anulacion_registro_cotizacion
{
  public function __construct($as_path)
  {
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_anulacion_registro_cotizacion
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 09/06/2007 								Fecha Última Modificación : 03/06/2007 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_include			= new sigesp_include();
		$io_conexion		= $io_include->uf_conectar();
		$this->io_sql       = new class_sql($io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
  }

function uf_load_registros_cotizacion($as_numcot,$ad_fecdes,$ad_fechas,$as_codpro)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_registros_cotizacion
//         Access: public
//      Argumento: 
//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
//      $ad_fecdes //Fecha desde el cual buscaremos las Ordenes de Compra.
//      $ad_fechas //Fecha hasta el cual buscaremos las Ordenes de Compra.
//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 06/03/2007							Fecha Última Modificación : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
  $ls_straux = "";
  if (!empty($ad_fecdes) && !empty($ad_fechas))
     {
	   $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
       $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
	   $ls_straux = " AND soc_cotizacion.feccot BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'"; 
	 }
  $ls_sql = "SELECT soc_cotizacion.numcot, soc_cotizacion.feccot, rpc_proveedor.nompro,soc_analisicotizacion.numanacot,".
  			"       soc_cotizacion.obscot, soc_cotizacion.cod_pro,soc_analisicotizacion.tipsolcot ".			
		    "  FROM soc_cotizacion , rpc_proveedor,soc_analisicotizacion, soc_cotxanalisis".
			" WHERE soc_cotizacion.codemp='".$this->ls_codemp."'".
			"   AND soc_cotizacion.numcot like '%".$as_numcot."%'".
			"   AND soc_cotizacion.cod_pro like '%".$as_codpro."%'".
			"   AND soc_analisicotizacion.estana= '2'".
			"   AND soc_cotizacion.estcot= '0'".
			"   AND soc_cotizacion.codemp=rpc_proveedor.codemp".
  	  	    "   AND soc_cotizacion.cod_pro=rpc_proveedor.cod_pro".
			"   AND soc_cotizacion.codemp=soc_cotxanalisis.codemp".
			"   AND soc_cotizacion.numcot=soc_cotxanalisis.numcot".
			"   AND soc_cotizacion.cod_pro=soc_cotxanalisis.cod_pro".
			"   AND soc_analisicotizacion.codemp=soc_cotxanalisis.codemp".
			"   AND soc_analisicotizacion.numanacot=soc_cotxanalisis.numanacot".
			" ORDER BY soc_cotizacion.numcot";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$lb_valido=false;
		$this->io_mensajes	->message("CLASE->sigesp_soc_c_anulacion_orden_compra.MÉTODO->uf_load_registros_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
	}
	return $rs_data;
} // end  function uf_load_ordenes_compra

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_load_monto_cotizacion_nivel($as_numanacot,$as_tipanacot)
{
		///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_analisis_cotizacion
		//		   Access: public
		//		 Argument: 
		//   $as_numanacot //Número del Análisis de Cotizacion
		//      $ad_fecdes //Fecha a partir del cual comenzará la búsqueda de los Análisis de Cotizacion
		//      $ad_fechas //Fecha hasta el cual comenzará la búsqueda de los Análisis de Cotizacion
		//   $as_tipanacot//Tipo del Analisis de Cotizacion B=Bienes , S=Servicios.
		//      $as_tipope //Tipo de la Operación a ejecutar A=Aprobacion, R=Reverso de la Aprobación.
		//	  Description: Función que busca los Analisis de Cotizacion que esten dispuestas para Aprobacion/Reverso.
		//	   Creado Por: Ing. Laura Cabre
		// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
        $ai_montocot = 0;
		
		if ($as_tipanacot=='Bienes')//Aprobacion
		   {  
				 $ls_sql ="SELECT SUM(montotart) as monto
							FROM soc_dtcot_bienes, soc_dtac_bienes
							WHERE soc_dtcot_bienes.codemp=soc_dtac_bienes.codemp
							AND soc_dtac_bienes.numanacot='".$as_numanacot."'
							AND soc_dtcot_bienes.codart=soc_dtac_bienes.codart
							AND soc_dtcot_bienes.numcot=soc_dtac_bienes.numcot
							AND soc_dtcot_bienes.cod_pro=soc_dtac_bienes.cod_pro";
		   }
		elseif($as_tipanacot=='Servicios')//Reverso.
		   {
				$ls_sql ="SELECT SUM(montotser) as monto
							FROM soc_dtcot_servicio, soc_dtac_servicios
							WHERE soc_dtcot_servicio.codemp=soc_dtac_servicios.codemp
							AND soc_dtac_servicios.numanacot='".$as_numanacot."'
							AND soc_dtcot_servicio.codser=soc_dtac_servicios.codser
							AND soc_dtcot_servicio.numcot=soc_dtac_servicios.numcot
							AND soc_dtcot_servicio.cod_pro=soc_dtac_servicios.cod_pro";		  
		   }
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->MÉTODO->uf_load_monto_cotizacion_nivel.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_montocot=$row["monto"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_montocot;

	}// end function uf_load_ordenes_compra
//-----------------------------------------------------------------------------------------------------------------------------------	


function uf_update_estatus_registro_cotizacion($ai_totrows,$aa_seguridad)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_update_estatus_registro_cotizacion
//         Access: public
//      Argumento: $ai_totrows = Total de filas dispuestas para su anulación.
//                 $aa_seguridad = Arreglo cargado con la informacion de la pantalla, usuario, entre otros.
//     $as_totrows //Total de Ordenes de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 06/03/2007							Fecha Última Modificación : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  if ($ai_totrows>0)
     {
	   $this->io_sql->begin_transaction();
	   for ($i=1;$i<=$ai_totrows;$i++)
	       {
			 if (array_key_exists("chk".$i,$_POST))
			    {
			      $ls_numcot = $_POST["txtnumord".$i];
			      $ls_codpro = $_POST["codpro".$i];
				  
			      $ls_sql="UPDATE soc_cotizacion".
			              "   SET estcot='2'".
			              " WHERE codemp='".$this->ls_codemp."'".
						  "   AND numcot='".$ls_numcot."'".
						  "   AND cod_pro='".$ls_codpro."'";
				  $rs_data = $this->io_sql->execute($ls_sql);
				  if ($rs_data===false)
					 {
					   $lb_valido = false;
					   $this->io_mensajes->message("CLASE->uf_update_estatus_analisis_cotizacion.php->MÉTODO->uf_update_estatus_registro_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					   break;
					 }
				  else
					 {
						/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
						$ls_descripcion ="Anuló el Analisis de Cotizacion Nro. $ls_numanacot asociada a la empresa ".$this->ls_codemp;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
					   $lb_valido = $this->uf_liberar_solicitud_cotizacion($ls_numcot,$ls_codpro,$aa_seguridad);
					 }
				}
		   }
	    if ($lb_valido)
	       {
		     $this->io_sql->commit();
			 $this->io_mensajes->message("Operación realizada con éxito !!!");
		     $this->io_sql->close();

		   } 
	    else
		   {
		     $this->io_sql->rollback();
			 $this->io_mensajes->message("Error  en Operación !!!");
		     $this->io_sql->close();
		   }
	 }
  return $lb_valido;
}

function uf_liberar_solicitud_cotizacion($as_numcot,$as_codpro,$aa_seguridad)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_liberar_solicitud_cotizacion
	//         Access: public
	//      Argumento: 
	//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
	//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
	//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
	//	      Returns: Retorna un resulset
	//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 06/06/2007							Fecha Última Modificación : 09/06/2007
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido= true;
	$ls_sql= "SELECT numsolcot ".
			 "  FROM soc_cotizacion ".
			 " WHERE codemp='".$this->ls_codemp."'".
			 "   AND numcot='".$as_numcot."'".
			 "   AND cod_pro='".$as_codpro."'";
	$rs_datos = $this->io_sql->select($ls_sql);
	if ($rs_datos===false)
	{
		$lb_valido = false;
		$this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->MÉTODO->uf_liberar_solicitud_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		echo $this->io_sql->message;
	}		    
	else
	{
		while ($row=$this->io_sql->fetch_row($rs_datos))
		{
			$ls_numsolcot = $row["numsolcot"];
			$lb_valido = $this->uf_update_estatus_cotizacion($as_numcot,$ls_numsolcot,$aa_seguridad);
		}
	}
  return $lb_valido;
}

function uf_update_estatus_cotizacion($as_numcot,$as_numsolcot,$aa_seguridad)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_update_estatus_incorporacion_item
	//         Access: public
	//      Argumento: 
	//   $as_numordcom //Número de la Orden de Compra (Bien o Servicio.)
	//      $as_codpro //Código del Proveedor asociado a la Orden de Compra.
	//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
	//	      Returns: Retorna un resulset
	//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulación. 
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 06/06/2007							Fecha Última Modificación : 09/06/2007
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido = true;
	$ls_sql="UPDATE soc_sol_cotizacion SET estcot='0' ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND numsolcot='".$as_numsolcot."'";
	$rs_dato = $this->io_sql->execute($ls_sql);
	if ($rs_dato===false)
	{
		$lb_valido = false;
		$this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->MÉTODO->uf_update_estatus_incorporacion_item.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	}
	else
	{
		/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
		$ls_descripcion ="Liberó la solicitud de Cotizacion $as_numsolcot asociada a la  Cotizacion Nro. $as_numcot asociada a la empresa ".$this->ls_codemp;
		$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
		/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
	}
  return $lb_valido; 
}
//-----------------------------------------------------------------------------------------------------------------------------------	
function uf_nivel_aprobacion_usu($as_codusu,$as_codtipniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_codniv="";
		$ls_sql="SELECT codasiniv ".
				"  FROM sss_niv_usuarios ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codusu='".$as_codusu."' ".
				"   AND codtipniv='".$as_codtipniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->uf_nivel_aprobacion_usu ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codniv=$row["codasiniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_codniv;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel_aprobacion_montohasta($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ai_monhas=0;
		$ls_sql="SELECT monnivhas ".
				"  FROM sigesp_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monhas=$row["monnivhas"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_monhas;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_nivel="";
		$ls_sql="SELECT codniv ".
				"  FROM sigesp_asig_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codasiniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_nivel=$row["codniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_nivel;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------


}
?>
