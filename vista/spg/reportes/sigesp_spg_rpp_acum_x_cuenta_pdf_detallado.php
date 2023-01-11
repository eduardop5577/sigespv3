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
if(!array_key_exists("la_logusr",$_SESSION)) {
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";		
}
	
//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo, $as_titulo1, $io_pdf) {
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->line(10,30,1000,30);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
	$tm=505-($li_tm/2);
	$io_pdf->addText($tm,550,16,$as_titulo); // Agregar el título
    /*$li_tm=$io_pdf->getTextWidth(16,$as_titulo1);
	$tm=505-($li_tm/2);
	$io_pdf->addText($tm,530,16,$as_titulo1); // Agregar el título*/
	
	$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	
	return $io_pdf;
}
	
//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_cabecera($io_pdf) {
	$io_cabecera=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->ezSetY(415);
	$la_data=array(array('cuenta'=>'<b>Cuenta</b>',
		                 'denominacion'=>'<b>Denominación</b>',
						 'asignado'=>'<b>Asignado</b>',
		                 'aumento'=>'<b>Aumento</b>',
						 'disminución'=>'<b>Disminución</b>',
						 'montoactualizado'=>'<b>Monto Actualizado</b>',
						 'precomprometido'=>'<b>Pre-Comprometido</b>',
						 'comprometido'=>'<b>Comprometido</b>',
						 'saldoxcomprometer'=>'<b>Saldo por Comprometer</b>',
						 'causado'=>'<b>Causado</b>',
						 'pagado'=>'<b>Pagado</b>',
						 'porpagar'=>'<b>Por Pagar</b>'));
		
	$la_columna=array('cuenta'=>'',
	                  'denominacion'=>'',
				      'asignado'=>'',
	                  'aumento'=>'',
					  'disminución'=>'',
					  'montoactualizado'=>'',
					  'precomprometido'=>'',
					  'comprometido'=>'',
					  'saldoxcomprometer'=>'',
					  'causado'=>'',
					  'pagado'=>'',
					  'porpagar'=>'');

	$la_config=array('showHeadings'=>0,     // Mostrar encabezados
					 'fontSize' => 9, // Tamaño de Letras
					 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
					 'showLines'=>1, // Mostrar Líneas
					 'shaded'=>0, // Sombra entre líneas
					 'colGap'=>1, // separacion entre tablas
					 'width'=>990, // Ancho de la tabla
					 'maxWidth'=>990, // Ancho Máximo de la tabla
					 'xOrientation'=>'center', // Orientación de la tabla
					 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
					 			   'denominacion'=>array('justification'=>'center','width'=>160), // Justificación y  
					 			   'asignado'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
					 			   'aumento'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
								   'disminución'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la 
								   'montoactualizado'=>array('justification'=>'center','width'=>75), // Justificación y ancho de 
								   'precomprometido'=>array('justification'=>'center','width'=>75), // Justificación y ancho de 
								   'comprometido'=>array('justification'=>'center','width'=>75), // Justificación  
								   'saldoxcomprometer'=>array('justification'=>'center','width'=>75), // Justificación y ancho 
								   'causado'=>array('justification'=>'center','width'=>75),
								   'pagado'=>array('justification'=>'center','width'=>75),
								   'porpagar'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la 
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_cabecera,'all');
	unset($la_data);
	unset($la_columnas);
	unset($la_config);
	
	return $io_pdf;
}// end function uf_print_cabecera
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle($la_data, $io_pdf) {
	$io_pdf->ezSetY(390);
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 'fontSize' => 8, // Tamaño de Letras
					 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
					 'showLines'=>1, // Mostrar Líneas
					 'shaded'=>0, // Sombra entre líneas
					 'colGap'=>1, // separacion entre tablas
					 'width'=>990, // Ancho de la tabla
					 'maxWidth'=>990, // Ancho Máximo de la tabla
					 'xOrientation'=>'center', // Orientación de la tabla
					 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
					 			   'denominacion'=>array('justification'=>'left','width'=>160), // Justificación y  
					 			   'asignado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
					 			   'aumento'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
								   'disminución'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
								   'montoactualizado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
								   'precomprometido'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
								   'comprometido'=>array('justification'=>'right','width'=>75), // Justificación  
								   'saldoxcomprometer'=>array('justification'=>'right','width'=>75), // Justificación y ancho 
								   'causado'=>array('justification'=>'right','width'=>75),
								   'pagado'=>array('justification'=>'right','width'=>75),
								   'porpagar'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la 
	$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
	                   'denominacion'=>'<b>Denominación</b>',
				       'asignado'=>'<b>Asignado</b>',
	                   'aumento'=>'<b>Aumento</b>',
					   'disminución'=>'<b>Disminución</b>',
					   'montoactualizado'=>'<b>Monto Actualizado</b>',
					   'precomprometido'=>'<b>Pre-Comprometido</b>',
					   'comprometido'=>'<b>Comprometido</b>',
					   'saldoxcomprometer'=>'<b>Saldo por Comprometer</b>',
					   'causado'=>'<b>Causado</b>',
					   'pagado'=>'<b>Pagado</b>',
					   'porpagar'=>'<b>Por Pagar</b>');
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	unset($la_data);
	unset($la_columnas);
	unset($la_config);
	
	return $io_pdf;
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_pie_cabecera($la_data_tot, $io_pdf) {
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 'fontSize' => 8, // Tamaño de Letras
					 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
					 'showLines'=>1, // Mostrar Líneas
					 'shaded'=>0, // Sombra entre líneas
					 'colGap'=>1, // separacion entre tablas
					 'width'=>990, // Ancho de la tabla
					 'maxWidth'=>990, // Ancho Máximo de la tabla
					 'xOrientation'=>'center', // Orientación de la tabla
					 'cols'=>array('total'=>array('justification'=>'center','width'=>230), // Justificación y ancho de la 
					 			   'asignado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
					 			   'aumento'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
								   'disminución'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la 
								   'montoactualizado'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
								   'precomprometido'=>array('justification'=>'right','width'=>75), // Justificación y ancho de 
								   'comprometido'=>array('justification'=>'right','width'=>75), // Justificación  
								   'saldoxcomprometer'=>array('justification'=>'right','width'=>75), // Justificación y ancho 
								   'causado'=>array('justification'=>'right','width'=>75),
								   'pagado'=>array('justification'=>'right','width'=>75),
								   'porpagar'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la 
	$la_columnas=array('total'=>'',
				       'asignado'=>'',
	                   'aumento'=>'',
					   'disminución'=>'',
					   'montoactualizado'=>'',
					   'precomprometido'=>'',
					   'comprometido'=>'',
					   'saldoxcomprometer'=>'',
					   'causado'=>'',
					   'pagado'=>'',
					   'porpagar'=>'');
	$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
	unset($la_data_tot);
	unset($la_columnas);
	unset($la_config);
	
	return $io_pdf;
}// end function uf_print_pie_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------
 function uf_print_cabecera_estructura($io_cabecera,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
	                                   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$io_pdf)
{
	$io_pdf->saveState();
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
	
	$ls_codestpro1    = trim(substr($ls_codestpro1,-$li_loncodestpro1));
	$ls_codestpro2    = trim(substr($ls_codestpro2,-$li_loncodestpro2));
	$ls_codestpro3    = trim(substr($ls_codestpro3,-$li_loncodestpro3));
	$ls_codestpro4    = trim(substr($ls_codestpro4,-$li_loncodestpro4));
	$ls_codestpro5    = trim(substr($ls_codestpro5,-$li_loncodestpro5));
	
	$io_pdf->ezSetY(520);
	if ($ls_estmodest==1) {
		$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
		$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
		$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);			
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>80),									  
									   'codestpro'=>array('justification'=>'right','width'=>60),
									   'denom'=>array('justification'=>'left','width'=>700)));
		$la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
		$io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);
	}
	else {
		$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
		$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
		$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);
		$ls_datat1[4]=array('nombre'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro'=>$ls_codestpro4,'denom'=>$ls_denestpro4);
		$ls_datat1[5]=array('nombre'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro'=>$ls_codestpro5,'denom'=>$ls_denestpro5);			
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>80),									  
									   'codestpro'=>array('justification'=>'right','width'=>60),
									   'denom'=>array('justification'=>'left','width'=>700)));
	   $la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
	   $io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);	
	}
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_cabecera,'all');
	unset($ls_datat1);
	unset($la_config);
	unset($la_columna);
	
	return $io_pdf;
}// end function uf_print_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("sigesp_spg_funciones_reportes.php");
$io_function_report = new sigesp_spg_funciones_reportes();
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funciones=new class_funciones();
require_once("sigesp_spg_funciones_reportes.php");
$io_fun_gasto=new sigesp_spg_funciones_reportes();		
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
$io_fecha = new class_fecha();
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_acumulado_cuenta.php");
$io_report = new ServicioAcumuladoCuenta();

