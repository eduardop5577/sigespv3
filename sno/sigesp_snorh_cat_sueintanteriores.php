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
   function uf_print($as_codper)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codper  // Código de Personal
		//				   ad_feciniper  // Fecha de Inicio
		//				   ad_fecfinper  // Fecha Fin
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 						Fecha Última Modificación : 
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
		print "<td width=60>Año </td>";
		print "<td width=50>Mes</td>";
		print "<td width=90>Sueldo Base</td>";
		print "<td width=90>Sueldo Integral</td>";
		print "</tr>";
		$ls_sql="SELECT codemp, codper, anosue, messue, suebase, sueint, bonvac, bonfinanio, otrasig ".
				"  FROM sno_sueintegral ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codper='".$as_codper."'";
		$ls_sql=$ls_sql." ORDER BY anosue,messue ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_anosue=$row["anosue"];
				$ls_messue=$row["messue"];
				$li_suebase=$row["suebase"];
				$li_sueint=$row["sueint"];
				$li_bonvac=$row["bonvac"];
				$li_bonfinanio=$row["bonfinanio"];
				$li_otrasig=$row["otrasig"];
				$li_suebase=$io_fun_nomina->uf_formatonumerico($li_suebase);
				$li_sueint=$io_fun_nomina->uf_formatonumerico($li_sueint);
				$li_bonvac=$io_fun_nomina->uf_formatonumerico($li_bonvac);
				$li_bonfinanio=$io_fun_nomina->uf_formatonumerico($li_bonfinanio);
				$li_otrasig=$io_fun_nomina->uf_formatonumerico($li_otrasig);
	
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ls_anosue','$ls_messue','$li_suebase','$li_sueint','$li_bonvac','$li_bonfinanio','$li_otrasig');\">".$ls_anosue."</a></td>";
				print "<td>".$ls_messue."</td>";
				print "<td>".$li_suebase."</td>";
				print "<td>".$li_sueint."</td>";
				print "</tr>";			
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($io_fun_nomina);
		unset($ls_codemp);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Permiso</title>
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
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Sueldos Integrales Anteriores </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      
  </table>
  <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_codper=$_GET["codper"];
	uf_print($ls_codper);
	unset($io_fun_nomina);
?>
</div>
          <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function aceptar(ls_anosue,ls_messue,li_suebase,li_sueint,li_bonvac,li_bonfinanio,li_otrasig)
{
	opener.document.form1.cmbano.value=ls_anosue;
	opener.document.form1.cmbmes.value=ls_messue;
    opener.document.form1.txtsuelbase.value=li_suebase;
	opener.document.form1.txtsuelint.value=li_sueint;
	opener.document.form1.txtbonvac.value=li_bonvac;
	opener.document.form1.txtbonfinanio.value=li_bonfinanio;
	opener.document.form1.txtotrasig.value=li_otrasig;
	opener.document.form1.existe.value="TRUE";		
	close();
}

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_snorh_cat_sueintanteriores.php";
  	f.submit();
}
</script>
</html>
