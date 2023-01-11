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
   function uf_print($as_codhor, $as_denhor, $as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codhor  // Código de Horario
		//				   as_denhor  // Descripción del horario
		//				   as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
		print "<td width=60>Código</td>";
		print "<td width=440>Descripción</td>";
		print "</tr>";
		$ls_sql="SELECT codemp,codhor,denhor,tiphor,horini,horfin,horlab,hordes ".
				"  FROM sno_horario ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codhor like '".$as_codhor."' AND denhor like '".$as_denhor."'".
				" ORDER BY codhor ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codhor=$rs_data->fields["codhor"];
				$ls_denhor=$rs_data->fields["denhor"];
				$ls_tiphor=$rs_data->fields["tiphor"];
				$ls_horini=$rs_data->fields["horini"];
				$ls_horfin=$rs_data->fields["horfin"];
				$li_horlab=$rs_data->fields["horlab"];
				$li_hordes=$rs_data->fields["hordes"];
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codhor','$ls_denhor','$ls_tiphor','$ls_horini','$ls_horfin','$li_horlab','$li_hordes');\">".$ls_codhor."</a></td>";
						print "<td>".$ls_denhor."</td>";
						print "</tr>";			
						break;
					
					case "hojatiempo": 
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarhojatiempo('$ls_codhor','$ls_denhor','$li_horlab');\">".$ls_codhor."</a></td>";
						print "<td>".$ls_denhor."</td>";
						print "</tr>";			
						break;
					
					case "lishojtiedes": 
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarlishojtiedes('$ls_codhor');\">".$ls_codhor."</a></td>";
						print "<td>".$ls_denhor."</td>";
						print "</tr>";			
						break;
					
					case "lishojtiehas": 
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarlishojtiehas('$ls_codhor');\">".$ls_codhor."</a></td>";
						print "<td>".$ls_denhor."</td>";
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
<title>Cat&aacute;logo de Horario</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Horario </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodhor" type="text" id="txtcodhor" size="30" maxlength="3" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdenhor" type="text" id="txtdenhor" size="30" maxlength="120" onKeyPress="javascript: ue_mostrar(this,event);">
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
		$ls_codhor="%".$_POST["txtcodhor"]."%";
		$ls_denhor="%".$_POST["txtdenhor"]."%";
		uf_print($ls_codhor, $ls_denhor, $ls_tipo);
	}
	else
	{
		$ls_codhor="%%";
		$ls_denhor="%%";
		uf_print($ls_codhor, $ls_denhor, $ls_tipo);
	}	
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codigo,descripcion,tiphor,horini,horfin,horlab,hordes)
{
	opener.document.form1.txtcodhor.value=codigo;
	opener.document.form1.txtcodhor.readOnly=true;
    opener.document.form1.txtdenhor.value=descripcion;
    opener.document.form1.cmbtiphor.value=tiphor;
    opener.document.form1.txthorini.value=horini;
    opener.document.form1.txthorfin.value=horfin;
    opener.document.form1.txthorlab.value=horlab;
    opener.document.form1.txthordes.value=hordes;
	opener.document.form1.existe.value="TRUE";
	close();
}

function aceptarhojatiempo(codigo,descripcion,horlab)
{
	filaactual=opener.document.form1.filaactual.value;
	eval("opener.document.form1.txtcodhor"+filaactual+".value='"+codigo+"';");
	eval("opener.document.form1.txtdenhor"+filaactual+".value='"+descripcion+"';");
	eval("opener.document.form1.txthorlab"+filaactual+".value="+horlab+";");
	eval("opener.document.form1.txthorextlab"+filaactual+".value=0;");
	close();
}

function aceptarlishojtiedes(codigo)
{
	opener.document.form1.txtcodhordes.value=codigo;
	opener.document.form1.txtcodhordes.readOnly=true;
	opener.document.form1.txtcodhorhas.value="";
	close();
}

function aceptarlishojtiehas(codigo)
{
	opener.document.form1.txtcodhorhas.value=codigo;
	opener.document.form1.txtcodhorhas.readOnly=true;
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
  	f.action="sigesp_snorh_cat_horario.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
