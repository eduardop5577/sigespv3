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
	$io_fun_scf=new class_funciones_scf("../");
	$arrResultado = $io_fun_scf->uf_load_seguridad("SCF","sigesp_scf_r_hoja_trabajo.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();
	
	//$ld_fecdes="01/01/".substr($_SESSION["la_empresa"]["periodo"],0,4);
	
	$fecha=date("d/m/Y");
	$mes=substr($fecha,3,2);
	$ano=substr($fecha,6,4);
	$ld_fecdes= "01/".$mes."/".$ano;
	//$ld_fecdes= "01/01/".$ano;
	$ls_last_day=$io_fecha->uf_last_day($mes,$ano);	
	$ld_fechas=$ls_last_day;
	//$lb_valido=$io_fun_scf->uf_convertir_scgsaldos($la_seguridad);
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
<title>Reporte Hoja de Trabajo</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_scf.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="css/scf.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu">
	<table width="777" border="0" bgcolor="#E7E7E7" align="center" cellpadding="0" cellspacing="0">
		
	  <td width="405" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema
		de Contabilidad Fiscal</td>
		<td width="366" bgcolor="#E7E7E7"><div align="right"><span class="letras-peque�as"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <!--<td class="toolbar" width="25"><div align="center"><a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Imprimir" width="20" height="20" border="0"></a></div></td>-->
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
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scf->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scf);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
  <table width="650" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td height="136"><p>&nbsp;</p>
          <table width="600" height="22" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
            <tr>
              <td width="98"></td>
            </tr>
            <tr class="titulo-ventana">
              <td height="22" colspan="4" align="center">Hoja de Trabajo </td>
            </tr>
            <tr>
              <td height="15" colspan="4" align="center">&nbsp;</td>
            </tr>
            <tr style="display:none">
              <td height="13" align="center"><div align="right">Reporte en </div></td>
              <td height="22" colspan="3" align="center"><div align="left">
                  <select name="cmbbsf" id="cmbbsf">
                    <option value="0" selected>Bs.</option>
                    <option value="1">Bs.F.</option>
                  </select>
              </div></td>
            </tr>
            <tr class="titulo-celdanew">
             <!-- <td height="22" colspan="4" align="center">Cuentas Contables </td>-->
            </tr>
            <tr style="display:none">
              <td height="22" align="center"><div align="right">Desde</div></td>
              <td align="center"><div align="left">
                  <input name="txtcuentadesde" type="text" id="txtcuentadesde" size="22">
              <a href="javascript:ue_buscarcuenta('REPDES')"><img src="../shared/imagebank/tools15/buscar.gif" alt="Catalogo Cuentas" width="15" height="15" border="0"></a></div></td>
              <td align="center"><div align="right">Hasta</div></td>
              <td align="center"><div align="left">
                  <input name="txtcuentahasta" type="text" id="txtcuentahasta" size="22">
              <a href="javascript:ue_buscarcuenta('REPHAS')"><img src="../shared/imagebank/tools15/buscar.gif" alt="Catalogo Cuentas" width="15" height="15" border="0"></a></div></td>
            </tr>
            <tr class="titulo-celdanew">
              <td height="22" colspan="4" align="center">Per&iacute;odo</td>
            </tr>
            <tr>
              <td height="22" align="center"><div align="right">Desde</div></td>
              <td height="22" align="center"><div align="left">
                  <input name="txtfecdes" type="text" id="txtfecdes"  onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print  $ld_fecdes; ?>" size="22" maxlength="10"  datepicker="true">
              </div></td>
              <td height="22" align="center"><div align="right">Hasta</div></td>
              <td height="22" align="center"><div align="left">
                  <input name="txtfechas" type="text" id="txtfechas"  onKeyPress="javascript: ue_formatofecha(this,'/',patron,true);" value="<?php print  $ld_fechas; ?>" size="22" maxlength="10"  datepicker="true">
              </div></td>
            </tr>
            <tr class="titulo-celdanew">
              <td height="22" colspan="4" align="center">&nbsp;</td>
            </tr>
            <tr>
              <td height="22" align="center"><div align="right">Nivel</div>
                  <div align="left"></div></td>
              <td width="152" align="center"><div align="left">
                <select name="nivel" id="nivel">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                  <option value="7">7</option>
                </select>
              </div></td>
              <td width="85" align="center">&nbsp;</td>
              <td width="198" align="center">&nbsp;</td>
            </tr>
            <tr>
              <td height="22" colspan="4" align="center"></td>
            </tr>
          </table>
        <p>&nbsp;</p></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  </table>
  </p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_search()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	valido=true;
	if(li_imprimir==1)
	{	
		fecdes=f.txtfecdes.value;
		fechas=f.txtfechas.value;
		cuentadesde=f.txtcuentadesde.value;
		cuentahasta=f.txtcuentahasta.value;
		nivel=f.nivel.value;
		tiporeporte=f.cmbbsf.value;
		if((fecdes=="")||(fechas==""))
		{
			alert("Debe colocar un rango de fechas.");
			valido=false;
		}
		if(cuentadesde>cuentahasta)
		{
			alert("Intervalo de cuentas incorrecto.");
			valido=false;
		}
		if(!ue_comparar_fechas(fecdes,fechas))
		{
			alert("Intervalo de fechas incorrecto.");
			valido=false;
		}
		if(valido)
		{
			pagina="reportes/sigesp_scf_rpp_hoja_trabajo.php?fecdes="+fecdes+"&fechas="+fechas;
			pagina=pagina+"&cuentadesde="+cuentadesde+"&cuentahasta="+cuentahasta+"&nivel="+nivel+"&tiporeporte="+tiporeporte;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operaci�n");
   	}		
}

