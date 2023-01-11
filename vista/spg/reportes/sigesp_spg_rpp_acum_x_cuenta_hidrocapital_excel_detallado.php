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
//---------------------------------------------------------------------------------------------------------------------------
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
require_once("../../../modelo/servicio/spg/sigesp_srv_spg_acumulado_cuenta.php");
$io_function_report = new sigesp_spg_funciones_reportes();
$io_report          = new sigesp_spg_reporte();
$io_funciones       = new class_funciones();			
$io_fecha           = new class_fecha();
$io_report          = new ServicioAcumuladoCuenta();

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
if($li_estmodest==1)
{
	$ls_codestpro4  = "0000000000000000000000000";
	$ls_codestpro5  = "0000000000000000000000000";
	$ls_codestpro4h = "0000000000000000000000000";
	$ls_codestpro5h = "0000000000000000000000000";
}
elseif($li_estmodest==2)
{
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
if($cmbnivel=="s1")
{
	$ls_cmbnivel="1";
}
else
{
	$ls_cmbnivel=$cmbnivel;
}
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
$ls_titulo="ACUMULADO POR CUENTAS DESDE FECHA  ".$ldt_fecini_rep."  HASTA ".$fecfin." ";  
//------------------------------------------------------------------------------------------------------------------------------
$ls_codestproD = '';
$ls_filtroD = '';
if ($ls_codestpro1 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro1,25,0,0);
	$ls_filtroD .= 'PCT.codestpro1,';
}
if ($ls_codestpro2 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro2,25,0,0);
	$ls_filtroD .= 'PCT.codestpro2,';
}
if ($ls_codestpro3 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro3,25,0,0);
	$ls_filtroD .= 'PCT.codestpro3,';
}
if ($ls_codestpro4 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro4,25,0,0);
	$ls_filtroD .= 'PCT.codestpro4,';
}
if ($ls_codestpro5 != "0000000000000000000000000")
{
	$ls_codestproD .= str_pad($ls_codestpro5,25,0,0);
	$ls_filtroD .= 'PCT.codestpro5,';
}
if ($ls_estclades != '')
{
	$ls_codestproD .= $ls_estclades;
	$ls_filtroD .= 'PCT.estcla,';
}
$ls_filtroD = substr($ls_filtroD,0,strlen($ls_filtroD)-1);

