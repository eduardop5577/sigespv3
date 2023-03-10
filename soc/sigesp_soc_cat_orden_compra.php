<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

  session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cat&aacute;logo del Registro de Orden de Compra</title>
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/general.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css" />
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css" />
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="js/funcion_soc.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<style type="text/css">
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
<?php
	require_once("class_folder/class_funciones_soc.php");
	$io_fun_soc=new class_funciones_soc();
	$ls_tipo=$io_fun_soc->uf_obtenertipo();
    if (array_key_exists("hidoperacion",$_POST))
    {
		$ls_numordcom  = $_POST["txtnumordcom"];
		$ls_operacion  = $_POST["hidoperacion"];
		$ls_codprov    = $_POST["txtcodprov"];
		$ls_nomprov    = $_POST["txtnomprov"];
		$ld_fecdes     = $_POST["txtfecdes"];
		$ld_fechas     = $_POST["txtfechas"];
		$ls_tipordcom  = $_POST["cmbtipordcom"];
    }
    else
    {
		$ls_numordcom = ""; 
		$ls_operacion = "";
		$ls_codprov   = "";
		$ls_nomprov   = "";
		$ld_fecdes    = "01/".date("m")."/".date("Y");
		$ld_fechas    = date("d/m/Y");
		$ls_tipordcom = "";
		$ls_origen    = $_GET["origen"];
    }
	if($ls_tipordcom=="")
	{
		$ls_tipordcom=$io_fun_soc->uf_obtenervalor_get("tipordcom","");
	}
	$ls_disabled = "";
	if (($ls_origen=='AS')||($ls_tipordcom!=""))
	{
		$ls_disabled = "disabled";
	}
	$ls_tipsol=$io_fun_soc->uf_obtenervalor_get("tipsol",""); 
	unset($io_fun_soc);
?>
<form id="formulario" name="formulario" method="post" action="">
  <input name="campoorden" type="hidden" id="campoorden" value="numordcom" />
  <input name="orden" type="hidden" id="orden" value="ASC" />
  <input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
  <input name="tipsol" type="hidden" id="tipsol" value="<?php print $ls_tipsol; ?>" />
  <input name="procesando" type="hidden" id="procesando" />
  <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr class="titulo-celda">
      <td height="22" colspan="4">Cat&aacute;logo del Registro de Orden de Compra  
        
      <input name="hidoperacion" type="hidden" id="hidoperacion" value="<?php print $ls_operacion ?>" /></td>
      <input name="origen" type="hidden" id="origen" value="<?php print $ls_origen ?>" />
      <input name="tipordcom" type="hidden" id="tipordcom" value="<?php print $ls_tipordcom; ?>" />
    </tr>
    <tr>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
      <td height="13">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Nro. Orden de Compra</td>
      <td height="22"><label>
        <input name="txtnumordcom" type="text" id="txtnumordcom" style="text-align:center" value="<?php print $ls_numordcom ?>" size="20" maxlength="15" />
      </label></td>
      <td height="22"><div align="right">Fecha</div></td>
      <td height="22">Desde 
        <input name="txtfecdes" type="text" id="txtfecdes"  value="<?php print $ld_fecdes ?>" size="13" maxlength="10" datepicker="true" onkeypress="currencyDate(this);" style="text-align:left">
        &nbsp;&nbsp; 
        Hasta
