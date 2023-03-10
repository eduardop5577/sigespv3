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
	function uf_print($as_codconc, $as_nomcon, $as_codnomdes, $as_codnomhas, $ai_totrow)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codconc  // C?digo del Concepto
		//				   as_nomcon  // Nombre del Concepto
		//	  Description: Funci?n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 19/01/2010 								Fecha ?ltima Modificaci?n : 
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
		$li_difconpnom=$io_sno->uf_select_config("SNO","NOMINA","DIFERENCIAR CONCEPTOS NOMINA","0","I");
		$ls_criterio="";
		$ls_sql2="";
		if ($as_codconc!="")
		{
			$ls_criterio .=" AND codconc LIKE  '".$as_codconc."'";
		}
		if ($as_nomcon!="")
		{
			$ls_criterio .=" AND nomcon LIKE '".$as_nomcon."'";
		}
		if (($as_codnomdes!="")&&($as_codnomhas!=""))
		{
			$ls_criterio .=" AND codnom >= '".$as_codnomdes."'".
						   " AND codnom <= '".$as_codnomhas."'";
		}
		if ($li_difconpnom==1)
		{
			$ls_sql2=", codnom";
			$title[1]="Todos <input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
			$title[2]="Nomina"; 
			$title[3]="C?digo";   
			$title[4]="Descripci?n";
		}
		else
		{
			$title[1]="Todos <input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
			$title[2]="C?digo";   
			$title[3]="Descripci?n";
		}
		
		$ls_sql="SELECT codconc, MAX(nomcon) as nomcon$ls_sql2 ". 
				"  FROM sno_concepto ".
				" WHERE codemp='".$ls_codemp."' ".
				$ls_criterio.
				" GROUP BY codconc$ls_sql2 ".
				" ORDER BY codconc$ls_sql2 ";		
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			$ai_totrow=1;
			if ($li_difconpnom==1)
			{
				while(!$rs_data->EOF)
				{
					$ls_codconc=$rs_data->fields["codconc"];
					$ls_nomcon=$rs_data->fields["nomcon"];
					$ls_codnom=$rs_data->fields["codnom"];
					$object[$ai_totrow][1]="<input name=chkconcepto".$ai_totrow." type=checkbox id=chkconcepto".$ai_totrow." value=1 class=sin-borde onClick=javascript:uf_selected('".$ai_totrow."');>";
					$object[$ai_totrow][2]="<input type=text name=txtcodnom".$ai_totrow." value='".$ls_codnom."' id=txtcodnom".$ai_totrow." class=sin-borde readonly style=text-align:center size=7 maxlength=4 >";		
					$object[$ai_totrow][3]="<input type=text name=txtcodconc".$ai_totrow." value='".$ls_codconc."' id=txtcodconc".$ai_totrow." class=sin-borde readonly style=text-align:center size=18 maxlength=18 >";		
					$object[$ai_totrow][4]="<input type=text name=txtnomcon".$ai_totrow." value='".$ls_nomcon."' id=txtnomcon".$ai_totrow." class=sin-borde readonly style=text-align:left size=25 maxlength=25>";	
					$ai_totrow++;
					$rs_data->MoveNext();
				}
				$object[$ai_totrow][1]="<input name=chkcta1 type=checkbox id=chkcta1 value=1 onClick=javascript:uf_selected('".$z."');>";
				$object[$ai_totrow][2]="<input type=text name=txtcodnom value='' id=txtcodnom class=sin-borde readonly style=text-align:center size=7 maxlength=4>";		
				$object[$ai_totrow][3]="<input type=text name=txtcodconc value='' id=txtcodconc class=sin-borde readonly style=text-align:center size=20 maxlength=20>";		
				$object[$ai_totrow][4]="<input type=text name=txtnomcon value='' id=txtnomcon class=sin-borde readonly style=text-align:center size=50 maxlength=254>";
				$grid->makegrid($ai_totrow,$title,$object,600,'Catalogo de Conceptos','gridconceptos');			
			}
			else
			{
				while(!$rs_data->EOF)
				{
					$ls_codconc=$rs_data->fields["codconc"];
					$ls_nomcon=$rs_data->fields["nomcon"];
					$object[$ai_totrow][1]="<input name=chkconcepto".$ai_totrow." type=checkbox id=chkconcepto".$ai_totrow." value=1 class=sin-borde onClick=javascript:uf_selected('".$ai_totrow."');>";
					$object[$ai_totrow][2]="<input type=text name=txtcodconc".$ai_totrow." value='".$ls_codconc."' id=txtcodconc".$ai_totrow." class=sin-borde readonly style=text-align:center size=18 maxlength=18 >";		
					$object[$ai_totrow][3]="<input type=text name=txtnomcon".$ai_totrow." value='".$ls_nomcon."' id=txtnomcon".$ai_totrow." class=sin-borde readonly style=text-align:left size=25 maxlength=25>";	
					$ai_totrow++;
					$rs_data->MoveNext();
				}
				$object[$ai_totrow][1]="<input name=chkcta1 type=checkbox id=chkcta1 value=1 onClick=javascript:uf_selected('".$z."');>";
				$object[$ai_totrow][2]="<input type=text name=txtcodconc value='' id=txtcodconc class=sin-borde readonly style=text-align:center size=20 maxlength=20>";		
				$object[$ai_totrow][3]="<input type=text name=txtnomcon value='' id=txtnomcon class=sin-borde readonly style=text-align:center size=50 maxlength=254>";
				$grid->makegrid($ai_totrow,$title,$object,600,'Catalogo de Conceptos','gridconceptos');			
			}
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
<title>Cat&aacute;logo de Concepto</title>
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Conceptos </td>
    </tr>
  </table>
  <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
    <tr>
      <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
      <td width="431"><div align="left">
          <input name="txtcodconc" type="text"  id="txtcodconc" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
      </div></td>
    </tr>
    <tr>
      <td height="22"><div align="right">Descripci&oacute;n</div></td>
      <td><div align="left">
          <input name="txtnomcon" type="text" id="txtnomcon" size="30" maxlength="30" onKeyPress="javascript: ue_mostrar(this,event);">
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
	$li_difconpnom=$io_sno->uf_select_config("SNO","NOMINA","DIFERENCIAR CONCEPTOS NOMINA","0","I");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion(); 	
	$ls_codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");
	$ls_codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");
	$ls_campo=$io_fun_nomina->uf_obtenervalor_get("campo","");
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
		$ls_codconc="%".$_POST["txtcodconc"];
		$ls_nomcon="%".$_POST["txtnomcon"]."%";
		$totrow=0;
		$li_totrow=uf_print($ls_codconc, $ls_nomcon, $ls_codnomdes, $ls_codnomhas, $li_totrow);		
	}
	unset($io_fun_nomina);	