$ls_codestproH = '';
$ls_filtroH ='';
if ($ls_codestpro1h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro1h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro1,';	
}
if ($ls_codestpro2h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro2h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro2,';	
}
if ($ls_codestpro3h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro3h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro3,';	
}
if ($ls_codestpro4h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro4h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro4,';	
}
if ($ls_codestpro5h != "0000000000000000000000000")
{
	$ls_codestproH .= str_pad($ls_codestpro5h,25,0,0);
	$ls_filtroH .= 'PCT.codestpro5,';	
}
$ls_codestproH .= $ls_estclahas;
if ($ls_estclahas != '')
{
	$ls_codestproH .= $ls_estclahas;
	$ls_filtroH .= 'PCT.estcla,';
}
$ls_filtroH = substr($ls_filtroH,0,strlen($ls_filtroH)-1);
$dataEstructura = $io_report->buscarEstructuraCuenta($ls_codestproD, $ls_codestproH, $ls_cuentades, $ls_cuentahas, $ls_cmbnivel, $ls_filtroD, $ls_filtroH);	
if($dataEstructura === false || $dataEstructura->EOF)
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else
{
	/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	$ls_desc_event="Se genero el Reporte Acumulado por Cuentas desde la fecha ".$ldt_fecini_rep." hasta ".$fecfin." ";
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
	$lo_titulo_left= &$lo_libro->addformat();
	$lo_titulo_left->set_bold();
	$lo_titulo_left->set_font("Verdana");
	$lo_titulo_left->set_align('left');
	$lo_titulo_left->set_size('9');
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
	
	$lo_datacenter_bold= &$lo_libro->addformat();
	$lo_datacenter_bold->set_font("Verdana");
	$lo_datacenter_bold->set_align('center');
	$lo_datacenter_bold->set_size('9');
	$lo_datacenter_bold->set_bold();
	$lo_dataright_bold= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
	$lo_dataright_bold->set_font("Verdana");
	$lo_dataright_bold->set_align('right');
	$lo_dataright_bold->set_size('9');
	$lo_dataright_bold->set_bold();
	$lo_dataleft_bold= &$lo_libro->addformat();
	$lo_dataleft_bold->set_text_wrap();
	$lo_dataleft_bold->set_font("Verdana");
	$lo_dataleft_bold->set_align('left');
	$lo_dataleft_bold->set_size('9');
	$lo_dataleft_bold->set_bold();
	
	//IMPRIMIENDO EL TITUTLO
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,40);
	$lo_hoja->set_column(2,2,25);
	$lo_hoja->set_column(3,3,25);
	$lo_hoja->set_column(4,4,25);
	$lo_hoja->set_column(5,11,25);
	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	//$lo_hoja->write(1, 3, $ls_titulo1,$lo_encabezado);
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
	$x = 1;
	$li_row = 3;
	$estructurAnt = '';
	$totalAsignadoGen          = 0;
	$totalAumentoGen           = 0;
	$totalDisminucionGen       = 0;
	$totalMontoActualizadoGen  = 0;
	$totalPrecomprometidoGen   = 0;
	$totalComprometidoGen      = 0;
	$totalSaldoxcomprometerGen = 0;
	$totalCausadoGen           = 0;
	$totalPagadoGen            = 0;
	$totalPorpagarGen          = 0;
	while (!$dataEstructura->EOF)
	{
		$codestpro1   = $dataEstructura->fields['codestpro1'];
		$codestpro2   = $dataEstructura->fields['codestpro2'];
		$codestpro3   = $dataEstructura->fields['codestpro3'];
		$codestpro4   = $dataEstructura->fields['codestpro4'];
		$codestpro5   = $dataEstructura->fields['codestpro5'];
		$estcla       = $dataEstructura->fields['estcla'];
		$denestpro1   = $dataEstructura->fields['denestpro1'];
		$denestpro2   = $dataEstructura->fields['denestpro2'];
		$denestpro3   = $dataEstructura->fields['denestpro3'];
		$denestpro4   = $dataEstructura->fields['denestpro4'];
		$denestpro5   = $dataEstructura->fields['denestpro5'];
		$spg_cuenta   = $dataEstructura->fields['spg_cuenta'];
		$nivel        = $dataEstructura->fields['nivel'];
		$denominacion = $dataEstructura->fields['denominacion'];
		$estructura = $codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5.$estcla;
	
		if (empty($estructurAnt))
		{
			$estructurAnt = $estructura;
			$codestpro1Ant = $codestpro1;
			$codestpro2Ant = $codestpro2;
			$codestpro3Ant = $codestpro3;
			$codestpro4Ant = $codestpro4;
			$codestpro5Ant = $codestpro5;
			$denestpro1Ant = $denestpro1;
			$denestpro2Ant = $denestpro2;
			$denestpro3Ant = $denestpro3;
			$denestpro4Ant = $denestpro4;
			$denestpro5Ant = $denestpro5;
		}
	
		if ($estructurAnt == $estructura)
		{
			if ($lb_codfuefin)
			{
				$asignado = $dataEstructura->fields['asignado'];
			}
			else
			{
				$asignado = $io_report->buscarAsignadoFuenteEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas);
			}
			
			$dataSaldo        = $io_report->buscarSaldoCuentaEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin);
			$aumento          = $dataSaldo->fields['aumento'];
			$disminucion      = $dataSaldo->fields['disminucion'];
			$precompromiso    = $dataSaldo->fields['precompromiso'];
			$compromiso       = $dataSaldo->fields['compromiso'];
			$causado          = $dataSaldo->fields['causado'];
			$pagado           = $dataSaldo->fields['pagado'];
			$montoActualizado = $asignado + $aumento - $disminucion;
			$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
			$porPagar         = $causado - $pagado;
	
			if ($dataEstructura->fields['status'] == 'C')
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
			else {
				if ($nivel == '1') {
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
			
			$la_data[]=array('cuenta'=>$spg_cuenta, 'denominacion'=>$denominacion, 'asignado'=>$asignado, 'aumento'=>$aumento, 
					         'disminución'=>$disminucion, 'montoactualizado'=>$montoActualizado, 'precomprometido'=>$precompromiso,
							 'comprometido'=>$compromiso, 'saldoxcomprometer'=>$saldoComprometer, 'causado'=>$causado, 'pagado'=>$pagado,
							 'porpagar'=>$porPagar);
		}
		else {
			$ls_estructura = "";
			if($li_estmodest == 1) {
				$ls_estructura = trim(substr($codestpro1Ant,-$ls_loncodestpro1))."-".trim(substr($codestpro2Ant,-$ls_loncodestpro2))."-".trim(substr($codestpro3Ant,-$ls_loncodestpro3));
			}
			elseif($li_estmodest == 2) {
				$ls_estructura = trim(substr($codestpro1Ant,-$ls_loncodestpro1))."-".trim(substr($codestpro2Ant,-$ls_loncodestpro2))."-".trim(substr($codestpro3Ant,-$ls_loncodestpro3))."-".trim(substr($codestpro4Ant,-$ls_loncodestpro4))."-".trim(substr($codestpro5Ant,-$ls_loncodestpro5));
			}
			
			$totalAsignadoGen         = $totalAsignadoGen + $totalAsignado;
			$totalAumentoGen          = $totalAumentoGen + $totalAumento;
			$totalDisminucionGen      = $totalDisminucionGen + $totalDisminucion;
			$totalMontoActualizadoGen = $totalMontoActualizadoGen + $totalMontoActualizado;
			$totalPrecomprometidoGen  = $totalPrecomprometidoGen + $totalPrecomprometido;
			$totalComprometidoGen     = $totalComprometidoGen + $totalComprometido;
			$totalSaldoxcomprometerGen = $totalSaldoxcomprometerGen + $totalSaldoxcomprometer;
			$totalCausadoGen           = $totalCausadoGen + $totalCausado;
			$totalPagadoGen            = $totalPagadoGen + $totalPagado;
			$totalPorpagarGen          = $totalPorpagarGen + $totalPorpagar;
			
			if ($_SESSION["la_empresa"]["estmodest"] == 1) {
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro1Ant,-$ls_loncodestpro1)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro1Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro2Ant,-$ls_loncodestpro2)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro2Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro3Ant,-$ls_loncodestpro3)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro3Ant,$lo_dataleft);
			}
			else {
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro1Ant,-$ls_loncodestpro1)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro1Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro2Ant,-$ls_loncodestpro2)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro2Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro3Ant,-$ls_loncodestpro3)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro3Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro4"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro4Ant,-$ls_loncodestpro4)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro4Ant,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro5"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro5Ant,-$ls_loncodestpro5)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro5Ant,$lo_dataleft);
			}
			$li_row++;
			$li_row++;
			$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
			$lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
			$lo_hoja->write($li_row, 2, "Asignado",$lo_titulo);
			$lo_hoja->write($li_row, 3, "Aumento",$lo_titulo);
			$lo_hoja->write($li_row, 4, "Disminucion",$lo_titulo);
			$lo_hoja->write($li_row, 5, "Monto Actualizado",$lo_titulo);
			$lo_hoja->write($li_row, 6, "Pre-Comprometido",$lo_titulo);
			$lo_hoja->write($li_row, 7, "Comprometido",$lo_titulo);
			$lo_hoja->write($li_row, 8, "Saldo Por Comprometer",$lo_titulo);
			$lo_hoja->write($li_row, 9, "Causado",$lo_titulo);
			$lo_hoja->write($li_row, 10,"Pagado",$lo_titulo);
			$lo_hoja->write($li_row, 11,"Por Pagar",$lo_titulo);
			
			foreach ($la_data as $cuenta) {
				$li_row++;
				$lo_hoja->write($li_row, 0, $cuenta['cuenta'],$lo_datacenter);
				$lo_hoja->write($li_row, 1, $cuenta['denominacion'],$lo_dataleft);
				$lo_hoja->write($li_row, 2, $cuenta['asignado'],$lo_dataright);
				$lo_hoja->write($li_row, 3, $cuenta['aumento'],$lo_dataright);
				$lo_hoja->write($li_row, 4, $cuenta['disminucion'],$lo_dataright);
				$lo_hoja->write($li_row, 5, $cuenta['montoactualizado'],$lo_dataright);
				$lo_hoja->write($li_row, 6, $cuenta['precomprometido'],$lo_dataright);
				$lo_hoja->write($li_row, 7, $cuenta['comprometido'],$lo_dataright);
				$lo_hoja->write($li_row, 8, $cuenta['saldoxcomprometer'],$lo_dataright);
				$lo_hoja->write($li_row, 9, $cuenta['causado'],$lo_dataright);
				$lo_hoja->write($li_row, 10,$cuenta['pagado'],$lo_dataright);
				$lo_hoja->write($li_row, 11,$cuenta['porpagar'],$lo_dataright);
			}
			unset($la_data);
			$li_row++;
			$lo_hoja->write($li_row, 0, "TOTAL ".$ls_estructura,$lo_datacenter_bold);
			$lo_hoja->write($li_row, 2, $totalAsignado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 3, $totalAumento,$lo_dataright_bold);
			$lo_hoja->write($li_row, 4, $totalDisminucion,$lo_dataright_bold);
			$lo_hoja->write($li_row, 5, $totalMontoActualizado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 6, $totalPrecomprometido,$lo_dataright_bold);
			$lo_hoja->write($li_row, 7, $totalComprometido,$lo_dataright_bold);
			$lo_hoja->write($li_row, 8, $totalSaldoxcomprometer,$lo_dataright_bold);
			$lo_hoja->write($li_row, 9, $totalCausado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 10,$totalPagado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 11,$totalPorpagar,$lo_dataright_bold);
			$li_row++;
			$li_row++;
			$totalAsignado          = 0;
			$totalAumento           = 0;
			$totalDisminucion       = 0;
			$totalMontoActualizado  = 0;
			$totalPrecomprometido   = 0;
			$totalComprometido      = 0;
			$totalSaldoxcomprometer = 0;
			$totalCausado           = 0;
			$totalPagado            = 0;
			$totalPorpagar          = 0;
			
			//reiniciar verificacion
			$estructurAnt = $estructura;
			$codestpro1Ant = $codestpro1;
			$codestpro2Ant = $codestpro2;
			$codestpro3Ant = $codestpro3;
			$codestpro4Ant = $codestpro4;
			$codestpro5Ant = $codestpro5;
			$denestpro1Ant = $denestpro1;
			$denestpro2Ant = $denestpro2;
			$denestpro3Ant = $denestpro3;
			$denestpro4Ant = $denestpro4;
			$denestpro5Ant = $denestpro5;
			if ($lb_codfuefin) {
				$asignado = $dataEstructura->fields['asignado'];
			}
			else {
				$asignado = $io_report->buscarAsignadoFuenteEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas);
			}
				
			$dataSaldo = $io_report->buscarSaldoCuentaEst($estructurAnt, $spg_cuenta, $nivel, $ls_codfuefindes, $ls_codfuefinhas, $ldt_fecfin);
			$aumento          = $dataSaldo->fields['aumento'];
			$disminucion      = $dataSaldo->fields['disminucion'];
			$precompromiso    = $dataSaldo->fields['precompromiso'];
			$compromiso       = $dataSaldo->fields['compromiso'];
			$causado          = $dataSaldo->fields['causado'];
			$pagado           = $dataSaldo->fields['pagado'];
			$montoActualizado = $asignado + $aumento - $disminucion;
			$saldoComprometer = $asignado + $aumento - $disminucion - $precompromiso -$compromiso;
			$porPagar         = $causado - $pagado;
				
			if ($dataEstructura->fields['status'] == 'C') {
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
			else {
				if ($ls_cmbnivel == '1') {
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
			$la_data[]=array('cuenta'=>$spg_cuenta, 'denominacion'=>$denominacion, 'asignado'=>$asignado, 'aumento'=>$aumento,
					'disminución'=>$disminucion, 'montoactualizado'=>$montoActualizado, 'precomprometido'=>$precompromiso,
					'comprometido'=>$compromiso, 'saldoxcomprometer'=>$saldoComprometer, 'causado'=>$causado, 'pagado'=>$pagado,
					'porpagar'=>$porPagar);
		}
		
		if ($dataEstructura->_numOfRows == $x) {
			//Imprimir Cabecera y detalle ultimo registro
			$totalAsignadoGen         = $totalAsignadoGen + $totalAsignado;
			$totalAumentoGen          = $totalAumentoGen + $totalAumento;
			$totalDisminucionGen      = $totalDisminucionGen + $totalDisminucion;
			$totalMontoActualizadoGen = $totalMontoActualizadoGen + $totalMontoActualizado;
			$totalPrecomprometidoGen  = $totalPrecomprometidoGen + $totalPrecomprometido;
			$totalComprometidoGen     = $totalComprometidoGen + $totalComprometido;
			$totalSaldoxcomprometerGen = $totalSaldoxcomprometerGen + $totalSaldoxcomprometer;
			$totalCausadoGen           = $totalCausadoGen + $totalCausado;
			$totalPagadoGen            = $totalPagadoGen + $totalPagado;
			$totalPorpagarGen          = $totalPorpagarGen + $totalPorpagar;
			if ($_SESSION["la_empresa"]["estmodest"] == 1) {
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro1,-$ls_loncodestpro1)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro1,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro2,-$ls_loncodestpro2)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro2,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro3,-$ls_loncodestpro3)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro3,$lo_dataleft);
			}
			else {
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro1"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro1,-$ls_loncodestpro1)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro1,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro2"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro2,-$ls_loncodestpro2)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro2,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro3"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro3,-$ls_loncodestpro3)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro3,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro4"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro4,-$ls_loncodestpro4)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro4,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row,0,trim($_SESSION["la_empresa"]["nomestpro5"]),$lo_titulo_left);
				$lo_hoja->write($li_row,1,substr($codestpro5,-$ls_loncodestpro5)." ",$lo_datacenter);
				$lo_hoja->write($li_row,2,$denestpro5,$lo_dataleft);
			}
			$li_row++;
			$li_row++;
			$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
			$lo_hoja->write($li_row, 1, "Denominacion",$lo_titulo);
			$lo_hoja->write($li_row, 2, "Asignado",$lo_titulo);
			$lo_hoja->write($li_row, 3, "Aumento",$lo_titulo);
			$lo_hoja->write($li_row, 4, "Disminucion",$lo_titulo);
			$lo_hoja->write($li_row, 5, "Monto Actualizado",$lo_titulo);
			$lo_hoja->write($li_row, 6, "Pre-Comprometido",$lo_titulo);
			$lo_hoja->write($li_row, 7, "Comprometido",$lo_titulo);
			$lo_hoja->write($li_row, 8, "Saldo Por Comprometer",$lo_titulo);
			$lo_hoja->write($li_row, 9, "Causado",$lo_titulo);
			$lo_hoja->write($li_row, 10,"Pagado",$lo_titulo);
			$lo_hoja->write($li_row, 11,"Por Pagar",$lo_titulo);
			foreach ($la_data as $cuenta) {
				$li_row++;
				$lo_hoja->write($li_row, 0, $cuenta['cuenta'],$lo_datacenter);
				$lo_hoja->write($li_row, 1, $cuenta['denominacion'],$lo_dataleft);
				$lo_hoja->write($li_row, 2, $cuenta['asignado'],$lo_dataright);
				$lo_hoja->write($li_row, 3, $cuenta['aumento'],$lo_dataright);
				$lo_hoja->write($li_row, 4, $cuenta['disminucion'],$lo_dataright);
				$lo_hoja->write($li_row, 5, $cuenta['montoactualizado'],$lo_dataright);
				$lo_hoja->write($li_row, 6, $cuenta['precomprometido'],$lo_dataright);
				$lo_hoja->write($li_row, 7, $cuenta['comprometido'],$lo_dataright);
				$lo_hoja->write($li_row, 8, $cuenta['saldoxcomprometer'],$lo_dataright);
				$lo_hoja->write($li_row, 9, $cuenta['causado'],$lo_dataright);
				$lo_hoja->write($li_row, 10,$cuenta['pagado'],$lo_dataright);
				$lo_hoja->write($li_row, 11,$cuenta['porpagar'],$lo_dataright);
			}
			unset($la_data);
			$li_row++;
			$lo_hoja->write($li_row, 0, "TOTAL ".$ls_estructura,$lo_datacenter_bold);
			$lo_hoja->write($li_row, 2, $totalAsignado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 3, $totalAumento,$lo_dataright_bold);
			$lo_hoja->write($li_row, 4, $totalDisminucion,$lo_dataright_bold);
			$lo_hoja->write($li_row, 5, $totalMontoActualizado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 6, $totalPrecomprometido,$lo_dataright_bold);
			$lo_hoja->write($li_row, 7, $totalComprometido,$lo_dataright_bold);
			$lo_hoja->write($li_row, 8, $totalSaldoxcomprometer,$lo_dataright_bold);
			$lo_hoja->write($li_row, 9, $totalCausado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 10,$totalPagado,$lo_dataright_bold);
			$lo_hoja->write($li_row, 11,$totalPorpagar,$lo_dataright_bold);
			
		}	
		$x++;
		$dataEstructura->MoveNext();
		unset($dataSaldo);
	}
	$li_row++;
	$lo_hoja->write($li_row, 0, "TOTAL GENERAL",$lo_datacenter_bold);
	$lo_hoja->write($li_row, 2, $totalAsignadoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 3, $totalAumentoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 4, $totalDisminucionGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 5, $totalMontoActualizadoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 6, $totalPrecomprometidoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 7, $totalComprometidoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 8, $totalSaldoxcomprometerGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 9, $totalCausadoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 10,$totalPagadoGen,$lo_dataright_bold);
	$lo_hoja->write($li_row, 11,$totalPorpagarGen,$lo_dataright_bold);
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"spg_acumulado_x_cuentas_detallado.xls\"");
	header("Content-Disposition: inline; filename=\"spg_acumulado_x_cuentas_detallado.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
	print("<script language=JavaScript>");
	print(" close();");
	print("</script>");
}
?> 