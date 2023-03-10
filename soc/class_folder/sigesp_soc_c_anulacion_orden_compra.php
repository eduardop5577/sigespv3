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

class sigesp_soc_c_anulacion_orden_compra
{
  public function __construct($as_path)
  {
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_anulacion_orden_compra
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. N?stor Falc?n.
	// Fecha Creaci?n: 09/06/2007 								Fecha ?ltima Modificaci?n : 03/06/2007 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_include			= new sigesp_include();
		$this->io_conexion	= $io_include->uf_conectar();
		$this->io_sql       = new class_sql($this->io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
  }

function uf_load_ordenes_compra($as_numordcom,$ad_fecdes,$ad_fechas,$as_codpro)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_ordenes_compra
//         Access: public
//      Argumento: 
//   $as_numordcom //N?mero de la Orden de Compra (Bien o Servicio.)
//      $ad_fecdes //Fecha desde el cual buscaremos las Ordenes de Compra.
//      $ad_fechas //Fecha hasta el cual buscaremos las Ordenes de Compra.
//      $as_codpro //C?digo del Proveedor asociado a la Orden de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulaci?n. 
//	   Creado Por: Ing. N?stor Falc?n.
// Fecha Creaci?n: 06/03/2007							Fecha ?ltima Modificaci?n : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
  $ls_straux = "";
  if (!empty($ad_fecdes) && !empty($ad_fechas))
     {
	   $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
       $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
	   $ls_straux = " AND soc_ordencompra.fecordcom BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'"; 
	 }
  //FILTRO POR ESTRUCTURA CASO BAER 
	$ls_filtroest = '';
	if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
		$ls_estconcat = $this->io_conexion->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
		$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
		                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
		                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) 
		                  AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos 
		                  							        WHERE sss_permisos_internos.codemp='{$this->ls_codemp}'".
			"                                				  AND codsis='SOC'".
			"                                                 AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
	}
  //FILTRO POR ESTRUCTURA CASO BAER
  $ls_sql = "SELECT soc_ordencompra.numordcom,soc_ordencompra.cod_pro,	   					".
            "       soc_ordencompra.fecordcom,soc_ordencompra.obscom,						".
			"       COALESCE(soc_ordencompra.numanacot,'-') as numanacot,					".
            "       soc_ordencompra.estcondat,soc_ordencompra.montot,rpc_proveedor.nompro   ".			
		    "  FROM soc_ordencompra , rpc_proveedor											".
			" WHERE soc_ordencompra.codemp='".$this->ls_codemp."'							".
			"   AND soc_ordencompra.numordcom like '%".$as_numordcom."%' 					".
			"   AND soc_ordencompra.cod_pro like '%".$as_codpro."%' 						".
			"   AND soc_ordencompra.estcom='1' 										        ".
			"   AND soc_ordencompra.estapro = '1'											".
			"       $ls_straux																".
			"   AND soc_ordencompra.numordcom <> '000000000000000'							".
			"   AND soc_ordencompra.codemp=rpc_proveedor.codemp             				".
  	  	    "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro           				".$ls_filtroest.
			"ORDER BY soc_ordencompra.numordcom ASC	 			            				";
    $rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$lb_valido=false;
		$this->io_msg->message("CLASE->sigesp_soc_c_anulacion_orden_compra.M?TODO->uf_load_ordenes_compra.ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
	}
	return $rs_data;
} // end  function uf_load_ordenes_compra

