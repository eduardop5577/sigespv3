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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_personal_rac_rec.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal_rac.xls");
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
	$ls_titulo="Listado de Personal";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_anio=$io_fun_nomina->uf_obtenervalor_get("anio","");	
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");	
	$ls_peri=$io_fun_nomina->uf_obtenervalor_get("codperi","");	
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");	
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Personal RAC en Excel </b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadopersonal_personal_rac_rec($ls_codnomdes,$ls_codnomhas,$ls_codperdes,$ls_codperhas,
														   		   $ls_anio,$ls_mes,$ls_peri,$ls_orden); // Obtenemos el detalle del reporte
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
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
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$li_row=1;
		$lo_hoja->write($li_row, 0, "Cód Empleado ", $lo_titulo);
		$lo_hoja->write($li_row, 1, "Cód Ubi. Adm ", $lo_titulo);
		$lo_hoja->write($li_row, 2, "Unidad Adscripción ", $lo_titulo);
		$lo_hoja->write($li_row, 3, "Ubicación Geográfica ", $lo_titulo);
		$lo_hoja->write($li_row, 4, "Cédula ", $lo_titulo);
		$lo_hoja->write($li_row, 5, "Nombre y Apellido ", $lo_titulo);
		$lo_hoja->write($li_row, 6, "Cargo por Conversión ", $lo_titulo);
		$lo_hoja->write($li_row, 7, "Manual de Competencia  Genérica", $lo_titulo);
		$lo_hoja->write($li_row, 8, "Grado ", $lo_titulo);
		$lo_hoja->write($li_row, 9, "Clase ", $lo_titulo);
		$lo_hoja->write($li_row, 10, "Sueldo ", $lo_titulo);
		$lo_hoja->write($li_row, 11, "Compensación ", $lo_titulo);
		$lo_hoja->write($li_row, 12, "Totales ", $lo_titulo);
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,60);
		$lo_hoja->set_column(3,3,50);
		$lo_hoja->set_column(4,4,10);
		$lo_hoja->set_column(5,5,50);
		$lo_hoja->set_column(6,6,50);
		$lo_hoja->set_column(7,7,50);
		$lo_hoja->set_column(8,8,10);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,20);
		$lo_hoja->set_column(11,11,20);
		$lo_hoja->set_column(12,12,20);
		
		$li_totrow=$io_report->DS->getRowCount("codcarnomina");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codper=$io_report->DS->data["codper"][$li_i];
			$ls_codnom=$io_report->DS->data["codnom"][$li_i];
			$ls_codcarnomina=$io_report->DS->data["codcarnomina"][$li_i];
			$ls_minorguniadm=$io_report->DS->data["minorguniadm"][$li_i];
			$ls_ofiuniadm=$io_report->DS->data["ofiuniadm"][$li_i];
			$ls_uniuniadm=$io_report->DS->data["uniuniadm"][$li_i];
			$ls_depuniadm=$io_report->DS->data["depuniadm"][$li_i];
			$ls_prouniadm=$io_report->DS->data["prouniadm"][$li_i];
			$ls_codubiadm=$ls_minorguniadm.$ls_ofiuniadm.$ls_uniuniadm.$ls_depuniadm.$ls_prouniadm;
			$ls_desuniadm=$io_report->DS->data["desuniadm"][$li_i];
			$ls_ubicacionfisica=$io_report->DS->data["codubifis"][$li_i]." ".$io_report->DS->data["desubifis"][$li_i];
			$ls_denasicar=$io_report->DS->data["denasicar"][$li_i];
			$ls_descasicar=$io_report->DS->data["descasicar"][$li_i];
			$ls_claasicar=$io_report->DS->data["claasicar"][$li_i];
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_cedper=str_pad(trim($ls_cedper),8,"0",0);
			$ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			$ls_codgra=$io_report->DS->data["codgra"][$li_i];
			$li_sueper=$io_report->DS->data["sueper"][$li_i];
			$li_total=$li_sueper;
			$li_sueper=number_format(trim($li_sueper),2,".","");
			$li_compensacion=$io_report->DS->data["compensacion"][$li_i];
			$li_total=$li_total+$li_compensacion;
			$li_compensacion=number_format(trim($li_compensacion),2,".","");
			$li_total=number_format(trim($li_total),2,".","");

			$li_row=$li_row+1;

			$lo_hoja->write($li_row, 0, " ".$ls_codcarnomina, $lo_datacenter);
			$lo_hoja->write($li_row, 1, " ".$ls_codubiadm, $lo_datacenter);
			$lo_hoja->write($li_row, 2, " ".$ls_desuniadm, $lo_dataleft);
			$lo_hoja->write($li_row, 3, " ".$ls_ubicacionfisica, $lo_dataleft);
			$lo_hoja->write($li_row, 4, " ".$ls_cedper, $lo_datacenter);
			$lo_hoja->write($li_row, 5, " ".$ls_nomper, $lo_dataleft);
			$lo_hoja->write($li_row, 6, " ".$ls_denasicar, $lo_dataleft);
			$lo_hoja->write($li_row, 7, " ".$ls_descasicar, $lo_dataleft);
			$lo_hoja->write($li_row, 8, " ".$ls_codgra, $lo_datacenter);
			$lo_hoja->write($li_row, 9, " ".$ls_claasicar, $lo_datacenter);
			$lo_hoja->write($li_row, 10, $li_sueper, $lo_dataright);
			$lo_hoja->write($li_row, 11, $li_compensacion, $lo_dataright);
			$lo_hoja->write($li_row, 12, $li_total, $lo_dataright);
		}
		$io_report->DS->resetds("codcarnomina");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_personal_rac.xls\"");
		header("Content-Disposition: inline; filename=\"listado_personal_rac.xls\"");
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