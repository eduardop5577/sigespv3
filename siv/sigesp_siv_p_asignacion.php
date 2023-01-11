<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_siv.php");
	$io_fun_siv=new class_funciones_siv();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid=new grid_param();
	$ls_permisos="";
	$la_seguridad=array();
	$la_permisos=array();
	$arrResultado=$io_fun_siv->uf_load_seguridad("SIV","sigesp_siv_p_asignacion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado);
//	$ls_reporte=$io_fun_siv->uf_select_config("siv","REPORTE","FORMATO_SOLPAG","sigesp_siv_rfs_solicitudes.php","C");
//	$ls_configuracion=$io_fun_siv->uf_select_config("siv","CONFIGURACION","VARIOS_REPORTES","-","C");
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 19/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_siv,$ld_fecasi,$ls_codasi,$ls_codcau,$ls_dencau,$ls_codrespri,$ls_denrespri,$ls_codresuso,$ls_denresuso,$ls_codalmdes,$ls_nomalmdes,$ls_obsasi,$li_canartemp;
		global $ls_existe,$ls_operacion,$li_totrowsc;
		
		$ls_operacion=
		$ld_fecasi=date("d/m/Y");
		$ls_codasi="";
		$ls_codcau="";
		$ls_dencau="";
		$ls_codrespri="";
		$ls_denrespri="";
		$ls_codresuso="";
		$ls_denresuso="";
		$ls_codalmdes="";
		$ls_nomalmdes="";
		$ls_obsasi="";
		$li_canartemp="";
		$ls_operacion=$io_fun_siv->uf_obteneroperacion();
		$ls_existe=$io_fun_siv->uf_obtenerexiste();
		$li_totrowsc=0;
   }
   //--------------------------------------------------------------------------------------------------------------

   //--------------------------------------------------------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 23/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $ls_existe,$ld_fecasi,$ls_codasi,$ls_codcau,$ls_dencau,$ls_codrespri,$ls_denrespri,$ls_codresuso,$ls_denresuso,$ls_codalmdes,$ls_nomalmdes,$ls_obsasi,$li_canartemp;
		global $li_totrowartsal,$li_totrowartent,$li_totrowsc;
		
		$ld_fecasi=$_POST["txtfecasi"];
		$ls_codasi=$_POST["txtcodasi"];
		$ls_codcau=$_POST["txtcodcau"];
		$ls_dencau=$_POST["txtdencau"];
		$ls_codrespri=$_POST["txtcodrespri"];
		$ls_denrespri=$_POST["txtdenrespri"];
		$ls_codresuso=$_POST["txtcodresuso"];
		$ls_denresuso=$_POST["txtdenresuso"];
		$ls_codalmdes=$_POST["txtcodalmdes"];
		$ls_nomalmdes=$_POST["txtnomfisdes"];
		$ls_obsasi=$_POST["txtobsasi"];
		$li_totrowartsal=$_POST["totrowartsal"];
		$li_totrowartent=$_POST["totrowartent"];
		$li_canartemp=$_POST["txtcanartemp"];
		$ls_existe=$_POST["existe"];
		$li_totrowsc= $_POST["totalfilasc"];


   }
   //--------------------------------------------------------------------------------------------------------------

   //--------------------------------------------------------------------------------------------------------------
   function uf_load_data($as_parametros)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $li_totrowartsal,$li_totrowartent,$io_fun_siv;	
		$ls_totpaqreq=$io_fun_siv->uf_obtenervalor("txtcanartemp",0);
		for($li_i=1;$li_i<$li_totrowartsal;$li_i++)
		{
			$ls_codart=trim($io_fun_siv->uf_obtenervalor("txtcodart".$li_i,""));
			$ls_denart=trim($io_fun_siv->uf_obtenervalor("txtdenart".$li_i,""));
			$ls_coddetart=trim($io_fun_siv->uf_obtenervalor("txtcoddetart".$li_i,""));
            $as_parametros=$as_parametros."&txtcodart".$li_i."=".$ls_codart."&txtdenart".$li_i."=".$ls_denart."".             
                                          "&txtcoddetart".$li_i."=".$ls_coddetart.""; 
										  
		}
		$as_parametros=$as_parametros."&totrowart=".$li_totrowartsal."";
		return $as_parametros;
   }
   //--------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Movimientos de Materiales</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_siv.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
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
</style>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<link href="css/siv.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {font-weight: bold}
-->
</style></head>
<body>
<?php
	require_once("class_folder/sigesp_siv_c_asignacion.php");
	$io_siv=new sigesp_siv_c_asignacion("../");
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_mensajes=new class_mensajes();		
	uf_limpiarvariables();
	$arrResultado=$io_siv->uf_siv_load_tipoarticulo();
	$ls_codtipart=$arrResultado["as_value"];
	$ls_dentipart=$arrResultado["as_dentipart"];
	switch($ls_operacion)
	{
		case "NUEVO":
			require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
			$io_keygen= new sigesp_c_generar_consecutivo();
			$ls_codasi= $io_keygen->uf_generar_numero_nuevo("SIV","siv_asignacion","codasi","SIVASI",15,"","","");
			if($ls_codemppro===false)
			{
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			unset($io_keygen);
			break;

		case "GUARDAR":
			uf_load_variables();
			$arrResultado=$io_siv->uf_guardar($ls_existe,$ld_fecasi,$ls_codasi,$ls_codcau,$ls_codrespri,$ls_codresuso,$ls_codalmdes,$ls_obsasi,$li_totrowartsal,$la_seguridad);
			$lb_valido=$arrResultado["lb_valido"];
			$ls_numsol=$arrResultado["as_numsol"];
			$ls_parametros=uf_load_data($ls_parametros);
			if($lb_valido)
			{
				$ls_existe="TRUE";
			}
		break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_siv->uf_delete($ls_codemppro,$la_seguridad);
			if(!$lb_valido)
			{
				$ls_parametros=uf_load_data($ls_parametros);
			}
			else
			{
				uf_limpiarvariables();
			}
		break;
		case "CALCULARCONTABLE":
			uf_load_variables();
			$ls_parametros=uf_load_data($ls_parametros);
			$ls_titlecontable="Detalle Contable";
			$li_widthcontable=600;
			$ls_namecontable="grid";
			$lo_titlecontable[1]="Cuenta";
			$lo_titlecontable[2]="Denominacion";
			$lo_titlecontable[3]="Debe/Haber";
			$lo_titlecontable[4]="Monto";
			$li_canartemp=str_replace(".","",$li_canartemp);
			$li_canartemp=str_replace(",",".",$li_canartemp);
			
			$li_montotartsal=   $_POST["txtmontotartsal"];
			$arrResultado=$io_siv->uf_siv_buscar_cuentaalmacen($ls_codalmdes);
			$ls_sccuenta=$arrResultado["sc_cuenta"];
			$ls_dencuenta=$arrResultado["dencta"];
			if($ls_sccuenta!="")
			{
				$li_totrowsc=1;
				$lo_objectc[1][1]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=20  value='".$ls_sccuenta."' readonly style='text-align:center'>";
				$lo_objectc[1][2]="<input  name=txtdencuenta".$li_totrowsc."  type=text   id=txtdencuenta".$li_totrowsc."  class=sin-borde size=40  value='".$ls_dencuenta."'   readonly style='text-align:left'>";
				$lo_objectc[1][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=10  value='DEBE'   readonly style='text-align:center'>";
				$lo_objectc[1][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=20  value='".$li_montotartsal."' style='text-align:right' readonly>";
			}
			


			$arrResultado=$io_siv->uf_siv_buscar_cuentaalmacen($ls_codalmori);
			$ls_sccuenta=$arrResultado["sc_cuenta"];
			$ls_dencuenta=$arrResultado["dencta"];
			if($ls_sccuenta!="")
			{
				$li_totrowsc=2;
				$lo_objectc[2][1]="<input  name=txtsccuenta".$li_totrowsc." type=text   id=txtsccuenta".$li_totrowsc." class=sin-borde size=20  value='".$ls_sccuenta."' readonly style='text-align:center'>";
				$lo_objectc[2][2]="<input  name=txtdencuenta".$li_totrowsc."  type=text   id=txtdencuenta".$li_totrowsc."  class=sin-borde size=40  value='".$ls_dencuenta."'   readonly style='text-align:left'>";
				$lo_objectc[2][3]="<input  name=txtdebhab".$li_totrowsc."   type=text   id=txtdebhab".$li_totrowsc."   class=sin-borde size=10  value='HABER'   readonly style='text-align:center'>";
				$lo_objectc[2][4]="<input  name=txtmonto".$li_totrowsc."    type=text   id=txtcansolc".$li_totrowsc."  class=sin-borde size=20  value='".$li_montotartsal."' style='text-align:right' readonly>";
			}
			
			//$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			//$li_totrows=$li_totrows-1;	
			break;
	}

?>
<table width="751" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="807" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7"><table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
  <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Inventario </td>
      <td width="353" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
    <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
    </table></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="28" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></div></td>
    <td class="toolbar" width="28"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Guardar"></a></div></td>
    <td class="toolbar" width="28"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></div></td>
    <td class="toolbar" width="28"><div align="center"><a href="javascript: ue_eliminar();"></a><a href="javascript: ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></div></td>
    <td class="toolbar" width="28"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></td>
    <td class="toolbar" width="28"><div align="center"></div></td>
    <td class="toolbar" width="28"><div align="center"><a href="javascript: ue_ayuda();"></a></div></td>
    <td class="toolbar" width="28"><div align="center"></div></td>
    <td class="toolbar" width="28"><div align="center"></div></td>
    <td class="toolbar" width="28"><div align="center"></div></td>
    <td class="toolbar" width="469">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
  <form name="formulario" method="post" action="" id="formulario">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_siv->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_siv);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
	<table width="806" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="790"  height="136"><p>&nbsp;</p>
            <table width="721" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr>
                <td colspan="4" class="titulo-ventana">Movimientos de Materiales </td>
              </tr>
              <tr>
                <td width="150" height="22" align="center"></td>
                <td height="13" colspan="3" align="center"><div align="left"></div></td>
              </tr>
              <tr>
                <td height="20"><div align="right"></div></td>
                <td width="317" height="22"><div align="left">
                  <input name="txtcodusu" type="hidden" id="txtcodusu" onKeyPress="javascript: ue_validarcomillas(this);" value="<?php print $ls_codusu?>" size="20" maxlength="60" readonly>
                </div></td>
                <td width="117" align="right">Fecha</td>
                <td width="135"><input name="txtfecasi" type="text" id="txtfecasi" style="text-align:center " onKeyPress="ue_separadores(this,'/',patron,true);" value="<?php print $ld_fecasi; ?>" size="12" maxlength="10" datepicker="true"></td>
              </tr>
              <tr>
                <td height="20"><div align="right">Comprobante</div></td>
                <td height="22" colspan="3"><input name="txtcodasi" type="text" id="txtcodasi" value="<?php print $ls_codasi; ?>" size="17" maxlength="15" style="text-align:center" readonly></td>
              </tr>
              <tr>
                <td height="20"><div align="right">Causa de Movimiento </div></td>
                <td height="22" colspan="3"><input name="txtcodcau" type="text" id="txtcodcau" value="<?php print $ls_codcau; ?>" size="6" maxlength="3" style="text-align:center" readonly>
                  <a href="javascript: ue_buscarcausa();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>                    <a href="javascript: ue_catatipart();"></a>
                    <input name="txtdencau" type="text" class="sin-borde" id="txtdencau" value="<?php print $ls_dencau; ?>" size="30" readonly></td>
              </tr>
              <tr>
                <td height="20"><div align="right">Responsable por Uso </div></td>
                <td height="22" colspan="3"><div align="left">
                  <input name="txtcodresuso" type="text" style="text-align:center" id="txtcodresuso" value="<?php print $ls_codresuso; ?>" size="15" maxlength="10" readonly>
                  <a href="javascript: ue_catalogo_responsable_uso();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                  <input name="txtdenresuso" type="text" style="text-align:left" class="sin-borde" id="txtdenresuso" value="<?php print $ls_denresuso; ?>" size="45" readonly>
                </div></td>
              </tr>
              <tr>
                <td height="20"><div align="right">Responsable Primario  </div></td>
                <td height="22" colspan="3"><div align="left">
                  <input name="txtcodrespri" type="text"  style="text-align:center" id="txtcodrespri" value="<?php print $ls_codrespri; ?>" size="15" maxlength="10" readonly>
                  <a href="javascript: ue_catalogo_responsable_primario();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                  <input name="txtdenrespri" type="text"  style="text-align:left" class="sin-borde" id="txtdenrespri" value="<?php print $ls_denrespri ?>" size="45" readonly>
                </div></td>
              </tr>
              <tr>
                <td height="20"><div align="right">Almac&eacute;n </div></td>
                <td height="22" colspan="3"><div align="left">
                    <input name="txtcodalmdes" type="text" id="txtcodalmdes" value="<?php print $ls_codalmdes?>" size="15" style="text-align:center " readonly>
                    <a href="javascript: ue_buscardestino();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                    <input name="txtnomfisdes" type="text" class="sin-borde" id="txtnomfisdes" value="<?php print $ls_nomalmdes?>" size="60" readonly>
                </div></td>
              </tr>
              <tr>
                <td height="16"><div align="right">Observaciones</div></td>
                <td colspan="3" rowspan="2"><div align="left">
                    <textarea name="txtobsasi" cols="50" rows="3" id="txtobsasi" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz&aacute;&eacute;&iacute;&oacute;&uacute;., ()@#!%/[]*-+_');"><?php print $ls_obsasi; ?></textarea>
                </div></td>
              </tr>
              <tr>
                <td height="20">&nbsp;</td>
              </tr>
              <tr>
                <td height="13">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
              <tr>
                <td height="13" colspan="4" align="center"><div id="articulos"></div></td>
              </tr>
              <tr>
                <td height="13" colspan="4" align="center">&nbsp;</td>
              </tr>
              <tr>
                <td height="13" colspan="4" align="center"><div align="center">
            <?php
					if($ls_operacion=="CALCULARCONTABLE")
						$in_grid->makegrid($li_totrowsc,$lo_titlecontable,$lo_objectc,$li_widthcontable,$ls_titlecontable,$ls_namecontable);
				?>
				</div></td>
              </tr>
              <tr>
                <td height="13">&nbsp;</td>
                <td colspan="3">&nbsp;</td>
              </tr>
            </table>
        <p>
          <input name="operacion"  type="hidden" id="operacion"  value="<?php print $ls_operacion;?>">
          <input name="existe"     type="hidden" id="existe"     value="<?php print $ls_existe;?>">
          <input name="totrowartsal"     type="hidden" id="totrowartsal"     value="<?php print $li_totrowartsal;?>">
          <input name="totrowartent"     type="hidden" id="totrowartent"     value="<?php print $li_totrowartent;?>">
          <input name="parametros"     type="hidden" id="parametros"     value="<?php print $ls_parametros;?>">
          <input name="estapr" type="hidden" id="estapr">
           			<input name="totalfilasc" type="hidden" id="totalfilasc" value="<?php print $li_totrowsc;?>">
       </p></td>
      </tr>
    </table>
</form>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

function ue_nuevo()
{
	f=document.formulario;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";		
		f.action="sigesp_siv_p_asignacion.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_guardar()
{
	f=document.formulario;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if(((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	{
		valido=true;
		estapro=f.estapr.value;
		// Obtenemos el total de filas de los Conceptos
		totrowartsal=ue_calcular_total_fila_local("txtcodart");
		totrowartent=2;
		f.totrowartsal.value=totrowartsal;
		f.totrowartent.value=totrowartent;
		codasi=ue_validarvacio(f.txtcodasi.value);
		fecasi=ue_validarvacio(f.txtfecasi.value);
		codalment=ue_validarvacio(f.txtcodalmdes.value);
		obsasi=ue_validarvacio(f.txtobsasi.value);
		totalgeneral=0;
		totalcuenta=0;
		totalcuentacargo=0;
		if(valido)
		{
			valido=ue_validarcampo(codasi,"El Codigo del asignacion no puede estar vacio.",f.txtcodasi);
		}
		if(valido)
		{
			valido=ue_validarcampo(fecasi,"La Fecha no puede estar vacia.",f.txtfecasi);
		}
		if(valido)
		{
			valido=ue_validarcampo(codalment,"El Codigo del Almacen no puede estar vacio.",f.txtcodalmdes);
		}
		if(valido)
		{
			valido=ue_validarcampo(obsasi,"La observacion del movimiento no puede estar vacia.",f.txtobsasi);
		}
		if(valido)
		{
			if(totrowartsal<=1)
			{
				alert("Debe Tener al menos un Articulo en el Movimiento.");
				valido=false;
			}
		}
		if(valido)
		{
			if(lb_existe=="TRUE")
			{
				alert("Este proceso no se puede modificar.");
			}
			else
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_siv_p_asignacion.php";
				f.submit();	
			}	
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operación.");
   	}
}

function ue_buscar()
{
	f=document.formulario;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_siv_cat_asignacion.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=630,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.formulario;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			estapro=f.estapr.value;
			if(estapro=="1")
			{
				alert("La solicitud esta aprobada no la puede eliminar.");
			}
			else
			{
				// Obtenemos el total de filas de los bienes
				codemppro= ue_validarvacio(f.txtcodemppro.value);
				if (codemppro!="")
				{
					if(confirm("¿Desea eliminar el Registro actual?"))
					{
						f.operacion.value="ELIMINAR";
						f.action="sigesp_siv_p_asignacion.php";
						f.submit();
					}
				}
				else
				{
					alert("Debe buscar el registro a eliminar.");
				}
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
function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_catalogo(ls_catalogo)
{
	// abre el catalogo que se paso por parametros
	window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_imprimir()
{
	f=document.formulario;
	li_imprimir=f.imprimir.value;
	lb_existe=f.existe.value;
	if(li_imprimir==1)
	{
		if(lb_existe=="TRUE")
		{
			codasi=f.txtcodasi.value;
			fecasi=f.txtfecasi.value;
			window.open("reportes/sigesp_siv_rfs_asignacion.php?codasi="+codasi+"&fecasi="+fecasi+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			alert("Debe existir un documento a imprimir");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_delete_articulo(fila)
{
	f=document.formulario;
		if(confirm("¿Desea eliminar el Registro actual?"))
		{
			valido=true;
			parametros="";
			//---------------------------------------------------------------------------------
			// Cargar los Cargos del opener y el seleccionado
			//---------------------------------------------------------------------------------
			totrowart=ue_calcular_total_fila_local("txtcodart");
			li_i=0;
			for(j=1;(j<totrowart)&&(valido);j++)
			{
				if(j!=fila)
				{
					li_i=li_i+1;
					codart=eval("document.formulario.txtcodart"+j+".value");
					denart=eval("document.formulario.txtdenart"+j+".value");
					coddetart=eval("document.formulario.txtcoddetart"+j+".value");
					parametros=parametros+"&txtcodart"+li_i+"="+codart+"&txtdenart"+li_i+"="+denart+""+
							   "&txtcoddetart"+li_i+"="+coddetart;

				
				}
			}
			li_i=li_i+1;
			totrowsal=eval(li_i);
			f.totrowartsal.value=totrowsal;
			parametros=parametros+"&totrowart="+li_i+"";
			
			if((parametros!="")&&(valido))
			{
				divgrid = document.getElementById("articulos");
				ajax=objetoAjax();
				ajax.open("POST","class_folder/sigesp_siv_c_asignacion_ajax.php",true);
				ajax.onreadystatechange=function() {
					if (ajax.readyState==4) {
						divgrid.innerHTML = ajax.responseText
					}
				}
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				ajax.send("proceso=LIMPIAR"+parametros);
			}
		}
}

function ue_reload()
{
	f=document.formulario;
	parametros=f.parametros.value;
	totrowartsal=ue_calcular_total_fila_local("txtcodart");
	totrowartent=ue_calcular_total_fila_local("txtcodartent");
	proceso="LIMPIAR";
	totrowartsal=totrowartsal+1;
	totrowartent=totrowartent+1;
	if(parametros!="")
	{
		divgrid = document.getElementById("articulos");
		ajax=objetoAjax();
		ajax.open("POST","class_folder/sigesp_siv_c_asignacion_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		ajax.send("proceso="+proceso+"&cargarcargos=0&totartsal="+totrowartsal+"&totartent="+totrowartent+parametros);
	}
}

function ue_cargarNuevo()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	f.totrowartent.value=1;
	f.totrowartsal.value=1;
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('articulos');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde están los métodos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_siv_c_asignacion_ajax.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divgrid.innerHTML = ajax.responseText
		}
	}
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("totartsal=1&totartent=1&proceso=LIMPIAR");
}

function ue_catalogoarticulos()
{
	f=document.formulario;
	codalm=f.txtcodalmdes.value;
	window.open("sigesp_siv_pdt_materiales.php?codalm="+codalm+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=850,height=400,left=50,top=50,location=no,resizable=yes");
}
/////////////////////////////////////////////////CATALOGOS////////////////////////////////////////////////////////
function ue_buscarorigen()
{
	tipo="origenii";
	window.open("sigesp_catdinamic_almacen.php?tipo="+tipo+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_buscardestino()
{
	f=document.formulario;
	totrowartsal=ue_calcular_total_fila_local("txtcodart");
	if(totrowartsal==1)
	{
		tipo="destinoii";
		window.open("sigesp_catdinamic_almacen.php?tipo="+tipo+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No se debe cambiar el almacen");
	}
}
function ue_buscarcausa()
{
	window.open("sigesp_siv_cat_causas.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}
function cargarCausas(codcau,dencau)
{
	f=document.formulario;
	f.txtcodcau.value=codcau;
	f.txtdencau.value=dencau;
}
function ue_catalogo_responsable_primario()
{
	f=document.formulario;
	window.open("sigesp_siv_cat_personal.php?destino=repasignadospri","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catalogo_responsable_uso()
{
	f=document.formulario;
	window.open("sigesp_siv_cat_personal.php?destino=repasignadosuso","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}


/////////////////////////////////////////////////CATALOGOS////////////////////////////////////////////////////////


</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<?php
if($ls_operacion=="NUEVO")
{
	print "<script language=JavaScript>";
	print "   ue_cargarNuevo();";
	print "</script>";
}
if(($ls_operacion=="GUARDAR")||($ls_operacion=="CALCULARCONTABLE")||(($ls_operacion=="ELIMINAR")&&(!$lb_valido)))
{
	print "<script language=JavaScript>";
	print "   ue_reload();";
	print "</script>";
}
?>		  
</html>
