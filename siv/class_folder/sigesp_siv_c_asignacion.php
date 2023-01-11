<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_siv_c_asignacion
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
		require_once("class_funciones_siv.php");
		$this->io_cxp= new class_funciones_siv();
        $this->ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
	}// end function sigesp_cxp_c_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_tipoarticulo()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_tipoarticulo
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/01/2007							Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value,".
				  "      (SELECT dentipart FROM siv_tipoarticulo".
				  "        WHERE siv_tipoarticulo.codtipart=sigesp_config.value) AS dentipart".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='TIPOART'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion MÉTODO->uf_siv_load_tipoarticulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$as_dentipart=$row["dentipart"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['as_dentipart']=$as_dentipart;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

	}// end  function uf_siv_load_centro_costos
   //---------------------------------------------------------------------------------------------------------------------------

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
	function uf_validar_fecha_solicitud($ad_fecasi)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_fecha_solicitud
		//		   Access: private
		//		 Argument: $ad_fecemppro // fecha de emision del proceso
		//	  Description: Función que busca la fecha de la última
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT fecasi ".
				"  FROM siv_asignacion  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" ORDER BY codasi DESC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->asignacion MÉTODO->uf_validar_fecha_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ld_fecha=$this->io_funciones->uf_formatovalidofecha($row["fecasi"]);
				//$ld_fecha=$row["fecemisol"];
				$lb_valido=$this->io_fecha->uf_comparar_fecha($ld_fecha,$ad_fecasi); 
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_fecha_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_solicitud($ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_solicitud
		//		   Access: private
		//	    Arguments: ad_fecregsol  // Fecha de Solicitud
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codempproaux=$ls_codemppro;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SIV","siv_asignacion","codasi","SIVASI",15,"","","",$ls_codasi);
		$ls_codasi=$arrResultado['as_numero'];
		unset($arrResultado);
		$lb_valido=true;
		if($lb_valido)
		{
			$ls_sql="INSERT INTO siv_asignacion (codemp, codasi, fecasi, codcau, codperpri, codperuso, codalm, obsasi)".
					"	  VALUES ('".$this->ls_codemp."','".$ls_codasi."','".$ld_fecasi."','".$ls_codcau."',".
					" 			  '".$ls_codperpri."','".$ls_codperuso."','".$ls_codalmdes."','".$ls_obsasi."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$arrResultado=$this->uf_insert_solicitud($ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad);
					$lb_valido=$arrResultado["lb_valido"];
					$ls_codasi=$arrResultado["ls_codasi"];
					unset($arrResultado);
														  
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el asignacion ".$ls_codasi." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
												$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
												$la_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_articulos($ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad);
				}			
				if($lb_valido)
				{	
					if($ls_codempproaux!=$ls_codemppro)
					{
						$this->io_mensajes->message("Se Asigno el Numero de asignacion: ".$ls_codemppro);
					}
					$lb_valido=true;
					$this->io_sql->commit();
					$this->io_mensajes->message("El Proceso ha sido Registrado."); 
				}			
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("Ocurrio un Error al Registrar el Proceso."); 
					$this->io_sql->rollback();
				}
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ls_codasi"]=$ls_codasi;
		return $arrResultado;
	}// end function uf_insert_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_articulos($ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_articulos
		//		   Access: private
		//	    Arguments: ls_codemppro            // Número de Solicitud 
		//				   aa_seguridad         // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for($li_i=1;($li_i<$li_totrowartsal)&&($lb_valido);$li_i++)
		{
			$ls_codart=$_POST["txtcodart".$li_i];
			$ls_coddetart=$_POST["txtcoddetart".$li_i];
			$lb_valido=$this->uf_update_materiales($ls_codart,$ls_coddetart,$ls_codperpri,$ls_codperuso);
			if($lb_valido)
			{
				$ls_sql="INSERT INTO siv_dt_asignacion (codemp, codasi, codart, coddetart)".
						"	  VALUES ('".$this->ls_codemp."','".$ls_codasi."','".$ls_codart."',".
						" 			  '".$ls_coddetart."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_articulos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó el articulo ".$ls_codart." - ".$ls_coddetart." al asignacion ".$ls_codasi.
									 " Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
													$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
													$la_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_articulos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_materiales($as_codart,$as_codalm,$as_serdes,$as_serhas)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_materiales
		//		   Access: public
		//		 Argument: as_numrecdoc // Número de Recepción de Documentos
		//		 		   as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_codtipdoc // Código del Tipo de Documento
		//	  Description: Función que verifica si una recepción existe ó no en otra solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 03/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_filtro="";
		if($as_codart!="")
		{
			$ls_filtro=" AND siv_dt_articulo.codart='".$as_codart."'";
		}
		if($as_serdes!="")
		{
			$ls_filtro=" AND siv_dt_articulo.coddetart>='".$as_serdes."'";
		}
		if($as_serhas!="")
		{
			$ls_filtro=" AND siv_dt_articulo.coddetart<='".$as_serhas."'";
		}
		$ls_sql="SELECT siv_articulo.codart,siv_articulo.denart, siv_dt_articulo.coddetart".
				"  FROM siv_dt_articulo,siv_articulo".
				" WHERE siv_dt_articulo.codemp='".$this->ls_codemp."'".
				"   AND siv_dt_articulo.codalm='". $as_codalm."'".$ls_filtro.
				"   AND siv_dt_articulo.estdetart='R'".
				"   AND siv_articulo.codemp=siv_dt_articulo.codemp".
				"   AND siv_articulo.codart=siv_dt_articulo.codart".
				" ORDER BY siv_dt_articulo.codemp,siv_dt_articulo.codart,siv_dt_articulo.coddetart";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_obtener_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_obtener_procedencia
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_asiento($ls_codemppro,$ld_fecemppro,$ls_codalmori,$ls_codalmdes,$li_montotartsal,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_asiento
		//		   Access: private
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la asignacion
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  asignacion de Pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado=$this->uf_siv_buscar_cuentaalmacen($ls_codalmori);
		$ls_sccuenta=$arrResultado["sc_cuenta"];
		$ls_sql="INSERT INTO siv_dt_asignacion_scg (codemp,codart,codemppro,fecemppro,sc_cuenta,debhab,monto,estint)".
				"	  VALUES ('".$this->ls_codemp."','---------------','".$ls_codemppro."','".$ld_fecemppro."','".$ls_sccuenta."','H',".
				" 			  ".$li_montotartsal.",'0')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_asiento_I ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			$arrResultado=$this->uf_siv_buscar_cuentaalmacen($ls_codalmdes);
			$ls_sccuenta=$arrResultado["sc_cuenta"];
			$ls_sql="INSERT INTO siv_dt_asignacion_scg (codemp,codart,codemppro,fecemppro,sc_cuenta,debhab,monto,estint)".
					"	  VALUES ('".$this->ls_codemp."','---------------','".$ls_codemppro."','".$ld_fecemppro."','".$ls_sccuenta."','D',".
					" 			  ".$li_montotartsal.",'0')";

			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_asiento_II ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_historico_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_load_config($as_codsis,$as_seccion,$as_entry,$as_scgcuenta) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_config
		//	          Access:  public
		//	       Arguments:  $as_codemp    // código de la Empresa.
		//        			   $as_codmis    //  código de la Misión.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de verificar si existe o no la configuracion de viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creación:  13/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$ls_sql=" SELECT value".
				"   FROM sigesp_config".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codsis='".$as_codsis."'".
				"    AND seccion='".$as_seccion."'".
				"    AND entry='".$as_entry."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_msg->message("CLASE->sigesp_scv_c_config METODO->uf_scv_load_config ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_scgcuenta=$row["value"];
				$lb_valido=true;
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["value"]=$value;
		return $arrResultado;
	} // fin de la function uf_scv_load_config
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulos($ls_codasi)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_articulos
		//		   Access: public
		//		 Argument: ls_codemppro // Número de Recepción de Documentos
		//	  Description: Función que verifica si una recepción existe ó no en otra solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 03/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql="SELECT codasi, codart, coddetart,".
				"		(SELECT denart FROM siv_articulo".
				"         WHERE siv_articulo.codemp=siv_dt_asignacion.codemp".
				"           AND  siv_articulo.codart=siv_dt_asignacion.codart) AS denart ".
				"  FROM siv_dt_asignacion".
				" WHERE siv_dt_asignacion.codemp='".$this->ls_codemp."'".
				"   AND siv_dt_asignacion.codasi='". $ls_codasi ."'".
				" ORDER BY siv_dt_asignacion.codart,siv_dt_asignacion.coddetart";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_obtener_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_obtener_procedencia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_guardar($ls_existe,$ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	    Arguments: as_existe    // Fecha de Solicitud
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que valida y guarda 
		//	   Creado Por: Ing. Yesenia Moreno /Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$ld_fecasi=$this->io_funciones->uf_convertirdatetobd($ld_fecasi);
		switch ($ls_existe)
		{
			case "FALSE":
				$lb_valido=$this->uf_validar_fecha_solicitud($ld_fecasi);
				if(!$lb_valido)
				{
					$this->io_mensajes->message("La Fecha este Proceso es menor a la fecha anterior.");
					return false;
				}
				$arrResultado=$this->uf_insert_solicitud($ld_fecasi,$ls_codasi,$ls_codcau,$ls_codperpri,$ls_codperuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad);
				$lb_valido=$arrResultado["lb_valido"];
				$ls_codemppro=$arrResultado["as_codemppro"];
				break;

			case "TRUE":
				break;
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_numsol"]=$as_numsol;
		return $arrResultado;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_materiales($ls_codart,$ls_coddetart,$ls_codperpri,$ls_codperuso)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_materiales
		//		   Access: private
		//	    Arguments: ad_fecregsol  // Fecha de Solicitud
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE siv_dt_articulo ".
				"   SET codperpri	= '".$ls_codperpri."', ".
				"		codperuso = '".$ls_codperuso."', ".
				"		estdetart = 'N'".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND codart = '".$ls_codart."' ".
				"	AND coddetart = '".$ls_coddetart."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_update_materiales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_update_materiales
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_asignacion($as_codemppro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_asignacion
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si la Solicitud de pago Existe
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codasi ".
				"  FROM siv_asignacion ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codasi='".$as_codemppro."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_asignacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	function uf_delete($as_codemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_solicitud
		//		   Access: public
		//	    Arguments: as_codemppro     // Número de Solicitud 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina la solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 30/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_valido=$this->uf_verificar_solicitudeliminar($as_codemppro);
		$this->io_sql->begin_transaction();				
		if($lb_valido)
		{
			$lb_valido=$this->uf_delete_detalles($as_codemppro,$aa_seguridad);
			if($lb_valido)
			{
				$ls_sql="DELETE FROM siv_asignacion ".
						" WHERE codemp = '".$this->ls_codemp."' ".
						"	AND codemppro = '".$as_codemppro."' ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->asignacion MÉTODO->uf_delete ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimino el empaquerado ".$as_codemppro." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					if($lb_valido)
					{	
						$this->io_mensajes->message("El proceso fue Eliminado.");
						$this->io_sql->commit();
					}
					else
					{
						$lb_valido=false;
						$this->io_mensajes->message("Ocurrio un Error al Eliminar el Proceso."); 
						$this->io_sql->rollback();
					}
				}
			}
			else
			{
				$this->io_mensajes->message("Ocurrio un Error al Eliminar el asignacion."); 
				$this->io_sql->rollback();
			}
		}
		else
		{
			$this->io_mensajes->message("No se pueden eliminar el Proceso ya que este ha sido Aprobado."); 
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_delete
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detalles($as_codemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detalles
		//		   Access: private
		//	    Arguments: as_numsol  // Número de asignacion 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una asignacion de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 30/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM siv_dt_asignacion ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND codemppro = '".$as_codemppro."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->asignacion MÉTODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_sql="DELETE FROM siv_dt_asignacion_scg ".
					" WHERE codemp = '".$this->ls_codemp."' ".
					"   AND codemppro = '".$as_codemppro."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->asignacion MÉTODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó todos los detalles dell asignacion ".$as_codemppro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_solicitudeliminar($as_codemppro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_solicitudeliminar
		//		   Access: private
		//	    Arguments: $as_codemppro  //  Número de asignacion
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el numero de asignacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT estemppro ".
				"  FROM siv_asignacion ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codemppro='".$as_codemppro."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->asignacion MÉTODO->uf_verificar_solicitudeliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_estemppro=$row["estemppro"];
			//	echo 'SQL->'.$ls_solicitud.' pantalla->'.$as_numsol;
				if($ls_estemppro=="1")
				{
					$lb_valido=false;
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_verificar_solicitudeliminar
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	
}
?>