<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	ini_set('memory_limit','256M');
	ini_set('max_execution_time','0');

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_cabecera($lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_nomban // Nombre del Banco
		//	    		   io_cabecera // Objeto cabecera
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ld_hoy=date('d/m/Y');
		$lo_tituloR= &$lo_libro->addformat();
		$lo_tituloR->set_text_wrap();
		$lo_tituloR->set_bold();
		$lo_tituloR->set_font("Verdana");
		$lo_tituloR->set_align('right');
		$lo_tituloR->set_size('9');		

		$lo_tituloL= &$lo_libro->addformat();
		$lo_tituloL->set_text_wrap();
		$lo_tituloL->set_font("Verdana");
		$lo_tituloL->set_align('left');
		$lo_tituloL->set_size('9');		

		$lo_tituloC= &$lo_libro->addformat();
		$lo_tituloC->set_text_wrap();
		$lo_tituloC->set_bold();
		$lo_tituloC->set_font("Verdana");
		$lo_tituloC->set_align('center');
		$lo_tituloC->set_size('9');
		$lo_tituloC->set_fg_color('gray');
		$lo_tituloC->set_border('1');

		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('left');
		$lo_datadate->set_size('9');
		
		$lo_hoja->set_column(0,0,5);
		$lo_hoja->set_column(1,1,20);
		$lo_hoja->set_column(2,2,25);
		$lo_hoja->set_column(3,3,60);
		$lo_hoja->set_column(4,4,8);
		$lo_hoja->set_column(5,5,15);
		$lo_hoja->set_column(6,8,35);
		$lo_hoja->set_column(9,9,15);
		$lo_hoja->set_column(10,11,30);
		$lo_hoja->set_column(12,12,60);
		$lo_hoja->set_column(13,14,30);
		$lo_hoja->set_column(15,15,20);
		$lo_hoja->set_column(16,16,35);
		$lo_hoja->set_column(17,17,20);
		$lo_hoja->set_column(18,18,35);
		$lo_hoja->set_column(19,20,15);
		$lo_hoja->set_column(21,21,25);
		$lo_hoja->set_column(22,24,15);
		$lo_hoja->set_column(25,25,60);
		
		$lo_hoja->write($li_fila, 2, 'ENTE:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["nombre"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'RESPONSABLE:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["nomrep"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'CEDULA:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["cedrep"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'CORREO ELECTRONICO :',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["email"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'TELEFONO:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["telrep"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'UBICACION:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$_SESSION["la_empresa"]["direccion"],$lo_tituloL);
		$li_fila++;
		$lo_hoja->write($li_fila, 2, 'FECHA:',$lo_tituloR);
		$lo_hoja->write($li_fila, 3, " ".$ld_hoy,$lo_datadate);
		$li_fila++;
		$li_fila++;
		
		$li_fila++;
		$lo_hoja->write($li_fila, 0, 'N°',$lo_tituloC);
		$lo_hoja->write($li_fila, 1, 'NACIONALIDAD',$lo_tituloC);
		$lo_hoja->write($li_fila, 2, 'CEDULA',$lo_tituloC);
		$lo_hoja->write($li_fila, 3, 'APELLIDOS Y NOMBRES',$lo_tituloC);
		$lo_hoja->write($li_fila, 4, 'SEXO (F-M)',$lo_tituloC);
		$lo_hoja->write($li_fila, 5, 'FECHA DE NACIMIENTO',$lo_tituloC);
		$lo_hoja->write($li_fila, 6, 'TIPO DE PERSONAL',$lo_tituloC);
		$lo_hoja->write($li_fila, 7, 'CARGO NOMINA',$lo_tituloC);
		$lo_hoja->write($li_fila, 8, 'CARGO FUNCIONAL',$lo_tituloC);
		$lo_hoja->write($li_fila, 9, 'FECHA DE INGRESO',$lo_tituloC);
		$lo_hoja->write($li_fila, 10, 'TELEFONO DE CONTACTO',$lo_tituloC);
		$lo_hoja->write($li_fila, 11, 'TELEFONO DE HABITACION',$lo_tituloC);
		$lo_hoja->write($li_fila, 12, 'DIRECCION RESIDENCIAL COMPLETA',$lo_tituloC);
		$lo_hoja->write($li_fila, 13, 'CORREO ELECTRONICO PERSONAL',$lo_tituloC);
		$lo_hoja->write($li_fila, 14, 'CORREO ELECTRONICO INSTITUCIONAL',$lo_tituloC);
		$lo_hoja->write($li_fila, 15, 'POSEE ALGUNA DISCAPACIDAD',$lo_tituloC);
		$lo_hoja->write($li_fila, 16, 'INDIQUE CUAL?',$lo_tituloC);
		$lo_hoja->write($li_fila, 17, 'CARNET DE DISCAPACIDAD',$lo_tituloC);
		$lo_hoja->write($li_fila, 18, 'ENFERMEDAD CRONICA',$lo_tituloC);
		$lo_hoja->write($li_fila, 19, 'NRO DE HIJOS',$lo_tituloC);
		$lo_hoja->write($li_fila, 20, 'HIJOS EN EDAD ESCOLAR',$lo_tituloC);
		$lo_hoja->write($li_fila, 21, 'FAMILIAR (CONYUGE) TRABAJA EN EL ORGANISMO',$lo_tituloC);
		$lo_hoja->write($li_fila, 22, 'CALZADO',$lo_tituloC);
		$lo_hoja->write($li_fila, 23, 'CAMISA',$lo_tituloC);
		$lo_hoja->write($li_fila, 24, 'PANTALON',$lo_tituloC);
		$lo_hoja->write($li_fila, 25, 'OBSERVACION',$lo_tituloC);
		
		return $li_fila;
	}// uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	function uf_print_detalle($as_data,$lo_libro,$lo_hoja,$li_fila)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle por banco
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=count((Array)$as_data);
		$lo_datacenter= &$lo_libro->addformat();
		$lo_datacenter->set_text_wrap();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$lo_datacenter->set_border('1');
		
		$lo_datadate= &$lo_libro->addformat(array('num_format' => 'dd/mm/yyyy'));
		$lo_datadate->set_text_wrap();
		$lo_datadate->set_font("Verdana");
		$lo_datadate->set_align('center');
		$lo_datadate->set_size('9');
		$lo_datadate->set_border('1');
		
		$li_fila++;		
		for($li_j=1;$li_j<=$li_total;$li_j++)
		{
			$lo_hoja->write($li_fila, 0, $as_data[$li_j]['nro'],$lo_datacenter);
			$lo_hoja->write($li_fila, 1, $as_data[$li_j]['nacper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 2, $as_data[$li_j]['cedper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 3, $as_data[$li_j]['nombre'],$lo_datacenter);
			$lo_hoja->write($li_fila, 4, $as_data[$li_j]['sexper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 5, $as_data[$li_j]['fecnacper'],$lo_datadate);
			$lo_hoja->write($li_fila, 6, $as_data[$li_j]['codtippersss'],$lo_datacenter);
			$lo_hoja->write($li_fila, 7, $as_data[$li_j]['cargon'],$lo_datacenter);
			$lo_hoja->write($li_fila, 8, $as_data[$li_j]['cargof'],$lo_datacenter);
			$lo_hoja->write($li_fila, 9, $as_data[$li_j]['fecingper'],$lo_datadate);
			$lo_hoja->write($li_fila, 10, $as_data[$li_j]['telmovper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 11, $as_data[$li_j]['telhabper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 12, $as_data[$li_j]['dirper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 13, $as_data[$li_j]['coreleper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 14, $as_data[$li_j]['coreleins'],$lo_datacenter);
			$lo_hoja->write($li_fila, 15, $as_data[$li_j]['tienedis'],$lo_datacenter);
			$lo_hoja->write($li_fila, 16, $as_data[$li_j]['desdis'],$lo_datacenter);
			$lo_hoja->write($li_fila, 17, $as_data[$li_j]['nrocardis'],$lo_datacenter);
			$lo_hoja->write($li_fila, 18, $as_data[$li_j]['enfermedad'],$lo_datacenter);
			$lo_hoja->write($li_fila, 19, $as_data[$li_j]['nrohijos'],$lo_datacenter);
			$lo_hoja->write($li_fila, 20, $as_data[$li_j]['nrohijosescolar'],$lo_datacenter);
			$lo_hoja->write($li_fila, 21, $as_data[$li_j]['contraorg'],$lo_datacenter);
			$lo_hoja->write($li_fila, 22, $as_data[$li_j]['talzapper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 23, $as_data[$li_j]['talcamper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 24, $as_data[$li_j]['talpanper'],$lo_datacenter);
			$lo_hoja->write($li_fila, 25, $as_data[$li_j]['obsper'],$lo_datacenter);
			$li_fila++;
		}							   
		return $li_fila;
	}// end function uf_print_detalle
	///---------------------------------------------------------------------------------------------------------------------------

	
   	//--------------------------------------------  Llamada a clases de gneracion de excel  ------------------------------------------
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
	require_once ("../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
	$lo_archivo = tempnam("/tmp", "Personal_Auditoria.xls");
	$lo_libro = new writeexcel_workbookbig($lo_archivo);
	$lo_hoja = &$lo_libro->addworksheet();
	//-----------------------------------------------------  Instancia de las clases ------------------------------------------------
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");	
	$ls_bolivares="";
	require_once("sigesp_snorh_class_report.php");
	$io_report=new sigesp_snorh_class_report();					
    $ls_bolivares ="Bs.";
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();				
	require_once("../class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="AUDITORIA PERSONAL";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	$ls_codperdes=$io_fun_nomina->uf_obtenervalor_get("codperdes","");
	$ls_codperhas=$io_fun_nomina->uf_obtenervalor_get("codperhas","");
	$ls_discapacitado=$io_fun_nomina->uf_obtenervalor_get("discapacitado","");
	$ls_orden=$io_fun_nomina->uf_obtenervalor_get("orden","1");
	//--------------------------------------------------------------------------------------------------------------------------------
	//set_time_limit(1800);
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
	$lo_datacenter= &$lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_dataleft= &$lo_libro->addformat();
	$lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');


	$lo_hoja->write(0, 13, $ls_titulo,$lo_encabezado);
	$li_fila=2;
	$lb_valido=$io_report->uf_seleccionar_personal_auditoria($ls_codperdes,$ls_codperhas,$ls_discapacitado,$ls_orden); 
	if($lb_valido==false) // Existe algún error ó no hay registros
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	}
	else // Imprimimos el reporte
	{
		/////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////////////////
		$ls_desc_event=" Generó el reporte de Personal Auditoria ";
		$io_fun_nomina->uf_load_seguridad_reporte("SNR","sigesp_snorh_r_listadopersonal_auditoria.php",$ls_desc_event);
		////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////////////////
		$li_fila=uf_print_cabecera($lo_libro,$lo_hoja,$li_fila);
		$li_j=1;	
		while ((!$io_report->rs_data->EOF)&&($lb_valido))
		{  
			$ls_codper=$io_report->rs_data->fields["codper"];
			$ls_nacper=$io_report->rs_data->fields["nacper"];
			if ($ls_nacper=="V")
			{
				$ls_nacper="VENEZOLANA";
			}
			else
			{
				$ls_nacper="EXTRANJERO";
			}
			$ls_cedper=$io_report->rs_data->fields["cedper"];
			$ls_nombre=$io_report->rs_data->fields["nomper"].", ".$io_report->rs_data->fields["apeper"];
			$ls_sexper=$io_report->rs_data->fields["sexper"];
			$ls_fecnacper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecnacper"]);
			$ls_codtippersss=$io_report->rs_data->fields["codtippersss"];
			$ld_fecingper=$io_funciones->uf_convertirfecmostrar($io_report->rs_data->fields["fecingper"]);
			$ls_telmovper=$io_report->rs_data->fields["telmovper"];
			$ls_telhabper=$io_report->rs_data->fields["telhabper"];
			$ls_dirper=$io_report->rs_data->fields["dirper"];
			$ls_coreleper=$io_report->rs_data->fields["coreleper"];
			$ls_coreleins=$io_report->rs_data->fields["coreleins"];
			$ls_tienedis=$io_report->rs_data->fields["tienedis"];
			if ($ls_tienedis=="0")
			{
				$ls_tienedis="NO";
			}
			else
			{
				$ls_tienedis="SI";
			}						
			$ls_desdis=$io_report->rs_data->fields["desdis"];
			$ls_nrocardis=$io_report->rs_data->fields["nrocardis"];
			$ls_contraorg=$io_report->rs_data->fields["contraorg"];
			if ($ls_contraorg=="0")
			{
				$ls_contraorg="NO";
			}
			else
			{
				$ls_contraorg="SI";
			}			
			$ls_talzapper=$io_report->rs_data->fields["talzapper"];
			$ls_talcamper=$io_report->rs_data->fields["talcamper"];
			$ls_talpanper=$io_report->rs_data->fields["talpanper"];
			$ls_obsper=$io_report->rs_data->fields["obsper"];
			$ls_carantper=$io_report->rs_data->fields["carantper"];
			$arrResultado=$io_report->uf_seleccionar_personal_auditoria_hijo($ls_codper);
			$li_nrohijos=$arrResultado["nrohijos"];
			$li_nrohijosescolar=$arrResultado["nrohijosescolar"];
			unset($arrResultado);
			$arrResultado=$io_report->uf_seleccionar_personal_auditoria_enfermedad($ls_codper);
			$ls_enfermedad=$arrResultado["enfermedad"];
			unset($arrResultado);
			
			$ls_data[$li_j]=array('nro'=>$li_j,'nacper'=>$ls_nacper,'cedper'=>$ls_cedper,'nombre'=>$ls_nombre,'sexper'=>$ls_sexper,'fecnacper'=>$ls_fecnacper,
								  'codtippersss'=>$ls_codtippersss,'cargon'=>$ls_carantper,'cargof'=>$ls_carantper,'fecingper'=>$ld_fecingper,'telmovper'=>$ls_telmovper,
								  'telhabper'=>$ls_telhabper,'dirper'=>$ls_dirper,'coreleper'=>$ls_coreleper,'coreleins'=>$ls_coreleins,'tienedis'=>$ls_tienedis,
								  'desdis'=>$ls_desdis,'nrocardis'=>$ls_nrocardis,'enfermedad'=>$ls_enfermedad,'nrohijos'=>$li_nrohijos,'nrohijosescolar'=>$li_nrohijosescolar,
								  'contraorg'=>$ls_contraorg,'talzapper'=>$ls_talzapper,'talcamper'=>$ls_talcamper,'talpanper'=>$ls_talpanper,'obsper'=>$ls_obsper);
		    $li_j++;
			$io_report->rs_data->MoveNext();	  
		}
		if (empty($ls_data))
		{
			$lb_valido=false;
		}
		$li_fila=uf_print_detalle($ls_data,$lo_libro,$lo_hoja,$li_fila);
		unset($ls_data);
		
	}
	unset($io_report);
	unset($la_data);
	unset($la_datat);
	unset($io_funciones);
	unset($io_fun_nomina);
	
	if($lb_valido)
	{
		unset($io_report);
		$lo_libro->close();
		header("Content-Type: application/x-msexcel; name=\"Personal_Auditoria.xls\"");
		header("Content-Disposition: inline; filename=\"Personal_Auditoria.xls\"");
		$fh=fopen($lo_archivo, "rb");
		fpassthru($fh);
		unlink($lo_archivo);
		print("<script language=JavaScript>");
		print(" close();");
		print("</script>");
	}
	else
	{
		print("<script language=JavaScript>");
		print(" alert('Ocurrio un error al generarse el Reporte');");
		print(" close();");
		print("</script>");
	}

?> 