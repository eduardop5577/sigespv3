<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_recepciones.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,$as_titulo,$as_tipproben,$as_codprobendes,$as_codprobenhas,$as_nomprobendes,$as_nomprobenhas,$as_periodo,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		
		if(($as_codprobendes!="")&&($as_codprobendes!=""))
		{
			switch($as_tipproben)
			{
				case"P":
					if($as_codprobendes==$as_codprobenhas)
					{
						$lo_hoja->write($li_fila, 2, "Proveedor: ".$as_codprobendes." - ".$as_nomprobendes,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
					
					}
					else
					{
						$lo_hoja->write($li_fila, 2, "Proveedores: ",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
						$li_fila++;
						$lo_hoja->write($li_fila, 2, "Desde: ".$as_codprobendes." - ".$as_nomprobendes,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
						$li_fila++;
						$lo_hoja->write($li_fila, 2, "Hasta: ".$as_codprobenhas." - ".$as_nomprobenhas,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
					}
				break;
				case"B":
					if($as_codprobendes==$as_codprobenhas)
					{
						$lo_hoja->write($li_fila, 2, "Beneficiario: ".$as_codprobendes." - ".$as_nomprobendes,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
					
					}
					else
					{
						$lo_hoja->write($li_fila, 2, "Beneficiarios: ",$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
						$li_fila++;
						$lo_hoja->write($li_fila, 2, "Desde: ".$as_codprobendes." - ".$as_nomprobendes,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
						$li_fila++;
						$lo_hoja->write($li_fila, 2, "Hasta: ".$as_codprobenhas." - ".$as_nomprobenhas,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
						$ls_criterio="Beneficiarios: ";
					}
				break;
				$li_fila++;
			}
		}

		$li_fila++;
		$lo_hoja->write($li_fila, 2, $as_periodo,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$li_fila++;
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($lo_libro,$lo_hoja,$la_data,$li_totalbasimp,$li_totalbasiva,$li_totalded,$li_totalcar,$li_totretiva,$li_totretislr,$li_totretaposol,$li_totretmilp,$li_totmontotdoc,$li_total,$li_fila)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   li_totaldoc // acumulado del total
		//				   li_totalcar // acumulado de los cargos
		//				   li_totalded // acumulado de las deducciones
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');

		$li_fila++;

		$lo_hoja->write($li_fila, 0, 'Usuario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Documento',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Proveedor / Beneficiario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Fecha Emision',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Fecha Registro',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Procedencia',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Compromiso',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'Sub Total',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 8, 'Cargos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 9, 'Total Factura',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 10, 'Reten. ISLR',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 11, 'Reten. IVA',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 12, 'Reten. Aporte Social',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 13, 'Reten. 1x1000',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 14, 'Neto a Pagar',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 15, 'Estatus',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 16, 'Cheque',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 17, 'Fecha',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));

		$li_fila++;
		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, $la_data[$li_j]['codusureg'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['numrecdoc'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['nombre'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['fecemidoc'],$lo_datadate);
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['fecregdoc'],$lo_datadate);
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['procede_doc'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 6, $la_data[$li_j]['numdoccom'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 7, $la_data[$li_j]['basimp'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 8, $la_data[$li_j]['moncardoc'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 9, $la_data[$li_j]['basiva'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 10, $la_data[$li_j]['islr'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 11, $la_data[$li_j]['iva'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 12, $la_data[$li_j]['retaposol'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 13, $la_data[$li_j]['estretmil'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 14, $la_data[$li_j]['montotdoc'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
			$lo_hoja->write($li_fila, 15, $la_data[$li_j]['estprosol'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 16, $la_data[$li_j]['cheques'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$lo_hoja->write($li_fila, 17, $la_data[$li_j]['fechache'],$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
			$li_fila++;
		}
		$li_fila++;

		$lo_hoja->write($li_fila, 6, 'TOTALES...',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 7, $li_totalbasimp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 8, $li_totalcar,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 9, $li_totalbasiva,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 10, $li_totretislr,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 11, $li_totretiva,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 12, $li_totretaposol,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 13, $li_totretmilp,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));
		$lo_hoja->write($li_fila, 14, $li_totmontotdoc,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'right','size'=>'9')));

	}// end function uf_print_detalle
	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "cuentas_por_pagar.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversión de numeros a letras.
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="CUENTAS POR PAGAR";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=trim($io_fun_cxp->uf_obtenervalor_get("codprobendes",""));
	$ls_codprobenhas=trim($io_fun_cxp->uf_obtenervalor_get("codprobenhas",""));
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_nomprobendes="";
	$ls_nomprobenhas="";
	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_periodo="";
	if(($ld_fecregdes!="")&&($ld_fecreghas!=""))
	{
		$ls_periodo="Periodo: Del: ".$ld_fecregdes."   "."Al: ".$ld_fecreghas;	
	}
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_select_cxp_f2($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fecregdes,$ld_fecreghas,$ls_orden); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			//print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
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
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,50);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,20);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(6,7,20);
		$lo_hoja->set_column(6,8,20);
		$lo_hoja->set_column(6,9,20);
		$lo_hoja->set_column(6,10,20);

		$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);

		$li_fila=2;

			$li_totrow=$io_report->DS->getRowCount("numrecdoc");
			if($ls_codprobendes!="")
				$ls_nomprobendes=$io_report->uf_select_proveedores($ls_tipproben,$ls_codprobendes);
			if($ls_codprobenhas!="")
				$ls_nomprobenhas=$io_report->uf_select_proveedores($ls_tipproben,$ls_codprobenhas);
			$li_totalbasimp= 0;
			$li_totalbasiva= 0;
			$li_totalded= 0;
			$li_totalcar= 0;

			$li_totretiva=0;
			$li_totretislr= 0;
			$li_totretaposol= 0;
			$li_totmontotdoc=0;
			$li_totretmil= 0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numrecdoc= $io_report->DS->data["numrecdoc"][$li_i];
				$ls_codpro= $io_report->DS->data["cod_pro"][$li_i];
				$ls_cedbene= $io_report->DS->data["ced_bene"][$li_i];
				$ls_codtipdoc= $io_report->DS->data["codtipdoc"][$li_i];
				$ls_nombre= $io_report->DS->data["nombre"][$li_i]; 
				$ls_numsol= $io_report->DS->data["numsol"][$li_i]; 
				$ls_estprosol= $io_report->DS->data["estprosol"][$li_i]; 
				switch ($ls_estprosol)
				{
					case "R":
						$ls_estprosol="Registro";
						break;
						
					case "S":
						$ls_estprosol="Programacion de Pago";
						break;
						
					case "P":
						$ls_estprosol="Cancelada";
						break;

					case "A":
						$ls_estprosol="Anulada";
						break;
						
					case "C":
						$ls_estprosol="Contabilizada";
						break;
						
					case "E":
						$ls_estprosol="Emitida";
						break;
						
					case "N":
						$ls_estprosol="Anulada sin Afectacion";
						break;
				}
				$ls_procede= $io_report->DS->data["procede_doc"][$li_i];
				if($ls_procede=="")
				{
					$ls_procede=$io_report->DS->data["procede_cont"][$li_i];
				}
				$ls_numdoccom= $io_report->DS->data["numdoccom"][$li_i];
				if($ls_numdoccom=="")
				{
					$ls_numdoccom=$io_report->DS->data["numdoccont"][$li_i];
				}
				$ls_codproben= $ls_codpro;
				if($ls_codproben=="----------")
				{
					$ls_codproben= $ls_cedbene;
				}
				$ld_fecemidoc= $io_report->DS->data["fecemidoc"][$li_i];
				$ld_fecregdoc= $io_report->DS->data["fecregdoc"][$li_i];
				$ls_codusureg= $io_report->DS->data["codusureg"][$li_i];
				$li_pagado=$io_report->uf_select_informacionpagos($ls_numsol);
				$ls_cheques=$io_report->uf_select_informacioncheques($ls_numsol);
				$ls_fechache=$io_report->uf_select_fechapagos($ls_numsol);
				$ld_fecemidoc= $io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
				$ld_fecregdoc= $io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
				$ls_fechache= $io_funciones->uf_convertirfecmostrar($ls_fechache);
				
				$li_montotdoc= $io_report->DS->data["montotdoc"][$li_i];
				$li_mondeddoc= $io_report->DS->data["mondeddoc"][$li_i];
				$li_moncardoc= $io_report->DS->data["moncardoc"][$li_i];
				$li_iva=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'iva');
				$li_islr=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'islr');
				$li_retaposol=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'retaposol');
				$li_estretmil=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'estretmil');
				$li_basimp=$li_montotdoc+$li_mondeddoc-$li_moncardoc;
				$li_basiva=$li_basimp+$li_moncardoc;
				
				$li_totalbasimp= $li_totalbasimp + $li_basimp;
				$li_totalbasiva= $li_totalbasiva + $li_basiva;
				$li_totalded= $li_totalded + $li_mondeddoc;
				$li_totalcar= $li_totalcar + $li_moncardoc;
				$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;

				$li_totretiva= $li_totretiva + $li_iva;
				$li_totretislr= $li_totretislr + $li_islr;
				$li_totretaposol= $li_totretaposol + $li_retaposol;
				$li_totretmil= $li_totretmil + $li_estretmil;
				
				$li_montotdoc= number_format($li_montotdoc,2,',','.');
				$li_mondeddoc= number_format($li_mondeddoc,2,',','.');
				$li_moncardoc= number_format($li_moncardoc,2,',','.');
				$li_basimp= number_format($li_basimp,2,',','.');
				$li_basiva= number_format($li_basiva,2,',','.');
				$li_iva= number_format($li_iva,2,',','.');
				$li_islr= number_format($li_islr,2,',','.');
				$li_retaposol= number_format($li_retaposol,2,',','.');
				$li_estretmil= number_format($li_estretmil,2,',','.');
				$la_data[$li_i]=array('codusureg'=>$ls_codusureg,'numrecdoc'=>$ls_numrecdoc,'fecemidoc'=>$ld_fecemidoc,'fecregdoc'=>$ld_fecregdoc,'codproben'=>$ls_codproben,
									  'nombre'=>$ls_nombre,'procede_doc'=>$ls_procede,'numdoccom'=>$ls_numdoccom,'basimp'=>$li_basimp,
									  'moncardoc'=>$li_moncardoc,'basiva'=>$li_basiva,'islr'=>$li_islr,'iva'=>$li_iva,'retaposol'=>$li_retaposol,
									  'estretmil'=>$li_estretmil,'montotdoc'=>$li_montotdoc,'montotdoc'=>$li_montotdoc,'montotdoc'=>$li_montotdoc,
									  'montotdoc'=>$li_montotdoc,'estprosol'=>$ls_estprosol,'cheques'=>$ls_cheques,'fechache'=>$ls_fechache);
			}
			$li_totalbasimp= number_format($li_totalbasimp,2,',','.');
			$li_totalbasiva= number_format($li_totalbasiva,2,',','.');
			$li_totalded= number_format($li_totalded,2,',','.');
			$li_totalcar= number_format($li_totalcar,2,',','.');
			$li_totretiva= number_format($li_totretiva,2,',','.');
			$li_totretislr= number_format($li_totretislr,2,',','.');
			$li_totretaposol= number_format($li_totretaposol,2,',','.');
			$li_totretmil= number_format($li_totretmil,2,',','.');
			$li_totmontotdoc= number_format($li_totmontotdoc,2,',','.');
			uf_print_encabezado_pagina($lo_libro,$lo_hoja,$ls_titulo,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ls_nomprobendes,$ls_nomprobenhas,$ls_periodo,$li_fila);
			uf_print_detalle_recepcion($lo_libro,$lo_hoja,$la_data,$li_totalbasimp,$li_totalbasiva,$li_totalded,$li_totalcar,$li_totretiva,$li_totretislr,$li_totretaposol,$li_totretmil,$li_totmontotdoc,$li_i,$li_fila);
			if($lb_valido) // Si no ocurrio ningún error
			{
				unset($io_report);
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"cuentas_por_pagar.xls\"");
				header("Content-Disposition: inline; filename=\"cuentas_por_pagar.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
			}
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}

?>
