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
$li_estmodest     	= $la_empresa["estmodest"];
$li_estpreing 		= $la_empresa["estpreing"]; 
$li_loncodestpro1 	= $la_empresa["loncodestpro1"];
$li_loncodestpro2 	= $la_empresa["loncodestpro2"];

$li_size1 = $li_loncodestpro1+10;
$li_size2 = $li_loncodestpro2+10;

require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
$io_include = new sigesp_include();
$ls_conect  = $io_include->uf_conectar();
$io_msg     = new class_mensajes();
$io_sql     = new class_sql($ls_conect);

if (array_key_exists("operacion",$_POST))
   {
	 $ls_operacion  = $_POST["operacion"];
	 $ls_codestpro1 = $_POST["codestpro1"];
	 $ls_denestpro1 = $_POST["denestpro1"];
	 $ls_codestpro2 = $_POST["codestprog2"];
	 $ls_denestpro2 = $_POST["denominacion"];
     $ls_estcla     = $_POST["hidestcla"];
   }
else
   {
	 $ls_operacion  = "BUSCAR";
	 $ls_codestpro1 = $_GET["codestpro1"];
	 if(array_key_exists("denestpro1",$_GET))
	 {
		$ls_denestpro1=$_GET["denestpro1"];
	 }
	 else
	 {
		$ls_denestpro1="";
	 }
	 //$ls_estcla     = $_GET["hidestcla"]; 
	 if(array_key_exists("estcla",$_GET))
	 {
		$ls_estcla=$_GET["estcla"];
	 }
	 else
	 {
		$ls_estcla="";
	 }
	 //$ls_estcla     = $_GET["estcla"];
	 $ls_codestpro2 = "";
	 $ls_denestpro2 = "";
	 
   }
