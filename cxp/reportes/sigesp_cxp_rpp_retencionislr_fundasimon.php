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
	function uf_print_encabezado_pagina($as_titulo,$ls_numsol,$ld_fecreg,$io_pdf)
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
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],47,520,60,60); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(48,520,702,60);	
		$io_pdf->line(600,520,600,580);
		$io_pdf->line(600,550,750,550);
		$io_pdf->addText(130,545,14,"<b>".$as_titulo."</b>"); // Agregar el tulo				
		$io_pdf->addText(630,570,10,"<b>Nro De Comprobante</b>"); // Agregar el tulo				
		$io_pdf->addText(640,555,10,$ls_numsol); // Agregar el tulo				
		$io_pdf->addText(660,540,10,"<b>Fecha</b>"); // Agregar el tulo				
		$io_pdf->addText(650,525,10,$ld_fecreg); // Agregar el tulo				
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nombre,$as_rif,$as_nit,$as_telefono,$as_direccion,$as_comprobante,$ls_perfiscal,$io_pdf)
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
   		$ls_diragente=$_SESSION["la_empresa"]["direccion"];
		
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'Agente de Retencion','name1'=>'No. de R.I.F. Agente de Retencion','name2'=>'Periodo Fiscal');
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>700, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>363), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>210), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>130))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>$as_agente,'name1'=>$ls_rifagente,'name2'=>$ls_perfiscal);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>700, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>363), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>210), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>130))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>Direccion del Agente de Retencion: </b>'.$ls_diragente);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>703, // Ancho de la tabla						 
						 'maxWidth'=>725); // Ancho Minimo de la tabl
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>Nombre o Razon Social del Sujeto Retenido </b>');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>703, // Ancho de la tabla						 
						 'maxWidth'=>725); // Ancho Minimo de la tabl
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'<b>NOMBRE: </b>'.$as_nombre);
		$la_data[2]=array('name'=>'<b>No. DE R.I.F.: </b>'.$as_rif);
		$la_data[3]=array('name'=>'<b>DIRECCION: </b>'.$as_direccion);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>703, // Ancho de la tabla						 
						 'maxWidth'=>725); // Ancho Minimo de la tabl
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------

		$la_data[1]=array('name'=>'');
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>703, // Ancho de la tabla
						 'maxWidth'=>510,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>703))); // Ancho Máximo de la tabla
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
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>510, // Ancho de la tabla
						 'maxWidth'=>510,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>100),
									   'factura'=>array('justification'=>'center','width'=>120),
									   'control'=>array('justification'=>'center','width'=>120),
									   'total'=>array('justification'=>'center','width'=>109),
									   'monto'=>array('justification'=>'center','width'=>109),
									   'porcentaje'=>array('justification'=>'center','width'=>65),
									   'retenido'=>array('justification'=>'center','width'=>80)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('fecha'=>$as_fecemidoc,'factura'=>$as_numdoc,'control'=>$as_numref,'total'=>$ai_montotdoc,'monto'=>$ad_monto,'porcentaje'=>$ad_porcentaje,'retenido'=>$ad_monret);	
		$la_data[2]=array('fecha'=>'','factura'=>'','control'=>'','total'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');	
		$la_data[3]=array('fecha'=>'','factura'=>'','control'=>'','total'=>$ai_montotdoc,'monto'=>$ad_monto,'porcentaje'=>'','retenido'=>$ad_monret);	
		$la_columna=array('fecha'=>'','factura'=>'','control'=>'','total'=>'','monto'=>'','porcentaje'=>'','retenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
					     'fontSize' => 8,  // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510, // Ancho de la tabla
						 'xPos'=>405, // Orientacion de la tabla
						 'maxWidth'=>510,
					     'cols'=>array('fecha'=>array('justification'=>'center','width'=>100),
									   'factura'=>array('justification'=>'center','width'=>120),
									   'control'=>array('justification'=>'center','width'=>120),
									   'total'=>array('justification'=>'center','width'=>109),
									   'monto'=>array('justification'=>'center','width'=>109),
									   'porcentaje'=>array('justification'=>'center','width'=>65),
									   'retenido'=>array('justification'=>'center','width'=>80)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($ls_numsol,$ls_fecsol,$ls_numche,$ls_fecche,$io_pdf)
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
		
		$la_data[1]=array('name'=>'ELABORADO POR','name1'=>'ADMINISTRACION');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>352), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>351))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'','name1'=>'');
		$la_data[2]=array('name'=>'','name1'=>'');
		$la_data[3]=array('name'=>'','name1'=>'');
		$la_data[4]=array('name'=>'','name1'=>'');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>352), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>351))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'RECIBE CONFORME','name1'=>'SELLO');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>400), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>303))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'','name1'=>'');
		$la_data[2]=array('name'=>'    Nombre y Apellido:  ____________________________________________','name1'=>'');
		$la_data[3]=array('name'=>'    Cedula de Identidad:  __________________________________________','name1'=>'');
		$la_data[4]=array('name'=>'    Fecha en que se recibe Comprobante: ____________________________','name1'=>'');
		$la_data[5]=array('name'=>'','name1'=>'');
		$la_columna=array('name'=>'','name1'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>900, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'left','width'=>400), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>303))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->setStrokeColor(0,0,0);
		if($ls_fecsol=="01/01/1900")
		{
			$ls_fecsol="";
		}
		
		if($ls_fecche=="01/01/1900")
		{
			$ls_fecche="";
		}

		$io_pdf->addText(350,20,8,"Fecha ".date("d/m/Y")."  Hora: ".date("h:i a")); // Agregar la Fecha
		$io_pdf->addText(620,70,8,"<b>FECHA OP1: </b>".$ls_fecsol); 			
		$io_pdf->addText(620,60,8,"<b>OP1: </b>".$ls_numsol); 		
		$io_pdf->addText(620,50,8,"<b>FECHA CHEQUE_1: </b>".$ls_fecche);			
		$io_pdf->addText(620,40,8,"<b>CHEQUE_1: </b>".$ls_numche);
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
			$io_pdf=new Cezpdf('LETTER','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(4,4,3,3);
			$lb_valido=true;
			$ls_codigoant="";
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
						$ld_fecreg=$io_funciones->uf_convertirfecmostrar($io_report->DS_ISLR->data["fecrep"][$li_i]);
						$li_montotdoc=number_format($io_report->DS_ISLR->data["totcmp_con_iva"][$li_i],2,',','.');  
						$li_monobjret=number_format($io_report->DS_ISLR->data["basimp"][$li_i],2,',','.');    
						$li_retenido=number_format($io_report->DS_ISLR->data["iva_ret"][$li_i],2,',','.');  
						$li_porcentaje=number_format($io_report->DS_ISLR->data["porimp"][$li_i],2,',','.');
						$ls_numsop=$io_report->DS_ISLR->data["numsop"][$li_i];
						$ls_fecsol=$io_funciones->uf_convertirfecmostrar($io_report->DS_ISLR->data["fecemisol"][$li_i]);
						$ls_numche=$io_report->DS_ISLR->data["numdocpag"][$li_i];
						$ls_fecche=$io_funciones->uf_convertirfecmostrar($io_report->DS_ISLR->data["fecmov"][$li_i]);
						$ls_perfiscal=substr($ls_numsol,0,6);
						if($ls_codigo!=$ls_codigoant)
						{
							if($li_z>=1)
							{
								uf_print_firmas($io_pdf);
								$io_pdf->ezNewPage();  
							}
							uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecreg,$io_pdf);
							uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_telefono,$ls_direccion,$ls_numsol,$ls_perfiscal,$io_pdf);
							$ls_codigoant=$ls_codigo;
						}
						uf_print_detalle($ls_numdoc,$ls_consol,$ld_fecemidoc,$li_monobjret,$li_retenido,$li_porcentaje,$ls_numref,$li_montotdoc,$io_pdf);
					}
				}	
			}
			uf_print_firmas($ls_numsop,$ls_fecsol,$ls_numche,$ls_fecche,$io_pdf);			  
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