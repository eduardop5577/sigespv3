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
	function uf_print_cabecera_detalle($io_encabezado,$as_descripcion,$as_numdoc,$as_fecmov,$as_fecaprmod,$as_tipmodpre,$ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$lb_gasto_capital,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabezera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	    $io_pdf->saveState();
	  //  $io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],40,715,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
        $imagen="hidroarriba.jpg";
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$imagen,25,703,550,$_SESSION["ls_height"]); // Agregar Logo
		//$io_pdf->addText(50,680,10,"<b>Tipo Modificación:</b> ".$as_tipmodpre);
		$ls_texto_1 = "<b>MODIFICACIONES PRESUPUESTARIAS APROBADAS</b>";

		$ls_texto_3 = "<b>Descripción : </b>";

		$as_descripcion2 = $io_pdf->addTextWrap(46,575,350,8,$ls_texto_3.$as_descripcion);
		$as_descripcion3 = $io_pdf->addTextWrap(46,565,350,8,$as_descripcion2);
	    $as_descripcion4 = $io_pdf->addTextWrap(46,555,350,8,$as_descripcion3);
	    $as_descripcion5 = $io_pdf->addTextWrap(46,545,350,8,$as_descripcion4);
	    $as_descripcion6 = $io_pdf->addTextWrap(46,535,350,8,$as_descripcion5);
	    $as_descripcion7 = $io_pdf->addTextWrap(46,525,350,8,$as_descripcion6);
	    $as_descripcion8 = $io_pdf->addTextWrap(46,515,350,8,$as_descripcion7);
		$io_pdf->addTextWrap(46,505,350,7,$as_descripcion8);


/*		$li_tm=$io_pdf->getTextWidth(14,$ls_texto_1);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,698,14,$ls_texto_1); // Agregar el título
*/
		
		$io_pdf->addText(415,575,9,'N°: '.$as_numdoc);
		$io_pdf->addText(415,545,9,'Fecha Registro     : '.$as_fecmov);
		$io_pdf->addText(415,530,9,'Fecha Aprobación: '.$as_fecaprmod);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(410,495,410,585);

		$io_pdf->rectangle(40,495,550,90);

	   // $io_pdf->ezSetY(586.8);
	    $la_datatitulos= array(array('estructura'=>"<b>Est. Presup.</b>",'especifica'=>"<b>ESPECIFICA</b>",
		                             'denominacion'=>"<b>DENOMINACION</b>",'cedente'=>"<b>CEDENTE</b>",
									 'receptora'=>"<b>RECEPTORA</b>"));

		$la_columna=array('estructura'=>'<b>Est. Presup.</b>',
						  'especifica'=>'<b>ESPECIFICA</b>',
						  'denominacion'=>'<b>CEDENTE</b>',
						  'cedente'=>'<b>CEDENTE</b>',
						  'receptora'=>'<b>RECEPTORA</b>');

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>210), // Justificación y ancho de la columna
									   'cedente'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'receptora'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatitulos,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabezera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


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
	//$io_pdf->rectangle(20,690,570,80);
	//$io_pdf->addText(30,750,8,"ORGANO:{$_SESSION['la_empresa']['nombre']}");
	//$io_pdf->addText(450,750,8,"<b>PÁGINA N°.</b>");
