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
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_reprocesar_comprobantes.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Reprocesar Comprobantes Descuadrados</title>
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
	require_once("class_folder/sigesp_ins_c_reprocesar_comprobantes.php");
	$io_reprocesar=new sigesp_ins_c_reprocesar_comprobantes();
	$io_reprocesar->uf_verificar_version_mis();
	$ls_sistema="";
	$ls_operacion="NUEVO";	
	$lb_valido=false;
	if(array_key_exists("operacion",$_POST))
	{
		if(array_key_exists("cmbsistema",$_POST))
		{
			$ls_sistema=$_POST["cmbsistema"];
		}
		$ls_operacion= $_POST["operacion"];
	}
	if($ls_operacion=="EJECUTAR")
	{
		switch($ls_sistema)
		{
			case "SEP": // Sistema de Solicitud de ejecución presupouestaria
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_sep($la_seguridad);
			break;

			case "SOC": // Sistema de Orden de Compra - Servicio
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_soc($la_seguridad);
			break;

			case "SNO": // Sistema de Nómina
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_sno($la_seguridad);
			break;

			case "CXP": // Sistema de Nómina
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_cxp($la_seguridad);
			break;

			case "SPG": // Sistema de Presupuesto de Gasto
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_spg($la_seguridad);
			break;
			
			// HAY QUE VERIFICAR BIEN ESTE PROCESO
			/*case "SCB": // Sistema de Caja y Banco
				$lb_valido=$io_reprocesar->uf_reprocesar_comprobantes_scb($la_seguridad);
			break;*/
		}
		if($lb_valido)
		{
			$io_reprocesar->io_message->message("Proceso Ejecutado Satisfactoriamente.");
		}
		else
		{
			$io_reprocesar->io_message->message("Ocurrio un error al reprocesar los comprobantes.");
		}
	}
	unset($io_reprocesar);
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
            <td colspan="4"><p>Reprocesar Comprobantes </p>            </td>
          </tr>
          <tr class="formato-blanco">
            <td height="18" colspan="4"><strong>Este es una proceso bastante critico por favor antes de ejecutar consultar con SIGESP CA </strong></td>
          </tr>
          <tr class="formato-blanco">
            <td width="72" height="22"><div align="right">Sistema 
            </div>
            <div align="right"></div></td>
            <td colspan="3"><div align="left">
              <label>
              <select name="cmbsistema" id="cmbsistema">
                <option value="" selected>--Seleccione Uno--</option>
                <option value="SEP">Solicitud de Ejecución Presupuestaria</option>
                <option value="SOC">Orden de Compra - Servicio</option>
                <option value="CXP">Cuentas por Pagar</option>
                <option value="SNO">Nómina</option>
                <option value="SPG">Modificación Presupuestaria</option>
              </select>
              </label>
            </div></td>
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
		if(f.cmbsistema.value!="")
		{
			f.operacion.value="EJECUTAR";
			f.action="sigesp_ins_p_reprocesar_comprobantes.php";
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