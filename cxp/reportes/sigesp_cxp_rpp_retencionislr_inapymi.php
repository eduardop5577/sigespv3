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
		$io_pdf->line(50,40,960,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,510,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(910,595,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(916,585,7,date("h:i a")); // Agregar la Hora
		$io_pdf->setStrokeColor(0,0,0);
     	$io_pdf->addText(260,508,13,"<b>COMPROBANTE DE RETENCION DEL I.S.L.R</b>"); // Agregar el t?ulo
     	$io_pdf->addText(300,550,9,"<b>REPUBLICA BOLIVARIANA DE VENEZUELA</b>"); // Agregar el título
     	$io_pdf->addText(228,539,9,"<b>INSTITUTO NACIONAL DE DESARROLLO DE LA PEQUEÑA Y MEDIANA INDUSTRIA</b>"); // Agregar el título
		$io_pdf->addText(270,528,9,"<b>DIRECCION GENERAL DE ADMINISTRACION Y FINANZAS</b>"); // Agregar el título
		$io_pdf->line(45,500,750,500);
		$io_pdf->addText(45,490,9,"<b>Reglamento Parcial de la ley de Impuesto Sobre la Renta en Materia de Retenciones Dec 1808</b>"); // Agregar el t?ulo
		$io_pdf->addText(45,481,9,"<b>6.0.36.203 de 12/05/1997</b>"); // Agregar el t?ulo
		$io_pdf->addText(345,451,9,"<b>Datos del Agente de Retención</b>"); // Agregar el t?ulo
		$io_pdf->addText(345,351,9,"<b>Datos del Contribuyente</b>"); // Agregar el t?ulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

//uf_print_encabezado($as_agenteret,$as_rifagenteret,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_rif,$as_diragenteret,
//					           $as_numcon,$ad_fecrep,$ai_estcmpret,$as_tlfagenteret,$io_pdf)

	function uf_print_encabezado($ad_fecrep,$as_agente,$as_nombre,$as_rifagenteret,$as_rif,$as_telagenteret,$as_diragenteret,$as_numsol,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retenciÃ³n
		//	    		   as_diragenteret // DirecciÃ³n del agente de retención
		//	    		   as_numcon // NÃºmero de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha CreaciÃ³n: 14/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		/*if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,480,180,30);
			$io_pdf->addText(90,490,15,"<b> ANULADO </b>");
		}*/

		//---> ubicar en el datastore estos campos
		$io_pdf->ezSetY(450);
		$as_perfiscal=substr($ad_fecrep,3,2).'/'.substr($ad_fecrep,8,2);

		$io_pdf->Rectangle(500,467,120,28);
		$io_pdf->addText(505,485,9,"<b>Número de Comprobante</b>"); // Agregar el titulo
		$io_pdf->addText(660,375,9,"".$as_numsol); // Agregar el titulo
		
		
		$io_pdf->Rectangle(650,408,100,28);
		$io_pdf->addText(665,425,9,"<b>Periodo Fiscal</b>"); // Agregar el titulo
		$io_pdf->addText(653,410,9,"<b>Año:  </b>".substr($ad_fecrep,8,2)."       <b>Mes:   </b>".substr($ad_fecrep,3,2)); // Agregar el titulo

		$io_pdf->Rectangle(650,467,100,28);
		$io_pdf->addText(655,485,9,"<b>Fecha</b>"); // Agregar el titulo
		$io_pdf->addText(660,470,9,$ad_fecrep); // Agregar el titulo

		$la_data[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // TamaÃ±o de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho Mï¿½imo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b>Nombre:   </b>'.$as_agente.'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // TamaÃ±o de Letras
						 'showLines'=>1, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>350, // Orientacion de la tabla
						 'width'=>300, // Ancho de la tabla
						 'maxWidth'=>450,
						 'rowGap' => 7,
						 'yPos'=>200 ); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->Rectangle(400,408,200,27);
		$io_pdf->addText(405,420,9,"<b>No. R.I.F:   </b>".$as_rifagenteret); // Agregar el titulo
        //---------------------------------------------------------------------------------------------------
		$la_data[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // TamaÃ±o de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>Dirección:   </b>  '.$as_diragenteret);
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // TamaÃ±o de Letras
						 'showLines'=>1, // Mostrar LÃ­neas
						 'shaded'=>0, // Sombra entre lÃ­neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>606, // Orientacion de la tabla
						 'width'=>555, // Ancho de la tabla
						 'rowGap' => 7,
						 'maxWidth'=>500); // Ancho Minimo de la tabl
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>500,
						 'rowGap' => 15,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>Nombre o Razon Social:</b>  ');
		$la_data[2]=array('name'=>$as_nombre.'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>350, // Orientacion de la tabla
						 'width'=>300, // Ancho de la tabla
						 'maxWidth'=>450,
						 'rowGap' => 7,
						 'maxWidth'=>500); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->Rectangle(400,273,200,50);
		$io_pdf->addText(440,310,9,"<b>RIF. del Sujeto Retenido:</b>"); // Agregar el titulo
		$io_pdf->addText(455,284,9,$as_rif); // Agregar el titulo
	}// end function uf_print_cabecera

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totalpagado,$ai_totalconiva,$ai_totalbaseimp,$ai_totalporcentaje,$ai_totalivaret,$as_ordenp,$io_pdf)
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
		
  		$io_pdf->Rectangle(650,370,100,28);	
		$io_pdf->addText(660,388,9,"<b>ORDEN DE PAGO</b>"); // Agregar el titulo	
		$io_pdf->addText(503,470,9,$as_ordenp); // Agregar el titulo
		
		$la_data1[1]=array('titulo'=>'');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>900,
						 'rowGap' => 7,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>900))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data1[1]=array('titulo'=>'Importe Gravado');
		$la_columna=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xPos'=>440, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>100))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$la_data1[1]=array('fecfac'=>'<b>Fecha de Factura</b>',
		            	  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>N° Control</b>',
						  'monto'=>'<b>Total Facturado</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>% Alic.</b>',
						  'sustraendo'=>'<b>Sustraendo</b>',
						  'totimp'=>'<b>ISRL Retenido</b>');
		$la_columna=array('fecfac'=>'','numfac'=>'','numref'=>'','monto'=>'','baseimp'=>'','porimp'=>'','sustraendo'=>'','totimp'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>2, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xPos'=>400, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla
						 'justification'=>'center', // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>120),
						               'numfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'monto'=>array('justification'=>'center','width'=>50),
  						 			   'baseimp'=>array('justification'=>'center','width'=>100),
   						 		       'porimp'=>array('justification'=>'center','width'=>90),
									   'sustraendo'=>array('justification'=>'center','width'=>70),
   						 			   'totimp'=>array('justification'=>'center','width'=>70)));
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetY(242);
		
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$la_columna=array('fecfac'=>'',
		            	  'numfac'=>'',
						  'numref'=>'',
						  'monto'=>'',
						  'baseimp'=>'',
						  'porimp'=>'',
						  'sustraendo'=>'',
						  'totimp'=>'');

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>400, // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>120),
						               'numfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'monto'=>array('justification'=>'center','width'=>50),
  						 			   'baseimp'=>array('justification'=>'center','width'=>100),
   						 		       'porimp'=>array('justification'=>'center','width'=>90),
									   'sustraendo'=>array('justification'=>'center','width'=>70),
   						 			   'totimp'=>array('justification'=>'center','width'=>70)));

		$io_pdf->ezSetDy(-25);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('numnotdeb'=>'TOTAL','numnotcre'=>$ai_totalpagado,
		                  'name1'=>$ai_totalbaseimp,'name3'=>$ai_totalporcentaje,'name4'=>'','name5'=>$ai_totalivaret);
		$la_columna=array('numnotdeb'=>'','numnotcre'=>'',
		                  'name1'=>'','name3'=>'','name4'=>'','name5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>8,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>490,
						 'yPos'=>734,       // Orientacion de la tabla
						 'width'=>900,
						 'maxWidth'=>900,
						 'cols'=>array('numnotdeb'=>array('justification'=>'center','width'=>80),
  						 			   'numnotcre'=>array('justification'=>'center','width'=>50),
									   'name1'=>array('justification'=>'center','width'=>100), 		// Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>90), 		// Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>70),
									   'name5'=>array('justification'=>'center','width'=>70)));

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por recepción
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[0]=array('firma1'=>'','firma2'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'');
		$la_data[2]=array('firma1'=>'_______________________________','firma2'=>'____________________________________');
		$la_data[3]=array('firma1'=>'FIRMA DEL AGENTE DE RETENCION','firma2'=>'FIRMA Y SELLO DEL SUJETO RETENIDO');
		$la_data[4]=array('firma1'=>'','firma2'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>250))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION I.S.L.R.</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_telagenteret=$_SESSION["la_empresa"]["telemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias=$io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
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
			$io_pdf->ezSetCmMargins(3.5,3,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				$ls_numcom=$la_datos[$li_z];
			 	$ls_procede=$la_procedencias[$li_z];
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numcom_esp="";
				if($ls_procede=="SCBBCH")
				{
					$lb_valido=$io_report->uf_retencionesislr_scb($ls_numsol);
					$lb_valido2=$io_report->uf_buscar_comp_islr_especial($ls_numsol);
					if($lb_valido2)
					{
						$li_pos=$li_z+1;
						$ls_numcom_esp= $io_report->DS_ISLR->data["numcom"][1];
					}
				}
				else
				{
					$lb_valido=$io_report->uf_retencionesislr_cxp($ls_numsol);
					$lb_valido2=$io_report->uf_buscar_comp_islr_especial($ls_numsol);
					if($lb_valido2)
					{
						$li_pos=$li_z+1;
						$ls_numcom_esp= $io_report->DS_ISLR->data["numcom"][1];
					}
				}
				if($lb_valido)
				{
				    $li_totalconiva = 0;
					$li_totalbaseimp = 0;
					$li_totalivaret = 0;
					$li_totalporcentaje= 0;
					$li_totalpagado = 0;

					$li_total=$io_report->DS->getRowCount("numdoc");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
						if($ls_tipproben=="P")
						{
							$ls_codigo=$io_report->DS->data["cod_pro"][$li_i];
							$ls_nombre=$io_report->DS->data["proveedor"][$li_i];
					    	$ls_rif=$io_report->DS->data["rifpro"][$li_i];
					    	$ls_telefpb=$io_report->DS->data["telpro"][$li_i];
					    	$ls_dirpb=$io_report->DS->data["dirpro"][$li_i];
						}
						else
						{
							$ls_codigo=$io_report->DS->data["ced_bene"][$li_i];
							$ls_nombre=$io_report->DS->data["beneficiario"][$li_i];
							$ls_rif=$io_report->DS->data["rifben"][$li_i];
							$ls_telefpb=$io_report->DS->data["telbene"][$li_i];
							$ls_dirpb=$io_report->DS->data["dirbene"][$li_i];
						}
						$ls_numref=$io_report->DS->data["numref"][$li_i];
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$li_mondeducible=$io_report->DS->data["monded"][$li_i];
						$li_montotdoc=$io_report->DS->data["montotdoc"][$li_i];
						$li_monobjret=$io_report->DS->data["monobjret"][$li_i];
						$li_retenido=$io_report->DS->data["retenido"][$li_i];
						$li_porcentaje=$io_report->DS->data["porcentaje"][$li_i];
						$li_montotdoc=$io_report->DS->data["montotdoc"][$li_i];
						$li_moncardoc=$io_report->DS->data["moncardoc"][$li_i];
						$li_mondeddoc=$io_report->DS->data["mondeddoc"][$li_i];
						$li_totsiniva=($li_montotdoc-$li_moncardoc+$li_mondeddoc);
						$li_totconiva=($li_totsiniva+$li_moncardoc);
						$ls_numche     = $io_report->DS->data["cheque"][$li_i];
						$ls_numfac     = $io_report->DS->data["numdoc"][$li_i];
						$ls_consol 	   = $io_report->DS->data["consol"][$li_i];
						$ls_fecche=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecche"][$li_i]);
						$li_totalbaseimp=$li_totalbaseimp + $li_monobjret;

						$li_montotdoc=number_format($li_montotdoc,2,",",".");
						$li_monobjret=number_format($li_monobjret,2,',','.');
						$li_retenido=number_format($li_retenido,2,',','.');
						$li_porcentaje=number_format($li_porcentaje,2,',',',');

						$la_data[$li_i]=array('numope'=>$li_i,'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numfac,
						                      'numref'=>$ls_numref,'actsuret'=>$ls_consol,'monto'=>$li_montotdoc,'baseimp'=>$li_monobjret,
										      'porimp'=>$li_porcentaje,'sustraendo'=>$li_mondeducible,
											  'totimp'=>$li_retenido);
					}

						$li_totconiva=number_format($li_totconiva,2,',',',');
					    $li_totalconiva=$li_totalconiva + $li_totconiva;
					    $li_totalporcentaje=$li_totalporcentaje + $li_porcentaje;
						$li_totalivaret=$li_totalivaret + $li_retenido;
						$li_totalpagado = $li_totalpagado + $li_montotdoc;

					    $li_totalconiva= number_format($li_totalconiva,2,",",".");
					    $li_totalbaseimp= number_format($li_totalbaseimp,2,",",".");
  					    $li_totalporcentaje= number_format($li_totalporcentaje,2,',','.');
					    $li_totalivaret= number_format($li_totalivaret,2,",",".");
					    $li_totalpagado= number_format($li_totalpagado,2,",",".");

						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();
							}
							uf_print_encabezado($ld_fecemidoc,$ls_agente,$ls_nombre,$ls_rifagenteret,$ls_rif,$ls_telagenteret,$ls_diragenteret,$ls_numsol,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}//if
					  uf_print_detalle($la_data,$li_totalpagado,$li_totalconiva,$li_totalbaseimp,$li_totalporcentaje,$li_totalivaret,$ls_numcom_esp,$io_pdf);
				}
			  }
			}
			uf_print_firmas($io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');");
			//	print(" close();");
				print("</script>");
			}
			unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?>
