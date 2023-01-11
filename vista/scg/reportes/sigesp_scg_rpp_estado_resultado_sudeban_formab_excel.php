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
if(!array_key_exists("la_logusr",$_SESSION)){
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";
}

// REPORTE CREADO POR OFIMATICA DE VENEZUELA EL 23/11/2013 PARA EL CLIENTE SOGAMPI
//
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
	$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_estado_resultado_formab.html",$ls_descripcion);
	return $lb_valido;
}
//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_init_niveles()
	{	///////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_init_niveles
		//	     Access: public
		//	    Returns: vacio	 
		//	Description: Este método realiza una consulta a los formatos de las cuentas
		//               para conocer los niveles de la escalera de las cuentas contables  
		//	   Creado Por: OFIMATICA DE VENEZUELA (Lcdo. Anibal Barraez)
		// Fecha Creación: 26/09/2013
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

function uf_is_negative($ad_monto,$ai_decimales=0)
{
    if ($ai_decimales==0)
	{
	   return number_format($ad_monto,0,".","");
	}
	else
	{
	   return number_format($ad_monto,2,".","");
	}
	
}
//---------------------------------------------------------------------------------------------------------------------------
// para crear el libro excel
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
$lo_archivo = tempnam("/tmp", "estado_resultado_sudeban_formab.xls");
$lo_libro = new writeexcel_workbookbig($lo_archivo);
$lo_hoja = &$lo_libro->addworksheet();
//---------------------------------------------------------------------------------------------------------------------------
// para crear la data necesaria del reporte
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("class_funciones_scg.php");
require_once("sigesp_scg_reporte.php");

$io_funciones = new class_funciones();
$io_fecha     = new class_fecha();
$io_fun_scg   = new class_funciones_scg();
$io_report    = new sigesp_scg_reporte();
$ia_niveles_scg[0]="";			
uf_init_niveles();
$li_total=count((array)$ia_niveles_scg)-1;
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
$ls_titulo="ESTADO DE RESULTADOS (FORMA B)";
$ls_titulo0="";
$ldt_periodo=$_SESSION["la_empresa"]["periodo"];
$li_ano=substr($ldt_periodo,0,4);
		
$ls_etiqueta=$_GET["tipo"];
if($ls_etiqueta=="Mensual")
{
	$ls_combo=$_GET["mesdes"];
	$ls_combomes=$_GET["meshas"];
	$li_mesdes=substr($ls_combo,0,2);
	$li_meshas=substr($ls_combomes,0,2); 
    $ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas)." ".$li_ano;
	$ls_nombre_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes)." ".$li_ano;
	$ls_nombre_meshas=$io_fecha->uf_load_nombre_mes($li_meshas)." ".$li_ano;	
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01";
	}
	else
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01"." 00:00:00";
	}
	$ls_last_day_des=$io_fecha->uf_last_day($li_mesdes,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinihas=$li_ano."-".$li_meshas."-01";
	}
	else
	{
		$ldt_fecinihas=$li_ano."-".$li_meshas."-01"." 00:00:00";
	}
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);		
}
else
{
	$ls_combo=$_GET["mesdes"];
	$li_mesdes=substr($ls_combo,0,2);
	$li_meshas=substr($ls_combo,3,2); 
	$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas)." ".$li_ano;
	$ls_nombre_mesdes=$ls_meses;
	$ls_combomes=$_GET["meshas"];
	$li_mesdesf=substr($ls_combomes,0,2);
	$li_meshasf=substr($ls_combomes,3,2); 
    $ls_meses=$ls_meses."  /   ".$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf)." ".$li_ano;
	$ls_nombre_meshas=$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf)." ".$li_ano;
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01";
	}
	else
	{
		$ldt_fecinides=$li_ano."-".$li_mesdes."-01"." 00:00:00";
	}
	$ls_last_day_des=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	if($_SESSION["ls_gestor"]=='INFORMIX')
	{
		$ldt_fecinihas=$li_ano."-".$li_mesdesf."-01";
	}
	else
	{
		$ldt_fecinihas=$li_ano."-".$li_mesdesf."-01"." 00:00:00";
	}
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshasf,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);	
		
}

