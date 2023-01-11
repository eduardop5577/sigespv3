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
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_d_tarifas.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codtar,$ls_dentar,$li_exterior,$ls_codpai,$ls_denpai,$ls_codreg,$ls_denreg,$li_monbol,$li_mondol;
   		global $li_monpas,$li_monhos,$li_monali,$li_monmov,$ls_codnom,$ls_desnom,$ls_codcat,$ls_dencat,$ls_existe,$ls_operacion,$ls_readonly;
		global $ls_codmis,$ls_denmis,$ls_codmon,$ls_denmon;
		
		$ls_codtar="";
		$ls_dentar="";
		$li_exterior=0;
		$ls_codpai="058";
		$ls_denpai="VENEZUELA";
		$ls_codreg="";
		$ls_denreg="";
		$li_monbol=0;
		$li_mondol=0;
		$li_monpas=0;
		$li_monhos=0;
		$li_monali=0;
		$li_monmov=0;
		$ls_codnom="";
		$ls_desnom="";
		$ls_codcat="";
		$ls_dencat="";
		$ls_codmis="";
		$ls_denmis="";
		$ls_readonly="";
		$ls_codmon="001";
		$ls_denmon="BOLIVAR";
		$ls_existe="FALSE";
		$ls_operacion="NUEVO";
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Definici&oacute;n de Tarifas de Vi&aacute;ticos</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/funciones.js"></script>
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
    <td width="20" height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Guardar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></td>
    <td width="658" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
