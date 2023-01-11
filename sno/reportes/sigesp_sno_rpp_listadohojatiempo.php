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
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_listadohojatiempo.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hlistadohojatiempo.php",$ls_descripcion,$ls_codnom);
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
		$io_pdf->line(50,40,755,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,540,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,530,11,$as_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,520,10,$as_desnom); // Agregar el título
		$io_pdf->addText(712,560,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(718,553,7,date("h:i a")); // Agregar la Hora		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codper,$as_nomper,$io_cabecera,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // Código de personal
		//	   			   as_nomper // Nombre del personal
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(40,500,700,$io_pdf->getFontHeight(14));
        $io_pdf->setColor(0,0,0);
		$io_pdf->addText(45,505,11,'<b>Personal </b>  '.$as_codper.' - '.$as_nomper.''); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
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
		global $ls_bolivares;
		$io_pdf->ezSetDy(-1);
		$la_columnas=array('anio'=>'<b>Año</b>',
		                   'semana'=>'<b>Semana</b>',
						   'dia'=>'<b>día</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'horario'=>'<b>Horario</b>',
						   'tipohorario'=>'<b>Tipo de Horario</b>',
						   'horini'=>'<b>Entrada</b>',
						   'horfin'=>'<b>Salida</b>',
						   'horlab'=>'<b>Horas Laboradas</b>',
						   'horextlab'=>'<b>Horas Extra Laboradas</b>',
						   'trasub'=>'<b>Subterraneo</b>',
						   'traesc'=>'<b>Escalera</b>',
						   'repcom'=>'<b>Reposo/Comida</b>',
						   'esthojtie'=>'<b>Aprobado</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('anio'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'semana'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'dia'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'horario'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'tipohorario'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'horini'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'horfin'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'horlab'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'horextlab'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'trasub'=>array('justification'=>'center','width'=>55), // Justificación y ancho de la columna
						 			   'traesc'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'repcom'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'esthojtie'=>array('justification'=>'center','width'=>45))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_tottra,$ai_montot,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_tottra // Total de Trabajadores
		//	   			   ai_montot // Monto total por concepto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total Trabajadores</b>'.' '.$ai_tottra.'','monto'=>$ai_montot));
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
						 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
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
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>Planilla de Recolección de Tiempo</b>";
	$ls_periodo="<b>Período Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codhordes=$io_fun_nomina->uf_obtenervalor_get("codhordes","");
	$ls_codhorhas=$io_fun_nomina->uf_obtenervalor_get("codhorhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_coduniadm=$io_fun_nomina->uf_obtenervalor_get("coduniadm","");
	$ls_denuniadm=$io_fun_nomina->uf_obtenervalor_get("denuniadm","");
	$ls_esthojtie=$io_fun_nomina->uf_obtenervalor_get("esthojtie","");
	$ls_semhojtiedes=$io_fun_nomina->uf_obtenervalor_get("semhojtiedes","");
	$ls_semhojtiehas=$io_fun_nomina->uf_obtenervalor_get("semhojtiehas","");
	$ld_fecdes=$io_fun_nomina->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_nomina->uf_obtenervalor_get("fechas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadohojatiempo($ls_codhordes,$ls_codhorhas,$ls_codperdes,$ls_codperhas,$ls_coduniadm,$ld_fecdes,
													$ld_fechas,$ls_esthojtie,$ls_semhojtiedes,$ls_semhojtiehas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false)// Existe algún error ó no hay registros
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
		$io_pdf->ezSetCmMargins(4,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->rs_data->RecordCount();
		$li_i=1;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			uf_print_cabecera($ls_codper,$ls_nomper,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_listadohojatiempo_personal($ls_codhordes,$ls_codhorhas,$ls_codper,$ld_fecdes,$ld_fechas,$ls_esthojtie,$ls_semhojtiedes,$ls_semhojtiehas); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->rs_data_detalle->RecordCount();
				$li_s=1;
				while(!$io_report->rs_data_detalle->EOF)
				{
					$li_anio=substr($io_report->rs_data_detalle->fields["fechojtie"],0,4);
					$li_semhojtie=$io_report->rs_data_detalle->fields["semhojtie"];
					$li_dia = date('N', strtotime($io_report->rs_data_detalle->fields["fechojtie"])); 
					$ld_fechojtie=$io_report->rs_data_detalle->fields["fechojtie"];
					$ld_fechojtie=$io_report->io_funciones->uf_convertirfecmostrar($ld_fechojtie);
					$ls_codhor=$io_report->rs_data_detalle->fields["codhor"];
					$ls_tiphor=$io_report->rs_data_detalle->fields["tiphor"];
					switch($ls_tiphor)
					{
						case "F":
							$ls_tiphor="FIJO";
						break;
						case "R":
							$ls_tiphor="ROTATIVO";
						break;
					}
					$ls_horini=$io_report->rs_data_detalle->fields["horini"];
					$ls_horfin=$io_report->rs_data_detalle->fields["horfin"];
					$li_horlab=$io_report->rs_data_detalle->fields["horlab"];
					$li_horextlab=$io_report->rs_data_detalle->fields["horextlab"];
					$li_trasub=$io_report->rs_data_detalle->fields["trasub"];
					$ls_trasub="No";
					if($li_trasub==1)
					{
						$ls_trasub="Si";
					}
					$li_traesc=$io_report->rs_data_detalle->fields["traesc"];
					$ls_traesc="No";
					if($li_traesc==1)
					{
						$ls_traesc="Si";
					}
					$li_repcom=$io_report->rs_data_detalle->fields["repcom"];
					$ls_repcom="No";
					if($li_repcom==1)
					{
						$ls_repcom="Si";
					}
					$li_esthojtie=$io_report->rs_data_detalle->fields["esthojtie"];
					$ls_esthojtie="No";
					if($li_esthojtie==1)
					{
						$ls_esthojtie="Si";
					}
					$la_data[$li_s]=array('anio'=>$li_anio,'semana'=>$li_semhojtie,'dia'=>$li_dia,'fecha'=>$ld_fechojtie,'horario'=>$ls_codhor,
										  'tipohorario'=>$ls_tiphor,'horini'=>$ls_horini,'horfin'=>$ls_horfin,'horlab'=>$li_horlab,
										  'horextlab'=>$li_horextlab,'trasub'=>$ls_trasub,'traesc'=>$ls_traesc,'repcom'=>$ls_repcom,
										  'esthojtie'=>$ls_esthojtie);					
					$li_s++;
					$io_report->rs_data_detalle->MoveNext();
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				unset($la_data);
			}
			$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
			unset($io_cabecera);
			$li_i++;
			$io_report->rs_data->MoveNext();	
			if($li_i<$li_totrow)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
			}
		}
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