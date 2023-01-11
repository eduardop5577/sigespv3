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
require_once("class_funciones_inventario.php");
$io_fun_activo=new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = array();
$la_permisos = array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SIV","sigesp_siv_p_cerrarsep.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_agregarlineablanca
	//	Access:    public
	//	Arguments:
	//  			  aa_object // arreglo de titulos 
	//  			  ai_totrows // ultima fila pintada en el grid
	//	Description:  Funcion que agrega una linea en blanco al final del grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$aa_object[$ai_totrows][1]="<input  name=txtnumsol".$ai_totrows." type=text id=txtnumsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='' readonly>";
		$aa_object[$ai_totrows][2]="<input  name=txtfecsol".$ai_totrows." type=text id=txtfecsol".$ai_totrows." class=sin-borde size=15 maxlength=20 value='' readonly>";
		$aa_object[$ai_totrows][3]="<input  name=txtconsol".$ai_totrows." type=text id=txtconsol".$ai_totrows." class=sin-borde size=60 maxlength=100 value='' readonly>";
		$aa_object[$ai_totrows][4]="<input  name=chkprocesar".$ai_totrows."   type='checkbox' class= sin-borde value=1>";
		return $aa_object;
   }

   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_numordcom,$ls_codpro,$ls_denpro,$ls_codalm,$ls_nomfisalm,$ld_fecdes,$ld_fechas;
		global $selected0,$selected1,$ls_codusu,$ls_readonly,$ls_accion;
		
		$ls_numordcom="";
		$ls_codpro="";
		$ls_denpro="";
		$ls_codalm="";
		$ls_nomfisalm="";
		$ld_fechas=date("d/m/Y");
		$ls_mes=date("m");
		$ls_annio=date("Y");
		$ld_fecdes="01/".$ls_mes."/".$ls_annio;
		$ls_obsrec="";
		$selected0="selected";
		$selected1="";
		$ls_codusu=$_SESSION["la_logusr"];
		$ls_readonly="true";
		$ls_accion=0;
   }
   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Cierre de Solicitudes de Ejecuci&oacute;n Presupuestaria</title>
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
<link href="css/siv.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script >
<!--

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
//-->
</script>
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu">
	<table width="776" border="0" align="center" cellpadding="0" cellspacing="0">
	
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
    <td height="13" colspan="11" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" class="sin-borde"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" width="20" height="20"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="24"><div align="center"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=      new sigesp_include();
	$con=     $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=  new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg=  new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fun=  new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_func= new class_funciones();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid= new grid_param();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("sigesp_siv_c_cerrarsep.php");
	$io_siv=  new sigesp_siv_c_cerrarsep();
	require_once("class_funciones_inventario.php");
	$io_inventario=  new class_funciones_inventario();

	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_codusu=$_SESSION["la_logusr"];
	$li_totrows = $io_inventario->uf_obtenervalor("totalfilas",1);
	$ls_titletable="Entradas Actuales";
	$li_widthtable=760;
	$ls_nametable="grid";
	$lo_title[1]="Solicitud";
	$lo_title[2]="Fecha";
	$lo_title[3]="Concepto";
	$lo_title[4]="";
	
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	//	$ls_status=$_POST["hidestatus"];
	}
	else
	{
		$ls_operacion="";
		$ls_status="";
		uf_limpiarvariables();
		$lo_object = uf_agregarlineablanca($lo_object,1);
	}
	switch ($ls_operacion) 
	{
		case "PROCESAR":
			$li_temp=0;
			$li_s=0;
			$ld_fecmov= date("Y-m-d");
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_fecmov);
			$ls_accion=$io_inventario->uf_obtenervalor("cmbaccion",1);
			$ld_fecdes=$io_inventario->uf_obtenervalor("txtfecdes",0);
			$ld_fechas=$io_inventario->uf_obtenervalor("txtfechas",0);
			$selected0="selected";
			if($lb_valido)
			{
				$io_sql->begin_transaction();
				for($li_i=1;$li_i<=$li_totrows;$li_i++)
				{
					$ls_numsol= $_POST["txtnumsol".$li_i];
					$ld_fecsol= $_POST["txtfecsol".$li_i];
					$ls_consol= $_POST["txtconsol".$li_i];
					if (array_key_exists("chkprocesar".$li_i,$_POST))
					{
						$li_s=$li_s + 1;
						$li_check= $_POST["chkprocesar".$li_i];
						if ($li_check==1)
						{
							$lb_valido=$io_siv->uf_siv_update_status($ls_codemp,$ls_numsol,$la_seguridad);
							if($lb_valido)
							{
							}
						}
					}
					else
					{
						$li_temp=$li_temp + 1;
		
						$lo_object[$li_temp][1]="<input  name=txtnumsol".$li_temp." type=text id=txtnumsol".$li_temp." class=sin-borde size=15 maxlength=20 value='".$ls_numsol."' readonly>";
						$lo_object[$li_temp][2]="<input  name=txtfecsol".$li_temp." type=text id=txtfecsol".$li_temp." class=sin-borde size=15 maxlength=20 value='".$ld_fecsol."' readonly>";
						$lo_object[$li_temp][3]="<input  name=txtconsol".$li_temp." type=text id=txtconsol".$li_temp." class=sin-borde size=60 maxlength=500 value='".$ls_consol."' readonly>";
						$lo_object[$li_temp][4]="<input  name=chkprocesar".$li_temp."   type='checkbox' class= sin-borde value=1>";
					}
				}
				if(($li_i<=1)||($li_s==0))
				{
					$io_msg->message("No se pudo realizar el proceso");
					//$li_totrows=1;
					//$lo_object = uf_agregarlineablanca($lo_object,1);
					break;
				}
				if($lb_valido)
				{
					$io_sql->commit();
					$io_msg->message("El proceso se realizo con exito");
				}
				else
				{
					$io_sql->rollback();
					$io_msg->message("No se pudo realizar el proceso");
				}
	
				if ($li_temp)
				{
					$li_totrows=$li_temp;
				}
				else
				{
					$li_totrows=1;
					$lo_object = uf_agregarlineablanca($lo_object,1);
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object = uf_agregarlineablanca($lo_object,1);
			}
		break;

		case "BUSCARORDEN":
			$li_totrows=0;
			$lo_object = array();
			$ls_accion=$io_inventario->uf_obtenervalor("cmbaccion",0);
			//$ls_accion=$io_inventario->uf_obtenervalor("radioaccion",1);
			$ld_fecdes=$io_inventario->uf_obtenervalor("txtfecdes",0);
			$ld_fechas=$io_inventario->uf_obtenervalor("txtfechas",0);
			if($ls_accion==0)
			{
				$selected1="";
				$selected0="selected";
			}
			else
			{
				$selected1="selected";
				$selected0="";
			}
			$arrResultado=$io_siv->uf_siv_load_solicitudes($li_totrows,$lo_object,$ls_accion,$ld_fecdes,$ld_fechas);
			$li_totrows = $arrResultado['ai_totrows'];
			$lo_object = $arrResultado['ao_object'];
			$lb_valido = $arrResultado['lb_valido'];
			if (!$lb_valido)
			{
				//$lo_object="";
				$lo_object = uf_agregarlineablanca($lo_object,1);
			}
			break;
	}
