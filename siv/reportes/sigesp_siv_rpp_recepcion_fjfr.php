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
	function uf_print_encabezado_pagina($as_titulo,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_desnom // Descripci?n de la n?mina
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],51,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el t?tulo
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,11,$ad_fecha); // Agregar el t?tulo
		$io_pdf->addText(510,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(516,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_numordcom,$ad_fecrec,$as_nomfisalm,$as_denpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_numordcom //numero de orden de compra
		//	    		   ad_fecrec    // fecha de la recepcion
		//	    		   as_nomfisalm // nombre fiscal del almacen
		//	    		   as_denpro    // denominacion del proveedor
		//	    		   io_pdf       // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$as_nomfisalm=substr($as_nomfisalm,0,35);
		$as_denpro=substr($as_denpro,0,25);
		$la_data=array(array('name'=>'<b>Ord?n de Compra</b>  '.$as_numordcom.'                                                   <b>Proveedor</b>  '.$as_denpro.''),
					   array('name'=>'<b>Almacen</b>                  '.$as_nomfisalm.''),
					   array('name'=>'<b>Fecha</b>                      '.$ad_fecrec.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar L?neas
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>2	, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-5);
		global $ls_tipoformato;
		if($ls_tipoformato==0)
		{
		  $ls_titulo="Bs.";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo="Bs.F.";
		}
		$la_columna=array('articulo'=>'<b>Art?culo</b>',
						  'unidad'=>'<b>Unidad</b>',
						  'cantidad'=>'<b>Cantidad</b>',
						  'pendiente'=>'<b>Pendiente</b>',
						  'precio'=>'<b>Precio '.$ls_titulo.'</b>',
						  'total'=>'<b>Total '.$ls_titulo.'</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('articulo'=>array('justification'=>'left','width'=>160), // Justificaci?n y ancho de la columna
						 			   'unidad'=>array('justification'=>'left','width'=>60), // Justificaci?n y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'pendiente'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'precio'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'total'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalleg
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('total'=>'',
						  'totcan'=>'',
						  'totpen'=>'',
						  'vacio'=>'',
						  'totmon'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 11,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>220), // Justificaci?n y ancho de la columna
						 			   'totcan'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'totpen'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'vacio'=>array('justification'=>'right','width'=>70), // Justificaci?n y ancho de la columna
						 			   'totmon'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($as_dentipart,$ai_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: as_dentipart // denominacion del tipo de articulo
		//	   			   ai_total // Total de articulos
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>500); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$la_data=array(array('total'=>''));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificaci?n y ancho de la columna
						 			   'entradas'=>array('justification'=>'left','width'=>100), // Justificaci?n y ancho de la columna
						 			   'salidas'=>array('justification'=>'left','width'=>100))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
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
	$ls_tipoformato=$io_fun_inventario->uf_obtenervalor_get("tipoformato",0);
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_siv_class_reportbsf.php");
		$io_report=new sigesp_siv_class_reportbsf();
		$ls_titulo_report="Bs.F.";
	}
	else
	{
		require_once("sigesp_siv_class_report.php");
		$io_report=new sigesp_siv_class_report();
		$ls_titulo_report="Bs.";
	}	
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_inventario->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_inventario->uf_obtenervalor_get("hasta","");

	$ls_titulo="<b> Reporte de Entradas de Suministros ".$ls_titulo_report."</b>";
	$ls_fecha="<b> Periodo ".$ld_desde." - ".$ld_hasta."</b>";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_numorddes="";
	$li_ordenfec=$io_fun_inventario->uf_obtenervalor_get("ordenfec","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=$io_report->uf_select_recepcion($ls_codemp,"",$ld_desde,$ld_hasta,$li_ordenfec); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe alg?n error ? no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////
		$ls_desc_event="Gener? el reporte de Entradas de Suministros Desde ".$ld_desde." hasta ".$ld_hasta;
		$io_fun_inventario->uf_load_seguridad_reporte("SIV","sigesp_siv_r_recepcion.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_totrow=$io_report->ds->getRowCount("numordcom");
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
		    $io_pdf->transaction('start'); // Iniciamos la transacci?n
			$li_numpag=$io_pdf->ezPageCount; // N?mero de p?gina
			$ls_numordcom=  $io_report->ds->data["numordcom"][$li_i];
			$ls_numconrec=  $io_report->ds->data["numconrec"][$li_i];
			$ld_fecrec=     $io_report->ds->data["fecrec"][$li_i];
			$ls_nomfisalm=  $io_report->ds->data["nomfisalm"][$li_i];
			$ls_denpro=     $io_report->ds->data["nompro"][$li_i];
			$li_totcan=0;
			$li_totpen=0;
			$li_total=0;
			$ld_fecrec=$io_funciones->uf_convertirfecmostrar($ld_fecrec);
			uf_print_cabecera($ls_numordcom,$ld_fecrec,$ls_nomfisalm,$ls_denpro,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_select_dt_recepcion($ls_codemp,$ls_numordcom,$ls_numconrec);// Obtenemos el detalle del reporte
			if($lb_valido)
			{
				$ls_estatus="";
				$li_totrow_det=$io_report->ds_detalle->getRowCount("codart");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codart=     $io_report->ds_detalle->data["codart"][$li_s];
					$ls_codartpri=     $io_report->ds_detalle->data["codartpri"][$li_s];
					$li_pos=$io_report->ds_detalle->find("codart",$ls_codartpri);
					$io_report->ds_detalle->updateRow("canart",0,$li_pos);
					$io_report->ds_detalle->updateRow("montotart",0,$li_pos);
					$io_report->ds_detalle->updateRow("preuniart",0,$li_pos);
				}
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_codart=     $io_report->ds_detalle->data["codart"][$li_s];
					$ls_estartgen=     $io_report->ds_detalle->data["estartgen"][$li_s];
					$ls_denart=     $io_report->ds_detalle->data["denart"][$li_s];
					$li_canart=     $io_report->ds_detalle->data["canart"][$li_s];
					$li_penart=     $io_report->ds_detalle->data["penart"][$li_s];
					$li_preuniart=  $io_report->ds_detalle->data["preuniart"][$li_s];
					$li_montotart=  $io_report->ds_detalle->data["montotart"][$li_s];						
					$ls_unidad=     $io_report->ds_detalle->data["unidad"][$li_s];
					$li_unidad=     $io_report->ds_detalle->data["unidades"][$li_s];
					$ls_denunimed=     $io_report->ds_detalle->data["denunimed"][$li_s];
					if($ls_estartgen=="1")
					{
						$ls_denart="    ".$ls_denart;
						$ls_estatus="H";
					}
					if($ls_unidad=="D")
					{
						$ls_unidad="Detal";
					}
					else
					{
						$ls_unidad="Mayor";
						$li_canart=($li_canart / $li_unidad);
					//	$li_preuniart=($li_preuniart * $li_unidad);
					}
					$li_totcan=$li_totcan + $li_canart;
					$li_totpen=$li_totpen + $li_penart;
					$li_total=$li_total + $li_montotart;
					$li_canart=number_format($li_canart,2,",",".");
					$li_penart=number_format($li_penart,2,",",".");
					$li_preuniart=number_format($li_preuniart,2,",",".");
					$li_montotart=number_format($li_montotart,2,",",".");
					$la_data[$li_s]=array('articulo'=>$ls_denart,'unidad'=>$ls_denunimed,'cantidad'=>$li_canart,'pendiente'=>$li_penart,'precio'=>$li_preuniart,'total'=>$li_montotart);
				}
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				$li_totcan=number_format($li_totcan,2,",",".");
				$li_totpen=number_format($li_totpen,2,",",".");
				$li_total=number_format($li_total,2,",",".");
				if($ls_estatus=="H")
				{
					$li_totpen="--";
				}
				$la_data1[1]=array('total'=>'<b>Total</b>','totcan'=>$li_totcan,'totpen'=>$li_totpen,'vacio'=>'--','totmon'=>$li_total);
				uf_print_totales($la_data1,$io_pdf); // Imprimimos el detalle 
				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					$io_pdf->transaction('commit');
				}
				elseif($li_numpag>1)
				{// Hacemos un rollback de los registros, agregamos una nueva p?gina y volvemos a imprimir
					$io_pdf->transaction('rewind');
					$io_pdf->ezNewPage(); // Insertar una nueva p?gina
					uf_print_cabecera($ls_numordcom,$ld_fecrec,$ls_nomfisalm,$ls_denpro,$io_pdf); // Imprimimos la cabecera del registro
					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
					uf_print_totales($la_data1,$io_pdf); // Imprimimos el detalle 
				}
			}
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
	unset($io_fun_nomina);
?> 