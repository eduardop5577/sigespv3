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

class sigesp_cxp_c_solicitudpago
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
		// Fecha Creaci?n: 02/04/2007 								Fecha ?ltima Modificaci?n : 
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
		// Fecha Creaci?n: 02/04/2007								Fecha ?ltima Modificaci?n : 
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
	function uf_validar_fecha_solicitud($ad_fecemisol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_fecha_solicitud
		//		   Access: private
		//		 Argument: $ad_fecemisol // fecha de emision de solicitud de pago
		//	  Description: Funci?n que busca la fecha de la ?ltima sep y la compara con la fecha actual
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numsol,fecemisol ".
				"  FROM cxp_solicitudes  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" ORDER BY numsol DESC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_validar_fecha_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ld_fecha=$this->io_funciones->uf_formatovalidofecha($row["fecemisol"]);
				//$ld_fecha=$row["fecemisol"];
				$lb_valido=$this->io_fecha->uf_comparar_fecha($ld_fecha,$ad_fecemisol); 
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_fecha_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,$ad_fecemisol,$as_consol,
								 $ai_monsol,$as_obssol,$as_estsol,$ai_totrowrecepciones,$aa_seguridad,$as_numordpagmin,
								 $as_codtipfon,$as_repcajchi,$as_nombenaltcre,$ls_prefijo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_solicitud
		//		   Access: private
		//	    Arguments: ad_fecregsol  // Fecha de Solicitud
		//				   as_numsol     // N?mero de Solicitud 
		//				   as_codpro     // Codigo de Proveedor
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codfuefin  // Codigo de Fuente de Financiamiento
		//				   as_tipproben  // Tipo Proveedor/Beneficiario 
		//				   ad_fecemisol  // Fecha de Emision de la Solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   as_codtipsol  // C?digo Tipo de solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   ai_monsol     // Monto de la Solicitud
		//				   as_obssol     // Observacion de la Solicitud
		//				   as_estsol     // Estatus de la Solicitud
		//				   ai_totrowrecepciones  // Total de Filas de R.D.
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 23/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if (empty($as_numordpagmin))
		   {
		     $as_numordpagmin = '-';
		   }
		if (empty($as_codtipfon))
		   {
		     $as_codtipfon = '----';
		   }
		$ls_numsolaux=$as_numsol;
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado3("CXP","cxp_solicitudes","numsol","CXPSOP",15,"","","",$as_numsol,$aa_seguridad["logusr"],$ls_prefijo);
		$as_numsol=$arrResultado['as_numero'];
		$lb_valido=true;
		if($lb_valido)
		{
			$ls_sql="INSERT INTO cxp_solicitudes (codemp, numsol, cod_pro, ced_bene, codfuefin, tipproben, fecemisol, consol,".
					"                             estprosol, monsol, obssol, estaprosol,procede,numordpagmin,codtipfon,repcajchi,nombenaltcre,codusureg)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_codpro."','".trim($as_cedbene)."',".
					" 			  '".$as_codfuefin."','".$as_tipproben."','".$ad_fecemisol."','".$as_consol."','".$as_estsol."',".
					"			  ".$ai_monsol.",'".$as_obssol."',0,'CXPSOP','".$as_numordpagmin."','".$as_codtipfon."','".$as_repcajchi."',".
					"             '".$as_nombenaltcre."','".$_SESSION["la_logusr"]."')";
			$this->io_sql->begin_transaction();				
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$arrResultado=$this->uf_insert_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,
														  $ad_fecemisol,$as_consol,$ai_monsol,$as_obssol,$as_estsol,
														  $ai_totrowrecepciones,$aa_seguridad,$as_numordpagmin,$as_codtipfon,
														  $as_repcajchi,$as_nombenaltcre,$ls_prefijo);
					$lb_valido=$arrResultado["lb_valido"];
					$as_numsol=$arrResultado["as_numsol"];
					unset($arrResultado);
														  
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert? la solicitud ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
				if($this->ls_supervisor!=0)
				{
					$ls_fromname="Cuentas Por Pagar";
					$ls_bodyenv="Se le envia la notificaci?n de actualizaci?n en el modulo de CXP, se insert? la solicitud de pago  N?.. ";
					$ls_nomper=$_SESSION["la_nomusu"];
					$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numsol,$ls_bodyenv,$ls_nomper);
				}
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_recepciones($as_numsol, $as_cedbene, $as_codpro, $ai_totrowrecepciones, $ad_fecemisol, $as_repcajchi, $aa_seguridad);
				}			
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_historico_solicitud($as_numsol, $ad_fecemisol, $aa_seguridad);
				}			
				if($lb_valido)
				{	
					if($ls_numsolaux!=$as_numsol)
					{
						$this->io_mensajes->message("Se Asigno el Numero de Solicitud: ".$as_numsol);
					}
					$lb_valido=true;
					$this->io_sql->commit();
					$this->io_mensajes->message("La Solicitud ha sido Registrada."); 
				}			
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("Ocurrio un Error al Registrar la Solicitud."); 
					$this->io_sql->rollback();
				}
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_numsol"]=$as_numsol;
		return $arrResultado;
	}// end function uf_insert_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepciones($as_numsol, $as_cedbene, $as_codpro, $ai_totrowrecepciones, $ad_fecemisol, $as_repcajchi, $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepciones
		//		   Access: private
		//	    Arguments: as_numsol            // N?mero de Solicitud 
		//				   as_cedbene           // Cedula de Beneficiario
		//				   as_codpro            // C?digo Proveedor
		//				   ai_totrowrecepciones // Total de Filas de R.D.
		//				   ad_fecemisol         // Fecha de emision de la solicitud de pago
		//				   aa_seguridad         // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 17/03/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for($li_i=1;($li_i<$ai_totrowrecepciones)&&($lb_valido);$li_i++)
		{
			$ls_numrecdoc=$_POST["txtnumrecdoc".$li_i];
			$ls_codtipdoc=$_POST["txtcodtipdoc".$li_i];
			$li_monto=$_POST["txtmontotdoc".$li_i];
			$li_monto=str_replace(".","",$li_monto);
			$li_monto=str_replace(",",".",$li_monto);
			$ls_auxpro=$_POST["txtauxpro".$li_i];
			$ls_auxben=$_POST["txtauxben".$li_i];
			$lb_existe=$this->uf_select_recepcion($ls_numrecdoc,$as_codpro,$as_cedbene,$ls_codtipdoc);
			if (($as_repcajchi=="1"))
			{
				$as_codpro=$ls_auxpro;
				$as_cedbene=$ls_auxben;
			}
			$lb_valido=$this->uf_comparar_fechas($ls_numrecdoc,$as_codpro,$as_cedbene,$ls_codtipdoc,$ad_fecemisol);
			$lb_cierre=$this->uf_verificar_cierre($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro);
			if($lb_cierre)
			{
				return false;
			}
			if((!$lb_existe)&&($lb_valido))
			{
				$ls_sql="INSERT INTO cxp_dt_solicitudes (codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto)".
						"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$ls_numrecdoc."','".$ls_codtipdoc."',".
						" 			  '".trim($as_cedbene)."','".$as_codpro."',".$li_monto.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="INSERT";
					$ls_descripcion ="Insert? la Recepcion ".$ls_numrecdoc." a la Solicitud de Pago ".$as_numsol.
									 " Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					if($lb_valido)
					{
						$lb_valido=$this->uf_procesar_asientos($as_numsol,$ls_numrecdoc,$ls_codtipdoc,$as_cedbene,
															   $as_codpro,$aa_seguridad);
					}
					if($lb_valido)
					{
						$lb_valido=$this->uf_update_estatus_procedencia($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,"E",$aa_seguridad);	
					}
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_historico_recepciones($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,
																		  $ad_fecemisol,"E",$aa_seguridad);	
					}
				}
			}
			else
			{
				if($lb_existe)
				{
					$this->io_mensajes->message("La Recepcion de documentos ".$ls_numrecdoc." ya esta tomada en otra Solicitud de Pago"); 
				}
				else
				{
					$this->io_mensajes->message("La Recepcion de documentos ".$ls_numrecdoc." tiene una fecha posterior a la Solicitud de Pago"); 
				}
				return false;
			}
		}
		return $lb_valido;
	}// end function uf_insert_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_asientos($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_asientos
		//		   Access: public
		//		 Argument: as_numsol    // N?mero de Solicitud de Pago
		//		 		   as_numrecdoc    // N?mero de Recepci?n de Documentos
		//		 		   as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_codtipdoc // C?digo del Tipo de Documento
		//	  Description: Funci?n que verifica si una recepci?n existe ? no en otra solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 05/03/2008								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		if($this->ls_conrecdoc)
		{
			$as_procedencia="";
			$arrResultado=$this->uf_obtener_procedencia($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc,$as_procedencia);
			$lb_valido=$arrResultado["lb_valido"];
			$as_procedencia=$arrResultado["as_procedencia"];
			unset($arrResultado);
			if($as_procedencia!="SCVSOV")
			{
				$as_cuentapro="";
				$as_cuentarecdoc="";
				$arrResultado=$this->uf_load_cuentaproveedor($as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc);
				$lb_valido=$arrResultado["lb_valido"];
				$as_cuentapro=$arrResultado["as_cuentapro"];
				$as_cuentarecdoc=$arrResultado["as_cuentarecdoc"];
				unset($arrResultado);
			}
			else
			{
				$as_cuentapro="";
				$as_cuentarecdoc="";
				$arrResultado=$this->uf_load_cuentaviaticos($as_cuentapro,$as_cuentarecdoc);
				$lb_valido=$arrResultado["lb_valido"];
				$as_cuentapro=$arrResultado["as_cuentapro"];
				$as_cuentarecdoc=$arrResultado["as_cuentarecdoc"];
				unset($arrResultado);
			}
			if($lb_valido)
			{
				if(($as_cuentapro!="")&&($as_cuentarecdoc!=""))
				{
					$lb_valido=$this->uf_crear_asientos($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_cuentapro,
														$as_cuentarecdoc,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("Existen errores el las cuentas del Proveedor/Beneficiario asociado.");
					$lb_valido=false;
				}
			}
		}
		return $lb_valido;
	}// end function uf_procesar_asientos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_procedencia($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc,$as_procedencia)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_procedencia
		//		   Access: public
		//		 Argument: as_numrecdoc // N?mero de Recepci?n de Documentos
		//		 		   as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_codtipdoc // C?digo del Tipo de Documento
		//	  Description: Funci?n que verifica si una recepci?n existe ? no en otra solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 03/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql="SELECT procede ".
				"  FROM cxp_rd ".
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"	AND cxp_rd.numrecdoc='".$as_numrecdoc."' ".
				"	AND cxp_rd.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_rd.cod_pro='".$as_codpro."' ".
				"   AND cxp_rd.ced_bene='".trim($as_cedbene)."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_obtener_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_procedencia=$row["procede"];
				$lb_valido=true;
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_procedencia"]=$as_procedencia;
		return $arrResultado;
	}// end function uf_obtener_procedencia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_asientos($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_asientos
		//		   Access: public
		//		 Argument:
		//	  Description: 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 03/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,sc_cuenta,estasicon,monto".
				"  FROM cxp_rd_scg".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numrecdoc='".$as_numrecdoc."'".
				"   AND codtipdoc='".$as_codtipdoc."'".
				"   AND cod_pro='".$as_codpro."'".
				"   AND ced_bene='".trim($as_cedbene)."'".
				"   AND sc_cuenta='".$as_cuentarecdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_crear_asientos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_procede_doc=$row["procede_doc"];
				$ls_numdoccom=$row["numrecdoc"];
				$ls_debhab=$row["debhab"];
				$ls_estasicon=$row["estasicon"];
				$li_monto=$row["monto"];
				$lb_valido=$this->uf_insert_asiento($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc,
													$ls_procede_doc,$ls_numdoccom,$ls_debhab,$ls_estasicon,$li_monto,$aa_seguridad);
			}
		}
		return $lb_valido;
	}// end function uf_crear_asientos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_asiento($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc,$as_procede_doc,
							   $as_numdoccom,$as_debhab,$as_estasicon,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_asiento
		//		   Access: private
		//	    Arguments: as_numrecdoc  // N?mero de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // C?digo Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 25/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="INSERT INTO cxp_solicitudes_scg (codemp,numsol,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,sc_cuenta,estasicon,monto)".
				"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_numrecdoc."','".$as_codtipdoc."','".trim($as_cedbene)."','".$as_codpro."',".
				" 			  '".$as_procede_doc."','".$as_numdoccom."','D','".$as_cuentarecdoc."','".$as_estasicon."','".$ai_monto."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_asiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_sql="INSERT INTO cxp_solicitudes_scg (codemp,numsol,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,sc_cuenta,estasicon,monto)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_numrecdoc."','".$as_codtipdoc."','".trim($as_cedbene)."','".$as_codpro."',".
					" 			  '".$as_procede_doc."','".$as_numdoccom."','H','".$as_cuentapro."','".$as_estasicon."','".$ai_monto."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_asiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert? el asiento contable originado de la contabilizaci?n de la R.D. ".$as_numrecdoc.
								 " ligada a la solicitud de pago ".$as_numsol." Con las cuentas D ".$as_cuentarecdoc." H ".$as_cuentapro." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
		}
		return $lb_valido;
	}// end function uf_insert_historico_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_cuentaproveedor($as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentaproveedor
		//		   Access: public
		//		 Argument: as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_cuentapro // Cuenta del Proveedor/Beneficiario
		//	  Description: 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 03/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_cuentapro="";
		$as_cuentarecdoc="";
		if($as_codpro!="----------")
		{
			$ls_sql="SELECT trim(sc_cuenta) as sc_cuenta,sc_cuentarecdoc".
					"  FROM rpc_proveedor ".
					" WHERE rpc_proveedor.codemp ='".$this->ls_codemp."'".
					"   AND rpc_proveedor.cod_pro = '".$as_codpro."'";
		}
		else
		{
			$ls_sql="SELECT trim(sc_cuenta) as sc_cuenta,sc_cuentarecdoc".
					"  FROM rpc_beneficiario ".
					" WHERE rpc_beneficiario.codemp ='".$this->ls_codemp."'".
					"   AND rpc_beneficiario.ced_bene = '".trim($as_cedbene)."'";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_load_cuentaproveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_cuentapro=$row["sc_cuenta"];
				$as_cuentarecdoc=$row["sc_cuentarecdoc"];
				$lb_valido=true;
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_cuentapro"]=$as_cuentapro;
		$arrResultado["as_cuentarecdoc"]=$as_cuentarecdoc;
		return $arrResultado;
	}// end function uf_load_cuentaproveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_cuentaviaticos($as_cuentapro,$as_cuentarecdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentaviaticos
		//		   Access: public
		//		 Argument: as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_cuentapro // Cuenta del Proveedor/Beneficiario
		//	  Description: 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 03/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_cuentapro="";
		$as_cuentarecdoc="";
		$arrResultado=$this->uf_scv_load_config("SCV","CONFIG","BENEFICIARIO",$as_cuentapro);
		$lb_valido=$arrResultado["lb_valido"];
		$as_cuentapro=$arrResultado["value"];
		unset($arrResultado);
		if($lb_valido)
		{
			$arrResultado=$this->uf_scv_load_config("SCV","CONFIG","BENEFICIARIORD",$as_cuentarecdoc);
			$lb_valido=$arrResultado["lb_valido"];
			$as_cuentarecdoc=$arrResultado["value"];
			unset($arrResultado);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_cuentapro"]=$as_cuentapro;
		$arrResultado["as_cuentarecdoc"]=$as_cuentarecdoc;
		return $arrResultado;
	}// end function uf_load_cuentaviaticos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_load_config($as_codsis,$as_seccion,$as_entry,$as_scgcuenta) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_config
		//	          Access:  public
		//	       Arguments:  $as_codemp    // c?digo de la Empresa.
		//        			   $as_codmis    //  c?digo de la Misi?n.
		//	         Returns:  $lb_valido.
		//	     Description:  Funci?n que se encarga de verificar si existe o no la configuracion de viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creaci?n:  13/11/2006
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
	function uf_comparar_fechas($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc,$ad_fecemisol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_comparar_fechas
		//		   Access: public
		//		 Argument: as_numrecdoc // N?mero de Recepci?n de Documentos
		//		 		   as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_codtipdoc // C?digo del Tipo de Documento
		//		 		   ad_fecemisol // Fecha de emision de la solicitud de pago
		//	  Description: Funci?n que verifica si la fecha de la solicitud de pago con la de la recepcion de documento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 09/04/2008								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=false;
		$ls_sql="SELECT fecregdoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND numrecdoc='".$as_numrecdoc."' ".
				"	AND codtipdoc='".$as_codtipdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".trim($as_cedbene)."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_comparar_fechas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$ld_fegregdoc=$row["fecregdoc"];
				$lb_valido=$this->io_fecha->uf_comparar_fecha($ld_fegregdoc,$ad_fecemisol);
			}
		}
		if(!$lb_valido)
		{
			$this->io_mensajes->message("La fecha de la Recepcion de Documentos ".$as_numrecdoc." es mayor que la fecha de la solicitud");
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_recepcion
		//		   Access: public
		//		 Argument: as_numrecdoc // N?mero de Recepci?n de Documentos
		//		 		   as_codpro    // C?digo del Proveedor 
		//		 		   as_cedbene   // C?dula del Beneficiario
		//		 		   as_codtipdoc // C?digo del Tipo de Documento
		//	  Description: Funci?n que verifica si una recepci?n existe ? no en otra solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 03/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_existe=false;
		$ls_sql="SELECT numrecdoc ".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes ".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"	AND cxp_dt_solicitudes.numrecdoc='".$as_numrecdoc."' ".
				"	AND cxp_dt_solicitudes.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_dt_solicitudes.cod_pro='".$as_codpro."' ".
				"   AND cxp_dt_solicitudes.ced_bene='".trim($as_cedbene)."'".
				"   AND cxp_solicitudes.estprosol<>'A'".
				"   AND cxp_solicitudes.estprosol<>'N'".
				"	AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"	AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_select_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
			}
		}
		return $lb_existe;
	}// end function uf_select_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_procedencia($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$ls_estatus,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_procedencia
		//		   Access: private
		//	    Arguments: as_numrecdoc // N?mero de Recepcion de Documentos
		//                 as_codtipdoc // Codigo de Tipo de Documento
		//				   as_cedbene   // Cedula de Beneficiario
		//				   as_codpro    // C?digo Proveedor
		//				   ls_estatus   // Estatus en que se desea colocar la R.D.
		//                 aa_seguridad // Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que actualiza el estatus de la Recepcion de Documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 25/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->ls_conrecdoc!=1)
		{
			$ls_sql="UPDATE cxp_rd ".
					"   SET estprodoc = '".$ls_estatus."' ".
					" WHERE codemp = '".$this->ls_codemp."'".
					"	AND numrecdoc = '".$as_numrecdoc."' ".
					"	AND codtipdoc = '".$as_codtipdoc."' ".
					"	AND ced_bene = '".trim($as_cedbene)."' ".
					"	AND cod_pro = '".$as_codpro."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_update_estatus_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualiz? en estatus de la recepcion <b>".$as_numrecdoc.
								 "</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
		}
		return $lb_valido;
	}// end function uf_update_estatus_procedencia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_historico_recepciones($as_numrecdoc, $as_codtipdoc, $as_cedbene, $as_codpro, $ad_fecemisol,
											 $as_estatus,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_historico_recepciones
		//		   Access: private
		//	    Arguments: as_numrecdoc  // N?mero de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // C?digo Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 25/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=$this->uf_select_historicord($as_numrecdoc, $as_codtipdoc, $as_cedbene, $as_codpro, $ad_fecemisol,$as_estatus);
		if(!$lb_existe)
		{
			$ls_sql="INSERT INTO cxp_historico_rd (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, fecha, estprodoc)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numrecdoc."','".$as_codtipdoc."',".
					" 			  '".trim($as_cedbene)."','".$as_codpro."','".$ad_fecemisol."','".$as_estatus."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_historico_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insert? un Movimiento en el Historico de la Recepcion ".$as_numrecdoc.
								 " Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
		}
		return $lb_valido;
	}// end function uf_insert_historico_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_historico_solicitud($as_numsol, $ad_fecemisol, $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_historico_solicitud
		//		   Access: private
		//	    Arguments: as_numsol    // N?mero de Solicitud 
		//                 ad_fecemisol //  Fecha de emision de la solicitud
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta un movimiento en el historico de la solicitud de orden de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO cxp_historico_solicitud (codemp, numsol, fecha, estprodoc)".
				"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$ad_fecemisol."','R')";        
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_insert_historico_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? un Movimiento en el Historico de la Solicitud de Pago ".$as_numsol.
							 " Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_insert_historico_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,$ad_fecemisol,$as_consol,
						$ai_monsol,$as_obssol,$as_estsol,$ai_totrowrecepciones,$aa_seguridad,$as_permisosadministrador,
						$as_numordpagmin,$as_codtipfon,$as_repcajchi,$as_nombenaltcre,$ls_prefijo)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	    Arguments: as_existe    // Fecha de Solicitud
		//				   as_numsol    // N?mero de Solicitud 
		//				   as_codpro    // Codigo de Proveedor
		//				   as_cedbene   // Codigo de Beneficiario
		//				   as_codfuefin // C?digo de Fuente de financiamiento
		//				   as_tipproben // Tipo de Proveedor / Beneficiario
		//				   as_consol    // Concepto de la Solicitud
		//				   ad_fecemisol // Fecha de Emision de la Solicitud
		//				   ai_monsol    // Total de la solicitud
		//				   as_obssol    // Observacion de la Solicitud
		//				   as_estsol    // Estatus de la Solicitud
		//				   ai_totrowrecepciones  // Total de Recepciones de Documento asociadas
		//				   aa_seguridad // arreglo de las variables de seguridad
		//				   as_permisosadministrador  // Indica si el usuario tiene permiso de administrador
		//	      Returns: lb_valido True si se ejecuto el guardar ? False si hubo error en el guardar
		//	  Description: Funcion que valida y guarda la solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno /Ing. Luis Lang
		// Fecha Creaci?n: 26/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$lb_encontrado=$this->uf_select_solicitud($as_numsol);
		$ai_monsol=str_replace(".","",$ai_monsol);
		$ai_monsol=str_replace(",",".",$ai_monsol);
		$ad_fecemisol=$this->io_funciones->uf_convertirdatetobd($ad_fecemisol);
		switch ($as_existe)
		{
			case "FALSE":
				if($as_permisosadministrador!=1)
				{
					$lb_valido=$this->uf_validar_fecha_solicitud($ad_fecemisol);
				    if(!$lb_valido)
					{
						$this->io_mensajes->message("La Fecha esta la Solicitud es menor a la fecha de la Solicitud anterior.");
						return false;
					}
				}
				$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecemisol,$this->ls_codemp);
				if (!$lb_valido)
				{
					$this->io_mensajes->message($this->io_fecha->is_msg_error);           
					return false;
				}                    
				$arrResultado=$this->uf_insert_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,
													  $ad_fecemisol,$as_consol,$ai_monsol,$as_obssol,$as_estsol,
													  $ai_totrowrecepciones,$aa_seguridad,$as_numordpagmin,$as_codtipfon,
													  $as_repcajchi,$as_nombenaltcre,$ls_prefijo);
				$lb_valido=$arrResultado["lb_valido"];
				$as_numsol=$arrResultado["as_numsol"];
				break;

			case "TRUE":
				if($lb_encontrado)
				{
					$lb_valido=$this->uf_update_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,
														  $ad_fecemisol,$as_consol,$ai_monsol,$as_obssol,$as_estsol,
														  $ai_totrowrecepciones,$aa_seguridad,$as_numordpagmin,$as_codtipfon,
														  $as_repcajchi,$as_nombenaltcre);
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
		//				   as_numsol     // N?mero de Solicitud 
		//				   as_codpro     // Codigo de Proveedor
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codfuefin  // Codigo de Fuente de Financiamiento
		//				   as_tipproben  // Tipo Proveedor/Beneficiario 
		//				   ad_fecemisol  // Fecha de Emision de la Solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   as_codtipsol  // C?digo Tipo de solicitud
		//				   as_consol     // Concepto de la Solicitud
		//				   ai_monsol     // Monto de la Solicitud
		//				   as_obssol     // Observacion de la Solicitud
		//				   as_estsol     // Estatus de la Solicitud
		//				   ai_totrowrecepciones  // Total de Filas de R.D.
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta la Solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 23/04/2007 								Fecha ?ltima Modificaci?n : 
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
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_update_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? la solicitud ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
				if($this->ls_supervisor!=0)
				{
					$ls_fromname="Cuentas Por Pagar";
					$ls_bodyenv="Se le envia la notificaci?n de actualizaci?n en el modulo de CXP, se actualiz? la solicitud de pago  N?.. ";
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
					$as_cedbene=$row["ced_bene"];// Agregado para reposici?n de caja chica
					$as_codpro=$row["cod_pro"];// Agregado para reposici?n de caja chica
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
	function uf_select_solicitud($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  N?mero de Solicitud
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si la Solicitud de pago Existe
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol ".
				"  FROM cxp_solicitudes ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_select_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	function uf_load_recepciones($as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_numsol // N?mero de solicitud
		//	  Description: Funci?n que busca las recepciones de documentos asociadas a una solicitud
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 29/04/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT cxp_dt_solicitudes.numrecdoc, cxp_dt_solicitudes.monto, cxp_dt_solicitudes.codtipdoc,".
				"       cxp_documento.dentipdoc,cxp_dt_solicitudes.cod_pro,cxp_dt_solicitudes.ced_bene,cxp_rd.codproalt,cxp_rd.numexprel ".
				"  FROM cxp_dt_solicitudes,cxp_documento,cxp_rd ".	
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_dt_solicitudes.numsol= '".$as_numsol."' ".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_documento.codtipdoc".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_solicitud($as_numsol,$as_codpro,$as_cedbene,$ad_fecemisol,$ai_totrow,$as_repcajchi,$aa_seguridad,$la_permisoadministrador)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_solicitud
		//		   Access: public
		//	    Arguments: as_numsol     // N?mero de Solicitud 
		//				   as_codpro     // Codigo de Proveedor
		//				   as_cedbene    // Codigo de Beneficiario
		//				   ad_fecemisol  // Fecha de Emision de la Solicitud
		//				   ai_totrow     // total de recepciones asociadas
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que elimina la solicitud de Pagos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 30/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecemisol=$this->io_funciones->uf_convertirdatetobd($ad_fecemisol);
		if($la_permisoadministrador!=1)
		{
			$lb_valido=$this->uf_verificar_solicitudeliminar($as_numsol);
		}
		if($lb_valido)
		{
			$this->io_sql->begin_transaction();	
			$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecemisol,$this->ls_codemp);
			if (!$lb_valido)
			{
				$this->io_mensajes->message($this->io_fecha->is_msg_error);           
				return false;
			}
			$rs_data=$this->uf_load_recepciones($as_numsol);
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_numrecdoc=$row["numrecdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$as_cedbene=$row["ced_bene"];// Agregado para reposici?n de caja chica
				$as_codpro=$row["cod_pro"];// Agregado para reposici?n de caja chica
				$lb_valido=$this->uf_update_estatus_procedencia($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,"R",$aa_seguridad);	
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_historico_recepciones($ls_numrecdoc,$ls_codtipdoc,$as_cedbene,$as_codpro,
																	  $ad_fecemisol,"R",$aa_seguridad);	
				}
			}
			$lb_valido=$this->uf_delete_detalles($as_numsol,$aa_seguridad);
			if($lb_valido)
			{
				$ls_cheque=$this->uf_select_cheque($as_numsol);
				if($ls_cheque=="")
				{
					$ls_sql="DELETE FROM cxp_solicitudes ".
							" WHERE codemp = '".$this->ls_codemp."' ".
							"	AND numsol = '".$as_numsol."' ";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_delete_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						$this->io_sql->rollback();
					}
					else
					{
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento="DELETE";
						$ls_descripcion ="Elimino la solicitud de pago ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
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
				}
				else
				{
					$this->io_mensajes->message("La solicitud de Pago esta relacionada en Banco al Documento ".$ls_cheque); 
					$this->io_sql->rollback();
				}
			}
			else
			{
				$this->io_mensajes->message("Ocurrio un Error al Eliminar la Solicitud."); 
				$this->io_sql->rollback();
			}
		}
		else
		{
			$this->io_mensajes->message("No se pueden eliminar solicitudes intermedias, si la desea dejar sin efecto debe anular la solicitud"); 
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_delete_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detalles($as_numsol,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detalles
		//		   Access: private
		//	    Arguments: as_numsol  // N?mero de Solicitud 
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 30/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM cxp_dt_solicitudes ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND numsol = '".$as_numsol."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_historico_solicitud ".
					" WHERE codemp = '".$this->ls_codemp."' ".
					"   AND numsol = '".$as_numsol."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			if($lb_valido)
			{
				$ls_sql="DELETE FROM cxp_solicitudes_scg ".
						" WHERE codemp = '".$this->ls_codemp."' ".
						"   AND numsol = '".$as_numsol."' ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_delete_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimin? todos los detalles de la solicitud de pago ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}
			}
		}
		return $lb_valido;
	}// end function uf_delete_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_historicord($as_numrecdoc, $as_codtipdoc, $as_cedbene, $as_codpro, $ad_fecemisol,$as_estatus)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_historicord
		//		   Access: private
		//	    Arguments: as_numrecdoc  // N?mero de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // C?digo Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Funci?n que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/05/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numrecdoc ".
				"  FROM cxp_historico_rd  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."'".
				"   AND codtipdoc='".$as_codtipdoc."'".
				"   AND ced_bene='".trim($as_cedbene)."'".
				"   AND cod_pro='".$as_codpro."'".
				"   AND fecha='".$ad_fecemisol."'".
				"   AND estprodoc='".$as_estatus."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_select_historicord ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_select_historicord
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_solicitudeliminar($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_solicitudeliminar
		//		   Access: private
		//	    Arguments: as_numsol  //  N?mero de Solicitud
		// 	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el numero de solicitud de pago es la ultima que esta registrado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 26/04/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
	   switch ($_SESSION["ls_gestor"])
	   {
	   		case "INFORMIX":
				$ls_sql="SELECT LIMIT 1 numsol ".
						"  FROM cxp_solicitudes ".
						" WHERE codemp='".$this->ls_codemp."' ".
						" ORDER BY numsol DESC ";
			break;
			
			case 'oci8po':
				$ls_sql="SELECT *	
							FROM (SELECT numsol 
									FROM cxp_solicitudes 
									WHERE codemp='".$this->ls_codemp."' ORDER BY fecemisol DESC, numsol DESC)
							WHERE rownum <=1";
				break;
			
			default: // POSTGRES
				$ls_sql="SELECT numsol ".
						"  FROM cxp_solicitudes ".
						" WHERE codemp='".$this->ls_codemp."' ".
						" ORDER BY fecemisol DESC, numsol DESC LIMIT 1";
			break;
	   }
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_verificar_solicitudeliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_solicitud=$row["numsol"];
			//	echo 'SQL->'.$ls_solicitud.' pantalla->'.$as_numsol;
				if($ls_solicitud==$as_numsol)
				{
					$lb_valido=true;
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
		//	    Arguments: as_numrecdoc  // N?mero de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // C?digo Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Funci?n que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/05/2007								Fecha ?ltima Modificaci?n : 
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
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_verificar_cierre ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		//				   $as_seccion  // Secci?n a la que pertenece la variable
		//				   $as_variable  // Variable nombre de la variable a buscar
		//				   $as_valor  // valor por defecto que debe tener la variable
		//				   $as_tipo  // tipo de la variable
		//	  Description: M?todo que busca el fisico del reporte 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 05/01/2009								Fecha ?ltima Modificaci?n : 
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
			$this->io_mensajes->message("CLASE->Solicitud->uf_load_archivoformato ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_validar_fecha_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cheque($as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cheque
		//		   Access: private
		//	    Arguments: as_numrecdoc  // N?mero de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // C?digo Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Funci?n que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/05/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cheque="";
		$ls_sql="SELECT numdoc ".
				"  FROM cxp_sol_banco  ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud M?TODO->uf_select_cheque ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_cheque=$row["numdoc"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_cheque;
	}// end function uf_select_historicord
	//-----------------------------------------------------------------------------------------------------------------------------------
	
}
?>