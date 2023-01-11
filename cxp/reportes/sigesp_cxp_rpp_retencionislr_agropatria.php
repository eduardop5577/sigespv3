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
	function uf_print_encabezado_pagina($as_titulo,$as_numdoc,$ld_fecemidoc,$io_pdf)
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
		$ls_y=substr($ld_fecemidoc,6,4);
		$ls_m=substr($ld_fecemidoc,3,2);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,510,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		//$io_pdf->rectangle(40,510,700,40);
		$io_pdf->addText(280,570,12,"<b>Empresa de propiedad social Agropatria, S.A</b>");// Agregar el título
		$io_pdf->addText(190,555,9,"Calle independencia norte, Edif.Agropatria, No. 104-39-18, piso PB OF 1,sector centro, Cagua-Edo. Aragua");// Agregar el título
		$io_pdf->addText(345,545,9,"Telefono: 0244-447-99-55");// Agregar el título
		$io_pdf->addText(355,535,9,"RIF: G-20010214-4");// Agregar el título
		$io_pdf->addText(280,510,14,"<b>COMPROBANTE DE RETENCION ISLR</b>");// Agregar el título
		//$io_pdf->addText(300,515,10,"<b>(Decreto 1.808 del 12-05-1997 Art. 24)</b>");// Agregar el título		
				
		//$io_pdf->line(530,485,740,485);
		//$io_pdf->line(655,465,655,505);
		//$io_pdf->addText(540,490,9,"<b>1. NRO. COMPROBANTE</b> ");
		//$io_pdf->addText(545,470,9,$as_numdoc);
		/*$io_pdf->addText(750,495,9,"<b>4. Periodo Fiscal</b> ");
		$io_pdf->addText(770,475,9,$ls_y.$ls_m);*/
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($as_agente,$as_nomproben,$as_rifproben,$as_nitproben,$as_condoc,$ld_fecemidoc,$ls_telemp,$ls_direccion,$io_pdf)
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
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 05/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->rectangle(40,465,100,40);
		  $io_pdf->addText(45,490,9,"<b>1. FECHA</b> ");
		  $io_pdf->addText(45,470,9,$ld_fecemidoc);
		  $ls_rifageret = $_SESSION["la_empresa"]["rifemp"];
          $ls_dirageret = $_SESSION["la_empresa"]["direccion"];
		  $ls_y=substr($ld_fecemidoc,6,4);
		  $ls_m=substr($ld_fecemidoc,3,2);
		  
		  $la_data    = array(array('ageret'=>$as_agente,'rifageret'=>$ls_rifageret,'perfiscal'=>$ls_y.$ls_m));	
	      $la_columna = array('ageret'=>'<b>2. NOMBRE O RAZON SOCIAL DEL AGENTE DE RETENCION</b>','rifageret'=>'<b>3. REGISTRO INF. FISCAL DEL AGENTE DE RETENCION </b>','perfiscal'=>'<b>4. PERIODO FISCAL</b>');
		  $la_config  = array('showHeadings'=>1, // Mostrar encabezados
						      'showLines'=>1, // Mostrar Líneas
						      'fontSize' => 9, // Tamaño de Letras
						      'titleFontSize' =>9,  // Tamaño de Letras de los títulos
						      'shaded'=>2, // Sombra entre líneas
						      'shadeCol'=>array(1,1,1),
						 	  'shadeCol2'=>array(1,1,1), // Color de la sombra
						 	  'xOrientation'=>'center', // Orientación de la tabla
						      'width'=>750, // Ancho de la tabla
						      'maxWidth'=>750,
						      'cols'=>array('ageret'=>array('justification'=>'left','width'=>305),
									        'rifageret'=>array('justification'=>'left','width'=>275),
											'perfiscal'=>array('justification'=>'left','width'=>120))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna); 
		unset($la_config); 

        $io_pdf->ezSetDy(-5);
	    $la_data    = array(array('dirageret'=>$ls_dirageret));	
	    $la_columna = array('dirageret'=>'<b>5. DIRECCION FISCAL DEL AGENTE DE RETENCION</b>');
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
	    $la_data    = array(array('nompro'=>$as_nomproben,'rifpro'=>$as_rifproben));	
	    $la_columna = array('nompro'=>'<b>6. NOMBRE O RAZON SOCIAL DEL SUJETO RETENIDO</b>','rifpro'=>'<b>7.REG. INF. FISCAL DEL CONTRIBUYENTE (RIF)</b>');
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
        $io_pdf->ezSetDy(-5);
	    $la_data    = array(array('dirageret'=>$ls_direccion));	
	    $la_columna = array('dirageret'=>'<b>8. DIRECCION FISCAL DEL SUJETO RETENIDO</b>');
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

	}// end function uf_print_encabezado
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
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
		
		$ls_titulo1="Total Compras Incluyendo el IVA";
		$ls_titulo2="Monto sin t DerechoCred";
		$la_columna=array('fecfac'=>'<b>Fecha Factura</b>',
						  'numfac'=>'<b>Nro. de Factura</b>',
  						  'numref'=>'<b>Nro. Control Fact</b>',		
						  'numnotdeb'=>'<b>Nro de N/D</b>',
						  'numnotcre'=>'<b>Nro de N/C</b>',				  
						  'numfacafe'=>'<b>Nro Factura Afectada</b>',				  
						  'totalconiva'=>'<b>Importe Facturas</b>',
						  'baseimp'=>'<b>Base Imponible Retencion</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'ivaret'=>'<b>Impuesto Retenido</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>395, // Orientación de la tabla
						 'cols'=>array('fecfac'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						 			   'numfac'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
									   'numref'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
 									   'numnotdeb'=>array('justification'=>'center','width'=>50),
  						 			   'numnotcre'=>array('justification'=>'center','width'=>50),
						 			   'numfacafe'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
  						 			   'totalconiva'=>array('justification'=>'center','width'=>90),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>50),
  						 			   'ivaret'=>array('justification'=>'center','width'=>80))); 
		$io_pdf->ezSetDy(-2);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
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
		
		$la_columna=array('numfac'=>'<b>Nro.</b>',
						  'totalconiva'=>'<b>Monto Total Factura</b>',
						  'baseimp'=>'<b>Base Imponible</b>',
						  'porimp'=>'<b>%     Alicuota</b>',
						  'ivaret'=>'<b>Impuesto ISLR</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Mínimo de la tabla
						 'xPos'=>525, // Orientación de la tabla
						 'cols'=>array('numfac'=>array('justification'=>'center','width'=>140), // Justificacion y ancho de la columna
   						 			   'totalconiva'=>array('justification'=>'center','width'=>90),
						 			   'baseimp'=>array('justification'=>'center','width'=>80),
						 			   'porimp'=>array('justification'=>'center','width'=>50),
  						 			   'ivaret'=>array('justification'=>'center','width'=>80))); 
		$io_pdf->ezSetDy(-15);
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
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
		$la_data[4]=array('f1'=>'','f2'=>'_________________________________','f3'=>'');
		$la_data[5]=array('f1'=>'','f2'=>'','f3'=>'');
		$la_data[6]=array('f1'=>'','f2'=>'Agente de Rentencion','f3'=>' ');
		$la_columna=array('f1'=>'','f2'=>'','f3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
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

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../class_folder/class_funciones_cxp.php");
	
	$io_report    = new sigesp_cxp_class_report();
	$io_funciones = new class_funciones();				
	$io_fun_cxp   = new class_funciones_cxp();

	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>COMPROBANTE DE RETENCION DE I.S.L.Ra.</b>";
    $ls_agente=$_SESSION["la_empresa"]["nombre"];
	$ls_telemp=$_SESSION["la_empresa"]["telemp"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes = $io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_procedencias = $io_fun_cxp->uf_obtenervalor_get("procedencias","");
	$ls_tiporeporte  = $io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	
	global $ls_tiporeporte;
	if ($ls_tiporeporte==1)
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
		     $io_pdf->ezSetCmMargins(5.3,3,3,3);
			$lb_valido=true;
			$ls_codigoant="";
			for ($li_z=0;($li_z<$li_totrow)&&($lb_valido);$li_z++)
			{
				$ls_numsol=$la_datos[$li_z];
				$ls_procede=$la_procedencias[$li_z];  
				if($ls_procede=="SCBBCH")
				{
					$rs_data=$io_report->uf_retencionesislr_scb($ls_numsol);  
				}
				else
				{
					$rs_data=$io_report->uf_retencionesislr_cxp($ls_numsol,false);
				}
					$li_totmontotdoc=0;
					$li_tottotdersiniva=0;
					$li_totmonobjret=0;
					$li_totretenido=0;
					$li_i=0;
					while((!$rs_data->EOF))
					{
						$ls_tipproben=$rs_data->fields["tipproben"];
						if($ls_tipproben=="P")
						{
							$ls_codigo=$rs_data->fields["cod_pro"];
							$ls_nombre=$rs_data->fields["proveedor"];
							$ls_telefono=$rs_data->fields["telpro"];
							$ls_direccion=$rs_data->fields["dirpro"];
							$ls_rif=$rs_data->fields["rifpro"];
						}					
						else
						{
							$ls_codigo=$rs_data->fields["ced_bene"];
							$ls_nombre=$rs_data->fields["beneficiario"];
							$ls_telefono=$rs_data->fields["telbene"];
							$ls_direccion=$rs_data->fields["dirbene"];
							$ls_rif=$rs_data->fields["rifben"];
						}
						$ls_nit=$rs_data->fields["nit"];
						$ls_consol=$rs_data->fields["consol"];
						$ls_numdoc=$rs_data->fields["numdoc"];
						$ls_numref=$rs_data->fields["numref"];
						$ld_fecemidoc  = $io_funciones->uf_convertirfecmostrar($rs_data->fields["fecemidoc"]);
						$ld_fecemisol  = $io_funciones->uf_convertirfecmostrar($rs_data->fields["fecemisol"]);
						$li_montotdoc=$rs_data->fields["montotdoc"];
						$li_monobjret=$rs_data->fields["monobjret"];
						$li_retenido=$rs_data->fields["retenido"];
						$li_moncardoc=$rs_data->fields["moncardoc"];
						$li_mondeddoc=$rs_data->fields["mondeddoc"];
						$li_totdersiniva="0,00";
						$li_montotdoc=$li_montotdoc+$li_mondeddoc;
						$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;
						$li_tottotdersiniva=$li_tottotdersiniva+$li_totdersiniva;
						$li_totmonobjret=$li_totmonobjret+$li_monobjret;
						$li_totretenido=$li_totretenido+$li_retenido;
						$li_porcentaje = number_format($rs_data->fields["porcentaje"],2,',','.');
						$li_montotdoc  = number_format($li_montotdoc,2,',','.');  
						$li_monobjret  = number_format($li_monobjret,2,',','.');    
						$li_retenido   = number_format($li_retenido,2,',','.'); 
						$li_i++; 
						$la_data[$li_i]=array('numope'=>"1",'fecfac'=>$ld_fecemidoc,'numfac'=>$ls_numdoc,'numref'=>$ls_numref,
										  'totalconiva'=>$li_montotdoc,'compsinderiva'=>$li_totdersiniva,
										  'baseimp'=>$li_monobjret,'porimp'=>$li_porcentaje,'ivaret'=>$li_retenido,'numnotdeb'=>"",'numnotcre'=>"",'numfacafe'=>"");														
						$rs_data->MoveNext();
					}
					
					$li_totmontotdoc  = number_format($li_totmontotdoc,2,',','.');  
					$li_totmonobjret  = number_format($li_totmonobjret,2,',','.');    
					$li_totretenido   = number_format($li_totretenido,2,',','.');  
					$la_datatot[1]=array('numfac'=>"<b>TOTALES</b>",'totalconiva'=>$li_totmontotdoc,'compsinderiva'=>$li_totdersiniva,
									  'baseimp'=>$li_totmonobjret,'porimp'=>"",'ivaret'=>$li_totretenido);														
					uf_print_encabezado_pagina($ls_titulo,$ls_numsol,$ld_fecemidoc,$io_pdf);
					uf_print_encabezado($ls_agente,$ls_nombre,$ls_rif,$ls_nit,$ls_consol,$ld_fecemisol,$ls_telemp,$ls_direccion,$io_pdf);
					uf_print_detalle($la_data,$io_pdf);
					uf_print_total($la_datatot,$io_pdf);
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