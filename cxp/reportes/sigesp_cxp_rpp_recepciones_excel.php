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
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,$as_tipproben,$as_codprobendes,$as_codprobenhas,$as_nomprobendes,$as_nomprobenhas,$li_fila)
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
		$lo_hoja->write($li_fila, 0, 'Documento',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Expediente',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Proveedor / Beneficiario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Fecha Emision',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Fecha Registro',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Procedencia',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Compromiso',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'Base Imponible',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 8, 'Deducciones',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 9, 'Cargos',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 10, 'Monto Total Factura',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($lo_libro,$lo_hoja,$la_data,$li_totaldoc,$li_totalcar,$li_totalded,$li_totbasimp,$li_total,$li_fila)
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
		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, " ".$la_data[$li_j]['numrecdoc'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['numexprel'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['nombre'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['fecemidoc'],$lo_datadate);
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['fecregdoc'],$lo_datadate);
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['procede_doc'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 6, $la_data[$li_j]['numdoccom'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 7, $la_data[$li_j]['basimp'],$lo_dataright);
			$lo_hoja->write($li_fila, 8, $la_data[$li_j]['mondeddoc'],$lo_dataright);
			$lo_hoja->write($li_fila, 9, $la_data[$li_j]['moncardoc'],$lo_dataright);
			$lo_hoja->write($li_fila, 10, $la_data[$li_j]['montotdoc'],$lo_dataright);
			$li_fila++;
		}

		$lo_hoja->write($li_fila, 6, 'Totales ',$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 7, $li_totbasimp,$lo_dataright);
		$lo_hoja->write($li_fila, 8, $li_totalded,$lo_dataright);
		$lo_hoja->write($li_fila, 9, $li_totalcar,$lo_dataright);
		$lo_hoja->write($li_fila, 10, $li_totaldoc,$lo_dataright);


	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversión de numeros a letras.
	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "recepciones.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="RECEPCIONES DE DOCUMENTOS";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=trim($io_fun_cxp->uf_obtenervalor_get("codprobendes",""));
	$ls_codprobenhas=trim($io_fun_cxp->uf_obtenervalor_get("codprobenhas",""));
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_codtipdoc=$io_fun_cxp->uf_obtenervalor_get("codtipdoc","");
	$ls_registrada=$io_fun_cxp->uf_obtenervalor_get("registrada","");
	$ls_anulada=$io_fun_cxp->uf_obtenervalor_get("anulada","");
	$ls_procesada=$io_fun_cxp->uf_obtenervalor_get("procesada","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_numexprel=$io_fun_cxp->uf_obtenervalor_get("numexprel","");
	$ls_nomprobendes="";
	$ls_nomprobenhas="";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_select_recepciones($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fecregdes,$ld_fecreghas,
													 $ls_codtipdoc,$ls_registrada,$ls_anulada,$ls_procesada,$ls_orden,$ls_numexprel); // Cargar el DS con los datos del reporte
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
			$li_totaldoc=0;
			$li_totalcar=0;
			$li_totalded=0;
			$li_totbasimp=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numrecdoc= $io_report->DS->data["numrecdoc"][$li_i];
				$ls_numexprel= $io_report->DS->data["numexprel"][$li_i];
				$ls_nombre= $io_report->DS->data["nombre"][$li_i]; 
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
				$ld_fecemidoc= $io_report->DS->data["fecemidoc"][$li_i];
				$ld_fecregdoc= $io_report->DS->data["fecregdoc"][$li_i];
				$li_montotdoc= $io_report->DS->data["montotdoc"][$li_i];
				$li_mondeddoc= $io_report->DS->data["mondeddoc"][$li_i];
				$li_moncardoc= $io_report->DS->data["moncardoc"][$li_i];
				$ld_fecemidoc= $io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
				$ld_fecregdoc= $io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
				$li_basimp=$li_montotdoc+$li_mondeddoc-$li_moncardoc;
				$li_totaldoc= $li_totaldoc + $li_montotdoc;
				$li_totalcar= $li_totalcar + $li_moncardoc;
				$li_totalded= $li_totalded + $li_mondeddoc;
				$li_totbasimp= $li_totbasimp + $li_basimp;
				$li_montotdoc= number_format($li_montotdoc,2,',','.');
				$li_mondeddoc= number_format($li_mondeddoc,2,',','.');
				$li_moncardoc= number_format($li_moncardoc,2,',','.');
				$li_basimp= number_format($li_basimp,2,',','.');
				$la_data[$li_i]=array('numrecdoc'=>$ls_numrecdoc,'numexprel'=>$ls_numexprel,'nombre'=>$ls_nombre,'fecemidoc'=>$ld_fecemidoc,'fecregdoc'=>$ld_fecregdoc,
									  'procede_doc'=>$ls_procede,'numdoccom'=>$ls_numdoccom,'basimp'=>$li_basimp,
									  'mondeddoc'=>$li_mondeddoc,'moncardoc'=>$li_moncardoc,'montotdoc'=>$li_montotdoc);
			}
			$li_totbasimp= number_format($li_totbasimp,2,',','.');
			$li_totaldoc= number_format($li_totaldoc,2,',','.');
			$li_totalcar= number_format($li_totalcar,2,',','.');
			$li_totalded= number_format($li_totalded,2,',','.');
			uf_print_encabezado_pagina($lo_libro,$lo_hoja,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ls_nomprobendes,$ls_nomprobenhas,$li_fila);
			uf_print_detalle_recepcion($lo_libro,$lo_hoja,$la_data,$li_totaldoc,$li_totalcar,$li_totalded,$li_totbasimp,$li_i,$li_fila);
			if($lb_valido)
			{
				unset($io_report);
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"recepciones.xls\"");
				header("Content-Disposition: inline; filename=\"recepciones.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
			}
			else
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generarse el Reporte');"); 
				print(" close();");
				print("</script>");
			}
		}
	}

?>
