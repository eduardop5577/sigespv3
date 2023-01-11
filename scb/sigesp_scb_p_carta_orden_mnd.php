<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

   session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
$dat=$_SESSION["la_empresa"];
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$ls_permisos="";
$la_seguridad=Array();
$la_permisos=Array();
$arrResultado=$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_p_carta_orden.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado["as_permisos"];
$la_seguridad=$arrResultado["aa_seguridad"];
$la_permisos=$arrResultado["aa_permisos"];
$ls_report = $io_fun_banco->uf_select_config("SCB","REPORTE","CARTA_ORDEN","sigesp_scb_rpp_cartaorden_pdf.php","C");
$ls_report_voucher = $io_fun_banco->uf_select_config("SCB","REPORTE","CHEQUE_VOUCHER","sigesp_scb_rpp_voucher_pdf.php","C");
$li_diasem = date('w');
$ls_ruta  = "txt/disco_banco/carta_orden";
@mkdir($ls_ruta,0755);
$ls_contintmovban= $_SESSION["la_empresa"]["contintmovban"];
 switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Carta Orden M&uacute;ltiples Notas de D&eacute;bito</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/general.css"  rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css"   rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="js/valida_fecha.js"></script>
<script type="text/javascript"  src="js/funcion_scb.js"></script>
<style type="text/css">

<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
</head>
<body>
<span class="toolbar"><a name="00"></a></span>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td height="20" colspan="12" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" class="toolbar">&nbsp;</td>
    <td class="toolbar">&nbsp;</td>
    <td class="toolbar">&nbsp;</td>
    <td class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="22" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo"   title="Nuevo"   width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif"          alt="Guardar" title="Guardar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar('<?PHP print $ls_ruta;?>');"><img src="../shared/imagebank/tools20/download.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
	<td class="toolbar" width="22"><div align="center"><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif"            alt="Salir"   title="Salir"   width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="694">&nbsp;</td>
  </tr>
</table>
<?php
require_once("sigesp_scb_c_carta_orden.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../shared/class_folder/grid_param.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../shared/class_folder/ddlb_conceptos.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("class_folder/sigesp_scb_c_disponibilidad_financiera.php");
	
$io_function = new class_funciones();	
$io_include  = new sigesp_include();
$ls_conect   = $io_include->uf_conectar();
$io_msg      = new class_mensajes();	
$obj_con     = new ddlb_conceptos($ls_conect);
$io_grid	 = new grid_param();
$io_carord   = new sigesp_scb_c_carta_orden();
$io_sql      = new class_sql($ls_conect);
$ls_codemp   = $_SESSION["la_empresa"]["codemp"];
$io_disfin    = new sigesp_scb_c_disponibilidad_financiera("../");
$ls_tipvaldis = $io_disfin->uf_load_tipo_validacion();
$ls_irnuevo='0';
$li_estciespg="";
$li_estciespi="";
$li_estciescg="";
$arrResultado="";
$arrResultado = $io_fun_banco->uf_load_estatus_cierre($li_estciespi,$li_estciescg);
$li_estciespg=$arrResultado["li_estciespg"];
$li_estciespi=$arrResultado["li_estciespi"];
$li_estciescg=$arrResultado["li_estciescg"];


require_once("sigesp_scb_c_movbanco.php");
require_once("sigesp_scb_c_config.php");
$in_classmovbanco=new sigesp_scb_c_movbanco($la_seguridad);
$in_classconfig=new sigesp_scb_c_config($la_seguridad);

	if (array_key_exists("chktipvia",$_POST))
	   {
	     $li_tipvia = $_POST["chktipvia"];
	   }
    else
	   {
	     $li_tipvia = 0;
	   }
	$ls_checked = "";
	if (array_key_exists("chkgentxt",$_POST))
	   {
	     $li_gentxt = $_POST["chkgentxt"];
	   }
    else
	   {
	     $li_gentxt = 0;
	   }
	 
	 if (array_key_exists("chktransmer",$_POST))
	   {
	   	$li_transmer = $_POST["chktransmer"];
	   }
	   else
	   {
	   	$li_transmer = 0;
	   }

	   $ls_checktransmer = "";
	   if ($li_transmer=='1')
	   {
	   	$ls_checktransmer = 'checked';
	   }
	   
	   
	$ls_checked2 = "";
	if ($li_gentxt=='1')
	{
	     $ls_checked2 = 'checked';
         $ls_style  = 'style="visibility:visible"';
	}
    else
	{
	   	if($li_tipvia!='1'){
	   		$ls_style  = 'style="visibility:hidden"';
	   	}
	}
	if($li_tipvia=='1'){
		$ls_checked = 'checked';
        $ls_style  = 'style="visibility:visible"';
	}
	else{
		if ($li_gentxt!='1'){
			$ls_style  = 'style="visibility:hidden"';
		}
	}
 if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion= $_POST["operacion"];
		$ls_mov_operacion="ND";
		$ls_documento=$_POST["txtdocumento"];
		$ls_codban=$_POST["txtcodban"];
		$ls_denban=$_POST["txtdenban"];
		$ls_cuenta_banco= trim($_POST["txtcuenta"]);
		$ls_txtctabanext=trim($_POST["txtctabanext"]);
		$ls_dencuenta_banco=$_POST["txtdenominacion"];
		$ls_provbene="";
		$ls_desproben="";
		$ls_selnin = '-';
		$ls_tipo=$_POST["cmbtipdes"];
        $ls_disabled = 'disabled="disabled"';
        $ls_enabled = 'enabled="enabled"';
		if ($ls_tipo=='P')
		   {
             //$ls_style  = 'style="visibility:hidden"';
			 $ls_selpro = 'selected'; 
		     $ls_selben = '';
		   }
		elseif($ls_tipo=='B')
		   {
		     $ls_disabled = '';
//			 if ($li_tipvia=='1')
//			    {
//			      $ls_style = 'style="visibility:visible"';
//				}
//			 else
//			    {
//			      $ls_style = 'style="visibility:hidden"';
//				}
			 $ls_selben   = 'selected'; 
		     $ls_selpro   = '';
		   }
		else
		   {
		      $ls_selpro = "";
		      $ls_selben = "";
		   }
		$ls_chevau="";
		$ldec_montomov=$_POST["totalchq"];
		$ldec_monobjret=$_POST["txtmonobjret"];
		$ldec_montoret=$_POST["txtretenido"];
		$ldec_montomov=str_replace(".","",$ldec_montomov);
		$ldec_montomov=str_replace(",",".",$ldec_montomov);
		$ldec_monobjret=str_replace(".","",$ldec_monobjret);
		$ldec_monobjret=str_replace(",",".",$ldec_monobjret);
		$ldec_montoret=str_replace(".","",$ldec_montoret);
		$ldec_montoret=str_replace(",",".",$ldec_montoret);
		$ls_estmov=$_POST["estmov"];		
		$ls_codconmov=$_POST["codconmov"];
		$ls_desmov=$_POST["txtconcepto"];
		$ls_cuenta_scg=$_POST["txtcuenta_scg"];
		if ($ls_contintmovban==0)
		{
			$ls_numcontint=" ";
		}
		else
		{
			$ls_numcontint=$_POST["txtnumconint"];
		}
		$ldec_disponible=$_POST["txtdisponible"];	
		$ld_fecha=$_POST["txtfecha"];
	    $ls_metban = $_POST["txtmetban"];
	    $ls_nommetban = $_POST["txtnommetban"];
		$ls_numordpagmin = $_POST["txtnumordpagmin"];
		$ls_codtipfon    = $_POST["hidcodtipfon"];
		$ld_monmaxmov    = $_POST["hidmonmaxmov"];
		$ls_fuente		 = $_POST["fuente"];
		$ls_fuente1x1000 = $_POST["fuente1x1000"];
		$ls_controlguard=$_POST["controlguard"];
	}
 else
	{
	  $ls_operacion= "NUEVO" ;	
	  $ls_estmov="N";		
	  $ls_metban = "";
	  $ls_nommetban = "";
	  $ls_controlguard="";
	  //Genera Numero
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
   		$io_keygen= new sigesp_c_generar_consecutivo();
		//$ls_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","");
		$ls_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBRE',15,'valinimovban','','',$_SESSION["la_logusr"]);
		if($ls_numcontint===false)
	    {
			 print "<script language=JavaScript>";
			 print "location.href='sigespwindow_blank.php'";
			 print "</script>";  
	    }
	    unset($io_keygen);
		$ls_fuente=$in_classconfig->uf_select_fuente();	
		$ls_fuente1x1000=$in_classconfig->uf_select_fuente_1x1000();
		//Genera Numeros
	}	

$ls_disable = "";
if ($li_estciescg==1)
   {
	 $ls_disable = "disabled";
   }
elseif(($li_estciespg==1 || $li_estciespi==1) && $li_estciescg==0 && $ls_operacion=="NUEVO")
   {
	 $io_msg->message("Ya fué procesado el Cierre Presupuestario, sólo serán cargadas Programaciones de Pago asociadas a Recepciones de Documentos netamente Contables !!!");	   
   }

$li_row=0;
$li_rows_spg=0;
$li_rows_ret=0;
$li_rows_spi=0;

function uf_load_datos_recepcion($as_codemp,$as_numsol,$ab_valido)
{
  global $io_sql,$io_msg,$io_function;
  $lb_valido  = true;
  $ls_procede = "";
  $ls_sql = "SELECT cxp_rd.procede as procedencia ". 
			"	FROM cxp_rd ,cxp_dt_solicitudes ".
			"	WHERE cxp_dt_solicitudes.codemp='".$as_codemp."' ".
			"	AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
			"	AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc ". 
			"	AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc ".
			"	AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene ".
			"	AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro "; 
  
  $rs_data = $io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $io_msg->message("PROCESO->sigesp_scb_p_carta_orden_mnd.php;Metodo:uf_load_datos_recepcion;Error en consulta, ".$io_function->uf_convertirmsg($io_sql->message));
	 }
  else
     {
	   if ($row=$io_sql->fetch_row($rs_data))
	      {
		    $ls_procede = $row["procedencia"];
		  }
	 }
	$arrResultado['ab_valido']=$lb_valido;
	$arrResultado['ls_procede']=$ls_procede;
  return $arrResultado;
}

