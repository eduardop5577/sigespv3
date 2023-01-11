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
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_familiar.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_familiares.xls");
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
	$ls_titulo="Reporte de Familiares";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_conyuge=$io_fun_nomina->uf_obtenervalor_get("conyuge","");
	$ls_progenitor=$io_fun_nomina->uf_obtenervalor_get("progenitor","");
	$ls_hijo=$io_fun_nomina->uf_obtenervalor_get("hijo","");
	$ls_hermano=$io_fun_nomina->uf_obtenervalor_get("hermano","");
	$ls_masculino=$io_fun_nomina->uf_obtenervalor_get("masculino","");
	$ls_femenino=$io_fun_nomina->uf_obtenervalor_get("femenino","");
	$li_edaddesde=$io_fun_nomina->uf_obtenervalor_get("edaddesde","");
	$li_edadhasta=$io_fun_nomina->uf_obtenervalor_get("edadhasta","");
	$ls_activo=$io_fun_nomina->uf_obtenervalor_get("activo","");
	$ls_egresado=$io_fun_nomina->uf_obtenervalor_get("egresado","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_activono=$io_fun_nomina->uf_obtenervalor_get("activono","");
	$ls_vacacionesno=$io_fun_nomina->uf_obtenervalor_get("vacacionesno","");
	$ls_suspendidono=$io_fun_nomina->uf_obtenervalor_get("suspendidono","");
	$ls_egresadono=$io_fun_nomina->uf_obtenervalor_get("egresadono","");
	$ls_personalmasculino=$io_fun_nomina->uf_obtenervalor_get("personalmasculino","");
	$ls_personalfemenino=$io_fun_nomina->uf_obtenervalor_get("personalfemenino","");
	$ls_beca=$io_fun_nomina->uf_obtenervalor_get("beca","");
	$ls_hcm=$io_fun_nomina->uf_obtenervalor_get("hcm","");
	$ls_nivaca=$io_fun_nomina->uf_obtenervalor_get("nivaca","");
	$ls_juguete=$io_fun_nomina->uf_obtenervalor_get("juguete","");
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Reporte de Familiares en Excel</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_familiar_personal($ls_codperdes,$ls_codperhas,$ls_conyuge,$ls_progenitor,$ls_hijo,
															$ls_hermano,$ls_masculino,$ls_femenino,$li_edaddesde,$li_edadhasta,
															$ls_codnomdes,$ls_codnomhas,$ls_activo,$ls_egresado,$ls_activono,
															$ls_vacacionesno,$ls_suspendidono,$ls_egresadono,$ls_personalmasculino,
															$ls_personalfemenino,$ls_beca,$ls_nivaca,$ls_juguete,$ls_hcm,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte	}
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
		$lo_hoja->set_column(1,1,60);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,20);
		$lo_hoja->set_column(5,5,10);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,20);
		$lo_hoja->set_column(8,8,50);
		$lo_hoja->set_column(9,9,50);
		$lo_hoja->write(0,2,$ls_titulo,$lo_encabezado);
		$li_encabezado=3;
		$li_totrow=$io_report->DS->getRowCount("codper");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codper=$io_report->DS->data["codper"][$li_i];
			$ls_apenomper=$io_report->DS->data["apeper"][$li_i].", ". $io_report->DS->data["nomper"][$li_i];
			$ld_fecnacper=$io_report->DS->data["fecnacper"][$li_i];
			$ld_hoy=date('Y');
			$ld_fecha=substr($ld_fecnacper,0,4);
			$li_edad=$ld_hoy-$ld_fecha;					
			if(intval(date('m'))<intval(substr($ld_fecnacper,5,2)))
			{
				$li_edad=$li_edad-1;
			}
			else
			{
				if(intval(date('m'))==intval(substr($ld_fecnacper,5,2)))
				{
					if(intval(date('d'))<intval(substr($ld_fecnacper,8,2)))
					{
						$li_edad=$li_edad-1;
					}
				}
			}
			$ld_fecnacper=$io_funciones->uf_convertirfecmostrar($ld_fecnacper);
			//Datos de la Cabecera
			$lo_hoja->write($li_encabezado,0, "Personal: ",$lo_titulo);
			$lo_hoja->write($li_encabezado,1," ".$ls_codper."-".$ls_apenomper, $lo_datacenter);
			$lo_hoja->write($li_encabezado,3,"Fecha de Nac. ",$lo_titulo);
			$lo_hoja->write($li_encabezado,4," ".$ld_fecnacper, $lo_datacenter);
			$lo_hoja->write($li_encabezado,6,"Edad ",$lo_titulo);
			$lo_hoja->write($li_encabezado,7," ".$li_edad, $lo_datacenter);
			//Datos de la Cabecera
			$lb_valido=$io_report->uf_familiar_familiar($ls_codper,$ls_conyuge,$ls_progenitor,$ls_hijo,$ls_hermano,$ls_masculino,$ls_femenino,$li_edaddesde,$li_edadhasta,$ls_beca,$ls_nivaca,$ls_juguete); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_detalle=$li_encabezado+1;
				$li_totrow_res=$io_report->DS_detalle->getRowCount("cedfam");
				$lo_hoja->write($li_detalle,0, "Cédula ",$lo_titulo);
				$lo_hoja->write($li_detalle,1, "Apellidos y Nombres ",$lo_titulo);
				$lo_hoja->write($li_detalle,2, "Género ",$lo_titulo);
				$lo_hoja->write($li_detalle,3, "Nexo ",$lo_titulo);
				$lo_hoja->write($li_detalle,4, "Fecha de Nacimiento ",$lo_titulo);
				$lo_hoja->write($li_detalle,5, "Edad ",$lo_titulo);
				$lo_hoja->write($li_detalle,6, "Becado ",$lo_titulo);
				$lo_hoja->write($li_detalle,7, "Nivel Academico ",$lo_titulo);
				for($li_s=1;$li_s<=$li_totrow_res;$li_s++)
				{	
					$li_detalle=$li_detalle+1;
					$ls_cedfam=$io_report->DS_detalle->data["cedfam"][$li_s];
					$ls_cedula=trim($io_report->DS_detalle->data["cedula"][$li_s]);
					if ($ls_cedula!='')
					{
						$ls_cedfam=$ls_cedula;
					}
					$ls_apenomfam=$io_report->DS_detalle->data["apefam"][$li_s].", ". $io_report->DS_detalle->data["nomfam"][$li_s];
					$ls_sexfam=$io_report->DS_detalle->data["sexfam"][$li_s];
					switch($ls_sexfam)
					{
						case "M":
							$ls_sexfam="Masculino";
							break;
						case "F":
							$ls_sexfam="Femenino";
							break;
					}
					$ls_nexfam=$io_report->DS_detalle->data["nexfam"][$li_s];
					switch($ls_nexfam)
					{
						case "C":
							$ls_nexfam="Conyuge";
							break;
						case "H":
							$ls_nexfam="Hijo";
							break;
						case "P":
							$ls_nexfam="Progenitor";
							break;
						case "E":
							$ls_nexfam="Hermano";
							break;
					}
					$ld_fecnacfam=$io_report->DS_detalle->data["fecnacfam"][$li_s];
					$ld_hoy=date('Y');
					$ld_fecha=substr($ld_fecnacfam,0,4);
					$li_edad=$ld_hoy-$ld_fecha;					
					if(intval(date('m'))<intval(substr($ld_fecnacfam,5,2)))
					{
						$li_edad=$li_edad-1;
					}
					else
					{
						if(intval(date('m'))==intval(substr($ld_fecnacfam,5,2)))
						{
							if(intval(date('d'))<intval(substr($ld_fecnacfam,8,2)))
							{
								$li_edad=$li_edad-1;
							}
						}
					}
					$ld_fecnacfam=$io_funciones->uf_convertirfecmostrar($ld_fecnacfam);
					$ls_estbec=$io_report->DS_detalle->data["estbec"][$li_s];
					if ($ls_estbec=='1')
					{
						$ls_estbec="Si";
					}
					else
					{
						$ls_estbec="No";
					}
					$ls_nivaca=$io_report->DS_detalle->data["nivaca"][$li_s];
					if($ls_nivaca=='P')
					{
						$ls_nivaca="Primaria";
					}
					if($ls_nivaca=='D')
					{
						$ls_nivaca="Diversificada";
					}
					if($ls_nivaca=='U')
					{
						$ls_nivaca="Universitaria";
					}
					//Datos del Detalle
					$lo_hoja->write($li_detalle,0," ".$ls_cedfam, $lo_datacenter);
					$lo_hoja->write($li_detalle,1," ".$ls_apenomfam, $lo_datacenter);
					$lo_hoja->write($li_detalle,2," ".$ls_sexfam, $lo_datacenter);
					$lo_hoja->write($li_detalle,3," ".$ls_nexfam, $lo_datacenter);
					$lo_hoja->write($li_detalle,4," ".$ld_fecnacfam, $lo_datacenter);
					$lo_hoja->write($li_detalle,5," ".$li_edad, $lo_datacenter);
					$lo_hoja->write($li_detalle,6," ".$ls_estbec, $lo_datacenter);
					$lo_hoja->write($li_detalle,7," ".$ls_nivaca, $lo_datacenter);
					//
					$ls_nivaca ='';
				}
				$li_encabezado=$li_detalle+3;
			}
			//unset($la_data);
		}
		//$io_report->DS->resetds("codper");
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_familiares.xls\"");
		header("Content-Disposition: inline; filename=\"listado_familiares.xls\"");
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
}
?> 