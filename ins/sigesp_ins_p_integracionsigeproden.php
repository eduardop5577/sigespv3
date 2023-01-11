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
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_integracionsigeproden.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
require_once("../shared/class_folder/grid_param.php");
$io_grid=new grid_param();
require_once("class_folder/sigesp_ins_c_integracionsigeproden.php");
$io_integracion=new sigesp_ins_c_integracionsigeproden();

$ls_gestor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","GESTOR_INT","POSTGRES","C");
$ls_puerto_int=$io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","PUERTO_INT","5432","C");
$ls_servidor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","SERVIDOR_INT","127.0.0.1","C");
$ls_basedatos_int=$io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","BASE_DATOS_INT","db_sigeproden","C");
$ls_login_int=$io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","LOGIN_INT","xxxxxxx","C");
$ls_password_int=trim($io_integracion->uf_select_config("INS","INTEGRACION-SIGEPRODEN","PASSWORD_INT","xxxxxx","C"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Integraci&oacute;n con SIGEPRODEN</title>
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
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	$ls_operacion=$oi_fun_instala->uf_obteneroperacion();
	switch ($ls_operacion) 
	{
		case "BUSCAR":
			$ls_gestor_int=$_POST["txtgestor"];
			$ls_puerto_int=$_POST["txtpuerto"];
			$ls_servidor_int=$_POST["txtservidor"];
			$ls_basedatos_int=$_POST["txtbasedatos"];
			$ls_login_int=$_POST["txtlogin"];
			$ls_password_int=trim($_POST["txtpassword"]);
			$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","GESTOR_INT",$ls_gestor_int,"C");
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","PUERTO_INT",$ls_puerto_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","SERVIDOR_INT",$ls_servidor_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","BASE_DATOS_INT",$ls_basedatos_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","LOGIN_INT",$ls_login_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","PASSWORD_INT",$ls_password_int,"C");
			}
			break;

		case "GUARDAR":
			$ls_gestor_int=$_POST["txtgestor"];
			$ls_puerto_int=$_POST["txtpuerto"];
			$ls_servidor_int=$_POST["txtservidor"];
			$ls_basedatos_int=$_POST["txtbasedatos"];
			$ls_login_int=$_POST["txtlogin"];
			$ls_password_int=trim($_POST["txtpassword"]);
			$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","GESTOR_INT",$ls_gestor_int,"C");
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","PUERTO_INT",$ls_puerto_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","SERVIDOR_INT",$ls_servidor_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","BASE_DATOS_INT",$ls_basedatos_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","LOGIN_INT",$ls_login_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SIGEPRODEN","PASSWORD_INT",$ls_password_int,"C");
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
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
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
         <td height="22" colspan="3" class="titulo-celdanew">Integraci&oacute;n con SIGEPRODEN</td>
      </tr>
      <tr class="formato-blanco">
         <td height="13" colspan="3">&nbsp;</td>
      </tr>
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew"><div align="right"></div>
                <div align="center">Informaci&oacute;n de Conexion SIGEPRODEN </div></td>
          </tr>
          <tr>
            <td width="143" height="21"><div align="right">Gestor</div></td>
            <td height="21" colspan="2"><div align="left">
              <input name="txtgestor" type="text" id="txtgestor" value="<?php print $ls_gestor_int;?>">
            </div></td>
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
            <td height="21" colspan="2"><label>
              <input name="txtbasedatos" type="text" id="txtbasedatos" value="<?php print $ls_basedatos_int;?>">
            </label></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Login</div></td>
            <td height="21" colspan="2"><input name="txtlogin" type="text" id="txtlogin" value="<?php print $ls_login_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Password</div></td>
            <td height="21" colspan="2"><label>
              <input name="txtpassword" type="text" id="txtpassword" value="<?php print $ls_password_int;?>">
            </label></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td width="256" colspan="-1">
              <p>&nbsp;</p>            </td>
            <td width="122">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
  <input name="operacion" type="hidden" id="operacion" value="<?php $_REQUEST["OPERACION"] ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function ue_nuevo()
{
   location.href='sigesp_ins_p_integracionsigeproden.php' 
}

function ue_buscar()
{	
	f=document.form1;
	f.operacion.value="BUSCAR";	
	f.action="sigesp_ins_p_integracionsigeproden.php";
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	if((li_cambiar==1)||(li_incluir==1))
	{
		f.operacion.value="GUARDAR";	
		f.action="sigesp_ins_p_integracionsigeproden.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion.")
	}	
}

function ue_cerrar()
{
   location.href='sigespwindow_blank.php' 
}
</script>
</html>
