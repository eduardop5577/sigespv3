<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
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
require_once("class_folder/class_funciones_viaticos.php");
$io_fun_viaticos=new class_funciones_viaticos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();	
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_d_categorias.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Definici&oacute;n de Carga Familiar</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript" src="js/funcion_scv.js"></script>
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
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset="><style type="text/css">
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
    <td height="30" colspan="7" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="7" class="cd-menu">
			<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Control de Viaticos </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
</td>
  </tr>
  <tr>
    <td height="20" colspan="7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="7" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td width="21" height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Guardar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
    <td width="657" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
</table>
<?php 
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$io_conect= new sigesp_include();
$conn=      $io_conect->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql= new class_sql($conn);
require_once("class_folder/sigesp_scv_c_cargafamiliar.php");
$io_scv= new sigesp_scv_c_cargafamiliar($conn);
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$io_dsclas= new class_datastore();
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion= new class_funciones();
require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php"); 
$io_funciondb= new class_funciones_db($conn);
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg= new class_mensajes();
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
$lb_existe= "";
require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
$io_keygen= new sigesp_c_generar_consecutivo();

$ls_operacion=$io_fun_viaticos->uf_obteneroperacion();
$ls_codcar=$io_fun_viaticos->uf_obtenervalor("txtcodcar","");
$ls_dencar=$io_fun_viaticos->uf_obtenervalor("txtdencar","");
$ls_porcar=$io_fun_viaticos->uf_obtenervalor("txtporcar","");
$ls_estatus=$io_fun_viaticos->uf_obtenervalor("hidestatus","");
switch ($ls_operacion) 
{
	case "NUEVO":
		$ls_codcar= $io_keygen->uf_generar_numero_nuevo("SCV","scv_cargafamiliar","codcar","",4,"","","");
		$ls_dencar="";
		$ls_porcar="";
		$ls_estatus="";
	break;
	case "GUARDAR":
		if ($ls_estatus=="C")
		{
			$lb_existe=$io_scv->uf_scv_select_cargafamiliar($ls_codemp,$ls_codcar);
			if (!$lb_existe)
			{
				$io_msg->message("La Carga Familiar no existe"); 
				break;
			}
			else
			{
				$lb_valido=$io_scv->uf_scv_update_cargafamiliar($ls_codemp,$ls_codcar,$ls_dencar,$ls_porcar,$la_seguridad);
			}
			if($lb_valido)
			{
				$io_msg->message("La  Carga Familiar fue actualizada");
				$ls_codcar="";
				$ls_dencar="";
				$ls_porcar="";
			}	
			else
			{
				$io_msg->message("La  Carga Familiar no pudo ser actualizada");
				$ls_codcar="";
				$ls_dencar="";
				$ls_porcar="";
			}
		}
		else
		{
			$lb_existe=$io_scv->uf_scv_select_cargafamiliar($ls_codemp,$ls_codcar);
			if ($lb_existe)
			{
				$io_msg->message("La  Carga Familiar ya existe"); 
			}
			else
			{
				$lb_valido=$io_scv->uf_scv_insert_cargafamiliar($ls_codemp,$ls_codcar,$ls_dencar,$ls_porcar,$la_seguridad);

				if ($lb_valido)
				{
					$io_msg->message("La  Carga Familiar fue registrada");
					$ls_codcar="";
					$ls_dencar="";
					$ls_porcar="";
				}
				else
				{
					$io_msg->message("No se pudo registrar la  Carga Familiar");
					$ls_codcar="";
					$ls_dencar="";
					$ls_porcar="";
				}
			
			}
		}
	break;
	case "ELIMINAR":
		$lb_existe=$io_scv->uf_scv_select_cargafamiliar($ls_codemp,$ls_codcar);
		if ($lb_existe)
		{
			$lb_valido=$io_scv->uf_scv_delete_cargafamiliar($ls_codemp,$ls_codcar,$la_seguridad);
			if ($lb_valido)
			{
				$io_msg->message("La Carga Familiar fue Eliminada"); 
				$ls_codcar="";
				$ls_dencar="";
				$ls_porcar="";
			}
			else
			{
				$io_msg->message("No se pudo eliminar la  Carga Familiar"); 
			}
		}
		else
		{
			$io_msg->message("La Categor?a de Vi?ticos No Existe");
			$ls_codcar="";
			$ls_dencar="";
			$ls_porcar="";
		}	 
	break;
}
?>
<p align="center"><font size="4" face="Verdana, Arial, Helvetica, sans-serif"></font></p>
<p align="center">&nbsp;</p>
<form name="form1" method="post" action="">
  <?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_viaticos->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_viaticos);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="519" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="517" height="170"><div align="center">
        <table  border="0" cellspacing="0" cellpadding="0" class="formato-blanco" align="center">
          <tr class="titulo-celdanew">
            <td height="22" colspan="2" class="titulo-ventana">Definici&oacute;n de Carga Familiar </td>
          </tr>
          <tr>
            <td height="22" >&nbsp;</td>
            <td height="22" ><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_estatus ?>"></td>
          </tr>
          <tr>
            <td width="122" height="22" ><div align="right">C&oacute;digo</div></td>
            <td width="346" height="22" ><input name="txtcodcar" type="text" id="txtcodcar" value="<?php print  $ls_codcar ?>" size="5" maxlength="4" style="text-transform:uppercase; text-align:center" readonly>
            <input name="operacion" type="hidden" id="operacion"  value="<?php print $ls_operacion?>"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Denominaci&oacute;n</div></td>
            <td height="22"><input name="txtdencar" id="txtdencar" value="<?php print $ls_dencar ?>" type="text" size="60" maxlength="250" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz '+'.,-');"></td>
          </tr>
          <tr> 
            <td height="22"><div align="right">Porcentaje</div></td>
            <td height="22"><input name="txtporcar" type="text" id="txtporcar" value="<?php print  $ls_codcat ?>" size="7" maxlength="4" onKeyPress="return(ue_formatonumero(this,'.',',',event));" style="text-align:right"></td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
  </div>
    </table>
  </div>
