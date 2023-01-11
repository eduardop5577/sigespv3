<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
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
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scg;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_situacion_financiera.html",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_titulo2,$as_titulo3,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,685,11,$as_titulo); // Agregar el título		
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo1); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,655,11,$as_titulo2); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,640,11,$as_titulo3); // Agregar el título

		$io_pdf->addText(510,760,7,$_SESSION["ls_database"]); // Agregar la Base de datos
		$io_pdf->addText(510,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(510,740,8,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data, $periodo_an, $periodo_ac, $io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'rowGap' => 1,
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('denominacion'=>array('justification'=>'left','width'=>305), // Justificación y ancho de la columna
									   'nota'=>array('justification'=>'left','width'=>80), // Justificación y ancho de la columna	
									   'periodo_ac'=>array('justification'=>'right','width'=>90),
									   'periodo_an'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$la_columnas=array('denominacion'=>'',
						   'nota'=>'<b>NOTA</b>',
						   'periodo_ac'=>"<b>{$periodo_ac}</b>",
						   'periodo_an'=>"<b>{$periodo_an}</b>");
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_is_negative($ad_monto)
	{
		if ($ad_monto<0)
		{
			return number_format(abs($ad_monto),2,",",".");
		}
		else
		{
			return number_format($ad_monto,2,",",".");
		}
	}
	
	function uf_print_firmas($io_pdf)
	{
	global $io_pdf;
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->setLineStyle(1);

		
		$io_pdf->line(45,200,160,200);
		$io_pdf->line(210,200,350,200);

		$io_pdf->addText(45,205,7,"Firma:"); // Agregar el título
		$io_pdf->addText(45,190,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(45,180,7,"Cargo:"); // Agregar el título
		$io_pdf->addText(210,205,7,"Firma:"); // Agregar el título
		$io_pdf->addText(210,190,7,"Nombre:"); // Agregar el título
		$io_pdf->addText(210,180,7,"Cargo:"); // Agregar el título
		
		$io_pdf->Rectangle(400,170,150,100);
		$io_pdf->addText(430,220,7,"SELLO INSTITUCIONAL"); // Agregar el título
	}
	
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("class_funciones_scg.php");
	require_once("sigesp_scg_class_situacionfinanciera.php");
	$io_funciones = new class_funciones();
	$io_report    = new sigesp_scg_class_situacionfinanciera();
	$io_fecha     = new class_fecha();
	$io_fun_scg   = new class_funciones_scg();
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_cmbmes=$_GET["cmbmes"];
	$ls_cmbagno=$_GET["cmbagno"];
	$ls_fecdesde = $_GET["fecdesde"];
	$ls_fechasta = $_GET["fechasta"];
	$ldt_fecdesde=$io_funciones->uf_convertirdatetobd($ls_fecdesde);
	$ldt_fechasta=$io_funciones->uf_convertirdatetobd($ls_fechasta);
	$ls_rango = $_GET["rango"];
	if ($ls_rango != '1')
	{
		$ls_cmbmes=substr($ldt_fecdesde, 5, 2);
		$ls_cmbagno=substr($ldt_fecdesde, 0, 4);
	}
	$ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b> ".$_SESSION["la_empresa"]["nombre"]." </b>";
	$ls_titulo1="<b>ESTADO DE SITUACION FINANCIERA</b>";
	if ($ls_rango != '1')
	{
		$ls_titulo2="<b> DEL ".$ls_fecdesde." AL ".$ls_fechasta."</b>";
	}
	else
	{
		$ls_titulo2="<b> AL ".substr($ls_last_day, 0, 2)." DE ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." DE ".$ls_cmbagno."</b>";
	}
	$ls_titulo3="<b>(EN BOLÍVARES)</b>";  
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido= uf_insert_seguridad("<b>Situacion Financiera en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$data=$io_report->uf_situacion_financiera($ls_cmbagno-1, $ldt_fechas, $ls_rango, $ldt_fecdesde, $ldt_fechasta); 
	}
	
	if($data===false){// Existe algún error 
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al emitir el reporte');"); 
		print(" close();");
		print("</script>");
	}	
	elseif(!$data->EOF)
	{
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.5,10,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		
		//totales y otras variables
		$ld_totalniv1     = 0;
		$ld_totalantniv1  = 0;
		$ls_dentotniv1    = '';
		$ld_totalniv2     = 0;
		$ld_totalantniv2  = 0;
		$ls_dentotniv2    = '';
		$cambioniv2       = false;
		$cambioultimo     = false;
		$nrecord          = $data->_numOfRows;
		$arrdata          = $data->GetArray();
		$li_indice        = 0;
		$li_indice_alt    = 0;
		$ld_totpasivo     = 0;
		$ld_totantpasivo  = 0;
		
		//buscar la ganancia o resultado
		$ld_ganancia = $io_report->uf_buscar_ganancia($ldt_fechas, $ls_rango, $ldt_fecdesde, $ldt_fechasta);
		foreach ($arrdata as $registro)
		{
			$ls_cuenta       = $registro['sc_cuenta'];
			$ls_denominacion = $registro['denominacion'];
			$ls_nivel        = $registro['nivel'];
			$ld_saldoant     = $registro['saldo_anterior'];
			$ld_saldo        = $registro['saldo'];
			switch ($ls_nivel)
			{
				case '1':
					//nivel uno;
					if($cambioniv2)
					{
							$cambioniv2    = false;
							$ls_dentotniv2 = 'TOTAL '.$ls_dentotniv2;
							$la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________'); 
							$la_data[] = array('denominacion'=>$ls_dentotniv2,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_totalantniv2),'periodo_ac'=>uf_is_negative($ld_totalniv2));
							$la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________');
							
							$ls_dentotniv1 = '<b>TOTAL '.$ls_dentotniv1.'</b>'; 
							$la_data[] = array('denominacion'=>$ls_dentotniv1,'nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_totalantniv1).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_totalniv1).'</b>');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'===============','periodo_ac'=>'===============');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
						
							$la_data[] = array('denominacion'=>'<b>'.$ls_denominacion.'</b>','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
							$ld_totalniv1    = $ld_saldo;
							$ld_totalantniv1 = $ld_saldoant;
							$ls_dentotniv1   = $ls_denominacion;
					}
					else
					{
						$ls_dentotniv1   = $ls_denominacion;
						$ld_totalniv1    = $ld_saldo;
						$ld_totalantniv1 = $ld_saldoant;
						$la_data[] = array('denominacion'=>'<b>'.$ls_denominacion.'</b>','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
					}
					
					if(substr($ls_cuenta, 0, 1)==$_SESSION['la_empresa']['pasivo'])
					{
						//$ld_totpasivo    = $ld_saldo;
						//$ld_totantpasivo = $ld_saldoant;
					}
					break;
				
				case '2':
					//nivel dos;
					if($cambioniv2 && !$cambioultimo)
					{
							$cambioniv2    = false;
							$ls_dentotniv2 = 'TOTAL '.$ls_dentotniv2;
							$la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________'); 
							$la_data[] = array('denominacion'=>$ls_dentotniv2,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_totalantniv2),'periodo_ac'=>uf_is_negative($ld_totalniv2));
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
						
							$la_data[] = array('denominacion'=>$ls_denominacion,'nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
							$ld_totalniv2     = $ld_saldo;
							$ld_totalantniv2  = $ld_saldoant;
							$ls_dentotniv2    = $ls_denominacion;
					}
					else
					{
						if($cambioultimo)
						{
							//echo 'aca tambien ';
							$cambioniv2    = false;
							$cambioultimo  = false;
							$ls_dentotniv2 = 'TOTAL '.$ls_dentotniv2; 
							$la_data[] = array('denominacion'=>$ls_dentotniv2,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_totalantniv2),'periodo_ac'=>uf_is_negative($ld_totalniv2));
							
							
							$ls_dentotniv1 = '<b>TOTAL '.$ls_dentotniv1.'</b>';
							$ld_totpasivo    = abs($ld_totpasivo) + abs($ld_totalniv1); //+ $ld_ganancia;
						    $ld_totantpasivo = $ld_totantpasivo+ $ld_totalantniv1;
						    $la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________'); 
						    $la_data[] = array('denominacion'=>$ls_dentotniv1,'nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_totalantniv1).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_totalniv1).'</b>');
						    $la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________');
							$la_data[] = array('denominacion'=>'<b>TOTAL PASIVO Y PATRIMONIO</b>','nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_totantpasivo).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_totpasivo).'</b>');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'===============','periodo_ac'=>'===============');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
													
							$la_data[] = array('denominacion'=>'<b>'.$ls_denominacion.'</b>','nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_saldoant).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_saldo).'</b>');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'===============','periodo_ac'=>'===============');
							$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
						}
						else
						{
							if(substr($ls_cuenta,0,1)=='4')
							{
								$ld_totalniv2    = $ld_saldo;
								$ld_totalantniv2 = $ld_saldoant;
								$ls_dentotniv2   = $ls_denominacion;
								$la_data[] = array('denominacion'=>'<b>'.$ls_denominacion.'</b>','nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_saldoant).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_saldo).'</b>');
								$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'===============','periodo_ac'=>'===============');
								$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
							}
							else
							{
								$ld_totalniv2    = $ld_saldo;
								$ld_totalantniv2 = $ld_saldoant;
								$ls_dentotniv2   = $ls_denominacion;
								$la_data[] = array('denominacion'=>$ls_denominacion,'nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
							}
						}
						
					}
					break;
					
				case '4':
					//nivel cuatro;
					if($arrdata[$li_indice+1]['nivel']=='2'||$arrdata[$li_indice+1]['nivel']=='1'){
					
						$la_data[] = array('denominacion'=>$ls_denominacion,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_saldoant),'periodo_ac'=>uf_is_negative($ld_saldo));
						$cambioniv2    = true;
					}
					else
					{
						$arrFormato = explode('-', $_SESSION["la_empresa"]["formcont"]);
						$formNiv4   = $arrFormato[0].$arrFormato[1].$arrFormato[2].$arrFormato[3];
						$longNiv4   = strlen($formNiv4);
						if (substr($ls_cuenta, 0, $longNiv4) == substr($_SESSION["la_empresa"]["c_resultad"], 0, $longNiv4))
						{
							$ld_saldo = $ld_ganancia;
						}
						$la_data[] = array('denominacion'=>$ls_denominacion,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_saldoant),'periodo_ac'=>uf_is_negative($ld_saldo));
					}
					
					if(substr($arrdata[$li_indice+1]['sc_cuenta'],0,1)=='4' && substr($ls_cuenta,0,1)!='4')
					{
						$cambioultimo   = true;
					}
					
					if($li_indice_alt+1==$nrecord && !$cambioultimo)
					{
						$ls_dentotniv2 = 'TOTAL '.$ls_dentotniv2; 
						//TOTAL ELIMINADO POR SOLICITUD DE SIGESP CONSULTORES
						//$la_data[] = array('denominacion'=>$ls_dentotniv2,'nota'=>'    ','periodo_an'=>uf_is_negative($ld_totalantniv2),'periodo_ac'=>uf_is_negative($ld_totalniv2));
							
						$ls_dentotniv1 = '<b>TOTAL '.$ls_dentotniv1.'</b>';
						$ld_totalniv1  = abs($ld_totalniv1) + $ld_ganancia;
						$ld_totpasivo    = abs($ld_totpasivo) + abs($ld_totalniv1); //+ $ld_ganancia;
						$ld_totantpasivo = $ld_totantpasivo+ $ld_totalantniv1;
						$la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________'); 
						$la_data[] = array('denominacion'=>$ls_dentotniv1,'nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_totalantniv1).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_totalniv1).'</b>');
						$la_data[] = array('denominacion'=>'','nota'=>'    ','periodo_an'=>'_________________','periodo_ac'=>'_________________');
						$la_data[] = array('denominacion'=>'<b>TOTAL PASIVO Y PATRIMONIO</b>','nota'=>'    ','periodo_an'=>'<b>'.uf_is_negative($ld_totantpasivo).'</b>','periodo_ac'=>'<b>'.uf_is_negative($ld_totpasivo).'</b>');
						$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'===============','periodo_ac'=>'===============');
						$la_data[] = array('denominacion'=>' ','nota'=>'    ','periodo_an'=>'','periodo_ac'=>'');
					}
					break;
			}
			
			if($li_indice+2<$nrecord)
			{
				$li_indice++;
			}
			
			$li_indice_alt++;
		}
		uf_print_detalle($la_data, $ls_cmbagno-1, $ls_cmbagno, $io_pdf);
		uf_print_firmas($io_pdf);
		unset($data);
		unset($arrdata);
		unset($la_data);		
		$io_pdf->ezStopPageNumbers(1,1);
		if (isset($d) && $d){
			$ls_pdfcode = $io_pdf->ezOutput(1);
			$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
			echo '<html><body>';
			echo trim($ls_pdfcode);
			echo '</body></html>';
		}
		else{
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	else {
		print("<script language=JavaScript>");
		print(" alert('No hay data para emitir el reporte');"); 
		print(" close();");
		print("</script>");
	}
	 
	unset($io_report);
    unset($io_funciones);		
?> 