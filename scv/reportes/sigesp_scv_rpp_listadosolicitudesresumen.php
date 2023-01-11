<?PHP
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	//-----------------------------------------------------------------------------------------------------------------------------------
	//Reporte Modificado para aceptar Bs. y Bs.F.
	//Modificado por: Ing. Luis Anibal Lang  08/08/2007	
	//-----------------------------------------------------------------------------------------------------------------------------------
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
	function uf_insert_seguridad($as_titulo,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo    // Título del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_viaticos;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Desde ".$ad_fecregdes.". Hasta ".$ad_fecreghas;
		$lb_valido=$io_fun_viaticos->uf_load_seguridad_reporte("SCV","sigesp_scv_r_listadosolicitudes.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ad_fecregdes,$ad_fecreghas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
//		$io_pdf->rectangle(10,710,580,60);
//		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],15,540,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,560,11,$as_titulo); // Agregar el título
		$ls_periodo="Periodo ".$ad_fecregdes." - ".$ad_fecreghas;
		$li_tm=$io_pdf->getTextWidth(11,$ls_periodo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,550,11,$ls_periodo); // Agregar el título
		$io_pdf->addText(730,580,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(736,573,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($aa_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: aa_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/07/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo="Monto Bs.F.";
		}
		else
		{
			$ls_titulo="Monto Bs.";
		}
		$io_pdf->ezSetDy(-2);
			$la_data[1]=array('mes'=>"MES",'cantidad'=>"CANTIDAD DE ORDENES",'dolares'=>"CANTIDAD DE US$",'bolivares'=>"CANTIDAD DE Bs.");
		$la_columnas=array('mes'=>'<b>Solicitud</b>',
						   'cantidad'=>'<b>Cédula</b>',
						   'dolares'=>'<b>Beneficiario</b>',
						   'bolivares'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('mes'=>array('justification'=>'center','width'=>165), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'center','width'=>85), // Justificación y ancho de la columna
						 			   'dolares'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'bolivares'=>array('justification'=>'center','width'=>85))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_columnas=array('mes'=>'<b>Solicitud</b>',
						   'cantidad'=>'<b>Cédula</b>',
						   'dolares'=>'<b>Beneficiario</b>',
						   'bolivares'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('mes'=>array('justification'=>'center','width'=>165), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'center','width'=>85), // Justificación y ancho de la columna
						 			   'dolares'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'bolivares'=>array('justification'=>'center','width'=>85))); // Justificación y ancho de la columna
		$io_pdf->ezTable($aa_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_total,$ai_montot,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_total // Total de Trabajadores
		//	   			   ai_montot // Monto total por concepto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total Registros</b>'.' '.$ai_total.'','monto'=>$ai_montot));
		$la_columna=array('total'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>650), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_viaticos.php");
	$io_fun_viaticos=new class_funciones_viaticos();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ld_fecregdes=$io_fun_viaticos->uf_obtenervalor_get("desde","");
	$ld_fecreghas=$io_fun_viaticos->uf_obtenervalor_get("hasta","");
	$ls_coduniadm=$io_fun_viaticos->uf_obtenervalor_get("coduniadm","");
	$ls_tiporeporte=$io_fun_viaticos->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipvia=$io_fun_viaticos->uf_obtenervalor_get("tipvia","0");
	$ls_codmis=$io_fun_viaticos->uf_obtenervalor_get("codmisori","");
	$ls_codmisdes=$io_fun_viaticos->uf_obtenervalor_get("codmisdes","");
	$ls_codsoldes=$io_fun_viaticos->uf_obtenervalor_get("codsoldes","");
	$ls_codsolhas=$io_fun_viaticos->uf_obtenervalor_get("codsolhas","");
	$ls_codtipdoc=$io_fun_viaticos->uf_obtenervalor_get("codtipdoc","");
	$ls_continente=$io_fun_viaticos->uf_obtenervalor_get("continente","");
	$ls_estatus=$io_fun_viaticos->uf_obtenervalor_get("estatus","");
	$ls_codben=$io_fun_viaticos->uf_obtenervalor_get("codben","");
	$ls_orden=$io_fun_viaticos->uf_obtenervalor_get("orden","scv_solicitudes.codsolvia");
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Resumen de Solicitud de Viaticos y pasajes por unidad administrativa</b>";
	global $ls_tiporeporte;
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scv_class_reportbsf.php");
		$io_report=new sigesp_scv_class_reportbsf();
	}
	else
	{
		require_once("sigesp_scv_class_report.php");
		$io_report=new sigesp_scv_class_report();
	}	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ld_fecregdes,$ld_fecreghas); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_listadosolicitudes($ld_fecregdes,$ld_fecreghas,$ls_coduniadm,$ls_orden,$ls_tipvia,$ls_codmis,$ls_codmisdes,$ls_codsoldes,$ls_codsolhas,$ls_codtipdoc,$ls_continente,$ls_estatus,$ls_codben); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el número de página
//		$li_totrow=$io_report->ds_solicitud->getRowCount("numsolvia");
		$li_totrow=$io_report->ds_solicitud->getRowCount("codsolvia");
		$li_montot=0;
		$ls_01=0;
		$li_dol01=0;
		$li_bol01=0;
		$ls_02=0;
		$li_dol02=0;
		$li_bol02=0;
		$ls_03=0;
		$li_dol03=0;
		$li_bol03=0;
		$ls_04=0;
		$li_dol04=0;
		$li_bol04=0;
		$ls_05=0;
		$li_dol05=0;
		$li_bol05=0;
		$ls_06=0;
		$li_dol06=0;
		$li_bol06=0;
		$ls_07=0;
		$li_dol07=0;
		$li_bol07=0;
		$ls_08=0;
		$li_dol08=0;
		$li_bol08=0;
		$ls_09=0;
		$li_dol09=0;
		$li_bol09=0;
		$ls_10=0;
		$li_dol10=0;
		$li_bol10=0;
		$ls_11=0;
		$li_dol11=0;
		$li_bol11=0;
		$ls_12=0;
		$li_dol12=0;
		$li_bol12=0;
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_numsolvia=$io_report->ds_solicitud->data["codsolvia"][$li_i];
			$ls_nombre=$io_report->ds_solicitud->data["nombre"][$li_i];
			$ls_cedula=$io_report->ds_solicitud->data["cedula"][$li_i];
			$ls_desrut=$io_report->ds_solicitud->data["desrut"][$li_i];
			$li_monto=$io_report->ds_solicitud->data["monto"][$li_i];
			$ld_fecsalvia=$io_report->ds_solicitud->data["fecsalvia"][$li_i];
			$ld_fecregvia=$io_report->ds_solicitud->data["fecregvia"][$li_i];
			$ld_fecsolvia=$io_report->ds_solicitud->data["fecsolvia"][$li_i];
			$ls_tipvia=$io_report->ds_solicitud->data["tipvia"][$li_i];
			$ls_desciuori=$io_report->ds_solicitud->data["desciuori"][$li_i];
			$ls_desciudes=$io_report->ds_solicitud->data["desciudes"][$li_i];
			$ls_mondolsol=$io_report->ds_solicitud->data["mondolsol"][$li_i];
			$li_montot=$li_montot+$li_monto;
			$ld_fecsalvia=$io_funciones->uf_convertirfecmostrar($ld_fecsalvia);
			$ld_fecregvia=$io_funciones->uf_convertirfecmostrar($ld_fecregvia);
			$ls_mes=substr($ld_fecsolvia,5,2);
		//print $ls_mes."<br>";
			switch ($ls_mes)
			{
				case "01":
					$ls_01=$ls_01+1;
					$li_dol01=$li_dol01+$ls_mondolsol;
					$li_bol01=$li_bol01+$li_monto;
				break;
				case "02":
					$ls_02=$ls_02+1;
					$li_dol02=$li_dol02+$ls_mondolsol;
					$li_bol02=$li_bol02+$li_monto;
				break;
				case "03":
					$ls_03=$ls_03+1;
					$li_dol03=$li_dol03+$ls_mondolsol;
					$li_bol03=$li_bol03+$li_monto;
				break;
				case "04":
					$ls_04=$ls_04+1;
					$li_dol04=$li_dol04+$ls_mondolsol;
					$li_bol04=$li_bol04+$li_monto;
				break;
				case "05":
					$ls_05=$ls_05+1;
					$li_dol05=$li_dol05+$ls_mondolsol;
					$li_bol05=$li_bol05+$li_monto;
				break;
				case "06":
					$ls_06=$ls_06+1;
					$li_dol06=$li_dol06+$ls_mondolsol;
					$li_bol06=$li_bol06+$li_monto;
				break;
				case "07":
					$ls_07=$ls_07+1;
					$li_dol07=$li_dol07+$ls_mondolsol;
					$li_bol07=$li_bol07+$li_monto;
				break;
				case "08":
					$ls_08=$ls_08+1;
					$li_dol08=$li_dol08+$ls_mondolsol;
					$li_bol08=$li_bol08+$li_monto;
				break;
				case "09":
					$ls_09=$ls_09+1;
					$li_dol09=$li_dol09+$ls_mondolsol;
					$li_bol09=$li_bol09+$li_monto;
				break;
				case "10":
					$ls_10=$ls_10+1;
					$li_dol10=$li_dol10+$ls_mondolsol;
					$li_bol10=$li_bol10+$li_monto;
				break;
				case "11":
					$ls_11=$ls_11+1;
					$li_dol11=$li_dol11+$ls_mondolsol;
					$li_bol11=$li_bol11+$li_monto;
				break;
				case "12":
					$ls_12=$ls_12+1;
					$li_dol12=$li_dol12+$ls_mondolsol;
					$li_bol12=$li_bol12+$li_monto;
				break;
			}
			$li_monto=number_format($li_monto,2,',','.');
			$ls_mondolsol=number_format($ls_mondolsol,2,',','.');
		}
		$ls_cantidad=$ls_01+$ls_02+$ls_03+$ls_04+$ls_05+$ls_06+$ls_07+$ls_08+$ls_09+$ls_10+$ls_11+$ls_12;
		$ls_totbol=$li_bol01+$li_bol02+$li_bol03+$li_bol04+$li_bol05+$li_bol06+$li_bol07+$li_bol08+$li_bol09+$li_bol10+$li_bol11+$li_bol12;
		$ls_totdol=$li_dol01+$li_dol02+$li_dol03+$li_dol04+$li_dol05+$li_dol06+$li_dol07+$li_dol08+$li_dol09+$li_dol10+$li_dol11+$li_dol12;
		
		$ls_01=number_format($ls_01,2,',','.');
		$li_dol01=number_format($li_dol01,2,',','.');
		$li_bol01=number_format($li_bol01,2,',','.');
		$ls_02=number_format($ls_02,2,',','.');
		$li_dol02=number_format($li_dol02,2,',','.');
		$li_bol02=number_format($li_bol02,2,',','.');
		$ls_03=number_format($ls_03,2,',','.');
		$li_dol03=number_format($li_dol03,2,',','.');
		$li_bol03=number_format($li_bol03,2,',','.');
		$ls_04=number_format($ls_04,2,',','.');
		$li_dol04=number_format($li_dol04,2,',','.');
		$li_bol04=number_format($li_bol04,2,',','.');
		$ls_05=number_format($ls_05,2,',','.');
		$li_dol05=number_format($li_dol05,2,',','.');
		$li_bol05=number_format($li_bol05,2,',','.');
		$ls_06=number_format($ls_06,2,',','.');
		$li_dol06=number_format($li_dol06,2,',','.');
		$li_bol06=number_format($li_bol06,2,',','.');
		$ls_07=number_format($ls_07,2,',','.');
		$li_dol07=number_format($li_dol07,2,',','.');
		$li_bol07=number_format($li_bol07,2,',','.');
		$ls_08=number_format($ls_08,2,',','.');
		$li_dol08=number_format($li_dol08,2,',','.');
		$li_bol08=number_format($li_bol08,2,',','.');
		$ls_09=number_format($ls_09,2,',','.');
		$li_dol09=number_format($li_dol09,2,',','.');
		$li_bol09=number_format($li_bol09,2,',','.');
		$ls_10=number_format($ls_10,2,',','.');
		$li_dol10=number_format($li_dol10,2,',','.');
		$li_bol10=number_format($li_bol10,2,',','.');
		$ls_11=number_format($ls_11,2,',','.');
		$li_dol11=number_format($li_dol11,2,',','.');
		$li_bol11=number_format($li_bol11,2,',','.');
		$ls_12=number_format($ls_12,2,',','.');
		$li_dol12=number_format($li_dol12,2,',','.');
		$li_bol12=number_format($li_bol12,2,',','.');
		$ls_cantidad=number_format($ls_cantidad,2,',','.');
		$ls_totbol=number_format($ls_totbol,2,',','.');
		$ls_totdol=number_format($ls_totdol,2,',','.');
		
			
			$la_data[1]=array('mes'=>"ENERO",'cantidad'=>$ls_01,'dolares'=>$li_dol01,'bolivares'=>$li_bol01);
			$la_data[2]=array('mes'=>"FEBRERO",'cantidad'=>$ls_02,'dolares'=>$li_dol02,'bolivares'=>$li_bol02);
			$la_data[3]=array('mes'=>"MARZO",'cantidad'=>$ls_03,'dolares'=>$li_dol03,'bolivares'=>$li_bol03);
			$la_data[4]=array('mes'=>"ABRIL",'cantidad'=>$ls_04,'dolares'=>$li_dol04,'bolivares'=>$li_bol04);
			$la_data[5]=array('mes'=>"MAYO",'cantidad'=>$ls_05,'dolares'=>$li_dol05,'bolivares'=>$li_bol05);
			$la_data[6]=array('mes'=>"JUNIO",'cantidad'=>$ls_06,'dolares'=>$li_dol06,'bolivares'=>$li_bol06);
			$la_data[7]=array('mes'=>"JULIO",'cantidad'=>$ls_07,'dolares'=>$li_dol07,'bolivares'=>$li_bol07);
			$la_data[8]=array('mes'=>"AGOSTO",'cantidad'=>$ls_08,'dolares'=>$li_dol08,'bolivares'=>$li_bol08);
			$la_data[9]=array('mes'=>"SEPTIEMBRE",'cantidad'=>$ls_09,'dolares'=>$li_dol09,'bolivares'=>$li_bol09);
			$la_data[10]=array('mes'=>"OCTUBRE",'cantidad'=>$ls_10,'dolares'=>$li_dol10,'bolivares'=>$li_bol10);
			$la_data[11]=array('mes'=>"NOVIEMBRE",'cantidad'=>$ls_11,'dolares'=>$li_dol11,'bolivares'=>$li_bol11);
			$la_data[12]=array('mes'=>"DICIEMBRE",'cantidad'=>$ls_12,'dolares'=>$li_dol12,'bolivares'=>$li_bol12);
			$la_data[12]=array('mes'=>"TOTAL",'cantidad'=>$ls_cantidad,'dolares'=>$ls_totdol,'bolivares'=>$ls_totbol);
		$li_montot=number_format($li_montot,2,',','.');
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
	//uf_print_piecabecera($li_totrow,$li_montot,$io_pdf); // Imprimimos el pie de la cabecera
		unset($io_cabecera);
		unset($la_data);
		$io_report->ds_solicitud->resetds("numsolvia");
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_viaticos);
?> 