</form>
</body>

<script >

function ue_nuevo()
{
   f=document.form1;
   li_incluir=f.incluir.value;	
   if(li_incluir==1)
   {			 
	  f.operacion.value="NUEVO";
	  f.action="sigesp_scv_d_cargafamiliar.php";
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
	evento    =f.hidestatus.value;
	if(((evento=="")&&(li_incluir==1))||(evento=="C")&&(li_cambiar==1))
	{  	
		with (document.form1)
		{
			if (campo_requerido(txtcodcar,"El codigo debe estar lleno")==false)
			{
				txtcodcar.focus();
			}
			else
			{
				if (campo_requerido(txtdencar,"La denominacion debe estar llena")==false)
				{
					txtdencar.focus();
				}
				else
				{
					if (campo_requerido(txtporcar,"El Porcentaje debe estar lleno")==false)
					{
						txtporcar.focus();
					}
					else
					{
						f=document.form1;
						f.operacion.value="GUARDAR";
						f.action="sigesp_scv_d_cargafamiliar.php";
						f.submit();
					}
				}
			}
		}	
	}	
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}		
}					
					
function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{		
		if (f.txtcodcar.value=="")
		{
			alert("No ha seleccionado ning?n registro para eliminar");
		}
		else
		{
			if (confirm("?Esta seguro de eliminar este registro?"))
			{ 
				f=document.form1;
				f.operacion.value="ELIMINAR";
				f.action="sigesp_scv_d_cargafamiliar.php";
				f.submit();
			}
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

		
function campo_requerido(field,mensaje)
{
	with (field) 
	{
		if (value==null||value=="")
		{
			alert(mensaje);
			return false;
		}
		else
		{
			return true;
		}
	}
}
		
		
function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
	   {
		 f.operacion.value="";			
		 pagina="sigesp_scv_cat_cargafamiliar.php";
		 window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,left=50,top=50,resizable=yes,location=no");
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