//		$io_pdf->rectangle(40,660,550,90);
	$io_pdf->addText(510,662,8,"COD.");
	$io_pdf->addText(45,662,7,"FECHA:".$ad_fecha);
	$io_pdf->addText(45,652,7,"FUENTE DE FINANCIAMIENTO: ".$as_fuentefin);
	$io_pdf->addText($tm,680,10,$as_titulo);
		$io_pdf->setStrokeColor(0,0,0);

	$io_pdf->rectangle(40,585,550,60);
	$io_pdf->addText(50,615,7,"INSUBSISTENCIA");$io_pdf->rectangle(113,613,10,10);
	$io_pdf->addText(140,615,7,"REDUCCIÓN");$io_pdf->rectangle(187,613,10,10);
	$io_pdf->addText(220,635,7,"<b>RECURSOS ADICIONALES</b>");
	$io_pdf->addText(223,615,7,"CRÉDITO ADICIONAL");$io_pdf->rectangle(300,613,10,10);
	$io_pdf->addText(238,595,7,"RECTIFICACIÓN");$io_pdf->rectangle(300,593,10,10);
	$io_pdf->addText(470,635,7,"<b>TRASPASO</b>");
	$io_pdf->addText(450,615,7,"GASTOS CORRIENTES");$io_pdf->rectangle(540,613,10,10);
	$io_pdf->addText(460,595,7,"GASTOS DE CAPITAL");$io_pdf->rectangle(540,593,10,10);
	//Impresión de las X para el Marcado de Operacion.
	switch ($as_procede){
		case 'SPGINS':
			$io_pdf->addText(114.5,614.5,7,"<b>X</b>");//Insubsistencia
			break;
		case 'SPGCRA':
			$io_pdf->addText(302.5,614.5,7,"<b>X</b>");//Crédito Adicional.
			break;
		case 'SPGREC':
			$io_pdf->addText(302.5,594.5,7,"<b>X</b>");//Rectificacion.
			break;
		case 'SPGTRA':
			if($ab_gastoc){
				$io_pdf->addText(542.5,614.5,7,"<b>X</b>");//Traspaso.
			}
			else{
				$io_pdf->addText(542.5,594.5,7,"<b>X</b>");//Traspaso.
			}
			break;
	}

	$io_pdf->Rectangle(40,100,550,15);

	$io_pdf->Rectangle(40,40,550,60);
	$io_pdf->line(40,55,590,55);
	$io_pdf->line(150,40,150,115);
	$io_pdf->line(260,40,260,115);
	$io_pdf->line(370,40,370,115);
	$io_pdf->line(480,40,480,115);
	$io_pdf->line(590,40,590,115);

//	$io_pdf->addText(200,102,8,"<b>INSTITUCION</b>"); // Agregar el título
//	$io_pdf->addText(410,102,8,"<b>OFICINA NACIONAL DE PRESUPUESTO</b>"); // Agregar el título


	$io_pdf->addText(65,109,6,"ELABORADO POR"); // Agregar el título
	$io_pdf->addText(45,46,6,"NOMBRE Y APELLIDO"); // Agregar el título
	$io_pdf->addText(180,109,6,"UNIDAD CEDENTE"); // Agregar el título
	$io_pdf->addText(155,46,6,"NOMBRE Y APELLIDO"); // Agregar el título
	$io_pdf->addText(280,109,6,"UNIDAD RECEPTORA"); // Agregar el título
	$io_pdf->addText(265,46,6,"NOMBRE Y APELLIDO"); // Agregar el título
//	$io_pdf->addText(210,83,7,"PLANIFICACION"); // Agregar el título
	$io_pdf->addText(388,109,6,"APROBADO POR GERENTE"); // Agregar el título
	$io_pdf->addText(390,102,6,"GENERAL O PRESIDENTE"); // Agregar el título
	$io_pdf->addText(378,46,6,"NOMBRE Y APELLIDO"); // Agregar el título
	$io_pdf->addText(508,109,6,"COORDINACION DE"); // Agregar el título
	$io_pdf->addText(512,102,6,"PRESUPUESTO"); // Agregar el título
	$io_pdf->addText(488,46,6,"NOMBRE Y APELLIDO"); // Agregar el título

	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezado_pagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_detalle2($la_data,$ls_titulo1,$ls_titulo2,$ld_monto,$io_pdf){
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
	function uf_print_detalle($la_data,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
			 $io_pdf->ezSetCmMargins(11,4.3,3,3); // Configuración de los margenes en centímetros
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>210),
									   'cedente'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
									   'receptora'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna

		$la_columnas=array('estructura'=>'',
						   'especifica'=>'<b>ESPECIFICA</b>',
						   'denominacion'=>'<b>DENOMINACION</b>',
						   'cedente'=>'<b>CEDENTE</b>',
						   'receptora'=>'<b>RECEPTORA</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_totalaumento,$ad_totaldismi,$as_denominacion,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total '.$as_denominacion.'</b>','disminucion'=>$ad_totaldismi,'aumento'=>$ad_totalaumento));
		$la_columna=array('total'=>'','disminucion'=>'','aumento'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre líneas
						 'width'=>550, // Ancho Máximo de la tabla
						 'fontSize' => 9, // Tamaño de Letras
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>320, // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>370), // Justificación y ancho de la columna
						 			   'disminucion'=>array('justification'=>'right','width'=>90),  // Justificación y ancho de la columna
									   'aumento'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
        $io_pdf->ezSetDy(-30);
	}// end function uf_print_pie_cabecera


//--------------------------------------------------------------------------------------------------------------------------------

$ls_procede     = $_GET["procede"];
$ls_comprobante = $_GET["comprobante"];
$ld_fecha       = $_GET["fecha"];
$ls_ckbrect		= "1";
$ls_ckbtras		=  "1";
$ls_ckbinsu		= "1";
$ls_ckbcre		=  "1";
$ldt_fecdes		= date("Y-01-01");
$ldt_fechas		= date("Y-m-d");

