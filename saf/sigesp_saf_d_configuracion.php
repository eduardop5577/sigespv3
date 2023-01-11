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
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$io_fun_activo=new class_funciones_activos();
	$ls_permisos="";
	$la_seguridad = array();
	$la_permisos = array();
	$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_d_configuracion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	require_once("sigesp_saf_c_activo.php");
    $io_saf_tipcat= new sigesp_saf_c_activo();
    $ls_rbtipocat= $io_saf_tipcat->uf_select_valor_config($ls_codemp);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_rbcsc,$ls_rbcgr,$ls_disabled;
		
		$ls_rbcsc="";
		$ls_rbcgr="";
		$ls_disabled="";
   }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Configuraci&oacute;n</title>
<script type="text/javascript" src="shared/js/disabled_keys.js"></script>
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<title >Definici&oacute;n de Activos</title>
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
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/stm31.js"></script>
</head>
<body>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
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
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();" ><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" title="Guardar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></div></td>
    <td class="toolbar" width="22">&nbsp;</td>
    <td class="toolbar" width="640">&nbsp;</td>
  </tr>
</table>
<p>
  <?php
	require_once("sigesp_saf_c_activo.php");
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_saf=  new sigesp_saf_c_activo();
	$io_msg=  new class_mensajes();

	if( array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_rbcsc="";
		$ls_rbcgr="";
		$disabled="";
	}
	else
	{
		$ls_operacion="";
		uf_limpiarvariables();
		$ls_rbtipocat=$io_saf->uf_select_valor_config($ls_codemp);
		switch ($ls_rbtipocat) 
		{
			case '0':
		        uf_limpiarvariables();
			break;
			
			case '1':
				 $ls_rbcsc="checked";
				 $ls_disabled="disabled";
			break;
			
			case '2':
				$ls_rbcgr="checked";
				$ls_disabled="disabled";
			break;
		}
		$ls_tipafedep ="";
	    $arrResultado = $io_saf->uf_load_config("SAF","DEPRECIACION","AFECTACION_DEPRECIACION",$ls_tipafedep);
		$ls_tipafedep = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		switch($ls_tipafedep){
			case 'P':
				 $ls_rbdeppre  = "checked";
				 $ls_distipafe = "disabled";
				 $ls_rbdepcon  = "";
			break;
			case 'C':
				$ls_rbdeppre  = "";
				$ls_rbdepcon  = "checked";
				$ls_distipafe = "disabled";
			break;
		}
	}
		$arrResultado = $io_saf->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estfecinc);
		$ls_estfecinc = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		switch($ls_estfecinc){
			case '1':
				 $ls_rbactivo  = "checked";
				 $ls_rbinactivo  = "";
				 $ls_habilitado = "disabled";
			break;
			case '0':
				$ls_rbactivo  = "";
				$ls_rbinactivo  = "checked";
				$ls_habilitado = "disabled";
			break;
			case '':
				$ls_rbinactivo  = "";
				$ls_rbactivo  = "";
				$ls_habilitado = "";
			break;
		}
		$ls_habcontable= "";
	    $arrResultado = $io_saf->uf_load_config("SAF","CONFIGURACION","GENERAR_CONTABLE",$ls_estcon);
		$ls_estcon = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		switch($ls_estcon){
			case '1':
				 $ls_rbchkconact  = "checked";
				 $ls_rbchkconina  = "";
				 $ls_habcontable= "disabled";
			break;
			case '0':
				$ls_rbchkconact  = "";
				$ls_rbchkconina  = "checked";
				$ls_habcontable= "disabled";
			break;
			case '':
				$ls_rbchkconact  = "";
				$ls_rbchkconina  = "";
				$ls_habcontable= "";
			break;
		}
	if($ls_operacion == "NUEVO")
	{
		uf_limpiarvariables();
	}
	if($ls_operacion == "GUARDAR")
	{
		if (array_key_exists("rbtipocat",$_POST))
		   {
			 $ls_rbtipocat=$_POST["rbtipocat"];
			 switch ($ls_rbtipocat) 
			 {
				case 'CSC':
					 $ls_rbtipocat="1";
					 $ls_rbcsc="checked";
					 $ls_disabled="disabled";
				break;
				
				case 'CGR':
					$ls_rbtipocat="2";
					$ls_rbcgr="checked";
					$ls_disabled="disabled";
				break;
				
			}
		   }
		else
		   {
			 $ls_rbtipocat = "";
			 $ls_rbcgr     = "";
			 $ls_disabled  = "disabled";
		   }
		   
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sistema="SAF"; 
		$ls_seccion="CATEGORIA"; 
		$ls_variable="TIPO-CATEGORIA-CSG-CGR"; 
		$ls_valor=$ls_rbtipocat; 
		$ls_tipo="C";
	    $lb_valido=$io_saf->uf_saf_guardar_configuracion($ls_codemp,$ls_sistema, $ls_seccion, $ls_variable, $ls_valor, $ls_tipo);
		if (array_key_exists("radiotipdep",$_POST))
		   {
			 $ls_tipdep = $_POST["radiotipdep"];
			 switch ($ls_tipdep) 
			 {
				case 'P':
					 $ls_rbdeppre  = "checked";
					 $ls_distipafe = "disabled";
					 $ls_rbdepcon  = "";
				break;
				
				case 'C':
				    $ls_rbdeppre  = "";
					$ls_rbdepcon  = "checked";
					$ls_distipafe = "disabled";
				break;
			 }
		   }
		else
		   {
			 $ls_rbdeppre  = "";
			 $ls_distipafe = "disabled";
			 $ls_rbdepcon  = "";
		   }
		$arrResultado = $io_saf->uf_load_config("SAF","DEPRECIACION","AFECTACION_DEPRECIACION",$ls_value);
		$ls_value = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		if (!$lb_existe)
		   {
		     $lb_valido = $io_saf->uf_insert_config($ls_codemp,"SAF","DEPRECIACION","AFECTACION_DEPRECIACION",$ls_tipdep,"C");
		   }   
		   
		if (array_key_exists("rbestinc",$_POST))
		   {
			 $ls_estfecinc = $_POST["rbestinc"];
				switch($ls_estfecinc){
					case '1':
						 $ls_rbactivo  = "checked";
						 $ls_rbinactivo  = "";
						 $ls_habilitado = "disabled";
					break;
					case '0':
						$ls_rbactivo  = "";
						$ls_rbinactivo  = "checked";
						$ls_habilitado = "disabled";
					break;
					case '':
						$ls_rbinactivo  = "";
						$ls_rbactivo  = "";
						$ls_habilitado = "";
					break;
				}
		   }
		else
		   {
				$ls_rbinactivo  = "";
				$ls_rbactivo  = "";
				$ls_habilitado = "";
		   }
		$arrResultado = $io_saf->uf_load_config("SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estfecinc);
		$ls_estfecinc = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		if (!$lb_existe)
		   {
		     $lb_valido = $io_saf->uf_insert_config($ls_codemp,"SAF","DEPRECIACION","MODIFICACION_INCORPORACION",$ls_estfecinc,"C");
		   }   
		   
		if (array_key_exists("rbestcon",$_POST))
		{
			$ls_rbestcon = $_POST["rbestcon"];
		}
		$arrResultado = $io_saf->uf_load_config("SAF","CONFIGURACION","GENERAR_CONTABLE",$ls_rbestcon);
		$ls_rbestcon = $arrResultado['ls_value'];
		$lb_existe=$arrResultado['lb_existe'];
		if (!$lb_existe)
		   {
		     $lb_valido = $io_saf->uf_insert_config($ls_codemp,"SAF","CONFIGURACION","GENERAR_CONTABLE",$ls_rbestcon,"C");
		   }   
			switch($ls_rbestcon){
				case '1':
					 $ls_rbchkconact  = "checked";
					 $ls_rbchkconina  = "";
				break;
				case '0':
					$ls_rbchkconact  = "";
					$ls_rbchkconina  = "checked";
				break;
				case '':
					$ls_rbchkconact  = "";
					$ls_rbchkconina  = "";
				break;
			}
		   
		   
		   
		if ($lb_valido)
		   {
		     $io_msg->message("Se guardo la configuracion con exito");  
		   }
	}
		
?>
</p>
<p>&nbsp;</p>
<form id="form1" name="form1" method="post" action="">
         <?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>

  <table width="500" border="0" align="center" class="formato-blanco">
    <tr>
      <td colspan="3" class="titulo-ventana"align="center">Configuraci&oacute;n
        <input name="operacion" type="hidden" id="operacion" value="<?php  print $ls_operacion; ?>" />      </td>
    </tr>
    <tr>
      <td width="173" style="text-align:right"><strong>Normativa de Activos Fijos</strong></td>
      <td width="208">&nbsp;</td>
      <td width="103">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
          <input name="rbtipocat" type="radio" class="sin-borde" value="CSC" <?php print $ls_rbcsc; ?> <?php print $ls_disabled; ?>>
          Manual del SIGECOF
          <input name="rbtipocat" type="radio" class="sin-borde" value="CGR" <?php print $ls_rbcgr; ?> <?php print $ls_disabled; ?>>
      Publicaciones CGR </div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><strong>Afectaci&oacute;n de la Depreciaci&oacute;n </strong></td>
    </tr>
    <tr>
      <td colspan="3">
      <div align="center">
        <label>
        <input name="radiotipdep" type="radio" class="sin-borde" value="P" <?php print $ls_rbdeppre; ?> <?php print $ls_distipafe; ?> />
        </label>
      Presupuestaria 
      <label>
      <input name="radiotipdep" type="radio" class="sin-borde" value="C" <?php print $ls_rbdepcon; ?> <?php print $ls_distipafe; ?> />
      </label>
      Contable</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><strong>Fecha de Incorporación Automatica (SUDEBAN)</strong></td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <input name="rbestinc" type="radio" class="sin-borde" value="1"  <?php print $ls_rbactivo; ?> <?php print $ls_habilitado; ?> />
      Si  
      <label>
        <input name="rbestinc" type="radio" class="sin-borde" value="0"  <?php print $ls_rbinactivo; ?> <?php print $ls_habilitado; ?> />
        </label>
      No</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><strong>Generar Asientos Contables </strong></td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">
        <input name="rbestcon" type="radio" class="sin-borde" value="1"  <?php print $ls_rbchkconact; ?> <?php print $ls_habcontable; ?> />
Si
<label>
<input name="rbestcon" type="radio" class="sin-borde" value="0"  <?php print $ls_rbchkconina; ?> <?php print $ls_habcontable; ?> />
</label>
No</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
 
</form>
<p>&nbsp;</p>
</body>
<script >

function ue_guardar()
{
	f=document.form1;
	if ((f.rbtipocat[0].checked)||(f.rbtipocat[1].checked))
	   {
	     if (f.radiotipdep[0].checked || f.radiotipdep[1].checked)
		    {
			  f.operacion.value ="GUARDAR";
			  f.action="sigesp_saf_d_configuracion.php";
			  f.submit();
			}
		 else
		    {
			  alert("Seleccione el Tipo de Afectación de la Depreciación !!!");
			}
	}
	else
	{
	 alert("Seleccione una de las opciones de Configuracion");
	} 
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}
</script>
</html>
