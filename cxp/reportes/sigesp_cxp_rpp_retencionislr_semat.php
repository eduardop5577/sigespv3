<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
		// Fecha Creación: 03/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesislr.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_numdoc,$ld_fecemidoc,$as_perfiscal,$as_numsol,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$ls_y=substr($ld_fecemidoc,6,4);
		$ls_m=substr($ld_fecemidoc,3,2);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_iribarren.jpg',47,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],660,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$io_pdf->rectangle(540,470,200,30);
        $io_pdf->Rectangle(160,520,450,60);	
		$io_pdf->addText(310,560,10,"<b>Republica Bolivariana de Venezuela</b>");		
		$io_pdf->addText(360,550,10,"<b>Estado Lara</b>"); // 		
		$io_pdf->addText(320,540,10,"<b>Alcaldia del Municipio Iribarren</b>"); // 			
		$io_pdf->addText(265,530,10,"<b>Servicio Municipal de Administracion Tributaria (SEMAT)</b>"); // 			
				
//		$io_pdf->rectangle(320,510,420,40);
//		$io_pdf->line(320,530,740,530);
//		$io_pdf->line(420,510,420,550);
//		$io_pdf->line(526,510,526,550);
//		$io_pdf->line(632,510,632,550);
//		$io_pdf->addText(330,535,9,"<b> Solicitud de Pago</b> ");
//		$io_pdf->addText(330,515,9,$as_numsol);
		$io_pdf->addText(550,480,9,"<b> No. Control</b> ");
		$io_pdf->addText(620,480,9,$as_numdoc);
//		$io_pdf->addText(560,535,9,"<b> Fecha</b> ");
//		$io_pdf->addText(565,515,9,$ld_fecemidoc);
//		$io_pdf->addText(640,535,9,"<b> Periodo Fiscal</b> ");
//		$io_pdf->addText(660,515,9,$as_perfiscal);
	//	$io_pdf->restoreState();
	//	$io_pdf->closeObject();
	//	$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nomproben,$as_rifproben,$ls_fecpag,$ls_direccion,$ls_telefono,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado
		//		   Access: private 
		//	    Arguments: as_agente // Nombre del agente de retención
		//	    		   as_nombre // Nombre del proveedor ó beneficiario
		//	    		   as_rif // Rif del proveedor ó beneficiario
		//	    		   as_nit // nit del proveedor ó beneficiario
		//	    		   as_telefono // Telefono del proveedor ó beneficiario
		//	    		   as_direccion // Dirección del proveedor ó beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		  $ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
          $ls_dirageret = $_SESSION["la_empresa"]["direccion"];
          $ls_estageret = $_SESSION["la_empresa"]["estemp"];
          $ls_ciuageret = $_SESSION["la_empresa"]["ciuemp"];
		  
	      $la_data = array(array('ageret'=>' ENTIDAD DE CARACTER PUBLICO:'));
	      $la_columna = array('ageret'=>'');
		  $la_config  = array('showHeadings'=>0, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>700))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

        $io_pdf->ezSetDy(-2);
		  $la_data    = array(array('ageret'=>$as_agente,'rifageret'=>$ls_rifageret,'dirageret'=>$ls_dirageret,'ciuageret'=>$ls_ciuageret,'estageret'=>$ls_estageret,'munageret'=>' IRIBARREN'));	
	      $la_columna = array('ageret'=>' NOMBRE','rifageret'=>' R.I.F.','dirageret'=>' DIRECCION','ciuageret'=>' CIUDAD','estageret'=>' ESTADO','munageret'=>' MUNICIPIO');
		  $la_config  = array('showHeadings'=>1, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 8, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>150),
									        'rifageret'=>array('justification'=>'left','width'=>80),
									        'dirageret'=>array('justification'=>'left','width'=>230),
									        'ciuageret'=>array('justification'=>'left','width'=>80),
									        'estageret'=>array('justification'=>'left','width'=>80),
									        'munageret'=>array('justification'=>'left','width'=>80))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

 	      $la_data = array(array('ageret'=>' AGENTES DE RETENCION:'));
	      $la_columna = array('ageret'=>'');
		  $la_config  = array('showHeadings'=>0, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>700))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

        $io_pdf->ezSetDy(-2);
		  $la_data    = array(array('ageret'=>'FRANCOIS ALVAREZ','rifageret'=>'V- 13.245.411','dirageret'=>'URB. TARABANA PLAZA CASA 5-45 CABUDARE','ciuageret'=>'CABUDARE','estageret'=>'LARA','munageret'=>'PALAVECINO'));	
	      $la_columna = array('ageret'=>' NOMBRE','rifageret'=>' R.I.F.','dirageret'=>' DIRECCION','ciuageret'=>' CIUDAD','estageret'=>' ESTADO','munageret'=>' MUNICIPIO');
		  $la_config  = array('showHeadings'=>1, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 8, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>150),
									        'rifageret'=>array('justification'=>'left','width'=>80),
									        'dirageret'=>array('justification'=>'left','width'=>230),
									        'ciuageret'=>array('justification'=>'left','width'=>80),
									        'estageret'=>array('justification'=>'left','width'=>80),
									        'munageret'=>array('justification'=>'left','width'=>80))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 
 	      $la_data = array(array('ageret'=>' BENEFICIARIO:'));
	      $la_columna = array('ageret'=>'');
		  $la_config  = array('showHeadings'=>0, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>700))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

        $io_pdf->ezSetDy(-2);
		  $la_data    = array(array('ageret'=>$as_nomproben,'rifageret'=>$as_rifproben,'fecpag'=>$ls_fecpag,'dirageret'=>$ls_direccion,'ciuageret'=>'','estageret'=>'','munageret'=>$ls_telefono));	
	      $la_columna = array('ageret'=>' NOMBRE','rifageret'=>' R.I.F.','fecpag'=>' FECHA DE PAGO','dirageret'=>' DIRECCION','ciuageret'=>' CIUDAD','estageret'=>' ESTADO','munageret'=>' TELEFONO');
		  $la_config  = array('showHeadings'=>1, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 8, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>150),
									        'rifageret'=>array('justification'=>'center','width'=>70),
									        'fecpag'=>array('justification'=>'center','width'=>90),
									        'dirageret'=>array('justification'=>'left','width'=>220),
									        'ciuageret'=>array('justification'=>'left','width'=>45),
									        'estageret'=>array('justification'=>'left','width'=>50),
									        'munageret'=>array('justification'=>'left','width'=>75))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 




