<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
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
		print "opener.document.form1.submit();";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$ad_fecdesde,$ad_fechasta,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 06/02/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$li_alto=$_SESSION["ls_height"];
		$io_pdf->convertir_valor_px_mm($li_alto);
		$li_ancho=$_SESSION["ls_width"];
		$io_pdf->convertir_valor_px_mm($li_ancho);
		$li_altura_logo=20;
		$io_pdf->convertir_valor_mm_px($li_altura_logo);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,(792-$li_altura_logo),$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->add_texto(65,5,13,$as_titulo);
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		
		//Fecha y hora
		$io_pdf->add_texto(160,30,7,"<b>Fecha de Impresión: ".date("d/m/Y")."</b>");
		$io_pdf->add_texto(160,33,7,"<b>Hora de Impresión: ".date("G:i:s")."</b>");
		$io_pdf->add_texto(10,15,9,"<b><i>".$_SESSION["la_empresa"]["nombre"]."</i></b>");
		/*//Banco
		$io_pdf->add_texto(10,30,10,"<b>BANCO:                    ".$ls_nomban."</b>");
		//Tipo de Cuenta
		$io_pdf->add_texto(10,35,10,"<b>TIPO DE CUENTA:  ".$ls_tipcta."</b>");
		//Cuenta
		$io_pdf->add_texto(10,40,10,"<b>CUENTA:                  ".$ls_ctaban."</b>");
		//Listado de 
		$io_pdf->add_texto(10,50,10,"<b>LISTADO DE:           ".$ls_tipolistado."</b>");		
		if ($ls_conceptos!="")
		{
			// Conceptos
			$io_pdf->add_texto(10,55,9,"<b>TIPO CONCEPTO:       ".$ls_conceptos."</b>");		
		}*/
		//Rango de Fechas
		if(($ad_fecdesde!="") && ($ad_fechasta!=""))
			$io_pdf->add_texto(10,30,9,"<b>PERIODO DESDE: ".$ad_fecdesde." HASTA ".$ad_fechasta."</b>");	
		elseif(($ad_fecdesde!="") && ($ad_fechasta==""))
			$io_pdf->add_texto(10,30,9,"<b>PERIODO DESDE: ".$ad_fecdesde." HASTA LA FECHA ACTUAL</b>");	
		elseif(($ad_fecdesde=="") && ($ad_fechasta!=""))
			$io_pdf->add_texto(10,30,9,"<b>PERIODO HASTA: ".$ad_fechasta."</b>");
		else
			$io_pdf->add_texto(10,30,9,"<b>TODAS LAS FECHAS</b>");		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_dataimprimir,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: $la_data
		//	    		   io_pdf // 
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. laura Cabre
		// Fecha Creación: 06/02/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->set_margenes(70,40,3,3);
		$la_columnas=array('documento'=>'<b>Documento</b>',
						   'ncontrol'=>'<b>Nº Control Interno</b>',	
						   'fecha'=>'<b>Fecha</b>',	
						   'proveedor'=>'<b>Proveedor/Beneficiario</b>',					
						   'conmov'=>'<b>Concepto</b>',
						   'monto'=>'<b>Monto</b>',
						   'status'=>'<b>Estatus</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('documento'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'ncontrol'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>60),// Justificación y ancho de la columna
						 			   'proveedor'=>array('justification'=>'center','width'=>110), // Justificación y ancho de la columna
									   'conmov'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'center','width'=>80),// Justificación y ancho de la columna
									   'status'=>array('justification'=>'center','width'=>45))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_dataimprimir,$la_columnas,'',$la_config);
		unset($la_dataimprimir);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_doc_origen($as_numdoc,$as_nomban,$as_cuenta,$ad_fecmov,$as_conmov,$ad_mon,$io_pdf)
	{
		//$io_pdf->ezSetY(650);
		$la_data[1]=array('name'=>'<b><i>Documento Origen</i></b>', 'monto'=>$as_numdoc);
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Justificación y ancho de la columna
						 'xPos'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b><i>Número del Documento:</i></b>', 'monto'=>$as_numdoc);
		$la_data[2]=array('name'=>'<b><i>Nombre del Banco:</i></b>', 'monto'=>$as_nomban);
		$la_data[3]=array('name'=>'<b><i>Nro. de Cuenta:</i></b>', 'monto'=>$as_cuenta);
		$la_data[4]=array('name'=>'<b><i>Fecha de la Transacción:</i></b>', 'monto'=>$ad_fecmov);
		$la_data[5]=array('name'=>'<b><i>Concepto:</i></b>', 'monto'=>$as_conmov);
		$la_data[6]=array('name'=>'<b><i>Monto Transferido:</i></b>', 'monto'=>$ad_mon);
		$la_columna=array('name'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Justificación y ancho de la columna
						 'xPos'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'left','width'=>430))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		return $io_pdf;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_doc_destino($as_numdoc,$as_nomban,$as_cuenta,$ad_fecmov,$as_conmov,$ad_mon,$io_pdf)
	{
		$la_data[1]=array('name'=>'<b><i>Documento Destino</i></b>', 'monto'=>$as_numdoc);
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 12, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Justificación y ancho de la columna
						 'xPos'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>550))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$la_data[1]=array('name'=>'<b><i>Número del Documento:</i></b>', 'monto'=>$as_numdoc);
		$la_data[2]=array('name'=>'<b><i>Nombre del Banco:</i></b>', 'monto'=>$as_nomban);
		$la_data[3]=array('name'=>'<b><i>Nro. de Cuenta:</i></b>', 'monto'=>$as_cuenta);
		$la_data[4]=array('name'=>'<b><i>Fecha de la Transacción:</i></b>', 'monto'=>$ad_fecmov);
		$la_data[5]=array('name'=>'<b><i>Concepto:</i></b>', 'monto'=>$as_conmov);
		$la_data[6]=array('name'=>'<b><i>Monto Recibido:</i></b>', 'monto'=>$ad_mon);
		$la_columna=array('name'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'width'=>200, // Ancho de la tabla
						 'maxWidth'=>200, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Justificación y ancho de la columna
						 'xPos'=>310,
						 'cols'=>array('name'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
						 			   'monto'=>array('justification'=>'left','width'=>430))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		unset($la_data);
		unset($la_columna);
		unset($la_config);
		$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
		return $io_pdf;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("sigesp_scb_class_report.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");

	$sig_inc   = new sigesp_include();
	$con       = $sig_inc->uf_conectar();
	$io_report = new sigesp_scb_class_report($con);
	$in        = new sigesp_include();
	$con       = $in->uf_conectar();
	$io_sql    = new class_sql($con);	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp    	= $_SESSION["la_empresa"]["codemp"];
	$ld_fecdesde  	= $_GET["fecdes"];
	$ld_fechasta  	= $_GET["fechas"];
	$ls_codban    	= $_GET["codban"];
	$ls_nomban    	= $_GET["nomban"];
	$ls_ctaban    	= $_GET["ctaban"];
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	$ls_numdocmov   = $_GET["documento"];
	$ls_numdocchk   = $_GET["chkados"];
	$ls_conchk 		= $_GET["chked"];

	//Opción para los selectivos
	$lr_numdocchk= explode('>>',$ls_numdocchk);
    $lr_datos= array_unique($lr_numdocchk);
    $li_total= count((array)$lr_datos);
	//print_r ($lr_datos)."<br>";
	sort($lr_datos,SORT_STRING);
	//Opción para los selectivos
	
	global $ls_tiporeporte;
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_scb_class_reportbsf.php");
		$io_report = new sigesp_scb_class_reportbsf($con);
		$ls_tipbol = 'Bs.F.';
	}
	$ls_titulo    		= "<b>Listado de Transferencias Bancarias</b>";
	$ldec_totaldebitos  = 0;
	$ldec_totalcreditos = 0;
	$ldec_saldo         = 0;
	$lb_valido          = true;
	
	set_time_limit(1800);
	$io_pdf=new class_pdf('LETTER','portrait');
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(5,6.5,3,3); // Configuración de los margenes en centímetros
	if ($ls_conchk==1)
	{
		$i=0;
		$li_check=0;
		$ld_totanu = 0;
		$ld_totdeb = 0;
		$ld_totcre = 0;
		for($li_p=0;$li_p<$li_total;$li_p++)
		{
			$li_check++;
			$ls_numdocmov		= $lr_datos[$li_p];
			$lb_valido=true;
			$rs_data            = $io_report->uf_cargar_transferencias($ld_fecdesde,$ld_fechasta,$ls_codban,$ls_ctaban,$ls_numdocmov,$ls_conchk);
			if($lb_valido)
			{
				if ($li_check==1)
				{
					$io_pdf=uf_print_encabezado_pagina($ls_titulo,$ld_fecdesde,$ld_fechasta,$io_pdf); // Imprimimos el encabezado de la página
				}
				while (!$rs_data->EOF)		
					{
						$i++;
						$io_pdf->transaction('start'); // Iniciamos la transacción
						$li_numpag    = $io_pdf->ezPageCount; // Número de página			
						$ls_numdoc    = $rs_data->fields["numdoc"];
						$ldec_monto   = $rs_data->fields["monto"]; 
						$ld_fecmov    = $rs_data->fields["fecmov"];
						$ld_fecmov2    = $rs_data->fields["fecmov"];
						$ld_fecmov    = $io_report->fun->uf_convertirfecmostrar($ld_fecmov);
						$ls_nombre    = $rs_data->fields["nomproben"];
						$ls_codopebd  = $rs_data->fields["codope"]; 
						$ls_conmov    = $rs_data->fields["conmov"];
						$ls_codbanco 	  = $rs_data->fields["codban"];
						$ls_cuenta    = $rs_data->fields["ctaban"];
						$ls_nomban    = $rs_data->fields["nomban"];	
						$ls_numdoc_trans = $rs_data->fields["docdestrans"];
						if (strlen($ls_conmov)>48)
					    {
							$ls_conmov=substr($ls_conmov,0,90)."..";
					    }
						$ld_mon=number_format($ldec_monto,2,",",".");
						$io_pdf=uf_print_detalle_doc_origen($ls_numdoc,$ls_nomban,$ls_cuenta,$ld_fecmov,$ls_conmov,$ld_mon,$io_pdf);
						$lb_valido2=true;
						$rs_data_detalle= $io_report->uf_cargar_transferencias_receptoras($ls_numdoc_trans,$ld_fecmov2,$ls_conchk);
						if ($lb_valido2)
						{
							while (!$rs_data_detalle->EOF)		
							{
								$ls_numdoc_d    = $rs_data_detalle->fields["numdoc"];
								$ldec_monto_d   = $rs_data_detalle->fields["monto"]; 
								$ld_fecmov_d    = $rs_data_detalle->fields["fecmov"];
								$ld_fecmov_d    = $io_report->fun->uf_convertirfecmostrar($ld_fecmov);
								$ls_nombre_d    = $rs_data_detalle->fields["nomproben"];
								$ls_codopebd_d  = $rs_data_detalle->fields["codope"]; 
								$ls_conmov_d    = $rs_data_detalle->fields["conmov"];
								$ls_codbanco_d 	  = $rs_data_detalle->fields["codban"];
								$ls_cuenta_d    = $rs_data_detalle->fields["ctaban"];
								$ls_nomban_d    = $rs_data_detalle->fields["nomban"];	
								$ls_numdoc_trans_d = $rs_data_detalle->fields["docdestrans"];
								if (strlen($ls_conmov_d)>48)
								{
									$ls_conmov_d=substr($ls_conmov_d,0,90)."..";
								}
								$ld_mon_d=number_format($ldec_monto_d,2,",",".");
								$io_pdf=uf_print_detalle_doc_destino($ls_numdoc_d,$ls_nomban_d,$ls_cuenta_d,$ld_fecmov_d,$ls_conmov_d,$ld_mon_d,$io_pdf);
								$rs_data_detalle->MoveNext();
							}
						}
						$rs_data->MoveNext();
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
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			//print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
?> 