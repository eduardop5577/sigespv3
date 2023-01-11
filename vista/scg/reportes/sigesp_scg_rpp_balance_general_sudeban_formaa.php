<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
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
if(!array_key_exists("la_logusr",$_SESSION)){
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";
}
//
// REPORTE CREADO POR OFIMATICA DE VENEZUELA EL 23/11/2013 PARA EL CLIENTE SOGAMPI
//

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_insert_seguridad($as_titulo)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_insert_seguridad
	//		   Access: private
	//	    Arguments: as_titulo // Título del Reporte
	//    Description: función que guarda la seguridad de quien generó el reporte
	//	   Creado Por: Ing. Yesenia Moreno
	// Fecha Creación: 22/09/2006
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_fun_scg;

	$ls_descripcion="Generó el Reporte ".$as_titulo;
	$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balance_general_formaa.html",$ls_descripcion);
	return $lb_valido;
}
//-----------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo1,$as_titulo2,$as_titulo,$as_titulo3,$as_titulo4,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezadopagina
	//		    Acess: private
	//	    Arguments: as_titulo // Título del Reporte
	//	    		   io_pdf // Instancia de objeto pdf
	//    Description: función que imprime los encabezados por página
	//	   Creado Por: Ing. Yozelin Barragan
	// Fecha Creación: 28/04/2006
	// MODIFICADO POR: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)  FECHA ULTIMA MODIFICACION: 11/03/2013
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	// MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
	$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
	$tm=306-($li_tm/2);
	$io_pdf->addText($tm,740,11,$as_titulo1); // Agregar el título

	$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
	$tm=306-($li_tm/2);
	$io_pdf->addText($tm,730,11,$as_titulo2); // Agregar el título

	$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
	$tm=306-($li_tm/2);
	$io_pdf->addText($tm,720,11,$as_titulo); // Agregar el título

	$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
	$tm=306-($li_tm/2);
	$io_pdf->addText($tm,710,11,$as_titulo3); // Agregar el título

	$li_tm=$io_pdf->getTextWidth(11,$as_titulo4);
	$tm=306-($li_tm/2);
	$io_pdf->addText($tm,700,11,$as_titulo4); // Agregar el título

    // FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
	$io_pdf->addText(510,740,7,$_SESSION["ls_database"]); // Agregar la Base de datos
	$io_pdf->addText(510,730,8,date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(510,720,8,date("h:i a")); // Agregar la hora
	
	
	
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_cabecera($as_nombre_mesdes,$as_nombre_meshas,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera
	//		   Access: private
	//	    Arguments: as_cuenta // cuenta
	//	    		   as_denominacion // denominacion
	//	    		   io_pdf // Objeto PDF
	//    Description: función que imprime la cabecera de cada página
	//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
	// Fecha Creación: 26/09/2013
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		
	global $io_pdf;
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->ezSety(700);				
	$la_data[1]=array('cuenta'=>'',
				      'denominacion'=>'',
				      'mesdes'=>'<b>'.$as_nombre_mesdes.'</b>',
				      'meshas'=>'<b>'.$as_nombre_meshas.'</b>',
				      'variacion_bs'=>'<b>Variacion Bs.</b>',
					  'variacion'=>'<b>Variacion %</b>');

	$la_columnas=array('cuenta'=>'',
					   'denominacion'=>'',
					   'mesdes'=>'',
					   'meshas'=>'',
					   'variacion_bs'=>'',
					   'variacion'=>'');

	$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 'fontSize' => 8, // Tamaño de Letras
					 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
					 'showLines'=>1, // Mostrar Líneas
					 'shaded'=>0, // Sombra entre líneas
					 'colGap'=>1, // separacion entre tablas
					 //'width'=>520, // Ancho de la tabla
					 'width'=>550, // Ancho de la tabla
					 'maxWidth'=>550, // Ancho Máximo de la tabla
					 'xOrientation'=>'center', // Orientación de la tabla
					 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
					 			   'denominacion'=>array('justification'=>'center','width'=>220), // Justificación y ancho de la columna
								   'mesdes'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
								   'meshas'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
								   'variacion_bs'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
								   'variacion'=>array('justification'=>'center','width'=>65))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_cabecera*/
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_subtitulo($as_titulo,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//	   			   io_pdf // Objeto PDF
	//    Description: función que imprime el subtitulo
	//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
	// Fecha Creación: 26/09/2013
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;

	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
	$la_columnas = array('titulo'=>'');
	$la_data[0]   = array('titulo'=>"");
	$la_data[1]   = array('titulo'=>$as_titulo);
	$la_data[2]   = array('titulo'=>"");
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	unset($la_data);
}
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle($la_data,$li_sli,$li_letra,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//	   			   io_pdf // Objeto PDF
	//    Description: función que imprime el detalle
	//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
	// Fecha Creación: 26/09/2013
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;

	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => $li_letra, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>$li_sli, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>220), // Justificación y ancho de la columna
								       'mesdes'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
								       'meshas'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
								       'variacion_bs'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
								       'variacion'=>array('justification'=>'right','width'=>65))); // Justificación y ancho de la columna
	$la_columnas=array('cuenta'=>'',
					   'denominacion'=>'',
				       'mesdes'=>'',
				       'meshas'=>'',
				       'variacion_bs'=>'',
					   'variacion'=>'');
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	unset($la_data);
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------

