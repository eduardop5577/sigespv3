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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Definici&oacute;n de Centro de Costo</title>
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
  <p align="center">&nbsp;</p>
  <br>
  <div align="center">
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td height="22" colspan="2" class="titulo-celda"><input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo de Definici&oacute;n de Centro de Costo</td>
      </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="81" height="22" style="text-align:right">Codigo</td>
        <td width="417" height="22"><input name="codigo" type="text" id="codigo" style="text-align:center"></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Denominaci&oacute;n</td>
        <td height="22"><div align="left">
          <input name="nombre" type="text" id="nombre" size="70" style="text-align:left">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0">Buscar</a></div></td>
      </tr>
    </table>
	<p><br>
<?php
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../shared/class_folder/class_sigesp_int.php");
require_once("../shared/class_folder/class_sigesp_int_scg.php");
$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$int_scg	= new class_sigesp_int_scg();
$io_msg     = new class_mensajes();
$io_sql		= new class_sql($ls_conect);

if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion    = $_POST["operacion"];
	 $ls_codigo       = $_POST["codigo"]."%";
	 $ls_denominacion = "%".$_POST["nombre"]."%";
   }
else
   {
	 $ls_operacion="";
   }
print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla>";
print "<tr class=titulo-celda>";
print "<td style=text-align:center width=120>Código</td>";
print "<td style=text-align:left   width=380>Denominación</td>";
print "</tr>";
if ($ls_operacion=="BUSCAR")
   {
     $ls_sql = "SELECT codcencos,denominacion 
	              FROM sigesp_cencosto
		         WHERE codcencos like '".$ls_codigo."' 
				   AND denominacion like '".$ls_denominacion."' 
				   AND codcencos<>'00'
		         ORDER BY codcencos ASC";
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
					  echo "<tr class=celdas-blancas>";
			          $ls_codcencos = $rs_data->fields["codcencos"];
			          $ls_denominacion = $rs_data->fields["denominacion"];
				      echo "<td style=text-align:center width=120><a href=\"javascript: aceptar('$ls_codcencos','$ls_denominacion');\">".$ls_codcencos."</a></td>";
				      echo "<td style=text-align:left title='".$ls_denominacion."' width=380>".$ls_denominacion."</td>";
				      echo "</tr>";
                      $rs_data->MoveNext();
					}
			 }
		  else
		     {
			   $io_msg->message("No se han creado codigos para el centro de costo !!!");
			 }
		 }  		 
   }
echo "</table>";
?></p>
	</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

  function aceptar(ls_codcencos,ls_denominacion)
  {
    opener.document.form1.txtcodcencos.value=ls_codcencos;
	opener.document.form1.txtdenominacion.value=ls_denominacion;
    close();
  }

  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_siv_cat_centro_costo.php";
	  f.submit();
  }
</script>
</html>