require_once("sigesp_spg_reporte.php");
$io_report2 = new sigesp_spg_reporte();
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funciones		= new class_funciones();


require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("sigesp_spg_reportes_class.php");
$io_report = new sigesp_spg_reportes_class();
$io_pdf    = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
//$io_pdf->ezSetCmMargins(7,6,3,3); // Configuración de los margenes en centímetros
$ls_titulo = "<b>SOLICITUD DE MODIFICACIÓN PRESUPUESTARIA Nº. ".$ls_comprobante."</b>";
$arrResultado = $io_report->uf_obtener_fuente_financiamiento_comprobante($ls_comprobante,$ls_procede,$ld_fecha);
$ls_fuentefin =$arrResultado['ls_fuente_financiamiento'];
$lb_gasto_capital = $io_report->uf_validar_gasto_capital($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha);
//$la_result_dis= $io_report->uf_buscar_detalle_comprobaten($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha,"D");
//$la_result_aum= $io_report->uf_buscar_detalle_comprobaten($_SESSION["la_empresa"]["codemp"],$ls_procede,$ls_comprobante,$ld_fecha,"A");
$lb_valido=$io_report2->uf_spg_reporte_modificaciones_presupuestarias_md($ls_ckbrect,$ls_ckbtras,$ls_ckbinsu,$ls_ckbcre,
																	 $ldt_fecdes,$ldt_fechas,$ls_comprobante,$ls_procede,
																	 $ld_fecha);

