<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');
	
	
	   // para crear el libro excel
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "spg_acumulado_x_cuentas.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_periodo_comp // Descripci�n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,16,$as_titulo); // Agregar el t�tulo
		
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
    function uf_print_encabezado_pagina2($as_titulo,$as_titulo1,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina2
		//		    Acess: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_periodo_comp // Descripci�n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci�n del per�odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina manejando esructuras presupuestarias
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,30,1000,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],10,550,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(16,$as_titulo);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,550,16,$as_titulo); // Agregar el t�tulo
	    $li_tm=$io_pdf->getTextWidth(16,$as_titulo1);
		$tm=505-($li_tm/2);
		$io_pdf->addText($tm,530,16,$as_titulo1); // Agregar el t�tulo
		
		$io_pdf->addText(900,550,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(900,540,10,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		
	}// end function uf_print_encabezadopagina

	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 27/09/2006 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		/*$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();*/
		//$io_pdf->ezSetY(460);
		$la_data=array(array('cuenta'=>'<b>Cuenta</b>','denominacion'=>'<b>Denominaci�n</b>','previsto'=>'<b>Previsto</b>',
		                     'aumento'=>'<b>Aumento</b>','disminuci�n'=>'<b>Disminuci�n</b>','devengado'=>'<b>Devengado</b>',
							 'cobrado'=>'<b>Cobrado</b>','cobrado_anticipado'=>'<b>Cobrado Anticipado</b>',
							 'montoactualizado'=>'<b>Monto Actualizado</b>','porcobrar'=>'<b>Por Cobrar</b>'));
		
		$la_columna=array('cuenta'=>'','denominacion'=>'','previsto'=>'','aumento'=>'','disminuci�n'=>'','devengado'=>'',
		                  'cobrado'=>'','cobrado_anticipado'=>'','montoactualizado'=>'','porcobrar'=>'');
		$la_config=array('showHeadings'=>0,     // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la 
						 			   'denominacion'=>array('justification'=>'center','width'=>160), // Justificaci�n y  
						 			   'previsto'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la 
						 			   'aumento'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la 
									   'disminuci�n'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la 
									   'devengado'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de 
									   'cobrado'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de 
									   'cobrado_anticipado'=>array('justification'=>'center','width'=>90), // Justificaci�n  
									   'montoactualizado'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho 
									   'porcobrar'=>array('justification'=>'center','width'=>90))); // Justificaci�n y ancho de la 
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	/*$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');*/
	unset($la_data);
	unset($la_columnas);
	unset($la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo="Monto Bs.F.";
		}
		else
		{
			$ls_titulo="Monto Bs.";
		}
		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la 
						 			   'denominacion'=>array('justification'=>'left','width'=>160), // Justificaci�n y ancho de la 
						 			   'previsto'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
						 			   'aumento'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
									   'disminuci�n'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
									   'devengado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de 
									   'cobrado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de 
									   'cobrado_anticipado'=>array('justification'=>'right','width'=>90), // Justificaci�n  
									   'montoactualizado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho 
									   'porcobrar'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la 
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominaci�n</b>',
						   'previsto'=>'<b>Previsto</b>',
						   'aumento'=>'<b>Aumento</b>',
						   'disminuci�n'=>'<b>Disminuci�n</b>',
						   'devengado'=>'<b>Devengado</b>',
						   'cobrado'=>'<b>Cobrado</b>',
						   'cobrado_anticipado'=>'<b>Cobrado Anticipado</b>',
						   'montoactualizado'=>'<b>Monto Actualizado</b>',
						   'porcobrar'=>'<b>Por Cobrar</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($la_data_tot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : funci�n que imprime el fin de la cabecera de cada p�gina
		//	   Creado Por: Ing. Yozelin Barrag�n
		// Fecha Creaci�n: 27/09/2006 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 8,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'colGap'=>0, // separacion entre tablas
						 'width'=>990, // Ancho de la tabla
						 'maxWidth'=>990, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('total'=>array('justification'=>'center','width'=>240), // Justificaci�n y ancho de la 
						 			   'previsto'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
						 			   'aumento'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
									   'disminuci�n'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de la 
									   'devengado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de 
									   'cobrado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho de 
									   'cobrado_anticipado'=>array('justification'=>'right','width'=>90), // Justificaci�n  
									   'montoactualizado'=>array('justification'=>'right','width'=>90), // Justificaci�n y ancho 
									   'porcobrar'=>array('justification'=>'right','width'=>90))); // Justificaci�n y ancho de la 
		$la_columnas=array('total'=>'',
						   'previsto'=>'',
						   'aumento'=>'',
						   'disminuci�n'=>'',
						   'devengado'=>'',
						   'cobrado'=>'',
						   'cobrado_anticipado'=>'',
						   'montoactualizado'=>'',
						   'porcobrar'=>'');
		$io_pdf->ezTable($la_data_tot,$la_columnas,'',$la_config);
		unset($la_data_tot);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_pie_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------
	 function uf_print_cabecera_estructura( $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
		                        $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$io_pdf)
	{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera
	//		   Access: private 
	//	    Arguments: as_programatica // programatica del comprobante
	//	    		   as_denestpro5 // denominacion de la programatica del comprobante
	//	    		   io_pdf // Objeto PDF
	//    Description: funci�n que imprime la cabecera de cada p�gina
	//	   Creado Por: Ing. Jennifer Rivero
	// Fecha Creaci�n: 17/11/2008 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$ls_estmodest  = $_SESSION["la_empresa"]["estmodest"];
		$li_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
		$li_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
		$li_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
		$li_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
		$li_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	    $li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	    $li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	    $li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	    $li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
		$ls_codestpro1    = trim(substr($ls_codestpro1,-$li_loncodestpro1));
		$ls_codestpro2    = trim(substr($ls_codestpro2,-$li_loncodestpro2));
		$ls_codestpro3    = trim(substr($ls_codestpro3,-$li_loncodestpro3));
		$ls_codestpro4    = trim(substr($ls_codestpro4,-$li_loncodestpro4));
		$ls_codestpro5    = trim(substr($ls_codestpro5,-$li_loncodestpro5));
		
		if ($ls_estmodest==1)
		{
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' =>7, // Tama�o de Letras
							 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
							 'showLines'=>0, // Mostrar L�neas
							 'shaded'=>0, // Sombra entre l�neas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>990, // Ancho de la tabla
							 'maxWidth'=>990, // Ancho M�ximo de la tabla
							 'xOrientation'=>'center', // Orientaci�n de la tabla
							 'xPos'=>290, // Orientaci�n de la tabla
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>150),									  
										   'codestpro'=>array('justification'=>'right','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>320)));		
			$io_pdf->ezTable($ls_datat1,'','',$la_config);
		}
		else
		{
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);
			$ls_datat1[4]=array('nombre'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro'=>$ls_codestpro4,'denom'=>$ls_denestpro4);
			$ls_datat1[5]=array('nombre'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro'=>$ls_codestpro5,'denom'=>$ls_denestpro5);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' => 6, // Tama�o de Letras
							 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
							 'showLines'=>0, // Mostrar L�neas
							 'shaded'=>0, // Sombra entre l�neas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>990, // Ancho de la tabla
							 'maxWidth'=>990, // Ancho M�ximo de la tabla
							 'xOrientation'=>'center', // Orientaci�n de la tabla
							 'xPos'=>302, // Orientaci�n de la tabla
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>150),									  
										   'codestpro'=>array('justification'=>'right','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>320)));			
		   $io_pdf->ezTable($ls_datat1,'','',$la_config);	
		}
		unset($ls_datat1);
		unset($la_config);			
	}// end function uf_print_cabecera
//--------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spi_reporte.php");
		$io_report = new sigesp_spi_reporte();
		require_once("sigesp_spi_funciones_reportes.php");
		$io_function_report = new sigesp_spi_funciones_reportes();
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../class_funciones_ingreso.php");
		$io_fun_ingreso=new class_funciones_ingreso();			
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
//--------------------------------------------------  Par�metros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-01-01";
		$ldt_fecini_rep="01/01/".$li_ano;
		$ls_cmbmeshas = $_GET["cmbmeshas"];
		$ls_saldocero = $_GET["saldocero"];		
		$ls_mes=$ls_cmbmeshas;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	    $ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		
		if($ls_estpreing==1)
		{
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
	
			if($ls_modalidad==1)
			{
				$ls_codestpro4_min =  "0000000000000000000000000";
				$ls_codestpro5_min =  "0000000000000000000000000";
				$ls_codestpro4h_max = "0000000000000000000000000";
				$ls_codestpro5h_max = "0000000000000000000000000";
				if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
				{
				 $arrResultado = $io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);
				 $ls_codestpro1_min = $arrResultado['as_codestpro1'];
				 $ls_codestpro2_min = $arrResultado['as_codestpro2'];
				 $ls_codestpro3_min = $arrResultado['as_codestpro3'];
				 $ls_codestpro4_min = $arrResultado['as_codestpro4'];
				 $ls_codestpro5_min = $arrResultado['as_codestpro5'];
				 $ls_estclades = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,$ls_codestpro3h_max,$ls_codestpro4h_max,$ls_codestpro5h_max,$ls_estclahas);
				 $ls_codestpro1h_max = $arrResultado['as_codestpro1'];
				 $ls_codestpro2h_max = $arrResultado['as_codestpro2'];
				 $ls_codestpro3h_max = $arrResultado['as_codestpro3'];
				 $ls_codestpro4h_max = $arrResultado['as_codestpro4'];
				 $ls_codestpro5h_max = $arrResultado['as_codestpro5'];
				 $ls_estclahas = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
			elseif($ls_modalidad==2)
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);
				 $ls_codestpro1_min = $arrResultado['as_codestpro1'];
				 $ls_codestpro2_min = $arrResultado['as_codestpro2'];
				 $ls_codestpro3_min = $arrResultado['as_codestpro3'];
				 $ls_codestpro4_min = $arrResultado['as_codestpro4'];
				 $ls_codestpro5_min = $arrResultado['as_codestpro5'];
				 $ls_estclades = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,$ls_codestpro3h_max,$ls_codestpro4h_max,$ls_codestpro5h_max,$ls_estclahas);
				 $ls_codestpro1h_max = $arrResultado['as_codestpro1'];
				 $ls_codestpro2h_max = $arrResultado['as_codestpro2'];
				 $ls_codestpro3h_max = $arrResultado['as_codestpro3'];
				 $ls_codestpro4h_max = $arrResultado['as_codestpro4'];
				 $ls_codestpro5h_max = $arrResultado['as_codestpro5'];
				 $ls_estclahas = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
			
			$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
			if($ls_modalidad==1)
			{
				if (($ls_codestpro1<>"")&&($ls_codestpro2=="")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3<>""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3);
				}
				else
				{
				 $ls_programatica_desde1="";
				 $ls_programatica_hasta1="";
				}
			}
			else
			{
				$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5)."-".$ls_estclades;
				$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3)."-".substr($ls_codestpro4h,-$ls_loncodestpro4)."-".substr($ls_codestpro5h,-$ls_loncodestpro5)."-".$ls_estclahas;
			}
		}
		$cmbnivel=$_GET["cmbnivel"];
		if($cmbnivel=="s1")
		{
          $ls_cmbnivel="1";
		}
		else
		{
          $ls_cmbnivel=$cmbnivel;
		}
        $ls_subniv=$_GET["checksubniv"];
		if($ls_subniv==1)
		{
		  $lb_subniv=true;
		}
		else
		{
		  $lb_subniv=false;
		}
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		
		
		
		$ls_desc_event="Solicitud de Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin;
		$io_fun_ingreso->uf_load_seguridad_reporte("SPI","sigesp_spi_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
     //----------------------------------------------------  Par�metros del encabezado  --------------------------------------------
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$ls_titulo=" <b> ACUMULADO POR CUENTAS  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin." </b> ";
		if($ls_estpreing==1)
		{
	    	$ls_titulo1="<b> DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1." </b>"; 
		}
		$ls_tiporeporte=$_GET["tiporeporte"];
		global $ls_tiporeporte;
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		
		if($ls_tiporeporte==1)
		{
			require_once("sigesp_spi_reportebsf.php");
			$io_report=new sigesp_spi_reportebsf();
		}              
    //--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
	if($ls_estpreing==1)
	{
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	}
	
	
	
	/*
	
	
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(3.4,3,3,3); // Configuraci�n de los margenes en cent�metros
	uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
	$io_pdf->ezStartPageNumbers(980,40,10,'','',1); // Insertar el n�mero de p�gina
	$ld_total_previsto=0;
	$ld_total_aumento=0;
	$ld_total_disminucion=0;
	$ld_total_devengado=0;
	$ld_total_cobrado=0;
	$ld_total_cobrado_anticipado=0;
	$ld_total_monto_actualizado=0;
	$ld_total_por_cobrar=0;
	$arrResultado = $io_function_report->uf_spi_reporte_select_max_cuenta($cuentamax);
	$cuentamax = $arrResultado['as_spi_cuenta'];
	$lb_valido = $arrResultado['lb_valido'];
	$arrResultado = $io_function_report->uf_spi_reporte_select_min_cuenta($cuentamin);
	$cuentamin = $arrResultado['as_spi_cuenta'];
	$lb_valido = $arrResultado['lb_valido'];
	$vacio = "";
	
	
	*/
	$cuentamin = $_GET["cuentadesde"];
	$cuentamax = $_GET["cuentahasta"];
	if(empty($cuentamin))
	{
	 	$arrResultado = $io_function_report->uf_spi_reporte_select_max_cuenta($cuentamax);
		$cuentamax = $arrResultado['as_spi_cuenta'];
		$lb_valido = $arrResultado['lb_valido'];
	}
	if(empty($cuentamax))
	{
	 	$arrResultado = $io_function_report->uf_spi_reporte_select_min_cuenta($cuentamin);
		$cuentamin = $arrResultado['as_spi_cuenta'];
		$lb_valido = $arrResultado['lb_valido'];
	}
	
	
		$contfilas=0;
		$ls_titulo=" ACUMULADO POR CUENTAS  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin."   ";
		if($ls_estpreing==1)
		{
	    	$ls_titulo2=" DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1.""; 
		}
		else
		{
			$ls_titulo2="";
		}
		
		
		
		
		$fecha=date('d/m/Y');
		$hora=date('H:i');
		$ls_desc_event="Solicitud de Reporte acumulado por cuentas del Presupuesto En Formato Excel Desde la Programatica  ".$ls_programatica_desde." hasta ".$ls_programatica_hasta;
		$io_function_report->uf_load_seguridad_reporte("SPI","sigesp_spi_r_acum_x_cuentas.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_datacenter=&$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9'); 
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,13);
		$lo_hoja->set_column(5,7,30);
		$contfilas++;
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(0, 6, $fecha,$lo_dataright);
		$lo_hoja->write(1, 6, $hora,$lo_dataright);
		$lo_hoja->write(1, 3,$ls_titulo2,$lo_encabezado);
		$contfilas++;
	
	    $ls_spg_cuenta_ant="";
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_monto_actualizado=0;
		$ld_total_compromiso=0;
		$ld_total_precompromiso=0;
		$ld_total_compromiso=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_por_paga=0;
		$li_row=2;
	
    if ($ls_estpreing==1)
	{
		
		
		
		$io_report->uf_spi_reporte_acum_cuentas2($cuentamin,$cuentamax,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
		                                         $ls_codestpro4,$ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,
												 $ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas,$ls_cmbnivel);
		
		$li_totfila=$io_report->dts_reporte->getRowCount("spi_cuenta");
		
		$ld_total_previsto=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_devengado=0;
		$ld_total_cobrado=0;
		$ld_total_cobrado_anticipado=0;
		$ld_total_monto_actualizado=0;
		$ld_total_por_cobrar=0;
		
		
		
		
		
		
		$lo_hoja->write($contfilas, 1, "Cuenta",$lo_titulo);
		$lo_hoja->write($contfilas, 2, "Denominaci�n",$lo_titulo);
		$lo_hoja->write($contfilas, 3, "Previsto",$lo_titulo);
		$lo_hoja->write($contfilas, 4, "Aumento",$lo_titulo);
		$lo_hoja->write($contfilas, 5, "Disminuci�n",$lo_titulo);
		$lo_hoja->write($contfilas, 6, "Devengado",$lo_titulo);
		$lo_hoja->write($contfilas, 7, "Cobrado",$lo_titulo);
		$lo_hoja->write($contfilas, 8, "Cobrado Anticipado",$lo_titulo);
		$lo_hoja->write($contfilas, 9, "Monto Actualizado",$lo_titulo);
		$lo_hoja->write($contfilas, 10,"Por Cobrar",$lo_titulo);
		$contfilas++;
		
	
		
		for($j=1;($j<=$li_totfila);$j++)
		{
			  $as_spg_cuenta=trim($io_report->dts_reporte->data["spi_cuenta"][$j]);
			  $lb_valido=$io_report->uf_spi_reporte_detalle_acumulado_cuentas($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                              $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,
	                                              $ls_codestpro5h,$ls_estclades,$ls_estclahas,
												  $as_spg_cuenta,$ldt_fecfin,$j,$ls_saldocero);												
															
			 

												  
									  
			 $li_tot=0;
			 $li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");
			
				  $thisPageNum=$io_pdf->ezPageCount;
				  $ls_spi_cuenta=$io_report->dts_reporte->data["spi_cuenta"][$j];
				  $ls_denominacion=$io_report->dts_reporte->data["denominacion"][$j];
				  $ls_nivel=$io_report->dts_reporte->data["nivel"][$j];
				  $ls_status=$io_report->dts_reporte->data["status"][$j];
				  $ld_previsto=$io_report->dts_reporte->data["previsto"][$j];
				  $ld_aumento=$io_report->dts_reporte->data["aumento"][$j];
				  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$j];
				  $ld_devengado=$io_report->dts_reporte->data["devengado"][$j];
				  $ld_cobrado=$io_report->dts_reporte->data["cobrado"][$j];
				  $ld_cobrado_anticipado=0;
				  $ld_monto_actualizado=$ld_previsto+$ld_aumento-$ld_disminucion-$ld_devengado;
				  $ld_por_cobrar=$ld_devengado-$ld_cobrado;
				 
				  if($ls_nivel==1)
				  {
					  $ld_total_previsto=$ld_total_previsto+$ld_previsto;
					  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
					  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
					  $ld_total_devengado=$ld_total_devengado+$ld_devengado;
					  $ld_total_cobrado=$ld_total_cobrado+$ld_cobrado;
					  $ld_total_cobrado_anticipado=$ld_total_cobrado_anticipado+$ld_cobrado_anticipado;
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
					  $ld_total_por_cobrar=$ld_total_por_cobrar+$ld_por_cobrar;
				  } 
				$ab_agregar=true;  
				if ($ls_saldocero=='1')
				{
					if(($ld_previsto==0)&&($ld_aumento==0)&&($ld_disminucion==0)&&($ld_devengado==0)&&($ld_cobrado==0)&&($ld_cobrado_anticipado==0)&&($ld_monto_actualizado==0)&&($ld_por_cobrar==0))
					{
						$ab_agregar=false;
					}
				}
				if ($ab_agregar)
				{
				  $ld_previsto=number_format($ld_previsto,2,",",".");
				  $ld_aumento=number_format($ld_aumento,2,",",".");
				  $ld_disminucion=number_format($ld_disminucion,2,",",".");
				  $ld_devengado=number_format($ld_devengado,2,",",".");
				  $ld_cobrado=number_format($ld_cobrado,2,",",".");
				  $ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
				  $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
				  $ld_por_cobrar=number_format($ld_por_cobrar,2,",","."); 
					  
					$lo_hoja->write($contfilas, 1,$ls_spi_cuenta,$lo_dataleft);
					$lo_hoja->write($contfilas, 2,$ls_denominacion,$lo_dataleft);
					$lo_hoja->write($contfilas, 3,$ld_previsto,$lo_dataright);
					$lo_hoja->write($contfilas, 4,$ld_aumento,$lo_dataright);
					$lo_hoja->write($contfilas, 5,$ld_disminucion,$lo_dataright);
					$lo_hoja->write($contfilas, 6,$ld_devengado,$lo_dataright);
					$lo_hoja->write($contfilas, 7,$ld_cobrado,$lo_dataright);
					$lo_hoja->write($contfilas, 8,$ld_cobrado_anticipado,$lo_dataright);
					$lo_hoja->write($contfilas,9,$ld_monto_actualizado,$lo_dataright);
					$lo_hoja->write($contfilas, 10,$ld_por_cobrar,$lo_dataright);	
									
					$contfilas++;
				}	  
 
				 $ld_previsto=str_replace('.','',$ld_previsto);
				 $ld_previsto=str_replace(',','.',$ld_previsto);		
				 $ld_aumento=str_replace('.','',$ld_aumento);
				 $ld_aumento=str_replace(',','.',$ld_aumento);		
				 $ld_disminucion=str_replace('.','',$ld_disminucion);
				 $ld_disminucion=str_replace(',','.',$ld_disminucion);		
				 $ld_monto_actualizado=str_replace('.','',$ld_monto_actualizado);
				 $ld_monto_actualizado=str_replace(',','.',$ld_monto_actualizado);
				 $ld_devengado=str_replace('.','',$ld_devengado);
				 $ld_devengado=str_replace(',','.',$ld_devengado);		
				 $ld_cobrado=str_replace('.','',$ld_cobrado);
				 $ld_cobrado=str_replace(',','.',$ld_cobrado);		
				 $ld_cobrado_anticipado=str_replace('.','',$ld_cobrado_anticipado);
				 $ld_cobrado_anticipado=str_replace(',','.',$ld_cobrado_anticipado);		
				 $ld_por_cobrar=str_replace('.','',$ld_por_cobrar);
				 $ld_por_cobrar=str_replace(',','.',$ld_por_cobrar);	

		 }
	
	  $ld_total_previsto=number_format($ld_total_previsto,2,",",".");
	  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
	  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
	  $ld_total_devengado=number_format($ld_total_devengado,2,",",".");
	  $ld_total_cobrado=number_format($ld_total_cobrado,2,",",".");
	  $ld_total_cobrado_anticipado=number_format($ld_total_cobrado_anticipado,2,",",".");
	  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
	  $ld_total_por_cobrar=number_format($ld_total_por_cobrar,2,",",".");
				
	$lo_hoja->write($contfilas, 1,'',$lo_dataleft);
	$lo_hoja->write($contfilas, 2,"Total",$lo_dataleft);
	$lo_hoja->write($contfilas, 3,$ld_total_previsto,$lo_dataright);
	$lo_hoja->write($contfilas, 4,$ld_total_aumento,$lo_dataright);
	$lo_hoja->write($contfilas, 5,$ld_total_disminucion,$lo_dataright);
	$lo_hoja->write($contfilas, 6,$ld_total_devengado,$lo_dataright);
	$lo_hoja->write($contfilas, 7,$ld_total_cobrado,$lo_dataright);
	$lo_hoja->write($contfilas, 8,$ld_total_cobrado_anticipado,$lo_dataright);
	$lo_hoja->write($contfilas, 9,$ld_total_monto_actualizado,$lo_dataright);
	$lo_hoja->write($contfilas, 10,$ld_por_cobrar,$lo_dataright);				
	$contfilas++;	
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"acumulado_por_cuentas.xls\"");
		header("Content-Disposition: inline; filename=\"acumulado_por_cuentas.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		//print(" close();");
		print("</script>");
	 
	
	 }
	unset($io_report);
	unset($io_funciones);
?> 