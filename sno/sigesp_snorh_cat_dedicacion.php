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
   function uf_print($as_codded, $as_desded,$as_tipo)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codded  // C?digo de Dedicaci?n
		//				   as_desde  // Descripci?n de la dedicaci?n
		//				   as_tipo  // Verifica de donde se est? llamando el cat?logo
		//	  Description: Funci?n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
		print "<td width=60>C?digo</td>";
		print "<td width=440>Descripci?n</td>";
		print "</tr>";
		$ls_sql="SELECT codded, desded ".
				"  FROM sno_dedicacion ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codded<>'000'".
				"   AND codded like '".$as_codded."' AND desded like '".$as_desded."'".
				" ORDER BY codded ";
				
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codded=$row["codded"];
				$ls_desded=$row["desded"];
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codded','$ls_desded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;
						
					case "tipo":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptartipo('$ls_codded','$ls_desded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;
	
					case "asignacion":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarasignacion('$ls_codded','$ls_desded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;

					case "fideiconfigurable":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarfideiconfigurable('$ls_codded','$ls_desded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;

					case "trabajoant":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptartrabajoant('$ls_codded','$ls_desded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;

					case "reppagunides":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreppagunides('$ls_codded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
						print "</tr>";			
						break;

					case "reppagunihas":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarreppagunihas('$ls_codded');\">".$ls_codded."</a></td>";
						print "<td>".$ls_desded."</td>";
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
<title>Cat&aacute;logo de Dedicaci&oacute;n</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Dedicaci&oacute;n </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodded" type="text" id="txtcodded" size="30" maxlength="3" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdesded" type="text" id="txtdesded" size="30" maxlength="100" onKeyPress="javascript: ue_mostrar(this,event);">
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
		$ls_codded="%".$_POST["txtcodded"]."%";
		$ls_desded="%".$_POST["txtdesded"]."%";
		uf_print($ls_codded, $ls_desded, $ls_tipo);
	}
	else
	{
		$ls_codded="%%";
		$ls_desded="%%";
		uf_print($ls_codded, $ls_desded, $ls_tipo);
	}

	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codded,desded)
{
	opener.document.form1.txtcodded.value=codded;
	opener.document.form1.txtcodded.readOnly=true;
    opener.document.form1.txtdesded.value=desded;
	opener.document.form1.existe.value="TRUE";
	opener.document.form1.btntipopersonal.disabled=false;	
	close();
}
function aceptartipo(codded,desded)
{
	opener.document.form1.txtcodded.value=codded;
	opener.document.form1.txtcodded.readOnly=true;
    opener.document.form1.txtdesded.value=desded;
	opener.document.form1.txtdesded.readOnly=true;
	close();
}
function aceptarasignacion(codded,desded)
{
	opener.document.form1.txtcodded.value=codded;
	opener.document.form1.txtcodded.readOnly=true;
    opener.document.form1.txtdesded.value=desded;
	opener.document.form1.txtdesded.readOnly=true;
	opener.document.form1.txtcodtipper.value="";
	opener.document.form1.txtdestipper.value="";
	close();
}

function aceptarfideiconfigurable(codded,desded)
{
	opener.document.form1.txtcodded.value=codded;
	opener.document.form1.txtcodded.readOnly=true;
    opener.document.form1.txtdesded.value=desded;
	opener.document.form1.txtdesded.readOnly=true;
	opener.document.form1.txtcodtipper.value="";
	opener.document.form1.txtdestipper.value="";
	close();
}

function aceptartrabajoant(codded,desded)
{
	opener.document.form1.txtcodded.value=codded;
	opener.document.form1.txtcodded.readOnly=true;
    opener.document.form1.txtdesded.value=desded;
	opener.document.form1.txtdesded.readOnly=true;
	close();
}

function aceptarreppagunides(codded)
{
	opener.document.form1.txtcoddeddes.value=codded;
	opener.document.form1.txtcoddeddes.readOnly=true;
    opener.document.form1.txtcoddedhas.value='';
	opener.document.form1.txtcoddedhas.readOnly=true;
	close();
}

function aceptarreppagunihas(codded)
{
	if(opener.document.form1.txtcoddeddes.value<=codded)
	{
		opener.document.form1.txtcoddedhas.value=codded;
		opener.document.form1.txtcoddedhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de Dedicaci?n inv?lido");
	}
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
  	f.action="sigesp_snorh_cat_dedicacion.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
