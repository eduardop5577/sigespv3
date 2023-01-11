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
	
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "Distribucion_mensual_de_presupuesto.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------------------------------------------------------------------------------	

		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
//-----------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_reportes_class.php");
		$io_report = new sigesp_spg_reportes_class();
			
	
//------------------------------------------------------------------------------------------------------------------------------		

//--------------------------------------------------  Parámetros para Filtar el Reporte  ------------------------------------
		$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
	    $ls_estclades       = $_GET["estclades"];
	    $ls_estclahas       = $_GET["estclahas"];
		$ls_cuentades       = $_GET["txtcuentades"];
	    $ls_cuentahas       = $_GET["txtcuentahas"];
		if($li_estmodest==1)
		{
			$ls_codestpro4_min = "0000000000000000000000000";;
			$ls_codestpro5_min = "0000000000000000000000000";;
			$ls_codestpro4h_max = "0000000000000000000000000";;
			$ls_codestpro5h_max = "0000000000000000000000000";;
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
			if(($ls_codestpro1_min=="")&&($ls_codestpro2_min=="")&&($ls_codestpro3_min=="")&&($ls_codestpro4_min=="")&&
			   ($ls_codestpro5_min==""))
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
			if(($ls_codestpro1h_max=="")&&($ls_codestpro2h_max=="")&&($ls_codestpro3h_max=="")&&($ls_codestpro4h_max=="")&&
			   ($ls_codestpro5h_max==""))
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
	   $ls_codfuefindes=$_GET["txtcodfuefindes"];
	   $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
	   if (($ls_codfuefindes=='' || $ls_codfuefindes=='--')&&($ls_codfuefindes=='' || $ls_codfuefindes=='--'))
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
	   if ($ls_cuentades=='')
	   {
	    $ls_cuenta = "";
		   $arrResultado=$io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuenta);
		   $ls_cuenta=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
			 $ls_cuentades = $ls_cuenta;
			}
	   }
	   
	   if ($ls_cuentahas=='')
	   {
	    $ls_cuenta = "";
		   $arrResultado=$io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuenta);
		   $ls_cuenta=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
			 $ls_cuentahas = $ls_cuenta;
			}
	   }
	 
	 $ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
	 $ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;
	 
	 
//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
		$ls_titulo="DISTRIBUCION MENSUAL DEL PRESUPUESTO"; 	
