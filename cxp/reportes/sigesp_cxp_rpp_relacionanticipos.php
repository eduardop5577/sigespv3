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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_relacionfacturas.php",$ls_descripcion);
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

		global $io_funciones;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,775,40);
        $io_pdf->Rectangle(15,530,753,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,570,11,$as_titulo); // Agregar el título
		$io_pdf->addText(740,598,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(746,591,6,date("h:i a")); // Agregar la Hora
		if(($ld_fecregdes!="")&&($ld_fecregdes!=""))
		{
			$ld_fecregdes=$io_funciones->uf_convertirfecmostrar($ld_fecregdes);
			$ld_fecreghas=$io_funciones->uf_convertirfecmostrar($ld_fecreghas);
			$ls_titfecha="Del ".$ld_fecregdes." al ".$ld_fecreghas;
			$li_tm=$io_pdf->getTextWidth(10,$ls_titfecha);
			$io_pdf->addText($tm,550,11,$ls_titfecha); // Agregar el título
		}
		// cuadro inferior
//        $io_pdf->Rectangle(15,60,753,70);
//		$io_pdf->line(15,73,768,73);		
//		$io_pdf->line(15,117,768,117);		
//		$io_pdf->line(203,60,203,130);		
//		$io_pdf->line(391,60,391,130);		
//		$io_pdf->line(579,60,579,130);		
//		$io_pdf->addText(80,122,7,"ELABORADO POR"); // Agregar el título
//		$io_pdf->addText(82,63,7,"FIRMA / SELLO"); // Agregar el título
//		$io_pdf->addText(262,122,7,"VERIFICADO POR"); // Agregar el título
//		$io_pdf->addText(252,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
//		$io_pdf->addText(460,122,7,"AUTORIZADO POR"); // Agregar el título
//		$io_pdf->addText(440,63,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
//		$io_pdf->addText(635,122,7,"CONTRALORIA INTERNA"); // Agregar el título
//		$io_pdf->addText(635,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codigo,$as_nombre,$as_tipproben,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codigo    // Codigo de Proveedor / Beneficiario
		//	   			   as_nombre    // Nombre de Proveedor / Beneficiario
		//	   			   as_tipproben // Tipo de Proveedor / Beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$la_data[1]=array('titulo'=>'<b> Nombre de Cliente:          </b>'.$ls_nombre);
		if($as_tipproben=="B")
		{
			$la_data[2]=array('titulo'=>'<b> Beneficiario:          </b>'.$as_codigo.' - '.$as_nombre);
		}
		else
		{
			$la_data[2]=array('titulo'=>'<b> Proveedor:          </b>'.$as_codigo.' - '.$as_nombre);
		}
		$la_data[3]=array('titulo'=>'<b> Rif:          </b>'.$ls_rifemp);
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'left','width'=>750))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);

		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$li_totmonant,$li_totmontotdoc,$li_totmonretiva,$li_totmonretislr,$li_totmonretmun,$li_totmonretmil,$li_totmonantamo,
								 $li_totmonsinant,$li_totsaldo,$li_totmonfact,$ls_totmonto,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   ai_j // numero de registros
		//				   ai_totalfacpro // acumulado de los montos
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_columnas=array('tipo'=>'Tipo','numrecdoc'=>'Recepcion de Documentos','codant'=>'Asociada al anticipo','numsol'=>'Orden de Pago','cheques'=>'Documento de Pago',
							  'fecemidoc'=>'Fecha Emision','fecvendoc'=>'Fecha Vencimiento','dias'=>'Dias Vencidos','monant'=>'Monto Anticipo','montotdoc'=>'Monto Facturado','monretiva'=>'Retenciones IVA',
							  'monretislr'=>'Retencion ISLR','monretmun'=>'Retencion Municipal','monretmil'=>'Retencion 1x1000','monantamo'=>'Monto Amortizado','monsinant'=>'Monto Menos Anticipo',
							  'monto'=>'Pagado en Banco','saldo'=>'Saldo','estpag'=>'Estatus');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'titleFontSize' => 5,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tipo'=>array('justification'=>'left','width'=>21), // Justificación y ancho de la columna
						 			   'numrecdoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'codant'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'numsol'=>array('justification'=>'center','width'=>58), // Justificación y ancho de la columna
						 			   'cheques'=>array('justification'=>'center','width'=>58), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'fecvendoc'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'dias'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna
						 			   'monant'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
						 			   'montotdoc'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'monretiva'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
						 			   'monretislr'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monretmun'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monretmil'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monantamo'=>array('justification'=>'right','width'=>42), // Justificación y ancho de la columna
						 			   'monsinant'=>array('justification'=>'right','width'=>42), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'saldo'=>array('justification'=>'right','width'=>44), // Justificación y ancho de la columna
						 			   'estpag'=>array('justification'=>'right','width'=>35))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_datatot[1]=array('tipo'=>'','numrecdoc'=>'','codant'=>'','numsol'=>'','cheques'=>'',
							  'fecemidoc'=>'','fecvendoc'=>'','dias'=>'','monant'=>$li_totmonant,'montotdoc'=>$li_totmontotdoc,'monretiva'=>$li_totmonretiva,
							  'monretislr'=>$li_totmonretislr,'monretmun'=>$li_totmonretmun,'monretmil'=>$li_totmonretmil,'monantamo'=>$li_totmonantamo,'monsinant'=>$li_totmonsinant,
							  'monto'=>$ls_totmonto,'saldo'=>$li_totsaldo,'estpag'=>'');
		$la_columnas=array('tipo'=>'Tipo','numrecdoc'=>'Recepcion de Documentos','codant'=>'Asociada al anticipo','numsol'=>'Orden de Pago','cheques'=>'Documento de Pago',
							  'fecemidoc'=>'Fecha Emision','fecvendoc'=>'Fecha Vencimiento','dias'=>'Dias Vencidos','monant'=>'Monto Anticipo','montotdoc'=>'Monto Facturado','monretiva'=>'Retenciones IVA',
							  'monretislr'=>'Retencion ISLR','monretmun'=>'Retencion Municipal','monretmil'=>'Retencion 1x1000','monantamo'=>'Monto Amortizado','monsinant'=>'Monto Menos Anticipo',
							  'monto'=>'Pagado en Banco','saldo'=>'Saldo','estpag'=>'Estatus');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'titleFontSize' => 5,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('tipo'=>array('justification'=>'left','width'=>21), // Justificación y ancho de la columna
						 			   'numrecdoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'codant'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'numsol'=>array('justification'=>'center','width'=>58), // Justificación y ancho de la columna
						 			   'cheques'=>array('justification'=>'center','width'=>58), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'fecvendoc'=>array('justification'=>'center','width'=>38), // Justificación y ancho de la columna
						 			   'dias'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna
						 			   'monant'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
						 			   'montotdoc'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'monretiva'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
						 			   'monretislr'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monretmun'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monretmil'=>array('justification'=>'right','width'=>38), // Justificación y ancho de la columna
						 			   'monantamo'=>array('justification'=>'right','width'=>42), // Justificación y ancho de la columna
						 			   'monsinant'=>array('justification'=>'right','width'=>42), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'saldo'=>array('justification'=>'right','width'=>44), // Justificación y ancho de la columna
						 			   'estpag'=>array('justification'=>'right','width'=>35))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatot,$la_columnas,'',$la_config);
