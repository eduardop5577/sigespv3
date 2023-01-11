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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_retencionesmunicipales.php",$ls_descripcion);
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
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],47,539,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->setStrokeColor(0,0,0);
        $io_pdf->Rectangle(48,520,702,60);	
		$io_pdf->line(600,520,600,580);
		$io_pdf->line(600,550,750,550);
		$io_pdf->addText(75,545,16,"<b>".$as_titulo."</b>"); // Agregar el tulo				
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
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$io_pdf)
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
		
		$io_pdf->setStrokeColor(0,0,0);
		if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}	


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
		$la_data[1]=array('name'=>$as_agenteret,'name1'=>$as_rifagenteret,'name2'=>$as_perfiscal);
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
		$la_data[1]=array('name'=>'<b>Direccion del Agente de Retencion: </b>'.$as_diragenteret);
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
		$la_data[1]=array('name'=>'<b>DATOS DEL CONTRIBUYENTE </b>');
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
		$la_data[1]=array('name'=>'<b>NOMBRE: </b>'.$as_nomsujret);
		$la_data[2]=array('name'=>'<b>No. DE R.I.F.: </b>'.$as_rif);
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
		$io_pdf->addText(440,395,10,"<b> PERSONA NATURAL ___    PERSONA JURIDICA ___ </b>"); 

	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($aa_data,$ai_totbasimp,$ai_totmonimp,$as_rifagenteret,$ls_consol,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: la_data // Arreglo de datos a imprimir
		//	    		   ai_totbasimp // Total de la base imponible
		//	    		   ai_totmonimp // Total monto imponible
		//	    		   as_rifagenteret // Rif del Agente de Retención
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		//---------------------------------------------------------------------------------------------------
		$la_data[1]=array('name'=>'Prestacion de Servicio','name1'=>'Adqusicion de Bienes o Suministros','name2'=>'Ejecucion de Obras','name3'=>'Descripcion');
		$la_data[2]=array('name'=>'','name1'=>'','name2'=>'','name3'=>$ls_consol);
		$la_columna=array('name'=>'','name1'=>'','name2'=>'','name3'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>405, // Orientacion de la tabla
						 'width'=>700, // Ancho de la tabla						 
						 'maxWidth'=>725, // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'name1'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>403))); 
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data1[1]=array('fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
  						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
						  'numsop'=>'<b>Monto Factura</b>',
						  'baseimp'=>'<b>Monto de la Operación</b>',
						  'porimp'=>'<b>Alícuota</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_columna=array('fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
  						  'numsop'=>'<b>Nº Orden de Pago</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Alícuota</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho Mínimo de la tabla
						 'xPos'=>405, // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numsop'=>array('justification'=>'center','width'=>115),
						 			   'baseimp'=>array('justification'=>'center','width'=>115),
						 			   'porimp'=>array('justification'=>'center','width'=>68),
   						 			   'iva_ret'=>array('justification'=>'center','width'=>105))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_columna=array('fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'numref'=>'<b>Num. Ctrol de Factura</b>',		
  						  'numsop'=>'<b>Nº Orden de Pago</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>Alícuota</b>',
						  'iva_ret'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>740, // Ancho de la tabla
						 'maxWidth'=>740, // Ancho Mínimo de la tabla
						 'xPos'=>405, // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>100), // Justificacion y ancho de la columna
						 			   'numsop'=>array('justification'=>'center','width'=>115),
						 			   'baseimp'=>array('justification'=>'center','width'=>115),
						 			   'porimp'=>array('justification'=>'center','width'=>68),
   						 			   'iva_ret'=>array('justification'=>'center','width'=>105))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($aa_data,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		$la_data1[1]=array('total'=>'<b>Total</b>',		
						   'monto'=>'<b>'.$ai_totbasimp.'</b>',
						   'imponible'=>'<b>'.$ai_totmonimp.'</b>');
		$la_columna=array('total'=>'<b>total</b>',		
						  'monto'=>'<b>monto</b>',		
						  'imponible'=>'<b>monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>360, // Ancho de la tabla
						 'maxWidth'=>360, // Ancho Mínimo de la tabla
						 'xPos'=>555, // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>115), // Justificacion y ancho de la columna
   						 			   'monto'=>array('justification'=>'right','width'=>115),
   						 			   'imponible'=>array('justification'=>'right','width'=>173))); 
		$io_pdf->ezSetDy(-0.5);
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
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
	$ls_titulo="<b>COMPROBANTE DE RETENCION DEL IMPUESTO TIMBRE FISCAL</b>";
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
			$io_pdf->ezSetCmMargins(4,4,3,3);
			$lb_valido=true;
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesmunicipales_proveedor($ls_numcom,$ls_mes,$ls_anio);
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
					}											
					uf_print_encabezado_pagina($ls_titulo,$ls_numcom,$ls_fecrep,$io_pdf);
					uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
									  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$io_pdf);
					$lb_valido=$io_report->uf_retencionesmunicipales_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totcmp_con_iva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$ls_consol=$io_report->ds_detalle->data["consol"][$li_i];
							$li_monfac=	$li_totcmp_con_iva-$li_totimp;
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];
							$ls_fecsol=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecemisol"][$li_i]);
							$ls_numche=$io_report->ds_detalle->data["numdocpag"][$li_i];
							$ls_fecche=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecmov"][$li_i]);

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,4,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_monfac=number_format($li_monfac,2,",",".");							
							$la_data[$li_i]=array('numero'=>$li_i,'numsop'=>$li_monfac, 'fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,
												  'numref'=>$ls_numref,'baseimp'=>$li_baseimp,'porimp'=>$li_porimp,'iva_ret'=>$li_totimp);														
						  }																		 																						  
  						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totalmontoimp,2,",","."); 
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$ls_rifagenteret,$ls_consol,$io_pdf); 						 						  
						  uf_print_firmas($ls_numsop,$ls_fecsol,$ls_numche,$ls_fecche,$io_pdf);			  
						  unset($la_data);							 
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