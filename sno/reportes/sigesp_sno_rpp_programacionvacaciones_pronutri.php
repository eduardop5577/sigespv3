<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 17/08/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_programacionvacaciones.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hprogramacionvacaciones.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/08/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,955,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,540,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,530,11,$as_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=500-($li_tm/2);
		$io_pdf->addText($tm,520,10,$as_desnom); // Agregar el título
		$io_pdf->addText(920,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(926,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/08/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSety(503);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,879.5,400,$io_pdf->getFontHeight(18.5));
        $io_pdf->setColor(0,0,0);
		$la_data[1]=array('nombre'=>'<b>Apellidos y Nombres</b>',
						  'cedula'=>'<b>Cedula</b>',
					      'cargo'=>'<b>Cargo</b>',
						  'ingreso'=>'<b>Fecha Ingreso</b>',
						  'anioservicio'=>'<b>Años de Servicio</b>',
						  'sueldomensual'=>'<b>Salario Mensual Bsf</b>',
						  'sueldodiario'=>'<b>Salario Diario Bsf</b>',
						  'diashabiles'=>'<b>Días Hábiles (Art. 190 y 191 LOTT)</b>',
						  'diasadicionales'=>'<b>Días Adic. de los Hábiles (Art. 190 LOTT)</b>',
						  'diasbonovac'=>'<b>Días de Bono Vacacional (Art. 192 LOTT)</b>',
						  'diasadicbonovac'=>'<b>Días Adic. de Bono Vacacional (Art. 192 LOTT)</b>',
						  'diasdescanso'=>'<b>Días Descanso y Feriados dentro de vacaciones</b>',
						  'totaldias'=>'<b>Total Días a Disfrutar</b>',
						  'periodo'=>'<b>Período de Disfrute</b>',
						  'desde'=>'<b>Fecha Desde</b>',
						  'hasta'=>'<b>Fecha Hasta</b>',
						  'totaldiaspagar'=>'<b>Total de Días a Pagar</b>',
						  'monto'=>'<b>Monto a Pagar</b>',
						  'firma'=>'<b>Firma Trabajador</b>');
		$la_columna=array('nombre'=>'',
						  'cedula'=>'',
					      'cargo'=>'',
						  'ingreso'=>'',
						  'anioservicio'=>'',
						  'sueldomensual'=>'',
						  'sueldodiario'=>'',
						  'diashabiles'=>'',
						  'diasadicionales'=>'',
						  'diasbonovac'=>'',
						  'diasadicbonovac'=>'',
						  'diasdescanso'=>'',
						  'totaldias'=>'',
						  'periodo'=>'',
						  'desde'=>'',
						  'hasta'=>'',
						  'totaldiaspagar'=>'',
						  'monto'=>'',
						  'firma'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'ingreso'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'anioservicio'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'sueldomensual'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'sueldodiario'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diashabiles'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasadicionales'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasbonovac'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasadicbonovac'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasdescanso'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'totaldias'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'periodo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'desde'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'hasta'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'totaldiaspagar'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'firma'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabecera
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
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columnas=array('nombre'=>'',
						  'cedula'=>'',
					      'cargo'=>'',
						  'ingreso'=>'',
						  'anioservicio'=>'',
						  'sueldomensual'=>'',
						  'sueldodiario'=>'',
						  'diashabiles'=>'',
						  'diasadicionales'=>'',
						  'diasbonovac'=>'',
						  'diasadicbonovac'=>'',
						  'diasdescanso'=>'',
						  'totaldias'=>'',
						  'periodo'=>'',
						  'desde'=>'',
						  'hasta'=>'',
						  'totaldiaspagar'=>'',
						  'monto'=>'',
						  'firma'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'ingreso'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'anioservicio'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'sueldomensual'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'sueldodiario'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diashabiles'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasadicionales'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasbonovac'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasadicbonovac'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'diasdescanso'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'totaldias'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'periodo'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'desde'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'hasta'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'totaldiaspagar'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'firma'=>array('justification'=>'center','width'=>50))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
	{
		require_once("sigesp_sno_class_report.php");
		$io_report=new sigesp_sno_class_report();
		$li_tipo=1;
	}
	if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
	{
		require_once("sigesp_sno_class_report_historico.php");
		$io_report=new sigesp_sno_class_report_historico();
		$li_tipo=2;
	}	
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ls_titulo="<b>Vacaciones Programadas</b>";
	$ls_periodo="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_estvac=$io_fun_nomina->uf_obtenervalor_get("estvac","");
	$ld_fecdisdes=$io_fun_nomina->uf_obtenervalor_get("fecdisdes","");
	$ld_fecdishas=$io_fun_nomina->uf_obtenervalor_get("fecdishas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	if($ld_fecdisdes!="")
	{
		$ls_periodo="<b>Desde ".$ld_fecdisdes." Hasta ".$ld_fecdishas."</b>";
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_programacionvacaciones_personal($ls_estvac,$ld_fecdisdes,$ld_fecdishas,$ls_subnomdes,$ls_subnomhas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
	}
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
		$io_pdf->ezSetCmMargins(4.35,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(950,50,10,'','',1); // Insertar el número de página
		uf_print_cabecera($io_pdf);
		$li_totrow=$io_report->DS->getRowCount("codper");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_nomper=$io_report->DS->data["apeper"][$li_i].", ".$io_report->DS->data["nomper"][$li_i];
			$ls_codcar=$io_report->DS->data["codcar"][$li_i];
			$ls_descar=$io_report->DS->data["descar"][$li_i];
			if($ls_codcar=='0000000000')
			{
				$ls_descar=$io_report->DS->data["denasicar"][$li_i];			
			}
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecingper"][$li_i]);
			$li_sueintbonvac=number_format($io_report->DS->data["sueintbonvac"][$li_i],2,',','.');
			$li_sueldodiario=number_format(($io_report->DS->data["sueintbonvac"][$li_i]/30),2,',','.');
			$li_dianorvac=number_format($io_report->DS->data["dianorvac"][$li_i],0,',','.');
			$li_diafer=number_format($io_report->DS->data["diafer"][$li_i],0,',','.');
			$li_sabdom=number_format($io_report->DS->data["sabdom"][$li_i],0,',','.');
			$li_diavac=number_format($io_report->DS->data["diavac"][$li_i],0,',','.');
			$li_diaadivac=number_format($io_report->DS->data["diaadivac"][$li_i],0,',','.');
			$li_diabonvac=number_format($io_report->DS->data["diabonvac"][$li_i],0,',','.');
			$li_diaadibon=number_format($io_report->DS->data["diaadibon"][$li_i],0,',','.');
			$li_diasdescanso=number_format($li_diafer+$li_sabdom,0,',','.');
			$li_totaldiaspagar=number_format($li_diabonvac+$li_diaadibon,0,',','.');
			$li_codvac=$io_report->DS->data["codvac"][$li_i];
			$la_periodo="".number_format(substr($io_report->DS->data["fecvenvac"][$li_i],0,4)-1)." - ".substr($io_report->DS->data["fecvenvac"][$li_i],0,4)."";
			$ld_fecdisvac=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecdisvac"][$li_i]);
			$ld_fecreivac=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecreivac"][$li_i]);
			$li_anioservicio=number_format(substr($_SESSION["la_nomina"]["fechasper"],0,4)-substr($io_report->DS->data["fecingper"][$li_i],0,4),0,',','.');
			if(intval(substr($_SESSION["la_nomina"]["fechasper"],5,2))<intval(substr($io_report->DS->data["fecingper"][$li_i],5,2)))
			{
				$li_anioservicio=$li_anioservicio-1;
			}
			else
			{
				if(intval(substr($_SESSION["la_nomina"]["fechasper"],5,2))==intval(substr($io_report->DS->data["fecingper"][$li_i],5,2)))
				{
					if(intval(substr($_SESSION["la_nomina"]["fechasper"],8,2))<intval(substr($io_report->DS->data["fecingper"][$li_i],8,2)))
					{
						$li_anioservicio=$li_anioservicio-1;
					}
				}
			}
			
			
			$la_data[$li_i]=array('nombre'=>$ls_nomper,
						  		  'cedula'=>$ls_cedper,
					      		  'cargo'=>$ls_descar,
						  		  'ingreso'=>$ld_fecingper,
						  		  'anioservicio'=>$li_anioservicio,
						  		  'sueldomensual'=>$li_sueintbonvac,
								  'sueldodiario'=>$li_sueldodiario,
								  'diashabiles'=>$li_diavac,
								  'diasadicionales'=>$li_diaadivac,
								  'diasbonovac'=>$li_diabonvac,
								  'diasadicbonovac'=>$li_diaadibon,
								  'diasdescanso'=>$li_diasdescanso,
								  'totaldias'=>$li_dianorvac,
								  'periodo'=>$la_periodo,
								  'desde'=>$ld_fecdisvac,
								  'hasta'=>$ld_fecreivac,
								  'totaldiaspagar'=>$li_totaldiaspagar,
								  'monto'=>'',
								  'firma'=>'');

		}
		$io_report->DS->resetds("codper");
		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		unset($la_data);
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
	unset($io_fun_nomina);
?> 