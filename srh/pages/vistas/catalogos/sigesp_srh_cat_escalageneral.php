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
if (isset($_GET["valor_cat"]))
	{ $ls_ejecucion=$_GET["valor_cat"];	}
else
	{$ls_ejecucion="";}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Escala General</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript"  src="../../js/sigesp_srh_js_escalageneral.js"></script>
<script type="text/javascript"  src="../../../public/js/librerias_comunes.js"></script>
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

<body onLoad="doOnLoad()">
<form name="form1" method="post" action="">
  <p align="center">
    
    <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_ejecucion ?>">
   
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo  de Escala General</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodesc" type="text" id="txtcodesc" onKeyUp="javascript: Buscar()">
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Denominaci&oacute;n</div></td>
        <td height="22"><div align="left">          <input name="txtdenesc" type="text" id="txtdenesc" size="60" maxlength="254" onKeyUp="javascript: Buscar()">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        
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
        <td align="center" bgcolor="#EBEBEB"><div id="gridbox" align="center" width="500" height="800" style="background-position:center"></div></td>
      </tr>
    </table>

  <br>


</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script>
	
		var loadDataURL = "../../php/sigesp_srh_a_escalageneral.php?valor=createXML";
		var actionURL = "../../php/sigesp_srh_a_escalageneral.php";
	    var img="<img src=\"../../../public/imagenes/progress.gif\"> ";
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
			mygrid.setHeader("Codigo,Denominacion, Escala");
			mygrid.setInitWidths("120,300,80")
			mygrid.setColAlign("center,center,center")
			mygrid.setColTypes("link,ro,ro");
			mygrid.setColSorting("str,str,str")//nuevo  ordenacion
			mygrid.setColumnColor("#FFFFFF,#FFFFFF,#FFFFFF")

			mygrid.loadXML(loadDataURL);
			mygrid.setSkin("xp");
			mygrid.init();
			setTimeout (terminar_buscar,500);
			
		}
		
		function terminar_buscar ()
		{ 
  		    divResultado = document.getElementById('mostrar');
   			divResultado.innerHTML= ''; 
        }
		
		
		
		function Buscar()
		{
	
		   codesc=document.form1.txtcodesc.value;
		   denesc=document.form1.txtdenesc.value;
		   mygrid.clearAll();
		   divResultado = document.getElementById('mostrar');
		   divResultado.innerHTML= img;
		   mygrid.loadXML("../../php/sigesp_srh_a_escalageneral.php?valor=buscar"+"&txtcodesc="+codesc+"&txtdenesc="+denesc);
           setTimeout (terminar_buscar,500);
			
		}
		
		function aceptar(ls_codesc,ls_denesc,ls_coddestino,ls_dendestino,ls_valini,ls_valfin,ls_valinidestino,ls_valfindestino)
		{
			 if (opener.document.form1.hidcontrol3.value=="1")
			 {			 
			   obj=eval("opener.document.form1."+ls_coddestino+"");
			   obj.value=ls_codesc;
			   obj1=eval("opener.document.form1."+ls_dendestino+"");
			   obj1.value=ls_denesc;
			  
			 }
			
			else 
			{		
				obj=eval("opener.document.form1."+ls_coddestino+"");
				obj.value=ls_codesc;
				obj1=eval("opener.document.form1."+ls_dendestino+"");
				obj1.value=ls_denesc;
				obj2=eval("opener.document.form1."+ls_valinidestino+"");
				obj2.value=ls_valini;
				obj3=eval("opener.document.form1."+ls_valfindestino+"");
				obj3.value=ls_valfin;
				opener.document.form1.txtcodesc.readOnly=true;
				ls_ejecucion=document.form1.hidstatus.value;
				if(ls_ejecucion=="1")
				{	
				opener.document.form1.hidstatus.value="C";
				}
				opener.document.form1.operacion.value="BUSCARDETALLE";
				opener.document.form1.action="../pantallas/sigesp_srh_d_escalageneral.php";
				opener.document.form1.existe.value="TRUE";
				opener.document.form1.hidguardar.value="modificar";			
				opener.document.form1.submit();	
				
			}	
				
				
				
				
				
				close();	
				
		
			
			
			
			
		}
	
	
		function nextPAge(val)
		{
			grid.clearAll(); //clear existing data
			grid.loadXML("some_url.php?page="+val);
		}


	</script>
	
</html>

