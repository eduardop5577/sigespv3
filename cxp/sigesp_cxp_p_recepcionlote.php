<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_cxp.php");
	$io_fun_cxp=new class_funciones_cxp();
	$arrResultado=$io_fun_cxp->uf_load_seguridad("CXP","sigesp_cxp_p_aprobacionrecepcion.php",$ls_permisos,$la_seguridad,$la_permisos);
    $ls_estrescxp=$_SESSION["la_empresa"]["estrescxp"];
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_fun_cxp,$ls_operacion,$ls_codtipsol,$ld_fecregdes,$ld_fecreghas,$ld_fecaprord,$li_totrow;
		
		$ls_operacion=$io_fun_cxp->uf_obteneroperacion();
		$ls_codtipsol="";
		$ld_fecregdes=date("01/m/Y");
		$ld_fecreghas=date("d/m/Y");
		$ld_fecaprord=date("d/m/Y");
		$li_totrow=0;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $li_totrow,$ls_tipope,$ld_fecaprord;
		
		$li_totrow = $_POST["totrow"];
		$ls_tipope = $_POST["rdtipooperacion"];
		$ld_fecaprord  =$_POST["txtfecaprord"];
   }
   //--------------------------------------------------------------
   
   //--------------------------------------------------------------
   function uf_load_data()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 29/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $ls_parametros,$ls_tipodestino,$ls_codprovben,$ls_nomprovben,$ls_rifpro,$ls_codtipdoc;
		$li_totrow=$_POST["rows"];
		$ls_codtipdoc=$_POST["cmbcodtipdoc"];
		$ls_tipodestino=$_POST["cmbtipdes"];
		$ls_codprovben=$_POST["txtcodigo"];
		$ls_nomprovben=$_POST["txtnombre"];
		$ls_rifpro=$_POST["txtrifpro"];
		$ls_parametros="";
		for($li_fila=1;($li_fila<$li_totrow);$li_fila++)
		{
			$ls_numrecdoc=$_POST["txtnumrecdoc".$li_fila];
			$ls_existe=$_POST["txtexiste".$li_fila];
			$ls_numref=$_POST["txtnumref".$li_fila];
			$ls_fecha=$_POST["txtfecha".$li_fila];
			$ls_spgcuenta=$_POST["txtspgcuenta".$li_fila];
			$li_monto=$_POST["txtmonto".$li_fila];

			$ls_parametros=$ls_parametros."&txtnumrecdoc".$li_fila."=".$ls_numrecdoc."&txtexiste".$li_fila."=".$ls_existe."&txtfecha".$li_fila."=".$ls_fecha."".
					   					  "&txtnumref".$li_fila."=".$ls_numref."&txtspgcuenta".$li_fila."=".$txtspgcuenta."".
										  "&txtmonto".$li_fila."=".$li_monto;
		}
		if($li_fila>1)
		{
			$ls_parametros=$ls_parametros."&rows=".$li_totrow;
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
<title >Recepci&oacute;n de Documentos por Lote </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
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
<script type="text/javascript"  src="js/funcion_cxp.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/cxp.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	uf_load_data();

	require_once("class_folder/sigesp_cxp_c_recepcionlote.php");
	$io_cxp=new sigesp_cxp_c_recepcionlote("../");
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_mensajes=new class_mensajes();		
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fecha=new class_fecha();		
	uf_limpiarvariables();
	if($ls_estrescxp=="1")
	{
		$ls_chkestrescxp="checked";
	}
	switch ($ls_operacion) 
	{
		case "PROCESAR":
			uf_load_variables();
			$lb_valido=false;
			$ls_generar=$io_fun_cxp->uf_obtenervalor("chkestrescxp","");
			for($li_i=0;$li_i<=$li_totrow;$li_i++)
			{
				if (array_key_exists("chkaprobacion".$li_i,$_POST))
				{
					$ls_numrecdoc=trim($io_fun_cxp->uf_obtenervalor("txtnumrecdoc".$li_i,""));
					$ld_fecregdoc=$io_fun_cxp->uf_obtenervalor("txtfecregdoc".$li_i,"");
					$ls_codpro=$io_fun_cxp->uf_obtenervalor("txtcodpro".$li_i,"");
					$ls_cedben=$io_fun_cxp->uf_obtenervalor("txtcedben".$li_i,"");
					$ls_codtipdoc=$io_fun_cxp->uf_obtenervalor("txtcodtipdoc".$li_i,"");
					switch ($ls_tipope)
					{
						case 0:
							$lb_valido=$io_fecha->uf_comparar_fecha($ld_fecregdoc,$ld_fecaprord);
							if($lb_valido)
							{
								$lb_existe=$io_cxp->uf_validar_estatus_recepcion($ls_numrecdoc,"1",$ls_codpro,$ls_cedben,$ls_codtipdoc);
								if(!$lb_existe)
								{
									$lb_valido=$io_cxp->uf_update_estatus_recepciones($ls_numrecdoc,1,$ls_codpro,$ls_cedben,
																					  $ls_codtipdoc,$ld_fecregdoc,$ls_generar,$la_seguridad);
								}
								else
								{
									$io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." ya esta aprobada");
								}
							}
							else
							{
								$io_mensajes->message("La Fecha de Registro de la Recepcion ".$ls_numrecdoc." debe ser menor a la fecha de Aprobacion");
							}							
							break;
		
						case 1:
							$lb_existe=$io_cxp->uf_validar_recepciones($ls_numrecdoc,$ls_codpro,$ls_cedben,$ls_codtipdoc);
							if($lb_existe)
							{
								$lb_valido=$io_cxp->uf_update_estatus_recepciones($ls_numrecdoc,0,$ls_codpro,$ls_cedben,
																				  $ls_codtipdoc,$ld_fecregdoc,$ls_generar,$la_seguridad);
							}
							else
							{
								$io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." debe estar en Registro");
							}
							break;
					}
				}
			}
			if($lb_valido)
			{
				$io_mensajes->message("El proceso se realizo con Exito");
			}
			else
			{
				$io_mensajes->message("No se pudo realizar el proceso");
			}
			uf_limpiarvariables();
			break;

	}
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="807" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Cuentas por Pagar </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
    </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
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
<form name="formulario" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_cxp->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_cxp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
  <table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
    <td width="760" height="136">
      <p>&nbsp;</p>
        <table width="741" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr> 
            <td colspan="4" class="titulo-ventana">Recepci&oacute;n de Documentos por Lote </td>
          </tr>
          <tr> 
            <td width="22%" height="22"><div align="right"></div></td>
            <td width="60%" colspan="2"><div align="right">Fecha</div></td>
            <td width="18%"><input name="txtfecaprord" type="text" id="txtfecaprord" style="text-align:center" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_fecaprord; ?>" size="15"  datepicker="true"></td>
          </tr>
          <tr>
            <td height="22"><div align="right">Estatus</div></td>
            <td colspan="2"><div align="left">
              <input name="txtestatus" type="text" class="sin-borde2" id="txtestatus" value="Registro" size="20" readonly>
            </div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">Tipo de Documento</div></td>
            <td colspan="2"><div align="left">
              <?php $io_cxp->uf_load_tipodocumento($ls_codtipdoc,"C");?>
            </div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">
              <select name="cmbtipdes" id="cmbtipdes" onChange="javascript: ue_cambiardestino();" >
                <option value="-" selected>-- Seleccione Uno --</option>
                <option value="P" <?php if($ls_tipodestino=="P"){ print "selected";} ?>>PROVEEDOR</option>
                <option value="B" <?php if($ls_tipodestino=="B"){ print "selected";} ?>>BENEFICIARIO</option>
              </select>
            </div></td>
            <td colspan="2"><div align="left">
              <input name="txtcodigo" type="text" id="txtcodigo" value="<?php print $ls_codprovben;?>" size="15" maxlength="10" readonly style="text-align:center">
              <input name="txtnombre" type="text" class="sin-borde" id="txtnombre" value="<?php print $ls_nomprovben;?>" size="45" maxlength="30" readonly>