function uf_load_datos_beneficiario($as_codemp,$as_cedbene,$as_nombene,$as_apebene,$ab_valido,$ls_procede,$adec_monto)
{
  global $io_sql,$io_msg,$io_function,$rs_datosbene;
  $lb_valido  = true;
  if ($ls_procede=='SCVSOV')
     {
       $ls_sql = "SELECT distinct a.codcueban,a.tipcuebanper as tipcuebanper,b.nomper as nomper,b.apeper as apeper,
	                     b.nacper as nacben, b.cedper, $adec_monto as monnetres, a.codban as codban, b.coreleper as coreleper
	                FROM sno_personalnomina a , sno_personal b, sno_nomina c
                   WHERE b.codemp='".$as_codemp."' 
				     AND b.cedper='".trim($as_cedbene)."'
					 AND c.espnom = '0'
					 AND b.codemp=a.codemp 
					 AND b.codper=a.codper 
					 AND a.codnom=c.codnom";
     }
  else
     {
   	   $ls_sql = "SELECT ctaban as codcueban,tipcuebanben as tipocta, nombene,apebene,nacben, ced_bene,  $adec_monto as monnetres, '' as codban, email ".
		  		 "  FROM rpc_beneficiario ".
 	             " WHERE codemp='".$as_codemp."'".
	             "   AND ced_bene='".$as_cedbene."'";
     }
  $rs_data = $io_sql->select($ls_sql);
  if ($rs_data===false)
     {
       $lb_valido = false;
       $io_msg->message("PROCESO->sigesp_scb_p_carta_orden_mnd.php;Metodo:uf_load_datos_beneficiario;Error en consulta, ".$io_function->uf_convertirmsg($io_sql->message));
	 }
  else
     {
		$rs_datosbene->insertRow("sql",$ls_sql);
    }
	$arrResultado['ab_valido']=$ab_valido;
	$arrResultado['rs_data']=$rs_data;
  return $arrResultado;
}
	
function uf_load_datos_global()
{		
	global $io_sql,$io_msg,$io_function,$rs_datosbene;
	$lb_valido  = true;
	$ls_sql="";
	$li_totrow=$rs_datosbene->getRowCount("sql");
	for($li_i=1;$li_i<=$li_totrow;$li_i++)
	{
		$ls_cadena=$rs_datosbene->data["sql"][$li_i];
		if($li_i!=1)
			$ls_sql=$ls_sql." UNION ".$ls_cadena;
		else
			$ls_sql=$ls_cadena;
	}
	$rs_data = $io_sql->select($ls_sql);
	if ($rs_data===false)
	{
		$io_msg->message("PROCESO->sigesp_scb_p_carta_orden_mnd.php;Metodo:uf_load_datos_beneficiario;Error en consulta, ".$io_function->uf_convertirmsg($io_sql->message));
		return false;
	}
	return $rs_data;	
}
	
if ($ls_operacion=="CARGAR_DT")
   {
   	 $object=array();
	 $li_rows=0;
	 $arrResultado=array();
     $arrResultado=$io_carord->uf_cargar_programaciones($ls_tipo,$ls_provbene,$ls_codban,$ls_cuenta_banco,$object,$li_rows,$li_tipvia,$ls_numordpagmin,$ls_codtipfon);
   	 $object=$arrResultado["object"];
	 $li_rows=$arrResultado["li_rows"];
   }	
	
