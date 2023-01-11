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
	function uf_print($ai_numper, $as_codper, $ai_totrow)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codconc  // Código del Concepto
		//				   as_nomcon  // Nombre del Concepto
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/01/2010 								Fecha Última Modificación : 
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
   		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();				
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("../shared/class_folder/grid_param.php");
		$grid = new grid_param();		
		$ls_criterio="";
		if ($ai_numper!="")
		{
			$ls_criterio=" AND numper = ".$ai_numper." ";
		}
		$title[1]="Todos <input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
		$title[2]="Número";   
		$title[3]="Fecha Inicio";
		$title[4]="Fecha Fin";   
		$title[5]="Días";
		$title[6]="Observación";   
		
		$ls_sql="SELECT codper,numper,feciniper,fecfinper,numdiaper,obsper ". 
				"  FROM sno_permiso ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND afevacper=0 ".
				"   AND desvacper='0' ".
				$ls_criterio.
				" ORDER BY numper ";	
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			$ai_totrow=1;
			while(!$rs_data->EOF)
			{
				$li_numper=$rs_data->fields["numper"];
				$ld_feciniper=$rs_data->fields["feciniper"];
				$ld_fecfinper=$rs_data->fields["fecfinper"];
				$li_numdiaper=$rs_data->fields["numdiaper"];
				$ls_obsper=$rs_data->fields["obsper"];
				$object[$ai_totrow][1]="<input name=chkpermiso".$ai_totrow." type=checkbox id=chkpermiso".$ai_totrow." value=1 class=sin-borde onClick=javascript:uf_selected('".$ai_totrow."');>";
				$object[$ai_totrow][2]="<input type=text name=txtnumper".$ai_totrow." value='".$li_numper."' id=txtnumper".$ai_totrow." class=sin-borde readonly style=text-align:left size=10 maxlength=10>";	
				$object[$ai_totrow][3]="<input type=text name=txtfeciniper".$ai_totrow." value='".$ld_feciniper."' id=txtfeciniper".$ai_totrow." class=sin-borde readonly style=text-align:left size=15 maxlength=15>";	
				$object[$ai_totrow][4]="<input type=text name=txtfecfinper".$ai_totrow." value='".$ld_fecfinper."' id=txtfecfinper".$ai_totrow." class=sin-borde readonly style=text-align:left size=15 maxlength=15>";	
				$object[$ai_totrow][5]="<input type=text name=txtnumdiaper".$ai_totrow." value='".$li_numdiaper."' id=txtnumdiaper".$ai_totrow." class=sin-borde readonly style=text-align:left size=10 maxlength=10>";	
				$object[$ai_totrow][6]="<input type=text name=txtobsper".$ai_totrow." value='".$ls_obsper."' id=txtobsper".$ai_totrow." class=sin-borde readonly style=text-align:left size=25 maxlength=25>";	
				$ai_totrow++;
				$rs_data->MoveNext();
			}
			$object[$ai_totrow][1]="<input name=chkcta1 type=checkbox id=chkcta1 value=1 onClick=javascript:uf_selected('".$z."');>";
			$object[$ai_totrow][2]="<input type=text name=txtnumper value='' id=txtnumper class=sin-borde readonly style=text-align:center size=10 maxlength=20>";		
			$object[$ai_totrow][3]="<input type=text name=txtfeciniper value='' id=txtfeciniper class=sin-borde readonly style=text-align:center size=15 maxlength=254>";
			$object[$ai_totrow][4]="<input type=text name=txtfecfinper value='' id=txtfecfinper class=sin-borde readonly style=text-align:center size=15 maxlength=20>";		
			$object[$ai_totrow][5]="<input type=text name=txtnumdiaper value='' id=txtnumdiaper class=sin-borde readonly style=text-align:center size=10 maxlength=254>";
			$object[$ai_totrow][6]="<input type=text name=txtobsper value='' id=txtobsper class=sin-borde readonly style=text-align:center size=25 maxlength=20>";		
			$grid->makegrid($ai_totrow,$title,$object,600,'Catalogo de Permisos','gridpermisos');			
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($io_sno);
		unset($ls_codemp);
		return $ai_totrow;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Permisos</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Permisos </td>
    </tr>
  </table>
  <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
    <tr>
      <td width="67" height="22"><div align="right">Número</div></td>
      <td width="431"><div align="left">
          <input name="txtnumper" type="text"  id="txtnumper" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
      </div></td>
    </tr>
    
    <tr>
      <td height="22">&nbsp;</td>
      <td><div align="right"><a href="javascript: ue_aceptar();"><img src="../shared/imagebank/tools20/aprobado.gif" width="20" height="20" border="0">Aceptar</a><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p><br>
  </p>
  <br>
