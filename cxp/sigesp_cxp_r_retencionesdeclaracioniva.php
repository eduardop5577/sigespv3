<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
	$ls_anoant=(date('Y')-1);
	$ls_ano=date('Y');
	$ls_mes=date('m');
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$arrResultado=$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_r_retencionesdeclaracioniva.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado); 
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$ls_ruta="declaracioninformativa";
	@mkdir($ls_ruta,0755);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Declaraci&oacute;n Informativa de Retenciones IVA</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_sep.js"></script>

<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script  src="js/funcion_cxp.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>
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
<?php
	$ls_operacion=$io_fun_cxp->uf_obteneroperacion();
	require_once("reportes/sigesp_cxp_class_report.php");
	$io_reporte=new sigesp_cxp_class_report("../");
	switch ($ls_operacion) 
	{
		case "GENDISK":
			$ls_fecemides=$_POST["txtfecemides"];
			$ls_fecemihas=$_POST["txtfecemihas"];
//			$ls_mes=$_POST["cmbmes"];
//			$ls_anio=$_POST["txtano"];
//			$ls_quincena=$_POST["cmbquincena"];
			$lb_valido=$io_reporte->uf_declaracioninformativa($ls_fecemides,$ls_fecemihas,"",$la_seguridad);
			if($lb_valido)
			{
				$io_reporte->io_mensajes->message("El txt fu� generado");
			}
			else
			{
				$io_reporte->io_mensajes->message("Ocurrio un error al generar el txt");
			}
			break;
	}
	unset($io_reporte);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="806" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="773" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			<td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </table>    </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_gendisk();"><img src="../shared/imagebank/tools20/gendisk.jpg" alt="Generar" width="21" height="20" border="0" title="Generar Archivo"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar();"><img src="../shared/imagebank/tools20/download.gif" alt="" width="20" height="20" border="0" title="Descargar"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Excel" title="Excel" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
</div> 
<p>&nbsp;	</p>
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cxp->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cxp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="142"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Declaraci&oacute;n Informativa de Retenciones de IVA </td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center"><table width="511" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
        <tr>
          <td height="22" colspan="5"><strong>Fecha de Periodo </strong></td>
        </tr>
        <tr>
          <td width="136"><div align="right">Desde</div></td>
          <td width="101"><input name="txtfecemides" type="text" id="txtfecemides"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true"></td>
          <td width="42"><div align="right">Hasta</div></td>
          <td width="129"><div align="left">
              <input name="txtfecemihas" type="text" id="txtfecemihas"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" size="15" maxlength="10"  datepicker="true">
          </div></td>
          <td width="101">&nbsp;</td>
        </tr>
      </table></td>
    </tr>
    <tr  style="visibility:hidden">
      <td height="22" align="center"><div align="right">Mes</div></td>
      <td width="208" height="22" align="center"><div align="left">
        <select name="cmbmes" id="cmbmes">
          <option value="01" <?php if($ls_mes=="01"){ print "selected";} ?>>ENERO</option>
          <option value="02" <?php if($ls_mes=="02"){ print "selected";} ?>>FEBRERO</option>
          <option value="03" <?php if($ls_mes=="03"){ print "selected";} ?>>MARZO</option>
          <option value="04" <?php if($ls_mes=="04"){ print "selected";} ?>>ABRIL</option>
          <option value="05" <?php if($ls_mes=="05"){ print "selected";} ?>>MAYO</option>
          <option value="06" <?php if($ls_mes=="06"){ print "selected";} ?>>JUNIO</option>
          <option value="07" <?php if($ls_mes=="07"){ print "selected";} ?>>JULIO</option>
          <option value="08" <?php if($ls_mes=="08"){ print "selected";} ?>>AGOSTO</option>
          <option value="09" <?php if($ls_mes=="09"){ print "selected";} ?>>SEPTIEMBRE</option>
          <option value="10" <?php if($ls_mes=="10"){ print "selected";} ?>>OCTUBRE</option>
          <option value="11" <?php if($ls_mes=="11"){ print "selected";} ?>>NOVIEMBRE</option>
          <option value="12" <?php if($ls_mes=="12"){ print "selected";} ?>>DICIEMBRE</option>
        </select>
</div></td>
      <td width="66" height="22" align="center"><div align="right">A&ntilde;o</div></td>
      <td width="182" align="center"><div align="left">
         <select name="txtano" id="txtano">
          <option value="<?php print $ls_ano ?>" ><?php print $ls_ano ?></option>
          <option value="<?php print $ls_anoant ?>" ><?php print $ls_anoant ?></option>
        </select>
</div></td>
    </tr>
    <tr  style="visibility:hidden">
      <td height="22" align="center"><div align="right">Quincena</div></td>
      <td height="22" align="center">
        <div align="left">
          <select name="cmbquincena" id="cmbquincena">
            <option value="1" selected>Primera Quincena</option>
            <option value="2" >Segunda Quincena</option>
          </select>
          </div>     </td>
      <td height="22" align="center">&nbsp;</td>
      <td height="22" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="4" align="center"><input name="operacion" type="hidden"  id="operacion">
 									<input name="basdatcmp" type="hidden" id="basdatcmp" value="<?php print $_SESSION["la_empresa"]["basdatcmp"]; ?>">  		</td>
    </tr>
  </table>
</form>      
</body>
<script language="JavaScript">
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)

function ue_gendisk()
{
	f=document.formulario;
	basdatcmp=f.basdatcmp.value;
	fecemides=f.txtfecemides.value;
	fecemihas=f.txtfecemihas.value;
	if((fecemides=="")||(fecemihas==""))
	{
			alert("Debe completar los datos");
	}
	else
	{
		f.operacion.value="GENDISK";
		f.action="sigesp_cxp_r_retencionesdeclaracioniva.php";
		f.submit();	  
	}
}

function ue_descargar()
{
	f=document.formulario;
	basdatcmp=f.basdatcmp.value;
	if(basdatcmp=="")
	{
		pagina="sigesp_cxp_cat_directorio.php?ruta=declaracioninformativa";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no,left=50,top=50");  
	}
	else
	{
		alert("La declaraci�n debe generarse desde la Base de Datos integradora");
	}
}
	function ue_openexcel()
	{
			f=document.formulario;
			li_imprimir=f.imprimir.value;
			intervalo=0;
			if(li_imprimir==1)
			{
//				mes=f.cmbmes.value;
//				anio=f.txtano.value;
//				quincena=f.cmbquincena.value;
				fecemides=f.txtfecemides.value;
				fecemihas=f.txtfecemihas.value;
				if((fecemides=="")||(fecemihas==""))
				{
						alert("Debe completar los datos");
				}
				else
				{
					pantalla="reportes/sigesp_cxp_rpp_declaracioniva_excel.php?fecemides="+fecemides+"&fecemihas="+fecemihas;
					window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}

			}
			else
			{alert("No tiene permiso para realizar esta operaci�n");}
	}
   
function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_cxp_rep_retencionesdeclaracioniva.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}
</script>
</html>