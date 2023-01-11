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
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_recepciones.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$ls_mesdes=substr($ld_fecregdes,3,2);
		$ls_meshas=substr($ld_fecreghas,3,2);
		$ls_yeardes=substr($ld_fecregdes,6,4);
		$ls_yearhas=substr($ld_fecreghas,6,4);
		$ls_periodofiscal=$ls_meshas." / ".$ls_yearhas;
		$io_encabezado=$io_pdf->openObject();
		$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
		$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$io_pdf->saveState();
		//$io_pdf->Rectangle(38,400,930,85);
		$io_pdf->addJpegFromFile('../../shared/imagebank/logo_sat_dc.jpeg',50,520,120,75); // Agregar Logo
		$io_pdf->addText(175,565,11,"<b>REPÚBLICA BOLIVARIANA DE VENEZUELA</b>"); // Agregar el título
		$io_pdf->addText(175,540,11,"<b>SERVICIO DE ADMINISTRACIÓN TRIBUTARIA DEL DISTRITO CAPITAL   </b>"); // Agregar el título
		$io_pdf->line(175,530,580,530);
		$io_pdf->addText(410,515,11,"<b>Sub Secretaria de Recaudación   </b>"); // Agregar el título
		
		$io_pdf->addText(50,475,9,"<b>Nombre de la  Institución: </b>".$ls_agenteret."<b>       RIF :</b>".$ls_rifagenteret); // Agregar el título
		$io_pdf->addText(50,460,9,"<b>Dirección:</b>". $ls_diragenteret); // Agregar el título
		$io_pdf->addText(50,445,9,"<b>Periodo Fiscal a Informar:  </b>".$ls_periodofiscal); // Agregar el título
		$io_pdf->addText(50,430,9,"<b>Número de Planilla de Deposito bancario:   </b>"); // Agregar el título
		
		$io_pdf->ezSetY(400);
		$la_data1[1]=array(	'numdocpag'=>'<b>Fecha de Orden de Pago</b>',
							'vacio1'=>'<b>Número de Orden de Pago</b>',
							'numcom'=>'<b>Nombre del Contribuyente</b>',
							'nomban'=>'<b>C.I/RIF.</b>',
							'vacio2'=>'<b>Monto de la Operación</b>',
							'nomsujret'=>'<b>Monto del Impuesto 1 X 1000</b>',
							'vacio3'=>'<b>Monto de la Factura</b>');
		
	  	$la_columna=array('numdocpag'=>'','vacio1'=>'','numcom'=>'','nomban'=>'',
						'vacio2'=>'','nomsujret'=>'','vacio3'=>'');
           
      	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						'fontSize' => 10, // Tamaño de Letras
						'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						'showLines'=>1, // Mostrar Líneas
						'shaded'=>0, // Sombra entre líneas
						'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						'width'=>690, // Ancho de la tabla
						'maxWidth'=>690, // Ancho Mínimo de la tabla
						'colGap'=>1,
						'cols'=>array('numdocpag'=>array('justification'=>'center','width'=>60),
						'vacio1'=>array('justification'=>'center','width'=>95),
						'numcom'=>array('justification'=>'center','width'=>200), // Justificacion y ancho de la columna
						'nomban'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
						'vacio2'=>array('justification'=>'center','width'=>80), // Justificacion y ancho de la columna
						'nomsujret'=>array('justification'=>'center','width'=>60),
						'vacio3'=>array('justification'=>'center','width'=>140))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($ld_fecregdes,$ld_fecreghas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$ls_mesdes=substr($ld_fecregdes,3,2);
		$ls_meshas=substr($ld_fecreghas,3,2);
		$ls_yeardes=substr($ld_fecregdes,6,4);
		$ls_yearhas=substr($ld_fecreghas,6,4);
		$ls_periodofiscal=$ls_meshas." - ".$ls_yearhas;
		$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
		$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
		$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
		$io_pdf->setStrokeColor(0,0,0);
		$la_data[1]=array('name'=>'(1)ENTE PUBLICO: '.$ls_agenteret);
		$la_data[2]=array('name'=>'(2)RIF: '.$ls_rifagenteret);
		$la_data[3]=array('name'=>'(3)DIRECCION: '.$ls_diragenteret);
		$la_data[4]=array('name'=>'(4)PERIODO FISCAL A INFORMAR: '.$ls_periodofiscal);
		$la_data[5]=array('name'=>'(5)NUMERO DE PLANILLA  DE DEPOSITO BANCARIO: ____________________');
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar lineas
						 'shaded'=>0, // Sombra entre lineas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientacion de la tabla
						 'width'=>600, // Ancho de la tabla						 
						 'maxWidth'=>600); // Ancho Minimo de la tabla
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config);		
		unset($la_data);
		unset($la_columna);
		unset($la_config);								
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   li_totaldoc // acumulado del total
		//				   li_totalcar // acumulado de los cargos
		//				   li_totalded // acumulado de las deducciones
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_columnas=array( 'numdocpag'=>'<b>(7) FECHA DE ORDEN DE PAGO</b>',
							 'vacio1'=>'<b>(8) NUMERO DE ORDEN DE PAGO</b>',
							 'numcom'=>'<b>(9) NOMBRE CONTRIBUYENTE</b>',
							 'nomban'=>'<b>(10) C.I./ R.IF DEL CONTRIBUYENTE</b>',
							 'vacio2'=>'<b>(12) MONTO  BRUTO DE ORDEN DE PAGO  </b>',
							 'nomsujret'=>'<b>(13) MONTO DEL IMPUESTO RETENIDO</b>',
							 'vacio3'=>'<b>(16)OPERACIONES ANULADAS</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						'fontSize' => 10, // Tamaño de Letras
						'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						'showLines'=>1, // Mostrar Líneas
						'shaded'=>0, // Sombra entre líneas
						'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						'width'=>690, // Ancho de la tabla
						'maxWidth'=>690, // Ancho Mínimo de la tabla
						'colGap'=>1,
						 'cols'=>array('numdocpag'=>array('justification'=>'center','width'=>60),
										'vacio1'=>array('justification'=>'center','width'=>95),
										'numcom'=>array('justification'=>'left','width'=>200), // Justificacion y ancho de la columna
										'nomban'=>array('justification'=>'center','width'=>70), // Justificacion y ancho de la columna
										'vacio2'=>array('justification'=>'right','width'=>80), // Justificacion y ancho de la columna
										'nomsujret'=>array('justification'=>'right','width'=>60),
										'vacio3'=>array('justification'=>'right','width'=>140)));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($li_total_iva_ret,$li_totalmonpag,$li_totalconiva,$li_totaltotdoc,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_agenteret // Nombre del Agente de retención
		//	    		   as_rifagenteret // Rif del Agente de retención
		//	    		   as_perfiscal // Período fiscal
		//	    		   as_codsujret // Código del Sujeto a retención
		//	    		   as_nomsujret // Nombre del Sujeto a retención
		//	    		   as_diragenteret // Dirección del agente de retención
		//	    		   as_numcon // Número de Comprobante
		//	    		   ad_fecrep // Fecha del comprobante
		//	    		   ai_estcmpret // estatus del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data1[1]=array('total'=>'<b>TOTAL:   </b>',
						   'monto2'=>'<b>'.$li_totalconiva.'</b>',
		                   'iva'=>'<b>'.$li_total_iva_ret.'</b>',
						   'obs'=>'<b>'.$li_totaltotdoc.'</b>');
		$la_columna=array('total'=>'',
						  'monto2'=>'',
		                  'iva'=>'',
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
						 'cols'=>array('total'=>array('justification'=>'right','width'=>425), // Justificacion y ancho de la columna
									   'monto2'=>array('justification'=>'right','width'=>80),
									   'iva'=>array('justification'=>'right','width'=>60),
									   'obs'=>array('justification'=>'right','width'=>140))); 
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);
		unset($la_data1);
		unset($la_columna);
		unset($la_config); 
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------			
	function uf_print_firma($io_pdf)
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
		
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->Rectangle(38,26,450,120);
		$io_pdf->line(38,119,489,119);
		$io_pdf->addText(160,135,10,"<b>Datos del Responsable de la Declaración</b>"); // Agregar el título
		$io_pdf->addText(200,123,10,"<b>Agente de Retención</b>"); // Agregar el título
		$io_pdf->addText(50,109,9,"<b>Nombre y Apellido :</b>"."   MARTINEZ XIOMARA"); // Agregar el título
		$io_pdf->addText(50,97,9,"<b>Número de C.I:</b>"."           9.579.942 "); // Agregar el título
		$io_pdf->addText(50,85,9,"<b>Cargo:  </b>"."                       CONTRALORA MUNICIPAL"); // Agregar el título
		$io_pdf->addText(50,64,9,"<b>Firma:  </b>"."________________________________________________________"); // Agregar el título
		$io_pdf->addText(50,52,9,"<b>Teléfono:  </b>"."0251-9921757"); // Agregar el título
		$io_pdf->addText(50,40,9,"<b>Correo Electronico:  </b>"."CONTRALORIASP@CANTV.NET"); // Agregar el título
		$io_pdf->addText(50,28,9,"<b>Sello:_   </b>"); // Agregar el título
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}

	//--------------------------------------------------------------------------------------------------------------------------------			

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversión de numeros a letras.
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>RECEPCIONES DE DOCUMENTOS</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=trim($io_fun_cxp->uf_obtenervalor_get("codprobendes",""));
	$ls_codprobenhas=trim($io_fun_cxp->uf_obtenervalor_get("codprobenhas",""));
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_codtipdoc=$io_fun_cxp->uf_obtenervalor_get("codtipdoc","");
	$ls_registrada=$io_fun_cxp->uf_obtenervalor_get("registrada","");
	$ls_anulada=$io_fun_cxp->uf_obtenervalor_get("anulada","");
	$ls_procesada=$io_fun_cxp->uf_obtenervalor_get("procesada","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_nomprobendes="";
	$ls_nomprobenhas="";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_retencionesunoxmil($ld_fecregdes,$ld_fecreghas); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(8.8,4,3,3);
			$io_pdf->ezStartPageNumbers(970,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numcom");
			//uf_print_cabecera($ld_fecregdes,$ld_fecreghas,$io_pdf);
			uf_print_encabezado_pagina($ls_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf);
			$li_total_iva_ret=0;
			$li_totalmonpag=0;
			$li_totalconiva=0;
			$li_totaltotdoc=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$li_montotdoc=$io_report->uf_retenciones1x1000_monfact($ls_numcom);
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecemisol"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["totcmp_con_iva"][$li_i];
				$li_totimp= $io_report->DS->data["totimp"][$li_i];
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$li_montopag= $io_report->DS->data["montopag"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);

				$li_total_iva_ret=$li_total_iva_ret+$li_iva_ret;
				$li_totalmonpag= $li_totalmonpag + $li_montopag;
				$li_totalconiva= $li_totalconiva + $li_totcmp_con_iva;
				$li_totaltotdoc= $li_totaltotdoc + $li_montotdoc;
//				$li_totalded= $li_totalded + $li_mondeddoc;

				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,',','.');
				$li_totimp= number_format($li_totimp,2,',','.');
				$li_iva_ret= number_format($li_iva_ret,2,',','.');
				$li_montopag= number_format($li_montopag,2,',','.');
				$li_montotdoc= number_format($li_montotdoc,2,',','.');

				$la_data[$li_i]=array('numsop'=>$li_i,'numdocpag'=>$ld_fecmov,'vacio1'=>$ls_numsop,'numcom'=>$ls_nomsujret,'nomban'=>$ls_rif,
									  'fecmov'=>$li_montopag,'vacio2'=>$li_totcmp_con_iva,'nomsujret'=>$li_iva_ret,'rif'=>"CONTADO",
									  'totcmp_con_iva'=>"SIMON PLANAS",'vacio3'=>$li_montotdoc);
			}
			uf_print_detalle_recepcion($la_data,$io_pdf);
			$li_total_iva_ret= number_format($li_total_iva_ret,2,',','.');
			$li_totalmonpag= number_format($li_totalmonpag,2,',','.');
			$li_totalconiva= number_format($li_totalconiva,2,',','.');
			$li_totaltotdoc= number_format($li_totaltotdoc,2,',','.');
			uf_print_total($li_total_iva_ret,$li_totalmonpag,$li_totalconiva,$li_totaltotdoc,$io_pdf);
			//uf_print_firma($io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}

?>
