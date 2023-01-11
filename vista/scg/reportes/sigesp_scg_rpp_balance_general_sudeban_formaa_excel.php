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
//
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
	$lb_valido=$io_fun_scg->uf_load_seguridad_reporte("SCG","sigesp_vis_scg_r_balance_general_formaa.html",$ls_descripcion);
	return $lb_valido;
}
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
//---------------------------------------------------------------------------------------------------------------------------
// para crear el libro excel
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_workbookbig.inc.php");
require_once ("../../../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");
$lo_archivo = tempnam("/tmp", "balance_general_sudeban_formaa.xls");
$lo_libro = new writeexcel_workbookbig($lo_archivo);
$lo_hoja = &$lo_libro->addworksheet();
//---------------------------------------------------------------------------------------------------------------------------
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../../shared/class_folder/class_sigesp_int.php");
require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
require_once("class_funciones_scg.php");
require_once("sigesp_scg_class_bal_general.php");

$io_report  = new sigesp_scg_class_bal_general();
$io_funciones=new class_funciones();
$io_fecha=new class_fecha();
$io_fun_scg=new class_funciones_scg();
$ia_niveles_scg[0]="";			
uf_init_niveles();
$li_total=count((array)$ia_niveles_scg)-1;
//--------------------------------------------------  Parámetros para Filtar el Reporte  -----------------------------------------
$ls_titulo=" BALANCE GENERAL(FORMA A) ";
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
    $ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas).$li_ano;
	$ls_nombre_mesdes=$io_fecha->uf_load_nombre_mes($li_mesdes).$li_ano;
	$ls_nombre_meshas=$io_fecha->uf_load_nombre_mes($li_meshas).$li_ano;	
	$ls_last_day_des=$io_fecha->uf_last_day($li_mesdes,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);		
}
else
{
	$ls_combo=$_GET["mesdes"];
	$li_mesdes=substr($ls_combo,0,2);
	$li_meshas=substr($ls_combo,3,2); 
	$ls_meses=$io_report->uf_nombre_mes_desde_hasta($li_mesdes,$li_meshas).$li_ano;
	$ls_nombre_mesdes=$ls_meses;
	$ls_combomes=$_GET["meshas"];
	$li_mesdesf=substr($ls_combomes,0,2);
	$li_meshasf=substr($ls_combomes,3,2); 
    $ls_meses=$ls_meses."  /   ".$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf).$li_ano;
	$ls_nombre_meshas=$io_report->uf_nombre_mes_desde_hasta($li_mesdesf,$li_meshasf).$li_ano;
	$ls_last_day_des=$io_fecha->uf_last_day($li_meshas,$li_ano);
	$ldt_fecfindes=$io_funciones->uf_convertirdatetobd($ls_last_day_des);
	$ls_last_day_has=$io_fecha->uf_last_day($li_meshasf,$li_ano);
	$ldt_fecfinhas=$io_funciones->uf_convertirdatetobd($ls_last_day_has);	
		
}

//----------------------------------------------------  Parámetros del encabezado  -----------------------------------------------
// MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_nombre=$_SESSION["la_empresa"]["titulo"];
$ls_rif=$_SESSION["la_empresa"]["rifemp"];
// FIN DE LO MODIFICADO Y AGREGADO POR OFIMATICA DE VENEZUELA
$ls_titulo1=" ".$ls_nombre;
// AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA EL 11/03/2013
$ls_titulo2=" RIF: ".$ls_rif;
$ls_titulo3=" ".$ls_meses;
// FIN DE LO AGREGADO Y MODIFICADO POR OFIMATICA DE VENEZUELA
$ls_titulo4=" (Expresado en Bs.) ";
//--------------------------------------------------------------------------------------------------------------------------------

$ls_pasivo=$_SESSION["la_empresa"]["pasivo"];
$ls_resultado=$_SESSION["la_empresa"]["resultado"];
$ls_capital=$_SESSION["la_empresa"]["capital"];
$ls_acreedora=trim($_SESSION["la_empresa"]["orden_h"]);

