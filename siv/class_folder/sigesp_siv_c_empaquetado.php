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

class sigesp_siv_c_empaquetado
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
	}// end function sigesp_cxp_c_empaquetado
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
	function uf_validar_fecha_solicitud($ad_fecemppro)
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
		$ls_sql="SELECT fecemppro ".
				"  FROM siv_empaquetado  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" ORDER BY codemppro DESC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Empaquetado MÉTODO->uf_validar_fecha_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ld_fecha=$this->io_funciones->uf_formatovalidofecha($row["fecemppro"]);
				//$ld_fecha=$row["fecemisol"];
				$lb_valido=$this->io_fecha->uf_comparar_fecha($ld_fecha,$ad_fecemppro); 
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_fecha_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_solicitud($ld_fecemppro,$ls_codemppro,$ls_codartemp,$ls_denartemp,$ls_codalmori,$ls_nomalmori,$ls_codalmdes,
								 $ls_nomalmdes,$ls_obspro,$li_canartemp,$li_totrowartsal,$li_totrowartent,$li_montotartsal,$la_seguridad)
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
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SIV","siv_empaquetado","codemppro","SIVEMP",15,"","","",$ls_codemppro);
		$ls_codemppro=$arrResultado['as_numero'];
		unset($arrResultado);
		$arrResultado=$this->uf_siv_load_tipoarticulo();
		$ls_codtipart=$arrResultado["as_value"];
		$lb_valido=true;
		if($lb_valido)
		{
			$ls_sql="INSERT INTO siv_empaquetado (codemp, codemppro, fecemppro, codtipart, codartemp, denartemp, codalmsal, codalment,".
					"                             canartemp, obspro,estemppro)".
					"	  VALUES ('".$this->ls_codemp."','".$ls_codemppro."','".$ld_fecemppro."','".$ls_codtipart."',".
					" 			  '".$ls_codartemp."','".$ls_denartemp."','".$ls_codalmori."','".$ls_codalmdes."',".$li_canartemp.",".
					"			  '".$ls_obspro."','0')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$arrResultado=$this->uf_insert_solicitud($ld_fecemppro,$ls_codemppro,$ls_codartemp,$ls_denartemp,$ls_codalmori,$ls_nomalmori,$ls_codalmdes,
															 $ls_nomalmdes,$ls_obspro,$li_canartemp,$li_totrowartsal,$li_totrowartent,$li_montotartsal,$la_seguridad);
					$lb_valido=$arrResultado["lb_valido"];
					$ls_codemppro=$arrResultado["ls_codemppro"];
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
				$ls_descripcion ="Insertó el empaquetado ".$ls_codemppro." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
												$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
												$la_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_articulos_salientes($ls_codemppro, $ld_fecemppro, $li_totrowartsal, $ls_codtipart, $la_seguridad);
				}			
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_articulos_entrantes($ls_codemppro, $ld_fecemppro, $li_totrowartsal, $ls_codtipart, $la_seguridad);
				}	
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_asiento($ls_codemppro,$ld_fecemppro,$ls_codalmori,$ls_codalmdes,$li_montotartsal,$aa_seguridad);
				}
				if($lb_valido)
				{	
					if($ls_codempproaux!=$ls_codemppro)
					{
						$this->io_mensajes->message("Se Asigno el Numero de Empaquetado: ".$ls_codemppro);
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
		$arrResultado["ls_codemppro"]=$ls_codemppro;
		return $arrResultado;
	}// end function uf_insert_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_articulos_salientes($ls_codemppro, $ld_fecemppro, $li_totrowartsal, $ls_codtipart, $la_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_articulos_salientes
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
			$li_cantidad=$_POST["txtcanart".$li_i];
			$li_cosuni=$_POST["txtcosuni".$li_i];
			$li_costot=$_POST["txtcossubtotsal".$li_i];
			$li_cantidad=str_replace(".","",$li_cantidad);
			$li_cantidad=str_replace(",",".",$li_cantidad);
			$li_cosuni=str_replace(".","",$li_cosuni);
			$li_cosuni=str_replace(",",".",$li_cosuni);
			$li_costot=str_replace(".","",$li_costot);
			$li_costot=str_replace(",",".",$li_costot);
			if($lb_valido)
			{
				$ls_sql="INSERT INTO siv_dt_empaquetado (codemp, codemppro, codart, opeinv, fecemppro, unidad, cantidad, cosuni, costot)".
						"	  VALUES ('".$this->ls_codemp."','".$ls_codemppro."','".$ls_codart."','S',".
						" 			  '".$ld_fecemppro."','D',".$li_cantidad.",".$li_cosuni.",".$li_costot.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_articulos_salientes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó el articulo ".$ls_codart." al Empaquetado ".$ls_codemppro.
									 " Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
													$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
													$la_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_articulos_entrantes($ls_codemppro, $ld_fecemppro, $li_totrowartent, $ls_codtipart, $la_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_articulos_entrantes
		//		   Access: private
		//	    Arguments: ls_codemppro            // Número de Solicitud 
		//				   aa_seguridad         // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for($li_i=1;($li_i<2)&&($lb_valido);$li_i++)
		{
			$ls_codart=$_POST["txtcodartent".$li_i];
			$li_cantidad=$_POST["txtcanartent".$li_i];
			$li_cosuni=$_POST["txtcosunient".$li_i];
			$li_costot=$_POST["txtcossubtotent".$li_i];
			$li_cantidad=str_replace(".","",$li_cantidad);
			$li_cantidad=str_replace(",",".",$li_cantidad);
			$li_cosuni=str_replace(".","",$li_cosuni);
			$li_cosuni=str_replace(",",".",$li_cosuni);
			$li_costot=str_replace(".","",$li_costot);
			$li_costot=str_replace(",",".",$li_costot);
			if($lb_valido)
			{
				$ls_sql="INSERT INTO siv_dt_empaquetado (codemp, codemppro, codart, opeinv, fecemppro, unidad, cantidad, cosuni, costot)".
						"	  VALUES ('".$this->ls_codemp."','".$ls_codemppro."','".$ls_codart."','E',".
						" 			  '".$ld_fecemppro."','D',".$li_cantidad.",".$li_cosuni.",".$li_costot.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_articulos_entrantes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insertó el articulo ".$ls_codart." al Empaquetado ".$ls_codemppro.
									 " Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
													$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
													$la_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_articulosdisponibles($as_codtipart,$as_codalm)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_procedencia
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
		$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS existencia,siv_dt_movimiento.codart,".
				"       siv_dt_movimiento.codalm, siv_dt_movimiento.cosart, MAX(siv_articulo.denart) AS denart,".
				"       (SELECT nomfisalm FROM siv_almacen".
				"         WHERE siv_almacen.codemp=siv_dt_movimiento.codemp".
				"           AND  siv_almacen.codalm=siv_dt_movimiento.codalm) AS nomfisalm".
				"  FROM siv_dt_movimiento,siv_articulo".
				" WHERE siv_dt_movimiento.codemp='".$this->ls_codemp."'".
				"   AND siv_articulo.codtipart='". $as_codtipart ."'".
				"   AND siv_dt_movimiento.codalm='". $as_codalm ."'".
				"   AND siv_articulo.codemp=siv_dt_movimiento.codemp".
				"   AND siv_articulo.codart=siv_dt_movimiento.codart".
				"   AND promov || numdocori NOT IN".
				"      (SELECT promov || numdocori FROM siv_dt_movimiento".
				"        WHERE opeinv ='REV')".
				" GROUP BY siv_dt_movimiento.codemp,siv_dt_movimiento.codart,siv_dt_movimiento.codalm, siv_dt_movimiento.cosart".
				" ORDER BY siv_dt_movimiento.codemp,siv_dt_movimiento.codart,siv_dt_movimiento.codalm, siv_dt_movimiento.cosart";
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
		//                 ad_fecemisol  // Fecha de emision de la Empaquetado
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Empaquetado de Pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 25/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado=$this->uf_siv_buscar_cuentaalmacen($ls_codalmori);
		$ls_sccuenta=$arrResultado["sc_cuenta"];
		$ls_sql="INSERT INTO siv_dt_empaquetado_scg (codemp,codart,codemppro,fecemppro,sc_cuenta,debhab,monto,estint)".
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
			$ls_sql="INSERT INTO siv_dt_empaquetado_scg (codemp,codart,codemppro,fecemppro,sc_cuenta,debhab,monto,estint)".
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
	function uf_load_articulos($ls_codemppro,$ls_opeinv)
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
		$ls_sql="SELECT codemppro, codart, opeinv, fecemppro, unidad, cantidad, cosuni, costot,".
				"		(SELECT denart FROM siv_articulo".
				"         WHERE siv_articulo.codemp=siv_dt_empaquetado.codemp".
				"           AND  siv_articulo.codart=siv_dt_empaquetado.codart) AS denart".
				"  FROM siv_dt_empaquetado".
				" WHERE siv_dt_empaquetado.codemp='".$this->ls_codemp."'".
				"   AND siv_dt_empaquetado.codemppro='". $ls_codemppro ."'".
				"   AND siv_dt_empaquetado.opeinv='". $ls_opeinv ."'".
				" ORDER BY siv_dt_empaquetado.codart";
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

	function uf_guardar($ls_existe,$ld_fecemppro,$ls_codemppro,$ls_codartemp,$ls_denartemp,$ls_codalmori,$ls_nomalmori,$ls_codalmdes,
						$ls_nomalmdes,$ls_obspro,$li_canartemp,$li_totrowartsal,$li_totrowartent,$li_montotartsal,$la_seguridad)
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
		$li_montotartsal=str_replace(".","",$li_montotartsal);
		$li_montotartsal=str_replace(",",".",$li_montotartsal);
		$lb_encontrado=$this->uf_select_empaquetado($ls_codemppro);
		$ld_fecemppro=$this->io_funciones->uf_convertirdatetobd($ld_fecemppro);
		switch ($ls_existe)
		{
			case "FALSE":
				
				$lb_valido=$this->uf_validar_fecha_solicitud($ld_fecemppro);
				if(!$lb_valido)
				{
					$this->io_mensajes->message("La Fecha este Proceso es menor a la fecha anterior.");
					return false;
				}
				$arrResultado=$this->uf_insert_solicitud($ld_fecemppro,$ls_codemppro,$ls_codartemp,$ls_denartemp,$ls_codalmori,$ls_nomalmori,$ls_codalmdes,
														$ls_nomalmdes,$ls_obspro,$li_canartemp,$li_totrowartsal,$li_totrowartent,$li_montotartsal,$la_seguridad);
				$lb_valido=$arrResultado["lb_valido"];
				$ls_codemppro=$arrResultado["as_codemppro"];
				break;

			case "TRUE":
				if($lb_encontrado)
				{
					$lb_valido=$this->uf_update_solicitud($ld_fecemppro,$ls_codemppro,$ls_codartemp,$ls_denartemp,$ls_codalmori,$ls_nomalmori,$ls_codalmdes,
														 $ls_nomalmdes,$ls_obspro,$li_canartemp,$li_totrowartsal,$li_totrowartent,$la_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Solicitud no existe, no la puede actualizar.");
				}
				break;
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_numsol"]=$as_numsol;
		return $arrResultado;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,$ad_fecemisol,$as_consol,
								 $ai_monsol,$as_obssol,$as_estsol,$ai_totrowrecepciones,$aa_seguridad,$as_numordpagmin,
								 $as_codtipfon,$as_repcajchi,$as_nombenaltcre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_solicitud
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
		$ls_sql="UPDATE cxp_solicitudes ".
				"   SET cod_pro	= '".$as_codpro."', ".
				"		ced_bene = '".trim($as_cedbene)."', ".
				"		consol = '".$as_consol."', ".
				"		codfuefin = '".$as_codfuefin."', ".
				"		tipproben = '".$as_tipproben."', ".
				"		monsol = ".$ai_monsol.", ".
				"		numordpagmin = '".$as_numordpagmin."', ".
				"		codtipfon = '".$as_codtipfon."', ".
				"		obssol = '".$as_obssol."', ".
				"		repcajchi= '".$as_repcajchi."', ".
				"       nombenaltcre= '".$as_nombenaltcre."'".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND numsol = '".$as_numsol."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_update_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
				if($this->ls_supervisor!=0)
				{
					$ls_fromname="Cuentas Por Pagar";
					$ls_bodyenv="Se le envia la notificación de actualización en el modulo de CXP, se actualizó la solicitud de pago  N°.. ";
					$ls_nomper=$_SESSION["la_nomusu"];
					$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numsol,$ls_bodyenv,$ls_nomper);
				}
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{
				$rs_data=$this->uf_load_recepciones($as_numsol);
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$ls_numrecdoc=$row["numrecdoc"];
					$ls_codtipdoc=$row["codtipdoc"];
					$as_cedbene=$row["ced_bene"];// Agregado para reposición de caja chica
					$as_codpro=$row["cod_pro"];// Agregado para reposición de caja chica
					$lb_valido=$this->uf_update_estatus_procedencia($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,"R",$aa_seguridad);	
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_historico_recepciones($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,
																		  $ad_fecemisol,"R",$aa_seguridad);	
					}
				}
				if($rs_data===false)
				{
					$lb_valido=false;
				}
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_detalles($as_numsol,$aa_seguridad);
			}	
			if($lb_valido)
			{	
					$lb_valido=$this->uf_insert_recepciones($as_numsol, $as_cedbene, $as_codpro, $ai_totrowrecepciones,
															$ad_fecemisol, $as_repcajchi, $aa_seguridad);
			}			
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
	function uf_select_empaquetado($as_codemppro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_empaquetado
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si la Solicitud de pago Existe
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codemppro ".
				"  FROM siv_empaquetado ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codemppro='".$as_codemppro."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_empaquetado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
				$ls_sql="DELETE FROM siv_empaquetado ".
						" WHERE codemp = '".$this->ls_codemp."' ".
						"	AND codemppro = '".$as_codemppro."' ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Empaquetado MÉTODO->uf_delete ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
				$this->io_mensajes->message("Ocurrio un Error al Eliminar el Empaquetado."); 
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
		//	    Arguments: as_numsol  // Número de Empaquetado 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una Empaquetado de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 30/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM siv_dt_empaquetado ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND codemppro = '".$as_codemppro."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Empaquetado MÉTODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_sql="DELETE FROM siv_dt_empaquetado_scg ".
					" WHERE codemp = '".$this->ls_codemp."' ".
					"   AND codemppro = '".$as_codemppro."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Empaquetado MÉTODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó todos los detalles dell empaquetado ".$as_codemppro." Asociado a la empresa ".$this->ls_codemp;
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
		//	    Arguments: $as_codemppro  //  Número de Empaquetado
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el numero de Empaquetado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT estemppro ".
				"  FROM siv_empaquetado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codemppro='".$as_codemppro."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Empaquetado MÉTODO->uf_verificar_solicitudeliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_verificar_cierre($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_cierre
		//		   Access: private
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Función que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 01/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT cxp_rd.numrecdoc,".
				"		(SELECT count(cxp_rd_spg.numrecdoc) ".
				"		   FROM cxp_rd_spg ".
				"		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp ".
				"			AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc ".
				"			AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc ".
				"			AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"			AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) as rowspg,".
				"		(SELECT count(cxp_rd_scg.numrecdoc) ".
				"		   FROM cxp_rd_scg ".
				"		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp ".
				"			AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc ".
				"			AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc ".
				"			AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"			AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) as rowscg ".
				"  FROM cxp_rd".
				" WHERE cxp_rd.codemp= '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc= '".$as_numrecdoc."' ".
				"   AND cxp_rd.codtipdoc= '".$as_codtipdoc."' ".
				"   AND cxp_rd.cod_pro= '".$as_codpro."' ".
				"   AND cxp_rd.ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_verificar_cierre ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_rowspg=$row["rowspg"];
				$ls_rowscg=$row["rowscg"];
				if($ls_rowspg>=1)
				{
					$ls_estciespg="";
					$arrResultado=$this->io_cxp->uf_verificar_cierre_spg("../",$ls_estciespg);
					$ls_estciespg=$arrResultado["as_estciespg"];
					if($ls_estciespg=="1")
					{
						$this->io_mensajes->message("Esta procesado el cierre presupuestario");
						$lb_valido=true;
					}
					
				}
				if($ls_rowscg>=1)
				{
					$ls_estciescg="";
					$arrResultado=$this->io_cxp->uf_verificar_cierre_scg("../",$ls_estciescg);
					$ls_estciescg=$arrResultado["as_estciescg"];
					if($ls_estciescg=="1")
					{
						$this->io_mensajes->message("Esta procesado el cierre contable");
						$lb_valido=true;
					}
					
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_verificar_cierre
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_archivoformato($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_archivoformato
		//		   Access: private
		//	    Arguments: $as_sistema  // Sistema al que pertenece la variable
		//				   $as_seccion  // Sección a la que pertenece la variable
		//				   $as_variable  // Variable nombre de la variable a buscar
		//				   $as_valor  // valor por defecto que debe tener la variable
		//				   $as_tipo  // tipo de la variable
		//	  Description: Método que busca el fisico del reporte 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/01/2009								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Empaquetado->uf_load_archivoformato ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
			}
			else
			{
				$ls_valor=$as_valor;
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_validar_fecha_Empaquetado
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_buscar_cuentaentrada($as_codtipart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuenta="";
		$ls_sql="SELECT sc_cuenta".
				"  FROM siv_tipoarticulo  ".
				" WHERE codtipart='".$as_codtipart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->produccion MÉTODO->uf_siv_buscar_cuenta ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuenta=$row["sc_cuenta"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_sc_cuenta;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_buscar_cuenta($as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuenta="";
		$ls_sql="SELECT sc_cuentainv".
				"  FROM siv_articulo  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->produccion MÉTODO->uf_siv_buscar_cuenta ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuenta=$row["sc_cuentainv"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_sc_cuenta;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_buscar_cuentaalmacen($as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codalm //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuenta="";
		$ls_dencta="";
		$ls_sql="SELECT sc_cuenta,".
				"       (SELECT denominacion FROM scg_cuentas".
				"         WHERE siv_almacen.codemp=scg_cuentas.codemp".
				"           AND siv_almacen.sc_cuenta=scg_cuentas.sc_cuenta) AS dencta".
				"  FROM siv_almacen  ".
				" WHERE codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->empaquetado MÉTODO->uf_siv_buscar_cuentaalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuenta=$row["sc_cuenta"];
				$ls_dencta=$row["dencta"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado["sc_cuenta"]=$ls_sc_cuenta;
		$arrResultado["dencta"]=$ls_dencta;
		return $arrResultado;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------
	
}
?>