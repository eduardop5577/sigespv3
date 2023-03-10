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
	
//------------------------------------------------------------------------------------------------------------------------------
		require_once ("sigesp_spg_class_tcpdf.php");
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		$ls_nombrearchivo="acumulado_por_cuentas.txt";
		$lo_archivo=@fopen("$ls_nombrearchivo","a+");
//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
		$ldt_periodo        = $_SESSION["la_empresa"]["periodo"];
		$li_ano             = substr($ldt_periodo,0,4);
		$li_estmodest       = $_SESSION["la_empresa"]["estmodest"];
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
		$ls_text_periodo 	= $_GET["tperiodo"];
		$ld_periodo      	= $_GET["periodo"];
		$ld_tipper       	= $_GET["tipper"];
		$ld_fecinirep		= "";
		$ld_fecfinrep 		= "";
//-----------------------------------------------------------------------------------------------------------------------------

		switch($ld_tipper)
		{
			 case 1:
			       $ld_per01 		= intval($ld_periodo);
				   $ld_per02 		= "";
				   $ld_per03 		= "";
				   $ls_desper 		= "MENSUAL"; 
			       $ld_fecinirep	= $li_ano."/".$ld_periodo."/"."01";
				   $ld_fecfinrep	= $io_fecha->uf_last_day($ld_periodo,$li_ano); 
				   $ld_fecfinrep	= $li_ano."/".substr($ld_periodo,0,2)."/".substr($ld_fecfinrep,0,2);
			       $ld_anterior 	= 1; 
			       break;

			 case 2:
			      $ld_per01 		= intval(substr($ld_periodo,0,2));
				  $ld_per02 		= intval(substr($ld_periodo,2,2));
				  $ls_desper 		= "BIMENSUAL";
				  $ld_fecinirep		= $li_ano."/".substr($ld_periodo,0,2)."/"."01";
				  $ld_fecfinrep		= $io_fecha->uf_last_day(substr($ld_periodo,2,2),$li_ano);
				  $ld_fecfinrep		= $li_ano."/".substr($ld_periodo,2,2)."/".substr($ld_fecfinrep,0,2);
				  $ld_per03 		= "";
				  $ld_anterior 		= 2;
			      break;

			 case 3:
                                  $ld_per01 		= intval(substr($ld_periodo,0,2));
				  $ld_per02 		= intval(substr($ld_periodo,2,2));
				  $ld_per03 		= intval(substr($ld_periodo,4,2));
				  $ls_desper 		= "TRIMESTRAL";
				  $ld_fecinirep		= $li_ano."/".substr($ld_periodo,0,2)."/"."01";
				  $ld_fecfinrep		= $io_fecha->uf_last_day(substr($ld_periodo,4,2),$li_ano);
				  $ld_fecfinrep		= $li_ano."/".substr($ld_periodo,4,2)."/".substr($ld_fecfinrep,0,2);
				  $ld_anterior 		= 3;
			      break;
                          
			 case 4:
                                  $ld_per01 		= intval(substr($ld_periodo,0,2));
				  $ld_per02 		= intval(substr($ld_periodo,2,2));
				  $ld_per03 		= intval(substr($ld_periodo,4,2));
				  $ls_desper 		= "RANGO DE FECHA";
				  $ld_fecinirep		= $_GET["fechaReporteDesde"];
				  $ld_fecfinrep		= $_GET["fechaReporteHasta"];
				  $ld_anterior 		= 4;
			      break;
		}


		global $ls_tipoformato;
		global $la_data_tot_bsf;
		global $la_data_tot;
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();

		$li_candeccon = $_SESSION["la_empresa"]["candeccon"];
		$li_tipconmon = $_SESSION["la_empresa"]["tipconmon"];
		$li_redconmon = $_SESSION["la_empresa"]["redconmon"];
