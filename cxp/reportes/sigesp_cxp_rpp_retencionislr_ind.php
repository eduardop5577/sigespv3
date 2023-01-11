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
	function uf_print_encabezado_pagina($as_titulo,$ls_rif_agente,$ls_tit_fecha,$ls_mar,$io_pdf)
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
		$io_pdf->line(20,40,700,40);
		$io_pdf->setStrokeColor(0,0,0);		
		$io_pdf->addText(310,580,11,'<b><i>INSTITUTO NACIONAL DE DEPORTES</i></b>'); // Agregar el título
		$io_pdf->addText(300,568,11,'<b><i>RETENCION IMPUESTO SOBRE LA RENTA</i></b>'); // Agregar el título
		$io_pdf->addText(345,556,11,'<b><i>PERSONAS JURIDICAS</i></b>'); // Agregar el título
		$io_pdf->addText($ls_mar,544,11,$ls_tit_fecha); // Agregar el título
		//$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],80,520,60,60); // Agregar Logo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($io_pdf)
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
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();	
		$io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(60,477,670,$io_pdf->getFontHeight(27));
		$ls_corrlativo[1]=array('name'=>'<b><i>DATOS GENERALES</i></b>','name2'=>'<b><i>RETENCIONES ISLR</i></b>','name3'=>'---');	
		$la_columna=array('name'=>'','name2'=>'','name3'=>'');	
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 7, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>0, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
					     'cols'=>array('name'=>array('justification'=>'center','width'=>454),
						 			   'name2'=>array('justification'=>'center','width'=>167),
									   'name3'=>array('justification'=>'center','width'=>50)));
		$io_pdf->ezTable($ls_corrlativo,$la_columna,'',$la_config);
		
		$la_data[1]=array('nro'=>'<b>N°</b>',
						  'fecha'=>'<b>Fecha Reten.</b>',
		                  'orden'=>'<b>N° Orden</b>',
						  'nombre'=>'<b>Beneficiario</b>',		                  
						  'rif'=>'<b>Rif N° </b>',
						  'monfac'=>'<b>Monto Factura </b>',
		                  'monto'=>'<b>Base Imponible</b>',	
						  'porcentaje'=>'<b>% ISLR</b>',
						  'tipor'=>'<b>Tipo de Reten</b>',						 
						  'monret'=>'<b>ISLR Retenido</b>',
						  'netpag'=>'<b><i>Neto a Pagar</i></b>');	
		$la_columna=array('nro'=>'',
						  'fecha'=>'',
		                  'orden'=>'',
						  'nombre'=>'',
						  'rif'=>'',
						  'monfac'=>'',
		                  'monto'=>'',
						  'porcentaje'=>'',	
						  'tipor'=>'',					  
						  'monret'=>'',
						  'netpag'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 7, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>0, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
					     'cols'=>array('nro'=>array('justification'=>'center','width'=>20),
						 			   'fecha'=>array('justification'=>'center','width'=>40),
						               'nombre'=>array('justification'=>'center','width'=>205),
						  			   'rif'=>array('justification'=>'center','width'=>60),
									   'monfac'=>array('justification'=>'center','width'=>60),								   
									   'monto'=>array('justification'=>'center','width'=>58),
									   'porcentaje'=>array('justification'=>'center','width'=>29),
									   'tipor'=>array('justification'=>'center','width'=>30),
									   'monret'=>array('justification'=>'center','width'=>50),
									   'orden'=>array('justification'=>'center','width'=>68.75),
									   'netpag'=>array('justification'=>'center','width'=>50)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');		
	}// end function uf_print_encabezado
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_numdoc,$as_concepto,$as_fechapago,$ad_monto,$ad_monret,$ad_porcentaje,$as_numcon,
	                          $la_montotdoc,$ls_numsol, $ls_correlativo, $ls_nombre, $ls_rif, $li_montofactura, $li_montotdoc, $li_nro, $io_pdf)
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
		
     	$la_data[1]=array('nro'=>$li_nro,
						  'fecha'=>$as_fechapago,
		                  'orden'=>$ls_numsol,
						  'nombre'=>$ls_nombre,
						  'rif'=>$ls_rif,
						  'monfac'=>$li_montofactura,
		                  'monto'=>$ad_monto,
						  'porcentaje'=>$ad_porcentaje,
						  'tipor'=>'',
						  'monret'=>$ad_monret,
						  'netpag'=>$li_montotdoc);	
	  	$la_columna=array('nro'=>'',
						  'fecha'=>'',
		                  'orden'=>'',
						  'nombre'=>'',
						  'rif'=>'',
						  'monfac'=>'',
		                  'monto'=>'',
						  'porcentaje'=>'',
						  'tipor'=>'',						  
						  'monret'=>'',
						  'netpag'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					      'fontSize' => 7, // Tamaño de Letras
					      'showLines'=>2, // Mostrar Líneas
					      'shaded'=>0, // Sombra entre líneas
					      'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
					      'rowGap'=>8,
						  'colGap'=>1,
						  'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						  'cols'=>array('nro'=>array('justification'=>'center','width'=>20),
						  			   'fecha'=>array('justification'=>'center','width'=>40),
						               'nombre'=>array('justification'=>'center','width'=>205),
						  			   'rif'=>array('justification'=>'center','width'=>60),							   
									   'monfac'=>array('justification'=>'center','width'=>60),	
									   'monto'=>array('justification'=>'center','width'=>58),
									   'porcentaje'=>array('justification'=>'center','width'=>29),
									   'tipor'=>array('justification'=>'center','width'=>30),
									   'monret'=>array('justification'=>'center','width'=>50),
									   'orden'=>array('justification'=>'center','width'=>68.75),
									   'netpag'=>array('justification'=>'center','width'=>50)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_sello($ai_totmonobjret,$ai_totretenido,$io_pdf)
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
		
	    $la_data1[1]=array('total'=>'<b>TOTAL   </b>',
		                   'monto'=>'<b>'.$ai_totmonobjret.'</b>',
		                   'iva'=>'',
						   'imponible'=>'<b>'.$ai_totretenido.'</b>',
						   'obs'=>'');
		$la_columna=array('total'=>'',
		                  'monto'=>'',
		                  'iva'=>'',
						  'imponible'=>'',
						  'obs'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'xOrientation'=>'center', // Orientación de la tabla
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>454), // Justificacion y ancho de la columna
   						 			   'monto'=>array('justification'=>'center','width'=>58),
									   'iva'=>array('justification'=>'center','width'=>59),
									   'imponible'=>array('justification'=>'center','width'=>50),
									   'obs'=>array('justification'=>'center','width'=>50))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE IMPUESTO SOBRE LA RENTA</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	$ls_rif_agente=$_SESSION["la_empresa"]["rifemp"]; 
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias=$io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	$ld_fechades=$io_fun_cxp->uf_obtenervalor_get("fechadesde","");
	$ld_fechahas=$io_fun_cxp->uf_obtenervalor_get("fechahasta","");
	$ld_mesdes=substr($ld_fechades,3,2);
	$ld_meshas=substr($ld_fechahas,3,2);
	$ld_aniodes=substr($ld_fechades,6,4);
	$ld_aniohas=substr($ld_fechahas,6,4);
	if ($ld_mesdes=='01')
	{$ls_mes_texto_d='ENERO';}
	elseif ($ld_mesdes=='02')
	{$ls_mes_texto_d='FEBRERO';}
	elseif ($ld_mesdes=='03')
	{$ls_mes_texto_d='MARZO';}
	elseif ($ld_mesdes=='04')
	{$ls_mes_texto_d='ABRIL';}
	elseif ($ld_mesdes=='05')
	{$ls_mes_texto_d='MAYO';}
	elseif ($ld_mesdes=='06')
	{$ls_mes_texto_d='JUNIO';}
	elseif ($ld_mesdes=='07')
	{$ls_mes_texto_d='JULIO';}
	elseif ($ld_mesdes=='08')
	{$ls_mes_texto_d='AGOSTO';}
	elseif ($ld_mesdes=='09')
	{$ls_mes_texto_d='SEPTIEMBRE';}
	elseif ($ld_mesdes=='10')
	{$ls_mes_texto_d='OCTUBRE';}
	elseif ($ld_mesdes=='11')
	{$ls_mes_texto_d='NOVIEMBRE';}
	elseif ($ld_mesdes=='12')
	{$ls_mes_texto_d='DICIEMBRE';}
	
	if ($ld_meshas=='01')
	{$ls_mes_texto_h='ENERO';}
	elseif ($ld_meshas=='02')
	{$ls_mes_texto_h='FEBRERO';}
	elseif ($ld_meshas=='03')
	{$ls_mes_texto_h='MARZO';}
	elseif ($ld_meshas=='04')
	{$ls_mes_texto_h='ABRIL';}
	elseif ($ld_meshas=='05')
	{$ls_mes_texto_h='MAYO';}
	elseif ($ld_meshas=='06')
	{$ls_mes_texto_h='JUNIO';}
	elseif ($ld_meshas=='07')
	{$ls_mes_texto_h='JULIO';}
	elseif ($ld_meshas=='08')
	{$ls_mes_texto_h='AGOSTO';}
	elseif ($ld_meshas=='09')
	{$ls_mes_texto_h='SEPTIEMBRE';}
	elseif ($ld_meshas=='10')
	{$ls_mes_texto_h='OCTUBRE';}
	elseif ($ld_meshas=='11')
	{$ls_mes_texto_h='NOVIEMBRE';}
	elseif ($ld_meshas=='12')
	{$ls_mes_texto_h='DICIEMBRE';}
	$ls_tit_fecha="";
	$ls_mar=0;
	if (($ld_mesdes==$ld_meshas)&&($ld_aniodes==$ld_aniohas))
	{
		$ls_mar=330;
		$ls_tit_fecha="<b><i> Mes de </i></b>"."<b><i>".$ls_mes_texto_d."</i></b>"."<b><i> del </i></b>"."<b><i>".$ld_aniodes."</i></b>"; 
	}
	else
	{
		$ls_mar=300;
		$ls_tit_fecha="<b><i> Meses </i></b>"."<b><i>".$ls_mes_texto_d." - ".$ls_mes_texto_h."</i></b>"."<b><i> del </i></b>"."<b><i>".$ld_aniodes."</i></b>"; 
	}
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
			$io_pdf->ezSetCmMargins(3.6,2.5,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			uf_print_encabezado_pagina($ls_titulo,$ls_rif_agente,$ls_tit_fecha,$ls_mar,$io_pdf);
			uf_print_encabezado($io_pdf);
			$li_nro=0;
			$li_totmonobjret=0;
			$li_totretenido=0;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z];  
				switch ($ls_procede)
				{
					case "SCBBCH":
						$lb_valido= $io_report->uf_retencionesislr_scb($ls_numsol);  
					break;
					case "INT":
						$lb_valido= $io_report->uf_retencionesislr_int($ls_numsol);
					break;
					default:
						$lb_valido= $io_report->uf_retencionesislr_cxp($ls_numsol);
					break;
				}
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numdoc");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_codpro=$io_report->DS->data["cod_pro"][$li_i];
						$ls_cedbene=$io_report->DS->data["ced_bene"][$li_i];
						if($ls_codpro!="----------")
						{
							$ls_tipproben="P";
						}
						else
						{
							$ls_tipproben="B";
						}
						if($ls_tipproben=="P")
						{
							$ls_codigo=$io_report->DS->data["cod_pro"][$li_i];
							$ls_nombre=$io_report->DS->data["proveedor"][$li_i];
							$ls_telefono=$io_report->DS->data["telpro"][$li_i];
							$ls_direccion=$io_report->DS->data["dirpro"][$li_i];
							$ls_rif=$io_report->DS->data["rifpro"][$li_i];
						}
						else
						{
							$ls_codigo=$io_report->DS->data["ced_bene"][$li_i];
							$ls_nombre=$io_report->DS->data["beneficiario"][$li_i];
							$ls_telefono=$io_report->DS->data["telbene"][$li_i];
							$ls_direccion=$io_report->DS->data["dirbene"][$li_i];
							$ls_rif=$io_report->DS->data["rifben"][$li_i];
							$ls_numsol=$io_report->DS->data["numsol"][$li_i];
						}						 
						$ls_nit=$io_report->DS->data["nit"][$li_i];
						$ls_consol=$io_report->DS->data["consol"][$li_i];
						$ls_numdoc=$io_report->DS->data["numdoc"][$li_i];// numero de la orden de pago
						$ls_numref=$io_report->DS->data["numref"][$li_i];
						$li_totmonobjret=$li_totmonobjret+$io_report->DS->data["monobjret"][$li_i];
						$li_totretenido=$li_totretenido+$io_report->DS->data["retenido"][$li_i];
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$li_montotdoc=number_format($io_report->DS->data["montotdoc"][$li_i],2,',','.');
						$li_mondeddoc=number_format($io_report->DS->data["mondeddoc"][$li_i],2,',','.');  //
						$li_monobjret=number_format($io_report->DS->data["monobjret"][$li_i],2,',','.');    
						$li_retenido=number_format($io_report->DS->data["retenido"][$li_i],2,',','.');  
						$li_porcentaje=number_format($io_report->DS->data["porcentaje"][$li_i],2,',','.');
						$ls_correlativo=$io_report->DS->data["numcmpislr"][$li_i];						
						$li_montofactura=$io_report->DS->data["montotdoc"][$li_i]+$io_report->DS->data["mondeddoc"][$li_i];
						$li_montofactura=number_format($li_montofactura,2,',','.');
						/*if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								$io_pdf->ezNewPage();  
							}
							
						}*/
						$li_nro++;
						uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,
						                 $li_porcentaje,$ls_numref,"", $ls_numsol, $ls_correlativo,$ls_nombre,$ls_rif,$li_montofactura,$li_montotdoc,$li_nro,$io_pdf);
					}
				}
			}
			//totales
			$li_totmonobjret=number_format($li_totmonobjret,2,',','.');  
			$li_totretenido=number_format($li_totretenido,2,',','.');
			uf_print_sello($li_totmonobjret,$li_totretenido,$io_pdf);  
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