function uf_nuevo()
{
	global $ls_mov_operacion,$ls_numordpagmin,$ls_codtipfon,$ld_monmaxmov;
	global $la_seguridad;
	$ls_mov_operacion="ND";
	global $ls_opepre;
	$ls_opepre="";
	global $ls_documento;
	$ls_documento="";
	global $ls_codban;
	$ls_codban="";
	global $ls_denban;
	$ls_denban="";
	global $ls_estmov;
	$ls_estmov="N";
	global $ls_cuenta_banco;
	$ls_cuenta_banco="";
	global $ls_txtctabanext;
	$ls_txtctabanext="";
	global $ls_dencuenta_banco;
	$ls_dencuenta_banco="";	
	global $ls_provbene;
	$ls_provbene="----------";
	global $ls_desproben;
	$ls_desproben="Ninguno";
	global $ls_tipo;
	$ls_tipo="-";
	global $ls_chevau;
	require_once("sigesp_scb_c_movbanco.php");
	$in_classmovbanco=new sigesp_scb_c_movbanco($la_seguridad);
	global $ls_empresa;
	global $ldec_disponible;	
	$ldec_disponible="";	
	$ls_chevau = $in_classmovbanco->uf_generar_voucher($ls_empresa);
	global $ld_fecha;
	global $ldec_montomov;
	$ldec_montomov=0;
	global $ldec_monobjret;
	$ldec_monobjret=0;
	global $ldec_montoret;
	$ldec_montoret=0;
	global $ls_codconmov;
	$ls_codconmov='---';
	global $ls_desmov;
	$ls_desmov="";
	global $ls_cuenta_scg;
	$ls_cuenta_scg="";
	global $li_rows;
	global $li_temp;
	global $object;
	global $ld_fecha;
	global $ls_metban;
	global $ls_nommetban;
	$ls_metban = "";
	$ls_nommetban = "";
	global $ls_style,$ls_disabled,$ls_disable,$ls_enable,$ls_enabled;
	$ls_style  = 'style="visibility:hidden"';
	$ls_disabled = 'disabled="disabled"';
	$ls_enabled = 'enabled="enabled"';
	global $ls_selnin,$ls_selpro,$ls_selben;
	$ls_selnin = '-';
	$ls_selpro = "";
	$ls_selben = $ls_numordpagmin = $ls_codtipfon = "";
	$ld_monmaxmov = 0;
	if(array_key_exists("la_deducciones",$_SESSION))
	{
		unset($_SESSION["la_deducciones"]);
	}
	$li_temp=1;
	$li_rows=$li_temp;
	$ld_fecha=date("d/m/Y");
	$object[$li_temp][1] = "<input name=chk".$li_temp." type=checkbox 			      id=chk".$li_temp." 				value=1   class=sin-borde onClick=javascript:uf_selected('".$li_temp."'); $ls_disable><input type=hidden  name=txtcodban".$li_temp."  id=txtcodban".$li_temp." value='' readonly>";
	$object[$li_temp][2] = "<input type=text 	  name=txtnumsol".$li_temp." 		  id=txtnumsol".$li_temp."  		value=''  class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
	$object[$li_temp][3] = "<input type=text 	  name=txtconsol".$li_temp." 		  id=txtconsol".$li_temp."			value=''  class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
	$object[$li_temp][4] = "<input type=hidden   name=txtcodproben".$li_temp."  	  id=txtcodproben".$li_temp."		value=''  class=sin-borde readonly style=text-align:left size=20 maxlength=20><input type=text name=txtnomproben".$li_temp." id=txtnomproben".$li_temp."  value=''  class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
	$object[$li_temp][5] = "<input type=text 	  name=txtmonsol".$li_temp."          id=txtmonsol".$li_temp."			value='".number_format(0,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=6>";
	$object[$li_temp][6] = "<input type=text	  name=txtmontopendiente".$li_temp."  id=txtmontopendiente".$li_temp."  value='".number_format(0,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=3>";				
	$object[$li_temp][7] = "<input type=text     name=txtmonto".$li_temp."           id=txtmonto".$li_temp."			onKeyPress=return(currencyFormat(this,'.',',',event)); value='".number_format(0,2,",",".")."' class=sin-borde onBlur=javascript:uf_actualizar_monto(".$li_temp."); style=text-align:right size=16 maxlength=20>";							
	$object[$li_temp][8] = "<input type=text     name=txtnomban".$li_temp."  	      id=txtnomban".$li_temp."          value=''  class=sin-borde  readonly style=text-align:left size=30 maxlength=254>";
	$object[$li_temp][9] = "<input type=text     name=txtctaban".$li_temp."  	      id=txtctaban".$li_temp."          value=''  class=sin-borde  readonly style=text-align:left size=25 maxlength=25><input type=hidden  name=txtdenctaban".$li_temp."  id=txtdenctaban".$li_temp."  value=''><input type=hidden  name=txtdenctaban".$li_temp."  id=txtdenctaban".$li_temp."  value=''><input type=hidden  name=txtdenctaban".$li_temp."  id=txtdenctaban".$li_temp."  value=''><input type=hidden  name=txtcodtipcta".$li_temp."  id=txtcodtipcta".$li_temp."  value=''><input type=hidden  name=txtnomtipcta".$li_temp."  id=txtnomtipcta".$li_temp."  value=''><input type=hidden  name=txtscgcuenta".$li_temp."  id=txtscgcuenta".$li_temp."  value=''><input type=hidden  name=txtdisponible".$li_temp."  id=txtdisponible".$li_temp."  value='0,00'><input type=hidden  name=txtctabanext".$li_temp."      id=txtctabanext".$li_temp."      value=''>";
	$object[$li_temp][10] = "<input type=text 	  name=txtfecpag".$li_temp." 		  id=txtfecpag".$li_temp."  		value=''  class=sin-borde readonly style=text-align:center  size=15 maxlength=10 datepicker=true onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=javascript: ue_validar_formatofecha(this); >";
	$object[$li_temp][11] = "<input type=text     name=cmbmodpag".$li_temp." 			id=cmbmodpag".$li_temp." value=''  class=sin-borde  readonly>";
} 

$title[1]="";
$title[2]="Solicitud";
$title[3]="Concepto";
$title[4]="Proveedor/Beneficiario";
$title[5]="Monto";
$title[6]="Monto Pendiente";
$title[7]="Monto a Pagar";
$title[8]="Banco";
$title[9]="Cuenta";
$title[10]="Fecha Pago";
$title[11]="Metodo Pago";
$grid="grid";	
 	
if ($ls_operacion == "NUEVO")
   {
     $ls_operacion= "" ;
	 //Genera Numero
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
   		$io_keygen= new sigesp_c_generar_consecutivo();
		//$ls_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","");
		$ls_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBRE',15,'valinimovban','','',$_SESSION["la_logusr"]);
		if($ls_numcontint===false)
	    {
			 print "<script language=JavaScript>";
			 print "location.href='sigespwindow_blank.php'";
			 print "</script>";  
	    }
	    unset($io_keygen);
		//Genera Numeros
	 uf_nuevo();
   }

 if ($ls_operacion=="GUARDAR")
	{		
		$li_cont = 0;
		require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
		$ds_sol_cancel=new class_datastore();
		$rs_datosbene=new class_datastore();
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		$ls_numrefcarord = $_SESSION["la_empresa"]["numrefcarord"];		
		$ls_modageret = $_SESSION["la_empresa"]["modageret"];
		$li_totalRows = $_POST["totalrows"];
		$arr_movbco["codban"]   = $ls_codban;
		$arr_movbco["ctaban"]   = $ls_cuenta_banco;
		$ld_fecdb=$io_function->uf_convertirdatetobd($ld_fecha);
		$arr_movbco["codope"]   = 'ND';
		$arr_movbco["fecha"]    = $ld_fecha;
		$arr_movbco["estmov"]   = $ls_estmov;
		$ls_numcarord  = $ls_documento;
		$ls_probentemp = "";
		$ls_numdoc=$io_carord->uf_generar_num_documento($ls_codemp,$ls_mov_operacion);
		if ($ls_numrefcarord=='1')
		{
			$ls_numdoc=$ls_numcarord;
		}
		$li_mondedtot=0;
		$ldec_totmontoret=0;
		$ldec_montomovaux=$ldec_montomov;
		$in_classmovbanco->io_sql->begin_transaction();
		$lb_valido=$io_carord->uf_procesar_movbanco($ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_mov_operacion,$ld_fecha,$ls_desmov,$ls_codconmov,"----------","----------","-",$ldec_montomov,$ldec_monobjret,$ldec_montoret,$ls_chevau,$ls_estmov,0,1,'T','SCBCOR','',$ls_tipo,$ls_numcarord,"--",$ls_numordpagmin,$ls_codtipfon,$ls_numcontint);
		if (!$lb_valido)
		{
			$lb_valido=false;   
			$io_msg->message("PROCESO->El numero de documento ya existe");
			$ls_irnuevo='1';
		}		
		$lb_cheked=0;
		$ls_solicitudes="";
		$li_consol = 0;
		$lb_unico=true;
		for ($li_i=1;$li_i<=$li_totalRows;$li_i++)				
		    {
			  if (array_key_exists("chk".$li_i,$_POST))
			     { 
				   $li_cont++;
			  	   $ls_numsol   		= $_POST["txtnumsol".$li_i];
				   $ldec_monsol 		= $_POST["txtmonsol".$li_i];
				   $ls_codproben		= $_POST["txtcodproben".$li_i];
				   $ldec_monsol 		= str_replace(".","",$ldec_monsol);
				   $ldec_monsol 		= str_replace(",",".",$ldec_monsol);
				   $ldec_montopendiente = $_POST["txtmontopendiente".$li_i];
				   $ldec_montopendiente = str_replace(".","",$ldec_montopendiente);
				   $ldec_montopendiente = str_replace(",",".",$ldec_montopendiente);
				   $ldec_monto 		    = $_POST["txtmonto".$li_i];
				   $ldec_monto 		    = str_replace(".","",$ldec_monto);
				   $ldec_monto 		    = str_replace(",",".",$ldec_monto);
				   $ls_desproben	    = $_POST["txtnomproben".$li_i];
				   $ls_codfuefin	    = $_POST["txtcodfuefin".$li_i];
				   
				   if($li_gentxt=='1'){
				   		$arrCredito[$li_consol]['numsol'] = $_POST["txtnumsol".$li_i];
				   		$arrCredito[$li_consol]['tipdes'] = $ls_tipo;
				   		$arrCredito[$li_consol]['monsol'] = $ldec_monto;
				   		$arrCredito[$li_consol]['fecpag'] = $_POST["txtfecpag".$li_i];
				   		$arrCredito[$li_consol]['cmbmodpag'] = $_POST["cmbmodpag".$li_i];
				   		$li_consol++;
				   }
				   
				   if($li_i == 1)
				   {
				   		$ls_solicitudes=$ls_numsol;
				   }
				   else
				   {
				   		$ls_solicitudes=$ls_numsol."--".$ls_solicitudes;
				   }
				   if($ls_codfuefin=="")
				   {
						$ls_codfuefin="--";
				   }
				   if($li_cont==1)
				   {
				   		$ls_codprobenaux = $ls_codproben;
				   }
				   if($ls_codprobenaux != $ls_codproben)
				   {
				   		$lb_unico=false;
				   }
				   if ($ls_tipo=='P')
				      {
						$ls_codpro  = $ls_codproben;
					    $ls_cedbene = "----------";				   
					}
					else
					{
					    $ls_codpro  = "----------";
					    $ls_cedbene = $ls_codproben;
					}
				if($lb_valido)
				{					
					$ls_procede="";
					$lb_valido="";
					$arrResultado="";
					$arrResultado = uf_load_datos_recepcion($ls_codemp,$ls_numsol,$lb_valido);//Encontrar la procedencia de la Recepcion de Documentos asociadas a la Solicitud de Pago.
					$ls_procede=$arrResultado["ls_procede"];
					$lb_valido=$arrResultado["ab_valido"];
				   if ($ls_procede=='SCVSOV')
					  {
						$arrResultado="";
						$arrResultado= uf_load_datos_beneficiario($ls_codemp,$ls_cedbene,$ls_nombene,$ls_apebene,$lb_valido,$ls_procede,$ldec_monto);
						$rs_datosbe3ne=$arrResultado["rs_data"];
			   			$lb_valido=$arrResultado["ab_valido"];
						$aa_seguridad["empresa"]	 = $ls_codemp;
						$aa_seguridad["sistema"]	 = "SCB";
						$aa_seguridad["logusr"]	     = $_SESSION["la_logusr"];
						$aa_seguridad["ventanas"]	 = "sigesp_scb_p_carta_orden_mnd.php";
						if ($rs_datosbe3ne->EOF)
						{
							$arrResultado="";
							$arrResultado 			     = uf_load_datos_beneficiario($ls_codemp,$ls_cedbene,$ls_nombene,$ls_apebene,$lb_valido,'',$ldec_monto);
							$rs_datosbe3ne=$arrResultado["rs_data"];
			   				$lb_valido=$arrResultado["ab_valido"];
						}
				      }
					$arr_movbco["mov_document"] = $ls_numdoc;
					$arr_movbco["objret"]   	= $ldec_monobjret;
					
				 if ($lb_valido)
				      {				  
						$lb_valido=$io_carord->uf_insert_fuentefinancimiento($ls_codemp,$ls_codban,$ls_cuenta_banco,$ls_numdoc,'ND',$ls_estmov,$ls_codfuefin);
					  }
				   if ($ldec_montopendiente==$ldec_monto)
				      {
					    $ls_estsol='C';	//Cancelado							
				      }
				   else
				      {
					    $ls_estsol='P';//Programado
				      }
				 if ($lb_valido)
				      {
						$lb_valido=$io_carord->uf_procesar_carta_orden($ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_mov_operacion,$ls_numsol,$ls_estmov,$ldec_monto,$ls_estsol);
					  }
				   if ($lb_valido)
				      {
//--------------------------------PARA EL CASO QUE LAS RETENCIONES SE APLIQUE DESDE CXP Y SE REFLEJAN EN BANCO------------					 
						///////////////// Guardo el detalle de las solicitudes de pago cancelades en esta carta orden ///////////////////
						 require_once("sigesp_scb_c_emision_chq.php");
						 $io_emiche  = new sigesp_scb_c_emision_chq();
						 $ls_estretiva = $_SESSION["la_empresa"]["estretiva"];
						 $ls_estretmil = $_SESSION["la_empresa"]["estretmil"];
						 //print "Ret-Iva--->  ".$ls_estretiva."<br>";
						 //print "Ret-1x1000--->  ".$ls_estretmil."<br>";
					     $as_codban="";
					     $as_ctaban="";
					     $ls_ctaprovbene="";
					     $arrResultado="";
						 $arrResultado=$io_carord->uf_select_ctaprovbene($ls_tipo,$ls_codproben,$as_codban,$as_ctaban);
					     $as_codban=$arrResultado["ls_codban"];
					     $as_ctaban=$arrResultado["ls_ctaban"];
					     $ls_ctaprovbene=trim($arrResultado["ls_cuenta_scg"]);
						 if(trim($ls_ctaprovbene)=='')
						 {
							$lb_valido=false;
							$io_msg->message("La Cuenta del proveedor o beneficiario esta en blanco.");
						 }
						 else
						 {
						 	$lb_valido=$io_carord->uf_procesar_dtmov($ls_codemp, $ls_codban, $ls_cuenta_banco, $ls_numdoc, $ls_mov_operacion,'N', $ls_codpro, $ls_cedbene, $ls_numsol, $ldec_monto,$ls_ctaprovbene);
						 }
						 if ($lb_valido)
					       {
							 if ($ls_estretiva=='B')//Retenciones aplicadas desde el Módulo de Cuentas Por Pagar y reflejadas en el Módulo Banco.
							    {
							      $ls_procede_doc = "CXPSOP";
								 // $la_deducciones1 = $io_emiche->uf_load_retenciones_iva_cxp($ls_codemp,$ls_numsol);
								}
							 elseif($ls_estretiva=='C')//Retenciones aplicadas desde el Módulo de Cuentas Por Pagar.
							    {
								  $ls_procede_doc = "SCBBCH";
								  if (array_key_exists("la_deducciones",$_SESSION))
								     {
									   $la_deducciones1=$_SESSION["la_deducciones"];
								     }										
								}
							 //print "Procede_doc--->  ".$ls_procede_doc."<br>";
							 $li_total = 0;
							 $ld_montotret = 0;
							 if (!empty($la_deducciones1))
							    {
								  $li_total = count((array)$la_deducciones1["codded"]);
								  if ($li_total==0)
								  {
								  	$li_total = count((array)$la_deducciones1["Codded"]);
								  }
							    }
							 for ($i=1;$i<=$li_total;$i++)
								 {
								   if (array_key_exists("$i",$la_deducciones1["codded"]))
									  {
									    $ls_ctascg	    = trim($la_deducciones1["sc_cuenta"][$i]);
									    $ls_dended	    = $la_deducciones1["dended"][$i];
										$ls_codded	    = $la_deducciones1["codded"][$i];
										$ldec_objret   = $la_deducciones1["monobjret"][$i];
										$ldec_montoret = $la_deducciones1["monret"][$i];
										$ld_montotret += $ldec_montoret; 
										if (($ls_codded!="")&&($ldec_montoret>0))
										   {
										     $lb_valido=$in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_ctascg,
											                                                       $ls_procede_doc,$ls_dended,
																								   $ls_numsol,'H',$ldec_montoret,
																								   $ldec_objret,true,$ls_codded);
											$ldec_totmontoret=$ldec_totmontoret+$ldec_montoret;
										   }//FIN DEL IF
									  }//FIN DEL IF
								 }// FIN DEL FOR
							// Agregado para retenciones aplicadas en Carta Orden	 
							if ($ls_modageret=="B")/// se realiza el calculo de la ret. municipal
							    {
								  $la_deducciones2=$_SESSION["la_deducciones"];
								  $li_total2 = count((array)$la_deducciones2["Codded"]);
								  for ($j=1;$j<=$li_total2;$j++)
								      { 
								        if (array_key_exists("$j",$la_deducciones2["Codded"]))
									       {
											 $ls_ctascg1	 = trim($la_deducciones2["SC_Cuenta"][$j]);
											 $ls_dended1	 = $la_deducciones2["Dended"][$j];
											 $ls_codded1	 = $la_deducciones2["Codded"][$j];
											 $ldec_objret1   = $la_deducciones2["MonObjRet"][$j];
											 $ldec_montoret1 = round($la_deducciones2["MonRet"][$j],2);										
											 $ld_montotret 	 = $ld_montotret+$ldec_montoret1;
											 if (!empty($ls_codded1)&&($ldec_montoret1>0))
											    {
													$lb_valido=$in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_ctascg1,
																										  $ls_procede_doc,$ls_dended1,
																										  $ls_numsol,'H',$ldec_montoret1,
																										  $ldec_objret1,true,$ls_codded1);
																										  
												$ldec_totmontoret=$ldec_totmontoret+$ldec_montoret1;
												}
										   }
								      }
							    }
							// Agregado para retenciones aplicadas en Carta Orden
							
							// Agregado para retenciones 1x1000 aplicadas en Banco	 
							if ($ls_estretmil=="B")/// se realiza el calculo de la ret. 1x1000
							{
							  $la_deducciones2=$_SESSION["la_deducciones"];
							  $li_total2 = count((array)$la_deducciones2["Codded"]);
							  for ($j=1;$j<=$li_total2;$j++)
								  { 
									if (array_key_exists("$j",$la_deducciones2["Codded"]))
									   {
										 $ls_ctascg1	 = trim($la_deducciones2["SC_Cuenta"][$j]);
										 $ls_dended1	 = $la_deducciones2["Dended"][$j];
										 $ls_codded1	 = $la_deducciones2["Codded"][$j];
										 $ldec_objret1   = $la_deducciones2["MonObjRet"][$j];
										 $ldec_montoret1 = round($la_deducciones2["MonRet"][$j],2);										
										 $ld_montotret 	 = $ld_montotret+$ldec_montoret1;
										 if (!empty($ls_codded1)&&($ldec_montoret1>0))
											{
												$lb_valido=$in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_ctascg1,
																									  $ls_procede_doc,$ls_dended1,
																									  $ls_numsol,'H',$ldec_montoret1,
																									  $ldec_objret1,true,$ls_codded1);
											$ldec_totmontoret=$ldec_totmontoret+$ldec_montoret1;
											}
									   }
								  }
							}
							// Agregado para retenciones aplicadas en Carta Orden
							
								if ($ls_estretiva=='B')
								{
									  $ldec_montotot=$ldec_montomov;
									  $li_mondedtot=$li_mondedtot+$ldec_montotot;
								}
								elseif($ls_estretiva=='C')
								{
									  $ldec_montotot=($ldec_montomov-$ldec_montoret);
									  $li_mondedtot=$li_mondedtot+$ldec_montotot;
								}
								unset($la_deducciones1);
					       }//FIN DEL IF
///-----------------------------------------------------------------------------------------------------------------------------------					  
					    //$ldec_montotot = ($ldec_montomov-$ldec_montoret);
					  //  $lb_valido     = $in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_cuenta_scg,'SCBCOR',$ls_desmov,$ls_numdoc,'H',$ldec_monto,$ldec_monobjret,false,'00000');
					  }
					    if ($lb_valido)
						{
						    if ($ls_clactacon==1)
							{
								$ls_ctaprovbene = trim($io_carord->uf_select_ctacxpclasificador($ls_numsol,$ls_tipo,$ls_codproben));
							}
							else
							{
								 $as_codban="";
								 $as_ctaban="";
								 $ls_ctaprovbene="";
								 $arrResultado="";
								 $arrResultado=$io_carord->uf_select_ctaprovbene($ls_tipo,$ls_codproben,$as_codban,$as_ctaban);
								 $as_codban=$arrResultado["ls_codban"];
								 $as_ctaban=$arrResultado["ls_ctaban"];
								 $ls_ctaprovbene=trim($arrResultado["ls_cuenta_scg"]);
						    }
							//Empiezan los detalles!!
							//Reemplazo los valores de banco y cuenta banco por los del proveedor.
						    /*$lb_valido=$in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_ctaprovbene,'CXPSOP',$ls_desmov,$ls_numsol,'D',$ldec_monto,$ldec_monobjret,false,'00000');*/
							if(trim($ls_ctaprovbene)=='')
							{
								$lb_valido=false;
								$io_msg->message("La Cuenta del proveedor o beneficiario esta en blanco.");
							}
							else
							{
								if ($ldec_monto>0)
								{								 
									$lb_valido=$in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_ctaprovbene,'CXPSOP',$ls_desmov,$ls_numsol,'D',$ldec_monto,$ldec_monobjret,false,'00000');
								}
							}							
							if ($lb_valido)
							{
							     //Sustiyuyo nuevamente las del movimiento.
							     if ($lb_valido){
									 $ldec_monto_spg=0;
							         $ldec_montospg2=0;
								     $aa_dt_spgcxp=$io_emiche->uf_buscar_dt_cxpspg($ls_numsol);
							 	     
								     
								     //CALCULO TOTAL PRESUPUESTARIO
							 	     $ld_totpre=0;
									 foreach($aa_dt_spgcxp as $dt_cxpspg){
										$ld_mon_aux    = $dt_cxpspg["monto"];
										$ld_totpre     = $ld_totpre + round(doubleval($ld_mon_aux), 2);
									 }										
									 	
									 $numSPG = count((array)$aa_dt_spgcxp);
									 $ld_total_spg = 0;
									 $x=1;
									 if ($ls_estsol=="C"){
									 	foreach($aa_dt_spgcxp as $dt_cxpspg){
												$ls_codestpro1 = $dt_cxpspg["codestpro1"];
												$ls_codestpro2 = $dt_cxpspg["codestpro2"];
												$ls_codestpro3 = $dt_cxpspg["codestpro3"];
												$ls_codestpro4 = $dt_cxpspg["codestpro4"];
												$ls_codestpro5 = $dt_cxpspg["codestpro5"];
												$ls_estcla     = $dt_cxpspg["estcla"];
												$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
												$ls_descripcion = $dt_cxpspg["descripcion"];
												$ld_monto_par   = $dt_cxpspg["monto"];
												$ls_codfuefin   = $dt_cxpspg["codfuefin"];
												$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
												$lb_valido      = $in_classmovbanco->uf_procesar_dt_gasto($ls_codban,$ls_cuenta_banco,$ls_numdoc,'ND',$ls_estmov,$ls_programa,$ls_cuentaspg,$ls_numsol,$ls_descripcion,'CXPSOP',$ld_monto_par,'PG',$ls_estcla,$ls_codfuefin);
											}
									 }else{
									 	if($ldec_monto<$ld_totpre){
									 		foreach($aa_dt_spgcxp as $dt_cxpspg){
												$ls_codestpro1 = $dt_cxpspg["codestpro1"];
												$ls_codestpro2 = $dt_cxpspg["codestpro2"];
												$ls_codestpro3 = $dt_cxpspg["codestpro3"];
												$ls_codestpro4 = $dt_cxpspg["codestpro4"];
												$ls_codestpro5 = $dt_cxpspg["codestpro5"];
												$ls_estcla     = $dt_cxpspg["estcla"];
												$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
												$ls_descripcion = $dt_cxpspg["descripcion"];
												$ld_monto_par   = $dt_cxpspg["monto"];
												$ls_codfuefin   = $dt_cxpspg["codfuefin"];
												if($numSPG - $x == 0){
													$ld_monto_spg   = $ldec_monto - $ld_total_spg;
												}
												else {
													$ld_monto_spg   = round(round($ld_monto_par , 2 ) *($ldec_monto  / $ld_totpre),2);
													$ld_total_spg   = $ld_total_spg + $ld_monto_spg;
												}
												$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
												$lb_valido      = $in_classmovbanco->uf_procesar_dt_gasto($ls_codban,$ls_cuenta_banco,$ls_numdoc,'ND',$ls_estmov,$ls_programa,$ls_cuentaspg,$ls_numsol,$ls_descripcion,'CXPSOP',$ld_monto_spg,'PG',$ls_estcla,$ls_codfuefin);
												$x++;
											}
										 }
										 elseif ($ldec_monto>=$ld_totpre){
										 	foreach($aa_dt_spgcxp as $dt_cxpspg){
												$ls_codestpro1 = $dt_cxpspg["codestpro1"];
												$ls_codestpro2 = $dt_cxpspg["codestpro2"];
												$ls_codestpro3 = $dt_cxpspg["codestpro3"];
												$ls_codestpro4 = $dt_cxpspg["codestpro4"];
												$ls_codestpro5 = $dt_cxpspg["codestpro5"];
												$ls_estcla     = $dt_cxpspg["estcla"];
												$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
												$ls_descripcion = $dt_cxpspg["descripcion"];
												$ld_monto_spg   = $dt_cxpspg["monto"];
												$ls_codfuefin   = $dt_cxpspg["codfuefin"];
												$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
												if($ld_monto_spg>0){
													$lb_valido      = $in_classmovbanco->uf_procesar_dt_gasto($ls_codban,$ls_cuenta_banco,$ls_numdoc,'ND',$ls_estmov,$ls_programa,$ls_cuentaspg,$ls_numsol,$ls_descripcion,'CXPSOP',$ld_monto_spg,'PG',$ls_estcla,$ls_codfuefin);
												}
												else{
													$lb_valido = true;
												}
											}
											
										 }
									 }
								 	 unset($aa_dt_spgcxp);
							     }
						    }
					    }
				      }
			        $ls_probentemp=$ls_codproben;
					 // Validacion de monto del pago
					 if($lb_valido)
					 {
						$li_origen=$in_classmovbanco->uf_validar_monto_cancelado($ls_numsol,$ls_numdoc,$ls_codban,$ls_cuenta_banco,$ldec_monto);
						if($li_origen==1)
						{
							$lb_valido=false;
							$io_msg->message("La solicitud de pago ".$ls_numsol." ya ha sido cancelada en su totalidad");
						}
						elseif($li_origen==2)
						{
							$lb_valido=false;
							$io_msg->message("El pago excede el monto estipulado en la solicitud de pago ".$ls_numsol."");
						}
					 }
			     }	
		    }
			//break;
			////// ACTUALIZA PROVEEDOR / BENEFICIARIO  ///////
			if($lb_valido)
			{			
				if($lb_unico)
				{
					if ($ls_tipo=='P')
					{
						$ls_codpro  = $ls_codproben;
						$ls_cedbene = "----------";
						$lb_valido=$io_carord->uf_update_provbene_movimiento($ls_codemp,$ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_mov_operacion,$ls_codpro,$ls_cedbene,$ls_tipo);	
					}
					else
					{
						$ls_codpro  = "----------";
						$ls_cedbene = $ls_codproben;
						$lb_valido=$io_carord->uf_update_provbene_movimiento($ls_codemp,$ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_mov_operacion,$ls_codpro,$ls_cedbene,$ls_tipo);	
					}
				}
				else
				{
					$lb_valido=$io_carord->uf_update_provbene_movimiento($ls_codemp,$ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_mov_operacion,"----------","----------",$ls_tipo);	
				}
			}
			////// ACTUALIZA PROVEEDOR / BENEFICIARIO  ///////
			if($lb_valido)
			{
				$ldec_montomovaux=$ldec_montomovaux-$ldec_totmontoret;
				if ($ldec_montomovaux>0)
				{								 
					$lb_valido     = $in_classmovbanco->uf_procesar_dt_contable($arr_movbco,$ls_cuenta_scg,'SCBCOR',$ls_desmov,$ls_numdoc,'H',$ldec_montomovaux,$ldec_monobjret,false,'00000');
				}
			}
	 if ($lb_valido)
		{
			$in_classmovbanco->io_sql->commit();
			$io_msg->message("Movimiento registrado !!!");
			$ls_controlguard="0";
		    if ($lb_valido)
			   {
			   		//AQUI INTEGRACION BANCO DEL PUEBLO

					if($_SESSION['la_empresa']['estintban'] == '1' && $ls_codban=='001') { 
						require_once ("class_folder/sigesp_scb_c_integracionIBS.php");
						$io_integracionIBS = new sigesp_scb_c_integracionIBS();
						
						//FORMATEAR MONTO
						//BUCAR DATOS DEL PROVEEDOR
						$arrDataProv = $in_classmovbanco->uf_datos_proveedor($ls_codpro);
						$ls_montoIBS = str_replace(".","",$ldec_montomov);
						$ls_montoIBS = str_pad($ls_montoIBS, 14, "0", STR_PAD_LEFT);
						//EJECUTAR LLAMADO A SP
						$coderr = $io_integracionIBS->procesarTransferenciaIBS($arrDataProv["ctaban"], $arrDataProv["rifpro"], $ls_montoIBS);
						//IMPRIMIR MENSAJE
						$mensajeIBS = $io_integracionIBS->mensajeTransferencia($coderr);
						$io_msg->message("Mensaje IBS: ".$mensajeIBS);	
					}
			
					//FIN INTEGRACION BANCO DEL PUEBLO
					
				      //$ds_banco_nomina->data = $ds_banco;
					  //$li_numrows = $ds_banco_nomina->getRowCount("codper");
					if ($li_tipvia=='1')
					{
					  $rs_datosbene= uf_load_datos_global();
					  $li_numrows=count((array)$rs_datosbene);
					  if ($li_numrows>0)
						 { 
						 	$ls_ruta	 = "txt/disco_banco/carta_orden";
						   @mkdir($ls_ruta,0755);
						   require_once("../sno/sigesp_sno_c_metodo_banco.php");
						   $io_metodobanco=new sigesp_sno_c_metodo_banco();
						   $lb_valido = $io_metodobanco->uf_metodo_banco($ls_ruta,$ls_nommetban,$ls_codban,'','',$ld_fecha,$ldec_montomov,$ls_txtctabanext,$rs_datosbene,$ls_metban,'',0,'',$ls_numdoc,$la_seguridad);
						 } 												// $as_ruta,$as_metodo,$ac_codperi,$ad_fdesde,$ad_fhasta,$ad_fecproc,$adec_montot,$as_codcueban,$rs_data,$as_codmetban,$as_desope,$as_quincena,$as_ref,$aa_seguridad
					}
					
						 
					if($li_gentxt=='1'){
						$rs_datprovbene=$io_carord->uf_load_datos_bene_prov($ls_codemp, $ls_codproben, $ls_tipo, $ldec_montomov);
						$ls_ruta	 = "txt/disco_banco/carta_orden";
						@mkdir($ls_ruta,0755);
						require_once("../sno/sigesp_sno_c_metodo_banco.php");
						$io_metodobanco=new sigesp_sno_c_metodo_banco();
						$lb_valido = $io_metodobanco->uf_metodo_banco($ls_ruta,$ls_nommetban,$ls_codban,'','',$ld_fecha,$ldec_montomov,$ls_txtctabanext,$rs_datprovbene,$ls_metban,'',0,'',$ls_numdoc,$la_seguridad,$li_transmer,$arrCredito);
					}		 
			   }
			$ls_codigo=$in_classconfig->uf_buscar_seleccionado();
			if($ls_codigo!="000")//distinto de chequevoucher
				$ls_pagina="reportes/".$ls_report."?codigo=$ls_codigo&codban=$ls_codban&ctaban=$ls_cuenta_banco&numdoc=$ls_numcarord&chevau=&codope=ND&tipproben=$ls_tipo&solicitud=$ls_solicitudes&fecha=$ld_fecha&numcarord=$ls_numdoc";
			else
				$ls_pagina="reportes/".$ls_report_voucher."?codban=$ls_codban&ctaban=$ls_cuenta_banco&numdoc=$ls_numdoc&chevau=&codope=ND";			
			?>
			<script >						
			window.open('<?php print $ls_pagina; ?>',"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=583,height=400,left=50,top=50,location=no,resizable=yes");
			</script>
			<?php 
		}
		else
		{
			$in_classmovbanco->io_sql->rollback();
			$io_msg->message("No pudo registrarse el movimiento".$io_carord->is_msg_error."  ".$in_classmovbanco->is_msg_error);
			//$io_msg->message("No pudo registrarse el movimiento, ya se generó una carta orden para esa solicitud!");
		}		
		uf_nuevo();			
	}
	
 if ($ls_tipo=='-')
	{
	  $rb_n="checked";
	  $rb_p="";
	  $rb_b="";			
	}
 if ($ls_tipo=='P')
	{
	  $rb_n="";
	  $rb_p="checked";
	  $rb_b="";			
	}
 if ($ls_tipo=='B')
	{
	  $rb_n="";
	  $rb_p="";
	  $rb_b="checked";			
	}
