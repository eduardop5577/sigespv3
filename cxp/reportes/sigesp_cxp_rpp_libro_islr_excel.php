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
		// Fecha Creación: 15/07/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_libro_islr_timbrefiscal.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "timbre_islr.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	   $ls_titulo="DECLARACION DE IMPUESTO SOBRE LA RENTA";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_mes=$io_fun_cxp->uf_obtenervalor_get("mes","");
	$ls_anio=$io_fun_cxp->uf_obtenervalor_get("anio","");
	$ls_agenteret=$_SESSION["la_empresa"]["nombre"];
	$ls_rifagenteret=$_SESSION["la_empresa"]["rifemp"];
	$ls_diragenteret=$_SESSION["la_empresa"]["direccion"];
	
	$mes="";
	switch ($ls_mes)
	{
		case '01':
			$mes='ENERO';
		break;
		case '02':
			$mes='FEBRERO';
		break;
		case '03':
			$mes='MARZO';
		break;
		case '04':
			$mes='ABRIL';
		break;
		case '05':
			$mes='MAYO';
		break;
		case '06':
			$mes='JUNIO';
		break;
		case '07':
			$mes='JULIO';
		break;
		case '08':
			$mes='AGOSTO';
		break;
		case '09':
			$mes='SEPTIEMBRE';
		break;
		case '10':
			$mes='OCTUBRE';
		break;
		case '11':
			$mes='NOVIEMBRE';
		break;
		case '12':
			$mes='DICIEMBRE';
		break;
	
	}
	$ls_periodo= $mes.' - '.$ls_anio;	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$rs_data="";
		$arrResultado=$io_report->uf_select_beneficiarios_libro_islr($ls_mes,$ls_anio,$rs_data);
		$lb_valido=$arrResultado["lb_valido"];
		$rs_data=$arrResultado["rs_data"];
		unset($arrResultado);

		if(!$lb_valido)
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else
		{
			//-------formato para el reporte----------------------------------------------------------
			$lo_encabezado= &$lo_libro->addformat();
			$lo_encabezado->set_bold();
			$lo_encabezado->set_font("Verdana");
			$lo_encabezado->set_align('center');
			$lo_encabezado->set_size('11');
			$lo_titulo= &$lo_libro->addformat();
			$lo_titulo->set_text_wrap();
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
			
			$lo_dataright2= &$lo_libro->addformat(array('num_format' => '#,##'));
			$lo_dataright2->set_font("Verdana");
			$lo_dataright2->set_align('right');
			$lo_dataright2->set_size('9');	
	
			
			$lo_dataleftcombinado1 =& $lo_libro->addformat();
			$lo_dataleftcombinado1->set_size('9');		
			$lo_dataleftcombinado1->set_font("Verdana");
			$lo_dataleftcombinado1->set_align('left');
			$lo_dataleftcombinado1->set_merge(); # This is the key feature
	
			$lo_dataleftcombinado2 =& $lo_libro->addformat();
			$lo_dataleftcombinado2->set_size('9');		
			$lo_dataleftcombinado2->set_font("Verdana");
			$lo_dataleftcombinado2->set_align('left');
			$lo_dataleftcombinado2->set_merge(); # This is the key feature
	
			$lo_hoja->set_column(0,0,50);
			$lo_hoja->set_column(1,2,15);	
			$lo_hoja->set_column(3,3,40);
			$lo_hoja->set_column(4,4,25);
			$lo_hoja->set_column(5,5,15);
			$lo_hoja->set_column(6,6,20);	
			$lo_hoja->set_column(7,7,20);	
			$lo_hoja->set_column(8,8,15);	
			$lo_hoja->set_column(9,9,15);
			$lo_hoja->set_column(10,10,15);	
			$lo_hoja->set_column(12,12,15);	
			$lo_hoja->set_column(13,13,15);	
			$lo_hoja->set_column(14,14,15);	
			$lo_hoja->set_column(15,15,15);
			$lo_hoja->set_column(16,16,15);	
			$lo_hoja->set_column(17,17,10);	
			$lo_hoja->set_column(18,18,15);	
			$lo_hoja->set_column(19,19,15);		
			$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
	
				$lb_valido=true;
			$li_totalbaseimp2porc=0;
			$li_totalmontret2porc=0;
			$li_totalbaseimp3porc=0;
			$li_totalmontret3porc=0;
			$li_totalbaseimp5porc=0;
			$li_totalmontret5porc=0;
			$li_s=0;
//			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
			$lo_hoja->write(4,0, "NOMBRE DE LA INSTITUCION: ".$ls_agenteret,$lo_dataleftcombinado1);	
			$lo_hoja->write_blank(4,1,                 $lo_dataleftcombinado2);	
			$lo_hoja->write_blank(4,2,                 $lo_dataleftcombinado2);	
			
			$lo_hoja->write(5,0, "RIF ".$ls_rifagenteret,$lo_dataleftcombinado1);
			$lo_hoja->write_blank(5,1,                 $lo_dataleftcombinado2);
			$lo_hoja->write_blank(5,2,                 $lo_dataleftcombinado2);
			
			$lo_hoja->write(6,0, "DIRECCION: ".$ls_diragenteret,$lo_dataleftcombinado1);	
			$lo_hoja->write_blank(6,1,                 $lo_dataleftcombinado2);	
			$lo_hoja->write_blank(6,2,                 $lo_dataleftcombinado2);	
			
			$lo_hoja->write(7,0, "PERIODO: ".$ls_periodo,$lo_dataleftcombinado1);	
			$lo_hoja->write_blank(7,1, $lo_dataleftcombinado2);	
			
			$lo_hoja->write(11,0, "BENEFICIARIO DE LAS REMUNERACIONES",$lo_datacenter);	
			$lo_hoja->write(11,1, "Nº RIF",$lo_datacenter);	
			$lo_hoja->write(11,2, "Nº COMP",$lo_datacenter);	
			$lo_hoja->write(11,3, "MONTO OBJETO DE RETENCIÓN ",$lo_datacenter);	
			$lo_hoja->write(11,4, "ALICUOTA 2%",$lo_datacenter);	
			$lo_hoja->write(11,5, "MONTO OBJETO DE RETENCIÓN ",$lo_datacenter);	
			$lo_hoja->write(11,6, "ALICUOTA 3% SUST. ",$lo_datacenter);	
			$lo_hoja->write(11,7, "MONTO OBJETO DE RETENCIÓN ",$lo_datacenter);	
			$lo_hoja->write(11,8, "ALICUOTA 5% ",$lo_datacenter);	
			$lo_hoja->write(11,9, "DEPENDENCIA ",$lo_datacenter);	
			$li_row=11;
			while ((!$rs_data->EOF)&&($lb_valido)){
				switch (trim($rs_data->fields["procede"])){
					case "SCBBCH":
						$rs_datadetalle= $io_report->uf_retencionesislr_scb($rs_data->fields["numero"],false);  
						break;
						
					case "INT":
						$rs_datadetalle= $io_report->uf_retencionesislr_int($rs_data->fields["numero"],false);
						break;
						
					default:
						$rs_datadetalle= $io_report->uf_retencionesislr_cxp($rs_data->fields["numero"],false);
						break;
				}
				
				while(!$rs_datadetalle->EOF)
				{
					$ls_codpro=$rs_datadetalle->fields["cod_pro"];
					$ls_cedbene=$rs_datadetalle->fields["ced_bene"];
					if($ls_codpro!="----------")
					{
						$ls_tipproben="P";
					}
					else
					{
						$ls_tipproben="B";
					}
					if($ls_tipproben=="P")
					{
						$ls_codigo=$rs_datadetalle->fields["cod_pro"];
						$ls_nombre=$rs_datadetalle->fields["proveedor"];
						$ls_rif=$rs_datadetalle->fields["rifpro"];
					}
					else
					{
						$ls_codigo=$rs_datadetalle->fields["ced_bene"];
						$ls_nombre=$rs_datadetalle->fields["beneficiario"];
						$ls_rif=$rs_datadetalle->fields["rifben"];
					}						 
					
					$ls_dependencia='TESORERIA';
					$li_monobjret=$rs_datadetalle->fields["monobjret"];    
					$li_retenido=$rs_datadetalle->fields["retenido"];  
					$li_porcentaje=$rs_datadetalle->fields["porcentaje"]/100;
					$ls_correlativo=$rs_datadetalle->fields["numcmpislr"];

					//echo $li_porcentaje;
					switch(trim($li_porcentaje))
					{
						case 0.02:
							$li_totalbaseimp2porc=$li_totalbaseimp2porc+$li_monobjret;
							$li_totalmontret2porc=$li_totalmontret2porc+$li_retenido;
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_nombre, $lo_dataleft);
							$lo_hoja->write($li_row, 1, $ls_rif, $lo_dataleft);
							$lo_hoja->write($li_row, 2, $ls_correlativo, $lo_dataleft);
							$lo_hoja->write($li_row, 3, $li_monobjret, $lo_dataright);
							$lo_hoja->write($li_row, 4, $li_retenido, $lo_dataright);
							$lo_hoja->write($li_row, 5, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 6, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 7, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 8, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 9, $ls_dependencia, $lo_datacenter);
							break;
							
						case 0.03:
							$li_totalbaseimp3porc=$li_totalbaseimp3porc+$li_monobjret;
							$li_totalmontret3porc=$li_totalmontret3porc+$li_retenido;
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_nombre, $lo_dataleft);
							$lo_hoja->write($li_row, 1, $ls_rif, $lo_dataleft);
							$lo_hoja->write($li_row, 2, $ls_correlativo, $lo_dataleft);
							$lo_hoja->write($li_row, 3, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 4, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 5, $li_monobjret, $lo_dataright);
							$lo_hoja->write($li_row, 6, $li_retenido, $lo_dataright);
							$lo_hoja->write($li_row, 7, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 8, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 9, $ls_dependencia, $lo_datacenter);
							break;
							
						case 0.05:
							$li_totalbaseimp5porc=$li_totalbaseimp5porc+$li_monobjret;
							$li_totalmontret5porc=$li_totalmontret5porc+$li_retenido;
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_nombre, $lo_dataleft);
							$lo_hoja->write($li_row, 1, $ls_rif, $lo_dataleft);
							$lo_hoja->write($li_row, 2, $ls_correlativo, $lo_dataleft);
							$lo_hoja->write($li_row, 3, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 4, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 5, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 6, "0,00", $lo_dataright);
							$lo_hoja->write($li_row, 7, $li_monobjret, $lo_dataright);
							$lo_hoja->write($li_row, 8, $li_retenido, $lo_dataright);
							$lo_hoja->write($li_row, 9, $ls_dependencia, $lo_datacenter);
							break;
					}
					$rs_datadetalle->MoveNext();
				}
				unset($rs_datadetalle);
				$rs_data->MoveNext();	
			}
			unset($rs_data);
			
				$li_totalbaseimp=$li_totalbaseimp2porc+$li_totalbaseimp3porc+$li_totalbaseimp5porc;
				$li_totalret=$li_totalmontret2porc+$li_totalmontret3porc+$li_totalmontret5porc;
				$lo_hoja->write($li_row+1, 2, "TOTAL", $lo_datacenter);
				$lo_hoja->write($li_row+1, 3, $li_totalbaseimp2porc, $lo_dataright);
				$lo_hoja->write($li_row+1, 4, $li_totalmontret2porc, $lo_dataright);
				$lo_hoja->write($li_row+1, 5, $li_totalbaseimp3porc, $lo_dataright);
				$lo_hoja->write($li_row+1, 6, $li_totalmontret3porc, $lo_dataright);
				$lo_hoja->write($li_row+1, 7, $li_totalbaseimp5porc, $lo_dataright);
				$lo_hoja->write($li_row+1, 8, $li_totalmontret5porc, $lo_dataright);

				$lo_hoja->write($li_row+3, 0, "MONTO OBJETO DE RETENCIÓN (2%) Bs.", $lo_dataleft);
				$lo_hoja->write($li_row+3, 1, $li_totalbaseimp2porc, $lo_dataright);
				$lo_hoja->write($li_row+3, 2, $li_totalmontret2porc, $lo_dataright);
				$lo_hoja->write($li_row+4, 0, "MONTO OBJETO DE RETENCIÓN (3%) Bs.", $lo_dataleft);
				$lo_hoja->write($li_row+4, 1, $li_totalbaseimp3porc, $lo_dataright);
				$lo_hoja->write($li_row+4, 2, $li_totalmontret3porc, $lo_dataright);
				$lo_hoja->write($li_row+5, 0, "MONTO OBJETO DE RETENCIÓN (5%) Bs.", $lo_dataleft);
				$lo_hoja->write($li_row+5, 1, $li_totalbaseimp5porc, $lo_dataright);
				$lo_hoja->write($li_row+5, 2, $li_totalmontret5porc, $lo_dataright);
				$lo_hoja->write($li_row+6, 0, "TOTAL MONTO OBJETO DE RETENCIÓN Bs.", $lo_dataleft);
				$lo_hoja->write($li_row+6, 1, $li_totalbaseimp, $lo_dataright);
				$lo_hoja->write($li_row+6, 2, $li_totalret, $lo_dataright);
				$lo_hoja->write($li_row+7, 2, "TOTAL RETENCIÓN Bs.", $lo_dataleft);
				$lo_hoja->write($li_row+7, 3, $li_totalret, $lo_dataright);
				
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"timbre_islr.xls\"");
				header("Content-Disposition: inline; filename=\"timbre_islr.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);		
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
				unset($la_data);
			unset($io_pdf);
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_cxp);
?> 