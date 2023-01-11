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
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_d_regiones.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Definici&oacute;n de Incremento por Orden de Transporte</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript" src="js/funcion_scv.js"></script>
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
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
</head>
<body link="#006699" vlink="#006699" alink="#006699">
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" colspan="6" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="6" class="cd-menu">
			<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Control de Viaticos </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table></td>
  </tr>
  <tr>
    <td height="20" colspan="6" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="6" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td width="21" height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Guardar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar"></a></td>
    <td width="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></td>
    <td width="657" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
</table>
<?php 
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=   new sigesp_include(); 
$conn= $in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql= new class_sql($conn);
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion= new class_funciones();
require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
$io_funciondb= new class_funciones_db($conn);
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg= new class_mensajes();
require_once("../shared/class_folder/grid_param.php");
$io_grid= new grid_param();
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$io_ds=    new class_datastore();
$io_dsdoc= new class_datastore();
require_once("class_folder/sigesp_scv_c_incremento.php");
$io_region= new sigesp_scv_c_incremento($conn);
require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
$io_keygen= new sigesp_c_generar_consecutivo();
$lb_existe= "";
global $object;
$ls_operacion= $io_fun_viaticos->uf_obteneroperacion();
$ls_codinc=    $io_fun_viaticos->uf_obtenervalor("txtcodinc","");
$ls_deninc=    $io_fun_viaticos->uf_obtenervalor("txtdeninc","");
$ls_codreg=    $io_fun_viaticos->uf_obtenervalor("txtcodreg","");
$ls_denreg=    $io_fun_viaticos->uf_obtenervalor("txtdenreg","");
$ls_codmis=    $io_fun_viaticos->uf_obtenervalor("txtcodmis","");
$ls_denmis=    $io_fun_viaticos->uf_obtenervalor("txtdenmis","");
$li_lastrow=   $io_fun_viaticos->uf_obtenervalor("lastrow",0);
$total=        $io_fun_viaticos->uf_obtenervalor("hidtotrows","");
$ls_estatus=   $io_fun_viaticos->uf_obtenervalor("hidestatus","NUEVO");
$ls_continente=   $io_fun_viaticos->uf_obtenervalor("cmbcontinente","---");
$ls_existe=    $io_fun_viaticos->uf_obtenervalor("existe","FALSE");
$ls_codemp= $_SESSION["la_empresa"]["codemp"];
//Titulos de la tabla de Detalle Estados.
$title[1]="Region Destino"; 
$title[2]="Porcentaje"; 
$title[3]=""; 
$grid="grid";	
////////////Fin de la Tabla//////////////.
switch ($ls_operacion) 
{
	case "NUEVO":
		$lb_empresa = true;
		$ls_codinc= $io_keygen->uf_generar_numero_nuevo("SCV","scv_incremento","codinc","",4,"","","");
		$ls_deninc="";
		$ls_codmis="";
		$ls_denmis="";
		$ls_denreg="";
		$ls_estatus= "NUEVO";
		$ls_codreg="";
		$li_lastrow= 0;
		$li_i=1;
		$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=50>".
						  "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='' readonly>";
		$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=25>";
		$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		$li_totrows = 1;	
	break;
	case "PINTAR":
		$li_lastrow = $_POST["hidlastrow"];
		$li_totrows = $_POST["hidtotrows"];
		for ($li_i=1;$li_i<=$li_totrows;$li_i++)
		{
			if (array_key_exists("txtcodregdes".$li_i,$_POST))
			{
				$ls_codregdes = $_POST["txtcodregdes".$li_i];
				$ls_denregdes = $_POST["txtdenregdes".$li_i];
				$ls_porinc = $_POST["txtporinc".$li_i];
				$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value='".$ls_denregdes."'  class=sin-borde  readonly  style=text-align:center  size=50>".
					 			  "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='".$ls_codregdes."' readonly>";
				$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value='".$ls_porinc."'  class=sin-borde  onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=25>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
			}
			else
			{
				$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=50>".
					              "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='' readonly>";
				$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=25>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
			} 
		}
	break;
	case "GUARDAR":
		$li_total   = 0;
		$li_lastrow = $_POST["hidlastrow"];                                 
		empty($lr_grid);
		for ($li_i=1;$li_i<=$li_lastrow;$li_i++)
		{
			$lr_grid["codregdes"][$li_i]= $_POST["txtcodregdes".$li_i];                
			$lr_grid["porinc"][$li_i]= $_POST["txtporinc".$li_i];                
			$li_total++;
		}
		$lb_existe = $io_region->uf_load_incremento($ls_codinc);
		if ($lb_existe)
		{         
			if ($ls_estatus=="NUEVO")
			{ 
				$io_msg->message("El Código  ya existe");  
				$lb_valido=false;
			}
			elseif($ls_estatus=="GRABADO")
			{ 
				$lb_valido = $io_region->uf_update_incremento($ls_codinc,$ls_codreg,$ls_codmis,$ls_deninc,$lr_grid,$li_total,$la_seguridad);
				if ($lb_valido)
				{
					$io_msg->message("El Incremento ha sido actualizado");
					$ls_codinc="";
					$ls_deninc="";
					$ls_codmis="";
					$ls_denmis="";
					$ls_denreg="";
					$ls_estatus= "NUEVO";
					$ls_codreg="";
				}
				else
				{
					$io_msg->message("No se pudo actualizar el Incremento");
				}
			}	 
		} 
		else
		{  
			$lb_valido = $io_region->uf_insert_incremento($ls_codinc,$ls_codreg,$ls_codmis,$ls_deninc,$lr_grid,$li_total,$la_seguridad);
			if ($lb_valido)
			{
				$io_msg->message("El Incremento ha sido incluido");
				$ls_codinc="";
				$ls_deninc="";
				$ls_codmis="";
				$ls_denmis="";
				$ls_denreg="";
				$ls_estatus= "NUEVO";
				$ls_codreg="";
			}
			else
			{
				$io_msg->message("No se pudo incluir El Incremento");
			}
		} 
		$object[1][1]="<input type=text name=txtdenregdes1  id=txtdenregdes1  value=''  class=sin-borde  readonly  style=text-align:center  size=50>".
					  "<input name=txtcodregdes1    type=hidden id=txtcodregdes1 class=sin-borde size=20 maxlength=20 value='' readonly>";
		$object[1][2]="<input type=text name=txtporinc1  id=txtporinc1  value=''  class=sin-borde   onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left  size=25>";
		$object[1][3]="<a href=javascript:uf_delete(1);><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		$li_totrows= 1;
		$li_lastrow= 0;
	break;
	case "CARGAR":
		$lb_valido = $io_region->uf_load_dt_incremento($ls_codinc);
		if ($lb_valido)
		{
			$li_total=   $io_region->ds_dtregion->getRowCount('codinc');	
			$li_lastrow= $li_total;
			$li_totrows= intval($li_lastrow)+1; 
			for ($li_i=1;$li_i<=$li_total;$li_i++)	   	   
			{							                  
				$ls_codregdes = $io_region->ds_dtregion->getValue('codregdes',$li_i);
				$ls_denregdes = $io_region->ds_dtregion->getValue('denreg',$li_i);
				$ls_porinc = $io_region->ds_dtregion->getValue('porinc',$li_i);
				$ls_porinc=number_format($ls_porinc,2,',','.');
				$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value='".$ls_denregdes."'  class=sin-borde  readonly  style=text-align:center  size=50>".
					 			  "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='".$ls_codregdes."' readonly>";
				$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value='".$ls_porinc."'  class=sin-borde  onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=25>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
			}
				$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=50>".
					 			  "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='' readonly>";
				$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value=''  class=sin-borde  onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=25>";
				$object[$li_i][3]="";
		}
	break;
	case "DELETEROW":
		$li_lastrow= $_POST["hidlastrow"];
		$li_totrows= $_POST["hidtotrows"];
		$li_rowdel=  $_POST["filadel"];
		$li_temp= 0;
		for ($li_i=1;$li_i<=$li_totrows;$li_i++)
		{ 
			if ($li_i!=$li_rowdel)
			{  		
				$li_temp   = $li_temp+1;
				$ls_codregdes = $_POST["txtcodregdes".$li_i];
				$ls_denregdes = $_POST["txtdenregdes".$li_i];
				$ls_porinc = $_POST["txtporinc".$li_i];

				$object[$li_temp][1]="<input type=text name=txtdenregdes".$li_temp."  id=txtdenregdes".$li_temp."  value='".$ls_denregdes."'  class=sin-borde  readonly  style=text-align:center  size=50>".
					 			  "<input name=txtcodregdes".$li_temp."    type=hidden id=txtcodregdes".$li_temp." class=sin-borde size=20 maxlength=20 value='".$ls_codregdes."' readonly>";
				$object[$li_temp][2]="<input type=text name=txtporinc".$li_temp."  id=txtporinc".$li_temp."  value='".$ls_porinc."'  class=sin-borde  onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=25>";
				$object[$li_temp][3]="<a href=javascript:uf_delete(".$li_temp.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";

			}
			else
			{	
				$li_rowdelete=0;
			}
		}
		$li_totrows = intval($li_totrows)-1;
		$li_lastrow = intval($li_lastrow)-1;
	break;
	case "ELIMINAR":
		$lb_existe = $io_region->uf_load_incremento($ls_codinc);
		if ($lb_existe)
		{
			$lb_valido=$io_region->uf_delete_incremento($ls_codinc,$la_seguridad);
			if ($lb_valido)
			{ 
				$io_msg->message("El Incremento ha sido eliminado");
				$ls_codinc="";
				$ls_deninc="";
				$ls_codmis="";
				$ls_denmis="";
				$ls_denreg="";
				$ls_estatus= "NUEVO";
				$ls_codreg="";
			}
			else
			{
				$io_msg->message("No se pudo eliminar la Región");
				$ls_codinc="";
				$ls_deninc="";
				$ls_codmis="";
				$ls_denmis="";
				$ls_denreg="";
				$ls_estatus= "NUEVO";
				$ls_codreg="";
			}	
		}	   
		else
		{
			$io_msg->message("El Registro No Existe");
		}
		$total=0;
		$li_lastrow=0;          
		
		for ($li_i=1;$li_i<=1;$li_i++)
		{  			   
			$object[$li_i][1]="<input type=text name=txtdenregdes".$li_i."  id=txtdenregdes".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=50>".
							  "<input name=txtcodregdes".$li_i."    type=hidden id=txtcodregdes".$li_i." class=sin-borde size=20 maxlength=20 value='' readonly>";
			$object[$li_i][2]="<input type=text name=txtporinc".$li_i."  id=txtporinc".$li_i."  value=''  class=sin-borde  onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=25>";
			$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		}
		$li_totrows=1;	  	  
	break;
}
?>