<input name="txtfechas" type="text" id="txtfechas" value="<?php print $ld_fechas ?>" size="13" maxlength="10" datepicker="true" onkeypress="currencyDate(this);" style="text-align:left"></td>
    </tr>
    <tr>
      <td width="129" height="22" style="text-align:right">Tipo</td>
      <td width="155" height="22"><label>
        <select name="cmbtipordcom" id="cmbtipordcom"  style="width:120px" <?php print $ls_disabled ?>>
          <option value="-">---seleccione---</option>
          <option value="B" <?php if($ls_tipordcom=="B"){ print 'selected';} ?>>Bienes</option>
          <option value="S" <?php if($ls_tipordcom=="S"){ print 'selected';} ?>>Servicios</option>
        </select>
      </label></td>
      <td width="41" height="22">&nbsp;</td>
      <td width="253" height="22">&nbsp;</td>
    </tr>
    <tr>
      <td height="22" style="text-align:right">Proveeedor</td>
      <td height="22"><label>
        <input name="txtcodprov" type="text" id="txtcodprov" style="text-align:center" value="<?php print $ls_codprov ?>" size="20" maxlength="10" />
        <a href="javascript: ue_catalogo_proveedor();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0" /></a></label></td>
      <td height="22" colspan="2"><label>
        
        <div align="left">
          <input name="txtnomprov" type="text" class="sin-borde" id="txtnomprov" value="<?php print $ls_nomprov ?>" size="55" readonly />
        </div>
      </label></td>
    </tr>
    <tr>
      <td height="22"><div align="center">
	  <?php 
	  	if($ls_tipo=="COPIAR")
		{
	  ?>
        <input name="btnmontos" type="button" class="boton" id="btnmontos" value="Recalcular Montos" onclick="javascript: ue_recalcularmontos(); " />
	  <?php 
	  	}
	  ?>
      </div></td>
      <td height="22">&nbsp;</td>
      <td height="22">&nbsp;</td>
      <td height="22"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" onclick="ue_search()" />Buscar Orden Compra </a></div></td>
    </tr>
    <tr>
      <td height="13" colspan="4">&nbsp;</td>
    </tr>
  </table>
	<p>
    <div id="mensajes" align="center"></div>
  <div id="resultados" align="center"></div>	
	</p>
</form>
</body>
<script >
f   = document.formulario;
function ue_catalogo_proveedor()
{
	window.open("sigesp_soc_cat_proveedor.php","_blank","menubar=no,toolbar=no,scrollbars=no,width=600,height=400,resizable=yes,location=no,left=50,top=50");
}

