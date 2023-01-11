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
	function uf_insert_seguridad($as_titulo,$as_nomina,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_nomina // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo.". ".$as_nomina.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_conceptos.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

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
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codconcdes=$io_fun_nomina->uf_obtenervalor_get("codconcdes","");
	$ls_codconchas=$io_fun_nomina->uf_obtenervalor_get("codconchas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_tipconc=$io_fun_nomina->uf_obtenervalor_get("tipconc","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","1");
	$ls_personaldes=$io_fun_nomina->uf_obtenervalor_get("personaldes","");
	$ls_personalhas=$io_fun_nomina->uf_obtenervalor_get("personalhas","");
	$ls_tiporeporte=$io_fun_nomina->uf_obtenervalor_get("tiporeporte",0);
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("codsubnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("codsubnomhas","");
	$ls_anocurper=$io_fun_nomina->uf_obtenervalor_get("year","");
	
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$ls_titulo="CONSOLIDADO DE CONCEPTOS";
	$ls_nomina="Nómina:  Desde ".$ls_codnomdes."  Hasta ".$ls_codnomhas."";
	$ls_periodo="Período:  Desde ".$ls_codperdes." Hasta ".$ls_codperhas."";
	$lb_valido=uf_insert_seguridad($ls_titulo." EXCEL",$ls_nomina,$ls_periodo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_consolidadoconcepto_conceptos($ls_codnomdes,$ls_codnomhas,$ls_codconcdes,$ls_codconchas,
																$ls_codperdes,$ls_codperhas,$ls_tipconc,$ls_conceptocero,
																$ls_personaldes,$ls_personalhas,$ls_subnomdes,$ls_subnomhas); // Cargar el DS con los datos de la cabecera del reporte
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
		//-------formato para el reporte----------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('left');
		$lo_titulo->set_size('10');		
		$lo_titulo->set_merge();

		$lo_titulo2= &$lo_libro->addformat();
		$lo_titulo2->set_bold();
		$lo_titulo2->set_font("Verdana");
		$lo_titulo2->set_align('center');
		$lo_titulo2->set_size('9');		
		
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
		
		$lo_hoja->set_column(0,0,5);
		$lo_hoja->set_column(1,1,12);
		$lo_hoja->set_column(2,2,55);
		$lo_hoja->set_column(3,3,15);
		//---------------------------------------------------------------------------------------------
		
		$lo_hoja->write(0,2,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,2,$ls_nomina,$lo_encabezado);
		$lo_hoja->write(2,2,$ls_periodo,$lo_encabezado);
		$li_row=4;
		$li_totrow=$io_report->DS->getRowCount("codconc");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codconc=$io_report->DS->data["codconc"][$li_i];
			$ls_nomcon=$io_report->DS->data["nomcon"][$li_i];
			$ls_sigcon=$io_report->DS->data["sigcon"][$li_i];
			switch($ls_sigcon)
			{
				case "A":
					$ls_signo="Asignación";
					break;
				case "D":
					$ls_signo="Deducción";
					break;
				case "P":
					$ls_signo="Aporte Patronal";
					break;
				case "B":
					$ls_signo="Reintegro de Deducción";
					break;
				case "E":
					$ls_signo="Reintegro de Asignación";
					break;
				case "R":
					$ls_signo="Reporte";
					break;
				case "X":
					$ls_signo="Prestaciones Sociales";
					break;
				case "I":
					$ls_signo="Intereses de Prestaciones";
					break;
			}
			$ls_concepto = ''.$ls_signo.' Concepto '.$ls_codconc.' - '.$ls_nomcon.''; // Agregar el título
			$lo_hoja->write($li_row, 0, $ls_concepto, $lo_titulo);
			$lo_hoja->write_blank($li_row, 1,$lo_titulo);
			$lo_hoja->write_blank($li_row, 2,$lo_titulo);
			$li_row++;
			$lo_hoja->write($li_row, 0, "Nro", $lo_titulo2);
			$lo_hoja->write($li_row, 1, "Cédula", $lo_titulo2);
			$lo_hoja->write($li_row, 2, "Apellidos y Nombre", $lo_titulo2);
			$lo_hoja->write($li_row, 3, "Monto", $lo_titulo2);
			$li_row++;
			$lb_valido=$io_report->uf_consolidadoconcepto_personal($ls_codnomdes,$ls_codnomhas,$ls_codconc,$ls_codperdes,
																   $ls_codperhas,$ls_conceptocero,$ls_personaldes,$ls_personalhas,
																   $ls_subnomdes,$ls_subnomhas,$ls_orden,$ls_anocurper); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_montot=0;
				$li_totunidad=0;
				$li_totrow_det=$io_report->DS_detalle->getRowCount("cedper");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codper=$io_report->DS_detalle->data["codper"][$li_s];
					$ls_cedper=$io_report->DS_detalle->data["cedper"][$li_s];
					$ls_apenomper=$io_report->DS_detalle->data["apeper"][$li_s].", ". $io_report->DS_detalle->data["nomper"][$li_s];
					$li_montot=$li_montot + abs($io_report->DS_detalle->data["total"][$li_s]);
					$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["total"][$li_s]));
	
					$ls_unidad=$io_report->uf_consolidadoconcepto_personal_unidad($ls_codnomdes,$ls_codnomhas,$ls_codconc,$ls_codperdes,
																   				  $ls_codperhas,$ls_codper,$ls_anocurper);
					$li_totunidad=$li_totunidad+$ls_unidad;

					$lo_hoja->write($li_row, 0, $li_s, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_cedper, $lo_datacenter);
					$lo_hoja->write($li_row, 2, $ls_apenomper, $lo_dataleft);
					$lo_hoja->write($li_row, 3, $ls_unidad, $lo_dataright);
					$lo_hoja->write($li_row, 4, $li_valsal, $lo_dataright);
					$li_row++;
				}
				$io_report->DS_detalle->resetds("cedper");
				$li_montot=$io_fun_nomina->uf_formatonumerico(abs($li_montot));
				$lo_hoja->write($li_row,2,"TOTAL  ",$lo_dataright2);;
				$lo_hoja->write($li_row,3,$li_totunidad,$lo_dataright);
				$lo_hoja->write($li_row,4,$li_montot,$lo_dataright);
				$li_row=$li_row+2;
			}
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"consolidado_de_conceptos.xls\"");
		header("Content-Disposition: inline; filename=\"consolidado_de_conceptos.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}/// fin de else // Imprimimos el reporte
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 
