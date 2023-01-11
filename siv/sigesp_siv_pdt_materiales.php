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
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "close();";
	print "</script>";		
}
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codart,$ls_denart,$ls_codartpri,$ls_denartpri,$ls_codtipart,$ls_dentipart,$ls_codunimed,$ls_denunimed;
   		global $ls_codcatsig,$ls_dencatsig,$ls_spg_cuenta,$li_canart,$li_cosart,$ls_dentipart,$ls_codunimed,$ls_ctasep;
		
		$ls_codart="";
		$ls_denart="";
		$ls_codartpri="";
		$ls_denartpri="";
		$ls_codtipart="";
		$ls_dentipart="";
		$ls_codunimed="";
		$ls_denunimed="";
		$ls_codcatsig="";
		$ls_dencatsig="";
		$ls_spg_cuenta="";
		$li_canart=0;
		$li_cosart=0;
		$ls_ctasep="";
   }
   
   function uf_titulosdespacho()
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_titulosdespacho
		//         Access: private
		//      Argumento:  	
		//	      Returns:
		//    Description: Función que carga las caracteristicas del grid de detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		
		$ls_titletable="Detalle del Articulo";
		$li_widthtable=800;
		$ls_nametable="grid";
		$lo_title[1]="Artículo";
		$lo_title[2]="Almacén";
		$lo_title[3]="Unidad";
		$lo_title[4]="Existencia";
		$lo_title[5]="Cant. a Despachar";
		$lo_title[6]="";
   }

   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 		
		//                 $ai_totrows // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="";
		$aa_object[$ai_totrows][2]="<input  name=txtcodart".$ai_totrows."     type=text   id=txtcodart".$ai_totrows." class=sin-borde size=25 maxlength=50 readonly>";
		$aa_object[$ai_totrows][3]="<input  name=txtdenart".$ai_totrows."     type=text   id=txtdenart".$ai_totrows." class=sin-borde size=13 maxlength=10 readonly>";
		$aa_object[$ai_totrows][4]="<input  name=txtcoddetart".$ai_totrows."     type=text id=txtcoddetart".$ai_totrows."    class=sin-borde size=15 maxlength=25 readonly>";
		return $aa_object;
   }
   //--------------------------------------------------------------
   function uf_loadgrid($lo_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 		
		//                 $ai_totrows // ultima fila pintada en el grid		
		//	      Returns:
		//    Description: Funcion que agrega una linea en blanco al final del grid del detalle de despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_inventario;
		for($li_i=1;$li_i<$ai_totrows;$li_i++)
		{
			$ls_codartgrid=$io_fun_inventario->uf_obtenervalor("txtcodart".$li_i,"");
			$ls_denartgrid=$io_fun_inventario->uf_obtenervalor("txtdenart".$li_i,"");
			$ls_codartprigrid=$io_fun_inventario->uf_obtenervalor("txtcodartpri".$li_i,"");
			$ls_codalmgrid=$io_fun_inventario->uf_obtenervalor("txtcodalm".$li_i,"");
			$ls_unidadgrid=$io_fun_inventario->uf_obtenervalor("txtunidad".$li_i,"");
			$ls_exiartgrid=$io_fun_inventario->uf_obtenervalor("txtexistencia".$li_i,"");
			$ls_canartgrid=$io_fun_inventario->uf_obtenervalor("txtcanart".$li_i,"");
			$ls_ctasepgrid=$io_fun_inventario->uf_obtenervalor("ctasep".$li_i,"");

			$lo_object[$li_i][1]="<input  name=txtdenart".$li_i."     type=text   id=txtdenart".$li_i." class=sin-borde size=25 maxlength=50 value='".$ls_denartgrid."'  readonly>".
								 "<input  name=txtcodart".$li_i."     type=hidden id=txtcodart".$li_i." class=sin-borde size=20 maxlength=50 value='".$ls_codartgrid."'  readonly>".
							     "<input  name=ctasep".$li_i."        type=hidden id=ctasep".$li_i." class=sin-borde size=20 maxlength=50 value='".$ls_ctasepgrid."'  readonly>".
								 "<input  name=txtcodartpri".$li_i."  type=hidden id=txtcodartpri".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_codartprigrid."' readonly>";
			$lo_object[$li_i][2]="<input  name=txtcodalm".$li_i."     type=text   id=txtcodalm".$li_i." class=sin-borde size=13 maxlength=10 value='".$ls_codalmgrid."' readonly>";
			$lo_object[$li_i][3]="<input  name=txtunidad".$li_i."     type=text id=txtunidad".$li_i."    class=sin-borde size=15 maxlength=25 value='".$ls_unidadgrid."' readonly>";
			$lo_object[$li_i][4]="<input  name=txtexistencia".$li_i." type=text   id=txtexistencia".$li_i." class=sin-borde size=12 maxlength=12 value='".$ls_exiartgrid."' readonly>";
			$lo_object[$li_i][5]="<input  name=txtcanart".$li_i."     type=text   id=txtcanart".$li_i." class=sin-borde size=12 maxlength=12 onKeyUp='javascript: ue_validarnumero(this);'value='".$ls_canartgrid."' readonly>";
			$lo_object[$li_i][6]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";			
		}
		return $lo_object;
   }
   //--------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Materiales</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
