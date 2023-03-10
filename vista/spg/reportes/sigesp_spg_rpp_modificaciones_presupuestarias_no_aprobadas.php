<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
	function uf_print_cabecera_detalle($io_encabezado,$as_descripcion,$as_numdoc,$as_fecmov,$as_tipmodpre,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabezera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
	    $io_pdf->saveState();
	    $io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],40,715,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(50,680,10,"<b>Tipo Modificación:</b> ".$as_tipmodpre);
		$ls_texto_1 = "<b>MODIFICACIONES PRESUPUESTARIAS NO APROBADAS</b>";

		$ls_texto_3 = "<b>Descripción : </b>";

		$as_descripcion2 = $io_pdf->addTextWrap(46,665,350,8,$ls_texto_3.$as_descripcion);
		$as_descripcion3 = $io_pdf->addTextWrap(46,655,350,8,$as_descripcion2);
	    $as_descripcion4 = $io_pdf->addTextWrap(46,645,350,8,$as_descripcion3);
	    $as_descripcion5 = $io_pdf->addTextWrap(46,635,350,8,$as_descripcion4);
	    $as_descripcion6 = $io_pdf->addTextWrap(46,625,350,8,$as_descripcion5);
	    $as_descripcion7 = $io_pdf->addTextWrap(46,615,350,8,$as_descripcion6);
	    $as_descripcion8 = $io_pdf->addTextWrap(46,605,350,8,$as_descripcion7);
		$io_pdf->addTextWrap(46,595,350,7,$as_descripcion8);


		$li_tm=$io_pdf->getTextWidth(14,$ls_texto_1);
		$tm=296-($li_tm/2);
		$io_pdf->addText($tm,698,14,$ls_texto_1); // Agregar el título

		$io_pdf->addText(415,660,10,'N°: '.$as_numdoc);
		$io_pdf->addText(415,640,10,'Fecha Registro     : '.$as_fecmov);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(410,675,410,580);

		$io_pdf->rectangle(40,585,550,90);

	    $io_pdf->ezSetY(586.8);
	    $la_datatitulos= array(array('estructura'=>"<b>Est. Presup.</b>",'especifica'=>"<b>ESPECIFICA</b>",
		                             'denominacion'=>"<b>DENOMINACION</b>",'cedente'=>"<b>CEDENTE</b>",
									 'receptora'=>"<b>RECEPTORA</b>"));

		$la_columna=array('estructura'=>'<b>Est. Presup.</b>',
						  'especifica'=>'<b>ESPECIFICA</b>',
						  'denominacion'=>'<b>CEDENTE</b>',
						  'cedente'=>'<b>CEDENTE</b>',
						  'receptora'=>'<b>RECEPTORA</b>');

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'center','width'=>210), // Justificación y ancho de la columna
									   'cedente'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
									   'receptora'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatitulos,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabezera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$io_pdf->ezSetCmMargins(5,6.5,3,3); // Configuración de los margenes en centímetros
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('estructura'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'especifica'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>210),
									   'cedente'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
									   'receptora'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna

		$la_columnas=array('estructura'=>'',
						   'especifica'=>'<b>ESPECIFICA</b>',
						   'denominacion'=>'<b>DENOMINACION</b>',
						   'cedente'=>'<b>CEDENTE</b>',
						   'receptora'=>'<b>RECEPTORA</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_totalaumento,$ad_totaldismi,$as_denominacion,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total   '.$as_denominacion.'</b>','disminucion'=>$ad_totaldismi,'aumento'=>$ad_totalaumento));
		$la_columna=array('total'=>'','disminucion'=>'','aumento'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre líneas
						 'width'=>550, // Ancho Máximo de la tabla
						 'fontSize' => 9, // Tamaño de Letras
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>320, // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>370), // Justificación y ancho de la columna
						 			   'disminucion'=>array('justification'=>'right','width'=>90),  // Justificación y ancho de la columna
									   'aumento'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
        $io_pdf->ezSetDy(-30);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("sigesp_spg_funciones_reportes.php");
		$io_funciones		= new class_funciones();
		$io_fecha 			= new class_fecha();
		$io_function_report = new sigesp_spg_funciones_reportes();
		
	//-----------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();
		
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
        $ls_ckbrect		= $_GET["ckbrect"];
        $ls_ckbtras		= $_GET["ckbtras"];
        $ls_ckbinsu		= $_GET["ckbinsu"];
        $ls_ckbcre		= $_GET["ckbcre"];
		$ls_comprobante = $_GET["txtcomprobante"];
		$ls_procede  	= $_GET["txtprocede"];
		$ldt_fecha  	= $_GET["txtfecha"];
		$fecdes			= $_GET["txtfecdes"];
		$ldt_fecdes		= $io_funciones->uf_convertirdatetobd($fecdes);
		$fechas			= $_GET["txtfechas"];
		$ldt_fechas		= $io_funciones->uf_convertirdatetobd($fechas);
		$ldt_fecha		= $io_funciones->uf_convertirdatetobd($ldt_fecha);
		$li_estmodest   = $_SESSION["la_empresa"]["estmodest"];
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Modificaciones Presupuestarias Aprobadas desde la fecha ".$fecdes." hasta ".$fechas.", Comprobante  ".$ls_comprobante." ,Procede  ".$ls_procede." ,Fecha del Comprobante  ".$ldt_fecha;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_modificaciones_presupuestarias.html",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
        // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	    $lb_valido=$io_report->uf_spg_reporte_modificaciones_presupuestarias_no_aprobadas($ls_ckbrect,$ls_ckbtras,$ls_ckbinsu,$ls_ckbcre,$ldt_fecdes,
																		                  $ldt_fechas,$ls_comprobante,$ls_procede,$ldt_fecha);
		if ($lb_valido==false) // Existe algún error ó no hay registros
	       {
		     print("<script language=JavaScript>");
			 print(" alert('No hay nada que Reportar');");
			 print(" close();");
			 print("</script>");
	       }
	    else // Imprimimos el reporte
	 	   {
	   		 
			 set_time_limit(1800);
			 $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			 $io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			 $io_pdf->ezSetCmMargins(7.7,3,3,3); // Configuración de los margenes en centímetros
			 $io_pdf->ezStartPageNumbers(550,50,8,'','',1); // Insertar el número de página
			 $io_report->dts_reporte->group_noorder("procomp");
			 $li_tot		  = $io_report->dts_reporte->getRowCount("spg_cuenta");
			 $ld_totalaumento = 0;
			 $ld_totaldismi   = 0;
			 $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		     $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		     $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		     $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		     $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
			 for ($z=1;$z<=$li_tot;$z++)
				 {
				   $io_pdf->transaction('start'); // Iniciamos la transacción
		    	   $li_tmp=($z+1);
				   $thisPageNum	   = $io_pdf->ezPageCount;
				   $ls_procede	   = $io_report->dts_reporte->data["procede"][$z];
				   $ls_procomp     = $io_report->dts_reporte->data["procomp"][$z];
				   $ls_comprobante = $io_report->dts_reporte->data["comprobante"][$z];
				   if ($z<$li_tot)
		              {
					    $ls_procomp_next=$io_report->dts_reporte->data["procomp"][$li_tmp];
		    	      }
		    	   elseif($z=$li_tot)
		    		  {
						$ls_procomp_next='no_next';
		              }
			       if (!empty($ls_procomp))
				  	  {
					    $ls_procomp_ant=$io_report->dts_reporte->data["procomp"][$z];
					  }
			       $ls_descripcion  = trim($io_report->dts_reporte->data["cmp_descripcion"][$z]);
			       $ls_codestpro    = $io_report->dts_reporte->data["programatica"][$z];
		 	       if ($li_estmodest=='1')
			          {
					    $ls_codestpro1 = substr($ls_codestpro,0,25);
						$ls_codestpro2 = substr($ls_codestpro,25,25);
						$ls_codestpro3 = substr($ls_codestpro,50,25);
						//$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
						$ls_codestpro=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
			          }
				   else
					  {
					    $ls_codestpro1 = substr($ls_codestpro,0,25);
						$ls_codestpro2 = substr($ls_codestpro,25,25);
						$ls_codestpro3 = substr($ls_codestpro,50,25);
						$ls_codestpro4 = substr($ls_codestpro,75,25);
						$ls_codestpro5 = substr($ls_codestpro,100,25);
						//$ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
						$ls_codestpro=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
					  }
			       $ls_especifica   = $io_report->dts_reporte->data["spg_cuenta"][$z];
				   $ls_denominacion = trim($io_report->dts_reporte->data["denominacion"][$z]);
				   $ls_documento	= $io_report->dts_reporte->data["documento"][$z];
				   $ldt_fecha_bd	= $io_report->dts_reporte->data["fecha"][$z];
				   $ldt_fecha_bd=date("Y-m-d",strtotime($ldt_fecha_bd));
				   $ldt_fecha		= $io_funciones->uf_convertirfecmostrar($ldt_fecha_bd);
				   $ld_aumento		= $io_report->dts_reporte->data["aumento"][$z];
				   $ld_disminucion  = $io_report->dts_reporte->data["disminucion"][$z];
			       if ($ls_procede=="SPGREC")
			          {
			            $ls_proc="RECTIFICACIONES";
			          }
			       if ($ls_procede=="SPGINS")
			          {
			            $ls_proc="INSUBSISTENCIAS";
			          }
			       if ($ls_procede=="SPGTRA")
					  {
					    $ls_proc="TRASPASOS";
					  }
			       if ($ls_procede=="SPGCRA")
					  {
					    $ls_proc="CREDITOS/INGRESOS ADICIONALES";
					  }
		           $ld_totalaumento = ($ld_totalaumento+$ld_aumento);
		           $ld_totaldismi   = ($ld_totaldismi+$ld_disminucion);
				   if (!empty($ls_procomp))
		              {
						$la_data[$z]  = array('estructura'=>$ls_codestpro,
											  'especifica'=>$ls_especifica,
											  'denominacion'=>$ls_denominacion,
											  'cedente'=>number_format($ld_disminucion,2,',','.'),
											  'receptora'=>number_format($ld_aumento,2,',','.'));
			          }
			       else
			          {
						$la_data[$z]=array('estructura'=>$ls_codestpro,
										   'especifica'=>$ls_especifica,
										   'denominacion'=>$ls_denominacion,
										   'cedente'=>number_format($ld_disminucion,2,',','.'),
										   'receptora'=>number_format($ld_aumento,2,',','.'));
					  }
		 	       if (!empty($ls_procomp_next))
			          {
						 $la_data[$z]=array('estructura'=>$ls_codestpro,
										    'especifica'=>$ls_especifica,
										    'denominacion'=>$ls_denominacion,
										    'cedente'=>number_format($ld_disminucion,2,',','.'),
										    'receptora'=>number_format($ld_aumento,2,',','.'));
						 $io_encabezado=$io_pdf->openObject();
						 uf_print_cabecera_detalle($io_encabezado,$ls_descripcion,$ls_comprobante,$ldt_fecha,$ls_proc,$io_pdf);
						 uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
						 $ld_totalaumento=number_format($ld_totalaumento,2,",",".");
						 $ld_totaldismi=number_format($ld_totaldismi,2,",",".");
						 uf_print_pie_cabecera($ld_totalaumento,$ld_totaldismi,'Bs.',$io_pdf);

						
						 $ld_totalaum=$ld_totalaumento;
						 $ld_totaldis=$ld_totaldismi;
						 $ld_totalaumento=0;
						 $ld_totaldismi=0;
						 $io_pdf->stopObject($io_encabezado);
						 if($z<$li_tot)
						 {
						   $io_pdf->ezNewPage(); // Insertar una nueva página
						 }
						 unset($la_data);
			          }
	             }
				$io_pdf->ezStopPageNumbers(1,1);
				if (isset($d) && $d)
				{
					$ls_pdfcode = $io_pdf->ezOutput(1);
					$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
					echo '<html><body>';
					echo trim($ls_pdfcode);
					echo '</body></html>';
				}
				else
				{
					$io_pdf->ezStream();
				}
				unset($io_pdf);
			}
			unset($io_report);
			unset($io_funciones);
			unset($io_function_report);
?>