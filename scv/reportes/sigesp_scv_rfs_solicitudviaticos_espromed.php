<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
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
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_codsolvia,$io_encabezado,$as_tipoviatico,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_codsolvia // Código de Solicitud de Viaticos
		//	    		   io_encabezado // Instancia del encabezado
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado, $io_pdf;

		$io_pdf->saveState();
//		$io_pdf->line(450,763.75,570,763.75);
//		$io_pdf->line(35,40,570,40);
//		$io_pdf->line(30,785,570,785);
//		$io_pdf->line(30,700,570,700);
//		$io_pdf->line(30,785,30,700);
//		$io_pdf->line(150,785,150,700);
		$io_pdf->line(400,732,400,697);
		$io_pdf->line(571,732,571,697);
		$io_pdf->line(400,732,571,732);
		$io_pdf->line(400,715,571,715);
//        $io_pdf->setColor(0.9,0.9,0.9);
 //       $io_pdf->filledRectangle(451,764.75,118,$io_pdf->getFontHeight(16.8));
 //       $io_pdf->filledRectangle(451,722.25,118,$io_pdf->getFontHeight(16.8));
        $io_pdf->setColor(0,0,0);		
		$io_pdf->addText(420,720,10,"N° SOLICITUD VIATICOS"); // Agregar NRO DE CONTROL
		$io_pdf->addText(455,702,11,$as_codsolvia); // Agregar NRO DE CONTROL