?>
  <form action="" method="post" name="form1" id="sigesp_scb_p_carta_orden_mnd.php">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <br>
  <table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr class="titulo-ventana">
      <td height="22" colspan="4"><input name="hidcodtipfon" type="hidden" id="hidcodtipfon" value="<?php echo $ls_codtipfon; ?>">
        <input name="hiddentipfon" type="hidden" id="hiddentipfon">
        <input name="hidmonmaxmov" type="hidden" id="hidmonmaxmov" value="<?php echo $ld_monmaxmov; ?>">
        Carta Orden 
      <input name="hidestciescg" type="hidden" id="hidestciescg" value="<?php echo $li_estciescg; ?>">
      <input name="hidmesabi" type="hidden" id="hidmesabi" value="true"></td>
    </tr>
    <tr>
      <td height="13" colspan="4">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">N&uacute;mero</td>
      <td height="22"><input name="txtdocumento" type="text" id="txtdocumento" value="<?php print $ls_documento;?>" size="24" maxlength="15" onBlur="javascript:rellenar_cad(this.value,15,'doc');" style="text-align:center" <?php echo $ls_disable; ?>>
      <input name="estmovld" type="hidden" id="estmovld" value="<?php print $ls_estmov;?>"></td>
      <td height="22" style="text-align:right"><div align="center"><span style="text-align:left">No. Orden Pago Ministerio
        <input name="txtnumordpagmin" type="text" id="txtnumordpagmin" onKeyPress="return keyRestrict(event,'0123456789'); " value="<?php echo $ls_numordpagmin; ?>" size="20" maxlength="15" style="text-align:center" readonly>
  &nbsp;<a href="javascript:uf_catalogo_ordenes();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar Ordenes de Pago Ministerio..." width="15" height="15" border="0" title="Buscar Ordenes de Pago Ministerio..."></a></span></div></td>
      <td height="22">Fecha
      <input name="txtfecha" type="text" id="txtfecha" value="<?php print $ld_fecha;?>" size="15" maxlength="10" style="text-align:left" datepicker="true" <?php echo $ls_disable; ?> onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);uf_validar_estatus_mes();"></td>
    </tr>
      <script >uf_validar_estatus_mes();</script>
	<tr>
      <td height="22" style="text-align:right">Tipo Concepto</td>
      <td height="22" colspan="3"><?php $obj_con->uf_cargar_conceptos($ls_mov_operacion,$ls_codconmov);	?>
          <input name="codconmov" type="hidden" id="codconmov" value="<?php print $ls_codconmov;?>">
      </td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Concepto</td>
      <td height="22" colspan="3"><input name="txtconcepto" type="text" id="txtconcepto" value="<?php print $ls_desmov;?>" size="127" onKeyPress="return keyRestrict(event,'0123456789'+'abcdefghijklmnopqrstuvwxyz&ntilde; .,*/-()$%&!&ordm;&ordf;&aacute;&eacute;&iacute;&oacute;&uacute;[]{}<>')" <?php echo $ls_disable; ?>></td>
    </tr>
    
    <tr>
      <td height="22" style="text-align:right">Tipo Destino</td>
      <td height="22">
        <select name="cmbtipdes" id="cmbtipdes" onChange="uf_cambio();" <?php echo $ls_disable; ?>>
          <option value="-" <?php print $ls_selnin ?>>---seleccione---</option>
          <option value="P" <?php print $ls_selpro ?>>Proveedor</option>
          <option value="B" <?php print $ls_selben ?>>Beneficiario</option>
        </select>
      &nbsp;&nbsp;&nbsp;&nbsp;</td>
