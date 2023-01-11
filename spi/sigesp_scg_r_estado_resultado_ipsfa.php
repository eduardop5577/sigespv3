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
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "window.close();";
	 print "</script>";
   }
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("../shared/class_folder/sigesp_c_seguridad.php");
	$io_seguridad= new sigesp_c_seguridad();

	$dat=$_SESSION["la_empresa"];
	$ls_empresa=$dat["codemp"];
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_sistema="SPI";
	$ls_ventanas="sigesp_spi_r_ejecucion_trimestral.php";

	$la_seguridad["empresa"]=$ls_empresa;
	$la_seguridad["logusr"]=$ls_logusr;
	$la_seguridad["sistema"]=$ls_sistema;
	$la_seguridad["ventanas"]=$ls_ventanas;

$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>


<script type="text/javascript" src="js/stm31.js"></script>
<title>Estado de Resultado </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
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
</style>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {font-weight: bold}
.Estilo2 {font-size: 14px}
-->
</style>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno" style="border-bottom:0px;">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
   <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Contabilidad Patrimonial</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu" style="border-bottom:0px;"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
</table>	


<iframe src="sigesp_scg_r_estado_resultado_ipsfa2.php" style="width:100%;height:700px;position:absolute;border-bottom-width:0px;border:0px" id="frame1"></iframe>

</body>
</html>


<script>
var res = screen.width + "x" + screen.height;
//alert(res);



 function SistemaOperativo() {
if (navigator.userAgent.indexOf('IRIX') != -1) {var SO = "Irix" }
else if ((navigator.userAgent.indexOf('Win') != -1) && (navigator.userAgent.indexOf('98') != -1)) {var SO= "Windows 98"}
else if ((navigator.userAgent.indexOf('Win') != -1) && (navigator.userAgent.indexOf('95') != -1)) {var SO= "Windows 95"}
else if (navigator.appVersion.indexOf("16") !=-1) {var SO= "Windows 3.1"}
else if (navigator.userAgent.indexOf("NT 5.1") !=-1) {var SO= "Windows XP"}
else if (navigator.userAgent.indexOf("NT 5.2") !=-1) {var SO= "Windows Server 2003"}
else if (navigator.userAgent.indexOf("NT 5") !=-1) {var SO= "Windows 2000"}
else if (navigator.userAgent.indexOf("NT 6") !=-1) {var SO= "Windows Vista"}
else if (navigator.appVersion.indexOf("NT") !=-1) {var SO= "Windows NT"}
else if (navigator.appVersion.indexOf("SunOS") !=-1) {var SO= "SunOS"}
else if (navigator.appVersion.indexOf("Linux") !=-1) {var SO= "Linux"}
else if (navigator.userAgent.indexOf('Mac') != -1) {var SO= "Macintosh"}
else if (navigator.appName=="WebTV Internet Terminal") {var SO="WebTV"}
else if (navigator.appVersion.indexOf("HP") !=-1) {var SO="HP-UX"}
else {var SO= "No identificado"}
return SO;} 

sis = SistemaOperativo();
//alert(sis);
marco = document.getElementById('frame1').style;

if( sis=='Windows XP')
{

switch (res)
{
	case '1024x768':
		marco.left  = '111px' ;
		break
	case '1152x864':
		marco.left  = '175px';
		break
	case '1280x600':
		marco.left  = '239px';
		break
	case '800x600':
		marco.left  = '0px';
		break
}
}
else
{
	switch (res)
	{
		case '1024x768':
			marco.left  = '61px' ;
			break
		case '1152x864':
			marco.left  = '175px';
			break
		case '1280x600':
			marco.left  = '239px';
			break
		case '800x600':
			marco.left  = '0px';
			break
	}
}
</script>

