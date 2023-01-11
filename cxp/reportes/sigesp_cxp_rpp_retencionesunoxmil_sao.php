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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesunoxmil.php",$ls_descripcion);
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
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,540,12,$as_titulo); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_dirsujret,$ai_estcmpret,$as_conceptosp,$io_pdf)
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
		
		$ls_telemp=$_SESSION["la_empresa"]["telemp"]." / ".$_SESSION["la_empresa"]["faxemp"];

		$ls_contribuyente= substr($as_rif,0,1);
		switch ($ls_contribuyente)
		{
			case "O":
				$ls_contribuyente="ORDINARIO";
			break;
			case "J":
				$ls_contribuyente="JURIDICO";
			break;
			case "F":
				$ls_contribuyente="FORMAL";
			break;
			default:
				$ls_contribuyente="NATURAL";
			break;
		}				 
		
		$io_pdf->addText(550,560,9,"N° DE COMPROBANTE:        ".$as_numcon); // Agregar el título
		$io_pdf->ezSetDy(-4);
	 	if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	
/*		$io_pdf->ezSetY(525);
		$la_data[1]=array('name'=>'<b>NRO COMPROBANTE </b>');
		$la_data[2]=array('name'=>$as_numcon);				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>500, // Orientación de la tabla
						 'width'=>140, // Ancho de la tabla						 
						 'maxWidth'=>140,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>140))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);	*/	
		$io_pdf->ezSetY(525);
		$la_data[1]=array('name1'=>'<b>DATOS DEL AGENTE DE RETENCIÓN:</b>','name2'=>'<b>DATOS DEL SUJETO RETENIDO:</b>');
	
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>370),
						                'name2'=>array('justification'=>'left','width'=>370))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		
		unset($la_data);
		$la_data[1]=array('name1'=>'<b>PERSONA:</b> GUBERNAMENTAL','name2'=>'<b>PERSONA: </b>'.$ls_contribuyente);
		$la_data[2]=array('name1'=>'<b>NOMBRE O RAZON SOCIAL:</b>  '.$as_agenteret,'name2'=>'<b>Razón:</b>   '.$as_nomsujret);
		$la_data[3]=array('name1'=>'<b>RIF:</b>  '.$as_rifagenteret,'name2'=>'<b>RIF:</b>   '.$as_rif);
		$la_data[4]=array('name1'=>'<b>DIRECCION:</b>  '.$as_diragenteret,'name2'=>'<b>DIRECCION:</b>   '.$as_dirsujret);
		$la_data[5]=array('name1'=>'<b>TELEFONO:</b>  '.$ls_telemp,'name2'=>'<b>TELEFONO:</b>   ');
	
        $la_columna=array('name1'=>'','name2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>370),
						                'name2'=>array('justification'=>'left','width'=>370))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);	
		
		$io_pdf->ezSetDy(-10);		
		$la_data[1]=array('name1'=>'<b>INFORMACION DEL IMPUESTO RETENIDO POR ENTERAR</b>');
	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>740))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$io_pdf->ezSetDy(-10);		
		$la_data[1]=array('name'=>'<b>CONCEPTO: </b>'.$as_conceptosp);
        $la_columna=array('name'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name'=>array('justification'=>'left','width'=>740))); // Ancho Máximo de la tabla
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
		
		$la_data1[1]=array('numero'=>'<b>ORDEN DE PAGO Nº</b>',
						  'fecfac'=>'<b>FECHA DE LA FACTURA</b>',
						  'numfac'=>'<b>Nº DE LA FACTURA</b>',
  						  'numref'=>'<b>MONTO TOTAL FACTURADO</b>',		
						  'baseimp'=>'<b>MONTO BASE DE LA OPERACIÓN</b>',
						  'porimp'=>'<b>FACTOR DE CALCULO  (1X1000)</b>',
						  'iva_ret'=>'<b>IMPUESTO RETENIDO</b>',
						  'numsop'=>'<b>FECHA DE LA RETENCION</b>');
		$la_columna=array('numero'=>'<b>Nº</b>',
						  'fecfac'=>'<b>Fecha</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numref'=>'<b>Numero de Control</b>',		
						  'baseimp'=>'<b>Monto de la Operación</b>',
						  'porimp'=>'<b>Alícuota</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>',
						  'numsop'=>'<b>Orden de Pago</b>');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>100),
						 			   'fecfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>110), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>110), // Justificacion y ancho de la columna
						 			   'baseimp'=>array('justification'=>'center','width'=>100),
						 			   'porimp'=>array('justification'=>'center','width'=>80),
   						 			   'iva_ret'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'numsop'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('numero'=>'<b>Nº</b>',
						  'fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
						  'baseimp'=>'<b>Monto de la Operación</b>',
						  'porimp'=>'<b>Alícuota</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>',
						  'numsop'=>'<b>Nº Orden de Pago</b>');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						 'cols'=>array('numero'=>array('justification'=>'center','width'=>100),
						 			   'fecfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>110), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>110), // Justificacion y ancho de la columna
						 			   'baseimp'=>array('justification'=>'right','width'=>100),
						 			   'porimp'=>array('justification'=>'right','width'=>80),
   						 			   'iva_ret'=>array('justification'=>'right','width'=>80), // Justificacion y ancho de la columna
									   'numsop'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);		
		$la_data1[1]=array('numero'=>'<b>TOTALES</b>',
						  'iva_ret'=>$ai_totmoniva,
						  'numsop'=>'');
		$la_columna=array('numero'=>'',
						  'iva_ret'=>'',
						  'numsop'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						 'cols'=>array('numero'=>array('justification'=>'left','width'=>560),
   						 			   'iva_ret'=>array('justification'=>'right','width'=>80), // Justificacion y ancho de la columna
									   'numsop'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);

	}// end function uf_print_detalle

	function uf_print_sello($io_pdf,$as_nomsujret)
	{
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_sello
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Jennifer Rivero
		//     Modificado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 13/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-5);
	    $la_data[1]=array('name1'=>'<b>ELABORADO POR:</b>','name2'=>'<b>REVISADO POR:</b>','name3'=>'<b>APROBADO POR: </b>','name4'=>'<b>RECIBE POR: </b>');	
	    $la_data[2]=array('name1'=>'<b>Lcda. Lesbia Sanchez</b>','name2'=>'<b>Lcdo. Luis E. Rodriguez</b>','name3'=>'<b>Dra. Gloria A. Soler </b>','name4'=>$as_nomsujret);	
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>690,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>185),						                
										'name2'=>array('justification'=>'left','width'=>185),
										'name3'=>array('justification'=>'left','width'=>185),
										'name4'=>array('justification'=>'left','width'=>185))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 		
		 
	    $la_data[1]=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_data[2]=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_data[3]=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_data[4]=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');	
		$la_data[5]=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');		
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'','name4'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados					  
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>530,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>185),						                
										'name2'=>array('justification'=>'center','width'=>185),
										'name3'=>array('justification'=>'center','width'=>185),
										'name4'=>array('justification'=>'center','width'=>185))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha	  = new class_fecha();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo= "COMPROBANTE DE RETENCION SOBRE IMPUESTO 1*1000";
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
							
						    uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
										  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_dirsujret,$li_estcmpret,$ls_conceptosp,$io_pdf);
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
							$li_mes= substr($ld_fecfac,3,2);
							$ls_mes= $io_fecha->uf_load_nombre_mes($li_mes);
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$li_totcmp_con_iva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	
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
							$li_totcmp_con_iva=number_format($li_totcmp_con_iva,2,",",".");							
							$li_montotdoc=number_format($li_montotdoc,2,",",".");							
							$la_data[$li_i]=array('numero'=>$ls_numsop,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,
												  'numref'=>$li_totcmp_con_iva,'baseimp'=>$li_baseimp,'iva_ret'=>$li_iva_ret,'porimp'=>"1000",'numsop'=>$ls_mes, );														
						  }																		 																						  
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  $li_totmontoiva= number_format($li_totmontoiva,2,",","."); 
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$io_pdf);
						  uf_print_sello($io_pdf,$ls_nomsujret);
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
				//print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 