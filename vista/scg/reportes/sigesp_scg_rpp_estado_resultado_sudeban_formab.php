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
	$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_estado_resultado_formab.html",$ls_descripcion);
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
	
	
	// cuadro inferior
    $io_pdf->setStrokeColor(0,0,0);
	$io_pdf->setLineStyle(1);
	// MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
    $io_pdf->line(45,80,160,80);
    $io_pdf->line(415,80,535,80);
    $io_pdf->addText(49,140,7,"* 440 Excepto 441"); // Agregar el título		
    $io_pdf->addText(55,70,7,"    HERLES CARRERO"); // Agregar el título
	$io_pdf->addText(60,60,7,"       PRESIDENTE"); // Agregar el título
	$io_pdf->addText(430,70,7,"    WUILLIAN VILLALBA"); // Agregar el título
	$io_pdf->addText(410,60,7,"              CONTADOR GENERAL"); // Agregar el título
	// FIN DE LO MODIFICADO POR OFIMATICA DE VENEZUELA
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
function uf_print_subtitulo($as_titulo,$io_pdf){
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
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
	$la_columnas = array('titulo'=>'');
	$la_data[]   = array('titulo'=>$as_titulo);
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	unset($la_data);
}
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_subtitulo_monto($as_titulo,$as_montodes,$as_montohas,$ai_variacion_bs,$ai_variacion,$io_pdf)
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
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>290),
									   'montodes'=>array('justification'=>'right','width'=>65),
									   'montohas'=>array('justification'=>'right','width'=>65),
									   'variacion_bs'=>array('justification'=>'right','width'=>65),
									   'variacion'=>array('justification'=>'right','width'=>65))); // Justificación y ancho de la columna
	$la_columnas = array('titulo'=>'','montodes'=>'','montohas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data[]   = array('titulo'=>"<b>".$as_titulo."</b>",'montodes'=>"<b>".$as_montodes."</b>",'montohas'=>"<b>".$as_montohas."</b>",'variacion_bs'=>"<b>".$ai_variacion_bs."</b>",'variacion'=>"<b>".$ai_variacion."</b>");
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	unset($la_data);
}
//--------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle($la_data,$li_sli,$li_letra,$io_pdf){
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
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
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

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_firmas($io_pdf) 
{
	global $io_pdf;
	// cuadro inferior
    $io_pdf->setStrokeColor(0,0,0);
	$io_pdf->setLineStyle(1);
	
	$io_pdf->line(45,80,130,80);
	$io_pdf->line(415,80,535,80);		
	$io_pdf->addText(55,70,7,"MARYZETH PUENTE"); // Agregar el título
	$io_pdf->addText(60,60,7,"  PRESIDENTE"); // Agregar el título
	$io_pdf->addText(430,70,7,"MARIA ANGELINA GERMINO"); // Agregar el título
	$io_pdf->addText(430,60,7,"   CONTADOR GENERAL   "); // Agregar el título
}

function uf_is_negative($ad_monto,$ai_decimales=0)
{
	if ($ad_monto<0) 
	{
		return "(".number_format(abs($ad_monto),$ai_decimales,",",".").")";
	}
	else
	{
		return number_format($ad_monto,$ai_decimales,",",".");
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
function cuentas_nat($saldo)//Función creada Por Wagner 15/05/2014
{
  if (substr($saldo, 0, 1)!="-") 
  {
    return "(".number_format(abs($saldo),"0",",",".").")";
  }
  else
  {
    return number_format(abs($saldo),"0",",",".");
  }
			
}

function cuentas_nat2($debe,$haber,$saldo)//Función creada Por Wagner 15/05/2014
{
	if ($debe>$haber) 
	{
		return "(".number_format(abs($saldo),"0",",",".").")";
	}
	else
	{
		return number_format(abs($saldo),"0",",",".");
	}
			
}
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("class_funciones_scg.php");
require_once("sigesp_scg_reporte.php");

$io_funciones = new class_funciones();
$io_fecha     = new class_fecha();
$io_fun_scg   = new class_funciones_scg();
$io_report    = new sigesp_scg_reporte();
$ia_niveles_scg[0]="";			
uf_init_niveles();
$li_total=count((array)$ia_niveles_scg)-1;
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
$ls_titulo="<b> ESTADO DE RESULTADOS (FORMA B) </b>";
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
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01";
	}
	else
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01"." 00:00:00";
	}
	$ls_last_day_des=$io_fecha->uf_last_day($li_mesdes,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinihas=$li_ano."-".$li_meshas."-01";
	}
	else
	{
		$ldt_fecinihas=$li_ano."-".$li_meshas."-01"." 00:00:00";
	}
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
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01";
	}
	else
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01"." 00:00:00";
	}
	$ls_last_day_des=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinihas=$li_ano."-".$li_mesdesf."-01";
	}
	else
	{
		$ldt_fecinihas=$li_ano."-".$li_mesdesf."-01"." 00:00:00";
	}
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshasf,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);	
		
}