<?php
require_once("sigesp_siv_c_despacho.php");
$io_siv=  new sigesp_siv_c_despacho();
require_once("class_funciones_inventario.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../shared/class_folder/grid_param.php");
$in_grid= new grid_param();
$io_msg= new class_mensajes();
$io_fun_inventario= new class_funciones_inventario();
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_inventario->uf_load_seguridad("SIV","sigesp_siv_p_transferencia.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
uf_titulosdespacho();
$li_totrows=1;
$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_operacion=$io_fun_inventario->uf_obteneroperacion();
	
	switch($ls_operacion)
	{
		case"NUEVO":
			uf_limpiarvariables();
			$ls_codalm=$io_fun_inventario->uf_obtenervalor_get("codalm","");
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
		break;
		case"BUSCAR":
			uf_limpiarvariables();
			$ls_codart=$io_fun_inventario->uf_obtenervalor("txtcodart",1);
			$li_totrowsopenner=$io_fun_inventario->uf_obtenervalor("totalfilas","");
			$li_totrows=$io_fun_inventario->uf_obtenervalor("totalfilaslocal","");
			$ls_origen=$io_fun_inventario->uf_obtenervalor("origen","");
			$arrResultado=$io_siv->uf_select_articulo($ls_codart,$ls_origen,$ls_codartpri,$ls_denart,$li_unidad,$ls_denartpri);
			$ls_codartpri = $arrResultado['ls_codartpri'];
			$ls_denart = $arrResultado['as_denart'];
			$li_unidad = $arrResultado['ai_unidad'];
			$ls_denartpri = $arrResultado['as_denartpri'];
			$lb_valido=$arrResultado['lb_valido'];

			$lo_object = uf_loadgrid($lo_object,$li_totrows);
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows);
			if(!$lb_valido)
			{
				$io_msg->message("El codigo indicado no esta registrado");
				$ls_codart="";
			}
		break;
	}
?>
<form name="formulario" method="post" action="">
  <table width="750" border="0" align="center" class="formato-blanco">
    <tr>
      <td height="22" colspan="4" class="titulo-celda">Materiales</td>
    </tr>
    <tr>
      <td width="169"><div align="right">Articulos </div></td>
      <td height="22" colspan="3"><input name="txtcodart" type="text" id="txtcodart" value="<?php print $ls_codtipart?>" size="25" maxlength="20" style="text-align:center" readonly>
        <a href="javascript: ue_articulo();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
        <input name="txtdenart" type="text" class="sin-borde" id="txtdenart" value="<?php print $ls_dentipart?>" size="30" readonly>      </td>
    </tr>
    <tr>
      <td><div align="right">Serial Desde </div></td>
      <td height="22" colspan="3"><div align="left">
        <input name="txtserdes" type="text" id="txtserdes" style="text-align:right " value="" size="10">
      </div></td>
    </tr>
    <tr>
      <td><div align="right">Serial Hasta </div></td>
      <td height="22" colspan="3"><input name="txtserhas" type="text" id="txtserhas" style="text-align:right "  size="10"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="22" colspan="3"><input name="btnbuscar" type="button" class="boton" id="btnbuscar" value="Buscar Disponibles" onClick="javascript: buscarDisponibles();">
      <input name="txtcodalm" type="hidden" id="txtcodalm" value="<?php print $ls_codalm; ?>"></td>
    </tr>
    <tr>
      <td height="22" colspan="4">
		<div align="center"><div id="articulos"></div>
		<?php
		//	$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
		?>
		</div></td>
    </tr>
    <tr>
      <td><div align="right"></div></td>
      <td width="271" align="center"><input name="operacion" type="hidden" id="operacion"></td>
	  <td width="124"><a href="javascript: ue_agregar();"></a></td>
      <td width="166"><div align="right"><a href="javascript: ue_agregar();"><img src="../shared/imagebank/tools15/aprobado.gif" width="15" height="15" class="sin-borde">Aceptar <img src="../shared/imagebank/tools15/eliminar.gif" width="15" height="15" class="sin-borde">Cancelar</a> </div></td>
    </tr>
  </table>