?>

<p>&nbsp;</p>
<div align="center">
  <table width="649" height="209" border="0" class="formato-blanco">
    <tr>
      <td width="755" height="203"><div align="left">
          <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
            <table width="626" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td width="620">&nbsp;</td>
              </tr>
              <tr>
                <td><table width="615" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
                    <tr>
                      <td colspan="4" class="titulo-ventana">Cierre de Solicitudes de Ejecuci&oacute;n Presupuestaria </td>
                    </tr>
                    <tr class="formato-blanco">
                      <td height="25"><label></label>
<div align="right"></div></td>
                      <td height="25">&nbsp;</td>
                      <td width="231" height="25"><div align="right">Fecha </div></td>
                      <td width="93"><div align="left">
                        <input name="txtfecha" type="text" id="txtfecha" value="<?php print date("d/m/Y"); ?>" size="15"  style="text-align:center" readonly>
                      </div></td>
                    </tr>
                    <tr class="formato-blanco">
                      <td width="159" height="22"><div align="right">Acci&oacute;n</div></td>
                      <td colspan="3"><label>
                      <select name="cmbaccion" size="1">
                        <option value="0" selected>Cerrar Solicitud</option>
                      </select>
                      </label></td>
                    </tr>
                    <tr class="formato-blanco">
                    <td height="22"><div align="right">Desde</div></td>
                    <td width="130" height="22"><input name="txtfecdes" type="text" id="txtfecdes"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_fecdes?>" size="18"  datepicker="true" style="text-align:center "></td>
                    <td colspan="2"> Hasta
                      <input name="txtfechas" type="text" id="txtfechas"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_fechas?>" size="18"  datepicker="true" style="text-align:center "></td>
                    </tr>
                    <tr class="formato-blanco">
                      <td height="22" colspan="4"><div align="right"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" class="sin-borde">Buscar Solicitudes </a></div></td>
                    </tr>
                    <tr class="formato-blanco">
                      <td height="22" colspan="4"><p align="center">
                          <?php
					$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					?>
                      </p></td>
                    </tr>
                    <tr class="formato-blanco">
                      <td height="28" colspan="4"><div align="center">
                          <input name="operacion" type="hidden" id="operacion">
                          <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
                          <input name="filadelete" type="hidden" id="filadelete">
                          <input name="catafilas" type="hidden" id="catafilas" value="<?php print $li_catafilas;?>">
                          <input name="btnproceasr" type="button" class="boton" id="btnproceasr" onClick="javascript: ue_procesar();" value="Procesar">
</div></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td><div align="center"> </div></td>
              </tr>
            </table>
          </form>
      </div></td>
    </tr>
  </table>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones 
function ue_buscar()
{
	
	f=document.form1;
	li_leer=f.leer.value;
	if(li_leer==1)
	{
		f.operacion.value="BUSCARORDEN";
		f.action="sigesp_siv_p_cerrarsep.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_procesar()
{
	
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{
		f.operacion.value="PROCESAR";
		f.action="sigesp_siv_p_cerrarsep.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}
function ue_validar_oc(li_row)
{
	f=document.form1;
	ls_numordcom=eval("f.txtnumordcom"+li_row+".value");
	li_totrows=f.totalfilas.value;
	for(li_i=1;li_i<=li_totrows;li_i++)
	{
		ls_numordcomgrid=eval("f.txtnumordcom"+li_i+".value");
		if((ls_numordcom==ls_numordcomgrid)&&(li_i!=li_row))
		{
			if(eval("f.chkprocesar"+li_i+".checked")==1)
			{
				alert("No puede cerrar la misma O/C para mas de una unidad");
				obj=eval("f.chkprocesar"+li_row+"");
				obj.checked=0;
			}
		}
	}
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Función que coloca los separadores (/) de las fechas
//--------------------------------------------------------
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
function ue_separadores(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++){
			val2 += val[r]	
		}
		if(nums){
			for(z=0;z<val2.length;z++){
				if(isNaN(val2.charAt(z))){
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++){
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++){
			if(q ==0){
				val = val3[q]
			}
			else{
				if(val3[q] != ""){
					val += sep + val3[q]
					}
			}
		}
	d.value = val
	d.valant = val
	}
}
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);	
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>