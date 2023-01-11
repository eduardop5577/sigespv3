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
$arrResultado=$io_fun_banco->uf_load_seguridad("SCB","sigesp_scb_r_listadoubicaciondocumentos.php",$ls_permisos,$la_seguridad,$la_permisos);
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
<title>Listado Ubicacion de Documentos</title>
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
    	<a href="javascript:ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a>
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
	$ls_tipo	  = $_POST["rb_provbene"];
	$ls_provbene  = $_POST["txtprovbene"];
	$ls_desproben = $_POST["txtdesproben"];
	$ld_fecdes    = $_POST["txtfecdesde"];
	$ld_fechas    = $_POST["txtfechasta"];
	$ls_codban    = $_POST["txtcodban"];
	$ls_deban     = $_POST["txtdenban"];
	$ls_cuenta_banco = $_POST["txtcuenta"];
	$ls_dencuenta_banco = $_POST["txtdenominacion"];
    $li_totrows   = $_POST["hidtotrows"];
    
	if($ls_tipo=='P')
	{
		$rb_p="checked";
		$rb_b="";
		$rb_n="";			
	}
	else if($ls_tipo=='B')
	{
		$rb_p="";
		$rb_b="checked";
		$rb_n="";			
	}
	else if($ls_tipo=='N'){
		$rb_p="";
		$rb_b="";
		$rb_n="checked";
	}
	
	if(array_key_exists("chksolicitudes",$_POST)){
		$li_chksol = 1;
	}
	
	$ls_estado    = $_POST["cmbestatus"];
	$ls_esttod    = "";
	$ls_estfir    = "";
	$ls_estcaj    = "";
	switch ($ls_estado) {
		case 'T':
			$ls_esttod    = "selected";
			break;
		case 'F':
			$ls_estfir    = "selected";
			break;
		case 'C':
			$ls_estcaj    = "selected"; 
			break;
	}
	$ls_orden    = $_POST["cmborden"];
	$ls_estfec    = "";
	$ls_estdoc    = "";
	if($ls_orden=='F') {
		$ls_estfec = "selected";
	}
	else {
		$ls_estdoc = "selected";
	}

	if ($ls_operacion=="NUEVO"){
		$ls_operacion = "";	
		$rb_p         = "";
		$rb_b         = "";
		$rb_n         = "";
		$ls_provbene  = "";
		$ls_desproben = ""; 
		$ld_fecha     = date("d/m/Y");
		$ld_fecdes    = $ld_fecha;
		$ld_fechas    = $ld_fecha;
    	$ls_accion    = "";
    	$li_totrows   = 0;
    	$li_chksol    = 0;
    	$ls_estado    = "";
    	$ls_codban    = "";
    	$ls_deban     = "";
    	$ls_cuenta_banco = "";
    	$ls_dencuenta_banco = "";
    	$ls_orden = "";
	}
}
else{
	$ls_operacion = "";	
	$rb_p         = "";
	$rb_b         = "";
	$rb_n         = "";
	$ls_provbene  = "";
	$ls_desproben = ""; 
	$ld_fecha     = date("d/m/Y");
	$ld_fecdes    = $ld_fecha;
	$ld_fechas    = $ld_fecha;
    $ls_accion    = "";
    $li_totrows   = 0;
    $li_chksol    = 0;
    $ls_estado    = "";
    $ls_esttod    = "";
	$ls_estfir    = "";
	$ls_estcaj    = "";
	$ls_codban    = "";
	$ls_deban     = "";
	$ls_cuenta_banco = "";
	$ls_dencuenta_banco = "";
	$ls_orden = "";
}



