<?PHP
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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
		//	    Arguments: as_titulo    // T?tulo del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_ins;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_ins->uf_load_seguridad_reporte("INS","sigesp_ins_r_compdescuadrado.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->rectangle(25,690,550,80);
		$io_pdf->line(15,40,585,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],30,695,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$as_titulo); // Agregar el t?tulo
		$io_pdf->addText(535,780,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(541,773,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($aa_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: aa_data // arreglo de informaci?n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por concepto
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 08/07/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_pdf->ezSetDy(-2);
		$la_data[1]=array('procede'=>"<b>Procede</b>",'comprobante'=>"<b>Comprobante</b>",'fecha'=>"<b>Fecha</b>",'debe'=>"<b>Debe</b>",'haber'=>"<b>Haber</b>");
		$la_columnas=array('procede'=>'<b>Solicitud</b>',
						   'comprobante'=>'<b>Proveedor / Beneficiario</b>',
						   'fecha'=>'<b>Fecha Emisi?n</b>',
						   'debe'=>'<b>Fecha Emisi?n</b>',
						   'haber'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>2, // Sombra entre l?neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('procede'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'comprobante'=>array('justification'=>'center','width'=>150), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'debe'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'center','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_columnas=array('procede'=>'<b>Solicitud</b>',
						   'comprobante'=>'<b>Proveedor / Beneficiario</b>',
						   'fecha'=>'<b>Fecha Emisi?n</b>',
						   'debe'=>'<b>Fecha Emisi?n</b>',
						   'haber'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 12,  // Tama?o de Letras de los t?tulos
						 'showLines'=>2, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('procede'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'comprobante'=>array('justification'=>'left','width'=>150), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>100), // Justificaci?n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($aa_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_ins_class_report.php");
	$io_report=new sigesp_ins_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				

	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	if(array_key_exists("procede",$_GET))
	{$ls_procede=$_GET["procede"];}
	else
	{$ls_procede="";}
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Listado de Comprobantes Descuadrados</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
	//$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_comprobantes($ls_procede); // Cargar el DS con los datos del reporte
	}
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_z=0;
		$ls_claveact="";
		$li_montodeb=0;
		$li_montohab=0;
		$li_reg=0;
		while(!$io_report->rs_data->EOF)
		{
			$ls_codemp=$io_report->rs_data->fields["codemp"];
			$ls_procede=$io_report->rs_data->fields["procede"];
			$ls_comprobante=$io_report->rs_data->fields["comprobante"];
			$ld_fecha=$io_report->rs_data->fields["fecha"];
			$ls_codban=$io_report->rs_data->fields["codban"];
			$ls_ctaban=$io_report->rs_data->fields["ctaban"];
			$ls_debhab=$io_report->rs_data->fields["debhab"];
			$li_monto=number_format($io_report->rs_data->fields["monto"],2,".","");
			$ls_clave=$ls_codemp."//".$ls_procede."//".$ls_comprobante."//".$ld_fecha."//".$ls_codban."//".$ls_ctaban;
			if($ls_clave!=$ls_claveact)
			{
				$li_montodeb=number_format($li_montodeb*100,2,".","");
				$li_montohab=number_format($li_montohab*100,2,".","");
				$li_total=$li_montodeb-$li_montohab;
				if($li_total<>0)
				{
					$li_z++;
					$ld_fecha=$io_funciones->uf_convertirfecmostrar($ld_fecha);
					$li_montodeb=number_format($li_montodeb/100,2,',','.');
					$li_montohab=number_format($li_montohab/100,2,',','.');
					$la_data[$li_z]=array('procede'=>$ls_procedeact,'comprobante'=>$ls_comprobanteact,'fecha'=>$ld_fechaact,'debe'=>$li_montodeb,'haber'=>$li_montohab);
				}
				$ls_claveact=$ls_clave;
				$ls_codempact=$ls_codemp;
				$ls_procedeact=$ls_procede;
				$ls_comprobanteact=$ls_comprobante;
				$ld_fechaact=$ld_fecha;
				$ls_codbanact=$ls_codban;
				$ls_ctabanact=$ls_ctaban;
				$li_montodeb=0;
				$li_montohab=0;
				if($ls_debhab=="D")
				{
					$li_montodeb=$li_montodeb + $li_monto;
				}
				else
				{
					$li_montohab=$li_montohab + $li_monto;
				}
			}
			else
			{
				if($ls_debhab=="D")
				{
					$li_montodeb=$li_montodeb + $li_monto;
				}
				else
				{
					$li_montohab=$li_montohab + $li_monto;
				}
			}
			$io_report->rs_data->MoveNext();
		}
		$li_montodeb=number_format($li_montodeb,2,".","");
		$li_montohab=number_format($li_montohab,2,".","");
		$li_total=$li_montodeb-$li_montohab;
		if($li_total<>0)
		{
			$li_z++;
			$ld_fecha=$io_funciones->uf_convertirfecmostrar($ld_fecha);
			$li_montodeb=number_format($li_montodeb,2,',','.');
			$li_montohab=number_format($li_montohab,2,',','.');
			$la_data[$li_z]=array('procede'=>$ls_procedeact,'comprobante'=>$ls_comprobanteact,'fecha'=>$ld_fechaact,'debe'=>$li_montodeb,'haber'=>$li_montohab);
		}
		
		
		if ($li_z>0)
		{
		 	 uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		  	 unset($la_data);
			if($lb_valido) // Si no ocurrio ning?n error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else  // Si hubo alg?n error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
		else
		{
		    print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");	
		    unset($io_pdf);
		}		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_ins);
?> 