//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
$li_ano             = substr($ldt_periodo,0,4);
$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
$ls_codestpro1      = $_GET["codestpro1"];
$ls_codestpro2      = $_GET["codestpro2"];
$ls_codestpro3      = $_GET["codestpro3"];
$ls_codestpro1h     = $_GET["codestpro1h"];
$ls_codestpro2h     = $_GET["codestpro2h"];
$ls_codestpro3h     = $_GET["codestpro3h"];
$ls_estclades       = $_GET["estclades"];
$ls_estclahas       = $_GET["estclahas"];
$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
$ls_cmbmesdes       = $_GET["cmbmesdes"];
$ls_cmbmeshas       = $_GET["cmbmeshas"];
$cmbnivel           = $_GET["cmbnivel"];
$ls_cuentades       = $_GET["txtcuentades"];
$ls_cuentahas       = $_GET["txtcuentahas"];
$ls_codfuefindes    = $_GET["txtcodfuefindes"];
$ls_codfuefinhas    = $_GET["txtcodfuefinhas"];
//-----------------------------------------------------------------------------------------------------------------------------
$ls_programatica_desde = '';
$ls_programatica_hasta = '';
if($li_estmodest==1)
{
	$ls_codestpro4  = "0000000000000000000000000";
	$ls_codestpro5  = "0000000000000000000000000";
	$ls_codestpro4h = "0000000000000000000000000";
	$ls_codestpro5h = "0000000000000000000000000";
	if (!empty($ls_codestpro1) && !empty($ls_codestpro2) && !empty($ls_codestpro3)) {
		$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;
		$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h;
	}
}
elseif($li_estmodest==2) {
	$ls_codestpro4  = $_GET["codestpro4"];
	$ls_codestpro5  = $_GET["codestpro5"];
	$ls_codestpro4h = $_GET["codestpro4h"];
	$ls_codestpro5h = $_GET["codestpro5h"];
	if (!empty($ls_codestpro1) && !empty($ls_codestpro2) && !empty($ls_codestpro3)) {
		$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
		$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h."-".$ls_codestpro4h."-".$ls_codestpro5h;
	}
}
$ldt_fecini = $li_ano."-".$ls_cmbmesdes."-01";
$ldt_fecini_rep = "01/".$ls_cmbmesdes."/".$li_ano;
$ls_mes=$ls_cmbmeshas;
$ls_ano=$li_ano;
$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
if($cmbnivel=="s1") {
	$ls_cmbnivel="1";
}
else {
	$ls_cmbnivel=$cmbnivel;
}
				
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------		
$ls_titulo  = " <b> ACUMULADO POR CUENTAS  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin." </b> ";
$ls_titulo1 = '';
if (!empty($ls_programatica_desde) && !empty($ls_programatica_hasta)) {
	$ls_titulo1 = " DESDE LA PROGRAMATICA  ".$ls_programatica_desde."  HASTA  ".$ls_programatica_hasta;	
}
else {
	$ls_titulo1 = " TODAS LAS ESTRUCTURAS PROGRAMATICAS  ";
}