//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
</div>
<p>&nbsp;</p>
<form id="sigesp_scb_r_listadoubicaciondocumentos.php" name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_banco->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_banco);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="670" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
   
    <tr>
      <td width="114"></td>
    </tr>
    <tr class="titulo-ventana">
      <td height="22" colspan="4" align="center">Filtros de Busqueda </td>
    </tr>
    <tr>
      <td height="13" colspan="4" align="center">&nbsp;</td>
    </tr>
     <tr>
      <td height="26"><div align="right">Tipo Destino </div></td>
      <td height="26" colspan="2">
      	<label>
      	 	<input type="radio" name="rb_provbene" id="radio" value="P" class="sin-borde" onClick="javascript:uf_verificar_provbene(this.checked);" <?php print $rb_p;?>>
          		Proveedor
      	</label>
        <label>
           	<input type="radio" name="rb_provbene" id="radio" value="B" class="sin-borde" onClick="javascript:uf_verificar_provbene(this.checked);" <?php print $rb_b;?>>
          		Beneficiario
        </label>
        <label>
            <input name="rb_provbene" id="radio" type="radio"  class="sin-borde" value="N" onClick="javascript:uf_verificar_provbene(this.checked);" <?php print $rb_n;?>>
          		Ninguno
        </label>
      </td>
      <td width="173" height="26">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" align="right">Proveedor/Beneficiario</td>
      <td height="22" colspan="3" style="text-align:left"><div align="left">
        <input name="txtprovbene" type="text" id="txtprovbene" style="text-align:center" value="<?php print $ls_provbene?>" size="24" readonly>
        <a href="javascript:catprovbene()"><img id="bot_provbene" src="../shared/imagebank/tools15/buscar.gif" alt="Catalogo Proveedores/Beneficiarios" width="15" height="15" border="0"></a>
        <input name="txtdesproben" type="text" id="txtdesproben" size="60" maxlength="250" class="sin-borde" value="<?php print $ls_desproben;?>"  readonly>
      </div></td>
    </tr>
    <tr>
      <td width="96" height="22"  align="right">Banco</td>
      <td colspan="3" align="left">
          <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban;?>" size="10" readonly>
          <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
          <input name="txtdenban" type="text" id="txtdenban" value="<?php print $ls_denban?>" size="70" class="sin-borde" readonly>      </td>
    </tr>
    <tr>
      <td height="22" align="right">Cuenta</td>
      <td colspan="3" align="left">
          <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" value="<?php print $ls_cuenta_banco; ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php print $ls_dencuenta_banco; ?>" size="50" maxlength="254" readonly>
      </td>
    </tr>
    <tr>
      <td height="27" style="text-align:right">Desde</td>
      <td width="147" height="27" align="center"><div align="left">
        <input name="txtfecdesde" type="text" id="txtfecdesde"  style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fecdes ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
      <td width="99" height="27" style="text-align:right">Hasta</td>
      <td width="173" height="27" align="center"><div align="left">
        <input name="txtfechasta" type="text" id="txtfechasta" style="text-align:center" onKeyPress="currencyDate(this);" value="<?php print $ld_fechas ?>" size="20" maxlength="10"  datepicker="true">
      </div></td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Estatus</td>
      <td height="22" colspan="3" align="center"><div align="left">
         <select name="cmbestatus" id="cmbestatus">
            <option value="T" <?php echo $ls_esttod?>>Todos</option>
            <option value="F" <?php echo $ls_estfir?>>En la Firma</option>
            <option value="C" <?php echo $ls_estcaj?>>En Caja</option>
         </select>
      </td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Ordenado por</td>
      <td height="22" colspan="3" align="center"><div align="left">
         <select name="cmborden" id="cmborden">
            <option value="F" <?php echo $ls_estfec?>>Fecha</option>
            <option value="D" <?php echo $ls_estdoc?>>Numero de Documento</option>
         </select>
      </td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Mostrar Solicitudes</td>
      <td height="22" colspan="3" align="center"><div align="left">
         <input type="checkbox" name="chksolicitudes">
      </td>
    </tr>
    <tr>
      <td height="22" colspan="4" align="center">
      	<p align="right">
      		<a href="javascript:ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0">Buscar</a>
      	</p>
      </td>
    </tr>
  </table>
 
