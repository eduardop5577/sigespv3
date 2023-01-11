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
$arrResultado=$io_fun_viaticos->uf_load_seguridad("SCV","sigesp_scv_p_calcularviaticos.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   //--------------------------------------------------------------
   function uf_limpiarvariables($as_titulo)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
		global $ls_codsolvia,$ld_fecsolvia,$ls_codmis,$ls_denmis,$ls_codrut,$ls_desrut,$ls_coduniadm,$ls_denuniadm;
		global $ls_ctaspg,$ls_denctaspg,$ld_fecsal,$ld_fecreg,$li_numdia,$ls_obssolvia,$ls_checked,$li_totsolviaaux;
		global $ls_codtipdoc,$ls_dentipdoc,$ls_codfuefin;
		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows;
		global $ls_titlepersonal,$lo_titlepersonal,$li_totrowspersonal;
		global $ls_titlepresupuesto,$lo_titlepresupuesto,$li_totrowspresupuesto;
		global $ls_titlecontable,$lo_titlecontable,$li_totrowscontable,$ls_numaut,$ld_fecaut;
		//Agregado por OFIMATICA DE VENEZUELA el 25-05-2011 para el control del check de reposicion de caja chica
		global $ls_checked_caj;
		$ls_checked_caj="";
		//Fin bloque OFIMATICA DE VENEZUELA
		$ls_codsolvia="";
		$ld_fecsolvia=date("d/m/Y");
		$ls_codmis="";
		$ls_denmis="";
		$ls_codrut="";
		$ls_desrut="";
		$ls_coduniadm="";
		$ls_denuniadm="";
		$ls_ctaspg="";
		$ls_denctaspg="";
		$ld_fecsal="";
		$ld_fecreg="";
		$ls_numaut= "";
		$ld_fecaut= "";
		$li_numdia="";
		$ls_obssolvia="";
		$ls_checked="";
		$li_totsolviaaux="0,00";
		$ls_codtipdoc="";
		$ls_dentipdoc="";
		$ls_codfuefin="";
		$ls_titletable="Asignaciones";
		$li_widthtable=700;
		$ls_nametable="grid";
		$lo_title[1]="Procedencia";
		$lo_title[2]="Código";
		$lo_title[3]="Concepto";
		$lo_title[4]="Cantidad";
		$li_totrows=1;
		$ls_titlepersonal="Personal";
		$lo_titlepersonal[1]="Código";
		$lo_titlepersonal[2]="Nombre";
		$lo_titlepersonal[3]="Cédula";
		$lo_titlepersonal[4]="Cargo";
		$lo_titlepersonal[5]="Categoría";
		$li_totrowspersonal=1;

		$ls_titlepresupuesto="Detalles Presupuestario";
		$lo_titlepresupuesto[1]=$as_titulo;
		$lo_titlepresupuesto[2]="Estatus";
		$lo_titlepresupuesto[3]="Cuenta";
		$lo_titlepresupuesto[4]="Monto";
		$li_totrowspresupuesto=1;

		$ls_titlecontable="Detalles Contables";
		$lo_titlecontable[1]="Ced. Beneficiario";
		$lo_titlecontable[2]="Cuenta";
		$lo_titlecontable[3]="Debe/Haber";
		$lo_titlecontable[4]="Monto";
		$li_totrowscontable=1;

   }
   


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
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
<title >C&aacute;lculo de Solicitud de Viaticos </title>
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
<script type="text/javascript" src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript" src="../shared/js/validaciones.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu">
		<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
            <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Control de Viaticos </td>
			  <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
	
	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="22" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Procesar" width="20" height="20" border="0" title="Procesar"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" width="20" height="20" border="0" title="Grabar"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" title="Ayuda"></div></td>
    <td class="toolbar" width="22"><div align="center"></div></td>
    <td class="toolbar" width="22"><div align="center"></div></td>
    <td class="toolbar" width="640">&nbsp;</td>
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
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_fun= new class_funciones();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fundb= new class_funciones_db($con);
	require_once("../shared/class_folder/grid_param.php");
	$in_grid= new grid_param();
	require_once("sigesp_scv_c_calcularviaticos.php");
	$io_scv= new sigesp_scv_c_calcularviaticos();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	$ls_codemp=    $_SESSION["la_empresa"]["codemp"];
	$ls_conrecdoc=    $_SESSION["la_empresa"]["conrecdoc"];
	$ls_modalidad= $_SESSION["la_empresa"]["estmodest"];
	switch($ls_modalidad)
	{
		case "1": // Modalidad por Proyecto
			$ls_titulo="Estructura Presupuestaria ";
			break;
			
		case "2": // Modalidad por Presupuesto
			$ls_titulo="Estructura Programática ";
			break;
	}
	$ls_operacion=$io_fun_viaticos->uf_obtenervalor("operacion","NUEVO");
	uf_limpiarvariables($ls_titulo);