//--------------------------------------------------------------------------------------------------------------------------------
// Cargar datastore con los datos del reporte
$lb_valido=uf_insert_seguridad("Balance General (FORMA A) Comparado en PDF"); // Seguridad de Reporte
if($lb_valido)
{
	$rs_data=$io_report->uf_balance_general_sudeban($ldt_fecfindes,$ldt_fecfinhas);
}

if($rs_data->EOF) // no hay registros
{
	print("<script language=JavaScript>");
	print(" alert('No hay nada que Reportar');");
	print(" close();");
	print("</script>");
}
else// Imprimimos el reporte
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
	//$lo_dataleft->set_text_wrap();
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
   
	//totales
	$ld_total_pasivopatrimonio=0;
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
	
	$ld_total_pasivopatrimonio_has=0;
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
	$la_data_diponibilidades   = array();
	$la_data_diponibilidades[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_inversiones       = array();
	$la_data_inversiones[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_deudores	       = array();
	$la_data_deudores[]        = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_intereses         = array();
	$la_data_intereses[]       = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_otrasinversion    = array();
	$la_data_otrasinversion[]  = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_pasivopatri       = array();
	$la_data_pasivopatri[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_interesesxpagar   = array();
	$la_data_interesesxpagar[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$la_data_acumyotrospasivos   = array();
	$la_data_acumyotrospasivos[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_aportespatrim       = array();
	$la_data_aportespatrim[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_resultadosacum      = array();
	$la_data_resultadosacum[]    = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
	
	//arreglos de data cuentas nivel 2
	$la_data_diponibilidades2   = array();
	$la_data_diponibilidades2[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_inversiones2       = array();
	$la_data_inversiones2[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_deudores2          = array();
	$la_data_deudores2[]        = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_intereses2         = array();
	$la_data_intereses2[]       = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_otrasinversion2    = array();
	$la_data_otrasinversion2[]  = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_pasivopatri2       = array();
	$la_data_pasivopatri2[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_otraobligacion     = array();
	$la_data_otraobligacion[]   = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_interesesxpagar2   = array();
	$la_data_interesesxpagar2[] = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_acumulaciones      = array();
	$la_data_acumulaciones[]    = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_310                = array();
	$la_data_310[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_311                = array();
	$la_data_311[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_330                = array();
	$la_data_330[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');	
	$la_data_340                = array();
	$la_data_340[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_350                = array();
	$la_data_350[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_360                = array();
	$la_data_360[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_370                = array();
	$la_data_370[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_300                = array();
	$la_data_300[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_610                = array();
	$la_data_610[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_620                = array();
	$la_data_620[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_810                = array();
	$la_data_810[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_820                = array();
	$la_data_820[] 				= array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_160                = array();
	$la_data_160[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_170                = array();
	$la_data_170[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_180                = array();
	$la_data_180[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	
	//arreglos de data cuentas nivel 1
	$la_data_totalpasivos       = array();
	$la_data_totalpasivos[]     = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_300                = array();
	$la_data_300[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_100                = array();
	$la_data_100[]              = array('cuenta'=>"",'denominacion'=>"",'mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	
	//informacion adicional
//	$ld_cap_suscrito=$io_report->uf_obtener_capital($ldt_fechas,"001");
//	$ld_cap_nopagado=$io_report->uf_obtener_capital($ldt_fechas,"002");
	//$la_data_311[] = array('cuenta'=>"",'denominacion'=>"CAPITAL SUSCRITO",'saldo'=>uf_is_negative($ld_cap_suscrito));
	//$la_data_311[] = array('cuenta'=>"",'denominacion'=>"CAPITAL NO PAGADO",'saldo'=>uf_is_negative($ld_cap_nopagado));
	
	//digito tipo de cuenta
	$ls_activo       = $_SESSION["la_empresa"]["activo"];
	$ls_pasivo       = $_SESSION["la_empresa"]["pasivo"];
	$ls_patrimonio   = $_SESSION["la_empresa"]["capital"];
	$ls_ingreso      = $_SESSION["la_empresa"]["ingreso"];
	$ls_gasto        = $_SESSION["la_empresa"]["gasto"];
	while (!$rs_data->EOF) 
	{
		$digtipcuenta = substr($rs_data->fields["sc_cuenta"],0,1);
		$codcuenta = substr($rs_data->fields["sc_cuenta"], 0, 3);
		$dencuenta = $rs_data->fields["denominacion"];
		$debedes         = $rs_data->fields["debedes"];
		$haberdes        = $rs_data->fields["haberdes"];
		$salcuentades    = $rs_data->fields["saldodes"];
		$debehas         = $rs_data->fields["debehas"];
		$haberhas        = $rs_data->fields["haberhas"];
		$salcuentahas    = $rs_data->fields["saldohas"];		
		$nivcuenta = $rs_data->fields["nivel"];
		
		if(($codcuenta!="119")&&($codcuenta!="129")&&($codcuenta!="139")&&($codcuenta!="149")&&($codcuenta!="159"))
		{
			switch ($digtipcuenta) 
			{
				case $ls_activo:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);					
					break;
			
				case $ls_pasivo:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);										
					break;
			
				case $ls_patrimonio:
					$salcuentades = abs($salcuentades);
					$salcuentahas = abs($salcuentahas);										
					break;
			
				case $ls_ingreso:
					if($debedes<$haberdes)
					{
						$salcuentades = abs($salcuentades);
					}
					if($debehas<$haberhas)
					{
						$salcuentahas = abs($salcuentahas);
					}					
					break;
				
				case $ls_gasto:
					if($debedes>$haberdes)
					{
						$salcuentades = abs($salcuentades);
					}
					if($debehas>$haberhas)
					{
						$salcuentahas = abs($salcuentahas);
					}					
					break;
			}
		}
		$li_variacion_bs=($salcuentahas-$salcuentades);
		if($salcuentades!=0)
		{
		   $li_variacion=($li_variacion_bs/$salcuentades)*100;
		}
		else
		{
		   $li_variacion=0;
		}		
		
		$arr_diponibilidades = array("111","113","114","116","119");
		$arr_inversiones     = array("121","122","123","124","125","129");
		$arr_deudores        = array("131","132","133","134","139");
		$arr_intereses       = array("141","142","143","144","145","149");
		$arr_otrasinversion  = array("151","159");
		//$arr_pasivopatri = array("240","241","242","243","244","245","246");//MARIAJOSEMORA
		$arr_pasivopatri = array("240");
		$arr_interesesxpagar = array("264","265");
		// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
		$arr_acumyotrospasivos = array("271","272","273","274","275","276"); 
		$arr_aportespatrim   = array("331","333");
		$arr_resultadosacum  = array("361","362");
		// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
		$arr_inggas          = array("421","422","423");
		if ($nivcuenta==1) 
		{
			if ($codcuenta == '100')
			{
				$la_data_100[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>"TOTAL ".$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '200')
			{
				$la_data_totalpasivos[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>"TOTAL DEL ".$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio + $salcuentades;
				$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has + $salcuentahas; 
			}
			elseif ($codcuenta == '300')
			{
				$la_data_300[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>"TOTAL ".$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
				$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio + $salcuentades;
				$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has + $salcuentahas;
			}
		}
		elseif ($nivcuenta==2)
		{
			if($codcuenta == '110')
			{
				$la_data_diponibilidades2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '120')
			{
				$la_data_inversiones2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '130')
			{
				$la_data_deudores2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '140')
			{
				$la_data_intereses2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '150')
			{
				$la_data_otrasinversion2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '160')
			{
				$la_data_160[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '170')
			{
				$la_data_170[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '180')
			{
				$la_data_180[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '240')
			{
				$la_data_pasivopatri2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '250')
			{
				$la_data_otraobligacion[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '260')
			{
				$la_data_interesesxpagar2[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '270')
			{
				$la_data_acumulaciones[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '310')
			{
				$la_data_310[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '330')
			{
				$la_data_330[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '340')
			{
				$la_data_340[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '350')
			{
				$la_data_350[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '360')
			{
				$la_data_360[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '370')
			{
				$la_data_370[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '610')
			{
				$la_data_610[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '620')
			{
				$la_data_620[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '810')
			{
				$la_data_810[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif ($codcuenta == '820')
			{
				$la_data_820[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			if ($codcuenta=="510") 
			{
				$ld_total_510 = $salcuentades;
				$ld_total_510_has = $salcuentahas;
			}
			elseif ($codcuenta=="410")
			{
				$ld_total_410 = $salcuentades;
				$ld_total_410_has = $salcuentahas;
			}
			elseif ($codcuenta=="520")
			{
				$ld_total_520 = $salcuentades;
				$ld_total_520_has = $salcuentahas;
			}
			elseif ($codcuenta=="440")
			{
				$ld_total_440=$salcuentades;
				$ld_total_440_has=$salcuentahas;
			}
			elseif ($codcuenta=="530")
			{
				$ld_total_530  = $salcuentades;
				$ld_total_530_has  = $salcuentahas;
			}
			elseif ($codcuenta=="430")
			{
				$ld_total_430  = $salcuentades;
				$ld_total_430_has  = $salcuentahas;
			}
			elseif ($codcuenta=="540")
			{
				$ld_total_540  = $salcuentades;
				$ld_total_540_has = $salcuentahas;
			}
			elseif ($codcuenta=="450")
			{
				$ld_total_450  = $salcuentades;
				$ld_total_450_has  = $salcuentahas;
			}
			elseif ($codcuenta=="470")
			{
				$ld_total_470  = $salcuentades;
				$ld_total_470_has  = $salcuentahas;
			}
		}
		elseif ($nivcuenta==3)
		{
			if (in_array($codcuenta, $arr_diponibilidades)) 
			{
				$la_data_diponibilidades[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_inversiones)) 
			{
				$la_data_inversiones[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_deudores))
			{
				$la_data_deudores[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_intereses))
			{
				$la_data_intereses[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_otrasinversion))
			{
				$la_data_otrasinversion[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_pasivopatri))
			{
				$la_data_pasivopatri[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_interesesxpagar))
			{
				$la_data_interesesxpagar[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
			elseif (in_array($codcuenta, $arr_acumyotrospasivos))
			{
				$la_data_acumyotrospasivos[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA
			elseif ($codcuenta == '311')
			{
				$la_data_311[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
			elseif (in_array($codcuenta, $arr_aportespatrim))
			{
				$la_data_aportespatrim[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}
			elseif (in_array($codcuenta, $arr_resultadosacum))
			{
				$la_data_resultadosacum[] = array('cuenta'=>$codcuenta.".00",'denominacion'=>$dencuenta,'mesdes'=>uf_is_negative($salcuentades),'meshas'=>uf_is_negative($salcuentahas),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
			}			
			// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA			
			elseif (in_array($codcuenta, $arr_inggas))
			{
				$ld_total_420     = $ld_total_420 + $salcuentades;
				$ld_total_420_has = $ld_total_420_has + $salcuentahas;
			}
		}
		
		$rs_data->MoveNext();
	}

	//CALCULANDO GASTO OPERATIVO
	$ld_total_margenfinancierobruto = $ld_total_510-$ld_total_410;
	$ld_total_margenfinancierobruto_has = $ld_total_510_has-$ld_total_410_has;
	$ld_total_margenfinancieroneto  = ($ld_total_margenfinancierobruto + $ld_total_520) - $ld_total_420;
	$ld_total_margenfinancieroneto_has  = ($ld_total_margenfinancierobruto_has + $ld_total_520_has) - $ld_total_420_has;	
	$ld_total_marinter = $ld_total_margenfinancieroneto - $ld_total_440;
	$ld_total_marinter_has = $ld_total_margenfinancieroneto_has - $ld_total_440_has;	
	$ld_total_marnegocio = ($ld_total_marinter + $ld_total_530) - $ld_total_430;
	$ld_total_marnegocio_has = ($ld_total_marinter_has + $ld_total_530_has) - $ld_total_430_has;	
	$ld_total_brutoantimp = ($ld_total_marnegocio+ $ld_total_540) - $ld_total_450;
	$ld_total_brutoantimp_has = ($ld_total_marnegocio_has+ $ld_total_540_has) - $ld_total_450_has;	
	$ld_total_neto = $ld_total_brutoantimp - $ld_total_470;
	$ld_total_neto_has = $ld_total_brutoantimp_has - $ld_total_470_has;	
	$li_variacion_bs=$ld_total_neto_has-$ld_total_neto;	
	if($ld_total_neto!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_neto)*100;
	}
	else
	{
	   $li_variacion=0;
	}	
	$la_data_gesoperativa   = array();
	$la_data_gesoperativa[] = array('cuenta'=>"",'denominacion'=>"GESTION OPERATIVA",'mesdes'=>uf_is_negative($ld_total_neto),'meshas'=>uf_is_negative($ld_total_neto_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	//CALCULANDO GASTO OPERATIVO
	$ld_total_pasivopatrimonio = $ld_total_pasivopatrimonio+$ld_total_neto;
	$ld_total_pasivopatrimonio_has = $ld_total_pasivopatrimonio_has+$ld_total_neto_has;
	$li_variacion_bs=$ld_total_pasivopatrimonio_has-$ld_total_pasivopatrimonio;	
	if($ld_total_pasivopatrimonio!=0)
	{
	   $li_variacion=($li_variacion_bs/$ld_total_pasivopatrimonio)*100;
	}
	else
	{
	   $li_variacion=0;
	}		
	$la_data_totalpasivopatrimonio[] = array('cuenta'=>'','denominacion'=>'','mesdes'=>'','meshas'=>'','variacion_bs'=>'','variacion'=>'');
	$la_data_totalpasivopatrimonio[] = array('cuenta'=>'','denominacion'=>'TOTAL DE PASIVO Y PATRIMONIO','mesdes'=>uf_is_negative($ld_total_pasivopatrimonio),'meshas'=>uf_is_negative($ld_total_pasivopatrimonio_has),'variacion_bs'=>uf_is_negative($li_variacion_bs),'variacion'=>uf_is_negative($li_variacion));
	
	
	
	//IMPRIMIENDO ACTIVO
	$ls_subtitulo='ACTIVO';
	$li_row=$li_row+2;
	$lo_hoja->write($li_row, 0, $ls_subtitulo, $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, "", $lo_dataright);	
	//IMPRIMIENDO DISPONIBILIDADES (110)	
	$li_total=count((array)$la_data_diponibilidades2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_diponibilidades2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_diponibilidades2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_diponibilidades2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_diponibilidades2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_diponibilidades2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_diponibilidades2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_diponibilidades2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_diponibilidades2,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;		
	$li_total=count((array)$la_data_diponibilidades);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_diponibilidades[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_diponibilidades[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_diponibilidades[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_diponibilidades[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_diponibilidades[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_diponibilidades[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_diponibilidades[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_diponibilidades,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	//IMPRIMIENDO INVERSIONES (120)
	$li_total=count((array)$la_data_inversiones2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_inversiones2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_inversiones2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_inversiones2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_inversiones2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_inversiones2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_inversiones2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_inversiones2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_inversiones2,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;		
	$li_total=count((array)$la_data_inversiones);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_inversiones[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_inversiones[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_inversiones[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_inversiones[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_inversiones[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_inversiones[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_inversiones[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_inversiones,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	//IMPRIMIENDO DEUDORES (130)
	$li_total=count((array)$la_data_deudores2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_deudores2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_deudores2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_deudores2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_deudores2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_deudores2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_deudores2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_deudores2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_deudores2,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_deudores);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_deudores[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_deudores[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_deudores[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_deudores[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_deudores[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_deudores[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_deudores[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_deudores,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	//IMPRIMIENDO INTERESES X COBRAR (140)
	$li_total=count((array)$la_data_intereses2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_intereses2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_intereses2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_intereses2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_intereses2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_intereses2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_intereses2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_intereses2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_intereses2,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_intereses);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_intereses[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_intereses[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_intereses[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_intereses[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_intereses[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_intereses[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_intereses[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_intereses,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (150)
	$li_total=count((array)$la_data_otrasinversion2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_otrasinversion2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_otrasinversion2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_otrasinversion2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_otrasinversion2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_otrasinversion2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_otrasinversion2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_otrasinversion2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_otrasinversion2,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_otrasinversion);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_otrasinversion[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_otrasinversion[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_otrasinversion[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_otrasinversion[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_otrasinversion[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_otrasinversion[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_otrasinversion[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_otrasinversion,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (160)
	$li_total=count((array)$la_data_160);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_160[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_160[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_160[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_160[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_160[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_160[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_160[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_160,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;		
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (170)
	$li_total=count((array)$la_data_170);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_170[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_170[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_170[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_170[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_170[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_170[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_170[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_170,2,7, $io_pdf);
	}		
	$li_row=$li_row+1;	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (180)
	$li_total=count((array)$la_data_180);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_180[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_180[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_180[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_180[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_180[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_180[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_180[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_180,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//IMPRIMIENDO INVERSIONES OTROS FONDOS (100)
	$li_total=count((array)$la_data_100);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_100[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_100[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_100[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_100[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_100[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_100[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_100[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_100,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//IMPRIMIENDO PASIVO
	$ls_subtitulo='PASIVO';
	$li_row=$li_row+2;
	$lo_hoja->write($li_row, 0, $ls_subtitulo, $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, "", $lo_dataright);
	
	//IMPRIMIENDO FINANCIAMIENTOS OBTENIDOS (240)
	$li_total=count((array)$la_data_pasivopatri2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_pasivopatri2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_pasivopatri2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_pasivopatri2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_pasivopatri2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_pasivopatri2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_pasivopatri2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_pasivopatri2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_pasivopatri2,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;		
	$li_total=count((array)$la_data_pasivopatri);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_pasivopatri[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_pasivopatri[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_pasivopatri[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_pasivopatri[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_pasivopatri[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_pasivopatri[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_pasivopatri[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_pasivopatri,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;
	//IMPRIMIENDO OTRAS OBLIGACIONES (250)
	$li_total=count((array)$la_data_otraobligacion);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_otraobligacion[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_otraobligacion[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_otraobligacion[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_otraobligacion[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_otraobligacion[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_otraobligacion[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_otraobligacion[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_otraobligacion,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//IMPRIMIENDO INTERESES POR PAGAR (260)
	$li_total=count((array)$la_data_interesesxpagar2);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_interesesxpagar2[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_interesesxpagar2[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_interesesxpagar2[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_interesesxpagar2[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_interesesxpagar2[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_interesesxpagar2[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_interesesxpagar2[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_interesesxpagar2,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_interesesxpagar);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_interesesxpagar[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_interesesxpagar[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_interesesxpagar[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_interesesxpagar[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_interesesxpagar[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_interesesxpagar[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_interesesxpagar[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_interesesxpagar,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//IMPRIMIENDO ACUMULACIONES (270)
	$li_total=count((array)$la_data_acumulaciones);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_acumulaciones[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_acumulaciones[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_acumulaciones[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_acumulaciones[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_acumulaciones[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_acumulaciones[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_acumulaciones[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_acumulaciones,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$li_total=count((array)$la_data_acumyotrospasivos);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_acumyotrospasivos[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_acumyotrospasivos[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_acumyotrospasivos[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_acumyotrospasivos[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_acumyotrospasivos[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_acumyotrospasivos[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_acumyotrospasivos[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_acumyotrospasivos,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//IMPRIMIENDO TOTAL PASIVO (200)
	$li_total=count((array)$la_data_totalpasivos);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_totalpasivos[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_totalpasivos[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_totalpasivos[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_totalpasivos[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_totalpasivos[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_totalpasivos[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_totalpasivos[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_totalpasivos,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_gesoperativa);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_gesoperativa[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_gesoperativa[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_gesoperativa[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_gesoperativa[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_gesoperativa[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_gesoperativa[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_gesoperativa[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_gesoperativa,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;		
	//IMPRIMIENDO PATRIMONIO
	$ls_subtitulo='PATRIMONIO';
	$li_row=$li_row+2;
	$lo_hoja->write($li_row, 0, $ls_subtitulo, $lo_dataleft);
	$lo_hoja->write($li_row, 1, "", $lo_dataleft);
	$lo_hoja->write($li_row, 2, "", $lo_dataright);
	
	//IMPRIMIENDO CAPITAL (310)
	$li_total=count((array)$la_data_310);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_310[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_310[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_310[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_310[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_310[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_310[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_310[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_310,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_311);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_311[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_311[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_311[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_311[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_311[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_311[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_311[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_311,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_330);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_330[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_330[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_330[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_330[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_330[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_330[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_330[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_330,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$li_total=count((array)$la_data_aportespatrim);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_aportespatrim[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_aportespatrim[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_aportespatrim[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_aportespatrim[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_aportespatrim[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_aportespatrim[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_aportespatrim[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_aportespatrim,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_340);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_340[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_340[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_340[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_340[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_340[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_340[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_340[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_340,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	//uf_print_detalle($la_data_350,1,6, $io_pdf);
	$li_total=count((array)$la_data_360);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_360[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_360[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_360[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_360[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_360[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_360[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_360[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_360,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;		
	// AGREGADO POR OFIMATICA DE VENEZUELA EL 26/03/2013
	$li_total=count((array)$la_data_resultadosacum);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_resultadosacum[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_resultadosacum[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_resultadosacum[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_resultadosacum[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_resultadosacum[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_resultadosacum[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_resultadosacum[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_resultadosacum,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;		
	// FIN DE LO AGREGADO POR OFIMATICA DE VENEZUELA		
	$li_total=count((array)$la_data_370);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_370[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_370[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_370[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_370[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_370[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_370[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_370[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_370,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_300);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_300[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_300[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_300[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_300[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_300[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_300[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_300[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_300,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_totalpasivopatrimonio);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_totalpasivopatrimonio[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_totalpasivopatrimonio[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_totalpasivopatrimonio[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_totalpasivopatrimonio[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_totalpasivopatrimonio[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_totalpasivopatrimonio[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_totalpasivopatrimonio[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_totalpasivopatrimonio,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;		
	$li_total=count((array)$la_data_610);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_610[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_610[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_610[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_610[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_610[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_610[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_610[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_610,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;	
	$li_total=count((array)$la_data_620);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_620[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_620[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_620[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_620[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_620[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_620[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_620[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_620,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_810);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_810[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_810[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_810[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_810[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_810[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_810[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_810[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_810,2,7, $io_pdf);
	}			
	$li_row=$li_row+1;
	$li_total=count((array)$la_data_820);
	for($li=1;$li<=$li_total;$li++)
	{
		if($la_data_820[$li]["cuenta"]!='')
		{
			$li_row=$li_row+1;
			$lo_hoja->write_string($li_row, 0, $la_data_820[$li]["cuenta"], $lo_dataleft);				
			$lo_hoja->write($li_row, 1, $la_data_820[$li]["denominacion"], $lo_dataleft);
			$lo_hoja->write($li_row, 2, $la_data_820[$li]["mesdes"], $lo_dataright);
			$lo_hoja->write($li_row, 3, $la_data_820[$li]["meshas"], $lo_dataright);
			$lo_hoja->write($li_row, 4, $la_data_820[$li]["variacion_bs"], $lo_dataright);
			$lo_hoja->write($li_row, 5, $la_data_820[$li]["variacion"], $lo_dataright);
		}
//	    uf_print_detalle($la_data_820,2,7, $io_pdf);
	}			
	$lo_libro->close();
	header("Content-Type: application/x-msexcel; name=\"balance_general_sudeban_formaa.xls\"");
	header("Content-Disposition: inline; filename=\"balance_general_sudeban_formaa.xls\"");
	$fh=fopen($lo_archivo, "rb");
	fpassthru($fh);
	unlink($lo_archivo);
}//else

unset($io_report);
unset($io_funciones);
print("<script language=JavaScript>");
print(" close();");
print("</script>");

?>