</div></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right">RIF</div></td>
            <td colspan="2"><input name="txtrifpro" type="text" class="texto-azul" id="txtrifpro" value="<?php print $ls_rifpro;?>"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td colspan="2"><input name="arcimp" type="file" class="formato-blanco" id="arcimp" size="30"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td colspan="2"><input name="btnimportar" type="button" class="boton" id="btnimportar" value="Agregar" onClick="javascript:uploadAjax(this.form);"></td>
            <td>&nbsp;</td>
          </tr>
          <?php            
          if($_SESSION["la_empresa"]["estfilpremod"]=='1'){ 
	  	  ?>
	  	<?php 
	  	}
	  	?>
          <?php            
          if($_SESSION["la_empresa"]["estrescxp"]=='1'){ 
	  	  ?>
	  	<?php 
	  	}
	  	?>
        </table>
        <table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="748"><input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrow; ?>">
            <input name="operacion" type="hidden" id="operacion">
            
            <input name="codigocuenta" type="hidden" id="codigocuenta">
            <input name="txtproveedor" type="hidden" id="txtproveedor">
            <input name="tipocontribuyente" type="hidden" id="tipocontribuyente">
            <input name="ageviapro" type="hidden" id="ageviapro">
            <input name="parametros" type="hidden" id="parametros" value="<?php print $ls_parametros; ?>"></td>
          </tr>
          <tr>
            <td><div id="detalles"></div></td>
          </tr>
        </table>        </td>
  </tr>