<?php
	require_once("class_folder/class_funciones_nomina.php");
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion(); 	
	$ls_codper=$io_fun_nomina->uf_obtenervalor_get("codper","");
	if(array_key_exists("selected",$_POST))
	{
		$li_selected= $_POST["selected"];
	}
	else
	{
		$li_selected= 0;
	}
	if($ls_operacion=="BUSCAR")
	{
		$li_numper="".$_POST["txtnumper"]."";
		$totrow=0;
		$li_totrow=uf_print($li_numper, $ls_codper, $li_totrow);		
	}
	unset($io_fun_nomina);	
?>
</div>
 <input name="total" type="hidden" id="total" value="<?php print $li_totrow;?>"> 
 <input name="selected" type="hidden" id="selected" value="<?php print $li_selected;?>">
 <input name="codper" type="hidden" id="codper" value="<?php print $ls_codper;?>"> 
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function ue_aceptar()
  {
	  f=document.form1;
	  fop=opener.document.form1;
	  li_total=f.total.value;	  
	  li_selected=f.selected.value;	
	  parametro="";
	  li_sel=0;	 
	  li_nrodias=0;	 
	  for(i=1;(i<parseInt(li_total,10));i++)	
	  {
		if(li_sel<=parseInt(li_selected,10))
		{
			if(eval("f.chkpermiso"+i+".checked==true"))
			{
				li_sel=eval(li_sel+1);			
				ls_txtnumper=eval("f.txtnumper"+i+".value");
			  	li_numdiaper=eval("f.txtnumdiaper"+i+".value");
				li_numdiaper=parseInt(li_numdiaper,10); 
			  	li_nrodias=parseInt(li_numdiaper+li_nrodias,10); 
				if(parametro=='')
				{
					parametro=ls_txtnumper;				
				}
				else
				{
					parametro=parametro+"-"+ls_txtnumper;
				}
			}			
		}
		else
		{
			break;
			close();			
		}	
	}
	fop.txtdiapervac.value=li_nrodias;
	fop.txtpermisosvac.value=parametro;
	fop.operacion.value="REPROGRAMAR";
	fop.action="sigesp_sno_p_vacacionprogramar.php";
	fop.submit();
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
	codper=f.codper.value;
  	f.action="sigesp_snorh_sel_catpermiso.php?codper="+codper;
  	f.submit();
}


function uf_select_all()
{
	  f=document.form1;
	  fop=opener.document.form1;
	  total=f.total.value; 
	  sel_all=f.chkall.value;	  	  
	  if(sel_all=='T')
	  {
		  for(i=1;i<total;i++)	
		  {
			eval("f.chkpermiso"+i+".checked=true");			
		  }		
		  f.selected.value=total; 
	  }
}

function uf_selected(li_i)
{
 	f=document.form1;
	li_total=f.total.value;
	li_selected=f.selected.value; 
	if(eval("f.chkpermiso"+li_i+".checked==true"))
	{
		li_selected=parseInt(li_selected,10)+1;
	}
 	f.selected.value=li_selected;
}	
</script>
</html>