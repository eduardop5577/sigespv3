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
		$lb_valido=$io_fun_scf->uf_load_seguridad_reporte("SCF","sigesp_scf_r_balance_general.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_titulo1,$as_titulo2,$as_titulo3,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,710,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,715,11,$as_titulo); // Agregar el t?tulo		
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo1);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,700,11,$as_titulo1); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo2);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,685,11,$as_titulo2); // Agregar el t?tulo
		
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo3);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_titulo3); // Agregar el t?tulo

		$io_pdf->addText(510,730,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(510,720,8,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$la_data_p,$la_data_t,$total_activo_t,$ls_total_pasivo, $total_pasivo_result,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_data_TT=array(array('titulo'=>'<b>CUENTAS DEL TESORO</b>'));
		$la_columna_TT=array('titulo'=>'',);
		$la_config_TT=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>560, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('titulo'=>array('justification'=>'center','width'=>560))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_TT,$la_columna_TT,'',$la_config_TT);	
		$la_data_T=array(array('activo'=>'<b>ACTIVO</b>','pasivo'=>'<b>PASIVO</b>'));
		$la_columna=array('activo'=>'','pasivo'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>560, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('activo'=>array('justification'=>'center','width'=>300), // Justificaci?n y ancho de la columna
						 			   'pasivo'=>array('justification'=>'center','width'=>260))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_T,$la_columna,'',$la_config);	
			
		//$io_pdf->ezSetY(640);		
		$io_pdf->ezSetY(620);		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla
						 'maxWidth'=>560, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo'=>array('justification'=>'right','width'=>100),
						 			   'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta'=>'',
						   'denominacion'=>'',
						   'saldo'=>'',
						   'vacio'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);	
		//--------------------------------------------------------------------------------------------------------------------
		//$io_pdf->ezSetY(640);
		$io_pdf->ezSetY(620);		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_p'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_p'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_p'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_p'=>'',
						   'denom_p'=>'',
						   'saldo_p'=>'');
		$io_pdf->ezTable($la_data_p,$la_columnas,'',$la_config);
		//-----------------------------------------------------------------------------------------------------------------------
		
		//$io_pdf->ezSetY(575);
		$io_pdf->ezSetY(500);
		$la_data_total_p[1]=array('total'=>'','vacio'=>'<b>----------------------</b>');
		$la_data_total_p[2]=array('total'=>'<b>Sub Total</b>','vacio'=>'<b>'.$ls_total_pasivo.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>150),
						               'vacio'=>array('justification'=>'right','width'=>110))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_p,$la_columnas,'',$la_config);
		
		//------------------------------------------------------------------------------------------------------------------------
		
		//$io_pdf->ezSetY(535);
		$io_pdf->ezSetY(410);
		$la_data_total_p[1]=array('total'=>'','vacio'=>'<b>----------------------</b>');
		$la_data_total_p[2]=array('total'=>'','vacio'=>'<b>'.$total_pasivo_result.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>150),
						               'vacio'=>array('justification'=>'right','width'=>110))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_p,$la_columnas,'',$la_config);
		
		//----------------------------------------------------------------------------------------------------------------------
		
		//$io_pdf->ezSetY(540);
		$io_pdf->ezSetY(460);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_t'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_t'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_t'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_t'=>'',
						   'denom_t'=>'',
						   'saldo_t'=>'');
		$io_pdf->ezTable($la_data_t,$la_columnas,'',$la_config);
		
		
		//$io_pdf->ezSetY(540);
		$io_pdf->ezSetY(410);
		$la_data_total[1]=array('total'=>'<b>----------------------</b>','vacio'=>'');
		$la_data_total[2]=array('total'=>'<b>'.$total_activo_t.'</b>','vacio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>240),
						               'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle_2($la_data,$la_data_p_h,$ad_tot_act_hac,$ad_tot_pas_hac,$ad_resultado=0,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$io_pdf->ezSetY(470);
		$io_pdf->ezSetY(350);
		$la_data_T=array(array('titulo'=>'<b>CUENTAS DE LA HACIENDA</b>'));
		$la_columna=array('titulo'=>'',);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>560, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('titulo'=>array('justification'=>'center','width'=>560))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_T,$la_columna,'',$la_config);	
					
		//$io_pdf->ezSetY(450);		
		$io_pdf->ezSetY(330);		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla
						 'maxWidth'=>560, // Ancho M?ximo de la tabla
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_h'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_h'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_h'=>array('justification'=>'right','width'=>100),
						 			   'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_h'=>'',
						   'denom_h'=>'',
						   'saldo_h'=>'',
						   'vacio'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		$io_pdf->ezSetY(160);
		$la_data_total[1]=array('total'=>'<b>----------------------</b>','vacio'=>'');
		$la_data_total[2]=array('total'=>'<b>'.$ad_tot_act_hac.'</b>','vacio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>240),
						               'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total,$la_columnas,'',$la_config);
			
		//--------------------------------------------------------------------------------------------------------------------
		//$io_pdf->ezSetY(450);
		$io_pdf->ezSetY(330);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_p_h'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_p_h'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_p_h'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_p_h'=>'',
						   'denom_p_h'=>'',
						   'saldo_p_h'=>'');
		if($ad_resultado<>0)
		{
		 $ls_denominacion = "SUPERAVIT DE LA HACIENDA";
		 $ld_saldo_resultado= number_format($ad_resultado,2,",",".");	
		 if($ad_resultado<0)
		 {
		  $ls_denominacion = "DEFICIT DE LA HACIENDA";
		  $ld_saldo_resultado="(".number_format(abs($ad_resultado),2,",",".").")";		
		 }
		
		 $li_total_p_h = count((array)$la_data_p_h);
		 $la_data_p_h[$li_total_p_h+1]=array('cuenta_p_h'=>"",'denom_p_h'=>$ls_denominacion,'saldo_p_h'=>$ld_saldo_resultado); 
		}
		$io_pdf->ezTable($la_data_p_h,$la_columnas,'',$la_config);
		
		$io_pdf->ezSetY(160);
		$la_data_total_p[1]=array('total'=>'','vacio'=>'<b>----------------------</b>');
		$la_data_total_p[2]=array('total'=>'','vacio'=>'<b>'.$ad_tot_pas_hac.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>150),
						               'vacio'=>array('justification'=>'right','width'=>110))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_p,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_detalle_3($la_data,$la_data_g,$ls_total_ingreso,$ls_total_gastos,$ad_resultado=0,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 28/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetY(630);
		$la_data_T=array(array('titulo'=>'<b>CUENTAS DE PRESUPUESTO</b>'));
		$la_columna=array('titulo'=>'',);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>560, // Ancho M?ximo de la tabla
						 'colGap'=>1, // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('titulo'=>array('justification'=>'center','width'=>560))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_T,$la_columna,'',$la_config);				
		
		//--------------------------------------------------------------------------------------------------------------------
         if($ad_resultado<>0)
		{
		 $ls_denominacion = "SUPERAVIT";
		 $ld_saldo_resultado= number_format($ad_resultado,2,",",".");	
		 if($ad_resultado<0)
		 {
		  $ls_denominacion = "DEFICIT";
		  $ld_saldo_resultado=number_format(abs($ad_resultado),2,",",".");
		  $li_total_ingresos = count((array)$la_data);
		  $la_data[$li_total_ingresos+1]=array('cuenta_i'=>'',
						                       'denom_i'=>$ls_denominacion,
						                       'saldo_i'=>$ld_saldo_resultado); 		
		 }
		 else
		 {
		  $li_total_gastos = count((array)$la_data_g);
		  $la_data_g[$li_total_gastos+1]=array('cuenta_g'=>'',
						                       'denom_g'=>$ls_denominacion,
						                       'saldo_g'=>$ld_saldo_resultado,
						                       'vacio'=>'');
		 }
		}
		$io_pdf->ezSetY(610);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_g'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_g'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_g'=>array('justification'=>'right','width'=>100),
						 			   'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_g'=>'',
						   'denom_g'=>'',
						   'saldo_g'=>'',
						   'vacio'=>'');
		$io_pdf->ezTable($la_data_g,$la_columnas,'',$la_config);
		
		$io_pdf->ezSetY(610);	
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla
						 'maxWidth'=>560, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_i'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_i'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_i'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_i'=>'',
						   'denom_i'=>'',
						   'saldo_i'=>'');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		
		//------------------------------------------------------------------------------------------------------------------------
		$io_pdf->ezSetY(500);
		$la_data_total_i[1]=array('total'=>'','vacio'=>'<b>----------------------</b>');
		$la_data_total_i[2]=array('total'=>'','vacio'=>'<b>'.$ls_total_ingreso.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>20),
						               'vacio'=>array('justification'=>'right','width'=>240))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_i,$la_columnas,'',$la_config);
		//------------------------------------------------------------------------------------------------------------------	
		//------------------------------------------------------------------------------------------------------------------------
		//$io_pdf->ezSetY(170);
		$io_pdf->ezSetY(500);
		$la_data_total_g[1]=array('total'=>'<b>----------------------</b>','vacio'=>'');
		$la_data_total_g[2]=array('total'=>'<b>'.$ls_total_gastos.'</b>','vacio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>240),
						               'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_g,$la_columnas,'',$la_config);
		//------------------------------------------------------------------------------------------------------------------	
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_cuentas_orden($aa_data_acreedora,$aa_data_deudora,$ad_total_acreedora,$ad_total_deudora,$io_pdf)
	{
	 	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cuentas_orden
		//		    Acess: private 
		//	    Arguments: $aa_data_acreedora  // Arreglo con los datos de las cuentas acreedoras
		//                 $aa_data_deudora    // Arreglo con los datos de las cuentas deudoras
		//                 $ad_total_acreedora // Total de las Cuentas Acreedoras
		//                 $ad_total_deudora   // Total de las Cuentas Deudoras
		//	   			   io_pdf            // Objeto PDF
		//    Description: funci?n que imprime el detalle de las Cuentas de Orden
		//	   Creado Por: Ing. Arnaldo Su?rez
		// Fecha Creaci?n: 30/04/2010 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$la_data_CO=array(array('titulo'=>'<b>CUENTAS DE ORDEN</b>'));
		$la_columna=array('titulo'=>'',);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0,    // Mostrar L?neas
						 'fontSize' => 9,   // Tama?o de Letras
						 'shaded'=>0,       // Sombra entre l?neas
						 'width'=>560,      // Ancho M?ximo de la tabla
						 'colGap'=>1,       // separacion entre tablas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('titulo'=>array('justification'=>'center','width'=>560))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data_CO,$la_columna,'',$la_config);				
		
		//--------------------------------------------------------------------------------------------------------------------
		$io_pdf->ezSetY(620);
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_deudora'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_deudora'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_deudora'=>array('justification'=>'right','width'=>100),
						 			   'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_deudora'=>'',
						   'denom_deudora'=>'',
						   'saldo_deudora'=>'',
						   'vacio'=>'');
		$io_pdf->ezTable($aa_data_deudora,$la_columnas,'',$la_config);
		
		$io_pdf->ezSetY(620);	
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla
						 'maxWidth'=>560, // Ancho M?ximo de la tabla
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('cuenta_acreedora'=>array('justification'=>'left','width'=>20), // Justificaci?n y ancho de la columna
						 			   'denom_acreedora'=>array('justification'=>'left','width'=>140), // Justificaci?n y ancho de la columna
						 			   'saldo_acreedora'=>array('justification'=>'right','width'=>100))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta_acreedora'=>'',
						   'denom_acreedora'=>'',
						   'saldo_acreedora'=>'');
		$io_pdf->ezTable($aa_data_acreedora,$la_columnas,'',$la_config);
		
		//------------------------------------------------------------------------------------------------------------------------
		$io_pdf->ezSetY(535);
		$la_data_total_acreedora[1]=array('total'=>'','vacio'=>'<b>----------------------</b>');
		$la_data_total_acreedora[2]=array('total'=>'','vacio'=>'<b>'.$ad_total_acreedora.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>20),
						               'vacio'=>array('justification'=>'right','width'=>240))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_acreedora,$la_columnas,'',$la_config);
		//------------------------------------------------------------------------------------------------------------------	
		//------------------------------------------------------------------------------------------------------------------------
		$io_pdf->ezSetY(535);
		$la_data_total_deudora[1]=array('total'=>'<b>----------------------</b>','vacio'=>'');
		$la_data_total_deudora[2]=array('total'=>'<b>'.$ad_total_deudora.'</b>','vacio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>240),
						               'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_deudora,$la_columnas,'',$la_config);
		
	}
	
	function uf_print_detalle_4($ai_posy,$ad_total_activos=0,$ad_total_pasivos=0,$io_pdf)
	{
		global $io_pdf;
	    $io_pdf->ezSetY($ai_posy);
		$la_data_total_pasivos[1]=array('total'=>'<b>===============</b>','vacio'=>'');
		$la_data_total_pasivos[2]=array('total'=>'<b>'.$ad_total_activos.'</b>','vacio'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'left', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>240),
						               'vacio'=>array('justification'=>'right','width'=>10))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_pasivos,$la_columnas,'',$la_config);
		
		
		$io_pdf->ezSetY($ai_posy);
		$la_data_total_activos[1]=array('total'=>'','vacio'=>'<b>===============</b>');
		$la_data_total_activos[2]=array('total'=>'','vacio'=>'<b>'.$ad_total_pasivos.'</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 10, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>560, // Ancho de la tabla		
						 'maxWidth'=>560, // Ancho M?ximo de la tabla				
						 'xOrientation'=>'right', // Orientaci?n de la tabla
						 'cols'=>array('total'=>array('justification'=>'right','width'=>20),
						               'vacio'=>array('justification'=>'right','width'=>240))); // Justificaci?n y ancho de la columna
		$la_columnas=array('total'=>'','vacio'=>'');
		$io_pdf->ezTable($la_data_total_activos,$la_columnas,'',$la_config);
		
	}
	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este m?todo realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_funciones,$ia_niveles_scg;
		
		$ls_formato=""; $li_posicion=0; $li_indice=0;
		$dat_emp=$_SESSION["la_empresa"];
		//contable
		$ls_formato = trim($dat_emp["formcont"])."-";
		$li_posicion = 1 ;
		$li_indice   = 1 ;
		$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		do
		{
			$ia_niveles_scg[$li_indice] = $li_posicion;
			$li_indice   = $li_indice+1;
			$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
		} while ($li_posicion>=0);
	}// end function uf_init_niveles
	//-----------------------------------------------------------------------------------------------------------------------------------

	 require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	 require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	 $io_funciones=new class_funciones();
	 require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	 require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	 require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	 require_once("../../shared/class_folder/class_sigesp_int.php");
	 require_once("../../shared/class_folder/class_sigesp_int_scg.php");
	$ls_tiporeporte="0";
	$ls_bolivares="";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("sigesp_scf_class_bal_general.php");
			$io_report  = new sigesp_scf_class_bal_general();
			$ls_bolivares ="Bs.";
			break;
	
		case "1":
			require_once("sigesp_scg_class_bal_generalbsf.php");
			$io_report  = new sigesp_scg_class_bal_generalbsf();
			$ls_bolivares ="Bs.F.";
			break;
	}	 
	 require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	 $io_fecha=new class_fecha();
	 require_once("../class_folder/class_funciones_scf.php");
	 $io_fun_scf=new class_funciones_scf("../../");
	 $ia_niveles_scg[0]="";			
	 uf_init_niveles();
	 $li_total=count((array)$ia_niveles_scg)-1;
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	   $ls_cmbmes=$_GET["cmbmes"];
	   $ls_cmbagno=$_GET["cmbagno"];
	   $ls_last_day=$io_fecha->uf_last_day($ls_cmbmes,$ls_cmbagno);
	   $fechas=$ls_last_day;
	   $ldt_fechas=$io_funciones->uf_convertirdatetobd($ls_last_day)." 00:00:00";
  	   $li_nivel=$_GET["cmbnivel"];
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$ls_nombre=$_SESSION["la_empresa"]["nombre"];
		$li_ano=substr($ldt_periodo,0,4);

		$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
		$ls_titulo="<b>BALANCE GENERAL MENSUAL</b>";
		$ls_titulo1="<b> ".$ls_nombre." </b>"; 
		$ls_titulo2="<b> al ".$ld_fechas."</b>";
		$ls_titulo3="<b>(Expresado en ".$ls_bolivares.")</b>";  
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar datastore con los datos del reporte
	$lb_valido=uf_insert_seguridad("<b>Balance General en PDF</b>"); // Seguridad de Reporte
	if($lb_valido)
	{
		$li_nivel=2;
		$lb_valido=$io_report->uf_balance_general($ldt_fechas,$li_nivel); 
	}
		if($lb_valido==false) // Existe alg?n error ? no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}	
		else// Imprimimos el reporte
		{
			
			//set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4.8,2,3,3); // Configuraci?n de los margenes en cent?metros
			uf_print_encabezado_pagina($ls_titulo,$ls_titulo1,$ls_titulo2,$ls_titulo3,$io_pdf); // Imprimimos el encabezado de la p?gina
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
			
			//------------------activo tesoro-----------------------------------------------------------
			$total_activo_t=0;
			$li_tot=$io_report->ds_Prebalance->getRowCount("sc_cuenta");	    		
			for($li_i=1;$li_i<=$li_tot;$li_i++)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_Prebalance->data["sc_cuenta"][$li_i]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);				

				////-----------activo del tesoro--------------------------------------------------------------
				$ls_denominacion=$io_report->ds_Prebalance->data["denominacion"][$li_i];
				$ls_nivel=$io_report->ds_Prebalance->data["nivel"][$li_i];				
				$ld_saldo=$io_report->ds_Prebalance->data["saldo"][$li_i];							
				///--------------------------------------------------------------------------------------------
				$total_activo_t=$total_activo_t+$ld_saldo;
				if($ld_saldo<0)
				{ 
				 $ld_saldo="(".number_format(abs($ld_saldo),2,",",".").")";				
				}
				else
				{
				 $ld_saldo=number_format($ld_saldo,2,",",".");	
				}
				$la_data[$li_i]=array('cuenta'=>$ver,'denominacion'=>$ls_denominacion,'saldo'=>$ld_saldo,'vacio'=>'');
			}//for
			//------------------------fin de activo tesoro-------------------------------------------------------------
			$ls_total_pasivo=0;
			//------------------pasivo tesoro-----------------------------------------------------------
			$li_tot_p=$io_report->ds_pasivo_t->getRowCount("cuenta_p");  
					
			for($li_j=1;$li_j<=$li_tot_p;$li_j++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_pasivo_t->data["cuenta_p"][$li_j]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_p=$io_report->ds_pasivo_t->data["denom_p"][$li_j];
				$ls_nivel_p=$io_report->ds_pasivo_t->data["nivel_p"][$li_j];				
				$ld_saldo_p=$io_report->ds_pasivo_t->data["saldo_p"][$li_j];						
				$ls_total_pasivo=$ls_total_pasivo+$ld_saldo_p;
				if($ld_saldo_p<0)
				{ 
				 $ld_saldo_p="(".number_format(abs($ld_saldo_p),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_p=number_format($ld_saldo_p,2,",",".");	
				}
								
				$la_data_p[$li_j]=array('cuenta_p'=>$ver,'denom_p'=>$ls_denom_p,'saldo_p'=>$ld_saldo_p);
			   
			}//for
			//------------------------fin de activo tesoro-------------------------------------------------------------
			
			//------------------activo hacienda-----------------------------------------------------------
			$li_tot_p=$io_report->ds_activo_h->getRowCount("cuenta_h"); 
			$ld_tot_act_hac = 0; 				
			for($li_k=1;$li_k<=$li_tot_p;$li_k++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_activo_h->data["cuenta_h"][$li_k]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_h=$io_report->ds_activo_h->data["denom_h"][$li_k];
				$ls_nivel_h=$io_report->ds_activo_h->data["nivel_h"][$li_k];				
				$ld_saldo_h=$io_report->ds_activo_h->data["saldo_h"][$li_k];						
				$ld_tot_act_hac += $ld_saldo_h;
				if($ld_saldo_h<0)
				{ 
				 $ld_saldo_h="(".number_format(abs($ld_saldo_h),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_h=number_format($ld_saldo_h,2,",",".");	
				}
							
				$la_data_h[$li_k]=array('cuenta_h'=>$ver,'denom_h'=>$ls_denom_h,'saldo_h'=>$ld_saldo_h,'vacio'=>'');
			   
			}//for
			//------------------------fin de activo hacienda-------------------------------------------------------------
			
			//------------------pasivo hacienda-----------------------------------------------------------
			$li_tot_h=$io_report->ds_pasivo_h->getRowCount("cuenta_p_h");
			$ld_tot_pas_hac = 0;				
			for($li_l=1;$li_l<=$li_tot_h;$li_l++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_pasivo_h->data["cuenta_p_h"][$li_l]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_p_h=$io_report->ds_pasivo_h->data["denom_p_h"][$li_l];
				$ls_nivel_p_h=$io_report->ds_pasivo_h->data["nivel_p_h"][$li_l];
				$ld_saldo_p_h=$io_report->ds_pasivo_h->data["saldo_p_h"][$li_l];	
				/*if(trim($ver)=='299')
				{
				 $ld_saldo_p_h=$io_report->ds_pasivo_h->data["saldo_p_h"][$li_l] + $io_report->saldo_ing_gas;
				}
				else
				{				
				 $ld_saldo_p_h=$io_report->ds_pasivo_h->data["saldo_p_h"][$li_l];						
				}*/
				$ld_tot_pas_hac += $ld_saldo_p_h;
				if($ld_saldo_p_h<0)
				{ 
				 $ld_saldo_p_h="(".number_format(abs($ld_saldo_p_h),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_p_h=number_format($ld_saldo_p_h,2,",",".");		
				}
							
				$la_data_p_h[$li_l]=array('cuenta_p_h'=>$ver,'denom_p_h'=>$ls_denom_p_h,'saldo_p_h'=>$ld_saldo_p_h);
			   
			}//for
			//------------------------fin de activo tesoro-------------------------------------------------------------
			$ls_total_ingreso=0;
			//------------------ingresos-----------------------------------------------------------
			$li_tot_i=$io_report->ds_ingreso->getRowCount("cuenta_i");
			  				
			for($li_m=1;$li_m<=$li_tot_i;$li_m++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_ingreso->data["cuenta_i"][$li_m]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_i=$io_report->ds_ingreso->data["denom_i"][$li_m];
				$ls_nivel_i=$io_report->ds_ingreso->data["nivel_i"][$li_m];				
				$ld_saldo_i=$io_report->ds_ingreso->data["saldo_i"][$li_m];						
				$ls_total_ingreso=$ls_total_ingreso+$ld_saldo_i;
				if($ld_saldo_i<0)
				{ 
				 $ld_saldo_i="(".number_format(abs($ld_saldo_i),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_i=number_format($ld_saldo_i,2,",",".");		
				}				
				$la_data_i[$li_m]=array('cuenta_i'=>$ver,'denom_i'=>$ls_denom_i,'saldo_i'=>$ld_saldo_i,'vacio'=>'');
			   
			}//for
			//------------------------fin de ingresos-------------------------------------------------------------
			
			$ls_total_gastos=0;
			//------------------gastos-----------------------------------------------------------
			$li_tot_g=$io_report->ds_gasto->getRowCount("cuenta_g");
			  				
			for($li_n=1;$li_n<=$li_tot_g;$li_n++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_gasto->data["cuenta_g"][$li_n]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_g=$io_report->ds_gasto->data["denom_g"][$li_n];
				$ls_nivel_g=$io_report->ds_gasto->data["nivel_g"][$li_n];				
				$ld_saldo_g=$io_report->ds_gasto->data["saldo_g"][$li_n];						
				$ls_total_gastos=$ls_total_gastos+$ld_saldo_g;
				if($ld_saldo_g<0)
				{ 
				 $ld_saldo_g="(".number_format(abs($ld_saldo_g),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_g=number_format($ld_saldo_g,2,",",".");			
				}
							
				$la_data_g[$li_n]=array('cuenta_g'=>$ver,'denom_g'=>$ls_denom_g,'saldo_g'=>$ld_saldo_g,'vacio'=>'');
			   
			}//for
			
			$ls_total_resultado=0;
			//------------------------fin de resultado del tesoro-------------------------------------------------------------
			
			$li_tot_t=$io_report->ds_resultado->getRowCount("cuenta_t");
			  				
			for($li_o=1;$li_o<=$li_tot_t;$li_o++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_resultado->data["cuenta_t"][$li_o]);				

					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_t=$io_report->ds_resultado->data["denom_t"][$li_o];
				$ls_nivel_t=$io_report->ds_resultado->data["nivel_t"][$li_o];				
				$ld_saldo_t=$io_report->ds_resultado->data["saldo_t"][$li_o];						
				$ls_total_resultado=$ls_total_resultado+$ld_saldo_t;
				if($ld_saldo_t<0)
				{ 
				 $ld_saldo_t="(".number_format(abs($ld_saldo_t),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_t=number_format($ld_saldo_t,2,",",".");			
				}
								
				$la_data_t[$li_o]=array('cuenta_t'=>$ver,'denom_t'=>$ls_denom_t,'saldo_t'=>$ld_saldo_t);
			   
			}//for
			//------------------------fin de resultado del tesoro-------------------------------------------------------------
			
			//------------------------ CUENTAS DE ORDEN - DEUDORAS -----------------------------------------------------------
			$ld_total_deudora=0;
			$la_data_deudora = NULL;
			$li_tot_deudora=$io_report->ds_scforden_d->getRowCount("cuenta_orden_d");	    		
			for($li_i=1;$li_i<=$li_tot_deudora;$li_i++)
			{
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_scforden_d->data["cuenta_orden_d"][$li_i]);				
	
					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);				
	
				$ls_denominacion=$io_report->ds_scforden_d->data["denom_orden_d"][$li_i];
				$ls_nivel=$io_report->ds_scforden_d->data["nivel_orden_d"][$li_i];				
				$ld_saldo=$io_report->ds_scforden_d->data["saldo_orden_d"][$li_i];							
				$ld_total_deudora += $ld_saldo;
				if($ld_saldo<0)
				{ 
				 $ld_saldo="(".number_format(abs($ld_saldo),2,",",".").")";				
				}
				else
				{
				 $ld_saldo=number_format($ld_saldo,2,",",".");			
				} 
							
				$la_data_deudora[$li_i]=array('cuenta_deudora'=>$ver,'denom_deudora'=>$ls_denominacion,'saldo_deudora'=>$ld_saldo,'vacio'=>'');
			}//for
			//------------------------ FIN CUENTAS DE ORDEN - DEUDORAS -------------------------------------------------------
			$ld_total_acreedora=0;
			//------------------------ CUENTAS DE ORDEN - ACREEDORAS ---------------------------------------------------------
			$li_tot_acreedora=$io_report->ds_scforden_h->getRowCount("cuenta_orden_h");  
			$la_data_acreedora = NULL;		
			for($li_j=1;$li_j<=$li_tot_acreedora;$li_j++)
			{ 
				$io_pdf->transaction('start'); // Iniciamos la transacci?n
				$thisPageNum=$io_pdf->ezPageCount;		
				
				$ls_sc_cuenta=trim($io_report->ds_scforden_h->data["cuenta_orden_h"][$li_j]);				
	
					$li_totfil=0;
					$as_cuenta="";
					for($li=$li_total;$li>1;$li--)
					{
						$li_ant=$ia_niveles_scg[$li-1];
						$li_act=$ia_niveles_scg[$li];
						$li_fila=$li_act-$li_ant;
						$li_len=strlen($ls_sc_cuenta);
						$li_totfil=$li_totfil+$li_fila;
						$li_inicio=$li_len-$li_totfil;
						if($li==$li_total)
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
						}
						else
						{
							$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
						}
					}
					$li_fila=$ia_niveles_scg[1]+1;
					$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;
					$ver=substr($as_cuenta,3,3);
				
				$ls_denom_acreedora=$io_report->ds_scforden_h->data["denom_orden_h"][$li_j];
				$ls_nivel_acreedora=$io_report->ds_scforden_h->data["nivel_orden_h"][$li_j];				
				$ld_saldo_acreedora=$io_report->ds_scforden_h->data["saldo_orden_h"][$li_j];						
				$ld_total_acreedora += $ld_saldo_acreedora;
				if($ld_saldo_p<0)
				{ 
				 $ld_saldo_acreedora="(".number_format(abs($ld_saldo_acreedora),2,",",".").")";				
				}
				else
				{
				 $ld_saldo_acreedora=number_format($ld_saldo_acreedora,2,",",".");				
				} 
							
				$la_data_acreedora[$li_j]=array('cuenta_acreedora'=>$ver,'denom_acreedora'=>$ls_denom_acreedora,'saldo_acreedora'=>$ld_saldo_acreedora);
			   
			}//for
			//------------------------ FIN CUENTAS DE ORDEN - ACREEDORAS ------------------------------------------------------
			
			$ld_total_activo    = 0; // TOTAL DE LA COLUMNA DE ACTIVO
			$ld_total_pasivo    = 0; // TOTAL DE LA COLUMNA DE PASIVO
			$ld_total_resultado = 0; // RESULTADO DEL EJERCICIO SUPERAVIT >= 0 O DEFICIT < 0
			$ld_total_resultado = $ls_total_ingreso - $ls_total_gastos;
			$total_pasivo_result= $ls_total_resultado+$ls_total_pasivo;
		    //$ld_total_activo = $total_activo_t + $ld_tot_act_hac + $ls_total_gastos;
			$ld_tot_pas_hac += $ld_total_resultado;
			$ld_total_activo = $total_activo_t + $ld_tot_act_hac + $ld_total_deudora;
			$ld_total_pasivo = $total_pasivo_result + $ld_tot_pas_hac + $ld_total_acreedora;
			$lb_mostrar_orden = false;
			if(($li_tot_deudora>0)&&($li_tot_acreedora>0))
			{
			 $lb_mostrar_orden = true;
			}
			//$ld_total_activo = $total_activo_t + $ld_tot_act_hac;
			//$ld_total_pasivo = $total_pasivo_result + $ld_tot_pas_hac+ $ls_total_ingreso;
			//$ld_total_pasivo = $total_pasivo_result + $ld_tot_pas_hac;
			
			if($ld_total_resultado<>0)
			{
			 if($ld_total_resultado>0)
			 {
			  $ls_total_gastos += $ld_total_resultado;
			 }
			 else
			 {
			  $ls_total_ingreso += abs($ld_total_resultado);
			 }
			}
			$ld_total_activo += $ls_total_gastos;
			$ld_total_pasivo += $ls_total_ingreso;
			
			$ld_total_activo = number_format($ld_total_activo,2,",",".");	
			$ld_total_pasivo = number_format($ld_total_pasivo,2,",",".");	
			$total_activo_t=number_format($total_activo_t,2,",",".");		    
		    $total_pasivo_result=number_format( $total_pasivo_result,2,",",".");		
		    $ls_total_pasivo=number_format($ls_total_pasivo,2,",",".");	
		    $ls_total_ingreso=number_format($ls_total_ingreso,2,",",".");
		    $ls_total_gastos=number_format($ls_total_gastos,2,",",".");	
			$ld_tot_act_hac=number_format($ld_tot_act_hac,2,",",".");
		    $ld_tot_pas_hac=number_format($ld_tot_pas_hac,2,",",".");
			$ld_total_acreedora=number_format($ld_total_acreedora,2,",",".");	
		    $ld_total_deudora=number_format($ld_total_deudora,2,",",".");
			
			uf_print_detalle($la_data,$la_data_p,$la_data_t,$total_activo_t,$ls_total_pasivo, $total_pasivo_result,$io_pdf); // Imprimimos el detalle 
			uf_print_detalle_2($la_data_h,$la_data_p_h,$ld_tot_act_hac,$ld_tot_pas_hac,$ld_total_resultado,$io_pdf); // Imprimimos el detalle
			$io_pdf->EzNewPage();
			uf_print_detalle_3($la_data_i,$la_data_g,$ls_total_ingreso,$ls_total_gastos,$ld_total_resultado,$io_pdf); // Imprimimos el detalle
			if($lb_mostrar_orden)
			{
			 $io_pdf->EzNewPage();
			 uf_print_cuentas_orden($la_data_acreedora,$la_data_deudora,$ld_total_acreedora,$ld_total_deudora,$io_pdf);
			 uf_print_detalle_4(500,$ld_total_activo,$ld_total_pasivo,$io_pdf);
			}
			else
			{
			 uf_print_detalle_4(400,$ld_total_activo,$ld_total_pasivo,$io_pdf);
			}

			
			unset($la_data);
			unset($la_data_p);
			unset($la_data_t);
			unset($la_data_p_h);
			unset($la_data_i);
			unset($la_data_g);		
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
		 }//else
		unset($io_report);
	    unset($io_funciones);			
?> 