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
function uf_print_encabezado_pagina($as_titulo,$as_procede,$ad_fecha,$as_fuentefin="",$io_pdf){
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
	$io_pdf->setStrokeColor(0,0,0);
	$io_pdf->setLineStyle(1);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],60); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
	$tm=306-($li_tm/2);
	$io_pdf->rectangle(15,710,580,70);
	$io_pdf->addText(25,713,7,"FECHA:".$ad_fecha);
	$io_pdf->addText($tm,714,10,$as_titulo);
	switch ($as_procede){
		case 'SPGTRA':
			$io_pdf->addText(420,740,11,'TRASPASO');
			break;
	}
	$io_pdf->addText(532,10,7,'pág.');
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
function uf_print_firma($as_concepto){
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

	// cuadro inferior uno
	$io_pdf->Rectangle(15,100,580,220);
	$io_pdf->line(15,220,595,220);
	$io_pdf->line(15,155,385,155);
	$io_pdf->line(385,220,385,100);
	$io_pdf->line(170,220,170,100);
	
	$as_descripcion2 = $io_pdf->addTextWrap(25,312,540,8,"JUSTIFICACION:");   // cambie 700 por 535 xr
	$as_descripcion3 = $io_pdf->addTextWrap(25,303,535,8,$as_concepto);
	$as_descripcion4 = $io_pdf->addTextWrap(25,294,535,8,$as_descripcion3);
	$as_descripcion5 = $io_pdf->addTextWrap(25,285,535,8,$as_descripcion4);
	$as_descripcion6 = $io_pdf->addTextWrap(25,276,535,8,$as_descripcion5);
	$as_descripcion7 = $io_pdf->addTextWrap(25,267,535,8,$as_descripcion6);
	$as_descripcion8 = $io_pdf->addTextWrap(25,258,535,8,$as_descripcion7);
	$as_descripcion9 = $io_pdf->addTextWrap(25,249,535,8,$as_descripcion8);
	$as_descripcion10 = $io_pdf->addTextWrap(25,240,535,8,$as_descripcion9);
	$as_descripcion11 = $io_pdf->addTextWrap(25,231,535,8,$as_descripcion10);
	$as_descripcion12 = $io_pdf->addTextWrap(25,222,535,8,$as_descripcion11);

	//$as_descripcion9 = $io_pdf->addTextWrap(25,320,535,8,$as_descripcion8);
	$io_pdf->addText(60,158,8,"ELABORADO"); // Agregar el título
	$io_pdf->addText(230,158,8,"GERENTE DE ADMINISTRACION"); // Agregar el título
	$io_pdf->addText(65,103,8,"PRESUPUESTO"); // Agregar el título
	$io_pdf->addText(260,103,8,"PRESIDENCIA"); // Agregar el título
	$io_pdf->addText(410,110,8,"OFICINA DE PLANIFICACION Y PRESUPUESTO"); // Agregar el título
	$io_pdf->addText(430,103,8,"ALCALDIA DEL MUNICIPIO IRIBARREN"); // Agregar el título
	
	// cuadro inferior dos
 	$io_pdf->Rectangle(15,16,580,80);
	$io_pdf->line(15,81,595,81);
	$io_pdf->line(15,40,190,40);  // Segunda linea horizontal

	$io_pdf->line(190,56,595,56);
	$io_pdf->line(385,41,595,41);

	$io_pdf->line(92,16,92,81);		
	$io_pdf->line(190,16,190,81);
	$io_pdf->line(385,16,385,96);
	$io_pdf->line(435,56,435,81);
	$io_pdf->line(435,16,435,41);
	$io_pdf->line(510,56,510,81);
	$io_pdf->line(510,16,510,41);
	$io_pdf->addText(160,86,7,"ORDENAMIENTO JURIDICO"); // Agregar el título
	$io_pdf->addText(430,86,7,"INSTANCIAS DE AUTORIZACION"); // Agregar el título

	$io_pdf->addText(22,63,6,"Ley Organica del Poder"); // Agregar el título
	$io_pdf->addText(24,54,6,"   Público Municipal"); // Agregar el título

	$io_pdf->addText(95,72,6, "Ordenanza del Presupuesto Anual"); // Agregar el título
	$io_pdf->addText(105,64,6,"de Recursos y Egresos del"); // Agregar el título
	$io_pdf->addText(105,56,6,"Municipio Iribarren Público"); // Agregar el título
	$io_pdf->addText(99,48,6,"Municipal Ejercicio Fiscal 2017"); // Agregar el título


	$io_pdf->addText(260,66,7,"Tipo Modificacion"); // Agregar el título
	$io_pdf->addText(391,66,5,"PRESIDENCIA"); // Agregar el título
	$io_pdf->addText(440,71,5,"OFICINA DE PLANIFICACION"); // Agregar el título
	$io_pdf->addText(450,61,5,"Y PRESUPUESTO"); // Agregar el título
	$io_pdf->addText(515,71,5,"ALCALDE DEL MUNICIPIO"); // Agregar el título
	$io_pdf->addText(535,61,5,"IRIBARREN"); // Agregar el título

	$io_pdf->addText(35,30,7,"Capítulo VI"); // Agregar el título
	$io_pdf->addText(34,23,7,"Artículo 242"); // Agregar el título


	$io_pdf->addText(123,26,7,"Articulo 44"); // Agregar el título

	$io_pdf->addText(192,44,6.8,'   ..."Las modificaciones presupuestarias que requeran los '); // Agregar el título
	$io_pdf->addText(192,34,6.8,'    órganos desconcentrados y entes descentralizados'); // Agregar el título	
        $io_pdf->addText(192,24,6.8,' funcionalmente, se regirán según las siguientes condiciones"...'); // Agregar el título

	$io_pdf->addText(405,46,6,' Marque con una equis las instancias vinculadas al procedimiento'); // Agregar el título


	$io_pdf->addText(410,26,7,'X'); // Agregar el título
	$io_pdf->addText(467,26,7,"X"); // Agregar el título
}// end function uf_print_encabezado_pagina
//--------------------------------------------------------------------------------------------------------------------------------



//--------------------------------------------------------------------------------------------------------------------------------

$ls_procede     = $_GET["procede"];
$ls_comprobante = $_GET["comprobante"];
$ld_fecha       = $_GET["fecha"];
$ls_juscomp     = $_GET["juscomp"];


require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("sigesp_spg_reportes_class.php");
$io_report = new sigesp_spg_reportes_class();
$io_pdf    = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
$io_pdf->ezSetCmMargins(3,11.5,3,3); // Configuración de los margenes en centímetros
$io_pdf->ezStartPageNumbers(570,10,7,'','',1); // Insertar el número de página
$ls_titulo = "<b>SOLICITUD DE MODIFICACIÓN PRESUPUESTARIA Nº. ".$ls_comprobante."</b>";
$arrResultado = $io_report->uf_obtener_fuente_financiamiento_comprobante($ls_comprobante,$ls_procede,$ld_fecha);
$ls_fuentefin =$arrResultado['ls_fuente_financiamiento'];
uf_print_encabezado_pagina($ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$io_pdf); // Imprimimos el encabezado de la página
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
	uf_print_firma($ls_juscomp);
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