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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_r_listadopersonal.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$ls_reportelistadopersonal=$io_sno->uf_select_config("SNR","REPORTE","LISTADO_PERSONAL","sigesp_snorh_rpp_listadopersonal.php","C");
	$ls_reportepermisopersonal=$io_sno->uf_select_config("SNR","REPORTE","LISTADO_PERSONAL_PERMISOS","sigesp_snorh_rpp_listadopermisos.php","C");
	$ls_reporteestudiospersonal=$io_sno->uf_select_config("SNR","REPORTE","LISTADO_PERSONAL_ESTUDIOS","sigesp_snorh_rpp_listadoestudios.php","C");
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
<title >Reporte Listado de Personal</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_print();"><img src="../shared/imagebank/tools20/imprimir.gif"  title="Imprimir" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" title="Excel" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
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
        
          <td height="20" colspan="5" class="titulo-ventana">Reporte Listado de Personal </td>
        </tr>
        <tr>
          <td height="20" colspan="6" class="titulo-celdanew">Intervalo de N&oacute;mina </td>
          </tr>
        <tr>
          <td width="141" height="22"><div align="right"> Desde </div></td>
          <td width="119"><div align="left">
            <input name="txtcodnomdes" type="text" id="txtcodnomdes" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarnominadesde();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="104"><div align="right">Hasta </div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodnomhas" type="text" id="txtcodnomhas" size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarnominahasta();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew">Intervalo de Personal </td>
          </tr>
        <tr>
          <td width="141" height="22"><div align="right"> Desde </div></td>
          <td width="119"><div align="left">
            <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10"  readonly>
            <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td width="104"><div align="right">Hasta </div></td>
          <td colspan="2"><div align="left">
            <input name="txtcodperhas" type="text" id="txtcodperhas"  size="13" maxlength="10" readonly>
            <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22" colspan="5" class="titulo-celdanew">Estatus del Personal en el Sistema </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Activo
                <input name="chkactivo" type="checkbox" class="sin-borde" id="chkactivo" value="1" checked>
          </div></td>
          <td><div align="right">Egresado
              <input name="chkegresado" type="checkbox" class="sin-borde" id="chkegresado" value="1">
          </div></td>
          <td colspan="3">
            <div align="left">
              <select name="cmbcauegrper" id="select">
                <option value="" selected>--Seleccione Uno--</option>
                <option value="N">Ninguno</option>
                <option value="D">Despido</option>
                <option value="P">Pensionado</option>
                <option value="R">Renuncia</option>
                <option value="T">Traslado</option>
                <option value="J">Jubilado</option>
                <option value="F">Fallecido</option>
              </select>
            </div></td>
          </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td>Masculino
            <label>
            <input name="chkmasculino" type="checkbox" class="sin-borde" id="chkmasculino" value="1" checked>
            </label></td>
          <td colspan="3">Femenino
            <label>
            <input name="chkfemenino" type="checkbox" class="sin-borde" id="chkfemenino" value="1" checked>
            </label></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Estado</div></td>
          <td colspan="4"><input name="txtcodestper" type="text" id="txtcodestper" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarestado('PERSONAL');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesestper" type="text" class="sin-borde" id="txtdesestper" value="" size="60" maxlength="50" readonly></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Municipio</div></td>
          <td colspan="4"><input name="txtcodmunper" type="text" id="txtcodmunper" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarmunicipio('PERSONAL');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesmunper" type="text" class="sin-borde" id="txtdesmunper" value="" size="60" maxlength="50" readonly></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Parroquia</div></td>
          <td colspan="4"><input name="txtcodparper" type="text" id="txtcodparper" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarparroquia('PERSONAL');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesparper" type="text" class="sin-borde" id="txtdesparper" value="" size="60" maxlength="50" readonly></td>
          </tr>
        <tr>
          <td height="22" colspan="5" class="titulo-celdanew">Estatus del Personal en N&oacute;mina </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Activo
            <input name="chkactivono" type="checkbox" class="sin-borde" id="chkactivono" value="1" checked>
          </div></td>
          <td><div align="right">Vacaciones
            <input name="chkvacacionesno" type="checkbox" class="sin-borde" id="chkvacacionesno" value="1" checked>
          </div></td>
          <td><div align="right">Egresado
            <input name="chkegresadono" type="checkbox" class="sin-borde" id="chkegresadono" value="1" checked>
          </div></td>
          <td width="129"><div align="right">Suspendido
            <input name="chksuspendidono" type="checkbox" class="sin-borde" id="chksuspendidono" value="1" checked>
          </div></td>
          <td width="45">&nbsp;</td>
        </tr>
        <tr>
        <td height="22"><div align="right">Unidad Administrativa </div></td>
        <td colspan="5"><div align="left">
          <input name="txtcoduniadm" type="text" id="txtcoduniadm"  size="19" maxlength="16" readonly>
    
          <a href="javascript: ue_buscarunidadadministrativa();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
          
          &nbsp;&nbsp;&nbsp;<a href="javascript: limpiar_tipo();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="15" height="15" border="0" title="Eliminar" /></a><input name="txtdesuniadm" type="text" class="sin-borde" id="txtdesuniadm"  size="50" maxlength="100" readonly>
        </div></td>
      </tr>
        <tr>
          <td height="22" colspan="5" class="titulo-celdanew">&nbsp;</td>
          </tr>
        <tr>
          <td height="22"><div align="right">Ubicaci&oacute;n F&iacute;sica </div></td>
          <td colspan="4"><div align="left">
            <input name="txtcodubifis" type="text" id="txtcodubifis" size="7" maxlength="4" readonly>
            <a href="javascript: ue_buscarubicacionfisica();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesubifis" type="text" class="sin-borde" id="txtdesubifis" size="60" maxlength="100" readonly>
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Estado</div></td>
          <td colspan="4"><div align="left">
            <input name="txtcodest" type="text" id="txcodest" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarestado('');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesest" type="text" class="sin-borde" id="txtdesest" value="" size="60" maxlength="50" readonly>
            <input name="txtcodpai" type="hidden" id="txtcodpai" value="058">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Municipio</div></td>
          <td colspan="4"><div align="left">
            <input name="txtcodmun" type="text" id="txtcodmun" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarmunicipio('');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdesmun" type="text" class="sin-borde" id="txtdesmun" value="" size="60" maxlength="50" readonly>
