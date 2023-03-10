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
	// para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo =  tempnam("/tmp", "spg_mayor_analitico.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_reporte.php");
		$io_report = new sigesp_spg_reporte();
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
   		$ldt_periodo		= $_SESSION["la_empresa"]["periodo"];
		$li_ano				= substr($ldt_periodo,0,4);
		$li_estmodest		= $_SESSION["la_empresa"]["estmodest"];
		$ls_loncodestpro1   = $_SESSION["la_empresa"]["loncodestpro1"];
		$ls_loncodestpro2   = $_SESSION["la_empresa"]["loncodestpro2"];
		$ls_loncodestpro3   = $_SESSION["la_empresa"]["loncodestpro3"];
		$ls_loncodestpro4   = $_SESSION["la_empresa"]["loncodestpro4"];
		$ls_loncodestpro5   = $_SESSION["la_empresa"]["loncodestpro5"];
		$ls_codestpro1_min  = $_GET["codestpro1"];
		$ls_codestpro2_min  = $_GET["codestpro2"];
		$ls_codestpro3_min  = $_GET["codestpro3"];
		$ls_codestpro1h_max = $_GET["codestpro1h"];
		$ls_codestpro2h_max = $_GET["codestpro2h"];
		$ls_codestpro3h_max = $_GET["codestpro3h"];
	    $ls_estclades       = $_GET["estclades"];
	    $ls_estclahas       = $_GET["estclahas"];
		$ls_tipoformato=1;
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
					$ls_codestpro1  = trim($ls_codestpro1_min);
					$ls_codestpro2  = trim($ls_codestpro2_min);
					$ls_codestpro3  = trim($ls_codestpro3_min);
					$ls_codestpro4  = trim($ls_codestpro4_min);
					$ls_codestpro5  = trim($ls_codestpro5_min);
			  }
			}
			else
			{
			 if($ls_codestpro1_min<>"")
		     {	
			  $ls_codestpro1_min=$io_funciones->uf_cerosizquierda($ls_codestpro1_min,25);
			  $ls_codestpro1= $ls_codestpro1_min;	
			 }
		     else
		     {	
				$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro1($ls_codestpro1_min,$ls_estclades);
				$ls_codestpro1_min=$arrResultado['as_codestpro1'];
				$ls_estclades=$arrResultado['as_estclades'];
				$lb_valido=$arrResultado['lb_valido'];
			  $ls_codestpro1=$ls_codestpro1_min;
		     }
			 
			 if($ls_codestpro2_min<>"")
		     {	
			  $ls_codestpro2_min=$io_funciones->uf_cerosizquierda($ls_codestpro2_min,25);
			  $ls_codestpro2= $ls_codestpro2_min;	
			 }
		     else
		     {	
			  $arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro2($ls_codestpro1_min,$ls_codestpro2_min,$ls_estclades);
			  $ls_codestpro2_min=$arrResultado['as_codestpro2'];
			  $lb_valido=$arrResultado['lb_valido'];
			  $ls_codestpro1=$ls_codestpro1_min;
		     }
			 
			 if($ls_codestpro3_min<>"")
		     {	
			  $ls_codestpro3_min=$io_funciones->uf_cerosizquierda($ls_codestpro3_min,25);
			  $ls_codestpro3= $ls_codestpro3_min;	
			 }
		     else
		     {	
			    $arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro3($ls_codestpro1_min,$ls_codestpro2_min,$ls_codestpro3_min,$ls_estclades);
				$ls_codestpro3_min=$arrResultado['as_codestpro3'];
				$lb_valido=$arrResultado['lb_valido'];
			    $ls_codestpro3=$ls_codestpro3_min;
		     }
			 
					$ls_codestpro1  = trim($ls_codestpro1_min);
					$ls_codestpro2  = trim($ls_codestpro2_min);
					$ls_codestpro3  = trim($ls_codestpro3_min);
					$ls_codestpro4  = trim($ls_codestpro4_min);
					$ls_codestpro5  = trim($ls_codestpro5_min);
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
					$ls_codestpro1h  = trim($ls_codestpro1h_max);
					$ls_codestpro2h  = trim($ls_codestpro2h_max);
					$ls_codestpro3h  = trim($ls_codestpro3h_max);
					$ls_codestpro4h  = trim($ls_codestpro4h_max);
					$ls_codestpro5h  = trim($ls_codestpro5h_max);
			  }
			}
			else
			{
					$ls_codestpro1h  = trim($ls_codestpro1h_max);
					$ls_codestpro2h  = trim($ls_codestpro2h_max);
					$ls_codestpro3h  = trim($ls_codestpro3h_max);
					$ls_codestpro4h  = trim($ls_codestpro4h_max);
					$ls_codestpro5h  = trim($ls_codestpro5h_max);
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
        $fecdes=$_GET["txtfecdes"];
	    $ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
        $fechas=$_GET["txtfechas"];
	    $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
		$ls_orden=$_GET["rborden"];
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
		$ls_programatica_desde=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
		$ls_programatica_hasta=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h;
		/////////////////////////////////         SEGURIDAD               ///////////////////////////////////
		$ls_desc_event="Solicitud de Reporte Mayor Analitico desde la fecha ".$fecdes." hasta ".$fechas." ,Desde la programatica ".$ls_programatica_desde."  hasta ".$ls_programatica_hasta." , Desde la Cuenta ".$ls_cuentades." hasta la ".$ls_cuentahas;
		$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_mayor_analitico.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////
//----------------------------------------------------  Par?metros del encabezado  ---------------------------------------------
		$ls_titulo=" MAYOR ANALITICO  DESDE  ".$fecdes."  AL  ".$fechas." "; 
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
	
	$lb_codfuefin = true;
	if (!empty($ls_codfuefindes) && !empty($ls_codfuefinhas))
	{
		if ($ls_codfuefindes<>'--')
		{
			$lb_codfuefin = false;
		}
	}
	 
     $arrResultado=$io_report->uf_spg_reporte_select_mayor_analitico($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                                              $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
																  $ls_codestpro4h,$ls_codestpro5h,$ldt_fecdes,$ldt_fechas,
																  $ls_cuentades,$ls_cuentahas,$ls_orden,$rs_data,$ls_codfuefindes,
																  $ls_codfuefinhas,$ls_estclades,$ls_estclahas);														  
	 $rs_data=$arrResultado['rs_data'];
	 $lb_valido=$arrResultado['lb_valido'];
	 if($lb_valido==false) // Existe alg?n error ? no hay registros
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
		$lo_hoja->set_column(0,5,40);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(7,7,30);
		$lo_hoja->set_column(8,8,15);
		$lo_hoja->set_column(9,9,20);
		$lo_hoja->set_column(10,10,30);
		$lo_hoja->set_column(11,11,20);
		$lo_hoja->set_column(12,12,40);
		$lo_hoja->set_column(13,13,50);
		$lo_hoja->set_column(14,22,30);
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		 $lo_hoja->write(3, 0, "Proyecto/Accion",$lo_titulo);
		 $lo_hoja->write(3, 1, "Accion Especifica",$lo_titulo);
		 $lo_hoja->write(3, 2, "Centro de Costo",$lo_titulo);
		 $lo_hoja->write(3, 3, "Fuente de Financiamiento",$lo_titulo);
		 $lo_hoja->write(3, 4, "Localidad",$lo_titulo);
		 $lo_hoja->write(3, 5, "Estructura",$lo_titulo);
		 $lo_hoja->write(3, 6, "Cuenta",$lo_titulo);
		 $lo_hoja->write(3, 7, "Denominacion",$lo_titulo);
		 $lo_hoja->write(3, 8, "Fecha",$lo_titulo);
		 $lo_hoja->write(3, 9, "Comprobante",$lo_titulo);			
		 $lo_hoja->write(3, 10, "Documento",$lo_titulo);
		 $lo_hoja->write(3, 11, "Procede",$lo_titulo);
		 $lo_hoja->write(3, 12, "Proveedor/Beneficiario",$lo_titulo);
		 $lo_hoja->write(3, 13, "Detalle",$lo_titulo);
		 $lo_hoja->write(3, 14, "Asignado",$lo_titulo);
		 $lo_hoja->write(3, 15, "Aumento",$lo_titulo);
		 $lo_hoja->write(3, 16, "Disminucion",$lo_titulo);
		 $lo_hoja->write(3, 17, "Monto Actualizado",$lo_titulo);
		 $lo_hoja->write(3, 18, "Pre Comprometido",$lo_titulo);
		 $lo_hoja->write(3, 19, "Comprometido",$lo_titulo);
		 $lo_hoja->write(3, 20, "Causado",$lo_titulo);
		 $lo_hoja->write(3, 21, "Pagado",$lo_titulo);
		 $lo_hoja->write(3, 22, "Por Pagar",$lo_titulo);  
		$li_row=3;
		while(!$rs_data->EOF)
		{
			$ld_total_asignado=0;
			$ld_total_aumento=0;
			$ld_total_disminucion=0;
			$ld_total_monto_actualizado=0;
			$ld_total_compromiso=0;
			$ld_total_precompromiso=0;
			$ld_total_compromiso=0;
			$ld_total_causado=0;
			$ld_total_pagado=0;
			$ld_total_por_paga=0;
            $ld_total_saldo_comprometer=0;
			$li_tmp=0;
			$ld_monto_actualizado=0;
			$ld_totspg_asignado=0;
			$ld_totspg_aumento=0;
			$ld_totspg_disminucion=0;
			$ld_totspg_monto_actualizado=0;
			$ld_totspg_compromiso=0;
			$ld_totspg_precompromiso=0;
			$ld_totspg_causado=0;
			$ld_totspg_pagado=0;
			$ld_totspg_por_pagar=0;
			$ls_programatica=$rs_data->fields["programatica"];
			$ls_estcla=substr($ls_programatica,-1);
		    $ls_codestpro1=substr($ls_programatica,0,25);
			$ls_denestpro1="";
		    $arrResultado=$io_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
			$ls_denestpro1=$arrResultado['as_denestpro1'];
			$lb_valido=$arrResultado['lb_valido'];
		    if($lb_valido)
		    {
			  $ls_denestpro1=$ls_denestpro1;
		    }
		    $ls_codestpro2=substr($ls_programatica,25,25);
			if($lb_valido)
		    {
			  $ls_denestpro2="";
			  $arrResultado=$io_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
		      $ls_denestpro2=$arrResultado['as_denestpro2'];
			  $lb_valido=$arrResultado['lb_valido'];
			  $ls_denestpro2=$ls_denestpro2;
		    }
		    $ls_codestpro3=substr($ls_programatica,50,25);
			if($lb_valido)
		    {
			  $ls_denestpro3="";
			  $arrResultado=$io_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
			  $ls_denestpro3=$arrResultado['as_denestpro3'];
			  $lb_valido=$arrResultado['lb_valido'];
			  $ls_denestpro3=$ls_denestpro3;
		    }
			if($li_estmodest==2)
			{
				$ls_codestpro4=substr($ls_programatica,75,25);
				if($lb_valido)
				{
				  $ls_denestpro4="";
				  $arrResultado=$io_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
				  $ls_denestpro4=$arrResultado['as_denestpro4'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro4=$ls_denestpro4;
				}
				$ls_codestpro5=substr($ls_programatica,100,25);
				if($lb_valido)
				{
				  $ls_denestpro5="";
				  $arrResultado=$io_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
				  $ls_denestpro5=$arrResultado['as_denestpro5'];
				  $lb_valido=$arrResultado['lb_valido'];
				  $ls_denestpro5=$ls_denestpro5;
				}
			    $ls_denestpro=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3." , ".$ls_denestpro4." , ".$ls_denestpro5;
			    $ls_programatica_mostrar=substr($ls_codestpro1,-$ls_loncodestpro1)."-".substr($ls_codestpro2,-$ls_loncodestpro2)."-".substr($ls_codestpro3,-$ls_loncodestpro3)."-".substr($ls_codestpro4,-$ls_loncodestpro4)."-".substr($ls_codestpro5,-$ls_loncodestpro5);
				
			}
			else
			{
                $ls_programatica=substr($ls_codestpro1,-$ls_loncodestpro1).substr($ls_codestpro2,-$ls_loncodestpro2).substr($ls_codestpro3,-$ls_loncodestpro3);
				$ls_denestpro = array();
				$ls_denestpro[0]=$ls_denestpro1;
				$ls_denestpro[1]=$ls_denestpro2;
				$ls_denestpro[2]=$ls_denestpro3;
			}
			$rs_data2="";
			$arrResultado=$io_report->uf_spg_reporte_mayor_analitico2($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
			                                                      $ls_codestpro5,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																  $ls_codestpro4,$ls_codestpro5,$ldt_fecdes,$ldt_fechas,
																  $ls_cuentades,$ls_cuentahas,$ls_orden,$ls_estcla,$ls_codfuefindes,
																  $ls_codfuefinhas,$rs_data2); 
			$rs_data2=$arrResultado['rs_data'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{  
			    /*$li_totrow_det=$rs_data2->RecordCount();
				if($li_totrow_det>0)
				{
		         	  $li_row=$li_row+1;
					  if ($li_estmodest == 2)
					  {
						$lo_hoja->write($li_row, 0, "Programatica",$lo_titulo);
						$lo_hoja->write($li_row, 1, $ls_programatica_mostrar,$lo_datacenter);
						$li_row=$li_row+1;
						$lo_hoja->write($li_row, 2, $ls_denestpro,$lo_libro->addformat(array('font'=>'Verdana','align'=>'left','size'=>'9')));					
					 }
					 else
					 {
						$titulo1=substr($ls_programatica,0,$ls_loncodestpro1).' '.$ls_denestpro[0];
						$titulo2=substr($ls_programatica,$ls_loncodestpro1,$ls_loncodestpro2).' '.$ls_denestpro[1];
						$titulo3=substr($ls_programatica,$ls_loncodestpro1+$ls_loncodestpro2,$ls_loncodestpro3).' '.$ls_denestpro[2];
							
						$lo_hoja->write($li_row, 0, "Estructura Programatica",$lo_titulo);
						$li_row=$li_row+1;
						$lo_hoja->write($li_row, 0, $titulo1,$lo_datacenter);
						$li_row=$li_row+1;
						$lo_hoja->write($li_row, 0, $titulo2,$lo_datacenter);
						$li_row=$li_row+1;
						$lo_hoja->write($li_row, 0, $titulo3,$lo_datacenter);
						
					}	
				}*/
				$entro=false;
				$ls_antasig=0; 
				$ls_antaum=0;
				$ls_antdis=0;									
				$ls_antpre=0;
				$ls_antcom=0;
				$ls_antcau=0;
				$ls_antpagado=0;
				$ls_antpagar=0;	
				$ls_antmon_act=0;
				$ls_spg_cuenta_aux="";
				while(!$rs_data2->EOF)
				{
				  
				  $ls_programatica=$rs_data2->fields["codestpro1"].$rs_data2->fields["codestpro2"].$rs_data2->fields["codestpro3"].$rs_data2->fields["codestpro4"].$rs_data2->fields["codestpro5"];   
				  $ls_codestpro1=$rs_data2->fields["codestpro1"];
				  $ls_codestpro2=$rs_data2->fields["codestpro2"];
				  $ls_codestpro3=$rs_data2->fields["codestpro3"];
				  $ls_codestpro4=$rs_data2->fields["codestpro4"];
			      $ls_codestpro5=$rs_data2->fields["codestpro5"];
				  $ls_estcla=$rs_data2->fields["estcla"];
							  
				  $ls_nombre_prog=$rs_data2->fields["nombre_prog"];
				  $ls_spg_cuenta=$rs_data2->fields["spg_cuenta"];
				  $ls_denominacion=$rs_data2->fields["denominacion"];
				 /* 
				  if (($ls_spg_cuenta!=$ls_spg_cuenta_aux)||($li_tmp==0))
				  {
				  	 $li_row=$li_row+1;
					 $lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
					 $lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter);
					 $lo_hoja->write($li_row, 2, $ls_denominacion,$lo_libro->addformat(array('font'=>'Verdana','align'=>'left','size'=>'9')));
					 $li_row=$li_row+1; 
				     $lo_hoja->write($li_row, 0, "Fecha",$lo_titulo);
					 $lo_hoja->write($li_row, 1, "Comprobante",$lo_titulo);			
					 $lo_hoja->write($li_row, 2, "Documento",$lo_titulo);
					 $lo_hoja->write($li_row, 3, "Procede",$lo_titulo);
					 $lo_hoja->write($li_row, 4, "Proveedor/Beneficiario",$lo_titulo);
					 $lo_hoja->write($li_row, 5, "Detalle",$lo_titulo);
					 $lo_hoja->write($li_row, 6, "Asignado",$lo_titulo);
					 $lo_hoja->write($li_row, 7, "Aumento",$lo_titulo);
					 $lo_hoja->write($li_row, 8, "Disminucion",$lo_titulo);
					 $lo_hoja->write($li_row, 9, "Monto Actualizado",$lo_titulo);
					 $lo_hoja->write($li_row, 10, "Pre Comprometido",$lo_titulo);
					 $lo_hoja->write($li_row, 11, "Comprometido",$lo_titulo);
					 $lo_hoja->write($li_row, 12, "Causado",$lo_titulo);
					 $lo_hoja->write($li_row, 13, "Pagado",$lo_titulo);
					 $lo_hoja->write($li_row, 14, "Por Pagar",$lo_titulo);  
				  }	
				  */
				 
				  
				 
				  if (($li_tmp)<$li_totrow_det)
				  {
						$rs_data2->MoveNext();
						$ls_spg_cuenta_next=$rs_data2->fields["spg_cuenta"];  
						$rs_data2->Move($li_tmp);
				  }
				  elseif(($li_tmp)==$li_totrow_det)
				  {
				        $ls_spg_cuenta_next=$rs_data2->fields["spg_cuenta"];  
						
				  }
				  
				  
				  
				  //PARA BUSCAR LOS SALDOS ANTERIORES
				  if  (!$entro)
				  {
				       $entro=true;
					   $rs_data3="";
					   $arrResultado=$io_report->uf_spg_calcular_saldo_anterior ($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
					   														  $ls_codestpro4, $ls_codestpro5,$ldt_fecdes,
																	          $ls_spg_cuenta,$ls_estcla,$rs_data3); 				 
					   $rs_data3=$arrResultado['rs_data'];
					   $lb_valido=$arrResultado['lb_valido'];
						 $li_num=$rs_data3->RecordCount();
						
						 if (($li_num>=1) && ($lb_valido))
						 {  
							$ld_asignado2=0;
							$ld_aumento2=0;
							$ld_disminucion2=0;
							$ld_precompromiso2=0;
							$ld_compromiso2=0;
							$ld_causado2=0;
							$ld_pagado2=0;
							$ld_por_paga2=0;
							$ld_monto_actualizado=0;							
							while (!$rs_data3->EOF)
							{
							
								if ($lb_codfuefin)
								{
									$asignado = $rs_data3->fields["asignar"];
								}
								else
								{
									$asignado = $io_report->buscarAsignadoFuente($ls_programatica.$ls_estcla, $ls_spg_cuenta, 11, $ls_codfuefindes, $ls_codfuefinhas);
								}

								$ld_asignado2=$ld_asignado2+$asignado;
								$ld_aumento2=$ld_aumento2+$rs_data3->fields["aumento"];
								$ld_disminucion2=$ld_disminucion2+$rs_data3->fields["disminucion"];
								$ld_precompromiso2=$ld_precompromiso2+$rs_data3->fields["precompromiso"];
								$ld_compromiso2=$ld_compromiso2+$rs_data3->fields["compromiso"];
								$ld_causado2=$ld_causado2+$rs_data3->fields["causado"];
								$ld_pagado2=$ld_pagado2+$rs_data3->fields["pago"];
								//$ld_por_paga2=$ld_por_paga2+($ld_causado2-$ld_pagado2);	
									
									$ls_antasig=$ld_asignado2; 
									$ls_antaum=$ld_aumento2;
									$ls_antdis=$ld_disminucion2;									
									$ls_antpre=$ld_precompromiso2;
									$ls_antcom=$ld_compromiso2;
									$ls_antcau=$ld_causado2;
									$ls_antpagado=$ld_pagado2;
									$ls_antpagar=$ld_por_paga2;					
								 
								$rs_data3->MoveNext();
							 }
							 
							 
							    $ld_por_paga2 = $ld_causado2-$ld_pagado2;
								$ld_monto_actualizado=$ld_monto_actualizado+$ld_asignado2+$ld_aumento2-$ld_disminucion2;
								
								$ls_antmon_act=$ld_monto_actualizado;
								
								$ld_total_asignado=$ld_total_asignado+$ld_asignado2;
								$ld_total_aumento=$ld_total_aumento+$ld_aumento2;
								$ld_total_disminucion=$ld_total_disminucion+$ld_disminucion2;
								$ld_total_precompromiso=$ld_total_precompromiso+$ld_precompromiso2;
								$ld_total_compromiso=$ld_total_compromiso+$ld_compromiso2;
								$ld_total_causado=$ld_total_causado+$ld_causado2;
								$ld_total_pagado=$ld_total_pagado+$ld_pagado2;
								$ld_total_por_paga=$ld_total_por_paga+$ld_por_paga2;							
								$ld_por_comprometer=$ld_monto_actualizado-$ld_precompromiso2-$ld_compromiso2; 
								
								$ld_totspg_asignado          = $ld_totspg_asignado + $ld_asignado2;
							    $ld_totspg_aumento           = $ld_totspg_aumento +  $ld_aumento2;
							    $ld_totspg_disminucion       = $ld_totspg_disminucion + $ld_disminucion2;
							    $ld_totspg_monto_actualizado = $ld_monto_actualizado;
							    $ld_totspg_compromiso        = $ld_totspg_compromiso + $ld_compromiso2;
							    $ld_totspg_precompromiso     = $ld_totspg_precompromiso + $ld_precompromiso2;
							    $ld_totspg_causado           = $ld_totspg_causado + $ld_causado2;
							    $ld_totspg_pagado            = $ld_totspg_pagado + $ld_pagado2;
							    $ld_totspg_por_pagar         = $ld_totspg_por_pagar + $ld_por_paga2;
																										
								 /*$li_row=$li_row+1; 
								 $lo_hoja->write($li_row, 0, $ls_programatica_mostrar,$lo_datacenter); 
								 $lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter); 
								 $lo_hoja->write($li_row, 2, '',$lo_datacenter); 
								 $lo_hoja->write($li_row, 3, '',$lo_datacenter);
								 $lo_hoja->write($li_row, 4, '',$lo_dataleft);
								 $lo_hoja->write($li_row, 5, '',$lo_dataleft);
								 $lo_hoja->write($li_row, 6, '',$lo_dataleft);
								 $lo_hoja->write($li_row, 7, 'SALDOS ANTERIORES',$lo_dataleft);
								 $lo_hoja->write($li_row, 8, $ld_asignado2,$lo_dataright);
								 $lo_hoja->write($li_row, 9, $ld_aumento2,$lo_dataright);
								 $lo_hoja->write($li_row, 10, $ld_disminucion2,$lo_dataright);
								 $lo_hoja->write($li_row, 11, $ld_monto_actualizado2,$lo_dataright);
								 $lo_hoja->write($li_row, 12, $ld_precompromiso2,$lo_dataright);
								 $lo_hoja->write($li_row, 13, $ld_compromiso2,$lo_dataright);
								 $lo_hoja->write($li_row, 14, $ld_causado2,$lo_dataright);
								 $lo_hoja->write($li_row, 15, $ld_pagado2,$lo_dataright);
								 $lo_hoja->write($li_row, 16, $ld_por_paga2,$lo_dataright);*/
							  
							   
						 }
						 else if ($lb_valido)
						 {
							/*$li_row=$li_row+1; 
							 $lo_hoja->write($li_row, 0, $ls_programatica_mostrar,$lo_datacenter); 
							 $lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter); 
							 $lo_hoja->write($li_row, 2, '',$lo_datacenter); 
							 $lo_hoja->write($li_row, 3, '',$lo_datacenter);
							 $lo_hoja->write($li_row, 4, '',$lo_dataleft);
							 $lo_hoja->write($li_row, 5, '',$lo_dataleft);
							 $lo_hoja->write($li_row, 6, '',$lo_dataleft);
							 $lo_hoja->write($li_row, 7, 'SALDOS ANTERIORES',$lo_dataleft);
							 $lo_hoja->write($li_row, 8, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 9, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 10, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 11, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 12,0,$lo_dataright);
							 $lo_hoja->write($li_row, 13, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 14, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 15, 0,$lo_dataright);
							 $lo_hoja->write($li_row, 16, 0,$lo_dataright);*/
							$ld_asignado2=0;
							$ld_aumento2=0;
							$ld_disminucion2=0;
							$ld_precompromiso2=0;
							$ld_compromiso2=0;
							$ld_causado2=0;
							$ld_pagado2=0;
							$ld_por_paga2=0;
							$ld_monto_actualizado=0;
							
						 
						 }
				 }
				  
				  $ls_tipodest=$rs_data2->fields["tipo_destino"];
				  $ls_nombene=utf8_encode($rs_data2->fields["nombene"]);
				  $ls_apebene=utf8_encode($rs_data2->fields["apebene"]);
				  $ls_nompro=utf8_encode($rs_data2->fields["nompro"]);
				  if ($ls_tipodest=='B')
				  {
				  	$ls_nomproben=$ls_apebene." ".$ls_nombene;
				  }
				  else
				  {
				  	$ls_nomproben=$ls_nompro;
				  }				  
				   
				  $fecha=$rs_data2->fields["fecha"];
				  $ls_fecha=$io_funciones->uf_convertirfecmostrar($fecha);
				  $ls_procede=$rs_data2->fields["procede"]; 
				  $ls_procede_doc=$rs_data2->fields["procede_doc"];
				  $ls_comprobante=$rs_data2->fields["comprobante"];
				  $ls_documento=$rs_data2->fields["documento"];
				  $ls_descripcion=$rs_data2->fields["nombre_prog"];
					if ($lb_codfuefin)
					{
						$asignado = $rs_data2->fields["asignar"];
					}
					else
					{
						$asignado = $io_report->buscarAsignadoFuente($ls_programatica.$ls_estcla, $ls_spg_cuenta, 11, $ls_codfuefindes, $ls_codfuefinhas);
					}
				  
				  $ld_asignado=$asignado;
				  $ld_aumento=$rs_data2->fields["aumento"];
				  $ld_disminucion=$rs_data2->fields["disminucion"];
				  
				  $ld_monto_actualizado=$ld_monto_actualizado+$ld_asignado+$ld_aumento-$ld_disminucion;
				
				  $ld_precompromiso=$rs_data2->fields["precompromiso"];
				  $ld_compromiso=$rs_data2->fields["compromiso"];
				  $ld_causado=$rs_data2->fields["causado"];
				  $ld_pagado=$rs_data2->fields["pago"];
				  
				  $ld_por_paga=$ld_causado-$ld_pagado;
				  $ld_total_asignado=$ld_total_asignado+$ld_asignado;
				  $ld_total_aumento=$ld_total_aumento+$ld_aumento;
				  $ld_total_disminucion=$ld_total_disminucion+$ld_disminucion;
				  $ld_total_precompromiso=$ld_total_precompromiso+$ld_precompromiso;
				  $ld_total_compromiso=$ld_total_compromiso+$ld_compromiso;
				  $ld_total_causado=$ld_total_causado+$ld_causado;
				  $ld_total_pagado=$ld_total_pagado+$ld_pagado;
				  $ld_total_por_paga=$ld_total_por_paga+$ld_por_paga;
				  $ld_por_comprometer=$ld_monto_actualizado-$ld_precompromiso-$ld_compromiso; 
				  
				  $ld_totspg_asignado          = $ld_totspg_asignado + $ld_asignado;
				  $ld_totspg_aumento           = $ld_totspg_aumento +  $ld_aumento;
				  $ld_totspg_disminucion       = $ld_totspg_disminucion + $ld_disminucion;
				  $ld_totspg_monto_actualizado = $ld_monto_actualizado;
				  $ld_totspg_compromiso        = $ld_totspg_compromiso + $ld_compromiso;
				  $ld_totspg_precompromiso     = $ld_totspg_precompromiso + $ld_precompromiso;
				  $ld_totspg_causado           = $ld_totspg_causado + $ld_causado;
				  $ld_totspg_pagado            = $ld_totspg_pagado + $ld_pagado;
				  $ld_totspg_por_pagar         = $ld_totspg_por_pagar + $ld_por_paga;
				  
				  $li_row=$li_row+1; 
				  $lo_hoja->write($li_row, 0, substr($ls_codestpro1,-$ls_loncodestpro1)." ",$lo_datacenter); 
				  $lo_hoja->write($li_row, 1, substr($ls_codestpro2,-$ls_loncodestpro2)." ",$lo_datacenter); 
				  $lo_hoja->write($li_row, 2, substr($ls_codestpro3,-$ls_loncodestpro3)." ",$lo_datacenter); 
				  $lo_hoja->write($li_row, 3, substr($ls_codestpro4,-$ls_loncodestpro4)." ",$lo_datacenter); 
				  $lo_hoja->write($li_row, 4, substr($ls_codestpro5,-$ls_loncodestpro5)." ",$lo_datacenter); 
				  $lo_hoja->write($li_row, 5, $ls_programatica_mostrar,$lo_datacenter); 
				  $lo_hoja->write($li_row, 6, $ls_spg_cuenta,$lo_datacenter); 
				  $lo_hoja->write($li_row, 7, $ls_denominacion,$lo_datacenter); 
				  $lo_hoja->write($li_row, 8, $ls_fecha,$lo_datacenter); 
				  $lo_hoja->write($li_row, 9, $ls_comprobante." ",$lo_datacenter);
				  $lo_hoja->write($li_row, 10, $ls_documento,$lo_dataleft);
				  $lo_hoja->write($li_row, 11, $ls_procede,$lo_dataleft);
				  $lo_hoja->write($li_row, 12, $ls_nomproben,$lo_dataleft);              //nombre proveedor-beneficiario
				  $lo_hoja->write($li_row, 13, $ls_descripcion." ",$lo_dataleft);
				  $lo_hoja->write($li_row, 14, $ld_asignado,$lo_dataright);
				  $lo_hoja->write($li_row, 15, $ld_aumento,$lo_dataright);
				  $lo_hoja->write($li_row, 16, $ld_disminucion,$lo_dataright);
				  $lo_hoja->write($li_row, 17, $ld_monto_actualizado,$lo_dataright);
				  $lo_hoja->write($li_row, 18, $ld_precompromiso,$lo_dataright);
				  $lo_hoja->write($li_row, 19, $ld_compromiso,$lo_dataright);
				  $lo_hoja->write($li_row, 20, $ld_causado,$lo_dataright);
				  $lo_hoja->write($li_row, 21, $ld_pagado,$lo_dataright);
				  $lo_hoja->write($li_row, 22, $ld_por_paga,$lo_dataright);
				 
				 
			      if(($ls_spg_cuenta!=$ls_spg_cuenta_next)&& ($li_tmp!=($li_totrow_det-1)))
			      {
					  
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado;// Agregado por Ing. Nelson Barraez 20-12-2006							  
					 $ld_monto_actualizado=0;
					 $entro=false;

					 
			
					 //---------- SUB TOTAL POR PERIODO DE LA CUENTA-----------------------------------
					 $ls_sub_asignado=abs($ld_totspg_asignado-$ls_antasig);
					 $ls_subaumento=abs($ld_totspg_aumento-$ls_antaum); 
					 $ls_subdisminucion=abs($ld_totspg_disminucion-$ls_antdis);
					 $ls_submonto_actual=abs($ld_totspg_monto_actualizado-$ls_antmon_act);
					 $ls_subprecompromiso=abs($ld_totspg_precompromiso-$ls_antpre);
					 $ls_subcompromiso=abs($ld_totspg_compromiso-$ls_antcom);
					 $ls_subcausado=abs($ld_totspg_causado-$ls_antcau);
					 $ls_subpagar=abs($ld_totspg_por_pagar-$ls_antpagar);	
					 $ls_subpagado=abs($ld_totspg_pagado-$ls_antpagado);					 
					
					/* $li_row=$li_row+1;
					$lo_hoja->write($li_row, 5, "SUB TOTAL CUENTA ".$ls_spg_cuenta,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
					$lo_hoja->write($li_row, 6, $ls_sub_asignado,$lo_dataright);
					$lo_hoja->write($li_row, 7, $ls_subaumento,$lo_dataright);
					$lo_hoja->write($li_row, 8, $ls_subdisminucion,$lo_dataright);
					$lo_hoja->write($li_row, 9, $ls_submonto_actual,$lo_dataright);
					$lo_hoja->write($li_row, 10, $ls_subprecompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 11, $ls_subcompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 12, $ls_subcausado,$lo_dataright);
					$lo_hoja->write($li_row, 13, $ls_subpagado,$lo_dataright);
					$lo_hoja->write($li_row, 14, $ls_subpagar,$lo_dataright);*/
					 				  
					 $ls_sub_asignado          =0;
					 $ls_subaumento            =0;
					 $ls_subdisminucion        =0;
				     $ls_submonto_actual       =0;
					 $ls_subcompromiso         =0;
					 $ls_subprecompromiso      =0;
					 $ls_subcausado            =0;
					 $ls_subpagar              =0;
					 $ls_subpagado             =0;
					 //------------------------------------------------------------------------------------------
										  
					
					/*$li_row=$li_row+1;
					$lo_hoja->write($li_row, 5, "TOTAL CUENTA ".$ls_spg_cuenta,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
					$lo_hoja->write($li_row, 6, $ld_totspg_asignado,$lo_dataright);
					$lo_hoja->write($li_row, 7, $ld_totspg_aumento,$lo_dataright);
					$lo_hoja->write($li_row, 8, $ld_totspg_disminucion,$lo_dataright);
					$lo_hoja->write($li_row, 9, $ld_totspg_monto_actualizado,$lo_dataright);
					$lo_hoja->write($li_row, 10, $ld_totspg_precompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 11, $ld_totspg_compromiso,$lo_dataright);
					$lo_hoja->write($li_row, 12, $ld_totspg_causado,$lo_dataright);
					$lo_hoja->write($li_row, 13, $ld_totspg_pagado,$lo_dataright);
					$lo_hoja->write($li_row, 14, $ld_totspg_por_pagar,$lo_dataright);*/	
					 
					 
					 $ld_totspg_asignado=0;
			         $ld_totspg_aumento=0;
			         $ld_totspg_disminucion=0;
			         $ld_totspg_monto_actualizado=0;
			         $ld_totspg_compromiso=0;
			         $ld_totspg_precompromiso=0;
			         $ld_totspg_causado=0;
			         $ld_totspg_pagado=0;
			         $ld_totspg_por_pagar=0;
					 
				 }//if	 
				
				 
				if($li_tmp==($li_totrow_det-1))
				{
					 
					  $ld_total_monto_actualizado=$ld_total_monto_actualizado+$ld_monto_actualizado; 
					  $ld_total_saldo_comprometer=$ld_total_monto_actualizado-$ld_total_precompromiso-$ld_total_compromiso;

					 $ls_sub_asignado=abs($ld_totspg_asignado-$ls_antasig);
					 $ls_subaumento=abs($ld_totspg_aumento-$ls_antaum);
					 $ls_subdisminucion=abs($ld_totspg_disminucion-$ls_antdis);
					 $ls_submonto_actual=abs($ld_totspg_monto_actualizado-$ls_antmon_act);
					 $ls_subprecompromiso=abs($ld_totspg_precompromiso-$ls_antpre);
					 $ls_subcompromiso=abs($ld_totspg_compromiso-$ls_antcom);
					 $ls_subcausado=abs($ld_totspg_causado-$ls_antcau);
					 $ls_subpagar=abs($ld_totspg_por_pagar-$ls_antpagar);	
					 $ls_subpagado=abs($ld_totspg_pagado-$ls_antpagado);				 
					
					/*$li_row=$li_row+1;
					$lo_hoja->write($li_row, 5, "SUB TOTAL CUENTA ".$ls_spg_cuenta,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
					$lo_hoja->write($li_row, 6, $ls_sub_asignado,$lo_dataright);
					$lo_hoja->write($li_row, 7, $ls_subaumento,$lo_dataright);
					$lo_hoja->write($li_row, 8, $ls_subdisminucion,$lo_dataright);
					$lo_hoja->write($li_row, 9, $ls_submonto_actual,$lo_dataright);
					$lo_hoja->write($li_row, 10, $ls_subprecompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 11, $ls_subcompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 12, $ls_subcausado,$lo_dataright);
					$lo_hoja->write($li_row, 13, $ls_subpagado,$lo_dataright);
					$lo_hoja->write($li_row, 14, $ls_subpagar,$lo_dataright);*/
					 				  
					 $ls_sub_asignado          =0;
					 $ls_subaumento            =0;
					 $ls_subdisminucion        =0;
				     $ls_submonto_actual       =0;
					 $ls_subcompromiso         =0;
					 $ls_subprecompromiso      =0;
					 $ls_subcausado            =0;
					 $ls_subpagar              =0;
					 $ls_subpagado             =0;
					 
					/*$li_row=$li_row+1;
					$lo_hoja->write($li_row, 5, "TOTAL CUENTA ".$ls_spg_cuenta,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
					$lo_hoja->write($li_row, 6, $ld_totspg_asignado,$lo_dataright);
					$lo_hoja->write($li_row, 7, $ld_totspg_aumento,$lo_dataright);
					$lo_hoja->write($li_row, 8, $ld_totspg_disminucion,$lo_dataright);
					$lo_hoja->write($li_row, 9, $ld_totspg_monto_actualizado,$lo_dataright);
					$lo_hoja->write($li_row, 10, $ld_totspg_precompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 11, $ld_totspg_compromiso,$lo_dataright);
					$lo_hoja->write($li_row, 12, $ld_totspg_causado,$lo_dataright);
					$lo_hoja->write($li_row, 13, $ld_totspg_pagado,$lo_dataright);
					$lo_hoja->write($li_row, 14, $ld_totspg_por_pagar,$lo_dataright);*/	
					
					 $ld_totspg_asignado=0;
			         $ld_totspg_aumento=0;
			         $ld_totspg_disminucion=0;
			         $ld_totspg_monto_actualizado=0;
			         $ld_totspg_compromiso=0;
			         $ld_totspg_precompromiso=0;
			         $ld_totspg_causado=0;
			         $ld_totspg_pagado=0;
			         $ld_totspg_por_pagar=0;
					 
					/* $li_row=$li_row+1;
				 	$lo_hoja->write($li_row, 4, "Saldo Por Comprometer",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
					$lo_hoja->write($li_row, 5, $ld_total_saldo_comprometer,$lo_dataright);
					$lo_hoja->write($li_row, 6, $ld_total_asignado,$lo_dataright);
					$lo_hoja->write($li_row, 7, $ld_total_aumento,$lo_dataright);
					$lo_hoja->write($li_row, 8, $ld_total_disminucion,$lo_dataright);
					$lo_hoja->write($li_row, 9, $ld_total_monto_actualizado,$lo_dataright);
					$lo_hoja->write($li_row, 10, $ld_total_precompromiso,$lo_dataright);
					$lo_hoja->write($li_row, 11, $ld_total_compromiso,$lo_dataright);
					$lo_hoja->write($li_row, 12, $ld_total_causado,$lo_dataright);
					$lo_hoja->write($li_row, 13, $ld_total_pagado,$lo_dataright);
					$lo_hoja->write($li_row, 14, $ld_total_por_paga,$lo_dataright);	*/
					
					 
					 
				}//if
			   $ls_spg_cuenta_aux=$ls_spg_cuenta;
			   $rs_data2->MoveNext();
			   $li_tmp=$li_tmp+1;
			   
			}//while
			
            }//if
		 unset($la_data);
		 $rs_data->MoveNext();			
		}//while
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"spg_mayor_analitico.xls\"");
		header("Content-Disposition: inline; filename=\"spg_mayor_analitico.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}//else
?> 