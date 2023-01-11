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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_fideicomiso.php",$ls_permisos,$la_seguridad,$la_permisos);
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
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_codfid,$ls_ficfid,$ls_ubifid,$ls_cuefid,$ld_fecingfid,$ls_capfid,$la_capfid,$ld_fecconpreant,$ls_conpreant;
		global $ls_capantcom, $la_capantcom, $io_fun_nomina,$ls_operacion,$ls_existe, $li_porintcap, $ls_scg_cuentafid, $ls_scg_cuentaintfid;
		global $ls_calintfid;
		
		$ls_codfid="";
		$ls_ficfid="";
		$ls_ubifid="";
		$ls_cuefid="";
		$ld_fecingfid="dd/mm/aaaa";
		$ld_fecconpreant="dd/mm/aaaa";	
		$ls_conpreant="";
		$ls_capfid="";
		$la_capfid[0]="";
		$la_capfid[1]="";
		$ls_capantcom ="";
		$la_capantcom[0]="";
		$la_capantcom[1]="";
		$li_porintcap="0.00";
		$ls_scg_cuentafid="";
		$ls_scg_cuentaintfid="";
		$ls_calintfid="0";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
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
		// Fecha Creación: 18/03/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codper, $ls_nomper, $ls_codfid, $ls_ficfid, $ls_ubifid, $ls_cuefid, $ld_fecingfid,$ls_capfid;
		global $ld_fecnacper,$ld_fecingper,$ls_capantcom,$ld_fecconpreant,$ls_conpreant,$ld_fecingadmpubper; 
		global $li_porintcap, $ls_scg_cuentafid, $ls_scg_cuentaintfid, $ls_calintfid;
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecnacper=$_POST["txtfecnacper"];
		$ld_fecingper=$_POST["txtfecingper"];
		$ld_fecingadmpubper=$_POST["txtfecingadmpubper"];
		$ls_codfid=$_POST["txtcodfid"];
		$ls_ficfid=$_POST["txtficfid"];
		$ls_ubifid=$_POST["txtubifid"];
		$ls_cuefid=$_POST["txtcuefid"];
		$ld_fecingfid=$_POST["txtfecingfid"];
		$ls_capfid=$_POST["cmbcapfid"];
		$ls_capantcom=$_POST["cmbcapantcom"];
		$ld_fecconpreant=$_POST["txtfecconpreant"];
		$li_porintcap=$_POST["txtporpintcap"];
		$ls_scg_cuentafid=$_POST["txtscg_cuentafid"];
		$ls_scg_cuentaintfid=$_POST["txtscg_cuentaintfid"];
		if(empty($ls_scg_cuentafid))
		{
			$ls_scg_cuentafid='-------------------------';
		}
		if(empty($ls_scg_cuentaintfid))
		{
			$ls_scg_cuentaintfid='-------------------------';
		}
		$ls_calintfid=$_POST["chkcalintfid"];
		if(empty($ls_calintfid))
		{
			$ls_calintfid=0;
		}
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
<title >Definici&oacute;n de Fideicomiso</title>
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
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_fideicomiso.php");
	$io_fideicomiso=new sigesp_snorh_c_fideicomiso();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			$ld_fecnacper=$_GET["fecnacper"];
			$ld_fecingper=$_GET["fecingper"];
			$ld_fecingadmpubper=$_GET["fecingadmpubper"];
			$arrResultado=$io_fideicomiso->uf_load_fideicomiso($ls_codper,$ls_codfid,$ls_ficfid,$ls_ubifid,$ls_cuefid,$ld_fecingfid,
			                                                $ls_capfid,$ls_capantcom,$ld_fecconpreant,$ls_conpreant,$li_porintcap,$ls_scg_cuentafid,
												   			$ls_scg_cuentaintfid,$ls_calintfid);
			$ls_codfid=$arrResultado['as_codfid'];
			$ls_ficfid=$arrResultado['as_ficfid'];
			$ls_ubifid=$arrResultado['as_ubifid'];
			$ls_cuefid=$arrResultado['as_cuefid'];
			$ld_fecingfid=$arrResultado['ad_fecingfid'];
			$ls_capfid=$arrResultado['as_capfid'];
			$ls_capantcom=$arrResultado['as_capantcom'];
			$ld_fecconpreant=$arrResultado['ad_fecconpreant'];
			$ls_conpreant=$arrResultado['as_conpreant'];
			$li_porintcap=$arrResultado['as_porpintcap'];
			$ls_scg_cuentafid=$arrResultado['as_scg_cuentafid'];
			$ls_scg_cuentaintfid=$arrResultado['as_scg_cuentaintfid'];
			$ls_calintfid=$arrResultado['as_calintfid'];
			$lb_valido=$arrResultado['lb_valido'];
															
			if($lb_valido)
			{
				$ls_existe="TRUE";
			}
			$la_capfid=$io_fun_nomina->uf_seleccionarcombo("S-N",$ls_capfid,$la_capfid,2);
			$la_capantcom=$io_fun_nomina->uf_seleccionarcombo("1-0",$ls_capantcom,$la_capantcom,2);
			if($ls_conpreant=="1")
			{
				$ls_conpreant="checked";
			}	
			break;

		case "GUARDAR":
			uf_load_variables();
			$ls_conpreant=$io_fun_nomina->uf_obtenervalor("chkconpreant","0");
			$lb_valido=$io_fideicomiso->uf_guardar($ls_codper,$ls_codfid,$ls_ficfid,$ls_ubifid,$ls_cuefid,$ld_fecingfid,$ls_capfid,
			                                       $ls_capantcom,$ld_fecconpreant,$ls_conpreant,$li_porintcap,$ls_scg_cuentafid,
												   $ls_scg_cuentaintfid,$ls_calintfid,$la_seguridad);
			$la_capfid=$io_fun_nomina->uf_seleccionarcombo("S-N",$ls_capfid,$la_capfid,2);
			$la_capantcom=$io_fun_nomina->uf_seleccionarcombo("1-0",$ls_capantcom,$la_capantcom,2);
			if($ls_conpreant=="1")
			{
				$ls_conpreant="checked";
			}			
			break;

		case "ELIMINAR":
			uf_load_variables();
			$ls_conpreant=$io_fun_nomina->uf_obtenervalor("chkconpreant","0");
			$lb_valido=$io_fideicomiso->uf_delete_fideicomiso($ls_codper,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			break;
	}
	$io_fideicomiso->uf_destructor();
	unset($io_fideicomiso);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_nomina">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	</table>
	 </td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td width="25" height="20" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
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
<table width="664" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="662">	<p>&nbsp;</p>      <table width="590" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td colspan="3"><div align="center">
            <input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
        </div></td>
      </tr>
      <tr class="titulo-ventana">
        <td height="20" colspan="3" class="titulo-ventana">Definici&oacute;n de Fideicomiso </td>
      </tr>
      <tr>
        <td width="175" height="22">&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"><div align="right">Planilla</div></td>
        <td colspan="2"><div align="left">
          <input name="txtcodfid" type="text" id="txtcodfid" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_codfid;?>" size="13" maxlength="10">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Ficha</div></td>
        <td colspan="2"><div align="left">
          <input name="txtficfid" type="text" id="txtficfid" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_ficfid;?>" size="13" maxlength="10">
        </div></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Ubicaci&oacute;n</div></td>
        <td colspan="2"><div align="left">
          <input name="txtubifid" type="text" id="txtubifid" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_ubifid;?>" size="13" maxlength="10">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Cuenta</div></td>
        <td colspan="2"><div align="left">
          <input name="txtcuefid" type="text" id="txtcuefid" onKeyUp="javascript: ue_validarcomillas(this);" value="<?php print $ls_cuefid;?>" size="30" maxlength="25">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Fecha de Ingreso</div></td>
        <td colspan="2"><div align="left">
          <input name="txtfecingfid" type="text" id="txtfecingfid" value="<?php print $ld_fecingfid;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
        </div></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Capitaliza</div></td>
        <td colspan="2"><div align="left">
          <select name="cmbcapfid" id="cmbcapfid">
            <option value="" selected>--Seleccione Uno--</option>
            <option value="S" <?php print $la_capfid[0];?> >Si</option>
            <option value="N" <?php print $la_capfid[1];?> >No</option>
          </select>
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Capitaliza Antiguedad Complementaria </div></td>
        <td colspan="2"><select name="cmbcapantcom" id="cmbcapantcom">
          <option value="" selected>--Seleccione Uno--</option>
          <option value="1" <?php print $la_capantcom[0];?> >Si</option>
          <option value="0" <?php print $la_capantcom[1];?> >No</option>
        </select></td>
      </tr>
      <tr>
        <td><div align="right"></div></td>
        <td colspan="2"><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
            <input name="txtfecingper" type="hidden" id="txtfecingper" value="<?php print $ld_fecingper;?>">			
			<input name="txtfecingadmpubper" type="hidden" id="txtfecingadmpubper" value="<?php print $ld_fecingadmpubper;?>">			
            <input name="txtfecnacper" type="hidden" id="txtfecnacper" value="<?php print $ld_fecnacper;?>"></td>
      </tr>
	  <tr>
        <td height="22"><div align="right">Fecha Continuedad de Prestaci&oacute;n Antig&uuml;edad</div></td>
	    <td width="82"><div align="left">
            <input name="txtfecconpreant" type="text" id="txtfecconpreant" value="<?php print $ld_fecconpreant;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
        </div></td>
	    <td><input name="chkconpreant" type="checkbox" class="sin-borde" id="chkconpreant" value="1" <?php print $ls_conpreant;?>></td>
	    </tr>
	  <tr>
        <td height="22"><div align="right">Porcentaje de Inter&eacute;s para Capitalizar o Abonar</div></td>
       <td><div align="left">
            <input name="txtporpintcap" type="text" id="txtporpintcap" value="<?php print $li_porintcap;?>" size="9" maxlength="9" onKeyPress="return(ue_formatonumero(this,'.',',',event))" style="text-align:right">
            <strong>%</strong></div></td>
        <td width="289">&nbsp;</td>
	  </tr>
	  <tr>
	    <td height="22"><div align="right">Cuenta Contable para el registro de la Prestaci&oacute;n Antiguedad </div></td>
	    <td colspan="2" align="right"><div align="left">
	      <input name="txtscg_cuentafid" type="text" id="txtscg_cuentafid" value="<?php print $ls_scg_cuentafid;?>" readonly>
	      <a href="javascript: ue_buscarcuentacontable('PRESTACION');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
	    </tr>
	  <tr>
	    <td height="22"><div align="right">Cuenta Contable para el registro de los Intereses sobre la Prestaci&oacute;n Antiguedad </div></td>
	    <td colspan="2"><div align="left">
	      <input name="txtscg_cuentaintfid" type="text" id="txtscg_cuentaintfid" value="<?php print $ls_scg_cuentaintfid;?>" readonly>
	      <a href="javascript: ue_buscarcuentacontable('PRESTACIONINT');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
	    </tr>
	  <tr>
	    <td height="22"><div align="right">Calcular Interes de Prestaci&oacute;n Antiguedad </div></td>
	    <td colspan="2"><input name="chkcalintfid" type="checkbox" class="sin-borde" id="chkcalintfid" value="1" <?php if($ls_calintfid=="1"){ print " checked ";}?>></td>
	    </tr>
	  <tr>
	    <td height="22">&nbsp;</td>
	    <td colspan="2">&nbsp;</td>
	    </tr>
	  
	  
    </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
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
		codfid = ue_validarvacio(f.txtcodfid.value);
		ficfid = ue_validarvacio(f.txtficfid.value);
		ubifid = ue_validarvacio(f.txtubifid.value);
		cuefid = ue_validarvacio(f.txtcuefid.value);
		capfid = ue_validarvacio(f.cmbcapfid.value);
		capantcom = ue_validarvacio(f.cmbcapantcom.value);
		f.txtfecingfid.value=ue_validarfecha(f.txtfecingfid.value);	
		fecingfid = ue_validarvacio(f.txtfecingfid.value);
		fecnacper=ue_validarvacio(f.txtfecnacper.value);	
		fecingper=ue_validarvacio(f.txtfecingper.value);
		fecingadmpubper=ue_validarvacio(f.txtfecingadmpubper.value);
		fecconpreant=f.txtfecconpreant.value;
		conpreant=f.chkconpreant.checked;
		if(!ue_comparar_fechas(fecnacper,fecingfid))
		{
			alert("La fecha de Ingreso al Fideicomiso es menor que la de Nacimiento del personal.");
			valido=false;
		}
		else if((fecconpreant!="") && (fecconpreant!="01/01/1900") && (!ue_comparar_fechas(fecnacper,fecconpreant)))
		{
			alert("La fecha de Continuidad de Prestación Antiguedad  es menor que la de Nacimiento del personal.");
			valido=false;
		}
		else if(!ue_comparar_fechas(fecingper,fecingfid))
		{
			alert("La fecha de Ingreso al Fideicomiso es menor que la de Ingreso a la Institución.");
			valido=false;
		}
		else if((fecconpreant=="") && (conpreant))
		{
			alert("Seleccionó la opción Continuidad de Prestación Antiguedad. Debe ingresar la fecha de Continuidad de Prestación Antiguedad.");
			valido=false;
		} 
		else if((fecconpreant!="") && (fecconpreant!="01/01/1900") && (!ue_comparar_fechas(fecingadmpubper,fecconpreant)))
		{
			alert("La fecha de Continuidad de Prestación Antiguedad es menor que la de Ingreso a la Administración Pública.");
			valido=false;
		}
		else if((fecconpreant!="") && (!ue_comparar_fechas(fecconpreant,fecingper)))
		{
			alert("La fecha de Continuidad de Prestación Antiguedad es mayor que la de Ingreso a la Institución.");
			valido=false;
		}
		else if((fecconpreant!="") && (!ue_comparar_fechas(fecconpreant,fecingfid)))
		{
			alert("La fecha de Continuidad de Prestación Antiguedad es mayor que la de Ingreso al Fideicomiso.");
			valido=false;
		}
		
		
		if(valido)
		{
			if ((codper!="")&&(capfid!="")&&(fecingfid!="")&&(capantcom!=""))
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_snorh_d_fideicomiso.php";
				f.submit();
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

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			codper = ue_validarvacio(f.txtcodper.value);
			codfid = ue_validarvacio(f.txtcodfid.value);
			if ((codper!="")&&(codfid!=""))
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_fideicomiso.php";
					f.submit();
				}
			}
			else
			{
				alert("Debe buscar el registro a eliminar.");
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

function ue_buscarcuentacontable(tipo)
{
	window.open("sigesp_sno_cat_cuentacontable.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
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