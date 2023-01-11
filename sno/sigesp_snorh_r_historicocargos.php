<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();   
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_historicocargos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Reporte Historico de Cargos</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>	 </td>
  </tr>
  <?php

	if (isset($_GET["valor"]))
	{ $ls_valor=$_GET["valor"];	}
	else
	{ $ls_valor="";}
	
	if ($ls_valor!='srh')
	{
	   print ('<tr>');
	   print ('<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>' );
	   print ('</tr>');
	}
	
	
  ?>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif" title="Imprimir"  alt="Imprimir" width="20" height="20" border="0"></a></div></td>    
   
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="620" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="570" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <td height="20" colspan="6" class="titulo-ventana">Reporte Historico de Cargos </td>
  </tr>
  <tr>
    <td height="20" colspan="7" class="titulo-celdanew">Intervalo de Cargo </td>
  </tr>
  <tr>
    <td width="41">&nbsp;</td>
    <td width="123" height="22"><div align="right"> Desde </div></td>
    <td width="118"><div align="left">
      <input name="txtcodcardes" type="text" id="txtcodcardes" size="13" maxlength="10" readonly>
      <a href="javascript: ue_buscarcargodesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
    <td width="97"><div align="right">Hasta </div></td>
    <td colspan="3"><div align="left">
      <input name="txtcodcarhas" type="text" id="txtcodcarhas" size="13" maxlength="10" readonly>
      <a href="javascript: ue_buscarcargohasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
  </tr>
  <tr>
    <td height="20" colspan="7" class="titulo-celdanew">Intervalo de Asignacion de Cargo </td>
  </tr>
  <tr>
    <td width="41">&nbsp;</td>
    <td width="123" height="22"><div align="right"> Desde </div></td>
    <td width="118"><div align="left">
      <input name="txtcodasicardes" type="text" id="txtcodasicardes" size="13" maxlength="10" readonly>
      <a href="javascript: ue_buscarasignacioncargodesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
    <td width="97"><div align="right">Hasta </div></td>
    <td colspan="3"><div align="left">
      <input name="txtcodasicarhas" type="text" id="txtcodasicarhas" size="13" maxlength="10" readonly>
      <a href="javascript: ue_buscarasignacioncargohasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
  </tr>
  <tr>
    <td height="20" colspan="7" class="titulo-celdanew">Intervalo de Personal </td>
  </tr>
  <tr>
    <td width="41">&nbsp;</td>
    <td width="123" height="22"><div align="right"> Desde </div></td>
    <td width="118"><div align="left">
      <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10"  value="<?php print $ls_codperdes;?>" readonly>
      <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
    <td width="97"><div align="right">Hasta </div></td>
    <td colspan="2"><div align="left">
      <input name="txtcodperhas" type="text" id="txtcodperhas"  value="<?php print $ls_codperhas;?>" size="13" maxlength="10" readonly>
      <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td height="22">&nbsp;</td>
    <td colspan="4">&nbsp;</td>
  </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >
var patron = new Array(2,2,4);
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		codcardes=f.txtcodcardes.value;
		codcarhas=f.txtcodcarhas.value;
		codasicardes=f.txtcodasicardes.value;
		codasicarhas=f.txtcodasicarhas.value;
		codperdes=f.txtcodperdes.value;
		codperhas=f.txtcodperhas.value;
		if ((codcardes!="")||(codcarhas!="")||(codasicardes!="")||(codasicarhas!="")||(codperdes!="")||(codperhas!=""))
		{
			if((codcardes!="")&&(codcarhas!=""))
			{
				if(codcardes<=codcarhas)
				{
					pagina="reportes/sigesp_snorh_rpp_historicocargo.php?codcardes="+codcardes+"&codcarhas="+codcarhas;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del Cargo está erroneo");
				}
			}
			if((codasicardes!="")&&(codasicarhas!=""))
			{
				if(codasicardes<=codasicarhas)
				{
					pagina="reportes/sigesp_snorh_rpp_historicocargo.php?codasicardes="+codasicardes+"&codasicarhas="+codasicarhas;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango de la Asignacion de Cargo está erroneo");
				}
			}
			if((codperdes!="")&&(codperhas!=""))
			{
				if(codperdes<=codperhas)
				{
					pagina="reportes/sigesp_snorh_rpp_historicocargopersonal.php?codperdes="+codperdes+"&codperhas="+codperhas;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del personal está erroneo");
				}
			}
		}
		else
		{
			alert("Debe colocar un rango válido");
		}		
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_buscarcargodesde()
{
	window.open("sigesp_sno_cat_cargo.php?tipo=rephiscardes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarcargohasta()
{
	f=document.form1;
	if(f.txtcodcardes.value!="")
	{
		window.open("sigesp_sno_cat_cargo.php?tipo=rephiscarhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un cargo desde.");
	}
}

function ue_buscarasignacioncargodesde()
{
	window.open("sigesp_sno_cat_asignacioncargo.php?tipo=rephiscardes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarasignacioncargohasta()
{
	f=document.form1;
	if(f.txtcodasicardes.value!="")
	{
		window.open("sigesp_sno_cat_asignacioncargo.php?tipo=rephiscarhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una Asignación de Cargo desde.");
	}
}

function ue_buscarpersonaldesde()
{
	window.open("sigesp_snorh_cat_personal.php?tipo=rephiscardes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarpersonalhasta()
{
	f=document.form1;
	if(f.txtcodperdes.value!="")
	{
		window.open("sigesp_snorh_cat_personal.php?tipo=rephiscarhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un personal desde.");
	}
}
</script> 
</html>