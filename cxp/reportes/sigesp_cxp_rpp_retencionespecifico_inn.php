<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 10/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesespecifico.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecdes,$as_fechas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 08/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(40,40,730,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_inn.jpg',35,555,720,40); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,530,11,$as_titulo); // Agregar el t?tulo
		$ls_periodo = "<b>Del :</b>".$as_fecdes."   "."<b>Al :</b>".$as_fechas;	
		$li_tm=$io_pdf->getTextWidth(11,$ls_periodo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,515,11,$ls_periodo); // Agregar el t?tulo
		$io_pdf->addText(700,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(706,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codded,$as_dended,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_codded // C?digo de Deduccion
		//	    		   as_dended // Deenominaci?n de Deduccion
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
		$la_data   =array(array('retencion'=>'<b><i>Retenci?n:<i></b>','codigo'=>$as_codded,'denominacion'=>$as_dended));
		$la_columna=array('retencion'=>'','codigo'=>'','denominacion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('retencion'=>array('justification'=>'left','width'=>60),
						               'codigo'=>array('justification'=>'left','width'=>50), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>590))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);			
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('beneficiario'=>'<b>Beneficiario</b>','solicitud'=>'<b>Solicitud de Pago</b>','fecha'=>'<b>Fecha</b>','numcom'=>'<b>Comprobante','monto'=>'<b>Monto Objeto Retencion','porded'=>'<b>Alicuota de Retencion (%) </b>','retencion'=>'<b>Monto Retenido</b>'));
		$la_columna=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'titleFontSize' =>10,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0,
						 'shadeCol2'=>array(0.86,0.86,0.86),
						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>200),
						               'solicitud'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'porded'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
						 			   'retencion'=>array('justification'=>'center','width'=>90))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo con todos los datos 
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' =>8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('beneficiario'=>array('justification'=>'left','width'=>200),
						               'solicitud'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
						 			   'numcom'=>array('justification'=>'right','width'=>90), // Justificaci?n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>90), // Justificaci?n y ancho de la columna
						 			   'porded'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
						 			   'retencion'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$la_columna=array('beneficiario'=>'','solicitud'=>'','fecha'=>'','numcom'=>'','monto'=>'','porded'=>'','retencion'=>'');
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	 function uf_print_totales($ai_filas,$ai_total,$ai_totbase,$io_pdf)
	 {
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: ai_filas // Total de Filas
		//				   ai_total // Monto total retenido
		//				   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los totales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 10/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo=" Bs.F.";
		}
		else
		{
			$ls_titulo=" Bs.";
		}
	    $la_data[1]=array('name'=>'_________________________________________________________________________________________________________________________');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xPos'=>412, // Orientaci?n de la tabla
						 'width'=>700); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('cantidad'=>'<b>Total de Retenciones :</b>','filas'=>$ai_filas,'totales'=>'<b>Total Montos </b>','base'=>$ai_totbase,'vacio'=>'','monto'=>$ai_total);
	    $la_columna=array('cantidad'=>'','filas'=>'','totales'=>'','base'=>'','vacio'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' =>8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
 						 'colGap'=>1,
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('cantidad'=>array('justification'=>'right','width'=>90),
						               'filas'=>array('justification'=>'left','width'=>20),
									   'totales'=>array('justification'=>'right','width'=>330),
									   'base'=>array('justification'=>'right','width'=>90),
									   'vacio'=>array('justification'=>'right','width'=>80),
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
	    $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	 }// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>LISTADO DE RETENCIONES</b>";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_codded=$io_fun_cxp->uf_obtenervalor_get("codded","");
	$ls_coddedhas=$io_fun_cxp->uf_obtenervalor_get("coddedhas","");
	//$ls_dended=$io_fun_cxp->uf_obtenervalor_get("dended","");
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecdes=$io_fun_cxp->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_cxp->uf_obtenervalor_get("fechas","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipded=$io_fun_cxp->uf_obtenervalor_get("tipded","");
	$ls_tipper=$io_fun_cxp->uf_obtenervalor_get("tipper","");
	if($ls_tipper=="T")
		$ls_tipper="";
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_retenciones($ls_codded,$ls_coddedhas,$ls_tipded);
	}
	if($lb_valido===false)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape');
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
		$io_pdf->ezSetCmMargins(4.4,3,3,3);                          
		uf_print_encabezado_pagina($ls_titulo,$ld_fecdes,$ld_fechas,$io_pdf);
		$li_rowcargos=$io_report->DS->getRowCount("codded");//print"s";
		$io_report->DS->sortData("codded");
		$lb_existe=false;
		for($li_j=1;$li_j<=$li_rowcargos;$li_j++)
		{
			$ls_codded= $io_report->DS->data["codded"][$li_j];
			$ls_dended= $io_report->DS->data["dended"][$li_j];
			$li_islr= $io_report->DS->data["islr"][$li_j];
			$li_iva= $io_report->DS->data["iva"][$li_j];
			$li_estretmun= $io_report->DS->data["estretmun"][$li_j];
			$li_retaposol= $io_report->DS->data["retaposol"][$li_j];
			$li_estretmil= $io_report->DS->data["estretmil"][$li_j];
			$lb_valido=$io_report->uf_retencionesespecifico($ls_codded,$ls_coddedhas,$ls_tipproben,$ls_codprobenhas,$ls_codprobendes,$ld_fecdes,$ld_fechas,$ls_tipper);
			$li_totbase=0;
			$li_totcargos=0;
			$li_totrow=$io_report->ds_detalle->getRowCount("numsol");
			for ($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_codded= $io_report->ds_detalle->data["codded"][$li_i];
				$ls_numsol= $io_report->ds_detalle->data["numsol"][$li_i];
				$ls_nombre= $io_report->ds_detalle->data["nombre"][$li_i];
				$ls_numcomiva= $io_report->ds_detalle->data["numcomiva"][$li_i];
				$ls_numcomislr= $io_report->ds_detalle->data["numcomislr"][$li_i];
				$ls_numcommun= $io_report->ds_detalle->data["numcommun"][$li_i];
				$ls_numcomapo= $io_report->ds_detalle->data["numcomapo"][$li_i];
				$ls_numcommil	= $io_report->ds_detalle->data["numcommil"][$li_i];
				$li_porded= ($io_report->ds_detalle->data["porded"][$li_i]*100);
				$ld_fecemisol= $io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecemisol"][$li_i]);
				$li_monsol= number_format($io_report->ds_detalle->data["mon_obj_ret"][$li_i],2,',','.');
				$li_monret= number_format($io_report->ds_detalle->data["monret"][$li_i],2,',','.');
				$li_totbase= $li_totbase+$io_report->ds_detalle->data["mon_obj_ret"][$li_i];
				$li_totcargos= $li_totcargos+$io_report->ds_detalle->data["monret"][$li_i];
				$ls_numcom="";
				if($li_iva==1)
				{$ls_numcom=$ls_numcomiva;}
				if($li_islr==1)
				{$ls_numcom=$ls_numcomislr;}
				if($li_estretmun==1)
				{$ls_numcom=$ls_numcommun;}
				if($li_retaposol==1)
				{$ls_numcom=$ls_numcomapo;}
				if($li_estretmil==1)
				{$ls_numcom=$ls_numcommil;}
				$la_data[$li_i]=array('beneficiario'=>$ls_nombre,'solicitud'=>$ls_numsol,'fecha'=>$ld_fecemisol,'numcom'=>$ls_numcom,'monto'=>$li_monsol,
									  'porded'=>$li_porded,'retencion'=>$li_monret);
			}
			if($li_i>1)
			{  
				$lb_existe=true;
				uf_print_cabecera($ls_codded,$ls_dended,$io_pdf);
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle  
				$li_totbase=number_format($li_totbase,2,',','.');
				$li_totcargos=number_format($li_totcargos,2,',','.');
				uf_print_totales($li_totrow,$li_totcargos,$li_totbase,$io_pdf);
			}
		}
		if(!$lb_existe)
		{
			$lb_valido=false;
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");		
		}
		
		if($lb_valido) // Si no ocurrio ning?n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg?n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
	//	unset($io_pdf);
	}
//	unset($io_report);
//	unset($io_funciones);
//	unset($io_fun_cxp);
?> 