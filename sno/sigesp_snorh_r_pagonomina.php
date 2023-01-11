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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_pagonomina.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$ls_reporte=$io_sno->uf_select_config("SNR","REPORTE","PAGO_NOMINA","sigesp_snorh_rpp_pagonomina.php","C");
	$ls_reporte2=$io_sno->uf_select_config("SNR","REPORTE","PAGO_NOMINA_EXCEL","sigesp_snorh_rpp_pagonomina_excel.php","C");
	unset($io_sno);
	
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
<title >Reporte Pago de N&oacute;mina Consolidado</title>
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

<body >
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
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif" title='Imprimir' alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" title="Excel" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title='Ayuda' alt="Ayuda" width="20" height="20"></div></td>
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
<table width="600" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="550" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Reporte de Pago de N&oacute;mina Consolidado</td>
        </tr>
        <tr style="display:none">
          <td height="21"><div align="right">Reporte en
            
          </div></td>
          <td ><div align="left">
            <select name="cmbbsf" id="cmbbsf">
              <option value="0" selected>Bs.</option>
              <option value="1">Bs.F.</option>
            </select>        
          </div>
          <td>        
          <td>        </tr>
        <tr>
          <td height="30"><div align="right">N&oacute;mina </div></td>
          <td width="171"><div align="left">
            <input name="txtcodnomdes" type="text" id="txtcodnomdes" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarnominadesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>          </div>
          <td width="111">
          <td width="151"><tr>
          <td height="20"><div align="right"> Subn&oacute;mina Desde </div></td>
          <td height="20"><input name="txtcodsubnomdes" type="text" id="txtcodsubnomdes" size="13" maxlength="10" value="" readonly>
            <a href="javascript: ue_buscarsubnominadesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></td>
          <td height="20"><div align="right">Subn&oacute;mina Hasta </div></td>
          <td height="20"><input name="txtcodsubnomhas" type="text" id="txtcodsubnomhas" value="" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarsubnominahasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Periodo Desde </div></td>
          <td><div align="left">
            <input name="txtperdes" type="text" id="txtperdes" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarperiododesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" name="periodo" width="15" height="15" border="0" id="periodo"></a>          
            <input name="txtfecdesper" type="hidden" id="txtfecdesper">
          </div>
          <td><div align="right">Periodo Hasta          </div>
          <td><div align="left">
            <input name="txtperhas" type="text" id="txtperhas" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarperiodohasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" name="periodo" width="15" height="15" border="0" id="periodo"></a>
            <input name="txtfechasper" type="hidden" id="txtfechasper">
          </div>        </tr>
        <tr>
          <td height="22"><div align="right">Ubicaci&oacute;n F&iacute;sica</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodubifis" type="text" id="txtcodubifis" size="7" maxlength="4" readonly>
            <a href="javascript: ue_buscarubicacionfisica();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesubifis" type="text" class="sin-borde" id="txtdesubifis" size="60" maxlength="100" readonly>
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Estado</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodest" type="text" id="txcodest" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarestado();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesest" type="text" class="sin-borde" id="txtdesest" value="" size="60" maxlength="50" readonly>
            <input name="txtcodpai" type="hidden" id="txtcodpai" value="058">
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Municipio</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodmun" type="text" id="txtcodmun" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarmunicipio();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesmun" type="text" class="sin-borde" id="txtdesmun" value="" size="60" maxlength="50" readonly>
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Parroquia</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodpar" type="text" id="txtcodpar" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarparroquia();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdespar" type="text" class="sin-borde" id="txtdespar" value="" size="60" maxlength="50" readonly>
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Quitar conceptos en cero </div></td>
          <td><div align="left">
            <input name="chkconceptocero" type="checkbox" class="sin-borde" id="chkconceptocero" value="1" checked>
          </div></td>
          <td><div align="right">Usar T&iacute;tulo de Concepto</div></td>
          <td><div align="left">
            <input name="chktituloconcepto" type="checkbox" class="sin-borde" id="chktituloconcepto" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Incluir Conceptos Reporte </div></td>
          <td><div align="left">
            <input name="chkconceptoreporte" type="checkbox" class="sin-borde" id="chkconceptoreporte" value="1">
          </div></td>
          <td><div align="right">Incluir Conceptos P2 </div></td>
          <td><div align="left">
            <input name="chkconceptop2" type="checkbox" class="sin-borde" id="chkconceptop2" value="1">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Pago Por Banco </div></td>
          <td><input name="chkpagbanper" type="checkbox" class="sin-borde" id="chkpagbanper" value="1" onClick="javascript: ue_activarpagos('BANCO');" checked></td>
          <td><div align="right">Pago Por cheque </div></td>
          <td><input name="chkpagcheper" type="checkbox" class="sin-borde" id="chkpagcheper" value="1" onClick="javascript: ue_activarpagos('CHEQUE');"checked></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Incluir Unidad de Constante</div></td>
          <td><input name="chkincunicon" type="checkbox" class="sin-borde" id="chkincunicon" value="1"></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Unidad Administrativa </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo del Personal </div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Apellido del Personal</div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Personal</div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="4">
            <input name="reporte" type="hidden" id="reporte" value="<?php print $ls_reporte;?>">
            <input name="reporte2" type="hidden" id="reporte2" value="<?php print $ls_reporte2;?>">
          </div></td>
        </tr>
		<tr>
          <td height="22"><div align="right">Ubicaci&oacute;n F&iacute;sica</div></td>
          <td colspan="3"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="5">
          </div></td>
        </tr>
        <tr>
          <td height="22"></td>
          <td colspan="3"> <div align="right"></div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
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
                reporte=f.reporte.value;			
                tiporeporte=f.cmbbsf.value;
                codnomdes=f.txtcodnomdes.value;
                codnomhas=f.txtcodnomdes.value;                
                perdes=f.txtperdes.value;
                perhas=f.txtperhas.value;                
                subnomdes=f.txtcodsubnomdes.value;
                subnomhas=f.txtcodsubnomhas.value;
                conceptocero="";
                tituloconcepto="";
                conceptoreporte="";
                conceptop2="";
                pagobanco=0;
                pagocheque=0;
                incunicon=0;
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
                if(f.rdborden[4].checked)
                {
                        orden="5";
                }
                if(f.chkconceptocero.checked)
                {
                        conceptocero=1;
                }
                if(f.chktituloconcepto.checked)
                {
                        tituloconcepto=1;
                }
                if(f.chkconceptoreporte.checked)
                {
                        conceptoreporte=1;
                }
                if(f.chkconceptop2.checked)
                {
                        conceptop2=1;
                }
                if(f.chkpagbanper.checked)
                {
                        pagobanco=1;
                }
                if(f.chkpagcheper.checked)
                {
                        pagocheque=1;
                }
                if(f.chkincunicon.checked)
                {
                        incunicon=1;
                }

                codubifis=f.txtcodubifis.value;
                codpai=f.txtcodpai.value;
                codest=f.txtcodest.value;
                codmun=f.txtcodmun.value;
                codpar=f.txtcodpar.value;
                pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&orden="+orden;
                pagina=pagina+"&conceptocero="+conceptocero+"&tituloconcepto="+tituloconcepto+"&conceptoreporte="+conceptoreporte;
                pagina=pagina+"&conceptop2="+conceptop2+"&tiporeporte="+tiporeporte+"&codubifis="+codubifis+"&codpai="+codpai;
                pagina=pagina+"&codest="+codest+"&codmun="+codmun+"&codpar="+codpar+"&subnomdes="+subnomdes+"&subnomhas="+subnomhas;
                pagina=pagina+"&perdes="+perdes+"&perhas="+perhas+"&pagobanco="+pagobanco;
                pagina=pagina+"&pagocheque="+pagocheque+"&incunicon="+incunicon;
                window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_openexcel()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
                reporte=f.reporte2.value;			
                tiporeporte=f.cmbbsf.value;
                codnomdes=f.txtcodnomdes.value;
                codnomhas=f.txtcodnomdes.value;
                perdes=f.txtperdes.value;
                perhas=f.txtperhas.value;                
                subnomdes=f.txtcodsubnomdes.value;
                subnomhas=f.txtcodsubnomhas.value;
                conceptocero="";
                tituloconcepto="";
                conceptoreporte="";
                conceptop2="";
                pagobanco=0;
                pagocheque=0;
                incunicon=0;
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
                if(f.rdborden[4].checked)
                {
                        orden="5";
                }
                if(f.chkconceptocero.checked)
                {
                        conceptocero=1;
                }
                if(f.chktituloconcepto.checked)
                {
                        tituloconcepto=1;
                }
                if(f.chkconceptoreporte.checked)
                {
                        conceptoreporte=1;
                }
                if(f.chkconceptop2.checked)
                {
                        conceptop2=1;
                }
                if(f.chkpagbanper.checked)
                {
                        pagobanco=1;
                }
                if(f.chkpagcheper.checked)
                {
                        pagocheque=1;
                }
                if(f.chkincunicon.checked)
                {
                        incunicon=1;
                }

                codubifis=f.txtcodubifis.value;
                codpai=f.txtcodpai.value;
                codest=f.txtcodest.value;
                codmun=f.txtcodmun.value;
                codpar=f.txtcodpar.value;
                pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&orden="+orden;
                pagina=pagina+"&conceptocero="+conceptocero+"&tituloconcepto="+tituloconcepto+"&conceptoreporte="+conceptoreporte;
                pagina=pagina+"&conceptop2="+conceptop2+"&tiporeporte="+tiporeporte+"&codubifis="+codubifis+"&codpai="+codpai;
                pagina=pagina+"&codest="+codest+"&codmun="+codmun+"&codpar="+codpar+"&subnomdes="+subnomdes+"&subnomhas="+subnomhas;
                pagina=pagina+"&perdes="+perdes+"&perhas="+perhas+"&pagobanco="+pagobanco;
                pagina=pagina+"&pagocheque="+pagocheque+"&incunicon="+incunicon;
                window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_activarpagos(tipo)
{
	f=document.form1;
	switch(tipo)
	{
		case 'CHEQUE':
			if((f.chkpagbanper.checked==false)&&(f.chkpagcheper.checked==false))
			{
				alert('Debe estar Seleccionada al menos una opción de pago.');
				f.chkpagcheper.checked=true;
			}	
			break;
		
		case 'BANCO':
			if((f.chkpagbanper.checked==false)&&(f.chkpagcheper.checked==false))
			{
				alert('Debe estar Seleccionada al menos una opción de pago.');
				f.chkpagbanper.checked=true;
			}	
			break;
	}
}

