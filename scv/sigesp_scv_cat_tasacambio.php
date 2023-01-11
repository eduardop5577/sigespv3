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
if(array_key_exists("hidcodtipmon",$_POST))
{
	$ls_codtipmon=$io_fun_viaticos->uf_obtenervalor("hidcodtipmon","");
}
else
{
	$ls_codtipmon=$io_fun_viaticos->uf_obtenervalor_get("codtipmon","");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Monedas</title>
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
<form name="form1" method="post" action="">
    <input name="operacion" type="hidden" id="operacion">
    <input name="hidcodtipmon" type="hidden" id="hidcodtipmon" value="<?php print $ls_codtipmon ?>">
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Monedas</td>
  </tr>
</table>
  <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
    <tr>
      <td width="76"><div align="right">C&oacute;digo</div></td>
      <td width="422" height="22"><div align="left">
          <input name="txtcodmis" type="text" id="txtnombre2">
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Descripci&oacute;n</div></td>
      <td height="22"><div align="left">
          <input name="txtdesmis" type="text" id="txtdesmis">
      </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
    </tr>
  </table>
  <br>
  <div align="center">
    <?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$ds=new class_datastore();
$io_sql=new class_sql($con);
$arr=$_SESSION["la_empresa"];
require_once("class_folder/sigesp_scv_c_misiones.php");
$io_scv= new sigesp_scv_c_misiones($con);

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codmon="%".$_POST["txtcodmon"]."%";
	$ls_denmon="%".$_POST["txtdenmon"]."%";
}
else
{
	$ls_operacion="";

}
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width='80' align='center'>Tasa</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_sql=" SELECT tascam1".
			"  FROM sigesp_dt_moneda".
			" WHERE codmon = '".$ls_codtipmon."'";
    $rs_cta=$io_sql->select($ls_sql);
    $data=$rs_cta;
	if($row=$io_sql->fetch_row($rs_cta))
	{
		$data=$io_sql->obtener_datos($rs_cta);
		$arrcols=array_keys($data);
		$totcol=count((array)$arrcols);
		$ds->data=$data;

		$totrow=$ds->getRowCount("tascam1");
		for($z=1;$z<=$totrow;$z++)
		{
			print "<tr class=celdas-blancas>";
			$ls_tascam1= number_format($data["tascam1"][$z],2,',','.');
			print "<td><a href=\"javascript: aceptar('$ls_tascam1');\">".$ls_tascam1."</a></td>";
			print "</tr>";			
		}
	}
	else
	{
		$io_msg->message("No hay registros");
	}

}
print "</table>";
?>
  </div>
</form>
</body>
<script >
function aceptar(tascam1)
{
	opener.document.form1.txttascam1.value= tascam1;
	close();
}

  function ue_search()
  {
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_scv_cat_tasacambio.php";
	f.submit();
  }

</script>
</html>