<?PHP
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		$ls_descripcion="Generó el Reporte ".$as_titulo." Forma 0205";
		$lb_valido=$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_instructivo_0205.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,$rango,$io_pdf)
	{		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_periodo // Período
		//	    		   as_rango // Rango de Meses
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		//     Modificado Por: Ing. Jennifer Rivero
		// Fecha Creación: 27/06/2006 
		// Fecha de modificaciòn: 06/06/2008
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
                
                $ls_periodo='MES';
                $ls_rango = substr($rango,0,2);
		if ($as_periodo=='3')
                {
                    $ls_periodo = 'TRIMESTRE';
                    $ls_rango = $rango;
                }
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->addText(50,730,7,"<b>CÓDIGO PRESUPUESTARIO DEL ENTE: </b>");		
		$io_pdf->addText(50,720,7,"<b>DENOMINACIÓN DEL ENTE:  ".$_SESSION["la_empresa"]["nombre"]."</b>");
		$io_pdf->addText(50,710,7,"<b>ORGANO DE ADSCRIPCIÓN:  ".$_SESSION["la_empresa"]["nomorgads"]."</b>");
		$io_pdf->addText(50,700,7,"<b>PERIODO PRESUPUESTARIO:</b> ".substr($_SESSION["la_empresa"]["periodo"],0,4));
		$io_pdf->addText(50,690,7,"<b>".$ls_periodo.":</b> ".$ls_rango);
		$li_tm=$io_pdf->getTextWidth(8,"RECURSOS HUMANOS POR TIPO DE CARGO Y GÉNERO");		
		$tm=280-($li_tm/2);
		$io_pdf->addText($tm,680,10,"<b>RECURSOS HUMANOS POR TIPO DE CARGO Y GÉNERO</b>"); // Agregar el título
		$io_pdf->Rectangle(50,570,515,100);
		$io_pdf->line(165,570,165,670);//linea vertical
		$io_pdf->addText(75,620,7,"<b>TIPO DE PERSONAL</b>");	
		$io_pdf->line(165,652,565,652);//Horizontal	
		$io_pdf->line(165,620,565,620);//Horizontal			
		$io_pdf->line(190,570,190,620);//linea vertical
		$io_pdf->line(215,570,215,620);//linea vertical
		$io_pdf->line(240,570,240,620);//linea vertical
		$io_pdf->line(275,570,275,620);//linea vertical
		$io_pdf->line(335,570,335,670);//linea vertical		
		$io_pdf->line(360,570,360,620);//linea vertical
		$io_pdf->line(385,570,385,620);//linea vertical
		$io_pdf->line(410,570,410,620);//linea vertical
		$io_pdf->line(445,570,445,620);//linea vertical
		$io_pdf->line(505,570,505,670);//linea vertical
		$io_pdf->addText(180,657,6,"<b>PRESUPUESTO APROBADO Y MODIFICACIONES</b>");	
		$io_pdf->addText(363,657,6,"<b>EJECUTADO EN EL ".$ls_periodo."</b>");	
		$io_pdf->addText(223,630,7,"<b>NRO. DE CARGOS</b>");
		$io_pdf->addText(393,630,7,"<b>NRO. DE CARGOS</b>");
		$io_pdf->addText(515,638,5,"<b>ACUMULADO AL</b>");
		$io_pdf->addText(510,631,5,"<b>".$ls_periodo."</b>");
		$io_pdf->addText(175,593,7,"<b>F</b>");	
		$io_pdf->addText(200,593,7,"<b>M</b>");	
		$io_pdf->addText(225,593,7,"<b>V</b>");	
		$io_pdf->addText(247,600,6,"<b>TOTAL</b>");
		$io_pdf->addText(245,592,6,"<b>NRO. DE</b>");
		$io_pdf->addText(245,584,6,"<b>CARGOS</b>");
		$io_pdf->addText(276,604,6,"<b>MONTO ASIGNADO</b>");
		$io_pdf->addText(276,596,6,"<b>REMUNERACIONES</b>");
		$io_pdf->addText(276,588,6,"<b>               (Bs)</b>");
		$io_pdf->addText(345,593,7,"<b>F</b>");	
		$io_pdf->addText(370,593,7,"<b>M</b>");	
		$io_pdf->addText(395,593,7,"<b>V</b>");	
		$io_pdf->addText(417,600,6,"<b>TOTAL</b>");
		$io_pdf->addText(415,592,6,"<b>NRO. DE</b>");
		$io_pdf->addText(415,584,6,"<b>CARGOS</b>");
		$io_pdf->addText(446,604,6,"<b>MONTO ASIGNADO</b>");
		$io_pdf->addText(446,596,6,"<b>REMUNERACIONES</b>");
		$io_pdf->addText(446,588,6,"<b>               (Bs)</b>");
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna=array('descripcion'=>'',
						  'cargof'=>'',
						  'cargom'=>'',
						  'cargov'=>'',
						  'cargo'=>'',
						  'monto'=>'',
						  'cargorealf'=>'',
						  'cargorealm'=>'',
						  'cargorealv'=>'',
						  'cargoreal'=>'',
						  'montoreal'=>'',
						  'montoacum'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 5.8, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xPos'=>505, // Ancho de la tabla
						 'width'=>940, // Ancho de la tabla
						 'maxWidth'=>940, // Ancho Máximo de la tabla
						 'xOrientation'=>'left', // Orientación de la tabla
						 'xPos'=>570,
						 'cols'=>array('descripcion'=>array('justification'=>'left','width'=>115),
									   'cargof'=>array('justification'=>'center','width'=>25),
									   'cargom'=>array('justification'=>'center','width'=>25),
									   'cargov'=>array('justification'=>'center','width'=>25),
									   'cargo'=>array('justification'=>'center','width'=>35),
									   'monto'=>array('justification'=>'right','width'=>60),
									   'cargorealf'=>array('justification'=>'center','width'=>25),
									   'cargorealm'=>array('justification'=>'center','width'=>25),
									   'cargorealv'=>array('justification'=>'center','width'=>25),
									   'cargoreal'=>array('justification'=>'center','width'=>35),
									   'montoreal'=>array('justification'=>'right','width'=>60),
									   'montoacum'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_totales($ls_totalf,$ls_totalm, $ls_totalv,$ls_totalcargo,$ls_totalmonto,
	                          $ls_totalrealf,$ls_totalrealm,$ls_totalrealv,$ls_totalreal,
							  $ls_totalmontoreal,$ls_totalmontoacum,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_totales
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los totales
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_data[1]=array('descripcion'=>'<b>TOTALES</b>',
						  'cargof'=>'<b>'.$ls_totalf.'</b>',
						  'cargom'=>'<b>'.$ls_totalm.'</b>',
						  'cargov'=>'<b>'.$ls_totalv.'</b>',
						  'cargo'=>'<b>'.$ls_totalcargo.'</b>',
						  'monto'=>'<b>'.number_format($ls_totalmonto,2,",",".").'</b>',
						  'cargorealf'=>'<b>'.$ls_totalrealf.'</b>',
						  'cargorealm'=>'<b>'.$ls_totalrealm.'</b>',
						  'cargorealv'=>'<b>'.$ls_totalrealv.'</b>',
						  'cargoreal'=>'<b>'.$ls_totalreal.'</b>',
						  'montoreal'=>'<b>'.number_format($ls_totalmontoreal,2,",",".").'</b>',
						  'montoacum'=>'<b>'.number_format($ls_totalmontoacum,2,",",".").'</b>');
		$la_columna=array('descripcion'=>'',
						  'cargof'=>'',
						  'cargom'=>'',
						  'cargov'=>'',
						  'cargo'=>'',
						  'monto'=>'',
						  'cargorealf'=>'',
						  'cargorealm'=>'',
						  'cargorealv'=>'',
						  'cargoreal'=>'',
						  'montoreal'=>'',
						  'montoacum'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 11,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xPos'=>505, // Ancho de la tabla
						 'width'=>940, // Ancho de la tabla
						 'maxWidth'=>940, // Ancho Máximo de la tabla
						 'xOrientation'=>'left', // Orientación de la tabla
						 'xPos'=>570,
						 'cols'=>array('descripcion'=>array('justification'=>'left','width'=>115),
									   'cargof'=>array('justification'=>'center','width'=>25),
									   'cargom'=>array('justification'=>'center','width'=>25),
									   'cargov'=>array('justification'=>'center','width'=>25),
									   'cargo'=>array('justification'=>'center','width'=>35),
									   'monto'=>array('justification'=>'right','width'=>60),
									   'cargorealf'=>array('justification'=>'center','width'=>25),
									   'cargorealm'=>array('justification'=>'center','width'=>25),
									   'cargorealv'=>array('justification'=>'center','width'=>25),
									   'cargoreal'=>array('justification'=>'center','width'=>35),
									   'montoreal'=>array('justification'=>'right','width'=>60),
									   'montoacum'=>array('justification'=>'right','width'=>60))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_totales
	//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="RECURSOS HUMANOS CLASIFICADOS POR GÉNERO";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_rango=$io_fun_nomina->uf_obtenervalor_get("rango",""); 
	$ls_periodo=$io_fun_nomina->uf_obtenervalor_get("periodo","");
	$ls_bolivares="Bolívares";
        //--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_instructivo_0205_programado(); // Obtenemos el detalle del reporte
	}
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
                $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(7.79,3,3,3); // Configuración de los margenes en centímetros
		uf_print_encabezado_pagina($ls_titulo,$ls_periodo,$ls_rango,$io_pdf); // Imprimimos el encabezado de la página		
		$li_i=0;
		$li_deduccion=0;
		$li_acumcargoprog=0;
		$li_acumcargoreal=0;
		$li_acummontoprog=0;
		$li_acummontoreal=0;	
		$li_acumcargoprogf=0;
		$li_acumcargoprogm=0;
		$li_acumcargoprogv=0;
		$li_acumcargorealf=0;
		$li_acumcargorealm=0;
		$li_acumcargorealv=0;
		$li_acummontoacum=0;
		while((!$io_report->rs_data->EOF)&&($lb_valido))
		{
			$ls_codigo=$io_report->rs_data->fields["codtipper"];
			$ls_denominacion="			".$io_report->rs_data->fields["destipper"];
			$ls_coded=$io_report->rs_data->fields["codded"];
			$ls_denoded=$io_report->rs_data->fields["desded"];
			$li_cargoreal=0;
			$li_cargorealf=0;
			$li_cargorealm=0;
			$li_cargorealv=0;
			$li_montoreal=0;
			$li_montoacum=0;
			$carene=$io_report->rs_data->fields["carene"];
			$carfeb=$io_report->rs_data->fields["carfeb"];
			$carmar=$io_report->rs_data->fields["carmar"];
			$carabr=$io_report->rs_data->fields["carabr"];
			$carmay=$io_report->rs_data->fields["carmay"];
			$carjun=$io_report->rs_data->fields["carjun"];
			$carjul=$io_report->rs_data->fields["carjul"];
			$carago=$io_report->rs_data->fields["carago"];
			$carsep=$io_report->rs_data->fields["carsep"];
			$caroct=$io_report->rs_data->fields["caroct"];
			$carnov=$io_report->rs_data->fields["carnov"];
			$cardic=$io_report->rs_data->fields["cardic"];
			$ls_cargo=0;
			
			$carenef=$io_report->rs_data->fields["carenef"];
			$carfebf=$io_report->rs_data->fields["carfebf"];
			$carmarf=$io_report->rs_data->fields["carmarf"];
			$carabrf=$io_report->rs_data->fields["carabrf"];
			$carmayf=$io_report->rs_data->fields["carmayf"];
			$carjunf=$io_report->rs_data->fields["carjunf"];
			$carjulf=$io_report->rs_data->fields["carjulf"];
			$caragof=$io_report->rs_data->fields["caragof"];
			$carsepf=$io_report->rs_data->fields["carsepf"];
			$caroctf=$io_report->rs_data->fields["caroctf"];
			$carnovf=$io_report->rs_data->fields["carnovf"];
			$cardicf=$io_report->rs_data->fields["cardicf"];
			$ls_cargof=0;
			
			$carenem=$io_report->rs_data->fields["carenem"];
			$carfebm=$io_report->rs_data->fields["carfebm"];
			$carmarm=$io_report->rs_data->fields["carmarm"];
			$carabrm=$io_report->rs_data->fields["carabrm"];
			$carmaym=$io_report->rs_data->fields["carmaym"];
			$carjunm=$io_report->rs_data->fields["carjunm"];
			$carjulm=$io_report->rs_data->fields["carjulm"];
			$caragom=$io_report->rs_data->fields["caragom"];
			$carsepm=$io_report->rs_data->fields["carsepm"];
			$caroctm=$io_report->rs_data->fields["caroctm"];
			$carnovm=$io_report->rs_data->fields["carnovm"];
			$cardicm=$io_report->rs_data->fields["cardicm"];
			$ls_cargom=0;
			
			$monene=$io_report->rs_data->fields["monene"];
			$monfeb=$io_report->rs_data->fields["monfeb"];
			$monmar=$io_report->rs_data->fields["monmar"];
			$monabr=$io_report->rs_data->fields["monabr"];
			$monmay=$io_report->rs_data->fields["monmay"];
			$monjun=$io_report->rs_data->fields["monjun"];
			$monjul=$io_report->rs_data->fields["monjul"];
			$monago=$io_report->rs_data->fields["monago"];
			$monsep=$io_report->rs_data->fields["monsep"];
			$monoct=$io_report->rs_data->fields["monoct"];
			$monnov=$io_report->rs_data->fields["monnov"];
			$mondic=$io_report->rs_data->fields["mondic"];
			$ls_monto=0;
			
			switch($ls_rango)
			{
				case "01":
					$li_cargo=$carmar;
					$li_cargof=$carmarf;
					$li_cargom=$carmarm;
					$li_cargov=$li_cargo-($li_cargof+$li_cargom);
					$li_monto=$monene+$monfeb+$monmar;
				break;
				case "02":
					$li_cargo=$carjun;
					$li_cargof=$carjunf;
					$li_cargom=$carjunm;
					$li_cargov=$li_cargo-($li_cargof+$li_cargom);
				    $li_monto=$monabr+$monmay+$monjun;
				break;
				case "03":
					$li_cargo=$carsep;
					$li_cargof=$carsepf;
					$li_cargom=$carsepm;
					$li_cargov=$li_cargo-($li_cargof+$li_cargom);
					$li_monto=$monjul+$monago+$monsep;
				break;
				case "04":
					$li_cargo=$cardic;
					$li_cargof=$cardicf;
					$li_cargom=$cardicm;
					$li_cargov=$li_cargo-($li_cargof+$li_cargom);
					$li_monto=$monoct+$monnov+$mondic;
				break;
			}
			$li_i++;
			if ($ls_codigo=="0000")
			{
				if ($li_deduccion>0)
				{
					$la_data[$li_deduccion]['cargof']='<b>'.$li_totalcargoprogf.'</b>';
					$la_data[$li_deduccion]['cargom']='<b>'.$li_totalcargoprogm.'</b>';
					$la_data[$li_deduccion]['cargov']='<b>'.$li_totalcargoprogv.'</b>';
					$la_data[$li_deduccion]['cargo']='<b>'.$li_totalcargoprog.'</b>';
					$la_data[$li_deduccion]['monto']='<b>'.number_format($li_totalmontoprog,2,",",".").'</b>';
					$la_data[$li_deduccion]['cargorealf']='<b>'.$li_totalcargorealf.'</b>';
					$la_data[$li_deduccion]['cargorealm']='<b>'.$li_totalcargorealm.'</b>';
					$la_data[$li_deduccion]['cargorealv']='<b>'.$li_totalcargorealv.'</b>';
					$la_data[$li_deduccion]['cargoreal']='<b>'.$li_totalcargoreal.'</b>';
					$la_data[$li_deduccion]['montoreal']='<b>'.number_format($li_totalmontoreal,2,",",".").'</b>';
					$la_data[$li_deduccion]['montoacum']='<b>'.number_format($li_totalmontoacum,2,",",".").'</b>';
				}
				$ls_denominacion = '<b>'.$ls_denoded.'</b>';
				$li_deduccion=$li_i;
				$li_totalcargoprog=0;
				$li_totalcargoreal=0;
				$li_totalmontoprog=0;
				$li_totalmontoreal=0;	
				$li_totalcargoprogf=0;
				$li_totalcargoprogm=0;
				$li_totalcargoprogv=0;
				$li_totalcargorealf=0;
				$li_totalcargorealm=0;
				$li_totalcargorealv=0;
				$li_totalmontoacum=0;
			}
			else
			{
				$arrResultado=$io_report->uf_instructivo_0205_cargos_real($ls_rango,$ls_coded,$ls_codigo,$li_cargoreal,$li_cargorealf,$li_cargorealm,$li_montoreal);
				$li_cargoreal=$arrResultado['ai_cargoreal'];
				$li_cargorealf=$arrResultado['ai_cargorealf'];
				$li_cargorealm=$arrResultado['ai_cargorealm'];
				$li_montoreal=$arrResultado['ai_montoreal'];
				$lb_valido=$arrResultado['lb_valido'];
				if($lb_valido)
				{
					$arrResultado=$io_report->uf_instructivo_0205_monto_acumulado($ls_rango,$ls_coded,$ls_codigo,$li_montoacum);
					$li_montoacum=$arrResultado['ai_monto_acumulado'];
					$lb_valido=$arrResultado['lb_valido'];
				}
				$li_cargorealv=$li_cargo-($li_cargorealf+$li_cargorealm);
				$li_totalcargoprog=$li_totalcargoprog+$li_cargo;
				$li_totalcargoreal=$li_totalcargoreal+$li_cargoreal;
				$li_totalmontoprog=$li_totalmontoprog+$li_monto;
				$li_totalmontoreal=$li_totalmontoreal+$li_montoreal;	
				$li_totalcargoprogf=$li_totalcargoprogf+$li_cargof;
				$li_totalcargoprogm=$li_totalcargoprogm+$li_cargom;
				$li_totalcargoprogv=$li_totalcargoprogv+$li_cargov;
				$li_totalcargorealf=$li_totalcargorealf+$li_cargorealf;
				$li_totalcargorealm=$li_totalcargorealm+$li_cargorealm;
				$li_totalcargorealv=$li_totalcargorealv+$li_cargorealv;
				$li_totalmontoacum=$li_totalmontoacum+$li_montoacum;
				$li_acumcargoprog=$li_acumcargoprog+$li_cargo;
				$li_acumcargoreal=$li_acumcargoreal+$li_cargoreal;
				$li_acummontoprog=$li_acummontoprog+$li_monto;
				$li_acummontoreal=$li_acummontoreal+$li_montoreal;	
				$li_acumcargoprogf=$li_acumcargoprogf+$li_cargof;
				$li_acumcargoprogm=$li_acumcargoprogm+$li_cargom;
				$li_acumcargoprogv=$li_acumcargoprogv+$li_cargov;
				$li_acumcargorealf=$li_acumcargorealf+$li_cargorealf;
				$li_acumcargorealm=$li_acumcargorealm+$li_cargorealm;
				$li_acumcargorealv=$li_acumcargorealv+$li_cargorealv;
				$li_acummontoacum=$li_acummontoacum+$li_montoacum;
			}
			$la_data[$li_i]=array('descripcion'=> $ls_denominacion,
								  'cargof'=>$li_cargof,
								  'cargom'=>$li_cargom,
								  'cargov'=>$li_cargov,
								  'cargo'=>$li_cargo,
								  'monto'=>number_format($li_monto,2,",","."),
								  'cargorealf'=>$li_cargorealf,
								  'cargorealm'=>$li_cargorealm,
								  'cargorealv'=>$li_cargorealv,
								  'cargoreal'=>$li_cargoreal,
								  'montoreal'=>number_format($li_montoreal,2,",","."),
								  'montoacum'=>number_format($li_montoacum,2,",","."));
			$io_report->rs_data->MoveNext();
		}// fin for (Programado)		
		if ($li_i>0)
		{
			$la_data[$li_deduccion]['cargof']='<b>'.$li_totalcargoprogf.'</b>';
			$la_data[$li_deduccion]['cargom']='<b>'.$li_totalcargoprogm.'</b>';
			$la_data[$li_deduccion]['cargov']='<b>'.$li_totalcargoprogv.'</b>';
			$la_data[$li_deduccion]['cargo']='<b>'.$li_totalcargoprog.'</b>';
			$la_data[$li_deduccion]['monto']='<b>'.number_format($li_totalmontoprog,2,",",".").'</b>';
			$la_data[$li_deduccion]['cargorealf']='<b>'.$li_totalcargorealf.'</b>';
			$la_data[$li_deduccion]['cargorealm']='<b>'.$li_totalcargorealm.'</b>';
			$la_data[$li_deduccion]['cargorealv']='<b>'.$li_totalcargorealv.'</b>';
			$la_data[$li_deduccion]['cargoreal']='<b>'.$li_totalcargoreal.'</b>';
			$la_data[$li_deduccion]['montoreal']='<b>'.number_format($li_totalmontoreal,2,",",".").'</b>';
			$la_data[$li_deduccion]['montoacum']='<b>'.number_format($li_totalmontoacum,2,",",".").'</b>';
			uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
			uf_print_totales($li_acumcargoprogf,$li_acumcargoprogm, $li_acumcargoprogv,$li_acumcargoprog,$li_acummontoprog,
							 $li_acumcargorealf,$li_acumcargorealm,$li_acumcargorealv,$li_acumcargoreal,$li_acummontoreal,
							 $li_acummontoacum,$io_pdf);
			unset($la_data);
		}
		if(($lb_valido)&&($li_i>0)) // Si no ocurrio ningún error
		{
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 