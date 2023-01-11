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
function uf_print_encabezado_pagina($as_titulo,$io_pdf)
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
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,520,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
	$tm=330-($li_tm/2);
	$io_pdf->addText($tm,530,10,$as_titulo); // Agregar el título

	$io_pdf->addText(600,540,9,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
	$io_pdf->addText(600,530,9,date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(600,520,9,date("h:i a")); // Agregar la hora
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_cabecera_cuenta($as_spgcuenta,$as_descripcion,$io_pdf)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera
	//		   Access: privates
	//	    Arguments: as_programatica // programatica del comprobante
	//	    		   as_denestpro5 // denominacion de la programatica del comprobante
	//	    		   io_pdf // Objeto PDF
	//    Description: función que imprime la cabecera de cada página
	//	   Creado Por: Ing.Yozelin Barragán
	// Fecha Creación: 22/09/2006
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_pdf->ezSetDy(-15);
	$la_data=array(array('name'=>'<b>CUENTA:</b> '.$as_spgcuenta.'      '.$as_descripcion));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>735); // Ancho Máximo de la tabla
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->ezSetDy(-10);
}// end function uf_print_cabecera
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle_ejecucion($la_data,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//	   			   io_pdf // Objeto PDF
	//    Description: función que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barragán
	// Fecha Creación: 22/09/2006
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

	$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>735, // Ancho de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('programatica'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la 
						               'descripcion'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la 
						 			   'montoactualizado'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la 
						 			   'precomprometido'=>array('justification'=>'right','width'=>70), // Justificación
						 			   'comprometido'=>array('justification'=>'right','width'=>70), // Justificación
									   'porcomprometido'=>array('justification'=>'right','width'=>35), // Justificación 
						 			   'causado'=>array('justification'=>'right','width'=>70),
									   'porcausado'=>array('justification'=>'right','width'=>35),
									   'pagado'=>array('justification'=>'right','width'=>70),
									   'porpagado'=>array('justification'=>'right','width'=>35))); // Justificación y ancho de la 
	$la_columnas=array('programatica'=>'<b>Programatica</b>',
		               'descripcion'=>'<b>Descripcion</b>',
					   'montoactualizado'=>'<b>Monto Actualizado</b>',
					   'precomprometido'=>'<b>Pre Comprometido</b>',
					   'comprometido'=>'<b>Comprometido</b>',
					   'porcomprometido'=>'<b>Pct.</b>',	
					   'causado'=>'<b>Causado</b>',
					   'porcausado'=>'<b>Pct.</b>',
					   'pagado'=>'<b>Pagado</b>',
					   'porpagado'=>'<b>Pct.</b>');
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_pie_cabecera($ad_totalcomprometer,$ad_totalcausado,$ad_totalpagado,$io_pdf,$as_titulo)
{
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function : uf_print_pie_cabecera
	//		    Acess : private
	//	    Arguments : ad_total // Total General
	//    Description : función que imprime el fin de la cabecera de cada página
	//	   Creado Por: Ing.Yozelin Barragán
	// Fecha Creación: 22/09/2006
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$la_datat=array(array('name'=>'______________________________________________________________________________________________________________'));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'width'=>560); // Ancho Máximo de la tabla
	$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);

	$la_data[]=array('cuenta'=>' ','comprobante'=>'','fecha'=>'<b>'.$as_titulo.'</b> ',
		                 'comprometido'=>number_format($ad_totalcomprometer,2,',','.'),
		                 'causado'=>number_format($ad_totalcausado,2,',','.'),
						 'pagado'=>number_format($ad_totalpagado,2,',','.'));
	$la_columnas=array('cuenta'=>' ','comprobante'=>'','fecha'=>'','comprometido'=>'','causado'=>'','pagado'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la 
						               'comprobante'=>array('justification'=>'center','width'=>150), // Justificación y ancho de  
						 			   'fecha'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						 			   'comprometido'=>array('justification'=>'right','width'=>80), // Justificación 
						 			   'causado'=>array('justification'=>'right','width'=>80),
									   'pagado'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la 
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	$la_data=array(array('name'=>''));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>630, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
}// end function uf_print_pie_cabecera

//--------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("sigesp_spg_funciones_reportes.php");
require_once("sigesp_spg_class_ejecucionpresupuestaria.php");

