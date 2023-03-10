<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
require_once("class_funciones_activos.php");
$io_fun_activo=new class_funciones_activos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_p_incorporaciones.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
require_once("sigesp_saf_c_activo.php");
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$io_saf_tipcat= new sigesp_saf_c_activo();
$ls_rbtipocat=$io_saf_tipcat->uf_select_valor_config($ls_codemp);
$ls_reporte=$io_fun_activo->uf_select_config("SAF","REPORTE","FORMATO_INCORPORACION","sigesp_saf_rfs_incorporacion.php","C");

//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_obtenervalor($as_valor, $as_valordefecto)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_obtenervalor
	//	Access:    public
	//	Arguments:
    // 				as_valor         //  nombre de la variable que desamos obtener
    // 				as_valordefecto  //  contenido de la variable
    // Description: Funci?n que obtiene el valor de una variable si viene de un submit
	//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Funci?n que limpia todas las variables necesarias en la p?gina
		/////////////////////////////////////////////////////////////////////////////////
   		global $ls_cmpmov,$ls_codres,$ls_codresnew,$ls_nomres,$ls_nomresnew,$ls_descmp,$ld_feccmp, $ls_codcau,$ls_dencau;
   		global $ls_estpromov,$ls_status,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows,$ls_codrespri,$ls_numcmp;
		global $ls_denrespri,$ls_codresuso,$ls_denresuso,$ls_tiprespri,$ls_tipresuso,$ls_coduniadm,$ls_denuniadm,$ls_ubigeo;
        global $ls_fecent,$ls_codsed,$ls_densed,$ls_estatus;
		
		$ls_cmpmov=$ls_numcmp="";
		$ls_codres="";
		$ls_codresnew="";
		$ls_nomres="";
		$ls_nomresnew="";
		$ls_descmp="";
		$ls_codcau="";
		$ls_dencau="";
		$ls_estpromov="";
		$ld_feccmp= date("d/m/Y");
		$ls_status="";		
		$ls_titletable="Activos";
		$li_widthtable=750;
		$ls_nametable="grid";
		$lo_title[1]="Activo";
		$lo_title[2]="Serial";
		$lo_title[3]="Descripci?n del Movimiento";
		$lo_title[4]="Monto Activo";
		$lo_title[5]="";
		$li_totrows=1;
		$ls_codrespri="";	
		$ls_denrespri="";
		$ls_codresuso="";
		$ls_denresuso="";
		$ls_tiprespri=uf_obtenervalor("cmbtiprespri","-");	
		$ls_tipresuso=uf_obtenervalor("cmbtipresuso","-");
		$ls_coduniadm="";
		$ls_denuniadm="";
		$ls_ubigeo="";
		$ls_fecent="";
		$ls_codsed="";
		$ls_densed="";
		//$ls_estatus="";
   }
   
   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 23/03/2006 								Fecha ?ltima Modificaci?n : 23/03/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtdenact".$ai_totrows." type=text   id=txtdenact".$ai_totrows." class=sin-borde size=25 maxlength=150 readonly>".
								   "<input name=txtcodact".$ai_totrows." type=hidden id=txtcodact".$ai_totrows." class=sin-borde size=17 maxlength=15 readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtidact".$ai_totrows."  type=text   id=txtidact".$ai_totrows."  class=sin-borde size=17 maxlength=15 readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtdesmov".$ai_totrows." type=text   id=txtdesmov".$ai_totrows." class=sin-borde size=45 readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtmonact".$ai_totrows." type=text   id=txtmonact".$ai_totrows." class=sin-borde size=15 readonly  style=text-align:right>";
		$aa_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		return $aa_object;
   }

   function uf_pintardetalle($lo_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_pintardetalle
		//         Access: private
		//      Argumento: $aa_object // arreglo de objetos
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que se encarga de repintar el detalle existente en el grid.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/04/2006 								Fecha ?ltima Modificaci?n : 11/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		for($li_i=1;$li_i<$ai_totrows;$li_i++)
		{
			$ls_codact= $_POST["txtcodact".$li_i];
			$ls_denact= $_POST["txtdenact".$li_i];
			$ls_idact=  $_POST["txtidact".$li_i];
			$ls_desmov= $_POST["txtdesmov".$li_i];
			$li_monact= $_POST["txtmonact".$li_i];

			$lo_object[$li_i][1]="<input name=txtdenact".$li_i." type=text   id=txtdenact".$li_i." class=sin-borde size=25 maxlength=150 value='".$ls_denact."' readonly>".
								 "<input name=txtcodact".$li_i." type=hidden id=txtcodact".$li_i." class=sin-borde size=17 maxlength=15 value='".$ls_codact."' readonly>";
			$lo_object[$li_i][2]="<input name=txtidact".$li_i."  type=text   id=txtidact".$li_i."  class=sin-borde size=17 maxlength=15 value='".$ls_idact."'  readonly>";
			$lo_object[$li_i][3]="<input name=txtdesmov".$li_i." type=text   id=txtdesmov".$li_i." class=sin-borde size=52 value='".$ls_desmov."' readonly>";
			$lo_object[$li_i][4]="<input name=txtmonact".$li_i." type=text   id=txtmonact".$li_i." class=sin-borde size=15 value='".$li_monact."' readonly style=text-align:right>";
			$lo_object[$li_i][5]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		}
		$lo_object=uf_agregarlineablanca($lo_object,$ai_totrows);
		return $lo_object;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
<title >Incorporaciones</title>
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
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Activos Fijos</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
  <?php 
    if ($ls_rbtipocat == 1) 
    {
   ?>
   <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_csc.js"></script></td>
  <?php 
    }
	elseif ($ls_rbtipocat == 2)
	{
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_cgr.js"></script></td>
  <?php 
	}
	else
	{
   ?>
	<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  <?php 
	}
   ?>
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" title="Guardar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" title="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_imprimir('<?php print $ls_reporte ?>');"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></div></td>
    <td class="toolbar" width="24">&nbsp;</td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=  new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fundb= new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_fun= new class_funciones();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("sigesp_saf_c_movimiento.php");
	$io_saf= new sigesp_saf_c_movimiento();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid= new grid_param();
	require_once("sigesp_saf_c_activo.php");
	$io_saf_dta= new sigesp_saf_c_activo();
	
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$li_totrows = uf_obtenervalor("totalfilas",1);	
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
		$ls_estatus=$_POST["txtestatus"];
	}
	else
	{
		$ls_operacion="";
		$ls_estatus="";
		uf_limpiarvariables();
		$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		$ls_readonly="readonly";
	}

	switch ($ls_operacion) 
	{
		case "NUEVO":
			uf_limpiarvariables();
			$ls_readonly="";
			$ls_tiprespri="-";	
		    $ls_tipresuso="-";
			$ls_emp="";
			$ls_codemp="";
			$ls_tabla="saf_movimiento";
			$ls_columna="cmpmov";
			$ls_cmpmov = $io_fundb->uf_generar_codigo($ls_emp,$ls_codemp,$ls_tabla,$ls_columna);
			$ls_numcmp = $io_fundb->uf_generar_codigo_movimiento_saf("IN");//N?mero de Comprobante Independiente para cada tipo de movimiento.
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		break;
		
		case "AGREGARDETALLE":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$li_totrows = $li_totrows+1;
			$ls_cmpmov = $_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau = $_POST["txtcodcau"];
			$ls_dencau = $_POST["txtdencau"];
			$ld_feccmp = $_POST["txtfeccmp"];
			$ls_descmp = $_POST["txtdescmp"];
			$ls_status = $_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_denrespri = $_POST["txtdenrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_denresuso = $_POST["txtdenresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_fecent=$_POST["txtfecent"];
			$ls_codsed=$_POST["txtcodsed"];
			$ls_densed=$_POST["txtdensed"];
			for($li_i=1;$li_i<$li_totrows;$li_i++)
			{
				$ls_codact= $_POST["txtcodact".$li_i];
				$ls_denact= $_POST["txtdenact".$li_i];
				$ls_idact=  $_POST["txtidact".$li_i];
				$ls_desmov= $_POST["txtdesmov".$li_i];
				$li_monact= $_POST["txtmonact".$li_i];
				
				$lo_object[$li_i][1]="<input name=txtdenact".$li_i." type=text   id=txtdenact".$li_i." class=sin-borde size=25 maxlength=150 value='".$ls_denact."' readonly>".
									 "<input name=txtcodact".$li_i." type=hidden id=txtcodact".$li_i." class=sin-borde size=17 maxlength=15 value='".$ls_codact."' readonly>";
				$lo_object[$li_i][2]="<input name=txtidact".$li_i."  type=text id=txtidact".$li_i."    class=sin-borde size=17 maxlength=15 value='". $ls_idact ."' readonly>";
				$lo_object[$li_i][3]="<input name=txtdesmov".$li_i." type=text id=txtdesmov".$li_i."   class=sin-borde size=52 value='". $ls_desmov ."' readonly>";
				$lo_object[$li_i][4]="<input name=txtmonact".$li_i." type=text id=txtmonact".$li_i."   class=sin-borde size=15 value='". $li_monact ."' readonly style=text-align:right>";
				$lo_object[$li_i][5]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";

			}	
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);

		break;
		case "GUARDAR":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$ls_codusureg = $_SESSION["la_logusr"];
			$ls_cmpmov = $_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau = $_POST["txtcodcau"];
			$ls_dencau = $_POST["txtdencau"];
			$ld_feccmp = $_POST["txtfeccmp"];
			$ls_descmp = $_POST["txtdescmp"];
			$ls_status = $_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_tiprespri = $_POST["cmbtiprespri"];	
			$ls_tipresuso = $_POST["cmbtipresuso"];
			$ldt_fecent=$_POST["txtfecent"];
			$ls_fecent=$_POST["txtfecent"];
			$ls_codsed=$_POST["txtcodsed"];
			$ls_densed=$_POST["txtdensed"];
			$ld_date = date("Y-m-d");
			$lb_valido = $io_fec->uf_valida_fecha_mes($ls_codemp,$ld_date);
			if($lb_valido)
			{
				if(($ls_cmpmov!="")&&($ls_codcau!="")&&($li_totrows>1)&&(!empty($ls_numcmp)))
				{
					$ls_estpromov="0";
					$ls_codpro="----------";
					$ls_cedbene="----------";
					$ls_codtipdoc="";
					$ld_feccmpbd=$io_fun->uf_convertirdatetobd($ld_feccmp);
					$ldt_fecent=$io_fun->uf_convertirdatetobd($ldt_fecent);

					$lb_existe=$io_saf->uf_saf_select_movimiento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd);
					if($lb_existe)
					{
						$li_totrows=1;
						uf_limpiarvariables();
						$lo_object=uf_agregarlineablanca($lo_object,1);
						$io_msg->message("El numero de comprobante ya existe");
						$lb_valido=false;
					}
					else
					{
						$io_sql->begin_transaction();
						$lb_valido=$io_saf->uf_saf_insert_movimento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd,$ls_descmp,
						                                            $ls_codpro,$ls_cedbene,$ls_codtipdoc,$ls_codusureg,
																	$ls_estpromov,$la_seguridad,$ls_codrespri,$ls_codresuso,
																	$ls_coduniadm,$ls_ubigeo,$ls_tiprespri,$ls_tipresuso,
																	$ldt_fecent,"IN",$ls_numcmp,$ls_codsed);
						if($lb_valido)
						{
							for($li_i=1;$li_i<$li_totrows;$li_i++)
							{
								$ls_codact= $_POST["txtcodact".$li_i];
								$ls_denact= $_POST["txtdenact".$li_i];
								$ls_idact=  $_POST["txtidact".$li_i];
								$ls_desmov= $_POST["txtdesmov".$li_i];
								$li_monact= $_POST["txtmonact".$li_i];
								$li_monact= str_replace(".","",$li_monact);
								$li_monact= str_replace(",",".",$li_monact);
								$ls_estsoc=0;
								$ls_estmov="";
								
								$lb_valido=$io_saf->uf_saf_insert_dt_movimiento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd,$ls_codact,$ls_idact,$ls_desmov,$li_monact,$ls_estsoc,$ls_estmov,$ls_coduniadm,$la_seguridad);
								if($lb_valido)
								{
									$ls_estact="I";
									$lb_valido=$io_saf->uf_saf_update_dtaincorporacion2($ls_codemp,$ls_codact,$ls_idact,$ls_estact,$ld_feccmpbd,$ls_codsed,$la_seguridad);
									if ($lb_valido)
									{
									 $lb_valido=$io_saf_dta->uf_saf_update_res_uniadm_seriales($ls_codemp,$ls_codact,$ls_idact,$ls_coduniadm,
															   $ls_codrespri,$ls_codresuso,$la_seguridad);
									}						   
								}
								$lo_object[$li_i][1]="<input name=txtdenact".$li_i." type=text   id=txtdenact".$li_i." class=sin-borde size=25 maxlength=150 value='".$ls_denact."' readonly>".
													 "<input name=txtcodact".$li_i." type=hidden id=txtcodact".$li_i." class=sin-borde size=17 maxlength=15 value='".$ls_codact."' readonly>";
								$lo_object[$li_i][2]="<input name=txtidact".$li_i."  type=text   id=txtidact".$li_i."  class=sin-borde size=17 maxlength=15 value='". $ls_idact ."' readonly>";
								$lo_object[$li_i][3]="<input name=txtdesmov".$li_i." type=text   id=txtdesmov".$li_i." class=sin-borde size=52 value='". $ls_desmov ."' readonly>";
								$lo_object[$li_i][4]="<input name=txtmonact".$li_i." type=text   id=txtmonact".$li_i." class=sin-borde size=15 value='". $li_monact ."' readonly style=text-align:right>";
								$lo_object[$li_i][5]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
								
							}				
						}
						if($lb_valido)
						{
							$io_sql->commit();
							$io_msg->message("El registro fue incluido con exito");
							$ls_estpromov=0;
							$lo_object=uf_pintardetalle($lo_object,$li_totrows);
							$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
							$ls_status="C";
							//$li_totrows=1;
						}
						else
						{
							$io_sql->rollback();
							$io_msg->message("No se pudo incluir el registro");
							$lo_object=uf_pintardetalle($lo_object,$li_totrows);
						}
					}
				}
				else
				{
					if($li_totrows<=1)
					{
						$io_msg->message("El registro debe tener al menos 1 detalle");
						$lo_object=uf_agregarlineablanca($lo_object,1);
					}
					else
					{
						$io_msg->message("Debe completar los datos");
						$lo_object=uf_pintardetalle($lo_object,$li_totrows);
					}
				}			
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
		break;

		case "ELIMINARDETALLE":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$ls_cmpmov = $_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_dencau=$_POST["txtdencau"];
			$ld_feccmp=$_POST["txtfeccmp"];
			$ls_descmp=$_POST["txtdescmp"];
			$ls_status=$_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_denrespri = $_POST["txtdenrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_denresuso = $_POST["txtdenresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_tiprespri = $_POST["cmbtiprespri"];	
			$ls_tipresuso = $_POST["cmbtipresuso"];
			$ldt_fecent=$_POST["txtfecent"];
			$ls_codsed=$_POST["txtcodsed"];
			$ls_densed=$_POST["txtdensed"];
			$li_totrows=$li_totrows-1;
			$li_rowdelete=$_POST["filadelete"];
			$li_temp=0;
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{		
					$li_temp=$li_temp+1;			
					$ls_codact= $_POST["txtcodact".$li_i];
					$ls_denact= $_POST["txtdenact".$li_i];
					$ls_idact=  $_POST["txtidact".$li_i];
					$ls_desmov= $_POST["txtdesmov".$li_i];
					$li_monact= $_POST["txtmonact".$li_i];
					
					$lo_object[$li_temp][1]="<input name=txtdenact".$li_temp." type=text   id=txtdenact".$li_temp." class=sin-borde size=25 maxlength=150 value='".$ls_denact."' readonly>".
										 	"<input name=txtcodact".$li_temp." type=hidden id=txtcodact".$li_temp." class=sin-borde size=17 maxlength=15 value='".$ls_codact."' readonly>";
					$lo_object[$li_temp][2]="<input name=txtidact".$li_temp."  type=text   id=txtidact".$li_temp."  class=sin-borde size=17 maxlength=15 value='". $ls_idact ."' readonly>";
					$lo_object[$li_temp][3]="<input name=txtdesmov".$li_temp." type=text   id=txtdesmov".$li_temp." class=sin-borde size=52 value='". $ls_desmov ."' readonly>";
					$lo_object[$li_temp][4]="<input name=txtmonact".$li_temp." type=text   id=txtmonact".$li_temp." class=sin-borde size=15 value='". $li_monact ."' readonly style=text-align:right>";
					$lo_object[$li_temp][5]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
				}
				else
				{
					$li_rowdelete= 0;
				}
			}
			if ($li_temp==0)
			{
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
			else
			{				
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
		break;
	
		case "BUSCARDETALLE":
			uf_limpiarvariables();
			$ls_cmpmov = $_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau = $_POST["txtcodcau"];
			$ls_dencau = $_POST["txtdencau"];
			$ld_feccmp = $_POST["txtfeccmp"];
			$ls_descmp = $_POST["txtdescmp"];
			$ls_estpromov = $_POST["hidestpromov"];
			$ls_status = $_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_denrespri = $_POST["txtdenrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_denresuso = $_POST["txtdenresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_fecent=$_POST["txtfecent"];
			$ls_codsed=$_POST["txtcodsed"];
			$ls_densed=$_POST["txtdensed"];
			$ld_feccmpbd=$io_fun->uf_convertirdatetobd($ld_feccmp);
			$li_montot="";

			$arrResultado=$io_saf->uf_siv_load_dt_movimiento($ls_codemp,$ls_cmpmov,$ld_feccmpbd,$li_totrows,$lo_object,$li_montot);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$li_montot=$arrResultado['ai_montot'];
			$lb_valido=$arrResultado['lb_valido'];
		break;
	}
?>

<p>&nbsp;</p>
<div align="center">
  <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
    <table width="783" height="159" border="0" class="formato-blanco">
      <tr>
        <td width="775" ><div align="left">
            <table width="735" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr>
                <td colspan="3" class="titulo-ventana">Incorporaciones</td>
              </tr>
              <tr class="formato-blanco">
                <td width="110" height="19">&nbsp;</td>
                <td width="459"><div align="right">Fecha</div></td>
                <td width="164"><input name="txtfeccmp" type="text" id="txtfeccmp" style="text-align:center " value="<?php print $ld_feccmp ?>" size="13" maxlength="10" onKeyPress="ue_separadores(this,'/',patron,true);" datepicker="true"></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20" style="text-align:right">Comprobante</td>
                <td height="20"><input name="txtnumcmp" type="text" id="txtnumcmp" style="text-align:center" value="<?php print $ls_numcmp ?>" size="20" maxlength="15" readonly>
                  <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_status ?>">
                  <input name="txtcmpmov" type="hidden"   id="txtcmpmov" value="<?php print $ls_cmpmov ?>" size="20" maxlength="15" onBlur="javascript: ue_rellenarcampo(this,'15')" style="text-align:center " readonly></td>
                <td height="20"><label>
                  <input name="txtestatus" type="text" class="sin-borde2" id="txtestatus" value="<?php print $ls_estatus; ?>">
                </label></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Causa de Movimiento</div></td>
                <td height="20" colspan="2"><input name="txtcodcau" type="text" id="txtcodcau" value="<?php print $ls_codcau ?>" size="10" style="text-align:center " readonly>
                  <a href="javascript: ue_catacausas();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtdencau" type="text" class="sin-borde" id="txtdencau" value="<?php print $ls_dencau ?>" size="50" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Responsable Primario</div></td>
                <td height="20" colspan="2"><select name="cmbtiprespri" id="cmbtiprespri" onChange="javascript: ue_catalogo_responsable_primario();">
                  <option value="-" selected>-- Seleccione Uno --</option>
                  <option value="P" <?php if($ls_tiprespri=="P"){ print "selected";} ?>>PERSONAL</option>
                  <option value="B" <?php if($ls_tiprespri=="B"){ print "selected";} ?>>BENEFICIARIO</option>
                                </select>
                <input name="txtcodrespri" type="text"  style="text-align:center" class="sin-borde" id="txtcodrespri" value="<?php print $ls_codrespri; ?>" size="15" maxlength="10" readonly>
                <input name="txtdenrespri" type="text"  style="text-align:left" class="sin-borde" id="txtdenrespri" value="<?php print $ls_denrespri ?>" size="45" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Responsable de Uso </div></td>
                <td height="20" colspan="2"><select name="cmbtipresuso" id="cmbtipresuso" onChange="javascript: ue_catalogo_responsable_uso();">
                    <option value="-" selected>-- Seleccione Uno --</option>
                    <option value="P" <?php if($ls_tipresuso=="P"){ print "selected";} ?>>PERSONAL</option>
                    <option value="B" <?php if($ls_tipresuso=="B"){ print "selected";} ?>>BENEFICIARIO</option>
                  </select>
                <input name="txtcodresuso" type="text" style="text-align:center" class="sin-borde" id="txtcodresuso" value="<?php print $ls_codresuso; ?>" size="15" maxlength="10" readonly>
                <input name="txtdenresuso" type="text" style="text-align:left" class="sin-borde" id="txtdenresuso" value="<?php print $ls_denresuso; ?>" size="45" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Ubicacion Organizacional </div></td>
                <td height="26" colspan="2"><label>
                  <input name="txtcoduniadm" type="text" id="txtcoduniadm" value="<?php print $ls_coduniadm; ?>" size="10" maxlength="15" readonly>
                  <a href="javascript: ue_catalogo_unidad_administrativa();"><img src="../shared/imagebank/tools15/buscar.gif" alt=" " width="15" height="15" border="0"></a>
                  <input name="txtdenuniadm" type="text" class="sin-borde" id="txtdenuniadm" value="<?php print $ls_denuniadm; ?>" size="45" readonly>
                </label></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Sede</div></td>
                <td height="26" colspan="2"><input name="txtcodsed" type="text" id="txtcodsed" value="<?php print $ls_codsed; ?>" size="10" maxlength="15" readonly>
                  <a href="javascript: ue_catalogo_sede();"><img src="../shared/imagebank/tools15/buscar.gif" alt=" " width="15" height="15" border="0"></a>
                  <input name="txtdensed" type="text" class="sin-borde" id="txtdensed" value="<?php print $ls_densed; ?>" size="45" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Fecha  de la  Entrega </div></td>
                <td height="26" colspan="2"><div align="left">
                  <label></label>
                  <label>
                  <input name="txtfecent" type="text" id="txtfecent" onKeyPress="ue_separadores(this,'/',patron,true);" value="<?php print $ls_fecent; ?>" size="13" maxlength="10" datepicker="true">
                  </label>
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Ubicacion Geografica</div></td>
                <td height="26" colspan="2"><div align="left">
                  <textarea name="txtubigeo" cols="60" rows="2" id="txtubigeo"  onKeyUp="javascript: ue_validarcomillas(this)" onBlur="javascript: ue_validarcomillas(this)"><?php print $ls_ubigeo; ?></textarea>
                </div></td>
              </tr>
              
              <tr class="formato-blanco">
                <td height="28"><div align="right">Observaciones</div></td>
                <td rowspan="2"><textarea name="txtdescmp" cols="60" rows="2" id="txtdescmp"  onKeyUp="javascript: ue_validarcomillas(this)" onBlur="javascript: ue_validarcomillas(this)"><?php print $ls_descmp ?></textarea></td>
                <td><input name="hidestpromov" type="hidden" id="hidestpromov" value="<?php print $ls_estpromov ?>"></td>
              </tr>
              <tr class="formato-blanco">
                <td height="23"><div align="right"></div></td>
                <td>&nbsp;</td>
              </tr>
              <tr class="formato-blanco">
                <td height="28" colspan="3"><a href="javascript: ue_agregardetalle();"><img src="../shared/imagebank/tools/nuevo.gif" width="15" height="15" class="sin-borde">Agregar Activo</a></td>
              </tr>
              <tr class="formato-blanco">
                <td height="28" colspan="3"><div align="center">
                    <?php
		             $in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
	                 ?>
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="28"><div align="right"></div></td>
                <td colspan="2">&nbsp;</td>
              </tr>
            </table>
            <input name="operacion"  type="hidden" id="operacion">
            <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
            <input name="filadelete" type="hidden" id="filadelete">
</div></td>
      </tr>
    </table>
  </form>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones 
function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_saf_cat_incorporaciones.php?tipo=incorporacion","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_catalogo_responsable_primario()
{
	f=document.form1;
	tipresuso=f.cmbtiprespri.value;
	if(tipresuso=='P')
	{
		window.open("sigesp_saf_cat_personal.php?destino=repasignadospri","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    }
	else if(tipresuso=='B')
	{
		window.open("sigesp_saf_cat_beneficiario.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catalogo_responsable_uso()
{
	f=document.form1;
	tipresuso=f.cmbtipresuso.value;
	if(tipresuso=='P')
	{
		window.open("sigesp_saf_cat_personal.php?destino=repasignadosuso","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    }
	else if(tipresuso=='B')
	{
		window.open("sigesp_saf_cat_beneficiario.php?destino=responsableuso","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catalogo_unidad_administrativa()
{
	f=document.form1;
	window.open("sigesp_saf_cat_unidadejecutora.php?destino=activo","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    //f.txtubigeo.disabled=true;	
}

function ue_catalogo_sede()
{
	f=document.form1;
	window.open("sigesp_saf_cat_sede.php?coddestino=activo","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    //f.txtubigeo.disabled=true;	
}

function ue_agregardetalle()
{
	f=document.form1;
	ls_cmpmov=f.txtcmpmov.value;
	if(ls_cmpmov=="")
	{
		alert("Debe existir un numero de comprobante");
	}
	else
	{
		li_totrow=f.totalfilas.value;
		window.open("sigesp_saf_pdt_activo.php?totrow="+ li_totrow +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=260,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catacausas()
{
	tipo="I";
	window.open("sigesp_saf_cat_causasmovimiento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_saf_p_incorporaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_guardar()
{
	f=document.form1;
	ls_status=f.hidstatus.value;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	if(((ls_status=="C")&&(li_cambiar==1))||(ls_status=="")&&(li_incluir==1))
	{
		if(ls_status!="C")
		{
			codrespri=f.txtcodrespri.value;
			codresuso=f.txtcodresuso.value;
			codcau=f.txtcodcau.value;
			if((codrespri!="")&&(codresuso!="")&&(codcau!=""))
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_saf_p_incorporaciones.php";
				f.submit();
			}
			else
			{
				alert("Debe completar los campos principales");
			}
		}
		else
		{alert("Este documento no debe ser modificado");}
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

/*function ue_procesar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		f.operacion.value="PROCESAR";
		f.action="sigesp_saf_p_incorporaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_reversar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		f.operacion.value="REVERSAR";
		f.action="sigesp_saf_p_incorporaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}*/

function uf_delete_dt(li_row)
{
	f=document.form1;
	li_fila=f.totalfilas.value;
	if(li_fila!=li_row)
	{
		if(confirm("?Desea eliminar el Registro actual?"))
		{	
			f.filadelete.value=li_row;
			f.operacion.value="ELIMINARDETALLE"
			f.action="sigesp_saf_p_incorporaciones.php";
			f.submit();
		}
	}
}


function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(confirm("?Seguro desea eliminar el Registro?"))
		{
			f=document.form1;
			f.operacion.value="ELIMINAR";
			f.action="sigesp_saf_p_incorporaciones.php";
			f.submit();
		}
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_imprimir(ls_reporte)
{
	f = document.form1;
	ls_status = f.hidstatus.value;
	ls_cmpmov = f.txtcmpmov.value;
	li_imprimir = f.imprimir.value;
	if(ls_status=="C")
	{
		if (li_imprimir==1)
		{
			window.open("reportes/"+ls_reporte+"?cmpmov="+ls_cmpmov,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}
	else
	{
		alert("Seleccione un documento a imprimir");
	}
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

//--------------------------------------------------------
//	Funci?n que coloca los separadores (/) de las fechas
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
function ue_validarcomillas(valor)
{
	val = valor.value;
	longitud = val.length;
	texto = "";
	textocompleto = "";
	for(r=0;r<=longitud;r++)
	{
		texto = valor.value.substring(r,r+1);
		if((texto != "'")&&(texto != '"'))
		{
			textocompleto += texto;
		}	
	}
	valor.value=textocompleto;
}
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>