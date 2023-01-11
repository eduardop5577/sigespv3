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
	function uf_print($as_codnom, $as_desnom)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
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
                $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("../shared/class_folder/grid_param.php");
		$grid = new grid_param();		
		$ls_criterio="";
		if ($as_codnom!="")
		{
			$ls_criterio .=" AND sno_dt_spg.codnom LIKE  '".$as_codnom."'";
		}
		if ($as_desnom!="")
		{
			$ls_criterio .=" AND sno_nomina.desnom LIKE '".$as_desnom."'";
		}
                $title[1]="Todos <input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); >";	
                $title[2]="Nomina"; 
                $title[3]="Periodo"; 
                $title[4]="Descripción";
		
		$ls_sql="SELECT sno_dt_spg.codnom, sno_dt_spg.codperi, MAX(sno_nomina.desnom) AS desnom ". 
		        "  FROM sno_dt_spg, sno_nomina, sss_permisos_internos ".
		        " WHERE sno_dt_spg.codemp='".$ls_codemp."' ".
		        "   AND sno_dt_spg.estatus=0 ".
                        $ls_criterio.
		        "   AND sno_dt_spg.codemp=sno_nomina.codemp ".
		        "   AND sno_dt_spg.codnom=sno_nomina.codnom ".
                        "    AND sss_permisos_internos.codsis='SNO'".
                        "    AND sss_permisos_internos.enabled=1".
                        "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
                        "    AND sno_nomina.codemp = sss_permisos_internos.codemp ".
                        "    AND sno_nomina.codnom = sss_permisos_internos.codintper ".                        
                        " GROUP BY sno_dt_spg.codnom, sno_dt_spg.codperi ".
                        " ORDER BY sno_dt_spg.codnom, sno_dt_spg.codperi ";
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
                        $ls_codnom=$rs_data->fields["codnom"];
                        $ls_codperi=$rs_data->fields["codperi"];
                        $ls_desnom=$rs_data->fields["desnom"];
                        $object[$ai_totrow][1]="<input name=chknomina".$ai_totrow." type=checkbox id=chknomina".$ai_totrow." value=1 class=sin-borde onClick=javascript:uf_selected('".$ai_totrow."');>";
                        $object[$ai_totrow][2]="<input type=text name=txtcodnom".$ai_totrow." value='".$ls_codnom."' id=txtcodnom".$ai_totrow." class=sin-borde readonly style=text-align:center size=5 maxlength=5 >";		
                        $object[$ai_totrow][3]="<input type=text name=txtcodperi".$ai_totrow." value='".$ls_codperi."' id=txtcodperi".$ai_totrow." class=sin-borde readonly style=text-align:center size=4 maxlength=4 >";		
                        $object[$ai_totrow][4]="<input type=text name=txtdesnom".$ai_totrow." value='".$ls_desnom."' id=txtdesnom".$ai_totrow." class=sin-borde readonly style=text-align:left size=50 maxlength=50>";	
                        $ai_totrow++;
                        $rs_data->MoveNext();
                    }
                    $object[$ai_totrow][1]="<input name=chknomina".$ai_totrow." type=checkbox id=chknomina".$ai_totrow." value=1 class=sin-borde onClick=javascript:uf_selected('".$ai_totrow."');>";
                    $object[$ai_totrow][2]="<input type=text name=txtcodnom".$ai_totrow." value='' id=txtcodnom".$ai_totrow." class=sin-borde readonly style=text-align:center size=5 maxlength=5 >";		
                    $object[$ai_totrow][3]="<input type=text name=txtcodperi".$ai_totrow." value='' id=txtcodperi".$ai_totrow." class=sin-borde readonly style=text-align:center size=4 maxlength=4 >";		
                    $object[$ai_totrow][4]="<input type=text name=txtdesnom".$ai_totrow." value='' id=txtdesnom".$ai_totrow." class=sin-borde readonly style=text-align:left size=50 maxlength=50>";	
                    print "<div align = 'center'>";
                    $grid->makegrid($ai_totrow,$title,$object,500,'Catalogo de Nóminas','gridnominas');	
                    print "</div>";
		}
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
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
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Nóminas </td>
    </tr>
  </table>
  <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
    <tr>
      <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
      <td width="431"><div align="left">
          <input name="txtcodnom" type="text"  id="txtcodnom" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">
      </div></td>
    </tr>
    <tr>
      <td height="22"><div align="right">Descripci&oacute;n</div></td>
      <td><div align="left">
          <input name="txtdesnom" type="text" id="txtdesnom" size="30" maxlength="30" onKeyPress="javascript: ue_mostrar(this,event);">
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
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion(); 	
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
		$ls_codnom="%".$_POST["txtcodnom"]."%";
		$ls_desnom="%".$_POST["txtdesnom"]."%";
		$totrow=0;
		$li_totrow=uf_print($ls_codnom, $ls_desnom);		
	}
	unset($io_fun_nomina);	
?>
</div>
 <input name="total" type="hidden" id="total" value="<?php print $li_totrow;?>"> 
 <input name="selected" type="hidden" id="selected" value="<?php print $li_selected;?>">
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
	  for(i=1;(i<parseInt(li_total,10));i++)	
	  {
		if(li_sel<=parseInt(li_selected,10))
		{
			if(eval("f.chknomina"+i+".checked==true"))
			{
				li_sel=eval(li_sel+1);			
				ls_txtcodnom=eval("f.txtcodnom"+i+".value");
				ls_txtcodperi=eval("f.txtcodperi"+i+".value");
                                if(parametro=='')
                                {
                                        parametro="*"+ls_txtcodnom+ls_txtcodperi+"*";				
                                }
                                else
                                {
                                        parametro=parametro+"-*"+ls_txtcodnom+ls_txtcodperi+"*";
                                }
			}			
		}
		else
		{
			break;
			close();			
		}	
	  }
            campo=eval("fop.txtnominas");
            campo.value=parametro;
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
  	f.action="sigesp_snorh_sel_catnominaperiodo.php";
  	f.submit();
}


function uf_select_all()
{
	  f=document.form1;
	  fop=opener.document.form1;
	  total=f.total.value; 
	  sel_all=f.chkall.value;
          li_selected=f.selected.value;
          li_selected=parseInt(li_selected,10)+1;
          if (li_selected<total)
	  {
            for(i=1;i<total;i++)	
            {
                eval("f.chknomina"+i+".checked=true");			
            }		
            f.selected.value=total; 
	  }
          else
          {
            for(i=1;i<total;i++)	
            {
                eval("f.chknomina"+i+".checked=false");			
            }		
            f.selected.value=0;               
          }
}

function uf_selected(li_i)
 {
 	f=document.form1;
	li_total=f.total.value;
	li_selected=f.selected.value; 
	if(eval("f.chknomina"+li_i+".checked==true"))
	{
		li_selected=parseInt(li_selected,10)+1;
	}
 	f.selected.value=li_selected;
 }	


</script>
</html>
