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
ini_set('memory_limit','1024M');
ini_set('max_execution_time ','0');
//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo,$as_procede,$ad_fecha,$as_fuentefin="",$io_pdf)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezado_pagina
	//		    Acess: private
	//	    Arguments: as_titulo // T?tulo del Reporte
	//	    		   io_pdf    // Instancia de objeto pdf
	//    Description: Funci?n que imprime los encabezados por p?gina
	//	   Creado Por: Ing. N?stor Falcon
	// Fecha Creaci?n: 18/05/2007.
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
	$tm=306-($li_tm/2);
	$io_pdf->rectangle(20,690,570,80);
	$io_pdf->addText(30,750,8,"ORGANO:");
	$io_pdf->addText(383,750,8,"<b>P?GINA N?.</b>");
	$io_pdf->addText(510,745,8,"COD.");
	$io_pdf->addText(25,710,7,"FECHA:".$ad_fecha);
	$io_pdf->addText(25,695,7,"FUENTE DE FINANCIAMIENTO: ".$as_fuentefin);
	//$io_pdf->addText(180,730,10,$as_titulo);
	$io_pdf->addText($tm,730,10,$as_titulo);
	$io_pdf->rectangle(20,625,570,60);
	$io_pdf->addText(30,660,7,"INSUBSISTENCIA");$io_pdf->rectangle(95,658,10,10);
	$io_pdf->addText(130,660,7,"REDUCCI?N");$io_pdf->rectangle(180,658,10,10);
	$io_pdf->addText(220,675,7,"<b>RECURSOS ADICIONALES</b>");
	$io_pdf->addText(223,660,7,"CR?DITO ADICIONAL");$io_pdf->rectangle(300,658,10,10);
	$io_pdf->addText(238,638,7,"RECTIFICACI?N");$io_pdf->rectangle(300,635,10,10);
	$io_pdf->addText(470,675,7,"<b>TRASPASO</b>");
	$io_pdf->addText(450,660,7,"GASTOS CORRIENTES");$io_pdf->rectangle(540,658,10,10);
	$io_pdf->addText(460,638,7,"GASTOS DE CAPITAL");$io_pdf->rectangle(540,635,10,10);
	//Impresi?n de las X para el Marcado de Operacion.

	switch ($as_procede){
		case 'SPGINS':
			$io_pdf->addText(97.5,660.5,7,"<b>X</b>");//Insubsistencia
			break;
		case 'SPGCRA':
			$io_pdf->addText(302.5,660.5,7,"<b>X</b>");//Cr?dito Adicional.
			break;
		case 'SPGREC':
			$io_pdf->addText(302.5,637.5,7,"<b>X</b>");//Rectificacion.
			break;
		case 'SPGTRA':
			$io_pdf->addText(542.5,660.5,7,"<b>X</b>");//Traspaso.
			break;
	}

	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezado_pagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle($la_data,$io_pdf)
{
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de informaci?n
	//	   			   io_pdf // Objeto PDF
	//    Description: funci?n que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barrag?n
	// Fecha Creaci?n: 13/09/2006
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_pdf->setStrokeColor(1,1,1);
	$io_pdf->ezSetY(615);
	$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho M?ximo de la tabla
						 'xPos'=>305, // Orientaci?n de la tabla
						 'cols'=>array('proyecto'=>array('justification'=>'center','width'=>65), // Justificaci?n y ancho de la 
						 			   'accion'=>array('justification'=>'center','width'=>45),
									   'ejecutora'=>array('justification'=>'center','width'=>25),
									   'partida'=>array('justification'=>'center','width'=>25),
									   'generica'=>array('justification'=>'center','width'=>25),
									   'especifica'=>array('justification'=>'center','width'=>25), 
									   'subespecifica'=>array('justification'=>'center','width'=>25),
									   'denominacion'=>array('justification'=>'left','width'=>255),
									   'monto'=>array('justification'=>'right','width'=>80))); // Justificaci?n y ancho 

	$la_columnas = array('proyecto'=>'<b>PROYECTO O ACCION CENTRALIZADA</b>',
		                     'accion'=>'<b>ACCI?N ESPEC?FICA</b>',
							 'ejecutora'=>'<b>UEL</b>',
							 'partida'=>'<b>PART</b>',
							 'generica'=>'<b>GEN</b>',
			                 'especifica'=>'<b>ESP</b>',
							 'subespecifica'=>'<b>SUB</b>',
							 'denominacion'=>'<b>DENOMINACI?N</b>',
							 'monto'=>'<b>BOL?VARES</b>');

	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_init_niveles()
{	///////////////////////////////////////////////////////////////////////////////////////////////////////
//	   Function: uf_init_niveles
//	     Access: public
//	    Returns: vacio
//	Description: Este m?todo realiza una consulta a los formatos de las cuentas
//               para conocer los niveles de la escalera de las cuentas contables
//////////////////////////////////////////////////////////////////////////////////////////////////////
global $io_funcion,$ia_niveles_scg;

$ls_formato  = ""; $li_posicion=0; $li_indice=0;
$ls_formato  = trim($_SESSION["la_empresa"]["formpre"])."-";
$li_posicion = 1 ;
$li_indice   = 1 ;
$li_posicion = $io_funcion->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
do
{
	$ia_niveles_scg[$li_indice] = $li_posicion;
	$li_indice   = $li_indice+1;
	$li_posicion = $io_funcion->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
} while ($li_posicion>=0);
}// end function uf_init_niveles
//-----------------------------------------------------------------------------------------------------------------------------------

