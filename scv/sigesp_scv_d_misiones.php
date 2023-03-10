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
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_d_misiones.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Definici&oacute;n de Misiones</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
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
require_once("class_folder/sigesp_scv_c_misiones.php");
$io_mision= new sigesp_scv_c_misiones($conn);
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

$ls_operacion=$io_fun_viaticos->uf_obteneroperacion();
$ls_codmis=$io_fun_viaticos->uf_obtenervalor("txtcodmis","");
$ls_denmis=$io_fun_viaticos->uf_obtenervalor("txtdenmis","");
$ls_estatus=$io_fun_viaticos->uf_obtenervalor("hidestatus","NUEVO");
$ls_existe=$io_fun_viaticos->uf_obtenervalor("existe","FALSE");
$ls_codpai=$io_fun_viaticos->uf_obtenervalor("txtcodpai","");
$ls_estdesviaper=$io_fun_viaticos->uf_obtenervalor("chkestdesviaper","0");
$ls_codest=$io_fun_viaticos->uf_obtenervalor("txtcodest","");
$ls_codciu=$io_fun_viaticos->uf_obtenervalor("txtcodciu","");
switch ($ls_operacion) 
{
	case "NUEVO":
		$lb_empresa= true;
		$ls_codmis= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_misiones','codmis');
		if(empty($ls_codmis))
		{
			$io_msg->message($io_funciondb->is_msg_error);
		}
		$ls_existe=false;
	break;
	case "GUARDAR":
		
		//$lb_existe= $io_mision->uf_load_mision($ls_codemp,$ls_codmis);
		if ($ls_existe=="TRUE")
		{ 
			if ($ls_estatus=="NUEVO")
			{
				$io_msg->message("El C?digo de Misi?n ya existe");  
				$lb_valido=false;
			}
			elseif($ls_estatus=="C")
			{
				$lb_valido=$io_mision->uf_update_mision($ls_codemp,$ls_codmis,$ls_denmis,$ls_codpai,$ls_codest,$ls_codciu,$la_seguridad,$ls_estdesviaper);
				if ($lb_valido)
				{
					$io_msg->message("La Misi?n ha sido actualizada");
					$lb_empresa=true;
					$ls_codmis=$io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_misiones','codmis');
					$ls_estatus="NUEVO";
					$ls_denmis="";
					$ls_codpai="";
					$ls_codest="";
					$ls_codciu="";
					$ls_existe=false;
				}
				else
				{
					$io_msg->message("La Misi?n no pudo ser actualizada");
				}
			}  
		}
		else  
		{  
			$lb_valido=$io_mision->uf_insert_mision($ls_codemp,$ls_codmis,$ls_denmis,$ls_codpai,$ls_codest,$ls_codciu,$la_seguridad,$ls_estdesviaper);
			if ($lb_valido)
			{
				$io_msg->message("La Misi?n ha sido registrada");
				$lb_empresa=true;
				$ls_codmis=$io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_misiones','codmis');
				$ls_estatus="NUEVO";
				$ls_denmis="";
				$ls_codpai="";
				$ls_codest="";
				$ls_codciu="";
				$ls_existe=false;
			}
			else
			{
				$io_msg->message("La Misi?n no pudo ser incluida");
				$io_msg->message($io_mision->is_msg_error);
			}
		} 
	break;
	case "ELIMINAR":
		$lb_existe = $io_mision->uf_load_mision($ls_codemp,$ls_codmis);
		if ($lb_existe)
		{
			$lb_valido=$io_mision->uf_delete_mision($ls_codemp,$ls_codmis,$la_seguridad);
			if ($lb_valido)
			{
				$io_msg->message("Registro Eliminado"); 
				$lb_empresa = false;
				$ls_codmis  = $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_misiones','codmis');
				$ls_denmis="";
				$ls_codpai="";
				$ls_codest="";
				$ls_codciu="";
				$ls_existe=false;
			}
			else
			{
				$io_msg->message($io_mision->is_msg_error);
			}
		}
		else
		{
			$io_msg->message("Este Registro No Existe");
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
            <td height="22" colspan="2" class="titulo-ventana">Definici&oacute;n de Misiones </td>
          </tr>
          <tr>
            <td height="22" >&nbsp;</td>
            <td height="22" ><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_estatus ?>">
			                 <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Pa&iacute;s Origen</div></td>
            <td><div align="left">
                <input name="txtcodpai" type="text" id="txtcodpai" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codpai; ?>" size="8" maxlength="3" readonly style="text-align:center ">
                <a href="javascript: ue_buscarpais();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdespai" type="text" class="sin-borde" id="txtdespai"  value="<?php print $ls_despai; ?>" size="45" readonly>
            </div></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Continente</div></td>
            <td><div align="left">
                <input name="txtcodcont" type="text" id="txtcodcont" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codcont; ?>" size="8" maxlength="3" readonly style="text-align:center ">
              -
              <input name="txtdencont" type="text" class="sin-borde" id="txtdencont"  value="<?php print $ls_dencont; ?>" size="45" readonly>
            </div></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Estado Origen</div></td>
            <td><div align="left">
                <input name="txtcodest" type="text" id="txtcodest" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codest; ?>" size="8" maxlength="3" readonly style="text-align:center ">
                <a href="javascript: ue_buscarestado();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdesest" type="text" class="sin-borde" id="txtdesest"  value="<?php print $ls_desest; ?>" size="40" readonly>
            </div></td>
          </tr>
          <tr>
            <td height="22"><div align="right"> Ciudad Origen</div></td>
            <td><div align="left">
                <input name="txtcodciu" type="text" id="txtcodciu" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codciu; ?>" size="8" maxlength="3" readonly style="text-align:center ">
                <a href="javascript: ue_buscarciudad();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdesciu" type="text" class="sin-borde" id="txtdesciu"  value="<?php print $ls_desciu; ?>" size="40" readonly>
            </div></td>
          </tr>
          <tr>
           	<td align="right"><input name="chkestdesviaper" type="checkbox" class="sin-borde" <?php print $ls_estdesviaper;?> id="chkestdesviaper" style="width:15px; height:15px" value="1"></td>
      		<td width="146" align="left">Destino Viatico de Permanencia </td>
          </tr>
          <tr>
            <td width="122" height="22" ><div align="right">C&oacute;digo</div></td>
            <td width="346" height="22" ><input name="txtcodmis" type="text" id="txtcodmis" value="<?php print  $ls_codmis ?>" size="10" maxlength="5" onKeyPress="return keyRestrict(event,'1234567890');" style="text-align:center "  onBlur="javascript:rellenar_cadena(this.value,5);" readonly>
                <input name="operacion" type="hidden" id="operacion"  value="<?php print $ls_operacion?>"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Denominaci&oacute;n</div></td>
            <td height="22"><input name="txtdenmis" id="txtdenmis" value="<?php print $ls_denmis ?>" type="text" size="60" maxlength="254" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz '+'.,-');"></td>
          </tr>
          <tr> 
            <td height="22">&nbsp;</td>
            <td height="22">&nbsp;</td>
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
	  f.hidestatus.value="NUEVO";
	  f.txtdenmis.value="";
	  f.txtdenmis.focus(true);
	  f.action="sigesp_scv_d_misiones.php";
	  f.submit();
   }
   else
   {
		alert("No tiene permiso para realizar esta operacion");
   }
}


