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

class sigesp_cxp_c_anulacionrecepcion
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
		//$this->io_conexion->debug=true;
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
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
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
	function uf_load_recepciones($as_numrecdoc,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion,$as_repcon='0')
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc     // Numero de Recepcion de Documentos
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
		$ls_filtrofrom="";
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
			$ls_filtrofrom = " ,cxp_rd_spg ";
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
				"                                                 AND cxp_rd.ced_bene=rpc_beneficiario.ced_bene)".
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
				"   AND cxp_rd.numrecdoc LIKE '".$as_numrecdoc."' ".
				"   AND cxp_rd.fecregdoc >= '".$ad_fecregdes."' ".
				"   AND cxp_rd.fecregdoc <= '".$ad_fecreghas."' ".
				"   AND (cxp_rd.estprodoc='R' OR cxp_rd.estprodoc='E')".
				"   AND cxp_rd.procede= 'CXPRCD'".
				"   AND cxp_rd.numrecdoc IN (SELECT cxp_dt_solicitudes.numrecdoc ".
				"						       FROM cxp_solicitudes,cxp_dt_solicitudes".
				"						      WHERE cxp_dt_solicitudes.numrecdoc like '".$as_numrecdoc."'".
				"								AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"								AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"								AND (cxp_solicitudes.estprosol='A' OR cxp_solicitudes.estprosol='N'))".
				"   AND cxp_rd.numrecdoc NOT IN (SELECT cxp_sol_dc.numrecdoc ".
				"						           FROM cxp_sol_dc ".
				"						          WHERE cxp_sol_dc.codemp = cxp_rd.codemp".
				"						            AND cxp_sol_dc.numrecdoc = cxp_rd.numrecdoc".
				"						            AND cxp_sol_dc.codtipdoc = cxp_rd.codtipdoc".
				"						            AND cxp_sol_dc.ced_bene = cxp_rd.ced_bene".
				"						            AND cxp_sol_dc.cod_pro = cxp_rd.cod_pro)";
		if($as_tipproben=="B")
		{
			$ls_sql= $ls_sql." AND cxp_rd.ced_bene LIKE '".$as_proben."'";
		}
		else
		{
			$ls_sql= $ls_sql." AND cxp_rd.cod_pro LIKE'".$as_proben."' ";
		}
		$ls_sql= $ls_sql.$ls_filtroest." ORDER BY cxp_rd.numrecdoc ";
		//print "BUSCAR-->".$ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_numero_anulado($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_numero_anulado
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que obtiene el numero de recepcion anulado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$li_i=0;
		$li_lonnumrecdoc = strlen(trim($as_numrecdoc));
		$ls_prenumrec    = substr(trim($as_numrecdoc),0,4);//Prefijo del Número de la Recepción de Documentos.
		while($lb_existe)
		{
			$li_i=$li_i+1;
			if ($ls_prenumrec=='SCV-' && $li_lonnumrecdoc>=14)//Para el Caso de las Solicitudes de Viaticos SCV-00000000000.
			   {
			     $ls_numrecdoc = substr(trim($as_numrecdoc),0,3).substr(trim($as_numrecdoc),5,15);
				 $ls_numrecdocnew="@".$li_i.$ls_numrecdoc;
			   }
			else
			   {
			     $ls_numrecdocnew="@".$li_i.$as_numrecdoc;
			   }

			$li_lonnewnumrecdoc = strlen(trim($ls_numrecdocnew));
			if($li_lonnewnumrecdoc>15)
			{
				$ls_numrecdocnew=substr($ls_numrecdocnew,0,15);			
			}
			$ls_sql="SELECT numrecdoc ".
					"  FROM cxp_rd ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND numrecdoc='".$ls_numrecdocnew."' ".
					"   AND cod_pro='".$as_codpro."' ".
					"   AND ced_bene='".$as_cedben."' ".
					"   AND codtipdoc='".$as_codtipdoc."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_numero_anulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				if(!$row=$this->io_sql->fetch_row($rs_data))
				{
					$lb_existe=false;
				}
			}
		}
		return $ls_numrecdocnew;
	}// end function uf_load_numero_anulado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codcla, dencondoc, fecemidoc,".
				"       fecregdoc, fecvendoc, montotdoc, mondeddoc, moncardoc, tipproben, numref, estprodoc,".
				"       procede, estlibcom, estaprord, fecaprord, usuaprord, numpolcon, estimpmun, montot".
				"  FROM cxp_rd".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
			//	print $ls_sql."<br>"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_validar_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$rs_data=$this->uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_numref= $row["numref"];  					
				$ls_tipproben= $row["tipproben"];										
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_codcla= $row["codcla"]; 
				$ls_dencondoc= $row["dencondoc"];    
				$ls_fecemidoc= $this->io_funciones->uf_formatovalidofecha($row["fecemidoc"]);    
				$ls_fecregdoc= $this->io_funciones->uf_formatovalidofecha($row["fecregdoc"]);    
				$ls_fecvendoc= $this->io_funciones->uf_formatovalidofecha($row["fecvendoc"]);    
				$li_montotdoc= number_format($row["montotdoc"],2,'.',''); 
				$li_mondeddoc= number_format($row["mondeddoc"],2,'.','');    
				$li_moncardoc= number_format($row["moncardoc"],2,'.','');     
				$ls_procede= $row["procede"];
				$li_estlibcom= 0;
				$ls_estaprord= 0;
												   
				$ls_sql=" INSERT INTO cxp_rd (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codcla,dencondoc, fecemidoc,  ".
					  	" 					  fecregdoc, fecvendoc, montotdoc, mondeddoc, moncardoc, tipproben, ".
					  	" 					  numref, estprodoc, procede, estlibcom, estaprord, fecaprord, usuaprord) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_codcla."','".$ls_dencondoc."','".$ls_fecemidoc."','".$ls_fecregdoc."','".$ls_fecvendoc."', ".
					  	" 		   ".$li_montotdoc.",".$li_mondeddoc.",".$li_moncardoc.",'".$ls_tipproben."','".$ls_numref."', ".
					  	" 		  'A', '".$ls_procede."',".$li_estlibcom.",".$ls_estaprord.",'1900-01-01','-')";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_insert_recepcion_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
					if($this->ls_supervisor!=0)
					{
						$ls_fromname="Cuentas Por Pagar";
						$ls_bodyenv="Se le envia la notificación de actualización en el modulo de CXP, se anuló la recepción de documentos  N°.. ";
						$ls_nomper=$_SESSION["la_nomusu"];
						$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numrecdoc,$ls_bodyenv,$ls_nomper);
					}
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
					$this->io_sql->free_result($rs_data);	
				}
			}
		}

		return $lb_valido;
	}// end function uf_insert_recepcion_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_cargos($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_cargos
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, cod_pro, ced_bene, codcar, procede_doc,".
				" 		numdoccom, monobjret, monret, codestpro1, codestpro2, codestpro3, codestpro4,".
				"		codestpro5, estcla, spg_cuenta, porcar, formula".
				"  FROM cxp_rd_cargos".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_cargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_cargos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_cargos_anulado($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_cargos_anulado
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_cargos($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_cargos_anulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codcar= $row["codcar"];  					
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$li_monobjret= number_format($row["monobjret"],2,'.','');    
				$li_monret= number_format($row["monret"],2,'.','');    
				$ls_codestpro1= $row["codestpro1"];    
				$ls_codestpro2= $row["codestpro2"];    
				$ls_codestpro3= $row["codestpro3"];    
				$ls_codestpro4= $row["codestpro4"];    
				$ls_codestpro5= $row["codestpro5"];    
				$ls_estcla= $row["estcla"];    
				$ls_spgcuenta= $row["spg_cuenta"];
				$li_porcar= $row["porcar"];
				$ls_formula= $row["formula"];

				$ls_sql=" INSERT INTO cxp_rd_cargos (codemp, numrecdoc, codtipdoc, cod_pro, ced_bene, codcar, procede_doc,".
						" 					  		 numdoccom, monobjret, monret, codestpro1, codestpro2, codestpro3, codestpro4,".
						"					 		 codestpro5, estcla, spg_cuenta, porcar, formula) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_codpro."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codcar."','".$ls_procededoc."','".$ls_numdoccom."',".$li_monobjret.",".$li_monret.", ".
					  	" 		  '".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."', ".
					  	" 		  '".$ls_estcla."','".$ls_spgcuenta."',".$li_porcar.",'".$ls_formula."')";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_cargos_anulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_insert_dt_cargos_anulado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_deducciones($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_deducciones
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, cod_pro, ced_bene, codded, procede_doc,".
				" 		numdoccom, monobjret, monret, sc_cuenta, porded".
				"  FROM cxp_rd_deducciones".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_deducciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_deducciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_deducciones_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_deducciones_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_deducciones($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_deducciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_codded= $row["codded"];  					
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$li_monobjret= number_format($row["monobjret"],2,'.','');    
				$li_monret= number_format($row["monret"],2,'.','');  
				$ls_sccuenta= $row["sc_cuenta"];    
				$li_porded= $row["porded"];

				$ls_sql=" INSERT INTO cxp_rd_deducciones (codemp, numrecdoc, codtipdoc, cod_pro, ced_bene, codded, procede_doc,".
						" 					 			  numdoccom, monobjret, monret, sc_cuenta, porded) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_codpro."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codded."','".$ls_procededoc."','".$ls_numdoccom."',".$li_monobjret.",".$li_monret.", ".
					  	" 		  '".$ls_sccuenta."',".$li_porded.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_deducciones_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_insert_dt_deducciones_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_spg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom, codestpro, estcla, spg_cuenta, monto".
				"  FROM cxp_rd_spg".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_spg_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_spg_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_spg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_spg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$ls_codestpro= $row["codestpro"];  					
				$ls_estcla= $row["estcla"];  					
				$ls_spgcuenta= $row["spg_cuenta"];
				$li_monto= number_format($row["monto"],2,'.',''); 

				$ls_sql=" INSERT INTO cxp_rd_spg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
						"					 	  codestpro, estcla, spg_cuenta, monto) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_procededoc."','".$ls_numdoccom."','".$ls_codestpro."','".$ls_estcla."','".$ls_spgcuenta."',".$li_monto.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_spg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}

		return $lb_valido;
	}// end function uf_insert_dt_spg_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_scg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_scg
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
				"		debhab, sc_cuenta, monto, estgenasi, estasicon ".
				"  FROM cxp_rd_scg".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_scg_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_scg_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_scg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_scg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_procededoc= $row["procede_doc"]; 
				$ls_numdoccom= $row["numdoccom"];    
				$ls_debhab= $row["debhab"];  					
				$ls_sccuenta= $row["sc_cuenta"];    
				$li_monto= number_format($row["monto"],2,'.',''); 
				$ls_estgenasi=trim($row["estgenasi"]);
				if($ls_estgenasi=="")
				{
					$ls_estgenasi=0;
				}
				$ls_estasicon= $row["estasicon"];

				$ls_sql=" INSERT INTO cxp_rd_scg (codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, procede_doc, numdoccom,".
						"						  debhab, sc_cuenta, estasicon, monto, estgenasi) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."','".$ls_codpro."', ".
					  	" 		  '".$ls_procededoc."','".$ls_numdoccom."','".$ls_debhab."','".$ls_sccuenta."','".$ls_estasicon."',".$li_monto.",".$ls_estgenasi.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_scg_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}

		return $lb_valido;
	}// end function uf_insert_dt_scg_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_solicitud
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto".
				"  FROM cxp_dt_solicitudes".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_amortizacion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_amortizacion
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro,codamo, cuenta, montotamo, monsal, monamo".
				"  FROM cxp_rd_amortizacion".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_amortizacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_anticipo($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_anticipo
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que obtiene los datos de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codemp, numrecdoc, codtipdoc, ced_bene, cod_pro, codamo, monto".
				"  FROM cxp_dt_amortizacion".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_select_dt_anticipo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			return $rs_data;
		}
	}// end function uf_select_dt_anticipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_solicitud_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_solicitud_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$rs_data=$this->uf_select_dt_solicitud($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_solicitud_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_numsol= $row["numsol"]; 
				$li_monto= number_format($row["monto"],2,'.',''); 

				$ls_sql=" INSERT INTO cxp_dt_solicitudes (codemp, numsol, numrecdoc, codtipdoc, ced_bene, cod_pro, monto) ".
					  	" VALUES ('".$this->ls_codemp."','".$ls_numsol."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codpro."',".$li_monto.")";	
			}
		}

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_solicitud_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Insertó la Recepcion de Documentos Anulada <b>".$as_numrecdoc."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_insert_dt_solicitud_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_amortizacion_anulada($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_amortizacion_anulada
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_amortizacion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_amortizacion_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_codamo= $row["codamo"]; 
				$ls_cuenta=$row["cuenta"];
				$li_monamo= number_format($row["monamo"],2,'.',''); 
				$li_monsal= number_format($row["monsal"],2,'.',''); 
				$li_montotamo= number_format($row["montotamo"],2,'.',''); 
				$lb_valido=$this->uf_update_anticipo($ls_codtipdoc,$ls_codpro,$ls_cedbene,$ls_codamo,$li_monsal,$aa_seguridad);

				$ls_sql=" INSERT INTO cxp_rd_amortizacion (codemp,  numrecdoc, codtipdoc, ced_bene, cod_pro, codamo, cuenta, ".
						"                                  monamo, monsal, montotamo) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codpro."','".$ls_codamo."','".$ls_cuenta."',".$li_monamo.",".$li_monsal.",".$li_montotamo.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_solicitud_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó la Amortizacion Anulada <b>".$ls_codamo." - ".$as_numrecdocnew."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$this->io_sql->free_result($rs_data);	
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_dt_solicitud_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dt_anticipo_anulado($as_numrecdoc,$as_numrecdocnew,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dt_anticipo_anulado
		//		   Access: private
		//	    Arguments: as_numrecdoc    //  Número de Recepcion de Documentos Actual
		//                 as_numrecdocnew //  Número de Recepcion de Documentos Anulada
		//                 as_codpro       //  Codigo de Proveedor
		//                 as_cedben       //  Codigo de Beneficiario
		//                 as_codtipdoc    //  Codigo de Tipo de Documento
		//                 ad_fecaprord    //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad    //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los datos de una recepcion a anular y los inserta en la anulada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_dt_anticipo($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_amortizacion_anulada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{

				$ls_codtipdoc= $row["codtipdoc"]; 
				$ls_codpro= $row["cod_pro"];					   
				$ls_cedbene= $row["ced_bene"];										
				$ls_codamo= $row["codamo"]; 
				$li_monto= number_format($row["monto"],2,'.',''); 
				$lb_valido=$this->uf_update_anticipo($ls_codtipdoc,$ls_codpro,$ls_cedbene,$ls_codamo,$li_monto,$aa_seguridad);

				$ls_sql=" INSERT INTO cxp_dt_amortizacion(codemp,  numrecdoc, codtipdoc, ced_bene, cod_pro, codamo, monto) ".
					  	" VALUES ('".$this->ls_codemp."','".$as_numrecdocnew."','".$ls_codtipdoc."','".$ls_cedbene."', ".
					  	" 		  '".$ls_codpro."','".$ls_codamo."',".$li_monto.")";	
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_insert_dt_anticipo_anulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="UPDATE";
					$ls_descripcion ="Insertó el Anticipo Anulado de la Amortización <b>".$ls_codamo." - ".$as_numrecdocnew."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$this->io_sql->free_result($rs_data);	
				}
			}
		}
		return $lb_valido;
	}// end function uf_insert_dt_solicitud_anulada
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_anticipo($as_codtipdoc,$as_codpro,$as_cedbene,$as_codamo,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_recepcion
		//		   Access: private
		//	    Arguments: as_numsol    //  Número de Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que actualiza el estatus de la solicitud 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/12/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE cxp_rd_amortizacion ".
				"   SET monsal = (monsal + ".$ai_monto.") ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codamo = '".$as_codamo."' ".
				"   AND cod_pro='".$as_codpro."'".
				"   AND ced_bene='".$as_cedbene."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			return false;
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_update_anticipo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}// end function uf_update_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_estatus($as_numrecdoc,$as_cedbene,$as_codpro,$as_codtipdoc,$as_estprodoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_estatus
		//		   Access: public
		//		 Argument: as_numrecdoc // Número de Recepción de Documentos
		//		 		   as_tipodestino // Tipo de Destino (Proveedor ó Beneficiario)
		//		 		   as_codprovben // Código del Proveedor ó Beneficiario
		//		 		   as_codtipdoc // Código del Tipo de Documento
		//	  Description: Función que busca en la tabla de la recepcion el estatus de la misma
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 06/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////		
		$lb_valido=true;
		$ls_sql="SELECT estprodoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND numrecdoc='".$as_numrecdoc."' ".
				"	AND codtipdoc='".$as_codtipdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_load_estatus ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_estprodoc=$row["estprodoc"];
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_estprodoc"]=$as_estprodoc;
		return $arrResultado;
	}// end function uf_load_estatus
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detallesrecepcion($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detallesrecepcion
		//		   Access: private
		//	    Arguments: as_numrecdoc  // Número de recepción de documentos
		//				   as_codtipdoc  // Tipo de Documento
		//				   as_cedbene  // Cédula del Beneficiario
		//				   as_codpro  // Código de proveedor
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina los detalles de una recepcion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM cxp_rd_cargos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND numrecdoc='".$as_numrecdoc."' ".
				"	AND codtipdoc='".$as_codtipdoc."' ".
				"	AND cod_pro='".$as_codpro."' ".
				"	AND ced_bene='".$as_cedbene."'";		  
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_deducciones ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_scg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_spg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_dt_solicitudes ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_historico_rd ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_rd_amortizacion ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			$ls_sql="DELETE FROM cxp_dt_amortizacion ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"	AND numrecdoc='".$as_numrecdoc."' ".
					"	AND codtipdoc='".$as_codtipdoc."' ".
					"	AND cod_pro='".$as_codpro."' ".
					"	AND ced_bene='".$as_cedbene."'";		  
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Recepcion MÉTODO->uf_delete_detallesrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó todos los detalles de Recepción de Documentos ".$as_numrecdoc." Tipo ".$as_codtipdoc." Beneficiario ".$as_cedbene.
							 "Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_detallesrecepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete
		//		   Access: public (sigesp_cxp_p_recepcion.php)
		//	    Arguments: as_numrecdoc  // Número de recepción de documentos
		//				   as_tipodestino  // Tipo Destino
		//				   as_codprovben  // Código de proveedor ó beneficiario
		//				   as_codtipdoc  // Tipo de Documento
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que valida y elimina la recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 07/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$as_codtipdoc=substr($as_codtipdoc,0,5);
		$lb_encontrado=$this->uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedbene,$as_codtipdoc);
		if($lb_encontrado)
		{
			$arrResultado=$this->uf_load_estatus($as_numrecdoc,$as_cedbene,$as_codpro,$as_codtipdoc,$ls_estprodoc);
			$lb_valido=$arrResultado["lb_valido"];
			$ls_estprodoc=$arrResultado["as_estprodoc"];
			if($ls_estprodoc!="R")
			{
				if($ls_estprodoc!="E")
				{
					$this->io_mensajes->message("La Recepción de Documentos no se puede eliminar, Tiene Movimientos.");           
					$lb_valido=false;
				}
			}
			if($lb_valido)
			{	
				$lb_valido=$this->uf_delete_detallesrecepcion($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro,$aa_seguridad);
			}			
			if($lb_valido)
			{	
				$ls_sql="DELETE FROM cxp_rd ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"	AND numrecdoc='".$as_numrecdoc."' ".
						"	AND codtipdoc='".$as_codtipdoc."' ".
						"	AND cod_pro='".$as_codpro."' ".
						"	AND ced_bene='".$as_cedbene."' ";		  
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{print $this->io_sql->message;
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Recepción MÉTODO->uf_eliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimino la Recepción de Documentos ".$as_numrecdoc." Tipo ".$as_codtipdoc." Beneficiario ".$as_cedbene.
									 "Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				}	
			}
		}
		else
		{
			$this->io_mensajes->message("No se encontro la Recepcion de Documentos");
			$lb_valido=false;	
		}
		return $lb_valido;
	}// end function uf_delete
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_anular_recepcion($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_anular_recepcion
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		//				   aa_seguridad  //  Arreglo de Seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que obtiene el numero de recepcion anulado
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 12/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_numrecdocnew="";
		$this->io_sql->begin_transaction();				
		$ls_numrecdocnew=$this->uf_load_numero_anulado($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc);
		if($ls_numrecdocnew!="")
		{
			$ls_numrecdocnew = trim($ls_numrecdocnew);
			$lb_valido=$this->uf_insert_recepcion_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
														  $as_codtipdoc,$aa_seguridad);
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_dt_cargos_anulado($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
															  $as_codtipdoc,$aa_seguridad);
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_dt_deducciones_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
																	   $as_codtipdoc,$aa_seguridad);
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_dt_spg_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
																   $as_codtipdoc,$aa_seguridad);
						if($lb_valido)
						{
							$lb_valido=$this->uf_insert_dt_scg_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,$as_cedben,
																	   $as_codtipdoc,$aa_seguridad);
							if($lb_valido)
							{
								$lb_valido=$this->uf_insert_dt_solicitud_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,
																				  $as_cedben,$as_codtipdoc,$aa_seguridad);
								if($lb_valido)
								{
									$lb_valido=$this->uf_insert_dt_amortizacion_anulada($as_numrecdoc,$ls_numrecdocnew,$as_codpro,
																					     $as_cedben,$as_codtipdoc,$aa_seguridad);
									if($lb_valido)
									{
										$lb_valido=$this->uf_insert_dt_anticipo_anulado($as_numrecdoc,$ls_numrecdocnew,$as_codpro,
																					     $as_cedben,$as_codtipdoc,$aa_seguridad);
										if($lb_valido)
										{
											$lb_valido=$this->uf_delete($as_numrecdoc,$as_codtipdoc,$as_cedben,$as_codpro,$aa_seguridad);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("La Recepción de Documentos fue Anulada.");
			$this->io_sql->commit();
		}
		else
		{
			$this->io_mensajes->message("Ocurrio un Error al Anular la Recepción de Documentos."); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_load_numero_anulado
	//-----------------------------------------------------------------------------------------------------------------------------------	

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