<!--      <td height="22" style="text-align:right">&nbsp;</td>-->
      <td height="22" style="text-align:right">Generar TXT</td>
      <td height="22"><input name="chkgentxt" type="checkbox" class="sin-borde" id="chkgentxt" value="1" <?php print $ls_enabled; ?> onClick="javascript:uf_check_boton2();" <?php print $ls_checked2;echo $ls_disable; ?>></td>
      <td height="22">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Tipo Vi&aacute;tico</td>
      <td height="22"><input name="chktipvia" type="checkbox" class="sin-borde" id="chktipvia" value="1" <?php print $ls_disabled; ?> onClick="javascript:uf_check_boton();" <?php print $ls_checked;echo $ls_disable; ?>></td>
      <td height="22" colspan="2">
        M&eacute;todo a Banco
        <label>
        <input name="txtmetban" type="text" id="txtmetban" value="<?php print $ls_metban ?>" size="6" maxlength="4" readonly style="text-align:center">
      <img src="../shared/imagebank/tools15/buscar.gif" name="buscarmetban" width="15" height="15" id="buscarmetban" <?php print $ls_style ?> onClick="javascript:uf_load_metodos_banco();"> 
      <input name="txtnommetban" type="text" class="sin-borde" id="txtnommetban" style="text-align:left" value="<?php print $ls_nommetban ?>" size="60" maxlength="60" readonly>
      </label></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Transferencia Mecantil</td>
      <td height="22" colspan="3"><input name="chktransmer" type="checkbox" class="sin-borde" id="chktransmer" value="1" <?php print $ls_checktransmer?>></td>
    </tr>
    <tr>
      <td width="94" height="22" style="text-align:right">Banco</td>
      <td height="22" colspan="3" style="text-align:left"><input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban;?>" size="10" readonly>
          <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
          <input name="txtdenban" type="text" id="txtdenban" value="<?php print $ls_denban?>" size="105" class="sin-borde" readonly>
      </td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta</td>
      <td height="22" colspan="3">
          <input name="txtcuenta"        type="text"   id="txtcuenta"    style="text-align:center" value="<?php print $ls_cuenta_banco; ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion"  type="text"   class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php print $ls_dencuenta_banco; ?>" size="85" maxlength="254" readonly>
          <input name="txttipocuenta"    type="hidden" id="txttipocuenta">
          <input name="txtdentipocuenta" type="hidden" id="txtdentipocuenta">
          <input name="txtctabanext"    type="hidden" id="txtctabanext" value="<?php print $ls_txtctabanext; ?>"></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta Contable</td>
      <td width="165" height="22" style="text-align:left"><input name="txtcuenta_scg" type="text" id="txtcuenta_scg" style="text-align:center" value="<?php print $ls_cuenta_scg;?>" size="24" readonly></td>
      <?php
	    if(($ls_contintmovban==1))
		{
	  ?>
	  <td width="345" height="22" style="text-align:left">Nº Control Interno
      <input name="txtnumconint" type="text" id="txtnumconint" onBlur="javascript: ue_rellenarcampo(this,15);" value="<?php print $ls_numcontint;?>" <?php if(($ls_operacion!="NUEVO")){print "readonly";} ?> onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnopqrstuvwxyz'+'-/');" maxlength="15" style="text-align:center"></td>
	   <?php
	  }
	  else
	  {
	  ?>
	  </td>
	  <?php
	  }
	  ?>
	  <td width="345" height="22" style="text-align:left">Disponible
      <input name="txtdisponible" type="text" id="txtdisponible" style="text-align:right" value="<?php print $ldec_disponible;?>" size="22" readonly></td>
      <td width="156" height="22" style="text-align:left">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Total</td>
      <td height="22"><input name="totalchq" type="text" id="totalchq" style="text-align:right" value="<?php print number_format($ldec_montomov,2,',','.'); ?>" size="24" readonly></td>
      <td height="22" style="text-align:left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M.O.R 
          <input name="txtmonobjret" type="text" id="txtmonobjret" style="text-align:right" value="<?php print  number_format($ldec_monobjret,2,',','.'); ?>" size="22" readonly>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      <td height="22"><span style="text-align:left">Monto Retenido</span>
        <input name="txtretenido" type="text" id="txtretenido" value="<?php print number_format($ldec_montoret,2,",",".");?>" size="22" maxlength="22" style="text-align:right" readonly>
		<a href="javascript:uf_cat_deducciones();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Cat&aacute;logo de Deducciones" title="Cat&aacute;logo de Deducciones" width="15" height="15" border="0"></td></tr>
    <tr>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="4"><div align="center"><?php $io_grid->make_gridScroll($li_rows,$title,$object,760,'Solicitudes Programadas',$grid,145);?>
        <input name="fila_selected" type="hidden" id="fila_selected">
        <input name="totalrows" type="hidden" id="totalrows" value="<?php print $li_rows;?>">
        <input name="operacion" type="hidden" id="operacion">
        <input name="estmov" type="hidden" id="estmov" value="<?php print $ls_estmov;?>">
		<input name="hidconint" type="hidden" id="hidconint" value="<?php print $ls_contintmovban;?>">
		<input name="fuente" type="hidden" id="fuente" value="<?php print $ls_fuente;?>">
		<input name="fuente1x1000" type="hidden" id="fuente1x1000" value="<?php print $ls_fuente1x1000;?>">
		<input name="controlguard" type="hidden" id="controlguard" value="<?php echo $ls_controlguard; ?>">
      </div></td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form>
