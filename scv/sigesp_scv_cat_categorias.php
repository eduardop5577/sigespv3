<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
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
<title>Cat&aacute;logo de Categor&iacute;as de Vi&aacute;ticos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
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
<?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");

$io_conect= new sigesp_include();
$conn = $io_conect->uf_conectar();
$io_dstar= new class_datastore();
$io_sql= new class_sql($conn);
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
$ls_sql= " SELECT * FROM scv_categorias".
		 " WHERE codemp='".$ls_codemp."'".
		 " ORDER BY codcat ASC ";
$rs_data= $io_sql->select($ls_sql);
$data= $rs_data;
?>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Categor&iacute;as de Vi&aacute;ticos</td>
  </tr>
</table>
  <br>
<form name="form1" method="post" action="">
  <div align="center">
    <?php
if ($row=$io_sql->fetch_row($rs_data))
{
	$data= $io_sql->obtener_datos($rs_data);
	$arrcols= array_keys($data);
	$totcol= count((array)$arrcols);
	$io_dstar->data= $data;
	$totrow= $io_dstar->getRowCount("codcat");
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td>C?digo</td>";
	print "<td>Denominaci?n</td>";
	print "</tr>";
	for ($z=1;$z<=$totrow;$z++)
	{
		print "<tr class=celdas-blancas>";
		$ls_codcat= $data["codcat"][$z];
		$ls_dencar= $data["dencat"][$z];
		print "<td><a href=\"javascript: aceptar('$ls_codcat','$ls_dencar');\">".$ls_codcat."</a></td>";
		print "<td>".$ls_dencar."</td>";
		print "</tr>";			
	}
	print "</table>";
	$io_sql->free_result($rs_data);
}
else
{ ?>
	<script >
	alert("No se han creado Categor?as de Vi?ticos");
	close();
	</script>
<?php
}		 

?>
  </div>
</form>
</body>
<script >
function aceptar(ls_codcat,ls_dencat)
{
	opener.document.form1.txtcodcat.value= ls_codcat;
	opener.document.form1.txtdencat.value= ls_dencat;
	opener.document.form1.hidestatus.value= 'C';
	close();
}
</script>
</html>