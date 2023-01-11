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
	
	function uf_is_negative($ad_monto) {
		if ($ad_monto<0) {
			return '('.number_format(abs($ad_monto),2,",",".").')';
		}
		else{
			return number_format($ad_monto,2,",",".");
		}
	}
	

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "balance_general.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
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
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
	$ls_cmbmes=$_GET["cmbmes"];
	$ls_cmbagno=$_GET["cmbagno"];
	$ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	$fechas=$ls_last_day;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
  	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
	$ldt_periodo = $_SESSION["la_empresa"]["periodo"];
	$li_ano      = substr($ldt_periodo,0,4);
	$ls_titulo   = $_SESSION["la_empresa"]["nombre"];
	$ls_titulo1  = "ESTADO DE MOVIMIENTO DE LAS CUENTAS DE PATRIMONIO";
	$ls_titulo2  = " AL ".substr($ls_last_day, 0, 2)." DE ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." DE ".$li_ano;
	$ls_titulo3  = "(EN BOLÍVARES)";  
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Movimiento Patrimonio en EXCEL</b>"); // Seguridad de Reporte
	if($lb_valido){
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
	}
	
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresión de la información encontrada en caso de que exista
	if($rs_data===false){// Existe algún error 
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al emitir el reporte');"); 
		print(" close();");
		print("</script>");
	}	
	else {
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
		$lo_subtitulo= &$lo_libro->addformat();
		$lo_subtitulo->set_bold();
		$lo_subtitulo->set_font("Verdana");
		$lo_subtitulo->set_align('center');
		$lo_subtitulo->set_size('9');
		$lo_subtitulo->set_text_wrap();
		$lo_datacenter= &$lo_libro->addformat(array('num_format' => '#'));
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat();//array('num_format' => '#,##0.00')
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');

		$lo_hoja->set_column(0,0,40);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,5,20);
		$lo_hoja->set_column(6,2,40);
		$lo_hoja->set_column(6,3,50);
		$lo_hoja->set_column(6,4,40);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(6,7,20);
		
		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_titulo1,$lo_encabezado);
		$lo_hoja->write(2, 2, $ls_titulo2,$lo_encabezado);
		$lo_hoja->write(3, 2, $ls_titulo3,$lo_encabezado);
		$lo_hoja->write(6, 2, 'CAPITAL FISCAL/INSTITUCIONAL',$lo_subtitulo);
		$lo_hoja->write(6, 3, 'TRANSFERENCIAS DONACIONES Y APORTES POR CAPITALIZAR RECIBIDOS',$lo_subtitulo);
		$lo_hoja->write(6, 4, 'SITUADO Y APORTES ESPECIALES',$lo_subtitulo);
		$lo_hoja->write(6, 5, 'RESULTADOS ACUMULADOS',$lo_subtitulo);
		$lo_hoja->write(6, 6, 'RESULTADO DEL EJERCICIO',$lo_subtitulo);
		$lo_hoja->write(6, 7, 'TOTAL',$lo_subtitulo);
		$lo_hoja->write(7, 0, "SALDOS AL ".$ls_fecsalini,$lo_titulo);
		$lo_hoja->write(7, 1, "NOTA",$lo_titulo);
		$lo_hoja->write(7, 2, uf_is_negative($ld_capfiscal),$lo_subtitulo);
		$lo_hoja->write(7, 3, uf_is_negative($ld_transferencia),$lo_subtitulo);
		$lo_hoja->write(7, 4, uf_is_negative($ld_situado),$lo_subtitulo);
		$lo_hoja->write(7, 5, uf_is_negative($ld_resacumlado),$lo_subtitulo);
		$lo_hoja->write(7, 6, uf_is_negative($ld_resactual),$lo_subtitulo);
		$lo_hoja->write(7, 7, uf_is_negative($ld_total),$lo_subtitulo);
		
		$indice = 8;
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
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 2, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totcapfiscal = $ld_totcapfiscal + $ls_monto;
					}
					else{
						$ld_totcapfiscal = $ld_totcapfiscal - $ls_monto;
					}
					break;
		
				case '321'://capital fiscal
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 2, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totcapfiscal = $ld_totcapfiscal + $ls_monto;
					}
					else{
						$ld_totcapfiscal = $ld_totcapfiscal - $ls_monto;
					}
					break;
		
				case '312'://transferencia
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 3, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_tottransfer = $ld_tottransfer + $ls_monto;
					}
					else {
						$ld_tottransfer = $ld_tottransfer - $ls_monto;
					}
					break;
		
				case '322'://transferencia
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 3, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_tottransfer = $ld_tottransfer + $ls_monto;
					}
					else {
						$ld_tottransfer = $ld_tottransfer - $ls_monto;
					}
					break;
						
				case '313'://situado
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 4, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totsituado = $ld_totsituado + $ls_monto;
					}
					else{
						$ld_totsituado = $ld_totsituado - $ls_monto;
					}
					break;
						
				case '314'://situado
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 4, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totsituado = $ld_totsituado + $ls_monto;
					}
					else{
						$ld_totsituado = $ld_totsituado - $ls_monto;
					}
					break;
		
				case '323'://situado
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 4, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totsituado = $ld_totsituado + $ls_monto;
					}
					else{
						$ld_totsituado = $ld_totsituado - $ls_monto;
					}
					break;
		
				case '324'://situado
					$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
					$lo_hoja->write($indice, 4, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
					if($ls_debhab=='H'){
						$ld_totsituado = $ld_totsituado + $ls_monto;
					}
					else{
						$ld_totsituado = $ld_totsituado - $ls_monto;
					}
					break;
		
				case '315'://resultado
					if(substr($ls_cuenta, 0, 5) == '31501'){//resultado acumulados
						$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
						$lo_hoja->write($indice, 5, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						if($ls_debhab=='H'){
							$ld_totresacum = $ld_totresacum + $ls_monto;
						}
						else{
							$ld_totresacum = $ld_totresacum + $ls_monto;
						}
					}
					else if(substr($ls_cuenta, 0, 5) == '31502'){//resultado ejercicio
						$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
						$lo_hoja->write($indice, 6, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
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
						$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
						$lo_hoja->write($indice, 5, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						if($ls_debhab=='H'){
							$ld_totresacum = $ld_totresacum + $ls_monto;
						}
						else{
							$ld_totresacum = $ld_totresacum + $ls_monto;
						}
					}
					else if(substr($ls_cuenta, 0, 5) == '32502'){//resultado ejercicio
						$lo_hoja->write($indice, 0, $ls_cuenta,$lo_datacenter);
						$lo_hoja->write($indice, 6, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						$lo_hoja->write($indice, 7, uf_is_debhab($ls_monto, $ls_debhab),$lo_dataright);
						if($ls_debhab=='H'){
							$ld_totresejer = $ld_totresejer + $ls_monto;
						}
						else{
							$ld_totresejer = $ld_totresejer - $ls_monto;
						}
					}
					break;
			}
			$indice++;
		
			$rs_data->MoveNext();
		}
		
		//Resultado calculado Cta Resultado Empresa
		if ($_SESSION["la_empresa"]["estciescg"] == '0') {
			$arrResultado = $io_balance->uf_scg_reporte_select_saldo_ingreso_BG($ldt_fechas,$_SESSION["la_empresa"]["ingreso"],$ld_saldo_i);
			$ld_saldo_i = $arrResultado['ad_saldo'];
			$lb_valido = $arrResultado['lb_valido'];

			$arrResultado = $io_balance->uf_scg_reporte_select_saldo_gasto_BG($ldt_fechas,$_SESSION["la_empresa"]["gasto"],$ld_saldo_g);
			$ld_saldo_g = $arrResultado['ad_saldo'];
			$lb_valido = $arrResultado['lb_valido'];

			$ls_rescal = $ld_saldo_i - $ld_saldo_g;
			$ld_totresejer = $ld_totresejer + $ls_rescal;
			$ld_tottotal = $ld_tottotal + $ls_rescal;
			$lo_hoja->write($indice, 0, $_SESSION["la_empresa"]["c_resultad"],$lo_datacenter);
			$lo_hoja->write($indice, 6, uf_is_debhab($ls_rescal, $ls_debhab),$lo_dataright);
			$lo_hoja->write($indice, 7, uf_is_debhab($ls_rescal, $ls_debhab),$lo_dataright);
			$indice++;
		}
		
		$lo_hoja->write($indice, 0, "SALDOS AL ".$ls_last_day,$lo_titulo);
		$lo_hoja->write($indice, 2, uf_is_negative($ld_totcapfiscal),$lo_subtitulo);
		$lo_hoja->write($indice, 3, uf_is_negative($ld_tottransfer),$lo_subtitulo);
		$lo_hoja->write($indice, 4, uf_is_negative($ld_totsituado),$lo_subtitulo);
		$lo_hoja->write($indice, 5, uf_is_negative($ld_totresacum),$lo_subtitulo);
		$lo_hoja->write($indice, 6, uf_is_negative($ld_totresejer),$lo_subtitulo);
		$lo_hoja->write($indice, 7, uf_is_negative($ld_tottotal),$lo_subtitulo);

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"movimiento_patrimonio.xls\"");
		header("Content-Disposition: inline; filename=\"movimiento_patrimonio.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
	
	
?> 