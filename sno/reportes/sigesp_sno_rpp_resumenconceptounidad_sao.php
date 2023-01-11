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
		// Fecha Creaci�n: 28/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". Para ".$as_desnom.". ".$as_periodo;
		if($ai_tipo==1)
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_resumenconceptounidad.php",$ls_descripcion,$ls_codnom);
		}
		else
		{
			$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte_nomina("SNO","sigesp_sno_r_hresumenconceptounidad.php",$ls_descripcion,$ls_codnom);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_desnom,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_periodo // Descripci�n del per�odo
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,720,11,$as_periodo); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(10,$as_desnom);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,710,10,$as_desnom); // Agregar el t�tulo
		$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_coduniadm,$as_desuniadm,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_coduniadm // C�digo de la unidad administrativa
		//	    		   as_desuniadm // Descripci�n de la unidad administrativa
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime la cabecera por unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Unidad Administrativa</b> '.$as_coduniadm.' - '.$as_desuniadm));
		$la_columnas=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 10,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>C�digo</b>',
						   'nombre'=>'<b>                                 Concepto</b>',
						   'asignacion'=>'<b>Asignaci�n    </b>',
						   'deduccion'=>'<b>Deducci�n     </b>',
						   'aporte'=>'<b>Aporte       Patronal     </b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>220), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piedetalle($ai_totalasignacion,$ai_totaldeduccion,$ai_totalaporte,$ai_total_neto,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piedetalle
		//		   Access: private 
		//	    Arguments: ai_totalasignacion // Total Asignaci�n
		//	   			   ai_totaldeduccion // Total Deduccci�n
		//	   			   ai_totalaporte // Total aporte
		//	   			   ai_total_neto // Total Neto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$la_data=array(array('totales'=>'<b>Totales Unidad '.$ls_bolivares.'</b>','asignacion'=>$ai_totalasignacion,'deduccion'=>$ai_totaldeduccion,
							 'aporte'=>$ai_totalaporte));
		$la_columna=array('totales'=>'','asignacion'=>'','deduccion'=>'','aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'shadeCol'=>array((249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255)), // Color de la sombra
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('totales'=>array('justification'=>'right','width'=>290), // Justificaci�n y ancho de la columna
						 			   'asignacion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'right','width'=>70), // Justificaci�n y ancho de la columna
						 			   'aporte'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>'<b>Neto Unidad '.$ls_bolivares.'</b> '.$ai_total_neto));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('name'=>array('justification'=>'center'))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci�n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_piedetalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_totasi,$ai_totded,$ai_totapo,$ai_totgeneral,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_totasi // Total de Asignaciones
		//	   			   ai_totded // Total de Deducciones
		//	   			   ai_totapo // Total de Aportes
		//	   			   ai_totgeneral // Total de Neto a Pagar
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por todos los registros
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 28/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;		
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>500); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('asignacion'=>'<b>Asignaciones: </b>'.$ai_totasi,'deduccion'=>'<b>Deducciones: </b>'.$ai_totded,
							 'aporte'=>'<b>Aportes: </b>'.$ai_totapo));
		$la_columna=array('asignacion'=>'','deduccion'=>'','aporte'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>450, // Ancho de la tabla
						 'maxWidth'=>450, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('asignacion'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'deduccion'=>array('justification'=>'center','width'=>150), // Justificaci�n y ancho de la columna
						 			   'aporte'=>array('justification'=>'center','width'=>150))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('name'=>'<b>Neto a Pagar '.$ls_bolivares.': </b>','total'=>$ai_totgeneral));
		$la_columna=array('name'=>'','total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize'=> 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('name'=>array('justification'=>'right','width'=>250), // Justificaci�n y ancho de la columna
						 			   'total'=>array('justification'=>'left','width'=>250))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_numsol // numero de la solicitud
		//	    		   ad_fecregsol // fecha de registro de la solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creaci�n: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		// cuadro inferior
        $io_pdf->Rectangle(15,60,570,70);
		$io_pdf->line(15,60,15,140);//vertical
		$io_pdf->line(585,60,585,140);//vertical
		
		//$io_pdf->line(15,71,585,71);//horizontal
		//$io_pdf->line(15,80,585,80);//horizontal		
		$io_pdf->line(15,120,585,120);//horizontal
		$io_pdf->line(15,140,585,140);//horizontal		
		$io_pdf->line(130,60,130,130);//vertical		
		$io_pdf->line(285,60,285,130);//vertical		
		$io_pdf->line(445,60,445,130);//vertical		
		$io_pdf->addText(500,132,7,"Fecha    ".date("d/m/Y")); // Agregar el t�tulo
		$io_pdf->addText(40,122,7,"Elaborado Por:"); // Agregar el t�tulo
		$io_pdf->addText(38,77,7,"Gustavo A. Oviedo"); // Agregar el t�tulo
		$io_pdf->addText(32,70,7,"Analista de la Unidad de"); // Agregar el t�tulo
		$io_pdf->addText(37,63,7,"Recursos Humanos"); // Agregar el t�tulo
		
		$io_pdf->addText(190,122,7,"Revisado:"); // Agregar el t�tulo
		$io_pdf->addText(170,71,7,"Lcda. Juana Puertas"); // Agregar el t�tulo
		$io_pdf->addText(140,64,7,"Jefe(E) de Recursos Humanos del SAO"); // Agregar el t�tulo
		
		$io_pdf->addText(340,122,7,"Autorizado Por:"); // Agregar el t�tulo
		$io_pdf->addText(327,77,7,"Dra. Gloria B. Soler A."); // Agregar el t�tulo
		$io_pdf->addText(305,70,7,"Presidenta del Servicio Desconcentrado"); // Agregar el t�tulo
                $io_pdf->addText(320,63,7,"Oncologico del Estado Lara"); // Agregar el t�tulo 

		//$io_pdf->addText(440,122,7,"Autorizaci�n Administrativa"); // Agregar el t�tulo
		$io_pdf->addText(480,63,7,"<b>Vo.Bo. Administraci�n</b>"); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina

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
			$ls_bolivares ="Bs.";
			break;

		case "1":
			if($_SESSION["la_nomina"]["tiponomina"]=="NORMAL")
			{
				require_once("sigesp_sno_class_reportbsf.php");
				$io_report=new sigesp_sno_class_reportbsf();
				$li_tipo=1;
			}
			if($_SESSION["la_nomina"]["tiponomina"]=="HISTORICA")
			{
				require_once("sigesp_sno_class_report_historicobsf.php");
				$io_report=new sigesp_sno_class_report_historicobsf();
				$li_tipo=2;
			}	
			$ls_bolivares ="Bs.F.";
			break;
	}
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_desnom=$_SESSION["la_nomina"]["desnom"];
	$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
	$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
	$ld_fechasper=$io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
	$ls_titulo="<b>Resumen de Conceptos</b>";
	$ls_periodo="<b>Per�odo Nro ".$ls_peractnom.", ".$ld_fecdesper." - ".$ld_fechasper."</b>";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codconcdes=$io_fun_nomina->uf_obtenervalor_get("codconcdes","");
	$ls_codconchas=$io_fun_nomina->uf_obtenervalor_get("codconchas","");
	$ls_coduniadm=$io_fun_nomina->uf_obtenervalor_get("coduniadm","");
	$ls_conceptocero=$io_fun_nomina->uf_obtenervalor_get("conceptocero","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	$ls_subnomdes=$io_fun_nomina->uf_obtenervalor_get("subnomdes","");
	$ls_subnomhas=$io_fun_nomina->uf_obtenervalor_get("subnomhas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ls_desnom,$ls_periodo,$li_tipo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_resumenconceptounidad_unidad($ls_codconcdes,$ls_codconchas,$ls_coduniadm,$ls_conceptocero,$ls_subnomdes,$ls_subnomhas); // Cargar el DS con los datos de la cabecera del reporte
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
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$ls_desnom,$ls_periodo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
		$li_totrow=$io_report->DS->getRowCount("minorguniadm");
		$li_totasignacion=0;
		$li_totdeduccion=0;
		$li_totaporte=0;		
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
	        $io_pdf->transaction('start'); // Iniciamos la transacci�n
			$li_numpag=$io_pdf->ezPageCount; // N�mero de p�gina
			$ls_minorguniadm=$io_report->DS->data["minorguniadm"][$li_i];
			$ls_ofiuniadm=$io_report->DS->data["ofiuniadm"][$li_i];
			$ls_uniuniadm=$io_report->DS->data["uniuniadm"][$li_i];
			$ls_depuniadm=$io_report->DS->data["depuniadm"][$li_i];
			$ls_prouniadm=$io_report->DS->data["prouniadm"][$li_i];
			$ls_coduniadm=$ls_minorguniadm."-".$ls_ofiuniadm."-".$ls_uniuniadm."-".$ls_depuniadm."-".$ls_prouniadm;
			$ls_desuniadm=$io_report->DS->data["desuniadm"][$li_i];
			uf_print_cabecera($ls_coduniadm,$ls_desuniadm,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_resumenconceptounidad_concepto($ls_codconcdes,$ls_codconchas,$ls_coduniadm,$ls_conceptocero,$ls_subnomdes,$ls_subnomhas,$ls_orden); // Obtenemos el detalle del reporte
			$li_totasi=0;
			$li_totded=0;
			$li_totapo=0;
			if($lb_valido)
			{
				$li_totrow_res=$io_report->DS_detalle->getRowCount("codconc");
				for($li_s=1;$li_s<=$li_totrow_res;$li_s++)
				{
					$ls_codconc=$io_report->DS_detalle->data["codconc"][$li_s];
					$ls_nomcon=$io_report->DS_detalle->data["nomcon"][$li_s];
					$ls_tipsal=rtrim($io_report->DS_detalle->data["tipsal"][$li_s]);
					$li_monto=$io_fun_nomina->uf_formatonumerico(abs($io_report->DS_detalle->data["monto"][$li_s]));
					switch($ls_tipsal)
					{
						case "A": // Asignaci�n
							$li_totasi=$li_totasi+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>$li_monto,'deduccion'=>'','aporte'=>'');
							break;
		
						case "V1": // Asignaci�n
							$li_totasi=$li_totasi+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>$li_monto,'deduccion'=>'','aporte'=>'');
							break;
		
						case "W1": // Asignaci�n
							$li_totasi=$li_totasi+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>$li_monto,'deduccion'=>'','aporte'=>'');
							break;
		
						case "D": // Deducci�n
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "V2": // Deducci�n
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "W2": // Deducci�n
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "P1": // Aporte Empleado
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "V3": // Aporte Empleado
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "W3": // Aporte Empleado
							$li_totded=$li_totded+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>$li_monto,'aporte'=>'');
							break;
		
						case "P2": // Aporte Patr�n
							$li_totapo=$li_totapo+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>'','aporte'=>$li_monto);
							break;
		
						case "V4": // Aporte Patr�n
							$li_totapo=$li_totapo+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>'','aporte'=>$li_monto);
							break;
		
						case "W4": // Aporte Patr�n
							$li_totapo=$li_totapo+abs($io_report->DS_detalle->data["monto"][$li_s]);
							$la_data[$li_s]=array('codigo'=>$ls_codconc,'nombre'=>$ls_nomcon,'asignacion'=>'','deduccion'=>'','aporte'=>$li_monto);
							break;
					}
				}
				$io_report->DS_detalle->resetds("codconc");
  			    uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle  
				$li_totnet=$li_totasi-$li_totded;
				$li_totasignacion=$li_totasignacion+$li_totasi;
				$li_totdeduccion=$li_totdeduccion+$li_totded;
				$li_totaporte=$li_totaporte+$li_totapo;		
				$li_totasi=$io_fun_nomina->uf_formatonumerico($li_totasi);
				$li_totded=$io_fun_nomina->uf_formatonumerico($li_totded);
				$li_totapo=$io_fun_nomina->uf_formatonumerico($li_totapo);
				$li_totnet=$io_fun_nomina->uf_formatonumerico($li_totnet);
				uf_print_piedetalle($li_totasi,$li_totded,$li_totapo,$li_totnet,$io_pdf); // Imprimimos el pie de la cabecera
				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				}
				else
				{// Hacemos un rollback de los registros, agregamos una nueva p�gina y volvemos a imprimir
					$io_pdf->transaction('rewind');
					$io_pdf->ezNewPage(); // Insertar una nueva p�gina
					uf_print_cabecera($ls_coduniadm,$ls_desuniadm,$io_pdf); // Imprimimos la cabecera del registro
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					uf_print_piedetalle($li_totasi,$li_totded,$li_totapo,$li_totnet,$io_pdf); // Imprimimos el pie del detalle
				}
			}
			unset($la_data);
		}
		$li_totneto=$li_totasignacion-$li_totdeduccion;
		$li_totasignacion=$io_fun_nomina->uf_formatonumerico($li_totasignacion);
		$li_totdeduccion=$io_fun_nomina->uf_formatonumerico($li_totdeduccion);
		$li_totaporte=$io_fun_nomina->uf_formatonumerico($li_totaporte);
		$li_totneto=$io_fun_nomina->uf_formatonumerico($li_totneto);
		uf_print_piecabecera($li_totasignacion,$li_totdeduccion,$li_totaporte,$li_totneto,$io_pdf); // Imprimimos el pie de la cabecera
		uf_firmas($io_pdf);
		$io_report->DS->resetds("minorguniadm");
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else // Si hubo alg�n error
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
