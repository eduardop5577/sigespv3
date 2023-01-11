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
require_once("class_folder/class_funciones_viaticos.php");
$io_fun_viaticos=new class_funciones_viaticos();
if(!array_key_exists("la_logusr",$_SESSION))
{
   print "<script language=JavaScript>";
   print "close();";
   print "opener.document.form1.submit();";
   print "</script>";		
}

if(array_key_exists("hiddestino",$_POST))
{
   $ls_destino=$io_fun_viaticos->uf_obtenervalor("hiddestino","");
} else {
   $ls_destino=$io_fun_viaticos->uf_obtenervalor_get("destino","");
}

   //--------------------------------------------------------------
function uf_print($as_codcar, $as_descar, $as_tipo)
{
   //////////////////////////////////////////////////////////////////////////////
   //	     Function: uf_print
   //		   Access: public
   //	    Arguments: as_codcar  // Código del cargo
   //				   as_descar  // Descripción del Cargo
   //				   as_tipo  // Tipo de Llamada del catálogo
   //	  Description: Función que obtiene e imprime los resultados de la busqueda
   //	   Creado Por: Ing. Yesenia Moreno
   // Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
   $ls_codnom=$_SESSION["la_nomina"]["codnom"];
   
   print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
   print "<tr class=titulo-celda>";
   print "<td width=100>Código</td>";
   print "<td width=100>Nomina</td>";
   print "<td width=400>Descripción</td>";
   print "</tr>";
   
   $ls_sql="SELECT codcar, descar, MAX(codnom) AS codnom ".
                  "  FROM sno_cargo ".
                  " WHERE codemp='".$ls_codemp."'".
                  "   AND codcar<>'0000000000'".
                  "   AND codcar like '".$as_codcar."' AND descar like '".$as_descar."'".
                  " GROUP BY codcar, descar ".
                  " ORDER BY codcar ";


//   $ls_sql=" SELECT DISTINCT  ON(  descar ) descar, min( codcar) as codcar, min(codnom) AS codnom ".
//           " FROM sno_cargo ".
//           " WHERE codemp='".$ls_codemp."'".
//	     "   AND codcar<>'0000000000'".
//           "   AND codcar like '".$as_codcar."' AND descar like '".$as_descar."'".
//	     " GROUP BY descar ".
//           " ORDER BY descar ";
//                        




$rs_data=$io_sql->select($ls_sql);
if($rs_data===false)
{
$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
}
else
{
   while($row=$io_sql->fetch_row($rs_data))
   {
      $ls_codcar=$row["codcar"];
      $ls_descar=$row["descar"];
      $ls_codnom=$row["codnom"];
      switch ($as_tipo)
      {
         case "":
               print "<tr class=celdas-blancas>";
               print "<td><a href=\"javascript: aceptar('$ls_codcar','$ls_descar','$ls_codnom');\">".$ls_codcar."</a></td>";
               print "<td>".$ls_codnom."</td>";
               print "<td>".$ls_descar."</td>";
               print "</tr>";			
               break;
         case "CATEGORIA":
               print "<tr class=celdas-blancas>";
               print "<td><a href=\"javascript: aceptar_categoria('$ls_codcar','$ls_descar','$ls_codnom');\">".$ls_codcar."</a></td>";
               print "<td>".$ls_codnom."</td>";
               print "<td>".$ls_descar."</td>";
               print "</tr>";			
               break;

      }
   }
   $io_sql->free_result($rs_data);
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
<title>Cat&aacute;logo de Cargo</title>
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
    <input name="hiddestino" type="hidden" id="hiddestino">
  </p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Cargo </td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="67" height="22"><div align="right">C&oacute;digo</div></td>
        <td width="431"><div align="left">
          <input name="txtcodcar" type="text" id="txtcodcar" size="30" maxlength="10" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Descripci&oacute;n</div></td>
        <td><div align="left">
          <input name="txtdescar" type="text" id="txtdescar" size="30" maxlength="100" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
  <br>
<?php
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion = $_POST["operacion"];   
	}
	else
	{
		$ls_operacion = "";
	}
	if($ls_operacion=="BUSCAR")
	{
		$ls_codcar="%".$_POST["txtcodcar"]."%";
		$ls_descar="%".$_POST["txtdescar"]."%";
		uf_print($ls_codcar, $ls_descar,$ls_destino);
	}
	else
	{
		$ls_codcar="%%";
		$ls_descar="%%";
		uf_print($ls_codcar, $ls_descar,$ls_destino);
	}
	unset($io_fun_nomina);	
?>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function aceptar(codcar,descar,codnom)
{
	fop        = opener.document.form1;
	li_lastrow = (fop.hidlastrow.value); 
	lb_existe  = false;
	for (i=1;i<=li_lastrow;i++)
	{
		ls_codcar = eval("fop.txtcodcar"+i+".value");
		ls_codnom = eval("fop.txtcodnom"+i+".value");
        ls_descar = eval("fop.txtdescar"+i+".value");
                
		if ((ls_codcar==codcar)&&(ls_codcar==codcar))
		{
			lb_existe = true;
			alert("Este Cargo ya fue Registrado !!!"); 
			break;
		}
	}
	if (!lb_existe)
	{
		li_lastrow = parseInt(li_lastrow)+1;
		eval("fop.txtcodcar"+li_lastrow+".value='"+codcar+"'");
		eval("fop.txtcodnom"+li_lastrow+".value='"+codnom+"'");
        eval("fop.txtdescar"+li_lastrow+".value='"+descar+"'");
                
  		fop.hidlastrow.value = li_lastrow; 
		fop.hidtotrows.value = parseInt(li_lastrow)+1; 
		fop.operacion.value  = 'PINTAR'
		fop.submit();
		close();
	}
}

function aceptar_categoria(codcar,descar,codnom)
{
	fop        = opener.document.form1;
	li_lastrow = (fop.hidlastrow.value); 
	lb_existe  = false;
	for (i=1;i<=li_lastrow;i++)
	{
		ls_codcar = eval("fop.txtcodcar"+i+".value");
		ls_codnom = eval("fop.txtcodnom"+i+".value");
        ls_descar = eval("fop.txtdescar"+i+".value");
                
                
		if ((ls_codcar==codcar)&&(ls_codcar==codcar))
		{
			lb_existe = true;
			alert("Este Cargo ya fue Registrado !!!"); 
			break;
		}
	}
	if (!lb_existe)
	{
		li_lastrow = parseInt(li_lastrow)+1;
		eval("fop.txtcodcar"+li_lastrow+".value='"+codcar+"'");
		eval("fop.txtcodnom"+li_lastrow+".value='"+codnom+"'");
		eval("fop.txtdescar"+li_lastrow+".value='"+descar+"'");
		fop.hidlastrow.value = li_lastrow; 
		fop.hidtotrows.value = parseInt(li_lastrow)+1; 
		fop.operacion.value  = 'PINTAR'
		fop.submit();
		close();
	}
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

function ue_search(existe)
{
	f=document.form1;
  	f.operacion.value="BUSCAR";
  	f.action="sigesp_scv_cat_cargo.php?tipo=<?php print $ls_tipo;?>";
  	f.submit();
}
</script>
</html>
