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
	function uf_print_encabezado_pagina($as_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf)
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
		global $io_pdf,$io_funciones;
		$ld_fecregdes= $io_funciones->uf_convertirfecmostrar($ld_fecregdes);
		$ld_fecreghas= $io_funciones->uf_convertirfecmostrar($ld_fecreghas);

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,975,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,"MINISTERIO POPULAR PARA LA DEFENSA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,570,10,"MINISTERIO POPULAR PARA LA DEFENSA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,"ESTADO MAYOR DE LA DEFENSA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,560,10,"ESTADO MAYOR DE LA DEFENSA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,"DIRECCION GENERAL DE CONTROL DE GESTION DE EMPRESA Y SERVICIO");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,10,"DIRECCION GENERAL DE CONTROL DE GESTION DE EMPRESA Y SERVICIO"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,"INSTITUTO DE PREVISION SOCIAL DE LA FUERZA ARMADA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,540,10,"INSTITUTO DE PREVISION SOCIAL DE LA FUERZA ARMADA"); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(10,"UNIDAD DE TRIBUTOS INTERNOS");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,10,"UNIDAD DE TRIBUTOS INTERNOS"); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(10,"LIBRO DE RETENCIONES DE RESPONSABILIDAD SOCIAL");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,520,10,"LIBRO DE RETENCIONES DE RESPONSABILIDAD SOCIAL"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,"PERIODO DESDE: ".$ld_fecregdes." HASTA: ".$ld_fecreghas);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,510,10,"PERIODO DESDE: ".$ld_fecregdes." HASTA: ".$ld_fecreghas); // Agregar el título
		
		$io_pdf->Rectangle(20,60,955,70);
		$io_pdf->line(497,60,497,130);		
		$io_pdf->addText(162,80,7,"TCNEL. MARIA D. SALCEDO SOMAZA"); // Agregar el título
		$io_pdf->addText(160,70,7,"JEFE DEL DPTO. DE TRIBUTO INTERNO"); // Agregar el título
		$io_pdf->addText(672,80,7,"MAY. TERRY ALEXANDER MITCHELL"); // Agregar el título
		$io_pdf->addText(670,70,7,"TESORERO/GERENTE DE RETENCION"); // Agregar el título
		// cuadro inferior
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

		$la_columnas=array('item'=>'<b>RENGLON</b>',
							 'nomsujret'=>'<b>BENEFICIARIO</b>',
							 'numdoccom'=>'<b>CONTRATO, O/C, O/S</b>',
							 'fecordcom'=>'<b>FECHA</b>',
							 'basimp'=>'<b>MONTO OBJ RETENCION</b>',
							 'totimp'=>'<b>IVA</b>',
							 'totcmp_con_iva'=>'<b>MONTO TOTAL</b>',
							 'numfac'=>'<b>FACTURA</b>',
							 'fecemidoc'=>'<b>FECHA</b>',
							 'numsop'=>'<b>SOLICITUD DE PAGO</b>',
							 'fecmov'=>'<b>FECHA</b>',
							 'numcom'=>'<b>NUMERO DE COMPROBANTE</b>',
							 'iva_ret'=>'<b>MONTO RETENIDO</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('item'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'nomsujret'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
						 			   'numdoccom'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'fecordcom'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'basimp'=>array('justification'=>'right','width'=>60),// Justificación y ancho de la columna
									   'totimp'=>array('justification'=>'right','width'=>50), // Justificación y ancho de la columna
						 			   'totcmp_con_iva'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numsop'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'fecmov'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'iva_ret'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
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

		$la_columnas=array('item'=>'<b>RENGLON</b>',
							 'nomsujret'=>'<b>BENEFICIARIO</b>',
							 'numdoccom'=>'<b>CONTRATO, O/C, O/S</b>',
							 'fecordcom'=>'<b>FECHA</b>',
							 'basimp'=>'<b>MONTO OBJ RETENCION</b>',
							 'totimp'=>'<b>IVA</b>',
							 'totcmp_con_iva'=>'<b>MONTO TOTAL</b>',
							 'numfac'=>'<b>FACTURA</b>',
							 'fecemidoc'=>'<b>FECHA</b>',
							 'numsop'=>'<b>SOLICITUD DE PAGO</b>',
							 'fecmov'=>'<b>FECHA</b>',
							 'numcom'=>'<b>NUMERO DE COMPROBANTE</b>',
							 'iva_ret'=>'<b>MONTO RETENIDO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('item'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'nomsujret'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
						 			   'numdoccom'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'fecordcom'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
									   'basimp'=>array('justification'=>'right','width'=>60),// Justificación y ancho de la columna
									   'totimp'=>array('justification'=>'right','width'=>50), // Justificación y ancho de la columna
						 			   'totcmp_con_iva'=>array('justification'=>'right','width'=>60), // Justificación y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numsop'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'fecmov'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'iva_ret'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
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

		$lb_valido=$io_report->uf_retencionesaporte($ld_fecregdes,$ld_fecreghas); // Cargar el DS con los datos del reporte
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
			$li_totalbasimp= 0;
			$li_totaltotimp= 0;
			$li_totaltotcmp_con_iva= 0;
			$li_totalli_iva_ret= 0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecemisol"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["totcmp_con_iva"][$li_i];
				$li_totimp= $io_report->DS->data["totimp"][$li_i];
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$li_basimp= $io_report->DS->data["basimp"][$li_i];
				$ls_numfac= $io_report->DS->data["numfac"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);
				$rs_compromisos=$io_report->uf_select_compromisos_relacionados($ls_numsop);
				$ls_anexo="";
				$ls_anexo2="";
				while(!$rs_compromisos->EOF)
				{
					$ls_numdoccom=$rs_compromisos->fields["numdoccom"];
					$ls_procede=$rs_compromisos->fields["procede_doc"];
					$ls_fecordcom=$io_report->uf_buscar_fechaOC($ls_numdoccom,$ls_procede);
					$ls_fecordcom= $io_funciones->uf_convertirfecmostrar($ls_fecordcom);
					$ls_anexo=$ls_anexo."  ".$ls_numdoccom;
					$ls_anexo2=$ls_anexo2."  ".$ls_fecordcom;
					$rs_compromisos->MoveNext();
				}
				$lb_valido=$io_report->uf_select_rec_doc_solicitud($ls_numsop); // Cargar el DS con los datos del reporte
				$ls_anexo3="";
				if($lb_valido)
				{
					$li_totrowdet=$io_report->ds_detalle_rec->getRowCount("numrecdoc");
					for($li_s=1;$li_s<=$li_totrowdet;$li_s++)
					{
						$ld_fecemidoc=$io_report->ds_detalle_rec->data["fecemidoc"][$li_s];
						$ld_fecemidoc= $io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
						$ls_anexo3=$ls_anexo3."  ".$ld_fecemidoc;
					}
				}

				$li_totalbasimp= $li_totalbasimp + $li_basimp;
				$li_totaltotimp= $li_totaltotimp + $li_totimp;
				$li_totaltotcmp_con_iva= $li_totaltotcmp_con_iva + $li_totcmp_con_iva;
				$li_totalli_iva_ret= $li_totalli_iva_ret + $li_iva_ret;

				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,',','.');
				$li_totimp= number_format($li_totimp,2,',','.');
				$li_iva_ret= number_format($li_iva_ret,2,',','.');
				$li_basimp= number_format($li_basimp,2,',','.');

				$la_data[$li_i]=array('item'=>$li_i,'numsop'=>$ls_numsop,'numdocpag'=>$ls_numdocpag,'numdoccom'=>$ls_anexo,'fecordcom'=>$ls_anexo2,
									  'vacio1'=>"",'numcom'=>$ls_numcom,'nomban'=>$ls_nomban,'fecemidoc'=>$ls_anexo3,'basimp'=>$li_basimp,'numfac'=>$ls_numfac,
									  'fecmov'=>$ld_fecmov,'vacio2'=>"",'nomsujret'=>$ls_nomsujret,'rif'=>$ls_rif,
									  'totcmp_con_iva'=>$li_totcmp_con_iva,'totimp'=>$li_totimp,'iva_ret'=>$li_iva_ret,'vacio3'=>"");
			}
			$li_basimp= number_format($li_basimp,2,',','.');
			$li_basimp= number_format($li_basimp,2,',','.');
			$li_basimp= number_format($li_basimp,2,',','.');
			$li_basimp= number_format($li_basimp,2,',','.');
			$la_datatot[$li_i]=array('item'=>"",'numsop'=>"",'numdocpag'=>"",'numdoccom'=>"",'fecordcom'=>"",
								  'vacio1'=>"",'numcom'=>"",'nomban'=>"",'fecemidoc'=>"",'basimp'=>$li_totalbasimp,'numfac'=>"",
								  'fecmov'=>"",'vacio2'=>"",'nomsujret'=>"",'rif'=>"",
								  'totcmp_con_iva'=>$li_totaltotcmp_con_iva,'totimp'=>$li_totaltotimp,'iva_ret'=>$li_iva_ret,'vacio3'=>"");
			uf_print_encabezado_pagina($ls_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf);
			uf_print_detalle_recepcion($la_data,$io_pdf);
			uf_print_totales($la_datatot,$io_pdf);
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
