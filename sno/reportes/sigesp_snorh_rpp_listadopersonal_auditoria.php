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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="eneró el reporte de Personal Auditoria ";
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonal_auditoria.php",$ls_descripcion);
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
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_bolivares;
		
		$ld_hoy=date('d/m/Y');
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		//----------------------------------------------Primer Encabezado-------------------------------------------------------------
		$io_pdf->addText(95,550,8,"<b>ENTE:</b>");
		$io_pdf->addText(120,550,8,$_SESSION["la_empresa"]["nombre"]); 
		$io_pdf->addText(55,540,8,"<b>RESPONSABLE:</b>");
		$io_pdf->addText(120,540,8,$_SESSION["la_empresa"]["nomrep"]); 
		$io_pdf->addText(83,530,8,"<b>CEDULA:</b>");
		$io_pdf->addText(120,530,8,$_SESSION["la_empresa"]["cedrep"]); 
		$io_pdf->addText(20,520,8,"<b>CORREO ELECTRONICO:</b>");
		$io_pdf->addText(120,520,8,$_SESSION["la_empresa"]["email"]); 
		$io_pdf->addText(72,510,8,"<b>TELEFONO:</b>");
		$io_pdf->addText(120,510,8,$_SESSION["la_empresa"]["telrep"]); 
		$io_pdf->addText(70.54,500,8,"<b>UBICACION:</b>");
		$io_pdf->addText(120,500,8,$_SESSION["la_empresa"]["direccion"]); 
		$io_pdf->addText(88,490,8,"<b>FECHA:</b>");
		$io_pdf->addText(120,490,8,$ld_hoy); 
		
		$li_tm=$io_pdf->getTextWidth(8,$as_titulo);		
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,560,8,"<b>".$as_titulo."</b>");
		//----------------------------------------------Segundo Encabezado----------------------------------------------------------

		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-10);
		$la_columna=array('campo1'=>'',
						  'campo2'=>'',
						  'campo3'=>'',
						  'campo4'=>'',
						  'campo5'=>'',
						  'campo6'=>'',
						  'campo7'=>'',
						  'campo8'=>'');

		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 6,  // Tamaño de Letras de los títulos
						 'showLines'=>2, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'xPos'=>505, // Ancho de la tabla
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('campo1'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'campo2'=>array('justification'=>'left','width'=>137), // Justificación y ancho de la columna
						 			   'campo3'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'campo4'=>array('justification'=>'left','width'=>137), // Justificación y ancho de la columna
						 			   'campo5'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'campo6'=>array('justification'=>'left','width'=>137), // Justificación y ancho de la columna
						 			   'campo7'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'campo8'=>array('justification'=>'left','width'=>137))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="AUDITORIA PERSONAL";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_discapacitado=$io_fun_nomina->uf_obtenervalor_get("discapacitado","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad(); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_seleccionar_personal_auditoria($ls_codperdes,$ls_codperhas,$ls_discapacitado,$ls_orden); 
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
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
		$li_i=0;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$li_j=0;
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			if ($ls_nacper=="V")
			{
				$ls_nacper="VENEZOLANA";
			}
			else
			{
				$ls_nacper="EXTRANJERO";
			}
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nombre=$io_report->rs_data->fields["nomper"].", ".$io_report->rs_data->fields["apeper"];
			$ls_sexper=$io_report->rs_data->fields["sexper"];
			$ls_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecnacper"]);
			$ls_codtippersss=$io_report->rs_data->fields["codtippersss"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$ls_telmovper=$io_report->rs_data->fields["telmovper"];
			$ls_telhabper=$io_report->rs_data->fields["telhabper"];
			$ls_dirper=$io_report->rs_data->fields["dirper"];
			$ls_coreleper=$io_report->rs_data->fields["coreleper"];
			$ls_coreleins=$io_report->rs_data->fields["coreleins"];
			$ls_tienedis=$io_report->rs_data->fields["tienedis"];
			if ($ls_tienedis=="0")
			{
				$ls_tienedis="NO";
			}
			else
			{
				$ls_tienedis="SI";
			}						
			$ls_desdis=$io_report->rs_data->fields["desdis"];
			$ls_nrocardis=$io_report->rs_data->fields["nrocardis"];
			$ls_contraorg=$io_report->rs_data->fields["contraorg"];
			if ($ls_contraorg=="0")
			{
				$ls_contraorg="NO";
			}
			else
			{
				$ls_contraorg="SI";
			}			
			$ls_talzapper=$io_report->rs_data->fields["talzapper"];
			$ls_talcamper=$io_report->rs_data->fields["talcamper"];
			$ls_talpanper=$io_report->rs_data->fields["talpanper"];
			$ls_obsper=$io_report->rs_data->fields["obsper"];
			$ls_carantper=$io_report->rs_data->fields["carantper"];
			$arrResultado=$io_report->uf_seleccionar_personal_auditoria_hijo($ls_codper);
			$li_nrohijos=$arrResultado["nrohijos"];
			$li_nrohijosescolar=$arrResultado["nrohijosescolar"];
			unset($arrResultado);
			$arrResultado=$io_report->uf_seleccionar_personal_auditoria_enfermedad($ls_codper);
			$ls_enfermedad=$arrResultado["enfermedad"];
			unset($arrResultado);
			$la_data[$li_j]=array('campo1'=>'<b>N°</b>','campo2'=>($li_j+1),'campo3'=>'<b>NACIONALIDAD</b>','campo4'=>$ls_nacper,'campo5'=>'<b>CEDULA</b>','campo6'=>$ls_cedper,'campo7'=>'<b>APELLIDOS Y NOMBRES</b>','campo8'=>$ls_nombre);
			$li_j++;
			$la_data[$li_j]=array('campo1'=>'<b>SEXO (F-M)</b>','campo2'=>$ls_sexper,'campo3'=>'<b>FECHA DE NACIMIENTO</b>','campo4'=>$ls_fecnacper,'campo5'=>'<b>FECHA DE INGRESO</b>','campo6'=>$ld_fecingper,'campo7'=>'<b>TELEFONO DE CONTACTO</b>','campo8'=>$ls_telmovper);
			$li_j++;
			$la_data[$li_j]=array('campo1'=>'<b>TELEFONO DE HABITACION</b>','campo2'=>$ls_telhabper,'campo3'=>'<b>DIRECCION RESIDENCIAL COMPLETA</b>','campo4'=>$ls_dirper,'campo5'=>'<b>CORREO ELECTRONICO PERSONAL</b>','campo6'=>$ls_coreleper,'campo7'=>'<b>CORREO ELECTRONICO INSTITUCIONAL</b>','campo8'=>$ls_coreleins);
			$li_j++;
			$la_data[$li_j]=array('campo1'=>'<b>POSEE ALGUNA DISCAPACIDAD</b>','campo2'=>$ls_tienedis,'campo3'=>'<b>INDIQUE CUAL?</b>','campo4'=>$ls_desdis,'campo5'=>'<b>CARNET DE DISCAPACIDAD</b>','campo6'=>$ls_nrocardis,'campo7'=>'<b>ENFERMEDAD CRONICA</b>','campo8'=>$ls_enfermedad);
			$li_j++;
			$la_data[$li_j]=array('campo1'=>'<b>NRO DE HIJOS</b>','campo2'=>$li_nrohijos,'campo3'=>'<b>HIJOS EN EDAD ESCOLAR</b>','campo4'=>$li_nrohijosescolar,'campo5'=>'<b>FAMILIAR (CONYUGE) TRABAJA EN EL ORGANISMO</b>','campo6'=>$ls_contraorg,'campo7'=>'<b>CALZADO</b>','campo8'=>$ls_talzapper);
			$li_j++;
			$la_data[$li_j]=array('campo1'=>'<b>CAMISA</b>','campo2'=>$ls_talcamper,'campo3'=>'<b>PANTALON</b>','campo4'=>$ls_talpanper,'campo5'=>'<b>OBSERVACION</b>','campo6'=>$ls_obsper);
			$li_j++;
		   
		    uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
			$li_i++;
		    unset($la_data);			
			$io_report->rs_data->MoveNext();	
			if($li_i==4)
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
				$li_i=0;
			}  
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
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