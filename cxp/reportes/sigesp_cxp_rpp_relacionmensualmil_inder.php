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
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_recepciones.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
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
		$io_pdf->line(15,40,975,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,"GOBIERNO BOLIVARIANO DEL DISTRITO CAPITAL");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,570,11,"GOBIERNO BOLIVARIANO DEL DISTRITO CAPITAL"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"SUPERINTENDENCIA DE ADMINISTRACION TRIBUTARIA DEL DISTRITO CAPITAL");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,560,11,"SUPERINTENDENCIA DE ADMINISTRACION TRIBUTARIA DEL DISTRITO CAPITAL"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"RELACION MENSUAL");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,11,"RELACION MENSUAL"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"IMPUESTO 1 X 1000 - ENTES PUBLICOS");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,540,11,"IMPUESTO 1 X 1000 - ENTES PUBLICOS"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   li_totaldoc // acumulado del total
		//				   li_totalcar // acumulado de los cargos
		//				   li_totalded // acumulado de las deducciones
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_columnas=array('numsop'=>'<b>ORDEN DE PAGO</b>',
							 'numdocpag'=>'<b>CHEQUE</b>',
							 'vacio1'=>'<b>TRANSFERENCIA</b>',
							 'numcom'=>'<b>No. INSTRUMENTO</b>',
							 'nomban'=>'<b>BANCO</b>',
							 'fecmov'=>'<b>FECHA</b>',
							 'vacio2'=>'<b>No. DEPOSITO</b>',
							 'nomsujret'=>'<b>NOMBRE CONTRIBUYENTE</b>',
							 'rif'=>'<b>RIF</b>',
							 'totcmp_con_iva'=>'<b>MONTO BRUTO</b>',
							 'totimp'=>'<b>MONTO DEL IMPUESTO</b>',
							 'iva_ret'=>'<b>MONTO RETENIDO</b>',
							 'vacio3'=>'<b>OPERACIONES ANULADAS O REVERSADAS</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numsop'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'numdocpag'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'vacio1'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
									   'nomban'=>array('justification'=>'center','width'=>100),// Justificación y ancho de la columna
									   'fecmov'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'vacio2'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'nomsujret'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 			   'rif'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'totcmp_con_iva'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
						 			   'totimp'=>array('justification'=>'right','width'=>55), // Justificación y ancho de la columna
						 			   'iva_ret'=>array('justification'=>'right','width'=>55), // Justificación y ancho de la columna
						 			   'vacio3'=>array('justification'=>'center','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversión de numeros a letras.
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>RECEPCIONES DE DOCUMENTOS</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=trim($io_fun_cxp->uf_obtenervalor_get("codprobendes",""));
	$ls_codprobenhas=trim($io_fun_cxp->uf_obtenervalor_get("codprobenhas",""));
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_codtipdoc=$io_fun_cxp->uf_obtenervalor_get("codtipdoc","");
	$ls_registrada=$io_fun_cxp->uf_obtenervalor_get("registrada","");
	$ls_anulada=$io_fun_cxp->uf_obtenervalor_get("anulada","");
	$ls_procesada=$io_fun_cxp->uf_obtenervalor_get("procesada","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_nomprobendes="";
	$ls_nomprobenhas="";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_retencionesunoxmil($ld_fecregdes,$ld_fecreghas); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4.1,5,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(970,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numcom");
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecmov"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["basimp"][$li_i];
				$li_totimp= '0.001';
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);

//				$li_totaldoc= $li_totaldoc + $li_montotdoc;
//				$li_totalcar= $li_totalcar + $li_moncardoc;
//				$li_totalded= $li_totalded + $li_mondeddoc;

				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,',','.');
				//$li_totimp= number_format($li_totimp,2,',','.');
				$li_iva_ret= number_format($li_iva_ret,2,',','.');

				$la_data[$li_i]=array('numsop'=>$ls_numsop,'numdocpag'=>$ls_numdocpag,'vacio1'=>"",'numcom'=>$ls_numcom,'nomban'=>$ls_nomban,
									  'fecmov'=>$ld_fecmov,'vacio2'=>"",'nomsujret'=>$ls_nomsujret,'rif'=>$ls_rif,
									  'totcmp_con_iva'=>$li_totcmp_con_iva,'totimp'=>$li_totimp,'iva_ret'=>$li_iva_ret,'vacio3'=>"");
			}
			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
			uf_print_detalle_recepcion($la_data,$io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}

?>
