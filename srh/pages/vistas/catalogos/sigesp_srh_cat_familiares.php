<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();

if(array_key_exists("tipo",$_GET))
	{
		$ls_tipo=$_GET["tipo"];
	}
else
{
		$ls_tipo="";
}	
	
if (isset($_GET["valor_cat"]))
	{ $ls_ejecucion=$_GET["valor_cat"];	}	
	
if (isset($_GET["codper"]))
	{ $ls_codper=$_GET["codper"];	}	

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Familiares</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript"  src="../../../public/js/librerias_comunes.js"></script>
<script type="text/javascript"  src="../../js/sigesp_srh_js_personal.js"></script>


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

</head>

<body onLoad="javascript: doOnLoad();">
<form name="form1" method="post" action="">
  <p align="center">
   
    <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_ejecucion?>" >
	<input name="hidtipo" type="hidden" id="hidtipo" value="<?php print $ls_tipo?>">
  
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Familiares </td>
    </tr>
  </table>
  <br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td width="273" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="227"><div align="right">C&oacute;digo Personal</div></td>
        <td height="22" colspan="2"><div align="left">
          <input name="txtcodper" type="text" id="txtcodper" value="<?php print $ls_codper?>"  size=16 readonly style="text-align:center">
        </div></td>
      </tr>
	 </table>
	<div align="center" id="mostrar" class="oculto1"></div> 
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="fondo-tabla" align="center">
      <tr>
        <td bgcolor="#EBEBEB">&nbsp;</td>
      </tr>
      <tr>
        <td bgcolor="#EBEBEB">&nbsp;</td>
      </tr>
      
      <tr>
        <td align="center" bgcolor="#EBEBEB"><div id="gridbox" align="center" width="600" height="800" style="background-position:center"></div>
		
		</td>
		</tr>
		
	  
    </table>
	
 
</div>
</form>
<p>&nbsp;</p>

</body>

</html>

<script >


        codper=document.form1.txtcodper.value;
        var loadDataURL = "../../php/sigesp_srh_a_personal.php?valor=createXML_familiares";
		var actionURL = "../../php/sigesp_srh_a_personal.php";
	    var img="<img src=../../../public/imagenes/progress.gif> ";
		var mygrid;
		var timeoutHandler;//update will be sent automatically to server if row editting was stoped;
		var rowUpdater;//async. Calls doUpdateRow function when got data from server
		var rowEraser;//async. Calls doDeleteRow function when got confirmation about row deletion
		var authorsLoader;//sync. Loads list of available authors from server to populate dropdown (co)
		var mandFields = [0,1,1,0,0]
		
		
	//initialise (from xml) and populate (from xml) grid
		function doOnLoad()
		{
            divResultado = document.getElementById('mostrar');
			divResultado.innerHTML= img; 
			mygrid = new dhtmlXGridObject('gridbox');
		 	mygrid.setImagePath("../../../public/imagenes/"); 
			//set columns properties
			mygrid.setHeader("C?dula,Nombre,Nexo");
			mygrid.setInitWidths("100,250,160")
			mygrid.setColAlign("center,center,center")
			mygrid.setColTypes("link,ro,ro");
			mygrid.setColSorting("str,str,str")//nuevo  ordenacion
			mygrid.setColumnColor("#FFFFFF,#FFFFFF,#FFFFFF")

			mygrid.loadXML(loadDataURL+"&codper="+codper);
			mygrid.setSkin("xp");
			mygrid.init();
            setTimeout (terminar_buscar,500);
			
		}
		
		function terminar_buscar ()
		{ 
  		    divResultado = document.getElementById('mostrar');
   			divResultado.innerHTML= ''; 
        }	
		
		
		
		
	function aceptar(ls_cedfam,ls_nomfam,ls_apefam,ls_sexfam,ls_fecnacfam,ls_nexfam,ls_hcfam,ls_hcmfam,ls_estfam, ls_cedfamdestino,ls_nomfamdestino,ls_apefamdestino,ls_sexfamdestino,ls_fecnacfamdestino,ls_nexfamdestino,ls_hcfamdestino,ls_hcmfamdestino,ls_estfamdestino,ls_hijesp,ls_hijespdestino,ls_bonjug,ls_bonjugdestino,
					ls_cedula,ls_ceduladestino,ls_estbec,ls_estbecdestino,ls_nivaca,ls_nivacadestino)
	{
		
		ls_tipo = document.form1.hidtipo.value;
		if (ls_tipo=='2')
		{
		  obj=eval("opener.document.form1.txtcedfam1");
		  obj.value=ls_cedfam;
	      obj1=eval("opener.document.form1.txtnomfam1");
	      obj1.value=ls_nomfam+' '+ls_apefam;
		}
		else if (ls_tipo=='3')
		{
		  obj=eval("opener.document.form1.txtcedfam1");
		  obj.value=ls_cedfam;
	      obj1=eval("opener.document.form1.txtnomfam1");
	      obj1.value=ls_nomfam+' '+ls_apefam;
		  obj2=eval("opener.document.form1.hidnexfam");
	      obj2.value=ls_nexfam;
   	      obj3=eval("opener.document.form1.hidsexfam");
	      obj3.value=ls_sexfam;
		  
		}
		else
		{
		 
		  obj=eval("opener.document.form1."+ls_cedfamdestino+"");
		  obj.value=ls_cedfam;
		  obj11=eval("opener.document.form1."+ls_ceduladestino+"");
	      obj11.value=ls_cedula;
		  obj1=eval("opener.document.form1."+ls_nomfamdestino+"");
	      obj1.value=ls_nomfam;
	   	  obj2=eval("opener.document.form1."+ls_apefamdestino+"");
		  obj2.value=ls_apefam;
		  obj3=eval("opener.document.form1."+ls_sexfamdestino+"");
		  obj3.value=ls_sexfam;
		  obj4=eval("opener.document.form1."+ls_fecnacfamdestino+"");
		  obj4.value=ls_fecnacfam;
		  obj5=eval("opener.document.form1."+ls_nexfamdestino+"");
		  obj5.value=ls_nexfam;
		  
		  obj6=eval("opener.document.form1."+ls_hcfamdestino+"");
		  if (ls_hcfam=='1')
		  {obj6.checked=true;}
		  else
		  {obj6.checked=false;}
		  
		  obj7=eval("opener.document.form1."+ls_hcmfamdestino+"");
		  if (ls_hcmfam=='1')
		  {obj7.checked=true;}
		  else
		  {obj7.checked=false;}
		  
		  
		  obj8=eval("opener.document.form1."+ls_estfamdestino+"");
		  if (ls_estfam=='1')
		  {obj8.checked=true;}
		  else
		  {obj8.checked=false;}
		  
		  obj9=eval("opener.document.form1."+ls_hijespdestino+"");
		  if (ls_hijesp=='1')
		  {obj9.checked=true;}
		  else
		  {obj9.checked=false;}
		  
		  obj10=eval("opener.document.form1."+ls_bonjugdestino+"");
		  if (ls_bonjug=='1')
		  {obj10.checked=true;}
		  else
		  {obj10.checked=false;}
		  
		   obj11=eval("opener.document.form1."+ls_estbecdestino+"");
		  if (ls_estbec=='1')
		  {obj11.checked=true;}
		  else
		  {obj11.checked=false;}
		  
		  obj12=eval("opener.document.form1."+ls_nivacadestino+"");
		  obj12.value=ls_nivaca;
		  
		  opener.document.form1.hidguardar_fam.value='modificar';
		 }
		  
		close();
	}
		
		
	
</script>