<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_cxp_c_conf_acta
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_id_process;
	var $ls_codemp;
	var $io_dscuentas;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_cxp_c_recepcion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		//$io_conexion->debug = true;
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
		require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("class_funciones_cxp.php");
		$this->io_cxp= new class_funciones_cxp();
        $this->ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
	}// end function sigesp_cxp_c_solicitudpago
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_cxp_p_recepcion.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/04/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_upload($as_nombre,$as_tipo,$as_tamano,$as_nombretemporal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_upload
		//		   Access: public (sigesp_scb_p_conf_cartaorden)
		//	    Arguments: as_nombre  // Nombre 
		//				   as_tipo  // Tipo 
		//				   as_tamano  // Tamaño 
		//				   as_nombretemporal  // Nombre Temporal
		//	      Returns: as_nombre sale vacio si da un error y con el mismo valor si se subio correctamente
		//	  Description: Funcion que sube un archivo al servidor
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($as_nombre!="")
		{
			$ls_ruta="documentos";
			@mkdir($ls_ruta,0755);
			if (!((strpos($as_tipo, "word")||strpos($as_tipo, "rtf")) && ($as_tamano < 2000000))) 
			{ 
				$as_nombre="";
				$this->io_mensajes->message("El archivo no es válido, es muy grande o no es de Extención RTF.");
			}
			else
			{ 
				if (!((move_uploaded_file($as_nombretemporal, "documentos/".$as_nombre))))
				{
					$as_nombre="";
		        	$this->io_mensajes->message("CLASE->Configuracion MÉTODO->uf_upload ERROR-> No tiene Permiso para copiar en la carpeta Contacte con el administrador del sistema."); 
				}
				else
				{
					@chmod("documentos/".$as_nombre,0755);
				}
			}
		}
		return $as_nombre;	
    }
	//-----------------------------------------------------------------------------------------------------------------------------------







	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_acta($ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_acta
		//		   Access: private
		//	    Arguments: ad_fecregsol  // Fecha de Solicitud
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_numsolaux=$ls_codigo;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("CXP","cxp_confacta","codigo","CXPSOP",3,"","","",$ls_codigo);
		$ls_codigo=$arrResultado['as_numero'];
		$lb_valido=true;
		if($lb_valido)
		{

			$ls_sql="INSERT INTO cxp_confacta (codemp, codigo, encabezado, cuerpo, pie, nombre, archrtf)".
					"	  VALUES ('".$this->ls_codemp."','".$ls_codigo."','".$ls_encabezado."','".$ls_cuerpo."',".
					" 			  '".$ls_pie."','".$ls_nombre."','".$ls_archrtf."')";
			$this->io_sql->begin_transaction();				
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$arrResultado=$this->uf_insert_solicitud($ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$la_seguridad);
					$lb_valido=$arrResultado["lb_valido"];
					$as_numsol=$arrResultado["as_numsol"];
					unset($arrResultado);
														  
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_acta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
			}
			else
			{ 
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó la configuracion de acta ".$ls_codigo." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				if($lb_valido)
				{	
					$this->io_mensajes->message("La Configuracion fue Registrada.");
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("Ocurrio un Error al Registrar la Configuracion."); 
					$this->io_sql->rollback();
				}
			}
		}
//		$arrResultado["lb_valido"]=$lb_valido;
//		$arrResultado["as_numsol"]=$as_numsol;
//		return $arrResultado;
	}// end function uf_insert_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($ls_existe,$ls_codemp,$ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$la_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	    Arguments: as_existe    // Fecha de Solicitud
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que valida y guarda la solicitud
		//	   Creado Por: Ing. Yesenia Moreno /Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$lb_encontrado=$this->uf_select_acta($ls_codigo);
		switch ($ls_existe)
		{
			case "FALSE":
				$arrResultado=$this->uf_insert_acta($ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$la_seguridad);
				$lb_valido=$arrResultado["lb_valido"];
				$as_numsol=$arrResultado["as_numsol"];
				break;

			case "TRUE":
				if($lb_encontrado)
				{
					$lb_valido=$this->uf_update_acta($ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$la_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Solicitud no existe, no la puede actualizar.");
				}
				break;
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["codigo"]=$ls_codigo;
		return $arrResultado;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_acta($ls_codigo,$ls_nombre,$ls_encabezado,$ls_cuerpo,$ls_pie,$ls_status,$ls_archrtf,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_acta
		//		   Access: private
		//	    Arguments: ad_fecregsol  // Fecha de Solicitud
		//				   as_numsol     // Número de Solicitud 
		//				   as_codpro     // Codigo de Proveedor
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codfuefin  // Codigo de Fuente de Financiamiento
		//				   as_tipproben  // Tipo Proveedor/Beneficiario 
		//				   ad_fecemisol  // Fecha de Emision de la Solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   as_codtipsol  // Código Tipo de solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   ai_monsol     // Monto de la Solicitud
		//				   as_obssol     // Observacion de la Solicitud
		//				   as_estsol     // Estatus de la Solicitud
		//				   ai_totrowrecepciones  // Total de Filas de R.D.
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_campo="";
		if($ls_archrtf!="")
		{
			$ls_campo=" archrtf = '".$ls_archrtf."',";
		}
		$ls_sql="UPDATE cxp_confacta ".
				"   SET encabezado	= '".$ls_encabezado."', ".
				"		cuerpo = '".$ls_cuerpo."', ".
				"		pie = '".$ls_pie."', ".$ls_campo.
				"		nombre = '".$ls_nombre."' ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND codigo = '".$ls_codigo."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_update_acta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó la solicitud ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Solicitud fue actualizada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Ocurrio un Error al Actualizar la Solicitud."); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_acta($ls_codigo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_acta
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si la Solicitud de pago Existe
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codigo ".
				"  FROM cxp_confacta ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codigo='".$ls_codigo."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_acta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_select_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_conf_acta($ls_codigo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_solicitud
		//		   Access: public
		//	    Arguments: ls_codigo     // Número de Solicitud 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina la solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 30/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM cxp_confacta".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND codigo = '".$ls_codigo."' ";print $ls_sql;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_delete_conf_acta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimino la configuracion de acta ".$ls_codigo." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Solicitud fue Eliminada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Ocurrio un Error al Eliminar la Solicitud."); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_delete_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	
}
?>