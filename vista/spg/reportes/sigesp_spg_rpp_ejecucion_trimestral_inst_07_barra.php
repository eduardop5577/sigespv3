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
///------------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report=new sigesp_spg_funciones_reportes();	
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../../../shared/graficos/pChart/pData.class");
		require_once("../../../shared/graficos/pChart/pChart.class");
//-----------------------------------------------------------------------------------------------------------------------------
		global $la_data_tot;
		require_once("sigesp_spg_class_reportes_instructivos.php");
		$io_report = new sigesp_spg_class_reportes_instructivos();
		
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
		$as_nombre["D"]["1"]=$as_denestpro1;
		if($ls_codestpro1h!="")
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1h,$as_denestpro1,$ls_estclahas);
			$as_denestpro1=$arrResultado['as_denestpro1'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["1"]=$as_denestpro1;
		}
		if($ls_codestpro2!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["2"]=$as_denestpro;
		}
		if($ls_codestpro2h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1h,$ls_codestpro2h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro2'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["2"]=$as_denestpro;
		}
		if($ls_codestpro3!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$as_denestpro,$ls_estclades);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["D"]["3"]=$as_denestpro;
		}
		if($ls_codestpro3h!="")
		{
			$as_denestpro="";
			$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$as_denestpro,$ls_estclahas);
		    $as_denestpro=$arrResultado['as_denestpro3'];
			$lb_valido=$arrResultado['lb_valido'];
			$as_nombre["H"]["3"]=$as_denestpro;
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
				$as_nombre["D"]["4"]=$as_denestpro;
			}
			if($ls_codestpro4h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro4'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["4"]=$as_denestpro;
			}
			if($ls_codestpro5!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$as_denestpro,$ls_estclades);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["D"]["5"]=$as_denestpro;
			}
			if($ls_codestpro5h!="")
			{
				$as_denestpro="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,$as_denestpro,$ls_estclahas);
				$as_denestpro=$arrResultado['as_denestpro5'];
				$lb_valido=$arrResultado['lb_valido'];
				$as_nombre["H"]["5"]=$as_denestpro;
			}
		}
		$ls_cmbmes=$_GET["cmbmes"];
		switch($ls_cmbmes)
		{
		 case '0103': $ls_trimestre = "01";
		 break;
		 
		 case '0406': $ls_trimestre = "02";
		 break;
		 
		 case '0709': $ls_trimestre = "03";
		 break;
		 
		 case '1012': $ls_trimestre = "04";
		 break;
		
		}
		$li_mesdes=substr($ls_cmbmes,0,2);
		$ldt_fecdes=$li_ano."-".$li_mesdes."-01";
		$li_meshas=substr($ls_cmbmes,2,2);
		$ldt_ult_dia=$io_fecha->uf_last_day($li_meshas,$li_ano);
		$fechas=$ldt_ult_dia;
		$ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ls_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes);
		$ls_meshas=$io_fecha->uf_load_nombre_mes($li_meshas);
		$ls_codfuefindes=$_GET["txtcodfuefindes"];
	    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
	    if (($ls_codfuefindes=='')&&($ls_codfuefindes==''))
	    {
			$arrResultado=$io_function_report->uf_spg_select_fuentefinanciamiento($ls_minfuefin,$ls_maxfuefin);
			$ls_minfuefin=$arrResultado['as_minfuefin'];
			$ls_maxfuefin=$arrResultado['as_maxfuefin'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
		     $ls_codfuefindes=$ls_minfuefin;
		     $ls_codfuefinhas=$ls_maxfuefin;
		  } 
	    }		
		
