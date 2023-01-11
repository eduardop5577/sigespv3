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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_cxp;
		$lb_valido=true;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_cxp->uf_load_seguridad_reporte("CXP","sigesp_cxp_r_actaretencion.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,680,13,$as_titulo); // Agregar el título
		if($as_fecha=="1")
		{
			$io_pdf->addText(512,750,8,date("d/m/Y")); // Agregar la Fecha
			$io_pdf->addText(518,743,7,date("h:i a")); // Agregar la Hora
		}
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_cxp_class_report.php");
	$io_report=new sigesp_cxp_class_report();
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$io_numero_letra= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$io_numero_letra->setMayusculas(1);
	//cambia a femenino
	$io_numero_letra->setGenero(1);
	//cambia moneda
	$io_numero_letra->setMoneda("Bolivares");
	//cambia prefijo
	$io_numero_letra->setPrefijo("");
	//cambia sufijo
	$io_numero_letra->setSufijo("");
	//imprime numero con los cambios
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	require_once("../../shared/class_folder/evaluate_formula.php");
	$io_evaluate=new evaluate_formula();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<i>CONSTANCIA</i>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_comprobantes=$io_fun_cxp->uf_obtenervalor_get("comprobantes","");
	$ls_codigo=$io_fun_cxp->uf_obtenervalor_get("codigo","");
	$ls_tipcont=$io_fun_cxp->uf_obtenervalor_get("tipcont","");
	$ls_codcont=$io_fun_cxp->uf_obtenervalor_get("codcont","");
	$ls_condpag=$io_fun_cxp->uf_obtenervalor_get("condpag","");

	$arr_comprobantes=explode ("@@",$ls_comprobantes); 
	$li_totcomprobantes=count((array)$arr_comprobantes);
	global $ls_tiporeporte;
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_existe=false;
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte	
	if($lb_valido)
	{
		$rs_data=$io_report->uf_acta_retencion($ls_codigo); // Obtenemos el detalle del reporte
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
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(1,1,3,3); // Configuración de los margenes en centímetros
		if ((!$rs_data->EOF))
		{
			$lb_existe=true;
			$ls_encabezado=$rs_data->fields["encabezado"];
			$ls_cuerpo=$rs_data->fields["cuerpo"];
			$ls_pie=$rs_data->fields["pie"];
		}
		if($lb_existe)
		{
			for($li_i=0;$li_i<$li_totcomprobantes;$li_i++)
			{
				$arr_documentos=explode ("**",$arr_comprobantes[$li_i]);
				$ls_compromiso=$arr_documentos[0];
				$ls_numrecdoc=$arr_documentos[1];
				$ls_codtipdoc=$arr_documentos[2];
				$ls_cedbene=$arr_documentos[3];
				$ls_codpro=$arr_documentos[4];
				$rs_valores=$io_report->uf_obtener_valores($ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedbene); // Obtenemos el detalle del reporte
				
				if ((!$rs_valores->EOF))
				{
					$ls_numrecdoc=$rs_valores->fields["numrecdoc"];
					$ls_dirpro=$rs_valores->fields["dirpro"];
					$ls_dencondoc=$rs_valores->fields["dencondoc"];
					$ls_montotdoc=$rs_valores->fields["montotdoc"];
					$ls_moncardoc=$rs_valores->fields["moncardoc"];
					$ls_mondeddoc=$rs_valores->fields["mondeddoc"];
					$ls_mondeddoc=$rs_valores->fields["mondeddoc"];
					$ls_monret=$rs_valores->fields["monret"];
					$ls_nompro=$rs_valores->fields["nompro"];
					$ls_cedrep=$rs_valores->fields["cedrep"];
					$ls_nomreppro=$rs_valores->fields["nomreppro"];
					$ls_carrep=$rs_valores->fields["carrep"];
					$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
					$ls_ciuemp=$_SESSION["la_empresa"]["ciuemp"];
					$ls_dia=date("d");
					$ls_mes=$io_fecha->uf_load_nombre_mes(date("m"));
					$ls_ano=date("Y");
					$ls_hora=date("h:i A");
					$ls_baseimp=$ls_montotdoc+$ls_mondeddoc-$ls_moncardoc;
					$io_numero_letra->setNumero($ls_baseimp);
					$ls_biletras= $io_numero_letra->letra();
					$ls_totiva=$ls_montotdoc+$ls_mondeddoc;
					$ls_baseimp=number_format($ls_baseimp,2,',','.');
					$ls_moncardoc=number_format($ls_moncardoc,2,',','.');
					$ls_totiva=number_format($ls_totiva,2,',','.');
					$ls_mondeddoc=number_format($ls_mondeddoc,2,',','.');
					$io_numero_letra->setNumero($ls_monret);
					$ls_monletras= $io_numero_letra->letra();
					$ls_monret=number_format($ls_monret,2,',','.');
					if($ls_encabezado!="")
					{
						$ls_encabezado=str_replace("@empresa@",$ls_nomemp,$ls_encabezado);
						$ls_encabezado=str_replace("@ciudad@",$ls_ciuemp,$ls_encabezado);
						$ls_encabezado=str_replace("@dia@",$ls_dia,$ls_encabezado);
						$ls_encabezado=str_replace("@mes@",$ls_mes,$ls_encabezado);
						$ls_encabezado=str_replace("@ano@",$ls_ano,$ls_encabezado);
						$ls_encabezado=str_replace("@hora@",$ls_hora,$ls_encabezado);
						$ls_encabezado=str_replace("@representante@",$ls_nomreppro,$ls_encabezado);
						$ls_encabezado=str_replace("@cedularep@",$ls_cedrep,$ls_encabezado);
						$ls_encabezado=str_replace("@cargorep@",$ls_carrep,$ls_encabezado);
						$ls_encabezado=str_replace("@proveedor@",$ls_nompro,$ls_encabezado);
						$ls_encabezado=str_replace("@compromiso@",$ls_compromiso,$ls_encabezado);
						$ls_encabezado=str_replace("@conceptocomp@",$ls_dencondoc,$ls_encabezado);
						$ls_encabezado=str_replace("@tipocontra@",$ls_tipcont,$ls_encabezado);
						$ls_encabezado=str_replace("@codigocont@",$ls_codcont,$ls_encabezado);
						$ls_encabezado=str_replace("@condpago@",$ls_condpag,$ls_encabezado);
						$ls_encabezado=str_replace("@baseimponible@",$ls_baseimp,$ls_encabezado);
						$ls_encabezado=str_replace("@montoretenido@",$ls_moncardoc,$ls_encabezado);
						$ls_encabezado=str_replace("@montototal@",$ls_totiva,$ls_encabezado);
						$ls_encabezado=str_replace("@aporteletras@",$ls_monletras,$ls_encabezado);
						$ls_encabezado=str_replace("@aportesocial@",$ls_monret,$ls_encabezado);
						$ls_encabezado=str_replace("@numrecdoc@",$ls_numrecdoc,$ls_encabezado);
						$ls_encabezado=str_replace("@dirproveedor@",$ls_dirpro,$ls_encabezado);
						$ls_encabezado=str_replace("@biletras@",$ls_biletras,$ls_encabezado);
						
//						$io_pdf->ezText($ls_encabezado,12,array('justification' =>'full','spacing' =>1));
//						$li_pos=700;
						//$li_texto1=$io_pdf->addTextWrap(50,$li_pos,500,12,$ls_encabezado,'center');
						//$io_pdf->addTextWrap(50,$li_pos,500,8,$li_texto1,'center');
					}
					
					
					if($ls_cuerpo!="")
					{
						$ls_cuerpo=str_replace("@empresa@",$ls_nomemp,$ls_cuerpo);
						$ls_cuerpo=str_replace("@ciudad@",$ls_ciuemp,$ls_cuerpo);
						$ls_cuerpo=str_replace("@dia@",$ls_dia,$ls_cuerpo);
						$ls_cuerpo=str_replace("@mes@",$ls_mes,$ls_cuerpo);
						$ls_cuerpo=str_replace("@ano@",$ls_ano,$ls_cuerpo);
						$ls_cuerpo=str_replace("@hora@",$ls_hora,$ls_cuerpo);
						$ls_cuerpo=str_replace("@representante@",$ls_nomreppro,$ls_cuerpo);
						$ls_cuerpo=str_replace("@cedularep@",$ls_cedrep,$ls_cuerpo);
						$ls_cuerpo=str_replace("@cargorep@",$ls_carrep,$ls_cuerpo);
						$ls_cuerpo=str_replace("@proveedor@",$ls_nompro,$ls_cuerpo);
						$ls_cuerpo=str_replace("@compromiso@",$ls_compromiso,$ls_cuerpo);
						$ls_cuerpo=str_replace("@conceptocomp@",$ls_dencondoc,$ls_cuerpo);
						$ls_cuerpo=str_replace("@tipocontra@",$ls_tipcont,$ls_cuerpo);
						$ls_cuerpo=str_replace("@codigocont@",$ls_codcont,$ls_cuerpo);
						$ls_cuerpo=str_replace("@condpago@",$ls_condpag,$ls_cuerpo);
						$ls_cuerpo=str_replace("@baseimponible@",$ls_baseimp,$ls_cuerpo);
						$ls_cuerpo=str_replace("@montoretenido@",$ls_moncardoc,$ls_cuerpo);
						$ls_cuerpo=str_replace("@montototal@",$ls_totiva,$ls_cuerpo);
						$ls_cuerpo=str_replace("@aporteletras@",$ls_monletras,$ls_cuerpo);
						$ls_cuerpo=str_replace("@aportesocial@",$ls_monret,$ls_cuerpo);
						$ls_cuerpo=str_replace("@numrecdoc@",$ls_numrecdoc,$ls_cuerpo);
						$ls_cuerpo=str_replace("@dirproveedor@",$ls_dirpro,$ls_cuerpo);
						$ls_cuerpo=str_replace("@biletras@",$ls_biletras,$ls_cuerpo);
						
						
					}



					if($ls_pie!="")
					{
						$ls_pie=str_replace("@empresa@",$ls_nomemp,$ls_pie);
						$ls_pie=str_replace("@ciudad@",$ls_ciuemp,$ls_pie);
						$ls_pie=str_replace("@dia@",$ls_dia,$ls_pie);
						$ls_pie=str_replace("@mes@",$ls_mes,$ls_pie);
						$ls_pie=str_replace("@ano@",$ls_ano,$ls_pie);
						$ls_pie=str_replace("@hora@",$ls_hora,$ls_pie);
						$ls_pie=str_replace("@representante@",$ls_nomreppro,$ls_pie);
						$ls_pie=str_replace("@cedularep@",$ls_cedrep,$ls_pie);
						$ls_pie=str_replace("@cargorep@",$ls_carrep,$ls_pie);
						$ls_pie=str_replace("@proveedor@",$ls_nompro,$ls_pie);
						$ls_pie=str_replace("@compromiso@",$ls_compromiso,$ls_pie);
						$ls_pie=str_replace("@conceptocomp@",$ls_dencondoc,$ls_pie);
						$ls_pie=str_replace("@tipocontra@",$ls_tipcont,$ls_pie);
						$ls_pie=str_replace("@codigocont@",$ls_codcont,$ls_pie);
						$ls_pie=str_replace("@condpago@",$ls_condpag,$ls_pie);
						$ls_pie=str_replace("@baseimponible@",$ls_baseimp,$ls_pie);
						$ls_pie=str_replace("@montoretenido@",$ls_moncardoc,$ls_pie);
						$ls_pie=str_replace("@montototal@",$ls_totiva,$ls_pie);
						$ls_pie=str_replace("@aporteletras@",$ls_monletras,$ls_pie);
						$ls_pie=str_replace("@aportesocial@",$ls_monret,$ls_pie);
						$ls_pie=str_replace("@numrecdoc@",$ls_numrecdoc,$ls_pie);
						$ls_pie=str_replace("@dirproveedor@",$ls_dirpro,$ls_pie);
						$ls_pie=str_replace("@biletras@",$ls_biletras,$ls_pie);

					}
					$li_marinfdoc=2;
					$li_intlindoc=1.5;
					$li_tamletpiedoc=8;
					$li_tamletdoc=10;
					$io_pdf->ezSetY(780);
					$io_pdf->ezText($ls_encabezado,12,array('justification' =>'full','spacing' =>1));
					$li_pos=($li_marinfdoc*10)*(72/25.4);
					
					$io_pdf->ezSetY(680);
					$io_pdf->ezText($ls_cuerpo,$li_tamletdoc,array('justification' =>'full','spacing' =>$li_intlindoc));
					$li_pos=($li_marinfdoc*10)*(72/25.4);
					
					$io_pdf->ezSetY(100);
					$io_pdf->ezText($ls_pie,8,array('justification' =>'full','spacing' =>1));
					$li_pos=($li_marinfdoc*10)*(72/25.4);
										


					if($li_i+1<$li_totcomprobantes)
					{
						$io_pdf->ezNewPage(); // Insertar una nueva página
					}
				}
			}
		}
		
	
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($lb_valido) // Si no ocurrio ningún error
		{
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);				
?> 