</table>
</p>
<p align="center">
<?php
if ($ls_operacion=="BUSCAR"){
	$rs_data   = $io_control->uf_buscar_documentos($ls_provbene,$ls_tipo,$ld_fecdes,$ld_fechas,$ls_estado,$li_chksol,$ls_codban,$ls_cuenta_banco,$ls_orden);
	if ($rs_data){
		$title[1] = "<font color=#FFFFFF>Documento</font>"; 
		$title[2] = "<font color=#FFFFFF>Fecha Emision</font>";
		$title[3] = "<font color=#FFFFFF>Proveedor/Beneficiario</font>"; 
		$title[4] = "<font color=#FFFFFF>Tipo</font>"; 
		$title[5] = "<font color=#FFFFFF>Monto</font></a>"; 
		$title[6] = "<font color=#FFFFFF>Estatus</font>";
		$title[7] = "<font color=#FFFFFF>Fecha Envio</font>"; 
		$ls_grid  = "grid_documentos";
		if (!$rs_data->EOF){
			$li_fila=0;
			while (!$rs_data->EOF){
				$ls_numero  = $rs_data->fields["numero"];
				$ls_fecha   = $io_funcion->uf_convertirfecmostrar($rs_data->fields["fecha"]);
				$ls_tipodoc = $rs_data->fields["tipodoc"];
				$ld_monto   = number_format($rs_data->fields["monto"],2,",",".");
				$ls_estatus = $rs_data->fields["estado"];
				$ls_proben = $rs_data->fields["nombre"];
				$ls_detest  = "";
				$ls_fecenv  = "N/A";
				
				if ($ls_tipodoc!='SP') {
					switch ($ls_estatus) {
						case 'S':
							$ls_detest = 'Emitido';
							$li_fila++;
							$object[$li_fila][1]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numero."'  class=sin-borde  size=20  style=text-align:center readonly>";
							$object[$li_fila][2]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecha."'  class=sin-borde  size=12   style=text-align:center readonly>";
							$object[$li_fila][3]="<input type=text      id=txtproben".$li_fila."  name=txtproben".$li_fila."  value='".$ls_proben."'  class=sin-borde  size=25  style=text-align:center readonly>";
							$object[$li_fila][4]="<input type=text      id=txttipdoc".$li_fila."  name=txttipdoc".$li_fila."  value='".$ls_tipodoc."'  class=sin-borde  size=5  style=text-align:center readonly>";
							$object[$li_fila][5]="<input type=text      id=txtmonto".$li_fila."   name=txtmotno".$li_fila."   value='".$ld_monto."'  class=sin-borde  size=15   style=text-align:center readonly>";
							$object[$li_fila][6]="<input type=text      id=txtestado".$li_fila."  name=txtestado".$li_fila."  value='".$ls_detest."'  class=sin-borde  size=15  style=text-align:left   readonly>";
							$object[$li_fila][7]="<input type=text      id=txtfecenv".$li_fila."  name=txtfecenv".$li_fila."  value='".$ls_fecenv."'  class=sin-borde  size=12  style=text-align:left   readonly>";
							break;
						
						case 'F':
							$ls_detest = 'Enviado a la Firma';
							$ls_fecenv = $io_funcion->uf_convertirfecmostrar($rs_data->fields['fecenvfir']);
							$li_fila++;
							$object[$li_fila][1]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numero."'  class=sin-borde  size=20  style=text-align:center readonly>";
							$object[$li_fila][2]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecha."'  class=sin-borde  size=12   style=text-align:center readonly>";
							$object[$li_fila][3]="<input type=text      id=txtproben".$li_fila."  name=txtproben".$li_fila."  value='".$ls_proben."'  class=sin-borde  size=25  style=text-align:center readonly>";
							$object[$li_fila][4]="<input type=text      id=txttipdoc".$li_fila."  name=txttipdoc".$li_fila."  value='".$ls_tipodoc."'  class=sin-borde  size=5  style=text-align:center readonly>";
							$object[$li_fila][5]="<input type=text      id=txtmonto".$li_fila."   name=txtmotno".$li_fila."   value='".$ld_monto."'  class=sin-borde  size=15   style=text-align:center readonly>";
							$object[$li_fila][6]="<input type=text      id=txtestado".$li_fila."  name=txtestado".$li_fila."  value='".$ls_detest."'  class=sin-borde  size=15  style=text-align:left   readonly>";
							$object[$li_fila][7]="<input type=text      id=txtfecenv".$li_fila."  name=txtfecenv".$li_fila."  value='".$ls_fecenv."'  class=sin-borde  size=12  style=text-align:left   readonly>";
							break;
					
						case 'C':
							$ls_detest = 'Enviado a Caja';
							$ls_fecenv = $io_funcion->uf_convertirfecmostrar($rs_data->fields['fecenvcaj']);
							$li_fila++;
							$object[$li_fila][1]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numero."'  class=sin-borde  size=20  style=text-align:center readonly>";
							$object[$li_fila][2]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecha."'  class=sin-borde  size=12   style=text-align:center readonly>";
							$object[$li_fila][3]="<input type=text      id=txtproben".$li_fila."  name=txtproben".$li_fila."  value='".$ls_proben."'  class=sin-borde  size=25  style=text-align:center readonly>";
							$object[$li_fila][4]="<input type=text      id=txttipdoc".$li_fila."  name=txttipdoc".$li_fila."  value='".$ls_tipodoc."'  class=sin-borde  size=5  style=text-align:center readonly>";
							$object[$li_fila][5]="<input type=text      id=txtmonto".$li_fila."   name=txtmotno".$li_fila."   value='".$ld_monto."'  class=sin-borde  size=15   style=text-align:center readonly>";
							$object[$li_fila][6]="<input type=text      id=txtestado".$li_fila."  name=txtestado".$li_fila."  value='".$ls_detest."'  class=sin-borde  size=15  style=text-align:left   readonly>";
							$object[$li_fila][7]="<input type=text      id=txtfecenv".$li_fila."  name=txtfecenv".$li_fila."  value='".$ls_fecenv."'  class=sin-borde  size=12  style=text-align:left   readonly>";
							break;
							
						case 'E':
							$ls_detest = 'Entregado';
							$ls_fecenv = $io_funcion->uf_convertirfecmostrar($rs_data->fields['fecenvcaj']);
							$li_fila++;
							$object[$li_fila][1]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numero."'  class=sin-borde  size=20  style=text-align:center readonly>";
							$object[$li_fila][2]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecha."'  class=sin-borde  size=12   style=text-align:center readonly>";
							$object[$li_fila][3]="<input type=text      id=txtproben".$li_fila."  name=txtproben".$li_fila."  value='".$ls_proben."'  class=sin-borde  size=25  style=text-align:center readonly>";
							$object[$li_fila][4]="<input type=text      id=txttipdoc".$li_fila."  name=txttipdoc".$li_fila."  value='".$ls_tipodoc."'  class=sin-borde  size=5  style=text-align:center readonly>";
							$object[$li_fila][5]="<input type=text      id=txtmonto".$li_fila."   name=txtmotno".$li_fila."   value='".$ld_monto."'  class=sin-borde  size=15   style=text-align:center readonly>";
							$object[$li_fila][6]="<input type=text      id=txtestado".$li_fila."  name=txtestado".$li_fila."  value='".$ls_detest."'  class=sin-borde  size=15  style=text-align:left   readonly>";
							$object[$li_fila][7]="<input type=text      id=txtfecenv".$li_fila."  name=txtfecenv".$li_fila."  value='".$ls_fecenv."'  class=sin-borde  size=12  style=text-align:left   readonly>";
							break;
					}
					
				}
				else{
					if($ls_estatus=='X'){
						$ls_detest = 'Por programar pago';
						$li_fila++;
						$object[$li_fila][1]="<input type=text      id=txtnumdoc".$li_fila."  name=txtnumdoc".$li_fila."  value='".$ls_numero."'  class=sin-borde  size=20  style=text-align:center readonly>";
						$object[$li_fila][2]="<input type=text      id=txtfecha".$li_fila."   name=txtfecha".$li_fila."   value='".$ls_fecha."'  class=sin-borde  size=12   style=text-align:center readonly>";
						$object[$li_fila][3]="<input type=text      id=txtproben".$li_fila."  name=txtproben".$li_fila."  value='".$ls_proben."'  class=sin-borde  size=25  style=text-align:center readonly>";
						$object[$li_fila][4]="<input type=text      id=txttipdoc".$li_fila."  name=txttipdoc".$li_fila."  value='".$ls_tipodoc."'  class=sin-borde  size=5  style=text-align:center readonly>";
						$object[$li_fila][5]="<input type=text      id=txtmonto".$li_fila."   name=txtmotno".$li_fila."   value='".$ld_monto."'  class=sin-borde  size=15   style=text-align:center readonly>";
						$object[$li_fila][6]="<input type=text      id=txtestado".$li_fila."  name=txtestado".$li_fila."  value='".$ls_detest."'  class=sin-borde  size=15  style=text-align:left   readonly>";
						$object[$li_fila][7]="<input type=text      id=txtfecenv".$li_fila."  name=txtfecenv".$li_fila."  value='".$ls_fecenv."'  class=sin-borde  size=12  style=text-align:left   readonly>";
					}
				}
				$rs_data->MoveNext();
			}
		    $io_grid->make_gridScroll($li_fila,$title,$object,770,'Listado de Documentos',$ls_grid,200);
		    $li_totrows = $li_fila;
		}
        else{
        	$io_msg->message("No se han encontrado Documentos para este Criterio de Búsqueda !!!");
		}
	}
	else{
		
	}
}
?>
</p>
<div align="center">
  <input name="hidtotrows"  type="hidden"   id="hidtotrows" value="<?php print $li_totrows ?>">
  <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">
