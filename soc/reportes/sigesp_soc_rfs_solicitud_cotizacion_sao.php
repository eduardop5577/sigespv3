<?php
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
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "</script>";		
	}

	ini_set('memory_limit','1024M');
 	ini_set('max_execution_time ','0');  

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_seguridad($as_titulo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_insert_seguridad
		//		   Access: private 
		//	    Arguments: as_titulo // Título del reporte
		//    Description: función que guarda la seguridad de quien generó el reporte
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_soc;
		
		$ls_descripcion="Generó el Reporte de Formato de salida de ".$as_titulo;
		$lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_solicitud_cotizacion.php",$ls_descripcion);
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_titulo,$as_numsolcot,$as_fecsolcot,$as_dentipsolcot,$as_obssolcot,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezadopagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   hidnumero // Número de solicitud
		//	    		   ls_fecsolcot // Número de solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->rectangle(140,705,450,40);
		$io_pdf->line(450,705,450,745);
		$io_pdf->line(450,725,590,725);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);		
		$io_pdf->addText(200,720,14,"<b>".$as_titulo."</b>"); // Agregar el título
		$io_pdf->addText(460,730,10,"<b>   No.:</b>");      // Agregar texto
		$io_pdf->addText(495,730,10,$as_numsolcot); // Agregar Numero de la solicitud
		$io_pdf->addText(450,710,10,"<b>  Fecha:</b>"); // Agregar texto
		$io_pdf->addText(495,710,10,$as_fecsolcot); // Agregar la Fecha
		$io_pdf->addText(555,770,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(560,760,7,date("h:i a")); // Agregar la hora
		
		$io_pdf->ezSetY(695);
		$la_data=array(array('name'=>'<b>                  TIPO:  </b>'.$as_dentipsolcot),
					   array('name'=>'<b>OBSERVACIÓN: </b> '.$as_obssolcot));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'titleFontSize' => 9,
						 'shaded'=>0, // Sombra entre líneas
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);
////////////////////////////////////////////////////////////////FIRMAS//////////////////////////////////////////////////////////		
		$io_pdf->line(40,115,590,115);//horizontal		
		$io_pdf->line(40,73,40,115);//vertical
		$io_pdf->line(590,73,590,115);//vertical
		$io_pdf->line(40,73,590,73);//horizontal		
//////////////////Recepción del Proveedor//////////////////////////////////////////////////////////
		$io_pdf->addText(260,108,6,"RECEPCIÓN POR EL PROVEEDOR"); // Agregar el título
		$io_pdf->line(40,106,590,106);//horizontal
		$io_pdf->line(175,73,175,106);//vertical		
		$io_pdf->line(315,73,315,106);//vertical		
		$io_pdf->line(455,73,455,106);//vertical
		$io_pdf->addText(50,100,6,"NOMBRE:"); // Agregar el título
		$io_pdf->addText(180,100,6,"No .DE CEDULA DE IDENTIDAD:"); // Agregar el título
		$io_pdf->addText(320,100,6,"FECHA:"); // Agregar el título
		$io_pdf->addText(460,100,6,"FIRMA:"); // Agregar el título
		
		$io_pdf->addText(40,67,6,"<b>NOTA:</b>Favor consignar la cotización en la <b>oficina de Administración del SAO, antes de las 3:00 PM a Compras</b>"); // Agregar el título
		$io_pdf->addText(40,62,6,"<b>Shirley González o Asuntos Administrativos Auristela Aparicio"); // Agregar el título
		$io_pdf->addText(40,57,6,"La cotización debe contar con todos los datos de la empresa (logo,sello,rif,dirección,telefonos,correo)."); // Agregar el título
		$io_pdf->addText(40,52,6,"La vigencia de la cotización, firma del vendedor y sello."); // Agregar el título
		$io_pdf->addText(40,47,6,"Asi mismo se le agradece cotizar solo lo requerido en C/U de los items, si falta o dispone de algun producto,"); // Agregar el título
		$io_pdf->addText(40,42,6,"favor detallar en la cotización, en el renglon correspondiente <b>''NO COTIZA''</b>"); // Agregar el título
		$io_pdf->addText(40,37,6,"* Medicamentos e insumos con vencimiento menor a 1 año debe estar acompañada de <b>CARTA DE COMPROMISO</b>"); // Agregar el título
		$io_pdf->addText(40,32,6,"* Es necesario colocar en la cotización el tiempo de entrega establecido una vez recibida la Orden de Compra"); // Agregar el título
