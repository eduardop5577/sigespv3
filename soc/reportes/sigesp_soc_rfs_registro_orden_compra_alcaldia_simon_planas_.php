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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//    REPORTE: Formato de salida  de la Orden de Compra
//  ORGANISMO: ALCALDIA SIMON PLANAS
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    session_start();   
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";		
		print "</script>";		
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($as_estcondat,$as_numordcom,$ad_fecordcom,$as_coduniadm,$as_denuniadm, $as_codfuefin,
	                                   $as_denfuefin,$as_codigo,$as_nombre,$as_conordcom,$as_rifpro,$as_diaplacom,$as_dirpro,
									   $ls_forpagcom,$as_telpro,$as_obsordcom,$as_estcom,$ai_seguro,$ad_porcentaje,$ad_montoseg,$ai_plazo,$as_condicion,
									   $as_forpagcom,$ad_monant,$as_lugcom,$as_codmoneda,$as_moneda,$ad_tasa,$ad_mondiv,$as_dirdependencia,
									   $as_dependencia,$ld_fechentdesde,$ld_fechenthasta,$io_pdf)
	{
		global $io_pdf;
				
	    $io_encabezado=$io_pdf->openObject();		
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		
		$io_pdf->addText(50,607,9,$as_numordcom); // Agregar Numero de la solicitud
		$io_pdf->addText(280,607,9,$ad_fecordcom); // Agregar la Fecha			
		$io_pdf->addText(50,574,9,$as_rifpro." - ".$as_nombre." - ".$as_dirpro); // Agregar la Fecha			
		$io_pdf->addText(83,536,9,"Servicios Generales"); // Agregar la Fecha			
		$io_pdf->addText(224,536,9,"Desde: ".$ld_fechentdesde." - Hasta: ".$ld_fechenthasta); // Agregar la Fecha			
		$io_pdf->addText(464,536,9,$as_forpagcom); // Agregar la Fecha			
		$io_pdf->addText(34,500,9,'</b>'.$as_coduniadm.' - '.'<b>'.$as_denuniadm.'</b>'); // Agregar la Fecha			
		$io_pdf->addText(324,500,9,$as_dirdependencia); // Agregar la Fecha			
		
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
	}
   	function uf_print_concepto($as_conordcom,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_concepto
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Selena Lucena
		// Fecha Creación: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $io_pdf;
				
			$x=50; //coordenada de x
			$y=120; //coordenada de y
			$w=500; //tamaño de la linea ke kiero ke se imprima
			$letra=9; //tamaño de letra
			
			$wrap=$io_pdf->addTextWrap($x,$y,$w,$letra,$as_conordcom,'full');//aki inicializamos la variable ke contendrá el texto
			
			while($wrap!="") //condicional ke se cumple hasta ke ya no hay mas palabras a imprimir en el explorador
			{
				$y=$y-15; // se le disminuye a la y para lo que será nuestra siguiente linea
				$wrap=$io_pdf->addTextWrap($x,$y,$w,$letra,"$wrap",'full'); // imprime y almacena lo ke kedó del mensaje 
			} 
	}
	//------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_detallespg($la_data,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
		global $io_pdf;
				
		$la_data1=array(array('name'=>''));				
		$la_columna=array('name'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 11, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>320, // Orientación de la tabla
						 'width'=>548, // Ancho de la tabla						 
						 'maxWidth'=>548); // Ancho Máximo de la tabla
		$io_pdf->ezTable($la_data1,$la_columna,'',$la_config);	
		unset($la_data1);		
		unset($la_columna);		
		unset($la_config);		
		
		$la_datasercon= array(array('codigo'=>"<b>Estructura Presupuestaria</b>",'spg_cuenta'=>"<b>Cuenta Presupuestaria</b>",'denominacion'=>"<b>Denominacion</b>",'monto'=>"<b>Monto </b>"));
		$la_columna=array('codigo'=>'<b>Estructura Programatica</b>',
						  'spg_cuenta'=>'<b>Cuenta Presupuestaria</b>',
  						  'denominacion'=>'<b>Denominacion</b>',
						  'monto'=>'<b>Monto</b>',
						  );
						  
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>1, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Sombra entre líneas
						 'width'=>548, // Ancho de la tabla
						 'maxWidth'=>548, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>90),
									   'denominacion'=>array('justification'=>'center','width'=>195),
									   'monto'=>array('justification'=>'right','width'=>100)
									  )
						); // Justificación y ancho de la columna
		//$io_pdf->ezTable($la_datasercon,$la_columna,'',$la_config);

		$la_columna=array('codigo'=>'<b>Codigo</b>',
						  'spg_cuenta'=>'<b>Cuenta</b>',
						  'denominacion'=>'<b>Denominacion</b>',
						  'monto'=>'<b>Monto</b>',
						  );
						  
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>548, // Ancho de la tabla
						 'maxWidth'=>548, // Ancho Máximo de la tabla
						 'xPos'=>320, // Orientación de la tabla
						 'cols'=>array('codigo'=>array('justification'=>'center','width'=>160), // Justificación y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>90),
									   'denominacion'=>array('justification'=>'left','width'=>195),
									   'monto'=>array('justification'=>'right','width'=>100)
									  )
						); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	}	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_detalle($as_estcondat,$la_data,$io_pdf)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		   Access: private 
		//	    Arguments: la_data ---> arreglo de información
		//	    		   io_pdf ---> Instancia de objeto pdf
		//    Description: función que imprime el detalle 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 21/06/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ls_bolivares;
		global $io_pdf;
				
		if ($as_estcondat=='B')
		{
		$io_pdf->ezSetY(460);
		$la_columnas=array('unidad'=>'<b>Unid</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'spg_cuenta'=>'<b>Codigo Pres</b>',
						   'precio'=>'<b>Precio </b>',
						   'montot'=>'<b>Total </b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>295, // Orientación de la tabla
						 'cols'=>array('unidad'=>array('justification'=>'center','width'=>33), // Justificación y ancho de la columna
									   'cantidad'=>array('justification'=>'left','width'=>34), // Justificación y ancho de la columna
									   'denominacion'=>array('justification'=>'left','width'=>260), // Justificación y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'precio'=>array('justification'=>'right','width'=>85), // Justificación y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>85))); // Justificación y ancho de la columna
	     }
		elseif($as_estcondat=='S')
		{
		$io_pdf->ezSetY(460);
		$la_columnas=array('unidad'=>'<b>Unid</b>',
						   'cantidad'=>'<b>Cant.</b>',
						   'denominacion'=>'<b>Denominacion</b>',
						   'spg_cuenta'=>'<b>Codigo Pres</b>',
						   'precio'=>'<b>Precio </b>',
						   'montot'=>'<b>Total </b>');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>570, // Ancho de la tabla
						 'maxWidth'=>570, // Ancho Máximo de la tabla
						 'xPos'=>295, // Orientación de la tabla
						 //'xOrientation'=>'center', // Orientación de la tabla
						 'cols'=>array('unidad'=>array('justification'=>'center','width'=>33), // Justificación y ancho de la columna
									   'cantidad'=>array('justification'=>'left','width'=>34), // Justificación y ancho de la columna
									   'denominacion'=>array('justification'=>'left','width'=>260), // Justificación y ancho de la columna
									   'spg_cuenta'=>array('justification'=>'center','width'=>90), // Justificación y ancho de la columna
						 			   'precio'=>array('justification'=>'right','width'=>85), // Justificación y ancho de la columna
						 			   'montot'=>array('justification'=>'right','width'=>85))); // Justificación y ancho de la columna
		
		
		}
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
	}// end function uf_print_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_print_pie_cabecera($ad_subtotal,$ad_cargos,$ad_total,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_cabecera
		//		   Access: private 
		//	    Arguments: ad_subtotal // Monto del Subtotal
		//	    		   ad_cargos // Monto de los Cargos
		//	    		   ad_total // Monto total
		//	    		   io_pdf // Instancia de Objeto PDF
		//    Description: función que imprime la cabecera de cada página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 17/05/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_pdf;
				
		$io_pdf->ezSetDy(-10);
		$la_data[1]=array('titulo'=>'Sub-Total.','valor'=>$ad_subtotal);				
		$la_columna=array('titulo'=>'','valor'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>  9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>338, // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla						 
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>420), // Justificación y ancho de la columna
						 			   'valor'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$la_data[1]=array('titulo'=>'Cargos.','valor'=>$ad_cargos);				
		$la_columna=array('titulo'=>'','valor'=>'');		
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' =>  9, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol2'=>array(0.9,0.9,0.9), // Color de la sombra
						 'xPos'=>338, // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla						 
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'cols'=>array('titulo'=>array('justification'=>'right','width'=>420), // Justificación y ancho de la columna
						 			   'valor'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		
		$io_pdf->addText(527,158,9,$ad_total); // Agregar Numero de la solicitud
		
	}
	//-----------------------------------------------------  Instancia de las clases  ------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	
	require_once("../../base/librerias/php/ezpdf/class.ezpdf.php");
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("sigesp_soc_class_report.php");	
	require_once("../class_folder/class_funciones_soc.php");
	
	$in           = new sigesp_include();
	$con          = $in->uf_conectar();
	$io_sql       = new class_sql($con);	
	$io_funciones = new class_funciones();	
	$io_fun_soc   = new class_funciones_soc();
	$io_report    = new sigesp_soc_class_report();
	$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
	$ls_estmodest = $_SESSION["la_empresa"]["estmodest"];
    $li_candeccon = $_SESSION["la_empresa"]["candeccon"];
	$li_tipconmon = $_SESSION["la_empresa"]["tipconmon"];
	$li_redconmon = $_SESSION["la_empresa"]["redconmon"];
	global $ld_monto;

	//Instancio a la clase de conversión de numeros a letras.
	include("../../base/librerias/php/general/sigesp_lib_numero_a_letra.php");
	$numalet= new class_numero_a_letra();
	//imprime numero con los valore por defecto
	//cambia a minusculas
	$numalet->setMayusculas(1);
	//cambia a femenino
	$numalet->setGenero(1);
	//cambia moneda
	$numalet->setMoneda("Bolivares");
	//cambia prefijo
	$numalet->setPrefijo("***");
	//cambia sufijo
	$numalet->setSufijo("***");
	$ls_tiporeporte=$io_fun_soc->uf_obtenervalor_get("tiporeporte",1);
	$ls_bolivares="Bs.";
	
	if($ls_tiporeporte==1)
	{
		require_once("sigesp_soc_class_reportbsf.php");
		$io_report=new sigesp_soc_class_reportbsf();
		$ls_bolivares="Bs.F.";
		$numalet->setMoneda("Bolivares Fuerte");
	}
	//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
	 global $ls_estcondat;
	 $ls_numordcom = $io_fun_soc->uf_obtenervalor_get("numordcom","");
	 $ls_estcondat = $io_fun_soc->uf_obtenervalor_get("tipord","");
	//--------------------------------------------------------------------------------------------------------------------------------
	$lb_valido=true;
	$arrResultado= $io_report->uf_select_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido); // Cargar los datos del reporte
	$rs_data= $arrResultado['rs_data'];
	$lb_valido = $arrResultado['lb_valido'];
	if ($lb_valido==false)
	   {
	     print("<script language=JavaScript>");
		 print(" alert('No hay nada que Reportar');"); 
		 print(" close();");
		 print("</script>");
	   }
	else
	   {
		 $ls_descripcion="Generó el Reporte de Orden de Compra";
		 $lb_valido=$io_fun_soc->uf_load_seguridad_reporte("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_descripcion);
		 if ($lb_valido)	
		    {
			  
			  set_time_limit(1800);
			  $io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
			  $io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
			  $io_pdf->ezSetCmMargins(11.5,6,3,3); // Configuración de los margenes en centímetros
		 	  while ($row=$io_sql->fetch_row($rs_data))
			        {
				      $ls_numordcom = $row["numordcom"];
				      $ls_estcondat = $row["estcondat"];
					  $ls_coduniadm = $row["coduniadm"];
					  $ls_denuniadm = $row["denuniadm"];
					  $ls_codfuefin = $row["codfuefin"];
					  $ls_denfuefin = $row["denfuefin"];
					  $ls_diaplacom = $row["diaplacom"];
					  $ls_forpagcom = $row["forpagcom"];
					  $ls_codpro    = $row["cod_pro"];
					  $ls_nompro    = $row["nompro"];
					  $ls_rifpro    = $row["rifpro"];
					  $ls_dirpro    = $row["dirpro"];
					  $ls_telpro    = $row["telpro"];
					  $ld_fecordcom = $row["fecordcom"];
					  $ls_obscom    = $row["obscom"];
					  $ld_monsubtot = $row["monsubtot"];
					  $ld_monimp    = $row["monimp"];
					  $ld_montot    = $row["montot"];
					  $ld_monbas    = $row["monsubtot"];
					  $ld_moncar    = $row["monimp"];
					  $ld_monbas     = number_format($ld_monbas,2,',','.');
					  $ld_moncar     = number_format($ld_moncar,2,',','.');
					  $li_seguro     = $row["estsegcom"];
					  $ld_porcentaje = $row["porsegcom"];
					  $ld_montoseg   = $row["monsegcom"];
					  $ls_condicion  = $row["concom"];
					  $ld_monant     = $row["monant"];
					  $ls_lugcom     = $row["estlugcom"];
					  $ls_codmoneda  = $row["codmon"];
					  $ld_tasa           = $row["tascamordcom"];
					  $ld_mondiv         = $row["montotdiv"];
					  $ls_dirdependencia = $row["lugentdir"];
				   	  $ls_dependencia    = $row["lugentnomdep"];
			 		  $ls_obsordcom      = $row["obsordcom"];
				   	  $ls_codpais        = $row["codpai"];
				   	  $ld_fechentdesde        = $row["fechentdesde"];
				   	  $ld_fechenthasta        = $row["fechenthasta"];
					  $ls_estcom=$row["estcom"];
			 		  $ls_pais           = $io_report->uf_select_denominacion('sigesp_pais','despai',"WHERE codpai='".$ls_codpais."'");
					  if ($ls_pais=="---seleccione---")
				         {
				           $ls_pais = "";
				         }
					  $ls_codestado = $row["codest"];
					  $ls_estado = $io_report->uf_select_denominacion('sigesp_estados','desest',"WHERE codpai='".$ls_codpais."' AND codest='".$ls_codestado."'");   
					  if ($ls_estado=="---seleccione---")
					     {
						   $ls_estado = "";   
					     }
				      //$ls_codmunicipio = $row["codmun"];
				      $ls_municipio = $row["denmun"];//$io_report->uf_select_denominacion('sigesp_municipio','denmun',"WHERE codpai='".$ls_codpais."' AND codest='".$ls_codestado."' AND codmun='".$ls_codmunicipio."'");				   
				      /*if ($ls_municipio)
				         {
				           $ls_municipio = "";
				         }*/
				      //$ls_codparroquia = $row["codpar"];
				      $ls_parroquia = $row["denpar"];//$io_report->uf_select_denominacion('sigesp_parroquia','denpar',"WHERE codpai='".$ls_codpais."' AND codest='".$ls_codestado."' AND codmun='".$ls_codmunicipio."' AND codpar='".$ls_codparroquia."'");				   
				      /*if ($ls_parroquia)
				         {
				           $ls_parroquia ="";
				         }*/
			    	  $ld_monto = number_format($ld_monto,2,',','.');
					  if (!empty($ls_coduniadm))
				         { 
  				           $ls_denuniadm = $io_report->uf_select_denominacion('spg_unidadadministrativa','denuniadm',"WHERE coduniadm='".$ls_coduniadm."'");				   
						 }
					  else
				         {
					       $ls_denuniadm="";
				         }
				      if ($ls_codfuefin!="--")
				         {
				           $ls_denfuefin = $io_report->uf_select_denominacion('sigesp_fuentefinanciamiento','denfuefin',"WHERE codfuefin='".$ls_codfuefin."'");				   
				         }  
				      else
				         {
				           $ls_denfuefin="";
				         }
					  if ($ls_codmoneda!="---")
				         {
				           $ls_moneda = $io_report->uf_select_denominacion('sigesp_moneda','denmon',"WHERE codmon='".$ls_codmoneda."'");				   
				         }  
				      else
				         {
				           $ls_moneda = "";
				           $ls_codmoneda="";
				         }
				      if ($ls_lugcom==0)
				         {
					       $ls_lugcom="Nacional";				
				         }
					  else
						 {
						   $ls_lugcom="Extranjero";				
						 }
				      if ($ls_forpagcom=="s1")
				         {
					       $ls_forpagcom="";
				         }				 			 
				      if ($li_seguro==0)
				         {
				           $li_seguro="No";
				         }
				      else
				         {
				  		   $li_seguro="Si";
				         } 
					  if ($ls_tiporeporte==0)
				         {
						   $ld_montotaux=$row["montotaux"];
						   $ld_montotaux=number_format($ld_montotaux,2,",",".");
				         }
				      $numalet->setNumero($ld_montot);
				      $ls_monto     = $numalet->letra();
				      $ld_montot    = number_format($ld_montot,2,",",".");
				      $ld_monsubtot = number_format($ld_monsubtot,2,",",".");
				      $ld_monimp    = number_format($ld_monimp,2,",",".");
					  $ld_tasa      = number_format($ld_tasa,2,',','.');	     
					  $ld_mondiv    = number_format($ld_mondiv,2,',','.');	     
					  $ld_porcentaje= number_format($ld_porcentaje,0,'','');	     
					  $ld_monant    = number_format($ld_monant,2,',','.');	     
					  $ld_montoseg  = number_format($ld_montoseg,2,',','.');
					  $ld_fecordcom = $io_funciones->uf_convertirfecmostrar($ld_fecordcom);
					  $ld_fechentdesde = $io_funciones->uf_convertirfecmostrar($ld_fechentdesde);
					  $ld_fechenthasta = $io_funciones->uf_convertirfecmostrar($ld_fechenthasta);
	 	
		 	 	      uf_print_encabezado_pagina($ls_estcondat,$ls_numordcom,$ld_fecordcom,$ls_coduniadm,$ls_denuniadm,
				                                 $ls_codfuefin,$ls_denfuefin,$ls_codpro,$ls_nompro,$ls_obscom,$ls_rifpro,
										         $ls_diaplacom,$ls_dirpro,$ls_forpagcom,$ls_telpro,$ls_obsordcom,$ls_estcom,
												 $li_seguro,$ld_porcentaje,$ld_montoseg,$ls_diaplacom,$ls_condicion,
												 $ls_forpagcom,$ld_monant,$ls_lugcom,
												 $ls_codmoneda,$ls_moneda,$ld_tasa,$ld_mondiv,$ls_dirdependencia,
												 $ls_dependencia,$ld_fechentdesde,$ld_fechenthasta,$io_pdf);
				
				     /* uf_print_cabecera($li_seguro,$ld_porcentaje,$ld_montoseg,$ls_diaplacom,$ls_condicion,
                                        $ls_forpagcom,$ld_monant,$ls_lugcom,
                                        $ls_codmoneda,$ls_moneda,$ld_tasa,$ld_mondiv,$ls_dirdependencia,
								        $ls_dependencia,$io_pdf);*/
			          $lb_validosep = true;
				      $li_totrow	 = 0;
				      /*$lb_validosep = $io_report->uf_select_soc_sep($ls_codemp,$ls_numordcom,$ls_estcondat);	
				      if ($lb_validosep)
					     {										
					       $li_totrow = $io_report->ds_soc_sep->getRowCount("numordcom");							
						   for ($li_row=1;$li_row<=$li_totrow;$li_row++)
							   { 
							     $ls_numsep   		  = $io_report->ds_soc_sep->data["numsol"][$li_row];  											  
							     $ls_denunadm 		  = $io_report->ds_soc_sep->data["denuniadm"][$li_row];  											  
							     $la_datasep[$li_row] = array('codigo'=>$ls_numsep,'denuniadm'=>$ls_denunadm);
							   }														
					     }*/ 
	                  $arrResultado = $io_report->uf_select_detalle_orden_imprimir($ls_numordcom,$ls_estcondat,$lb_valido);
					  $rs_datos = $arrResultado['rs_data'];
					  $lb_valido = $arrResultado['lb_valido'];
	                  if ($lb_valido)
		                 {
			               $li_totrows = $io_sql->num_rows($rs_datos);
			               if ($li_totrows>0)
			                  {
			 	                $li_i = 0;
				                while ($row=$io_sql->fetch_row($rs_datos))
				                      {
						                $li_i++;
						                $ls_codartser = $row["codartser"];
										$ls_denartser=$row["denartser"];
										if ($ls_estcondat=="B")
										   {
										     $ls_unidad    = $row["unidad"];
											 $ls_codartser = substr($ls_codartser,10,20);   
										   }
										else
										   {
										     $ls_unidad="";
										   }
										if ($ls_unidad=="D")
						                   {
						                     $ls_unidad = "Detal";
						                   }
					 	                elseif($ls_unidad=="M")
										   {
										     $ls_unidad="Mayor";
										   }
						                $li_cantartser   = $row["cantartser"];
										$ld_preartser    = $row["preartser"];
										$ld_subtotartser = $ld_preartser*$li_cantartser;
										$ld_totartser    = $row["monttotartser"];
										$ls_spg_cuenta_art = $row["spg_cuenta"];
										$ls_denunimed = $row["denunimed"];
										$ld_carartser    = $ld_totartser-$ld_subtotartser;
										$ld_preartser    = number_format($ld_preartser,2,",",".");
										$ld_subtotartser = number_format($ld_subtotartser,2,",",".");
										$ld_totartser	 = number_format($ld_totartser,2,",",".");
										$ld_carartser	 = number_format($ld_carartser,2,",",".");
										$ls_denunimed=substr($ls_denunimed,0,5);
										$la_data[$li_i]  = array('codigo'=>$ls_codartser,
																 'denominacion'=>$ls_denartser,
																 'cantidad'=>number_format($li_cantartser,2,',','.'),
																 'precio'=>$ld_preartser,
																 'unidad'=>$ls_denunimed,
																 'subtotal'=>$ld_subtotartser,
																 'cargo'=>$ld_carartser,
																 'montot'=>$ld_subtotartser,
																 'spg_cuenta'=>$ls_spg_cuenta_art);
					                  }
								uf_print_detalle($ls_estcondat,$la_data,$io_pdf);
								unset($la_data);
			                    $arrResultado = $io_report->uf_select_cuenta_gasto($ls_numordcom,$ls_estcondat,$lb_valido); 
								$rs_datos_cuenta = $arrResultado['rs_data'];
								$lb_valido = $arrResultado['lb_valido'];
					            if ($lb_valido)
					               {
						             $li_totrows = $io_sql->num_rows($rs_datos);
						             if ($li_totrows>0)
						                {
							              $li_s = 0;
							              while($row=$io_sql->fetch_row($rs_datos_cuenta))
							                   {
											     $li_s++;
											     $ls_codestpro1 = trim($row["codestpro1"]);
											     $ls_codestpro2 = trim($row["codestpro2"]);
											     $ls_codestpro3 = trim($row["codestpro3"]);
											     $ls_codestpro4 = trim($row["codestpro4"]);
											     $ls_codestpro5 = trim($row["codestpro5"]);
											     $ls_spg_cuenta = trim($row["spg_cuenta"]);
											     $ld_monto      = $row["monto"];
											     $ld_monto      = number_format($ld_monto,2,",",".");
												$ls_dencuenta="";
												$arrResultado = $io_report->uf_select_denominacionspg($ls_spg_cuenta,$ls_dencuenta);	
												$ls_dencuenta=$arrResultado['as_denominacion'];
												$lb_valido = $arrResultado['lb_valido'];
											     if ($ls_estmodest==1)
											        {
												      $ls_codestpro=$ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
											        }
												 else
												    {
													  $ls_codestpro=substr($ls_codestpro1,-2)."-".substr($ls_codestpro2,-2)."-".substr($ls_codestpro3,-2)."-".substr($ls_codestpro4,-2)."-".substr($ls_codestpro5,-2);
												    }
								                 $la_data[$li_s] = array('codigo'=>$ls_codestpro,
												                         'spg_cuenta'=>$ls_spg_cuenta,
																		 'denominacion'=>$ls_dencuenta,
													                     'monto'=>$ld_monto);
							                   }	
							              uf_print_detallespg($la_data,$io_pdf);
							              unset($la_data);
						                }
				                   }
								uf_print_pie_cabecera($ld_monsubtot,$ld_monimp,$ld_montot,$io_pdf);
							  }
		                 }
		            }
		    } 
		 uf_print_concepto($ls_obscom,$io_pdf);
		/*uf_print_encabezado_pie($ld_montot,$ls_monto,$ls_lugcom,$ls_moneda,$ls_moneda,$ld_tasa,$ld_mondiv,$ls_estcondat,
		 	 			        $ls_pais,$ls_estado,$ls_municipio,$ls_parroquia,$ls_estcondat,$ls_bolivares,
                                $ls_tiporeporte,$io_pdf);*/
	   }
   
    if ($lb_valido)
	   {
		 $io_pdf->ezStopPageNumbers(1,1);
		 $io_pdf->ezStream();
	   }
	else
	   {
		 print("<script language=JavaScript>");
		 print(" alert('Ocurrio un error al generar el reporte. Intente de Nuevo');"); 
		 print(" close();");
		 print("</script>");		
	   }
	unset($io_report);
	unset($io_funciones);
	unset($io_fun_soc);
?>