function ue_guardar()
{
var resul="";
f=document.form1;
li_incluir=f.incluir.value;
li_cambiar=f.cambiar.value;
evento    =f.hidestatus.value;
		
    if(((evento=="NUEVO")&&(li_incluir==1))||(evento=="C")&&(li_cambiar==1))
    {  	
		 with (document.form1)
		 {
		 if (campo_requerido(txtcodmis,"El codigo de la misi?n debe estar lleno !!")==false)
			{
			  txtcodmis.focus();
			}
		 else
			{
			  if (campo_requerido(txtdenmis,"La denominacion de la misi?n debe estar llena !!")==false)
				 {
				   txtdenmis.focus();
				 }
			   else
				 {
				   f=document.form1;
				   f.operacion.value="GUARDAR";
				   f.action="sigesp_scv_d_misiones.php";
				   f.submit();
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
var borrar="";

  f=document.form1;
  li_eliminar=f.eliminar.value;
  if(li_eliminar==1)
  {		
	   if (f.txtcodmis.value=="")
	   {
		 alert("No ha seleccionado ning?n registro para eliminar");
	   }
		else
		{
			borrar=confirm("? Esta seguro de eliminar este registro ?");
			if (borrar==true)
			   { 
				  f=document.form1;
				  f.operacion.value="ELIMINAR";
				  f.action="sigesp_scv_d_misiones.php";
				  f.submit();
			   }
			else
			   { 
				 alert("Eliminaci?n Cancelada !!!");
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
		 ls_destino="DEFINICION";		
		 pagina="sigesp_scv_cat_misiones.php?destino="+ls_destino+"";
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

function rellenar_cadena(cadena,longitud)
{
var mystring=new String(cadena);
cadena_ceros="";
lencad=mystring.length;
total=longitud-lencad;
for (i=1;i<=total;i++)
	{
	  cadena_ceros=cadena_ceros+"0";
	}
	  cadena=cadena_ceros+cadena;
	  document.form1.txtcodmis.value=cadena;
}
function ue_buscarpais()
{
	f=document.form1;
	window.open("sigesp_scv_cat_pais.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	f.txtcodest.value="";
	f.txtdesest.value="";
	f.txtcodciu.value="";
	f.txtdesciu.value="";
}

function ue_buscarestado()
{
	f=document.form1;
	codpai=f.txtcodpai.value;
	if(codpai!="")
	{
		window.open("sigesp_scv_cat_estados.php?codpai="+codpai+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais.");
	}
	f.txtcodciu.value="";
	f.txtdesciu.value="";

}

function ue_buscarciudad()
{
	f=document.form1;
	ls_codpai=f.txtcodpai.value;
	ls_codest=f.txtcodest.value;
	ls_destino="MISION";
	if((ls_codpai!="")||(ls_codest!=""))
	{
		window.open("sigesp_scv_cat_ciudades.php?hidpais="+ls_codpai+"&hidestado="+ls_codest+"&destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un pais y un estado.");
	}
}

</script>
</html>