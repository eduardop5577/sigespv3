<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('memory_limit','512M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($ls_descripcion)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_inventario;
		$lb_valido=$io_fun_inventario->uf_load_seguridad_reporte("SIV","sigesp_siv_r_articuloxalmacen.php",$ls_descripcion);
		return $lb_valido;
	}
	//--------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------
	// para crear el libro excel
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "listado_existencias.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//---------------------------------------------------------------------------------------------------------------------------
	// para crear la data necesaria del reporte
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("sigesp_siv_class_report.php");
	$io_report=new sigesp_siv_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_funciones_inventario.php");
	$io_fun_inventario=new class_funciones_inventario();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
        $ls_titulo="Niveles de Existencia de Artículos";
	$ls_fecha="";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
	$ls_codart="";
	$ls_codarti=$io_fun_inventario->uf_obtenervalor_get("codart","");
	$ls_codalmacen=$io_fun_inventario->uf_obtenervalor_get("codalm","");
	$ls_codtipart=$io_fun_inventario->uf_obtenervalor_get("codtipart","");
	$li_ordenalm=$io_fun_inventario->uf_obtenervalor_get("ordenalm",0);
	$li_ordenart=$io_fun_inventario->uf_obtenervalor_get("ordenart",0);
	$li_valexistencia=$io_fun_inventario->uf_obtenervalor_get("existencia",0);
	$ls_codseg=$io_fun_inventario->uf_obtenervalor_get("codseg","");
	$ls_codfam=$io_fun_inventario->uf_obtenervalor_get("codfam","");
	$ls_codcla=$io_fun_inventario->uf_obtenervalor_get("codcla","");
	$ls_codpro=$io_fun_inventario->uf_obtenervalor_get("codpro","");	
	//---------------------------------------------------------------------------------------------------------------------------
	//Busqueda de la data 
        $ls_descripcion="Genero el reporte de Niveles de Existencia de Articulos, del Articulo ".$ls_codarti." en el almacen  ".$ls_codalm;
	$lb_valido=uf_insert_seguridad($ls_descripcion); // Seguridad de Reporte
      	$lb_valido=$io_report->uf_select_almacen($ls_codemp,$ls_codalmacen,$ls_codarti,$ls_codtipart,$li_ordenalm,$ls_codseg,$ls_codfam,$ls_codcla,$ls_codpro); // Cargar el DS con los datos de la cabecera del reporte
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
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
		
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,45);
		$lo_hoja->set_column(2,2,20);
		$lo_hoja->set_column(3,3,20);
		//---------------------------------------------------------------------------------------------
		$lo_hoja->set_column(0,0,20);
		$lo_hoja->set_column(1,1,60);
		$lo_hoja->write(0,1,$ls_titulo,$lo_encabezado);
                $li_fila=0;
                $li_totaldetal=0;
                $li_totalmayor=0;
		$li_totrow=$io_report->ds->getRowCount("codalm");
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
                    $ls_codalm=$io_report->ds->data["codalm"][$li_i];
                    $ls_nomfisalm=$io_report->ds->data["nomfisalm"][$li_i];
                    $li_fila++;
                    $li_fila++;
                    $li_fila++;
                    $lo_hoja->write($li_fila,0,"Empresa",$lo_titulo);
                    $lo_hoja->write($li_fila,1,$ls_nomemp,$lo_dataleft);
                    $li_fila++;
                    $lo_hoja->write($li_fila,0,"Almacen",$lo_titulo);
                    $lo_hoja->write($li_fila,1,$ls_nomfisalm,$lo_dataleft);
                    $lb_valido=$io_report->uf_select_articuloxalmacen($ls_codemp,$ls_codalm,$ls_codarti,$ls_codtipart,$li_ordenalm,$li_ordenart,$ls_codseg,$ls_codfam,$ls_codcla,$ls_codpro); // Obtenemos el detalle del reporte
                    if($lb_valido)
                    {
                        $li_totrow_det=$io_report->ds_detalle->getRowCount("codart");
                        $li_fila++;
                        $li_fila++;
                        $lo_hoja->write($li_fila,0,"Codigo",$lo_titulo);
                        $lo_hoja->write($li_fila,1,"Denominacion",$lo_titulo);
                        $lo_hoja->write($li_fila,2,"Existencia (Detal)",$lo_titulo);
                        $lo_hoja->write($li_fila,3,"Existencia (Mayor)",$lo_titulo);
                        for($li_s=1;$li_s<=$li_totrow_det;$li_s++)
                        {
                            $ls_codart= $io_report->ds_detalle->data["codart"][$li_s];
                            $ls_denart= $io_report->ds_detalle->data["denart"][$li_s];
                            $li_detal= $io_report->ds_detalle->data["existencia"][$li_s];
                            $li_totaldetal=$li_totaldetal + $li_detal;
                            $li_unidad=     $io_report->ds_detalle->data["unidades"][$li_s];
                            $li_existencia= ($li_detal/$li_unidad);
                            if ($li_valexistencia==1)
                            {
                                if ($li_existencia>0)
                                {
                                    //$li_existencia=number_format($li_existencia,2,",",".");
                                    //$li_detal=number_format($li_detal,2,",",".");
                                    $li_totalmayor=$li_totalmayor + $li_existencia;
                                    $li_fila++;
                                    $lo_hoja->write($li_fila,0,$ls_codart,$lo_datacenter);
                                    $lo_hoja->write($li_fila,1,$ls_denart,$lo_dataleft);
                                    $lo_hoja->write($li_fila,2,$li_detal,$lo_dataright2);
                                    $lo_hoja->write($li_fila,3,$li_existencia,$lo_dataright2);
                                }
                            }
                            else
                            {
                                //$li_existencia=number_format($li_existencia,2,",",".");
                                // $li_detal=number_format($li_detal,2,",",".");
                                $li_totalmayor=$li_totalmayor + $li_existencia;
                                $li_fila++;
                                $lo_hoja->write($li_fila,0,$ls_codart,$lo_datacenter);
                                $lo_hoja->write($li_fila,1,$ls_denart,$lo_dataleft);
                                $lo_hoja->write($li_fila,2,$li_detal,$lo_dataright2);
                                $lo_hoja->write($li_fila,3,$li_existencia,$lo_dataright2);
                            }
        		}
                    }
		}
                if ($ls_codalmacen=="")
                {
                    $li_fila++;
                    $li_fila++;
                    $lo_hoja->write($li_fila,0,"Total Existencia",$lo_titulo);
                    $lo_hoja->write($li_fila,1,"",$lo_titulo);
                    $lo_hoja->write($li_fila,2,$li_totaldetal,$lo_dataright2);
                    $lo_hoja->write($li_fila,3,$li_totalmayor,$lo_dataright2);                    
                }
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"listado_existencias.xls\"");
		header("Content-Disposition: inline; filename=\"listado_existencias.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
		unset($io_pdf);
	}/// fin de else // Imprimimos el reporte
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_inventario);
?> 
