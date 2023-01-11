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

function uf_restar_fechas($ls_fecini,$ls_fecfin)
{

	$diaActual = substr($ls_fecfin, 8, 2); 
	$mesActual = substr($ls_fecfin, 5,2); 
	$anioActual = substr($ls_fecfin, 0,4); 
	$diaInicio = substr($ls_fecini, 8, 2); 
	$mesInicio = substr($ls_fecini, 5,2); 
	$anioInicio = substr($ls_fecini, 0,4); 
	$b = 0; 
	$mes = $mesInicio-1; 
	if($mes==2)
	{ 
		if(($anioActual%4==0 && $anioActual%100!=0) || $anioActual%400==0)
		{ 
			$b = 29; 
		}
		else
		{ 
			$b = 28; 
		} 
	} 
	else if($mes<=7)
	{ 
		if($mes==0)
		{ 
			$b = 31; 
		} 
		else
		{
			if($mes%2==0)
			{ 
				$b = 30; 
			} 
			else
			{ 
				$b = 31; 
			}
		} 
	} 
	else if($mes>7)
	{ 
		if($mes%2==0)
		{ 
			$b = 31; 
		} 
		else
		{ 
			$b = 30; 
		} 
	} 
	if(($anioInicio>$anioActual) || ($anioInicio==$anioActual && $mesInicio>$mesActual) || ($anioInicio==$anioActual && $mesInicio == $mesActual && $diaInicio>$diaActual))
	{ 
		echo "La fecha de inicio ha de ser anterior a la fecha Actual"; 
	}
	else
	{ 
		if($mesInicio <= $mesActual)
		{ 
			$anios = $anioActual - $anioInicio; 
			if($diaInicio <= $diaActual)
			{ 
				$meses = $mesActual - $mesInicio; 
				$dias = $diaActual - $diaInicio; 
			}
			else
			{ 
				if($mesActual == $mesInicio)
				{ 
					$anios = $anios - 1; 
				} 
				$meses = ($mesActual - $mesInicio - 1 + 12) % 12; 
				$dias = $b-($diaInicio-$diaActual); 
			} 
		}
		else
		{ 
			$anios = $anioActual - $anioInicio - 1; 
			if($diaInicio > $diaActual)
			{ 
				$meses = $mesActual - $mesInicio -1 +12; 
				$dias = $b - ($diaInicio-$diaActual); 
			}
			else
			{ 
				$meses = $mesActual - $mesInicio + 12; 
				$dias = $diaActual - $diaInicio; 
			} 
		}
		$arrResultado["anios"]= $anios;
		$arrResultado["meses"]= $meses;
		$arrResultado["dias"]= $dias;
		return $arrResultado;

	}
}

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
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_relacionvacaciones.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hrelacionvacaciones.php",$ls_descripcion,$ls_codnom);
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
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$as_periodo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,10,$as_desnom); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nomper,$ad_fecingper,$as_desuniadm,$ai_sueintvac,$ad_fecdisvac,$ad_fecreivac,
							   $ai_diavac,$as_codvac,$as_descar,$ai_sueintdia,$as_sueint,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cedper // Cédula del personal 
		//	   			   as_nomcon // Nombre del personal
		//	    		   ad_fecingper // fecha de ingreso del personal
		//	    		   as_desuniadm // Descripción de la unidad adinistrativa
		//	    		   ai_sueintvac // sueldo integral de vacaciones
		//	    		   ad_fecdisvac // fecha de disfrute de las vacaciones
		//	    		   ad_fecreivac // fecha de reintegro de las vacaciones
		//	    		   ai_diavac // días hábiles de vacaciones
		//	    		   as_codvac // código de vacaciones
		//	    		   as_descar // descripción del cargo
		//	    		   ai_sueintdia // Sueldo integral diario
		//                 as_sueint // denominación de sueldo integral
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		if ($as_sueint=="")
		{
			$titulo1="Sueldo Integral de Vacaciones";
			$titulo2="Sueldo Diario Integral";
		}
		else
		{
			$titulo1=$as_sueint." de Vacaciones";
			$titulo2=$as_sueint." Diario";
		}
		
		$la_data[1]=array('titulo'=>'<b>Identificación del Empleado</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		$la_data[1]=array('nombre'=>'<b>Apellidos y Nombres</b>','cedula'=>'<b>Cédula de Identidad</b>','unidad'=>'<b>Unidad Administrativa</b>','fechaingreso'=>'<b>Fecha de Ingreso</b>');
		$la_data[2]=array('nombre'=>$as_nomper,'cedula'=>$as_cedper,'unidad'=>$as_desuniadm,'fechaingreso'=>$ad_fecingper);
		$la_columnas=array('nombre'=>'','cedula'=>'','unidad'=>'','fechaingreso'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 		 	   'unidad'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 		 	   'fechaingreso'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		$la_data[1]=array('sueldovac'=>'<b>'.$titulo1.'</b>','sueldodia'=>'<b>'.$titulo2.'</b>','cargo'=>'<b>Cargo</b>');
		$la_data[2]=array('sueldovac'=>$ai_sueintvac,'sueldodia'=>$ai_sueintdia,'cargo'=>$as_descar);
		$la_columnas=array('sueldovac'=>'','sueldodia'=>'','cargo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('sueldovac'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'sueldodia'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 		 	   'cargo'=>array('justification'=>'center','width'=>240))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		$la_data[1]=array('fechasalida'=>'<b>Fecha de Salida</b>','fechaincorporacion'=>'<b>Fecha de Incorporación</b>',
						  'anoservicio'=>'<b>Años de Servicio</b>','diashabiles'=>'<b>Días Hábiles</b>');
		$la_data[2]=array('fechasalida'=>$ad_fecdisvac,'fechaincorporacion'=>$ad_fecreivac,'anoservicio'=>$as_codvac,'diashabiles'=>$ai_diavac);
		$la_columnas=array('fechasalida'=>'','fechaincorporacion'=>'','anoservicio'=>'','diashabiles'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('fechasalida'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
						 			   'fechaincorporacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'anoservicio'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 		 	   'diashabiles'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_descripcion,$la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: as_descripcion // Descripción si es un reporte de salida ó de reintegro
		//	    		   la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_titulo[1]=array('titulo'=>'');
		$la_titulo[2]=array('titulo'=>'');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		$la_titulo[1]=array('titulo'=>'<b>'.$as_descripcion.'</b>',
						    'asignacion'=>'<b>ASIGNACIÓN</b>',
						    'deduccion'=>'<b>DEDUCCIÓN</b>',
						    'aporte'=>'<b>APORTE PATRONAL</b>');
		$la_columnas=array('titulo'=>'',
						   'asignacion'=>'',
						   'deduccion'=>'',
						   'aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'aporte'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		$la_columnas=array('codigo'=>'',
						   'nombre'=>'',
						   'asignacion'=>'',
						   'deduccion'=>'',
						   'aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>140), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_trabajo($ls_obsvac,$la_datatra,$li_totdialab,$li_totmeslab,$li_totanolab,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_trabajo
		//		   Access: private 
		//	    Arguments: as_descripcion // Descripción si es un reporte de salida ó de reintegro
		//	    		   la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_titulo[1]=array('titulo'=>'');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		
		$la_titulo[1]=array('titulo'=>'Antigüedad en la Administracion Publica Nacional (debidamente certificado con el FP-023 y otras constancias)');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		
		$ls_titulo="Periodo Laborado";
		$la_data1[1]=array('name'=>$ls_titulo);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>9,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>396, 						 					
						 'width'=>170,      // Ancho de la tabla						 
						 'maxWidth'=>170,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>170)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$ls_titulo="";
		$la_data1[1]=array('name'=>"Desde",'name2'=>"Hasta",'name3'=>"Antigüedad");
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>9,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>446, 						 					
						 'width'=>270,      // Ancho de la tabla						 
						 'maxWidth'=>270,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>85),
						               'name2'=>array('justification'=>'center','width'=>85),
						               'name3'=>array('justification'=>'center','width'=>100)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		
		$la_columnas=array('emptraant'=>'Organismo',
						   'ultcartraant'=>'Cargo',
						   'dfecingtraant'=>'Dia',
						   'mfecingtraant'=>'Mes',
						   'yfecingtraant'=>'Año',
						   'dfecrettraant'=>'Dia',
						   'mfecrettraant'=>'Mes',
						   'yfecrettraant'=>'Año',
						   'dialab'=>'Dias',
						   'meslab'=>'Meses',
						   'anolab'=>'Años');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('emptraant'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'ultcartraant'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'dfecingtraant'=>array('justification'=>'center','width'=>25), // Justificación y ancho de la columna
						 			   'mfecingtraant'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'yfecingtraant'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'dfecrettraant'=>array('justification'=>'center','width'=>25), // Justificación y ancho de la columna
						 			   'mfecrettraant'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'yfecrettraant'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'dialab'=>array('justification'=>'center','width'=>30), // Justificación y ancho de la columna
						 			   'meslab'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna
						 			   'anolab'=>array('justification'=>'center','width'=>35))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatra,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		$ls_titulo="";
		$la_data1[1]=array('name'=>"<b>TOTAL ANTIÜEDAD APN</b>",'name1'=>$li_totdialab,'name2'=>$li_totmeslab,'name3'=>$li_totanolab);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>450),
						               'name1'=>array('justification'=>'center','width'=>30),
						               'name2'=>array('justification'=>'center','width'=>35),
						               'name3'=>array('justification'=>'center','width'=>35)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_titulo[1]=array('titulo'=>'Observaciones: ');
		$la_titulo[2]=array('titulo'=>$ls_obsvac);
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($ai_totasig,$ai_totdedu,$ai_totapor,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
		//		   Access: private 
		//	    Arguments: ai_totasig // Total Asignación
		//	   			   ai_totdedu // Total Deducción
		//	   			   ai_totapor // Total Aporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		$la_data[1]=array('total'=>'<b>Total '.$ls_bolivares.'</b>','asignacion'=>$ai_totasig,'deduccion'=>$ai_totdedu,'aporte'=>$ai_totapor);
		$la_columna=array('total'=>'','asignacion'=>'','deduccion'=>'','aporte'=>'');
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
						 'cols'=>array('total'=>array('justification'=>'right','width'=>200), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_total
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($ls_nombre,$as_codusu)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
		//		   Access: private 
		//	    Arguments: ai_totasig // Total Asignación
		//	   			   ai_totdedu // Total Deducción
		//	   			   ai_totapor // Total Aporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_report;
		global $ls_bolivares;
		$ls_nomusu=$io_report->uf_select_usuario($as_codusu);
		$la_titulo[1]=array('titulo'=>'');
		$la_titulo[2]=array('titulo'=>'4.- FIRMAS: ');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		unset($la_config);

		$la_data1[1]=array('name'=>"Unidad de Trabajo",'name1'=>"Coordinacion de Recursos Humanos");
		$la_data1[2]=array('name'=>"Firmas",'name1'=>"Firmas");
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>350),
						               'name1'=>array('justification'=>'center','width'=>200)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$ls_titulo="";
		$la_data1[1]=array('name'=>"Director(a)",'name1'=>"Coordinador(a)",'name2'=>"Trabajador(a)",'name3'=>"Elaborado por:",'name4'=>"Revisado por:");
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>116),
						               'name1'=>array('justification'=>'center','width'=>116),
						               'name2'=>array('justification'=>'center','width'=>118),
						               'name3'=>array('justification'=>'center','width'=>100),
						               'name4'=>array('justification'=>'center','width'=>100)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_data1[2]=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_data1[3]=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>116),
						               'name1'=>array('justification'=>'center','width'=>116),
						               'name2'=>array('justification'=>'center','width'=>118),
						               'name3'=>array('justification'=>'center','width'=>100),
						               'name4'=>array('justification'=>'center','width'=>100)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('name'=>"Lcda. Elionora Marenco",'name1'=>"Lcdo. Jose La Rosa",'name2'=>$ls_nombre,'name3'=>$ls_nomusu,'name4'=>"Lcdo. Jose La Rosa");
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>116),
						               'name1'=>array('justification'=>'center','width'=>116),
						               'name2'=>array('justification'=>'center','width'=>118),
						               'name3'=>array('justification'=>'center','width'=>100),
						               'name4'=>array('justification'=>'center','width'=>100)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_titulo[1]=array('titulo'=>'Original: Coordinación de Recursos Humanod');
		$la_titulo[2]=array('titulo'=>'Copia: Trabajador(a)');
		$la_titulo[3]=array('titulo'=>'Copia: Supervisor Inmediato del Trabajador(a)');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columnas,'',$la_config);
		unset($la_titulo);
		unset($la_columnas);
		unset($la_config);

	}// end function uf_print_total
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
	$ls_titulo="<b>Liquidación de Vacaciones</b>";
	$ls_periodo="<b>Período Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper","");
	$ls_codvac=$io_fun_nomina->uf_obtenervalor_get("codvac","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_sueint=$io_fun_nomina->uf_obtenervalor_get("sueint","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_relacionvacacion_personal($ls_codper,$ls_codvac,$ls_conceptocero,$rs_data); // Cargar el DS con los datos de la cabecera del reporte
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
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.1,2,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la página
		//$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		while((!$rs_data->EOF)&&($lb_valido))
		{
			$ls_codper=$rs_data->fields["codper"];
			$ls_cedper=$rs_data->fields["cedper"];
			$ls_nombre=$rs_data->fields["nomper"]." ".$rs_data->fields["apeper"];
			$ls_nomper=$rs_data->fields["apeper"].", ".$rs_data->fields["nomper"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecingper"]);
			$ls_desuniadm=$rs_data->fields["desuniadm"];
			$li_sueintvac=$io_fun_nomina->uf_formatonumerico($rs_data->fields["sueintvac"]);
			$li_sueintdia=($rs_data->fields["sueintvac"]/30);
			$li_sueintdia=$io_fun_nomina->uf_formatonumerico($li_sueintdia);
			$ld_fecdisvac=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecdisvac"]);
			$ld_fecreivac=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecreivac"]);
			$li_diavac=$rs_data->fields["diavac"];
			$ls_codvac=$rs_data->fields["codvac"];
			$ls_descar=$rs_data->fields["descar"];
			$ls_obsvac=$rs_data->fields["obsvac"];
			$ls_codusu=$rs_data->fields["codusu"];
			uf_print_cabecera($ls_cedper,$ls_nomper,$ld_fecingper,$ls_desuniadm,$li_sueintvac,$ld_fecdisvac,$ld_fecreivac,
							  $li_diavac,$ls_codvac,$ls_descar,$li_sueintdia,$ls_sueint,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_relacionvacacion_concepto($ls_codper,$ls_codvac,$ls_conceptocero,$ls_tituloconcepto); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totasig=0;
				$li_totdedu=0;
				$li_totapor=0;
				$li_totrow_det=$io_report->DS_detalle->getRowCount("codconc");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
					$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
					$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
					$li_asig=$io_fun_nomina->uf_formatonumerico(0);
					$li_dedu=$io_fun_nomina->uf_formatonumerico(0);
					$li_apor=$io_fun_nomina->uf_formatonumerico(0);
					$ls_persalvac=$io_report->DS_detalle->data["persalvac"][$li_s];
					$ls_peringvac=$io_report->DS_detalle->data["peringvac"][$li_s];
					$ls_descripcion="CONCEPTOS DE SALIDA DE VACACIONES";
					if($ls_peringvac==$_SESSION["la_nomina"]["peractnom"])
					{
						$ls_descripcion="CONCEPTOS DE REINTEGRO DE VACACIONES";
					}
					switch($ls_tipsal)
					{
						case "V1":
							$li_asig=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totasig=$li_totasig+$io_report->DS_detalle->data["valsal"][$li_s];
							break;
							
						case "W1":
							$li_asig=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totasig=$li_totasig+$io_report->DS_detalle->data["valsal"][$li_s];
							break;
							
						case "V2":
							$li_dedu=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totdedu=$li_totdedu+$io_report->DS_detalle->data["valsal"][$li_s];
							break;

						case "W2":
							$li_dedu=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totdedu=$li_totdedu+$io_report->DS_detalle->data["valsal"][$li_s];
							break;
							
						case "V3":
							$li_dedu=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totdedu=$li_totdedu+$io_report->DS_detalle->data["valsal"][$li_s];
							break;

						case "W3":
							$li_dedu=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totdedu=$li_totdedu+$io_report->DS_detalle->data["valsal"][$li_s];
							break;

						case "V4":
							$li_apor=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totapor=$li_totapor+$io_report->DS_detalle->data["valsal"][$li_s];
							break;
							
						case "W4":
							$li_apor=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$li_totapor=$li_totapor+$io_report->DS_detalle->data["valsal"][$li_s];
							break;
					}
					$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>$li_asig,
										  'deduccion'=>$li_dedu,'aporte'=>$li_apor);
				}
				$io_report->DS_detalle->resetds("codconc");
				uf_print_detalle($ls_descripcion,$la_data,$io_pdf); // Imprimimos el detalle 
				$li_totasig=$io_fun_nomina->uf_formatonumerico($li_totasig);
				$li_totdedu=$io_fun_nomina->uf_formatonumerico($li_totdedu);
				$li_totapor=$io_fun_nomina->uf_formatonumerico($li_totapor);
				uf_print_total($li_totasig,$li_totdedu,$li_totapor,$io_pdf); // Imprimimos el pie de la cabecera
			}
			$li_z=0;
			$la_datatra="";
			$ls_fecingtraantaux="";
			$li_totdialab=0;
			$li_totmeslab=0;
			$li_totanolab=0;
			$rs_datatraant=$io_report->uf_trabajo_anterior($ls_codper);
			while((!$rs_datatraant->EOF))
			{
				$ls_emptraant=$rs_datatraant->fields["emptraant"];
				if($ls_emptraant=="")
					$ls_emptraant=$_SESSION["la_empresa"]["nombre"];
				$ls_ultcartraant=$rs_datatraant->fields["ultcartraant"];
				$ls_fecingtraant=$rs_datatraant->fields["fecingtraant"];
				$ls_dfecingtraant=substr($ls_fecingtraant,8,2);
				$ls_mfecingtraant=substr($ls_fecingtraant,5,2);
				$ls_yfecingtraant=substr($ls_fecingtraant,0,4);
				
				$ls_fecrettraant=$rs_datatraant->fields["fecrettraant"];
				$ls_dfecrettraant=substr($ls_fecrettraant,8,2);
				$ls_mfecrettraant=substr($ls_fecrettraant,5,2);
				$ls_yfecrettraant=substr($ls_fecrettraant,0,4);
				$ls_anolab=$rs_datatraant->fields["anolab"];
				$ls_meslab=$rs_datatraant->fields["meslab"];
				$ls_dialab=$rs_datatraant->fields["dialab"];
				if($ls_fecingtraantaux!=$ls_fecingtraant)
				{
					if($ls_fecrettraant=="1900-01-01")
					{
						$ls_fecrettraant=date("Y-m-d");
						$arrResultado=uf_restar_fechas($ls_fecingtraant,$ls_fecrettraant);
						$ls_anolab=$arrResultado["anios"];
						$ls_meslab=$arrResultado["meses"];
						$ls_dialab=$arrResultado["dias"];
						$ls_dfecrettraant="--";
						$ls_mfecrettraant="--";
						$ls_yfecrettraant="----";
					}
					$li_totdialab=$li_totdialab+$ls_dialab;
					$li_totmeslab=$li_totmeslab+$ls_meslab;
					$li_totanolab=$li_totanolab+$ls_anolab;
					$li_z++;
					$la_datatra[$li_z]=array('emptraant'=>$ls_emptraant,'ultcartraant'=>$ls_ultcartraant,'dfecingtraant'=>$ls_dfecingtraant,'mfecingtraant'=>$ls_mfecingtraant,
											  'yfecingtraant'=>$ls_yfecingtraant,'dfecrettraant'=>$ls_dfecrettraant,'mfecrettraant'=>$ls_mfecrettraant,
											  'yfecrettraant'=>$ls_yfecrettraant,'anolab'=>$ls_anolab,'meslab'=>$ls_meslab,'dialab'=>$ls_dialab);
				}
				$ls_fecingtraantaux=$ls_fecingtraant;
				$rs_datatraant->MoveNext();
			}
			if($la_datatra!="")
			{
				uf_print_detalle_trabajo($ls_obsvac,$la_datatra,$li_totdialab,$li_totmeslab,$li_totanolab,$io_pdf); // Imprimimos el detalle 
			}
			$rs_data->MoveNext();
		}
		$io_report->DS->resetds("cedper");
		uf_print_firmas($ls_nombre,$ls_codusu);
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