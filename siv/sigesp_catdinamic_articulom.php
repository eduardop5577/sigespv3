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
	if(array_key_exists("coddestino",$_GET))
	{
		$ls_coddestino=$_GET["coddestino"];
		$ls_dendestino=$_GET["dendestino"];
	}
	else
	{
		$ls_coddestino="txtcodart";
		$ls_dendestino="txtdenart";		
	}
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_codart="%".$_POST["txtcodart"]."%";
		$ls_denart="%".$_POST["txtdenart"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
		$ls_coddestino=$_POST["hidcoddestino"];
		$ls_dendestino=$_POST["hiddendestino"];
	}
	else
	{
		$ls_operacion="";
	
	}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Art&iacute;culo</title>
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
    <input name="hidcoddestino" type="hidden" id="hidcoddestino" value="<?php print $ls_coddestino ?>">
    <input name="hiddendestino" type="hidden" id="hiddendestino" value="<?php print $ls_dendestino ?>">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Art&iacute;culo</td>
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
          <input name="txtcodart" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdenart" type="text" id="txtdenart">
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
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	$io_msg =new class_mensajes();
	$ds     =new class_datastore();
	$io_sql =new class_sql($con);
	$io_fun =new class_funciones();
	require_once("class_funciones_inventario.php");
	$io_fun_siv=new class_funciones_inventario();
	$ls_tipo=$io_fun_siv->uf_obtenervalor_get("tipo","");  
	
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];

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
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td width=100>C?digo</td>";
	print "<td>Denominacion</td>";
	print "<td>Clasificaci?n</td>";
	print "</tr>";
	$ls_aux="";
	if($ls_operacion=="BUSCAR")
	{
		$ls_aux="AND estartgen='0'";
		$ls_sql="SELECT siv_articulo.*,".
				"      (SELECT dentipart FROM siv_tipoarticulo".
				"        WHERE siv_tipoarticulo.codtipart = siv_articulo.codtipart) AS dentipart,".
				"      (SELECT tipart FROM siv_tipoarticulo".
				"        WHERE siv_tipoarticulo.codtipart = siv_articulo.codtipart) AS tipart,".
				"      (SELECT denunimed FROM siv_unidadmedida".
				"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS denunimed,".
				"      (SELECT unidad FROM siv_unidadmedida".
				"        WHERE siv_unidadmedida.codunimed = siv_articulo.codunimed) AS unidad".
				"  FROM siv_articulo".
				" WHERE codemp = '".$ls_codemp."'".
				$ls_aux.
				"   AND codart LIKE '".$ls_codart."'".
				"   AND denart LIKE '".$ls_denart."'".
				" ORDER BY codart";
		$rs_data=$io_sql->select($ls_sql);
		while(!$rs_data->EOF)
		{
			print "<tr class=celdas-blancas>";
			$ls_codart= trim($rs_data->fields["codart"]);
			$ls_denart= $rs_data->fields["denart"];
			$ls_denunimed= $rs_data->fields["denunimed"];
			$li_unidad= $rs_data->fields["unidad"];
			$ls_clasi= $rs_data->fields["tipart"];
			$spg_cuenta= $rs_data->fields["spg_cuenta"];
			if ($ls_clasi=="")
			{
				$ls_clasificaci?n="No posee";
			}elseif ($ls_clasi=="1")
			{
				$ls_clasificaci?n="Bienes";
			}elseif ($ls_clasi=="2")
			{
				$ls_clasificaci?n="Material y Suministro";
			}
			switch($ls_tipo)
			{
				  case"":
						print "<td><a href=\"javascript: aceptar('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_coddestino','$ls_dendestino','$ls_clasi','$ls_denunimed');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;

				   case"tipo":
						print "<td><a href=\"javascript: aceptar2('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_coddestino','$ls_dendestino','$ls_clasi');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
			   case"generico":
						print "<td><a href=\"javascript: aceptar2('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_coddestino','$ls_dendestino','$ls_clasi');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
					case"recepcion":
						print "<td><a href=\"javascript: aceptar3('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_coddestino','$ls_dendestino','$ls_clasi');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
				  
				  case"reporte":
						print "<td><a href=\"javascript: aceptar_rep('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_coddestino','$ls_dendestino');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
				  case"produccion":
						print "<td><a href=\"javascript: aceptar_produccion('$ls_codart','$ls_denart','$li_linea',$li_unidad,'$ls_denunimed');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
				  case"materiales":
						print "<td><a href=\"javascript: aceptar_materiales('$ls_codart','$ls_denart');\">".$ls_codart."</a></td>";
						print "<td>".$ls_denart."</td>";
						print "<td>".$ls_clasificaci?n."</td>";
						print "</tr>";	
				  break;
			}  	
			$rs_data->MoveNext();
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
	function aceptar(ls_codart,ls_denart,li_linea,li_unidad,ls_coddestino,ls_dendestino,ls_clasi,ls_denunimed)
	{  
		obj=eval("opener.document.form1."+ls_coddestino+li_linea+"");
		obj.value=ls_codart;
		obj=eval("opener.document.form1.txtdenunimed"+li_linea+"");
		obj.value=ls_denunimed;
		obj1=eval("opener.document.form1."+ls_dendestino+li_linea+"");
		obj1.value=ls_denart;
		obj1=eval("opener.document.form1.hidunidad"+li_linea+"");
		obj1.value=li_unidad;
		obj1=eval("opener.document.form1.hclasi"+li_linea+"");
		obj1.value=ls_clasi;
		close();
	}
	
	function aceptar_produccion(ls_codart,ls_denart,li_linea,li_unidad,ls_denunimed)
	{  
		obj=eval("opener.document.form1.txtcodartent"+li_linea+"");
		obj.value=ls_codart;
		obj=eval("opener.document.form1.txtdenartent"+li_linea+"");
		obj.value=ls_denart;
		obj1=eval("opener.document.form1.txtdenunimedent"+li_linea+"");
		obj1.value=ls_denunimed;
		obj1=eval("opener.document.form1.hidunidadent"+li_linea+"");
		obj1.value=li_unidad;
		totalcosto=opener.document.form1.txttotentsum.value;
		obj1=eval("opener.document.form1.txtcostotent"+li_linea+"");
		obj1.value=totalcosto;
		close();
	}
	
	function aceptar_materiales(ls_codart,ls_denart)
	{  
		opener.document.formulario.txtcodart.value=ls_codart;
		opener.document.formulario.txtdenart.value=ls_denart;
		close();
	}
	
	function aceptar2(ls_codart,ls_denart,li_linea,li_unidad,ls_coddestino,ls_dendestino,ls_clasi)
	{  
		obj=eval("opener.document.form1."+ls_coddestino+li_linea+"");
		obj.value=ls_codart;
		obj1=eval("opener.document.form1."+ls_dendestino+li_linea+"");
		obj1.value=ls_denart;
		obj1=eval("opener.document.form1.hidunidad"+li_linea+"");
		obj1.value=li_unidad;
		close();
	}
	
	function aceptar3(ls_codart,ls_denart,li_linea,li_unidad,ls_coddestino,ls_dendestino,ls_clasi)
	{  
		obj=eval("opener.document.form1."+ls_coddestino+li_linea+"");
		obj.value=ls_codart;
		obj1=eval("opener.document.form1."+ls_dendestino+li_linea+"");
		obj1.value=ls_denart;
		obj1=eval("opener.document.form1.hidunidad"+li_linea+"");
		obj1.value=li_unidad;
		opener.document.form1.operacion.value="VALIDAR";
		opener.document.form1.submit();
		close();
	}
	
	function aceptar_rep(ls_codart,ls_denart,li_linea,li_unidad,ls_coddestino,ls_dendestino)
	{  
		obj=eval("opener.document.form1."+ls_coddestino+li_linea+"");
		obj.value=ls_codart;
		obj1=eval("opener.document.form1."+ls_dendestino+li_linea+"");
		obj1.value=ls_denart;
		obj1=eval("opener.document.form1.hidunidad"+li_linea+"");
		obj1.value=li_unidad;
		close();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_articulom.php?tipo=<?PHP print $ls_tipo;?>";
		f.submit();
	}
</script>
</html>
