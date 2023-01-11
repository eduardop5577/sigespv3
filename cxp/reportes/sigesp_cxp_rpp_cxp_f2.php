<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_recepciones.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_tipproben,$as_codprobendes,$as_codprobenhas,$as_nomprobendes,$as_nomprobenhas,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
        $io_pdf->Rectangle(15,530,962,60);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=496-($li_tm/2);
		$io_pdf->addText($tm,575,11,$as_titulo); // Agregar el título
		if(($as_codprobendes!="")&&($as_codprobendes!=""))
		{
			switch($as_tipproben)
			{
				case"P":
					if($as_codprobendes==$as_codprobenhas)
					{
						$ls_criterio="Proveedor: ".$as_codprobendes." - <b>".$as_nomprobendes."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,560,8,$ls_criterio); // Agregar el título
					
					}
					else
					{
						$ls_criterio="Proveedores: ";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,565,8,$ls_criterio); // Agregar el título
						$ls_criterio="Desde: ".$as_codprobendes." - <b>".$as_nomprobendes."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,555,8,$ls_criterio); // Agregar el título
						$ls_criterio="Hasta: ".$as_codprobenhas." - <b>".$as_nomprobenhas."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,545,8,$ls_criterio); // Agregar el título
					}
				break;
				case"B":
					if($as_codprobendes==$as_codprobenhas)
					{
						$ls_criterio="Beneficiario: ".$as_codprobendes." - <b>".$as_nomprobendes."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,560,8,$ls_criterio); // Agregar el título
					
					}
					else
					{
						$ls_criterio="Beneficiarios: ";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,565,8,$ls_criterio); // Agregar el título
						$ls_criterio="Desde: ".$as_codprobendes." - <b>".$as_nomprobendes."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,555,8,$ls_criterio); // Agregar el título
						$ls_criterio="Hasta: ".$as_codprobenhas." - <b>".$as_nomprobenhas."</b>";
						$li_tm=$io_pdf->getTextWidth(8,$ls_criterio);
						$tm=496-($li_tm/2);
						$io_pdf->addText($tm,545,8,$ls_criterio); // Agregar el título
					}
				break;
			}
		}
		$li_tm=$io_pdf->getTextWidth(8,$as_periodo);
		$tm=496-($li_tm/2);
		$io_pdf->addText($tm,535,8,$as_periodo); // Agregar el título
		// cuadro inferior
         $io_pdf->Rectangle(15,60,962,70);
       //$io_pdf->Rectangle(10,60,762,70);
		$io_pdf->line(15,73,977,73);		
		$io_pdf->line(15,117,977,117);		
		$io_pdf->line(255,60,255,130);		
		$io_pdf->line(495,60,495,130);		
		$io_pdf->line(735,60,735,130);		
		$io_pdf->addText(90,122,7,"ELABORADO POR"); // Agregar el título
		$io_pdf->addText(95,63,7,"FIRMA / SELLO"); // Agregar el título
		$io_pdf->addText(332,122,7,"VERIFICADO POR"); // Agregar el título
		$io_pdf->addText(322,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->addText(580,122,7,"AUTORIZADO POR"); // Agregar el título
		$io_pdf->addText(560,63,7,"ADMINISTRACIÓN Y FINANZAS"); // Agregar el título
		$io_pdf->addText(815,122,7,"CONTRALORIA INTERNA"); // Agregar el título
		$io_pdf->addText(815,63,7,"FIRMA / SELLO / FECHA"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle_recepcion($la_data,$li_totalbasimp,$li_totalbasiva,$li_totalded,$li_totalcar,$li_totretiva,$li_totretislr,$li_totretaposol,$li_totretmilp,$li_totmontotdoc,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//				   li_totaldoc // acumulado del total
		//				   li_totalcar // acumulado de los cargos
		//				   li_totalded // acumulado de las deducciones
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle de las recepciones de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;

		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->ezSetY(520);
		$la_datatit[1]=array('codusureg'=>'<b>Usuario</b>',
							 'numrecdoc'=>'<b>Documento</b>',
							 'fecemidoc'=>'<b>Proveedor / Beneficiario</b>',
							 'fecemidoc'=>'<b>Fecha Emision</b>',
							 'fecregdoc'=>'<b>Fecha Registro</b>',
							 'nombre'=>'<b>Proveedor</b>',
							 'procede_doc'=>'<b>Procedencia</b>',
							 'numdoccom'=>'<b>Compromiso</b>',
						     'basimp'=>'<b>Sub Total</b>',
							 'moncardoc'=>'<b>Cargos</b>',
							 'basiva'=>'<b>Total Factura</b>',
							 'islr'=>'<b>Reten. ISLR</b>',
							 'iva'=>'<b>Reten. IVA</b>',
							 'retaposol'=>'<b>Reten. Aporte Social</b>',
							 'estretmil'=>'<b>Reten. 1x1000</b>',
							 'montotdoc'=>'<b>Neto a Pagar</b>',
							 'estprosol'=>'<b>Estatus</b>',
							 'cheques'=>'<b>Cheque</b>',
							 'fechache'=>'<b>Fecha</b>');
		$la_columnas=array('codusureg'=>'<b>Usuario</b>',
						   'numrecdoc'=>'<b>Documento</b>',
						   'fecemidoc'=>'<b>Proveedor / Beneficiario</b>',
						   'fecemidoc'=>'<b>Fecha Emision</b>',
						   'fecregdoc'=>'<b>Fecha Registro</b>',
						   'nombre'=>'<b>Fecha Registro</b>',
						   'procede_doc'=>'<b>Procedencia</b>',
						   'numdoccom'=>'<b>Compromiso</b>',
						   'basimp'=>'<b>Sub Total</b>',
						   'moncardoc'=>'<b>Cargos</b>',
						   'basiva'=>'<b>Total Factura</b>',
						   'islr'=>'<b>Cargos</b>',
						   'iva'=>'<b>Cargos</b>',
						   'retaposol'=>'<b>Reten. Aporte Social</b>',
						   'estretmil'=>'<b>Reten. 1x1000</b>',
						   'montotdoc'=>'<b>Neto a Pagar</b>',
						   'estprosol'=>'<b>Estatus</b>',
						   'moncardoc'=>'<b>Cargos</b>',
						   'cheques'=>'<b>Cheque</b>',
						   'fechache'=>'<b>Fecha</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codusureg'=>array('justification'=>'center','width'=>60), // Justificación y ancho de la columna
						 			   'numrecdoc'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'fecregdoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'center','width'=>120), // Justificación y ancho de la columna
									   'procede_doc'=>array('justification'=>'center','width'=>53),// Justificación y ancho de la columna
									   'numdoccom'=>array('justification'=>'center','width'=>70), // Justificación y ancho de la columna
						 			   'basimp'=>array('justification'=>'center','width'=>48), // Justificación y ancho de la columna
						 			   'moncardoc'=>array('justification'=>'center','width'=>48), // Justificación y ancho de la columna
						 			   'basiva'=>array('justification'=>'center','width'=>48), // Justificación y ancho de la columna
						 			   'islr'=>array('justification'=>'center','width'=>43), // Justificación y ancho de la columna
						 			   'iva'=>array('justification'=>'center','width'=>43), // Justificación y ancho de la columna
						 			   'retaposol'=>array('justification'=>'center','width'=>43), // Justificación y ancho de la columna
						 			   'estretmil'=>array('justification'=>'center','width'=>43), // Justificación y ancho de la columna
						 			   'montotdoc'=>array('justification'=>'center','width'=>48), // Justificación y ancho de la columna
						 			   'estprosol'=>array('justification'=>'center','width'=>48), // Justificación y ancho de la columna
						 			   'cheques'=>array('justification'=>'center','width'=>65), // Justificación y ancho de la columna
						 			   'fechache'=>array('justification'=>'center','width'=>42))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatit,$la_columnas,'',$la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');

		$la_columnas=array('codusureg'=>'<b>Documento</b>',
						   'numrecdoc'=>'<b>Expediente</b>',
						   'fecemidoc'=>'<b>Proveedor / Beneficiario</b>',
						   'fecemidoc'=>'<b>Fecha Emision</b>',
						   'fecregdoc'=>'<b>Fecha Registro</b>',
						   'nombre'=>'<b>Proveedor</b>',
						   'procede_doc'=>'<b>Procedencia</b>',
						   'numdoccom'=>'<b>Compromiso</b>',
						   'basimp'=>'<b>Base Imponible</b>',
						   'moncardoc'=>'<b>Cargos</b>',
						   'basiva'=>'<b>Total Factura</b>',
						   'islr'=>'<b>Cargos</b>',
						   'iva'=>'<b>Cargos</b>',
						   'retaposol'=>'<b>Reten. Aporte Social</b>',
						   'estretmil'=>'<b>Reten. 1x1000</b>',
						   'montotdoc'=>'<b>Monto Total Factura</b>',
						   'estprosol'=>'<b>Estatus</b>',
						   'moncardoc'=>'<b>Cargos</b>',
						   'cheques'=>'<b>Cheque</b>',
						   'fechache'=>'<b>Fecha</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codusureg'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
						 			   'numrecdoc'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'left','width'=>45), // Justificación y ancho de la columna
						 			   'fecemidoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'fecregdoc'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>120), // Justificación y ancho de la columna
									   'procede_doc'=>array('justification'=>'center','width'=>53),// Justificación y ancho de la columna
									   'numdoccom'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'basimp'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'moncardoc'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'basiva'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'islr'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'iva'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'retaposol'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'estretmil'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'montotdoc'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'estprosol'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'cheques'=>array('justification'=>'right','width'=>65), // Justificación y ancho de la columna
						 			   'fechache'=>array('justification'=>'right','width'=>42))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		$la_datatot[1]=array('numrecdoc'=>'<b>Totales Bs.</b>','totalbasimp'=>$li_totalbasimp,'totalbasiva'=>$li_totalbasiva,'totalcar'=>$li_totalcar,'totretiva'=>$li_totretiva,'totretislr'=>$li_totretislr,'totretaposol'=>$li_totretaposol,'totretmilp'=>$li_totretmilp,'totmontotdoc'=>$li_totmontotdoc,'estprosol'=>'');
		$la_columnas=array('numrecdoc'=>'<b>Totales Bs.</b>','totalbasimp'=>$li_totalbasimp,'totalcar'=>$li_totalcar,'totalbasiva'=>$li_totalbasiva,'totretislr'=>$li_totretislr,'totretiva'=>$li_totretiva,'totretaposol'=>$li_totretaposol,'totretmilp'=>$li_totretmilp,'totmontotdoc'=>$li_totmontotdoc,'estprosol'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('numrecdoc'=>array('justification'=>'right','width'=>443), // Justificación y ancho de la columna
						 			   'totalbasimp'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'totalcar'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'totalbasiva'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'totretislr'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'totretiva'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'totretaposol'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'totretmilp'=>array('justification'=>'right','width'=>43), // Justificación y ancho de la columna
						 			   'totmontotdoc'=>array('justification'=>'right','width'=>48), // Justificación y ancho de la columna
						 			   'estprosol'=>array('justification'=>'right','width'=>155))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_datatot,$la_columnas,'',$la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	//Instancio a la clase de conversión de numeros a letras.
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>CUENTAS POR PAGAR</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_tipproben=$io_fun_cxp->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=trim($io_fun_cxp->uf_obtenervalor_get("codprobendes",""));
	$ls_codprobenhas=trim($io_fun_cxp->uf_obtenervalor_get("codprobenhas",""));
	$ld_fecregdes=$io_fun_cxp->uf_obtenervalor_get("fecregdes","");
	$ld_fecreghas=$io_fun_cxp->uf_obtenervalor_get("fecreghas","");
	$ls_orden=$io_fun_cxp->uf_obtenervalor_get("orden","");
	$ls_nomprobendes="";
	$ls_nomprobenhas="";
	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_periodo="";
	if(($ld_fecregdes!="")&&($ld_fecreghas!=""))
	{
		$ls_periodo="<b>Del: </b>".$ld_fecregdes."   "."<b>Al: </b>".$ld_fecreghas;	
	}
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{

		$lb_valido=$io_report->uf_select_cxp_f2($ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ld_fecregdes,$ld_fecreghas,$ls_orden); // Cargar el DS con los datos del reporte
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			//print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(4.3,5,3,3); // Configuración de los margenes en centímetros
			$io_pdf->ezStartPageNumbers(970,47,8,'','',1); // Insertar el número de página
			$li_totrow=$io_report->DS->getRowCount("numrecdoc");
			if($ls_codprobendes!="")
				$ls_nomprobendes=$io_report->uf_select_proveedores($ls_tipproben,$ls_codprobendes);
			if($ls_codprobenhas!="")
				$ls_nomprobenhas=$io_report->uf_select_proveedores($ls_tipproben,$ls_codprobenhas);
			$li_totalbasimp= 0;
			$li_totalbasiva= 0;
			$li_totalded= 0;
			$li_totalcar= 0;

			$li_totretiva=0;
			$li_totretislr= 0;
			$li_totretaposol= 0;
			$li_totmontotdoc=0;
			$li_totretmil= 0;
			for($li_i=1;$li_i<=$li_totrow;$li_i++)
			{
				$ls_numrecdoc= $io_report->DS->data["numrecdoc"][$li_i];
				$ls_codpro= $io_report->DS->data["cod_pro"][$li_i];
				$ls_cedbene= $io_report->DS->data["ced_bene"][$li_i];
				$ls_codtipdoc= $io_report->DS->data["codtipdoc"][$li_i];
				$ls_nombre= $io_report->DS->data["nombre"][$li_i]; 
				$ls_numsol= $io_report->DS->data["numsol"][$li_i]; 
				$ls_estprosol= $io_report->DS->data["estprosol"][$li_i]; 
				switch ($ls_estprosol)
				{
					case "R":
						$ls_estprosol="Registro";
						break;
						
					case "S":
						$ls_estprosol="Programacion de Pago";
						break;
						
					case "P":
						$ls_estprosol="Cancelada";
						break;

					case "A":
						$ls_estprosol="Anulada";
						break;
						
					case "C":
						$ls_estprosol="Contabilizada";
						break;
						
					case "E":
						$ls_estprosol="Emitida";
						break;
						
					case "N":
						$ls_estprosol="Anulada sin Afectacion";
						break;
				}
				$ls_procede= $io_report->DS->data["procede_doc"][$li_i];
				if($ls_procede=="")
				{
					$ls_procede=$io_report->DS->data["procede_cont"][$li_i];
				}
				$ls_numdoccom= $io_report->DS->data["numdoccom"][$li_i];
				if($ls_numdoccom=="")
				{
					$ls_numdoccom=$io_report->DS->data["numdoccont"][$li_i];
				}
				$ls_codproben= $ls_codpro;
				if($ls_codproben=="----------")
				{
					$ls_codproben= $ls_cedbene;
				}
				$ld_fecemidoc= $io_report->DS->data["fecemidoc"][$li_i];
				$ld_fecregdoc= $io_report->DS->data["fecregdoc"][$li_i];
				$ls_codusureg= $io_report->DS->data["codusureg"][$li_i];
				$li_pagado=$io_report->uf_select_informacionpagos($ls_numsol);
				$ls_cheques=$io_report->uf_select_informacioncheques($ls_numsol);
				$ls_fechache=$io_report->uf_select_fechapagos($ls_numsol);
				$ld_fecemidoc= $io_funciones->uf_convertirfecmostrar($ld_fecemidoc);
				$ld_fecregdoc= $io_funciones->uf_convertirfecmostrar($ld_fecregdoc);
				$ls_fechache= $io_funciones->uf_convertirfecmostrar($ls_fechache);
				
				$li_montotdoc= $io_report->DS->data["montotdoc"][$li_i];
				$li_mondeddoc= $io_report->DS->data["mondeddoc"][$li_i];
				$li_moncardoc= $io_report->DS->data["moncardoc"][$li_i];
				$li_iva=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'iva');
				$li_islr=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'islr');
				$li_retaposol=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'retaposol');
				$li_estretmil=$io_report->uf_retenciones_factura($ls_codpro,$ls_cedbene,$ls_numrecdoc,$ls_codtipdoc,'estretmil');
				$li_basimp=$li_montotdoc+$li_mondeddoc-$li_moncardoc;
				$li_basiva=$li_basimp+$li_moncardoc;
				
				$li_totalbasimp= $li_totalbasimp + $li_basimp;
				$li_totalbasiva= $li_totalbasiva + $li_basiva;
				$li_totalded= $li_totalded + $li_mondeddoc;
				$li_totalcar= $li_totalcar + $li_moncardoc;
				$li_totmontotdoc=$li_totmontotdoc+$li_montotdoc;

				$li_totretiva= $li_totretiva + $li_iva;
				$li_totretislr= $li_totretislr + $li_islr;
				$li_totretaposol= $li_totretaposol + $li_retaposol;
				$li_totretmil= $li_totretmil + $li_estretmil;
				
				$li_montotdoc= number_format($li_montotdoc,2,',','.');
				$li_mondeddoc= number_format($li_mondeddoc,2,',','.');
				$li_moncardoc= number_format($li_moncardoc,2,',','.');
				$li_basimp= number_format($li_basimp,2,',','.');
				$li_basiva= number_format($li_basiva,2,',','.');
				$li_iva= number_format($li_iva,2,',','.');
				$li_islr= number_format($li_islr,2,',','.');
				$li_retaposol= number_format($li_retaposol,2,',','.');
				$li_estretmil= number_format($li_estretmil,2,',','.');
				$la_data[$li_i]=array('codusureg'=>$ls_codusureg,'numrecdoc'=>$ls_numrecdoc,'fecemidoc'=>$ld_fecemidoc,'fecregdoc'=>$ld_fecregdoc,'codproben'=>$ls_codproben,
									  'nombre'=>$ls_nombre,'procede_doc'=>$ls_procede,'numdoccom'=>$ls_numdoccom,'basimp'=>$li_basimp,
									  'moncardoc'=>$li_moncardoc,'basiva'=>$li_basiva,'islr'=>$li_islr,'iva'=>$li_iva,'retaposol'=>$li_retaposol,
									  'estretmil'=>$li_estretmil,'montotdoc'=>$li_montotdoc,'montotdoc'=>$li_montotdoc,'montotdoc'=>$li_montotdoc,
									  'montotdoc'=>$li_montotdoc,'estprosol'=>$ls_estprosol,'cheques'=>$ls_cheques,'fechache'=>$ls_fechache);
			}
			$li_totalbasimp= number_format($li_totalbasimp,2,',','.');
			$li_totalbasiva= number_format($li_totalbasiva,2,',','.');
			$li_totalded= number_format($li_totalded,2,',','.');
			$li_totalcar= number_format($li_totalcar,2,',','.');
			$li_totretiva= number_format($li_totretiva,2,',','.');
			$li_totretislr= number_format($li_totretislr,2,',','.');
			$li_totretaposol= number_format($li_totretaposol,2,',','.');
			$li_totretmil= number_format($li_totretmil,2,',','.');
			$li_totmontotdoc= number_format($li_totmontotdoc,2,',','.');
			uf_print_encabezado_pagina($ls_titulo,$ls_tipproben,$ls_codprobendes,$ls_codprobenhas,$ls_nomprobendes,$ls_nomprobenhas,$ls_periodo,$io_pdf);
			uf_print_detalle_recepcion($la_data,$li_totalbasimp,$li_totalbasiva,$li_totalded,$li_totalcar,$li_totretiva,$li_totretislr,$li_totretaposol,$li_totretmil,$li_totmontotdoc,$io_pdf);
			if($lb_valido) // Si no ocurrio ningún error
			{
				$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
				$io_pdf->ezStream(); // Mostramos el reporte
			}
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}

?>