?>
</div>
 <input name="total" type="hidden" id="total" value="<?php print $li_totrow;?>"> 
 <input name="selected" type="hidden" id="selected" value="<?php print $li_selected;?>">
 <input name="codnomdes" type="hidden" id="codnomdes" value="<?php print $ls_codnomdes;?>"> 
 <input name="codnomhas" type="hidden" id="codnomhas" value="<?php print $ls_codnomhas;?>"> 
 <input name="difnom" type="hidden" id="difnom" value="<?php print $li_difconpnom;?>"> 
 <input name="campo" type="hidden" id="campo" value="<?php print $ls_campo;?>"> 
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
	  li_difnom=f.difnom.value;	 
	  campo=f.campo.value;	 
	  parametro="";
	  li_sel=0;	 
	  tipo= fop.tipo.value; 
	  for(i=1;(i<parseInt(li_total,10));i++)	
	  {
		if(li_sel<=parseInt(li_selected,10))
		{
			if(eval("f.chkconcepto"+i+".checked==true"))
			{
				li_sel=eval(li_sel+1);			
				ls_txtcodconc=eval("f.txtcodconc"+i+".value");
				if (li_difnom==1)
				{
					ls_txtcodnom=eval("f.txtcodnom"+i+".value");
					if(parametro=='')
					{
						parametro=ls_txtcodnom+";"+ls_txtcodconc;				
					}
					else
					{
						parametro=parametro+"-"+ls_txtcodnom+";"+ls_txtcodconc;
					}
				}
				else
				{
					if(parametro=='')
					{
						parametro=ls_txtcodconc;				
					}
					else
					{
						parametro=parametro+"-"+ls_txtcodconc;
					}
				}
			}			
		}
		else
		{
			break;
			close();			
		}	
	  }
	  switch (tipo)
	  {
	  		case '':
				campo=eval("fop."+campo);
				campo.value=parametro;
			break;
			
	  		case 'consolidadopagounidad':
				fop.txtcodconc.value=parametro;
			break;
			
	  		case 'listado_ministerio':
				campo=eval("fop."+fop.campo.value);
				campo.value=parametro;
			break;
			
	  		case 'rac_rec':
				campo=eval("fop."+fop.campo.value);
				campo.value=parametro;
			break;
			
	  		case 'fpa':
				fop.txtcodconc.value=parametro;
			break;
			
	  		case 'constanciatrabajo':
				fop.txtconsumcont.value=parametro;
			break;
	  }
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
	codnomdes=f.codnomdes.value;
	codnomhas=f.codnomhas.value;
	campo=f.campo.value;
	difnom=f.difnom.value;
  	f.action="sigesp_snorh_sel_catconcepto.php?codnomdes="+codnomdes+"&codnomhas="+codnomhas+"&campo="+campo+"";
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
			eval("f.chkconcepto"+i+".checked=true");			
		  }		
		  f.selected.value=total; 
	  }
}

function uf_selected(li_i)
 {
 	f=document.form1;
	li_total=f.total.value;
	li_selected=f.selected.value; 
	if(eval("f.chkconcepto"+li_i+".checked==true"))
	{
		li_selected=parseInt(li_selected,10)+1;
	}
 	f.selected.value=li_selected;
 }	


</script>
</html>