function uf_print_firmas($io_pdf) 
{
	global $io_pdf;
	// cuadro inferior
    $io_pdf->setStrokeColor(0,0,0);
	$io_pdf->setLineStyle(1);
	
	$io_pdf->line(45,80,160,80);
	$io_pdf->line(415,80,535,80);		
	$io_pdf->addText(55,70,7,"     HERLES CARRERO"); // Agrego el nombre la persona presidente
	$io_pdf->addText(60,60,7,"       PRESIDENTE"); // Aqui el cargo
	$io_pdf->addText(430,70,7,"     WUILLIAN VILLALBA"); // nombre gte de administracion
	$io_pdf->addText(410,60,7,"             CONTADOR GENERAL"); // cargo
}



function uf_is_negative($ad_monto) 
{
	if ($ad_monto<0) {
		return "(".number_format(abs($ad_monto),0,",",".").")";
	}
	else{
		return number_format($ad_monto,0,",",".");
	}
}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
		// Fecha Creación: 26/09/2013
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../../shared/class_folder/class_sigesp_int.php");
require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
require_once("class_funciones_scg.php");
require_once("sigesp_scg_class_bal_general.php");

$io_report  = new sigesp_scg_class_bal_general();
$io_funciones=new class_funciones();
$io_fecha=new class_fecha();
$io_fun_scg=new class_funciones_scg();
$ia_niveles_scg[0]="";			
uf_init_niveles();
$li_total=count((array)$ia_niveles_scg)-1;
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
$ls_titulo="<b> BALANCE GENERAL(FORMA A) </b>";
$ls_titulo0="";
$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
$li_ano=substr($ldt_periodo,0,4);
		
$ls_etiqueta=$_GET["tipo"];
if($ls_etiqueta=="Mensual")
{
	$ls_combo=$_GET["mesdes"];
	$ls_combomes=$_GET["meshas"];
	$li_mesdes=substr($ls_combo,0,2);
	$li_meshas=substr($ls_combomes,0,2); 
    $ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas)." ".$li_ano;
	$ls_nombre_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes)." ".$li_ano;
	$ls_nombre_meshas=$io_fecha->uf_load_nombre_mes($li_meshas)." ".$li_ano;	
	$ls_last_day_des=$io_fecha->uf_last_day($li_mesdes,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);		
}
else
{
	$ls_combo=$_GET["mesdes"];
	$li_mesdes=substr($ls_combo,0,2);
	$li_meshas=substr($ls_combo,3,2); 
	$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas)." ".$li_ano;
	$ls_nombre_mesdes=$ls_meses;
	$ls_combomes=$_GET["meshas"];
	$li_mesdesf=substr($ls_combomes,0,2);
	$li_meshasf=substr($ls_combomes,3,2); 
    $ls_meses=$ls_meses."  /   ".$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf)." ".$li_ano;
	$ls_nombre_meshas=$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf)." ".$li_ano;
	$ls_last_day_des=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshasf,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);	
		
}

