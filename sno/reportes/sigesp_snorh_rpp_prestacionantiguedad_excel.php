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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_prestacionantiguedad.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal.xls");
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
	$ls_titulo="Listado de Prestación Antiguedad Excel";
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_desnomdes=$io_fun_nomina->uf_obtenervalor_get("desnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_desnomhas=$io_fun_nomina->uf_obtenervalor_get("desnomhas","");
	$ls_anocurperdes=$io_fun_nomina->uf_obtenervalor_get("anocurperdes","");
	$ls_mescurperdes=$io_fun_nomina->uf_obtenervalor_get("mescurperdes","");
	$ls_desmesperdes=$io_fun_nomina->uf_obtenervalor_get("desmesperdes","");
	$ls_anocurperhas=$io_fun_nomina->uf_obtenervalor_get("anocurperhas","");
	$ls_mescurperhas=$io_fun_nomina->uf_obtenervalor_get("mescurperhas","");
	$ls_desmesperhas=$io_fun_nomina->uf_obtenervalor_get("desmesperhas","");
	$ls_sueint=$io_fun_nomina->uf_obtenervalor_get("sueint","");
	$ls_tiporeporte=0;
	if ($ls_codnomdes==$ls_codnomhas)
	{
		$ls_desnom=$ls_desnomdes;
	}
	else
	{
		$ls_desnom=$ls_desnomdes." - ".$ls_desnomhas;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_prestacionantiguedad_personal($ls_codnomdes,$ls_codnomhas,$ls_anocurperdes,$ls_mescurperdes,$ls_anocurperhas,$ls_mescurperhas); // Obtenemos el detalle del reporte
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
		if(trim($ls_desmesperdes)==trim($ls_desmesperhas))
		{
			$ls_mes=" Mes: ".$ls_desmesperdes;
		}
		else
		{
			$ls_mes=" Meses: ".$ls_desmesperdes." - ".$ls_desmesperhas;
		}
		$ls_rango="Año: ".$ls_anocurperdes." ".$ls_mes;
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
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,50);
		$lo_hoja->set_column(3,3,15);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->write(0,2,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,2,$ls_rango,$lo_encabezado);			
		$lo_hoja->write(2,2,$ls_desnom,$lo_encabezado);			
		$lo_hoja->write(3, 0, "Periodo",$lo_titulo);
		$lo_hoja->write(3, 1, "Cédula",$lo_titulo);
		$lo_hoja->write(3, 2, "Apellidos y Nombres",$lo_titulo);
		$lo_hoja->write(3, 3, "Sueldo Integral",$lo_titulo);
		$lo_hoja->write(3, 4, "Alicuota Bono Vacacional",$lo_titulo);
		$lo_hoja->write(3, 5, "Alicuota Bono Fin Año",$lo_titulo);
		$lo_hoja->write(3, 6, "Monto a Depositar",$lo_titulo);

		$li_row=4;
		$li_totrow=$io_report->DS->getRowCount("cedper");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$li_numper=str_pad($li_i,2,"0",0);
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			$ls_periodo=str_pad($io_report->DS->data["mescurper"][$li_i],2,"0",0)." - ".$io_report->DS->data["anocurper"][$li_i];
			$li_sueintper=$io_fun_nomina->uf_formatonumerico($io_report->DS->data["sueintper"][$li_i]);
			$li_bonvacper=$io_fun_nomina->uf_formatonumerico($io_report->DS->data["bonvacper"][$li_i]);
			$li_bonfinper=$io_fun_nomina->uf_formatonumerico($io_report->DS->data["bonfinper"][$li_i]);
			$li_apoper=$io_fun_nomina->uf_formatonumerico($io_report->DS->data["apoper"][$li_i]);
			$li_totalsueintper=$li_totalsueintper+$io_report->DS->data["sueintper"][$li_i];
			$li_totalbonvacper=$li_totalbonvacper+$io_report->DS->data["bonvacper"][$li_i];
			$li_totalbonfinper=$li_totalbonfinper+$io_report->DS->data["bonfinper"][$li_i];
			$li_totalapoper=$li_totalapoper+$io_report->DS->data["apoper"][$li_i];
			$lo_hoja->write($li_row, 0, $ls_periodo,$lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_cedper,$lo_datacenter);
			$lo_hoja->write($li_row, 2, $ls_nomper,$lo_dataleft);
			$lo_hoja->write_string($li_row, 3, $li_sueintper,$lo_dataright);
			$lo_hoja->write_string($li_row, 4, $li_bonvacper,$lo_dataright);
			$lo_hoja->write_string($li_row, 5, $li_bonfinper,$lo_dataright);
			$lo_hoja->write_string($li_row, 6, $li_apoper,$lo_dataright);
			$li_row++;
		}
		$li_totalsueintper=$io_fun_nomina->uf_formatonumerico($li_totalsueintper);
		$li_totalbonvacper=$io_fun_nomina->uf_formatonumerico($li_totalbonvacper);
		$li_totalbonfinper=$io_fun_nomina->uf_formatonumerico($li_totalbonfinper);
		$li_totalapoper=$io_fun_nomina->uf_formatonumerico($li_totalapoper);
		$lo_hoja->write($li_row, 2, "TOTAL",$lo_titulo);
		$lo_hoja->write_string($li_row, 3, $li_totalsueintper,$lo_dataright);
		$lo_hoja->write_string($li_row, 4, $li_totalbonvacper,$lo_dataright);
		$lo_hoja->write_string($li_row, 5, $li_totalbonfinper,$lo_dataright);
		$lo_hoja->write_string($li_row, 6, $li_totalapoper,$lo_dataright);
		$io_report->DS->resetds("cedper");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"prestacion_antiguedad.xls\"");
		header("Content-Disposition: inline; filename=\"prestacion_antiguedad.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 