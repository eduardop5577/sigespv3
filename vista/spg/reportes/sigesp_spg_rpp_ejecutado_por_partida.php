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
		// Fecha Creaci?n: 25/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(14,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,14,$as_titulo); // Agregar el t?tulo

		$li_tm=$io_pdf->getTextWidth(14,$as_fecha);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,520,14,$as_fecha); // Agregar el t?tulo
		$io_pdf->addText(900,560,10,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_spg_cuenta,$as_denominacion,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 25/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Cuenta</b> '.$as_spg_cuenta.''),
		               array('name'=>'<b>Denominacion</b> '.$as_denominacion.'' ));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						// 'xPos'=>305, // Orientaci?n de la tabla
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990); // Ancho M?ximo de la tabla
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
		// Fecha Creaci?n: 25/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0.5, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>500, // Orientaci?n de la tabla
						 'cols'=>array('programatica'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la
						               'descripcion'=>array('justification'=>'left','width'=>130), // Justificaci?n y ancho de la
						 			   'asignado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la
						 			   'precomprometido'=>array('justification'=>'right','width'=>100), // Justificaci?n
						 			   'comprometido'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
						 			   'porcomprometido'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho
						 			   'causado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
						 			   'porcausado'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho
						 			   'pagado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
									   'porpagado'=>array('justification'=>'right','width'=>80))); // Justificaci?n y ancho de la
		$la_columnas=array('programatica'=>'<b>Programatica</b>',
		                   'descripcion'=>'<b>Descripcion</b>',
						   'asignado'=>'<b>Asignado</b>',
						   'precomprometido'=>'<b>Precomprometido</b>',
						   'comprometido'=>'<b>Comprometido</b>',
						   'porcomprometido'=>'<b>% Comprometido</b>',
						   'causado'=>'<b>Causado</b>',
						   'porcausado'=>'<b>% Causado</b>',
						   'pagado'=>'<b>Pagado</b>',
						   'porpagado'=>'<b>% Pagado</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_total_asignado,$ad_total_precompromiso,$ad_total_compromiso,$ad_total_causado,
	                               $ad_total_pagado,$io_pdf,$as_titulo)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 25/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datat=array(array('name'=>'___________________________________________________________________________________________________________________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>500, // Orientaci?n de la tabla
						 'width'=>990); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);

		$la_data[]=array('programatica'=>'','descripcion'=>'<b>'.$as_titulo.'</b>','asignado'=>$ad_total_asignado,
		                 'precomprometido'=>$ad_total_precompromiso,'comprometido'=>$ad_total_compromiso,
		                 'porcomprometido'=>'','causado'=>$ad_total_causado,'porcausado'=>'','pagado'=>$ad_total_pagado,
						 'porpagado'=>'');
		$la_columnas=array('programatica'=>' ','descripcion'=>'','asignado'=>'','precomprometido'=>'','comprometido'=>'',                           'porcomprometido'=>'','causado'=>'','porcausado'=>'','pagado'=>'','porpagado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>0.5, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>500, // Orientaci?n de la tabla
						 'cols'=>array('programatica'=>array('justification'=>'left','width'=>120), // Justificaci?n y ancho de la
						               'descripcion'=>array('justification'=>'left','width'=>130), // Justificaci?n y ancho de la
						 			   'asignado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho de la
						 			   'precomprometido'=>array('justification'=>'right','width'=>100), // Justificaci?n
						 			   'comprometido'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
						 			   'porcomprometido'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho
						 			   'causado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
						 			   'porcausado'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho
						 			   'pagado'=>array('justification'=>'right','width'=>100), // Justificaci?n y ancho
									   'porpagado'=>array('justification'=>'right','width'=>80))); // Justificaci?n y ancho de la
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>990, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spg_reportes_class.php");
		$io_report = new sigesp_spg_reportes_class();
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
        require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_function=new class_funciones() ;
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once ("sigesp_spg_class_tcpdf.php");
		require_once("sigesp_spg_reportes_class.php");
		$io_report = new sigesp_spg_reportes_class();
		$lb_resumen = TRUE;
	//------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------  Par?metros para Filtar el Reporte  --------------------------------------
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ldt_fecdes = $_GET["txtfecdes"];
		$ldt_fechas = $_GET["txtfechas"];
	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
	    $ls_resumen=$_GET["resumen"];
	    
	    
		if($ls_cuentades_min=="")
		{
		   $arrResultado=$io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min);
		   $ls_cuentades_min=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   $arrResultado=$io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max);
		   $ls_cuentahas_max=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   }
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');");
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}

	    $ls_fechades=$io_function->uf_convertirfecmostrar($ldt_fecdes);
	    $ls_fechahas=$io_function->uf_convertirfecmostrar($ldt_fechas);
	    
	    if($ls_resumen=="1"){
	    	$lb_resumen = FALSE;
	    }

	 /////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Ejecutado por Partida desde la  Fecha ".$ls_fechades."  hasta ".$ls_fechahas." Desde la Cuenta ".$ls_cuentades."  hasta ".$ls_cuentahas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_ejecutado_por_partida.html",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//----------------------------------------------------  Par?metros del encabezado  ----------------------------------------------
		$ls_titulo="EJECUTADO POR PARTIDA";
		$ls_fecha=" DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas."";
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	$rs_datos="";
      $arrResultado=$io_report->uf_spg_reportes_ejecutado_por_partida_nuevo($ldt_fecdes,$ldt_fechas,$ls_cuentades,$ls_cuentahas,$rs_datos, $lb_resumen);
	 $rs_datos=$arrResultado['rs_data'];
	 $lb_valido=$arrResultado['lb_valido'];
     // $lb_valido=$io_report->uf_spg_reportes_ejecutado_por_partida($ldt_fecdes,$ldt_fechas,$ls_cuentades,$ls_cuentahas);
	//echo "sa";