//------------------------------------------------------------------------------------------------------------------------------
$ls_codestproD = '';
if ($ls_codestpro1 != "0000000000000000000000000") {
	$ls_codestproD = str_pad($ls_codestpro1,25,0,0).str_pad($ls_codestpro2,25,0,0).str_pad($ls_codestpro3,25,0,0).str_pad($ls_codestpro4,25,0,0).str_pad($ls_codestpro5,25,0,0).$ls_estclades;
}    

$ls_codestproH = '';
if ($ls_codestpro1h != "0000000000000000000000000") {
	$ls_codestproH = str_pad($ls_codestpro1h,25,0,0).str_pad($ls_codestpro2h,25,0,0).str_pad($ls_codestpro3h,25,0,0).str_pad($ls_codestpro4h,25,0,0).str_pad($ls_codestpro5h,25,0,0).$ls_estclahas;
}
		          
//--------------------------------------------------------------------------------------------------------------------------------
$dataEstructura = $io_report->buscarEstructuraCuenta($ls_codestproD, $ls_codestproH, $ls_cuentades, $ls_cuentahas, $ls_cmbnivel);	
if($dataEstructura === false || $dataEstructura->EOF) {
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else {
	$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin;
	$io_fun_gasto->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_acum_x_cuentas.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(7.8,3,3,3); // Configuración de los margenes en centímetros
	$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
	$lb_codfuefin = true;
	if (!empty($ls_codfuefindes) && !empty($ls_codfuefinhas))
	{
		$lb_codfuefin = false;
	}
	else
	{
		if ($ls_codfuefindes=='--' && $ls_codfuefinhas=='--')
		{		
			$lb_codfuefin = false;
		}
	}
	$io_pdf = uf_print_encabezado_pagina($ls_titulo, $ls_titulo1, $io_pdf);
	$io_pdf = uf_print_cabecera($io_pdf);
	$x = 1;
	$estructurAnt = '';
	$totalAsignadoGen         = 0;
	$totalAumentoGen          = 0;
	$totalDisminucionGen      = 0;
	$totalMontoActualizadoGen = 0;
	$totalPrecomprometidoGen  = 0;
	$totalComprometidoGen     = 0;
	$totalSaldoxcomprometerGen = 0;
	$totalCausadoGen           = 0;
	$totalPagadoGen            = 0;
	$totalPorpagarGen          = 0;
	while (!$dataEstructura->EOF) 
        {
		$codestpro1   = $dataEstructura->fields['codestpro1'];
		$codestpro2   = $dataEstructura->fields['codestpro2'];
		$codestpro3   = $dataEstructura->fields['codestpro3'];
		$codestpro4   = $dataEstructura->fields['codestpro4'];
		$codestpro5   = $dataEstructura->fields['codestpro5'];
		$estcla       = $dataEstructura->fields['estcla'];
		$denestpro1   = $dataEstructura->fields['denestpro1'];
		$denestpro2   = $dataEstructura->fields['denestpro2'];
		$denestpro3   = $dataEstructura->fields['denestpro3'];
		$denestpro4   = $dataEstructura->fields['denestpro4'];
		$denestpro5   = $dataEstructura->fields['denestpro5'];
		$spg_cuenta   = $dataEstructura->fields['spg_cuenta'];
		$nivel        = $dataEstructura->fields['nivel'];
		$denominacion = $dataEstructura->fields['denominacion'];
		$estructura = $codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5.$estcla;
		
		if (empty($estructurAnt))
		{
			$estructurAnt = $estructura;
			$codestpro1Ant = $codestpro1;
			$codestpro2Ant = $codestpro2;
			$codestpro3Ant = $codestpro3;
			$codestpro4Ant = $codestpro4;
			$codestpro5Ant = $codestpro5;
			$denestpro1Ant = $denestpro1;
			$denestpro2Ant = $denestpro2;
			$denestpro3Ant = $denestpro3;
			$denestpro4Ant = $denestpro4;
			$denestpro5Ant = $denestpro5;
		}
		
		if ($estructurAnt == $estructura)
		{
			if ($lb_codfuefin)
			{
				$asignado = $dataEstructura->fields['asignado'];
			}
			else
			{
				$asignado = $io_report->buscarAsignadoFuenteEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas);
			}
				
			$dataSaldo        = $io_report->buscarSaldoCuentaEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin);
			$aumento          = $dataSaldo->fields['aumento'];
			$disminucion      = $dataSaldo->fields['disminucion'];
			$precompromiso    = $dataSaldo->fields['precompromiso'];
			$compromiso       = $dataSaldo->fields['compromiso'];
			$causado          = $dataSaldo->fields['causado'];
			$pagado           = $dataSaldo->fields['pagado'];
			$montoActualizado = $asignado + $aumento - $disminucion;
			$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
			$porPagar         = $causado - $pagado;
				
			if ($dataEstructura->fields['status'] == 'C')
			{
				$totalAsignado = $totalAsignado + $asignado;
				$totalAumento = $totalAumento + $aumento;
				$totalDisminucion = $totalDisminucion + $disminucion;
				$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
				$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
				$totalComprometido = $totalComprometido + $compromiso;
				$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
				$totalCausado = $totalCausado + $causado;
				$totalPagado = $totalPagado + $pagado;
				$totalPorpagar = $totalPorpagar + $porPagar;
			}
			else
			{
				if ($nivel == $ls_cmbnivel)
				{
					$totalAsignado = $totalAsignado + $asignado;
					$totalAumento = $totalAumento + $aumento;
					$totalDisminucion = $totalDisminucion + $disminucion;
					$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
					$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
					$totalComprometido = $totalComprometido + $compromiso;
					$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
					$totalCausado = $totalCausado + $causado;
					$totalPagado = $totalPagado + $pagado;
					$totalPorpagar = $totalPorpagar + $porPagar;
				}
			}
				
			$la_data[]=array('cuenta'=>$spg_cuenta, 'denominacion'=>$denominacion, 'asignado'=>number_format($asignado,2,",","."),
					'aumento'=>number_format($aumento,2,",","."),'disminución'=>number_format($disminucion,2,",","."),
					'montoactualizado'=>number_format($montoActualizado,2,",","."),'precomprometido'=>number_format($precompromiso,2,",","."),
					'comprometido'=>number_format($compromiso,2,",","."),'saldoxcomprometer'=>number_format($saldoComprometer,2,",","."),
					'causado'=>number_format($causado,2,",","."),'pagado'=>number_format($pagado,2,",","."),
					'porpagar'=>number_format($porPagar,2,",","."));
		}
		else
		{
			//Imprimir Cabecera y detalle
			$ls_estructura = "";
			if($li_estmodest == 1)
			{
				$ls_estructura = trim(substr($codestpro1Ant,-$ls_loncodestpro1))."-".trim(substr($codestpro2Ant,-$ls_loncodestpro2))."-".trim(substr($codestpro3Ant,-$ls_loncodestpro3));
			}
			elseif($li_estmodest == 2)
			{
				$ls_estructura = trim(substr($codestpro1Ant,-$ls_loncodestpro1))."-".trim(substr($codestpro2Ant,-$ls_loncodestpro2))."-".trim(substr($codestpro3Ant,-$ls_loncodestpro3))."-".trim(substr($codestpro4Ant,-$ls_loncodestpro4))."-".trim(substr($codestpro5Ant,-$ls_loncodestpro5));
			}
			$totalAsignadoGen         = $totalAsignadoGen + $totalAsignado;
			$totalAumentoGen          = $totalAumentoGen + $totalAumento;
			$totalDisminucionGen      = $totalDisminucionGen + $totalDisminucion;
			$totalMontoActualizadoGen = $totalMontoActualizadoGen + $totalMontoActualizado;
			$totalPrecomprometidoGen  = $totalPrecomprometidoGen + $totalPrecomprometido;
			$totalComprometidoGen     = $totalComprometidoGen + $totalComprometido;
			$totalSaldoxcomprometerGen = $totalSaldoxcomprometerGen + $totalSaldoxcomprometer;
			$totalCausadoGen           = $totalCausadoGen + $totalCausado;
			$totalPagadoGen            = $totalPagadoGen + $totalPagado;
			$totalPorpagarGen          = $totalPorpagarGen + $totalPorpagar;
			$la_data_tot[]=array('total'=>'<b>TOTAL '.$ls_estructura.'</b>',
					'asignado'=>number_format($totalAsignado,2,",","."),
					'aumento'=>number_format($totalAumento,2,",","."),
					'disminución'=>number_format($totalDisminucion,2,",","."),
					'montoactualizado'=>number_format($totalMontoActualizado,2,",","."),
					'precomprometido'=>number_format($totalPrecomprometido,2,",","."),
					'comprometido'=>number_format($totalComprometido,2,",","."),
					'saldoxcomprometer'=>number_format($totalSaldoxcomprometer,2,",","."),
					'causado'=>number_format($totalCausado,2,",","."),
					'pagado'=>number_format($totalPagado,2,",","."),
					'porpagar'=>number_format($totalPorpagar,2,",","."));
			$io_cabecera=$io_pdf->openObject();
			$io_pdf = uf_print_cabecera_estructura($io_cabecera, $codestpro1Ant, $codestpro2Ant, $codestpro3Ant, $codestpro4Ant, $codestpro5Ant, 
										 		   $denestpro1Ant, $denestpro2Ant, $denestpro3Ant, $denestpro4Ant, $denestpro5Ant, $io_pdf);
			$io_pdf = uf_print_detalle($la_data,$io_pdf);
			$io_pdf = uf_print_pie_cabecera($la_data_tot,$io_pdf);
			unset($la_data);
			unset($la_data_tot);
			$io_pdf->stopObject($io_cabecera);
			$totalAsignado          = 0;
			$totalAumento           = 0;
			$totalDisminucion       = 0;
			$totalMontoActualizado  = 0;
			$totalPrecomprometido   = 0;
			$totalComprometido      = 0;
			$totalSaldoxcomprometer = 0;
			$totalCausado           = 0;
			$totalPagado            = 0;
			$totalPorpagar          = 0;
			$io_pdf->ezNewPage();
			
			//reiniciar verificacion
			$estructurAnt = $estructura;
			$codestpro1Ant = $codestpro1;
			$codestpro2Ant = $codestpro2;
			$codestpro3Ant = $codestpro3;
			$codestpro4Ant = $codestpro4;
			$codestpro5Ant = $codestpro5;
			$denestpro1Ant = $denestpro1;
			$denestpro2Ant = $denestpro2;
			$denestpro3Ant = $denestpro3;
			$denestpro4Ant = $denestpro4;
			$denestpro5Ant = $denestpro5;
			if ($lb_codfuefin)
			{
				$asignado = $dataEstructura->fields['asignado'];
			}
			else
			{
				$asignado = $io_report->buscarAsignadoFuenteEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas);
			}
			
			$dataSaldo = $io_report->buscarSaldoCuentaEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin);
			$aumento          = $dataSaldo->fields['aumento'];
			$disminucion      = $dataSaldo->fields['disminucion'];
			$precompromiso    = $dataSaldo->fields['precompromiso'];
			$compromiso       = $dataSaldo->fields['compromiso'];
			$causado          = $dataSaldo->fields['causado'];
			$pagado           = $dataSaldo->fields['pagado'];
			$montoActualizado = $asignado + $aumento - $disminucion;
			$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
			$porPagar         = $causado - $pagado;
			
			if ($dataEstructura->fields['status'] == 'C')
			{
				$totalAsignado = $totalAsignado + $asignado;
				$totalAumento = $totalAumento + $aumento;
				$totalDisminucion = $totalDisminucion + $disminucion;
				$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
				$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
				$totalComprometido = $totalComprometido + $compromiso;
				$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
				$totalCausado = $totalCausado + $causado;
				$totalPagado = $totalPagado + $pagado;
				$totalPorpagar = $totalPorpagar + $porPagar;
			}
			else
			{
				if ($nivel == $ls_cmbnivel)
				{
					$totalAsignado = $totalAsignado + $asignado;
					$totalAumento = $totalAumento + $aumento;
					$totalDisminucion = $totalDisminucion + $disminucion;
					$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
					$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
					$totalComprometido = $totalComprometido + $compromiso;
					$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
					$totalCausado = $totalCausado + $causado;
					$totalPagado = $totalPagado + $pagado;
					$totalPorpagar = $totalPorpagar + $porPagar;
				}
			}
			
			$la_data[]=array('cuenta'=>$spg_cuenta, 'denominacion'=>$denominacion, 'asignado'=>number_format($asignado,2,",","."),
					'aumento'=>number_format($aumento,2,",","."),'disminución'=>number_format($disminucion,2,",","."),
					'montoactualizado'=>number_format($montoActualizado,2,",","."),'precomprometido'=>number_format($precompromiso,2,",","."),
					'comprometido'=>number_format($compromiso,2,",","."),'saldoxcomprometer'=>number_format($saldoComprometer,2,",","."),
					'causado'=>number_format($causado,2,",","."),'pagado'=>number_format($pagado,2,",","."),
					'porpagar'=>number_format($porPagar,2,",","."));
		}

		if ($dataEstructura->_numOfRows == $x)
		{
			//Imprimir Cabecera y detalle ultimo registro
                    
			$ls_estructura = "";
			if($li_estmodest == 1)
			{
				$ls_estructura = trim(substr($codestpro1,-$ls_loncodestpro1))."-".trim(substr($codestpro2,-$ls_loncodestpro2))."-".trim(substr($codestpro3,-$ls_loncodestpro3));
			}
			elseif($li_estmodest == 2)
			{
				$ls_estructura = trim(substr($codestpro1,-$ls_loncodestpro1))."-".trim(substr($codestpro2,-$ls_loncodestpro2))."-".trim(substr($codestpro3,-$ls_loncodestpro3))."-".trim(substr($codestpro4,-$ls_loncodestpro4))."-".trim(substr($codestpro5,-$ls_loncodestpro5));
			}
                    
			$totalAsignadoGen         = $totalAsignadoGen + $totalAsignado;
			$totalAumentoGen          = $totalAumentoGen + $totalAumento;
			$totalDisminucionGen      = $totalDisminucionGen + $totalDisminucion;
			$totalMontoActualizadoGen = $totalMontoActualizadoGen + $totalMontoActualizado;
			$totalPrecomprometidoGen  = $totalPrecomprometidoGen + $totalPrecomprometido;
			$totalComprometidoGen     = $totalComprometidoGen + $totalComprometido;
			$totalSaldoxcomprometerGen = $totalSaldoxcomprometerGen + $totalSaldoxcomprometer;
			$totalCausadoGen           = $totalCausadoGen + $totalCausado;
			$totalPagadoGen            = $totalPagadoGen + $totalPagado;
			$totalPorpagarGen          = $totalPorpagarGen + $totalPorpagar;
			$la_data_tot[]=array('total'=>'<b>TOTAL '.$ls_estructura.'</b>',
					'asignado'=>number_format($totalAsignado,2,",","."),
					'aumento'=>number_format($totalAumento,2,",","."),
					'disminución'=>number_format($totalDisminucion,2,",","."),
					'montoactualizado'=>number_format($totalMontoActualizado,2,",","."),
					'precomprometido'=>number_format($totalPrecomprometido,2,",","."),
					'comprometido'=>number_format($totalComprometido,2,",","."),
					'saldoxcomprometer'=>number_format($totalSaldoxcomprometer,2,",","."),
					'causado'=>number_format($totalCausado,2,",","."),
					'pagado'=>number_format($totalPagado,2,",","."),
					'porpagar'=>number_format($totalPorpagar,2,",","."));
			$io_cabecera=$io_pdf->openObject();
			$io_pdf = uf_print_cabecera_estructura($io_cabecera, $codestpro1, $codestpro2, $codestpro3, $codestpro4, $codestpro5,
										 		   $denestpro1, $denestpro2, $denestpro3, $denestpro4, $denestpro5, $io_pdf);
			$io_pdf = uf_print_detalle($la_data,$io_pdf);
			$io_pdf = uf_print_pie_cabecera($la_data_tot,$io_pdf);
			unset($la_data);
			unset($la_data_tot);
			$io_pdf->stopObject($io_cabecera);
			$totalAsignado          = 0;
			$totalAumento           = 0;
			$totalDisminucion       = 0;
			$totalMontoActualizado  = 0;
			$totalPrecomprometido   = 0;
			$totalComprometido      = 0;
			$totalSaldoxcomprometer = 0;
			$totalCausado           = 0;
			$totalPagado            = 0;
			$totalPorpagar          = 0;
		}
		$x++;
		
		$dataEstructura->MoveNext();
		unset($dataSaldo);
	}
	$la_data_tot[]=array('total'=>'<b>TOTAL GENERAL</b>',
			'asignado'=>number_format($totalAsignadoGen,2,",","."),
			'aumento'=>number_format($totalAumentoGen,2,",","."),
			'disminución'=>number_format($totalDisminucionGen,2,",","."),
			'montoactualizado'=>number_format($totalMontoActualizadoGen,2,",","."),
			'precomprometido'=>number_format($totalPrecomprometidoGen,2,",","."),
			'comprometido'=>number_format($totalComprometidoGen,2,",","."),
			'saldoxcomprometer'=>number_format($totalSaldoxcomprometerGen,2,",","."),
			'causado'=>number_format($totalCausadoGen,2,",","."),
			'pagado'=>number_format($totalPagadoGen,2,",","."),
			'porpagar'=>number_format($totalPorpagarGen,2,",","."));
	$io_pdf = uf_print_pie_cabecera($la_data_tot,$io_pdf);
	$io_pdf->ezStopPageNumbers(1,1);
	$io_pdf->ezStream();
}
unset($io_pdf);
unset($io_report);
unset($io_funciones);
?> 