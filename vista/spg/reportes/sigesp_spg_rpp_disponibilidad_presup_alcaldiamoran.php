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
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,10,$as_fecha); // Agregar el título
		$io_pdf->addText(500,740,9,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,720,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_programatica,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		if ($_SESSION["la_empresa"]["estmodest"] == 2)
		{
			$la_data=array(array('name'=>'<b>Programatica</b> '.$as_programatica.''));
			$la_columna=array('name'=>'');
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 8, // Tamaño de Letras
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'shadeCol'=>array(0.9,0.9,0.9),
							 'shadeCo2'=>array(0.9,0.9,0.9),
							 'colGap'=>1, // separacion entre tablas
							 'xOrientation'=>'center', // Orientación de la tabla
							 'xPos'=>299, // Orientación de la tabla
							 'width'=>550, // Ancho de la tabla
							 'maxWidth'=>550); // Ancho Máximo de la tabla
			$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		}
		else
		{
		 	$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	 		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	 		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];

	 		$la_datatit=array(array('name'=>'<b>ESTRUCTURA PRESUPUESTARIA </b>'));

	 		$la_columnatit=array('name'=>'');

	 		$la_configtit=array('showHeadings'=>0, // Mostrar encabezados
								 'showLines'=>0, // Mostrar Líneas
								 'shaded'=>0, // Sombra entre líneas
								 'fontSize' => 8, // Tamaño de Letras
								 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
								 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
								 'colGap'=>1, // separacion entre tablas
								 'xOrientation'=>'center', // Orientación de la tabla
								 'xPos'=>299, // Orientación de la tabla
								 'width'=>550, // Ancho de la tabla
								 'maxWidth'=>550);// Ancho Máximo de la tabla

	 		$io_pdf->ezTable($la_datatit,$la_columnatit,'',$la_configtit);

			 $la_data=array(array('name'=>substr($as_programatica,0,$ls_loncodestpro1).'</b>','name2'=>$as_denestpro[0]),
							array('name'=>substr($as_programatica,$ls_loncodestpro1,$ls_loncodestpro2),'name2'=>$as_denestpro[1]),
							array('name'=>substr($as_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3),'name2'=>$as_denestpro[2]));

			 $la_columna=array('name'=>'','name2'=>'');
			 $la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'fontSize' => 8, // Tamaño de Letras
							 'shadeCol'=>array(0.98,0.98,0.98), // Color de la sombra
							 'shadeCol2'=>array(0.98,0.98,0.98), // Color de la sombra
							 'colGap'=>1, // separacion entre tablas
							 'xOrientation'=>'center', // Orientación de la tabla
							 'xPos'=>299, // Orientación de la tabla
							 'width'=>560, // Ancho de la tabla
							 'maxWidth'=>560,// Ancho Máximo de la tabla
							 'cols'=>array('name'=>array('justification'=>'right','width'=>50), // Justificación y ancho de la columna
										   'name2'=>array('justification'=>'left','width'=>510))); // Justificación y ancho de la columna
			 $io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		}
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columnas=array('cuenta'=>'Cuenta','denominacion'=>'Denominación','asignado'=>'Asignado','disponibilidad'=>'Disponibilidad');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la
						 			   'denominacion'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la
						 			   'asignado'=>array('justification'=>'center','width'=>125), // Justificación y ancho de la
									   'disponibilidad'=>array('justification'=>'center','width'=>125))); // Justificación y ancho
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$io_pdf->ezSetDy(-15);
		
	}// end function uf_print_cabecera_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	/*/--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 21/04/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetY(630);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la
						 			   'asignado'=>array('justification'=>'right','width'=>125), // Justificación y ancho de la
									   'disponibilidad'=>array('justification'=>'right','width'=>125))); // Justificación y ancho de
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominación</b>',
						   'asignado'=>'<b>Asignado</b>',
						   'disponibilidad'=>'<b>Disponibilidad</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------*/

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_totalasignado,$ad_totaldisponible,$io_pdf)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación : 18/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b> Total  </b>','asignado'=>$ad_totalasignado,'disminucion'=>$ad_totaldisponible));
		$la_columna=array('total'=>'','asignado'=>'','disminucion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>299, // Orientación de la tabla
						 'colGap'=>1,
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 	           'asignado'=>array('justification'=>'right','width'=>125), // Justificación y ancho de la
							           'disminucion'=>array('justification'=>'right','width'=>125))); // Justificación y ancho de la

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("sigesp_spg_funciones_reportes.php");
	require_once("sigesp_spg_reporte.php");
	$io_report = new sigesp_spg_reporte();
	$io_function_report = new sigesp_spg_funciones_reportes();
	$io_funciones=new class_funciones();
	$io_fecha = new class_fecha();
		
	//----------------------------------------------------------------------------------------------------------------------------
	$li_candeccon=$_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon=$_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon=$_SESSION["la_empresa"]["redconmon"];
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
    
	//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
	
	$ls_codestpro1_min  = $_GET["codestpro1"];
	$ls_codestpro2_min  = $_GET["codestpro2"];
	$ls_codestpro3_min  = $_GET["codestpro3"];
	$ls_codestpro1h_max = $_GET["codestpro1h"];
	$ls_codestpro2h_max = $_GET["codestpro2h"];
	$ls_codestpro3h_max = $_GET["codestpro3h"];
	$ls_estclades       = $_GET["estclades"];
	$ls_estclahas       = $_GET["estclahas"];
	if($li_estmodest==1){
		$ls_codestpro4_min  = "0000000000000000000000000";
		$ls_codestpro5_min  = "0000000000000000000000000";
		$ls_codestpro4h_max = "0000000000000000000000000";
		$ls_codestpro5h_max = "0000000000000000000000000";
		if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min=="")){
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
																					  $ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);				
			$ls_codestpro1_min=$arrResultado['as_codestpro1'];
			$ls_codestpro2_min=$arrResultado['as_codestpro2'];
			$ls_codestpro3_min=$arrResultado['as_codestpro3'];
			$ls_codestpro4_min=$arrResultado['as_codestpro4'];
			$ls_codestpro5_min=$arrResultado['as_codestpro5'];
			$ls_estclades=$arrResultado['as_estclades'];
			$lb_valido= $arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
			}
		}
		else{
			$ls_codestpro1  = $ls_codestpro1_min;
			$ls_codestpro2  = $ls_codestpro2_min;
			$ls_codestpro3  = $ls_codestpro3_min;
			$ls_codestpro4  = $ls_codestpro4_min;
			$ls_codestpro5  = $ls_codestpro5_min;
		}
		if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max=="")){
				$arrResultado=$io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas);
				$ls_codestpro1h_max=$arrResultado['as_codestpro1'];
				$ls_codestpro2h_max=$arrResultado['as_codestpro2'];
				$ls_codestpro3h_max=$arrResultado['as_codestpro3'];
				$ls_codestpro4h_max=$arrResultado['as_codestpro4'];
				$ls_codestpro5h_max=$arrResultado['as_codestpro5'];
				$ls_estclahas=$arrResultado['as_estclahas'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}
		else{
			$ls_codestpro1h  = $ls_codestpro1h_max;
			$ls_codestpro2h  = $ls_codestpro2h_max;
			$ls_codestpro3h  = $ls_codestpro3h_max;
			$ls_codestpro4h  = $ls_codestpro4h_max;
			$ls_codestpro5h  = $ls_codestpro5h_max;
		}
	}
	elseif($li_estmodest==2){
		$ls_codestpro4_min = $_GET["codestpro4"];
		$ls_codestpro5_min = $_GET["codestpro5"];
		$ls_codestpro4h_max = $_GET["codestpro4h"];
		$ls_codestpro5h_max = $_GET["codestpro5h"];
		if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min=="")&&($ls_codestpro4_min=="")&&($ls_codestpro5_min=="")){
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,
																					  $ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);				
			$ls_codestpro1_min=$arrResultado['as_codestpro1'];
			$ls_codestpro2_min=$arrResultado['as_codestpro2'];
			$ls_codestpro3_min=$arrResultado['as_codestpro3'];
			$ls_codestpro4_min=$arrResultado['as_codestpro4'];
			$ls_codestpro5_min=$arrResultado['as_codestpro5'];
			$ls_estclades=$arrResultado['as_estclades'];
			$lb_valido= $arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ls_codestpro1  = $ls_codestpro1_min;
				$ls_codestpro2  = $ls_codestpro2_min;
				$ls_codestpro3  = $ls_codestpro3_min;
				$ls_codestpro4  = $ls_codestpro4_min;
				$ls_codestpro5  = $ls_codestpro5_min;
			}
		}
		else{
			$ls_codestpro1  = $ls_codestpro1_min;
			$ls_codestpro2  = $ls_codestpro2_min;
			$ls_codestpro3  = $ls_codestpro3_min;
			$ls_codestpro4  = $ls_codestpro4_min;
			$ls_codestpro5  = $ls_codestpro5_min;
		}
		
		if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max=="")&&($ls_codestpro4h_max=="")&&($ls_codestpro5h_max=="")){
				$arrResultado=$io_function_report->uf_spg_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,
																			 $ls_codestpro3h_max,$ls_codestpro4h_max,
																			 $ls_codestpro5h_max,$ls_estclahas);
				$ls_codestpro1h_max=$arrResultado['as_codestpro1'];
				$ls_codestpro2h_max=$arrResultado['as_codestpro2'];
				$ls_codestpro3h_max=$arrResultado['as_codestpro3'];
				$ls_codestpro4h_max=$arrResultado['as_codestpro4'];
				$ls_codestpro5h_max=$arrResultado['as_codestpro5'];
				$ls_estclahas=$arrResultado['as_estclahas'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
				}
		}
		else{
			$ls_codestpro1h  = $ls_codestpro1h_max;
			$ls_codestpro2h  = $ls_codestpro2h_max;
			$ls_codestpro3h  = $ls_codestpro3h_max;
			$ls_codestpro4h  = $ls_codestpro4h_max;
			$ls_codestpro5h  = $ls_codestpro5h_max;
		}
	}
	
	$ls_cuentades_min=$_GET["txtcuentades"];
	$ls_cuentahas_max=$_GET["txtcuentahas"];
	if($ls_cuentades_min==""){
		   $arrResultado=$io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min);
		   $ls_cuentades_min=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
				$ls_cuentades=$ls_cuentades_min;
			}
	   	else{
	   		print("<script language=JavaScript>");
			print(" alert('No hay cuentas presupuestraias');");
			print(" close();");
			print("</script>");
	   	}
	}
	else{
		$ls_cuentades=$ls_cuentades_min;
	}

	if($ls_cuentahas_max==""){
		   $arrResultado=$io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max);
		   $ls_cuentahas_max=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
			$ls_cuentahas=$ls_cuentahas_max;
	    }
	   	else{
	   		print("<script language=JavaScript>");
			print(" alert('No hay cuentas presupuestraias');");
			print(" close();");
			print("</script>");
	   	}
	}
	else{
		$ls_cuentahas=$ls_cuentahas_max;
	}

	$fechas=$_GET["txtfechas"];
	if (!empty($fechas)){
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	}	
	else{
		$ldt_fechas=""; 
	}

    $li_ckbhasfec=$_GET["ckbhasfec"];
    $li_ckbctasinmov=$_GET["ckbctasinmov"];

	if($li_ckbhasfec==1){
		$ldt_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
		$ldt_fecdes=$ldt_ano."-01"."-01";
	}
	else{
		$ldt_fecdes="00-00-0000";
	}
	   
	$ls_fecha_titulo=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
	$ls_codfuefindes=$_GET["txtcodfuefindes"];
	$ls_codfuefinhas=$_GET["txtcodfuefinhas"];

	if (($ls_codfuefindes=='')&&($ls_codfuefindes=='')){
			$arrResultado=$io_function_report->uf_spg_select_fuentefinanciamiento($ls_minfuefin,$ls_maxfuefin);
			$ls_minfuefin=$arrResultado['as_minfuefin'];
			$ls_maxfuefin=$arrResultado['as_maxfuefin'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$ls_codfuefindes=$ls_minfuefin;
				$ls_codfuefinhas=$ls_maxfuefin;
		}
	}
	
	/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
	$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;
	$ls_desc_event="Solicitud de Reporte Disponibilidad Presupuestaria Desde la Cuenta ".$ls_cuentades." hasta ".$ls_cuentahas." ,  Desde la Programatica  ".$ls_programatica_desde." hasta ".$ls_programatica_hasta;
	$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_disponibilidad.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
	//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------------------------------------------------------------------------------------------------
	$ls_titulo="<b>DISPONIBILIDAD PRESUPUESTARIAS </b>";
	$ls_fecha="<b>HASTA LA FECHA  ".$ls_fecha_titulo."</b>";
    //-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
	$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
	$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
	$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
	$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
	
	//print "PROGRAMATICA: ".$ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
	$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
	$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
	$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
	$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
	$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
	$lb_valido=$io_report->uf_spg_reporte_disponibilidad_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                            $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																$ls_codestpro4h,$ls_codestpro5h,$ldt_fecdes,$ldt_fechas,
								                                $ls_cuentades,$ls_cuentahas,$li_ckbctasinmov,$li_ckbhasfec,
																$ls_codfuefindes,$ls_codfuefinhas,$ls_estclades,$ls_estclahas,true);
	if($lb_valido==false){// Existe algún error ó no hay registros
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	}
	else{// Imprimimos el reporte
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.7,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		$ld_totalasignado=0;
		$ld_totaldisponible=0;
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
		
		if ($li_tot > 0){
			for($z=1;$z<=$li_tot;$z++){
				$ls_programatica = $io_report->dts_reporte->data["programatica"][$z];
				if($li_estmodest==2){
					$ls_programatica = substr(substr($ls_programatica,0,25),-$ls_loncodestpro1)."-".
					                   substr(substr($ls_programatica,25,25),-$ls_loncodestpro2)."-".
					                   substr(substr($ls_programatica,50,25),-$ls_loncodestpro3)."-".
					                   substr(substr($ls_programatica,75,25),-$ls_loncodestpro4)."-".
					                   substr(substr($ls_programatica,100,25),-$ls_loncodestpro5);
				}
				uf_print_cabecera($ls_programatica,$io_pdf);
				$ls_spg_cuenta=$io_report->dts_reporte->data["spg_cuenta"][$z];
				$ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
				$ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
				$ld_disponible=$io_report->dts_reporte->data["disponible"][$z];
				$ls_status=$io_report->dts_reporte->data["status"][$z];
				
				if($ls_status=="C"){
					$ld_totalasignado=$ld_totalasignado+$ld_asignado;
					$ld_totaldisponible=$ld_totaldisponible+$ld_disponible;
				}
				
				$la_data[]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,
								   'asignado'=>number_format($ld_asignado,2,',','.'),'disponibilidad'=>number_format($ld_disponible,2,',','.'));
				uf_print_detalle($la_data, $io_pdf);
				unset($la_data);
			}
			
			uf_print_pie_cabecera(number_format($ld_totalasignado,2,',','.'),number_format($ld_totaldisponible,2,',','.'),$io_pdf);
		}
		else{
		  print("<script language=JavaScript>");
		  print(" alert('No hay nada que Reportar');");
		  print(" close();");
		  print("</script>");
		}
		
		$io_pdf->ezStopPageNumbers(1,1);
		if (isset($d) && $d){
			$ls_pdfcode = $io_pdf->ezOutput(1);
		  	$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
		  	echo '<html><body>';
		  	echo trim($ls_pdfcode);
		  	echo '</body></html>';
		}
		else{
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
?>