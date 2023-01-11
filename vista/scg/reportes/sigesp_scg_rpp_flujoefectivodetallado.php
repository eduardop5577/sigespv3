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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_flujo_efectivo.html",$ls_descripcion);
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
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,685,11,$as_titulo); // Agregar el título		
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo1); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,655,11,$as_titulo2); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,640,11,$as_titulo3); // Agregar el título

		$io_pdf->addText(510,750,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(510,740,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(510,730,8,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data, $periodo_an, $periodo_ac, $io_pdf){
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
		$la_encabezados[] = array('denominacion'=>'','nota'=>'','años'=>'<b>Años</b>','variacion'=>'<b>Variación</b>');
		$la_encabezados[] = array('denominacion'=>'','nota'=>'','años'=>'','variacion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
									   'nota'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna	
									   'años'=>array('justification'=>'center','width'=>160),
									   'variacion'=>array('justification'=>'center','width'=>160))); // Justificación y ancho de la columna
		$la_columnas=array('denominacion'=>'',
						   'nota'=>'',
						   'años'=>'',
						   'variacion'=>'');
		$io_pdf->ezTable($la_encabezados,$la_columnas,'',$la_config);
		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
									   'nota'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna	
									   'periodo_ac'=>array('justification'=>'right','width'=>80),
									   'periodo_an'=>array('justification'=>'right','width'=>80),
									   'absoluta'=>array('justification'=>'right','width'=>80),
									   'relativa'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$la_columnas=array('denominacion'=>'',
						   'nota'=>'<b>NOTA</b>',
						   'periodo_ac'=>"<b>{$periodo_ac}</b>",
						   'periodo_an'=>"<b>{$periodo_an}</b>",
						   'absoluta'=>"<b>Absoluta</b>",
						   'relativa'=>"<b>Relativa</b>");
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_formato($ad_monto) {
		if ($ad_monto<0) {
			return '('.number_format(abs($ad_monto),2,",",".").')';
		}
		else{
			return number_format($ad_monto,2,",",".");
		}
	}
	
	function uf_print_firmas($io_pdf) {
		global $io_pdf;
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->setLineStyle(1);

		
		$io_pdf->line(45,200,160,200);
		$io_pdf->line(210,200,350,200);

		$io_pdf->addText(45,205,7,"Firma:"); // Agregar el título
		$io_pdf->addText(45,190,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(45,180,7,"Cargo:"); // Agregar el título
		$io_pdf->addText(210,205,7,"Firma:"); // Agregar el título
		$io_pdf->addText(210,190,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(210,180,7,"Cargo:"); // Agregar el título
		
		$io_pdf->Rectangle(400,170,150,100);
		$io_pdf->addText(430,220,7,"SELLO INSTITUCIONAL"); // Agregar el título
	}
	
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
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
	$ls_titulo  = "<b> ".$_SESSION["la_empresa"]["nombre"]." </b>";
	$ls_titulo1 = "<b>ESTADO DE FLUJO DE EFECTIVO DETALLADO</b>";
	$ls_titulo2 = "<b> AL 01 de ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." de ".$li_ano."</b>";
	$ls_titulo3 = "<b>(EN BOLÍVARES)</b>";  
	
	
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Estado de Flujo de Efectivo en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(6,8.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$totEfeASecA = 0;
		$totEfeBSecA = 0;
		$la_data[] = array('denominacion'=>'<b>Flujo de efectivo proveniente de actividades de operación</b>','nota'=>'',
		                   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Resultado del ejercicio.
		$resultado = $io_report->uf_buscar_resultado($ldt_fecha);
		$resultAnt = $io_report->uf_buscar_resultado($ldt_perant);
		$difResult = $resultado - $resultAnt;
		$varResult = $io_report->uf_calcular_variacion_relativa($resultado, $resultAnt);
		$la_data[] = array('denominacion'=>'Resultado del ejercicio','nota'=>'    ',
						   'periodo_an'=>uf_formato($resultAnt),'periodo_ac'=>uf_formato($resultado),'absoluta'=>uf_formato($difResult),'relativa'=>$varResult);
		$totEfeASecA += $resultado;
		$totEfeBSecA += $resultAnt; 
		//Ajustes
		$la_data[] = array('denominacion'=>'Ajuste al resultado y el efectivo neto provisto por actividades operativas','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Depreciacion
		$arr61401  = $io_report->uf_obtener_saldo('61401', $ldt_fecha);
		$arr22501  = $io_report->uf_obtener_saldo('22501', $ldt_fecha);
		$difDepre  = $arr22501['salAct'] - $arr61401['salAct'];
		$varDepre  = $io_report->uf_calcular_variacion_relativa($arr22501['salAct'], $arr61401['salAct']);
		$la_data[] = array('denominacion'=>'Depreciación','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr61401['salAct']),'periodo_ac'=>uf_formato($arr22501['salAct']),'absoluta'=>uf_formato($difDepre),'relativa'=>$varDepre);
		$totEfeASecA += $arr22501['salAct'];
		$totEfeBSecA += $arr61401['salAct'];
		//Amortizacion
		$arr61402  = $io_report->uf_obtener_saldo('61402', $ldt_fecha);
		$arr22502  = $io_report->uf_obtener_saldo('22502', $ldt_fecha);
		$difAmor   = $arr22502['salAct'] - $arr61402['salAct'];
		$varAmor   = $io_report->uf_calcular_variacion_relativa($arr22502['salAct'], $arr61402['salAct']);
		$la_data[] = array('denominacion'=>'Amortización','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr61402['salAct']),'periodo_ac'=>uf_formato($arr22502['salAct']),'absoluta'=>uf_formato($difAmor),'relativa'=>$varAmor);
		$totEfeASecA += $arr22502['salAct'];
		$totEfeBSecA += $arr61402['salAct'];
		//Provisiones
		$arr22401  = $io_report->uf_obtener_saldo('22401', $ldt_fecha);
		$difProvi  = $arr22401['salAct'] - $arr22401['salAnt'];
		$varProvi  = $io_report->uf_calcular_variacion_relativa($arr22401['salAct'], $arr22401['salAnt']);
		$la_data[] = array('denominacion'=>'Provisiones','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr22401['salAnt']),'periodo_ac'=>uf_formato($arr22401['salAct']),'absoluta'=>uf_formato($difProvi),'relativa'=>$varProvi);
		$totEfeASecA += $arr22401['salAct'];
		$totEfeBSecA += $arr22401['salAnt'];
		//Utilidad
		$arr51504  = $io_report->uf_obtener_saldo('51504', $ldt_fecha);
		$difUtili  = $arr51504['salAct'] - $arr51504['salAnt'];
		$varUtili  = $io_report->uf_calcular_variacion_relativa($arr51504['salAct'], $arr51504['salAnt']);
		$la_data[] = array('denominacion'=>'Utilidad en venta de acciones','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr51504['salAnt']),'periodo_ac'=>uf_formato($arr51504['salAct']),'absoluta'=>uf_formato($difUtili),'relativa'=>$varUtili);
		$totEfeASecA += $arr51504['salAct'];
		$totEfeBSecA += $arr51504['salAnt'];
		//Venta de activo
		$arr51806  = $io_report->uf_obtener_saldo('51806', $ldt_fecha);
		$difVenAct = $arr51806['salAct'] - $arr51806['salAnt'];
		$varVenAct = $io_report->uf_calcular_variacion_relativa($arr51806['salAct'], $arr51806['salAnt']);
		$la_data[] = array('denominacion'=>'Utilidad en venta de activo fijo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr51806['salAnt']),'periodo_ac'=>uf_formato($arr51806['salAct']),'absoluta'=>uf_formato($difVenAct),'relativa'=>$varVenAct); 
		$totEfeASecA += $arr51806['salAct'];
		$totEfeBSecA += $arr51806['salAnt'];
		//Cambios netos 
		$la_data[] = array('denominacion'=>'Cambios netos en operaciones de activos y pasivos','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Cuentas por cobrar
		$la_data[] = array('denominacion'=>'Aumento/Disminución en cuentas por cobrar','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Cuentas comerciales
		$arr11203  = $io_report->uf_obtener_saldo('11203', $ldt_fecha);
		$difCueCom = $arr11203['salAct'] - $arr11203['salAnt'];
		$varCueCom = $io_report->uf_calcular_variacion_relativa($arr11203['salAct'], $arr11203['salAnt']);
		$la_data[] = array('denominacion'=>'      Cuentas comerciales por cobrar a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11203['salAnt']),'periodo_ac'=>uf_formato($arr11203['salAct']),'absoluta'=>uf_formato($difVenAct),'relativa'=>$varCueCom);
		$totEfeASecA += $arr11203['salAct'];
		$totEfeBSecA += $arr11203['salAnt'];
		//Anticipos contratistas
		$arr11211  = $io_report->uf_obtener_saldo('11211', $ldt_fecha);
		$difAntCon = $arr11211['salAct'] - $arr11211['salAnt'];
		$varAntCon = $io_report->uf_calcular_variacion_relativa($arr11211['salAct'], $arr11211['salAnt']);
		$la_data[] = array('denominacion'=>'      Anticipos a contratistas por contratos de corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11211['salAnt']),'periodo_ac'=>uf_formato($arr11211['salAct']),'absoluta'=>uf_formato($difAntCon),'relativa'=>$varAntCon);
		$totEfeASecA += $arr11211['salAct'];
		$totEfeBSecA += $arr11211['salAnt'];
		//Inventarios
		$la_data[] = array('denominacion'=>'Aumento/Disminución en inventarios','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		//Inventario de mercancia
		$arr11304  = $io_report->uf_obtener_saldo('11304', $ldt_fecha);
		$difInvMer = $arr11304['salAct'] - $arr11304['salAnt'];
		$varInvMer = $io_report->uf_calcular_variacion_relativa($arr11304['salAct'], $arr11304['salAnt']);
		$la_data[] = array('denominacion'=>'      Inventario de mercancías','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11304['salAnt']),'periodo_ac'=>uf_formato($arr11304['salAct']),'absoluta'=>uf_formato($difInvMer),'relativa'=>$varInvMer);
		$totEfeASecA += $arr11304['salAct'];
		$totEfeBSecA += $arr11304['salAnt'];
		//Inventario de materiales
		$arr11305  = $io_report->uf_obtener_saldo('11305', $ldt_fecha);
		$difInvMat = $arr11305['salAct'] - $arr11305['salAnt'];
		$varInvMat = $io_report->uf_calcular_variacion_relativa($arr11305['salAct'], $arr11305['salAnt']);
		$la_data[] = array('denominacion'=>'      Inventario de materiales y suministros','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11305['salAnt']),'periodo_ac'=>uf_formato($arr11305['salAct']),'absoluta'=>uf_formato($difInvMat),'relativa'=>$varInvMat);	
		$totEfeASecA += $arr11305['salAct'];
		$totEfeBSecA += $arr11305['salAnt'];
		//Prepagados
		$la_data[] = array('denominacion'=>'Aumento/Disminución en prepagados','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Anticipado a corto plazo
		$arr11401  = $io_report->uf_obtener_saldo('11401', $ldt_fecha);
		$difGasAnt = $arr11401['salAct'] - $arr11401['salAnt'];
		$varGasAnt = $io_report->uf_calcular_variacion_relativa($arr11401['salAct'], $arr11401['salAnt']);
		$la_data[] = array('denominacion'=>'      Gastos pagados por anticipado a corto plazo','nota'=>'    ',
		           		   'periodo_an'=>uf_formato($arr11401['salAnt']),'periodo_ac'=>uf_formato($arr11401['salAct']),'absoluta'=>uf_formato($difGasAnt),'relativa'=>$varGasAnt);
		$totEfeASecA += $arr11401['salAct'];
		$totEfeBSecA += $arr11401['salAnt'];
		//Deposito garantia a corto plazo
		$arr11402  = $io_report->uf_obtener_saldo('11402', $ldt_fecha);
		$difDepGar = $arr11402['salAct'] - $arr11402['salAnt'];
		$varDepGar = $io_report->uf_calcular_variacion_relativa($arr11402['salAct'], $arr11402['salAnt']);
		$la_data[] = array('denominacion'=>'      Depósitos otorgados en garantía a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11402['salAnt']),'periodo_ac'=>uf_formato($arr11402['salAct']),'absoluta'=>uf_formato($difDepGar),'relativa'=>$varDepGar);
		$totEfeASecA += $arr11402['salAct'];
		$totEfeBSecA += $arr11402['salAnt'];
		//Otros activos diferidos
		$arr11499  = $io_report->uf_obtener_saldo('11499', $ldt_fecha);
		$difActDif = $arr11499['salAct'] - $arr11499['salAnt'];
		$varActDif = $io_report->uf_calcular_variacion_relativa($arr11499['salAct'], $arr11499['salAnt']);
		$la_data[] = array('denominacion'=>'      Otros activos diferidos a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11499['salAnt']),'periodo_ac'=>uf_formato($arr11499['salAct']),'absoluta'=>uf_formato($difActDif),'relativa'=>$varActDif);
		$totEfeASecA += $arr11499['salAct'];
		$totEfeBSecA += $arr11499['salAnt'];
		//Cargos diferidos
		$la_data[] = array('denominacion'=>'Aumento/Disminución en cargos diferidos','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		$arr21301  = $io_report->uf_obtener_saldo('21301', $ldt_fecha); 
		$difCarDif = $arr21301['salAct'] - $arr21301['salAnt'];
		$varCarDif = $io_report->uf_calcular_variacion_relativa($arr21301['salAct'], $arr21301['salAnt']);
		$la_data[] = array('denominacion'=>'      Pasivos diferidos a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr21301['salAnt']),'periodo_ac'=>uf_formato($arr21301['salAct']),'absoluta'=>uf_formato($difCarDif),'relativa'=>$varCarDif);
		$totEfeASecA += $arr21301['salAct'];
		$totEfeBSecA += $arr21301['salAnt'];
		//Otros activos
		$la_data[] = array('denominacion'=>'Aumento/Disminución en otros activos','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr11909  = $io_report->uf_obtener_saldo('11909', $ldt_fecha);
		$difOtrAct = $arr11909['salAct'] - $arr11909['salAnt']; 
		$varOtrAct = $io_report->uf_calcular_variacion_relativa($arr11909['salAct'], $arr11909['salAnt']);
		$la_data[] = array('denominacion'=>'      Otros activos circulantes','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11909['salAnt']),'periodo_ac'=>uf_formato($arr11909['salAct']),'absoluta'=>uf_formato($difOtrAct),'relativa'=>$varOtrAct);
		$totEfeASecA += $arr11909['salAct'];
		$totEfeBSecA += $arr11909['salAnt'];
		//Cuentas por pagar
		$la_data[] = array('denominacion'=>'Aumento/Disminución en  cuentas por pagar','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr21103  = $io_report->uf_obtener_saldo('21103', $ldt_fecha);
		$difCuePag = $arr21103['salAct'] - $arr21103['salAnt'];  
		$varCuePag = $io_report->uf_calcular_variacion_relativa($arr21103['salAct'], $arr21103['salAnt']);
		$la_data[] = array('denominacion'=>'      Cuentas por pagar a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr21103['salAnt']),'periodo_ac'=>uf_formato($arr21103['salAct']),'absoluta'=>uf_formato($difCuePag),'relativa'=>$varCuePag);
		$totEfeASecA += $arr21103['salAct'];
		$totEfeBSecA += $arr21103['salAnt'];
		//Gastos acumulados
		$la_data[] = array('denominacion'=>'Aumento/Disminución en gastos acumulados','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		//Personal por pagar
		$arr21101  = $io_report->uf_obtener_saldo('21101', $ldt_fecha);
		$difPerPag = $arr21101['salAct'] - $arr21101['salAnt'];  
		$varPerPag = $io_report->uf_calcular_variacion_relativa($arr21101['salAct'], $arr21101['salAnt']);
		$la_data[] = array('denominacion'=>'      Gastos de personal por pagar','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr21101['salAnt']),'periodo_ac'=>uf_formato($arr21101['salAct']),'absoluta'=>uf_formato($difPerPag),'relativa'=>$varPerPag);
		$totEfeASecA += $arr21101['salAct'];
		$totEfeBSecA += $arr21101['salAnt'];
		//Fondos de terceros
		$arr21499  = $io_report->uf_obtener_saldo('21499', $ldt_fecha);
		$difFonTer = $arr21499['salAct'] - $arr21499['salAnt'];  
		$varFonTer = $io_report->uf_calcular_variacion_relativa($arr21499['salAct'], $arr21499['salAnt']);
		$la_data[] = array('denominacion'=>'      Otros fondos de terceros','nota'=>'    ',
		 				   'periodo_an'=>uf_formato($arr21499['salAnt']),'periodo_ac'=>uf_formato($arr21499['salAct']),'absoluta'=>uf_formato($difFonTer),'relativa'=>$varFonTer);
		$totEfeASecA += $arr21499['salAct'];
		$totEfeBSecA += $arr21499['salAnt'];
		//Pasivos circulantes
		$arr21909  = $io_report->uf_obtener_saldo('21909', $ldt_fecha);
		$difPasCir = $arr21909['salAct'] - $arr21909['salAnt'];  
		$varPasCir = $io_report->uf_calcular_variacion_relativa($arr21909['salAct'], $arr21909['salAnt']);
		$la_data[] = array('denominacion'=>'      Otros pasivos circulantes','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr21909['salAnt']),'periodo_ac'=>uf_formato($arr21909['salAct']),'absoluta'=>uf_formato($difPasCir),'relativa'=>$varPasCir);
		$totEfeASecA += $arr21909['salAct'];
		$totEfeBSecA += $arr21909['salAnt'];
		//Prestaciones sociales
		$la_data[] = array('denominacion'=>'Pago prestaciones sociales','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>''); 
		$arr21102  = $io_report->uf_obtener_saldo('21102', $ldt_fecha);
		$difPreSoc = $arr21102['salAct'] - $arr21102['salAnt'];  
		$varPreSoc = $io_report->uf_calcular_variacion_relativa($arr21102['salAct'], $arr21102['salAnt']);
		$la_data[] = array('denominacion'=>'      Aportes patronales y retenciones laborales por pagar','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr21102['salAnt']),'periodo_ac'=>uf_formato($arr21102['salAct']),'absoluta'=>uf_formato($difPreSoc),'relativa'=>$varPreSoc);
		$totEfeASecA += $arr21102['salAct'];
		$totEfeBSecA += $arr21102['salAnt'];
		//Efectivo neto
		$difTotA = $totEfeASecA - $totEfeBSecA;
		$varTotA = $io_report->uf_calcular_variacion_relativa($totEfeASecA, $totEfeBSecA);
		$la_data[] = array('denominacion'=>'<b>Efectivo neto provisto por actividades de operación</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($totEfeBSecA),'periodo_ac'=>uf_formato($totEfeASecA),'absoluta'=>uf_formato($difTotA),'relativa'=>$varTotA);
		$la_data[] = array('denominacion'=>'','nota'=>'','periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		
		//Actividades de inversión
		$totEfeASecB = 0;
		$totEfeBSecB = 0;
		$la_data[] = array('denominacion'=>'<b>Flujo de efectivo proveniente de actividades de inversión</b>','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$la_data[] = array('denominacion'=>'Compra de activo fijo','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		//Bienes de uso
		$arr12301  = $io_report->uf_obtener_saldo('12301', $ldt_fecha);
		$difBieUso = $arr12301['salAct'] - $arr12301['salAnt'];  
		$varBieUso = $io_report->uf_calcular_variacion_relativa($arr12301['salAct'], $arr12301['salAnt']);
		$la_data[] = array('denominacion'=>'      Bienes de uso','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr12301['salAnt']),'periodo_ac'=>uf_formato($arr12301['salAct']),'absoluta'=>uf_formato($difBieUso),'relativa'=>$varBieUso);
		$totEfeASecB += $arr12301['salAct'];
		$totEfeBSecB += $arr12301['salAnt'];
		//Tierras y terrenos
		$arr12302  = $io_report->uf_obtener_saldo('12302', $ldt_fecha);
		$difTieTer = $arr12302['salAct'] - $arr12302['salAnt'];
		$varTieTer = $io_report->uf_calcular_variacion_relativa($arr12302['salAct'], $arr12302['salAnt']);
		$la_data[] = array('denominacion'=>'      Tierras y terrenos','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr12302['salAnt']),'periodo_ac'=>uf_formato($arr12302['salAct']),'absoluta'=>uf_formato($difTieTer),'relativa'=>$varTieTer);
		$totEfeASecB += $arr12302['salAct'];
		$totEfeBSecB += $arr12302['salAnt'];
		//Efectivo recibido
		$la_data[] = array('denominacion'=>'Efectivo recibido en venta de activo fijo','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr51806  = $io_report->uf_obtener_saldo('51806', $ldt_fecha);
		$difUtiVen = $arr51806['salAct'] - $arr51806['salAnt'];
		$varUtiVen = $io_report->uf_calcular_variacion_relativa($arr51806['salAct'], $arr51806['salAnt']);
		$la_data[] = array('denominacion'=>'      Utilidad en venta de activo fijo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr12302['salAnt']),'periodo_ac'=>uf_formato($arr12302['salAct']),'absoluta'=>uf_formato($difUtiVen),'relativa'=>$varUtiVen);
		$totEfeASecB += $arr51806['salAct'];
		$totEfeBSecB += $arr51806['salAnt'];
		// Compra de acciones
		$la_data[] = array('denominacion'=>'Compra de acciones','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr11201  = $io_report->uf_obtener_saldo('11201', $ldt_fecha);
		$difComAcc = $arr11201['salAct'] - $arr11201['salAnt'];
		$varComAcc = $io_report->uf_calcular_variacion_relativa($arr11201['salAct'], $arr11201['salAnt']);
		$la_data[] = array('denominacion'=>'      Inversiones financieras en títulos y valores a corto plazo','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11201['salAnt']),'periodo_ac'=>uf_formato($arr11201['salAct']),'absoluta'=>uf_formato($difComAcc),'relativa'=>$varComAcc);
		$totEfeASecB += $arr11201['salAct'];
		$totEfeBSecB += $arr11201['salAnt'];
		// Venta de acciones
		$la_data[] = array('denominacion'=>'Efectivo recibido en venta de acciones','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr51504  = $io_report->uf_obtener_saldo('51504', $ldt_fecha);
		$difVenAcc = $arr51504['salAct'] - $arr51504['salAnt'];
		$varVenAcc = $io_report->uf_calcular_variacion_relativa($arr51504['salAct'], $arr51504['salAnt']);
		$la_data[] = array('denominacion'=>'      Utilidades de acciones y participaciones de capital','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr51504['salAnt']),'periodo_ac'=>uf_formato($arr51504['salAct']),'absoluta'=>uf_formato($difVenAcc),'relativa'=>$varVenAcc); 
		$totEfeASecB += $arr51504['salAct'];
		$totEfeBSecB += $arr51504['salAnt'];
		//Efectivo neto actividades de inversión
		$difTotB = $totEfeASecB - $totEfeBSecB;
		$varTotB = $io_report->uf_calcular_variacion_relativa($totEfeASecB, $totEfeBSecB);
		$la_data[] = array('denominacion'=>'<b>Efectivo neto usado en actividades de inversión</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($totEfeBSecB),'periodo_ac'=>uf_formato($totEfeASecB),'absoluta'=>uf_formato($difTotB),'relativa'=>$varTotB);
		$la_data[] = array('denominacion'=>'','nota'=>'','periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		
		//Actividades de financiamiento
		$totEfeASecC = 0;
		$totEfeBSecC = 0;
		$la_data[] = array('denominacion'=>'<b>Flujo de efectivo proveniente de actividades de financiamineto</b>','nota'=>'    ',
						   'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		//Pagares bancarios recibidos
		$la_data[]  = array('denominacion'=>'Pagares bancarios recibidos','nota'=>'    ',
							'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr2120207 = $io_report->uf_obtener_saldo('2120207', $ldt_fecha);
		$difDeuInt  = $arr2120207['salAct'] - $arr2120207['salAnt'];
		$varDeuInt  = $io_report->uf_calcular_variacion_relativa($arr2120207['salAct'], $arr2120207['salAnt']);
		$la_data[]  = array('denominacion'=>'      Deuda interna por préstamos recibidos de entes descentralizados financieros bancarios por pagar a corto plazo','nota'=>'    ',
						    'periodo_an'=>uf_formato($arr2120207['salAnt']),'periodo_ac'=>uf_formato($arr2120207['salAct']),'absoluta'=>uf_formato($difDeuInt),'relativa'=>$varDeuInt);
		$totEfeASecC += $arr2120207['salAct'];
		$totEfeBSecC += $arr2120207['salAnt'];
		//Pagares bancarios pagados
		$la_data[]    = array('denominacion'=>'Pagares bancarios pagados','nota'=>'    ',
							  'periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		$arr121030206 = $io_report->uf_obtener_saldo('121030206', $ldt_fecha);
		$difPreCob    = $arr121030206['salAct'] - $arr121030206['salAnt'];
		$varPreCob    = $io_report->uf_calcular_variacion_relativa($arr121030206['salAct'], $arr121030206['salAnt']);
		$la_data[]    = array('denominacion'=>'      Prestamos por cobrar a largo plazo a entes financieros bancarios','nota'=>'    ',
							  'periodo_an'=>uf_formato($arr121030206['salAnt']),'periodo_ac'=>uf_formato($arr121030206['salAct']),'absoluta'=>uf_formato($difPreCob),'relativa'=>$varPreCob);
		$totEfeASecC += $arr121030206['salAct'];
		$totEfeBSecC += $arr121030206['salAnt'];
		//Efectivo neto actividades de financiamiento
		$difTotC = $totEfeASecC - $totEfeBSecC;
		$varTotC = $io_report->uf_calcular_variacion_relativa($totEfeASecC, $totEfeBSecC);
		$la_data[] = array('denominacion'=>'<b>Efectivo neto provisto por actividades financieras</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($totEfeBSecC),'periodo_ac'=>uf_formato($totEfeASecC),'absoluta'=>uf_formato($difTotC),'relativa'=>$varTotC);
		$la_data[] = array('denominacion'=>'','nota'=>'','periodo_an'=>'','periodo_ac'=>'','absoluta'=>'','relativa'=>'');
		
		//TOTALES
		$totEfeA = $totEfeASecA + $totEfeASecB + $totEfeASecC;
		$totEfeB = $totEfeBSecA + $totEfeBSecB + $totEfeBSecC;
		$difAuDiNe = $totEfeA - $totEfeB;
		$varAuDiNe = $io_report->uf_calcular_variacion_relativa($totEfeA, $totEfeB);
		$la_data[] = array('denominacion'=>'<b>Aumento/Disminución neto en efectivo y equivalentes de efectivo</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($totEfeB),'periodo_ac'=>uf_formato($totEfeA),'absoluta'=>uf_formato($difAuDiNe),'relativa'=>$varAuDiNe);
		//Activo Disponible saldo de apertura
		$arr11101  = $io_report->uf_obtener_saldo('111', $ldt_perant);
		$difActDis = $arr11101['salAct'] - $arr11101['salAnt'];
		$varActDis = $io_report->uf_calcular_variacion_relativa($arr11101['salAct'], $arr11101['salAnt']);
		$la_data[] = array('denominacion'=>'<b>Efectivo y equivalente de efectivo al inicio del ejercicio</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($arr11101['salAnt']),'periodo_ac'=>uf_formato($arr11101['salAct']),'absoluta'=>uf_formato($difActDis),'relativa'=>$varActDis);
		//Total final.
		$totFinA = $totEfeA + $arr11101['salAct'];
		$totFinB = $totEfeB + $arr11101['salAnt'];
		$difTotFin = $totFinA - $totFinB;
		$varTotal  = $io_report->uf_calcular_variacion_relativa($totFinA, $totFinB);
		$la_data[] = array('denominacion'=>'<b>Efectivo y equivalente de efectivo al cierre del ejercicio</b>','nota'=>'    ',
						   'periodo_an'=>uf_formato($totFinB),'periodo_ac'=>uf_formato($totFinA),'absoluta'=>uf_formato($difTotFin),'relativa'=>$varTotal);
		
		uf_print_detalle($la_data, $li_anoant, $li_ano, $io_pdf);
		uf_print_firmas($io_pdf);
		unset($data);
		unset($arrdata);
		unset($la_data);		
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
	else {
		print("<script language=JavaScript>");
		print(" alert('No hay data para emitir el reporte');"); 
		print(" close();");
		print("</script>");
	}
	 
	unset($io_report);
    unset($io_funciones);		
?> 