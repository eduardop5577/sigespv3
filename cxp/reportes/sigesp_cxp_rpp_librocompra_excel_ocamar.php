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

    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if (!array_key_exists("la_logusr",$_SESSION))
	{
		 print "<script language=JavaScript>";
		 print "close();";
		 print "</script>";		
	}
    ini_set('memory_limit','512M');
	ini_set('max_execution_time ','0');
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Ing. Nelson Barraez
		// Fecha Creación: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte Libro de Compra para el periodo ".$as_periodo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_librocompra.php",$ls_descripcion);
		return $lb_valido;
	}
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "libro_compra.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_cxp_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
    $io_fecha = new class_fecha();
	$io_in    = new sigesp_include();
	$con      = $io_in->uf_conectar();
    $io_sql   = new class_sql($con);
	$io_report= new sigesp_cxp_class_report("../../");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();		
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();		
	//----------------------------------------------------  Parmetros del encabezado  -----------------------------------------------
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_agno=$io_fun_cxp->uf_obtenervalor_get("agno","");
	$ls_tiprep=$io_fun_cxp->uf_obtenervalor_get("tiprep","M");
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	$ls_titulo     = "<b>Libro de Compras</b>";
	$li_lastday    = $io_fecha->uf_last_day($ls_mes,$ls_agno);
	$li_lastday    = substr($li_lastday,0,2);
	$ls_mesletras        = $io_fecha->uf_load_nombre_mes($ls_mes);