//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
// MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_nombre=$_SESSION["la_empresa"]["titulo"];
$ls_rif=$_SESSION["la_empresa"]["rifemp"];
// FIN DE LO MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA
$ld_fecdes=$io_funciones->uf_convertirfecmostrar($fecdes);
$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
$ls_titulo1="<b> ".$ls_nombre." </b>";
// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_titulo2="<b> RIF: ".$ls_rif." </b>";
$ls_titulo3="<b> ".$ls_meses."</b>";
// FIN DE LO AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA
$ls_titulo4="<b>(Expresado en Bs.)</b>";
//--------------------------------------------------------------------------------------------------------------------------------
$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
$io_pdf->ezSetCmMargins(4,5,3,3); // Configuración de los margenes en centímetros
// MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
uf_print_encabezado_pagina($ls_titulo1,$ls_titulo2,$ls_titulo,$ls_titulo3,$ls_titulo4,$io_pdf); // Imprimimos el encabezado de la página
uf_print_cabecera($ls_nombre_mesdes,$ls_nombre_meshas,$io_pdf);
$lb_valido=uf_insert_seguridad("<b>Estado de Resultado (FORMA B) Comparado en PDF</b>"); // Seguridad de Reporte
if($lb_valido)
{
	$lb_valido=$io_report->uf_scg_reporte_estado_de_resultado_sudeban($ldt_fecinides,$ldt_fecfindes,$ldt_fecinihas,$ldt_fecfinhas);
}