/*	if(empty($ls_operacion))
	{
		$lo_object=$io_scv->uf_agregarlineablanca($lo_object,$li_totrows);
		$lo_objectpersonal=$io_scv->uf_agregarlineablancapersonal($lo_objectpersonal,$li_totrowspersonal);
		$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
		$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
	}
*/	$ls_codsolvia= $io_fun_viaticos->uf_obtenervalor("txtcodsolvia","");
	$ld_fecsolvia= $io_fun_viaticos->uf_obtenervalor("txtfecsolvia","");
	$ls_codmis=    $io_fun_viaticos->uf_obtenervalor("txtcodmis","");
	$ls_denmis=    $io_fun_viaticos->uf_obtenervalor("txtdenmis","");
	$ls_codrut=    $io_fun_viaticos->uf_obtenervalor("txtcodrut","");
	$ls_desrut=    $io_fun_viaticos->uf_obtenervalor("txtdesrut","");
	$ls_coduniadm= $io_fun_viaticos->uf_obtenervalor("txtcoduniadm","");
	$ls_denuniadm= $io_fun_viaticos->uf_obtenervalor("txtdenuniadm","");
	$ls_ctaspg=    $io_fun_viaticos->uf_obtenervalor("txtctaspg","");
	$ls_denctaspg= $io_fun_viaticos->uf_obtenervalor("txtdenctaspg","");
	$ls_obssolvia= $io_fun_viaticos->uf_obtenervalor("txtobssolvia","");
	$ld_fecsal=    $io_fun_viaticos->uf_obtenervalor("txtfecsal","");
	$ld_fecreg=    $io_fun_viaticos->uf_obtenervalor("txtfecreg","");
	$ls_codtipdoc= $io_fun_viaticos->uf_obtenervalor("txtcodtipdoc","");
	$ls_dentipdoc= $io_fun_viaticos->uf_obtenervalor("txtdentipdoc","");
	$ls_codfuefin= $io_fun_viaticos->uf_obtenervalor("txtcodfuefin","");
	$li_numdia=    $io_fun_viaticos->uf_obtenervalor("txtnumdia","");
	$ls_estatus=   $io_fun_viaticos->uf_obtenervalor("hidestatus","");
	$ls_estsolvia= $io_fun_viaticos->uf_obtenervalor("hidestsolvia","");
	$li_solviaext= $io_fun_viaticos->uf_obtenervalor("chksolviaext",0);
	$ls_tipvia= $io_fun_viaticos->uf_obtenervalor("cmbtipvia","-");
	$ls_numaut= $io_fun_viaticos->uf_obtenervalor("txtnumaut","");
	$ld_fecaut= $io_fun_viaticos->uf_obtenervalor("txtfecaut","");
	$ld_fecsalvia= $io_fun->uf_convertirdatetobd($ld_fecsal);
	$ld_fecregvia= $io_fun->uf_convertirdatetobd($ld_fecreg);
	$ld_fecsolaux= $io_fun->uf_convertirdatetobd($ld_fecsolvia);
	$li_numdiaaux= str_replace(".","",$li_numdia);
	$li_numdiaaux= str_replace(",",".",$li_numdiaaux);
	$li_totsolviadol= $io_fun_viaticos->uf_obtenervalor("txttotsolviadol",0);
	$li_tasacambio= $io_fun_viaticos->uf_obtenervalor("txttascam",0);
	$li_totsolviadol= str_replace(".","",$li_totsolviadol);
	$li_totsolviadol= str_replace(",",".",$li_totsolviadol);
	$li_tasacambio= str_replace(".","",$li_tasacambio);
	$li_tasacambio= str_replace(",",".",$li_tasacambio);
	$lb_cierre=$io_fun_viaticos->uf_select_cierre_presupuestario();
	$ls_tipodoc= $io_fun_viaticos->uf_obtenervalor("cmbtipdoc","1");
	switch($ls_tipodoc)
	{
		case "1":
			$ls_chktipdoc1="selected";
			$ls_chktipdoc2="";
			$ls_chktipdoc3="";
		break;
		case "0":
			$ls_chktipdoc1="";
			$ls_chktipdoc2="selected";
			$ls_chktipdoc3="";
		break;
		case "2":
			$ls_chktipdoc1="";
			$ls_chktipdoc2="";
			$ls_chktipdoc3="selected";
		break;
	}
	
	if($li_solviaext==1)
	{
		$ls_checked="checked";
	}
	//AGREGADO POR OFIMATICA DE VENEZUELA 25-05-2011 PARA EL MANEJO DE VIATICOS COMO REPOSICION DE GASTOS
	$li_repcajchi= $io_fun_viaticos->uf_obtenervalor("chkrepcajchi",0);
	if($li_repcajchi==1)
	{
		$ls_checked_caj="checked";
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	switch ($ls_operacion) 
	{
		case "NUEVO":
			uf_limpiarvariables($ls_titulo);
			$ls_estatus="";
			$lo_object=$io_scv->uf_agregarlineablanca($lo_object,1);
			$lo_objectpersonal=$io_scv->uf_agregarlineablancapersonal($lo_objectpersonal,$li_totrowspersonal);
			$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
			$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
		break;
		
		case "GUARDAR":
			$li_totrowscontable=    $io_fun_viaticos->uf_obtenervalor("totalfilascontable","");
			$li_totrowspresupuesto= $io_fun_viaticos->uf_obtenervalor("totalfilaspresupuesto","");
			$li_totrowspersonal=    $io_fun_viaticos->uf_obtenervalor("totalfilaspersonal","");
			$ls_codestpro1= $io_fun_viaticos->uf_obtenervalor("txtcodestpro1","");
			$ls_codestpro2= $io_fun_viaticos->uf_obtenervalor("txtcodestpro2","");
			$ls_codestpro3= $io_fun_viaticos->uf_obtenervalor("txtcodestpro3","");
			$ls_codestpro4= $io_fun_viaticos->uf_obtenervalor("txtcodestpro4","");
			$ls_codestpro5= $io_fun_viaticos->uf_obtenervalor("txtcodestpro5","");
			$ls_estcla= $io_fun_viaticos->uf_obtenervalor("hidestcla","");
			$li_totsolvia=  $io_fun_viaticos->uf_obtenervalor("txttotsolvia","");
			$li_totsolviaaux= str_replace(".","",$li_totsolvia);
			$li_totsolviaaux= str_replace(",",".",$li_totsolviaaux);
			$ls_codpro="----------";
			$ls_tipodestino="B";
			if($ls_obssolvia=="")
			{
				$ls_descripcion="Calculo de Viaticos de la solicitud ".$ls_codsolvia;
			}
			else
			{
				$ls_descripcion=$ls_obssolvia;
			}
			$lb_valido=$io_fun_viaticos->uf_select_cierre_presupuestario();
			if($lb_valido)
			{
				$ls_operacion="OC";
				$ls_codcom=$io_fun->uf_cerosizquierda($ls_codsolvia,11);
				$ls_codcom="SCV-".$ls_codcom;
				$li_totper=($li_totrowspersonal-1);
				$li_montotper=($li_totsolviaaux/$li_totper);
				$io_sql->begin_transaction();
				for($li_i=1;$li_i<$li_totrowspersonal;$li_i++)
				{
					$ls_cedper= $io_fun_viaticos->uf_obtenervalor("txtcedper".$li_i,"");
					$ls_codper= $io_fun_viaticos->uf_obtenervalor("txtcodper".$li_i,"");
					
					if($ls_tipodoc!="1")
					{
						for($li_j=1;$li_j<$li_totrowspresupuesto;$li_j++)
						{
							$ls_spgcuenta=$io_fun_viaticos->uf_obtenervalor("txtspgcuenta".$li_j,"");
							$ls_monpre=$io_fun_viaticos->uf_obtenervalor("txtmonpre".$li_j,"");
							$ls_monpre= str_replace(".","",$ls_monpre);
							$ls_monpre= str_replace(",",".",$ls_monpre);
							$ls_monpreper=($ls_monpre/$li_totper);
							$lb_valido=$io_scv->uf_scv_insert_dt_spg($ls_codemp,$ls_codsolvia,$ls_codcom,$ls_codestpro1,$ls_codestpro2,
																	 $ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,
																	 $ls_spgcuenta,$ls_operacion,$ls_codpro,$ls_cedper,$ls_tipodestino,
																	 $ls_descripcion,$ls_monpreper,$ls_codfuefin,$la_seguridad);
							if(!$lb_valido)
							{break;}
						}
					}
					if($lb_valido)
					{
						if($ls_tipodoc!="0")
						{
							for($li_k=1;$li_k<$li_totrowscontable;$li_k++)
							{
								$ls_sccuenta=$io_fun_viaticos->uf_obtenervalor("txtsccuenta".$li_k,"");
								$ls_columna=$io_fun_viaticos->uf_obtenervalor("txtdebhab".$li_k,"");
								$ls_moncon=$io_fun_viaticos->uf_obtenervalor("txtmoncon".$li_k,"");
								$ls_moncon= str_replace(".","",$ls_moncon);
								$ls_moncon= str_replace(",",".",$ls_moncon);
								$ls_monconper=$ls_moncon;
								if($ls_columna=="DEBE"){$ls_debhab="D";}
								else{$ls_debhab="H";}		
								$lb_valido=$io_scv->uf_scv_insert_dt_scg($ls_codemp,$ls_codsolvia,$ls_codcom,$ls_sccuenta,$ls_debhab,
																		 $ls_codpro,$ls_cedper,$ls_tipodestino,$ls_descripcion,
																		 $ls_monconper,$la_seguridad);			
							}
						}
					}
					if($lb_valido)
					{
						$lb_valido=$io_scv->uf_scv_update_montopersonal($ls_codemp,$ls_codsolvia,$ls_codper,$li_montotper,$la_seguridad);
					}
				}
				/*for($li_i=1;$li_i<$li_totrowspersonal;$li_i++)
				{
					if($lb_valido)
					{
						$ls_cedper= $io_fun_viaticos->uf_obtenervalor("txtcedper".$li_i,"");
						$lb_valido=$io_scv->uf_scv_procesar_recepcion_documento_viatico($ls_codsolvia,$ls_codcom,$ls_cedper,
																					  $ls_codtipdoc,$ls_descripcion,$ld_fecsolaux,
																						$li_montotper,$ls_codfuefin,$la_seguridad);
						if(!$lb_valido)
						{break;}
					}
				}
				if($lb_valido)
				{
					$lb_valido=$io_scv->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedper,$ls_codpro,$li_montotper);
					if($lb_valido)
					{
						$lb_valido=$io_scv->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedper,$ls_codpro,
																				 $li_montotper);
					}
				}*/
				if($lb_valido)
				{
					$lb_valido=$io_scv->uf_scv_update_solicitudviatico_dol($ls_codemp,$ls_codsolvia,$ls_codtipdoc,$li_totsolviaaux,$ls_tipodoc,$li_totsolviadol,$li_tasacambio,$la_seguridad);
				}
				if($lb_valido)
				{
					$io_sql->commit();
					$io_msg->message("La Solicitud de Viaticos fue Procesada");
				}
				else
				{
					$io_sql->rollback();
					$io_msg->message("No se pudo Procesar la Solicitud de Viaticos");
				}
			}
			uf_limpiarvariables($ls_titulo);
			$ls_estatus="";
			$lo_object=$io_scv->uf_agregarlineablanca($lo_object,$li_totrows);
			$lo_objectpersonal=$io_scv->uf_agregarlineablancapersonal($lo_objectpersonal,$li_totrowspersonal);
			$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
			$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
		break;
		case "BUSCARDETALLE":
			$li_totrows=0;
			$li_totrowspersonal=0;
			$arrResultado=$io_scv->uf_scv_load_dt_personal_int($ls_codemp,$ls_codsolvia,$li_totrowspersonal,$lo_objectpersonal);
			$li_totrowspersonal=$arrResultado['ai_totrows'];
			$lo_objectpersonal=$arrResultado['ao_objectpersonal'];
			$lb_valido=$arrResultado['lb_valido'];
			
			$li_totrows++;
			$li_totrowspersonal++;
			$lo_object=$io_scv->uf_agregarlineablanca($lo_object,$li_totrows);
			$lo_objectpersonal=$io_scv->uf_agregarlineablancapersonal($lo_objectpersonal,$li_totrowspersonal);
			$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
			$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
			$li_maxvia=$io_scv->uf_scv_load_maxinter($ls_codemp,"SCV","CONFIG","MAXINTER");
		break;
		
		case "PROCESAR":
			$lb_valido=false;
			$li_totrows=   $io_fun_viaticos->uf_obtenervalor("totalfilas","");
			$ls_codcatper= $io_fun_viaticos->uf_obtenervalor("hidcatper","");
			$li_maxefe= $io_fun_viaticos->uf_obtenervalor("txtmonto","");
			$li_maxefe=    str_replace(".","",$li_maxefe);
			$li_maxefe=    str_replace(",",".",$li_maxefe);
			$_SESSION["fechacomprobante"]=$ld_fecsolaux;
			$li_totrowspersonal= $io_fun_viaticos->uf_obtenervalor("totalfilaspersonal","");
			$arrResultado=$io_scv->uf_repintarpersonal_int($lo_objectpersonal,$li_totrowspersonal);
			$lo_objectpersonal=$arrResultado['aa_objectpersonal'];
			$li_totrowspersonal=$arrResultado['ai_totrowspersonal'];
			$li_totper=$li_totrowspersonal-1;
			$li_totsolvia=0;
			$li_totdisvia=0;
			$li_montoasiento=0;
			$li_totsolviadol=0;
			$ls_sccuentadis="";
			$ls_sccuenta="";
			$lb_valido=$io_fun_viaticos->uf_select_cierre_presupuestario();
			if($lb_valido)
			{
				$lb_valido=$io_fec->uf_valida_fecha_periodo($ld_fecsolvia,$ls_codemp);
				if($lb_valido)
				{
					if($ls_conrecdoc==1)
					{				
						$arrResultado= $io_scv->uf_scv_load_config($ls_codemp,"SCV","CONFIG","BENEFICIARIORD",$ls_scben);
						$ls_scben=$arrResultado['as_spgcuenta'];
						$lb_existe=$arrResultado['lb_valido'];
					}
					else
					{
						$arrResultado= $io_scv->uf_scv_load_config($ls_codemp,"SCV","CONFIG","BENEFICIARIO",$ls_scben);
						$ls_scben=$arrResultado['as_spgcuenta'];
						$lb_existe=$arrResultado['lb_valido'];
					}
					if($lb_existe)
					{
						$arrResultado=$io_scv->uf_scv_load_config($ls_codemp,"SCV","CONFIG","DOLARES",$ls_scpagodol,$ls_denpagodol);
						$ls_scpagodol=$arrResultado['as_spgcuenta'];
						$lb_existe=$arrResultado['lb_valido'];
					}

					if(!$lb_existe)
					{
						$io_msg->message("No se ha definido cuenta para el Beneficiario");
						$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
						$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
						$li_totsolviaaux="0,00";
						break;
					}
					for($li_i=1;$li_i<$li_totrowspersonal;$li_i++)
					{
						$ls_codper= $io_fun_viaticos->uf_obtenervalor("txtcodper".$li_i,"");
						$ls_cedper= $io_fun_viaticos->uf_obtenervalor("txtcedper".$li_i,"");
						$ls_codcar= $io_fun_viaticos->uf_obtenervalor("txtcodcar".$li_i,"");
						$ls_cargo= $io_fun_viaticos->uf_obtenervalor("txtcargo".$li_i,"");
						$ls_codnom= $io_fun_viaticos->uf_obtenervalor("txtcodnom".$li_i,"");
						$li_maxvia=$io_scv->uf_scv_load_maxinter($ls_codemp,"SCV","CONFIG","MAXINTER");
						if($li_maxvia=="")
						{
							$io_msg->message("No ha definido correctamente la tarifa en configuracion");
							return false;
						}
						if($ls_codcar!="")
						{
							$li_montar= $io_scv->uf_scv_select_tarifacargo($ls_codemp,$ls_cargo,$ls_codnom,$ls_tipvia);
							if($li_montar=="")
							{
								$io_msg->message("No ha definido correctamente la tarifa del cargo");
								return false;
							}
							if($li_montar>0)
								$li_tasacambio= $io_scv->uf_scv_select_tasacambio($ls_codemp,$ls_cargo,$ls_codnom,$ls_tipvia);
							$ls_denmisdes=$io_scv->uf_scv_select_mision_destino($ls_codemp,$ls_codsolvia);
							
							$lb_origen=$io_scv->uf_scv_select_origensolicitud($ls_codemp,$ls_codsolvia);
							if($lb_origen)
							{
								$li_montoasientodol=$li_montar;
								$li_montoasiento=($li_montoasientodol*$li_tasacambio);
								$li_totsolviadol=$li_totsolviadol+$li_montoasientodol;
								$li_totsolvia=$li_totsolvia+$li_montoasiento;
/*								$li_montoasiento=$li_maxvia;
								$li_totsolvia=$li_totsolvia+($li_maxvia+$li_montar);
								
*/								
								switch($ls_tipodoc)
								{
									case "1":
										$li_maxefe=$li_montoasientodol;
										$li_montrans=0;
										$li_monpresupuestario=($li_montrans*$li_tasacambio);
										$li_moncontable=($li_maxefe*$li_tasacambio);
									break;
									case "0":
										$li_maxefe=0;
										$li_montrans=$li_montoasientodol;
										$li_monpresupuestario=($li_montrans*$li_tasacambio);
										$li_moncontable=($li_maxefe*$li_tasacambio);
									break;
									case "2":
										$li_montrans=$li_montar-$li_maxefe;
										$li_monpresupuestario=($li_montrans*$li_tasacambio);
										$li_moncontable=($li_maxefe*$li_tasacambio);
									break;
								}
								if(($lb_valido)&&($li_totsolvia>0))
								{
									$arrResultado=$io_scv->uf_scv_procesar_asientos($ls_codemp,$ls_coduniadm,"SCV","CONFIG","INTERNACIONALES",$ls_spgcuenta,$ls_estpre,$ls_sccuenta,$li_disponible,$ls_codestpro1,
																				    $ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_codsolvia,$li_monpresupuestario);
									$ls_spgcuenta=$arrResultado['as_spgcuenta'];
									$ls_estpre=$arrResultado['as_estpre'];
									$ls_sccuenta=$arrResultado['as_sccuenta'];
									$li_disponible=$arrResultado['ai_disponible'];
									$ls_codestpro1=$arrResultado['as_codestpro1'];
									$ls_codestpro2=$arrResultado['as_codestpro2'];
									$ls_codestpro3=$arrResultado['as_codestpro3'];
									$ls_codestpro4=$arrResultado['as_codestpro4'];
									$ls_codestpro5=$arrResultado['as_codestpro5'];
									$ls_estcla=$arrResultado['as_estcla'];
									$lb_valido=$arrResultado['lb_valido'];
									if($lb_valido)
									{
										$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
										$ls_programatica="";
										$ls_programatica=$io_fun_viaticos->uf_formatoprogramatica($ls_codestpro,$ls_programatica);
										switch($ls_modalidad)
										{
											case "1": // Modalidad por Proyecto
												$ls_titulo="Estructura Presupuestaria ";
												break;
												
											case "2": // Modalidad por Presupuesto
												$ls_titulo="Estructura Programática ";
												break;
										}
											$arrResultado=$io_scv->uf_scv_load_presupuesto($ls_codestpro,$ls_spgcuenta,$li_monpresupuestario,$ls_estpre,$ls_estcla,$lo_objectpresupuesto,
																						   $li_totrowspresupuesto,$ls_programatica);
											$lo_objectpresupuesto=$arrResultado['aa_object'];
											$li_totrowspresupuesto=$arrResultado['ai_totrows'];
											$lb_valido=$arrResultado['lb_valido'];
									}
								}
								$arrResultado=$io_scv->uf_scv_load_contable_int($ls_cedper,$ls_scpagodol,$li_montohaber,$ls_scben,$li_moncontable,
																		        "",$li_totdisvia,$lo_objectcontable,$li_totrowscontable);
								$lo_objectcontable=$arrResultado['aa_object'];
								$li_totrowscontable=$arrResultado['ai_totrows'];
								$lb_valido=$arrResultado['lb_valido'];
							}
							else
							{
								$li_montoasientodol=$li_montar;
								$li_montoasiento=($li_montoasientodol*$li_tasacambio);
								$li_totsolviadol=$li_totsolviadol+$li_montoasientodol;
								$li_totsolvia=$li_totsolvia+$li_montoasiento;

								$li_maxefe=$li_montoasientodol;
								$li_montrans=0;
								$li_monpresupuestario=($li_montrans*$li_tasacambio);
								$li_moncontable=($li_maxefe*$li_tasacambio);
								$arrResultado=$io_scv->uf_scv_load_contable_int($ls_cedper,$ls_scpagodol,$li_montohaber,$ls_scben,$li_moncontable,
																		        "",$li_totdisvia,$lo_objectcontable,$li_totrowscontable);
								$lo_objectcontable=$arrResultado['aa_object'];
								$li_totrowscontable=$arrResultado['ai_totrows'];
								$lb_valido=$arrResultado['lb_valido'];
								$ls_chktipdoc1="selected";
								$ls_chktipdoc2="";
								$ls_chktipdoc3="";
								$ls_tipodoc="1";
							}

							
						}
						else
						{
							$lb_valido=false;
							$io_msg->message("El personal no tiene Cargo Asociado.");
							$lo_objectcontable=$io_scv->uf_agregarlineablancacontable_int($lo_objectcontable,$li_totrowscontable);
						}

					}
					$li_totsolviaaux= number_format($li_totsolvia,2,',','.');

				}
				else
				{
					$io_msg->message("El mes no esta abierto");
					$lo_objectpresupuesto=$io_scv->uf_agregarlineablancapresupuesto($lo_objectpresupuesto,$li_totrowspresupuesto);
					$lo_objectcontable=$io_scv->uf_agregarlineablancacontable($lo_objectcontable,$li_totrowscontable);
					$li_totsolviaaux="0,00";
				}
			}
		unset($_SESSION["fechacomprobante"]);
		break;
	}
	switch($ls_estsolvia)
	{
		case "R":
			$ls_estatussol="Registro";
		break;
		case "":
			$ls_estatussol="";
		break;
	}
?>
<p>&nbsp;</p>
<form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_viaticos->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_viaticos);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="22" colspan="7"><table width="400" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="70" class="sin-borde2"><div align="right">Estatus:</div></td>
        <td width="330"><input name="txtestsolvia" type="text" class="sin-borde2" id="txtestsolvia" value="<?php print $ls_estatussol;?>">
          <input name="hidestsolvia" type="hidden" id="hidestsolvia" value="<?php print $ls_estsolvia;?>"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="22" colspan="7" class="titulo-ventana">C&aacute;lculo de Solicitud de Viaticos </td>  
  </tr>
  <tr>
    <td height="22" colspan="2">&nbsp;</td>
    <td width="259"><input name="hidestatus" type="hidden" id="hidestatus" value="<?php print $ls_estatus;?>">
        <input name="operacion" type="hidden" id="operacion" value="<?php print $ls_operacion;  ?>">
        <input name="cierre" type="hidden" id="cierre" value="<?php print $lb_cierre; ?>"></td>
    <td width="124" colspan="2"><div align="right">Fecha</div></td>
    <td colspan="2"><input name="txtfecsolvia" type="text" id="txtfecsolvia" style="text-align:center"  value="<?php print $ld_fecsolvia;  ?>"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"size="17" readonly></td>  </tr>
  <tr>
    <td height="22" colspan="2"><div align="right">Tipo de Viatico </div></td>
    <td colspan="5"><select name="cmbtipvia" id="cmbtipvia" onChange="javascript: ue_tipoviatico();">
      <option value="0">--Ninguna--</option>
      <option value="1" selected>Viaticos de Instalacion</option>
      <option value="2">Gasto de Transporte</option>
      <option value="3">Permanencia</option>
      <option value="4" >Internacionales</option>
         <option value="5">Nacionales</option>
    </select></td>
  </tr>
  <tr>
    <td height="22" colspan="2"><div align="right">C&oacute;digo</div></td>
    <td colspan="5"><input name="txtcodsolvia" type="text" id="txtcodsolvia" style="text-align:center" value="<?php print $ls_codsolvia;?>" size="10"></td>  </tr>
  <tr>
    <td height="22" colspan="2"><div align="right">Documento a Generar </div></td>
    <td height="22" colspan="5"><select name="cmbtipdoc" id="cmbtipdoc">
        <option value="1"<?php print $ls_chktipdoc1; ?>>Efectivo - Recepcion de Documentos</option>
        <option value="0"<?php print $ls_chktipdoc2; ?>>Transferencia - Compromiso</option>
        <option value="2"<?php print $ls_chktipdoc3; ?>>Transferencia/Efectivo - Compromiso/R.D.</option>
          </select>    </td>
  </tr>
  <tr>
    <td colspan="2"><div align="right">Tipo de Documento </div></td>
    <td colspan="5"><input name="txtcodtipdoc" type="text" id="txtcodtipdoc" value="<?php print $ls_codtipdoc; ?>" size="10" readonly>
      <a href="javascript: ue_buscartipodocumento();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
      <input name="txtdentipdoc" type="text" class="sin-borde" id="txtdentipdoc" value="<?php print $ls_dentipdoc; ?>" size="60" readonly></td>
  </tr>
  <tr>
    <td width="23" height="22"><div align="left"><a href="javascript: ue_agregardetalleasignaciones();"></a> </div></td>
    <td width="164" height="22"><div align="right"><a href="javascript: ue_agregardetalleasignaciones();"></a>Monto a Procesar </div></td>
    <td height="22" colspan="5"><input name="txtmonto" type="text" class="formato-blanco" id="txtmonto" style="text-align:right" value="<?php print number_format($li_maxvia,2,',','.'); ?>" size="10" onKeyPress="return(ue_formatonumero(this,'.',',',event));"></td>  </tr>
  <tr>
   <tr>
    <td height="22" colspan="2"><div align="right">Numero de Autorizacion </div></td>
    <td><input name="txtnumaut" type="text" id="txtnumaut" value="<?php print $ls_numaut; ?>" size="15" maxlength="10"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmnñopqrstuvwxyz '+'.,-');"></td>
    <td><div align="right">Fecha de Autorizacion </div></td>
    <td colspan="2"><input name="txtfecaut" type="text" id="txtfecaut" style="text-align:center"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);"  value="<?php print $ld_fecaut;  ?>" size="17" datepicker="true"></td>
  </tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7"><div align="center">
 <?php
		if($ls_operacion=="PROCESAR")
		{
	?>     <table width="700" border="1" cellpadding="0" cellspacing="0">
        <tr class="titulo-celda">
          <td colspan="3">Asignaciones</td>
        </tr>
        <tr>
          <td width="193" height="22"><div align="right">Monto Segun Cargo</div></td>
          <td height="22" colspan="2"><div align="right">
              <input name="txtmontar" type="text" class="texto-azul" id="txtmontar" style="text-align:right" value="<?php print number_format($li_montar,2,',','.'); ?>" size="10" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Monto en Efectivo </div></td>
          <td height="22" colspan="2"><div align="right">
            <input name="txtdeninc2" type="text" class="sin-borde" id="txtdeninc2" value="<?php print $ls_denmisdes; ?>" size="55" readonly>
            -
            <input name="txtmontarcarfam" type="text" class="texto-azul" id="txtmontarcarfam" value="<?php print number_format($li_maxefe,2,',','.'); ?>" size="10" readonly style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Monto en Transferencia </div></td>
          <td height="22" colspan="2"><div align="right">
              <input name="txtdeninc" type="text" class="sin-borde" id="txtdeninc" value="<?php print $ls_denmisdes; ?>" size="55" readonly>
              - 
              <input name="txtmontarporinc" type="text" class="texto-azul" id="txtmontarporinc" value="<?php print number_format($li_montrans,2,',','.'); ?>" size="10" readonly style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tasa de Cambio </div></td>
          <td height="22" colspan="2"><div align="right">
              <input name="txttascam" type="text" class="texto-azul" id="txttascam" value="<?php print number_format($li_tasacambio,2,',','.') ; ?>" size="10" readonly style="text-align:right" >
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Total de la Solicitud de Viaticos </div></td>
          <td width="159" height="22"><div align="center">$
            <input name="txttotsolviadol" type="text" class="texto-azul" id="txttotsolviadol" value="<?php print number_format($li_totsolviadol,2,',','.'); ?>" size="10">
          </div></td>
          <td width="143"><div align="center">Bs.
            <input name="textfield2" type="text" class="texto-azul" value="<?php print number_format($li_totsolvia,2,',','.'); ?>" size="10">
          </div></td>
        </tr>
      </table>
	  	<?php
		}
	?>
