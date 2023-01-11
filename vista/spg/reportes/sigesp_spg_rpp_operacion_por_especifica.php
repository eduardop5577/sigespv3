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
	if(!array_key_exists("la_logusr",$_SESSION)){
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf){
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 25/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=330-($li_tm/2);
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
	function uf_print_cabecera($as_spg_cuenta,$as_den_spg_cta,$as_programatica,$as_denestpro,$io_pdf){
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 25/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		if ($_SESSION["la_empresa"]["estmodest"] == 2){
			$la_datacab=array(array('name'=>'<b>Cuenta</b> '.$as_spg_cuenta.''),
		                array('name'=>'<b>Denominacion</b> '.$as_den_spg_cta.'' ),
		   			    array('name'=>'<b>Programatica</b> '.$as_programatica.'' ),
					    array('name'=>'<b> </b>'.$as_denestpro.'' ));
		}
		else{
			$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	 	 	$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	 	 	$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
			$la_datacab=array(array('name'=>'<b>Cuenta</b> '.$as_spg_cuenta.''),
		                array('name'=>'<b>Denominacion</b> '.$as_den_spg_cta.'' ),
		   			    array('name'=>'<b>Estructura Presupuestaria </b> '),
					    array('name'=>$as_programatica),
		   			    array('name'=>$as_denestpro));
		}				
		
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 7, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550); // Ancho Máximo de la tabla
		
		$io_pdf->ezTable($la_datacab,$la_columna,'',$la_config);
		unset($la_datacab);
		unset($la_config);
		unset($la_columna);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 25/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'cols'=>array('documento'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						               'procede'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la 
						 			   'fecha'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la 
						 			   'beneficiario'=>array('justification'=>'center','width'=>100), // Justificación 
						 			   'concepto'=>array('justification'=>'left','width'=>170), // Justificación y ancho de la 
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la 
		$la_columnas=array('documento'=>'<b>Documento</b>',
		                   'procede'=>'<b>Procede</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'beneficiario'=>'<b>Proveedor/Beneficiario</b>',
						   'concepto'=>'<b>Concepto</b>',
						   'monto'=>'<b>Monto</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_total_monto,$io_pdf,$as_titulo){
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 25/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datat=array(array('name'=>'___________________________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>560); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		
		$la_data[]=array('total'=>'<b>'.$as_titulo.'</b>','monto'=>$ad_total_monto);
		$la_columnas=array('total'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>450), // Justificación y ancho de la 
									   'monto'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
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
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("sigesp_spg_funciones_reportes.php");
    require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	$io_function		= new class_funciones() ;
	$io_fecha 			= new class_fecha();
	require_once("sigesp_spg_reportes_class.php");
	$io_report = new sigesp_spg_reportes_class();
	//------------------------------------------------------------------------------------------------------------------------------		
		
	//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ldt_fecdes = $_GET["txtfecdes"];
	$ldt_fechas = $_GET["txtfechas"];
	$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
	$ls_codestpro1_min  = $_GET["codestpro1"];
	$ls_codestpro2_min  = $_GET["codestpro2"];
	$ls_codestpro3_min  = $_GET["codestpro3"];
	$ls_codestpro1h_max = $_GET["codestpro1h"];
	$ls_codestpro2h_max = $_GET["codestpro2h"];
	$ls_codestpro3h_max = $_GET["codestpro3h"];
    $ls_estclades       = $_GET["estclades"];
    $ls_estclahas       = $_GET["estclahas"];
	$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
	$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
	$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
	$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
	$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
	if($li_estmodest==1){
			$ls_codestpro4_min =  "0000000000000000000000000";
			$ls_codestpro5_min =  "0000000000000000000000000";
			$ls_codestpro4h_max = "0000000000000000000000000";
			$ls_codestpro5h_max = "0000000000000000000000000";
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
			{
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
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max==""))
			{
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
			else
			{
					$ls_codestpro1h  = $ls_codestpro1h_max;
					$ls_codestpro2h  = $ls_codestpro2h_max;
					$ls_codestpro3h  = $ls_codestpro3h_max;
					$ls_codestpro4h  = $ls_codestpro4h_max;
					$ls_codestpro5h  = $ls_codestpro5h_max;
			}
		}
		elseif($li_estmodest==2)
		{
			$ls_codestpro4_min  = $_GET["codestpro4"];
			$ls_codestpro5_min  = $_GET["codestpro5"];
			$ls_codestpro4h_max = $_GET["codestpro4h"];
			$ls_codestpro5h_max = $_GET["codestpro5h"];

			if(($ls_codestpro1_min=='**') ||($ls_codestpro1_min==''))
			{
				$ls_codestpro1_min='';
			}
			else
			{
			    $ls_codestpro1_min  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
			}
			if(($ls_codestpro2_min=='**') ||($ls_codestpro2_min==''))
			{
				$ls_codestpro2_min='';
			}
			else
			{
				$ls_codestpro2_min  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);

			}
			if(($ls_codestpro3_min=='**')||($ls_codestpro3_min==''))
			{
				$ls_codestpro3_min='';
			}
			else
			{

				$ls_codestpro3_min  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
			}
			if(($ls_codestpro4_min=='**') ||($ls_codestpro4_min==''))
			{
				$ls_codestpro4_min='';
			}
			else
			{
				$ls_codestpro4_min  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);


			}
			if(($ls_codestpro5_min=='**') ||($ls_codestpro5_min==''))
			{
				$ls_codestpro5_min='';
			}
			else
			{
				$ls_codestpro5_min  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			}


			if(($ls_codestpro1h_max=='**')||($ls_codestpro1h_max==''))
			{
				$ls_codestpro1h_max='';
			}
			else
			{
				$ls_codestpro1h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
			}
			if(($ls_codestpro2h_max=='**') ||($ls_codestpro2h_max==''))
			{
				$ls_codestpro2h_max='';
			}else
			{
				$ls_codestpro2h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
			}
			if(($ls_codestpro3h_max=='**') ||($ls_codestpro3h_max==''))
			{
				$ls_codestpro3h_max='';
			}else
			{
				$ls_codestpro3h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
			}
			if(($ls_codestpro4h_max=='**')  ||($ls_codestpro4h_max==''))
			{
				$ls_codestpro4h_max='';
			}else
			{
				$ls_codestpro4h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
			}
			if(($ls_codestpro5h_max=='**')  || ($ls_codestpro5h_max==''))
			{
				$ls_codestpro5h_max='';
			}else
			{
				$ls_codestpro5h_max  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
			}

			if(($ls_codestpro1_min=="")||($ls_codestpro2_min=="")||($ls_codestpro3_min=="")||($ls_codestpro4_min=="")||($ls_codestpro5_min==""))
			{
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
			else
			{
					$ls_codestpro1  = $ls_codestpro1_min;
					$ls_codestpro2  = $ls_codestpro2_min;
					$ls_codestpro3  = $ls_codestpro3_min;
					$ls_codestpro4  = $ls_codestpro4_min;
					$ls_codestpro5  = $ls_codestpro5_min;
			}
			if(($ls_codestpro1h_max=="")||($ls_codestpro2h_max=="")||($ls_codestpro3h_max=="")||($ls_codestpro4h_max=="")||($ls_codestpro5h_max==""))
			{
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
			else
			{
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
			print(" alert('No hay cuentas presupuestarias');"); 
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
			print(" alert('No hay cuentas presupuestarias');"); 
			print(" close();");
			print("</script>");
		}
	}
	else{
		$ls_cuentahas=$ls_cuentahas_max;
	}
	
	$ls_orden=$_GET["rborden"];
	$ls_prvbendes  = $_GET["txtprvbendes"];
	$ls_prvbenhas  = $_GET["txtprvbenhas"];
	$ldec_montodes = $_GET["txtmondes"];
	$ldec_montohas = $_GET["txtmonhas"];
	$ls_tipoprvben = $_GET["tipoprvben"];
	$ls_concepto   = $_GET["txtconcepto"];

    $ls_fechades=$io_function->uf_convertirfecmostrar($ldt_fecdes);
    $ls_fechahas=$io_function->uf_convertirfecmostrar($ldt_fechas);
		
	 /////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Operacion por Especifica desde la  Fecha ".$ldt_fecdes."  hasta ".$ldt_fechas." Desde la Cuenta ".$ls_cuentades."  hasta ".$ls_cuentahas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_operacion_por_especifica.php",$ls_desc_event);
	////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_estpre="";
	 switch ($ls_orden) {
	 	case "PC":
	 		$ls_estpre="PRECOMPROMETIDO";
	 		break;
	 	case "CP":
	 		$ls_estpre="COMPROMETIDO";
	 		break;
	 	case "CS":
	 		$ls_estpre="CAUSADO";
	 		break;
	 	case "PG":
	 		$ls_estpre="PAGADO";
	 		break;
	 }
	 
	  if($ai_est_pres=="PG"){
	    $ls_estado_presupuestaria="spg_operaciones.pagar  = 1";
	  }
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------------------------------------------------------------
	$ls_titulo="<b>OPERACION POR ESPECIFICA   ".$ls_estpre."</b> "; 
	$ls_fecha="<b> DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas." </b>";      
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    
	// Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
    $report_data=$io_report->uf_spg_reportes_operacion_especifica($ldt_fecdes,$ldt_fechas,$ls_cuentades,$ls_cuentahas,$ls_orden,$ls_prvbendes,$ls_prvbenhas,$ls_tipoprvben,$ldec_montodes,$ldec_montohas,$ls_concepto,
                                                                  $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estclades,
                                                                  $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$ls_estclahas);
    if ($report_data->EOF){
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	else{
		
	  	set_time_limit(1800);
	  	$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
	  	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	  	$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
	  	uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
	  	$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página

	  	$ls_spg_cuenta_ant='';
	  	$ls_spg_progra_ant='';
	  	$ld_totcuenta=0;
	  	$ld_totprogra=0;
	  	while (!$report_data->EOF) {
	  		$ls_procede=$report_data->fields["procede"];
			$ls_comprobante=$report_data->fields["comprobante"];
			$ldt_fecha=$report_data->fields["fecha"];
			$ls_codestpro1=$report_data->fields["codestpro1"];
			$ls_codestpro2=$report_data->fields["codestpro2"];
			$ls_codestpro3=$report_data->fields["codestpro3"];
			$ls_codestpro4=$report_data->fields["codestpro4"];
			$ls_codestpro5=$report_data->fields["codestpro5"];
			$ls_estcla=$report_data->fields["estcla"];
			$ls_spg_cuenta=trim($report_data->fields["spg_cuenta"]);
			$ls_procede_doc=$report_data->fields["procede_doc"];
			$ls_documento=$report_data->fields["documento"];
			$ls_operacion=$report_data->fields["operacion"];
			$ls_descripcion=$report_data->fields["descripcion"];
			$ld_monto=$report_data->fields["monto"];
			$li_orden=$report_data->fields["orden"];
			$ls_apebene=$report_data->fields["apebene"];
			$ls_nombene=$report_data->fields["nombene"];
			$ls_cod_pro=$report_data->fields["cod_pro"];
			$ls_ced_bene=$report_data->fields["ced_bene"];
			$ls_den_spg_cta=$report_data->fields["den_spg_cta"];
			$ls_nom_benef=$report_data->fields["nom_benef"];
			$ls_tipo_destino=$report_data->fields["tipo_destino"];
			//echo $ls_codestpro1." - ".$ls_codestpro2." - ".$ls_codestpro3;
			$ls_programatica=$io_function_report->uf_formato_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5);
			//echo $ls_programatica.'<br>'; 
			if($ls_spg_progra_ant==''){
				$ls_spg_progra_ant=$ls_programatica;
				if($ls_spg_cuenta_ant==''){
					//Es el primer registro se imprime la primera cabecera de cuenta
					//y se acumula el primer registro en la_data se acumula el monto para el total por cuenta
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto;
					$ls_denestpro = $io_function_report->uf_denominacion_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estcla);
					uf_print_cabecera($ls_spg_cuenta,$ls_den_spg_cta,$ls_programatica,$ls_denestpro,$io_pdf);
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
				}
				elseif ($ls_spg_cuenta_ant==$ls_spg_cuenta){
					//Este registro pertenece al mismo grupo de la cuenta anterior
					//se acumula el registro en la_data y se acumula el monto para el total por cuenta
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto;
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
					
				}
				elseif ($ls_spg_cuenta_ant!=$ls_spg_cuenta){
					//Este regristro ya no pertenece al grupo de la cuenta anterior
					//imprimimos el detalle acumulado de la cuenta anterior y su total
					uf_print_detalle($la_data,$io_pdf);
					$ld_totcuenta=number_format($ld_totcuenta,2,",",".");
					uf_print_pie_cabecera($ld_totcuenta,$io_pdf,'Total de la Cuenta '.$ls_spg_cuenta_ant);
					$ld_totcuenta=0; //inicializamoes el totalizador de la cuenta
					
					//Imprimimos la cabecera de la proxima cuenta
					$ls_denestpro = $io_function_report->uf_denominacion_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estcla);
					uf_print_cabecera($ls_spg_cuenta,$ls_den_spg_cta,$ls_programatica,$ls_denestpro,$io_pdf);
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					//inicializamos la_data
					$la_data=array();
					//acumulamos nuevamente en la data el detalle de la proxima cuenta
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto;
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
				}
			}
			elseif ($ls_spg_progra_ant==$ls_programatica){
				$ls_spg_progra_ant=$ls_programatica;
				if($ls_spg_cuenta_ant==''){
					//Es el primer registro se imprime la primera cabecera de cuenta
					//y se acumula el primer registro en la_data se acumula el monto para el total por cuenta
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto; 
					$ls_denestpro = $io_function_report->uf_denominacion_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estcla);
					uf_print_cabecera($ls_spg_cuenta,$ls_den_spg_cta,$ls_programatica,$ls_denestpro,$io_pdf);
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
				}
				elseif ($ls_spg_cuenta_ant==$ls_spg_cuenta){
					//Este registro pertenece al mismo grupo de la cuenta anterior
					//se acumula el registro en la_data y se acumula el monto para el total por cuenta
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto;
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
					
				}
				elseif ($ls_spg_cuenta_ant!=$ls_spg_cuenta){
					//Este regristro ya no pertenece al grupo de la cuenta anterior
					//imprimimos el detalle acumulado de la cuenta anterior y su total
					uf_print_detalle($la_data,$io_pdf);
					$ld_totcuenta=number_format($ld_totcuenta,2,",",".");
					uf_print_pie_cabecera($ld_totcuenta,$io_pdf,'Total de la Cuenta '.$ls_spg_cuenta_ant);
					$ld_totcuenta=0; //inicializamoes el totalizador de la cuenta
					
					//Imprimimos la cabecera de la proxima cuenta
					$ls_denestpro = $io_function_report->uf_denominacion_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estcla);
					uf_print_cabecera($ls_spg_cuenta,$ls_den_spg_cta,$ls_programatica,$ls_denestpro,$io_pdf);
					$ls_spg_cuenta_ant=$ls_spg_cuenta;
					//inicializamos la_data
					$la_data=array();
					//acumulamos nuevamente en la data el detalle de la proxima cuenta
					$ld_totcuenta = $ld_totcuenta + $ld_monto;
					$ld_totprogra = $ld_totprogra + $ld_monto;
					$ld_monto=number_format($ld_monto,2,",",".");
					$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
				}
			}
			elseif ($ls_spg_progra_ant!=$ls_programatica){
				uf_print_detalle($la_data,$io_pdf);
				$ld_totcuenta=number_format($ld_totcuenta,2,",",".");
				$ld_totprogra=number_format($ld_totprogra,2,",",".");
				uf_print_pie_cabecera($ld_totcuenta,$io_pdf,'Total de la Cuenta '.$ls_spg_cuenta_ant);
				uf_print_pie_cabecera($ld_totprogra,$io_pdf,'Total de la Programatica '.$ls_spg_progra_ant);
				$ls_spg_progra_ant=$ls_programatica;
				$ld_totprogra=0; //inicializamos el totalizador de la programatica
				$ld_totcuenta=0; //inicializamos el totalizador de la cuenta
					
				//Imprimimos la cabecera de la proxima cuenta
				$ls_denestpro = $io_function_report->uf_denominacion_estructura($ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4, $ls_codestpro5, $ls_estcla);
				uf_print_cabecera($ls_spg_cuenta,$ls_den_spg_cta,$ls_programatica,$ls_denestpro,$io_pdf);
				$ls_spg_cuenta_ant=$ls_spg_cuenta;
				//inicializamos la_data
				$la_data=array();
				//acumulamos nuevamente en la data el detalle de la proxima cuenta
				$ld_totcuenta = $ld_totcuenta + $ld_monto;
				$ld_totprogra = $ld_totprogra + $ld_monto;
				$ld_monto=number_format($ld_monto,2,",",".");
				$la_data[]=array('documento'=>$ls_comprobante,'procede'=>$ls_procede,'fecha'=>$ldt_fecha,
					                 'beneficiario'=>$ls_nom_benef,'concepto'=>$ls_descripcion,'monto'=>$ld_monto);
			}
			
			
			
			$report_data->MoveNext();
	  	}
	    uf_print_detalle($la_data,$io_pdf);
		$ld_totcuenta=number_format($ld_totcuenta,2,",",".");
		$ld_totprogra=number_format($ld_totprogra,2,",",".");
		uf_print_pie_cabecera($ld_totcuenta,$io_pdf,'Total de la Cuenta '.$ls_spg_cuenta_ant);
		uf_print_pie_cabecera($ld_totprogra,$io_pdf,'Total de la Programatica '.$ls_spg_progra_ant);
		$io_pdf->ezStopPageNumbers(1,1);
		$io_pdf->ezStream();
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
?>