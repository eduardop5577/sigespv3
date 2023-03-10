<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
	
	// para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spg_estado_de_resultado.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_nombre_empresa,$as_bs,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 05/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		//print $as_titulo."---".$as_nombre_empresa."---".$as_bs;
		//$io_pdf->rectangle(10,480,988,110);
		//$io_pdf->addText(15,580,11,"<b>OFICINA NACIONAL DE PRESUPUESTO (ONAPRE)</b>"); // Agregar la Fecha
		//$io_pdf->addText(15,565,11,"<b>OFICINA DE PLANIFICACI?N DEL SECTOR UNIVERSITARIO (OPSU)</b>"); // Agregar la Fecha
		
		$li_tm=$io_pdf->getTextWidth(12,$as_nombre_empresa);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,12,$as_nombre_empresa); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,515,12,$as_titulo); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(12,$as_bs);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,500,12,$as_bs); // Agregar el t?tulo
		
		//$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		//$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(590);
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_nombre    = $_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads = $_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona = $_SESSION['la_empresa']['codasiona'];
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones = new class_funciones();	
		$ls_periodo   = $io_funciones->uf_convertirfecmostrar(substr($_SESSION['la_empresa']['periodo'],0,10));
		$la_data=array(array('name'=>'<b>CODIGO PRESUPUESTARIO DEL ENTE:     </b>'.'<b>'.$ls_codasiona.'</b>'),
		               array('name'=>'<b>DENOMINACION:    </b>'.'<b>'.$ls_nombre.'</b>'),
					   array('name'=>'<b>ORGANO DE ADSCRIPCION:    </b>'.'<b>'.$ls_nomorgads.'</b>'),
		               array('name'=>'<b>PERIODO PRESUPUESTARIO:    </b>'.'<b>'.$ls_periodo.'</b>'));
		$la_columna=array('name'=>'','name'=>'','name'=>'','name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tama?o de Letras
						 'titleFontSize' => 8, // Tama?o de Letras de los t?tulos
						 'showLines'=>0,        // Mostrar L?neas
						 'shaded'=>0,           // Sombra entre l?neas
						 'xPos'=>465,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($ai_mesdes,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 05/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(475);
		if($ai_mesdes==1)
		{
		  $ls_etiqueta="I";
		}
		if($ai_mesdes==4)
		{
		  $ls_etiqueta="II";
		}
		if($ai_mesdes==7)
		{
		  $ls_etiqueta="III";
		}
		if($ai_mesdes==10)
		{
		  $ls_etiqueta="IV";
		}
		$la_data=array(array('name1'=>'','name2'=>'<b>TRIMESTRE N?: '.strtoupper($ls_etiqueta).'</b>',
		                     'name3'=>'<b>VARIACI?N EJECUTADO - PROGRAMADO TRIMESTRE N?: '.strtoupper($ls_etiqueta).'</b>',
							 'name4'=>'<b>TOTAL ACUMULADO AL TRIMESTRE N?: '.strtoupper($ls_etiqueta).'</b>'));
		$la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 9,       // Tama?o de Letras
						 'titleFontSize' => 9, // Tama?o de Letras de los t?tulos
						 'showLines'=>1,        // Mostrar L?neas
						 'shaded'=>0,           // Sombra entre l?neas
						 'xPos'=>509,
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'cols'=>array('name1'=>array('justification'=>'center','width'=>450),// Justificaci?n y ancho de la columna
						               'name2'=>array('justification'=>'center','width'=>200),// Justificaci?n y ancho de la columna
									   'name3'=>array('justification'=>'center','width'=>140),// Justificaci?n y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>200))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 05/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominaci?n</b>','presupuesto'=>'<b>Presupuesto Aprobado  </b>',
		                     'presupuesto_modificado'=>'<b>Presupuesto Modificado</b>','programado'=>'<b>Programado</b>',
		                     'ejecutado'=>'<b>Ejecutado</b>','absoluta'=>'<b>Absoluta</b>','porcentaje'=>'<b>Porcentaje (%)</b>',
							 'programado_acumulado'=>'<b>Programado</b>','ejecutado_acumulado'=>'<b>Ejecutado</b>'));
		$la_columna=array('cuenta'=>'','denominacion'=>'','presupuesto'=>'','presupuesto_modificado'=>'',
		                  'programado'=>'','ejecutado'=>'','absoluta'=>'','porcentaje'=>'','programado_acumulado'=>'',
						  'ejecutado_acumulado'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 9,       // Tama?o de Letras
						 'titleFontSize' => 9, // Tama?o de Letras de los t?tulos
						 'showLines'=>2,        // Mostrar L?neas
						 'shaded'=>0,           // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>160), // Justificaci?n y ancho de la columna
									   'presupuesto'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'presupuesto_modificado'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'programado'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'ejecutado'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'absoluta'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
									   'porcentaje'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
									   'programado_acumulado'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'ejecutado_acumulado'=>array('justification'=>'center','width'=>100))); // Justificaci?n y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 05/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>160), // Justificaci?n y ancho de la columna
									   'presupuesto'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'presupuesto_modificado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'programado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
						 			   'ejecutado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'absoluta'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'porcentaje'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'programado_acumulado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'ejecutado_acumulado'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		
		$la_columnas=array('cuenta'=>'',
						   'denominacion'=>'',
						   'presupuesto'=>'',
						   'presupuesto_modificado'=>'',
						   'programado'=>'',
						   'ejecutado'=>'',
						   'absoluta'=>'',
						   'porcentaje'=>'',
						   'programado_acumulado'=>'',
						   'ejecutado_acumulado'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle*/
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_resultado($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_resultado
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 05/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>250), // Justificaci?n y ancho de la columna
									   'presupuesto'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'presupuesto_modificado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'programado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
						 			   'ejecutado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'absoluta'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'porcentaje'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
									   'programado_acumulado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
									   'ejecutado_acumulado'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		
		$la_columnas=array('total'=>'',
						   'presupuesto'=>'',
						   'presupuesto_modificado'=>'',
						   'programado'=>'',
						   'ejecutado'=>'',
						   'absoluta'=>'',
						   'porcentaje'=>'',
						   'programado_acumulado'=>'',
						   'ejecutado_acumulado'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_resultado
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report=new sigesp_spg_funciones_reportes();	
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("sigesp_spg_class_reportes_instructivos.php");
		$io_report = new sigesp_spg_class_reportes_instructivos();
	//-----------------------------------------------------------------------------------------------------------------------------
		
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		
		$ls_trimestre=$_GET["trimestre"];
		$li_mesdes=substr($ls_trimestre,0,2);
		$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
		$li_meshas=substr($ls_trimestre,2,2);
		$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
		$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
		$ls_nombre_empresa="<b>".$ls_nombre."</b>";
		$ls_titulo=" ESTADO DE RESULTADO";    
		$ls_bs="(En Bol?vares Fuertes)"  ; 
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spg_reportes_estado_de_resultado($ldt_fecdes,$ldt_fechas,"",$ls_mesdes,$ls_meshas);
	 if($lb_valido==false) // Existe alg?n error ? no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	 /*
	    
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(6.9,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_nombre_empresa,$ls_bs,$io_pdf); // Imprimimos el encabezado de la p?gina
 	 */
	//$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el n?mero de p?gina
	
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
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
		$lo_hoja->set_column(0,0,16);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
		$lo_hoja->write(0, 3,$ls_titulo,$lo_titulo);

		$li_row=4;
		$lo_hoja->write(0, 0,"C?DIGO PRESUPUESTARIO DEL ENTE",$lo_dataleft);
		$lo_hoja->write(0, 1, $_SESSION['la_empresa']['codasiona'],$lo_dataleft);
		$lo_hoja->write(1, 0,"DENOMINACION DEL ENTE",$lo_dataleft);
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nombre"],$lo_dataleft);
		$lo_hoja->write(1, 0,"ORGANO DE ADSCRIPCION",$lo_dataleft);
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nomorgads"],$lo_dataleft);
		$lo_hoja->write(1, 0,"PERIODO PRESUPUESTARIO",$lo_dataleft);
		$lo_hoja->write(1, 1,$io_funciones->uf_convertirfecmostrar(substr($_SESSION['la_empresa']['periodo'],0,10)),$lo_dataleft);
		$lo_hoja->write(3, 5,$ls_titulo,$lo_encabezado);
	
		$li_row=4;
		$lo_hoja->write($li_row, 4, "TRIMESTRE N? ",$lo_titulo);
		$lo_hoja->write($li_row, 5, "",$lo_titulo);
		$lo_hoja->write($li_row, 6, "VARIACION EJECUTADO PROGRAMADO EN EL TRIMESTRE N? ",$lo_titulo);
		$lo_hoja->write($li_row, 7, "",$lo_titulo);
		$lo_hoja->write($li_row, 8, "TOTAL ACUMULADO AL TRIMESTRE N?:",$lo_titulo);
		$lo_hoja->write($li_row, 9, "",$lo_titulo);
		$li_row++;
		
		$lo_hoja->write($li_row, 0, "CODIGO",$lo_titulo);
		$lo_hoja->write($li_row, 1, "DENOMINACION",$lo_titulo);
		$lo_hoja->write($li_row, 2, "PRESUPUESTO APROBADO",$lo_titulo);
		$lo_hoja->write($li_row, 3, "PRESUPUESTO MODIFICADO",$lo_titulo);
		$lo_hoja->write($li_row, 4, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 5, "EJECUTADO",$lo_titulo);
		$lo_hoja->write($li_row, 6, "ABSOLUTA",$lo_titulo);
		$lo_hoja->write($li_row, 7, "PORCENTAJE (%)",$lo_titulo);
		$lo_hoja->write($li_row, 8, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 9, "EJECUTADO",$lo_titulo);
		$li_row++;
	
	
	
		$li_total=$io_report->dts_reporte->getRowCount("cuenta");
		for($z=1;$z<=$li_total;$z++)
		{
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_spg_cuenta=trim($io_report->dts_reporte->data["cuenta"][$z]);
			$ls_denominacion=trim(str_replace("</b>","",str_replace("<b>","",$io_report->dts_reporte->data["denominacion"][$z])));
			$ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
			$ld_asignado_modificado=$io_report->dts_reporte->data["asignado_modificado"][$z];
			$ld_programado=$io_report->dts_reporte->data["programado"][$z];
			$ld_ejecutado=$io_report->dts_reporte->data["ejecutado"][$z];
			$ld_variacion_absoluta=$io_report->dts_reporte->data["variacion_absoluta"][$z];
			$ld_variacion_porcentual=$io_report->dts_reporte->data["variacion_porcentual"][$z];
			$ld_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_ejecutado_acumulado=$io_report->dts_reporte->data["ejecutado_acumulado"][$z];
			$ls_tipo=$io_report->dts_reporte->data["tipo"][$z];
			
			$ld_variacion_absoluta=abs($ld_programado-$ld_ejecutado);
			if(($ld_ejecutado==0)or($ld_programado==0))
			{
				$ld_variacion_porcentual=0;
			}//if
			else
			{
				$ld_producto=$ld_ejecutado*100;
				$ld_variacion_porcentual=$ld_producto/$ld_programado;
			}//else
			$ld_asignado=number_format($ld_asignado,2,",",".");
			$ld_asignado_modificado=number_format($ld_asignado_modificado,2,",",".");
			$ld_programado=number_format($ld_programado,2,",",".");
			$ld_ejecutado=number_format($ld_ejecutado,2,",",".");
			$ld_variacion_absoluta=number_format($ld_variacion_absoluta,2,",",".");
			$ld_variacion_porcentual=number_format($ld_variacion_porcentual,2,",",".");
			$ld_programado_acumulado=number_format($ld_programado_acumulado,2,",",".");
			$ld_ejecutado_acumulado=number_format($ld_ejecutado_acumulado,2,",",".");
			
			$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,
			                   'presupuesto'=>$ld_asignado,'presupuesto_modificado'=>$ld_asignado_modificado,
			                   'programado'=>$ld_programado,'ejecutado'=>$ld_ejecutado,
							   'absoluta'=>$ld_variacion_absoluta,'porcentaje'=>$ld_variacion_porcentual,
							   'programado_acumulado'=>$ld_programado_acumulado,
							   'ejecutado_acumulado'=>$ld_ejecutado_acumulado);
							   
		if($ld_asignado ==  $ld_asignado_modificado)
		{
		 $ld_asignado_modificado = " ";
		}
		
		$lo_hoja->write($li_row, 0, $io_function_report-> uf_formato_cuenta_instructivo(trim($ls_spg_cuenta)),$lo_dataleft);
		$lo_hoja->write($li_row, 1, $ls_denominacion,$lo_dataleft);
		$lo_hoja->write($li_row, 2,$ld_asignado,$lo_dataright);
		$lo_hoja->write($li_row, 3, $ld_asignado_modificado,$lo_dataright);
		$lo_hoja->write($li_row, 4, $ld_programado,$lo_dataright);
		$lo_hoja->write($li_row, 5, $ld_ejecutado,$lo_dataright);
		$lo_hoja->write($li_row, 6, $ld_variacion_absoluta,$lo_dataright);
		$lo_hoja->write($li_row, 7, $ld_variacion_porcentual,$lo_dataright);
		$lo_hoja->write($li_row, 8, $ld_programado_acumulado,$lo_dataright);
		$lo_hoja->write($li_row, 9, $ld_ejecutado_acumulado,$lo_dataright);
		$li_row++;   
							   
		}//for
		$li_total=$io_report->dts_resultado->getRowCount("resultado_ejercicio_asignado");
		for($li=1;$li<=$li_total;$li++)
		{
			$ld_asignado=$io_report->dts_resultado->data["resultado_ejercicio_asignado"][$li];
			$ld_asignado_modificado=$io_report->dts_resultado->data["resultado_ejercicio_asignado_modificado"][$li];
			$ld_programado=$io_report->dts_resultado->data["resultado_ejercicio_programado"][$li];
			$ld_ejecutado=$io_report->dts_resultado->data["resultado_ejercicio_ejecutado"][$li];
			$ld_variacion_absoluta=$io_report->dts_resultado->data["resultado_ejercicio_variacion_absoluta"][$li];
			$ld_variacion_porcentual=$io_report->dts_resultado->data["resultado_ejercicio_variacion_porcentual"][$li];
			$ld_programado_acumulado=$io_report->dts_resultado->data["resultado_ejercicio_programado_acumulado"][$li];
			$ld_ejecutado_acumulado=$io_report->dts_resultado->data["resultado_ejercicio_ejecutado_acumulado"][$li];
			$ld_asignado=number_format($ld_asignado,2,",",".");
			$ld_asignado_modificado=number_format($ld_asignado_modificado,2,",",".");
			$ld_programado=number_format($ld_programado,2,",",".");
			$ld_ejecutado=number_format($ld_ejecutado,2,",",".");
			$ld_variacion_absoluta=number_format($ld_variacion_absoluta,2,",",".");
			$ld_variacion_porcentual=number_format($ld_variacion_porcentual,2,",",".");
			$ld_programado_acumulado=number_format($ld_programado_acumulado,2,",",".");
			$ld_ejecutado_acumulado=number_format($ld_ejecutado_acumulado,2,",",".");
			
			$la_data_resultado[$li]=array('total'=>'Resultado del Ejercicio ( 3 = 1 - 2 )',
			                   'presupuesto'=>$ld_asignado,'presupuesto_modificado'=>$ld_asignado_modificado,
			                   'programado'=>$ld_programado,'ejecutado'=>$ld_ejecutado,
							   'absoluta'=>$ld_variacion_absoluta,'porcentaje'=>$ld_variacion_porcentual,
							   'programado_acumulado'=>$ld_programado_acumulado,
							   'ejecutado_acumulado'=>$ld_ejecutado_acumulado);
		
		if($ld_asignado ==  $ld_asignado_modificado)
		{
		 $ld_asignado_modificado = " ";
		}
		$lo_hoja->write($li_row, 0, "",$lo_titulo);
		$lo_hoja->write($li_row, 1,'Resultado del Ejercicio ( 3 = 1 - 2 )',$lo_titulo);
		$lo_hoja->write($li_row, 2,$ld_asignado,$lo_dataright);
		$lo_hoja->write($li_row, 3, $ld_asignado_modificado,$lo_dataright);
		$lo_hoja->write($li_row, 4, $ld_programado,$lo_dataright);
		$lo_hoja->write($li_row, 5, $ld_ejecutado,$lo_dataright);
		$lo_hoja->write($li_row, 6, $ld_variacion_absoluta,$lo_dataright);
		$lo_hoja->write($li_row, 7, $ld_variacion_porcentual,$lo_dataright);
		$lo_hoja->write($li_row, 8, $ld_programado_acumulado,$lo_dataright);
		$lo_hoja->write($li_row, 9, $ld_ejecutado_acumulado,$lo_dataright);
		$li_row++;
							   
        }
		
		/*
		uf_print_titulo_reporte($io_pdf);
		uf_print_titulo($li_mesdes,$io_pdf);
		uf_print_cabecera($io_pdf);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
        uf_print_resultado($la_data_resultado,$io_pdf);
		unset($la_data);
		unset($la_data_resultado);
		
		
		if($z<$li_total)
		{
		 $io_pdf->ezNewPage(); // Insertar una nueva p?gina
		}
		$io_pdf->ezStopPageNumbers(1,1);
		$io_pdf->ezStream();
		if (isset($d) && $d)
		{
			$ls_pdfcode = $io_pdf->ezOutput(1);
			$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
			echo '<html><body>';
			echo trim($ls_pdfcode);
			echo '</body></html>';
		}
		else
		{
			$io_pdf->ezStream();
		}
		unset($io_pdf);
		*/
	}//else
	
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"ESTADO DE RESULTADO.xls\"");
	header("Content-Disposition: inline; filename=\"ESTADO DE RESULTADO.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
	print("<script language=JavaScript>");
	print(" close();");
	print("</script>");			
	unset($io_report);
	unset($io_funciones);

	unset($io_report);
	unset($io_funciones);
?> 