//		$io_pdf->addText(180,740,11,"<b>SOLICITUD DE VIATICOS </b>"); // Agregar NRO DE CONTROL
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nomper,$as_desuniadm,$as_codcueban,$as_tipcuebanper,$as_desded,$as_destipper,
							  $as_codclavia,$ad_fecsalvia,$ad_fecregvia,$ai_numdiavia,$as_denmis,$ai_acompanante,$as_cargo,
							  $as_telefono,$ai_sueper,$as_denestpro1,$as_denestpro2,$as_desrut,$ai_solviaext,$as_obssolvia,$ad_fecsolvia,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: $as_cedper  // Cédula de personal
		//	    		   $as_nomper     // Nombre del personal
		//	    		   $as_desuniadm     // descripción de la unidad administrativa
		//	    		   $as_codcueban     // codigo cuenta de banco
		//	    		   $as_tipcuebanper     // tipo de cuenta de banco
		//	    		   $as_desded  // Descripción de la dedicación
		//	    		   $as_destipper  // Descripción del tipo de personal
		//	    		   $as_codclavia  // Clasificación del viaticos
		//	    		   $ad_fecsalvia  // fecha de salida del viatico
		//	    		   $ad_fecregvia  // fecha de regreso del viatico
		//	    		   $ai_numdiavia     // numero de dias
		//	    		   $as_denmis  // Denominación de las misiones
		//	    		   $as_telefono  // Telefono de personal
		//	    		   $ai_sueper  // Sueldo del personal
		//	    		   $as_denestpro1  // Denominacion estructuta programatica nivel 1
		//	    		   $as_denestpro2  // Denominacion estructuta programatica nivel 2
		//	    		   $as_desrut  // Denominacion de la ruta
		//	    		   $ai_solviaext  // Indica si el viatico es para el exterior
		//	    		   $as_obssolvia  // Observacion de la solicitud
		//	    		   io_pdf         // Instancia del objeto pdf
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/11/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();				
		$ad_fecsalvia=$io_funciones->uf_convertirfecmostrar($ad_fecsalvia);
		$ad_fecregvia=$io_funciones->uf_convertirfecmostrar($ad_fecregvia);
		$ad_fecsolvia=$io_funciones->uf_convertirfecmostrar($ad_fecsolvia);
        $io_pdf->setColor(0,0,0);		
		$la_data=array(array('titulo'=>'<b>SOLICITUD DE VIATICOS </b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 18, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-4);
		$la_data=array(array('nombre'=>$ad_fecsolvia,'cedula'=>$as_desuniadm,'cargo'=>$ad_fecsalvia,'unidad'=>$ad_fecregvia));
		$la_columna=array('nombre'=>'<b>FECHA / EMISION</b>','cedula'=>'<b>UNIDAD SOLICITANTE</b>',
						  'cargo'=>'<b>FECHA DE SALIDA</b>','unidad'=>'<b>FECHA DE RETORNO</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>90),
						       		   'cedula'=>array('justification'=>'center','width'=>270),
									   'cargo'=>array('justification'=>'center','width'=>85),
									   'unidad'=>array('justification'=>'center','width'=>95))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$li_pernocta=$ai_numdiavia-1;
		$ai_numdiavia=number_format($ai_numdiavia,2,",",".");
		$li_pernocta=number_format($li_pernocta,2,",",".");
		$io_pdf->ezSetDy(-2);
		$la_data=array(array('nombre'=>$ai_numdiavia,'cedula'=>$li_pernocta,'cargo'=>$as_obssolvia,'unidad'=>$as_desrut));
		$la_columna=array('nombre'=>'<b>N° DIAS</b>','cedula'=>'<b>N° DIAS PERNOCTA</b>',
						  'cargo'=>'<b>OBJETO DEL VIATICO</b>','unidad'=>'<b>DESTINO</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>60),
						       		   'cedula'=>array('justification'=>'center','width'=>60),
									   'cargo'=>array('justification'=>'center','width'=>275),
									   'unidad'=>array('justification'=>'center','width'=>145))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_asignaciones($aa_data,$ai_total,$as_totalletras,$as_cedper,$as_nomper,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_asignaciones
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-2);
		$la_data=array(array('nombre'=>$as_cedper,'cargo'=>$as_nomper,'unidad'=>$ai_total));
		$la_columna=array('nombre'=>'<b>N° RIF - CI</b>','cargo'=>'<b>NOMBRE DEL BENEFICIARIO</b>','unidad'=>'<b>MONTO EN Bs.</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>70),
									   'cargo'=>array('justification'=>'center','width'=>325),
									   'unidad'=>array('justification'=>'center','width'=>145))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data=array(array('letra'=>' '),array('letra'=>'<b>CANTIDAD EN LETRAS:</b> '.$as_totalletras),array('letra'=>''));
		$la_columna=array('letra'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_datos=array(array('titulo'=>'<b>DETALLE DE ASIGNACIONES</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columna=array('codigo'=>'CODIGO','descripcion'=>'CONCEPTO','tarifa'=>'TARIFA','dias'=>'DIAS','subtotal'=>'TOTAL');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>60),
						               'descripcion'=>array('justification'=>'left','width'=>220),
									   'tarifa'=>array('justification'=>'right','width'=>100),
									   'dias'=>array('justification'=>'center','width'=>60),
									   'subtotal'=>array('justification'=>'right','width'=>100))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($aa_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('codigo'=>'No. PERSONAS','subtotal'=>'1,00'),array('codigo'=>'TOTAL VIATICOS','subtotal'=>$ai_total));
		$la_columna=array('codigo'=>'CODIGO','subtotal'=>'TOTAL');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'right','width'=>440),
									   'subtotal'=>array('justification'=>'right','width'=>100))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_datos=array(array('titulo'=>'<b>OBSERVACIONES</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_datos=array(array('titulo'=>'<b></b>'),array('titulo'=>'<b></b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_presupuestario($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_presupuestario
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datos=array(array('titulo'=>'<b>CONTROL PRESUPUESTARIO</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columna=array('ano'=>'<b>Año</b>',
   						  'proyecto'=>'<b>'.$_SESSION["la_empresa"]["nomestpro1"].'</b>',
						  'especifica'=>'<b>'.$_SESSION["la_empresa"]["nomestpro2"].'</b>',
						  'cuenta'=>'<b>Cuenta</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('ano'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'proyecto'=>array('justification'=>'center','width'=>190), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>190), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('titulo'=>'<b>FIRMAS AUTORIZADAS</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('elaborado'=>'<b>ELABORADO</b>','revisado'=>'<b>UNIDAD SOLICITANTE</b>','autorizado'=>'<b>GERENTE DE ADMINISTRACION Y SERVICIOS</b>','aprobado'=>'<b>PRESIDENCIA</b>'),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>'','aprobado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>'','aprobado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>'','aprobado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>'','aprobado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>'','aprobado'=>''),
					   array('elaborado'=>'FIRMA / SELLO ','revisado'=>'FIRMA / SELLO ','autorizado'=>'FIRMA / SELLO ','aprobado'=>'FIRMA / SELLO '));
		$la_columna=array('elaborado'=>'',
   						  'revisado'=>'',
						  'autorizado'=>'',
						  'aprobado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('elaborado'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'revisado'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'autorizado'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'aprobado'=>array('justification'=>'center','width'=>135))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_scv_class_report.php");
	$io_report=new sigesp_scv_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_viaticos.php");
	$io_fun_viaticos=new class_funciones_viaticos();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_codsoldes=$io_fun_viaticos->uf_obtenervalor_get("codsoldes","");
	$ls_codsolhas=$io_fun_viaticos->uf_obtenervalor_get("codsolhas","");
	$ld_desde=$io_fun_viaticos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_viaticos->uf_obtenervalor_get("hasta","");

	$ls_titulo="<b> SOLICITUD Y APROBACION DE VIATICOS Y </b>";
	$ls_titulo1="<b> BOLETOS AEREOS </b>";
	$ls_fecha="Periodo ".$ld_desde." - ".$ld_hasta;
	$ls_modalidad= $_SESSION["la_empresa"]["estmodest"];
	switch($ls_modalidad)
	{
		case "1": // Modalidad por Proyecto
			$ls_titest="Estructura Presupuestaria ";
			break;
			
		case "2": // Modalidad por Presupuesto
			$ls_titest="Estructura Programática ";
			break;
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codsolvia=$io_fun_viaticos->uf_obtenervalor_get("codsolvia","");
	$li_orden="";
	$lb_tipben="P";
	//--------------------------------------------------------------------------------------------------------------------------------
	$arrResultado=$io_report->uf_select_solicitudpago_personal($ls_codemp,$ls_codsoldes,$ls_codsolhas,$ld_desde,$ld_hasta,$li_orden,$ls_codsolvia,$rs_data); // Cargar el DS con los datos de la cabecera del reporte
	$lb_valido=$arrResultado['lb_valido'];
	$rs_data=$arrResultado['rs_data'];
	if(!$lb_valido)
	{
		$lb_valido=$io_report->uf_select_solicitudpago_beneficiario($ls_codemp,$ls_codsoldes,$ls_codsolhas,$ld_desde,$ld_hasta,$li_orden,$ls_codsolvia);
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
		$io_pdf->ezSetCmMargins(3.2,3,3,3); // Configuración de los margenes en centímetros
		$io_pdf->ezStartPageNumbers(545,25,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->io_sql->num_rows($rs_data);
		$li_i=0;
		
		while($row=$io_report->io_sql->fetch_row($rs_data))
		{
			$li_i++;
			$ls_codsolvia= $row["codsolvia"];
			$io_encabezado=$io_pdf->openObject();
			$ls_cedper= $row["cedper"];
			$ls_nomper= $row["nomper"]." ".$row["apeper"];
			$ls_cargo= $row["cargo"];
			$ls_desuniadm= $row["denuniadm"];
			$ls_codcueban = $row["codcueban"];
			$ls_tipcuebanper= $row["tipcuebanper"];
			$ls_desded= $row["desded"];
			$ls_destipper= $row["destipper"];			
			$ls_codclavia= $row["codclavia"];
			$ld_fecsalvia= $row["fecsalvia"];
			$ld_fecsolvia= $row["fecsolvia"];
			$ld_fecregvia= $row["fecregvia"];
			$li_numdiavia= $row["numdiavia"];
			$ls_denmis= $row["denmis"];
			$li_acompanante= $row["acompanante"];
			$ls_telefono= $row["telmovper"];
			$li_sueper= $row["sueper"];
			$li_solviaext= $row["solviaext"];
			$ls_obssolvia= $row["obssolvia"];
			$ls_tipvia= $row["tipvia"];
			switch($ls_tipvia)
			{
				case '1':
					$ls_tipoviatico="INSTALACION";
				break;
				case '2':
					$ls_tipoviatico="GASTOS DE TRANSPORTE";
				break;
				case '3':
					$ls_tipoviatico="PERMANENCIA";
				break;
				case '4':
					$ls_tipoviatico="INTERNACIONALES";
				break;
				case '5':
					$ls_tipoviatico="NACIONALES";
				break;
				default:
					$ls_tipoviatico="";
				break;
			}
			$li_sueper=number_format($li_sueper,2,",",".");
			uf_print_encabezado_pagina($ls_codsolvia,$io_encabezado,$ls_tipoviatico,$io_pdf); // Imprimimos el encabezado de la página
			
			$lb_existe=$io_report->uf_select_solicitudpago_spg($ls_codemp,$ls_codsolvia);
			$ls_denestpro1="";
			$ls_denestpro2="";
			if($lb_existe)
			{
				$li_totrow_detpres=$io_report->ds_detpresup->getRowCount("spg_cuenta");
				$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
				for($li_j=1;$li_j<=$li_totrow_detpres;$li_j++)
				{
					$ls_denestpro1=$io_report->ds_detpresup->data["denestpro1"][1];
					$ls_denestpro2=$io_report->ds_detpresup->data["denestpro2"][1];
				}
			}
			$ls_desrut=$io_report->uf_select_ruta($ls_codemp,$ls_codsolvia);
			uf_print_cabecera($ls_cedper,$ls_nomper,$ls_desuniadm,$ls_codcueban,$ls_tipcuebanper,$ls_desded,$ls_destipper,
							  $ls_codclavia,$ld_fecsalvia,$ld_fecregvia,$li_numdiavia,$ls_denmis,$li_acompanante,$ls_cargo,
							  $ls_telefono,$li_sueper,$ls_denestpro1,$ls_denestpro2,$ls_desrut,$li_solviaext,$ls_obssolvia,$ld_fecsolvia,$io_pdf); // Imprimimos la cabecera del registro
			$lb_existe=$io_report->uf_select_solicitudpago_asignaciones($ls_codemp,$ls_codsolvia);
			if($lb_existe)
			{
				$li_totrow_det=$io_report->ds_detalle->getRowCount("codasi");
				$li_total=0;
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codasi= $io_report->ds_detalle->data["codasi"][$li_s];
					$ls_denasi= $io_report->ds_detalle->data["denasi"][$li_s];
					$li_canasi= $io_report->ds_detalle->data["canasi"][$li_s];
					$li_monto= $io_report->ds_detalle->data["monto"][$li_s];
					$li_subtotal = $li_monto*$li_canasi;
					$li_total=$li_total+$li_subtotal;
					$li_canasi=number_format($li_canasi,2,",",".");
					$li_monto=number_format($li_monto,2,",",".");
					$li_subtotal=number_format($li_subtotal,2,",",".");
					$la_data[$li_s]=array('codigo'=>$ls_codasi,'descripcion'=>$ls_denasi,'tarifa'=>$li_monto,'dias'=>$li_canasi,'subtotal'=>$li_subtotal);
				}
				$io_numero_letra->setNumero($li_total);
				$ls_totalletras=$io_numero_letra->letra();
				$li_total=number_format($li_total,2,",",".");
				uf_print_detalle_asignaciones($la_data,$li_total,$ls_totalletras,$ls_cedper,$ls_nomper,$io_pdf); // Imprimimos el detalle 
				unset($la_data);
				$io_report->ds_detalle->reset_ds();
				$lb_existe=$io_report->uf_select_solicitudpago_spg($ls_codemp,$ls_codsolvia);
				if($lb_existe)
				{
					$li_totrow_detpres=$io_report->ds_detpresup->getRowCount("spg_cuenta");
					$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
					for($li_j=1;$li_j<=$li_totrow_detpres;$li_j++)
					{
						$ls_spgcuenta=$io_report->ds_detpresup->data["spg_cuenta"][$li_j];
						$ls_denestpro1=$io_report->ds_detpresup->data["denestpro1"][$li_j];
						$ls_denestpro2=$io_report->ds_detpresup->data["denestpro2"][$li_j];
						$la_data[$li_j]=array('ano'=>$ls_ano,'proyecto'=>$ls_denestpro1,'especifica'=>$ls_denestpro2,'cuenta'=>$ls_spgcuenta);
					}
//					uf_print_detalle_presupuestario($la_data,$io_pdf); // Imprimimos el detalle 
					unset($la_data);
				}
			}
			
			uf_print_firmas($io_pdf);
			$io_pdf->stopObject($io_encabezado); // Detener el objeto cabecera
			unset($io_encabezado);
			if($li_i<$li_totrow)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
			}
			
		}
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		unset($io_pdf);
		
	}
	$io_report->io_sql->free_result($rs_data);
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_viaticos);
?> 