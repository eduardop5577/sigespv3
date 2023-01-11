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
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_print($as_codasicar, $as_denasicar, $as_tipo)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_print
		//	Arguments:    as_codasicar  // Código de Asignación de Cargo
		//				  as_denasicar // Denominación de Asignación de Cargo
		//				  as_tipo  // Tipo de Llamada del catálogo
		//	Description:  Función que obtiene e imprime los resultados de la busqueda
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
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
        $ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
        $ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=40>Código</td>";
		print "<td width=400>Denominación</td>";
		print "<td width=60>Disponibilidad</td>";
		print "</tr>";
		$ls_sql="SELECT distinct(sno_thasignacioncargo.codasicar), sno_thasignacioncargo.denasicar, sno_thasignacioncargo.claasicar, ".
				"		sno_thasignacioncargo.minorguniadm, sno_thasignacioncargo.ofiuniadm, sno_thasignacioncargo.uniuniadm, ".
				"		sno_thasignacioncargo.depuniadm, sno_thasignacioncargo.prouniadm, sno_thasignacioncargo.codtab, ".
				"		sno_thasignacioncargo.codpas, sno_thasignacioncargo.codgra, sno_thasignacioncargo.codded, ".
				"		sno_thasignacioncargo.codtipper, sno_thasignacioncargo.numvacasicar, sno_thasignacioncargo.numocuasicar, ".
				"		sno_thasignacioncargo.codestpro1, sno_thasignacioncargo.codestpro2, sno_thasignacioncargo.codestpro3, sno_thasignacioncargo.codestpro4, ".
				"		sno_thasignacioncargo.codestpro5, sno_thtabulador.destab, sno_thgrado.monsalgra, sno_thgrado.moncomgra, ".
				"		sno_dedicacion.desded, sno_tipopersonal.destipper, sno_thunidadadmin.desuniadm,sno_thunidadadmin.estcla, ".
				"		(SELECT denestpro1 ".
				"		   FROM spg_ep1 ".
				"		  WHERE spg_ep1.codemp=sno_thasignacioncargo.codemp".
				"		    AND spg_ep1.codestpro1=sno_thasignacioncargo.codestpro1".
				"           AND spg_ep1.estcla=sno_thasignacioncargo.estcla) as denestpro1, ".
				"		(SELECT denestpro2 ".
				"		   FROM spg_ep2 ".
				"		  WHERE spg_ep2.codemp=sno_thasignacioncargo.codemp".
				"		    AND spg_ep2.codestpro1=sno_thasignacioncargo.codestpro1 ".
				"		    AND spg_ep2.codestpro2=sno_thasignacioncargo.codestpro2 ".
				"           AND spg_ep2.estcla=sno_thasignacioncargo.estcla) as denestpro2, ".
				"		(SELECT denestpro3 ".
				"		   FROM spg_ep3 ".
				"		  WHERE spg_ep3.codemp=sno_thasignacioncargo.codemp".
				"		    AND spg_ep3.codestpro1=sno_thasignacioncargo.codestpro1 ".
				"		    AND spg_ep3.codestpro2=sno_thasignacioncargo.codestpro2 ".
				"		    AND spg_ep3.codestpro3=sno_thasignacioncargo.codestpro3 ".
				"           AND spg_ep3.estcla=sno_thasignacioncargo.estcla) as denestpro3, ".
				"		(SELECT denestpro4 ".
				"		   FROM spg_ep4 ".
				"		  WHERE spg_ep4.codemp=sno_thunidadadmin.codemp".
				"		    AND spg_ep4.codestpro1=sno_thasignacioncargo.codestpro1 ".
				"		    AND spg_ep4.codestpro2=sno_thasignacioncargo.codestpro2 ".
				"		    AND spg_ep4.codestpro3=sno_thasignacioncargo.codestpro3 ".
				"		    AND spg_ep4.codestpro4=sno_thasignacioncargo.codestpro4 ".
				"           AND spg_ep4.estcla=sno_thasignacioncargo.estcla) as denestpro4, ".
				"		(SELECT denestpro5 ".
				"		   FROM spg_ep5 ".
				"		  WHERE spg_ep5.codemp=sno_thunidadadmin.codemp".
				"		    AND spg_ep5.codestpro1=sno_thasignacioncargo.codestpro1 ".
				"		    AND spg_ep5.codestpro2=sno_thasignacioncargo.codestpro2 ".
				"		    AND spg_ep5.codestpro3=sno_thasignacioncargo.codestpro3 ".
				"		    AND spg_ep5.codestpro4=sno_thasignacioncargo.codestpro4 ".
				"		    AND spg_ep5.codestpro5=sno_thasignacioncargo.codestpro5 ".
				"           AND spg_ep5.estcla=sno_thasignacioncargo.estcla) as denestpro5 ".
				"  FROM sno_thasignacioncargo, sno_thtabulador, sno_thgrado, sno_dedicacion,sno_tipopersonal,  sno_thunidadadmin  ".
		        " WHERE sno_thasignacioncargo.codemp='".$ls_codemp."'".
				"   AND sno_thasignacioncargo.codnom='".$ls_codnom."'".
				"   AND sno_thasignacioncargo.codasicar like '".$as_codasicar."'".
				"   AND sno_thasignacioncargo.denasicar like '".$as_denasicar."' ".
				"	AND sno_thasignacioncargo.codemp=sno_thtabulador.codemp ".
		        "   AND	sno_thasignacioncargo.codnom=sno_thtabulador.codnom ".
		        "   AND	sno_thasignacioncargo.codtab=sno_thtabulador.codtab ".
				"	AND sno_thasignacioncargo.codemp=sno_thgrado.codemp ".
				" 	AND	sno_thasignacioncargo.codnom=sno_thgrado.codnom ".
				" 	AND	sno_thasignacioncargo.codtab=sno_thgrado.codtab ".
				" 	AND	sno_thasignacioncargo.codpas=sno_thgrado.codpas ".
				" 	AND	sno_thasignacioncargo.codgra=sno_thgrado.codgra ".
				"	AND sno_thasignacioncargo.codemp=sno_dedicacion.codemp ".
				" 	AND	sno_thasignacioncargo.codded=sno_dedicacion.codded ".
				"	AND sno_thasignacioncargo.codemp=sno_tipopersonal.codemp ".
				" 	AND sno_thasignacioncargo.codded=sno_tipopersonal.codded ".
				" 	AND	sno_thasignacioncargo.codtipper=sno_tipopersonal.codtipper ".
				"	AND sno_thasignacioncargo.codemp=sno_thunidadadmin.codemp ".
				" 	AND	sno_thasignacioncargo.minorguniadm=sno_thunidadadmin.minorguniadm ".
				" 	AND	sno_thasignacioncargo.ofiuniadm=sno_thunidadadmin.ofiuniadm ".
				" 	AND	sno_thasignacioncargo.uniuniadm=sno_thunidadadmin.uniuniadm ".
				" 	AND	sno_thasignacioncargo.depuniadm=sno_thunidadadmin.depuniadm ".
				" 	AND	sno_thasignacioncargo.prouniadm=sno_thunidadadmin.prouniadm ".
				" 	AND	sno_thasignacioncargo.anocur='".$ls_anocurnom."'".
				" 	AND	sno_thasignacioncargo.codperi='".$ls_peractnom."'".
				" ORDER BY sno_thasignacioncargo.codasicar ";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codasicar=$rs_data->fields["codasicar"];
				$ls_denasicar=$rs_data->fields["denasicar"];
				$ls_coduniadm=$rs_data->fields["minorguniadm"]."-".$rs_data->fields["ofiuniadm"]."-".$rs_data->fields["uniuniadm"]."-".$rs_data->fields["depuniadm"]."-".$rs_data->fields["prouniadm"];
				$ls_claasicar=$rs_data->fields["claasicar"];
				$ls_codtab=$rs_data->fields["codtab"];
				$ls_codpas=$rs_data->fields["codpas"];
				$ls_codgra=$rs_data->fields["codgra"];
				$ls_codded=$rs_data->fields["codded"];
				$ls_codtipper=$rs_data->fields["codtipper"];		
				$li_numvacasicar=$rs_data->fields["numvacasicar"];
				$li_numocuasicar=$rs_data->fields["numocuasicar"];
				$li_disponasicar=($li_numvacasicar-$li_numocuasicar);
				$ls_destab=$rs_data->fields["destab"];
				$ls_desded=$rs_data->fields["desded"];
				$ls_destipper=$rs_data->fields["destipper"];
				$ls_desuniadm=$rs_data->fields["desuniadm"];
				$li_monsalgra=$rs_data->fields["monsalgra"];
				$li_moncomgra=$rs_data->fields["moncomgra"];
				$li_monsalgra=$io_fun_nomina->uf_formatonumerico($li_monsalgra);
				$li_moncomgra=$io_fun_nomina->uf_formatonumerico($li_moncomgra);
				$ls_codproasicar=$rs_data->fields["codestpro1"].$rs_data->fields["codestpro2"].$rs_data->fields["codestpro3"].$rs_data->fields["codestpro4"].$rs_data->fields["codestpro5"];
				$ls_codest1="";
				$ls_codest2="";
				$ls_codest3="";
				$ls_codest4="";
				$ls_codest5="";
				$arrResultado=$io_fun_nomina->uf_formato_estructura($ls_codproasicar,$ls_codest1,$ls_codest2,$ls_codest3,$ls_codest4,$ls_codest5);
				$ls_codest1=$arrResultado['as_codestpro1'];
				$ls_codest2=$arrResultado['as_codestpro2'];
				$ls_codest3=$arrResultado['as_codestpro3'];
				$ls_codest4=$arrResultado['as_codestpro4'];
				$ls_codest5=$arrResultado['as_codestpro5'];
				$ls_denestpro1=$rs_data->fields["denestpro1"];
				$ls_denestpro2=$rs_data->fields["denestpro2"];
				$ls_denestpro3=$rs_data->fields["denestpro3"];
				$ls_denestpro4=$rs_data->fields["denestpro4"];
				$ls_denestpro5=$rs_data->fields["denestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				switch ($as_tipo)
				{									
					case "listado1":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_listado1('$ls_codasicar');\">".$ls_codasicar."</a></td>";
						print "<td>".$ls_denasicar."</td>";
						print "<td>".$li_disponasicar."</td>";
						print "</tr>";			
						break;
					
					case "listado2":
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: aceptar_listado2('$ls_codasicar');\">".$ls_codasicar."</a></td>";
						print "<td>".$ls_denasicar."</td>";
						print "<td>".$li_disponasicar."</td>";
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
		unset($ls_codnom);
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Asignaci&oacute;n de Cargo</title>
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
  <table width="500" height="20" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-ventana">Cat&aacute;logo de Asignaci&oacute;n de Cargo </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodasicar" type="text" id="txtcodasicar" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdenasicar" type="text" id="txtdenasicar" size="30" maxlength="24" onKeyPress="javascript: ue_mostrar(this,event);">
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
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codasicar="%".$_POST["txtcodasicar"]."%";
		$ls_denasicar="%".$_POST["txtdenasicar"]."%";
		uf_print($ls_codasicar, $ls_denasicar, $ls_tipo);
	}
	else
	{
		$ls_codasicar="%%";
		$ls_denasicar="%%";
		uf_print($ls_codasicar, $ls_denasicar, $ls_tipo);
	}
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

function aceptar_listado1(codasicar)

{
	opener.document.form1.txtcodasignacdes.value=codasicar;
	opener.document.form1.txtcodasignacdes.readOnly=true;
	close();	
}

function aceptar_listado2(codasicar)

{
	opener.document.form1.txtcodasignachas.value=codasicar;
	opener.document.form1.txtcodasignachas.readOnly=true;
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
  	f.action="sigesp_sno_cat_hasignacioncargo.php?tipo=<?PHP print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
