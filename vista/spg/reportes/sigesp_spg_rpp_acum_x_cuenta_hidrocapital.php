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

function uf_print_encabezado_pagina($as_titulo , $la_codestproD, $la_denestproD, $la_codestproH, $la_denestproH, $io_pdf)
{
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->line(10,30,1000,30);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
	$tm=505-($li_tm/2);
	$io_pdf->addText($tm,540,16,$as_titulo); // Agregar el título
	$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
	if (!empty($la_codestproD))
	{
		$io_pdf->ezSetY(530);
		$la_data[1]=array('titulo1'=>'<b> Estructura Desde</b>', 'titulo2'=>'<b> Estructura Hasta</b>');
		$la_columnas=array('titulo1'=>'', 'titulo2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
				'fontSize' => 9, // Tamaño de Letras
				'titleFontSize' => 12,  // Tamaño de Letras de los títulos
				'showLines'=>0, // Mostrar Líneas
				'shaded'=>0, // Sombra entre líneas
				'width'=>970, // Ancho de la tabla
				'maxWidth'=>970, // Ancho Máximo de la tabla
				'xOrientation'=>'center', // Orientación de la tabla
				'cols'=>array('titulo1'=>array('justification'=>'center','width'=>485),
						'titulo2'=>array('justification'=>'center','width'=>485))); // Justificación y ancho de la columna
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


		if ($ls_estmodest==1)
		{
			$ls_datat1[1]=array('nombre1'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro1'=>$ls_codestpro1,'denom1'=>$la_denestproD["denestpro1"],
					'nombre2'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro2'=>$ls_codestpro1H,'denom2'=>$la_denestproH["denestpro1"]);
			$ls_datat1[2]=array('nombre1'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro1'=>$ls_codestpro2,'denom1'=>$la_denestproD["denestpro2"],
					'nombre2'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro2'=>$ls_codestpro2H,'denom2'=>$la_denestproH["denestpro2"]);
			$ls_datat1[3]=array('nombre1'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro1'=>$ls_codestpro3,'denom1'=>$la_denestproD["denestpro3"],
					'nombre2'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro2'=>$ls_codestpro3H,'denom2'=>$la_denestproH["denestpro3"]);
		}
		else
		{
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
				'width'=>970, // Ancho de la tabla
				'maxWidth'=>970, // Ancho Máximo de la tabla
				'xOrientation'=>'center', // Orientación de la tabla
				'cols'=>array('nombre1'=>array('justification'=>'left','width'=>80),
						'codestpro1'=>array('justification'=>'right','width'=>60),
						'denom1'=>array('justification'=>'left','width'=>345),
						'nombre2'=>array('justification'=>'left','width'=>80),
						'codestpro2'=>array('justification'=>'right','width'=>60),
						'denom2'=>array('justification'=>'left','width'=>345)));
		$la_columna=array('nombre1'=>'','codestpro1'=>'','denom1'=>'','nombre2'=>'','codestpro2'=>'','denom2'=>'');
		$io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);
		unset($ls_datat1);
		unset($la_config);
		unset($la_columna);
	}
	
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	
	return $io_pdf;
}

function uf_print_cabecera($io_pdf)
{
	$io_cabecera=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->ezSetY(382);
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

function uf_print_detalle($la_data,$io_pdf)
{
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
}


function uf_print_pie_cabecera($la_data_tot, $io_pdf)
{
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

//------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("sigesp_spg_funciones_reportes.php");
$io_function_report = new sigesp_spg_funciones_reportes();
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funciones=new class_funciones();
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
$io_fecha = new class_fecha();
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_acumulado_cuenta.php");
$io_report = new ServicioAcumuladoCuenta();
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_utilidadreporte.php");
$io_utilidad = new ServicioUtilidadReporte();

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
}
elseif($li_estmodest==2)
 {
	$ls_codestpro4  = $_GET["codestpro4"];
	$ls_codestpro5  = $_GET["codestpro5"];
	$ls_codestpro4h = $_GET["codestpro4h"];
	$ls_codestpro5h = $_GET["codestpro5h"];
}
$ldt_fecini = $li_ano."-".$ls_cmbmesdes."-01";
$ldt_fecini_rep = "01/".$ls_cmbmesdes."/".$li_ano;
$ls_mes=$ls_cmbmeshas;
$ls_ano=$li_ano;
$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
if($cmbnivel=="s1")
{
	$ls_cmbnivel="1";
}
else
{
	$ls_cmbnivel=$cmbnivel;
}

//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
$ls_titulo  = " ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA  ".$fecfin;

$ls_codestproD = '';
$ls_filtroD = '';
if ($ls_codestpro1 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro1,25,0,0);
	$ls_filtroD .= 'PCT.codestpro1,';
}
if ($ls_codestpro2 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro2,25,0,0);
	$ls_filtroD .= 'PCT.codestpro2,';
}
if ($ls_codestpro3 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro3,25,0,0);
	$ls_filtroD .= 'PCT.codestpro3,';
}
if ($ls_codestpro4 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro4,25,0,0);
	$ls_filtroD .= 'PCT.codestpro4,';
}
if ($ls_codestpro5 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro5,25,0,0);
	$ls_filtroD .= 'PCT.codestpro5,';
}
if ($ls_estclades != '')
{
	$ls_codestproD .= $ls_estclades;
	$ls_filtroD .= 'PCT.estcla,';
}
$ls_filtroD = substr($ls_filtroD,0,strlen($ls_filtroD)-1);

