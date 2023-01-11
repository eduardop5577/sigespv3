<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Categor&iacute;as de Activos seg&uacute;n clasificaci&oacute;n de SUDEBAN</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
    <input name="txtempresa" type="hidden" id="txtempresa">
    <input name="hidstatus" type="hidden" id="hidstatus">
    <input name="txtnombrevie" type="hidden" id="txtnombrevie">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Categor&iacute;as de Activos seg&uacute;n clasificaci&oacute;n de SUDEBAN</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodigo" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdenominacion" type="text" id="txtdenominacion">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
      </tr>
    </table>
  <br>
    <?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$ds=new class_datastore();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);
$ls_empresa=$_SESSION["la_empresa"]["codemp"];
$ls_tipo="";
if(array_key_exists("tipo",$_GET))
{
	$ls_tipo=$_GET["tipo"];
}
else
{
	if(array_key_exists("tipo",$_POST))
	{
		$ls_tipo=$_POST["tipo"];
	}
}
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codigo="%".$_POST["txtcodigo"]."%";
	$ls_denominacion="%".$_POST["txtdenominacion"]."%";
	$ls_status="%".$_POST["hidstatus"]."%";
}
else
{
	$ls_operacion="BUSCAR";
	$ls_codigo="%%";
	$ls_denominacion="%%";
	$ls_status="%%";
}
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Código</td>";
print "<td>Denominación</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_sql="SELECT codcat,dencat".
			" FROM saf_catsudeban".
			" WHERE codemp='".$ls_empresa."'".
			"   AND codcat like '".$ls_codigo."'".
			"   AND dencat like '".$ls_denominacion."'".
			" ORDER BY codcat";
    $rs_data=$io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$io_msg->message("CLASE->CATALOGO MÉTODO->CATEGORIA SUDEBAN ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		$lb_valido=false;
	}
	else
	{
		while(!$rs_data->EOF)
		{
			$ls_codcat=trim($rs_data->fields["codcat"]);
			$ls_dencat=trim($rs_data->fields["dencat"]);
			print "<tr class=celdas-blancas>";
			if($ls_tipo=="")
			{
				print "<td><a href=\"javascript: aceptar('$ls_codcat','$ls_dencat');\">".$ls_codcat."</a></td>";
			}
			else
			{
				print "<td><a href=\"javascript: aceptar_activos('$ls_codcat','$ls_dencat');\">".$ls_codcat."</a></td>";
			}
			print "<td>".$ls_dencat."</td>";
			print "</tr>";			
			$rs_data->MoveNext();
		}
	}
}
print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
	function aceptar(ls_codigo,ls_denominacion)
	{
		opener.document.form1.txtcodigo.value=ls_codigo;
		opener.document.form1.txtdenominacion.value=ls_denominacion;
		opener.document.form1.hidstatus.value="C";
		close();
	}
	function aceptar_activos(ls_codigo,ls_denominacion)
	{
		opener.document.form1.txtcodsudeban.value=ls_codigo;
		opener.document.form1.txtdensudeban.value=ls_denominacion;
		close();
	}
	function ue_search()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_saf_cat_catsudeban.php";
		f.submit();
	}
</script>
</html>