</div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">Parroquia</div></td>
          <td colspan="4"><div align="left">
            <input name="txtcodpar" type="text" id="txtcodpar" value="" size="6" maxlength="3" readonly>
            <a href="javascript: ue_buscarparroquia('');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdespar" type="text" class="sin-borde" id="txtdespar" value="" size="60" maxlength="50" readonly>
</div></td>
          </tr>
        <tr id='opcion1'>
          <td height="22" align="right">Intervalo de Fechas:</td>
          <td  colspan="4" valign="bottom"><input name="fec_desde" type="text" id="fec_desde" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" size="15" maxlength="10" datepicker="true">
            --
            <input name="fec_hasta" type="text" id="fec_hasta" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" size="15" maxlength="10" datepicker="true">
  &nbsp;&nbsp;
  <input type="radio" name="orden" id="radio11" value="feciniper"></td>
        </tr>
         <tr id='opcion2'>
            <td height="22"><div align="right">Tipo Permiso</div></td>
            <td colspan="4"><div align="left">
                <select name="cmbtipper" id="cmbtipper">
                  <option value="" selected>--Seleccione--</option>    
					<option value="1">Estudio</option>
					<option value="2">M&eacute;dico</option>
					<option value="3">Tr&aacute;mites</option>
					<option value="4">Otro</option>
					<option value="5">Reposo</option>
					<option value="6">Reposo Laboral</option>
					<option value="7">Ausencia</option>
					<option value="8">Permiso Sindical</option>
					<option value="9">Compensatorio</option>
                </select>
  &nbsp;&nbsp;
  <input type="radio" name="orden" id="radio" value="tipper">
            </div></td>
     	 </tr>
        <tr id='opcion3'>
           <td height="22">&nbsp;</td>
            <td colspan="4"><input name="chkforcon" type="checkbox" id="chkforcon" value="1">
            Imprimir Forma Continua </td>
      	 </tr>
       <tr>
           <td height="22">&nbsp;</td>
         </tr>		
        <tr>
          <td height="22" colspan="5" class="titulo-celdanew">Reportes Adicionales del Personal </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Reporte pdf </div></td>
          <td colspan="4">
            <select name="cmdreporte" id="cmdreporte" onChange="evaluar_opciones()">
              <option value="<?php print $ls_reportelistadopersonal; ?>" selected>Listado de Personal</option>
              <option value="<?php print $ls_reportepermisopersonal; ?>">Listado de Permisos</option>
              <option value="sigesp_snorh_rpp_listadotrabajosanteriores.php">Listado de Trabajos Anteriores</option>
              <option value="<?php print $ls_reporteestudiospersonal; ?>">Listado de Estudios Realizados</option>
			  <option value="sigesp_snorh_rpp_listapersonalobservacion.php">Listado de Observaciones en Ficha de Personal</option>
            </select>          </td>
          </tr>
        <tr>
          <td height="22"><div align="right">Reporte Excel </div></td>
          <td colspan="4"><select name="cmdreporteexcel" id="cmdreporteexcel" onChange="evaluar_opciones2()">
            <option value="sigesp_snorh_rpp_listadopersonal_excel.php" selected>Listado de Personal</option>
			<option value="sigesp_snorh_rpp_listadopermisos_excel.php">Listado de Permisos</option>
          </select></td>
        </tr>
        <tr>
          <td height="20" colspan="5" class="titulo-celdanew"><div align="right" class="titulo-celdanew">Ordenado por </div></td>
          </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo del Personal </div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="1" checked>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Apellido del Personal</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="2">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nombre del Personal</div></td>
          <td colspan="4"><div align="left">
            <input name="rdborden" type="radio" class="sin-borde" value="3">
          </div></td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td colspan="4"> <div align="right"></div></td>
        </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function evaluar_opciones()
{
	f=document.form1;
	e_reporte = f.cmdreporte.value;
	document.getElementById('opcion1').style.visibility = 'hidden';
	document.getElementById('opcion2').style.visibility = 'hidden';
	document.getElementById('opcion3').style.visibility = 'hidden';
	switch(e_reporte.substring(0,32))
	{
		case "sigesp_snorh_rpp_listadopermisos":
			document.getElementById('opcion1').style.visibility = 'visible';
			document.getElementById('opcion2').style.visibility = 'visible';
			document.getElementById('opcion3').style.visibility = 'visible';
		break;
	}
}
function evaluar_opciones2()
{
	f=document.form1;
	e_reporte2 = f.cmdreporteexcel.value;
	document.getElementById('opcion1').style.visibility = 'hidden';
	document.getElementById('opcion2').style.visibility = 'hidden';
	document.getElementById('opcion3').style.visibility = 'hidden';
	switch(e_reporte2.substring(0,38))
	{
		case "sigesp_snorh_rpp_listadopermisos_excel":
			document.getElementById('opcion1').style.visibility = 'visible';
			document.getElementById('opcion2').style.visibility = 'visible';
			document.getElementById('opcion3').style.visibility = 'visible';
		break;
	}
}

