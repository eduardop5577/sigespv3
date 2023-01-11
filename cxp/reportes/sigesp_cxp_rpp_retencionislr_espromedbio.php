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
	function uf_print_encabezado_pagina($as_titulo,$as_numcom,$as_perfiscal,$io_pdf)
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
		
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha=new class_fecha();
		$as_anio=substr($as_perfiscal,0,4);
		$as_mes=substr($as_perfiscal,4,2);
		$ld_fechadesde="01/".$as_mes."/".$as_anio;
		$ld_fechahasta=substr($io_fecha->uf_last_day($as_mes,$as_anio),0,2)."/".$as_mes."/".$as_anio;

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->rectangle(20,40,558,640);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,650,11,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(460,720,10,"Numero de Comprobante"); // Agregar la Hora
		$io_pdf->rectangle(460,685,115,30); 
		$io_pdf->addText(480,700,10,$as_numcom); // Agregar la Hora
		$io_pdf->addText(480,700,10,$as_numcom); // Agregar la Hora
		$io_pdf->addText(480,700,10,$as_numcom); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_direccion,$io_pdf)
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
		$la_data[1]=array('name'=>'<b><i>Agente de Retención:</i></b>'."  ".$as_agente);
		$la_data[2]=array('name'=>'<b><i>Nombre o Razón Social:</i></b>'."  ".$as_nombre);
		$la_data[3]=array('name'=>'<b><i>RIF:</i></b>'."  ".$as_rif."                                                        <b><i>NIT:</i></b>  ".$as_nit);
		$la_data[4]=array('name'=>'<b><i>Direccion:</i></b>'."  ".$as_direccion);
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
	function uf_print_detalle($as_numsol,$as_concepto,$as_fechapago,$ad_monto,$ad_monret,$ad_porcentaje,$as_numcon,$as_codded,$as_desserded,$io_pdf)
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
		
     	$la_data[1]=array('solicitud'=>'<b><i>Factura:</i></b>'."  ".$as_numsol,'control'=>'<b><i>Nro Control: </i></b>'.$as_numcon);	
		$la_columna=array('solicitud'=>'','control'=>'');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
					     'fontSize' => 10,  // Tamaño de Letras
					     'showLines'=>0,    // Mostrar Líneas
					     'shaded'=>0,       // Sombra entre líneas
					     'width'=>530,     // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('solicitud'=>array('justification'=>'left','width'=>250),
						 			   'control'=>array('justification'=>'left','width'=>250))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		       
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>'<b>Fecha de Pago</b>','monto'=>'<b>Monto Objeto de Retención</b>',
						  'porcentaje'=>'<b>% Aplicado</b>','retenido'=>'<b>Total Impuesto Retenido</b>');	
		$la_columna=array('fecha'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					     'fontSize' => 10, // Tamaño de Letras
					     'showLines'=>2, // Mostrar Líneas
					     'shaded'=>2, // Sombra entre líneas
					     'shadeCol'=>array(0.9,0.9,0.9),
					     'shadeCol2'=>array(0.9,0.9,0.9),
					     'xOrientation'=>'center', // Orientación de la tabla
					     'colGap'=>1,
					     'width'=>500,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>100),
									   'monto'=>array('justification'=>'center','width'=>150),
									   'porcentaje'=>array('justification'=>'center','width'=>100),
									   'retenido'=>array('justification'=>'center','width'=>150)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>$as_fechapago,'monto'=>$ad_monto,'porcentaje'=>$ad_porcentaje,'retenido'=>$ad_monret);	
	  	$la_columna=array('fecha'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
					      'fontSize' => 10, // Tamaño de Letras
					      'showLines'=>2, // Mostrar Líneas
					      'shaded'=>0, // Sombra entre líneas
					      'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
					      'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('fecha'=>array('justification'=>'center','width'=>100),
						                'monto'=>array('justification'=>'right','width'=>150),
										'porcentaje'=>array('justification'=>'center','width'=>100),
										'retenido'=>array('justification'=>'right','width'=>150)));
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
		$la_data[3]=array('firma1'=>'AGENTE DE RETENCION','firma2'=>'BENEFICIARIOS');
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
			$io_pdf->ezSetCmMargins(7,4,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			$la_deduccion="";
			$ls_codded="";
			$ls_desserded="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z];  
				$lb_valido=$io_report->uf_buscar_comp_islr($ls_numsol);
				$li_pos=0;
				if($lb_valido)
				{
					$li_pos=$li_pos+1;
					$ls_codigo= $io_report->DS->data["codsujret"][$li_pos];
					$ls_nombre= $io_report->DS->data["nomsujret"][$li_pos];
					$ls_rif= $io_report->DS->data["rif"][$li_pos];
					$ls_nit= $io_report->DS->data["nit"][$li_pos];
					$ls_dirsujret= $io_report->DS->data["dirsujret"][$li_pos];
					$ls_numcom= $io_report->DS->data["numcom"][$li_pos];
					$ls_perfiscal= $io_report->DS->data["perfiscal"][$li_pos];
					$ls_fecrep  = $io_funciones->uf_convertirfecmostrar($io_report->DS->data["fecfac"][$li_pos]);
					uf_print_encabezado_pagina($ls_titulo,$ls_numcom,$ls_perfiscal,$io_pdf);
					uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_dirsujret,$io_pdf);
				
					$lb_valido=$io_report->uf_buscar_dt_comp_islr($ls_numcom,$ls_numsol);
					$li_total=$io_report->ds_detalle->getRowCount("numfac");
					$li_totmonfac=0;
					$li_totmonobjret=0;
					$li_totretenido=0;
					$la_datosded=0;
					$ls_codded="";
					$ls_desserded="";
					for($li_i=1;($li_i<=$li_total);$li_i++)
					{
						$ls_numdoc	   = $io_report->ds_detalle->data["numfac"][$li_i];
						$la_datosded     = $io_report->uf_datos_deduccion($ls_numsol,$ls_numdoc);
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
						if($la_datosded!="")
						{
							$ls_codded=$la_datosded["codded"];
							$ls_desserded=$la_datosded["desserded"];
						}
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firma($io_pdf);
								$io_pdf->ezNewPage();  
							}
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($ls_numdoc,"",$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$ls_codded,$ls_desserded,$io_pdf);
/*						$la_data[$li_i]=array('numope'=>"1",'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numdoc,'numref'=>$ls_numref,
										  'totalconiva'=>$li_montotdoc,'compsinderiva'=>$li_totdersiniva,
										  'baseimp'=>$li_monobjret,'porimp'=>$li_porcentaje,'ivaret'=>$li_retenido,'monded'=>$li_monded);														
*/					}
				
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
				print(" alert('No existe informacion para Generar el Reporte. Debe ser Generado el Comprobante');"); 
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