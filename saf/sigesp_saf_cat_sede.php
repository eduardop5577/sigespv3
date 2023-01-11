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
require_once("class_funciones_activos.php");
$io_fact= new class_funciones_activos();
if (array_key_exists("coddestino",$_POST))
   {
	 $ls_coddestino=$_POST["coddestino"];
   }
else
   {
	 $ls_coddestino=$io_fact->uf_obtenervalor_get("coddestino","definicion");
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo Sede</title>
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
  <p align="center"><br>
  </p>
  <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="22" colspan="2"><input name="coddestino" type="hidden" id="coddestino" value="<?php print $ls_coddestino ?>">
          <input name="hidstatus" type="hidden" id="hidstatus">
          <input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo Sede 
        <input name="txtempresa" type="hidden" id="txtempresa">
        <input name="dendestino" type="hidden" id="dendestino" value="<?php print $ls_dendestino ?>">
        <input name="txtnombrevie" type="hidden" id="txtnombrevie"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="67" height="22" style="text-align:right">C&oacute;digo</td>
        <td width="431" height="22" style="text-align:left"><input name="txtcodsed" type="text" id="txtcodsed"></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Denominaci&oacute;n</td>
        <td height="22" style="text-align:left"><input name="txtdensed" type="text" id="txtdensed" size="70"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
      </tr>
  </table>
    <p align="center">
      <?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=new sigesp_include();
	$con=$in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
	$ds=new class_datastore();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg=new class_mensajes();
	$arr=$_SESSION["la_empresa"];
	
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_codsed="%".$_POST["txtcodsed"]."%";
		$ls_densed="%".$_POST["txtdensed"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="";
	
	}
echo "<table width=530 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
echo "<tr class=titulo-celda>";
echo "<td style=text-align:center width=80>C&oacute;digo</td>";
echo "<td style=text-align:center width=200>Denominaci&oacute;n</td>";
echo "<td style=text-align:center width=260>Direccion</td>";
echo "</tr>";
if ($ls_operacion=="BUSCAR")
   {
     $ls_sql="SELECT trim(codsed) AS codsed, densed,dirsed
	            FROM saf_sede
			   WHERE codsed like '".$ls_codsed."'
			     AND UPPER(densed) like '".strtoupper($ls_densed)."'"; 
   
	 $rs_data = $io_sql->select($ls_sql);
	 if ($rs_data===false)
	    {
		  $io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
		}
     else
	    {
		   $li_totrows = $io_sql->num_rows($rs_data);
		   if ($li_totrows>0)
		      {
			    while(!$rs_data->EOF)
				     {
					  if($ls_coddestino=="definicion")
					  {
						echo "<tr class=celdas-blancas>";
						$ls_codsed = $rs_data->fields["codsed"];
						$ls_densed = $rs_data->fields["densed"];
						$ls_dirsed = $rs_data->fields["dirsed"];
						echo "<td align='center'><a href=\"javascript: aceptar('$ls_codsed','$ls_densed','$ls_dirsed');\">".$ls_codsed."</a></td>";
						echo "<td style=text-align:left   width=300 title='".$ls_densed."'>".$ls_densed."</td>";
						echo "<td style=text-align:center width=100>".$ls_dirsed."</td>";
						echo "</tr>";
					  }
					  else
					  {
						echo "<tr class=celdas-blancas>";
						$ls_codsed = $rs_data->fields["codsed"];
						$ls_densed = $rs_data->fields["densed"];
						$ls_dirsed = $rs_data->fields["dirsed"];
						echo "<td align='center'><a href=\"javascript: aceptar_II('$ls_codsed','$ls_densed');\">".$ls_codsed."</a></td>";
						echo "<td style=text-align:left   width=300 title='".$ls_densed."'>".$ls_densed."</td>";
						echo "<td style=text-align:center width=100>".$ls_dirsed."</td>";
						echo "</tr>";
					  }
					  
                       $rs_data->MoveNext();
					 }
			  }
		   else
		      {
			    $io_msg->message("No se han definido Sedes!!!");
			  }
		 }  		 
   }
echo "</table>";
?>
  </p>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
	function aceptar(ls_codsed,ls_densed,ls_dirsed)
	{
		opener.document.form1.txtcodsed.value=ls_codsed;
		opener.document.form1.txtdensed.value=ls_densed;
		opener.document.form1.txtdirsed.value=ls_dirsed;
		opener.document.form1.hidstatus.value="C";
		close();
	}

	function aceptar_II(ls_codsed,ls_densed)
	{
		opener.document.form1.txtcodsed.value=ls_codsed;
		opener.document.form1.txtdensed.value=ls_densed;
		close();
	}

	function ue_search()
	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_saf_cat_sede.php";
		f.submit();
	}
</script>
</html>