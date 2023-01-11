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
<title>Cat&aacute;logo de Cuentas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
		fop.txtdocumento.value = as_numche;
		fop.txtchequera.value  = as_chequera;
		opener.document.getElementById("txtdocumento").readOnly = true
	}
  	else {
  	  	fop.txtdocumento.value = "";
	}
  	close();
}
</script>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style></head>

<body>
<form name="form1" method="post" action="">
<?php

require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("sigesp_c_cuentas_banco.php");
require_once("class_funciones_banco.php");/////agregado 13/12/2007

$in		   = new sigesp_include();
$con	   = $in->uf_conectar();
$io_msg    = new class_mensajes();
$io_sql    = new class_sql($con);
$fun       = new class_funciones();
$ls_codemp = $arr["codemp"];
$io_ctaban = new sigesp_c_cuentas_banco();
$io_update 		 = new class_funciones_banco();/////agregado el 13/12/2003

if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion    = $_POST["operacion"];
	 $ls_codigo       = $_POST["codigo"];
	 $ls_nomban       = $_POST["hidnomban"];
	 $ls_ctaban       = "%".$_POST["cuenta"]."%";
	 $ls_denominacion = "%".$_POST["denominacion"]."%";
         $ls_fecha= $_POST["fecha"];         
   }
else
   {
	 $ls_operacion    = "BUSCAR";
	 $ls_codigo       = $_GET["codigo"];
	 $ls_nomban       = $_GET["hidnomban"];
	 $ls_ctaban       = "%%";
	 $ls_denominacion = "%%";
         $ls_fecha= $_GET["fecha"];
   }
   
function obtener_tasa($ls_codemp,$codmon,$fecha,$io_sql)
{
    $tascam=1;
    $ls_sql = "SELECT tascam1 ".
              "  FROM sigesp_dt_moneda ".
              " WHERE codemp='".$ls_codemp."' ".
              "   AND codmon='".$codmon."' ".
              "   AND fecha<='".$fecha."' ".
              " ORDER BY fecha DESC ";

    $rs_data = $io_sql->select($ls_sql);
    if ($rs_data===false)
    {
        //$io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
    }
    else
    {
        if(!$rs_data->EOF)
        {
             $tascam=$rs_data->fields["tascam1"];
        }
    }
    return $tascam;
}
   
?>
<table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="22" colspan="2"><input name="operacion" type="hidden" id="operacion">
        <input name="codigo" type="hidden" id="codigo" value="<?php print $ls_codigo;?>">
          <input name="hidnomban" type="hidden" id="hidnomban" value="<?php print $ls_nomban ?>">
          <input name="fecha" type="hidden" id="fecha" value="<?php print $ls_fecha ?>">
          Cat&aacute;logo de Cuentas <?php print $ls_nomban ?></td>
       </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="67" height="22"><div align="right">Cuenta</div></td>
        <td width="431" height="22"><div align="left">
          <input name="cuenta" type="text" id="cuenta" size="35" maxlength="25" style="text-align:center">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td height="22"><div align="left">
          <input name="denominacion" type="text" id="denominacion" size="60">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div>
		  <input name="txtcuenta" type="hidden" id="txtcuenta" value="<?php print $ls_cuenta;?>">
		 <input name="txtcodban" type="hidden" id="txtcodban" value="<?php print $ls_codban;?>">
		 <input name="txtdocumento" type="hidden" id="txtdocumento" value="<?php print $ls_numche;?>">
		 <input name="txtlectura" type="hidden" id="txtlectura" value="<?php print $ls_lectura;?>"></td>
      </tr>
    </table>
	 <div align="center"><br>
