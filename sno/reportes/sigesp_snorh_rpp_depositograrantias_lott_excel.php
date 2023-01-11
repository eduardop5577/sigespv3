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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nombre,$ad_fecingper,$ai_diabonvac,$ai_diabonfin,$as_periodo,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomban // Nombre del Banco
		//	    		   io_cabecera // Objeto cabecera
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('left');
		$lo_titulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('left');
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
		$lo_hoja->set_column(0,0,35);
		$lo_hoja->set_column(1,1,35);
		$lo_hoja->write($li_fila, 0, 'Nombre del Trabajador:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$as_nombre,$lo_titulo);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Cédula del Trabajador:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$as_cedper,$lo_titulo);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Fecha de Ingreso:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$ad_fecingper,$lo_datadate);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Días de Bono vacacional:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$ai_diabonvac,$lo_titulo);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Días de Utilidades:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$ai_diabonfin,$lo_titulo);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Ultimo periodo calculado:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, " ".$as_periodo,$lo_dataleft);
		$li_fila++;
		$li_fila++;
		$li_fila++;
		return $li_fila;
	}// uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle($as_data,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=count((Array)$as_data);
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
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,3,15);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,15);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,10,15);
		$lo_hoja->set_column(11,11,15);
		$lo_hoja->set_column(12,12,15);
		$lo_hoja->set_column(13,13,15);
		$lo_hoja->write($li_fila, 0, 'Período',$lo_titulo);
		$lo_hoja->write($li_fila, 1, 'Salario Normal',$lo_titulo);
		$lo_hoja->write($li_fila, 2, 'Salario Diario',$lo_titulo);
		$lo_hoja->write($li_fila, 3, 'Incidencia Vacaciones',$lo_titulo);
		$lo_hoja->write($li_fila, 4, 'Incidencia Utilidades',$lo_titulo);
		$lo_hoja->write($li_fila, 5, 'Días por mes',$lo_titulo);
		$lo_hoja->write($li_fila, 6, 'Antiguedad Mensual',$lo_titulo);
		$lo_hoja->write($li_fila, 7, 'Anticipo de Prestaciones',$lo_titulo);
		$lo_hoja->write($li_fila, 8, 'Antiguedad Acumulada',$lo_titulo);
		$lo_hoja->write($li_fila, 9, 'Capital para determinar Intereses',$lo_titulo);
		$lo_hoja->write($li_fila, 10, 'Tasa publicada por el BCV 2/',$lo_titulo);
		$lo_hoja->write($li_fila, 11, 'Intereses Mensuales',$lo_titulo);
		$lo_hoja->write($li_fila, 12, 'Intereses Pagados',$lo_titulo);
		$lo_hoja->write($li_fila, 13, 'Intereses Acumulados',$lo_titulo);
		$li_fila++;
		
		for($li_j=0;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, $as_data[$li_j]['periodo'],$lo_datacenter);
			$lo_hoja->write($li_fila, 1, $as_data[$li_j]['salario_n'],$lo_datacenter);
			$lo_hoja->write($li_fila, 2, $as_data[$li_j]['salario_d'],$lo_datacenter);
			$lo_hoja->write($li_fila, 3, $as_data[$li_j]['inc_vac'],$lo_datacenter);
			$lo_hoja->write($li_fila, 4, $as_data[$li_j]['inc_uti'],$lo_datacenter);
			$lo_hoja->write($li_fila, 5, $as_data[$li_j]['dias_mes'],$lo_datacenter);
			$lo_hoja->write($li_fila, 6, $as_data[$li_j]['ant_mens'],$lo_dataright);
			$lo_hoja->write($li_fila, 7, $as_data[$li_j]['anticipo_prest'],$lo_dataright);
			$lo_hoja->write($li_fila, 8, $as_data[$li_j]['ant_acumulada'],$lo_dataright);
			$lo_hoja->write($li_fila, 9, $as_data[$li_j]['cap_p_detint'],$lo_dataright);
			$lo_hoja->write($li_fila, 10, $as_data[$li_j]['tasa_bcv'],$lo_dataright);
			$lo_hoja->write($li_fila, 11, $as_data[$li_j]['int_mensuales'],$lo_dataright);
			$lo_hoja->write($li_fila, 12, $as_data[$li_j]['int_pagados'],$lo_dataright);
			$lo_hoja->write($li_fila, 13, $as_data[$li_j]['int_acumulados'],$lo_dataright);
			$li_fila++;
		}							   
		return $li_fila;
	}// end function uf_print_detalle
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_totales_detalles($li_dias,$li_ant_acu,$li_ant_prest,$li_cap_d_int,$li_in_acum,$li_int_pag,
								 $li_total_depositado,$li_total_retirado,$li_total,$lo_libro,$lo_hoja,$li_fila)
	{
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		
		$lo_titulo2= &$lo_libro->addformat();
		$lo_titulo2->set_text_wrap();
		$lo_titulo2->set_bold();
		$lo_titulo2->set_font("Verdana");
		$lo_titulo2->set_align('right');
		$lo_titulo2->set_size('9');		
		//$lo_titulo2->set_merge(); 
		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('left');
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
		$lo_hoja->set_column(0,0,35);
		$lo_hoja->set_column(1,1,35);
		$lo_hoja->set_column(2,2,35);
		$lo_hoja->set_column(3,3,35);
		$lo_hoja->set_column(5,5,45);
		
		$lo_hoja->write($li_fila, 0, 'Total Prestaciones Sociales',$lo_titulo);
		$lo_hoja->write($li_fila, 5, " ".$li_dias,$lo_titulo);
		$lo_hoja->write($li_fila, 6, " ".$li_ant_acu,$lo_titulo);
		$lo_hoja->write($li_fila, 7, " ".$li_ant_prest,$lo_titulo);
		$lo_hoja->write($li_fila, 9, " ".$li_cap_d_int,$lo_titulo);
		$lo_hoja->write($li_fila, 11, " ".$li_in_acum,$lo_titulo);
		$lo_hoja->write($li_fila, 12, " ".$li_int_pag,$lo_titulo);
		$li_fila++;
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Total Días depositados:',$lo_titulo2);
		$lo_hoja->write($li_fila, 1, " ".$li_dias,$lo_titulo2);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Total Antiguedad Acumulada:',$lo_titulo2);
		$lo_hoja->write($li_fila, 1, " ".$li_ant_acu,$lo_titulo2);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Total Intereses Mesuales:',$lo_titulo2);
		$lo_hoja->write($li_fila, 1, " ".$li_in_acum,$lo_titulo2);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Total Depósito de la garantia de las prestaciones sociales:',$lo_titulo2);
		$lo_hoja->write($li_fila, 1, " ".$li_total_depositado,$lo_titulo2);
		$lo_hoja->write($li_fila, 5, 'Total disponible a lo depositado o acreditado de la garantia de las prestaciones sociales:',$lo_titulo2);
		$lo_hoja->write($li_fila, 6, " ".$li_total,$lo_titulo2);
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 1, 'Total Anticipo de Prestaciones:',$lo_titulo2);
		$lo_hoja->write($li_fila, 2, " ".$li_ant_prest,$lo_titulo2);
		$li_fila++;
		$lo_hoja->write($li_fila, 1, 'Total Intereses Pagado:',$lo_titulo2);
		$lo_hoja->write($li_fila, 2, " ".$li_int_pag,$lo_titulo2);
		$li_fila++;
		$lo_hoja->write($li_fila, 1, 'Total Retirado del depósito de la garantia de las prestaciones sociales:',$lo_titulo2);
		$lo_hoja->write($li_fila, 2, " ".$li_total_retirado,$lo_titulo2);
		$li_fila++;
		$li_fila++;
		$li_fila++;
		return $li_fila;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------------------------------------
	function uf_firmantes($lo_libro,$lo_hoja,$li_fila)
	{	
		$li_total=count((Array)$as_data);
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
		$lo_hoja->set_column(6,6,45);
		$lo_hoja->set_column(7,7,45);
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Firma del trabajador:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, ' ______________________',$lo_titulo);
		$lo_hoja->write($li_fila, 6, 'Firma por RRHH:',$lo_titulo);
		$lo_hoja->write($li_fila, 7, ' ______________________',$lo_titulo);
		$li_fila++;
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Nombre y apellido del trabajador:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, '______________________',$lo_titulo);
		$lo_hoja->write($li_fila, 6, 'Nombre y apellido por RRHH:',$lo_titulo);
		$lo_hoja->write($li_fila, 7, ' ______________________',$lo_titulo);
		$li_fila++;
		$li_fila++;
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'Cédula del trabajador:',$lo_titulo);
		$lo_hoja->write($li_fila, 1, ' ______________________',$lo_titulo);
		$lo_hoja->write($li_fila, 6, 'Cédula por RRHH:',$lo_titulo);
		$lo_hoja->write($li_fila, 7, ' ______________________',$lo_titulo);
		$li_fila++;
		$lo_hoja->write($li_fila, 8, 'Sello institucional',$lo_titulo);
		$li_fila++;
		$li_fila++;
		return $li_fila;
	}
	//----------------------------------------------------------------------------------------------------------------------------
   	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "Deposito_Garantias.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");	
	$ls_bolivares="";
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();					
    $ls_bolivares ="Bs.";
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Informe del monto depositado o acreditado por concepto de garantía de las prestaciones sociales según Ley de 2012";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper","");
	$ls_periodo=$io_fun_nomina->uf_obtenervalor_get("periodo","");
	$ls_anio=substr($ls_periodo,0,4);
	$ls_mes=substr($ls_periodo,7,9);
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
	$lo_hoja->set_column(0,0,15);
	$lo_hoja->set_column(1,1,20);
	$lo_hoja->set_column(2,2,50);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,30);
	$lo_hoja->set_column(5,5,30);
	$lo_hoja->set_column(6,6,30);

	$lo_hoja->write(0, 5, $ls_titulo,$lo_encabezado);
	$li_fila=2;
	$lb_valido=$io_report->uf_seleccionar_personal_garantia($ls_codper,$ls_anio); 
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////
		$ls_desc_event=" Generó el reporte de Deposito de Garantias. ";
		$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_garantia_prestaciones_segurosocial.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			$ls_cedula=$ls_nacper."-".$ls_cedper;
			$ls_nombre=$io_report->rs_data->fields["nomper"].", ".$io_report->rs_data->fields["apeper"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$li_diabonvac=$io_report->rs_data->fields["diabonvacfid"];
			$li_diabonfin=$io_report->rs_data->fields["diabonfinfid"];
			$ls_mes_int=intval($ls_mes);
			$li_fila=uf_print_cabecera($ls_cedula,$ls_nombre,$ld_fecingper,$li_diabonvac,$li_diabonfin,$ls_periodo,$lo_libro,$lo_hoja,$li_fila);	
			$lb_valido=$io_report->uf_detalles_personal_garantia($ls_codper,$ls_mes_int,$ls_anio);
			$ls_data=array();
			$li_antiguedad_acumulada=0;
			$li_intereses_acumulados=0;
			$li_totales_dias=0;
			$li_totales_ant_prestacion=0;
			$li_totales_capital_d_interes=0;
			$li_totales_intereses_pagados=0;
			$li_antiguedad_acumulada_m=0;
			$li_totales_ant_prestacion_m=0;
			$li_totales_capital_d_interes_m=0;
			$li_intereses_acumulados_m=0;
			$li_totales_intereses_pagados_m=0;
			$li_j=0;
			$x=0;
			$arrResultado=$io_report->uf_obtener_deudaanterior($ls_codper);
			$ls_feccordeu=$arrResultado['feccordeu'];
			$li_monpreant=$arrResultado['monpreant'];
			$li_monint=$arrResultado['monint'];
			$li_monant=$arrResultado['monant'];
			$li_monantint=$arrResultado['monantint'];
			$lb_valido=$arrResultado['valido'];
			if ($ls_feccordeu<>'')
			{
				$ls_data[$li_j]=array('periodo'=>$ls_feccordeu,'salario_n'=>$io_fun_nomina->uf_formatonumerico(0),'salario_d'=>$io_fun_nomina->uf_formatonumerico(0),
									  'inc_vac'=>$io_fun_nomina->uf_formatonumerico(0),'inc_uti'=>$io_fun_nomina->uf_formatonumerico(0),'dias_mes'=>0,
									  'ant_mens'=>$io_fun_nomina->uf_formatonumerico(0),
									  'anticipo_prest'=>$io_fun_nomina->uf_formatonumerico($li_monant),
									  'ant_acumulada'=>$io_fun_nomina->uf_formatonumerico($li_monpreant),
									  'cap_p_detint'=>0,'tasa_bcv'=>$io_fun_nomina->uf_formatonumerico(0),'int_mensuales'=>$io_fun_nomina->uf_formatonumerico(0),
									  'int_pagados'=>$io_fun_nomina->uf_formatonumerico($li_monantint),'int_acumulados'=>$io_fun_nomina->uf_formatonumerico($li_monint));
				$li_j++;
				$li_totales_ant_prestacion=$li_totales_ant_prestacion+$li_monant;				
				$li_antiguedad_acumulada = $li_antiguedad_acumulada+$li_monpreant-$li_monant;				
				$li_totales_intereses_pagados=$li_totales_intereses_pagados+$li_monantint;							
				$li_intereses_acumulados=($li_intereses_acumulados+$li_monint);
			}
			while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
			{  
				$ls_codnom=$io_report->rs_detalle->fields["codnom"];			  
				$ls_codper=$io_report->rs_detalle->fields["codper"];			  
				$ls_anocurper=$io_report->rs_detalle->fields["anocurper"];
				$ls_mescurper=$io_report->rs_detalle->fields["mescurper"];
				if ($ls_mescurper < 10)
				{
					$ls_mescurper="0".$ls_mescurper;
				}
				$ls_periodo_p=$ls_anocurper."-".$ls_mescurper;
				$li_salario_normal=$io_report->rs_detalle->fields["sueintper"];
				$li_salario_diario=$li_salario_normal/30;
				$li_salario_normal=$io_fun_nomina->uf_formatonumerico($li_salario_normal);
				$li_salario_diario=$io_fun_nomina->uf_formatonumerico($li_salario_diario);
				$li_incid_vacac=$io_report->rs_detalle->fields["bonvacper"];
				$li_incid_vacac=$io_fun_nomina->uf_formatonumerico($li_incid_vacac);
				$li_incid_util=$io_report->rs_detalle->fields["bonfinper"];
				$li_incid_util=$io_fun_nomina->uf_formatonumerico($li_incid_util);
				$li_dia_fid=$io_report->rs_detalle->fields["diafid"];
				$li_dia_adi=$io_report->rs_detalle->fields["diaadi"];
				$li_dias_mes=$li_dia_fid+$li_dia_adi;
				$li_totales_dias=$li_totales_dias+$li_dias_mes;
				$li_ant_mensual=$io_report->rs_detalle->fields["apoper"];
				$li_antiguedad_acumulada=$li_antiguedad_acumulada+$li_ant_mensual;
				$li_int_mensuales=$io_report->rs_detalle->fields["intereses_mens"];
				$li_intereses_acumulados=$li_intereses_acumulados+$li_int_mensuales;
				$li_ant_prestacion=$io_report->rs_detalle->fields["anticipo_prestacion"];
				$li_totales_ant_prestacion=$li_totales_ant_prestacion+$li_ant_prestacion;
				if ($x == 0)
				{
					$li_capital_d_interes=$li_antiguedad_acumulada;
					$li_totales_capital_d_interes=$li_totales_capital_d_interes+$li_capital_d_interes;
					$li_int_mensual_anterior=$li_int_mensuales;
				}
				else
				{
					$li_capital_d_interes=$li_antiguedad_acumulada+$li_int_mensual_anterior;
					$li_totales_capital_d_interes=$li_totales_capital_d_interes+$li_capital_d_interes;
					$li_int_mensual_anterior=$li_int_mensuales;
				}
				
				$li_tasa_public=$io_report->rs_detalle->fields["tasa_public"];
				$li_tasa_public=$io_fun_nomina->uf_formatonumerico($li_tasa_public);
				$li_intereses_pagados=$io_report->rs_detalle->fields["intereses_pagados"];
				$li_totales_intereses_pagados=$li_totales_intereses_pagados+$li_intereses_pagados;
				$li_intereses_pagados_m=$io_fun_nomina->uf_formatonumerico($li_intereses_pagados);
				$li_ant_prestacion_m=$io_fun_nomina->uf_formatonumerico($li_ant_prestacion);
				$li_ant_mensual=$io_fun_nomina->uf_formatonumerico($li_ant_mensual);
				$li_antiguedad_acumulada_m=$io_fun_nomina->uf_formatonumerico($li_antiguedad_acumulada);
				$li_int_mensuales_m=$io_fun_nomina->uf_formatonumerico($li_int_mensuales);
				$li_capital_d_interes_m=$io_fun_nomina->uf_formatonumerico($li_capital_d_interes);
				$li_intereses_acumulados_m=$io_fun_nomina->uf_formatonumerico($li_intereses_acumulados);
				$li_totales_ant_prestacion_m=$io_fun_nomina->uf_formatonumerico($li_totales_ant_prestacion);
				$li_totales_capital_d_interes_m=$io_fun_nomina->uf_formatonumerico($li_totales_capital_d_interes);
				$li_totales_intereses_pagados_m=$io_fun_nomina->uf_formatonumerico($li_totales_intereses_pagados);
				
				
				
				$ls_data[$li_j]=array('periodo'=>$ls_periodo_p,'salario_n'=>$li_salario_normal,'salario_d'=>$li_salario_diario,
									  'inc_vac'=>$li_incid_vacac,'inc_uti'=>$li_incid_util,'dias_mes'=>$li_dias_mes,
									  'ant_mens'=>$li_ant_mensual,'anticipo_prest'=>$li_ant_prestacion_m,'ant_acumulada'=>$li_antiguedad_acumulada_m,
									  'cap_p_detint'=>$li_capital_d_interes_m,'tasa_bcv'=>$li_tasa_public,'int_mensuales'=>$li_int_mensuales_m,
									  'int_pagados'=>$li_intereses_pagados_m,'int_acumulados'=>$li_intereses_acumulados_m);
				$li_j++;
				$x++;
				$io_report->rs_detalle->MoveNext();			      
			}

			if (empty($ls_data))
			{
				$lb_valido=false;
			}
			$li_total_depositado=$li_antiguedad_acumulada+$li_intereses_acumulados;
			$li_total_depositado_m=$io_fun_nomina->uf_formatonumerico($li_total_depositado);
			$li_total_retirado=$li_totales_ant_prestacion+$li_totales_intereses_pagados;
			$li_total_retirado_m=$io_fun_nomina->uf_formatonumerico($li_total_retirado);
			$li_total=$li_total_depositado-$li_total_retirado;
			$li_total_m=$io_fun_nomina->uf_formatonumerico($li_total);
			$li_fila=uf_print_detalle($ls_data,$lo_libro,$lo_hoja,$li_fila);
			$li_fila=uf_totales_detalles($li_totales_dias,$li_antiguedad_acumulada_m,$li_totales_ant_prestacion_m,
								$li_totales_capital_d_interes_m,$li_intereses_acumulados_m,$li_totales_intereses_pagados_m,
								$li_total_depositado_m,$li_total_retirado_m,$li_total_m,$lo_libro,$lo_hoja,$li_fila);	
			$li_fila=uf_firmantes($lo_libro,$lo_hoja,$li_fila);
			unset($ls_data);
			$io_report->rs_data->MoveNext();	  

		}
	}
	unset($io_report);
	unset($la_data);
	unset($la_datat);
	unset($io_funciones);
	unset($io_fun_nomina);
	
	if($lb_valido)
	{
		unset($io_report);
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Deposito_Garantias.xls\"");
		header("Content-Disposition: inline; filename=\"Deposito_Garantias.xls\"");
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