/*       $io_pdf->ezSetDy(-5);
	    $la_data    = array(array('dirageret'=>$ls_dirageret));	
	    $la_columna = array('dirageret'=>' DIRECCION FISCAL DEL AGENTE DE RETENCION');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(1,1,1),
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530,
						 'cols'=>array('dirageret'=>array('justification'=>'left','width'=>700))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

		$io_pdf->ezSetDy(-5);
	    $la_data    = array(array('nompro'=>$as_nomproben,'rifpro'=>$as_rifproben));	
	    $la_columna = array('nompro'=>' NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO','rifpro'=>'REGISTRO DE INFORMACION FISCAL DEL SUJETO RETENIDO (RIF)');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(1,1,1),
						 'shadeCol2'=>array(1,1,1), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>530, // Ancho de la tabla
						 'maxWidth'=>530,
						 'cols'=>array('nompro'=>array('justification'=>'left','width'=>350),
									   'rifpro'=>array('justification'=>'left','width'=>350))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); */
	
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: as_numsol // Número de recepción
		//	    		   as_concepto // Concepto de la solicitud
		//	    		   as_fechapago // Fecha de la recepción
		//	    		   ad_monto // monto de la recepción
		//	    		   ad_monret // monto retenido
		//	    		   ad_porcentaje // porcentaje de retención
		//	    		   as_numcon // numero de referencia
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
        $io_pdf->ezSetDy(-10);
 	      $la_data1 = array(array('ageret'=>' INFORMACION DEL IMPUESTO SOBRE LA RENTA RETENIDO Y ENTERADO'));
	      $la_columna = array('ageret'=>'');
		  $la_config  = array('showHeadings'=>0, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'center','width'=>700))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna); 
		unset($la_config); 
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$ls_titulo2="Monto sin t DerechoCred";
		$la_columna=array('fecfac'=>'<b>FECHA FACTURA</b>',
						  'numope'=>'<b>DOC, PAGO</b>',
						  'numfac'=>'<b>Nº FACTURA</b>',
  						  'numref'=>'<b>Nº CONTROL</b>',		
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>PORCENTAJE</b>',
						  'ivaret'=>'<b>IMP. RETENIDO</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 	  'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numope'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'baseimp'=>array('justification'=>'center','width'=>100),
						 			   'porimp'=>array('justification'=>'center','width'=>100),
  						 			   'ivaret'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
		//		   Access: private 
		//	    Arguments: as_numsol // Número de recepción
		//	    		   as_concepto // Concepto de la solicitud
		//	    		   as_fechapago // Fecha de la recepción
		//	    		   ad_monto // monto de la recepción
		//	    		   ad_monret // monto retenido
		//	    		   ad_porcentaje // porcentaje de retención
		//	    		   as_numcon // numero de referencia
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_columna=array('numfac'=>'<b>Nro.</b>',
						  'totalconiva'=>'<b>Monto Total Factura</b>',
						  'compsinderiva'=>'<b></b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'ivaret'=>'<b>Impuesto ISLR</b>',
						  'monded'=>'<b>Impuesto ISLR</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>455, // Orientación de la tabla
						 'cols'=>array('numfac'=>array('justification'=>'center','width'=>140), // Justificacion y ancho de la columna
   						 			   'totalconiva'=>array('justification'=>'center','width'=>90),
									   'compsinderiva'=>array('justification'=>'center','width'=>80),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>50),
  						 			   'ivaret'=>array('justification'=>'center','width'=>80),
  						 			   'monded'=>array('justification'=>'center','width'=>60))); 
		$io_pdf->ezSetDy(-15);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firma($io_pdf)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_firmas
	//		   Access: private 
	//	    Arguments: io_pdf // Instancia de objeto pdf
	//    Description: función que imprime el detalle por recepción
	//	   Creado Por: Ing. Néstor Falcón.
	// Fecha Creación: 02/11/2007. 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data1[1]=array('firma'=>'');	
		$la_data1[2]=array('firma'=>'_________________________________________________');	
		$la_data1[3]=array('firma'=>'LCDA. ALHIBIS PEREZ');	
		$la_data1[4]=array('firma'=>'GERENTE DE ADMINISTRACIÓN Y FINANZAS DEL SEMAT ');	
		$la_data1[5]=array('firma'=>'RESOLUCIÓN N° 154-2016 GACETA MUNICIPAL ORDINARIA  N° 129 DE FECHA 22/08/2016 ');	
		$la_columna=array('firma'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Mínimo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('firma'=>array('justification'=>'center','width'=>450))); 
		$io_pdf->ezSetDy(-50);
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../class_folder/class_funciones_cxp.php");
	
	$io_report    = new sigesp_cxp_class_report();
	$io_funciones = new class_funciones();				
	$io_fun_cxp   = new class_funciones_cxp();

	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE I.S.L.R.</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
    $ls_rifagente=$_SESSION["la_empresa"]["rifemp"];
    $ls_diragente=$_SESSION["la_empresa"]["direccion"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes = $io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias = $io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte  = $io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	
	global $ls_tiporeporte;
	if ($ls_tiporeporte==1)
	   {
		 require_once("sigesp_cxp_class_reportbsf.php");
		 $io_report=new sigesp_cxp_class_reportbsf();
	   }

	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_procedencias=explode('<<<',$ls_procedencias);
		$la_comprobantes=explode('<<<',$ls_comprobantes);
		$la_datos=array_unique($la_comprobantes);
		$li_totrow=count((array)$la_datos);
		sort($la_datos,SORT_STRING);
		if($li_totrow<=0)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
		    $io_pdf->ezSetCmMargins(5.3,3,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z];  
				$lb_valido=$io_report->uf_buscar_comp_islr($ls_numsol);
				$li_pos=0;
				if($lb_valido)
				{
					$li_totfac=$io_report->DS->getRowCount("numcom");
					for ($li_pos=1;($li_pos<=$li_totfac)&&($lb_valido);$li_pos++)
					{
						$ls_codigo= $io_report->DS->data["codsujret"][$li_pos];
						$ls_nombre= $io_report->DS->data["nomsujret"][$li_pos];
						$ls_direccion= $io_report->DS->data["dirsujret"][$li_pos];
						$ls_rif= $io_report->DS->data["rif"][$li_pos];
						$ls_numcom= $io_report->DS->data["numcom"][$li_pos];
						$ls_perfiscal= $io_report->DS->data["perfiscal"][$li_pos];
						$ls_fecrep  = $io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecrep"][$li_pos]);
						$ls_telefono=$io_report->uf_select_datos_proveedores($ls_rif);							
						uf_print_encabezado_pagina($ls_titulo,$ls_numcom,$ls_fecrep,$ls_perfiscal,$ls_numsol,$io_pdf);
						
						$lb_valido=$io_report->uf_buscar_dt_comp_islr($ls_numcom,$ls_numsol);
						$li_total=$io_report->ds_detalle->getRowCount("numfac");
						$li_totmonfac=0;
						$li_totmonobjret=0;
						$li_totretenido=0;
						for($li_i=1;($li_i<=$li_total);$li_i++)
						{
							$ls_numdoc	   = $io_report->ds_detalle->data["numfac"][$li_i];
							$ls_numsop	   = $io_report->ds_detalle->data["numsop"][$li_i];
							$li_monded     = $io_report->uf_datos_retencion($ls_numsol,$ls_numdoc);
							$ls_numref	   = $io_report->ds_detalle->data["numcon"][$li_i];
							$ld_fecemidoc  = $io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);
							$li_montotdoc  = $io_report->ds_detalle->data["totcmp_con_iva"][$li_i];
							$li_monobjret  = $io_report->ds_detalle->data["basimp"][$li_i];
							$li_retenido   = $io_report->ds_detalle->data["iva_ret"][$li_i];
							$li_totmonfac=$li_totmonfac+$li_montotdoc;
							$li_totmonobjret=$li_totmonobjret+$li_monobjret;
							$li_totretenido=$li_totretenido+$li_retenido;
							$li_totdersiniva="0,00";
							$li_porcentaje = number_format($io_report->ds_detalle->data["porimp"][$li_i],2,',','.');
							$li_montotdoc  = number_format($li_montotdoc,2,',','.');  
							$li_monobjret  = number_format($li_monobjret,2,',','.');    
							$li_retenido   = number_format($li_retenido,2,',','.');  
							$li_monded   = number_format($li_monded,2,',','.');  
							$arrResultado=$io_report->uf_select_datos_cheque_retencion($ls_numsop,"","","");
							$numdocpag=$arrResultado["as_nummov"];
						    $ls_fecpag=$io_report->uf_select_fechapagos($ls_numsop);
						    $ls_fecpag=$io_funciones->uf_convertirfecmostrar( $ls_fecpag);
/*							if($ls_codigo!=$ls_codigoant)
							{
								if($li_z>=1)
								{
									uf_print_firma($io_pdf);
									$io_pdf->ezNewPage();  
								}
								$ls_codigoant=$ls_codigo;
							}
*/							$la_data[$li_i]=array('numope'=>$numdocpag,'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numdoc,'numref'=>$ls_numref,
											  'totalconiva'=>$li_montotdoc,'compsinderiva'=>$li_totdersiniva,
											  'baseimp'=>$li_monobjret,'porimp'=>$li_porcentaje,'ivaret'=>$li_retenido,'monded'=>$li_monded);														
						}
						$li_totmonfac  = number_format($li_totmonfac,2,',','.');  
						$li_totmonobjret  = number_format($li_totmonobjret,2,',','.');    
						$li_totretenido   = number_format($li_totretenido,2,',','.');  
						$la_datatot[1]=array('numfac'=>"<b>TOTALES </b>",'totalconiva'=>$li_totmonfac,'compsinderiva'=>$li_totdersiniva,
										  'baseimp'=>$li_totmonobjret,'porimp'=>"",'ivaret'=>$li_totretenido,'monded'=>"");														
						uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_fecpag,$ls_direccion,$ls_telefono,$io_pdf);
						uf_print_detalle($la_data,$io_pdf);
						uf_print_total($la_datatot,$io_pdf);
						uf_print_firma($io_pdf);
						if ($li_pos<$li_totfac)
						   {
							 $io_pdf->ezNewPage();  
						   }
					}
				}
				else
				{
					print("<script language=JavaScript>");
					print(" alert('La solicitud indicada no se le ha generado el comprobante correspondiente');"); 
					print("</script>");		
				}
					//	print $li_z."  ".$li_totrow."<br>"; 
				if ($li_z<$li_totrow-1)
				   {
					 $io_pdf->ezNewPage();  
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
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 