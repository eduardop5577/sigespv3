<?php
/***********************************************************************************
* @fecha de modificacion: 15/08/2022, para la version de php 8.1 
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
		global $io_fun_sep;
		$ls_descripcion="Generó el Reporte ".$as_titulo;
		$lb_valido=$io_fun_sep->uf_load_seguridad_reporte("SEP","sigesp_sep_r_solicitudes.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
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
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(50,40,555,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],50,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título
		$io_pdf->addText(500,750,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(506,743,7,date("h:i a")); // Agregar la Hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezado_pagina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el detalle por concepto
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 13/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetDy(-2);
		$la_columnas=array('codigo'=>'<b>Solicitud</b>',
						   'nombre'=>'<b>Proveedor / Beneficiario</b>',
						   'status'=>'<b>Estatus</b>',
						   'documento'=>'<b>Documento</b>',
						   'origen'=>'<b>Origen</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>550, // Ancho de la tabla
						 'maxWidth'=>550, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>170), // Justificación y ancho de la columna
						 			   'status'=>array('justification'=>'left','width'=>90), // Justificación y ancho de la columna
						 			   'documento'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
						 			   'origen'=>array('justification'=>'left','width'=>120))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_sep.php");
	$io_fun_sep=new class_funciones_sep();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>UBICACION DE SOLICITUDES DE EJECUCIÓN PRESUPUESTARIA</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_numsoldes=$io_fun_sep->uf_obtenervalor_get("numsoldes","");
	$ls_numsolhas=$io_fun_sep->uf_obtenervalor_get("numsolhas","");
	$ls_tipproben=$io_fun_sep->uf_obtenervalor_get("tipproben","");
	$ls_codprobendes=$io_fun_sep->uf_obtenervalor_get("codprobendes","");
	$ls_codprobenhas=$io_fun_sep->uf_obtenervalor_get("codprobenhas","");
	$li_registrada=$io_fun_sep->uf_obtenervalor_get("registrada",0);
	$li_emitida=$io_fun_sep->uf_obtenervalor_get("emitida",0);
	$li_contabilizada=$io_fun_sep->uf_obtenervalor_get("contabilizada",0);
	$li_procesada=$io_fun_sep->uf_obtenervalor_get("procesada",0);
	$li_anulada=$io_fun_sep->uf_obtenervalor_get("anulada",0);
	$li_despachada=$io_fun_sep->uf_obtenervalor_get("despachada",0);
	$li_aprobada=$io_fun_sep->uf_obtenervalor_get("aprobada",0);
	$li_pagada=$io_fun_sep->uf_obtenervalor_get("pagada",0);
	$ls_codunides=$io_fun_sep->uf_obtenervalor_get("codunides","");
	$ls_codunihas=$io_fun_sep->uf_obtenervalor_get("codunihas","");
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("sigesp_sep_class_report.php");
	$io_report=new sigesp_sep_class_report();
	$lb_valido=uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if($lb_valido)
	{
		$arrResultado=$io_report->uf_select_ubicacionsolicitudes($ls_numsoldes,$ls_numsolhas,$ls_tipproben,$ls_codprobendes,
															  $ls_codprobenhas,$li_registrada,$li_emitida,$li_contabilizada,
															  $li_procesada,$li_anulada,$li_despachada,$li_aprobada,
															  $li_pagada,$ls_codunides,$ls_codunihas,$lb_valido); // Cargar el DS con los datos del reporte
		$rs_data = $arrResultado['rs_data'];
		$lb_valido = $arrResultado['lb_valido'];
		if($lb_valido==false) // Existe algún error ó no hay registros
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");
		}
		else  // Imprimimos el reporte
		{
			
			set_time_limit(1800);
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(3.6,2.5,3,3); // Configuración de los margenes en centímetros
			uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
			$li_totrow=$io_report->io_sql->num_rows($rs_data);
			$li_s=0;
			if($li_totrow>0)
			{			
				while((!$rs_data->EOF))
				{
					$li_ok=0;
					$ls_numsol=$rs_data->fields["numsol"]; 
					$ls_estsol=$rs_data->fields["estsol"];
					$ls_estope=$rs_data->fields["estope"];
					$ls_modsep=$rs_data->fields["modsep"];
					$ls_codpro=$rs_data->fields["cod_pro"];
					$ls_cedbene=$rs_data->fields["ced_bene"];
					$ls_nombre=$rs_data->fields["nombre"];
					$ls_estapro=$rs_data->fields["estapro"];
					switch ($ls_estsol)
					{
						case "R":
							$ls_estsol="Registro";					
							break;
						case "E":
							if ($ls_estapro==0)
							{
							  $ls_estsol="Emitida";					
							}
							else
							{
							  $ls_estsol="Aprobada";
							}					
							break;
						case "C":
							$ls_estsol="Contabilizada";					
							break;
						case "A":
							$ls_estsol="Anulada";					
							break;
						case "P":
							$ls_estsol="Procesada";					
							break;
						case "D":
							$ls_estsol="Despachada";
							break;
					}
					if($ls_estope=="O")
					{
						$arrResultado=$io_report->uf_load_sep_ubicacioncompromiso($ls_numsol,$ls_codpro,$ls_cedbene,"SEPSPC",$lb_valido);
						$rs_datacomp = $arrResultado['rs_data'];
						$lb_valido = $arrResultado['lb_valido'];						
						$li_rowdet=$io_report->io_sql->num_rows($rs_datacomp);
						if($li_rowdet>0)
						{
							while((!$rs_datacomp->EOF))
							{	
								$ls_documento  = $rs_datacomp->fields["documento"];
								$ls_estatus  = $rs_datacomp->fields["estatus"];
								$ls_origen  = $rs_datacomp->fields["origen"];
								if($ls_origen=="RD")
								{						
									$ls_origen="Recepcion de Documentos";
									switch($ls_estatus)
									{
										case "R": 
											$ls_estatus="Recibida";
											break;
										case "E": 
											$ls_estatus="Emitida";
											break;
										case "C": 
											$ls_estatus="Contabilizada";
											break;
										case "A": 
											$ls_estatus="Anulada";
											break;
									}
								}
								$li_s=$li_s+1;
								$li_ok=1;
								$la_data[$li_s]= array('codigo'=>$ls_numsol,'nombre'=>$ls_nombre,'status'=>$ls_estsol,
												       'documento'=>$ls_documento,'origen'=>$ls_origen);
								$rs_datacomp->MoveNext();
							}
						}
					}
					else
					{
						$arrResultado=$io_report->uf_load_sep_ubicacionprecompromiso($ls_numsol,$lb_valido);
						$rs_datadet = $arrResultado['rs_data'];
						$lb_valido = $arrResultado['lb_valido'];						
						$li_rowdet=$io_report->io_sql->num_rows($rs_datadet);
						if($li_rowdet>0)
						{
							while((!$rs_datadet->EOF))
							{	
								$ls_documento  = $rs_datadet->fields["numdocdes"];
								$ls_estatus  ="EN ESPERA";// $rs_data1->fields["estatus"];
								$ls_origen  = $rs_datadet->fields["estincite"];
								if($ls_origen=="SC")
								{						
									$ls_origen="Solicitud de Cotizacion";
									switch($ls_estatus)
									{
										case "R": 
											$ls_estatus="Recibida";
											break;
										case "E": 
											$ls_estatus="Emitida";
											break;
										case "C": 
											$ls_estatus="Contabilizada";
											break;
										case "A": 
											$ls_estatus="Anulada";
											break;
									}
								}
								if($ls_origen=="OC")
								{
									$ls_origen="Orden de Compra";
								}
								$li_s=$li_s+1;
								$li_ok=1;
								$la_data[$li_s]= array('codigo'=>$ls_numsol,'nombre'=>$ls_nombre,'status'=>$ls_estsol,
												 	   'documento'=>$ls_documento,'origen'=>$ls_origen);
								$rs_datadet->MoveNext();
							}
						}
					}
					if($li_ok==0)
					{
						$li_s=$li_s+1;
						$li_ok=0;
						$la_data[$li_s]= array('codigo'=>$ls_numsol,'nombre'=>$ls_nombre,'status'=>$ls_estsol,
											   'documento'=>"",'origen'=>"");
					}
					$rs_data->MoveNext();
				}
				$io_pdf = uf_print_detalle($la_data,$io_pdf);
				unset($la_data);
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
			else // Si hubo algún error
			{
				print("<script language=JavaScript>");
				print(" alert('No hay nada que Reportar');"); 
				print(" close();");
				print("</script>");		
			}
		}
	}
?>