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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Cuentas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
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
require_once("sigesp_c_cuentas_banco.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");

$io_include  = new sigesp_include();
$ls_conect   = $io_include->uf_conectar();
$io_sql      = new class_sql($ls_conect);
$io_msg      = new class_mensajes();
$io_function = new class_funciones();
$ls_codemp   = $_SESSION["la_empresa"]["codemp"];
$io_ctaban   = new sigesp_c_cuentas_banco();

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

if (array_key_exists("operacion",$_POST))
   {
		 $ls_operacion = $_POST["operacion"];
		 $ls_codigo    = $_POST["codigo"];
		 $ls_nomban    = $_POST["hidnomban"];
		 $ls_ctaban    = $_POST["cuenta"];
		 $ls_denctaban = $_POST["denominacion"];
		 $ls_codcon= $_POST["codcon"];
                 $ls_fecha= $_POST["fecha"];
		 $ls_tipo2= $_POST["tipo2"];
		 if (array_key_exists("tipo",$_GET))
		 {
			 $ls_tipo=$_GET["tipo"];
			 $ls_codbandes=$_GET["bandes"];
			 $ls_nombandes=$_GET["hidnombandes"];
			 $ls_codbanhas=$_GET["banhas"];
			 $ls_nombanhas=$_GET["hidnombanhas"];
                         $ls_fecha= $_GET["fecha"];
		 }
		 else
		 {
			 $ls_tipo='';
			 $ls_codbandes='';
			 $ls_nombandes='';
			 $ls_codbanhas='';
			 $ls_nombanhas='';
                         $ls_fecha= '';
		 }
   }
else
   {
     if(array_key_exists("tipo2",$_GET))
	 {
   		$ls_tipo2=$_GET["tipo2"];
	 }
	 else
	 {
   		$ls_tipo2="";
	 }
	 if (array_key_exists("tipo",$_GET))
		{
		 $ls_tipo=$_GET["tipo"];
		 $ls_codbandes=$_GET["bandes"];
		 $ls_nombandes=$_GET["hidnombandes"];
		 $ls_codbanhas=$_GET["banhas"];
		 $ls_nombanhas=$_GET["hidnombanhas"];
		}
	else
	 	{
		 $ls_tipo='';
		 $ls_codbandes='';
		 $ls_nombandes='';
		 $ls_codbanhas='';
		 $ls_nombanhas='';
		}
	 $ls_operacion = "BUSCAR";
	 if ($ls_tipo=='')
	 {
		 $ls_codigo    = $_GET["codigo"];
		 $ls_nomban    = $_GET["hidnomban"];
                  $ls_fecha= $_GET["fecha"];
	 }
	 if (array_key_exists("codcon",$_GET))
	    {
		  $ls_codcon=$_GET["codcon"];
	    }
	 else
	    {
		  $ls_codcon='---';
	    }
	 $ls_ctaban    = "";
	 $ls_denctaban = "";
   }
?>
<table width="554" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="22" colspan="2"><input name="tipo2" type="hidden" id="tipo2" value="<?php print $ls_tipo2; ?>">
        <input name="operacion" type="hidden" id="operacion">
        <input name="codigo" type="hidden" id="codigo" value="<?php print $ls_codigo;?>">
        <input name="hidnomban" type="hidden" id="hidnomban" value="<?php print $ls_nomban ?>">
        <input name="fecha" type="hidden" id="fecha" value="<?php print $ls_fecha ?>">
	<input name="bandes" type="hidden" id="bandes" value="<?php print $ls_codbandes;?>">
        <input name="hidnombandes" type="hidden" id="hidnombandes" value="<?php print $ls_nombandes ?>">
		<input name="banhas" type="hidden" id="banhas" value="<?php print $ls_codbanhas;?>">
        <input name="hidnombanhas" type="hidden" id="hidnombanhas" value="<?php print $ls_nombanhas ?>">
          <?php
		  if ($ls_tipo=='')
		  {
		  ?>
		  Cat&aacute;logo de Cuentas <?php print $ls_nomban ?></td>
		  <?php
		  }
		   else
		  {
		  ?>
		  Cat&aacute;logo de Cuentas entre Bancos <?php print $ls_nombandes ?> y <?php print $ls_nombanhas ?><td width="59"></td>
		  <?php
		  }
		  ?>
    </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="66" height="22" style="text-align:right">Cuenta</td>
        <td width="427" height="22"><div align="left">
          <input name="cuenta" type="text" id="cuenta" size="35" maxlength="25" style="text-align:center">        
        </div></td>
      </tr>
      <tr>
        <td height="22" style="text-align:right">Nombre</td>
        <td height="22"><div align="left">
          <input name="denominacion" type="text" id="denominacion" size="60">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
  </table>
  <p align="center">
