<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
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
	 print "close();";
	 print "opener.document.form1.submit();";
	 print "</script>";		
   }
$la_empresa		  	= $_SESSION["la_empresa"];
$li_loncodestpro1 	= $la_empresa["loncodestpro1"];
$li_loncodestpro2 	= $la_empresa["loncodestpro2"];
$li_loncodestpro3 	= $la_empresa["loncodestpro3"];
$li_loncodestpro4 	= $la_empresa["loncodestpro4"];
$li_estpreing 		= $la_empresa["estpreing"]; 

$li_size1 = $li_loncodestpro1+10;
$li_size2 = $li_loncodestpro2+10;
$li_size3 = $li_loncodestpro3+10;
$li_size4 = $li_loncodestpro4+10;

require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	
$io_include  = new sigesp_include();
$ls_connect  = $io_include->uf_conectar();
$io_msg      = new class_mensajes();
$io_sql      = new class_sql($ls_connect);

if (array_key_exists("operacion",$_POST))
   {
     $ls_operacion  = $_POST["operacion"];
   	 $ls_codestpro1 = $_POST["codestpro1"];
	 $ls_codestpro2 = $_POST["codestpro2"];
	 $ls_codestpro3 = $_POST["codestpro3"];
	 $ls_denestpro1 = $_POST["denestpro1"];
	 $ls_denestpro2 = $_POST["denestpro2"];
	 $ls_denestpro3 = $_POST["denestpro3"];
	 $ls_codestpro4 = $_POST["codestprog3"];
	 $ls_denestpro4 = $_POST["denominacion"];
     $ls_estcla     = $_POST["hidestcla"];
     
	 if (array_key_exists("tipo",$_GET))
	   {
	     $ls_tipo=$_GET["tipo"];
 	   }
	 else
	   {
	     $ls_tipo="";
	   }
   } 
