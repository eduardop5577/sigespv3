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
		$io_pdf->line(20,40,578,40);
		$io_pdf->rectangle(20,40,558,730);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],20,680,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,"Republica Bolivariana de Venezuela");
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,9,"Republica Bolivariana de Venezuela"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(8,"Estado Carabobo");
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,8,"Estado Carabobo"); // Agregar el título
		$io_pdf->addText(460,730,8,"LOS GUAYOS : ".date("d-m-Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$as_comprobante,$io_pdf)
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
   		$ls_rifagente=$_SESSION["la_empresa"]["rifemp"];
   		$ls_periodo=$_SESSION["la_empresa"]["periodo"];
		
     	$la_data[1]=array('name'=>'<b><i>Nombre o Razón Social del Agente de Retención</i></b>','name1'=>'<b><i>RIF del Agente de Retención </i></b>','name2'=>'<b><i>Periodo Fiscal </i></b>','name3'=>'<b><i>Nro Comprobante </i></b>');	
     	$la_data[2]=array('name'=>$as_agente,'name1'=>$ls_rifagente,'name2'=>$ls_periodo,'name3'=>$as_comprobante);	

		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>220),
						 			   'name1'=>array('justification'=>'center','width'=>130),
						 			   'name2'=>array('justification'=>'center','width'=>70),
						 			   'name3'=>array('justification'=>'center','width'=>90))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b><i>Direccion Fiscal del Agente de Retención</i></b>');
		$la_data[2]=array('name'=>$as_direccion);
		$la_data[3]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>510))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
     	$la_data[1]=array('name'=>'<b><i>Nombre o Razón Social del Sujeto Retenido</i></b>','name1'=>'<b><i>RIF del Sujeto Retenido </i></b>');	
     	$la_data[2]=array('name'=>$as_nombre,'name1'=>$as_rif);	

		$la_columna=array('name'=>'','name1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>380),
						 			   'name1'=>array('justification'=>'center','width'=>130))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>510))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($as_numdoc,$as_concepto,$as_fecemidoc,$ad_monto,$ad_monret,$ad_porcentaje,$as_numref,$ai_montotdoc,$io_pdf)
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
		
		$la_data[1]=array('fecha'=>'<b>Fecha de Factura</b>','factura'=>'<b>No. de Factura</b>','control'=>'<b>No. de Control</b>','total'=>'<b>Monto Total</b>','monto'=>'<b>Base Imponible</b>',
						  'porcentaje'=>'<b>% Alicuota</b>','retenido'=>'<b>Impuesto</b>');	
		$la_columna=array('fecha'=>'','factura'=>'','control'=>'','total'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>80),
									   'factura'=>array('justification'=>'center','width'=>80),
									   'control'=>array('justification'=>'center','width'=>75),
									   'total'=>array('justification'=>'center','width'=>75),
									   'monto'=>array('justification'=>'center','width'=>75),
									   'porcentaje'=>array('justification'=>'center','width'=>55),
									   'retenido'=>array('justification'=>'center','width'=>70)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>$as_fecemidoc,'factura'=>$ad_monto,'control'=>$as_numref,'total'=>$ai_montotdoc,'monto'=>$ad_monto,'porcentaje'=>$ad_porcentaje,'retenido'=>$ad_monret);	
		$la_data[2]=array('fecha'=>'','factura'=>'','control'=>'','total'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');	
		$la_data[3]=array('fecha'=>'','factura'=>'','control'=>'','total'=>$ai_montotdoc,'monto'=>$ad_monto,'porcentaje'=>'','retenido'=>$ad_monret);	
		$la_columna=array('fecha'=>'','factura'=>'','control'=>'','total'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
					      'cols'=>array('fecha'=>array('justification'=>'center','width'=>80),
									   'factura'=>array('justification'=>'center','width'=>80),
									   'control'=>array('justification'=>'center','width'=>75),
									   'total'=>array('justification'=>'center','width'=>75),
									   'monto'=>array('justification'=>'center','width'=>75),
									   'porcentaje'=>array('justification'=>'center','width'=>55),
									   'retenido'=>array('justification'=>'center','width'=>70)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
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
		$la_data[2]=array('firma1'=>'____________________________','firma2'=>'____________________________');
		$la_data[3]=array('firma1'=>'AGENTE DE RETENCION','firma2'=>'FECHA DE ENTREGA');
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


		$io_pdf->rectangle(450,60,110,90); 
		$io_pdf->addText(485,66,10,'<b>SELLO</b>');
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
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE IMPUESTO SOBRE LA RENTA</b>";
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
			$io_pdf->ezSetCmMargins(5,4,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
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
					case "GENCMP":
						$lb_valido= $io_report->uf_buscar_comp_islr_gen($ls_numsol);
					break;
					default:
						$lb_valido= $io_report->uf_retencionesislr_cxp($ls_numsol);
					break;
				}
				if($lb_valido)
				{
					$li_total=$io_report->DS_ISLR->getRowCount("numcom");
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_codigo=$io_report->DS_ISLR->data["codsujret"][$li_i];
						$ls_nombre=$io_report->DS_ISLR->data["nomsujret"][$li_i];
						$ls_telefono="";
						$ls_direccion=$io_report->DS_ISLR->data["dirsujret"][$li_i];
						$ls_rif=$io_report->DS_ISLR->data["rif"][$li_i];

						$ls_nit=$io_report->DS_ISLR->data["nit"][$li_i];
						$ls_consol="";
						$ls_numdoc=$io_report->DS_ISLR->data["numfac"][$li_i];
						$ls_numref=$io_report->DS_ISLR->data["numcon"][$li_i];
						$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($io_report->DS_ISLR->data["fecfac"][$li_i]);
						$li_montotdoc=number_format($io_report->DS_ISLR->data["totcmp_con_iva"][$li_i],2,',','.');  
						$li_monobjret=number_format($io_report->DS_ISLR->data["basimp"][$li_i],2,',','.');    
						$li_retenido=number_format($io_report->DS_ISLR->data["iva_ret"][$li_i],2,',','.');  
						$li_porcentaje=number_format($io_report->DS_ISLR->data["porimp"][$li_i],2,',','.');
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();  
							}
							uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$ls_numsol,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$li_montotdoc,$io_pdf);
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