$io_report          = new sigesp_spg_class_ejecucionpresupuestaria();
$io_function_report = new sigesp_spg_funciones_reportes();
$io_funciones       = new class_funciones();
$io_fecha           = new class_fecha();
//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
$li_ano             = substr($ldt_periodo,0,4);
$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
$ls_codestpro1_min  = $_GET["codestpro1"];
$ls_codestpro2_min  = $_GET["codestpro2"];
$ls_codestpro3_min  = $_GET["codestpro3"];
$ls_codestpro1h_max = $_GET["codestpro1h"];
$ls_codestpro2h_max = $_GET["codestpro2h"];
$ls_codestpro3h_max = $_GET["codestpro3h"];
$ls_estclades       = $_GET["estclades"];
$ls_estclahas       = $_GET["estclahas"];
$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
$ls_text_periodo 	= $_GET["tperiodo"];
$ld_periodo      	= $_GET["periodo"];
$ld_tipper       	= $_GET["tipper"];
$ld_fecinirep		= "";
$ld_fecfinrep 		= "";


switch($ld_tipper){
	case 1:
		$ld_per01 		= intval($ld_periodo);
		$ld_per02 		= "";
		$ld_per03 		= "";
		$ls_desper 		= "MENSUAL";
		$ld_fecinirep	= $li_ano."/".$ld_periodo."/"."01";
		$ld_fecfinrep	= $io_fecha->uf_last_day($ld_periodo,$li_ano);
		$ld_fecfinrep	= $li_ano."/".substr($ld_periodo,0,2)."/".substr($ld_fecfinrep,0,2);
		$ld_anterior 	= 1;
		break;

	case 2:
		$ld_per01 		= intval(substr($ld_periodo,0,2));
		$ld_per02 		= intval(substr($ld_periodo,2,2));
		$ls_desper 		= "BIMENSUAL";
		$ld_fecinirep		= $li_ano."/".substr($ld_periodo,0,2)."/"."01";
		$ld_fecfinrep		= $io_fecha->uf_last_day(substr($ld_periodo,2,2),$li_ano);
		$ld_fecfinrep		= $li_ano."/".substr($ld_periodo,2,2)."/".substr($ld_fecfinrep,0,2);
		$ld_per03 		= "";
		$ld_anterior 		= 2;
		break;

	case 3:
		$ld_per01 		= intval(substr($ld_periodo,0,2));
		$ld_per02 		= intval(substr($ld_periodo,2,2));
		$ld_per03 		= intval(substr($ld_periodo,4,2));
		$ls_desper 		= "TRIMESTRAL";
		$ld_fecinirep		= $li_ano."/".substr($ld_periodo,0,2)."/"."01";
		$ld_fecfinrep		= $io_fecha->uf_last_day(substr($ld_periodo,4,2),$li_ano);
		$ld_fecfinrep		= $li_ano."/".substr($ld_periodo,4,2)."/".substr($ld_fecfinrep,0,2);
		$ld_anterior 		= 3;
		break;
			 case 4:
                                  $ld_per01 		= intval(substr($ld_periodo,0,2));
				  $ld_per02 		= intval(substr($ld_periodo,2,2));
				  $ld_per03 		= intval(substr($ld_periodo,4,2));
				  $ls_desper 		= "RANGO DE FECHA";
				  $ld_fecinirep		= $_GET["fechaReporteDesde"];
				  $ld_fecfinrep		= $_GET["fechaReporteHasta"];
				  $ld_anterior 		= 4;
			      break;
            
}

