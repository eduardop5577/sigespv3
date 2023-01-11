<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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
    require_once("class_folder/class_funciones_ins.php");
    $oi_fun_instala=new class_funciones_ins("../");
    $ls_permisos = "";
    $la_seguridad = Array();
    $la_permisos = Array();
    $arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_d_configuracionsargus.php",$ls_permisos,$la_seguridad,$la_permisos);
    $ls_permisos = $arrResultado['as_permisos'];
    $la_seguridad = $arrResultado['aa_seguridad'];
    $la_permisos = $arrResultado['aa_permisos'];	
    //////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
    require_once("../shared/class_folder/grid_param.php");
    $io_grid=new grid_param();
    require_once("class_folder/sigesp_ins_c_integracionsargus.php");
    $io_integracion=new sigesp_ins_c_integracionsargus();

    $ls_gestor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","GESTOR_INT","MYSQLT","C");
    $ls_puerto_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","PUERTO_INT","3306","C");
    $ls_servidor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","SERVIDOR_INT","127.0.0.1","C");
    $ls_basedatos_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","BASE_DATOS_INT","db_sargus","C");
    $ls_login_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","LOGIN_INT","xxxxxxx","C");
    $ls_password_int=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","PASSWORD_INT","xxxxxx","C"));
    $ls_cuenta_ingreso=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO","303020100","C"));
    $ls_cuenta_ingreso_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO_IVA","303020100","C"));
    $ls_cuenta_contable_iva_retenido=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_CONTABLE_IVA_RETENIDO","11401000020000","C"));
    $ld_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","IVA",16,"I"));
    $ld_retencion_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","RETENCION_IVA",75,"I"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Integraci&oacute;n con SARGUS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="js/funcion_ins.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">

</head>
<body>
<?php 
    $ls_operacion=$oi_fun_instala->uf_obteneroperacion();
    switch ($ls_operacion) 
    {
        case "GUARDAR":
            $ls_gestor_int=$_POST["txtgestor"];
            $ls_puerto_int=$_POST["txtpuerto"];
            $ls_servidor_int=$_POST["txtservidor"];
            $ls_basedatos_int=$_POST["txtbasedatos"];
            $ls_login_int=$_POST["txtlogin"];
            $ls_password_int=trim($_POST["txtpassword"]);
            $ls_cuenta_ingreso=trim($_POST["txtcuentaingreso"]);
            $ls_cuenta_ingreso_iva=trim($_POST["txtcuentaingresoiva"]);
            $ls_cuenta_contable_iva_retenido=trim($_POST["txtcuentacontableivaretenido"]);
            $ld_iva=trim($_POST["txtiva"]);
            $ld_retencion_iva=trim($_POST["txtretencioniva"]);
            $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","GESTOR_INT",$ls_gestor_int,"C");
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","PUERTO_INT",$ls_puerto_int,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","SERVIDOR_INT",$ls_servidor_int,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","BASE_DATOS_INT",$ls_basedatos_int,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","LOGIN_INT",$ls_login_int,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","PASSWORD_INT",$ls_password_int,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO",$ls_cuenta_ingreso,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO_IVA",$ls_cuenta_ingreso_iva,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","CUENTA_CONTABLE_IVA_RETENIDO",$ls_cuenta_contable_iva_retenido,"C");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","IVA",$ld_iva,"I");
            }
            if ($lb_valido)
            {
                $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","RETENCION_IVA",$ld_retencion_iva,"I");
            }
            break;
	}
	unset($io_integracion);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Procesar" alt="Procesar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
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
<p>
</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<label></label>
  <table width="200" border="0" align="center">
    <tr>
      <td><div align="center">
        <table width="570" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
         <td height="22" colspan="3" class="titulo-celdanew">Configuraci&oacute;n SARGUS</td>
      </tr>
      <tr class="formato-blanco">
         <td height="13" colspan="3">&nbsp;</td>
      </tr>
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew">
                <div align="center">Informaci&oacute;n de Conexion SARGUS </div>
            </td>
          </tr>
          <tr>
            <td width="143" height="21"><div align="right">Gestor</div></td>
            <td height="21" colspan="2"><input name="txtgestor" type="text" id="txtgestor" value="<?php print $ls_gestor_int;?>"></td>
	  </tr>
          <tr>
            <td height="21"><div align="right">Puerto</div></td>
            <td height="21" colspan="2"><input name="txtpuerto" type="text" id="txtpuerto" value="<?php print $ls_puerto_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Servidor</div></td>
            <td height="21" colspan="2"><input name="txtservidor" type="text" id="txtservidor" value="<?php print $ls_servidor_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Base de Datos Origen </div></td>
            <td height="21" colspan="2"><input name="txtbasedatos" type="text" id="txtbasedatos" value="<?php print $ls_basedatos_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Login</div></td>
            <td height="21" colspan="2"><input name="txtlogin" type="text" id="txtlogin" value="<?php print $ls_login_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Password</div></td>
            <td height="21" colspan="2"><input name="txtpassword" type="text" id="txtpassword" value="<?php print $ls_password_int;?>"></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew">
                <div align="center">Informaci&oacute;n para Integrar </div>
            </td>
          </tr>
          <tr>
            <td height="21"><div align="right">Cuenta Ingreso</div></td>
            <td height="21" colspan="2"><input name="txtcuentaingreso" type="text" id="txtcuentaingreso" value="<?php print $ls_cuenta_ingreso;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Cuenta Ingreso IVA</div></td>
            <td height="21" colspan="2"><input name="txtcuentaingresoiva" type="text" id="txtcuentaingresoiva" value="<?php print $ls_cuenta_ingreso_iva;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Cuenta Contable IVA Retenido </div></td>
            <td height="21" colspan="2"><input name="txtcuentacontableivaretenido" type="text" id="txtcuentacontableivaretenido" value="<?php print $ls_cuenta_contable_iva_retenido;?>"></td>
          </tr>
          
          <tr>
            <td height="21"><div align="right">% IVA</div></td>
            <td height="21" colspan="2"><input name="txtiva" type="text" id="txtiva" value="<?php print $ld_iva;?>" size="6" maxlength="5"  onKeyPress="return(currencyFormat(this,'.',',',event))" style="text-align:right" onBlur="javascript: ue_validarporcentaje();"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">% Retenci&oacute;n de IVA</div></td>
            <td height="21" colspan="2"><input name="txtretencioniva" type="text" id="txtretencioniva" value="<?php print $ld_retencion_iva;?>" size="6" maxlength="5"  onKeyPress="return(currencyFormat(this,'.',',',event))" style="text-align:right" onBlur="javascript: ue_validarporcentaje();"></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
    <p>
       <div id=transferir style="visibility:hidden" align="center"><img src="../shared/imagebank/cargando.gif">Procesando ... </div>		  
    </p>

  <input name="operacion" type="hidden" id="operacion" value="<?php $_REQUEST["OPERACION"] ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);

