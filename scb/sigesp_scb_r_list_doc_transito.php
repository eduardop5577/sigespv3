<?Php
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
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
$ls_logusr = $_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$ls_permisos="";
$la_seguridad=Array();
$la_permisos=Array();
$arrResultado=$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_r_list_doc_transito.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado["as_permisos"];
$la_seguridad=$arrResultado["aa_seguridad"];
$la_permisos=$arrResultado["aa_permisos"];

$li_diasem = date('w');
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
<title>Documentos en Transito</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
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
</style></head>
<body>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a><a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></td>
  </tr>
</table>
  <?Php
require_once("../shared/class_folder/grid_param.php");
$io_grid=new grid_param();

require_once("../base/librerias/php/general/sigesp_lib_include.php");
$sig_inc=new sigesp_include();
$con=$sig_inc->uf_conectar();



$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ld_fecha=$_POST["txtfecha"];
	$ls_codban=$_POST["txtcodban"];;
	$ls_denban=$_POST["txtnomban"];
	$ls_cuenta_banco=$_POST["txtcuenta"];
	$ls_dencuenta_banco=$_POST["txtdenctaban"];
	$ls_periodo       = $_POST["txtperiodo"];
	$ls_mes     	  = $_POST["cmbmes"];
}
else
{
	$ls_operacion="";	
	$ls_codban="";
	$ls_denban="";
	$ls_cuenta_banco="";
	$ls_dencuenta_banco="";
	$ls_periodo=substr($la_emp["periodo"],0,4);
	$ls_mes='01';
	$ld_fecha=$ls_mes."/".$ls_periodo;
}
$lb_01=""; $lb_02=""; $lb_03=""; $lb_04="";	$lb_05=""; $lb_06=""; 
$lb_07="";         $lb_08=""; $lb_09=""; $lb_10=""; $lb_11=""; $lb_12="";

switch ($ls_mes){
		case '01':
			$lb_01="selected";
			break;
		case '02':
			$lb_02="selected";			
			break;
		case '03':
			$lb_03="selected";
			break;
		case '04':
			$lb_04="selected";
			break;
		case '05':
			$lb_05="selected";
			break;
		case '06':
			$lb_06="selected";
			break;	
		case '07':
			$lb_07="selected";
			break;
		case '08':
			$lb_08="selected";
			break;
		case '09':
			$lb_09="selected";
			break;
		case '10':
			$lb_10="selected";
			break;
		case '11':
			$lb_11="selected";
			break;
		case '12':
			$lb_12="selected";				
			break;
	}	
?>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="535" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="65"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Documentos en Tr&aacute;nsito</td>
    </tr>
    <tr>
      <td height="13" colspan="4" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="4" style="visibility:hidden">&nbsp;&nbsp;Reporte en
        <select name="cmbbsf" id="cmbbsf">
          <option value="0" selected>Bs.</option>
          <option value="1">Bs.F.</option>
        </select></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Banco</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" size="10" readonly>
        <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
        <input name="txtdenban" type="text" id="txtdenban" size="51" class="sin-borde" readonly>
        <input name="txttipocuenta" type="hidden" id="txttipocuenta">
        <input name="txtdentipocuenta" type="hidden" id="txtdentipocuenta">
        <input name="txtcuenta_scg" type="hidden" id="txtcuenta_scg" style="text-align:center" value="<?php print $ls_cuenta_scg;?>" size="24" readonly>
        <input name="txtdisponible" type="hidden" id="txtdisponible" style="text-align:right" size="24" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" size="45" maxlength="254" readonly>
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Periodo</td>
      <td height="22" colspan="3" align="center"><div align="left"><input name="txtfecha" type="text" id="txtfecha" style="text-align:center" value="<?php print $ld_fecha;?>" size="10" maxlength="7" readonly>
          <span class="style1">Mes/A&ntilde;o</span>
          <select name="cmbmes" onChange="javascript: uf_periodo(this);">
            <option value="01" <?php print $lb_01;?>>ENERO</option>
            <option value="02" <?php print $lb_02;?>>FEBRERO</option>
            <option value="03" <?php print $lb_03;?>>MARZO</option>
            <option value="04" <?php print $lb_04;?>>ABRIL</option>
            <option value="05" <?php print $lb_05;?>>MAYO</option>
            <option value="06" <?php print $lb_06;?>>JUNIO</option>
            <option value="07" <?php print $lb_07;?>>JULIO</option>
            <option value="08" <?php print $lb_08;?>>AGOSTO</option>
            <option value="09" <?php print $lb_09;?>>SEPTIEMBRE</option>
            <option value="10" <?php print $lb_10;?>>OCTUBRE</option>
            <option value="11" <?php print $lb_11;?>>NOVIEMBRE</option>
            <option value="12" <?php print $lb_12;?>>DICIEMBRE</option>
          </select>
          <input name="txtperiodo" type="text" id="txtperiodo" value="<?php print $ls_periodo?>" size="6" maxlength="4" style="text-align:center" readonly></div>        </td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Ordenar</td>
      <td width="143" height="22" align="center"><div align="left">
        <select name="orden">
          <option value="D">Documento</option>
          <option value="F">Fecha</option>
          <option value="O">Operacion</option>
        </select>