</table>
<?php 
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=  new class_sql($con);
	require_once("class_folder/sigesp_scv_c_tarifas.php");
	$io_scv= new sigesp_scv_c_tarifas();
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funcion= new class_funciones(); 
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php"); 
	$io_funciondb= new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("class_folder/sigesp_scv_c_tarifas.php");
	$io_scv= new sigesp_scv_c_tarifas();
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];

	$ls_operacion=$io_fun_viaticos->uf_obteneroperacion();	
	$lb_empresa= true;
	switch($ls_operacion)
	{
		case "NUEVO":
			uf_limpiarvariables();
			$ls_estatus="NUEVO";
			$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
			if(empty($ls_codtar))
			{
				$io_msg->message("Error asignando Código de Tarifas");
			}
		break;
		case "EXTERIOR":
			uf_limpiarvariables();
			$ls_estatus="NUEVO";
			$ls_codtar=$io_fun_viaticos->uf_obtenervalor("txtcodtar","");
			$ls_dentar=$io_fun_viaticos->uf_obtenervalor("txtdentar","");
			$li_exterior=$io_fun_viaticos->uf_obtenervalor("hidexterior",0);
			$ls_codreg=$io_fun_viaticos->uf_obtenervalor("txtcodreg","");
			$ls_denreg=$io_fun_viaticos->uf_obtenervalor("txtdenreg","");
			$li_monbol=$io_fun_viaticos->uf_obtenervalor("txtmonbol","0");
			$li_mondol=$io_fun_viaticos->uf_obtenervalor("txtmondol","0");
			$li_monpas=$io_fun_viaticos->uf_obtenervalor("txtmonpas","0");
			$li_monhos=$io_fun_viaticos->uf_obtenervalor("txtmonhos","0");
			$li_monali=$io_fun_viaticos->uf_obtenervalor("txtmonali","0");
			$li_monmov=$io_fun_viaticos->uf_obtenervalor("txtmonmov","0");
			$ls_existe=$io_fun_viaticos->uf_obtenervalor("existe","FALSE");
			$ls_codmis=$io_fun_viaticos->uf_obtenervalor("txtcodmis","");
			$ls_denmis=$io_fun_viaticos->uf_obtenervalor("txtdenmis","");
			if($li_exterior==0)
			{
				$ls_codpai="058";
				$ls_denpai="VENEZUELA";
				$ls_codmon="001";
				$ls_denmon="BOLIVAR";
			}
			else
			{
				$ls_codpai="";
				$ls_denpai="";
				$ls_codmon="";
				$ls_denmon="";
			}
		break;
		case "GUARDAR":
			$ls_codtar= $io_fun_viaticos->uf_obtenervalor("txtcodtar","");
			$ls_dentar= $io_fun_viaticos->uf_obtenervalor("txtdentar","");
			$ls_codcat= $io_fun_viaticos->uf_obtenervalor("txtcodcat","");
			$ls_codnom= $io_fun_viaticos->uf_obtenervalor("txtcodnom","");
			$li_exterior= $io_fun_viaticos->uf_obtenervalor("hidexterior",0);
			$ls_codpai= $io_fun_viaticos->uf_obtenervalor("txtcodpai","");
			$ls_denpai= $io_fun_viaticos->uf_obtenervalor("txtdenpai","");
			$ls_codreg= $io_fun_viaticos->uf_obtenervalor("txtcodreg","-----");
			$ls_denreg= $io_fun_viaticos->uf_obtenervalor("txtdenreg","");
			$li_monbol= $io_fun_viaticos->uf_obtenervalor("txtmonbol",0);
			$li_mondol= $io_fun_viaticos->uf_obtenervalor("txtmondol",0);
			$li_monpas= $io_fun_viaticos->uf_obtenervalor("txtmonpas",0);
			$li_monhos= $io_fun_viaticos->uf_obtenervalor("txtmonhos",0);
			$li_monali= $io_fun_viaticos->uf_obtenervalor("txtmonali",0);
			$li_monmov= $io_fun_viaticos->uf_obtenervalor("txtmonmov",0);
			$ls_estatus= $io_fun_viaticos->uf_obtenervalor("hidestatustar","");
			$ls_existe=$io_fun_viaticos->uf_obtenervalor("existe","FALSE");
			$ls_codmis=$io_fun_viaticos->uf_obtenervalor("txtcodmis","");
			$ls_denmis=$io_fun_viaticos->uf_obtenervalor("txtdenmis","");
			$ls_codmon=   $io_fun_viaticos->uf_obtenervalor("txtcodmon","");
			$ls_denmon=   $io_fun_viaticos->uf_obtenervalor("txtdenmon","");
			$li_monbol=    str_replace(".","",$li_monbol);
			$li_monbol=    str_replace(",",".",$li_monbol);
			$li_mondol=    str_replace(".","",$li_mondol);
			$li_mondol=    str_replace(",",".",$li_mondol);
			$li_monpas=    str_replace(".","",$li_monpas);
			$li_monpas=    str_replace(",",".",$li_monpas);
			$li_monhos=    str_replace(".","",$li_monhos);
			$li_monhos=    str_replace(",",".",$li_monhos);
			$li_monali=    str_replace(".","",$li_monali);
			$li_monali=    str_replace(",",".",$li_monali);
			$li_monmov=    str_replace(".","",$li_monmov);
			$li_monmov=    str_replace(",",".",$li_monmov);
			$li_monbol= round($li_monbol,2);
			$li_mondol=  round($li_mondol,2);
			$li_monpas= round($li_monpas,2);
			$li_monhos= round($li_monhos,2);
			$li_monali= round($li_monali,2);
			$li_monmov= round($li_monmov,2);
			if($ls_estatus=="C")
			{
				$lb_existe=$io_scv->uf_select_tarifa($ls_codemp,$ls_codtar); 
				if ($ls_existe=="TRUE")
				{
					$lb_valido=$io_scv->uf_update_tarifa($ls_codemp,$ls_codtar,$ls_dentar,$ls_codpai,$ls_codreg,$li_monbol,
														 $li_mondol,$li_monpas,$li_monhos,$li_monali,$li_monmov,$li_exterior,
														 $ls_codcat,$ls_codnom,$ls_codmis,$ls_codmon,$la_seguridad);
					if($lb_valido)
					{
						uf_limpiarvariables();
						$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
						$io_msg->message("La tarifa de viáticos ha sido actualizada");
					} 
					else
					{	
						uf_limpiarvariables();
						$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
						$io_msg->message("No se pudo actualizar la tarifa de viáticos");
					}
				}
				else
				{
					uf_limpiarvariables();
					$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
					$io_msg->message("La tarifa a actulizar no se encuentra registrada");
				}
			}
			else
			{
					$arrResultado=$io_scv->uf_insert_tarifa($ls_codemp,$ls_codtar,$ls_dentar,$ls_codpai,$ls_codreg,$li_monbol,
														    $li_mondol,$li_monpas,$li_monhos,$li_monali,$li_monmov,$li_exterior,
														    $ls_codcat,$ls_codnom,$ls_codmis,$ls_codmon,$la_seguridad);
					$ls_codtar=$arrResultado['as_codtar'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						uf_limpiarvariables();
						$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
						$io_msg->message("La tarifa de viáticos ha sido registrada");
					}
					else
					{
						uf_limpiarvariables();
						$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
						$io_msg->message("No se pudo registrar la tarifa de viáticos");
					}
			}
			
		break;
		case "BUSCAR":
			$ls_codtar= $io_fun_viaticos->uf_obtenervalor("txtcodtar","");
			$ls_dentar= trim($io_fun_viaticos->uf_obtenervalor("txtdentar",""));
			$ls_codcat= $io_fun_viaticos->uf_obtenervalor("txtcodcat","");
			$ls_dencat= $io_fun_viaticos->uf_obtenervalor("txtdencat","");
			$ls_codnom= $io_fun_viaticos->uf_obtenervalor("txtcodnom","");
			$ls_desnom= $io_fun_viaticos->uf_obtenervalor("txtdesnom","");
			$ls_estatus=$io_fun_viaticos->uf_obtenervalor("hidestatustar",0);
			$ls_existe=$io_fun_viaticos->uf_obtenervalor("existe","FALSE");
			$rs_data=$io_scv->uf_load_tarifa($ls_codemp,$ls_codtar);
			$ls_codmis=$io_fun_viaticos->uf_obtenervalor("txtcodmis","");
			$ls_denmis=$io_fun_viaticos->uf_obtenervalor("txtdenmis","");
			$ls_codmon=   $io_fun_viaticos->uf_obtenervalor("txtcodmon","");
			$ls_denmon=   $io_fun_viaticos->uf_obtenervalor("txtdenmon","");
			if($rs_data===false)
			{
				uf_limpiarvariables();
				$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
			}
			else
			{
				$row=$io_sql->fetch_row($rs_data);
				$ls_codpai= $row["codpai"];
				$ls_denpai= $row["despai"];
				$ls_codreg= $row["codreg"]; 
				$ls_denreg= $row["denreg"]; 
				$li_monbol= $row["monbol"]; 
				$li_mondol= $row["mondol"]; 
				$li_monpas= $row["monpas"]; 
				$li_monhos= $row["monhos"]; 
				$li_monali= $row["monali"]; 
				$li_monmov= $row["monmov"]; 
				$li_exterior=$row["nacext"];
				$ls_readonly="readonly";
			}

		break;
		case "ELIMINAR":
			$ls_codtar= $io_fun_viaticos->uf_obtenervalor("txtcodtar","");
			$lb_valido=$io_scv->uf_delete_tarifa($ls_codemp,$ls_codtar,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
				$io_msg->message("La tarifa de viáticos ha sido eliminada");
			}
			else
			{
				uf_limpiarvariables();
				$ls_codtar= $io_funciondb->uf_generar_codigo($lb_empresa,$ls_codemp,'scv_tarifas','codtar');
				$io_msg->message($io_scv->is_msg_error);
				$io_msg->message("No se pudo eliminar la tarifa de viáticos");
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
  <table width="599" height="222" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="517" height="170"><div align="center">
        <table width="563"  border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-celdanew">
            <td height="22" colspan="2" class="titulo-ventana">Definici&oacute;n de Tarifas de Vi&aacute;ticos </td>
          </tr>
          <tr>
            <td height="22" >&nbsp;</td>
            <td height="22" ><input name="hidestatustar" type="hidden" id="hidestatustar" value="<?php print $ls_estatus ?>">
              <input name="hidestatus" type="hidden" id="hidestatus">
			  <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>"></td>
          </tr>
          <tr>
            <td width="173" height="22" ><div align="right">C&oacute;digo</div></td>
            <td width="388" height="22" ><input name="txtcodtar" type="text" id="txtcodtar" value="<?php print  $ls_codtar ?>" size="10" maxlength="5" onKeyPress="return keyRestrict(event,'abcdefghijklmnñopqrstwxyz1234567890');" style="text-align:center "  onBlur="javascript:rellenar_cadena(this.value,3);" readonly>
                <input name="operacion" type="hidden" id="operacion"  value="<?php print $ls_operacion?>"></td>
          </tr>
          <tr>
            <td height="25"><div align="right">Concepto</div></td>
            <td height="22" rowspan="2"><textarea name="txtdentar" cols="60" id="txtdentar" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnopqrstuvwxyz ()#!%/[]*-+_');"><?php print $ls_dentar ?></textarea></td>
          </tr>
          <tr>
            <td height="11">&nbsp;</td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td height="22">
                <input name="chkexterior" type="checkbox" class="sin-borde" onChange="javascript:ue_chkexterior();" value="checkbox"<?php if($li_exterior==1){ print "checked";}   ?>>
            <label>Exterior            
            <input name="hidexterior" type="hidden" id="hidexterior" value="<?php print $li_exterior  ?>">
            </label></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Categor&iacute;a</div></td>
            <td height="22"><input name="txtcodcat" type="text" id="txtcodcat" style="text-align:center" value="<?php print $ls_codcat;  ?>" size="5" readonly>
                <a href="javascript:ue_catanomina();"></a><a href="javascript:ue_catacategorias();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtdencat" type="text" class="sin-borde" id="txtdencat" value="<?php print $ls_dencat;  ?>" size="50"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">N&oacute;mina</div></td>
            <td height="22"><input name="txtcodnom" type="text" id="txtcodnom" style="text-align:center" value="<?php print $ls_codnom;  ?>" size="8" readonly>
                <a href="javascript:ue_catanomina();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a> <a href="javascript:ue_catacategorias();"></a>
                <input name="txtdesnom" type="text" class="sin-borde" id="txtdesnom" value="<?php print $ls_desnom;  ?>" size="50"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Pa&iacute;s</div></td>
            <td height="22"><input name="txtcodpai" type="text" id="txtcodpai" value="<?php print $ls_codpai;  ?>" size="6" style="text-align:center" readonly>
			<?php
//			if($li_exterior==1)
//			{
			?>
			  <a href="javascript:ue_catapais();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
			<?php
//			}
			?>             
			  <input name="txtdespai" type="text" class="sin-borde" id="txtdespai" value="<?php print $ls_denpai  ?>" size="40" readonly>            </td>
          </tr>
          <tr>
			<?php
			if($li_exterior==0)
			{
			?>
            <td height="22"><div align="right">Regi&oacute;n</div></td>
            <td height="22"><input name="txtcodreg" type="text" id="txtcodreg" value="<?php print $ls_codreg  ?>" size="10" style="text-align:center" readonly>
              <a href="javascript:ue_cataregion();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
              <label>
              <input name="txtdenreg" type="text" class="sin-borde" id="txtdenreg" value="<?php print $ls_denreg  ?>" size="40" readonly>
              </label></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Total Bs. </div></td>
            <td height="22"><input name="txtmonbol" type="text" id="txtmonbol" value="<?php print number_format($li_monbol,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr> 
            <td height="22"><div align="right">Monto Pasaje </div></td>
            <td height="22"><input name="txtmonpas" type="text" id="txtmonpas" value="<?php print number_format($li_monpas,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Hospedaje </div></td>
            <td height="22"><input name="txtmonhos" type="text" id="txtmonhos" value="<?php print number_format($li_monhos,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Alimentaci&oacute;n </div></td>
            <td height="22"><input name="txtmonali" type="text" id="txtmonali" value="<?php print number_format($li_monali,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Movilizaci&oacute;n </div></td>
            <td height="22"><input name="txtmonmov" type="text" id="txtmonmov" value="<?php print number_format($li_monmov,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
			<?php
			}
			else
			{
			?>             
          <tr>
            <td height="22"><div align="right">Monto $ </div></td>
            <td height="22"><input name="txtmondol" type="text" id="txtmondol" value="<?php print number_format($li_mondol,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr> 
            <td height="22"><div align="right">Monto Pasaje </div></td>
            <td height="22"><input name="txtmonpas" type="text" id="txtmonpas" value="<?php print number_format($li_monpas,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Hospedaje </div></td>
            <td height="22"><input name="txtmonhos" type="text" id="txtmonhos" value="<?php print number_format($li_monhos,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Alimentaci&oacute;n </div></td>
            <td height="22"><input name="txtmonali" type="text" id="txtmonali" value="<?php print number_format($li_monali,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Monto Movilizaci&oacute;n </div></td>
            <td height="22"><input name="txtmonmov" type="text" id="txtmonmov" value="<?php print number_format($li_monmov,2,",",".");  ?>" onKeyPress="return(ue_formatonumero(this,'.',',',event));" size="15" style="text-align:right" <?php print $ls_readonly; ?>></td>
          </tr>
			<?php
			}
			?>             
            <tr>
              <td height="22"><div align="right">Mision</div></td>
              <td height="22"><input name="txtcodmis" type="text" id="txtcodmis" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_codmis; ?>" size="10" maxlength="5" readonly style="text-align:center ">
                <a href="javascript: ue_buscarmision();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdenmis" type="text" class="sin-borde" id="txtdenmis"  value="<?php print $ls_denmis; ?>" size="50" readonly>
              <input name="txtobssolvia" type="hidden" id="txtobssolvia"></td>
            </tr>
            <tr>
              <td height="22" align="right">Tipo de moneda </td>
              <td height="22"><input name="txtcodmon" type="text" id="txtcodmon" value="<?php print $ls_codmon; ?>" size="4" maxlength="3" readonly>
                  <a href="javascript:uf_buscar_moneda();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Moneda"></a>
                  <input name="txtdenmon" type="text" class="sin-borde" id="txtdenmon" value="<?php print $ls_denmon; ?>" size="30" readonly>
                </a></td>
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

function ue_catanomina()
{
	window.open("sigesp_scv_cat_nominas.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catacategorias()
{
	window.open("sigesp_scv_cat_categorias.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catapais()
{
	f= document.form1;
	if(f.chkexterior.checked==true){
		window.open("sigesp_scv_cat_pais.php?esttarvia=0","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("Opcion solo valida para viajes al exterior");
	}
}

function ue_cataregion()
{
	f=document.form1;
	codpai=f.txtcodpai.value
	window.open("sigesp_scv_cat_regiones.php?catalogo=1&hidpais="+codpai+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}
function ue_buscarmision()
{
	ls_destino="SOLICITUD";
	window.open("sigesp_scv_cat_misiones.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}

function uf_buscar_moneda()
{
	f= document.form1;
	if(f.chkexterior.checked==true)
	{
		pagina= "sigesp_cat_moneda.php";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=500,left=50,top=50,resizable=yes,location=no");
	}
	else
	{
		alert("Opcion solo valida para viajes al exterior");
	}
} 		
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;	
	if(li_incluir==1)
	{			 
		f.operacion.value="NUEVO";
		f.action="sigesp_scv_d_tarifas.php";
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
	evento=f.hidestatustar.value;
	chkexterior=f.chkexterior;
	if(((evento=="NUEVO")&&(li_incluir==1))||(evento=="C")&&(li_cambiar==1))
	{  	
		with (document.form1)
		{
			if (campo_requerido(txtcodtar,"El codigo de la tarifa debe estar lleno")==false)
			{txtcodtar.focus();}
			else
			{
				if (campo_requerido(txtdentar,"La denominacion de la tarifa debe estar llena")==false)
				{txtdendentar.focus();}
				else
				{
					if (campo_requerido(txtcodcat,"Debe indicar la categoria de la tarifa")==false)
					{txtcodcat.focus();}
					else
					{
						
						if (f.chkexterior.checked==false)
						{
							if (campo_requerido(txtcodreg,"Debe indicar la region de la tarifa")==false)
							{txtcodreg.focus();}
							else
							{
								f=document.form1;
								f.operacion.value="GUARDAR";
								f.action="sigesp_scv_d_tarifas.php";
								f.submit();	
							}
						}
						else
						{
							if(f.txtcodmon.value=="")
							{
								alert("Si configuro la tatifa como exterior, debe indicar la Moneda");
							}
							else
							{
								f=document.form1;
								f.operacion.value="GUARDAR";
								f.action="sigesp_scv_d_tarifas.php";
								f.submit();
							}
						}
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
		if (f.txtcodtar.value=="")
		{
			alert("No ha seleccionado ningún registro para eliminar");
		}
		else
		{
			if(confirm("¿Seguro desea eliminar el Registro?"))
			{ 
				f=document.form1;
				f.operacion.value="ELIMINAR";
				f.action="sigesp_scv_d_tarifas.php";
				f.submit();
			}
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_chkexterior()
{
	f=document.form1;
	if(f.chkexterior.checked==true)
	{
		f.hidexterior.value=1;
	}
	else
	{
		f.hidexterior.value=0;
	}
	if(f.chkexterior.checked==false)
	{
		f.txtcodmon.value="";
		f.txtdenmon.value="";
	}
	f.operacion.value="EXTERIOR";
	f.action="sigesp_scv_d_tarifas.php";
	f.submit();
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
	{
		f.operacion.value="";	
		ls_destino="DEFINICION";		
		pagina="sigesp_scv_cat_tarifas.php?destino="+ls_destino+"";
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
	document.form1.txtcodtar.value=cadena;
}
</script>
</html>