if($li_estmodest==1){
	$ls_codestpro4_min =  "0000000000000000000000000";
	$ls_codestpro5_min =  "0000000000000000000000000";
	$ls_codestpro4h_max = "0000000000000000000000000";
	$ls_codestpro5h_max = "0000000000000000000000000";
	if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min=="")){
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
	else{
		$ls_codestpro1  = $ls_codestpro1_min;
		$ls_codestpro2  = $ls_codestpro2_min;
		$ls_codestpro3  = $ls_codestpro3_min;
		$ls_codestpro4  = $ls_codestpro4_min;
		$ls_codestpro5  = $ls_codestpro5_min;
	}
	if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max=="")){
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
	else{
		$ls_codestpro1h  = $ls_codestpro1h_max;
		$ls_codestpro2h  = $ls_codestpro2h_max;
		$ls_codestpro3h  = $ls_codestpro3h_max;
		$ls_codestpro4h  = $ls_codestpro4h_max;
		$ls_codestpro5h  = $ls_codestpro5h_max;
	}
}
elseif($li_estmodest==2){
	$ls_codestpro4_min  = $_GET["codestpro4"];
	$ls_codestpro5_min  = $_GET["codestpro5"];
	$ls_codestpro4h_max = $_GET["codestpro4h"];
	$ls_codestpro5h_max = $_GET["codestpro5h"];
		
	if(($ls_codestpro1_min=='**') ||($ls_codestpro1_min=='')){
		$ls_codestpro1_min='';
	}
	else{
		$ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
	}
	
	if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min=='')){
		$ls_codestpro2_min='';
	}
	else{
		$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
	}
	
	if(($ls_codestpro3_min=='**')||($ls_codestpro3_min=='')){
		$ls_codestpro3_min='';
	}
	else{
		$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
	}
	
	if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min=='')){
		$ls_codestpro4_min='';
	}
	else{
		$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	}
	
	if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min=='')){
		$ls_codestpro5_min='';
	}
	else{
		$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
	}
		
	if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max=='')){
		$ls_codestpro1h_max='';
	}
	else{
		$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
	}
	
	if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max=='')){
		$ls_codestpro2h_max='';
	}
	else{
		$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
	}
	
	if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max=='')){
		$ls_codestpro3h_max='';
	}
	else{
		$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
	}
	
	if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max=='')){
		$ls_codestpro4h_max='';
	}
	else{
		$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
	}
	
	if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max=='')){
		$ls_codestpro5h_max='';
	}
	else{
		$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
	}
		
	if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min=="")){
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
	else{
		$ls_codestpro1  = $ls_codestpro1_min;
		$ls_codestpro2  = $ls_codestpro2_min;
		$ls_codestpro3  = $ls_codestpro3_min;
		$ls_codestpro4  = $ls_codestpro4_min;
		$ls_codestpro5  = $ls_codestpro5_min;
	}
	
	if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max=="")){
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
 
$ls_cuentades_min=$_GET["txtcuentades"];
$ls_cuentahas_max=$_GET["txtcuentahas"];
if($ls_cuentades_min==""){
		   $arrResultado=$io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min);
		   $ls_cuentades_min=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
				$ls_cuentades=$ls_cuentades_min;
			}
	else{
		print("<script language=JavaScript>");
		print(" alert('No hay cuentas presupuestraias');");
		print(" close();");
		print("</script>");
	}
}
else{
	$ls_cuentades=$ls_cuentades_min;
}

if($ls_cuentahas_max==""){
		   $arrResultado=$io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max);
		   $ls_cuentahas_max=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
				$ls_cuentahas=$ls_cuentahas_max;
			}
	else{
		print("<script language=JavaScript>");
		print(" alert('No hay cuentas presupuestraias');");
		print(" close();");
		print("</script>");
	}
}
else{
	$ls_cuentahas=$ls_cuentahas_max;
}

$cmbnivel=$_GET["cmbnivel"];
if($cmbnivel=="s1"){
	$ls_cmbnivel="1";
}
else{
	$ls_cmbnivel=$cmbnivel;
}

$ls_codfuefindes=$_GET["txtcodfuefindes"];
$ls_codfuefinhas=$_GET["txtcodfuefinhas"];
if (($ls_codfuefindes=='')&&($ls_codfuefindes=='')){
			$arrResultado=$io_function_report->uf_spg_select_fuentefinanciamiento($ls_minfuefin,$ls_maxfuefin);
			$ls_minfuefin=$arrResultado['as_minfuefin'];
			$ls_maxfuefin=$arrResultado['as_maxfuefin'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ls_codfuefindes=$ls_minfuefin;
				$ls_codfuefinhas=$ls_maxfuefin;
			} 
}

