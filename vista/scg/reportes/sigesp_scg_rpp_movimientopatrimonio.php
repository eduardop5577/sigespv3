<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
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
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_movimiento_cuentas.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_titulo2,$as_titulo3,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,520,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=406-($li_tm/2);
		$io_pdf->addText($tm,550,11,$as_titulo); // Agregar el título		
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=406-($li_tm/2);
		$io_pdf->addText($tm,540,11,$as_titulo1); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=406-($li_tm/2);
		$io_pdf->addText($tm,530,11,$as_titulo2); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=406-($li_tm/2);
		$io_pdf->addText($tm,520,11,$as_titulo3); // Agregar el título

		$io_pdf->addText(710,540,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(710,530,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(710,520,8,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_saldos($la_data, $fecsaldo, $capfiscal, $transferencias, $situado, $resacumulado, $resejercicio, $total, $io_pdf){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('saldos'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
									   'nota'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna	
									   'capital_fiscal'=>array('justification'=>'right','width'=>90),
									   'transferencia'=>array('justification'=>'right','width'=>90),
									   'situado_aportes'=>array('justification'=>'right','width'=>90),
		 							   'resultado_acumulado'=>array('justification'=>'right','width'=>90),
		                  			   'resultado_ejercicio'=>array('justification'=>'right','width'=>90),
									   'total'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('saldos'=>'SALDOS AL '.$fecsaldo,
						   'nota'=>'NOTA',
						   'capital_fiscal'=>uf_is_negative($capfiscal),
						   'transferencia'=>uf_is_negative($transferencias),
						   'situado_aportes'=>uf_is_negative($situado),
						   'resultado_acumulado'=>uf_is_negative($resacumulado),
						   'resultado_ejercicio'=>uf_is_negative($resejercicio),
						   'total'=>uf_is_negative($total));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($fecsaldo, $capfiscal, $transferencias, $situado, $resacumulado, $resejercicio, $total, $io_pdf){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('saldos'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
									   'nota'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna	
									   'capital_fiscal'=>array('justification'=>'right','width'=>90),
									   'transferencia'=>array('justification'=>'right','width'=>90),
									   'situado_aportes'=>array('justification'=>'right','width'=>90),
		 							   'resultado_acumulado'=>array('justification'=>'right','width'=>90),
		                  			   'resultado_ejercicio'=>array('justification'=>'right','width'=>90),
									   'total'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('saldos'=>'',
						   'nota'=>'',
						   'capital_fiscal'=>'',
						   'transferencia'=>'',
						   'situado_aportes'=>'',
						   'resultado_acumulado'=>'',
						   'resultado_ejercicio'=>'',
						   'total'=>'');
		$la_data[] = array('saldos'=>'SALDOS AL '.$fecsaldo,
		                   'nota'=>'',
		                   'capital_fiscal'=> number_format(abs($capfiscal),2,",","."),
		                   'transferencia'=> number_format(abs($transferencias),2,",","."),
		                   'situado_aportes'=> number_format(abs($situado),2,",","."),
		                   'resultado_acumulado'=> number_format(abs($resacumulado),2,",","."),
		                   'resultado_ejercicio'=> number_format(abs($resejercicio),2,",","."),
		                   'total'=>number_format(abs($total),2,",","."));
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado($io_pdf){
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xPos'=>486,	
						 'cols'=>array('capital_fiscal'=>array('justification'=>'center','width'=>90),
									   'transferencia'=>array('justification'=>'center','width'=>90),
									   'situado_aportes'=>array('justification'=>'center','width'=>90),
		 							   'resultado_acumulado'=>array('justification'=>'center','width'=>90),
		                  			   'resultado_ejercicio'=>array('justification'=>'center','width'=>90),
									   'total'=>array('justification'=>'center','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('capital_fiscal'=>'',
						   'transferencia'=>'',
						   'situado_aportes'=>'',
						   'resultado_acumulado'=>'',
						   'resultado_ejercicio'=>'',
						   'total'=>'');
		
		$la_data[] = array('capital_fiscal'=>'CAPITAL FISCAL/INSTITUCIONAL',
		                   'transferencia'=>'TRANSFERENCIAS DONACIONES Y APORTES POR CAPITALIZAR RECIBIDOS',
		                   'situado_aportes'=>'SITUADO Y APORTES ESPECIALES',
		                   'resultado_acumulado'=>'RESULTADOS ACUMULADOS',
		                   'resultado_ejercicio'=>'RESULTADO DEL EJERCICIO',
		                   'total'=>'TOTAL');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_is_debhab($ad_monto,$as_debhab) {
		$ls_monto = '';
		if ($as_debhab == 'D') {
			$ls_monto = '('.number_format(abs($ad_monto),2,",",".").')';
		}
		else{
			$ls_monto = number_format(abs($ad_monto),2,",",".");
		}
		
		return $ls_monto;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_is_negative($ad_monto) {
		if ($ad_monto<0) {
			return '('.number_format(abs($ad_monto),2,",",".").')';
		}
		else{
			return number_format($ad_monto,2,",",".");
		}
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_firmas($io_pdf) {
		global $io_pdf;
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->setLineStyle(1);

		
		$io_pdf->line(45,95,160,95);
		$io_pdf->line(210,95,350,95);

		$io_pdf->addText(45,100,7,"Firma:"); // Agregar el título
		$io_pdf->addText(45,85,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(45,75,7,"Cargo:"); // Agregar el título
		$io_pdf->addText(210,100,7,"Firma:"); // Agregar el título
		$io_pdf->addText(210,85,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(210,75,7,"Cargo:"); // Agregar el título
		
		$io_pdf->Rectangle(400,60,150,100);
		$io_pdf->addText(430,100,7,"SELLO INSTITUCIONAL"); // Agregar el título
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//------------------------------------------------- Instanciando las Clases del Reporte -----------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("class_funciones_scg.php");
	require_once("sigesp_scg_class_movimientopatrimonio.php");
	require_once("sigesp_scg_class_bal_general.php");
	$io_balance   = new sigesp_scg_class_bal_general();
	$io_funciones = new class_funciones();
	$io_report    = new sigesp_scg_class_movimientopatrimonio();
	$io_fecha     = new class_fecha();
	$io_fun_scg   = new class_funciones_scg();
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_cmbmes=$_GET["cmbmes"];
	$ls_cmbagno=$_GET["cmbagno"];
	$ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ldt_periodo = $_SESSION["la_empresa"]["periodo"];
	$li_ano      = substr($ldt_periodo,0,4);
	$ls_titulo  = "<b> ".$_SESSION["la_empresa"]["nombre"]." </b>";
	$ls_titulo1 = "<b>ESTADO DE MOVIMIENTO DE LAS CUENTAS DE PATRIMONIO</b>";
	$ls_titulo2 = "<b> AL ".substr($ls_last_day, 0, 2)." DE ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." DE ".$li_ano."</b>";
	$ls_titulo3 = "<b>(EN BOLÍVARES)</b>";  
	//--------------------------------------------------------------------------------------------------------------------------------
    
	//--------------------------------------------------  Imprimiendo el Reporte  -----------------------------------------------------
	$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(4.8,3,1,1); // Configuración de los margenes en centímetros
	uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la página
	//$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
	
	//Calcular saldos iniciales
	$ld_fecsalini = $li_ano-1;
	$ls_fecsalini = '31/12/'.$ld_fecsalini;
	$ld_fecsalini = $ld_fecsalini.'-12-31';
	
	
	//Saldos capital fiscal (311 o 321)
	$ld_capfiscal = 0;
	$ld_saldo311  = $io_report->uf_obtener_saldo($ld_fecsalini, '311', 3);
	$ld_saldo321  = $io_report->uf_obtener_saldo($ld_fecsalini, '321', 3);
	$ld_capfiscal = $ld_saldo311 + $ld_saldo321;
	
	//Saldos transferencias (312 o 322)
	$ld_transferencia = 0;
	$ld_saldo312      = $io_report->uf_obtener_saldo($ld_fecsalini, '312', 3);
	$ld_saldo322      = $io_report->uf_obtener_saldo($ld_fecsalini, '322', 3);
	$ld_transferencia = $ld_saldo312 + $ld_saldo322;

	//Saldos situado (313 y 314 o 323 y 324)
	$ld_situado       = 0;
	$ld_saldo313_314  = $io_report->uf_obtener_saldo($ld_fecsalini, '313', 3) + $io_report->uf_obtener_saldo($ld_fecsalini, '314', 3);
	$ld_saldo323_324  = $io_report->uf_obtener_saldo($ld_fecsalini, '323', 3) + $io_report->uf_obtener_saldo($ld_fecsalini, '324', 3);
	$ld_situado       = $ld_saldo313_314 + $ld_saldo323_324;
	
	//Saldos resultados acumulado (31501 o 32501)
	$ld_resacumlado = 0;
	$ld_saldo31501  = $io_report->uf_obtener_saldo($ld_fecsalini, '31501', 4);
	$ld_saldo32501  = $io_report->uf_obtener_saldo($ld_fecsalini, '32501', 4);
	$ld_resacumlado = $ld_saldo31501 + $ld_saldo32501;
	
	//Saldos resultados ejercicio (31502 o 32502)
	$ld_resactual   = 0;
	$ld_saldo31502  = $io_report->uf_obtener_saldo($ld_fecsalini, '31502', 4);
	$ld_saldo32502  = $io_report->uf_obtener_saldo($ld_fecsalini, '32502', 4);
	$ld_resactual   = $ld_saldo31502 + $ld_saldo32502;
	
	//total
	$ld_total = $ld_capfiscal + $ld_transferencia + $ld_situado + $ld_resacumlado + $ld_resactual;
	
	//buscar movimiento de las cuentas de patrimonio
	$rs_data = $io_report->uf_obtener_movimiento($ldt_fechas);
	
	//totales de movimientos
	$ld_totcapfiscal = $ld_capfiscal;
	$ld_tottransfer  = $ld_transferencia;
	$ld_totsituado   = $ld_situado;
	$ld_totresacum   = $ld_resacumlado;
	$ld_totresejer   = $ld_resactual;
	$ld_tottotal     = $ld_total;
	$la_data         = array();
	while (!$rs_data->EOF) {
		$ls_cuenta   = trim($rs_data->fields['sc_cuenta']);
		$ls_debhab   = $rs_data->fields['debhab'];
		$ls_monto    = $rs_data->fields['monto'];
		if($ls_debhab=='H'){
			$ld_tottotal = $ld_tottotal + $ls_monto;
		}
		else{
			$ld_tottotal = $ld_tottotal - $ls_monto;
		}
		
			
		switch (substr($ls_cuenta,0,3)) {
			case '311'://capital fiscal
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>uf_is_debhab($ls_monto, $ls_debhab),'transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totcapfiscal = $ld_totcapfiscal + $ls_monto;
				}
				else{
					$ld_totcapfiscal = $ld_totcapfiscal - $ls_monto;
				}	
				break;
				
			case '321'://capital fiscal
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>uf_is_debhab($ls_monto, $ls_debhab),'transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totcapfiscal = $ld_totcapfiscal + $ls_monto;
				}
				else{
					$ld_totcapfiscal = $ld_totcapfiscal - $ls_monto;
				}
				break;
				
			case '312'://transferencia
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>uf_is_debhab($ls_monto, $ls_debhab),'situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_tottransfer = $ld_tottransfer + $ls_monto;
				}
				else {
					$ld_tottransfer = $ld_tottransfer - $ls_monto;
				}
				break;
				
			case '322'://transferencia
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>uf_is_debhab($ls_monto, $ls_debhab),'situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_tottransfer = $ld_tottransfer + $ls_monto;
				}
				else {
					$ld_tottransfer = $ld_tottransfer - $ls_monto;
				}
				break;
			
			case '313'://situado
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totsituado = $ld_totsituado + $ls_monto;
				}
				else{
					$ld_totsituado = $ld_totsituado - $ls_monto;
				}
				break;
			
			case '314'://situado
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totsituado = $ld_totsituado + $ls_monto;
				}
				else{
					$ld_totsituado = $ld_totsituado - $ls_monto;
				}
				break;
				
			case '323'://situado
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totsituado = $ld_totsituado + $ls_monto;
				}
				else{
					$ld_totsituado = $ld_totsituado - $ls_monto;
				}
				break;
				
			case '324'://situado
				$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_acumulado'=>'','resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
				if($ls_debhab=='H'){
					$ld_totsituado = $ld_totsituado + $ls_monto;
				}
				else{
					$ld_totsituado = $ld_totsituado - $ls_monto;
				}
				break;
				
			case '315'://resultado
				if(substr($ls_cuenta, 0, 5) == '31501'){//resultado acumulados
					$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
					if($ls_debhab=='H'){
						$ld_totresacum = $ld_totresacum + $ls_monto;
					}
					else{
						$ld_totresacum = $ld_totresacum - $ls_monto;
					}
				}
				else if(substr($ls_cuenta, 0, 5) == '31502'){//resultado ejercicio
					$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>uf_is_debhab($ls_monto, $ls_debhab),'total'=>uf_is_debhab($ls_monto, $ls_debhab));
					if($ls_debhab=='H'){
						$ld_totresejer = $ld_totresejer + $ls_monto;
					}
					else{
						$ld_totresejer = $ld_totresejer - $ls_monto;
					}
				}
				break;
			
			case '325'://resultado
				if(substr($ls_cuenta, 0, 5) == '32501'){//resultado acumulados
					$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>uf_is_debhab($ls_monto, $ls_debhab),'resultado_ejercicio'=>'','total'=>uf_is_debhab($ls_monto, $ls_debhab));
					if($ls_debhab=='H'){
						$ld_totresacum = $ld_totresacum + $ls_monto;
					}
					else{
						$ld_totresacum = $ld_totresacum - $ls_monto;
					}
				}
				else if(substr($ls_cuenta, 0, 5) == '32502'){//resultado ejercicio
					$la_data[] = array('saldos'=>$ls_cuenta,'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>uf_is_debhab($ls_monto, $ls_debhab),'total'=>uf_is_debhab($ls_monto, $ls_debhab));
					if($ls_debhab=='H'){
						$ld_totresejer = $ld_totresejer + $ls_monto;
					}
					else{
						$ld_totresejer = $ld_totresejer - $ls_monto;
					}
				}
				break;
		}
		
		
		$rs_data->MoveNext();
	}
	
	//Resultado calculado Cta Resultado Empresa
	if ($_SESSION["la_empresa"]["estciescg"] == '0')
	{
		$ld_saldo_i = 0;
		$arrResultado = $io_balance->uf_scg_reporte_select_saldo_ingreso_BG($ldt_fechas,$_SESSION["la_empresa"]["ingreso"],$ld_saldo_i);
		$ld_saldo_i = $arrResultado['ad_saldo'];
		$lb_valido = $arrResultado['lb_valido'];
		$ld_saldo_g = 0;
		$arrResultado = $io_balance->uf_scg_reporte_select_saldo_gasto_BG($ldt_fechas,$_SESSION["la_empresa"]["gasto"],$ld_saldo_g);
		$ld_saldo_g = $arrResultado['ad_saldo'];
		$lb_valido = $arrResultado['lb_valido'];
		$ls_rescal = $ld_saldo_i - $ld_saldo_g;
		$ld_totresejer = $ld_totresejer + $ls_rescal;
		$ld_tottotal = $ld_tottotal + $ls_rescal;
		$la_data[] = array('saldos'=>$_SESSION["la_empresa"]["c_resultad"],'nota'=>'    ','capital_fiscal'=>'','transferencia'=>'','situado_aportes'=>'','resultado_acumulado'=>'','resultado_ejercicio'=>uf_is_negative($ls_rescal),'total'=>uf_is_negative($ls_rescal));
	}

	uf_print_encabezado($io_pdf);
	$io_pdf->ezSetDy(-2);
	uf_print_saldos($la_data, $ls_fecsalini, $ld_capfiscal, $ld_transferencia, $ld_situado, $ld_resacumlado, $ld_resactual, $ld_total, $io_pdf);
	uf_print_totales($ls_last_day, $ld_totcapfiscal, $ld_tottransfer, $ld_totsituado, $ld_totresacum, $ld_totresejer, $ld_tottotal, $io_pdf);
	
	
	uf_print_firmas($io_pdf);
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
	
	 
	unset($io_report);
    unset($io_funciones);		
?> 