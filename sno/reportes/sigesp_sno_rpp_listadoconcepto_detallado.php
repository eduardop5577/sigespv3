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
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_resumenconcepto.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hresumenconcepto.php",$ls_descripcion,$ls_codnom);
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
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,720,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,520,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=380-($li_tm/2);
		$io_pdf->addText($tm,535,12,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=390-($li_tm/2);
		$io_pdf->addText($tm,520,12,$as_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=390-($li_tm/2);
		$io_pdf->addText($tm,510,10,$as_desnom); // Agregar el título
		$io_pdf->addText(682,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(688,543,7,date("h:i a")); // Agregar la Hora
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
		// Fecha Creación: 27/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->ezSety(503);
		$io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(51,688,500,$io_pdf->getFontHeight(11.5));
        $io_pdf->setColor(0,0,0);
		$la_datat[1]=array('ctacon'=>'<b>Cuenta Concepto</b>',
						   'ctaapo'=>'<b>Cuenta Aporte</b>');
		$la_columna=array('ctacon'=>'',
						   'ctaapo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xPos'=>551, // Orientación de la tabla
						 'cols'=>array('ctacon'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'ctaapo'=>array('justification'=>'center','width'=>180))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		
		
		
		$la_data[1]=array('codigo'=>'<b>Código</b>',
						   'nombre'=>'<b>Descripción</b>',
						   'tipoconc'=>'<b>Tipo Concepto</b>',
						   'cueprecon'=>'<b>Cuenta Presupuestaria</b>',
						   'cueconcon'=>'<b>Cuenta Contable</b>',
						   'cueprepatcon'=>'<b>Cuenta Presupuestaria</b>',
						   'cueconpatcon'=>'<b>Cuenta Contable</b>');
		$la_columna=array('codigo'=>'',
						   'nombre'=>'',
						   'tipoconc'=>'',
						   'cueprecon'=>'',
						   'cueconcon'=>'',
						   'cueprepatcon'=>'',
						   'cueconpatcon'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'tipoconc'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'cueprecon'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cueconcon'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'cueprepatcon'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
									   'cueconpatcon'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
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
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columnas=array('codigo'=>'<b>Nro</b>',
						   'nombre'=>'<b>Código</b>',
						   'tipoconc'=>'<b>                  Denominación</b>',
						   'cueprecon'=>'<b>Asignación      </b>',
						   'cueconcon'=>'<b>Deducción       </b>',
						   'cueprepatcon'=>'<b>Deducción       </b>',
						   'cueconpatcon'=>'<b>Aporte Patronal</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'tipoconc'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'cueprecon'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cueconcon'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'cueprepatcon'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
									   'cueconpatcon'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_totasi,$ai_totded,$ai_totapo,$ai_totgeneral,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_totasi // Total de Asignaciones
		//	   			   ai_totded // Total de Deducciones
		//	   			   ai_totapo // Total de Aportes
		//	   			   ai_totgeneral // Total de Neto a Pagar
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$la_data=array(array('titulo'=>'<b>Totales '.$ls_bolivares.'</b>','asignacion'=>$ai_totasi,'deduccion'=>$ai_totded,'aporte'=>$ai_totapo));
		$la_columna=array('titulo'=>'','asignacion'=>'','deduccion'=>'','aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>260),
						 			   'asignacion'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	

		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	


		$la_data=array(array('name'=>'<b>Neto a Pagar '.$ls_bolivares.': </b>','total'=>$ai_totgeneral));
		$la_columna=array('name'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize'=> 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((224/255),(224/255),(224/255)), // Color de la sombra
						 'shadeCol2'=>array((224/255),(224/255),(224/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'right','width'=>400), // Justificación y ancho de la columna
						 			   'total'=>array('justification'=>'left','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
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
			$ls_bolivares ="Bs.";
			break;

		case "1":
			if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
			{
				require_once("sigesp_sno_class_reportbsf.php");
				$io_report=new sigesp_sno_class_reportbsf();
				$li_tipo=1;
			}
			if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
			{
				require_once("sigesp_sno_class_report_historicobsf.php");
				$io_report=new sigesp_sno_class_report_historicobsf();
				$li_tipo=2;
			}	
			$ls_bolivares ="Bs.F.";
			break;
	}
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>Codificación Contable y Presupuestaria de los Conceptos</b>";
	$ls_periodo="<b>Período Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codconcdes=$io_fun_nomina->uf_obtenervalor_get("codconcdes","");
	$ls_codconchas=$io_fun_nomina->uf_obtenervalor_get("codconchas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_aportepatronal=$io_fun_nomina->uf_obtenervalor_get("aportepatronal","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$arrResultado=$io_report->uf_listadoconcepto_detalado($ls_codconcdes,$ls_codconchas,$ls_aportepatronal,$ls_conceptocero,$ls_subnomdes,
															$ls_subnomhas,$rs_data,$ls_conceptoreporte); // Cargar el DS con los datos de la cabecera del reporte
		$rs_data=$arrResultado['rs_data'];
		$lb_valido=$arrResultado['lb_valido'];
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
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.38,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(720,50,10,'','',1); // Insertar el número de página
		$li_totrow=$rs_data->RecordCount();
		$li_totasi=0;
		$li_totded=0;
		$li_totapo=0;
		uf_print_cabecera($io_pdf);
		//for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		$li_i=1;
		$la_data=Array();
		while ((!$rs_data->EOF)&&($lb_valido))
		{
			$ls_codconc=$rs_data->fields["codconc"];
			$ls_nomcon=$rs_data->fields["nomcon"];
			$ls_sigcon=rtrim($rs_data->fields["sigcon"]);
			$ls_cueprecon=$rs_data->fields["cueprecon"];
			$ls_cueconcon=$rs_data->fields["cueconcon"];
			$ls_cueprepatcon=$rs_data->fields["cueprepatcon"];
			$ls_cueconpatcon=$rs_data->fields["cueconpatcon"];
			switch($ls_sigcon)
			{
				case "A": // Asignación
				$ls_sigcon="ASIGNACION";
				break;
				
				case "D": // Asignación
				$ls_sigcon="DEDUCCION";
				break;
				
				case "P": // Asignación
				$ls_sigcon="APORTE PATRONAL";
				break;
				
				case "R": // Asignación
				$ls_sigcon="REPORTE";
				break;
				
				case "B": // Asignación
				$ls_sigcon="REINTEGRO DEDUCCION";
				break;
				
				case "E": // Asignación
				$ls_sigcon="REINTEGRO ASIGNACION";
				break;
				
				case "X": // Asignación
				$ls_sigcon="PRESTACION ANTIGUEDAD";
				break;
				
				case "I": // Asignación
				$ls_sigcon="INTERESES DE PRESTACION";
				break;
			}
			$la_data[$li_i]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'tipoconc'=>$ls_sigcon,
								  'cueprecon'=>$ls_cueprecon,'cueconcon'=>$ls_cueconcon,'cueprepatcon'=>$ls_cueprepatcon,
								  'cueconpatcon'=>$ls_cueconpatcon);
			$li_i++;
			$rs_data->MoveNext();
		}
		
		if ((count((array)$la_data))>0)
		{
			uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar!');"); 
			print(" close();");
			print("</script>");		
		}
		/*$li_totgeneral=$li_totasi-$li_totded;
		$li_totasi=$io_fun_nomina->uf_formatonumerico($li_totasi);
		$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
		$li_totapo=$io_fun_nomina->uf_formatonumerico($li_totapo);
		$li_totgeneral=$io_fun_nomina->uf_formatonumerico($li_totgeneral);*/
		//uf_print_piecabecera($li_totasi,$li_totded,$li_totapo,$li_totgeneral,$io_pdf); // Imprimimos el pie de la cabecera
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