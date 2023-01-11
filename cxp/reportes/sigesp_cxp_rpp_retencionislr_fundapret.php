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
		//$io_pdf->addJpegFromFile('../../shared/imagebank/logo_mh.jpg',47,552,800,50); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/fundapret.jpg',36,552,300,57); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
     	$io_pdf->addText(90,545,9,"<b>MINISTERIO DEL PODER POPULAR PARA</b>");// Agregar el título
		$io_pdf->addText(90,535,9,"<b>RELACIONES INTERIORES Y JUSTICIA</b>");// Agregar el título
		$io_pdf->addText(140,525,9,"<b>FUNDAPRET</b>");// Agregar el título
		$io_pdf->addText(350,510,10,"<b> COMPROBANTE DE RETENCION I.S.L.R. </b>"); // Agregar el t?ulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

//uf_print_encabezado($as_agenteret,$as_rifagenteret,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_rif,$as_diragenteret,
//					           $as_numcon,$ad_fecrep,$ai_estcmpret,$as_tlfagenteret,$io_pdf)

	function uf_print_encabezado($ad_fecrep,$as_agente,$as_nombre,$as_rifagenteret,$as_rif,$as_telagenteret,$as_diragenteret,$ls_numcom,$io_pdf)
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
		$io_pdf->ezSetY(500);
		$io_pdf->Rectangle(645,560,100,28);
		$io_pdf->addText(675,576,9,"<b>FECHA</b>"); // Agregar el titulo
		$io_pdf->addText(670,566,9,date("d/m/Y")); // Agregar el titulo
		
		$io_pdf->Rectangle(525,530,150,28);
		$io_pdf->addText(530,546,10,"<b>NUMERO DE COMPROBANTE</b>"); // Agregar el titulo
		$io_pdf->addText(550,533,10,$ls_numcom); // Agregar el titulo
		
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
		$la_data[1]=array('name'=>'<b>NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCION </b>');
		$la_data[2]=array('name'=>$as_agente.'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // TamaÃ±o de Letras
						 'showLines'=>1, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>450, // Orientacion de la tabla
						 'width'=>400, // Ancho de la tabla
						 'maxWidth'=>500,
						 'yPos'=>200 ); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->Rectangle(450,446,300,39);
		$io_pdf->addText(452,475,9,"<b>REGISTRO DE INFORMACION FISCAL DEL AGENTE DE RETENCION</b>"); // Agregar el titulo
		$io_pdf->addText(453,460,9,$as_rifagenteret); // Agregar el tï¿½ulo
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
		$la_data[1]=array('name'=>'<b>DIRECCION FISCAL DEL AGENTE DE RETENCION</b>  ');
		$la_data[2]=array('name'=>$as_diragenteret);
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // TamaÃ±o de Letras
						 'showLines'=>1, // Mostrar LÃ­neas
						 'shaded'=>0, // Sombra entre lÃ­neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>450, // Orientacion de la tabla
						 'width'=>400, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho Minimo de la tabl
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->Rectangle(450,404,300,28);
		$io_pdf->addText(452,422,9,"<b>TELEFONO</b>"); // Agregar el titulo
		$io_pdf->addText(453,406,9,$as_telagenteret); // Agregar el titulo
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
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>500))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO</b>  ');
		$la_data[2]=array('name'=>$as_nombre.'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'left', // Orientacion de la tabla
						 'xPos'=>450, // Orientacion de la tabla
						 'width'=>400, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->Rectangle(450,361,300,29);
		$io_pdf->addText(450.3,381,9,"<b>REGISTRO DE INFORMACION FISCAL DEL SUJETO RETENIDO (R.I.F)</b>"); // Agregar el titulo
		$io_pdf->addText(452,364,9,$as_rif); // Agregar el titulo
	}// end function uf_print_cabecera

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totalpagado,$ai_totalconiva,$ai_totalbaseimp,$ai_totalporcentaje,$ai_totalivaret,$io_pdf)
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
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>900))); // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

		$ls_titulo1="Total Compras Incluyendo el IVA";
		$la_columna=array('numope'=>'<b>OPER.NRO</b>',
	                      'fecfac'=>'<b>FECHA DE LA FACTURA</b>',
		            	  'numfac'=>'<b>NRO. DE LA FACTURA</b>',
						  'numref'=>'<b>NRO. CONTROL DE FACTURA</b>',
						  'numnotdeb'=>'<b>NRO. NOTA DEBITO</b>',
						  'tiptransc'=>'<b>TIPO DE TRANSACC.</b>',
						  'numref2'=>'<b>NRO. DE FACTURA AFECTADA</b>',
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>ALICUOTA</b>',
						  'totimp'=>'<b>ISLR RETENIDO</b>');

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>400, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>60),
						               'fecfac'=>array('justification'=>'center','width'=>120),
						               'numfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numref'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'numnotdeb'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
  						 			   'tiptransc'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
									   'numref2'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'baseimp'=>array('justification'=>'center','width'=>55),
   						 		       'porimp'=>array('justification'=>'center','width'=>60),
   						 			   'totimp'=>array('justification'=>'center','width'=>60)));

		$io_pdf->ezSetDy(-15);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecche'=>'','fecfac'=>'','numfac'=>'','numnotdeb1'=>'','numnotdeb'=>'TOTAL','name6'=>'','name4'=>'',
		                  'name1'=>$ai_totalbaseimp,'name3'=>'','name4'=>'','name5'=>$ai_totalivaret);
		$la_columna=array('fecche'=>'','fecfac'=>'','numfac'=>'','numnotdeb1'=>'','numnotdeb'=>'','name6'=>'','name4'=>'',
		                  'name1'=>'','name3'=>'','name5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>8,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>400,
						 'yPos'=>734,       // Orientacion de la tabla
						 'width'=>900,
						 'maxWidth'=>900,
						 'cols'=>array('fecche'=>array('justification'=>'center','width'=>60),
						 			   'fecfac'=>array('justification'=>'center','width'=>120), 	// Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>60), 		// Justificacion y ancho de la columna
									   'numnotdeb1'=>array('justification'=>'center','width'=>80),
									   'numnotdeb'=>array('justification'=>'center','width'=>60),
									   'name6'=>array('justification'=>'center','width'=>60), 		// Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>80), 		// Justificacion y ancho de la columna
									   'name1'=>array('justification'=>'center','width'=>55), 		// Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>60), 		// Justificacion y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>60)));

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
		
		$io_pdf->ezSetY(145);
		$la_data[0]=array('firma1'=>'ELABORADO POR:','firma2'=>'REVISADO POR:','firma3'=>'RECIBIDO POR:');
		$la_data[1]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[2]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_data[3]=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'','firma3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xPos'=>280, // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>150),
									   'firma3'=>array('justification'=>'center','width'=>150),)); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->addText(60,60,7,"Retención según articulo 27 Ley de ISLR (Paragrafo 31-41 y 48,64,65 y 77)"); // Agregar el titulo
		$io_pdf->addText(60,50,7,"Decreto 1808 Gaceta Oficial 36.203 12/05/1997"); // Agregar el titulo
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
			$ls_numsolant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numsol=$la_datos[$li_z];
				$ls_numcom=$la_datos[$li_z];
			 	$ls_procede=$la_procedencias[$li_z];
				if($ls_procede=="SCBBCH")
				{
					$lb_valido=$io_report->uf_retencionesislr_scb($ls_numsol);
				}
				else
				{
					$lb_valido=$io_report->uf_retencionesislr_cxp($ls_numsol);
				}
				if($lb_valido)
				{
				    $li_totalconiva = 0;
					$li_totalbaseimp = 0;
					$li_totalivaret = 0;
					$li_totalporcentaje= 0;
					$li_totalpagado = 0;

					$li_total=$io_report->DS->getRowCount("numdoc");
					//print $li_total."<br>";
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
						$li_totalivaret=$li_totalivaret + $li_retenido;
						$li_montotdoc=number_format($li_montotdoc,2,",",".");
						$li_monobjret=number_format($li_monobjret,2,',','.');
						$li_retenido=number_format($li_retenido,2,',','.');
						$li_porcentaje=number_format($li_porcentaje,2,',',',');

						$la_data[$li_i]=array('numope'=>$li_i,'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numfac,
						                      'numref'=>$ls_numref,'actsuret'=>$ls_consol,'monto'=>$li_montotdoc,'baseimp'=>$li_monobjret,
										      'porimp'=>$li_porcentaje,'sustraendo'=>$li_mondeducible,
											  'totimp'=>$li_retenido,'numnotdeb'=>'','tiptransc'=>'C','numref2'=>$ls_numref);
					}

						$li_totconiva=number_format($li_totconiva,2,',',',');
					    $li_totalconiva=$li_totalconiva + $li_totconiva;
					    $li_totalporcentaje=$li_totalporcentaje + $li_porcentaje;
						$li_totalpagado = $li_totalpagado + $li_montotdoc;

					    $li_totalconiva= number_format($li_totalconiva,2,",",".");
					    $li_totalbaseimp= number_format($li_totalbaseimp,2,",",".");
  					    $li_totalporcentaje= number_format($li_totalporcentaje,2,',','.');
					    $li_totalivaret= number_format($li_totalivaret,2,",",".");
					    $li_totalpagado= number_format($li_totalpagado,2,",",".");

						if(($ls_codigo!=$ls_codigoant)||($ls_numsol!=$ls_numsolant))
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();
							}
							uf_print_encabezado($ld_fecemidoc,$ls_agente,$ls_nombre,$ls_rifagenteret,$ls_rif,$ls_telagenteret,$ls_diragenteret,$ls_numcom,$io_pdf);
							$ls_codigoant=$ls_codigo;
							$ls_numsolant=$ls_numsol;
						}//if
					  uf_print_detalle($la_data,$li_totalpagado,$li_totalconiva,$li_totalbaseimp,$li_totalporcentaje,$li_totalivaret,$io_pdf);
					  unset($la_data);
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
