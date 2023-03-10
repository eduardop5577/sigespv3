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
<title>Cat&aacute;logo de Ciudades</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
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
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Ciudades </td>
    </tr>
</table>
  <br>
<form name="form1" method="post" action="">
  <div align="center">
    <input name="hidpais" type="hidden" id="hidpais">
    <?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("class_funciones_activos.php");
$io_fun_activos=new class_funciones_activos();

$io_conect=new sigesp_include();
$conn=$io_conect->uf_conectar();
$io_dsmun=new class_datastore();
$io_sql=new class_sql($conn);
if (array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codpais=$_POST["hidpais"];
}
else
{
	$ls_operacion="";
	$ls_codpais=$_GET["codpai"];
}
if (array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codest=$_POST["hidestado"];
}
else
{
	$ls_operacion="";
	$ls_codest=$_GET["codest"];
}
$ls_destino=$io_fun_activos->uf_obtenervalor_get("destino","");
$ls_sql=" SELECT codciu,desciu ".
        " FROM  saf_ciudades ".
		" WHERE codpai= '".$ls_codpais."'".
		" AND   codest= '".$ls_codest."' ".
		" ORDER BY codciu ASC";
$rs_data=$io_sql->select($ls_sql);
$data=$rs_data;
if($row=$io_sql->fetch_row($rs_data))
{
     $data=$io_sql->obtener_datos($rs_data);
	 $arrcols=array_keys($data);
	 $totcol=count((array)$arrcols);
	 $io_dsmun->data=$data;
	 $totrow=$io_dsmun->getRowCount("codciu");
     print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla>";
	 print "<tr class=titulo-celda>";
     print "<td>C?digo</td>";
     print "<td>Denominaci?n</td>";
	 print "</tr>";
	 for($z=1;$z<=$totrow;$z++)
	 {
		print "<tr class=celdas-blancas>";
		$ls_codciu=$data["codciu"][$z];
		$ls_desciu=$data["desciu"][$z];
		print "<td><a href=\"javascript: aceptar('$ls_codciu','$ls_desciu');\">".$ls_codciu."</a></td>";
		print "<td>".$ls_desciu."</td>";
		print "</tr>";			
	 }
     print "</table>";
	$io_sql->free_result($rs_data);
}
else
{ ?>
  <script >
  alert("No se han creado Ciudades para este Estado");
//  close();
  </script>
<?php
}
?>
</div>
</form>
</body>
<script language="JavaScript">
  function aceptar(ls_codciu,ls_desciu)
  {
    opener.document.form1.txtcodciu.value=ls_codciu;
    opener.document.form1.txtdesciu.value=ls_desciu;
	close();
  }
</script>
</html>