if ($lb_valido==false) // Existe algún error ó no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else{

			 set_time_limit(1800);
			 $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			 $io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			 $io_pdf->ezSetCmMargins(10.5,3.5,3,3); // Configuración de los margenes en centímetros
			// $io_pdf->ezStartPageNumbers(550,50,8,'','',1); // Insertar el número de página
			 $io_report->dts_reporte->group_noorder("procomp");
			 $li_tot		  = $io_report2->dts_reporte->getRowCount("spg_cuenta");
			 $ld_totalaumento = 0;
			 $ld_totaldismi   = 0;
			 $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		     $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		     $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		     $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		     $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
			 uf_print_encabezado_pagina($ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$lb_gasto_capital,$io_pdf); // Imprimimos el encabezado de la página
			 for ($z=1;$z<=$li_tot;$z++)
				 {
				   $io_pdf->transaction('start'); // Iniciamos la transacción
		    	   $li_tmp=($z+1);
				   $thisPageNum	   = $io_pdf->ezPageCount;
				   $ls_procede	   = $io_report2->dts_reporte->data["procede"][$z];
				   $ls_procomp     = $io_report2->dts_reporte->data["procomp"][$z];
				   $ldt_fecaprmod  = $io_report2->dts_reporte->data["fecaprmod"][$z];
				   $ldt_fecaprmod	= date("Y-m-d",strtotime($ldt_fecaprmod));
				   $ldt_fecaprmod  = $io_funciones->uf_convertirfecmostrar($ldt_fecaprmod);
				   $ls_comprobante = $io_report2->dts_reporte->data["comprobante"][$z];
				   if ($z<$li_tot)
		              {
					    $ls_procomp_next=$io_report2->dts_reporte->data["procomp"][$li_tmp];
		    	      }
		    	   elseif($z=$li_tot)
		    		  {
						$ls_procomp_next='no_next';
		              }
			       if (!empty($ls_procomp))
				  	  {
					    $ls_procomp_ant=$io_report2->dts_reporte->data["procomp"][$z];
					  }
			       $ls_descripcion  = trim($io_report2->dts_reporte->data["cmp_descripcion"][$z]);
			       $ls_codestpro    = $io_report2->dts_reporte->data["programatica"][$z];
		 	       if ($li_estmodest=='1')
			       {
					    $ls_codestpro1 = substr($ls_codestpro,0,25);
						$ls_codestpro2 = substr($ls_codestpro,25,25);
						$ls_codestpro3 = substr($ls_codestpro,50,25);
						//$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
						$ls_codestpro=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
			       }
				   else
				   {
					    $ls_codestpro1 = substr($ls_codestpro,0,25);
						$ls_codestpro2 = substr($ls_codestpro,25,25);
						$ls_codestpro3 = substr($ls_codestpro,50,25);
						$ls_codestpro4 = substr($ls_codestpro,75,25);
						$ls_codestpro5 = substr($ls_codestpro,100,25);
						//$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
						$ls_codestpro=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				   }
			       $ls_especifica   = $io_report2->dts_reporte->data["spg_cuenta"][$z];
				   $ls_denominacion = trim($io_report2->dts_reporte->data["denominacion"][$z]);
				   $ls_documento	= $io_report2->dts_reporte->data["documento"][$z];
				   $ldt_fecha_bd	= $io_report2->dts_reporte->data["fecha"][$z];
				   $ldt_fecha_bd	= date("Y-m-d",strtotime($ldt_fecha_bd));
				   $ldt_fecha		= $io_funciones->uf_convertirfecmostrar($ldt_fecha_bd);
				   $ld_aumento		= $io_report2->dts_reporte->data["aumento"][$z];
				   $ld_disminucion  = $io_report2->dts_reporte->data["disminucion"][$z];
			       if ($ls_procede=="SPGREC")
			          {
			            $ls_proc="RECTIFICACIONES";
			          }
			       if ($ls_procede=="SPGINS")
			          {
			            $ls_proc="INSUBSISTENCIAS";
			          }
			       if ($ls_procede=="SPGTRA")
					  {
					    $ls_proc="TRASPASOS";
					  }
			       if ($ls_procede=="SPGCRA")
					  {
					    $ls_proc="CREDITOS/INGRESOS ADICIONALES";
					  }
		           $ld_totalaumento = ($ld_totalaumento+$ld_aumento);
		           $ld_totaldismi   = ($ld_totaldismi+$ld_disminucion);
				   if (!empty($ls_procomp))
		              {
						$la_data[$z]  = array('estructura'=>$ls_codestpro,
											  'especifica'=>$ls_especifica,
											  'denominacion'=>$ls_denominacion,
											  'cedente'=>number_format($ld_disminucion,2,',','.'),
											  'receptora'=>number_format($ld_aumento,2,',','.'));
			          }
			       else
			          {
						$la_data[$z]=array('estructura'=>$ls_codestpro,
										   'especifica'=>$ls_especifica,
										   'denominacion'=>$ls_denominacion,
										   'cedente'=>number_format($ld_disminucion,2,',','.'),
										   'receptora'=>number_format($ld_aumento,2,',','.'));
					  }
		 	       if ($ls_procomp_next=='no_next')
			          {
						 $la_data[$z]=array('estructura'=>$ls_codestpro,
										    'especifica'=>$ls_especifica,
										    'denominacion'=>$ls_denominacion,
										    'cedente'=>number_format($ld_disminucion,2,',','.'),
										    'receptora'=>number_format($ld_aumento,2,',','.'));
						 $io_encabezado=$io_pdf->openObject();
						 uf_print_cabecera_detalle($io_encabezado,$ls_descripcion,$ls_comprobante,$ldt_fecha,$ldt_fecaprmod,$ls_proc,$ls_titulo,$ls_procede,$ld_fecha,$ls_fuentefin,$lb_gasto_capital,$io_pdf);
						 uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle


						 $ld_totalaumento=number_format($ld_totalaumento,2,",",".");
						 $ld_totaldismi=number_format($ld_totaldismi,2,",",".");
						 uf_print_pie_cabecera($ld_totalaumento,$ld_totaldismi,'Bs.',$io_pdf);


						 $ld_totalaum=$ld_totalaumento;
						 $ld_totaldis=$ld_totaldismi;
						 $ld_totalaumento=0;
						 $ld_totaldismi=0;
						 $io_pdf->stopObject($io_encabezado);
						 if($z<$li_tot)
						 {
						   $io_pdf->ezNewPage(); // Insertar una nueva página
						 }
						 unset($la_data);
			          }
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
			
/*	$arrResultado = $io_report->uf_obtener_datadetalle($la_result_dis,$ld_tot_dis);
	$ld_tot_dis=$arrResultado['ld_total_general'];
	$la_data_detalle_dis =$arrResultado['la_data'];
	uf_print_detalle($la_data_detalle_dis,"Partidas Cedentes","TOTAL PARTIDAS CEDENTES",$ld_tot_dis,$io_pdf);
	$arrResultado = $io_report->uf_obtener_datadetalle($la_result_aum,$ld_tot_aum);
	$ld_tot_aum=$arrResultado['ld_total_general'];
	$la_data_detalle_aum =$arrResultado['la_data'];
	uf_print_detalle($la_data_detalle_aum,'Partidas Receptoras',"TOTAL PARTIDAS RECEPTORAS",$ld_tot_aum,$io_pdf);*/
}

/*$io_pdf->ezStopPageNumbers(1,1);
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
*/	
//unset($io_pdf);
//unset($io_report2);
?>