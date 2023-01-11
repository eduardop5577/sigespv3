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
	$ls_nomestpro1=$_SESSION["la_empresa"]["nomestpro1"];		
	$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	switch($ls_modalidad)
	{
		case "1": // Modalidad por Proyecto
			$ls_titulo="Catalogo de Estructura Presupuestaria ".$ls_nomestpro1;
			break;
			
		case "2": // Modalidad por Presupuesto
			$ls_titulo="Catalogo de Estructura Programática ".$ls_nomestpro1;
			break;
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print($as_codestpro1, $as_denestpro1, $as_tipo)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	   Function: uf_print
		//	  Arguments: as_codestpro1  // Código de la estructura Programática
		//				 as_denestpro1 // Denominación de la estructura programática
		//				 as_tipo  // Tipo de Llamada del catálogo
		//	Description: Función que obtiene e imprime los resultados de la busqueda
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
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td>Código </td>";
		print "<td>Denominación</td>";
		print "<td>Tipo</td>";
		print "</tr>";
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
		$ls_sql="SELECT codestpro1, denestpro1,estcla ".
				"  FROM spg_ep1 ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codestpro1 like '".$as_codestpro1."' ".
				"   AND denestpro1 like '".$as_denestpro1."' ".
				" ORDER BY codestpro1 ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro1=$io_fun_nomina->uf_formato_programatica_detallado($li_len1,$ls_codestpro1);
				$ls_denestpro1=$rs_data->fields["denestpro1"];
				$ls_estcla=$rs_data->fields["estcla"];
				switch($ls_estcla)
				{
					case "P":
					$ls_estclatipo="PROYECTO";
				    break;
					
					case "A":
					$ls_estclatipo="ACCION";
				    break;
				}
				switch($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codestpro1','$ls_denestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
					break;
						
					case "asignacioncargo":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarasignacioncargo('$ls_codestpro1','$ls_denestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
					break;
					
					case "REPDES":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptardes('$ls_codestpro1','$ls_denestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
					break;
					
					case "REPHAS":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarhas('$ls_codestpro1','$ls_denestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
					break;
					
					case "config":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarconfig('$ls_codestpro1','$ls_estcla');\">".$ls_codestpro1."</a></td>";
						print "<td>".$ls_denestpro1."</td>";
						print "<td>".$ls_estclatipo."</td>";
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
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php print $ls_titulo; ?> </title>
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
     	 	<td width="500" height="20" colspan="2" class="titulo-ventana"><?php print $ls_titulo; ?>  </td>
    	</tr>
  </table>
	 <br>
	 <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">Codigo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodestpro1" type="text" id="txtcodestpro1" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Nombre</div></td>
        <td><div align="left">
          <input name="txtdenestpro1" type="text" id="txtdenestpro1" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
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
		$ls_codestpro1="%".$_POST["txtcodestpro1"]."%";
		$ls_denestpro1="%".$_POST["txtdenestpro1"]."%";
		uf_print($ls_codestpro1, $ls_denestpro1, $ls_tipo);
	}
	else
	{
		$ls_codestpro1="%%";
		$ls_denestpro1="%%";
		uf_print($ls_codestpro1, $ls_denestpro1, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codestpro1,deno,estcla)
{
	opener.document.form1.txtcodestpro1.value=codestpro1;
	opener.document.form1.txtdenestpro1.value=deno;
	opener.document.form1.txtestcla.value=estcla;
	opener.document.form1.txtcodestpro2.value="";
	opener.document.form1.txtdenestpro2.value="";
	opener.document.form1.txtcodestpro3.value="";
	opener.document.form1.txtdenestpro3.value="";
	opener.document.form1.txtcodestpro4.value="";
	opener.document.form1.txtdenestpro4.value="";
	opener.document.form1.txtcodestpro5.value="";
	opener.document.form1.txtdenestpro5.value="";
	close();
}

function aceptardes(codestpro1,deno,estcla)
{
	opener.document.form1.txtcodestpro1.value=codestpro1;
	opener.document.form1.txtdenestpro1.value=deno;
	opener.document.form1.txtestcla1.value=estcla;
	opener.document.form1.txtcodestpro2.value="";
	opener.document.form1.txtdenestpro2.value="";
	opener.document.form1.txtestcla2.value="";
	opener.document.form1.txtcodestpro3.value="";
	opener.document.form1.txtdenestpro3.value="";
	opener.document.form1.txtestcla3.value="";
	opener.document.form1.txtcodestpro4.value="";
	opener.document.form1.txtdenestpro4.value="";
	opener.document.form1.txtestcla4.value="";
	opener.document.form1.txtcodestpro5.value="";
	opener.document.form1.txtdenestpro5.value="";
	opener.document.form1.txtestcla5.value="";
	close();
}

function aceptarhas(codestpro1,deno,estcla)
{
	opener.document.form1.txtcodestpro6.value=codestpro1;
	opener.document.form1.txtdenestpro6.value=deno;
	opener.document.form1.txtestcla6.value=estcla;
	opener.document.form1.txtcodestpro7.value="";
	opener.document.form1.txtdenestpro7.value="";
	opener.document.form1.txtestcla7.value="";
	opener.document.form1.txtcodestpro8.value="";
	opener.document.form1.txtdenestpro8.value="";
	opener.document.form1.txtestcla8.value="";
	opener.document.form1.txtcodestpro9.value="";
	opener.document.form1.txtdenestpro9.value="";
	opener.document.form1.txtestcla9.value="";
	opener.document.form1.txtcodestpro10.value="";
	opener.document.form1.txtdenestpro10.value="";
	opener.document.form1.txtestcla10.value="";
	close();
}

function aceptarasignacioncargo(codestpro1,deno,estcla)
{
	opener.document.form1.txtcodestpro1.value=codestpro1;
	opener.document.form1.txtdenestpro1.value=deno;
	opener.document.form1.txtestcla.value=estcla;
	opener.document.form1.txtcodestpro2.value="";
	opener.document.form1.txtdenestpro2.value="";
	opener.document.form1.txtcodestpro3.value="";
	opener.document.form1.txtdenestpro3.value="";
	opener.document.form1.txtcodestpro4.value="";
	opener.document.form1.txtdenestpro4.value="";
	opener.document.form1.txtcodestpro5.value="";
	opener.document.form1.txtdenestpro5.value="";
	close();
}

function aceptarconfig(codestpro1,estcla)
{
	opener.document.form1.txtcodestpro1.value=codestpro1;
	opener.document.form1.txtestcla.value=estcla;
	opener.document.form1.txtcodestpro2.value="";
	opener.document.form1.txtcodestpro3.value="";
	opener.document.form1.txtcodestpro4.value="";
	opener.document.form1.txtcodestpro5.value="";
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
	f.action="sigesp_snorh_cat_estpre1.php?tipo=<?php print $ls_tipo;?>";
	f.submit();
}
</script>
</html>