//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
// MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_nombre=$_SESSION["la_empresa"]["titulo"];
$ls_rif=$_SESSION["la_empresa"]["rifemp"];
// FIN DE LO MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA
$ld_fecdes=$io_funciones->uf_convertirfecmostrar($fecdes);
$ld_fechas=$io_funciones->uf_convertirfecmostrar($fechas);
$ls_titulo1=" ".$ls_nombre." ";
// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_titulo2=" RIF: ".$ls_rif." ";
$ls_titulo3=" ".$ls_meses." ";
// FIN DE LO AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA
$ls_titulo4="(Expresado en Bs.)";
$lb_valido=uf_insert_seguridad("Estado de Resultado (FORMA B) Comparado en PDF"); // Seguridad de Reporte
if($lb_valido)
{
	$lb_valido=$io_report->uf_scg_reporte_estado_de_resultado_sudeban($ldt_fecinides,$ldt_fecfindes,$ldt_fecinihas,$ldt_fecfinhas);
	
}

if(($io_report->rs_data_comp->EOF)||(!$lb_valido)) // Existe algún error ó no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else
{
	$lo_encabezado= &$lo_libro->addformat();
	$lo_encabezado->set_bold();
	$lo_encabezado->set_font("Verdana");
	$lo_encabezado->set_align('center');
	$lo_encabezado->set_size('11');
	$lo_titulo= &$lo_libro->addformat();
	$lo_titulo->set_bold();
	$lo_titulo->set_font("Verdana");
	$lo_titulo->set_align('center');
	$lo_titulo->set_size('9');
	$lo_datacenter= &$lo_libro->addformat();
	$lo_datacenter->set_font("Verdana");
	$lo_datacenter->set_align('center');
	$lo_datacenter->set_size('9');
	$lo_dataleft= &$lo_libro->addformat();
//  $lo_dataleft->set_text_wrap();
	$lo_dataleft->set_font("Verdana");
	$lo_dataleft->set_align('left');
	$lo_dataleft->set_size('9');
	$lo_dataright= &$lo_libro->addformat(array('num_format' => '#,##0'));
	$lo_dataright->set_font("Verdana");
	$lo_dataright->set_align('right');
	$lo_dataright->set_size('9');

	$lo_hoja->set_column(0,0,20);
	$lo_hoja->set_column(1,1,80);
	$lo_hoja->set_column(2,2,20);
	$lo_hoja->set_column(3,3,20);
	$lo_hoja->set_column(4,4,20);
	$lo_hoja->set_column(5,5,20);
	
	$lo_hoja->write(0,1,$ls_titulo1,$lo_encabezado);
	$lo_hoja->write(1,1,$ls_titulo2,$lo_encabezado);
	$lo_hoja->write(2,1,$ls_titulo,$lo_encabezado);
	$lo_hoja->write(3,1,$ls_titulo3,$lo_encabezado);
	$lo_hoja->write(4,1,$ls_titulo4,$lo_encabezado);
	$li_row=7;
	$lo_hoja->write($li_row,2,$ls_nombre_mesdes,$lo_titulo);
	$lo_hoja->write($li_row,3,$ls_nombre_meshas,$lo_titulo);
	$lo_hoja->write($li_row,4,'Variacion Bs.',$lo_titulo);
	$lo_hoja->write($li_row,5,'Variacion %',$lo_titulo);
    $li_row=$li_row+2;

    // BUSCAMOS LA TASA DE CAMBIO VIGENTE A LA FECHA DE LA MONEDA POR DEFECTO (DOLAR)
    $li_tasa_cambio=$io_report->uf_buscar_tasa('002');
	//

	//totales
	$ld_total_margenfinancierobruto=0;
	$ld_total_margenfinancieroneto=0;
	$ld_total_510=0;
	$ld_total_520=0;
	$ld_total_410=0;
	$ld_total_420=0;
	$ld_total_440=0;
	$ld_total_441=0;
	$ld_total_gasoper=0;
	$ld_total_530=0;
	$ld_total_430=0;
	$ld_total_540=0;
	$ld_total_450=0;
	$ld_total_470=0;

	$ld_total_margenfinancierobruto_has=0;
	$ld_total_margenfinancieroneto_has=0;
	$ld_total_510_has=0;
	$ld_total_520_has=0;
	$ld_total_410_has=0;
	$ld_total_420_has=0;
	$ld_total_440_has=0;
	$ld_total_441_has=0;
	$ld_total_gasoper_has=0;
	$ld_total_530_has=0;
	$ld_total_430_has=0;
	$ld_total_540_has=0;
	$ld_total_450_has=0;
	$ld_total_470_has=0;
	
	//arreglos de data cuentas nivel 3
	$la_data_ingfin   = array();
	$la_data_ingfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_gasfin   = array();
	$la_data_gasfin[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_inggas = array();
	$la_data_inggas[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_441    = array();
	$la_data_441[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	//arreglos de data cuentas nivel 2
	$la_data_510   = array();
    $la_data_510[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_410   = array();
    $la_data_410[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_520   = array();
	$la_data_520[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// AGREGADO POR OFIMATICA DE VENEZUELA 26/03/2013
	$la_data_420   = array();
	$la_data_420[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA	
	$la_data_440 = array();
	$la_data_440[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_530 = array();
	$la_data_530[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_430 = array();
	$la_data_430[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_540 = array();
	$la_data_540[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_450 = array();
	$la_data_450[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_470 = array();
	$la_data_470[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	//arreglos de data cuentas totales
	$la_data_totalmarfinbru = array();
	$la_data_totalmarfinnet = array();
	$la_data_menos          = array();
	$la_data_gastosoperati  = array();
	$la_data_margeninter    = array();

	//digito tipo de cuenta
	$ls_activo       = $_SESSION["la_empresa"]["activo"];
	$ls_pasivo       = $_SESSION["la_empresa"]["pasivo"];
	$ls_patrimonio   = $_SESSION["la_empresa"]["capital"];
	$ls_ingreso      = $_SESSION["la_empresa"]["ingreso"];
	$ls_gasto        = $_SESSION["la_empresa"]["gasto"];
	while(!$io_report->rs_data_comp->EOF)
	{
		$digtipcuenta = substr($io_report->rs_data_comp->fields["sc_cuenta"],0,1);
		$codcuenta    = substr($io_report->rs_data_comp->fields["sc_cuenta"],0,3);
		$denominacion = $io_report->rs_data_comp->fields["denominacion"];
		$debedes      = $io_report->rs_data_comp->fields["debedes"];
		$haberdes     = $io_report->rs_data_comp->fields["haberdes"];
		$montodes     = $io_report->rs_data_comp->fields["saldodes"];
		$debehas      = $io_report->rs_data_comp->fields["debehas"];
		$haberhas     = $io_report->rs_data_comp->fields["haberhas"];
		$montohas     = $io_report->rs_data_comp->fields["saldohas"];		
		$nivel        = $io_report->rs_data_comp->fields["nivel"];
		
		switch ($digtipcuenta) 
		{
			case $ls_activo:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_pasivo:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_patrimonio:
				$montodes = abs($montodes);
				$montohas = abs($montohas);
				break;
			
			case $ls_ingreso:
				if($debedes<$haberdes)
				{
					$montodes = abs($montodes);
				}
				if($debehas<$haberhas)
				{
					$montohas = abs($montohas);
				}
				
				break;
				
			case $ls_gasto:
				if($debedes>$haberdes)
				{
					$montodes = abs($montodes);
				}
				if($debehas>$haberhas)
				{
					$montohas = abs($montohas);
				}				
				break;
		}
		$li_variacion_bs=($montohas-$montodes);
		if($montodes!=0)
		{
		   $li_variacion=($li_variacion_bs/$montodes)*100;
		}
		else
		{
		   $li_variacion=0;
		}
		$arr_ingfin   = array("511","512","513","514","519");
		$arr_gasfin   = array("414","415","419");
		$arr_inggas   = array("421","422","423");
		
		if ($nivel==2) 
		{
			if ($codcuenta=="510") 
			{
				$la_data_510[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_510 = $montodes;
				$ld_total_510_has = $montohas;
			}
			elseif ($codcuenta=="410")
			{
				$la_data_410[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_410=$montodes;
				$ld_total_410_has=$montohas;
			}
			elseif ($codcuenta=="520")
			{
				$la_data_520[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_520  = $ld_total_520 + $montodes;
				$ld_total_520_has  = $ld_total_520_has + $montohas;
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA 26/03/2013
			elseif ($codcuenta=="420")
			{
				$la_data_420[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_420=$montodes;
				$ld_total_420_has=$montohas;
			}
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
			elseif ($codcuenta=="440")
			{
				$la_data_440[] = array('cuenta'=>'','denominacion'=>'GASTOS DE TRANSFORMACION','mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_440=$montodes;
				$ld_total_440_has=$montohas;
			}
			elseif ($codcuenta=="530")
			{
				$la_data_530[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_530  = $montodes;
				$ld_total_530_has  = $montohas;
			}
			elseif ($codcuenta=="430")
			{
				$la_data_430[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_430  = $montodes;
				$ld_total_430_has  = $montohas;
			}
			elseif ($codcuenta=="540")
			{
				$la_data_540[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_540  = $montodes;
				$ld_total_540_has  = $montohas;
			}
			elseif ($codcuenta=="450")
			{
				$la_data_450[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_450  = $montodes;
				$ld_total_450_has  = $montohas;
			}
			elseif ($codcuenta=="470")
			{
				$la_data_470[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_470  = $montodes;
				$ld_total_470_has  = $montohas;
			}
		}
		elseif ($nivel==3)
		{
			if (in_array($codcuenta, $arr_ingfin)) 
			{
				$la_data_ingfin[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_gasfin))
			{
				$la_data_gasfin[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));	
			}
			elseif (in_array($codcuenta, $arr_inggas))
			{
				$la_data_inggas[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
//				$ld_total_420     = $ld_total_420 + $monto;
			}
			elseif ($codcuenta=="441")
			{
				$la_data_441[] = array('cuenta'=>$codcuenta." . 00",'denominacion'=>$denominacion,'mesdes'=>uf_is_negative($montodes),'meshas'=>uf_is_negative($montohas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_441  = $montodes;
				$ld_total_441_has  = $montohas;
			}
		}
		$io_report->rs_data_comp->MoveNext();
	}
	$ld_total_margenfinancierobruto = $ld_total_510-$ld_total_410;
	$ld_total_margenfinancierobruto_has = $ld_total_510_has-$ld_total_410_has;
	$ld_total_margenfinancieroneto  = ($ld_total_margenfinancierobruto + $ld_total_520) - $ld_total_420;
	$ld_total_margenfinancieroneto_has  = ($ld_total_margenfinancierobruto_has + $ld_total_520_has) - $ld_total_420_has;
	$la_data_menos[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_menos[] = array('cuenta'=>'','denominacion'=>'MENOS:','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$ld_total_gasoper = $ld_total_440 - $ld_total_441;
	$ld_total_gasoper_has = $ld_total_440_has - $ld_total_441_has;
	$li_variacion_bs=$ld_total_gasoper_has-$ld_total_gasoper;
	if($ld_total_gasoper!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_gasoper)*100;
	}
	else
	{
	   $li_variacion=0;
	}
	$la_data_gastosoperati[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_gastosoperati[] = array('cuenta'=>'     *','denominacion'=>'GASTOS GENERALES Y ADMINISTRATIVOS','mesdes'=>uf_is_negative($ld_total_gasoper),'meshas'=>uf_is_negative($ld_total_gasoper_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	$ld_total_marinter = $ld_total_margenfinancieroneto - $ld_total_440;
	$ld_total_marinter_has = $ld_total_margenfinancieroneto_has - $ld_total_440_has;
	$ld_total_marnegocio = ($ld_total_marinter + $ld_total_530) - $ld_total_430;
	$ld_total_marnegocio_has = ($ld_total_marinter_has + $ld_total_530_has) - $ld_total_430_has;
	$ld_total_brutoantimp = ($ld_total_marnegocio+ $ld_total_540) - $ld_total_450;
	$ld_total_brutoantimp_has = ($ld_total_marnegocio_has+ $ld_total_540_has) - $ld_total_450_has;
	$ld_total_neto = $ld_total_brutoantimp - $ld_total_470;
    $ld_total_neto_has = $ld_total_brutoantimp_has - $ld_total_470_has;	
	
		
	//IMPRIMIENDO LAS CUENTAS 510
	$li_total=count((array)$la_data_510);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_510[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_510[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_510[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_510[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_510[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_510[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_510[$li]["variacion"], $lo_dataright);
		}
//		    uf_print_detalle($la_data_510,2,7,$io_pdf);
	}
    $li_row=$li_row+1;			
	$li_total=count((array)$la_data_ingfin);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_ingfin[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_ingfin[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_ingfin[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_ingfin[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_ingfin[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_ingfin[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_ingfin[$li]["variacion"], $lo_dataright);
		}
//      uf_print_detalle($la_data_ingfin,0,6,$io_pdf);
	}		
	$li_row=$li_row+1;
    //IMPRIMIENDO LAS CUENTAS 410	
	$li_total=count((array)$la_data_410);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_410[$li]["cuenta"]!='')
		{			
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_410[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_410[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_410[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_410[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_410[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_410[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_410,2,7,$io_pdf);
	}				
    $li_row=$li_row+1;		
	$li_total=count((array)$la_data_gasfin);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_gasfin[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_gasfin[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_gasfin[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_gasfin[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_gasfin[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_gasfin[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_gasfin[$li]["variacion"], $lo_dataright);
		}
//     uf_print_detalle($la_data_gasfin,0,6,$io_pdf);
	}					
	$li_row=$li_row+2;
	//IMPRIMIENDO MARGEN FINANCIERO BRUTO
	$li_variacion_bs=$ld_total_margenfinancierobruto_has-$ld_total_margenfinancierobruto;
	if($ld_total_margenfinancierobruto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_margenfinancierobruto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$lo_hoja->write($li_row, 0, "MARGEN FINANCIERO BRUTO", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_margenfinancierobruto), $lo_dataright);	
	$lo_hoja->write($li_row, 3, uf_is_negative($ld_total_margenfinancierobruto_has), $lo_dataright);	
	$lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);	
	$lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);					
//	uf_print_subtitulo_monto("MARGEN FINANCIERO BRUTO", uf_is_negative($ld_total_margenfinancierobruto), $io_pdf);
    $li_row=$li_row+1;	
	$li_total=count((array)$la_data_520);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_520[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_520[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_520[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_520[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_520[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_520[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_520[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_520,0,6,$io_pdf);
	}						
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$li_total=count((array)$la_data_420);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_420[$li]["cuenta"]!='')
		{			
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_420[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_420[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_420[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_420[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_420[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_420[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_420,0,6,$io_pdf);
	}							
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
	$li_total=count((array)$la_data_inggas);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_inggas[$li]["cuenta"]!='')
		{			
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_inggas[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_inggas[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_inggas[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_inggas[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_inggas[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_inggas[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_inggas,0,6,$io_pdf);
	}					
	$li_row=$li_row+2;
	//IMPRIMIENDO MARGEN FINANCIERO NETO
	$li_variacion_bs=$ld_total_margenfinancieroneto_has-$ld_total_margenfinancieroneto;
	if($ld_total_margenfinancieroneto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_margenfinancieroneto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$lo_hoja->write($li_row, 0, "MARGEN FINANCIERO NETO", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_margenfinancieroneto), $lo_dataright);	
    $lo_hoja->write($li_row, 3, uf_is_negative($ld_total_margenfinancieroneto_has), $lo_dataright);				
    $lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);		
    $lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);		
//	uf_print_subtitulo_monto("MARGEN FINANCIERO NETO", uf_is_negative($ld_total_margenfinancieroneto), $io_pdf);
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_menos);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_menos[$li]["denominacion"]!='')
		{			
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_menos[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_menos[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_menos[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_menos[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_menos[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_menos[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_menos,0,6,$io_pdf);
	}						
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_440);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_440[$li]["denominacion"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_440[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_440[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_440[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_440[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_440[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_440[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_440,0,6,$io_pdf);
	}					
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_441);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_441[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_441[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_441[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_441[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_441[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_441[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_441[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_441,0,6,$io_pdf);
	}					
	$li_total=count((array)$la_data_gastosoperati);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_gastosoperati[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_gastosoperati[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_gastosoperati[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_gastosoperati[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_gastosoperati[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_gastosoperati[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_gastosoperati[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_gastosoperati,0,6,$io_pdf);
	}					
	$li_row=$li_row+2;	
	//IMPRIMIENDO MARGEN DE INTERMEDIACION
	$li_variacion_bs=$ld_total_marinter_has-$ld_total_marinter;
	if($ld_total_marinter!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_marinter)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$lo_hoja->write($li_row, 0, "MARGEN DE INTERMEDIACION", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_marinter), $lo_dataright);	
	$lo_hoja->write($li_row, 3, uf_is_negative($ld_total_marinter_has), $lo_dataright);	
	$lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);	
	$lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);						
//	uf_print_subtitulo_monto("MARGEN DE INTERMEDIACION", uf_is_negative($ld_total_marinter), $io_pdf);
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_530);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_530[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_530[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_530[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_530[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_530[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_530[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_530[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_530,0,6,$io_pdf);
	}	
	$li_total=count((array)$la_data_430);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_430[$li]["cuenta"]!='')
		{			
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_430[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_430[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_430[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_430[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_430[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_430[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_430,0,6,$io_pdf);
	}				
	$li_row=$li_row+2;		
	//IMPRIMIENDO MARGEN DEL NEGOCIO 
	$li_variacion_bs=$ld_total_marnegocio_has-$ld_total_marnegocio;
	if($ld_total_marnegocio!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_marnegocio)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$lo_hoja->write($li_row, 0, "MARGEN DEL NEGOCIO", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_marnegocio), $lo_dataright);	
	$lo_hoja->write($li_row, 3, uf_is_negative($ld_total_marnegocio_has), $lo_dataright);	
	$lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);	
	$lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);							
//	uf_print_subtitulo_monto("MARGEN DEL NEGOCIO", uf_is_negative($ld_total_marnegocio), $io_pdf);
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_540);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_540[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_540[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_540[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_540[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_540[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_540[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_540[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_540,0,6,$io_pdf);
	}	
	$li_total=count((array)$la_data_450);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_450[$li]["cuenta"]!='')
		{		
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_450[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_450[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_450[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_450[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_450[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_450[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_450,0,6,$io_pdf);
	}			
	$li_row=$li_row+2;
	$li_variacion_bs=$ld_total_brutoantimp_has-$ld_total_brutoantimp;
	if($ld_total_brutoantimp!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_brutoantimp)*100;
	}
	else
	{
	   $li_variacion=0;
	}	

	$lo_hoja->write($li_row, 0, "RESULTADO BRUTO ANTES DE IMPUESTO", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_brutoantimp), $lo_dataright);		
	$lo_hoja->write($li_row, 3, uf_is_negative($ld_total_brutoantimp_has), $lo_dataright);		
	$lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);		
	$lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);					
//	uf_print_subtitulo_monto("RESULTADO BRUTO ANTES DE IMPUESTO", uf_is_negative($ld_total_brutoantimp), $io_pdf);
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_470);
	for($li=1;$li<=$li_total;$li++)
	{
	    if($la_data_470[$li]["cuenta"]!='')
		{				
			$li_row=$li_row+1;
            $lo_hoja->write_string($li_row, 0, $la_data_470[$li]["cuenta"], $lo_dataleft);				
		    $lo_hoja->write($li_row, 1, $la_data_470[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_470[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_470[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_470[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_470[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_470,0,6,$io_pdf);
	}	
	$li_row=$li_row+2;
	$li_variacion_bs=$ld_total_neto_has-$ld_total_neto;
	if($ld_total_neto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_neto)*100;
	}
	else
	{
	   $li_variacion=0;
	}		

	$lo_hoja->write($li_row, 0, "RESULTADO NETO", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, uf_is_negative($ld_total_neto), $lo_dataright);		
	$lo_hoja->write($li_row, 3, uf_is_negative($ld_total_neto_has), $lo_dataright);		
	$lo_hoja->write($li_row, 4, uf_is_negative($li_variacion_bs), $lo_dataright);		
	$lo_hoja->write($li_row, 5, uf_is_negative($li_variacion), $lo_dataright);					
//	uf_print_subtitulo_monto("RESULTADO NETO", uf_is_negative($ld_total_neto), $io_pdf);
    $li_row=$li_row+1;		
	$lo_hoja->write($li_row, 0, "* 440 Excepto 441", $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, "", $lo_dataright);			
//  $io_pdf->addText(49,160,7,"* 440 Excepto 441"); // Agregar el título	
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"estado_resultado_sudeban_formab.xls\"");
	header("Content-Disposition: inline; filename=\"estado_resultado_sudeban_formab.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);

}
unset($io_report);
unset($io_funciones);
print("<script language=JavaScript>");
print(" close();");
print("</script>");
?>