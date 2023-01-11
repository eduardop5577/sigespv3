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
	function uf_print_encabezado_pagina($as_titulo,$ls_mes,$ls_anio,$io_pdf)
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
		//$io_pdf->Rectangle(50,515,690,65);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/logo_sucre.JPEG',50,540,90,70); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],80,500,110,90); // Agregar Logo
		$io_pdf->addText(250,590,11,"<i>REPÚBLICA BOLIVARIANA DE VENEZUELA</i>"); // Agregar el título
		$io_pdf->addText(250,545,9,"<b><i>SERVICIOS DE ADMINISTRACIÓN TRIBUTARIA DEL DISTRITO CAPITAL</b></i>"); // Agregar el título
		$io_pdf->addText(252,542,6,"____________________________________________________________________________________________________________________________"); // Agregar el título
		$io_pdf->addText(540,530,9,"<i>Sub Secretaria de Recaudación</i>"); // Agregar el título
		
		$io_pdf->addText(430,500,9,"<b>RELACIÓN MENSUAL DEL IMPUESTO 1X1000</b>"); // Agregar el título
		$io_pdf->addText(540,490,9,"<b>ORDENES DE PAGO</b>"); // Agregar el título
		
		$io_pdf->addText(50,460,9,"<b>Nombre de la  Institución:</b>"." INSTITUTO NACIONAL DE DEPORTES "."<b>     R.I.F :</b>"."   G-20000046-5"); // Agregar el título
		$io_pdf->addText(50,445,9,"<b>Dirección:</b>"." Av.Principal de Montalbán, Prolongación La Vega, Velódromo  TEO CAPRILES "); // Agregar el título
		$io_pdf->addText(50,430,9,"<b>Periodo:  </b>".$ls_mes."-".$ls_anio); // Agregar el título
		$io_pdf->addText(50,415,9,"<b>Nº de Planilla(s) Bancaria(s):   </b>"); // Agregar el título
		
		$io_pdf->ezSetY(400);
		$la_data1[1]=array(	'fecfac'=>'<b>Fecha Orden de Pago</b>',
							'numsop'=>'<b>Orden de Pago Nº</b>',
							'numfac'=>'<b>Nombre del Contribuyente</b>',
							'fecliq'=>'<b>C.I o RIF</b>',
							'baseimp'=>'<b>Monto de la Operación</b>',
							'iva_ret'=>'<b>Monto del Impuesto / 1x1000</b>',
							'porimp'=>'<b>Municipio donde se efectuó el pago</b>',
							'obs'=>'<b>Observaciones</b>');
		
	  	$la_columna=array('fecfac'=>'','numsop'=>'','numfac'=>'','fecliq'=>'',
						'baseimp'=>'','iva_ret'=>'','porimp'=>'','obs'=>'');
           
      	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						'fontSize' => 10, // Tamaño de Letras
						'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						'showLines'=>1, // Mostrar Líneas
						'shaded'=>0, // Sombra entre líneas
						'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						'width'=>690, // Ancho de la tabla
						'maxWidth'=>690, // Ancho Mínimo de la tabla
						'colGap'=>1,
						'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70),
						'numsop'=>array('justification'=>'center','width'=>110),
						'numfac'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						'fecliq'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						'baseimp'=>array('justification'=>'center','width'=>90), // Justificacion y ancho de la columna
						'iva_ret'=>array('justification'=>'center','width'=>120),
						'porimp'=>array('justification'=>'center','width'=>150),
						'obs'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$as_conceptosp,$io_pdf)
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
		
		$io_pdf->ezSetY(420);
        
		$la_data[1]=array('agen_ret'=>'<b>Nombre o Razón Social:   </b>',
		                  'ubic'=>'  '.$as_nomsujret.' ');				
		$la_columna=array('agen_ret'=>'','ubic'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('agen_ret'=>array('justification'=>'right','width'=>200),
						               'ubic'=>array('justification'=>'left','width'=>490)));
  		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data[1]=array('agen_ret'=>'<b>R.I.F.: </b>',
		                  'ubic'=>'  '.$as_rif.'');				
		$la_columna=array('agen_ret'=>'',
		                  'ubic'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla	
						 'colGap'=>1,					 
						 'maxWidth'=>690,
						 'cols'=>array('agen_ret'=>array('justification'=>'right','width'=>200),
						               'ubic'=>array('justification'=>'left','width'=>490)));
       $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	   unset($la_data1);
	   unset($la_columna);
	   unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($ld_fecfac,$ls_nomsujret,$ls_numref,$li_baseimp,$li_iva_ret,
							  $li_porimp,$li_montotdoc,$ls_numsop,$ls_rif,$io_pdf)
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
		
		$la_data[1]=array('fecfac'=>$ld_fecfac,
						  'numsop'=>$ls_numsop,
						  'numfac'=>$ls_nomsujret,
						  'fecliq'=>$ls_rif,
						  'baseimp'=>$li_baseimp,
						  'iva_ret'=>$li_iva_ret,
						  'porimp'=>'---',
						  'obs'=>'');
		
		$la_columna=array('fecfac'=>'<b>Fecha Orden de Pago</b>',
						  'numsop'=>'<b>Orden de Pago Nº</b>',
						  'numfac'=>'<b>Numero de Factura</b>',
						  'fecliq'=>'<b>Fecha Liquidación</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'iva_ret'=>'<b>Monto Ret.</b>',
						  'porimp'=>'<b>Retención 1x1000</b>',
						  'obs'=>'');
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70),
						 'numsop'=>array('justification'=>'center','width'=>110),
						 'numfac'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						 'fecliq'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 'baseimp'=>array('justification'=>'center','width'=>90), // Justificacion y ancho de la columna
						 'iva_ret'=>array('justification'=>'center','width'=>120),
						 'porimp'=>array('justification'=>'center','width'=>150),
						 'obs'=>array('justification'=>'center','width'=>100)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle

	function uf_print_sello($ai_totalbaseimp,$ai_totalmontoimp,$ai_totmontoiva,$io_pdf)
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
		
	    $la_data1[1]=array('total'=>'<b>Total Monto Retenido:   </b>',
		                   'monto'=>'<b>'.$ai_totalbaseimp.'</b>',
		                   'iva'=>'<b>'.$ai_totmontoiva.'</b>',
						   'imponible'=>' ',
						   'obs'=>'');
		$la_columna=array('total'=>'',
		                  'monto'=>'',
		                  'iva'=>'',
						  'imponible'=>'',
						  'obs'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,
						 'maxWidth'=>690, // Ancho Mínimo de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>450), // Justificacion y ancho de la columna
   						 			   'monto'=>array('justification'=>'center','width'=>90),
									   'iva'=>array('justification'=>'center','width'=>120),
									   'imponible'=>array('justification'=>'center','width'=>150),
									   'obs'=>array('justification'=>'center','width'=>100))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config); 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_funciones_fecha=new class_fecha();				
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
	$ls_titulo= "<b>COMPROBANTE DE RETENCION DE IMPUESTO DE TIMBRE FISCAL</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	if ($ls_mes=='01')
	{
		$ls_mes_texto='ENERO';
	}
	elseif ($ls_mes=='02')
	{
		$ls_mes_texto='FEBRERO';
	}
	elseif ($ls_mes=='03')
	{
		$ls_mes_texto='MARZO';
	}
	elseif ($ls_mes=='04')
	{
		$ls_mes_texto='ABRIL';
	}
	elseif ($ls_mes=='05')
	{
		$ls_mes_texto='MAYO';
	}
	elseif ($ls_mes=='06')
	{
		$ls_mes_texto='JUNIO';
	}
	elseif ($ls_mes=='07')
	{
		$ls_mes_texto='JULIO';
	}
	elseif ($ls_mes=='08')
	{
		$ls_mes_texto='AGOSTO';
	}
	elseif ($ls_mes=='09')
	{
		$ls_mes_texto='SEPTIEMBRE';
	}
	elseif ($ls_mes=='10')
	{
		$ls_mes_texto='OCTUBRE';
	}
	elseif ($ls_mes=='11')
	{
		$ls_mes_texto='NOVIEMBRE';
	}
	elseif ($ls_mes=='12')
	{
		$ls_mes_texto='DICIEMBRE';
	}
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
			$io_pdf = new Cezpdf("LEGAL","landscape");
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(8.5,1.5,3,3);
			$lb_valido=true;
			$ls_numcomant = "";
			$li_totalbaseimp=0;
			$li_totalmontoimp=0;
			$li_totmontoiva=0;
			uf_print_encabezado_pagina($ls_titulo,$ls_mes_texto,$ls_anio,$io_pdf);
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_numcom,$ls_mes,$ls_anio);
				if($lb_valido)
				{
					$li_total=$io_report->DS->getRowCount("numcom");
					$ls_conceptosp="";
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
						$lb_valido=$io_report->uf_retencionesunoxmil_detalles($ls_numcom);
						if($lb_valido)
						{
							$li_totmontotdoc=0;
							$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
							$ndias=30;
							for($li_i=1;$li_i<=$li_total;$li_i++)
							{
								$li_montotdoc=$io_report->uf_retencionesmunicipales_monfact($ls_numcon);
								$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
								$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
								$ld_fecliq=$io_funciones_fecha->suma_fechas($ld_fecfac,$ndias);	
								$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
								$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
								$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
								$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
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
								$li_montotdoc=number_format($li_montotdoc,2,",",".");							
							  	uf_print_detalle($ld_fecfac,$ls_nomsujret,$ls_numref,$li_baseimp,$li_iva_ret,
							  				   $li_porimp,$li_montotdoc,$ls_numsop,$ls_rif,$io_pdf);
							  }																		 																						  
						}
					}											
				$io_report->DS->reset_ds();
				}
			}
			$li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
		    $li_totalmontoimp= number_format($li_totmontotdoc,2,",","."); 
		    $li_totmontoiva= number_format($li_totmontoiva,2,",",".");
			uf_print_sello($li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$io_pdf);
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