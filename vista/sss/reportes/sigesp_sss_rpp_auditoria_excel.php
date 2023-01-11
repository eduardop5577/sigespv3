<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	// para crear el libro excel
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "auditoria.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();

	require_once("../../../modelo/sss/sigesp_sss_class_report.php");
	$io_report=new sigesp_sss_class_report();
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("class_funciones_seguridad.php");
	$io_fun_inventario=new class_funciones_seguridad();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ld_fecdes=$io_fun_inventario->uf_obtenervalor_get("fecdes","");
	$ld_fechas=$io_fun_inventario->uf_obtenervalor_get("fechas","");
	$ls_titulo="Reporte de Auditoría Desde ".$ld_fecdes." Hasta ".$ld_fechas;
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codusu=$io_fun_inventario->uf_obtenervalor_get("codigo","");
	$ls_evento=$io_fun_inventario->uf_obtenervalor_get("evento","");
	$ls_codsis=$io_fun_inventario->uf_obtenervalor_get("sistema","");
	$ls_numdocumento=$io_fun_inventario->uf_obtenervalor_get("numdocumento","");
	$ls_numprefijo=$io_fun_inventario->uf_obtenervalor_get("numprefijo","");
	$ls_nomsis="";
	$ls_nomusu="";
	$lb_valido=true;
	//--------------------------------------------------------------------------------------------------------------------------------
	if($lb_valido)
	{
		$lb_valido=$io_report->uf_sss_select_auditoria($ls_codemp,$ls_codusu,$ls_evento,$ls_codsis,$ld_fecdes,$ld_fechas,$ls_numdocumento,$ls_numprefijo); // Cargar el DS con los datos de la cabecera del reporte
	}
	if(($lb_valido==false)||($io_report->ds->EOF)) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		set_time_limit(1800);
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
		$lo_dataright= &$lo_libro->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$lo_hoja->set_column(0,0,30);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,20);
		$lo_hoja->set_column(4,4,15);
		$lo_hoja->set_column(5,5,100);
		$lo_hoja->write(0,3,$ls_titulo,$lo_encabezado);
		if(!$io_report->ds->EOF)
		{
			$ls_nomusu= $io_report->ds->fields["nomusu"]." ".$io_report->ds->fields["apeusu"];
			$ls_nomsis= $io_report->ds->fields["nomsis"];
			if($ls_codusu=="")
			{
				$ls_nomusu="Todos los Usuarios";
			}
			if($ls_codsis=="")
			{
				$ls_nomsis="Todos los Sistemas";
			}
			if($ls_evento=="")
			{
				$ls_evento="Todos los Eventos";
			}
			$ls_usuario = 'Usuario '.$ls_codusu." - ".$ls_nomusu.'';
			$ls_sistema = 'Sistema '.$ls_codsis." - ".$ls_nomsis.'';
			$ls_evento =  'Evento - '.$ls_evento.'';
			
			$lo_hoja->write(2,1,$ls_usuario,$lo_encabezado);
			$lo_hoja->write(3,1,$ls_sistema,$lo_encabezado);
			$lo_hoja->write(4,1,$ls_evento,$lo_encabezado);
			$lo_hoja->write(6,0,'Usuario',$lo_titulo);
			$lo_hoja->write(6,1,'Evento',$lo_titulo);
			$lo_hoja->write(6,2,'Ventana',$lo_titulo);
			$lo_hoja->write(6,3,'Fecha/Hora',$lo_titulo);
			$lo_hoja->write(6,4,'Equipo',$lo_titulo);
			$lo_hoja->write(6,5,'Descripción',$lo_titulo);
			$li_pos=6;
			while(!$io_report->ds->EOF)
			{
				$li_pos=$li_pos+1;
				$ls_evento= $io_report->ds->fields["evento"];
				$ls_ventana= $io_report->ds->fields["titven"];		
				$ld_fecevetra=  date("d/m/Y H:i",strtotime($io_report->ds->fields["fecevetra"]));
				$ls_equevetra=  $io_report->ds->fields["equevetra"];
				$ls_desevetra=  $io_report->ds->fields["desevetra"];
				$ls_nomusu= $io_report->ds->fields["nomusu"]." ".$io_report->ds->fields["apeusu"];
				$ls_nomsis= $io_report->ds->fields["nomsis"];

				$lo_hoja->write($li_pos,0,$ls_nomusu,$lo_dataleft);
				$lo_hoja->write($li_pos,1,$ls_evento,$lo_datacenter);
				$lo_hoja->write($li_pos,2,$ls_ventana,$lo_dataleft);
				$lo_hoja->write($li_pos,3,$ld_fecevetra,$lo_datacenter);
				$lo_hoja->write($li_pos,4,$ls_equevetra,$lo_datacenter);
				$lo_hoja->write($li_pos,5,$ls_desevetra,$lo_dataleft);
									  
				$io_report->ds->MoveNext();
			}
			if($lb_valido)
			{
				$lo_libro->close();
				header("Content-Type: application/x-msexcel; name=\"auditoria.xls\"");
				header("Content-Disposition: inline; filename=\"auditoria.xls\"");
				$fh=fopen($lo_archivo, "rb");
				fpassthru($fh);
				unlink($lo_archivo);
				print("<script language=JavaScript>");
				print(" close();");
				print("</script>");
			}
		}
		else
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print(" close();");
			print("</script>");		
		}
	}
?>