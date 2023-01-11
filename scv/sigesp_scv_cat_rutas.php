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
if($ls_destino=="INTERNACIONAL")
{
	if(array_key_exists("txtcodpaiori",$_POST))
		$ls_codpaiori=$io_fun_viaticos->uf_obtenervalor("txtcodpaiori","");
	else
		$ls_codpaiori=$io_fun_viaticos->uf_obtenervalor_get("codpaiori","");

	if(array_key_exists("txtcodestori",$_POST))
		$ls_codestori=$io_fun_viaticos->uf_obtenervalor("txtcodestori","");
	else
		$ls_codestori=$io_fun_viaticos->uf_obtenervalor_get("codestori","");

	if(array_key_exists("txtcodciuori",$_POST))
		$ls_codciuori=$io_fun_viaticos->uf_obtenervalor("txtcodciuori","");
	else
		$ls_codciuori=$io_fun_viaticos->uf_obtenervalor_get("codciuori","");

	if(array_key_exists("txtcodpaides",$_POST))
		$ls_codpaides=$io_fun_viaticos->uf_obtenervalor("txtcodpaides","");
	else
		$ls_codpaides=$io_fun_viaticos->uf_obtenervalor_get("codpaides","");

	if(array_key_exists("txtcodestdes",$_POST))
		$ls_codestdes=$io_fun_viaticos->uf_obtenervalor("txtcodestdes","");
	else
		$ls_codestdes=$io_fun_viaticos->uf_obtenervalor_get("codestdes","");

	if(array_key_exists("txtcodciudes",$_POST))
		$ls_codciudes=$io_fun_viaticos->uf_obtenervalor("txtcodciudes","");
	else
		$ls_codciudes=$io_fun_viaticos->uf_obtenervalor_get("codciudes","");
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Rutas </title>
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
    <input name="hiddestino" type="hidden" id="hiddestino" value="<?php print $ls_destino ?>">
    <input name="txtcodpaiori" type="hidden" id="txtcodpaiori" value="<?php print $ls_codpaiori; ?>">
    <input name="txtcodestori" type="hidden" id="txtcodestori" value="<?php print $ls_codestori; ?>">
    <input name="txtcodciuori" type="hidden" id="txtcodciuori" value="<?php print $ls_codciuori; ?>">
    <input name="txtcodpaides" type="hidden" id="txtcodpaides" value="<?php print $ls_codpaides; ?>">
    <input name="txtcodestdes" type="hidden" id="txtcodestdes" value="<?php print $ls_codestdes; ?>">
    <input name="txtcodciudes" type="hidden" id="txtcodciudes" value="<?php print $ls_codciudes; ?>">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Rutas </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="76"><div align="right">C&oacute;digo</div></td>
        <td width="422" height="22"><div align="left">
          <input name="txtcodrut" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Descripci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdesrut" type="text" id="txtdesrut">
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
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$ds=new class_datastore();
$io_sql=new class_sql($con);
require_once("class_folder/sigesp_scv_c_rutas.php");
$io_scv= new sigesp_scv_c_rutas($con);
$arr=$_SESSION["la_empresa"];

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codrut="%".$_POST["txtcodrut"]."%";
	$ls_desrut="%".$_POST["txtdesrut"]."%";
}
else
{
	$ls_operacion="";

}
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width='80' align='center'>Código</td>";
print "<td>Denominación</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_parametros="";
	if($ls_destino=="INTERNACIONAL")
	{
		$ls_parametros= " AND codpaiori='".$ls_codpaiori."'".
						" AND codestori='".$ls_codestori."'".
						" AND codciuori='".$ls_codciuori."'".
						" AND codpaides='".$ls_codpaides."'".
						" AND codestdes='".$ls_codestdes."'".
						" AND codciudes='".$ls_codciudes."'";
	}
	
	$ls_sql=" SELECT codrut,desrut,codpaiori,codestori,codciuori,".
			"        (SELECT  despai FROM sigesp_pais".
			" 	       WHERE scv_rutas.codpaiori=sigesp_pais.codpai) AS despaiori,".
			"        (SELECT  desest FROM sigesp_estados".
			"	       WHERE scv_rutas.codpaiori=sigesp_estados.codpai".
			"	         AND   scv_rutas.codestori=sigesp_estados.codest) AS desestori,".
			"        (SELECT  desciu FROM scv_ciudades".
			"	       WHERE scv_rutas.codpaiori=scv_ciudades.codpai".
			"	         AND   scv_rutas.codestori=scv_ciudades.codest".
			"	         AND   scv_rutas.codciuori=scv_ciudades.codciu) AS desciuori".
			" FROM  scv_rutas".
			" WHERE codrut LIKE '".$ls_codrut."'".
			" AND   desrut LIKE '".$ls_desrut."'".
			$ls_parametros.
			" GROUP BY codrut,desrut,codpaiori,codestori,codciuori".
			" ORDER BY codrut ";
    $rs_cta=$io_sql->select($ls_sql);
    $data=$rs_cta;
	if($row=$io_sql->fetch_row($rs_cta))
	{
		$data=$io_sql->obtener_datos($rs_cta);
		$arrcols=array_keys($data);
		$totcol=count((array)$arrcols);
		$ds->data=$data;

		$totrow=$ds->getRowCount("codrut");
	
		for($z=1;$z<=$totrow;$z++)
		{
			switch($ls_destino)
			{
				case"SOLICITUD":
					print "<tr class=celdas-blancas>";
					$ls_codrut=$data["codrut"][$z];
					$ls_desrut=$data["desrut"][$z];
					$ls_codpai=$data["codpaiori"][$z];
					$ls_despai=$data["despaiori"][$z];
					$ls_codest=$data["codestori"][$z];
					$ls_desest=$data["desestori"][$z];
					$ls_codciu=$data["codciuori"][$z];
					$ls_desciu=$data["desciuori"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td align='center'><a href=\"javascript: aceptar('$ls_codrut','$ls_desrut');\">".$ls_codrut."</a></td>";
					print "<td>".$ls_desrut."</td>";
					print "</tr>";			
				break;
	
				case"DEFINICION":
					print "<tr class=celdas-blancas>";
					$ls_codrut=$data["codrut"][$z];
					$ls_desrut=$data["desrut"][$z];
					$ls_codpai=$data["codpaiori"][$z];
					$ls_despai=$data["despaiori"][$z];
					$ls_codest=$data["codestori"][$z];
					$ls_desest=$data["desestori"][$z];
					$ls_codciu=$data["codciuori"][$z];
					$ls_desciu=$data["desciuori"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td align='center'><a href=\"javascript: aceptar_definicion('$ls_codrut','$ls_desrut','$ls_codpai',".
						  "                                                            '$ls_despai','$ls_codest','$ls_desest',".
						  "												     		   '$ls_codciu','$ls_desciu','$ls_codcont',".
						  "															   '$ls_dencont');\">".$ls_codrut."</a></td>";
					print "<td>".$ls_desrut."</td>";
					print "</tr>";			
				break;
				case"INTERNACIONAL":
					print "<tr class=celdas-blancas>";
					$ls_codrut=$data["codrut"][$z];
					$ls_desrut=$data["desrut"][$z];
					$ls_codpai=$data["codpaiori"][$z];
					$ls_despai=$data["despaiori"][$z];
					$ls_codest=$data["codestori"][$z];
					$ls_desest=$data["desestori"][$z];
					$ls_codciu=$data["codciuori"][$z];
					$ls_desciu=$data["desciuori"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td align='center'><a href=\"javascript: aceptar('$ls_codrut','$ls_desrut');\">".$ls_codrut."</a></td>";
					print "<td>".$ls_desrut."</td>";
					print "</tr>";			
				break;
			}
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
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar_definicion(ls_codrut,ls_desrut,ls_codpai,ls_despai,ls_codest,ls_desest,ls_codciu,ls_desciu,
  							  ls_codcont,ls_dencont)
  {

	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtcodpai.value=ls_codpai;
	opener.document.form1.txtdespai.value=ls_despai;
	opener.document.form1.txtcodest.value=ls_codest;
	opener.document.form1.txtdesest.value=ls_desest;
	opener.document.form1.txtcodciu.value=ls_codciu;
	opener.document.form1.txtdesciu.value=ls_desciu;
	opener.document.form1.txtcodcont.value=ls_codcont;
	opener.document.form1.txtdencont.value=ls_dencont;
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.existe.value="TRUE";
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.submit();
	close();
  }

  function aceptar(ls_codrut,ls_desrut)
  {

	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	ls_obssolvia=opener.document.form1.txtobssolvia.value;
	if(ls_obssolvia=="")
	{
		opener.document.form1.txtobssolvia.value= "Ruta: "+ls_desrut;
	}
	else
	{
		opener.document.form1.txtobssolvia.value= ls_obssolvia+", Ruta: "+ls_desrut;
	}
	close();
  }

  function ue_search()
  {
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_scv_cat_rutas.php";
	f.submit();
  }
</script>
</html>