//------------------------------------------------------------------------------------------------------------------------------		
		if($li_estmodest==1)
		{
			$ls_codestpro4_min =  "0000000000000000000000000";
			$ls_codestpro5_min =  "0000000000000000000000000";
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
		
		$ls_cmbmesdes = $_GET["cmbmesdes"];
		$ldt_fecini=$li_ano."-".$ls_cmbmesdes."-01";
		$ldt_fecini_rep="01/".$ls_cmbmesdes."/".$li_ano;
		$ls_mes=$ls_cmbmesdes;
		$ls_ano=$li_ano;
		$fecfin=$io_fecha->uf_last_day($ls_mes,$ls_ano);
		$ldt_fecfin=$io_funciones->uf_convertirdatetobd($fecfin);
		
		$cmbnivel=$_GET["cmbnivel"];
		if($cmbnivel=="s1")
		{
          $ls_cmbnivel="1";
		}
		else
		{
          $ls_cmbnivel=$cmbnivel;
		}
		$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
		$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
		if($li_estmodest==1)
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
		
	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
		if($ls_cuentades_min=="")
		{
		   $arrResultado=$io_function_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min);
		   $ls_cuentades_min=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   $arrResultado=$io_function_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max);
		   $ls_cuentahas_max=$arrResultado['as_spg_cuenta'];
		   $lb_valido=$arrResultado['lb_valido'];
		   if($lb_valido)
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}
	    $ls_codfuefindes=$_GET["txtcodfuefindes"];
	    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
 		$lb_asignado = false;
	    if (empty($ls_codfuefindes) && empty($ls_codfuefinhas))
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
	    	
	    	$lb_asignado = true;
	    }
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Ejecucion Presupuestaria Mensual de Gasto desde la fecha ".$ld_fecinirep." hasta ".$ld_fecfinrep." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_ejecucion_financiera_mensual.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
	//----------------------------------------------------  Parámetros del encabezado  ---------------------------------------------
		$ls_titulo=" EJECUCION PRESUPUESTARIA MENSUAL DE GASTO DESDE FECHA  ".substr($ld_fecinirep,8,2)."/".substr($ld_fecinirep,5,2)."/".substr($ld_fecinirep,0,4)."  HASTA  ".substr($ld_fecfinrep,8,2)."/".substr($ld_fecfinrep,5,2)."/".substr($ld_fecfinrep,0,4);  
		$ls_titulo1="";
		if($li_estmodest==1)
	    {
		 $ls_titulo1=" DESDE LA ESTRUCTURA PRESUPUESTARIA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;  
		}
		elseif($li_estmodest==2)
		{
		 $ls_titulo1=" DESDE LA PROGRAMATICA  ".$ls_programatica_desde1."  HASTA  ".$ls_programatica_hasta1;  
		}
    //------------------------------------------------------------------------------------------------------------------------------ $ld_fecinirep,$ld_fecfinrep
    
    
    
    
    
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
	$arrResultado=$io_report->uf_spg_reporte_ejecucion_financiera_mensual($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                        $ls_codestpro4h,$ls_codestpro5h,$ld_fecinirep,$ld_fecfinrep,$ls_cmbnivel,$ls_cuentades,$ls_cuentahas,
															$ls_codfuefindes,$ls_codfuefinhas,$ls_estclades,$ls_estclahas,$rs_data);
	  $rs_data=$arrResultado['rs_data'];
	  $lb_valido=$arrResultado['lb_valido'];
	 if($lb_valido==false) // Existe algún error ó no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
		$io_tcpdf= new sigesp_spg_class_tcpdf ("L", PDF_UNIT, "legal", true);
		$io_tcpdf->AliasNbPages();
