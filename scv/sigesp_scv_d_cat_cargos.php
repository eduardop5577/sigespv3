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
<title>Definici&oacute;n de Categorias de Viaticos por Cargo</title>
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
require_once("class_folder/sigesp_scv_c_cat_cargos.php");
$io_region= new sigesp_scv_c_cat_cargos($conn);
require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
$io_keygen= new sigesp_c_generar_consecutivo();
$lb_existe= "";
global $object;
$ls_operacion= $io_fun_viaticos->uf_obteneroperacion();
$ls_codtar=    $io_fun_viaticos->uf_obtenervalor("txtcodtar","");
$ls_codcat=    $io_fun_viaticos->uf_obtenervalor("txtcodcat","");
$ls_dencat=    $io_fun_viaticos->uf_obtenervalor("txtdencat","");
$li_lastrow=   $io_fun_viaticos->uf_obtenervalor("lastrow",0);
$total=        $io_fun_viaticos->uf_obtenervalor("hidtotrows","");
$ls_estatus=   $io_fun_viaticos->uf_obtenervalor("hidestatus","NUEVO");
$ls_tipvia=   $io_fun_viaticos->uf_obtenervalor("cmbtipvia","0");
$ls_exterior=   $io_fun_viaticos->uf_obtenervalor("chkexterior","0");
$ls_codmon=   $io_fun_viaticos->uf_obtenervalor("txtcodmon","");
$ls_denmon=   $io_fun_viaticos->uf_obtenervalor("txtdenmon","");
if($ls_exterior=="1")
	$ls_checked="checked";
else
	$ls_checked="";