/*	switch($ls_tiprep)
	{
		case"M":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-01';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-'.$li_lastday;
			$ls_periodo    = "MENSUAL  MES: ".$ls_mesletras."    AÑO".$ls_agno."";
		break;
		case"PQ":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-01';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-15';
			$ls_periodo    = "PRIMERA QUINCENA   MES: ".$ls_mesletras."    AÑO".$ls_agno."";
		break;
		case"SQ":
			$as_fechadesde = $ls_agno.'-'.$ls_mes.'-16';
			$as_fechahasta = $ls_agno.'-'.$ls_mes.'-'.$li_lastday;
			$ls_periodo    = "SEGUNDA QUINCENA MES: ".$ls_mesletras."    AÑO".$ls_agno."";
		break;
	}*/
	$fecemides=$io_fun_cxp->uf_obtenervalor_get("fecemides","");
	$fecemihas=$io_fun_cxp->uf_obtenervalor_get("fecemihas","");
	$as_fechadesde=$io_funciones->uf_convertirdatetobd($fecemides);
	$as_fechahasta=$io_funciones->uf_convertirdatetobd($fecemihas);
	$ls_periodo    = "PERIODO DESDE: ".$fecemides."    HASTA: ".$fecemihas."";

	//--------------------------------------------------------------------------------------------------------------------------------
	$ld_monto    = 0;
	$ld_impuesto = 0;
	$ld_sumcom   = 0;
	$ld_baseimp  = 0;
	$arremp      = $_SESSION["la_empresa"];
    $ls_codemp   = $arremp["codemp"];
	set_time_limit(1800);
	//-------formato para el reporte----------------------------------------------------------
	$lo_encabezado= &$lo_libro->addformat();
	$lo_encabezado->set_bold();
	$lo_encabezado->set_font("Verdana");
	$lo_encabezado->set_align('center');
	$lo_encabezado->set_size('11');
	$lo_titulo= &$lo_libro->addformat();
	$lo_titulo->set_text_wrap();
	$lo_titulo->set_bold();
	$lo_titulo->set_font("Verdana");
	$lo_titulo->set_align('center');
	$lo_titulo->set_size('9');		
	$lo_datacenter= &$lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_dataleft= &$lo_libro->addformat();
	$lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');
	$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');
	
	$lo_dataright2= &$lo_libro->addformat(array('num_format' => '#,##'));
	$lo_dataright2->set_font("Verdana");
	$lo_dataright2->set_align('right');
	$lo_dataright2->set_size('9');	
	$lo_hoja->set_column(0,0,50);
	$lo_hoja->set_column(1,2,15);	
	$lo_hoja->set_column(3,3,40);
	$lo_hoja->set_column(4,4,25);
	$lo_hoja->set_column(5,5,15);
	$lo_hoja->set_column(6,6,20);	
	$lo_hoja->set_column(7,7,20);	
	$lo_hoja->set_column(8,8,15);	
	$lo_hoja->set_column(9,9,15);
	$lo_hoja->set_column(10,10,15);	
	$lo_hoja->set_column(12,12,15);	
	$lo_hoja->set_column(13,13,15);	
	$lo_hoja->set_column(14,14,15);	
	$lo_hoja->set_column(15,15,15);
	$lo_hoja->set_column(16,16,15);	
	$lo_hoja->set_column(17,17,10);	
	$lo_hoja->set_column(18,18,15);	
	$lo_hoja->set_column(19,19,15);		
	$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
	$lo_hoja->write(1,3,$ls_periodo,$lo_encabezado);			
	$lo_hoja->write(4,0, "Nro. Oper",$lo_titulo);
	$lo_hoja->write(4,1, "Fecha Factura",$lo_titulo);	
	$lo_hoja->write(4,2, "RIF",$lo_titulo);	
	$lo_hoja->write(4,3, "Número o Razón Social",$lo_titulo);
	$lo_hoja->write(4,4, "Nro Planilla Importacin (C-80 o c-81)",$lo_titulo);
	$lo_hoja->write(4,5, "Nro de Factura",$lo_titulo);
	$lo_hoja->write(4,6, "Nro de Control",$lo_titulo);
	$lo_hoja->write(4,7, "Nro Nota Debito",$lo_titulo);
	$lo_hoja->write(4,8, "Nro Nota Credito",$lo_titulo);
	$lo_hoja->write(4,9, "Tipo de Transacc.",$lo_titulo);
	$lo_hoja->write(4,10, "Nro de Factura Afectada",$lo_titulo);
	$lo_hoja->write(4,11, "Total de Compra Incluyendo IVA",$lo_titulo);
	$lo_hoja->write(4,12, "Compra sin Derecho a Credito IVA",$lo_titulo);
	$lo_hoja->write(3,13, "Compras",$lo_titulo);
	$lo_hoja->write(3,14, "Internas o",$lo_titulo);
	$lo_hoja->write(3,15, "Importaciones 12%",$lo_titulo);
	$lo_hoja->write(4,13, "Base Imponible ",$lo_titulo);
	$lo_hoja->write(4,14, " % ",$lo_titulo);
	$lo_hoja->write(4,15, "Impuesto Iva ",$lo_titulo);
	$lo_hoja->write(3,16, "Compras",$lo_titulo);
	$lo_hoja->write(3,17, "Internas o",$lo_titulo);
	$lo_hoja->write(3,18, "Importaciones 8%",$lo_titulo);
	$lo_hoja->write(4,16, "Base Imponible ",$lo_titulo);
	$lo_hoja->write(4,17, " % ",$lo_titulo);
	$lo_hoja->write(4,18, "Impuesto Iva ",$lo_titulo);
	//------------------------------------------------------------------------------------------------------
	$arrResultado=$io_report->uf_select_report_libcompra_ocamar($as_fechadesde,$as_fechahasta,"1");
	$lb_valido=$arrResultado["lb_valido"];
	$rs_resultado=$arrResultado["rs_data"];
	unset($arrResultado);
	$ldec_totimp8      = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 8%.
	$ldec_totimp9      = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 14%.
	$ldec_totimp25     = 0; //Variable de tipo acumulador que almacenara el monto total de los impuestos a 25%.
	$ldec_totimpret8   = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 8%.
	$ldec_totimpret9   = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 14%.
	$ldec_totimpret25  = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 25%.
	$ldec_totbasimp8   = 0;
	$ldec_totbasimp9   = 0;
	$ldec_totbasimp25  = 0;
	$ldec_totcomsiniva = 0;
	$ldec_totcomconiva  = 0; //Variable de tipo acumulador que almacenara el monto total retenido de los impuestos a 25%.
	$ldec_totimpiva   = 0;
	$ldec_totivaret   = 0;
	$totbaseimp8=0;
	$totbaseimp12=0;
	$totimp8=0;
	$totimp12=0;
	$baseimpgrl=0;
	$impgrl=0;
	$li_row=4;
	if($lb_valido)
	{
		$li=0;
		$li_aux=0;
		while($row=$io_report->io_sql->fetch_row($rs_resultado))	
		{
			$li++;
			$li_row++;
			$ls_numnc="";
			$ls_numnd="";
		    $ldec_monret=0;
			$ldec_totbaseimp=0;
			$ldec_totmonimp=0;
			$ls_numrecdoc=$row["numrecdoc"];
			$ls_tipproben=$row["tipproben"];
			$ls_codpro=$row["cod_pro"];
			$ls_cedben=$row["ced_bene"];
			$ldec_montoret=$row["monret"];
			$ldec_montodoc=$row["montotdoc"];
			$ldec_mondeddoc=$row["mondeddoc"];
			$ls_codtipdoc =$row["codtipdoc"];
			$ls_porded =$row["porded"];
			$li_total_cargos=0;
			if($ls_tipproben=='P')
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_proveedor WHERE cod_pro='".$ls_codpro."'");
				$ls_rif=$la_provben["rifpro"];
				$ls_nombre=$la_provben["nompro"];
			}	
			else
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_beneficiario WHERE ced_bene='".$ls_cedben."'");
				$ls_rif=$la_provben["rifben"];
				$ls_nombre=$la_provben["nombene"]." ".$la_provben["apebene"];
			}
			$ls_cheque=$io_report->uf_select_data($io_sql,"SELECT distinct cxp_sol_banco.numdoc AS numdoc".
														  "  FROM cxp_dt_solicitudes, cxp_sol_banco".
														  " WHERE cxp_dt_solicitudes.codemp='".$ls_codemp."'".
														  "   AND cxp_dt_solicitudes.numrecdoc='".$ls_numrecdoc."'".
														  "   AND cxp_dt_solicitudes.cod_pro='".$ls_codpro."'".
														  "   AND cxp_dt_solicitudes.ced_bene='".$ls_cedben."'".
														  "   AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
														  "   AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol","numdoc");
	if($ls_tiporeporte==1)
	{
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT DISTINCT max(a.numrecdoc) as numrecdoc,max(a.monobjretaux) as monobjret,SUM(a.monretaux) as monret,max(a.porded) as porded,max(b.codret) as codret,max(b.numcom) as numcom,max(b.iva_retaux) as iva_ret,max(tiptrans) as tiptrans".
															  "   FROM cxp_rd_deducciones a,scb_dt_cmp_ret b                                                                         ".
															  "  WHERE a.codemp=b.codemp AND a.numrecdoc=b.numfac AND a.codemp='".$ls_codemp."' AND a.numrecdoc='".$ls_numrecdoc."' AND ".
															  "        a.cod_pro='".$ls_codpro."' AND a.ced_bene='".$ls_cedben."'                                              ".
															  " GROUP BY a.numrecdoc ");
	}
	else
	{
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT cxp_rd_deducciones.numrecdoc as numrecdoc, cxp_rd_deducciones.monobjret as monobjret, SUM(cxp_rd_deducciones.monret) as monret, cxp_rd_deducciones.porded as porded, scb_dt_cmp_ret.codret as codret, ".
                                                             "       scb_dt_cmp_ret.numcom as numcom, scb_dt_cmp_ret.iva_ret as iva_ret, scb_dt_cmp_ret.tiptrans as tiptrans ".
  															 "FROM cxp_rd_deducciones, cxp_rd, cxp_dt_solicitudes, sigesp_deducciones, scb_cmp_ret, scb_dt_cmp_ret ".
															 "WHERE (cxp_rd_deducciones.codemp = '".$ls_codemp."' AND cxp_rd_deducciones.numrecdoc = '".$ls_numrecdoc."' AND cxp_rd_deducciones.codtipdoc = '".$ls_codtipdoc."' AND cxp_rd_deducciones.cod_pro = '".$ls_codpro."' AND cxp_rd_deducciones.ced_bene = '".$ls_cedben."') AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_rd.codemp AND cxp_rd_deducciones.numrecdoc = cxp_rd.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_rd.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_rd.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_rd.ced_bene) AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_dt_solicitudes.codemp AND cxp_rd_deducciones.numrecdoc = cxp_dt_solicitudes.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_dt_solicitudes.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_dt_solicitudes.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_dt_solicitudes.ced_bene) AND ". 
															 "      (cxp_rd_deducciones.codded = sigesp_deducciones.codded AND sigesp_deducciones.iva = 1) AND ".
															 "      (cxp_rd_deducciones.codemp = scb_dt_cmp_ret.codemp AND cxp_rd_deducciones.numrecdoc = scb_dt_cmp_ret.numfac AND substring(scb_dt_cmp_ret.numsop,1,15) = cxp_dt_solicitudes.numsol) AND ". 
															 "       scb_dt_cmp_ret.numcom = scb_cmp_ret.numcom AND scb_cmp_ret.estcmpret = '1' AND scb_dt_cmp_ret.codret = '0000000001' AND".
															 "      (scb_cmp_ret.codsujret = (CASE cxp_rd_deducciones.ced_bene WHEN '----------' THEN cxp_rd_deducciones.cod_pro ELSE cxp_rd_deducciones.ced_bene END) AND cxp_rd_deducciones.codemp = scb_cmp_ret.codemp) AND ".
															 "       substring(scb_cmp_ret.perfiscal,1,4) = substring(CAST(cxp_rd.fecregdoc as text),1,4) AND substring(scb_cmp_ret.perfiscal,5,2) = substring(CAST(cxp_rd.fecregdoc as text),6,2) AND scb_cmp_ret.estcmpret = '1' AND ".
															 "       scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
															 "GROUP BY  cxp_rd_deducciones.numrecdoc , cxp_rd_deducciones.monobjret ,  cxp_rd_deducciones.porded , scb_dt_cmp_ret.codret , ".
															 "          scb_dt_cmp_ret.numcom, scb_dt_cmp_ret.iva_ret , scb_dt_cmp_ret.tiptrans ");
		    // Fin de la consulta cambiaba
 	}
			if(count((array)$la_cmpret)>0)
			{
				$ls_codret = $la_cmpret["codret"];
				if ($ls_codret=='0000000001')
				{
					 $ldec_monret    = $la_cmpret["monret"];  
					 $ls_cmpret      = $la_cmpret["numcom"];
					 $ldec_monobjret = $la_cmpret["monobjret"];
					 $ldec_porded    = $la_cmpret["porded"];
					 $ldec_ivaret    = $la_cmpret["iva_ret"];
					 $ls_tiptrans    = $la_cmpret["tiptrans"];
				}
				else
				{
					 $ldec_monret    = 0; 
					 $ls_cmpret      = '';
					 $ldec_monobjret = 0;
					 $ldec_porded    = 0;
					 $ldec_ivaret    = 0;
 					 $ls_tiptrans    = "";
				}													  
			}
		    else
			{
				$ldec_monret    = $ldec_monret+$ldec_montoret;  
				$ls_cmpret      = '';
				$ldec_monobjret = 0;
				$ldec_porded    = 0;
				$ls_tiptrans    = "";
				$ldec_ivaret    = 0;
			}
			//Consultas Modificadas por Ofimatica de Venezuela el 23-05-2011, se elimina el código del cargo y se agrupa y suma por el tipo de iva y se 
			//cambia el metodo uf_select_rowdata por uf_select_arraydata porque pueden aplicar varios ivas distintos.		
			if($ls_tiporeporte==1)
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT cxp_rd_cargos.monobjretaux as basimp,cxp_rd_cargos.porcar,cxp_rd_cargos.monretaux as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'");
			}
			else
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT SUM(cxp_rd_cargos.monobjret) as basimp,MAX(cxp_rd_cargos.porcar) AS porcar,SUM(cxp_rd_cargos.monret) as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codcar=sigesp_cargos.codcar ".
																 "   AND cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'".
																 " GROUP BY cxp_rd_cargos.codemp, cxp_rd_cargos.numrecdoc, cxp_rd_cargos.cod_pro, cxp_rd_cargos.ced_bene, cxp_rd_cargos.codtipdoc,tipo_iva ORDER BY porcar ASC");
			}
			if(!empty($la_cargos))
			{
				$li_total_cargos=count((array)$la_cargos["porcar"]);
				if($li_total_cargos>0)
				{
					$ldec_porcar=$la_cargos["porcar"][1];
					$ldec_baseimp=$la_cargos["basimp"][1];
					$ldec_monimp=$la_cargos["impiva"][1];
					$ldec_totbaseimp=$la_cargos["basimp"][1];
					$ldec_totmonimp=$la_cargos["impiva"][1];
					//Modificado por Ofimatica de Venezuela el 23-05-2011, se toma el tipo de iva directo del arreglo resultante de la consulta.	
					$ldec_porcentaje_conf=$la_cargos["tipo_iva"][1];
					//$ldec_cargo=$la_cargos["codcar"]; //Código utilizado anteriormente modificado según descripción superior
					//$ldec_porcentaje_conf=$io_report->uf_porcentaje_eval($ldec_cargo); //Código utilizado anteriormente modificado según descripción superior
				}
				else
				{
					$ldec_porcar="";
					$ldec_baseimp=0;
					$ldec_monimp=0;	
					$ldec_porcentaje_conf=0;	
				}
			}
			else
			{
				$ldec_porcar="";
				$ldec_baseimp=0;
				$ldec_monimp=0;	
				$ldec_porcentaje_conf=0;			
			}	
			$li_habman=0;
			$li_debman=0;
			if($_SESSION["la_empresa"]["confiva"]=='P')
			{
				$la_manualesd=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='D'".
																 "   AND estasicon='M'");
				
				$la_manualesh=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='H'".
																 "   AND estasicon='M'");
			
				if(count((array)$la_manualesd)>0)
				{
					$li_debman=$la_manualesd["monto"];
				}	
				if(count((array)$la_manualesh)>0)
				{
					$li_habman=$la_manualesh["monto"];
				}	
			}
			else
			{
				$la_manualesh=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='H'".
																 "   AND estasicon='M'");
				if(count((array)$la_manualesh)>0)
				{
					$li_habman=$la_manualesh["monto"];
				}	
			}