<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_viaticos->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_viaticos);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<p>&nbsp;</p>
<table  border="0" cellspacing="0" cellpadding="0" class="formato-blanco" align="center">
      <tr class="titulo-celdanew"> 
        <td height="22" colspan="2" class="titulo-ventana">Definici&oacute;n de Incremento por Orden de Transporte</td>
      </tr>
      <tr>
        <td height="22" >&nbsp;</td>
        <td width="362" height="22" ><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_estatus ?>">
        <input name="hidpais" type="hidden" id="hidpais" value="<?php print $ls_continente ?>"></td>
      </tr>
      <tr> 
        <td width="103" height="22" align="right">C&oacute;digo </td>
        <td height="22" ><input name="txtcodinc" type="text" id="txtcodinc" value="<?php print $ls_codinc ?>" size="10" maxlength="4" style="text-align:center" onBlur="javascript: validar_pais();" onKeyPress="return keyRestrict(event,'1234567890');" readonly> 
        <input name="operacion" type="hidden" class="formato-blanco" id="operacion"  value="<?php print $ls_operacion?>"> </td>
      </tr>
      <tr> 
        <td height="22" align="right">Denominación </td>
        <td height="22"><p>
          <input name="txtdeninc" id="txtdeninc" value="<?php print $ls_deninc ?>" type="text" size="60" maxlength="254"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnñopqrstuvwxyz '+',.-()');">
        </p>        </td>
      </tr>
      <tr>
        <td height="22" align="right">Regi&oacute;n</td>
        <td height="22"><input name="txtcodreg" type="text" id="txtcodreg" value="<?php print $ls_codreg ?>" size="10" maxlength="5" style="text-align:center" onBlur="javascript: validar_pais();" onKeyPress="return keyRestrict(event,'1234567890');" readonly>
        <a href="javascript:ue_buscarregion();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt=""></a>
        <input name="txtdenreg" type="text" class="sin-borde" id="txtdenreg"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz '+',.-()');" value="<?php print $ls_denreg ?>" size="50" maxlength="254">        </td>
      </tr>
      
      <tr>
        <td height="22" colspan="2"><a href="javascript:ue_buscarregiondestino();"><img src="../shared/imagebank/tools20/nuevo.gif" width="20" height="20" border="0" alt="Agregar Estado">Agregar Destino </a>
          <input name="hidtotrows"  type="hidden"     id="hidtotrows"     value="<?php print $li_totrows;?>">
          <input name="hidlastrow"  type="hidden"     id="hidlastrow"    value="<?php print $li_lastrow;?>"> 
		  <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
        <input name="filadel"       type="hidden"     id="filadel"></td>
      </tr>
      <tr>
        <td height="22" colspan="2">
          <div align="center"></div>
          <div align="center">
          <?php 
			 $io_grid->makegrid($li_totrows,$title,$object,500,'Detalle de Regiones',$grid);
		  ?>
          </div>
          <div align="center"></div>
          <div align="center"></div>
        <div align="center"></div></td>
      </tr>
      <tr>
        <td height="22" colspan="2"><div align="right"></div> </td>
      </tr>
  </table>
