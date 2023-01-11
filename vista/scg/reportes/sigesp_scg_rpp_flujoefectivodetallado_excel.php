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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_flujo_efectivo.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_formato($ad_monto) {
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
	$lo_archivo = tempnam("/tmp", "flujodeefectivodetallado.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("class_funciones_scg.php");
	require_once("sigesp_scg_class_flujoefectivo.php");
	$io_funciones = new class_funciones();
	$io_report    = new sigesp_scg_class_flujoefectivo();
	$io_fecha     = new class_fecha();
	$io_fun_scg   = new class_funciones_scg();
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$li_ano       = substr($_SESSION["la_empresa"]["periodo"],0,4);
	$li_anoant    = $li_ano-1;
	$ls_cmbmes    = $_GET["cmbmes"];
	$ls_last_day  = $io_fecha->uf_last_day($ls_cmbmes,$li_ano);
	$ldt_fecha    = $io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
	$ldt_perant   = $li_anoant."-12-31 00:00:00";
  	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo  = $_SESSION["la_empresa"]["nombre"];
	$ls_titulo1 = "ESTADO DE FLUJO DE EFECTIVO DETALLADO";
	$ls_titulo2 = "AL 01 de ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." de ".$li_ano;
	$ls_titulo3 = "(EN BOLÍVARES)";    
	//---------------------------------------------------------------------------------------------------------------------------
	
	$lb_valido=uf_insert_seguridad("<b>Estado de Flujo de Efectivo en PDF</b>"); // Seguridad de Reporte
	if($lb_valido){
		
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
		$lo_subtitulo->set_text_wrap();
		$lo_subtitulo->set_bold();
		$lo_subtitulo->set_font("Verdana");
		$lo_subtitulo->set_align('left');
		$lo_subtitulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
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

		$lo_hoja->set_column(0,0,50);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,5,20);
		
		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_titulo1,$lo_encabezado);
		$lo_hoja->write(2, 2, $ls_titulo2,$lo_encabezado);
		$lo_hoja->write(3, 2, $ls_titulo3,$lo_encabezado);
		$lo_hoja->write(6, 0, "",$lo_titulo);
		$lo_hoja->write(6, 1, "Nota",$lo_titulo);
		$lo_hoja->write(6, 2, $li_anoant,$lo_titulo);
		$lo_hoja->write(6, 3, $li_ano,$lo_titulo);
		$lo_hoja->write(6, 4, 'Absoluta',$lo_titulo);
		$lo_hoja->write(6, 5, 'Relativa',$lo_titulo);
		
		$x = 7;
		$lo_hoja->write($x, 0, 'Flujo de efectivo proveniente de actividades de operación',$lo_subtitulo);
		$x++;
		//Resultado del ejercicio.
		$resultado = $io_report->uf_buscar_resultado($ldt_fecha);
		$resultAnt = $io_report->uf_buscar_resultado($ldt_perant);
		$difResult = $resultado - $resultAnt;
		$varResult = $io_report->uf_calcular_variacion_relativa($resultado, $resultAnt);
		$lo_hoja->write($x, 0, 'Resultado del ejercicio',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($resultado),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($resultAnt),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difResult),$lo_dataright);
		$lo_hoja->write($x, 5, $varResult,$lo_dataright);
		$x++;
		$totEfeASecA += $resultado;
		$totEfeBSecA += $resultAnt;
		//Ajustes
		$lo_hoja->write($x, 0, 'Ajuste al resultado y el efectivo neto provisto por actividades operativas',$lo_dataleft);
		$x++;
		//Depreciacion
		$arr61401  = $io_report->uf_obtener_saldo('61401', $ldt_fecha);
		$arr22501  = $io_report->uf_obtener_saldo('22501', $ldt_fecha);
		$difDepre  = $arr22501['salAct'] - $arr61401['salAct'];
		$varDepre  = $io_report->uf_calcular_variacion_relativa($arr22501['salAct'], $arr61401['salAct']);
		$lo_hoja->write($x, 0, 'Depreciación',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr22501['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr61401['salAct']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difDepre),$lo_dataright);
		$lo_hoja->write($x, 5, $varDepre,$lo_dataright);
		$x++;
		$totEfeASecA += $arr22501['salAct'];
		$totEfeBSecA += $arr61401['salAct'];
		//Amortizacion
		$arr61402  = $io_report->uf_obtener_saldo('61402', $ldt_fecha);
		$arr22502  = $io_report->uf_obtener_saldo('22502', $ldt_fecha);
		$difAmor   = $arr22502['salAct'] - $arr61402['salAct'];
		$varAmor   = $io_report->uf_calcular_variacion_relativa($arr22502['salAct'], $arr61402['salAct']);
		$lo_hoja->write($x, 0, 'Amortización',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr22502['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr61402['salAct']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difAmor),$lo_dataright);
		$lo_hoja->write($x, 5, $varAmor,$lo_dataright);
		$x++;
		$totEfeASecA += $arr22502['salAct'];
		$totEfeBSecA += $arr61402['salAct'];
		//Provisiones
		$arr22401  = $io_report->uf_obtener_saldo('22401', $ldt_fecha);
		$difProvi  = $arr22401['salAct'] - $arr22401['salAnt'];
		$varProvi  = $io_report->uf_calcular_variacion_relativa($arr22401['salAct'], $arr22401['salAnt']);
		$lo_hoja->write($x, 0, 'Provisiones',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr22401['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr22401['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difProvi),$lo_dataright);
		$lo_hoja->write($x, 5, $varProvi,$lo_dataright);
		$x++;
		$totEfeASecA += $arr22401['salAct'];
		$totEfeBSecA += $arr22401['salAnt'];
		//Utilidad
		$arr51504  = $io_report->uf_obtener_saldo('51504', $ldt_fecha);
		$difUtili  = $arr51504['salAct'] - $arr51504['salAnt'];
		$varUtili  = $io_report->uf_calcular_variacion_relativa($arr51504['salAct'], $arr51504['salAnt']);
		$lo_hoja->write($x, 0, 'Provisiones',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr51504['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr51504['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difUtili),$lo_dataright);
		$lo_hoja->write($x, 5, $varUtili,$lo_dataright);
		$x++;
		$totEfeASecA += $arr51504['salAct'];
		$totEfeBSecA += $arr51504['salAnt'];
		//Venta de activo
		$arr51806  = $io_report->uf_obtener_saldo('51806', $ldt_fecha);
		$difVenAct = $arr51806['salAct'] - $arr51806['salAnt'];
		$varVenAct = $io_report->uf_calcular_variacion_relativa($arr51806['salAct'], $arr51806['salAnt']);
		$lo_hoja->write($x, 0, 'Provisiones',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr51806['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr51806['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difVenAct),$lo_dataright);
		$lo_hoja->write($x, 5, $varVenAct,$lo_dataright);
		$x++;
		$totEfeASecA += $arr51806['salAct'];
		$totEfeBSecA += $arr51806['salAnt'];
		//Cambios netos 
		$lo_hoja->write($x, 0, 'Cambios netos en operaciones de activos y pasivos',$lo_dataleft);
		$x++;
		//Cuentas por cobrar
		$lo_hoja->write($x, 0, 'Aumento/Disminución en cuentas por cobrar',$lo_dataleft);
		$x++;
		//Cuentas comerciales
		$arr11203  = $io_report->uf_obtener_saldo('11203', $ldt_fecha);
		$difCueCom = $arr11203['salAct'] - $arr11203['salAnt'];
		$varCueCom = $io_report->uf_calcular_variacion_relativa($arr11203['salAct'], $arr11203['salAnt']);
		$lo_hoja->write($x, 0, 'Cuentas comerciales por cobrar a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11203['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11203['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difCueCom),$lo_dataright);
		$lo_hoja->write($x, 5, $varCueCom,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11203['salAct'];
		$totEfeBSecA += $arr11203['salAnt'];
		//Anticipos contratistas
		$arr11211  = $io_report->uf_obtener_saldo('11211', $ldt_fecha);
		$difAntCon = $arr11211['salAct'] - $arr11211['salAnt'];
		$varAntCon = $io_report->uf_calcular_variacion_relativa($arr11211['salAct'], $arr11211['salAnt']);
		$lo_hoja->write($x, 0, 'Anticipos a contratistas por contratos de corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11211['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11211['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difAntCon),$lo_dataright);
		$lo_hoja->write($x, 5, $varAntCon,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11211['salAct'];
		$totEfeBSecA += $arr11211['salAnt'];
		//Inventarios
		$lo_hoja->write($x, 0, 'Aumento/Disminución en inventarios',$lo_dataleft);
		$x++;
		//Inventario de mercancia
		$arr11304  = $io_report->uf_obtener_saldo('11304', $ldt_fecha);
		$difInvMer = $arr11304['salAct'] - $arr11304['salAnt'];
		$varInvMer = $io_report->uf_calcular_variacion_relativa($arr11304['salAct'], $arr11304['salAnt']);
		$lo_hoja->write($x, 0, 'Inventario de mercancías',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11304['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11304['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difInvMer),$lo_dataright);
		$lo_hoja->write($x, 5, $varInvMer,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11304['salAct'];
		$totEfeBSecA += $arr11304['salAnt'];
		//Inventario de materiales
		$arr11305  = $io_report->uf_obtener_saldo('11305', $ldt_fecha);
		$difInvMat = $arr11305['salAct'] - $arr11305['salAnt'];
		$varInvMat = $io_report->uf_calcular_variacion_relativa($arr11305['salAct'], $arr11305['salAnt']);
		$lo_hoja->write($x, 0, 'Inventario de materiales y suministros',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11305['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11305['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difInvMat),$lo_dataright);
		$lo_hoja->write($x, 5, $varInvMat,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11305['salAct'];
		$totEfeBSecA += $arr11305['salAnt'];
		//Prepagados
		$lo_hoja->write($x, 0, 'Aumento/Disminución en prepagados',$lo_dataleft);
		$x++;
		//Anticipado a corto plazo
		$arr11401  = $io_report->uf_obtener_saldo('11401', $ldt_fecha);
		$difGasAnt = $arr11401['salAct'] - $arr11401['salAnt'];
		$varGasAnt = $io_report->uf_calcular_variacion_relativa($arr11401['salAct'], $arr11401['salAnt']);
		$lo_hoja->write($x, 0, 'Gastos pagados por anticipado a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11401['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11401['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difGasAnt),$lo_dataright);
		$lo_hoja->write($x, 5, $varGasAnt,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11401['salAct'];
		$totEfeBSecA += $arr11401['salAnt'];
		//Deposito garantia a corto plazo
		$arr11402  = $io_report->uf_obtener_saldo('11402', $ldt_fecha);
		$difDepGar = $arr11402['salAct'] - $arr11402['salAnt'];
		$varDepGar = $io_report->uf_calcular_variacion_relativa($arr11402['salAct'], $arr11402['salAnt']);
		$lo_hoja->write($x, 0, 'Depósitos otorgados en garantía a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11402['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11402['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difDepGar),$lo_dataright);
		$lo_hoja->write($x, 5, $varDepGar,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11402['salAct'];
		$totEfeBSecA += $arr11402['salAnt'];
		//Otros activos diferidos
		$arr11499  = $io_report->uf_obtener_saldo('11499', $ldt_fecha);
		$difActDif = $arr11499['salAct'] - $arr11499['salAnt'];
		$varActDif = $io_report->uf_calcular_variacion_relativa($arr11499['salAct'], $arr11499['salAnt']);
		$lo_hoja->write($x, 0, 'Otros activos diferidos a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11499['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11499['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difActDif),$lo_dataright);
		$lo_hoja->write($x, 5, $varActDif,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11499['salAct'];
		$totEfeBSecA += $arr11499['salAnt'];
		//Cargos diferidos
		$lo_hoja->write($x, 0, 'Aumento/Disminución en cargos diferidos',$lo_dataleft);
		$x++;
		$arr21301  = $io_report->uf_obtener_saldo('21301', $ldt_fecha); 
		$difCarDif = $arr21301['salAct'] - $arr21301['salAnt'];
		$varCarDif = $io_report->uf_calcular_variacion_relativa($arr21301['salAct'], $arr21301['salAnt']);
		$lo_hoja->write($x, 0, 'Pasivos diferidos a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21301['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21301['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difCarDif),$lo_dataright);
		$lo_hoja->write($x, 5, $varCarDif,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21301['salAct'];
		$totEfeBSecA += $arr21301['salAnt'];
		//Otros activos
		$lo_hoja->write($x, 0, 'Aumento/Disminución en otros activos',$lo_dataleft);
		$x++;
		$arr11909  = $io_report->uf_obtener_saldo('11909', $ldt_fecha);
		$difOtrAct = $arr11909['salAct'] - $arr11909['salAnt']; 
		$varOtrAct = $io_report->uf_calcular_variacion_relativa($arr11909['salAct'], $arr11909['salAnt']);
		$lo_hoja->write($x, 0, 'Otros activos circulantes',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11909['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11909['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difOtrAct),$lo_dataright);
		$lo_hoja->write($x, 5, $varOtrAct,$lo_dataright);
		$x++;
		$totEfeASecA += $arr11909['salAct'];
		$totEfeBSecA += $arr11909['salAnt'];
		//Cuentas por pagar
		$lo_hoja->write($x, 0, 'Aumento/Disminución en  cuentas por pagar',$lo_dataleft);
		$x++;
		$arr21103  = $io_report->uf_obtener_saldo('21103', $ldt_fecha);
		$difCuePag = $arr21103['salAct'] - $arr21103['salAnt'];  
		$varCuePag = $io_report->uf_calcular_variacion_relativa($arr21103['salAct'], $arr21103['salAnt']);
		$lo_hoja->write($x, 0, 'Cuentas por pagar a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21103['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21103['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difCuePag),$lo_dataright);
		$lo_hoja->write($x, 5, $varCuePag,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21103['salAct'];
		$totEfeBSecA += $arr21103['salAnt'];
		//Gastos acumulados
		$lo_hoja->write($x, 0, 'Aumento/Disminución en gastos acumulados',$lo_dataleft);
		$x++;
		//Personal por pagar
		$arr21101  = $io_report->uf_obtener_saldo('21101', $ldt_fecha);
		$difPerPag = $arr21101['salAct'] - $arr21101['salAnt'];  
		$varPerPag = $io_report->uf_calcular_variacion_relativa($arr21101['salAct'], $arr21101['salAnt']);
		$lo_hoja->write($x, 0, 'Gastos de personal por pagar',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21101['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21101['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difPerPag),$lo_dataright);
		$lo_hoja->write($x, 5, $varPerPag,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21101['salAct'];
		$totEfeBSecA += $arr21101['salAnt'];
		//Fondos de terceros
		$arr21499  = $io_report->uf_obtener_saldo('21499', $ldt_fecha);
		$difFonTer = $arr21499['salAct'] - $arr21499['salAnt'];  
		$varFonTer = $io_report->uf_calcular_variacion_relativa($arr21499['salAct'], $arr21499['salAnt']);
		$lo_hoja->write($x, 0, 'Otros fondos de terceros',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21499['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21499['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difFonTer),$lo_dataright);
		$lo_hoja->write($x, 5, $varFonTer,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21499['salAct'];
		$totEfeBSecA += $arr21499['salAnt'];
		//Pasivos circulantes
		$arr21909  = $io_report->uf_obtener_saldo('21909', $ldt_fecha);
		$difPasCir = $arr21909['salAct'] - $arr21909['salAnt'];  
		$varPasCir = $io_report->uf_calcular_variacion_relativa($arr21909['salAct'], $arr21909['salAnt']);
		$lo_hoja->write($x, 0, 'Otros pasivos circulantes',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21909['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21909['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difPasCir),$lo_dataright);
		$lo_hoja->write($x, 5, $varPasCir,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21909['salAct'];
		$totEfeBSecA += $arr21909['salAnt'];
		//Prestaciones sociales
		$lo_hoja->write($x, 0, 'Pago prestaciones sociales',$lo_dataleft);
		$x++;
		$arr21102  = $io_report->uf_obtener_saldo('21102', $ldt_fecha);
		$difPreSoc = $arr21102['salAct'] - $arr21102['salAnt'];  
		$varPreSoc = $io_report->uf_calcular_variacion_relativa($arr21102['salAct'], $arr21102['salAnt']);
		$lo_hoja->write($x, 0, 'Aportes patronales y retenciones laborales por pagar',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr21102['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr21102['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difPreSoc),$lo_dataright);
		$lo_hoja->write($x, 5, $varPreSoc,$lo_dataright);
		$x++;
		$totEfeASecA += $arr21102['salAct'];
		$totEfeBSecA += $arr21102['salAnt'];
		//Efectivo neto
		$difTotA = $totEfeASecA - $totEfeBSecA;
		$varTotA = $io_report->uf_calcular_variacion_relativa($totEfeASecA, $totEfeBSecA);
		$lo_hoja->write($x, 0, 'Efectivo neto provisto por actividades de operación',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($totEfeASecA),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($totEfeBSecA),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difTotA),$lo_dataright);
		$lo_hoja->write($x, 5, $varTotA,$lo_dataright);
		$x++;$x++;
		//Actividades de inversión
		$totEfeASecB = 0;
		$totEfeBSecB = 0;
		$lo_hoja->write($x, 0, 'Flujo de efectivo proveniente de actividades de inversión',$lo_subtitulo);
		$x++;
		$lo_hoja->write($x, 0, 'Compra de activo fijo',$lo_dataleft);
		$x++;
		//Bienes de uso
		$arr12301  = $io_report->uf_obtener_saldo('12301', $ldt_fecha);
		$difBieUso = $arr12301['salAct'] - $arr12301['salAnt'];  
		$varBieUso = $io_report->uf_calcular_variacion_relativa($arr12301['salAct'], $arr12301['salAnt']);
		$lo_hoja->write($x, 0, 'Bienes de uso',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr12301['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr12301['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difBieUso),$lo_dataright);
		$lo_hoja->write($x, 5, $varBieUso,$lo_dataright);
		$x++;
		$totEfeASecB += $arr12301['salAct'];
		$totEfeBSecB += $arr12301['salAnt'];
		//Tierras y terrenos
		$arr12302  = $io_report->uf_obtener_saldo('12302', $ldt_fecha);
		$difTieTer = $arr12302['salAct'] - $arr12302['salAnt'];
		$varTieTer = $io_report->uf_calcular_variacion_relativa($arr12302['salAct'], $arr12302['salAnt']);
		$lo_hoja->write($x, 0, 'Tierras y terrenos',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr12302['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr12302['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difTieTer),$lo_dataright);
		$lo_hoja->write($x, 5, $varTieTer,$lo_dataright);
		$x++;
		$totEfeASecB += $arr12302['salAct'];
		$totEfeBSecB += $arr12302['salAnt'];
		//Efectivo recibido
		$lo_hoja->write($x, 0, 'Efectivo recibido en venta de activo fijo',$lo_dataleft);
		$x++;
		$arr51806  = $io_report->uf_obtener_saldo('51806', $ldt_fecha);
		$difUtiVen = $arr51806['salAct'] - $arr51806['salAnt'];
		$varUtiVen = $io_report->uf_calcular_variacion_relativa($arr51806['salAct'], $arr51806['salAnt']);
		$lo_hoja->write($x, 0, 'Utilidad en venta de activo fijo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr51806['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr51806['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difUtiVen),$lo_dataright);
		$lo_hoja->write($x, 5, $varUtiVen,$lo_dataright);
		$x++;
		$totEfeASecB += $arr51806['salAct'];
		$totEfeBSecB += $arr51806['salAnt'];
		// Compra de acciones
		$lo_hoja->write($x, 0, 'Compra de acciones',$lo_dataleft);
		$x++;
		$arr11201  = $io_report->uf_obtener_saldo('11201', $ldt_fecha);
		$difComAcc = $arr11201['salAct'] - $arr11201['salAnt'];
		$varComAcc = $io_report->uf_calcular_variacion_relativa($arr11201['salAct'], $arr11201['salAnt']);
		$lo_hoja->write($x, 0, 'Inversiones financieras en títulos y valores a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11201['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11201['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difComAcc),$lo_dataright);
		$lo_hoja->write($x, 5, $varComAcc,$lo_dataright);
		$x++;
		$totEfeASecB += $arr11201['salAct'];
		$totEfeBSecB += $arr11201['salAnt'];
		// Venta de acciones
		$lo_hoja->write($x, 0, 'Efectivo recibido en venta de acciones',$lo_dataleft);
		$x++;
		$arr51504  = $io_report->uf_obtener_saldo('51504', $ldt_fecha);
		$difVenAcc = $arr51504['salAct'] - $arr51504['salAnt'];
		$varVenAcc = $io_report->uf_calcular_variacion_relativa($arr51504['salAct'], $arr51504['salAnt']);
		$lo_hoja->write($x, 0, 'Inversiones financieras en títulos y valores a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr51504['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr51504['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difVenAcc),$lo_dataright);
		$lo_hoja->write($x, 5, $varVenAcc,$lo_dataright);
		$x++;
		$totEfeASecB += $arr51504['salAct'];
		$totEfeBSecB += $arr51504['salAnt'];
		//Efectivo neto actividades de inversión
		$difTotB = $totEfeASecB - $totEfeBSecB;
		$varTotB = $io_report->uf_calcular_variacion_relativa($totEfeASecB, $totEfeBSecB);
		$lo_hoja->write($x, 0, 'Efectivo neto usado en actividades de inversión',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($totEfeASecB),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($totEfeBSecB),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difTotB),$lo_dataright);
		$lo_hoja->write($x, 5, $varTotB,$lo_dataright);
		$x++;$x++;
		//Actividades de financiamiento
		$totEfeASecC = 0;
		$totEfeBSecC = 0;
		$lo_hoja->write($x, 0, 'Flujo de efectivo proveniente de actividades de financiamineto',$lo_subtitulo);
		$x++;
		//Pagares bancarios recibidos
		$lo_hoja->write($x, 0, 'Pagares bancarios recibidos',$lo_dataleft);
		$x++;
		$arr2120207 = $io_report->uf_obtener_saldo('2120207', $ldt_fecha);
		$difDeuInt  = $arr2120207['salAct'] - $arr2120207['salAnt'];
		$varDeuInt  = $io_report->uf_calcular_variacion_relativa($arr2120207['salAct'], $arr2120207['salAnt']);
		$lo_hoja->write($x, 0, 'Deuda interna por préstamos recibidos de entes descentralizados financieros bancarios por pagar a corto plazo',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr2120207['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr2120207['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difDeuInt),$lo_dataright);
		$lo_hoja->write($x, 5, $varDeuInt,$lo_dataright);
		$x++;
		$totEfeASecC += $arr2120207['salAct'];
		$totEfeBSecC += $arr2120207['salAnt'];
		//Pagares bancarios pagados
		$lo_hoja->write($x, 0, 'Pagares bancarios pagados',$lo_dataleft);
		$x++;
		$arr121030206 = $io_report->uf_obtener_saldo('121030206', $ldt_fecha);
		$difPreCob    = $arr121030206['salAct'] - $arr121030206['salAnt'];
		$varPreCob    = $io_report->uf_calcular_variacion_relativa($arr121030206['salAct'], $arr121030206['salAnt']);
		$lo_hoja->write($x, 0, 'Prestamos por cobrar a largo plazo a entes financieros bancarios',$lo_dataleft);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr121030206['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr121030206['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difPreCob),$lo_dataright);
		$lo_hoja->write($x, 5, $varPreCob,$lo_dataright);
		$x++;
		$totEfeASecC += $arr121030206['salAct'];
		$totEfeBSecC += $arr121030206['salAnt'];
		//Efectivo neto actividades de financiamiento
		$difTotC = $totEfeASecC - $totEfeBSecC;
		$varTotC = $io_report->uf_calcular_variacion_relativa($totEfeASecC, $totEfeBSecC);
		$lo_hoja->write($x, 0, 'Efectivo neto provisto por actividades financieras',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($totEfeASecC),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($totEfeBSecC),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difTotC),$lo_dataright);
		$lo_hoja->write($x, 5, $varTotC,$lo_dataright);
		$x++;$x++;
		//TOTALES
		$totEfeA = $totEfeASecA + $totEfeASecB + $totEfeASecC;
		$totEfeB = $totEfeBSecA + $totEfeBSecB + $totEfeBSecC;
		$difAuDiNe = $totEfeA - $totEfeB;
		$varAuDiNe = $io_report->uf_calcular_variacion_relativa($totEfeA, $totEfeB);
		$lo_hoja->write($x, 0, 'Aumento/Disminución neto en efectivo y equivalentes de efectivo',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($totEfeA),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($totEfeB),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difAuDiNe),$lo_dataright);
		$lo_hoja->write($x, 5, $varAuDiNe,$lo_dataright);
		$x++;
		//Activo Disponible saldo de apertura
		$arr11101  = $io_report->uf_obtener_saldo('111', $ldt_perant);
		$difActDis = $arr11101['salAct'] - $arr11101['salAnt'];
		$varActDis = $io_report->uf_calcular_variacion_relativa($arr11101['salAct'], $arr11101['salAnt']);
		$lo_hoja->write($x, 0, 'Efectivo y equivalente de efectivo al inicio del ejercicio',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($arr11101['salAct']),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($arr11101['salAnt']),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difActDis),$lo_dataright);
		$lo_hoja->write($x, 5, $varActDis,$lo_dataright);
		$x++;
		//Total final.
		$totFinA = $totEfeA + $arr11101['salAct'];
		$totFinB = $totEfeB + $arr11101['salAnt'];
		$difTotFin = $totFinA - $totFinB;
		$varTotal  = $io_report->uf_calcular_variacion_relativa($totFinA, $totFinB);
		$lo_hoja->write($x, 0, 'Efectivo y equivalente de efectivo al cierre del ejercicio',$lo_subtitulo);
		$lo_hoja->write($x, 1, '',$lo_titulo);
		$lo_hoja->write($x, 2, uf_formato($totFinA),$lo_dataright);
		$lo_hoja->write($x, 3, uf_formato($totFinB),$lo_dataright);
		$lo_hoja->write($x, 4, uf_formato($difTotFin),$lo_dataright);
		$lo_hoja->write($x, 5, $varTotal,$lo_dataright);
		

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"flujodeefectivodetallado.xls\"");
		header("Content-Disposition: inline; filename=\"flujodeefectivodetallado.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 