function ue_aceptar(ls_numordcom,ls_estcondat,ld_fecordcom,ls_estsegcom,ls_porsegcom,ld_monsegcom,ls_forpagcom,ls_diaplacom,
                    ls_concom,ld_monsubtot,ld_monbasimp,ld_mondes,ld_monimp,ld_montot,ls_lugentnomdep,ls_lugentdir,
					ld_monant,ls_estlugcom,ld_tascamordcom,ld_montotdiv,ls_codunieje,ls_denunieje,ls_codpro,ls_nompro,
					ls_codfuefin,ls_denfuefin,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,ls_codmon,
					ls_denmon,ls_codtipmod,ls_denmodcla,ls_codpai,ls_despai,ls_codest,ls_desest,ls_codmun,ls_denmun,
					ls_codpar,ls_denpar,ls_estcom,ls_estapro,ls_estatus,ls_numanacot,ls_tipconpro,ls_uniejeaso,
					ld_prentdesde,ld_prenthasta,as_tipbieordcom,ls_rifpro,li_i,ls_conanusoc,ls_numdiacre,ls_tipsiscam,ls_codconobr,ls_ressoc,pagele,origen)
{
   tipsol="SOC"; 
   opener.document.formulario.txtnumordcom.value=ls_numordcom;
   opener.document.formulario.numanacot.value=ls_numanacot;
   opener.document.formulario.txtnumordcom.readOnly=true;
   opener.document.formulario.txtestatus.value=ls_estatus;
   opener.document.formulario.txtestcom.value=ls_estcom;
   opener.document.formulario.txtestapro.value=ls_estapro;
   opener.document.formulario.tipord.value=ls_estcondat;
   opener.document.formulario.txtlugentnomdep.value=ls_lugentnomdep;
   opener.document.formulario.txtlugentdir.value=ls_lugentdir;
   opener.document.formulario.cmbpagele.value=pagele;
   opener.document.formulario.crearasiento.value=0;
   if (ls_estcondat=='B')
	{
	  opener.document.formulario.cmbtipordcom[1].selected = true;
	  opener.document.formulario.radiotipbieordcom[0].disabled = false;
	  opener.document.formulario.radiotipbieordcom[1].disabled = false;
	  if (as_tipbieordcom=='M')
		 {
		   opener.document.formulario.radiotipbieordcom[0].checked = true;
		   opener.document.formulario.radiotipbieordcom[1].checked = false;
		 }
	  else
		 {
		   if (as_tipbieordcom=='A')
			  {
			    opener.document.formulario.radiotipbieordcom[0].checked = false;
				opener.document.formulario.radiotipbieordcom[1].checked = true;				   
			  }
		 }
	  if (ls_estapro==1)
		 {
           opener.document.formulario.cmbtipordcom.disabled = true;
		   opener.document.formulario.radiotipbieordcom[0].disabled = true;
	       opener.document.formulario.radiotipbieordcom[1].disabled = true;
		 }
	}
    else
	{
	  opener.document.formulario.cmbtipordcom[2].selected = true;
	  opener.document.formulario.radiotipbieordcom[0].selected = false;
	  opener.document.formulario.radiotipbieordcom[1].selected = false;
	  opener.document.formulario.radiotipbieordcom[0].disabled = true;
	  opener.document.formulario.radiotipbieordcom[1].disabled = true;
	}
	opener.document.formulario.txtfecordcom.value=ld_fecordcom;
	if (ls_estsegcom=="1")
    {
	   opener.document.formulario.chkbestsegcom.checked=true;
    }
	else
	{
	   opener.document.formulario.chkbestsegcom.checked=false;
	}
	opener.document.formulario.tipconpro.value=ls_tipconpro;
	opener.document.formulario.txtporsegcom.value=ls_porsegcom;
	opener.document.formulario.txtmonsegcom.value=ld_monsegcom;
	opener.document.formulario.cmbforpag.value=ls_forpagcom;
	if(ls_tipsiscam==""){
		ls_tipsiscam="-";}
	opener.document.formulario.cmbtipsiscam.value=ls_tipsiscam;
	opener.document.formulario.txtdiaplacom.value=ls_diaplacom;
	opener.document.formulario.cbmconcom.value=ls_concom;
	opener.document.formulario.txtconordanu.value=ls_conanusoc;
	opener.document.formulario.txtantpag.value=ld_monant;
	if(ls_estlugcom==0)
	{
	  opener.document.formulario.rblugcom[0].checked=true;	  
	}
	else
	{
	  opener.document.formulario.rblugcom[1].checked=true;	  
	}
	opener.document.formulario.txttascamordcom.value=ld_tascamordcom;
	opener.document.formulario.txtmontotdiv.value=ld_montotdiv;
	opener.document.formulario.txtcodunieje.value=ls_codunieje;
	opener.document.formulario.txtcodunieje.readOnly=true;
	opener.document.formulario.txtdenunieje.value=ls_denunieje;
	opener.document.formulario.txtdenunieje.readOnly=true;
//    opener.document.formulario.txtobscom.value=ls_obsordcom;
//	opener.document.formulario.txtconordcom.value=ls_obscom;
	opener.document.formulario.txtobscom.value=eval("f.txtobscom"+li_i+".value;");
	opener.document.formulario.txtconordcom.value=eval("f.txtconordcom"+li_i+".value;");
	opener.document.formulario.txtcodprov.value=ls_codpro;
	opener.document.formulario.txtnomprov.value=ls_nompro;
	opener.document.formulario.txtrifpro.value=ls_rifpro;
	opener.document.formulario.txtcodfuefin.value=ls_codfuefin;
	opener.document.formulario.txtcodfuefin.readOnly=true;
	opener.document.formulario.txtdenfuefin.value=ls_denfuefin;
	opener.document.formulario.txtdenfuefin.readOnly=true;
	opener.document.formulario.txtcodestpro1.value=ls_codestpro1;
	opener.document.formulario.txtcodestpro2.value=ls_codestpro2;
	opener.document.formulario.txtcodestpro3.value=ls_codestpro3;
	opener.document.formulario.txtcodestpro4.value=ls_codestpro4;
	opener.document.formulario.txtcodestpro5.value=ls_codestpro5;
	opener.document.formulario.hidestcla.value=ls_estcla;
	if (ls_codmon!='---')
	   {
	     opener.document.formulario.txtcodmon.value=ls_codmon;	   
	     opener.document.formulario.txtdenmon.value=ls_denmon;
	   }
	else
	   {
	     opener.document.formulario.txtcodmon.value = "";	   
	     opener.document.formulario.txtdenmon.value = "";	   
	   }
	opener.document.formulario.txtcodtipmod.value=ls_codtipmod;
	opener.document.formulario.txtcodtipmod.readOnly=true;
	opener.document.formulario.txtdenmodcla.value=ls_denmodcla;
	opener.document.formulario.txtdenmodcla.readOnly=true;
	opener.document.formulario.cmbpais.value=ls_codpai;
	opener.document.formulario.cmbestado.options[0]= new Option('--Seleccione--','');
	opener.document.formulario.cmbestado.options[1]= new Option(ls_desest,ls_codest);
	opener.document.formulario.cmbestado.value=ls_codest;
	opener.document.formulario.cmbmunicipio.options[0]= new Option('--Seleccione--','');
	opener.document.formulario.cmbmunicipio.options[1]= new Option(ls_denmun,ls_codmun);
	opener.document.formulario.cmbmunicipio.value=ls_codmun;
	opener.document.formulario.cmbparroquia.options[0]= new Option('--Seleccione--','');
	opener.document.formulario.cmbparroquia.options[1]= new Option(ls_denpar,ls_codpar);
	opener.document.formulario.cmbparroquia.value=ls_codpar;
	opener.document.formulario.despai.value=ls_despai;
	opener.document.formulario.desest.value=ls_desest;
	opener.document.formulario.desmun.value=ls_denmun;
	opener.document.formulario.txtnumdiacre.value=ls_numdiacre;
	opener.document.formulario.txtcodconobr.value=ls_codconobr;
	if(ls_ressoc=="1")
	{
	  opener.document.formulario.chkressoc.checked=true;	  
	}
	else
	{
	  opener.document.formulario.chkressoc.checked=false;	  
	}
	
	 if (ld_prentdesde=='1900-01-01' || ld_prentdesde=='01/01/1900')
	   {
	     ld_prentdesde = '';
	   }
	   else
	   {
	     opener.document.formulario.txtperentdesde.value=ld_prentdesde;
	   }
	if (ld_prenthasta=='1900-01-01' || ld_prenthasta=='01/01/1900')
	   {
	     ld_prenthasta = '';
	   }
	   else
	   {
	     opener.document.formulario.txtperenthasta.value=ld_prenthasta;
	   }
	ls_uniejeaso=trim(ls_uniejeaso);   
	if(ls_uniejeaso!="")
	{
		opener.document.formulario.txttipsol.value='SEP';
		opener.document.formulario.existe.value="TRUE";
		opener.document.formulario.tipo.value="OC";
		tipsol2='SEP';
	}
	else
	{
		opener.document.formulario.txttipsol.value='SOC';
		opener.document.formulario.existe.value="TRUE";
		opener.document.formulario.tipo.value="OC";
		tipsol2='SOC';
	}
	li_estciespg = "<?php echo $_SESSION["la_empresa"]["estciespg"] ?>";
    li_estciespi = "<?php echo $_SESSION["la_empresa"]["estciespi"] ?>";
	if (ls_estapro==1 || li_estciespg==1 || li_estciespi==1)
	   {
	     opener.document.formulario.rblugcom[0].disabled  = true;
		 opener.document.formulario.rblugcom[1].disabled  = true;
	     opener.document.formulario.radiotipbieordcom[0].disabled  = true;
		 opener.document.formulario.radiotipbieordcom[1].disabled  = true;
		 opener.document.formulario.cmbpais.disabled      = true;
		 opener.document.formulario.cmbestado.disabled    = true;
		 opener.document.formulario.cmbmunicipio.disabled = true;
		 opener.document.formulario.cmbparroquia.disabled = true;
		 opener.document.formulario.txtperentdesde.disabled = true;
	     opener.document.formulario.txtperenthasta.disabled = true;
		 opener.document.formulario.txtantpag.disabled      = true;
		 opener.document.formulario.cmbforpag.disabled      = true;
		 opener.document.formulario.cbmconcom.disabled      = true;
		 opener.document.formulario.txtconordcom.disabled   = true
		 opener.document.formulario.txtobscom.disabled      = true;
		 opener.document.formulario.chkbestsegcom.disabled  = true;
		 opener.document.formulario.cmbtipordcom.disabled   = true;
	   }
	opener.document.formulario.txtuniejeaso.value=ls_uniejeaso;
	opener.document.formulario.txtorigen.value=origen;
	parametros="";
	parametros=parametros+"&numordcom="+ls_numordcom+"&tipsol="+tipsol+"&tipsol2="+tipsol2;
	parametros=parametros+"&subtotal="+ld_monsubtot+"&cargos="+ld_monimp+"&total="+ld_montot;
	if(ls_estcondat=="B") // Bienes
	{
		proceso="LOADBIENES";
	}
	if(ls_estcondat=="S") // Servicios
	{
		proceso="LOADSERVICIOS";
	}
	if(parametros!="")
	{
		// Div donde se van a cargar los resultados
		document.getElementById("mensajes").innerHTML = 'Procesando ... !';
		
		divgrid = opener.document.getElementById("bienesservicios");
		divlocal = document.getElementById("resultados");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
 		ajax.onreadystatechange=function()
		{ 
			
			if(ajax.readyState==1)
			{
			
			    
				document.getElementById("mensajes").innerHTML = "<img src='../shared/imagenes/cargando2.gif' width='20' height='20'><br>Cargando datos...";
				
			}
			if(ajax.readyState==4)
			{
				if(ajax.status==200)
				{//mostramos los datos dentro del contenedor
				    document.getElementById("mensajes").innerHTML = 'Finalizado !';
					divgrid.innerHTML = ajax.responseText
					close();
				}
				else
				{
					if(ajax.status==404)
					{
						divgrid.innerHTML = "La p?gina no existe";
					}
					else
					{//mostramos el posible error     
						divgrid.innerHTML = "Error:".ajax.status;
					}
				}
				
			}
		}	
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("proceso="+proceso+""+parametros);
	}
}


