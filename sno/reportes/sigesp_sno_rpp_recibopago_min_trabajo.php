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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Arreglo de las variables de seguridad
		//	    		   as_desnom // Arreglo de las variables de seguridad
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_recibopago.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hrecibopago.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina1($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina1
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addJpegFromFile('../../shared/imagebank/mintras.jpg',30,740,550,40); // Agregar Logo

		/*$li_tm=$io_pdf->getTextWidth(9,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,9,'<b><i>'.$as_desnom.'</i></b>'); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(9,$as_periodo);
		$tm=306-($li_tm/2);*/
		$io_pdf->addText(230,650,8,'<b><i>'.$as_periodo.'</i></b>'); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina1
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera1($as_codper,$as_cedper,$as_nomper,$as_descar,$as_codcueban,$as_desuniadm,$ad_fecingper,$as_codcueban,$ls_tipper,$ls_codnom,$ls_estatus,
							    $ls_cargo,$li_total,$io_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera1
		//		   Access: private 
		//	    Arguments: as_cedper // C�dula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripci�n del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: funci�n que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$io_pdf->ezSety(730);
		$la_data[1]=array('nombre'=>'<b><i>Apellidos y Nombres: </b></i>'.$as_nomper, 
						  'cedula'=>'<b><i>C�dula de identidad: </b></i>'.$as_cedper);
		$la_columna=array('nombre'=>'',
						  'cedula'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap' => 1.2 ,
						 'cols'=>array('nombre'=>array('justification'=>'left','width'=>395), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'left','width'=>155))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('tipo_per'=>'<b><i>Tipo de Personal: </b></i>'.$ls_tipper,
		 				  'codigo_nom'=>'<b><i>C�digo de N�mina: </b></i>'.$ls_codnom);
		$la_columna=array('tipo_per'=>'','codigo_nom'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap' => 1.2 ,
						 'cols'=>array('tipo_per'=>array('justification'=>'left','width'=>395), // Justificaci�n y ancho de la columna
						 			   'codigo_nom'=>array('justification'=>'left','width'=>155))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('estatus'=>'<b><i>Estatus Actual: </b></i>'.$ls_estatus,
		 				  'estatus2'=>'</b></i>');
		$la_columna=array('estatus'=>'','estatus2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap' => 1.2 ,
						 'cols'=>array('estatus'=>array('justification'=>'left','width'=>395), // Justificaci�n y ancho de la columna
						 			   'estatus2'=>array('justification'=>'left','width'=>155))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('cargo'=>'<b><i>Cargo: </b></i>'.$ls_cargo,
		 				  'cta'=>'<b><i>Cta. N�mina: </b></i>'.$as_codcueban);
		$la_columna=array('cargo'=>'','cta'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap' => 1.2 ,
						 'cols'=>array('cargo'=>array('justification'=>'left','width'=>395), // Justificaci�n y ancho de la columna
						 			   'cta'=>array('justification'=>'left','width'=>155))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('unidad'=>'<b><i>Unidad Administrativa: </b></i>'.$as_desuniadm,
		 				  'sueldo'=>'<b><i>Sueldo/Salario B�sico: </b></i>'.$li_total);
		$la_columna=array('unidad'=>'','sueldo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'rowGap' => 1.2 ,
						 'cols'=>array('unidad'=>array('justification'=>'left','width'=>395), // Justificaci�n y ancho de la columna
						 			   'sueldo'=>array('justification'=>'left','width'=>155))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->addText(65,630,8,'=================================================================================================='); // Agregar el t�tulo
		$io_pdf->ezSety(630);
		$la_data[1]=array('denominacion'=>'<b>CONCEPTOS SALARIALES</b>',
						  'asignacion'=>'<b>ASIGNACI�N</b>',
						  'deduccion'=>'<b>DEDUCCI�N</b>');
		$la_columna=array('denominacion'=>'<b></b>',
						  'asignacion'=>'<b></b>',
						  'deduccion'=>'<b></b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>310), // Justificaci�n y ancho de la columna
									   'asignacion'=>array('justification'=>'right','width'=>70),
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		$io_pdf->addText(65,610,8,'=================================================================================================='); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSety(605);
		$la_columna=array('codigo'=>'<b>C�DIGO</b>', 
						  'denominacion'=>'<b>DENOMINACI�N</b>', 
						  'asignacion'=>'<b>ASIGNACION</b>',
						  'deduccion'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>65), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>245), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera1($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera1
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_codcueban // Codigo cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		//$io_pdf->addText(350,480,'10','________________________________________');
		//$io_pdf->ezSety(480);
		$io_pdf->ezSetDy(-11);
		$la_data=array(array('codigo'=>'-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'));
		$la_columna=array('codigo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>300, // Mostrar L�neas
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>460))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data=array(array('codigo'=>'', 'denominacion'=>'<b>TOTALES</b>', 'asignacion'=>'<b>'.$ai_toting.'</b>','deduccion'=>'<b>'.$ai_totded.'</b>'));
		$la_columna=array('codigo'=>'<b>C�DIGO</b>', 
						  'denominacion'=>'<b>DENOMINACI�N</b>', 
						  'asignacion'=>'<b>ASIGNACION</b>',
						  'deduccion'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>65), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'right','width'=>245), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
        unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data=array(array('codigo'=>'------------------------------------------------------------------------------------------------'));
		$la_columna=array('codigo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>298, // Mostrar L�neas
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'right','width'=>460))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('neto'=>'<b>NETO</b>                                                                '.'<b>'.$ai_totnet.'</b>'));
		$la_columna=array('neto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>450, // Orientaci�n de la tabla
						 'cols'=>array('neto'=>array('justification'=>'left','width'=>220))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle2($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-11);
		$la_datat[1]=array('title'=>'<b>================================================================================================================</b>');
		$la_columna=array('title'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>295,
						 'cols'=>array('title'=>array('justification'=>'left','width'=>470))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		unset($la_datat);
		unset($la_columna);
		unset($la_config);
		
		$la_datat[1]=array('denominacion'=>'<b>CONCEPTOS NO SALARIALES</b>',
						  'asignacion'=>'<b>ASIGNACI�N</b>',
						  'deduccion'=>'<b>DEDUCCI�N</b>');
		$la_columna=array('denominacion'=>'<b></b>',
						  'asignacion'=>'<b></b>',
						  'deduccion'=>'<b></b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>310), // Justificaci�n y ancho de la columna
									   'asignacion'=>array('justification'=>'right','width'=>70),
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		unset($la_datat);
		unset($la_columna);
		unset($la_config);
		
		$la_datat[1]=array('title'=>'<b>================================================================================================================</b>');
		$la_columna=array('title'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>295,
						 'cols'=>array('title'=>array('justification'=>'left','width'=>470))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		unset($la_datat);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('codigo'=>'<b>C�DIGO</b>', 
						  'denominacion'=>'<b>DENOMINACI�N</b>', 
						  'asignacion'=>'<b>ASIGNACION</b>',
						  'deduccion'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>65), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>245), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera2($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera1
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_codcueban // Codigo cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		//$io_pdf->addText(350,480,'10','________________________________________');
		//$io_pdf->ezSety(480);
		$io_pdf->ezSetDy(-11);
		$la_data=array(array('codigo'=>'-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'));
		$la_columna=array('codigo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>300, // Mostrar L�neas
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>460))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		
		/*$la_data=array(array('codigo'=>'', 'denominacion'=>'<b>TOTALES</b>', 'asignacion'=>'<b>'.$ai_toting.'</b>','deduccion'=>'<b>'.$ai_totded.'</b>'));
		$la_columna=array('codigo'=>'<b>C�DIGO</b>', 
						  'denominacion'=>'<b>DENOMINACI�N</b>', 
						  'asignacion'=>'<b>ASIGNACION</b>',
						  'deduccion'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>65), // Justificaci�n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'right','width'=>245), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
        unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data=array(array('codigo'=>'------------------------------------------------------------------------------------------------'));
		$la_columna=array('codigo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xPos'=>298, // Mostrar L�neas
						 'rowGap' => 0 ,
						 'cols'=>array('codigo'=>array('justification'=>'right','width'=>460))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);*/
		$la_data=array(array('neto'=>'<b>NETO</b>                                                                '.'<b>'.$ai_totnet.'</b>'));
		$la_columna=array('neto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>450, // Orientaci�n de la tabla
						 'cols'=>array('neto'=>array('justification'=>'left','width'=>220))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		
		
		$io_pdf->addText(420,200,7,"<i>Fecha de Impresion   </i>".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares ="Bs.";
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
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$li_adelanto=$_SESSION["la_nomina"]["adenom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>RECIBO DE PAGO</b>";
	$ls_periodo="QUINCENA DEL ".$ld_fecdesper." AL ".$ld_fechasper."";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","-");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_codubifis=$io_fun_nomina->uf_obtenervalor_get("codubifis","");
	$ls_codpai=$io_fun_nomina->uf_obtenervalor_get("codpai","");
	$ls_codest=$io_fun_nomina->uf_obtenervalor_get("codest","");
	$ls_codmun=$io_fun_nomina->uf_obtenervalor_get("codmun","");
	$ls_codpar=$io_fun_nomina->uf_obtenervalor_get("codpar","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_recibopago_personal($ls_codperdes,$ls_codperhas,$ls_coduniadmdes,$ls_coduniadmhas,$ls_conceptocero,$ls_conceptop2,
													  $ls_conceptoreporte,$ls_codubifis,$ls_codpai,$ls_codest,$ls_codmun,$ls_codpar,
													  $ls_subnomdes,$ls_subnomhas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false) || ($io_report->rs_data->RecordCount()==0)) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(2.9,1,1,2); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina1($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$li_totrow=$io_report->rs_data->RecordCount();
		$li_reg=1;
		$li_i=1;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$li_toting=0;
			$li_totded=0;
			$li_toting_n=0;
			$li_totded_n=0;			
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["nomper"].", ".$io_report->rs_data->fields["apeper"];
			$ls_descar=$io_report->rs_data->fields["descar"];
			$ls_codcueban=$io_report->rs_data->fields["codcueban"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$li_total=$io_report->rs_data->fields["total"];
			$li_total=$io_fun_nomina->uf_formatonumerico(abs($li_total));
			$ls_tipper=$io_report->rs_data->fields["destipper"];
			$ls_cargo=$io_report->rs_data->fields["descasicar"];
			$ls_staper=$io_report->rs_data->fields["staper"];
			if ($ls_staper=='0')
			{
				$ls_estatus='N/A';
			}
			if ($ls_staper=='1')
			{
				$ls_estatus='ACTIVO';
			}
			if ($ls_staper=='2')
			{
				$ls_estatus='VACACIONES';
			}
			if ($ls_staper=='3')
			{
				$ls_estatus='EGRESADO';
			}
			if ($ls_staper=='4')
			{
				$ls_estatus='SUSPENDIDO';
			}
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			uf_print_cabecera1($ls_codper,$ls_cedper,$ls_nomper,$ls_descar,$ls_codcueban,$ls_desuniadm,$ld_fecingper,
							   $ls_codcueban,$ls_tipper,$ls_codnom,$ls_estatus,$ls_cargo,$li_total,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro

			$lb_valido=$io_report->uf_recibopago_conceptopersonal($ls_codper,$ls_conceptocero,$ls_conceptop2,
																  $ls_conceptoreporte,$ls_tituloconcepto,$ls_quincena); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->rs_data_detalle->RecordCount();
				$li_asig=0;
				$li_asig_n=0;
				$li_dedu=0;
				$li_dedu_n=0;
				$li_apor=0;
				$li_apor_n=0;
				$li_reporte=0;
				$li_reporte_n=0;
				if($li_adelanto==1)// Utiliza el adelanto de quincena
				{
					switch($ls_quincena)
					{
						case "1": // primera quincena;
							$li_asig=$li_asig+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_toting=$li_toting+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "2": // segunda quincena;
							while(!$io_report->rs_data_detalle->EOF)
							{
								$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
								$ls_persalnor=$io_report->rs_data_detalle->fields["persalnor"];
								if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&(($ls_persalnor!=1))) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&(($ls_persalnor==1))) // Buscamos las asignaciones
								{
									$li_asig_n=$li_asig_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_toting_n=$li_toting_n+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_a_n[$li_asig_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") ||
								   ($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3"))&&($ls_persalnor!=1) ) // Buscamos las deducciones
								{
									$li_dedu=$li_dedu+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") ||
								   ($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3"))&&($ls_persalnor==1) ) // Buscamos las deducciones
								{
									$li_dedu_n=$li_dedu_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_totded_n=$li_totded_n+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_d_n[$li_dedu_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor!=1) ) // Buscamos los aportes
								{
									$li_apor=$li_apor+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_p[$li_apor]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor==1) ) // Buscamos los aportes
								{
									$li_apor_n=$li_apor_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_p_n[$li_apor_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if(($ls_tipsal=="R")&&($ls_persalnor!=1)) // Buscamos los conceptos de tipo Reporte
								{
									$li_reporte=$li_reporte+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_r[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if(($ls_tipsal=="R")&&($ls_persalnor==1)) // Buscamos los conceptos de tipo Reporte
								{
									$li_reporte_n=$li_reporte_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_r_n[$li_asig_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								$io_report->rs_data_detalle->MoveNext();
							}
							$li_dedu=$li_dedu+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_totded=$li_totded+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "3": // Mes Completo;
							while(!$io_report->rs_data_detalle->EOF)
							{
								$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
								$ls_persalnor=$io_report->rs_data_detalle->fields["persalnor"];
								if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&($ls_persalnor!=1)) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&($ls_persalnor==1)) // Buscamos las asignaciones
								{
									$li_asig_n=$li_asig_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_toting_n=$li_toting_n+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_a_n[$li_asig_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") || 
								($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3"))&&($ls_persalnor!=1) ) // Buscamos las deducciones
								{
									$li_dedu=$li_dedu+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") || 
								($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3"))&&($ls_persalnor==1) ) // Buscamos las deducciones
								{
									$li_dedu_n=$li_dedu_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_totded_n=$li_totded_n+abs($io_report->rs_data_detalle->fields["valsal"]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_d_n[$li_dedu_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor!=1) ) // Buscamos los aportes
								{
									$li_apor=$li_apor+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_p[$li_apor]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor==1) ) // Buscamos los aportes
								{
									$li_apor_n=$li_apor_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_p_n[$li_apor_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
						   		if(($ls_tipsal=="R")&&($ls_persalnor!=1))// Buscamos los conceptos tipo reporte
								{
									$li_reporte=$li_reporte+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_r[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								if(($ls_tipsal=="R")&&($ls_persalnor==1))// Buscamos los conceptos tipo reporte
								{
									$li_reporte_n=$li_reporte_n+1;
									$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
									$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
									$la_data_r_n[$li_asig_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								$io_report->rs_data_detalle->MoveNext();
							}
							break;
					}
				}
				else// No utiliza adelanto de quincena
				{
					while(!$io_report->rs_data_detalle->EOF)
					{
						$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]); 
						$ls_persalnor=$io_report->rs_data_detalle->fields["persalnor"];
						if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&($ls_persalnor!=1)) // Buscamos las asignaciones
						{
							$li_asig=$li_asig+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
							$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if((($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2"))&&($ls_persalnor==1)) // Buscamos las asignaciones
						{
							$li_asig_n=$li_asig_n+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
							$li_toting_n=$li_toting_n+abs($io_report->rs_data_detalle->fields["valsal"]);
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_a_n[$li_asig_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") ||
						   ($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3")) && ($ls_persalnor!=1) ) // Buscamos las deducciones
						{
							$li_dedu=$li_dedu+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
							$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if((($ls_tipsal=="D") || ($ls_tipsal=="V2") || ($ls_tipsal=="W2") ||
						   ($ls_tipsal=="P1") || ($ls_tipsal=="V3") || ($ls_tipsal=="W3")) && ($ls_persalnor==1) ) // Buscamos las deducciones
						{
							$li_dedu_n=$li_dedu_n+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
							$li_totded_n=$li_totded_n+abs($io_report->rs_data_detalle->fields["valsal"]);
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_d_n[$li_dedu_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor!=1) ) // Buscamos los aportes
						{
							$li_apor=$li_apor+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];									
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_p[$li_apor]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if((($ls_tipsal=="P2") || ($ls_tipsal=="V4") || ($ls_tipsal=="W4"))&&($ls_persalnor==1) ) // Buscamos los aportes
						{
							$li_apor_n=$li_apor_n+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];									
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_p_n[$li_apor_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if(($ls_tipsal=="R")&&($ls_persalnor!=1))// Buscamos los conceptos de tipo reporte
						{ 	
							$li_reporte=$li_reporte+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];									
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_r[$li_reporte]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						if(($ls_tipsal=="R")&&($ls_persalnor==1))// Buscamos los conceptos de tipo reporte
						{ 	
							$li_reporte_n=$li_reporte_n+1;
							$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
							$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];									
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
							$la_data_r_n[$li_reporte_n]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						$io_report->rs_data_detalle->MoveNext();
					}
				}
				$la_data=array();
				$la_data_n=array();
				$li_count=0;
				for($li_s=1;$li_s<=$li_asig;$li_s++) 
				{
					$li_count++;
					$la_data[$li_count]=array('order'=>$la_data_a[$li_s]["codigo"],
											  'codigo'=>$la_data_a[$li_s]["codigo"], 
									          'denominacion'=>$la_data_a[$li_s]["denominacion"], 
									          'asignacion'=>$la_data_a[$li_s]["valor"],
									          'deduccion'=>'');
				} 
				for($li_s=1;$li_s<=$li_dedu;$li_s++) 
				{
					$li_count++;
					$la_data[$li_count]=array('order'=>$la_data_d[$li_s]["codigo"]."0",
											  'codigo'=>$la_data_d[$li_s]["codigo"], 
									          'denominacion'=>$la_data_d[$li_s]["denominacion"], 
									          'asignacion'=>'',
									          'deduccion'=>$la_data_d[$li_s]["valor"]);
				}
				$li_count2=0;
				for($li_n=1;$li_n<=$li_asig_n;$li_n++) 
				{
					$li_count2++;
					$la_data_n[$li_count2]=array('order'=>$la_data_a_n[$li_n]["codigo"],
											  'codigo'=>$la_data_a_n[$li_n]["codigo"], 
									          'denominacion'=>$la_data_a_n[$li_n]["denominacion"], 
									          'asignacion'=>$la_data_a_n[$li_n]["valor"],
									          'deduccion'=>'');
				} 
				for($li_n=1;$li_n<=$li_dedu_n;$li_n++) 
				{
					$li_count2++;
					$la_data_n[$li_count2]=array('order'=>$la_data_d_n[$li_n]["codigo"]."0",
											  'codigo'=>$la_data_d_n[$li_n]["codigo"], 
									          'denominacion'=>$la_data_d_n[$li_n]["denominacion"], 
									          'asignacion'=>'',
									          'deduccion'=>$la_data_d_n[$li_n]["valor"]);
				}
				
				sort($la_data);
				sort($la_data_n);
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				$li_totnet=$li_toting-$li_totded;
				$li_toting=$io_fun_nomina->uf_formatonumerico($li_toting);
				$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
				$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);
				uf_print_pie_cabecera1($li_toting,$li_totded,$li_totnet,$ls_codcueban,$io_pdf); // Imprimimos pie de la cabecera
				
				uf_print_detalle2($la_data_n,$io_pdf); // Imprimimos el detalle 
				$li_totnet_n=$li_toting_n-$li_totded_n;
				$li_toting_n=$io_fun_nomina->uf_formatonumerico($li_toting_n);
				$li_totded_n=$io_fun_nomina->uf_formatonumerico($li_totded_n);
				$li_totnet_n=$io_fun_nomina->uf_formatonumerico($li_totnet_n);
				uf_print_pie_cabecera2($li_toting_n,$li_totded_n,$li_totnet_n,$ls_codcueban,$io_pdf); // Imprimimos pie de la cabecera
				
				unset($la_data_a);
				unset($la_data_d);
				unset($la_data_p);
				unset($la_data);
				unset($la_data_a_n);
				unset($la_data_d_n);
				unset($la_data_p_n);
				unset($la_data_n);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
				if(($li_i<$li_totrow))
				{
					$io_pdf->ezNewPage(); // Insertar una nueva p�gina
				}
			}
			$li_i++;
			$io_report->rs_data->MoveNext();
		}
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
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