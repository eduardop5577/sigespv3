<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$as_codcatsudeban,$li_cuecon,$io_pdf)
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
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(25,40,970,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,560,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_fecha."</b>"); // Agregar el título
		if($as_codcatsudeban=="")
		{
			$as_codcatsudeban="Todas las Clasificaciones";
		}
		$li_tm=$io_pdf->getTextWidth(10,"Bienes de Clasificacion: ".$as_codcatsudeban);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,540,10,"<b>"."Bienes de Clasificacion: ".$as_codcatsudeban."</b>"); // Agregar el título
		if($li_cuecon!="")
		{
			$li_tm=$io_pdf->getTextWidth(9,"Cuenta Contable: ".$li_cuecon);
			$tm=504-($li_tm/2);
			$io_pdf->addText($tm,530,9,"<b>"."Cuenta Contable: ".$li_cuecon."</b>"); // Agregar el título
		}
		$io_pdf->addText(931,550,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(937,543,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/07/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSety(713);
        $io_pdf->setColor(0.9,0.9,0.9);
        $io_pdf->filledRectangle(50,500,500,$io_pdf->getFontHeight(11));
        $io_pdf->setColor(0,0,0);
		$la_data[1]=array('codigo'=>'<b>Código</b>',
						  'nombre'=>'<b>Nombre Fiscal</b>',
						  'telefono'=>'<b>Teléfono</b>',
						  'ubicacion'=>'<b>Ubicación</b>',
						  'responsable'=>'<b>Responsable</b>');
		$la_columna=array('codigo'=>'<b>Código</b>',
						  'nombre'=>'<b>Nombre Fiscal</b>',
						  'telefono'=>'<b>Teléfono</b>',
						  'ubicacion'=>'<b>Ubicación</b>',
						  'responsable'=>'<b>Responsable</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>730, // Ancho de la tabla
						 'maxWidth'=>730, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'left','width'=>75), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
						 			   'telefono'=>array('justification'=>'left','width'=>85), // Justificación y ancho de la columna
						 			   'ubicacion'=>array('justification'=>'left','width'=>240), // Justificación y ancho de la columna
						 			   'responsable'=>array('justification'=>'left','width'=>130))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

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
		global $ls_tipoformato;
		$ls_titulo="";
		$la_columna=array('codact'=>'<b>Código</b>',
						  'ideact'=>'<b>Identificador</b>',
						  'denact'=>'<b>Denominación</b>',
						  'fecinc'=>'<b>Incorporacion</b>',
						  'viduti'=>'<b>V.U.</b>',
						  'costo'=>'<b>Costo '.$ls_titulo.'</b>',
						  'cossal'=>'<b>Valor Rescate '.$ls_titulo.'</b>',
						  'mondep'=>'<b>Costo - VR '.$ls_titulo.'</b>',
						  'mesdep'=>'<b>Mes Dep.</b>',
						  'depmen'=>'<b>Dep. Mensual '.$ls_titulo.'</b>',
						  'depacu'=>'<b>Dep. Acum. '.$ls_titulo.'</b>',
						  'pordep'=>'<b>Por Depreciar '.$ls_titulo.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>950, // Ancho de la tabla
						 'maxWidth'=>950, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codact'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna
						 			   'ideact'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna
						 			   'denact'=>array('justification'=>'left','width'=>175), // Justificación y ancho de la columna
						 			   'fecinc'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'viduti'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'cossal'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mondep'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mesdep'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'depmen'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'depacu'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'pordep'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('total'=>'',
						  'costo'=>'',
						  'cossal'=>'',
						  'mondep'=>'',
						  'mesdep'=>'',
						  'depmen'=>'',
						  'depacu'=>'',
						  'pordep'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>445), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'cossal'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mondep'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mesdep'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'depmen'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'depacu'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'pordep'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales_generales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-10);
		$la_columna=array('total'=>'',
						  'costo'=>'',
						  'cossal'=>'',
						  'mondep'=>'',
						  'mesdep'=>'',
						  'depmen'=>'',
						  'depacu'=>'',
						  'pordep'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>445), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'cossal'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mondep'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'mesdep'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'depmen'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'depacu'=>array('justification'=>'right','width'=>75), // Justificación y ancho de la columna
						 			   'pordep'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_categoria($as_codcat,$as_dencat,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: 
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yozelin Barrgan
		// Fecha Creación: 03/09/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('total'=>"Clasificacion: ".$as_codcat." - ".$as_dencat));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>2, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'width'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'left','width'=>945))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$io_pdf->ezSetDy(-5);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	$la_data = array();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
		$ls_titulo_report="Bs.F.";
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
		$ls_titulo_report="Bs.";
	}	
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="Reporte de Depreciación Mensual en ".$ls_titulo_report." ";
	$ls_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_mes=$io_fun_activos->uf_obtenervalor_get("mes","");
	$li_anio=$io_fun_activos->uf_obtenervalor_get("anio","");
	$li_cuecon=$io_fun_activos->uf_obtenervalor_get("cuecon","");
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_codcatsudeban=$io_fun_activos->uf_obtenervalor_get("codcatsudeban","");
	$ls_soloincorporados=$io_fun_activos->uf_obtenervalor_get("soloincorporados","");
	$ls_estsudeban=$io_report->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estsudeban);
	$li_auxmes=$io_fec->uf_load_numero_mes($ls_mes);
	$ls_fecha="Periodo:  ".$ls_mes."  ".$li_anio;
	//--------------------------------------------------------------------------------------------------------------------------------
	$ld_fecdep=$io_fec->uf_last_day($li_auxmes,$li_anio);
	$ld_fecdepaux="01/".$li_auxmes."/".$li_anio;
	if($ls_estsudeban!=1)
	{
		$rs_data=$io_report->uf_saf_load_depmensual($ls_codemp,$li_ordenact,$ld_fecdep,$ld_fecdepaux,$ls_codcatsudeban,$ls_soloincorporados,$li_cuecon); // Cargar el DS con los datos de la cabecera del reporte
			/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////
			$ls_desc_event=" Generó el reporte de Depreciacion Mensual de Activos. ";
			$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_depmensual.php",$ls_desc_event);
			////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
			uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$ls_codcatsudeban,$li_cuecon,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(940,50,10,'','',1); // Insertar el número de página
			$li_totrow=$io_report->ds->getRowCount("codact");
			$li_totmondep=0;
			$li_totcosto=0;
			$li_totcossal=0;
			$li_totmondep=0;
			$li_totdepmen=0;
			$li_totdepacu=0;
			$li_totpordep=0;
			//Total General
			$li_totmondep_tot=0;
			$li_totdepmen_tot=0;
			$li_totdepacu_tot=0;
			$li_totpordep_tot=0;
			$li_totcossal_tot=0;
			$li_totcosto_tot=0;
			//Total General
			$li_i=0;
			while(!$rs_data->EOF)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacción
				$li_numpag=$io_pdf->ezPageCount; // Número de página
				$ls_codact=  $rs_data->fields["codact"];
				$ls_denact=  $rs_data->fields["denact"];
				$ls_ideact=  $rs_data->fields["ideact"];
				$li_mesdep=" -- ";
				$li_viduti=  $rs_data->fields["vidautil"];
				$li_costo=   $rs_data->fields["costo"];
				$li_cossal=  $rs_data->fields["cossal"];
				$li_depmen=  $rs_data->fields["mondepmen"];
				$li_depacu=  $rs_data->fields["mondepacu"];
				$ls_depcomp=  $rs_data->fields["depcomp"];
				$ls_fecincact=  $rs_data->fields["fecincact"];
//				$ls_codact=  $io_report->ds->data["codact"][$li_i];
//				$ls_denact=  $io_report->ds->data["denact"][$li_i];
//				$ls_ideact=  $io_report->ds->data["ideact"][$li_i];
//				$li_mesdep=" -- ";
//				$li_viduti=  $io_report->ds->data["vidautil"][$li_i];
//				$li_costo=   $io_report->ds->data["costo"][$li_i];
//				$li_cossal=  $io_report->ds->data["cossal"][$li_i];
//				$li_depmen=  $io_report->ds->data["mondepmen"][$li_i];
//				$li_depacu=  $io_report->ds->data["mondepacu"][$li_i];
//				$ls_fecincact=  $io_report->ds->data["fecincact"][$li_i];
				$ls_fecincact=$io_funciones->uf_convertirfecmostrar($ls_fecincact);
				$li_mondep= ($li_costo - $li_cossal);
				$li_pordep= ($li_mondep - $li_depacu);
				$li_totmondep=($li_totmondep + $li_mondep);
				$li_totdepmen=($li_totdepmen + $li_depmen);
				$li_totdepacu=($li_totdepacu + $li_depacu);
				$li_totpordep=($li_totpordep + $li_pordep);
				$li_totcossal=($li_totcossal + $li_cossal);
				$li_totcosto=($li_totcosto + $li_costo);
				$lb_valido1=$io_report->uf_saf_select_dt_depactivo($ls_codemp,$ls_codact,$ls_ideact);
				$li_vidutimes=($li_viduti * 12);
				if($lb_valido1)
				{
					$li_mes=1;
					$li_totrow1=$io_report->ds_detalle->getRowCount("codact");
					for($li_s=1;$li_s<=$li_totrow1;$li_s++)
					{
						$ld_fecdepact= $io_report->ds_detalle->data["fecdep"][$li_s];
						$ld_fecdepact=$io_funciones->uf_convertirfecmostrar($ld_fecdepact);
						if($ld_fecdepact!=$ld_fecdep)
						{
							$li_mes=($li_mes + 1);
						}
						else
						{break;}
					}
				}
				if($ls_depcomp==1)
				{
					$li_mesdep=0;
				}
				else
				{
					$li_mesdep=$li_mes."/".$li_vidutimes;
				}
				$li_viduti=$io_fun_activos->uf_formatonumerico($li_viduti);
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$li_cossal=$io_fun_activos->uf_formatonumerico($li_cossal);
				$li_depmen=$io_fun_activos->uf_formatonumerico($li_depmen);
				$li_depacu=$io_fun_activos->uf_formatonumerico($li_depacu);
				$li_mondep=$io_fun_activos->uf_formatonumerico($li_mondep);
				$li_pordep=$io_fun_activos->uf_formatonumerico($li_pordep);
				$li_i++;
				$la_data[$li_i]=array('codact'=>$ls_codact,'ideact'=>$ls_ideact,'denact'=>$ls_denact,'fecinc'=>$ls_fecincact,'mesdep'=>$li_mesdep,
									  'viduti'=>$li_viduti,'costo'=>$li_costo,'cossal'=>$li_cossal,'depmen'=>$li_depmen,
									  'depacu'=>$li_depacu,'mondep'=>$li_mondep,'pordep'=>$li_pordep);
				$rs_data->MoveNext();
			}
			uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
			//total general
			$li_totmondep_tot=$li_totmondep_tot+$li_totmondep;
			$li_totdepmen_tot=$li_totdepmen_tot+$li_totdepmen;
			$li_totdepacu_tot=$li_totdepacu_tot+$li_totdepacu;
			$li_totpordep_tot=$li_totpordep_tot+$li_totpordep;
			$li_totcossal_tot=$li_totcossal_tot+$li_totcossal;
			$li_totcosto_tot=$li_totcosto_tot+$li_totcosto;
			//total general
			$li_totmondep=$io_fun_activos->uf_formatonumerico($li_totmondep);
			$li_totdepmen=$io_fun_activos->uf_formatonumerico($li_totdepmen);
			$li_totdepacu=$io_fun_activos->uf_formatonumerico($li_totdepacu);
			$li_totpordep=$io_fun_activos->uf_formatonumerico($li_totpordep);
			$li_totcossal=$io_fun_activos->uf_formatonumerico($li_totcossal);
			$li_totcosto=$io_fun_activos->uf_formatonumerico($li_totcosto);
			$la_datat[1]=array('total'=>"Total ",'costo'=>$li_totcosto,'cossal'=>$li_totcossal,'mondep'=>$li_totmondep,
							   'mesdep'=>" -- ",'depmen'=>$li_totdepmen,'depacu'=>$li_totdepacu,'pordep'=>$li_totpordep);
			uf_print_totales($la_datat,$io_pdf);
		if($li_i<1) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		
		unset($la_data);			
		unset($la_data1);			
		if($li_i>1) // Existe algún error ó no hay registros
		{
			$li_totmondep_tot=$io_fun_activos->uf_formatonumerico($li_totmondep_tot);
			$li_totdepmen_tot=$io_fun_activos->uf_formatonumerico($li_totdepmen_tot);
			$li_totdepacu_tot=$io_fun_activos->uf_formatonumerico($li_totdepacu_tot);
			$li_totpordep_tot=$io_fun_activos->uf_formatonumerico($li_totpordep_tot);
			$li_totcossal_tot=$io_fun_activos->uf_formatonumerico($li_totcossal_tot);
			$li_totcosto_tot=$io_fun_activos->uf_formatonumerico($li_totcosto_tot);
			$la_datatt[1]=array('total'=>"Total General",'costo'=>$li_totcosto_tot,'cossal'=>$li_totcossal_tot,'mondep'=>$li_totmondep_tot,
									   'mesdep'=>" -- ",'depmen'=>$li_totdepmen_tot,'depacu'=>$li_totdepacu_tot,'pordep'=>$li_totpordep_tot);
			uf_print_totales_generales($la_datatt,$io_pdf);
			unset($la_datatt);
		}
			
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		unset($io_pdf);
		unset($io_report);
		unset($io_funciones);
		unset($io_fun_nomina);
	}
	else
	{
		$lb_valido=$io_report->uf_saf_load_sudeban($ls_codcatsudeban); // Cargar el DS con los datos de la cabecera del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else // Imprimimos el reporte
		{
			/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
			$ls_desc_event=" Generó el reporte de Depreciacion Mensual de Activos. ";
			$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_depmensual.php",$ls_desc_event);
			////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(940,50,10,'','',1); // Insertar el número de página
			uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$ls_codcatsudeban,$li_cuecon,$io_pdf);
			$li_totrowcat=$io_report->ds_sudeban->getRowCount("codcat");
			$i=0;
			$ld_total_costo=0;
			//Total General
			$li_totmondep_tot=0;
			$li_totdepmen_tot=0;
			$li_totdepacu_tot=0;
			$li_totpordep_tot=0;
			$li_totcossal_tot=0;
			$li_totcosto_tot=0;
			//Total General
			for($li_j=1;$li_j<=$li_totrowcat;$li_j++)
			{
				$li_numpag=$io_pdf->ezPageCount; // Número de página
				$ls_codcat=$io_report->ds_sudeban->data["codcat"][$li_j];
				$ls_dencat=$io_report->ds_sudeban->data["dencat"][$li_j];
				uf_print_categoria($ls_codcat,$ls_dencat,$io_pdf);
				$rs_data=$io_report->uf_saf_load_depmensual($ls_codemp,$li_ordenact,$ld_fecdep,$ld_fecdepaux,$ls_codcat,$ls_soloincorporados); // Cargar el DS con los datos de la cabecera del reporte
				$li_totrow=$io_report->ds->getRowCount("codact");
				$li_totmondep=0;
				$li_totcosto=0;
				$li_totcossal=0;
				$li_totmondep=0;
				$li_totdepmen=0;
				$li_totdepacu=0;
				$li_totpordep=0;
				$la_data="";
				$li_i=1;
				while(!$rs_data->EOF)
				{
					$li_i++;
					$io_pdf->transaction('start'); // Iniciamos la transacción
					$li_numpag=$io_pdf->ezPageCount; // Número de página
					$ls_codact=  $rs_data->fields["codact"];
					$ls_denact=  $rs_data->fields["denact"];
					$ls_ideact=  $rs_data->fields["ideact"];
					$ls_fecincact=  $rs_data->fields["fecincact"];
					$ls_fecincact=$io_funciones->uf_convertirfecmostrar($ls_fecincact);
					$li_mesdep=" -- ";
					$li_viduti=  $rs_data->fields["vidautil"];
					$li_costo=   $rs_data->fields["costo"];
					$li_cossal=  $rs_data->fields["cossal"];
					$li_depmen=  $rs_data->fields["mondepmen"];
					$li_depacu=  $rs_data->fields["mondepacu"];
					$ls_depcomp=  $rs_data->fields["depcomp"];
					$li_mondep= ($li_costo - $li_cossal);
					$li_pordep= ($li_mondep - $li_depacu);
					$li_totmondep=($li_totmondep + $li_mondep);
					$li_totdepmen=($li_totdepmen + $li_depmen);
					$li_totdepacu=($li_totdepacu + $li_depacu);
					$li_totpordep=($li_totpordep + $li_pordep);
					$li_totcossal=($li_totcossal + $li_cossal);
					$li_totcosto=($li_totcosto + $li_costo);
					$lb_valido1=$io_report->uf_saf_select_dt_depactivo($ls_codemp,$ls_codact,$ls_ideact);
					$li_vidutimes=($li_viduti * 12);
					if($lb_valido1)
					{
						$li_mes=1;
						$li_totrow1=$io_report->ds_detalle->getRowCount("codact");
						for($li_s=1;$li_s<=$li_totrow1;$li_s++)
						{
							$ld_fecdepact= $io_report->ds_detalle->data["fecdep"][$li_s];
							$ld_fecdepact=$io_funciones->uf_convertirfecmostrar($ld_fecdepact);
							if(($ld_fecdepact!=$ld_fecdep)&&($ld_fecdepact!=$ld_fecdepaux))
							{
								$li_mes=($li_mes + 1);
							}
							else
							{break;}
						}
					}
					if($ls_depcomp==1)
					{
						$li_mesdep=0;
					}
					else
					{
						$li_mesdep=$li_mes."/".$li_vidutimes;
					}
					$li_viduti=$io_fun_activos->uf_formatonumerico($li_viduti);
					$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
					$li_cossal=$io_fun_activos->uf_formatonumerico($li_cossal);
					$li_depmen=$io_fun_activos->uf_formatonumerico($li_depmen);
					$li_depacu=$io_fun_activos->uf_formatonumerico($li_depacu);
					$li_mondep=$io_fun_activos->uf_formatonumerico($li_mondep);
					$li_pordep=$io_fun_activos->uf_formatonumerico($li_pordep);
					$la_data[$li_i]=array('codact'=>$ls_codact,'ideact'=>$ls_ideact,'denact'=>$ls_denact,'fecinc'=>$ls_fecincact,'mesdep'=>$li_mesdep,
										  'viduti'=>$li_viduti,'costo'=>$li_costo,'cossal'=>$li_cossal,'depmen'=>$li_depmen,
										  'depacu'=>$li_depacu,'mondep'=>$li_mondep,'pordep'=>$li_pordep);
					
					
					
					$rs_data->MoveNext();
				}
				if($la_data!="")
				{
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					//total general
					$li_totmondep_tot=$li_totmondep_tot+$li_totmondep;
					$li_totdepmen_tot=$li_totdepmen_tot+$li_totdepmen;
					$li_totdepacu_tot=$li_totdepacu_tot+$li_totdepacu;
					$li_totpordep_tot=$li_totpordep_tot+$li_totpordep;
					$li_totcossal_tot=$li_totcossal_tot+$li_totcossal;
					$li_totcosto_tot=$li_totcosto_tot+$li_totcosto;
					//total general
					$li_totmondep=$io_fun_activos->uf_formatonumerico($li_totmondep);
					$li_totdepmen=$io_fun_activos->uf_formatonumerico($li_totdepmen);
					$li_totdepacu=$io_fun_activos->uf_formatonumerico($li_totdepacu);
					$li_totpordep=$io_fun_activos->uf_formatonumerico($li_totpordep);
					$li_totcossal=$io_fun_activos->uf_formatonumerico($li_totcossal);
					$li_totcosto=$io_fun_activos->uf_formatonumerico($li_totcosto);
					$la_datat[1]=array('total'=>"Total ",'costo'=>$li_totcosto,'cossal'=>$li_totcossal,'mondep'=>$li_totmondep,
									   'mesdep'=>" -- ",'depmen'=>$li_totdepmen,'depacu'=>$li_totdepacu,'pordep'=>$li_totpordep);
					uf_print_totales($la_datat,$io_pdf);
					unset($la_data);			
					unset($la_data1);			
				}
			}
			if($lb_valido)
			{
				$li_totmondep_tot=$io_fun_activos->uf_formatonumerico($li_totmondep_tot);
				$li_totdepmen_tot=$io_fun_activos->uf_formatonumerico($li_totdepmen_tot);
				$li_totdepacu_tot=$io_fun_activos->uf_formatonumerico($li_totdepacu_tot);
				$li_totpordep_tot=$io_fun_activos->uf_formatonumerico($li_totpordep_tot);
				$li_totcossal_tot=$io_fun_activos->uf_formatonumerico($li_totcossal_tot);
				$li_totcosto_tot=$io_fun_activos->uf_formatonumerico($li_totcosto_tot);
				$la_datatt[1]=array('total'=>"Total General",'costo'=>$li_totcosto_tot,'cossal'=>$li_totcossal_tot,'mondep'=>$li_totmondep_tot,
										   'mesdep'=>" -- ",'depmen'=>$li_totdepmen_tot,'depacu'=>$li_totdepacu_tot,'pordep'=>$li_totpordep_tot);
				uf_print_totales_generales($la_datatt,$io_pdf);
				unset($la_datatt);
				
				$io_pdf->ezStopPageNumbers(1,1);
				$io_pdf->ezStream();
			}
			unset($io_pdf);
			unset($io_report);
			unset($io_funciones);
			unset($io_fun_nomina);
		}
	}
?> 