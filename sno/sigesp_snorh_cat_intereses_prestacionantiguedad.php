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
   function uf_print($as_mesint, $as_anoint, $as_nrogacint, $as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_mesint  // Mes
		//				   as_anoint  // año
		//				   as_nrogacint  // Gaceta
		//				   as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/02/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=100>Año</td>";
		print "<td width=100>Mes</td>";
		print "<td width=100>Gaceta</td>";
		print "<td width=100>Fecha</td>";
		print "<td width=100>Monto</td>";
		print "</tr>";
		$ls_sqlaux="";
		if($as_mesint!="")
		{
			$ls_sqlaux="   AND mesint =".$as_mesint." ";
		}
		$ls_sql="SELECT codemp, mesint, anoint, nrogacint, fecviggacint, montasint ".
				"  FROM sno_fideiintereses ".
				" WHERE codemp='".$ls_codemp."'".
				$ls_sqlaux.
				"   AND anoint like '".$as_anoint."'".
				"   AND nrogacint like '".$as_nrogacint."'".
				" ORDER BY anoint, mesint ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_anoint=$rs_data->fields["anoint"];
				$ls_mesint=$rs_data->fields["mesint"];
				$ls_nrogacint=$rs_data->fields["nrogacint"];
				$ld_fecviggacint=$io_funciones->uf_formatovalidofecha($rs_data->fields["fecviggacint"]);
				$ld_fecviggacint=$io_funciones->uf_convertirfecmostrar($ld_fecviggacint);
				$li_montasint=$io_fun_nomina->uf_formatonumerico($rs_data->fields["montasint"]);
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_anoint','$ls_mesint','$ls_nrogacint','$ld_fecviggacint','$li_montasint');\">".$ls_anoint."</a></td>";
						print "<td>".$ls_mesint."</td>";
						print "<td>".$ls_nrogacint."</td>";
						print "<td>".$ld_fecviggacint."</td>";
						print "<td>".$li_montasint."</td>";
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
<title>Cat&aacute;logo de Intereses Prestaci&oacute;n Antiguedad</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Intereses Prestaci&oacute;n Antiguedad </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">Mes</div></td>
        <td width="431"><div align="left">
          <select name="cmbmesint" id="cmbmesint">
            <option value="1" selected>Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">A&ntilde;o</div></td>
        <td><select name="cmbanoint" id="cmbanoint">
        </select></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Gaceta</div></td>
        <td><div align="left">
			<input name="txtnrogacint" type="text" id="txtnrogacint" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">				
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
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_operacion=="BUSCAR")
	{
		$ls_mesint=$_POST["cmbmesint"];
		$ls_anoint="%".$_POST["cmbanoint"]."%";
		$ls_nrogacint="%".$_POST["txtnrogacint"]."%";
		uf_print($ls_mesint, $ls_anoint, $ls_nrogacint, $ls_tipo);
	}
	else
	{
		$ls_mesint="";
		$ls_anoint="%%";
		$ls_nrogacint="%%";
		uf_print($ls_mesint, $ls_anoint, $ls_nrogacint, $ls_tipo);
	}	
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
f=document.form1;
f.cmbanoint.length=0;
var fecha = new Date();
actual = fecha.getFullYear();
i=0;
for(inicio=1970;inicio<=actual;inicio++)
{
	f.cmbanoint.options[i]= new Option(inicio,inicio);
	i++;
}
function aceptar(anoint,mesint,nrogacint,fecviggacint,montasint)
{
	opener.document.form1.txtmesint.value=mesint;
	opener.document.form1.cmbmesint.value=mesint;
	opener.document.form1.cmbmesint.disabled=true;
	opener.document.form1.txtanoint.value=anoint;
	opener.document.form1.cmbanoint.value=anoint;
	opener.document.form1.cmbanoint.disabled=true;
    opener.document.form1.txtnrogacint.value=nrogacint;
    opener.document.form1.txtfecviggacint.value=fecviggacint;
    opener.document.form1.txtmontasint.value=montasint;
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

function ue_search(existe)
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_snorh_cat_intereses_prestacionantiguedad.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