//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
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
    $lb_valido=$io_report->uf_spg_reportes_comparados_distribucion_mensual_presupuesto($ls_codestpro1,$ls_codestpro2,
	                                                                                   $ls_codestpro3,$ls_codestpro4,
	                                                                                   $ls_codestpro5,$ls_codestpro1h,
	                                                                                   $ls_codestpro2h,$ls_codestpro3h,
																                       $ls_codestpro4h,$ls_codestpro5h,
																					   $ls_codfuefindes,$ls_codfuefinhas,
																					   $ls_estclades,$ls_estclahas,
																					   $ls_cuentades, $ls_cuentahas);
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	         
        $io_report->dts_reporte->group_noorder("programatica");
		$li_tot=$io_report->dts_reporte->getRowCount("spg_cuenta");
		
		$contfilas=0;
		
		$fecha=date('d/m/Y');
		$hora=date('H:i');
		$ls_desc_event="Solicitud de Reporte Distribucion mensual del Presupuesto En Formato Excel Desde la Programatica  ".$ls_programatica_desde." hasta ".$ls_programatica_hasta;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_distribucion_mentri_presupuesto.php",$ls_desc_event);
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
		$lo_datacenter=&$lo_libro->addformat();
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
		$contfilas++;
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(0, 6, $fecha,$lo_dataright);
		$lo_hoja->write(1, 6, $hora,$lo_dataright);
		$contfilas++;
		$lo_hoja->write(1, 3, $ls_titulo1,$lo_encabezado);
		$contfilas++;
	    $ls_spg_cuenta_ant="";
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_disminucion=0;
		$ld_total_monto_actualizado=0;
		$ld_total_compromiso=0;
		$ld_total_precompromiso=0;
		$ld_total_compromiso=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_causado=0;
		$ld_total_pagado=0;
		$ld_total_por_paga=0;
		$li_row=2;
		
		
		
		
		
		
		
		
		
		
		
		
		
		$ld_total_enero=0;
		$ld_total_febrero=0;
		$ld_total_marzo=0;
		$ld_total_abril=0;
		$ld_total_mayo=0;
		$ld_total_junio=0;
		$ld_total_julio=0;
		$ld_total_agosto=0;
		$ld_total_septiembre=0;
		$ld_total_octubre=0;
		$ld_total_noviembre=0;
		$ld_total_diciembre=0;
		$ld_total_general_cuenta=0;
		$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		for($z=1;$z<=$li_tot;$z++)
		{
		    $li_tmp=($z+1);
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_programatica=$io_report->dts_reporte->data["programatica"][$z];
			$ls_spg_cuenta=trim($io_report->dts_reporte->data["spg_cuenta"][$z]);
		    if ($z<$li_tot)
		    {
				$ls_programatica_next=$io_report->dts_reporte->data["programatica"][$li_tmp]; 
		    }
		    elseif($z=$li_tot)
		    {
				$ls_programatica_next='no_next';
		    }
			if(!empty($ls_programatica))
			{
				$ls_estcla=substr($ls_programatica,-1);
				$ls_codestpro1=substr($ls_programatica,0,25);
				$ls_denestpro1="";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
				  $ls_denestpro1=trim($ls_denestpro1);
				}
				$ls_codestpro2=substr($ls_programatica,25,25);
				if($lb_valido)
				{
				  $ls_denestpro2="";
				  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
				  $ls_denestpro2=$arrResultado['as_denestpro2'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro2=trim($ls_denestpro2);
				}
				$ls_codestpro3=substr($ls_programatica,50,25);
				if($lb_valido)
				{
				  $ls_denestpro3="";
				  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
				  $ls_denestpro3=$arrResultado['as_denestpro3'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro3=trim($ls_denestpro3);
				}
				if($li_estmodest==2)
				{
					$ls_codestpro4=substr($ls_programatica,75,25);
					if($lb_valido)
					{
					  $ls_denestpro4="";
					  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
					  $ls_denestpro4=$arrResultado['as_denestpro4'];
					  $lb_valido=$arrResultado['lb_valido'];
					  $ls_denestpro4=trim($ls_denestpro4);
					}
					$ls_codestpro5=substr($ls_programatica,100,25);
					if($lb_valido)
					{
					  $ls_denestpro5="";
					  $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
					  $ls_denestpro5=$arrResultado['as_denestpro5'];
					  $lb_valido=$arrResultado['lb_valido'];
					  $ls_denestpro5=trim($ls_denestpro5);
					}
					$ls_denestpro_ant=trim($ls_denestpro1)." , ".trim($ls_denestpro2)." , ".trim($ls_denestpro3)." , ".trim($ls_denestpro4)." , ".trim($ls_denestpro5);
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				}
				else
				{
					//$ls_denestpro_ant=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					$ls_denestpro_ant = array();
					$ls_denestpro_ant[0]=$ls_denestpro1;
					$ls_denestpro_ant[1]=$ls_denestpro2;
					$ls_denestpro_ant[2]=$ls_denestpro3;
					$ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
				}
			}
			/*if(($li_tot==1)&&($li_estmodest==1))
			{
			   //$ls_programatica_ant=$io_report->dts_reporte->data["programatica"][$z];
			   $ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
			   $ls_denestpro_ant=$ls_denestpro;
			}
			if(($li_tot==1)&&($li_estmodest==2))
			{
			   $ls_programatica_ant=$io_report->dts_reporte->data["programatica"][$z];
			   $ls_denestpro_ant=$ls_denestpro;
			   $ls_programatica_ant=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
			}*/
			$ls_denominacion=trim($io_report->dts_reporte->data["denominacion"][$z]);
			$li_nivel=$io_report->dts_reporte->data["nivel"][$z];
			$li_status=$io_report->dts_reporte->data["status"][$z];
			$ld_enero=$io_report->dts_reporte->data["enero"][$z];
			$ld_febrero=$io_report->dts_reporte->data["febrero"][$z];
			$ld_marzo=$io_report->dts_reporte->data["marzo"][$z];
			$ld_abril=$io_report->dts_reporte->data["abril"][$z];
			$ld_mayo=$io_report->dts_reporte->data["mayo"][$z];
			$ld_junio=$io_report->dts_reporte->data["junio"][$z];
			$ld_julio=$io_report->dts_reporte->data["julio"][$z];
			$ld_agosto=$io_report->dts_reporte->data["agosto"][$z];
			$ld_septiembre=$io_report->dts_reporte->data["septiembre"][$z];
			$ld_octubre=$io_report->dts_reporte->data["octubre"][$z];
			$ld_noviembre=$io_report->dts_reporte->data["noviembre"][$z];
			$ld_diciembre=$io_report->dts_reporte->data["diciembre"][$z];
			
			$ld_total_cuenta=$ld_enero+$ld_febrero+$ld_marzo+$ld_abril+$ld_mayo+$ld_junio+$ld_julio+$ld_agosto+$ld_septiembre+$ld_octubre+$ld_noviembre+$ld_diciembre;
			if (($li_nivel=="1")&&($li_status=="S"))
			{
				$ld_total_enero=$ld_total_enero+$ld_enero;
				$ld_total_febrero=$ld_total_febrero+$ld_febrero;
				$ld_total_marzo=$ld_total_marzo+$ld_marzo;
				$ld_total_abril=$ld_total_abril+$ld_abril;
				$ld_total_mayo=$ld_total_mayo+$ld_mayo;
				$ld_total_junio=$ld_total_junio+$ld_junio;
				$ld_total_julio=$ld_total_julio+$ld_julio;
				$ld_total_agosto=$ld_total_agosto+$ld_agosto;
				$ld_total_septiembre=$ld_total_septiembre+$ld_septiembre;
				$ld_total_octubre=$ld_total_octubre+$ld_octubre;
				$ld_total_noviembre=$ld_total_noviembre+$ld_noviembre;
				$ld_total_diciembre=$ld_total_diciembre+$ld_diciembre;
				$ld_total_general_cuenta=$ld_total_general_cuenta+$ld_total_cuenta;
			}
			
			if (!empty($ls_programatica))
		    {
				$ld_enero=number_format($ld_enero,2,",",".");
				$ld_febrero=number_format($ld_febrero,2,",",".");
				$ld_marzo=number_format($ld_marzo,2,",",".");
				$ld_abril=number_format($ld_abril,2,",",".");
				$ld_mayo=number_format($ld_mayo,2,",",".");
				$ld_junio=number_format($ld_junio,2,",",".");
				$ld_julio=number_format($ld_julio,2,",",".");
				$ld_agosto=number_format($ld_agosto,2,",",".");
				$ld_septiembre=number_format($ld_septiembre,2,",",".");
				$ld_octubre=number_format($ld_octubre,2,",",".");
				$ld_noviembre=number_format($ld_noviembre,2,",",".");
				$ld_diciembre=number_format($ld_diciembre,2,",",".");
				$ld_total_cuenta=number_format($ld_total_cuenta,2,",",".");	
							
				if ($li_status=="S")
				 {
				 	$ls_spg_cuenta=$ls_spg_cuenta;
					$ls_denominacion=$ls_denominacion;	
					$ld_enero=$ld_enero;
					$ld_febrero=$ld_febrero;
					$ld_marzo=$ld_marzo;
					$ld_abril=$ld_abril;
					$ld_mayo=$ld_mayo;
					$ld_junio=$ld_junio;
					$ld_julio=$ld_julio;
					$ld_agosto=$ld_agosto;
					$ld_septiembre=$ld_septiembre;
					$ld_octubre=$ld_octubre;
					$ld_noviembre=$ld_noviembre;
					$ld_diciembre=$ld_diciembre;
					$ld_total_cuenta=$ld_total_cuenta;				
				 }//print $ls_spg_cuenta."1<br>";	
				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,'enero'=>$ld_enero,
				                   'febrero'=>$ld_febrero,'marzo'=>$ld_marzo,'abril'=>$ld_abril,'mayo'=>$ld_mayo,
								   'junio'=>$ld_junio,'julio'=>$ld_julio,'agosto'=>$ld_agosto,'septiembre'=>$ld_septiembre,
								   'octubre'=>$ld_octubre,'noviembre'=>$ld_noviembre,'diciembre'=>$ld_diciembre,
								   'total'=>$ld_total_cuenta);
			   
				$ld_enero=str_replace('.','',$ld_enero);
				$ld_enero=str_replace(',','.',$ld_enero);		
				$ld_febrero=str_replace('.','',$ld_febrero);
				$ld_febrero=str_replace(',','.',$ld_febrero);
				$ld_marzo=str_replace('.','',$ld_marzo);
				$ld_marzo=str_replace(',','.',$ld_marzo);		
				$ld_abril=str_replace('.','',$ld_abril);
				$ld_abril=str_replace(',','.',$ld_abril);
				$ld_mayo=str_replace('.','',$ld_mayo);
				$ld_mayo=str_replace(',','.',$ld_mayo);		
				$ld_junio=str_replace('.','',$ld_junio);
				$ld_junio=str_replace(',','.',$ld_junio);
				$ld_julio=str_replace('.','',$ld_julio);
				$ld_julio=str_replace(',','.',$ld_julio);		
				$ld_agosto=str_replace('.','',$ld_agosto);
				$ld_agosto=str_replace(',','.',$ld_agosto);
				$ld_septiembre=str_replace('.','',$ld_septiembre);
				$ld_septiembre=str_replace(',','.',$ld_septiembre);		
				$ld_octubre=str_replace('.','',$ld_octubre);
				$ld_octubre=str_replace(',','.',$ld_octubre);
				$ld_noviembre=str_replace('.','',$ld_noviembre);
				$ld_noviembre=str_replace(',','.',$ld_noviembre);		
				$ld_diciembre=str_replace('.','',$ld_diciembre);
				$ld_diciembre=str_replace(',','.',$ld_diciembre);
				$ld_total_cuenta=str_replace('.','',$ld_total_cuenta);
				$ld_total_cuenta=str_replace(',','.',$ld_total_cuenta);
			}
			else
			{
				$ld_enero=number_format($ld_enero,2,",",".");
				$ld_febrero=number_format($ld_febrero,2,",",".");
				$ld_marzo=number_format($ld_marzo,2,",",".");
				$ld_abril=number_format($ld_abril,2,",",".");
				$ld_mayo=number_format($ld_mayo,2,",",".");
				$ld_junio=number_format($ld_junio,2,",",".");
				$ld_julio=number_format($ld_julio,2,",",".");
				$ld_agosto=number_format($ld_agosto,2,",",".");
				$ld_septiembre=number_format($ld_septiembre,2,",",".");
				$ld_octubre=number_format($ld_octubre,2,",",".");
				$ld_noviembre=number_format($ld_noviembre,2,",",".");
				$ld_diciembre=number_format($ld_diciembre,2,",",".");
				$ld_total_cuenta=number_format($ld_total_cuenta,2,",",".");
				
				if ($li_status=="S")
				 {
				 	$ls_spg_cuenta=$ls_spg_cuenta;
					$ls_denominacion=$ls_denominacion;
					$ld_enero=$ld_enero;
					$ld_febrero=$ld_febrero;
					$ld_marzo=$ld_marzo;
					$ld_abril=$ld_abril;
					$ld_mayo=$ld_mayo;
					$ld_junio=$ld_junio;
					$ld_julio=$ld_julio;
					$ld_agosto=$ld_agosto;
					$ld_septiembre=$ld_septiembre;
					$ld_octubre=$ld_octubre;
					$ld_noviembre=$ld_noviembre;
					$ld_diciembre=$ld_diciembre;
					$ld_total_cuenta=$ld_total_cuenta;					
				 }	//print $ls_spg_cuenta."  ".$ld_noviembre."2<br>";
				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,'enero'=>$ld_enero,
				                   'febrero'=>$ld_febrero,'marzo'=>$ld_marzo,'abril'=>$ld_abril,'mayo'=>$ld_mayo,
								   'junio'=>$ld_junio,'julio'=>$ld_julio,'agosto'=>$ld_agosto,'septiembre'=>$ld_septiembre,
								   'octubre'=>$ld_octubre,'noviembre'=>$ld_noviembre,'diciembre'=>$ld_diciembre,
								   'total'=>$ld_total_cuenta);
			   
				$ld_enero=str_replace('.','',$ld_enero);
				$ld_enero=str_replace(',','.',$ld_enero);		
				$ld_febrero=str_replace('.','',$ld_febrero);
				$ld_febrero=str_replace(',','.',$ld_febrero);
				$ld_marzo=str_replace('.','',$ld_marzo);
				$ld_marzo=str_replace(',','.',$ld_marzo);		
				$ld_abril=str_replace('.','',$ld_abril);
				$ld_abril=str_replace(',','.',$ld_abril);
				$ld_mayo=str_replace('.','',$ld_mayo);
				$ld_mayo=str_replace(',','.',$ld_mayo);		
				$ld_junio=str_replace('.','',$ld_junio);
				$ld_junio=str_replace(',','.',$ld_junio);
				$ld_julio=str_replace('.','',$ld_julio);
				$ld_julio=str_replace(',','.',$ld_julio);		
				$ld_agosto=str_replace('.','',$ld_agosto);
				$ld_agosto=str_replace(',','.',$ld_agosto);
				$ld_septiembre=str_replace('.','',$ld_septiembre);
				$ld_septiembre=str_replace(',','.',$ld_septiembre);		
				$ld_octubre=str_replace('.','',$ld_octubre);
				$ld_octubre=str_replace(',','.',$ld_octubre);
				$ld_noviembre=str_replace('.','',$ld_noviembre);
				$ld_noviembre=str_replace(',','.',$ld_noviembre);		
				$ld_diciembre=str_replace('.','',$ld_diciembre);
				$ld_diciembre=str_replace(',','.',$ld_diciembre);
				$ld_total_cuenta=str_replace('.','',$ld_total_cuenta);
				$ld_total_cuenta=str_replace(',','.',$ld_total_cuenta);
			}
			if (!empty($ls_programatica_next))
			{
				$ld_enero=number_format($ld_enero,2,",",".");
				$ld_febrero=number_format($ld_febrero,2,",",".");
				$ld_marzo=number_format($ld_marzo,2,",",".");
				$ld_abril=number_format($ld_abril,2,",",".");
				$ld_mayo=number_format($ld_mayo,2,",",".");
				$ld_junio=number_format($ld_junio,2,",",".");
				$ld_julio=number_format($ld_julio,2,",",".");
				$ld_agosto=number_format($ld_agosto,2,",",".");
				$ld_septiembre=number_format($ld_septiembre,2,",",".");
				$ld_octubre=number_format($ld_octubre,2,",",".");
				$ld_noviembre=number_format($ld_noviembre,2,",",".");
				$ld_diciembre=number_format($ld_diciembre,2,",",".");
				$ld_total_cuenta=number_format($ld_total_cuenta,2,",",".");
				
				
				if ($li_status=="S")
				 {
				 	$ls_spg_cuenta=$ls_spg_cuenta;
					$ls_denominacion=$ls_denominacion;	
					$ld_enero=$ld_enero;
					$ld_febrero=$ld_febrero;
					$ld_marzo=$ld_marzo;
					$ld_abril=$ld_abril;
					$ld_mayo=$ld_mayo;
					$ld_junio=$ld_junio;
					$ld_julio=$ld_julio;
					$ld_agosto=$ld_agosto;
					$ld_septiembre=$ld_septiembre;
					$ld_octubre=$ld_octubre;
					$ld_noviembre=$ld_noviembre;
					$ld_diciembre=$ld_diciembre;
					$ld_total_cuenta=$ld_total_cuenta;				
				 }//print $ls_spg_cuenta."3<br>";		
				$la_data[$z]=array('cuenta'=>$ls_spg_cuenta,'denominacion'=>$ls_denominacion,'enero'=>$ld_enero,
				                   'febrero'=>$ld_febrero,'marzo'=>$ld_marzo,'abril'=>$ld_abril,'mayo'=>$ld_mayo,
								   'junio'=>$ld_junio,'julio'=>$ld_julio,'agosto'=>$ld_agosto,'septiembre'=>$ld_septiembre,
								   'octubre'=>$ld_octubre,'noviembre'=>$ld_noviembre,'diciembre'=>$ld_diciembre,
								   'total'=>$ld_total_cuenta);
		        
				
				
				  /// Bolivar
				  $ld_total_enero=number_format($ld_total_enero,2,",",".");
				  $ld_total_febrero=number_format($ld_total_febrero,2,",",".");
				  $ld_total_marzo=number_format($ld_total_marzo,2,",",".");
				  $ld_total_abril=number_format($ld_total_abril,2,",",".");
				  $ld_total_mayo=number_format($ld_total_mayo,2,",",".");
				  $ld_total_junio=number_format($ld_total_junio,2,",",".");
				  $ld_total_julio=number_format($ld_total_julio,2,",",".");
				  $ld_total_agosto=number_format($ld_total_agosto,2,",",".");
				  $ld_total_septiembre=number_format($ld_total_septiembre,2,",",".");
				  $ld_total_octubre=number_format($ld_total_octubre,2,",",".");
				  $ld_total_noviembre=number_format($ld_total_noviembre,2,",",".");
				  $ld_total_diciembre=number_format($ld_total_diciembre,2,",",".");
				  $ld_total_general_cuenta=number_format($ld_total_general_cuenta,2,",",".");
				  
				  
				 
				 	$ld_total_enero=$ld_total_enero;
					$ld_total_febrero=$ld_total_febrero;
					$ld_total_marzo=$ld_total_marzo;
					$ld_total_abril=$ld_total_abril;
					$ld_total_mayo=$ld_total_mayo;
					$ld_total_junio=$ld_total_junio;
					$ld_total_julio=$ld_total_julio;
					$ld_total_agosto=$ld_total_agosto;
					$ld_total_septiembre=$ld_total_septiembre;
					$ld_total_octubre=$ld_total_octubre;
					$ld_total_noviembre=$ld_total_noviembre;
					$ld_total_diciembre=$ld_total_diciembre;
					$ld_total_general_cuenta=$ld_total_general_cuenta;					
				 		
				  $la_data_tot[$z]=array('totalgeneral'=>'TOTAL Bs.','enero'=>$ld_total_enero,'febrero'=>$ld_total_febrero,                                         'marzo'=>$ld_total_marzo,'abril'=>$ld_total_abril,'mayo'=>$ld_total_mayo,
										 'junio'=>$ld_total_junio,'julio'=>$ld_total_julio,'agosto'=>$ld_total_agosto,
										 'septiembre'=>$ld_total_septiembre,'octubre'=>$ld_total_octubre,
										 'noviembre'=>$ld_total_noviembre,'diciembre'=>$ld_total_diciembre,
										 'total'=>$ld_total_general_cuenta);
								
				
				$lo_hoja->write($contfilas++, 1, "ESTRUCTURA PRESUPUESTARIA",$lo_datacenter);
				$contfilas++;
				$lo_hoja->write($contfilas, 1," ".substr($ls_programatica_ant,0,$ls_loncodestpro1),$lo_dataleft);
				$lo_hoja->write($contfilas, 2, $ls_denestpro_ant[0],$lo_dataleft);
				$contfilas++; 
				$lo_hoja->write($contfilas, 1," ".substr($ls_programatica_ant,$ls_loncodestpro1,$ls_loncodestpro2),$lo_dataleft);
				$lo_hoja->write($contfilas, 2, $ls_denestpro_ant[1],$lo_dataleft);
				$contfilas++; 
				$lo_hoja->write($contfilas,1," ".substr($ls_programatica_ant,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3),$lo_dataleft);
				$lo_hoja->write($contfilas, 2, $ls_denestpro_ant[2],$lo_dataleft);
				$contfilas++; 
				
				$lo_hoja->write($contfilas, 1, "Cuenta",$lo_titulo);
				$lo_hoja->write($contfilas, 2, "Denominación",$lo_titulo);
				$lo_hoja->write($contfilas, 3, "Enero",$lo_titulo);
				$lo_hoja->write($contfilas, 4, "Febrero",$lo_titulo);
				$lo_hoja->write($contfilas, 5, "Marzo",$lo_titulo);
				$lo_hoja->write($contfilas, 6, "Abril",$lo_titulo);
				$lo_hoja->write($contfilas, 7, "Mayo",$lo_titulo);
				$lo_hoja->write($contfilas, 8, "Junio",$lo_titulo);
				$lo_hoja->write($contfilas, 9, "Julio",$lo_titulo);
				$lo_hoja->write($contfilas, 10, "Agosto",$lo_titulo);
				$lo_hoja->write($contfilas,11, "Septiembre",$lo_titulo);
				$lo_hoja->write($contfilas, 12, "Octubre",$lo_titulo);
				$lo_hoja->write($contfilas, 13, "Noviembre",$lo_titulo);
				$lo_hoja->write($contfilas, 14, "Diciembre",$lo_titulo);
				$lo_hoja->write($contfilas, 15, "TOTAL",$lo_titulo);
				$contfilas++;
				$datadesde=0;
				
				foreach($la_data as $data)
				{
					$lo_hoja->write($contfilas, 1,$data["cuenta"],$lo_dataleft);
					$lo_hoja->write($contfilas, 2,$data["denominacion"],$lo_dataleft);
					$lo_hoja->write($contfilas, 3,$data["enero"],$lo_dataright);
					$lo_hoja->write($contfilas, 4, $data["febrero"],$lo_dataright);
					$lo_hoja->write($contfilas, 5, $data["marzo"],$lo_dataright);
					$lo_hoja->write($contfilas, 6, $data["abril"],$lo_dataright);
					$lo_hoja->write($contfilas, 7, $data["mayo"],$lo_dataright);
					$lo_hoja->write($contfilas, 8, $data["junio"],$lo_dataright);
					$lo_hoja->write($contfilas, 9, $data["julio"],$lo_dataright);
					$lo_hoja->write($contfilas, 10, $data["agosto"],$lo_dataright);
					$lo_hoja->write($contfilas, 11, $data["septiembre"],$lo_dataright);
					$lo_hoja->write($contfilas, 12, $data["octubre"],$lo_dataright);
					$lo_hoja->write($contfilas, 13, $data["noviembre"],$lo_dataright);
					$lo_hoja->write($contfilas, 14, $data["diciembre"],$lo_dataright);
					$lo_hoja->write($contfilas, 15, $data["total"],$lo_dataright);					
					$contfilas++;
				}

				
				$lo_hoja->write($contfilas, 2,"TOTAL Bs",$lo_dataleft);
				$lo_hoja->write($contfilas, 3,$ld_total_enero,$lo_dataright);
				$lo_hoja->write($contfilas, 4, $ld_total_febrero,$lo_dataright);
				$lo_hoja->write($contfilas, 5, $ld_total_marzo,$lo_dataright);
				$lo_hoja->write($contfilas, 6, $ld_total_abril,$lo_dataright);
				$lo_hoja->write($contfilas, 7, $ld_total_mayo,$lo_dataright);
				$lo_hoja->write($contfilas, 8, $ld_total_junio,$lo_dataright);
				$lo_hoja->write($contfilas, 9, $ld_total_julio,$lo_dataright);
				$lo_hoja->write($contfilas, 10, $ld_total_agosto,$lo_dataright);
				$lo_hoja->write($contfilas, 11, $ld_total_septiembre,$lo_dataright);
				$lo_hoja->write($contfilas, 12, $ld_total_octubre,$lo_dataright);
				$lo_hoja->write($contfilas, 13, $ld_total_noviembre,$lo_dataright);
				$lo_hoja->write($contfilas, 14, $ld_total_diciembre,$lo_dataright);
				$lo_hoja->write($contfilas, 15,$ld_total_general_cuenta,$lo_dataright);	
				$contfilas++;
				
				
				
				if ((!empty($ls_programatica_next))&&($z<$li_tot))
				{
				 //$io_pdf->ezNewPage(); // Insertar una nueva página
				} 
                $ld_total_general_cuenta=0;
			    unset($la_data);
			    unset($la_data_tot);
			}//if
	    }//for
	   
	    $z=0;
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"distribucion_presupuesto_mensual.xls\"");
		header("Content-Disposition: inline; filename=\"distribucion_presupuesto_mensual.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
	unset($io_fecha);
?> 