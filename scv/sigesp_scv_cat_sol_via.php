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
require_once("class_folder/class_funciones_viaticos.php");
$io_fun_viaticos=new class_funciones_viaticos();
if(array_key_exists("hiddestino",$_POST))
{
	$ls_destino=$io_fun_viaticos->uf_obtenervalor("hiddestino","");
}
else
{
	$ls_destino=$io_fun_viaticos->uf_obtenervalor_get("destino","");
}
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$ds=new class_datastore();
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_fun= new class_funciones();
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	function uf_scv_select_beneficiario($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_beneficiario
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $as_codasi    // codigo de asignacion
		//  			   $as_proasi    // procedencia de asignaciones
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica la existencia de una asignacion de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		$io_sql=new class_sql($con);
		$io_msg=new class_mensajes();
		$io_fun= new class_funciones();
		$lb_valido=false;
		$la_personal="";
		$ls_sql="SELECT nomper,apeper,cedper".
				"  FROM scv_dt_personal,sno_personal".
				" WHERE scv_dt_personal.codemp='". $as_codemp ."'".
				"   AND scv_dt_personal.codsolvia='". $as_codsolvia ."'".
				"   AND scv_dt_personal.codemp=sno_personal.codemp".
				"   AND scv_dt_personal.codper=sno_personal.codper";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_beneficiario ERROR->".$io_fun->uf_convertirmsg($io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$io_sql->fetch_row($rs_data))
			{
				$la_personal[1]=$row["nomper"];
				$la_personal[2]=$row["apeper"];
				$la_personal[3]=$row["cedper"];
			}
			$io_sql->free_result($rs_data);
		}
		return $la_personal;
	}  // end function uf_scv_select_dt_asignaciones

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Solicitudes de Vi&aacute;ticos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
    <input name="hiddestino" type="hidden" id="hiddestino" value="<?php print $ls_destino;?>">
    <input name="hidstatus" type="hidden" id="hidstatus">
  </p>
  <table width="578" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="574" colspan="2" class="titulo-celda">Cat&aacute;logo de Solicitudes de Vi&aacute;ticos</td>
    </tr>
  </table>
<br>
    <table width="578" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="115"><div align="right">C&oacute;digo</div></td>
        <td width="461" height="22"><div align="left">
          <input name="txtcodsolvia" type="text" id="txtnombre2">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Mision</div></td>
        <td height="22"><div align="left">          <input name="txtdenmis" type="text" id="txtdenmis">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Ruta</div></td>
        <td height="22"><input name="txtdesrut" type="text" id="txtdesrut"></td>
      </tr>
      <tr>
        <td height="22"><div align="right">C&eacute;dula Beneficiario </div></td>
        <td height="22"><input name="txtcedben" type="text" id="txtcedben"></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre Beneficiario </div></td>
        <td height="22"><div align="left">
          <input name="txtnomben" type="text" id="txtnomben">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Apellido Beneficiario </div></td>
        <td height="22"><div align="left">
          <input name="txtapeben" type="text" id="txtapeben">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Buscar</a></div></td>
      </tr>
    </table>
  <?php

