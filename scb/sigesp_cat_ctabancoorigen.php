<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "close();";
	print "opener.document.form1.submit();";
	print "</script>";		
}
$arr=$_SESSION["la_empresa"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Cuentas Bancarias</title>
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
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script language="JavaScript">

function uf_blonumche()
{
	opener.document.getElementById("txtdocumento").readOnly = true;
  	alert('El numero de documento no podra ser editado, ya que se configuraron chequeras');
}

function uf_gennumche(as_numche,as_chequera)
{
	fop = opener.document.form1;
  	if (as_numche!='') {
		fop.txtdocorigen.value = as_numche;
		fop.txtchequera.value  = as_chequera;
		opener.document.getElementById("txtdocorigen").readOnly = true
	}
  	else {
  	  	fop.txtdocumento.value = "";
	}
  	close();
}
</script>
</head>

<body>
<form name="form1" method="post" action="">
<?php

require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$msg=new class_mensajes();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$SQL=new class_sql($con);
$ds=new class_datastore();
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$fun=new class_funciones();
$ls_codemp=$arr["codemp"];
require_once("sigesp_c_cuentas_banco.php");
$io_ctaban = new sigesp_c_cuentas_banco();
require_once("class_funciones_banco.php");
$io_update 		 = new class_funciones_banco();

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codigo=$_POST["codigo"];
	$ls_denban=$_POST["denban"];
	$ls_ctaban="%".$_POST["cuenta"]."%";
	$ls_denominacion="%".$_POST["denominacion"]."%";
	$ls_conf_ch=$_POST["conf_ch"];
}
else
{
	$ls_operacion="";
	$ls_codigo=$_GET["codigo"];
	$ls_denban=$_GET["denban"];
	$ls_conf_ch=$_GET["conf_ch"];
}
?>
<br>
	 <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="22" colspan="2"><input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo de Cuentas Bancarias</td>
       </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="67" height="22" style="text-align:right">Cuenta</td>
        <td width="431" height="22"><div align="left">
          <input name="cuenta" type="text" id="cuenta">        
        </div></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Nombre</td>
        <td height="22" style="text-align:left"><input name="denominacion" type="text" id="denominacion" size="60"></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Banco</td>
        <td height="22"><input name="denban" type="text" id="denban" value="<?php print $ls_denban;?>">
        <input name="codigo" type="hidden" id="codigo" value="<?php print $ls_codigo;?>"></td>
        <input name="conf_ch" type="hidden" id="conf_ch" value="<?php print $ls_conf_ch;?>"></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
    </table>
	<br>