$ls_selec0="selected";
$ls_selec1="";
$ls_selec2="";
switch ($ls_tipvia)
{
	case "4":
		$ls_selec0="";
		$ls_selec1="selected";
		$ls_selec2="";
	break;
	case "5":
		$ls_selec0="";
		$ls_selec1="";
		$ls_selec2="selected";
	break;
}
$ls_existe=    $io_fun_viaticos->uf_obtenervalor("existe","FALSE");
$ls_codemp= $_SESSION["la_empresa"]["codemp"];
//Titulos de la tabla de Detalle Estados.
$title[1]="Código"; 
$title[2]="Denominacion del Cargo"; 
$title[3]=""; 
$grid="grid";	
////////////Fin de la Tabla//////////////.
switch ($ls_operacion) 
{
	case "NUEVO":
		$ls_codtar= $io_keygen->uf_generar_numero_nuevo("SCV","scv_catcargos","codcatcar","",4,"","","");
		$ls_dentar="";
		$ls_checked="";
		$ls_codmon="";
		$ls_denmon="";
		$ls_tipvia="0";
		$ls_codcat="";
		$ls_dencat="";
		$ls_estatus="NUEVO";
		$li_lastrow= 0;
		$li_i=1;
		$ls_selec0="selected";
		$ls_selec1="";
		$ls_selec2="";
		$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
						  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
		$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
		$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		$li_totrows = 1;	
	break;
	case "PINTAR":
		$li_lastrow = $_POST["hidlastrow"];
		$li_totrows = $_POST["hidtotrows"];
		for ($li_i=1;$li_i<=$li_totrows;$li_i++)
		{
			if (array_key_exists("txtcodcar".$li_i,$_POST))
			{
				$ls_codcar = $_POST["txtcodcar".$li_i];
				$ls_codnom = $_POST["txtcodnom".$li_i];
				$ls_descar = $_POST["txtdescar".$li_i];
				$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value='".$ls_codcar."'  class=sin-borde  readonly  style=text-align:center  size=15>".
								  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value='".$ls_codnom."'  class=sin-borde  readonly  style=text-align:center  size=15>";
				$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value='".$ls_descar."'  class=sin-borde  readonly  style=text-align:left    size=30>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
			}
			else
			{
				$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
								  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
				$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
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
			$ls_codcar= $_POST["txtcodcar".$li_i];                
			$ls_codnom= $_POST["txtcodnom".$li_i];                
			$ls_descar= $_POST["txtdescar".$li_i];
			if($ls_codnom=="")
			{
				$ls_codnom="0001";
			}
			$lr_grid["cargo"][$li_i]= $ls_codcar;                          
			$lr_grid["nomina"][$li_i]= $ls_codnom;                          
			$lr_grid["descar"][$li_i]= $ls_descar;                          
			$li_total++;
		}
		$lb_existe = $io_region->uf_load_tarifa($ls_codemp,$ls_codtar);
		if ($lb_existe=="TRUE")
		{           
			if ($ls_estatus=="NUEVO")
			{ 
				$io_msg->message("El Código de Tarifa ya existe");  
				$lb_valido=false;
			}
			elseif($ls_estatus=="GRABADO")
			{ 
				$lb_valido = $io_region->uf_update_tarifa($ls_codemp,$ls_codtar,$ls_codcat,$ls_tipvia,$lr_grid,$li_total,$ls_exterior,$ls_codmon,$la_seguridad);
				if ($lb_valido)
				{
					$io_msg->message("La Tarifa ha sido actualizada");
					$ls_codtar="";
					$ls_dentar="";
					$ls_checked="";
					$ls_codmon="";
					$ls_denmon="";
					$ls_tipvia="0";
					$ls_codcat="";
					$ls_dencat="";
					$ls_estatus="NUEVO";
					$li_i=1;
					$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
									  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
					$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
					$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
					$li_totrows= 1;
					$li_lastrow= 0;
				}
				else
				{
					$io_msg->message("No se pudo actualizar la Tarifa");
					$li_lastrow = $_POST["hidlastrow"];
					$li_totrows = $_POST["hidtotrows"];
					for ($li_i=1;$li_i<=$li_totrows;$li_i++)
					{

						if (array_key_exists("txtcodcar".$li_i,$_POST))
						{
							$ls_codcar = $_POST["txtcodcar".$li_i];
							$ls_codnom = $_POST["txtcodnom".$li_i];
							$ls_descar = $_POST["txtdescar".$li_i];
							$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value='".$ls_codcar."'  class=sin-borde  readonly  style=text-align:center  size=15>".
											  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value='".$ls_codnom."'  class=sin-borde  readonly  style=text-align:center  size=15>";
							$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value='".$ls_descar."'  class=sin-borde  readonly  style=text-align:left    size=30>";
							$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
						}
						else
						{
							$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
											  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
							$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
							$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
						} 
					}
				}
			}	 
		} 
		else
		{  
			$arrResultado = $io_region->uf_insert_tarifa($ls_codemp,$ls_codtar,$ls_codcat,$ls_tipvia,$lr_grid,$li_total,$ls_exterior,$ls_codmon,$la_seguridad);
			$ls_codtar=$arrResultado['as_codtar'];
			$lb_valido=$arrResultado['lb_valido'];
			if ($lb_valido)
			{
				$io_msg->message("La Tarifa ha sido incluida");
				$ls_codtar="";
				$ls_dentar="";
				$ls_checked="";
				$ls_codmon="";
				$ls_denmon="";
				$ls_tipvia="0";
				$ls_codcat="";
				$ls_dencat="";
				$ls_estatus="NUEVO";
				$ls_estatus= "NUEVO";
				$li_i=1;
				$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
								  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
				$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
				$li_totrows= 1;
				$li_lastrow= 0;
			}
			else
			{
				$io_msg->message("No se pudo incluir la Tarifa");
				$li_lastrow = $_POST["hidlastrow"];
				$li_totrows = $_POST["hidtotrows"];
				for ($li_i=1;$li_i<=$li_totrows;$li_i++)
				{
					if (array_key_exists("txtcodcar".$li_i,$_POST))
					{
						$ls_codcar = $_POST["txtcodcar".$li_i];
						$ls_codnom = $_POST["txtcodnom".$li_i];
						$ls_descar = $_POST["txtdescar".$li_i];
						$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value='".$ls_codcar."'  class=sin-borde  readonly  style=text-align:center  size=15>".
										  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value='".$ls_codnom."'  class=sin-borde  readonly  style=text-align:center  size=15>";
						$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value='".$ls_descar."'  class=sin-borde  readonly  style=text-align:left    size=30>";
						$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
					}
					else
					{
						$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
										  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
						$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
						$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
					} 
				}
			}
		} 
	break;
	case "CARGAR":
		$lb_valido = $io_region->uf_load_dt_tarifacargo($ls_codemp,$ls_codtar);
		if ($lb_valido)
		{
			$li_total=   $io_region->ds_dtregion->getRowCount('codcar');	
			$li_lastrow= $li_total;
			$li_totrows= intval($li_lastrow)+1; 
			for ($li_i=1;$li_i<=$li_total;$li_i++)	   	   
			{							                  
				$ls_codcar = $io_region->ds_dtregion->getValue('codcar',$li_i);
				$ls_codnom = $io_region->ds_dtregion->getValue('codnom',$li_i);
				$ls_descar = $io_region->ds_dtregion->getValue('descar',$li_i);
				$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value='".$ls_codcar."'  class=sin-borde  readonly  style=text-align:center  size=15>".
								  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value='".$ls_codnom."'  class=sin-borde  readonly  style=text-align:center  size=15>";
				$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value='".$ls_descar."'  class=sin-borde  readonly  style=text-align:left    size=30>";
				$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";

			}
			$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
							  "<input type=hidden name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
			$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:left    size=30>";
			$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
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
				$ls_codcar = $_POST["txtcodcar".$li_i];
				$ls_codnom = $_POST["txtcodnom".$li_i];
				$ls_descar = $_POST["txtdescar".$li_i];
				$object[$li_temp][1]="<input type=text name=txtcodcar".$li_temp."  id=txtcodcar".$li_temp."  value='".$ls_codcar."'  class=sin-borde  readonly  style=text-align:center  size=15>".
								 	 "<input type=hidden name=txtcodnom".$li_temp."  id=txtcodnom".$li_temp."  value='".$ls_codnom."'  class=sin-borde  readonly  style=text-align:center  size=15>";
				$object[$li_temp][2]="<input type=text name=txtdescar".$li_temp."  id=txtdescar".$li_temp."  value='".$ls_descar."'  class=sin-borde  readonly  style=text-align:left    size=30>";
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
			$lb_valido=$io_region->uf_delete_region($ls_codemp,$ls_codtar,$la_seguridad);
			if ($lb_valido)
			{ 
				$io_msg->message("La Tarifa ha sido eliminada");
				$ls_codtar="";
				$ls_dentar="";
				$ls_checked="";
				$ls_codmon="";
				$ls_denmon="";
				$ls_tipvia="0";
				$ls_codcat="";
				$ls_dencat="";
				$ls_estatus="NUEVO";
			}
			else
			{
				$io_msg->message("No se pudo eliminar la Tarifa");
				$ls_codtar="";
				$ls_dentar="";
				$ls_checked="";
				$ls_codmon="";
				$ls_denmon="";
				$ls_tipvia="0";
				$ls_codcat="";
				$ls_dencat="";
				$ls_estatus="NUEVO";
			}	
		$total=0;
		$li_lastrow=0;          
		
		for ($li_i=1;$li_i<=1;$li_i++)
		{  			   
			$object[$li_i][1]="<input type=text name=txtcodcar".$li_i."  id=txtcodcar".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>".
							  "<input type=text name=txtcodnom".$li_i."  id=txtcodnom".$li_i."  value=''  class=sin-borde  readonly  style=text-align:center  size=15>";
			$object[$li_i][2]="<input type=text name=txtdescar".$li_i."  id=txtdescar".$li_i."  value=''  class=sin-borde    onKeyPress=return(ue_formatonumero(this,'.',',',event));  style=text-align:left    size=30>";
			$object[$li_i][3]="<a href=javascript:uf_delete(".$li_i.");><img src=../shared/imagebank/tools20/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		}
		$li_totrows=1;	  	  
	break;
}
/*if($ls_estatus=="NUEVO")
{
	$ls_selec0="selected";
	$ls_selec1="";
	$ls_selec2="";
}*/
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
        <td height="22" colspan="2" class="titulo-ventana">Definici&oacute;n de Categoria de Viaticos por Cargo</td>
      </tr>
      <tr>
        <td height="22" >&nbsp;</td>
        <td width="334" height="22" ><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_estatus ?>"> </td>
      </tr>
      <tr> 
        <td width="103" height="22" align="right">C&oacute;digo </td>
        <td height="22" ><input name="txtcodtar" type="text" id="txtcodtar" value="<?php print $ls_codtar ?>" size="10" maxlength="5" style="text-align:center" onBlur="javascript: validar_pais();" onKeyPress="return keyRestrict(event,'1234567890');" readonly> 
        <input name="operacion" type="hidden" class="formato-blanco" id="operacion"  value="<?php print $ls_operacion?>"> </td>
      </tr>
      <tr> 
        <td height="22" align="right">Categoria </td>
        <td height="22"><p>
          <input name="txtcodcat" type="text" id="txtcodcat" style="text-align:center" value="<?php print $ls_codcat;  ?>" size="5" readonly>
          <a href="javascript:ue_catanomina();"></a><a href="javascript:ue_catacategorias();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
          <input name="txtdencat" type="text" class="sin-borde" id="txtdencat" value="<?php print $ls_dencat;  ?>" size="50" readonly>
        </p>        </td>
      </tr>
      <tr>
        <td height="22" align="right">Tipo de Viatico </td>
        <td height="22"><select name="cmbtipvia" id="cmbtipvia">
          <option value="0" <?php print $ls_selec0; ?>>--Ninguna--</option>
          <option value="4" <?php print $ls_selec1; ?>>Internacionales</option>
          <option value="5" <?php print $ls_selec2; ?>>Nacionales</option>
        </select></td>
      </tr>
      <tr>
        <td height="22" align="right">Aplicar Foraneos </td>
        <td height="22"><input name="chkexterior" type="checkbox" class="sin-borde" id="chkexterior" value="1" <?php print $ls_checked; ?>></td>
      </tr>
      <tr>
        <td height="22" align="right">Tipo de moneda </td>
        <td height="22"><input name="txtcodmon" type="text" id="txtcodmon" value="<?php print $ls_codmon; ?>" size="4" maxlength="3" readonly>
            <a href="javascript:uf_buscar_moneda();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Moneda"></a>
            <input name="txtdenmon" type="text" class="sin-borde" id="txtdenmon" value="<?php print $ls_denmon; ?>" size="30" readonly>
          </a></td>
      </tr>
      <tr>
        <td height="22" colspan="2"><a href="javascript:uf_cat_cargos();"><img src="../shared/imagebank/tools20/nuevo.gif" width="20" height="20" border="0" alt="Agregar Estado">Agregar Cargos </a>
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
			 $io_grid->makegrid($li_totrows,$title,$object,500,'Detalle de Cargos',$grid);
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
function ue_catacategorias()
{
	window.open("sigesp_scv_cat_categorias.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}
function uf_cat_cargos()
{
	f= document.form1;
	f.operacion.value= "";			
	ls_destino="CATEGORIA";		
	pagina= "sigesp_scv_cat_cargo.php?destino="+ls_destino+"";
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=500,left=50,top=50,resizable=yes,location=no");
} 		
function uf_buscar_moneda()
{
	f= document.form1;
	if(f.cmbtipvia.value=="4")
	{
		pagina= "sigesp_cat_moneda.php";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=500,left=50,top=50,resizable=yes,location=no");
	}
	else
	{
		alert("Opcion solo valida para viajes al exterior");
	}
} 		

function uf_verificar()
{
	f= document.form1;
	if(f.chkexterior.checked==false)
	{
		f.txtcodmon.value="";
		f.txtdenmon.value="";
	}
} 		

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if (li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_scv_d_cat_cargos.php";
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
if(lb_status=="C")
	lb_status="NUEVO";
valido=true;
if (((lb_status=="GRABADO")&&(li_cambiar==1))||(lb_status=="NUEVO")&&(li_incluir==1))
   {
     with (document.form1)
          {	
	        if (campo_requerido(txtcodtar,"El Código de la Tarifa debe estar lleno")==false)
	 	       {
		         txtcodtar.focus();
		       } 
 	        else
		       { 
		         if (campo_requerido(txtcodcat,"La categoria debe estar llena")==false)
			        {
			          txtcodcat.focus();
			        }
		         else
			        {
					   li_numdet = f.hidlastrow.value;//Obtenemos el Número de Detalles 
					   if ((li_numdet>=1)&& valido)
						  {
							f.operacion.value="GUARDAR";
							f.action="sigesp_scv_d_cat_cargos.php";
							f.submit();
						  }
					   else
						  {
							alert("El Número de Cargos asociados a una Tarifa debe ser mayor o igual a 1(uno)");
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
		if (f.txtcodtar.value=="")
		{
			alert("No ha seleccionado ningún registro para eliminar");
		}
		else
		{
			if (confirm("¿ Esta seguro de eliminar este registro ?"))
			{ 
				f=document.form1;
				f.operacion.value="ELIMINAR";
				f.action="sigesp_scv_d_cat_cargos.php";
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
		pagina="sigesp_scv_cat_catcargos.php";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=200,left=50,top=50,resizable=yes,location=no");
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
	ls_codcar= eval("f.txtcodcar"+li_row+".value");
	if (ls_codcar!="")
	{
		f.filadel.value= li_row;          
		f.operacion.value= "DELETEROW"
		f.action= "sigesp_scv_d_cat_cargos.php";
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