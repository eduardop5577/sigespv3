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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}

   //--------------------------------------------------------------
   function uf_print($ls_codnom,$ls_anocurper,$ls_mescurpe,$li_totalcalculado)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function : uf_print
		//		   Access : public 
		//	    Arguments : as_tipo  // Tipo de Llamada del catálogo
		//	    			as_codnom  // Código de Nómina
		//	    			as_codnomhas  // Código de Nómina Hasta
		//	  Description : Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por : Ing. Yesenia Moreno
		// Fecha Creación : 10/04/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();
		require_once("class_folder/class_funciones_nomina.php");
		$io_fun_nomina=new class_funciones_nomina();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=12>Código</td>";
		print "<td width=12>Cédula</td>";
		print "<td width=200>Apellidos y Nombre</td>";
		print "<td width=5>Días</td>";
		print "<td width=50>Monto Aporte</td>";
		print "</tr>";
		$lb_valido=true;
		
		$ls_criterio=" AND ((sno_fideiperiodo.codnom='".$ls_codnom."')";
		$ls_criterio=$ls_criterio.") ";
		$ls_sql="SELECT sno_fideiperiodo.codemp, sno_fideiperiodo.codnom, sno_fideiperiodo.codper, sno_fideiperiodo.anocurper, ".
				"	    sno_fideiperiodo.mescurper, sno_fideiperiodo.bonvacper, sno_fideiperiodo.bonfinper, sno_fideiperiodo.sueintper, ".
				"		sno_fideiperiodo.apoper, sno_fideiperiodo.bonextper, sno_fideiperiodo.diafid, sno_fideiperiodo.diaadi, sno_personal.cedper, ".
				"		sno_personal.nomper, sno_personal.apeper, sno_fideiperiodo.bonvacadiper, sno_fideiperiodo.bonfinadiper, sno_fideiperiodo.sueintadiper, ".
				"		sno_fideiperiodo.apopreper, sno_fideiperiodo.apoadiper ".
				"  FROM sno_fideiperiodo, sno_personal ".
				" WHERE sno_fideiperiodo.codemp='".$ls_codemp."' ".
				"   AND sno_fideiperiodo.anocurper='".$ls_anocurper."'".
				"   AND sno_fideiperiodo.mescurper=".$ls_mescurpe." ".
				$ls_criterio.
				"   AND sno_fideiperiodo.codemp=sno_personal.codemp ".
				"   AND sno_fideiperiodo.codper=sno_personal.codper ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$io_mensajes->message("CLASE->Cat. Fid Garantia MÉTODO->uf_load_fideiperiodo ERROR->".$io_funciones->uf_convertirmsg($io_sql->message));
		}
		else
		{
			$li_totalcalculado=0;
			while(!$rs_data->EOF)
			{
				$li_aportes=0;
				$li_diastotales=0;
				$ls_codper=$rs_data->fields["codper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_nomper=$rs_data->fields["apeper"].", ".$rs_data->fields["nomper"];
				$li_sueintper=$io_fun_nomina->uf_formatonumerico($rs_data->fields["sueintper"]);
				$li_bonvacper=$io_fun_nomina->uf_formatonumerico($rs_data->fields["bonvacper"]);
				$li_bonfinper=$io_fun_nomina->uf_formatonumerico($rs_data->fields["bonfinper"]);
				$li_bonexpter=$io_fun_nomina->uf_formatonumerico($rs_data->fields["bonextper"]);
				$li_diasfid=$rs_data->fields["diafid"];
				$li_diasadi=$rs_data->fields["diaadi"];
				$li_diastotales=$li_diasfid+$li_diasadi;
				$li_sueint_adi=$io_fun_nomina->uf_formatonumerico($rs_data->fields["sueintadiper"]);
				$li_bonvacper_adi=$io_fun_nomina->uf_formatonumerico($rs_data->fields["bonvacadiper"]);
				$li_bonfinper_adi=$io_fun_nomina->uf_formatonumerico($rs_data->fields["bonfinadiper"]);
				$li_apoadiper=$io_fun_nomina->uf_formatonumerico($rs_data->fields["apoadiper"]);
				$li_apopreper=$io_fun_nomina->uf_formatonumerico($rs_data->fields["apopreper"]);
				$li_aportes=$rs_data->fields["apoper"];
				$li_totalcalculado=$li_totalcalculado+$li_aportes;
				$li_aportes=$io_fun_nomina->uf_formatonumerico($li_aportes);
				
				$rs_data->MoveNext();
				
				print "<tr class=celdas-blancas>";
				print "<td>".$ls_codper."</td>";
				print "<td>".$ls_cedper."</td>";
				print "<td>".$ls_nomper."</td>";
				print "<td>".$li_diastotales."</td>";
				print "<td>".$li_aportes."</td>";
				print "</tr>";	
			}
			$io_sql->free_result($rs_data);
			$li_totalcalculado=$io_fun_nomina->uf_formatonumerico($li_totalcalculado);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($ls_codnom);
		unset($ld_peractnom);
		return $li_totalcalculado;
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Total calculado para el deposito de la garantía</title>
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
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana">Total calculado para el deposito de la garantía </td>
    </tr>
  </table>
  <p>
    <?php
	$li_totalcalculado=0;
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	$ls_anocurper=$io_fun_nomina->uf_obtenervalor_get("anocurper","");
	$ls_mescurpe=$io_fun_nomina->uf_obtenervalor_get("mescurpe","");
	$li_totalcalculado=uf_print($ls_codnom,$ls_anocurper,$ls_mescurpe,$li_totalcalculado);
	unset($io_fun_nomina);
?>
    </div></p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="396" height="20" class="titulo-conect"><div align="right">Total Calculado      
          
      </div></td>
      <td width="97" height="20" class="titulo-conect"><div align="right">
        <input name="txttotalcalculado" type="text" id="txttotalcalculado" style="text-align:right" value="<?php print $li_totalcalculado;?>" size="10" readonly="readonly">
      </div></td>
    </tr>
  </table>
  <p>&nbsp;  </p>
</form>
</body>
<script >
function aceptar(anocur,mescurper,desmes)
{
	opener.document.form1.txtanocurper.value=anocur;
	opener.document.form1.txtanocurper.readOnly=true;
	opener.document.form1.txtmescurper.value=mescurper;
	opener.document.form1.txtmescurper.readOnly=true;
	opener.document.form1.txtdesmesper.value=desmes;
	opener.document.form1.txtdesmesper.readOnly=true;
	close();
}

</script>
</html>
