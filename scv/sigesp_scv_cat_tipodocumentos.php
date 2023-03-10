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
require_once("class_folder/class_funciones_viaticos.php");
$io_fun_viaticos=new class_funciones_viaticos();
if(array_key_exists("hiddestino",$_POST))
{
	$ls_destino=$io_fun_viaticos->uf_obtenervalor("hiddestino","");
}
else
{
	$ls_destino=$io_fun_viaticos->uf_obtenervalor_get("destino","");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat?logo de Tipos de Documentos</title>
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
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
    <input name="hiddestino" type="hidden" id="hiddestino" value="<?php print $ls_destino;?>">
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo Tipos de Documentos</td>
  </tr>
</table>
<div align="center"><br>
  <?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");

$io_conect=new sigesp_include();
$conn=$io_conect->uf_conectar();
$io_msg=new class_mensajes();
$io_dstipodoc=new class_datastore();
$io_sql=new class_sql($conn);
$arr=$_SESSION["la_empresa"];
if ($ls_destino=="CALCULOINT")
{
	$ls_sql=" SELECT codtipdoc,dentipdoc ".
			"   FROM cxp_documento ".
			"  WHERE estcon=1".
			"    AND estpre=4".
			"  ORDER BY codtipdoc ASC";

}
else
{
	$ls_sql=" SELECT codtipdoc,dentipdoc ".
			"   FROM cxp_documento ".
			"  WHERE estcon=1".
			"    AND estpre=2".
			"  ORDER BY codtipdoc ASC";
}
$rs_tipodoc=$io_sql->select($ls_sql);
$data=$rs_tipodoc;
if($row=$io_sql->fetch_row($rs_tipodoc))
{
	$data=$io_sql->obtener_datos($rs_tipodoc);
	$arrcols=array_keys($data);
	$totcol=count((array)$arrcols);
	$io_dstipodoc->data=$data;
	$totrow=$io_dstipodoc->getRowCount("codtipdoc");
    print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla>";
	print "<tr class=titulo-celda>";
	print "<td>C?digo</td>";
	print "<td>Denominaci?n</td>";
	print "</tr>";
	for($z=1;$z<=$totrow;$z++)
	{
		print "<tr class=celdas-blancas>";
		$ls_codtipdoc =$data["codtipdoc"][$z];
		$ls_dentipdoc =$data["dentipdoc"][$z];
		print "<td style=text-align:center><a href=\"javascript: aceptar('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
		print "<td style=text-align:left>".$ls_dentipdoc."</td>";
		print "</tr>";			
	}
    print "</table>";
}
else
  {
    print "No se han creado Documentos !!!";
  }
$io_sql->free_result($rs_tipodoc);
$io_sql->close();  
?>
</div>
</form>
</body>
<script >
function aceptar(codtipdoc,dentipdoc)
{
	opener.document.form1.txtcodtipdoc.value=codtipdoc;
	opener.document.form1.txtdentipdoc.value=dentipdoc;
	close();
}
</script>
</html>