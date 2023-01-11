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
	function uf_print_encabezado_pagina($as_titulo,$ad_fecha,$io_pdf)
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
		$io_pdf->line(40,40,955,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],40.5,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$io_pdf->addText(920,580,7,$_SESSION["ls_database"]);
		$io_pdf->addText(920,570,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(925,560,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_codusu,$as_nomusu,$as_codsis,$as_nomsis,$as_evento,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codusu // codigo de usuario
		//	    		   as_nomusu // nombre nombre de usuario
		//	    		   as_codsis // codigo de sistema
		//	    		   as_nomsis // nombre de sistema
		//	    		   as_evento // Evento 
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($as_codusu==""){$as_nomusu="Todos los Usuarios";}
		if($as_codsis==""){$as_nomsis="Todos los Sistemas";}
		if($as_evento==""){$as_evento="Todos los Eventos";}
		
		$la_data=array(array('name'=>'<b>Usuario</b>  '.$as_codusu." - ".$as_nomusu.''),
					   array ('name'=>'<b>Sistema</b>  '.$as_codsis." - ".$as_nomsis.''),
					   array ('name'=>'<b>Evento</b>    '.$as_evento.'')
					   );
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>900))); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		return $io_pdf;		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		$la_columna=array('codusu'=>'<b>Usuario</b>',
						  'evento'=>'<b>Evento</b>',
						  'titven'=>'<b>Ventana</b>',
						  'fecevetra'=>'<b>Fecha/Hora</b>',
						  'equevetra'=>'<b>Equipo</b>',
						  'desevetra'=>'<b>Descripción</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codusu'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'evento'=>array('justification'=>'left','width'=>55), // Justificación y ancho de la columna
						 			   'titven'=>array('justification'=>'left','width'=>140), // Justificación y ancho de la columna
						 			   'fecevetra'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'equevetra'=>array('justification'=>'left','width'=>65), // Justificación y ancho de la columna
						 			   'desevetra'=>array('justification'=>'left','width'=>440))); // Justificación y ancho de la columna
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
	$ld_fecdes=$io_fun_inventario->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_inventario->uf_obtenervalor_get("fechas","");
	$ls_titulo="<b> Reporte de Auditoría </b> Desde ".$ld_fecdes." Hasta ".$ld_fechas;
	$ls_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codusu=$io_fun_inventario->uf_obtenervalor_get("codigo","");
	$ls_evento=$io_fun_inventario->uf_obtenervalor_get("evento","");
	$ls_codsis=$io_fun_inventario->uf_obtenervalor_get("sistema","");
	$ls_numdocumento=$io_fun_inventario->uf_obtenervalor_get("numdocumento","");
	$ls_numprefijo=$io_fun_inventario->uf_obtenervalor_get("numprefijo","");
	$ls_nomsis="";
	$ls_nomusu="";
	$lb_valido=true;
	//--------------------------------------------------------------------------------------------------------------------------------
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_sss_select_auditoria($ls_codemp,$ls_codusu,$ls_evento,$ls_codsis,$ld_fecdes,$ld_fechas,$ls_numdocumento,$ls_numprefijo); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false)||($io_report->ds->EOF)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		//
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3,2.5,3,3); // Configuración de los margenes en centímetros
		$io_pdf = uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(940,50,10,'','',1); // Insertar el número de página
		if(!$io_report->ds->EOF)
		{
			$io_pdf = uf_print_cabecera($ls_codusu,$ls_nomusu,$ls_codsis,$io_report->ds->fields["nomsis"],$ls_evento,$io_pdf); // Imprimimos la cabecera del registro
			$li_pos=0;
			while(!$io_report->ds->EOF)
			{
				$li_pos=$li_pos+1;
				$io_pdf->transaction('start'); // Iniciamos la transacción
				$li_numpag=$io_pdf->ezPageCount; // Número de página
				$ls_evento= $io_report->ds->fields["evento"];
				$ls_ventana= $io_report->ds->fields["titven"];		
				$ld_fecevetra=  date("d/m/Y H:i",strtotime($io_report->ds->fields["fecevetra"]));
				$ls_equevetra=  $io_report->ds->fields["equevetra"];
				$ls_desevetra=  $io_report->ds->fields["desevetra"];
				$ls_nomusu= $io_report->ds->fields["nomusu"]." ".$io_report->ds->fields["apeusu"];
				$la_data[$li_pos]=array('codusu'=>$ls_nomusu,'evento'=>$ls_evento,'titven'=>$ls_ventana,'fecevetra'=>$ld_fecevetra,
									  'equevetra'=>$ls_equevetra,'desevetra'=>$ls_desevetra);
				$io_report->ds->MoveNext();
			}
			$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
			if($lb_valido)
			{
				$io_pdf->ezStopPageNumbers(1,1);
				$io_pdf->ezStream();
			}
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");		
		}
	}
?>