<?php
echo "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
echo "<tr class=titulo-celda height=22>";
echo "<td style=text-align:center>C?digo</td>";
echo "<td style=text-align:center>Denominaci?n</td>";
echo "<td style=text-align:center>Tipo</td>";
echo "<td style=text-align:center>Contable</td>";
echo "<td style=text-align:center>Descripci?n</td>";
echo "<td style=text-align:center>Apertura</td>";
echo "</tr>";
$ls_casacon=$_SESSION["la_empresa"]["casconmov"];
if ($ls_operacion=="BUSCAR")
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
	 if (($ls_casacon==1)&&($ls_codcon!=="---"))
	 {
	 	 $ls_sql="SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta, ".
					 " scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
					 " scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr, ".
					 " scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact, scb_ctabanco.ctabanext, MAX(sigesp_moneda.codmon) AS codmon, MAX(sigesp_moneda.denmon) AS denmon ".
			    " FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, scb_casamientoconcepto, sss_permisos_internos, sigesp_moneda ".
			   " WHERE scb_ctabanco.codemp='".$ls_codemp."' ".
			     " AND scb_ctabanco.codban like '%".$ls_codigo."%'  ".
				 " AND scb_ctabanco.ctaban like '".$ls_ctaban."%' ".
				 " AND scb_ctabanco.estact='1' ".
				 " AND scb_casamientoconcepto.codconmov='".$ls_codcon."' ".
				 " AND scb_casamientoconcepto.codban='".$ls_codigo."' ".
				 " AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($ls_denctaban)."%' ".
			     " AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
			     " AND scb_ctabanco.codban=scb_banco.codban ".
				 " AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
				 " AND scb_ctabanco.codemp=scg_cuentas.codemp ".
				 " AND scb_ctabanco.codmon=sigesp_moneda.codmon ".
				 " AND scb_ctabanco.codemp=sigesp_moneda.codemp ".
				 " ".$ls_sql_seguridad." ".
				 " GROUP BY scb_ctabanco.ctaban, scb_ctabanco.dencta, scb_ctabanco.sc_cuenta, ".
				 " scg_cuentas.denominacion, scb_ctabanco.codban, scb_banco.nomban, scb_ctabanco.codtipcta, ".
				 " scb_tipocuenta.nomtipcta, scb_ctabanco.fecapr, scb_ctabanco.feccie, scb_ctabanco.estact, scb_ctabanco.ctabanext"; 
	 }
	 else
	 {
		 if ($ls_tipo!='')
		 {
		 $ls_sql="SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta, ".
						 " scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
						 " scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr, ".
						 " scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact, scb_ctabanco.ctabanext, sigesp_moneda.codmon AS codmon, sigesp_moneda.denmon AS denmon ".
					" FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos, sigesp_moneda ".
				   " WHERE scb_ctabanco.codemp='".$ls_codemp."' ".
					 " AND scb_ctabanco.codban between '".$ls_codbandes."' and '".$ls_codbanhas."' ".
					 " AND scb_ctabanco.ctaban like '%".$ls_ctaban."' ".
					 " AND scb_ctabanco.estact='1' ".  
					 " AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($ls_denctaban)."%' ".
					 " AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
					 " AND scb_ctabanco.codban=scb_banco.codban ".
					 " AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
					 " AND scb_ctabanco.codemp=scg_cuentas.codemp".
                                        " AND scb_ctabanco.codmon=sigesp_moneda.codmon ".
                                        " AND scb_ctabanco.codemp=sigesp_moneda.codemp ".
					 " ".$ls_sql_seguridad." ";
		 }
		 else
		 {
		 	if($ls_tipo2=="RPT")
			{
				$activo="";
			}
			else
			{
				$activo=" AND scb_ctabanco.estact='1' ";
			}
		 $ls_sql="SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta, ".
						 " scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
						 " scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr, ".
						 " scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact, scb_ctabanco.ctabanext, sigesp_moneda.codmon AS codmon, sigesp_moneda.denmon AS denmon ".
					" FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos, sigesp_moneda ".
				   "WHERE scb_ctabanco.codemp='".$ls_codemp."' ".
					" AND scb_ctabanco.codban like '%".$ls_codigo."%' ".  
					" AND scb_ctabanco.ctaban like '".$ls_ctaban."%' ".
					" AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($ls_denctaban)."%' ".
					$activo.
					" AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
					" AND scb_ctabanco.codban=scb_banco.codban ".
					" AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
					" AND scb_ctabanco.codemp=scg_cuentas.codemp ".
				 " AND scb_ctabanco.codmon=sigesp_moneda.codmon ".
				 " AND scb_ctabanco.codemp=sigesp_moneda.codemp ".
					 " ".$ls_sql_seguridad." ";
		 }			 
	}
	  $rs_data = $io_sql->select($ls_sql);
	  if ($rs_data===false)
	     {
		   $io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
		 }
      else
	     {
		   $li_totrows = $io_sql->num_rows($rs_data);
		   if ($li_totrows>0)
		      {
			    while(!$rs_data->EOF)
				     {
					   $ls_codban 	   = trim(str_pad($rs_data->fields["codban"],3,0,0));
					   $ls_nomban 	   = $rs_data->fields["nomban"];
					   $ls_ctaban      = trim($rs_data->fields["ctaban"]);
					   $ls_dencta      = $rs_data->fields["dencta"];
					   $ls_codtipcta   = $rs_data->fields["codtipcta"];
					   $ls_nomtipcta   = $rs_data->fields["nomtipcta"];
					   $ls_ctascg      = $rs_data->fields["sc_cuenta"];
					   $ls_denctascg   = $rs_data->fields["denominacion"];
					   $ls_ctabanext   = $rs_data->fields["ctabanext"];
					   $ls_codmon   = $rs_data->fields["codmon"];
					   $ls_denmon   = $rs_data->fields["denmon"];
                                           $ls_tascam=obtener_tasa($ls_codemp,$ls_codmon,$ls_fecha,$io_sql);
                                           $ls_tascam=number_format($ls_tascam,8,',','.');
					   $ls_fecapertura = $io_function->uf_convertirfecmostrar($rs_data->fields["fecapr"]);
					   $ls_feccierre   = $io_function->uf_convertirfecmostrar($rs_data->fields["feccie"]);
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
					   $ls_status  = $rs_data->fields["estact"];
					   if ($ls_tipo==1)
					   {
						   echo "<td style=text-align:center><a href=\"javascript: aceptarcoldes('$ls_codban','$ls_nomban','$ls_ctaban','$ls_dencta','$ls_ctascg','$ls_denctascg','$ls_fecapertura','$ls_feccierre','$ls_status','$ls_codtipcta','$ls_nomtipcta','$ldec_saldo');\">".$ls_ctaban."</a></td>";
						   echo "<td style=text-align:left title='".$ls_dencta."'>".$ls_dencta."</td>";
						   echo "<td style=text-align:left title='".$ls_nomtipcta."'>".$ls_nomtipcta."</td>";
						   echo "<td style=text-align:center>".$ls_ctascg."</td>";
						   echo "<td style=text-align:left title='".$ls_denctascg."'>".$ls_denctascg."</td>";																			
						   echo "<td style=text-align:center>".$ls_fecapertura."</td>";					
						   echo "</tr>";
					   }
					   elseif ($ls_tipo==2)
					   {
					   	   echo "<td style=text-align:center><a href=\"javascript: aceptarcolhas('$ls_codban','$ls_nomban','$ls_ctaban','$ls_dencta','$ls_ctascg','$ls_denctascg','$ls_fecapertura','$ls_feccierre','$ls_status','$ls_codtipcta','$ls_nomtipcta','$ldec_saldo');\">".$ls_ctaban."</a></td>";
						   echo "<td style=text-align:left title='".$ls_dencta."'>".$ls_dencta."</td>";
						   echo "<td style=text-align:left title='".$ls_nomtipcta."'>".$ls_nomtipcta."</td>";
						   echo "<td style=text-align:center>".$ls_ctascg."</td>";
						   echo "<td style=text-align:left title='".$ls_denctascg."'>".$ls_denctascg."</td>";																			
						   echo "<td style=text-align:center>".$ls_fecapertura."</td>";					
						   echo "</tr>";
					   }
					   else
					   {
					   	   echo "<td style=text-align:center><a href=\"javascript: aceptar('$ls_codban','$ls_nomban','$ls_ctaban','$ls_dencta','$ls_ctascg','$ls_denctascg','$ls_fecapertura','$ls_feccierre','$ls_status','$ls_codtipcta','$ls_nomtipcta','$ldec_saldo','$ls_ctabanext','$ls_codmon','$ls_denmon','$ls_tascam');\">".$ls_ctaban."</a></td>";
						   echo "<td style=text-align:left title='".$ls_dencta."'>".$ls_dencta."</td>";
						   echo "<td style=text-align:left title='".$ls_nomtipcta."'>".$ls_nomtipcta."</td>";
						   echo "<td style=text-align:center>".$ls_ctascg."</td>";
						   echo "<td style=text-align:left title='".$ls_denctascg."'>".$ls_denctascg."</td>";																			
						   echo "<td style=text-align:center>".$ls_fecapertura."</td>";					
						   echo "</tr>";
					   }			
                       $rs_data->MoveNext();
					 }
			  }
		   else
		      {
			  		if (($ls_casacon==1)&&($ls_codcon!=="---"))
					{
			    		$io_msg->message("No se han creado Cuentas Bancarias !!!");
					}
					else
					{
						if ($ls_tipo!='')
						{
							$io_msg->message("Por Favor Ajuste el Rango de los Bancos");
						}
						else
						{
							$io_msg->message("No se han asociado Cuentas Bancarias  al tipo de concepto seleccionado o no posee permisologia para las cuentas!");
						}	
					}   
			  }
		 }  		 
   }
