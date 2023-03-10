<?php
/***********************************************************************************
* @fecha de modificacion: 15/08/2022, para la version de php 8.1 
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
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_sep;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_sep->uf_load_seguridad_reporte("SEP","sigesp_sep_p_solicitud.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_numsol,$ad_fecregsol,$as_nomusu,$lb_nombreusu,$li_montof,$li_codtipsol,$as_estsol,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_numsol // numero de la solicitud
		//	    		   ad_fecregsol // fecha de registro de la solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$ls_titulo2="<b>INSTITUTO NACIONAL DE DEPORTES</b>";
		$ls_titulo3="<b> RIF:G-20000046-5</b>";
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->setLineStyle(1);
		$io_pdf->line(15,40,585,40);
		$io_pdf->line(480,700,480,785);
		$io_pdf->line(480,730,585,730);
        $io_pdf->Rectangle(15,700,570,85);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logoind.jpg',25,703,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if ($as_estsol=="A")
		{
			$io_pdf->addText(482,765,10,"<b>ANULADO</b>"); // Agregar la Fecha
		}
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$li_tm2=$io_pdf->getTextWidth(11,$ls_titulo2);
		$li_tm3=$io_pdf->getTextWidth(11,$ls_titulo3);
		$tm=300-($li_tm/2);
		$tm2=300-($li_tm2/2);
		$tm3=350-($li_tm3/2);
		$io_pdf->addText($tm,750,12,$as_titulo); // Agregar el t?tulo
		$io_pdf->addText($tm2,740,10,$ls_titulo2); // Agregar el t?tulo
		$io_pdf->addText($tm3,730,8,$ls_titulo3); // Agregar el t?tulo
		$io_pdf->addText(485,740,9,"No. ".$as_numsol); // Agregar el t?tulo
		$io_pdf->addText(485,710,9,"Fecha ".$ad_fecregsol); // Agregar el t?tulo
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		// cuadro inferior
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(15,60,570,70);
        $io_pdf->Rectangle(200,130,200,70); //RECTANGULO PARA PRESUPUESTO
		$io_pdf->line(15,73,585,73);
		$io_pdf->line(200,145,399,145); //linea inferior del rectangulo de validacion presupuestaria
		$io_pdf->line(15,117,585,117); 
		$io_pdf->line(200,185,399,185); //linea superior rectangulo de validacion presupuestaria
				
		if ($li_codtipsol!='08' && $li_montof > 450000)
		{
			$io_pdf->addText(450,75,7,"<b>PRESIDENTE IND</b>"); // Agregar la Fecha
		}
		if ($li_codtipsol!='08' && $li_montof <= 450000)
		{
			$io_pdf->addText(450,75,7,"<b>DIRECTOR GENERAL(E)</b>"); // Agregar la Fecha
		}
		$io_pdf->line(200,60,200,130);		
		$io_pdf->line(400,60,400,130);		
		$io_pdf->addText(78,122,7,"ELABORADO POR"); // Agregar el t?tulo del usuario que elaboro sep
		$io_pdf->addText(80,63,7,"FIRMA"); // Agregar el t?tulo para que firme el usuario
		$io_pdf->addText(60,75,7,$lb_nombreusu); // Agregar el nombre del usuario que elaboro sep
		$io_pdf->addText(202,122,7,"RESPONSABLE DE EJECUTAR PRESUPUESTO"); // Agregar el t?tulo
		$io_pdf->addText(265,63,7,"FIRMA / SELLO"); // Agregar el t?tulo
		$io_pdf->addText(465,122,7,"APROBADO POR"); // Agregar el t?tulo
		$io_pdf->addText(430,63,7,"FIRMA  AUT?GRAFA, SELLO, FECHA"); // Agregar el t?tulo
		$io_pdf->addText(250,135,7,"FIRMA, SELLO, FECHA"); // Agregar Presupuesto
		$io_pdf->addText(240,148,7,"D.G. PLANIFICACI?N Y PRESUPUESTO"); // Validar Presupuesto
		$io_pdf->addText(275,187,7,"VALIDADO"); // Agregar el Presupuesto
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numsol,$as_dentipsol,$as_denuniadm,$as_denfuefin,$as_codigo,$as_nombre,$as_consol,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numsol    // numero de la solicitud de ejecucion presupuestaria
		//	   			   as_dentipsol // Denominacion del tipo de solicitud
		//	   			   as_denuniadm // Denominacion de la Unidad Ejecutora solicitante
		//	   			   as_denfuefin // Denominacion de la fuente de financiamiento
		//	   			   as_codigo    // Codigo del Proveedor / Beneficiario
		//	   			   as_nombre    // Nombre del Proveedor / Beneficiario
		//	   			   as_consol    // Concepto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 17/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				
		//$la_data[1]=array('titulo'=>'<b> Tipo</b>','contenido'=>$as_dentipsol);
		if ($as_dentipsol==''){
		$la_data[1]=array('titulo'=>'<b> </b>','contenido'=>$as_dentipsol);
		}else {
		$la_data[1]=array('titulo'=>'<b> Tipo</b>','contenido'=>$as_dentipsol);
		}
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]=array('titulo'=>'<b> Unidad Ejecutora</b>','contenido'=>$as_denuniadm);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('titulo'=>'<b>Fuente Financiamiento</b>','contenido'=>$as_denfuefin,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('titulo'=>'<b>Proveedor / Beneficiario</b>','contenido'=>$as_nombre,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('titulo'=>'<b>Concepto</b>','contenido'=>$as_consol,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('titulo'=>'<b> Detalle de '.$as_dentipsol.'</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		return $io_pdf;
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$li_codtipsol,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-2);
		if ($li_codtipsol!='08')
		{
			$la_columnas=array('codigo'=>'<b>C?digo</b>',
							   'denominacion'=>'<b>Denominacion</b>',
							   'cantidad'=>'<b>Cant.</b>',
							   'unidad'=>'<b>Unidad</b>',
							   'cosuni'=>'<b>Costo</b>',
							   'baseimp'=>'<b>Sub-Total</b>',
							   'cargo'=>'<b>Cargo</b>',
							   'montot'=>'<b>Total</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>115), // Justificaci?n y ancho de la columna
										   'denominacion'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
										   'cantidad'=>array('justification'=>'left','width'=>40), // Justificaci?n y ancho de la columna
										   'unidad'=>array('justification'=>'center','width'=>45), // Justificaci?n y ancho de la columna
										   'cosuni'=>array('justification'=>'right','width'=>60), // Justificaci?n y ancho de la columna
										   'baseimp'=>array('justification'=>'right','width'=>65), // Justificaci?n y ancho de la columna
										   'cargo'=>array('justification'=>'right','width'=>60), // Justificaci?n y ancho de la columna
										   'montot'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		else
		{
			$la_columnas=array('codigo'=>'<b>C?digo</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   'unidad'=>'<b>Unidad</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>115), // Justificaci?n y ancho de la columna
										   'denominacion'=>array('justification'=>'left','width'=>335), // Justificaci?n y ancho de la columna
										   'cantidad'=>array('justification'=>'left','width'=>60), // Justificaci?n y ancho de la columna
										   'unidad'=>array('justification'=>'center','width'=>60))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		return $io_pdf;		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cargos($la_data,$li_codtipsol,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cargos
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		if ($li_codtipsol!='08')
		{
			$la_datatit[1]=array('titulo'=>'<b> Detalle de Cargos </b>');
			$la_columnas=array('titulo'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>2, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
			unset($la_datatit);
			unset($la_columnas);
			unset($la_config);
			$io_pdf->ezSetDy(-2);
			$la_columnas=array('codigo'=>'<b>C?digo</b>',
							   'dencar'=>'<b>Denominaci?n</b>',
							   'monbasimp'=>'<b>Base Imp.</b>',
							   'monimp'=>'<b>Cargo</b>',
							   'monto'=>'<b>Total</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>115), // Justificaci?n y ancho de la columna
										   'dencar'=>array('justification'=>'left','width'=>200), // Justificaci?n y ancho de la columna
										   'monbasimp'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
										   'monimp'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
										   'monto'=>array('justification'=>'right','width'=>95))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		else
		{
			$la_datatit[1]=array('titulo'=>'<b> Detalle de Cargos </b>');
			$la_columnas=array('titulo'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>2, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
			unset($la_datatit);
			unset($la_columnas);
			unset($la_config);
			$io_pdf->ezSetDy(-2);
			$la_columnas=array('codigo'=>'<b>C?digo</b>',
							   'dencar'=>'<b>Denominaci?n</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codigo'=>array('justification'=>'center','width'=>215), // Justificaci?n y ancho de la columna
										   'dencar'=>array('justification'=>'center','width'=>355))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		return $io_pdf;		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,$li_codtipsol,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		global $ls_estmodest;
		if($ls_estmodest==1)
		{
			$ls_titcuentas="Estructura Presupuestaria";
		}
		else
		{
			$ls_titcuentas="Estructura Programatica";
		}
		if ($li_codtipsol!='08')
		{
			$la_datatit[1]=array('titulo'=>'<b> Detalle de Presupuesto </b>');
			$la_columnas=array('titulo'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>2, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
			unset($la_datatit);
			unset($la_columnas);
			unset($la_config);
			$io_pdf->ezSetDy(-2);
			$la_columnas=array('codestpro'=>'<b>'.$ls_titcuentas.'</b>',
							   'cuenta'=>'<b>Cuenta</b>',
							   'monto'=>'<b>Total</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>270), // Justificaci?n y ancho de la columna
										   'cuenta'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
										   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		else
		{
			$la_datatit[1]=array('titulo'=>'<b> Detalle de Presupuesto </b>');
			$la_columnas=array('titulo'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>2, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
			unset($la_datatit);
			unset($la_columnas);
			unset($la_config);
			$io_pdf->ezSetDy(-2);
			$la_columnas=array('codestpro'=>'<b>'.$ls_titcuentas.'</b>',
							   'cuenta'=>'<b>Cuenta</b>');
			$la_config=array('showHeadings'=>1, // Mostrar encabezados
							 'fontSize' => 9, // Tama?o de Letras
							 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
							 'showLines'=>1, // Mostrar L?neas
							 'shaded'=>0, // Sombra entre l?neas
							 'width'=>540, // Ancho de la tabla
							 'maxWidth'=>540, // Ancho M?ximo de la tabla
							 'xOrientation'=>'center', // Orientaci?n de la tabla
							 'outerLineThickness'=>0.5,
							 'innerLineThickness' =>0.5,
							 'cols'=>array('codestpro'=>array('justification'=>'center','width'=>320), // Justificaci?n y ancho de la columna
										   'cuenta'=>array('justification'=>'center','width'=>250))); // Justificaci?n y ancho de la columna
			$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		}
		return $io_pdf;		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet,$li_codtipsol,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		    Acess: private 
		//	    Arguments: li_subtot // Subtotal del articulo
		//	    		   li_totcar  //  Total cargos
		//	    		   li_montot  // Monto total
		//	    		   ls_monlet   //Monto en letras
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime los totales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 17/03/07
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tipoformato;
		if($ls_tipoformato==1)
		{
		   $ls_titsub="Bs.F.";
		   $ls_titcar="Bs.F.";
		   $ls_tittot="Bs.F.";
		}
		else
		{
		   $ls_titsub="Bs.";
		   $ls_titcar="Bs.";
		   $ls_tittot="Bs.";
		}
		if ($li_codtipsol=='08')
		{
			$li_subtot='0,00';
			$li_totcar='0,00';
			$li_montot='0,00';
		}	
		$la_data[1]=array('titulo'=>'<b>Sub Total  '.$ls_titsub.'</b>','contenido'=>$li_subtot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Cargos  '.$ls_titcar.'</b>','contenido'=>$li_totcar,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Total  '.$ls_tittot.'</b>','contenido'=>$li_montot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$io_pdf->ezSetDy(-5);
		$la_data[1]=array('titulo'=>'<b> Son: '.$ls_monlet.'</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>570))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//    Description: funci?n que imprime las firmas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 13/08/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetY(300);
		$la_data[0]=array('firma1'=>'CONFORMADO POR:');
		$la_columna=array('firma1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>600, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'left','width'=>600))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);

/*
		$la_data[0]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[2]=array('firma1'=>'ANALISTA RESPONSABLE','firma2'=>'CONTROL PREVIO','firma3'=>'APROBADO');
		$la_data[3]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[4]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[5]=array('firma1'=>'____________________________','firma2'=>'____________________________','firma3'=>'____________________________');
		$la_data[6]=array('firma1'=>''.$_SESSION['la_apeusu'].','.$_SESSION['la_nomusu'].'','firma2'=>'Gilman Enrique Rivas','firma3'=>'Rafael Rivas Cabrera');
		$la_data[7]=array('firma1'=>'Oficina Recursos Humanos','firma2'=>'Apoyo T?cnico','firma3'=>'Gerente de Recursos Humanos');
		$la_data[8]=array('firma1'=>'C.I  N?. '.$_SESSION['la_cedusu'].'','firma2'=>'C.I. N?. 10.400.238','firma3'=>'C.I. N?. 3.254.864');
		$la_columna=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>600, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>200),
						 			   'firma3'=>array('justification'=>'center','width'=>200))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);*/
		return $io_pdf;		
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_sep.php");
	$io_fun_sep=new class_funciones_sep();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	if($ls_estmodest==1)
	{
		$ls_titcuentas="Estructura Presupuestaria";
	}
	else
	{
		$ls_titcuentas="Estructura Programatica";
	}
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	 $ls_numsol=$io_fun_sep->uf_obtenervalor_get("numsol","");
	 $ls_tipoformato=$io_fun_sep->uf_obtenervalor_get("tipoformato",0);
	//--------------------------------------------------------------------------------------------------------------------------------
	 global $ls_tipoformato;
	 if($ls_tipoformato==1)
	 {
		require_once("sigesp_sep_class_reportbsf.php");
		$io_report=new sigesp_sep_class_reportbsf();
	 }
	 else
	 {
		require_once("sigesp_sep_class_report.php");
		$io_report=new sigesp_sep_class_report();
  	 }	
	 //Instancio a la clase de conversi?n de numeros a letras.
	 include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	 $numalet= new class_numero_a_letra();
	 //imprime numero con los valore por defecto
	 //cambia a minusculas
	 $numalet->setMayusculas(1);
	 //cambia a femenino
	 $numalet->setGenero(1);
	 //cambia moneda
	 if($ls_tipoformato==1)
	 {
		 $numalet->setMoneda("Bolivares Fuerte");
	     $ls_moneda="EN Bs.F.";
	 }
	 else
	 {
		 $numalet->setMoneda("Bolivares");
	     $ls_moneda="EN Bs.";
  	 }	
	 //cambia prefijo
	 $numalet->setPrefijo("***");
	 //cambia sufijo
	 $numalet->setSufijo("***");
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	 $ls_titulo='<b>SOLICITUD DE EJECUCION PRESUPUESTARIA  '.$ls_moneda.'</b>';
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido = true;//$io_report->uf_validar_impresion($ls_numsol,$ls_datos_ctas); // valida si se puede imprimir el reporte, Creado por: Ramon Tineo
		if($lb_valido)
		{
			$lb_valido=$io_report->uf_select_solicitud($ls_numsol); // Cargar el DS con los datos del reporte
			if($lb_valido==false) // Existe alg?n error ? no hay registros
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
				$io_pdf->ezSetCmMargins(3.6,7,3,3); // Configuraci?n de los margenes en cent?metros
				$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el n?mero de p?gina
				$li_totrow=$io_report->DS->getRowCount("numsol");
				$ls_nomusu = "";
				$arrResultado = $io_report->uf_sep_select_usuario($_SESSION["la_logusr"],$ls_nomusu);
				$ls_nomusu = $arrResultado['as_nomusu'];
				$lb_valido = $arrResultado['lb_valido'];
				for($li_i=1;$li_i<=$li_totrow;$li_i++)
				{
					$ls_numsol=$io_report->DS->data["numsol"][$li_i];
					$ls_dentipsol=$io_report->DS->data["dentipsol"][$li_i];
					if($ls_dentipsol=='REQUISICION'){
						 $ls_dentipsol='Requisici?n';
					}
					$ls_denuniadm=$io_report->DS->data["denuniadm"][$li_i];
					$ls_denfuefin=$io_report->DS->data["denfuefin"][$li_i];
					$ls_codpro=$io_report->DS->data["cod_pro"][$li_i];
					$ls_cedbene=$io_report->DS->data["ced_bene"][$li_i];
					$ls_nombre=$io_report->DS->data["nombre"][$li_i];
					$ld_fecregsol=$io_report->DS->data["fecregsol"][$li_i];
					$ls_consol=$io_report->DS->data["consol"][$li_i];
					$li_monto=$io_report->DS->data["monto"][$li_i];
					$li_montof=$io_report->DS->data["monto"][$li_i];
					$li_monbasimptot=$io_report->DS->data["monbasinm"][$li_i];
					$li_montotcar=$io_report->DS->data["montotcar"][$li_i];
					$li_codtipsol=$io_report->DS->data["codtipsol"][$li_i];
					$lb_nombreusu=$io_report->DS->data["nomusu"][$li_i];
					$ls_estsol=$io_report->DS->data["estsol"][$li_i];
					if($li_codtipsol=='08'){
						 $ls_titulo='<b>R E Q U I S I C I O N</b>';
					}else{
						 $ls_titulo='<b>SOLICITUD DE EJECUCION PRESUPUESTARIA  '.$ls_moneda.'</b>';
					}
					$numalet->setNumero($li_monto);
					$ls_monto= $numalet->letra();
					$li_monto=number_format($li_monto,2,",",".");
					$li_monbasimptot=number_format($li_monbasimptot,2,",",".");
					$li_montotcar=number_format($li_montotcar,2,",",".");
					$ld_fecregsol=$io_funciones->uf_convertirfecmostrar($ld_fecregsol);
					if($ls_codpro!="----------")
					{
						$ls_codigo=$ls_codpro;
					}
					else
					{
						$ls_codigo=$ls_cedbene;
					}						
					$io_pdf = uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecregsol,$ls_nomusu,$lb_nombreusu,$li_montof,$li_codtipsol,$ls_estsol,$io_pdf);
					$io_pdf = uf_print_cabecera($ls_numsol,$ls_dentipsol,$ls_denuniadm,$ls_denfuefin,$ls_codigo,$ls_nombre,$ls_consol,$io_pdf);
					$io_report->ds_detalle->reset_ds();
					$lb_valido=$io_report->uf_select_dt_solicitud($ls_numsol); // Cargar el DS con los datos del reporte
					if($lb_valido)
					{
						$li_totrowdet=$io_report->ds_detalle->getRowCount("codigo");
						$la_data=Array();
						for($li_s=1;$li_s<=$li_totrowdet;$li_s++)
						{
							$ls_codigo=$io_report->ds_detalle->data["codigo"][$li_s];
							$ls_tipo=$io_report->ds_detalle->data["tipo"][$li_s];
							$ls_denominacion=$io_report->ds_detalle->data["denominacion"][$li_s];
							$ls_unidad=$io_report->ds_detalle->data["unidad"][$li_s];
							$li_cantidad=$io_report->ds_detalle->data["cantidad"][$li_s];
							$li_cosuni=$io_report->ds_detalle->data["monpre"][$li_s];
							$li_basimp=$li_cosuni*$li_cantidad;
							$li_monart=$io_report->ds_detalle->data["monto"][$li_s];
							
							if(($ls_tipo=="B")&&($ls_unidad=="M"))
							{
								$li_unidad=$io_report->uf_select_dt_unidad($ls_codigo);
								$li_basimp=$li_cosuni*($li_cantidad*$li_unidad);
							}
							$li_monart=number_format($li_monart,2,".","");
							$li_basimp=number_format($li_basimp,2,".","");
							$li_cargos=($li_monart-$li_basimp);
							if($ls_unidad=="M")
							{
								$ls_unidad="MAYOR";
							}
							else
							{
								$ls_unidad="DETAL";
							}
							
							$li_cosuni=number_format($li_cosuni,2,",",".");
							$li_basimp=number_format($li_basimp,2,",",".");
							$li_monart=number_format($li_monart,2,",",".");
							$li_cargos=number_format($li_cargos,2,",",".");
							$la_data[$li_s]=array('codigo'=>$ls_codigo,'denominacion'=>$ls_denominacion,'cantidad'=>$li_cantidad,
												  'unidad'=>$ls_unidad,'cosuni'=>$li_cosuni,'baseimp'=>$li_basimp,'cargo'=>$li_cargos,'montot'=>$li_monart);
						}
						$io_pdf = uf_print_detalle($la_data,$li_codtipsol,$io_pdf);
						unset($la_data);
						$lb_valido=$io_report->uf_select_dt_cargos($ls_numsol); // Cargar el DS con los datos del reporte
						if($lb_valido)
						{
							$li_totrowcargos=$io_report->ds_cargos->getRowCount("codigo");
							$la_data=Array();
							for($li_s=1;$li_s<=$li_totrowcargos;$li_s++)
							{
								$ls_codigo=$io_report->ds_cargos->data["codcar"][$li_s];
								$ls_dencar=$io_report->ds_cargos->data["dencar"][$li_s];
								$li_monbasimp=$io_report->ds_cargos->data["monbasimp"][$li_s];
								$li_monimp=$io_report->ds_cargos->data["monimp"][$li_s];
								$li_montocar=$io_report->ds_cargos->data["monto"][$li_s];
								$li_monbasimp=number_format($li_monbasimp,2,",",".");
								$li_monimp=number_format($li_monimp,2,",",".");
								$li_montocar=number_format($li_montocar,2,",",".");
								$la_data[$li_s]=array('codigo'=>$ls_codigo,'dencar'=>$ls_dencar,'monbasimp'=>$li_monbasimp,
													  'monimp'=>$li_monimp,'monto'=>$li_montocar);
							}	
							$io_pdf = uf_print_detalle_cargos($la_data,$li_codtipsol,$io_pdf);
							unset($la_data);
							$lb_valido=$io_report->uf_select_dt_spgcuentas($ls_numsol); // Cargar el DS con los datos del reporte
							if($lb_valido)
							{
								$li_totrowcuentas=$io_report->ds_cuentas->getRowCount("codestpro1");
								$la_data=Array();
								for($li_s=1;$li_s<=$li_totrowcuentas;$li_s++)
								{
									$ls_codestpro1=trim($io_report->ds_cuentas->data["codestpro1"][$li_s]);
									$ls_codestpro2=trim($io_report->ds_cuentas->data["codestpro2"][$li_s]);
									$ls_codestpro3=trim($io_report->ds_cuentas->data["codestpro3"][$li_s]);
									$ls_codestpro4=trim($io_report->ds_cuentas->data["codestpro4"][$li_s]);
									$ls_codestpro5=trim($io_report->ds_cuentas->data["codestpro5"][$li_s]);
									$ls_spgcuenta=$io_report->ds_cuentas->data["spg_cuenta"][$li_s];
									if($ls_estmodest==1)
									{

										$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3;
									}
									else
									{
										$ls_codestpro=$ls_codestpro1." - ".$ls_codestpro2." - ".$ls_codestpro3." - ".$ls_codestpro4." - ".$ls_codestpro5;
									}
									
									$li_montocta=$io_report->ds_cuentas->data["monto"][$li_s];
									$li_montocta=number_format($li_montocta,2,",",".");
									$la_data[$li_s]=array('codestpro'=>$ls_codestpro,'cuenta'=>$ls_spgcuenta,'monto'=>$li_montocta);
								}	
								$io_pdf = uf_print_detalle_cuentas($la_data,$li_codtipsol,$io_pdf);
								unset($la_data);
							}
						}
					}
				}
			}
			$io_pdf = uf_print_piecabecera($li_monbasimptot,$li_montotcar,$li_monto,$ls_monto,$li_codtipsol,$io_pdf);
			if($lb_valido) // Si no ocurrio ning?n error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else // Si hubo alg?n error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}else
		{   	
			  // Creada por: T.S.U Ramon Tineo 	Fecha: 21/03/2012
			$count = count((array)$ls_datos_ctas);
			for($i=0;$i<$count;$i++)
			{ // Imprime detalle de las cuentas con problemas de disponibilidad
				$datos_ctas = explode(',', $ls_datos_ctas[$i]);
				echo "PARTIDA PRESUPUESTARIA: ".$datos_ctas[0]."<br>";
				echo "DISPONIBILIDAD: ".$datos_ctas[1]."<br>";
				echo "MONTO: ".$datos_ctas[2]."<br>";
				echo "DIFERENCIA: ".($datos_ctas[3]*(-1))."<br>";
			}// Emite mensaje de disponibilidad
			print("<script language=JavaScript>");
			print(" alert('NO SE PERMITE IMPRIMIR EL REPORTE DEBIDO A QUE UNA DE SUS CUENTAS ASOCIADAS NO TIENE DISPONIBILIDAD PRESUPUESTARIA. SOLICITE MODIFICACION PRESUPUESTARIA.');");
			print(" close();");
			print("</script>");		
		}
	}
?>
