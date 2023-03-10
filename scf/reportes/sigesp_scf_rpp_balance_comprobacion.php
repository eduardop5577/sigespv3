<?php
/***********************************************************************************
* @fecha de modificacion: 09/08/2022, para la version de php 8.1 
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
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/09/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_scf;
		
		$ls_descripcion="Gener? el Reporte ".$as_titulo;
		$lb_valido=$io_fun_scf->uf_load_seguridad_reporte("SCF","sigesp_scf_r_balance_comprobacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 18/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,9,$as_titulo); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,9,$as_fecha); // Agregar el t?tulo
		
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cuenta,$as_denominacion,$ad_saldo_ant,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_cuenta // cuenta
		//	    		   as_denominacion // denominacion 
		//	    		   io_pdf // Objeto PDF
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 18/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_data=array(array('name'=>'<b>Cuenta</b> '.$as_cuenta.'  -----  '.$as_denominacion.''),
		               array('name'=>'<b>Saldo Anterior</b> '.$ad_saldo_ant.' '));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 7, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCo2'=>array(0.9,0.9,0.9),
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>305, // Orientaci?n de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
    
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 18/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tama?o de Letras
						 'titleFontSize' => 7,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'xPos'=>299, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldoanterior'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>80))); // Justificaci?n y ancho de la columna
		
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'                        <b>Denominaci?n</b>',
						   'saldoanterior'=>'<b>Saldo Anterior</b>       ',
						   'debe'=>'<b>Debe</b>              ',
						   'haber'=>'<b>Haber</b>              ',
						   'saldo'=>'<b>Saldo Actual</b>         ');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($adec_totaldebe,$adec_totalhaber,$adec_total_saldo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private 
		//	    Arguments : ad_total // Total General
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing.Yozelin Barrag?n
		// Fecha Creaci?n: 18/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
		$la_data=array(array('total'=>'<b>Total '.$ls_bolivares.'</b>','debe'=>$adec_totaldebe,'haber'=>$adec_totalhaber,'saldo'=>$adec_total_saldo));
		$la_columna=array('total'=>'','debe'=>'','haber'=>'','saldo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>299, // Orientaci?n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>310), // Justificaci?n y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>80), // Justificaci?n y ancho de la columna
									   'saldo'=>array('justification'=>'right','width'=>80))); // Justificaci?n y ancho de la columna
						 			  
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m?todo realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scf;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scf[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

		require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();			
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
		require_once("../class_folder/class_funciones_scf.php");
		$io_fun_scf=new class_funciones_scf("../../");
		$ls_tiporeporte="0";
		$ls_bolivares="";
		if (array_key_exists("tiporeporte",$_GET))
		{
			$ls_tiporeporte=$_GET["tiporeporte"];
		}
		switch($ls_tiporeporte)
		{
			case "0":
				require_once("sigesp_scf_class_report.php");
				$io_report  = new sigesp_scf_class_report();
				$ls_bolivares ="Bs.";
				break;
	
			case "1":
				require_once("sigesp_scf_class_reportbsf.php");
				$io_report  = new sigesp_scf_class_reportbsf();
				$ls_bolivares ="Bs.F.";
				break;
		}
		$ia_niveles_scf[0]="";			
		uf_init_niveles();
		$li_total=count((array)$ia_niveles_scf)-1;
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	$ld_fecdes=$_GET["fecdes"];
	$ld_fechas=$_GET["fechas"];
	$ls_cuentadesde=$_GET["cuentadesde"];
	$ls_cuentahasta=$_GET["cuentahasta"];
	$li_nivel=$_GET["nivel"];
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
		$ldt_fecha=" <b>Desde  ".$ld_fecdes."  al ".$ld_fechas."</b> ";
		$ls_titulo=" <b>BALANCE DE COMPROBACION</b> ";       
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )	
	$lb_valido=uf_insert_seguridad("<b>Balance de Comprobaci?n en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_balancecomprobacion($ld_fecdes,$ld_fechas,$ls_cuentadesde,$ls_cuentahasta,$li_nivel);
	}
	 if($lb_valido==false) // Existe alg?n error ? no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	    
		//set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ldt_fecha,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_tot=$io_report->DS->getRowCount("sc_cuenta");
		$ldec_totaldebe=0;
		$ldec_totalhaber=0;
		$ldec_total_saldo=0;
        $ld_saldo=0;
		$ldec_mondeb=0;
        $ldec_monhab=0;
		for($i=1;$i<=$li_tot;$i++)
		{
		    $li_tmp=($i+1);
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_cuenta=rtrim($io_report->DS->getValue("sc_cuenta",$i));
			$li_totfil=0;
			$as_cuenta="";
			for($li=$li_total;$li>1;$li--)
			{
				$li_ant=$ia_niveles_scf[$li-1];
				$li_act=$ia_niveles_scf[$li];
				$li_fila=$li_act-$li_ant;
				$li_len=strlen($ls_cuenta);
				$li_totfil=$li_totfil+$li_fila;
				$li_inicio=$li_len-$li_totfil;
				if($li==$li_total)
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila);
				}
				else
				{
					$as_cuenta=substr($ls_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
				}
			}
			$li_fila=$ia_niveles_scf[1]+1;
			$as_cuenta=substr($ls_cuenta,0,$li_fila)."-".$as_cuenta;
			$ls_denominacion=rtrim($io_report->DS->getValue("denominacion",$i));
			$ldec_debe=$io_report->DS->getValue("debe_mes",$i);
			$ldec_haber=$io_report->DS->getValue("haber_mes",$i);
			$ldec_saldo_ant=($io_report->DS->getValue("debe_mes_ant",$i)-$io_report->DS->getValue("haber_mes_ant",$i));
			$ldec_saldo_act=$ldec_saldo_ant+$ldec_debe-$ldec_haber;
			$ldec_BalDebe=$io_report->DS->getValue("total_debe",$i);
			$ldec_BalHABER=$io_report->DS->getValue("total_haber",$i);
			
			$ldec_totaldebe=$ldec_totaldebe+$ldec_BalDebe;
			$ldec_totalhaber=$ldec_totalhaber+$ldec_BalHABER;
		   		
			$ldec_saldo=$ldec_saldo_act;
			if($ldec_debe<0)
			{
			    $ldec_debe_aux=abs($ldec_debe);
				$ldec_debe_aux=number_format($ldec_debe_aux,2,",",".");
				$ldec_debe="(".$ldec_debe_aux.")";
			}
			else
			{
			   $ldec_debe=number_format($ldec_debe,2,",",".");
			}
			if($ldec_haber<0)
			{
			    $ldec_haber_aux=abs($ldec_haber);
				$ldec_haber_aux=number_format($ldec_haber_aux,2,",",".");
				$ldec_haber="(".$ldec_haber_aux.")";
			}
			else
			{
			     $ldec_haber=number_format($ldec_haber,2,",",".");
			}
			if($ldec_saldo<0)
			{
			    $ldec_saldo_aux=abs($ldec_saldo);
				$ldec_saldo_aux=number_format($ldec_saldo_aux,2,",",".");
				$ldec_saldo="(".$ldec_saldo_aux.")";
			}
			else
			{
			    $ldec_saldo=number_format($ldec_saldo,2,",",".");
			}
			if($ldec_saldo_ant<0)
			{
			    $ldec_saldo_ant_aux=abs($ldec_saldo_ant);
				$ldec_saldo_ant_aux=number_format($ldec_saldo_ant_aux,2,",",".");
				$ldec_saldo_ant="(".$ldec_saldo_ant_aux.")";
			}
			else
			{
               $ldec_saldo_ant=number_format($ldec_saldo_ant,2,",",".");
			}
			
			$la_data[$i]=array('cuenta'=>$as_cuenta,'denominacion'=>$ls_denominacion,'saldoanterior'=>$ldec_saldo_ant,
							   'debe'=>$ldec_debe,'haber'=>$ldec_haber,'saldo'=>$ldec_saldo);

		}//for

		uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
		if($ldec_totaldebe<0)
		{
			$ldec_totaldebe_aux=abs($ldec_totaldebe);
			$ldec_totaldebe_aux=number_format($ldec_totaldebe_aux,2,",",".");
			$ldec_totaldebe="(".$ldec_totaldebe_aux.")";
		}
		else
		{
		    $ldec_totaldebe=number_format($ldec_totaldebe,2,",",".");
		}
		if($ldec_totalhaber<0)
		{
			$ldec_totalhaber_aux=abs($ldec_totalhaber);
			$ldec_totalhaber_aux=number_format($ldec_totalhaber_aux,2,",",".");
			$ldec_totalhaber="(".$ldec_totalhaber_aux.")";
		}
		else
		{
		   $ldec_totalhaber=number_format($ldec_totalhaber,2,",",".");
		}
		$ldec_total_saldo=($ldec_totaldebe-$ldec_totalhaber);
		if($ldec_total_saldo<0)
		{
			$ldec_total_saldo_aux=abs($ldec_total_saldo);
			$ldec_total_saldo_aux=number_format($ldec_total_saldo_aux,2,",",".");
			$ldec_total_saldo="(".$ldec_total_saldo_aux.")";
		}
		else
		{
		   $ldec_total_saldo=number_format($ldec_total_saldo,2,",",".");
		}
		uf_print_pie_cabecera($ldec_totaldebe,$ldec_totalhaber,$ldec_total_saldo,$io_pdf);
		unset($la_data);			
			
		$io_pdf->ezStopPageNumbers(1,1);
		if (isset($d) && $d)
		{
			$ls_pdfcode = $io_pdf->ezOutput(1);
			$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
			echo '<html><body>';
			echo trim($ls_pdfcode);
			echo '</body></html>';
		}
		else
		{
			$io_pdf->ezStream();
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
?> 