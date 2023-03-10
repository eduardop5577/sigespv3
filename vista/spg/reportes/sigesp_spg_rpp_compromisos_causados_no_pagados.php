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
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 22/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,720,10,$as_fecha); // Agregar el t?tulo
		$io_pdf->addText(500,740,10,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,720,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cod_pro,$as_nomprobene,$as_ced_bene,$as_tipo_destino,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 22/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		if($as_tipo_destino=="P")
		{
			$ls_titulo="Proveedor";
			$ls_codigo=$as_cod_pro;
		}
		elseif($as_tipo_destino=="B")
		{
			$ls_titulo="Beneficiario";
			$ls_codigo=$as_ced_bene;
		}
		else
		{
			$ls_titulo="Ninguno";
			$ls_codigo="----------";
		}
		$la_data=array(array('name'=>'<b>Codigo</b> '.$ls_codigo.''),
		               array('name'=>'<b>'.$ls_titulo.'</b> '.$as_nomprobene.'' ));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>305, // Orientaci?n de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 22/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>305, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la 
						               'comprobante'=>array('justification'=>'center','width'=>150), // Justificaci?n y ancho de la 
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la 
						 			   'comprometido'=>array('justification'=>'right','width'=>120), // Justificaci?n 
						 			   'causado'=>array('justification'=>'right','width'=>60), // Justificaci?n y ancho de la 
									   'pagado'=>array('justification'=>'right','width'=>60))); // Justificaci?n y ancho de la 
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
		                   'comprobante'=>'<b>Comprobante</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'comprometido'=>'<b>Comprometido</b>',
						   'causado'=>'<b>Causado</b>',
						   'pagado'=>'<b>Pagado</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_totalcomprometer,$ad_totalcausado,$ad_totalpagado,$io_pdf,$ls_titulo)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 22/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datat=array(array('name'=>'____________________________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>315, // Orientaci?n de la tabla
						 'width'=>560); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		
		$la_data[]=array('cuenta'=>' ','comprobante'=>'','fecha'=>'<b>'.$ls_titulo.'</b> ','comprometido'=>$ad_totalcomprometer,
		                 'causado'=>$ad_totalcausado,'pagado'=>$ad_totalpagado);
		$la_columnas=array('cuenta'=>' ','comprobante'=>'','fecha'=>'','comprometido'=>'','causado'=>'','pagado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>305, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la 
						               'comprobante'=>array('justification'=>'center','width'=>150), // Justificaci?n y ancho de  
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la 
						 			   'comprometido'=>array('justification'=>'right','width'=>120), // Justificaci?n 
						 			   'causado'=>array('justification'=>'right','width'=>60), // Justificaci?n y ancho de la 
									   'pagado'=>array('justification'=>'right','width'=>60))); // Justificaci?n y ancho de la 
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>550, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spg_funciones_reportes.php");
        require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		$io_function        = new class_funciones() ;
		$io_fecha           = new class_fecha();
	    require_once("sigesp_spg_reportes_class.php");
		$io_report = new sigesp_spg_reportes_class();
		 
	//------------------------------------------------------------------------------------------------------------------------------		
			
	//--------------------------------------------------  Par?metros para Filtar el Reporte  --------------------------------------
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ldt_fecdes = $_GET["txtfecdes"];
		$ldt_fechas = $_GET["txtfechas"];	
	    $ls_fechades=$io_function->uf_convertirfecmostrar($ldt_fecdes);
	    $ls_fechahas=$io_function->uf_convertirfecmostrar($ldt_fechas);
		
	 /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Compromisos Causados no Pagados desde la  Fecha ".$ldt_fecdes."  hasta ".$ldt_fechas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_compromisos_causados_no_pagados.html",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////////
	//----------------------------------------------------  Par?metros del encabezado  ----------------------------------------------
		$ls_titulo="<b>COMPROMISOS CAUSADOS NO PAGADOS</b> "; 
		$ls_fecha="<b> DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas." </b>";      
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spg_reportes_compromiso_causados_no_pagados($ldt_fecdes,$ldt_fechas);
 
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
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		//$io_report->dts_reporte_final->group_noorder("cod_pro");
		$io_report->dts_reporte_final->group_noorder("codproben");
		$li_tot=$io_report->dts_reporte_final->getRowCount("spg_cuenta");
		$ld_total_comprometer=0;
	    $ld_total_causado=0;
	    $ld_total_pagado=0;
		$ld_sub_total_comprometer=0;
		$ld_sub_total_causado=0;
		$ld_sub_total_pagado=0;
		//$ls_cod_pro_ant="";
		$ls_cod_pro_ben_ant="";
		for($z=1;$z<=$li_tot;$z++)
		{
		    $li_tmp=($z+1);
			$io_pdf->transaction('start'); // Iniciamos la transacci?n
			$thisPageNum	 = $io_pdf->ezPageCount;
			$ls_spg_cuenta	 = $io_report->dts_reporte_final->data["spg_cuenta"][$z];
			$ls_procede		 = $io_report->dts_reporte_final->data["procede"][$z];
			$ls_comprobante	 = $io_report->dts_reporte_final->data["comprobante"][$z];
			$ldt_fecha		 = $io_report->dts_reporte_final->data["fecha"][$z]; 
			$ldt_fecha		 = $io_function->uf_convertirfecmostrar($ldt_fecha);
			$ld_comprometer	 = $io_report->dts_reporte_final->data["compromiso"][$z];  
			$ld_causado		 = $io_report->dts_reporte_final->data["causado"][$z];  
			$ld_pagado		 = $io_report->dts_reporte_final->data["pagado"][$z];  	  
			$ls_proc_comp	 = $ls_procede."---".$ls_comprobante;
			$ls_programatica = $io_report->dts_reporte_final->data["programatica"][$z];
			$ls_nombene		 = $io_report->dts_reporte_final->data["nombene"][$z];
			$ls_nompro		 = $io_report->dts_reporte_final->data["nompro"][$z];
			$ls_cod_pro		 = $io_report->dts_reporte_final->data["cod_pro"][$z];
			$ls_ced_bene	 = $io_report->dts_reporte_final->data["ced_bene"][$z];
			$ls_tipo_destino = $io_report->dts_reporte_final->data["tipo_destino"][$z];
			$ls_codproben	 = $io_report->dts_reporte_final->data["codproben"][$z];
			
			
			if($ls_tipo_destino=="P")
		    {
			    $ls_nomprobene=$ls_nompro;
		    }
			if($ls_tipo_destino=="B")
			{
				$ls_nomprobene=$ls_nombene;
			}
			if($ls_tipo_destino=="-" || $ls_tipo_destino=="")
			{
				$ls_nomprobene="Ninguno";
			}
		    if ($z<$li_tot)
		    {
				//$ls_cod_pro_next=$io_report->dts_reporte_final->data["cod_pro"][$li_tmp]; 
				$ls_cod_pro_ben_next=$io_report->dts_reporte_final->data["codproben"][$li_tmp]; 
		    }
		    elseif($z==$li_tot)
		    {
				//$ls_cod_pro_next='no_next';
				$ls_cod_pro_ben_next='no_next';
		    }
			//$ls_cod_pro_ant=$io_report->dts_reporte_final->data["cod_pro"][$z];
			//if(empty($ls_cod_pro_next)&&(!empty($ls_cod_pro)))
			if(empty($ls_cod_pro_ben_next)&&(!empty($ls_codproben)))
			{
			   //$ls_cod_pro_ant=$io_report->dts_reporte_final->data["cod_pro"][$z];
			   $ls_cod_pro_ben_ant=$io_report->dts_reporte_final->data["codproben"][$z];
			}
			if($li_tot==1)
			{
			   //$ls_cod_pro_ant=$io_report->dts_reporte_final->data["cod_pro"][$z];
			   $ls_cod_pro_ben_ant=$io_report->dts_reporte_final->data["codproben"][$z];
			}
			
			$ld_total_comprometer=$ld_total_comprometer+$ld_comprometer;
			$ld_total_causado=$ld_total_causado+$ld_causado;
			$ld_total_pagado=$ld_total_pagado+$ld_pagado;
		  
			$ld_sub_total_comprometer=$ld_sub_total_comprometer+$ld_comprometer;
			$ld_sub_total_causado=$ld_sub_total_causado+$ld_causado;
			$ld_sub_total_pagado=$ld_sub_total_pagado+$ld_pagado;
			
			//if (!empty($ls_cod_pro))
			if (!empty($ls_codproben))
		    {
				  $ld_comprometer=number_format($ld_comprometer,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  
				  $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				                     'comprometido'=>$ld_comprometer,'causado'=>$ld_causado,'pagado'=>$ld_pagado);
				  
				  $ld_comprometer=str_replace('.','',$ld_comprometer);
				  $ld_comprometer=str_replace(',','.',$ld_comprometer);	
				  $ld_causado=str_replace('.','',$ld_causado);
				  $ld_causado=str_replace(',','.',$ld_causado);	
				  $ld_pagado=str_replace('.','',$ld_pagado);
				  $ld_pagado=str_replace(',','.',$ld_pagado);	
			}
			else
			{
				  $ld_comprometer=number_format($ld_comprometer,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  
				  $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				                     'comprometido'=>$ld_comprometer,'causado'=>$ld_causado,'pagado'=>$ld_pagado);
				  
				  $ld_comprometer=str_replace('.','',$ld_comprometer);
				  $ld_comprometer=str_replace(',','.',$ld_comprometer);	
				  $ld_causado=str_replace('.','',$ld_causado);
				  $ld_causado=str_replace(',','.',$ld_causado);	
				  $ld_pagado=str_replace('.','',$ld_pagado);
				  $ld_pagado=str_replace(',','.',$ld_pagado);	
			}
			//if (!empty($ls_cod_pro_next))
			if (!empty($ls_cod_pro_ben_next))
			{
				  $ld_comprometer=number_format($ld_comprometer,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  
				  $la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				                     'comprometido'=>$ld_comprometer,'causado'=>$ld_causado,'pagado'=>$ld_pagado);
					 //if($ls_cod_pro=="")
					 if($ls_codproben=="")
					 {
						//$ls_cod_pro=$ls_cod_pro_ant;
						$ls_codproben=$ls_cod_pro_ben_ant;
					 }
			     uf_print_cabecera($ls_cod_pro,$ls_nomprobene,$ls_ced_bene,$ls_tipo_destino,$io_pdf);
 				 uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				 $ld_subtotal_comprometer=$ld_sub_total_comprometer;
				 $ld_subtotal_causado=$ld_sub_total_causado;
				 $ld_subtotal_pagado=$ld_sub_total_pagado;
				 $ld_sub_total_comprometer=number_format($ld_sub_total_comprometer,2,",",".");
				 $ld_sub_total_causado=number_format($ld_sub_total_causado,2,",",".");
				 $ld_sub_total_pagado=number_format($ld_sub_total_pagado,2,",",".");
				  uf_print_pie_cabecera($ld_sub_total_comprometer,$ld_sub_total_causado,$ld_sub_total_pagado,$io_pdf,"Total Bs.");	
				 $ld_sub_total_comprometer=0;
				 $ld_sub_total_causado=0;
				 $ld_sub_total_pagado=0;
				 if($z==$li_tot)
				 {
				   // Imprimimos pie de la cabecera
					
					  $ld_total_comprometer=number_format($ld_total_comprometer,2,",",".");
					  $ld_total_causado=number_format($ld_total_causado,2,",",".");
					  $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
					  uf_print_pie_cabecera($ld_total_comprometer,$ld_total_causado,$ld_total_pagado,$io_pdf,"Total Bs.");
				 }
			     unset($la_data);		
			}//if
	    }//for
			
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
	unset($io_fecha);
?> 