//			print "--->".$ldec_montodoc."<br>";
//			print " ldec_montodoc->".$ldec_montodoc."  li_debman->".$li_debman." <br><br><br>";
//			print $ls_nombre." MONTOTDOC->".$ldec_montodoc."  DED->".$ldec_mondeddoc." HAB->".$li_habman." DEB->".$li_debman."<br>";
			$ldec_montodoc     = $ldec_montodoc+$ldec_mondeddoc+$li_habman-$li_debman;			 
//			print "2--->".$ldec_montodoc."<br>";
//			print $ls_numrecdoc." MONTOTDOC->".$ldec_montodoc."  BASE IMP->".$ldec_baseimp." MONIMP->".$ldec_monimp."<br>";
			
//			print " ldec_montodoc->".$ldec_montodoc."  li_debman->".$li_debman." <br><br><br>";
			//print $ls_nombre." MONTOTDOC->".$ldec_montodoc."  DED->".$ldec_mondeddoc." HAB->".$li_habman." DEB->".$li_debman."<br>";
			$ldec_sinderiva    = $ldec_montodoc-$ldec_baseimp-$ldec_monimp;// Total de la Compra sin Derecho a Crédito Iva.
			$ldec_sinderiva    =number_format($ldec_sinderiva,4,".","");
			if(($ldec_sinderiva<0)&&($ldec_sinderiva>-1))
			{
				$ldec_sinderiva=0;
			}
			$ldec_totimpiva   = $ldec_totimpiva+$ldec_monimp;
			$ldec_totivaret   = $ldec_totivaret+$ldec_montoret;
			$ldec_totcomconiva= $ldec_totcomconiva+$ldec_montodoc;
			switch ($ldec_porcar)
			{
				case "8":
					$ldec_baseimp8=$ldec_baseimp;
					$ldec_baseimp12=0;
					$ldec_monimp8=$ldec_monimp;
					$ldec_monimp12=0;
					$ldec_porcar8=$ldec_porcar;
					$ldec_porcar12=0;
					$totbaseimp8=$totbaseimp8+$ldec_baseimp8;
					$totimp8=$totimp8+$ldec_monimp8;
				break;
				case "16":
					$ldec_baseimp8=0;
					$ldec_baseimp12=$ldec_baseimp;
					$ldec_monimp8=0;
					$ldec_monimp12=$ldec_monimp;
					$ldec_porcar8=0;
					$ldec_porcar12=$ldec_porcar;
					$totbaseimp12=$totbaseimp12+$ldec_baseimp12;
					$totimp12=$totimp12+$ldec_monimp12;
				break;
				default:
					$ldec_baseimp8=0;
					$ldec_baseimp12=0;
					$ldec_monimp8=0;
					$ldec_monimp12=0;
					$ldec_porcar8=0;
					$ldec_porcar12=0;
				break;
			}
			 $lo_hoja->write($li_row, 0, $li_row, $lo_datacenter);
			 $lo_hoja->write($li_row, 1, $io_report->io_funciones->uf_convertirfecmostrar($row["fecemidoc"]), $lo_datacenter);
			 $lo_hoja->write($li_row, 2, $ls_rif, $lo_datacenter);
			 $lo_hoja->write($li_row, 3, $ls_nombre, $lo_dataleft);
			 $lo_hoja->write($li_row, 4, '', $lo_datacenter);
			 $lo_hoja->write($li_row, 5, trim($ls_numrecdoc), $lo_datacenter);
			 $lo_hoja->write($li_row, 6, $row["numref"], $lo_datacenter);
			 $lo_hoja->write($li_row, 7,$ls_numnd, $lo_datacenter);
			 $lo_hoja->write($li_row, 8,$ls_numnc, $lo_datacenter);
			 $lo_hoja->write($li_row, 9,$ls_tiptrans, $lo_datacenter);
			 $lo_hoja->write($li_row, 10,trim($row["numrecdoc"]), $lo_datacenter);
			 $lo_hoja->write($li_row, 11,$ldec_montodoc, $lo_dataright);
			 $lo_hoja->write($li_row, 12,$ldec_sinderiva, $lo_dataright);
			 $lo_hoja->write($li_row, 13,$ldec_baseimp12, $lo_dataright);
			 $lo_hoja->write($li_row, 14,$ldec_porcar12, $lo_datacenter);
			 $lo_hoja->write($li_row, 15,$ldec_monimp12, $lo_dataright);
			 $lo_hoja->write($li_row, 16,$ldec_baseimp8, $lo_dataright);
			 $lo_hoja->write($li_row, 17,$ldec_porcar8, $lo_datacenter);
			 $lo_hoja->write($li_row, 18,$ldec_monimp8, $lo_dataright);
						 	
			 $li_porcentaje   = intval($ldec_porcentaje_conf);
			 if ($ldec_porcar>0)
			 {
				switch ($li_porcentaje){
				  case '2':
					$ldec_totimp8    = ($ldec_totimp8+$ldec_monimp);
					$ldec_totbasimp8 = ($ldec_totbasimp8+$ldec_baseimp);
					$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
					break;
				  case '1':
					$ldec_totimp9    = ($ldec_totimp9+$ldec_monimp);  
					$ldec_totbasimp9 = ($ldec_totbasimp9+$ldec_baseimp);
					$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
					break;
				  case '3':
					$ldec_totimp25    = ($ldec_totimp25+$ldec_monimp);  
					$ldec_totbasimp25 = ($ldec_totbasimp28+$ldec_baseimp);
					$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
					break;
				  }
			 }
			 
		 $ldec_totcomsiniva = ($ldec_totcomsiniva+$ldec_sinderiva);	
		 $ldec_basimpga     = ($ldec_totbasimp9+$ldec_totbasimp25);//Total Base Imponible Compras Internas Afectadas en Alicuota General + Adicional(9% y 25%).
		 $ldec_totgenadi    = ($ldec_totimp9+$ldec_totimp25);//Total Compras Internas Afectadas en Alicuota Reducida. Impuestos al 9% y 25%.
		}	
			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$la_notas=$io_report->uf_select_arraydata($io_sql,"SELECT cxp_sol_dc.*,(SELECT porcar FROM cxp_dc_cargos WHERE cxp_sol_dc.codemp=cxp_dc_cargos.codemp AND cxp_sol_dc.numsol=cxp_dc_cargos.numsol AND cxp_sol_dc.numrecdoc=cxp_dc_cargos.numrecdoc AND cxp_sol_dc.codtipdoc=cxp_dc_cargos.codtipdoc AND cxp_sol_dc.cod_pro=cxp_dc_cargos.cod_pro AND cxp_sol_dc.ced_bene=cxp_dc_cargos.ced_bene AND cxp_sol_dc.codope=cxp_dc_cargos.codope AND cxp_sol_dc.numdc=cxp_dc_cargos.numdc) as porcar, (SELECT numref FROM cxp_rd WHERE cxp_sol_dc.codemp=cxp_rd.codemp AND  cxp_sol_dc.numrecdoc=cxp_rd.numrecdoc AND  cxp_sol_dc.codtipdoc=cxp_rd.codtipdoc AND  cxp_sol_dc.cod_pro=cxp_rd.cod_pro AND  cxp_sol_dc.ced_bene=cxp_rd.ced_bene) AS numref FROM cxp_sol_dc WHERE cxp_sol_dc.fecope>='".$as_fechadesde."' AND cxp_sol_dc.fecope<='".$as_fechahasta."' AND cxp_sol_dc.estlibcom='1'  ");
			$contador=count((array)$la_notas);
			if($contador!=0)
			{
				$li_total_data=count((array)$la_notas["numdc"]);
				for($lk=1;$lk<=$li_total_data;$lk++)
				{
					$ls_codope=$la_notas["codope"][$lk];
					$ls_numnota=$la_notas["numdc"][$lk];
					$ls_monnota=$la_notas["monto"][$lk];
					$ls_carnota=$la_notas["moncar"][$lk];
					$ldec_porcar=$la_notas["porcar"][$lk];
					$ls_codpro=$la_notas["cod_pro"][$lk];
					$ls_cedben=$la_notas["ced_bene"][$lk];
					$ls_numrecdoc=$la_notas["numrecdoc"][$lk];
					$ls_numref=$la_notas["numref"][$lk];
					if($ls_codpro=="----------")
						$ls_tipproben='B';
					else
						$ls_tipproben='P';
					$ls_basnota=$ls_monnota-$ls_carnota;
					if($ls_codope=='NC')
					{
						$ls_numnc=$ls_numnota;
						$ls_numnd="";
						$ls_monnota=$ls_monnota*(-1);
						$ls_carnota=$ls_carnota*(-1);
						$ls_basnota=$ls_basnota*(-1);
					}
					else
					{
						$ls_numnd=$ls_numnota;
						$ls_numnc="";
					}
					$li++;
				//	$ldec_montodoc     = $ldec_montodoc+$ls_monnota;			 
				//	$ldec_sinderiva    = $ldec_montodoc-$ldec_baseimp-$ldec_monimp;// Total de la Compra sin Derecho a Crédito Iva.
					$ldec_totcomconiva= $ldec_totcomconiva+$ls_monnota;
					$ldec_totimpiva   = $ldec_totimpiva+$ls_carnota;
					$ls_cheque="";
					if($ls_tipproben=='P')
					{
						$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_proveedor WHERE cod_pro='".$ls_codpro."'");
						$ls_rif=$la_provben["rifpro"];
						$ls_nombre=$la_provben["nompro"];
					}	
					else
					{
						$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_beneficiario WHERE ced_bene='".$ls_cedben."'");
						$ls_rif=$la_provben["rifben"];
						$ls_nombre=$la_provben["nombene"]." ".$la_provben["apebene"];
					}
						switch ($ldec_porcar)
						{
							case "8":
								$ldec_baseimp8=$ls_basnota;
								$ldec_baseimp12=0;
								$ldec_monimp8=$ls_carnota;
								$ldec_monimp12=0;
								$ldec_porcar8=$ldec_porcar;
								$ldec_porcar12=0;
								$totbaseimp8=$totbaseimp8+$ldec_baseimp8;
								$totimp8=$totimp8+$ldec_monimp8;
							break;
							case "16":
								$ldec_baseimp8=0;
								$ldec_baseimp12=$ls_basnota;
								$ldec_monimp8=0;
								$ldec_monimp12=$ls_carnota;
								$ldec_porcar8=0;
								$ldec_porcar12=$ldec_porcar;
								$totbaseimp12=$totbaseimp12+$ldec_baseimp12;
								$totimp12=$totimp12+$ldec_monimp12;
							break;
							default:
								$ldec_baseimp8=0;
								$ldec_baseimp12=0;
								$ldec_monimp8=0;
								$ldec_monimp12=0;
								$ldec_porcar8=0;
								$ldec_porcar12=0;
							break;
						}
						$li_row++;
					 $lo_hoja->write($li_row, 0, $li_row, $lo_datacenter);
					 $lo_hoja->write($li_row, 1, $io_report->io_funciones->uf_convertirfecmostrar($la_notas["fecope"]), $lo_datacenter);
					 $lo_hoja->write($li_row, 2, $ls_rif, $lo_datacenter);
					 $lo_hoja->write($li_row, 3, $ls_nombre, $lo_dataleft);
					 $lo_hoja->write($li_row, 4, $ls_cmpret, $lo_datacenter);
					 $lo_hoja->write($li_row, 5, $ls_tipproben, $lo_datacenter);
					 $lo_hoja->write($li_row, 6, '', $lo_datacenter);
					 $lo_hoja->write($li_row, 7, trim($ls_numrecdoc), $lo_datacenter);
					 $lo_hoja->write($li_row, 8, '', $lo_datacenter);
					 $lo_hoja->write($li_row, 9,$ls_numnd, $lo_datacenter);
					 $lo_hoja->write($li_row, 10,$ls_numnc, $lo_datacenter);
					 $lo_hoja->write($li_row, 11,$ls_tiptrans, $lo_datacenter);
					 $lo_hoja->write($li_row, 12,trim($row["numrecdoc"]), $lo_datacenter);
					 $lo_hoja->write($li_row, 13,$ls_monnota, $lo_dataright);
					 $lo_hoja->write($li_row, 14,0, $lo_dataright);
					 $lo_hoja->write($li_row, 15,$ldec_baseimp12, $lo_dataright);
					 $lo_hoja->write($li_row, 16,$ldec_porcar12, $lo_datacenter);
					 $lo_hoja->write($li_row, 17,$ldec_monimp12, $lo_dataright);
					 $lo_hoja->write($li_row, 18,$ldec_baseimp8, $lo_dataright);
					 $lo_hoja->write($li_row, 19,$ldec_porcar8, $lo_datacenter);
					 $lo_hoja->write($li_row, 20,$ldec_monimp8, $lo_dataright);
					 $lo_hoja->write($li_row, 21,'', $lo_dataright);
					 $lo_hoja->write($li_row, 22,'', $lo_dataright);
					 $li_porcentaje   = intval($ldec_porcentaje_conf);
					 if ($ldec_porcar>0)
					 {
						switch ($li_porcentaje){
						  case '2':
							$ldec_totimp8    = ($ldec_totimp8+$ls_carnota);
							$ldec_totbasimp8 = ($ldec_totbasimp8+$ls_basnota);
							//$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
							break;
						  case '1':
							$ldec_totimp9    = ($ldec_totimp9+$ls_carnota);  
							$ldec_totbasimp9 = ($ldec_totbasimp9+$ls_basnota);
							//$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
							break;
						  case '3':
							$ldec_totimp25    = ($ldec_totimp25+$ls_carnota);  
							$ldec_totbasimp25 = ($ldec_totbasimp28+$ls_basnota);
							//$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
							break;
						  }
					 }	
				}
			}
