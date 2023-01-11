<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('max_execution_time','0');
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "Articulo_detallado.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}

	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report($con);
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",0);
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
		
	//----------------------------------------------------  Inicializacion de variables  -----------------------------------------------
	$lb_valido=true;
	//----------------------------------------------------  Parámetros del encabezado    -----------------------------------------------
	$ls_titulo ="LISTADO DE LAS ORDENES DE COMPRAS";	
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	
	$ls_numordcomdes=$io_fun_soc->uf_obtenervalor_get("txtnumordcomdes","");
	$ls_numordcomhas=$io_fun_soc->uf_obtenervalor_get("txtnumordcomhas","");
	$ls_codprodes=$io_fun_soc->uf_obtenervalor_get("txtcodprodes","");
	$ls_codprohas=$io_fun_soc->uf_obtenervalor_get("txtcodprohas","");
	$ls_fecordcomdes=$io_fun_soc->uf_obtenervalor_get("txtfecordcomdes","");
	$ls_fecordcomhas=$io_fun_soc->uf_obtenervalor_get("txtfecordcomhas","");
	$ls_coduniadmdes=$io_fun_soc->uf_obtenervalor_get("txtcoduniejedes","");
	$ls_coduniadmhas=$io_fun_soc->uf_obtenervalor_get("txtcoduniejehas","");
	$ls_codartdes=$io_fun_soc->uf_obtenervalor_get("txtcodartdes","");
	$ls_codarthas=$io_fun_soc->uf_obtenervalor_get("txtcodarthas","");
	
	//--------------------------------------------------------------------------------------------------------------------------------
	$rs_data = $io_report->uf_select_listado_articulos_detallados($ls_numordcomdes,$ls_numordcomhas,$ls_codprodes,
															$ls_codprohas,$ls_fecordcomdes,$ls_fecordcomhas,$ls_coduniadmdes,
															$ls_coduniadmhas,$ls_codartdes,$ls_codarthas);
	if($rs_data==="") // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('12');
		$lo_encabezado2= &$lo_libro->addformat();
		$lo_encabezado2->set_bold();
		$lo_encabezado2->set_font("Verdana");
		$lo_encabezado2->set_align('left');
		$lo_encabezado2->set_size('10');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_text_wrap();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		/////////////////////////////////////////////////
		$lo_titulocombinado1 =& $lo_libro->addformat();
		$lo_titulocombinado1->set_size('9');		
		$lo_titulocombinado1->set_bold();
		$lo_titulocombinado1->set_font("Verdana");
		$lo_titulocombinado1->set_align('center');
		$lo_titulocombinado1->set_merge(); # This is the key feature

		$lo_titulocombinado2 =& $lo_libro->addformat();
		$lo_titulocombinado2->set_size('9');		
		$lo_titulocombinado2->set_bold();
		$lo_titulocombinado2->set_font("Verdana");
		$lo_titulocombinado2->set_align('center');
		$lo_titulocombinado2->set_merge(); # This is the key feature
		
		$lo_datacentercombinado1 =& $lo_libro->addformat();
		$lo_datacentercombinado1->set_size('9');		
		$lo_datacentercombinado1->set_font("Verdana");
		$lo_datacentercombinado1->set_align('center');
		$lo_datacentercombinado1->set_merge(); # This is the key feature
		$lo_datacentercombinado1->set_align('vcenter');
		$lo_datacentercombinado1->set_align('vjustify');

		$lo_datacentercombinado2 =& $lo_libro->addformat();
		$lo_datacentercombinado2->set_size('9');		
		$lo_datacentercombinado2->set_font("Verdana");
		$lo_datacentercombinado2->set_align('center');
		$lo_datacentercombinado2->set_merge(); # This is the key feature
		$lo_datacentercombinado2->set_align('vcenter');
		$lo_datacentercombinado2->set_align('vjustify');
		
		$lo_dataleftcombinado1 =& $lo_libro->addformat();
		$lo_dataleftcombinado1->set_size('9');		
		$lo_dataleftcombinado1->set_font("Verdana");
		$lo_dataleftcombinado1->set_align('left');
		$lo_dataleftcombinado1->set_merge(); # This is the key feature

		$lo_dataleftcombinado2 =& $lo_libro->addformat();
		$lo_dataleftcombinado2->set_size('9');		
		$lo_dataleftcombinado2->set_font("Verdana");
		$lo_dataleftcombinado2->set_align('left');
		$lo_dataleftcombinado2->set_merge(); # This is the key feature
		/////////////////////////////////////////////////
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
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,15);
		$lo_hoja->set_column(2,2,30);
		$lo_hoja->set_column(3,3,15);
		$lo_hoja->set_column(4,4,30);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,6,15);
		$lo_hoja->set_column(7,7,15);
		$lo_hoja->set_column(8,8,15);
		$lo_hoja->set_column(9,9,15);

		$lo_hoja->write(1,3,$ls_titulo,$lo_encabezado);
		
		$lo_hoja->write(3,0,"Orden de Compra",$lo_titulo);
		$lo_hoja->write(3,1,"Fecha",$lo_titulo);
		$lo_hoja->write(3,2,"Proveedoor",$lo_titulo);
		$lo_hoja->write(3,3,"Codigo",$lo_titulo);
		$lo_hoja->write(3,4,"Denominacion",$lo_titulo);
		$lo_hoja->write(3,5,"Precio Unitario",$lo_titulo);
		$lo_hoja->write(3,6,"Cantidad Solicitada",$lo_titulo);
		$lo_hoja->write(3,7,"Cantidad Recibida",$lo_titulo);
		$lo_hoja->write(3,8,"Monto",$lo_titulo);
		$li_totmonsub=0;
		$li_totcanart=0;
		$li_totrec=0;
		$li_i=0;
		while($row=$io_sql->fetch_row($rs_data))
		{
			$li_i++;
			$ls_numordcom  = $row["numordcom"]; 
			$ls_codpro  = $row["cod_pro"]; 
			$ls_fecordcom  = $row["fecordcom"]; 
			$ls_codart  = rtrim($row["codart"]); 
			$ls_denart  = rtrim($row["denart"]); 
			$ls_nompro  = rtrim($row["nompro"]); 
			$li_preuniart  = $row["preuniart"]; 
			$li_monsubart  = $row["monsubart"]; 
			$li_canart  = $row["canart"]; 
			$li_totartrec = $io_report->uf_select_listado_articulos_recibidos($ls_numordcom,$ls_codpro,$ls_codart);
			$li_totmonsub=$li_totmonsub+$li_monsubart;
			$li_totcanart=$li_totcanart+$li_canart;
			$li_totrec=$li_totrec+$li_totartrec;
			$li_preuniart  = number_format($li_preuniart,2,',','.');
			$li_monsubart  = number_format($li_monsubart,2,',','.');
			$li_totartrec  = number_format($li_totartrec,2,',','.');
			$li_canart  = number_format($li_canart,2,',','.');
			$ls_fecordcom   = $io_funciones->uf_convertirfecmostrar($ls_fecordcom);	
					
			$lo_hoja->write(3+$li_i,0,$ls_numordcom,$lo_datacenter);
			$lo_hoja->write(3+$li_i,1,$ls_fecordcom,$lo_datacenter);
			$lo_hoja->write(3+$li_i,2,$ls_nompro,$lo_dataleft);
			$lo_hoja->write(3+$li_i,3,$ls_codart,$lo_datacenter);
			$lo_hoja->write(3+$li_i,4,$ls_denart,$lo_dataleft);
			$lo_hoja->write(3+$li_i,5,$li_preuniart,$lo_dataright);
			$lo_hoja->write(3+$li_i,6,$li_canart,$lo_dataright);
			$lo_hoja->write(3+$li_i,7,$li_totartrec,$lo_dataright);
			$lo_hoja->write(3+$li_i,8,$li_monsubart,$lo_dataright);


			$la_data[$li_i]= array('numordcom'=>$ls_numordcom,'fecordcom'=>$ls_fecordcom,'codart'=>$ls_codart,
								 'denart'=>$ls_denart,'nompro'=>$ls_nompro,'preuniart'=>$li_preuniart,'canart'=>$li_canart,'totartrec'=>$li_totartrec,'monsubart'=>$li_monsubart);
		}
		
		if($li_i>0) // Si no ocurrio ningún error
		{
			$li_totmonsub  = number_format($li_totmonsub,2,',','.');
			$li_totcanart  = number_format($li_totcanart,2,',','.');
			$li_totrec  = number_format($li_totrec,2,',','.');
			$lo_hoja->write(5+$li_i,5,"TOTALES:",$lo_dataleft);
			$lo_hoja->write(5+$li_i,6,$li_totcanart,$lo_dataright);
			$lo_hoja->write(5+$li_i,7,$li_totrec,$lo_dataright);
			$lo_hoja->write(5+$li_i,8,$li_totmonsub,$lo_dataright);

			$lo_libro->close();
			header("Content-Type: application/x-msexcel; name=\"articulosdetallado.xls\"");
			header("Content-Disposition: inline; filename=\"articulosdetallado.xls\"");
			$fh=fopen($lo_archivo, "rb");
			fpassthru($fh);
			unlink($lo_archivo);
			print("<script language=JavaScript>");
			print(" close();");
			print("</script>");
			unset($io_pdf);
		}
		else  // Si hubo algún error
		{
			print("<script language=JavaScript>");
			print(" alert('No hay nada que Reportar');"); 
			print("close();");
			print("</script>");		
		}
	}	
?> 