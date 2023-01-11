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
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_sep->uf_load_seguridad_reporte("SPG","sigesp_spg_reporte_precompromisos_nocomprometidos.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 22/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,720,10,$as_fecha); // Agregar el título
		$io_pdf->addText(500,740,9,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,720,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 13/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-20);
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'comprobante'=>'<b>Comprobante</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'monto'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'comprobante'=>array('justification'=>'left','width'=>170), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'left','width'=>90), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'left','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_total,$ai_totrows,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_total // Total por personal
		//	   			   ai_totrows // Total por patrón
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno /Ing. Luis Lang
		// Fecha Creación: 13/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'<b>Total Solicitudes</b>','totrows'=>$ai_totrows,'total'=>$ai_total));
		$la_columna=array('name'=>'','totrows'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'right','width'=>390), // Justificación y ancho de la columna
						 			   'totrows'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_estructura($la_codestproD,$la_denestproD,$la_codestproH,$la_denestproH,$io_pdf)
	{
		//$io_pdf->ezSetY(460);
		$la_data[1]=array('titulo1'=>'<b> Estructura Desde</b>', 'titulo2'=>'<b> Estructura Hasta</b>');
		$la_columnas=array('titulo1'=>'', 'titulo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
				'fontSize' => 8, // Tamaño de Letras
				'titleFontSize' => 12,  // Tamaño de Letras de los títulos
				'showLines'=>1, // Mostrar Líneas
				'shaded'=>2, // Sombra entre líneas
				'width'=>590, // Ancho de la tabla
				'maxWidth'=>590, // Ancho Máximo de la tabla
				'xOrientation'=>'center', // Orientación de la tabla
				'outerLineThickness'=>0.5,
				'innerLineThickness' =>0.5,
				'cols'=>array('titulo1'=>array('justification'=>'center','width'=>295),
							  'titulo2'=>array('justification'=>'center','width'=>295))); // Justificación y ancho de la columna
				$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
				unset($la_data);
				unset($la_columnas);
				unset($la_config);
	
				$ls_estmodest  = $_SESSION["la_empresa"]["estmodest"];
				$li_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
				$li_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
				$li_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
				$li_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
				$li_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
				$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
				$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
				$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
				$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
				$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	
				$ls_codestpro1    = trim(substr($la_codestproD["codestpro1"],-$li_loncodestpro1));
				$ls_codestpro2    = trim(substr($la_codestproD["codestpro2"],-$li_loncodestpro2));
				$ls_codestpro3    = trim(substr($la_codestproD["codestpro3"],-$li_loncodestpro3));
				$ls_codestpro4    = trim(substr($la_codestproD["codestpro4"],-$li_loncodestpro4));
				$ls_codestpro5    = trim(substr($la_codestproD["codestpro5"],-$li_loncodestpro5));
	
				$ls_codestpro1H    = trim(substr($la_codestproH["codestpro1"],-$li_loncodestpro1));
				$ls_codestpro2H    = trim(substr($la_codestproH["codestpro2"],-$li_loncodestpro2));
				$ls_codestpro3H    = trim(substr($la_codestproH["codestpro3"],-$li_loncodestpro3));
				$ls_codestpro4H    = trim(substr($la_codestproH["codestpro4"],-$li_loncodestpro4));
				$ls_codestpro5H    = trim(substr($la_codestproH["codestpro5"],-$li_loncodestpro5));
	
	
				if ($ls_estmodest==1) {
					$ls_datat1[1]=array('nombre1'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro1'=>$ls_codestpro1,'denom1'=>$la_denestproD["denestpro1"],
							'nombre2'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro2'=>$ls_codestpro1H,'denom2'=>$la_denestproH["denestpro1"]);
					$ls_datat1[2]=array('nombre1'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro1'=>$ls_codestpro2,'denom1'=>$la_denestproD["denestpro2"],
							'nombre2'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro2'=>$ls_codestpro2H,'denom2'=>$la_denestproH["denestpro2"]);
					$ls_datat1[3]=array('nombre1'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro1'=>$ls_codestpro3,'denom1'=>$la_denestproD["denestpro3"],
							'nombre2'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro2'=>$ls_codestpro3H,'denom2'=>$la_denestproH["denestpro3"]);
	
						
				}
				else {
					$ls_datat1[1]=array('nombre1'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro1'=>$ls_codestpro1,'denom1'=>$la_denestproD["denestpro1"],
					                    'nombre2'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro2'=>$ls_codestpro1H,'denom2'=>$la_denestproH["denestpro1"]);
					$ls_datat1[2]=array('nombre1'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro1'=>$ls_codestpro2,'denom1'=>$la_denestproD["denestpro2"],
					                    'nombre2'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro2'=>$ls_codestpro2H,'denom2'=>$la_denestproH["denestpro2"]);
					$ls_datat1[3]=array('nombre1'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro1'=>$ls_codestpro3,'denom1'=>$la_denestproD["denestpro3"],
					                    'nombre2'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro2'=>$ls_codestpro3H,'denom2'=>$la_denestproH["denestpro3"]);
					$ls_datat1[4]=array('nombre1'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro1'=>$ls_codestpro4,'denom1'=>$la_denestproD["denestpro4"],
					                    'nombre2'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro2'=>$ls_codestpro4H,'denom2'=>$la_denestproH["denestpro4"]);
					$ls_datat1[5]=array('nombre1'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro1'=>$ls_codestpro5,'denom1'=>$la_denestproH["denestpro5"],
					                    'nombre2'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro2'=>$ls_codestpro5H,'denom2'=>$la_denestproH["denestpro5"]);
				}
	
				$la_config=array('showHeadings'=>0, // Mostrar encabezados
						'fontSize' =>7, // Tamaño de Letras
						'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						'showLines'=>0, // Mostrar Líneas
						'shaded'=>0, // Sombra entre líneas
						'colGap'=>1, // separacion entre tablas
						'width'=>580, // Ancho de la tabla
						'maxWidth'=>580, // Ancho Máximo de la tabla
						'xOrientation'=>'center', // Orientación de la tabla
						'cols'=>array('nombre1'=>array('justification'=>'left','width'=>50),
								'codestpro1'=>array('justification'=>'right','width'=>60),
								'denom1'=>array('justification'=>'left','width'=>190),
								'nombre2'=>array('justification'=>'left','width'=>50),
								'codestpro2'=>array('justification'=>'right','width'=>60),
								'denom2'=>array('justification'=>'left','width'=>190)));
				$la_columna=array('nombre1'=>'','codestpro1'=>'','denom1'=>'','nombre2'=>'','codestpro2'=>'','denom2'=>'');
				$io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);
				unset($ls_datat1);
				unset($la_config);
				unset($la_columna);
	
				return $io_pdf;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("sigesp_spg_funciones_reportes.php");
	require_once("sigesp_spg_reportes_class.php");
	$io_funciones=new class_funciones();
	$io_function_report = new sigesp_spg_funciones_reportes();
	$io_report = new sigesp_spg_reportes_class();
	require_once("../../../modelo/servicio/spg/sigesp_srv_spg_utilidadreporte.php");
	$io_utilidad = new ServicioUtilidadReporte();
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ldt_fecdes   = $_GET["txtfecdes"];
	$ldt_fechas   = $_GET["txtfechas"];	
	$ls_fechades  = $io_funciones->uf_convertirfecmostrar($ldt_fecdes);
	$ls_fechahas  = $io_funciones->uf_convertirfecmostrar($ldt_fechas);
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	
	//FILTRO POR ESTRUCTURA
	$ls_codestpro1      = $_GET["codestpro1"];
	$ls_codestpro2      = $_GET["codestpro2"];
	$ls_codestpro3      = $_GET["codestpro3"];
	$ls_codestpro4      = $_GET["codestpro4"];
	$ls_codestpro5      = $_GET["codestpro5"];
	$ls_estclades       = $_GET["estclades"];
	$ls_codestpro1h     = $_GET["codestpro1h"];
	$ls_codestpro2h     = $_GET["codestpro2h"];
	$ls_codestpro3h     = $_GET["codestpro3h"];
	$ls_codestpro4h     = $_GET["codestpro4h"];
	$ls_codestpro5h     = $_GET["codestpro5h"];
	$ls_estclahas       = $_GET["estclahas"];
	
	if($li_estmodest==1) {
		if ($ls_codestpro1 != "0000000000000000000000000") {
			$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;
			$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h;
		}
	}
	elseif($li_estmodest==2) {
		if ($ls_codestpro1 != "0000000000000000000000000") {
			$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
			$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h."-".$ls_codestpro4h."-".$ls_codestpro5h;
		}
	}
	
	$ls_codestproD = '';
	if ($ls_codestpro1 != "0000000000000000000000000") {
		$ls_codestproD = str_pad($ls_codestpro1,25,0,0).str_pad($ls_codestpro2,25,0,0).str_pad($ls_codestpro3,25,0,0).str_pad($ls_codestpro4,25,0,0).str_pad($ls_codestpro5,25,0,0).$ls_estclades;
	}
	
	$ls_codestproH = '';
	if ($ls_codestpro1h != "0000000000000000000000000") {
		$ls_codestproH = str_pad($ls_codestpro1h,25,0,0).str_pad($ls_codestpro2h,25,0,0).str_pad($ls_codestpro3h,25,0,0).str_pad($ls_codestpro4h,25,0,0).str_pad($ls_codestpro5h,25,0,0).$ls_estclahas;
	}
	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>PRE - COMPROMISOS</b> "; 
	$ls_fecha="<b> DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas." </b>";
		
	/////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte  Precompromisos ".$ldt_fecdes."  hasta ".$ldt_fechas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_precompromisos_nocomprometidos.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////////
	
	$la_data = $io_report->uf_spg_precompromisos_nocomprometidos($ldt_fecdes, $ldt_fechas, $ls_codestproD, $ls_codestproH);
	if(empty($la_data)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		if ($ls_codestpro1 != "0000000000000000000000000" && $ls_codestpro1h != "0000000000000000000000000") {
			$la_codestproD["codestpro1"] = $ls_codestpro1;
			$la_codestproD["codestpro2"] = $ls_codestpro2;
			$la_codestproD["codestpro3"] = $ls_codestpro3;
			$la_codestproD["codestpro4"] = $ls_codestpro4;
			$la_codestproD["codestpro5"] = $ls_codestpro5;
			$la_denestproD["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro1, 1, $ls_estclades);
			$la_denestproD["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro2, 2, $ls_estclades, $ls_codestpro1);
			$la_denestproD["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro3, 3, $ls_estclades, $ls_codestpro1, $ls_codestpro2);
			$la_denestproD["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro4, 4, $ls_estclades, $ls_codestpro1, $ls_codestpro2, $ls_codestpro3);
			$la_denestproD["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro5, 5, $ls_estclades, $ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4);
				
			$la_codestproH["codestpro1"] = $ls_codestpro1h;
			$la_codestproH["codestpro2"] = $ls_codestpro2h;
			$la_codestproH["codestpro3"] = $ls_codestpro3h;
			$la_codestproH["codestpro4"] = $ls_codestpro4h;
			$la_codestproH["codestpro5"] = $ls_codestpro5h;
			$la_denestproH["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro1h, 1, $ls_estclahas);
			$la_denestproH["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro2h, 2, $ls_estclahas, $ls_codestpro1h);
			$la_denestproH["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro3h, 3, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h);
			$la_denestproH["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro4h, 4, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h, $ls_codestpro3h);
			$la_denestproH["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro5h, 5, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h, $ls_codestpro3h, $ls_codestpro4h);
			uf_print_cabecera_estructura($la_codestproD,$la_denestproD,$la_codestproH,$la_denestproH,$io_pdf);
		}
		uf_print_detalle($la_data,$io_pdf);
		unset($la_data);
		$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
		$io_pdf->ezStream(); // Mostramos el reporte
	}
	

?>
