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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_report;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_report->uf_load_seguridad_reporte("MIS","sigesp_vis_mis_reporte_documentoscontabilizado.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------*/

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 13/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('numdoc'=>'<b>Documento</b>',
						   'monto'=>'<b>Monto</b>',
						   'fecha'=>'<b>Fecha Contabilización</b>',
						   'codusu'=>'<b>Contabilizado por</b>',
						   'modulo'=>'<b>Modulo</b>',
						   'tipoope'=>'<b>Tipo Operación</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numdoc'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'codusu'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
									   'modulo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'tipoope'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/ezpdf/class.ezpdf.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once('../../../base/librerias/php/general/Json.php');
	require_once("../../../modelo/servicio/mis/sigesp_srv_mis_class_report.php");
	$io_report=new sigesp_mis_class_report();
	$io_funciones=new class_funciones();				
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>DOCUMENTOS CONTABILIZADOS </b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	if ($_GET['ObjSon']) 	
	{
	$submit = str_replace("\\","",$_GET['ObjSon']);
	$json = new Services_JSON;	
	$objetoJson = $json->decode($submit);
	} 
	$ls_codusu   = $objetoJson->codusu;
	$ls_fecdes   = $objetoJson->fecdes;
	$ls_fechas   = $objetoJson->fechas;
	$ls_modulo   = $objetoJson->modulo;
	$ls_orden    = $objetoJson->orden;
	$ls_concepto = $objetoJson->concepto;
	//--------------------------------------------------------------------------------------------------------------------------------	
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$rs_data=$io_report->uf_select_documentos_contabilizados($ls_codusu,$ls_fecdes,$ls_fechas,$ls_modulo,$ls_concepto,$ls_orden);
		//var_dump($rs_data);
		if($rs_data->EOF) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
			uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
			$li_s=0;
			
			while(!$rs_data->EOF){
				$ls_numdoc  = $rs_data->fields['numdoc']; 
				$ld_monto   = $rs_data->fields['monto'];
				$ls_fecha   = $rs_data->fields['fecha'];
				$ls_procede = $rs_data->fields['procede'];
				$ls_tipoope = $rs_data->fields['desproc'];
				$ls_codusur = $rs_data->fields['codusu'];
				$ls_modulo  = substr($ls_procede,0,3);
				$ls_fecha   = $io_funciones->uf_convertirfecmostrar($ls_fecha);
				$ld_monto   = number_format($ld_monto,2,",",".");
				
				
				$la_data[$li_s]= array('numdoc'=>$ls_numdoc,'monto'=>$ld_monto,'fecha'=>$ls_fecha,'codusu'=>$ls_codusur,'modulo'=>$ls_modulo,'tipoope'=>$ls_tipoope);
				$rs_data->MoveNext();
				$li_s++;
			}		
					
			uf_print_detalle($la_data,$io_pdf);
			unset($la_data);
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
}		
?>