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
   function uf_print($ad_fecfer, $as_nomfer)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: ad_fecfer  // Fecha del Feriado
		//				   as_nomfer  // Descripci�n del Feriado
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
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
		print "<td width=60>Fecha</td>";
		print "<td width=440>Nombre</td>";
		print "</tr>";
		$ls_sql="SELECT fecfer, nomfer, tipfer, codpai, codest, codmun, codpar, ".
				"		(SELECT despai FROM sigesp_pais ".
				"		  WHERE sigesp_pais.codpai = sno_diaferiado.codpai ) AS despai, ".
				"		(SELECT desest FROM sigesp_estados ".
				"		  WHERE sigesp_estados.codpai = sno_diaferiado.codpai ".
				"			AND sigesp_estados.codest = sno_diaferiado.codest ) AS desest, ".
				"		(SELECT denmun FROM sigesp_municipio ".
				"		  WHERE sigesp_municipio.codpai = sno_diaferiado.codpai ".
				"			AND sigesp_municipio.codest = sno_diaferiado.codest ".
				"			AND sigesp_municipio.codmun = sno_diaferiado.codmun ) AS desmun, ".
				"		(SELECT denpar FROM sigesp_parroquia ".
				"		  WHERE sigesp_parroquia.codpai = sno_diaferiado.codpai ".
				"			AND sigesp_parroquia.codest = sno_diaferiado.codest ".
				"			AND sigesp_parroquia.codmun = sno_diaferiado.codmun ".
				"			AND sigesp_parroquia.codpar = sno_diaferiado.codpar ) AS despar ".
				"  FROM sno_diaferiado ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND nomfer like '".$as_nomfer."'";
		if($ad_fecfer!="")
		{
			$ad_fecfer = $io_funciones->uf_convertirdatetobd($ad_fecfer);
			$ls_sql=$ls_sql."   AND fecfer='".$ad_fecfer."'";
		}
		$ls_sql=$ls_sql." ORDER BY fecfer ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ld_fecfer=$io_funciones->uf_formatovalidofecha($rs_data->fields["fecfer"]);
				$ld_fecfer=$io_funciones->uf_convertirfecmostrar($ld_fecfer);				
				$ls_nomfer=$rs_data->fields["nomfer"];
				$ls_tipfer=$rs_data->fields["tipfer"];
				$ls_codpai=$rs_data->fields["codpai"];
				$ls_despai=$rs_data->fields["despai"];
				$ls_codest=$rs_data->fields["codest"];
				$ls_desest=$rs_data->fields["desest"];
				$ls_codmun=$rs_data->fields["codmun"];
				$ls_desmun=$rs_data->fields["desmun"];
				$ls_codpar=$rs_data->fields["codpar"];
				$ls_despar=$rs_data->fields["despar"];
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ld_fecfer','$ls_nomfer','$ls_tipfer','$ls_codpai','$ls_despai',";
				print "'$ls_codest','$ls_desest','$ls_codmun','$ls_desmun','$ls_codpar','$ls_despar');\">".$ld_fecfer."</a></td>";
				print "<td>".$ls_nomfer."</td>";
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
<title>Cat&aacute;logo de D&iacute;a Feriado</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de D&iacute;a Feriado</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">Fecha</div></td>
        <td width="431"><div align="left">
          <input name="txtfecfer" type="text" id="txtfecfer" size="30" maxlength="10"  onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" onKeyUp="javascript: ue_mostrar(this,event);" datepicker="true">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtnomfer" type="text" id="txtnomfer" size="30" maxlength="120" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
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
	if($ls_operacion=="BUSCAR")
	{
		$ld_fecfer=$_POST["txtfecfer"];
		$ls_nomfer="%".$_POST["txtnomfer"]."%";
		uf_print($ld_fecfer, $ls_nomfer);
	}
	else
	{
		$ld_fecfer="";
		$ls_nomfer="%%";
		uf_print($ld_fecfer, $ls_nomfer);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function aceptar(fecfer,nomfer,tipfer,codpai,despai,codest,desest,codmun,desmun,codpar,despar)
{
	opener.document.form1.txtfecfer.value=fecfer;
	opener.document.form1.txtfecfer.readOnly=true;
	opener.document.form1.txtnomfer.value=nomfer;
    opener.document.form1.cmbtipfer.value=tipfer;
    opener.document.form1.txtcodpai.value=codpai;
    opener.document.form1.txtdespai.value=despai;
    opener.document.form1.txtcodest.value=codest;
    opener.document.form1.txtdesest.value=desest;
    opener.document.form1.txtcodmun.value=codmun;
    opener.document.form1.txtdesmun.value=desmun;
    opener.document.form1.txtcodpar.value=codpar;
    opener.document.form1.txtdespar.value=despar;
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
  	f.action="sigesp_snorh_cat_diaferiado.php";
  	f.submit();
}
</script>
</html>
