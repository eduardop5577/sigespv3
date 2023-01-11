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
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.formulario.submit();";
		print "</script>";		
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	require_once("class_folder/sigesp_soc_c_analisis_cotizacion.php");
	$io_analisis=new sigesp_soc_c_analisis_cotizacion();
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_funciones=new class_funciones();	
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();	
	if($_GET["tipsolcot"])
		$ls_tipsolcot="Bienes";
	else
		$ls_tipsolcot="Servicios";
	$li_totalcotizaciones=$_GET["totalcotizaciones"];
	$la_montos=$io_analisis->uf_select_items_cotizacion($la_montos);
	$la_items=array_keys($la_montos);
	$li_totalitems=count((array)$la_items);
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>An&aacute;lisis de Precios</title>
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
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey))
		{
			window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ return false;} 
		} 
	}
</script>

</head>
<script type="text/javascript"  src="js/funcion_soc.js" ></script>
<body>
<form name="formulario" method="post" action="">
  <br> <br> <br>
  <table height="107" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco" style=" overflow:scroll">
    <tr class="titulo-nuevo">
      <th width="512" height="13" colspan="8" scope="col">
      <div align="center">An&aacute;lisis de Precios </div></th>
    </tr>
    <tr>
      <th height="28" colspan="8" scope="col"><div align="center">
        <br><br><br>
		<table border="1">
          <tr class="titulo-celda">
            <td width="106"><?php print $ls_tipsolcot?></td>
            <?php
				for($li_i=1;$li_i<=$li_totalcotizaciones;$li_i++)
				{
					$ls_nompro=$_GET["nompro".$li_i];
					print "<td width=100>".$ls_nompro."</td>";
				}
			?>           
          </tr>
          <?php
		 		for($li_i=0;$li_i<$li_totalitems;$li_i++)
				{
					print "<tr>";
					$ls_item=$la_items[$li_i];
					print "<td class=titulo-celda>".$ls_item."</td>";
					$li_min=min($la_montos[$ls_item]);//calculando el minimo
            		for($li_j=1;$li_j<=$li_totalcotizaciones;$li_j++)
					{
						$ls_nompro=$_GET["nompro".$li_j];
						$ls_monto=$la_montos[$ls_item][$ls_nompro];	
						if($li_min==$ls_monto)					
							print "<td class=celdas-verdes width=100><div align=right><b>".number_format($ls_monto,2,",",".")."</b></div></td>";
						else
							print "<td class=formato-blanco width=100><div align=right>".number_format($ls_monto,2,",",".")."</div></td>";
					}
            		print "</tr>";         
				}  
		  ?>		        
        </table>
      </div>
      <div align="center"></div></th>
    </tr>
    <tr>
      <th height="25" colspan="8" scope="col">&nbsp;</th>
    </tr>
  </table>

  <div align="center"></div>
</form>      
</body>
<script type="text/javascript"  src="js/funcion_soc.js" ></script>
<script language="JavaScript">
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>