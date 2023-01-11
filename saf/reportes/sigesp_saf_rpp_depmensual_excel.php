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
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($lo_libro,$lo_hoja,$as_fecha,$as_codcatsudeban,$li_fila,$li_cuecon)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_titulo->set_merge();
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_hoja->write($li_fila, 3, $as_fecha,$lo_titulo);
		if($as_codcatsudeban=="")
		{
			$li_fila++;
			$as_codcatsudeban="Todas las Clasificaciones";
			$lo_hoja->write($li_fila, 3, $as_codcatsudeban,$lo_titulo);
		}
		else
		{
			$li_fila++;
			$as_cat="Bienes de Clasificacion: ".$as_codcatsudeban;
			$lo_hoja->write($li_fila, 3, $as_cat,$lo_titulo);
		}
		if($li_cuecon)
		{
			$li_fila++;
			$as_cuenta="Cuenta: ".$li_cuecon;
			$lo_hoja->write($li_fila, 3, $as_cuenta,$lo_titulo);
		}
		$li_fila++;
		return $li_fila;
	}// end function uf_print_encabezadopagina
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
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_hoja->set_column(0,0,25);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,40);
		$lo_hoja->set_column(3,3,10);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,15);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,20);
		$lo_hoja->write($li_fila, 0, 'Código',$lo_titulo);
		$lo_hoja->write($li_fila, 1, 'Identificador',$lo_titulo);
		$lo_hoja->write($li_fila, 2, 'Denominación',$lo_titulo);
		$lo_hoja->write($li_fila, 3, 'Incorporacion',$lo_titulo);
		$lo_hoja->write($li_fila, 4, 'V.U.',$lo_titulo);
		$lo_hoja->write($li_fila, 5, 'Costo Bs.',$lo_titulo);
		$lo_hoja->write($li_fila, 6, 'Valor Rescate Bs.',$lo_titulo);
		$lo_hoja->write($li_fila, 7, 'Costo - VR Bs.',$lo_titulo);
		$lo_hoja->write($li_fila, 8, 'Mes Dep.',$lo_titulo);
		$lo_hoja->write($li_fila, 9, 'Dep. Mensual Bs.',$lo_titulo);
		$lo_hoja->write($li_fila, 10, 'Dep. Acum. Bs.',$lo_titulo);
		$lo_hoja->write($li_fila, 11, 'Por Depreciar Bs.',$lo_titulo);
		$li_fila++;
		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, " ".$la_data[$li_j]['codact'],$lo_dataleft);
			$lo_hoja->write($li_fila, 1, " ".$la_data[$li_j]['ideact'],$lo_dataleft);
			$lo_hoja->write($li_fila, 2, $la_data[$li_j]['denact'],$lo_dataleft);
			$lo_hoja->write($li_fila, 3, $la_data[$li_j]['fecinc'],$lo_dataleft);
			$lo_hoja->write($li_fila, 4, $la_data[$li_j]['viduti'],$lo_dataright);
			$lo_hoja->write($li_fila, 5, $la_data[$li_j]['costo'],$lo_dataright);
			$lo_hoja->write($li_fila, 6, $la_data[$li_j]['cossal'],$lo_dataright);
			$lo_hoja->write($li_fila, 7, $la_data[$li_j]['mondep'],$lo_dataright);
			$lo_hoja->write($li_fila, 8, $la_data[$li_j]['mesdep'],$lo_dataleft);
			$lo_hoja->write($li_fila, 9, $la_data[$li_j]['depmen'],$lo_dataright);
			$lo_hoja->write($li_fila, 10, $la_data[$li_j]['depacu'],$lo_dataright);
			$lo_hoja->write($li_fila, 11, $la_data[$li_j]['pordep'],$lo_dataright);
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
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		$li_fila++;
		$lo_hoja->write($li_fila, 5, 'TOTAL',$lo_titulo);
		$lo_hoja->write($li_fila, 6, $ai_montot,$lo_dataright);
		$li_fila++;
		return $li_fila;
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total($la_datat,$lo_libro,$lo_hoja,$li_fila)
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
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 4, "Total ",$lo_titulo);
		$lo_hoja->write($li_fila, 5, $la_datat[1]['costo'],$lo_dataright);
		$lo_hoja->write($li_fila, 6, $la_datat[1]['cossal'],$lo_dataright);
		$lo_hoja->write($li_fila, 7, $la_datat[1]['mondep'],$lo_dataright);
		$lo_hoja->write($li_fila, 8, $la_datat[1]['mesdep'],$lo_dataright);
		$lo_hoja->write($li_fila, 9, $la_datat[1]['depmen'],$lo_dataright);
		$lo_hoja->write($li_fila, 10, $la_datat[1]['depacu'],$lo_dataright);
		$lo_hoja->write($li_fila, 11, $la_datat[1]['pordep'],$lo_dataright);
		$li_fila++;
		return $li_fila;
	}// end function uf_print_total
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales_generales($la_datat,$lo_libro,$lo_hoja,$li_fila)
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
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 4, "Total General",$lo_titulo);
		$lo_hoja->write($li_fila, 5, $la_datat[1]['costo'],$lo_dataright);
		$lo_hoja->write($li_fila, 6, $la_datat[1]['cossal'],$lo_dataright);
		$lo_hoja->write($li_fila, 7, $la_datat[1]['mondep'],$lo_dataright);
		$lo_hoja->write($li_fila, 8, $la_datat[1]['mesdep'],$lo_dataright);
		$lo_hoja->write($li_fila, 9, $la_datat[1]['depmen'],$lo_dataright);
		$lo_hoja->write($li_fila, 10, $la_datat[1]['depacu'],$lo_dataright);
		$lo_hoja->write($li_fila, 11, $la_datat[1]['pordep'],$lo_dataright);
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
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataleft->set_bold();
		$li_fila++;
		$lo_hoja->write($li_fila, 0, "Clasificación:   ".$as_codcat."   -",$lo_dataleft);
		$lo_hoja->write($li_fila, 1, $as_dencat,$lo_dataleft);
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
	$lo_archivo = tempnam("/tmp", "Depreciacion_Mensual.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
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
	$ls_titulo="Reporte de Depreciación Mensual en ".$ls_titulo_report." ";
	$ld_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_mes=$io_fun_activos->uf_obtenervalor_get("mes","");
	$li_anio=$io_fun_activos->uf_obtenervalor_get("anio","");
	$li_cuecon=$io_fun_activos->uf_obtenervalor_get("cuecon","");
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_codcatsudeban=$io_fun_activos->uf_obtenervalor_get("codcatsudeban","");
	$ls_estsudeban=$io_report->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estsudeban);
	$li_auxmes=$io_fec->uf_load_numero_mes($ls_mes);
	$ls_fecha="Periodo:  ".$ls_mes."  ".$li_anio;
	//--------------------------------------------------------------------------------------------------------------------------------
	$ld_fecdep=$io_fec->uf_last_day($li_auxmes,$li_anio);
	$ld_fecdepaux="01/".$li_auxmes."/".$li_anio;
	//--------------------------------------------------------------------------------------------------------------------------------
	//set_time_limit(1800);
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
/*	$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');*/
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,20);
	$lo_hoja->set_column(2,2,50);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,30);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,30);

	$lo_hoja->write(0, 3, $ls_titulo,$lo_encabezado);
	$li_fila=2;
	if($ls_estsudeban!=1)
	{
			$lb_valido=true;
			$rs_data=$io_report->uf_saf_load_depmensual($ls_codemp,$li_ordenact,$ld_fecdep,$ld_fecdepaux,$ls_codcatsudeban,'',$li_cuecon); // Cargar el DS con los datos de la cabecera del reporte
			/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////
			$ls_desc_event=" Generó el reporte de Depreciacion Mensual de Activos. ";
			$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_depmensual.php",$ls_desc_event);
			////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////
			$li_fila=uf_print_encabezado_pagina($lo_libro,$lo_hoja,$ls_fecha,$ls_codcatsudeban,$li_fila,$li_cuecon); // Imprimimos el encabezado de la página
			$li_totrow=$io_report->ds->getRowCount("codact");
			$li_totmondep=0;
			$li_totcosto=0;
			$li_totcossal=0;
			$li_totmondep=0;
			$li_totdepmen=0;
			$li_totdepacu=0;
			$li_totpordep=0;
			//Total General
			$li_totmondep_tot=0;
			$li_totdepmen_tot=0;
			$li_totdepacu_tot=0;
			$li_totpordep_tot=0;
			$li_totcossal_tot=0;
			$li_totcosto_tot=0;
			//Total General
			$li_i=0;
			while(!$rs_data->EOF)
			{
				$ls_codact=  $rs_data->fields["codact"];
				$ls_denact=  $rs_data->fields["denact"];
				$ls_ideact=  $rs_data->fields["ideact"];
				$li_mesdep=" -- ";
				$li_viduti=  $rs_data->fields["vidautil"];
				$li_costo=   $rs_data->fields["costo"];
				$li_cossal=  $rs_data->fields["cossal"];
				$li_depmen=  $rs_data->fields["mondepmen"];
				$li_depacu=  $rs_data->fields["mondepacu"];
				$ls_fecincact=  $rs_data->fields["fecincact"];
				$ls_fecincact=$io_funciones->uf_convertirfecmostrar($ls_fecincact);
				$li_mondep= ($li_costo - $li_cossal);
				$li_pordep= ($li_mondep - $li_depacu);
				$li_totmondep=($li_totmondep + $li_mondep);
				$li_totdepmen=($li_totdepmen + $li_depmen);
				$li_totdepacu=($li_totdepacu + $li_depacu);
				$li_totpordep=($li_totpordep + $li_pordep);
				$li_totcossal=($li_totcossal + $li_cossal);
				$li_totcosto=($li_totcosto + $li_costo);
					$lb_valido1=$io_report->uf_saf_select_dt_depactivo($ls_codemp,$ls_codact,$ls_ideact);
					$li_vidutimes=($li_viduti * 12);
					if($lb_valido1)
					{
						$li_mes=1;
						$li_totrow1=$io_report->ds_detalle->getRowCount("codact");
						for($li_s=1;$li_s<=$li_totrow1;$li_s++)
						{
							$ld_fecdepact= $io_report->ds_detalle->data["fecdep"][$li_s];
							$ld_fecdepact=$io_funciones->uf_convertirfecmostrar($ld_fecdepact);
							if(($ld_fecdepact!=$ld_fecdep)&&($ld_fecdepact!=$ld_fecdepaux))
							{
								$li_mes=($li_mes + 1);
							}
							else
							{break;}
						}
					}
				if($ls_depcomp==1)
				{
					$li_mesdep=0;
				}
				else
				{
					$li_mesdep=$li_mes."/".$li_vidutimes;
				}
				$li_i++;
				$la_data[$li_i]=array('codact'=>$ls_codact,'ideact'=>$ls_ideact,'denact'=>$ls_denact,'fecinc'=>$ls_fecincact,'mesdep'=>$li_mesdep,
									  'viduti'=>$li_viduti,'costo'=>$li_costo,'cossal'=>$li_cossal,'depmen'=>$li_depmen,
									  'depacu'=>$li_depacu,'mondep'=>$li_mondep,'pordep'=>$li_pordep);
				
				$rs_data->MoveNext();
			}
			$li_fila=uf_print_detalle($li_i,$lo_libro,$lo_hoja,$la_data,$li_fila); // Imprimimos el detalle 
			//total general
			$li_totmondep_tot=$li_totmondep_tot+$li_totmondep;
			$li_totdepmen_tot=$li_totdepmen_tot+$li_totdepmen;
			$li_totdepacu_tot=$li_totdepacu_tot+$li_totdepacu;
			$li_totpordep_tot=$li_totpordep_tot+$li_totpordep;
			$li_totcossal_tot=$li_totcossal_tot+$li_totcossal;
			$li_totcosto_tot=$li_totcosto_tot+$li_totcosto;
			//total general
			$la_datat[1]=array('total'=>"Total ",'costo'=>$li_totcosto,'cossal'=>$li_totcossal,'mondep'=>$li_totmondep,
							   'mesdep'=>" -- ",'depmen'=>$li_totdepmen,'depacu'=>$li_totdepacu,'pordep'=>$li_totpordep);
			$li_fila=uf_print_total($la_datat,$lo_libro,$lo_hoja,$li_fila);
			unset($la_data);			
			unset($la_data1);
			//total general
			$la_datatt[1]=array('total'=>"Total General",'costo'=>$li_totcosto_tot,'cossal'=>$li_totcossal_tot,'mondep'=>$li_totmondep_tot,
									   'mesdep'=>" -- ",'depmen'=>$li_totdepmen_tot,'depacu'=>$li_totdepacu_tot,'pordep'=>$li_totpordep_tot);
			$li_fila=uf_print_totales_generales($la_datatt,$lo_libro,$lo_hoja,$li_fila);
			unset($la_datatt);
			//total general
		unset($io_report);
		unset($la_data);
		unset($la_datat);
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
			$li_fila=uf_print_encabezado_pagina($lo_libro,$lo_hoja,$ls_fecha,$ls_codcatsudeban,$li_fila,$li_cuecon);
			$li_totrowcat=$io_report->ds_sudeban->getRowCount("codcat");
			$i=0;
			$ld_total_costo=0;
			//Total General
			$li_totmondep_tot=0;
			$li_totdepmen_tot=0;
			$li_totdepacu_tot=0;
			$li_totpordep_tot=0;
			$li_totcossal_tot=0;
			$li_totcosto_tot=0;
			//Total General
			for($li_j=1;$li_j<=$li_totrowcat;$li_j++)
			{
				$ls_codcat=$io_report->ds_sudeban->data["codcat"][$li_j];
				$ls_dencat=$io_report->ds_sudeban->data["dencat"][$li_j];
				$li_fila=uf_print_categoria($ls_codcat,$ls_dencat,$lo_libro,$lo_hoja,$li_fila);
				$rs_data=$io_report->uf_saf_load_depmensual($ls_codemp,$li_ordenact,$ld_fecdep,$ld_fecdepaux,$ls_codcat); // Cargar el DS con los datos de la cabecera del reporte
				$li_totrow=$io_report->ds->getRowCount("codact");
				$li_totmondep=0;
				$li_totcosto=0;
				$li_totcossal=0;
				$li_totmondep=0;
				$li_totdepmen=0;
				$li_totdepacu=0;
				$li_totpordep=0;
				$la_data="";
				$li_i=0;
				while(!$rs_data->EOF)
				{
					$li_i++;
					$ls_codact=  $rs_data->fields["codact"];
					$ls_denact=  $rs_data->fields["denact"];
					$ls_ideact=  $rs_data->fields["ideact"];
					$li_mesdep=" -- ";
					$li_viduti=  $rs_data->fields["vidautil"];
					$li_costo=   $rs_data->fields["costo"];
					$li_cossal=  $rs_data->fields["cossal"];
					$li_depmen=  $rs_data->fields["mondepmen"];
					$li_depacu=  $rs_data->fields["mondepacu"];
					$ls_depcomp=  $rs_data->fields["depcomp"];
					$ls_fecincact=  $rs_data->fields["fecincact"];
					$ls_fecincact=$io_funciones->uf_convertirfecmostrar($ls_fecincact);
					$li_mondep= ($li_costo - $li_cossal);
					$li_pordep= ($li_mondep - $li_depacu);
					$li_totmondep=($li_totmondep + $li_mondep);
					$li_totdepmen=($li_totdepmen + $li_depmen);
					$li_totdepacu=($li_totdepacu + $li_depacu);
					$li_totpordep=($li_totpordep + $li_pordep);
					$li_totcossal=($li_totcossal + $li_cossal);
					$li_totcosto=($li_totcosto + $li_costo);
					$lb_valido1=$io_report->uf_saf_select_dt_depactivo($ls_codemp,$ls_codact,$ls_ideact);
					$li_vidutimes=($li_viduti * 12);
					if($lb_valido1)
					{
						$li_mes=1;
						$li_totrow1=$io_report->ds_detalle->getRowCount("codact");
						for($li_s=1;$li_s<=$li_totrow1;$li_s++)
						{
							$ld_fecdepact= $io_report->ds_detalle->data["fecdep"][$li_s];
							$ld_fecdepact=$io_funciones->uf_convertirfecmostrar($ld_fecdepact);
							if($ld_fecdepact!=$ld_fecdep)
							{
								$li_mes=($li_mes + 1);
							}
							else
							{break;}
						}
					}
					if($ls_depcomp==1)
					{
						$li_mesdep=0;
					}
					else
					{
						$li_mesdep=$li_mes."/".$li_vidutimes;
					}
					$la_data[$li_i]=array('codact'=>$ls_codact,'ideact'=>$ls_ideact,'denact'=>$ls_denact,'fecinc'=>$ls_fecincact,'mesdep'=>$li_mesdep,
										  'viduti'=>$li_viduti,'costo'=>$li_costo,'cossal'=>$li_cossal,'depmen'=>$li_depmen,
										  'depacu'=>$li_depacu,'mondep'=>$li_mondep,'pordep'=>$li_pordep);
					
					$rs_data->MoveNext();
				}
				$li_conta=count((array)$la_data);
				if($la_data!="")
				{
					$li_fila=uf_print_detalle($li_conta,$lo_libro,$lo_hoja,$la_data,$li_fila); // Imprimimos el detalle 
					//total general
					$li_totmondep_tot=$li_totmondep_tot+$li_totmondep;
					$li_totdepmen_tot=$li_totdepmen_tot+$li_totdepmen;
					$li_totdepacu_tot=$li_totdepacu_tot+$li_totdepacu;
					$li_totpordep_tot=$li_totpordep_tot+$li_totpordep;
					$li_totcossal_tot=$li_totcossal_tot+$li_totcossal;
					$li_totcosto_tot=$li_totcosto_tot+$li_totcosto;
					//total general					
					$la_datat[1]=array('total'=>"Total ",'costo'=>$li_totcosto,'cossal'=>$li_totcossal,'mondep'=>$li_totmondep,
									   'mesdep'=>" -- ",'depmen'=>$li_totdepmen,'depacu'=>$li_totdepacu,'pordep'=>$li_totpordep);
					$li_fila=uf_print_total($la_datat,$lo_libro,$lo_hoja,$li_fila);
					unset($la_data);			
					unset($la_data1);			
				}
			}
			//total general
			$la_datatt[1]=array('total'=>"Total General",'costo'=>$li_totcosto_tot,'cossal'=>$li_totcossal_tot,'mondep'=>$li_totmondep_tot,
										   'mesdep'=>" -- ",'depmen'=>$li_totdepmen_tot,'depacu'=>$li_totdepacu_tot,'pordep'=>$li_totpordep_tot);
			$li_fila=uf_print_totales_generales($la_datatt,$lo_libro,$lo_hoja,$li_fila);
			//total general
			unset($la_datatt);
			unset($io_report);
			unset($io_funciones);
			unset($io_fun_nomina);
			unset($la_data);
			unset($la_datat);
		}
	}
		if($lb_valido)
		{
			unset($io_report);
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"Depreciacion_Mensual.xls\"");
			header("Content-Disposition: inline; filename=\"Depreciacion_Mensual.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			//print(" close();");
			print("</script>");
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generarse el Reporte');");
			//print(" close();");
			print("</script>");
		}
?> 