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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/03/2018 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". ";
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_historicocargos.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/03/2018 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t�tulo
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codcar,$as_descar,$as_cargo,$io_cabecera,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    		   io_cabecera // objeto cabecera
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime la cabecera por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/03/2018 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(50,690,500,$io_pdf->getFontHeight(14));
        $io_pdf->setColor(0,0,0);
		$io_pdf->addText(55,695,11,'<b>'.$as_cargo.' </b>  '.$as_codcar.' - '.$as_descar.''); // Agregar el t�tulo
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
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
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/03/2018 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('fecha'=>'<b>Fecha</b>',
						   'codigo'=>'<b>    C�digo</b>',
						   'nombre'=>'<b>                  Apellidos y Nombres</b>',
						   'nomina'=>'<b>                            Nomina</b>');
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
						 'cols'=>array('fecha'=>array('justification'=>'center','width'=>60, // Justificaci�n y ancho de la columna
						 			   'codigo'=>array('justification'=>'center','width'=>80), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>180), // Justificaci�n y ancho de la columna
						 			   'nomina'=>array('justification'=>'left','width'=>180)))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codcardes=$io_fun_nomina->uf_obtenervalor_get("codcardes","");
	$ls_codcarhas=$io_fun_nomina->uf_obtenervalor_get("codcarhas","");
	$ls_codasicardes=$io_fun_nomina->uf_obtenervalor_get("codasicardes","");
	$ls_codasicarhas=$io_fun_nomina->uf_obtenervalor_get("codasicarhas","");
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Historico de Cargos</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_historicocargos_cargos($ls_codcardes,$ls_codcarhas,$ls_codasicardes,$ls_codasicarhas); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n�mero de p�gina
		while ((!$io_report->rs_data->EOF) &&($lb_valido))
		{
			$ls_cargo=$io_report->rs_data->fields["tipo"];
			$ls_codasicar='';
			if($ls_cargo=='Cargo')
			{
				$ls_codcar=$io_report->rs_data->fields["codcar"];
				$ls_descar=$io_report->rs_data->fields["descar"];			
			}
			else
			{
				$ls_codcar=$io_report->rs_data->fields["codasicar"];
				$ls_descar=$io_report->rs_data->fields["desasicar"];
				$ls_codasicar=$io_report->rs_data->fields["codasicar"];			
			}
			
			$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
			uf_print_cabecera($ls_codcar,$ls_descar,$ls_cargo,$io_cabecera,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_historicocargos_personal($ls_codcar,$ls_codasicar); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$li_i=0;
				while ((!$io_report->rs_detalle->EOF) && ($lb_valido))
				{
					$ld_fecmov=$io_funciones->uf_convertirfecmostrar($io_report->rs_detalle->fields["fecmov"]);
					$ls_codper=$io_report->rs_detalle->fields["codper"];
					$ls_apenomper=$io_report->rs_detalle->fields["apeper"].", ".$io_report->rs_detalle->fields["nomper"];
					$ls_nomina=$io_report->rs_detalle->fields["codnom"]." ".$io_report->rs_detalle->fields["desnom"];
					$la_data[$li_i]=array('fecha'=>$ld_fecmov,'codigo'=>$ls_codper,'nombre'=>$ls_apenomper,'nomina'=>$ls_nomina);
					$io_report->rs_detalle->MoveNext();
					$li_i++;
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				unset($la_data);
			}		
			$io_report->rs_data->MoveNext();
			$io_pdf->stopObject($io_cabecera); // Detener el objeto cabecera
			if(!$io_report->rs_data->EOF)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva p�gina
			}
			unset($io_cabecera);
		}
		if($lb_valido) // Si no ocurrio ning�n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg�n error
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