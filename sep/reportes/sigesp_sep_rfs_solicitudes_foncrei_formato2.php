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
	function uf_print_encabezado_pagina($as_titulo,$as_numsol,$ad_fecregsol,$as_dentipsol,$as_estsol,$io_pdf)
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
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(480,700,480,760); //vertical
		$io_pdf->line(480,730,590,730); //Horizontal
        $io_pdf->Rectangle(40,700,550,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],45,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		if ($as_estsol=="A")
		{
			$io_pdf->addText(470,765,10,"<b>ANULADO</b>"); // Agregar la Fecha
		}
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,725,11,$as_titulo); // Agregar el t?tulo
		$io_pdf->addText($tm,710,12,"<b> TIPO:  ".$as_dentipsol."</b>"); // Agregar el t?tulo
		$io_pdf->addText(543,765,7,"P?g."); // Agregar texto
		$io_pdf->addText(485,740,9,"No. ".$as_numsol); // Agregar el t?tulo
		$io_pdf->addText(485,710,9,"Fecha ".$ad_fecregsol); // Agregar el t?tulo
		
		// cuadro inferior
		$io_pdf->rectangle(40,50,550,130);           // Agregar rectangulo grande				
		$io_pdf->line(40,145,590,145);	//Horizontal 1
		$io_pdf->line(40,160,590,160);	//Horizontal 2
		$io_pdf->line(315,50,315,180);	//vertical	1
		$io_pdf->line(40,75,590,75);	//Horizontal 3
		$io_pdf->addText(120,167,7,"ELABORADO POR");      // Agregar texto
		$io_pdf->addText(120,147,7,"FECHA:         /        /       ");      // Agregar texto
		$io_pdf->addText(120,137,7,"NOMBRE:");      // Agregar texto
		$io_pdf->addText(120,78,7,"FIRMA:"); 
		$io_pdf->addText(120,65,7,"ANALISTA");      // Agregar texto
		
		$io_pdf->addText(420,167,7,"APROBADO POR");      // Agregar texto
		$io_pdf->addText(420,147,7,"FECHA:         /        /       ");      // Agregar texto
		$io_pdf->addText(420,137,7,"NOMBRE: ");      // Agregar texto
		$io_pdf->addText(420,78,7,"FIRMA:"); 
		$io_pdf->addText(380,65,7,"GERENCIA DE GESTION DE PROYECTOS - Y/O");      // Agregar texto
		$io_pdf->addText(380,55,7," GERENCIA DE FINANCIAMIENTO INTEGRAL");      // Agregar texto
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numsol,$as_coduniadm,$as_denuniadm,$as_denfuefin,$as_codigo,$as_nombre,$as_consol,$io_pdf)
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
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_deudor=$as_codigo.'  -  '.$as_nombre;
		$la_data[1]=array('titulo'=>'<b>Deudor</b>','contenido'=>$ls_deudor,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>100), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$ls_departamento=$as_coduniadm.'  -  '.$as_denuniadm;
		$la_data[1]=array('titulo'=>'<b>Departamento</b>','contenido'=>$ls_departamento);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>100), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('titulo'=>'<b>Concepto</b>','contenido'=>$as_consol);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>100), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>450))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-2);
	   //--------------------------------- TABLA CON EL TITULO DEL DETALLE   -----------------------------------------------------------
	    $la_datatitulos= array(array('numero'=>"<b>No.</b>",'codigo'=>"<b>CODIGO</b>",'denominacion'=>"<b>DENOMINACION</b>",
		                             'montot'=>"<b>MONTO</b>",'baseimp'=>"<b>SUBTOTAL</b>"));
		
		$la_columna=array('numero'=>'<b>No.</b>',
		                  'codigo'=>'<b>CODIGO</b>',
						  'denominacion'=>'<b>DENOMINACION</b>',
						  'montot'=>'<b>MONTO</b>',
						  'baseimp'=>'<b>SUBTOTAL</b>');
						  
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre l?neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>40), // Justificaci?n y ancho de la columna
						               'codigo'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>105), // Justificaci?n y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>105))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datatitulos,$la_columna,'',$la_config);
		unset($la_colunma);
		unset($la_config);
	   //------------------------------------------ TABLA CON EL  DETALLE   -----------------------------------------------------------
		$la_columna=array('numero'=>'',
		                  'codigo'=>'',
						  'denominacion'=>'',
						  'montot'=>'',
						  'baseimp'=>'');
						  
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>40), // Justificaci?n y ancho de la columna
						               'codigo'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>105), // Justificaci?n y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>105))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cargos($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cargos
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
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
		return $io_pdf;
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_cuentas($la_data,$ad_total,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_cuentas
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
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
		//--------------------------------- ESTRUCTURA PRESUPUESTARIA  -----------------------------------------------------------
	    $la_dataest= array(array('name'=>''));
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>9, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xPos'=>315, // Orientaci?n de la tabla
						 'width'=>550, // Ancho de la tabla	
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'maxWidth'=>550); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_dataest,$la_columna,'',$la_config);
		unset($la_dataest);
		unset($la_columna);
		unset($la_config);		
	   //--------------------------------- TABLA CON EL TITULO DEL DETALLE PRESUPUESTARIO  -----------------------------------------------------------
		$la_datatitulo= array(array('programatica'=>"<b>CODIGO ESTADISTICO</b>",
		                            'spg_cuenta'=>"<b>PARTIDA</b>",
		                            'denominacion'=>"<b>DENOMINACION</b>",
		                            'monto'=>"<b>MONTO</b>"));
		$la_columna=array('programatica'=>"<b>CODIGO ESTADISTICO</b>",
		                  'spg_cuenta'=>'<b>PARTIDA </b>',
		                  'denominacion'=>'<b>DENOMINACION</b>',
						  'monto'=>'<b>MONTO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre l?neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('programatica'=>array('justification'=>'left','width'=>150), // Justificaci?n y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'denominacion'=>array('justification'=>'left','width'=>200), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_datatitulo,$la_columna,'',$la_config);
        unset($la_datatitulo);
		unset($la_columna);
		unset($la_config);
	   //--------------------------------- TABLA CON EL DETALLE PRESUPUESTARIO  -----------------------------------------------------------
		$la_columna=array('programatica'=>"<b>CODIGO ESTADISTICO</b>",
		                  'spg_cuenta'=>'<b>PARTIDA </b>',
		                  'denominacion'=>'<b>DENOMINACION</b>',
						  'monto'=>'<b>MONTO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('programatica'=>array('justification'=>'left','width'=>150), // Justificaci?n y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
									   'denominacion'=>array('justification'=>'left','width'=>200), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($li_subtot,$li_totcar,$li_montot,$ls_monlet,$io_pdf)
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
		// Modificado por: Ing. Yozelin Barragan            
		// Fecha Creaci?n: 11/03/2007                     Fecha ?ltima Modificaci?n : 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tipoformato;
		if($ls_tipoformato==1)
		{
		   $ls_titsub="Bs.F.";
		}
		else
		{
		   $ls_titsub="Bs.";
		}	
		$la_data[1]=array('titulo'=>'<b>Total  '.$ls_titsub.'</b>','contenido'=>$li_montot,);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>450), // Justificaci?n y ancho de la columna
						 			   'contenido'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
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
	 $ls_titulo='<b>SOLICITUD EJECUCION PRESUPUESTARIA  '.$ls_moneda.'</b>';
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
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
			$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci?n de los margenes en cent?metros
		    $io_pdf->ezStartPageNumbers(588,765,7,'','',1); // Insertar el n?mero de p?gina
			$li_totrow=$io_report->DS->getRowCount("numsol");
			$ld_montototal=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsol=$io_report->DS->data["numsol"][$li_i];
				$ls_dentipsol=$io_report->DS->data["dentipsol"][$li_i];
				$ls_coduniadm=$io_report->DS->data["coduniadm"][$li_i];
				$ls_denuniadm=$io_report->DS->data["denuniadm"][$li_i];
				$ls_denfuefin=$io_report->DS->data["denfuefin"][$li_i];
				$ls_codpro=$io_report->DS->data["cod_pro"][$li_i];
				$ls_cedbene=$io_report->DS->data["ced_bene"][$li_i];
				$ls_nombre=$io_report->DS->data["nombre"][$li_i];
				$ld_fecregsol=$io_report->DS->data["fecregsol"][$li_i];
				$ls_consol=$io_report->DS->data["consol"][$li_i];
				$li_monto=$io_report->DS->data["monto"][$li_i];
				$li_monbasimptot=$io_report->DS->data["monbasinm"][$li_i];
				$li_montotcar=$io_report->DS->data["montotcar"][$li_i];
				$ls_estsol=$io_report->DS->data["estsol"][$li_i];
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
				$io_pdf = uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecregsol,$ls_dentipsol,$ls_estsol,$io_pdf);
				$io_pdf = uf_print_cabecera($ls_numsol,$ls_coduniadm,$ls_denuniadm,$ls_denfuefin,$ls_codigo,$ls_nombre,$ls_consol,$io_pdf);
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
						$ld_montototal=$ld_montototal+$li_monart;
						$ls_denunimed="";
						if(($ls_tipo=="B")&&($ls_unidad=="M"))
						{
							$ls_codunimed="";
							$ls_denunimed="";
							$li_unidad=$io_report->uf_select_dt_unidad($ls_codigo);
							$li_basimp=$li_cosuni*($li_cantidad*$li_unidad);
							$ls_codunimed    = "";
                            $arrResultado=$io_report->uf_sep_select_unidad_medida($ls_codigo,$ls_codunimed);
							$ls_codunimed = $arrResultado['as_codunimed'];
							$lb_valido = $arrResultado['lb_valido'];
							if ($lb_valido)
							{
								$arrResultado= "";
								$arrResultado=$io_report->uf_sep_select_denominacion_unidad_medida($ls_codigo,$ls_codunimed,$ls_denunimed);
								$ls_unidad = $arrResultado['as_denunimed'];
								$lb_valido = $arrResultado['lb_valido'];
							}
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

						$la_data[$li_s]=array('numero'=>$li_s,'codigo'=>$ls_codigo,'denominacion'=>$ls_denominacion,
											  'montot'=>$li_monart,'baseimp'=>$li_basimp);
					}
					$io_pdf = uf_print_detalle($la_data,$io_pdf);
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
						//$io_pdf = uf_print_detalle_cargos($la_data,$io_pdf);
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
								$ls_estcla=$io_report->ds_cuentas->data["estcla"][$li_s];
								$ls_dencuenta="";
								$arrResultado = $io_report->uf_select_denominacionspg($ls_spgcuenta,$ls_dencuenta);	
								$ls_dencuenta = $arrResultado['as_denominacion'];
								$lb_valido1 = $arrResultado['lb_valido'];
								$ld_disponible =0;
								$arrResultado = $io_report->uf_select_disponible($ls_spgcuenta,$ls_codestpro1,$ls_codestpro2,
	                                                                          $ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,
																			  $ld_disponible);																			   	
								$ld_disponible = $arrResultado['ad_monto_disponible'];
								$lb_valido = $arrResultado['lb_valido'];
								$ld_disponible = number_format($ld_disponible,2,',','.');
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
								
								$la_data[$li_s]=array('programatica'=>$ls_codestpro,'spg_cuenta'=>$ls_spgcuenta,
								                      'denominacion'=>$ls_dencuenta,'monto'=>$li_montocta);
							}	
							$io_pdf = uf_print_detalle_cuentas($la_data,$ls_monto,$io_pdf);
							unset($la_data);
						}
					}
				}
			}
		}
		$io_pdf = uf_print_piecabecera($li_monbasimptot,$li_montotcar,$li_monto,$ls_monto,$io_pdf);
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
	}
?>