<?php
print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Código </td>";
print "<td>Denominación</td>";
print "<td>Tipo</td>";
print "<td>Contable</td>";
print "<td>Descripción</td>";
print "<td>Apertura</td>";
print "</tr>";
if($ls_operacion=="BUSCAR")
{
	$ls_gestor = $_SESSION["ls_gestor"];
	 if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
	 {
		  $ls_sql_seguridad =" AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
		  				     " AND sss_permisos_internos.enabled='1'". 
		  				     " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
		  					 " AND trim(CONCAT(scb_ctabanco.codban,'-',scb_ctabanco.ctaban))= trim(sss_permisos_internos.codintper)";
	 }
	 else
	 {
		  $ls_sql_seguridad =" AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'". 
		  				     " AND sss_permisos_internos.enabled='1'". 
		  				     " AND scb_ctabanco.codemp=sss_permisos_internos.codemp". 
 		                     " AND  trim(sss_permisos_internos.codintper)=trim(scb_ctabanco.codban||'-'||scb_ctabanco.ctaban) ";
	 }
	$ls_sql=" SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,scb_ctabanco.sc_cuenta as sc_cuenta, ".
			" scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
			" scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta, ".
			" scb_ctabanco.fecapr as fecapr,scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact ".
			" FROM scb_ctabanco ,scb_tipocuenta ,scb_banco ,scg_cuentas, sss_permisos_internos  ".
			" WHERE scb_ctabanco.codemp='".$ls_codemp."' ".
			" AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
			" AND scb_ctabanco.codban=scb_banco.codban ".
			" AND scb_ctabanco.codban like '%".$ls_codigo."%'  ".
			" AND scb_ctabanco.ctaban like '".$ls_ctaban."' ".
			" AND (scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta AND scb_ctabanco.codemp=scg_cuentas.codemp) ".
			" ".$ls_sql_seguridad." ";
			$rs_cta=$SQL->select($ls_sql);
			if($rs_cta==false)
			{
				$msg->message("No se han creado cuentas");
			}
			else
			{
				if($row=$SQL->fetch_row($rs_cta))
				{
					$data=$SQL->obtener_datos($rs_cta);
					$arrcols=array_keys($data);
					$totcol=count((array)$arrcols);
					$ds->data=$data;
					$totrow=$ds->getRowCount("ctaban");
						
					for($z=1;$z<=$totrow;$z++)
					{
						$codban=$data["codban"][$z];
						$nomban=$data["nomban"][$z];
						$ctaban=$data["ctaban"][$z];
						$dencta=$data["dencta"][$z];
						$codtipcta=$data["codtipcta"][$z];
						$nomtipcta=$data["nomtipcta"][$z];
						$ctascg=$data["sc_cuenta"][$z];
						$denctascg=$data["denominacion"][$z];
						$fecapertura=$fun->uf_convertirfecmostrar($data["fecapr"][$z]);
						$feccierre=$fun->uf_convertirfecmostrar($data["feccie"][$z]);
						$adec_saldo="";
						$arrResultado="";
						$arrResultado=$io_ctaban->uf_verificar_saldo($codban,$ctaban,$adec_saldo);
						$adec_saldo=$arrResultado['ldec_saldo'];
				 	    if ($adec_saldo>0)
					       {
						     echo "<tr class=celdas-azules>";						   
						   }
					    else
					       {
						     echo "<tr class=celdas-blancas>"; 
						   }
						$ldec_saldo = number_format($adec_saldo,2,',','.');
						$status=$data["estact"][$z];
						print "<td><a href=\"javascript: aceptar('$codban','$nomban','$ctaban','$dencta','$ctascg','$denctascg','$fecapertura','$feccierre','$status','$codtipcta','$nomtipcta','$ldec_saldo','$ls_conf_ch');\">".$ctaban."</a></td>";
						print "<td>".$dencta."</td>";
						print "<td>".$nomtipcta."</td>";
						print "<td>".$ctascg."</td>";
						print "<td>".$denctascg."</td>";																			
						print "<td>".$fecapertura."</td>";					
						print "</tr>";			
					}
				}
				else
				{
					$msg->message( "No se han creado Cuentas de Banco o no posee permisologia en las cuentas");
				}
		}
}
print "</table>";

if ($ls_operacion=="TOMAR") {
	$ls_ctaban = $_POST["cuenta"];
	$ls_codban = $_POST["codigo"];
	$ls_codusu = $_SESSION['la_logusr'];

	if ($_SESSION["la_empresa"]["blonumche"]=='1'){
		if($io_update->uf_select_chequera($ls_codban, $ls_ctaban)){
			?>
		  	 <script >uf_blonumche();</script>
		  	<?php
		}
	}

	$li_cfgchq = $_SESSION["la_empresa"]["confi_ch"];
	if ($li_cfgchq=='1')//Generación Automática de los Cheques.
	{
		$ls_chenum="";
		$ls_numche="";
		$arr_resultado = $io_update->uf_select_cheques($ls_codban,$ls_ctaban,$ls_codusu,$ls_chenum);
		$ls_chenum=$arr_resultado["as_numchequera"];
		$ls_numche=$arr_resultado["ls_valor"];
		if (!empty($ls_numche)){
			$ls_numche = str_pad($ls_numche,15,0,0);
		}		
		else {
			$io_msg->message("No tiene Chequera asociada !!!");
		}
		?>
		  <script >uf_gennumche('<?php print $ls_numche; ?>','<?php print $ls_chenum; ?>');</script>
		<?php
	}
}
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(codban,nomban,ctaban,dencta,ctascg,denctascg,fecapertura,feccierre,status,codtipcta,nomtipcta,saldo,conf_ch) {
	fop=opener.document.form1;
    fop.txtcuenta.value=ctaban;
    fop.txtdenominacion.value=dencta;
	fop.txttipocuenta.value=codtipcta;
	fop.txtdentipocuenta.value=nomtipcta;
	fop.txtcuenta_scg.value=ctascg;
	fop.txtdisponible.value=uf_convertir(saldo);	
	opener.uf_verificar_operacion();
	if(conf_ch == 0) {
		close();
	}
	else {
		f=document.form1;	   
		f.operacion.value="TOMAR";
		f.cuenta.value=ctaban;
		f.codigo.value=codban;
		f.action="sigesp_cat_ctabancoorigen.php";
		f.submit();
	}
}

function ue_search() {
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_cat_ctabancoorigen.php";
  	f.submit();
}
</script>
</html>
