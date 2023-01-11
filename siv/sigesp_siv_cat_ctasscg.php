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
if(array_key_exists("operacion",$_POST))
{
	$ls_tipo=$_POST["tipo"];
}
else
{
	$ls_tipo=$_GET["tipo"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Cuentas Contables</title>
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
    <input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Cuentas Contables</td>
    </tr>
  </table>
  <br>
  <div align="center">
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td align="right" width="122">Cuenta</td>
        <td width="238" height="22"><div align="left">
          <input name="codigo" type="text" id="codigo">        
        </div></td>
        <td width="138">&nbsp;</td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22" colspan="2"><div align="left">
          <input name="nombre" type="text" id="nombre">
<label></label>
<br>
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar </a></div></td>
      </tr>
    </table>
	<br>
<?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
$dat=$_SESSION["la_empresa"];
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);
$ds=new class_datastore();
$as_codemp=$_SESSION["la_empresa"]["codemp"];
$ls_activo=$_SESSION["la_empresa"]["activo"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codigo="%".$_POST["codigo"]."%";
	$ls_denominacion="%".$_POST["nombre"]."%";
}
else
{
	$ls_operacion="";
}
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Cuenta Contable</td>";
print "<td>Denominación</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_sql="";
	if($ls_tipo=="centrocostos")
	{
		$ls_sql= " AND sc_cuenta like '".$ls_activo."%'";
	}
	$ls_cadena=" SELECT * FROM scg_cuentas ".
			   " WHERE codemp = '".$as_codemp."'".
		       " AND sc_cuenta like '".$ls_codigo."'".
		       " AND denominacion like '".$ls_denominacion."'".
			   $ls_sql.
		       " ORDER BY sc_cuenta";
	$rs_cta=$io_sql->select($ls_cadena);
	$li_row=$io_sql->num_rows($rs_cta);
	if($li_row>0)
	{
		while($row=$io_sql->fetch_row($rs_cta))
		{
			$ls_sccuenta=$row["sc_cuenta"];
			$ls_denctacon=$row["denominacion"];
			$status=$row["status"];
			if($status=="S")
			{
				print "<tr class=celdas-blancas>";
				print "<td>".$ls_sccuenta."</td>";
				print "<td  align=left>".$ls_denctacon."</td>";
			}
			else
			{
				if($ls_tipo=="centrocostos")
				{
					print "<tr class=celdas-azules>";
					print "<td><a href=\"javascript: aceptar_centrocostos('$ls_sccuenta','$ls_denctacon','$status');\">".$ls_sccuenta."</a></td>";
					print "<td  align=left>".$ls_denctacon."</td>";
				}
				else
				{
					print "<tr class=celdas-azules>";
					print "<td><a href=\"javascript: aceptar('$ls_sccuenta','$ls_denctacon','$status');\">".$ls_sccuenta."</a></td>";
					print "<td  align=left>".$ls_denctacon."</td>";
				}
			}
			print "</tr>";			
		}
	}
	else
	{
		print "No se han creado Cuentas Contables";
	}
}
print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

  function aceptar(cuenta,denominacion,status)
  {
    opener.document.form1.txtsccuenta.value=cuenta;
	opener.document.form1.txtdensccuenta.value=denominacion;
	 close();
  }

  function aceptar_centrocostos(cuenta,denominacion,status)
  {
    opener.document.form1.txtsccuentainv.value=cuenta;
	opener.document.form1.txtdensccuentainv.value=denominacion;
	 close();
  }

  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_siv_cat_ctasscg.php";
	  f.submit();
  }

</script>
</html>