//	die();
      if($lb_valido==false) // Existe alg?n error ? no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	    
		set_time_limit(0);
		$io_tcpdf= new sigesp_spg_class_tcpdf ("L", PDF_UNIT, "legal", true);
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->SetFont("helvetica","BI",8);	
		$ls_mensaje = str_repeat(' ',80).$ls_titulo;
		$ls_mensaje2 = str_repeat(' ',70).$ls_fecha.str_repeat(' ',70).date("d/m/Y").' '.date("h:i a").'-'.$_SESSION["ls_database"];
		$io_tcpdf->SetHeaderData($_SESSION["ls_logo"],$_SESSION["ls_width"], $ls_mensaje, $ls_mensaje2,$_SESSION["ls_height"]);
		$io_tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$io_tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$io_tcpdf->SetMargins(2, 45,2);
		$io_tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$io_tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$io_tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$io_tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->AddPage();	
		$io_tcpdf->SetFont("helvetica","B",8);
		
		$io_report->dts_reporte_final->group_noorder("spg_cuenta");
		$li_tot=$io_report->dts_reporte_final->getRowCount("spg_cuenta");
		$ld_total_asignado=0;		        $ld_total_precompromiso=0;
		$ld_total_compromiso=0;		        $ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_asignado_general=0;		$ld_total_precompromiso_general=0;
		$ld_total_compromiso_general=0;		$ld_total_causado_general=0;
		$ld_total_pagado_general=0;
		$ld_asignado_apertura=0;
		$ls_spg_cuenta_ant="";
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		$li_tot=$rs_datos->RecordCount();
		//for($z=1;$z<=$li_tot;$z++)
		$z=0;
		while(!$rs_datos->EOF)	
		{
			$z++;
			//$io_pdf->transaction('start'); // Iniciamos la transacci?n
		//	$thisPageNum=$io_pdf->ezPageCount;
			$ls_spg_cuenta=$rs_datos->fields["spg_cuenta"];
			$ls_denominacion=$rs_datos->fields["denominacion"];
			$ls_programatica=$rs_datos->fields["codestpro1"].$rs_datos->fields["codestpro2"].$rs_datos->fields["codestpro3"].$rs_datos->fields["codestpro4"].$rs_datos->fields["codestpro5"].$rs_datos->fields["estcla"];
			$ls_estcla=substr($ls_programatica,-1);
			$ls_codestpro1   = substr($ls_programatica,0,25);
			$ls_codestpro2=substr($ls_programatica,25,25);
			$ls_codestpro3 = substr($ls_programatica,50,25);
			if ($li_estmodest=='2')
		    {
			    $ls_codestpro4=substr($ls_programatica,75,25);
			    $ls_codestpro5 = substr($ls_programatica,100,25);
				$ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
		    }
            else
			{
				$ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
			}
			$ls_descripcion=$rs_datos->fields["descripcion"];
			$ld_asignado=$rs_datos->fields["asignado"];
			$ld_aumento=$rs_datos->fields["aumento"];
			$ld_disminucion=$rs_datos->fields["disminucion"];
		    $ld_precompromiso=$rs_datos->fields["precompromiso"];
		    $ld_compromiso=$rs_datos->fields["compromiso"];
		    $ld_causado=$rs_datos->fields["causado"];
		    $ld_pagado=$rs_datos->fields["pagado"];
		    $rs_datos->MoveNext();
		    /*$ld_porc_comprometido=$io_report->dts_reporte_final->data["porc_compromiso"][$z];
		    $ld_porc_causado=$io_report->dts_reporte_final->data["porc_causado"][$z];
		    $ld_porc_pagado=$io_report->dts_reporte_final->data["porc_pagado"][$z];*/
			
		    if ($z<$li_tot)
		    {
				$ls_spg_cuenta_next=$rs_datos->fields["spg_cuenta"];
		    }
		    elseif($z=$li_tot)
		    {
				$ls_spg_cuenta_next='no_next';
		    }
			if(($ls_spg_cuenta_next==$ls_spg_cuenta)&&(!empty($ls_spg_cuenta)))
			{
			   $ls_spg_cuenta_ant=$ls_spg_cuenta;
			}
            if(($ls_spg_cuenta_next<>$ls_spg_cuenta)&&(!empty($ls_spg_cuenta)))
            {
               $ls_spg_cuenta_ant=$ls_spg_cuenta;
            }
			if($li_tot==1)
			{
			   $ls_spg_cuenta_ant=$ls_spg_cuenta;
			}
			if($ld_asignado<>0)
			{
			  $ld_asignado_apertura=$ld_asignado;
			}

			$ld_suma=$ld_asignado_apertura+$ld_aumento+$ld_disminucion;
			if($ld_suma>0)
			{
			   $ld_porc_comprometido=($ld_compromiso*100)/$ld_suma;
   			  /* $ld_porc_causado=($ld_causado*100)/$ld_suma;
			   $ld_porc_pagado=($ld_pagado*100)/$ld_suma;*/
		    }
            else
			{
			   $ld_porc_comprometido=0;
			  /* $ld_porc_causado=0;
			   $ld_porc_pagado=0;*/
			}
			if($ld_compromiso>0)
			{
			   $ld_porc_causado=($ld_causado*100)/$ld_compromiso;
		    }
            else
			{
			   $ld_porc_causado=0;
			}
			if($ld_causado>0)
			{
			   $ld_porc_pagado=($ld_pagado*100)/$ld_causado;
		    }
            else
			{
			   $ld_porc_pagado=0;
			}

		    $ld_total_asignado=$ld_total_asignado+$ld_asignado;
		    $ld_total_precompromiso=$ld_total_precompromiso+$ld_precompromiso;
		    $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso;
		    $ld_total_causado=$ld_total_causado+$ld_causado;
		    $ld_total_pagado=$ld_total_pagado+$ld_pagado;

		    $ld_total_asignado_general=$ld_total_asignado_general+$ld_asignado;
		    $ld_total_precompromiso_general=$ld_total_precompromiso_general+$ld_precompromiso;
		    $ld_total_compromiso_general=$ld_total_compromiso_general+$ld_compromiso;
		    $ld_total_causado_general=$ld_total_causado_general+$ld_causado;
		    $ld_total_pagado_general=$ld_total_pagado_general+$ld_pagado;
			
			if (!empty($ls_spg_cuenta))
		    {
				//  $ld_total_asignado_general=$ld_total_asignado_general+$ld_total_asignado;
				  $ld_asignado=number_format($ld_asignado,2,",",".");
				  $ld_precompromiso=number_format($ld_precompromiso,2,",",".");
				  $ld_compromiso=number_format($ld_compromiso,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  $ld_porc_comprometido=number_format($ld_porc_comprometido,2,",",".");
				  $ld_porc_causado=number_format($ld_porc_causado,2,",",".");
				  $ld_porc_pagado=number_format($ld_porc_pagado,2,",",".");

				  $la_data[$z]=array('programatica'=>$ls_programatica,'descripcion'=>$ls_descripcion,'asignado'=>$ld_asignado,
									 'precomprometido'=>$ld_precompromiso,'comprometido'=>$ld_compromiso,
									 'porcomprometido'=>$ld_porc_comprometido,'causado'=>$ld_causado,'porcausado'=>$ld_porc_causado,
									 'pagado'=>$ld_pagado,'porpagado'=>$ld_porc_pagado);

				 $ld_asignado=str_replace('.','',$ld_asignado);
				 $ld_asignado=str_replace(',','.',$ld_asignado);
				 $ld_precompromiso=str_replace('.','',$ld_precompromiso);
				 $ld_precompromiso=str_replace(',','.',$ld_precompromiso);
				 $ld_compromiso=str_replace('.','',$ld_compromiso);
				 $ld_compromiso=str_replace(',','.',$ld_compromiso);
				 $ld_causado=str_replace('.','',$ld_causado);
				 $ld_causado=str_replace(',','.',$ld_causado);
				 $ld_pagado=str_replace('.','',$ld_pagado);
				 $ld_pagado=str_replace(',','.',$ld_pagado);
				 $ld_porc_comprometido=str_replace('.','',$ld_porc_comprometido);
				 $ld_porc_comprometido=str_replace(',','.',$ld_porc_comprometido);
				 $ld_porc_causado=str_replace('.','',$ld_porc_causado);
				 $ld_porc_causado=str_replace(',','.',$ld_porc_causado);
				 $ld_porc_pagado=str_replace('.','',$ld_porc_pagado);
				 $ld_porc_pagado=str_replace(',','.',$ld_porc_pagado);
			}
			else
			{
				  $ld_asignado=number_format($ld_asignado,2,",",".");
				  $ld_precompromiso=number_format($ld_precompromiso,2,",",".");
				  $ld_compromiso=number_format($ld_compromiso,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  $ld_porc_comprometido=number_format($ld_porc_comprometido,2,",",".");
				  $ld_porc_causado=number_format($ld_porc_causado,2,",",".");
				  $ld_porc_pagado=number_format($ld_porc_pagado,2,",",".");

				  $la_data[$z]=array('programatica'=>$ls_programatica,'descripcion'=>$ls_descripcion,'asignado'=>$ld_asignado,
									 'precomprometido'=>$ld_precompromiso,'comprometido'=>$ld_compromiso,
									 'porcomprometido'=>$ld_porc_comprometido,'causado'=>$ld_causado,'porcausado'=>$ld_porc_causado,
									 'pagado'=>$ld_pagado,'porpagado'=>$ld_porc_pagado);

				 $ld_asignado=str_replace('.','',$ld_asignado);
				 $ld_asignado=str_replace(',','.',$ld_asignado);
				 $ld_precompromiso=str_replace('.','',$ld_precompromiso);
				 $ld_precompromiso=str_replace(',','.',$ld_precompromiso);
				 $ld_compromiso=str_replace('.','',$ld_compromiso);
				 $ld_compromiso=str_replace(',','.',$ld_compromiso);
				 $ld_causado=str_replace('.','',$ld_causado);
				 $ld_causado=str_replace(',','.',$ld_causado);
				 $ld_pagado=str_replace('.','',$ld_pagado);
				 $ld_pagado=str_replace(',','.',$ld_pagado);
				 $ld_porc_comprometido=str_replace('.','',$ld_porc_comprometido);
				 $ld_porc_comprometido=str_replace(',','.',$ld_porc_comprometido);
				 $ld_porc_causado=str_replace('.','',$ld_porc_causado);
				 $ld_porc_causado=str_replace(',','.',$ld_porc_causado);
				 $ld_porc_pagado=str_replace('.','',$ld_porc_pagado);
				 $ld_porc_pagado=str_replace(',','.',$ld_porc_pagado);
			}
			
			if ($ls_spg_cuenta_next!=$ls_spg_cuenta)
			{
				
				//echo "sas";
				//die();
				  $ld_asignado=number_format($ld_asignado,2,",",".");
				  $ld_precompromiso=number_format($ld_precompromiso,2,",",".");
				  $ld_compromiso=number_format($ld_compromiso,2,",",".");
				  $ld_causado=number_format($ld_causado,2,",",".");
				  $ld_pagado=number_format($ld_pagado,2,",",".");
				  $ld_porc_comprometido=number_format($ld_porc_comprometido,2,",",".");
				  $ld_porc_causado=number_format($ld_porc_causado,2,",",".");
				  $ld_porc_pagado=number_format($ld_porc_pagado,2,",",".");
				  $la_data[$z]=array('programatica'=>$ls_programatica,'descripcion'=>$ls_descripcion,'asignado'=>$ld_asignado,
									 'precomprometido'=>$ld_precompromiso,'comprometido'=>$ld_compromiso,
									 'porcomprometido'=>$ld_porc_comprometido,'causado'=>$ld_causado,'porcausado'=>$ld_porc_causado,
									 'pagado'=>$ld_pagado,'porpagado'=>$ld_porc_pagado);

				   //var_dump($la_data);                  
                   //print "xxx<br>";
	//  uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
  //uf_print_cabecera($ls_spg_cuenta_ant,$ls_denominacion,$io_pdf);

				  //var_dump($ls_spg_cuenta_ant);
				  $par=0;
				 $io_tcpdf->uf_print_cabecera_ejecutado_por_partida($ls_spg_cuenta_ant,$ls_denominacion); // Imprimimos la cabecera del registro
				 $io_tcpdf->uf_print_titulos_ejecutadoporpartidas();
				 $io_tcpdf->uf_print_detalle_ejecutadoporpartida($la_data,$par,'',''); // Imprimimos el detalle
	
				 $ld_totalasignado=$ld_total_asignado;
				 $ld_totalprecompromiso=$ld_total_precompromiso;
				 $ld_totalcompromiso=$ld_total_compromiso;
				 $ld_totalcausado=$ld_total_causado;
				 $ld_totalpagado=$ld_total_pagado;

				 $ld_total_asignado=number_format($ld_total_asignado,2,",",".");
				 $ld_total_precompromiso=number_format($ld_total_precompromiso,2,",",".");
				 $ld_total_compromiso=number_format($ld_total_compromiso,2,",",".");
				 $ld_total_causado=number_format($ld_total_causado,2,",",".");
				 $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
				 $io_tcpdf->uf_print_total_formato_ejecutado_porpartida($ld_total_asignado,$ld_total_precompromiso,$ld_total_compromiso,$ld_total_causado,
										   									$ld_total_pagado);
				 
				 $ld_total_asignado=0;
				 $ld_total_precompromiso=0;
				 $ld_total_compromiso=0;
				 $ld_total_causado=0;
				 $ld_total_pagado=0;
				 /*if ($io_pdf->ezPageCount==$thisPageNum)
				 {// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				 }
				 elseif($thisPageNum>1)
				 {// Hacemos un rollback de los registros, agregamos una nueva p?gina y volvemos a imprimir
					$io_pdf->transaction('rewind');
					$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					uf_print_cabecera($ls_spg_cuenta_ant,$ls_denominacion,$io_pdf);
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
					$ld_totalasignado=number_format($ld_totalasignado,2,",",".");
					$ld_totalprecompromiso=number_format($ld_totalprecompromiso,2,",",".");
					$ld_totalcompromiso=number_format($ld_totalcompromiso,2,",",".");
					$ld_totalcausado=number_format($ld_totalcausado,2,",",".");
					$ld_totalpagado=number_format($ld_totalpagado,2,",",".");
					if($ls_tipoformato==1)
					{
					  uf_print_pie_cabecera($ld_totalasignado,$ld_totalprecompromiso,$ld_totalcompromiso,$ld_totalcausado,
					       				    $ld_totalpagado,$io_pdf,'Total Bs.F.');
					}
					else
					{
					  uf_print_pie_cabecera($ld_totalasignado,$ld_totalprecompromiso,$ld_totalcompromiso,$ld_totalcausado,
					       				    $ld_totalpagado,$io_pdf,'Total Bs.');
					}
				    $ld_totalasignado=0;
				    $ld_totalprecompromiso=0;
				    $ld_totalcompromiso=0;
				    $ld_totalcausado=0;
				    $ld_totalpagado=0;
				 }*/
				 if($z==$li_tot)
				 {
				     // Imprimimos pie de la cabecera
					$ld_total_asignado_general=number_format($ld_total_asignado_general,2,",",".");
					$ld_total_precompromiso_general=number_format($ld_total_precompromiso_general,2,",",".");
					$ld_total_compromiso_general=number_format($ld_total_compromiso_general,2,",",".");
					$ld_total_causado_general=number_format($ld_total_causado_general,2,",",".");
					$ld_total_pagado_general=number_format($ld_total_pagado_general,2,",",".");

					$io_tcpdf->uf_print_total_formato_ejecutado_porpartida($ld_total_asignado_general,$ld_total_precompromiso_general,$ld_total_compromiso_general,
												$ld_total_causado_general,$ld_total_pagado_general,true);
					
			 	 }
			     unset($la_data);
			}//if
			
	    }//for

	    
	    
	    unset($la_data);
		unset($la_data_tot);
		$io_tcpdf->Output("sigesp_spg_rpp_ejecutado_x_cuenta.pdf", "I");
		unset($io_tcpdf);	 
	    die();
		//$io_pdf->ezStopPageNumbers(1,1);
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