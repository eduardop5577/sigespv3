<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
if (!array_key_exists("la_logusr",$_SESSION)){
	print "<script language=JavaScript>";
	print "location.href='sigesp_inicio_sesion.php'";
	print "</script>";
}

$ls_logusr = $_SESSION["la_logusr"];
require_once("class_funciones_banco.php");
$io_fun_banco= new class_funciones_banco();
$ls_permisos="";
$la_seguridad=Array();
$la_permisos=Array();
$arrResultado=$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_p_controldocumentos.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado["as_permisos"];
$la_seguridad=$arrResultado["aa_seguridad"];
$la_permisos=$arrResultado["aa_permisos"];
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
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Control de Documentos</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="../scb/js/ajax.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript"  src="../shared/js/sigesp_cat_ordenar.js"></script>
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
<script language="JavaScript">
function ue_limpiar(){
	var f =document.form1;
	f.txtcodban.value="";
	f.txtdenban.value="";
	f.txtcuenta.value="";
	f.txtdenominacion.value="";
	if(f.cmboperacion.disabled){
		f.cmboperacion.disabled=false;
	}
	f.cmboperacion.selectedIndex=0;
}
</script>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar">
        <a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a>
    	<a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Grabar" width="20" height="20" border="0" title="Procesar"></a>
    	<a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a>
    	<img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20">
    </td>
  </tr>
