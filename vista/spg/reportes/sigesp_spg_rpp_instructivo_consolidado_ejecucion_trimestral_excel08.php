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

		
	  // para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "spg_acumulado_x_cuentas.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();

		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report=new sigesp_spg_funciones_reportes();	
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("sigesp_spg_class_reportes_instructivos.php");
		$io_report = new sigesp_spg_class_reportes_instructivos();
	//-----------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

		$ls_trimestre=$_GET["trimestre"];
		$li_mesdes=substr($ls_trimestre,0,2);
		$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
		$li_meshas=substr($ls_trimestre,2,2);
		$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
		$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
		//FILTRO POR ESTRUCTURA
		$ls_codestpro1      = $_GET["codestpro1"];
		$ls_codestpro2      = $_GET["codestpro2"];
		$ls_codestpro3      = $_GET["codestpro3"];
		$ls_codestpro4      = $_GET["codestpro4"];
		$ls_codestpro5      = $_GET["codestpro5"];
		$ls_estclades       = $_GET["estclades"];
		$ls_codestpro1h     = $_GET["codestpro1h"];
		$ls_codestpro2h     = $_GET["codestpro2h"];
		$ls_codestpro3h     = $_GET["codestpro3h"];
		$ls_codestpro4h     = $_GET["codestpro4h"];
		$ls_codestpro5h     = $_GET["codestpro5h"];
		$ls_estclahas       = $_GET["estclahas"];
		
		if($li_estmodest==1) {
			if ($ls_codestpro1 != "0000000000000000000000000") {
				$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;
				$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h;
			}
		}
		elseif($li_estmodest==2) {
			if ($ls_codestpro1 != "0000000000000000000000000") {
				$ls_programatica_desde = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
				$ls_programatica_hasta = $ls_codestpro1h."-".$ls_codestpro2h."-".$ls_codestpro3h."-".$ls_codestpro4h."-".$ls_codestpro5h;
			}
		}
		
		$ls_codestproD = '';
		if ($ls_codestpro1 != "0000000000000000000000000") {
			$ls_codestproD = str_pad($ls_codestpro1,25,0,0).str_pad($ls_codestpro2,25,0,0).str_pad($ls_codestpro3,25,0,0).str_pad($ls_codestpro4,25,0,0).str_pad($ls_codestpro5,25,0,0).$ls_estclades;
		}
		
		$ls_codestproH = '';
		if ($ls_codestpro1h != "0000000000000000000000000") {
			$ls_codestproH = str_pad($ls_codestpro1h,25,0,0).str_pad($ls_codestpro2h,25,0,0).str_pad($ls_codestpro3h,25,0,0).str_pad($ls_codestpro4h,25,0,0).str_pad($ls_codestpro5h,25,0,0).$ls_estclahas;
		}
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
	$ls_titulo="CONSOLIDADO DE EJECUCIÓN FINANCIERA TRIMESTRAL DE PROYECTOS Y ACCIONES CENTRALIZADAS POR PARTIDAS";       	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spg_reporte_consolidado_de_ejecucion_trimestral($ldt_fecdes,$ldt_fechas,$ls_mesdes,$ls_meshas,$ls_codestproD,$ls_codestproH);
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
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
		$li_row=4;
		$lo_hoja->write(0, 0,"CÓDIGO PRESUPUESTARIO DEL ENTE",$lo_dataleft);
		$lo_hoja->write(0, 1, $_SESSION['la_empresa']['codasiona'],$lo_dataleft);
		$lo_hoja->write(1, 0,"DENOMINACION DEL ENTE",$lo_dataleft);
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nombre"],$lo_dataleft);
		$lo_hoja->write(1, 0,"ORGANO DE ADSCRIPCION",$lo_dataleft);
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nomorgads"],$lo_dataleft);
		$lo_hoja->write(1, 0,"PERIODO PRESUPUESTARIO",$lo_dataleft);
		$lo_hoja->write(1, 1,$io_funciones->uf_convertirfecmostrar(substr($_SESSION['la_empresa']['periodo'],0,10)),$lo_dataleft);
		
		$lo_hoja->write(3, 5,$ls_titulo,$lo_encabezado);
		
		$lo_hoja->write($li_row, 7, "Ejecutado en el Trimestre Nº ",$lo_titulo);
		$lo_hoja->write($li_row, 8, "",$lo_titulo);
		$lo_hoja->write($li_row, 9, "",$lo_titulo);
		$lo_hoja->write($li_row, 10, "Acumulado en el Trimestre Nº ",$lo_titulo);
		$lo_hoja->write($li_row, 11, "",$lo_titulo);
		$lo_hoja->write($li_row, 12, "",$lo_titulo);
	
	
		$li_row=$li_row+1;
		$lo_hoja->write($li_row, 0, "PARTIDA",$lo_titulo);
		$lo_hoja->write($li_row, 1, "DENOMINACIÓN",$lo_titulo);
		$lo_hoja->write($li_row, 2, "PRESUPUESTO APROBADO",$lo_titulo);
		$lo_hoja->write($li_row, 3, "PRESUPUESTO MODIFICADO",$lo_titulo);
		$lo_hoja->write($li_row, 4, "PROGRAMADO EN EL TRIMESTRE Nº ",$lo_titulo);
		$lo_hoja->write($li_row, 5, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 6, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 7, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 8, "PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 9, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 10, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 11, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 12, "DISPONIBILIDAD PRESUPUESTARIA",$lo_titulo);
		$li_row++;
		
	
		$li_total=$io_report->dts_reporte->getRowCount("spg_cuenta");
	    $ld_asignado_total=0;
	    $ld_asignado_modificado_total=0;
	    $ld_programado_trimestral_total=0;
	    $ld_comprometer_total=0;
	    $ld_causado_total=0;
	    $ld_pagado_total=0;
	    $ld_programado_acumulado_total=0;
	    $ld_comprometer_acumulado_total=0;
	    $ld_causado_acumulado_total=0;
	    $ld_pagado_acumulado_total=0;
	    $ld_disponibilidad_total=0;
		for($z=1;$z<=$li_total;$z++)
	    {
			  $thisPageNum=$io_pdf->ezPageCount;
			  $ls_spg_cuenta=substr(trim($io_report->dts_reporte->data["spg_cuenta"][$z]),0,3);
			  $ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			  $ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
			  $ld_asignado_modificado=$io_report->dts_reporte->data["asignado_modificado"][$z];
			  $ld_programado_trimestral=$io_report->dts_reporte->data["programado"][$z];
			  $ld_comprometer=$io_report->dts_reporte->data["compromiso"][$z];
			  $ld_causado=$io_report->dts_reporte->data["causado"][$z];
			  $ld_pagado=$io_report->dts_reporte->data["pagado"][$z];
			  $ld_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			  $ld_comprometer_acumulado=$io_report->dts_reporte->data["compromiso_acumulado"][$z];
			  $ld_causado_acumulado=$io_report->dts_reporte->data["causado_acumulado"][$z];
			  $ld_pagado_acumulado=$io_report->dts_reporte->data["pagado_acumulado"][$z];
			  $ld_disponibilidad=$io_report->dts_reporte->data["disponibilidad"][$z];
			  
			  $ld_asignado_total=$ld_asignado_total+$ld_asignado;
			  $ld_asignado_modificado_total=$ld_asignado_modificado_total+$ld_asignado_modificado;
			  $ld_programado_trimestral_total=$ld_programado_trimestral_total+$ld_programado_trimestral;
			  $ld_comprometer_total=$ld_comprometer_total+$ld_comprometer;
			  $ld_causado_total=$ld_causado_total+$ld_causado;
			  $ld_pagado_total=$ld_pagado_total+$ld_pagado;
			  $ld_programado_acumulado_total=$ld_programado_acumulado_total+$ld_programado_acumulado;
			  $ld_comprometer_acumulado_total=$ld_comprometer_acumulado_total+$ld_comprometer_acumulado;
			  $ld_causado_acumulado_total=$ld_causado_acumulado_total+$ld_causado_acumulado;
			  $ld_pagado_acumulado_total=$ld_pagado_acumulado_total+$ld_pagado_acumulado;
			  $ld_disponibilidad_total=$ld_disponibilidad_total+$ld_disponibilidad;
			  
			  $ld_asignado=number_format($ld_asignado,2,",",".");
			  $ld_asignado_modificado=number_format($ld_asignado_modificado,2,",",".");
			  $ld_programado_trimestral=number_format($ld_programado_trimestral,2,",",".");
			  $ld_comprometer=number_format($ld_comprometer,2,",",".");
			  $ld_causado=number_format($ld_causado,2,",",".");
			  $ld_pagado=number_format($ld_pagado,2,",",".");
			  $ld_programado_acumulado=number_format($ld_programado_acumulado,2,",",".");
			  $ld_comprometer_acumulado=number_format($ld_comprometer_acumulado,2,",",".");
			  $ld_causado_acumulado=number_format($ld_causado_acumulado,2,",",".");
			  $ld_pagado_acumulado=number_format($ld_pagado_acumulado,2,",",".");
			  $ld_disponibilidad=number_format($ld_disponibilidad,2,",",".");
			  
			if($ld_asignado == $ld_asignado_modificado)
			{
			 $ld_asignado_modificado = " ";
			}
			
			$lo_hoja->write($li_row, 0, " ".$ls_spg_cuenta,$lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_denominacion,$lo_dataleft);
			$lo_hoja->write($li_row, 2,$ld_asignado,$lo_dataright);
			$lo_hoja->write($li_row, 3, $ld_asignado_modificado,$lo_dataright);
			$lo_hoja->write($li_row, 4,$ld_programado_trimestral,$lo_dataright);
			$lo_hoja->write($li_row, 5, $ld_comprometer,$lo_dataright);
			$lo_hoja->write($li_row, 6, $ld_causado,$lo_dataright);
			$lo_hoja->write($li_row, 7, $ld_pagado,$lo_dataright);
			$lo_hoja->write($li_row, 8, $ld_programado_acumulado,$lo_dataright);
			$lo_hoja->write($li_row, 9, $ld_comprometer_acumulado,$lo_dataright);
			$lo_hoja->write($li_row, 10, $ld_causado_acumulado,$lo_dataright);
			$lo_hoja->write($li_row, 11, $ld_pagado_acumulado,$lo_dataright);
			$lo_hoja->write($li_row, 12,$ld_disponibilidad,$lo_dataright);				  
			$li_row++;	  							 						   
		}
	    $ld_asignado_total=number_format($ld_asignado_total,2,",",".");
	    $ld_asignado_modificado_total=number_format($ld_asignado_modificado_total,2,",",".");
	    $ld_programado_trimestral_total=number_format($ld_programado_trimestral_total,2,",",".");
	    $ld_comprometer_total=number_format($ld_comprometer_total,2,",",".");
	    $ld_causado_total=number_format($ld_causado_total,2,",",".");
	    $ld_pagado_total=number_format($ld_pagado_total,2,",",".");
	    $ld_programado_acumulado_total=number_format($ld_programado_acumulado_total,2,",",".");
	    $ld_comprometer_acumulado_total=number_format($ld_comprometer_acumulado_total,2,",",".");
	    $ld_causado_acumulado_total=number_format($ld_causado_acumulado_total,2,",",".");
	    $ld_pagado_acumulado_total=number_format($ld_pagado_acumulado_total,2,",",".");
	    $ld_disponibilidad_total=number_format($ld_disponibilidad_total,2,",",".");
	  
			if($ld_asignado_total == $ld_asignado_modificado_total)
			{
			 $ld_asignado_modificado_total = " ";
			}
			
			$lo_hoja->write($li_row, 0,"",$lo_dataleft);
			$lo_hoja->write($li_row, 1, "TOTALES Bs",$lo_dataleft);
			$lo_hoja->write($li_row, 2,$ld_asignado_total,$lo_dataright);
			$lo_hoja->write($li_row, 3, $ld_asignado_modificado_total,$lo_dataright);
			$lo_hoja->write($li_row, 4,$ld_programado_trimestral_total,$lo_dataright);
			$lo_hoja->write($li_row, 5, $ld_comprometer_total,$lo_dataright);
			$lo_hoja->write($li_row, 6, $ld_causado_total,$lo_dataright);
			$lo_hoja->write($li_row, 7, $ld_pagado_total,$lo_dataright);
			$lo_hoja->write($li_row, 8, $ld_programado_acumulado_total,$lo_dataright);
			$lo_hoja->write($li_row, 9, $ld_comprometer_acumulado_total,$lo_dataright);
			$lo_hoja->write($li_row, 10, $ld_causado_acumulado_total,$lo_dataright);
			$lo_hoja->write($li_row, 11, $ld_pagado_acumulado_total,$lo_dataright);
			$lo_hoja->write($li_row, 12,$ld_disponibilidad_total,$lo_dataright);				  
			$li_row++;	  							 						   
	}//else				
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"CONSOLIDADO DE EJECUCION FINANCIERA TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS.xls\"");
	header("Content-Disposition: inline; filename=\"CONSOLIDADO DE EJECUCION FINANCIERA TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
	print("<script language=JavaScript>");
	print(" close();");
	print("</script>");			
	unset($io_report);
	unset($io_funciones);
?> 