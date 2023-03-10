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
<title>Cat&aacute;logo de Entradas de Suministros a Almac&eacute;n </title>
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
<link href="js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Entradas de Suministros a Almac&eacute;n </td>
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
          <input name="txtnumordcom" type="text" id="txtnumordcom" size="15" maxlength="15">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Almac&eacute;n</div></td>
        <td height="22"><div align="left">
          <input name="txtcodalm" type="text" id="txtcodalm" size="11" maxlength="10">
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
	$in=     new sigesp_include();
	$con=    $in->uf_conectar();
	$ds=     new class_datastore();
	$io_sql= new class_sql($con);
	$io_func=new class_funciones();
	$io_msg= new class_mensajes();
	$io_fun= new class_funciones();
	
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
	
	if (array_key_exists("tipo",$_GET))
	{
		$tipo=$_GET["tipo"];
	}
	else
	{
		$tipo=$_POST["tipo"];
	}
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_numordcom="%".$_POST["txtnumordcom"]."%";
		$ls_codalm="%".$_POST["txtcodalm"]."%";
		$ls_status="%".$_POST["hidstatus"]."%";
	}
	else
	{
		$ls_operacion="";
	
	}
	print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
	print "<td width=100>Codigo</td>";
	print "<td width=100>Compra/Factura</td>";
	print "<td width=100>Operacion</td>";
	print "<td width=50 align='center'>Proveedor</td>";
	print "<td width=140>Almac?n</td>";
	print "<td width=50>Copiar</td>";
	print "</tr>";
	if($ls_operacion=="BUSCAR")
	{
		$ls_sql="SELECT siv_recepcion.*, ".
				"      (SELECT nomfisalm FROM siv_almacen ".
				"        WHERE siv_almacen.codalm = siv_recepcion.codalm) AS nomfisalm, ".
				"      (SELECT nompro FROM rpc_proveedor ".
				"        WHERE rpc_proveedor.cod_pro = siv_recepcion.cod_pro) AS nompro ".
			    "  FROM siv_recepcion ".
				" WHERE codemp = '".$ls_codemp."' ".
				"   AND numordcom like '".$ls_numordcom."' ".
				"   AND codalm like '".$ls_codalm."' ".
				"   AND estrevrec='1'".
				"   AND codalm IN".
				" 		(SELECT codintper FROM sss_permisos_internos".
				"   	  WHERE codemp ='".$ls_codemp."'".
				"     		AND codsis='SIV'".
				" 			AND codusu ='".$ls_codusu."'  AND enabled=1) ".
				" ORDER BY numconrec";
		$rs_cta=$io_sql->select($ls_sql);
		$li_row=$io_sql->num_rows($rs_cta);
		if($li_row>0)
		{
			while($row=$io_sql->fetch_row($rs_cta))
			{
				print "<tr class=celdas-blancas>";
				$ls_numconrec  = $row["numconrec"];
				$ls_numordcom  = $row["numordcom"];
				$ls_obsrec     = $row["obsrec"];
				$ls_codpro     = $row["cod_pro"];
				$ls_nompro     = trim($row["nompro"]);
				$ls_codalm     = $row["codalm"];
				$ls_nomfisalm  = $row["nomfisalm"];
				$ls_estpro     = $row["estpro"];
				$ls_estrec     = $row["estrec"];
				$ls_estapr     = $row["estapr"];
				$ls_estrevrec  = $row["estrevrec"];
				if($ls_estrevrec=="1")
				{
				   $ls_opeinv="Entrada de Suministro Almacen";
				}
				if($ls_estrevrec=="0")
				{
				   $ls_opeinv="Reverso de Suministro Almacen";
				}
				
				$ld_fecrec     = $row["fecrec"];
				$ld_fecrec     = $io_fun->uf_convertirfecmostrar($ld_fecrec);
				if($ls_estrevrec=="1")
				{
					print "<td><a href=\"javascript: aceptar('$ls_numconrec','$ls_numordcom','$ls_obsrec','$ls_codpro','$ls_nompro','$ls_codalm','$ls_nomfisalm','$ls_estpro','$ls_estrec','$ls_estrevrec','$ld_fecrec','$ls_estapr','$tipo');\">".$ls_numconrec."</a></td>";
				}
				else
				{
					print "<td>".$ls_numconrec."</td>";
				}
				print "<td>".$ls_numordcom."</td>";
				print "<td>".$ls_opeinv."</td>";
				print "<td>".$ls_nompro."</td>";
				print "<td>".$ls_nomfisalm."</td>";
				if($ls_estrevrec=="0")
				{
					print "<td><a href=\"javascript: aceptar_copiar_reverso('$ls_numconrec','$ls_numordcom','$ls_obsrec','$ls_codpro','$ls_nompro','$ls_codalm','$ls_nomfisalm','$ls_estpro','$ls_estrec','$ls_estrevrec','$ld_fecrec','$ls_estapr');\"><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar title='Copiar el Reverso' width='15' height='15' border=0></a></td>";
				}
				else
				{
					print "<td><img src=../shared/imagebank/tools15/aprobado-off.gif alt='Aceptar' width='15' height='15' border=0></a></td>";
				}
				print "</tr>";			
			}
		}
		else
		{
			$io_msg->message("No hay registros de entrada de suministro");
		}
	}
	print "</table>";