<?php
print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td>Código </td>";
print "<td>Denominación</td>";
print "<td>Banco</td>";
print "<td>Tipo de Cuenta</td>";
print "<td>Cuenta Contable</td>";
print "<td>Denominación Cta. Contable</td>";
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
			" scb_ctabanco.fecapr as fecapr,scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact, sigesp_moneda.codmon AS codmon, sigesp_moneda.denmon AS denmon  ".
			" FROM scb_ctabanco ,scb_tipocuenta ,scb_banco ,scg_cuentas, sss_permisos_internos, sigesp_moneda  ".
			" WHERE scb_ctabanco.codemp='".$ls_codemp."' ".
			" AND scb_ctabanco.estact='1' ".
			" AND scb_ctabanco.codban like '%".$ls_codigo."%' ".  
			" AND scb_ctabanco.ctaban like '".$ls_ctaban."' ".
			" AND scb_ctabanco.dencta like '".$ls_denominacion."' ".
			" AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
			" AND scb_ctabanco.codban=scb_banco.codban ".
			" AND (scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta AND scb_ctabanco.codemp=scg_cuentas.codemp) ".
                        " AND scb_ctabanco.codmon=sigesp_moneda.codmon ".
                        " AND scb_ctabanco.codemp=sigesp_moneda.codemp ".
			" ".$ls_sql_seguridad." ";
	$rs_data = $io_sql->select($ls_sql);
	if ($rs_data===false)
	   {
	     $io_msg->message("Error en select");
	   }
 	else
	   {
	     $li_numrows = $io_sql->num_rows($rs_data);
		 if ($li_numrows>0)
		    {
			  while ($row=$io_sql->fetch_row($rs_data))
			        {
					  print "<tr class=celdas-blancas>";
					  $ls_codban 	  = $row["codban"];
					  $ls_nomban 	  = $row["nomban"];
					  $ls_ctaban      = $row["ctaban"];
					  $ls_dencta      = $row["dencta"];
					  $ls_codtipcta   = $row["codtipcta"];
					  $ls_nomtipcta   = $row["nomtipcta"];
					  $ls_ctascg      = $row["sc_cuenta"];
					  $ls_denctascg   = $row["denominacion"];
					   $ls_codmon   = $row["codmon"];
					   $ls_denmon   = $row["denmon"];
                                            $ls_tascam=obtener_tasa($ls_codemp,$ls_codmon,$ls_fecha,$io_sql);
                                           $ls_tascam=number_format($ls_tascam,8,',','.');                                           
					  $ls_fecapertura = $fun->uf_convertirfecmostrar($row["fecapr"]);
					  $ls_feccierre   = $fun->uf_convertirfecmostrar($row["feccie"]);
						$adec_saldo="";
						$arrResultado="";
					   $arrResultado=$io_ctaban->uf_verificar_saldo($ls_codban,$ls_ctaban,$adec_saldo);					  
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
					  $ls_status  = $row["estact"];
					  print "<td><a href=\"javascript: aceptar('$ls_codban','$ls_nomban','$ls_ctaban','$ls_dencta','$ls_ctascg','$ls_denctascg','$ls_fecapertura','$ls_feccierre','$ls_status','$ls_codtipcta','$ls_nomtipcta','$ldec_saldo','$ls_codmon','$ls_denmon','$ls_tascam');\">".$ls_ctaban."</a></td>";
					  print "<td>".$ls_dencta."</td>";
					  print "<td>".$ls_nomban."</td>";
					  print "<td>".$ls_nomtipcta."</td>";
					  print "<td>".$ls_ctascg."</td>";
					  print "<td>".$ls_denctascg."</td>";																			
					  print "<td>".$ls_fecapertura."</td>";					
					  print "</tr>";			
					}
			} 
	     else
		    {?>
	          <script >
			    alert("No se han creado Cuentas Bancarias, o no posee permisologias para la cuenta bancaria !");
				close();
			  </script>
	     <?php
			}
		}
}
print "</table>";

if ($ls_operacion=="TOMAR") {
	$ls_ctaban = $_POST["txtcuenta"];
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
	else {
		?>
			<script >close();</script>
		<?php
	}
}
?>

</div>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">


  function aceptar(codban,nomban,ctaban,dencta,ctascg,denctascg,fecapertura,feccierre,status,codtipcta,nomtipcta,saldo,codmon,denmon,tascam)
  {
    opener.document.form1.txtcuenta.value=ctaban;
    opener.document.form1.txtdenominacion.value=dencta;
	opener.document.form1.txttipocuenta.value=codtipcta;
	opener.document.form1.txtdentipocuenta.value=nomtipcta;
	opener.document.form1.txtcuenta_scg.value=ctascg;
	opener.document.form1.txtdisponible.value=uf_convertir(saldo);
	opener.document.form1.txtcodmon.value=codmon;
	opener.document.form1.txtdenmon.value=denmon;
        opener.document.form1.txttascam.value=tascam;
    f=document.form1;	   
	f.operacion.value="TOMAR";
	f.txtcuenta.value=ctaban;
	f.txtcodban.value=codban;
	f.action="sigesp_cat_ctabanco2.php";
	f.submit();	   
  }
  
  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_cat_ctabanco.php";
  f.submit();
  }




</script>
</html>