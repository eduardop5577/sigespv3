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
   function uf_print($as_tipo,$as_codnom)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function : uf_print
		//		   Access : public 
		//	    Arguments : as_tipo  // Tipo de Llamada del catálogo
		//	                as_codnom  // Código de Nómina
		//	  Description : Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por : Ing. Yesenia Moreno
		// Fecha Creación : 25/10/2006 								Fecha Última Modificación : 
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
		print "<td width=100>Período</td>";
		print "<td width=200>Fecha de Inicio</td>";
		print "<td width=200>Fecha de Finalización</td>";
		print "</tr>";
		if(($as_tipo=="aprobar")||($as_tipo=="reversar"))
		{
			switch($as_tipo)
			{
				case "aprobar":
					$ls_status=0;
					break;
				case "reversar":
					$ls_status=1;
					break;
			}
			$ls_sql="SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
					"  FROM sno_periodo, sno_dt_scg ".
					" WHERE sno_periodo.cerper = 1 ";
			if($as_codnom!="")
			{
				$ls_sql=$ls_sql." AND sno_periodo.codnom = '".$as_codnom."' ";
			}		
					 
			$ls_sql=$ls_sql."	AND sno_dt_scg.estatus = ".$ls_status." ".
							"   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$ls_codemp."' )  ".
							"   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spi WHERE codemp = '".$ls_codemp."' )  ".
							"   AND sno_periodo.codemp = sno_dt_scg.codemp ".
							"   AND sno_periodo.codnom = sno_dt_scg.codnom ".
							"   AND sno_periodo.codperi = sno_dt_scg.codperi ".
							" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
							" UNION ".
							"SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
							"  FROM sno_periodo, sno_dt_spg ".
							" WHERE sno_periodo.cerper = 1 ";
			if($as_codnom!="")
			{
				$ls_sql=$ls_sql." AND sno_periodo.codnom = '".$as_codnom."' ";
			}		
					 
			$ls_sql=$ls_sql."	AND sno_dt_spg.estatus = ".$ls_status." ".
							"   AND sno_periodo.codemp = sno_dt_spg.codemp ".
							"   AND sno_periodo.codnom = sno_dt_spg.codnom ".
							"   AND sno_periodo.codperi = sno_dt_spg.codperi ".
							" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
							" UNION ".
							"SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
							"  FROM sno_periodo, sno_dt_spi ".
							" WHERE sno_periodo.cerper = 1 ";
			if($as_codnom!="")
			{
				$ls_sql=$ls_sql." AND sno_periodo.codnom = '".$as_codnom."' ";
			}		
					 
			$ls_sql=$ls_sql."	AND sno_dt_spi.estatus = ".$ls_status." ".
							"   AND sno_periodo.codemp = sno_dt_spi.codemp ".
							"   AND sno_periodo.codnom = sno_dt_spi.codnom ".
							"   AND sno_periodo.codperi = sno_dt_spi.codperi ".
							" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
							" ORDER BY codperi ";
		}
		if(($as_tipo=="repconconapodes")||($as_tipo=="repconconapohas"))
		{
			$ls_sql="SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
					"  FROM sno_periodo ".
					" WHERE sno_periodo.cerper = 1 ".
					"   AND sno_periodo.codnom='".$as_codnom."' ".
					" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
					" ORDER BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ";
		}
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codperi=$rs_data->fields["codperi"];
				$ld_fecdesper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecdesper"]);
				$ld_fechasper=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fechasper"]);
				switch ($as_tipo)
				{
					case "aprobar": // sigesp_mis_p_contabiliza_sno
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codperi','$ld_fecdesper','$ld_fechasper');\">".$ls_codperi."</a></td>";
						print "<td>".$ld_fecdesper."</td>";
						print "<td>".$ld_fechasper."</td>";
						print "</tr>";			
						break;
						
					case "reversar": // sigesp_mis_p_reverso_sno
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codperi','$ld_fecdesper','$ld_fechasper');\">".$ls_codperi."</a></td>";
						print "<td>".$ld_fecdesper."</td>";
						print "<td>".$ld_fechasper."</td>";
						print "</tr>";			
						break;
							
					case "repconconapodes":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepconconapodes('$ls_codperi','$ld_fecdesper','$ld_fechasper');\">".$ls_codperi."</a></td>";
						print "<td>".$ld_fecdesper."</td>";
						print "<td>".$ld_fechasper."</td>";
						print "</tr>";			
						break;

					case "repconconapohas":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarrepconconapohas('$ls_codperi','$ld_fecdesper','$ld_fechasper');\">".$ls_codperi."</a></td>";
						print "<td>".$ld_fecdesper."</td>";
						print "<td>".$ld_fechasper."</td>";
						print "</tr>";			
						break;
				}
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
		unset($ls_codnom);
		unset($ld_peractnom);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Per&iacute;odos</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Per&iacute;odos </td>
    </tr>
  </table>
<br>
<br>
<?PHP
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_tipo=='')
	{
		$ls_tipo=$io_fun_nomina->uf_obtenervalor_get("tipo","");
	}
	$ls_codnom=$io_fun_nomina->uf_obtenervalor_get("codnom","");
	uf_print($ls_tipo,$ls_codnom);
	unset($io_fun_nomina);
?>
</div>
</form>
</body>
<script >
function aceptar(codperi,fecdesper,fechasper)
{
	opener.document.form1.txtcodperi.value=codperi;
	opener.document.form1.txtcodperi.readOnly=true;
    opener.document.form1.txtfecper.value=fecdesper+" - "+fechasper;
	opener.document.form1.txtfecper.readOnly=true;
	close();
}
function aceptarrepconconapodes(codperi,fecdesper,fechasper)
{
	opener.document.form1.txtfecdesperdes.value=fecdesper;
	opener.document.form1.txtfecdesperdes.readOnly=true;
	opener.document.form1.txtfechasperdes.value=fechasper;
	opener.document.form1.txtfechasperdes.readOnly=true;
	opener.document.form1.txtcodperides.value=codperi;
	opener.document.form1.txtcodperides.readOnly=true;
	close();
}
function aceptarrepconconapohas(codperi,fecdesper,fechasper)
{
	opener.document.form1.txtfecdesperhas.value=fecdesper;
	opener.document.form1.txtfecdesperhas.readOnly=true;
	opener.document.form1.txtfechasperhas.value=fechasper;
	opener.document.form1.txtfechasperhas.readOnly=true;
	opener.document.form1.txtcodperihas.value=codperi;
	opener.document.form1.txtcodperihas.readOnly=true;
	close();
}
</script>
</html>