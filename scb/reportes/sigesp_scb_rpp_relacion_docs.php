<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
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
		print "opener.document.form1.submit();";
		print "close();";
		print "</script>";		
	}
	ini_set('memory_limit','1024M');
 	ini_set('max_execution_time ','0');  
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ls_periodo,$ls_denban,$ls_ctaban,$ls_dencta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci�n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,550,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,770,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,750,11,$as_titulo); // Agregar el t�tulo
		$io_pdf->addText(500,750,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(50,730,10,'<b>Banco:</b>'.$ls_denban); // Agregar la Fecha
		$io_pdf->addText(50,717,10,'<b>Cuenta:</b>'.$ls_ctaban."   ".$ls_dencta); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: $la_data
		//	    		   io_pdf // 
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci�n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetY(700);
		$la_columna=array('fecha'=>'<b>Fecha</b>',
						  'documento'=>'<b>Documento</b>',
						  'ncontrol'=>'<b>N� Control Interno</b>',
						  'operacion'=>'<b>Operacion</b>',
						  'proveedor'=>'<b>Proveedor</b>',
						  'monto'=>'<b>Monto</b>',
						  'estmov'=>'<b>Estatus</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
 						 'shadeCol'=>array(0.95,0.95,0.95), // Color de la sombra
						 'shadeCol2'=>array(1.5,1.5,1.5), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', 
						 'cols'=>array('fecha'=>array('justification'=>'center','width'=>56),
						 			   'documento'=>array('justification'=>'center','width'=>90),
									   'ncontrol'=>array('justification'=>'center','width'=>90),
						 			   'operacion'=>array('justification'=>'center','width'=>55),
						 			   'proveedor'=>array('justification'=>'center','width'=>120),
						 			   'monto'=>array('justification'=>'right','width'=>90),
									   'estmov'=>array('justification'=>'center','width'=>60))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);			
		return $io_pdf;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ad_debitos,$ad_creditos,$ad_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: as_numdoc // N�mero del documento
		//	    		   as_conmov // concepto del documento
		//	    		   as_nomproben // nombre del proveedor beneficiario
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		$la_data[1]=array('name'=>'<b>Total Cr�ditos:</b>', 'monto'=>$ad_creditos);
		$la_data[2]=array('name'=>'<b>Total D�bitos:</b>', 'monto'=>$ad_debitos);
		$la_data[3]=array('name'=>'<b>Total Saldo:</b>', 'monto'=>$ad_total);
		$la_columna=array('name'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Justificaci�n y ancho de la columna
						 'xPos'=>460,
						 'cols'=>array('name'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		return $io_pdf;
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_scb_class_report.php");
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
    require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");

	$sig_inc   = new sigesp_include();
	$con       = $sig_inc->uf_conectar();
	$io_report = new sigesp_scb_class_report($con);
	$io_sql    = new class_sql($con);
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codban		= $_GET["codban"];
	$ls_ctaban		= $_GET["ctaban"];
	$ls_denban		= $_GET["denban"];
	$ls_dencta		= $_GET["dencta"];
	$ls_documentos	= $_GET["documentos"];
	$ls_fechas		= $_GET["fechas"];
	$ls_operaciones = $_GET["operaciones"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scb_class_reportbsf.php");
		$io_report = new sigesp_scb_class_reportbsf($con);
		$ls_tipbol = 'Bs.F.';
	}
	$ls_titulo		= "<b>Relaci�n de Documentos $ls_tipbol</b>";
	//Descompongo la cadena de documentos en un arreglo tomando como separaci�n el '-'
	$arr_documentos = explode("]",$ls_documentos);
	$li_totdoc		= count((array)$arr_documentos);
	//Descompongo la cadena de documentos en un arreglo tomando como separaci�n el '-'
	$arr_fecmov		= explode("]",$ls_fechas);
	$li_totfec		= count((array)$arr_fecmov);
	//Descompongo la cadena de documentos en un arreglo tomando como separaci�n el '-'
	$arr_operaciones = explode("]",$ls_operaciones);
	$li_totope       = count((array)$arr_operaciones);
	$la_data=$io_report->uf_cargar_documentos_relacion($arr_documentos,$arr_fecmov,$arr_operaciones,$ls_codban,$ls_ctaban);
	$ld_totdeb  = 0;
	$ld_totcre  = 0;
	$ldec_saldo = 0;
	if(!empty($la_data))
	{
		//
		set_time_limit(1800);
		$io_pdf=new Cezpdf('A4','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci�n de los margenes en cent�metros
		$io_pdf=uf_print_encabezado_pagina($ls_titulo,'',$ls_denban,$ls_ctaban,$ls_dencta,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(500,50,10,'','',1); // Insertar el n�mero de p�gina
		$io_pdf=uf_print_detalle($la_data,$io_pdf);
		$ldec_saldo = ($io_report->totcre-$io_report->totdeb);//Calculo del saldo total para todas las cuentas
		$ld_totcre  = number_format($io_report->totcre,2,",",".");
		$ld_totdeb  = number_format($io_report->totdeb,2,",",".");
		$ldec_saldo = number_format($ldec_saldo,2,",",".");
		$io_pdf=uf_print_totales($ld_totdeb,$ld_totcre,$ldec_saldo,$io_pdf);
		$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
		$io_pdf->ezStream(); // Mostramos el reporte
		unset($io_pdf);
	}
	else
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
?> 