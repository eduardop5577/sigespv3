<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "BM3.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");
	$ld_fecha="";
	$ls_titulo="RELACION DE BIENES MUEBLES FALTANTES";
	if(($ld_desde!="")&&($ld_hasta!=""))
	{
		$ld_fecha="Desde:".$ld_desde."  Hasta:".$ld_hasta."";
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_estemp=$arre["estemp"];
	$ls_nomemp=$arre["nombre"];
	$ls_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_cmpmov_desde=$io_fun_activos->uf_obtenervalor_get("cmpmov_desde","");
	$ls_cmpmov_hasta=$io_fun_activos->uf_obtenervalor_get("cmpmov_hasta","");
	$ls_coduniadm=$io_fun_activos->uf_obtenervalor_get("coduniadm","");
	$ls_denuniadm=$io_fun_activos->uf_obtenervalor_get("denuniadm","");
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("codgru","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("codsubgru","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("codsec","");
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	$li_orden=$io_fun_activos->uf_obtenervalor_get("ordenact",0);
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	require_once("sigesp_saf_class_report.php");
	$io_report=new sigesp_saf_class_report();
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_saf_load_relbiemuefal($ls_codemp,$ls_coduniadm,$ls_cmpmov_desde,$ls_cmpmov_hasta,$ld_desde,$ld_hasta,
													$li_orden,$ls_codgru,$ls_codsubgru,$ls_codsec,$ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print("alert('No hay nada que Reportar');"); 
		print("close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Relacion de Bienes Muebles Faltantes. Desde el activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_activo.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
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
		$lo_hoja->set_column(0,0,25);
		$lo_hoja->set_column(1,2,25);	
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,100);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,25);	
		$lo_hoja->set_column(7,7,20);	
		$lo_hoja->set_column(8,8,15);	
		$lo_hoja->set_column(8,8,20);	
		$lo_hoja->set_column(9,9,60);	
		$lo_hoja->set_column(10,10,35);	
		$lo_hoja->set_column(11,11,35);	
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,3,$ld_fecha,$lo_encabezado);
//		uf_print_encabezado_pagina($ls_titulo,"",$ld_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$li_totrow=$io_report->ds->getRowCount("cmpmov");
		$i=0;
		$li_row=2;					
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$ls_cmpmov=$io_report->ds->data["cmpmov"][$li_i];
			$ls_coduniadm=$io_report->ds->data["coduniadm"][$li_i];
			$ld_feccmp=$io_report->ds->data["feccmp"][$li_i];
			$ld_feccmp=$io_funciones->uf_convertirfecmostrar($ld_feccmp);
			$li_row++;
			$lo_hoja->write($li_row,0,"ESTADO:",$lo_titulo);
			$lo_hoja->write($li_row,5,"IDENTIFICACION DEL COMPROBANTE",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_estemp,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"MUNICIPIO:",$lo_titulo);
			$lo_hoja->write($li_row,1,"",$lo_dataleft);
			$lo_hoja->write($li_row,5,"CODIGO CONCEPTO DE MOVIMENTO:",$lo_titulo);
			$lo_hoja->write($li_row,6,"060",$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"UNIDAD DE TRABAJO:",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_denuniadm,$lo_dataleft);
			$lo_hoja->write($li_row,5,"NUMERO DE COMPROBANTE:",$lo_titulo);
			$lo_hoja->write($li_row,6,$ls_cmpmov,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"UBICACION ADMINISTRATIVA",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_denuniadm,$lo_dataleft);
			$lo_hoja->write($li_row,5,"FECHA DE LA OPERACION:",$lo_titulo);
			$lo_hoja->write($li_row,6,$ld_feccmp,$lo_dataleft);
			$lb_valido=$io_report->uf_saf_load_dt_relbiemuefal($ls_codemp,$ls_coduniadm,$ls_cmpmov,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$li_orden); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->ds_detalle->getRowCount("ideact");
				$la_data="";
				$li_row=$li_row+2;
				$lo_hoja->write($li_row,0,"Grupo",$lo_titulo);
				$lo_hoja->write($li_row,1,"Sub-Grupo",$lo_titulo);
				$lo_hoja->write($li_row,2,"Seccion",$lo_titulo);
				$lo_hoja->write($li_row,3,"Nro. de Identificacion",$lo_titulo);
				$lo_hoja->write($li_row,4,"Descripcion",$lo_titulo);
				$lo_hoja->write($li_row,5,"Marca",$lo_titulo);
				$lo_hoja->write($li_row,6,"Modelo",$lo_titulo);
				$lo_hoja->write($li_row,7,"Existencias Fisicas",$lo_titulo);
				$lo_hoja->write($li_row,8,"Registros Contables",$lo_titulo);
				$lo_hoja->write($li_row,9,"Valor Unitario",$lo_titulo);
				$lo_hoja->write($li_row,10,"Diferencia",$lo_titulo);
				$lo_hoja->write($li_row,11,"Valor Total",$lo_titulo);
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_grupo= $io_report->ds_detalle->data["grupo"][$li_s];
					$ls_subgrupo= $io_report->ds_detalle->data["subgrupo"][$li_s];
					$ls_seccion= $io_report->ds_detalle->data["seccion"][$li_s];
					$ls_ideact= $io_report->ds_detalle->data["ideact"][$li_s];
					$ls_denact= $io_report->ds_detalle->data["denact"][$li_s];
					$ls_marca= $io_report->ds_detalle->data["marca"][$li_s];
					$ls_modelo= $io_report->ds_detalle->data["modelo"][$li_s];
					$ls_cantidad= $io_report->ds_detalle->data["cantidad"][$li_s];
					$ls_costo= $io_report->ds_detalle->data["costo"][$li_s];
					$ls_costo = $io_fun_activos->uf_formatonumerico($ls_costo);
					$li_row++;
					$lo_hoja->write($li_row,0," ".$ls_grupo,$lo_dataleft);
					$lo_hoja->write($li_row,1," ".$ls_subgrupo,$lo_dataleft);
					$lo_hoja->write($li_row,2," ".$ls_seccion,$lo_dataleft);
					$lo_hoja->write($li_row,3," ".$ls_ideact,$lo_dataleft);
					$lo_hoja->write($li_row,4,$ls_denact,$lo_dataleft);
					$lo_hoja->write($li_row,5,$ls_marca,$lo_dataleft);
					$lo_hoja->write($li_row,6,$ls_modelo,$lo_dataleft);
					$lo_hoja->write($li_row,7," ".$ls_cantidad,$lo_dataleft);
					$lo_hoja->write($li_row,8,"",$lo_dataleft);
					$lo_hoja->write($li_row,9,$ls_costo,$lo_dataright2);
					$lo_hoja->write($li_row,10,"",$lo_dataleft);
					$lo_hoja->write($li_row,11,"",$lo_dataleft);
					$la_data[$li_s]=array('codgru'=>$ls_grupo,'codsubgru'=>$ls_subgrupo,'codsec'=>$ls_seccion,'ideact'=>$ls_ideact,
										  'denact'=>$ls_denact,'maract'=>$ls_marca,'modact'=>$ls_modelo,
										  'cantidad'=>$ls_cantidad,'regcont'=>'','costo'=>$ls_costo,'cantdif'=>'','costot'=>'');
				}
			}
			unset($la_data);			
		}
		if(($lb_valido))
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"BM2.xls\"");
			header("Content-Disposition: inline; filename=\"BM2.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);		
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($io_pdf);
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}		
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 