</div>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function ue_nuevo(){
	var f = document.form1;
	f.operacion.value = "NUEVO";
	f.action="sigesp_scb_r_listadoubicaciondocumentos.php";
	f.submit();
}

function ue_search(){
	var f = document.form1;
	var li_leer = f.leer.value;
	if (li_leer==1){
		f.operacion.value = "BUSCAR";
		f.action          = "sigesp_scb_r_listadoubicaciondocumentos.php";
		f.submit();
	}
  	else{
  	  	alert("No tiene permiso para realizar esta operación !!!");
	}
}

function ue_imprimir()
{
  var f           = document.form1;
  var li_imprimir = f.imprimir.value;
  var codigo      = f.txtprovbene.value;
  var descripcion = f.txtdesproben.value;
  var tipo	      = getRadioButtonSelectedValue(f.rb_provbene);
  var fecdes      = f.txtfecdesde.value;
  var fechas      = f.txtfechasta.value;
  var estatus     = f.cmbestatus.value;
  var codban      = f.txtcodban.value;
  var cuenta      = f.txtcuenta.value;
  var orden       = f.cmborden.value;	
  var bansol      = 0;		
  if(f.chksolicitudes.checked){
	  bansol = 1;  
  }
  if (li_imprimir=='1'){
	  pagina="reportes/sigesp_scb_rpp_listadoubicaciondocumentos.php?codigo="+codigo+"&descripcion="+descripcion+"&tipo="+tipo+"&fecdes="+fecdes+"&fechas="+fechas+"&estatus="+estatus+"&bansol="+bansol+"&codban="+codban+"&cuenta="+cuenta+"&orden="+orden;
	  window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
  }
  else{
	   alert("No tiene permiso para realizar esta operación !!!");
  }
}

