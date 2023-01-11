<?PHP
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	//-----------------------------------------------------------------------------------------------------------------------------------
	//Reporte Modificado para aceptar Bs. y Bs.F.
	//Modificado por: Ing. Luis Anibal Lang  08/08/2007	
	//-----------------------------------------------------------------------------------------------------------------------------------
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
	function uf_insert_seguridad($as_titulo,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo    // T�tulo del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//    Description: funci�n que guarda la seguridad de quien gener� el reporte
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 08/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_viaticos;
		
		$ls_descripcion="Gener� el Reporte ".$as_titulo.". Desde ".$ad_fecregdes.". Hasta ".$ad_fecreghas;
		$lb_valido=$io_fun_viaticos->uf_load_seguridad_reporte("SCV","sigesp_scv_r_listadosolicitudes.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ad_fecregdes,$ad_fecreghas,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 08/06/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
//		$io_pdf->rectangle(10,710,580,60);
//		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],15,540,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,560,11,$as_titulo); // Agregar el t�tulo
		$ls_periodo="Periodo ".$ad_fecregdes." - ".$ad_fecreghas;
		$li_tm=$io_pdf->getTextWidth(11,$ls_periodo);
		$tm=396-($li_tm/2);
		$io_pdf->addText($tm,550,11,$ls_periodo); // Agregar el t�tulo
		$io_pdf->addText(730,580,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(736,573,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($aa_data,$as_tit,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: aa_data // arreglo de informaci�n
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el detalle por concepto
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci�n: 08/07/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $ls_tiporeporte;
		if($ls_tiporeporte==1)
		{
			$ls_titulo="Monto Bs.F.";
		}
		else
		{
			$ls_titulo="Monto Bs.";
		}
		$io_pdf->ezSetDy(-2);
			$la_data[1]=array('li'=>"<b>No</b>",'numsolvia'=>"<b>Solicitud</b>",'nombre'=>"<b>Funcionario</b>",'tipvia'=>$as_tit,'salida'=>"<b>Salida</b>",
								  'destino'=>"<b>Destino</b>",'fecsalvia'=>"<b>Fecha</b>",
								  'dolares'=>"<b>US$</b>",'monto'=>"<b>Bs.</b>");
		$la_columnas=array('li'=>'<b>No</b>',
						   'numsolvia'=>'<b>Solicitud</b>',
						   'nombre'=>'<b>C�dula</b>',
						   'tipvia'=>'<b>Beneficiario</b>',
						   'salida'=>'<b>Fecha Salida</b>',
						   'destino'=>'<b>Fecha Retorno</b>',
						   'fecsalvia'=>'<b>Ruta</b>',
						   'dolares'=>'<b>Ruta</b>',
						   'monto'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>2, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('li'=>array('justification'=>'center','width'=>30), // Justificaci�n y ancho de la columna
						 			   'numsolvia'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>135), // Justificaci�n y ancho de la columna
						 			   'tipvia'=>array('justification'=>'center','width'=>105), // Justificaci�n y ancho de la columna
						 			   'salida'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'destino'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'fecsalvia'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'dolares'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_columnas=array('li'=>'<b>No</b>',
						   'numsolvia'=>'<b>Solicitud</b>',
						   'nombre'=>'<b>C�dula</b>',
						   'tipvia'=>'<b>Beneficiario</b>',
						   'salida'=>'<b>Fecha Salida</b>',
						   'destino'=>'<b>Fecha Retorno</b>',
						   'fecsalvia'=>'<b>Ruta</b>',
						   'dolares'=>'<b>Ruta</b>',
						   'monto'=>'<b>Monto</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama�o de Letras
						 'titleFontSize' => 12,  // Tama�o de Letras de los t�tulos
						 'showLines'=>2, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M�ximo de la tabla
						 'xOrientation'=>'center', // Orientaci�n de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('li'=>array('justification'=>'center','width'=>30), // Justificaci�n y ancho de la columna
						 			   'numsolvia'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>135), // Justificaci�n y ancho de la columna
						 			   'tipvia'=>array('justification'=>'center','width'=>105), // Justificaci�n y ancho de la columna
						 			   'salida'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'destino'=>array('justification'=>'center','width'=>100), // Justificaci�n y ancho de la columna
						 			   'fecsalvia'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'dolares'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($aa_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_piecabecera($ai_total,$ai_montot,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_piecabecera
		//		   Access: private 
		//	    Arguments: ai_total // Total de Trabajadores
		//	   			   ai_montot // Monto total por concepto
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime el fin de la cabecera por conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 26/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total Registros</b>'.' '.$ai_total.'','monto'=>$ai_montot));
		$la_columna=array('total'=>'','monto'=>'');
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
						 'cols'=>array('total'=>array('justification'=>'right','width'=>650), // Justificaci�n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>70))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_viaticos.php");
	$io_fun_viaticos=new class_funciones_viaticos();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------

	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ld_fecregdes=$io_fun_viaticos->uf_obtenervalor_get("desde","");
	$ld_fecreghas=$io_fun_viaticos->uf_obtenervalor_get("hasta","");
	$ls_coduniadm=$io_fun_viaticos->uf_obtenervalor_get("coduniadm","");
	$ls_tiporeporte=$io_fun_viaticos->uf_obtenervalor_get("tiporeporte",0);
	$ls_tipvia=$io_fun_viaticos->uf_obtenervalor_get("tipvia","0");
	$ls_codmis=$io_fun_viaticos->uf_obtenervalor_get("codmisori","");
	$ls_codmisdes=$io_fun_viaticos->uf_obtenervalor_get("codmisdes","");
	$ls_codsoldes=$io_fun_viaticos->uf_obtenervalor_get("codsoldes","");
	$ls_codsolhas=$io_fun_viaticos->uf_obtenervalor_get("codsolhas","");
	$ls_codtipdoc=$io_fun_viaticos->uf_obtenervalor_get("codtipdoc","");
	$ls_continente=$io_fun_viaticos->uf_obtenervalor_get("continente","");
	$ls_estatus=$io_fun_viaticos->uf_obtenervalor_get("estatus","");
	$ls_codben=$io_fun_viaticos->uf_obtenervalor_get("codben","");
	$ls_orden=$io_fun_viaticos->uf_obtenervalor_get("orden","scv_solicitudes.codsolvia");
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Listado de Solicitudes de Viaticos</b>";
	global $ls_tiporeporte;
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scv_class_reportbsf.php");
		$io_report=new sigesp_scv_class_reportbsf();
	}
	else
	{
		require_once("sigesp_scv_class_report.php");
		$io_report=new sigesp_scv_class_report();
	}	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo,$ld_fecregdes,$ld_fecreghas); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_select_listadosolicitudes($ld_fecregdes,$ld_fecreghas,$ls_coduniadm,$ls_orden,$ls_tipvia,$ls_codmis,$ls_codmisdes,$ls_codsoldes,$ls_codsolhas,$ls_codtipdoc,$ls_continente,$ls_estatus,$ls_codben); // Cargar el DS con los datos de la cabecera del reporte
	}
	if($lb_valido==false) // Existe alg�n error � no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		//print(" close();");
		print("</script>");
	}
	else  // Imprimimos el reporte
	{
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuraci�n de los margenes en cent�metros
		uf_print_encabezado_pagina($ls_titulo,$ld_fecregdes,$ld_fecreghas,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(750,50,10,'','',1); // Insertar el n�mero de p�gina
//		$li_totrow=$io_report->ds_solicitud->getRowCount("numsolvia");
		$li_totrow=$io_report->ds_solicitud->getRowCount("codsolvia");
		$li_montot=0;
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_numsolvia=$io_report->ds_solicitud->data["codsolvia"][$li_i];
			$ls_nombre=$io_report->ds_solicitud->data["nombre"][$li_i];
			$ls_cedula=$io_report->ds_solicitud->data["cedula"][$li_i];
			$ls_desrut=$io_report->ds_solicitud->data["desrut"][$li_i];
			$li_monto=$io_report->ds_solicitud->data["monto"][$li_i];
			$ld_fecsalvia=$io_report->ds_solicitud->data["fecsalvia"][$li_i];
			$ld_fecregvia=$io_report->ds_solicitud->data["fecregvia"][$li_i];
			$ls_tipvia=$io_report->ds_solicitud->data["tipvia"][$li_i];
			$ls_desciuori=$io_report->ds_solicitud->data["desciuori"][$li_i];
			$ls_desciudes=$io_report->ds_solicitud->data["desciudes"][$li_i];
			$ls_mondolsol=$io_report->ds_solicitud->data["mondolsol"][$li_i];
			$li_montot=$li_montot+$li_monto;
			$li_monto=number_format($li_monto,2,',','.');
			$ls_mondolsol=number_format($ls_mondolsol,2,',','.');
			$ld_fecsalvia=$io_funciones->uf_convertirfecmostrar($ld_fecsalvia);
			$ld_fecregvia=$io_funciones->uf_convertirfecmostrar($ld_fecregvia);
			$ls_tit="Orden";
			switch ($ls_tipvia)
			{
				case "1":
					$ls_tipvia="Viaticos de Instalacion";
				break;
				case "2":
					$ls_tipvia="Orden de Transporte";
				break;
				case "3":
					$ls_tipvia="Permanencia";
				break;
				case "4":
					$ls_tipvia="Internacionales";
				break;
				case "5":
					$ls_tipvia="Nacionales";
				break;
				default:
					$ls_tipvia=$ls_desrut;
					$ls_tit="Ruta";
				break;
			}
			
			$la_data[$li_i]=array('li'=>$li_i,'numsolvia'=>$ls_numsolvia,'nombre'=>$ls_nombre,'tipvia'=>$ls_tipvia,'salida'=>$ls_desciuori,
								  'destino'=>$ls_desciudes,'fecsalvia'=>$ld_fecsalvia,
								  'dolares'=>$ls_mondolsol,'monto'=>$li_monto);
		}
		$li_montot=number_format($li_montot,2,',','.');
		uf_print_detalle($la_data,$ls_tit,$io_pdf); // Imprimimos el detalle 
		uf_print_piecabecera($li_totrow,$li_montot,$io_pdf); // Imprimimos el pie de la cabecera
		unset($io_cabecera);
		unset($la_data);
		$io_report->ds_solicitud->resetds("numsolvia");
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
	unset($io_fun_viaticos);
?> 