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
	function uf_print_encabezado_pagina($as_codemp,$as_nomemp,$as_depen,$as_distrito,$as_direccion,$as_servicio,$as_titulo,$as_cmpmov,
										$ad_fecha,$ls_codrespri,$ls_nombrepri,$ls_cargopri,$ls_codresuso,$ls_nombreuso,$ls_cargouso,$io_pdf)
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
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->line(50,40,950,40);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],22,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(9,"INVENTARIO DE BIENES NACIONALES (NORMAS Y CONVENCIONES GENERALES. CAPITULO I, PARRAFO 13. PUBLICACION 9 ACTUALIZADA CGR)");
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,560,9,"<b>INVENTARIO DE BIENES NACIONALES (NORMAS Y CONVENCIONES GENERALES. CAPITULO I, PARRAFO 13. PUBLICACION 9 ACTUALIZADA CGR)</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,"COMPROBANTE DE INCORPORACION (CI): ASIGNACION DE No. DE IDENTIFICACION DE BIENES NACIONALES");
		$tm=504-($li_tm/2);
		$io_pdf->addText($tm,550,9,"<b>COMPROBANTE DE INCORPORACION (CI): ASIGNACION DE No. DE IDENTIFICACION DE BIENES NACIONALES</b>"); // Agregar el título
		$li_tm=$io_pdf->getTextWidth(9,$ad_fecha);
		$tm=504-($li_tm/2);
		$io_pdf->addText(750,535,11,""); // Agregar la fecha
		$io_pdf->addText($tm,535,11,$ad_fecha); // Agregar la fecha
		$io_pdf->addText(750,555,11,""); // Agregar la fecha
		$io_pdf->addText(800,555,11,""); // Agregar la fecha
		$io_pdf->ezSetY(525);


		$la_data=array(array('name'=>'<b>X  Bienes Muebles</b>  '),
		               array('name'=>'<b>Organismo:</b>  '.$as_nomemp.''),
					   array ('name'=>'<b>Unidad Administrativa:</b>  '.$as_depen.''),
					   array ('name'=>'<b>Dirección:</b>  '.$as_direccion.''),
					   array ('name'=>'<b>Responsable Administrativo:</b>  '.$ls_nombrepri.'<b>     Cedula:</b>  '.$ls_codrespri.'<b>     Cargo:</b>  '.$ls_cargopri),
					   array ('name'=>'<b>Responsable Administrativo:</b>  '.$ls_nombreuso.'<b>     Cedula:</b>  '.$ls_codresuso.'<b>     Cargo:</b>  '.$ls_cargouso));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>965, // Ancho de la tabla
						 'maxWidth'=>900); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
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
		global $io_pdf;
		global $ls_tipoformato;
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
	function uf_print_detalle($la_data,$io_pdf)
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
		$io_pdf->ezSetDy(-10);
		$la_columna=array('ideact'=>'Codigo','codgru'=>'Grupo','codsubgru'=>'Subgrupo','codsec'=>'Sección','codact'=>'Clasificacion del Bien','idchapa'=>'Chapa',
							  'denact'=>'Denominacion','maract'=>'Marca','modact'=>'Modelo','seract'=>'Serial','colact'=>'Color','denconbie'=>'Condicion del Bien','estact'=>'Estado del Bien',
							  'densed'=>'Sede','denuniadm'=>'Unidad','nomrespri'=>'Responsable Primario','nomresuso'=>'Responsable por Uso','feccmpact'=>'Fecha de Compra','costo'=>'Costo','spg'=>'Codigo Presupuestario');
		
		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>900, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('ideact'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna
						 			   'codgru'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna	
						               'codsubgru'=>array('justification'=>'center','width'=>37), // Justificación y ancho de la columna	
									   'codsec'=>array('justification'=>'center','width'=>35), // Justificación y ancho de la columna	
									   'codact'=>array('justification'=>'center','width'=>45), // Justificación y ancho de la columna	
									   'idchapa'=>array('justification'=>'center','width'=>40), // Justificación y ancho de la columna	
									   'denact'=>array('justification'=>'left','width'=>150), // Justificación y ancho de la columna						 			  
						 			   'maract'=>array('justification'=>'left','width'=>40), // Justificación y ancho de la columna
						 			   'modact'=>array('justification'=>'left','width'=>40), // Justificación y ancho de la columna
						 			   'seract'=>array('justification'=>'left','width'=>40), // Justificación y ancho de la columna
						 			   'colact'=>array('justification'=>'left','width'=>40), // Justificación y ancho de la columna
						 			   'denconbie'=>array('justification'=>'left','width'=>40), // Justificación y ancho de la columna
						 			   'estact'=>array('justification'=>'left','width'=>43), // Justificación y ancho de la columna
						 			   'densed'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
						 			   'denuniadm'=>array('justification'=>'left','width'=>60), // Justificación y ancho de la columna
						 			   'nomrespri'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
						 			   'nomresuso'=>array('justification'=>'left','width'=>50), // Justificación y ancho de la columna
						 			   'feccmpact'=>array('justification'=>'left','width'=>45), // Justificación y ancho de la columna
						 			   'costo'=>array('justification'=>'right','width'=>40), // Justificación y ancho de la columna
									   'spg'=>array('justification'=>'left','width'=>45))); // Justificación y ancho de la columna
									   
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
		$la_data=array(array('name'=>'                                                                                                                                                                                                                                                                                                                                                                                            <b>TOTAL: </b>  '.$ai_montot));
		$la_columna=array('name'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'lineCol'=>array(0.9,0.9,0.9), // Mostrar Líneas
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0	, // Sombra entre líneas
						 'shadeCol'=>array(0.9,0.9,0.9), // Color de la sombra
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>965, // Ancho de la tabla
						 'maxWidth'=>900); // Ancho Máximo de la tabla
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
		$io_pdf->ezSetDy(-170);
		$la_data[1]=array('name1'=>'JEFE DE LA UNIDAD DE TRABAJO','name2'=>'REPONSABLE DE BIENES DE LA UNIDAD DE TRABAJO','name3'=>'UNIDAD DE TRABAJO');
		$la_data[2]=array('name1'=>'','name2'=>'','name3'=>'');
		$la_data[3]=array('name1'=>'Nombre y Apellido:___________________________________','name2'=>'Nombre y Apellido:___________________________________','name3'=>'Nombre y Apellido:___________________________________');
		$la_data[4]=array('name1'=>'Firma:                    ___________________________________','name2'=>'Firma:                    ___________________________________','name3'=>'Firma:                    ___________________________________');
		$la_data[5]=array('name1'=>'C.I:                         ___________________________________','name2'=>'C.I:                         ___________________________________','name3'=>'C.I:                         ___________________________________');
		$la_columna=array('name1'=>'',
						  'name2'=>'',
						  'name3'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>900, // Ancho de la tabla
						 'maxWidth'=>930, // Ancho Mínimo de la tabla
						 'xPos'=>500, // Orientación de la tabla
						 'cols'=>array('name1'=>array('justification'=>'left','width'=>300), // Justificacion y ancho de la columna
  						 			   'name2'=>array('justification'=>'left','width'=>300),
									   'name3'=>array('justification'=>'left','width'=>300))); 
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
	$ls_codconbie=$io_fun_activos->uf_obtenervalor_get("codconbie","");
	$ls_codrespri=$io_fun_activos->uf_obtenervalor_get("codrespri","");
	$ls_codresuso=$io_fun_activos->uf_obtenervalor_get("codresuso","");
	$ls_coduniadm2=$io_fun_activos->uf_obtenervalor_get("coduniadm","");
	$ls_codsed=$io_fun_activos->uf_obtenervalor_get("codsed","");
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
													   $ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$ls_grupohas,$ls_subgrupohas,$ls_seccionhas,
													   $ls_unitri,$ls_codconbie,$ls_codrespri,$ls_codresuso,$ls_coduniadm2,$ls_codsed); // Cargar el DS con los datos de la cabecera del reporte
	if($rs_data	==false) // Existe algún error ó no hay registros
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
		$io_pdf->ezSetCmMargins(5.8,3,3,3); // Configuración de los margenes en centímetros
		$io_pdf->ezStartPageNumbers(940,50,10,'','',1); // Insertar el número de página
			$li_numpag=$io_pdf->ezPageCount; // Número de página	
					
			$li_totrow_det=$io_report->ds->getRowCount("codact");
			$la_data="";
			$i=0;
			$li_s=0;
			$acum_total=0;
			while(!$rs_data->EOF)
			{  				
				$li_s++;
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
				$acum_total=$acum_total+$li_costo;
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$ls_ideact=	    $rs_data->fields["ideact"];
				$ls_idchapa=	$rs_data->fields["idchapa"];	
				$ls_servicio=	$rs_data->fields["denuniadm"];						
				$ls_cantidad=	$rs_data->fields["cantidad"];						
				$ls_colact=	$rs_data->fields["colact"];						
				$ls_denconbie=	$rs_data->fields["denconbie"];						
				$ls_densed=	$rs_data->fields["densed"];						
				$ls_nomrespri=	$rs_data->fields["nomrespri1"];	
				if($ls_nomrespri=="")
					$ls_nomrespri=	$rs_data->fields["nomrespri2"];	
				$ls_nomresuso=	$rs_data->fields["nomresuso1"];	
				if($ls_nomresuso=="")
					$ls_nomresuso=	$rs_data->fields["nomresuso2"];	
				$ls_feccmpact=	$io_funciones->uf_convertirfecmostrar($rs_data->fields["feccmpact"]);						
				$ls_spg=	$rs_data->fields["spg_cuenta_act"];						
				if($ls_estact=="R"){$ls_estact="Reasignado";}
				if($ls_estact=="I"){$ls_estact="Incorporado";}					
				$la_data[$li_s]=array('ideact'=>$ls_ideact,'codgru'=>$ls_codgru,'codsubgru'=>$ls_codsubgru,'codsec'=>$ls_codsec,'codact'=>$ls_codact,'idchapa'=>$ls_idchapa,
				                      'denact'=>$ls_denact,'maract'=>$ls_maract,'modact'=>$ls_modact,'seract'=>$ls_seract,'colact'=>$ls_colact,'denconbie'=>$ls_denconbie,'estact'=>$ls_estact,
									  'densed'=>$ls_densed,'denuniadm'=>$ls_denuniadm,'nomrespri'=>$ls_nomrespri,'nomresuso'=>$ls_nomresuso,'feccmpact'=>$ls_feccmpact,'costo'=>$li_costo,'spg'=>$ls_spg);
				$rs_data->MoveNext();
			}
			if($la_data!="")
			{
				$i=$i +1;
				$ls_nombrepri="";
				$ls_cargopri="";
				$ls_nombreuso="";
				$ls_cargouso="";
				if($ls_codrespri!="")
				{
					$rs_pri=$io_report->uf_select_datospersonal($ls_codrespri);
					if(!$rs_pri->EOF)
					{
						$ls_nombrepri=    $rs_pri->fields["nombre"].", ".$rs_pri->fields["apellido"];
						$ls_cargopri=    $rs_pri->fields["cargo"];
					}
				}
				if($ls_codresuso!="")
				{
					$rs_uso=$io_report->uf_select_datospersonal($ls_codresuso);
					if(!$rs_uso->EOF)
					{
						$ls_nombreuso=    $rs_uso->fields["nombre"].", ".$rs_uso->fields["apellido"];
						$ls_cargouso=    $rs_uso->fields["cargo"];
					}
				}
				$acum_total=$io_fun_activos->uf_formatonumerico($acum_total);
				uf_print_encabezado_pagina($ls_codemp,$ls_nomemp,$ls_denuniadm,$ls_distrito,$ls_direccion,$ls_servicio,$ls_titulo,"",$ld_fecha,
										   $ls_codrespri,$ls_nombrepri,$ls_cargopri,$ls_codresuso,$ls_nombreuso,$ls_cargouso,$io_pdf); // Imprimimos el encabezado de la página	
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
				uf_print_pie_cabecera($acum_total,$io_pdf);
				if ($io_pdf->ezPageCount==$li_numpag)
				{// Hacemos el commit de los registros que se desean imprimir
					uf_print_firmas($io_pdf);
					$io_pdf->transaction('commit');
				}
				else
				{// Hacemos un rollback de los registros y volvemos a imprimir
					$io_pdf->transaction('rewind');
				}
			}
			 uf_print_firmas($io_pdf);				
			unset($la_data);			
		if(($lb_valido)&&($i>0))
		{
			$io_pdf->ezStopPageNumbers(1,1);
			$io_pdf->ezStream();
		}
		else
		{
		   	print "numero de filas ".$li_totrow_det."<br>";
		   	print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar, entro por aqui tambien');"); 
			print(" close();");
			print("</script>");
		}		
		unset($io_pdf);
	}
	unset($io_report);
	unset($io_funciones);
?> 