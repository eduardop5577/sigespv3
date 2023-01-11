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
		$li_tm=$io_pdf->getTextWidth(11,"REPUBLICA BOLIVARIANA DE VENEZUELA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,580,11,"REPUBLICA BOLIVARIANA DE VENEZUELA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"GOBERNACION DEL ESTADO LARA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,570,11,"GOBERNACION DEL ESTADO LARA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"SERVICIO AUTONOMO DE ADMINISTRACION TRIBUTARIA DEL ESTADO LARA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,560,11,"SERVICIO AUTONOMO DE ADMINISTRACION TRIBUTARIA DEL ESTADO LARA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"RENDICION INFORMATIVA MENSUAL IMPUESTO 1 X 1000");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,11,"RENDICION INFORMATIVA MENSUAL IMPUESTO 1 X 1000"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"ENTES PUBLICOS");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,520,11,"ENTES PUBLICOS"); // Agregar el título
		// cuadro inferior
	/*	$io_pdf->line(18,117,772,117);		
		$io_pdf->line(18,60,203,130);		
		$io_pdf->line(18,60,391,130);		
		$io_pdf->line(18,60,579,130);		
		$io_pdf->addText(80,122,7,"ELABORADO POR"); // Agregar el título
		$io_pdf->addText(82,63,7,"FIRMA / SELLO"); // Agregar el título
		$io_pdf->addText(262,122,7,"VERIFICADO POR"); // Agregar el título
		$io_pdf->addText(252,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->addText(460,122,7,"AUTORIZADO POR"); // Agregar el título
		$io_pdf->addText(440,63,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
		$io_pdf->addText(635,122,7,"CONTRALORIA INTERNA"); // Agregar el título
		$io_pdf->addText(635,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título*/
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($ld_fecregdes,$ld_fecreghas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$ls_mesdes=substr($ld_fecregdes,3,2);
		$ls_meshas=substr($ld_fecreghas,3,2);
		$ls_yeardes=substr($ld_fecregdes,6,4);
		$ls_yearhas=substr($ld_fecreghas,6,4);
		$ls_periodofiscal=$ls_meshas." - ".$ls_yearhas;
		$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
		$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$io_pdf->setStrokeColor(0,0,0);
		$la_data[1]=array('name'=>'(1)ENTE PUBLICO: '.$ls_agenteret);
		$la_data[2]=array('name'=>'(2)RIF: '.$ls_rifagenteret);
		$la_data[3]=array('name'=>'(3)DIRECCION: '.$ls_diragenteret);
		$la_data[4]=array('name'=>'(4)PERIODO FISCAL A INFORMAR: '.$ls_periodofiscal);
		$la_data[5]=array('name'=>'(5)NUMERO DE PLANILLA  DE DEPOSITO BANCARIO: ____________________');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientacion de la tabla
						 'width'=>600, // Ancho de la tabla						 
						 'maxWidth'=>600); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);								
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
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

		$io_pdf->EzSetDy(-10);
		$la_columnas=array('numsop'=>'<b>(6) No.</b>',
							 'numdocpag'=>'<b>(7) FECHA DE ORDEN DE PAGO</b>',
							 'vacio1'=>'<b>(8) NUMERO DE ORDEN DE PAGO</b>',
							 'numcom'=>'<b>(9) NOMBRE CONTRIBUYENTE</b>',
							 'nomban'=>'<b>(10) C.I./ R.IF DEL CONTRIBUYENTE</b>',
							 'fecmov'=>'<b>(11) MONTO DE LA OBRA/SERVICIO</b>',
							 'vacio2'=>'<b>(12) MONTO  BRUTO DE ORDEN DE PAGO  </b>',
							 'nomsujret'=>'<b>(13) MONTO DEL IMPUESTO RETENIDO</b>',
							 'rif'=>'<b>(14) TIPO DE PAGO</b>',
							 'totcmp_con_iva'=>'<b>(15) MUNICIPIO </b>',
							 'vacio3'=>'<b>(16)OPERACIONES ANULADAS</b>');
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
						 'cols'=>array('numsop'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'numdocpag'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'vacio1'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>205), // Justificación y ancho de la columna
									   'nomban'=>array('justification'=>'center','width'=>80),// Justificación y ancho de la columna
									   'fecmov'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'vacio2'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'nomsujret'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'rif'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'totcmp_con_iva'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'vacio3'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($li_total_iva_ret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_pdf->EzSetDy(-2);
		$la_data[1]=array('numsop'=>'(17) TOTAL DEL IMPUESTO RETENIDO ','vacio3'=>$li_total_iva_ret);		
		$io_pdf->setStrokeColor(0,0,0);
		$la_columna=array('numsop'=>'','vacio3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>640, // Orientacion de la tabla
						 'width'=>600, // Ancho de la tabla						 
						 'maxWidth'=>600, // Ancho Minimo de la tabla
 						 'cols'=>array('numsop'=>array('justification'=>'left','width'=>180), // Justificación y ancho de la columna
						 			   'vacio3'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
       $io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);								
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(18,50,350,135);
		$io_pdf->line(18,170,368,170);		
		$io_pdf->line(18,155,368,155);		
		$io_pdf->line(18,140,368,140);		
		$io_pdf->line(18,125,368,125);		
		$io_pdf->line(18,110,368,110);		
		$io_pdf->line(18,95,368,95);		
		$io_pdf->line(18,80,368,80);		
		$io_pdf->line(18,65,368,65);		
		$io_pdf->addText(60,172,9,"(18) DATOS DEL RESPONSABLE DE LA DECLARACION "); // Agregar el título
		$io_pdf->addText(125,157,9,"AGENTE  DE RETENCION "); // Agregar el título
		$io_pdf->addText(25,142,9,"NOMBRE Y APELLIDO"); // Agregar el título
		$io_pdf->addText(25,127,9,"NUMERO C.I. "); // Agregar el título
		$io_pdf->addText(25,112,9,"CARGO"); // Agregar el título
		$io_pdf->addText(25,97,9,"FIRMA"); // Agregar el título
		$io_pdf->addText(25,82,9,"TELEFONO"); // Agregar el título
		$io_pdf->addText(25,67,9,"CORREO  ELECTRONICO  "); // Agregar el título
		$io_pdf->addText(25,52,9,"SELLO"); // Agregar el título
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			

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
			$io_pdf->ezSetCmMargins(4.1,7,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(970,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numcom");
			uf_print_cabecera($ld_fecregdes,$ld_fecreghas,$io_pdf);
			$li_total_iva_ret=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecmov"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["totcmp_con_iva"][$li_i];
				$li_totimp= $io_report->DS->data["totimp"][$li_i];
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$li_montopag= $io_report->DS->data["montopag"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);

				$li_total_iva_ret=$li_total_iva_ret+$li_iva_ret;
//				$li_totaldoc= $li_totaldoc + $li_montotdoc;
//				$li_totalcar= $li_totalcar + $li_moncardoc;
//				$li_totalded= $li_totalded + $li_mondeddoc;

				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,',','.');
				$li_totimp= number_format($li_totimp,2,',','.');
				$li_iva_ret= number_format($li_iva_ret,2,',','.');
				$li_montopag= number_format($li_montopag,2,',','.');

				$la_data[$li_i]=array('numsop'=>$li_i,'numdocpag'=>$ld_fecmov,'vacio1'=>$ls_numsop,'numcom'=>$ls_nomsujret,'nomban'=>$ls_rif,
									  'fecmov'=>$li_montopag,'vacio2'=>$li_totcmp_con_iva,'nomsujret'=>$li_iva_ret,'rif'=>"CHEQUE",
									  'totcmp_con_iva'=>"PALAVECINO",'vacio3'=>"");
			}
			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
			uf_print_detalle_recepcion($la_data,$io_pdf);
			$li_total_iva_ret= number_format($li_total_iva_ret,2,',','.');
			uf_print_total($li_total_iva_ret,$io_pdf);
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
