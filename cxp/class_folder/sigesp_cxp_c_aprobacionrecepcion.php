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

class sigesp_cxp_c_aprobacionrecepcion
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	private $io_conexion;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_cxp_c_aprobacionrecepcion
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion = $io_include->uf_conectar();
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_estrescxp=$_SESSION["la_empresa"]["estrescxp"];
        $this->ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
	}// end function sigesp_sep_c_aprobacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($this->io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_recepciones($as_numsol,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion,$as_repcon='0')
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de ejecucion presupuestaria
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las recepciones  a aprobar o reversar aprobacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('nombene',"' '",'apebene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1' && $as_repcon=='0') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro";
			$ls_filtrofrom = ",cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		//FILTRAR REPCIONES CONTABLES
		if($as_repcon=='1') {
			$ls_filtrofrom = ",cxp_documento ";
			$ls_filtroest = 'AND cxp_rd.codtipdoc = cxp_documento.codtipdoc AND cxp_documento.estcon=1 AND cxp_documento.estpre=4';	
		}
		//FILTRAR REPCIONES CONTABLES
		$ls_sql="SELECT DISTINCT cxp_rd.numrecdoc,cxp_rd.fecregdoc,cxp_rd.estaprord,cxp_rd.montotdoc,cxp_rd.tipproben,".
				"       cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.codtipdoc,".
				"       (CASE WHEN cxp_rd.tipproben='B' THEN (SELECT ".$ls_cadena." ".
				"                                               FROM rpc_beneficiario".
				"                                              WHERE cxp_rd.codemp=rpc_beneficiario.codemp".
				"                                                AND cxp_rd.ced_bene=rpc_beneficiario.ced_bene)".
				"             WHEN cxp_rd.tipproben='P' THEN (SELECT nompro".
				"                                               FROM rpc_proveedor".
				"                                              WHERE cxp_rd.codemp=rpc_proveedor.codemp".
				"                                                AND cxp_rd.cod_pro=rpc_proveedor.cod_pro)".
				"                                       ELSE 'NINGUNO'".
				"         END) AS nombre,".
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
				"  FROM cxp_rd".$ls_filtrofrom.
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc LIKE '".$as_numsol."' ".
				"   AND cxp_rd.fecregdoc >= '".$ad_fecregdes."' ".
				"   AND cxp_rd.fecregdoc <= '".$ad_fecreghas."' ".
				"   AND cxp_rd.estprodoc='R'".
				"   AND cxp_rd.estaprord='".$as_tipooperacion."'  ";
		if($as_tipproben=="B")
		{
			$ls_sql= $ls_sql." AND cxp_rd.ced_bene LIKE '".$as_proben."'";
		}
		else
		{
			$ls_sql= $ls_sql." AND cxp_rd.cod_pro LIKE'".$as_proben."' ";
		}
		$ls_sql= $ls_sql.$ls_filtroest." ORDER BY cxp_rd.numrecdoc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_estatus_recepcion($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numrecdoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".$as_cedben."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"   AND estaprord=".$as_estsol."";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_validar_estatus_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_recepciones($as_numrecdoc,$as_estrd,$as_codpro,$as_cedben,$as_codtipdoc,$ad_fecaprord,$as_generar,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_recepciones
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//                 as_estrd      //  Estatus en que se desea colocar la Recepcion de Documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//                 ad_fecaprord  //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad  //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que actualiza el estatus de aprobacion de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecaprord,$this->ls_codemp);
		if (!$lb_valido)
		{
			$this->io_mensajes->message($this->io_fecha->is_msg_error);           
			return false;
		}
		$ls_usuario=$_SESSION["la_logusr"];
		if($as_estrd==0)
		{
			$ad_fecaprsep="1900-01-01";
			$ls_usuario="";
		}
		$ad_fecaprord=$this->io_funciones->uf_convertirdatetobd($ad_fecaprord);
		$ls_sql="UPDATE cxp_rd ".
				"   SET estaprord = ".$as_estrd.", ".
				"       fecaprord = '".$ad_fecaprord."', ".
				"		usuaprord = '".$ls_usuario."' ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND numrecdoc = '".$as_numrecdoc."' ".
				"	AND cod_pro = '".$as_codpro."' ".
				"	AND ced_bene = '".$as_cedben."' ".
				"	AND codtipdoc = '".$as_codtipdoc."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_update_estatus_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			if($as_estrd==1)
			{
				$ls_descripcion ="Aprobó la Recepcion de Documentos <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			else
			{
				$ls_descripcion ="Reversó la Recepcion de Documentos <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				if($as_estrd==1)
				{
					$ls_fromname="Aprobación de Recepción de Documentos";
				}
				else
				{
					$ls_fromname="Reverso de Recepción de Documentos";
				}	
				$ls_bodyenv="Se le envia la notificación de actualización en el modulo de CXP, se actualizó la recepción de documentos  N°.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numrecdoc,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{
				if(($as_estrd==1)&&($this->ls_estrescxp=="1")&&($as_generar=="1"))
				{
					$lb_valido=$this->uf_generar_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc,$ad_fecaprord,$aa_seguridad);
				}
			}
			
			if($lb_valido)
			{
				$this->io_sql->commit();
			}
			else
			{
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_estatus_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc,$ad_fecaprord,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_solicitud
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que verifica que una recepcion de documentos este en estatus de registro
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2015								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_buscar_datos_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($row=$this->io_sql->fetch_row($rs_data))
		{
			$ls_codfuefin=$row["codfuefin"];
			$ls_tipproben=$row["tipproben"];
			$ls_dencondoc=$row["dencondoc"];
			$ls_codtipfon=$row["codtipfon"];
			$ls_repcajchi=$row["repcajchi"];
			$li_monto=$row["montotdoc"];
			$li_monretasu=$row["monretasu"];
			$ls_estretasu=$row["estretasu"];
			if($ls_estretasu=="1")
				$li_monto=$li_monretasu;
			if($ls_repcajchi!="1")
			{
				$ls_numsol= $this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_solicitudes","numsol","CXPSOP",15,"numsolpag","","");
				$lb_valido= $this->uf_insert_solicitud($ls_numsol,$as_codpro,$as_cedben,$ls_codfuefin,$ls_tipproben,
															  $ad_fecaprord,$ls_dencondoc,$li_monto,"Solicitud Generada en Proceso Automatico","E",$aa_seguridad,
															  "",$ls_codtipfon,$as_numrecdoc,$as_codtipdoc);
			}
			else
			{
				$this->io_mensajes->message("Proceso no valido para Reposicion de Caja Chica"); 
			}
			if($lb_valido)
			{
				$this->io_mensajes->message("Se Genero la Solicitud de Pago Correspondiente"); 
			}
		}
		
		return $lb_valido;
	}// end function uf_validar_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_datos_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la recepcion de documentos
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2015 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data="";
		$ls_sql="SELECT numrecdoc,codfuefin,tipproben,dencondoc,codtipfon,repcajchi,montotdoc, (montotdoc+mondeddoc) AS monretasu, estretasu ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".$as_cedben."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		return $rs_data;
	}// end function uf_validar_estatus_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,$ad_fecemisol,$as_consol,
								 $ai_monsol,$as_obssol,$as_estsol,$aa_seguridad,$as_numordpagmin,$as_codtipfon,$as_numrecdoc,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_solicitud
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
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 23/04/2007 								Fecha Última Modificación : 
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
		$arrResultado= $this->io_keygen->uf_verificar_numero_generado("CXP","cxp_solicitudes","numsol","CXPSOP",15,"","","",$as_numsol);
		$as_numsol=$arrResultado['as_numero'];
		$lb_valido=true;
		if($lb_valido)
		{
			$ls_sql="INSERT INTO cxp_solicitudes (codemp, numsol, cod_pro, ced_bene, codfuefin, tipproben, fecemisol, consol,".
					"                             estprosol, monsol, obssol, estaprosol,procede,numordpagmin,codtipfon,repcajchi,nombenaltcre,codusureg,fecaprosol,usuaprosol)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_codpro."','".trim($as_cedbene)."',".
					" 			  '".$as_codfuefin."','".$as_tipproben."','".$ad_fecemisol."','".$as_consol."','".$as_estsol."',".
					"			  ".$ai_monsol.",'".$as_obssol."',1,'CXPSOP','".$as_numordpagmin."','".$as_codtipfon."','0',".
					"             '','".$_SESSION["la_logusr"]."','".$ad_fecemisol."','".$_SESSION["la_logusr"]."')";		

			$this->io_sql->begin_transaction();				
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_sql->rollback();
				if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
				{
					$lb_valido=$this->uf_insert_solicitud($as_numsol,$as_codpro,$as_cedbene,$as_codfuefin,$as_tipproben,
														  $ad_fecemisol,$as_consol,$ai_monsol,$as_obssol,$as_estsol,$aa_seguridad,$as_numordpagmin,$as_codtipfon,
														  $as_numrecdoc,$as_codtipdoc);
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
				$ls_descripcion ="Insertó la solicitud ".$as_numsol." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				if($lb_valido)
				{	
					$lb_valido=$this->uf_insert_recepciones($as_numsol,$as_numrecdoc,$as_codtipdoc, $as_cedbene, $as_codpro, $ad_fecemisol, $ai_monsol, $aa_seguridad);
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
		return $lb_valido;
	}// end function uf_insert_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepciones($as_numsol,$as_numrecdoc,$as_codtipdoc, $as_cedbene, $as_codpro,  $ad_fecemisol, $ai_monsol, $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepciones
		//		   Access: private
		//	    Arguments: as_numsol            // Número de Solicitud 
		//				   as_cedbene           // Cedula de Beneficiario
		//				   as_codpro            // Código Proveedor
		//				   ai_totrowrecepciones // Total de Filas de R.D.
		//				   ad_fecemisol         // Fecha de emision de la solicitud de pago
		//				   aa_seguridad         // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 17/03/2015 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=$this->uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc);
		if((!$lb_existe)&&($lb_valido))
		{
			$ls_sql="INSERT INTO cxp_dt_solicitudes (codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto)".
					"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_numrecdoc."','".$as_codtipdoc."',".
					" 			  '".trim($as_cedbene)."','".$as_codpro."',".$ai_monsol.")";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó la Recepcion ".$ls_numrecdoc." a la Solicitud de Pago ".$as_numsol.
								 " Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				if($lb_valido)
				{
					$lb_valido=$this->uf_procesar_asientos($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,
														   $as_codpro,$aa_seguridad);
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_update_estatus_procedencia($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,"E",$aa_seguridad);	
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_historico_recepciones($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,
																	  $ad_fecemisol,"E",$aa_seguridad);	
				}
			}
		}
		else
		{
			if($lb_existe)
			{
				$this->io_mensajes->message("La Recepcion de documentos ".$as_numrecdoc." ya esta tomada en otra Solicitud de Pago"); 
			}
			return false;
		}
		return $lb_valido;
	}// end function uf_insert_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_recepciones($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que verifica que una recepcion de documentos este en estatus de registro
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 05/05/2015								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numrecdoc".
				"  FROM cxp_rd".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'".
				"   AND estprodoc = 'R' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
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
	}// end function uf_validar_recepciones
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
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 26/02/2015 								Fecha Última Modificación : 
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
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 26/02/2015 								Fecha Última Modificación : 
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
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 26/02/2015 								Fecha Última Modificación : 
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
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_recepcion
		//		   Access: public
		//		 Argument: as_numrecdoc // Número de Recepción de Documentos
		//		 		   as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_codtipdoc // Código del Tipo de Documento
		//	  Description: Función que verifica si una recepción existe ó no en otra solicitud de pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 03/04/2015								Fecha Última Modificación : 
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
		//print "<br>".$ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	function uf_procesar_asientos($as_numsol,$as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_asientos
		//		   Access: public
		//		 Argument: as_numsol    // Número de Solicitud de Pago
		//		 		   as_numrecdoc    // Número de Recepción de Documentos
		//		 		   as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_codtipdoc // Código del Tipo de Documento
		//	  Description: Función que verifica si una recepción existe ó no en otra solicitud de pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 05/03/2008								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		if($this->ls_conrecdoc)
		{
			$arrResultado=$this->uf_obtener_procedencia($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc,$as_procedencia);
			$lb_valido=$arrResultado["lb_valido"];
			$as_procedencia=$arrResultado["as_procedencia"];
			unset($arrResultado);
			if($as_procedencia!="SCVSOV")
			{
				$arrResultado=$this->uf_load_cuentaproveedor($as_cedbene,$as_codpro,$as_cuentapro,$as_cuentarecdoc);
				$lb_valido=$arrResultado["lb_valido"];
				$as_cuentapro=$arrResultado["as_cuentapro"];
				$as_cuentarecdoc=$arrResultado["as_cuentarecdoc"];
				unset($arrResultado);
			}
			else
			{
				$lb_valido=$this->uf_load_cuentaviaticos($as_cuentapro,$as_cuentarecdoc);
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
		//		 Argument: as_numrecdoc // Número de Recepción de Documentos
		//		 		   as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_codtipdoc // Código del Tipo de Documento
		//	  Description: Función que verifica si una recepción existe ó no en otra solicitud de pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 03/04/2015								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_obtener_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_procedencia=$row["procede"];
				$lb_valido=true;
			}
		}
		$arrResultado["as_procedencia"]=$as_procedencia;
		$arrResultado["lb_valido"]=$lb_valido;
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
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 03/04/2015								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_crear_asientos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 25/04/2015 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="INSERT INTO cxp_solicitudes_scg (codemp,numsol,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,sc_cuenta,estasicon,monto)".
				"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$as_numrecdoc."','".$as_codtipdoc."','".trim($as_cedbene)."','".$as_codpro."',".
				" 			  '".$as_procede_doc."','".$as_numdoccom."','D','".$as_cuentarecdoc."','".$as_estasicon."','".$ai_monto."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_asiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
				$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_asiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el asiento contable originado de la contabilización de la R.D. ".$as_numrecdoc.
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
		//		 Argument: as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_cuentapro // Cuenta del Proveedor/Beneficiario
		//	  Description: 
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 03/04/2015								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_load_cuentaproveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		//		 Argument: as_codpro    // Código del Proveedor 
		//		 		   as_cedbene   // Cédula del Beneficiario
		//		 		   as_cuentapro // Cuenta del Proveedor/Beneficiario
		//	  Description: 
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 03/04/2015								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_cuentapro="";
		$as_cuentarecdoc="";
		$lb_valido=$this->uf_scv_load_config("SCV","CONFIG","BENEFICIARIO",$as_cuentapro);
		if($lb_valido)
		{
			$lb_valido=$this->uf_scv_load_config("SCV","CONFIG","BENEFICIARIORD",$as_cuentarecdoc);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_cuentapro"]=$as_cuentapro;
		$arrResultado["as_cuentarecdoc"]=$as_cuentarecdoc;
		return $arrResultado;
	}// end function uf_load_cuentaviaticos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_procedencia($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$ls_estatus,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_procedencia
		//		   Access: private
		//	    Arguments: as_numrecdoc // Número de Recepcion de Documentos
		//                 as_codtipdoc // Codigo de Tipo de Documento
		//				   as_cedbene   // Cedula de Beneficiario
		//				   as_codpro    // Código Proveedor
		//				   ls_estatus   // Estatus en que se desea colocar la R.D.
		//                 aa_seguridad // Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que actualiza el estatus de la Recepcion de Documentos
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 25/04/2015 								Fecha Última Modificación : 
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
				$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_update_estatus_procedencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizó en estatus de la recepcion <b>".$as_numrecdoc.
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
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//				   aa_seguridad  // Arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta las Recepciones de Documento de una  Solicitud de Pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 25/04/2015 								Fecha Última Modificación : 
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
				$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_historico_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó un Movimiento en el Historico de la Recepcion ".$as_numrecdoc.
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
	function uf_select_historicord($as_numrecdoc, $as_codtipdoc, $as_cedbene, $as_codpro, $ad_fecemisol,$as_estatus)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_historicord
		//		   Access: private
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Función que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 01/05/2015								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_select_historicord ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	function uf_insert_historico_solicitud($as_numsol, $ad_fecemisol, $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_historico_solicitud
		//		   Access: private
		//	    Arguments: as_numsol    // Número de Solicitud 
		//                 ad_fecemisol //  Fecha de emision de la solicitud
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta un movimiento en el historico de la solicitud de orden de pago
		//	   Creado Por:  Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2015 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO cxp_historico_solicitud (codemp, numsol, fecha, estprodoc)".
				"	  VALUES ('".$this->ls_codemp."','".$as_numsol."','".$ad_fecemisol."','R')";        
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Solicitud MÉTODO->uf_insert_historico_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó un Movimiento en el Historico de la Solicitud de Pago ".$as_numsol.
							 " Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_insert_historico_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------


}
?>