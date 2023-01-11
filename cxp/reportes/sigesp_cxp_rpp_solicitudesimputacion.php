<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_solicitudesf1.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(15,40,585,40);
        $io_pdf->Rectangle(15,700,570,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$io_pdf->addText(540,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(546,764,6,date("h:i a")); // Agregar la Hora
		// cuadro inferior
        $io_pdf->Rectangle(15,60,570,70);
		$io_pdf->line(15,73,585,73);		
		$io_pdf->line(15,117,585,117);		
		$io_pdf->line(130,60,130,130);		
		$io_pdf->line(240,60,240,130);		
		$io_pdf->line(380,60,380,130);		
		$io_pdf->addText(40,122,7,"ELABORADO POR"); // Agregar el título
		$io_pdf->addText(42,63,7,"FIRMA / SELLO"); // Agregar el título
		$io_pdf->addText(157,122,7,"VERIFICADO POR"); // Agregar el título
		$io_pdf->addText(145,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->addText(275,122,7,"AUTORIZADO POR"); // Agregar el título
		$io_pdf->addText(257,63,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
		$io_pdf->addText(440,122,7,"CONTRALORIA INTERNA"); // Agregar el título
		$io_pdf->addText(445,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_solicitudes($as_numsol,$as_nombre,$ad_fecemisol,$as_denest,$ai_monsol,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_solicitudes
		//		   Access: private 
		//	    Arguments: la_data      // arreglo de información
		//				   ai_i         // total de registros
		//				   li_totmonsol // total de solicitudes (Montos)
		//	    		   io_pdf       // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-2);
		$la_datatit[1]=array('numsol'=>'<b>Solicitud</b>','nombre'=>'<b>Proveedor/Beneficiario</b>','fecemisol'=>'<b>Fecha Emision</b>',
							 'denest'=>'<b>Estatus</b>','monsol'=>'<b>Monto </b>');
		$la_columnas=array('numsol'=>'','nombre'=>'','fecemisol'=>'','denest'=>'','monsol'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numsol'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						 			   'fecemisol'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'denest'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'monsol'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);

		$la_data[1]=array('numsol'=>$as_numsol,'nombre'=>$as_nombre,'fecemisol'=>$ad_fecemisol,'denest'=>$as_denest,'monsol'=>$ai_monsol);
		$la_columnas=array('numsol'=>'','nombre'=>'','fecemisol'=>'','denest'=>'','monsol'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numsol'=>array('justification'=>'left','width'=>95), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>250), // Justificación y ancho de la columna
						 			   'fecemisol'=>array('justification'=>'left','width'=>65), // Justificación y ancho de la columna
						 			   'denest'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna
						 			   'monsol'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detallepresupuestario($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data      // arreglo de información
		//				   ai_i         // total de registros
		//				   li_totmonsol // total de solicitudes (Montos)
		//	    		   io_pdf       // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-2);
		$la_datatit[1]=array('titulo'=>'<b> Detalle Presupuestario </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>490))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);

		$la_datatit[1]=array('numrecdoc'=>'<b>Recepcion</b>','programatica'=>'<b>Estructura Presupuestaria</b>','estcla'=>'<b>Estatus</b>',
							 'cuenta'=>'<b>Cuenta</b>','monto'=>'<b>Monto </b>');
		$la_columnas=array('numrecdoc'=>'',
						   'programatica'=>'',
						   'estcla'=>'',
						   'cuenta'=>'',
						   'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numrecdoc'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'programatica'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'estcla'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);

		$la_columnas=array('numrecdoc'=>'',
						   'programatica'=>'',
						   'estcla'=>'',
						   'cuenta'=>'',
						   'monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numrecdoc'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'programatica'=>array('justification'=>'center','width'=>170), // Justificación y ancho de la columna
						 			   'estcla'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detallecontable($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data      // arreglo de información
		//				   ai_i         // total de registros
		//				   li_totmonsol // total de solicitudes (Montos)
		//	    		   io_pdf       // Instancia de objeto pdf
		//    Description: Función que imprime el detalle del reporte
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 16/06/2007 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->ezSetDy(-2);
		$la_datatit[1]=array('titulo'=>'<b> Detalle Contable </b>');
		$la_columnas=array('titulo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo'=>array('justification'=>'center','width'=>490))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		unset($la_datatit);
		unset($la_columnas);
		unset($la_config);

		$la_datatit[1]=array('numrecdoc'=>'<b>Recepcion</b>','cuenta'=>'<b>Cuenta</b>','debe'=>'<b>Debe</b>',
							 'haber'=>'<b>Haber</b>');
		$la_columnas=array('numrecdoc'=>'',
						   'cuenta'=>'',
						   'debe'=>'',
						   'haber'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numrecdoc'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						 			   'debe'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'haber'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);

		$la_columnas=array('numrecdoc'=>'',
						   'cuenta'=>'',
						   'debe'=>'',
						   'haber'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numrecdoc'=>array('justification'=>'center','width'=>95), // Justificación y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>250), // Justificación y ancho de la columna
						 			   'debe'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'haber'=>array('justification'=>'center','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>LISTADO DE ORDENES DE PAGO</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_cxp->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_cxp->uf_obtenervalor_get("codprobenhas","");
	$ld_fecemides=$io_fun_cxp->uf_obtenervalor_get("fecemides","");
	$ld_fecemihas=$io_fun_cxp->uf_obtenervalor_get("fecemihas","");
	$li_emitida=$io_fun_cxp->uf_obtenervalor_get("emitida","");
	$li_contabilizada=$io_fun_cxp->uf_obtenervalor_get("contabilizada","");
	$li_anulada=$io_fun_cxp->uf_obtenervalor_get("anulada","");
	$li_propago=$io_fun_cxp->uf_obtenervalor_get("propago","");
	$li_pagada=$io_fun_cxp->uf_obtenervalor_get("pagada","");
	$ls_tiporeporte=$io_fun_cxp->uf_obtenervalor_get("tiporeporte",0);
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_cxp_class_reportbsf.php");
		$io_report=new sigesp_cxp_class_reportbsf();
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_select_solicitudesf1($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fecemides,$ld_fecemihas,
													   $li_emitida,$li_contabilizada,$li_anulada,$li_propago,$li_pagada); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
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
			$io_pdf->ezSetCmMargins(3.6,4.8,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(570,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numsol");
			$li_totmonsol=0;
			$ls_estretiva=$_SESSION["la_empresa"]["estretiva"];
			uf_print_encabezado_pagina($ls_titulo,$io_pdf);
			$li_i=1;
			while (!$io_report->rs_data->EOF)
			{
				$ls_numsol=$io_report->rs_data->fields["numsol"];
				$ls_nombre=$io_report->rs_data->fields["nombre"];
				$ld_fecemisol=$io_report->rs_data->fields["fecemisol"];
				$ls_estprosol=$io_report->rs_data->fields["estprosol"];
				$li_monsol=$io_report->rs_data->fields["monsol"];
				if($ls_estretiva=="B")
				{
					$li_monretiva=$io_report->uf_select_det_deducciones_solpag($ls_numsol);
					$li_monsol=$li_monsol+$li_monretiva;
				}
				switch ($ls_estprosol)
				{
					case 'E':
						$ls_denest='Emitida';
						break;
					case 'C':
						$ls_denest='Contabilizada';
						break;
					case 'A':
						$ls_denest='Anulada';
						break;
					case 'S':
						$ls_denest='Programacion de Pago';
						break;
					case 'P':
						$ls_denest='Pagada';
						break;
					case "N":
						$ls_denest="Anulada sin Afectacion";
						break;
				}
				$la_datap=array();
				$la_datac=array();
				$li_totmonsol=$li_totmonsol+$li_monsol;
				$li_monsol=number_format($li_monsol,2,",",".");
				$ld_fecemisol=$io_funciones->uf_convertirfecmostrar($ld_fecemisol);
				uf_print_solicitudes($ls_numsol,$ls_nombre,$ld_fecemisol,$ls_denest,$li_monsol,$io_pdf);
				$lb_valido=$io_report->uf_select_imputacionpresupuestaria_solicitud($ls_numsol);
				$li_j=0;
				while(!$io_report->rs_imputacionpresupuestaria->EOF)
				{
					$ls_programatica="";
					$ls_numrecdoc= $io_report->rs_imputacionpresupuestaria->fields['numrecdoc'];
					$ls_codestpro= $io_report->rs_imputacionpresupuestaria->fields['codestpro'];
					$arrResultado=$io_fun_cxp->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
					$ls_programatica=$arrResultado["as_programatica"];
					$ls_estcla= $io_report->rs_imputacionpresupuestaria->fields['estcla'];
					if($ls_estcla=="A")
					{
						$ls_estcla="Accion";
					}
					else
					{
						$ls_estcla="Proyecto";
					}
					$ls_spg_cuenta= $io_report->rs_imputacionpresupuestaria->fields['spg_cuenta'];
					$li_monto= $io_report->rs_imputacionpresupuestaria->fields['monto'];
					$li_monto=number_format($li_monto,2,",",".");
					$li_j++;
					$la_datap[$li_j]=array('numrecdoc'=>$ls_numrecdoc,'programatica'=>$ls_programatica,'estcla'=>$ls_estcla,'cuenta'=>$ls_spg_cuenta,
										  'monto'=>$li_monto);
					$io_report->rs_imputacionpresupuestaria->MoveNext();
				}
				$li_j=0;
				$lb_valido=$io_report->uf_select_imputacioncontable_solicitud($ls_numsol);
				while(!$io_report->rs_imputacioncontable->EOF)
				{
					$ls_numrecdoc= $io_report->rs_imputacioncontable->fields['numrecdoc'];
					$ls_sc_cuenta= $io_report->rs_imputacioncontable->fields['sc_cuenta'];
					$ls_debhab= $io_report->rs_imputacioncontable->fields['debhab'];
					$li_monto= $io_report->rs_imputacioncontable->fields['monto'];
					$li_monto=number_format($li_monto,2,",",".");
					if($ls_debhab=="D")
					{
						$li_montodebe=$li_monto;
						$li_montohaber="0,00";
					}
					else
					{
						$li_montohaber=$li_monto;
						$li_montodebe="0,00";
					}
					$li_j++;
					$la_datac[$li_j]=array('numrecdoc'=>$ls_numrecdoc,'cuenta'=>$ls_sc_cuenta,'debe'=>$li_montodebe,'haber'=>$li_montohaber);
					$io_report->rs_imputacioncontable->MoveNext();
				}
				if($la_datap!="")
				{
					uf_print_detallepresupuestario($la_datap,$io_pdf);
				}
				if($la_datac!="")
				{
					uf_print_detallecontable($la_datac,$io_pdf);
				}
				$li_i++;
				$io_report->rs_data->MoveNext();
			}
			$li_totmonsol=number_format($li_totmonsol,2,",",".");
		}
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		
	}

?>
