<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_cmpmov,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_cmpmov // numero de comprobante de movimiento
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->rectangle(780,530,170,40);
		$io_pdf->line(780,550,950,550);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,545,11,"<b>".$as_titulo."</b>"); // Agregar el t?tulo
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=490;
		$io_pdf->addText(790,535,11,"Fecha:"); // Agregar la fecha
		$io_pdf->addText(830,535,11,$ad_fecha); // Agregar la fecha
		$io_pdf->addText(790,555,11,"No.:"); // Agregar la fecha
		$io_pdf->addText(830,555,11,$as_cmpmov); // Agregar la fecha
		$io_pdf->addText(908,580,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(914,573,7,date("h:i a")); // Agregar la Hora
		// cuadro inferior
        $io_pdf->Rectangle(50,40,900,70);
		$io_pdf->line(50,53,950,53);		
		$io_pdf->line(50,97,950,97);		
		$io_pdf->line(275,40,275,110);		
		$io_pdf->line(500,40,500,110);		
		$io_pdf->line(725,40,725,110);		
		$io_pdf->addText(130,102,7,"ELABORADO POR"); // Agregar el t?tulo
//		$io_pdf->addText(140,43,7,"COMPRAS"); // Agregar el t?tulo
		$io_pdf->addText(355,102,7,"VERIFICADO POR"); // Agregar el t?tulo
//		$io_pdf->addText(355,43,7,"PRESUPUESTO"); // Agregar el t?tulo
		$io_pdf->addText(580,102,7,"AUTORIZADO POR"); // Agregar el t?tulo
//		$io_pdf->addText(560,43,7,"ADMINISTRACI?N Y FINANZAS"); // Agregar el t?tulo
		$io_pdf->addText(815,102,7,"PROVEEDOR"); // Agregar el t?tulo
//		$io_pdf->addText(780,43,7,"FIRMA AUTOGRAFA, SELLO, FECHA"); // Agregar el t?tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($ls_codemp,$ls_nomemp,$ls_codcau,$ls_dencau,$ls_descmp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: ls_codemp // codigo de empresa
		//	    		   ls_nomemp // nombre de empresa
		//	    		   ls_codcau    // codigo de causa
		//	    		   ls_dencau    // denominacion de causa
		//	    		   ls_descmp    // descripcion del comprobante
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Organismo:</b>  '.$ls_codemp." - ".$ls_nomemp.''),
					   array ('name'=>'<b>Causa:</b>  '.$ls_codcau." - ".$ls_dencau.''),
					   array ('name'=>'<b>Observaciones:</b>  '.$ls_descmp.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar L?neas
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-5);
		global $ls_tipoformato;
		if($ls_tipoformato==0)
		{
		  $ls_titulo=" Bs.";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo=" Bs.F.";
		}
		$la_columna=array('fectraact'=>'<b>Fecha</b>',
						  'codact'=>'<b>C?digo</b>',
						  'denact'=>'<b>Activo</b>',
						  'ideact'=>'<b>Identificador</b>',
						  'desmov'=>'<b>Descripci?n del Movimiento</b>',
						  'codres'=>'<b>Responsable Actual</b>',
						  'coduniadm'=>'<b>Unidad Actual</b>',
						  'codresnew'=>'<b>Responsable Nuevo</b>',
						  'coduniadmnew'=>'<b>Unidad nueva</b>',
						  'monact'=>'<b>Monto '.$ls_titulo.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('fectraact'=>array('justification'=>'left','width'=>55), // Justificaci?n y ancho de la columna
						 			   'codact'=>array('justification'=>'left','width'=>80),   // Justificaci?n y ancho de la columna
						 			   'denact'=>array('justification'=>'left','width'=>150),  // Justificaci?n y ancho de la columna
						 			   'ideact'=>array('justification'=>'left','width'=>80),   // Justificaci?n y ancho de la columna
						 			   'desmov'=>array('justification'=>'left','width'=>160),  // Justificaci?n y ancho de la columna
						 			   'codres'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
						 			   'coduniadm'=>array('justification'=>'left','width'=>70), // Justificaci?n y ancho de la columna
						 			   'codresnew'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
						 			   'coduniadmnew'=>array('justification'=>'left','width'=>70), // Justificaci?n y ancho de la columna
						 			   'monact'=>array('justification'=>'right','width'=>95))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>Detalle de Activos</b>',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detallecontable($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detallecontable
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tipoformato;
		if($ls_tipoformato==0)
		{
		  $ls_titulo=" Bs.";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo=" Bs.F.";
		}
		$la_columna=array('cuenta'=>'<b>Cuenta Contable</b>',
						  'documento'=>'<b>Documento</b>',
						  'debhab'=>'<b>Debe/Haber</b>',
						  'monto'=>'<b>Monto '.$ls_titulo.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>225), // Justificaci?n y ancho de la columna
						 			   'documento'=>array('justification'=>'left','width'=>225), // Justificaci?n y ancho de la columna
						 			   'debhab'=>array('justification'=>'center','width'=>225), // Justificaci?n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>225))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>Detalle Contable</b>',$la_config);
	}// end function uf_print_detallecontable
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('total'=>'',
						  'monact'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 11,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>805), // Justificaci?n y ancho de la columna
						 			   'monact'=>array('justification'=>'right','width'=>95))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totalescontable($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totalescontable
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('total'=>'',
						  'debe'=>'',
						  'haber'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 11,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>600),
						 			   'debe'=>array('justification'=>'right','width'=>150), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>150))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
		$ls_titulo_report="Bs.F.";
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
		$ls_titulo_report="Bs.";
	}	
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_fecrec=$io_fun_activos->uf_obtenervalor_get("fecrec","");

	$ls_titulo="<b>Comprobante de Reasignaci?n en ".$ls_titulo_report."</b>";
	$ls_fecha=$ls_fecrec;
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$ls_cmpmov=$io_fun_activos->uf_obtenervalor_get("cmpmov","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_saf_load_movimiento($ls_codemp,$ls_cmpmov,"","","R","","",""); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,5,3,3); // Configuraci?n de los margenes en cent?metros
		$ld_fecha=$io_report->ds->data["feccmp"][1];
		$ld_fechaaux=$io_funciones->uf_convertirfecmostrar($ld_fecha);
		$li_totrow=$io_report->ds->getRowCount("cmpmov");
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
	        $io_pdf->transaction('start'); // Iniciamos la transacci?n
			$li_numpag=$io_pdf->ezPageCount; // N?mero de p?gina
			$li_totprenom=0;
			$li_totant=0;
			$ls_codcau=$io_report->ds->data["codcau"][$li_i];
			$ls_dencau=$io_report->ds->data["dencau"][$li_i];
			$ls_descmp=$io_report->ds->data["descmp"][$li_i];
			$ls_numcmp=$io_report->ds->data["numcmp"][$li_i];
			uf_print_encabezado_pagina($ls_titulo,$ls_numcmp,$ld_fechaaux,$io_pdf); // Imprimimos el encabezado de la p?gina
			uf_print_cabecera($ls_codemp,$ls_nomemp,$ls_codcau,$ls_dencau,$ls_descmp,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_siv_load_dt_movreasignacion($ls_codemp,$ls_cmpmov,$ls_codcau); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_montot=0;
				$li_totrow_det=$io_report->ds_detalle->getRowCount("codact");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ld_fectraact= $io_report->ds_detalle->data["fectraact"][$li_s];
					$ls_codact=    $io_report->ds_detalle->data["codact"][$li_s];
					$ls_denact=    $io_report->ds_detalle->data["denact"][$li_s];
					$ls_ideact=    $io_report->ds_detalle->data["ideact"][$li_s];
					$ls_desmov=    $io_report->ds_detalle->data["desmov"][$li_s];
					$ls_codres=    $io_report->ds_detalle->data["codres"][$li_s];
					$ls_coduniadm= $io_report->ds_detalle->data["coduniadm"][$li_s];
					$ls_codresnew= $io_report->ds_detalle->data["codresnew"][$li_s];
					$ls_coduniadmnew= $io_report->ds_detalle->data["coduniadmnew"][$li_s];
					$li_monact=    $io_report->ds_detalle->data["monact"][$li_s];
					$li_montot=$li_montot+$li_monact;
					$li_monact=$io_fun_activos->uf_formatonumerico($li_monact);
					$ld_fectraact=$io_funciones->uf_convertirfecmostrar($ld_fectraact);
					$la_dataa[$li_s]=array('fectraact'=>$ld_fectraact,'codact'=>$ls_codact,'denact'=>$ls_denact,'ideact'=>$ls_ideact,'desmov'=>$ls_desmov,
										  'codres'=>$ls_codres,'coduniadm'=>$ls_coduniadm,'codresnew'=>$ls_codresnew,'coduniadmnew'=>$ls_coduniadmnew,'monact'=>$li_monact);
				}
				$li_montot=$io_fun_activos->uf_formatonumerico($li_montot);
				uf_print_detalle($la_dataa,$io_pdf); // Imprimimos el detalle 
				$la_datat[1]=array('total'=>"Total",'monact'=>$li_montot);
				uf_print_totales($la_datat,$io_pdf);
				$lb_valido=$io_report->uf_saf_load_dt_contable($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_fecha); // Obtenemos el detalle del reporte
				$la_data="";
				if($lb_valido)
				{
					$li_montotdeb=0;
					$li_montothab=0;
					$li_totrow_det=$io_report->ds_detcontable->getRowCount("sc_cuenta");
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
					{
						$ls_cuenta=    $io_report->ds_detcontable->data["sc_cuenta"][$li_s];
						$ls_documento= $io_report->ds_detcontable->data["documento"][$li_s];
						$ls_debhab=    $io_report->ds_detcontable->data["debhab"][$li_s];
						$li_monto=     $io_report->ds_detcontable->data["monto"][$li_s];
						if($ls_debhab=="D")
						{$li_montotdeb=$li_montotdeb+$li_monto;}
						else
						{$li_montothab=$li_montothab+$li_monto;}
						$li_monto=$io_fun_activos->uf_formatonumerico($li_monto);
						$la_data[$li_s]=array('cuenta'=>$ls_cuenta,'documento'=>$ls_documento,'debhab'=>$ls_debhab,'monto'=>$li_monto);
					}
					if($la_data!="")
					{
						$li_montotdeb=$io_fun_activos->uf_formatonumerico($li_montotdeb);
						$li_montothab=$io_fun_activos->uf_formatonumerico($li_montothab);
						$la_datatc[1]=array('total'=>"Total",'debe'=>"Debe ".$li_montotdeb,'haber'=>"Haber ".$li_montothab);
						uf_print_detallecontable($la_data,$io_pdf); // Imprimimos el detalle 
						uf_print_totalescontable($la_datatc,$io_pdf);
					}
				}


				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				}
				else
				{// Hacemos un rollback de los registros, agregamos una nueva p?gina y volvemos a imprimir
					$io_pdf->transaction('rewind');
					if($li_numpag!=1)
					{
						$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					}
					uf_print_encabezado_pagina($ls_titulo,$ls_numcmp,$ld_fechaaux,$io_pdf); // Imprimimos el encabezado de la p?gina
					uf_print_cabecera($ls_codemp,$ls_nomemp,$ls_codcau,$ls_dencau,$ls_descmp,$io_pdf); // Imprimimos la cabecera del registro
					uf_print_detalle($la_dataa,$io_pdf); // Imprimimos el detalle 
				}//
			}
			unset($la_data);			
		}
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 