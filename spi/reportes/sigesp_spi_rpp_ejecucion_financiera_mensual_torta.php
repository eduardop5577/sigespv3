<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');
//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spi_reporte.php");
		$io_report = new sigesp_spi_reporte();
		require_once("sigesp_spi_funciones_reportes.php");
		$io_function_report = new sigesp_spi_funciones_reportes();
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../class_funciones_ingreso.php");
		$io_fun_ingreso=new class_funciones_ingreso();			
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../../shared/graficos/pChart/pData.class");
		require_once("../../shared/graficos/pChart/pChart.class");
//--------------------------------------------------  Parámetros para Filtar el Reporte  ---------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-".$ls_cmbmesdes."-01";
		$ldt_fecini_rep="01"."/".$ls_cmbmesdes."/".$li_ano;
		$ls_mes=$ls_cmbmesdes;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	    $ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		
		if($ls_estpreing==1)
		{
			$ls_codestpro1_min  = $_GET["codestpro1"];
			$ls_codestpro2_min  = $_GET["codestpro2"];
			$ls_codestpro3_min  = $_GET["codestpro3"];
			$ls_codestpro1h_max = $_GET["codestpro1h"];
			$ls_codestpro2h_max = $_GET["codestpro2h"];
			$ls_codestpro3h_max = $_GET["codestpro3h"];
			$ls_estclades       = $_GET["estclades"];
			$ls_estclahas       = $_GET["estclahas"];
			$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
			$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
			$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
			$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
			$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
	
			if($ls_modalidad==1)
			{
				$ls_codestpro4_min =  "0000000000000000000000000";
				$ls_codestpro5_min =  "0000000000000000000000000";
				$ls_codestpro4h_max = "0000000000000000000000000";
				$ls_codestpro5h_max = "0000000000000000000000000";
				if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min==""))
				{
				 $arrResultado = $io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);
				 $ls_codestpro1_min = $arrResultado['as_codestpro1'];
				 $ls_codestpro2_min = $arrResultado['as_codestpro2'];
				 $ls_codestpro3_min = $arrResultado['as_codestpro3'];
				 $ls_codestpro4_min = $arrResultado['as_codestpro4'];
				 $ls_codestpro5_min = $arrResultado['as_codestpro5'];
				 $ls_estclades = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,$ls_codestpro3h_max,$ls_codestpro4h_max,$ls_codestpro5h_max,$ls_estclahas);
				 $ls_codestpro1h_max = $arrResultado['as_codestpro1'];
				 $ls_codestpro2h_max = $arrResultado['as_codestpro2'];
				 $ls_codestpro3h_max = $arrResultado['as_codestpro3'];
				 $ls_codestpro4h_max = $arrResultado['as_codestpro4'];
				 $ls_codestpro5h_max = $arrResultado['as_codestpro5'];
				 $ls_estclahas = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
			elseif($ls_modalidad==2)
			{
				$ls_codestpro4_min  = $_GET["codestpro4"];
				$ls_codestpro5_min  = $_GET["codestpro5"];
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
				}
				else
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_min_programatica($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,$ls_codestpro4_min,$ls_codestpro5_min,$ls_estclades);
				 $ls_codestpro1_min = $arrResultado['as_codestpro1'];
				 $ls_codestpro2_min = $arrResultado['as_codestpro2'];
				 $ls_codestpro3_min = $arrResultado['as_codestpro3'];
				 $ls_codestpro4_min = $arrResultado['as_codestpro4'];
				 $ls_codestpro5_min = $arrResultado['as_codestpro5'];
				 $ls_estclades = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
				 $arrResultado = $io_function_report->uf_spi_reporte_select_max_programatica($ls_codestpro1h_max,$ls_codestpro2h_max,$ls_codestpro3h_max,$ls_codestpro4h_max,$ls_codestpro5h_max,$ls_estclahas);
				 $ls_codestpro1h_max = $arrResultado['as_codestpro1'];
				 $ls_codestpro2h_max = $arrResultado['as_codestpro2'];
				 $ls_codestpro3h_max = $arrResultado['as_codestpro3'];
				 $ls_codestpro4h_max = $arrResultado['as_codestpro4'];
				 $ls_codestpro5h_max = $arrResultado['as_codestpro5'];
				 $ls_estclahas = $arrResultado['as_estclahas'];
				 $lb_valido = $arrResultado['lb_valido'];
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
			
			$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
			if($ls_modalidad==1)
			{
				if (($ls_codestpro1<>"")&&($ls_codestpro2=="")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3==""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2);
				}
				elseif(($ls_codestpro1<>"")&&($ls_codestpro2<>"")&&($ls_codestpro3<>""))
				{
				 $ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3);
				 $ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3);
				}
				else
				{
				 $ls_programatica_desde1="";
				 $ls_programatica_hasta1="";
				}
			}
			else
			{
				$ls_programatica_desde1=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5)."-".$ls_estclades;
				$ls_programatica_hasta1=substr($ls_codestpro1h,-$ls_loncodestpro1)."-".substr($ls_codestpro2h,-$ls_loncodestpro2)."-".substr($ls_codestpro3h,-$ls_loncodestpro3)."-".substr($ls_codestpro4h,-$ls_loncodestpro4)."-".substr($ls_codestpro5h,-$ls_loncodestpro5)."-".$ls_estclahas;
			}
		}
		$cmbnivel=$_GET["cmbnivel"];
		if($cmbnivel=="s1")
		{
          $ls_cmbnivel="1";
		}
		else
		{
          $ls_cmbnivel=$cmbnivel;
		}
        
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Ejecucion Presupuestaria Mensual de Ingreso la fecha ".$ldt_fecini_rep." hasta ".$fecfin;
		$io_fun_ingreso->uf_load_seguridad_reporte("SPI","sigesp_spi_r_ejecucion_financiera_mensual.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
     //----------------------------------------------------  Parámetros del encabezado  --------------------------------------------
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$ls_titulo=" <b> EJECUCION PRESUPUESTARIA MENSUAL DE INGRESO  DESDE LA FECHA ".$ldt_fecini_rep."  HASTA  ".$fecfin." </b> ";
		if($ls_estpreing==1)
		{
	    	$ls_titulo1="<b> DESDE LA ESTRUCTURA PRESUPUESTARIA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1." </b>"; 
		}
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");            
    //--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
	if($ls_estpreing==1)
	{
		$ls_codestpro1  = $io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
		$ls_codestpro2  = $io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
		$ls_codestpro3  = $io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
		$ls_codestpro4  = $io_funciones->uf_cerosizquierda($ls_codestpro4_min,25);
		$ls_codestpro5  = $io_funciones->uf_cerosizquierda($ls_codestpro5_min,25);
			
		$ls_codestpro1h  = $io_funciones->uf_cerosizquierda($ls_codestpro1h_max,25);
		$ls_codestpro2h  = $io_funciones->uf_cerosizquierda($ls_codestpro2h_max,25);
		$ls_codestpro3h  = $io_funciones->uf_cerosizquierda($ls_codestpro3h_max,25);
		$ls_codestpro4h  = $io_funciones->uf_cerosizquierda($ls_codestpro4h_max,25);
		$ls_codestpro5h  = $io_funciones->uf_cerosizquierda($ls_codestpro5h_max,25);
		
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	}
	
	set_time_limit(1800);
	$ld_total_previsto=0;
	$ld_total_aumento=0;
	$ld_total_disminucion=0;
	$ld_total_devengado=0;
	$ld_total_cobrado=0;
	$ld_total_cobrado_anticipado=0;
	$ld_total_monto_actualizado=0;
	$ld_total_por_cobrar=0;
    if ($ls_estpreing==1)
	{
	    $lb_valido=$io_report->select_estructuras_spi($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
											        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
											        $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas);
		$li_totfila=$io_report->data_est->getRowCount("programatica");
		if ($li_totfila>0)
		{
		 for ($j=1;(($j<=$li_totfila)&&($lb_valido));$j++)
		  {
			  $ls_codestpro1=trim($io_report->data_est->data["codestpro1"][$j]); 
			  $ls_codestpro2=trim($io_report->data_est->data["codestpro2"][$j]);
			  $ls_codestpro3=trim($io_report->data_est->data["codestpro3"][$j]);
			  $ls_codestpro4=trim($io_report->data_est->data["codestpro4"][$j]);
			  $ls_codestpro5=trim($io_report->data_est->data["codestpro5"][$j]);
			  $ls_estcla=trim($io_report->data_est->data["estcla"][$j]);
			  $ls_estclades=trim($io_report->data_est->data["estcla"][$j]);
			  $ld_total_previsto=0;
			  $ld_total_aumento=0;
			  $ld_total_disminucion=0;
			  $ld_total_devengado=0;
			  $ld_total_cobrado=0;
			  $ld_total_cobrado_anticipado=0;
			  $ld_total_monto_actualizado=0;
			  $ld_total_por_cobrar=0;
			
			  $ls_codestpro1h=trim($io_report->data_est->data["codestpro1"][$j]);
			  $ls_codestpro2h=trim($io_report->data_est->data["codestpro2"][$j]);
			  $ls_codestpro3h=trim($io_report->data_est->data["codestpro3"][$j]);
			  $ls_codestpro4h=trim($io_report->data_est->data["codestpro4"][$j]);
			  $ls_codestpro5h=trim($io_report->data_est->data["codestpro5"][$j]);
			  $ls_estclahas=trim($io_report->data_est->data["estcla"][$j]); 
		      $lb_valido=$io_report->uf_spi_reporte_ejecucion_financiera_mensual_estructura($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                        $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas,$ldt_fecini,$ldt_fecfin,
															$ls_cmbnivel,$ls_modalidad);
			 $li_tot=0;
			 $li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");
			 for($z=1;$z<=$li_tot;$z++)
			 {
				  $ls_spi_cuenta=$io_report->dts_reporte->data["spi_cuenta"][$z];
				  $ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
				  $ls_nivel=$io_report->dts_reporte->data["nivel"][$z];
				  $ld_previsto=$io_report->dts_reporte->data["previsto"][$z];
				  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
				  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
				  $ld_devengado=$io_report->dts_reporte->data["devengado"][$z];
				  $ld_cobrado=$io_report->dts_reporte->data["cobrado"][$z];
				  $ld_cobrado_anticipado=$io_report->dts_reporte->data["cobrado_anticipado"][$z];
				  $ls_status=$io_report->dts_reporte->data["status"][$z];
				  $ld_monto_actualizado=$ld_previsto+$ld_aumento-$ld_disminucion-$ld_devengado;
				  $ld_por_cobrar=$ld_devengado-$ld_cobrado;
				 
				  if($ls_status=='C')
				  {
					  $ld_total_previsto=$ld_total_previsto+$ld_previsto;
					  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
					  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
					  $ld_total_devengado=$ld_total_devengado+$ld_devengado;
					  $ld_total_cobrado=$ld_total_cobrado+$ld_cobrado;
					  $ld_total_cobrado_anticipado=$ld_total_cobrado_anticipado+$ld_cobrado_anticipado;
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
					  $ld_total_por_cobrar=$ld_total_por_cobrar+$ld_por_cobrar;
				  } 
				  $ld_previsto=number_format($ld_previsto,2,",",".");
				  $ld_aumento=number_format($ld_aumento,2,",",".");
				  $ld_disminucion=number_format($ld_disminucion,2,",",".");
				  $ld_devengado=number_format($ld_devengado,2,",",".");
				  $ld_cobrado=number_format($ld_cobrado,2,",",".");
				  $ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
				  $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
				  $ld_por_cobrar=number_format($ld_por_cobrar,2,",",".");
				
				  $la_data1[$z]=array('cuenta'=>$ls_spi_cuenta,'denominacion'=>$ls_denominacion,'previsto'=>$ld_previsto,
									  'aumento'=>$ld_aumento,'disminución'=>$ld_disminucion,'devengado'=>$ld_devengado,
									  'cobrado'=>$ld_cobrado,'cobrado_anticipado'=>$ld_cobrado_anticipado,
									  'montoactualizado'=>$ld_monto_actualizado,'porcobrar'=>$ld_por_cobrar);
				 $ld_previsto=str_replace('.','',$ld_previsto);
				 $ld_previsto=str_replace(',','.',$ld_previsto);		
				 $ld_aumento=str_replace('.','',$ld_aumento);
				 $ld_aumento=str_replace(',','.',$ld_aumento);		
				 $ld_disminucion=str_replace('.','',$ld_disminucion);
				 $ld_disminucion=str_replace(',','.',$ld_disminucion);		
				 $ld_monto_actualizado=str_replace('.','',$ld_monto_actualizado);
				 $ld_monto_actualizado=str_replace(',','.',$ld_monto_actualizado);
				 $ld_devengado=str_replace('.','',$ld_devengado);
				 $ld_devengado=str_replace(',','.',$ld_devengado);		
				 $ld_cobrado=str_replace('.','',$ld_cobrado);
				 $ld_cobrado=str_replace(',','.',$ld_cobrado);		
				 $ld_cobrado_anticipado=str_replace('.','',$ld_cobrado_anticipado);
				 $ld_cobrado_anticipado=str_replace(',','.',$ld_cobrado_anticipado);		
				 $ld_por_cobrar=str_replace('.','',$ld_por_cobrar);
				 $ld_por_cobrar=str_replace(',','.',$ld_por_cobrar);
		
				if($z==$li_tot)
				{ 
				  
				 $DataSet = new pData;
				 $DataSet->AddPoint(array($ld_total_monto_actualizado,$ld_total_devengado,$ld_total_cobrado,$ld_total_por_cobrar),"Serie1");
				 $DataSet->AddPoint(array("Actualizado","Devengado","Cobrado","Por Cobrar"),"titulos");
				 $DataSet->AddAllSeries();
				 $DataSet->SetAbsciseLabelSerie("titulos");
				
			
				 // Initialise the graph
				 $Test = new pChart(500,400);
				 $Test->drawFilledRoundedRectangle(7,7,493,393,5,240,240,240);
				 $Test->drawRoundedRectangle(5,5,495,395,5,230,230,230);
				
				 // This will draw a shadow under the pie chart
				 $Test->drawFilledCircle(225,205,170,200,200,200);
				
				 // Draw the pie chart
				 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",8);
				 $Test->AntialiasQuality = 0;
				 $Test->drawBasicPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),220,200,170,PIE_PERCENTAGE,255,255,218);
				 $Test->drawPieLegend(380,15,$DataSet->GetData(),$DataSet->GetDataDescription(),150,150,150);
				
				 $Test->Render("ejecucionfinanciera.png");
				}//if
			}//for
			    $arrResultado=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);			
			    $ls_denestpro1 = $arrResultado['as_denestpro'];
	 		    $lb_valido= $arrResultado['lb_valido'];
				if($lb_valido)
				{
					$ls_denestpro1=$ls_denestpro1; 
				}			
				if($lb_valido)
				{
					  $ls_denestpro2="";
					  $arrResultado=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
					  $ls_denestpro2 = $arrResultado['as_denestpro'];
					  $lb_valido= $arrResultado['lb_valido'];
					  $ls_denestpro2=$ls_denestpro2;
				}
				if($lb_valido)
				{
					  $ls_denestpro3="";
					  $arrResultado=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
					  $ls_denestpro3 = $arrResultado['as_denestpro'];
					  $lb_valido= $arrResultado['lb_valido'];
					  $ls_denestpro3=$ls_denestpro3;
					  $ls_denestpro4="";
					  $ls_denestpro5="";
				} 
				if($ls_modalidad==2)
				{
					$ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $arrResultado=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4 = $arrResultado['as_denestpro'];
					  $lb_valido= $arrResultado['lb_valido'];
					  $ls_denestpro4=$ls_denestpro4;
					}
					$ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $arrResultado=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					  $ls_denestpro5 = $arrResultado['as_denestpro'];
					  $lb_valido= $arrResultado['lb_valido'];
					  $ls_denestpro5=$ls_denestpro5;
					}			
				}
				if ($li_tot>0)
				{ 
					unset($la_data1);
				    unset($la_data_tot);
			    }		
				unset($la_data1);
				unset($la_data_tot);			
		 }// fin del for
		}
		else
		{
		 print("<script language=JavaScript>");
		 print(" alert('No hay nada que Reportar');"); 
		 print(" close();");
		 print("</script>");
		}
	}
	
	 else // Imprimimos el reporte
	 {
		$arrResultado=$io_report->uf_spi_reporte_ejecucion_financiera_mensual($ldt_fecini,$ldt_fecfin,$ls_cmbnivel,$ai_MenorNivel);
		$ai_MenorNivel = $arrResultado['ai_MenorNivel'];
		$lb_valido = $arrResultado['lb_valido'];
		$li_tot=$io_report->dts_reporte->getRowCount("spi_cuenta");// print $li_tot;
		if($li_tot>0)
		{
			for($z=1;$z<=$li_tot;$z++)
			{ 
				  $ls_spi_cuenta=$io_report->dts_reporte->data["spi_cuenta"][$z];
				  $ls_denominacion=$io_report->dts_reporte->data["denominacion"][$z];
				  $ls_nivel=$io_report->dts_reporte->data["nivel"][$z];
				  $ld_previsto=$io_report->dts_reporte->data["previsto"][$z]; 
				  $ld_aumento=$io_report->dts_reporte->data["aumento"][$z];
				  $ld_disminucion=$io_report->dts_reporte->data["disminucion"][$z];
				  $ld_devengado=$io_report->dts_reporte->data["devengado"][$z];
				  $ld_cobrado=$io_report->dts_reporte->data["cobrado"][$z];
				  $ld_cobrado_anticipado=$io_report->dts_reporte->data["cobrado_anticipado"][$z];
				  $ls_status=$io_report->dts_reporte->data["status"][$z];
				  $ld_monto_actualizado=$ld_previsto+$ld_aumento-$ld_disminucion-$ld_devengado;
				  $ld_por_cobrar=$ld_devengado-$ld_cobrado;
				 
				  if($ls_status=='C')
				  {
					  $ld_total_previsto=$ld_total_previsto+$ld_previsto;
					  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
					  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
					  $ld_total_devengado=$ld_total_devengado+$ld_devengado;
					  $ld_total_cobrado=$ld_total_cobrado+$ld_cobrado;
					  $ld_total_cobrado_anticipado=$ld_total_cobrado_anticipado+$ld_cobrado_anticipado;
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
					  $ld_total_por_cobrar=$ld_total_por_cobrar+$ld_por_cobrar;
				  } 
				  $ld_previsto=number_format($ld_previsto,2,",",".");
				  $ld_aumento=number_format($ld_aumento,2,",",".");
				  $ld_disminucion=number_format($ld_disminucion,2,",",".");
				  $ld_devengado=number_format($ld_devengado,2,",",".");
				  $ld_cobrado=number_format($ld_cobrado,2,",",".");
				  $ld_cobrado_anticipado=number_format($ld_cobrado_anticipado,2,",",".");
				  $ld_monto_actualizado=number_format($ld_monto_actualizado,2,",",".");
				  $ld_por_cobrar=number_format($ld_por_cobrar,2,",",".");
				
				  $la_data[$z]=array('cuenta'=>$ls_spi_cuenta,'denominacion'=>$ls_denominacion,'previsto'=>$ld_previsto,
									  'aumento'=>$ld_aumento,'disminución'=>$ld_disminucion,'devengado'=>$ld_devengado,
									  'cobrado'=>$ld_cobrado,'cobrado_anticipado'=>$ld_cobrado_anticipado,
									  'montoactualizado'=>$ld_monto_actualizado,'porcobrar'=>$ld_por_cobrar);
				
				 $ld_previsto=str_replace('.','',$ld_previsto);
				 $ld_previsto=str_replace(',','.',$ld_previsto);		
				 $ld_aumento=str_replace('.','',$ld_aumento);
				 $ld_aumento=str_replace(',','.',$ld_aumento);		
				 $ld_disminucion=str_replace('.','',$ld_disminucion);
				 $ld_disminucion=str_replace(',','.',$ld_disminucion);		
				 $ld_monto_actualizado=str_replace('.','',$ld_monto_actualizado);
				 $ld_monto_actualizado=str_replace(',','.',$ld_monto_actualizado);
				 $ld_devengado=str_replace('.','',$ld_devengado);
				 $ld_devengado=str_replace(',','.',$ld_devengado);		
				 $ld_cobrado=str_replace('.','',$ld_cobrado);
				 $ld_cobrado=str_replace(',','.',$ld_cobrado);		
				 $ld_cobrado_anticipado=str_replace('.','',$ld_cobrado_anticipado);
				 $ld_cobrado_anticipado=str_replace(',','.',$ld_cobrado_anticipado);		
				 $ld_por_cobrar=str_replace('.','',$ld_por_cobrar);
				 $ld_por_cobrar=str_replace(',','.',$ld_por_cobrar);
	
				if($z==$li_tot)
				{
				  
					 $DataSet = new pData;
					 $DataSet->AddPoint(array($ld_total_monto_actualizado,$ld_total_devengado,$ld_total_cobrado,$ld_total_por_cobrar),"Serie1");
					 $DataSet->AddPoint(array("Actualizado","Devengado","Cobrado","Por Cobrar"),"titulos");
					 $DataSet->AddAllSeries();
					 $DataSet->SetAbsciseLabelSerie("titulos");
					
				
					 // Initialise the graph
					 $Test = new pChart(500,400);
					 $Test->drawFilledRoundedRectangle(7,7,493,393,5,240,240,240);
					 $Test->drawRoundedRectangle(5,5,495,395,5,230,230,230);
					
					 // This will draw a shadow under the pie chart
					 $Test->drawFilledCircle(225,205,170,200,200,200);
					
					 // Draw the pie chart
					 $Test->setFontProperties("../../shared/graficos/Fonts/tahoma.ttf",8);
					 $Test->AntialiasQuality = 0;
					 $Test->drawBasicPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),220,200,170,PIE_PERCENTAGE,255,255,218);
					 $Test->drawPieLegend(380,15,$DataSet->GetData(),$DataSet->GetDataDescription(),150,150,150);
					
					 $Test->Render("ejecucionfinanciera.png");
				}//if
			}//for
			unset($la_data);
			unset($la_data_tot);			
	   }// if
	   else
	   {
		 print("<script language=JavaScript>");
		 print(" alert('No hay nada que Reportar');"); 
		 print(" close();");
		 print("</script>");
	   }
	}
	unset($io_report);
	unset($io_funciones);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ejecucion Financiera Mensual</title>
<link href="../../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/report.css" rel="stylesheet" type="text/css" />
<link href="../../shared/css/tablas.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="498" border="0" align="center">
  <tr>
    <td width="320" class="sin-borde2"><div align="center" class="titulo-celdanew"> Ejecucion Financiera Mensual </div></td>
  </tr>
  <tr>
    <td width="320"><img src="ejecucionfinanciera.png" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="right">
<a href="javascript:ue_print();"> <img src="../../shared/imagebank/tools20/print.gif" width="35" height="30" border="0"/></a></div>
	</td>
  </tr>
  <tr>
    <td width="320"></td>
  </tr>
</table>


</body>
<script >
function ue_print()
{
	window.print();
}
</script>
</html>
