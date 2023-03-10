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
	//	    Arguments: as_titulo // T?tulo del Reporte
	//	    		   io_pdf // Instancia de objeto pdf
	//    Description: funci?n que imprime los encabezados por p?gina
	//	   Creado Por: Ing. Yesenia Moreno
	// Fecha Creaci?n: 21/04/2006 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->setStrokeColor(0,0,0);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,715,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
	$li_tm=$io_pdf->getTextWidth(14,$as_titulo);
	$io_pdf->addText(230,680,14,$as_titulo); // Agregar el t?tulo
	$io_pdf->addText(470,735,10,"Fecha: ".date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(476,715,10,"Hora: ".date("h:i a")); // Agregar la hora
    $io_pdf->addText(43,650,9,'<b> LA DIVISI?N DE COMPRAS Y SUMINISTROS DE LA ALCALDIA DEL MUNICIPIO PALAVECINO DEL ESTADO LARA,</b>');
    $io_pdf->addText(43,640,9,'<b> UNIDAD RESPONSABLE DEL REGISTRO Y CONTROL DE CONTRATISTAS Y PROVEEDORES, HACE CONSTAR QUE,</b>');
	$io_pdf->addText(43,630,9,'<b> LA EMPRESA:</b>');
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
	return $io_pdf;
}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_registronacional($ls_fecreg,$ls_numreg,$ls_fecvenrnc,$io_pdf)
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Function: uf_print_registronacional
//		   Access: private 
//	    Arguments: as_codper // total de registros que va a tener el reporte
//	    		   as_nomper // total de registros que va a tener el reporte
//	    		   io_pdf    // total de registros que va a tener el reporte
//    Description: funci?n que imprime la cabecera de cada p?gina
//	   Creado Por: Ing. Yesenia Moreno
// Fecha Creaci?n: 21/04/2006 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$la_datos[0]= array('titulo'=>'<b>REGISTRO NACIONAL DE CONTRATISTA</b>');
	$la_columna = array('titulo'=>'');
	$la_config  = array('showHeadings'=>0,     // Mostrar encabezados
					    'fontSize' => 10,       // Tama?o de Letras
					    'titleFontSize' => 5, // Tama?o de Letras de los t?tulos
					    'showLines'=>2,        // Mostrar L?neas
					    'shaded'=>0,           // Sombra entre l?neas
					    'xOrientation'=>'center', // Orientaci?n de la tabla
					    'width'=>520, // Ancho de la tabla
					    'maxWidth'=>520,
						'xPos'=>300,
					    'cols'=>array('titulo'=>array('justification'=>'center','width'=>520))); // Justificaci?n y ancho de la columna
	$io_pdf->ezSetY(473);
	$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
	unset($la_datos);
	unset($la_columna);
	unset($la_config);
	$la_datos[1]=array('inscripcion'=>'<b>INSCRIPCION:  </b>'.$ls_fecreg,
	                   'registro'=>'<b>N? DE REGISTRO:  </b>'.$ls_numreg,
					   'vencimiento'=>'<b>VENCIMIENTO:  </b>'.$ls_fecvenrnc);
	$la_columna = array('inscripcion'=>'','registro'=>'','vencimiento'=>'');
	$la_config  = array('showHeadings'=>0,     // Mostrar encabezados
					    'fontSize' => 10,       // Tama?o de Letras
					    'titleFontSize' => 5, // Tama?o de Letras de los t?tulos
					    'showLines'=>1,        // Mostrar L?neas
					    'shaded'=>0,           // Sombra entre l?neas
					    'xOrientation'=>'center', // Orientaci?n de la tabla
					    'width'=>520, // Ancho de la tabla
					    'maxWidth'=>520,
						'xPos'=>300,
					    'cols'=>array('inscripcion'=>array('justification'=>'left','width'=>150), // Justificaci?n y ancho de la columna Nro de Operacion.
								      'registro'=>array('justification'=>'left','width'=>220), // Justificaci?n y ancho de la columna
								      'vencimiento'=>array('justification'=>'left','width'=>150))); // Justificaci?n y ancho de la columna
	$io_pdf->ezSetY(457.5);
	$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
	return $io_pdf;	
}// end function uf_print_registronacional
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_documentos($la_data,$io_pdf)
{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//       Function: uf_print_documentos
//		   Access: private 
//	    Arguments: as_codper // total de registros que va a tener el reporte
//	    		   as_nomper // total de registros que va a tener el reporte
//	    		   io_pdf    // total de registros que va a tener el reporte
//    Description: funci?n que imprime la cabecera de cada p?gina
//	   Creado Por: Ing. Yesenia Moreno
// Fecha Creaci?n: 21/04/2006 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$la_datos[0]= array('titulo'=>'<b>DOCUMENTOS CONSIGNADOS</b>');
	$la_columna = array('titulo'=>'');
	$la_config  = array('showHeadings'=>0,     // Mostrar encabezados
					    'fontSize' => 10,       // Tama?o de Letras
					    'titleFontSize' => 5, // Tama?o de Letras de los t?tulos
					    'showLines'=>2,        // Mostrar L?neas
					    'shaded'=>0,           // Sombra entre l?neas
					    'xOrientation'=>'center', // Orientaci?n de la tabla
					    'width'=>520, // Ancho de la tabla
					    'maxWidth'=>520,
						'xPos'=>300,
					    'cols'=>array('titulo'=>array('justification'=>'center','width'=>520))); // Justificaci?n y ancho de la columna
	$io_pdf->ezSetY(437);
	$io_pdf->ezTable($la_datos,$la_columna,'',$la_config);	
	unset($la_datos);
	unset($la_columna);
	unset($la_config);
	$la_columna = array('denominacion'=>'                                  <b>Denominaci?n</b>','recepcion'=>'<b>Fecha Recepci?n</b>','vencimiento'=>'<b>Fecha Vencimiento o  Plazo de Entrega</b>','estatus'=>'<b>Estatus</b>');
	$la_config  = array('showHeadings'=>1,     // Mostrar encabezados
					    'fontSize' => 10,       // Tama?o de Letras
					    'titleFontSize' => 5, // Tama?o de Letras de los t?tulos
					    'showLines'=>2,        // Mostrar L?neas
					    'shaded'=>0,           // Sombra entre l?neas
					    'xOrientation'=>'center', // Orientaci?n de la tabla
					    'width'=>520, // Ancho de la tabla
					    'maxWidth'=>520,
						'xPos'=>300,
					    'cols'=>array('denominacion'=>array('justification'=>'left','width'=>270), // Justificaci?n y ancho de la columna Nro de Operacion.
								      'recepcion'=>array('justification'=>'center','width'=>80), // Justificaci?n y ancho de la columna
								      'vencimiento'=>array('justification'=>'center','width'=>90), // Justificaci?n y ancho de la columna
								      'estatus'=>array('justification'=>'center','width'=>80))); // Justificaci?n y ancho de la columna
	$io_pdf->ezSetY(419);
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	return $io_pdf;		
}// end function uf_print_documentos
//--------------------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
    require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_in      = new sigesp_include();
	$con        = $io_in->uf_conectar();	
	$io_sql     = new class_sql($con);
	$io_funcion = new class_funciones();
	$ls_tiporeporte=0;
	if (array_key_exists("tiporeporte",$_GET))
	{
		$ls_tiporeporte=$_GET["tiporeporte"];
	}
	switch($ls_tiporeporte)
	{
		case "0":
			require_once("../../../modelo/servicio/rpc/reportes/sigesp_rpc_class_report.php");
			$io_report  = new sigesp_rpc_class_report($con);
			break;

		case "1":
			require_once("sigesp_rpc_class_reportbsf.php");
			$io_report  = new sigesp_rpc_class_reportbsf($con);
			break;
	}
	//----------------------------------------------------  Par?metros del encabezado  -----------------------------------------------
		$ls_titulo = "<b>Certificado de Inscripcion</b>";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
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
	if (array_key_exists("total",$_POST))
	   {
		 $li_total = $_POST["total"];
	   }
	else
	   {
		 $li_total = $_GET["total"];
	   }
	$la_documentos[0]="";
	if($li_total>0)
	{
		for($li_i=1;$li_i<=$li_total;$li_i++)
		{
			$la_documentos[$li_i]=$_GET["coddoc".$li_i];
		}
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
		 $io_pdf->ezSetCmMargins(3.8,3,3,3); // Configuraci?n de los margenes en cent?metros
		 $io_pdf = uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p?gina
		 $io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el n?mero de p?gina
		 for ($z=1;$z<=$li_total;$z++)
			 {
				   $ls_codpro     = $data["cod_pro"][$z];
			   $ls_nompro     = $data["nompro"][$z];
			   $ls_dirpro     = $data["dirpro"][$z];
			   $ls_rifpro     = $data["rifpro"][$z];
			   $ls_telpro     = $data["telpro"][$z];
			   $ls_faxpro     = $data["faxpro"][$z];
			   $ls_nomrep     = $data["nomreppro"][$z];
			   $ls_carrep     = $data["carrep"][$z];
			   $ld_capital    = $data["capital"][$z];
			   $ls_cuenta     = $data["sc_cuenta"][$z];
  			   $ls_fecregmod  = $data["fecregmod"][$z];
			   $ls_codesp     = $data["codesp"][$z];
			   $ls_nitpro     = $data["nitpro"][$z];
			   $ls_codpai     = $data["codpai"][$z];
			   $ls_estpro     = $data["estado"][$z];
			   $ls_numreg     = $data["ocei_no_reg"][$z];
			   $ls_fecvenrnc  = $data["fecvenrnc"][$z];
			   $ls_fecreg     = $data["ocei_fec_reg"][$z];
			   $ls_nompai     = $data["pais"][$z];
			   $arrResultado="";
			   $arrResultado     = $io_report->uf_load_especialidadproveedor($ls_codpro,$lb_valido) ;
			   $ls_denesp = $arrResultado["ls_especialidad"];
			   $lb_valido = $arrResultado["lb_valido"];					   
			   $ls_fecvenrnc  = $io_funcion->uf_convertirfecmostrar($ls_fecvenrnc);
			   $ls_fecreg     = $io_funcion->uf_convertirfecmostrar($ls_fecreg);
			  $io_pdf->addText(43,610,16,'<b>'.$ls_nompro.'</b>'); 
		       	  $io_pdf->addText(43,598,9,'Domiciliada en:');
			  $ls_dirpro = $io_pdf->addTextWrap(108,598,400,9,'<b>'.$ls_dirpro.'</b>');
			  $io_pdf->addText(42,586,10,' Con un capital de:');
			  $io_pdf->addText(138,586,10,'<b>'.$ld_capital.'</b>');
			  $io_pdf->addText(43,561,10,'Telefono:');
			  $io_pdf->addText(99,561,10,'<b>'.$ls_telpro.'</b>');
			  $io_pdf->addText(43,575,10,'Representado Legalmente por:');
			  $io_pdf->addText(192,575,10,'<b>'.$ls_nomrep.'</b>'); 
			  $io_pdf->addText(43,547,10,'RIF:');
			  $io_pdf->addText(65,547,10,'<b>'.$ls_rifpro.'</b>');
			  $io_pdf->addText(43,535,10,'Ha sido inscrita en esta Divisi?n el:');
			  $io_pdf->addText(215,535,10,'<b>'.$ls_fecregmod.'</b>');
			  $io_pdf->addText(43,523,10,'Bajo el c?digo de registro n?mero:');
			  $io_pdf->addText(210,523,10,'<b>'.$ls_codpro.'</b>');
			  $io_pdf->addText(43,505,10,'<b>PRESENTACION, REVISION Y ADMISION CONFORME DE LOS DOCUMENTOS Y RECAUDOS EXIGIDOS:</b>');
              $io_pdf->line(60,135,260,135);
              $io_pdf->line(300,135,500,135);
              $io_pdf->addText(110,125,9,'<b>'."JEFE DE LA DIVISION".'</b>');
              $io_pdf->addText(345,125,9,'<b>'."REPRESENTANTE LEGAL".'</b>');
			  $io_pdf->addText(33,100,9,'<b>'."NOTA:".'</b>');
              $io_pdf->addText(68,100,8,'<b>'."CERTIFICADO EXPEDIDO EN BASE A DATOS APORTADOS POR EL SOLICITANTE, VALIDO HASTA EL:  ____________".'</b>');
			  $io_pdf = uf_print_registronacional($ls_fecreg,$ls_numreg,$ls_fecvenrnc,$io_pdf);
			  $arrResultado ="";
			  $lb_existe = "";
			  $arrResultado = $io_report->uf_select_documentosproveedores($ls_codemp,$ls_codpro,$la_documentos,"0",$lb_existe);
			  $rs_documentos = $arrResultado["rs_data"];
			  $lb_existe = $arrResultado["lb_valido"];		 
			   if($lb_existe)
			   {
			   		$li_documentos=$io_sql->num_rows($rs_documentos);
					$documentos=$io_sql->obtener_datos($rs_documentos);
					for($li_i=1;$li_i<=$li_documentos;$li_i++)
				    {
						$ls_dendoc= $documentos["dendoc"][$li_i];
						$ls_estdoc= $documentos["estdoc"][$li_i];
						switch($ls_estdoc)
						{
							case "0":
								$ls_estatus="No Entregado";
								break;
							case "1":
								$ls_estatus="Entregado";
								break;
							case "2":
								$ls_estatus="En Tr?mite";
								break;
							case "3":
								$ls_estatus="No Aplica";
								break;
						}
						$ld_fecrecdoc= $documentos["fecrecdoc"][$li_i];
						$ld_fecvendoc= $documentos["fecvendoc"][$li_i];
					    $ld_fecrecdoc  = $io_funcion->uf_convertirfecmostrar($ld_fecrecdoc);
					    $ld_fecvendoc     = $io_funcion->uf_convertirfecmostrar($ld_fecvendoc);
						$la_data[$li_i]=array('denominacion'=>$ls_dendoc,'recepcion'=>$ld_fecrecdoc,'vencimiento'=>$ld_fecvendoc,'estatus'=>$ls_estatus);
					}
					$io_pdf = uf_print_documentos($la_data,$io_pdf);
			   }
			   if ($z<$li_total)
			      {$io_pdf->ezNewPage();}
 		    }
		if ($lb_valido) // Si no ocurrio ning?n error
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