</body>
<script >
f=document.form1;
var patron = new Array(2,2,4);
var irnuevo ="<?php print $ls_irnuevo; ?>";
if (irnuevo=="1")
{
	ue_nuevo();
}

function ue_nuevo()
{
  if (uf_evaluate_cierre())
     {	
	   f.operacion.value ="NUEVO";
	   f.controlguard.value=0;
	   f.action="sigesp_scb_p_carta_orden_mnd.php";
	   f.submit();
	}
}	
	
function ue_guardar()
{
  valido = true;
  lb_mesabi = f.hidmesabi.value;
  ls_conint = f.hidconint.value;
  if (lb_mesabi=='true')
     {
	  if (uf_evaluate_cierre('SCG'))
		 {
		   ls_numdoc   = f.txtdocumento.value;
		   ls_concepto = f.txtconcepto.value;
		   ldec_monto  = f.totalchq.value;
		   ldec_monto  = uf_convertir_monto(ldec_monto);
		   ls_fecha	   = f.txtfecha.value;
		   ls_codban   = f.txtcodban.value;
		   ls_cuenta   = f.txtcuenta.value;
		   ls_tipdes   = f.cmbtipdes.value;
		   ls_metban   = f.txtmetban.value;
		   if (ls_conint==0)
		   {
		   	ls_numconint=" ";
		   }
		   else
		   {
		   	ls_numconint= f.txtnumconint.value;
		   }
		   if (f.chktipvia.checked==true)
			  {
				li_tipvia = '1';
			  }
		   else
			  {
				li_tipvia ='0';
			  }
		   li_totrows  = f.totalrows.value;
		   if ((ls_numdoc!="")&&(ls_concepto!="")&&(ldec_monto>0) && (ls_fecha!="") && (ls_codban!="") && (ls_cuenta!="") && (li_totrows>0))
			  {
				if (li_tipvia=='1' && ls_metban=="")
				   {
					 alert("Debe seleccionar un Método a Banco !!!");
				   }
				else
				   {
					li_total=f.totalrows.value;
					for(i=1;((i<=li_total)&&valido);i++)
					{
						if(eval("f.chk"+i+".checked"))
						{
							ld_fecha1=f.txtfecha.value;
							ld_fecha2  = eval("f.txtfecemisol"+i+".value");
							valid_fecha=validarFechaSol(ld_fecha2,ld_fecha1);
							if (valid_fecha==false)
							{
								valido = false;
								alert("La fecha de la carta orden no debe ser menor a la solicitud de pago !!!");
							}
							if (valid_fecha)
							{
								ld_fecha1= eval("f.txtfecpag"+i+".value");
								ld_fecha2  = eval("f.txtfecemisol"+i+".value");
								valid_fecha=validarFechaSol(ld_fecha2,ld_fecha1);
								if (valid_fecha==false)
								{
									valido = false;
									alert("La fecha de la carta orden no debe ser menor a la solicitud de pago !!!");
								}
							}
						}
					}
					if(valido)
					{
						 ld_totmondis = f.txtdisponible.value;
						 ls_tipvaldis = "<?php echo $ls_tipvaldis; ?>";
						 lb_valido    = uf_validar_disponible("ND",ls_tipvaldis,ld_totmondis,f.totalchq.value);
						 if(lb_valido)
						 {
							 
							if(f.controlguard.value=="1")
							{
								lb_valido=false;
							}
						 }
						 if (lb_valido)
							{
							  f.controlguard.value=1;
							  f.operacion.value ="GUARDAR";
							  f.action="sigesp_scb_p_carta_orden_mnd.php";
							  f.submit();
							}
					   }
					  }
			  }
		   else
			  {
				alert("Complete todos los datos para poder registrar la Carta Orden !!!");
			  }	
		 }
	 }
  else
     {
	   alert("Operación No puede ser procesada, El Més está Cerrado !!!");
	 }
}

