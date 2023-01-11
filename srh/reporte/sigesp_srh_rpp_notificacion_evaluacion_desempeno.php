<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
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
		global $io_fun_srh;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_srh->uf_load_seguridad_reporte("SRH","sigesp_srh_r_listado_evaluacion_desempeno.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_uniadm,$as_fecfine,$io_encabezado,$io_pdf)
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
		global $io_pdf,$io_encabezado ;
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,760,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(12,"REPÚBLICA BOLIVARIANA DE VENEZUELA");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,790,12,"REPÚBLICA BOLIVARIANA DE VENEZUELA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(12,"MINISTERIO DEL PODER POPULAR PARA LA DEFENSA");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,775,12,"MINISTERIO DEL PODER POPULAR PARA LA DEFENSA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(12,"INSTITUTO DE PREVISION SOCIAL DE LA FUERZA ARMADA");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,760,12,"INSTITUTO DE PREVISION SOCIAL DE LA FUERZA ARMADA"); // Agregar el título
		$io_pdf->addText(80,720,12,"GERENCIA: <b>".$as_uniadm."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"NOTIFICACIÓN DE EVALUACIÓN DE DESEMPEÑO");
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,700,11,"NOTIFICACIÓN DE EVALUACIÓN DE DESEMPEÑO"); // Agregar el título
		$li_mes=intval(substr($as_fecfine,3,2));
		$li_anio=substr($as_fecfine,6,4);
		$ls_semestre = ' PRIMER ';
		if ($li_mes>6)
		{
			$ls_semestre = ' SEGUNDO ';
		}
		$li_tm=$io_pdf->getTextWidth(11,$ls_semestre."SEMESTRE AÑO ".$li_anio);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,685,11,$ls_semestre."SEMESTRE AÑO ".$li_anio); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detallecontenido($as_nomper,$as_apeper,$as_cedper,$as_actuacion,$as_fecinie,$as_fecfine,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detallecontenido
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
		$io_pdf->ezSetDy(-5);
		$ls_texto = 'Por medio de la presente se notifica a <b>'.$as_apeper.' '.$as_nomper.'</b>, titular de la cédula de identidad No.'.
								   '<b>'.$as_cedper.'</b>, quien obtuvo en su Evaluación de Desempeño un Rango de Actuación'.
								   ' <b>'.$as_actuacion.'</b>, en el período comprendido <b>'.$as_fecinie.'</b> al <b>'.$as_fecfine.'</b>.';
		$io_pdf->ezText($ls_texto,11,array('justification' =>'full', 'spacing' =>2));
		$io_pdf->ezSetDy(-15);
		$ls_texto = 'Notificación que hago a usted, de conformidad con lo provisto en el Art. 62 de la Ley del Estatuto de la Función Pública';
		$io_pdf->ezText($ls_texto,11,array('justification' =>'full', 'spacing' =>2));
		$io_pdf->ezSetDy(-15);
		$ls_texto = 'Notificación que se expide en caracas a la fecha: '.date('d/m/Y');
		$io_pdf->ezText($ls_texto,11,array('justification' =>'full', 'spacing' =>2));
		$io_pdf->ezSetDy(-150);
		$la_data[1]=array('texto'=>'<b>CC JOEL MENA SORETT</b>');
		$la_data[2]=array('texto'=>'<b>GERENTE DE RECURSOS HUMANOS</b>');
		$la_columnas=array('texto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-60);
		$la_data[1]=array('texto'=>'Nombre y Firma del Supervisor','texto2'=>'Firma del Evaluado');
		$la_data[2]=array('texto'=>'Evaluador','texto2'=>'');
		$la_data[3]=array('texto'=>'','texto2'=>'Fecha:');
		$la_data[4]=array('texto'=>'','texto2'=>'');
		$la_columnas=array('texto'=>'','texto2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>300),
						 			   'texto2'=>array('justification'=>'left','width'=>200))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('texto'=>'..." Los resultados de la evaluación deberán ser notificados al evaluado, quien podrá solicitar por escrito'.
								   ' la reconsideración de los mismos dentro de los primeros cinco días hábiles siguientes a su notificación"...');
		$la_columnas=array('texto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

    require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");	
	require_once("class_folder/sigesp_srh_class_report.php");
	$io_report=new sigesp_srh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/utilidades/class_funciones_srh.php");
	$io_fun_srh=new class_funciones_srh('../../');
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ld_fechades=$io_fun_srh->uf_obtenervalor_get("fechades","");
	$ld_fechahas=$io_fun_srh->uf_obtenervalor_get("fechahas","");
	$ls_codperdes=$io_fun_srh->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_srh->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_srh->uf_obtenervalor_get("orden","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_evaluacion_desemp($ld_fechades,$ld_fechahas,$ls_codperdes,$ls_codperhas,$ls_orden); // Cargar el DS con los datos del reporte
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
			$io_pdf=new Cezpdf('PORTRAIT','letter'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(7,3,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(770,47,8,'','',1); // Insertar el número de página
			while(!$io_report->rs_data->EOF)
			{
				$ls_cedper=$io_report->rs_data->fields["cedper"];
				$ls_nomper=$io_report->rs_data->fields["nomper"];
				$ls_apeper=$io_report->rs_data->fields["apeper"];
				$ls_actuacion=$io_report->rs_data->fields["actuacion"];
				$ls_fecinie=$io_report->rs_data->fields["fecinie"];
				$ls_fecfine=$io_report->rs_data->fields["fecfine"];
				$ls_uniadm=$io_report->rs_data->fields["desuniadm"];
			   	$ls_fecinie=$io_funciones->uf_formatovalidofecha($ls_fecinie);
				$ls_fecinie=$io_funciones->uf_convertirfecmostrar($ls_fecinie);
				$ls_fecfine=$io_funciones->uf_formatovalidofecha($ls_fecfine);
				$ls_fecfine=$io_funciones->uf_convertirfecmostrar($ls_fecfine);
				$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
				uf_print_encabezado_pagina($ls_uniadm,$ls_fecfine,$io_cabecera,$io_pdf);
				uf_print_detallecontenido($ls_nomper,$ls_apeper,$ls_cedper,$ls_actuacion,$ls_fecinie,$ls_fecfine, $io_pdf);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
				unset($io_cabecera);
				$io_report->rs_data->MoveNext();
				if(!$io_report->rs_data->EOF)
				{
					$io_pdf->ezNewPage(); // Insertar una nueva página
				}
			}
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
