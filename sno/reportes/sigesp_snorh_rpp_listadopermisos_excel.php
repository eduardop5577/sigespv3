<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonal.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_personal.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Listado de Permisos";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_activo=$io_fun_nomina->uf_obtenervalor_get("activo","");
	$ls_egresado=$io_fun_nomina->uf_obtenervalor_get("egresado","");
	$ls_causaegreso=$io_fun_nomina->uf_obtenervalor_get("causaegreso","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_activono=$io_fun_nomina->uf_obtenervalor_get("activono","");
	$ls_vacacionesno=$io_fun_nomina->uf_obtenervalor_get("vacacionesno","");
	$ls_suspendidono=$io_fun_nomina->uf_obtenervalor_get("suspendidono","");
	$ls_egresadono=$io_fun_nomina->uf_obtenervalor_get("egresadono","");
	$ls_masculino=$io_fun_nomina->uf_obtenervalor_get("masculino","");
	$ls_femenino=$io_fun_nomina->uf_obtenervalor_get("femenino","");
	$ls_fec_des=$io_fun_nomina->uf_obtenervalor_get("fec_desde","");
	$ls_fec_has=$io_fun_nomina->uf_obtenervalor_get("fec_hasta","");
	$ls_tipo_permiso=$io_fun_nomina->uf_obtenervalor_get("tipo_permiso","");
	$ls_uniadmin=$io_fun_nomina->uf_obtenervalor_get("uniadmin","");
	$ls_codestper=$io_fun_nomina->uf_obtenervalor_get("codestper","");
	$ls_codmunper=$io_fun_nomina->uf_obtenervalor_get("codmunper","");
	$ls_codparper=$io_fun_nomina->uf_obtenervalor_get("codparper","");
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Listado de Permisos en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_permisospersonal_personal($ls_codnomdes,$ls_codnomhas,$ls_codperdes,$ls_codperhas,$ls_activo,
														    $ls_egresado,$ls_causaegreso,$ls_activono,$ls_vacacionesno,
														    $ls_suspendidono,$ls_egresadono,$ls_masculino,$ls_femenino,$ls_orden,
															$ls_fec_des,$ls_fec_has,$ls_tipo_permiso,$ls_uniadmin,$ls_codestper,$ls_codmunper,$ls_codparper); // Obtenemos el detalle del reporte
	}
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
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('left');
		$lo_datacenter->set_size('9');
		$lo_datacenterdet= &$lo_libro->addformat();
		$lo_datacenterdet->set_font("Verdana");
		$lo_datacenterdet->set_align('center');
		$lo_datacenterdet->set_size('9');
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
		$lo_hoja->set_column(1,1,10);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,20);
		$lo_hoja->set_column(6,6,20);
		$lo_hoja->set_column(7,7,20);
		$lo_hoja->set_column(8,8,30);
		$lo_hoja->set_column(9,9,50);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,12,50);
		$lo_hoja->set_column(13,14,20);
		$lo_hoja->set_column(15,18,50);

		$li_row_tit=0;
		$li_row_tit2=3;
		$li_det=5;
		$li_det2=6;
		$li_det3=7;
		$li_det4=9;

		$li_totrow=$io_report->DS->getRowCount("codper");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$lo_hoja->write($li_row_tit,4,$ls_titulo,$lo_encabezado);
			$lo_hoja->write($li_row_tit2, 0, "Datos del Personal",$lo_titulo);
			$li_row_tit=$li_row_tit+20;
			$li_row_tit2=$li_row_tit2+20;
			
			$lo_hoja->write($li_det, 0, "Cédula",$lo_titulo);
			$lo_hoja->write($li_det, 5, "Nombres",$lo_titulo);
			$lo_hoja->write($li_det, 8, "Apellidos",$lo_titulo);
			$lo_hoja->write($li_det2, 0, "Teléfono Hab.",$lo_titulo);
			$lo_hoja->write($li_det2, 5, "Teléfono Mov.",$lo_titulo);
			$lo_hoja->write($li_det2, 8, "Email",$lo_titulo);
			$lo_hoja->write($li_det3, 0, "Dirección",$lo_titulo);
			
			$ls_codper=$io_report->DS->data["codper"][$li_i];
			$ls_cedper=$io_report->DS->data["cedper"][$li_i];
			$ls_nomper=$io_report->DS->data["nomper"][$li_i];
			$ls_apeper=$io_report->DS->data["apeper"][$li_i];
			$ls_estper=$io_report->DS->data["estper"][$li_i];
			$ls_dirper=$io_report->DS->data["dirper"][$li_i];
			$ls_telhabper=$io_report->DS->data["telhabper"][$li_i];
			$ls_telmovper=$io_report->DS->data["telmovper"][$li_i];
			$ls_coreleper=$io_report->DS->data["coreleper"][$li_i];
			switch ($ls_estper)
			{
				case "0":
					$ls_estper="Pre-Ingreso";
					break;
				case "1":
					$ls_estper="Activo";
					break;
				case "2":
					$ls_estper="N/A";
					break;
				case "3":
					$ls_estper="Egresado";
					break;
			}
				$li_row=$li_row+1;
				$lo_hoja->write($li_det, 1, $ls_cedper, $lo_datacenter);
				$lo_hoja->write($li_det, 6, $ls_nomper, $lo_datacenter);
				$lo_hoja->write($li_det, 9, $ls_apeper, $lo_datacenter);
				$lo_hoja->write($li_det2, 1, $ls_telhabper, $lo_datacenter);
				$lo_hoja->write($li_det2, 6, $ls_telmovper, $lo_datacenter);
				$lo_hoja->write($li_det2, 9, $ls_coreleper, $lo_datacenter);
				$lo_hoja->write($li_det3, 1, $ls_dirper, $lo_datacenter);
				$li_det=$li_det+20;
				$li_det2=$li_det2+20;
				$li_det3=$li_det3+20;
			$lb_valido=$io_report->uf_permisospersonal_permiso($ls_codper,$ls_fec_des,$ls_fec_has,$ls_tipo_permiso); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_total=$io_report->DS_detalle->getRowCount("numper");
				$lo_hoja->write($li_det4, 1, "Nro",$lo_titulo);
				$lo_hoja->write($li_det4, 2, "Inicio",$lo_titulo);
				$lo_hoja->write($li_det4, 3, "Fin",$lo_titulo);
				$lo_hoja->write($li_det4, 4, "Nro de Dias",$lo_titulo);
				$lo_hoja->write($li_det4, 5, "Nro de Horas",$lo_titulo);
				$lo_hoja->write($li_det4, 6, "Afecta Vacaciones",$lo_titulo);
				$lo_hoja->write($li_det4, 7, "Remunerado",$lo_titulo);
				$lo_hoja->write($li_det4, 8, "Tipo",$lo_titulo);
				$lo_hoja->write($li_det4, 9, "Observación",$lo_titulo);
				for($li_j=1;(($li_j<=$li_total)&&($lb_valido));$li_j++)
				{
					$li_numper=$io_report->DS_detalle->data["numper"][$li_j];
					$li_horper=$io_report->DS_detalle->data["tothorper"][$li_j];
					$ld_feciniper=$io_funciones->uf_convertirfecmostrar($io_report->DS_detalle->data["feciniper"][$li_j]);
					$ld_fecfinper=$io_funciones->uf_convertirfecmostrar($io_report->DS_detalle->data["fecfinper"][$li_j]);
					$li_numdiaper=$io_report->DS_detalle->data["numdiaper"][$li_j];
					$ls_afevacper=$io_report->DS_detalle->data["afevacper"][$li_j];
					switch($ls_afevacper)
					{
						case "1":
							$ls_afevacper="NO";
							break;
						
						default:
							$ls_afevacper="SI";
							break;
					}
					$ls_remper=$io_report->DS_detalle->data["remper"][$li_j];
					switch($ls_remper)
					{
						case "1":
							$ls_remper="SI";
							break;
						
						default:
							$ls_remper="NO";
							break;
					}
					$ls_tipper=$io_report->DS_detalle->data["tipper"][$li_j];
					switch($ls_tipper)
					{
						case "1":
							$ls_tipper="Estudio";
							break;
						
						case "2":
							$ls_tipper="Médico";
							break;
						case "3":
							$ls_tipper="Tramites";
							break;

						case "4":
							$ls_tipper="Otro";
							break;
						
						default:
							$ls_tipper="";
							break;
					}
					$ls_obsper=rtrim($io_report->DS_detalle->data["obsper"][$li_j]);
					$li_det4=$li_det4+1;
					$lo_hoja->write($li_det4, 1, $li_numper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 2, $ld_feciniper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 3, $ld_fecfinper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 4, $li_numdiaper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 5, $li_horper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 6, $ls_afevacper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 7, $ls_remper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 8, $ls_tipper,$lo_datacenterdet);
					$lo_hoja->write($li_det4, 9, $ls_obsper,$lo_datacenterdet);
					
				}
				$li_det4=$li_det4+20;
				$io_report->DS_detalle->resetds("numper");
			}
				
		}
		$io_report->DS->resetds("codper");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_permisos.xls\"");
		header("Content-Disposition: inline; filename=\"listado_permisos.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 