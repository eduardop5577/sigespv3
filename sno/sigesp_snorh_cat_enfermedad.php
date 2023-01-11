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
   function uf_print($as_codenf, $as_desenf, $as_tipo)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: 
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
		print "<td width=60>Codigo</td>";
		print "<td width=440>Descripcion</td>";
		print "</tr>";
		$ls_sql="SELECT codenf, desenf, enfcro, obsenf ".
				"  FROM sno_enfermedad ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codenf like '".$as_codenf."'".
				"   AND desenf like '".$as_desenf."'";
				
		        " ORDER BY codenf ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codenf=$rs_data->fields["codenf"];
				$ls_desenf=$rs_data->fields["desenf"];
				$ls_enfcro=$rs_data->fields["enfcro"];
				$ls_obsenf=$rs_data->fields["obsenf"];
				switch ($as_tipo)
				{
					case "": 
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codenf','$ls_desenf','$ls_enfcro','$ls_obsenf');\">".$ls_codenf."</a></td>";
						print "<td>".$ls_desenf."</td>";
						print "</tr>";	
					break;
					
					case "PERSONAL": 
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarpersonal('$ls_codenf','$ls_desenf');\">".$ls_codenf."</a></td>";
						print "<td>".$ls_desenf."</td>";
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
<title>Cat&aacute;logo de Enfermedad</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Enfermedad </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">Codigo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodenf" type="text" id="txtcodenf" size="30" maxlength="10"  onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtdesenf" type="text" id="txtdesenf" size="30" maxlength="120" onKeyPress="javascript: ue_mostrar(this,event);">
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
		$ls_codenf=$_POST["txtcodenf"];
		$ls_desenf="%".$_POST["txtdesenf"]."%";
		uf_print($ls_codenf, $ls_desenf, $ls_tipo);
	}
	else
	{
		$ls_codenf="%%";
		$ls_desenf="%%";
		uf_print($ls_codenf, $ls_desenf, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

function aceptar(codenf,desenf,enfcro,obsenf)
{
	opener.document.form1.txtcodenf.value=codenf;
	opener.document.form1.txtcodenf.readOnly=true;
	opener.document.form1.txtdesenf.value=desenf;
    opener.document.form1.txtobsenf.value=obsenf;
	opener.document.form1.chkenfcro.checked=false;
	if (enfcro=='1')
	{
		opener.document.form1.chkenfcro.checked=true;
	}
		
	opener.document.form1.existe.value="TRUE";	
	close();
}

function aceptarpersonal(codenf,desenf)
{
	opener.document.form1.txtcodenf.value=codenf;
	opener.document.form1.txtcodenf.readOnly=true;
	opener.document.form1.txtdesenf.value=desenf;
	opener.document.form1.txtdesenf.readOnly=true;
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
  	f.action="sigesp_snorh_cat_enfermedad.php";
  	f.submit();
}
</script>
</html>
