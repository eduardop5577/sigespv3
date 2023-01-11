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
	
	function uf_is_negative($ad_monto)
	{
		if($ad_monto != '')
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
		else
		{
			return $ad_monto;
		}
	}
	

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "situacion_financiera.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
	require_once("class_funciones_scg.php");
	require_once("sigesp_scg_class_situacionfinanciera.php");
	$io_funciones = new class_funciones();
	$io_report    = new sigesp_scg_class_situacionfinanciera();
	$io_fecha     = new class_fecha();
	$io_fun_scg   = new class_funciones_scg();
	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros para Filtar el Reporte
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
	$fechas=$ls_last_day;
	$ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
  	//---------------------------------------------------------------------------------------------------------------------------
	//Parámetros del encabezado
	$ls_titulo   = $_SESSION["la_empresa"]["nombre"];
	$ls_titulo1  = "ESTADO DE SITUACION FINANCIERA";
	if ($ls_rango != '1')
	{
		$ls_titulo2="<b> DEL ".$ls_fecdesde." AL ".$ls_fechasta."</b>";
	}
	else
	{
		$ls_titulo2="<b> AL ".substr($ls_last_day, 0, 2)." DE ".$io_fecha->uf_load_nombre_mes($ls_cmbmes)." DE ".$ls_cmbagno."</b>";
	}
	$ls_titulo3  = "(EN BOLÍVARES)";  
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
	$lb_valido=uf_insert_seguridad("<b>Situacion Financiera en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$data=$io_report->uf_situacion_financiera($ls_cmbagno-1, $ldt_fechas, $ls_rango, $ldt_fecdesde, $ldt_fechasta); 
	}
	
	//---------------------------------------------------------------------------------------------------------------------------
	// Impresión de la información encontrada en caso de que exista
	if($data===false)
	{// Existe algún error 
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al emitir el reporte');"); 
		print(" close();");
		print("</script>");
	}	
	elseif(!$data->EOF)
	{
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_subtitulo= &$lo_libro->addformat();
		$lo_subtitulo->set_bold();
		$lo_subtitulo->set_font("Verdana");
		$lo_subtitulo->set_align('left');
		$lo_subtitulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat();//array('num_format' => '#,##0.00')
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');

		$lo_hoja->set_column(0,0,50);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,5,20);
		
		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_titulo1,$lo_encabezado);
		$lo_hoja->write(2, 2, $ls_titulo2,$lo_encabezado);
		$lo_hoja->write(3, 2, $ls_titulo3,$lo_encabezado);
		$lo_hoja->write(6, 0, "",$lo_titulo);
		$lo_hoja->write(6, 1, "Nota",$lo_titulo);
		$lo_hoja->write(6, 3, $ls_cmbagno,$lo_titulo);
		$lo_hoja->write(6, 4, $ls_cmbagno-1,$lo_titulo);
		
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
		$ld_totpasivo     = 0;
		$ld_totantpasivo  = 0;
		
		//buscar la ganancia o resultado
		$ld_ganancia = $io_report->uf_buscar_ganancia($ldt_fechas, $ls_rango, $ldt_fecdesde, $ldt_fechasta);
		$li_row = 7;
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
							$lo_hoja->write($li_row, 0, $ls_dentotniv2, $lo_dataleft);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totalniv2), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totalantniv2), $lo_dataright);
							
							
							$li_row++;
							$ls_dentotniv1 = 'TOTAL '.$ls_dentotniv1; 
							$lo_hoja->write($li_row, 0, $ls_dentotniv1, $lo_subtitulo);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totalniv1), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totalantniv1), $lo_dataright);
							
							//linea en blanco
							$li_row++;
							$lo_hoja->write($li_row);
							
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_subtitulo);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$ld_totalniv1    = $ld_saldo;
							$ld_totalantniv1 = $ld_saldoant;
							$ls_dentotniv1   = $ls_denominacion;
					}
					else
					{
						$ls_dentotniv1   = $ls_denominacion;
						$ld_totalniv1    = $ld_saldo;
						$ld_totalantniv1 = $ld_saldoant;
						$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_subtitulo);
						$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
					}
					
					if(substr($ls_cuenta, 0, 1)==$_SESSION['la_empresa']['pasivo'])
					{
						$ld_totpasivo    = $ld_saldo;
						$ld_totantpasivo = $ld_saldoant;
					}
					break;
				
				case '2':
					//nivel dos;
					if($cambioniv2 && !$cambioultimo)
					{
							$cambioniv2    = false;
							$ls_dentotniv2 = 'TOTAL '.$ls_dentotniv2;
							//linea en blanco
							$lo_hoja->write($li_row, 0, $ls_dentotniv2, $lo_dataleft);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totalniv2), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totalantniv2), $lo_dataright);
							$li_row++;
							//linea en blanco
							$lo_hoja->write($li_row);

							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
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
							$lo_hoja->write($li_row);
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_dentotniv2, $lo_dataleft);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totalniv2), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totalantniv2), $lo_dataright);
							$li_row++;
							//linea en blanco
							$lo_hoja->write($li_row);

							$li_row++;
							$ls_dentotniv1 = 'TOTAL '.$ls_dentotniv1;
							$ld_totpasivo    = abs($ld_totpasivo) + abs($ld_totalniv1) + $ld_ganancia;
						    $ld_totantpasivo = $ld_totantpasivo+ $ld_totalantniv1;
						    $lo_hoja->write($li_row, 0, $ls_dentotniv1, $lo_subtitulo);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totalniv1), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totalantniv1), $lo_dataright);
							$li_row++;
							$lo_hoja->write($li_row, 0, 'TOTAL PASIVO Y PATRIMONIO', $lo_subtitulo);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_totpasivo), $lo_dataright);
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_totantpasivo), $lo_dataright);
						    
							$li_row++;
							$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
							$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							$lo_hoja->write($li_row, 3, uf_is_negative($ld_saldo), $lo_dataright);						
							$lo_hoja->write($li_row, 4, uf_is_negative($ld_saldoant), $lo_dataright);
						}
						else
						{
							if(substr($ls_cuenta,0,1)=='4')
							{
								$ld_totalniv2    = $ld_saldo;
								$ld_totalantniv2 = $ld_saldoant;
								$ls_dentotniv2   = $ls_denominacion;
								$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
								$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
								$lo_hoja->write($li_row, 3, uf_is_negative($ld_saldo), $lo_dataright);
								$lo_hoja->write($li_row, 4, uf_is_negative($ld_saldoant), $lo_dataright);
							}
							else
							{
								$ld_totalniv2    = $ld_saldo;
								$ld_totalantniv2 = $ld_saldoant;
								$ls_dentotniv2   = $ls_denominacion;
								$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
								$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
							}
						}
					}
					break;
					
				case '4':
					//nivel cuatro;
					if($arrdata[$li_indice+1]['nivel']=='2'||$arrdata[$li_indice+1]['nivel']=='1')
					{
						$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
						$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
						$lo_hoja->write($li_row, 3, uf_is_negative($ld_saldo), $lo_dataright);
						$lo_hoja->write($li_row, 4, uf_is_negative($ld_saldoant), $lo_dataright);
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
						$lo_hoja->write($li_row, 0, $ls_denominacion, $lo_dataleft);
						$lo_hoja->write($li_row, 1, '    ', $lo_dataleft);
						$lo_hoja->write($li_row, 3, uf_is_negative($ld_saldo), $lo_dataright);
						$lo_hoja->write($li_row, 4, uf_is_negative($ld_saldoant), $lo_dataright);
					}
					
					if(substr($arrdata[$li_indice+1]['sc_cuenta'],0,1)=='4' && substr($ls_cuenta,0,1)!='4')
					{
						$cambioultimo   = true;
					}
					break;
			}
			if($li_indice+2<$nrecord)
			{
				$li_indice++;
			}
			$li_row++;
		}
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"situacion_financiera.xls\"");
		header("Content-Disposition: inline; filename=\"situacion_financiera.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
?> 