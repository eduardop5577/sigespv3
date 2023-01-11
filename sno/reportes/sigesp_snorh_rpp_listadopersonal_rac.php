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
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_personal_rac_rec.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,955,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=496-($li_tm/2);
		$io_pdf->addText($tm,540,11,$as_titulo); // Agregar el título
		$io_pdf->addText(912,560,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(918,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_cabecera,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$la_data[1]=array('codigo'=>'<b>Cód Empleado</b>',
						  'ubiadm'=>'<b>Cód Ubi. Adm</b>',
						  'unidad'=>'<b>Unidad Adscripción</b>',
						  'ubicacion'=>'<b>Ubicación Geográfica</b>',
						  'cedula'=>'<b>Cédula</b>',
						  'nombre'=>'<b>Nombre y Apellido</b>',
						  'cargo'=>'<b>Cargo por Conversión</b>',
						  'cargo2'=>'<b>Manual de Competencia  Genérica</b>',
						  'grado'=>'<b>Grado</b>',
						  'clase'=>'<b>Clase</b>',
						  'basico'=>'<b>Sueldo</b>',
						  'compensacion'=>'<b>Compensación</b>',
						  'total'=>'<b>Totales</b>');
		$la_columna=array('codigo'=>'',
						  'ubiadm'=>'',
						  'unidad'=>'',
						  'ubicacion'=>'',
						  'cedula'=>'',
						  'nombre'=>'',
						  'cargo'=>'',
						  'cargo2'=>'',
						  'grado'=>'',
						  'clase'=>'',
						  'basico'=>'',
						  'compensacion'=>'',
						  'total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'ubiadm'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'unidad'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'ubicacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cargo2'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'grado'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'clase'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'basico'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'compensacion'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('codigo'=>'<b>Cód Empleado</b>',
						  'ubiadm'=>'<b>Cód Ubi. Adm</b>',
						  'unidad'=>'<b>Unidad Adscripción</b>',
						  'ubicacion'=>'<b>Ubicación Geográfica</b>',
						  'cedula'=>'<b>Cédula</b>',
						  'nombre'=>'<b>Nombre y Apellido</b>',
						  'cargo'=>'<b>Cargo por Conversión</b>',
						  'cargo2'=>'<b>Manual de Competencia  Genérica</b>',
						  'grado'=>'<b>Grado</b>',
						  'clase'=>'<b>Clase</b>',
						  'basico'=>'<b>Sueldo</b>',
						  'compensacion'=>'<b>Compensación</b>',
						  'total'=>'<b>Totales</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'ubiadm'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'unidad'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'ubicacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'cargo2'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'grado'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'clase'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'basico'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'compensacion'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>REGISTRO DE ASIGNACIÓN DE CARGOS (RAC)</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_anio=$io_fun_nomina->uf_obtenervalor_get("anio","");	
	$ls_mes=$io_fun_nomina->uf_obtenervalor_get("mes","");	
	$ls_peri=$io_fun_nomina->uf_obtenervalor_get("codperi","");	
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","");	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadopersonal_personal_rac_rec($ls_codnomdes,$ls_codnomhas,$ls_codperdes,$ls_codperhas,$ls_anio,$ls_mes,$ls_peri,$ls_orden); // Obtenemos el detalle del reporte
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
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(950,50,10,'','',1); // Insertar el número de página
		//$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
		//uf_print_cabecera($io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
		
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
			$la_data[$li_i]=array('codigo'=>$ls_codcarnomina,
						  'ubiadm'=>$ls_codubiadm,
						  'unidad'=>$ls_desuniadm,
						  'ubicacion'=>$ls_ubicacionfisica,
						  'cedula'=>$ls_cedper,
						  'nombre'=>$ls_nomper,
						  'cargo'=>$ls_denasicar,
						  'cargo2'=>$ls_descasicar,
						  'grado'=>$ls_codgra,
						  'clase'=>$ls_claasicar,
						  'basico'=>$li_sueper,
						  'compensacion'=>$li_compensacion,
						  'total'=>$li_total);
		
		}
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 