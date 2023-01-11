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
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_prestacionantiguedad_intereses.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_desnom,$as_anocurper,$as_desmesperdes,$as_desmesperhas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_anocurper // Año en curso
		//	    		   as_desmesper // Mes en curso
		//	    		   io_pdf // Instancia de objetso pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		if(trim($as_desmesperdes)==trim($as_desmesperhas))
		{
			$ls_mes=" Mes: ".$as_desmesperdes;
		}
		else
		{
			$ls_mes=" Meses: ".$as_desmesperdes." - ".$as_desmesperhas;
		}
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$ls_periodo="Año: ".$as_anocurper." ".$ls_mes;
		$li_tm=$io_pdf->getTextWidth(11,$ls_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$ls_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,11,$as_desnom); // Agregar el título
		$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSety(700);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,679,500,$io_pdf->getFontHeight(17));
        $io_pdf->setColor(0,0,0);
		$la_data[1]=array('periodo'=>'<b>Periodo</b>',
						  'cedula'=>'<b>Cédula</b>',
						  'nombre'=>'<b>Apellidos y Nombres</b>',
						  'antiguedad'=>'<b>Monto Prestación Antiguedad</b>',
						  'tasa'=>'<b>Tasa de Interes</b>',
						  'monto'=>'<b>Monto Interes</b>');
		$la_columna=array('periodo'=>'',
						  'cedula'=>'',
						  'nombre'=>'',
						  'antiguedad'=>'',
						  'tasa'=>'',
						  'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('periodo'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'antiguedad'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'tasa'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
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
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('periodo'=>'',
						  'cedula'=>'',
						  'nombre'=>'',
						  'antiguedad'=>'',
						  'tasa'=>'',
						  'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('periodo'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'antiguedad'=>array('justification'=>'right','width'=>120), // Justificación y ancho de la columna
						 			   'tasa'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('total'=>'',
						  'antiguedad'=>'',
						  'tasa'=>'',
						  'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>260), // Justificación y ancho de la columna
						 			   'antiguedad'=>array('justification'=>'right','width'=>120), // Justificación y ancho de la columna
						 			   'tasa'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
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
	$ls_titulo="<b>Listado de Intereses Prestación de Antiguedad</b>";
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
		$lb_valido=$io_report->uf_prestacionantiguedadintereses_personal($ls_codnomdes,$ls_codnomhas,$ls_anocurperdes,$ls_mescurperdes,
																		 $ls_anocurperhas,$ls_mescurperhas); // Obtenemos el detalle del reporte
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
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.9,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_anocurperdes,$ls_desmesperdes,$ls_desmesperhas,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		uf_print_cabecera($io_pdf);
		$li_i=0;
		$li_monant_total=0;
		$li_monint_total=0;
		while(!$io_report->rs_data->EOF)
		{
			$li_i++;
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_periodo=str_pad($io_report->rs_data->fields["mescurper"],2,"0",0)." - ".$io_report->rs_data->fields["anocurper"];
			$li_monant_total=$li_monant_total+$io_report->rs_data->fields["monant"];
			$li_monint_total=$li_monint_total+$io_report->rs_data->fields["monint"];
			$li_monant=$io_fun_nomina->uf_formatonumerico($io_report->rs_data->fields["monant"]);
			$li_porint=$io_fun_nomina->uf_formatonumerico($io_report->rs_data->fields["porint"]*100);
			$li_monint=$io_fun_nomina->uf_formatonumerico($io_report->rs_data->fields["monint"]);
			$la_data[$li_i]=array('periodo'=>$ls_periodo,'cedula'=>$ls_cedper,'nombre'=>$ls_nomper,'antiguedad'=>$li_monant,
								  'tasa'=>$li_porint,'monto'=>$li_monint);
			$io_report->rs_data->MoveNext();
		}
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		unset($la_data);
		$li_monant_total=$io_fun_nomina->uf_formatonumerico($li_monant_total);
		$li_monint_total=$io_fun_nomina->uf_formatonumerico($li_monint_total);
		$la_data[1]=array('total'=>'<b>Total BS </b>','antiguedad'=>$li_monant_total,'tasa'=>'','monto'=>$li_monint_total);
		uf_print_totales($la_data,$io_pdf); // Imprimimos el detalle 
		unset($la_data);
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