function ue_nuevo()
{
   location.href='sigesp_ins_d_configuracionsargus.php' 
}

function ue_guardar()
{
    f=document.form1;
    li_incluir=f.incluir.value;
    li_cambiar=f.cambiar.value;
    if ((li_cambiar==1)||(li_incluir==1))
    {
        mostrar('transferir');
        f.operacion.value="GUARDAR";	
        f.action="sigesp_ins_d_configuracionsargus.php";
        f.submit();
    }
    else
    {
        alert("No tiene permiso para realizar esta operacion.")
    }	
}

function ue_cerrar()
{
   location.href='sigespwindow_blank.php'; 
}

function mostrar(nombreCapa)
{
    capa= document.getElementById(nombreCapa) ;
    capa.style.visibility="visible"; 
} 

function ue_validarporcentaje()
{
    f=document.form1;
    ls_porcentaje=eval("f.txtiva.value");
    if(parseFloat(ls_porcentaje)>100)
    {
        alert("El porcentaje no debe exeder el 100%.");
        f.txtiva.value="";
    }
}

function currencyFormat(fld, milSep, decSep, e)
{ 
    var sep = 0; 
    var key = ''; 
    var i = j = 0; 
    var len = len2 = 0; 
    var strCheck = '0123456789'; 
    var aux = aux2 = ''; 
    var whichCode = (window.Event) ? e.which : e.keyCode; 
    if (whichCode == 13) return true; // Enter 
        if (whichCode == 8)  return true; // Enter 
    key = String.fromCharCode(whichCode); // Get key value from key code 
    if (strCheck.indexOf(key) == -1) return false; // Not a valid key 
    len = fld.value.length; 
    for(i = 0; i < len; i++) 
     if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break; 
    aux = ''; 
    for(; i < len; i++) 
     if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i); 
    aux += key; 
    len = aux.length; 
    if (len == 0) fld.value = ''; 
    if (len == 1) fld.value = '0'+ decSep + '0' + aux; 
    if (len == 2) fld.value = '0'+ decSep + aux; 
    if (len > 2) { 
     aux2 = ''; 
     for (j = 0, i = len - 3; i >= 0; i--) { 
      if (j == 3) { 
       aux2 += milSep; 
       j = 0; 
      } 
      aux2 += aux.charAt(i); 
      j++; 
     } 
     fld.value = ''; 
     len2 = aux2.length; 
     for (i = len2 - 1; i >= 0; i--) 
      fld.value += aux2.charAt(i); 
     fld.value += decSep + aux.substr(len - 2, len); 
    } 
    return false; 
}

</script>
</html>