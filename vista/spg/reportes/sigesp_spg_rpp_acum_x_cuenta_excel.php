<?php
session_start();   
// para crear el libro excel
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
$lo_archivo =  tempnam("/tmp", "spg_acumulado_x_cuentas.xls");
$lo_libro = new writeexcel_workbookbig($lo_archivo);
$lo_hoja = &$lo_libro->addworksheet();
//---------------------------------------------------------------------------------------------------------------------------
	
//------------------------------------------------------------------------------------------------------------------------------
require_once("sigesp_spg_funciones_reportes.php");
require_once("sigesp_spg_reporte.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
$io_function_report = new sigesp_spg_funciones_reportes();
$io_report          = new sigesp_spg_reporte();
$io_funciones       = new class_funciones();			
$io_fecha           = new class_fecha();
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_acumulado_cuenta.php");
$io_report = new ServicioAcumuladoCuenta();
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_utilidadreporte.php");
$io_utilidad = new ServicioUtilidadReporte();

//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
$li_ano             = substr($ldt_periodo,0,4);
$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
$ls_codestpro1      = $_GET["codestpro1"];
$ls_codestpro2      = $_GET["codestpro2"];
$ls_codestpro3      = $_GET["codestpro3"];
$ls_codestpro1h     = $_GET["codestpro1h"];
$ls_codestpro2h     = $_GET["codestpro2h"];
$ls_codestpro3h     = $_GET["codestpro3h"];
$ls_estclades       = $_GET["estclades"];
$ls_estclahas       = $_GET["estclahas"];
$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
$ls_cmbmesdes       = $_GET["cmbmesdes"];
$ls_cmbmeshas       = $_GET["cmbmeshas"];
$cmbnivel           = $_GET["cmbnivel"];
$ls_cuentades       = $_GET["txtcuentades"];
$ls_cuentahas       = $_GET["txtcuentahas"];
$ls_codfuefindes    = $_GET["txtcodfuefindes"];
$ls_codfuefinhas    = $_GET["txtcodfuefinhas"];
//-----------------------------------------------------------------------------------------------------------------------------
$ls_programatica_desde = '';
$ls_programatica_hasta = '';
if($li_estmodest==1) {
	$ls_codestpro4  = "0000000000000000000000000";
	$ls_codestpro5  = "0000000000000000000000000";
	$ls_codestpro4h = "0000000000000000000000000";
	$ls_codestpro5h = "0000000000000000000000000";
	
}
elseif($li_estmodest==2) {
	$ls_codestpro4  = $_GET["codestpro4"];
	$ls_codestpro5  = $_GET["codestpro5"];
	$ls_codestpro4h = $_GET["codestpro4h"];
	$ls_codestpro5h = $_GET["codestpro5h"];
	
}
$ldt_fecini = $li_ano."-".$ls_cmbmesdes."-01";
$ldt_fecini_rep = "01/".$ls_cmbmesdes."/".$li_ano;
$ls_mes=$ls_cmbmeshas;
$ls_ano=$li_ano;
$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
if($cmbnivel=="s1") {
	$ls_cmbnivel="1";
}
else {
	$ls_cmbnivel=$cmbnivel;
}
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
$ls_titulo="ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA ".$fecfin." ";  
	if (($ls_codestpro1 === "0000000000000000000000000")  && ($ls_codestpro1h === "0000000000000000000000000"))
	{
		$ls_estclades = $io_utilidad->obtenerCodigoMaxMin('MIN', 0, true);
		$la_codestproD["codestpro1"] = $io_utilidad->obtenerCodigoMaxMin('MIN', 1, false, $ls_estclades);
		$la_codestproD["codestpro2"] = $io_utilidad->obtenerCodigoMaxMin('MIN', 2, false, $ls_estclades, $la_codestproD["codestpro1"]);
		$la_codestproD["codestpro3"] = $io_utilidad->obtenerCodigoMaxMin('MIN', 3, false, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"]);
		$la_codestproD["codestpro4"] = $io_utilidad->obtenerCodigoMaxMin('MIN', 4, false, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"], $la_codestproD["codestpro3"]);
		$la_codestproD["codestpro5"] = $io_utilidad->obtenerCodigoMaxMin('MIN', 5, false, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"], $la_codestproD["codestpro3"], $la_codestproD["codestpro4"]);
		
		$la_denestproD["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproD["codestpro1"], 1, $ls_estclades);
		$la_denestproD["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproD["codestpro2"], 2, $ls_estclades, $la_codestproD["codestpro1"]);
		$la_denestproD["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproD["codestpro3"], 3, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"]);
		$la_denestproD["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproD["codestpro4"], 4, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"], $la_codestproD["codestpro3"]);
		$la_denestproD["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproD["codestpro5"], 5, $ls_estclades, $la_codestproD["codestpro1"], $la_codestproD["codestpro2"], $la_codestproD["codestpro3"], $la_codestproD["codestpro4"]);
		
		$ls_estclahas = $io_utilidad->obtenerCodigoMaxMin('MAX', 0, true);
		$la_codestproH["codestpro1"] = $io_utilidad->obtenerCodigoMaxMin('MAX', 1, false, $ls_estclahas);
		$la_codestproH["codestpro2"] = $io_utilidad->obtenerCodigoMaxMin('MAX', 2, false, $ls_estclahas, $la_codestproH["codestpro1"]);
		$la_codestproH["codestpro3"] = $io_utilidad->obtenerCodigoMaxMin('MAX', 3, false, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"]);
		$la_codestproH["codestpro4"] = $io_utilidad->obtenerCodigoMaxMin('MAX', 4, false, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"], $la_codestproH["codestpro3"]);
		$la_codestproH["codestpro5"] = $io_utilidad->obtenerCodigoMaxMin('MAX', 5, false, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"], $la_codestproH["codestpro3"], $la_codestproH["codestpro4"]);
		
		$la_denestproH["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproH["codestpro1"], 1, $ls_estclahas);
		$la_denestproH["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproH["codestpro2"], 2, $ls_estclahas, $la_codestproH["codestpro1"]);
		$la_denestproH["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproH["codestpro3"], 3, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"]);
		$la_denestproH["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproH["codestpro4"], 4, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"], $la_codestproH["codestpro3"]);
		$la_denestproH["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($la_codestproH["codestpro5"], 5, $ls_estclahas, $la_codestproH["codestpro1"], $la_codestproH["codestpro2"], $la_codestproH["codestpro3"], $la_codestproH["codestpro4"]);
	}
	else
	{
		$la_codestproD["codestpro1"] = $ls_codestpro1;
		$la_codestproD["codestpro2"] = $ls_codestpro2;
		$la_codestproD["codestpro3"] = $ls_codestpro3;
		$la_codestproD["codestpro4"] = $ls_codestpro4;
		$la_codestproD["codestpro5"] = $ls_codestpro5;
		$la_denestproD["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro1, 1, $ls_estclades);
		$la_denestproD["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro2, 2, $ls_estclades, $ls_codestpro1);
		$la_denestproD["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro3, 3, $ls_estclades, $ls_codestpro1, $ls_codestpro2);
		$la_denestproD["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro4, 4, $ls_estclades, $ls_codestpro1, $ls_codestpro2, $ls_codestpro3);
		$la_denestproD["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro5, 5, $ls_estclades, $ls_codestpro1, $ls_codestpro2, $ls_codestpro3, $ls_codestpro4);
				
		$la_codestproH["codestpro1"] = $ls_codestpro1h;
		$la_codestproH["codestpro2"] = $ls_codestpro2h;
		$la_codestproH["codestpro3"] = $ls_codestpro3h;
		$la_codestproH["codestpro4"] = $ls_codestpro4h;
		$la_codestproH["codestpro5"] = $ls_codestpro5h;
		$la_denestproH["denestpro1"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro1h, 1, $ls_estclahas);
		$la_denestproH["denestpro2"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro2h, 2, $ls_estclahas, $ls_codestpro1h);
		$la_denestproH["denestpro3"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro3h, 3, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h);
		$la_denestproH["denestpro4"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro4h, 4, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h, $ls_codestpro3h);
		$la_denestproH["denestpro5"] = $io_utilidad->obtenerDenominacionEstructura($ls_codestpro5h, 5, $ls_estclahas, $ls_codestpro1h, $ls_codestpro2h, $ls_codestpro3h, $ls_codestpro4h);
	}
//------------------------------------------------------------------------------------------------------------------------------
$ls_codestproD = '';
if ($ls_codestpro1 != "0000000000000000000000000") {
	$ls_codestproD = str_pad($ls_codestpro1,25,0,0).str_pad($ls_codestpro2,25,0,0).str_pad($ls_codestpro3,25,0,0).str_pad($ls_codestpro4,25,0,0).str_pad($ls_codestpro5,25,0,0).$ls_estclades;
}    

$ls_codestproH = '';
if ($ls_codestpro1h != "0000000000000000000000000") {
	$ls_codestproH = str_pad($ls_codestpro1h,25,0,0).str_pad($ls_codestpro2h,25,0,0).str_pad($ls_codestpro3h,25,0,0).str_pad($ls_codestpro4h,25,0,0).str_pad($ls_codestpro5h,25,0,0).$ls_estclahas;
}

$dataReporte = $io_report->buscarCuentas($ls_codestproD, $ls_codestproH, $ls_cuentades, $ls_cuentahas, $ls_cmbnivel);
if($dataReporte === false || $dataReporte->EOF) {
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else {
	/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	$ls_desc_event="Se genero el Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
	$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_acum_x_cuentas.php",$ls_desc_event);
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
	$lo_titulo2= &$lo_libro->addformat();
	$lo_titulo2->set_bold();
	$lo_titulo2->set_font("Verdana");
	$lo_titulo2->set_align('left');
	$lo_titulo2->set_size('9');
	$lo_datacenter= &$lo_libro->addformat();
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
	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	
	$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	
	$lo_hoja->write(2, 2, 'Estructura Desde ',$lo_titulo);
	$lo_hoja->write(3, 1, trim(substr($la_codestproD["codestpro1"],-$li_loncodestpro1)).' '.$la_denestproD["denestpro1"],$lo_titulo2);
	$lo_hoja->write(4, 1, trim(substr($la_codestproD["codestpro2"],-$li_loncodestpro1)).' '.$la_denestproD["denestpro2"],$lo_titulo2);
	$lo_hoja->write(5, 1, trim(substr($la_codestproD["codestpro3"],-$li_loncodestpro1)).' '.$la_denestproD["denestpro3"],$lo_titulo2);
	if($li_estmodest==2) {
		$lo_hoja->write(6, 1, trim(substr($la_codestproD["codestpro4"],-$li_loncodestpro1)).' '.$la_denestproD["denestpro4"],$lo_titulo2);
		$lo_hoja->write(7, 1, trim(substr($la_codestproD["codestpro5"],-$li_loncodestpro1)).' '.$la_denestproD["denestpro5"],$lo_titulo2);
	}
	$lo_hoja->write(2, 7, 'Estructura Hasta ',$lo_titulo);
	$lo_hoja->write(3, 6, trim(substr($la_codestproH["codestpro1"],-$li_loncodestpro1)).' '.$la_denestproH["denestpro1"],$lo_titulo2);
	$lo_hoja->write(4, 6, trim(substr($la_codestproH["codestpro2"],-$li_loncodestpro1)).' '.$la_denestproH["denestpro2"],$lo_titulo2);
	$lo_hoja->write(5, 6, trim(substr($la_codestproH["codestpro3"],-$li_loncodestpro1)).' '.$la_denestproH["denestpro3"],$lo_titulo2);
	if($li_estmodest==2) {
		$lo_hoja->write(6, 6, trim(substr($la_codestproH["codestpro4"],-$li_loncodestpro1)).' '.$la_denestproH["denestpro4"],$lo_titulo2);
		$lo_hoja->write(7, 6, trim(substr($la_codestproH["codestpro5"],-$li_loncodestpro1)).' '.$la_denestproH["denestpro5"],$lo_titulo2);
	}
	
	
	$lb_codfuefin = true;
	if (!empty($ls_codfuefindes) && !empty($ls_codfuefinhas))
	{
		$lb_codfuefin = false;
	}
	else
	{
		if ($ls_codfuefindes=='--' && $ls_codfuefinhas=='--')
		{		
			$lb_codfuefin = false;
		}
	}
	$totalAsignado = 0;
	$totalAumento = 0;
	$totalDisminucion = 0;
	$totalMontoActualizado = 0;
	$totalPrecomprometido = 0;
	$totalComprometido = 0;
	$totalSaldoxcomprometer = 0;
	$totalCausado = 0;
	$totalPagado = 0;
	$totalPorpagar = 0;
    
	$li_row=10;
	$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
    $lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
    $lo_hoja->write($li_row, 2, "Asignado",$lo_titulo);
    $lo_hoja->write($li_row, 3, "Aumento",$lo_titulo);
    $lo_hoja->write($li_row, 4, "Disminucion",$lo_titulo);
    $lo_hoja->write($li_row, 5, "Monto Actualizado",$lo_titulo);
    $lo_hoja->write($li_row, 6, "Pre Comprometido",$lo_titulo);
    $lo_hoja->write($li_row, 7, "Comprometido",$lo_titulo);
    $lo_hoja->write($li_row, 8, "Saldo Por Comprometer",$lo_titulo);
    $lo_hoja->write($li_row, 9, "Causado",$lo_titulo);
    $lo_hoja->write($li_row, 10, "Pagado",$lo_titulo);
    $lo_hoja->write($li_row, 11, "Por Pagar",$lo_titulo);
		
	while (!$dataReporte->EOF) {
		$spg_cuenta   = $dataReporte->fields['spg_cuenta'];
		$nivel        = $dataReporte->fields['nivel'];
		$denominacion = $dataReporte->fields['denominacion'];
		
		if ($lb_codfuefin) {
			$asignado = $dataReporte->fields['asignado'];
		}
		else {
			$asignado = $io_report->buscarAsignadoFuente($ls_codestproD, $ls_codestproH, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas);
		}

		$dataSaldo = $io_report->buscarSaldoCuenta($ls_codestproD, $ls_codestproH, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin);
		$aumento          = $dataSaldo->fields['aumento'];
		$disminucion      = $dataSaldo->fields['disminucion'];
		$precompromiso    = $dataSaldo->fields['precompromiso'];
		$compromiso       = $dataSaldo->fields['compromiso'];
		$causado          = $dataSaldo->fields['causado'];
		$pagado           = $dataSaldo->fields['pagado'];
		$montoActualizado = $asignado + $aumento - $disminucion;
		$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
		$porPagar         = $causado - $pagado;
		
		if ($dataReporte->fields['status'] == 'C')
		{
			$totalAsignado = $totalAsignado + $asignado;
			$totalAumento = $totalAumento + $aumento;
			$totalDisminucion = $totalDisminucion + $disminucion;
			$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
			$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
			$totalComprometido = $totalComprometido + $compromiso;
			$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
			$totalCausado = $totalCausado + $causado;
			$totalPagado = $totalPagado + $pagado;
			$totalPorpagar = $totalPorpagar + $porPagar;
		}
		else
		{
			if ($nivel == $ls_cmbnivel)
			{
				$totalAsignado = $totalAsignado + $asignado;
				$totalAumento = $totalAumento + $aumento;
				$totalDisminucion = $totalDisminucion + $disminucion;
				$totalMontoActualizado = $totalMontoActualizado + $montoActualizado;
				$totalPrecomprometido = $totalPrecomprometido + $precompromiso;
				$totalComprometido = $totalComprometido + $compromiso;
				$totalSaldoxcomprometer = $totalSaldoxcomprometer + $saldoComprometer;
				$totalCausado = $totalCausado + $causado;
				$totalPagado = $totalPagado + $pagado;
				$totalPorpagar = $totalPorpagar + $porPagar;
			}
		}
			  
		$li_row++;
		$lo_hoja->write($li_row, 0, $spg_cuenta,$lo_datacenter);
		$lo_hoja->write($li_row, 1, $denominacion." ",$lo_dataleft);
		$lo_hoja->write($li_row, 2, number_format($asignado,2,",","."),$lo_dataright);
	 	$lo_hoja->write($li_row, 3, number_format($aumento,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 4, number_format($disminucion,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 5, number_format($montoActualizado,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 6, number_format($precompromiso,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 7, number_format($compromiso,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 8, number_format($saldoComprometer,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 9, number_format($causado,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 10, number_format($pagado,2,",","."),$lo_dataright);
		$lo_hoja->write($li_row, 11, number_format($porPagar,2,",","."),$lo_dataright);

		$dataReporte->MoveNext();
		unset($dataSaldo);
	} 
	$li_row++;
	$lo_hoja->write($li_row, 1, "Total ",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
	$lo_hoja->write($li_row, 2, number_format($totalAsignado,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 3, number_format($totalAumento,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 4, number_format($totalDisminucion,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 5, number_format($totalMontoActualizado,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 6, number_format($totalPrecomprometido,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 7, number_format($totalComprometido,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 8, number_format($totalSaldoxcomprometer,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 9, number_format($totalCausado,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 10, number_format($totalPagado,2,",","."),$lo_dataright);
	$lo_hoja->write($li_row, 11, number_format($totalPorpagar,2,",","."),$lo_dataright);
		
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"spg_acumulado_x_cuentas.xls\"");
	header("Content-Disposition: inline; filename=\"spg_acumulado_x_cuentas.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
	print("<script language=JavaScript>");
	print(" close();");
	print("</script>");
}
?> 