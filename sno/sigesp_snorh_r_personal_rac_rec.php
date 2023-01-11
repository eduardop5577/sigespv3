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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_personal_rac_rec.php",$ls_permisos,$la_seguridad,$la_permisos);
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
<title >Reporte Listado de Personal RAC - REC</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de N�mina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
    </table>	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif"  title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a></div></td>    
	<td class="toolbar" width="26"><div align="center"><a href="javascript: ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" title="Excel" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="20"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="23"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif"  title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="351"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="68"><div align="center"></div></td>
    <td class="toolbar" width="3">&nbsp;</td>
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
<table width="650" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="600" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Reporte Listado de Personal RAC - REC </td>
        </tr>
        
        
         <tr>
          <td width="158" height="22"><div align="right">N&oacute;mina Desde </div></td>
          <td><div align="left">
            <input name="txtcodnomdes" type="text" id="txtcodnomdes" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarnominadesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">N&oacute;mina Hasta </div></td>
          <td><div align="left">
            <input name="txtcodnomhas" type="text" id="txtcodnomhas" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarnominahasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
       
        <tr>
          <td height="22"><div align="right">Mes</div></td>
          <td colspan="3"><div align="left">
            <input name="txtanocurper" type="text" id="txtanocurper" size="7" maxlength="4" readonly>
            <input name="txtmescurper" type="text" id="txtmescurper" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarmeses();"><img id="meses" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesmesper" type="text" class="sin-borde" id="txtdesmesper" value="" size="30" maxlength="20" readonly>
            <input name="txtcodperi" type="hidden" id="txtcodperi">
          </div></td>
        </tr>
       
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew">Rango del Personal </td>
          </tr>
		<tr>
          <td width="158" height="22"><div align="right"> Desde </div></td>
          <td width="135"><div align="left">
            <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10" value="" readonly>
            <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="124"><div align="right">Hasta </div></td>
          <td colspan="2"><div align="left">
            <input name="txtcodperhas" type="text" id="txtcodperhas" value="" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
		<tr>
		  <td height="22" colspan="5" class="titulo-celdanew">&nbsp;</td>
		  </tr>
		<tr>
		  <td height="22"><div align="right">Compensaci&oacute;n</div></td>
		  <td colspan="4"><img src="../shared/imagebank/mas.gif" width="9" height="17" border="0"> <a href="javascript:uf_selectconcepto('txtcompensacion');"> Buscar Conceptos por lote</a>
            <input type="text" id="txtcompensacion" name="txtcompensacion"  size="50" readonly></td>
		  </tr>
		<tr>
          <td height="22" colspan="5" class="titulo-celdanew">Reporte Deseado  </td>
        </tr>
        <tr>
          <td colspan="4"><div align="center">
            <select name="cmdreporteexcel" id="cmdreporteexcel">
              <option value="sigesp_snorh_rpp_listadopersonal_excel_rac.php" selected>Listado Excel RAC</option>
              <option value="sigesp_snorh_rpp_listadopersonal_excel_rec.php">Listado Excel REC</option>
              <option value="sigesp_snorh_rpp_listadopersonal_excel_rap.php">Listado Excel RAP OBREROS</option>
              <option value="sigesp_snorh_rpp_listadopersonal_rac.php">Listado PDF RAC</option>
              <option value="sigesp_snorh_rpp_listadopersonal_rac_nomina.php">Listado PDF RAC N�mina</option>
            </select>
          </div></td>
        </tr>
        <tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo del Personal </div></td>
          <td width="135"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
          <td width="124"><div align="right">Apellido del Personal</div></td>
          <td width="173"><div align="left"><input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Personal</div></td>
          <td>            <div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
          </div></td>
          <td><div align="right">C&eacute;dula del Personal</div></td>
          <td><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="4">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right"></div></td>
          <td colspan="3"><div align="right">
            <input name="operacion" type="hidden" id="operacion">
            <input name="tipo" type="hidden" id="tipo" value="rac_rec">	
            <input name="campo" type="hidden" id="campo" value="">	
          </div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_buscarnominadesde()
{
	window.open("sigesp_snorh_cat_nomina.php?tipo=repperipsdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarnominahasta()
{
	f=document.form1;
	if(f.txtcodnomdes.value!="")
	{
		window.open("sigesp_snorh_cat_nomina.php?tipo=repperipshas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una n�mina desde.");
	}
}

function ue_buscarpersonaldesde()
{
	window.open("sigesp_snorh_cat_personal.php?tipo=replisperdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarpersonalhasta()
{
	f=document.form1;
	if(f.txtcodperdes.value!="")
	{
		window.open("sigesp_snorh_cat_personal.php?tipo=replisperhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un personal desde.");
	}
}

function ue_buscarmeses()
{
	f=document.form1;
	codnomdes=f.txtcodnomdes.value;
	codnomhas=f.txtcodnomhas.value;
	if((f.txtcodnomdes.value!="")&&(f.txtcodnomhas.value!=""))
	{
		window.open("sigesp_sno_cat_hmes.php?tipo=repperips&codnom="+codnomdes+"&codnomhas="+codnomhas+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
   	}
	else
   	{
		alert("Debe seleccionar una n�mina desde.");
   	}
}
function uf_selectconcepto(campo)
{   
	f=document.form1;
	codnomdes=f.txtcodnomdes.value;
	codnomhas=f.txtcodnomhas.value;
	f.campo.value=campo;
	if((codnomdes!="")&&(codnomhas!=""))
	{
		window.open("sigesp_snorh_sel_catconcepto.php?codnomdes="+codnomdes+"&codnomhas="+codnomhas,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un rango de n�minas y mes.");
	}
}

function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		reporte=f.cmdreporteexcel.value;
		if((reporte!="")&&((reporte=="sigesp_snorh_rpp_listadopersonal_rac.php")||(reporte=="sigesp_snorh_rpp_listadopersonal_rac_nomina.php")))
		{
			codnomdes=f.txtcodnomdes.value;
			codnomhas=f.txtcodnomhas.value;
			if((codnomdes!="")&&(codnomdes<=codnomhas))
			{
				codperdes=f.txtcodperdes.value;
				codperhas=f.txtcodperhas.value;
				compensacion=f.txtcompensacion.value;
				if(codperdes<=codperhas)
				{
					anio=f.txtanocurper.value;
					mes=f.txtmescurper.value;
					if(f.rdborden[0].checked)
					{
						orden="1";
					}
					if(f.rdborden[1].checked)
					{
						orden="2";
					}
					if(f.rdborden[2].checked)
					{
						orden="3";
					}
					if(f.rdborden[3].checked)
					{
						orden="4";
					}
					pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&codperdes="+codperdes+"&codperhas="+codperhas;
					pagina=pagina+"&anio="+anio+"&mes="+mes+"&orden="+orden+"&compensacion="+compensacion;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del personal est� erroneo");
				}
			}
			else
			{
				alert("El rango del n�mina est� erroneo");
			}
		}
		else
		{
			alert("El Reporte no est� desarrollado para esta opci�n");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operaci�n");
   	}		
}

function ue_openexcel()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		reporte=f.cmdreporteexcel.value;
		if((reporte!="")&&((reporte=="sigesp_snorh_rpp_listadopersonal_excel_rac.php")||(reporte=="sigesp_snorh_rpp_listadopersonal_excel_rap.php")||(reporte=="sigesp_snorh_rpp_listadopersonal_excel_rec.php")))
		{
			codnomdes=f.txtcodnomdes.value;
			codnomhas=f.txtcodnomhas.value;
			if((codnomdes!="")&&(codnomdes<=codnomhas))
			{
				codperdes=f.txtcodperdes.value;
				codperhas=f.txtcodperhas.value;
				if(codperdes<=codperhas)
				{
					anio=f.txtanocurper.value;
					mes=f.txtmescurper.value;
					codperi=f.txtcodperi.value;	
					if(f.rdborden[0].checked)
					{
						orden="1";
					}
					if(f.rdborden[1].checked)
					{
						orden="2";
					}
					if(f.rdborden[2].checked)
					{
						orden="3";
					}
					if(f.rdborden[3].checked)
					{
						orden="4";
					}
					pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&codperdes="+codperdes+"&codperhas="+codperhas;
					pagina=pagina+"&anio="+anio+"&mes="+mes+"&codperi="+codperi+"&orden="+orden;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del personal est� erroneo");
				}
			}
			else
			{
				alert("El rango del n�mina est� erroneo");
			}
		}
		else
		{
			alert("El Reporte no est� desarrollado para esta opci�n");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operaci�n");
   	}		
}
</script> 
</html>