?>
</div>
<input name="hidlinea" type="hidden" id="hidlinea" value="<?php print $li_linea?>">
<input name="tipo" type="hidden" id="tipo" value="<?php print $tipo; ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
	function aceptar(ls_numconrec,ls_numordcom,ls_obsrec,ls_codpro,ls_nompro,ls_codalm,ls_nomfisalm,ls_estpro,ls_estrec,
					 ls_estrevrec,ld_fecrec,ls_estapr,tipo)
	{
		opener.document.form1.txtnumconrec.value=ls_numconrec;
		opener.document.form1.txtnumconrecmov.value=ls_numconrec;
		opener.document.form1.txtnumordcom.value=ls_numordcom;
		opener.document.form1.txtcodpro.value=ls_codpro;
		opener.document.form1.txtdenpro.value=ls_nompro;
		opener.document.form1.txtcodalm.value=ls_codalm;
		opener.document.form1.txtnomfisalm.value=ls_nomfisalm;
		opener.document.form1.txtobsrec.value=ls_obsrec;
		opener.document.form1.txtfecrec.value=ld_fecrec;
		opener.document.form1.hidestatus.value="C";
		opener.document.form1.estapr.value=ls_estapr;
		if(ls_estpro==1)
		{
			opener.document.form1.radiotipo[1].checked= true;
		}
		else
		{	
			if(ls_estpro==0)
			{
				opener.document.form1.radiotipo[0].checked= true;
			}
			else
			{
				opener.document.form1.radiotipo[2].checked= true;
			}
		}

		if(ls_estrec==0)
		{
			opener.document.form1.radiotipentrega[1].checked= true;
		}
		else
		{
			opener.document.form1.radiotipentrega[0].checked= true;
		}
		opener.document.form1.operacion.value="BUSCARDETALLE";
		opener.document.form1.hidreadonly.value="false";
		if(tipo!="")
			opener.document.form1.action="sigesp_siv_p_recepcion_lote.php";
		else
			opener.document.form1.action="sigesp_siv_p_recepcion.php";
		opener.document.form1.submit();
		close();
	}
	
	function aceptar_copiar_reverso(ls_numconrec,ls_numordcom,ls_obsrec,ls_codpro,ls_nompro,ls_codalm,ls_nomfisalm,ls_estpro,
									ls_estrec,ls_estrevrec,ld_fecrec,ls_estapr)
	{ 
		opener.document.form1.txtnumconrec.value=ls_numconrec;
		opener.document.form1.txtnumordcom.value=ls_numordcom;
		opener.document.form1.txtcodpro.value=ls_codpro;
		opener.document.form1.txtdenpro.value=ls_nompro;
		opener.document.form1.txtcodalm.value=ls_codalm;
		opener.document.form1.txtnomfisalm.value=ls_nomfisalm;
		opener.document.form1.txtobsrec.value=ls_obsrec;
		opener.document.form1.txtfecrec.value=ld_fecrec;
		opener.document.form1.hidestatus.value="C";
		opener.document.form1.estapr.value=ls_estapr;
		if(ls_estpro==1)
		{
			opener.document.form1.radiotipo[1].checked= true;
		}
		else
		{
			opener.document.form1.radiotipo[0].checked= true;
		}

		if(ls_estrec==0)
		{
			opener.document.form1.radiotipentrega[1].checked= true;
		}
		else
		{
			opener.document.form1.radiotipentrega[0].checked= true;
		}
		opener.document.form1.operacion.value="COPIARREVERSO";
		opener.document.form1.hidreadonly.value="false";
		opener.document.form1.hidsaverev.value="true";
		opener.document.form1.action="sigesp_siv_p_recepcion.php";
		opener.document.form1.submit();
		close();
	}
	
	function ue_search()
  	{
		f=document.form1;
		f.operacion.value="BUSCAR";
		f.action="sigesp_catdinamic_recepcion.php";
		f.submit();
	}
</script>
<script  src="js/js_intra/datepickercontrol.js"></script>
</html>