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
		//	    Arguments: as_titulo // T?tulo del reporte
		//    Description: funci?n que guarda la seguridad de quien gener? el reporte
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_soc;
		
		$ls_descripcion="Gener? el Reporte de Formato de salida de ".$as_titulo;
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
		//	    Arguments: as_titulo // T?tulo del Reporte
		//	    		   hidnumero // N?mero de solicitud
		//	    		   ls_fecsolcot // N?mero de solicitud
		//	    		   io_pdf // Instancia de objeto pdf
		//    Description: funci?n que imprime los encabezados por p?gina
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->saveState();
		$io_pdf->rectangle(40,705,550,40);
		$io_pdf->line(250,705,250,745);
		$io_pdf->line(500,705,500,745);
		$io_pdf->addText(42,738,6,"<b>REP?BLICA BOLIVARIANA DE VENEZUELA</b>");      // Agregar texto
		$io_pdf->addText(42,731,6,"<b>MINISTERIO DEL PODER POPULAR DE PLANIFICACI?N Y FINANZAS</b>");      // Agregar texto
		$io_pdf->addText(42,724,6,"<b>SUPERINTENDENCIA DE LA ACTIVIDAD ASEGURADORA</b>");      // Agregar texto
		$io_pdf->addText(42,717,6,"<b>OFICINA DE ADMINISTRACI?N</b>");      // Agregar texto
		
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_pdf->rectangle(40,665,550,40);
		$io_pdf->line(450,665,450,705);
		$io_pdf->line(450,685,590,685);
		//$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],40,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$li_tm=$io_pdf->getTextWidth(10,$as_titulo);		
		$io_pdf->addText(200,680,14,"<b>".$as_titulo."</b>"); // Agregar el t?tulo
		$io_pdf->addText(460,690,10,"<b>   No.:</b>");      // Agregar texto
		$io_pdf->addText(495,690,10,$as_numsolcot); // Agregar Numero de la solicitud
		$io_pdf->addText(450,670,10,"<b>  Fecha:</b>"); // Agregar texto
		$io_pdf->addText(495,670,10,$as_fecsolcot); // Agregar la Fecha
		$io_pdf->addText(505,730,7,date("d/m/Y")); // Agregar la Fecha
		$io_pdf->addText(505,720,7,date("h:i a")); // Agregar la hora
		
		$io_pdf->ezSetY(660);
		$la_data=array(array('name'=>'<b>                  TIPO:  </b>'.$as_dentipsolcot),
					   array('name'=>'<b>OBSERVACI?N: </b> '.$as_obssolcot));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'titleFontSize' => 9,
						 'shaded'=>0, // Sombra entre l?neas
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columna);
		unset($la_config);		

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
		//	    Arguments: as_numsolcot // N?mero
		//	    		   as_fecsolcot // Fecha
		//	    		   as_obssolcot // Observaci?n
		//	    		   as_codpro // C?digo de Proveedor
		//	    		   as_nompro // Nombre de Proveedor
		//	    		   as_dirpro // Direcci?n de Proveedor
		//	    		   as_telpro // Tel?fono de Proveedor
		//	    		   io_pdf // total de registros que va a tener el reporte
		//    Description: funci?n que imprime la cabecera de cada p?gina
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 19/06/2007 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
		$io_pdf->saveState();
		$la_data=array(array('name'=>'<b>Nombre o Raz?n Social: </b>'.$as_codpro.'  -  '.$as_nompro),
 		               array('name'=>'<b>Direcci?n: </b>'.$as_dirpro),
					   array('name'=>'<b>Tel?fono: </b> '.$as_telpro.'  -                                                 <b>E-Mail</b>: '.$as_email.'                                                                         <b>RIF: </b>'.$as_rifpro));				
		
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('name'=>'<b>DATOS DEL PROVEEDOR</b>');		
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'showLines'=>1, // Mostrar L?neas
						 'titleFontSize' => 9,
						 'shaded'=>0, // Sombra entre l?neas
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'justification'=>'center', // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M?ximo de la tabla
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
		//	    Arguments: la_data // arreglo de informaci?n
		//	   			   io_pdf // Objeto PDF
		//    Description: funci?n que imprime el detalle
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $io_pdf;
		
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tama?o de Letras
						 'showLines'=>0, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientaci?n de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho M?ximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		
		unset($la_data1);
		unset($la_columna);
		unset($la_config);
		
		$la_columna=array('codigo'=>'<b>C?digo</b>',
						  'denominacion'=>'<b>Denominaci?n</b>',
  						  'cantidad'=>'<b>Cantidad</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tama?o de Letras
						 'titleFontSize' => 9,  // Tama?o de Letras de los t?tulos
						 'showLines'=>1, // Mostrar L?neas
						 'shaded'=>0, // Sombra entre l?neas
						 'width'=>500, // Ancho de la tabla
						 'maxWidth'=>500, // Ancho M?ximo de la tabla
						 'xPos'=>320, // Orientaci?n de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>125),      // Justificaci?n y ancho de la columna
						 			   'denominacion'=>array('justification'=>'left','width'=>335), // Justificaci?n y ancho de la columna
						 			   'cantidad'=>array('justification'=>'right','width'=>90))); // Justificaci?n y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'<b>DETALLE DE LOS MATERIALES, SUMINISTROS O SERVICIOS REQUERIDOS</b>',$la_config);
	}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_autorizacion($io_pdf)
{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizacion
		//		    Acess: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: funci?n el final del voucher 
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creaci?n: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
		
       	$io_pie = $io_pdf->openObject();
		// cuadro inferior
		$io_pdf->line(15,160,585,160);//Horizontal
		$io_pdf->line(15,230,585,230);//Horizontal
		$io_pdf->line(15,175,585,175);//Horizontal	
		$io_pdf->line(15,160,585,160);//Horizontal
		$io_pdf->line(15,148,585,148);//Horizontal
		$io_pdf->line(15,84,585,84);//Horizontal				
        $io_pdf->Rectangle(15,60,570,180);
		$io_pdf->line(195,148,195,60);//Vertical
		$io_pdf->line(415,148,415,60);//Vertical	
		$io_pdf->addText(230,232,6,"IMPORTANTE: REQUISITOS DEL PRESUPUESTO"); // Agregar el t?tulo
		$io_pdf->addText(20,223,6,"1.- INDICAR PRECIO UNITARIO Y TOTAL"); // Agregar el t?tulo
		$io_pdf->addText(20,217,6,"2.- IMPUESTO AL VALOR AGREGADO (IVA ) 12%"); // Agregar el t?tulo
		$io_pdf->addText(20,210,6,"3.- SE?ALAR EL N?MERO DEL REGISTRO DE INFORMACI?N FISCAL (RIF)"); // Agregar el t?tulo
		$io_pdf->addText(20,203,6,"4.- DIRECCI?N COMPLETA Y TEL?FONO"); // Agregar el t?tulo
		$io_pdf->addText(20,197,6,"5.- N?MERO DE INSCRIPCI?N EN EL REGISTRO NACIONAL DE CONTRATISTAS (RNC), PARA CANTIDADES MAYORES A 4.000 UNIDADES TRIBUTARIAS"); // Agregar el t?tulo
		$io_pdf->addText(20,190,6,"6.- INDICAR TIEMPO DE ENTREGA"); // Agregar el t?tulo
		$io_pdf->addText(20,183,6,"7.- INDICAR SI CUMPLE  CON EL COMPROMISO DE RESPONSABILIDAD SOCIAL  (ART. 6 DE LA LEY DE CONTRATACIONES P?BLICAS)"); // Agregar el t?tulo
		$io_pdf->addText(20,177,6,"8.- ELABORAR A NOMBRE DE: SUPERINTENDENCIA DE LA ACTIVIDAD ASEGURADORA"); // Agregar el t?tulo
		
		$io_pdf->addText(20,169,6,"A partir de la recepci?n de la presente solicitud, la Empresa cuenta con tres (3) d?as h?biles para presentar su Oferta en bienes o servicios, y cuatro (4) d?as h?biles para Obras, seg?n el Art. 45 de la Ley de"); // Agregar el t?tulo
		$io_pdf->addText(20,162,6,"Contrataciones P?blicas vigente."); // Agregar el t?tulo

		$io_pdf->addText(240,150,6,"JEFE DE LA OFICINA DE ADMINISTRACI?N"); // Agregar el t?tulo
		
		$io_pdf->addText(230,78,6,"Direcci?n: Final Av. Venezuela, Torre Del Desarrollo, El Rosal"); // Agregar el t?tulo
		$io_pdf->addText(265,71,6,"N? de RIF: G-20008047-7"); // Agregar el t?tulo
		$io_pdf->addText(230,64,6,"Contactos: 0212/901688 ? 9051524 Telefax: 0212/9051688"); // Agregar el t?tulo
		$io_pdf->closeObject();
		$io_pdf->addObject($io_pie,'all');
	}	

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
	$ls_titulo = "SOLICITUD DE COTIZACI?N";

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
				   $io_pdf->ezSetCmMargins(2,2,3,3); // Configuraci?n de los margenes en cent?metros
				   //$io_pdf->ezStartPageNumbers(550,30,10,'','',1); // Insertar el n?mero de p?gina
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
	           		       uf_print_autorizacion($io_pdf);
						   $io_pdf->setStrokeColor(0,0,0);
					       //$io_pdf->line(20,50,580,50);
					     }
			        //$io_pdf->ezStopPageNumbers(1,1);
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