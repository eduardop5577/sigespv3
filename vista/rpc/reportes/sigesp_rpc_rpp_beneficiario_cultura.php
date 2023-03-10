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

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->line(20,40,578,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->rectangle(185,710,370,40);
		$io_pdf->line(400,750,400,710);
		$io_pdf->line(400,730,555,730);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],30,715,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$io_pdf->addText(225,725,11,$as_titulo); // Agregar el t?tulo
		$io_pdf->addText(430,735,10,"Fecha: ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(430,715,10,"Hora: ".date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_cabecera_detalle($io_pdf)
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Function: uf_print_cabecera_detalle
//		   Access: private 
//	    Arguments: la_data // arreglo de informaci?n
//	   			   io_pdf // Objeto PDF
//    Description: funci?n que imprime el detalle
//	   Creado Por: Ing. Yesenia Moreno
// Fecha Creaci?n: 21/04/2006 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->ezSetY(700);
	$la_data   =array(array('cedula'=>'<b>C?dula</b>','nombre'=>'<b>Nombre</b>','apellido'=>'<b>Apellido</b>','cuenta'=>'<b>Cuenta</b>'));
	$la_columna=array('cedula'=>'','nombre'=>'','apellido'=>'','cuenta'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 'titleFontSize' =>10,  // Tama?o de Letras de los t?tulos
					 'showLines'=>1, // Mostrar L?neas
					 'shaded'=>0,
					 'shadeCol2'=>array(0.86,0.86,0.86),
					 'colGap'=>1,
					 'width'=>520, // Ancho de la tabla
					 'maxWidth'=>520, // Ancho M?ximo de la tabla
					 'xPos'=>296, // Orientaci?n de la tabla
					 'cols'=>array('cedula'=>array('justification'=>'center','width'=>70),
								   'nombre'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
								   'apellido'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
								   'cuenta'=>array('justification'=>'center','width'=>90))); // Justificaci?n y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	return $io_pdf;	
}// end function uf_print_cabecera_detalle
//------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_columna=array('cedula'=>'','nombre'=>'','apellido'=>'','cuenta'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>520, // Ancho de la tabla
						 'maxWidth'=>520, // Ancho M?ximo de la tabla
						 'xPos'=>300, // Orientaci?n de la tabla
						 'cols'=>array('cedula'=>array('justification'=>'center','width'=>70), // Justificaci?n y ancho de la columna
						 			   'nombre'=>array('justification'=>'left','width'=>180), // Justificaci?n y ancho de la columna
						 			   'apellido'=>array('justification'=>'left','width'=>180), // Justificaci?n y ancho de la columna
						 			   'cuenta'=>array('justification'=>'center','width'=>90))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		return $io_pdf;		
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	$io_in=new sigesp_include();
	$con=$io_in->uf_conectar();	
	require_once("../../../modelo/servicio/rpc/reportes/sigesp_rpc_class_report.php");
	$io_report = new sigesp_rpc_class_report($con);
	require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql = new class_sql($con);
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
	$ls_titulo="<b>Listado de Beneficiarios</b>";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
	if (array_key_exists("hidorden",$_POST))
	   {
		 $li_orden=$_POST["hidorden"];
	   }
	else
	   {
		 $li_orden=$_GET["hidorden"];
	   }
	if (array_key_exists("hidcedula1",$_POST))
	   {
		 $ls_cedula1=$_POST["hidcedula1"];
	   }
	else
	   {
		 $ls_cedula1=$_GET["hidcedula1"];
	   }
	if (array_key_exists("hidcedula2",$_POST))
	   {
		 $ls_cedula2=$_POST["hidcedula2"];
	   }
	else
	   {
		 $ls_cedula2=$_GET["hidcedula2"];
	   }
	$lb_valido=true;
	$arrResultado=$io_report->uf_select_beneficiario($li_orden,$ls_cedula1,$ls_cedula2,$lb_valido);
	$rs_beneficiario=$arrResultado["rs_data"];
	$lb_valido = $arrResultado["lb_valido"];			
	if ($lb_valido)
	   {
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(3.8,3,3,3); // Configuraci?n de los margenes en cent?metros
		$io_pdf = uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		$li_total=$io_sql->num_rows($rs_beneficiario);
		$data=$io_sql->obtener_datos($rs_beneficiario);
		for ($z=1;$z<=$li_total;$z++)
			{//1
			  $ls_cedbene =$data["ced_bene"][$z];
			  $ls_nombene =$data["nombene"][$z];
			  $ls_apebene =$data["apebene"][$z];
			  $ls_cuenta  =$data["sc_cuenta"][$z];
  			  $la_data[$z]=array('cedula'=>$ls_cedbene,'nombre'=>$ls_nombene,'apellido'=>$ls_apebene,'cuenta'=>$ls_cuenta);
			}//4
		$io_pdf = uf_print_cabecera_detalle($io_pdf);
		$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		if($lb_valido) // Si no ocurrio ning?n error
		{
			$io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi?n de los n?meros de p?gina
			$io_pdf->ezStream(); // Mostramos el reporte
		}
		else  // Si hubo alg?n error
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
