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
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//	    		   as_desnom // descripción de la nómina
		//	    		   as_periodo // período actual de la nómina
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_garantia_prestaciones_segurosocial.php",$ls_descripcion);
		return $lb_valido;
	}		
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_desnom // Descripción de la nómina
		//	    		   as_periodo // Descripción del período
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(30,30,600,30);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->ezSety(690);
		$la_titulo[1]=array('codigo'=>$as_titulo);
		$la_columna=array('codigo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_titulo,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cedper,$as_nombre,$ad_fecingper,$ai_diabonvac,$ai_diabonfin,$as_periodo,$io_pdf)
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
		global $io_pdf;
        $io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$la_data[0]=array('datos1'=>'Nombre del Trabajador:        '."<b>".$as_nombre."</b>");
		$la_data[1]=array('datos1'=>'Cédula del Trabajador:          '."<b>".$as_cedper."</b>");
		$la_data[2]=array('datos1'=>'Fecha de Ingreso:                 '.$ad_fecingper);
		$la_data[3]=array('datos1'=>'Días de Bono vacacional:     '."<b>".$ai_diabonvac."</b>");
		$la_data[4]=array('datos1'=>'Días de Utilidades:               '."<b>".$ai_diabonfin."</b>");
		$la_data[5]=array('datos1'=>'Ultimo periodo calculado:     '.$as_periodo);
		$la_columna=array('datos1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho Máximo de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
				 		 'cols'=>array('datos1'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');		
	}// uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle($as_data,$io_pdf)
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
		global $io_pdf;
		$io_pdf->ezSetY(601);
		$la_columna=array('periodo'=>'<b>Período</b>','salario_n'=>'<b>Salario Normal</b>','salario_d'=>'<b>Salario Diario</b>','inc_vac'=>'<b>Incidencia Vacaciones</b>',
					   'inc_uti'=>'<b>Incidencia Utilidades</b>','dias_mes'=>'<b>Días por mes</b>','ant_mens'=>'<b>Antiguedad Mensual</b>','anticipo_prest'=>'<b>Anticipo de Prestaciones</b>',
					   'ant_acumulada'=>'<b>Antiguedad Acumulada</b>','cap_p_detint'=>'<b>Capital para determinar Intereses</b>',
					   'tasa_bcv'=>'<b>Tasa publicada por el BCV 2/</b>','int_mensuales'=>'<b>Intereses Mensuales</b>','int_pagados'=>'<b>Intereses Pagados</b>',
					   'int_acumulados'=>'<b>Intereses Acumulados</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('periodo'=>array('justification'=>'center','width'=>30),
									   'salario_n'=>array('justification'=>'center','width'=>40),
									   'salario_d'=>array('justification'=>'center','width'=>30),
									   'inc_vac'=>array('justification'=>'center','width'=>40),
									   'inc_uti'=>array('justification'=>'center','width'=>40),
									   'dias_mes'=>array('justification'=>'center','width'=>25),
									   'ant_mens'=>array('justification'=>'center','width'=>40),
									   'anticipo_prest'=>array('justification'=>'center','width'=>42),
									   'ant_acumulada'=>array('justification'=>'center','width'=>50),
									   'cap_p_detint'=>array('justification'=>'center','width'=>50),
									   'tasa_bcv'=>array('justification'=>'center','width'=>40),
									   'int_mensuales'=>array('justification'=>'center','width'=>40),
						 			   'int_pagados'=>array('justification'=>'center','width'=>40),
									   'int_acumulados'=>array('justification'=>'center','width'=>43))); // Justificación y ancho de la 		
	   $io_pdf->ezTable($as_data,$la_columna,'',$la_config);		
									   
	}// end function uf_print_detalle
	///---------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_totales_detalles($li_dias,$li_ant_acu,$li_ant_prest,$li_in_acum,$li_int_pag,$li_total_depositado,$li_total_retirado,
								 $li_antiguedad_disponible,$li_intereses_disponible,$li_total,$li_antiguedad_acumulada,$io_pdf)
	{
		global $io_pdf;
		$la_data[1]=array('total'=>'<b>Total Prestaciones Sociales</b>',
		                  'total1'=>'<b>'.$li_dias.'</b>', 
						  'total2'=>'<b>'.$li_ant_acu.'</b>',
						  'total3'=>'<b>'.$li_ant_prest.'</b>',
						  'total4'=>'<b>'.$li_in_acum.'</b>',
						  'total5'=>'<b>'.$li_int_pag.'</b>',
						  'total6'=>'');

		$la_columna=array('total'=>'',
		                  'total1'=>'', 
						  'total2'=>'',
						  'total3'=>'',
						  'total4'=>'',
						  'total5'=>'',
						  'total6'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'left','width'=>180),
						 			   'total1'=>array('justification'=>'center','width'=>25),
						 			   'total2'=>array('justification'=>'center','width'=>40),
						 			   'total3'=>array('justification'=>'center','width'=>42),
						 			   'total4'=>array('justification'=>'right','width'=>180),
						 			   'total5'=>array('justification'=>'center','width'=>40),
						 			   'total6'=>array('justification'=>'center','width'=>43))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);

		$la_data[1]=array('total1'=>'<b>Total Prestaciones de antiguedad Acumulada</b>','monto1'=>$li_antiguedad_acumulada,'total2'=>'<b>Total Anticipo de Prestacion de Antiguedad</b>','monto2'=>$li_ant_prest,'total3'=>'<b>Total Disponible en Prestacion de Antiguedad</b>','monto3'=>$li_antiguedad_disponible);
		$la_data[2]=array('total1'=>'<b>Total Interés acumulado</b>','monto1'=>$li_in_acum,'total2'=>'<b>Total anticipos de Interés</b>','monto2'=>$li_int_pag,'total3'=>'<b>Total Disponible en Intereses</b>','monto3'=>$li_intereses_disponible);
		$la_data[3]=array('total1'=>'<b>Total depósito de la garantia de las prestaciones sociales</b>','monto1'=>$li_total_depositado,'total2'=>'<b>Total Anticipos</b>','monto2'=>$li_total_retirado,'total3'=>'<b>Total Disponible en Prestacion de Antiguedad e intereses</b>','monto3'=>$li_total);
		$la_columna=array('total1'=>'','monto1'=>'','total2'=>'','monto2'=>'','total3'=>'','monto3'=>'');

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total1'=>array('justification'=>'left','width'=>135),
						 			   'monto1'=>array('justification'=>'right','width'=>50),
						 			   'total2'=>array('justification'=>'left','width'=>135),
						 			   'monto2'=>array('justification'=>'right','width'=>50),
						 			   'total3'=>array('justification'=>'left','width'=>130),
						 			   'monto3'=>array('justification'=>'right','width'=>50))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//----------------------------------------------------------------------------------------------------------------------------
	function uf_firmantes($io_pdf)
	{		
		global $io_pdf;
		$la_data[1]=array('total'=>'',
		                  'total1'=>'', 
						  'total2'=>'');
		$la_data[2]=array('total'=>'',
		                  'total1'=>'', 
						  'total2'=>'');
		$la_data[3]=array('total'=>'<b>Firma del trabajador:                        ______________________</b>',
		                  'total1'=>'                              ', 
						  'total2'=>'<b>Firma por RRHH:                      ______________________</b>');
		$la_data[4]=array('total'=>'<b>Nombre y apellido del trabajador:  ______________________</b>',
		                  'total1'=>'                              ', 
						  'total2'=>'<b>Nombre y apellido por RRHH: ______________________</b>');
		$la_data[5]=array('total'=>'Cédula del trabajador:                        ______________________</b>',
		                  'total1'=>'                              ', 
						  'total2'=>'Cédula por RRHH:                       ______________________</b>');

		$la_columna=array('total'=>'',
		                  'total1'=>'', 
						  'total2'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas						 
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('total'=>array('justification'=>'left','width'=>225),
						 			   'total1'=>array('justification'=>'center','width'=>100),
						 			   'total2'=>array('justification'=>'center','width'=>225))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);

	}
	//----------------------------------------------------------------------------------------------------------------------------

     
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
	$ls_titulo="<b>Informe del monto depositado o acreditado por concepto de garantía de las prestaciones sociales según Ley de 2012</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper","");
	$ls_periodo=$io_fun_nomina->uf_obtenervalor_get("periodo","");
	$ls_anio=substr($ls_periodo,0,4);
	$ls_mes=substr($ls_periodo,7,9);
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,'',$ls_periodo); // Seguridad de Reporte
	$lb_valido=true;
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_seleccionar_personal_garantia($ls_codper,$ls_anio); 
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
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(6.7,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
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
			uf_print_cabecera($ls_cedula,$ls_nombre,$ld_fecingper,$li_diabonvac,$li_diabonfin,$ls_periodo,$io_pdf);	
			$lb_valido=$io_report->uf_detalles_personal_garantia($ls_codper,$ls_mes_int,$ls_anio);
			$ls_data=array();
			$li_antiguedad_acumulada=0;
			$li_intereses_acumulados=0;
			$li_totales_ant_prestacion=0;
			$li_totales_intereses_pagados=0;
			$li_totales_dias=0;
			$li_total_apoper=0;
			$li_antiguedad_disponible=0;
			$li_intereses_disponible=0;
			$li_intereses_ant=0;
			$li_capital_d_interes=0;
			$li_total_depositado=0;
			$li_j=0;
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
				$li_antiguedad_acumulada = $li_antiguedad_acumulada+$li_monpreant;				
				$li_totales_intereses_pagados=$li_totales_intereses_pagados+$li_monantint;							
				$li_intereses_acumulados=($li_intereses_acumulados+$li_monint);
				$li_capital_d_interes=$li_capital_d_interes-$li_monant+$li_monpreant-$li_monantint+$li_monint;
				$li_antiguedad_disponible=$li_antiguedad_disponible+$li_monpreant+$li_monint-$li_monantint-$li_monant;
				$li_total_depositado=$li_total_depositado+$li_monpreant+$li_monint;
			}
			while ((!$io_report->rs_detalle->EOF)&&($lb_valido))
			{  
				$ls_codnom=$io_report->rs_detalle->fields["codnom"];			  
				$ls_codper=$io_report->rs_detalle->fields["codper"];			  
				$ls_anocurper=$io_report->rs_detalle->fields["anocurper"];
				$ls_mescurper=str_pad($io_report->rs_detalle->fields["mescurper"],2,'0',0);
				$ls_periodo_p=$ls_anocurper."-".$ls_mescurper;
				$li_salario_normal=$io_report->rs_detalle->fields["sueintper"];
				$li_salario_diario=$li_salario_normal/30;
				$li_incid_vacac=$io_report->rs_detalle->fields["bonvacper"];
				$li_incid_util=$io_report->rs_detalle->fields["bonfinper"];
				$li_dia_fid=$io_report->rs_detalle->fields["diafid"];
				$li_dia_adi=$io_report->rs_detalle->fields["diaadi"];
				$li_dias_mes=$li_dia_fid+$li_dia_adi;
				$li_apoper=$io_report->rs_detalle->fields["apoper"];
				$li_ant_prestacion=$io_report->rs_detalle->fields["anticipo_prestacion"];
				$li_tasa_public=$io_report->rs_detalle->fields["tasa_public"];
				$li_int_mensuales=$io_report->rs_detalle->fields["intereses_mens"];
				$li_intereses_pagados=$io_report->rs_detalle->fields["intereses_pagados"];	
				
				$li_antiguedad_acumulada = $li_antiguedad_acumulada+$li_apoper-$li_ant_prestacion;				

				$li_capital_d_interes=($li_capital_d_interes-$li_ant_prestacion-$li_intereses_pagados+$li_apoper+$li_intereses_ant);
				
				$li_totales_intereses_pagados=$li_totales_intereses_pagados+$li_intereses_pagados;							
				$li_intereses_acumulados=($li_intereses_acumulados+$li_int_mensuales);
				$li_totales_dias=$li_totales_dias+$li_dias_mes;
				$li_totales_ant_prestacion=$li_totales_ant_prestacion+$li_ant_prestacion;				
				$li_antiguedad_disponible=($li_antiguedad_disponible+$li_apoper)-$li_ant_prestacion;
				$li_intereses_disponible=($li_intereses_disponible+$li_int_mensuales)-$li_intereses_pagados;
				$li_intereses_ant=$li_int_mensuales;
				
				$li_salario_normal=$io_fun_nomina->uf_formatonumerico($li_salario_normal);
				$li_salario_diario=$io_fun_nomina->uf_formatonumerico($li_salario_diario);				
				$li_incid_vacac=$io_fun_nomina->uf_formatonumerico($li_incid_vacac);
				$li_incid_util=$io_fun_nomina->uf_formatonumerico($li_incid_util);
				$li_tasa_public=$io_fun_nomina->uf_formatonumerico($li_tasa_public);
								
				$ls_data[$li_j]=array('periodo'=>$ls_periodo_p,'salario_n'=>$li_salario_normal,'salario_d'=>$li_salario_diario,
									  'inc_vac'=>$li_incid_vacac,'inc_uti'=>$li_incid_util,'dias_mes'=>$li_dias_mes,
									  'ant_mens'=>$io_fun_nomina->uf_formatonumerico($li_apoper),
									  'anticipo_prest'=>$io_fun_nomina->uf_formatonumerico($li_ant_prestacion),
									  'ant_acumulada'=>$io_fun_nomina->uf_formatonumerico($li_antiguedad_acumulada),
									  'cap_p_detint'=>$io_fun_nomina->uf_formatonumerico($li_capital_d_interes),'tasa_bcv'=>$li_tasa_public,'int_mensuales'=>$io_fun_nomina->uf_formatonumerico($li_int_mensuales),
									  'int_pagados'=>$io_fun_nomina->uf_formatonumerico($li_intereses_pagados),'int_acumulados'=>$io_fun_nomina->uf_formatonumerico($li_intereses_disponible));
			    $li_total_apoper=$li_total_apoper+$li_apoper;
				$li_j++;
				$io_report->rs_detalle->MoveNext();			      
			}
			if (empty($ls_data))
			{
				$lb_valido=false;
			}
			uf_print_detalle($ls_data,$io_pdf);
			
			$li_total_depositado=$li_total_depositado+$li_total_apoper+$li_intereses_acumulados;
			$li_total_retirado=$li_totales_ant_prestacion+$li_totales_intereses_pagados;
			$li_total=$li_total_depositado-$li_total_retirado;
			$li_total_apoper=$io_fun_nomina->uf_formatonumerico($li_total_apoper);
			$li_totales_ant_prestacion=$io_fun_nomina->uf_formatonumerico($li_totales_ant_prestacion);
			$li_intereses_acumulados=$io_fun_nomina->uf_formatonumerico($li_intereses_acumulados);
			$li_totales_intereses_pagados=$io_fun_nomina->uf_formatonumerico($li_totales_intereses_pagados);
			$li_total_depositado=$io_fun_nomina->uf_formatonumerico($li_total_depositado);			
			$li_total_retirado=$io_fun_nomina->uf_formatonumerico($li_total_retirado);
			$li_antiguedad_disponible=$io_fun_nomina->uf_formatonumerico($li_antiguedad_disponible);
			$li_intereses_disponible=$io_fun_nomina->uf_formatonumerico($li_intereses_disponible);
			$li_total=$io_fun_nomina->uf_formatonumerico($li_total);
			$li_antiguedad_acumulada=$io_fun_nomina->uf_formatonumerico($li_antiguedad_acumulada);

			uf_totales_detalles($li_totales_dias,$li_total_apoper,$li_totales_ant_prestacion,$li_intereses_acumulados,$li_totales_intereses_pagados,
								$li_total_depositado,$li_total_retirado,$li_antiguedad_disponible,$li_intereses_disponible,$li_total,$li_antiguedad_acumulada,$io_pdf);
			uf_firmantes($io_pdf);
			unset($ls_data);
			$io_report->rs_data->MoveNext();	  

		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 