//			print "--->".$ldec_sinderiva."<br>";
		$li_row++;
		$lo_hoja->write($li_row, 0, "AJUSTES", $lo_datacenter);
 //		uf_print_totales(number_format($ldec_totcomsiniva,2,",","."),number_format($ldec_basimpga,2,",","."),number_format($ldec_totcomconiva,2,",","."),number_format($ldec_totimpiva,2,",","."),number_format($ldec_totivaret,2,",","."),$io_pdf);
		//uf_print_table_default(0,number_format($ldec_totbasimp9,2,",","."),number_format($ldec_totimp9,2,",","."),0,number_format($ldec_totcomsiniva,2,",","."),number_format($ldec_totimp8,2,",","."),number_format($ldec_totbasimp8,2,",","."),number_format($ldec_basimpga,2,",","."),number_format($ldec_totgenadi,2,",","."),number_format($ldec_totivaret,2,",","."),$io_pdf);
		$baseimpgrl=$ldec_totcomsiniva+$totbaseimp8+$totbaseimp12;
		$impgrl=$totimp8+$totimp12;
//		uf_print_subtotales(number_format($ldec_totcomsiniva,2,",","."),number_format($totbaseimp8,2,",","."),number_format($totbaseimp12,2,",","."),number_format($totimp8,2,",","."),number_format($totimp12,2,",","."),number_format($baseimpgrl,2,",","."),number_format($impgrl,2,",","."),$io_pdf);
	}
	$ldec_totcomconivaaju=0;
	$ldec_totsinderivaaju=0;
	$totbaseimpaju12=0;
	$totimpaju12=0;
	$totbaseimpaju8=0;
	$totimpaju8=0;
	$arrResultado=$io_report->uf_select_report_libcompra_ocamar($as_fechadesde,$as_fechahasta,"0");
	$lb_valido=$arrResultado["lb_valido"];
	$rs_resultado2=$arrResultado["rs_data"];
	unset($arrResultado);
	if($lb_valido)
	{
		$li=0;
		$li_aux=0;
		while($row=$io_report->io_sql->fetch_row($rs_resultado2))	
		{
			$li++;
			$ls_numnc="";
			$ls_numnd="";
		    $ldec_monret=0;
			$ldec_totbaseimp=0;
			$ldec_totmonimp=0;
			$ls_numrecdoc=$row["numrecdoc"];
			$ls_tipproben=$row["tipproben"];
			$ls_codpro=$row["cod_pro"];
			$ls_cedben=$row["ced_bene"];
			$ldec_montoret=$row["monret"];
			$ldec_montodoc=$row["montotdoc"];
			$ldec_mondeddoc=$row["mondeddoc"];
			$ls_codtipdoc =$row["codtipdoc"];
			$ls_porded =$row["porded"];
			$li_total_cargos=0;
			if($ls_tipproben=='P')
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_proveedor WHERE cod_pro='".$ls_codpro."'");
				$ls_rif=$la_provben["rifpro"];
				$ls_nombre=$la_provben["nompro"];
			}	
			else
			{
				$la_provben=$io_report->uf_select_rowdata($io_sql,"SELECT * FROM rpc_beneficiario WHERE ced_bene='".$ls_cedben."'");
				$ls_rif=$la_provben["rifben"];
				$ls_nombre=$la_provben["nombene"]." ".$la_provben["apebene"];
			}
			$ls_cheque=$io_report->uf_select_data($io_sql,"SELECT distinct cxp_sol_banco.numdoc AS numdoc".
														  "  FROM cxp_dt_solicitudes, cxp_sol_banco".
														  " WHERE cxp_dt_solicitudes.codemp='".$ls_codemp."'".
														  "   AND cxp_dt_solicitudes.numrecdoc='".$ls_numrecdoc."'".
														  "   AND cxp_dt_solicitudes.cod_pro='".$ls_codpro."'".
														  "   AND cxp_dt_solicitudes.ced_bene='".$ls_cedben."'".
														  "   AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
														  "   AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol","numdoc");
	if($ls_tiporeporte==1)
	{
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT DISTINCT max(a.numrecdoc) as numrecdoc,max(a.monobjretaux) as monobjret,SUM(a.monretaux) as monret,max(a.porded) as porded,max(b.codret) as codret,max(b.numcom) as numcom,max(b.iva_retaux) as iva_ret,max(tiptrans) as tiptrans".
															  "   FROM cxp_rd_deducciones a,scb_dt_cmp_ret b                                                                         ".
															  "  WHERE a.codemp=b.codemp AND a.numrecdoc=b.numfac AND a.codemp='".$ls_codemp."' AND a.numrecdoc='".$ls_numrecdoc."' AND ".
															  "        a.cod_pro='".$ls_codpro."' AND a.ced_bene='".$ls_cedben."'                                              ".
															  " GROUP BY a.numrecdoc ");
	}
	else
	{
			$la_cmpret=$io_report->uf_select_rowdata($io_sql,"SELECT cxp_rd_deducciones.numrecdoc as numrecdoc, cxp_rd_deducciones.monobjret as monobjret, SUM(cxp_rd_deducciones.monret) as monret, cxp_rd_deducciones.porded as porded, scb_dt_cmp_ret.codret as codret, ".
                                                             "       scb_dt_cmp_ret.numcom as numcom, scb_dt_cmp_ret.iva_ret as iva_ret, scb_dt_cmp_ret.tiptrans as tiptrans ".
  															 "FROM cxp_rd_deducciones, cxp_rd, cxp_dt_solicitudes, sigesp_deducciones, scb_cmp_ret, scb_dt_cmp_ret ".
															 "WHERE (cxp_rd_deducciones.codemp = '".$ls_codemp."' AND cxp_rd_deducciones.numrecdoc = '".$ls_numrecdoc."' AND cxp_rd_deducciones.codtipdoc = '".$ls_codtipdoc."' AND cxp_rd_deducciones.cod_pro = '".$ls_codpro."' AND cxp_rd_deducciones.ced_bene = '".$ls_cedben."') AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_rd.codemp AND cxp_rd_deducciones.numrecdoc = cxp_rd.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_rd.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_rd.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_rd.ced_bene) AND ".
															 "      (cxp_rd_deducciones.codemp = cxp_dt_solicitudes.codemp AND cxp_rd_deducciones.numrecdoc = cxp_dt_solicitudes.numrecdoc AND cxp_rd_deducciones.codtipdoc = cxp_dt_solicitudes.codtipdoc AND cxp_rd_deducciones.cod_pro = cxp_dt_solicitudes.cod_pro AND cxp_rd_deducciones.ced_bene = cxp_dt_solicitudes.ced_bene) AND ". 
															 "      (cxp_rd_deducciones.codded = sigesp_deducciones.codded AND sigesp_deducciones.iva = 1) AND ".
															 "      (cxp_rd_deducciones.codemp = scb_dt_cmp_ret.codemp AND cxp_rd_deducciones.numrecdoc = scb_dt_cmp_ret.numfac AND substring(scb_dt_cmp_ret.numsop,1,15) = cxp_dt_solicitudes.numsol) AND ". 
															 "       scb_dt_cmp_ret.numcom = scb_cmp_ret.numcom AND scb_cmp_ret.estcmpret = '1' AND scb_dt_cmp_ret.codret = '0000000001' AND".
															 "      (scb_cmp_ret.codsujret = (CASE cxp_rd_deducciones.ced_bene WHEN '----------' THEN cxp_rd_deducciones.cod_pro ELSE cxp_rd_deducciones.ced_bene END) AND cxp_rd_deducciones.codemp = scb_cmp_ret.codemp) AND ".
															 "       substring(scb_cmp_ret.perfiscal,1,4) = substring(CAST(cxp_rd.fecregdoc as text),1,4) AND substring(scb_cmp_ret.perfiscal,5,2) = substring(CAST(cxp_rd.fecregdoc as text),6,2) AND scb_cmp_ret.estcmpret = '1' AND ".
															 "       scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
															 "GROUP BY  cxp_rd_deducciones.numrecdoc , cxp_rd_deducciones.monobjret ,  cxp_rd_deducciones.porded , scb_dt_cmp_ret.codret , ".
															 "          scb_dt_cmp_ret.numcom, scb_dt_cmp_ret.iva_ret , scb_dt_cmp_ret.tiptrans ");
 	}
			if(count((array)$la_cmpret)>0)
			{
				$ls_codret = $la_cmpret["codret"];
				if ($ls_codret=='0000000001')
				{
					 $ldec_monret    = $la_cmpret["monret"];  
					 $ls_cmpret      = $la_cmpret["numcom"];
					 $ldec_monobjret = $la_cmpret["monobjret"];
					 $ldec_porded    = $la_cmpret["porded"];
					 $ldec_ivaret    = $la_cmpret["iva_ret"];
					 $ls_tiptrans    = $la_cmpret["tiptrans"];
				}
				else
				{
					 $ldec_monret    = 0; 
					 $ls_cmpret      = '';
					 $ldec_monobjret = 0;
					 $ldec_porded    = 0;
					 $ldec_ivaret    = 0;
 					 $ls_tiptrans    = "";
				}													  
			}
		    else
			{
				$ldec_monret    = $ldec_monret+$ldec_montoret;  
				$ls_cmpret      = '';
				$ldec_monobjret = 0;
				$ldec_porded    = 0;
				$ls_tiptrans    = "";
				$ldec_ivaret    = 0;
			}
			if($ls_tiporeporte==1)
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT cxp_rd_cargos.monobjretaux as basimp,cxp_rd_cargos.porcar,cxp_rd_cargos.monretaux as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'");
			}
			else
			{
				$la_cargos=$io_report->uf_select_arraydata($io_sql,"SELECT SUM(cxp_rd_cargos.monobjret) as basimp,MAX(cxp_rd_cargos.porcar) AS porcar,SUM(cxp_rd_cargos.monret) as impiva, tipo_iva".
																 "  FROM cxp_rd_cargos,sigesp_cargos ".
																 " WHERE cxp_rd_cargos.codcar=sigesp_cargos.codcar ".
																 "   AND cxp_rd_cargos.codemp='".$ls_codemp."'".
																 "   AND cxp_rd_cargos.numrecdoc='".$ls_numrecdoc."'".
																 "   AND cxp_rd_cargos.cod_pro='".$ls_codpro."'".
																 "   AND cxp_rd_cargos.ced_bene='".$ls_cedben."'".
																 " GROUP BY cxp_rd_cargos.codemp, cxp_rd_cargos.numrecdoc, cxp_rd_cargos.cod_pro, cxp_rd_cargos.ced_bene, cxp_rd_cargos.codtipdoc,tipo_iva ORDER BY porcar ASC");
			}
			if(!empty($la_cargos))
			{
				$li_total_cargos=count((array)$la_cargos["porcar"]);
				if($li_total_cargos>0)
				{
					$ldec_porcar=$la_cargos["porcar"][1];
					$ldec_baseimp=$la_cargos["basimp"][1];
					$ldec_monimp=$la_cargos["impiva"][1];
					$ldec_totbaseimp=$la_cargos["basimp"][1];
					$ldec_totmonimp=$la_cargos["impiva"][1];
					$ldec_porcentaje_conf=$la_cargos["tipo_iva"][1];
				}
				else
				{
					$ldec_porcar="";
					$ldec_baseimp=0;
					$ldec_monimp=0;	
					$ldec_porcentaje_conf=0;	
				}
			}
			else
			{
				$ldec_porcar="";
				$ldec_baseimp=0;
				$ldec_monimp=0;	
				$ldec_porcentaje_conf=0;			
			}	
			$li_habman=0;
			$li_debman=0;
			if($_SESSION["la_empresa"]["confiva"]=='P')
			{
				$la_manualesd=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='D'".
																 "   AND estasicon='M'");
				
				$la_manualesh=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='H'".
																 "   AND estasicon='M'");
			
				if(count((array)$la_manualesd)>0)
				{
					$li_debman=$la_manualesd["monto"];
				}	
				if(count((array)$la_manualesh)>0)
				{
					$li_habman=$la_manualesh["monto"];
				}	
			}
			else
			{
				$la_manualesh=$io_report->uf_select_rowdata($io_sql,"SELECT SUM(monto) AS monto".
																 "  FROM cxp_rd_scg ".
																 " WHERE codemp='".$ls_codemp."'".
																 "   AND numrecdoc='".$ls_numrecdoc."'".
																 "   AND cod_pro='".$ls_codpro."'".
																 "   AND ced_bene='".$ls_cedben."'".
																 "   AND debhab='H'".
																 "   AND estasicon='M'");
				if(count((array)$la_manualesh)>0)
				{
					$li_habman=$la_manualesh["monto"];
				}	
			}
			$ldec_montodoc     = $ldec_montodoc+$ldec_mondeddoc+$li_habman-$li_debman;			 
			$ldec_sinderiva    = $ldec_montodoc-$ldec_baseimp-$ldec_monimp;// Total de la Compra sin Derecho a Crédito Iva.
			$ldec_sinderiva    =number_format($ldec_sinderiva,4,".","");
			if(($ldec_sinderiva<0)&&($ldec_sinderiva>-1))
			{
				$ldec_sinderiva=0;
			}
			$ldec_totimpiva   = $ldec_totimpiva+$ldec_monimp;
			$ldec_totivaret   = $ldec_totivaret+$ldec_montoret;
			$ldec_totcomconiva= $ldec_totcomconiva+$ldec_montodoc;
			$ldec_totcomconivaaju=$ldec_totcomconiva+$ldec_montodoc;
			$ldec_totsinderivaaju=$ldec_totsinderivaaju+$ldec_sinderiva;
			switch ($ldec_porcar)
			{
				case "8":
					$ldec_baseimp8=$ldec_baseimp;
					$ldec_baseimp12=0;
					$ldec_monimp8=$ldec_monimp;
					$ldec_monimp12=0;
					$ldec_porcar8=$ldec_porcar;
					$ldec_porcar12=0;
					$totbaseimp8=$totbaseimp8+$ldec_baseimp8;
					$totimp8=$totimp8+$ldec_monimp8;
					$totbaseimpaju8=$totbaseimpaju8+$ldec_baseimp8;
					$totimpaju8=$totimpaju8+$ldec_monimp8;
				break;
				case "16":
					$ldec_baseimp8=0;
					$ldec_baseimp12=$ldec_baseimp;
					$ldec_monimp8=0;
					$ldec_monimp12=$ldec_monimp;
					$ldec_porcar8=0;
					$ldec_porcar12=$ldec_porcar;
					$totbaseimp12=$totbaseimp12+$ldec_baseimp12;
					$totimp12=$totimp12+$ldec_monimp12;
					$totbaseimpaju12=$totbaseimpaju12+$ldec_baseimp12;
					$totimpaju12=$totimpaju12+$ldec_monimp12;
				break;
				default:
					$ldec_baseimp8=0;
					$ldec_baseimp12=0;
					$ldec_monimp8=0;
					$ldec_monimp12=0;
					$ldec_porcar8=0;
					$ldec_porcar12=0;
				break;
			}
			 $li_row++;
			 $lo_hoja->write($li_row, 0, $li_row, $lo_datacenter);
			 $lo_hoja->write($li_row, 1, $io_report->io_funciones->uf_convertirfecmostrar($row["fecemidoc"]), $lo_datacenter);
			 $lo_hoja->write($li_row, 2, $ls_rif, $lo_datacenter);
			 $lo_hoja->write($li_row, 3, $ls_nombre, $lo_dataleft);
			 $lo_hoja->write($li_row, 4, $ls_cmpret, $lo_datacenter);
			 $lo_hoja->write($li_row, 5, $ls_tipproben, $lo_datacenter);
			 $lo_hoja->write($li_row, 6, '', $lo_datacenter);
			 $lo_hoja->write($li_row, 7, trim($ls_numrecdoc), $lo_datacenter);
			 $lo_hoja->write($li_row, 8, $row["numref"], $lo_datacenter);
			 $lo_hoja->write($li_row, 9,$ls_numnd, $lo_datacenter);
			 $lo_hoja->write($li_row, 10,$ls_numnc, $lo_datacenter);
			 $lo_hoja->write($li_row, 11,$ls_tiptrans, $lo_datacenter);
			 $lo_hoja->write($li_row, 12,trim($row["numrecdoc"]), $lo_datacenter);
			 $lo_hoja->write($li_row, 13,$ldec_montodoc, $lo_dataright);
			 $lo_hoja->write($li_row, 14,$ldec_sinderiva, $lo_dataright);
			 $lo_hoja->write($li_row, 15,$ldec_baseimp12, $lo_dataright);
			 $lo_hoja->write($li_row, 16,$ldec_porcar12, $lo_datacenter);
			 $lo_hoja->write($li_row, 17,$ldec_monimp12, $lo_dataright);
			 $lo_hoja->write($li_row, 18,$ldec_baseimp8, $lo_dataright);
			 $lo_hoja->write($li_row, 19,$ldec_porcar8, $lo_datacenter);
			 $lo_hoja->write($li_row, 20,$ldec_monimp8, $lo_dataright);
			 $lo_hoja->write($li_row, 21,$ldec_montoret, $lo_dataright);
			 $lo_hoja->write($li_row, 22,$ls_porded, $lo_dataright);
						 	
			 $li_porcentaje   = intval($ldec_porcentaje_conf);
			 if ($ldec_porcar>0)
			 {
				switch ($li_porcentaje){
				  case '2':
					$ldec_totimp8    = ($ldec_totimp8+$ldec_monimp);
					$ldec_totbasimp8 = ($ldec_totbasimp8+$ldec_baseimp);
					$ldec_totimpret8    = ($ldec_totimpret8+$ldec_montoret);
					break;
				  case '1':
					$ldec_totimp9    = ($ldec_totimp9+$ldec_monimp);  
					$ldec_totbasimp9 = ($ldec_totbasimp9+$ldec_baseimp);
					$ldec_totimpret9    = ($ldec_totimpret9+$ldec_montoret);
					break;
				  case '3':
					$ldec_totimp25    = ($ldec_totimp25+$ldec_monimp);  
					$ldec_totbasimp25 = ($ldec_totbasimp28+$ldec_baseimp);
					$ldec_totimpret25    = ($ldec_totimpret25+$ldec_montoret);
					break;
				  }
			 }
			 
		 $ldec_totcomsiniva = ($ldec_totcomsiniva+$ldec_sinderiva);	
		 $ldec_basimpga     = ($ldec_totbasimp9+$ldec_totbasimp25);//Total Base Imponible Compras Internas Afectadas en Alicuota General + Adicional(9% y 25%).
		 $ldec_totgenadi    = ($ldec_totimp9+$ldec_totimp25);//Total Compras Internas Afectadas en Alicuota Reducida. Impuestos al 9% y 25%.
		}	
		$impgrl=$totimp8+$totimp12;

		$li_row++;
		$lo_hoja->write($li_row, 3, 'TOTAL AJUSTE', $lo_dataleft);
		$lo_hoja->write($li_row, 13,$ldec_totcomconivaaju, $lo_dataright);
		$lo_hoja->write($li_row, 14,$ldec_totsinderivaaju, $lo_dataright);
		$lo_hoja->write($li_row, 15,$totbaseimpaju12, $lo_dataright);
		$lo_hoja->write($li_row, 17,$totimpaju12, $lo_dataright);
		$lo_hoja->write($li_row, 18,$ldec_baseimp8, $lo_dataright);
		$lo_hoja->write($li_row, 20,$ldec_monimp8, $lo_dataright);
	}
		$li_row++;
		$lo_hoja->write($li_row, 3, 'RESUMEN DEL MES', $lo_dataleft);
		$lo_hoja->write($li_row, 4, 'BASE IMPONIBLE', $lo_dataleft);
		$lo_hoja->write($li_row, 5, 'MONTO DEL IMPUESTO', $lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row, 3, 'Total de compras exentas', $lo_dataleft);
		$lo_hoja->write($li_row, 4, $ldec_totcomsiniva, $lo_dataright);
		$lo_hoja->write($li_row, 5, 0, $lo_dataright);
		$li_row++;
		$lo_hoja->write($li_row, 3, 'Total compras 16%', $lo_dataleft);
		$lo_hoja->write($li_row, 4, $totbaseimp12, $lo_dataright);
		$lo_hoja->write($li_row, 5, $totimp12, $lo_dataright);
		$li_row++;
		$lo_hoja->write($li_row, 3, 'Total compras 8%', $lo_dataleft);
		$lo_hoja->write($li_row, 4, $totbaseimp8, $lo_dataright);
		$lo_hoja->write($li_row, 5, $totimp8, $lo_dataright);
		$li_row++;
		$lo_hoja->write($li_row, 3, 'TOTAL GENERAL', $lo_dataleft);
		$lo_hoja->write($li_row, 4, $baseimpgrl, $lo_dataright);
		$lo_hoja->write($li_row, 5, $impgrl, $lo_dataright);

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"libro_compra.xls\"");
		header("Content-Disposition: inline; filename=\"libro_compra.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);		
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	if($lb_valido==false)
	{
		print("<script language=JavaScript>");
		print("alert('No hay Registros para Mostrar');"); 
		print("close();");
		print("</script>");	
	}
	
	
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?>
