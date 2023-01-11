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
		//$io_pdf->Rectangle(50,515,690,65);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_sucre.JPEG',35,480,180,90); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(18,$as_titulo);
		$tm=520-($li_tm/2);
		//$io_pdf->addText($tm,540,12,$as_titulo); // Agregar el título
		$io_pdf->addText(440,565,10,"República Bolivariana de Venezuela"); // Agregar el título
		$io_pdf->addText(410,555,10,"Servicio Metropolitano de Administración Tributaria"); // Agregar el título
		$io_pdf->addText(410,545,10,"de la alcaldia del Distrito Metropolitano de Caracas"); // Agregar el título
		
		$io_pdf->addText(410,500,9,"<b>COMPROBANTE DE RETENCIÓN DEL IMPUESTO</b>"); // Agregar el título
		$io_pdf->addText(450,490,9,"<b>DEL UNO POR MIL (1 x 1000)</b>"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numcon,$ad_fecrep,$as_agenteret,$as_rifagenteret,$as_perfiscal,$as_licagenteret,$as_diragenteret,
							   $as_nomsujret,$as_rif,$as_numlic,$ai_estcmpret,$as_diragenteret,$as_telagenteret,$io_pdf)
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
		
		$io_pdf->ezSetDy(10);
	 	$ld_date=date("d/m/Y");
		$io_pdf->Rectangle(30,40,950,535);// Marco general
		/*if($ai_estcmpret==2)
		{
		    $io_pdf->Rectangle(45,495,180,30);		
			$io_pdf->addText(90,505,15,"<b> ANULADO </b>"); 
		}*/	
		$as_tipper="X";
		if (substr($as_rif,0,1)=="J")
		{
			$io_pdf->addText(200,311,8,$as_tipper);
		}
		else
		{
			$io_pdf->addText(480,311,8,$as_tipper);
		}
		$io_pdf->addText(860,516,8,$as_numcon);
		$io_pdf->addText(780,515,8,"<b>N° Correlativo  _____________________</b>"); 
		
		
		$io_pdf->addText(40,30,7,"Retenciones e Impuestos");
		$io_pdf->addText(40,23,7,"Cuenta 39673-2011");
		$io_pdf->addText(40,16,7,"SAIME");
		
		$io_pdf->addText(60,450,8,"<b>Agente de Retención:             ________________________________________________________________________________________________________________________________________________________________________________</b>");
		$io_pdf->addText(200,451,8,$as_agenteret); 
		
		$io_pdf->addText(60,430,8,"<b>N° de R.I.F.:                              ________________________________________________________________________________________________________________________________________________________________________________</b>");
		$io_pdf->addText(200,431,8,$as_rifagenteret); 
		
		$io_pdf->addText(60,410,8,"<b>Domicilio Fiscal:                     ________________________________________________________________________________________________________________________________________________________________________________</b>");
		$io_pdf->addText(200,411,8,$as_diragenteret); 
		
		$io_pdf->addText(60,390,8,"<b>Télefono(s):                             ________________________________________________________________________________________________________________________________________________________________________________</b>");
		$io_pdf->addText(200,391,8,$as_telagenteret); 
		
		$io_pdf->line(30,369,980,369); //Horizontal
		$io_pdf->line(120,240,949,240); //Horizontal
		$io_pdf->line(30,85,980,85); //Horizontal
		//$io_pdf->line(30,190,580,190); //Horizontal
		
		$io_pdf->addText(60,340,8,"<b>Contribuyente:                        _______________________________________________________________________________________________________________________________________________________________________________</b>");
		$io_pdf->addText(200,341,8,$as_nomsujret); 
		
		$io_pdf->addText(60,310,8,"<b>Persona Natural:                    __________________                                                          Persona Juridica:  _______________                                          Cedula de Identidad o R.I.F. N°                          _____________________________________</b>");
		$io_pdf->addText(785,311,8,$as_rif);
				
		$io_pdf->addText(60,275,8,"<b>Por concepto de: Prestación de Servicios                _____________________           Adquisición de Bienes o Suministros                               __________________           Ejecución de Obras                                    ________________________</b>");
		
		$io_pdf->addText(60,60,8,"<b>Agente de Retención:   _____________________________________________________________                   Firma: _______________________________          Sello:                             Firma del Contribuyente:     ______________________</b>");
		$io_pdf->addText(60,53,8,"<b>          (Responsable)</b>");
		$io_pdf->addText(150,61,8,$as_agenteret);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
			
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ai_totbasimp,$ai_totmonimp,$ai_totmoniva,$as_rifagenteret,$as_numsop,$as_desope,$as_totconiva,$ad_fecfac,$io_pdf)
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
		
		$io_pdf->addText(360,211,8,$ai_totbasimp);
		$io_pdf->addText(800,211,8,$as_totconiva);
		$io_pdf->addText(340,181,8,$ai_totmoniva);
		$io_pdf->addText(700,181,8,$ad_fecfac);
		$io_pdf->ezSetDy(-265);
		$la_data_l[1]=array('name'=>'<b>Descripción :     </b>'.$as_desope);
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Lieas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>515, // Orientación de la tabla
						 'width'=>690, // Ancho de la tabla
						 'colGap'=>1,						 
						 'maxWidth'=>690,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>910))); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data_l,$la_columna,'',$la_config);
		unset($la_data_l);
		unset($la_columna);
		unset($la_config);	
	
		$io_pdf->addText(60,210,8,"<b>Monto Bruto de la Operación: Bs.                                         ___________________________________________________                                   Monto del Impuesto Bs.                 _____________________________________________________</b>");
		
		$io_pdf->addText(60,180,8,"<b>Monto Retenido: Bs.                                                     ______________________________________________                                                   Fecha de la Retención:________________________________________________________________</b>");
		
		$io_pdf->addText(60,150,8,"<b>Llenar sólo en caso de pagos efectuados directamente en las cuentas receptoras de Fondos del Distrito Capital</b>");
	
		$io_pdf->addText(60,125,8,"<b>Banco:              ___________________________________________________     N° de planilla:          ______________________________</b>");
	
		$io_pdf->addText(60,95,8,"<b>Monto Pagado: Bs. _______________________________________________     Fecha de pago:        ______________________________</b>");
				
	}// end function uf_print_detalle

	function uf_print_sello($io_pdf)
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
		
	    $io_pdf->ezSetDy(-59);
		$io_pdf->addText(35,230,10,'<b>FIRMA:</b>'); // Agregar el título
		$io_pdf->addText(35,150,10,'<b>FECHA:</b>'); // Agregar el título
		
		$io_pdf->addText(220,230,10,'<b>FIRMA:</b>'); // Agregar el título
		$io_pdf->addText(220,190,10,'<b>SELLO:</b>'); // Agregar el título
		$io_pdf->addText(220,150,10,'<b>FECHA:</b>'); // Agregar el título
		
		$io_pdf->addText(400,230,10,'<b>FIRMA:</b>'); // Agregar el título
		$io_pdf->addText(400,190,10,'<b>SELLO:</b>'); // Agregar el título
		$io_pdf->addText(400,150,10,'<b>FECHA:</b>'); // Agregar el título
		$la_data[1]=array('name1'=>'<b>EL LIQUIDADOR</b>',
	                    'name2'=>'<b>ENTE GUBERNAMENTAL</b>',
						'name3'=>'<b>RECIBIDO POR </b>');	
        $la_columna=array('name1'=>'','name2'=>'','name3'=>'');
		$la_config= array('showHeadings'=>0, // Mostrar encabezados
						  'fontSize' => 10, // Tamaño de Letras
						  'showLines'=>0, // Mostrar Líneas
						  'shaded'=>0, // Sombra entre líneas
						  'shadeCol'=>array(0.9,0.9,0.9),
						  'shadeCol2'=>array(0.9,0.9,0.9),
						  'xOrientation'=>'center', // Orientación de la tabla
						  'colGap'=>1,
						  'width'=>690,
						  'cols'=>array('name1'=>array('justification'=>'center','width'=>185),						                
										'name2'=>array('justification'=>'center','width'=>185),
										'name3'=>array('justification'=>'center','width'=>185))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config); 		
	}
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
	$ls_titulo= "COMPROBANTE DE RETENCION DE IMPUESTO DE TIMBRE FISCAL";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_licagenteret=$_SESSION["la_empresa"]["numlicemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	$ls_telagenteret=$_SESSION["la_empresa"]["telemp"];
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
			$io_pdf = new Cezpdf('LEGAL','landscape');
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm');
			$io_pdf->ezSetCmMargins(3.5,1.5,3,3);
			$lb_valido=true;
			$ls_numcomant = "";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				uf_print_encabezado_pagina($ls_titulo,$io_pdf);
				$ls_numcom=$la_datos[$li_z];
				$lb_valido=$io_report->uf_retencionesunoxmil_proveedor($ls_numcom,$ls_mes,$ls_anio);
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
						if ($ls_numcom!=$ls_numcomant)
					   {
					    if ($li_z>=1)
						   {
							 $io_pdf->ezNewPage();  
						   }
					     uf_print_cabecera($ls_numcon,$ls_fecrep,$ls_agenteret,$ls_rifagenteret,$ls_perfiscal,$ls_licagenteret,
									  $ls_diragenteret,$ls_nomsujret,$ls_rif,$ls_numlic,$li_estcmpret,$ls_diragenteret,$ls_telagenteret,$io_pdf);
						 $ls_numcomant=$ls_numcom;
					   }
					}											
					$lb_valido=$io_report->uf_retencionesunoxmil_detalles($ls_numcom);
					if($lb_valido)
					{
						$li_totalbaseimp=0;
						$li_totalmontoimp=0;
						$li_totmontoiva=0;
						$li_totmontotdoc=0;
						$li_total=$io_report->ds_detalle->getRowCount("numfac");			   
						for($li_i=1;$li_i<=$li_total;$li_i++)
						{
							$li_montotdoc=$io_report->uf_retencionesmunicipales_monfact($ls_numcon);
							$ls_numsop=$io_report->ds_detalle->data["numsop"][$li_i];					
							$ld_fecfac=$io_funciones->uf_convertirfecmostrar($io_report->ds_detalle->data["fecfac"][$li_i]);	
							$ls_numfac=$io_report->ds_detalle->data["numfac"][$li_i];	
							$ls_numref=$io_report->ds_detalle->data["numcon"][$li_i];	              
							$li_baseimp=$io_report->ds_detalle->data["basimp"][$li_i];
							$li_iva_ret=$io_report->ds_detalle->data["iva_ret"][$li_i];	
							$li_porimp=$io_report->ds_detalle->data["porimp"][$li_i];	
							$li_totimp=$io_report->ds_detalle->data["totimp"][$li_i];
							$ls_desope=$io_report->ds_detalle->data["desope"][$li_i];
							$ls_totconiva=$io_report->ds_detalle->data["totcmp_con_iva"][$li_i];	

							$li_totalbaseimp=$li_totalbaseimp + $li_baseimp ;	
							$li_totalmontoimp=$li_totalmontoimp + $li_totimp;
							$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
							$li_totmontoiva=$li_totmontoiva+$li_iva_ret;
							$li_iva_ret=number_format($li_iva_ret,2,",",".");	
							$li_baseimp=number_format($li_baseimp,2,",",".");			
							$li_porimp=number_format($li_porimp,4,",",".");			
							$li_totimp=number_format($li_totimp,2,",",".");							
							$li_montotdoc=number_format($li_montotdoc,2,",",".");
							$ls_totconiva=number_format($ls_totconiva,2,",",".");							
							$la_data[$li_i]=array('fecfac'=>$ld_fecfac,'numfac'=>$ls_numfac,
												  'numref'=>$ls_numref,'baseimp'=>$li_baseimp,'iva_ret'=>$li_iva_ret,'porimp'=>$li_porimp,'totimp'=>$li_montotdoc,'numsop'=>$ls_numsop, );														
						  }																		 																						  
						  $li_totalbaseimp= number_format($li_totalbaseimp,2,",","."); 
  						  $li_totalmontoimp= number_format($li_totmontotdoc,2,",","."); 
						  $li_totmontoiva= number_format($li_totmontoiva,2,",","."); 
						  uf_print_detalle($la_data,$li_totalbaseimp,$li_totalmontoimp,$li_totmontoiva,$ls_rifagenteret,$ls_numsop,$ls_desope,$ls_totconiva,$ld_fecfac,$io_pdf);
						  //uf_print_sello($io_pdf);
						  unset($la_data);							 
						  
					}
				}
				$io_report->DS->reset_ds();
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