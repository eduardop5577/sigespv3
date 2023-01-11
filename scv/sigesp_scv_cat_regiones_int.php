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
<title>Cat&aacute;logo de Regiones</title>
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
$ls_destino="";
if (array_key_exists("destino",$_GET))
{
	$ls_destino = $_GET["destino"];   
}
$ls_auxiliar="";
if($ls_destino=="")
{
	$ls_auxiliar= " AND      codcont='".$ls_codcont."'";
}
if($ls_destino=="SOLICITUDINC")
{
	$ls_auxiliar= $ls_auxiliar." AND codreg NOT IN (SELECT codregori FROM scv_incremento)";
}
$ls_sql= "SELECT * FROM scv_regiones_int".
		 " WHERE    codreg<>'---'".
		 $ls_auxiliar.
		 " ORDER BY codreg ASC ";
$rs_data= $io_sql->select($ls_sql);
$data= $rs_data;
?>
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Regiones </td>
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
     $totrow= $io_dsclasi->getRowCount("codreg");
     print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	 print "<tr class=titulo-celda>";
	 print "<td>Código</td>";
	 print "<td>Denominación</td>";
 	 print "</tr>";
	 for ($z=1;$z<=$totrow;$z++)
         {
			switch ($ls_destino)
			{
				default:
					print "<tr class=celdas-blancas>";
					$ls_codigo       = $data["codreg"][$z];
					$ls_denominacion = $data["denreg"][$z];
					print "<td><a href=\"javascript: aceptar('$ls_codigo','$ls_denominacion','$ls_catalogo');\">".$ls_codigo."</a></td>";
					print "<td>".$ls_denominacion."</td>";
					print "</tr>";			
				break;
				case"SOLICITUD":
					print "<tr class=celdas-blancas>";
					$ls_codigo       = $data["codreg"][$z];
					$ls_denominacion = $data["denreg"][$z];
					print "<td><a href=\"javascript: aceptar_solicitud('$ls_codigo','$ls_denominacion');\">".$ls_codigo."</a></td>";
					print "<td>".$ls_denominacion."</td>";
					print "</tr>";			
				break;
				case"SOLICITUDINC":
					print "<tr class=celdas-blancas>";
					$ls_codigo       = $data["codreg"][$z];
					$ls_denominacion = $data["denreg"][$z];
					print "<td><a href=\"javascript: aceptar_solicitudinc('$ls_codigo','$ls_denominacion');\">".$ls_codigo."</a></td>";
					print "<td>".$ls_denominacion."</td>";
					print "</tr>";			
				break;
				case"DETALLE":
					print "<tr class=celdas-blancas>";
					$ls_codigo       = $data["codreg"][$z];
					$ls_denominacion = $data["denreg"][$z];
					print "<td><a href=\"javascript: aceptar_detalle('$ls_codigo','$ls_denominacion');\">".$ls_codigo."</a></td>";
					print "<td>".$ls_denominacion."</td>";
					print "</tr>";			
				break;
				
			}
         }
	 print "</table>";
     $io_sql->free_result($rs_data);
   }
else
   { ?>
	  <script >
	  alert("No se han creado Regiones para este Continente");
	  close();
	  </script>
	  <?php
   }		 

?>
  </div>
</form>
</body>
<script >
function aceptar(codigo,denominacion,catalogo)
{
	f= opener.document.form1;
	f.txtcodreg.value= codigo;
	f.txtdenreg.value= denominacion;
	f.existe.value= "TRUE";
	if(catalogo!=1)
	{
		f.hidestatus.value= 'GRABADO';
		f.operacion.value= 'CARGAR';
		f.submit();
	}
	close();
}
function aceptar_solicitud(codigo,denominacion)
{
	f= opener.document.form1;
	f.txtcodreg.value= codigo;
	f.txtdenreg.value= denominacion;
	close();
}
function aceptar_solicitudinc(codigo,denominacion)
{
	f= opener.document.form1;
	f.txtcodreg.value= codigo;
	f.txtdenreg.value= denominacion;
	f.txtdeninc.value= denominacion;
	close();
}
function aceptar_detalle(codigo,denominacion)
{
	fop        = opener.document.form1;
	li_lastrow = (fop.hidlastrow.value); 
	lb_existe  = false;
	for (i=1;i<=li_lastrow;i++)
	{
		ls_codregdes = eval("fop.txtcodregdes"+i+".value");
		ls_denregdes = eval("fop.txtdenregdes"+i+".value");
		if ((ls_codregdes==codigo)&&(ls_denregdes==denominacion))
		{
			lb_existe = true;
			alert("Esta Region ya fue Registrada !!!"); 
			break;
		}
	}
	if (!lb_existe)
	{
		li_lastrow = parseInt(li_lastrow)+1;
		eval("fop.txtcodregdes"+li_lastrow+".value='"+codigo+"'");
		eval("fop.txtdenregdes"+li_lastrow+".value='"+denominacion+"'");
		fop.hidlastrow.value = li_lastrow; 
		fop.hidtotrows.value = parseInt(li_lastrow)+1; 
		fop.operacion.value  = 'PINTAR'
		fop.submit();
		close();
	}
}
</script>
</html>