</form>
</body>
<script >

function checkall()
{
	f=document.formulario;
	totrow=ue_calcular_total_fila_local("txtcodart");
	if(f.chkall.checked==true)
	{
		for(j=1;(j<=totrow);j++)
		{
			eval("document.formulario.chkselect"+j+".checked=true");
		}
	}
	else
	{
		for(j=1;(j<=totrow);j++)
		{
			eval("document.formulario.chkselect"+j+".checked=false");
		}
	}	
}

function buscarDisponibles()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	codart=f.txtcodart.value;
	serdes=f.txtserdes.value;
	serhas=f.txtserhas.value;
	codalm=f.txtcodalm.value;
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('articulos');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde están los métodos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_siv_c_asignacion_ajax.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divgrid.innerHTML = ajax.responseText
		}
	}
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("codart="+codart+"&serdes="+serdes+"&serhas="+serhas+"&codalm="+codalm+"&proceso=DISPONIBLES");
}

function ue_agregar()
{
	f=document.formulario;
	valido=true;
	parametros="";
	totrowent=2;
	totrowopen=ue_calcular_total_fila_opener("txtcodart");
	totrow=ue_calcular_total_fila_local("txtcodart");
	
	for(x=1;x<totrowopen;x++)
	{
		codart=eval("opener.document.formulario.txtcodart"+x+".value");
		denart=eval("opener.document.formulario.txtdenart"+x+".value");
		coddetart=eval("opener.document.formulario.txtcoddetart"+x+".value");
		if(codart!="")
		{
			parametros=parametros+"&txtcodart"+x+"="+codart+"&txtdenart"+x+"="+denart+""+
					   "&txtcoddetart"+x+"="+coddetart;
		}
	}
	for(j=1;(j<=totrow);j++)
	{
		if(eval("document.formulario.chkselect"+j+".checked==true"))
		{
			codart=eval("document.formulario.txtcodart"+j+".value");
			denart=eval("document.formulario.txtdenart"+j+".value");
			coddetart=eval("document.formulario.txtcoddetart"+j+".value");
			valido=true;
			for(i=1;(i<=totrowopen)&&(valido);i++)
			{
				codartgrid=eval("opener.document.formulario.txtcodart"+i+".value");
				coddetartgrid=eval("opener.document.formulario.txtcoddetart"+i+".value");
				if((codartgrid!="")||(totrowopen==1))
				{
					if((codart!=codartgrid)||(coddetart!=coddetartgrid))
					{
					}
					else
					{
						valido=false;
					}
				}
			}
			if(valido)
			{
				parametros=parametros+"&txtcodart"+x+"="+codart+"&txtdenart"+x+"="+denart+""+
						   "&txtcoddetart"+x+"="+coddetart;
				x++;
			}
		}
	}
	if(parametros!="")
	{
		// Div donde se van a cargar los resultados
		divgrid = opener.document.getElementById("articulos");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_siv_c_asignacion_ajax.php",true);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX 
			}
			else
			{
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
					}
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La página no existe";
						}
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					}
					
				}
			}
		}	
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("proceso=LIMPIAR"+parametros+"&totrowart="+x);
	}
}

	
function ue_articulo()
{
	tipo="materiales";
	ls_coddestino="txtcodart";
	ls_dendestino="txtdenart";
	window.open("sigesp_catdinamic_articulom.php?coddestino="+ ls_coddestino +"&dendestino="+ ls_dendestino+"&tipo="+ tipo +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=120,top=70,location=no,resizable=yes");
}


function ue_cancelar()
{
	close();
}
</script> 
<script type="text/javascript"  src="js/funcion_siv.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script  src="js/funciones.js"></script>
</html>