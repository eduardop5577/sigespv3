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


//-----------------------------------------------------------------------------------------------------------------------------------
function uf_insert_seguridad($as_titulo)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_insert_seguridad
	//		   Access: private
	//	    Arguments: as_titulo // Título del Reporte
	//    Description: función que guarda la seguridad de quien generó el reporte
	//	   Creado Por: Ing. Yesenia Moreno
	// Fecha Creación: 22/09/2006
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_fun_scg;

	$ls_descripcion="Generó el Reporte ".$as_titulo;
	$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_comprobante.html",$ls_descripcion);
	return $lb_valido;
}
//-----------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_encabezado_pagina($as_titulo,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_encabezadopagina
	//		    Acess: private
	//	    Arguments: as_titulo // Título del Reporte
	//	    		   io_pdf // Instancia de objeto pdf
	//    Description: función que imprime los encabezados por página
	//	   Creado Por: Ing. Yozelin Barragan
	// Fecha Creación: 28/04/2006
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;
	
	$io_encabezado=$io_pdf->openObject();
	$io_pdf->saveState();
	$io_pdf->line(20,40,578,40);
	$io_pdf->addJpegFromFile('../../../shared/imagebank/'.$_SESSION["ls_logo"],15,720,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo

	$li_tm=$io_pdf->getTextWidth(9,$as_titulo);
	$tm=310-($li_tm/2);
	$io_pdf->addText($tm,730,9,$as_titulo); // Agregar el título

	$io_pdf->addText(530,740,7,$_SESSION["ls_database"]); // Agregar la Base de datos
	$io_pdf->addText(530,730,9,date("d/m/Y")); // Agregar la Fecha
	$io_pdf->addText(530,720,9,date("h:i a")); // Agregar la Fecha
	$io_pdf->restoreState();
	$io_pdf->closeObject();
	$io_pdf->addObject($io_encabezado,'all');
}// end function uf_print_encabezadopagina
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_cabecera($as_procede,$as_comprobante,$as_nomprobene,$adt_fecha,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function: uf_print_cabecera
	//		   Access: private
	//	    Arguments: as_procede // procede
	//	    		   as_comprobante // comprobante
	//                 as_nomprobene   // nombre del proveedor
	//                 adt_fecha     // fecha del comprobante
	//	    		   io_pdf // Objeto PDF
	//    Description: función que imprime la cabecera de cada página
	//	   Creado Por: Ing. Yozelin Barragan
	// Fecha Creación: 28/04/2006
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;
	$r=(237)/255;
	$g=(244)/255;
	$b=(248)/255;
	$la_data=array(array('name'=>'<b>Comprobante</b>  '.$as_procede.'---'.$as_comprobante.'                '.$adt_fecha.''),
	array('name'=>'<b>Beneficiario</b>  '.$as_nomprobene.''));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>1, // Mostrar Líneas
						 'shadeCol'=>array($r,$g,$b), // Color de la sombra
						 'shadeCol2'=>array($r,$g,$b), // Color de la sombra 
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>2, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580); // Ancho Máximo de la tabla
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
}// end function uf_print_cabecera
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
	//	   Creado Por: Ing. Yozelin Barragan
	// Fecha Creación: 28/04/2006
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $io_pdf;
	$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 6, // Tamaño de Letras
						 'titleFontSize' => 9,  // Tamaño de Letras de los títulos
						 'showLines'=>0, // Mostrar Líneas
						 'colGap'=>0.5, // separacion entre tablas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>580, // Ancho de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
						 'xPos'=>300, // Orientación de la tabla
						 'cols'=>array('cuenta'=>array('justification'=>'center','width'=>110), // Justificación y ancho de la columna
									   'denominacion'=>array('justification'=>'left','width'=>100), // Justificación y ancho de la columna
									   'descripcion'=>array('justification'=>'left','width'=>160), // Justificación y ancho de la columna
						 			   'documento'=>array('justification'=>'left','width'=>70), // Justificación y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>70), // Justificación y ancho de la columna
									   'haber'=>array('justification'=>'right','width'=>70))); // Justificación y ancho de la columna
	$la_columnas=array('cuenta'=>'<b>Cuenta</b>',
						   'denominacion'=>'<b>Denominación</b>',
						   'descripcion'=>'<b>Descripción</b>',
						   'documento'=>'<b>Documento</b>',
						   'debe'=>'<b>Debe</b>',
						   'haber'=>'<b>Haber</b>');
	$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
}// end function uf_print_detalle
//--------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
function uf_print_pie_cabecera($ad_totaldebe,$ad_totalhaber,$as_tipo,$io_pdf)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function : uf_print_pie_cabecera
	//		    Acess : private
	//	    Arguments : ad_totaldebe // Total debe
	//	   				ad_totalhaber // Total haber
	//    Description : función que imprime el fin de la cabecera de cada página
	//	   Creado Por: Ing. Yozelin Barragan
	// Fecha Creación : 18/02/2006
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	global $ls_bolivares;
	global $io_pdf;
	$la_data=array(array('name'=>'--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 8, // Tamaño de Letras
						 'showLines'=>0, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'xOrientation'=>'center', // Orientación de la tabla
						 'width'=>560); // Ancho Máximo de la tabla
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	if($as_tipo=="1")// comprobante
	{
		$la_data=array(array('total'=>'<b>Total Comprobante '.$ls_bolivares.'</b>','debe'=>$ad_totaldebe,'haber'=>$ad_totalhaber));
	}
	elseif($as_tipo=="2")//total general
	{
		$la_data=array(array('total'=>'<b>Total '.$ls_bolivares.'</b>','debe'=>$ad_totaldebe,'haber'=>$ad_totalhaber));
	}
	$la_columna=array('total'=>'','debe'=>'','haber'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'colGap'=>0.5, // separacion entre tablas
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>580, // Ancho Máximo de la tabla
						 'maxWidth'=>580, // Ancho Máximo de la tabla
						 'xPos'=>300, // Orientación de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				 		 'cols'=>array('total'=>array('justification'=>'right','width'=>400), // Justificación y ancho de la columna
						 			   'debe'=>array('justification'=>'right','width'=>80), // Justificación y ancho de la columna
						 			   'haber'=>array('justification'=>'right','width'=>80))); // Justificación y ancho de la columna
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
	$la_data=array(array('name'=>''));
	$la_columna=array('name'=>'');
	$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'showLines'=>0, // Mostrar Líneas
						 'fontSize' => 8, // Tamaño de Letras
						 'shaded'=>0, // Sombra entre líneas
						 'width'=>560, // Ancho Máximo de la tabla
						 'xOrientation'=>'center'); // Orientación de la tabla
	$io_pdf->ezTable($la_data,$la_columna,'',$la_config);
}// end function uf_print_pie_cabecera

