<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_anticipos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codant, $ls_estant, $ld_fecant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_porant, $li_monant;
		global $li_monint, $ls_motant, $ls_obsant, $li_pormaxant, $li_saldo, $li_saldoint, $ls_estatus, $io_fun_nomina, $ls_operacion, $ls_existe;
		
		$ls_codant="000";
		$ls_estant="R";
		$ls_estatus="REGISTRO";
		$ld_fecant=date("d/m/Y");
		$li_monpreant="0,00";
		$li_monintant="0,00";
		$li_monantant="0,00";
		$li_monantint="0,00";
		$li_saldo="0,00";
		$li_saldoint="0,00";
		$li_porant="0,00";
		$li_monant="0,00";
		$ls_motant="";
		$li_monint="0,00";
		$ls_obsant="";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
		$li_pormaxant=trim($io_sno->uf_select_config("SNO","NOMINA","POR_MAX_ANTICIPO","0","I"));;
		unset($io_sno);
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codant, $ls_estant, $ld_fecant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_porant, $li_monant;
		global $ls_motant, $li_monint, $ls_obsant, $ld_fecnacper, $ld_fecingper, $ld_fecingadmpubper, $ls_codper, $ls_nomper, $io_fun_nomina;
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecnacper=$_POST["txtfecnacper"];
		$ld_fecingper=$_POST["txtfecingper"];
		$ld_fecingadmpubper=$_POST["txtfecingadmpubper"];
		$ls_codant=$_POST["txtcodant"];
		$ls_estant=$_POST["txtestant"];
		$ld_fecant=$_POST["txtfecant"];
		$li_monpreant=$_POST["txtmonpreant"];
		$li_monintant=$_POST["txtmonintant"];
		$li_monantant=$_POST["txtmonantant"];
		$li_monantint=$_POST["txtmonantint"];
		$li_porant=$_POST["txtporant"];
		$li_monant=$_POST["txtmonant"];
		$li_monint=$_POST["txtmonint"];
		$ls_motant=$_POST["txtmotant"];
		$ls_obsant=$_POST["txtobsant"];
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
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
<title >Definici&oacute;n de Anticipos Pretaci&oacute;n antiguedad</title>
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="js/valida_fecha.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_anticipos.php");
	$io_anticipos=new sigesp_snorh_c_anticipos();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			$ld_fecnacper=$_GET["fecnacper"];
			$ld_fecingper=$_GET["fecingper"];
			$ld_fecingadmpubper=$_GET["fecingadmpubper"];
			$arrResultado=$io_anticipos->uf_load_datos($ls_codper, $li_pormaxant, $ls_codant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_saldo, $li_saldoint);
			$ls_codant=$arrResultado['as_codant'];
			$li_monpreant=$arrResultado['ai_monpreant'];
			$li_monintant=$arrResultado['ai_monintant'];
			$li_monantant=$arrResultado['ai_monantant'];
			$li_monantint=$arrResultado['ai_monantint'];
			$li_saldo=$arrResultado['ai_saldo'];
			$li_saldoint=$arrResultado['ai_saldoint'];
			$lb_valido=$arrResultado['lb_valido'];
													
			//AGREGADO POR OFIMATICA DE VENEZUELA PARA MEJORAR MANEJO DE CAMPO DE FECHA
			$ls_obj_fecha='datepicker="true" onKeyDown="javascript:ue_formato_fecha(this,\'/\',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"';
			break;

		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_anticipos->uf_guardar($ls_codper,$ls_codant,$ls_estant,$ld_fecant,$li_monpreant,$li_monintant,$li_monantant,
												 $li_monantint,$li_porant,$li_monant,$li_monint,$ls_motant,$ls_obsant,$ls_existe,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
				$arrResultado=$io_anticipos->uf_load_datos($ls_codper, $li_pormaxant, $ls_codant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_saldo, $li_saldoint);
				$ls_codant=$arrResultado['as_codant'];
				$li_monpreant=$arrResultado['ai_monpreant'];
				$li_monintant=$arrResultado['ai_monintant'];
				$li_monantant=$arrResultado['ai_monantant'];
				$li_monantint=$arrResultado['ai_monantint'];
				$li_saldo=$arrResultado['ai_saldo'];
				$li_saldoint=$arrResultado['ai_saldoint'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			//AGREGADO POR OFIMATICA DE VENEZUELA PARA MEJORAR MANEJO DE CAMPO DE FECHA
			$ls_obj_fecha="readonly";
			break;
			
		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_anticipos->uf_eliminar($ls_codper,$ls_codant,$ls_estant,$ld_fecant,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
				$arrResultado=$io_anticipos->uf_load_datos($ls_codper, $li_pormaxant, $ls_codant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_saldo, $li_saldoint);
				$ls_codant=$arrResultado['as_codant'];
				$li_monpreant=$arrResultado['ai_monpreant'];
				$li_monintant=$arrResultado['ai_monintant'];
				$li_monantant=$arrResultado['ai_monantant'];
				$li_monantint=$arrResultado['ai_monantint'];
				$li_saldo=$arrResultado['ai_saldo'];
				$li_saldoint=$arrResultado['ai_saldoint'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			uf_limpiarvariables();
			uf_load_variables();
			$ls_obj_fecha="readonly";
			break;
			
		case "ACTUALIZAR":
		 	uf_load_variables();
			$ls_mes=substr($ld_fecant,3,2);
			$ls_anio=substr($ld_fecant,6,4);
			$arrResultado=$io_anticipos->uf_reload_datos($ls_codper, $li_pormaxant, $ls_codant, $li_monpreant, $li_monintant, $li_monantant, $li_monantint, $li_saldo, $li_saldoint,$ls_mes,$ls_anio);
			$ls_codant=$arrResultado['as_codant'];
			$li_monpreant=$arrResultado['ai_monpreant'];
			$li_monintant=$arrResultado['ai_monintant'];
			$li_monantant=$arrResultado['ai_monantant'];
			$li_monantint=$arrResultado['ai_monantint'];
			$li_saldo=$arrResultado['ai_saldo'];
			$li_saldoint=$arrResultado['ai_saldoint'];
			$lb_valido=$arrResultado['lb_valido'];
			//AGREGADO POR OFIMATICA DE VENEZUELA PARA MEJORAR MANEJO DE CAMPO DE FECHA
			$ls_obj_fecha='datepicker="true" onKeyDown="javascript:ue_formato_fecha(this,\'/\',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"';
			break;
	}
	$io_anticipos->uf_destructor();
	unset($io_anticipos);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_nomina">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
  	</table>	 </td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td width="30" height="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif"  title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="35"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="31"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="29"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
	<td class="toolbar" width="33"><div align="center"><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="29"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="303"><div align="center"></div></td>
    <td class="toolbar" width="72"><div align="center"></div></td>
    <td class="toolbar" width="72"><div align="center"></div></td>
    <td class="toolbar" width="72"><div align="center"></div></td>
    <td class="toolbar" width="72"><div align="center"></div></td>
    <td class="toolbar" width="3">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigesp_snorh_d_personal.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="731" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="729">	<p>&nbsp;</p>      <table width="596" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td colspan="3"><div align="center">
            <input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
        </div></td>
      </tr>
      <tr class="titulo-ventana">
        <td height="20" colspan="3" class="titulo-ventana">Definici&oacute;n de Anticipos Prestaci&oacute;n Antiguedad </td>
      </tr>
      <tr>
        <td width="173" height="22">&nbsp;</td>
        <td width="417" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"><div align="right">N&uacute;mero</div></td>
        <td colspan="2"><div align="left">
          <input name="txtcodant" type="text" id="txtcodant" onKeyUp="javascript: ue_validarnumero(this);" value="<?php print $ls_codant;?>" size="5" maxlength="3" readonly>
          <input name="txtestatus" type="text" class="sin-borde2" id="txtestatus" style="text-align: center" value="<?php print $ls_estatus;?>" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Fecha</div></td>
        <td colspan="2"><div align="left">
          <input name="txtfecant" type="text" id="txtfecant" value="<?php print $ld_fecant;?>" size="15" maxlength="10"  <?php print $ls_obj_fecha;?>>
        </div></td>
      </tr>
	  <tr>
        <td height="22"><div align="right">                     </div></td>
		<td colspan="2"><div align="left">
          <input name="btnrecalculo" type="button" class="boton" id="btnrecalculo" value="Recalcular Anticipos" onClick="javascript: ue_actualizar();">
        </div></td>
      </tr>
      <tr>
        <td height="22" colspan="3" class="titulo-celdanew">Acumulados a la fecha </td>
        </tr>
      <tr>
        <td height="22"><div align="right">Antiguedad Acumulada  </div></td>
        <td colspan="2"><input name="txtmonpreant" type="text" id="txtmonpreant" value="<?php print $li_monpreant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Intereses Acumulados </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonintant" type="text" id="txtmonintant" value="<?php print $li_monintant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="22" colspan="3" class="titulo-celdanew">Anticipos solicitados anteriormente </td>
        </tr>
      <tr>
        <td height="22"><div align="right">Anticipos Antiguedad </div></td>
        <td colspan="2"><input name="txtmonantant" type="text" id="txtmonantant" value="<?php print $li_monantant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Anticipos Intereses </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonantint" type="text" id="txtmonantint" value="<?php print $li_monantint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="22" colspan="3" class="titulo-celdanew">Montos M&aacute;ximos a Solicitar </td>
        </tr>
      <tr>
        <td height="22"><div align="right">Antiguedad </div></td>
        <td colspan="2"><input name="txtsaldo" type="text" id="txtsaldo" value="<?php print $li_saldo;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Intereses</div></td>
        <td colspan="2"><input name="txtsaldoint" type="text" id="txtsaldoint" value="<?php print $li_saldoint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" readonly></td>
      </tr>
      <tr>
        <td height="22" colspan="3" class="titulo-celdanew">&nbsp;</td>
        </tr>
      <tr>
        <td height="22"><div align="right">% Antiguedad Solicitado </div></td>
        <td colspan="2"><div align="left">
          <input name="txtporant" type="text" id="txtporant" value="<?php print $li_porant;?>" size="5" maxlength="3" style="text-align:right" onKeyPress="return(currencyFormat(this,'.',',',event));" onBlur="javascript: ue_calcularmontoantiguedad();">
		en base al Monto m&aacute;ximo a solicitar </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">% Intereses Solicitado </div></td>
        <td colspan="2"><input name="txtporint" type="text" id="txtporint" value="<?php print $li_porant;?>" size="5" maxlength="3" style="text-align:right" onKeyPress="return(currencyFormat(this,'.',',',event));" onBlur="javascript: ue_calcularmontointereses();">
en base al Monto m&aacute;ximo a solicitar </td>
      </tr>
      <tr>
        <td height="22"><div align="right">Monto Anticipo Antiguedad </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonant" type="text" id="txtmonant" value="<?php print $li_monant;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" onBlur="javascript: ue_calcularporcentajeantiguedad();">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Monto Anticipo Intereses </div></td>
        <td colspan="2"><div align="left">
          <input name="txtmonint" type="text" id="txtmonint" value="<?php print $li_monint;?>" size="23" maxlength="20" style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event))" onBlur="javascript: ue_calcularporcentajeintereses();">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Motivo  </div></td>
        <td colspan="2"><div align="left"><textarea name="txtmotant" cols="55" rows="4" id="txtmotant"  value="<?php print $ls_motant;?>" onKeyUp="javascript: ue_validarcomillas(this);"></textarea></div></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Observación  </div></td>
        <td colspan="2"><div align="left"><textarea name="txtobsant" cols="55" rows="4" id="txtobsant"  value="<?php print $ls_obsant;?>" onKeyUp="javascript: ue_validarcomillas(this);"></textarea></div></td>
        </tr>
      <tr>
        <td><div align="right"></div></td>
        <td colspan="2"><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
            <input name="txtfecnacper" type="hidden" id="txtfecnacper" value="<?php print $ls_fecnacper;?>">
            <input name="txtfecingper" type="hidden" id="txtfecingper" value="<?php print $ld_fecingper;?>">			
			<input name="txtfecingadmpubper" type="hidden" id="txtfecingadmpubper" value="<?php print $ld_fecingadmpubper;?>">			
            <input name="txtpormaxant" type="hidden" id="txtpormaxant" value="<?php print $li_pormaxant;?>">
            <input name="txtestant" type="hidden" class="sin-borde2" id="txtestant"  value="<?php print $ls_estant;?>"></td>
      </tr>
    </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";	
		codper=ue_validarvacio(f.txtcodper.value);
		nomper=ue_validarvacio(f.txtnomper.value);	
		fecnacper=ue_validarvacio(f.txtfecnacper.value);	
		fecingper=ue_validarvacio(f.txtfecingper.value);
		fecingadmpubper=ue_validarvacio(f.txtfecingadmpubper.value);
		f.action="sigesp_snorh_d_anticipos.php?codper="+codper+"&nomper="+nomper+"&fecnacper="+fecnacper+"&fecingper="+fecingper+"&fecingadmpubper="+fecingadmpubper+"";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_actualizar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="ACTUALIZAR";
		f.existe.value="FALSE";	
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_volver()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.existe.value="TRUE";	
	codper=ue_validarvacio(f.txtcodper.value);
	f.action="sigesp_snorh_d_personal.php?codper="+codper;
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		valido=true;
		codper = ue_validarvacio(f.txtcodper.value);
		codant = ue_validarvacio(f.txtcodant.value);
		estant = ue_validarvacio(f.txtestant.value);
		f.txtfecant.value=ue_validarfecha(f.txtfecant.value);	
		fecant = ue_validarvacio(f.txtfecant.value);
		monpreant=ue_formato_calculo(f.txtmonpreant.value);
		monintant=ue_formato_calculo(f.txtmonintant.value);
		monantant=ue_formato_calculo(f.txtmonantant.value);
		saldo=ue_formato_calculo(f.txtsaldo.value);
		saldoint=redondear(ue_formato_calculo(f.txtsaldoint.value),2);
		pormaxant = ue_validarvacio(f.txtpormaxant.value);
		porant=ue_formato_calculo(f.txtporant.value);
		monant=ue_validarvacio(f.txtmonant.value);	
		monant=ue_formato_calculo(f.txtmonant.value);
		monint=ue_formato_calculo(f.txtmonint.value);	
		motant=ue_validarvacio(f.txtmotant.value);
		obsant=ue_validarvacio(f.txtobsant.value);
		fecnacper=ue_validarvacio(f.txtfecnacper.value);
		fecingadmpubper=ue_validarvacio(f.txtfecingadmpubper.value);
		fecingper=ue_validarvacio(f.txtfecingper.value);

		if(lb_existe=="TRUE")
		{
			valido=false;
			alert("El Anticipo no se puede actualizar. Debe Anularlo. ");
		}
		if(!ue_comparar_fechas(fecnacper,fecant)&&(valido))
		{
			alert("La fecha del anticipo es menor que la de Nacimiento del personal.");
			valido=false;
		}
		else if(!ue_comparar_fechas(fecingadmpubper,fecant)&&(valido))
		{
			alert("La fecha del anticipo es menor que la de Ingreso a la Administración Pública.");
			valido=false;
		}
		else if(!ue_comparar_fechas(fecingper,fecant)&&(valido))
		{
			alert("La fecha del anticipo es menor que la de Ingreso al organismo.");
			valido=false;
		}
		if((pormaxant=="")||(pormaxant==0)&&(valido))
		{
			valido=false;
			alert("El Porcentaje máximo para los anticipos no está definido.");
		}
		/*if((porant=="")||(porant==0)&&(valido))
		{
			valido=false;
			alert("Debe definir el porcentaje a solicitar.");
		}*/
		if((saldo=="")&&(saldo==0)&&(valido))
		{
			valido=false;
			alert("No tiene Saldo disponible.");
		}
		/*if((monant=="")&&(monant==0)&&(valido))
		{
			valido=false;
			alert("El monto del anticipo debe ser mayor que cero.");
		}*/
		if((monpreant=="")&&(monpreant==0)&&(valido))
		{
			valido=false;
			alert("La antiguedad acumulada debe ser mayor que cero.");
		}
		if((parseFloat(monant)>parseFloat(saldo))&&(valido))
		{
			valido=false;
			alert("El Monto a solicitar en antiguedad es mayor al máximo.");
		}
		if((parseFloat(monint)>parseFloat(saldoint))&&(valido))
		{
			valido=false;
			alert("El Monto a solicitar en intereses es mayor al máximno.");
		}
		if(valido)
		{
			if ((codper!="")&&(codant!="")&&(estant!="")&&(fecant!="")&&(motant!="")&&(obsant!=""))
			{
				if(confirm("¿Esta Seguro que los datos están correctos. Una vez guardado en anticipo no se pueden modificar los datos.?"))
				{
					f.operacion.value="GUARDAR";
					f.action="sigesp_snorh_d_anticipos.php";
					f.submit();
				}
			}
			else
			{
				alert("Debe llenar todos los datos.");
			}
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		codper = ue_validarvacio(f.txtcodper.value);
		window.open("sigesp_snorh_cat_anticipos.php?codper="+codper+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.form1;
	lb_existe=f.existe.value;
	li_leer=f.leer.value;
	estatus=f.txtestant.value;
	if (li_leer==1)
   	{
		if(f.existe.value=="TRUE")
		{
			if (estatus!="A")
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_anticipos.php";
					f.submit();
				}
			}
			else
			{
				alert("El anticipo esta aprobado no puede eliminarlo");
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_calcularmontoantiguedad()
{
	f=document.form1;
	valido=true;
	porant=redondear(ue_formato_calculo(f.txtporant.value),2);
	saldo=redondear(ue_formato_calculo(f.txtsaldo.value),2);
	/*if((porant=="")||(porant==0)&&(valido))
	{
		valido=false;
		alert("Debe definir el porcentaje a solicitar.");
	}*/
	if((saldo=="")&&(saldo==0)&&(valido))
	{
		valido=false;
		alert("No tiene Saldo disponible.");
	}
	if(valido)
	{
		monant=redondear((saldo*(porant/100)),2);
		if(parseFloat(monant)<=parseFloat(saldo))
		{
			monant=uf_convertir(monant);
			f.txtmonant.value=monant;
		}
		else
		{
			alert("La persona no puede solicitar este monto de anticipo.");
			f.txtmonant.value="0,00";
			f.txtporant.value="0,00";
		}
	}
	else
	{
		f.txtmonant.value="0,00";
		f.txtporant.value="0,00";
	}
}

function ue_calcularporcentajeantiguedad()
{
	f=document.form1;
	valido=true;
	monant=redondear(ue_formato_calculo(f.txtmonant.value),2);
	saldo=redondear(ue_formato_calculo(f.txtsaldo.value),2);
	/*if((monant=="")||(monant==0)&&(valido))
	{
		valido=false;
		alert("Debe definir el Monto a solicitar.");
	}*/
	if((saldo=="")&&(saldo==0)&&(valido))
	{
		valido=false;
		alert("No tiene Saldo disponible.");
	}
	if(valido)
	{
		porant=redondear(((monant*100)/saldo),2);
		if(parseFloat(porant)<=parseFloat(100))
		{
			porant=uf_convertir(porant);
			f.txtporant.value=porant;
		}
		else
		{
			alert("La persona no puede solicitar este monto de anticipo.");
			f.txtmonant.value="0,00";
			f.txtporant.value="0,00";
		}
	}
	else
	{
		f.txtmonant.value="0,00";
		f.txtporant.value="0,00";
	}
}

function ue_calcularmontointereses()
{
	f=document.form1;
	valido=true;
	porint=redondear(ue_formato_calculo(f.txtporint.value),2);
	saldo=redondear(ue_formato_calculo(f.txtsaldoint.value),2);
	/*if((porint=="")||(porint==0)&&(valido))
	{
		valido=false;
		alert("Debe definir el porcentaje de Intereses a solicitar.");
	}*/
	if((saldo=="")&&(saldo==0)&&(valido))
	{
		valido=false;
		alert("No tiene Saldo de Intereses disponible.");
	}
	if(valido)
	{
		monint=redondear((saldo*(porint/100)),2);
		if(parseFloat(monint)<=parseFloat(saldo))
		{
			monint=uf_convertir(monint);
			f.txtmonint.value=monint;
		}
		else
		{
			alert("La persona no puede solicitar este monto de Intereses.");
			f.txtmonint.value="0,00";
			f.txtporint.value="0,00";
		}
	}
	else
	{
		f.txtmonint.value="0,00";
		f.txtporint.value="0,00";
	}
}

function ue_calcularporcentajeintereses()
{
	f=document.form1;
	valido=true;
	monint=redondear(ue_formato_calculo(f.txtmonint.value),2);
	saldo=redondear(ue_formato_calculo(f.txtsaldoint.value),2);
	if((monint=="")||(monint==0)&&(valido))
	{
		valido=false;
		alert("Debe definir el Monto a solicitar de Intereses.");
	}
	if((saldo=="")&&(saldo==0)&&(valido))
	{
		valido=false;
		alert("No tiene Saldo disponible de Intereses.");
	}
	if(valido)
	{
		porint=redondear(((monint*100)/saldo),2);
		if(parseFloat(porint)<=parseFloat(100))
		{
			porint=uf_convertir(porint);
			f.txtporint.value=porint;
		}
		else
		{
			alert("La persona no puede solicitar este monto de anticipo de Intereses.");
			f.txtmonint.value="0,00";
			f.txtporint.value="0,00";
		}
	}
	else
	{
		f.txtmonint.value="0,00";
		f.txtporint.value="0,00";
	}
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
//	window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script>
</html>