$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
if($li_estmodest==1){
	if (($ls_codestpro1<>"")&&($ls_codestpro2=="")&&($ls_codestpro3=="")){
		$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1);
		$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1);
	}
	elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3=="")){
		$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2);
		$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2);
	}
	elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3<>"")){
		$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
		$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3);
	}
	else{
		$ls_programatica_desde1="";
		$ls_programatica_hasta1="";
	}
}
else{
	$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5)."-".$ls_estclades;
	$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3)."-".substr($ls_codestpro4h,-$ls_loncodestpro4)."-".substr($ls_codestpro5h,-$ls_loncodestpro5)."-".$ls_estclahas;
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
$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);


/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////
$ls_desc_event="Solicitud de Reporte Ejecucion Presupuestaria Mensual de Gasto al ".$ld_fecfinrep." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
$io_function_report->uf_load_seguridad_reporte("SPG","igesp_spg_r_ejecucion_financiera_mensual.php",$ls_desc_event);
////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////////


$data_cuentas = $io_report->uf_obtener_cuentas($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estclades,
											   $ls_codestpro1h, $ls_codestpro2h, $ls_codestpro3h, $ls_codestpro4h, $ls_codestpro5h, $ls_estclahas,
											   $ls_cmbnivel, $ls_cuentades, $ls_cuentahas, $ls_codfuefindes, $ls_codfuefinhas);

$arrdataejecucion = array();
while(!$data_cuentas->EOF){
	$ls_spgcuenta    = $data_cuentas->fields['spg_cuenta'];
	$ls_denominacion = $data_cuentas->fields['denominacion'];
	$ls_codestpro1_c = $data_cuentas->fields['codestpro1'];
	$ls_codestpro2_c = $data_cuentas->fields['codestpro2'];
	$ls_codestpro3_c = $data_cuentas->fields['codestpro3'];
	$ls_codestpro4_c = $data_cuentas->fields['codestpro4'];
	$ls_codestpro5_c = $data_cuentas->fields['codestpro5'];
	$ls_estcla_c     = $data_cuentas->fields['estcla'];
	$ls_descestpro   = $data_cuentas->fields['descestpro'];
	 
	$arrtotales = $io_report->uf_obtener_ejecucion_cuenta($ls_spgcuenta, $ls_codestpro1_c, $ls_codestpro2_c, $ls_codestpro3_c, $ls_codestpro4_c, $ls_codestpro5_c, $ls_estcla_c, $ld_fecfinrep);
	if($li_estmodest==1){
		$ls_programatica_c = substr($ls_codestpro1_c,-$ls_loncodestpro1)."-".substr($ls_codestpro2_c,-$ls_loncodestpro2)."-".substr($ls_codestpro3_c,-$ls_loncodestpro3);
	}
	else{
		$ls_programatica_c = substr($ls_codestpro1_c,-$ls_loncodestpro1)."-".substr($ls_codestpro2_c,-$ls_loncodestpro2)."-".substr($ls_codestpro3_c,-$ls_loncodestpro3)."-".substr($ls_codestpro4_c,-$ls_loncodestpro4)."-".substr($ls_codestpro5_c,-$ls_loncodestpro5)."-".$ls_estclahas;
	}
	 
	$arrdataejecucion[] = array('spg_cuenta'=>$ls_spgcuenta, 'denominacion'=>$ls_denominacion, 'programatica'=>$ls_programatica_c,'descestpro'=>$ls_descestpro,
	                             'montoactualizado'=>$arrtotales['montoactualizado'],'precomprometido'=>$arrtotales['precomprometido'],
	                             'comprometido'=>$arrtotales['comprometido'],'porcomprometido'=>$arrtotales['porcentcomp'],
	 							 'causado'=>$arrtotales['causado'],'porcausado'=>$arrtotales['porcentcaus'],
	 							 'pagado'=>$arrtotales['pagado'],'porpagado'=>$arrtotales['porcentpaga']); 
	unset($arrtotales); 
	$data_cuentas->MoveNext();
}
unset($data_cuentas);
unset($io_report);
/*echo count($arrdataejecucion);
die();*/
  
