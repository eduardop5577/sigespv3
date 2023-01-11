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

   //--------------------------------------------------------------
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
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   // Función que obtiene e imprime los resultados de la busqueda
   function uf_imprimirresultados($as_codcatsud, $as_dencatsud)
   {
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$msg=new class_mensajes();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$SQL=new class_sql($con);
		$ds=new class_datastore();
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>Código</td>";
		print "<td width=440>Descripción</td>";
		print "</tr>";
		$ls_sql="SELECT codcatsud,dencatsud FROM saf_sudebip".
				" WHERE codcatsud like '".$as_codcatsud."'".
				" AND dencatsud like '".$as_dencatsud."'";
		$rs_est=$SQL->select($ls_sql);
                while (!$rs_est->EOF)
                {
                    print "<tr class=celdas-blancas>";
                    $ls_codcatsud=$rs_est->fields["codcatsud"];
                    $ls_dencatsud=$rs_est->fields["dencatsud"];

                    print "<td><a href=\"javascript: aceptar('$ls_codcatsud','$ls_dencatsud');\">".$ls_codcatsud."</a></td>";
                    print "<td>".$ls_dencatsud."</td>";
                    print "</tr>";
                    $rs_est->MoveNext();
		}
		print "</table>";
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de SUDEBIP</title>
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
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de SUDEBIP </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodcatsud" type="text" id="txtcodcatsud" size="30" maxlength="10">        
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdencatsud" type="text" id="txtdencatsud" size="30" maxlength="50">
        </div></td>
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
		$ls_codcatsud="%".$_POST["txtcodcatsud"]."%";
		$ls_dencatsud="%".$_POST["txtdencatsud"]."%";
		
		uf_imprimirresultados($ls_codcatsud, $ls_dencatsud);
	}
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(codigo,descripcion)
{
	opener.document.form1.txtcodcatsud.value=codigo;
    opener.document.form1.txtdencatsud.value=descripcion;
	close();
}
function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_saf_cat_sudebip.php";
  	f.submit();
}
</script>
</html>
