<?php
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
		print "</script>";		
	}
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');	
//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_codsolvia,$io_encabezado,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_codsolvia // C?digo de Solicitud de Viaticos
		//	    		   io_encabezado // Instancia del encabezado
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado,$io_pdf;
		$io_pdf->saveState();
		$io_pdf->line(35,40,570,40);
		$io_pdf->line(30,785,570,785);
		$io_pdf->line(30,700,570,700);
		$io_pdf->line(30,785,30,700);
		$io_pdf->line(150,785,150,700);
		$io_pdf->line(450,785,450,700);
		$io_pdf->line(570,785,570,700);
		$io_pdf->line(450,763.75,570,763.75);
		$io_pdf->line(450,742.5,570,742.5);
		$io_pdf->line(450,721.25,570,721.25);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(451,764.75,118,$io_pdf->getFontHeight(16.8));
        $io_pdf->filledRectangle(451,722.25,118,$io_pdf->getFontHeight(16.8));
        $io_pdf->setColor(0,0,0);		
		$io_pdf->addText(493,770,11,"FECHA"); // Agregar FECHA
		$io_pdf->addText(468,727,11,"N? DE CONTROL"); // Agregar NRO DE CONTROL
		$io_pdf->addText(487,706,11,$as_codsolvia); // Agregar NRO DE CONTROL
		$io_pdf->addText(215,740,11,"<b>SOLICITUD PAGO DE VIATICOS</b>"); // Agregar NRO DE CONTROL
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nomper,$as_desuniadm,$as_codcueban,$as_tipcuebanper,$as_desded,$as_destipper,
							  $as_codclavia,$ad_fecsalvia,$ad_fecregvia,$ai_numdiavia,$as_denmis,$ai_acompanante,$as_cargo,$ld_fecsolvia,$ls_tipovia,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: $as_cedper  // C?dula de personal
		//	    		   $as_nomper     // Nombre del personal
		//	    		   $as_desuniadm     // descripci?n de la unidad administrativa
		//	    		   $as_codcueban     // codigo cuenta de banco
		//	    		   $as_tipcuebanper     // tipo de cuenta de banco
		//	    		   $as_desded  // Descripci?n de la dedicaci?n
		//	    		   $as_destipper  // Descripci?n del tipo de personal
		//	    		   $as_codclavia  // Clasificaci?n del viaticos
		//	    		   $ad_fecsalvia  // fecha de salida del viatico
		//	    		   $ad_fecregvia  // fecha de regreso del viatico
		//	    		   $ai_numdiavia     // numero de dias
		//	    		   $as_denmis  // Denominaci?n de las misiones
		//	    		   io_pdf         // Instancia del objeto pdf
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 29/11/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		$ad_fecsalvia=$io_funciones->uf_convertirfecmostrar($ad_fecsalvia);
		$ad_fecregvia=$io_funciones->uf_convertirfecmostrar($ad_fecregvia);
		$ld_fecsolvia=$io_funciones->uf_convertirfecmostrar($ld_fecsolvia);
		$ai_numdiavia=number_format($ai_numdiavia,2,",",".");
		$io_pdf->addText(483,748,11,$ld_fecsolvia); // Agregar FECHA
        $io_pdf->setColor(0,0,0);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>DATOS DEL FUNCIONARIO ACTUALIZADO PARA LA MISION</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_data=array(array('nombre'=>$as_nomper,'cedula'=>$as_cedper,'cargo'=>$as_cargo,'unidad'=>$as_desuniadm,'categoria'=>$as_desded.' '.$as_destipper));
		$la_columna=array('nombre'=>'<b>Nombre del Funcionario</b>','cedula'=>'<b>C?dula de Identidad N?</b>',
						  'cargo'=>'<b>Denominaci?n del Cargo</b>','unidad'=>'<b>Ubicaci?n Administrativa</b>','categoria'=>'<b>Categoria</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>110),
						       		   'cedula'=>array('justification'=>'center','width'=>100),
									   'cargo'=>array('justification'=>'center','width'=>100),
									   'unidad'=>array('justification'=>'center','width'=>100),
									   'categoria'=>array('justification'=>'center','width'=>130))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		switch($as_tipcuebanper)
		{
			case "A": //Ahorro
				$ls_ahorro="X";
				$ls_corriente="";
				break;
			case "C": // Corriente
				$ls_ahorro="";
				$ls_corriente="X";
				break;
			default: // Ninguna
				$ls_ahorro="";
				$ls_corriente="";
				break;
		}
		$la_data=array(array('titulo'=>'<b>N? de Cuenta N?mina</b>','corriente'=>$ls_corriente,'titulocorriente'=>'Corriente',
							 'ahorro'=>$ls_ahorro,'tituloahorro'=>'Ahorro','cuenta'=>$as_codcueban));
		$la_columna=array('titulo'=>'','corriente'=>'','titulocorriente'=>'','ahorro'=>'','tituloahorro'=>'','cuenta'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>110),
						       		   'corriente'=>array('justification'=>'center','width'=>20),
									   'titulocorriente'=>array('justification'=>'center','width'=>80),
									   'ahorro'=>array('justification'=>'center','width'=>20),
									   'tituloahorro'=>array('justification'=>'center','width'=>80),
									   'cuenta'=>array('justification'=>'left','width'=>230))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>DESCRIPCI?N DE LA SOLICITUD</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-1.08);
		$ls_acompanante = "";
		if($ai_acompanante==1)
		{
			$ls_acompanante = "NO";
		}
		if($ai_acompanante>1)
		{
			$ls_acompanante = "SI";
		}
		$la_data=array(array('dependencia'=>'<b>Dependencia Solicitante:</b> '.$as_desuniadm));
		$la_columna=array('dependencia'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('dependencia'=>array('justification'=>'left','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('desde'=>'<b>Periodo Desde:</b> '.$ad_fecsalvia,'hasta'=>'<b>Periodo Hasta:</b> '.$ad_fecregvia,'dias'=>'<b>Total D?as:</b> '.$ai_numdiavia));
		$la_columna=array('desde'=>'','hasta'=>'','dias'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('desde'=>array('justification'=>'left','width'=>200),
						       		   'hasta'=>array('justification'=>'left','width'=>200),
									   'dias'=>array('justification'=>'left','width'=>140))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('mision'=>'<b>Conceptos de la Misi?n:</b> '.$as_denmis));
		$la_columna=array('mision'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('mision'=>array('justification'=>'left','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('mision'=>'<b>Tipo de Viatico:</b> '.$ls_tipovia));
		$la_columna=array('mision'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('mision'=>array('justification'=>'left','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>ASIGNACIONES</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>(NO SE RECONOCERAN CONCEPTOS DISTINTOS)</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('codigo'=>'<b>C?digo</b>','descripcion'=>'<b>Descripci?n</b>','tarifa'=>'<b>Tarifa</b>',
							 'dias'=>'<b>D?as</b>','subtotal'=>'<b>Subtotal</b>','subtotal'=>'<b>Subtotal</b>','subtotal'=>'<b>Subtotal</b>','tascam'=>'<b>Tasa Cambio</b>','denmon'=>'<b>Moneda</b>','subtotdiv'=>'<b>Subtotal Moneda</b>'));
		$la_columna=array('codigo'=>'','descripcion'=>'','tarifa'=>'','dias'=>'','subtotal'=>'','tascam'=>'','denmon'=>'','subtotdiv'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>40),
						               'descripcion'=>array('justification'=>'center','width'=>140),
									   'tarifa'=>array('justification'=>'center','width'=>60),
									   'dias'=>array('justification'=>'center','width'=>40),
									   'subtotal'=>array('justification'=>'center','width'=>70),
									   'tascam'=>array('justification'=>'right','width'=>60),
									   'denmon'=>array('justification'=>'right','width'=>50),
									   'subtotdiv'=>array('justification'=>'right','width'=>80))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_asignaciones($la_data,$ai_total,$as_totalletras,$ls_denmon,$li_totaldiv,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_asignaciones
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo="Total Misi?n Bs.F.:";
		}
		else
		{
			$ls_titulo="Total Misi?n Bs.:";
		}
		$la_columna=array('codigo'=>'','descripcion'=>'','tarifa'=>'','dias'=>'','subtotal'=>'','tascam'=>'','denmon'=>'','subtotdiv'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>40),
						               'descripcion'=>array('justification'=>'left','width'=>140),
									   'tarifa'=>array('justification'=>'right','width'=>60),
									   'dias'=>array('justification'=>'right','width'=>40),
									   'subtotal'=>array('justification'=>'right','width'=>70),
									   'tascam'=>array('justification'=>'right','width'=>60),
									   'denmon'=>array('justification'=>'center','width'=>50),
									   'subtotdiv'=>array('justification'=>'right','width'=>80))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>'.$ls_titulo.'</b>              ','total'=>$ai_total,'titulo2'=>'Total Mision '.$ls_denmon,'total2'=>$li_totaldiv));
		$la_columna=array('titulo'=>'','total'=>'','titulo2'=>'','total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>280),
									   'total'=>array('justification'=>'right','width'=>70),
									   'titulo2'=>array('justification'=>'right','width'=>110),
									   'total2'=>array('justification'=>'right','width'=>80))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('letra'=>'<b>Son:</b> '.$as_totalletras));
		$la_columna=array('letra'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_presupuestario($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_presupuestario
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datos=array(array('titulo'=>'<b>CONTROL PRESUPUESTARIO</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columna=array('ano'=>'<b>A?o</b>',
   						  'proyecto'=>'<b>'.$_SESSION["la_empresa"]["nomestpro1"].'</b>',
						  'especifica'=>'<b>'.$_SESSION["la_empresa"]["nomestpro2"].'</b>',
						  'subespecifica'=>'<b>'.$_SESSION["la_empresa"]["nomestpro3"].'</b>',
						  'cuenta'=>'<b>Partida Presupuestaria</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('ano'=>array('justification'=>'center','width'=>35), // Justificaci?n y ancho de la columna
						 			   'proyecto'=>array('justification'=>'center','width'=>140), // Justificaci?n y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>150), // Justificaci?n y ancho de la columna
						 			   'subespecifica'=>array('justification'=>'center','width'=>140), // Justificaci?n y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>75))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('elaborado'=>'<b>Elaborado Por:</b>','revisado'=>'<b>Revisado Por:</b>','autorizado'=>'<b>Autorizado Por:</b>'),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'Nombre y Apellido / Firma','revisado'=>'Firma / Sello','autorizado'=>'Firma / Sello'),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''));
		$la_columna=array('elaborado'=>'',
   						  'revisado'=>'',
						  'autorizado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('elaborado'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
						 			   'revisado'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
						 			   'autorizado'=>array('justification'=>'center','width'=>180))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>DATOS DEL BENEFICIARIO EN CONFORMIDAD DE RECEPCION DEL PAGO</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('nombre'=>'<b>Nombre y Apellido:</b>','cedula'=>'<b>C.I. No:</b>','firma'=>'<b>Firma:</b>','fecha'=>'<b>Fecha:</b>'),
					   array('nombre'=>'','cedula'=>'','firma'=>'','fecha'=>''),
					   array('nombre'=>'','cedula'=>'','firma'=>'','fecha'=>''),
					   array('nombre'=>'','cedula'=>'','firma'=>'','fecha'=>''),
					   array('nombre'=>'','cedula'=>'','firma'=>'','fecha'=>''));
		$la_columna=array('nombre'=>'',
   						  'cedula'=>'',
   						  'firma'=>'',
						  'fecha'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>135), // Justificaci?n y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>135), // Justificaci?n y ancho de la columna
						 			   'firma'=>array('justification'=>'center','width'=>135), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>135))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_viaticos.php");
	$io_fun_viaticos=new class_funciones_viaticos();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------

	$ld_desde=$io_fun_viaticos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_viaticos->uf_obtenervalor_get("hasta","");
	$ls_titulo="<b> SOLICITUD PAGO DE VIATICOS </b>";
	$ls_fecha="Periodo ".$ld_desde." - ".$ld_hasta;
	$ls_modalidad= $_SESSION["la_empresa"]["estmodest"];
	switch($ls_modalidad)
	{
		case "1": // Modalidad por Proyecto
			$ls_titest="Estructura Presupuestaria ";
			break;
			
		case "2": // Modalidad por Presupuesto
			$ls_titest="Estructura Program?tica ";
			break;
	}
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codsolvia="";
	$li_orden=$io_fun_viaticos->uf_obtenervalor_get("ordenfec","");
	$ls_codsoldes=$io_fun_viaticos->uf_obtenervalor_get("codsoldes","");
	$ls_codsolhas=$io_fun_viaticos->uf_obtenervalor_get("codsolhas","");
	$ls_tiporeporte=$io_fun_viaticos->uf_obtenervalor_get("tiporeporte",0);
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
	$lb_valido=$io_report->uf_select_solicitudviaticos($ls_codemp,"",$ld_desde,$ld_hasta,$ls_codsoldes,$ls_codsolhas,"","","","","","","","",$li_orden);
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$li_totrowsol=$io_report->ds->getRowCount("codsolvia");		
		$li_totrowtot=0;
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		$io_pdf->ezStartPageNumbers(545,25,10,'','',1); // Insertar el n?mero de p?gina
		for($li_z=1;$li_z<=$li_totrowsol;$li_z++)
		{
			$ls_codsolvia= $io_report->ds->data["codsolvia"][$li_z];
			$io_encabezado=$io_pdf->openObject();
			uf_print_encabezado_pagina($ls_codsolvia,$io_encabezado,$io_pdf); // Imprimimos el encabezado de la p?gina
			$rs_data="";
			$arrResultado=$io_report->uf_select_solicitudpago_personal($ls_codemp,$ls_codsoldes,$ls_codsolhas,$ld_desde,$ld_hasta,$li_orden,$ls_codsolvia,$rs_data); // Cargar el DS con los datos de la cabecera del reporte
			$lb_valido=$arrResultado['lb_valido'];
			$rs_data=$arrResultado['rs_data'];
			if($lb_valido==false) // Existe alg?n error ? no hay registros
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar');"); 
				print(" close();");
				print("</script>");
			}
			else // Imprimimos el reporte
			{
				$li_totrow=$io_report->io_sql->num_rows($rs_data);
				$li_totrowtot=$li_totrowtot+$li_totrow;
				$li_k=0;
				while($row=$io_report->io_sql->fetch_row($rs_data))
				{
					$li_k=$li_k+1;
					if($li_k>1)
					{
						$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					}
					$ls_cedper= $row["cedper"];
					$ls_nomper= $row["nomper"]." ".$row["apeper"];
					$ls_cargo= $row["cargo"];
					$ls_desuniadm= $row["desuniadm"];
					$ls_codcueban = $row["codcueban"];
					$ls_tipcuebanper= $row["tipcuebanper"];
					$ls_desded= $row["desded"];
					$ls_destipper= $row["destipper"];			
					$ls_codclavia= $row["codclavia"];
					$ld_fecsalvia= $row["fecsalvia"];
					$ld_fecregvia= $row["fecregvia"];
					$ld_fecsolvia= $row["fecsolvia"];
					$li_numdiavia= $row["numdiavia"];
					$ls_denmis= $row["denmis"];
					$li_acompanante= $row["acompanante"];
					$li_tipvia= $row["tipvia"];
					$ls_estopediv= $row["estopediv"];
					$ls_codmon= $row["codmon"];
					$ls_denmon= $row["denmon"];
					$li_tascam1= $row["tascam1"];
					$li_tascam1aux=number_format($li_tascam1,2,',','.');
					if($ls_codmon!="")
						$ls_tipovia="INTERNACIONAL, Moneda: ".$ls_codmon." - ".$ls_denmon." / Tasa vigente en la fecha de calculo del viatico: ".$li_tascam1aux;
					else
						$ls_tipovia="";
					uf_print_cabecera($ls_cedper,$ls_nomper,$ls_desuniadm,$ls_codcueban,$ls_tipcuebanper,$ls_desded,$ls_destipper,
									  $ls_codclavia,$ld_fecsalvia,$ld_fecregvia,$li_numdiavia,$ls_denmis,$li_acompanante,$ls_cargo,$ld_fecsolvia,$ls_tipovia,$io_pdf); // Imprimimos la cabecera del registro
					
					if ($li_tipvia=='-')
					{
						$lb_valido=$io_report->uf_select_solicitudpago_asignaciones($ls_codemp,$ls_codsolvia);
						if($lb_valido)
						{
							$li_totrow_det=$io_report->ds_detalle->getRowCount("codasi");
							$li_total=0;
							$li_totaldiv=0;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
							{
								$ls_codasi= $io_report->ds_detalle->data["codasi"][$li_s];
								$ls_denasi= $io_report->ds_detalle->data["denasi"][$li_s];
								$li_canasi= $io_report->ds_detalle->data["canasi"][$li_s];
								$li_monto= $io_report->ds_detalle->data["monto"][$li_s];
								$li_subtotal = $li_monto*$li_canasi;
								$li_subtotaldiv=$li_subtotal/$li_tascam1;
								$li_total=$li_total+$li_subtotal;
								$li_totaldiv=$li_totaldiv+$li_subtotaldiv;
								$li_canasi=number_format($li_canasi,2,",",".");
								$li_monto=number_format($li_monto,2,",",".");
								$li_subtotal=number_format($li_subtotal,2,",",".");
								$li_subtotaldiv=number_format($li_subtotaldiv,2,",",".");
								$la_data[$li_s]=array('codigo'=>$ls_codasi,'descripcion'=>$ls_denasi,'tarifa'=>$li_monto,'dias'=>$li_canasi,'subtotal'=>$li_subtotal,'tascam'=>$li_tascam1aux,'denmon'=>$ls_denmon,'subtotdiv'=>$li_subtotaldiv);
							}
							$io_numero_letra->setNumero($li_total);
							$ls_totalletras=$io_numero_letra->letra();
							$li_total=number_format($li_total,2,",",".");
							$li_totaldiv=number_format($li_totaldiv,2,",",".");
							uf_print_detalle_asignaciones($la_data,$li_total,$ls_totalletras,$ls_denmon,$li_totaldiv,$io_pdf); // Imprimimos el detalle 
							unset($la_data);
							$io_report->ds_detalle->reset_ds();
							$lb_existe=$io_report->uf_select_solicitudpago_spg($ls_codemp,$ls_codsolvia);
							if($lb_existe)
							{
								$li_totrow_detpres=$io_report->ds_detpresup->getRowCount("spg_cuenta");
								$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
								for($li_j=1;$li_j<=$li_totrow_detpres;$li_j++)
								{
									$ls_spgcuenta=$io_report->ds_detpresup->data["spg_cuenta"][$li_j];
									$ls_denestpro1=$io_report->ds_detpresup->data["denestpro1"][$li_j];
									$ls_denestpro2=$io_report->ds_detpresup->data["denestpro2"][$li_j];
									$ls_denestpro3=$io_report->ds_detpresup->data["denestpro3"][$li_j];
									$la_data[$li_j]=array('ano'=>$ls_ano,'proyecto'=>$ls_denestpro1,'especifica'=>$ls_denestpro2,'subespecifica'=>$ls_denestpro3,'cuenta'=>$ls_spgcuenta);
								}
								uf_print_detalle_presupuestario($la_data,$io_pdf); // Imprimimos el detalle 
								unset($la_data);
							}
						}
					}
					uf_print_firmas($io_pdf);
				}
			}
			$io_pdf->stopObject($io_encabezado); // Detener el objeto cabecera
			if($li_z<$li_totrowsol)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva p?gina
			}
		}
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_encabezado);
	$io_report->io_sql->free_result($rs_data);
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_viaticos);
?> 