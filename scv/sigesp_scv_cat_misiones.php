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
<title>Cat&aacute;logo de Misiones</title>
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
    <input name="hiddestino" type="hidden" id="hiddestino" value="<?php print $ls_destino ?>">
<table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Misiones</td>
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
	$ls_codmis="%".$_POST["txtcodmis"]."%";
	$ls_denmis="%".$_POST["txtdesmis"]."%";
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
	$ls_sql=" SELECT codmis,denmis,codpai,codest,codciu,estdesviaper,".
			"        (SELECT  despai FROM sigesp_pais".
			" 	       WHERE scv_misiones.codpai=sigesp_pais.codpai) AS despai,".
			"        (SELECT  desest FROM sigesp_estados".
			"	       WHERE scv_misiones.codpai=sigesp_estados.codpai".
			"	         AND   scv_misiones.codest=sigesp_estados.codest) AS desest,".
			"        (SELECT  desciu FROM scv_ciudades".
			"	       WHERE scv_misiones.codpai=scv_ciudades.codpai".
			"	         AND   scv_misiones.codest=scv_ciudades.codest".
			"	         AND   scv_misiones.codciu=scv_ciudades.codciu) AS desciu".
			"  FROM scv_misiones".
			" WHERE codmis LIKE '".$ls_codmis."'".
			"   AND denmis LIKE '".$ls_denmis."'".
			"   AND codmis <>'-----'".
			" ORDER BY codmis ";
    $rs_cta=$io_sql->select($ls_sql);
    $data=$rs_cta;
	if($row=$io_sql->fetch_row($rs_cta))
	{
		$data=$io_sql->obtener_datos($rs_cta);
		$arrcols=array_keys($data);
		$totcol=count((array)$arrcols);
		$ds->data=$data;

		$totrow=$ds->getRowCount("codmis");
		for($z=1;$z<=$totrow;$z++)
		{
			switch($ls_destino)
			{
				case"SOLICITUD":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					print "<td><a href=\"javascript: aceptar('$ls_codmis','$ls_denmis');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
	
				case"DEFINICION":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					$ls_codpai=$data["codpai"][$z];
					$ls_despai=$data["despai"][$z];
					$ls_codest=$data["codest"][$z];
					$ls_desest=$data["desest"][$z];
					$ls_codciu=$data["codciu"][$z];
					$ls_desciu=$data["desciu"][$z];
					$ls_estdesviaper= $data["estdesviaper"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td><a href=\"javascript: aceptar_definicion('$ls_codmis','$ls_denmis','$ls_codpai',".
						  "                                                            '$ls_despai','$ls_codest','$ls_desest',".
						  "												     		   '$ls_codciu','$ls_desciu','$ls_codcont',".
						  "															   '$ls_dencont','$ls_estdesviaper');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
				case"INCREMENTO":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					print "<td><a href=\"javascript: aceptar_incremento('$ls_codmis','$ls_denmis');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
				case"INTERNACIONAL":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					$ls_codpai=$data["codpai"][$z];
					$ls_despai=$data["despai"][$z];
					$ls_codest=$data["codest"][$z];
					$ls_desest=$data["desest"][$z];
					$ls_codciu=$data["codciu"][$z];
					$ls_desciu=$data["desciu"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td><a href=\"javascript: aceptar_internacional('$ls_codmis','$ls_denmis','$ls_codpai','$ls_codest',".
						  "												     		   '$ls_codciu','$ls_codcont');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
				case"INTERNACIONALDES":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					$ls_codpai=$data["codpai"][$z];
					$ls_despai=$data["despai"][$z];
					$ls_codest=$data["codest"][$z];
					$ls_desest=$data["desest"][$z];
					$ls_codciu=$data["codciu"][$z];
					$ls_desciu=$data["desciu"][$z];
					$arrResultado=$io_scv->uf_scv_select_continente($ls_codpai,$ls_codcont,$ls_dencont);
					$ls_codcont=$arrResultado['as_codcont'];
					$ls_dencont=$arrResultado['as_dencont'];
					$lb_valido=$arrResultado['lb_valido'];
					print "<td><a href=\"javascript: aceptar_internacionaldes('$ls_codmis','$ls_denmis','$ls_codpai','$ls_codest',".
						  "												     		   '$ls_codciu','$ls_codcont');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
				case"DETALLE":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					print "<td><a href=\"javascript: aceptar_detalle('$ls_codmis','$ls_denmis');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "</tr>";			
				break;
				case"DESTINO":
					print "<tr class=celdas-blancas>";
					$ls_codmis= $data["codmis"][$z];
					$ls_denmis= $data["denmis"][$z];
					print "<td><a href=\"javascript: aceptar_destino('$ls_codmis','$ls_denmis');\">".$ls_codmis."</a></td>";
					print "<td>".$ls_denmis."</td>";
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
</body>
<script >
function aceptar(ls_codmis,ls_denmis)
{
	opener.document.form1.txtcodmis.value= ls_codmis;
	opener.document.form1.txtdenmis.value= ls_denmis;
	ls_obssolvia=opener.document.form1.txtobssolvia.value;
	if(ls_obssolvia=="")
	{
		opener.document.form1.txtobssolvia.value= ls_denmis;
	}
	else
	{
		opener.document.form1.txtobssolvia.value= ls_obssolvia+", "+ls_denmis;
	}
	close();
}

function aceptar_internacional(ls_codmis,ls_denmis,ls_codpai,ls_codest,ls_codciu,ls_codcont)
{
	opener.document.form1.txtcodmis.value= ls_codmis;
	opener.document.form1.txtdenmis.value= ls_denmis;
	opener.document.form1.txtcodpaiori.value=ls_codpai;
	opener.document.form1.txtcodestori.value=ls_codest;
	opener.document.form1.txtcodciuori.value=ls_codciu;
	ls_obssolvia=opener.document.form1.txtobssolvia.value;
	if(ls_obssolvia=="")
	{
		opener.document.form1.txtobssolvia.value= "Mision Origen: "+ls_denmis;
	}
	else
	{
		opener.document.form1.txtobssolvia.value= "Mision Origen: "+ls_obssolvia+", "+ls_denmis;
	}
	close();
}
function aceptar_internacionaldes(ls_codmis,ls_denmis,ls_codpai,ls_codest,ls_codciu,ls_codcont)
{
	opener.document.form1.txtcodmisdes.value= ls_codmis;
	opener.document.form1.txtdenmisdes.value= ls_denmis;
	opener.document.form1.txtcodpaides.value=ls_codpai;
	opener.document.form1.txtcodestdes.value=ls_codest;
	opener.document.form1.txtcodciudes.value=ls_codciu;
	ls_obssolvia=opener.document.form1.txtobssolvia.value;
	if(ls_obssolvia=="")
	{
		opener.document.form1.txtobssolvia.value= "Mision Destino: "+ls_denmis;
	}
	else
	{
		opener.document.form1.txtobssolvia.value= ls_obssolvia+",  Mision Destino: 	"+ls_denmis;
	}
	close();
}
function aceptar_definicion(ls_codmis,ls_denmis,ls_codpai,ls_despai,ls_codest,ls_desest,ls_codciu,ls_desciu,
  							  ls_codcont,ls_dencont,ls_estdesviaper)
{
	opener.document.form1.txtcodmis.value= ls_codmis;
	opener.document.form1.txtdenmis.value= ls_denmis;
	opener.document.form1.existe.value= "TRUE";
	opener.document.form1.hidestatus.value= "C";
	opener.document.form1.txtcodpai.value=ls_codpai;
	opener.document.form1.txtdespai.value=ls_despai;
	opener.document.form1.txtcodest.value=ls_codest;
	opener.document.form1.txtdesest.value=ls_desest;
	opener.document.form1.txtcodciu.value=ls_codciu;
	opener.document.form1.txtdesciu.value=ls_desciu;
	opener.document.form1.txtcodcont.value=ls_codcont;
	opener.document.form1.txtdencont.value=ls_dencont;
	if(ls_estdesviaper=="1"){
		opener.document.form1.chkestdesviaper.checked="1";
	}
	else if(ls_estdesviaper=="0"){
		opener.document.form1.chkestdesviaper.checked=false;
	}
	close();
}
function aceptar_incremento(ls_codmis,ls_denmis)
{
	opener.document.form1.txtcodmis.value= ls_codmis;
	opener.document.form1.txtdenmis.value= ls_denmis;
	close();
}

function aceptar_destino(ls_codmis,ls_denmis)
{
	opener.document.form1.txtcodmisdes.value= ls_codmis;
	opener.document.form1.txtdenmisdes.value= ls_denmis;
	close();
}

function aceptar_detalle(codmis,denmis)
{
	fop        = opener.document.form1;
	li_lastrow = (fop.totalfilas.value); 
	dias = (fop.txtnumdia.value); 
	lb_existe  = false;
	for (i=1;i<=li_lastrow;i++)
	{
		ls_codmis = eval("fop.txtcodmisdes"+i+".value");
		if (ls_codmis==codmis)
		{
			lb_existe = true;
			alert("La Mision ya existe en el detalle"); 
			break;
		}
	}
	if (!lb_existe)
	{
		li_lastrowprint = parseInt(li_lastrow)-1;
		eval("fop.txtcodmisdes"+li_lastrow+".value='"+codmis+"'");
		eval("fop.txtdenmisdes"+li_lastrow+".value='"+denmis+"'");
		if(li_lastrow==1)
			eval("fop.txtcantidad"+li_lastrow+".value='"+dias+"'");
					
//		fop.totalfilas.value = li_lastrow; 
		fop.operacion.value  = 'AGREGARMISIONES'
		fop.submit();
		close();
	}
}

  function ue_search()
  {
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_scv_cat_misiones.php";
	f.submit();
  }

</script>
</html>