if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
	$ls_codsolvia="%".$_POST["txtcodsolvia"]."%";
	$ls_denmis="%".$_POST["txtdenmis"]."%";
	$ls_desrut="%".$_POST["txtdesrut"]."%";
	$ls_cedben="%".$_POST["txtcedben"]."%";
	$ls_nombre="".$_POST["txtnomben"]."";
	$ls_apellido="".$_POST["txtapeben"]."";
}
else
{
	$ls_operacion="";

}
$ls_filtro="";
if($ls_operacion=="BUSCAR")
{	
	$ls_tipvia="-";
	$ls_tabla="";
	$ls_filtro2="";
	$ls_campos="";
	switch ($ls_destino)
	{
		case "SOLICITUDINT":
			$ls_tipvia="1";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "CALCULOINT":
			$ls_tipvia="1";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "SOLICITUDORD":
			$ls_tipvia="2";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "CALCULOORD":
			$ls_tipvia="2";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "SOLICITUDPERM":
			$ls_tipvia="3";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "CALCULOPERM":
			$ls_tipvia="3";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "SOLICITUDINTERNACIONAL":
			$ls_tipvia="4";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "CALCULOINTERNACIONAL":
			$ls_tipvia="4";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "SOLICITUDNACIONAL":
			$ls_tipvia="5";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		case "CALCULONACIONAL":
			$ls_tipvia="5";
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='100' align='center'>Solicitud</td>";
			print "<td width='100'>Cedula</td>";
			print "<td>Nombre y Apellido</td>";
			print "</tr>";
			$ls_campos="MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,MAX(sno_personal.cedper) AS cedper,";
			$ls_tabla=",sno_personal,scv_dt_personal";
			$ls_filtro2="   AND sno_personal.nomper LIKE '%". $ls_nombre ."%'".
						"   AND sno_personal.apeper LIKE '%". $ls_apellido ."%'".
						"   AND scv_dt_personal.codemp=sno_personal.codemp".
						"   AND scv_dt_personal.codper=sno_personal.codper";
		break;
		default:
			print "<table width=580 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
			print "<tr class=titulo-celda>";
			print "<td width='60' align='center'>Solicitud</td>";
			print "<td width='260'>Misión</td>";
			print "<td>Ruta</td>";
			print "</tr>";
		break;
		
	}	
	if(($ls_destino!="REPORTESOLICITUDPAGODESDE")&&($ls_destino!="REPORTESOLICITUDPAGOHASTA"))
	{
		$ls_filtro="   AND scv_solicitudviatico.tipvia='".$ls_tipvia."'";
	}
	if(($ls_nombre!="$ls_destino")||($ls_apellido!=""))
	{
	}
	if($ls_cedben!="%%")
	{
		$ls_tabla=$ls_tabla.",scv_dt_personal";
		$ls_filtro=$ls_filtro."   AND scv_solicitudviatico.codemp=scv_dt_personal.codemp".
			"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
			"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
			"   AND scv_dt_personal.codper LIKE '".$ls_cedben."'";
		
	}
//	if(($ls_nombre!="")||($ls_apellido!=""))
//	{print "ENMTRO";
//		$ls_cedben=uf_scv_select_cedula($ls_codemp,$ls_nombre,$ls_apellido);
//	}
	$ls_sql="SELECT scv_solicitudviatico.codsolvia,scv_solicitudviatico.codmis,scv_solicitudviatico.codrut,".
            "       scv_solicitudviatico.fecsolvia,scv_solicitudviatico.coduniadm,scv_solicitudviatico.fecsalvia,".
			"       scv_solicitudviatico.fecregvia,scv_solicitudviatico.obssolvia,scv_solicitudviatico.codmisdes,".
			"       scv_solicitudviatico.numdiavia,scv_solicitudviatico.estsolvia,scv_solicitudviatico.solviaext,".
			"       scv_misiones.denmis,denuniadm,scv_solicitudviatico.codfuefin,scv_solicitudviatico.numaut,scv_solicitudviatico.fecaut,".		
			"       scv_solicitudviatico.codestpro1,scv_solicitudviatico.codestpro2,scv_solicitudviatico.codestpro3,".
			"       scv_solicitudviatico.codestpro4,scv_solicitudviatico.codestpro5,scv_solicitudviatico.estcla,".
			"       scv_solicitudviatico.repcajchi,scv_solicitudviatico.codinc,scv_solicitudviatico.codcar,scv_solicitudviatico.estopediv,scv_solicitudviatico.codmon,scv_solicitudviatico.tascam1, ".
			"       MAX(scv_solicitudviatico.estsolfam) AS estsolfam,".$ls_campos.
			"       (SELECT denmis FROM scv_misiones WHERE scv_solicitudviatico.codemp=scv_misiones.codemp".
			"                                        AND scv_solicitudviatico.codmisdes=scv_misiones.codmis) AS denmisdes,".	
			"       (SELECT deninc FROM scv_incremento WHERE scv_solicitudviatico.codemp=scv_incremento.codemp".
			"                                        AND scv_solicitudviatico.codinc=scv_incremento.codinc) AS deninc,".	
			"       (SELECT dencar FROM scv_cargafamiliar WHERE scv_solicitudviatico.codemp=scv_cargafamiliar.codemp".
			"                                        AND scv_solicitudviatico.codcar=scv_cargafamiliar.codcar) AS dencar,".	
			"       (SELECT MAX(desrut) FROM scv_rutas WHERE scv_solicitudviatico.codemp=scv_rutas.codemp".
			"                                        AND scv_solicitudviatico.codrut=scv_rutas.codrut) AS desrut".	
			"  FROM scv_solicitudviatico,scv_misiones,spg_unidadadministrativa, ".
			"        spg_dt_unidadadministrativa ".$ls_tabla.//AGREGADO POR OFIMATICA DE VENEZUELA EL 24-05-2011 para el manejo de viaticos por reposicion de VIATICOS
			" WHERE scv_solicitudviatico.codemp='".$ls_codemp."'".
			"   AND scv_solicitudviatico.codsolvia LIKE '".$ls_codsolvia."'".
			"   AND scv_misiones.denmis LIKE '".$ls_denmis."'".
		//	"   AND scv_rutas.desrut LIKE '".$ls_desrut."'".
			$ls_filtro.
			$ls_filtro2.
			"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
			"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
//			"   AND scv_solicitudviatico.codemp=scv_rutas.codemp".
//			"   AND scv_solicitudviatico.codrut=scv_rutas.codrut".
			"   AND scv_solicitudviatico.codemp=spg_unidadadministrativa.codemp".
			"   AND scv_solicitudviatico.coduniadm=spg_unidadadministrativa.coduniadm ".
			"  	AND spg_dt_unidadadministrativa.codemp=scv_solicitudviatico.codemp ".
			"   AND spg_dt_unidadadministrativa.estcla=scv_solicitudviatico.estcla ".
			"   AND spg_dt_unidadadministrativa.codestpro1=scv_solicitudviatico.codestpro1 ".
			"   AND spg_dt_unidadadministrativa.codestpro2=scv_solicitudviatico.codestpro2 ".
			"   AND spg_dt_unidadadministrativa.codestpro3=scv_solicitudviatico.codestpro3 ".
			"   AND spg_dt_unidadadministrativa.codestpro4=scv_solicitudviatico.codestpro4 ".
			"   AND spg_dt_unidadadministrativa.codestpro5=scv_solicitudviatico.codestpro5".
			" GROUP BY scv_solicitudviatico.codemp, scv_solicitudviatico.codsolvia, scv_solicitudviatico.codmis, ".
			"          scv_solicitudviatico.codrut,scv_solicitudviatico.codmisdes,".
            "          scv_solicitudviatico.fecsolvia,scv_solicitudviatico.coduniadm,scv_solicitudviatico.fecsalvia,".
			"          scv_solicitudviatico.fecregvia,scv_solicitudviatico.obssolvia,scv_solicitudviatico.codinc,scv_solicitudviatico.codcar,".
			"          scv_solicitudviatico.numdiavia,scv_solicitudviatico.estsolvia,scv_solicitudviatico.solviaext,".
			"          scv_misiones.denmis,denuniadm,scv_solicitudviatico.codfuefin,".
			"          scv_solicitudviatico.codestpro1,scv_solicitudviatico.codestpro2,scv_solicitudviatico.codestpro3,".
			"          scv_solicitudviatico.codestpro4,scv_solicitudviatico.codestpro5,scv_solicitudviatico.estcla,scv_solicitudviatico.repcajchi,scv_solicitudviatico.numaut,scv_solicitudviatico.fecaut".
			" ORDER BY scv_solicitudviatico.codsolvia";
	$rs_cta=$io_sql->select($ls_sql);
    $data=$rs_cta;
    if($row=$io_sql->fetch_row($rs_cta))
	{
		$data=$io_sql->obtener_datos($rs_cta);
		$arrcols=array_keys($data);
		$totcol=count((array)$arrcols);
		$ds->data=$data;

		$totrow=$ds->getRowCount("codsolvia");
	
		for($z=1;$z<=$totrow;$z++)
		{
			switch ($ls_destino)
			{
				case "SOLICITUD":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ls_estopediv= $data["estopediv"][$z];
					$ls_codmon= $data["codmon"][$z];
					$ls_tascam1= $data["tascam1"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					print "<td align='center'><a href=\"javascript: aceptar('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia',".
						  "                                                 '$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1','$ls_codestpro2',".
						  "                                                 '$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi',".
						  "                                                 '$ls_estopediv','$ls_codmon','$ls_tascam1');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_denmis."</td>";
					print "<td>".$ls_desrut."</td>";
					print "</tr>";			
				break;
				case "SOLICITUDINT":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					$ls_nomper= $data["nomper"][$z];
					$ls_apeper= $data["apeper"][$z];
					$ls_cedper= $data["cedper"][$z];
					$ls_numaut= $data["numaut"][$z];
					$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
					print "<td align='center'><a href=\"javascript: aceptar_int('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia','$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi','$ls_codmisdes','$ls_denmisdes','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_cedper."</td>";
					print "<td>".$ls_nomper." ".$ls_apeper."</td>";
					print "</tr>";			
				break;
				case "SOLICITUDORD":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ls_codinc= $data["codinc"][$z];
					$ls_deninc= $data["deninc"][$z];
					$ls_codcar= $data["codcar"][$z];
					$ls_dencar= $data["dencar"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					$ls_nomper= $data["nomper"][$z];
					$ls_apeper= $data["apeper"][$z];
					$ls_cedper= $data["cedper"][$z];
					$ls_numaut= $data["numaut"][$z];
					$li_estsolfam= $data["estsolfam"][$z];
					$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
					print "<td align='center'><a href=\"javascript: aceptar_ord('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia','$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi',".
						  "                                                 '$ls_codmisdes','$ls_denmisdes','$ls_codinc','$ls_deninc','$ls_codcar','$ls_dencar','$ls_numaut','$ls_fecaut','$li_estsolfam');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_cedper."</td>";
					print "<td>".$ls_nomper." ".$ls_apeper."</td>";
					print "</tr>";			
				break;
				case "SOLICITUDPERM":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					$ls_nomper= $data["nomper"][$z];
					$ls_apeper= $data["apeper"][$z];
					$ls_cedper= $data["cedper"][$z];
					$ls_numaut= $data["numaut"][$z];
					$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
					print "<td align='center'><a href=\"javascript: aceptar_perm('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia','$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi','$ls_codmisdes','$ls_denmisdes','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_cedper."</td>";
					print "<td>".$ls_nomper." ".$ls_apeper."</td>";
					print "</tr>";			
				break;
				case "SOLICITUDINTERNACIONAL":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					$ls_nomper= $data["nomper"][$z];
					$ls_apeper= $data["apeper"][$z];
					$ls_cedper= $data["cedper"][$z];
					$ls_numaut= $data["numaut"][$z];
					$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
					print "<td align='center'><a href=\"javascript: aceptar_internacional('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia',".
						  "                                                 '$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1','$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_cedper."</td>";
					print "<td>".$ls_nomper." ".$ls_apeper."</td>";
					print "</tr>";			
				break;
				case "SOLICITUDNACIONAL":
					print "<tr class=celdas-blancas>";
					$ls_codsolvia=$data["codsolvia"][$z];
					$ls_codmis=    $data["codmis"][$z];
					$ls_denmis=    $data["denmis"][$z];
					$ls_codmisdes= $data["codmisdes"][$z];
					$ls_denmisdes= $data["denmisdes"][$z];
					$ls_codrut=    $data["codrut"][$z];
					$ls_desrut=    $data["desrut"][$z];
					$ld_fecsolvia= $data["fecsolvia"][$z];
					$ls_coduniadm= $data["coduniadm"][$z];
					$ls_denuniadm= $data["denuniadm"][$z];
					$ld_fecsalvia= $data["fecsalvia"][$z];
					$ld_fecregvia= $data["fecregvia"][$z];
					$ls_obssolvia= $data["obssolvia"][$z];
					$li_numdiavia= $data["numdiavia"][$z];
					$ls_estsolvia= $data["estsolvia"][$z];
					$li_solviaext= $data["solviaext"][$z];
					$ls_codfuefin= $data["codfuefin"][$z];
					$ls_codestpro1= $data["codestpro1"][$z];
					$ls_codestpro2= $data["codestpro2"][$z];
					$ls_codestpro3= $data["codestpro3"][$z];
					$ls_codestpro4= $data["codestpro4"][$z];
					$ls_codestpro5= $data["codestpro5"][$z];
					$ls_estcla= $data["estcla"][$z];
					$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
					$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
					$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
					$li_numdiavia=number_format($li_numdiavia,2,',','.');
					$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
					$ls_nomper= $data["nomper"][$z];
					$ls_apeper= $data["apeper"][$z];
					$ls_cedper= $data["cedper"][$z];
					$ls_numaut= $data["numaut"][$z];
					$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
					print "<td align='center'><a href=\"javascript: aceptar_nacional('$ls_codsolvia','$ls_codmis','$ls_denmis','$ls_codrut',".
						  "                                                 '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
						  "													'$ls_denuniadm','$ld_fecsalvia','$ld_fecregvia',".
						  "                                                 '$ls_obssolvia','$li_numdiavia',".
						  "                                                 '$ls_estsolvia','$li_solviaext','$ls_codfuefin','$ls_codestpro1','$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$li_repcajchi','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
					print "<td>".$ls_cedper."</td>";
					print "<td>".$ls_nomper." ".$ls_apeper."</td>";
					print "</tr>";			
				break;
				case "CALCULO":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_codmis=    $data["codmis"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_codrut=    $data["codrut"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ld_fecsolvia= $data["fecsolvia"][$z];
						$ls_coduniadm= $data["coduniadm"][$z];
						$ls_denuniadm= $data["denuniadm"][$z];
						$ld_fecsalvia= $data["fecsalvia"][$z];
						$ld_fecregvia= $data["fecregvia"][$z];
						$ls_obssolvia= $data["obssolvia"][$z];
						$li_numdiavia= $data["numdiavia"][$z];
						$li_solviaext= $data["solviaext"][$z];
						$ls_codfuefin= $data["codfuefin"][$z];
						$ls_tascam1= $data["tascam1"][$z];
						$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
						$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
						$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
						$li_numdiavia=number_format($li_numdiavia,2,',','.');
						$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
						print "<td align='center'><a href=\"javascript: aceptar_cal('$ls_codsolvia', '$ls_codmis','$ls_denmis', 
						  														   '$ls_codrut',".
							  "                                                    '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
							  "													   '$ls_denuniadm','$ld_fecsalvia',  ".
							  "                                                    '$ld_fecregvia',".
							  "                                                    '$ls_obssolvia','$li_numdiavia',".
							  "                                                    '$ls_estsolvia','$li_solviaext', ". 
							  "                                                    '$ls_codfuefin','$li_repcajchi','$ls_tascam1');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_denmis."</td>";
						print "<td>".$ls_desrut."</td>";
						print "</tr>";			
					}
				break;
				case "REPORTESOLICITUDPAGODESDE":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="P" OR $ls_estsolvia=="C") // Modificada por Ofimatica de Venezuela el 09-05-2011, agregada esta condicion, ya que la solicitud de pago de viaticos se puede imprimir antes de su aprobacion, ya que muchas veces la aprobacion de dichas solicitudes de pago estan en el departamento de presupuesto, pero estas requieren el fisico (la impresion) de dicha solicitud de pago (Calculo)
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_codmis=    $data["codmis"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_codrut=    $data["codrut"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ld_fecsolvia= $data["fecsolvia"][$z];
						$ls_coduniadm= $data["coduniadm"][$z];
						$ls_denuniadm= $data["denuniadm"][$z];
						$ld_fecsalvia= $data["fecsalvia"][$z];
						$ld_fecregvia= $data["fecregvia"][$z];
						$ls_obssolvia= $data["obssolvia"][$z];
						$li_numdiavia= $data["numdiavia"][$z];
						$li_solviaext= $data["solviaext"][$z];
						$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
						$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
						$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
						$li_numdiavia=number_format($li_numdiavia,2,',','.');
						print "<td align='center'><a href=\"javascript: aceptar_solicituddesde('$ls_codsolvia');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_denmis."</td>";
						print "<td>".$ls_desrut."</td>";
						print "</tr>";			
					}
				break;
				case "REPORTESOLICITUDPAGOHASTA":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="P" OR $ls_estsolvia=="C") // Modificada por Ofimatica de Venezuela el 09-05-2011, agregada esta condicion, ya que la solicitud de pago de viaticos se puede imprimir antes de su aprobacion, ya que muchas veces la aprobacion de dichas solicitudes de pago estan en el departamento de presupuesto, pero estas requieren el fisico (la impresion) de dicha solicitud de pago (Calculo)
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_codmis=    $data["codmis"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_codrut=    $data["codrut"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ld_fecsolvia= $data["fecsolvia"][$z];
						$ls_coduniadm= $data["coduniadm"][$z];
						$ls_denuniadm= $data["denuniadm"][$z];
						$ld_fecsalvia= $data["fecsalvia"][$z];
						$ld_fecregvia= $data["fecregvia"][$z];
						$ls_obssolvia= $data["obssolvia"][$z];
						$li_numdiavia= $data["numdiavia"][$z];
						$li_solviaext= $data["solviaext"][$z];
						$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
						$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
						$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
						$li_numdiavia=number_format($li_numdiavia,2,',','.');
						print "<td align='center'><a href=\"javascript: aceptar_solicitudhasta('$ls_codsolvia');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_denmis."</td>";
						print "<td>".$ls_desrut."</td>";
						print "</tr>";			
					}
				break;
				case "CALCULOINT":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ls_codestpro1= $data["codestpro1"][$z];
						$ls_codestpro2= $data["codestpro2"][$z];
						$ls_codestpro3= $data["codestpro3"][$z];
						$ls_codestpro4= $data["codestpro4"][$z];
						$ls_codestpro5= $data["codestpro5"][$z];
						$ls_estcla= $data["estcla"][$z];
						$ls_nomper= $data["nomper"][$z];
						$ls_apeper= $data["apeper"][$z];
						$ls_cedper= $data["cedper"][$z];
						$ls_numaut= $data["numaut"][$z];
						$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
						print "<td align='center'><a href=\"javascript: aceptar_cal_int('$ls_codsolvia','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper." ".$ls_apeper."</td>";
						print "</tr>";			
					}
				break;
				case "CALCULOORD":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ls_codestpro1= $data["codestpro1"][$z];
						$ls_codestpro2= $data["codestpro2"][$z];
						$ls_codestpro3= $data["codestpro3"][$z];
						$ls_codestpro4= $data["codestpro4"][$z];
						$ls_codestpro5= $data["codestpro5"][$z];
						$ls_estcla= $data["estcla"][$z];
						$ls_nomper= $data["nomper"][$z];
						$ls_apeper= $data["apeper"][$z];
						$ls_cedper= $data["cedper"][$z];
						$ls_numaut= $data["numaut"][$z];
						$li_estsolfam= $data["estsolfam"][$z];
						$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
						print "<td align='center'><a href=\"javascript: aceptar_cal_ord('$ls_codsolvia','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5',".
						  "                                                 '$ls_estcla','$ls_numaut','$ls_fecaut','$li_estsolfam');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper." ".$ls_apeper."</td>";
						print "</tr>";			
					}
				break;
				case "CALCULOPERM":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$li_numdiavia= $data["numdiavia"][$z];
						$ls_codestpro1= $data["codestpro1"][$z];
						$ls_codestpro2= $data["codestpro2"][$z];
						$ls_codestpro3= $data["codestpro3"][$z];
						$ls_codestpro4= $data["codestpro4"][$z];
						$ls_codestpro5= $data["codestpro5"][$z];
						$ls_estcla= $data["estcla"][$z];
						$ls_nomper= $data["nomper"][$z];
						$ls_apeper= $data["apeper"][$z];
						$ls_cedper= $data["cedper"][$z];
						$ls_numaut= $data["numaut"][$z];
						$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
						print "<td align='center'><a href=\"javascript: aceptar_cal_perm('$ls_codsolvia','$li_numdiavia','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper." ".$ls_apeper."</td>";
						print "</tr>";			
					}
				break;
				case "CALCULOINTERNACIONAL":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ls_codestpro1= $data["codestpro1"][$z];
						$ls_codestpro2= $data["codestpro2"][$z];
						$ls_codestpro3= $data["codestpro3"][$z];
						$ls_codestpro4= $data["codestpro4"][$z];
						$ls_codestpro5= $data["codestpro5"][$z];
						$ls_estcla= $data["estcla"][$z];
						$ls_nomper= $data["nomper"][$z];
						$ls_apeper= $data["apeper"][$z];
						$ls_cedper= $data["cedper"][$z];
						$ls_numaut= $data["numaut"][$z];
						$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
						print "<td align='center'><a href=\"javascript: aceptar_cal_internacional('$ls_codsolvia','$ls_codestpro1',".
						  "                                                 '$ls_codestpro2','$ls_codestpro3','$ls_codestpro4','$ls_codestpro5','$ls_estcla','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper." ".$ls_apeper."</td>";
						print "</tr>";			
					}
				break;
				case "CALCULONACIONAL":
					$ls_estsolvia= $data["estsolvia"][$z];
					if($ls_estsolvia=="R")
					{
						print "<tr class=celdas-blancas>";
						$ls_codsolvia=$data["codsolvia"][$z];
						$ls_codmis=    $data["codmis"][$z];
						$ls_denmis=    $data["denmis"][$z];
						$ls_codrut=    $data["codrut"][$z];
						$ls_desrut=    $data["desrut"][$z];
						$ld_fecsolvia= $data["fecsolvia"][$z];
						$ls_coduniadm= $data["coduniadm"][$z];
						$ls_denuniadm= $data["denuniadm"][$z];
						$ld_fecsalvia= $data["fecsalvia"][$z];
						$ld_fecregvia= $data["fecregvia"][$z];
						$ls_obssolvia= $data["obssolvia"][$z];
						$li_numdiavia= $data["numdiavia"][$z];
						$li_solviaext= $data["solviaext"][$z];
						$ls_codfuefin= $data["codfuefin"][$z];
						$ld_fecsolvia=$io_fun->uf_convertirfecmostrar($ld_fecsolvia);
						$ld_fecsalvia=$io_fun->uf_convertirfecmostrar($ld_fecsalvia);
						$ld_fecregvia=$io_fun->uf_convertirfecmostrar($ld_fecregvia);
						$li_numdiavia=number_format($li_numdiavia,2,',','.');
						$li_repcajchi=$data["repcajchi"][$z];//Campo agregado por OFIMATICA DE VENEZUELA para manejo de viaticos por reposicion de VIATICOS
						$ls_nomper= $data["nomper"][$z];
						$ls_apeper= $data["apeper"][$z];
						$ls_cedper= $data["cedper"][$z];
						$ls_numaut= $data["numaut"][$z];
						$ls_fecaut= $io_fun->uf_convertirfecmostrar($data["fecaut"][$z]);
						print "<td align='center'><a href=\"javascript: aceptar_cal_nacional('$ls_codsolvia', '$ls_codmis','$ls_denmis', 
						  														   '$ls_codrut',".
							  "                                                    '$ls_desrut','$ld_fecsolvia','$ls_coduniadm',".
							  "													   '$ls_denuniadm','$ld_fecsalvia',  ".
							  "                                                    '$ld_fecregvia',".
							  "                                                    '$ls_obssolvia','$li_numdiavia',".
							  "                                                    '$ls_estsolvia','$li_solviaext', ". 
							  "                                                    '$ls_codfuefin','$li_repcajchi','$ls_numaut','$ls_fecaut');\">".$ls_codsolvia."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper." ".$ls_apeper."</td>";
						print "</tr>";			
					}
				break;
			}
		}
	}
	else
	{
		$io_msg->message("No hay registros");
	}

}
print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,ls_estopediv,ls_codmon,ls_tascam1)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos.php";
	if(li_solviaext==1)
	{opener.document.form1.chksolviaext.checked=true;}
	else
	{opener.document.form1.chksolviaext.checked=false;}

	if(ls_estopediv==1)
	{opener.document.form1.chkestopediv.checked=true;}
	else
	{opener.document.form1.chkestopediv.checked=false;}
	opener.document.form1.txtcodtipmon.value=ls_codmon;
	opener.document.form1.txttascam1.value=ls_tascam1;
	//Agregado por ofimatica de Venezuela el 24-05-2011 para el manejo de viaticos por reposicion de caja chica
	if(li_repcajchi==1)
	{opener.document.form1.chkrepcajchi.checked=true;}
	else
	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 

	opener.document.form1.submit();
	close();
  }

  function aceptar_nacional(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos_nacionales.php";
	if(li_solviaext==1)
	{opener.document.form1.chksolviaext.checked=true;}
	else
	{opener.document.form1.chksolviaext.checked=false;}
	//Agregado por ofimatica de Venezuela el 24-05-2011 para el manejo de viaticos por reposicion de caja chica
	if(li_repcajchi==1)
	{opener.document.form1.chkrepcajchi.checked=true;}
	else
	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 

	opener.document.form1.submit();
	close();
  }

  function aceptar_int(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,ls_codmisdes,ls_denmisdes,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodmisdes.value=ls_codmisdes;
	opener.document.form1.txtdenmisdes.value=ls_denmisdes;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;	
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos_int.php";

	opener.document.form1.submit();
	close();
  }
  function aceptar_ord(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,
				   ls_codmisdes,ls_denmisdes,ls_codinc,ls_deninc,ls_codcar,ls_dencar,ls_numaut,ls_fecaut,li_estsolfam)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodmisdes.value=ls_codmisdes;
	opener.document.form1.txtdenmisdes.value=ls_denmisdes;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;	
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtcodcar.value=ls_codcar;
	opener.document.form1.txtdencar.value=ls_dencar;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	if(li_estsolfam==1)
	{opener.document.form1.chkestsolfam.checked=true;}
	else
	{opener.document.form1.chkestsolfam.checked=false;}
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos_orden.php";


	opener.document.form1.submit();
	close();
  }

  function aceptar_perm(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,ls_codmisdes,ls_denmisdes,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodmisdes.value=ls_codmisdes;
	opener.document.form1.txtdenmisdes.value=ls_denmisdes;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;	
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos_permanencia.php";
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	if(li_solviaext==1)
	{opener.document.form1.chksolviaext.checked=true;}
	else
	{opener.document.form1.chksolviaext.checked=false;}
	//Agregado por ofimatica de Venezuela el 24-05-2011 para el manejo de viaticos por reposicion de caja chica
	if(li_repcajchi==1)
	{opener.document.form1.chkrepcajchi.checked=true;}
	else
	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 

	opener.document.form1.submit();
	close();
  }
  
  function aceptar_internacional(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				   ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,
				   ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,li_repcajchi,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
//	opener.document.form1.txtcodrut.value=ls_codrut;
//	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.hidestcla.value=ls_estcla;
	opener.document.form1.action="sigesp_scv_p_solicitudviaticos_internacionales.php";
//	if(li_solviaext==1)
//	{opener.document.form1.chksolviaext.checked=true;}
//	else
//	{opener.document.form1.chksolviaext.checked=false;}
	//Agregado por ofimatica de Venezuela el 24-05-2011 para el manejo de viaticos por reposicion de caja chica
//	if(li_repcajchi==1)
//	{opener.document.form1.chkrepcajchi.checked=true;}
//	else
//	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 

	opener.document.form1.submit();
	close();
  }



  function aceptar_cal(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				       ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,li_repcajchi,ls_tascam1)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos.php";
	if(li_solviaext==1)
	{opener.document.form1.chksolviaext.checked=true;}
	else
	{opener.document.form1.chksolviaext.checked=false;}
	//Agregado por ofimatica de Venezuela el 25-05-2011 para el manejo de viaticos por reposicion de caja chica
	if(li_repcajchi==1)
	{opener.document.form1.chkrepcajchi.checked=true;}
	else
	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 
	opener.document.form1.txttascam1.value=ls_tascam1;
	opener.document.form1.submit();
	close();
  }

  function aceptar_cal_int(ls_codsolvia,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos_int.php";
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	
	opener.document.form1.submit();
	close();
  }

  function aceptar_cal_internacional(ls_codsolvia,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos_internacionales.php";
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;

	opener.document.form1.submit();
	close();
  }

  function aceptar_cal_perm(ls_codsolvia,li_numdiavia,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos_permanencia.php";
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;

	opener.document.form1.submit();
	close();
  }

  function aceptar_cal_ord(ls_codsolvia,ls_codestpro1,ls_codestpro2,ls_codestpro3,ls_codestpro4,ls_codestpro5,ls_estcla,ls_numaut,ls_fecaut,li_estsolfam)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos_orden.php";
	opener.document.form1.txtcodestpro1.value=ls_codestpro1;
	opener.document.form1.txtcodestpro2.value=ls_codestpro2;
	opener.document.form1.txtcodestpro3.value=ls_codestpro3;
	opener.document.form1.txtcodestpro4.value=ls_codestpro4;
	opener.document.form1.txtcodestpro5.value=ls_codestpro5;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.hidestsolfam.value=li_estsolfam;
	if(li_estsolfam==1)
	{opener.document.form1.chkestsolfam.checked=true;}
	else
	{opener.document.form1.chkestsolfam.checked=false;}

	opener.document.form1.submit();
	close();
  }

  function aceptar_cal_nacional(ls_codsolvia,ls_codmis,ls_denmis,ls_codrut,ls_desrut,ld_fecsolvia,ls_coduniadm,ls_denuniadm,
  				       ld_fecsalvia,ld_fecregvia,ls_obssolvia,li_numdiavia,ls_estsolvia,li_solviaext,ls_codfuefin,li_repcajchi,ls_numaut,ls_fecaut)
  {
	opener.document.form1.txtcodsolvia.value=ls_codsolvia;
	opener.document.form1.txtcodmis.value=ls_codmis;
	opener.document.form1.txtdenmis.value=ls_denmis;
	opener.document.form1.txtcodrut.value=ls_codrut;
	opener.document.form1.txtdesrut.value=ls_desrut;
	opener.document.form1.txtfecsolvia.value=ld_fecsolvia;
	opener.document.form1.txtcoduniadm.value=ls_coduniadm;
	opener.document.form1.txtdenuniadm.value=ls_denuniadm;
	opener.document.form1.txtfecsal.value=ld_fecsalvia;
	opener.document.form1.txtfecreg.value=ld_fecregvia;
	opener.document.form1.txtobssolvia.value=ls_obssolvia;
	opener.document.form1.txtnumdia.value=li_numdiavia;
	opener.document.form1.hidestsolvia.value=ls_estsolvia;
	opener.document.form1.txtcodfuefin.value=ls_codfuefin;
	opener.document.form1.txtnumaut.value=ls_numaut;
	opener.document.form1.txtfecaut.value=ls_fecaut;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.hidestatus.value="C";
	opener.document.form1.action="sigesp_scv_p_calcularviaticos_nacionales.php";
	if(li_solviaext==1)
	{opener.document.form1.chksolviaext.checked=true;}
	else
	{opener.document.form1.chksolviaext.checked=false;}
	//Agregado por ofimatica de Venezuela el 25-05-2011 para el manejo de viaticos por reposicion de caja chica
	if(li_repcajchi==1)
	{opener.document.form1.chkrepcajchi.checked=true;}
	else
	{opener.document.form1.chkrepcajchi.checked=false;}
	//FIN de bloque de ofimatica de Venezuela 

	opener.document.form1.submit();
	close();
  }
  function aceptar_solicituddesde(ls_codsolvia)
  {
	opener.document.form1.txtcodsoldes.value=ls_codsolvia;
	opener.document.form1.txtcodsoldes.readonly=true;
	opener.document.form1.txtcodsolhas.value="";
	opener.document.form1.txtcodsolhas.readonly=true;
	close();
  }

  function aceptar_solicitudhasta(ls_codsolvia)
  {
	if(opener.document.form1.txtcodsoldes.value<=ls_codsolvia)
	{
		opener.document.form1.txtcodsolhas.value=ls_codsolvia;
		opener.document.form1.txtcodsolhas.readonly=true;
	}
	else
	{
		alert("El Rango esta Inválido");
	}
	close();
  }

  function ue_search()
  {
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.action="sigesp_scv_cat_sol_via.php";
	f.submit();
  }
</script>
</html>
