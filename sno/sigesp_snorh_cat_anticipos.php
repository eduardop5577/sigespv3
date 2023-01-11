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
   function uf_print($as_codper)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codper  // Código de Personal
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/11/2010 								Fecha Última Modificación : 
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
		print "<td width=80>Código </td>";
		print "<td width=80>Fecha </td>";
		print "<td width=60>Monto</td>";
		print "<td width=200>Motivo</td>";
		print "<td width=80>Estatus</td>";
		print "</tr>";
		$ls_sql="SELECT codant, estant, fecant, monpreant, monintant, monantant, monantint, porant, monant, monint, motant, obsant ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" ORDER BY codant";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codant=$rs_data->fields["codant"];	
				$ls_estant=$rs_data->fields["estant"];	
				switch($ls_estant)
				{
					case 'R':
						$ls_estatus='REGISTRO';
					break;
					case 'A':
						$ls_estatus='APROBADO';
					break;
					case 'C':
						$ls_estatus='CONTABILIZADO';
					break;
					case 'X':
						$ls_estatus='ANULADO';
					break;
				}
				$ld_fecant=$io_funciones->uf_convertirfecmostrar($rs_data->fields["fecant"]);
				$li_monpreant=number_format($rs_data->fields["monpreant"],2,",",".");				
				$li_monintant=number_format($rs_data->fields["monintant"],2,",",".");					
				$li_monantant=number_format($rs_data->fields["monantant"],2,",",".");					
				$li_monantint=number_format($rs_data->fields["monantint"],2,",",".");					
				$li_porant=$rs_data->fields["porant"];	
				$li_monant=number_format($rs_data->fields["monant"],2,",",".");	
				$li_monint=number_format($rs_data->fields["monint"],2,",",".");	
				$ls_motant=$rs_data->fields["motant"];	
				$ls_obsant=$rs_data->fields["obsant"];	
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ls_codant','$ls_estant','$ls_estatus','$ld_fecant',".
					  "'$li_monpreant','$li_monintant','$li_monantant','$li_monantint','$li_porant','$li_monant',".
					  "'$li_monint','$ls_motant','$ls_obsant');\">".$ls_codant."</a></td>";
				print "<td>".$ld_fecant."</td>";
				print "<td>".$li_monant."</td>";
				print "<td>".$ls_motant."</td>";
				print "<td>".$ls_estatus."</td>";
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
<title>Cat&aacute;logo de Sueldos Hist&oacute;ricos</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Anticipos de Prestacion </td>
    </tr>
  </table>
<br>
<br>
<?php
	$ls_codper=$_GET["codper"];
	uf_print($ls_codper);
?>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(ls_codant,ls_estant,ls_estatus,ld_fecant,li_monpreant,li_monintant,li_monantant,li_monantint,li_porant,li_monant,li_monint,ls_motant,ls_obsant)
{
	opener.document.form1.txtcodant.value=ls_codant;
	opener.document.form1.txtcodant.readOnly=true;
    opener.document.form1.txtestant.value=ls_estant;
	opener.document.form1.txtestatus.value=ls_estatus;
    opener.document.form1.txtfecant.value=ld_fecant;
    opener.document.form1.txtmonpreant.value=li_monpreant;
    opener.document.form1.txtmonintant.value=li_monintant;
    opener.document.form1.txtmonantant.value=li_monantant;
    opener.document.form1.txtmonantint.value=li_monantint;
    opener.document.form1.txtporant.value=li_porant;
    opener.document.form1.txtmonant.value=li_monant;
    opener.document.form1.txtmonint.value=li_monint;
    opener.document.form1.txtmotant.value=ls_motant;
    opener.document.form1.txtobsant.value=ls_obsant;
	opener.document.form1.existe.value="TRUE";		
	close();
}

</script>
</html>
