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

class sigesp_soc_c_aprobacion_analisis_cotizacion
{
  public function __construct($as_path)
  {
	////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: sigesp_soc_c_aprobacion_analisis_cotizacion
	//		   Access: public 
	//	  Description: Constructor de la Clase
	//	   Creado Por: Ing. Laura Cabré
	// Fecha Creación: 05/08/2007 								Fecha Última Modificación : 29/05/2007 
	////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");
		$io_include			= new sigesp_include();
		$io_conexion		= $io_include->uf_conectar();
		$this->io_sql       = new class_sql($io_conexion);	
		$this->io_mensajes  = new class_mensajes();		
		$this->io_fecha  = new class_fecha();		
		$this->io_funciones = new class_funciones();	
		$this->io_seguridad = new sigesp_c_seguridad();
		$this->ls_codemp    = $_SESSION["la_empresa"]["codemp"];
  }

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_analisis_cotizacion($as_numanacot,$ad_fecdes,$ad_fechas,$as_tipanacot,$as_tipope)
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
        $ls_straux = "";
		
        if (!empty($as_numanacot))
		   {
		     $ls_straux = " AND numanacot LIKE '%".$as_numanacot."%'";
		   } 
		if (!empty($ad_fecdes) && !empty($ad_fechas))
		   {  
		     $ld_fecdes = $this->io_funciones->uf_convertirdatetobd($ad_fecdes);
			 $ld_fechas = $this->io_funciones->uf_convertirdatetobd($ad_fechas);
			 $ls_straux = $ls_straux." AND fecanacot BETWEEN '".$ld_fecdes."' AND '".$ld_fechas."'";
		   }
		if ($as_tipanacot!='-')
		   {  
		     $ls_straux = $ls_straux." AND tipsolcot='".$as_tipanacot."'";
		   }
		if ($as_tipope=='A')//Aprobacion
		   {  
		     $ls_straux = $ls_straux." AND estana='0'";
		   }
		elseif($as_tipope=='R')//Reverso.
		   {
			 $ls_straux = $ls_straux." AND estana='1' AND numanacot not in (SELECT CASE WHEN numanacot IS NULL THEN '------' ELSE numanacot END FROM soc_ordencompra WHERE codemp='$this->ls_codemp')";
		   }
		$ls_sql ="SELECT numanacot,obsana,fecanacot,tipsolcot,fecapro
				 FROM soc_analisicotizacion
		         WHERE codemp='$this->ls_codemp'
				 $ls_straux
				 ORDER BY numanacot ASC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->MÉTODO->uf_load_analisis_cotizacion.ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_ordenes_compra
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($ai_totrows,$as_tipope,$ad_fecope,$aa_seguridad)
	{
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_guardar
	//		   Access: public
	//		 Argument: 
	//     $ai_totrows //Total de elementos cargados en el Grid de Analisis de Cotizacion
	//      $as_tipope //Tipo de la Operación a realizar A=Aprobación, R=Reverso de Aprobación.
	//      $ad_fecope //Fecha en la cual se ejecuta la Operación.
	//   $aa_seguridad //Arreglo de seguridad cargado de la informacion de usuario y pantalla.
	//	  Description: Función que recorre el grid de los analisis de cotizacion que esten dispuestas para Aprobacion/Reverso.
	//	   Creado Por: Ing. Laura Cabré
	// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = false;
	  $this->io_sql->begin_transaction();
	  for ($i=1;$i<=$ai_totrows;$i++)
		  {
			if (array_key_exists("chk".$i,$_POST))
			   {
				 $ls_numanacot = $_POST["txtnumanacot".$i];
				 $ls_tipanacot = $_POST["txttipanacot".$i];
				 $ls_fecanacot = $_POST["txtfecanacot".$i];
				 $lb_valido=$this->io_fecha->uf_comparar_fecha($ls_fecanacot,$ad_fecope);
				 if($lb_valido)
				 {
				 	$lb_valido    = $this->uf_update_estatus_aprobacion($ls_numanacot,$as_tipope,$ls_tipanacot,$ad_fecope,$aa_seguridad);
				 }
				 else
				 {
					$this->io_mensajes->message("El Analisis ".$ls_numanacot." tiene fecha superior a la aprobacion");
				 }
				 if (!$lb_valido)
					{
					  break;
					}
			   }
		  }
	   if ($lb_valido)
		  {
			$this->io_sql->commit();
			$this->io_mensajes->message("Operación realizada con Éxito !!!");
		    $this->io_sql->close();
		  }
	   else 
		  {
			$this->io_sql->rollback();
			$this->io_mensajes->message("Error Operación !!!");
		    $this->io_sql->close();
		  }
	}// end function uf_guardar

	function uf_update_estatus_aprobacion($as_numanacot,$as_tipope,$as_tipanacot,$ad_fecope,$aa_seguridad)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_update_estatus_aprobacion
	//		   Access: public
	//		 Argument: 
	//   $as_numanacot //Número del Analisis de Cotizacion
	//      $as_tipope //Tipo de la Operación a ejecutar A=Aprobacion, R=Reverso de la Aprobación.
	//   $as_tipanacot //Tipo de Analisis de Cotizacion B=Bienes , S=Servicios.
	//      $ad_fecope //Fecha en la cual se ejecuta la Operación.
	//   $aa_seguridad //Arreglo de seguridad cargado de la informacion de usuario y pantalla.
	//	  Description: Función que recorre el grid de los Analisis de Cotizacion que esten dispuestas para Aprobacion/Reverso.
	//	   Creado Por: Ing. Laura Cabre
	// Fecha Creación: 05/08/2007								Fecha Última Modificación : 
	//////////////////////////////////////////////////////////////////////////////
	
  	  $lb_valido    = true;
	  $ls_tipanacot = "";
	  if ($as_tipope=='A')
		 {
		   $li_aprest = 1;//Colocar en Aprobada
		   $ad_fecope = $this->io_funciones->uf_convertirdatetobd($ad_fecope);
		 }
	  elseif($as_tipope=='R')
		 {
		   $ad_fecope = '1900-01-01';
		   $li_aprest = 0;//Colocar en No Aprobada.
		 }
	  if ($as_tipanacot=='Bienes')
		 {
		   $ls_tipanacot = 'B';
		 }
	  elseif($as_tipanacot=='Servicios')
		 {
		   $ls_tipanacot = 'S';
		 }
	  $ls_nomusu = $aa_seguridad["logusr"];
	  $ls_sql    = "UPDATE soc_analisicotizacion
					   SET estana='".$li_aprest."', fecapro='".$ad_fecope."'
					 WHERE codemp='".$this->ls_codemp."'
					   AND numanacot='".$as_numanacot."'";
					  // print $ls_sql;
	  $rs_data   = $this->io_sql->execute($ls_sql);
	  if ($rs_data===false)
		 {
		   $lb_valido=false;
		   $this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion; METODO->uf_update_estatus_aprobacion;ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));	
		 }
	  else
		 {
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Actualizó el Estatus del Análisis de Cotizaciones ".$as_numanacot." en ".$li_aprest." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				$ls_fromname="Analisis de Cotización";
				$ls_bodyenv="Se le envia la notificación de actualización en el modulo de SOC, se aprobó el analisis de cotización N°.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numanacot,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////			
		 }
	  return $lb_valido;
	}// end function uf_update_estatus_aprobacion
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