function uf_cargar_dt()
{
  f.txtcodban.value 	    = "";
  f.txtdenban.value 	    = "";
  f.txtcuenta.value 	    = "";
  f.txtdenominacion.value = "";
  f.totalchq.value        = "0,00";
  f.txtmonobjret.value    = "0,00";
  f.txtretenido.value     = "0,00";
  f.txttipocuenta.value   = ""; 
  f.txtdentipocuenta.value= ""; 
  f.txtctabanext.value = "";
  f.txtcuenta_scg.value   = ""; 
  f.txtdisponible.value   = "0,00"; 
  f.operacion.value       = "CARGAR_DT";
  f.action				= "sigesp_scb_p_carta_orden_mnd.php";
  f.submit();		
}
	
function uf_cambio()
{
  ls_tipdes = f.cmbtipdes.value;
  	if (f.chkgentxt.checked==false){
  		if (ls_tipdes=='B')
  		 {
  		   f.chktipvia.disabled = false;
  		 } 
  	  	else
  		 {
  		   f.chktipvia.checked = false;
  		   f.chktipvia.disabled = true;
  		   eval("document.images['buscarmetban'].style.visibility='hidden'");
  		   f.txtnommetban.value = "";
  		   f.txtmetban.value = "";
  		 }
	}
  	else{
  		//f.chktipvia.checked = false;
		//f.chktipvia.disabled = true;
		eval("document.images['buscarmetban'].style.visibility='hidden'");
		f.txtnommetban.value = "";
		f.txtmetban.value = "";
  	}
  uf_cargar_dt();
} 

function rellenar_cad(cadena,longitud,campo)
{
  if (cadena!="")
	 {
	   var mystring=new String(cadena);
		   cadena_ceros="";
		   lencad=mystring.length;
	   
	   total=longitud-lencad;
	   for (i=1;i<=total;i++)
		   {
		     cadena_ceros=cadena_ceros+"0";
		   }
	   cadena=cadena_ceros+cadena;
	   if (campo=="doc")
		  {
		    document.form1.txtdocumento.value=cadena;
		  }
	 }	
}
	
function catalogo_cuentabanco()
{
  if (uf_evaluate_cierre('SCG'))
     {
	   ls_codban=f.txtcodban.value;
	   ls_nomban=f.txtdenban.value;
	   if (ls_codban!="")
		  {
		    pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_nomban;
			window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
		  }
	   else
		  {
		    alert("Seleccione el Banco !!!");
		  }
     }
}
	 
function catalogo_cuentascg()
{
 if (uf_evaluate_cierre('SCG'))
    {
	  pagina="sigesp_cat_filt_scg.php?filtro="+'11102'+"&opener=sigesp_scb_d_colocacion.php";
	  window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	}
}
	 	 
function cat_bancos()
{
 if (uf_evaluate_cierre('SCG'))
    {
      pagina="sigesp_cat_bancos.php";
      window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
    }
}
   
function catprovbene()
{
  if (uf_evaluate_cierre('SCG'))
     {
	   if (f.rb_provbene[0].checked==true)
		  {
		    f.txtprovbene.disabled=false;	
			window.open("sigesp_cat_prog_proveedores.php","Catalogo","menubar=no,toolbar=no,scrollbars=yes,width=565,height=400,left=50,top=50,location=no,resizable=yes");
		  }
	   else if(f.rb_provbene[1].checked==true)
		  {
		    f.txtprovbene.disabled=false;	
			window.open("sigesp_cat_prog_beneficiario.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=565,height=400,left=50,top=50,location=no,resizable=yes");
		  }
	 }
}   

function uf_verificar_provbene(lb_checked,obj)
{
  if ((f.rb_provbene[0].checked)&&(obj!='P'))
	 {
	   f.tipo.value='P';		
	 }
  if ((f.rb_provbene[1].checked)&&(obj!='B'))
	 {
	   f.tipo.value='B';
	 }
}

function uf_format(obj)
{
  ldec_monto=uf_convertir(obj.value);
  obj.value=ldec_monto;
}

function validarFechaSol(fechainicio,fechafinal) 
{
	var booleano = true;
	var fechaini = new Date();
	fechaini.setFullYear(parseFloat(fechainicio.substr(0,4)),(parseFloat(fechainicio.substr(5,2))-1),parseFloat(fechainicio.substr(8,2)));
	var fechafin = new Date();
	fechafin.setFullYear(parseFloat(fechafinal.substr(6,4)),(parseFloat(fechafinal.substr(3,2))-1),parseFloat(fechafinal.substr(0,2)));
	if (fechaini.getTime() > fechafin.getTime()) {
		booleano = false;
	}

	return booleano;
}

function uf_selected(li_i)
{
    uf_validar_estatus_mes();
	li_totrows = f.totalrows.value;
	ls_banco   = f.txtcodban.value;
	ls_cuenta  = f.txtcuenta.value;
	li_totsel  = 0;
	for (i=1;i<=li_totrows;i++)
		{
		  if (eval("f.chk"+i+".checked"))
			 {
			   li_totsel++;
			   ls_codban 	 = eval("f.txtcodban"+i+".value");
			   ls_nomban 	 = eval("f.txtnomban"+i+".value");
			   ls_ctaban 	 = eval("f.txtctaban"+i+".value");
			   ls_denctaban  = eval("f.txtdenctaban"+i+".value");
			   ls_codtipcta  = eval("f.txtcodtipcta"+i+".value");
			   ls_nomtipcta  = eval("f.txtnomtipcta"+i+".value");
			   ls_ctabanext  = eval("f.txtctabanext"+i+".value");
			   ls_sccuenta   = eval("f.txtscgcuenta"+i+".value");
			   ld_disponible = eval("f.txtdisponible"+i+".value");
			   ld_fecemisol  = eval("f.txtfecemisol"+i+".value");
			   ls_concepto   = eval("f.txtconsol"+i+".value"); 
			   if (li_totsel==1 && ls_cuenta=="" && ls_banco=="")
				  {
					f.txtcodban.value       = ls_codban;
					f.txtdenban.value       = ls_nomban;
					f.txtcuenta.value       = ls_ctaban;
					f.txtdenominacion.value = ls_denctaban;
					f.txttipocuenta.value   = ls_codtipcta; 
					f.txtdentipocuenta.value= ls_nomtipcta; 
					f.txtctabanext.value   = ls_ctabanext; 
					f.txtcuenta_scg.value   = ls_sccuenta; 
					f.txtdisponible.value   = ld_disponible;
					f.txtconcepto.value     = ls_concepto;
					f.fila_selected.value   = li_i;
					uf_actualizar_monto(li_i);
				  }	 
			   else
				  {
					if (ls_banco!=ls_codban && ls_cuenta!=ls_ctaban)
					   {
						 alert("El Banco o la Cuenta Bancaria son distintos !!!");
						 eval("f.chk"+li_i+".checked=false");
					   }
				  }
			   var feccarord = f.txtfecha.value;
				  if(!validarFechaSol(ld_fecemisol,feccarord)) {
					  alert("La fecha de la solicitud es mayor a la de la carta orden !!!");
					  eval("f.chk"+li_i+".checked=false");
					  f.txtcodban.value       = '';
					  f.txtdenban.value       = '';
					  f.txtcuenta.value       = '';
					  f.txtdenominacion.value = '';
					  f.txttipocuenta.value   = ''; 
					  f.txtdentipocuenta.value= ''; 
					  f.txtctabanext.value   = ''; 
					  f.txtcuenta_scg.value   = ''; 
					  f.txtdisponible.value   = 0; 
					  f.fila_selected.value   = li_i;
					  uf_actualizar_monto(li_i);
				  }
			 }
		}
	if (li_totsel==0)
	   {
		 f.txtcodban.value 	     = "";
		 f.txtdenban.value 	     = "";
		 f.txtcuenta.value 	     = "";
		 f.txtdenominacion.value = "";
		 f.totalchq.value        = "0,00";
		 f.txtmonobjret.value    = "0,00";
		 f.txtretenido.value     = "0,00";
		 f.txttipocuenta.value   = ""; 
		 f.txtdentipocuenta.value= ""; 
         f.txtctabanext.value   = ''; 
		 f.txtcuenta_scg.value   = ""; 
		 f.txtdisponible.value   = "0,00"; 
	   }
	uf_calcular();
}
	