function ue_print()
{
	f=document.form1;
	li_imprimir=f.imprimir.value;
	if(li_imprimir==1)
	{	
		codnomdes=f.txtcodnomdes.value;
		codnomhas=f.txtcodnomhas.value;
		reporte=f.cmdreporte.value;
		if(reporte!="")
		{
			if(codnomdes<=codnomhas)
			{
				codperdes=f.txtcodperdes.value;
				codperhas=f.txtcodperhas.value;
				if(codperdes<=codperhas)
				{
					activo="";
					egresado="";
					causaegreso="";
					activono="";
					vacacionesno="";
					egresadono="";
					suspendidono="";
					masculino="";
					femenino="";
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
					if(f.chkactivo.checked)
					{
						activo=1;
					}
					if(f.chkegresado.checked)
					{
						egresado=1;
						causaegreso=f.cmbcauegrper.value;
					}
					if(f.chkactivono.checked)
					{
						activono=1;
					}
					if(f.chkvacacionesno.checked)
					{
						vacacionesno=1;
					}
					if(f.chkegresadono.checked)
					{
						egresadono=1;
					}
					if(f.chksuspendidono.checked)
					{
						suspendidono=1;
					}
					if(f.chkmasculino.checked)
					{
						masculino=1;
					}
					if(f.chkfemenino.checked)
					{
						femenino=1;
					}
					codubifis=f.txtcodubifis.value;
					codpai=f.txtcodpai.value;
					codest=f.txtcodest.value;
					codmun=f.txtcodmun.value;
					codpar=f.txtcodpar.value;
					codestper=f.txtcodestper.value;
					codmunper=f.txtcodmunper.value;
					codparper=f.txtcodparper.value;


					fec_d=f.fec_desde.value;
					fec_h=f.fec_hasta.value;
					tipo_per=f.cmbtipper.value;
					uniadmin=f.txtcoduniadm.value;
					if(f.chkforcon.checked==true)
					{
						reporte="sigesp_snorh_rpp_listadopermisos_continuo.php";
					}
					pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&codperdes="+codperdes+"&codperhas="+codperhas;
					pagina=pagina+"&activono="+activono+"&vacacionesno="+vacacionesno+"&egresadono="+egresadono+"&suspendidono="+suspendidono;
					pagina=pagina+"&activo="+activo+"&egresado="+egresado+"&orden="+orden+"&causaegreso="+causaegreso+"&masculino="+masculino;
					pagina=pagina+"&femenino="+femenino+"&codubifis="+codubifis+"&codpai="+codpai+"&codest="+codest+"&codmun="+codmun+"&codpar="+codpar;
					pagina=pagina+"&fec_desde="+fec_d+"&fec_hasta="+fec_h+"&tipo_permiso="+tipo_per+"&uniadmin="+uniadmin;
					pagina=pagina+"&codestper="+codestper+"&codmunper="+codmunper+"&codparper="+codparper;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del personal está erroneo");
				}
			}
			else
			{
				alert("El rango del nómina está erroneo");
			}
		}
		else
		{
			alert("El Reporte no está desarrollado para esta opción");
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
		reporte=f.cmdreporteexcel.value;
		if(reporte!="")
		{
			codnomdes=f.txtcodnomdes.value;
			codnomhas=f.txtcodnomhas.value;
			if(codnomdes<=codnomhas)
			{
				codperdes=f.txtcodperdes.value;
				codperhas=f.txtcodperhas.value;
				if(codperdes<=codperhas)
				{
					activo="";
					egresado="";
					causaegreso="";
					activono="";
					vacacionesno="";
					egresadono="";
					suspendidono="";
					masculino="";
					femenino="";
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
					if(f.chkactivo.checked)
					{
						activo=1;
					}
					if(f.chkegresado.checked)
					{
						egresado=1;
						causaegreso=f.cmbcauegrper.value;
					}
					if(f.chkactivono.checked)
					{
						activono=1;
					}
					if(f.chkvacacionesno.checked)
					{
						vacacionesno=1;
					}
					if(f.chkegresadono.checked)
					{
						egresadono=1;
					}
					if(f.chksuspendidono.checked)
					{
						suspendidono=1;
					}
					if(f.chkmasculino.checked)
					{
						masculino=1;
					}
					if(f.chkfemenino.checked)
					{
						femenino=1;
					}
					codubifis=f.txtcodubifis.value;
					codpai=f.txtcodpai.value;
					codest=f.txtcodest.value;
					codmun=f.txtcodmun.value;
					codpar=f.txtcodpar.value;
					uniadmin=f.txtcoduniadm.value;
					pagina="reportes/"+reporte+"?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&codperdes="+codperdes+"&codperhas="+codperhas;
					pagina=pagina+"&activono="+activono+"&vacacionesno="+vacacionesno+"&egresadono="+egresadono+"&suspendidono="+suspendidono;
					pagina=pagina+"&activo="+activo+"&egresado="+egresado+"&orden="+orden+"&causaegreso="+causaegreso+"&masculino="+masculino;
					pagina=pagina+"&femenino="+femenino+"&codubifis="+codubifis+"&codpai="+codpai+"&codest="+codest+"&codmun="+codmun+"&codpar="+codpar+"&uniadmin="+uniadmin;
					window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
				}
				else
				{
					alert("El rango del personal está erroneo");
				}
			}
			else
			{
				alert("El rango del nómina está erroneo");
			}
		}
		else
		{
			alert("El Reporte no está desarrollado para esta opción");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación");
   	}		
}

function ue_buscarnominadesde()
{
	window.open("sigesp_snorh_cat_nomina.php?tipo=replisperdes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarnominahasta()
{
	f=document.form1;
	if(f.txtcodnomdes.value!="")
	{
		window.open("sigesp_snorh_cat_nomina.php?tipo=replisperhas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar una nómina desde.");
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

function ue_buscarubicacionfisica()
{
	window.open("sigesp_snorh_cat_ubicacionfisica.php?tipo=listadopersonal","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarestado(tipo)
{
	f=document.form1;
	f.txtcodubifis.value="";
    f.txtdesubifis.value="";
	codpai=ue_validarvacio(f.txtcodpai.value);
	if(codpai!="")
	{
		window.open("sigesp_snorh_cat_estado.php?tipo="+tipo+"&codpai="+codpai+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais.");
	}
}

function ue_buscarmunicipio(tipo)
{
	f=document.form1;
	codpai=ue_validarvacio(f.txtcodpai.value);
	if (tipo=='PERSONAL')
	{
		codest=ue_validarvacio(f.txtcodestper.value);	
	}
	else
	{
		codest=ue_validarvacio(f.txtcodest.value);
	}
	if((codpai!="")||(codest!=""))
	{
		window.open("sigesp_snorh_cat_municipio.php?tipo="+tipo+"&codpai="+codpai+"&codest="+codest+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais y un estado.");
	}
}

function ue_buscarparroquia(tipo)
{
	f=document.form1;
	codpai=ue_validarvacio(f.txtcodpai.value);
	if (tipo=='PERSONAL')
	{
		codest=ue_validarvacio(f.txtcodestper.value);	
		codmun=ue_validarvacio(f.txtcodmunper.value);
	}
	else
	{
		codest=ue_validarvacio(f.txtcodest.value);
		codmun=ue_validarvacio(f.txtcodmun.value);
	}

	if((codpai!="")||(codest!="")||(codmun!=""))
	{
		window.open("sigesp_snorh_cat_parroquia.php?tipo="+tipo+"&codpai="+codpai+"&codest="+codest+"&codmun="+codmun+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais, un estado y un municipio.");
	}
}

function ue_buscarunidadadministrativa()
{
	window.open("sigesp_snorh_cat_uni_ad.php?tipo=asignacion","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function limpiar_tipo()
{
	f=document.form1;
	f.txtcoduniadm.value = '';
	f.txtdesuniadm.value = '';
}
document.getElementById('opcion1').style.visibility = 'hidden';
document.getElementById('opcion2').style.visibility = 'hidden';
document.getElementById('opcion3').style.visibility = 'hidden';
</script> 
</html>