//-----------------------------------------------------------------------------------------------------------------------------------
function uf_init_niveles()
{	///////////////////////////////////////////////////////////////////////////////////////////////////////
//	   Function: uf_init_niveles
//	     Access: public
//	    Returns: vacio
//	Description: Este método realiza una consulta a los formatos de las cuentas
//               para conocer los niveles de la escalera de las cuentas contables
//////////////////////////////////////////////////////////////////////////////////////////////////////
global $io_funciones,$ia_niveles_scg;

$ls_formato=""; $li_posicion=0; $li_indice=0;
$dat_emp=$_SESSION["la_empresa"];
//contable
$ls_formato = trim($dat_emp["formcont"])."-";
$li_posicion = 1 ;
$li_indice   = 1 ;
$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
do
{
	$ia_niveles_scg[$li_indice] = $li_posicion;
	$li_indice   = $li_indice+1;
	$li_posicion = $io_funciones->uf_posocurrencia($ls_formato, "-" , $li_indice ) - $li_indice;
} while ($li_posicion>=0);
}// end function uf_init_niveles
//-----------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/ezpdf/class.ezpdf.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funciones=new class_funciones();
require_once("class_funciones_scg.php");
$io_fun_scg=new class_funciones_scg();
$ls_tiporeporte="0";
$ls_bolivares="";

