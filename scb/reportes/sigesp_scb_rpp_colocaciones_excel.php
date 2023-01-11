<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "Colocaciones.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
		require_once("sigesp_scb_report.php");
		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
				
		$io_conect       = new sigesp_include();
		$con             = $io_conect->uf_conectar();
		$io_report       = new sigesp_scb_report($con);
		$io_funciones    = new class_funciones();
		$io_sql          = new class_sql($con);
		$io_fecha        = new class_fecha();
						

	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_titemp=$_SESSION["la_empresa"]["titulo"];
		$ls_codbandes=$_GET["codbandes"];
		$ls_codbanhas=$_GET["codbanhas"];
		$ls_ctabandes=$_GET["ctabandes"];
		$ls_ctabanhas=$_GET["ctabanhas"];
		$ld_fecdesde=$_GET["fecdes"];		
		$ld_fechasta=$_GET["fechas"];
		$ls_orden=$_GET["orden"];	
		$ls_tipbol      = 'Bsf.';
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
		$ldt_fecha="Desde  ".$ld_fecdesde."  al ".$ld_fechasta."";
		$ls_titulo="INVENTARIOS DE COLOCACIONES BANCARIAS ";       
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido            = true;
	$rsdata=$io_report->uf_generar_estado_cuenta_colocacion($ls_codemp,$ls_codbandes,$ls_ctabandes,$ls_codbanhas,$ls_ctabanhas,$ld_fecdesde,$ld_fechasta,$ls_orden);
	//---------------------------------------------------------------------------------------------------------------------------
	function restaFechas($dFecIni, $dFecFin)
	{
		$dFecIni = str_replace("-","",$dFecIni);
		$dFecIni = str_replace("/","",$dFecIni);
		$dFecFin = str_replace("-","",$dFecFin);
		$dFecFin = str_replace("/","",$dFecFin);
		preg_match( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecIni, $aFecIni);
		preg_match( "([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $dFecFin, $aFecFin);
		$date1 = mktime(0,0,0,$aFecIni[2], $aFecIni[1], $aFecIni[3]);
		$date2 = mktime(0,0,0,$aFecFin[2], $aFecFin[1], $aFecFin[3]);
		return round(($date2 - $date1) / (60 * 60 * 24));
	}
	//---------------------------------------------------------------------------------------------------------------------------
  	// Impresión de la información encontrada en caso de que exista
	if(!$rsdata) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar !!!');"); 
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
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('9');
		$lo_datanumcen= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_datanumcen->set_font("Verdana");
		$lo_datanumcen->set_align('center');
		$lo_datanumcen->set_size('9');
		$lo_total= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_total->set_bold();
		$lo_total->set_font("Verdana");
		$lo_total->set_align('right');
		$lo_total->set_size('9');
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,5,30);
		$lo_hoja->set_column(6,6,40);
		$lo_hoja->set_column(7,9,30);
		
		$lo_hoja->set_column(9,9,40);
		$lo_hoja->set_column(10,10,40);
		$lo_hoja->set_column(11,11,50);
		$lo_hoja->set_column(12,12,20);
		$lo_hoja->set_column(7,7,20);
		$lo_hoja->set_column(5,5,30);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(8,8,20);
		$lo_hoja->set_column(13,13,20);
		
		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 3, $ldt_fecha,$lo_encabezado);
		
		$li_row = 5;
		$lo_hoja->write(5, 0, "Proyecto",$lo_titulo);
		$lo_hoja->write(5, 1, "Capital",$lo_titulo);
		$lo_hoja->write(5, 2, "Fecha Emisión",$lo_titulo);
		$lo_hoja->write(5, 3, "Fecha Vencimiento",$lo_titulo);
		$lo_hoja->write(5, 4, "Días",$lo_titulo);
		$lo_hoja->write(5, 5, "Tasa",$lo_titulo);
		$lo_hoja->write(5, 6, "Rendimiento",$lo_titulo);
		$lo_hoja->write(5, 7, "Conjunto",$lo_titulo);
		$lo_hoja->write(5, 8, "N° Expediente",$lo_titulo);
		$lo_hoja->write(5, 9, "Cuenta Cedente",$lo_titulo);
		$lo_hoja->write(5, 10, "Concepto",$lo_titulo);
		$lo_hoja->write(5, 11, "Entidad",$lo_titulo);
		
		$li_totcap=0;
		$li_totren=0;
		$li_totcon=0;
		
        while(!$rsdata->EOF){
			$ls_banco		= $rsdata->fields['nomban'];
			$ls_colocacion	= $rsdata->fields['numcol'];
			$ls_fecinicol   = $rsdata->fields['feccol'];
		    $ls_fecinicol   = $io_funciones->uf_convertirfecmostrar($ls_fecinicol);
		    $ls_fecfincol   = $rsdata->fields['fecvencol'];
		    $ls_fecfincol   = $io_funciones->uf_convertirfecmostrar($ls_fecfincol);
			$ls_diacol      = $rsdata->fields['diacol'];
			$ls_tascol   	= $rsdata->fields['tascol'];
			$ls_rendimiento = $rsdata->fields['monint'];
			$ls_monto       = $rsdata->fields['monto'];
			$ls_conjunto    = $ls_rendimiento+$ls_monto;
			$ls_ctabancedente = $rsdata->fields['ctaban'];
			$ls_concepto	= $rsdata->fields['dencta'];
			$ls_tipocol  	= $rsdata->fields['denominacion'];
			$li_row=$li_row+1;
			
			//acumuladores
			$li_totcap = $li_totcap + $ls_monto;
			$li_totren = $li_totren + $ls_rendimiento;
			$li_totcon = $li_totcon + $ls_conjunto;
			
			
			$lo_hoja->write($li_row, 0, $ls_banco, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_monto, $lo_dataright);
			$lo_hoja->write($li_row, 2, $ls_fecinicol, $lo_datadate);
			$lo_hoja->write($li_row, 3, $ls_fecfincol, $lo_datadate);
			$lo_hoja->write($li_row, 4, $ls_diacol, $lo_datacenter);
			$lo_hoja->write($li_row, 5, $ls_tascol, $lo_datanumcen);
			$lo_hoja->write($li_row, 6, $ls_rendimiento, $lo_dataright);
			$lo_hoja->write($li_row, 7, $ls_conjunto, $lo_dataright);
			$lo_hoja->write($li_row, 8, $ls_colocacion, $lo_datacenter);
			$lo_hoja->write($li_row, 9, " ".$ls_ctabancedente, $lo_datacenter);
			$lo_hoja->write($li_row, 10, $ls_concepto,$lo_datacenter);
			$lo_hoja->write($li_row, 11, $ls_tipocol,$lo_datacenter);
			
			$rsdata->MoveNext();
		}
		$li_row++;
		$lo_hoja->write($li_row, 0, "TOTALES", $lo_titulo);
		$lo_hoja->write($li_row, 1, $li_totcap, $lo_total);
		$lo_hoja->write($li_row, 2, '---', $lo_titulo);
		$lo_hoja->write($li_row, 3, '---', $lo_titulo);
		$lo_hoja->write($li_row, 4, '---', $lo_titulo);
		$lo_hoja->write($li_row, 5, '---', $lo_titulo);
		$lo_hoja->write($li_row, 6, $li_totren, $lo_total);
		$lo_hoja->write($li_row, 7, $li_totcon, $lo_total);
		$lo_hoja->write($li_row, 8, '---', $lo_titulo);
		$lo_hoja->write($li_row, 9, '---', $lo_titulo);
		$lo_hoja->write($li_row, 10, '---',$lo_titulo);
		$lo_hoja->write($li_row, 11, '---',$lo_titulo);
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Colocaciones.xls\"");
		header("Content-Disposition: inline; filename=\"Colocaciones.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		unset($io_funciones);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
    }
?> 