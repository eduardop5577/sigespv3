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
	function uf_print_encabezado_pagina($as_titulo,$as_periodo_comp,$as_fecha_comp,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   as_periodo_comp // Descripci?n del periodo del comprobante
		//	    		   as_fecha_comp // Descripci?n del per?odo de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo

		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,694,11,$as_titulo); // Agregar el t?tulo

		$li_tm=$io_pdf->getTextWidth(11,$as_periodo_comp);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,682,11,$as_periodo_comp); // Agregar el t?tulo

		$li_tm=$io_pdf->getTextWidth(11,$as_fecha_comp);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,670,11,$as_fecha_comp); // Agregar el t?tulo

		$io_pdf->addText(500,720,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,710,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_procede,$as_comprobante,$as_nomprobene,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: as_procede // procede
		//	    		   as_comprobante // comprobante
		//                 as_nomprobene   // nombre del proveedor
		//	    		   io_pdf // Objeto PDF
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Comprobante</b>  '.$as_procede.'---'.$as_comprobante.''),
		               array('name'=>'<b>Proveedor</b>  '.$as_nomprobene.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 8, // Tama?o de Letras
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xPos'=>299); // Orientaci?n de la tabla 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera_programatica($as_programatica,$as_denestpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'<b>Programatica</b>  '.$as_programatica.''),
		               array('name'=>'<b></b>'.$as_denestpro.''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>2, // Sombra entre l?neas
						 'shadeCol'=>array(0.98,0.98,0.98), // Color de la sombra
						 'shadeCol2'=>array(0.98,0.98,0.98), // Color de la sombra
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xPos'=>299); // Orientaci?n de la tabla 
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
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n: 21/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>299, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'operacion'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'dencuenta'=>'<b>Denominacion Cuenta</b>',
						   'descripcion'=>'<b>Descripci?n Movimiento</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'operacion'=>'<b>Operacion</b>',
						   'monto'=>'<b>Monto</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_programatica($ad_totalprogramatica,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_total_programatica
		//		    Acess : private
		//	    Arguments : ad_totalprogramatica // Total Programatica
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[1]=array('cuenta'=>' ','dencuenta'=>' ','descripcion'=>' ','fecha'=>' ','operacion'=>'<b>Total Programatica </b>','monto'=>$ad_totalprogramatica);
		$la_columnas=array('cuenta'=>'','dencuenta'=>'','descripcion'=>'','fecha'=>'','operacion'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>299, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'operacion'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_total_programatica
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_total_comprobante($ad_totalcomprobante,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_total_programatica
		//		    Acess : private
		//	    Arguments : ad_totalcomprobante // Total Comprobante
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[1]=array('cuenta'=>' ','dencuenta'=>' ','descripcion'=>' ','fecha'=>' ','operacion'=>'<b>Total Comprobante </b>','monto'=>$ad_totalcomprobante);
		$la_columnas=array('cuenta'=>'','dencuenta'=>'','descripcion'=>'','fecha'=>'','operacion'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tama?o de Letras
						 'titleFontSize' => 8,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'xPos'=>299, // Orientaci?n de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
						 			   'dencuenta'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'descripcion'=>array('justification'=>'left','width'=>115), // Justificaci?n y ancho de la columna
						 			   'fecha'=>array('justification'=>'center','width'=>50), // Justificaci?n y ancho de la columna
						 			   'operacion'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
									   'monto'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);									   
	}// end function uf_print_total_comprobante
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : funci?n que imprime el fin de la cabecera de cada p?gina
		//	   Creado Por: Ing. Yozelin Barrag?n
		// Fecha Creaci?n : 18/02/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data=array(array('name'=>'------------------------------------------------------------------------------------------------------------------------------------------------------------------------'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'xOrientation'=>'center', // Orientaci?n de la tabla
						 'width'=>520); // Ancho M?ximo de la tabla---
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('total'=>'<b>Total</b>','monto'=>$ad_total));
		$la_columna=array('total'=>'','monto'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'fontSize' => 9, // Tama?o de Letras
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>520, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center', // Orientaci?n de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificaci?n y ancho de la columna
						 			   'monto'=>array('justification'=>'right','width'=>120))); // Justificaci?n y ancho de la columna

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>520, // Ancho M?ximo de la tabla
						 'xOrientation'=>'center'); // Orientaci?n de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_spg_reporte.php");
	$io_report = new sigesp_spg_reporte();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	    $ls_cuentades_min=$_GET["txtcuentades"];
	    $ls_cuentahas_max=$_GET["txtcuentahas"];
		if($ls_cuentades_min=="")
		{
		   if($io_report->uf_spg_reporte_select_min_cuenta($ls_cuentades_min))
		   {
		     $ls_cuentades=$ls_cuentades_min;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentades=$ls_cuentades_min;
		}
		if($ls_cuentahas_max=="")
		{
		   if($io_report->uf_spg_reporte_select_max_cuenta($ls_cuentahas_max))
		   {
		     $ls_cuentahas=$ls_cuentahas_max;
		   } 
		   else
		   {
				print("<script language=JavaScript>");
				print(" alert('No hay cuentas presupuestraias');"); 
				print(" close();");
				print("</script>");
		   }
		}
		else
		{
		    $ls_cuentahas=$ls_cuentahas_max;
		}
	 $fecdes=$_GET["txtfecdes"];
	 if (!empty($fecdes))
	 {
	     $ldt_fecdes=$io_funciones->uf_convertirdatetobd($fecdes);
	 }	else {  $ldt_fecdes=""; }
	 $fechas=$_GET["txtfechas"];
	 if (!empty($fechas))
	 {
  	    $ldt_fechas=$io_funciones->uf_convertirdatetobd($fechas);
	 }	else {  $ldt_fechas=""; }

	 $ls_orden=$_GET["rborden"];
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
		$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
		$li_ano=substr($ldt_periodo,0,4);

		$ldt_fecdes_cab=$io_funciones->uf_convertirfecmostrar(substr($ldt_fecdes,0,10));
		$ldt_fechas_cab=$io_funciones->uf_convertirfecmostrar(substr($ldt_fechas,0,10));

		$ls_titulo="<b>COMPROBANTE PRESUPUESTARIO</b> ";
		$ls_periodo_comp="<b>Comprobantes desde la Cuenta Nro.  ".$ls_cuentades." al ".$ls_cuentahas."</b>";
		$ls_fecha_comp="<b>Desde ".$ldt_fecdes_cab." al ".$ldt_fechas_cab."</b>";
	//--------------------------------------------------------------------------------------------------------------------------------
    // Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
	 $lb_valido=$io_report->uf_spg_reporte_select_comprobante_formato2($ls_cuentades,$ls_cuentahas,$ldt_fecdes,$ldt_fechas);
	 if($lb_valido==false) // Existe alg?n error ? no hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');");
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
	    
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4.5,3,3,3); // Configuraci?n de los margenes en cent?metros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo_comp,$ls_fecha_comp,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_tot=$io_report->dts_cab->getRowCount("comprobante");
		$ld_total=0;
		$ld_totalcomprobante=0;
		$ld_totalprogramatica=0;
		for($li_i=1;$li_i<=$li_tot;$li_i++)
		{
	        $io_pdf->transaction('start'); // Iniciamos la transacci?n
			$thisPageNum=$io_pdf->ezPageCount;
			$ls_comprobante=$io_report->dts_cab->data["comprobante"][$li_i];
			$ls_procede=$io_report->dts_cab->data["procede"][$li_i];
			$ls_ced_bene=$io_report->dts_cab->data["ced_bene"][$li_i];
			$ls_cod_pro=$io_report->dts_cab->data["cod_pro"][$li_i];
			$ls_nompro=$io_report->dts_cab->data["nompro"][$li_i];
			$ls_apebene=$io_report->dts_cab->data["apebene"][$li_i];
			$ls_nombene=$io_report->dts_cab->data["nombene"][$li_i];
			$ls_tipo_destino=$io_report->dts_cab->data["tipo_destino"][$li_i];
		    
			if($ls_tipo_destino=="P")
		    {
			    $ls_nomprobene=$ls_nompro;
		    }
			if($ls_tipo_destino=="B")
			{
				$ls_nomprobene=$ls_apebene.", ".$ls_nombene;
			}
			if($ls_tipo_destino=="-")
			{
				$ls_nomprobene="";
			}
			uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomprobene,$io_pdf); // Imprimimos la cabecera del registro
			$lb_valido=$io_report->uf_spg_reporte_comprobante_formato2($ls_cuentades,$ls_cuentahas,$ldt_fecdes,$ldt_fechas,
			                                                           $ls_comprobante,$ls_procede);
            if($lb_valido)
			{
				$li_totrow_det=$io_report->dts_reporte->getRowCount("programatica");
				for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
				{
					$ls_procede=$io_report->dts_reporte->data["procede"][$li_s];
					$ls_comprobante=$io_report->dts_reporte->data["comprobante"][$li_s];
					$fecha=$io_report->dts_reporte->data["fecha"][$li_s];
					$ldt_fecha=$io_funciones->uf_convertirfecmostrar($fecha);
					$ls_programatica=$io_report->dts_reporte->data["programatica"][$li_s];
					$ls_denestpro1=$io_report->dts_reporte->data["denestpro1"][$li_s];
					$ls_denestpro2=$io_report->dts_reporte->data["denestpro2"][$li_s];
					$ls_denestpro3=$io_report->dts_reporte->data["denestpro3"][$li_s];
					$ls_denestpro=$ls_denestpro1." , ".$ls_denestpro2." , ".$ls_denestpro3;
					$ls_spg_cuenta=$io_report->dts_reporte->data["spg_cuenta"][$li_s];
					$ls_documento=$io_report->dts_reporte->data["documento"][$li_s];
					$ls_operacion=$io_report->dts_reporte->data["operacion"][$li_s];
					$ls_descripcion=$io_report->dts_reporte->data["descripcion"][$li_s];
					$ld_monto=$io_report->dts_reporte->data["monto"][$li_s];
					$ls_orden=$io_report->dts_reporte->data["orden"][$li_s];
					$ls_denominacion=$io_report->dts_reporte->data["denominacion"][$li_s];
					$ls_denoperacion=$io_report->dts_reporte->data["denoperacion"][$li_s];
					$ls_denestpro5=$io_report->dts_reporte->data["denestpro5"][$li_s];
					$ls_tipo_destino=$io_report->dts_reporte->data["tipo_destino"][$li_s];
					$ls_cod_pro=$io_report->dts_reporte->data["cod_pro"][$li_s];
					$ls_ced_bene=$io_report->dts_reporte->data["ced_bene"][$li_s];
					$ls_nompro=$io_report->dts_reporte->data["nompro"][$li_s];
					$ls_apebene=$io_report->dts_reporte->data["apebene"][$li_s];
					$ls_nombene=$io_report->dts_reporte->data["nombene"][$li_s];

					$ld_totalprogramatica=$ld_totalprogramatica+$ld_monto;
					$ld_totalcomprobante=$ld_totalcomprobante+$ld_monto;
					$ld_total=$ld_total+$ld_monto;
					
					if($ld_monto<0)
					{
					  $ld_monto_positivo=abs($ld_monto);
					  $ld_monto=number_format($ld_monto_positivo,2,",",".");
					  $ld_monto="(".$ld_monto.")";
					}
					else
					{
					  $ld_monto=number_format($ld_monto,2,",",".");
					}
					
					$la_data[$li_s]=array('cuenta'=>$ls_spg_cuenta,'dencuenta'=>$ls_denominacion,'descripcion'=>$ls_descripcion,'fecha'=>$ldt_fecha,'operacion'=>$ls_denoperacion,'monto'=>$ld_monto);
					$ld_monto=str_replace('.','',$ld_monto);
					$ld_monto=str_replace(',','.',$ld_monto);
				}
			    uf_print_cabecera_programatica($ls_programatica,$ls_denestpro,$io_pdf); // Imprimimos la cabecera del registro
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
				if($ld_totalprogramatica<0)
				{
				  $ld_monto_positivo=abs($ld_totalprogramatica);
				  $ld_totalprogramatica=number_format($ld_monto_positivo,2,",",".");
				  $ld_totalprogramatica="(".$ld_totalprogramatica.")";
				}
				else
				{
			       $ld_totalprogramatica=number_format($ld_totalprogramatica,2,",",".");
				}
                $ld_totalprogram=$ld_totalprogramatica;
			    uf_print_total_programatica($ld_totalprogramatica,$io_pdf); // Imprimimos el total programatica
				if($ld_totalcomprobante<0)
				{
				  $ld_monto_positivo=abs($ld_totalcomprobante);
				  $ld_totalcomprobante=number_format($ld_monto_positivo,2,",",".");
				  $ld_totalcomprobante="(".$ld_totalcomprobante.")";
				}
				else
				{
			       $ld_totalcomprobante=number_format($ld_totalcomprobante,2,",",".");
				}
			    $ld_totalcomprob=$ld_totalcomprobante;
			    uf_print_total_comprobante($ld_totalcomprobante,$io_pdf); // Imprimimos el total comprobante
				$ld_totalcomprobante=0;
				$ld_totalprogramatica=0;
			}
			if ($io_pdf->ezPageCount==$thisPageNum)
			{// Hacemos el commit de los registros que se desean imprimir
				$io_pdf->transaction('commit');
			}
			elseif($thisPageNum>1)
			{// Hacemos un rollback de los registros, agregamos una nueva p?gina y volvemos a imprimir
				$io_pdf->transaction('rewind');
				$io_pdf->ezNewPage(); // Insertar una nueva p?gina
				uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomprobene,$io_pdf); // Imprimimos la cabecera del registro
				uf_print_cabecera_programatica($ls_programatica,$ls_denestpro,$io_pdf); // Imprimimos la cabecera del registro
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				uf_print_total_programatica($ld_totalprogram,$io_pdf); // Imprimimos el total programatica
				uf_print_total_comprobante($ld_totalcomprob,$io_pdf); // Imprimimos el total comprobante
			}
			else
			{
				$io_pdf->transaction('commit');
			}
			if($li_i==$li_tot)
			{
			  $ld_total=number_format($ld_total,2,",",".");
			  uf_print_pie_cabecera($ld_total,$io_pdf); // Imprimimos pie de la cabecera
			}
			unset($la_data);			
		}//for
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