function catprovbene()
{
	var f = document.form1;
	if(f.rb_provbene[0].checked==true)
	{
		f.txtprovbene.disabled=false;	
		window.open("sigesp_catdinamic_prov.php","Catalogo","menubar=no,toolbar=no,scrollbars=yes,width=540,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else if(f.rb_provbene[1].checked==true)
	{
		f.txtprovbene.disabled=false;	
		window.open("sigesp_catdinamic_bene.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=540,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function uf_verificar_provbene(lb_checked)
{
	f=document.form1;

	if((f.rb_provbene[0].checked))
	{
		f.txtprovbene.value="";
		f.txtdesproben.value="";
	}
	if((f.rb_provbene[1].checked))
	{
		f.txtprovbene.value="";
		f.txtdesproben.value="";
	}
	if((f.rb_provbene[2].checked))
	{
		f.txtprovbene.value="----------";
		f.txtdesproben.value="Ninguno";
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

function getRadioButtonSelectedValue(ctrl)
{
    for(i=0;i<ctrl.length;i++)
        if(ctrl[i].checked) return ctrl[i].value;
}

function cat_bancos() {
	pagina="sigesp_cat_bancos.php";
  	window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
}

function catalogo_cuentabanco() {
	var f = document.form1;
	var ls_codban = f.txtcodban.value;
  	var ls_denban = f.txtdenban.value;
  	if (ls_codban!="") {
  	  	var pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_denban;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=700,height=400,resizable=yes,location=no");
	} 
	else {
		alert("Seleccione el Banco");   
	}
}

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>