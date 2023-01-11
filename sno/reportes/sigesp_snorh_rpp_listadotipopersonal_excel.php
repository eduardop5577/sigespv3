<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 30/08/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo." en Excel";
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadotipopersonal.php",$ls_descripcion);
		if($lb_valido==false)
		{
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listadotipopersonal.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();

	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Reporte Tipo de Personal por Ubicación Física";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_tipo_personal('',''); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
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
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,30);
		$lo_hoja->set_column(1,6,20);
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(4, 0, "DEPENDENCIA",$lo_titulo);
		$lo_hoja->write(4, 1, "EMPLEADOS FIJOS",$lo_titulo);
		$lo_hoja->write(4, 2, "OBREROS FIJOS",$lo_titulo);
		$lo_hoja->write(4, 3, "EMPLEADOS CONTRATADOS",$lo_titulo);
		$lo_hoja->write(4, 4, "OBREROS CONTRATADOS",$lo_titulo);
		$lo_hoja->write(4, 5, "TOTAL",$lo_titulo);
		$li_row=4;
		$ld_totalempleadosfijos=0;
		$ld_totalobrerosfijos=0;
		$ld_totalempleadoscontratados=0;
		$ld_totalobreroscontratados=0;
		$ld_totalgeneral=0;
		
		$li_totrow=$io_report->DS_detalle->getRowCount("codubifis");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_desubifis=$io_report->DS_detalle->data["desubifis"][$li_i];
			$ld_empleadosfijos=number_format($io_report->DS_detalle->data["empleadosfijos"][$li_i],2,",",".");
			$ld_obrerosfijos=number_format($io_report->DS_detalle->data["obrerosfijos"][$li_i],2,",",".");
			$ld_empleadoscontratados=number_format($io_report->DS_detalle->data["empleadoscontratados"][$li_i],2,",",".");
			$ld_obreroscontratados=number_format($io_report->DS_detalle->data["obreroscontratados"][$li_i],2,",",".");
			$ld_total=number_format($io_report->DS_detalle->data["obreroscontratados"][$li_i]+$io_report->DS_detalle->data["empleadosfijos"][$li_i]+$io_report->DS_detalle->data["obrerosfijos"][$li_i]+$io_report->DS_detalle->data["empleadoscontratados"][$li_i],2,",",".");
			$ld_totalempleadosfijos=$ld_totalempleadosfijos+$io_report->DS_detalle->data["empleadosfijos"][$li_i];
			$ld_totalobrerosfijos=$ld_totalobrerosfijos+$io_report->DS_detalle->data["obrerosfijos"][$li_i];
			$ld_totalempleadoscontratados=$ld_totalempleadoscontratados+$io_report->DS_detalle->data["empleadoscontratados"][$li_i];
			$ld_totalobreroscontratados=$ld_totalobreroscontratados+$io_report->DS_detalle->data["obreroscontratados"][$li_i];
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, $ls_desubifis, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ld_empleadosfijos, $lo_dataright);
			$lo_hoja->write($li_row, 2, $ld_obrerosfijos, $lo_dataright);
			$lo_hoja->write($li_row, 3, $ld_empleadoscontratados, $lo_dataright);
			$lo_hoja->write($li_row, 4, $ld_obreroscontratados, $lo_dataright);
			$lo_hoja->write($li_row, 5, $ld_total, $lo_dataright);
		}
		$ld_totalgeneral=number_format($ld_totalempleadosfijos+$ld_totalempleadosfijos+$ld_totalempleadoscontratados+$ld_totalobreroscontratados,2,",",".");
		$ld_totalempleadosfijos=number_format($ld_totalempleadosfijos,2,",",".");
		$ld_totalobrerosfijos=number_format($ld_totalobrerosfijos,2,",",".");
		$ld_totalempleadoscontratados=number_format($ld_totalempleadoscontratados,2,",",".");
		$ld_totalobreroscontratados=number_format($ld_totalobreroscontratados,2,",",".");
		$li_row=$li_row+1;
		$lo_hoja->write($li_row, 0, "TOTAL GENERAL", $lo_titulo);
		$lo_hoja->write($li_row, 1, $ld_totalempleadosfijos, $lo_dataright);
		$lo_hoja->write($li_row, 2, $ld_totalobrerosfijos, $lo_dataright);
		$lo_hoja->write($li_row, 3, $ld_totalempleadoscontratados, $lo_dataright);
		$lo_hoja->write($li_row, 4, $ld_totalobreroscontratados, $lo_dataright);
		$lo_hoja->write($li_row, 5, $ld_totalgeneral, $lo_dataright);
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listadotipopersonal.xls\"");
		header("Content-Disposition: inline; filename=\"listadotipopersonal.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 