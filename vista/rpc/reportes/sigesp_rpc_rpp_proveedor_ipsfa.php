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
		$io_pdf->line(50,40,950,40);
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],50,530,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(11,$as_titulo);
		$io_pdf->addText(430,540,11,$as_titulo); // Agregar el t?tulo
		$io_pdf->addText(906,560,8,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(912,553,7,date("h:i a")); // Agregar la Hora
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
	$io_pdf->ezSetY(520);
	$la_data =array(array('codigo'=>'<b>C?digo</b>',
	                        'nombre'=>'<b>Nombre</b>',
							'rif'=>'<b>RIF</b>',
							'direccion'=>'<b>Direcci?n</b>',
							'telefono'=>'<b>Tel?fono</b>',
							'especialidad'=>'<b>Especialidad</b>',
							'nivel'=>'<b>Nivel de Contrataci?n</b>',
							'calificacion'=>'<b>Calificaci?n de Contrataci?n</b>',
						    'capacidad'=>'<b>Capacidad Financiera</b>'));
	$la_columna=array('codigo'=>'','nombre'=>'','rif'=>'','direccion'=>'','telefono'=>'','especialidad'=>'','nivel'=>'',
	                  'calificacion'=>'','capacidad'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
	                 'fontSize' => 9,
					 'titleFontSize' =>10,  // Tama?o de Letras de los t?tulos
					 'showLines'=>1, // Mostrar L?neas
					 'shaded'=>0,
					 'shadeCol2'=>array(0.86,0.86,0.86),
					 'colGap'=>1,
					 'width'=>900, // Ancho de la tabla
					 'maxWidth'=>900, // Ancho M?ximo de la tabla
					 'xOrientation'=>'center',
					 'cols'=>array('codigo'=>array('justification'=>'center','width'=>55), // Justificaci?n y ancho de la columna
					 			   'nombre'=>array('justification'=>'center','width'=>175), // Justificaci?n y ancho de la columna
					 			   'rif'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
					 			   'direccion'=>array('justification'=>'center','width'=>180), // Justificaci?n y ancho de la columna
					 			   'telefono'=>array('justification'=>'center','width'=>80),
								   'especialidad'=>array('justification'=>'center','width'=>140),
								   'nivel'=>array('justification'=>'center','width'=>60),
								   'calificacion'=>array('justification'=>'center','width'=>60),
								   'capacidad'=>array('justification'=>'center','width'=>90)));								   
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
	$la_columna=array('codigo'=>'<b>C?digo</b>','nombre'=>'<b>Nombre</b>','rif'=>'<b>Rif</b>',
	                  'direccion'=>'<b>Direcci?n</b>','telefono'=>'<b>Tel?fono</b>','especialidad'=>'<b>Especialidad</b>',
					  'nivel'=>'<b>Nivel de Contrataci?n</b>','calificacion'=>'<b>Calificaci?n de Contrataci?n</b>',
					  'capacidad'=>'<b>Capacidad Financiera</b>');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
					 'fontSize' => 8, // Tama?o de Letras
					 'titleFontSize' => 10,  // Tama?o de Letras de los t?tulos
					 'showLines'=>0, // Mostrar L?neas
					 'shaded'=>0, // Sombra entre l?neas
					 'shadeCol'=>array(0.8,0.8,0.8),
					 'shadeCol2'=>array(0.9,0.9,0.9),
					 'width'=>900, // Ancho de la tabla
					 'maxWidth'=>900, // Ancho M?ximo de la tabla
					 'xOrientation'=>'center', // Orientaci?n de la tabla
					 'cols'=>array('codigo'=>array('justification'=>'center','width'=>55), // Justificaci?n y ancho de la columna
					 			   'nombre'=>array('justification'=>'left','width'=>175), // Justificaci?n y ancho de la columna
					 			   'rif'=>array('justification'=>'center','width'=>60), // Justificaci?n y ancho de la columna
					 			   'direccion'=>array('justification'=>'left','width'=>180), // Justificaci?n y ancho de la columna
					 			   'telefono'=>array('justification'=>'center','width'=>80),
								   'especialidad'=>array('justification'=>'center','width'=>140),
								   'nivel'=>array('justification'=>'center','width'=>60),
								   'calificacion'=>array('justification'=>'center','width'=>60),
								   'capacidad'=>array('justification'=>'right','width'=>90)));		
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
	$ls_titulo="<b>Listado de Proveedores</b>";
	//--------------------------------------------------  Par?metros para Filtar el Reporte  -----------------------------------------
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
	$lb_valido=true;
	$arrResultado=$io_report->uf_load_proveedores($ls_codemp,$li_orden,$ls_tipo,$ls_codprov1,$ls_codprov2,$ls_codesp,$lb_valido);
	$rs_proveedor=$arrResultado["rs_data"];
	$lb_valido=$arrResultado["lb_valido"];		
	if ($lb_valido)
    {
		set_time_limit(1800);
		$io_pdf=new Cezpdf('LEGAL','landscape'); // Instancia de la clase PDF
		$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
		$io_pdf->ezSetCmMargins(4.5,2.5,3,3); // Configuraci?n de los margenes en cent?metros
		$io_pdf = uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la p?gina
		$io_pdf->ezStartPageNumbers(950,50,9,'','',1); // Insertar el n?mero de p?gina
		$li_total=$io_sql->num_rows($rs_proveedor);
		$data=$io_sql->obtener_datos($rs_proveedor);
		for ($z=1;$z<=$li_total;$z++)
		{//1
		  $ls_codpro=$data["cod_pro"][$z];
		  $ls_nompro=$data["nompro"][$z];
		  $ls_rifpro=$data["rifpro"][$z];
		  $ls_dirpro=$data["dirpro"][$z];
		  $ls_telpro=$data["telpro"][$z];
		  $arrResultado ="";
		  $arrResultado = $io_report->uf_load_especialidadproveedor2($ls_codpro,$lb_valido); 
		  $ls_especialidad = $arrResultado["ls_especialidad"];
		  $lb_valido = $arrResultado["lb_valido"];					   
		  $arrResultado="";
		  $arrResultado = $io_report->uf_load_niveldecontratacionporproveedor($ls_codpro,$lb_valido); 
		  $ls_nivel = $arrResultado["ls_nivel"];
		  $lb_valido = $arrResultado["lb_valido"];		
		  $arrResultado ="";
		  $arrResultado =   $io_report->uf_load_niveldeclasificacionporproveedor($ls_codpro,$lb_valido); 
		  $ls_clas = $arrResultado["ls_clas"];
		  $lb_valido = $arrResultado["lb_valido"];		
		   
		  $ld_moncapfin = number_format($data["monfincon"][$z],2,",",".");//Capacidad Financiera.
		  
		  		  		  
  		  $la_data[$z]=array('codigo'=>$ls_codpro,'nombre'=>$ls_nompro,'rif'=>$ls_rifpro,'direccion'=>$ls_dirpro,
		                     'telefono'=>$ls_telpro,'especialidad'=>$ls_especialidad,'nivel'=>$ls_nivel,'calificacion'=>$ls_clas,'capacidad'=>$ld_moncapfin);
		}//1
		$io_pdf = uf_print_cabecera_detalle($io_pdf);
		$io_pdf = uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle 
		
		
//-----------------------------------------------MOSTRAR TOTAL---------------------------------------------------------------------------------//

    	
	$la_data =array(array('titulo'=>'','total'=>'' ));
	$la_data =array(array('titulo'=>'','total'=>''));
	$la_data =array(array('titulo'=>'<b>Total de Proveedores</b>','total'=>$li_total.'   ' ));
	$la_columna=array('titulo'=>'','total'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
	                 'fontSize' => 9,
					 'titleFontSize' =>10,  // Tama?o de Letras de los t?tulos
					 'showLines'=>0, // Mostrar L?neas
					 'shaded'=>0,
					 'shadeCol2'=>array(0.86,0.86,0.86),
					 'colGap'=>1,
					 'width'=>900, // Ancho de la tabla
					 'maxWidth'=>900, // Ancho M?ximo de la tabla
					 'xOrientation'=>'center',
					 'cols'=>array('titulo'=>array('justification'=>'right','width'=>830),
					 			   'total'=>array('justification'=>'right','width'=>70))); // Justificaci?n y ancho de la columna
					 			   								   
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);



//--------------------------------------------------------------------------------------------------------------------------------//		
		
		
		
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
	}
	else
	 {
		print("<script language=JavaScript>");
		print(" alert('No hay nada que Reportar');"); 
		print(" close();");
		print("</script>");
	 }
?> 