</div></td>
      <td width="80" height="22" align="center">&nbsp;</td>
      <td width="245" height="22" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><div align="right">     <span class="Estilo1">
        <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
      </span></div></td>
    </tr>
  </table>
 
</table>
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">

function ue_openexcel()
{  f=document.form1;
  ls_periodo     = f.txtfecha.value;
  ls_codban      = f.txtcodban.value;
  ls_ctaban      = f.txtcuenta.value;
  ls_denban      = f.txtdenban.value;
  ls_dencta      = f.txtdenominacion.value;
  ls_orden       = f.orden.value;
  ls_tiporeporte = f.cmbbsf.value;
  li_imprimir    = f.imprimir.value;
  if (li_imprimir=='1')
     {
       if ((ls_periodo!="")&&(ls_codban!="")&&(ls_ctaban!=""))
          {
	        pagina="reportes/sigesp_scb_rpp_list_doc_transito_excel.php?periodo="+ls_periodo+"&codban="+ls_codban+"&ctaban="+ls_ctaban+"&orden="+ls_orden+"&denban="+ls_denban+"&dencta="+ls_dencta+"&tiporeporte="+ls_tiporeporte;
	        window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
          }
	   else
	      {
		   alert("Seleccione los parametros de busqueda");
	      }
     }
  else
     {
	   alert("No tiene permiso para realizar esta operaci?n !!!");
	 }

}

function ue_imprimir()
{
  f=document.form1;
  ls_periodo     = f.txtfecha.value;
  ls_codban      = f.txtcodban.value;
  ls_ctaban      = f.txtcuenta.value;
  ls_denban      = f.txtdenban.value;
  ls_dencta      = f.txtdenominacion.value;
  ls_orden       = f.orden.value;
  ls_tiporeporte = f.cmbbsf.value;
  li_imprimir    = f.imprimir.value;
  if (li_imprimir=='1')
     {
       if ((ls_periodo!="")&&(ls_codban!="")&&(ls_ctaban!=""))
          {
	        pagina="reportes/sigesp_scb_rpp_list_doc_transito.php?periodo="+ls_periodo+"&codban="+ls_codban+"&ctaban="+ls_ctaban+"&orden="+ls_orden+"&denban="+ls_denban+"&dencta="+ls_dencta+"&tiporeporte="+ls_tiporeporte;
	        window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
          }
	   else
	      {
		   alert("Seleccione los parametros de busqueda");
	      }
     }
  else
     {
	   alert("No tiene permiso para realizar esta operaci?n !!!");
	 }
}	 

	function catalogo_cuentabanco()
	{
	  f=document.form1;
	  ls_codban=f.txtcodban.value;
	  ls_denban=f.txtdenban.value;
	  if (ls_codban!="")
		 {
		   pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_denban;
		   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=620,height=400,resizable=yes,location=no");
		 }
	  else
		 {
		   alert("Seleccione el Banco !!!");
		 }
	 }	
	 	 
	 function cat_bancos()
	 {
	   f=document.form1;
	   pagina="sigesp_cat_bancos.php";
	   window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
	 }

	function uf_periodo(obj)
	{
		f=document.form1;
		ls_ano=f.txtperiodo.value;
		ls_periodo=obj.value;
		ls_periodo=ls_periodo+"/"+ls_ano;
		f.txtfecha.value=ls_periodo;
		uf_cambio();
		
	}
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
