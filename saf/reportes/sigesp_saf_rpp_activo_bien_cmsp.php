<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
	function uf_print_encabezado_pagina($as_codemp,$as_nomemp,$as_depen,$as_distrito,$as_direccion,$as_servicio,$as_titulo,$as_cmpmov,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_cmpmov // numero de comprobante de movimiento
		//	    		   ad_fecha // Fecha 
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		//$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->line(50,40,950,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,550,11,"<b>".$as_titulo."</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(11,$ad_fecha);
		$tm=504-($li_tm/2);
		$io_pdf->addText(750,535,11,""); // Agregar la fecha
		$io_pdf->addText($tm,535,11,$ad_fecha); // Agregar la fecha
		$io_pdf->addText(750,555,11,""); // Agregar la fecha
		$io_pdf->addText(800,555,11,""); // Agregar la fecha
		//$io_pdf->addText(928,570,8,date("d/m/Y")); // Agregar la Fecha
		//$io_pdf->addText(934,563,7,date("h:i a")); // Agregar la Hora
		$io_pdf->ezSetY(525);
		$la_data=array(array('name'=>'<b>ENTIDAD PROPIETARIA:</b>  '.'MUNICIPIO SIMON PLANAS'.'         '.'<b>SERVICIO:</b>  '.$as_nomemp),
					   array ('name'=>'<b>UNIDAD DE TRABAJO O DEPENDENCIA:</b>  '.$as_depen.''),
					   array ('name'=>'<b>ESTADO:</b>  '.$as_distrito.'         '.'<b>MUNICIPIO:</b>  '.'SIMON PLANAS'),
					   array ('name'=>'<b>DIRECCIÓN O LUGAR:</b>  '.$as_direccion.'          '.'<b>FECHA:</b>  '.date("d/m/Y")));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 //'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		/*$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');*/
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_codemp   // codigo de empresa
		//	    		   as_nomemp   // nombre de empresa
		//	    		   as_codact   // codigo de activo
		//	    		   as_denact   // denominacion de activo
		//	    		   as_maract   // marca del activo
		//	    		   as_modact   // modelo del activo
		//	    		   ad_fecmpact // fecha de compra del activo
		//	    		   ai_costo    // costo del activo
		//	    		   io_pdf      // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_tipoformato;
		global $io_pdf;
		if($ls_tipoformato==0)
		{
		  $ls_titulo="Costo Bs.:";
		}
		elseif($ls_tipoformato==1)
		{
		  $ls_titulo="Costo Bs.F.:";
		}
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$posicion,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-$posicion);
		$la_columna=array('codgru'=>'<b>Grupo</b>',
		                  'codsubgru'=>'<b>Subgrupo</b>',
						  'codsec'=>'<b>Sección</b>',
						  'cantidad'=>'<b>Cantidad</b>',
						  'ideact'=>'<b>Identificación</b>',
		                  'denact'=>"<b>Nombre y Descripción de los Bienes, Referencia de los Comprobantes y de los Precios Unitarios</b>",					  
						  'costo1'=>'<b>Valor Unitario</b>',
						  'costo'=>'<b>Valor Total</b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('codgru'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna	
						               'codsubgru'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna	
									   'codsec'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna	
									   'cantidad'=>array('justification'=>'center','width'=>50), // Justificación y ancho de la columna	
									   'ideact'=>array('justification'=>'center','width'=>80), // Justificación y ancho de la columna	
									   'denact'=>array('justification'=>'left','width'=>470), // Justificación y ancho de la columna						 			  
						 			   'costo1'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
									   'costo'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
									   
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_cabecera($ai_montot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_cabecera
		//		   Access: private 
		//	    Arguments: ai_montot // Total movimiento
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime el fin de la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-20);
		$la_data=array(array('total'=>'<b>TOTAL GENERAL:</b>                               '.$ai_montot));
		$la_columna=array('total'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 10, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>480, // Ancho Máximo de la tabla
						 'xPos'=>690,  //Orientación de la tabla
						 'xOrientation'=>'right'); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}// end function uf_print_pie_cabecera
	
	function uf_print_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_firmas
		//		   Access: private 
		//	    Arguments: io_pdf // Instancia de objeto pdf
		//    Description: función que imprime las firmas
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 06/12/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->ezSetDy(-90);
		$la_data[1]=array('name1'=>'       ___________________________________');
		$la_data[2]=array('name1'=>'<b>                      ABG. LEONEL LUCENA</b>');
		$la_data[3]=array('name1'=>'<b>                      AUDITOR INETRNO (E)</b>');
		$la_columna=array('name1'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>930, // Ancho Mínimo de la tabla
						 'xPos'=>550, // Orientación de la tabla
						 'cols'=>array('name1'=>array('justification'=>'left','width'=>300))); 
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_firmas		//--------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");
	$ld_fecha="";
	$ls_titulo="<b>INVENTARIO DE BIENES MUEBLES</b>";
	if(($ld_desde!="")&&($ld_hasta!=""))
	{
		$ld_fecha="Desde:".$ld_desde."  Hasta:".$ld_hasta."";
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$ls_distrito=$arre["estemp"];
	$ls_direccion=$arre["direccion"];
	$ls_coddesde=$_GET["coddesde"];
	$ls_codhasta=$_GET["codhasta"];
	$ls_ordenact=$_GET["ordenact"];
	$ls_status=$_GET["status"];
	$ls_coduniadm=$_GET["coduni"]; 
	$ls_grupo=$_GET["grupo"];
	$ls_subgrupo=$_GET["subgrupo"];
	$ls_seccion=$_GET["seccion"];
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	global $ls_tipoformato;
	if($ls_tipoformato==1)
	{
		require_once("sigesp_saf_class_reportbsf.php");
		$io_report=new sigesp_saf_class_reportbsf();
	}
	else
	{
		require_once("sigesp_saf_class_report.php");
		$io_report=new sigesp_saf_class_report();
	}	
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data=$io_report->uf_select_inventario_unidad($ls_coduniadm,$ld_desde,$ld_hasta,$ls_status,$ls_ordenact,$ls_coddesde,
													   $ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,
													   $ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
	if($rs_data==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
	    $lb_valido=true;
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Activo. Desde el activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_activo_bien.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
		
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.8,2,3,3); // Configuración de los margenes en centímetros
		$io_pdf->ezStartPageNumbers(940,50,10,'','',1); // Insertar el número de página
			$li_numpag=$io_pdf->ezPageCount; // Número de página	
			$ls_coduniadm="";
				$ls_coduniadm_com= "";
				$la_data="";
				$li_montot=0;
				$li_acum=0;
				$i=1;
				$li_s=1;
				while(!$rs_data->EOF)
				{
					$li_acum++;
					$ls_coduniadm=    $rs_data->fields["coduniadm"];
					$ls_denuniadm=    $rs_data->fields["denuniadm"];
					if($ls_coduniadm_com!=$ls_coduniadm || $li_acum==18)
					{
						if($li_s>1)
						{
							uf_print_detalle($la_data,45,$io_pdf);
							$io_pdf->ezNewPage();
							$la_data="";
							$li_s=1;
						}
						// cuadro de encabezado de la tabla
						uf_print_encabezado_pagina($ls_codemp,$ls_nomemp,$ls_denuniadm,$ls_distrito,$ls_direccion,"",$ls_titulo,"",$ld_fecha,$io_pdf); // Imprimimos el encabezado de la página	
						$io_pdf->Rectangle(49,425,900.5,40); 
						$io_pdf->line(49,450,189,450);	//HORIZONTAL	
						$io_pdf->addText(100,455,7,"<b>CÓDIGO</b>"); // Agregar el título
						$io_pdf->addText(60,435,7,"<b>Grupo</b>"); // Agregar el título
						$io_pdf->addText(97,435,7,"<b>SubGrupo</b>"); // Agregar el título
						$io_pdf->addText(147,435,7,"<b>Sección</b>"); // Agregar el título
						$io_pdf->addText(200,435,7,"<b>Cantidad</b>"); // Agregar el título
						$io_pdf->addText(257,450,7,"<b>Número de</b>"); // Agregar el título
						$io_pdf->addText(254,435,7,"<b>Identificación</b>"); // Agregar el título
						$io_pdf->addText(460,450,7,"<b>NOMBRE Y DESCRIPCIÓN DE LOS BIENES, REFERENCIA DE</b>"); // Agregar el título
						$io_pdf->addText(473,435,7,"<b>LOS COMPROBANTES Y DE LOS PRECIOS UNITARIOS</b>"); // Agregar el título
						$io_pdf->addText(820,450,7,"<b>Valor</b>"); // Agregar el título
						$io_pdf->addText(810,435,7,"<b>Unitario Bs.</b>"); // Agregar el título
						$io_pdf->addText(900,450,7,"<b>Valor</b>"); // Agregar el título
						$io_pdf->addText(895,435,7,"<b>Total Bs.</b>"); // Agregar el título
						$io_pdf->line(189,425,189,465);	//VERTICAL	
						$io_pdf->line(239,425,239,465);	//VERTICAL	
						$io_pdf->line(319,425,319,465);	//VERTICAL	
						$io_pdf->line(789,425,789,465);	//VERTICAL	
						$io_pdf->line(869,425,869,465);	//VERTICAL	
						$io_pdf->line(89,425,89,450);	//VERTICAL	
						$io_pdf->line(139,425,139,450);	//VERTICAL
						$ls_coduniadm_com= $ls_coduniadm;
						$li_acum=0;
						unset($la_data);
					}
					$ls_codact=    $rs_data->fields["codact"];
					$ls_codgru=    $rs_data->fields["codgru"];
					$ls_codsubgru= $rs_data->fields["codsubgru"];
					$ls_codsec=    $rs_data->fields["codsec"];
					$ls_seract=    $rs_data->fields["seract"];
					$ls_denact=    $rs_data->fields["denact"];
					$ls_maract=    $rs_data->fields["maract"];
					$ls_modact=    $rs_data->fields["modact"];					
					$ls_denuniadm= $rs_data->fields["denuniadm"];									
					$ls_estact=    $rs_data->fields["estact"];					
					$li_costo=     $rs_data->fields["costo"];
					$li_montot=$li_montot+$li_costo;
					$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
					$ls_ideact=	    $rs_data->fields["ideact"];
					$ls_cantidad=	$rs_data->fields["cantidad"];	
					$ls_servicio=	$rs_data->fields["denuniadm"];						
					if($ls_estact=="R"){$ls_estact="Reasignado";}
					if($ls_estact=="I"){$ls_estact="Incorporado";}					
					$la_data[$li_s]=array('codact'=>$ls_codact,'codgru'=>$ls_codgru,'codsubgru'=>$ls_codsubgru,'codsec'=>$ls_codsec,'denact'=>$ls_denact,'seract'=>$ls_seract,'denuniadm'=>$ls_denuniadm,'estact'=>$ls_estact,'maract'=>$ls_maract,'modact'=>$ls_modact,'costo1'=>$li_costo,'costo'=>$li_costo,'ideact'=>$ls_ideact,'cantidad'=>$ls_cantidad,'servicio'=>$ls_servicio);
					$li_s++;
					$rs_data->MoveNext();
				}
				$li_montot=$io_fun_activos->uf_formatonumerico($li_montot);
				$la_data[$li_s+1]=array('codact'=>'','codgru'=>'','codsubgru'=>'','codsec'=>'','denact'=>'<b>                                                                                                                                                                                        TOTAL BS.</b>','seract'=>'','denuniadm'=>'','estact'=>'','maract'=>'','modact'=>'','costo1'=>'','costo'=>$li_montot,'ideact'=>'','cantidad'=>'','servicio'=>'');
				$io_pdf->Rectangle(49,425,900.5,40); 
				$io_pdf->line(49,450,189,450);	//HORIZONTAL	
				$io_pdf->addText(100,455,7,"<b>CÓDIGO</b>"); // Agregar el título
				$io_pdf->addText(60,435,7,"<b>Grupo</b>"); // Agregar el título
				$io_pdf->addText(97,435,7,"<b>SubGrupo</b>"); // Agregar el título
				$io_pdf->addText(147,435,7,"<b>Sección</b>"); // Agregar el título
				$io_pdf->addText(200,435,7,"<b>Cantidad</b>"); // Agregar el título
				$io_pdf->addText(257,450,7,"<b>Número de</b>"); // Agregar el título
				$io_pdf->addText(254,435,7,"<b>Identificación</b>"); // Agregar el título
				$io_pdf->addText(460,450,7,"<b>NOMBRE Y DESCRIPCIÓN DE LOS BIENES, REFERENCIA DE</b>"); // Agregar el título
				$io_pdf->addText(473,435,7,"<b>LOS COMPROBANTES Y DE LOS PRECIOS UNITARIOS</b>"); // Agregar el título
				$io_pdf->addText(820,450,7,"<b>Valor</b>"); // Agregar el título
				$io_pdf->addText(810,435,7,"<b>Unitario Bs.</b>"); // Agregar el título
				$io_pdf->addText(900,450,7,"<b>Valor</b>"); // Agregar el título
				$io_pdf->addText(895,435,7,"<b>Total Bs.</b>"); // Agregar el título
				$io_pdf->line(189,425,189,465);	//VERTICAL	
				$io_pdf->line(239,425,239,465);	//VERTICAL	
				$io_pdf->line(319,425,319,465);	//VERTICAL	
				$io_pdf->line(789,425,789,465);	//VERTICAL	
				$io_pdf->line(869,425,869,465);	//VERTICAL	
				$io_pdf->line(89,425,89,450);	//VERTICAL	
				$io_pdf->line(139,425,139,450);	//VERTICAL
				uf_print_detalle($la_data,45,$io_pdf);
			   // uf_print_firmas($io_pdf);				
				unset($la_data);			
		if(($lb_valido)&&($i>0))
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		else
		{
		   	print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar, entro por aqui tambien');"); 
		//	print(" close();");
			print("</script>");
		}		
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
?> 