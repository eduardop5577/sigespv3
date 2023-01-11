<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
		print "</script>";		
	}

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "Activos.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");
	$ld_fecha="";
	if(($ld_desde!="")&&($ld_hasta!=""))
	{
		$ld_fecha="Compra Desde:".$ld_desde."  Hasta:".$ld_hasta."";
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$cadena='';	
	$ls_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	if(!empty($ls_coddesde))
	{
		$cadena .= 'Activo Desde: '.$ls_coddesde;
	}
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	if(!empty($ls_codhasta))
	{
		$cadena .= ' Hasta: '.$ls_codhasta;
	}
	$ls_status=$io_fun_activos->uf_obtenervalor_get("status","");
	switch($ls_status)
	{
		case 1:
			$cadena .=' Registrados ';
		break;
		case 2:
			$cadena .=' Incorporado ';
		break;
		case 3:
			$cadena .=' Reasignado ';
		break;
		case 4:
			$cadena .=' Modificados ';
		break;
		case 5:
			$cadena .=' Contabilizado ';
		break;
		case 6:
			$cadena .=' Desincorporado ';
		break;
	}
	$ls_codrespri=$io_fun_activos->uf_obtenervalor_get("codrespri","");
	if(!empty($ls_codrespri))
	{
		$cadena .= ' Responsable Primario: '.$ls_codrespri;
	}
	$ls_codresuso=$io_fun_activos->uf_obtenervalor_get("codresuso","");
	if(!empty($ls_codresuso))
	{
		$cadena .= ' Responsable de Uso: '.$ls_codresuso;
	}
	$ls_coduniadm=$io_fun_activos->uf_obtenervalor_get("coduni","");
	if(!empty($ls_coduniadm))
	{
		$cadena .= ' Unidad Física: '.$ls_coduniadm;
	}
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	if(!empty($ls_grupo))
	{
		$cadena .= ' Grupo Desde: '.$ls_grupo;
	}
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	if(!empty($ls_grupohas))
	{
		$cadena .= ' Grupo Hasta: '.$ls_grupohas;
	}
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	if(!empty($ls_subgrupo))
	{
		$cadena .= ' SubGrupo Desde: '.$ls_subgrupo;
	}
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	if(!empty($ls_subgrupohas))
	{
		$cadena .= ' SubGrupo Hasta: '.$ls_subgrupohas;
	}
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	if(!empty($ls_seccion))
	{
		$cadena .= ' Seccion Desde: '.$ls_seccion;
	}
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	if(!empty($ls_seccionhas))
	{
		$cadena .= ' Seccion Hasta: '.$ls_seccionhas;
	}
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	if(!empty($ls_unitri))
	{
		$cadena .= ' Unidad Tributaria > 14 (Pub 21)';
	}

	$ls_titulo="<b>Listado de Activos</b> ".$cadena."";
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
	}	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_saf_load_activos($ls_codemp,$ls_ordenact,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_status,
											   $ls_codrespri,$ls_codresuso,$ls_coduniadm,$ls_grupo,$ls_subgrupo,$ls_seccion,
											   $ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Activo. Desde el activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_activo.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
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
		$lo_hoja->set_column(0,0,50);
		$lo_hoja->set_column(1,2,25);	
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,60);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,20);	
		$lo_hoja->set_column(7,7,20);	
		$lo_hoja->set_column(8,8,15);	
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		set_time_limit(1800);
		$li_totrow=$io_report->ds->getRowCount("codact");
		$i=0;
		$li_row=0;
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$ls_codact=$io_report->ds->data["codact"][$li_i];
			$ls_denact=$io_report->ds->data["denact"][$li_i];
			$ls_maract=$io_report->ds->data["maract"][$li_i];
			$ls_modact=$io_report->ds->data["modact"][$li_i];
			$ld_fecmpact=$io_report->ds->data["feccmpact"][$li_i];
			$li_modificacion=$io_report->uf_saf_load_montomodificacion($ls_codemp,$ls_codact,"");
			$ld_fecmpactaux=$io_funciones->uf_convertirfecmostrar($ld_fecmpact);
			$li_costo=$io_report->ds->data["costo"][$li_i];
			$li_costo=$li_costo+$li_modificacion;
			$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
			$li_row++;
			$lo_hoja->write($li_row,0,"Organismo:",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_nomemp,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"Activo:",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_denact,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"Marca:",$lo_titulo);
			$lo_hoja->write($li_row,1,$ls_maract,$lo_dataleft);
			$lo_hoja->write($li_row,2,"Modelo:",$lo_titulo);
			$lo_hoja->write($li_row,3,$ls_modact,$lo_dataleft);
			$li_row++;
			$lo_hoja->write($li_row,0,"Fecha de Compra:",$lo_titulo);
			$lo_hoja->write($li_row,1,$ld_fecmpactaux,$lo_dataleft);
			$lo_hoja->write($li_row,2,"Costo:",$lo_titulo);
			$lo_hoja->write($li_row,3,$li_costo,$lo_dataleft);


			$li_row++;
			$lo_hoja->write($li_row,0,"Serial",$lo_titulo);
			$lo_hoja->write($li_row,1,"Identificador",$lo_titulo);
			$lo_hoja->write($li_row,2,"Chapa",$lo_titulo);
			$lo_hoja->write($li_row,3,"Responsable Primario",$lo_titulo);
			$lo_hoja->write($li_row,4,"Responsable por Uso",$lo_titulo);
			$lo_hoja->write($li_row,5,"Unidad Administrativa",$lo_titulo);
			$lo_hoja->write($li_row,6,"Incorporacion",$lo_titulo);
			$lo_hoja->write($li_row,7,"Desincorporacion",$lo_titulo);
			$lo_hoja->write($li_row,8,"Estatus",$lo_titulo);
			$lb_valido=$io_report->uf_saf_select_dt_activo($ls_codemp,$ls_codact,$ls_status,$ls_codrespri,$ls_codresuso,$ls_coduniadm); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_montot=0;
				$li_totrow_det=$io_report->ds_detalle->getRowCount("ideact");
				$la_data="";
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_seract=    $io_report->ds_detalle->data["seract"][$li_s];
					$ls_ideact=    $io_report->ds_detalle->data["ideact"][$li_s];
					$ls_chapa=     $io_report->ds_detalle->data["idchapa"][$li_s];
					$ls_nomrespri= $io_report->ds_detalle->data["nomrespri"][$li_s]." ".$io_report->ds_detalle->data["aperespri"][$li_s];
					$ls_nomresuso= $io_report->ds_detalle->data["nomres"][$li_s]." ".$io_report->ds_detalle->data["aperes"][$li_s];
					$ls_denuniadm= $io_report->ds_detalle->data["denuniadm"][$li_s];
					$ld_fecincact= $io_report->ds_detalle->data["fecincact"][$li_s];
					$ld_fecdesact= $io_report->ds_detalle->data["fecdesact"][$li_s];
					$ls_estact=    $io_report->ds_detalle->data["estact"][$li_s];
					$ld_fecincact=$io_funciones->uf_convertirfecmostrar($ld_fecincact);
					$ld_fecincact=$io_funciones->uf_convertirfecmostrar($ld_fecincact);
					$ld_fecdesact=$io_funciones->uf_convertirfecmostrar($ld_fecdesact);
					if($ls_estact=="R"){$ls_estact="Registrado";}
					if($ls_estact=="I"){$ls_estact="Incorporado";}
					if($ls_estact=="M"){$ls_estact="Modificado";}
					if($ls_estact=="D"){$ls_estact="Desincorporado";}
					if($ls_estact=="C"){$ls_estact="Contabilizado";}
					if($ls_seract!="")
					{
						$li_row++;
						$lo_hoja->write($li_row,0," ".$ls_seract,$lo_dataleft);
						$lo_hoja->write($li_row,1," ".$ls_ideact,$lo_dataleft);
						$lo_hoja->write($li_row,2," ".$ls_chapa,$lo_dataleft);
						$lo_hoja->write($li_row,3,$ls_nomrespri,$lo_dataleft);
						$lo_hoja->write($li_row,4,$ls_nomresuso,$lo_dataleft);
						$lo_hoja->write($li_row,5,$ls_denuniadm,$lo_dataleft);
						$lo_hoja->write($li_row,6,$ld_fecincact,$lo_dataleft);
						$lo_hoja->write($li_row,7,$ld_fecdesact,$lo_dataleft);
						$lo_hoja->write($li_row,8,$ls_estact,$lo_dataleft);
					}
				}
				$li_montot=$io_fun_activos->uf_formatonumerico($li_montot);
			}
			$li_row=$li_row+2;
		}
		if(($lb_valido))
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"Activos.xls\"");
			header("Content-Disposition: inline; filename=\"Activos.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);		
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($io_pdf);
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 