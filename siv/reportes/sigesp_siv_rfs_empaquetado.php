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
	function uf_print_encabezado_pagina($as_titulo,$as_numtra,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_numtra // Numero de transferencia
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->rectangle(420,710,130,40);
		$io_pdf->line(420,730,550,730);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,727,11,$as_titulo); // Agregar el título
		$io_pdf->addText(423,735,11,"No.:");      // Agregar texto
		$io_pdf->addText(455,735,11,$as_numtra); // Agregar Numero de la solicitud
		$io_pdf->addText(423,715,10,"Fecha:"); // Agregar texto
		$io_pdf->addText(455,715,10,$ad_fecha); // Agregar la Fecha
		$io_pdf->addText(510,760,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,753,7,date("h:i a")); // Agregar la Hora
		// cuadro inferior
        $io_pdf->Rectangle(50,40,500,70);
		$io_pdf->line(50,53,550,53);		
		$io_pdf->line(50,97,550,97);		
		$io_pdf->line(130,40,130,110);		
		$io_pdf->line(240,40,240,110);		
		$io_pdf->line(380,40,380,110);		
		$io_pdf->addText(60,102,7,"ELABORADO POR"); // Agregar el título
		$io_pdf->addText(70,43,7,"ALMACÉN"); // Agregar el título
		$io_pdf->addText(157,102,7,"VERIFICADO POR"); // Agregar el título
		$io_pdf->addText(160,43,7,"PRESUPUESTO"); // Agregar el título
		$io_pdf->addText(280,102,7,"AUTORIZADO POR"); // Agregar el título
		//$io_pdf->addText(257,43,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
		$io_pdf->addText(440,102,7,"PROVEEDOR"); // Agregar el título
		$io_pdf->addText(405,43,7,"FIRMA AUTOGRAFA, SELLO, FECHA"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numtra,$as_codalmori,$as_codalmdes,$as_nomfisori,$as_nomfisdes,$as_obstra,$ad_fecemi,$ls_codartemp,$ls_denartemp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numtra    // numero de transaccion
		//	    		   as_codalmori // codigo de almacen origen
		//	    		   as_codalmdes // codigo de almacen destino
		//	    		   as_nomfisori // nombre fiscal de almacen origen
		//	    		   as_nomfisdes // nombre fiscal de almacen destino
		//	    		   as_obstra    // observaciones de la transferencia
		//	    		   ad_fecemi    // fecha de emision
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$as_nomfisori=substr($as_nomfisori,0,40);
		//$as_nomfisdes=substr($as_nomfisdes,0,40);
		$la_data=array(array('name'=>'<b>Articulo Empaquetado</b>   '.trim($ls_codartemp)." - ".$ls_denartemp.''),
					   array('name'=>'<b>Origen</b>                 '.$as_codalmori." - ".$as_nomfisori.''),
					   array('name'=>'<b>Destino</b>               '.$as_codalmdes." - ".$as_nomfisdes.''),
					   array ('name'=>'<b>Observaciones</b>  '.$as_obstra.''));
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
		$la_columna=array('articulo'=>'<b>Artículos Salientes</b>',
						  'unidad'=>'<b>Unidad</b>',
						  'cantidad'=>'<b>Cantidad</b>',
						  'costo'=>'<b>Costo Unitario</b>',
						  'total'=>'<b>Total</b>',
						  'pestot'=>'<b>Peso Kg.</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('articulo'=>array('justification'=>'left','width'=>185), // Justificación y ancho de la columna
						 			   'unidad'=>array('justification'=>'left','width'=>45), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>62), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>84), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'pestot'=>array('justification'=>'right','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle2($la_data,$io_pdf)
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
		$la_columna=array('articulo'=>'<b>Artículos Entrantes</b>',
						  'unidad'=>'<b>Unidad</b>',
						  'cantidad'=>'<b>Cantidad</b>',
						  'costo'=>'<b>Costo Unitario</b>',
						  'total'=>'<b>Total</b>',
						  'pestot'=>'<b>Peso Kg.</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('articulo'=>array('justification'=>'left','width'=>185), // Justificación y ancho de la columna
						 			   'unidad'=>array('justification'=>'left','width'=>45), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>62), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>84), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'pestot'=>array('justification'=>'right','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle3($la_data,$io_pdf)
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
		$io_pdf->ezSetDy(-15);
		$la_columna=array('articulo'=>'<b>Artículos</b>',
						  'cuenta'=>'<b>Cuenta</b>',
						  'debhab'=>'<b>Debe/Haber</b>',
						  'monto'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('articulo'=>array('justification'=>'left','width'=>235), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'left','width'=>129), // Justificación y ancho de la columna
						 			   'debhab'=>array('justification'=>'right','width'=>62), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
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
		$la_columna=array('total'=>'',
						  'totcan'=>'',
						  'vacio'=>'',
						  'totmon'=>'',
						  'vacio2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>230), // Justificación y ancho de la columna
						 			   'totcan'=>array('justification'=>'right','width'=>62), // Justificación y ancho de la columna
						 			   'vacio'=>array('justification'=>'right','width'=>84), // Justificación y ancho de la columna
						 			   'totmon'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'vacio2'=>array('justification'=>'right','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_siv_class_report.php");
	$io_report=new sigesp_siv_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_inventario.php");
	$io_fun_inventario=new class_funciones_inventario();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_fecemppro= $io_fun_inventario->uf_obtenervalor_get("fecemppro","");

	$ls_titulo="<b>Empaquetado de Productos</b>";
	$ls_fecha=$ld_fecemppro;
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_codemppro=    $io_fun_inventario->uf_obtenervalor_get("codemppro","");
	$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
	/*$ls_codalmori= $io_fun_inventario->uf_obtenervalor_get("codalmori","");
	$ls_codalmdes= $io_fun_inventario->uf_obtenervalor_get("codalmdes","");
	$ls_nomfisori= $io_fun_inventario->uf_obtenervalor_get("nomfisori","");
	$ls_nomfisdes= $io_fun_inventario->uf_obtenervalor_get("nomfisdes","");
	$ls_obstra=    $io_fun_inventario->uf_obtenervalor_get("obstra","");
	$ld_fecemi=    $io_fun_inventario->uf_obtenervalor_get("fecemi","");*/
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_select_empaquetado($ls_codemp,$ls_codemppro,"","",""); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_codemppro,$ld_fecemppro,$io_pdf); // Imprimimos el encabezado de la página
		$li_totrow=1;//$io_report->DS->getRowCount("codper");
		$li_totpestot=0;
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
	        $io_pdf->transaction('start'); // Iniciamos la transacción
			$li_numpag=$io_pdf->ezPageCount; // Número de página
			$li_totcan=0;
			$li_total=0;
			$ls_codartemp=$io_report->ds->data["codartemp"][$li_i];
			$ls_denartemp=$io_report->ds->data["denartemp"][$li_i];
			$ls_codalmori=$io_report->ds->data["codalmsal"][$li_i];
			$ls_codalmdes=$io_report->ds->data["codalment"][$li_i];
			$ls_nomfisori=$io_report->ds->data["nomfisalmori"][$li_i];
			$ls_nomfisdes=$io_report->ds->data["nomfisalmdes"][$li_i];
			$ls_obstra=$io_report->ds->data["obspro"][$li_i];
			uf_print_cabecera($ls_codemppro,$ls_codalmori,$ls_codalmdes,$ls_nomfisori,$ls_nomfisdes,$ls_obstra,$ld_fecemppro,$ls_codartemp,$ls_denartemp,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_select_dt_empaquetado1($ls_codemp,$ls_codemppro); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->ds_detalle->getRowCount("codart");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codart=     $io_report->ds_detalle->data["codart"][$li_s];
					$ls_denart=     $io_report->ds_detalle->data["denart"][$li_s];
					$li_cantidad=   $io_report->ds_detalle->data["cantidad"][$li_s];
					$li_cosuni=     $io_report->ds_detalle->data["cosuni"][$li_s];
					$li_costot=     $io_report->ds_detalle->data["costot"][$li_s];
					$ls_pesart=     $io_report->ds_detalle->data["pesart"][$li_s];
					$li_totcan=$li_totcan + $li_cantidad;
					$li_total=$li_total + $li_costot;
					$li_pestot=$ls_pesart*$li_cantidad;
					$li_totpestot=$li_totpestot + $li_pestot;
					if($li_numdecper!="3")
					{
						$li_cantidad=number_format($li_cantidad,2,",",".");
					}
					else
					{
						$li_cantidad=number_format($li_cantidad,3,",",".");
					}
					$ls_pesart=number_format($ls_pesart,2,",",".");
					$li_pestot=number_format($li_pestot,2,",",".");
					$li_cosuni=$io_fun_inventario->uf_formatonumerico($li_cosuni);
					$li_costot=$io_fun_inventario->uf_formatonumerico($li_costot);
	
					$la_data[$li_s]=array('articulo'=>$ls_denart,'unidad'=>"Detal",'cantidad'=>$li_cantidad,'costo'=>$li_cosuni,'total'=>$li_costot,'pestot'=>$li_pestot);
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					if($li_numdecper!="3")
					{
						$li_totcan=number_format($li_totcan,2,",",".");
					}
					else
					{
						$li_totcan=number_format($li_totcan,3,",",".");
					}
				$li_total=number_format($li_total,2,",",".");
				$li_totpestot=number_format($li_totpestot,2,",",".");
				$la_data1[1]=array('total'=>'<b>Total</b>','totcan'=>$li_totcan,'vacio'=>'--','totmon'=>$li_total,'vacio2'=>$li_totpestot);
				uf_print_totales($la_data1,$io_pdf); // Imprimimos el detalle 
			}
			$lb_valido=$io_report->uf_select_dt_empaquetado2($ls_codemp,$ls_codemppro); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$la_data=Array();
				$li_totcan=0;
				$li_costot=0;
				$li_total=0;
				$li_totrow_det=$io_report->dts_reporte->getRowCount("codart");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codart=     $io_report->dts_reporte->data["codart"][$li_s];
					$ls_denart=     $io_report->dts_reporte->data["denart"][$li_s];
					$li_cantidad=   $io_report->dts_reporte->data["cantidad"][$li_s];
					$li_cosuni=     $io_report->dts_reporte->data["cosuni"][$li_s];
					$li_costot=     $io_report->dts_reporte->data["costot"][$li_s];
					$li_totcan=$li_totcan + $li_cantidad;
					$li_total=$li_total + $li_costot;
					if($li_numdecper!="3")
					{
						$li_cantidad=number_format($li_cantidad,2,",",".");
					}
					else
					{
						$li_cantidad=number_format($li_cantidad,3,",",".");
					}
						$li_cosuni=$io_fun_inventario->uf_formatonumerico($li_cosuni);
						$li_costot=$io_fun_inventario->uf_formatonumerico($li_costot);
	
						$la_data[$li_s]=array('articulo'=>$ls_denart,'unidad'=>"Detal",'cantidad'=>$li_cantidad,'costo'=>$li_cosuni,'total'=>$li_costot,'pestot'=>$li_totpestot);
				}
				uf_print_detalle2($la_data,$io_pdf); // Imprimimos el detalle 
				$lb_valido=$io_report->uf_select_detalle_scg($ls_codemp,$ls_codemppro); // Obtenemos el detalle del reporte
				if($lb_valido)
				{
					$la_data=Array();
					$li_totcan=0;
					$li_costot=0;
					$li_total=0;
					$li_totrow_det=$io_report->dts_reporte->getRowCount("sc_cuenta");
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
					{
						$ls_codart=     $io_report->dts_reporte->data["codart"][$li_s];
						$ls_denart=     "";
						$ls_cuenta=   $io_report->dts_reporte->data["sc_cuenta"][$li_s];
						$ls_debhab=     $io_report->dts_reporte->data["debhab"][$li_s];
						$li_monto=     $io_report->dts_reporte->data["monto"][$li_s];
						$li_monto=$io_fun_inventario->uf_formatonumerico($li_monto);
						switch ($ls_debhab) 
						{
							case "D":
								$ls_debhab="DEBE";
								break;
							case "H":
								$ls_debhab="HABER";
								break;
						}
		
							$la_data[$li_s]=array('articulo'=>$ls_codart,'cuenta'=>$ls_cuenta,'debhab'=>$ls_debhab,'monto'=>$li_monto);
					}
					uf_print_detalle3($la_data,$io_pdf); // Imprimimos el detalle 
				}
				$li_totcan=number_format($li_totcan,2,",",".");
				$li_total=number_format($li_total,2,",",".");
				$la_data1[1]=array('total'=>'<b>Total</b>','totcan'=>$li_totcan,'vacio'=>'--','totmon'=>$li_total);
				//uf_print_totales($la_data1,$io_pdf); // Imprimimos el detalle 
			}
			unset($la_data);			
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