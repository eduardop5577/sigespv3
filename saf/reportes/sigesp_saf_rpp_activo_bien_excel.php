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

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "BM2.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	$io_fun_activos=new class_funciones_activos();
	require_once("sigesp_saf_class_report.php");
	$io_report=new sigesp_saf_class_report();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_desde=$io_fun_activos->uf_obtenervalor_get("desde","");
	$ld_hasta=$io_fun_activos->uf_obtenervalor_get("hasta","");
	$ld_fecha="";
	$ls_titulo="INVENTARIO DE BIENES MUEBLES";
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
	$ls_denuni=$io_fun_activos->uf_obtenervalor_get("denuni","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data=$io_report->uf_select_inventario_unidad($ls_coduniadm,$ld_desde,$ld_hasta,$ls_status,$ls_ordenact,$ls_coddesde,
													   $ls_codhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$ls_grupohas,$ls_subgrupohas,$ls_seccionhas,
													   $ls_unitri,$ls_codconbie,$ls_codrespri,$ls_codresuso,$ls_coduniadm2,$ls_codsed); // Cargar el DS con los datos de la cabecera del reporte
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
		//-------formato para el reporte----------------------------------------------------------
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');		
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_dataleft= &$lo_libro->addformat();
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0.00'));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		
		$lo_dataright2= &$lo_libro->addformat(array('num_format' => '#,##'));
		$lo_dataright2->set_font("Verdana");
		$lo_dataright2->set_align('right');
		$lo_dataright2->set_size('9');	
		$lo_hoja->set_column(0,0,25);
		$lo_hoja->set_column(1,1,50);	
		$lo_hoja->set_column(2,2,20);	
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,25);
		$lo_hoja->set_column(5,5,25);
		$lo_hoja->set_column(6,6,100);	
		$lo_hoja->set_column(7,7,20);	
		$lo_hoja->set_column(8,8,20);	
		$lo_hoja->set_column(9,9,20);	
		$lo_hoja->set_column(10,10,20);	
		$lo_hoja->set_column(11,11,20);	
		$lo_hoja->set_column(12,12,20);	
		$lo_hoja->set_column(13,13,20);	
		$lo_hoja->set_column(14,14,80);	
		$lo_hoja->set_column(15,15,80);	
		$lo_hoja->set_column(16,16,80);	
		$lo_hoja->set_column(17,17,20);	
		$lo_hoja->set_column(18,18,20);	
		$lo_hoja->set_column(19,19,20);	
		$lo_hoja->set_column(20,20,40);	
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		$lo_hoja->write(1,3,$ld_fecha,$lo_encabezado);
//		uf_print_encabezado_pagina($ls_titulo,"",$ld_fecha,$io_pdf); // Imprimimos el encabezado de la página	
		$li_row=2;					
		
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
		$li_row++;
		$lo_hoja->write($li_row,0,"X :",$lo_titulo);
		$lo_hoja->write($li_row,1,"Bienes Muebles",$lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row,0,"Organismo:",$lo_titulo);
		$lo_hoja->write($li_row,1,$ls_nomemp,$lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row,0,"Unidad Administrativa:",$lo_titulo);
		$lo_hoja->write($li_row,1,$ls_denuni,$lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row,0,"Direccion:",$lo_titulo);
		$lo_hoja->write($li_row,1,$ls_direccion,$lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row,0,"Responsable Administrativo:",$lo_titulo);
		$lo_hoja->write($li_row,1,$ls_nombrepri,$lo_dataleft);
		$lo_hoja->write($li_row,2,"Cedula",$lo_dataleft);
		$lo_hoja->write($li_row,3,$ls_codrespri,$lo_dataleft);
		$lo_hoja->write($li_row,4,"Cargo",$lo_dataleft);
		$lo_hoja->write($li_row,5,$ls_cargopri,$lo_dataleft);
		$li_row++;
		$lo_hoja->write($li_row,0,"Responsable Administrativo:",$lo_titulo);
		$lo_hoja->write($li_row,1,$ls_nombreuso,$lo_dataleft);
		$lo_hoja->write($li_row,2,"Cedula",$lo_dataleft);
		$lo_hoja->write($li_row,3,$ls_codresuso,$lo_dataleft);
		$lo_hoja->write($li_row,4,"Cargo",$lo_dataleft);
		$lo_hoja->write($li_row,5,$ls_cargouso,$lo_dataleft);
		$li_row++;
		if($lb_valido)
		{			
			$li_totrow_det=$io_report->ds->getRowCount("codact");
			$la_data="";
			$i=0;
			$acum_total=0;
			$li_row++;
			$lo_hoja->write($li_row,0,"Codigo",$lo_titulo);
			$lo_hoja->write($li_row,1,"Grupo",$lo_titulo);
			$lo_hoja->write($li_row,2,"Subgrupo",$lo_titulo);
			$lo_hoja->write($li_row,3,"Sección",$lo_titulo);
			$lo_hoja->write($li_row,4,"Clasificacion del Bien",$lo_titulo);
			$lo_hoja->write($li_row,5,"Chapa",$lo_titulo);
			$lo_hoja->write($li_row,6,"Denominacion",$lo_titulo);
			$lo_hoja->write($li_row,7,"Marca",$lo_titulo);
			$lo_hoja->write($li_row,8,"Modelo",$lo_titulo);
			$lo_hoja->write($li_row,9,"Serial",$lo_titulo);
			$lo_hoja->write($li_row,10,"Color",$lo_titulo);
			$lo_hoja->write($li_row,11,"Condicion del Bien",$lo_titulo);
			$lo_hoja->write($li_row,12,"Estado del Bien",$lo_titulo);
			$lo_hoja->write($li_row,13,"Sede",$lo_titulo);
			$lo_hoja->write($li_row,14,"Unidad",$lo_titulo);
			$lo_hoja->write($li_row,15,"Responsable Primario",$lo_titulo);
			$lo_hoja->write($li_row,16,"Responsable por Uso",$lo_titulo);
			$lo_hoja->write($li_row,17,"Fecha de Compra",$lo_titulo);
			$lo_hoja->write($li_row,18,"Costo",$lo_titulo);
			$lo_hoja->write($li_row,19,"Codigo Presupuestario",$lo_titulo);
			$li_s=0;
			$li_totcosto=0;
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
				$ls_cantidad=	$rs_data->fields["cantidad"];	
				$ls_servicio=	$rs_data->fields["denuniadm"];						
				$ls_idchapa=	$rs_data->fields["idchapa"];	
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
				$li_row++;
				$lo_hoja->write($li_row,0," ".$ls_ideact,$lo_dataleft);
				$lo_hoja->write($li_row,1," ".$ls_codgru,$lo_dataleft);
				$lo_hoja->write($li_row,2," ".$ls_codsubgru,$lo_dataleft);
				$lo_hoja->write($li_row,3," ".$ls_codsec,$lo_dataleft);
				$lo_hoja->write($li_row,4," ".$ls_codact,$lo_dataleft);
				$lo_hoja->write($li_row,5," ".$ls_idchapa,$lo_dataleft);
				$lo_hoja->write($li_row,6,$ls_denact,$lo_dataleft);
				$lo_hoja->write($li_row,7,$ls_maract,$lo_dataleft);
				$lo_hoja->write($li_row,8,$ls_modact,$lo_dataleft);
				$lo_hoja->write($li_row,9," ".$ls_seract,$lo_dataleft);
				$lo_hoja->write($li_row,10,$ls_colact,$lo_dataleft);
				$lo_hoja->write($li_row,11,$ls_denconbie,$lo_dataleft);
				$lo_hoja->write($li_row,12,$ls_estact,$lo_dataleft);
				$lo_hoja->write($li_row,13,$ls_densed,$lo_dataright2);
				$lo_hoja->write($li_row,14,$ls_denuniadm,$lo_dataleft);
				$lo_hoja->write($li_row,15,$ls_nomrespri,$lo_dataleft);
				$lo_hoja->write($li_row,16,$ls_nomresuso,$lo_dataleft);
				$lo_hoja->write($li_row,17,$ls_feccmpact,$lo_dataleft);
				$lo_hoja->write($li_row,18,$li_costo,$lo_dataright2);
				$lo_hoja->write($li_row,19,$ls_spg,$lo_dataleft);
				$rs_data->MoveNext();
			}
			$li_row++;
			$lo_hoja->write($li_row,2,"Cantidad",$lo_titulo);
			$lo_hoja->write($li_row,3,$li_s,$lo_dataleft);
			$lo_hoja->write($li_row,6,"Total Bs.",$lo_titulo);
			$acum_total=$io_fun_activos->uf_formatonumerico($acum_total);
			$lo_hoja->write($li_row,7,$acum_total,$lo_dataleft);
			if($la_data!="")
			{
				$i=$i +1;
//					uf_print_cabecera($ls_codemp,$ls_nomemp,$ls_denuniadm,$ls_distrito,$ls_direccion,$ls_servicio,$io_pdf); // Imprimimos la cabecera del registro
//					uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
			}
//			 uf_print_firmas($io_pdf);				
		unset($la_data);			
	}
		if($lb_valido)
		{
			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"BM1.xls\"");
			header("Content-Disposition: inline; filename=\"BM1.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);		
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($io_pdf);
		}
		else
		{
		   	print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar, entro por aqui tambien');"); 
			print(" close();");
			print("</script>");
		}		
	}
	unset($io_report);
	unset($io_funciones);
?> 