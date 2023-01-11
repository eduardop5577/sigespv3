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
	function uf_print_encabezado_pagina($as_titulo,$as_numdoc,$ld_fecemidoc,$io_pdf)
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
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],870,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_mri.jpg',47,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(380,563,13,"<b>COMPROBANTE DE RETENCIÓN PARA</b>"); // Agregar el tiulo				
		$io_pdf->addText(390,548,13,"<b>EL IMPUESTO SOBRE LA RENTA</b>"); // Agregar el tiulo			
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nomproben,$as_rifproben,$as_nitproben,$as_condoc,$as_numdoc,$ld_fecemidoc,$ls_direccion,$io_pdf)
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
		
		  $ls_y=substr($ld_fecemidoc,6,4);
		  $ls_m=substr($ld_fecemidoc,3,2);
		  $ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
          $ls_dirageret = $_SESSION["la_empresa"]["direccion"];
		  $io_pdf->ezSetY(518);
		  $la_data[1]=array('name'=>'<b>NRO. DE COMPROBANTE </b>','name2'=>'<b>FECHA</b>','name3'=>'<b>PERIODO FISCAL</b>');
		  $la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		  $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200),
						 			   'name2'=>array('justification'=>'center','width'=>170),
									   'name3'=>array('justification'=>'center','width'=>170))); // Ancho  de la tabla
		  $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		  unset($la_data);
		  unset($la_columna);
		  unset($la_config);	
		
		  $la_data[1]=array('name'=>$as_numdoc,'name2'=>$ld_fecemidoc,'name3'=>$ls_y);
		  $la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		  $la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>200),
						 			   'name2'=>array('justification'=>'center','width'=>170),
									   'name3'=>array('justification'=>'center','width'=>170))); // Ancho Minimo de la tabla
		
		  $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		  unset($la_data);
		  unset($la_columna);
		  unset($la_config);
		  
		$io_pdf->ezSetY(478);
		$io_pdf->Rectangle(48.6,420,900,56);
		$io_pdf->Rectangle(48.6,350,900,56);
		$io_pdf->Rectangle(49,61,901,57);		
		$io_pdf->line(499,61,499,110);
		$io_pdf->addText(50,54,7,"ELABORADO POR OFICINA DE PLANIFICACION"); // Agregar el tulo	
		$io_pdf->addText(50,47,7,"PRESUPUESTO, ORGANIZACION Y SISTEMAS."); // Agregar el tulo
		$la_data[1]=array('name'=>'<b>NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCIÓN</b>','name2'=>'<b>R.I.F AGENTE DE RETENCIÓN</b>','name3'=>'<b>TIPO DE AGENTE DE RETENCIÓN</b>');
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>500),
						 			   'name2'=>array('justification'=>'center','width'=>200),
									   'name3'=>array('justification'=>'center','width'=>200),)); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		if (substr($ls_rifageret,0,1)=="J")
		{
			$ls_tipo="Natural";
		}
		else
		{
			$ls_tipo="Juridico";
		}
		$la_data[1]=array('name'=>$as_agente,'name2'=>$ls_rifageret,'name3'=>$ls_tipo);
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>500),
						 			   'name2'=>array('justification'=>'center','width'=>200),
									   'name3'=>array('justification'=>'center','width'=>200))); // Ancho Mimo de la tabla
		
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data[1]=array('name'=>'<b>DIRECCIÓN FISCAL DEL AGENTE DE RETENCIÓN</b>');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>900))); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('name'=>$ls_dirageret);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>900))); // Ancho Mimo de la tabla
		
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);  


		$io_pdf->ezSetY(408);
		$la_data[1]=array('name'=>'<b>NOMBRE O RAZON SOCIAL DEL CONTRIBUYENTE </b>','name2'=>'<b>R.I.F DEL CONTRIBUYENTE</b>','name3'=>'<b>TIPO DE AGENTE DE CONTRIBUYENTE</b>');
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>500),
						 			   'name2'=>array('justification'=>'center','width'=>200),
									   'name3'=>array('justification'=>'center','width'=>200),)); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		if (substr($as_rifproben,0,1)=="J")
		{
			$ls_tipo2="Natural";
		}
		else
		{
			$ls_tipo2="Juridico";
		}
		$la_data[1]=array('name'=>$as_nomproben,'name2'=>$as_rifproben,'name3'=>$ls_tipo2);
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>500),
						 			   'name2'=>array('justification'=>'center','width'=>200),
									   'name3'=>array('justification'=>'center','width'=>200))); // Ancho Mimo de la tabla
		
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('name'=>'<b>DIRECCIÓN FISCAL DEL CONTRIBUYENTE</b>');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>900))); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('name'=>$ls_direccion);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>0, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>900))); // Ancho Mimo de la tabla
		
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetY(120);
		$la_data[1]=array('name'=>'<b>FIRMA DEL AGENTE DE RETENCIÓN (SAIME)</b>','name2'=>'<b>FIRMA DEL CONTRIBUYENTE</b>');
		$la_columna=array('name'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xOrientation'=>'center', // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>450),
						 			   'name2'=>array('justification'=>'center','width'=>450))); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$li_retenido,$io_pdf)
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
		
		$io_pdf->ezSetY(318);
		$la_dataw[1]=array('name'=>'');
		$la_columnaw=array('name'=>'');		
		$la_configw=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Letras
						 'shaded'=>2, // Sombra entre lineas
						 'xPos'=>500, // Orientacion de la tabla
						 'width'=>500, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>896))); // Ancho Mimo de la tabla
		$io_pdf->ezTable($la_dataw,$la_columnaw,'',$la_configw);
		unset($la_dataw);
		unset($la_columnaw);
		unset($la_configw);

		
		$io_pdf->ezSetY(318);
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$la_columna=array('numfac'=>'<b>Nº FACTURA</b>',
						  'fecfac'=>'<b>FECHA</b>',
						  'totrep'=>'<b>MONTO TOTAL (BS.)</b>',
						  'baseimp'=>'<b>BASE IMPONIBLE</b>',
						  'porimp'=>'<b>% RETENCIÓN</b>',
						  'ivaret'=>'<b>MONTO RETENIDO (BS.)</b>',
						  'totalconiva'=>'<b>NETO A PAGAR (BS.)</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>500, // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>128), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>128), // Justificacion y ancho de la columna
									   'totrep'=>array('justification'=>'center','width'=>128), // Justificacion y ancho de la columna
   						 			   'totalconiva'=>array('justification'=>'center','width'=>128),
						 			   'baseimp'=>array('justification'=>'center','width'=>128),
						 			   'porimp'=>array('justification'=>'center','width'=>128),
  						 			   'ivaret'=>array('justification'=>'center','width'=>128))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$io_pdf->ezSetDy(-2);
		$la_data[1]=array('name4'=>'Total Bs.','name6'=>$li_retenido);						                      
		$la_columna=array('name4'=>'','name6'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>8,    // Tamaño de Letras
						 'showLines'=>0,    // Mostrar Lineas
						 'shaded'=>2,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>565, 
						 'yPos'=>734,       // Orientacion de la tabla						
						 'width'=>200,
						 'xOrientation'=>'right',      // Ancho de la tabla						 
						 'maxWidth'=>200,
						 'cols'=>array('name4'=>array('justification'=>'center','width'=>128), // Justificacion y ancho de la columna
   						 			   'name6'=>array('justification'=>'center','width'=>128)));  
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../class_folder/class_funciones_cxp.php");
	
	$io_report    = new sigesp_cxp_class_report();
	$io_funciones = new class_funciones();				
	$io_fun_cxp   = new class_funciones_cxp();

	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE I.S.L.Ra.</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
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
			$io_pdf=new Cezpdf('LEGAL','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
		     $io_pdf->ezSetCmMargins(5.3,3,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
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
					$li_total=$io_report->DS->getRowCount("numdoc");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
						if($ls_tipproben=="P")
						{
							$ls_codigo    = $io_report->DS->data["cod_pro"][$li_i];
							$ls_nombre    = $io_report->DS->data["proveedor"][$li_i];
							$ls_telefono  = $io_report->DS->data["telpro"][$li_i];
							$ls_direccion = $io_report->DS->data["dirpro"][$li_i];
							$ls_rif		  = $io_report->DS->data["rifpro"][$li_i];
						}
						else
						{
							$ls_codigo	  = $io_report->DS->data["ced_bene"][$li_i];
							$ls_nombre	  = $io_report->DS->data["beneficiario"][$li_i];
							$ls_telefono  = $io_report->DS->data["telbene"][$li_i];
							$ls_direccion = $io_report->DS->data["dirbene"][$li_i];
							$ls_rif		  = $io_report->DS->data["rifben"][$li_i];
						}						 
						$ls_nit		   = $io_report->DS->data["nit"][$li_i];
						$ls_consol	   = $io_report->DS->data["consol"][$li_i];
						$ls_numdoc	   = $io_report->DS->data["numdoc"][$li_i];
						$ls_numref	   = $io_report->DS->data["numref"][$li_i];
						$ld_fecemidoc  = $io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$li_montotdoc  = $io_report->DS->data["montotdoc"][$li_i];
						$li_monobjret  = $io_report->DS->data["monobjret"][$li_i];
						$li_mondeddoc  = $io_report->DS->data["mondeddoc"][$li_i];
						$li_retenido   = $io_report->DS->data["retenido"][$li_i];
						$li_moncardoc   = $io_report->DS->data["moncardoc"][$li_i];
						$li_totdersiniva="0,00";
						$li_totalrep   = $li_montotdoc + $li_mondeddoc;
						
						$li_porcentaje = number_format($io_report->DS->data["porcentaje"][$li_i],2,',','.');
						$li_montotdoc  = number_format($li_montotdoc,2,',','.');  
						$li_monobjret  = number_format($li_monobjret,2,',','.');    
						$li_retenido   = number_format($li_retenido,2,',','.');
						$li_totalrep  = number_format($li_totalrep,2,',','.');  
						if($ls_codigo!=$ls_codigoant)
						{
							/*if($li_z>=1)
							{
								uf_print_firma($io_pdf);
								$io_pdf->ezNewPage();  
							}*/
							$ls_codigoant=$ls_codigo;
						}
						$la_data[1]=array('numope'=>"1",'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numdoc,'numref'=>$ls_numref,
										  'totalconiva'=>$li_montotdoc,'compsinderiva'=>$li_totdersiniva,
										  'baseimp'=>$li_monobjret,'porimp'=>$li_porcentaje,'ivaret'=>$li_retenido,'totrep'=>$li_totalrep);														
						$la_datatot[1]=array('numfac'=>"<b>TOTALES BS F.</b>",'totalconiva'=>$li_montotdoc,'compsinderiva'=>$li_totdersiniva,
										  'baseimp'=>$li_monobjret,'porimp'=>"",'ivaret'=>$li_retenido);														
						uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecemidoc,$io_pdf);
						uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_consol,$ls_numsol,$ld_fecemidoc,$ls_direccion,$io_pdf);
						uf_print_detalle($la_data,$li_retenido,$io_pdf);
						//uf_print_total($la_datatot,$io_pdf);
						if (($li_z<$li_total)&&($li_totrow>1))
					 	{
							$io_pdf->ezNewPage();  
					  	}
					}
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