</div></td>  </tr>
  <tr><td height="22"><a href="javascript: ue_agregardetallepersonal();"></a></td>
    <td height="22"><a href="javascript: ue_agregardetallepersonal();"></a></td>
    <td height="22" colspan="5"><input name="hidcatper" type="hidden" id="hidcatper"></td></tr><tr><td colspan="7"><div align="CENTER">
  <?php
		$in_grid->makegrid($li_totrowspersonal,$lo_titlepersonal,$lo_objectpersonal,$li_widthtable,$ls_titlepersonal,$ls_nametable);
	?>
  </div><div align="CENTER"></div></td></tr>
    <tr>
      <td colspan="7">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="7"  align="center"><?php
		$in_grid->makegrid($li_totrowspresupuesto,$lo_titlepresupuesto,$lo_objectpresupuesto,$li_widthtable,$ls_titlepresupuesto,$ls_nametable);
	?>      </td>
    </tr>
    <tr>
      <td colspan="7"  align="center">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="7"  align="center">
        <?php
		$in_grid->makegrid($li_totrowscontable,$lo_titlecontable,$lo_objectcontable,$li_widthtable,$ls_titlecontable,$ls_nametable);
	?>      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2"><div align="right">Monto de la Solicitud </div></td>
      <td width="139"><div align="right">
        <input name="txttotsolvia" type="text" id="txttotsolvia" value="<?php print $li_totsolviaaux; ?>" style="text-align:right" readonly>
      </div></td>
      <td width="35">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
  <tr>
    <td height="22" colspan="7"><div align="left">
      <strong>TVS</strong>: Tarifas de Viaticos <strong>TRP</strong>: Tarifa de Transporte <strong>TDS</strong>: Tarifa de Distancias <strong>TOA</strong>: Otras Asignaciones </td>
    </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
    <td colspan="5"><input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows; ?>">
      <input name="filadelete" type="hidden" id="filadelete">
      <input name="filadeleteper" type="hidden" id="filadeleteper">
      <input name="totalfilaspersonal" type="hidden" id="totalfilaspersonal" value="<?php print $li_totrowspersonal; ?>">
      <input name="totalfilaspresupuesto" type="hidden" id="totalfilaspresupuesto" value="<?php print $li_totrowspresupuesto; ?>">
      <input name="totalfilascontable" type="hidden" id="totalfilascontable" value="<?php print $li_totrowscontable; ?>">
	  <input name="txtcodestpro1" type="hidden" id="txtcodestpro1" value="<?php print $ls_codestpro1;?>">
	  <input name="txtcodestpro2" type="hidden" id="txtcodestpro2" value="<?php print $ls_codestpro2;?>">
	  <input name="txtcodestpro3" type="hidden" id="txtcodestpro3" value="<?php print $ls_codestpro3;?>">
	  <input name="txtcodestpro4" type="hidden" id="txtcodestpro4" value="<?php print $ls_codestpro4;?>">
	  <input name="txtcodestpro5" type="hidden" id="txtcodestpro5" value="<?php print $ls_codestpro5;?>">
	  <input name="hidestcla"     type="hidden" id="hidestcla"     value="<?php print $ls_estcla ?>"></td>
  </tr></table>