</form>
</body>
<script >
function ue_buscarmision()
{
	ls_destino="INCREMENTO";
	window.open("sigesp_scv_cat_misiones.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarregion()
{
	ls_destino="SOLICITUDINC";
	window.open("sigesp_scv_cat_regiones_int.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}
function ue_buscarregiondestino()
{
	ls_destino="DETALLE";
	window.open("sigesp_scv_cat_regiones_int.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
}
function uf_cat_paises()
{
	f= document.form1;
	f.operacion.value= "";			
	lastrow= f.hidlastrow.value;           
	ls_codcont= f.cmbcontinente.value; 
	ls_destino="REGIONES";
	pagina= "sigesp_scv_cat_pais.php?codcont="+ls_codcont+"&destino="+ls_destino+"";
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=500,left=50,top=50,resizable=yes,location=no");
} 		

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if (li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_scv_d_incremento.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}


function ue_guardar()
{
var resul="";					   
f=document.form1;
li_incluir=f.incluir.value;
li_cambiar=f.cambiar.value;
lb_status=f.hidestatus.value;
if (((lb_status=="GRABADO")&&(li_cambiar==1))||(lb_status=="NUEVO")&&(li_incluir==1))
   {
     with (document.form1)
          {	
	        if (campo_requerido(txtdeninc,"El Campo denominacion debe estar lleno")==false)
	 	       {
		         txtdeninc.focus();
		       } 
 	        else
		       { 
		         if (campo_requerido(txtcodreg,"El Codigo de la Región debe estar lleno")==false)
			        {
			          txtcodreg.focus();
			        }
		         else
			        {
					   li_numdet = f.hidlastrow.value;//Obtenemos el Número de Detalles de una Región.
					   if (li_numdet>=1)
						  {
							f.operacion.value="GUARDAR";
							f.action="sigesp_scv_d_incremento.php";
							f.submit();
						  }
					   else
						  {
							alert("El Número de Regiones debe ser mayor o igual a 1(uno)");
						  }
                    }			
               }
	      }
    }
}
	
function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if (li_eliminar==1)
	{	
		if (f.txtcodinc.value=="")
		{
			alert("No ha seleccionado ningún registro para eliminar");
		}
		else
		{
			if (confirm("¿ Esta seguro de eliminar este registro ?"))
			{ 
				f=document.form1;
				f.operacion.value="ELIMINAR";
				f.action="sigesp_scv_d_incremento.php";
				f.submit();
			}
		}	   
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
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
		
function rellenar_cad(cadena,longitud)
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
	document.form1.txtcodreg.value=cadena;
}
		
function ue_buscar()
{
	f=document.form1;
	li_leer    = f.leer.value;
	if (li_leer==1)
	{
		f.operacion.value="";			
		pagina="sigesp_scv_cat_incremento.php";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,left=50,top=50,resizable=yes,location=no");
	}
	else
	{
		alert("No tiene permiso para realizar esta operación");
	}
}


function uf_delete(li_row)
{     
	var borrar="";
	f= document.form1;
	ls_codregdes= eval("f.txtcodregdes"+li_row+".value");
	if (ls_codregdes!="")
	{
		f.filadel.value= li_row;          
		f.operacion.value= "DELETEROW"
		f.action= "sigesp_scv_d_incremento.php";
		f.submit();
	}
	else
	{
		alert("No puede eliminar una fila en blanco !!!");
	}
}

function currencyFormat(fld, milSep, decSep, e) { 
    var sep = 0; 
    var key = ''; 
    var i = j = 0; 
    var len = len2 = 0; 
    var strCheck = '0123456789'; 
    var aux = aux2 = ''; 
    var whichCode = (window.Event) ? e.which : e.keyCode; 
    if (whichCode == 13) return true; // Enter 
	if (whichCode == 8)  return true; // Enter 
    key = String.fromCharCode(whichCode); // Get key value from key code 
    if (strCheck.indexOf(key) == -1) return false; // Not a valid key 
    len = fld.value.length; 
    for(i = 0; i < len; i++) 
     if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break; 
    aux = ''; 
    for(; i < len; i++) 
     if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i); 
    aux += key; 
    len = aux.length; 
    if (len == 0) fld.value = ''; 
    if (len == 1) fld.value = '0'+ decSep + '0' + aux; 
    if (len == 2) fld.value = '0'+ decSep + aux; 
    if (len > 2) { 
     aux2 = ''; 
     for (j = 0, i = len - 3; i >= 0; i--) { 
      if (j == 3) { 
       aux2 += milSep; 
       j = 0; 
      } 
      aux2 += aux.charAt(i); 
      j++; 
     } 
     fld.value = ''; 
     len2 = aux2.length; 
     for (i = len2 - 1; i >= 0; i--) 
      fld.value += aux2.charAt(i); 
     fld.value += decSep + aux.substr(len - 2, len); 
    } 
    return false; 
   }  
  
 function validar_pais()
 {
 	if (document.form1.txtcodreg.value=="")
	{
		alert("Debe seleccionar un país");
	}
 }
</script>
</html>