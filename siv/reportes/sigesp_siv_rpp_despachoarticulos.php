<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$ad_fecha,$ls_nomemp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,730,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,750,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$ls_nomemp);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,765,11,"<b>".$ls_nomemp."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,735,11,$ad_fecha); // Agregar la fecha
		$io_pdf->addText(495,745,8,"<b>Emision:</b> ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_denart,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numorddes    //numero de orden de compra
		//	    		   as_fecdes    // fecha del despacho
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$as_nomfisalm=substr($as_nomfisalm,0,35);
		//$as_denpro=substr($as_denpro,0,25);
		$la_data=array(array('name'=>'<b>Unidad Ejecutora: </b>  '.$as_denart.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('denunimed'=>'',
						  'canart'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>670, // Ancho de la tabla
						 'maxWidth'=>670, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denunimed'=>array('justification'=>'left','width'=>375), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'right','width'=>82))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>660, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$li_totgeneral,$io_pdf)
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
		$la_datatit[1]=array('fecdes'=>'<b>Fecha</b>',
						  'numorddes'=>'<b>No. Despacho</b>',
						  'codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_columna=array('fecdes'=>'<b>Fecha</b>',
						  'numorddes'=>'<b>No. Despacho</b>',
						  'codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('fecdes'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numorddes'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'codart'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'preuniart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'montotart'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('fecdes'=>'<b>Fecha</b>',
						  'numorddes'=>'<b>No. Despacho</b>',
						  'codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('fecdes'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numorddes'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'codart'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'left','width'=>170), // Justificación y ancho de la columna
						 			   'preuniart'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'montotart'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_columna);
		unset($la_config);
		unset($la_datatit);
		$la_datatit[1]=array('name'=>'<b>TOTAL GENERAL</b>',
						  'name1'=>$li_totgeneral);
		$la_columna=array('name'=>'<b>Fecha</b>',
						  'name1'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'right','width'=>520), // Justificación y ancho de la columna
						 			   'name1'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);
	}// end function uf_print_detalleg
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalleresumido($la_data,$li_totgeneral,$io_pdf)
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
		$la_datatit[1]=array('codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_columna=array('codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codart'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'center','width'=>270), // Justificación y ancho de la columna
						 			   'preuniart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'montotart'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('codart'=>'<b>Codigo de Articulo</b>',
						  'denart'=>'<b>Descripcion</b>',
						  'preuniart'=>'<b>Precio</b>',
						  'canart'=>'<b>Cantidad</b>',
						  'montotart'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codart'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
						 			   'denart'=>array('justification'=>'left','width'=>270), // Justificación y ancho de la columna
						 			   'preuniart'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'montotart'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_columna);
		unset($la_config);
		unset($la_datatit);
		$la_datatit[1]=array('name'=>'<b>TOTAL GENERAL</b>',
						  'name1'=>$li_totgeneral);
		$la_columna=array('name'=>'<b>Fecha</b>',
						  'name1'=>'<b>Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'right','width'=>510), // Justificación y ancho de la columna
						 			   'name1'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);
	}// end function uf_print_detalleg
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_codart($la_data,$io_pdf)
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
		$la_datatit[1]=array('denart'=>'<b>Articulo</b>',
						  'denuniadm'=>'<b>Unidad Solicitante</b>',
						  'canart'=>'<b>Cantidad Despachada</b>');
		$la_columna=array('denart'=>'<b>Articulo</b>',
						  'denuniadm'=>'<b>Unidad Solicitante</b>',
						  'canart'=>'<b>Cantidad Despachada</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>670, // Ancho de la tabla
						 'maxWidth'=>670, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denart'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'denuniadm'=>array('justification'=>'center','width'=>290), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('denart'=>'<b>Articulo</b>',
						  'denuniadm'=>'<b>Unidad Solicitante</b>',
						  'canart'=>'<b>Cantidad Despachada</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>670, // Ancho de la tabla
						 'maxWidth'=>670, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denart'=>array('justification'=>'left','width'=>180), // Justificación y ancho de la columna
						 			   'denuniadm'=>array('justification'=>'left','width'=>290), // Justificación y ancho de la columna
						 			   'canart'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalleg
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_inventario.php");
	$io_fun_inventario=new class_funciones_inventario();
	require_once("sigesp_siv_class_report.php");
	$io_report=new sigesp_siv_class_report();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_inventario->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_inventario->uf_obtenervalor_get("hasta","");
	$ls_resumido=$io_fun_inventario->uf_obtenervalor_get("resumido","");
	$ls_denuniadm=$io_fun_inventario->uf_obtenervalor_get("denuniadm","");
	$ls_coduniadm=$io_fun_inventario->uf_obtenervalor_get("coduniadm","");

	$ls_titulo=" Reporte de Articulos Despachados ";
	$ls_fecha="<b> Periodo ".$ld_desde." - ".$ld_hasta."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------------------------------------------------------------------------------------
	$arrResultado=$io_report->uf_select_despachoarticulos($ls_resumido,$ld_desde,$ld_hasta,$ls_coduniadm,$lb_valido); // Cargar el DS con los datos de la cabecera del reporte
	$rs_data=$arrResultado['rs_data'];
	$lb_valido=$arrResultado['lb_valido'];
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
	//	print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{//print "entro1";
		/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////
		$ls_desc_event="Generó el reporte de Ordenes de Despacho Desde ".$ld_desde." hasta ".$ld_hasta;
		$io_fun_inventario->uf_load_seguridad_reporte("SIV","sigesp_siv_r_despachos.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$ls_nomemp,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(690,50,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->io_sql->num_rows($rs_data);
		$li_s=0;
		$li_totgeneral=0;
		if($li_totrow>0)
		{
			if($ls_denuniadm!="")
				uf_print_cabecera($ls_denuniadm,$io_pdf); // Imprimimos la cabecera del registro
			while((!$rs_data->EOF))
			{
				$ls_denart=$rs_data->fields["denart"];
				$ls_codart=$rs_data->fields["codart"]; 
				$ls_numorddes=$rs_data->fields["numorddes"]; 
				$ls_fecdes=$rs_data->fields["fecdes"];
				$ls_fecdes=$io_funciones->uf_convertirfecmostrar($ls_fecdes); 
				$li_canart=number_format($rs_data->fields["canart"],2,',','.'); 
				$li_preuniart=number_format($rs_data->fields["preuniart"],2,',','.'); 
				$li_montotart=$rs_data->fields["montotart"];
				$li_totgeneral=$li_totgeneral+$li_montotart;
				$li_montotart=number_format($li_montotart,2,',','.'); 
				$li_s++;
				$la_data[$li_s]= array('fecdes'=>$ls_fecdes,'numorddes'=>$ls_numorddes,'codart'=>$ls_codart,'denart'=>$ls_denart,'preuniart'=>$li_preuniart,'canart'=>$li_canart,'montotart'=>$li_montotart);
				$rs_data->MoveNext();
			}
			if($la_data!="")
			{
				$li_totgeneral=number_format($li_totgeneral,2,',','.'); 
				if($ls_resumido=="")
				{
					uf_print_detalle($la_data,$li_totgeneral,$io_pdf); 
				}
				else
				{
					uf_print_detalleresumido($la_data,$li_totgeneral,$io_pdf);
				}
			}
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 