</table>
<?Php
require_once("../shared/class_folder/grid_param.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("class_folder/sigesp_scb_c_controldocumentos.php");

$io_grid    = new grid_param();
$io_msg     = new class_mensajes();
$io_funcion = new class_funciones();
$io_control = new sigesp_scb_c_controldocumentos('../');
$ls_codemp  = $_SESSION["la_empresa"]["codemp"];

if(array_key_exists("operacion",$_POST)){
	$ls_operacion = $_POST["operacion"];
	$ls_codban    = $_POST["txtcodban"];
	$ls_denban    = $_POST["txtdenban"];
	$ls_ctaban    = $_POST["txtcuenta"];
	$ls_denctaban = $_POST["txtdenominacion"];
	$ld_fecdes    = $_POST["txtfecdesde"];
	$ld_fechas    = $_POST["txtfechasta"];
    $ls_accion    = $_POST["cmboperacion"];
    $ls_estado    = $_POST["estado"];
    $li_totrows   = $_POST["hidtotrows"];
    $ls_EF="";
	$ls_EC="";
    $ls_DF="";
    $ls_DC="";
    if ($ls_operacion!="NUEVO"){
    	$ls_disabled="disabled";
    }
    else{
    	$ls_disabled="";
    }
    switch ($ls_accion){
    	case "EF";
    		$ls_EF="selected";
    		break;
    	case "EC";
    		$ls_EC="selected";
    		break;
    	case "DF";
    		$ls_DF="selected";
    		break;
    	case "DC";
    		$ls_DC="selected";
    		break;
    }
	
    
}
else{
	$ls_operacion = "";	
	$ld_fecha     = date("d/m/Y");
	$ls_codban    = "";
	$ls_denban    = "";
	$ls_ctaban    = "";
	$ls_denctaban = "";
	$ld_fecdes    = $ld_fecha;
	$ld_fechas    = $ld_fecha;
    $ls_accion    = "";
    $li_totrows   = 0;
    $ls_EF="selected";
	$ls_EC="";
    $ls_DF="";
    $ls_DC="";
    $ls_disabled="";
    $ls_estado    = "";
}



//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
</div>
<p>&nbsp;</p>
<form id="sigesp_scb_p_controldocumentos.php" name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="535" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
   
    <tr>
      <td width="62"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Cheques / Carta Orden </td>
    </tr>
    <tr>
      <td height="13" colspan="4" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Banco</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban ?>" size="10" readonly>
        <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
        <input name="txtdenban" type="text" class="sin-borde" id="txtdenban" value="<?php print $ls_denban ?>" size="60" readonly>
        <span class="Estilo1">
        </span></div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Cuenta</td>
      <td height="22" colspan="3" align="center"><div align="left">
        <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" value="<?php print $ls_ctaban ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php print $ls_denctaban ?>" size="45" maxlength="254" readonly>
      </div></td>
    </tr>
	<tr>
      <td height="22" style="text-align:right">Desde</td>
      <td width="146" height="22" align="center"><div align="left">
        <input name="txtfecdesde" type="text" id="txtfecdesde"  style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fecdes ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
      <td width="80" height="22" style="text-align:right">Hasta</td>
      <td width="245" height="22" align="center"><div align="left">
        <input name="txtfechasta" type="text" id="txtfechasta" style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fechas ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Operacion</td>
      <td height="22" colspan="3" align="center"><div align="left">
         <select name="cmboperacion" id="cmboperacion" <?php echo $ls_disabled?>>
            <option value="EF" <?php echo $ls_EF?>>Enviar a Firma</option>
            <option value="EC" <?php echo $ls_EC?>>Enviar a Caja</option>
            <option value="DF" <?php echo $ls_DF?>>Deshacer Envio a Firma</option>
            <option value="DC" <?php echo $ls_DC?>>Deshacer Envio a Caja</option>
         </select>
      </td>
    </tr>
	<tr>
      <td height="22" colspan="4" align="center">
      	<p align="right">
      		<a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0">Buscar</a>
      	</p>
      	<p align="left">
      		<a href="javascript: seleccionar_todos();"><img src="../shared/imagebank/tools20/aprobado.gif"  width="20" height="20" border="0">Seleccionar Todos</a>
      		<a href="javascript: deseleccionar_todos();"><img src="../shared/imagebank/tools20/eliminar.gif" width="20" height="20" border="0">Deseleccionar Todos</a>
      	</p>
      </td>
    </tr>
  </table>
 
</table>
</p>
<p align="center">
<?php
if ($ls_operacion=="NUEVO"){
	$ls_operacion = "";	
	$ld_fecha     = date("d/m/Y");
	$ls_codban    = "";
	$ls_denban    = "";
	$ls_ctaban    = "";
	$ls_denctaban = "";
	$ld_fecdes    = $ld_fecha;
	$ld_fechas    = $ld_fecha;
    $ls_accion    = "";
    $li_totrows   = 0;
    $ls_EF="selected";
	$ls_EC="";
    $ls_DF="";
    $ls_DC="";
    $ls_disabled="";
    $ls_estado    = "";
}
else if ($ls_operacion=="BUSCAR"){
	switch ($ls_accion){
    	case "EF";
    		$ls_estpro="S";
    		break;
    	case "EC";
    		$ls_estpro="F";
    		break;
    	case "DF";
    		$ls_estpro="F";
    		break;
    	case "DC";
    		$ls_estpro="C";
    		break;
    }
	
    $rs_data   = $io_control->uf_load_documentos($ls_codban,$ls_ctaban,$ld_fecdes,$ld_fechas,$ls_estpro);
	if ($rs_data){
		$title[1] = ""; 
		$title[2] = "<a href=javascript:ue_ordenar('M.numdoc');><font color=#FFFFFF>Documento</font></a>"; 
		$title[3] = "<a href=javascript:ue_ordenar('M.codope');><font color=#FFFFFF>Operacion</font>"; 
		$title[4] = "<a href=javascript:ue_ordenar('nombre');><font color=#FFFFFF>Proveedor/Beneficiario</font></a>"; 
		$title[5] = "<a href=javascript:ue_ordenar('M.fecmov');><font color=#FFFFFF>Fecha</font></a>"; 
		$title[6] = "<font color=#FFFFFF>Monto</font>"; 
		$ls_grid  = "grid_documentos";
		if (!$rs_data->EOF){
			$li_fila=0;
			while (!$rs_data->EOF){
				$li_fila++;
				$ls_codemp = trim($rs_data->fields["codemp"]);
				$ls_codban = trim($rs_data->fields["codban"]);
				$ls_ctaban = trim($rs_data->fields["ctaban"]);
			    $ls_numdoc = trim($rs_data->fields["numdoc"]);
				$ls_codope = $rs_data->fields["codope"];
				$ls_estmov = $rs_data->fields["estmov"];
				$ls_nombre = $rs_data->fields["nombre"];
				$ld_monmov = $rs_data->fields["monto"];
				$ls_fecmov = $io_funcion->uf_formatovalidofecha($rs_data->fields["fecmov"]);
				$ls_fecmov = $io_funcion->uf_convertirfecmostrar($ls_fecmov);
		        $object[$li_fila][1]="<input type=checkbox name=chksel".$li_fila.">
		        					  <input type=hidden    id=txtcodemp".$li_fila."  name=txtcodemp".$li_fila."  value='".$ls_codemp."'>
		        					  <input type=hidden    id=txtcodban".$li_fila."  name=txtcodban".$li_fila."  value='".$ls_codban."'>
		        					  <input type=hidden    id=txtctaban".$li_fila."  name=txtctaban".$li_fila."  value='".$ls_ctaban."'>
		        					  <input type=hidden    id=txtestmov".$li_fila."  name=txtestmov".$li_fila."  value='".$ls_estmov."'>";
				$object[$li_fila][2]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numdoc."'  class=sin-borde  size=15  style=text-align:center readonly>";
				$object[$li_fila][3]="<input type=text      id=txttipope".$li_fila."  name=txttipope".$li_fila."  value='".$ls_codope."'  class=sin-borde  size=5   style=text-align:center readonly>";
				$object[$li_fila][4]="<input type=text      id=txtnombre".$li_fila."  name=txtnombre".$li_fila."  value='".$ls_nombre."'  class=sin-borde  size=40  style=text-align:left   readonly>"; 
				$object[$li_fila][5]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecmov."'  class=sin-borde  size=8   style=text-align:center readonly>";
		        $object[$li_fila][6]="<input type=text      id=txtmonto".$li_fila."   name=txtmonto".$li_fila."   value='".number_format($ld_monmov,2,',','.')."'  class=sin-borde  size=20  style=text-align:right   readonly>";
		        $rs_data->MoveNext();
			}
		    $io_grid->make_gridScroll($li_fila,$title,$object,670,'Listado de Documentos',$ls_grid,250);
		    $li_totrows = $li_fila;
		}
        else{
        	$io_msg->message("No se han encontrado Documentos para este Criterio de Búsqueda !!!");
		}
	}
}
else if($ls_operacion=="PROCESAR"){
	$ls_estpro="";
	switch ($ls_estado){
    	case "EF";
    		$ls_estpro="F";
    		break;
    	case "EC";
    		$ls_estpro="C";
    		break;
    	case "DF";
    		$ls_estpro="S";
    		break;
    	case "DC";
    		$ls_estpro="F";
    		break;
    }
	
    if($ls_estpro!=""){
		$lb_bandera=true;
		for($li_i=1;$li_i<=$li_totrows;$li_i++){
			if(array_key_exists("chksel".$li_i,$_POST)){
				$ls_codemp=$_POST["txtcodemp".$li_i];
				$ls_codban=$_POST["txtcodban".$li_i];
				$ls_ctaban=$_POST["txtctaban".$li_i];
				$ls_numdoc=$_POST["txtnumdoc".$li_i];
				$ls_codope=$_POST["txttipope".$li_i];
				$ls_estmov=$_POST["txtestmov".$li_i];
				$lb_bandera=$io_control->uf_actualizar_estcondoc($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_estmov,$ls_estpro,$la_seguridad);
				if(!$lb_bandera){
					$io_msg->message("Ocurrio un error procesando el documento ".$ls_numdoc);
					break;
				}
			}
		}
		
		if($lb_bandera){
			$io_msg->message("El proceso se realizo exitosamente");
		}
	}
	else{
		$io_msg->message("El estado seleccionado es invalido");
	}
	echo"<script language=javascript>ue_limpiar();</script>";
}
?>
</p>
<div align="center">
  <input name="hidtotrows"  type="hidden"   id="hidtotrows" value="<?php print $li_totrows ?>">
  <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
  <input name="estado"   type="hidden"   id="estado"   value="<?php print $ls_estado;?>">
  <input name="txttipocuenta"   type="hidden"   id="txttipocuenta">
</div>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">

function catalogo_cuentabanco(){
	var f = document.form1;
	var ls_codban=f.txtcodban.value;
	var ls_nomban=f.txtdenban.value;
	if(ls_codban!=""){
		pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_nomban;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=730,height=400,resizable=yes,location=no");
	}
	else{
		alert("Debe Seleccionar un Banco !!!");   
	}
}
	 
function cat_bancos(){
	var f = document.form1;
	var pagina = "sigesp_cat_bancos.php";
	window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
}

function ue_nuevo()
{
	ue_limpiar();
	var f =document.form1;
	f.operacion.value ="NUEVO";
	f.action="sigesp_scb_p_controldocumentos.php";
	f.submit();
}

function ue_search(){
	var f = document.form1;
	var li_leer = f.leer.value;
	if (li_leer==1){
		f.operacion.value = "BUSCAR";
		f.action          = "sigesp_scb_p_controldocumentos.php";
		f.submit();
	}
  	else{
  	  	alert("No tiene permiso para realizar esta operación !!!");
	}
}

function ue_procesar()
{
	var f = document.form1;
	var li_ejecutar = f.ejecutar.value;
	if(li_ejecutar==1)
	{
		// Para verificar que se selecciono algun comprobante
		var lb_valido=false;
		var li_total=f.hidtotrows.value;
		for(li_i=1;((li_i<=li_total)&&(lb_valido==false));li_i++)
		{
			lb_valido=eval("f.chksel"+li_i+".checked");
		}
		if(lb_valido)
		{
			f.estado.value=f.cmboperacion.value;
			f.operacion.value ="PROCESAR";
			f.action="sigesp_scb_p_controldocumentos.php";
			f.submit();
		}
		else
		{
			alert("No hay nada que procesar.");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function seleccionar_todos()
{	
	var f = document.form1;
	var li_total = f.hidtotrows.value;
	for(li_i=1;li_i<=li_total;li_i++)
	{
		eval("f.chksel"+li_i+".checked=true");
	}
}

function deseleccionar_todos()
{	
	var f = document.form1;
	var li_total = f.hidtotrows.value;
	for(li_i=1;li_i<=li_total;li_i++)
	{
		eval("f.chksel"+li_i+".checked=false");
	}
}

function currencyDate(date){ 
	ls_date=date.value;
	li_long=ls_date.length;
			 
		if(li_long==2)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(0,2);
			li_string=parseInt(ls_string,10);

			if((li_string>=1)&&(li_string<=31))
			{
				date.value=ls_date;
			}
			else
			{
				date.value="";
			}
			
		}
		if(li_long==5)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(3,2);
			li_string=parseInt(ls_string,10);
			if((li_string>=1)&&(li_string<=12))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,3);
			}
		}
		if(li_long==10)
		{
			ls_string=ls_date.substr(6,4);
			li_string=parseInt(ls_string,10);
			if((li_string>=1900)&&(li_string<=2090))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,6);
			}
		}
}	
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>