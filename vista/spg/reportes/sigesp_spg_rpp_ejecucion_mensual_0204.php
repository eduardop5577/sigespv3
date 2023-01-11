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
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_moneda,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->rectangle(10,390,990,200);
		
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,420,16,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(16,'<b>'.$as_moneda.'</b>');
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,400,16,'<b>'.$as_moneda.'</b>'); // Agregar el título
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($io_encabezado,$fechas,$mes,$as_nombre,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
		$io_pdf->ezSetY(590);
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_nombre    = $_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads = $_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona = $_SESSION['la_empresa']['codasiona'];
		$la_data[1]=array('name'=>'<b>CODIGO PRESUPUESTARIO DEL ENTE:     </b>'.'<b>'.$ls_codasiona.'</b>');
		$la_data[2]=array('name'=>'<b>DENOMINACION DEL ENTE:    </b>'.'<b>'.$ls_nombre.'</b>');
                $la_data[3]=array('name'=>'<b>ORGANO DE ADSCRIPCION:    </b>'.'<b>'.$ls_nomorgads.'</b>');
		$la_data[4]=array('name'=>'<b>FECHA:    </b>'.'<b>'.$fechas.'</b>');
                $la_data[5]=array('name'=>'<b>MES:    </b>'.'<b>'.$mes.'</b>');
                $la_data[6]=array('name'=>'<b>CÓDIGO Y DENOMINACION DE LA CATEGORIA PRESUPUESTARIA:    </b>'.'');
		$la_data[7]=array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro1"]).' Desde:    </b>'.$as_nombre["D"]["1"].'   <b> - Hasta:</b> '.$as_nombre["H"]["1"]);
		$la_data[8]=array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro2"]).' Desde:    </b>'.$as_nombre["D"]["2"].'   <b> - Hasta:</b> '.$as_nombre["H"]["2"]);
		$la_data[9]=array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro3"]).' Desde:    </b>'.$as_nombre["D"]["3"].'   <b> - Hasta:</b> '.$as_nombre["H"]["3"]);
		if($_SESSION["la_empresa"]["estmodest"]==2)
		{
                    $la_data[10]=array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro4"]).' Desde:    </b>'.$as_nombre["D"]["4"].'   <b> - Hasta:</b> '.$as_nombre["H"]["4"]);
                    $la_data[11]=array('name'=>'<b>'.strtoupper($_SESSION["la_empresa"]["nomestpro5"]).' Desde:    </b>'.$as_nombre["D"]["5"].'   <b> - Hasta:</b> '.$as_nombre["H"]["5"]);
		}		
                
		$la_columna=array('name'=>'','name'=>'','name'=>'','name'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 8,       // Tamaño de Letras
						 'titleFontSize' => 8, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>465,//65
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($io_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
		$io_pdf->ezSetDy(-5); // para  el rectangulo
		$io_pdf->ezSetY(390);
		$la_data=array(array('name1'=>'',
		                     'name2'=>'<b>EJECUCION MENSUAL</b>',
		                     'name3'=>'',
		                     'name4'=>'<b>ACUMULADO</b>',
		                     'name5'=>''));
		$la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'');
		$la_config =array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>0,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'xPos'=>504,
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'cols'=>array('name1'=>array('justification'=>'center','width'=>374),// Justificación y ancho de la columna
						               'name2'=>array('justification'=>'center','width'=>168),// Justificación y ancho de la columna
						               'name3'=>array('justification'=>'center','width'=>112),// Justificación y ancho de la columna
						               'name4'=>array('justification'=>'center','width'=>168),// Justificación y ancho de la columna
							       'name5'=>array('justification'=>'center','width'=>168))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_titulo,'all');
	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
		$la_data=array(array('cuenta'=>'<b>PARTIDAS-SUBPARTIDAS</b>',
		                     'denominacion'=>'<b>DENOMINACION</b>',
				     'presupuesto'=>'<b>PRESUPUESTO APROBADO</b>',
		                     'modificado_mes'=>'<b>MODIFICACIONES DEL MES</b>',
		                     'presupuesto_modificado'=>'<b>PRESUPUESTO MODIFICADO</b>',
				     'programado'=>'<b>PROGRAMADO MENSUAL</b>',
				     'compromiso'=>'<b>COMPROMISO</b>',
				     'causado'=>'<b>CAUSADO</b>',
   				     'pagado'=>'<b>PAGADO</b>',
				     'variacion'=>'<b>VARIACION ABSOLUTA CAUSADO VS PROGRAMADO</b>',
				     'programado_acumulado'=>'<b>PROGRAMADO ACUMULADO</b>',
				     'compromiso_acumulado'=>'<b>COMPROMISO</b>',
				     'causado_acumulado'=>'<b>CAUSADO</b>',
			             'pagado_acumulado'=>'<b>PAGADO</b>',
				     'variacion_acumulado'=>'<b>VARIACION ABSOLUTA ACUMULADA CAUSADO VS PROGRAMADO</b>',
				     'disponibilidad_compromiso'=>'<b>DISPONIBILIDAD PRESUPUESTARIA COMPROMISO</b>',
				     'disponibilidad_causado'=>'<b>DISPONIBILIDAD PRESUPUESTARIA CAUSADO</b>'));
		$la_columna=array('cuenta'=>'','denominacion'=>'','presupuesto'=>'','modificado_mes'=>'',
		                  'presupuesto_modificado'=>'','programado'=>'','compromiso'=>'','causado'=>'',
				  'pagado'=>'','variacion'=>'','programado_acumulado'=>'','compromiso_acumulado'=>'','causado_acumulado'=>'',
				  'pagado_acumulado'=>'','variacion_acumulado'=>'','disponibilidad_compromiso'=>'','disponibilidad_causado'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 7,       // Tamaño de Letras
						 'titleFontSize' => 7, // Tamaño de Letras de los títulos
						 'showLines'=>2,        // Mostrar Líneas
						 'shaded'=>0,           // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990,
						 'colGap'=>0,
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 	       'denominacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						               'presupuesto'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'modificado_mes'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'presupuesto_modificado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_causado'=>array('justification'=>'center','width'=>56))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 	       'denominacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						               'presupuesto'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'modificado_mes'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'presupuesto_modificado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_causado'=>array('justification'=>'center','width'=>56))); // Justificación y ancho de la columna
		$la_columnas=array('cuenta'=>'','denominacion'=>'','presupuesto'=>'','modificado_mes'=>'',
		                  'presupuesto_modificado'=>'','programado'=>'','compromiso'=>'','causado'=>'',
				  'pagado'=>'','variacion'=>'','programado_acumulado'=>'','compromiso_acumulado'=>'','causado_acumulado'=>'',
				  'pagado_acumulado'=>'','variacion_acumulado'=>'','disponibilidad_compromiso'=>'','disponibilidad_causado'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>150),// Justificación y ancho de la columna
						               'presupuesto'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'modificado_mes'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'presupuesto_modificado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'programado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'compromiso_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'causado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'pagado_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'variacion_acumulado'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_compromiso'=>array('justification'=>'center','width'=>56), // Justificación y ancho de la columna
                                                               'disponibilidad_causado'=>array('justification'=>'center','width'=>56))); // Justificación y ancho de la columna
		$la_columnas=array('total'=>'','presupuesto'=>'','modificado_mes'=>'',
		                  'presupuesto_modificado'=>'','programado'=>'','compromiso'=>'','causado'=>'',
				  'pagado'=>'','variacion'=>'','programado_acumulado'=>'','compromiso_acumulado'=>'','causado_acumulado'=>'',
				  'pagado_acumulado'=>'','variacion_acumulado'=>'','disponibilidad_compromiso'=>'','disponibilidad_causado'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones = new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();	
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("sigesp_spg_class_reportes_instructivos.php");
		$io_report = new sigesp_spg_class_reportes_instructivos();
	//-----------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro1_aux=$ls_codestpro1_min;		
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro4_min  = $_GET["codestpro4"];
		$ls_codestpro5_min  = $_GET["codestpro5"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
		$ls_codestpro4h_max = $_GET["codestpro4h"];
		$ls_codestpro5h_max = $_GET["codestpro5h"];
		$ls_estclades       = $_GET["estclades"];
	        $ls_estclahas       = $_GET["estclahas"];
		$ls_tipoformato=1;
		if($li_estmodest==1)
		{
			$ls_codestpro4_min = "0000000000000000000000000";
			$ls_codestpro5_min = "0000000000000000000000000";
			$ls_codestpro4h_max = "0000000000000000000000000";
			$ls_codestpro5h_max = "0000000000000000000000000";
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
			{
				$arrResultado=$io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
			                                                                 			  $ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);				
				$ls_codestpro1_min=$arrResultado['as_codestpro1'];
				$ls_codestpro2_min=$arrResultado['as_codestpro2'];
				$ls_codestpro3_min=$arrResultado['as_codestpro3'];
				$ls_codestpro4_min=$arrResultado['as_codestpro4'];
				$ls_codestpro5_min=$arrResultado['as_codestpro5'];
				$ls_estclades=$arrResultado['as_estclades'];
				$lb_valido= $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max==""))
			{
				$arrResultado=$io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas);
				$ls_codestpro1h_max=$arrResultado['as_codestpro1'];
				$ls_codestpro2h_max=$arrResultado['as_codestpro2'];
				$ls_codestpro3h_max=$arrResultado['as_codestpro3'];
				$ls_codestpro4h_max=$arrResultado['as_codestpro4'];
				$ls_codestpro5h_max=$arrResultado['as_codestpro5'];
				$ls_estclahas=$arrResultado['as_estclahas'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			  }
			}
			else
			{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}
		elseif($li_estmodest==2)
		{   
		    $ls_codestpro4_min = $_GET["codestpro4"];
			$ls_codestpro5_min = $_GET["codestpro5"];
			$ls_codestpro4h_max = $_GET["codestpro4h"];
			$ls_codestpro5h_max = $_GET["codestpro5h"];
			
			
			if(($ls_codestpro1_min=='**') ||($ls_codestpro1_min==''))
			{
				$ls_codestpro1_min='';
			}
			else
			{
			    $ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
			}
			if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min==''))
			{
				$ls_codestpro2_min='';
			}
			else
			{
				$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
			
			}
			if(($ls_codestpro3_min=='**')||($ls_codestpro3_min==''))
			{
				$ls_codestpro3_min='';
			}
			else
			{
			
				$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
			}
			if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min==''))
			{
				$ls_codestpro4_min='';
			}
			else
			{
				$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	
			
			}
			if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min==''))
			{
				$ls_codestpro5_min='';
			}else
			{
					$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			}
			
			
			if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max==''))
			{
				$ls_codestpro1h_max='';
			}
			else
			{
				$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
			}
			if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max==''))
			{
				$ls_codestpro2h_max='';
			}else
			{
				$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
			}
			if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max==''))
			{
				$ls_codestpro3h_max='';
			}else
			{
				$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
			}
			if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max==''))
			{
				$ls_codestpro4h_max='';
			}else
			{
				$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
			}
			if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max==''))
			{
				$ls_codestpro5h_max='';
			}else
			{
				$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
			}
			
			if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min==""))
			{
				$arrResultado=$io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
			                                                                 			  $ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);				
				$ls_codestpro1_min=$arrResultado['as_codestpro1'];
				$ls_codestpro2_min=$arrResultado['as_codestpro2'];
				$ls_codestpro3_min=$arrResultado['as_codestpro3'];
				$ls_codestpro4_min=$arrResultado['as_codestpro4'];
				$ls_codestpro5_min=$arrResultado['as_codestpro5'];
				$ls_estclades=$arrResultado['as_estclades'];
				$lb_valido= $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			  }
			}
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max==""))
			{
				$arrResultado=$io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas);
				$ls_codestpro1h_max=$arrResultado['as_codestpro1'];
				$ls_codestpro2h_max=$arrResultado['as_codestpro2'];
				$ls_codestpro3h_max=$arrResultado['as_codestpro3'];
				$ls_codestpro4h_max=$arrResultado['as_codestpro4'];
				$ls_codestpro5h_max=$arrResultado['as_codestpro5'];
				$ls_estclahas=$arrResultado['as_estclahas'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
				  }
			}
			else
			{
				$ls_codestpro1h  = $ls_codestpro1h_max;
				$ls_codestpro2h  = $ls_codestpro2h_max;
				$ls_codestpro3h  = $ls_codestpro3h_max;
				$ls_codestpro4h  = $ls_codestpro4h_max;
				$ls_codestpro5h  = $ls_codestpro5h_max;
			}
			}	
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
                $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
                $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
                $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
                $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
                $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
                
		$as_nombre["D"]["1"]="";
		$as_nombre["D"]["2"]="";
		$as_nombre["D"]["3"]="";
		$as_nombre["H"]["1"]="";
		$as_nombre["H"]["2"]="";
		$as_nombre["H"]["3"]="";
		$as_denestpro1="";
		$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$as_denestpro1,$ls_estclades);
		$as_denestpro1=$arrResultado['as_denestpro1'];
		$lb_valido=$arrResultado['lb_valido'];
		$as_nombre["D"]["1"]=substr($ls_codestpro1,-$ls_loncodestpro1)." ".$as_denestpro1;
		if($ls_codestpro1h!="")
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1h,$as_denestpro1,$ls_estclahas);
			$as_denestpro1=$arrResultado['as_denestpro1'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["1"]=substr($ls_codestpro1h,-$ls_loncodestpro1)." ".$as_denestpro1;
		}
		if($ls_codestpro2!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["2"]=substr($ls_codestpro2,-$ls_loncodestpro2)." ".$as_denestpro;
		}
		if($ls_codestpro2h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1h,$ls_codestpro2h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["2"]=substr($ls_codestpro2h,-$ls_loncodestpro2)." ".$as_denestpro;
		}
		if($ls_codestpro3!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["3"]=substr($ls_codestpro3,-$ls_loncodestpro3)." ".$as_denestpro;
		}
		if($ls_codestpro3h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["3"]=substr($ls_codestpro3h,-$ls_loncodestpro3)." ".$as_denestpro;
		}
		if($li_estmodest==2)
		{
			$as_nombre["D"]["4"]="";
			$as_nombre["D"]["5"]="";
			$as_nombre["H"]["4"]="";
			$as_nombre["H"]["5"]="";
			if($ls_codestpro4!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$as_denestpro,$ls_estclades);
				$as_denestpro=$arrResultado['as_denestpro4'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["D"]["4"]=substr($ls_codestpro4,-$ls_loncodestpro4)." ".$as_denestpro;
			}
			if($ls_codestpro4h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro4'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["4"]=substr($ls_codestpro4h,-$ls_loncodestpro4)." ".$as_denestpro;
			}
			if($ls_codestpro5!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$as_denestpro,$ls_estclades);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["D"]["5"]=substr($ls_codestpro5,-$ls_loncodestpro5)." ".$as_denestpro;
			}
			if($ls_codestpro5h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["5"]=substr($ls_codestpro5h,-$ls_loncodestpro5)." ".$as_denestpro;
			}
		}
                
		$li_mes=$_GET["cmbmes"];
		$ldt_ult_dia=$io_fecha->uf_last_day($li_mes,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
                $ldt_fecdes=$li_ano."-".$li_mes."-01";
		$ls_mes=$io_fecha->uf_load_nombre_mes($li_mes);
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo="<b>EJECUCION MENSUAL DEL PRESUPUESTO DE EGRESOS POR PROYECTOS, ACCIONES CENTRALIZADAS E INSTITUCIONAL</b>";       
	//--------------------------------------------------------------------------------------------------------------------------------
       
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spg_reporte_de_ejecucion_mensual_recursos($ls_codestpro1,$ls_codestpro2,
	                                                             $ls_codestpro3,$ls_codestpro4,
																 $ls_codestpro5,$ls_codestpro1h,
																 $ls_codestpro2h,$ls_codestpro3h,
															     $ls_codestpro4h,$ls_codestpro5h,
																 $ldt_fecdes,$ldt_fechas,
																 $ls_codfuefindes,$ls_codfuefinhas,
																 $ls_estclades,$ls_estclahas);
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	    //
	    //set_time_limit(1800);
	    $io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
            $io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	    uf_print_encabezado_pagina($ls_titulo,'(Bolivares)',$io_pdf); // Imprimimos el encabezado de la página
 	    $io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
	    $li_total=$io_report->dts_reporte->getRowCount("spg_cuenta");
            $ld_asignado_total=0;
            $ld_modificacion_mes_total=0;
            $ld_presupuesto_modificado_total=0;
            $ld_programado_total=0;
            $ld_compromiso_total=0;
            $ld_causado_total=0;
            $ld_pagado_total=0;
            $ld_variacion_total=0;
            $ld_programado_acumulado_total=0;
            $ld_compromiso_acum_total=0;
            $ld_causado_acum_total=0;
            $ld_pagado_acum_total=0;
	    $ld_disponible_total=0;
	    $ld_disponible_causado_total=0;
            $ld_variacion_acum_total=0;
	    for($z=1;$z<=$li_total;$z++)
	    {
			  $thisPageNum=$io_pdf->ezPageCount;
                          $ls_status=trim($io_report->dts_reporte->data["status"][$z]);
			  $ls_spg_cuenta=trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
			  $ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			  $ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
			  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
			  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
			  $ld_programado=$io_report->dts_reporte->data["programado"][$z];
			  $ld_compromiso=$io_report->dts_reporte->data["compromiso"][$z];
			  $ld_causado=$io_report->dts_reporte->data["causado"][$z];
			  $ld_pagado=$io_report->dts_reporte->data["pagado"][$z];
			  $ld_programado_acum=$io_report->dts_reporte->data["programado_acum"][$z];
			  $ld_compromiso_acum=$io_report->dts_reporte->data["compromiso_acum"][$z];
			  $ld_causado_acum=$io_report->dts_reporte->data["causado_acum"][$z];
			  $ld_pagado_acum=$io_report->dts_reporte->data["pagado_acum"][$z];
			  $ld_aumento_acum=$io_report->dts_reporte->data["aumento_acum"][$z];
			  $ld_disminucion_acum=$io_report->dts_reporte->data["disminucion_acum"][$z];
			  $ld_disponible=$io_report->dts_reporte->data["disponible_fecha"][$z];
			  $ld_disponible_causado=$io_report->dts_reporte->data["disponible_causado"][$z];
                          
                          $ld_programado=$ld_programado+$ld_aumento-$ld_disminucion;
                          $ld_programado_acum=$ld_programado_acum+$ld_aumento_acum-$ld_disminucion_acum;
                          if ($ls_status == 'C')
                          {
                            $ld_asignado_total=$ld_asignado_total+$ld_asignado;
                            $ld_modificacion_mes_total=$ld_modificacion_mes_total+$ld_aumento-$ld_disminucion;
                            $ld_presupuesto_modificado_total=$ld_presupuesto_modificado_total+$ld_asignado+$ld_aumento_acum-$ld_disminucion_acum;
                            $ld_programado_total=$ld_programado_total+$ld_programado;
                            $ld_compromiso_total=$ld_compromiso_total+$ld_compromiso;
                            $ld_causado_total=$ld_causado_total+$ld_causado;
                            $ld_pagado_total=$ld_pagado_total+$ld_pagado;
                            $ld_variacion_total=$ld_variacion_total+$ld_causado-$ld_programado;
                            $ld_programado_acumulado_total=$ld_programado_acumulado_total+$ld_programado_acum;
                            $ld_compromiso_acum_total=$ld_compromiso_acum_total+$ld_compromiso_acum;
                            $ld_causado_acum_total=$ld_causado_acum_total+$ld_causado_acum;
                            $ld_pagado_acum_total=$ld_pagado_acum_total+$ld_pagado_acum;
                            $ld_variacion_acum_total=$ld_variacion_acum_total+$ld_causado_acum-$ld_programado_acum;
                            $ld_disponible_total=$ld_disponible_total+$ld_disponible;
                            $ld_disponible_causado_total=$ld_disponible_causado_total+$ld_disponible_causado;
                          }
			  $ld_presupuesto_modificado=number_format($ld_asignado+$ld_aumento_acum-$ld_disminucion_acum,2,",",".");
                          $ld_variacion=number_format(($ld_causado-$ld_programado),2,",",".");
                          $ld_variacion_acum=number_format($ld_causado_acum-$ld_programado_acum,2,",",".");
			  $ld_asignado=number_format($ld_asignado,2,",",".");
                          $ld_modificacion_mes=number_format(($ld_aumento-$ld_disminucion),2,",",".");
			  $ld_programado=number_format(($ld_programado),2,",",".");
			  $ld_compromiso=number_format($ld_compromiso,2,",",".");
			  $ld_causado=number_format($ld_causado,2,",",".");
			  $ld_pagado=number_format($ld_pagado,2,",",".");
			  $ld_programado_acum=number_format($ld_programado_acum,2,",",".");
			  $ld_compromiso_acum=number_format($ld_compromiso_acum,2,",",".");
			  $ld_causado_acum=number_format($ld_causado_acum,2,",",".");
			  $ld_pagado_acum=number_format($ld_pagado_acum,2,",",".");
			  $ld_disponible=number_format($ld_disponible,2,",",".");
			  $ld_disponible_causado=number_format($ld_disponible_causado,2,",",".");
                          
			  $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,
			                     'denominacion'=>$ls_denominacion,
			                     'presupuesto'=>$ld_asignado,
			                     'modificado_mes'=>$ld_modificacion_mes,
			                     'presupuesto_modificado'=>$ld_presupuesto_modificado,
                                             'programado'=>$ld_programado,
					     'compromiso'=>$ld_compromiso,
					     'causado'=>$ld_causado,
					     'pagado'=>$ld_pagado,
                                             'variacion'=>$ld_variacion,
					     'programado_acumulado'=>$ld_programado_acum,
					     'compromiso_acumulado'=>$ld_compromiso_acum,
					     'causado_acumulado'=>$ld_causado_acum,
				             'pagado_acumulado'=>$ld_pagado_acum,
				             'variacion_acumulado'=>$ld_variacion_acum,
				             'disponibilidad_compromiso'=>$ld_disponible,
					     'disponibilidad_causado'=>$ld_disponible_causado);
		}
		
            $ld_asignado_total=number_format($ld_asignado_total,2,",",".");
            $ld_modificacion_mes_total=number_format($ld_modificacion_mes_total,2,",",".");
            $ld_presupuesto_modificado_total=number_format($ld_presupuesto_modificado_total,2,",",".");
            $ld_programado_total=number_format($ld_programado_total,2,",",".");
            $ld_compromiso_total=number_format($ld_compromiso_total,2,",",".");
            $ld_causado_total=number_format($ld_causado_total,2,",",".");
            $ld_pagado_total=number_format($ld_pagado_total,2,",",".");
            $ld_variacion_total=number_format($ld_variacion_total,2,",",".");
            $ld_programado_acumulado_total=number_format($ld_programado_acumulado_total,2,",",".");
            $ld_compromiso_acum_total=number_format($ld_compromiso_acum_total,2,",",".");
            $ld_causado_acum_total=number_format($ld_causado_acum_total,2,",",".");
            $ld_pagado_acum_total=number_format($ld_pagado_acum_total,2,",",".");
            $ld_variacion_acum_total=number_format($ld_variacion_acum_total,2,",",".");
            $ld_disponible_total=number_format($ld_disponible_total,2,",",".");
            $ld_disponible_causado_total=number_format($ld_disponible_causado_total,2,",",".");

	    $la_data_totales[$z]=array('total'=>'<b>TOTALES Bs.</b>',
		                           'presupuesto'=>$ld_asignado_total,
		                           'modificado_mes'=>$ld_modificacion_mes_total,
		                           'presupuesto_modificado'=>$ld_presupuesto_modificado_total,
					   'programado'=>$ld_programado_total,
				           'compromiso'=>$ld_compromiso_total,
				           'causado'=>$ld_causado_total,
					   'pagado'=>$ld_pagado_total,
                                           'variacion'=>$ld_variacion_total,
					   'programado_acumulado'=>$ld_programado_acumulado_total,
					   'compromiso_acumulado'=>$ld_compromiso_acum_total,
					   'causado_acumulado'=>$ld_causado_acum_total,
					   'pagado_acumulado'=>$ld_pagado_acum_total,
					   'variacion_acumulado'=>$ld_variacion_acum_total,
					   'disponibilidad_compromiso'=>$ld_disponible_total,
					   'disponibilidad_causado'=>$ld_disponible_causado_total);
							   
		$io_encabezado=$io_pdf->openObject();
		uf_print_titulo_reporte($io_encabezado,$fechas,$ls_mes,$as_nombre,$io_pdf);
		$io_titulo=$io_pdf->openObject();
		uf_print_titulo($io_titulo,$io_pdf);
		$io_cabecera=$io_pdf->openObject();
		uf_print_cabecera($io_cabecera,$io_pdf);
		$io_pdf->ezSetCmMargins(9.83,3,3,3);
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		uf_print_pie_cabecera($la_data_totales,$io_pdf);
		$io_pdf->stopObject($io_encabezado);
		$io_pdf->stopObject($io_titulo);
		$io_pdf->stopObject($io_cabecera);
		unset($la_data);
		unset($la_data_totales);
		$io_pdf->ezStopPageNumbers(1,1);
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
	}//else
	unset($io_report);
	unset($io_funciones);
?> 