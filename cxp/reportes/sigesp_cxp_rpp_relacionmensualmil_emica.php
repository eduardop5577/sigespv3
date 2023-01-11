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
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
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

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,975,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/saatel_lara.jpg',25,535,100,60); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,"REPUBLICA BOLIVARIANA DE VENEZUELA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,580,11,"REPUBLICA BOLIVARIANA DE VENEZUELA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"GOBERNACION DEL ESTADO LARA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,570,11,"GOBERNACION DEL ESTADO LARA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"SERVIVIO AUTONOMO DE ADMINISTRACION TRIBUTARIA");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,560,11,"SERVIVIO AUTONOMO DE ADMINISTRACION TRIBUTARIA"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"DEL ESTADO LARA (SAATEL)");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,11,"DEL ESTADO LARA (SAATEL)"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,"RELACION MENSUAL IMPUESTO 1 X 1000 - ENTES PUBLICOS");
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,520,11,"RELACION MENSUAL IMPUESTO 1 X 1000 - ENTES PUBLICOS"); // Agregar el título
		// cuadro inferior
/*        $io_pdf->Rectangle(10,60,762,70);
		$io_pdf->line(10,73,772,73);		
		$io_pdf->line(10,117,772,117);		
		$io_pdf->line(203,60,203,130);		
		$io_pdf->line(391,60,391,130);		
		$io_pdf->line(579,60,579,130);		
		$io_pdf->addText(80,122,7,"ELABORADO POR"); // Agregar el título
		$io_pdf->addText(82,63,7,"FIRMA / SELLO"); // Agregar el título
		$io_pdf->addText(262,122,7,"VERIFICADO POR"); // Agregar el título
		$io_pdf->addText(252,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->addText(460,122,7,"AUTORIZADO POR"); // Agregar el título
		$io_pdf->addText(440,63,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
		$io_pdf->addText(635,122,7,"CONTRALORIA INTERNA"); // Agregar el título
		$io_pdf->addText(635,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título*/
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_perfiscal,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numsol    // Numero de la Solicitud de Pago
		//	   			   as_codigo    // Codigo del Proveedor / Beneficiario
		//	   			   as_nombre    // Nombre del Proveedor / Beneficiario
		//	   			   as_denfuefin // Denominacion de la fuente de financiamiento
		//	   			   ad_fecemisol // Fecha de Emision de la Solicitud
		//	   			   as_consol    // Concepto de la Solicitud
		//	   			   as_obssol    // Observaciones de la Solicitud
		//	   			   ai_monsol    // Monto de la Solicitud
		//	   			   as_monto     // Monto de la Solicitud en letras
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_data[1]=array('titulo'=>'<b>Ente Publico:</b>','contenido'=>$_SESSION["la_empresa"]["nombre"]);
		$la_data[2]=array('titulo'=>'<b>R.I.F.:</b>','contenido'=>$_SESSION["la_empresa"]["rifemp"]);
		$la_data[3]=array('titulo'=>'<b>Direccion:</b>','contenido'=>$_SESSION["la_empresa"]["direccion"]);
		$la_data[4]=array('titulo'=>'<b>Periodo:</b>','contenido'=>$as_perfiscal);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>870))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>Nro.(s) Planilla(s) Bancaria(s):</b> _________________________________');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>970))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);

	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

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

		$io_pdf->ezSetDy(-5);
		$la_columnas=array('numsop'=>'<b>No.</b>',
							 'numdocpag'=>'<b>Fecha de la Orden de Pago</b>',
							 'vacio1'=>'<b>No. Orden de Pago</b>',
							 'numcom'=>'<b>Nombre Contribuuyente</b>',
							 'nomban'=>'<b>C.I./RIF Contribuyente</b>',
							 'fecmov'=>'<b>Monto de la Obra de Servicio</b>',
							 'vacio2'=>'<b>Monto Bruto de la Orden</b>',
							 'nomsujret'=>'<b>Monto del Impuesto Retenido</b>',
							 'rif'=>'<b>Tipo de Pago</b>',
							 'totcmp_con_iva'=>'<b>Municipio</b>',
							 'totimp'=>'<b>Operaciones Anuladas o Reversadas</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numsop'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'numdocpag'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'vacio1'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'numcom'=>array('justification'=>'center','width'=>243), // Justificación y ancho de la columna
									   'nomban'=>array('justification'=>'center','width'=>100),// Justificación y ancho de la columna
									   'fecmov'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'vacio2'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'nomsujret'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'rif'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'totcmp_con_iva'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'totimp'=>array('justification'=>'center','width'=>105))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_pagina($as_total,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numsol    // Numero de la Solicitud de Pago
		//	   			   as_codigo    // Codigo del Proveedor / Beneficiario
		//	   			   as_nombre    // Nombre del Proveedor / Beneficiario
		//	   			   as_denfuefin // Denominacion de la fuente de financiamiento
		//	   			   ad_fecemisol // Fecha de Emision de la Solicitud
		//	   			   as_consol    // Concepto de la Solicitud
		//	   			   as_obssol    // Observaciones de la Solicitud
		//	   			   ai_monsol    // Monto de la Solicitud
		//	   			   as_monto     // Monto de la Solicitud en letras
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera 
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		//Instancio a la clase de conversión de numeros a letras.
		include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
		$numalet= new class_numero_a_letra();
		//imprime numero con los valore por defecto
		//cambia a minusculas
		$numalet->setMayusculas(1);
		//cambia a femenino
		$numalet->setGenero(1);
		//cambia moneda
		$numalet->setMoneda("Bolivares");
		//cambia prefijo
		$numalet->setPrefijo("***");
		//cambia sufijo
		$numalet->setSufijo("***");
		$numalet->setNumero($as_total);
		$ls_monto= $numalet->letra();
		$as_total= number_format($as_total,2,',','.');
		
		
		$la_data[1]=array('titulo'=>'','contenido'=>"Total Retenido:                           ".$as_total);
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>570), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'left','width'=>400))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'<b>TOTAL:'.$ls_monto);
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>970))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);

		$la_data[1]=array('titulo'=>'<b>Datos del Responsable de la Declaracion Agente de Retencion:</b>');
		$la_data[2]=array('titulo'=>'');
		$la_data[3]=array('titulo'=>'<b>Nombre y Apellido:     Marianela Golzalez</b>');
		$la_data[4]=array('titulo'=>'<b>Numero de C.I.:           V- 11432464</b>');
		$la_data[5]=array('titulo'=>'<b>Cargo:                         Gerente de Administracion y Finanzas</b>');
		$la_data[6]=array('titulo'=>'<b>Telefono:                     0416-7562611</b>');
		$la_data[7]=array('titulo'=>'<b>Correo Electronico:     marianela2828@hotmail.com</b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>970))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		$la_data[1]=array('titulo'=>'','contenido'=>"______________________________");
		$la_data[2]=array('titulo'=>'','contenido'=>"Lcda. Marianela Golzalez");
		$la_data[3]=array('titulo'=>'','contenido'=>"Gerente de Admon. y Finanzas");
		$la_columnas=array('titulo'=>'',
						   'contenido'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>570), // Justificación y ancho de la columna
						 			   'contenido'=>array('justification'=>'center','width'=>400))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);

	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------

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
	$ls_perfiscal=substr($ld_fecregdes,3,2)." - ".substr($ld_fecregdes,6,4);
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
			$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4.1,5,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(970,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numcom");
			$li_total_ivaret=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ls_numfac= $io_report->DS->data["numfac"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecmov"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["totcmp_con_iva"][$li_i];
				$li_totimp= $io_report->DS->data["totimp"][$li_i];
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$ls_fecemisol= $io_report->DS->data["fecemisol"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);
				$ls_fecemisol= $io_funciones->uf_convertirfecmostrar($ls_fecemisol);
				$rs_compromisos=$io_report->uf_select_compromisos_relacionados($ls_numsop);
				$li_montocompromiso=0;
				while(!$rs_compromisos->EOF)
				{
					$ls_numdoccom=$rs_compromisos->fields["numdoccom"];
					$ls_procede=$rs_compromisos->fields["procede_doc"];
				//	print $ls_numdoccom." ->".$ls_numsop."->".$ls_procede."<br>";
					if($ls_procede=="CXPRCD")
					{
						$li_compromiso=$io_report->uf_select_monto_recepcion($ls_numsop,$ls_numdoccom);
					}
					else
					{
						$li_compromiso=$io_report->uf_select_monto_compromisos($ls_numdoccom,$ls_procede);
					}
					$li_montocompromiso=$li_montocompromiso+$li_compromiso;
					$rs_compromisos->MoveNext();
				}
				if($li_montocompromiso==0)
				{
					$li_compromiso=$io_report->uf_select_monto_recepcion_contable($ls_numsop,$ls_numfac);
					$li_montocompromiso=$li_montocompromiso+$li_compromiso;
				}

//				$li_totaldoc= $li_totaldoc + $li_montotdoc;
//				$li_totalcar= $li_totalcar + $li_moncardoc;
//				$li_totalded= $li_totalded + $li_mondeddoc;
				if($li_totcmp_con_iva>=$li_montocompromiso)
				{
					$ls_tipopago="UNICO";
				}
				else
				{
					$ls_tipopago="PARCIAL";
				}
				$li_total_ivaret=$li_total_ivaret+$li_iva_ret;
				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,',','.');
				$li_totimp= number_format($li_totimp,2,',','.');
				$li_iva_ret= number_format($li_iva_ret,2,',','.');
				$li_montocompromiso= number_format($li_montocompromiso,2,',','.');

				$la_data[$li_i]=array('numsop'=>$li_i,'numdocpag'=>$ls_fecemisol,'vacio1'=>$ls_numsop,'numcom'=>$ls_nomsujret,'nomban'=>$ls_rif,
									  'fecmov'=>$li_montocompromiso,'vacio2'=>$li_totcmp_con_iva,'nomsujret'=>$li_iva_ret,'rif'=>$ls_tipopago,
									  'totcmp_con_iva'=>"IRIBARREN",'totimp'=>"");
			}
			uf_print_cabecera($ls_perfiscal,$io_pdf);
			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
			uf_print_detalle_recepcion($la_data,$io_pdf);
			uf_print_pie_pagina($li_total_ivaret,$io_pdf);
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
