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
   function uf_print($as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_tipo  // Verifica de donde se está llamando el catálogo
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/06/2006 								Fecha Última Modificación : 
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
		$ls_criterio="";
		if (($as_tipo=="PAGOPERSONAL") || ($as_tipo=="ANTICIPO"))
		{
			$ls_criterio=" AND (estpre = 3 OR estpre = 4 )";
		}
		elseif ($as_tipo=="CAUSADO")
		{
			$ls_criterio=" AND estpre = 1 ";
		}
		else
		{
			$ls_criterio=" AND estpre = 2 ";
		}
		
		$ls_sql="SELECT codtipdoc,dentipdoc ".
				"  FROM cxp_documento ".
				" WHERE estcon = 1".
				$ls_criterio.
				" ORDER BY codtipdoc ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				switch ($as_tipo)
				{
					case "NOMINA": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarnomina('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;
					
					case "APORTE": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptaraporte('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;
					
					case "FIDEICOMISO": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarfideicomiso('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;
						
					case "PAGOPERSONAL": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarpagpersonal('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;

					case "LIQUIDACION": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarliquidacion('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;
						
					case "GUARDERIA": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarguarderia('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;	

					case "ANTICIPO": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptaranticipo('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
						print "</tr>";			
						break;

					case "CAUSADO": // llamado desde sigesp_snorh_p_configuracion.php
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarcausado('$ls_codtipdoc','$ls_dentipdoc');\">".$ls_codtipdoc."</a></td>";
						print "<td>".$ls_dentipdoc."</td>";
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
<title>Cat&aacute;logo de Tipo de Documento</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Tipo de Documento </td>
    </tr>
  </table>
<br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	uf_print($ls_tipo);
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptarnomina(codigo,descripcion)
{
	opener.document.form1.txttipdocnom.value=codigo;
	opener.document.form1.txttipdocnom.readOnly=true;
	close();
}

function aceptaraporte(codigo,descripcion)
{
	opener.document.form1.txttipdocapo.value=codigo;
	opener.document.form1.txttipdocapo.readOnly=true;
	close();
}

function aceptarfideicomiso(codigo,descripcion)
{
	opener.document.form1.txttipdocfid.value=codigo;
	opener.document.form1.txttipdocfid.readOnly=true;
	close();
}

function aceptarpagpersonal(codigo,descripcion)
{
	opener.document.form1.txttipdocpagper.value=codigo;
	opener.document.form1.txttipdocpagper.readOnly=true;
	close();
}

function aceptarliquidacion(codigo,descripcion)
{
	opener.document.form1.txttipdocpagper.value=codigo;
	opener.document.form1.txttipdocpagper.readOnly=true;
	close();
}

function aceptarguarderia(codigo,descripcion)
{
	opener.document.form1.txttipdocguar.value=codigo;
	opener.document.form1.txttipdocguar.readOnly=true;
	close();
}

function aceptaranticipo(codigo,descripcion)
{
	opener.document.form1.txttipdocant.value=codigo;
	opener.document.form1.txttipdocant.readOnly=true;
	close();
}

function aceptarcausado(codigo,descripcion)
{
	opener.document.form1.txttipdoccaunom.value=codigo;
	opener.document.form1.txttipdoccaunom.readOnly=true;
	close();
}
</script>
</html>