//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo="EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS";       
//--------------------------------------------------------------------------------------------------------------------------------
      //$lb_valido=true;
     $lb_valido=$io_report->uf_spg_reportes_ejecucion_trimestral($ls_codestpro1,$ls_codestpro2,
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
		//print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	    
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
 	    $io_pdf->ezStartPageNumbers(980,10,10,'','',1); // Insertar el número de página
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$thisPageNum=$io_pdf->ezPageCount;
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
	    $ld_total_asignado=0;
		$ld_total_modificado=0;
		$ld_total_programado=0;
		$ld_total_compromiso=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_programado_acum=0;
		$ld_total_compromiso_acum=0;
		$ld_total_causado_acum=0;
		$ld_total_pagado_acum=0;
		$ld_total_disp_fecha=0;	
		
		///-------------------------------
		$ld_asignado_partida=0;
		$ld_modificado_partida=0;
		$ld_programado_partida=0;
		$ld_compromiso_partida=0;
		$ld_causado_partida=0;
		$ld_pagado_partida=0;
		$ld_programado_acum_partida=0;
		$ld_compromiso_acum_partida=0;
		$ld_causado_acum_partida=0;
		$ld_pagado_acum_partida=0;
		$ld_disp_fecha_partida=0;		
		//--------------------------------	
		
		
		$ls_partida_aux="";	
		for($z=1;$z<=$li_tot;$z++)
		{		
			$ld_asignado=0;
			$ld_modificado=0;
			$ld_programado=0;
			$ld_compromiso=0;
			$ld_causado=0;
			$ld_pagado=0;
			$ld_programado_acum=0;
			$ld_compromiso_acum=0;
			$ld_causado_acum=0;
			$ld_pagado_acum=0;
			$ld_disp_fecha=0;
			$ls_partida="";
			$ls_generica="";
			$ls_especifica="";
			$ls_subesp="";
			$ls_status="";

				  $ls_spg_cuenta             = trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
				  $arrResultado=$io_function_report->uf_get_spg_cuenta($ls_spg_cuenta,$ls_partida,$ls_generica,$ls_especifica,$ls_subesp);
				  $ls_partida=$arrResultado['as_spg_partida'];
				  $ls_generica=$arrResultado['as_spg_generica'];
				  $ls_especifica=$arrResultado['as_spg_especifica'];
				  $ls_subesp=$arrResultado['as_spg_subesp'];
				  $ls_denominacion           = trim($io_report->dts_reporte->data["denominacion"][$z]);
				  $ld_asignado               = $io_report->dts_reporte->data["asignado"][$z];
				  $ld_modificado             = $io_report->dts_reporte->data["modificado"][$z];
				  $ld_programado             = $io_report->dts_reporte->data["programado"][$z];
				  $ld_compromiso             = $io_report->dts_reporte->data["compromiso"][$z];
				  $ld_causado                = $io_report->dts_reporte->data["causado"][$z];
				  $ld_pagado                 = $io_report->dts_reporte->data["pagado"][$z];
				  $ld_programado_acum        = $io_report->dts_reporte->data["programado_acum"][$z];
				  $ld_compromiso_acum        = $io_report->dts_reporte->data["compromiso_acum"][$z];
				  $ld_causado_acum           = $io_report->dts_reporte->data["causado_acum"][$z];
				  $ld_pagado_acum            = $io_report->dts_reporte->data["pagado_acum"][$z];
				  $ld_disp_fecha             = $io_report->dts_reporte->data["disponible_fecha"][$z];
				  $ls_status                 = $io_report->dts_reporte->data["status"][$z];
				  
				  if($ls_status == "C")
				  {
				   $ld_total_asignado         = $ld_total_asignado + $ld_asignado;
				   $ld_total_modificado       = $ld_total_modificado + $ld_modificado;
		           $ld_total_programado       = $ld_total_programado + $ld_programado;
		           $ld_total_compromiso       = $ld_total_compromiso + $ld_compromiso;
		           $ld_total_causado          = $ld_total_causado + $ld_causado;
		           $ld_total_pagado           = $ld_total_pagado + $ld_pagado;
		           $ld_total_programado_acum  = $ld_total_programado_acum + $ld_programado_acum;
		           $ld_total_compromiso_acum  = $ld_total_compromiso_acum + $ld_compromiso_acum;
		           $ld_total_causado_acum     = $ld_total_causado_acum + $ld_causado_acum;
		           $ld_total_pagado_acum      = $ld_total_pagado_acum + $ld_pagado_acum;
		           $ld_total_disp_fecha       = $ld_total_disp_fecha + $ld_disp_fecha;
				  }
				  
				  ///-------------------agrupar por partida---------------------------------------
				  if ($ls_partida_aux=="")
				  {
				 		$ls_partida_aux=$ls_partida; 
				  }
				  elseif ($ls_partida_aux==$ls_partida)
				  {
				  	
					 if($ls_status=="C")
					 {
						 $ls_partida_aux=$ls_partida;
						 $ld_asignado_partida=$ld_asignado_partida+$ld_asignado;
						 $ld_modificado_partida=$ld_modificado_partida+$ld_modificado;
						 $ld_programado_partida=$ld_programado_partida+$ld_programado;
						 $ld_compromiso_partida=$ld_compromiso_partida+$ld_compromiso;
						 $ld_causado_partida=$ld_causado_partida+$ld_causado;
						 $ld_pagado_partida=$ld_pagado_partida+$ld_pagado;
						 $ld_programado_acum_partida=$ld_programado_acum_partida+$ld_programado_acum;
						 $ld_compromiso_acum_partida=$ld_compromiso_acum_partida+$ld_compromiso_acum;
						 $ld_causado_acum_partida=$ld_causado_acum_partida+$ld_causado_acum;
						 $ld_pagado_acum_partida=$ld_pagado_acum_partida+$ld_pagado_acum;
						 $ld_disp_fecha_partida=$ld_disp_fecha_partida+$ld_disp_fecha;	
					}			 
				  }
				  else
				  {
				  	 $ld_asignado_partida       = number_format($ld_asignado_partida,2,",",".");
				     $ld_modificado_partida     = number_format($ld_modificado_partida,2,",",".");
				     $ld_programado_partida     = number_format($ld_programado_partida,2,",",".");
				     $ld_compromiso_partida     = number_format($ld_compromiso_partida,2,",",".");
				     $ld_causado_partida        = number_format($ld_causado_partida,2,",",".");
				     $ld_pagado_partida         = number_format($ld_pagado_partida,2,",",".");
				     $ld_programado_acum_partida  = number_format($ld_programado_acum_partida,2,",",".");
				     $ld_compromiso_acum_partida  = number_format($ld_compromiso_acum_partida,2,",",".");
				     $ld_causado_acum_partida     = number_format($ld_causado_acum_partida,2,",",".");
				     $ld_pagado_acum_partida      = number_format($ld_pagado_acum_partida,2,",",".");
				     $ld_disp_fecha_partida       = number_format($ld_disp_fecha_partida,2,",",".");
				
					if($ld_asignado_partida == $ld_modificado_partida)
					{
					  $ld_modificado_partida = '';
					}
					 $ld_asignado_partida=0;
					 $ld_modificado_partida=0;
					 $ld_programado_partida=0;
					 $ld_compromiso_partida=0;
					 $ld_causado_partida=0;
					 $ld_pagado_partida=0;
					 $ld_programado_acum_partida=0;
					 $ld_compromiso_acum_partida=0;
					 $ld_causado_acum_partida=0;
					 $ld_pagado_acum_partida=0;
					 $ld_disp_fecha_partida=0;		
					 $ls_partida_aux=$ls_partida;
				  }
				 
				  //------------------------------------------------------------------------------
				  $ld_asignado               = number_format($ld_asignado,2,",",".");
				  $ld_modificado             = number_format($ld_modificado,2,",",".");
				  $ld_programado             = number_format($ld_programado,2,",",".");
				  $ld_compromiso             = number_format($ld_compromiso,2,",",".");
				  $ld_causado                = number_format($ld_causado,2,",",".");
				  $ld_pagado                 = number_format($ld_pagado,2,",",".");
				  $ld_programado_acum        = number_format($ld_programado_acum,2,",",".");
				  $ld_compromiso_acum        = number_format($ld_compromiso_acum,2,",",".");
				  $ld_causado_acum           = number_format($ld_causado_acum,2,",",".");
				  $ld_pagado_acum            = number_format($ld_pagado_acum,2,",",".");
				  $ld_disp_fecha             = number_format($ld_disp_fecha,2,",",".");
				  
				   if($ld_modificado == $ld_asignado)
					{
					  $ld_modificado = '';
					} 
					  							 						   
			}//for
		
		
		//----------------------------------------------------------------------------------------------	
		 $DataSet = new pData;
		 $DataSet->AddPoint(array($ld_total_programado),"Serie0");
		 $DataSet->AddPoint(array($ld_total_compromiso),"Serie1");
		 $DataSet->AddPoint(array($ld_total_causado),"Serie2");
		 $DataSet->AddPoint(array($ld_total_pagado),"Serie3");
		 $DataSet->AddPoint(array(""),"titulos");
		 $DataSet->AddSerie("Serie0");
		 $DataSet->AddSerie("Serie1");
		 $DataSet->AddSerie("Serie2");
		 $DataSet->AddSerie("Serie3");
		 $DataSet->SetSerieName("Programado","Serie0");
		 $DataSet->SetSerieName("Compromiso","Serie1");
		 $DataSet->SetSerieName("Causado","Serie2");
		 $DataSet->SetSerieName("Pagado","Serie3");
		 $DataSet->SetAbsciseLabelSerie("titulos");
		
		 // Initialise the graph
		 $Test = new pChart(700,230);
		 $Test->setFontProperties("../../../shared/graficos/Fonts/tahoma.ttf",8);
		 $Test->setGraphArea(90,30,580,200);
		 $Test->drawFilledRoundedRectangle(7,7,593,223,5,240,240,240);
		 $Test->drawRoundedRectangle(5,5,595,225,5,230,230,230);
		 $Test->drawGraphArea(255,255,255,TRUE);
		 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
		 $Test->drawGrid(4,TRUE,230,230,230,50);
		
		 // Draw the 0 line
		 $Test->setFontProperties("../../../shared/graficos/Fonts/tahoma.ttf",6);
		 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);
		
		 // Draw the bar graph
		 $Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,80);
		
		
		 // Finish the graph
		 $Test->setFontProperties("../../../shared/graficos/Fonts/tahoma.ttf",8);
		 $Test->drawLegend(596,50,$DataSet->GetDataDescription(),255,255,255);
		 $Test->setFontProperties("../../../shared/graficos/Fonts/tahoma.ttf",10);
		 $Test->drawTitle(50,22,$ls_titulo,50,50,50,585);
		
		 $Test->Render("comparadobarra.png");
				 
		unset($io_pdf);
	}//else
	unset($io_report);
	unset($io_funciones);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS</title>
<link href="../../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/report.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/tablas.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="498" border="0" align="center">
  <tr>
    <td width="320" class="sin-borde2"><div align="center" class="titulo-celdanew">EJECUCION TRIMESTRAL DE GASTOS Y APLICACIONES FINANCIERAS</div></td>
  </tr>
  <tr>
    <td width="320"><img src="comparadobarra.png" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">
<a href="javascript:ue_print();"><img src="../../shared/imagebank/tools20/print.gif" width="35" height="30" border="0"/></div></a>
	</td>
  </tr>
  <tr>
    <td width="320"></td>
  </tr>
</table>


</body>
<script language="JavaScript">
function ue_print()
{
	window.print();
}
</script>
</html>

