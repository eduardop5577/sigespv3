<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
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
	ini_set('memory_limit','512M');
	ini_set('max_execution_time ','0');
//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezadopagina
	//		   Access: private 
	//	    Arguments: as_titulo // Título del Reporte
	//	    		   io_pdf // Instancia de objeto pdf
	//    Description: función que imprime los encabezados por página
	//	   Creado Por: Ing. Yesenia Moreno
	// Fecha Creación: 21/04/2006 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_nomemp  = $_SESSION["la_empresa"]["nombre"];
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->line(20,40,578,40);
	$io_pdf->setStrokeColor(0,0,0);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,715,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(14,$as_titulo);
	$io_pdf->addText(230,725,14,$as_titulo); // Agregar el título
	$io_pdf->addText(470,735,10,"Fecha: ".date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(476,715,10,"Hora: ".date("h:i a")); // Agregar la hora
	$li_tm=$io_pdf->getTextWidth(10,$ls_nomemp);
	$tm=296-($li_tm/2);
	$io_pdf->addText($tm,710,10,$ls_nomemp); // Agregar el título
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	return $io_pdf;
}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_constancia($as_constancia,$io_pdf)
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Function: uf_print_constancia
//		   Access: private 
//	    Arguments: la_data // arreglo de información
//	   			   io_pdf // Objeto PDF
//    Description: función que imprime el detalle
//	   Creado Por: Ing. Yesenia Moreno
// Fecha Creación: 21/04/2006 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$io_pdf->ezSetY(665);
	$la_data    = array(array('constancia'=>$as_constancia));
	$la_columna = array('constancia'=>'');
	$la_config  = array('showHeadings'=>0,
					    'titleFontSize' =>10,
					    'showLines'=>0, 
					    'shaded'=>0,
					    'shadeCol2'=>array(0.86,0.86,0.86),
					    'colGap'=>1,
					    'width'=>520, 
					    'maxWidth'=>520, 
					    'xPos'=>300,
					    'cols'=>array('constancia'=>array('justification'=>'left','width'=>520)));
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	return $io_pdf;	
}// end function uf_print_cabecera_detalle
//------------------------------------------------------------------------------------------------------------------------------



	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
    require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_in      = new sigesp_include();
	$con        = $io_in->uf_conectar();	
	$io_sql     = new class_sql($con);
	$io_funcion = new class_funciones();
	$ls_tiporeporte="0";
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("../../../modelo/servicio/rpc/reportes/sigesp_rpc_class_report.php");
			$io_report  = new sigesp_rpc_class_report($con);
			$ls_bolivares ="Bs.";
			break;

		case "1":
			require_once("sigesp_rpc_class_reportbsf.php");
			$io_report  = new sigesp_rpc_class_reportbsf($con);
			$ls_bolivares ="Bs.F.";
			break;
	}
	//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
	$ls_titulo="<b>FICHA DE PROVEEDOR</b>";
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	if (array_key_exists("hidorden",$_POST))
	   {
		 $li_orden=$_POST["hidorden"];
	   }
	else
	   {
		 $li_orden=$_GET["hidorden"];
	   }
	if (array_key_exists("hidcodproben1",$_POST))
	   {
		 $ls_codproben1 = $_POST["hidcodproben1"];
	   }
	else
	   {
		 $ls_codproben1=$_GET["hidcodproben1"];
	   }
	if (array_key_exists("hidcodproben2",$_POST))
	   {
		 $ls_codproben2 = $_POST["hidcodproben2"];
	   }
	else
	   {
		 $ls_codproben2 = $_GET["hidcodproben2"];
	   }
    $lb_valido  = true;
	$la_empresa = $_SESSION["la_empresa"];
	$ls_codemp  = $la_empresa["codemp"];
    $arrResultado    = $io_report->uf_select_proveedores($ls_codemp,$li_orden,$ls_codproben1,$ls_codproben2,$lb_valido);
	$rs_data = $arrResultado["rs_data"];
	$lb_valido = $arrResultado["lb_valido"];		
	if ($lb_valido)
	   {
		 set_time_limit(1800);
		 $li_total=$io_sql->num_rows($rs_data);
		 $data=$io_sql->obtener_datos($rs_data);
		 $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		 $io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		 $io_pdf->ezSetCmMargins(3.8,3,3,3); // Configuración de los margenes en centímetros
		 $io_pdf = uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
		 $io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
		 for ($z=1;$z<=$li_total;$z++)
			 {
			   $ls_codpro     = $data["cod_pro"][$z];
			   $ls_nompro     = $data["nompro"][$z];
			   $ls_dirpro     = $data["dirpro"][$z];
			   $ls_rifpro     = $data["rifpro"][$z];
			   $ls_telpro     = $data["telpro"][$z];
			   $ls_nomrep     = $data["nomreppro"][$z];
			   $ld_capital    = $data["capital"][$z];
			   $ls_cuenta     = $data["sc_cuenta"][$z];
  			   $ls_fecreg     = $data["fecreg"][$z];
			   $ls_fecreg     = $io_funcion->uf_convertirfecmostrar($ls_fecreg);
			   $ls_constancia = 'Domiciliada en: <b>'.$ls_dirpro.'</b>'; 
			   $io_pdf = uf_print_constancia($ls_constancia,$io_pdf);
			   $ls_constancia="";
		       $io_pdf->addText(40,670,16,'<b>'.$ls_nompro.'</b>'); 
		       $io_pdf->addText(40,630,10,"Con un capital de ".$ls_bolivares.":"); 
	 	       $io_pdf->addText(150,630,10,'<b>'.number_format($ld_capital,2,',','.').'</b>'); 
			   $io_pdf->addText(40,610,10,"Representada legalmente por:");
		       $io_pdf->addText(185,610,10,'<b>'.$ls_nomrep.'</b>'); 
			   $io_pdf->addText(40,590,10,"Teléfono:"); 
		       $io_pdf->addText(90,590,10,'<b>'.$ls_telpro.'</b>'); 
			   $io_pdf->addText(40,570,10,"RIF:"); 
		       $io_pdf->addText(65,570,10,'<b>'.$ls_rifpro.'</b>');
               $io_pdf->addText(40,550,10,'<b>'."PRESENTACION, REVISION Y ADMISION CONFORME DE LOS DOCUMENTOS Y RECAUDOS EXIGIDOS".'</b>'); // Agregar el título
               $io_pdf->line(140,175,500,175);
			   $io_pdf->addText(230,160,10,'<b>'."".'</b>'); // Agregar el título
			   $io_pdf->addText(190,150,10,'<b>'."".'</b>'); // Agregar el título
			   $io_pdf->addText(20,80,10,'<b>'."NOTA:".'</b>'." "."CERTIFICADO EXPEDIDO EN BASE A DATOS APORTADOS POR EL SOLICITANTE"); // Agregar el título
			   if ($z<$li_total)
			      {$io_pdf->ezNewPage();}
 		    }
		if ($lb_valido) // Si no ocurrio ningún error
		   {
			 $io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresión de los números de página
			 $io_pdf->ezStream(); // Mostramos el reporte
	  	   }
		else  // Si hubo algún error
		   {
			 print("<script language=JavaScript>");
			 print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			 print(" close();");
			 print("</script>");		
		   }
		unset($io_pdf);			
	}//1
	else
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
?> 
