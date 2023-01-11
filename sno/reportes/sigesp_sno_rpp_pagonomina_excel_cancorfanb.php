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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 30/08/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Gener� el Reporte ".$as_titulo." en Excel. Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_pagonomina.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hpagonomina.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "pagonomina.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
	{
		require_once("sigesp_sno_class_report.php");
		$io_report=new sigesp_sno_class_report();
		$li_tipo=1;
		$ls_tabla="sno_salida";
	}
	if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
	{
		require_once("sigesp_sno_class_report_historico.php");
		$io_report=new sigesp_sno_class_report_historico();
		$li_tipo=2;
		$ls_tabla="sno_thsalida";
	}	
	$ls_bolivares ="Bs.";
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="Reporte General de Pago";
	$ls_periodo="Per�odo Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_codubifis=$io_fun_nomina->uf_obtenervalor_get("codubifis","");
	$ls_codpai=$io_fun_nomina->uf_obtenervalor_get("codpai","");
	$ls_codest=$io_fun_nomina->uf_obtenervalor_get("codest","");
	$ls_codmun=$io_fun_nomina->uf_obtenervalor_get("codmun","");
	$ls_codpar=$io_fun_nomina->uf_obtenervalor_get("codpar","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	$ls_pagobanco=$io_fun_nomina->uf_obtenervalor_get("pagobanco","");
	$ls_pagocheque=$io_fun_nomina->uf_obtenervalor_get("pagocheque","");		
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_pagonomina_personal($ls_codperdes,$ls_codperhas,$ls_conceptocero,$ls_conceptoreporte,$ls_conceptop2,
													  $ls_codubifis,$ls_codpai,$ls_codest,$ls_codmun,$ls_codpar,$ls_subnomdes,$ls_subnomhas,$ls_orden,
													  $ls_pagobanco,$ls_pagocheque); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false)||($io_report->rs_data->RecordCount()==0)) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$io_fun_nomina->uf_loadmodalidad(&$li_len1,&$li_len2,&$li_len3,&$li_len4,&$li_len5,&$ls_titulo);
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
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,15);
		$lo_hoja->set_column(1,1,50);
		$lo_hoja->set_column(2,2,15);
		$lo_hoja->set_column(3,3,15);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,25);
		$lo_hoja->set_column(8,8,40);
		$lo_hoja->set_column(9,9,20);
		$lo_hoja->set_column(10,10,20);
		$lo_hoja->set_column(11,11,20);
		$lo_hoja->set_column(12,12,15);
		$lo_hoja->set_column(13,13,15);
		$lo_hoja->set_column(14,14,15);
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,3,$ls_periodo,$lo_encabezado);
		$lo_hoja->write(2,3,$ls_desnom,$lo_encabezado);
		$lo_hoja->write(4, 0, "C�dula",$lo_titulo);
		$lo_hoja->write(4, 1, "Apellidos y Nombres",$lo_titulo);
		$lo_hoja->write(4, 2, "Cargo",$lo_titulo);
		$lo_hoja->write(4, 3, "Fecha Ingreso",$lo_titulo);
		$lo_hoja->write(4, 4, "Salario",$lo_titulo);
		$li_col=5;
		// Buscamos los conceptos de asignaci�n
		$lb_valido=$io_report->uf_pagonomina_concepto_excel($ls_tituloconcepto," AND (sigcon='A' OR sigcon='B' OR sigcon='X' OR sigcon='I')"); // Obtenemos el detalle del reporte
		if($lb_valido)
		{
			$lo_hoja->write(3, $li_col+1, "ASIGNACIONES",$lo_titulo);
			while(!$io_report->rs_data_detalle->EOF)
			{
				$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
				$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,20);
				$lo_hoja->write(4, $li_col, $ls_nomcon,$lo_titulo);
				$io_report->DS_detalle2->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle2->insertRow("columna",$li_col);
				$io_report->rs_data_detalle->MoveNext();
			}
		}
		// Buscamos los conceptos de Deducci�n
		$lb_valido=$io_report->uf_pagonomina_concepto_excel($ls_tituloconcepto," AND (sigcon='D' OR sigcon='E')"); // Obtenemos el detalle del reporte
		if($lb_valido)
		{
			$lo_hoja->write(3, $li_col+1, "DEDUCCIONES",$lo_titulo);
			while(!$io_report->rs_data_detalle->EOF)
			{
				$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
				$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,20);
				$lo_hoja->write(4, $li_col, $ls_nomcon,$lo_titulo);
				$io_report->DS_detalle2->insertRow("codconc",$ls_codconc);
				$io_report->DS_detalle2->insertRow("columna",$li_col);
				$io_report->rs_data_detalle->MoveNext();
			}
		}
		// Buscamos los conceptos de Aporte Patronal
		$lb_valido=$io_report->uf_pagonomina_concepto_excel($ls_tituloconcepto," AND sigcon='P'"); // Obtenemos el detalle del reporte
		if($lb_valido)
		{
			while(!$io_report->rs_data_detalle->EOF)
			{
				$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
				$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,20);
				$lo_hoja->write(4, $li_col, $ls_nomcon,$lo_titulo);
				$io_report->DS_detalle2->insertRow("codconc",$ls_codconc."P1");
				$io_report->DS_detalle2->insertRow("columna",$li_col);
				$io_report->rs_data_detalle->MoveNext();
			}
			$lo_hoja->write(3, $li_col+2, "APORTE PATRON",$lo_titulo);
			$io_report->rs_data_detalle->MoveFirst();
			while(!$io_report->rs_data_detalle->EOF)
			{
				$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
				$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
				$li_col++;
				$lo_hoja->set_column($li_col,$li_col,20);
				$lo_hoja->write(4, $li_col, $ls_nomcon,$lo_titulo);
				$io_report->DS_detalle2->insertRow("codconc",$ls_codconc."P2");
				$io_report->DS_detalle2->insertRow("columna",$li_col);
				$io_report->rs_data_detalle->MoveNext();
			}
		}
		$li_final=$li_col;
		$li_row=4;
		$li_totasi=0;
		$li_totded=0;
		$li_totapo=0;
		$li_totgeneral=0;
		$check=0;
		$check2=0;
		$li_total_filas=$io_report->rs_data->RecordCount();
		while(!$io_report->rs_data->EOF)
		{
			$li_totalasignacion=0;
			$li_totaldeduccion=0;
			$li_totalaporte=0;
			$li_total_neto=0;
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_apenomper=$io_report->rs_data->fields["apeper"].", ". $io_report->rs_data->fields["nomper"];
			$ls_descar=$io_report->rs_data->fields["descar"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$ld_fecingnom=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingnom"]);
			$ld_feculcont=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecculcontr"]);
			if ($ld_feculcont!='01/01/1900')
			{
				$ld_feculcont=$ld_feculcont;
			}
			else
			{
				$ld_feculcont="";
			}
			$ls_codcueban=$io_report->rs_data->fields["codcueban"];
			$ld_sueper=number_format($io_report->rs_data->fields["sueper"],2,",",".");
			$ls_coduniadm=$io_report->rs_data->fields["minorguniadm"]."-".$io_report->rs_data->fields["ofiuniadm"]."-".
						  $io_report->rs_data->fields["uniuniadm"]."-".$io_report->rs_data->fields["depuniadm"]."-".
						  $io_report->rs_data->fields["prouniadm"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			switch($ls_nacper)
			{
				case "V":
					$ls_nacper="Venezolano";
					break;
				case "E":
					$ls_nacper="Extranjero";
					break;
			}
			$ls_desubifis=$io_report->rs_data->fields["desubifis"];
			$ls_desest=$io_report->rs_data->fields["desest"];
			$ls_denmun=$io_report->rs_data->fields["denmun"];
			$ls_denpar=$io_report->rs_data->fields["denpar"];
			$ls_programatica="";
			$ls_pro=$io_report->rs_data->fields["codestpro1"].$io_report->rs_data->fields["codestpro2"].$io_report->rs_data->fields["codestpro3"].$io_report->rs_data->fields["codestpro4"].$io_report->rs_data->fields["codestpro5"];
			$io_fun_nomina->uf_formatoprogramatica($ls_pro,&$ls_programatica);
			$li_row=$li_row+1;
			$lo_hoja->write($li_row, 0, $ls_cedper, $lo_datacenter);
			$lo_hoja->write($li_row, 1, $ls_apenomper, $lo_dataleft);
			$lo_hoja->write($li_row, 2, $ls_descar, $lo_dataleft);
			$lo_hoja->write($li_row, 3, $ld_fecingper, $lo_datacenter);
			$lo_hoja->write($li_row, 4, $ld_sueper, $lo_dataright);
			$li_col=5;
			// Buscamos las Asignaciones
			$lb_valido=$io_report->uf_pagonomina_conceptopersonal_excel($ls_codper,$ls_tituloconcepto," AND (".$ls_tabla.".tipsal='A' OR ".$ls_tabla.".tipsal='V1' OR ".$ls_tabla.".tipsal='W1')"); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				while(!$io_report->rs_data_detalle->EOF)
				{
					$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
					$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
					$li_valsal=abs($io_report->rs_data_detalle->fields["valsal"]);
					$li_totalasignacion=$li_totalasignacion + $li_valsal;
					$li_find=$io_report->DS_detalle2->find("codconc",$ls_codconc);
					$li_col=$io_report->DS_detalle2->getValue("columna",$li_find);
					$lo_hoja->write($li_row, $li_col, $li_valsal, $lo_dataright);
					$io_report->rs_data_detalle->MoveNext();
				}
			}
			// Buscamos las Deducciones
			$lb_valido=$io_report->uf_pagonomina_conceptopersonal_excel($ls_codper,$ls_tituloconcepto,"AND (".$ls_tabla.".tipsal='D' OR ".$ls_tabla.".tipsal='V2' OR ".$ls_tabla.".tipsal='W2')"); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				while(!$io_report->rs_data_detalle->EOF)
				{
					$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
					$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
					$li_valsal=abs($io_report->rs_data_detalle->fields["valsal"]);
					$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
					$li_find=$io_report->DS_detalle2->find("codconc",$ls_codconc);
					$li_col=$io_report->DS_detalle2->getValue("columna",$li_find);
					$lo_hoja->write($li_row, $li_col, $li_valsal, $lo_dataright);
					$io_report->rs_data_detalle->MoveNext();
				}
			}
			// Buscamos los Aportes Empleado
			$lb_valido=$io_report->uf_pagonomina_conceptopersonal_excel($ls_codper,$ls_tituloconcepto,"AND (".$ls_tabla.".tipsal='P1' OR ".$ls_tabla.".tipsal='V3' OR ".$ls_tabla.".tipsal='W3')"); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				while(!$io_report->rs_data_detalle->EOF)
				{
					$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
					$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
					$li_valsal=abs($io_report->rs_data_detalle->fields["valsal"]);
					$li_totaldeduccion=$li_totaldeduccion + $li_valsal;
					$li_find=$io_report->DS_detalle2->find("codconc",$ls_codconc."P1");
					$li_col=$io_report->DS_detalle2->getValue("columna",$li_find);
					$lo_hoja->write($li_row, $li_col, $li_valsal, $lo_dataright);
					$io_report->rs_data_detalle->MoveNext();
				}
			}
			// Buscamos los Aportes Patron
			$lb_valido=$io_report->uf_pagonomina_conceptopersonal_excel($ls_codper,$ls_tituloconcepto,"AND (".$ls_tabla.".tipsal='P2' OR ".$ls_tabla.".tipsal='V4' OR ".$ls_tabla.".tipsal='W4')"); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				while(!$io_report->rs_data_detalle->EOF)
				{
					$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
					$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
					$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
					$li_valsal=abs($io_report->rs_data_detalle->fields["valsal"]);
					$li_totalaporte=$li_totalaporte + $li_valsal;
					$li_find=$io_report->DS_detalle2->find("codconc",$ls_codconc."P2");
					$li_col=$io_report->DS_detalle2->getValue("columna",$li_find);
					$lo_hoja->write($li_row, $li_col, $li_valsal, $lo_dataright);
					$io_report->rs_data_detalle->MoveNext();
				}
			}
			$li_total_neto=($li_totalasignacion-$li_totaldeduccion);
			if ($check==0)
			{
				$li_colf=$li_final+2;
				$lo_hoja->write(3, $li_colf+1, "TOTALES",$lo_titulo);
				$lo_hoja->set_column($li_colf,$li_colf,20);
				$li_colf++;
				$lo_hoja->write(4, $li_colf, "TOTAL ASIGNACI�N",$lo_titulo);
				$li_colasi=$li_colf;
				$li_colf++;
				$lo_hoja->set_column($li_colf,$li_colf,20);
				$lo_hoja->write(4, $li_colf, "TOTAL DEDUCCI�N",$lo_titulo);
				$li_coldedu=$li_colf;
				$li_colf++;
				$lo_hoja->set_column($li_colf,$li_colf,20);
				$lo_hoja->write(4, $li_colf, "TOTAL APORTE PATRON",$lo_titulo);
				$li_colapo=$li_colf;
				$li_colf++;
				$lo_hoja->set_column($li_colf,$li_colf,20);
				$lo_hoja->write(4, $li_colf, "NETO A COBRAR",$lo_titulo);
				$li_colnet=$li_colf;
				$li_colf++;
				$check++;
			}
			$lo_hoja->write($li_row, $li_colasi, $li_totalasignacion, $lo_dataright);
			$lo_hoja->write($li_row, $li_coldedu, $li_totaldeduccion, $lo_dataright);
			$lo_hoja->write($li_row, $li_colapo, $li_totalaporte, $lo_dataright);
			$lo_hoja->write($li_row, $li_colnet, $li_total_neto, $lo_dataright);
			$io_report->rs_data->MoveNext();
		}
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		

		$border1 =&$lo_libro->addformat();
		$border1->set_text_wrap();
		$border1->set_bold();
		$border1->set_font("Verdana");
		$border1->set_align('center');
		$border1->set_size('9');		
		$border1->set_align('vcenter');
		$border1->set_merge(); # This is the key feature
		
		# Create another border format. Note you could use copy() here.
		$border2 =&$lo_libro->addformat();
		$border2->set_text_wrap();
		$border2->set_bold();
		$border2->set_font("Verdana");
		$border2->set_align('center');
		$border2->set_size('9');		
		$border2->set_merge(); # This is the key feature
		
		
		$li_row=$li_row+3;
		$li_row++;
		$lo_hoja->write($li_row, 1, "CAP. ISRAEL GUZMAN LOZADA",$border1);
		$lo_hoja->write_blank($li_row, 2,                 $border2);
		$lo_hoja->write($li_row, 5, "TCNEL LUISARVELIO RAMIREZ GARCIA",$border1);
		$lo_hoja->write_blank($li_row, 6,                 $border2);
		$lo_hoja->write($li_row, 8, "M/G JUAN DE JESUS GARCIA TOUSSAINTT",$border1);
		$lo_hoja->write_blank($li_row, 9,                 $border2);
		$li_row++;
		$lo_hoja->write($li_row, 1, "JEFE DE LA GERENCIA DE GESTION HUMANA",$border1);
		$lo_hoja->write_blank($li_row, 2,                 $border2);
		$lo_hoja->write($li_row, 5, "ADMINISTRADOR",$border1);
		$lo_hoja->write_blank($li_row, 6,                 $border2);
		$lo_hoja->write($li_row, 8, "PRESIDENTE  DE CANCORFANB S.A.",$border1);
		$lo_hoja->write_blank($li_row, 9,                 $border2);
		
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"pagonomina.xls\"");
		header("Content-Disposition: inline; filename=\"pagonomina.xls\"");
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