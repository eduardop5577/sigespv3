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
		// Fecha Creación: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesmunicipales.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
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
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_iribarren.jpg',47,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],640,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(150,530,450,60);	
		$io_pdf->addText(285,570,10,"<b>Republica Bolivariana de Venezuela</b>"); 
		$io_pdf->addText(335,560,10,"<b>Estado Lara</b>");
		$io_pdf->addText(295,550,10,"<b>Alcaldia del Municipio Iribarren</b>"); 
		$io_pdf->addText(240,540,10,"<b>Servicio Municipal de Administracion Tributaria (SEMAT)</b>"); 
		//$io_pdf->addText(712,560,8,date("d/m/Y")); // Agregar la Fecha
		//$io_pdf->addText(718,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$as_conceptosp,$ls_dirsujret,$ls_fecpag,$ls_telefono,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   as_agenteret // agente de Retención
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   as_perfiscal // Período Fiscal
		//	    		   as_licagenteret // Número de licencia de agente de retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_nomsujret // Nombre del sujeto retenido
		//	    		   as_rif // Rif del sujeto retenido
		//	    		   as_numlic // Número de Licencia del sujeto retenido
		//	    		   ai_estcmpret // Estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		  $ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
          $ls_dirageret = $_SESSION["la_empresa"]["direccion"];
          $ls_estageret = $_SESSION["la_empresa"]["estemp"];
          $ls_ciuageret = $_SESSION["la_empresa"]["ciuemp"];

		$io_pdf->ezSetDy(-4);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>NRO COMPROBANTE </b>');
		$la_data[2]=array('name'=>$as_numcon);				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>605, // Orientación de la tabla
						 'width'=>140, // Ancho de la tabla						 
						 'maxWidth'=>140,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>140))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		
        $io_pdf->ezSetDy(-5);
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
		  $la_data    = array(array('ageret'=>$as_agenteret,'rifageret'=>$ls_rifageret,'dirageret'=>$ls_dirageret,'ciuageret'=>$ls_ciuageret,'estageret'=>$ls_estageret,'munageret'=>' IRIBARREN'));	
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
									        'rifageret'=>array('justification'=>'center','width'=>80),
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
									        'rifageret'=>array('justification'=>'center','width'=>80),
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
		  $la_data    = array(array('ageret'=>$as_nomsujret,'rifageret'=>$as_rif,'fecpag'=>$ls_fecpag,'dirageret'=>$ls_dirsujret,'ciuageret'=>'','estageret'=>'','munageret'=>$ls_telefono));	
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



	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totbasimp,$ai_totmonimp,$ai_totmoniva,$as_rifagenteret,$io_pdf)
	{						 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//                 ai_totmoniva // Total monto iva
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-15);
		$la_data1[1]=array('titulo'=>'<b>INFORMACIÓN DEL IMPUESTO UNO POR MIL</b>');
		$la_data1[2]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						  'showLines'=>0, // Mostrar Líneas
						  'fontSize' => 9, // Tamaño de Letras
						  'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						  'xOrientation'=>'center', // Orientación de la tabla
						  'width'=>530, // Ancho de la tabla
						  'maxWidth'=>530,
						  'cols'=>array('titulo'=>array('justification'=>'center','width'=>530))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$la_data1[1]=array('fecfac'=>'<b>FECHA DE FACT.</b>',
						  'numero'=>'<b>DOC. PAGO</b>',
						  'numfac'=>'<b>No. FACTURA</b>',
						  'numref'=>'<b>No. CONTROL</b>',
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>PORCENTAJE</b>',
						  'iva_ret'=>'<b>IMP. RETENIDO</b>');
		$la_columna=array('fecfac'=>'<b>FECHA DE FACT.</b>',
						  'numero'=>'<b>DOC. PAGO</b>',
						  'numfac'=>'<b>No. FACTURA</b>',
						  'numref'=>'<b>No. CONTROL</b>',
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>PORCENTAJE</b>',
						  'iva_ret'=>'<b>IMP. RETENIDO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						  'showLines'=>1, // Mostrar Líneas
						  'fontSize' => 8, // Tamaño de Letras
						  'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						  'shaded'=>2, // Sombra entre líneas
						  'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						  'xOrientation'=>'center', // Orientación de la tabla
						  'width'=>530, // Ancho de la tabla
						  'maxWidth'=>530,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numero'=>array('justification'=>'center','width'=>120),
						 			   'numfac'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>120),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>80),
   						 			   'iva_ret'=>array('justification'=>'center','width'=>80))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('fecfac'=>'<b>FECHA DE FACT.</b>',
						  'numero'=>'<b>DOC. PAGO</b>',
						  'numfac'=>'<b>No. FACTURA</b>',
						  'numref'=>'<b>No. CONTROL</b>',
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>PORCENTAJE</b>',
						  'iva_ret'=>'<b>IMP. RETENIDO</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho Mínimo de la tabla
						  'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numero'=>array('justification'=>'center','width'=>120),
						 			   'numfac'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>120),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>80),
   						 			   'iva_ret'=>array('justification'=>'center','width'=>80))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);		

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
		$io_pdf->ezSetDy(-100);
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo= "COMPROBANTE DE RETENCION DE IMPUESTO DE TIMBRE FISCAL";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$la_comprobantes=explode('-',$ls_comprobantes);
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
			$io_pdf = new Cezpdf("LETTER","landscape");
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1.5,3,3);
			$lb_valido=true;
			$ls_numcomant = "";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_numcom,$ls_mes,$ls_anio);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numcon=$io_report->DS->data["numcom"][$li_i];		 								
						$ls_codret=$io_report->DS->data["codret"][$li_i];			   
						$ls_fecrep=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecrep"][$li_i]);
						$ls_perfiscal=$io_report->DS->data["perfiscal"][$li_i];						
						$ls_codsujret=$io_report->DS->data["codsujret"][$li_i];			     
						$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];	
						$ls_rif=$io_report->DS->data["rif"][$li_i];	
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];		
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];	
						$ls_numlic=$io_report->DS->data["numlic"][$li_i];	
						$ls_telefono=$io_report->uf_select_datos_proveedores($ls_rif);							
						if ($ls_numcom!=$ls_numcomant)
					   	{
						    if ($li_z>=1)
							{
								 $io_pdf->ezNewPage();  
							}
							$lb_valido=$io_report->uf_retencion1x1000_detalle_solpago($ls_numcom);
							if ($lb_valido)
							{
								
								$ls_conceptosp=$io_report->ds_detalle_solpago1x1000->data['descrip'][1];
								
							}
							
							$ls_numcomant=$ls_numcom;
					   	}
					}											
					$lb_valido=$io_report->uf_retencionesunoxmil_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_totmontoiva=0;
						$li_totmontotdoc=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$li_montotdoc=$io_report->uf_retencionesmunicipales_monfact($ls_numcon);
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];	

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
							$li_totmontoiva=$li_totmontoiva+$li_iva_ret;
							$li_iva_ret=number_format($li_iva_ret,2,",",".");	
							$li_baseimp=number_format($li_baseimp,2,",",".");
							$li_porimp=number_format($li_porimp,4,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_montotdoc=number_format($li_montotdoc,2,",",".");	
							$arrResultado=$io_report->uf_select_datos_cheque_retencion($ls_numsop,"","","");
							$numdocpag=$arrResultado["as_nummov"];
							$la_data[$li_i]=array('numero'=>$numdocpag,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,
												  'numref'=>$ls_numref,'baseimp'=>$li_baseimp,'iva_ret'=>$li_iva_ret,'porimp'=>$li_porimp,'totimp'=>$li_montotdoc,'numsop'=>$ls_numsop, );														
						  }																		 																						  
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  $li_totmontoiva= number_format($li_totmontoiva,2,",","."); 
						  $ls_fecpag=$io_report->uf_select_fechapagos($ls_numsop);
						  $ls_fecpag=$io_funciones->uf_convertirfecmostrar( $ls_fecpag);
						    uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
										  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$ls_conceptosp,$ls_dirsujret,$ls_fecpag,$ls_telefono,$io_pdf);
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$io_pdf);
						  unset($la_data);							 
						  
					}
				}
				$io_report->DS->reset_ds();
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
