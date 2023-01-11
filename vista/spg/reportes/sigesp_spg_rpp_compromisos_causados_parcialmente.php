<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		    Acess: private
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo_comp // Descripción del periodo del comprobante
		//	    		   as_fecha_comp // Descripción del período de la fecha del comprobante
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 22/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(10,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],25,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,730,10,$as_titulo); // Agregar el título
		
		$li_tm=$io_pdf->getTextWidth(11,$as_fecha);
		$tm=330-($li_tm/2);
		$io_pdf->addText($tm,720,10,$as_fecha); // Agregar el título
		$io_pdf->addText(500,740,9,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,9,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(500,720,9,date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($as_cod_pro,$as_nomprobene,$as_tipo_destino,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: privates
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 22/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$ls_codigo = $as_cod_pro;
		if($as_tipo_destino=="P"){
			$ls_titulo="Proveedor";
		}
		elseif($as_tipo_destino=="B"){
			$ls_titulo="Beneficiario";
		}
		else
		{
			$ls_titulo="Ninguno";
			$ls_codigo="----------";
		}
		$la_data=array(array('name'=>'<b>Codigo</b> '.$ls_codigo.''),
		               array('name'=>'<b>'.$ls_titulo.'</b> '.$as_nomprobene.'' ));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 9, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9),
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 22/09/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>1, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la 
						               'comprobante'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la 
						 			   'fecha'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						 			   'comprometido'=>array('justification'=>'right','width'=>80), // Justificación 
						 			   'causado'=>array('justification'=>'right','width'=>80),
									   'pagado'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la 
		$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
		                   'comprobante'=>'<b>Comprobante</b>',
						   'fecha'=>'<b>Fecha</b>',
						   'comprometido'=>'<b>Comprometido</b>',
						   'causado'=>'<b>Causado</b>',
						   'pagado'=>'<b>Pagado</b>');
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ad_totalcomprometer,$ad_totalcausado,$ad_totalpagado,$io_pdf,$as_titulo)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function : uf_print_pie_cabecera
		//		    Acess : private
		//	    Arguments : ad_total // Total General
		//    Description : función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing.Yozelin Barragán
		// Fecha Creación: 22/09/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_datat=array(array('name'=>'_____________________________________________________________________________________________________________'));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'width'=>560); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_datat,$la_columna,'',$la_config);
		
		$la_data[]=array('cuenta'=>' ','comprobante'=>'','fecha'=>'<b>'.$as_titulo.'</b> ',
		                 'comprometido'=>number_format($ad_totalcomprometer,2,',','.'),
		                 'causado'=>number_format($ad_totalcausado,2,',','.'),
						 'pagado'=>number_format($ad_totalpagado,2,',','.'));
		$la_columnas=array('cuenta'=>' ','comprobante'=>'','fecha'=>'','comprometido'=>'','causado'=>'','pagado'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'colGap'=>2, // separacion entre tablas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>305, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la 
						               'comprobante'=>array('justification'=>'center','width'=>150), // Justificación y ancho de  
						 			   'fecha'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la 
						 			   'comprometido'=>array('justification'=>'right','width'=>80), // Justificación 
						 			   'causado'=>array('justification'=>'right','width'=>80),
									   'pagado'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la 
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_data=array(array('name'=>''));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>630, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	
	//--------------------------------------------------------------------------------------------------------------------------------
		require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
		require_once("sigesp_spg_funciones_reportes.php");
		$io_function_report = new sigesp_spg_funciones_reportes();
        require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_function=new class_funciones() ;
		require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha = new class_fecha();
	//-----------------------------------------------------------------------------------------------------------------------------
		require_once("sigesp_spg_class_compromiso_causado_parcial.php");
		$io_report = new sigesp_spg_class_compromiso_causado_parcial();
	//------------------------------------------------------------------------------------------------------------------------------		
	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ldt_fecdes = $io_function->uf_convertirdatetobd($_GET["txtfecdes"]);
		$ldt_fechas = $io_function->uf_convertirdatetobd($_GET["txtfechas"]);	
	    $ls_fechades=$io_function->uf_convertirfecmostrar($ldt_fecdes);
	    $ls_fechahas=$io_function->uf_convertirfecmostrar($ldt_fechas);
		
	    
	     
	    
	 /////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////////
	 $ls_desc_event="Solicitud de Reporte Compromisos Causados Parcialmente desde la  Fecha ".$ldt_fecdes."  hasta ".$ldt_fechas;
	 $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_compromisos_causados_parcialmente.html",$ls_desc_event);
	 ////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////////
	
	 //----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
		$ls_titulo="<b>COMPROMISOS CAUSADOS PARCIALMENTE</b> "; 
		$ls_fecha="<b> DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas." </b>";      
	 //--------------------------------------------------------------------------------------------------------------------------------
     $arrdatareporte    = array();	
     $data_compromisos  = $io_report->uf_obtener_compromisos($ldt_fecdes, $ldt_fechas);
	 $monto_suma=0;
     while(!$data_compromisos->EOF)
	 {
     	$procede            = $data_compromisos->fields['procede'];
     	$documento          = $data_compromisos->fields['documento'];
     	$spg_cuenta         = $data_compromisos->fields['spg_cuenta'];
     	$codestpro1         = $data_compromisos->fields['codestpro1'];
     	$estcla             = $data_compromisos->fields['estcla'];
     	$codestpro2         = $data_compromisos->fields['codestpro2'];
     	$codestpro3         = $data_compromisos->fields['codestpro3'];
     	$codestpro4         = $data_compromisos->fields['codestpro4'];
     	$codestpro5         = $data_compromisos->fields['codestpro5'];
     	$monto_comprometido = $data_compromisos->fields['monto'];     

        $monto_reverso = $io_report->uf_buscar_reverso_compromiso($procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5);	

		$monto_comprometido = $monto_comprometido + ($monto_reverso);
     	
     	$resultado = $io_report->uf_buscar_causado( $procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5);
     	$data_causado = $resultado[1];
     	if((number_format($monto_comprometido, 2) > number_format($resultado[0],2)) && $resultado[0]>0)
		{
     		$ld_total_pagado  = 0;
     		$ld_total_anulado = 0;
     		while(!$data_causado->EOF)
			{
				$procede_ca    = $data_causado->fields['procede'];
	     		$documento_ca  = $data_causado->fields['documento'];
	     		$spg_cuenta_ca = $data_causado->fields['spg_cuenta'];
	     		$codestpro1_ca = $data_causado->fields['codestpro1'];
	     		$estcla_ca     = $data_causado->fields['estcla'];
	     		$codestpro2_ca = $data_causado->fields['codestpro2'];
	     		$codestpro3_ca = $data_causado->fields['codestpro3'];
	     		$codestpro4_ca = $data_causado->fields['codestpro4'];
	     		$codestpro5_ca = $data_causado->fields['codestpro5'];
	     		
	     		$data_pagado  = $io_report->uf_buscar_pagado($procede_ca, $documento_ca, $spg_cuenta_ca, $codestpro1_ca, $estcla_ca, $codestpro2_ca, $codestpro3_ca, $codestpro4_ca, $codestpro5_ca);
	     		$ld_pagado    = 0;
	     		$ld_anulado   = 0;
	     		while(!$data_pagado->EOF)
				{
	     			$procede_pa    = $data_pagado->fields['procede'];
	     			$documento_pa  = $data_pagado->fields['documento'];
	     			$spg_cuenta_pa = $data_pagado->fields['spg_cuenta'];
	     			$codestpro1_pa = $data_pagado->fields['codestpro1'];
	     			$estcla_pa     = $data_pagado->fields['estcla'];
	     			$codestpro2_pa = $data_pagado->fields['codestpro2'];
	     			$codestpro3_pa = $data_pagado->fields['codestpro3'];
	     			$codestpro4_pa = $data_pagado->fields['codestpro4'];
	     			$codestpro5_pa = $data_pagado->fields['codestpro5'];
	     			$ld_pagado     = $ld_pagado + $data_pagado->fields['monto'];
	     			
	     			$ld_anulado = $ld_anulado + $io_report->uf_buscar_anulado($procede_pa, $documento_pa, $spg_cuenta_pa, $codestpro1_pa, $estcla_pa, $codestpro2_pa, $codestpro3_pa, $codestpro4_pa, $codestpro5_pa);
	     			$data_pagado->MoveNext();
	     		}	     		
	     		unset($data_pagado);
	     		
	     		
	     		$ld_total_pagado  = $ld_total_pagado  + $ld_pagado;
	     		$ld_total_anulado = $ld_total_anulado + $ld_anulado;
	     		$data_causado->MoveNext();
	     	}
	     	$ld_total_pagado = $ld_total_pagado + $ld_total_anulado;
	     	unset($data_causado);
	     	$arrdatareporte [] = array('codigo'=>$data_compromisos->fields['codigo'],
	     		                           'nombre'=>$data_compromisos->fields['nombre'],
	     								   'tipo'=>$data_compromisos->fields['tipo_destino'],
	     								   'cuenta'=>$spg_cuenta,
	     								   'comprobante'=>$data_compromisos->fields['comprobante'],
	     								   'fecha'=>$io_function->uf_convertirfecmostrar($data_compromisos->fields['fecha']),
	     		 						   'comprometido'=>$monto_comprometido,
	     								   'causado'=>$resultado[0],
	     								   'pagado'=>$ld_total_pagado);
	    }
	    unset($resultado);
	    $data_compromisos->MoveNext();
     }
     unset($data_compromisos);
     
     if(empty($arrdatareporte)) // No hay registros
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
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_fecha,$io_pdf); // Imprimimos el encabezado de la página
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		$ld_total_comprometer=0;
	    $ld_total_causado=0;
	    $ld_total_pagado=0;
		$ld_sub_total_comprometer=0;
		$ld_sub_total_causado=0;
		$ld_sub_total_pagado=0;
		
		$li_control        = 0;
		$li_causadoparcial = count($arrdatareporte);
		$ls_codprobene_ant = '';
		foreach ($arrdatareporte as $causadoparcial)
		{
			$ls_codprobene = $causadoparcial['codigo'];
			$ls_spg_cuenta = $causadoparcial['cuenta'];
			$ls_proc_comp  = $causadoparcial['comprobante'];
			$ldt_fecha     = $causadoparcial['fecha'];
			$ld_comprometer= $causadoparcial['comprometido'];
			$ld_causado    = $causadoparcial['causado'];
			$ld_pagado     = $causadoparcial['pagado'];
			
			$ld_total_comprometer     = $ld_total_comprometer + $ld_comprometer;
	   	 	$ld_total_causado         = $ld_total_causado + $ld_causado;
	    	$ld_total_pagado          = $ld_total_pagado  + $ld_pagado;
						
			if($ls_codprobene_ant == ''){
				$ls_codprobene_ant = $ls_codprobene;
				$ls_nomprobene     = $causadoparcial['nombre'];
				$ls_tipo_destino   = $causadoparcial['tipo'];
				uf_print_cabecera($ls_codprobene,$ls_nomprobene,$ls_tipo_destino,$io_pdf);
				$la_data[] = array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				               'comprometido'=>number_format($ld_comprometer,2,",","."),'causado'=>number_format($ld_causado,2,",","."),'pagado'=>number_format($ld_pagado,2,",","."));
				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
			}
			else if($ls_codprobene_ant == $ls_codprobene){
				$la_data[] = array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				               'comprometido'=>number_format($ld_comprometer,2,",","."),'causado'=>number_format($ld_causado,2,",","."),'pagado'=>number_format($ld_pagado,2,",","."));
				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
				$ls_codprobene_ant = $ls_codprobene;
			}
			else{
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
				uf_print_pie_cabecera($ld_sub_total_comprometer,$ld_sub_total_causado,$ld_sub_total_pagado,$io_pdf,"Total Bs.");
				$ld_sub_total_comprometer = 0;
				$ld_sub_total_causado     = 0;
				$ld_sub_total_pagado      = 0;
				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
				unset($la_data);
				
				//nuevo encabezado
				$ls_nomprobene     = $causadoparcial['nombre'];
				$ls_tipo_destino   = $causadoparcial['tipo'];
				uf_print_cabecera($ls_codprobene,$ls_nomprobene,$ls_tipo_destino,$io_pdf);
				$la_data[] = array('cuenta'=>$ls_spg_cuenta,'comprobante'=>$ls_proc_comp,'fecha'=>$ldt_fecha,
				               'comprometido'=>number_format($ld_comprometer,2,",","."),'causado'=>number_format($ld_causado,2,",","."),'pagado'=>number_format($ld_pagado,2,",","."));
				
				$ls_codprobene_ant = $ls_codprobene;
			}
			
			if($li_control+2>$li_causadoparcial){
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
				uf_print_pie_cabecera($ld_sub_total_comprometer,$ld_sub_total_causado,$ld_sub_total_pagado,$io_pdf,"Total Bs.");
				uf_print_pie_cabecera($ld_total_comprometer,$ld_total_causado,$ld_total_pagado,$io_pdf,"Total General");
				unset($la_data);
			}
			
			$li_control++;
		}
		
			
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
	
	unset($arrdatareporte);
	unset($la_data);
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
	unset($io_fecha);
?> 