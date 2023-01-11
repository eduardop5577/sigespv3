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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	ini_set('memory_limit','1024M');
	ini_set('max_execution_time ','0');
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
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_relacionfacturas.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($lo_libro,$lo_hoja,$as_codigo,$as_nombre,$as_tipproben,$li_fila)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codigo    // Codigo de Proveedor / Beneficiario
		//	   			   as_nombre    // Nombre de Proveedor / Beneficiario
		//	   			   as_tipproben // Tipo de Proveedor / Beneficiario
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;

		if($as_tipproben=="B")
		{
			$lo_hoja->write($li_fila, 0, 'Beneficiario:          ',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $as_codigo.' - '.$as_nombre,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		}
		else
		{
			$lo_hoja->write($li_fila, 0, 'Proveedor:          ',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $as_codigo.' - '.$as_nombre,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		}
		$li_fila++;


		$lo_hoja->write($li_fila, 0, 'Documento',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Concepto',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Fecha Emisión',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Fecha Registro',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Total Factura',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Deducciones',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Neto a Pagar',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'Solicitud de Pago',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_fila++;
		
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($lo_libro,$lo_hoja,$li_totrow,$la_data,$ai_j,$ai_totalfacpro,$ai_totaldedpro,$ai_totaldocpro,$li_fila)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   ai_j // numero de registros
		//				   ai_totalfacpro // acumulado de los montos
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;


		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		for ($index = 0; $index < ($li_totrow+1); $index++)
		{
			//print $index.' Solicitud'.$la_data[$index]["numsol"].'<br>';
			$lo_hoja->write($li_fila, 0, $la_data[$index]["numrecdoc"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$index]["dencondoc"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$index]["fecemidoc"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$index]["fecregdoc"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$index]["montotfac"],$lo_dataright);
			$lo_hoja->write($li_fila, 5, $la_data[$index]["mondeddoc"],$lo_dataright);
			$lo_hoja->write($li_fila, 6, $la_data[$index]["montotdoc"],$lo_dataright);
			$lo_hoja->write($li_fila, 7, $la_data[$index]["numsol"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
		}


			$lo_hoja->write($li_fila, 3, 'Totales: ',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $ai_totalfacpro,$lo_dataright);
			$lo_hoja->write($li_fila, 5, $ai_totaldedpro,$lo_dataright);
			$lo_hoja->write($li_fila, 6, $ai_totaldocpro,$lo_dataright);
			$lo_hoja->write($li_fila, 7, '',$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
			$li_fila++;
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "solicitudes_f1.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	if($ls_estmodest==1)
	{
		$ls_titcuentas="Estructura Presupuestaria";
	}
	else
	{
		$ls_titcuentas="Estructura Programatica";
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="RELACION DE FACTURAS";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$li_ordendoc=$io_fun_cxp->uf_obtenervalor_get("ordendoc","");
	$li_ordenfec=$io_fun_cxp->uf_obtenervalor_get("ordenfec",0);
	$li_ordencod=$io_fun_cxp->uf_obtenervalor_get("ordencod",0);
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobante","");
	$ls_comprobantes=trim($ls_comprobantes);
	$li_totrow_comp=0;
	if ($ls_comprobantes!="")
	{
		$la_comprobantes=explode('<<<',$ls_comprobantes);
		$la_datos=array_unique($la_comprobantes);
		$li_totrow_comp=count((array)$la_datos);
		sort($la_datos,SORT_STRING);
	}
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	$ls_periodo="";
	if(($ld_fecregdes!="")&&($ld_fecreghas!=""))
	{
		$ls_periodo="Del: ".$ld_fecregdes."   "."Al:".$ld_fecreghas;	
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	//
	set_time_limit(1800);

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
	$lo_hoja->set_column(4,4,30);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,30);
	
	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	$lo_hoja->write(1, 3, $ls_periodo,$lo_encabezado);

	$li_fila=3;

	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		if ($li_totrow_comp > 0)
		{
			for ($li_z=0;($li_z<$li_totrow_comp)&&($lb_valido);$li_z++)
			{
				$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
				$ls_numsol2=$la_datos[$li_z];
				$lb_valido=$io_report->uf_select_probenrelacionfacturas($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,
																		$ld_fecregdes,$ld_fecreghas,$ls_numsol2); // Cargar el DS con los datos del reporte
				if($lb_valido==false) // Existe algún error ó no hay registros
				{
					print("<script language=JavaScript>");
					print(" alert('No hay nada que Reportar');"); 
					print(" close();");
					print("</script>");
				}
				else  // Imprimimos el reporte
				{
					$li_totrow=$io_report->DS->getRowCount("codigo");
					$li_totaldoc=0;
					$li_totalcar=0;
					$li_totalded=0;
					for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
					{
						$li_numpag=$io_pdf->ezPageCount; // Número de página
						$ls_codigo=$io_report->DS->data["codigo"][$li_i];
						$ls_nombre=$io_report->DS->data["nombre"][$li_i];
						$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
						uf_print_cabecera($lo_libro,$lo_hoja,$ls_codigo,$ls_nombre,$ls_tipproben,$li_fila);
						$lb_valido=$io_report->uf_select_facturasproben($ls_tipproben,$ls_codigo,$ld_fecregdes,$ld_fecreghas,$li_ordendoc,
																		$li_ordenfec,$ls_numsol2);
						if($lb_valido)
						{
							$li_totrowfac=$io_report->ds_detrecdoc->getRowCount("numrecdoc");
							$li_totalfacpro=0;
							$li_totaldedpro=0;
							$li_totaldocpro=0;
							for($li_j=1;$li_j<=$li_totrowfac;$li_j++)
							{
								$ls_numrecdoc=$io_report->ds_detrecdoc->data["numrecdoc"][$li_j];
								$ld_fecregdoc=$io_report->ds_detrecdoc->data["fecregdoc"][$li_j];
								$ld_fecemidoc=$io_report->ds_detrecdoc->data["fecemidoc"][$li_j];
								$ls_dencondoc=$io_report->ds_detrecdoc->data["dencondoc"][$li_j];
								$li_montotdoc=$io_report->ds_detrecdoc->data["montotdoc"][$li_j];
								$li_moncardoc=$io_report->ds_detrecdoc->data["moncardoc"][$li_j];
								$li_mondeddoc=$io_report->ds_detrecdoc->data["mondeddoc"][$li_j];
								$li_montotfac=$li_montotdoc+$li_mondeddoc;
								$ls_numsol=$io_report->ds_detrecdoc->data["numsol"][$li_j];
								$ld_fecregdoc=$io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
								$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
								$li_totalfacpro=$li_totalfacpro + $li_montotfac;
								$li_totaldedpro=$li_totaldedpro + $li_mondeddoc;
								$li_totaldocpro=$li_totaldocpro + $li_montotdoc;
								$li_montotdoc=number_format($li_montotdoc,2,',','.');
								$li_montotfac=number_format($li_montotfac,2,',','.');
								$li_mondeddoc=number_format($li_mondeddoc,2,',','.');
								$la_data[$li_j]=array('numrecdoc'=>$ls_numrecdoc,'dencondoc'=>$ls_dencondoc,'fecemidoc'=>$ld_fecemidoc,
													  'fecregdoc'=>$ld_fecregdoc,'montotfac'=>$li_montotfac,'mondeddoc'=>$li_mondeddoc,
													  'montotdoc'=>$li_montotdoc,'numsol'=>$ls_numsol);
							}
							$li_totalfacpro=number_format($li_totalfacpro,2,',','.');
							$li_totaldedpro=number_format($li_totaldedpro,2,',','.');
							$li_totaldocpro=number_format($li_totaldocpro,2,',','.');
							uf_print_detalle_recepcion($lo_libro,$lo_hoja,$li_j,$la_data,$li_totrowfac,$li_totalfacpro,$li_totaldedpro,$li_totaldocpro,$li_fila);
						}
						unset($la_data);
					}
				}
			}
			if($lb_valido) // Si no ocurrio ningún error
			{
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"relacion_facturas.xls\"");
				header("Content-Disposition: inline; filename=\"relacion_facturas.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				//print(" close();");
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
		else
		{
				$lb_valido=$io_report->uf_select_probenrelacionfacturas($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,
																		$ld_fecregdes,$ld_fecreghas,""); // Cargar el DS con los datos del reporte
				if($lb_valido==false) // Existe algún error ó no hay registros
				{
					print("<script language=JavaScript>");
					print(" alert('No hay nada que Reportar');"); 
					print(" close();");
					print("</script>");
				}
				else  // Imprimimos el reporte
				{
					$li_totrow=$io_report->DS->getRowCount("codigo");
					$li_totaldoc=0;
					$li_totalcar=0;
					$li_totalded=0;
					for($li_i=1;($li_i<=$li_totrow)&&($lb_valido);$li_i++)
					{
						$ls_codigo=$io_report->DS->data["codigo"][$li_i];
						$ls_nombre=$io_report->DS->data["nombre"][$li_i];
						$ls_tipproben=$io_report->DS->data["tipproben"][$li_i];
						uf_print_cabecera($lo_libro,$lo_hoja,$ls_codigo,$ls_nombre,$ls_tipproben,$li_fila);
						$lb_valido=$io_report->uf_select_facturasproben($ls_tipproben,$ls_codigo,$ld_fecregdes,$ld_fecreghas,$li_ordendoc,
																		$li_ordenfec,"");
						if($lb_valido)
						{
							$li_totrowfac=$io_report->ds_detrecdoc->getRowCount("numrecdoc");
							$li_totalfacpro=0;
							$li_totaldedpro=0;
							$li_totaldocpro=0;
							for($li_j=1;$li_j<=$li_totrowfac;$li_j++)
							{
								$ls_numrecdoc=$io_report->ds_detrecdoc->data["numrecdoc"][$li_j];
								$ld_fecregdoc=$io_report->ds_detrecdoc->data["fecregdoc"][$li_j];
								$ld_fecemidoc=$io_report->ds_detrecdoc->data["fecemidoc"][$li_j];
								$ls_dencondoc=$io_report->ds_detrecdoc->data["dencondoc"][$li_j];
								$li_montotdoc=$io_report->ds_detrecdoc->data["montotdoc"][$li_j];
								$li_moncardoc=$io_report->ds_detrecdoc->data["moncardoc"][$li_j];
								$li_mondeddoc=$io_report->ds_detrecdoc->data["mondeddoc"][$li_j];
								$li_montotfac=$li_montotdoc+$li_mondeddoc;
								$ls_numsol=$io_report->ds_detrecdoc->data["numsol"][$li_j];
								$ld_fecregdoc=$io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
								$ld_fecemidoc=$io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
								$li_totalfacpro=$li_totalfacpro + $li_montotfac;
								$li_totaldedpro=$li_totaldedpro + $li_mondeddoc;
								$li_totaldocpro=$li_totaldocpro + $li_montotdoc;
								$li_montotdoc=number_format($li_montotdoc,2,',','.');
								$li_montotfac=number_format($li_montotfac,2,',','.');
								$li_mondeddoc=number_format($li_mondeddoc,2,',','.');
								$la_data[$li_j]=array('numrecdoc'=>$ls_numrecdoc,'dencondoc'=>$ls_dencondoc,'fecemidoc'=>$ld_fecemidoc,
													  'fecregdoc'=>$ld_fecregdoc,'montotfac'=>$li_montotfac,'mondeddoc'=>$li_mondeddoc,
													  'montotdoc'=>$li_montotdoc,'numsol'=>$ls_numsol);
							}
							$li_totalfacpro=number_format($li_totalfacpro,2,',','.');
							$li_totaldedpro=number_format($li_totaldedpro,2,',','.');
							$li_totaldocpro=number_format($li_totaldocpro,2,',','.');
							uf_print_detalle_recepcion($lo_libro,$lo_hoja,$li_j,$la_data,$li_totrowfac,$li_totalfacpro,$li_totaldedpro,$li_totaldocpro,$li_fila);
						}
						unset($la_data);			
					}
					if($lb_valido) // Si no ocurrio ningún error
					{
						$lo_libro->close();
						header("Content-Type: application/x-msexcel; name=\"relacion_facturas.xls\"");
						header("Content-Disposition: inline; filename=\"relacion_facturas.xls\"");
						$fh=fopen($lo_archivo, "rb");
						fpassthru($fh);
						unlink($lo_archivo);
						print("<script language=JavaScript>");
						//print(" close();");
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
		
	}
	unset($io_report);
	unset($io_funciones);
?>
