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
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Arreglo de las variables de seguridad
		//	    		   as_desnom // Arreglo de las variables de seguridad
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Gener� el Reporte Consolidado ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_recibopago.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$ls_nomepm=$_SESSION["la_empresa"]["titulo"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$io_pdf->addText(40,760,10,'<b><i>'.$ls_nomepm.'</b></i>'); // Agregar el t�tulo
		$io_pdf->addText(40,750,9,'<b><i>'.$ls_nombre.'</b></i>'); // Agregar el t�tulo
		$io_pdf->addText(120,750,9,'<i>'.$ls_rifemp.'</i>'); // Agregar el t�tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,9,$as_titulo); // Agregar el t�tulo
		
		$li_tm=$io_pdf->getTextWidth(9,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,705,9,$as_periodo); // Agregar el t�tulo
		
		$li_tm=$io_pdf->getTextWidth(9,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,718,9,$as_desnom); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina1
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codper,$as_cedper,$as_nomper,$ad_fecingper,$as_codcueban,$as_descar,$as_desunidad,$ai_sueper,$ai_sueintper,$io_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cedper // C�dula del personal
		//	    		   as_nomper // Nombre del personal
		//	    		   as_descar // Decripci�n del cargo
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Objeto PDF
		//    Description: funci�n que imprime la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$ai_sueper_ext=$ai_sueper;
		$ai_sueper=($ai_sueper/30);
		$ai_sueper=number_format($ai_sueper,2,",",".");
		$ai_sueper_ext=number_format($ai_sueper_ext,2,",",".");
		$io_pdf->addText(50,700,8,"Sueldo Bs.   ".$ai_sueper_ext); // Agregar el t�tulo
		$io_pdf->addText(470,700,8,"Sueldo Diario Bs.   ".$ai_sueper); // Agregar el t�tulo
		$io_pdf->ezSety(700);
		$la_data[1]=array('codigo'=>'C�digo:', 'cedula'=>'C�dula:', 'nombre'=>'Apellidos y Nombres:', 'fecha'=>'Fecha Ingreso', 'cta'=>'Cta. Bancaria');
		$la_data[2]=array('codigo'=>$as_codper, 'cedula'=>$as_cedper, 'nombre'=>$as_nomper, 'fecha'=>$ad_fecingper, 'cta'=>$as_codcueban);
		$la_columna=array('codigo'=>'', 'cedula'=>'', 'nombre'=>'', 'fecha'=>'', 'cta'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'left','width'=>80), // Justificaci�n y ancho de la columna
						 			   'cedula'=>array('justification'=>'left','width'=>80), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>190), // Justificaci�n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
									   'cta'=>array('justification'=>'left','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('cargo'=>'Cargo:', 'dpto'=>'Departamento:');
		$la_data[2]=array('cargo'=>$as_descar, 'dpto'=>$as_desunidad);
		$la_columna=array('cargo'=>'', 'dpto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'xPos'=>315,
						 'fontSize' => 7, // Tama�o de Letras
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('cargo'=>array('justification'=>'left','width'=>270), // Justificaci�n y ancho de la columna
						 			   'dpto'=>array('justification'=>'left','width'=>260))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$la_data2,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$ls_data_t=array();
		$la_columna=array('codigoasig'=>'<b>CODIGO</b>',
						  'denomasig'=>'<b>DENOMINACION</b>',
						  'valorasig'=>'<b>ASIGNACION</b>',
						  'valordedu'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'rowGap' =>0.5,
						 'cols'=>array('codigoasig'=>array('justification'=>'left','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomasig'=>array('justification'=>'left','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>160), // Justificaci�n y ancho de la columna
									   'valordedu'=>array('justification'=>'right','width'=>120))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($ls_data_t,$la_columna,'',$la_config);
		$la_columna=array('codigoasig'=>'<b>CODIGO</b>',
						  'denomasig'=>'<b>DENOMINACION</b>',
						  'valorasig'=>'<b>ASIGNACION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'rowGap' =>0.5,
						 'cols'=>array('codigoasig'=>array('justification'=>'left','width'=>80), // Justificaci�n y ancho de la columna
						 			   'denomasig'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'left','width'=>150))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_columna=array('codigodedu'=>'<b>CODIGO</b>',
						  'denomdedu'=>'<b>DENOMINACION</b>',
						  'valordedu'=>'<b>DEDUCCION</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'rowGap' =>0.5,
						 'cols'=>array('codigodedu'=>array('justification'=>'left','width'=>80), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'left','width'=>300), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>150))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data2,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_toting,$ai_totded,$ai_totnet,$as_codcueban,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_toting // Total Ingresos
		//	   			   ai_totded // Total Deducciones
		//	   			   ai_totnet // Total Neto
		//	   			   as_codcueban // Codigo cuenta bancaria
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares,  $ls_tiporeporte;
		
		$io_piepagina=$io_pdf->openObject(); // Creamos el objeto pie de p�gina
		$io_pdf->saveState();
		$io_pdf->ezSetDy(-5);
		$la_data=array(array('linea'=>'__________________________________________'));
		$la_columna=array('linea'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'cols'=>array('linea'=>array('justification'=>'right','width'=>530))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('denomasig'=>'<b></b>', 'valorasig'=>$ai_toting, 'denomdedu'=>'<b> </b>','valordedu'=>$ai_totded));
		$la_columna=array('denomasig'=>'<b>DENOMINACI�N</b>',
						  'denomdedu'=>'<b>DENOMINACI�N</b>',
						  'valorasig'=>'<b>ASIGNACI�N</b>',
						  'valordedu'=>'<b>DEDUCCI�N</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'cols'=>array('denomasig'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'denomdedu'=>array('justification'=>'right','width'=>180), // Justificaci�n y ancho de la columna
						 			   'valorasig'=>array('justification'=>'right','width'=>150), // Justificaci�n y ancho de la columna
						 			   'valordedu'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezSetDy(-20);
		$la_data=array(array('linea'=>'_________________________________                                                         ========================================'));
		$la_columna=array('linea'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'cols'=>array('linea'=>array('justification'=>'right','width'=>530))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data=array(array('linea'=>'             RECIBE CONFORME                                                                            '.'<b>Total a Cobrar:</b>'.'                          '.$ai_totnet));
		$la_columna=array('linea'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 7,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M�ximo de la tabla
						 'xPos'=>315, // Orientaci�n de la tabla
						 'cols'=>array('linea'=>array('justification'=>'left','width'=>530)));// Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_piepagina,'all');
		$io_pdf->stopObject($io_piepagina); // Detener el objeto pie de p�gina
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_snorh_class_report.php");
			$io_report=new sigesp_snorh_class_report();
			$ls_bolivares ="Bs.";
			break;

		case "1":
			require_once("sigesp_snorh_class_reportbsf.php");
			$io_report=new sigesp_snorh_class_reportbsf();
			$ls_bolivares ="Bs.F.";
			break;
	}
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../base/librerias/php/phpMailer_v2.1/class.phpmailer.php"); 				 
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$ls_desnom="<b>".$io_fun_nomina->uf_obtenervalor_get("desnom","")."</b>";
	$ls_codperides=$io_fun_nomina->uf_obtenervalor_get("codperides","");
	$ls_codperihas=$io_fun_nomina->uf_obtenervalor_get("codperihas","");
	$ld_fecdesper=$io_fun_nomina->uf_obtenervalor_get("fecdesper","");
	$ld_fechasper=$io_fun_nomina->uf_obtenervalor_get("fechasper","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_coduniadmdes=$io_fun_nomina->uf_obtenervalor_get("coduniadmdes","");
	$ls_coduniadmhas=$io_fun_nomina->uf_obtenervalor_get("coduniadmhas","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_conceptop2=$io_fun_nomina->uf_obtenervalor_get("conceptop2","");
	$ls_conceptoreporte=$io_fun_nomina->uf_obtenervalor_get("conceptoreporte","");
	$ls_tituloconcepto=$io_fun_nomina->uf_obtenervalor_get("tituloconcepto","");
	$ls_quincena=$io_fun_nomina->uf_obtenervalor_get("quincena","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("codsubnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("codsubnomhas","");
	$ls_consolidar=$io_fun_nomina->uf_obtenervalor_get("consolidar","0");
	$ls_codubifisdes=$io_fun_nomina->uf_obtenervalor_get("codubifisdes","");
	$ls_codubifishas=$io_fun_nomina->uf_obtenervalor_get("codubifishas","");	
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>RECIBO DE PAGO</b>";
	$ls_periodo="";
	if($ls_consolidar=="1")
	{
		$ls_periodo="Periodos: <b>".$ls_codperides." - ".$ls_codperihas."</b> del <b>".$ld_fecdesper."</b> al <b>".$ld_fechasper."</b>";
	}
	$ls_quincena=3;
	//--------------------------------------------------------------------------------------------------------------------------------
	//-- Se eliminan todos los recibos de pago generados anteriormente
	$ls_ruta = '../txt/recibo_pago';
	$lista = array();
	$handle = opendir($ls_ruta);
	while (false!==$file = readdir($handle))
	{
		if(($file != '.') && ($file != '..'))
		{
			@unlink($ls_ruta.'/'.$file);
		}
	}
	closedir($handle);
        
        $lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_recibopago_personal($ls_codnom,$ld_fecdesper,$ld_fechasper,$ls_codperdes,$ls_codperhas,$ls_coduniadmdes,$ls_coduniadmhas,
													  $ls_conceptocero,$ls_conceptop2,$ls_conceptoreporte,$ls_subnomdes,$ls_subnomhas,
													  $ls_consolidar,$ls_orden,$ls_codubifisdes,$ls_codubifishas); // Cargar el DS con los datos de la cabecera del reporte*/
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		set_time_limit(1800);
                $li_totrow=$io_report->rs_data->RecordCount();
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
                        $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
                        $io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
                        $io_pdf->ezSetCmMargins(3,1,1,2); // Configuraci�n de los margenes en cent�metros
                        uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
			$li_toting=0;
			$li_totded=0;
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_descar=$io_report->rs_data->fields["descar"];
			$ls_codcueban=$io_report->rs_data->fields["codcueban"];
			$li_total=$io_report->rs_data->fields["total"];
			$li_adelanto=$io_report->rs_data->fields["adenom"];
			$li_racnom=$io_report->rs_data->fields["racnom"];
			$ls_desunidad=$io_report->rs_data->fields["desuniadm"];
			$ld_fecingper=$io_report->rs_data->fields["fecingper"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($ld_fecingper);
			$li_sueper=$io_report->rs_data->fields["sueper"];	
			$li_sueintper=$io_report->rs_data->fields["sueintper"];	  
			if($li_racnom==1)
			{
				$ls_descar=$io_report->rs_data->fields["denasicar"];
			}
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			uf_print_cabecera($ls_codper,$ls_cedper,$ls_nomper,$ld_fecingper,$ls_codcueban,$ls_descar,$ls_desunidad,$li_sueper,$li_sueintper,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
			$ls_codperi="";
			if($ls_consolidar=="0")
			{
				$ls_codperi=$io_report->rs_data->fields["codperi"];
				$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecdesper"]);
				$ld_fechasper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fechasper"]);
				$ls_periodo="Periodo: <b>".$ls_codperi."</b> del <b>".$ld_fecdesper."</b> al <b>".$ld_fechasper."</b>";
				if($li_reg==1)
				{
					$li_tm=$io_pdf->getTextWidth(9,$ls_periodo);
					$tm=306-($li_tm/2);
					$io_pdf->addText($tm,720,9,$ls_periodo); // Agregar el t�tulo
				}
				else
				{
					$li_tm=$io_pdf->getTextWidth(9,$ls_periodo);
					$tm=306-($li_tm/2);
					$io_pdf->addText($tm,300,9,$ls_periodo); // Agregar el t�tulo
				}
			}
			$lb_valido=$io_report->uf_recibopago_conceptopersonal($ls_codnom,$ld_fecdesper,$ld_fechasper,$ls_codper,
                                                                              $ls_conceptocero,$ls_conceptop2,$ls_conceptoreporte,
                                                                              $ls_tituloconcepto,$ls_codperi); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_totrow_det=$io_report->DS_detalle->getRowCount("codconc");
				$li_asig=0;
				$li_dedu=0;
				if($li_adelanto==1)// Utiliza el adelanto de quincena
				{
					switch($ls_quincena)
					{
						case "1": // primera quincena;
							$li_asig=$li_asig+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_toting=$li_toting+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "2": // segunda quincena;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
							{
								$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
								if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									if ($ls_tipsal!="R")
									{
										$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								else // Buscamos las deducciones y aportes
								{
									$li_dedu=$li_dedu+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
							}
							$li_dedu=$li_dedu+1;
							$ls_codconc="----------";
							$ls_nomcon="ADELANTO 1ra QUINCENA";
							$li_valsal=round($li_total/2,2);
							$li_totded=$li_totded+$li_valsal;
							$li_valsal=$io_fun_nomina->uf_formatonumerico($li_valsal);
							$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
							break;
							
						case "3": // Mes Completo;
							for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
							{
								$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
								if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
								{
									$li_asig=$li_asig+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									if ($ls_tipsal!="R")
									{
										$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									}
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
								else // Buscamos las deducciones y aportes
								{
									$li_dedu=$li_dedu+1;
									$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
									$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
									$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
									$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
									$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
								}
							}
							break;
					}
				}
				else// No utiliza adelanto de quincena
				{
					for($li_s=1;$li_s<=$li_totrow_det;$li_s++) 
					{
						$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
						if(($ls_tipsal=="A") || ($ls_tipsal=="V1") || ($ls_tipsal=="V2") || ($ls_tipsal=="R")) // Buscamos las asignaciones
						{
							$li_asig=$li_asig+1;
							$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
							$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
							if ($ls_tipsal!="R")
							{
								$li_toting=$li_toting+abs($io_report->DS_detalle->data["valsal"][$li_s]);
							}
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$la_data_a[$li_asig]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
						else // Buscamos las deducciones y aportes
						{
							$li_dedu=$li_dedu+1;
							$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
							$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["valsal"][$li_s]);
							$li_valsal=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["valsal"][$li_s]));
							$la_data_d[$li_dedu]=array('codigo'=>$ls_codconc,'denominacion'=>$ls_nomcon,'valor'=>$li_valsal);
						}
					}
				}
				if($li_asig<=$li_dedu)
				{
					$li_total=$li_dedu;
				}
				else
				{
					$li_total=$li_asig;
				}
				for($li_s=1;$li_s<=$li_dedu;$li_s++) 
				{
					$la_valores["denomdedu"]="";
					$la_valores["valordedu"]="";
					if($li_s<=$li_dedu)
					{
						$la_valores_d["codigodedu"]=$la_data_d[$li_s]["codigo"];
						$la_valores_d["denomdedu"]=$la_data_d[$li_s]["denominacion"];
						$la_valores_d["valordedu"]=$la_data_d[$li_s]["valor"];
					}
					$la_data2[$li_s]=$la_valores_d;
				}
				for($li_s=1;$li_s<=$li_asig;$li_s++) 
				{
					$la_valores["denomasig"]="";
					$la_valores["valorasig"]="";
					if($li_s<=$li_asig)
					{
						$la_valores_a["codigoasig"]=$la_data_a[$li_s]["codigo"];
						$la_valores_a["denomasig"]=$la_data_a[$li_s]["denominacion"];
						$la_valores_a["valorasig"]=$la_data_a[$li_s]["valor"];
					}
					$la_data[$li_s]=$la_valores_a;
				}
				if($li_dedu==0)
				{
					$la_data2=array();
				}
				uf_print_detalle($la_data,$la_data2,$io_pdf); // Imprimimos el detalle 
				$li_totnet=$li_toting-$li_totded;
				$li_toting=$io_fun_nomina->uf_formatonumerico($li_toting);
				$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
				$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);
				uf_print_pie_cabecera($li_toting,$li_totded,$li_totnet,$ls_codcueban,$io_pdf); // Imprimimos pie de la cabecera
				$io_report->DS_detalle->resetds("codconc");
				unset($la_data_a);
				unset($la_data_d);
				unset($la_data);
				unset($la_data2);
				$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
				$pdfcode = $io_pdf->ezOutput();
				if($ls_consolidar=="1")
				{
					$fp=fopen($ls_ruta.'/Recibo_Pago_'.$ls_codper.'.pdf','wb');
				}
				else
				{
					$fp=fopen($ls_ruta.'/Recibo_Pago_'.$ls_codper.'_'.$ls_codperi.'.pdf','wb');
				}
				fwrite($fp,$pdfcode);
				fclose($fp);
			}
			unset($io_pdf);
						
			$io_mail=new PHPMailer();  
			$io_mail->IsSMTP(true); 
			$io_mail->IsHTML(true);			
			
			if ($ls_coreleper!="")
			{
				$ls_servidor='';
				$ls_puerto='';
				$ls_remitente='';
				$arrResultado=$io_report->uf_buscar_datos_correo($ls_servidor,$ls_puerto,$ls_remitente);
				$ls_servidor=$arrResultado['as_serv'];
				$ls_puerto=$arrResultado['as_port'];
				$ls_remitente=$arrResultado['as_remitente'];
				$lb_valido=$arrResultado['lb_valido'];
				if (($lb_valido)&&($ls_servidor!="")&&($ls_puerto!="")&&($ls_remitente!=""))
				{
					$io_mail->Host = $ls_servidor;
					$io_mail->Port = $ls_puerto;
					$io_mail->From = $ls_remitente;
					$io_mail->FromName = "Recibo de Pago";
					$io_mail->Subject = "Recibo de Pago";
					$io_mail->AddAddress($ls_coreleper,$ls_nomper);
					$body  = " Le estoy remitiendo  <strong>".$ls_nomper."</strong> su Recibo de Pago correspondiente a la N�mina ";
					$body .= " <strong>".$ls_desnom."</strong> de ".$ls_periodo;
					$io_mail->Body = $body;
					if($ls_consolidar=="1")
					{
						$io_mail->AddAttachment($ls_ruta.'/Recibo_Pago_'.$ls_codper.'.pdf', 'Recibo_Pago_'.$ls_codper.'.pdf');
					}
					else
					{
						$io_mail->AddAttachment($ls_ruta.'/Recibo_Pago_'.$ls_codper.'.pdf', 'Recibo_Pago_'.$ls_codper.'_'.$ls_codperi.'.pdf');
					}
					if(!$io_mail->Send())
					{
						print("<script language=JavaScript>");
						print(" alert('Ocurrio un error al enviarle el Recibo de Pago a ".$ls_nomper." ERROR->".$io_mail->ErrorInfo."');");
						print("</script>");
					}
					else
					{
						print("<script language=JavaScript>");
						print(" alert('Recibo de Pago Enviado a ".$ls_nomper."');");
						print("</script>");			
					}
					unset($io_mail);
				}
				else
				{
					$lb_valido=false;
					print("<script language=JavaScript>");
					print(" alert('Error en la Configuraci�n de los Datos del Correo de la Empresa.');");
					print("</script>");
				}
			}
			else
			{
					print("<script language=JavaScript>");
					print(" alert('La persona ".$ls_codper." - ".$ls_nomper." no tiene cuenta de correo asociada.');");
					print("</script>");
			}
			$io_report->rs_data->MoveNext();
		}
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
	print("<script language=JavaScript>");
	print(" close();");
	print("</script>");
?> 