if(array_key_exists("tipo",$_GET))
{
	$ls_tipo=$_GET["tipo"];
}
else
{
	$ls_tipo="";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat?logo de <?php print $la_empresa["nomestpro2"] ?></title>
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
  	 <table width="550" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr class="titulo-celda">
        <td height="21" colspan="2"><input name="hidestcla" type="hidden" id="hidestcla" value="<?php print $ls_estcla ?>">
          <input name="operacion" type="hidden" id="operacion">
        Cat&aacute;logo <?php print $la_empresa["nomestpro2"] ?></td>
       </tr>
      <tr>
        <td height="13">&nbsp;</td>
        <td height="13">&nbsp;</td>
      </tr>
      <tr>
        <td width="137" height="21"><div align="right"><?php print $la_empresa["nomestpro1"]?></div></td>
        <td width="461" height="21"><div align="left">
          <input name="codestpro1" type="text" id="codestpro1" value="<?php print $ls_codestpro1 ?>" size="22" maxlength="20" readonly style="text-align:center">        
          <input name="denestpro1" type="text" class="sin-borde" id="denestpro1" size="50" value="<?php print $ls_denestpro1 ?>" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="21" style="text-align:right">C&oacute;digo</td>
        <td height="21" style="text-align:left"><input name="codestprog2" type="text" id="codestprog2" size="<?php print $li_size2 ?>" maxlength="<?php print $li_loncodestpro2 ?>"  style="text-align:center">
        </td>
      </tr>
      <tr>
        <td height="21" style="text-align:right">Denominaci&oacute;n</td>
        <td height="21" style="text-align:left"><input name="denominacion" type="text" id="denominacion" size="72" maxlength="100" style="text-align:left">
        </td>
      </tr>
      <tr>
        <td height="21">&nbsp;</td>
        <td height="21"><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
<div align="center"><br>
<?php
print "<table width=600 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
print "<tr class=titulo-celda>";
print "<td width=160 style=text-align:center>".$la_empresa["nomestpro1"]."</td>";
print "<td width=160 style=text-align:center>C?digo</td>";
print "<td width=350 style=text-align:center>Denominaci?n</td>";
print "<td width=40 style=text-align:center>Tipo</td>";
print "</tr>";
if ($ls_operacion=="BUSCAR")
{
	$ls_codestpro1 = str_pad($ls_codestpro1,25,0,0);
	if (!empty($ls_codestpro2))
	{
		$ls_codestpro2 = str_pad($ls_codestpro2,25,0,0);
	}
	$ls_estcla     = $_GET["estcla"];
	if ($li_estpreing==1) 
	{
		$ls_sql=" SELECT DISTINCT(spg_ep2.codestpro2),spg_ep2.codestpro1,spg_ep2.denestpro2,spg_ep2.estcla  ".
				"	  FROM spg_ep2 LEFT OUTER JOIN spi_cuentas_estructuras  ".
				"		 ON  spi_cuentas_estructuras.codemp=spg_ep2.codemp ".
				"		AND spi_cuentas_estructuras.codestpro1 =spg_ep2.codestpro1 ".
				"		AND spi_cuentas_estructuras.codestpro2 =spg_ep2.codestpro2 ".
				"		AND spi_cuentas_estructuras.estcla  =spg_ep2.estcla ".
				"	  WHERE spg_ep2.codemp='".$la_empresa["codemp"]."' ".
				"		AND spg_ep2.codestpro1 = '".$ls_codestpro1."' ".
				"		AND spg_ep2.codestpro2 like '%".$ls_codestpro2."%' ".
				"		AND spg_ep2.denestpro2 like '%".$ls_denestpro2."%' ".
				"		AND spg_ep2.estcla='".$ls_estcla."' ".
				"	 ORDER BY spg_ep2.codestpro1,spg_ep2.codestpro2";
	}
	else
	{
		$ls_sql=" SELECT DISTINCT(spg_ep2.codestpro2),spg_ep2.codestpro1,spg_ep2.denestpro2,spg_ep2.estcla  ".
				"	  FROM spg_ep2, spi_cuentas_estructuras  ".
				"	  WHERE spg_ep2.codemp='".$la_empresa["codemp"]."' ".
				"		AND spg_ep2.codestpro1 = '".$ls_codestpro1."' ".
				"		AND spg_ep2.codestpro2 like '%".$ls_codestpro2."%' ".
				"		AND spg_ep2.denestpro2 like '%".$ls_denestpro2."%' ".
				"		AND spg_ep2.estcla='".$ls_estcla."' ".
				"		AND spi_cuentas_estructuras.codemp=spg_ep2.codemp ".
				"		AND spi_cuentas_estructuras.codestpro1 =spg_ep2.codestpro1 ".
				"		AND spi_cuentas_estructuras.codestpro2 =spg_ep2.codestpro2 ".
				"		AND spi_cuentas_estructuras.estcla  =spg_ep2.estcla ".
				"	 ORDER BY spg_ep2.codestpro1,spg_ep2.codestpro2";
	}
		 
	$rs_data = $io_sql->select($ls_sql);
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
				$ls_denestpro2 = $row["denestpro2"]; 
				$ls_estcla     = $row["estcla"]; 
				if ($ls_estcla=='P')
				{
					$ls_denestcla='Proyecto';
				}
				elseif($ls_estcla=='A')
				{
					$ls_denestcla='Actividad';
				}
				if($ls_tipo=="")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro2','$ls_denestpro2');\">".$ls_codestpro1."</a></td>";
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar('$ls_codestpro2','$ls_denestpro2');\">".$ls_codestpro2."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro2."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
				if($ls_tipo=="reporteacumdes")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumdes('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumdes('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro2."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro2."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
				if($ls_tipo=="reporteacumdes_ma")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumdes_ma('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumdes_ma('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro2."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro2."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}				
				if($ls_tipo=="reporteacumhas")
				{
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumhas('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro1."</a></td>";
					print "<td width=160 style=text-align:center><a href=\"javascript: aceptar_reporteacumhas('$ls_codestpro2','$ls_estcla');\">".$ls_codestpro2."</a></td>";
					print "<td width=350 style=text-align:left>".$ls_denestpro2."</td>";
					print "<td width=40 style=text-align:center>".$ls_denestcla."</td>";
					print "</tr>";
				}
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
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(ls_codestpro2,ls_denestpro2)
  {
    opener.document.form1.denestpro2.value=ls_denestpro2;
	opener.document.form1.codestpro2.value=ls_codestpro2;
    opener.document.form1.denestpro3.value="";
	opener.document.form1.codestpro3.value="";
	if("<?php print $li_estmodest;?>"==2)
	{
		opener.document.form1.codestpro4.value="";
		opener.document.form1.denestpro4.value="";
		opener.document.form1.codestpro5.value="";
		opener.document.form1.denestpro5.value="";
	}
	close();
  }
  
  function aceptar_reporteacumdes(codestprog2,estcla)
  {
	opener.document.form1.codestpro2.value=codestprog2;
	opener.document.form1.codestpro2.readOnly=true;
	opener.document.form1.codestpro3.value="";
	if(estcla=="PROYECTO")
	{
	  estcla="P";
	}
	else if(estcla=="ACCION")
	{
	  estcla="A";
	}
	opener.document.form1.estclades.value=estcla;
	close();
  }
  

  function aceptar_reporteacumdes_ma(codestprog2,estcla)
  {
	opener.document.form1.codestpro22.value=codestprog2;
	opener.document.form1.codestpro22.readOnly=true;
	opener.document.form1.codestpro32.value="";
	if(estcla=="PROYECTO")
	{
	  estcla="P";
	}
	else if(estcla=="ACCION")
	{
	  estcla="A";
	}
	opener.document.form1.estclades.value=estcla;
	close();
  }  
  
  
  function aceptar_reporteacumhas(codestprog2,estcla)
  {
		if("<?php print $li_estmodest;?>"==2) 
		{
			if  (opener.document.getElementById('codestpro2h2') ) 
			{      			
			opener.document.form1.codestpro2h2.value=codestprog2;
			opener.document.form1.codestpro2h2.readOnly=true;
			opener.document.form1.codestpro3h2.value=""; 
			}
			
			if  (opener.document.getElementById('codestpro2h') ) 
			{      			
			opener.document.form1.codestpro2h.value=codestprog2;
			opener.document.form1.codestpro2h.readOnly=true;
			opener.document.form1.codestpro3h.value=""; 
			}

		} 
		else
		{
			opener.document.form1.codestpro2h.value=codestprog2;
			opener.document.form1.codestpro2h.readOnly=true;
			opener.document.form1.codestpro3h.value=""; 
		}
	
	if(estcla=="PROYECTO")
	{
	  estcla="P";
	}
	else if(estcla=="ACCION")
	{
	  estcla="A";
	}
	opener.document.form1.estclahas.value=estcla;
	close();
  }
  
  function ue_search()
  {
  f=document.form1;
  f.operacion.value="BUSCAR";
  f.action="sigesp_spi_cat_public_estpro2.php?tipo=<?php print $ls_tipo; ?>";
  f.submit();
  }
</script>
</html>