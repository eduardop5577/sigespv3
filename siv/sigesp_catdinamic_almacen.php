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
<title>Cat&aacute;logo de Almac&eacute;n </title>
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
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Almac&eacute;n </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="80"><div align="right">C&oacute;digo</div></td>
        <td width="418" height="22"><div align="left">
          <input name="txtcodalm" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Nombre Fiscal </div></td>
        <td height="22"><div align="left">          <input name="txtnomfisalm" type="text" id="txtnomfisalm">
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
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("class_funciones_inventario.php");
	$io_classf =new class_funciones_inventario();
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	$io_msg =new class_mensajes();
	$io_sql =new class_sql($con);
	
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];

	$li_linea=$io_classf->uf_obtenervalor_get("linea","");
	if($li_linea=="")
	{
		$li_linea=$io_classf->uf_obtenervalor("hidlinea","");
	}
	$ls_tipo=$io_classf->uf_obtenervalor_get("tipo","");
	if($ls_tipo=="")
	{
		$ls_tipo=$io_classf->uf_obtenervalor("tipo","");
	}
	$ls_operacion=$io_classf->uf_obteneroperacion();
	$ls_codalm="%".$io_classf->uf_obtenervalor("txtcodalm","")."%";
	$ls_nomfisalm="%".$io_classf->uf_obtenervalor("txtnomfisalm","")."%";
	$ls_status="%".$io_classf->uf_obtenervalor("hidstatus","")."%";
	
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td>Código</td>";
	print "<td>Nombre Fiscal</td>";
	print "<td>Responsable</td>";
	print "</tr>";
	if($ls_operacion=="BUSCAR")
	{
		$ls_sql="SELECT codalm,nomfisalm,desalm,telalm,ubialm,nomresalm,telresalm,codcencos,sc_cuenta,".
				"       (SELECT sigesp_cencosto.denominacion".
				"          FROM sigesp_cencosto".
				"         WHERE sigesp_cencosto.codemp=siv_almacen.codemp".
				"           AND sigesp_cencosto.codcencos=siv_almacen.codcencos) AS dencencos,".
				"       (SELECT scg_cuentas.denominacion".
				"          FROM scg_cuentas".
				"         WHERE scg_cuentas.codemp=siv_almacen.codemp".
				"           AND scg_cuentas.sc_cuenta=siv_almacen.sc_cuenta) AS dencuenta".
				"  FROM siv_almacen".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND codalm LIKE '".$ls_codalm."'".
				"   AND nomfisalm LIKE '".$ls_nomfisalm."'".
				"   AND codalm IN".
				" 		(SELECT codintper FROM sss_permisos_internos".
				"   	  WHERE codemp ='".$ls_codemp."'".
				"     		AND codsis='SIV'".
				" 			AND codusu ='".$ls_codusu."' AND enabled=1) ".
				" ORDER BY codalm";
		$rs_data=$io_sql->select($ls_sql);
		$li_i=0;
		while(!$rs_data->EOF)
		{
			$li_i++;
			$ls_codalm=$rs_data->fields["codalm"];
			$ls_nomfisalm=$rs_data->fields["nomfisalm"];
			$ls_desalm=$rs_data->fields["desalm"];
			$ls_telalm=$rs_data->fields["telalm"];
			$ls_ubialm=$rs_data->fields["ubialm"];
			$ls_nomresalm=$rs_data->fields["nomresalm"];
			$ls_telresalm=$rs_data->fields["telresalm"];
			$ls_codcencos=$rs_data->fields["codcencos"];
			$ls_dencencos=$rs_data->fields["dencencos"];
			$ls_sccuenta=$rs_data->fields["sc_cuenta"];
			$ls_dencuenta=$rs_data->fields["dencuenta"];
			switch($ls_tipo)
			{
				case "":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar('$ls_codalm','$ls_nomfisalm','$ls_desalm','$ls_telalm','$ls_ubialm',";
					print "'$ls_nomresalm','$ls_telresalm','$ls_status','$li_linea','$ls_codcencos','$ls_dencencos','$ls_sccuenta','$ls_dencuenta');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";
				break;
				case "origen":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_origen('$ls_codalm','$ls_nomfisalm','$ls_codcencos','$ls_dencencos');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";
				break;
				case "destino":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_destino('$ls_codalm','$ls_nomfisalm','$ls_desalm','$ls_telalm','$ls_ubialm',";
					print "'$ls_nomresalm','$ls_telresalm','$ls_status','$li_linea');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";
				break;
				case "origenii":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_origenII('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";
				break;
				case "destinoii":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_destinoII('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";
				break;
				case "toma":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_toma('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";			
				break;
				case "listado":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_listado('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";			
				break;
				case "repdesde":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_repdesde('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";			
				break;
				case "rephasta":
					print "<tr class=celdas-blancas>";
					print "<td><a href=\"javascript: aceptar_rephasta('$ls_codalm','$ls_nomfisalm');\">".$ls_codalm."</a></td>";
					print "<td>".$ls_nomfisalm."</td>";
					print "<td>".$ls_nomresalm."</td>";
					print "</tr>";			
				break;
			}
			
			$rs_data->MoveNext();
		}
		if($li_i==0)
		{
			$io_msg->message("No hay registros");
		}
	}
	print "</table>";
?>
</div>
<input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
<input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function aceptar(ls_codalm,ls_nomfisalm,ls_desalm,ls_telalm,ls_ubialm,ls_nomresalm,ls_telresalm,hidstatus,li_linea,ls_codcencos,ls_dencencos,ls_sccuenta,ls_dencuenta)
	{
		obj=eval("opener.document.form1.txtcodalm"+li_linea+"");
		obj.value=ls_codalm;
		opener.document.form1.txtnomfisalm.value=ls_nomfisalm;
		opener.document.form1.txtdesalm.value=ls_desalm;
		opener.document.form1.txttelalm.value=ls_telalm;
		opener.document.form1.txtubialm.value=ls_ubialm;
		opener.document.form1.txtnomresalm.value=ls_nomresalm;
		opener.document.form1.txttelresalm.value=ls_telresalm;
		opener.document.form1.txtcodcencos.value=ls_codcencos;
		opener.document.form1.txtdenominacion.value=ls_dencencos;
		opener.document.form1.txtsccuenta.value=ls_sccuenta;
		opener.document.form1.txtdensccuenta.value=ls_dencuenta;
		opener.document.form1.hidstatus.value="C";
		close();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_almacen.php";
		f.submit();
	}

	function aceptar_origen(ls_codalm,ls_nomfisalm,ls_codcencos,ls_dencencos)
	{
		opener.document.form1.txtcodalm.value=ls_codalm;
		opener.document.form1.txtdesalm.value=ls_nomfisalm;
		opener.document.form1.txtcodcencos.value=ls_codcencos;
		opener.document.form1.txtdenominacion.value=ls_dencencos;
		close();
	}
	
	function aceptar_destino(ls_codalm,ls_nomfisalm)
	{
		opener.document.form1.txtcodalmdes.value=ls_codalm;
		opener.document.form1.txtnomfisdes.value=ls_nomfisalm;
		close();
	}
	
	function aceptar_origenII(ls_codalm,ls_nomfisalm)
	{
		opener.document.formulario.txtcodalm.value=ls_codalm;
		opener.document.formulario.txtnomfisalm.value=ls_nomfisalm;
		close();
	}
	
	function aceptar_destinoII(ls_codalm,ls_nomfisalm)
	{
		opener.document.formulario.txtcodalmdes.value=ls_codalm;
		opener.document.formulario.txtnomfisdes.value=ls_nomfisalm;
		close();
	}
	
	function aceptar_toma(ls_codalm,ls_nomfisalm)
	{
		opener.document.form1.txtcodalm.value=ls_codalm;
		opener.document.form1.txtnomfisalm.value=ls_nomfisalm;
		opener.document.form1.operacion.value="BUSCARARTICULOS";
		opener.document.form1.submit();
		close();
	}
	function aceptar_listado(ls_codalm,ls_nomfisalm)
	{
		opener.document.form1.txtcodalm.value=ls_codalm;
		opener.document.form1.txtdenalm.value=ls_nomfisalm;
		close();
	}
	
	function aceptar_repdesde(ls_codalm,ls_nomfisalm)
	{
		opener.document.form1.txtcoddesde.value=ls_codalm;
		opener.document.form1.txtdendesde.value=ls_nomfisalm;
		close();
	}
	
	function aceptar_rephasta(ls_codalm,ls_nomfisalm)
	{
		opener.document.form1.txtcodhasta.value=ls_codalm;
		opener.document.form1.txtdenhasta.value=ls_nomfisalm;
		close();
	}
	
	

</script>
</html>
