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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time ','0');
	
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$as_titulo,$as_moneda)
	{//uf_print_encabezado_pagina($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$ls_titulo,'(En Bolivares Fuertes)'); 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezadopagina
	//		   Acess: private 
	//	Arguments: as_titulo // Título del Reporte
	//	    	   as_periodo_comp // Descripción del periodo del comprobante
	//	    	   as_fecha_comp // Descripción del período de la fecha del comprobante 
	//	    	   io_pdf // Instancia de objeto pdf
	//    Description: función que imprime los encabezados por página
	//     Creado Por: Ing. Yozelin Barragán
	// Fecha Creación: 26/06/2006 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;
		$lo_hoja->write($li_row, 5, $as_titulo,$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 5, $as_moneda,$lo_titulo);
		$li_row++;
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo_reporte($li_row,$lo_titulo,$fechas,$mes,$as_nombre,$lo_hoja,$lo_encabezado,$io_encabezado)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_nombre    = $_SESSION["la_empresa"]["nombre"];
		$ls_nomorgads = $_SESSION["la_empresa"]["nomorgads"];
		$ls_codasiona = $_SESSION['la_empresa']['codasiona'];
		
		$lo_hoja->write($li_row, 0, "CODIGO PRESUPUESTARIO DEL ENTE: $ls_codasiona ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "DENOMINACION: $ls_nombre ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "ORGANO DE ADSCRIPCION: $ls_nomorgads ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "FECHA: $fechas ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, "MES: $mes ",$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, ''.strtoupper($_SESSION["la_empresa"]["nomestpro1"]).' Desde:    '.$as_nombre["D"]["1"].'    - Hasta: '.$as_nombre["H"]["1"],$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, ''.strtoupper($_SESSION["la_empresa"]["nomestpro2"]).' Desde:    '.$as_nombre["D"]["2"].'    - Hasta: '.$as_nombre["H"]["2"],$lo_titulo);
		$li_row++;
		$lo_hoja->write($li_row, 0, ''.strtoupper($_SESSION["la_empresa"]["nomestpro3"]).' Desde:    '.$as_nombre["D"]["3"].'    - Hasta: '.$as_nombre["H"]["3"],$lo_titulo);
		$li_row++;
		if($_SESSION["la_empresa"]["estmodest"]==2)
		{
                    $lo_hoja->write($li_row, 0, ''.strtoupper($_SESSION["la_empresa"]["nomestpro4"]).' Desde:    '.$as_nombre["D"]["4"].'    - Hasta: '.$as_nombre["H"]["4"],$lo_titulo);
                    $li_row++;
                    $lo_hoja->write($li_row, 0, ''.strtoupper($_SESSION["la_empresa"]["nomestpro5"]).' Desde:   '.$as_nombre["D"]["5"].'    - Hasta: '.$as_nombre["H"]["5"],$lo_titulo);
                    $li_row++;
                }
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$io_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_titulo
		//		   Access: private 
		//      Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//     Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;
		$li_row++;
		
		$lo_hoja->write($li_row, 6, "EJECUCION MENSUAL ",$lo_titulo);
		$lo_hoja->write($li_row, 10, "ACUMULADO ",$lo_titulo);
		
		$li_row++;

	}// end function uf_print_titulo
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$io_cabecera)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codper // total de registros que va a tener el reporte
		//	    		   as_nomper // total de registros que va a tener el reporte
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;		
		$li_row++;
		$lo_hoja->write($li_row, 0, "PARTIDAS-SUBPARTIDAS",$lo_titulo);
		$lo_hoja->write($li_row, 1, "DENOMINACION",$lo_titulo);
		$lo_hoja->write($li_row, 2, "PRESUPUESTO APROBADO",$lo_titulo);
		$lo_hoja->write($li_row, 3, "MODIFICACIONES DEL MES",$lo_titulo);
		$lo_hoja->write($li_row, 4, "PRESUPUESTO MODIFICADO",$lo_titulo);
		$lo_hoja->write($li_row, 5, "PROGRAMADO MENSUAL ",$lo_titulo);
		$lo_hoja->write($li_row, 6, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 7, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 8, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 9, "VARIACION ABSOLUTA CAUSADO VS PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 10, "PROGRAMADO ACUMULADO",$lo_titulo);
		$lo_hoja->write($li_row, 11, "COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 12, "CAUSADO",$lo_titulo);
		$lo_hoja->write($li_row, 13, "PAGADO",$lo_titulo);
		$lo_hoja->write($li_row, 14, "VARIACION ABSOLUTA ACUMULADA CAUSADO VS PROGRAMADO",$lo_titulo);
		$lo_hoja->write($li_row, 15, "DISPONIBILIDAD PRESUPUESTARIA COMPROMISO",$lo_titulo);
		$lo_hoja->write($li_row, 16, "DISPONIBILIDAD PRESUPUESTARIA CAUSADO",$lo_titulo);
		$li_row++;
		
	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;		
		for( $i = 1; $i <= count($la_data); $i ++)
		{
			$lo_hoja->write($li_row, 0, $la_data[$i]['cuenta'],$lo_datacenter);
			$lo_hoja->write($li_row, 1, $la_data[$i]['denominacion'],$lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data[$i]['presupuesto'],$lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data[$i]['modificado_mes'],$lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data[$i]['presupuesto_modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data[$i]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data[$i]['compromiso'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data[$i]['causado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data[$i]['pagado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data[$i]['variacion'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data[$i]['programado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data[$i]['compromiso_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data[$i]['causado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 13, $la_data[$i]['pagado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 14, $la_data[$i]['variacion_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 15, $la_data[$i]['disponibilidad_compromiso'],$lo_dataright);
			$lo_hoja->write($li_row, 16, $la_data[$i]['disponibilidad_causado'],$lo_dataright);
			$li_row++;
		}
		$li_row++;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_totales,$z)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barragán
		// Fecha Creación: 26/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_row,$lo_titulo;
			$li_row++;$li_row++;
		
			$lo_hoja->write($li_row, 1, $la_data_totales[$z]['total'],$lo_datacenter);
			$lo_hoja->write($li_row, 2, $la_data_totales[$z]['presupuesto'],$lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_totales[$z]['modificado_mes'],$lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_totales[$z]['presupuesto_modificado'],$lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_totales[$z]['programado'],$lo_dataright);
			$lo_hoja->write($li_row, 6, $la_data_totales[$z]['compromiso'],$lo_dataright);
			$lo_hoja->write($li_row, 7, $la_data_totales[$z]['causado'],$lo_dataright);
			$lo_hoja->write($li_row, 8, $la_data_totales[$z]['pagado'],$lo_dataright);
			$lo_hoja->write($li_row, 9, $la_data_totales[$z]['variacion'],$lo_dataright);
			$lo_hoja->write($li_row, 10, $la_data_totales[$z]['programado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 11, $la_data_totales[$z]['compromiso_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 12, $la_data_totales[$z]['causado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 13, $la_data_totales[$z]['pagado_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 14, $la_data_totales[$z]['variacion_acumulado'],$lo_dataright);
			$lo_hoja->write($li_row, 15, $la_data_totales[$z]['disponibilidad_compromiso'],$lo_dataright);
			$lo_hoja->write($li_row, 16, $la_data_totales[$z]['disponibilidad_causado'],$lo_dataright);

	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones = new class_funciones();	
	require_once("sigesp_spg_funciones_reportes.php");
        $io_function_report = new sigesp_spg_funciones_reportes();	
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha = new class_fecha();
	require_once("sigesp_spg_class_reportes_instructivos.php");
	$io_report = new sigesp_spg_class_reportes_instructivos();
	//-----------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------
	//para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spi_ejecucion_mensual_presupuesto_de_recursos_0203.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	$li_row = 1;
	//---------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];

		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro1_aux=$ls_codestpro1_min;		
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro4_min  = $_GET["codestpro4"];
		$ls_codestpro5_min  = $_GET["codestpro5"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
		$ls_codestpro4h_max = $_GET["codestpro4h"];
		$ls_codestpro5h_max = $_GET["codestpro5h"];
		$ls_estclades       = $_GET["estclades"];
	        $ls_estclahas       = $_GET["estclahas"];
		$ls_tipoformato=1;
		if($li_estmodest==1)
		{
			$ls_codestpro4_min = "0000000000000000000000000";
			$ls_codestpro5_min = "0000000000000000000000000";
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
		    $ls_codestpro4_min = $_GET["codestpro4"];
			$ls_codestpro5_min = $_GET["codestpro5"];
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
			}else
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
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
                $ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
                $ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
                $ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
                $ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
                $ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
                
		$as_nombre["D"]["1"]="";
		$as_nombre["D"]["2"]="";
		$as_nombre["D"]["3"]="";
		$as_nombre["H"]["1"]="";
		$as_nombre["H"]["2"]="";
		$as_nombre["H"]["3"]="";
		$as_denestpro1="";
		$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$as_denestpro1,$ls_estclades);
		$as_denestpro1=$arrResultado['as_denestpro1'];
		$lb_valido=$arrResultado['lb_valido'];
		$as_nombre["D"]["1"]=substr($ls_codestpro1,-$ls_loncodestpro1)." ".$as_denestpro1;
		if($ls_codestpro1h!="")
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1h,$as_denestpro1,$ls_estclahas);
			$as_denestpro1=$arrResultado['as_denestpro1'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["1"]=substr($ls_codestpro1h,-$ls_loncodestpro1)." ".$as_denestpro1;
		}
		if($ls_codestpro2!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["2"]=substr($ls_codestpro2,-$ls_loncodestpro2)." ".$as_denestpro;
		}
		if($ls_codestpro2h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1h,$ls_codestpro2h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["2"]=substr($ls_codestpro2h,-$ls_loncodestpro2)." ".$as_denestpro;
		}
		if($ls_codestpro3!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["3"]=substr($ls_codestpro3,-$ls_loncodestpro3)." ".$as_denestpro;
		}
		if($ls_codestpro3h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["3"]=substr($ls_codestpro3h,-$ls_loncodestpro3)." ".$as_denestpro;
		}
		if($li_estmodest==2)
		{
			$as_nombre["D"]["4"]="";
			$as_nombre["D"]["5"]="";
			$as_nombre["H"]["4"]="";
			$as_nombre["H"]["5"]="";
			if($ls_codestpro4!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$as_denestpro,$ls_estclades);
				$as_denestpro=$arrResultado['as_denestpro4'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["D"]["4"]=substr($ls_codestpro4,-$ls_loncodestpro4)." ".$as_denestpro;
			}
			if($ls_codestpro4h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro4'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["4"]=substr($ls_codestpro4h,-$ls_loncodestpro4)." ".$as_denestpro;
			}
			if($ls_codestpro5!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$as_denestpro,$ls_estclades);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["D"]["5"]=substr($ls_codestpro5,-$ls_loncodestpro5)." ".$as_denestpro;
			}
			if($ls_codestpro5h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["5"]=substr($ls_codestpro5h,-$ls_loncodestpro5)." ".$as_denestpro;
			}
		}
                
		$li_mes=$_GET["cmbmes"];
		$ldt_ult_dia=$io_fecha->uf_last_day($li_mes,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
                $ldt_fecdes=$li_ano."-".$li_mes."-01";
		$ls_mes=$io_fecha->uf_load_nombre_mes($li_mes);
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo="EJECUCION MENSUAL DEL PRESUPUESTO DE EGRESOS POR PROYECTOS, ACCIONES CENTRALIZADAS E INSTITUCIONAL";       
	//--------------------------------------------------------------------------------------------------------------------------------
       
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
     $lb_valido=$io_report->uf_spg_reporte_de_ejecucion_mensual_recursos($ls_codestpro1,$ls_codestpro2,
	                                                             $ls_codestpro3,$ls_codestpro4,
																 $ls_codestpro5,$ls_codestpro1h,
																 $ls_codestpro2h,$ls_codestpro3h,
															     $ls_codestpro4h,$ls_codestpro5h,
																 $ldt_fecdes,$ldt_fechas,
																 $ls_codfuefindes,$ls_codfuefinhas,
																 $ls_estclades,$ls_estclahas);
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		//--------------------------------------------------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('left');
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
		$lo_hoja->set_column(0,0,10);
		$lo_hoja->set_column(1,1,70);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,25);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,25);
		$lo_hoja->set_column(7,7,25);
		$lo_hoja->set_column(8,8,25);
		$lo_hoja->set_column(9,9,25);
		$lo_hoja->set_column(10,10,25);
		$lo_hoja->set_column(11,11,25);
		$lo_hoja->set_column(12,12,25);
		$lo_hoja->set_column(13,13,25);
		$lo_hoja->set_column(14,14,25);
		$lo_hoja->set_column(15,15,25);
		$lo_hoja->set_column(16,16,25);

		$li_row = 4;
		//--------------------------------------------------------------------------------------------------
				
		uf_print_encabezado_pagina($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$ls_titulo,'(En Bolivares Fuertes)'); // Imprimimos el encabezado de la página
				
		$li_total=$io_report->dts_reporte->getRowCount("spg_cuenta");
            $ld_asignado_total=0;
            $ld_modificacion_mes_total=0;
            $ld_presupuesto_modificado_total=0;
            $ld_programado_total=0;
            $ld_compromiso_total=0;
            $ld_causado_total=0;
            $ld_pagado_total=0;
            $ld_variacion_total=0;
            $ld_programado_acumulado_total=0;
            $ld_compromiso_acum_total=0;
            $ld_causado_acum_total=0;
            $ld_pagado_acum_total=0;
	    $ld_disponible_total=0;
	    $ld_disponible_causado_total=0;
            $ld_variacion_acum_total=0;
	    for($z=1;$z<=$li_total;$z++)
	    {
			  $thisPageNum=$io_pdf->ezPageCount;
                          $ls_status=trim($io_report->dts_reporte->data["status"][$z]);                          
			  $ls_spg_cuenta=" ".trim($io_report->dts_reporte->data["spg_cuenta"][$z])." ";
			  $ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			  $ld_asignado=$io_report->dts_reporte->data["asignado"][$z];
			  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
			  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
			  $ld_programado=$io_report->dts_reporte->data["programado"][$z];
			  $ld_compromiso=$io_report->dts_reporte->data["compromiso"][$z];
			  $ld_causado=$io_report->dts_reporte->data["causado"][$z];
			  $ld_pagado=$io_report->dts_reporte->data["pagado"][$z];
			  $ld_programado_acum=$io_report->dts_reporte->data["programado_acum"][$z];
			  $ld_compromiso_acum=$io_report->dts_reporte->data["compromiso_acum"][$z];
			  $ld_causado_acum=$io_report->dts_reporte->data["causado_acum"][$z];
			  $ld_pagado_acum=$io_report->dts_reporte->data["pagado_acum"][$z];
			  $ld_aumento_acum=$io_report->dts_reporte->data["aumento_acum"][$z];
			  $ld_disminucion_acum=$io_report->dts_reporte->data["disminucion_acum"][$z];
			  $ld_disponible=$io_report->dts_reporte->data["disponible_fecha"][$z];
			  $ld_disponible_causado=$io_report->dts_reporte->data["disponible_causado"][$z];
			  $ld_variacion_acum=0;
                                  
                          $ld_programado=$ld_programado+$ld_aumento-$ld_disminucion;
                          $ld_programado_acum=$ld_programado_acum+$ld_aumento_acum-$ld_disminucion_acum;
                          if ($ls_status == 'C')
                          {
                            $ld_asignado_total=$ld_asignado_total+$ld_asignado;
                            $ld_modificacion_mes_total=$ld_modificacion_mes_total+$ld_aumento-$ld_disminucion;
                            $ld_presupuesto_modificado_total=$ld_presupuesto_modificado_total+$ld_asignado+$ld_aumento_acum-$ld_disminucion_acum;
                            $ld_programado_total=$ld_programado_total+$ld_programado;
                            $ld_compromiso_total=$ld_compromiso_total+$ld_compromiso;
                            $ld_causado_total=$ld_causado_total+$ld_causado;
                            $ld_pagado_total=$ld_pagado_total+$ld_pagado;
                            $ld_variacion_total=$ld_variacion_total+$ld_causado-$ld_programado;
                            $ld_programado_acumulado_total=$ld_programado_acumulado_total+$ld_programado_acum;
                            $ld_compromiso_acum_total=$ld_compromiso_acum_total+$ld_compromiso_acum;
                            $ld_causado_acum_total=$ld_causado_acum_total+$ld_causado_acum;
                            $ld_pagado_acum_total=$ld_pagado_acum_total+$ld_pagado_acum;
                            $ld_variacion_acum_total=$ld_variacion_acum_total+$ld_causado_acum-$ld_programado_acum;
                            $ld_disponible_total=$ld_disponible_total+$ld_disponible;
                            $ld_disponible_causado_total=$ld_disponible_causado_total+$ld_disponible_causado;
                          }
			  $ld_presupuesto_modificado=number_format($ld_asignado+$ld_aumento_acum-$ld_disminucion_acum,2,",",".");
                          $ld_variacion=number_format(($ld_causado-$ld_programado),2,",",".");
                          $ld_variacion_acum=number_format($ld_causado_acum-$ld_programado_acum,2,",",".");
			  $ld_asignado=number_format($ld_asignado,2,",",".");
                          $ld_modificacion_mes=number_format(($ld_aumento-$ld_disminucion),2,",",".");
			  $ld_programado=number_format(($ld_programado),2,",",".");
			  $ld_compromiso=number_format($ld_compromiso,2,",",".");
			  $ld_causado=number_format($ld_causado,2,",",".");
			  $ld_pagado=number_format($ld_pagado,2,",",".");
			  $ld_programado_acum=number_format($ld_programado_acum,2,",",".");
			  $ld_compromiso_acum=number_format($ld_compromiso_acum,2,",",".");
			  $ld_causado_acum=number_format($ld_causado_acum,2,",",".");
			  $ld_pagado_acum=number_format($ld_pagado_acum,2,",",".");
			  $ld_disponible=number_format($ld_disponible,2,",",".");
			  $ld_disponible_causado=number_format($ld_disponible_causado,2,",",".");
                          
			  $la_data[$z]=array('cuenta'=>" ".$ls_spg_cuenta." ",
			                     'denominacion'=>$ls_denominacion,
			                     'presupuesto'=>$ld_asignado,
			                     'modificado_mes'=>$ld_modificacion_mes,
			                     'presupuesto_modificado'=>$ld_presupuesto_modificado,
                                             'programado'=>$ld_programado,
					     'compromiso'=>$ld_compromiso,
					     'causado'=>$ld_causado,
					     'pagado'=>$ld_pagado,
                                             'variacion'=>$ld_variacion,
					     'programado_acumulado'=>$ld_programado_acum,
					     'compromiso_acumulado'=>$ld_compromiso_acum,
					     'causado_acumulado'=>$ld_causado_acum,
				             'pagado_acumulado'=>$ld_pagado_acum,
				             'variacion_acumulado'=>$ld_variacion_acum,
				             'disponibilidad_compromiso'=>$ld_disponible,
					     'disponibilidad_causado'=>$ld_disponible_causado);
		}
		
            $ld_asignado_total=number_format($ld_asignado_total,2,",",".");
            $ld_modificacion_mes_total=number_format($ld_modificacion_mes_total,2,",",".");
            $ld_presupuesto_modificado_total=number_format($ld_presupuesto_modificado_total,2,",",".");
            $ld_programado_total=number_format($ld_programado_total,2,",",".");
            $ld_compromiso_total=number_format($ld_compromiso_total,2,",",".");
            $ld_causado_total=number_format($ld_causado_total,2,",",".");
            $ld_pagado_total=number_format($ld_pagado_total,2,",",".");
            $ld_variacion_total=number_format($ld_variacion_total,2,",",".");
            $ld_programado_acumulado_total=number_format($ld_programado_acumulado_total,2,",",".");
            $ld_compromiso_acum_total=number_format($ld_compromiso_acum_total,2,",",".");
            $ld_causado_acum_total=number_format($ld_causado_acum_total,2,",",".");
            $ld_pagado_acum_total=number_format($ld_pagado_acum_total,2,",",".");
            $ld_variacion_acum_total=number_format($ld_variacion_acum_total,2,",",".");
            $ld_disponible_total=number_format($ld_disponible_total,2,",",".");
            $ld_disponible_causado_total=number_format($ld_disponible_causado_total,2,",",".");

	    $la_data_totales[$z]=array('total'=>'TOTALES Bs.',
		                           'presupuesto'=>$ld_asignado_total,
		                           'modificado_mes'=>$ld_modificacion_mes_total,
		                           'presupuesto_modificado'=>$ld_presupuesto_modificado_total,
					   'programado'=>$ld_programado_total,
				           'compromiso'=>$ld_compromiso_total,
				           'causado'=>$ld_causado_total,
					   'pagado'=>$ld_pagado_total,
                                           'variacion'=>$ld_variacion_total,
					   'programado_acumulado'=>$ld_programado_acumulado_total,
					   'compromiso_acumulado'=>$ld_compromiso_acum_total,
					   'causado_acumulado'=>$ld_causado_acum_total,
					   'pagado_acumulado'=>$ld_pagado_acum_total,
					   'variacion_acumulado'=>$ld_variacion_acum_total,
					   'disponibilidad_compromiso'=>$ld_disponible_total,
					   'disponibilidad_causado'=>$ld_disponible_causado_total);
		
		uf_print_titulo_reporte($li_row,$lo_titulo,$fechas,$ls_mes,$as_nombre,$lo_hoja,$lo_encabezado,$io_encabezado);
		uf_print_titulo($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$io_titulo);
		uf_print_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_encabezado,$io_cabecera);
		uf_print_detalle($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data); // Imprimimos el detalle 
		uf_print_pie_cabecera($li_row,$lo_titulo,$lo_hoja,$lo_datacenter,$lo_dataleft,$lo_dataright,$la_data_totales,$z);
		
		unset($la_data);
		unset($la_data_totales);
		
		if (isset($d) && $d)
		{
			//$ls_pdfcode = $io_pdf->ezOutput(1);
			//$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
			//echo '<html><body>';
			//echo trim($ls_pdfcode);
			//echo '</body></html>';
		}
		else
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"sigesp_spg_ejecucion_mensual_0204_excel.xls\"");
			header("Content-Disposition: inline; filename=\"sigesp_spg_ejecucion_mensual_0204_excel.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
		}
		
	}//else
	unset($io_report);
	unset($io_funciones);
	
?> 