</table>
</form>   
<?php
	$io_cxp->uf_destructor();
	unset($io_cxp);
?>   
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_cambiardestino()
{
	f=document.formulario;
	// Se verifica si el destino es un proveedor ó beneficiario y se carga el catalogo
	// dependiendo de esa información
	f.txtcodigo.value="";
	f.txtnombre.value="";
	tipdes=ue_validarvacio(f.cmbtipdes.value);
	tipo="CATALOGO";
	if(tipdes!="-")
	{
		if(tipdes=="P")
		{
			window.open("sigesp_cxp_cat_proveedor.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}
		else
		{
			window.open("sigesp_cxp_cat_beneficiario.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}	
	}
}
function ue_cambiartipodocumento()
{
}
function uf_detalles_movimientos(row)
{
	f=document.formulario;
	numrecdoc=eval("f.txtnumrecdoc"+row+".value");
	numref=eval("f.txtnumref"+row+".value");
	fecregfac=eval("f.txtfecha"+row+".value");
	spgcuenta=eval("f.txtspgcuenta"+row+".value");
	monto=eval("f.txtmonto"+row+".value");
	tipdes=f.cmbtipdes.value;
	codproben=f.txtcodigo.value;
	nomproben=f.txtnombre.value;
	rifproben=f.txtrifpro.value;
	codtipdoc=f.cmbcodtipdoc.value;
	codigocuenta=f.codigocuenta.value;
	tipconpro=f.tipocontribuyente.value;
	operacion="NUEVO";
	valido=true;
	if(codproben=="")
	{
		valido=false;
		alert("Debe Indicar el Proveedor/Beneficiario");	
	}
	if(rifproben=="")
	{
		valido=false;
		alert("El Proveedor/Beneficiario no tiene registrado su RIF");	
	}
	if(numrecdoc=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Factura");	
	}
	if(numrecdoc=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Factura");	
	}
	if(numref=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Control");	
	}
	if(fecregfac=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido la fecha de la Factura");	
	}
	if(spgcuenta=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido la cuenta Presupuestaria");	
	}
	if(monto=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el monto de la Factura");	
	}
	if(codtipdoc=="-")
	{
		valido=false;
		alert("Debe seleccionar un tipo de Documento");	
	}
	if(valido)
	{
		eval("f.txtexiste"+row+".value='1'");
		window.open("sigesp_cxp_p_detrecepcionlote.php?numrecdoc="+numrecdoc+"&numref="+numref+"&fecregfac="+fecregfac
					+"&spgcuenta="+spgcuenta+"&monto="+monto+"&tipdes="+tipdes+"&codproben="+codproben+"&nomproben="+nomproben+"&codigocuenta="+codigocuenta+"&tipconpro="+tipconpro
					+"&rifproben="+rifproben+"&codtipdoc="+codtipdoc+"&operacion="+operacion,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=1000,height=1200,resizable=yes,location=no");
	}
}

function uf_detalles_movimientos2(row)
{
	f=document.formulario;
	numrecdoc=eval("f.txtnumrecdoc"+row+".value");
	numref=eval("f.txtnumref"+row+".value");
	fecregfac=eval("f.txtfecha"+row+".value");
	compromiso=eval("f.txtcompromiso"+row+".value");
	concepto=eval("f.txtconcepto"+row+".value");
	cuentadebe=eval("f.txtcuentadebe"+row+".value");
	cuentahaber=eval("f.txtcuentahaber"+row+".value");
	tipdes=f.cmbtipdes.value;
	codproben=f.txtcodigo.value;
	nomproben=f.txtnombre.value;
	rifproben=f.txtrifpro.value;
	codtipdoc=f.cmbcodtipdoc.value;
	codigocuenta=f.codigocuenta.value;
	tipconpro=f.tipocontribuyente.value;
	operacion="NUEVO";
	valido=true;
	if(codproben=="")
	{
		valido=false;
		alert("Debe Indicar el Proveedor/Beneficiario");	
	}
	if(rifproben=="")
	{
		valido=false;
		alert("El Proveedor/Beneficiario no tiene registrado su RIF");	
	}
	if(numrecdoc=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Factura");	
	}
	if(numrecdoc=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Factura");	
	}
	if(numref=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Control");	
	}
	if(fecregfac=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido la fecha de la Factura");	
	}
	if(compromiso=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el Compromiso");	
	}
	if(codtipdoc=="-")
	{
		valido=false;
		alert("Debe seleccionar un tipo de Documento");	
	}
	if(valido)
	{
		eval("f.txtexiste"+row+".value='1'");
		window.open("sigesp_cxp_p_detrecepcionlotecausa.php?numrecdoc="+numrecdoc+"&numref="+numref+"&fecregfac="+fecregfac
					+"&compromiso="+compromiso+"&tipdes="+tipdes+"&codproben="+codproben+"&nomproben="+nomproben+"&codigocuenta="+codigocuenta
					+"&tipconpro="+tipconpro+"&rifproben="+rifproben+"&codtipdoc="+codtipdoc+"&operacion="+operacion+"&concepto="+concepto
					+"&cuentadebe="+cuentadebe+"&cuentahaber="+cuentahaber,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=1000,height=1200,resizable=yes,location=no");
	}
}


function uploadAjax(form){

	f=document.formulario;
	tipdes=f.cmbtipdes.value;
	codproben=f.txtcodigo.value;
	codtipdoc=f.cmbcodtipdoc.value;

	if((tipdes!="")&&(codproben!="")&&(codtipdoc!=""))
	{
		/* Creamos un objeto FormData que es un formulario con 	enctype=multipart/form-data 
		y le pasamos como parametro el formulario HTML */
	 
		var Data = new FormData(form);
		
		/* Creamos el objeto que hara la petición AJAX al servidor, debemos de validar si existe el 	objeto " XMLHttpRequest" ya que en internet explorer viejito no esta, y si no esta usamos 
		"ActiveXObject" */
		
		if(window.XMLHttpRequest) {
			var Req = new XMLHttpRequest();
		}else if(window.ActiveXObject) {
			var Req = new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		//Pasándole la url a la que haremos la petición
		Req.open("POST", "class_folder/sigesp_cxp_c_recepcionlote_ajax.php", true);
		divgrid = document.getElementById('detalles');
		
		/* Le damos un evento al request, esto quiere decir que cuando
		termine de hacer la petición, se ejecutara este fragmento de
		código */
		
		Req.onload = function(Event) {
			//Validamos que el status http sea  ok
			if (Req.status == 200) {
				/*Como la info de respuesta vendrá en JSON 
				la parseamos */
						divgrid.innerHTML = Req.responseText;
			} else {
					console.log(Req.status); //Vemos que paso.
			}
		};	  
		
		//Enviamos la petición
		Req.send(Data);
	}
	else
	{
		alert("Debe llenar todos los datos");
	}
}


function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_reload()
{
	f=document.form1;
	funcion="RELOAD";
	parametros=f.parametros.value;
	if(parametros!="")
	{
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById("detalles");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_scb_c_movbancolote_ajax.php",true);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX 
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
							divgrid.innerHTML = "La página no existe";
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
		ajax.send("funcion="+funcion+""+parametros);
	}
}


</script> 
<?php
if($ls_parametros!="")
{
	print "<script language=JavaScript>";
	print "   ue_reload();";
	print "</script>";
}
?>		  

</html>