//		$io_pdf->ezTable($la_datatot,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
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
		
	if($ls_estmodest==1)
	{
		$ls_titcuentas="Estructura Presupuestaria";
	}
	else
	{
		$ls_titcuentas="Estructura Programatica";
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>RELACION DE ANTICIPOS</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$rs_data=$io_report->uf_select_probenrelacionanticipos($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fecregdes,$ld_fecreghas); 
		if($rs_data==="") // Existe algún error ó no hay registros
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
			$io_pdf->ezSetCmMargins(3,3,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(770,47,8,'','',1); // Insertar el número de página
			$li_totaldoc=0;
			$li_totalcar=0;
			$li_totalded=0;
			uf_print_encabezado_pagina($ls_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf);
			while (!$rs_data->EOF)
			{
				$ls_codigo= $rs_data->fields["codigo"];
				$ls_nombre= $rs_data->fields["nombre"];
				$ls_tipproben= $rs_data->fields["tipproben"];
				uf_print_cabecera($ls_codigo,$ls_nombre,$ls_tipproben,$io_pdf);
				$rs_datadetalle=$io_report->uf_select_facturasanticipos($ls_tipproben,$ls_codigo,$ld_fecregdes,$ld_fecreghas);
				$li_j=0;
				$li_i=0;
				$li_saldo=0;
				$li_totmonant=0;
				$li_totmontotdoc=0;	
				$li_totmonretiva=0;
				$li_totmonretislr=0;
				$li_totmonretmun=0;
				$li_totmonretmil=0;
				$li_totmonantamo=0;
				$li_totmonsinant=0;
				$li_totsaldo=0;
				$li_totmonfact=0;
				$ls_totmonto=0;
				while (!$rs_datadetalle->EOF)
				{
					$ls_numrecdoc= $rs_datadetalle->fields["numrecdoc"];
					$ls_codtipdoc= $rs_datadetalle->fields["codtipdoc"];
					$ls_fecregdoc= $rs_datadetalle->fields["fecregdoc"];
					$ld_fecemidoc= $rs_datadetalle->fields["fecemidoc"];
					$ld_fecvendoc= $rs_datadetalle->fields["fecvendoc"];
					$li_montotdoc= $rs_datadetalle->fields["montotdoc"];
					$li_moncardoc= $rs_datadetalle->fields["moncardoc"];
					$ls_numsol= $rs_datadetalle->fields["numsol"];
					$ls_codant= $rs_datadetalle->fields["codant"];
					$li_monant= $rs_datadetalle->fields["monant"];
					$ls_codantamo= $rs_datadetalle->fields["codantamo"];
					$li_monantamo= $rs_datadetalle->fields["monantamo"];
					if($ls_tipproben=="B")
					{
						$ls_codpro="----------";
						$ls_cedbene=$ls_codigo;
					}
					else
					{
						$ls_cedbene="----------";
						$ls_codpro=$ls_codigo;
					}
					$li_monretiva=$io_report->uf_datos_deduccionrecepcion($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene,"IVA");
					$li_monretislr=$io_report->uf_datos_deduccionrecepcion($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene,"ISLR");
					$li_monretmun=$io_report->uf_datos_deduccionrecepcion($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene,"MUNICIPAL");
					$li_monretmil=$io_report->uf_datos_deduccionrecepcion($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene,"MIL");
					$la_pagos=$io_report->uf_select_pagosrelacionados($ls_numsol);
					$ls_cheques="";
					$ls_monto=0;
					if($la_pagos!="")
					{
						$ls_cheques=$la_pagos["numdoc"];
						$ls_monto=$la_pagos["monto"];
					}
					if($ls_codant!="")
						$ls_tipo="ANT";
					else
						$ls_tipo="FAC";
					
					if($ls_cheques!="")
						$ls_estpag="PAGADO";
					else
						$ls_estpag="CONTAB.";
					$li_j++;
					$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
					$ld_fecvendoc=$io_funciones->uf_convertirfecmostrar($ld_fecvendoc);
					$ld_fecact=date("d/m/Y");
					$ld_dias=$io_fecha->uf_restar_fechas($ld_fecvendoc,$ld_fecact,true);
					if($ld_dias>0)
						$ld_dias=0;
					$ld_dias=abs($ld_dias);
					$li_monfact=$li_montotdoc-$li_monant;
					$li_monsinant=$li_monfact-$li_monantamo;
					if($ls_tipo=="ANT")
						$li_saldo=$li_saldo-$ls_monto;
					else
						$li_saldo=$li_saldo-$li_monsinant+$li_montotdoc;
					
					$li_totmonant=$li_totmonant+$li_monant;
					$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;	
					$li_totmonretiva=$li_totmonretiva+$li_monretiva;
					$li_totmonretislr=$li_totmonretislr+$li_monretislr;
					$li_totmonretmun=$li_totmonretmun+$li_monretmun;
					$li_totmonretmil=$li_totmonretmil+$li_monretmil;
					$li_totmonantamo=$li_totmonantamo+$li_monantamo;
					$li_totmonsinant=$li_totmonsinant+$li_monsinant;
					$li_totsaldo=$li_totsaldo+$li_saldo;
					$li_totmonfact=$li_totmonfact+$li_monfact;
					$ls_totmonto=$ls_totmonto+$ls_monto;
					if($li_monretiva=="")
						$li_monretiva=0;
					if($li_monretislr=="")
						$li_monretislr=0;
					if($li_monretmun=="")
						$li_monretmun=0;
					if($li_monretmil=="")
						$li_monretmil=0;
					if($li_monantamo=="")
						$li_monantamo=0;
					if($li_monsinant=="")
						$li_monsinant=0;

					$li_monant=number_format($li_monant,2,',','.');	
					$li_montotdoc=number_format($li_montotdoc,2,',','.');	
					$li_monretiva=number_format($li_monretiva,2,',','.');	
					$li_monretislr=number_format($li_monretislr,2,',','.');	
					$li_monretmun=number_format($li_monretmun,2,',','.');	
					$li_monretmil=number_format($li_monretmil,2,',','.');	
					$li_monantamo=number_format($li_monantamo,2,',','.');	
					$li_monsinant=number_format($li_monsinant,2,',','.');	
					$li_saldoaux=number_format($li_saldo,2,',','.');	
					$li_monfact=number_format($li_monfact,2,',','.');	
					$ls_monto=number_format($ls_monto,2,',','.');
					if($ls_codant=="")
						$ls_codant=	$ls_codantamo;
					$la_data[$li_j]=array('tipo'=>$ls_tipo,'numrecdoc'=>$ls_numrecdoc,'codant'=>$ls_codant,'numsol'=>$ls_numsol,'cheques'=>$ls_cheques,
										  'fecemidoc'=>$ld_fecemidoc,'fecvendoc'=>$ld_fecvendoc,'dias'=>$ld_dias,'monant'=>$li_monant,'montotdoc'=>$li_monfact,'monretiva'=>$li_monretiva,
										  'monretislr'=>$li_monretislr,'monretmun'=>$li_monretmun,'monretmil'=>$li_monretmil,'monantamo'=>$li_monantamo,'monsinant'=>$li_monsinant,
										  'monto'=>$ls_monto,'saldo'=>$li_saldoaux,'estpag'=>$ls_estpag);
			
			
					$rs_datadetalle->MoveNext();	
				}
				$li_totmonant=number_format($li_totmonant,2,',','.');	
				$li_totmontotdoc=number_format($li_totmontotdoc,2,',','.');	
				$li_totmonretiva=number_format($li_totmonretiva,2,',','.');	
				$li_totmonretislr=number_format($li_totmonretislr,2,',','.');	
				$li_totmonretmun=number_format($li_totmonretmun,2,',','.');	
				$li_totmonretmil=number_format($li_totmonretmil,2,',','.');	
				$li_totmonantamo=number_format($li_totmonantamo,2,',','.');	
				$li_totmonsinant=number_format($li_totmonsinant,2,',','.');	
				$li_totsaldo=number_format($li_totsaldo,2,',','.');	
				$li_totmonfact=number_format($li_totmonfact,2,',','.');	
				$ls_totmonto=number_format($ls_totmonto,2,',','.');
				uf_print_detalle($la_data,$li_totmonant,$li_totmontotdoc,$li_totmonretiva,$li_totmonretislr,$li_totmonretmun,$li_totmonretmil,$li_totmonantamo,
								 $li_totmonsinant,$li_saldoaux,$li_totmonfact,$ls_totmonto,$io_pdf);
				$rs_data->MoveNext();	
				$li_i++;
				if(!$rs_data->EOF)
				{
					$io_pdf->ezNewPage(); // Insertar una nueva página
				}
				unset($la_data);
			}
			
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
			unset($io_pdf);
		}
		
	}
	unset($io_report);
	unset($io_funciones);
?>
