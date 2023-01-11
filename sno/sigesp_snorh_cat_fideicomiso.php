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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
   //--------------------------------------------------------------
   function uf_print($as_anocurper, $as_mescurper, $as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_anocurper  // Año en Curso
		//				   as_mescurper  // Mes en Curso
		//				   as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 17/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
		print "<td width=60>Año</td>";
		print "<td width=60>Mes</td>";
		print "<td width=380>Nómina</td>";
		print "</tr>";
		$ls_sql="SELECT sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom, MAX(sno_periodo.fidconper) as fidconper ".
				"  FROM sno_fideiperiodo, sno_nomina, sno_periodo ".
				" WHERE sno_fideiperiodo.codemp='".$ls_codemp."' ".
				"   AND sno_fideiperiodo.anocurper like '".$as_anocurper."' ".
				"   AND sno_fideiperiodo.mescurper =".$as_mescurper." ".
				"   AND SUBSTR(cast(sno_periodo.fecdesper as char(10)),6,2)='".str_pad($as_mescurper,2,"0",0)."' ".			
				"   AND sno_fideiperiodo.codemp=sno_nomina.codemp ".
				"   AND sno_fideiperiodo.codnom=sno_nomina.codnom ".
				"   AND sno_nomina.codemp=sno_periodo.codemp ".
				"   AND sno_nomina.codnom=sno_periodo.codnom ".
				" GROUP BY sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom ".
				" ORDER BY sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom ";
		if (($as_tipo=="garantia"))
		{
			$ls_sql="SELECT sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom, MAX(sno_periodo.fidconper) as fidconper ".
					"  FROM sno_fideiperiodo, sno_nomina, sno_periodo ".
					" WHERE sno_fideiperiodo.codemp='".$ls_codemp."' ".
					"   AND sno_fideiperiodo.anocurper like '".$as_anocurper."' ".
					"   AND sno_fideiperiodo.mescurper =".$as_mescurper." ".
					"   AND SUBSTR(cast(sno_periodo.fecdesper as char(10)),6,2)='".str_pad($as_mescurper,2,"0",0)."' ".			
					"   AND sno_periodo.cerper= 1 ".
					"   AND sno_fideiperiodo.anocurper||sno_fideiperiodo.mescurper >='201205' ".
					"   AND sno_fideiperiodo.codemp=sno_nomina.codemp ".
					"   AND sno_fideiperiodo.codnom=sno_nomina.codnom ".
					"   AND sno_nomina.codemp=sno_periodo.codemp ".
					"   AND sno_nomina.codnom=sno_periodo.codnom ".
					" GROUP BY sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom ".
					" ORDER BY sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom ";
		}
		if (($as_tipo=="intereses"))
		{
			$ls_sql="SELECT sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom, MAX(sno_periodo.fidintconper) as fidconper ".
					"  FROM sno_fideiperiodo, sno_nomina, sno_periodo ".
					" WHERE sno_fideiperiodo.codemp='".$ls_codemp."' ".
					"   AND sno_fideiperiodo.anocurper like '".$as_anocurper."' ".
					"   AND sno_fideiperiodo.mescurper =".$as_mescurper." ".
					"   AND SUBSTR(cast(sno_periodo.fecdesper as char(10)),6,2)='".str_pad($as_mescurper,2,"0",0)."' ".			
					"   AND sno_fideiperiodo.codemp=sno_nomina.codemp ".
					"   AND sno_fideiperiodo.codnom=sno_nomina.codnom ".
					"   AND sno_nomina.codemp=sno_periodo.codemp ".
					"   AND sno_nomina.codnom=sno_periodo.codnom ".
					" GROUP BY sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_nomina.codnom, sno_nomina.desnom ".
					" UNION ".
					"SELECT SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),1,4), cast(SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),6,2) as int), sno_nomina.codnom, sno_nomina.desnom, MAX(sno_periodo.fidintconper) as fidconper ".
					"  FROM sno_deudaanterior, sno_personalnomina, sno_nomina, sno_periodo ".
					" WHERE sno_deudaanterior.codemp='".$ls_codemp."' ".
					"   AND SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),1,4) = '".$as_anocurper."' ".
					"   AND SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),6,2) = '".str_pad($as_mescurper,2,"0",0)."' ".
					"   AND sno_nomina.espnom='0' ".
					"   AND sno_deudaanterior.codemp=sno_personalnomina.codemp  ".
					"   AND sno_deudaanterior.codper=sno_personalnomina.codper ".
					"   AND sno_personalnomina.codemp=sno_nomina.codemp ". 
					"   AND sno_personalnomina.codnom=sno_nomina.codnom ".
					"   AND sno_nomina.codemp=sno_periodo.codemp ".
					"   AND sno_nomina.codnom=sno_periodo.codnom ".
					" GROUP BY SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),1,4), SUBSTR(cast(sno_deudaanterior.feccordeu as char(10)),6,2), sno_nomina.codnom, sno_nomina.desnom  ".
					" ORDER BY anocurper, mescurper, codnom ";
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
				$ls_anocurper=$rs_data->fields["anocurper"];
				$ls_mescurper=str_pad($rs_data->fields["mescurper"],2,"0",0);
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_desnom=$rs_data->fields["desnom"];
				$li_fidconper=$rs_data->fields["fidconper"];
				switch($ls_mescurper)
				{
					case "01":
						$ls_desmes="ENERO";
						break;

					case "02":
						$ls_desmes="FEBRERO";
						break;

					case "03":
						$ls_desmes="MARZO";
						break;

					case "04":
						$ls_desmes="ABRIL";
						break;

					case "05":
						$ls_desmes="MAYO";
						break;

					case "06":
						$ls_desmes="JUNIO";
						break;

					case "07":
						$ls_desmes="JULIO";
						break;

					case "08":
						$ls_desmes="AGOSTO";
						break;

					case "09":
						$ls_desmes="SEPTIEMBRE";
						break;

					case "10":
						$ls_desmes="OCTUBRE";
						break;

					case "11":
						$ls_desmes="NOVIEMBRE";
						break;

					case "12":
						$ls_desmes="DICIEMBRE";
						break;
				}
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_anocurper','$ls_mescurper','$ls_desmes','$ls_codnom','$li_fidconper');\">".$ls_anocurper."</a></td>";
						print "<td>".$ls_desmes."</td>";
						print "<td>".$ls_desnom."</td>";
						print "</tr>";			
						break;

					case "intereses":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarintereses('$ls_anocurper','$ls_mescurper','$ls_codnom','$ls_desmes');\">".$ls_anocurper."</a></td>";
						print "<td>".$ls_desmes."</td>";
						print "<td>".$ls_desnom."</td>";
						print "</tr>";			
						break;
					
					case "garantia":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_garantia('$ls_anocurper','$ls_mescurper','$ls_desmes','$ls_codnom','$li_fidconper');\">".$ls_anocurper."</a></td>";
						print "<td>".$ls_desmes."</td>";
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
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Prestaci&oacute;n Antiguedad</title>
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
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Prestaci&oacute;n Antiguedad <?php  if ($ls_tipo=='intereses'){ print " / Deuda Anterior";} ?></td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">A&ntilde;o</div></td>
        <td width="431"><div align="left">
          <input name="txtanocurper" type="text" id="txtanocurper" size="30" maxlength="4" onKeyUp="javascript: ue_validarnumero(this);" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Mes</div></td>
        <td><div align="left">
          <select name="txtmescurper">
            <option value="01" selected>ENERO</option>
            <option value="02">FEBRERO</option>
            <option value="03">MARZO</option>
            <option value="04">ABRIL</option>
            <option value="05">MAYO</option>
            <option value="06">JUNIO</option>
            <option value="07">JULIO</option>
            <option value="08">AGOSTO</option>
            <option value="09">SEPTIEMBRE</option>
            <option value="10">OCTUBRE</option>
            <option value="11">NOVIEMBRE</option>
            <option value="12">DICIEMBRE</option>
          </select>
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	if($ls_operacion=="BUSCAR")
	{
		$ls_anocurper=$_POST["txtanocurper"];
		$ls_mescurper=$_POST["txtmescurper"];
		if (trim($ls_anocurper)=='')
		{
			$ls_anocurper = date('Y');
		}
		uf_print($ls_anocurper, $ls_mescurper, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(anocurper,mescurper,desmes,codnom,fidconper)
{
	var largo= opener.document.form1.txtnominas.length;

	opener.document.form1.txtanocurper.value=anocurper;
	opener.document.form1.txtanocurper.readOnly=true;
	opener.document.form1.txtmescurper.value=mescurper;
	opener.document.form1.txtmescurper.readOnly=true;
	opener.document.form1.fidconper.value=fidconper;
	opener.document.form1.txtdesmesper.value=desmes;
	opener.document.form1.txtdesmesper.readOnly=true;
	for(i=0;i<largo;i++)
	{
		if (opener.document.form1.txtnominas.options[i].value==codnom) 
		{
			opener.document.form1.txtnominas.options[i].selected=true;
			i=largo;
		}
	}
	opener.document.images["meses"].style.visibility="hidden";
	opener.document.form1.existe.value="TRUE";
	opener.document.form1.operacion.value="BUSCAR";
	opener.document.form1.action="sigesp_snorh_p_fideicomiso.php";
	opener.document.form1.submit();	
	close();
}

function aceptarintereses(anocurper,mescurper,codnom,desmes)
{
	var largo= opener.document.form1.txtnominas.length;

	opener.document.form1.txtanocurper.value=anocurper;
	opener.document.form1.txtanocurper.readOnly=true;
	opener.document.form1.txtmescurper.value=mescurper;
	opener.document.form1.txtmescurper.readOnly=true;
	opener.document.form1.txtdesmesper.value=desmes;
	opener.document.form1.txtdesmesper.readOnly=true;
	for(i=0;i<largo;i++)
	{
		if (opener.document.form1.txtnominas.options[i].value==codnom) 
		{
			opener.document.form1.txtnominas.options[i].selected=true;
			i=largo;
		}
	}
	opener.document.form1.operacion.value="CARGARNOMINAS";
	opener.document.form1.action="sigesp_snorh_p_fideicomiso_intereses.php";
	opener.document.form1.submit();	
	close();
}

function aceptar_garantia(anocurper,mescurper,desmes,codnom,fidconper)
{
	var largo= opener.document.form1.txtnominas.length;

	opener.document.form1.txtanocurper.value=anocurper;
	opener.document.form1.txtanocurper.readOnly=true;
	opener.document.form1.txtmescurper.value=mescurper;
	opener.document.form1.txtmescurper.readOnly=true;
	opener.document.form1.fidconper.value=fidconper;
	opener.document.form1.txtdesmesper.value=desmes;
	opener.document.form1.txtdesmesper.readOnly=true;
	for(i=0;i<largo;i++)
	{
		if (opener.document.form1.txtnominas.options[i].value==codnom) 
		{
			opener.document.form1.txtnominas.options[i].selected=true;
			i=largo;
		}
	}
	opener.document.images["meses"].style.visibility="hidden";
	opener.document.form1.existe.value="TRUE";
	opener.document.form1.operacion.value="BUSCAR";
	opener.document.form1.action="sigesp_snorh_p_fideicomiso_garantia.php";
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

function ue_search(existe)
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_snorh_cat_fideicomiso.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