function uf_update_estatus_orden_compra($ai_totrows,$aa_seguridad)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_update_estatus_orden_compra
//         Access: public
//      Argumento: $ai_totrows = Total de filas dispuestas para su anulaci?n.
//                 $aa_seguridad = Arreglo cargado con la informacion de la pantalla, usuario, entre otros.
//     $as_totrows //Total de Ordenes de Compra.
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulaci?n. 
//	   Creado Por: Ing. N?stor Falc?n.
// Fecha Creaci?n: 06/03/2007							Fecha ?ltima Modificaci?n : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  if ($ai_totrows>0)
     {
	   $this->io_sql->begin_transaction();
	   for ($i=1;$i<=$ai_totrows;$i++)
	       {
			 if (array_key_exists("chk".$i,$_POST))
			    {
			      $ls_numordcom = str_pad($_POST["txtnumord".$i],15,0,0);
                  $ls_codpro    = str_pad($_POST["hidcodpro".$i],10,0,0);
			      $ls_tipordcom = $_POST["txttipordcom".$i];
			      if ($ls_tipordcom=='Bienes')
			         { 
				       $ls_tipordcom = 'B';
			      	 }
			      elseif($ls_tipordcom=='Servicios')
			         {
				       $ls_tipordcom = 'S';
				     }
			      $ld_fecordcom = $_POST["txtfecordcom".$i];
		          $ld_fecordcom = $this->io_funciones->uf_convertirdatetobd($ld_fecordcom);
				  $ls_numanacot = trim($_POST["hidnumanacot".$i]);
				  
			      $ls_sql       = "UPDATE soc_ordencompra 			     ".
			                      "   SET estcom='3'      			     ".
			                      " WHERE codemp='".$this->ls_codemp."'  ".
							      "   AND numordcom='".$ls_numordcom."'  ".
					              "   AND cod_pro='".$ls_codpro."'       ".
								  "   AND estcondat ='".$ls_tipordcom."' ".
							      "   AND fecordcom='".$ld_fecordcom."'  ";
				  $rs_data = $this->io_sql->execute($ls_sql);
				  if ($rs_data===false)
					 {
					   $lb_valido = false;
					   $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_update_estatus_orden_compra.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					   break;
					 }
				  else
					 {
						/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
						$ls_descripcion ="Anul? la Orden de Compra Nro. $ls_numordcom de tipo $ls_tipordcom asociada a la empresa ".$this->ls_codemp;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
					    $this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
						if($this->ls_supervisor!=0)
						{
							$ls_fromname="Orden de Compra";
							$ls_bodyenv="Se le envia la notificaci?n de actualizaci?n en el modulo de SOC, se anul? la orden de compra  N?.. ";
							$ls_nomper=$_SESSION["la_nomusu"];
							$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$ls_numordcom,$ls_bodyenv,$ls_nomper);
						}
						/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					   $lb_valido = $this->uf_delete_enlace_sep($ls_numordcom,$ls_codpro,$ls_tipordcom,$ls_numanacot,$aa_seguridad);
					 }
				}
		   }
	    if ($lb_valido)
	       {
		     $this->io_sql->commit();
			 $this->io_mensajes->message("Operaci?n realizada con ?xito !!!");
		     $this->io_sql->close();

		   } 
	    else
		   {
		     $this->io_sql->rollback();
			 $this->io_mensajes->message("Error  en Operaci?n !!!");
		     $this->io_sql->close();
		   }
	 }
  return $lb_valido;
}

function uf_delete_enlace_sep($as_numordcom,$as_codpro,$as_tipordcom,$as_numanacot,$aa_seguridad)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_delete_enlace_sep
//         Access: public
//      Argumento: 
//   $as_numordcom //N?mero de la Orden de Compra (Bien o Servicio.)
//      $as_codpro //C?digo del Proveedor asociado a la Orden de Compra.
//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulaci?n. 
//	   Creado Por: Ing. N?stor Falc?n.
// Fecha Creaci?n: 06/06/2007							Fecha ?ltima Modificaci?n : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  $ls_sql    = " SELECT numsol ".
               "   FROM soc_enlace_sep ".
		       "  WHERE codemp='".$this->ls_codemp."' AND numordcom='".$as_numordcom."' AND estcondat='".$as_tipordcom."'";
  $rs_datos = $this->io_sql->select($ls_sql);
  if ($rs_datos===false)
     {
	   $lb_valido = false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_delete_enlace_sep.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	   echo $this->io_sql->message;
	 }		    
  else
     {
	   while ($row=$this->io_sql->fetch_row($rs_datos))
	         {
			   $ls_numsol = str_pad($row["numsol"],15,0,0);
			   if ($as_numanacot=='-')//Indica que la Orden de Compra no proviene de un An?lisis de Cotizaci?n.
			      {
				    $lb_valido = $this->uf_update_estatus_incorporacion_item($ls_numsol,$as_numordcom,$as_tipordcom);
				    if ($lb_valido)
					   {
						  if ($as_tipordcom=='B')
							 {
							   $ls_tabla = "sep_dt_articulos";
							 } 
						  elseif($as_tipordcom=='S')
							 {
							   $ls_tabla = "sep_dt_servicio";
							 } 
						 $ls_sql = "SELECT sep_solicitud.numsol 
									  FROM sep_solicitud, $ls_tabla 
									 WHERE sep_solicitud.codemp='".$this->ls_codemp."' 
									   AND sep_solicitud.numsol='".$ls_numsol."'
									   AND $ls_tabla.estincite<>'NI'
							           AND sep_solicitud.codemp=$ls_tabla.codemp
							           AND sep_solicitud.numsol=$ls_tabla.numsol";
						 $rs_data = $this->io_sql->select($ls_sql);
						 if ($rs_data===false)
						    {
						      $lb_valido = false;
						      $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_delete_enlace_sep.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
							  echo $this->io_sql->message;
							}					   
					     else
						    {
							  $li_totrows = $this->io_sql->num_rows($rs_data);
							  if ($li_totrows<=0)
							     {
								   $ls_sql  = "UPDATE sep_solicitud SET estsol='C' WHERE codemp='".$this->ls_codemp."' AND numsol='".$ls_numsol."'";		                 
								   $rs_dato = $this->io_sql->execute($ls_sql);
								   if ($rs_dato===false)
									  {
									    $lb_valido = false;
									    $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_delete_enlace_sep.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
										echo $this->io_sql->message;
									  }
								   else
									  {
										/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
										$ls_descripcion ="Actualiz? la SEP Nro. $ls_numsol, con el  estatus C = Contabilizada, asociada a la empresa ".$this->ls_codemp;
										$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
																		$aa_seguridad["sistema"],"UPDATE",$aa_seguridad["logusr"],
																		$aa_seguridad["ventanas"],$ls_descripcion);
										/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
									  }
								 }
							} 
					   }
				  }			     
	           $ls_sql = " DELETE
					         FROM soc_enlace_sep
					        WHERE codemp='".$this->ls_codemp."'
					          AND numordcom='".$as_numordcom."'
					          AND estcondat='".$as_tipordcom."' 
							  AND numsol='".$ls_numsol."'"; 
			   $rs_dato = $this->io_sql->execute($ls_sql);
			   if ($rs_dato===false)
				  {
				    $lb_valido = false;
				    $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_delete_enlace_sep.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				  }
			   else
			      {
					/////////////////////////////////         SEGURIDAD               /////////////////////////////////////////		
					$ls_descripcion ="Elimin? el Enlace de la SEP Nro. $ls_numsol, con la Orden de Compra Nro. $as_numordcom de tipo $as_tipordcom asociada a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],"DELETE",$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////				   
				  }
			 }
	 }
  return $lb_valido;
}

