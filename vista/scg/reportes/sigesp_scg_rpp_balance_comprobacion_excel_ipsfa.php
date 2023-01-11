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
	ini_set('max_execution_time','0');

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
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balance_comprobacion.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "balance_comprobacion.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("sigesp_scg_reporte.php");
		$io_report = new sigesp_scg_reporte();
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("class_funciones_scg.php");
		$io_fun_scg=new class_funciones_scg();
		$ls_tiporeporte="0";
		$ls_bolivares="";
		require_once("sigesp_scg_reporte.php");
		$io_report  = new sigesp_scg_reporte();
		$ls_bolivares ="Bs.";
		$ia_niveles_scg[0]="";
		uf_init_niveles();
		$li_total=count((array)$ia_niveles_scg)-1;
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
		$li_saldocero=$_GET["saldocero"];
		$li_saldomes=$_GET["saldomes"];
		$ld_fecdesde=$_GET["fecdes"];
		$ld_fechasta=$_GET["fechas"];
		$ls_costodesde=$_GET["costodesde"];
		$ls_costohasta=$_GET["costohasta"];
		$ls_cuentadesde=$_GET["cuentadesde"];
		$ls_cuentahasta=$_GET["cuentahasta"];
		$ls_codmon=$_GET["codmon"];
		if(($ls_cuentadesde=="")&&($ls_cuentahasta==""))
		{
			$arrResultado = $io_report->uf_spg_reporte_select_cuenta_min_max($ls_cuentadesde,$ls_cuentahasta);
			$ls_cuentadesde = $arrResultado['as_sc_cuenta_min'];
			$ls_cuentahasta = $arrResultado['as_sc_cuenta_max'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido)
			{
				//$ls_cuentadesde=$ls_cuentadesde_min;
				//$ls_cuentahasta=$ls_cuentahasta_max;
			}
		}
		$li_nivel=$_GET["nivel"];
		if ($li_saldomes==0)
		{
			$ncol=0;
		}
		else
		{
			$ncol=1;
		}
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdesde."  al ".$ld_fechasta."";
		$ls_titulo="BALANCE DE COMPROBACIÓN";
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data
	$lb_valido=uf_insert_seguridad("<b>Balance de Comprobación en Excel</b>"); // Seguridad de Reporte
	$arrResultado=$io_report->uf_buscar_tasacambio($ls_codmon);
	$ls_tascam1=$arrResultado["tascam1"];
	$ls_denmon=$arrResultado["denmon"];
	$ls_abrmon=$arrResultado["abrmon"];
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_scg_reporte_balance_comprobante($ls_cuentadesde,$ls_cuentahasta,$ld_fecdesde,$ld_fechasta,$li_nivel,$li_saldocero,$ls_costodesde,$ls_costohasta);
	}
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresión de la información encontrada en caso de que exista
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
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,50);
		$lo_hoja->set_column(2,6,30);

		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, "Expresado en ".$ls_denmon,$lo_encabezado);
		$lo_hoja->write(3, 2, $ldt_fecha,$lo_encabezado);

		$lo_hoja->write(5, 0, "Cuenta",$lo_titulo);
		$lo_hoja->write(5, 1, "Denominación",$lo_titulo);
		$lo_hoja->write(5, 2, "Saldo Anterior",$lo_titulo);
		$lo_hoja->write(5, 3, "Debe",$lo_titulo);
		$lo_hoja->write(5, 4, "Haber",$lo_titulo);
		if ($li_saldomes==1)
		{
			$lo_hoja->write(5, 5, "Saldo del Mes",$lo_titulo);
		}
		$lo_hoja->write(5, 5+$ncol, "Saldo Actual",$lo_titulo);
		$li_tot=$io_report->dts_reporte->getRowCount("sc_cuenta");
		$ldec_totaldebe=0;
		$ldec_totalhaber=0;
		$ldec_total_saldo=0;
        $ld_saldo=0;
		$ldec_mondeb=0;
        $ldec_monhab=0;
		$li_row=5;
		for($i=1;$i<=$li_tot;$i++)
		{
			$ls_cuenta=rtrim($io_report->dts_reporte->getValue("sc_cuenta",$i));
			$li_totfil=0;
			$as_cuenta="";
			for($li=$li_total;$li>1;$li--)
			{
				$li_ant=$ia_niveles_scg[$li-1];
				$li_act=$ia_niveles_scg[$li];
				$li_fila=$li_act-$li_ant;
				$li_len=strlen($ls_cuenta);
				$li_totfil=$li_totfil+$li_fila;
				$li_inicio=$li_len-$li_totfil;
				if($li==$li_total)
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila);
				}
				else
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
				}
			}
			$li_fila=$ia_niveles_scg[1]+1;
			$as_cuenta=substr($ls_cuenta,0,$li_fila)."-".$as_cuenta;
			$ls_denominacion=rtrim($io_report->dts_reporte->getValue("denominacion",$i));
			$ldec_debe=number_format($io_report->dts_reporte->getValue("debe_mes",$i),2,".","");
			$ldec_haber=number_format($io_report->dts_reporte->getValue("haber_mes",$i),2,".","");
			$ldec_saldo_ant=number_format($io_report->dts_reporte->getValue("anterior",$i)+$io_report->dts_reporte->getValue("debe_mes_ant",$i)-$io_report->dts_reporte->getValue("haber_mes_ant",$i),2,".","");
			$ldec_saldo_act=number_format($ldec_saldo_ant+$ldec_debe-$ldec_haber,2,".","");
			$ldec_BalDebe=number_format($io_report->dts_reporte->getValue("total_debe",$i),2,".","");
			$ldec_BalHABER=number_format($io_report->dts_reporte->getValue("total_haber",$i),2,".","");
			$ldec_saldomes=number_format($ldec_debe-$ldec_haber,2,".","");
			
			$ldec_totaldebe=number_format($ldec_totaldebe + $ldec_BalDebe*100,2,'.','');
			$ldec_totalhaber=number_format($ldec_totalhaber + $ldec_BalHABER*100,2,'.','');
			
			$ldec_debe=($ldec_debe/$ls_tascam1);
			$ldec_haber=($ldec_haber/$ls_tascam1);
			$ldec_saldo_ant=($ldec_saldo_ant/$ls_tascam1);
			$ldec_saldo_act=($ldec_saldo_act/$ls_tascam1);
			$ldec_BalDebe=($ldec_BalDebe/$ls_tascam1);
			$ldec_BalHABER=($ldec_BalHABER/$ls_tascam1);
			$ldec_saldomes=($ldec_saldomes/$ls_tascam1);

			$ldec_saldo=$ldec_saldo_act;
			$li_row=$li_row+1;
			
			$ldec_saldo=$ldec_saldo_act;
			/*if($ldec_debe<0)
			{
				$ldec_debe_aux=abs($ldec_debe);
				$ldec_debe_aux=number_format($ldec_debe_aux,2,",",".");
				$ldec_debe="(".$ldec_debe_aux.")";
			}
			else
			{
			   $ldec_debe=number_format($ldec_debe,2,",",".");
			}
			
			if($ldec_haber<0)
			{
				$ldec_haber_aux=abs($ldec_haber);
				$ldec_haber_aux=number_format($ldec_haber_aux,2,",",".");
				$ldec_haber="(".$ldec_haber_aux.")";
			}
			else
			{
				$ldec_haber=number_format($ldec_haber,2,",",".");
			}
			
			if($ldec_saldo<0)
			{
				$ldec_saldo_aux=abs($ldec_saldo);
				$ldec_saldo_aux=number_format($ldec_saldo_aux,2,",",".");
				$ldec_saldo="(".$ldec_saldo_aux.")";
			}
			else
			{
				$ldec_saldo=number_format($ldec_saldo,2,",",".");
			}
			
			if($ldec_saldo_ant<0)
			{
				$ldec_saldo_ant_aux=abs($ldec_saldo_ant);
				$ldec_saldo_ant_aux=number_format($ldec_saldo_ant_aux,2,",",".");
				$ldec_saldo_ant="(".$ldec_saldo_ant_aux.")";
			}
			else
			{
				$ldec_saldo_ant=number_format($ldec_saldo_ant,2,",",".");
			}

			$ldec_saldomes=number_format($ldec_saldomes,2,",",".");*/
			
			$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
			$lo_hoja->write($li_row, 2, $ldec_saldo_ant, $lo_dataright);
			$lo_hoja->write($li_row, 3, $ldec_debe, $lo_dataright);
			$lo_hoja->write($li_row, 4, $ldec_haber, $lo_dataright);
			if ($li_saldomes==1)
			{
				$lo_hoja->write($li_row, 5, $ldec_saldomes,$lo_dataright);
			}
			$lo_hoja->write($li_row, 5+$ncol, $ldec_saldo, $lo_dataright);
		}//for
		
		$li_row=$li_row+1;		
		$ldec_totaldebe=number_format($ldec_totaldebe/100,2,'.','');
		$ldec_totalhaber=number_format($ldec_totalhaber/100,2,'.','');
		
		//$ldec_total_saldo=number_format($ldec_totaldebe-$ldec_totalhaber,2,",","");
		$ldec_total_saldomes=$ldec_total_saldo;
		/*if($ldec_totaldebe<0)
		{
			$ldec_totaldebe_aux=abs($ldec_totaldebe);
			$ldec_totaldebe_aux=number_format($ldec_totaldebe_aux,2,",",".");
			$ldec_totaldebe="(".$ldec_totaldebe_aux.")";
		}
		else
		{
		    $ldec_totaldebe=number_format($ldec_totaldebe,2,",",".");
		}
		if($ldec_totalhaber<0)
		{
			$ldec_totalhaber_aux=abs($ldec_totalhaber);
			$ldec_totalhaber_aux=number_format($ldec_totalhaber_aux,2,",",".");
			$ldec_totalhaber="(".$ldec_totalhaber_aux.")";
		}
		else
		{
		   $ldec_totalhaber=number_format($ldec_totalhaber,2,",",".");
		}
		if($ldec_total_saldo<0)
		{
			$ldec_total_saldo_aux=abs($ldec_total_saldo);
			$ldec_total_saldo_aux=number_format($ldec_total_saldo_aux,2,",",".");
			$ldec_total_saldo="(".$ldec_total_saldo_aux.")";
		}*/
		
		$lo_hoja->write($li_row, 2, "Total ".$ls_abrmon,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 3, $ldec_totaldebe, $lo_dataright);
		$lo_hoja->write($li_row, 4, $ldec_totalhaber, $lo_dataright);
		if ($li_saldomes==1)
		{
			$lo_hoja->write($li_row, 5, $ldec_total_saldomes,$lo_dataright);
		}
		$lo_hoja->write($li_row, 5+$ncol, $ldec_total_saldo, $lo_dataright);

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"balance_comprobacion.xls\"");
		header("Content-Disposition: inline; filename=\"balance_comprobacion.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?>