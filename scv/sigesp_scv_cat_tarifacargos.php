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
<title>Cat&aacute;logo de Tarifas por Cargos</title>
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
$io_conect= new sigesp_include();
$conn= $io_conect->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$io_sql= new class_sql($conn);
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_dsclasi= new class_datastore();

if (array_key_exists("catalogo",$_GET))
{$ls_catalogo= $_GET["catalogo"];}
else
{$ls_catalogo="";}
if (array_key_exists("hidcont",$_POST))
{
	$ls_codcont = $_POST["hidcont"];   
}
else
{
	$ls_codcont = $_GET["hidcont"];
}
$ls_sql="SELECT scv_tarifacargos.*,".
		"       (SELECT denmon FROM sigesp_moneda ".
		"         WHERE scv_tarifacargos.codmon=sigesp_moneda.codmon) AS denmon".
		"  FROM scv_tarifacargos".
		" ORDER BY codtar ASC ";
$rs_data= $io_sql->select($ls_sql);
$data= $rs_data;
?>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Tarifas por Cargo </td>
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
     $io_dsclasi->data= $data;
     $totrow= $io_dsclasi->getRowCount("codtar");
     print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	 print "<tr class=titulo-celda>";
	 print "<td>Código</td>";
	 print "<td>Denominación</td>";
 	 print "</tr>";
	 for ($z=1;$z<=$totrow;$z++)
         {
			print "<tr class=celdas-blancas>";
			$ls_codigo       = $data["codtar"][$z];
			$ls_denominacion = $data["dentar"][$z];
			$ls_tipvia = $data["tipvia"][$z];
			$ls_exterior = $data["exterior"][$z];
			$ls_codmon = $data["codmon"][$z];
			$ls_denmon = $data["denmon"][$z];
			if($ls_codmon=="---")
			{
				$ls_codmon = "";
				$ls_denmon = "";
			}
			print "<td><a href=\"javascript: aceptar('$ls_codigo','$ls_denominacion','$ls_tipvia','$ls_exterior','$ls_codmon','$ls_denmon');\">".$ls_codigo."</a></td>";
			print "<td>".$ls_denominacion."</td>";
			print "</tr>";			
         }
	 print "</table>";
     $io_sql->free_result($rs_data);
   }
else
   { ?>
	  <script >
	  alert("No se han creado Tarifas");
	  close();
	  </script>
	  <?php
   }		 

?>
  </div>
</form>
</body>
<script >
function aceptar(codigo,denominacion,tipvia,exterior,codmon,denmon)
{
	f= opener.document.form1;
	f.txtcodtar.value= codigo;
	f.txtdentar.value= denominacion;
	f.txtcodmon.value= codmon;
	f.txtdenmon.value= denmon;
	f.cmbtipvia.value= tipvia;
	f.existe.value= "TRUE";
	f.hidestatus.value= 'GRABADO';
	f.operacion.value= 'CARGAR';
	if(exterior==1)
	{
		f.chkexterior.checked=true;
	}
	else
	{
		f.chkexterior.checked=false;
	}
	f.submit();
	close();
}
</script>
</html>