function uf_print_pie_de_pagina($io_pdf)
{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	//	   Function: uf_print_pie_de_pagina
	//	     Access: public
	//	    Returns: vacio
	//	Description: M?todo que imprime el pie de pagina de Forma 0301 De Modificaciones Presupuestarias.
	//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	 
	$io_pdf->Rectangle(19,100,570,10);

	$io_pdf->Rectangle(19,40,570,60);
	$io_pdf->addText(120,102,8,"<b>PRESUPUESTO</b>"); // Agregar el t?tulo
	$io_pdf->addText(400,102,8,"<b>CONTRALORA MUNICIPAL</b>"); // Agregar el t?tulo

}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("sigesp_spg_funciones_reportes.php");
require_once("sigesp_spg_reportes_class.php");

$io_report      = new sigesp_spg_reportes_class();
$io_funrep      = new sigesp_spg_funciones_reportes();
$io_funcion     = new class_funciones();
$io_fecha       = new class_fecha();
$io_conect      = new sigesp_include();
$con            = $io_conect-> uf_conectar ();
$io_msg         = new class_mensajes(); //Instanciando la clase mensajes
$io_sql         = new class_sql($con); //Instanciando  la clase sql
$lb_valido      = true;
$io_dsreport    = new class_datastore();
$ls_codemp      = $_SESSION["la_empresa"]["codemp"];
$ls_forpre      = $_SESSION["la_empresa"]["formpre"];
$ls_procede     = $_GET["procede"];
$ls_comprobante = $_GET["comprobante"];
$ld_fecha       = $_GET["fecha"];
$arrResultado=$io_report->uf_init_niveles($ia_niveles_scg,$li_posicion);
$ia_niveles_scg=$arrResultado['ia_niveles_scg'];
$li_posicion=$arrResultado['li_posicion'];

if ($lb_valido==false)
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else
{
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(6.2,4,3,3); // Configuraci?n de los margenes en cent?metros
	$ls_titulo = "<b>SOLICITUD DE MODIFICACI?N PRESUPUESTARIA N?. ".$ls_comprobante."</b>";
	$arrResultado = $io_report->uf_obtener_fuente_financiamiento_comprobante($ls_comprobante,$ls_procede,$ld_fecha);
	$ls_fuentefin =$arrResultado['ls_fuente_financiamiento'];
	uf_print_encabezado_pagina($ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$io_pdf); // Imprimimos el encabezado de la p?gina
	$li_total   = count($ia_niveles_scg);
	$li_numrows = 0;
	$arrResultado= $io_report->uf_select_dt_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ld_fecha,$li_numrows,$rs_dat);
	$li_numrows=$arrResultado['ai_numrows'];
	$rs_dat=$arrResultado['rs_data'];
	$lb_ok=$arrResultado['lb_valido'];
	if ($li_numrows==0)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	}
	else
	{
		$li_pos = 0;
		$lb_impreso = false;
		$ld_totced  = 0;
		$ld_totrec  = 0;
		$li_filas   = 0;
			
		$arrResultado=$io_report->uf_select_dt_comprobante_r($ls_codemp,$ls_procede,$ls_comprobante,$ld_fecha,$li_total,$la_data,$ia_niveles_scg,$li_posicion,$li_numrows);
		$la_data=$arrResultado['la_data'];
		$lb_valido=$arrResultado['lb_valido'];
		uf_print_detalle($la_data,$io_pdf);
		uf_print_pie_de_pagina($io_pdf);
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
}
unset($io_pdf);
unset($io_report);
unset($io_funciones);
unset($io_function_report);
unset($io_fecha);
?>