function ue_buscarnominadesde()
{
	f=document.form1;
	window.open("sigesp_snorh_cat_nomina.php?tipo=replisbandes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarnominahasta()
{
	f=document.form1;	
	if(f.txtcodnomdes.value!="")
	{
		window.open("sigesp_snorh_cat_nomina.php?tipo=replisbanhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una nómina desde.");
	}
}
function ue_buscarperiododesde()
{
	f=document.form1;
	codnomdes=f.txtcodnomdes.value;
	codnomhas=f.txtcodnomdes.value;
	if((codnomdes!="")&&(codnomhas!=""))
	{
		window.open("sigesp_sno_cat_hperiodo.php?tipo=replisbandes&codnom="+codnomdes+"&codnomhas="+codnomhas+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un rango de nóminas.");
	}
}

function ue_buscarperiodohasta()
{
	f=document.form1;
	codnomdes=f.txtcodnomdes.value;
	codnomhas=f.txtcodnomdes.value;
	if((codnomdes!="")&&(codnomhas!="")&&(f.txtperdes.value!=""))
	{
		window.open("sigesp_sno_cat_hperiodo.php?tipo=replisbanhas&codnom="+codnomdes+"&codnomhas="+codnomhas+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un rango de nóminas y aun período desde.");
	}
}
function ue_buscarsubnominadesde()
{
	f=document.form1;
	codnomdes=f.txtcodnomdes.value;
	codnomhas=f.txtcodnomdes.value;
	if((codnomdes==codnomhas)&&(codnomdes!=""))
	{
		window.open("sigesp_snorh_cat_subnomina.php?tipo=reportedesde&codnom="+codnomdes,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Para filtrar por Subnóminas La nómina desde y hasta debe ser la misma.");
	}
}

function ue_buscarsubnominahasta()
{
	f=document.form1;
	codsubnomdes=f.txtcodsubnomdes.value;
	codnomdes=f.txtcodnomdes.value;
	if(codsubnomdes!="")
	{
		window.open("sigesp_snorh_cat_subnomina.php?tipo=reportehasta&codnom="+codnomdes,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una subnómina desde.");
	}
}

function ue_buscarubicacionfisica()
{
	window.open("sigesp_snorh_cat_ubicacionfisica.php?tipo=pagonomina","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarestado()
{
	f=document.form1;
	f.txtcodubifis.value="";
    f.txtdesubifis.value="";
	codpai=ue_validarvacio(f.txtcodpai.value);
	if(codpai!="")
	{
		window.open("sigesp_snorh_cat_estado.php?codpai="+codpai+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais.");
	}
}

function ue_buscarmunicipio()
{
	f=document.form1;
	codpai=ue_validarvacio(f.txtcodpai.value);
	codest=ue_validarvacio(f.txtcodest.value);
	if((codpai!="")||(codest!=""))
	{
		window.open("sigesp_snorh_cat_municipio.php?codpai="+codpai+"&codest="+codest+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais y un estado.");
	}
}

function ue_buscarparroquia()
{
	f=document.form1;
	codpai=ue_validarvacio(f.txtcodpai.value);
	codest=ue_validarvacio(f.txtcodest.value);
	codmun=ue_validarvacio(f.txtcodmun.value);
	if((codpai!="")||(codest!="")||(codmun!=""))
	{
		window.open("sigesp_snorh_cat_parroquia.php?codpai="+codpai+"&codest="+codest+"&codmun="+codmun+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais, un estado y un municipio.");
	}
}
</script> 
</html>