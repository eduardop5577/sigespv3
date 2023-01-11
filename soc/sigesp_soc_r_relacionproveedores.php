<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
if (!array_key_exists("la_logusr",$_SESSION))
   {
	 print "<script language=JavaScript>";
	 print "location.href='sigesp_inicio_sesion.php'";
	 print "</script>";		
   }
	require_once("class_folder/class_funciones_soc.php");
	$io_soc=new class_funciones_soc();
//	$ls_reporte=$io_soc->uf_select_config("RPC","REPORTE","LISTADO_PROVEEDORES","sigesp_rpc_rpp_proveedor.php","C");
	$ls_reporte="sigesp_soc_rpp_listado_orden_compra_proveedor.php";
	unset($io_soc);

$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Relacion de Compras a Proveedores</title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_soc.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<meta http-equiv="" content="text/html; charset=iso-8859-1"><meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Type" content="text/html; charset=">
<link href="css/rpc.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:hover {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style></head>
<body>
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr> 
    <td height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td height="20" class="cd-menu">
	<table width="778" border="0" align="center" cellpadding="0" cellspacing="0">
			
          <td width="423" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Ordenes de Compra</td>
			<td width="349" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  	  <tr>
	  	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	    <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>

      </table></td>
  </tr>
  <tr>
    <td height="20" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" bgcolor="#FFFFFF" class="toolbar">&nbsp;</td>
  </tr>
  <tr> 
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a>
	                                                  <a href="javascript:ue_openexcel();"><img src="../shared/imagebank/tools20/excel.jpg" alt="Exportar a Excel" title="Exportar a Excel" width="20" height="20" border="0"></a><a href="sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda" /></a></td>
  </tr>
</table>
  <?php
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$io_in=new sigesp_include();
$con=$io_in->uf_conectar();

require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
$io_ds=new class_datastore();

require_once("../base/librerias/php/general/sigesp_lib_sql.php");
$io_sql=new class_sql($con);

require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
$io_msg=new class_mensajes();

require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_funcion=new class_funciones(); 

require_once("../shared/class_folder/grid_param.php");
$grid=new grid_param();


$la_emp=$_SESSION["la_empresa"];
if(array_key_exists("operacion",$_POST))
{
	$ls_operacion=$_POST["operacion"];
}
else
{
	$ls_operacion="";	
}
if (array_key_exists("txtcodprov1",$_POST))
   {
     $ls_codprov1=$_POST["txtcodprov1"];	   
   }
else
   {
     $ls_codprov1="";
   }
if (array_key_exists("txtcodprov2",$_POST)) 
   {  
     $ls_codprov2 =$_POST["txtcodprov2"];	  
   }
else
   {
     $ls_codprov2="";
  }
if  (array_key_exists("radioorden",$_POST))
	{
	  $li_orden=$_POST["radioorden"];
    }
else
	{
	  $li_orden="0";
	}
if  (array_key_exists("radiocategoria",$_POST))
	{
	  $ls_tipo=$_POST["radiocategoria"];
    }
else
	{
	  $ls_tipo="P";
	}			
if (array_key_exists("total",$_POST)) 
   {
     $totrow=$_POST["total"];	   
   }
else
   {
     $totrow="";
   }
if (array_key_exists("hidcodesp",$_POST)) 
   {
     $ls_codigoesp=$_POST["hidcodesp"];	   
   }
else
   {
     $ls_codigoesp="";
   } 
if	(array_key_exists("cmbespecialidad",$_POST))
	{
	  $ls_especialidad=$_POST["cmbespecialidad"];
    }
else
	{
	  $ls_especialidad="";
	}   
