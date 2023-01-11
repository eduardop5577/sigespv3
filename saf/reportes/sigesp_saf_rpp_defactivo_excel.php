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
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($li_total,$lo_libro,$lo_hoja,$la_data,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->write($li_fila, 0, 'Código',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, 'Chapa',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 2, 'Denominación',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 3, 'Responsable Uso',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 4, 'Responsable Primario',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 5, 'Unidad',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 6, 'Marca',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 7, 'Modelo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 8, 'Fecha de Compra',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 9, 'Costo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$lo_hoja->write($li_fila, 10, 'Catálogo',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_fila++;

		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, " ".$la_data[$li_j]['codact'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 1, $la_data[$li_j]['chapa'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['denact'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['resuso'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['respri'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['denuniadm'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 6, $la_data[$li_j]['maract'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 7, $la_data[$li_j]['modact'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$lo_hoja->write($li_fila, 8, $la_data[$li_j]['feccmpact'],$lo_datadate);
			$lo_hoja->write($li_fila, 9, $la_data[$li_j]['costo'],$lo_dataright);
			$lo_hoja->write($li_fila, 10, $la_data[$li_j]['catalogo'],$lo_libro->addformat(array('bold'=>0,'font'=>'Verdana','align'=>'left','size'=>'9')));
			$li_fila++;
		}
		return $li_fila;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_montot,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barrgan
		// Fecha Creación: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$li_fila++;
		$lo_hoja->write($li_fila, 5, 'TOTAL',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 6, $ai_montot,$lo_dataright);
		$li_fila++;
		return $li_fila;
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($ai_montot,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_total
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barrgan
		// Fecha Creación: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 5, 'TOTAL GENERAL',$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 6, $ai_montot,$lo_dataright);
		$li_fila++;
		return $li_fila;
	}// end function uf_print_total
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_categoria($as_codcat,$as_dencat,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: 
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barrgan
		// Fecha Creación: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_fila++;
		$lo_hoja->write($li_fila, 0, $as_codcat,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'center','size'=>'9')));
		$lo_hoja->write($li_fila, 1, $as_dencat,$lo_libro->addformat(array('bold'=>1,'font'=>'Verdana','align'=>'left','size'=>'9')));
		$li_fila++;
		return $li_fila;
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
/*	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo =  tempnam("/tmp", "definicion_activos.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();*/
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "definicion_activos.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	global $ls_tipoformato;
	require_once("sigesp_saf_class_report.php");
	$io_report=new sigesp_saf_class_report();
	$ls_titulo_report="Bs.";
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");

	$ld_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
/*	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_codresuso=$io_fun_activos->uf_obtenervalor_get("codresuso","");
	$ls_codcatsudeban=$io_fun_activos->uf_obtenervalor_get("codcatsudeban","");
	$ls_estdes=$io_fun_activos->uf_obtenervalor_get("estdes","");
	$ls_tipact=$io_fun_activos->uf_obtenervalor_get("tipact","");
*/	
	$ls_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_status=$io_fun_activos->uf_obtenervalor_get("status","");
	$ls_codrespri=$io_fun_activos->uf_obtenervalor_get("codrespri","");
	$ls_codresuso=$io_fun_activos->uf_obtenervalor_get("codresuso","");
	$ls_coduniadm=$io_fun_activos->uf_obtenervalor_get("coduni","");
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	$ls_denrespri=$io_fun_activos->uf_obtenervalor_get("denrespri","");
	$ls_denresusu=$io_fun_activos->uf_obtenervalor_get("denresusu","");
	$ls_denuni=$io_fun_activos->uf_obtenervalor_get("denuni","0");
	$ls_todos=$io_fun_activos->uf_obtenervalor_get("todos","1");
	$ls_titulo="Listado de Activos ";

	$ls_estsudeban=$io_report->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estsudeban);
	//--------------------------------------------------------------------------------------------------------------------------------
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
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,20);
	$lo_hoja->set_column(2,2,50);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,30);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,30);

	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	$li_fila=1;
	$cadena='';
	switch($ls_status)
	{
		case 1:
			$cadena=' Registrados ';
		break;
		case 2:
			$cadena=' Incorporado ';
		break;
		case 3:
			$cadena=' Reasignado ';
		break;
		case 4:
			$cadena=' Modificados ';
		break;
		case 5:
			$cadena=' Contabilizado ';
		break;
		case 6:
			$cadena=' Desincorporado ';
		break;
	}
	if (!empty($cadena))
	{
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}	
	if(!empty($ls_coddesde))
	{
		$cadena = 'Activo Desde: '.$ls_coddesde;
		if(!empty($ls_codhasta))
		{
			$cadena .= ' Hasta: '.$ls_codhasta;
		}
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_codrespri))
	{
		$cadena = ' Responsable Primario: '.$ls_denrespri;
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_codresuso))
	{
		$cadena = ' Responsable de Uso: '.$ls_denresusu;
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_coduniadm))
	{
		$cadena = ' Unidad Física: '.$ls_denuni;
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_grupo))
	{
		$cadena = ' Grupo Desde: '.$ls_grupo;
		if(!empty($ls_grupohas))
		{
			$cadena .= ' Grupo Hasta: '.$ls_grupohas;
		}
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_subgrupo))
	{
		$cadena = ' SubGrupo Desde: '.$ls_subgrupo;
		if(!empty($ls_subgrupohas))
		{
			$cadena .= ' SubGrupo Hasta: '.$ls_subgrupohas;
		}
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_seccion))
	{
		$cadena = ' Seccion Desde: '.$ls_seccion;
		if(!empty($ls_seccionhas))
		{
			$cadena .= ' Seccion Hasta: '.$ls_seccionhas;
		}
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ls_unitri))
	{
		$cadena = ' Unidad Tributaria > 14 (Pub 21)';
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	if(!empty($ld_desde))
	{
		$cadena = ' Fecha de Compra Desde: '.$ld_desde;
		if(!empty($ld_hasta))
		{
			$cadena .= ' Fecha de Compra Hasta: '.$ld_hasta;
		}	
		$lo_hoja->write($li_fila, 3, $cadena,$lo_encabezado);
		$li_fila++;
	}
	$li_fila++;
	if($ls_estsudeban!=1)
	{
		$lb_valido=$io_report->uf_saf_load_defactivos_2($ls_codemp,$ls_ordenact,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_status,
											   $ls_codrespri,$ls_codresuso,$ls_coduniadm,$ls_grupo,$ls_subgrupo,$ls_seccion,
											   $ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_unitri,$ls_todos); // Cargar el DS con los datos de la cabecera del reporte
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
			$ls_desc_event="Generó un reporte de Listado de Activo. Desde el Activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
			$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_defactivo.php",$ls_desc_event);
			////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
			$li_totrow=$io_report->ds->getRowCount("codact");
			$i=0;
			$ld_total_costo=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_codact=$io_report->ds->data["codact"][$li_i];
				$ls_denact=$io_report->ds->data["denact"][$li_i];
				$ls_nomresuso= $io_report->ds->data["nomres"][$li_i].",".$io_report->ds->data["aperes"][$li_i];
				$ls_chapa=     $io_report->ds->data["idchapa"][$li_i];
				$ls_nomrespri= $io_report->ds->data["nomrespri"][$li_i]." ".$io_report->ds->data["aperespri"][$li_i];
				$ls_denuniadm= $io_report->ds->data["denuniadm"][$li_i];
				$ls_maract=$io_report->ds->data["maract"][$li_i];
				$ls_modact=$io_report->ds->data["modact"][$li_i];
				$ls_estact=$io_report->ds->data["estact"][$li_i];
				$ls_catalogo=$io_report->ds->data["catalogo"][$li_i];
				$ld_fecmpact=$io_report->ds->data["feccmpact"][$li_i];
				$ld_fecmpactaux=$io_funciones->uf_convertirfecmostrar($ld_fecmpact);
				$li_costo=$io_report->ds->data["costo"][$li_i];
				$li_modificacion=$io_report->uf_saf_load_montomodificacion($ls_codemp,$ls_codact,"");
				$li_costo=$li_costo+$li_modificacion;
				if ($ls_estact=='D')
				   {
					 $li_costo = $li_costo*(-1);
				   }
				$ld_total_costo=$ld_total_costo+$li_costo;	
				$li_costo = number_format($li_costo,2,',','.');		
				$la_data[$li_i]=array('codact'=>$ls_codact,'chapa'=>$ls_chapa,'denact'=>$ls_denact,'resuso'=>$ls_nomresuso,'respri'=>$ls_nomrespri,'denuniadm'=>$ls_denuniadm,'maract'=>$ls_maract,
									  'modact'=>$ls_modact,'feccmpact'=>$ld_fecmpactaux,'costo'=>$li_costo,
									  'catalogo'=>$ls_catalogo);
/*				$la_data[$li_i]=array('codact'=>$ls_codact,'denact'=>$ls_denact,'resuso'=>$ls_nomresuso,'maract'=>$ls_maract,
									  'modact'=>$ls_modact,'feccmpact'=>$ld_fecmpactaux,'costo'=>$li_costo,
									  'catalogo'=>$ls_catalogo);
*/			}
			$li_fila=uf_print_detalle($li_totrow,$lo_libro,$lo_hoja,$la_data,$li_fila); // Imprimimos el detalle 
			$ld_total_costo=$io_fun_activos->uf_formatonumerico($ld_total_costo);
			$li_fila=uf_print_pie_cabecera($ld_total_costo,$lo_libro,$lo_hoja,$li_fila);
			unset($la_data);			
		}
		unset($io_report);
		unset($io_funciones);
		unset($io_fun_nomina);
	}
	else
	{
		$lb_valido=$io_report->uf_saf_load_sudeban($ls_codcatsudeban); // Cargar el DS con los datos de la cabecera del reporte
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
			$ls_desc_event="Generó un reporte de Listado de Activo. Desde el Activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
			$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_defactivo.php",$ls_desc_event);
			////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
			$li_totrow=$io_report->ds_sudeban->getRowCount("codcat");
			$i=0;
			$ld_totaltotal=0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$la_data="";
				$ld_total_costo=0;
				$ls_codcat=$io_report->ds_sudeban->data["codcat"][$li_i];
				$ls_dencat=$io_report->ds_sudeban->data["dencat"][$li_i];
				$lb_existe=$io_report->uf_saf_load_defactivos($ls_codemp,$li_ordenact,$ld_desde,$ld_hasta,$ls_coddesde,$ls_codhasta,$ls_codresuso,$ls_codcat,$ls_estdes,$ls_tipact);
				$li_totrowact=$io_report->ds->getRowCount("codact");
				for($li_j=1;$li_j<=$li_totrowact;$li_j++)
				{
					$ls_codact=$io_report->ds->data["codact"][$li_j];
					$ls_denact=$io_report->ds->data["denact"][$li_j];
					$ls_nomresuso= $io_report->ds->data["nomres"][$li_j].",".$io_report->ds->data["aperes"][$li_j];
					$ls_maract=$io_report->ds->data["maract"][$li_j];
					$ls_modact=$io_report->ds->data["modact"][$li_j];
					$ls_estact=$io_report->ds->data["estact"][$li_j];
					$ls_catalogo=$io_report->ds->data["catalogo"][$li_j];
					$ld_fecmpact=$io_report->ds->data["feccmpact"][$li_j];
					$ld_fecmpactaux=$io_funciones->uf_convertirfecmostrar($ld_fecmpact);
					$li_costo=$io_report->ds->data["costo"][$li_j];
					if ($ls_estact=='D')
					   {
						 $li_costo = $li_costo*(-1);
					   }
					$ld_total_costo=$ld_total_costo+$li_costo;	
					$li_costo = number_format($li_costo,2,',','.');		
					$la_data[$li_j]=array('codact'=>$ls_codact,'denact'=>$ls_denact,'resuso'=>$ls_nomresuso,'maract'=>$ls_maract,
										  'modact'=>$ls_modact,'feccmpact'=>$ld_fecmpactaux,'costo'=>$li_costo,
										  'catalogo'=>$ls_catalogo);
				}
				if($la_data!="")
				{
					$li_fila=uf_print_categoria($ls_codcat,$ls_dencat,$lo_libro,$lo_hoja,$li_fila);
					$ld_totaltotal=$ld_totaltotal+$ld_total_costo;
					$li_fila=uf_print_detalle($li_totrowact,$lo_libro,$lo_hoja,$la_data,$li_fila); // Imprimimos el detalle 
					$ld_total_costo=$io_fun_activos->uf_formatonumerico($ld_total_costo);
					$li_fila=uf_print_pie_cabecera($ld_total_costo,$lo_libro,$lo_hoja,$li_fila);
				}
			}
			$ld_totaltotal=$io_fun_activos->uf_formatonumerico($ld_totaltotal);
			$li_fila=uf_print_total($ld_totaltotal,$lo_libro,$lo_hoja,$li_fila);
		}
	}
		if($lb_valido)
		{
			unset($io_report);
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"definicion_activos.xls\"");
			header("Content-Disposition: inline; filename=\"definicion_activos.xls\"");
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
?> 