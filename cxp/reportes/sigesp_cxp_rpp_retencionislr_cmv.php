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
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,$as_numsol,$io_pdf)
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
		$io_pdf->line(20,40,578,40);
		//$io_pdf->rectangle(20,40,558,640);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,700,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,689,10,$as_titulo2); // Agregar el título
		$io_pdf->addText(460,750,8,"Fecha de Emisión     ".date("d/m/Y")); // Agregar la Fecha 500
		//$io_pdf->addText(460,740,8,"Nro. de Comprobante  ".$as_numsol); // Agregar la Hora 506
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$io_pdf)
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
		
		$la_data[1]=array('name'=>'<b><i>Agente de Retención:</i></b>'."  ".$as_agente);
		$la_data[2]=array('name'=>'<b><i>RIF Agente de Retención:</i></b>'."  ".$_SESSION["la_empresa"]["rifemp"]);
		$la_data[3]=array('name'=>'<b><i>Direccion Fiscal:</i></b>'."  ".$_SESSION["la_empresa"]["direccion"]);
		$la_data[4]=array('name'=>'');
		$la_data[5]=array('name'=>'<b><i>Proveedor / Contribuyente:</i></b>'."  ".$as_nombre);
		$la_data[6]=array('name'=>'<b><i>RIF Proveedor:</i></b>'."  ".$as_rif);
		$la_data[7]=array('name'=>'<b><i>Direccion Fiscal:</i></b>'."  ".$as_direccion);
		$la_data[8]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_data,$io_pdf)
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
		
		$la_data[1]=array('fecha'=>'<b>Fecha Factura</b>','solicitud'=>'<b>Nro. Factura / ND</b>',
		                   'control'=>'<b>Nro. Control </b>','monto'=>'<b>Base Imponible</b>',
						   'concepto'=>'<b>Concepto de la Retención </b>',
						   'porcentaje'=>'<b>% de Retención</b>','retenido'=>'<b>Monto Retenido</b>');	
		$la_columna=array('fecha'=>'','solicitud'=>'','control'=>'','monto'=>'','concepto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 8, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>2, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>50),
									   'solicitud'=>array('justification'=>'center','width'=>60),
									   'control'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>70),
									   'concepto'=>array('justification'=>'center','width'=>150),
									   'porcentaje'=>array('justification'=>'center','width'=>50),
									   'retenido'=>array('justification'=>'center','width'=>60)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);	
	  	$la_columna=array('fecha'=>'','solicitud'=>'','control'=>'','monto'=>'','concepto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					      'fontSize' => 7, // Tamaño de Letras
					      'showLines'=>2, // Mostrar Líneas
					      'shaded'=>0, // Sombra entre líneas
					      'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
					      'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('fecha'=>array('justification'=>'center','width'=>50),
									   'solicitud'=>array('justification'=>'center','width'=>60),
									   'control'=>array('justification'=>'center','width'=>60),
									   'monto'=>array('justification'=>'center','width'=>70),
									   'concepto'=>array('justification'=>'left','width'=>150),
									   'porcentaje'=>array('justification'=>'center','width'=>50),
									   'retenido'=>array('justification'=>'center','width'=>60)));
		$io_pdf->ezTable($as_data,$la_columna,'',$la_config);	
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($total_monobjret,$total_retenido,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//	    Arguments: total_monobjret // total de la base imponible
		//	    Arguments: total_retenido // total del monto retenido
		//    Description: función que imprime los totales
		//	   Creado Por: Ing. Maryoly Caceres
		// Fecha Creación: 20/05/2014 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data[1]=array('totaluno'=>'<b>Totales</b>','monto'=>$total_monobjret,
						   'concepto'=>'','porcentaje'=>'','retenido'=>$total_retenido);
		$la_columna=array('totaluno'=>'','monto'=>'','concepto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 8, // Tamaño de Letras
					     'showLines'=>0, // Mostrar Líneas
					     'shaded'=>0, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9),
						 'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
						 'width'=>500,
				 		 'cols'=>array('totaluno'=>array('justification'=>'right','width'=>170),
									   'monto'=>array('justification'=>'center','width'=>70),
									   'concepto'=>array('justification'=>'center','width'=>150),
									   'porcentaje'=>array('justification'=>'left','width'=>50),
									   'retenido'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);	
		$la_data[1]=array('totaluno'=>'','monto'=>'','concepto'=>'<b>Total Impuesto Retenido</b>','retenido'=>$total_retenido);
		$la_columna=array('totaluno'=>'','monto'=>'','concepto'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 8, // Tamaño de Letras
					     'showLines'=>0, // Mostrar Líneas
					     'shaded'=>0, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9),
						 'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
						 'width'=>500,
				 		 'cols'=>array('totaluno'=>array('justification'=>'center','width'=>170),
									   'monto'=>array('justification'=>'center','width'=>70),
									   'concepto'=>array('justification'=>'right','width'=>200),
									   'retenido'=>array('justification'=>'center','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_firmas
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
		$la_data[2]=array('firma1'=>'','firma2'=>'');
		$la_data[3]=array('firma1'=>'','firma2'=>'');
		$la_data[4]=array('firma1'=>'____________________________','firma2'=>'____________________________');
		$la_data[5]=array('firma1'=>'Firma y Sello del Beneficiario','firma2'=>'Firma y Sello del Agente de Retencion');
		$la_data[6]=array('firma1'=>'','firma2'=>'');
		$la_columna=array('firma1'=>'','firma2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						 			   'firma2'=>array('justification'=>'center','width'=>250))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		/*$io_pdf->rectangle(450,60,110,90); 
		$io_pdf->addText(485,66,10,'<b>SELLO</b>');*/
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
	$ls_titulo="<b>COMPROBANTE DE RETENCIONES DE I.S.L.R</b>";
	$ls_titulo2="Artículo 24 Decreto 1.808, G.O N°. 36.203, del 12 de Mayo de 1997";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
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
			$io_pdf=new Cezpdf('LETTER','portrait');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(4.5,4,2,2);
			$lb_valido=true;
			$ls_codigoant="";
			$ls_data="";
			$i=0;
			$total_monobjret=0;
			$total_retenido=0;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$ls_numsol,$io_pdf);
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
						}						 
						$ls_nit=$io_report->DS->data["nit"][$li_i];
						$ls_consol=$io_report->DS->data["consol"][$li_i];
						$ls_numdoc=$io_report->DS->data["numdoc"][$li_i];
						$ls_numref=$io_report->DS->data["numref"][$li_i];
						$li_monobjret=$io_report->DS->data["monobjret"][$li_i];
						$total_monobjret+=$li_monobjret;
						$li_retenido=$io_report->DS->data["retenido"][$li_i];
						$total_retenido+=$li_retenido;
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecemidoc"][$li_i]);
						$li_montotdoc=number_format($io_report->DS->data["montotdoc"][$li_i],2,',','.');  
						$li_monobjret=number_format($li_monobjret,2,',','.');    
						$li_retenido=number_format($li_retenido,2,',','.');  
						$li_porcentaje=number_format($io_report->DS->data["porcentaje"][$li_i],2,',','.');
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();  
							}
							uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						$i++;
						$ls_data[$i]=array('fecha'=>$ld_fecemidoc,'solicitud'=>$ls_numdoc,'control'=>$ls_numref,
		                                   'monto'=>$li_monobjret,'concepto'=>$ls_consol,'porcentaje'=>$li_porcentaje,'retenido'=>$li_retenido);
					}
					uf_print_detalle($ls_data,$io_pdf);
					$total_monobjret=number_format($total_monobjret,2,',','.'); 
					$total_retenido=number_format($total_retenido,2,',','.');
					uf_print_totales($total_monobjret,$total_retenido,$io_pdf);
					$total_monobjret=0;
					$total_retenido=0;
					unset($ls_data);
					$i=0;
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
	//			print(" close();");
				print("</script>");		
			}
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 