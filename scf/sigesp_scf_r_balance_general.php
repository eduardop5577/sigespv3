<?php
/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
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
		print "	window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_scf.php");
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$io_fun_scg=new class_funciones_scf("../");
	$arrResultado = $io_fun_scg->uf_load_seguridad("SCF","sigesp_scf_r_balance_general.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	//$lb_valido=$io_fun_scg->uf_convertir_scgsaldos($la_seguridad);
	$ls_reporte   = $io_fun_scg->uf_select_config("SCF","REPORTE","BALANCE_GENERAL_MENSUAL","sigesp_scf_rpp_balance_general.php","C");//print $ls_reporte;
	$ls_reporteexcel   = $io_fun_scg->uf_select_config("SCF","REPORTE","BALANCE_GENERAL_MENSUAL_EXCEL","sigesp_scf_rpp_balance_general_excel.php","C");//print $ls_reporte;
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
<title>Balance General</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
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
<link href="css/scf.css" rel="stylesheet" type="text/css">
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
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu">
	<table width="778" border="0" bgcolor="#E7E7E7" align="center" cellpadding="0" cellspacing="0">
			
          <td width="413" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema
            de Contabilidad Fiscal</td>
			<td width="365" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
	  </table></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_search('<?php print $ls_codemp; ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <!--<td class="toolbar" width="25"><div align="center"><a href="javascript:ue_openexcel('<?php print $ls_codemp; ?>');"><img src="../shared/imagebank/tools20/excel.jpg" alt="Imprimir" width="20" height="20" border="0"></a></div></td>-->
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
</div> 
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scg->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scg);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="330" border="0" align="center" cellpadding="0" cellspacing="1" class="formato-blanco">
    <tr>
      <td width="83"></td>
    </tr>
    <tr>
      <td height="24" colspan="3" align="center" class="titulo-ventana">Balance General </td>
    </tr>
    <tr  style="display:none">
      <td height="24" align="center"><div align="right">Reporte en</div></td>
      <td width="235" height="24" align="center"><div align="left">
        <select name="cmbbsf" id="cmbbsf">
          <option value="0" selected>Bs.</option>
          <option value="1">Bs.F.</option>
        </select>
      </div></td>
      <td width="6" height="24" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="72" colspan="3" align="center"><div align="left"></div>        <div align="left"></div>        <div align="left" class="style14"></div>        <div align="left">
        <table width="293" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-celdanew">
            <td height="13" colspan="5"><strong class="titulo-celdanew">Mes y A&ntilde;o</strong></td>
            </tr>
          <tr>
            <td width="28" height="22">&nbsp;</td>
            <td width="36" height="22"><div align="right">Mes </div></td>
            <td width="89" height="22">
                <div align="left">
                  <select name="cmbmes" id="cmbmes">
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                  </select>
                </div></td>
            <td width="44" height="22"><div align="right">A&ntilde;o</div></td>
            <td width="94" height="22">		  <select name="cmbagno" id="cmbagno">             
              <option value="2000">2000</option>
              <option value="2005">2005</option>
              <option value="2006">2006</option>
              <option value="2007">2007</option>
              <option value="2008">2008</option>
              <option value="2009">2009</option>
              <option value="2010">2010</option>
              <option value="2011">2011</option>
              <option value="2012">2012</option>
              <option value="2013">2013</option>
              <option value="2014">2014</option>
              <option value="2015">2015</option>
              <option value="2016">2016</option>
              <option value="2017">2017</option>
              <option value="2018">2018</option>
              <option value="2019">2019</option>
              <option value="2020">2020</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>
              <option value="2026">2026</option>
              <option value="2027">2027</option>
              <option value="2028">2028</option>
              <option value="2029">2029</option>
              <option value="2030">2030</option>
            </select></td>
            </tr>
          <tr class="titulo-celdanew">
            <!--<td height="13" colspan="5">Nivel de la Cuenta </td>-->
            </tr>
          <tr style="display:none">
            <td height="22">&nbsp;</td>
            <td height="22"><div align="right">Nivel</div></td>
            <td height="22" colspan="2"><div align="left">
              <select name="cmbnivel" id="cmbnivel">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                  </select>
            </div></td>
            <td height="22">&nbsp;</td>
            </tr>
        </table>
        </div></td>
    </tr>
    <tr>
      <td height="22" colspan="3" align="center">
	            <input name="formato" type="hidden" id="formato" value="<?php print $ls_reporte; ?>">
	            <input name="formatoEXCEL" type="hidden" id="formatoEXCEL" value="<?php print $ls_reporteexcel; ?>">
	  </td>
    </tr>
  </table>
</form>      
</body>
<script >
function ue_search(codemp)
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		cmbnivel=f.cmbnivel.value;
		cmbmes=f.cmbmes.value;
		cmbagno=f.cmbagno.value;
		tiporeporte=f.cmbbsf.value;
		formato=f.formato.value;		
		if(cmbnivel=="s1")
		{
			alert ("Debe Seleccionar los Parametros de Busqueda");
		}
		else
		{
			pagina="reportes/"+formato+"?hidcodemp="+codemp+"&cmbmes="+cmbmes;
			pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&tiporeporte="+tiporeporte;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_openexcel(codemp)
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		cmbnivel=f.cmbnivel.value;
		cmbmes=f.cmbmes.value;
		cmbagno=f.cmbagno.value;
		tiporeporte=f.cmbbsf.value;
		formato=f.formatoEXCEL.value;		
		
		if(cmbnivel=="s1")
		{
			alert ("Debe Seleccionar los Parametros de Busqueda");
		}
		else
		{
			pagina="reportes/"+formato+"?hidcodemp="+codemp+"&cmbmes="+cmbmes;
			pagina=pagina+"&cmbagno="+cmbagno+"&cmbnivel="+cmbnivel+"&tiporeporte="+tiporeporte;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}
   
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}
</script>
</html>