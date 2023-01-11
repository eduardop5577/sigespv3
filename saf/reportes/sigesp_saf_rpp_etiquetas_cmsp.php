<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$as_cmpmov,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_cmpmov // numero de comprobante de movimiento
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		//$io_pdf->line(50,40,555,40);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		//$tm=504-($li_tm/2);
		$io_pdf->addText($tm,950,11,"<b>".$as_titulo."</b>"); // Agregar el título //550
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=504-($li_tm/2);
		$io_pdf->addText(350,935,11,""); // Agregar la fecha 750
		$io_pdf->addText($tm,935,11,$ad_fecha); // Agregar la fecha  
		$io_pdf->addText(350,955,11,""); // Agregar la fecha  750
		$io_pdf->addText(400,955,11,""); // Agregar la fecha 800
		$io_pdf->addText(528,970,8,date("d/m/Y")); // Agregar la Fecha 928  
		$io_pdf->addText(534,963,7,date("h:i a")); // Agregar la Hora 934
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codemp,$as_nomemp,$as_codact,$as_denact,$as_maract,$as_modact,$ad_fecmpact,$ai_costo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codemp   // codigo de empresa
		//	    		   as_nomemp   // nombre de empresa
		//	    		   as_codact   // codigo de activo
		//	    		   as_denact   // denominacion de activo
		//	    		   as_maract   // marca del activo
		//	    		   as_modact   // modelo del activo
		//	    		   ad_fecmpact // fecha de compra del activo
		//	    		   ai_costo    // costo del activo
		//	    		   io_pdf      // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tipoformato;
		if($ls_tipoformato==0)
		{
		  $ls_titulo="Costo Bs.:";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo="Costo Bs.F.:";
		}
		$io_pdf->ezSetDy(-5);
		$la_data=array(array ('name'=>'<b>IDENTIFICACIÓN DE BIENES MUEBLES</b>  '),
		               array ('name'=>''),
					   array ('name'=>$as_codact));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-5);
		$la_columna=array('seract'=>'<b>Serial</b>',
						  'ideact'=>'<b>Identificador</b>',
						  'idchapa'=>'<b>Chapa</b>',
						  'nomrespri'=>'<b>Responsable Primario</b>',
						  'nomresuso'=>'<b>Responsable por Uso</b>',
						  'denuniadm'=>'<b>Unidad Administrativa</b>',
						  'fecincact'=>'<b>Incorporación</b>',
						  'fecdesact'=>'<b>Desincorporación</b>',
						  'estact'=>'<b>Estatus</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('seract'=>array('justification'=>'left','width'=>90), // Justificación y ancho de la columna
						 			   'ideact'=>array('justification'=>'center','width'=>77), // Justificación y ancho de la columna
						 			   'idchapa'=>array('justification'=>'center','width'=>77), // Justificación y ancho de la columna
						 			   'nomrespri'=>array('justification'=>'left','width'=>160), // Justificación y ancho de la columna
						 			   'nomresuso'=>array('justification'=>'left','width'=>160), // Justificación y ancho de la columna
						 			   'denuniadm'=>array('justification'=>'left','width'=>170), // Justificación y ancho de la columna
						 			   'fecincact'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'fecdesact'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'estact'=>array('justification'=>'left','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_montot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('total'=>""));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>900))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");
	$ld_fecha="";
	$ls_titulo="<b>Reporte de Etiquetas de Activos</b>";
	if(($ld_desde!="")&&($ld_hasta!=""))
	{
		$ld_fecha="Compra Desde:".$ld_desde."  Hasta:".$ld_hasta."";
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
	}	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_saf_load_activos_etiqueta($ls_codemp,$ls_coddesde,$ls_codhasta); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Activo. Desde el activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_activo.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,4.5,3,3); // Configuración de los margenes en centímetros
		//$io_pdf->ezStartPageNumbers(540,50,10,'','',1); // Insertar el número de página 50
		uf_print_encabezado_pagina($ls_titulo,"",$ld_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$li_totrow=$io_report->ds->getRowCount("codact");
		$rec_y=880;
		$li_i=1; 
		$i=1;
		while($li_i<=$li_totrow)
		{
			$io_pdf->transaction('start'); // Iniciamos la transacción
			$li_numpag=$io_pdf->ezPageCount; // Número de página
			$ls_denact=$io_report->ds->data["denact"][$li_i];
			$ls_maract=$io_report->ds->data["maract"][$li_i];
			$ls_modact=$io_report->ds->data["modact"][$li_i];
			$ld_fecmpact=$io_report->ds->data["feccmpact"][$li_i];
			$ld_fecmpactaux=$io_funciones->uf_convertirfecmostrar($ld_fecmpact);
			$li_costo=$io_report->ds->data["costo"][$li_i];
			$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
			
			//Rectangulo uno
			$io_pdf->Rectangle(30,$rec_y,265,50); 
			$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp1.jpg',38,$rec_y+10,40,35); // Agregar Logo
			$io_pdf->addText(88,$rec_y+35,8,"<b>IDENTIFICACIÓN DE BIENES MUEBLES</b>"); // Agregar FECHA
			$ls_codact=$io_report->ds->data["ideact"][$li_i];
			$io_pdf->addText(130,$rec_y+10,8,$ls_codact); // Agregar FECHA
			$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp2.jpg',247,$rec_y+10,40,35); // Agregar Logo
			
			if($li_i<=$li_totrow){
				//Rectangulo dos
				$io_pdf->Rectangle(315,$rec_y,265,50); 
				$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp1.jpg',323,$rec_y+10,40,35); // Agregar Logo
				$io_pdf->addText(373,$rec_y+35,8,"<b>IDENTIFICACIÓN DE BIENES MUEBLES</b>"); // Agregar FECHA
				$ls_codact=$io_report->ds->data["ideact"][$li_i+1];
				$io_pdf->addText(415,$rec_y+10,8,$ls_codact); // Agregar FECHA
				$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp2.jpg',532,$rec_y+10,40,35); // Agregar Logo
			}
			$rec_y=$rec_y-60;
			$li_i=$li_i+2;
			if($i==14){
				$io_pdf->ezNewPage();
				$rec_y=880;
				$i=1;
			}
			else{
				$i++;
			}
			//uf_print_cabecera($ls_codemp,$ls_nomemp,$ls_codact,$ls_denact,$ls_maract,$ls_modact,$ld_fecmpactaux,$li_costo,$io_pdf); // Imprimimos la cabecera del registro
			unset($la_data);			
		}
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}		
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 