$ls_codestproH = '';
$ls_filtroH ='';
if ($ls_codestpro1h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro1h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro1,';	
}
if ($ls_codestpro2h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro2h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro2,';	
}
if ($ls_codestpro3h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro3h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro3,';	
}
if ($ls_codestpro4h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro4h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro4,';	
}
if ($ls_codestpro5h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro5h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro5,';	
}
$ls_codestproH .= $ls_estclahas;
if ($ls_estclahas != '')
{
	$ls_codestproH .= $ls_estclahas;
	$ls_filtroH .= 'PCT.estcla,';
}
$ls_filtroH = substr($ls_filtroH,0,strlen($ls_filtroH)-1);

$dataReporte = $io_report->buscarCuentas($ls_codestproD, $ls_codestproH, $ls_cuentades, $ls_cuentahas, $ls_cmbnivel, $ls_filtroD, $ls_filtroH);
if($dataReporte === false || $dataReporte->EOF)
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else
{
	/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
	$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_acum_x_cuentas.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(9,3,3,3); // Configuración de los margenes en centímetros
	$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el número de página
	$dataEncabezado = $io_report->buscarNombreEstructura($ls_codestproD, $ls_codestproH, $ls_cmbnivel, $ls_filtroD, $ls_filtroH);
	$i=0;
	while (!$dataEncabezado->EOF)
	{
		if ($i==0)
		{
			$la_codestproD["codestpro1"] = $dataEncabezado->fields["codestpro1"];
			$la_codestproD["codestpro2"] = $dataEncabezado->fields["codestpro2"];
			$la_codestproD["codestpro3"] = $dataEncabezado->fields["codestpro3"];
			$la_codestproD["codestpro4"] = $dataEncabezado->fields["codestpro4"];
			$la_codestproD["codestpro5"] = $dataEncabezado->fields["codestpro5"];
			$la_denestproD["denestpro1"] = $dataEncabezado->fields["denestpro1"];
			$la_denestproD["denestpro2"] = $dataEncabezado->fields["denestpro2"];
			$la_denestproD["denestpro3"] = $dataEncabezado->fields["denestpro3"];
			$la_denestproD["denestpro4"] = $dataEncabezado->fields["denestpro4"];
			$la_denestproD["denestpro5"] = $dataEncabezado->fields["denestpro5"];
			$i=1;
		}		
		$la_codestproH["codestpro1"] = $dataEncabezado->fields["codestpro1"];
		$la_codestproH["codestpro2"] = $dataEncabezado->fields["codestpro2"];
		$la_codestproH["codestpro3"] = $dataEncabezado->fields["codestpro3"];
		$la_codestproH["codestpro4"] = $dataEncabezado->fields["codestpro4"];
		$la_codestproH["codestpro5"] = $dataEncabezado->fields["codestpro5"];
		$la_denestproH["denestpro1"] = $dataEncabezado->fields["denestpro1"];
		$la_denestproH["denestpro2"] = $dataEncabezado->fields["denestpro2"];
		$la_denestproH["denestpro3"] = $dataEncabezado->fields["denestpro3"];
		$la_denestproH["denestpro4"] = $dataEncabezado->fields["denestpro4"];
		$la_denestproH["denestpro5"] = $dataEncabezado->fields["denestpro5"];
		$dataEncabezado->MoveNext();
	}
	unset($dataEncabezado);
	$io_pdf = uf_print_encabezado_pagina($ls_titulo , $la_codestproD, $la_denestproD, $la_codestproH, $la_denestproH, $io_pdf);
	
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
	$totalAsignado = 0;
	$totalAumento = 0;
	$totalDisminucion = 0;
	$totalMontoActualizado = 0;
	$totalPrecomprometido = 0;
	$totalComprometido = 0;
	$totalSaldoxcomprometer = 0;
	$totalCausado = 0;
	$totalPagado = 0;
	$totalPorpagar = 0;
	
	while (!$dataReporte->EOF)
	{
		$spg_cuenta   = $dataReporte->fields['spg_cuenta'];
		$nivel        = $dataReporte->fields['nivel'];
		$denominacion = $dataReporte->fields['denominacion'];
                $status = $dataReporte->fields['status'];
		
		if ($lb_codfuefin)
		{
			$asignado = $dataReporte->fields['asignado'];
		}
		else
		{
			$asignado = $io_report->buscarAsignadoFuente($ls_codestproD, $ls_codestproH, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhasl, $ls_filtroD, $ls_filtroH);
		}
		$dataSaldo = $io_report->buscarSaldoCuenta($ls_codestproD, $ls_codestproH, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin, $ls_filtroD, $ls_filtroH, $status);
		$aumento          = $dataSaldo->fields['aumento'];
		$disminucion      = $dataSaldo->fields['disminucion'];
		$precompromiso    = $dataSaldo->fields['precompromiso'];
		$compromiso       = $dataSaldo->fields['compromiso'];
		$causado          = $dataSaldo->fields['causado'];
		$pagado           = $dataSaldo->fields['pagado'];
		$montoActualizado = $asignado + $aumento - $disminucion;
		$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
		$porPagar         = $causado - $pagado;
		if ($status == 'C')
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
		
		$dataReporte->MoveNext();
		unset($dataSaldo);
	}
	
	$la_data_tot[]=array('total'=>'<b>TOTAL</b>',
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
	
	$io_pdf = uf_print_cabecera($io_pdf);
	$io_pdf = uf_print_detalle($la_data,$io_pdf);
	$io_pdf = uf_print_pie_cabecera($la_data_tot,$io_pdf);
	$io_pdf->ezStopPageNumbers(1,1);
	$io_pdf->ezStream();
	unset($dataReporte);
}

unset($io_report);
unset($io_funciones);
unset($io_function_report);
?>
