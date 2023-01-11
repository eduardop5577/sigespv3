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
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
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
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=125>Fecha Sueldo</td>";
		print "<td width=125>Sueldo Base</td>";
		print "<td width=125>Sueldo Integral</td>";
		print "<td width=125>Sueldo Promedio Diario</td>";
		print "</tr>";
		$ls_sql="SELECT fecsue,suebas,sueint,sueprodia, codded, codtipper,alibonvac,alibonfinanio,otrasig,confij,convar,estmodcont,bonvac,bonfinanio,comsue,".
				"		(SELECT desded ".
				"		   FROM sno_dedicacion ".
				"         WHERE sno_sueldoshistoricos.codemp = sno_dedicacion.codemp ".
				"			AND sno_sueldoshistoricos.codded = sno_dedicacion.codded) AS desded, ".
				"		(SELECT destipper ".
				"		   FROM sno_tipopersonal ".
				"         WHERE sno_sueldoshistoricos.codemp = sno_tipopersonal.codemp ".
				"			AND sno_sueldoshistoricos.codded = sno_tipopersonal.codded ".
				"			AND sno_sueldoshistoricos.codtipper = sno_tipopersonal.codtipper) AS destipper ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" ORDER BY fecsue";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ld_fecsue=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecsue"]);
				$li_suebas=number_format($rs_data->fields["suebas"],2,",",".");				
				$li_sueint=number_format($rs_data->fields["sueint"],2,",",".");					
				$li_sueprodia=number_format($rs_data->fields["sueprodia"],2,",",".");
				$li_bonvac=number_format($rs_data->fields["bonvac"],2,",",".");
				$li_bonfinanio=number_format($rs_data->fields["bonfinanio"],2,",",".");
				$li_otrasig=number_format($rs_data->fields["otrasig"],2,",",".");
				$li_confij=number_format($rs_data->fields["confij"],2,",",".");	
				$li_convar=number_format($rs_data->fields["convar"],2,",",".");	
                                $li_comsue=number_format($rs_data->fields["comsue"],2,",",".");
				$ls_estmodcont=$rs_data->fields["estmodcont"];		
				$ls_codded=$rs_data->fields["codded"];	
				$ls_desded=$rs_data->fields["desded"];	
				$ls_codtipper=$rs_data->fields["codtipper"];	
				$ls_destipper=$rs_data->fields["destipper"];	
				$ls_mes=substr($rs_data->fields["fecsue"],5,2);	
				$ls_anio=substr($rs_data->fields["fecsue"],0,4);	
				$li_alibonvac=number_format($rs_data->fields["alibonvac"],2,",",".");
				$li_alibonfinanio=number_format($rs_data->fields["alibonfinanio"],2,",",".");
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ld_fecsue','$li_suebas','$li_sueint','$li_sueprodia',".
					  "'$ls_codded','$ls_desded','$ls_codtipper','$ls_destipper','$li_alibonvac','$li_alibonfinanio',".
					  "'$li_otrasig','$li_confij','$li_convar','$ls_estmodcont','$ls_mes','$ls_anio','$li_bonvac','$li_bonfinanio','$li_comsue');\">".$ld_fecsue."</a></td>";
				print "<td>".$li_suebas."</td>";
				print "<td>".$li_sueint."</td>";
				print "<td>".$li_sueprodia."</td>";
				print "</tr>";		
				$rs_data->MoveNext();	
			}
			$io_sql->free_result($rs_data);
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Sueldos Hist&oacute;ricos</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Sueldos Hist&oacute;ricos </td>
    </tr>
  </table>
<br>
<br>
<?php
	$ls_codper=$_GET["codper"];
	uf_print($ls_codper);
?>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(ld_fecsue,li_suebas,li_sueint,li_sueprodia,ls_codded,ls_desded,ls_codtipper,ls_destipper,li_alibonvac,
				 li_alibonfinanio,li_otrasig,li_confij,li_convar,ls_estmodcont,ls_mes,ls_anio,li_bonvac,li_bonfinanio,li_comsue)
{
	opener.document.form1.cmbmes.value=ls_mes;
	opener.document.form1.cmbano.value=ls_anio;
	opener.document.form1.cmbmes.disabled=true;
	opener.document.form1.cmbano.disabled=true;
	opener.document.form1.txtfecsue.value=ld_fecsue;
	opener.document.form1.txtfecsue.readOnly=true;
    opener.document.form1.txtsuebas.value=li_suebas;
	opener.document.form1.txtsueint.value=li_sueint;
    opener.document.form1.txtsueprodia.value=li_sueprodia;
    opener.document.form1.txtcodded.value=ls_codded;
    opener.document.form1.txtdesded.value=ls_desded;
    opener.document.form1.txtcodtipper.value=ls_codtipper;
    opener.document.form1.txtdestipper.value=ls_destipper;
	opener.document.form1.txtbonvac.value=li_bonvac;
	opener.document.form1.txtbonfinanio.value=li_bonfinanio;
	opener.document.form1.txtotrasig.value=li_otrasig;
	opener.document.form1.txtconfij.value=li_confij;
	opener.document.form1.txtconvar.value=li_convar;
	opener.document.form1.txthidvalguar.value=ls_estmodcont;
	opener.document.form1.txtalibonvac.value=li_alibonvac;
	opener.document.form1.txtalibonfinanio.value=li_alibonfinanio;	
        opener.document.form1.txtcomsue.value=li_comsue;
	opener.document.form1.existe.value="TRUE";		
	close();
}
</script>
</html>
