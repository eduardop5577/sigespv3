<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print($as_codarc, $as_denarc, $as_tipo, $as_codban)
   	{
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();	
                $ls_criterio = "";
                $ls_codemp=$_SESSION["la_empresa"]["codemp"];
                if ($as_codban!="")
                {
                    $ls_criterio = "AND scb_archivoconciliacion.codban = '".$as_codban."'";
                }
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>C�digo</td>";
		print "<td width=200>Denominaci�n</td>";
		print "<td width=100>Banco</td>";
		print "<td width=140>Tipo Archivo</td>";
		print "</tr>";
		$ls_sql="SELECT scb_archivoconciliacion.* ,scb_banco.nomban ".
                        "  FROM scb_archivoconciliacion, scb_banco ".
                        " WHERE scb_archivoconciliacion.codemp='".$ls_codemp."'".
                        "   AND scb_archivoconciliacion.codarc like '".$as_codarc."'".
                        "   AND scb_archivoconciliacion.denarc like '".$as_denarc."'".$ls_criterio.				
                        "   AND scb_archivoconciliacion.codemp = scb_banco.codemp ".
                        "   AND scb_archivoconciliacion.codban = scb_banco.codban ".
                        " ORDER BY codarc ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codarc=$rs_data->fields["codarc"];
				$ls_denarc=$rs_data->fields["denarc"];
                                $ls_codban=$rs_data->fields["codban"];
                                $ls_denban=$rs_data->fields["nomban"];
				$ls_tiparc=$rs_data->fields["tiparc"];
				$ls_separc=$rs_data->fields["separc"];
				$li_filiniarc=$rs_data->fields["filiniarc"];
				$ls_ndequarc=$rs_data->fields["ndequarc"];
				$ls_ncequarc=$rs_data->fields["ncequarc"];
				$ls_chequarc=$rs_data->fields["chequarc"];
				$ls_dpequarc=$rs_data->fields["dpequarc"];
				$ls_rtequarc=$rs_data->fields["rtequarc"];
				switch ($as_tipo)
				{
					case "":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar('$ls_codarc','$ls_denarc','$ls_codban','$ls_denban','$ls_tiparc','$ls_separc',".
                                                      "'$li_filiniarc','$ls_ndequarc','$ls_ncequarc','$ls_dpequarc','$ls_chequarc','$ls_rtequarc');\">".$ls_codarc."</a></td>";
						print "<td>".$ls_denarc."</td>";
						print "<td>".$ls_denban."</td>";
						switch ($ls_tiparc)
						{
							case "0":
								print "<td>Plano TXT(Metodo)</td>";
							break;

							case "1":
								print "<td>Plano TXT(Separador)</td>";
							break;

							case "2":
								print "<td>Excel</td>";
							break;
						}
						print "</tr>";
						break;
						
					case "conciliacion":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptarconciliacion('$ls_codarc','$ls_denarc','$ls_tiparc');\">".$ls_codarc."</a></td>";
						print "<td>".$ls_denarc."</td>";
						print "<td>".$ls_denban."</td>";
						switch ($ls_tiparc)
						{
							case "0":
								print "<td>Plano TXT(Metodo)</td>";
							break;

							case "1":
								print "<td>Plano TXT(Separador)</td>";
							break;

							case "2":
								print "<td>Excel</td>";
							break;
						}
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
<title>Cat&aacute;logo de Archivos txt</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Archivos txt </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodarc" type="text" id="txtcodarc" size="30" maxlength="4" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdenarc" type="text" id="txtdenarc" size="30" maxlength="120" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();">
          <img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	require_once("class_folder/class_funciones_scb.php");
	$io_fun=new class_funciones_scb();
	$ls_operacion =$io_fun->uf_obteneroperacion();
	$ls_tipo=$io_fun->uf_obtenertipo();
        if (array_key_exists("codban",$_GET))
	{
            $ls_codban=$_GET["codban"];
	}
	else
	{
            $ls_codban=$_POST["codban"];
	}        
	if($ls_operacion=="BUSCAR")
	{
		$ls_codarc="%".$_POST["txtcodarc"]."%";
		$ls_denarc="%".$_POST["txtdenarc"]."%";
		uf_print($ls_codarc, $ls_denarc, $ls_tipo,$ls_codban);
	}
	else
	{
		$ls_codarc="%%";
		$ls_denarc="%%";
		uf_print($ls_codarc, $ls_denarc, $ls_tipo,$ls_codban);
	}
	unset($io_fun);
?>
</div>
<input name="codban" type="hidden" id="codban" value="<?php print $ls_codban;?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(codarc,denarc,codban,denban,tiparc,separc,filiniarc,ndequarc,ncequarc,dpequarc,chequarc,rtequarc)
{
	opener.document.form1.txtcodarc.value=codarc;
	opener.document.form1.txtcodarc.readOnly=true;
	opener.document.form1.txtdenarc.value=denarc;
	opener.document.form1.txtcodban.value=codban;
	opener.document.form1.txtdenban.value=denban;
	opener.document.form1.cmbtiparc.value=tiparc;
	opener.document.form1.txtseparc.value=separc;
	opener.document.form1.txtfiliniarc.value=filiniarc;
	opener.document.form1.txtndequarc.value=ndequarc;
	opener.document.form1.txtncequarc.value=ncequarc;
	opener.document.form1.txtdpequarc.value=dpequarc;
	opener.document.form1.txtchequarc.value=chequarc;
	opener.document.form1.txtrtequarc.value=rtequarc;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.action="sigesp_scb_d_archivoconciliacion.php";
	opener.document.form1.existe.value="TRUE";			
	opener.document.form1.submit();	
	close();
}

function aceptarconciliacion(codarc,denarc,tiparc)
{
	opener.document.form1.txtcodarc.value=codarc;
	opener.document.form1.txtcodarc.readOnly=true;
	opener.document.form1.txtdenarc.value=denarc;
	opener.document.form1.txtdenarc.readOnly=true;
	opener.document.form1.txttiparc.value=tiparc;
  	opener.document.form1.operacion.value="VERIFICAR";
  	opener.document.form1.action="sigesp_scb_p_conciliacionautomatica.php";
  	opener.document.form1.submit();
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
  	f.action="sigesp_scb_cat_archivoconciliacion.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>