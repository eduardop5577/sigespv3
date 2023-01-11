<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
require_once("class_funciones_activos.php");
$io_fun_activo=new class_funciones_activos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_d_sede.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
require_once("sigesp_saf_c_activo.php");
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$io_saf_tipcat= new sigesp_saf_c_activo();
$ls_rbtipocat=$io_saf_tipcat->uf_select_valor_config($ls_codemp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Funci�n que limpia todas las variables necesarias en la p�gina
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codsed,$ls_densed,$ls_dirsed;
		
		$ls_codsed="";
		$ls_densed="";
		$ls_dirsed="";
   }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
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
<title >Definici&oacute;n de Sede</title>
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
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Activos Fijos</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
  <?php 
    if ($ls_rbtipocat == 1) 
    {
   ?>
   <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_csc.js"></script></td>
  <?php 
    }
	elseif ($ls_rbtipocat == 2)
	{
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_cgr.js"></script></td>
  <?php 
	}
	else
	{
   ?>
	<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  <?php 
	}
   ?>
    <!-- <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td> -->
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" title="Guardar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" title="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"></a><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="580">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("sigesp_saf_c_sede.php");
	$io_saf= new sigesp_saf_c_sede();
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= 	 $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun= new class_funciones_db($con);

	$arre=$_SESSION["la_empresa"];
	$ls_empresa=$arre["codemp"];
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
		uf_limpiarvariables();
	}
	if ($ls_operacion=="GUARDAR")
	{
		$ls_valido= false;
		$ls_codsed=$_POST["txtcodsed"];
		$ls_densed=$_POST["txtdensed"];
		$ls_dirsed=$_POST["txtdirsed"];
		$ls_status=$_POST["hidstatus"];
		if(($ls_codsed=="")||($ls_densed=="")||($ls_dirsed==""))
		{
			$io_msg->message("Debe compeltar todos los campos");
		}
		else
		{
			if ($ls_status=="C")
			{
				$lb_encontrado=$io_saf->uf_saf_select_sede($ls_codsed);
				if($lb_encontrado)
				{
					$lb_valido=$io_saf->uf_saf_update_sede($ls_codsed,$ls_densed,$ls_dirsed,$la_seguridad);
					if($lb_valido)
					{
						$io_msg->message("La Sede fue actualizada");
						uf_limpiarvariables();
						
					}	
					else
					{
						$io_msg->message("No se pudo actualizar la Sede");
						uf_limpiarvariables();
					}
				}
				else
				{
					$io_msg->message("La Sede no existe");
					uf_limpiarvariables();
				}
			}
			else
			{
				$lb_encontrado=$io_saf->uf_saf_select_sede($ls_codsed);
				if ($lb_encontrado)
				{
					$io_msg->message("Registro ya existe"); 
					uf_limpiarvariables();
				}
				else
				{
					$lb_valido=$io_saf->uf_saf_insert_sede($ls_codsed,$ls_densed,$ls_dirsed,$la_seguridad);
					if ($lb_valido)
					{
						$io_msg->message("La Sede fue registrada.");
						uf_limpiarvariables();
					}
					else
					{
						$io_msg->message("No se pudo registrar la Sede");
						uf_limpiarvariables();
					}
				}
			}
		}
	}
	elseif ($ls_operacion=="ELIMINAR")
	{
		$ls_codsed=$_POST["txtcodsed"];
		$lb_valido=$io_saf->uf_saf_delete_sede($ls_codsed,$la_seguridad);
		if($lb_valido)
		{
			$io_msg->message("El registro fue eliminado");
			uf_limpiarvariables();
		}	
		else
		{
			$io_msg->message("No se pudo eliminar el registro");
			uf_limpiarvariables();
		}
	}
	elseif($ls_operacion=="NUEVO")
	{
		uf_limpiarvariables();
	}
?>

<p>&nbsp;</p>
<div align="center">
  <table width="596" height="209" border="0" class="formato-blanco">
    <tr>
      <td width="588" height="203"><div align="left">
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>	
<table width="588" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><table width="586" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
                  <tr>
                    <td colspan="2" class="titulo-ventana">Definici&oacute;n de Sede </td>
                  </tr>
                  <tr class="formato-blanco">
                    <td width="124" height="19">&nbsp;</td>
                    <td width="452"><input name="txtempresa" type="hidden" id="txtempresa" value="<?php print $ls_empresa ?>">                      </td>
                  </tr>
                  <tr class="formato-blanco">
                    <td height="22"><div align="right">C&oacute;digo</div></td>
                    <td height="22"><input name="txtcodsed" type="text" id="txtcodsed" value="<?php print $ls_txtcodsed; ?>" size="12" maxlength="10" onKeyPress="return keyRestrict(event,'1234567890-');" style="text-align:center ">
                      <input name="hidstatus" type="hidden" id="hidstatus"></td>
                  </tr>
                  <tr class="formato-blanco">
                    <td height="26"><div align="right">Denominaci&oacute;n</div></td>
                    <td><input name="txtdensed" type="text" id="txtdensed" onBlur="javascript: ue_validarcomillas(this);" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_densed; ?>" size="50" maxlength="100">                    </td>
                  </tr>
                  <tr>
                    <td height="20"><div align="right">Direccion</div></td>
                    <td height="20"><input name="txtdirsed" type="text" id="txtdirsed" value="<?php print $ls_dirsed; ?>" size="80" maxlength="500" onBlur="javascript: ue_validarcomillas(this);" onKeyUp="javascript: ue_validarcomillas(this);" ></td>
                  </tr>
                  <tr class="formato-blanco">
                    <td height="19"><div align="right"></div></td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table>
              <input name="operacion" type="hidden" id="operacion">
          </form>
      </div></td>
    </tr>
  </table>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones
function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_saf_cat_sede.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_saf_d_sede.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}
function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_status=f.hidstatus.value;
	ls_codsed=f.txtcodsed.value;
	ls_densed=f.txtdensed.value;
	ls_dirsed=f.txtdirsed.value;
	if((ls_codsed=="")||(ls_densed=="")||(ls_dirsed==""))
	{
		alert("Debe llenar todos los campos");
	}
	else
	{
		if(((lb_status=="")&&(li_incluir==1))||(lb_status=="C")&&(li_cambiar==1))
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_saf_d_sede.php";
			f.submit();
		}
		else
		{alert("No tiene permiso para realizar esta operacion");}
	}	
}
function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(confirm("�Seguro desea eliminar el Registro?"))
		{
			f.operacion.value="ELIMINAR";
			f.action="sigesp_saf_d_sede.php";
			f.submit();
		}
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}
function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

</script> 
</html>