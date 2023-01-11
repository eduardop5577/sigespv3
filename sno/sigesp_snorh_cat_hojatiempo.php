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
	require_once("sigesp_snorh_c_hojatiempo.php");
	$io_hojatiempo=new sigesp_snorh_c_hojatiempo();

   //--------------------------------------------------------------
   function uf_print($as_codper, $as_cedper, $as_nomper, $as_apeper, $as_tipo, $as_codnom)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_print
		//	Arguments:    as_codper  // Código de Personal
		//				  as_cedper  // Cédula de Pesonal
		//				  as_nomper  // Nombre de Personal
		//				  as_apeper // Apellido de Personal
		//				  as_tipo  // Tipo de Llamada del catálogo
		//				  ai_subnomina  // si tiene sub nómina=1 ó Nó =0
		//	Description:  Función que obtiene e imprime los resultados de la busqueda
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha=new class_fecha();		
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>Código</td>";
		print "<td width=40>Cédula</td>";
		print "<td width=280>Nombre y Apellido</td>";
		print "<td width=120>Nomina</td>";
		print "</tr>";
		$ls_sql="SELECT DISTINCT (sno_personalnomina.codper), sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, ".
				"		sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, ".
				"		sno_personalnomina.prouniadm, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper,  ".
				"		sno_unidadadmin.desuniadm, sno_nomina.desnom, sno_nomina.codnom, sno_periodo.fecdesper ".
				"  FROM sno_personalnomina, sno_personal, sno_unidadadmin, sno_nomina, sno_periodo  ".
				" WHERE sno_personalnomina.codemp = '".$ls_codemp."'".
				"   AND sno_nomina.codnom = '".$as_codnom."'".
			    "   AND sno_nomina.hojtienom = '1'".
				"   AND sno_personal.codper like '".$as_codper."' ".
				"   AND sno_personal.cedper like '".$as_cedper."' ".
				"   AND sno_personal.nomper like '".$as_nomper."' ".
				"   AND sno_personal.apeper like '".$as_apeper."' ".
				"   AND sno_personal.estper = '1' ".     
				"   AND sno_personalnomina.codemp = sno_personal.codemp ".
				"   AND sno_personalnomina.codper = sno_personal.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND sno_nomina.codemp=sno_periodo.codemp ".
				"   AND sno_nomina.codnom=sno_periodo.codnom ".
				"   AND sno_nomina.peractnom=sno_periodo.codperi ".
				" ORDER BY sno_personalnomina.codper ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_nomper=$rs_data->fields["nomper"]." ".$rs_data->fields["apeper"];
				$ls_coduniadm=$rs_data->fields["minorguniadm"]."-".$rs_data->fields["ofiuniadm"]."-".$rs_data->fields["uniuniadm"]."-".$rs_data->fields["depuniadm"]."-".$rs_data->fields["prouniadm"];			
				$ls_desuniadm=$rs_data->fields["desuniadm"];
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_desnom=$rs_data->fields["desnom"];
				$ld_fecdesper=$rs_data->fields["fecdesper"];
				switch ($as_tipo)
				{					
					case "hojatiempo": // el llamado se hace desde sigesp_sno_d_hojatiempo.php
						$ld_sueper=$rs_data->fields["sueper"];
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarhojatiempo('$ls_codper','$ls_cedper','$ls_nomper','$ls_desuniadm','$ls_codnom','$ls_desnom','$ld_fecdesper');\">".$ls_codper."</a></td>";
						print "<td>".$ls_cedper."</td>";
						print "<td>".$ls_nomper."</td>";
						print "<td>".$ls_desnom."</td>";
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
		unset($io_fecha);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Personal N&oacute;mina</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Personal N&oacute;mina </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
            <input name="txtcodper" type="text" id="txtcodper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">C&eacute;dula</div></td>
        <td><div align="left">
          <input name="txtcedper" type="text" id="txtcedper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtnomper" type="text" id="txtnomper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Apellido</div></td>
        <td><div align="left">
            <input name="txtapeper" type="text" id="txtapeper" size="30" maxlength="60" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nomina </div></td>
        <td><?php $io_hojatiempo->uf_cargarnomina($ls_codnom,$li_calculada); ?></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codper="%".$_POST["txtcodper"]."%";
		$ls_cedper="%".$_POST["txtcedper"]."%";
		$ls_nomper="%".$_POST["txtnomper"]."%";
		$ls_apeper="%".$_POST["txtapeper"]."%";
		$ls_codnom=$_POST["txtcodnom"];

		uf_print($ls_codper, $ls_cedper, $ls_nomper, $ls_apeper, $ls_tipo, $ls_codnom);
	}
	unset($io_fun_nomina);
?>
</div>
<input name="calculada" type="hidden" id="calculada" value="<?php print $li_calculada;?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function uf_cambiarnomina()
{
	f=document.form1;
	f.txtcodnom.value=f.cmbnomina.value;
	f.calculada.value=eval("document.form1.calculada"+f.txtcodnom.value+".value");
}


function aceptarhojatiempo(codper,cedper,nomper,desuniadm,codnom,desnom,fecdesper)
{
	opener.document.form1.txtcodper.value=codper;
	opener.document.form1.txtcodper.readOnly=true;
	opener.document.form1.txtcedper.value=cedper;
	opener.document.form1.txtcedper.readOnly=true;
    opener.document.form1.txtnomper.value=nomper;
	opener.document.form1.txtnomper.readOnly=true;
    opener.document.form1.txtuniad.value=desuniadm;
	opener.document.form1.txtuniad.readOnly=true;
    opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtcodnom.readOnly=true;
    opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtdesnom.readOnly=true;
    opener.document.form1.txtfecdesper.value=fecdesper;
	opener.document.form1.txtfecdesper.readOnly=true;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.action="sigesp_snorh_d_hojatiempo.php";
	opener.document.form1.submit();
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
  	if(f.txtcodnom.value=="")
	{
		alert('Debe Seleccionar una nómina');
	}
	else
	{
		f.operacion.value="BUSCAR";
		f.action="sigesp_snorh_cat_hojatiempo.php?tipo=<?PHP print $ls_tipo;?>";
		f.submit();	
	}
}


</script>
</html>