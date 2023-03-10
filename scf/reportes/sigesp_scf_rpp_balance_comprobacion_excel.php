<?php
/***********************************************************************************
* @fecha de modificacion: 09/08/2022, para la version de php 8.1 
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
		global $io_fun_scf;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scf->uf_load_seguridad_reporte("SCF","sigesp_scf_r_balance_comprobacion.php",$ls_descripcion);
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
		global $io_funciones,$ia_niveles_scf;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scf[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "balance_comprobacion.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../class_folder/class_funciones_scf.php");
		$io_fun_scf=new class_funciones_scf("../../");
		$ls_tiporeporte="0";
		$ls_bolivares="";
		if (array_key_exists("tiporeporte",$_GET))
		{
			$ls_tiporeporte=$_GET["tiporeporte"];
		}
		switch($ls_tiporeporte)
		{
			case "0":
				require_once("sigesp_scf_class_report.php");
				$io_report  = new sigesp_scf_class_report();
				$ls_bolivares ="Bs.";
				break;
	
			case "1":
				require_once("sigesp_scf_class_reportbsf.php");
				$io_report  = new sigesp_scf_class_reportbsf();
				$ls_bolivares ="Bs.F.";
				break;
		}
		$ia_niveles_scf[0]="";			
		uf_init_niveles();
		$li_total=count((array)$ia_niveles_scf)-1;
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros para Filtar el Reporte
		$ld_fecdes=$_GET["fecdes"];
		$ld_fechas=$_GET["fechas"];
		$ls_cuentadesde=$_GET["cuentadesde"];
		$ls_cuentahasta=$_GET["cuentahasta"];
		$li_nivel=$_GET["nivel"];
	//---------------------------------------------------------------------------------------------------------------------------
	//Par?metros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdes."  al ".$ld_fechas."";
		$ls_titulo="BALANCE DE COMPROBACI?N";       
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Balance de Comprobaci?n en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balancecomprobacion($ld_fecdes,$ld_fechas,$ls_cuentadesde,$ls_cuentahasta,$li_nivel);
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
		$lo_hoja->write(1, 2, $ldt_fecha,$lo_encabezado);
		
		$lo_hoja->write(3, 0, "Cuenta",$lo_titulo);
		$lo_hoja->write(3, 1, "Denominaci?n",$lo_titulo);
		$lo_hoja->write(3, 2, "Saldo Anterior",$lo_titulo);
		$lo_hoja->write(3, 3, "Debe",$lo_titulo);
		$lo_hoja->write(3, 4, "Haber",$lo_titulo);
		$lo_hoja->write(3, 5, "Saldo Actual",$lo_titulo);
		$li_tot=$io_report->DS->getRowCount("sc_cuenta");
		$ldec_totaldebe=0;
		$ldec_totalhaber=0;
		$ldec_total_saldo=0;
        $ld_saldo=0;
		$ldec_mondeb=0;
        $ldec_monhab=0;
		$li_row=3;
		for($i=1;$i<=$li_tot;$i++)
		{
			$ls_cuenta=rtrim($io_report->DS->getValue("sc_cuenta",$i));
			$li_totfil=0;
			$as_cuenta="";
			for($li=$li_total;$li>1;$li--)
			{
				$li_ant=$ia_niveles_scf[$li-1];
				$li_act=$ia_niveles_scf[$li];
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
			$li_fila=$ia_niveles_scf[1]+1;
			$as_cuenta=substr($ls_cuenta,0,$li_fila)."-".$as_cuenta;
			$ls_denominacion=rtrim($io_report->DS->getValue("denominacion",$i));
			$ldec_debe=$io_report->DS->getValue("debe_mes",$i);
			$ldec_haber=$io_report->DS->getValue("haber_mes",$i);
			$ldec_saldo_ant=($io_report->DS->getValue("debe_mes_ant",$i)-$io_report->DS->getValue("haber_mes_ant",$i));
			$ldec_saldo_act=$ldec_saldo_ant+$ldec_debe-$ldec_haber;
			$ldec_BalDebe=$io_report->DS->getValue("total_debe",$i);
			$ldec_BalHABER=$io_report->DS->getValue("total_haber",$i);
			$ldec_totaldebe=$ldec_totaldebe+$ldec_BalDebe;
			$ldec_totalhaber=$ldec_totalhaber+$ldec_BalHABER;
			$ldec_saldo=$ldec_saldo_act;
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, $as_cuenta, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_denominacion, $lo_dataleft);
			$lo_hoja->write($li_row, 2, $ldec_saldo_ant, $lo_dataright);
			$lo_hoja->write($li_row, 3, $ldec_debe, $lo_dataright);
			$lo_hoja->write($li_row, 4, $ldec_haber, $lo_dataright);
			$lo_hoja->write($li_row, 5, $ldec_saldo, $lo_dataright);
		}//for
		$li_row=$li_row+1;
		$ldec_total_saldo=round($ldec_totaldebe-$ldec_totalhaber,2);
		$lo_hoja->write($li_row, 2, "Total ".$ls_bolivares,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'10')));
		$lo_hoja->write($li_row, 3, $ldec_totaldebe, $lo_dataright);
		$lo_hoja->write($li_row, 4, $ldec_totalhaber, $lo_dataright);
		$lo_hoja->write($li_row, 5, $ldec_total_saldo, $lo_dataright);

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