else
   { 
	 $ls_operacion  = "BUSCAR";
	 $ls_codestpro1 = $_GET["codestpro1"];
	 $ls_codestpro2 = $_GET["codestpro2"];
	 $ls_codestpro3 = $_GET["codestpro3"];
	 $ls_denestpro1 = $_GET["denestpro1"];
	 $ls_denestpro2 = $_GET["denestpro2"];
	 $ls_denestpro3 = $_GET["denestpro3"];
	 $ls_codestpro4 = "";
	 $ls_denestpro4 = "";
	 if (array_key_exists("tipo",$_GET))
	   {
	     $ls_tipo=$_GET["tipo"];
 	   }
	 else
	   {
	     $ls_tipo="";
	   }
	 $ls_estcla = $_GET["hidestcla"];
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat�logo de <?php print $la_empresa["nomestpro4"] ?></title>
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
  <p align="center">&nbsp;</p>
  	 <br>
	 <table width="550" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td height="21" colspan="2" class="titulo-celda"><input name="hidestcla" type="hidden" id="hidestcla" value="<?php print $ls_estcla ?>">
          <input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo <?php print $la_empresa["nomestpro4"] ?> </td>
       </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="118" height="21" style="text-align:right"><?php print $la_empresa["nomestpro1"]?></td>
        <td width="380" height="21" style="text-align:left">
            <input name="codestpro1" type="text" id="codestpro1" value="<?php print $ls_codestpro1 ?>" size="<?php print $li_size1 ?>" maxlength="<?php print $li_loncodestpro1 ?>" readonly style="text-align:center">
            <input name="denestprog1" type="hidden" id="denestprog1" value="<?php print $ls_denestpro1 ?>">
        </td>
      </tr>
      <tr>
        <td height="21" style="text-align:right"><?php print $la_empresa["nomestpro2"]?></td>
        <td height="21" style="text-align:left">
          <input name="codestpro2" type="text" id="codestpro2" value="<?php print  $ls_codestpro2 ?>" size="<?php print $li_size2 ?>" maxlength="<?php print $li_loncodestpro2 ?>" readonly style="text-align:center">
          <input name="denestprog2" type="hidden" id="denestprog2" value="<?php print $ls_denestpro2 ?>">
        </td>
      </tr>
      <tr>
        <td height="21" style="text-align:right"><?php print $la_empresa["nomestpro3"]?></td>
        <td height="21" style="text-align:left"><input name="codestpro3" type="text" id="codestpro3" value="<?php print  $ls_codestpro3 ?>" size="<?php print $li_size3 ?>" maxlength="<?php print $li_loncodestpro3 ?>" readonly style="text-align:center">          
		<input name="denestprog3" type="hidden" id="denestprog3" value="<?php print $ls_denestpro3 ?>">
        </td>
      </tr>
      <tr>
        <td height="21"  style="text-align:right">C&oacute;digo</td>
        <td height="21" style="text-align:left"><input name="codestprog3" type="text" id="codestprog3"  size="<?php print $li_size4 ?>" maxlength="<?php print $li_loncodestpro4 ?>" style="text-align:center">        </td>
      </tr>
      <tr>
        <td height="21" style="text-align:right">Denominaci&oacute;n</td>
        <td height="21" style="text-align:left"><input name="denominacion" type="text" id="denominacion"  size="75" maxlength="100"></td>
      </tr>
      <tr>
        <td height="21">&nbsp;</td>
        <td height="21" style="text-align:right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0">Buscar</a></td>
      </tr>
    </table>
	 <div align="center"><br>
<?php
print "<table width=690 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width=130 style=text-align:center>".$la_empresa["nomestpro1"]."</td>";
print "<td width=130 style=text-align:center>".$la_empresa["nomestpro2"]."</td>";
print "<td width=130 style=text-align:center>".$la_empresa["nomestpro3"]."</td>";
print "<td width=130 style=text-align:center>C�digo</td>";
print "<td width=130 style=text-align:center>Denominaci�n</td>";
print "<td width=40  style=text-align:center>Tipo</td>";
print "</tr>";
if ($ls_operacion=="BUSCAR")
	{
		$ls_estcla     = $_GET["estcla"];
		
		$ls_codestpro1 = str_pad($ls_codestpro1,25,0,0);
		$ls_codestpro2 = str_pad($ls_codestpro2,25,0,0);
		$ls_codestpro3 = str_pad($ls_codestpro3,25,0,0);
		$ls_estcla     = $_GET["estcla"];
     	if (!empty($ls_codestpro4))
	    {
		  	$ls_codestpro4 = str_pad($ls_codestpro4,25,0,0);
		} 
		
		if ($li_estpreing==1) 
		{
			$ls_sql=" SELECT distinct(spg_ep4.codestpro4), spg_ep4.codestpro1, spg_ep4.codestpro2, spg_ep4.codestpro3,".
					"	     spg_ep4.denestpro4, spg_ep4.estcla ".
				    "   FROM spg_ep4 LEFT OUTER JOIN spi_cuentas_estructuras   ".
				    "		ON spi_cuentas_estructuras.codemp=spg_ep4.codemp ".
					"	   AND spi_cuentas_estructuras.codestpro1 =spg_ep4.codestpro1 ".
					"	   AND spi_cuentas_estructuras.codestpro2 =spg_ep4.codestpro2 ".
					"	   AND spi_cuentas_estructuras.codestpro3 =spg_ep4.codestpro3 ".
					"	   AND spi_cuentas_estructuras.codestpro4 =spg_ep4.codestpro4 ".
					"	   AND spi_cuentas_estructuras.estcla  =spg_ep4.estcla ".
					"    WHERE spg_ep4.codemp = '".$la_empresa["codemp"]."' ".
					"	   AND spg_ep4.codestpro1 = '".$ls_codestpro1."' ". 
				    "	   AND spg_ep4.codestpro2 = '".$ls_codestpro2."' ".
					"	   AND spg_ep4.codestpro3 = '".$ls_codestpro3."' ".
					"	   AND spg_ep4.codestpro4 like '%".$ls_codestpro4."%' ".
					"      AND denestpro4 like '%".$ls_denestpro4."%' ".
					"	   AND spg_ep4.estcla = '".$ls_estcla."' ".
					"	 ORDER BY spg_ep4.codestpro1,spg_ep4.codestpro2,spg_ep4.codestpro3,spg_ep4.codestpro4 ";			
		}
		else
		{
			$ls_sql=" SELECT distinct(spg_ep4.codestpro4), spg_ep4.codestpro1, spg_ep4.codestpro2, spg_ep4.codestpro3,".
					"	     spg_ep4.denestpro4, spg_ep4.estcla ".
				    "   FROM spg_ep4, spi_cuentas_estructuras   ".
					"  WHERE spg_ep4.codemp = '".$la_empresa["codemp"]."' ".
					"	   AND spg_ep4.codestpro1 = '".$ls_codestpro1."' ". 
				    "	   AND spg_ep4.codestpro2 = '".$ls_codestpro2."' ".
					"	   AND spg_ep4.codestpro3 = '".$ls_codestpro3."' ".
					"	   AND spg_ep4.codestpro4 like '%".$ls_codestpro4."%' ".
					"      AND denestpro4 like '%".$ls_denestpro4."%' ".
					"	   AND spg_ep4.estcla = '".$ls_estcla."' ".
					"	   AND spi_cuentas_estructuras.codemp=spg_ep4.codemp ".
					"	   AND spi_cuentas_estructuras.codestpro1 =spg_ep4.codestpro1 ".
					"	   AND spi_cuentas_estructuras.codestpro2 =spg_ep4.codestpro2 ".
					"	   AND spi_cuentas_estructuras.codestpro3 =spg_ep4.codestpro3 ".
					"	   AND spi_cuentas_estructuras.codestpro4 =spg_ep4.codestpro4 ".
					"	   AND spi_cuentas_estructuras.estcla  =spg_ep4.estcla ".
					"	 ORDER BY spg_ep4.codestpro1,spg_ep4.codestpro2,spg_ep4.codestpro3,spg_ep4.codestpro4 ";			
		}
  		
	 $rs_data=$io_sql->select($ls_sql);
	 if ($rs_data===false)
	    {
	      $io_msg->message("Error en Consulta, Contacte al Administrador del Sistema !!!");
	    }
	 else
	    {
	      $li_numrows = $io_sql->num_rows($rs_data);
	      if ($li_numrows>0)
		     {
			   while ($row=$io_sql->fetch_row($rs_data))
			         {
					   print "<tr class=celdas-blancas>";
					   $ls_codestpro1 = trim(substr($row["codestpro1"],-$li_loncodestpro1));
					   $ls_codestpro2 = trim(substr($row["codestpro2"],-$li_loncodestpro2));
					   $ls_codestpro3 = trim(substr($row["codestpro3"],-$li_loncodestpro3));
					   $ls_codestpro4 = trim(substr($row["codestpro4"],-$li_loncodestpro4));
					   $ls_denestpro4 = $row["denestpro4"]; 
					   $ls_estcla     = $row["estcla"]; 
					   if ($ls_estcla=='P')
					      {
						    $ls_denestcla='Proyecto';
						  }
						  elseif($ls_estcla=='A')
					      {
						    $ls_denestcla='Acci�n';
						  }
				       if ($ls_tipo=="")
				          {
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro4."</a></td>";
				          }
				       if ($ls_tipo=="apertura")
						  {
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_apertura('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_apertura('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_apertura('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_apertura('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro4."</a></td>";
						  }
					   if ($ls_tipo=="progrep")
						  {
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_progrep('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_progrep('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_progrep('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_progrep('$ls_codestpro4','$ls_denestpro4');\">".$ls_codestpro4."</a></td>";
						  }
					   if ($ls_tipo=="reporte")		
						  { 
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro4."</a></td>";
					 	  }
					   if ($ls_tipo=="rephas")		
						  {
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro1."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro2."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro3."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro4."</a></td>";
						  }
					   if ($ls_tipo=="reporteacumdes")		
						  { 
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rep('$ls_codestpro4');\">".$ls_codestpro4."</a></td>";
					 	  }	
						if ($ls_tipo=="reporteacumdes_ma")		
						{ 
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_repma('$ls_codestpro4');\">".$ls_codestpro1."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_repma('$ls_codestpro4');\">".$ls_codestpro2."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_repma('$ls_codestpro4');\">".$ls_codestpro3."</a></td>";
							print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_repma('$ls_codestpro4');\">".$ls_codestpro4."</a></td>";
						}
					   	if ($ls_tipo=="reporteacumhas")		
						  {
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro1."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro2."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro3."</a></td>";
						    print "<td width=130 style=text-align:center><a href=\"javascript: aceptar_rephas('$ls_codestpro4');\">".$ls_codestpro4."</a></td>";
						  }	   
					   print "<td width=130 style=text-align:left>".$ls_denestpro4."</td>";
					   print "<td width=40  style=text-align:center>".$ls_denestcla."</td>";
					   print "</tr>";	
					 }
			 } 
	      else
		     {
			   $io_msg->message("No se han definido registros !!!");
			 }
	    }
}
print "</table>";
?>
</div>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(ls_codestpro4,ls_denestpro4)
  {
    opener.document.form1.codestpro4.value=ls_codestpro4;
	opener.document.form1.denestpro4.value=ls_denestpro4;
	opener.document.form1.codestpro5.value="";
	opener.document.form1.denestpro5.value="";
	close();
  }
  
  function aceptar_apertura(ls_codestpro4,ls_denestpro4)
  {
	opener.document.form1.codestpro4.value=ls_codestpro4;
    opener.document.form1.denestpro4.value=ls_denestpro4;
 	opener.document.form1.operacion.value="CARGAR";
    opener.document.form1.submit();
	close();
  }
  
  function aceptar_progrep(ls_codestpro4,ls_denestpro4)
  {
	opener.document.form1.codestpro4.value=ls_codestpro4;
    opener.document.form1.denestpro4.value=ls_denestpro4;
 	opener.document.form1.operacion.value="CARGAR";
    opener.document.form1.submit();
	close();
  }
  
  function aceptar_apertura(ls_codestpro4,ls_denestpro4)
  {
	opener.document.form1.codestpro4.value=ls_codestpro4;
    opener.document.form1.denestpro4.value=ls_denestpro4;
 	opener.document.form1.operacion.value="CARGAR";
    opener.document.form1.submit();
	close();
  }
  
  function aceptar_rep(ls_codestpro4)
  {
	opener.document.form1.codestpro4.value=ls_codestpro4;
	opener.document.form1.codestpro4.readOnly=true;
	close();
  }
  
  function aceptar_repma(ls_codestpro4)
  {
	opener.document.form1.codestpro4.value=ls_codestpro4;
	opener.document.form1.codestpro4.readOnly=true;
	close();
  }
  
  function aceptar_rephas(ls_codestpro4)
  {
	opener.document.form1.codestpro4h.value=ls_codestpro4;
	opener.document.form1.codestpro4h.readOnly=true;
	close();
  }
  
  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_spi_cat_public_estpro4.php?tipo=<?php print $ls_tipo; ?>";
  f.submit();
  }
</script>
</html>