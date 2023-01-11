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
//		$io_pdf->rectangle(20,40,558,640);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(100,680,7,"SERVICIO DESCONCENTRADO ONCOLOGICO"); // Agregar el título
		$io_pdf->addText(100,670,7,"REPUBLICA BOLIVARIANA DE VENEZUELA"); // Agregar el título
		$io_pdf->addText(100,660,7,"GOBERNACION DEL ESTADO LARA"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nomsujret,$as_rif,$as_nit,$as_direccion,$io_pdf)
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
		
		$io_pdf->ezSetY(630);
		$la_data[1]=array('name1'=>'<b>CONSTANCIA DE RENTENCION DEL IMPUESTO MUNICIPAL SOBRE LA ACTIVIDAD ECONOMICA</b>');
	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 12, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('name1'=>'<b>PROVEEDOR: '.$as_nomsujret.' </b>');
		$la_data[2]=array('name1'=>'                                                                                     <b>RIF: '.$as_rif.' </b>');
	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('name1'=>array('justification'=>'left','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('name1'=>'');
		$la_data[2]=array('name1'=>'Por los conceptos que se detallan a continuación:');
		$la_data[3]=array('name1'=>'');
	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($ai_monto,$ai_cantidad,$ai_porcentaje,$as_tiporet, $ai_totalret, $as_numche,$ad_fecha,$as_numfac,$as_fecfac,$ai_totcmp_con_iva,$io_pdf)
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
		
		$la_data1[1]=array('c1'=>'<b>FACTURA N°</b>','c2'=>'<b>FECHA</b>','c3'=>'<b>O.C. ó  S.N°</b>','c4'=>'<b>MONTO</b>');
		$la_columna=array('c1'=>'','c2'=>'','c3'=>'','c4'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
									   'c2'=>array('justification'=>'center','width'=>100),
						 			   'c3'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'c4'=>array('justification'=>'center','width'=>120))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data1[1]=array('c1'=>$as_numfac,'c2'=>$as_fecfac,'c3'=>"",'c4'=>$ai_totcmp_con_iva);
		$la_data1[2]=array('c1'=>"",'c2'=>"",'c3'=>"",'c4'=>"");
		$la_data1[3]=array('c1'=>"",'c2'=>"",'c3'=>"",'c4'=>"");
		$la_data1[4]=array('c1'=>"",'c2'=>"",'c3'=>"",'c4'=>"");
		$la_data1[5]=array('c1'=>"",'c2'=>"",'c3'=>"",'c4'=>"");
		$la_columna=array('c1'=>'','c2'=>'','c3'=>'','c4'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
									   'c2'=>array('justification'=>'center','width'=>100),
						 			   'c3'=>array('justification'=>'center','width'=>160), // Justificacion y ancho de la columna
						 			   'c4'=>array('justification'=>'right','width'=>120))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_data1[1]=array('c1'=>'<b>Monto Facturado</b>','c2'=>$ai_totcmp_con_iva);
		$la_data1[2]=array('c1'=>'<b>Descuento Otorgado por la Empresa</b>','c2'=>"");
		$la_data1[3]=array('c1'=>'<b>Monto Total</b>','c2'=>$ai_totcmp_con_iva);
		$la_columna=array('c1'=>'','c2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'right','width'=>380), // Justificacion y ancho de la columna
						 			   'c2'=>array('justification'=>'right','width'=>120))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_data[1]=array('name1'=>'<b>IMPUESTO RETENIDO</b>');
	
        $la_columna=array('name1'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>500))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data1[1]=array('c1'=>'<b>FECHA DEL PAGO/ABONO EN CUENTA</b>',
						  'c2'=>'<b>TOTAL CANTIDAD PAGADA O ABONADA EN CUENTA Bs.</b>',
						  'c3'=>'<b>BASE IMPONIBLE</b>',
						  'c4'=>'<b>PORCENTAJE DE RETENCION</b>',
  						  'c5'=>'<b>IMPUESTO RETENIDO</b>');
		$la_columna=array('c1'=>'','c2'=>'','c3'=>'','c4'=>'','c5'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 7, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
									   'c2'=>array('justification'=>'center','width'=>120),
						 			   'c3'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
						 			   'c4'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
									   'c5'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		
		$la_data1[1]=array('c1'=>$ad_fecha,'c2'=>$ai_monto,'c3'=>$ai_cantidad,'c4'=>$ai_porcentaje,'c5'=>$ai_totalret);
		$la_columna=array('c1'=>'','c2'=>'','c3'=>'','c4'=>'','c5'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>1, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'center','width'=>60), // Justificacion y ancho de la columna
									   'c2'=>array('justification'=>'center','width'=>120),
						 			   'c3'=>array('justification'=>'center','width'=>120), // Justificacion y ancho de la columna
						 			   'c4'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
									   'c5'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_data1[1]=array('c1'=>'<b>Total Retenido</b>','c2'=>$ai_totalret);
		$la_data1[2]=array('c1'=>'<b>Monto Factura</b>','c2'=>$ai_totcmp_con_iva);
		$la_data1[3]=array('c1'=>'<b>Neto a Pagar</b>','c2'=>$ai_monto);
		$la_columna=array('c1'=>'','c2'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 9, // Tamaño de Letras
						  'showLines'=>2, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>500,
						 'cols'=>array('c1'=>array('justification'=>'right','width'=>400), // Justificacion y ancho de la columna
						 			   'c2'=>array('justification'=>'right','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($as_agente,$io_pdf)
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
		
		$io_pdf->ezSetY(260);
		
		
		$la_data[0]=array('firma1'=>'','firma2'=>'');
		$la_data[1]=array('firma1'=>'','firma2'=>'');
		$la_data[2]=array('firma1'=>'<b>Lcda. Lesbia Sanchez</b>','firma2'=>'<b>Dra. Gloria A. Soler</b>');
		$la_data[3]=array('firma1'=>'Func. Enc. Retencion del Impuesto Municipal','firma2'=>'Firma y Sello Agente de Retención');
		$la_data[4]=array('firma1'=>'','firma2'=>'');
		$la_data[5]=array('firma1'=>'SELLO:                                          ','firma2'=>'RECIBIDO POR:                               ');
		$la_data[6]=array('firma1'=>'','firma2'=>'C.I.:                                                    ');
		$la_columna=array('firma1'=>'','firma2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('firma1'=>array('justification'=>'left','width'=>250), // Justificación y ancho de la columna
						 			   'firma2'=>array('justification'=>'left','width'=>250))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
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
	
	$ls_titulo="<b>RETENCION 1 X 1.000</b>";
	
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
			$io_pdf=new Cezpdf('LETTER','portrait');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(7,4,3,3);
			$lb_valido=true;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesmunicipales_proveedor($ls_numcom,$ls_mes,$ls_anio);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numcon=$io_report->DS->data["numcom"][$li_i];		 								
						$ls_codret=$io_report->DS->data["codret"][$li_i];			   
						$ls_nomsujret=$io_report->DS->data["nomsujret"][$li_i];	
						$ls_rif=$io_report->DS->data["rif"][$li_i];	
						$ls_nit=$io_report->DS->data["nit"][$li_i];	
						$ls_dirsujret=$io_report->DS->data["dirsujret"][$li_i];		
						$li_estcmpret=$io_report->DS->data["estcmpret"][$li_i];	
						$ls_numlic=$io_report->DS->data["numlic"][$li_i];									
					}											
					uf_print_encabezado($ls_agente,$ls_nomsujret,$ls_rif,$ls_nit,$ls_dirsujret,$io_pdf);
					$lb_valido=$io_report->uf_retencionesmunicipales_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;($li_i<=$li_total)&&($lb_valido);$li_i++)
						{
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];					
							$ls_fecfac=$io_report->ds_detalle->data["fecfac"][$li_i];					
							$li_totcmp_con_iva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];					
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];	
							$li_porimp='RETENCION 1 X 1.000';	
							$li_porcentaje=$io_report->ds_detalle->data["porimp"][$li_i];;
							$li_totimp=$io_report->ds_detalle->data["iva_ret"][$li_i];	

							$ls_nummov="";
							$ld_fecmov="";
							$li_monto="";
							$arrResultado=$io_report->uf_select_datos_cheque_retencion($ls_numsop,$ls_nummov,$ld_fecmov,$li_monto);
							$lb_valido=$arrResultado["lb_valido"];
							$ls_nummov=$arrResultado["as_nummov"];
							$ld_fecmov=$arrResultado["ad_fecmov"];
							$li_monto=$arrResultado["as_monto"];
							unset($arrResultado);
							
							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_monto=number_format($li_monto,2,",",".");		
							$li_totcmp_con_iva=number_format($li_totcmp_con_iva,2,",",".");		
							$ld_fecmov=$io_funciones->uf_convertirfecmostrar($ld_fecmov);
							$ls_fecfac=$io_funciones->uf_convertirfecmostrar($ls_fecfac);
																					
						  }																		 																						  						  if($lb_valido) // Si no ocurrio ningún error
						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  uf_print_detalle($li_monto,$li_baseimp,$li_porcentaje,$li_porimp, $li_totimp, $ls_nummov,
										   $ld_fecmov,$ls_numfac,$ls_fecfac,$li_totcmp_con_iva,$io_pdf);
						  uf_print_firmas($ls_agente,$io_pdf);
						  							 
					}
				}
				$io_report->DS->reset_ds();
				if($li_z<($li_totrow-1))
				{
					$io_pdf->ezNewPage(); 					  
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