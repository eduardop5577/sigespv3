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
	
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");	
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("class_funciones_scg.php");
	require_once("sigesp_scg_reporte_comparado_0809.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "resumeninversiones0809.xls");
	$ls_bolivares ="Bs.";
	$io_fecha = new class_fecha();
	$io_fun_scg=new class_funciones_scg();
	$io_funciones=new class_funciones();
	$io_report  = new sigesp_scg_reporte_comparado_0809();	
	$io_fecha=new class_fecha();
	$io_fun_scg = new class_funciones_scg();
	$lo_libro   = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja    =  &$lo_libro->addworksheet();

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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_resumeninversiones0809.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_formato_salida($as_cuenta,$ai_nivel,$as_formato,$as_separador)
	{
	    ///////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_formato_salida
        //         Access: public
        //        Returns: vacio     
        //    Description: Este método da formato según lo que estable los instrucivos de la ONAPRE para la cuentas
        //////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_arreglo = explode('-',$as_formato);
        $ls_cuenta = "";
		$j=0;
		$ls_nvoformato = "";
		do
		{
		 if($j<$ai_nivel-1)
		 {
		  $ls_nvoformato .= $la_arreglo[$j].'-';
		 }
		 else
		 {
		  $ls_nvoformato .= $la_arreglo[$j];
		 }
		 $j++;
		}while($j<$ai_nivel);
		
		$la_arreglo_nvo = explode('-',trim($ls_nvoformato));
		$li_total = count((array)$la_arreglo_nvo);
		$ini = 0;
		foreach($la_arreglo_nvo as $key => $valor)
		{
		  if($key <> 0)
		  {
		   $ini += strlen(trim($la_arreglo_nvo[$key-1]));
		  }
		  $len = strlen(trim($valor));
		  if($key<$li_total-1)
		  {
		   $ls_cuenta .= substr(trim($as_cuenta),$ini,$len).$as_separador;
		  }
		  else
		  {
		   $ls_cuenta .= substr(trim($as_cuenta),$ini,$len);
		  }
		}
		
		return $ls_cuenta;
	
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_formato_cont = $_SESSION["la_empresa"]["formcont"];
		$ls_etiqueta=$_GET["etiqueta"];
		if($ls_etiqueta=="Mensual")
		{
			$ls_combo=$_GET["mesdes"];
			$ls_combomes=$_GET["meshas"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combomes,0,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			$ls_cant_mes=1;
			$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			$ls_combo=$ls_combo.$ls_combomes;
			
		}
		else
		{
			$ls_combo=$_GET["mesdes"];
			$li_mesdes=substr($ls_combo,0,2);
			$li_meshas=substr($ls_combo,2,2); 
			$li_mesdes=intval($li_mesdes);
			$li_meshas=intval($li_meshas); 
			if($ls_etiqueta=="Bimensual")
			{
				$ls_cant_mes=2;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Trimestral")
			{
				$ls_cant_mes=3;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
			if($ls_etiqueta=="Semestral")
			{
				$ls_cant_mes=6;
				$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas);
			}
		}
		if($ls_etiqueta=="Mensual")
		{
		   $ls_etiqueta="MES";
		}
		if($ls_etiqueta=="Bi-Mensual")
		{
		   $ls_etiqueta="BIMESTRE";
		}
		if($ls_etiqueta=="Trimestral")
		{
		   $ls_etiqueta="TRIMESTRE";
		}
		if($ls_etiqueta=="Semestral")
		{
		   $ls_etiqueta="SEMESTRE";
		}
		$ls_mesdes=substr($ls_combo,0,2);
		$ls_meshas=substr($ls_combo,2,2);
		$ls_diades="01";
		$ls_diahas=$io_fecha->uf_last_day($ls_meshas,$li_ano);
		$ldt_fecdes=$ls_diades."/".$ls_mesdes."/".$li_ano;
		$ldt_fechas=$ls_diahas;
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($ldt_fechas);
		$ls_periodo = "Desde el ".$ldt_fecdes." al ".$ld_fechas;
//----------------------------------------------------  Parámetros del encabezado  --------------------------------------------
		$ls_titulo="RESUMEN DE  INVERSIONES";       
		$ls_titulo1=" (En Bolívares) ";
//------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$lb_valido=uf_insert_seguridad("<b>Instructivo 08 Comparado Resumen de Inversiones</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
	    $lb_valido=$io_report->uf_scg_reportes_comparados_resumen_inversiones_ins08($ldt_fecdes,$ldt_fechas,$li_mesdes,$li_meshas,
	                                                                    $ls_cant_mes);
	}
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
		$lo_dataleftbold= &$lo_libro->addformat();
		$lo_dataleftbold->set_text_wrap();
		$lo_dataleftbold->set_font("Verdana");
		$lo_dataleftbold->set_align('left');
		$lo_dataleftbold->set_size('9');
		$lo_dataleftbold->set_bold();
		$lo_dataright= &$lo_libro->addformat(array('num_format' =>'#,##0.00;[Red](#,##0.00)'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_datarightbold= &$lo_libro->addformat(array('num_format' =>'#,##0.00;[Red](#,##0.00)'));
		$lo_datarightbold->set_font("Verdana");
		$lo_datarightbold->set_align('right');
		$lo_datarightbold->set_size('9');
		$lo_datarightbold->set_bold();
		$lo_datacuenta= &$lo_libro->addformat();
		$lo_datacuenta->set_font("Verdana");
		$lo_datacuenta->set_align('right');
		$lo_datacuenta->set_size('9');
		
		$lo_hoja->set_column(0,0,40);
		$lo_hoja->set_column(1,1,60);
		$lo_hoja->write(0, 0, "CODIGO PRESUPUESTARIO DEL ENTE: ",$lo_dataleftbold); // Agregar el título
		$lo_hoja->write(1, 0, "DENOMINACION DEL ENTE: ",$lo_dataleftbold); // Agregar el título
		$lo_hoja->write(2, 0, "ORGANO DE ADSCRIPCION: ",$lo_dataleftbold); // Agregar el título
		$lo_hoja->write(3, 0, "PERIODO: ",$lo_dataleftbold); // Agregar el título
		$lo_hoja->write(0, 1,$_SESSION["la_empresa"]["codasiona"],$lo_dataleft); // Agregar el título
		$lo_hoja->write(1, 1,$_SESSION["la_empresa"]["nombre"],$lo_dataleft); // Agregar el título
		$lo_hoja->write(2, 1,$_SESSION["la_empresa"]["nomorgads"],$lo_dataleft); // Agregar el título
		$lo_hoja->write(3, 1,strtoupper($ls_periodo),$lo_dataleft); // Agregar el título
		$lo_hoja->write(5, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(6, 3, $ls_titulo1,$lo_encabezado);
		$li_row = 8;
		$lo_hoja->set_column(2,7,40);
		$lo_hoja->set_column(8,9,50);
		$lo_hoja->write($li_row, 4,"VARIACION ".$ls_etiqueta,$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 6,"VARIACION EJECUTADO - PROGRAMADO ".$ls_etiqueta,$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 8,"TOTAL ACUMULADO AL ".$ls_etiqueta,$lo_titulo); // Agregar el título
		$li_row++;
		$lo_hoja->write($li_row, 0,"Codigo",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 1,"Denominacion",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 2,"Presupuesto Aprobado",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 3,"Presupuesto Modificado",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 4,"Programado",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 5,"Ejecutado",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 6,"Absoluta",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 7,"Porcentual",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 8,"Programado",$lo_titulo); // Agregar el título
		$lo_hoja->write($li_row, 9,"Ejecutado",$lo_titulo); // Agregar el título
		
		$ld_total_monto_programado=0;
		$ld_total_monto_programado_acumulado=0;
		$ld_total_monto_ejecutado=0;
		$ld_total_monto_ejecutado_acumulado=0;
		$ld_total_variacion_absoluta=0;
		$ld_total_porcentaje_variacion=0;
		$ld_total_variacion_absoluta_acumulada=0;
		$ld_total_porcentaje_variacion_acumulada=0;
		$ld_total_reprog_proxima=0;
        $ld_total_presupuesto_aprobado = 0;
        $ld_total_presupuesto_modificado = 0;
		$li_total=$io_report->dts_reporte->getRowCount("cuenta");
		$la_cuentas_saldos = array();
		$ls_mascara = str_replace("-","",$ls_formato_cont);
		$li_len_mas = strlen($ls_mascara);
		$ls_ceros = str_pad("",$li_len_mas - 9,"0");  
        $la_cuentas_saldos[1]  = '121010100'.$ls_ceros;
        $la_cuentas_saldos[2]  = '121010200'.$ls_ceros;
        $la_cuentas_saldos[3]  = '121020100'.$ls_ceros;
        $la_cuentas_saldos[4]  = '121020200'.$ls_ceros;
        $la_cuentas_saldos[5]  = '121030100'.$ls_ceros;
        $la_cuentas_saldos[6]  = '121030200'.$ls_ceros;
        $la_cuentas_saldos[7]  = '123010100'.$ls_ceros;
        $la_cuentas_saldos[8]  = '123010200'.$ls_ceros;
        $la_cuentas_saldos[9]  = '123010300'.$ls_ceros;
        $la_cuentas_saldos[10] = '123010400'.$ls_ceros;
        $la_cuentas_saldos[11] = '123010500'.$ls_ceros;
        $la_cuentas_saldos[12] = '123010600'.$ls_ceros;
        $la_cuentas_saldos[13] = '123010700'.$ls_ceros;
        $la_cuentas_saldos[14] = '123010800'.$ls_ceros;
        $la_cuentas_saldos[15] = '123011900'.$ls_ceros;
        $la_cuentas_saldos[16] = '123020000'.$ls_ceros;
        $la_cuentas_saldos[17] = '123030000'.$ls_ceros;
        $la_cuentas_saldos[18] = '123040000'.$ls_ceros;
        $la_cuentas_saldos[19] = '123050100'.$ls_ceros;
        $la_cuentas_saldos[20] = '123050200'.$ls_ceros;
        $la_cuentas_saldos[21] = '124010000'.$ls_ceros;
        $la_cuentas_saldos[22] = '124020000'.$ls_ceros;
        $la_cuentas_saldos[23] = '124030000'.$ls_ceros;
        $la_cuentas_saldos[24] = '124040000'.$ls_ceros;
        $la_cuentas_saldos[25] = '124050000'.$ls_ceros;
        $la_cuentas_saldos[26] = '124190000'.$ls_ceros;
		
		for($z=1;$z<=$li_total;$z++)
		{
			$li_row++;
			$ls_sc_cuenta=$io_report->dts_reporte->data["cuenta"][$z];
			$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
			$li_nivel=$io_report->dts_reporte->data["nivel"][$z];
			$ld_monto_programado=$io_report->dts_reporte->data["monto_programado"][$z];
			$ld_monto_programado_acumulado=$io_report->dts_reporte->data["programado_acumulado"][$z];
			$ld_monto_ejecutado=$io_report->dts_reporte->data["monto_ejecutado"][$z];
			$ld_monto_ejecutado_acumulado=$io_report->dts_reporte->data["ejecutado_acumulado"][$z];
			$ld_variacion_absoluta=$io_report->dts_reporte->data["variacion_absoluta"][$z];
			$ld_porcentaje_variacion=$io_report->dts_reporte->data["porcentaje_variacion"][$z];
            $ld_presupuesto_aprobado=$io_report->dts_reporte->data["presupuesto_aprobado"][$z];
            $ld_presupuesto_modificado=$io_report->dts_reporte->data["presupuesto_modificado"][$z];
			
			if(($li_nivel==1)&&(substr($ls_sc_cuenta,0,1) == "4"))
			{
				$ld_total_monto_programado=$ld_total_monto_programado+$ld_monto_programado;
				$ld_total_monto_programado_acumulado=$ld_total_monto_programado_acumulado+$ld_monto_programado_acumulado;
				$ld_total_monto_ejecutado=$ld_total_monto_ejecutado+$ld_monto_ejecutado;
				$ld_total_monto_ejecutado_acumulado=$ld_total_monto_ejecutado_acumulado+$ld_monto_ejecutado_acumulado;
				$ld_total_reprog_proxima=$ld_total_reprog_proxima+$ld_reprog_proxima;
                $ld_total_presupuesto_aprobado = $ld_total_presupuesto_aprobado +  $ld_presupuesto_aprobado;
                $ld_total_presupuesto_modificado = $ld_total_presupuesto_modificado + $ld_presupuesto_modificado;  
			}
			
			if(is_int(array_search($ls_sc_cuenta,$la_cuentas_saldos)))
			{
			    $ld_total_monto_programado=$ld_total_monto_programado+$ld_monto_programado;
				$ld_total_monto_programado_acumulado=$ld_total_monto_programado_acumulado+$ld_monto_programado_acumulado;
				$ld_total_monto_ejecutado=$ld_total_monto_ejecutado+$ld_monto_ejecutado;
				$ld_total_monto_ejecutado_acumulado=$ld_total_monto_ejecutado_acumulado+$ld_monto_ejecutado_acumulado;
				$ld_total_reprog_proxima=$ld_total_reprog_proxima+$ld_reprog_proxima;
                $ld_total_presupuesto_aprobado = $ld_total_presupuesto_aprobado +  $ld_presupuesto_aprobado;
                $ld_total_presupuesto_modificado = $ld_total_presupuesto_modificado + $ld_presupuesto_modificado;
			
			}
			
			
			if(substr($ls_sc_cuenta,0,1) != 4)
			{
             $ls_sc_cuenta = uf_formato_salida($ls_sc_cuenta,$li_nivel,$ls_formato_cont,".");
			}
			else
			{
			 $li_nivel_spg = count((array)explode('-',$_SESSION["la_empresa"]["formpre"]));
			 $ls_sc_cuenta = uf_formato_salida($ls_sc_cuenta,$li_nivel_spg,$_SESSION["la_empresa"]["formpre"],".");
			}	
							   		   
			  	$lo_hoja->write($li_row, 0,$ls_sc_cuenta,$lo_datacuenta); // Agregar el título
				$lo_hoja->write($li_row, 1,$ls_denominacion,$lo_dataleft); // Agregar el título
				$lo_hoja->write($li_row, 2,$ld_presupuesto_aprobado,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 3,$ld_presupuesto_modificado,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 4,$ld_monto_programado,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 5,$ld_monto_ejecutado,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 6,$ld_variacion_absoluta,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 7,$ld_porcentaje_variacion,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 8,$ld_monto_programado_acumulado,$lo_dataright); // Agregar el título
				$lo_hoja->write($li_row, 9,$ld_monto_programado_acumulado,$lo_dataright); // Agregar el título
								                                              	
		}//for
		        $ld_total_variacion_absoluta = abs($ld_total_monto_programado - $ld_total_monto_ejecutado);
				
				$ld_total_variacion_absoluta_acumulada = abs($ld_total_monto_programado_acumulado-$ld_total_monto_ejecutado_acumulado);
				
				if($ld_total_monto_programado > 0)
				{
				 $ld_total_porcentaje_variacion =  ($ld_total_monto_ejecutado/$ld_total_monto_programado)*100;
				}
				else
				{
				 $ld_total_porcentaje_variacion = 0;
				}
				
				if($ld_total_monto_programado_acumulado > 0)
				{
				 $ld_total_porcentaje_variacion_acumulada= ($ld_total_monto_ejecutado_acumulado/$ld_total_monto_programado_acumulado)*100; 
				}
				else
				{
				 $ld_total_porcentaje_variacion_acumulada = 0;
				}
				
				
				
				$ld_total_monto_programado=number_format($ld_total_monto_programado,2,",",".");
				$ld_total_monto_programado_acumulado=number_format($ld_total_monto_programado_acumulado,2,",",".");
				$ld_total_monto_ejecutado=number_format($ld_total_monto_ejecutado,2,",",".");
				$ld_total_monto_ejecutado_acumulado=number_format($ld_total_monto_ejecutado_acumulado,2,",",".");
				$ld_total_variacion_absoluta=number_format($ld_total_variacion_absoluta,2,",",".");
				$ld_total_porcentaje_variacion=number_format($ld_total_porcentaje_variacion,2,",",".");
                $ld_total_presupuesto_aprobado = number_format($ld_total_presupuesto_aprobado,2,",",".");
                $ld_total_presupuesto_modificado = number_format($ld_total_presupuesto_modificado,2,",",".");
				$li_row++;
				$lo_hoja->write($li_row, 0,'',$lo_datacuenta); // Agregar el título
				$lo_hoja->write($li_row, 1,'TOTALES',$lo_dataleftbold); // Agregar el título
				$lo_hoja->write($li_row, 2,$ld_presupuesto_aprobado,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 3,$ld_presupuesto_modificado,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 4,$ld_monto_programado,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 5,$ld_monto_ejecutado,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 6,$ld_variacion_absoluta,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 7,$ld_porcentaje_variacion,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 8,$ld_monto_programado_acumulado,$lo_datarightbold); // Agregar el título
				$lo_hoja->write($li_row, 9,$ld_monto_programado_acumulado,$lo_datarightbold); // Agregar el título
				
	    $lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"resumeninversiones0809.xls\"");
		header("Content-Disposition: inline; filename=\"resumeninversiones0809.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
				 
	}//else
	unset($io_report);
	unset($io_funciones);
?> 