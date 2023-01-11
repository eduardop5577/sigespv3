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
	function uf_insert_seguridad($as_titulo,$as_desnom,$as_periodo,$ai_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Arreglo de las variables de seguridad
		//	    		   as_desnom // Arreglo de las variables de seguridad
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Generó el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_recibopago.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hrecibopago.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera1($as_cedper,$as_nomper,$as_descar,$as_descripcion,$io_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera1
		//		   Access: private 
		//	    Arguments: as_cedper // Cédula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripción del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
        $io_pdf->addJpegFromFile('../../shared/imagebank/cmsp1.jpg',40,720,65,65); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp2.jpg',510,720,65,65); // Agregar Logo
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0,1);
		$io_pdf->line(30,715,580,715); //HORIZONTAL
		$io_pdf->line(30,715,30,670); //VERTICAL
		$io_pdf->line(310,715,310,670); //VERTICAL
		$io_pdf->line(580,715,580,670); //VERTICAL
		$io_pdf->line(30,690,580,690); //HORIZONTAL
		$io_pdf->line(30,670,580,670); //HORIZONTAL
		$io_pdf->line(445,690,445,670); //VERTICAL

		$io_pdf->addText(40,705,8,$_SESSION["la_empresa"]["nombre"]);
		$io_pdf->addText(40,695,8,$as_descripcion);
		$io_pdf->addText(315,705,8,$as_nomper);
		$io_pdf->addText(315,695,8,$as_descar);
		$io_pdf->addText(40,675,9,"DESCRIPCIÓN");
		$io_pdf->addText(345,675,9,"ASIGNACIONES");
		$io_pdf->addText(480,675,9,"DEDUCCIONES");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera1_p2($as_cedper,$as_nomper,$as_descar,$as_descripcion,$io_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera1
		//		   Access: private 
		//	    Arguments: as_cedper // Cédula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripción del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
        $io_pdf->addJpegFromFile('../../shared/imagebank/cmsp1.jpg',40,720,65,65); // Agregar Logo
		$io_pdf->addJpegFromFile('../../shared/imagebank/cmsp2.jpg',510,720,65,65); // Agregar Logo
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0,1);
		$io_pdf->line(30,715,580,715); //HORIZONTAL
		$io_pdf->line(30,715,30,670); //VERTICAL
		$io_pdf->line(310,715,310,670); //VERTICAL
		$io_pdf->line(580,715,580,670); //VERTICAL
		$io_pdf->line(30,690,580,690); //HORIZONTAL
		$io_pdf->line(30,670,580,670); //HORIZONTAL
		$io_pdf->line(400,690,400,670); //VERTICAL
		$io_pdf->line(490,690,490,670); //VERTICAL
		$io_pdf->addText(40,705,8,$_SESSION["la_empresa"]["nombre"]);
		$io_pdf->addText(40,695,8,$as_descripcion);
		$io_pdf->addText(315,705,8,$as_nomper);
		$io_pdf->addText(315,695,8,$as_descar);
		$io_pdf->addText(40,675,9,"DESCRIPCIÓN");
		$io_pdf->addText(320,675,9,"ASIGNACIONES");
		$io_pdf->addText(415,675,9,"DEDUCCIONES");
		$io_pdf->addText(510,675,9,"APORTES");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_p2($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetY(672);
		$la_columna=array('denominacion'=>'',
						  'asignacion'=>'',
						  'deduccion'=>'',
						  'aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>600, // Ancho de la tabla
						 'maxWidth'=>600, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>90),
									   'aporte'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetY(672);
		$la_columna=array('denominacion'=>'',
						  'asignacion'=>'',
						  'deduccion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>600, // Ancho de la tabla
						 'maxWidth'=>600, // Ancho Máximo de la tabla
						 'xPos'=>310, // Orientación de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>280), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>135), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>135))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera1($ai_toting,$ai_totded,$ai_totapo,$ai_totnet,$as_nomper,$as_cedper,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera1
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_cedper // Cédula del Personal
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de página
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0,0);

		$la_data=array(array('texto'=>'', 'asignacion'=>$ai_toting,'deduccion'=>$ai_totded),
					   array('texto'=>'_______________________________________', 'asignacion'=>'','deduccion'=>''),
					   array('texto'=>$as_nomper, 'asignacion'=>'','deduccion'=>''),
					   array('texto'=>'CI: '.$as_cedper, 'asignacion'=>'<b>NETO A COBRAR '.$ls_bolivares.'      </b>','deduccion'=>$ai_totnet));
		$la_columna=array('texto'=>'',
						  'asignacion'=>'',
						  'deduccion'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>600, // Ancho de la tabla
						 'maxWidth'=>600, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>310, // Orientación de la tabla
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>280), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>135), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>135))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data=array(array('texto'=>"Procesado por: ".$_SESSION["la_empresa"]["nombre"]." Serial:SCP-0000003299               FIRMA ADMINISTRACIÓN"));
		$la_columna=array('texto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'rowGap' => 7 ,
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>580))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
					
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de página
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera1_p2($ai_toting,$ai_totded,$ai_totapo,$ai_totnet,$as_nomper,$as_cedper,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera1
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_cedper // Cédula del Personal
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de página
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0,0);
		$la_data=array(array('texto'=>'', 'asignacion'=>$ai_toting,'deduccion'=>$ai_totded,'aporte'=>''),
					   array('texto'=>'_______________________________________', 'asignacion'=>'','deduccion'=>'','aporte'=>''),
					   array('texto'=>$as_nomper, 'asignacion'=>'','deduccion'=>'','aporte'=>''),
					   array('texto'=>'CI: '.$as_cedper, 'asignacion'=>'<b>NETO A COBRAR '.$ls_bolivares.'      </b>','deduccion'=>$ai_totnet,'aporte'=>''));
		$la_columna=array('texto'=>'',
						  'asignacion'=>'',
						  'deduccion'=>'',
						  'aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>600, // Ancho de la tabla
						 'maxWidth'=>600, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>310, // Orientación de la tabla
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>280), // Justificación y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>90), // Justificación y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>90),
									   'aporte'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data=array(array('texto'=>"Procesado por: ".$_SESSION["la_empresa"]["nombre"]." Serial:SCP-0000003299               FIRMA ADMINISTRACIÓN"));
		$la_columna=array('texto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'rowGap' => 7 ,
						 'titleFontSize' => 7,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('texto'=>array('justification'=>'center','width'=>580))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de página
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($as_obsrecper,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_totalasignacion // Total Asignación
		//	   			   ai_totaldeduccion // Total Deduccción
		//	   			   ai_totalaporte // Total aporte
		//	   			   ai_total_neto // Total Neto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		if(trim($as_obsrecper)!='')
		{
			$la_data[0]=array('name'=>'OBSERVACIÓN:');
			$la_data[1]=array('name'=>'			'.$as_obsrecper);
			$la_data[2]=array('name'=>'');
		}
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('name'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_piecabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares ="Bs.";
	if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
	{
		require_once("sigesp_sno_class_report.php");
		$io_report=new sigesp_sno_class_report();
		$li_tipo=1;
	}
	if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
	{
		require_once("sigesp_sno_class_report_historico.php");
		$io_report=new sigesp_sno_class_report_historico();
		$li_tipo=2;
	}			
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$li_adelanto=$_SESSION["la_nomina"]["adenom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_descripcion="DEL ".$ld_fecdesper." AL ".$ld_fechasper;
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","-");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_codubifis=$io_fun_nomina->uf_obtenervalor_get("codubifis","");
	$ls_codpai=$io_fun_nomina->uf_obtenervalor_get("codpai","");
	$ls_codest=$io_fun_nomina->uf_obtenervalor_get("codest","");
	$ls_codmun=$io_fun_nomina->uf_obtenervalor_get("codmun","");
	$ls_codpar=$io_fun_nomina->uf_obtenervalor_get("codpar","");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	$ls_titulo="<b>COMPROBANTE DE PAGO</b>";
	$ls_periodo="Periodo: <b>".$ls_peractnom."</b> del <b>".$ld_fecdesper."</b> al <b>".$ld_fechasper."</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_recibopago_personal($ls_codperdes,$ls_codperhas,$ls_coduniadmdes,$ls_coduniadmhas,$ls_conceptocero,$ls_conceptop2,
													  $ls_conceptoreporte,$ls_codubifis,$ls_codpai,$ls_codest,$ls_codmun,$ls_codpar,
													  $ls_subnomdes,$ls_subnomhas,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false) || ($io_report->rs_data->RecordCount()==0)) // Existe algún error ó no hay registros
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
		$io_pdf->ezSetCmMargins(1,1,1,1); // Configuración de los margenes en centímetros
		$li_totrow=$io_report->rs_data->RecordCount();
		$li_reg=1;
		$li_i=1;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$li_toting=0;
			$li_totded=0;
			$li_totapo=0;				
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_descar=$io_report->rs_data->fields["descar"];
			$ls_codcueban=$io_report->rs_data->fields["codcueban"];
			$li_total=$io_report->rs_data->fields["total"];
			$ls_obsrecper=$io_report->rs_data->fields["obsrecper"];
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			if ($ls_conceptop2!=1)
			{
				uf_print_cabecera1($ls_cedper,$ls_nomper,$ls_descar,$ls_descripcion,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
				$io_pdf->ezSetY(718);
			}
			else
			{
				uf_print_cabecera1_p2($ls_cedper,$ls_nomper,$ls_descar,$ls_descripcion,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
				$io_pdf->ezSetY(718);
			}

			$lb_valido=$io_report->uf_recibopago_conceptopersonal($ls_codper,$ls_conceptocero,$ls_conceptop2,
																  $ls_conceptoreporte,$ls_tituloconcepto,$ls_quincena); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->rs_data_detalle->RecordCount();
				$li_asig=0;
				$li_dedu=0;
				$li_s=1;
				while(!$io_report->rs_data_detalle->EOF)
				{
					$ls_tipsal=rtrim($io_report->rs_data_detalle->fields["tipsal"]);
					if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="W1") ) // Buscamos las asignaciones
					{
						$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
						$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
						$li_toting=$li_toting+abs($io_report->rs_data_detalle->fields["valsal"]);
						$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
						$la_data[$li_s]=array('denominacion'=>$ls_nomcon,'asignacion'=>$li_valsal,'deduccion'=>'','aporte'=>'');
					}
					else // Buscamos las deducciones y aportes
					{
						$ls_codconc=$io_report->rs_data_detalle->fields["codconc"];
						$ls_nomcon=$io_report->rs_data_detalle->fields["nomcon"];
						$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->rs_data_detalle->fields["valsal"]));
						if ($ls_tipsal!="P2")
						{
							$li_totded=$li_totded+abs($io_report->rs_data_detalle->fields["valsal"]);
							$la_data[$li_s]=array('denominacion'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_valsal,'aporte'=>'');
						}
						else
						{
							$li_totapo=$li_totapo+abs($io_report->rs_data_detalle->fields["valsal"]);
							$la_data[$li_s]=array('denominacion'=>$ls_nomcon,'asignacion'=>'','deduccion'=>'','aporte'=>$li_valsal);
						}
					}	
					$li_s++;				
					$io_report->rs_data_detalle->MoveNext();
				}
				$la_data[$li_s]=array('denominacion'=>'','asignacion'=>'','deduccion'=>'','aporte'=>'');
				$li_s++;
				$la_data[$li_s]=array('denominacion'=>'He recibido conforme mi remuneración correspondiente al  período  antes','asignacion'=>'','deduccion'=>'','aporte'=>'');
				$li_s++;
				$la_data[$li_s]=array('denominacion'=>'indicado........................................................................................................','asignacion'=>'','deduccion'=>'','aporte'=>'');
				$li_s++;
				$la_data[$li_s]=array('denominacion'=>'','asignacion'=>'','deduccion'=>'','aporte'=>'');
				$li_s++;

				$li_totnet=$li_toting-$li_totded;
				$li_toting=$io_fun_nomina->uf_formatonumerico($li_toting);
				$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
				$li_totapo=$io_fun_nomina->uf_formatonumerico($li_totapo);
				$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);
				if ($ls_conceptop2!=1)
				{
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					uf_print_pie_cabecera1($li_toting,$li_totded,$li_totapo,$li_totnet,$ls_nomper,$ls_cedper,$io_pdf); // Imprimimos pie de la cabecera
				}
				else
				{
					uf_print_detalle_p2($la_data,$io_pdf); // Imprimimos el detalle 
					uf_print_pie_cabecera1_p2($li_toting,$li_totded,$li_totapo,$li_totnet,$ls_nomper,$ls_cedper,$io_pdf); // Imprimimos pie de la cabecera
				}
				uf_print_piecabecera($ls_obsrecper,$io_pdf);
				unset($la_data);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera*/
				if($li_i<$li_totrow)
				{
					$io_pdf->ezNewPage(); // Insertar una nueva página
					$li_reg=1;
				}
			}
			$li_i++;
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
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 