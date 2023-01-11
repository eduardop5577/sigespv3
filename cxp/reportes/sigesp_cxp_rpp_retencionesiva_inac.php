<?PHP
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
		// Fecha Creación: 14/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesiva.php",$ls_descripcion);
		return $lb_valido;
	}// end function uf_insert_seguridad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezadopagina($as_titulo,$as_numcom,$as_fecrep,$as_perfiscal,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,510,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->rectangle(40,470,700,30);
		$io_pdf->addText(100,488,9,"<b>ARTICULO 11.</b>  La Administracion Tributaria podra designar como responsables del pago de impuesto,en calidad de agente de retencion");// Agregar el título		
		$io_pdf->addText(85,475,9," por sus funciones publicas por razon de sus actividades privadas que  intervengan en operaciones gravadas con el impuesto establecido en la Ley.");// Agregar el título		
				
		$io_pdf->rectangle(420,510,320,40);
		$io_pdf->line(420,530,740,530);
		$io_pdf->line(526,510,526,550);
		$io_pdf->line(632,510,632,550);
		$io_pdf->addText(430,535,9,"<b>0. No. Comprobante</b> ");
		$io_pdf->addText(435,515,9,$as_numcom);
		$io_pdf->addText(560,535,9,"<b>1. Fecha</b> ");
		$io_pdf->addText(565,515,9,$as_fecrep);
		$io_pdf->addText(640,535,9,"<b>4. Periodo Fiscal</b> ");
		$io_pdf->addText(660,515,9,$as_perfiscal);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------	

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_agenteret,$as_rifagenteret,$as_perfiscal,$as_codsujret,$as_nomsujret,$as_rif,$as_diragenteret,
					           $as_numcon,$ad_fecrep,$ai_estcmpret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->setStrokeColor(0,0,0);
		if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,480,180,30);		
			$io_pdf->addText(90,490,15,"<b> ANULADO </b>"); 
		}	
		  
		  $ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
          $ls_dirageret = $_SESSION["la_empresa"]["direccion"];
		  
		  $la_data    = array(array('ageret'=>$as_agenteret,'rifageret'=>$ls_rifageret));	
	      $la_columna = array('ageret'=>'2. NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCION','rifageret'=>'3. REGISTRO DE INFORMACION FISCAL DEL AGENTE DE RETENCION');
		  $la_config  = array('showHeadings'=>1, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>530, // Ancho de la tabla
						      'maxWidth'=>530,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>350),
									        'rifageret'=>array('justification'=>'left','width'=>350))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

        $io_pdf->ezSetDy(-5);
	    $la_data    = array(array('dirageret'=>$ls_dirageret));	
	    $la_columna = array('dirageret'=>'5. DIRECCION FISCAL DEL AGENTE DE RETENCION');
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
	    $la_data    = array(array('nompro'=>$as_nomsujret,'rifpro'=>$as_rif));	
	    $la_columna = array('nompro'=>'6. NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO','rifpro'=>'7.REGISTRO DE INFORMACION FISCAL DEL SUJETO RETENIDO (RIF)');
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
		unset($la_config); 
	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totconiva,$ai_totsiniva,$ai_totbasimp,$ai_totmonimp,$ai_totivaret,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totconiva // Total con iva
		//	    		   ai_totsiniva // Total sin iva
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   ai_totivaret // Total iva retenido
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
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
		$ls_titulo="Compras Internas o Importaciones";
		$la_data1[1]=array('name'=>$ls_titulo);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>9,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>583, 						 					
						 'width'=>185,      // Ancho de la tabla						 
						 'maxWidth'=>185,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>195)));  // Ancho Minimo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('numope'=>'<b>Nro.</b>',
						  'fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>No. de Factura</b>',
  						  'numref'=>'<b>No.Control Fact</b>',		
						  'totalconiva'=>'<b>Monto Total Factura</b>',
						  'compsinderiva'=>'<b>Monto sin t DerechoCred</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'totimp'=>'<b>Impuesto IVA</b>',
						  'ivaret'=>'<b>IVA Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>398, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'fecfac'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
   						 			   'totalconiva'=>array('justification'=>'center','width'=>90),
									   'compsinderiva'=>array('justification'=>'center','width'=>70),
						 			   'baseimp'=>array('justification'=>'center','width'=>70),
						 			   'porimp'=>array('justification'=>'center','width'=>55),
   						 			   'totimp'=>array('justification'=>'center','width'=>70),
  						 			   'ivaret'=>array('justification'=>'center','width'=>70))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'TOTALES BS F.','name1'=>$ai_totconiva,'name2'=>$ai_totsiniva,'name3'=>$ai_totbasimp,
		                  'name4'=>'','name5'=>$ai_totmonimp,'name6'=>$ai_totivaret);						                      
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'','name6'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>8,    // Tamaño de Letras
						 'showLines'=>1,    // Mostrar Lineas
						 'shaded'=>0,       // Sombra entre Lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>176, 
						 'yPos'=>734,       // Orientacion de la tabla						
						 'width'=>200,
						 'xOrientation'=>'right',      // Ancho de la tabla						 
						 'maxWidth'=>200,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>150), // Justificacion y ancho de la columna
						               'name1'=>array('justification'=>'center','width'=>90), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
									   'name4'=>array('justification'=>'center','width'=>55), // Justificacion y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
   						 			   'name6'=>array('justification'=>'center','width'=>70)));  
		$io_pdf->ezSetDy(-15);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pagos($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pagos
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-15);
		$la_columna=array('numope'=>'<b>Nro.</b>',
						  'numdocpag'=>'<b>No. Cheque</b>',
						  'fecmov'=>'<b>Fecha de Cheque</b>',
  						  'nomban'=>'<b>Banco</b>',		
						  'montopag'=>'<b>Monto del Cheque</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>198, // Orientación de la tabla
						 'cols'=>array('numope'=>array('justification'=>'center','width'=>30), // Justificacion y ancho de la columna
						 			   'numdocpag'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
						 			   'fecmov'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
									   'nomban'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
  						 			   'montopag'=>array('justification'=>'center','width'=>70))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

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
		
		$la_data[1]=array('f1'=>'','f2'=>'','f3'=>'');
		$la_data[2]=array('f1'=>'','f2'=>'','f3'=>'');
		$la_data[3]=array('f1'=>'','f2'=>'','f3'=>'');
		$la_data[4]=array('f1'=>'_________________________________','f2'=>'_________________________________','f3'=>'_________________________________');
		$la_data[5]=array('f1'=>'UNIDAD DE TRIBUTOS INTERNOS','f2'=>'AGENTE  DE RETENCION ','f3'=>'FIRMA Y SELLO');
		$la_data[6]=array('f1'=>'INAC','f2'=>'INAC','f3'=>'RECIBIDO POR ');
		$la_columna=array('f1'=>'','f2'=>'','f3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>395, // Orientación de la tabla
						 'cols'=>array('f1'=>array('justification'=>'center','width'=>233), // Justificacion y ancho de la columna
   						 			   'f2'=>array('justification'=>'center','width'=>234),
  						 			   'f3'=>array('justification'=>'center','width'=>233))); 
		$io_pdf->ezSetDy(-15);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	
	}// end function uf_print_firmas
	//--------------------------------------------------------------------------------------------------------------------------------

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
	if($ls_tiporeporte==1)
	{
		$ls_titulo="COMPROBANTE DE RETENCION DEL IMPUESTO AL VALOR AGREGADO EN Bs.F.";	
	}
	else
	{
		$ls_titulo="COMPROBANTE DE RETENCION DEL IMPUESTO AL VALOR AGREGADO EN Bs.";	
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
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
			$io_pdf=new Cezpdf('LETTER','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(5.3,3,3,3);
			$lb_valido=true;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesiva_proveedor($ls_numcom);
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
					}											
					uf_print_encabezadopagina($ls_titulo,$ls_numcom,$ls_fecrep,$ls_perfiscal,$io_pdf); 
					uf_print_cabecera($ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_codsujret,$ls_nomsujret,$ls_rif,
					                  $ls_diragenteret,$ls_numcon,$ls_fecrep,$li_estcmpret,$io_pdf);
					$lb_valido=$io_report->uf_retencionesiva_detalle($ls_numcom);
					if($lb_valido)
					{
						$li_totalconiva = 0;
						$li_totalsiniva = 0;
						$li_totalbaseimp = 0;
						$li_totalmontoimp = 0;
						$li_totalivaret = 0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");
						$li_s=0;
						$la_datapag="";
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numope=$io_report->ds_detalle->data["numope"][$li_i];					
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$li_siniva=$io_report->ds_detalle->data["totcmp_sin_iva"][$li_i];	
							$li_coniva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];	
							$li_ivaret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$ls_numdoc=$io_report->ds_detalle->data["numdoc"][$li_i];	
							$ls_tiptrans=$io_report->ds_detalle->data["tiptrans"][$li_i];	
							$ls_numnotdeb=$io_report->ds_detalle->data["numnd"][$li_i];	
							$ls_numnotcre=$io_report->ds_detalle->data["numnc"][$li_i];									
							$ls_nomban=$io_report->ds_detalle->data["nomban"][$li_i];									
							$ls_fecmov=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecmov"][$li_i]);	
							$ls_montopag=$io_report->ds_detalle->data["montopag"][$li_i];									
							$ls_numdocpag=$io_report->ds_detalle->data["numdocpag"][$li_i];									
							$li_monto=$li_baseimp + $li_totimp;  
							$li_totdersiniva= abs($li_coniva - $li_monto);
							$ls_numfacafec="";
							$li_totalconiva=$li_totalconiva + $li_coniva;	
							$li_totalsiniva=$li_totalsiniva + $li_totdersiniva;
							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;	
							$li_totalivaret=$li_totalivaret + $li_ivaret;								
							$li_totdersiniva=number_format($li_totdersiniva,2,",","."); 
							$li_siniva=number_format($li_siniva,2,",","."); 
							$li_coniva=number_format($li_coniva,2,",",".");			
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,2,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_ivaret=number_format($li_ivaret,2,",",".");															
							$ls_montopag=number_format($ls_montopag,2,",",".");															
							$la_data[$li_i]=array('numope'=>$ls_numope,'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,'numref'=>$ls_numref,
												  'numnotdeb'=>$ls_numnotdeb,'numnotcre'=>$ls_numnotcre,'tiptrans'=>$ls_tiptrans,
												  'numfacafec'=>$ls_numfacafec,'totalconiva'=>$li_coniva,'compsinderiva'=>$li_totdersiniva,
												  'baseimp'=>$li_baseimp,'porimp'=>$li_porimp,'totimp'=>$li_totimp,
												  'ivaret'=>$li_ivaret,'numdoc'=>$ls_numdoc,'totalsiniva'=>$li_siniva);		
							if($ls_numdocpag!="")
							{												
						 		$li_s++;
								$la_datapag[$li_s]=array('numope'=>$li_s,'numdocpag'=>$ls_numdocpag,'fecmov'=>$ls_fecmov,'nomban'=>$ls_nomban,'montopag'=>$ls_montopag);		
							}
						  }																		 																						  
						  $li_totalconiva= number_format($li_totalconiva,2,",","."); 
						  $li_totalsiniva= number_format($li_totalsiniva,2,",","."); 
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  $li_totalivaret= number_format($li_totalivaret,2,",","."); 
						  uf_print_detalle($la_data,$li_totalconiva,$li_totalsiniva,$li_totalbaseimp,$li_totalmontoimp,
						  				   $li_totalivaret,$io_pdf); 
						  if($la_datapag!="")
						  {	
						  	uf_print_pagos($la_datapag,$io_pdf);					 						  
							unset($la_datapag);							 
						  }
						  unset($la_data);							 
					}
				}
				if($li_z<($li_totrow-1))
				{
					$io_pdf->ezNewPage(); 					  
				}		
			}
			uf_print_firma($io_pdf);
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