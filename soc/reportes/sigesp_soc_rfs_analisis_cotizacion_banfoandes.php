<?PHP
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
	ini_set('memory_limit','1024M');
 	ini_set('max_execution_time ','0');  
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");//Teleferico
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();"	;	
		print "close();";
		print "</script>";		
	}	
	//---------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 25/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_compra;
		$ls_descripcion="Gener? el Reporte An?lisis de Cotizaci?n";
		$lb_valido=$io_fun_compra->uf_load_seguridad_reporte("SOC","sigesp_soc_p_analisis_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_numanacot,$observacion,$ad_fecha,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime el banner del reporte
		//	   Creado Por: Ing. Laura Cabr?
		// Fecha Creaci?n: 17/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
        $io_encabezado=$io_pdf->openObject();
        $io_pdf->saveState();
        $io_pdf->setStrokeColor(0,0,0); 
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,535,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
        $ls_titulo="<b>An?lisis de Cotizaciones</b>";
        $li_tm=$io_pdf->getTextWidth(13,$ls_titulo);
        $tm=296-($li_tm/2);
        $io_pdf->addText($tm,535,13,$ls_titulo); // Agregar el t?tulo
		$io_pdf->addText(640,535,10," $ad_fecha");		
        $io_pdf->addText(25,510,11," <b>No. : </b>".$as_numanacot); // Agregar el t?tulo
        $io_pdf->addText(25,490,11,"<b>Observaci?n: </b>".$observacion); // Agregar el t?tulo
        
        $io_pdf->Rectangle(35,40,740,70); 
        $io_pdf->line(35,75,775,75);                                                //HORIZONTAL    
        $io_pdf->addText(110,102,7,"Analista de Compras"); 
        $io_pdf->addText(370,102,7,"Jefe de Compras"); 
        $io_pdf->addText(600,102,7,"Gte. de Servicios Administrativos"); 
        $io_pdf->addText(105,68,7,"Vicep. de Administraci?n"); 
        $io_pdf->addText(370,68,7,"Vicep. Ejecutivo"); 
        $io_pdf->addText(635,68,7,"Presidente"); 
        $io_pdf->addText(75,63,7,"");  
        $io_pdf->line(260,40,260,110);    
        $io_pdf->addText(250,72,7,""); 
        $io_pdf->line(540,40,540,110); 
        $io_pdf->restoreState();
        $io_pdf->closeObject();
        $io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_proveedores($la_cotizaciones,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_proveedores
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime el el listado de  proveedores participantes
		//	   Creado Por: Ing. Laura Cabr?
		// Fecha Creaci?n: 18/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;		
		global $io_pdf;
		
		$li_totalcotizaciones=count((array)$la_cotizaciones);
		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Cotizaciones</b>";
		$la_anchos_col = array(260);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 1,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(200,200,200),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-2);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	//primera fila del item, color gris	
		
		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Nro. Solicitud</b>";
		$la_data[0]["2"]="<b>Nro. Cotizaci?n</b>";
		$la_data[0]["3"]="<b>Proveedor</b>";
		$la_data[0]["4"]="<b>Fecha</b>";
		$la_data[0]["5"]="<b>Monto Total ".$ls_bolivares."</b>";
		$la_data[0]["6"]="<b>I.V.A. ".$ls_bolivares."</b>";

		$la_anchos_col = array(33,33,98,28,43,25);
		$la_justificaciones = array("center","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	
		
		//Imprimiendo columnas
		$la_data=array();
		for($li_i=0;$li_i<$li_totalcotizaciones;$li_i++)
		{
			$la_data[$li_i]["1"]=$la_cotizaciones[$li_i+1]["numsolcot"];
			$la_data[$li_i]["2"]=$la_cotizaciones[$li_i+1]["numcot"];
			$la_data[$li_i]["3"]=$la_cotizaciones[$li_i+1]["nompro"];
			$la_data[$li_i]["4"]=$io_funciones->uf_convertirfecmostrar($la_cotizaciones[$li_i+1]["feccot"]);
			$la_data[$li_i]["5"]=number_format($la_cotizaciones[$li_i+1]["montotcot"],2,",",".");
			$la_data[$li_i]["6"]=number_format($la_cotizaciones[$li_i+1]["poriva"],2,",",".");
		}
	
			$la_justificaciones=array();
			$la_justificaciones = array("center","center","left","center","right","right");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => $la_anchos_col,
								   "tamano_texto"  => 10,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
									   "grosor_lineas_externas"=>0.5,
									   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(5,$la_data,$la_opciones);	
	}//fin de uf_print_proveedores
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_items($as_tipsolcot,$la_items,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime los items del analisis de cotizacion y su respectivo proveedor
		//	   Creado Por: Ing. Laura Cabr?
		// Fecha Creaci?n: 17/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;		
		global $io_pdf;
		
		$li_totalitems=count((array)$la_items);
		if($as_tipsolcot=="B")
			$ls_item="Bienes";
		else
			$ls_item="Servicios";
		
		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>$ls_item</b>";
		$la_anchos_col = array(260);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 1,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(200,200,200),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-5);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	//primera fila del item, color gris	
		
		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>C?digo</b>";
		$la_data[0]["2"]="<b>Descripcion</b>";
		$la_data[0]["3"]="<b>Proveedor</b>";
		$la_data[0]["4"]="<b>Cant.</b>";
		$la_data[0]["5"]="<b>Precio/Unid. ".$ls_bolivares."</b>";
		$la_data[0]["6"]="<b>I.V.A. ".$ls_bolivares."</b>";
		$la_data[0]["7"]="<b>Monto Total ".$ls_bolivares."</b>";
		$la_data[0]["8"]="<b>Observaci?n</b>";

		$la_anchos_col = array(25,33,37,17,32,32,32,52);
		$la_justificaciones = array("center","center","center","center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	
		
		//Imprimiendo columnas
		$la_data=array();
		$li_totalprecio=0;
		$li_totaliva=0;
		$li_totalmonto=0;
		for($li_i=0;$li_i<$li_totalitems;$li_i++)
		{
			$la_data[$li_i]["1"]=$la_items[$li_i+1]["codigo"];
			$la_data[$li_i]["2"]=$la_items[$li_i+1]["denominacion"];
			$la_data[$li_i]["3"]=$la_items[$li_i+1]["nompro"];
			$la_data[$li_i]["4"]=number_format($la_items[$li_i+1]["cantidad"],2,",",".");
			$la_data[$li_i]["5"]=number_format($la_items[$li_i+1]["precio"],2,",",".");
			$la_data[$li_i]["6"]=number_format($la_items[$li_i+1]["moniva"],2,",",".");
			$la_data[$li_i]["7"]=number_format($la_items[$li_i+1]["monto"],2,",",".");
			$la_data[$li_i]["8"]=$la_items[$li_i+1]["obsanacot"];
			$li_totalprecio+=$la_items[$li_i+1]["precio"];
			$li_totaliva+=$la_items[$li_i+1]["moniva"];
			$li_totalmonto+=$la_items[$li_i+1]["monto"];
		}
	
			$la_justificaciones=array();
			$la_justificaciones = array("center","left","left","right","right","right","right","left");
			$la_opciones = array("color_texto"     => array(0,0,0),
								   "anchos_col"    => $la_anchos_col,
								   "tamano_texto"  => 9,
								   "lineas"        => 2,
								   "alineacion_col"=> $la_justificaciones,
									   "grosor_lineas_externas"=>0.5,
									   "grosor_lineas_internas"=>0.5);
			$io_pdf->add_tabla(5,$la_data,$la_opciones);	
		
		//imprimiendo totales
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Totales ".$ls_bolivares."</b>";
		$la_data[0]["2"]="<b>".number_format($li_totalprecio,2,",",".")."</b>";
		$la_data[0]["3"]="<b>".number_format($li_totaliva,2,",",".")."</b>";
		$la_data[0]["4"]="<b>".number_format($li_totalmonto,2,",",".")."</b>";		

		$la_anchos_col = array(17,32,32,32);
		$la_justificaciones = array("center","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232));
		$io_pdf->add_tabla(100,$la_data,$la_opciones);
	}//fin de uf_print_detalle

	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_ganadores($as_numanacot,$as_tipsolcot,$aa_ganadores,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_items
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime los ganadores del analisis de cotizacion
		//	   Creado Por: Ing. Laura Cabr?
		// Fecha Creaci?n: 26/08/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_class_report;
		global $io_funciones, $ls_bolivares;				
		global $io_pdf;
						
		//Imprimiendo primer titulo
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Resumen de Proveedores Ganadores</b>";
		$la_anchos_col = array(260);
		$la_justificaciones = array("center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 1,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(200,200,200),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->ezSetDy(-35);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	//primera fila del item, color gris	
		
		//Imprimiendo titulos columnas
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>C?digo</b>";
		$la_data[0]["2"]="<b>Nombre</b>";
		$la_data[0]["3"]="<b>Subtotal ".$ls_bolivares."</b>";
		$la_data[0]["4"]="<b>Total Cargos ".$ls_bolivares."</b>";
		$la_data[0]["5"]="<b>Monto Total ".$ls_bolivares."</b>";

		$la_anchos_col = array(25,80,52,51,52);
		$la_justificaciones = array("center","center","center","center","center");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232),
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	
		
		$la_data=array();
		$li_totalganadores=count((array)$aa_ganadores);
		$li_totalsubtotal=0;
		$li_totaliva=0;
		$li_totalmonto=0;
		for($li_i=0;$li_i<$li_totalganadores;$li_i++)
		{
			$ls_proveedor		= $aa_ganadores[$li_i]["cod_pro"];
			$ls_cotizacion		= $aa_ganadores[$li_i]["numcot"];
			$ls_tipo_proveedor	= $aa_ganadores[$li_i]["tipconpro"];
			$la_items = "";
			$li_totrow = "";
			$arrResultado = $io_class_report->uf_select_items_proveedor($ls_cotizacion,$ls_proveedor,$as_numanacot,$as_tipsolcot,$la_items,$li_totrow); 
			$la_items = $arrResultado['aa_items'];
			$li_totrow = $arrResultado['li_i'];
			$la_totales = "";
			$la_totales = $io_class_report->uf_calcular_montos($li_totrow,$la_items,$la_totales,$ls_tipo_proveedor);
			$la_data[$li_i]["1"]=$ls_proveedor;
			$la_data[$li_i]["2"]=$aa_ganadores[$li_i]["nompro"];
			$la_data[$li_i]["4"]=number_format($la_totales["subtotal"],2,",",".");
			$la_data[$li_i]["5"]=number_format($la_totales["totaliva"],2,",",".");
			$la_data[$li_i]["6"]=number_format($la_totales["total"],2,",",".");
			$li_totalsubtotal+=$la_totales["subtotal"];
			$li_totaliva+=$la_totales["totaliva"];
			$li_totalmonto+=$la_totales["total"];
		}
		
		//Imprimiendo columnas
	
		$la_justificaciones=array();
		$la_justificaciones = array("center","left","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 9,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "grosor_lineas_externas"=>0.5,
								   "grosor_lineas_internas"=>0.5);
		$io_pdf->add_tabla(5,$la_data,$la_opciones);	
		
		//imprimiendo totales
		$la_data=array();
		$la_anchos=array();
		$la_justificaciones=array();
		$la_data[0]["1"]="<b>Totales ".$ls_bolivares."</b>";
		$la_data[0]["2"]="<b>".number_format($li_totalsubtotal,2,",",".")."</b>";
		$la_data[0]["3"]="<b>".number_format($li_totaliva,2,",",".")."</b>";
		$la_data[0]["4"]="<b>".number_format($li_totalmonto,2,",",".")."</b>";		

		$la_anchos_col = array(17,52,51,52);
		$la_justificaciones = array("center","right","right","right");
		$la_opciones = array("color_texto"     => array(0,0,0),
							   "anchos_col"    => $la_anchos_col,
							   "tamano_texto"  => 10,
							   "lineas"        => 2,
							   "alineacion_col"=> $la_justificaciones,
								   "color_fondo"=>array(232,232,232));
		$io_pdf->add_tabla(93,$la_data,$la_opciones);
	}//fin de uf_print_detalle
	//------------------------------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_print_pie_pagina($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_pie_pagina
		//		    Acess: private 
		//	    Arguments: $io_pdf   : Instancia de objeto pdf
		//    Description: funci?n que imprime el pie del reporte
		//	   Creado Por: Ing. Laura Cabr?
		// Fecha Creaci?n: 17/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
        $la_data=array(array('elaborado'=>'Analista de Compras','revisado'=>'Jefe de Compras','autorizado'=>'Gte. de Servicios Administrativos'),
                       array('elaborado'=>'','revisado'=>'','autorizado'=>''),
                       array('elaborado'=>'','revisado'=>'','autorizado'=>''));
        $la_columna=array('elaborado'=>'',
                             'revisado'=>'',
                          'autorizado'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 8, // Tama?o de Letras
                         'rowGap' => 1,
                         'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
                         'showLines'=>1, // Mostrar L?neas
                         'shaded'=>0, // Sombra entre l?neas
                         'width'=>540, // Ancho de la tabla
                         'maxWidth'=>540, // Ancho M?ximo de la tabla
                         'xOrientation'=>'center', // Orientaci?n de la tabla
                         'cols'=>array('elaborado'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
                                        'revisado'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
                                        'autorizado'=>array('justification'=>'center','width'=>200))); // Justificaci?n y ancho de la columna                               
		$io_pdf->ezSetDy(-5);
        //S$io_pdf->y=80;
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config); 
        unset($la_data);
        unset($la_columna);
        unset($la_config);
        $la_data=array(array('elaborado'=>'Vicep. de Administraci?n','revisado'=>'Vicep. Ejecutivo','autorizado'=>'Presidente'),
                       array('elaborado'=>'','revisado'=>'','autorizado'=>''),
                       array('elaborado'=>'','revisado'=>'','autorizado'=>''));
        $la_columna=array('elaborado'=>'',
                             'revisado'=>'',
                          'autorizado'=>'');
        $la_config=array('showHeadings'=>0, // Mostrar encabezados
                         'fontSize' => 8, // Tama?o de Letras
                         'rowGap' => 1,
                         'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
                         'showLines'=>1, // Mostrar L?neas
                         'shaded'=>0, // Sombra entre l?neas
                         'width'=>540, // Ancho de la tabla
                         'maxWidth'=>540, // Ancho M?ximo de la tabla
                         'xOrientation'=>'center', // Orientaci?n de la tabla
                         'cols'=>array('elaborado'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
                                        'revisado'=>array('justification'=>'center','width'=>200), // Justificaci?n y ancho de la columna
                                        'autorizado'=>array('justification'=>'center','width'=>200))); // Justificaci?n y ancho de la columna                                       
        $io_pdf->ezTable($la_data,$la_columna,'',$la_config); 
        unset($la_data);
        unset($la_columna);
        unset($la_config);
		
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	//--------------------------------------------------------------------------------------------------------------------------------
  	
	require_once('../../shared/class_folder/class_pdf.php');
	require_once("sigesp_soc_class_report.php");
	$io_class_report=new sigesp_soc_class_report();
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();
	require_once("../class_folder/class_funciones_soc.php");
	$io_fun_compra = new class_funciones_soc();
	$ls_tiporeporte=$io_fun_compra->uf_obtenervalor_get("tiporeporte",1);
	$ls_bolivares="Bs.";
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_class_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
	}
	
	set_time_limit(3000);	
	$io_pdf=new class_pdf('LETTER','landscape'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->numerar_paginas(7);	
	//$io_pdf->set_margenes(0,20,0,0);	
    $io_pdf->ezSetCmMargins(5,5,2,2); // Configuraci?n de los margenes en cent?metros
	$ls_tipsolcot=$_GET["tipsolcot"];
	$ls_numanacot=$_GET["numanacot"];
	$ld_fecha=$_GET["fecha"];
	$ls_observacion=$_GET["observacion"];	
	$lb_valido=uf_insert_seguridad();
	if($lb_valido)
	{
		uf_print_encabezado_pagina($ls_numanacot,$ls_observacion,$ld_fecha,$io_pdf);
		$la_cotizaciones="";
		$arrResultado=$io_class_report->uf_cargar_cotizaciones($ls_numanacot, $la_cotizaciones);
		$la_cotizaciones = $arrResultado['aa_proveedores'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{
            //$io_pdf->ezSetY(470);
			uf_print_proveedores($la_cotizaciones,$io_pdf);
			$lb_valido=$io_class_report->uf_select_items($ls_numanacot,$ls_tipsolcot,$la_items);
			if($lb_valido)
			{
				//$io_pdf->set_margenes(20,20,0,0);
				uf_print_items($ls_tipsolcot,$la_items,$io_pdf);
				$la_ganadores=$io_class_report->uf_select_cotizacion_analisis($ls_numanacot,$ls_tipsolcot);
				uf_print_ganadores($ls_numanacot,$ls_tipsolcot,$la_ganadores,$io_pdf);
				//$io_pdf->set_margenes(0,0,0,0);
				//uf_print_pie_pagina($io_pdf);
				$io_pdf->ezStream();
				unset($io_pdf);
			}
		}
	}
	if(!$lb_valido)
	{
		print("<script language=JavaScript>");
		print(" alert('No hay nada que reportar');"); 
		print(" close();");
		print("</script>");	
	}
?> 