if(($io_report->rs_data_comp->EOF)||(!$lb_valido)) // Existe algún error ó no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else
{
    // BUSCAMOS LA TASA DE CAMBIO VIGENTE A LA FECHA DE LA MONEDA POR DEFECTO (DOLAR)
    $li_tasa_cambio=$io_report->uf_buscar_tasa('002');
	//

	//totales
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
	$la_data_ingfin   = array();
	$la_data_ingfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_gasfin   = array();
	$la_data_gasfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_inggas = array();
	$la_data_441    = array();
	
	//arreglos de data cuentas nivel 2
	$la_data_510   = array();
	$la_data_410   = array();
	$la_data_520   = array();
	$la_data_520[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// AGREGADO POR OFIMATICA DE VENEZUELA 26/03/2013
	$la_data_420   = array();
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA	
	$la_data_440 = array();
	$la_data_530 = array();
	$la_data_530[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_430 = array();
	$la_data_540 = array();
	$la_data_540[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_450 = array();
	$la_data_470 = array();
	$la_data_470[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	//arreglos de data cuentas totales
	$la_data_totalmarfinbru = array();
	$la_data_totalmarfinnet = array();
	$la_data_menos          = array();
	$la_data_gastosoperati  = array();
	$la_data_margeninter    = array();

	//digito tipo de cuenta
	$ls_activo       = $_SESSION["la_empresa"]["activo"];
	$ls_pasivo       = $_SESSION["la_empresa"]["pasivo"];
	$ls_patrimonio   = $_SESSION["la_empresa"]["capital"];
	$ls_ingreso      = $_SESSION["la_empresa"]["ingreso"];
	$ls_gasto        = $_SESSION["la_empresa"]["gasto"];
	while(!$io_report->rs_data_comp->EOF)
	{
		$digtipcuenta = substr($io_report->rs_data_comp->fields["sc_cuenta"],0,1);
		$codcuenta    = substr($io_report->rs_data_comp->fields["sc_cuenta"],0,3);
		$denominacion = $io_report->rs_data_comp->fields["denominacion"];
		$debedes      = $io_report->rs_data_comp->fields["debedes"];
		$haberdes     = $io_report->rs_data_comp->fields["haberdes"];
		$montodes     = $io_report->rs_data_comp->fields["saldodes"];
		$debehas      = $io_report->rs_data_comp->fields["debehas"];
		$haberhas     = $io_report->rs_data_comp->fields["haberhas"];
		$montohas     = $io_report->rs_data_comp->fields["saldohas"];		
		$nivel        = $io_report->rs_data_comp->fields["nivel"];
		$otmontodes   = $io_report->rs_data_comp->fields["saldodes"];//variables creadas por wagner 28/08/2014
		$otmontohas   = $io_report->rs_data_comp->fields["saldohas"];//variables creadas por wagner 28/08/2014
		switch ($digtipcuenta) 
		{
			case $ls_activo:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_pasivo:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_patrimonio:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_ingreso:
				if($debedes<$haberdes)
				{
					$montodes = abs($montodes);
				}
				if($debehas<$haberhas)
				{
					$montohas = abs($montohas);
				}
				
				break;
				
			case $ls_gasto:
				if($debedes>$haberdes)
				{
					$montodes = abs($montodes);
				}
				if($debehas>$haberhas)
				{
					$montohas = abs($montohas);
				}				
				break;
		}
		$li_variacion_bs=($montohas-$montodes);
		if($montodes!=0)
		{
		   $li_variacion=($li_variacion_bs/$montodes)*100;
		}
		else
		{
		   $li_variacion=0;
		}
		$arr_ingfin   = array("511","512","513","514","519");
		$arr_gasfin   = array("414","415","419");
		$arr_inggas   = array("421","422","423");
		
		if ($nivel==2) 
		{
			if ($codcuenta=="510") 
			{
				$la_data_510[] = array('cuenta'=>"<b>".$codcuenta." . 00</b>",'denominacion'=>"<b>".$denominacion."</b>",'mesdes'=>"<b>".uf_is_negative($montodes)."</b>",'meshas'=>"<b>".uf_is_negative($montohas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
				$ld_total_510 = $montodes;
				$ld_total_510_has = $montohas;
			}
			elseif ($codcuenta=="410")
			{
				$la_data_410[] = array('cuenta'=>"<b>".$codcuenta." . 00</b>",'denominacion'=>"<b>".$denominacion."</b>",'mesdes'=>"<b>".uf_is_negative($montodes)."</b>",'meshas'=>"<b>".uf_is_negative($montohas)."</b>",'variacion_bs'=>"<b>".uf_is_negative($li_variacion_bs)."</b>",'variacion'=>"<b>".uf_is_negative($li_variacion)."</b>");
				$ld_total_410=$montodes;
				$ld_total_410_has=$montohas;
			}
			elseif ($codcuenta=="520")
			{
				$la_data_520[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_520  = $ld_total_520 + $montodes;
				$ld_total_520_has  = $ld_total_520_has + $montohas;
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA 26/03/2013
			elseif ($codcuenta=="420")
			{
				$la_data_420[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat($otmontodes),'meshas'=>cuentas_nat($otmontohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_420=$montodes;
				$ld_total_420_has=$montohas;
			}
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
			elseif ($codcuenta=="440")
			{
				$la_data_440[] = array('cuenta'=>'','denominacion'=>'GASTOS DE TRANSFORMACION','mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_440=$montodes;
				$ld_total_440_has=$montohas;
			}
			elseif ($codcuenta=="530")
			{		      
				$la_data_530[] = array('cuenta'=>$codcuenta.". 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat($otmontodes),'meshas'=>cuentas_nat($otmontohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_530  =  $debedes >$haberdes ? (-1 * $montodes) : $montodes;
				$ld_total_530_has  = $debehas >$haberhas ? (-1 * $montohas) : $montohas; //$montohas;
			}
			elseif ($codcuenta=="430")
			{
			      
				$la_data_430[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat($otmontodes),'meshas'=>cuentas_nat($otmontohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_430  = $montodes;
				$ld_total_430_has  =$montohas;
				
			}
			elseif ($codcuenta=="540")
			{
				$la_data_540[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_540  = $montodes;
				$ld_total_540_has  = $montohas;
			}
			elseif ($codcuenta=="450")
			{
				$la_data_450[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat($otmontodes),'meshas'=>cuentas_nat($otmontohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_450  = $montodes;//uf_is_negative($montohas); 
				$ld_total_450_has  = $montohas;
			}
			elseif ($codcuenta=="470")
			{
				$la_data_470[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_470  = $montodes;
				$ld_total_470_has  = $montohas;
			}
		}
		elseif ($nivel==3)
		{
			if (in_array($codcuenta, $arr_ingfin)) 
			{
				if($codcuenta=="514"){//aqui
				  $la_data_ingfin[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat2($debedes, $haberdes,$montodes),'meshas'=>cuentas_nat2($debehas,$haberhas, $montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				}
				else
				{
				   $la_data_ingfin[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				}
			}
			elseif (in_array($codcuenta, $arr_gasfin))
			{
				$la_data_gasfin[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));	
			}
			elseif (in_array($codcuenta, $arr_inggas))//aqui van la relaciones con las cuentas 420 421
			{
				$la_data_inggas[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>cuentas_nat($otmontodes),'meshas'=>cuentas_nat($otmontohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
//				$ld_total_420     = $ld_total_420 + $monto;
			}
			elseif ($codcuenta=="441")
			{
				$la_data_441[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_441  = $montodes;
				$ld_total_441_has  = $montohas;
			}
		}
		
		$io_report->rs_data_comp->MoveNext();
	}
	$ld_total_margenfinancierobruto = $ld_total_510-$ld_total_410;
	$ld_total_margenfinancierobruto_has = $ld_total_510_has-$ld_total_410_has;
	$ld_total_margenfinancieroneto  = ($ld_total_margenfinancierobruto + $ld_total_520) - $ld_total_420;
	$ld_total_margenfinancieroneto_has  = ($ld_total_margenfinancierobruto_has + $ld_total_520_has) - $ld_total_420_has;
	$la_data_menos[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_menos[] = array('cuenta'=>'','denominacion'=>'MENOS:','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$ld_total_gasoper = $ld_total_440 - $ld_total_441;
	$ld_total_gasoper_has = $ld_total_440_has - $ld_total_441_has;
	$li_variacion_bs=$ld_total_gasoper_has-$ld_total_gasoper;
	if($ld_total_gasoper!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_gasoper)*100;
	}
	else
	{
	   $li_variacion=0;
	}
	$la_data_gastosoperati[] = array('cuenta'=>'     *','denominacion'=>'GASTOS GENERALES Y ADMINISTRATIVOS','mesdes'=>uf_is_negative($ld_total_gasoper),'meshas'=>uf_is_negative($ld_total_gasoper_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	$ld_total_marinter = $ld_total_margenfinancieroneto - $ld_total_440;
	$ld_total_marinter_has = $ld_total_margenfinancieroneto_has - $ld_total_440_has;
	//$ld_total_marinter ." ". (-1*$ld_total_530)." ".$ld_total_430."<br>";
	$ld_total_marnegocio = ($ld_total_marinter + $ld_total_530) - $ld_total_430;
	$ld_total_marnegocio_has = ($ld_total_marinter_has + $ld_total_530_has) - $ld_total_430_has;
	$ld_total_brutoantimp = ($ld_total_marnegocio+ $ld_total_540) - $ld_total_450;
	$ld_total_brutoantimp_has = ($ld_total_marnegocio_has+ $ld_total_540_has) - $ld_total_450_has;
	$ld_total_neto = $ld_total_brutoantimp - $ld_total_470;
    $ld_total_neto_has = $ld_total_brutoantimp_has - $ld_total_470_has;	
	
		
	//IMPRIMIENDO LAS CUENTAS 510
	uf_print_detalle($la_data_510,2,7,$io_pdf);
	$la_data_ingfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_ingfin,0,6,$io_pdf);
	
	//IMPRIMIENDO LAS CUENTAS 410
	uf_print_detalle($la_data_410,2,7,$io_pdf);
	$la_data_gasfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_gasfin,0,6,$io_pdf);
	
	//IMPRIMIENDO MARGEN FINANCIERO BRUTO
	$li_variacion_bs=$ld_total_margenfinancierobruto_has-$ld_total_margenfinancierobruto;
	if($ld_total_margenfinancierobruto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_margenfinancierobruto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	uf_print_subtitulo_monto("MARGEN FINANCIERO BRUTO", uf_is_negative($ld_total_margenfinancierobruto),uf_is_negative($ld_total_margenfinancierobruto_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion), $io_pdf);
	uf_print_detalle($la_data_520,0,6,$io_pdf);
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	uf_print_detalle($la_data_420,0,6,$io_pdf);
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
	$la_data_inggas[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_inggas,0,6,$io_pdf);
	
	//IMPRIMIENDO MARGEN FINANCIERO NETO
	$li_variacion_bs=$ld_total_margenfinancieroneto_has-$ld_total_margenfinancieroneto;
	if($ld_total_margenfinancieroneto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_margenfinancieroneto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	uf_print_subtitulo_monto("MARGEN FINANCIERO NETO", uf_is_negative($ld_total_margenfinancieroneto),uf_is_negative($ld_total_margenfinancieroneto_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion), $io_pdf);
	uf_print_detalle($la_data_menos,0,6,$io_pdf);
	$la_data_440[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_440,0,6,$io_pdf);
	uf_print_detalle($la_data_441,0,6,$io_pdf);
	$la_data_gastosoperati[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_gastosoperati,0,6,$io_pdf);
		
	//IMPRIMIENDO MARGEN DE INTERMEDIACION
	$li_variacion_bs=$ld_total_marinter_has-$ld_total_marinter;
	if($ld_total_marinter!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_marinter)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	uf_print_subtitulo_monto("MARGEN DE INTERMEDIACION", uf_is_negative($ld_total_marinter),uf_is_negative($ld_total_marinter_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion), $io_pdf);
	uf_print_detalle($la_data_530,0,6,$io_pdf);
	$la_data_430[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_430,0,6,$io_pdf);
	
	//IMPRIMIENDO MARGEN DEL NEGOCIO 
	$li_variacion_bs=$ld_total_marnegocio_has-$ld_total_marnegocio;
	if($ld_total_marnegocio!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_marnegocio)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	uf_print_subtitulo_monto("MARGEN DEL NEGOCIO", uf_is_negative($ld_total_marnegocio),uf_is_negative($ld_total_marnegocio_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion),$io_pdf);
	uf_print_detalle($la_data_540,0,6,$io_pdf);
	$la_data_450[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_450,0,6,$io_pdf);
	
	//
	$li_variacion_bs=$ld_total_brutoantimp_has-$ld_total_brutoantimp;
	if($ld_total_brutoantimp!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_brutoantimp)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	uf_print_subtitulo_monto("RESULTADO BRUTO ANTES DE IMPUESTO", uf_is_negative($ld_total_brutoantimp),uf_is_negative($ld_total_brutoantimp_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion), $io_pdf);
	$la_data_470[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	uf_print_detalle($la_data_470,0,6,$io_pdf);
	
	//
	$li_variacion_bs=$ld_total_neto_has-$ld_total_neto;
	if($ld_total_neto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_neto)*100;
	}
	else
	{
	   $li_variacion=0;
	}		
	uf_print_subtitulo_monto("RESULTADO NETO", uf_is_negative($ld_total_neto), uf_is_negative($ld_total_neto_has),uf_is_negative($li_variacion_bs),uf_is_negative($li_variacion),$io_pdf);
}

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
unset($io_report);
unset($io_funciones);
?>
