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
<title>Cat&aacute;logo de Clasificador</title>
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
   <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo Clasificador</td>
  </tr>
</table>
<div align="center"><br>
  <?php
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>C�digo</td>";
		print "<td width=440>Descripci�n</td>";
		print "</tr>";
		
		$ls_sql="SELECT codcla, dencla, sc_cuenta ".
				"  FROM cxp_clasificador_rd ".
				" WHERE codemp = '".$ls_codemp."' ".
				" ORDER BY codcla ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codcla=$rs_data->fields["codcla"];
				$ls_dencla=$rs_data->fields["dencla"];
				$ls_sc_cuenta=$rs_data->fields["sc_cuenta"];
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ls_codcla','$ls_dencla','$ls_sc_cuenta');\">".$ls_codcla."</a></td>";
				print "<td>".$ls_dencla."</td>";
				print "</tr>";			
				$rs_data->MoveNext();
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
?>
</div>
</form>
</body>
<script >
function aceptar(codcla,dencla, sc_cuenta)
{
	opener.document.form1.txtcodcla.value=codcla;
	opener.document.form1.txtdencla.value=dencla;
	opener.document.form1.txtsc_cuentacla.value=sc_cuenta;
	close();
}
</script>
</html>