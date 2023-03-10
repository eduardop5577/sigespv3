<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
   function uf_print()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_spg_cuenta  // C?digo de cuenta
		//				   as_tipo  // Tipo de Llamada del cat?logo
		//	  Description: Funci?n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
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
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_msg =new class_mensajes();
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td width=100>C?digo</td>";
		print "<td width=400>Denominaci?n</td>";
		print "</tr>";
		$ls_sql="SELECT spg_cuenta, denominacion ".
				"  FROM spg_cuentas ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND status='C'".
				"   AND spg_cuenta LIKE '408%'";
				
		$rs_data=$io_sql->select($ls_sql);
		$li_i=0;
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i + 1;
				$ls_spg_cuenta=$row["spg_cuenta"];
				$ls_denominacion=$row["denominacion"];
	
				print "<tr class=celdas-blancas>";
				print "<td><a href=\"javascript: aceptar('$ls_spg_cuenta','$ls_denominacion');\">".$ls_spg_cuenta."</a></td>";
				print "<td>".$ls_denominacion."</td>";
				print "</tr>";			
			}
			$io_sql->free_result($rs_data);
		}
		if($li_i==0)
		{
			$io_msg->message("No existen cuentas definidas del grupo de las 408");
		}
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
		unset($ls_codnom);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Cuentas</title>
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
  <table width="500" height="20" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="496" colspan="2" class="titulo-ventana">Cat&aacute;logo de Cuentas</td>
    </tr>
  </table>
<br>
    <br>
<?php
	uf_print();
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script language="JavaScript">
function aceptar(spg_cuenta,denominacion)
{
	opener.document.form1.txtctaspg.value=spg_cuenta;
	opener.document.form1.txtdenctaspg.value=denominacion;
	close();
}

</script>
</html>