//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
// MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_nombre=$_SESSION["la_empresa"]["titulo"];
$ls_rif=$_SESSION["la_empresa"]["rifemp"];
// FIN DE LO MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA
$ls_titulo1="<b> ".$ls_nombre." </b>";
// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_titulo2="<b> RIF: ".$ls_rif." </b>";
$ls_titulo3="<b> ".$ls_meses."</b>";
// FIN DE LO AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA
$ls_titulo4="<b>(Expresado en Bs.)</b>";
//--------------------------------------------------------------------------------------------------------------------------------

$ls_pasivo=$_SESSION["la_empresa"]["pasivo"];
$ls_resultado=$_SESSION["la_empresa"]["resultado"];
$ls_capital=$_SESSION["la_empresa"]["capital"];
$ls_acreedora=trim($_SESSION["la_empresa"]["orden_h"]);

//--------------------------------------------------------------------------------------------------------------------------------
// Cargar datastore con los datos del reporte
$lb_valido=uf_insert_seguridad("<b>Balance General (FORMA A) Comparado en PDF</b>"); // Seguridad de Reporte
if($lb_valido)
{
	$rs_data=$io_report->uf_balance_general_sudeban($ldt_fecfindes,$ldt_fecfinhas);
}

if($rs_data->EOF) // no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else// Imprimimos el reporte
{
	$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(4,4.3,3,3); // Configuración de los margenes en centímetros
	// MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
	uf_print_encabezado_pagina($ls_titulo1,$ls_titulo2,$ls_titulo,$ls_titulo3,$ls_titulo4,$io_pdf); // Imprimimos el encabezado de la página
	// FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA 
	uf_print_cabecera($ls_nombre_mesdes,$ls_nombre_meshas,$io_pdf);
	
	//totales
	$ld_total_pasivopatrimonio=0;
	$ld_total_margenfinancierobruto=0;
	$ld_total_margenfinancieroneto=0;
	$ld_total_510=0;
	$ld_total_520=0;
	$ld_total_410=0;
	$ld_total_420=0;
	$ld_total_440=0;
	$ld_total_441=0;
	$ld_total_gasoper=0;
	$ld_total_530=0;
	$ld_total_430=0;
	$ld_total_540=0;
	$ld_total_450=0;
	$ld_total_470=0;
	
	$ld_total_pasivopatrimonio_has=0;
	$ld_total_margenfinancierobruto_has=0;
	$ld_total_margenfinancieroneto_has=0;
	$ld_total_510_has=0;
	$ld_total_520_has=0;
	$ld_total_410_has=0;
	$ld_total_420_has=0;
	$ld_total_440_has=0;
	$ld_total_441_has=0;
	$ld_total_gasoper_has=0;
	$ld_total_530_has=0;
	$ld_total_430_has=0;
	$ld_total_540_has=0;
	$ld_total_450_has=0;
	$ld_total_470_has=0;	
	
	//arreglos de data cuentas nivel 3
	$la_data_diponibilidades   = array();
	$la_data_diponibilidades[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_inversiones       = array();
	$la_data_inversiones[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_deudores	       = array();
	$la_data_deudores[]        = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_intereses         = array();
	$la_data_intereses[]       = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_otrasinversion    = array();
	$la_data_otrasinversion[]  = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_pasivopatri       = array();
	$la_data_pasivopatri[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_interesesxpagar   = array();
	$la_data_interesesxpagar[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$la_data_acumyotrospasivos   = array();
	$la_data_acumyotrospasivos[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_aportespatrim       = array();
	$la_data_aportespatrim[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_resultadosacum      = array();
	$la_data_resultadosacum[]    = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
	
	//arreglos de data cuentas nivel 2
	$la_data_diponibilidades2   = array();
	$la_data_inversiones2       = array();
	$la_data_deudores2          = array();
	$la_data_intereses2         = array();
	$la_data_otrasinversion2    = array();
	$la_data_pasivopatri2       = array();
	$la_data_otraobligacion     = array();
	$la_data_interesesxpagar2   = array();
	$la_data_interesesxpagar2[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_acumulaciones      = array();
	$la_data_310                = array();
	$la_data_311                = array();
	$la_data_311[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_330                = array();
	$la_data_340                = array();
	$la_data_340[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_350                = array();
	$la_data_350[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_360                = array();
	$la_data_360[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_370                = array();
	$la_data_370[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_300                = array();
	$la_data_300[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_610                = array();
	$la_data_610[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_620                = array();
	$la_data_620[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_810                = array();
	$la_data_810[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_820                = array();
	$la_data_820[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_160                = array();
	$la_data_160[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_170                = array();
	$la_data_170[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_180                = array();
	$la_data_180[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	
	//arreglos de data cuentas nivel 1
	$la_data_totalpasivos       = array();
	$la_data_totalpasivos[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_300                = array();
	$la_data_300[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_100                = array();
	$la_data_100[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	//informacion adicional
//	$ld_cap_suscrito=$io_report->uf_obtener_capital($ldt_fechas,"001");
//	$ld_cap_nopagado=$io_report->uf_obtener_capital($ldt_fechas,"002");
	//$la_data_311[] = array('cuenta'=>"",'denominacion'=>"CAPITAL SUSCRITO",'saldo'=>uf_is_negative($ld_cap_suscrito));
	//$la_data_311[] = array('cuenta'=>"",'denominacion'=>"CAPITAL NO PAGADO",'saldo'=>uf_is_negative($ld_cap_nopagado));
	
	//digito tipo de cuenta
	$ls_activo       = $_SESSION["la_empresa"]["activo"];
	$ls_pasivo       = $_SESSION["la_empresa"]["pasivo"];
	$ls_patrimonio   = $_SESSION["la_empresa"]["capital"];
	$ls_ingreso      = $_SESSION["la_empresa"]["ingreso"];
	$ls_gasto        = $_SESSION["la_empresa"]["gasto"];
	while (!$rs_data->EOF) 
	{
		$digtipcuenta = substr($rs_data->fields["sc_cuenta"],0,1);
		$codcuenta = substr($rs_data->fields["sc_cuenta"], 0, 3);
		$dencuenta = $rs_data->fields["denominacion"];
		$debedes         = $rs_data->fields["debedes"];
		$haberdes        = $rs_data->fields["haberdes"];
		$salcuentades    = $rs_data->fields["saldodes"];
		$debehas         = $rs_data->fields["debehas"];
		$haberhas        = $rs_data->fields["haberhas"];
		$salcuentahas    = $rs_data->fields["saldohas"];		
		$nivcuenta = $rs_data->fields["nivel"];
		
		if(($codcuenta!="119")&&($codcuenta!="129")&&($codcuenta!="139")&&($codcuenta!="149")&&($codcuenta!="159"))
		{
			switch ($digtipcuenta) 
			{
				case $ls_activo:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);					
					break;
			
				case $ls_pasivo:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);										
					break;
			
				case $ls_patrimonio:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);										
					break;
			
				case $ls_ingreso:
					if($debedes<$haberdes)
					{
						$salcuentades = abs($salcuentades);
					}
					if($debehas<$haberhas)
					{
						$salcuentahas = abs($salcuentahas);
					}					
					break;
				
				case $ls_gasto:
					if($debedes>$haberdes)
					{
						$salcuentades = abs($salcuentades);
					}
					if($debehas>$haberhas)
					{
						$salcuentahas = abs($salcuentahas);
					}					
					break;
			}
		}
		$li_variacion_bs=($salcuentahas-$salcuentades);
		if($salcuentades!=0)
		{
		   $li_variacion=($li_variacion_bs/$salcuentades)*100;
		}
		else
		{
		   $li_variacion=0;
		}		
		
		$arr_diponibilidades = array("111","113","114","116","119");
		$arr_inversiones     = array("121","122","123","124","125","129");
		$arr_deudores        = array("131","132","133","134","139");
		$arr_intereses       = array("141","142","143","144","145","149");
		$arr_otrasinversion  = array("151","159");
		//$arr_pasivopatri = array("240","241","242","243","244","245","246");//MARIAJOSEMORA
		$arr_pasivopatri = array("240");
		$arr_interesesxpagar = array("264","265");
		// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
		$arr_acumyotrospasivos = array("271","272","273","274","275","276"); 
		$arr_aportespatrim   = array("331","333");
		$arr_resultadosacum  = array("361","362");
		// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
		$arr_inggas          = array("421","422","423");
		if ($nivcuenta==1) 
		{
			if ($codcuenta == '100')
			{
				$la_data_100[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>TOTAL ".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '200')
			{
				$la_data_totalpasivos[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>TOTAL DEL ".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
				$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio + $salcuentades;
				$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has + $salcuentahas; 
			}
			elseif ($codcuenta == '300')//Modifivcado por Wagner 24/04/2014 por el concepto de la denominacion se repetia el total
			{
				$la_data_300[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
				$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio + $salcuentades;
				$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has + $salcuentahas;
			}
		}
		elseif ($nivcuenta==2)
		{
			if($codcuenta == '110')
			{
				$la_data_diponibilidades2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '120')
			{
				$la_data_inversiones2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '130')
			{
				$la_data_deudores2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '140')
			{
				$la_data_intereses2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '150')
			{
				$la_data_otrasinversion2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '160')
			{
				$la_data_160[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '170')
			{
				$la_data_170[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '180')
			{
				$la_data_180[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '240')
			{
				$la_data_pasivopatri2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '250')
			{
				$la_data_otraobligacion[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '260')
			{
				$la_data_interesesxpagar2[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '270')
			{
				$la_data_acumulaciones[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '310')
			{
				$la_data_310[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '330')
			{
				$la_data_330[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '340')
			{
				$la_data_340[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '350')
			{
				$la_data_350[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '360')
			{
				$la_data_360[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '370')//Modificado Wagner 08/04/2014
			{
				$acreedorades=uf_is_negative($salcuentades);
				$acreedorahas=uf_is_negative($salcuentahas);
				if($acreedorades > 0){ $acreedorades="(".$acreedorades.")"; }else {  $acreedorades; }				
				if($acreedorahas > 0){ $acreedorahas="(".$acreedorahas.")"; }else {  $acreedorahas; }	
				$la_data_370[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".$acreedorades."</b>",'meshas'=>"<b>".$acreedorahas."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '610')
			{
				$la_data_610[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '620')
			{
				$la_data_620[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '810')
			{
				$la_data_810[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			elseif ($codcuenta == '820')
			{
				$la_data_820[] = array('cuenta'=>"<b>".$codcuenta.".00</b>",'denominacion'=>"<b>".$dencuenta."</b>",'mesdes'=>"<b>".uf_is_negative($salcuentades)."</b>",'meshas'=>"<b>".uf_is_negative($salcuentahas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
			}
			if ($codcuenta=="510") 
			{
				$ld_total_510 = $salcuentades;
				$ld_total_510_has = $salcuentahas;
			}
			elseif ($codcuenta=="410")
			{
				$ld_total_410 = $salcuentades;
				$ld_total_410_has = $salcuentahas;
			}
			elseif ($codcuenta=="520")
			{
				$ld_total_520 = $salcuentades;
				$ld_total_520_has = $salcuentahas;
			}
			elseif ($codcuenta=="440")
			{
				$ld_total_440=$salcuentades;
				$ld_total_440_has=$salcuentahas;
			}
			elseif ($codcuenta=="530")
			{
				$ld_total_530  = $salcuentades;
				$ld_total_530_has  = $salcuentahas;
			}
			elseif ($codcuenta=="430")
			{
				$ld_total_430  = $salcuentades;
				$ld_total_430_has  = $salcuentahas;
			}
			elseif ($codcuenta=="540")
			{
				$ld_total_540  = $salcuentades;
				$ld_total_540_has = $salcuentahas;
			}
			elseif ($codcuenta=="450")
			{
				$ld_total_450  = $salcuentades;
				$ld_total_450_has  = $salcuentahas;
			}
			elseif ($codcuenta=="470")
			{
				$ld_total_470  = $salcuentades;
				$ld_total_470_has  = $salcuentahas;
			}
		}
		elseif ($nivcuenta==3)
		{
			if (in_array($codcuenta, $arr_diponibilidades)) 
			{
				$la_data_diponibilidades[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_inversiones)) 
			{
				$la_data_inversiones[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_deudores))
			{
				$la_data_deudores[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_intereses))
			{
				$la_data_intereses[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_otrasinversion))
			{
				$la_data_otrasinversion[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_pasivopatri))
			{
				$la_data_pasivopatri[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_interesesxpagar))
			{
				$la_data_interesesxpagar[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
			elseif (in_array($codcuenta, $arr_acumyotrospasivos))
			{
				$la_data_acumyotrospasivos[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
			elseif ($codcuenta == '311')
			{
				$la_data_311[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
			elseif (in_array($codcuenta, $arr_aportespatrim))
			{
				$la_data_aportespatrim[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_resultadosacum))
			{
				$la_data_resultadosacum[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}			
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA			
			elseif (in_array($codcuenta, $arr_inggas))
			{
				$ld_total_420     = $ld_total_420 + $salcuentades;
				$ld_total_420_has = $ld_total_420_has + $salcuentahas;
			}
		}
		
		$rs_data->MoveNext();
	}

	//CALCULANDO GASTO OPERATIVO
	$ld_total_margenfinancierobruto = $ld_total_510-$ld_total_410;
	$ld_total_margenfinancierobruto_has = $ld_total_510_has-$ld_total_410_has;
	$ld_total_margenfinancieroneto  = ($ld_total_margenfinancierobruto + $ld_total_520) - $ld_total_420;
	$ld_total_margenfinancieroneto_has  = ($ld_total_margenfinancierobruto_has + $ld_total_520_has) - $ld_total_420_has;	
	$ld_total_marinter = $ld_total_margenfinancieroneto - $ld_total_440;
	$ld_total_marinter_has = $ld_total_margenfinancieroneto_has - $ld_total_440_has;	
	$ld_total_marnegocio = ($ld_total_marinter + $ld_total_530) - $ld_total_430;
	$ld_total_marnegocio_has = ($ld_total_marinter_has + $ld_total_530_has) - $ld_total_430_has;	
	$ld_total_brutoantimp = ($ld_total_marnegocio+ $ld_total_540) - $ld_total_450;
	$ld_total_brutoantimp_has = ($ld_total_marnegocio_has+ $ld_total_540_has) - $ld_total_450_has;	
	$ld_total_neto = $ld_total_brutoantimp - $ld_total_470;
	$ld_total_neto_has = $ld_total_brutoantimp_has - $ld_total_470_has;	
	$li_variacion_bs=$ld_total_neto_has-$ld_total_neto;	
	if($ld_total_neto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_neto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$la_data_gesoperativa   = array();
	$la_data_gesoperativa[] = array('cuenta'=>"",'denominacion'=>"GESTION OPERATIVA",'mesdes'=>uf_is_negative($ld_total_neto),'meshas'=>uf_is_negative($ld_total_neto_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	//CALCULANDO GASTO OPERATIVO
	$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio+$ld_total_neto;
	$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has+$ld_total_neto_has;
	$li_variacion_bs=$ld_total_pasivopatrimonio_has-$ld_total_pasivopatrimonio;	
	if($ld_total_pasivopatrimonio!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_pasivopatrimonio)*100;
	}
	else
	{
	   $li_variacion=0;
	}		
	$la_data_totalpasivopatrimonio[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_totalpasivopatrimonio[] = array('cuenta'=>'','denominacion'=>'TOTAL DE PASIVO Y PATRIMONIO','mesdes'=>uf_is_negative($ld_total_pasivopatrimonio),'meshas'=>uf_is_negative($ld_total_pasivopatrimonio_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	
	
	
	//IMPRIMIENDO ACTIVO
	$ls_subtitulo='<b>ACTIVO</b>';
	uf_print_subtitulo($ls_subtitulo, $io_pdf);
	
	//IMPRIMIENDO DISPONIBILIDADES (110)
	uf_print_detalle($la_data_diponibilidades2,2,7, $io_pdf);
	$la_data_diponibilidades[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_diponibilidades,0,6, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES (120)
	uf_print_detalle($la_data_inversiones2,2,7, $io_pdf);
	$la_data_inversiones[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_inversiones,0,6, $io_pdf);
	
	//IMPRIMIENDO DEUDORES (130)
	uf_print_detalle($la_data_deudores2,2,7, $io_pdf);
	$la_data_deudores[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_deudores,0,6, $io_pdf);
	
	//IMPRIMIENDO INTERESES X COBRAR (140)
	uf_print_detalle($la_data_intereses2,2,7, $io_pdf);
	$la_data_intereses[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_intereses,0,6, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (150)
	uf_print_detalle($la_data_otrasinversion2,2,7, $io_pdf);
	uf_print_detalle($la_data_otrasinversion,0,6, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (160)
	uf_print_detalle($la_data_160,1,7, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (170)
	uf_print_detalle($la_data_170,1,7, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (180)
	uf_print_detalle($la_data_180,1,7, $io_pdf);
	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (100)
	uf_print_detalle($la_data_100,1,7, $io_pdf);
	
	//IMPRIMIENDO PASIVO
	$ls_subtitulo='<b>PASIVO</b>';
	uf_print_subtitulo($ls_subtitulo, $io_pdf);
	
	//IMPRIMIENDO FINANCIAMIENTOS OBTENIDOS (240)
	uf_print_detalle($la_data_pasivopatri2,2,6, $io_pdf);
	$la_data_pasivopatri[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_pasivopatri,0,6, $io_pdf);
	
	//IMPRIMIENDO OTRAS OBLIGACIONES (250)
	uf_print_detalle($la_data_otraobligacion,2,6, $io_pdf);
	
	//IMPRIMIENDO INTERESES POR PAGAR (260)
	uf_print_detalle($la_data_interesesxpagar2,1,6, $io_pdf);
	$la_data_interesesxpagar[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_interesesxpagar,0,6, $io_pdf);
	
	//IMPRIMIENDO ACUMULACIONES (270)
	uf_print_detalle($la_data_acumulaciones,2,6, $io_pdf);
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$la_data_acumyotrospasivos[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_acumyotrospasivos,0,6, $io_pdf);	
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
	
	//IMPRIMIENDO TOTAL PASIVO (200)
	uf_print_detalle($la_data_totalpasivos,1,6, $io_pdf);
	
	uf_print_detalle($la_data_gesoperativa,0,6, $io_pdf);
	
	//IMPRIMIENDO PATRIMONIO
	$ls_subtitulo='<b>PATRIMONIO</b>';
	uf_print_subtitulo($ls_subtitulo, $io_pdf);
	
	//IMPRIMIENDO CAPITAL (310)
	uf_print_detalle($la_data_310,2,6, $io_pdf);
	$la_data_311[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_311,0,6, $io_pdf);
	
	uf_print_detalle($la_data_330,2,6, $io_pdf);
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$la_data_aportespatrim[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_aportespatrim,0,6, $io_pdf);	
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA	
	
	uf_print_detalle($la_data_340,1,6, $io_pdf);
	
	//uf_print_detalle($la_data_350,1,6, $io_pdf);
	
	uf_print_detalle($la_data_360,1,6, $io_pdf);
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$la_data_resultadosacum[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_resultadosacum,0,6, $io_pdf);	
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA		
	
	uf_print_detalle($la_data_370,1,6, $io_pdf);
	
	uf_print_detalle($la_data_300,1,6, $io_pdf);
	
	uf_print_detalle($la_data_totalpasivopatrimonio,0,6, $io_pdf);
	
	uf_print_detalle($la_data_610,1,6, $io_pdf);
	uf_print_detalle($la_data_620,1,6, $io_pdf);
	uf_print_detalle($la_data_810,1,6, $io_pdf);
	uf_print_detalle($la_data_820,1,6, $io_pdf);
	uf_print_firmas($io_pdf);

	
	
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
