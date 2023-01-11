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
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		global $io_encabezado;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_relacion_cargosrac.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo2,$as_desnom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,755,40);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(12,$as_titulo2);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,540,12,$as_titulo2); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);
		$tm=256-($li_tm/2);
		$io_pdf->addText($tm,520,11,$as_titulo); // Agregar el título
		$io_pdf->addText(340,520,10,"<b><i>".$as_desnom."</b></i>"); // Agregar el título
		$io_pdf->addText(712,560,8,date("d-m-Y")); // Agregar la Fecha
		$io_pdf->addText(718,553,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_titulo($io_pdf)
	{
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setColor(0,0,0);
		$io_pdf->line(5,515,760,515);//horizontal
		$io_pdf->line(5,465,760,465);//horizontal
		$io_pdf->line(5,445,760,445);//horizontal
		
		$io_pdf->addText(10,450,11,'Se-Pr-Código     Cargo                                                Nombre                                                            Ingreso                     Cédula                    Sueldo Obser.'); // Agregar el título
		$io_pdf->ezSetDy(30);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_titulo1($ls_sector,$ls_denestpro1,$ls_programa,$ls_denestpro2,$io_cabecera,$io_pdf)
	{
		global $io_pdf,$io_cabecera;
		$io_pdf->saveState();
		$io_pdf->setColor(0,0,0);
		$io_pdf->addText(55,500,11,'<b>Sector                </b>'.'<b>'.$ls_sector.'        '.$ls_denestpro1.'</b>'); // Agregar el título
		$io_pdf->addText(55,480,11,'<b>Programa          </b>'.'<b>'.$ls_programa.'        '.$ls_denestpro2.'</b>'); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_cabecera,'all');
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales_actividad($li_cont1,$io_pdf)
	{
		global $io_pdf;
		$la_data_1[1]=array('sector'=>'<b>Total Sub_Actividad         </b>'.'<b>'.$li_cont1.'</b>');
		//$la_data_1[2]=array('sector'=>'');
		$la_columna=array('sector'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xPos' => 430,
						 'cols'=>array('sector'=>array('justification'=>'left','width'=>250))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_1,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_titulo2($ls_actividad,$ls_denestpro5,$io_pdf)
	{
		global $io_pdf;
		//$io_pdf->ezSety(500);
		$la_data_1[1]=array('sector'=>'<b>Actividad           </b>'.'<b>'.$ls_actividad.'        '.$ls_denestpro5.'</b>');
		$la_data_1[2]=array('sector'=>'');
		$la_columna=array('sector'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xPos' => 385,
						 'cols'=>array('sector'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_1,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_titulo3($ls_unidad,$io_pdf)
	{
		global $io_pdf;
		//$io_pdf->ezSety(500);
		$la_data_1[1]=array('sector'=>'<b>                    '.$ls_unidad.'</b>');
		//$la_data_1[2]=array('sector'=>'');					  
		$la_columna=array('sector'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'xPos' => 385,
						 'cols'=>array('sector'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_1,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_personal($la_data_personal,$io_pdf)
	{
		global $io_pdf;
		$la_columna=array('codigo'=>'',
						  'cargo'=>'',
						  'nombre'=>'',
						  'fecha'=>'',
						  'cedula'=>'',
						  'sueldo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>700, // Ancho de la tabla
						 'maxWidth'=>700, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'xPos' => 390,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'cargo'=>array('justification'=>'left','width'=>180), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
						 			   'cedula'=>array('justification'=>'center','width'=>100),
									   'sueldo'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data_personal,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b><i>Relación de Cargos</i></b>";
	$ls_titulo_empresa=$_SESSION["la_empresa"]["nombre"];
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$ls_desnom=$io_fun_nomina->uf_obtenervalor_get("desnom","");
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_titulo2="<b><i>".$ls_titulo_empresa."</i></b>";
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_listadopersonalrelacion_rac($ls_codnom,$ls_codperdes,$ls_codperhas,$ls_orden); // Obtenemos el detalle del reporte
	}
	$li_totrow=$io_report->rs_data->RecordCount();
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(7,2.5,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo2,$ls_desnom,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el número de página
		$li_i=1;
		$li_x=0;
		$li_cont1=0;
		$ls_sectorant="";
		$ls_programaant="";
		$ls_actividadant="";
		$ls_unidadant="";
		$pagina=2;
		uf_print_titulo($io_pdf);
		$io_report->DS->resetds("codper");
		while (!$io_report->rs_data->EOF)
		{
			$li_x++;
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nomper=$io_report->rs_data->fields["apeper"].", ".$io_report->rs_data->fields["nomper"];
			$ls_desuniadm=$io_report->rs_data->fields["desuniadm"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$ls_codestpro1=$io_report->rs_data->fields["codestpro1"];
			$ls_sector = substr($ls_codestpro1,-$_SESSION["la_empresa"]["loncodestpro1"]);
			$ls_codestpro2=$io_report->rs_data->fields["codestpro2"];
			$ls_programa = substr($ls_codestpro2,-$_SESSION["la_empresa"]["loncodestpro2"]);
			$ls_codestpro5=$io_report->rs_data->fields["codestpro5"];
			$ls_actividad = substr($ls_codestpro5,-$_SESSION["la_empresa"]["loncodestpro5"]);
			$ls_denestpro1=$io_report->rs_data->fields["denestpro1"];
			$ls_denestpro2=$io_report->rs_data->fields["denestpro2"];
			$ls_denestpro5=$io_report->rs_data->fields["denestpro5"];
			$ls_descar=$io_report->rs_data->fields["denasicar"];
			$ls_codcar=$io_report->rs_data->fields["codasicar"];
			$li_sueldo=$io_fun_nomina->uf_formatonumerico($io_report->rs_data->fields["sueper"]);
			$ls_codesp=$ls_sector."-".$ls_programa."-".$ls_codcar;
			if(($ls_sectorant<>$ls_sector)||($ls_programaant<>$ls_programa))
			{
				if ($li_x>1)
				{
					$io_pdf->stopObject($io_cabecera);
					$io_pdf->ezNewPage();
				}
				$ls_sectorant=$ls_sector;
				$ls_programaant=$ls_programa;
				$io_cabecera=$io_pdf->openObject(); // Creamos el objeto cabecera
				uf_print_detalle_titulo1($ls_sector,$ls_denestpro1,$ls_programa,$ls_denestpro2,$io_cabecera,$io_pdf);
				
			}
			if($ls_actividadant<>$ls_actividad)
			{
				$ls_actividadant=$ls_actividad;
				if ($li_x>1)
				{
					uf_print_totales_actividad($li_cont1,$io_pdf);
					$ls_unidadant='';
				}
				uf_print_detalle_titulo2($ls_actividad,$ls_denestpro5,$io_pdf);
			}
			if($ls_unidadant<>$ls_desuniadm)
			{
				if($ls_unidadant<>'')
				{
					uf_print_totales_actividad($li_cont1,$io_pdf);
				}
				$ls_unidadant=$ls_desuniadm;
				uf_print_detalle_titulo3($ls_desuniadm,$io_pdf);
				$li_cont1=0;
			}

			$la_data_personal[$li_i]=array('codigo'=>$ls_codesp,'cargo'=>$ls_descar,'nombre'=>$ls_nomper,'fecha'=>$ld_fecingper,'cedula'=>$ls_cedper,'sueldo'=>$li_sueldo);
			uf_print_personal($la_data_personal,$io_pdf);
			$li_cont1++;
			/*if (($ls_sectorant<>$ls_sector)||($ls_programaant<>$ls_programa))
			{
				$io_pdf->ezNewPage(); // Insertar una nueva página
			}*/
			$io_report->rs_data->MoveNext();
		}
		unset($la_data);			
		$io_report->DS->resetds("codper");		
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