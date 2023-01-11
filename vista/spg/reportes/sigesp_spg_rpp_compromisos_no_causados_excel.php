<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
$lo_archivo =  tempnam("/tmp", "spg_compromisos_no_causados.xls");
$lo_libro = new writeexcel_workbookbig($lo_archivo);
$lo_hoja = &$lo_libro->addworksheet();
//---------------------------------------------------------------------------------------------------------------------------

require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("sigesp_spg_funciones_reportes.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
$io_function_report = new sigesp_spg_funciones_reportes();
$io_function        = new class_funciones() ;
$io_fecha           = new class_fecha();

require_once("sigesp_spg_class_compromiso_causado_parcial.php");
$io_report = new sigesp_spg_class_compromiso_causado_parcial();
//------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------  Parámetros para Filtar el Reporte  --------------------------------------
$li_estmodest = $_SESSION["la_empresa"]["estmodest"];
$ls_tipprovbene = $_GET["tipprovbene"];
$ls_codprovbene = $_GET["codprovbene"];
$ls_procedencia = $_GET["procedencia"];
$ldt_fecdes     = $_GET["txtfecdes"];
$ldt_fechas     = $_GET["txtfechas"];
$ls_fechades    = $io_function->uf_convertirfecmostrar($ldt_fecdes);
$ls_fechahas    = $io_function->uf_convertirfecmostrar($ldt_fechas);

/////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////////
$ls_desc_event="Solicitud de Reporte  Compromisos no Causados desde la  Fecha ".$ldt_fecdes."  hasta ".$ldt_fechas;
$io_function_report->uf_load_seguridad_reporte("SPG","sigesp_vis_spg_reporte_compromisos_no_causados.html",$ls_desc_event);
////////////////////////////////         SEGURIDAD               //////////////////////////////////////////////////////
//----------------------------------------------------  Parámetros del encabezado  ----------------------------------------------
$ls_titulo="COMPROMISOS NO CAUSADOS ";
$ls_fecha=" DESDE  ".$ls_fechades."   HASTA LA FECHA  ".$ls_fechahas." ";
//--------------------------------------------------------------------------------------------------------------------------------
// Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
     $arrdatareporte    = array();	
     $data_compromisos  = $io_report->uf_obtener_compromisos($ldt_fecdes, $ldt_fechas);
	 $monto_suma=0;
     while(!$data_compromisos->EOF)
	 {
     	$procede            = $data_compromisos->fields['procede'];
     	$documento          = $data_compromisos->fields['documento'];
     	$spg_cuenta         = $data_compromisos->fields['spg_cuenta'];
     	$codestpro1         = $data_compromisos->fields['codestpro1'];
     	$estcla             = $data_compromisos->fields['estcla'];
     	$codestpro2         = $data_compromisos->fields['codestpro2'];
     	$codestpro3         = $data_compromisos->fields['codestpro3'];
     	$codestpro4         = $data_compromisos->fields['codestpro4'];
     	$codestpro5         = $data_compromisos->fields['codestpro5'];
     	$monto_comprometido = $data_compromisos->fields['monto'];            
     	
     	$resultado = $io_report->uf_buscar_causado( $procede, $documento, $spg_cuenta, $codestpro1, $estcla, $codestpro2, $codestpro3, $codestpro4, $codestpro5);
     	$data_causado = $resultado[1];
     	if((number_format($monto_comprometido, 2) > 0) && $resultado[0]==0)
		{
	     	$arrdatareporte [] = array('codigo'=>$data_compromisos->fields['codigo'],
	     		                           'nombre'=>$data_compromisos->fields['nombre'],
	     								   'tipo'=>$data_compromisos->fields['tipo_destino'],
	     								   'cuenta'=>$spg_cuenta,
	     								   'comprobante'=>$data_compromisos->fields['comprobante'],
	     								   'fecha'=>$io_function->uf_convertirfecmostrar($data_compromisos->fields['fecha']),
	     		 						   'comprometido'=>$monto_comprometido,
	     								   'causado'=>$resultado[0],
	     								   'pagado'=>$ld_total_pagado);
	    }
	    unset($resultado);
	    $data_compromisos->MoveNext();
     }
     unset($data_compromisos);
     if(empty($arrdatareporte)) // No hay registros
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
	 else // Imprimimos el reporte
	 {
		//Definimos los formatos que vamos a usar en el reporte
		$lo_encabezado= &$lo_libro->addformat();
		$lo_encabezado->set_bold();
		$lo_encabezado->set_font("Verdana");
		$lo_encabezado->set_align('center');
		$lo_encabezado->set_size('11');
		$lo_titulo= &$lo_libro->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_titulo_l= &$lo_libro->addformat();
		$lo_titulo_l->set_bold();
		$lo_titulo_l->set_font("Verdana");
		$lo_titulo_l->set_align('left');
		$lo_titulo_l->set_size('9');
		$lo_titulo_r= &$lo_libro->addformat();
		$lo_titulo_r->set_bold();
		$lo_titulo_r->set_font("Verdana");
		$lo_titulo_r->set_align('right');
		$lo_titulo_r->set_size('9');
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

		//Definimos el ancho de las columnas
		$lo_hoja->set_column(0,0,30);//nombre proveedor/beneficiario
		$lo_hoja->set_column(1,2,35);//cuenta, comprobante
		$lo_hoja->set_column(3,3,20);//fecha
		$lo_hoja->set_column(4,6,30);//comprometido, causado, pagado

		$lo_hoja->write(0, 2, $ls_titulo,$lo_encabezado);
		$lo_hoja->write(1, 2, $ls_fecha,$lo_encabezado);
		$ld_total_comprometer=0;
	    $ld_total_causado=0;
	    $ld_total_pagado=0;
		$ld_sub_total_comprometer=0;
		$ld_sub_total_causado=0;
		$ld_sub_total_pagado=0;
		
		$li_control        = 0;
		$li_causadoparcial = count($arrdatareporte);
		$ls_codprobene_ant = '';
		$li_row=3;
		foreach ($arrdatareporte as $causadoparcial) 
		{
			$ls_codprobene = $causadoparcial['codigo'];
			$ls_spg_cuenta = $causadoparcial['cuenta'];
			$ls_proc_comp  = $causadoparcial['comprobante'];
			$ldt_fecha     = $causadoparcial['fecha'];
			$ld_comprometer= $causadoparcial['comprometido'];
			$ld_causado    = $causadoparcial['causado'];
			$ld_pagado     = $causadoparcial['pagado'];
			
			$ld_total_comprometer     = $ld_total_comprometer + $ld_comprometer;
	   	 	$ld_total_causado         = $ld_total_causado + $ld_causado;
	    	$ld_total_pagado          = $ld_total_pagado  + $ld_pagado;
						
			if($ls_codprobene_ant == '')
			{
				$ls_codprobene_ant = $ls_codprobene;
				$ls_nomprobene     = $causadoparcial['nombre'];
				$ls_tipo_destino   = $causadoparcial['tipo'];
				$li_row++;
				$lo_hoja->write($li_row, 0, 'Proveedor/Beneficiario: ',$lo_titulo_r);
				$lo_hoja->write($li_row, 1, $ls_codprobene,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row, 0, 'Nombre: ',$lo_titulo_r);
				$lo_hoja->write($li_row, 1, $ls_nomprobene,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row, 1, 'Cuenta',$lo_titulo);
				$lo_hoja->write($li_row, 2, 'Comprobante',$lo_titulo);
				$lo_hoja->write($li_row, 3, 'Fecha',$lo_titulo);
				$lo_hoja->write($li_row, 4, 'Comprometido',$lo_titulo);
				$lo_hoja->write($li_row, 5, 'Causado',$lo_titulo);
				$lo_hoja->write($li_row, 6, 'Pagado',$lo_titulo);
				$li_row++;
				$lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter);
				$lo_hoja->write($li_row, 2, $ls_proc_comp,$lo_datacenter);
				$lo_hoja->write($li_row, 3, $ldt_fecha,$lo_datacenter);
				$lo_hoja->write($li_row, 4, $ld_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_pagado,$lo_dataright);

				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
			}
			else if($ls_codprobene_ant == $ls_codprobene)
			{
				$li_row++;
				$lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter);
				$lo_hoja->write($li_row, 2, $ls_proc_comp,$lo_datacenter);
				$lo_hoja->write($li_row, 3, $ldt_fecha,$lo_datacenter);
				$lo_hoja->write($li_row, 4, $ld_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_pagado,$lo_dataright);
				
				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
				$ls_codprobene_ant = $ls_codprobene;
			}
			else
			{
				$li_row++;
				$lo_hoja->write($li_row, 3, "Total Bs.",$lo_titulo_r);
				$lo_hoja->write($li_row, 4, $ld_sub_total_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_sub_total_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_sub_total_pagado,$lo_dataright);

				$ld_sub_total_comprometer = 0;
				$ld_sub_total_causado     = 0;
				$ld_sub_total_pagado      = 0;
				$ld_sub_total_comprometer = $ld_sub_total_comprometer + $ld_comprometer;
				$ld_sub_total_causado     = $ld_sub_total_causado + $ld_causado;
				$ld_sub_total_pagado      = $ld_sub_total_pagado + $ld_pagado;
				unset($la_data);
				
				
				//nuevo encabezado
				$ls_nomprobene     = $causadoparcial['nombre'];
				$ls_tipo_destino   = $causadoparcial['tipo'];
				$li_row++;
				$li_row++;
				$lo_hoja->write($li_row, 0, 'Proveedor/Beneficiario: ',$lo_titulo_r);
				$lo_hoja->write($li_row, 1, $ls_codprobene,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row, 0, 'Nombre: ',$lo_titulo_r);
				$lo_hoja->write($li_row, 1, $ls_nomprobene,$lo_dataleft);
				$li_row++;
				$lo_hoja->write($li_row, 1, 'Cuenta',$lo_titulo);
				$lo_hoja->write($li_row, 2, 'Comprobante',$lo_titulo);
				$lo_hoja->write($li_row, 3, 'Fecha',$lo_titulo);
				$lo_hoja->write($li_row, 4, 'Comprometido',$lo_titulo);
				$lo_hoja->write($li_row, 5, 'Causado',$lo_titulo);
				$lo_hoja->write($li_row, 6, 'Pagado',$lo_titulo);

				$li_row++;
				$lo_hoja->write($li_row, 1, $ls_spg_cuenta,$lo_datacenter);
				$lo_hoja->write($li_row, 2, $ls_proc_comp,$lo_datacenter);
				$lo_hoja->write($li_row, 3, $ldt_fecha,$lo_datacenter);
				$lo_hoja->write($li_row, 4, $ld_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_pagado,$lo_dataright);
				
				$ls_codprobene_ant = $ls_codprobene;
			}
			if($li_control+2>$li_causadoparcial)
			{
				$li_row++;
				$lo_hoja->write($li_row, 3, "Total Bs.",$lo_titulo_r);
				$lo_hoja->write($li_row, 4, $ld_sub_total_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_sub_total_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_sub_total_pagado,$lo_dataright);

				$li_row++;
				$li_row++;
				$li_row++;
				$lo_hoja->write($li_row, 3, "Total General",$lo_titulo_r);
				$lo_hoja->write($li_row, 4, $ld_total_comprometer,$lo_dataright);
				$lo_hoja->write($li_row, 5, $ld_total_causado,$lo_dataright);
				$lo_hoja->write($li_row, 6, $ld_total_pagado,$lo_dataright);
			}
			$li_control++;
		}
		

		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"spg_compromisos_no_causados.xls\"");
		header("Content-Disposition: inline; filename=\"spg_compromisos_no_causados.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
	
	unset($arrdatareporte);
	unset($la_data);
	unset($io_report);
	unset($io_funciones);
	unset($io_function_report);
	unset($io_fecha);
?> 