function uf_actualizar_monto(li_i)
{
	ldec_monto			= eval("f.txtmonto"+li_i+".value");
	ldec_montopendiente = eval("f.txtmontopendiente"+li_i+".value");
	ldec_temp1=ldec_monto;
	ldec_temp2=ldec_montopendiente;
	while(ldec_temp1.indexOf('.')>0)
	{//Elimino todos los puntos o separadores de miles
		ldec_temp1=ldec_temp1.replace(".","");
	}
	ldec_temp1=ldec_temp1.replace(",",".");
	while(ldec_temp2.indexOf('.')>0)
	{//Elimino todos los puntos o separadores de miles
		ldec_temp2=ldec_temp2.replace(".","");
	}
	ldec_temp2=ldec_temp2.replace(",",".");

	if(parseFloat(ldec_temp1)<=parseFloat(ldec_temp2))
	{
		eval("f.txtmonto"+li_i+".value='"+uf_convertir(ldec_monto)+"'");
	}
	else
	{
		alert("Monto a cancelar no puede ser mayor al monto pendiente");
		eval("f.txtmonto"+li_i+".value='"+ldec_montopendiente+"'");	
		eval("f.txtmonto"+li_i+".focus()");
	}
	uf_calcular();
}
	
function uf_calcular()
{
		li_total=f.totalrows.value;
		ldec_total=0;
		for (i=1;i<=li_total;i++)
		    {
			  if (eval("f.chk"+i+".checked"))
			     {
				   ldec_monto=eval("f.txtmonto"+i+".value");
				   while(ldec_monto.indexOf('.')>0)
				        {
					      ldec_monto=ldec_monto.replace(".","");
				        }
				   ldec_monto = ldec_monto.replace(",",".");
				   ldec_total = parseFloat(ldec_total)+parseFloat(ldec_monto);
				   f.totalchq.value=uf_convertir(ldec_total);
				   f.txtmonobjret.value=uf_convertir(ldec_total);
			     }
		    }
}
   
function uf_cat_deducciones() 
{
	alert("Las retenciones deben aplicarse a través del módulo de Cuentas por Pagar");
/*
  if (uf_evaluate_cierre('SCG'))
     {
	   if ((f.fuente.value=="B")||(f.fuente1x1000.value=="B") )
          {
	        ls_documento=f.txtdocumento.value;
	        ldec_monto=f.totalchq.value;
	        ldec_monto=uf_convertir_monto(ldec_monto);//Lo convierto a decimal separado solo por punto( . )
	        ldec_monobjret=f.txtmonobjret.value;
			if(f.fuente.value=="B")
			{
				ls_origen="1";
			}
			else
			{
				ls_origen="0";
			}
			if(f.fuente1x1000.value=="B")
			{
				ls_origen1x1000="1";
			}
			else
			{
				ls_origen1x1000="0";
			}
	        ldec_monobjret=uf_convertir_monto(ldec_monobjret);//Lo convierto a decimal separado solo por punto( . )
	        if ((ls_documento!="")&&(ldec_monto>0)&&(ldec_monobjret>0)&&(ldec_monto>=ldec_monobjret))   
	           {
		         ldec_monto=uf_convertir(ldec_monto);//Lo convierto a formato decimal con separdores de miles y decimales
		         ldec_monobjret=uf_convertir(ldec_monobjret);//Lo convierto a formato decimal con separdores de miles y decimales
		         pagina="sigesp_cat_deducciones.php?monto="+ldec_monto+"&objret="+ldec_monobjret+"&txtdocumento="+ls_documento+"&origen="+ls_origen+"&origen1x1000="+ls_origen1x1000;
		         window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	           }
	        else
	           {
			     if (ls_documento=="")
			        {
				      alert("Introduzca un numero de documento");
			        }
			     else if(ldec_monto<=0)
			        {
				      alert("El monto de be ser mayor a cero(0)");
			        }
			     else if(ldec_monobjret<=0)
			        {
				      alert("La base imponible debe ser mayor a cero(0)");
			        }
			     else if(ldec_monto<ldec_monobjret)
			        {
				      alert("Base imponible no puede ser mayor al monto del documento");
				      f.txtmonobjret.value=uf_convertir(ldec_monto);
			        }
	           }
          }
	   else if (f.fuente.value=="B")
          {
	        ls_documento=f.txtdocumento.value;
	        ldec_monto=f.totalchq.value;
	        ldec_monto=uf_convertir_monto(ldec_monto);//Lo convierto a decimal separado solo por punto( . )
	        ldec_monobjret=f.txtmonobjret.value;	
	        ls_origen="1";
	        ldec_monobjret=uf_convertir_monto(ldec_monobjret);//Lo convierto a decimal separado solo por punto( . )
	        if ((ls_documento!="")&&(ldec_monto>0)&&(ldec_monobjret>0)&&(ldec_monto>=ldec_monobjret))   
	           {
		         ldec_monto=uf_convertir(ldec_monto);//Lo convierto a formato decimal con separdores de miles y decimales
		         ldec_monobjret=uf_convertir(ldec_monobjret);//Lo convierto a formato decimal con separdores de miles y decimales
		         pagina="sigesp_cat_deducciones.php?monto="+ldec_monto+"&objret="+ldec_monobjret+"&txtdocumento="+ls_documento+"&origen="+ls_origen;
		         window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
	           }
	        else
	           {
			     if (ls_documento=="")
			        {
				      alert("Introduzca un numero de documento");
			        }
			     else if(ldec_monto<=0)
			        {
				      alert("El monto de be ser mayor a cero(0)");
			        }
			     else if(ldec_monobjret<=0)
			        {
				      alert("La base imponible debe ser mayor a cero(0)");
			        }
			     else if(ldec_monto<ldec_monobjret)
			        {
				      alert("Base imponible no puede ser mayor al monto del documento");
				      f.txtmonobjret.value=uf_convertir(ldec_monto);
			        }
	           }
          }  
	   else
          {
		    alert("Las retenciones municipales deben aplicarse a través del módulo de Cuentas por Pagar");
          } 
	 }
*/}
   
function uf_validar_monobjret(txtmonobjret)
{
		ldec_monobjret=txtmonobjret.value;
		ldec_monto=f.totalchq.value;
		while(ldec_monto.indexOf('.')>0)
		{//Elimino todos los puntos o separadores de miles
			ldec_monto=ldec_monto.replace(".","");
		}
		ldec_monto=ldec_monto.replace(",",".");
		if(ldec_monto>=ldec_monobjret)
		{
			txtmonobjret.value=uf_convertir(ldec_monobjret);
		}
		else
		{
			txtmonobjret.value=uf_convertir(ldec_monto);
			alert("Monto Objeto a Retención no puede ser mayor al monto total del Cheque.");
			txtmonobjret.focus();
		}	
	}

function uf_load_metodos_banco()
{
   pagina="sigesp_scb_cat_metodobanco.php";
   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=568,height=400,resizable=yes,location=no");
}

function uf_check_boton()
{
  if (f.chktipvia.checked==true)
     {
       eval("document.images['buscarmetban'].style.visibility='visible'");
	 }
  else
     {
	   eval("document.images['buscarmetban'].style.visibility='hidden'");
	 }
  uf_cargar_dt();
}

function uf_check_boton2()
{
  if (f.chkgentxt.checked==true)
     {
       eval("document.images['buscarmetban'].style.visibility='visible'");
	 }
  else
     {
	   eval("document.images['buscarmetban'].style.visibility='hidden'");
	 }
  uf_cargar_dt();
}

function uf_evaluate_cierre(as_tipafe)
{
  lb_valido = true;
  if (as_tipafe=='SPG' || as_tipafe=='SPI')
     {
       li_estciespg = f.hidestciespg.value;
       li_estciespi = f.hidestciespi.value;
	   if (li_estciespg==1 || li_estciespi==1)
		  {
		    lb_valido = false;
		    alert("Ya fué procesado el Cierre Presupuestario, No pueden efectuarse movimientos, Contacte al Administrador del Sistema !!!");
		  }	   
	 }
  else
     {
	   if (as_tipafe=='SCG')
	      {
  		    li_estciescg = f.hidestciescg.value;
			if (li_estciescg==1)
			   {
			     lb_valido = false;
			     alert("Ya fué procesado el Cierre Contable, No pueden efectuarse movimientos, Contacte al Administrador del Sistema !!!");
			   }
		  }
	 }
  return lb_valido
}

function uf_catalogo_ordenes()
{
  pagina="sigesp_scb_cat_ordenes_pago_ministerio.php?origen=CO";
  window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=800,height=450,resizable=yes,location=no,dependent=yes");
}
function ue_descargar(ruta)
{
  window.open("sigesp_scb_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}	

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>