if (array_key_exists("tiporeporte",$_GET))
{
	$ls_tiporeporte=$_GET["tiporeporte"];
}
require_once("sigesp_scg_reporte.php");
$io_report  = new sigesp_scg_reporte();
$ls_bolivares ="Bs.";
$ia_niveles_scg[0]="";
uf_init_niveles();
$li_total=count((array)$ia_niveles_scg)-1;
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
$ls_comprobante=$_GET["comprobante"];
$ls_procede=$_GET["procede"];
//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
$ls_titulo=" <b>COMPROBANTE  CONTABLE</b> ";
//--------------------------------------------------------------------------------------------------------------------------------
// Cargar el dts_cab con los datos de la cabecera del reporte( Selecciono todos comprobantes )
$lb_valido=uf_insert_seguridad("<b>Comprobante Contable </b>"); // Seguridad de Reporte
if($lb_valido)
{
	$lb_valido=$io_report->uf_scg_comprobante($ls_procede,$ls_comprobante);
}
if($io_report->rs_data->EOF) // Existe algún error ó no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else // Imprimimos el reporte
{
	set_time_limit(1800);
	$io_pdf=new Cezpdf('LETTER','portrait'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->ezSetCmMargins(3.5,3,3,3); // Configuración de los margenes en centímetros
	uf_print_encabezado_pagina($ls_titulo,$io_pdf); // Imprimimos el encabezado de la página
	$io_pdf->ezStartPageNumbers(550,50,10,'','',1); // Insertar el número de página
	$li_tot=$io_report->rs_data->RowCount();
	$ld_totald=0;
	$ld_totalh=0;
	$li_i=0;
	while(!$io_report->rs_data->EOF)
	{
		$li_i++;
		$io_pdf->transaction('start'); // Iniciamos la transacción
		$thisPageNum=$io_pdf->ezPageCount;
		$ld_totaldebe=0;
		$ld_totalhaber=0;
		$ls_comprobante=$io_report->rs_data->fields["comprobante"];
		$ldt_fecha=$io_report->rs_data->fields["fecha"];
		$ls_procede=$io_report->rs_data->fields["procede"];
		$ls_ced_bene=$io_report->rs_data->fields["ced_bene"];
		$ls_cod_pro=$io_report->rs_data->fields["cod_pro"];
		$ls_nomproben=$io_report->rs_data->fields["nombre"];
		$ls_tipo_destino=$io_report->rs_data->fields["tipo_destino"];

		$ls_codban=$io_report->rs_data->fields["codban"];
		$ls_ctaban=$io_report->rs_data->fields["ctaban"];

		$ldt_fec=$io_funciones->uf_convertirfecmostrar($ldt_fecha);
		uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomproben,$ldt_fec,$io_pdf); // Imprimimos la cabecera del registro
		$lb_valido=$io_report->uf_scg_comprobante_detalle($ls_procede,$ls_comprobante,$ldt_fecha,$ls_codban,$ls_ctaban);
		if($lb_valido)
		{
			$li_s=0;
			while(!$io_report->rs_data_comp->EOF)
			{
				$ls_comprobante=$io_report->rs_data_comp->fields["comprobante"];
				$ls_sc_cuenta=trim($io_report->rs_data_comp->fields["sc_cuenta"]);
				$li_totfil=0;
				$as_cuenta="";
				for($li=$li_total;$li>1;$li--)
				{
					$li_ant=$ia_niveles_scg[$li-1];
					$li_act=$ia_niveles_scg[$li];
					$li_fila=$li_act-$li_ant;
					$li_len=strlen($ls_sc_cuenta);
					$li_totfil=$li_totfil+$li_fila;
					$li_inicio=$li_len-$li_totfil;
					if($li==$li_total)
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila);
					}
					else
					{
						$as_cuenta=substr($ls_sc_cuenta,$li_inicio,$li_fila)."-".$as_cuenta;
					}
				}
				$li_fila=$ia_niveles_scg[1]+1;
				$as_cuenta=substr($ls_sc_cuenta,0,$li_fila)."-".$as_cuenta;

				$ls_procede_doc=$io_report->rs_data_comp->fields["procede_doc"];
				$ls_documento=$io_report->rs_data_comp->fields["documento"];
				$ls_debhab=$io_report->rs_data_comp->fields["debhab"];
				$ld_monto=$io_report->rs_data_comp->fields["monto"];
				$ls_denominacion=$io_report->rs_data_comp->fields["denominacion"];
				$ls_CMP_descripcion=$io_report->rs_data_comp->fields["cmp_descripcion"];
				if($ls_debhab=='D')
				{
					$ld_debe=number_format($ld_monto,2,",",".");
					$ld_totaldebe=$ld_totaldebe+$ld_monto;
					$ld_haber=" ";
				}
				if($ls_debhab=='H')
				{
					$ld_haber=number_format($ld_monto,2,",",".");
					$ld_totalhaber=$ld_totalhaber+$ld_monto;
					$ld_debe=" ";
				}
					
				$ls_documentoproc=$ls_procede_doc."-".$ls_documento;
				$li_s++;
				$la_data[$li_s]=array('cuenta'=>$as_cuenta,'denominacion'=>$ls_denominacion,'descripcion'=>$ls_CMP_descripcion,'documento'=>$ls_documentoproc,'debe'=>$ld_debe,'haber'=>$ld_haber);
				$io_report->rs_data_comp->MoveNext();
			}
			 
			uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
			$ld_totald=$ld_totald+$ld_totaldebe;
			$ld_totalh=$ld_totalh+$ld_totalhaber;

			$ld_totaldebe=number_format($ld_totaldebe,2,",",".");
			$ld_totalhaber=number_format($ld_totalhaber,2,",",".");
			$ld_totalde=$ld_totaldebe;
			$ld_totalha=$ld_totalhaber;

			uf_print_pie_cabecera($ld_totaldebe,$ld_totalhaber,1,$io_pdf); // Imprimimos pie de la cabecera
			$ld_totaldebe=str_replace('.','',$ld_totaldebe);
			$ld_totaldebe=str_replace(',','.',$ld_totaldebe);
			$ld_totalhaber=str_replace('.','',$ld_totalhaber);
			$ld_totalhaber=str_replace(',','.',$ld_totalhaber);

		}

		if ($io_pdf->ezPageCount==$thisPageNum)
		{// Hacemos el commit de los registros que se desean imprimir
			$io_pdf->transaction('commit');
		}
		else
		{// Hacemos un rollback de los registros, agregamos una nueva página y volvemos a imprimir
			if($thisPageNum==1)
			{
				$io_pdf->transaction('commit');
			}
			else
			{
				$io_pdf->transaction('rewind');
				$io_pdf->ezNewPage(); // Insertar una nueva página
				uf_print_cabecera($ls_procede,$ls_comprobante,$ls_nomproben,$ldt_fec,$io_pdf); // Imprimimos la cabecera del registro
				uf_print_detalle($la_data,$io_pdf); // Imprimimos el detalle
				uf_print_pie_cabecera($ld_totalde,$ld_totalha,1,$io_pdf); // Imprimimos pie de la cabecera
			}
		}
		if($li_i==$io_report->rs_data->RowCount())
		{
			$ld_totald=number_format($ld_totald,2,",",".");
			$ld_totalh=number_format($ld_totalh,2,",",".");
			uf_print_pie_cabecera($ld_totald,$ld_totalh,2,$io_pdf); // Imprimimos pie de la cabecera
		}
		unset($la_data);
		$io_report->rs_data->MoveNext();
	}//for
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
}
unset($io_report);
unset($io_funciones);
?>
