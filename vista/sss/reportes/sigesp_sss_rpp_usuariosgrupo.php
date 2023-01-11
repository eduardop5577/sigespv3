<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
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
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$io_pdf->addText(500,760,7,$_SESSION["ls_database"]);
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_nomemp,$as_codusu,$as_tipo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomemp    // nombre de la empresa
		//	    		   as_codsis    // codigo de sistema
		//	    		   as_nomsis    // nombre de sistema
		//	    		   as_codusu    // codigo de usuario
		//	    		   as_nomusu    // nombre de usuario
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_cabecera=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(50,670,500,$io_pdf->getFontHeight(30));
        $io_pdf->setColor(0,0,0);
		$io_pdf->addText(55,695,11,'<b>Empresa</b>  '.$as_nomemp.''); // Agregar el título
		$io_pdf->addText(55,675,11,'<b>'.$as_tipo.'</b>  '.$as_codusu.''); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
		return $io_pdf;		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$ls_titulo2,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 10/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('nomgru'=>'<b>'.$ls_titulo2.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('nomgru'=>array('justification'=>'left','width'=>500))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../modelo/sss/sigesp_sss_class_report.php");
	$io_report=new sigesp_sss_class_report();
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("class_funciones_seguridad.php");
	$io_fun_inventario=new class_funciones_seguridad();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_fecha="";
	$ls_titulo2="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_nomgru=$io_fun_inventario->uf_obtenervalor_get("nomgru","");
	$ls_codusu=$io_fun_inventario->uf_obtenervalor_get("codusu","");
	$li_orden=$io_fun_inventario->uf_obtenervalor_get("orden","");
	//--------------------------------------------------------------------------------------------------------------------------------
	if(trim($ls_nomgru)=='')
	{
		$lb_valido=$io_report->uf_sss_select_grupos_por_usuario($ls_codemp,$ls_codusu); // Cargar el DS con los datos de la cabecera del reporte
		$ls_titulo="Reporte de Grupos por Usuario";
		$ls_tipo="Usuario";
		$ls_titulo2="Grupos a los que pertenece el Usuario";
	}
	else
	{
		$lb_valido=$io_report->uf_sss_select_usuarios_por_grupo($ls_codemp,$ls_nomgru); // Cargar el DS con los datos de la cabecera del reporte
		$ls_titulo="Reporte de Usuarios por Grupo";
		$ls_tipo="Grupo";
		$ls_titulo2="Usuarios a los que pertenece el Grupo";
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
		$io_pdf=new Cezpdf('LETTER','letter'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4.5,3,3,3); // Configuración de los margenes en centímetros
		$io_pdf = uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$li_s=0;
		while(!$io_report->rs_data->EOF)
		{
			if ($li_s==0)
			{
				$ls_nomusu=  $io_report->rs_data->fields["codusu"]." ".$io_report->rs_data->fields["nomusu"]." ".$io_report->rs_data->fields["apeusu"];
				$io_pdf = uf_print_cabecera($ls_nomemp,$ls_nomusu,$ls_tipo,$io_pdf); // Imprimimos la cabecera del registro
			}
			$li_s++;
			$ls_grupo= $io_report->rs_data->fields["nomgru"];
			$la_data[$li_s]=array('nomgru'=>$ls_grupo);
			$io_report->rs_data->MoveNext();
		}
		if($li_s==0) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else
		{
			$io_pdf = uf_print_detalle($la_data,$ls_titulo2,$io_pdf); // Imprimimos el detalle 
			unset($la_data);			
		}
		if($lb_valido)
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
?> 