function ue_openexcel()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		fecdes=f.txtfecdes.value;
		fechas=f.txtfechas.value;
		cuentadesde=f.txtcuentadesde.value;
		cuentahasta=f.txtcuentahasta.value;
		nivel=f.nivel.value;
		tiporeporte=f.cmbbsf.value;
		if((fecdes=="")||(fechas==""))
		{
			alert("Debe colocar un rango de fechas.");
			valido=false;
		}
		if(cuentadesde>cuentahasta)
		{
			alert("Intervalo de cuentas incorrecto.");
			valido=false;
		}
		if(!ue_comparar_fechas(fecdes,fechas))
		{
			alert("Intervalo de fechas incorrecto.");
			valido=false;
		}
		if(valido)
		{
			pagina="reportes/sigesp_scf_rpp_balance_comprobacion_excel.php?fecdes="+fecdes+"&fechas="+fechas;
			pagina=pagina+"&cuentadesde="+cuentadesde+"&cuentahasta="+cuentahasta+"&nivel="+nivel+"&tiporeporte="+tiporeporte;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operaci�n");
   	}		
}
   
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_buscarcuenta(tipo)
{
	window.open("sigesp_scf_cat_cuentasscg.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
}
//--------------------------------------------------------
//	Funci�n que verifica que la fecha 2 sea mayor que la fecha 1
//--------------------------------------------------------
function ue_comparar_fechas(fecha1,fecha2)
{
	vali=false;
	dia1 = fecha1.substr(0,2);
	mes1 = fecha1.substr(3,2);
	ano1 = fecha1.substr(6,4);
	dia2 = fecha2.substr(0,2);
	mes2 = fecha2.substr(3,2);
	ano2 = fecha2.substr(6,4);
	if (ano1 < ano2)
	{
		vali = true; 
	}
    else 
	{ 
    	if (ano1 == ano2)
	 	{ 
      		if (mes1 < mes2)
	  		{
	   			vali = true; 
	  		}
      		else 
	  		{ 
       			if (mes1 == mes2)
	   			{
 					if (dia1 <= dia2)
					{
		 				vali = true; 
					}
	   			}
      		} 
     	} 	
	}
	return vali;
}

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>