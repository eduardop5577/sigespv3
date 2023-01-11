<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->setStrokeColor(0,0,0);
		//$io_pdf->rectangle(200,710,350,40);
		//$io_pdf->line(400,750,400,710);
		//$io_pdf->line(400,730,550 ,730);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,700,10,$as_titulo); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(10,$as_fecha);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,780,10,$as_fecha); // Agregar el título
		$io_pdf->addText(510,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_titulo_cabecera,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomemp    // nombre de la empresa
		//	    		   as_nomfisalm // nombre fiscal de la empresa
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>$as_titulo_cabecera));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>483, // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name'=>array('justification'=>'center','width'=>225))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
		$la_data=array(array('cod'=>'<b>CODIGO</b>',
							 'des'=>'<b>DESCRIPCIÓN</b>',
							 'salini'=>'<b>SALDO ANTERIOR</b>',
							 'entra'=>'<b>ENTRADA</b>',
							 'sal'=>'<b>SALIDA</b>',
							 'tot'=>'<b>TOTAL</b>',));
		$la_columna=array('cod'=>'','des'=>'','salini'=>'','entra'=>'','sal'=>'','tot'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('cod'=>array('justification'=>'center','width'=>60),
									   'des'=>array('justification'=>'center','width'=>240),
									   'salini'=>array('justification'=>'center','width'=>75),
									   'entra'=>array('justification'=>'center','width'=>65),
									   'sal'=>array('justification'=>'center','width'=>65),
									   'tot'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_2($as_codart,$as_dentipart,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomemp    // nombre de la empresa
		//	    		   as_nomfisalm // nombre fiscal de la empresa
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name0'=>'<b><i></b></i>','name'=>'<b><i>'.$as_dentipart.'</b></i>','name2'=>'','name3'=>'','name4'=>'','name5'=>''));
		$la_columna=array('name0'=>'','name'=>'','name2'=>'','name3'=>'','name4'=>'','name5'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 //'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500,
						 'cols'=>array('name0'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
						 			   'name'=>array('justification'=>'left','width'=>240), // Justificación y ancho de la columna
						 			   'name2'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'name3'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'name4'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
									   'name5'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		
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
		global $io_pdf;
		//$io_pdf->ezSetDy(-5);
		$la_columna=array('codart'=>'<b>Código</b>',
						  'denominacion'=>'<b>Código</b>',
						  'salini'=>'<b>Denominación</b>',
						  'entradas'=>'<b>Existencia (Detal)</b>',
						  'salidas'=>'<b>Existencia (Detal)</b>',
						  'total'=>'<b>Existencia (Mayor)</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codart'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>240), // Justificación y ancho de la columna
						 			   'salini'=>array('justification'=>'center','width'=>75), // Justificación y ancho de la columna
						 			   'entradas'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'salidas'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
									   'total'=>array('justification'=>'center','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_totprenom,$ai_totant,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_totprenom // Total Prenómina
		//	   			   ai_totant // Total Anterior
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>''));
		//$la_data=array(array('name'=>'_________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>510); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('total'=>''));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>510, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>300), // Justificación y ancho de la columna
						 			   'prenomina'=>array('justification'=>'right','width'=>100), // Justificación y ancho de la columna
						 			   'anterior'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>510, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_siv_class_report.php");
	$io_report=new sigesp_siv_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_inventario.php");
	$io_fun_inventario=new class_funciones_inventario();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fun_fecha=new class_fecha();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_fecrec=$io_fun_inventario->uf_obtenervalor_get("fecrec","");

	$ls_titulo="<b> Niveles de Existencia de Artículos </b>";
	$ls_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_mesdesde=$io_fun_inventario->uf_obtenervalor_get("mesd","");
	$ls_meshasta=$io_fun_inventario->uf_obtenervalor_get("mesh","");
	$ls_aniodesde=$io_fun_inventario->uf_obtenervalor_get("aniod","");
	$ls_aniohasta=$io_fun_inventario->uf_obtenervalor_get("anioh","");
	$ls_orden=$io_fun_inventario->uf_obtenervalor_get("ordenact","");
	$ls_codalm=$io_fun_inventario->uf_obtenervalor_get("codalm","");
	$ls_nomfisalm=$io_fun_inventario->uf_obtenervalor_get("nomfisalm","");
	$ls_fechac_desde="01-".$ls_mesdesde."-".$ls_aniodesde;
	$ls_fechac_hasta=$io_fun_fecha->uf_last_day($ls_meshasta,$ls_aniohasta);
	$ls_fechac_hasta=$io_fun_fecha->uf_convert_date_to_db($ls_fechac_hasta);
	$ls_fechac_desde=$io_fun_fecha->uf_convert_date_to_db($ls_fechac_desde);
	if ($ls_mesdesde=='01')
	{$ls_mesdesdetxt="Enero";}
	elseif ($ls_mesdesde=='02')
	{$ls_mesdesdetxt="Febrero";}
	elseif ($ls_mesdesde=='03')
	{$ls_mesdesdetxt="Marzo";}
	elseif ($ls_mesdesde=='04')
	{$ls_mesdesdetxt="Abril";}
	elseif ($ls_mesdesde=='05')
	{$ls_mesdesdetxt="Mayo";}
	elseif ($ls_mesdesde=='06')
	{$ls_mesdesdetxt="Junio";}
	elseif ($ls_mesdesde=='07')
	{$ls_mesdesdetxt="Julio";}
	elseif ($ls_mesdesde=='08')
	{$ls_mesdesdetxt="Agosto";}
	elseif ($ls_mesdesde=='09')
	{$ls_mesdesdetxt="Septiembre";}
	elseif ($ls_mesdesde=='10')
	{$ls_mesdesdetxt="Octubre";}
	elseif ($ls_mesdesde=='11')
	{$ls_mesdesdetxt="Noviembre";}
	elseif ($ls_mesdesde=='12')
	{$ls_mesdesdetxt="Diciembre";}
	$ls_titulo_cabecera=$ls_mesdesdetxt." - ".$ls_aniodesde;
	
	if ($ls_meshasta=='01')
	{$ls_meshastatxt="Enero";}
	elseif ($ls_meshasta=='02')
	{$ls_meshastatxt="Febrero";}
	elseif ($ls_meshasta=='03')
	{$ls_meshastatxt="Marzo";}
	elseif ($ls_meshasta=='04')
	{$ls_meshastatxt="Abril";}
	elseif ($ls_meshasta=='05')
	{$ls_meshastatxt="Mayo";}
	elseif ($ls_meshasta=='06')
	{$ls_meshastatxt="Junio";}
	elseif ($ls_meshasta=='07')
	{$ls_meshastatxt="Julio";}
	elseif ($ls_meshasta=='08')
	{$ls_meshastatxt="Agosto";}
	elseif ($ls_meshasta=='09')
	{$ls_meshastatxt="Septiembre";}
	elseif ($ls_meshasta=='10')
	{$ls_meshastatxt="Octubre";}
	elseif ($ls_meshasta=='11')
	{$ls_meshastatxt="Noviembre";}
	elseif ($ls_meshasta=='12')
	{$ls_meshastatxt="Diciembre";}
	//$li_ordenalm=0;
	//$li_ordenart=1;
	$ls_tituloalmacen="";
	if(trim($ls_codalm)!="")
	{
		$ls_tituloalmacen="Almacen: ".$ls_nomfisalm;
	}
	if ($ls_mesdesdetxt!=$ls_meshastatxt)
	{
		$ls_titulo=$ls_titulo."<b> Desde el mes de </b>"."<b>".$ls_mesdesdetxt." hasta el mes de ".$ls_meshastatxt."</b>";
		$ls_titulo_cabecera="<b> ".$ls_mesdesdetxt." ".$ls_aniodesde." - ".$ls_meshastatxt." ".$ls_aniohasta."</b>";
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_select_articulos_existentes($ls_codemp,$ls_orden); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////
		$ls_desc_event="Generó el reporte de Existencias mensuales de articulos ";
		$io_fun_inventario->uf_load_seguridad_reporte("SIV","sigesp_siv_r_articuloxalmacen_mensual.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,4,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$li_totrow=$io_report->ds->getRowCount("codart");
		uf_print_cabecera($ls_titulo_cabecera,$io_pdf); // Imprimimos la cabecera del registro
		$ls_dentipart_aux="";
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
		    $io_pdf->transaction('start'); // Iniciamos la transacción
			$li_numpag=$io_pdf->ezPageCount; // Número de página
			$li_totprenom=0;
			$li_totant=0;
			$ls_codart=$io_report->ds->data["codart"][$li_i];
			$ls_denart=$io_report->ds->data["denart"][$li_i];
			$ls_dentipart=$io_report->ds->data["dentipart"][$li_i];
			$lb_valido=$io_report->uf_select_entrasale_art($ls_codemp,$ls_codart,$ls_fechac_desde,$ls_fechac_hasta,$ls_codalm); // Obtenemos el detalle del reporte
			if($lb_valido)
			{
				if ($ls_dentipart_aux!=$ls_dentipart)
				{
					uf_print_cabecera_2($ls_codart,$ls_dentipart,$io_pdf); // Imprimimos la cabecera del registro
					$ls_dentipart_aux=$ls_dentipart;
				}
				$li_total=0;
				$li_totrow_det=$io_report->ds_detalle->getRowCount("codart");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$li_entini= $io_report->ds_detalle->data["entradas_inicial"][$li_s];
					$li_salidini= $io_report->ds_detalle->data["salidas_inicial"][$li_s];
					$li_salini= ($li_entini-$li_salidini);
					$li_entradas= $io_report->ds_detalle->data["entradas"][$li_s];
					$li_salidas= $io_report->ds_detalle->data["salidas"][$li_s];
					$li_total=$li_salini + $li_entradas - $li_salidas;
					if ($li_salini=="")
					{
						$li_salini=0;
					}
					if ($li_entradas=="")
					{
						$li_entradas=0;
					}
					if ($li_salidas=="")
					{
						$li_salidas=0;
					}
					if ($li_total=="")
					{
						$li_total=0;
					}
					$la_data[$li_s]=array('codart'=>$ls_codart,'denominacion'=>$ls_denart,'salini'=>$li_salini,'entradas'=>$li_entradas,'salidas'=>$li_salidas,'total'=>$li_total);
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				}
				else
				{// Hacemos un rollback de los registros, agregamos una nueva página y volvemos a imprimir
					$io_pdf->transaction('rewind');
					if($li_numpag>1)
					{
						$io_pdf->ezNewPage(); // Insertar una nueva página
					}
					uf_print_cabecera($ls_titulo_cabecera,$io_pdf); // Imprimimos la cabecera del registro
					//uf_print_cabecera_2($ls_dentipart,$io_pdf); // Imprimimos la cabecera del registro
					//uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				}
			}
			unset($la_data);			
		}
		//if($lb_valido)
		//{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		//}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 