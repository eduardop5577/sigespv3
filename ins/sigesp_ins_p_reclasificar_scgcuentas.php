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
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_reclasificar_scgcuentas.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Reclasificar cuentas contables</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">

</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php 
	require_once("class_folder/sigesp_ins_c_reclasificar_scg_cuentas.php");
	$io_reclasificar=new sigesp_ins_c_reclasificar_scg_cuentas();
	$lb_valido=false;
	$ls_operacion=$oi_fun_instala->uf_obteneroperacion();
	if($ls_operacion=="EJECUTAR")
	{
		$ls_archivoscgcuentas=$_FILES['txtarchivoscgcuentas']['name'];
		if($ls_archivoscgcuentas!="")
		{
			$ls_tiparc=$_FILES['txtarchivoscgcuentas']['type']; 
			$ls_tamarc=$_FILES['txtarchivoscgcuentas']['size']; 
			$ls_nomtemarc=$_FILES['txtarchivoscgcuentas']['tmp_name'];
			$ls_archivoscgcuentas=$_SESSION['la_logusr'];
			$lb_valido=$io_reclasificar->uf_upload($ls_archivoscgcuentas,$ls_tiparc,$ls_tamarc,$ls_nomtemarc);
		}
		if($lb_valido)
		{
			$lb_valido=$io_reclasificar->uf_reclasificar_scg_cuentas($ls_archivoscgcuentas);
		}
		if($lb_valido)
		{
			$io_reclasificar->io_message->message("Proceso Ejecutado Satisfactoriamente.");
		}
		else
		{
			$io_reclasificar->io_message->message("Ocurrio un error al reclasificar las cuentas.");
		}
	}
	unset($io_reclasificar);
?>

<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<form name="form1" method="post" enctype="multipart/form-data" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<p>&nbsp;</p>
<table width="442" height="223" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="571" height="221" valign="top">
        <p>&nbsp;</p>
        <table width="360" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-ventana">
            <td colspan="4"><p>Reclasificar Cuentas Contables</p>            </td>
          </tr>
          <tr class="formato-blanco">
            <td height="18" colspan="4"><strong>Este es un proceso critico por favor antes de ejecutar consultar con SIGESP CA </strong></td>
          </tr>
          <tr class="formato-blanco">
            <td height="22"><div align="right"></div></td>
            <td colspan="3"><div align="left"></div></td>
          </tr>
          <tr class="formato-blanco">
			  <td height="22"><div align="right">Archivo Excel</div></td>
			  <td height="22" colspan="3"><input name="txtarchivoscgcuentas" type="file" id="txtarchivoscgcuentas" size="50" maxlength="200"></td>			
          </tr>
          <tr class="formato-blanco">
            <td height="22"><div align="right"></div></td>
            <td colspan="3"><div align="left"></div></td>
          </tr>
          <tr class="formato-blanco">
            <td height="22" colspan="4"><div align="center">
              <input name="botejecutar" type="button" class="boton" id="botejecutar" onClick="javascript:uf_ejecutar();" value="Ejecutar">
            </div></td>
          </tr>
          <tr class="formato-blanco">
            <td height="20">&nbsp;</td>
            <td colspan="3">&nbsp;</td>
          </tr>
        </table>
        <p>
          <input name="operacion" type="hidden" id="operacion">
        </p>
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
		if(f.txtarchivoscgcuentas.value!="")
		{
			f.operacion.value="EJECUTAR";
			f.action="sigesp_ins_p_reclasificar_scgcuentas.php";
			f.submit();
		}
		else
		{
			alert("Debe seleccionar un sistema.");
		}
	}
	else
	{
      alert("No tiene permiso para realizar esra operacion");	
	}	
}
</script>
</html>