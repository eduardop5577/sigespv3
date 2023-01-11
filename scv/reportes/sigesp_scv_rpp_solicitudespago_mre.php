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
		//	    Arguments: as_codsolvia // Código de Solicitud de Viaticos
		//	    		   io_encabezado // Instancia del encabezado
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_encabezado,$io_pdf;
		$io_pdf->saveState();
		$io_pdf->line(30,785,570,785);//Horizontal
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		$io_pdf->line(30,710,570,710);//Horizontal
		$io_pdf->line(30,785,30,40);//Vertical
		$io_pdf->line(30,750,570,750);//Horizontal
		$io_pdf->line(420,785,420,710);//Vertical
		$io_pdf->line(570,785,570,40);//Vertical
		$io_pdf->line(30,40,570,40);//Horizontal
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->setColor(0,0,0);		
		$io_pdf->addText(452,774,9,"1. VIATICO NRO."); // Agregar FECHA
		$io_pdf->addText(485,761,10,"<b>".$as_codsolvia."</b>"); // Agregar NRO DE CONTROL
		$io_pdf->addText(452,740,9,"2. FECHA"); // Agregar NRO DE CONTROL
		$io_pdf->addText(483,725,10,"<b>".date("d/m/Y")."</b>"); // Agregar FECHA		
		$io_pdf->addText(33,735,9,"<b>OFICINA DE SERVICIOS ADMINISTRATIVOS</b>"); // Agregar NRO DE CONTROL
		$io_pdf->addText(33,725,9,"<b>DIRECCIÓN DE ADMINISTRACIÓN</b>"); // Agregar NRO DE CONTROL
		$io_pdf->addText(33,715,9,"<b>ÁREA DE VÍATICOS Y PASAJE</b>"); // Agregar NRO DE CONTROL
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/headmre.JPG',30.5,754,260,30); // Agregar Logo
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nomper,$as_desuniadm,$as_codcueban,$as_tipcuebanper,$as_desded,$as_destipper,
							  $as_codclavia,$ad_fecsalvia,$ad_fecregvia,$ai_numdiavia,$as_denmis,$ai_acompanante,$as_cargo,
							  $ai_tipvia,$as_titulotip,$as_mppre,$ls_codnom,$ls_obssolvia,$ls_mision_d,$ls_tipopago,$li_montar,
							  $li_tasacambio,$li_totsolviadol,$li_totsolvia,$li_porinc,$li_montarporinc,$li_porcar,
							  $li_montarcarfam,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_spgcuenta,$ls_numaut, $ld_fecaut,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: $as_cedper  // Cédula de personal
		//	    		   $as_nomper     // Nombre del personal
		//	    		   $as_desuniadm     // descripción de la unidad administrativa
		//	    		   $as_codcueban     // codigo cuenta de banco
		//	    		   $as_tipcuebanper     // tipo de cuenta de banco
		//	    		   $as_desded  // Descripción de la dedicación
		//	    		   $as_destipper  // Descripción del tipo de personal
		//	    		   $as_codclavia  // Clasificación del viaticos
		//	    		   $ad_fecsalvia  // fecha de salida del viatico
		//	    		   $ad_fecregvia  // fecha de regreso del viatico
		//	    		   $ai_numdiavia     // numero de dias
		//	    		   $as_denmis  // Denominación de las misiones
		//	    		   io_pdf         // Instancia del objeto pdf
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 29/11/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//////////////////////////  MONTOS EN LETRAS  //////////////////////////	
		global  $io_pdf;
