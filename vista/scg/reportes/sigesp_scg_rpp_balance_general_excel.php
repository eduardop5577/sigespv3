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
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balance_general.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m?todo realiza una consulta a los formatos de las cuentas
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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "balance_general.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../../shared/class_folder/class_sigesp_int.php");
		require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
		$ls_tiporeporte="0";
		$ls_bolivares="";
		require_once("sigesp_scg_class_bal_general.php");
		$io_report  = new sigesp_scg_class_bal_general();
		$ls_bolivares ="Bs.";
			 
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha=new class_fecha();
		require_once("class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ia_niveles_scg[0]="";			
		uf_init_niveles();
		$li_total=count((array)$ia_niveles_scg)-1;
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros para Filtar el Reporte
	   $ls_cmbmes=$_GET["cmbmes"];
	   $ls_cmbagno=$_GET["cmbagno"];
	   $ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	   $ls_fecdesde = $_GET["fecdesde"];
	   $ls_fechasta = $_GET["fechasta"];
	   $fechas=$ls_last_day;
	   $ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day);
	   $ldt_fecdesde=$io_funciones->uf_convertirdatetobd($ls_fecdesde);
	   $ldt_fechasta=$io_funciones->uf_convertirdatetobd($ls_fechasta);
	   $li_nivel=$_GET["cmbnivel"];
	   $ls_rango = $_GET["rango"];
		$ls_codmon=$_GET["codmon"];
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros del encabezado
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$li_ano=substr($ldt_periodo,0,4);
        
        $ls_pasivo=$_SESSION["la_empresa"]["pasivo"];
        $ls_resultado=$_SESSION["la_empresa"]["resultado"];
        $ls_capital=$_SESSION["la_empresa"]["capital"];
        
		$arrResultado=$io_report->uf_buscar_tasacambio($ls_codmon);
		$ls_tascam1=$arrResultado["tascam1"];
		$ls_denmon=$arrResultado["denmon"];
		$ls_abrmon=$arrResultado["abrmon"];

		$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
		$ls_titulo="BALANCE GENERAL";
		$ls_titulo1=" ".$ls_nombre." "; 
		if ($ls_rango == '1') {
			$ls_titulo2=" al ".$ld_fechas."";
		}
		else {
			$ls_titulo2=" del ".$ls_fecdesde." al ".$ls_fechasta." ";
		} 
		$ls_titulo3="(Expresado en ".$ls_denmon.")";  
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Balance General en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balance_general($ldt_fechas,$li_nivel,false, $ls_rango,$ldt_fecdesde,$ldt_fechasta); 
	}
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresi?n de la informaci?n encontrada en caso de que exista
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

		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,50);
		$lo_hoja->set_column(2,5,20);
		
		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_titulo1,$lo_encabezado);
		$lo_hoja->write(2, 2, $ls_titulo2,$lo_encabezado);
		$lo_hoja->write(3, 2, $ls_titulo3,$lo_encabezado);
		$lo_hoja->write(4, 0, "Cuenta",$lo_titulo);
		$lo_hoja->write(4, 1, "Denominaci?n",$lo_titulo);
		//$lo_hoja->write(4, 2, "Debe",$lo_titulo);
		//$lo_hoja->write(4, 3, "Haber",$lo_titulo);
		$lo_hoja->write(4, 4, "Saldo",$lo_titulo);

		$li_tot=$io_report->ds_reporte->getRowCount("sc_cuenta");
		$ld_saldo4="";
		$ld_saldo3="";  
		$ld_saldo2="";	
		$li_row=4;	
		for($li_i=1;$li_i<=$li_tot;$li_i++)
		{
			$ls_orden=$io_report->ds_reporte->data["orden"][$li_i];
			$li_nro_reg=$io_report->ds_reporte->data["num_reg"][$li_i];
			$ls_sc_cuenta=trim($io_report->ds_reporte->data["sc_cuenta"][$li_i]);
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
			$ls_denominacion=$io_report->ds_reporte->data["denominacion"][$li_i];
			$ls_nivel=$io_report->ds_reporte->data["nivel"][$li_i];
			$ls_nivel=abs($ls_nivel);
			$ld_saldo=$io_report->ds_reporte->data["saldo"][$li_i];

				$ld_saldo=($ld_saldo/$ls_tascam1);

			$ls_rnivel=$io_report->ds_reporte->data["rnivel"][$li_i];
			$ls_estatus=$io_report->ds_reporte->data["estatus"][$li_i];
                if($ls_pasivo."000"==substr($ls_sc_cuenta,0,4))
                {
                    $ld_total=$ld_total+$ld_saldo;
                }
                if($ls_capital."000"==substr($ls_sc_cuenta,0,4))
                {
                    $ld_total=$ld_total+$ld_saldo;
                }            
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
			if($ls_estatus == 'C')
			{
			 $lo_hoja->write($li_row, 3, $ld_saldo, $lo_dataright);
			}
			else
			{
			 $lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
			}
		}
		$li_row=$li_row+1;
		$lo_hoja->write($li_row, 2, "Total Pasivo + Capital  ".$ls_abrmon,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 4, $ld_total, $lo_dataright);
		$lb_ctas_acreedoras = $io_report->uf_obtener_cuentas_acreedoras($ldt_fechas,$li_nivel);
		$la_data_acreedoras = NULL; 
		if($lb_ctas_acreedoras)
		{
			$li_tot_acree=$io_report->ds_cuentas_acreedoras->getRowCount("sc_cuenta");
			$pos=0;	
			$ld_total=0;
			for($li_i=1;$li_i<=$li_tot_acree;$li_i++)
			{
			 $ls_sc_cuenta=trim($io_report->ds_cuentas_acreedoras->data["sc_cuenta"][$li_i]);
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
			 $ls_denominacion=$io_report->ds_cuentas_acreedoras->data["denominacion"][$li_i];
			 $ls_nivel=$io_report->ds_cuentas_acreedoras->data["nivel"][$li_i];
			 $ls_nivel=abs($ls_nivel);
			 $ld_saldo=$io_report->ds_cuentas_acreedoras->data["saldo"][$li_i];
				
				$ld_saldo=($ld_saldo/$ls_tascam1);

			 $ls_rnivel=$io_report->ds_cuentas_acreedoras->data["rnivel"][$li_i];
			 $ls_estatus=$io_report->ds_cuentas_acreedoras->data["estatus"][$li_i];
			 $li_row=$li_row+1;
			 $lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
			 $lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
			 if($ls_estatus == 'C')
			 {
				 $lo_hoja->write($li_row, 3, $ld_saldo, $lo_dataright);
			 }
			 else
			 {
				 $lo_hoja->write($li_row, 4, $ld_saldo, $lo_dataright);
			 }
			}
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"balance_general.xls\"");
		header("Content-Disposition: inline; filename=\"balance_general.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 