function ue_copiar(ls_numordcom,ls_estcondat,ld_monsubtot,ld_monimp,ld_montot)
{
	procesando=f.procesando.value;
	if(ls_estcondat=="B")
	{
		totalbienes=ue_calcular_total_fila_opener("txtcodart");
		totalservicios=0;
	}
	else
	{
		totalservicios=ue_calcular_total_fila_opener("txtcodser");
		totalbienes=0;
	}
	parametros="";
	for(j=1;(j<=totalbienes);j++)
	{
		codart		 = eval("opener.document.formulario.txtcodart"+j+".value");
		denart		 = eval("opener.document.formulario.txtdenart"+j+".value");
		canart		 = eval("opener.document.formulario.txtcanart"+j+".value");
		unidad		 = eval("opener.document.formulario.cmbunidad"+j+".value");
		preart		 = eval("opener.document.formulario.txtpreart"+j+".value");
		subtotart    = eval("opener.document.formulario.txtsubtotart"+j+".value");
		carart       = eval("opener.document.formulario.txtcarart"+j+".value");
		totart       = eval("opener.document.formulario.txttotart"+j+".value");
		spgcuenta    = eval("opener.document.formulario.txtspgcuenta"+j+".value");
		unidadfisica = eval("opener.document.formulario.txtunidad"+j+".value");
		ls_codunieje = eval("opener.document.formulario.txtcoduniadmsep"+j+".value");
		ls_denunieje = eval("opener.document.formulario.txtdenuniadmsep"+j+".value");
		ls_codestpro = eval("opener.document.formulario.hidcodestpro"+j+".value");
		ls_estcla    = eval("opener.document.formulario.estcla"+j+".value");
		ls_numsep    = eval("opener.document.formulario.txtnumsolord"+j+".value");
	   
		parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
				   "&txtcanart"+j+"="+canart+"&cmbunidad"+j+"="+unidad+""+
				   "&txtpreart"+j+"="+preart+"&txtsubtotart"+j+"="+subtotart+""+
				   "&txtcarart"+j+"="+carart+"&txttotart"+j+"="+totart+""+
				   "&txtspgcuenta"+j+"="+spgcuenta+"&txtunidad"+j+"="+unidadfisica+"&txtdenuniadmsep"+j+"="+ls_denunieje+""+
				   "&txtnumsolord"+j+"="+ls_numsep+"&txtcoduniadmsep"+j+"="+ls_codunieje+""+
				   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla;
	}
	
	
	for(j=1;(j<totalservicios);j++)
	{
		codser		 = eval("opener.document.formulario.txtcodser"+j+".value");
		denser		 = eval("opener.document.formulario.txtdenser"+j+".value");
		canser		 = eval("opener.document.formulario.txtcanser"+j+".value");
		preser		 = eval("opener.document.formulario.txtpreser"+j+".value");
		subtotser	 = eval("opener.document.formulario.txtsubtotser"+j+".value");
		carser		 = eval("opener.document.formulario.txtcarser"+j+".value");
		totser       = eval("opener.document.formulario.txttotser"+j+".value");
		spgcuenta	 = eval("opener.document.formulario.txtspgcuenta"+j+".value");
		ls_codestpro = eval("opener.document.formulario.hidcodestpro"+j+".value");
		ls_codunieje = eval("opener.document.formulario.txtcoduniadmsep"+j+".value");
		ls_denunieje = eval("opener.document.formulario.txtdenuniadmsep"+j+".value");
		ls_estcla    = eval("opener.document.formulario.estcla"+j+".value");
		ls_numsep    = eval("opener.document.formulario.txtnumsolord"+j+".value"); 
		//ls_hidspgcuentas= eval("opener.document.formulario.txtspgcuenta"+j+".value");

		parametros=parametros+"&txtcodser"+j+"="+codser+"&txtdenser"+j+"="+denser+""+
				   "&txtcanser"+j+"="+canser+"&txtpreser"+j+"="+preser+""+
				   "&txtsubtotser"+j+"="+subtotser+"&txtcarser"+j+"="+carser+""+
				   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
				   "&txtdenuniadmsep"+j+"="+ls_denunieje+"&txtnumsolord"+j+"="+ls_numsep+""+
				   "&txttotser"+j+"="+totser+"&txtspgcuenta"+j+"="+spgcuenta+"&txtcoduniadmsep"+j+"="+ls_codunieje;
	}
	totalcargos=ue_calcular_total_fila_opener("txtcodservic");
	opener.document.formulario.totrowcargos.value=totalcargos;
	for(j=1;j<=totalcargos;j++)
	{
		codservic=eval("opener.document.formulario.txtcodservic"+j+".value");
		codcar=eval("opener.document.formulario.txtcodcar"+j+".value");
		dencar=eval("opener.document.formulario.txtdencar"+j+".value"); 
		bascar=eval("opener.document.formulario.txtbascar"+j+".value");
		moncar=eval("opener.document.formulario.txtmoncar"+j+".value");
		subcargo=eval("opener.document.formulario.txtsubcargo"+j+".value");
		cuentacargo=eval("opener.document.formulario.cuentacargo"+j+".value"); 
		formulacargo= eval("opener.document.formulario.formulacargo"+j+".value");
		ls_numsep= eval("opener.document.formulario.hidnumsepcar"+j+".value");
		codprogcargo=eval("opener.document.formulario.codprogcargo"+j+".value");
		estclacargo=eval("opener.document.formulario.estclacargo"+j+".value"); 
		
		parametros=parametros+"&txtcodservic"+j+"="+codservic+"&txtcodcar"+j+"="+codcar+
				   "&txtdencar"+j+"="+dencar+"&txtbascar"+j+"="+bascar+
				   "&txtmoncar"+j+"="+moncar+"&txtsubcargo"+j+"="+subcargo+
				   "&cuentacargo"+j+"="+cuentacargo+"&formulacargo"+j+"="+formulacargo+
				   "&hidnumsepcar"+j+"="+ls_numsep+"&codprogcargo"+j+"="+codprogcargo+
				   "&estclacargo"+j+"="+estclacargo;
	}
	if(procesando=="1")
	{
		alert("Aun se esta procesando");
	}
	else
	{
		ls_coduniadm  = opener.document.formulario.txtcodunieje.value;
		ls_denuniadm  = opener.document.formulario.txtdenunieje.value;
		ls_codestpro1 = opener.document.formulario.txtcodestpro1.value;
		ls_codestpro2 = opener.document.formulario.txtcodestpro2.value;
		ls_codestpro3 = opener.document.formulario.txtcodestpro3.value;
		ls_codestpro4 = opener.document.formulario.txtcodestpro4.value;
		ls_codestpro5 = opener.document.formulario.txtcodestpro5.value;
		ls_estclauni  = opener.document.formulario.hidestcla.value; 
		ls_tipoconpro=opener.document.formulario.tipconpro.value;
		ls_codestpre  = ls_codestpro1+ls_codestpro2+ls_codestpro3+ls_codestpro4+ls_codestpro5;
		parametros=parametros+"&numordcom="+ls_numordcom+"&tipsol=SOC"+"&tipoconpro="+ls_tipoconpro;
		parametros=parametros+"&subtotal="+ld_monsubtot+"&cargos="+ld_monimp+"&total="+ld_montot+"&totalbienes="+totalbienes+"&totalservicios="+totalservicios;
		parametros=parametros+"&coduniadm="+ls_coduniadm+"&denuniadm="+ls_denuniadm+"&codestpre="+ls_codestpre+"&estclauni="+ls_estclauni+"&totalcargos="+totalcargos;
		if(ls_estcondat=="B") // Bienes
		{
			proceso="COPIARBIENES";
		}
		if(ls_estcondat=="S") // Servicios
		{
			proceso="COPIARSERVICIOS";
		}
		if(parametros!="")
		{
			// Div donde se van a cargar los resultados
			divgrid = opener.document.getElementById("bienesservicios");
			divlocal = document.getElementById("resultados");
			// Instancia del Objeto AJAX
			ajax=objetoAjax();
			// Pagina donde est?n los m?todos para buscar y pintar los resultados
			ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
			ajax.onreadystatechange=function()
			{ 
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
						f.procesando.value="";
					}
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La p?gina no existe";
						}
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					}
					
				}
				else
				{
					f.procesando.value="1";
				}
			}	
			ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			// Enviar todos los campos a la pagina para que haga el procesamiento
			ajax.send("proceso="+proceso+""+parametros);
		}
	}
}