////////////////////////////////////////////////////////////////FIRMAS//////////////////////////////////////////////////////////		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_datos_proveedor($as_codpro,$as_nompro,$as_dirpro,$as_telpro,$as_email,$as_rifpro,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_datos_proveedor
		//		   Access: private 
		//	    Arguments: as_numsolcot // Número
		//	    		   as_fecsolcot // Fecha
		//	    		   as_obssolcot // Observación
		//	    		   as_codpro // Código de Proveedor
		//	    		   as_nompro // Nombre de Proveedor
		//	    		   as_dirpro // Dirección de Proveedor
		//	    		   as_telpro // Teléfono de Proveedor
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 19/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->saveState();
		$la_data=array(array('name'=>'<b>Nombre o Razón Social: </b>'.$as_codpro.'  -  '.$as_nompro),
 		               array('name'=>'<b>Dirección: </b>'.$as_dirpro),
					   array('name'=>'<b>Teléfono: </b> '.$as_telpro.'  -                                                 <b>E-Mail</b>: '.$as_email.'                                                                         <b>RIF: </b>'.$as_rifpro));				
		
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('name'=>'<b>DATOS DEL PROVEEDOR</b>');		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'showLines'=>1, // Mostrar Líneas
						 'titleFontSize' => 9,
						 'shaded'=>0, // Sombra entre líneas
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		
	}// end function uf_print_cabecera
	//--------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Néstor Falcón.
		// Fecha Creación: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $io_pdf;
		
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('codigo'=>'<b>Código</b>',
						  'denominacion'=>'<b>Denominación</b>',
  						  'cantidad'=>'<b>Cantidad</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>125),      // Justificación y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>335), // Justificación y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>90))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>DETALLE DE LOS MATERIALES, SUMINISTROS O SERVICIOS REQUERIDOS</b>',$la_config);
	}// end function uf_print_detalle
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_firmas($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		   Access: private 
		//	    Arguments: as_titulo // Título del Reporte
		//	    		   as_numsol // numero de la solicitud
		//	    		   ad_fecregsol // fecha de registro de la solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: Función que imprime los encabezados por página
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		// cuadro inferior
        $io_pdf->Rectangle(40,43,550,87);
		
		$io_pdf->line(40,120,590,120);//horizontal
		$io_pdf->line(130,43,130,130);//vertical		
		$io_pdf->line(290,43,290,130);//vertical		
		$io_pdf->line(450,43,450,130);//vertical		
		$io_pdf->addText(40,122,7,"Elaborado Por:"); // Agregar el título
		$io_pdf->addText(38,76,7,"Gustavo A. Oviedo"); // Agregar el título
		$io_pdf->addText(32,70,7,"Analista de la Unidad de"); // Agregar el título
		$io_pdf->addText(37,63,7,"Recursos Humanos"); // Agregar el título
		
		$io_pdf->addText(167,122,7,"Autorizado Por:"); // Agregar el título
		$io_pdf->addText(175,76,7,"Dra. Gloria B. Soler A."); // Agregar el título
		$io_pdf->addText(145,70,7,"Presidenta del Servicio Desconcentrado"); // Agregar el título
		$io_pdf->addText(160,63,7,"Oncologico del Estado Lara"); // Agregar el título
		
		$io_pdf->addText(350,122,7,"Revisado:"); // Agregar el título
		$io_pdf->addText(327,73,7,"Lcda. Juana Puertas"); // Agregar el título
		$io_pdf->addText(307,63,7,"Jefe(E) de Recursos Humanos del SAO"); // Agregar el título
		
		//$io_pdf->addText(440,122,7,"Autorización Administrativa"); // Agregar el título
		$io_pdf->addText(510,73,7,"<b>Sello:</b>"); // Agregar el título
		$io_pdf->addText(480,63,7,"<b>Vo.Bo. Administración</b>"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}// end function uf_print_encabezado_pagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	require_once("sigesp_soc_class_report.php");	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	
	require_once("../class_folder/class_funciones_soc.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_report    = new sigesp_soc_class_report($con);
	$io_funciones = new class_funciones();
	$io_fun_soc	  = new class_funciones_soc();
	
	$ls_numsolcot = $_GET["numsolcot"];
	$ls_tipsolcot = $_GET["tipsolcot"];
	$ls_fecsolcot = $_GET["fecsolcot"];
	if ($ls_tipsolcot=='B')
	   {
	     $ls_tabla = "soc_dtsc_bienes";
	     $ls_campo = "codart";
	     $ls_table = "siv_articulo"; 
	     $ls_tipo  = "Bienes"; 
	   }
	elseif($ls_tipsolcot=='S')
	   {
	     $ls_tabla = "soc_dtsc_servicios";
	     $ls_campo = "codser";
	     $ls_table = "soc_servicios";
	     $ls_tipo  = "Servicios"; 
	   }
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	$ls_titulo = "SOLICITUD DE COTIZACIÓN";

	$lb_valido = uf_insert_seguridad($ls_titulo); // Seguridad de Reporte
	if ($lb_valido)
	   {
	     $arrResultado = $io_report->uf_load_cabecera_formato_solicitud_cotizacion($ls_numsolcot,$ls_tipsolcot,$ls_fecsolcot,$ls_tabla,$lb_valido);
		 $rs_data = $arrResultado['rs_data'];
		 $lb_valido = $arrResultado['lb_valido'];
	     if (!$lb_valido)
		    {
			  print("<script language=JavaScript>");
			  print(" alert('No hay nada que Reportar !!!');"); 
			  print(" close();");
			  print("</script>");
		    }
	     else
	        {
	          $li_numrows = $io_sql->num_rows($rs_data);
		      if ($li_numrows>0)
		         {
				   
				   set_time_limit(1800);
				   $io_pdf = new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
				   $io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
				   $io_pdf->ezSetCmMargins(5,5,3,3); // Configuración de los margenes en centímetros
				   $io_pdf->ezStartPageNumbers(550,30,10,'','',1); // Insertar el número de página
				   $li_count = 0; 
				   while (($row=$io_sql->fetch_row($rs_data)) && $lb_valido)
						 {
                           $li_count++;
					       if ($li_count>1)
					          {
								$io_pdf->ezNewPage(); 					  
						      }   
 					  	   $ls_codpro    = $row["cod_pro"];
					  	   $ls_nompro    = $row["nompro"];
						   $ls_dirpro    = $row["dirpro"];
						   $ls_telpro    = $row["telpro"];
						   $ls_obssolcot = $row["obssol"];
						   $ls_fecsolcot = $row["fecsol"];
						   $ls_mailpro   = $row["email"];
						   $ls_rifpro    = $row["rifpro"];
						   $ls_fecsolcot = $io_funciones->uf_convertirfecmostrar($ls_fecsolcot);
						   $arrResultado     = $io_report->uf_load_dt_solicitud_cotizacion($ls_numsolcot,$ls_codpro,$ls_tabla,$ls_table,$ls_campo,$lb_valido);
						   $rs_datos = $arrResultado['rs_data'];
						   $lb_valido = $arrResultado['lb_valido'];
						   if ($lb_valido)
					          {
					     	    $li_totrows = $io_sql->num_rows($rs_datos);
							    if ($li_totrows>0)
							       { 
							         $li_i = 0;
								     while($row=$io_sql->fetch_row($rs_datos))
								          {
									        $li_i++;
										    $ls_codigo       = $row["codite"];
										    $ls_denite       = $row["denite"];
										    $ld_canite       = number_format($row["canite"],2,',','.');
									        $la_datos[$li_i] = array('codigo'=>$ls_codigo,'denominacion'=>$ls_denite,'cantidad'=>$ld_canite);
									      }
				    		       }
						        else
							       {
							         $lb_valido = false;
							       }
						      }
					       uf_print_encabezado_pagina($ls_titulo,$ls_numsolcot,$ls_fecsolcot,$ls_tipo,$ls_obssolcot,$io_pdf);
					       uf_print_datos_proveedor($ls_codpro,$ls_nompro,$ls_dirpro,$ls_telpro,$ls_mailpro,$ls_rifpro,$io_pdf);
					       uf_print_detalle($la_datos,$io_pdf);
	           		       //uf_firmas($io_pdf);
						   $io_pdf->setStrokeColor(0,0,0);
					       //$io_pdf->line(40,40,590,40);
					     }
			        $io_pdf->ezStopPageNumbers(1,1);
			        $io_pdf->ezStream();
			     }
		      else
		         {
			       print("<script language=JavaScript>");
			       print(" alert('No hay nada que Reportar');"); 
			       print(" close();");
			       print("</script>");
			     }
	        } 
	   }			
?>
