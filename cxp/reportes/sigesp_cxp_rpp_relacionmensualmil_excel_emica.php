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
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_recepciones.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($li_totrows,$lo_libro,$lo_hoja,$la_data,$totcmp_con_iva,$totimp,$iva_ret,$li_fila)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//				   li_totaldoc // acumulado del total
		//				   li_totalcar // acumulado de los cargos
		//				   li_totalded // acumulado de las deducciones
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci?n: 20/05/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $li_fila;
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('8');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('8');
		$lo_hoja->write($li_fila, 0, 'No.',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'FECHA DE ORDEN DE PAGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'NUMERO DE ORDEN DE PAGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'NOMBRE CONTRIBUYENTE',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'CI/RIF DEL CONTRIBUYENTE',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'MONTO DE LA OBRA O SERVICIO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'BASE IMPONIBLE',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'MONTO DEL IMPUESTO RETENIDO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 8, 'TIPO DE PAGO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 9, 'MUNICIPIO',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 10, 'OPERACIONES ANULADAS O REVERSADAS',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		for($i=1;$i<=$li_totrows;$i++)
		{
			$li_fila++;
			$lo_hoja->write($li_fila, 0, " ".$la_data[$i]['li_i'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 1, " ".$la_data[$i]['numdocpag'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 2, " ".$la_data[$i]['fecmov'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 3, " ".$la_data[$i]['nomsujret'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 4, " ".$la_data[$i]['rif'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 5, $la_data[$i]['monto'],$lo_dataright);
			$lo_hoja->write($li_fila, 6, $la_data[$i]['totcmp_con_iva'],$lo_dataright);
			$lo_hoja->write($li_fila, 7, $la_data[$i]['iva_ret'],$lo_dataright);
			$lo_hoja->write($li_fila, 8, " ".$la_data[$i]['tipopago'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 9, " IRIBARREN",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write($li_fila, 10, " ".$la_data[$i]['vacio3'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		}
		$li_fila++;
		$lo_hoja->write($li_fila, 6, " TOTAL RETENIDA",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$lo_hoja->write($li_fila, 7, $iva_ret,$lo_dataright);
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "RESPONSABLE DE LA DECLARACION",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "AGENTE DE RETENCION",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Nombre y Apellido:    LORENA  ELIZABETH  MARMOL  JIMENEZ",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Numero de C.I.:       V-7.376.079",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Cargo:                Gerente de Administraci?n y Finanzas",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$lo_hoja->write($li_fila, 5, "___________________________________________________________",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Tel?fono:             0416-5013988",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
		$lo_hoja->write($li_fila, 5, "LCDA. LORENA  ELIZABETH  MARMOL  JIMENEZ",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'center','size'=>'8')));
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Correo Electr?nico:   emicasa1999@hotmail.com",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'center','size'=>'8')));
		$lo_hoja->write($li_fila, 5, "GERENTE DE ADMON. Y FINANZAS",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'center','size'=>'8')));
	
	}// end function uf_print_detalle
	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "relacion_mensual.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversi?n de numeros a letras.
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_perfiscal=substr($ld_fecregdes,3,2)." - ".substr($ld_fecregdes,6,4);
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_retencionesunoxmil($ld_fecregdes,$ld_fecreghas); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe alg?n error ? no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('9');
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
			$lo_hoja->set_column(4,4,30);
			$lo_hoja->set_column(5,5,30);
			$lo_hoja->set_column(6,6,30);
	
			$lo_hoja->write(0, 3, "REPUBLICA BOLIVARIANA DE VENEZUELA",$lo_encabezado);
			$lo_hoja->write(1, 3, "GOBIERNO DEL ESTADO LARA",$lo_encabezado);
			$lo_hoja->write(2, 3, "SERVICIO AUTONOMO DE ADMINISTRACION TRIBUTARIA",$lo_encabezado);
			$lo_hoja->write(3, 3, "DEL ESTADO LARA",$lo_encabezado);
			$lo_hoja->write(4, 3, "RELACION MENSUAL IMPUESTO 1X1000 ENTES PUBLICOS",$lo_encabezado);

			$lo_hoja->write(6, 0, "Ente P?blico:            ".$_SESSION["la_empresa"]["nombre"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write(7, 0, "R.I.F.:                     ".$_SESSION["la_empresa"]["rifemp"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write(8, 0, "Direcci?n:               ".$_SESSION["la_empresa"]["direccion"],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write(9, 0, "Periodo:                  ".$ls_perfiscal,$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));
			$lo_hoja->write(10, 0, "Nro.(s) de Planilla (s) Bancaria (s): ___________________________________",$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'8')));

			$li_fila=12;
			$li_totrow=$io_report->DS->getRowCount("numcom");
			$totcmp_con_iva= 0;
			$totimp= 0;
			$iva_ret= 0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numsop= $io_report->DS->data["numsop"][$li_i];
				$ls_numdocpag= $io_report->DS->data["numdocpag"][$li_i]; 
				$ls_numcom= $io_report->DS->data["numcom"][$li_i];
				$ls_nomban= $io_report->DS->data["nomban"][$li_i];
				$ld_fecmov= $io_report->DS->data["fecemisol"][$li_i];
				$ls_nomsujret= $io_report->DS->data["nomsujret"][$li_i];
				$ls_rif= $io_report->DS->data["rif"][$li_i];
				$li_totcmp_con_iva= $io_report->DS->data["totcmp_con_iva"][$li_i];
				$li_totimp= $io_report->DS->data["totimp"][$li_i];
				$li_iva_ret= $io_report->DS->data["iva_ret"][$li_i];
				$li_basimp= $io_report->DS->data["basimp"][$li_i];
				$ld_fecmov= $io_funciones->uf_convertirfecmostrar($ld_fecmov);
				$rs_compromisos=$io_report->uf_select_compromisos_relacionados($ls_numsop);
				$li_montocompromiso=0;
				while(!$rs_compromisos->EOF)
				{
					$ls_numdoccom=$rs_compromisos->fields["numdoccom"];
					$ls_procede=$rs_compromisos->fields["procede_doc"];
				//	print $ls_numdoccom." ->".$ls_numsop."->".$ls_procede."<br>";
					if($ls_procede=="CXPRCD")
					{
						$li_compromiso=$io_report->uf_select_monto_recepcion($ls_numsop,$ls_numdoccom);
					}
					else
					{
						$li_compromiso=$io_report->uf_select_monto_compromisos($ls_numdoccom,$ls_procede);
					}
					$li_montocompromiso=$li_montocompromiso+$li_compromiso;
					$rs_compromisos->MoveNext();
				}
				if($li_montocompromiso==0)
				{
					$li_compromiso=$io_report->uf_select_monto_recepcion_contable($ls_numsop,$ls_numfac);
					$li_montocompromiso=$li_montocompromiso+$li_compromiso;
				}

//				$li_totaldoc= $li_totaldoc + $li_montotdoc;
//				$li_totalcar= $li_totalcar + $li_moncardoc;
//				$li_totalded= $li_totalded + $li_mondeddoc;

				if($li_totcmp_con_iva>=$li_montocompromiso)
				{
					$ls_tipopago="UNICO";
				}
				else
				{
					$ls_tipopago="PARCIAL";
				}
				$li_totcmp_con_iva= number_format($li_totcmp_con_iva,2,'.','');
				$li_totimp= number_format($li_totimp,2,'.','');
				$li_iva_ret= number_format($li_iva_ret,2,'.','');
				
				$totcmp_con_iva= $totcmp_con_iva + $li_totcmp_con_iva;
				$totimp= $totimp + $li_totimp;
				$iva_ret= $iva_ret + $li_iva_ret;

				$la_data[$li_i]=array('li_i'=>$li_i,'numsop'=>$ls_numsop,'numdocpag'=>$ls_numdocpag,'vacio1'=>"",'numcom'=>$ls_numcom,'nomban'=>$ls_nomban,
									  'fecmov'=>$ld_fecmov,'vacio2'=>"",'nomsujret'=>$ls_nomsujret,'rif'=>$ls_rif,
									  'totcmp_con_iva'=>$li_totcmp_con_iva,'totimp'=>$li_totimp,'iva_ret'=>$li_iva_ret,'vacio3'=>"",'monto'=>$li_montocompromiso,'tipopago'=>$ls_tipopago);
			}
			uf_print_detalle_recepcion($li_i,$lo_libro,$lo_hoja,$la_data,$totcmp_con_iva,$totimp,$iva_ret,$li_fila);
			if($lb_valido) // Si no ocurrio ning?n error
			{
				unset($io_report);
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"relacion_mensual.xls\"");
				header("Content-Disposition: inline; filename=\"relacion_mensual.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
			}
			else // Si hubo alg?n error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}

?>
