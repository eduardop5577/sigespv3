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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_periodo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 25/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->stopObject($io_encabezado);
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],30,700,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,730,11,$as_titulo); // Agregar el título

		$li_tm=$io_pdf->getTextWidth(11,$as_periodo);
		$tm=306-($li_tm/2);
		$io_pdf->addText($tm,718,11,$as_periodo); // Agregar el título
		$io_pdf->addText(500,740,10,$_SESSION["ls_database"]);// Agrerar el nombre de la base de datos actual
		$io_pdf->addText(500,730,10,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina

	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_denominacion_estructura2($la_columna,$la_config,$la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 24/04/2006
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		global $io_fun_nomina;

		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		//$io_pdf->ezText('                     ',10);//Inserto una linea en blanco
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------


	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Arnal Suárez
		// Fecha Creación: 28/06/2010
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$la_columna  = array('cuenta'=>'<b>Cuenta</b>   ','denominacion'=>"<b>Denominacion Cta. Presupuestaria</b>",'cuenta_scg'=>"<b>Cuenta Contable (Articulo)</b>",'denscg'=>'<b>Denominacion Cta.Contable(Articulo)</b>');
		$la_config   = array('showHeadings'=>1, // Mostrar encabezados
							 'showLines'=>1, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'shadeCol'=>array(0.95,0.95,0.95), // Color de la sombra
							 'shadeCol2'=>array(1.5,1.5,1.5), // Color de la sombra
							 'xOrientation'=>'center', // Orientación de la tabla
							 'width'=>550, // Ancho de la tabla
							 'maxWidth'=>550,
							 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>75) ,
										   'denominacion'=>array('justification'=>'left','width'=>190),
										   'cuenta_scg'=>array('justification'=>'center','width'=>95),
										   'denominacion'=>array('justification'=>'left','width'=>190)));
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	 function uf_print_cabecera_estructura($io_encabezado,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
		                                   $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estcla,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: as_programatica // programatica del comprobante
		//	    		   as_denestpro5 // denominacion de la programatica del comprobante
		//	    		   io_pdf // Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Arnaldo Suárez
		// Fecha Creación: 28/06/2010
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		$io_pdf->saveState();
		$io_pdf->ezSetY(700);
		$ls_estmodest  = $_SESSION["la_empresa"]["estmodest"];
		$li_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
		$li_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
		$li_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
		$li_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
		$li_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	    $li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	    $li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	    $li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	    $li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
		
		$ls_codestpro1    = trim(substr($ls_codestpro1,-$li_loncodestpro1));
		$ls_codestpro2    = trim(substr($ls_codestpro2,-$li_loncodestpro2));
		$ls_codestpro3    = trim(substr($ls_codestpro3,-$li_loncodestpro3));
		$ls_codestpro4    = trim(substr($ls_codestpro4,-$li_loncodestpro4));
		$ls_codestpro5    = trim(substr($ls_codestpro5,-$li_loncodestpro5));
		
		if ($ls_estmodest==1)
		{
			$ls_tipoest = "";
			if($ls_estcla == "A")
			{
			 $ls_tipoest = "ACCION";
			}
			elseif($ls_estcla == "P")
			{
			 $ls_tipoest = "PROYECTO";
			}
			$la_config_tipo=array('showHeadings'=>0, // Mostrar encabezados
								 'fontSize' =>10, // Tamaño de Letras
								 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
								 'showLines'=>0, // Mostrar Líneas
								 'shaded'=>0, // Sombra entre líneas
								 'colGap'=>1, // separacion entre tablas
								 'width'=>500, // Ancho de la tabla
								 'maxWidth'=>500, // Ancho Máximo de la tabla
								 'xOrientation'=>'center', // Orientación de la tabla
								 'xPos'=>280,
								 'cols'=>array('column'=>array('justification'=>'left','width'=>500)));
			$la_columna_tipo=array('column'=>'');
			$ls_data_tipo[0]=array('column'=>'<b> ESTRUCTURA PRESUPUESTARIA TIPO ('.$ls_tipoest.')</b>');			
			$io_pdf->ezTable($ls_data_tipo,$la_columna_tipo,'',$la_config_tipo);
			
			$ls_denestpro1=substr($ls_denestpro1,0,100);
			$ls_denestpro2=substr($ls_denestpro2,0,100);
			$ls_denestpro3=substr($ls_denestpro3,0,100);
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' =>9, // Tamaño de Letras
							 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>500, // Ancho de la tabla
							 'maxWidth'=>500, // Ancho Máximo de la tabla
							 'xPos'=>300,
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>150),									  
										   'codestpro'=>array('justification'=>'center','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>340)));
			$la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
			$io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);
		}
		else
		{
			$ls_denestpro1=substr($ls_denestpro1,0,100);
			$ls_denestpro2=substr($ls_denestpro2,0,100);
			$ls_denestpro3=substr($ls_denestpro3,0,100);
			$ls_denestpro4=substr($ls_denestpro4,0,100);
			$ls_denestpro5=substr($ls_denestpro5,0,100);
			$ls_datat1[0]=array('nombre'=>'<b>PROGRAMATICA</b> ','codestpro'=>"",'denom'=>"");
			$ls_datat1[1]=array('nombre'=>'<b>'.$li_nomestpro1.":</b> ",'codestpro'=>$ls_codestpro1,'denom'=>$ls_denestpro1);
			$ls_datat1[2]=array('nombre'=>'<b>'.$li_nomestpro2.":</b> ",'codestpro'=>$ls_codestpro2,'denom'=>$ls_denestpro2);
			$ls_datat1[3]=array('nombre'=>'<b>'.$li_nomestpro3.":</b> ",'codestpro'=>$ls_codestpro3,'denom'=>$ls_denestpro3);
			$ls_datat1[4]=array('nombre'=>'<b>'.$li_nomestpro4.":</b> ",'codestpro'=>$ls_codestpro4,'denom'=>$ls_denestpro4);
			$ls_datat1[5]=array('nombre'=>'<b>'.$li_nomestpro5.":</b> ",'codestpro'=>$ls_codestpro5,'denom'=>$ls_denestpro5);			
			
			$la_config=array('showHeadings'=>0, // Mostrar encabezados
							 'fontSize' =>9, // Tamaño de Letras
							 'titleFontSize' => 10,  // Tamaño de Letras de los títulos
							 'showLines'=>0, // Mostrar Líneas
							 'shaded'=>0, // Sombra entre líneas
							 'colGap'=>1, // separacion entre tablas
							 'width'=>500, // Ancho de la tabla
							 'maxWidth'=>500, // Ancho Máximo de la tabla
							 'xPos'=>300,
							 'cols'=>array('nombre'=>array('justification'=>'left','width'=>100),									  
										   'codestpro'=>array('justification'=>'center','width'=>60),
										   'denom'=>array('justification'=>'left','width'=>340)));
		   $la_columna=array('nombre'=>'','codestpro'=>'','denom'=>'');			
		   $io_pdf->ezTable($ls_datat1,$la_columna,'',$la_config);	
		}
		unset($ls_datat1);
		unset($la_config);
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');			
	}// end function uf_print_cabecera_estructura


	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=new class_sql($con);
	$io_sql2=new class_sql($con);
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../../../base/librerias/php/general/sigesp_lib_datastore.php");
	$ds_prog=new class_datastore();
	$ds_ctas=new class_datastore();
	require_once("sigesp_spg_funciones_reportes.php");
	$io_function_report = new sigesp_spg_funciones_reportes();
	require_once("sigesp_spg_reporte.php");
	$io_spg_report=new sigesp_spg_reporte();

	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_codestpro1_desde=$_GET["codestpro1"];
	$ls_codestpro2_desde=$_GET["codestpro2"];
	$ls_codestpro3_desde=$_GET["codestpro3"];
	$ls_codestpro1_hasta=$_GET["codestpro1h"];
	$ls_codestpro2_hasta=$_GET["codestpro2h"];
	$ls_codestpro3_hasta=$_GET["codestpro3h"];
	$ls_cuenta_desde=$_GET["txtcuentades"];
	$ls_cuenta_hasta=$_GET["txtcuentahas"];
	$ls_ctascg_desde=$_GET["cuentascg_desde"];
	$ls_ctascg_hasta=$_GET["cuentascg_hasta"];
	$ls_estclades = $_GET["estclades"];
	$ls_estclahas = $_GET["estclahas"];
    $ls_codfuefindes=$_GET["txtcodfuefindes"];
    $ls_codfuefinhas=$_GET["txtcodfuefinhas"];
	
    if (($ls_codfuefindes=='')&&($ls_codfuefindes==''))
    {
			$arrResultado=$io_function_report->uf_spg_select_fuentefinanciamiento($ls_minfuefin,$ls_maxfuefin);
			$ls_minfuefin=$arrResultado['as_minfuefin'];
			$ls_maxfuefin=$arrResultado['as_maxfuefin'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				 $ls_codfuefindes=$ls_minfuefin;
				 $ls_codfuefinhas=$ls_maxfuefin;
			  }
    }
	
	if(!empty($$ls_codestpro1_desde))
	 {
	  $ls_codestpro1_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro1_desde,25);
	 }
	 if(!empty($ls_codestpro2_desde))
	 {
	  $ls_codestpro2_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro2_desde,25);
	 }
	 if(!empty($ls_codestpro3_desde))
	 {
	  $ls_codestpro3_desde  = $io_funciones->uf_cerosizquierda($ls_codestpro3_desde,25);
	 }
	 
	 if(!empty($$ls_codestpro1_hasta))
	 {
	  $ls_codestpro1_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro1_hasta,25);
	 }
	 if(!empty($ls_codestpro2_hasta))
	 {
	  $ls_codestpro2_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro2_hasta,25);
	 }
	 if(!empty($ls_codestpro3_hasta))
	 {
	  $ls_codestpro3_hasta  = $io_funciones->uf_cerosizquierda($ls_codestpro3_hasta,25);
	 }
	 if($li_estmodest==2)
	 {
		 if(!empty($_GET["codestpro4"]))
		 {
		  $ls_codestpro4_desde  = $io_funciones->uf_cerosizquierda($_GET["codestpro4"],25);
		 }
		 if(!empty($_GET["codestpro5"]))
		 {
		  $ls_codestpro5_desde  = $io_funciones->uf_cerosizquierda($_GET["codestpro5"],25);
		 }
		 
		 if(!empty($_GET["codestpro4h"]))
		 {
		  $ls_codestpro4_hasta  = $io_funciones->uf_cerosizquierda($_GET["codestpro4h"],25);
		 }
		 if(!empty($_GET["codestpro5h"]))
		 {
		  $ls_codestpro5_hasta  = $io_funciones->uf_cerosizquierda($_GET["codestpro5h"],25);
		 }
	 }
	 else
	 {
		 $ls_codestpro4_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_desde=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro4_hasta=$io_funciones->uf_cerosizquierda(0,25);
		 $ls_codestpro5_hasta=$io_funciones->uf_cerosizquierda(0,25);
	 }
	 
	 if(!empty($ls_codestpro1_desde))
	 {
		$ls_codestpro1_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_desde,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro1($ls_codestpro1_desde,$ls_estclades);
		$ls_codestpro1_desde=$arrResultado['as_codestpro1'];
		$ls_estclades=$arrResultado['as_estclades'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro2_desde))
	 {
		$ls_codestpro2_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_desde,25);	
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro2($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_estclades);
		$ls_codestpro2_desde=$arrResultado['as_codestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro3_desde))
	 {
		$ls_codestpro3_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_desde,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro3($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_estclades);
		$ls_codestpro3_desde=$arrResultado['as_codestpro3'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 
	 if(!empty($ls_codestpro1_hasta))
	 {
		$ls_codestpro1_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro1_hasta,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro1($ls_codestpro1_hasta,$ls_estclahas);
		$ls_codestpro1_hasta=$arrResultado['as_codestpro1'];
		$ls_estclahas=$arrResultado['as_estclahas'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro2_hasta))
	 {
		$ls_codestpro2_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro2_hasta,25);	
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro2($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_estclahas);
		$ls_codestpro2_hasta=$arrResultado['as_codestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(!empty($ls_codestpro3_hasta))
	 {
		$ls_codestpro3_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro3_hasta,25);
	 }
	 else
	 {
		$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro3($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_estclahas);
		$ls_codestpro3_hasta=$arrResultado['as_codestpro3'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if($li_estmodest==2)
	 {
	    if(!empty($ls_codestpro4_desde))
		{	
		 $ls_codestpro4_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro4_desde,25);	
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro4($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_estclades);
			$ls_codestpro4_desde=$arrResultado['as_codestpro4'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if(!empty($ls_codestpro5_desde))
		{	
		  $ls_codestpro5_desde=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro5_desde,25);
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_min_codestpro5($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde,$ls_estclades);
			$ls_codestpro5_desde=$arrResultado['as_codestpro5'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		
		if(!empty($ls_codestpro4_hasta))
		{	
		 $ls_codestpro4_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro4_hasta,25);	
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro4($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_estclahas);
			$ls_codestpro4_hasta=$arrResultado['as_codestpro4'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if(!empty($ls_codestpro5_hasta))
		{	
		  $ls_codestpro5_hasta=$io_spg_report->fun->uf_cerosizquierda($ls_codestpro5_hasta,25);
		}
		else
		{
			$arrResultado=$io_function_report->uf_spg_reporte_select_max_codestpro5($ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta,$ls_estclahas);
			$ls_codestpro5_hasta=$arrResultado['as_codestpro5'];
			$lb_valido=$arrResultado['lb_valido'];
		}
	 
	 }
	$rs_estructuras=NULL;
	$arrResultado = $io_spg_report->uf_reporte_listado_cuenta_estructura($ls_codestpro1_desde,$ls_codestpro2_desde,$ls_codestpro3_desde,$ls_codestpro4_desde,$ls_codestpro5_desde,
                                                         $ls_codestpro1_hasta,$ls_codestpro2_hasta,$ls_codestpro3_hasta,$ls_codestpro4_hasta,$ls_codestpro5_hasta,
											             $ls_estclades,$ls_estclahas,$ls_codfuefindes,$ls_codfuefinhas,$ls_cuenta_desde,$ls_cuenta_hasta,
											             $ls_ctascg_desde,$ls_ctascg_hasta,$rs_estructuras);
	$rs_estructuras=$arrResultado['rs_estructuras'];
	$lb_valido =$arrResultado['lb_valido'];
	if($lb_valido)
	{
	 	//$li_totrow=$ds_prog->getRowCount("codestpro1");
		$li_totrow=$rs_estructuras->RecordCount();
		if($li_totrow<=0)
		{
			?>
			<script language=javascript>
			 alert('No hay datos a reportar!!!');
			 close();
			</script>
			<?php
		}
		else
		{
			$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			$io_pdf->ezSetCmMargins(5.0,3.5,3.5,3.5); // Configuración de los margenes en centímetros
			
			uf_print_encabezado_pagina("Listado de Cuentas Presupuestarias(Vinculación Cuenta Articulos) "," ",$io_pdf); // Imprimimos el encabezado de la página
			$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
	
			$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
			$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
			$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
			$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
			$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
			
			if($li_estmodest==1)
			{
			 $ls_estructura_desde = substr($ls_codestpro1_desde,-$ls_loncodestpro1)."-".substr($ls_codestpro2_desde,-$ls_loncodestpro2)."-".substr($ls_codestpro3_desde,-$ls_loncodestpro3)."-".$ls_estclades;
			 $ls_estructura_hasta = substr($ls_codestpro1_hasta,-$ls_loncodestpro1)."-".substr($ls_codestpro2_hasta,-$ls_loncodestpro2)."-".substr($ls_codestpro3_hasta,-$ls_loncodestpro3)."-".$ls_estclahas;
			}
			else
			{
			 $ls_estructura_desde = substr($ls_codestpro1_desde,-$ls_loncodestpro1)."-".substr($ls_codestpro2_desde,-$ls_loncodestpro2)."-".substr($ls_codestpro3_desde,-$ls_loncodestpro3)."-".substr($ls_codestpro4_desde,-$ls_loncodestpro4)."-".substr($ls_codestpro5_desde,-$ls_loncodestpro5)."-".$ls_estclades;
			 $ls_estructura_hasta = substr($ls_codestpro1_hasta,-$ls_loncodestpro1)."-".substr($ls_codestpro2_hasta,-$ls_loncodestpro2)."-".substr($ls_codestpro3_hasta,-$ls_loncodestpro3)."-".substr($ls_codestpro4_hasta,-$ls_loncodestpro4)."-".substr($ls_codestpro5_hasta,-$ls_loncodestpro5)."-".$ls_estclahas;
			}
			
			$ls_desc_event="Solicitud de Reporte Listado de Cuentas Presupuestarias desde la Estructura  ".$ls_estructura_desde." hasta ".$ls_estructura_hasta;
	        $io_function_report->uf_load_seguridad_reporte("SPG","sigesp_spg_r_cuentas.php",$ls_desc_event);
			
			$ls_nomestpro1 = $_SESSION["la_empresa"]["nomestpro1"];
			$ls_nomestpro2 = $_SESSION["la_empresa"]["nomestpro2"];
			$ls_nomestpro3 = $_SESSION["la_empresa"]["nomestpro3"];
			$ls_nomestpro4 = $_SESSION["la_empresa"]["nomestpro4"];
			$ls_nomestpro5 = $_SESSION["la_empresa"]["nomestpro5"];
			$li_total = 0;
			while(!$rs_estructuras->EOF)
			{
				
				$ls_codestpro1=$rs_estructuras->fields["codestpro1"];
				$ls_codestpro2=$rs_estructuras->fields["codestpro2"];
				$ls_codestpro3=$rs_estructuras->fields["codestpro3"];
				$ls_estcla = $rs_estructuras->fields["estcla"];
				$ls_codestpro4="";
				$ls_codestpro5="";
				$ls_denestpro1   = "";
				$ls_denestpro2   = "";
				$ls_denestpro3   = "";
				$ls_denestpro4   = "";
				$ls_denestpro5   = "";
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];

				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
			    $ls_denestpro2=$arrResultado['as_denestpro2'];
			    $lb_valido=$arrResultado['lb_valido'];
				$arrResultado=$io_function_report->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
			     $ls_denestpro3=$arrResultado['as_denestpro3'];
			     $lb_valido=$arrResultado['lb_valido'];
				if($li_estmodest==1)
				{
				  $ls_codestpro4="0000000000000000000000000";
				  $ls_codestpro5="0000000000000000000000000";
				}
			    elseif($li_estmodest==2)
				{
				 $ls_codestpro4=$rs_estructuras->fields["codestpro4"];
				 $ls_codestpro5=$rs_estructuras->fields["codestpro5"];
				 $ls_denestpro4   = "";
				 $ls_denestpro5   = "";
				 $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
				 $ls_denestpro4=$arrResultado['as_denestpro4'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $arrResultado=$io_function_report->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
				 $ls_denestpro5=$arrResultado['as_denestpro5'];
				 $lb_valido=$arrResultado['lb_valido'];
				}
				
			   $rs_cuentas = NULL;
				
				$arrResultado=$io_spg_report->uf_reporte_listado_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
														  $ls_estcla,$ls_cuenta_desde,$ls_cuenta_hasta,$ls_ctascg_desde,$ls_ctascg_hasta,
														  $rs_cuentas, true);
				$rs_cuentas=$arrResultado['rs_cuentas'];
				$lb_valido=$arrResultado['lb_valido'];
				$la_data_ctas = NULL;										  
				if($lb_valido)
				{
				 $li_a = 0;
				 while(!$rs_cuentas->EOF)
				 {
					 $ls_cuenta      = trim($rs_cuentas->fields["spg_cuenta"]);
					 $ls_denominacion= trim($rs_cuentas->fields["denominacion"]);
					 $ls_cuenta_scg  = trim($rs_cuentas->fields["sc_cuenta"]);
					 $ls_status      = trim($rs_cuentas->fields["status"]);
					 $ls_denscg      = trim($rs_cuentas->fields["denominacion_scg"]);
					 if($ls_status=='C')
					 {
						$la_data_ctas[$li_a] = array('cuenta'=>'<b>'.$ls_cuenta.'</b>','denominacion'=>'<b>'.$ls_denominacion.'</b>','cuenta_scg'=>'<b>'.$ls_cuenta_scg.'</b>','denscg'=>'<b>'.$ls_denscg.'</b>');
					 }
					 else
					 {
						$la_data_ctas[$li_a] = array('cuenta'=>$ls_cuenta,'denominacion'=>$ls_denominacion,'cuenta_scg'=>' ','denscg'=>' ');
					 }
				   $li_a++;
				   $rs_cuentas->MoveNext();
				 }
			    }
			   $io_encabezado=$io_pdf->openObject();
			   uf_print_cabecera_estructura($io_encabezado,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,
		                                    $ls_denestpro1,$ls_denestpro2,$ls_denestpro3,$ls_denestpro4,$ls_denestpro5,$ls_estcla,$io_pdf);
			   $io_pdf->ezSetCmMargins(7.0,3.5,3.5,3.5); // Configuración de los margenes en centímetros
			   uf_print_detalle($la_data_ctas,$io_pdf);
			   $io_pdf->stopObject($io_encabezado);
			   $li_total++;
			   if($li_total < $li_totrow)
			   {
			    $io_pdf->ezNewPage();
			   }
			  $rs_estructuras->MoveNext();	
			}
			$io_pdf->ezStopPageNumbers(1,1);
			if (isset($d) && $d)
			{
				$ls_pdfcode = $io_pdf->ezOutput(1);
				$ls_pdfcode = str_replace("\n","\n<br>",htmlspecialchars($ls_pdfcode));
				echo '<html><body>';
				echo trim($ls_pdfcode);
				echo '</body></html>';
			}
			else
			{
				$io_pdf->ezStream();
			}
			unset($io_pdf);
			unset($class_report);
			unset($io_funciones);
		}
    }
	else
	{
		 ?>
			<script language=javascript>
				 alert('No hay nada que reportar!!!');
				 close();
			</script>
	 	<?php
		
	}
?>