echo "</table>";
?></p>
<input name="codcon" type="hidden" id="codcon" value="<? print $ls_codcon;?>">

</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
  function aceptar(codban,nomban,ctaban,dencta,ctascg,denctascg,fecapertura,feccierre,status,codtipcta,nomtipcta,saldo,ctabanext,codmon,denmon,tascam)
  {
    fop 	  = opener.document.form1;
	ls_opener = opener.document.form1.id;
	if (ls_opener=='sigesp_scb_p_progpago_creditos.php')
	   {
	     li_filsel = fop.hidfilsel.value;
		 ls_denctaban = ctaban+" - "+dencta;
		 eval("fop.txtctaban"+li_filsel+".value="+"'      "+ls_denctaban+"'");
		 eval("fop.txtctaban"+li_filsel+".title="+"'"+ls_denctaban+"'");
		 eval("fop.hidctaban"+li_filsel+".value="+"'"+ctaban+"'");
	   }
	else
	   {
		 if (ls_opener=='sigesp_scb_p_liquidacion_creditos.php')
		    {
			  if (fop.hidcodope.value=='CH')
			     {
				   li_filins = fop.hidtotrowscg.value;//Fila donde insertaremos la Cuenta Contable y su denominaci?n.
				   eval("fop.txtscgcta"+li_filins+".value="+ctascg);
				   eval("fop.txtdenscgcta"+li_filins+".value="+"'"+denctascg+"'");
				   eval("fop.txtdenscgcta"+li_filins+".title="+"'"+denctascg+"'");
				   fop.txtctaban.value    = ctaban;
				   fop.txtdenctaban.value = dencta;
				   fop.hidscgcta.value    = ctascg;
				 }
			}
		 else
		    {
			  if (ls_opener=='sigesp_scb_p_progpago.php')
			     {
				   lb_valido = uf_evaluate_datos_programacion(ctaban);
				   if (lb_valido)
				      {
					    fop.txtcuenta.value=ctaban;
					    fop.txtdenominacion.value=dencta;
					    fop.txttipocuenta.value=codtipcta;
					    fop.txtdentipocuenta.value=nomtipcta;
					    fop.txtcuenta_scg.value=ctascg;
					    fop.txtdisponible.value=saldo;
					  }
				 }
			  else if(ls_opener=='sigesp_scb_p_controldocumentos.php')
			     {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				 }
			  else if(ls_opener=='sigesp_scb_r_transferencias_bancarias.php')
			     {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				 }
			  else if(ls_opener=='sigesp_scb_r_listadoubicaciondocumentos.php')
			     {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				 }
			  else if(ls_opener=='sigesp_scb_p_conciliacionautomatica.php')
			     {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				 } 
			  else if(ls_opener=='sigesp_scb_p_conciliacion.php')
			     {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				   fop.txtcodmon.value=codmon;
				   fop.txtdenmon.value=denmon;
				   fop.txtcuenta_scg.value=ctascg;
				   fop.txtdisponible.value=saldo;
				 } 
			  else
				 {
				   fop.txtcuenta.value=ctaban;
				   fop.txtdenominacion.value=dencta;
				   fop.txttipocuenta.value=codtipcta;
				   fop.txtdentipocuenta.value=nomtipcta;
				   fop.txtcuenta_scg.value=ctascg;
				   fop.txtdisponible.value=saldo;
				   //fop.txtctabanext.value=ctabanext;
				   fop.txtcodmon.value=codmon;
				   fop.txtdenmon.value=denmon;
                                   fop.txttascam.value=tascam;
				 }
			}
	   }
	   
	close();
  }
  
  function aceptarcoldes(codban,nomban,ctaban,dencta,ctascg,denctascg,fecapertura,feccierre,status,codtipcta,nomtipcta,saldo)
  {
    fop 	  = opener.document.form1;
    fop.txtcuentades.value=ctaban;
	fop.txtdenominaciondes.value=dencta;
	close();
  }
  
  function aceptarcolhas(codban,nomban,ctaban,dencta,ctascg,denctascg,fecapertura,feccierre,status,codtipcta,nomtipcta,saldo)
  {    
  	fop 	  = opener.document.form1;
    fop.txtcuentahas.value=ctaban;
	fop.txtdenominacionhas.value=dencta;
	close();
  }
  
  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_cat_ctabanco.php?bandes=<?php print $ls_codbandes;?>&hidnombandes=<?php print $ls_nombandes;?>&banhas=<?php print $ls_codbanhas;?>&hidnombanhas=<?php print $ls_nombanhas;?>&tipo=<?php print $ls_tipo;?>";
  f.submit();
  }

function uf_evaluate_datos_programacion(as_ctaban)
{
  fop       = opener.document.form1;
  li_totrow = fop.totsol.value;
  lb_valido = true;
  for (li_i=1;li_i<=li_totrow;li_i++)
      {
	    if (eval("fop.chksel"+li_i+".checked"))
		   {
			 ls_ctaban = eval("fop.hidctaban"+li_i+".value");			 
			 if (ls_ctaban!="")
			    {
				  if (ls_ctaban!=as_ctaban)
				     {
					   lb_valido = false;
					   ls_numsol = eval("fop.txtnumsol"+li_i+".value");
					   alert("Solicitud "+ls_numsol+ ", esta asociada a Orden de Pago Ministerio emitida por Cuenta Bancaria Distinta !!!");
					 }
				}
		   }
	  }
  return lb_valido;
}  
</script>
</html>