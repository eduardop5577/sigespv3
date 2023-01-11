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
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_release.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
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
<title >Release SIGESP </title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">

</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body>
<?php 
	if(array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
	}
	if ($ls_operacion=="EJECUTAR")  
	{  
		require_once("../shared/class_folder/sigesp_release.php");  
		$io_release=new sigesp_release();
		$lb_valido=$io_release->uf_check_update($la_seguridad);
		if($lb_valido)
		{
			$io_release->io_msg->message("Proceso Ejecutado satisfactoriamente.");
		}
		else
		{
			$io_release->io_msg->message("Error al ejecutar Release.");
		}
		$io_release->uf_destructor();
		unset($io_release);
	}
	if ($ls_operacion=="EJECUTAR2011")  
	{  
		require_once("../shared/class_folder/sigesp_release_2011.php");  
		$io_release=new sigesp_release();
		$lb_valido=$io_release->uf_check_update($la_seguridad);
		if($lb_valido)
		{
			$io_release->io_msg->message("Proceso Ejecutado satisfactoriamente.");
		}
		else
		{
			$io_release->io_msg->message("Error al ejecutar Release.");
		}
		$io_release->uf_destructor();
		unset($io_release);
	}
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<form name="form1" method="post" action="">
  <p>
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
</p>
  <p>&nbsp;  </p>
  <table width="442" height="223" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="571" height="221" valign="top">
        <p>&nbsp;</p>
        <table width="353" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-ventana">
            <td><p>Release SIGESP BASE DE DATOS</p>            </td>
          </tr>
          <tr align="right" valign="top" class="formato-blanco">
            <td width="354" height="162" valign="middle" background="../shared/imagebank/release.jpg"><table width="245" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="27">&nbsp;</td>
              </tr>
              <tr>
                <td height="95"><label></label></td>
              </tr>
              <tr>
                <td height="27"><div align="center">
                  <input name="botejecutar" style="height:15" type="button" class="boton" id="botejecutar" value="Ejecutar Release" onClick="javascript:uf_ejecutar();">
                </div></td>
              </tr>
            </table>              
            <label></label></td>
          </tr>
        </table>
		<p>
		   <div id=transferir style="visibility:hidden" align="center"><img src="../shared/imagebank/cargando.gif">Procesando Release... </div>		  
		</p>
        <p>
          <input name="operacion" type="hidden" id="operacion">
          <label>
          <input name="chk2011" type="checkbox" class="sin-borde" id="chk2011" value="checkbox">
          </label>
        Ejecutar Release anteriores al 2022</p>
      </td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
function  uf_ejecutar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{
		mostrar('transferir');		
		if(f.chk2011.checked)
		{
			f.operacion.value="EJECUTAR2011";
		}
		else
		{
			f.operacion.value="EJECUTAR";
		}	
		f.action="sigesp_ins_p_release.php";
		f.submit();
	}
	else
	{
      alert("No tiene permiso para realizar esra operacion");	
	}	
}
function mostrar(nombreCapa)
{
	capa= document.getElementById(nombreCapa) ;
	capa.style.visibility="visible"; 
} 

</script>
</html>