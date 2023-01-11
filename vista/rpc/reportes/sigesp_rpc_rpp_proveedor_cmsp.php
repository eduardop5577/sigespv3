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
		//	    Arguments: as_titulo // T�tulo del Reporte
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci�n que imprime los encabezados por p�gina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->line(20,40,770,40);
		$io_pdf->rectangle(140,500,630,40);
		$io_pdf->line(600,540,600,500);
		$io_pdf->line(600,520,770,520);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],35,500,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(15,$as_titulo);
		$io_pdf->addText(280,515,15,$as_titulo); // Agregar el t�tulo
		$io_pdf->addText(620,525,10,"<b>Fecha:</b> ".date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(620,505,10,"<b>Hora:</b> ".date("h:i a")); // Agregar la hora
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		return $io_pdf;
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_detalle
	//		   Access: private 
	//	    Arguments: la_data // arreglo de informaci�n
	//	   			   io_pdf // Objeto PDF
	//    Description: funci�n que imprime el detalle
	//	   Creado Por: Ing. Yesenia Moreno
	// Fecha Creaci�n: 21/04/2006 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->ezSetY(480);
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
	
		$la_datatit = array(array('codigo'=>'<b>C�digo</b>','nombre'=>'<b>Nombre</b>','direccion'=>'<b>Direccion</b>',
		                          'telefono'=>'<b>Tel�fono</b>','rif'=>'<b>RIF</b>','capital'=>'<b>Capital Social Suscrito</b>',
								  'repleg'=>'<b>Representante Legal</b>','numreg'=>'<b>N� RNC</b>','scgcta'=>'<b>Contable</b>',
								  'especialidad'=>'<b>Especialidad</b>'));
		$la_columna = array('codigo'=>'','nombre'=>'','direccion'=>'','telefono'=>'','rif'=>'','capital'=>'',
							'repleg'=>'','numreg'=>'','scgcta'=>'','especialidad'=>'');
		$la_config  = array('showHeadings'=>0, // Mostrar encabezados
							'titleFontSize' =>8,  // Tama�o de Letras de los t�tulos
							'showLines'=>1, // Mostrar L�neas
							'shaded'=>0,
							'shadeCol2'=>array(0.86,0.86,0.86),
							'width'=>750, // Ancho de la tabla
							'maxWidth'=>750, // Ancho M�ximo de la tabla
							'xPos'=>405, // Orientaci�n de la tabla
							'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
										  'nombre'=>array('justification'=>'center','width'=>110), // Justificaci�n y ancho de la columna
										  'direccion'=>array('justification'=>'center','width'=>110), // Justificaci�n y ancho de la columna
										  'telefono'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
										  'rif'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
										  'capital'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
										  'repleg'=>array('justification'=>'center','width'=>90), // Justificaci�n y ancho de la columna
										  'numreg'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
										  'scgcta'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
										  'especialidad'=>array('justification'=>'center','width'=>90)));								   
		$io_pdf->ezTable($la_datatit,$la_columna,'',$la_config);	
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	
		$la_columna=array('codigo'=>'','nombre'=>'','direccion'=>'','telefono'=>'','rif'=>'','capital'=>'',
							'repleg'=>'','numreg'=>'','scgcta'=>'','especialidad'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize'=>7, // Tama�o de Letras
						 'titleFontSize'=>9,  // Tama�o de Letras de los t�tulos
						 'showLines'=>1, // Mostrar L�neas
						 'shaded'=>0, // Sombra entre l�neas
						 'shadeCol'=>array(0.8,0.8,0.8),
						 'shadeCol2'=>array(0.9,0.9,0.9),
						 'width'=>750, // Ancho de la tabla
						 'maxWidth'=>750, // Ancho M�ximo de la tabla
						 'xPos'=>405, // Orientaci�n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>50), // Justificaci�n y ancho de la columna
									   'nombre'=>array('justification'=>'left','width'=>110), // Justificaci�n y ancho de la columna
									   'direccion'=>array('justification'=>'left','width'=>110), // Justificaci�n y ancho de la columna
									   'telefono'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
									   'rif'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
									   'capital'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
									   'repleg'=>array('justification'=>'left','width'=>90), // Justificaci�n y ancho de la columna
									   'numreg'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
									   'scgcta'=>array('justification'=>'center','width'=>60), // Justificaci�n y ancho de la columna
									   'especialidad'=>array('justification'=>'left','width'=>90))); // Justificaci�n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);	
		return $io_pdf;		
	}// end function uf_print_detalle
	
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	
	require_once("../../../modelo/servicio/rpc/reportes/sigesp_rpc_class_report.php");
	require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
	$io_in     = new sigesp_include();
	$con       = $io_in->uf_conectar();	
	$io_report = new sigesp_rpc_class_report($con);
	$io_sql    = new class_sql($con);
	
	//--------------------------------------------------  Par�metros para Filtar el Reporte  -----------------------------------------
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	if (array_key_exists("hidtipo",$_POST))
	   {
		 $ls_tipo=$_POST["hidtipo"];
	   }
	else
	   {
		 $ls_tipo=$_GET["hidtipo"];
	   }
	if (array_key_exists("hidorden",$_POST))
	   {
		 $li_orden=$_POST["hidorden"];
	   }
	else
	   {
		 $li_orden=$_GET["hidorden"];
	   }
	if (array_key_exists("hidcodprov1",$_POST))
	   {
		 $ls_codprov1=$_POST["hidcodprov1"];
	   }
	else
	   {
		 $ls_codprov1=$_GET["hidcodprov1"];
	   }
	if (array_key_exists("hidcodprov2",$_POST))
	   {
		 $ls_codprov2=$_POST["hidcodprov2"];
	   }
	else
	   {
		 $ls_codprov2=$_GET["hidcodprov2"];
	   }
	if (array_key_exists("hidcodesp",$_POST))
	   {
		 $ls_codesp=$_POST["hidcodesp"];
	   }
	else
	   {
		 $ls_codesp=$_GET["hidcodesp"];
	   }
	//----------------------------------------------------  Par�metros del encabezado  -----------------------------------------------
	if($ls_tipo=="C")
	{$ls_titulo="<b>Listado de Contratistas</b>";}
	else
	{$ls_titulo="<b>Listado de Proveedores</b>";}
	//---------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=true;
	$arrResultado=$io_report->uf_load_proveedores($ls_codemp,$li_orden,$ls_tipo,$ls_codprov1,$ls_codprov2,$ls_codesp,$lb_valido);
	$rs_proveedor=$arrResultado["rs_data"];
	$lb_valido=$arrResultado["lb_valido"];		
	if ($lb_valido)
    {
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LETTER','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(5.2,3,3,3); // Configuraci�n de los margenes en cent�metros
		$io_pdf = uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p�gina
		$io_pdf->ezStartPageNumbers(740,25,10,'','',1); // Insertar el n�mero de p�gina
		$li_total = $io_sql->num_rows($rs_proveedor);
		if ($li_total>0)
		   {
		     $z = 0;
			 while($row=$io_sql->fetch_row($rs_proveedor))
			      {
				    $z++;
					$ls_codpro   = $row["cod_pro"];
					$ls_nompro   = $row["nompro"];
					$ls_dirpro   = $row["dirpro"];
					$ls_rifpro   = $row["rifpro"];
					$ls_nitpro   = $row["nitpro"];
					$ls_telpro   = $row["telpro"];
					$ls_scgcta   = $row["sc_cuenta"];
					$ls_capital  = $row["capital"];
					$ls_nomreppro= "";//$row["nomreppro"];
					$ls_nro_reg  = $row["ocei_no_reg"];
					$arrResultado ="";
					$arrResultado=$io_report->uf_load_especialidadproveedor($ls_codpro,$lb_valido);
					$ls_denesp = $arrResultado["ls_especialidad"];
					$lb_valido = $arrResultado["lb_valido"];					   			   
					$la_data[$z] = array('codigo'=>$ls_codpro,'nombre'=>$ls_nompro,'direccion'=>$ls_dirpro,'telefono'=>$ls_telpro,
					                     'rif'=>$ls_rifpro,'capital'=>$ls_capital,'repleg'=>$ls_nomreppro,'numreg'=>$ls_nro_reg,
										 'scgcta'=>$ls_scgcta,'especialidad'=>$ls_denesp);
				  }
		   }
		$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		if ($lb_valido) // Si no ocurrio ning�n error
		   {
			 $io_pdf->ezStopPageNumbers(1,1); // Detenemos la impresi�n de los n�meros de p�gina
			 $io_pdf->ezStream(); // Mostramos el reporte
		   }
		else  // Si hubo alg�n error
		{
			print("<script language=JavaScript>");
			print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
			print(" close();");
			print("</script>");		
		}
		unset($io_pdf);			
	}
	else
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
?> 