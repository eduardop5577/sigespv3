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
	
if (isset($_GET["valor_cat"]))
	{ $ls_ejecucion=$_GET["valor_cat"];	}
else
{ $ls_ejecucion="";	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Personal</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script type="text/javascript"  src="../../../public/js/librerias_comunes.js"></script>
<script type="text/javascript"  src="../../js/sigesp_srh_js_cat_personal.js"></script>
<script type="text/javascript"  src="../../../../shared/js/number_format.js"></script>
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

<body onLoad="doOnLoad()">
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
    <input name="txtempresa" type="hidden" id="txtempresa">
    <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_ejecucion?>">
	<input name="hidtipo" type="hidden" id="hidtipo" value="<?php print $ls_tipo?>">
  
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Personal</td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td width="119"><div align="right">C&oacute;digo Personal</div></td>
        <td height="22" colspan="2"><div align="left">
          <input name="txtcodper" type="text" id="txtcodper"   size=16 onKeyUp="javascript: ue_validarnumero(this); " onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      
      
       
   <tr>
        <td><div align="right">C&eacute;dula Personal</div></td>
        <td height="22" colspan="2"><div align="left">    
		    <input name="txtcedper" type="text" id="txtcedper" size=16  onKeyUp="javascript: ue_validarnumero(this);" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>

  </tr>
   <tr>
        <td><div align="right">Nombre Personal</div></td>
        <td height="22" colspan="2"><div align="left">          
		  <input name="txtnomper" type="text" id="txtnomper" size=40 onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
 
  </tr>
   <tr class="formato-blanco"> 
  <td><div align="right">Apellido Personal</div></td>
        <td height="22" colspan="2"><div align="left">      
		    <input name="txtapeper" type="text" id="txtapeper" size=40  onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
         
  </tr>
  <tr class="formato-blanco"> 
  <td><div align="right">Nro. Expediente</div></td>
        <td height="22" colspan="2"><div align="left">      
		    <input name="txtnumexp" type="text" id="txtnumexp" size=40  onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
         
  </tr>  
      <tr>
        <td>&nbsp;</td>
            </tr>
			<tr>
    <td>&nbsp;</td>
   
    <td width="182"><div align="right"><a href="javascript: Limpiar_busqueda();"> <img src="../../../public/imagenes/nuevo.gif" alt="Limpiar" width="15" height="15" border="0">Limpiar</a></div></td> 
    <td width="199"><div align="right"><a href="javascript: Buscar()"><img src="../../../../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0">Haga click aqu&iacute; para Buscar</a></div></td>
    
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
<p>&nbsp;</p>
</body>

</html>