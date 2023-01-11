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
if(!array_key_exists("la_logusr",$_SESSION)){
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";
}

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo,$as_procede,$ad_fecha,$as_fuentefin="",$ab_gastoc,$io_pdf){
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezado_pagina
	//		    Acess: private
	//	    Arguments: as_titulo // Título del Reporte
	//	    		   io_pdf    // Instancia de objeto pdf
	//    Description: Función que imprime los encabezados por página
	//	   Creado Por: Ing. Néstor Falcon
	// Fecha Creación: 18/05/2007.
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
	$tm=306-($li_tm/2);
	$io_pdf->rectangle(20,690,570,80);
	$io_pdf->addText(30,750,8,"ORGANO:{$_SESSION['la_empresa']['nombre']}");
	$io_pdf->addText(450,750,8,"<b>PÁGINA N°.</b>");
	$io_pdf->addText(510,745,8,"COD.");
	$io_pdf->addText(25,710,7,"FECHA:".$ad_fecha);
	$io_pdf->addText(25,695,7,"FUENTE DE FINANCIAMIENTO: ".$as_fuentefin);
	$io_pdf->addText($tm,730,10,$as_titulo);
	$io_pdf->rectangle(20,625,570,60);
	$io_pdf->addText(30,660,7,"INSUBSISTENCIA");$io_pdf->rectangle(95,658,10,10);
	$io_pdf->addText(130,660,7,"REDUCCIÓN");$io_pdf->rectangle(180,658,10,10);
	$io_pdf->addText(220,675,7,"<b>RECURSOS ADICIONALES</b>");
	$io_pdf->addText(223,660,7,"CRÉDITO ADICIONAL");$io_pdf->rectangle(300,658,10,10);
	$io_pdf->addText(238,638,7,"RECTIFICACIÓN");$io_pdf->rectangle(300,635,10,10);
	$io_pdf->addText(470,675,7,"<b>TRASPASO</b>");
	$io_pdf->addText(450,660,7,"GASTOS CORRIENTES");$io_pdf->rectangle(540,658,10,10);
	$io_pdf->addText(460,638,7,"GASTOS DE CAPITAL");$io_pdf->rectangle(540,635,10,10);
	//Impresión de las X para el Marcado de Operacion.

	switch ($as_procede){
		case 'SPGINS':
			$io_pdf->addText(97.5,660.5,7,"<b>X</b>");//Insubsistencia
			break;
		case 'SPGCRA':
			$io_pdf->addText(302.5,660.5,7,"<b>X</b>");//Crédito Adicional.
			break;
		case 'SPGREC':
			$io_pdf->addText(302.5,637.5,7,"<b>X</b>");//Rectificacion.
			break;
		case 'SPGTRA':
			if($ab_gastoc){
				$io_pdf->addText(542.5,637.5,7,"<b>X</b>");//Traspaso.
			}
			else{
				$io_pdf->addText(542.5,660.5,7,"<b>X</b>");//Traspaso.
			}
			break;
	}

	//$io_pdf->Rectangle(19,80,570,30);

	//$io_pdf->Rectangle(19,40,570,60);
	$io_pdf->setStrokeColor(0,0,0);
    $io_pdf->Rectangle(19,40,570,70);
	//$io_pdf->line(19,80,590,80);
	$io_pdf->line(19,90,590,90);
	$io_pdf->line(90,40,90,90);
	$io_pdf->line(161,40,161,90);
	$io_pdf->line(232,40,232,90);
	$io_pdf->line(303,40,303,90);
	$io_pdf->line(374,40,374,110);
	$io_pdf->line(446,40,446,110);
	$io_pdf->line(518,40,518,90);

	$io_pdf->addText(155,97,8,"<b>INSTITUCION</b>"); // Agregar el título
	$io_pdf->addText(383,102,8,"<b>APROBACION </b>"); // Agregar el título
	$io_pdf->addText(387,92,8,"<b>MINISTERIO</b>"); // Agregar el título
	$io_pdf->addText(505,97,8,"<b>ONAPRE</b>"); // Agregar el título


	$io_pdf->addText(35,55,6,"Gerencia de "); // Agregar el título
	$io_pdf->addText(32,48,6,"Administracion"); // Agregar el título
	$io_pdf->addText(38,42,6,"y Finanzas"); // Agregar el título

	$io_pdf->addText(98,55,6,"Responsable Accion"); // Agregar el título
	$io_pdf->addText(96,48,6,"Centralizada/Proyecto"); // Agregar el título
	$io_pdf->addText(111,42,6,"(Cedente)"); // Agregar el título

	$io_pdf->addText(167,55,6,"Responsable Accion"); // Agregar el título
	$io_pdf->addText(165,48,6,"Centralizada/Proyecto"); // Agregar el título
	$io_pdf->addText(180,42,6,"(Receptora)"); // Agregar el título

	$io_pdf->addText(250,55,6,"Gerencia de"); // Agregar el título
	$io_pdf->addText(247,48,6,"Planificacion y"); // Agregar el título
	$io_pdf->addText(250,42,6,"Presupuesto"); // Agregar el título

	$io_pdf->addText(330,48,6,"Maxima"); // Agregar el título
	$io_pdf->addText(328,42,6,"Autoridad"); // Agregar el título

	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezado_pagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle($la_data,$ls_titulo1,$ls_titulo2,$ld_monto,$io_pdf){
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		    Acess: private
	//	    Arguments: la_data // arreglo de información
	//	   			   io_pdf // Objeto PDF
	//    Description: función que imprime el detalle
	//	   Creado Por: Ing.Yozelin Barragán
	// Fecha Creación: 13/09/2006
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	$io_pdf->setStrokeColor(1,1,1);
	$io_pdf->ezSetDy(-15);


	$la_datatit[1]=array('titulo'=>"<b>".$ls_titulo1."</b>");
	$la_columnas=array('titulo'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
	$io_pdf->ezSetDy(-5);

	$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						  'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('proyecto'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la 
						 			   'accion'=>array('justification'=>'center','width'=>45),
									   'ejecutora'=>array('justification'=>'center','width'=>25),
									   'partida'=>array('justification'=>'center','width'=>25),
									   'generica'=>array('justification'=>'center','width'=>25),
									   'especifica'=>array('justification'=>'center','width'=>25), 
									   'subespecifica'=>array('justification'=>'center','width'=>25),
									   'denominacion'=>array('justification'=>'left','width'=>255),
									   'monto'=>array('justification'=>'right','width'=>80))); // Justificación y ancho 
	$la_columnas = array('proyecto'=>'<b>PROYECTO O ACCION CENTRALIZADA</b>',
		                     'accion'=>'<b>ACCIÓN ESPECÍFICA</b>',
							 'ejecutora'=>'<b>UEL</b>',
							 'partida'=>'<b>PART</b>',
							 'generica'=>'<b>GEN</b>',
			                 'especifica'=>'<b>ESP</b>',
							 'subespecifica'=>'<b>SUB</b>',
							 'denominacion'=>'<b>DENOMINACIÓN</b>',
							 'monto'=>'<b>BOLÍVARES</b>');
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);

	unset($la_data);
	unset($la_columnas);
	unset($la_config);

	//IMPRIMIENDO EL TOTAL
	$la_datatit[1]=array('proyecto'=>'','accion'=>'','ejecutora'=>'','partida'=>'',
						 'generica'=>'','especifica'=>'','subespecifica'=>'',
						 'denominacion'=>'<b>'.$ls_titulo2.'</b>','monto'=>'<b>'.number_format($ld_monto,2,",",".").'</b>');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('proyecto'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la 
						 			   'accion'=>array('justification'=>'center','width'=>45),
									   'ejecutora'=>array('justification'=>'center','width'=>25),
									   'partida'=>array('justification'=>'center','width'=>25),
									   'generica'=>array('justification'=>'center','width'=>25),
									   'especifica'=>array('justification'=>'center','width'=>25), 
									   'subespecifica'=>array('justification'=>'center','width'=>25),
									   'denominacion'=>array('justification'=>'left','width'=>255),
									   'monto'=>array('justification'=>'right','width'=>80))); // Justificación y ancho 

	$la_columnas = array('proyecto'=>'<b>PROYECTO O ACCION CENTRALIZADA</b>',
		                     'accion'=>'<b>ACCIÓN ESPECÍFICA</b>',
							 'ejecutora'=>'<b>UEL</b>',
							 'partida'=>'<b>PART</b>',
							 'generica'=>'<b>GEN</b>',
			                 'especifica'=>'<b>ESP</b>',
							 'subespecifica'=>'<b>SUB</b>',
							 'denominacion'=>'<b>DENOMINACIÓN</b>',
							 'monto'=>'<b>BOLÍVARES</b>');

	$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
	unset($la_data);
	unset($la_columnas);
	unset($la_config);
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------

