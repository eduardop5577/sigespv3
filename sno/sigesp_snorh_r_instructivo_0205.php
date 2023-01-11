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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_instructivo_0205.php",$ls_permisos,$la_seguridad,$la_permisos);
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
<title >Reporte Recursos Humanos por tipo de cargo y género (0205)</title>
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
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
        </table>
	 </td>
  </tr>
 <?php
	$ls_valor="";
	if (isset($_GET["valor"]))
	{
		$ls_valor=$_GET["valor"];
	}
	if ($ls_valor!='spg')
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif" title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
<?php
	$ls_valor="";
	if (isset($_GET["valor"]))
	{ 
		$ls_valor=$_GET["valor"];
	}
	if ($ls_valor!='spg')
	{
		print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>' );	   
	}
	else
	{
		print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: close();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>' );	
	}
?>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
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
<table width="550" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="500" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Reporte Recursos Humanos por tipo de cargo y género (0205)</td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew">Intervalo de Fechas </td>
          </tr>
        <tr>
          <td width="130" height="22" colspan="2"><div align="center">Mensual
            <input name="rdbintervalo" type="radio" class="sin-borde" onClick="javascript: ue_cambiarcombo('1');" value="1" checked>
          </div></td>
          <td width="130" colspan="2"><div align="center">Trimestral 
            <input name="rdbintervalo" type="radio" class="sin-borde" onClick="javascript: ue_cambiarcombo('3');" value="3" >
          </div></td>
        </tr>
        <tr>
          <td width="130" height="22" colspan="2"><div align="right">
            <label>
            <input name="txtdescripcion" type="text" class="sin-borde" id="txtdescripcion" style="text-align:right" value="Mensual">
            </label>
          </div></td>
          <td width="130" height="22" colspan="2"> 
            <div align="left">
              <select name="cmbrango" id="cmbrango">
                <option value="01-01" selected>Enero</option>
                <option value="02-02">Febrero</option>
                <option value="03-03">Marzo</option>
                <option value="04-04">Abril</option>
                <option value="05-05">Mayo</option>
                <option value="06-06">Junio</option>
                <option value="07-07">Julio</option>
                <option value="08-08">Agosto</option>
                <option value="09-09">Septiembre</option>
                <option value="10-10">Octubre</option>
                <option value="11-11">Noviembre</option>
                <option value="12-12">Diciembre</option>
              </select>
            </div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >
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
		rango=f.cmbrango.value;
		periodo="";
		if(f.rdbintervalo[0].checked)
		{
			periodo="1"; //Mensual
		}
		if(f.rdbintervalo[1].checked)
		{
			periodo="3"; //Trimestral
		}
		if(rango!="")
		{
			pagina="reportes/sigesp_snorh_rpp_instructivo_0205.php?rango="+rango+"&periodo="+periodo;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			alert("Debe seleccionar un rango de meses.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_cambiarcombo(codigo)
{
	f=document.form1;
	if(codigo=="1")
	{
		f.txtdescripcion.value="Mensual";
		f.cmbrango.length=0;
		f.cmbrango.options[0]= new Option('--Seleccione--','');
		f.cmbrango.options[1]= new Option('Enero','01-01');
		f.cmbrango.options[2]= new Option('Febrero','02-02');
		f.cmbrango.options[3]= new Option('Marzo','03-03');
		f.cmbrango.options[4]= new Option('Abril','04-04');
		f.cmbrango.options[5]= new Option('Mayo','05-05');
		f.cmbrango.options[6]= new Option('Junio','06-06');
		f.cmbrango.options[7]= new Option('Julio','07-07');
		f.cmbrango.options[8]= new Option('Agosto','08-08');
		f.cmbrango.options[9]= new Option('Septiembre','09-09');
		f.cmbrango.options[10]= new Option('Octubre','10-10');
		f.cmbrango.options[11]= new Option('Noviembre','11-11');
		f.cmbrango.options[12]= new Option('Diciembre','12-12');
	}
        if(codigo=="3")
	{
		f.txtdescripcion.value="Trimestral";
		f.cmbrango.length=0;
		f.cmbrango.options[0]= new Option('--Seleccione--','');
		f.cmbrango.options[1]= new Option('Enero-Marzo','01-03');
		f.cmbrango.options[2]= new Option('Febrero-Abril','02-04');
		f.cmbrango.options[3]= new Option('Marzo-Mayo','03-05');
		f.cmbrango.options[4]= new Option('Abril-Junio','04-06');
		f.cmbrango.options[5]= new Option('Mayo-Julio','05-07');
		f.cmbrango.options[6]= new Option('Junio-Agosto','06-08');
		f.cmbrango.options[7]= new Option('Julio-Septiembre','07-09');
		f.cmbrango.options[8]= new Option('Agosto-Octubre','08-10');
		f.cmbrango.options[9]= new Option('Septiembre-Noviembre','09-11');
		f.cmbrango.options[10]= new Option('Octubre-Diciembre','10-12');
	}
}
</script> 
</html>