//		$io_tcpdf->SetHeaderData($_SESSION["ls_logo"],$_SESSION["ls_width"], date("d/m/Y"), date("h:i a").'-'.$_SESSION["ls_database"],$_SESSION["ls_height"]);
		$io_tcpdf->SetHeaderData($_SESSION["ls_logo"],$_SESSION["ls_width"], "", $_SESSION["ls_database"],$_SESSION["ls_height"]);
		$io_tcpdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$io_tcpdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$io_tcpdf->SetMargins(3, PDF_MARGIN_TOP,3);
		$io_tcpdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$io_tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$io_tcpdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$io_tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
		$io_tcpdf->AliasNbPages();
		$io_tcpdf->AddPage('', '', false, false);
		$io_tcpdf->SetFont("helvetica","B",8);
		$io_tcpdf->Ln(5);
		$io_tcpdf->Cell(0,10,$ls_titulo,0,0,'C');
		$io_tcpdf->Ln(3);
		$io_tcpdf->Cell(0,10,$ls_titulo1,0,0,'C');
		$io_tcpdf->Ln();
		
		$ld_total_asignado=0;
		$ld_total_aumento=0;
		$ld_total_aumento_acumulado=0;
		$ld_total_disminucion=0;
		$ld_total_disminucion_acumulado=0;
		$ld_total_monto_actualizado=0;
		$ld_total_monto_actualizado_acumulado=0;
		$ld_total_compromiso=0;
		$ld_total_compromiso_acumulado=0;
		$ld_total_precompromiso=0;
		$ld_total_precompromiso_acumulado=0;
		$ld_total_compromiso=0;
		$ld_total_compromiso_acumulado=0;
		$ld_total_saldo_comprometer=0;
		$ld_total_saldo_comprometer_acumulado=0;
		$ld_total_causado=0;
		$ld_total_causado_acumulado=0;
		$ld_total_pagado=0;
		$ld_total_pagado_acumulado=0;
		$ld_total_por_paga=0;
		$ld_total_por_paga_acumulado=0;
		$ld_aumento=0;
		$ld_aumento_acumulado=0;
	    $ld_disminucion=0;
	    $ld_disminucion_acumulado=0;
	    $ld_precompromiso=0;
	    $ld_precompromiso_acumulado=0;
	    $ld_compromiso=0;
	    $ld_compromiso_acumulado=0;
	    $ld_causado=0;
	    $ld_causado_acumulado=0;
	    $ld_pagado=0;
	    $ld_pagado_acumulado=0;
		$ld_monto_actualizado=0;
		$ld_monto_actualizado_acumulado=0;
  	    $ld_saldo_comprometer=0;
  	    $ld_saldo_comprometer_acumulado=0;
		$ld_por_paga=0;
		$ld_por_paga_acumulado=0;
		$lb_valido2=false;		
		$li_tot=$rs_data->RecordCount();
		$z=0;
		
		while(!$rs_data->EOF)
		{
			  $ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
		      $ls_denominacion=utf8_encode(trim($rs_data->fields["denominacion"]));
			  $ls_nivel=$rs_data->fields["nivel"];
			  if ($lb_asignado) {
			  	$ld_asignado=$rs_data->fields["asignado"];
			  }
			  else {
			  	$ld_asignado= $io_report->uf_buscar_asignado_fuente($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
													           $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,$ls_codestpro4h,$ls_codestpro5h,
													  $ls_estclades,$ls_estclahas,$ls_codfuefindes,$ls_codfuefinhas,$ls_spg_cuenta, $ls_nivel);
			  }
			  
			  
			  
			  $rs_data2="";			  
			  $arrResultado=$io_report->uf_spg_reporte_detalle_ejecucion_financiera_mensual($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
			                                                                   $ls_codestpro4,$ls_codestpro5,
																			   $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                                           $ls_codestpro4h,$ls_codestpro5h,
																			   $ls_estclades,$ls_estclahas,$ls_spg_cuenta,$ld_fecinirep,
															                   $ld_fecfinrep,$ls_codfuefindes,$ls_codfuefinhas,$rs_data2);
			  $rs_data2=$arrResultado['rs_data'];
			  $lb_valido2=$arrResultado['lb_valido'];
			  while((!$rs_data2->EOF)&&($lb_valido2))
			  {
				  $ld_aumento=$ld_aumento+$rs_data2->fields["aumento"]; 
				  $ld_disminucion=$ld_disminucion+$rs_data2->fields["disminucion"];
				  $ld_precompromiso=$ld_precompromiso+$rs_data2->fields["precompromiso"];
				  $ld_compromiso=$ld_compromiso+$rs_data2->fields["compromiso"];
				  $ld_causado=$ld_causado+$rs_data2->fields["causado"];
				  $ld_pagado=$ld_pagado+$rs_data2->fields["pagado"];				
				  //$ld_monto_actualizado=$ld_aumento-$ld_disminucion;				 
				  $ld_por_paga=$ld_causado-$ld_pagado;
			  	  $rs_data2->MoveNext();
			  }
			  //echo "Saldos del trimestre!!".$ls_spg_cuenta."    ".$ld_asignado."    ".$ld_aumento."    ".$ld_disminucion."<br>";
			  $rs_data3="";			  
			 $arrResultado=$io_report->uf_spg_reporte_detalle_ejecucion_financiera_mensual_acumulado($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
			                                                                   $ls_codestpro4,$ls_codestpro5,
																			   $ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                                                           $ls_codestpro4h,$ls_codestpro5h,
																			   $ls_estclades,$ls_estclahas,$ls_spg_cuenta,$ld_fecinirep,
															                   $ld_fecfinrep,$ls_codfuefindes,$ls_codfuefinhas,$rs_data3);
					
			  $rs_data3=$arrResultado['rs_data'];
			  $lb_valido3=$arrResultado['lb_valido'];
			  while((!$rs_data3->EOF)&&($lb_valido3))
			  {
				  //echo "aumento ".$rs_data3->fields["aumento"];
			  	  $ld_aumento_acumulado=$ld_aumento_acumulado+$rs_data3->fields["aumento"]; 
				  $ld_disminucion_acumulado=$ld_disminucion_acumulado+$rs_data3->fields["disminucion"];
				  $ld_precompromiso_acumulado=$ld_precompromiso_acumulado+$rs_data3->fields["precompromiso"];
				  $ld_compromiso_acumulado=$ld_compromiso_acumulado+$rs_data3->fields["compromiso"];
				  $ld_causado_acumulado=$ld_causado_acumulado+$rs_data3->fields["causado"];
				  $ld_pagado_acumulado=$ld_pagado_acumulado+$rs_data3->fields["pagado"];				
				  $ld_por_paga_acumulado=$ld_causado_acumulado-$ld_pagado_acumulado;
			  	  $rs_data3->MoveNext();
			  }
			  //echo "Saldo acumulado hasta la fecha!!".$ls_spg_cuenta."    ".$ld_asignado."    ".$ld_aumento_acumulado."      ".$ld_disminucion_acumulado."<br>";
			  $ld_monto_actualizado = $ld_aumento - $ld_disminucion;
			  $ld_monto_actualizado_acumulado=$ld_aumento_acumulado-$ld_disminucion_acumulado;
			  
			  $ld_monto_actualizado = $ld_monto_actualizado+$ld_monto_actualizado_acumulado+$ld_asignado;
			  $ld_saldo_comprometer  = $ld_monto_actualizado-($ld_compromiso+$ld_compromiso_acumulado);
			  //$ld_saldo_comprometer  = $ld_saldo_comprometer+$ld_saldo_comprometer_acumulado+$ld_asignado;
			  $ld_por_paga          = $ld_por_paga+$ld_por_paga_acumulado;
			  
			   if($ls_nivel==1)
			   {
			  	  //echo "<br> acumulado".$ld_monto_actualizado_acumulado;
				  $ld_total_asignado=$ld_total_asignado+$ld_asignado; 
				  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
				  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
				  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;
				  $ld_total_precompromiso=$ld_total_precompromiso+$ld_precompromiso;
				  $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso;
				  $ld_total_saldo_comprometer=$ld_total_saldo_comprometer+$ld_saldo_comprometer;
				  $ld_total_causado=$ld_total_causado+$ld_causado;
				  $ld_total_pagado=$ld_total_pagado+$ld_pagado;
				  $ld_total_por_paga=$ld_total_por_paga+$ld_por_paga;
			  }
			  
			  
			  $ls_spg_cuenta=trim($ls_spg_cuenta);
			  $la_data[$z]=array($ls_spg_cuenta,utf8_encode($ls_denominacion),number_format($ld_asignado,2,",","."),
			                     number_format($ld_aumento,2,",","."),number_format($ld_disminucion,2,",","."),
								 number_format($ld_monto_actualizado,2,",","."),number_format($ld_precompromiso,2,",","."),
								 number_format($ld_compromiso,2,",","."),number_format($ld_saldo_comprometer,2,",","."),
								 number_format($ld_causado,2,",","."),number_format($ld_pagado,2,",","."),
							     number_format($ld_por_paga,2,",","."));
             
			 $ls_cadena=$ls_spg_cuenta."/".$ls_denominacion."/".number_format($ld_asignado,2,",",".")."/".number_format($ld_aumento,2,",",".")."/".number_format($ld_disminucion,2,",",".")."/".number_format($ld_monto_actualizado,2,",",".")."/".number_format($ld_precompromiso,2,",",".")."/".number_format($ld_compromiso,2,",",".")."/".number_format($ld_saldo_comprometer,2,",",".")."/".number_format($ld_saldo_comprometer,2,",",".")."/".number_format($ld_causado,2,",",".")."/".number_format($ld_pagado,2,",",".")."/".number_format($ld_por_paga,2,",",".")."\r\n";
			 if ($lo_archivo)			
			 {
				@fwrite($lo_archivo,$ls_cadena);
			 }
			 
			 
			 $ld_aumento=0;
			 $ld_aumento_acumulado=0;
			 $ld_disminucion=0;
			 $ld_disminucion_acumulado=0;
			 $ld_precompromiso=0;
			 $ld_precompromiso_acumulado=0;
			 $ld_compromiso=0;
			 $ld_compromiso_acumulado=0;
			 $ld_causado=0;
			 $ld_causado_acumulado=0;
			 $ld_pagado=0;
			 $ld_pagado_acumulado = 0;
			 $ld_monto_actualizado=0;
			 $ld_monto_actualizado_acumulado=0;
			 $ld_saldo_comprometer=0;
			 $ld_saldo_comprometer_acumulado=0;
			 $ld_por_paga=0;
			 $ld_por_paga_acumulado=0;
			 		
			if($z==($li_tot-1))
			{
				  $ld_total_asignado=number_format($ld_total_asignado,2,",",".");
				  $ld_total_aumento=number_format($ld_total_aumento,2,",",".");
				  $ld_total_disminucion=number_format($ld_total_disminucion,2,",",".");
				  $ld_total_monto_actualizado=number_format($ld_total_monto_actualizado,2,",",".");
				  $ld_total_precompromiso=number_format($ld_total_precompromiso,2,",",".");
				  $ld_total_compromiso=number_format($ld_total_compromiso,2,",",".");
				  $ld_total_saldo_comprometer=number_format($ld_total_saldo_comprometer,2,",",".");
				  $ld_total_causado=number_format($ld_total_causado,2,",",".");
				  $ld_total_pagado=number_format($ld_total_pagado,2,",",".");
				  $ld_total_por_paga=number_format($ld_total_por_paga,2,",",".");
		 
				  $la_data_tot[$z]=array('TOTAL Bs.',$ld_total_asignado,$ld_total_aumento,$ld_total_disminucion,
				  						$ld_total_monto_actualizado,$ld_total_precompromiso,$ld_total_compromiso,
										$ld_total_saldo_comprometer,$ld_total_causado,$ld_total_pagado,$ld_total_por_paga);
			}//if
			  
			  
			  $rs_data->MoveNext();	
			  $z=$z+1;
		}//fin del while 
		
		
		if($lb_valido2)
		{
			$io_tcpdf->uf_print_cabecera_acumulado();
			$io_tcpdf->uf_print_detalle_acumulado($la_data); // Imprimimos el detalle 
			$io_tcpdf->uf_print_total_acumulado($la_data_tot);//Bs		
			unset($la_data);
			unset($la_data_tot);			
			$io_tcpdf->Output("sigesp_spg_rpp_ejecucion_financiera_mensual.pdf", "I");	
			unset($io_tcpdf);
		}
		else		
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay nada que reportar');"); 
			//	print(" close();");
				print("</script>");
		   }
		
		}
			
		
	   
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);		
?> 