if(empty($arrdataejecucion)) // No hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else // Imprimimos el reporte
{
	$ls_titulo = 'EJECUCION DEL PRESUPUESTO AL '.$io_funciones->uf_convertirfecmostrar($ld_fecfinrep);
	
	set_time_limit(1800);
	ini_set('memory_limit','1024M');
	$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
	uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
	$io_pdf->ezStartPageNumbers(750,25,10,'','',1); // Insertar el número de página
	//totales

	$li_control    = 0;
	$li_ejecucion  = count($arrdataejecucion);
	$ls_cuenta_ant = '';
	foreach ($arrdataejecucion as $ejecucion) {
		$ls_cuenta           = $ejecucion['spg_cuenta'];
		$ls_dencuenta        = $ejecucion['denominacion'];
		$ls_estprograma      = $ejecucion['programatica'];
		$ls_denestpro        = $ejecucion['descestpro'];
		$ld_montoactualizado = $ejecucion['montoactualizado'];
		$ld_precomprometido  = $ejecucion['precomprometido'];
		$ld_comprometido     = $ejecucion['comprometido'];
		$ld_porcomprometido  = $ejecucion['porcomprometido'];
		$ld_causado          = $ejecucion['causado'];
		$ld_porcausado       = $ejecucion['porcausado'];
		$ld_pagado           = $ejecucion['pagado'];
		$ld_porpagado        = $ejecucion['porpagado'];

		
		if($ls_cuenta_ant == ''){
			$ls_cuenta_ant = $ls_cuenta;
			uf_print_cabecera_cuenta($ls_cuenta, $ls_dencuenta, $io_pdf);
			$la_data[] = array('programatica'=>$ls_estprograma,'descripcion'=>$ls_denestpro,'montoactualizado'=>number_format($ld_montoactualizado,2,",","."),'precomprometido'=>number_format($ld_precomprometido,2,",","."),
 							   'comprometido'=>number_format($ld_comprometido,2,",","."),'porcomprometido'=>number_format($ld_porcomprometido,2,",","."),
 							   'causado'=>number_format($ld_causado,2,",","."),'porcausado'=>number_format($ld_porcausado,2,",","."),
 							   'pagado'=>number_format($ld_pagado,2,",","."),'porpagado'=>number_format($ld_porpagado,2,",","."));
			
		}
		else if($ls_cuenta_ant == $ls_cuenta){
			$la_data[] = array('programatica'=>$ls_estprograma,'descripcion'=>$ls_denestpro,'montoactualizado'=>number_format($ld_montoactualizado,2,",","."),'precomprometido'=>number_format($ld_precomprometido,2,",","."),
 							   'comprometido'=>number_format($ld_comprometido,2,",","."),'porcomprometido'=>number_format($ld_porcomprometido,2,",","."),
 							   'causado'=>number_format($ld_causado,2,",","."),'porcausado'=>number_format($ld_porcausado,2,",","."),
 							   'pagado'=>number_format($ld_pagado,2,",","."),'porpagado'=>number_format($ld_porpagado,2,",","."));
			$ls_cuenta_ant = $ls_cuenta;
		}
		else{
			
			//print_r($la_data);
			uf_print_detalle_ejecucion($la_data,$io_pdf); // Imprimimos el detalle
			unset($la_data);

			//nuevo encabezado
			uf_print_cabecera_cuenta($ls_cuenta, $ls_dencuenta, $io_pdf);
			//echo $ls_cuenta;
			$la_data[] = array('programatica'=>$ls_estprograma,'descripcion'=>$ls_denestpro,'montoactualizado'=>number_format($ld_montoactualizado,2,",","."),'precomprometido'=>number_format($ld_precomprometido,2,",","."),
 							   'comprometido'=>number_format($ld_comprometido,2,",","."),'porcomprometido'=>number_format($ld_porcomprometido,2,",","."),
 							   'causado'=>number_format($ld_causado,2,",","."),'porcausado'=>number_format($ld_porcausado,2,",","."),
 							   'pagado'=>number_format($ld_pagado,2,",","."),'porpagado'=>number_format($ld_porpagado,2,",","."));

			$ls_cuenta_ant = $ls_cuenta;
		}

		if($li_control+2>$li_ejecucion){
			//print_r($la_data);
			uf_print_detalle_ejecucion($la_data,$io_pdf); // Imprimimos el detalle
			unset($la_data);
		}

		$li_control++;
	}


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
}
 
unset($arrdataejecucion);
unset($la_data);
unset($io_report);
unset($io_funciones);
unset($io_function_report);
unset($io_fecha);
?>