//		include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
		$numalet= new class_numero_a_letra();
		//imprime numero con los valore por defecto
		//cambia a minusculas
		$numalet->setMayusculas(1);
		//cambia a femenino
		$numalet->setGenero(1);
		//cambia moneda
		$numalet->setMoneda("Bolivares");
		//cambia prefijo
		$numalet->setPrefijo("***");
		//cambia sufijo
		$numalet->setSufijo("***");
		$numalet->setNumero($li_totsolvia);
		$ls_totsolvialet= $numalet->letra();
		$ls_totsolvialet1=substr($ls_totsolvialet,0,70);
		$ls_totsolvialet2=substr($ls_totsolvialet,70,200);
		$numaletdol= new class_numero_a_letra();
		//imprime numero con los valore por defecto
		//cambia a minusculas
		$numaletdol->setMayusculas(1);
		//cambia a femenino
		$numaletdol->setGenero(1);
		//cambia moneda
		$numaletdol->setMoneda("Dolares");
		//cambia prefijo
		$numaletdol->setPrefijo("***");
		//cambia sufijo
		$numaletdol->setSufijo("***");
		$numaletdol->setNumero($li_totsolviadol);
		$li_totsolviadollet= $numaletdol->letra();
		$li_totsolviadollet=str_replace("BOLIVARES","DOLARES",$li_totsolviadollet);
		$li_totsolviadollet=str_replace("CENTIMOS","CENTAVOS",$li_totsolviadollet);
		$li_totsolviadollet1=substr($li_totsolviadollet,0,70);
		$li_totsolviadollet2=substr($li_totsolviadollet,70,200);
		//////////////////////////  MONTOS EN LETRAS  //////////////////////////	
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();				
		$ad_fecsalvia=$io_funciones->uf_convertirfecmostrar($ad_fecsalvia);
		$ad_fecregvia=$io_funciones->uf_convertirfecmostrar($ad_fecregvia);
		$ai_numdiavia=number_format($ai_numdiavia,0,",",".");
		$li_montar=number_format($li_montar,2,",",".");
		$li_totsolviadol=number_format($li_totsolviadol,2,",",".");
		$li_totsolvia=number_format($li_totsolvia,2,",",".");
		$li_tasacambio=number_format($li_tasacambio,2,",",".");
		$li_porinc=number_format($li_porinc,2,",",".");
		$li_montarporinc=number_format($li_montarporinc,2,",",".");
		$li_porcar=number_format($li_porcar,2,",",".");
		$li_montarcarfam=number_format($li_montarcarfam,2,",",".");
        $io_pdf->setColor(0,0,0);
		$ls_spgcuenta1=substr($ls_spgcuenta,0,3);
		$ls_spgcuenta2=substr($ls_spgcuenta,3,4);
		$ls_spgcuenta3=substr($ls_spgcuenta,7,2);
		$ld_fecaut=$io_funciones->uf_convertirfecmostrar($ld_fecaut);
		$ls_autorizacion = $ls_numaut.' - '.$ld_fecaut;
		
		// Ministerio
		$io_pdf->addText(220,690,12,"<b><i>".$as_titulotip."</i></b>"); // Agregar NRO DE CONTROL$as_titulotip
		$io_pdf->line(30,680,570,680);//Horizontal
		$io_pdf->addText(33,669,9,"3. DEPENDENCIA SOLICITANTE "); // Agregar FECHA
		$io_pdf->addText(45,650,10,$as_desuniadm); // Agregar FECHA//
		$io_pdf->line(30,645,570,645);//Horizontal
		$io_pdf->line(420,680,420,645);//Vertical		
		$io_pdf->addText(423,669,9,"4. AUTORIZACIÓN  -  DE FECHA"); // Agregar FECHA
		$io_pdf->addText(429,650,10,$ls_autorizacion);
		$io_pdf->line(30,628,570,628);//Horizontal
		$io_pdf->addText(240,634,9,"<b>BENEFICIARIO DEL CHEQUE</b>"); // Agregar NRO DE CONTROL$as_titulotip
		$io_pdf->addText(33,618,9,"5. APELLIDOS Y NOMBRES "); // Agregar FECHA
		$io_pdf->addText(45,600,10,$as_nomper); // Agregar FECHA//
		$io_pdf->line(370,628,370,590);//Vertical
		$io_pdf->addText(375,618,9,"6. CÉDULA "); // Agregar FECHA
		$io_pdf->addText(380,600,10,$as_cedper); // Agregar FECHA//
		$io_pdf->addText(440,618,9,"7. FUNCIONARIO DEL MPPRE "); // Agregar FECHA
        $io_pdf->addText(450,600,10,$as_mppre); // Agregar FECHA//
		$io_pdf->line(435,628,435,590);//Vertical
		$io_pdf->line(30,590,570,590);//Horizontal
		$io_pdf->addText(33,580,9,"8. CARGO "); // Agregar FECHA
		$io_pdf->addText(45,565,10,$as_cargo); // Agregar FECHA//
		$io_pdf->line(370,590,370,560);//Vertical
		$io_pdf->addText(375,580,9,"9. NÚMERO NOMINA "); // Agregar FECHA
		$io_pdf->addText(380,565,10,$ls_codnom); // Agregar FECHA//
		$io_pdf->line(30,560,570,560);//Horizontal
		$io_pdf->addText(250,550,9,"<b>DATOS DEL VIATICO</b>"); // Agregar NRO DE CONTROL$as_titulotip
		$io_pdf->line(30,545,570,545);//Horizontal
		$io_pdf->addText(33,535,9,"10. MOTIVO "); // Agregar FECHA
		$ls_obssolvia_c=wordwrap($ls_obssolvia,108,"?");
		$la_arreglo=array();
		$la_arreglo=explode("?",$ls_obssolvia_c);
		if(array_key_exists(0,$la_arreglo))
			$io_pdf->addText(45,525,9,$la_arreglo[0]);
		if(array_key_exists(1,$la_arreglo))
			$io_pdf->addText(45,516,9,$la_arreglo[1]);
		if(array_key_exists(2,$la_arreglo))
			$io_pdf->addText(45,507,9,$la_arreglo[2]);
		$io_pdf->line(30,505,570,505);//Horizontal
		$io_pdf->addText(33,495,9,"11. CALCULO DEL VIATICO "); // Agregar FECHA
		$io_pdf->line(30,490,570,490);//Horizontal
		$io_pdf->addText(33,480,8,"ASIGNACIÓN"); // Agregar FECHA
		$io_pdf->addText(33,450,8,$li_montar); // Agregar FECHA
		$io_pdf->line(90,490,90,440);//Vertical
		$io_pdf->addText(92,480,8,"% INCREMENTO"); // Agregar FECHA
		$io_pdf->addText(92,450,8,$li_porinc); // Agregar FECHA
		$io_pdf->line(160,490,160,440);//Vertical
		$io_pdf->addText(175,480,8,"RESULTADO"); // Agregar FECHA
		$io_pdf->addText(172,472,8,"INCREMENTO"); // Agregar FECHA
		$io_pdf->addText(172,450,8,$li_montarporinc); // Agregar FECHA
		$io_pdf->line(235,490,235,440);//Vertical
		$io_pdf->addText(245,480,8,"%CARGA"); // Agregar FECHA
		$io_pdf->addText(245,472,8,"FAMILIAR"); // Agregar FECHA
		$io_pdf->addText(245,450,8,$li_porcar); // Agregar FECHA
		$io_pdf->line(295,490,295,440);//Vertical
		$io_pdf->addText(300,480,8,"TOTAL CARGA"); // Agregar FECHA
		$io_pdf->addText(307,472,8,"FAMILIAR"); // Agregar FECHA
		$io_pdf->addText(307,450,8,$li_montarcarfam); // Agregar FECHA
		$io_pdf->line(360,490,360,440);//Vertical
		$io_pdf->addText(363,480,8,"TOTAL DOLARES"); // Agregar FECHA
		$io_pdf->addText(363,450,8,$li_totsolviadol); // Agregar FECHA
		$io_pdf->line(433,490,433,440);//Vertical
		$io_pdf->addText(440,480,8,"CAMBIO"); // Agregar FECHA
		$io_pdf->addText(440,450,8,$li_tasacambio); // Agregar FECHA
		$io_pdf->line(480,490,480,440);//Vertical
		$io_pdf->addText(485,480,8,"TOTAL BOLIVARES"); // Agregar FECHA
		$io_pdf->addText(485,450,8,$li_totsolvia); // Agregar FECHA
		$io_pdf->line(30,440,570,440);//Horizontal
		$io_pdf->addText(33,430,9,"MISIÓN ORIGEN: "); // Agregar FECHA
		$io_pdf->addText(130,430,9,$as_denmis); // Agregar FECHA
		$io_pdf->addText(33,400,9,"MISION DESTINO:"); // Agregar FECHA
		$io_pdf->addText(130,400,9,$ls_mision_d); // Agregar FECHA
		$io_pdf->line(30,395,570,395);//Horizontal
		$io_pdf->addText(33,385,9,"12. BOLIVARES EN LETRAS "); // Agregar FECHA
		$io_pdf->addText(43,370,9,$ls_totsolvialet1); // Agregar FECHA
		$io_pdf->addText(43,360,9,$ls_totsolvialet2); // Agregar FECHA
		$io_pdf->line(440,395,440,300);//Vertical
		$io_pdf->addText(450,385,9,"13. BOLIVARES EN CIFRAS "); // Agregar FECHA
		$io_pdf->addText(463,365,9,$li_totsolvia); // Agregar FECHA
		$io_pdf->line(30,350,570,350);//Horizontal
		$io_pdf->addText(33,340,9,"14. DOLARES EN LETRAS "); // Agregar FECHA
		$io_pdf->addText(43,325,9,$li_totsolviadollet1); // Agregar FECHA
		$io_pdf->addText(43,315,9,$li_totsolviadollet2); // Agregar FECHA
		$io_pdf->addText(450,340,9,"15. DOLARES EN CIFRAS "); // Agregar FECHA
		$io_pdf->addText(463,320,9,$li_totsolviadol); // Agregar FECHA
		$io_pdf->line(30,300,570,300);//Horizontal
		$io_pdf->addText(33,288,9,"16. FORMA DE PAGO "); // Agregar FECHA
		$io_pdf->addText(160,288,9,$ls_tipopago." $"); // Agregar FECHA
		$io_pdf->line(30,280,570,280);//Horizontal
		$io_pdf->addText(100,267,9,"CODIFICACIÓN "); // Agregar FECHA
		$io_pdf->addText(460,267,9,"SUB-PARTIDA "); // Agregar FECHA
		//$io_pdf->line(350,280,350,40);//Vertical
		$io_pdf->addText(60,250,8,"AÑO"); // Agregar FECHA
		$io_pdf->line(50,260,50,179);//Vertical
		$io_pdf->addText(95,250,8,"FONDO"); // Agregar FECHA
		$io_pdf->line(90,260,90,179);//Vertical
		$io_pdf->addText(134,250,8,"UNID."); // Agregar FECHA
		$io_pdf->addText(134,242,8,"PRIM."); // Agregar FECHA
		$io_pdf->line(130,260,130,179);//Vertical
		$io_pdf->addText(162,250,8,"ACC. CENT"); // Agregar FECHA
		$io_pdf->addText(162,242,8,"PROYECTO"); // Agregar FECHA
		$io_pdf->addText(168,230,8,$ls_codestpro1); // Agregar FECHA
		$io_pdf->line(160,260,160,179);//Vertical
		$io_pdf->addText(219,250,8,"ACCIÓN"); // Agregar FECHA
		$io_pdf->addText(219,242,8,"ESPECIF."); // Agregar FECHA
		$io_pdf->addText(229,230,8,$ls_codestpro2); // Agregar FECHA
		$io_pdf->line(207,260,207,179);//Vertical
		$io_pdf->addText(300,250,8,"UEL"); // Agregar FECHA
		$io_pdf->addText(290,230,8,$ls_codestpro3); // Agregar FECHA
		$io_pdf->line(270,260,270,179);//Vertical
		$io_pdf->addText(360,250,8,"PART."); // Agregar FECHA
		$io_pdf->addText(363,230,8,$ls_spgcuenta1); // Agregar FECHA
		$io_pdf->line(340,260,340,179);//Vertical
		$io_pdf->addText(430,250,8,"GENERIC"); // Agregar FECHA
		$io_pdf->addText(435,230,8,$ls_spgcuenta2); // Agregar FECHA
		$io_pdf->line(400,280,400,179);//Vertical
		$io_pdf->addText(510,250,8,"ESPECIFIC."); // Agregar FECHA
		$io_pdf->addText(525,230,8,$ls_spgcuenta3); // Agregar FECHA
		$io_pdf->line(490,260,490,179);//Vertical
		
		
		
		
		
		$io_pdf->line(30,260,570,260);//Horizontal
		$io_pdf->line(30,240,570,240);//Horizontal
		//Ministerio
		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_asignaciones($la_data,$ai_total,$as_totalletras,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_asignaciones
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global  $io_pdf;
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo="Total Misión Bs.F.:";
		}
		else
		{
			$ls_titulo="Total Misión Bs.:";
		}
		$la_columna=array('codigo'=>'','descripcion'=>'','tarifa'=>'','dias'=>'','subtotal'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>60),
						               'descripcion'=>array('justification'=>'left','width'=>220),
									   'tarifa'=>array('justification'=>'right','width'=>100),
									   'dias'=>array('justification'=>'center','width'=>60),
									   'subtotal'=>array('justification'=>'right','width'=>100))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>'.$ls_titulo.'</b>              ','total'=>$ai_total));
		$la_columna=array('titulo'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>440),
									   'total'=>array('justification'=>'right','width'=>100))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('letra'=>'<b>Son:</b> '.$as_totalletras));
		$la_columna=array('letra'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>540))); // Ancho Máximo de la tabla
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
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global  $io_pdf;
		$la_datos=array(array('titulo'=>'<b>CONTROL PRESUPUESTARIO</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>540))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-2);
		$la_columna=array('ano'=>'<b>Año</b>',
   						  'proyecto'=>'<b>'.$_SESSION["la_empresa"]["nomestpro1"].'</b>',
						  'especifica'=>'<b>'.$_SESSION["la_empresa"]["nomestpro2"].'</b>',
						  'subespecifica'=>'<b>'.$_SESSION["la_empresa"]["nomestpro3"].'</b>',
						  'cuenta'=>'<b>Partida Presupuestaria</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('ano'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna
						 			   'proyecto'=>array('justification'=>'center','width'=>140), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 			   'subespecifica'=>array('justification'=>'center','width'=>140), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la columna
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
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global  $io_pdf;
		$io_pdf->ezSetY(180);
		$la_data=array(array('elaborado'=>'<b>Elaborado Por:</b>','revisado'=>'<b>Revisado Por:</b>','autorizado'=>'<b>Autorizado Por:</b>'),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''),
					   array('elaborado'=>'Nombre y Apellido / Firma','revisado'=>'Firma / Sello','autorizado'=>'Firma / Sello'),
					   array('elaborado'=>'','revisado'=>'','autorizado'=>''));
		$la_columna=array('elaborado'=>'',
   						  'revisado'=>'',
						  'autorizado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>539, // Ancho de la tabla
						 'maxWidth'=>539, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('elaborado'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'revisado'=>array('justification'=>'center','width'=>180), // Justificación y ancho de la columna
						 			   'autorizado'=>array('justification'=>'center','width'=>180))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('titulo'=>'<b>DATOS DEL BENEFICIARIO EN CONFORMIDAD DE RECEPCION DEL PAGO</b>'));
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>539, // Ancho de la tabla
						 'maxWidth'=>539,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>539))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('nombre'=>'<b>Nombre y Apellido:</b>','cedula'=>'<b>C.I. No:</b>','firma'=>'<b>Firma:</b>','fecha'=>'<b>Fecha:</b>'));
		$la_columna=array('nombre'=>'',
   						  'cedula'=>'',
   						  'firma'=>'',
						  'fecha'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'rowGap' => 1,
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>539, // Ancho de la tabla
						 'maxWidth'=>539, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('nombre'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'firma'=>array('justification'=>'center','width'=>135), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>135))); // Justificación y ancho de la columna
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
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------

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
			$ls_titest="Estructura Programática ";
			break;
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codsolvia="";
	$li_orden=$io_fun_viaticos->uf_obtenervalor_get("ordenfec","");
	$ls_codsoldes=$io_fun_viaticos->uf_obtenervalor_get("codsoldes","");
	$ls_codsolhas=$io_fun_viaticos->uf_obtenervalor_get("codsolhas","");
	$ls_tiporeporte=$io_fun_viaticos->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipvia=$io_fun_viaticos->uf_obtenervalor_get("tipvia",0);
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
	$lb_valido=$io_report->uf_select_solicitudviaticos($ls_codemp,"",$ld_desde,$ld_hasta,$ls_codsoldes,$ls_codsolhas,"","","","","","","","",$li_orden,$ls_tipvia);
	if($lb_valido==false) // Existe algún error ó no hay registros
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
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		$io_pdf->ezStartPageNumbers(545,25,10,'','',1); // Insertar el número de página
		for($li_z=1;$li_z<=$li_totrowsol;$li_z++)
		{
			$ls_codsolvia= $io_report->ds->data["codsolvia"][$li_z];
			$arrResultado=$io_report->uf_select_solicitudpago_personal($ls_codemp,$ls_codsoldes,$ls_codsolhas,$ld_desde,$ld_hasta,$li_orden,$ls_codsolvia,$rs_data); // Cargar el DS con los datos de la cabecera del reporte
			$lb_valido=$arrResultado['lb_valido'];
			$rs_data=$arrResultado['rs_data'];
			if($lb_valido==false) // Existe algún error ó no hay registros
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
				$li_porinc=0;
				$li_montarporinc=0;
				$li_porcar=0;
				$li_montarcarfam=0;
				$li_montar= 0;
				$li_tasacambio= 0;
				while($row=$io_report->io_sql->fetch_row($rs_data))
				{
					$li_k=$li_k+1;
					$ls_codsolvia= $row["codsolvia"];
					$io_encabezado=$io_pdf->openObject();
					$ls_cedper= $row["cedper"];
					$ls_nomper= $row["nomper"]." ".$row["apeper"];
					$ls_cargo= $row["cargo"];
					$ls_codcar= $row["codcar"];
					$ls_desuniadm= $row["desuniadm"];
					$ls_codcueban = $row["codcueban"];
					$ls_tipcuebanper= $row["tipcuebanper"];
					$ls_desded= $row["desded"];
					$ls_destipper= $row["destipper"];			
					$ls_codclavia= $row["codclavia"];
					$ld_fecsalvia= $row["fecsalvia"];
					$ld_fecregvia= $row["fecregvia"];
					$li_numdiavia= $row["numdiavia"];
					$ls_denmis= $row["denmis"];
					$li_acompanante= $row["acompanante"];
					$li_tipvia= $row["tipvia"];
					$ls_funcmppre= $row["mppre"];
					$ls_obssolvia= $row["obssolvia"];
					$ls_mision_d= $row["mision_d"];
					$ls_tipopago= $row["tipodoc"];
					$li_monsolvia= $row["monsolvia"];
					$ls_codestpro1= $row["codestpro1"];
					$ls_codestpro2= $row["codestpro2"];
					$ls_codestpro3= $row["codestpro3"];
					$ls_codestpro4= $row["codestpro4"];
					$ls_codestpro5= $row["codestpro5"];
					$li_totsolviadol= $row["mondolsol"];
					$li_tasacambio= $row["tascamsol"];
					$ls_numaut= $row["numaut"];
					$ld_fecaut= $row["fecaut"];
					$li_len1=0;
					$li_len2=0;
					$li_len3=0;
					$li_len4=0;
					$li_len5=0;
					$ls_titulo="";
					$arrResultado=$io_fun_viaticos->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
					$li_len1=$arrResultado['ai_len1'];
					$li_len2=$arrResultado['ai_len2'];
					$li_len3=$arrResultado['ai_len3'];
					$li_len4=$arrResultado['ai_len4'];
					$li_len5=$arrResultado['ai_len5'];
					$ls_titulo=$arrResultado['as_titulo'];
					$ls_codestpro1=substr($ls_codestpro1,(25-$li_len1),$li_len1);
					$ls_codestpro2=substr($ls_codestpro2,(25-$li_len2),$li_len2);
					$ls_codestpro3=substr($ls_codestpro3,(25-$li_len3),$li_len3);
					$ls_codestpro4=substr($ls_codestpro4,(25-$li_len4),$li_len4);
					$ls_codestpro5=substr($ls_codestpro5,(25-$li_len5),$li_len5);		
					if ($ls_tipopago=='0')
					{
						$ls_tipopago='TRANSFERENCIA';
					}
					elseif ($ls_tipopago=='1')
					{
						$ls_tipopago='EFECTIVO';
					}
					else
					{
						$ls_tipopago='TRANSFERENCIA Y EFECTIVO';
					}
					if ($ls_funcmppre!="")
					{
						$ls_mppre='SI';
					}
					else
					{
						$ls_mppre='NO';
					}
					if ($li_tipvia=='1')
					{
						$ls_titulotip='VIATICOS DE INSTALACIÓN';
						$li_totsolvia=($li_totsolviadol*$li_tasacambio);
					}
					elseif($li_tipvia=='2')
					{
						$ls_titulotip='ORDEN DE TRANSPORTE';
						$li_totsolvia=($li_totsolviadol*$li_tasacambio);
					}
					elseif($li_tipvia=='3')
					{
						$ls_titulotip='PERMANENCIA';
						$li_totsolvia=($li_totsolviadol*$li_tasacambio);
					}
					elseif($li_tipvia=='4')
					{
						$ls_titulotip='INTERNACIONALES';
						$li_totsolvia=($li_totsolviadol*$li_tasacambio);
					}
					elseif($li_tipvia=='5')
					{
						$ls_titulotip='NACIONALES';
						$li_montar=$li_monsolvia;
						$li_tasacambio= 1;
						$li_totsolviadol=0;
						$li_totsolvia=$li_monsolvia;
					}
					
					if ($li_tipvia=='-')
					{
						$lb_valido=$io_report->uf_select_solicitudpago_asignaciones($ls_codemp,$ls_codsolvia);
						if($lb_valido)
						{
							$li_totrow_det=$io_report->ds_detalle->getRowCount("codasi");
							$li_total=0;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
							{
								$ls_codasi= $io_report->ds_detalle->data["codasi"][$li_s];
								$ls_denasi= $io_report->ds_detalle->data["denasi"][$li_s];
								$li_canasi= $io_report->ds_detalle->data["canasi"][$li_s];
								$li_monto= $io_report->ds_detalle->data["monto"][$li_s];
								$li_subtotal = $li_monto*$li_canasi;
								$li_total=$li_total+$li_subtotal;
								$li_canasi=number_format($li_canasi,2,",",".");
								$li_monto=number_format($li_monto,2,",",".");
								$li_subtotal=number_format($li_subtotal,2,",",".");
								$la_data[$li_s]=array('codigo'=>$ls_codasi,'descripcion'=>$ls_denasi,'tarifa'=>$li_monto,'dias'=>$li_canasi,'subtotal'=>$li_subtotal);
							}
							$io_numero_letra->setNumero($li_total);
							$ls_totalletras=$io_numero_letra->letra();
							$li_total=number_format($li_total,2,",",".");
							uf_print_detalle_asignaciones($la_data,$li_total,$ls_totalletras,$io_pdf); // Imprimimos el detalle 
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
					else
					{
						$lb_existe=$io_report->uf_select_solicitudpago_spg($ls_codemp,$ls_codsolvia);
						if($lb_existe)
						{
							$li_totrow_detpres=$io_report->ds_detpresup->getRowCount("spg_cuenta");
							$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
							$li_montospg=0;
							$ls_spgcuenta="";
							for($li_j=1;$li_j<=$li_totrow_detpres;$li_j++)
							{
								$ls_spgcuenta=$io_report->ds_detpresup->data["spg_cuenta"][$li_j];
								$ls_denestpro1=$io_report->ds_detpresup->data["denestpro1"][$li_j];
								$ls_denestpro2=$io_report->ds_detpresup->data["denestpro2"][$li_j];
								$ls_denestpro3=$io_report->ds_detpresup->data["denestpro3"][$li_j];
								$li_montospg=$io_report->ds_detpresup->data["monto"][$li_j];
								$la_data[$li_j]=array('ano'=>$ls_ano,'proyecto'=>$ls_denestpro1,'especifica'=>$ls_denestpro2,'subespecifica'=>$ls_denestpro3,'cuenta'=>$ls_spgcuenta);
							}
							//uf_print_detalle_presupuestario($la_data,$io_pdf); // Imprimimos el detalle 
							unset($la_data);
						}
						$lb_existe=$io_report->uf_select_dt_scg($ls_codemp,$ls_codsolvia);
						if($lb_existe)
						{
							$li_totrow_detpres=$io_report->ds_detpresup->getRowCount("spg_cuenta");
							$ls_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
							$li_montoscg=0;
							for($li_j=1;$li_j<=$li_totrow_detpres;$li_j++)
							{
								$li_montoscg=$io_report->ds_detcontable->data["monto"][$li_j];
							}
							unset($la_data);
						}
					}
					$ls_codsolviaaux=$ls_codsolvia;
					if ($li_tipvia=='1')
					{
						if($li_tasacambio>0)
							$li_montar=($li_montoscg/$li_tasacambio);
						else
							$li_montar=$li_montoscg;
						$li_totsolviadol=$li_montar;
						$li_totsolvia=$li_montoscg;
						$ls_codsolviaaux="E".$ls_codsolvia;
					}					
					uf_print_encabezado_pagina($ls_codsolviaaux,$io_encabezado,$io_pdf); // Imprimimos el encabezado de la página
					uf_print_cabecera($ls_cedper,$ls_nomper,$ls_desuniadm,$ls_codcueban,$ls_tipcuebanper,$ls_desded,$ls_destipper,
									  $ls_codclavia,$ld_fecsalvia,$ld_fecregvia,$li_numdiavia,$ls_denmis,$li_acompanante,$ls_cargo,
									  $li_tipvia,$ls_titulotip,$ls_mppre,$ls_funcmppre,$ls_obssolvia,$ls_mision_d,$ls_tipopago,
									  $li_montar,$li_tasacambio,$li_totsolviadol,$li_totsolvia,$li_porinc,$li_montarporinc,
									  $li_porcar,$li_montarcarfam,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_spgcuenta,$ls_numaut, $ld_fecaut,
									  $io_pdf); // Imprimimos la cabecera del registro
					uf_print_firmas($io_pdf);
					$io_pdf->stopObject($io_encabezado); // Detener el objeto cabecera
					if ($li_tipvia=='1')
					{
						$io_pdf->ezNewPage(); // Insertar una nueva página
						if($li_tasacambio>0)
							$li_montar=($li_montospg/$li_tasacambio);
						else
							$li_montar=$li_montospg;
						$li_totsolviadol=$li_montar;
						$li_totsolvia=$li_montospg;
						$ls_codsolviaaux="T".$ls_codsolvia;
						uf_print_encabezado_pagina($ls_codsolviaaux,$io_encabezado,$io_pdf); // Imprimimos el encabezado de la página
						uf_print_cabecera($ls_cedper,$ls_nomper,$ls_desuniadm,$ls_codcueban,$ls_tipcuebanper,$ls_desded,$ls_destipper,
										  $ls_codclavia,$ld_fecsalvia,$ld_fecregvia,$li_numdiavia,$ls_denmis,$li_acompanante,$ls_cargo,
										  $li_tipvia,$ls_titulotip,$ls_mppre,$ls_funcmppre,$ls_obssolvia,$ls_mision_d,$ls_tipopago,
										  $li_montar,$li_tasacambio,$li_totsolviadol,$li_totsolvia,$li_porinc,$li_montarporinc,
										  $li_porcar,$li_montarcarfam,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_spgcuenta,$ls_numaut, $ld_fecaut,
										  $io_pdf); // Imprimimos la cabecera del registro
						uf_print_firmas($io_pdf);
					}
				}
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