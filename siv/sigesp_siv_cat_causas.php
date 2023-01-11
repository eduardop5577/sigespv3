<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
<title>Cat&aacute;logo de Causas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/number_format.js"></script>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="js/validaciones.js"></script>
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
	color: #006699#006699;
}
-->
</style></head>

<body>
<form name="form1" method="post" action="">
<input type="hidden" name="campo" id="campo" value="<?php print $ls_campo;?>" >
<input type="hidden" name="orden" id="orden" value="<?php print $ls_orden;?>">
<?php

require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_include=new sigesp_include();
$io_connect=$io_include->uf_conectar();
$io_msg=new class_mensajes();
$io_sql=new class_sql($io_connect);
$io_data=new class_datastore();
$io_funcion=new class_funciones();
$ls_codemp=$_SESSION["la_empresa"]["codemp"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codcau="%".$_POST["txtcodcau"]."%";
	$ls_dencau="%".$_POST["txtdencau"]."%";

}
else
{
	$ls_operacion="";
	$ls_codcau="";
	$ls_dencau="";
}

?>
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  	 <br>
	 <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="22" colspan="2" class="titulo-celdanew">Catalogo de Causas </td>
       </tr>
      <tr>
        <td width="80" height="28"><div align="right">Codigo </div></td>
        <td width="389"><div align="left">
          <input name="txtcodcau" type="text" id="txtcodcau">
</div></td>
      </tr>
      <tr>
        <td height="26"><div align="right">Denominacion</div></td>
        <td><div align="left">
          <input name="txtdencau" type="text" id="txtdencau" size="60">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
	<br>
    <?php


if($ls_operacion=="BUSCAR")
{

	$ls_sql=" SELECT codcau, dencau ".
			" FROM siv_causas ".
			" WHERE siv_causas.codemp='".$ls_codemp."'".
			"   AND siv_causas.codcau like '".$ls_codcau."'".
			"   AND siv_causas.dencau like '".$ls_dencau."'".
			" ORDER BY codcau";
//print $ls_sql;			
	$rs_data=$io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$io_msg->message("No hay registros");
	}
	else
	{
		$i=0;
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td style=text-align:center width=50>C&oacute;digo</td>";
		print "<td style=text-align:center width=270>Denominaci&oacute;n</td>";
		print "</tr>";
		while(!$rs_data->EOF)
		{
			$i++;
			print "<tr class=celdas-blancas>";
			$ls_codcau = $rs_data->fields["codcau"];
			$ls_dencau = $rs_data->fields["dencau"];
			print "<td style=text-align:center width=50><a href=\"javascript: aceptar('$ls_codcau','$ls_dencau');\">".$ls_codcau."</a></td>";
			print "<td style=text-align:left width=50><a href=\"javascript: aceptar('$ls_codcau','$ls_dencau');\">".$ls_dencau."</a></td>";
			echo "</tr>";
		
			$rs_data->MoveNext();
		}
		if($i==0)
		{
			$io_msg->message("No hay Registros");
		}
		print "</table>";
	}
}
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
fop = opener.document.form1;
	function aceptar(codcau,dencau)
	{
		opener.cargarCausas(codcau,dencau);
		close();
	}
	function ue_buscar()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_siv_cat_causas.php";
		f.submit();
	}
</script>
</html>
