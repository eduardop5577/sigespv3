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
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   as_desnom // Descripci�n de la n�mina
		//	    		   as_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el t�tulo
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$io_pdf->addText(700,560,7,$_SESSION["ls_database"]);
		$io_pdf->addText(700,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(706,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_nomemp,$as_codsis,$as_nomsis,$as_codusu,$as_nomusu,$as_tipo,$io_pdf)
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
		//    Description: funci�n que imprime la cabecera de cada p�gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>'<b>Empresa</b>  '.$as_nomemp.''),
					   array ('name'=>'<b>'.$as_tipo.'</b>    '.$as_codusu." - ".$as_nomusu.''),
					   array ('name'=>'<b>Sistema</b>   '.$as_codsis." - ".$as_nomsis.''),
					   );
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama�o de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar L�neas
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>2	, // Sombra entre l�neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>710, // Ancho de la tabla
						 'maxWidth'=>710); // Ancho M�ximo de la tabla
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
		//	    Arguments: la_data // arreglo de informaci�n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci�n que imprime el detalle
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 10/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-5);
		$la_columna=array('pantalla'=>'<b>Pantalla</b>',
						  'acceso'=>'<b>Acceso</b>',
						  'leer'=>'<b>Buscar</b>',
						  'incluir'=>'<b>Incluir</b>',
						  'cambiar'=>'<b>Modificar</b>',
						  'eliminar'=>'<b>Eliminar</b>',
						  'ejecutar'=>'<b>Procesar</b>',
						  'imprimir'=>'<b>Imprimir</b>',
						  'anular'=>'<b>Anular</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama�o de Letras
						 'titleFontSize' => 9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>730, // Ancho de la tabla
						 'maxWidth'=>730, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'cols'=>array('pantalla'=>array('justification'=>'left','width'=>150), // Justificaci�n y ancho de la columna
						 			   'acceso'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'leer'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'incluir'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'cambiar'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'eliminar'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'ejecutar'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'imprimir'=>array('justification'=>'center','width'=>70), // Justificaci�n y ancho de la columna
						 			   'anular'=>array('justification'=>'center','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_totent,$ai_totsal,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_totent // Total Entradas
		//	   			   ai_totsal // Total Salidas
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera de cada p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama�o de Letras
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'xOrientation'=>'center', // Orientaci�n de la tabla
						 'width'=>730); // Ancho M�ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('total'=>''));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>730, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>500), // Justificaci�n y ancho de la columna
						 			   'entradas'=>array('justification'=>'right','width'=>100), // Justificaci�n y ancho de la columna
						 			   'salidas'=>array('justification'=>'right','width'=>100))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>730, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci�n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../modelo/sss/sigesp_sss_class_report.php");
	$io_report=new sigesp_sss_class_report();
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("class_funciones_seguridad.php");
	$io_fun_inventario=new class_funciones_seguridad();
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_fecha="";
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_nomgru=$io_fun_inventario->uf_obtenervalor_get("nomgru","");
	$ls_codusu=$io_fun_inventario->uf_obtenervalor_get("codusu","");
	$ls_codsis=$io_fun_inventario->uf_obtenervalor_get("codsis","");
	$li_orden=$io_fun_inventario->uf_obtenervalor_get("orden","");
	//--------------------------------------------------------------------------------------------------------------------------------
	if(trim($ls_nomgru)=='')
	{
		$lb_valido=$io_report->uf_sss_select_permisos_usuario($ls_codemp,$ls_codusu,$ls_codsis,$li_orden); // Cargar el DS con los datos de la cabecera del reporte
		$ls_titulo="Reporte de Permisos de Usuarios";
		$ls_tipo="Usuario";
	}
	else
	{
		$lb_valido=$io_report->uf_sss_select_permisos_grupo($ls_codemp,$ls_nomgru,$ls_codsis,$li_orden); // Cargar el DS con los datos de la cabecera del reporte
		$ls_titulo="Reporte de Permisos de Grupo";
		$ls_tipo="Grupo";
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
		//
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(700,50,10,'','',1); // Insertar el n�mero de p�gina
		while(!$io_report->rs_data->EOF)
		{
		    $io_pdf->transaction('start'); // Iniciamos la transacci�n
			$li_numpag=$io_pdf->ezPageCount; // N�mero de p�gina
			$li_totent=0;
			$li_totsal=0;
			$ls_codsis=  $io_report->rs_data->fields["codsis"];
			$ls_nomsis=  $io_report->rs_data->fields["nomsis"];
			$ls_codusu=  $io_report->rs_data->fields["codusu"];
			$ls_nomusu=  $io_report->rs_data->fields["nomusu"]." ".$io_report->rs_data->fields["apeusu"];
			$io_pdf = uf_print_cabecera($ls_nomemp,$ls_codsis,$ls_nomsis,$ls_codusu,$ls_nomusu,$ls_tipo,$io_pdf); // Imprimimos la cabecera del registro
			$li_s=0;
			if(trim($ls_nomgru)=='')
			{
				$lb_valido=$io_report->uf_sss_select_dt_permisos_usuario($ls_codemp,$ls_codusu,$ls_codsis); // Obtenemos el detalle del reporte
			}
			else
			{
				$lb_valido=$io_report->uf_sss_select_dt_permisos_grupo($ls_codemp,$ls_nomgru,$ls_codsis); // Obtenemos el detalle del reporte
			}
			if($lb_valido)
			{
				while(!$io_report->rs_data_detalle->EOF)
				{
					$li_enabled=  $io_report->rs_data_detalle->fields["enabled"];
					$li_leer=     $io_report->rs_data_detalle->fields["leer"];
					$li_incluir=  $io_report->rs_data_detalle->fields["incluir"];
					$li_cambiar=  $io_report->rs_data_detalle->fields["cambiar"];
					$li_eliminar= $io_report->rs_data_detalle->fields["eliminar"];
					$li_imprimir= $io_report->rs_data_detalle->fields["imprimir"];
					$li_anular=   $io_report->rs_data_detalle->fields["anular"];
					$li_ejecutar= $io_report->rs_data_detalle->fields["ejecutar"];
					$ls_pantalla= $io_report->rs_data_detalle->fields["titven"];
					if($li_enabled=="1"){$ls_enabled="S�";}else{$ls_enabled="No";}
					if($li_leer=="1"){$ls_leer="S�";}else{$ls_leer="No";}
					if($li_incluir=="1"){$ls_incluir="S�";}else{$ls_incluir="No";}
					if($li_cambiar=="1"){$ls_cambiar="S�";}else{$ls_cambiar="No";}
					if($li_eliminar=="1"){$ls_eliminar="S�";}else{$ls_eliminar="No";}
					if($li_imprimir=="1"){$ls_imprimir="S�";}else{$ls_imprimir="No";}
					if($li_anular=="1"){$ls_anular="S�";}else{$ls_anular="No";}
					if($li_ejecutar=="1"){$ls_ejecutar="S�";}else{$ls_ejecutar="No";}
					$li_s++;
					$la_data[$li_s]=array('pantalla'=>$ls_pantalla,'acceso'=>$ls_enabled,'leer'=>$ls_leer,'incluir'=>$ls_incluir,'cambiar'=>$ls_cambiar,
										  'eliminar'=>$ls_eliminar,'imprimir'=>$ls_imprimir,'anular'=>$ls_anular,'ejecutar'=>$ls_ejecutar);
					$io_report->rs_data_detalle->MoveNext();
				}
				$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				$io_pdf = uf_print_pie_cabecera("","",$io_pdf); // Imprimimos pie de la cabecera
				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				}
				else
				{// Hacemos un rollback de los registros, agregamos una nueva p�gina y volvemos a imprimir
					$io_pdf->transaction('rewind');
					if($li_numpag>1)
					{
						$io_pdf->ezNewPage(); // Insertar una nueva p�gina
					}
					$io_pdf = uf_print_cabecera($ls_nomemp,$ls_codsis,$ls_nomsis,$ls_codusu,$ls_nomusu,$ls_tipo,$io_pdf); // Imprimimos la cabecera del registro
					$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					$io_pdf = uf_print_pie_cabecera("","",$io_pdf); // Imprimimos pie de la cabecera
				}
			}
			$io_report->rs_data->MoveNext();
			unset($la_data);			
			if($li_s==0) // Existe alg�n error � no hay registros
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar');"); 
				print(" close();");
				print("</script>");
			}
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