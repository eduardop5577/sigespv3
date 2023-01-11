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
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codper  // Código de personal
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/03/2018 								Fecha Última Modificación : 
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
		print "<td>Fecha</td>";
		print "<td>Nomina</td>";
		print "<td>Cargo</td>";
		print "<td>Asignacion de Cargo</td>";
		print "</tr>";
		
		$ls_sql="SELECT fecmov, codnom, codasicar, codtab, codgra, codpas, codcar, desnom, descar, desasicar, destab ".
	   			"  FROM sno_historicocargo ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codper = '".$as_codper."' ".
			    " ORDER BY fecmov ASC";
		$rs_data=$io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ld_fecmov=$io_funciones->uf_convertirfecmostrar($rs_data->fields['fecmov']);
				$ls_codnom=$rs_data->fields['codnom'];
				$ls_codasicar=$rs_data->fields['codasicar'];
				$ls_codtab=$rs_data->fields['codtab'];
				$ls_codgra=$rs_data->fields['codgra'];
				$ls_codpas=$rs_data->fields['codpas'];
				$ls_codcar=$rs_data->fields['codcar'];
				$ls_desnom=$rs_data->fields['desnom'];
				$ls_descar=$rs_data->fields['descar'];
				$ls_desasicar=$rs_data->fields['desasicar'];
				$ls_destab=$rs_data->fields['destab'];
				print "<tr class=celdas-blancas>";
				print "<td align=center><a href=\"javascript: aceptar('$ld_fecmov','$ls_codnom','$ls_codasicar','$ls_codtab,','$ls_codgra','$ls_codpas','$ls_codcar',".
					  "												  '$ls_desnom','$ls_descar','$ls_desasicar','$ls_destab');\">".$ld_fecmov."</a></td>";
				print "<td>".$ls_codnom."</td>";
				print "<td>".$ls_codcar."</td>";
				print "<td>".$ls_codasicar."</td>";
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
		unset($io_unidadadmin);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Historico de Cargos</title>
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
<style type="text/css">
<!--
.Estilo1 {font-size: 11px}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="">
  	 <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="500" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Historico de Cargos </td>
    	</tr>
     </table>
	 <br>
	 <br>
    <?php
		$ls_codper=$_GET["codper"];
		uf_print($ls_codper);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(fecmov,codnom,codasicar,codtab,codgra,codpas,codcar,desnom,descar,desasicar,destab)
  {
	opener.document.form1.txtfecmov.value=fecmov;
	opener.document.form1.txtcodnom.value=codnom;
	opener.document.form1.txtdesnom.value=desnom;
	opener.document.form1.txtcodasicar.value=codasicar;
	opener.document.form1.txtdesasicar.value=desasicar;
	opener.document.form1.txtcodtab.value=codtab;			
	opener.document.form1.txtdestab.value=destab;			
	opener.document.form1.txtcodpas.value=codpas;
	opener.document.form1.txtcodgra.value=codgra;
	opener.document.form1.txtcodcar.value=codcar;
	opener.document.form1.txtdescar.value=descar;
	opener.document.form1.existe.value='TRUE';
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
	  f.action="sigesp_snorh_cat_historicocargos.php?codper=<?php print $ls_codper;?>";
	  f.submit();
  }
</script>
</html>
