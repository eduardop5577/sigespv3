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
<title>Cat&aacute;logo de Empaquetado</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Empaquetado </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="1" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="80"><div align="right">Numero </div></td>
        <td width="418" height="22"><div align="left">
          <input name="txtnumtra" type="text" id="txtnumtra" maxlength="15">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Fecha</div></td>
        <td height="22"><div align="left"><input name="txtfecemi" type="text" id="txtfecemi" size="20" maxlength="12" datepicker="true">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$in=     new sigesp_include();
	$con=    $in->uf_conectar();
	$ds=     new class_datastore();
	$io_sql= new class_sql($con);
	$io_func=new class_funciones();
	$io_msg= new class_mensajes();
	
	$ls_gestor=   $_SESSION["ls_gestor"];
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];

	if (array_key_exists("tipo",$_GET))
	{
		$ls_tipo=$_GET["tipo"];
	}
	else
	{
		if(array_key_exists("tipo",$_POST))
		{
			$ls_tipo=$_POST["tipo"];
		}
		else
		{
			$ls_tipo="";
		}
	}
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_numtra="%".$_POST["txtnumtra"]."%";
		$ld_fecemi="%".$_POST["txtfecemi"]."%";
		if ($ld_fecemi!="%%")
		{
			$porc="%";
			$ld_fecemi=str_replace($porc,"",$ld_fecemi);
			$ld_fecemi="   AND fecemppro = '".$io_func->uf_convertirdatetobd($ld_fecemi)."'";
		}
		else
		{
			$ld_fecemi="";
		}
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="";
	
	}
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td width=50>N?mero</td>";
	print "<td width=50>Fecha</td>";
	print "<td width=140>Origen</td>";
	print "<td width=140>Destino</td>";
	print "</tr>";
	if($ls_operacion=="BUSCAR")
	{   
		$ls_sql="SELECT siv_empaquetado.*, ".
				"      (SELECT nomfisalm FROM siv_almacen".
				"        WHERE siv_almacen.codalm = siv_empaquetado.codalment) AS nomfisdes,".
				"      (SELECT nomfisalm FROM siv_almacen".
				"        WHERE siv_almacen.codalm = siv_empaquetado.codalmsal) AS nomfisori".
			    "  FROM	siv_empaquetado".
				" WHERE codemp = '".$ls_codemp."'".
				"   AND codemppro like '".$ls_numtra."'".
				" ".$ld_fecemi." ".
				"   AND codalment IN".
				" 		(SELECT codintper FROM sss_permisos_internos".
				"   	  WHERE sss_permisos_internos.codemp =siv_empaquetado.codemp".
				"     		AND codsis='SIV'".
				" 			AND codusu ='".$ls_codusu."'  AND enabled=1) ".
				"   AND codalmsal IN".
				" 		(SELECT codintper FROM sss_permisos_internos".
				"   	  WHERE sss_permisos_internos.codemp =siv_empaquetado.codemp".
				"     		AND codsis='SIV'".
				" 			AND codusu ='".$ls_codusu."'  AND enabled=1) ";
		$rs_cta=$io_sql->select($ls_sql);
		$data=$rs_cta;
		if($row=$io_sql->fetch_row($rs_cta))
		{
			$data=$io_sql->obtener_datos($rs_cta);
			$arrcols=array_keys($data);
			$totcol=count((array)$arrcols);
			$ds->data=$data;
	
			$totrow=$ds->getRowCount("codemppro");
		
			for($z=1;$z<=$totrow;$z++)
			{
				print "<tr class=celdas-blancas>";
				$ls_codemppro=    $data["codemppro"][$z];
				$ld_fecemppro=    $data["fecemppro"][$z];
				$ld_fecemppro=    $io_func->uf_convertirfecmostrar($ld_fecemppro);
				$ls_obspro=    $data["obspro"][$z];
				$ls_codartemp=    $data["codartemp"][$z];
				$ls_denartemp=    $data["denartemp"][$z];
				$ls_codalmori= $data["codalmsal"][$z];
				$ls_codalmdes= $data["codalment"][$z];
				$ls_nomfisori= $data["nomfisori"][$z];
				$ls_nomfisdes= $data["nomfisdes"][$z];
				$li_canartemp= $data["canartemp"][$z];
				$li_canartemp=number_format($li_canartemp,2,',','.');
				$ls_nomfisdes= $data["nomfisdes"][$z];
				$ls_estemppro= $data["estemppro"][$z];
				print "<td><a href=\"javascript: aceptar('$ls_codemppro','$ld_fecemppro','$ls_obspro','$ls_codartemp','$ls_denartemp','$ls_codalmori',";
				print "'$ls_codalmdes','$ls_nomfisori','$ls_nomfisdes','$li_canartemp','$ls_estemppro');\">".$ls_codemppro."</a></td>";
				print "<td>".$ld_fecemppro."</td>";
				print "<td>".$data["nomfisori"][$z]."</td>";
				print "<td>".$data["nomfisdes"][$z]."</td>";
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
<input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
<input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function aceptar(codemppro,fecemppro,obspro,codartemp,denartemp,codalmori,codalmdes,nomfisori,nomfisdes,canartemp,estemppro)
	{ 
		opener.document.formulario.txtcodemppro.value=codemppro;
		opener.document.formulario.txtfecemppro.value=fecemppro;
		opener.document.formulario.txtobspro.value=obspro;
		
		opener.document.formulario.txtcodartemp.value=codartemp;
		opener.document.formulario.txtdenartemp.value=denartemp;

		opener.document.formulario.txtcodalm.value=codalmori;
		opener.document.formulario.txtnomfisalm.value=nomfisori;
		opener.document.formulario.txtcodalmdes.value=codalmdes;
		opener.document.formulario.txtnomfisdes.value=nomfisdes;
		
		opener.document.formulario.txtcanartemp.value=canartemp;
		opener.document.formulario.estapr.value=estemppro;

		opener.document.formulario.existe.value="TRUE";
		parametros="";
		parametros=parametros+"&codemppro="+codemppro+"&denartemp="+denartemp;
		proceso="LOADARTICULOS";
		if(parametros!="")
		{
			// Div donde se van a cargar los resultados
			divgrid = opener.document.getElementById("articulos");
			// Instancia del Objeto AJAX
			ajax=objetoAjax();
			// Pagina donde est?n los m?todos para buscar y pintar los resultados
			ajax.open("POST","class_folder/sigesp_siv_c_empaquetado_ajax.php",true);
			ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
			}
			else
			{
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
						close();
					}
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La p?gina no existe";
						}
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					}
					
				}
			}
		}	
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("proceso="+proceso+""+parametros);
		}
	//	close();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_siv_cat_empaquetado.php";
		f.submit();
	}
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="js/funcion_siv.js"></script>
</html>