</form>
<p>&nbsp;</p>
<div align="center"></div>
<p align="center">&nbsp;</p>
</body>
<script >
function ue_tipoviatico()
{
	f=document.form1;
	tipoviatico=f.cmbtipvia.value;
	switch(tipoviatico)
	{
		case "1":
			location.href='sigesp_scv_p_calcularviaticos_int.php';
		break;	
		case "2":
			location.href='sigesp_scv_p_calcularviaticos_orden.php';
		break;	
		case "3":
			location.href='sigesp_scv_p_calcularviaticos_permanencia.php';
		break;	
		case "4":
			location.href='sigesp_scv_p_calcularviaticos_internacionales.php';
		break;	
		case "5":
			location.href='sigesp_scv_p_calcularviaticos_nacionales.php';
		break;	
	}
}

function ue_buscartipodocumento()
{
	f= document.form1;
	tipdoc=f.cmbtipdoc.value;
	if(tipdoc!=0)
	{
		ls_destino="CALCULOINT";
		window.open("sigesp_scv_cat_tipodocumentos.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Este calculo no genera R.D.");
	}
}
//--------------------------------------------------------
//  Funciones de las operaciones de la páginas
//--------------------------------------------------------
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	lb_cierre=f.cierre.value;
	ls_destino="CALCULOINT";
	if(li_incluir==1)
	{	
		if(lb_cierre!=false)
		{
			window.open("sigesp_scv_cat_sol_via.php?destino="+ls_destino+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
		}
		else
		{
			alert("Se ha procesado el cierre presupuestario del sistema")
		}
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
	valido=true;
	if(li_ejecutar==1)
	{
		ls_codsolvia=f.txtcodsolvia.value;
		if(ls_codsolvia!="")
		{
			li_totrows=f.totalfilaspersonal.value;
			ls_catmayor=f.txtcodclavia1.value;
			for(li_i=1;li_i<li_totrows;li_i++)
			{
				ls_cateval=eval("f.txtcodclavia"+li_i+".value");
				if(ls_cateval>ls_catmayor)
				{
					ls_catmayor=ls_cateval;
				}
			}
			ls_tipodoc=f.cmbtipdoc.value;
			if((ls_tipodoc=="1")||(ls_tipodoc=="2"))
			{
				codtipdoc=f.txtcodtipdoc.value;
				if(codtipdoc=="")
				{
					valido=false
					alert("Debe indicar el tipo de documento");
				}	
			}
			if(valido)
			{
				f.hidcatper.value=ls_catmayor;
				f.operacion.value="PROCESAR";
				f.action="sigesp_scv_p_calcularviaticos_int.php";
				f.submit();
			}
		}
		else
		{alert("Debe buscar una solicitud a calcular");}
	}
	else
   	{alert("No tiene permiso para realizar esta operacion");}
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{
		li_totrowspresupuesto=f.totalfilaspresupuesto.value;
		li_totrowscontable=f.totalfilascontable.value;
		ls_codtipdoc=f.txtcodtipdoc.value;
		ls_tipvia=f.cmbtipvia.value;
		if(ls_tipvia=="1")
		{
			totsolvia=f.txttotsolvia.value;
			if(totsolvia!="0,00")
			{
				f.operacion.value="GUARDAR";
				f.action="sigesp_scv_p_calcularviaticos_int.php";
				f.submit();
			}
			else
			{alert("Antes de Guardar debe procesar el calculo de la solicitud");}
		}
		else
		{
			if(li_totrowscontable>1)
			{
				if(ls_codtipdoc!="")
				{
					f.operacion.value="GUARDAR";
					f.action="sigesp_scv_p_calcularviaticos_int.php";
					f.submit();
				}
				else
				{alert("Debe seleccionar un tipo de documento");}
			}
			else
			{alert("Antes de Guardar debe procesar el calculo de la solicitud");}
		}
	}
	else
   	{alert("No tiene permiso para realizar esta operacion");}

}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>