function ue_aceptar_reporte_desde(ls_numordcom)
{
   opener.document.formulario.txtnumordcomdes.value=ls_numordcom;
   opener.document.formulario.txtnumordcomdes.readOnly=true;
   close();
}

function ue_aceptar_reporte_hasta(ls_numordcom)
{
   opener.document.formulario.txtnumordcomhas.value=ls_numordcom;
   opener.document.formulario.txtnumordcomhas.readOnly=true;
   close();
}

function ue_recalcularmontos()
{
	opener.ue_recalcularmontos();	
}

function currencyDate(date)
{
ls_date=date.value;
li_long=ls_date.length;
f=document.form1;
		 
	if(li_long==2)
	{
		ls_date=ls_date+"/";
		ls_string=ls_date.substr(0,2);
		li_string=parseInt(ls_string,10);
		if((li_string>=1)&&(li_string<=31))
		{
			date.value=ls_date;
		}
		else
		{
			date.value="";
		}
		
	}
	if(li_long==5)
	{
		ls_date=ls_date+"/";
		ls_string=ls_date.substr(3,2);
		li_string=parseInt(ls_string,10);
		if((li_string>=1)&&(li_string<=12))
		{
			date.value=ls_date;
		}
		else
		{
			date.value=ls_date.substr(0,3);
		}
	}
	if(li_long==10)
	{
		ls_string=ls_date.substr(6,4);
		li_string=parseInt(ls_string,10);
		if((li_string>=1900)&&(li_string<=2090))
		{
			date.value=ls_date;
		}
		else
		{
			date.value=ls_date.substr(0,6);
		}
	}
}  

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	ls_numordcom=f.txtnumordcom.value;
	ls_codpro=f.txtcodprov.value;
	ld_fecregdes=f.txtfecdes.value;
	ld_fecreghas=f.txtfechas.value;
	ls_origen = f.origen.value;
       if (ls_origen=='AS')
          {
            ls_tipordcom = 'S';
          } 
       else
          {
            ls_tipordcom=f.cmbtipordcom.value;   
          }       
	if ((ls_origen=="REPORTE-DESDE")||(ls_origen=="REPORTE-HASTA"))
	{
		ls_tipo=ls_origen;
	}
	else
	{
		ls_tipo=f.tipo.value;
	}
	ls_orden=f.orden.value;
	ls_campoorden=f.campoorden.value;
	if((ld_fecregdes!="")&&(ld_fecreghas!=""))
	{
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById('resultados');
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_soc_c_catalogo_ajax.php",true);
 		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
			}
			else
			{
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
                    }
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La p?gina no existe";
                        }
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					 }
				}
					
			}
	}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("catalogo=ORDEN-COMPRA&numordcom="+ls_numordcom+"&fecregdes="+ld_fecregdes+"&fecreghas="+ld_fecreghas+
				  "&tipo="+ls_tipo+"&tipordcom="+ls_tipordcom+"&orden="+ls_orden+"&campoorden="+ls_campoorden+"&codpro="+ls_codpro);
	}
	else
	{
		alert("Debe seleccionar un rango de Fecha.");
	}
}
</script>
</html>