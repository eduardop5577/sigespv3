<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();

   //--------------------------------------------------------------
   // Función que obtiene que tipo de operación se va a ejecutar
   // NUEVO, GUARDAR, ó ELIMINAR
   function uf_obteneroperacion()
   {
		if(array_key_exists("operacion",$_POST))
		{
			$operacion=$_POST["operacion"];
		}
		else
		{
			$operacion="NUEVO";
		}
   		return $operacion; 
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   // Función que obtiene e imprime los resultados de la busqueda
   function uf_imprimirresultados($as_codpai, $as_despai,$as_codcont,$as_destino)
   {
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$in=new sigesp_include();
		$con=$in->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_msg=new class_mensajes();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($con);
		$ds=new class_datastore();
   
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=60>Código</td>";
		print "<td width=440>Descripción</td>";
		print "</tr>";
		$ls_sqlaux="";
		$ls_codpai="";
		if($as_codcont!="")
		{
			$ls_sqlaux=" AND codcont='".$as_codcont."'";
		}
		if($as_destino=="REGIONESINT")
		{
			$ls_sqlaux=$ls_sqlaux." AND codpai NOT IN (SELECT codpai FROM scv_dt_regiones_int)";
		}
  		if (array_key_exists("esttarvia",$_POST))
		{  
			$ls_codpai= "   AND codpai <> '"."058"."'";
		}
		else{
			$ls_codpai="";
		}
		$ls_sql="SELECT codpai,despai,codcont,".
				"       (SELECT dencont FROM sigesp_continente".
				"         WHERE sigesp_pais.codcont=sigesp_continente.codcont) AS dencont".
				" FROM sigesp_pais".
				" WHERE codpai like '".$as_codpai."'".
				"   AND despai like '".$as_despai."'".$ls_codpai.
				$ls_sqlaux.
				"   AND codpai <> '---'".
				" ORDER BY codpai";
		$rs_pai=$io_sql->select($ls_sql);
		if($row=$io_sql->fetch_row($rs_pai))
		{
			$data=$io_sql->obtener_datos($rs_pai);
			$ds->data=$data;
			$li_rows=$ds->getRowCount("codpai");
			switch ($as_destino)
			{
				case "":
					for($li_index=1;$li_index<=$li_rows;$li_index++)
					{
						print "<tr class=celdas-blancas>";
						$ls_codpai=$data["codpai"][$li_index];
						$ls_despai=$data["despai"][$li_index];
						$ls_codcont=$data["codcont"][$li_index];
						$ls_dencont=$data["dencont"][$li_index];
						print "<td><a href=\"javascript: aceptar('$ls_codpai','$ls_despai','$ls_codcont','$ls_dencont');\">".$ls_codpai."</a></td>";
						print "<td>".$ls_despai."</td>";
						print "</tr>";			
					}
				break;
				case "REGIONES":
					for($li_index=1;$li_index<=$li_rows;$li_index++)
					{
						print "<tr class=celdas-blancas>";
						$ls_codpai=$data["codpai"][$li_index];
						$ls_despai=$data["despai"][$li_index];
						$ls_codcont=$data["codcont"][$li_index];
						$ls_dencont=$data["dencont"][$li_index];
						print "<td><a href=\"javascript: aceptar_regiones('$ls_codpai','$ls_despai','$ls_codcont','$ls_dencont');\">".$ls_codpai."</a></td>";
						print "<td>".$ls_despai."</td>";
						print "</tr>";			
					}
				break;
				case "REGIONESINT":
					for($li_index=1;$li_index<=$li_rows;$li_index++)
					{
						print "<tr class=celdas-blancas>";
						$ls_codpai=$data["codpai"][$li_index];
						$ls_despai=$data["despai"][$li_index];
						$ls_codcont=$data["codcont"][$li_index];
						$ls_dencont=$data["dencont"][$li_index];
						print "<td><a href=\"javascript: aceptar_regiones('$ls_codpai','$ls_despai','$ls_codcont','$ls_dencont');\">".$ls_codpai."</a></td>";
						print "<td>".$ls_despai."</td>";
						print "</tr>";			
					}
				break;
			}
		}
		else
		{
			$io_msg->message("No hay registros");
		}
		print "</table>";
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Pais</title>
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
    <input name="codcont" type="hidden" id="codcont">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" colspan="2" class="titulo-celda">Cat&aacute;logo de Pais </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="0" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67"><div align="right">C&oacute;digo</div></td>
        <td width="431" height="22"><div align="left">
          <input name="txtcodpai" type="text" id="txtcodpai" size="30" maxlength="3">        
        </div></td>
      </tr>
      <tr>
        <td><div align="right">Descripci&oacute;n</div></td>
        <td height="22"><div align="left">
          <input name="txtdespai" type="text" id="txtdespai" size="30" maxlength="50">
        </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="left"></div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
    </table>
  <br>
<?php
	$ls_operacion=uf_obteneroperacion();
	if($ls_operacion=="BUSCAR")
	{
		$ls_codpai="%".$_POST["txtcodpai"]."%";
		$ls_despai="%".$_POST["txtdespai"]."%";
		$ls_codcont=$_POST["txtcodcont"];
		$ls_destino=$_POST["txtdestino"];

		uf_imprimirresultados($ls_codpai, $ls_despai,$ls_codcont,$ls_destino);
	}
	else
	{
		if(array_key_exists("codcont",$_GET))
		{$ls_codcont=$_GET["codcont"];}
		else
		{$ls_codcont="";}
		if(array_key_exists("destino",$_GET))
		{$ls_destino=$_GET["destino"];}
		else
		{$ls_destino="";}
		
	}
?>
</div>
          <input name="txtcodcont" type="hidden" id="txtcodcont" value="<?php print $ls_codcont; ?>">
          <input name="txtdestino" type="hidden" id="txtdestino" value="<?php print $ls_destino; ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codigo,descripcion,continente,dencont)
{
	opener.document.form1.txtcodpai.value=codigo;
	opener.document.form1.txtcodpai.readOnly=true;
    opener.document.form1.txtdespai.value=descripcion;
	opener.document.form1.txtcodcont.value=continente;
    opener.document.form1.txtdencont.value=dencont;
	close();
}
function aceptar_regiones(codigo,descripcion)
{
	fop        = opener.document.form1;
	li_lastrow = (fop.hidlastrow.value); 
	lb_existe  = false;
	for (i=1;i<=li_lastrow;i++)
	{
		ls_codpai = eval("fop.txtcodpai"+i+".value");
		if (ls_codpai==codigo)
		{
			lb_existe = true;
			alert("Este Pais ya fue Registrado !!!"); 
			break;
		}
	}
	if (!lb_existe)
	{
		li_lastrow = parseInt(li_lastrow)+1;
		eval("fop.txtcodpai"+li_lastrow+".value='"+codigo+"'");
		eval("fop.txtdenpai"+li_lastrow+".value='"+descripcion+"'");
		fop.hidlastrow.value = li_lastrow; 
		fop.hidtotrows.value = parseInt(li_lastrow)+1; 
		fop.operacion.value  = 'PINTAR'
		fop.submit();
		close();
	}
}
function ue_search()
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_scv_cat_pais.php";
  	f.submit();
}
</script>
</html>