$ls_procede     = $_GET["procede"];
$ls_comprobante = $_GET["comprobante"];
$ld_fecha       = $_GET["fecha"];


require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("sigesp_spg_reportes_class.php");
$io_report = new sigesp_spg_reportes_class();
$io_pdf    = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
$io_pdf->ezSetCmMargins(7,6,3,3); // Configuración de los margenes en centímetros
$ls_titulo = "<b>SOLICITUD DE MODIFICACIÓN PRESUPUESTARIA Nº. ".$ls_comprobante."</b>";
$arrResultado = $io_report->uf_obtener_fuente_financiamiento_comprobante($ls_comprobante,$ls_procede,$ld_fecha);
$ls_fuentefin =$arrResultado['ls_fuente_financiamiento'];
$lb_gasto_capital = $io_report->uf_validar_gasto_capital($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha);
uf_print_encabezado_pagina($ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$lb_gasto_capital,$io_pdf); // Imprimimos el encabezado de la página
$la_result_dis= $io_report->uf_buscar_detalle_comprobaten($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha,"D");
$la_result_aum= $io_report->uf_buscar_detalle_comprobaten($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha,"A");

if ($la_result_dis->EOF && $la_result_aum->EOF){
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else{
	$arrResultado = $io_report->uf_obtener_datadetalle($la_result_dis,$ld_tot_dis);
	$ld_tot_dis=$arrResultado['ld_total_general'];
	$la_data_detalle_dis =$arrResultado['la_data'];
	uf_print_detalle($la_data_detalle_dis,"Partidas Cedentes","TOTAL PARTIDAS CEDENTES",$ld_tot_dis,$io_pdf);
	$arrResultado = $io_report->uf_obtener_datadetalle($la_result_aum,$ld_tot_aum);
	$ld_tot_aum=$arrResultado['ld_total_general'];
	$la_data_detalle_aum =$arrResultado['la_data'];
	uf_print_detalle($la_data_detalle_aum,'Partidas Receptoras',"TOTAL PARTIDAS RECEPTORAS",$ld_tot_aum,$io_pdf);
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
unset($io_report);
?>