?>
</div> 
<p>&nbsp;</p>
<form name="formulario" method="post" action="">
  <table width="442" height="18" border="0" align="center" cellpadding="1" cellspacing="1">
  </table>
  <table width="497" height="268" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
      <td width="495" height="22" align="center" class="titulo-celdanew"><span class="titulo-celdanew">Relacion de Compras  a Proveedores</span></td>
    </tr>
    <tr>
      <td height="50" align="center"><div align="left">
        <table width="430" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="4" style="text-align:left"><strong>Proveedor</strong></td>
          </tr>
          <tr>
            <td width="63" style="text-align:right">Desde</td>
            <td width="171" style="text-align:left"><input name="txtcodprodes" type="text" id="txtcodprodes" value="<?php print $ls_codprodes ?>" size="20" maxlength="15"  style="text-align:center "  onblur="javascript:rellenar_cad(this.value,15,this)" onkeypress="return keyRestrict(event,'1234567890');" />
              <a href="javascript: ue_catalogo('sigesp_soc_cat_proveedor.php?tipo=REPDES');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar hasta..." name="buscar1" width="15" height="15" border="0"  id="buscar1" /></a></td>
            <td width="44" style="text-align:right">Hasta</td>
            <td width="154" style="text-align:left"><input name="txtcodprohas" type="text" id="txtcodprohas" value="<?php print $ls_codprohas ?>" size="20" maxlength="15"  style="text-align:center"  onblur="javascript:rellenar_cad(this.value,15,this)"  onkeypress="return keyRestrict(event,'1234567890');" />
              <a href="javascript: ue_catalogo('sigesp_soc_cat_proveedor.php?tipo=REPHAS');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar desde..." name="buscar2" width="15" height="15" border="0" id="buscar2" /></a></td>
          </tr>
        </table>
      </div>        </td>
    </tr>
    <tr>
      <td height="13" align="center"><div align="center">Monto Bs. 
        <input name="txtmontot" type="text" id="txtmontot"  onKeyPress="return(ue_formatonumero(this,'.',',',event));" style="text-align:right">
      </div></td>
    </tr>
    <tr>
      <td height="13" align="center">&nbsp;</td>
    </tr>
    <tr>
      <td height="47" align="center"><div align="right" class="style1 style14">
        <table width="430" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="4" style="text-align:left"><strong>Fecha</strong></td>
          </tr>
          <tr>
            <td width="63" style="text-align:right">Desde</td>
            <td width="171" style="text-align:left"><input name="txtfecordcomdes" type="text" id="txtfecordcomdes" value="<?php print $ls_fecordcomdes ?>" size="12" maxlength="10"  style="text-align:left"  datepicker="true" onkeypress="currencyDate(this);" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"/>              </td>
            <td width="44" style="text-align:right">Hasta</td>
            <td width="154" style="text-align:left"><input name="txtfecordcomhas" type="text" id="txtfecordcomhas" value="<?php print $ls_fecordcomhas ?>" size="12" maxlength="10"  style="text-align:left"  datepicker="true" onkeypress="currencyDate(this);" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"/>              </td>
          </tr>
        </table>
      </div>        <div align="right" class="style1 style14"></div>        <div align="left">          </div></td>
    </tr>
    <tr>
      <td height="36" align="left"><div align="center">
        <input name="chkut" type="checkbox" class="sin-borde" id="chkut" value="1" checked>
      Compras Mayores a 2500 U.T. </div></td>
    </tr>
    <tr>
      <td height="22" align="left"><span class="style14"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Especialidad<?php
		 //Llenar Combo Banco
		 $ls_sql=" SELECT * FROM rpc_especialidad where codesp<>'---' ORDER BY denesp";
		 $rs_especialidad=$io_sql->select($ls_sql);
		 ?>
              <select name="cmbespecialidad" id="cmbespecialidad"  onChange="document.formulario.hidcodesp.value=document.formulario.cmbespecialidad.value" style="width:150px ">
                <option value="---" selected>---seleccione---</option>
         <?php
		 while ($row=$io_sql->fetch_row($rs_especialidad))
		       {
		         $ls_codesp=$row["codesp"];
		         $ls_denesp=$row["denesp"];
		         if (($ls_codesp==$ls_especialidad)&&($ls_especialidad!=""))
		            {
		              print "<option value='$ls_codesp' selected>$ls_denesp</option>";
		            }
		         else
		            {
		              print "<option value='$ls_codesp'>$ls_denesp</option>";
		            }
		        } 
		 ?>
              </select>
         <input name="hidcodesp" type="hidden"  id="hidcodesp" value="<?php print $ls_codigoesp ?>">
         <input name="hidrango" type="hidden" id="hidrango">
      </span></div>      
      <input name="operacion"   type="hidden"   id="operacion"   value="<?php print $ls_operacion;?>">      </td>
    </tr>
    <tr>
      <td height="65" align="center"><div align="left">
        <table width="430" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
		    <td colspan="3"><span class="style14"><strong>Ordenado Por</strong></span></td>
			</tr>
		  <tr>
            <td width="93" height="27"><div align="right"><span class="style1">
              <?php 	 
      if (($li_orden=="0")||($li_orden==""))
	     {
		    $ls_codigo   ="checked";		
		    $ls_nombre   ="";
         }
	   else
	     {
 	       $ls_codigo  ="";
		   $ls_nombre  ="checked";
		 }	
	  ?>
              C&oacute;digo
              <input name="radioorden" type="radio" value="0" checked  <?php print $ls_codigo ?>>
              </span></div></td>
            <td width="153">&nbsp;</td>
            <td width="166">Nombre
              <input name="radioorden" type="radio" value="1"  <?php print $ls_nombre ?>></td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
  <div align="left"></div>
  <p align="center">
