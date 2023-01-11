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
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');
	
	// para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "spg_acumulado_x_cuentas.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	
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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_estado_resultado_estructura.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	 require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	 require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	 $io_funciones=new class_funciones();
	 require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	 require_once('../../../base/librerias/php/general/Json.php');
	 require_once('sigesp_spg_class_report.php');
	 $oGastos= new sigesp_spg_class_report();
	 $io_fecha=new class_fecha();
	require_once("class_funciones_scg.php");
	$io_fun_scg=new class_funciones_scg();
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if ($_GET['jasonest']) 	
	{
		$submit = str_replace("\\","",$_GET['jasonest']);
		//$submit = utf8_decode($submit);
		$json = new Services_JSON;
		$ArJson = $json->decode($submit);	
		
		if($ArJson->estructuras[0]->codestpro1=='todas')
		{
			$rsEst = $oGastos->uf_select_todasest();
				
		}
	}
	
	
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_scg_reporte.php");
			$io_report  = new sigesp_scg_reporte();
			$ls_bolivares ="Bs.";
			break;

		case "1":
			require_once("sigesp_scg_reportebsf.php");
			$io_report  = new sigesp_scg_reportebsf();
			$ls_bolivares ="Bs.F.";
			break;
	}
	 $ia_niveles_scg[0]="";			
	 uf_init_niveles();
	 $li_total=count((array)$ia_niveles_scg)-1;
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	 $ls_hidbot=$_GET["hidbot"];
	 if($ls_hidbot==true)
	 {
	   $ls_cmbmesdes=$_GET["cmbmesdes"];
	   $ls_cmbagnodes=$_GET["cmbagnodes"];
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	     $fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01";
	     $ldt_fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01";
	   }
	   else 
	   {
	     $fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01"." 00:00:00";
	     $ldt_fecdes=$ls_cmbagnodes."-".$ls_cmbmesdes."-01"." 00:00:00";
	   }
	   $ls_cmbmeshas=$_GET["cmbmeshas"];
	   $ls_cmbagnohas=$_GET["cmbagnohas"];
	   $ls_last_day=$io_fecha->uf_last_day($ls_cmbmeshas,$ls_cmbagnohas);
	   $fechas=$ls_last_day;
	   $ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day);
	 }
	 elseif($ls_hidbot==false)
	 {
		 $fecdes=$_GET["txtfecdes"];
		 $ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
		 $fechas=$_GET["txtfechas"];
		 $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	 }
	 $li_nivel=$_GET["cmbnivel"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ld_fecdes=$io_funciones->uf_convertirfecmostrar($fecdes);
		$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
		$ls_encabezado="ESTADO DE RESULTADOS";
		$ls_titulo1="".$ls_nombre." "; 
		$ls_titulo2=" al ".$ld_fechas."";
		$ls_titulo3="(Expresado en ".$ls_bolivares.")";  
       // $ls_titulo2=" del  ".$ld_fecdes."  al  ".$ld_fechas." </b>";
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte

		$lb_valido=uf_insert_seguridad("<b>Estado de Resultado en excel</b>"); // Seguridad de Reporte
		
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
                        $ls_loncodestpro1 = $_SESSION['la_empresa']['loncodestpro1'];
	 		$ls_loncodestpro2 = $_SESSION['la_empresa']['loncodestpro2'];
	 		$ls_loncodestpro3 = $_SESSION['la_empresa']['loncodestpro3'];
                
		$lo_hoja->write(0, 3,$ls_encabezado,$lo_titulo);
		$lo_hoja->write(1, 3,$ls_titulo2,$lo_titulo);
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
		$contlineas=0;
		$z=0;
		if(!is_object($rsEst))
		{
		$j=0;
		for($i=0;$i<count((array)$ArJson->estructuras);$i++)
		{	
			$auxEst="0";
			$ls_denestpro = array();
			$arestructuras[0]=str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",1);
			$arestructuras[1]=str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",1);
			$arestructuras[2]=str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",1);
			$arestructuras[3]=str_pad($auxEst,25,"0",1);
			$arestructuras[4]=str_pad($auxEst,25,"0",1);	
			$arestructuras[5]=$ArJson->estructuras[$i]->estcla;
			//$ls_programatica=$ArJson->estructuras[$i]->codestpro1.$ArJson->estructuras[$i]->codestpro2.$ArJson->estructuras[$i]->codestpro3;
			$ls_programatica[0]=str_pad($ArJson->estructuras[$i]->codestpro1,25,'0',0);
			$ls_programatica[1]=str_pad($ArJson->estructuras[$i]->codestpro2,25,'0',0);
			$ls_programatica[2]=str_pad($ArJson->estructuras[$i]->codestpro3,25,'0',0);			
                        $ls_denestpro[0]="";
                        $ls_denestpro[1]="";
                        $ls_denestpro[2]="";
			$ls_denestpro[0]=$oGastos->uf_spg_reporte_select_denestpro1($arestructuras[0],$ls_denestpro[0]);
			$ls_denestpro[1]=$oGastos->uf_spg_reporte_select_denestpro2($arestructuras[0],$arestructuras[1],$ls_denestpro[1]);
			$ls_denestpro[2]=$oGastos->uf_spg_reporte_select_denestpro3($arestructuras[0],$arestructuras[1],$arestructuras[2],$ls_denestpro[2]);
		if($lb_valido)
		{
			$lb_valido_ing=$io_report->uf_scg_reporte_estado_de_resultado_est_ingreso($ldt_fecdes,$ldt_fechas,$li_nivel,$arestructuras);
		
			$lb_valido_egr=$io_report->uf_scg_reporte_estado_de_resultado_est_egreso($ldt_fecdes,$ldt_fechas,$li_nivel,$arestructuras);
			
		}
		if((($lb_valido_ing==false)&&($lb_valido_egr==false))||($lb_valido==false)) // Existe algún error ó no hay registros
	    {
	    	continue;
	    }
		else// Imprimimos el reporte
		{
			
			$ls_tit1="ESTRUCTURA PRESUPUESTARIA";
			$ls_tit2a= substr($ls_programatica[0],25-$ls_loncodestpro1,$ls_loncodestpro1);
			$ls_tit2b= $ls_denestpro[0];
			$ls_tit3a= substr($ls_programatica[1],25-$ls_loncodestpro2,$ls_loncodestpro2);
			$ls_tit3b= $ls_denestpro[1];
			$ls_tit4a= substr($ls_programatica[2],25-$ls_loncodestpro3,$ls_loncodestpro3);
			$ls_tit4b= $ls_denestpro[2];
			$li_row++;
			$lo_hoja->write($li_row, 0,$ls_tit1,$lo_titulo);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit2a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit2b,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit3a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit3b,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit4a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit4b,$lo_dataleft);
			$li_row++;
			$lb_valido=true;		
			
		 if($lb_valido_ing)
		 {
		 	$lo_hoja->write($li_row, 0, "INGRESOS",$lo_titulo);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
			$lo_hoja->write($li_row, 1, "Denominación",$lo_titulo);
			$lo_hoja->write($li_row, 2, "Saldo",$lo_titulo);
			$lo_hoja->write($li_row, 3, "",$lo_titulo);
			$lo_hoja->write($li_row, 4, "",$lo_titulo);
			$li_row++;
		 	
		 	
			$li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
			$ld_total_ingresos = 0;
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				
				$ls_sc_cuenta=trim($io_report->dts_reporte->data["sc_cuenta"][$li_i]);
				$li_totfil=0;
				$as_cuenta="";
				for($li=$li_total;$li>1;$li--)
				{
					$li_ant=$ia_niveles_scg[$li-1];
					$li_act=$ia_niveles_scg[$li];
					$li_fila=$li_act-$li_ant;
					$li_len=strlen($ls_sc_cuenta);						
					$li_totfil=$li_totfil+$li_fila;     
					$li_inicio=$li_len-$li_totfil;      
					if($li==$li_total)
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
					}
					else
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
					}
				}
				$li_fila=$ia_niveles_scg[1]+1; 
				$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
				$ls_status=$io_report->dts_reporte->data["status"][$li_i];
				$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$li_i];
				$ld_saldo=$io_report->dts_reporte->data["saldo"][$li_i];
				$ld_total_ingresos+=$ld_saldo;
				$ls_nivel=$io_report->dts_reporte->data["nivel"][$li_i];
				if($ls_nivel>3)
				{
                     $ld_saldo=abs($ld_saldo);
					 $ld_saldomay=number_format($ld_saldo,2,",",".");
					 $ld_saldomen="";  
					 $ld_saldo="";
				}
				if($ls_nivel==3)
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen=number_format($ld_saldo,2,",",".");  
					 $ld_saldo="";
				}
				if(($ls_nivel==1)||($ls_nivel==2))
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen="";  
					 $ld_saldo=number_format($ld_saldo,2,",",".");
				}
					$li_row=$li_row+1;
					$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
					$lo_hoja->write($li_row, 2, $ld_saldomay, $lo_dataright);
					$lo_hoja->write($li_row, 3, $ld_saldomen, $lo_dataright);
					$lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
			}//for
				$ld_total_ingresos=abs($ld_total_ingresos);
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 2, "Total Ingresos ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
				$lo_hoja->write($li_row, 4, $ld_total_ingresos,$lo_dataright);
				$li_row=$li_row+1;
		}//if($lb_valido_ing)
		if($lb_valido_egr)
	    {
	    		$lo_hoja->write($li_row, 0, "EGRESOS",$lo_titulo);
				$li_row++;
				$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
				$lo_hoja->write($li_row, 1, "Denominación",$lo_titulo);
				$lo_hoja->write($li_row, 2, "Saldo",$lo_titulo);
				$lo_hoja->write($li_row, 3, "",$lo_titulo);
				$lo_hoja->write($li_row, 4, "",$lo_titulo);
				$li_row++;
				$li_tot=$io_report->dts_egresos->getRowCount("sc_cuenta");
				$ld_total_egresos=0;
				for($li_i=1;$li_i<=$li_tot;$li_i++)
				{
					//$io_pdf->transaction('start'); // Iniciamos la transacción
					
					$ls_sc_cuenta=trim($io_report->dts_egresos->data["sc_cuenta"][$li_i]);
					
					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);	
						$li_totfil=$li_totfil+$li_fila;     
						$li_inicio=$li_len-$li_totfil;      
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1; 
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ls_status=$io_report->dts_egresos->data["status"][$li_i];
					$ls_denominacion=$io_report->dts_egresos->data["denominacion"][$li_i];
					$ld_saldo=$io_report->dts_egresos->data["saldo"][$li_i];
					$ld_total_egresos+=$ld_saldo;
					$ls_nivel=$io_report->dts_egresos->data["nivel"][$li_i];
					
					
					if($ls_nivel>3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay=number_format($ld_saldo,2,",",".");
						 if ($ld_saldomay < 0)
						 {
						 	$ld_saldomay='('.$ld_saldomay.')';
						    $ld_saldomay=str_replace('-',"",$ld_saldomay);
						 }
						 $ld_saldomen="";  
						 $ld_saldo="";
					}
					if($ls_nivel==3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen=number_format($ld_saldo,2,",",".");  
						 $ld_saldo="";
					}
					if(($ls_nivel==1)||($ls_nivel==2))
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen="";  
						 $ld_saldo=number_format($ld_saldo,2,",",".");
					}
					$li_row=$li_row+1;
					$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
					$lo_hoja->write($li_row, 2, $ld_saldomay, $lo_dataright);
					$lo_hoja->write($li_row, 3, $ld_saldomen, $lo_dataright);
					$lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
				}//for

			$ld_total_egresos=abs($ld_total_egresos);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 2, "Total Egresos ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
			$lo_hoja->write($li_row, 4, $ld_total_egresos,$lo_dataright);
			$li_row=$li_row+1;	
				
			
	    	
			
			if($lb_valido_ing)
			{ 
				//$ld_total_ingresos=str_replace('.','',$ld_total_ingresos);
				//$ld_total_ingresos=str_replace(',','.',$ld_total_ingresos);	
			}
			else
			{
			   $ld_total_ingresos=0;
			}
		    $ld_total=$ld_total_ingresos-$ld_total_egresos;
			    
			if($ld_total<0)
			{
				$ls_cadena="DESAHORRO";
			}
			else
			{
				$ls_cadena="AHORRO";
			}
			
			
			
			
			$lo_hoja->write($li_row, 2, "Total (".$ls_cadena.") ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
			$lo_hoja->write($li_row, 4, $ld_total,$lo_dataright);
		}//if		
	 }//else
	 $li_row++;
	 $contfilas=$li_row;
	 $ld_total_egresos=0;
	 $ld_total_ingresos=0;
 	}
	}
 	else
 	{
 	
		
 		$j=0;
		while(!$rsEst->EOF)
		{ 
			if($rsEst->fields["codestpro1"]!="-------------------------")
			{
				
			$auxEst="0";
			$ls_denestpro = array();	
			$arestructuras[0]=str_pad($rsEst->fields["codestpro1"],25,"0",0);
			$arestructuras[1]=str_pad($rsEst->fields["codestpro2"],25,"0",0);
			$arestructuras[2]=str_pad($rsEst->fields["codestpro3"],25,"0",0);
			$arestructuras[3]=str_pad($auxEst,25,"0",0);
			$arestructuras[4]=str_pad($auxEst,25,"0",0);	
			$arestructuras[5]=$rsEst->fields["estcla"];
			//$ls_programatica=$rsEst->fields["codestpro1"].$rsEst->fields["codestpro2"].$rsEst->fields["codestpro3"];			
			$ls_programatica[0]=str_pad($rsEst->fields["codestpro1"],25,'0',0);
			$ls_programatica[1]=str_pad($rsEst->fields["codestpro2"],25,'0',0);
			$ls_programatica[2]=str_pad($rsEst->fields["codestpro3"],25,'0',0);			
 		
                        $ls_denestpro[0]="";
                        $ls_denestpro[1]="";
                        $ls_denestpro[2]="";
			$ls_denestpro[0]=$oGastos->uf_spg_reporte_select_denestpro1($arestructuras[0],$ls_denestpro[0]);
			$ls_denestpro[1]=$oGastos->uf_spg_reporte_select_denestpro2($arestructuras[0],$arestructuras[1],$ls_denestpro[1]);
			$ls_denestpro[2]=$oGastos->uf_spg_reporte_select_denestpro3($arestructuras[0],$arestructuras[1],$arestructuras[2],$ls_denestpro[2]);

	
		$lb_valido=true;
		if($lb_valido)
		{
			
			$lb_valido_ing=$io_report->uf_scg_reporte_estado_de_resultado_est_ingreso($ldt_fecdes,$ldt_fechas,$li_nivel,$arestructuras);
		
			$lb_valido_egr=$io_report->uf_scg_reporte_estado_de_resultado_est_egreso($ldt_fecdes,$ldt_fechas,$li_nivel,$arestructuras);
					
		}
		
		
		
		
		if((($lb_valido_ing==false)&&($lb_valido_egr==false))||($lb_valido==false)) // Existe algún error ó no hay registros
	    {
	    	$rsEst->MoveNext();
	    	continue;
	    }
		else// Imprimimos el reporte
		{			
			$ls_tit1="ESTRUCTURA PRESUPUESTARIA";
			$ls_tit2a= substr($ls_programatica[0],25-$ls_loncodestpro1,$ls_loncodestpro1);
			$ls_tit2b= $ls_denestpro[0];
			$ls_tit3a= substr($ls_programatica[1],25-$ls_loncodestpro2,$ls_loncodestpro2);
			$ls_tit3b= $ls_denestpro[1];
			$ls_tit4a= substr($ls_programatica[2],25-$ls_loncodestpro3,$ls_loncodestpro3);
			$ls_tit4b= $ls_denestpro[2];
			$li_row++;
			$lo_hoja->write($li_row, 0,$ls_tit1,$lo_titulo);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit2a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit2b,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit3a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit3b,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row, 0," ".$ls_tit4a, $lo_dataleft);
			$lo_hoja->write($li_row, 1, $ls_tit4b,$lo_dataleft);
			$li_row++;
			$lb_valido=true;		
			
		 if($lb_valido_ing)
		 {
		 	$lo_hoja->write($li_row, 0, "INGRESOS",$lo_titulo);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
			$lo_hoja->write($li_row, 1, "Denominación",$lo_titulo);
			$lo_hoja->write($li_row, 2, "Saldo",$lo_titulo);
			$lo_hoja->write($li_row, 3, "",$lo_titulo);
			$lo_hoja->write($li_row, 4, "",$lo_titulo);
			$li_row++;
		 	
			$li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
			$ld_total_ingresos=0;	
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$ls_sc_cuenta=trim($io_report->dts_reporte->data["sc_cuenta"][$li_i]);
				$li_totfil=0;
				$as_cuenta="";
				for($li=$li_total;$li>1;$li--)
				{
					$li_ant=$ia_niveles_scg[$li-1];
					$li_act=$ia_niveles_scg[$li];
					$li_fila=$li_act-$li_ant;
					$li_len=strlen($ls_sc_cuenta);						
					$li_totfil=$li_totfil+$li_fila;     
					$li_inicio=$li_len-$li_totfil;      
					if($li==$li_total)
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
					}
					else
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
					}
				}
				$li_fila=$ia_niveles_scg[1]+1; 
				$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
				$ls_status=$io_report->dts_reporte->data["status"][$li_i];
				$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$li_i];
				$ld_saldo=$io_report->dts_reporte->data["saldo"][$li_i];
				$ld_total_ingresos+=$ld_saldo;
				$ls_nivel=$io_report->dts_reporte->data["nivel"][$li_i];
				if($ls_nivel>3)
				{
                     $ld_saldo=abs($ld_saldo);
					 $ld_saldomay=number_format($ld_saldo,2,",",".");
					 $ld_saldomen="";  
					 $ld_saldo="";
				}
				if($ls_nivel==3)
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen=number_format($ld_saldo,2,",",".");  
					 $ld_saldo="";
				}
				if(($ls_nivel==1)||($ls_nivel==2))
				{
                     $ld_saldo=abs($ld_saldo);					
					 $ld_saldomay="";
					 $ld_saldomen="";  
					 $ld_saldo=number_format($ld_saldo,2,",",".");
				}
					$li_row=$li_row+1;
					$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
					$lo_hoja->write($li_row, 2, $ld_saldomay, $lo_dataright);
					$lo_hoja->write($li_row, 3, $ld_saldomen, $lo_dataright);
					$lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
			}//for
			
		
			
			
			
			
			
				$ld_total_ingresos=abs($ld_total_ingresos);
				$li_row=$li_row+1;
				$lo_hoja->write($li_row, 2, "Total Ingresos ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
				$lo_hoja->write($li_row, 4, $ld_total_ingresos,$lo_dataright);
				$li_row=$li_row+1;
		}//if($lb_valido_ing)
		if($lb_valido_egr)
	    {
	    		$lo_hoja->write($li_row, 0, "EGRESOS",$lo_titulo);
				$li_row++;
				$lo_hoja->write($li_row, 0, "Cuenta",$lo_titulo);
				$lo_hoja->write($li_row, 1, "Denominación",$lo_titulo);
				$lo_hoja->write($li_row, 2, "Saldo",$lo_titulo);
				$lo_hoja->write($li_row, 3, "",$lo_titulo);
				$lo_hoja->write($li_row, 4, "",$lo_titulo);
				$li_row++;
				$li_tot=$io_report->dts_egresos->getRowCount("sc_cuenta");
				$ld_total_egresos = 0;
				for($li_i=1;$li_i<=$li_tot;$li_i++)
				{
					//$io_pdf->transaction('start'); // Iniciamos la transacción
					
					$ls_sc_cuenta=trim($io_report->dts_egresos->data["sc_cuenta"][$li_i]);
					
					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);	
						$li_totfil=$li_totfil+$li_fila;     
						$li_inicio=$li_len-$li_totfil;      
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1; 
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ls_status=$io_report->dts_egresos->data["status"][$li_i];
					$ls_denominacion=$io_report->dts_egresos->data["denominacion"][$li_i];
					$ld_saldo=$io_report->dts_egresos->data["saldo"][$li_i];
					$ld_total_egresos+=$ld_saldo;
					$ls_nivel=$io_report->dts_egresos->data["nivel"][$li_i];
					
					
					if($ls_nivel>3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay=number_format($ld_saldo,2,",",".");
						 if ($ld_saldomay < 0)
						 {
						 	$ld_saldomay='('.$ld_saldomay.')';
						    $ld_saldomay=str_replace('-',"",$ld_saldomay);
						 }
						 $ld_saldomen="";  
						 $ld_saldo="";
					}
					if($ls_nivel==3)
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen=number_format($ld_saldo,2,",",".");  
						 $ld_saldo="";
					}
					if(($ls_nivel==1)||($ls_nivel==2))
					{
						 //$ld_saldo=abs($ld_saldo);
						 $ld_saldo=$ld_saldo*(-1);
						 $ld_saldomay="";
						 $ld_saldomen="";  
						 $ld_saldo=number_format($ld_saldo,2,",",".");
					}
					$li_row=$li_row+1;
					$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
					$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
					$lo_hoja->write($li_row, 2, $ld_saldomay, $lo_dataright);
					$lo_hoja->write($li_row, 3, $ld_saldomen, $lo_dataright);
					$lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
				}//for

			$ld_total_egresos=abs($ld_total_egresos);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 2, "Total Egresos ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
			$lo_hoja->write($li_row, 4, $ld_total_egresos,$lo_dataright);
			$li_row=$li_row+1;	
				
			
	    	
			
			if($lb_valido_ing)
			{ 
				//$ld_total_ingresos=str_replace('.','',$ld_total_ingresos);
				//$ld_total_ingresos=str_replace(',','.',$ld_total_ingresos);	
			}
			else
			{
			   $ld_total_ingresos=0;
			}
		    $ld_total=$ld_total_ingresos-$ld_total_egresos;
			    
			if($ld_total<0)
			{
				$ls_cadena="DESAHORRO";
			}
			else
			{
				$ls_cadena="AHORRO";
			}
			
			
			
			
			$lo_hoja->write($li_row, 2, "Total (".$ls_cadena.") ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
			$lo_hoja->write($li_row, 4, $ld_total,$lo_dataright);
		}//if		
	 }//else
	 $li_row++;
	 $contfilas=$li_row;
	 $ld_total_egresos=0;
	 $ld_total_ingresos=0;
 	} 		

 		$rsEst->MoveNext();
 	}
 	 	
}
 	
 	
 	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"estado_resultado_estructura_presupuestaria.xls\"");
	header("Content-Disposition: inline; filename=\"estado_resultado_estructura_presupuestaria.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
    unset($io_report);
	unset($io_funciones);			
?> 