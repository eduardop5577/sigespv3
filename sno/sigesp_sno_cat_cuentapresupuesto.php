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
   function uf_print($as_spg_cuenta, $as_tipo, $ai_nro)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_spg_cuenta  // Código de cuenta
		//				   as_tipo  // Tipo de Llamada del catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;

		$li_len1=0;
		$li_len2=0;
		$li_len3=0;
		$li_len4=0;
		$li_len5=0;
		$ls_titulo="";
		$arrResultado=$io_fun_nomina->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado['ai_len1'];
		$li_len2=$arrResultado['ai_len2'];
		$li_len3=$arrResultado['ai_len3'];
		$li_len4=$arrResultado['ai_len4'];
		$li_len5=$arrResultado['ai_len5'];
		$ls_titulo=$arrResultado['as_titulo'];
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
		print "<td width=100>Código</td>";
		print "<td width=400>Denominación</td>";
		print "<td width=220>".$ls_titulo."</td>";
		print "</tr>";
		$ls_sql="SELECT spg_cuenta, MAX(denominacion) AS denominacion, MAX(codestpro1) AS codestpro1, MAX(codestpro2) AS codestpro2, ".
				"       MAX(codestpro3) AS codestpro3, MAX(codestpro4) AS codestpro4, MAX(codestpro5) AS codestpro5 ".
				"  FROM spg_cuentas ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND status='C' ";
		$la_spg_cuenta=explode(",",$as_spg_cuenta);
		$li_total=count((Array)$la_spg_cuenta);
		for($li_i=0;$li_i<$li_total;$li_i++)
		{
			if($li_i==0)
			{
				$ls_sql=$ls_sql."   AND spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
			}
			else
			{
				$ls_sql=$ls_sql."    OR spg_cuenta like '".$la_spg_cuenta[$li_i]."%'";
			}
		
		}
		$ls_sql=$ls_sql." GROUP BY spg_cuenta ";
		$ls_sql=$ls_sql." ORDER BY spg_cuenta ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
				$ls_codest1=$row["codestpro1"];
				$ls_codest2=$row["codestpro2"];
				$ls_codest3=$row["codestpro3"];
				$ls_codest4=$row["codestpro4"];
				$ls_codest5=$row["codestpro5"];
				$ls_programatica=$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5;
				$ls_programatica=$io_fun_nomina->uf_formatoprogramatica($ls_programatica,$ls_programatica);
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_spg_cuenta','$ls_denominacion');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
	
					case "PATRONAL":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarpatronal('$ls_spg_cuenta','$ls_denominacion');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
	
					case "FIDEICOMISO":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarfideicomiso('$ls_spg_cuenta');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
					
					case "per":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarper('$ls_spg_cuenta');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
					
					case "obr":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarobr('$ls_spg_cuenta');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;	
					
					case "percon":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarpercon('$ls_spg_cuenta');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
					
					case "obrcon":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarobrcon('$ls_spg_cuenta');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;			
	
					case "LOTE":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarlote('$ls_spg_cuenta','$ai_nro');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
	
					case "PATRONALLOTE":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarpatronallote('$ls_spg_cuenta','$ai_nro');\">".$ls_spg_cuenta."</a></td>";
						print "<td>".$ls_denominacion."</td>";
						print "<td>".$ls_programatica."</td>";
						print "</tr>";			
						break;
				}
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
<title>Cat&aacute;logo de Cuentas</title>
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
  <table width="500" height="20" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="496" colspan="2" class="titulo-ventana">Cat&aacute;logo de Cuentas</td>
    </tr>
  </table>
<br>
    <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	$ls_spg_cuenta=$io_fun_nomina->uf_obtenervalor_get("spg_cuenta","");
	$li_nro=$io_fun_nomina->uf_obtenervalor_get("nro","");
	uf_print($ls_spg_cuenta, $ls_tipo, $li_nro);
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(spg_cuenta,denominacion)
{
	opener.document.form1.txtcuepre.value=spg_cuenta;
    opener.document.form1.txtdencuepre.value=denominacion;
	close();
}

function aceptarpatronal(spg_cuenta,denominacion)
{
	opener.document.form1.txtcueprepat.value=spg_cuenta;
    opener.document.form1.txtdencueprepat.value=denominacion;
	close();
}

function aceptarfideicomiso(spg_cuenta)
{
	opener.document.form1.txtcueprefid.value=spg_cuenta;
	close();
}

function aceptarper(spg_cuenta)
{
	opener.document.form1.txtctaguarper.value=spg_cuenta;
	close();
}
function aceptarobr(spg_cuenta)
{
	opener.document.form1.txtctaguarobr.value=spg_cuenta;
	close();
}
function aceptarpercon(spg_cuenta)
{
	opener.document.form1.txtctaguarpercon.value=spg_cuenta;
	close();
}
function aceptarobrcon(spg_cuenta)
{
	opener.document.form1.txtctaguarobrcon.value=spg_cuenta;
	close();
}

function aceptarlote(spg_cuenta,nro)
{
	eval("opener.document.form1.txtcuepre"+nro+".value='"+spg_cuenta+"';");
	close();
}

function aceptarpatronallote(spg_cuenta,nro)
{
	eval("opener.document.form1.txtcueprepatcon"+nro+".value='"+spg_cuenta+"';");
	close();
}

</script>
</html>