<input name="total" type="hidden" id="total" value="<?php print $totrow;?>">
            <input name="reporte" type="hidden" id="reporte" value="<?php print $ls_reporte;?>">
</p>
</form>      
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function ue_catalogo(ls_catalogo)
{
	// abre el catalogo que se paso por parametros
	window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=850,height=400,left=50,top=50,location=no,resizable=yes");
}



function uf_catalogoprov()
{
    f=document.formulario;
    pagina="sigesp_catdin_prove.php";
    window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no");
}

function rellenar_cad(cadena,longitud,objeto)
{
	var mystring=new String(cadena);
	cadena_ceros="";
	lencad=mystring.length;

	total=longitud-lencad;
	if (cadena!="")
	   {
		for (i=1;i<=total;i++)
			{
			  cadena_ceros=cadena_ceros+"0";
			}
		cadena=cadena_ceros+cadena;
		if (objeto=="txtcodprov1")
		   {
			 document.formulario.txtcodprov1.value=cadena;
		   }
		 else
		   {
			 document.formulario.txtcodprov2.value=cadena;
		   }  
        }
}

function ue_imprimir()
{
	f= document.formulario;
	ls_codprodes    = f.txtcodprodes.value;
	ls_codprohas    = f.txtcodprohas.value;
	ls_fecordcomdes = f.txtfecordcomdes.value;
	ls_fecordcomhas = f.txtfecordcomhas.value;
	codigoesp= f.cmbespecialidad.value;
	montot= f.txtmontot.value;
	
	if (codigoesp=='---')
	   {
		  //codigoesp='';
	   }
	if (f.radioorden[0].checked==true)
	   {
		 li_orden ="rpc_proveedor.cod_pro";
	   }
	else
	   {
		 li_orden = "rpc_proveedor.nompro";
	   }
	if (f.chkut.checked==true)
	   {
		 ls_unitri=1;
	   }
	else
	{
		ls_unitri=0;
	}
	reporte=f.reporte.value;
	pagina="reportes/"+reporte+"?orden="+li_orden+"&codprodes="+ls_codprodes+"&codprohas="+ls_codprohas+"&hidcodesp="+codigoesp+"&fecordcomdes="+ls_fecordcomdes+"&ls_fecordcomhas="+ls_fecordcomhas+"&unitri="+ls_unitri+"&montot="+montot;
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
}

function ue_openexcel()
{
	f= document.formulario;
	ls_codprodes    = f.txtcodprodes.value;
	ls_codprohas    = f.txtcodprohas.value;
	ls_fecordcomdes = f.txtfecordcomdes.value;
	ls_fecordcomhas = f.txtfecordcomhas.value;
	codigoesp= f.cmbespecialidad.value;
	montot= f.txtmontot.value;
	
	if (codigoesp=='---')
	   {
		  //codigoesp='';
	   }
	if (f.radioorden[0].checked==true)
	   {
		 li_orden ="rpc_proveedor.cod_pro";
	   }
	else
	   {
		 li_orden = "rpc_proveedor.nompro";
	   }
	if (f.chkut.checked==true)
	   {
		 ls_unitri=1;
	   }
	else
	{
		ls_unitri=0;
	}
	pagina="reportes/sigesp_soc_rpp_listado_orden_compra_proveedor_excel.php?orden="+li_orden+"&codprodes="+ls_codprodes+"&codprohas="+ls_codprohas+"&hidcodesp="+codigoesp+"&fecordcomdes="+ls_fecordcomdes+"&ls_fecordcomhas="+ls_fecordcomhas+"&unitri="+ls_unitri+"&montot="+montot;
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
}
function currencyDate(date)
{ 
	ls_date=date.value;
	li_long=ls_date.length;
	if (li_long==2)
	   {
	     ls_date   = ls_date+"/";
	 	 ls_string = ls_date.substr(0,2);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1)&&(li_string<=31))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value="";
			}
			
	   }
	if (li_long==5)
	   {
	     ls_date   = ls_date+"/";
		 ls_string = ls_date.substr(3,2);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1)&&(li_string<=12))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value=ls_date.substr(0,3);
			}
	   }
	if (li_long==10)
	   {
	     ls_string = ls_date.substr(6,4);
		 li_string = parseInt(ls_string);
		 if ((li_string>=1900)&&(li_string<=2090))
			{
			  date.value=ls_date;
			}
		 else
			{
			  date.value=ls_date.substr(0,6);
			}
	   }
} 

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_soc_reporte_relacioncompro.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

</script>
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>

</html>