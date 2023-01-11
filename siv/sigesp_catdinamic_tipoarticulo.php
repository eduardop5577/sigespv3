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
		$ls_obsdestino="txtobstipart";	
	}
	else
	{
		$ls_coddestino="txtcodtipart";
		$ls_dendestino="txtdentipart";
		$ls_obsdestino="txtobstipart";		
	}	
	if(array_key_exists("tipo",$_GET))
	{
		$ls_tipo=$_GET["tipo"];
	}
	else
	{
		$ls_tipo="";
	}
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_coddestino=$_POST["hidcoddestino"];
		$ls_dendestino=$_POST["hiddendestino"];
		$ls_codtipart="%".$_POST["txtcodtipart"]."%";
		$ls_dentipart="%".$_POST["txtdentipart"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="BUSCAR";
		$ls_codtipart="%%";
		$ls_dentipart="%%";
		$ls_status="%%";
	
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Tipos de Art&iacute;culos </title>
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
    <input name="txtempresa" type="hidden" id="txtempresa">
    <input name="hidstatus" type="hidden" id="hidstatus">
    <input name="txtnombrevie" type="hidden" id="txtnombrevie">
    <input name="hidcoddestino" type="hidden" id="hidcoddestino" value="<?php print $ls_coddestino ?>">
    <input name="hiddendestino" type="hidden" id="hiddendestino" value="<?php print $ls_dendestino ?>">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Tipos de Art&iacute;culos </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodtipart" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdentipart" type="text" id="txtdentipart">
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
	$in     =new sigesp_include();
	$con    =$in->uf_conectar();
	$io_msg =new class_mensajes();
	$ds     =new class_datastore();
	$io_sql =new class_sql($con);

	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td>Código</td>";
	print "<td>Denominación</td>";
	print "<td>Clasificación</td>";
	print "</tr>";
	
	if($ls_operacion=="BUSCAR")
	{
		$ls_sql="SELECT *,(SELECT denunimed FROM siv_unidadmedida".
				"           WHERE siv_tipoarticulo.codunimed=siv_unidadmedida.codunimed) AS denunimed,".
				"       (SELECT MAX(denominacion) FROM spg_cuentas WHERE  siv_tipoarticulo.spg_cuenta=spg_cuentas.spg_cuenta ) AS denspg,".
				"       (SELECT denominacion FROM scg_cuentas WHERE  siv_tipoarticulo.sc_cuenta=scg_cuentas.sc_cuenta ) AS denscg".
				"  FROM siv_tipoarticulo".
				" WHERE codtipart like '".$ls_codtipart."' ".
				"   AND dentipart like '".$ls_dentipart."' ".
				" ORDER BY codtipart";
		$rs_cta=$io_sql->select($ls_sql);
		$data=$rs_cta;
		if($row=$io_sql->fetch_row($rs_cta))
		{
			$data=$io_sql->obtener_datos($rs_cta);
			$arrcols=array_keys($data);
			$totcol=count((array)$arrcols);
			$ds->data=$data;
	
			$totrow=$ds->getRowCount("codtipart");
		
			for($z=1;$z<=$totrow;$z++)
			{
				print "<tr class=celdas-blancas>";
				$ls_codtipart=$data["codtipart"][$z];
				$ls_dentipart=$data["dentipart"][$z];
				$ls_obstipart=$data["obstipart"][$z];
				$ls_clasif=$data["tipart"][$z];
				$ls_esttipmer=$data["esttipmer"][$z];
				$ls_codunimed=$data["codunimed"][$z];
				$ls_denunimed=$data["denunimed"][$z];
				$ls_spgcuenta=$data["spg_cuenta"][$z];
				$ls_sccuenta=$data["sc_cuenta"][$z];
				$ls_denspg=$data["denspg"][$z];
				$ls_denscg=$data["denscg"][$z];
				if ($ls_clasif=="")
				{
					$ls_clasificacion="No posee";
				}elseif ($ls_clasif=="1")
				{
					$ls_clasificacion="Bienes";
				}elseif ($ls_clasif=="2")
				{
					$ls_clasificacion="Material y Suministro";
				}
				switch ($ls_tipo)
				{
					case"":
						print "<td><a href=\"javascript: aceptar('$ls_codtipart','$ls_dentipart','$ls_obstipart','$ls_status','$ls_coddestino','$ls_dendestino','$ls_obsdestino','$ls_clasif');\">".$ls_codtipart."</a></td>";
					break;
					case"DEFINICION":
						print "<td><a href=\"javascript: aceptar_definicion('$ls_codtipart','$ls_dentipart','$ls_obstipart','$ls_status','$ls_coddestino','$ls_dendestino','$ls_obsdestino','$ls_clasif','$ls_esttipmer','$ls_codunimed','$ls_denunimed','$ls_spgcuenta','$ls_sccuenta','$ls_denspg','$ls_denscg');\">".$ls_codtipart."</a></td>";
					break;
					case"CONFIGURACION":
						print "<td><a href=\"javascript: aceptar_configuracion('$ls_codtipart');\">".$ls_codtipart."</a></td>";
					break;
					case"EMPAQUETADO":
						print "<td><a href=\"javascript: aceptar_empaquetado('$ls_codtipart','$ls_dentipart');\">".$ls_codtipart."</a></td>";
					break;
					default:
						print "<td><a href=\"javascript: aceptar_articulo('$ls_codtipart','$ls_dentipart','$ls_obstipart','$ls_status','$ls_coddestino','$ls_dendestino','$ls_obsdestino');\">".$ls_codtipart."</a></td>";
					break;
				}

				print "<td>".$data["dentipart"][$z]."</td>";
				print "<td>".$ls_clasificacion."</td>";
				print "</tr>";			
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
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function aceptar(ls_codtipart,ls_dentipart,ls_obstipart,hidstatus,ls_coddestino,ls_dendestino,ls_obsdestino, ls_clasif)
	{
		obj=eval("opener.document.form1."+ls_coddestino+"");
		obj.value=ls_codtipart;
		obj1=eval("opener.document.form1."+ls_dendestino+"");
		obj1.value=ls_dentipart;
		obj1=eval("opener.document.form1."+ls_obsdestino+"");
		obj1.value=ls_obstipart;
		opener.document.form1.hidstatus.value="C";
		opener.document.form1.cmbclasificacion.value=ls_clasif;		
		close();
	}
	
	
	function aceptar_definicion(ls_codtipart,ls_dentipart,ls_obstipart,hidstatus,ls_coddestino,ls_dendestino,ls_obsdestino, ls_clasif, ls_esttipmer, ls_codunimed, ls_denunimed, ls_spgcuenta, ls_sccuenta, ls_denspg,ls_denscg)
	{
		obj=eval("opener.document.form1."+ls_coddestino+"");
		obj.value=ls_codtipart;
		obj1=eval("opener.document.form1."+ls_dendestino+"");
		obj1.value=ls_dentipart;
		obj1=eval("opener.document.form1."+ls_obsdestino+"");
		obj1.value=ls_obstipart;
		opener.document.form1.txtcodunimed.value=ls_codunimed;
		opener.document.form1.txtdenunimed.value=ls_denunimed;
		opener.document.form1.txtspg_cuenta.value=ls_spgcuenta;
		opener.document.form1.txtsccuenta.value=ls_sccuenta;
		//opener.document.form1.txtsccuenta.value=ls_denspg;
		opener.document.form1.txtdensccuenta.value=ls_denscg;
		if(ls_esttipmer==1)
		{
			opener.document.form1.chkesttipmer.checked=true;
		}
		else
		{
			opener.document.form1.chkesttipmer.checked=false;
		}
		opener.document.form1.hidstatus.value="C";
		opener.document.form1.cmbclasificacion.value=ls_clasif;		
		close();
	}
	
	function aceptar_articulo(ls_codtipart,ls_dentipart,ls_obstipart,hidstatus,ls_coddestino,ls_dendestino,ls_obsdestino)
	{
		obj=eval("opener.document.form1."+ls_coddestino+"");
		obj.value=ls_codtipart;
		obj1=eval("opener.document.form1."+ls_dendestino+"");
		obj1.value=ls_dentipart;
		obj1=eval("opener.document.form1."+ls_obsdestino+"");
		obj1.value=ls_obstipart;
		opener.document.form1.hidstatus.value="C";			
		close();
	}
	function aceptar_configuracion(ls_codtipart)
	{
		opener.document.form1.txtcodtipart.value=ls_codtipart;
		close();
	}
	function aceptar_empaquetado(ls_codtipart,ls_dentipart)
	{
		opener.document.formulario.txtcodtipart.value=ls_codtipart;
		opener.document.formulario.txtdentipart.value=ls_dentipart;
		close();
	}
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_tipoarticulo.php";
		f.submit();
	}
</script>
</html>
