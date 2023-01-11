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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_garantia_prestaciones_segurosocial.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	$ld_ano=substr($_SESSION["la_empresa"]["periodo"],0,4);
	$li_anocurper="";
	$ls_mescurper="";
	$ls_desmesper="";
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
<title >Reporte Deposito de Garantias</title>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif"  title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
   <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_openexcel();"></a><a href="javascript: ue_enviarcorreo();"><img src="../shared/imagebank/tools20/email.png" title="Enviar por Correo" alt="Enviar por Correo" width="20" height="20" border="0"></a></div></td>
   <?php

	if (isset($_GET["valor"]))
	{ $ls_valor=$_GET["valor"];	}
	else
	{ $ls_valor="";}
	
	if ($ls_valor!='srh')
	{
	    print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title=Salir alt="Salir" width="20" height="20" border="0"></a></div></td>' );	   
	}
	else
	{
	 print ('<td class="toolbar" width="25"><div align="center"><a href="javascript: close();"><img src="../shared/imagebank/tools20/salir.gif" title=Salir alt="Salir" width="20" height="20" border="0"></a></div></td>' );	
	}
	
  ?>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_enviarcorreo();"></a><a href="javascript: ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" title="Excel" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_openexcel();"></a></div></td>
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
<table width="607" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="605" height="136">
      
      <table width="484" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="3" class="titulo-ventana">Deposito de la Garantía de las Prestaciones Sociales por persona</td>
        </tr>
        <tr>
          <td height="20" colspan="3" class="titulo-celdanew">Personal </td>
          </tr>
        <tr>
          <td width="107" height="22"><div align="right">C&oacute;digo</div></td>
          <td width="343">
            <div align="left">
              <input name="txtcodperdes" type="text" id="txtcodperdes" size="15" maxlength="10" value="" readonly>
            <a href="javascript: ue_buscarpersonal();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
			<input name="txtnomperdes" type="text" id="txtnomperdes" class="sin-borde" size="35"  value="" readonly>
            </div> </td>
          <td width="32">&nbsp;</td>
          </tr>
		 <tr>
          <td height="22"><div align="right">Per&iacute;odos </div></td>
          <td>
		  	<div align="left">
		  	  <input name="txtanocurper" type="text" id="txtanocurper" value="<?php print $li_anocurper."-".$ls_mescurper;?>" size="15" maxlength="10" readonly>
		  	  <a href="javascript: ue_buscarmeses();"><img id="meses" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0" ></a>
		  	  <input name="txtdesmesper" type="text" class="sin-borde" id="txtdesmesper" value="<?php print $ls_desmesper;?>" size="30" maxlength="20" readonly>
	  	      </div></td>
          </tr>
      </table>
      <input name="txtestatus" type="hidden" id="txtestatus" value="">
	  <input name="txtfecegr" type="hidden" id="txtfecegr" value="" >
	  <input name="txtanoact" type="hidden" id="txtanoact" value="<?php print $ld_ano;?>" >
	  </td>
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
		codper=f.txtcodperdes.value;
		periodo=f.txtanocurper.value;
		if ((codper!="")&&(periodo!=""))
		{   
			pagina="reportes/sigesp_snorh_rpp_depositograrantias_lott.php?codper="+codper+"&periodo="+periodo;					
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");						
		}
		else
		{
			alert("Debe seleccionar al personal");
		}	
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}			
}

function ue_openexcel()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		codper=f.txtcodperdes.value;
		periodo=f.txtanocurper.value;
		if ((codper!="")&&(periodo!=""))
		{   
			pagina="reportes/sigesp_snorh_rpp_depositograrantias_lott_excel.php?codper="+codper+"&periodo="+periodo;					
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");						
		}
		else
		{
			alert("Debe seleccionar al personal");
		}	
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}

function ue_buscarpersonal()
{
	f=document.form1;
	window.open("sigesp_snorh_cat_personal.php?&tipo=repconttrabivssdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarmeses()
{
	f=document.form1;
	codper=f.txtcodperdes.value;
	if (codper=="")
	{
		alert("Debe seleccionar un personal!");
	}
	else
	{
		window.open("sigesp_sno_cat_hmes.php?tipo=garantia_seguro","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
	}
}

function ue_enviarcorreo()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		codper=f.txtcodperdes.value;
		periodo=f.txtanocurper.value;
		if ((codper!="")&&(periodo!=""))
		{   
			pagina="reportes/sigesp_snorh_rpp_depositograrantias_lott_correo.php?codper="+codper+"&periodo="+periodo;					
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");						
		}
		else
		{
			alert("Debe seleccionar al personal");
		}	
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}
</script> 
</html>