function uf_update_estatus_incorporacion_item($as_numsol,$as_numordcom,$as_tipordcom)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_update_estatus_incorporacion_item
//         Access: public
//      Argumento: 
//   $as_numordcom //N?mero de la Orden de Compra (Bien o Servicio.)
//      $as_codpro //C?digo del Proveedor asociado a la Orden de Compra.
//   $as_tipordcom //Tipo de Orden de Compra (Bien o Servicio).
//	      Returns: Retorna un resulset
//    Description: Funcion que carga la Ordenes de Compra dispuestas para el proceso de Anulaci?n. 
//	   Creado Por: Ing. N?stor Falc?n.
// Fecha Creaci?n: 06/06/2007							Fecha ?ltima Modificaci?n : 09/06/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;
  if ($as_tipordcom=='B')
     {
	   $ls_tabla = "sep_dt_articulos";
	   $ls_campo = "codart";
	 } 
  elseif($as_tipordcom=='S')
     {
	   $ls_tabla = "sep_dt_servicio";
	   $ls_campo = "codser";
	 } 
  $ls_sql    = "SELECT $ls_campo FROM $ls_tabla WHERE codemp='".$this->ls_codemp."' AND numsol='".$as_numsol."' AND estincite='OC' AND numdocdes='".$as_numordcom."'";//print $ls_sql;  
  $rs_result = $this->io_sql->select($ls_sql);
  if ($rs_result===false)
     {
	   $lb_valido= false;
	   $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_update_estatus_incorporacion_item.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	 }
  else
     {
	   while ($row=$this->io_sql->fetch_row($rs_result))
	         {
			   $ls_row = $row["$ls_campo"];
			   $ls_sql = "UPDATE $ls_tabla SET estincite='NI', numdocdes='' ".
						 " WHERE codemp='".$this->ls_codemp."'".
						 "   AND numsol='".$as_numsol."'      ".
						 "   AND $ls_campo='".$ls_row."'      ".
						 "   AND numdocdes='".$as_numordcom."'".
						 "   AND estincite='OC'				  ";
			 
			   $rs_dato = $this->io_sql->execute($ls_sql);
			   if ($rs_dato===false)
			      {
				    $lb_valido = false;
	                $this->io_mensajes->message("CLASE->sigesp_soc_c_anulacion_orden_compra.php->M?TODO->uf_update_estatus_incorporacion_item.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				  } 
			 }
	 }
  return $lb_valido; 
}
//-----------------------------------------------------------------------------------------------------------------------------------	
function uf_nivel_aprobacion_usu($as_codusu,$as_codtipniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  N?mero de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/02/2007 								Fecha ?ltima Modificaci?n : 
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
		//	    Arguments: as_numsol  //  N?mero de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/02/2007 								Fecha ?ltima Modificaci?n : 
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
		//	    Arguments: as_numsol  //  N?mero de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/02/2007 								Fecha ?ltima Modificaci?n : 
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
