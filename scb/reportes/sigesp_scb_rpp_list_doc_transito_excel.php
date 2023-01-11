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
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time ','0');	
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ls_periodo,$ls_denban,$ls_ctaban,$ls_dencta,$lo_encabezado,$lo_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_hoja;
		
		$lo_hoja->write(1, 2, $as_titulo,$lo_encabezado);
		$lo_encabezado->set_align('left');
		
		$lo_hoja->write(4, 0, 'Periodo: ',$lo_encabezado);
		$lo_hoja->write(5, 0, 'Banco: ',$lo_encabezado);
		$lo_hoja->write(6, 0, 'Cuenta: ',$lo_encabezado);
		$lo_hoja->write(4, 1, $ls_periodo,$lo_encabezado);
		$lo_hoja->write(5, 1, $ls_denban,$lo_encabezado);
		$lo_hoja->write(6, 1, $ls_ctaban."   ".$ls_dencta,$lo_encabezado);
		
		$lo_hoja->write(9, 0, 'Fecha',$lo_titulo);
		$lo_hoja->write(9, 1, 'Documento',$lo_titulo);
		$lo_hoja->write(9, 2, 'Operacion',$lo_titulo);
		$lo_hoja->write(9, 3, 'Proveedor',$lo_titulo);
		$lo_hoja->write(9, 4, 'Monto',$lo_titulo);
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ad_debitos,$ad_creditos,$ad_total,$li_fila,$lo_titulo,$lo_dataright)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: as_numdoc // Número del documento
		//	    		   as_conmov // concepto del documento
		//	    		   as_nomproben // nombre del proveedor beneficiario
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $lo_hoja;

		$li_fila++;
		$lo_hoja->write($li_fila, 3, 'Total Créditos:',$lo_titulo);
		$lo_hoja->write($li_fila, 4, $ad_debitos,$lo_dataright);
		$li_fila++;
		$lo_hoja->write($li_fila, 3, 'Total Débitos:',$lo_titulo);
		$lo_hoja->write($li_fila, 4, $ad_creditos,$lo_dataright);
		$li_fila++;
		$lo_hoja->write($li_fila, 3, 'Total Saldo:',$lo_titulo);
		$lo_hoja->write($li_fila, 4, $ad_total,$lo_dataright);
		$li_fila++;
		
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_scb_class_report.php");
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	$sig_inc   = new sigesp_include();
	$con       = $sig_inc->uf_conectar();
	$io_report = new sigesp_scb_class_report($con);
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
		require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
		$lo_archivo = tempnam("/tmp", "documentos_en_transito.xls");
		$lo_libro = new writeexcel_workbookbig($lo_archivo);
		$lo_hoja = &$lo_libro->addworksheet();
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_periodo     = $_GET["periodo"];
	$ls_codban      = $_GET["codban"];
	$ls_ctaban      = $_GET["ctaban"];
	$ls_denban      = $_GET["denban"];
	$ls_dencta      = $_GET["dencta"];
	$ls_orden       = $_GET["orden"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_titulo="Listado de Documentos en Transito $ls_tipbol";
	$io_report->uf_cargar_documentos_transito($ls_periodo,$ls_codban,$ls_ctaban,$ls_orden);
	$ldec_totaldebitos=0;
	$ldec_totalcreditos=0;
	$ldec_saldo=0;
	$lb_valido=true;
	$li_total=$io_report->ds_documentos->getRowCount("codban");
	if($li_total>0)
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
		$lo_tituloright= &$lo_libro->addformat();
		$lo_tituloright->set_bold();
		$lo_tituloright->set_font("Verdana");
		$lo_tituloright->set_align('right');
		$lo_tituloright->set_size('9');		
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
		$lo_hoja->set_column(0,4,20);
		$lo_hoja->set_column(3,3,50);
		$lo_hoja->set_column(4,4,20);
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$ls_denban,$ls_ctaban,$ls_dencta,$lo_encabezado,$lo_titulo); // Imprimimos el encabezado de la página
		$li_fila=10;
		for($i=1;$i<=$li_total;$i++)
		{
			$ls_numdoc	  = $io_report->ds_documentos->getValue("numdoc",$i);
			$ls_ctaban	  = $io_report->ds_documentos->getValue("ctaban",$i);
			$ldec_monto	  = $io_report->ds_documentos->getValue("monto",$i);
			$ld_fecmov	  = $io_report->ds_documentos->getValue("fecmov",$i);
			$ld_fecmov	  =	$io_report->fun->uf_convertirfecmostrar($ld_fecmov);
			$ls_nomproben = $io_report->ds_documentos->getValue("nomproben",$i);
			$ls_codope	  = $io_report->ds_documentos->getValue("codope",$i);
			$ls_conmov	  = $io_report->ds_documentos->getValue("conmov",$i);
			$ls_estmov	  = $io_report->ds_documentos->getValue("estmov",$i);
			if ($ls_estmov=='O')
			   {
			     $ls_estatus = 'ORIGINAL';
			   }
			elseif($ls_estmov=='A')
			   {
			     $ls_estatus = 'ANULADO';
			   }
			elseif($ls_estmov=='C')
			   {
			     $ls_estatus = 'CONTABILIZADO';
			   }
			elseif($ls_estmov=='L')
			   {
			     $ls_estatus = 'NO CONTABILIZABLE';
			   }
			elseif($ls_estmov=='E')
			   {
			     $ls_estatus = 'EMITIDO';
			   }
			elseif($ls_estmov=='N')
			   {
			     $ls_estatus = 'POR CONTABILIZAR';
			   }
			if(strlen($ls_conmov)>48)
			{
				$ls_conmov=substr($ls_conmov,0,46)."..";
			}
			if((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE")))
			{
				if ($ls_estmov!='A')
				   {
				     $ldec_totalcreditos=$ldec_totalcreditos+$ldec_monto;
				   }
				else
				   {
				     $ldec_totaldebitos=$ldec_totaldebitos+$ldec_monto;
				   }								
			}
			////Acumuladores de movimientos que generan un crédito.
			if((($ls_codope=="DP")||($ls_codope=="NC")))
			{
				if ($ls_estmov!='A')
				   {
				     $ldec_totaldebitos=$ldec_totaldebitos+$ldec_monto; 
				   }
				else
				   {
				     $ldec_totalcreditos=$ldec_totalcreditos+$ldec_monto;
				   }								
			}
			$ld_mon      = number_format($ldec_monto,2,",",".");
			$lo_hoja->write($li_fila, 0, $ld_fecmov,$lo_datadate);
			$lo_hoja->write($li_fila, 1, $ls_numdoc.' ',$lo_datacenter);
			$lo_hoja->write($li_fila, 2, $ls_codope,$lo_datacenter);
			$lo_hoja->write($li_fila, 3, $ls_nomproben,$lo_dataleft);
			$lo_hoja->write($li_fila, 4, $ld_mon,$lo_dataright);
			$li_fila++;
		}
		$ldec_saldo         = $ldec_totalcreditos-$ldec_totaldebitos;//Calculo del saldo total para todas las cuentas
		$ldec_totalcreditos = number_format($ldec_totalcreditos,2,",",".");
		$ldec_totaldebitos  = number_format($ldec_totaldebitos,2,",",".");
		$ldec_saldo         = number_format($ldec_saldo,2,",",".");
		uf_print_totales($ldec_totaldebitos,$ldec_totalcreditos,$ldec_saldo,$li_fila,$lo_tituloright,$lo_dataright);

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"documentos_en_transito.xls\"");
		header("Content-Disposition: inline; filename=\"documentos_en_transito.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		//unlink($lo_archivo);
		unset($io_funciones);
		print("<script language=JavaScript>");
	//	print(" close();");
		print("</script>");
	}
	else
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
	//	print(" close();");
		print("</script>");
	}
?> 