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
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../shared/class_folder/class_sigesp_int.php");
require_once("../shared/class_folder/class_sigesp_int_scg.php");
require_once("../shared/class_folder/class_sigesp_int_spg.php");
require_once("../shared/class_folder/class_sigesp_int_spi.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
$dat=$_SESSION["la_empresa"];
$int_scg=new class_sigesp_int_scg();
$msg=new class_mensajes();
$fun=new class_funciones();
$ds=new class_datastore();
$io_sql=new class_sql($con);
$arr=$_SESSION["la_empresa"];
$as_codemp=$arr["codemp"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Cuentas Presupuestarias de Ingreso</title>
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
    <?php
		if(array_key_exists("operacion",$_POST))
		{
			$ls_operacion=$_POST["operacion"];
			$ls_codigo=$_POST["codigo"]."%";
			$ls_denominacion="%".$_POST["nombre"]."%";
		}
		else
		{
			$ls_operacion="";
            $ls_codigo="";
			$ls_denominacion="";
		}
		?>
    <input name="operacion" type="hidden" id="operacion">
  </p>
  <br>
  <div align="center">
    <table width="550" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr class="titulo-celda">
        <td height="22" colspan="3" align="right"><div align="center">Cat&aacute;logo de Cuentas Presupuestaria Ingreso</div></td>
      </tr>
      <tr>
        <td height="13" align="right">&nbsp;</td>
        <td height="13" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="135" height="22" align="right">C&oacute;digo</td>
        <td height="22" colspan="2"><div align="left">
          <input name="codigo" type="text" id="codigo" size="22" maxlength="20">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Denominaci&oacute;n</div></td>
        <td height="22" colspan="2"><div align="left">
          <input name="nombre" type="text" id="nombre" size="72">
<label></label>
<br>
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td width="122" height="22">&nbsp;</td>
        <td width="341" height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"> Buscar </a></div></td>
      </tr>
    </table>
	<br>
    <?php
print "<table width=550 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width=100 style=text-align:center>C&oacute;digo</td>";
print "<td width=450 style=text-align:center>Denominaci&oacute;n</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_cadena =" SELECT spi_cuenta,denominacion FROM spi_cuentas ".
		   		" WHERE codemp = '".$as_codemp."' AND spi_cuenta like '".$ls_codigo."' AND  ".
				"       denominacion like '".$ls_denominacion."'".
				" ORDER BY  spi_cuenta  ";
	$rs_data = $io_sql->select($ls_cadena);
	if($rs_data===false)
	{
		$msg->message($fun->uf_convertirmsg($io_sql->message));
	}
	else
	{
		$li_numrows = $rs_data->RecordCount();
		if ($li_numrows>0)
		   {
			 while(!$rs_data->EOF)
			      {
					$ls_spicue = $rs_data->fields["spi_cuenta"];
				    $ls_dencue = $rs_data->fields["denominacion"];
				    print "<tr class=celdas-blancas>";
					print "<td width=100 style=text-align:center><a href=\"javascript: aceptar('$ls_spicue','$ls_dencue');\">".$ls_spicue."</a></td>";
					print "<td width=450 style=text-align:left>".$ls_dencue."</td>";
				    print "</tr>";
					$rs_data->MoveNext();	
			      }
			 $io_sql->free_result($rs_data);
			 $io_sql->close();
		   }
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

  function aceptar(cuenta,deno)
  {
    opener.document.form1.txtcuentahas.value=cuenta;
    opener.document.form1.txtcuentahas.readOnly=true;
	close();
  }

  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_cat_ctasrephas.php";
	  f.submit();
  }
</script>
</html>
