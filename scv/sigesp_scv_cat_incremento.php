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
	//-----------------------------------------------------------------------------------------------------------------------------------
   	// Función que obtiene que tipo de operación se va a ejecutar
   	// NUEVO, GUARDAR, ó ELIMINAR
   	function uf_obteneroperacion()
   	{
		if(array_key_exists("operacion",$_POST))
		{
			$operacion=$_POST["operacion"];
		}
		else
		{
			$operacion="NUEVO";
		}
   		return $operacion; 
   	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	// Función que obtiene e imprime los resultados de la busqueda
	function uf_imprimirresultados($as_codinc, $as_deninc,$as_destino)
   	{
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		$io_msg=new class_mensajes();
		$io_sql=new class_sql($con);
		$ds=new class_datastore();
		$fun=new class_funciones();				
       	$emp=$_SESSION["la_empresa"];
        $ls_codemp=$emp["codemp"];

		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>Código</td>";
		print "<td width=100>Denominación</td>";
		print "</tr>";
		$ls_sql="SELECT codinc, codregori, codmis, deninc,".
				"       (SELECT denreg FROM scv_regiones_int".
				"         WHERE scv_incremento.codemp=scv_regiones_int.codemp".
				"           AND scv_incremento.codregori=scv_regiones_int.codreg) AS denreg,".
				"       (SELECT denmis FROM scv_misiones".
				"         WHERE scv_incremento.codemp=scv_misiones.codemp".
				"           AND scv_incremento.codmis=scv_misiones.codmis) AS denmis".
				"  FROM scv_incremento".
				" WHERE scv_incremento.codemp = '".$ls_codemp."'".
				"   AND scv_incremento.codinc LIKE '".$as_codinc."'".
				"   AND scv_incremento.deninc LIKE '".$as_deninc."'";
		$rs_per=$io_sql->select($ls_sql);
		if($row=$io_sql->fetch_row($rs_per))
		{
			$data=$io_sql->obtener_datos($rs_per);
			$ds->data=$data;
			$li_rows=$ds->getRowCount("codinc");
			for($li_index=1;$li_index<=$li_rows;$li_index++)
			{
				print "<tr class=celdas-blancas>";
				$ls_codinc=    $data["codinc"][$li_index];
				$ls_codregori= $data["codregori"][$li_index];
				$ls_codmis=    $data["codmis"][$li_index];
				$ls_deninc=    $data["deninc"][$li_index];				
				$ls_denreg=    $data["denreg"][$li_index];				
				$ls_denmis=    $data["denmis"][$li_index];			
				
				switch($as_destino)
				{
					case"INTERNACIONAL":
						print "<td><a href=\"javascript: aceptar_internacional('$ls_codinc','$ls_deninc');\">".$ls_codinc."</a></td>";
						print "<td>".$ls_deninc."</td>";
						print "</tr>";			
					break;
					default:
						print "<td><a href=\"javascript: aceptar('$ls_codinc','$ls_codregori','$ls_codmis','$ls_deninc',".
							  "               '$ls_denreg','$ls_denmis');\">".$ls_codinc."</a></td>";
						print "<td>".$ls_deninc."</td>";
						print "</tr>";			
					break;
				
				}
			}
		}
		else
		{
			$io_msg->message("No hay nada que reportar");
		}

		print "</table>";
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Incremento</title>
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
    <input name="hiddestino" type="hidden" id="hiddestino" value="<?php print $ls_destino; ?>">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Incremento </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="1" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodinc" type="text" id="txtcodinc" size="30" maxlength="10">        
        </div></td>
      </tr>
      <tr>
        <td><div align="right">C&eacute;dula</div></td>
        <td height="22"><input name="txtdeninc" type="text" id="txtdeninc" size="30" maxlength="10"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left"></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();">
          <img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	$ls_operacion=uf_obteneroperacion();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codinc="%".$_POST["txtcodinc"]."%";
		$ls_deninc="%".$_POST["txtdeninc"]."%";

		uf_imprimirresultados($ls_codinc, $ls_deninc,$ls_destino);
	}
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

function aceptar(ls_codinc,ls_codregori,ls_codmis,ls_deninc,ls_denreg,ls_denmis)
{
	opener.document.form1.txtcodinc.value=ls_codinc;
	opener.document.form1.txtcodreg.value=ls_codregori;
	opener.document.form1.txtdeninc.value=ls_deninc;
	opener.document.form1.txtdenreg.value=ls_denreg;
	opener.document.form1.hidestatus.value="GRABADO";
	opener.document.form1.operacion.value="CARGAR"
	opener.document.form1.submit();
	close();

}

function aceptar_internacional(ls_codinc,ls_deninc)
{
	opener.document.form1.txtcodinc.value=ls_codinc;
	opener.document.form1.txtdeninc.value=ls_deninc;
	close();

}

function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_scv_cat_incremento.php";
  	f.submit();
}
</script>
</html>
