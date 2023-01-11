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
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_metodo_tarjeta_prepagada.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	unset($io_sno);
	$ls_ruta="txt/general/";
	@mkdir($ls_ruta,0755);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><script type="text/javascript"  src="../shared/js/disabled_keys.js"></script><script >
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
<title >Reporte Tarjeta Prepagada</title>
<meta http-equiv="imagetoolbar" content="no"><style type="text/css">
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
</style><script type="text/javascript"  src="js/stm31.js"></script><script type="text/javascript"  src="js/funcion_nomina.js"></script><script type="text/javascript"  src="../shared/js/validaciones.js"></script><script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
	require_once("sigesp_sno_c_metodo_banco.php");
	$io_metodobanco=new sigesp_sno_c_metodo_banco();
	$ls_codconc="";
	switch ($ls_operacion) 
	{
		case "GENDISK":			
					
			$ls_desmet="VENEZUELA TARJETAS PREPAGADAS";
			$ls_tipper=$_POST["txtcodtipper"];
			$ld_fecpro=$_POST["txtfecpro"];
			$ls_dentippersss=$_POST["txtdestippersss"];
			//$ls_ref=$_POST["txtref"];
			$rs_data="";
			$arrResultado=$io_metodobanco->uf_listadobanco_gendisk_tarjeta_prepagada($ls_tipper, $rs_data);
			$rs_data=$arrResultado['rs_data'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$lb_valido=$io_metodobanco->uf_metodo_banco($ls_ruta,$ls_desmet,$ls_tipper,$ld_fecpro,$ld_fhasta,$ld_fecproc,$adec_montot,$as_codcueban,$rs_data,$as_codmetban,$as_desope,$as_quincena,$as_ref,$aa_seguridad);
			}
			break;
			
		default:
			$ls_codmet="";
			$ls_desmet="";
			$ls_codban="";
			$ls_nomban="";
			$ls_codcue="";
			$ld_fecpro="";
			$ls_sc_cuenta="";
			$ls_ctaban="";
			$ls_pagtaqnom="0";
			$ls_codconc="";
			break;
	}
	unset($io_metodobanco);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="10" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="10" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nomina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>	 </td>
  </tr>
  <tr>
    <td height="20" colspan="10" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="10" class="toolbar"></td>
  </tr>
  <tr>
    <td width="25" height="20" class="toolbar"><div align="center"><a href="javascript: ue_gendisk();"><img src="../shared/imagebank/tools20/gendisk.jpg" title="Generar" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar('<?php print $ls_ruta;?>');"><img src="../shared/imagebank/tools20/download.gif" title="Descargar" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
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
<table width="650" height="138" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <td height="136">
      <p>&nbsp;</p>
      <table width="600" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventfonzana">Generacion de Txt para la Emision de Tarjetas Prepagadas </td>
        </tr>
         <tr>
          <td height="20" colspan="4" class="titulo-celdanew">Personal</td>
          </tr>
        <tr>
          <td colspan="1" height="20"><div align="right"> Tipo de Personal </div></td>
          <td colspan="3">
            <div align="left">
              <input name="txtcodtipper" type="text" id="txtcodtipper" size="13" maxlength="10" value="<?php print $ls_tipper;?>" readonly> 
              <a href="javascript:ue_buscartipopersonal();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> 
              <input name="txtdestippersss" type="text" class="sin-borde" id="txtdestippersss" value="<?php print $ls_dentippersss;?>" size="60" maxlength="50" readonly>
            </div></td>
          </tr>
        <tr>
                    <td colspan="1" ><div align="right">Fecha de Procesamiento </div></td>
          <td colspan="2"><div align="left">
            <input name="txtfecpro" type="text" id="txtfecpro" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10" value="<?php print $ld_fecpro;?>" datepicker="true">
          </div>
        </tr>
	<tr>
            <input name="operacion" type="hidden" id="operacion">
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body><script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_gendisk()
{
	f=document.form1;
	li_procesar=f.ejecutar.value;
	if(li_procesar==1)
	{	
		tipper=f.txtcodtipper.value;
		fecpro=f.txtfecpro.value;
		if((tipper!="")&&(fecpro!=""))
		{
			f.operacion.value="GENDISK";
			f.action="sigesp_snorh_r_metodo_tarjeta_prepagada.php";
			f.submit();
		}
		else
		{
			alert("Debe colocar toda la informacion.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_descargar(ruta)
{
	window.open("sigesp_sno_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscartipopersonal()
{
	window.open("sigesp_snorh_cat_tipopersonalsss.php?tipo=tarjeta","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}
</script> 
</html>