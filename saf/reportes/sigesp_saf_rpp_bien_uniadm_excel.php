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
	$lo_archivo = tempnam("/tmp", "Act_Unidad.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_activos.php");
	require_once("sigesp_saf_class_report.php");
	$io_report=new sigesp_saf_class_report();
	$io_fun_activos=new class_funciones_activos();
	$ls_tipoformato=$io_fun_activos->uf_obtenervalor_get("tipoformato",0);
	
	//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
	$ls_titulo="INVENTARIO DE BIENES MUNICIPALES";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$ls_nomemp=$arre["nombre"];
	$li_ordenact=$io_fun_activos->uf_obtenervalor_get("ordenact","");
	$ls_coddesde=$io_fun_activos->uf_obtenervalor_get("coddesde","");
	$ls_codhasta=$io_fun_activos->uf_obtenervalor_get("codhasta","");
	$ls_coduniadmdesde=$io_fun_activos->uf_obtenervalor_get("coduniadmdesde","");
	$ls_coduniadmhasta=$io_fun_activos->uf_obtenervalor_get("coduniadmhasta","");	//--------------------------------------------------------------------------------------------------------------------------------
	$ls_grupo=$io_fun_activos->uf_obtenervalor_get("grupo","");
	$ls_subgrupo=$io_fun_activos->uf_obtenervalor_get("subgrupo","");
	$ls_seccion=$io_fun_activos->uf_obtenervalor_get("seccion","");
	$li_incorporado=$io_fun_activos->uf_obtenervalor_get("incorporado","");
	$ls_grupohas=$io_fun_activos->uf_obtenervalor_get("grupohas","");
	$ls_subgrupohas=$io_fun_activos->uf_obtenervalor_get("subgrupohas","");
	$ls_seccionhas=$io_fun_activos->uf_obtenervalor_get("seccionhas","");
	$ls_unitri=$io_fun_activos->uf_obtenervalor_get("unitri","0");
	
	$lb_valido=$io_report->uf_saf_load_bienes_uniadm($ls_codemp,$li_ordenact,$ls_coddesde,$ls_codhasta,$ls_coduniadmdesde,
													 $ls_coduniadmhasta,$ls_grupo,$ls_subgrupo,$ls_seccion,$li_incorporado,
													 $ls_grupohas,$ls_subgrupohas,$ls_seccionhas,$ls_unitri); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////
		$ls_desc_event="Generó un reporte de Inventario por Unidad Organizativa. Desde el Activo   ".$ls_coddesde." hasta   ".$ls_codhasta;
		$io_fun_activos->uf_load_seguridad_reporte("SAF","sigesp_saf_r_bien_uniadm.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////
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
		$lo_hoja->set_column(0,0,50);
		$lo_hoja->set_column(1,2,25);	
		$lo_hoja->set_column(3,3,25);
		$lo_hoja->set_column(4,4,60);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,20);	
		$lo_hoja->set_column(7,7,20);	
		$lo_hoja->set_column(8,8,15);	
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);

		set_time_limit(1800);
		
		
		$li_totrow=$io_report->ds->getRowCount("codact");
		$i=0;
		$num=1;
		$ls_cantidad=0; 
		$li_totcosto=0;
		$li_row=0;
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
	        if ($li_i==1)
			{
			  $aux_uniad ="";
			}
			else
			{
			
			   $aux_uniad = $io_report->ds->data["coduniadm"][$li_i+1];
			}
			
			$ls_codnidadm= $io_report->ds->data["coduniadm"][$li_i];
			$ls_codact=$io_report->ds->data["codact"][$li_i];
			if ($aux_uniad == $ls_codnidadm) 
			{

				$ls_numero=$num;
				$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i];
				$ls_ideact= $io_report->ds->data["ideact"][$li_i];
				$ls_codact=$io_report->ds->data["codact"][$li_i];
				$ls_denconbie=$io_report->ds->data["denconbie"][$li_i];
				$ls_codgru=$io_report->ds->data["codgru"][$li_i];
				$ls_codsubgru=$io_report->ds->data["codsubgru"][$li_i];
				$ls_codsec=$io_report->ds->data["codsec"][$li_i];
				$ls_descripcion=$io_report->ds->data["denact"][$li_i];
				
				if (($li_ordenact==0) && ($li_i<$li_totrow))
				{
				  if ($ls_codact == $io_report->ds->data["codact"][$li_i+1] )
				  {
				    $ls_cantidad=$ls_cantidad+1; 
				  }
				  else
				  {
				   $ls_cantidad=1; 
				  }
				}
				else if (($li_ordenact==1) && ($li_i<$li_totrow))
				{
				  if ($ls_descripcion == $io_report->ds->data["denact"][$li_i+1] )
				  {
				    $ls_cantidad=$ls_cantidad+1; 
				  }
				  else
				  {
				    $ls_cantidad=1; 
				  }
				}
				
				$li_costo=$io_report->ds->data["costo"][$li_i];
				$li_totcosto=$li_totcosto+$li_costo;
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$li_row++;
				$lo_hoja->write($li_row+4, 0,$ls_codgru." ".$ls_codsubgru." ".$ls_codsec, $lo_datacenter);
				$lo_hoja->write($li_row+4, 1," ".$ls_codact, $lo_dataleft);
				$lo_hoja->write($li_row+4, 2,$ls_cantidad, $lo_dataleft);
				$lo_hoja->write($li_row+4, 3,$ls_descripcion, $lo_dataleft);
				$lo_hoja->write($li_row+4, 4,$ls_denconbie, $lo_datacenter);
				$lo_hoja->write($li_row+4, 5,$li_costo, $lo_dataright2);
				$la_data[$num]=array('codigo'=>$ls_codgru." ".$ls_codsubgru." ".$ls_codsec,'ideact'=>$ls_codact,'cantidad'=>$ls_cantidad,'descripcion'=>$ls_descripcion,
									  'estado'=>$ls_denconbie,'precio'=>$li_costo);
			   $num=$num+1;
			   $ls_cantidad=0;	
			}
			else
			{
				$ls_numero=$num;
				$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i+1];
				$ls_codact=$io_report->ds->data["codact"][$li_i];
				$ls_ideact= $io_report->ds->data["ideact"][$li_i];
				$ls_codgru=$io_report->ds->data["codgru"][$li_i];
				$ls_codsubgru=$io_report->ds->data["codsubgru"][$li_i];
				$ls_codsec=$io_report->ds->data["codsec"][$li_i];
				$ls_descripcion=$io_report->ds->data["denact"][$li_i];
				$ls_denconbie=$io_report->ds->data["denconbie"][$li_i];
				$ls_cantidad=1;
				$li_costo=$io_report->ds->data["costo"][$li_i];
				$li_totcosto=$li_totcosto+$li_costo;
				$li_costo=$io_fun_activos->uf_formatonumerico($li_costo);
				$la_data[$num]=array('codigo'=>$ls_codact,'ideact'=>$ls_ideact,'cantidad'=>$ls_cantidad,'descripcion'=>$ls_descripcion,
									  'estado'=>$ls_denconbie,'precio'=>$li_costo);
				
				if($ls_denunidadm!="")
				{
					$ls_denunidadm= $io_report->ds->data["denuniadm"][$li_i];
					$li_row++;
					$lo_hoja->write($li_row+4,0, "UNIDAD ORGANIZATIVA: ".$ls_denunidadm,$lo_titulo);	
					$li_row++;
					$lo_hoja->write($li_row+4,0, "Codigo del Bien",$lo_titulo);	
					$lo_hoja->write($li_row+4,1, "Numero de Identificacion",$lo_titulo);
					$lo_hoja->write($li_row+4,2, "Cantidad",$lo_titulo);	
					$lo_hoja->write($li_row+4,3, "Descripcion de los Bienes",$lo_titulo);
					$lo_hoja->write($li_row+4,4, "Estado del Bien",$lo_titulo);
					$lo_hoja->write($li_row+4,5, "Precio Unitario",$lo_titulo);	
				}
		
				$li_row++;
				$lo_hoja->write($li_row+4, 0,$ls_codgru." ".$ls_codsubgru." ".$ls_codsec, $lo_datacenter);
				$lo_hoja->write($li_row+4, 1," ".$ls_codact, $lo_dataleft);
				$lo_hoja->write($li_row+4, 2,$ls_cantidad, $lo_dataleft);
				$lo_hoja->write($li_row+4, 3,$ls_descripcion, $lo_dataleft);
				$lo_hoja->write($li_row+4, 4,$ls_denconbie, $lo_datacenter);
				$lo_hoja->write($li_row+4, 5,$li_costo, $lo_dataright2);

				
				unset($la_data);	
				$num=1;			
					
			}
		}
		$li_totcosto=$io_fun_activos->uf_formatonumerico($li_totcosto);
		$li_row++;
		$li_row++;
		$lo_hoja->write($li_row+4, 4,"Monto Total:", $lo_dataright);
		$lo_hoja->write($li_row+4, 5,$li_totcosto, $lo_dataright2);
		
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Act_Unidad.xls\"");
		header("Content-Disposition: inline; filename=\"Act_Unidad.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);		
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
		
	}
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_nomina);
?> 