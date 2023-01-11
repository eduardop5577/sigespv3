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
   function uf_print($as_codtipesc, $as_dentipesc, $as_tipo)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/12/2019 								Fecha Última Modificación : 
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
		print "<td width=100>Código</td>";
		print "<td width=400>Descripción</td>";
		print "</tr>";
		$ls_sql="SELECT codemp,codtipesc,dentipesc,escbol,tophor,difacc,medacc,rural,colnoc,colesp,colpen ".
                        "  FROM sno_tipoescuela ".
                        " WHERE codemp='".$ls_codemp."'".
                        "   AND codtipesc like '".$as_codtipesc."'".
                        "   AND dentipesc like '".$as_dentipesc."'".
                        " ORDER BY  codtipesc, dentipesc ASC";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codtipesc=$row["codtipesc"];
				$ls_dentipesc=$row["dentipesc"];
				$ls_escbol=$row["escbol"];
				$li_tophor=$row["tophor"];
				$ls_difacc=$row["difacc"];
				$ls_medacc=$row["medacc"];
				$ls_rural=$row["rural"];
				$ls_colnoc=$row["colnoc"];
				$ls_colesp=$row["colesp"];
				$ls_colpen=$row["colpen"];
				switch ($as_tipo)
				{
					case "":				
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codtipesc','$ls_dentipesc','$ls_escbol','$li_tophor','$ls_difacc','$ls_medacc','$ls_rural','$ls_colnoc','$ls_colesp','$ls_colpen');\">".$ls_codtipesc."</a></td>";
						print "<td>".$ls_dentipesc."</td>";
						print "</tr>";			
						break;
					
					case "ubicacionfisica":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarubicacionfisica('$ls_codtipesc','$ls_dentipesc');\">".$ls_codtipesc."</a></td>";
						print "<td>".$ls_dentipesc."</td>";
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
<title>Cat&aacute;logo de M&eacute;todo Banco</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Tipo Escuela </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="111" height="22"><div align="right"> M&eacute;todo </div></td>
        <td width="380"><div align="left">
          <input name="txtcodtipesc" type="text" id="txtcodtipesc" size="30" maxlength="4" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdentipesc" type="text" id="txtdentipesc" size="30" maxlength="100" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
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
		$ls_codtipesc="%".$_POST["txtcodtipesc"]."%";
		$ls_dentipesc="%".$_POST["txtdentipesc"]."%";
		uf_print($ls_codtipesc, $ls_dentipesc, $ls_tipo);
	}
	else
	{
		$ls_codtipesc="%%";
		$ls_dentipesc="%%";
		uf_print($ls_codtipesc, $ls_dentipesc, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codtipesc,dentipesc,escbol,tophor,difacc,medacc,rural,colnoc,colesp,colpen)
{
	opener.document.form1.txtcodtipesc.value=codtipesc;
	opener.document.form1.txtcodtipesc.readOnly=true;
        opener.document.form1.txtdentipesc.value=dentipesc;
        opener.document.form1.txttophor.value="";
        opener.document.form1.existe.value="TRUE";
        opener.document.form1.chkescbol.checked=false;
        opener.document.form1.chkdifacc.checked=false;
        opener.document.form1.chkmedacc.checked=false;
        opener.document.form1.chkrural.checked=false;
        opener.document.form1.chkcolnoc.checked=false;
        opener.document.form1.chkcolesp.checked=false;
        opener.document.form1.chkcolpen.checked=false;
	if (escbol=='1')
	{
            opener.document.form1.chkescbol.checked=true;
            opener.document.form1.txttophor.value=tophor;
	}
	if (difacc=='1')
	{
            opener.document.form1.chkdifacc.checked=true;
	}
	if (medacc=='1')
	{
            opener.document.form1.chkmedacc.checked=true;
	}
	if (rural=='1')
	{
            opener.document.form1.chkrural.checked=true;
	}
	if (colnoc=='1')
	{
            opener.document.form1.chkcolnoc.checked=true;
	}
	if (colesp=='1')
	{
            opener.document.form1.chkcolesp.checked=true;
	}
	if (colpen=='1')
	{
            opener.document.form1.chkcolpen.checked=true;
	}        
	close();
}

function aceptarubicacionfisica(codtipesc,dentipesc)
{
	opener.document.form1.txtcodtipesc.value=codtipesc;
	opener.document.form1.txtcodtipesc.readOnly=true;
        opener.document.form1.txtdentipesc.value=dentipesc;
	opener.document.form1.txtdentipesc.readOnly=true;
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
  	f.action="sigesp_snorh_cat_tipoescuela.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
