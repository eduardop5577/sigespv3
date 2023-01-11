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
<title>Cat&aacute;logo de Toma de Almac&eacute;n</title>
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
    <input name="hidstatus" type="hidden" id="hidstatus">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Toma de Almac&eacute;n</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="90"><div align="right">N&uacute;mero de Toma </div></td>
        <td width="408" height="22"><div align="left">          <input name="txtnumtom" type="text" id="txtnumtom">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Almac&eacute;n</div></td>
        <td height="22"><div align="left">
          <input name="txtcodalm" type="text" id="txtcodalm">
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
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	$io_msg =new class_mensajes();
	$ds     =new class_datastore();
	$io_sql =new class_sql($con);
	$io_fun =new class_funciones($con);
	
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];

	if (array_key_exists("linea",$_GET))
	{
		$li_linea=$_GET["linea"];
	}
	else
	{
		if(array_key_exists("hidlinea",$_POST))
		{
			$li_linea=$_POST["hidlinea"];
		}
		else
		{
			$li_linea="";
		}
	}
	
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_codalm="%".$_POST["txtcodalm"]."%";
		$ls_numtom="%".$_POST["txtnumtom"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="BUSCAR";
		$ls_codalm="%%";
		$ls_numtom="%%";
		$ls_status="%%";
	}
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td>Numero de Toma</td>";
	print "<td>Nombre Almacen</td>";
	print "<td>Fecha</td>";
	print "</tr>";
	if($ls_operacion=="BUSCAR")
	{
		$ls_sql="SELECT siv_toma.numtom,siv_toma.codalm,siv_toma.fectom,siv_toma.estpro,siv_toma.obstom,".
				"       (SELECT nomfisalm FROM siv_almacen".
				"         WHERE siv_toma.codalm=siv_almacen.codalm) AS nomfisalm".
				"  FROM siv_toma".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND codalm LIKE '".$ls_codalm."'".
				"   AND numtom LIKE '".$ls_numtom."'".
				"   AND codalm IN".
				" 		(SELECT codintper FROM sss_permisos_internos".
				"   	  WHERE sss_permisos_internos.codemp =siv_toma.codemp".
				"     		AND codsis='SIV'".
				" 			AND codusu ='".$ls_codusu."'  AND enabled=1) ";
		$rs_cta=$io_sql->select($ls_sql);
		$data=$rs_cta;
		if(!$rs_cta->EOF)
		{
			while(!$rs_cta->EOF)
			{
				print "<tr class=celdas-blancas>";
				$ls_numtom=    $rs_cta->fields["numtom"];
				$ls_codalm=    $rs_cta->fields["codalm"];
				$ls_nomfisalm= $rs_cta->fields["nomfisalm"];
				$ls_estpro=    $rs_cta->fields["estpro"];
				$ld_fectom=    $rs_cta->fields["fectom"];
				$ls_obstom=    $rs_cta->fields["obstom"];
				$ld_fectom=$io_fun->uf_convertirfecmostrar($ld_fectom);
				print "<td><a href=\"javascript: aceptar('$ls_numtom','$ls_codalm','$ls_nomfisalm','$ld_fectom','$ls_estpro','$ls_obstom');\">".$ls_numtom."</a></td>";
				print "<td>".$ls_nomfisalm."</td>";
				print "<td>".$ld_fectom."</td>";
				print "</tr>";	
				$rs_cta->MoveNext();		
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
<input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function aceptar(ls_numtom,ls_codalm,ls_nomfisalm,ld_fectom,ls_estpro,ls_obstom)
	{
		f=opener.document.form1;
		f.txtnumtom.value=ls_numtom;
		f.txtcodalm.value=ls_codalm;
		f.txtnomfisalm.value=ls_nomfisalm;
		f.hidestpro.value=ls_estpro;
		f.txtobstom.value=ls_obstom;
		f.operacion.value="BUSCARTOMA";
		f.submit();
		close();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_siv_cat_tomaalmacen.php";
		f.submit();
	}
</script>
</html>
