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
	print "window.close();";
	print "</script>";		
}
   function uf_agregarlineablanca($aa_object,$ai_totrows,$ls_codart)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_agregarlineablanca
	//	Access:    public
	//	Arguments:
	//  aa_object  // arreglo de titulos 
	//  ai_totrows // ultima fila pintada en el grid
	//  ls_codart  // codigo del articulo
	//	Description:  Funcion que agrega una linea en blanco al final del grid
	//              
	//////////////////////////////////////////////////////////////////////////////		
		$aa_object[$ai_totrows][1]="<input name=txtcodart".$ai_totrows."    type=text id=txtcodart".$li_j."    class=sin-borde size=25 maxlength=20   readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtcoddetart".$ai_totrows."    type=text id=txtcoddetart".$li_j."    class=sin-borde size=20 maxlength=12   readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtdendetart".$ai_totrows."    type=text id=txtdendetart".$ai_totrows."    class=sin-borde size=38 maxlength=254 readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtfecregdet".$ai_totrows." type=text id=txtfecregdet".$ai_totrows." class=sin-borde size=13 maxlength=5  readonly>";
		$aa_object[$ai_totrows][5]="<input name=txtestdetart".$ai_totrows."    type=text id=txtestdetart".$ai_totrows."    class=sin-borde size=10 maxlength=10  readonly>";			
		$aa_object[$ai_totrows][6]="<input name=txtnomfisalm".$ai_totrows."    type=text id=txtnomfisalm".$ai_totrows."    class=sin-borde size=10 maxlength=10 readonly>";			
		$aa_object[$ai_totrows][7]="<input name=txtnomrespri".$ai_totrows."    type=text id=txtnomrespri".$ai_totrows."    class=sin-borde size=10 maxlength=10 readonly>";			
		$aa_object[$ai_totrows][8]="<input name=txtnomrespri".$ai_totrows."    type=text id=txtnomrespri".$ai_totrows."    class=sin-borde size=10 maxlength=10 readonly>";			

		return $aa_object;		
   }
   	//--------------------------------------------------------------
   function uf_obtenervalor($as_valor, $as_valordefecto)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_obtenervalor
	//	Access:    public
	//	Arguments:
    // as_valor         //  nombre de la variable que desamos obtener
    // as_valordefecto  //  contenido de la variable
    // Description: Función que obtiene el valor de una variable si viene de un submit
	//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Detalles de Articulos (Materiales)</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funciones.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
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

<body>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_fun= new class_funciones();
	require_once("sigesp_siv_c_componente.php");
	$io_siv= new sigesp_siv_c_componente();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid=new grid_param();

	$arre=$_SESSION["la_empresa"];
	$la_codemp=$arre["codemp"];
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_sistema="SIV";
	$ls_ventanas="sigesp_siv_d_articulo.php";

	$la_seguridad["empresa"]=$la_codemp;
	$la_seguridad["logusr"]=$ls_logusr;
	$la_seguridad["sistema"]=$ls_sistema;
	$la_seguridad["ventanas"]=$ls_ventanas;

	$li_totrows = uf_obtenervalor("totalfilas",1);

	$ls_titletable="Listado de Materiales";
	$li_widthtable=700;
	$ls_nametable="grid";
	$lo_title[1]="Articulo";
	$lo_title[2]="Codigo";
	$lo_title[3]="Denominación";
	$lo_title[4]="Fecha de Creacion";
	$lo_title[5]="Estatus";
	$lo_title[6]="Almacen";
	$lo_title[7]="Responsable Primario";
	$lo_title[8]="Responsable Por Uso";
	$lo_title[9]="Causa de Movimiento";


	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion= $_POST["operacion"];
		$ls_denart=    $_POST["txtdenart"];
		
		
	}
	else
	{
		$ls_codart=$_GET["codart"];
		$ls_denart=$_GET["denart"];

		$ls_operacion="";
		$lo_object= uf_agregarlineablanca($lo_object,1,$ls_codart);

		$ls_sql= "SELECT siv_dt_articulo.*, siv_almacen.nomfisalm,".
				 "       (SELECT nomper||' '||apeper FROM sno_personal".
				 "         WHERE  siv_dt_articulo.codemp=sno_personal.codemp".
				 "           AND siv_dt_articulo.codperpri=sno_personal.codper) AS nomrespri,".
				 "       (SELECT nomper||' '||apeper FROM sno_personal ".
				 "         WHERE  siv_dt_articulo.codemp=sno_personal.codemp".
				 "           AND siv_dt_articulo.codperuso=sno_personal.codper) AS nomresuso,".
				 "       (SELECT denart FROM siv_articulo".
				 "         WHERE  siv_dt_articulo.codemp=siv_articulo.codemp".
				 "           AND siv_dt_articulo.codart=siv_articulo.codart) AS denart,".
				 "       (SELECT MAX(dencau) FROM siv_asignacion,siv_dt_asignacion,siv_causas".
				 "         WHERE siv_dt_asignacion.codemp=siv_dt_articulo.codemp".
				 "           AND siv_dt_asignacion.codart=siv_dt_articulo.codart".
				 "           AND siv_dt_asignacion.coddetart=siv_dt_articulo.coddetart".
				 "           AND siv_dt_asignacion.codemp=siv_asignacion.codemp".
				 "           AND siv_dt_asignacion.codasi=siv_asignacion.codasi".
				 "           AND siv_asignacion.codemp=siv_causas.codemp".
				 "           AND siv_asignacion.codcau=siv_causas.codcau) AS dencau".
				 "  FROM siv_dt_articulo,siv_almacen". 
				 " WHERE siv_dt_articulo.codemp= '".$la_codemp."'".
				 "   AND siv_dt_articulo.codart= '".$ls_codart."'".
				 "   AND siv_dt_articulo.codemp=siv_almacen.codemp".
				 "   AND siv_dt_articulo.codalm=siv_almacen.codalm".
				 "  ORDER BY siv_dt_articulo.codart,siv_dt_articulo.coddetart";
		$result=$io_sql->select($ls_sql);

		$li_j=1;
		while($row=$io_sql->fetch_row($result))
		{
				$ls_codart=$row["codart"];
				$ls_denart=$row["denart"];
				$ls_coddetart=$row["coddetart"];
				$ls_dendetart=$row["dendetart"];
				$ls_fecregdet=$io_fun->uf_convertirfecmostrar($row["fecregdet"]);
				$ls_estdetart=$row["estdetart"];
				$ls_nomfisalm=$row["nomfisalm"];
				$ls_nomrespri=$row["nomrespri"];
				$ls_nomresuso=$row["nomresuso"];
				$ls_dencau=$row["dencau"];
				switch ($ls_estdetart)
				{
					case "R":
						$ls_estdetart="REGISTRO";
					break;
					case "N":
						$ls_estdetart="NO DISPONIBLE";
					break;
					case "D":
						$ls_estdetart="DESPACHADO";
					break;
				}

				$lo_object[$li_j][1]="<input name=txtcodart".$li_j."    type=text id=txtcodart".$li_j."    class=sin-borde size=21  value='".$ls_codart."' readonly>";
				$lo_object[$li_j][2]="<input name=txtcoddetart".$li_j."    type=text id=txtcoddetart".$li_j."    class=sin-borde size=15 maxlength=12  value='".$ls_coddetart."' readonly>";
				$lo_object[$li_j][3]="<input name=txtdendetart".$li_j."    type=text id=txtdendetart".$li_j."    class=sin-borde size=38 maxlength=254 value='".$ls_denart."' onKeyUp='javascript: ue_validarcomillas();'>";
				$lo_object[$li_j][4]="<input name=txtfecregdet".$li_j." type=text id=txtfecregdet".$li_j." class=sin-borde size=10   value='".$ls_fecregdet."' readonly>";
				$lo_object[$li_j][5]="<input name=txtestdetart".$li_j."    type=text id=txtestdetart".$li_j."    class=sin-borde size=10  value='".$ls_estdetart."' readonly>";			
				$lo_object[$li_j][6]="<input name=txtnomfisalm".$li_j."    type=text id=txtnomfisalm".$li_j."    class=sin-borde size=15 value='".$ls_nomfisalm."' readonly>";			
				$lo_object[$li_j][7]="<input name=txtnomrespri".$li_j."    type=text id=txtnomrespri".$li_j."    class=sin-borde size=15  value='".$ls_nomrespri."' readonly>";			
				$lo_object[$li_j][8]="<input name=txtnomreuso".$li_j."     type=text id=txtnomresuso".$li_j."    class=sin-borde size=15  value='".$ls_nomresuso."' readonly>";			
				$lo_object[$li_j][9]="<input name=txtdencau".$li_j."     type=text id=txtdencau".$li_j."    class=sin-borde size=20 value='".$ls_dencau."' readonly>";			

			$li_j=$li_j + 1;			
		}

			$li_totrows=$li_j;
			$lo_object = uf_agregarlineablanca($lo_object,$li_totrows,$ls_codart);
	}
	switch ($ls_operacion) 
	{

			
	}

?>
<div align="center">
  <table width="632" height="143" border="0" class="formato-blanco">
 <form name="form1" method="post" action="">
    <tr>
      <td width="624" height="137"><div align="left">
<table width="624" height="92" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="13" colspan="3" class="titulo-ventana">Detalles de Articulos (Materiales)</td>
  </tr>
  <tr class="formato-blanco">
    <td height="22" colspan="3">          <div align="left">
              <input name="txtdenart" type="text" class="sin-borde2" id="txtdenart" value="<?php print $ls_denart?>" size="70" readonly="true">
                  <input name="txtdenunimed" type="hidden" id="txtdenunimed">
                  <input name="txtunidad" type="hidden" id="txtunidad">
                  <input name="txtobsunimed" type="hidden" id="txtobsunimed">
                  <input name="hidstatus" type="hidden" id="hidstatus">
</div></td>
    </tr>
  <tr class="formato-blanco">
    <td height="22" colspan="3">
	<?php	
			$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
	?>			</td>
    </tr>
  <tr class="formato-blanco">
    <td width="552" height="28"><div align="right">
      <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
      <input name="filadelete" type="hidden" id="filadelete">
</div></td>
    <td width="70" height="22" colspan="2"><div align="right"><a href="javascript: ue_cancelar();"><img src="../shared/imagebank/eliminar.gif" alt="Cancelar" width="15" height="15" border="0">Cerrar</a> </div></td>
    </tr>
</table>

<div align="center">
  <input name="operacion" type="hidden" id="operacion">
      </div>
      </div></td>
    </tr>
    </form>
  </table>
</div>
<p align="center">&nbsp; </p>
</body>
<script >
//Funciones de operaciones sobre el comprobante
function ue_cataunimed(li_linea)
{
	window.open("sigesp_catdinamic_unidadmedida.php?linea="+li_linea+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}
function ue_unidad()
{
	window.open("sigesp_catdinamic_unidad.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,left=50,top=50,location=no,resizable=yes");
}
function ue_buscar()
{
	window.open("sigesp_catdinamic_rotulacion.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}
function ue_nuevo()
{
	f=document.form1;
	f.operacion.value="NUEVO";
	f.action="sigesp_siv_d_componentes.php";
	f.submit();
}
function ue_agregar()
{
	f=document.form1;
	f.operacion.value="AGREGARDETALLE";
	f.action="sigesp_siv_d_componentes.php";
	f.submit();
}
function ue_guardar(totrow)
{
	f=document.form1;
	for (li_row=1; li_row<=totrow ;li_row++)
	{
		ls_codart=eval("f.txtcodart"+li_row+".value");
		ls_codart=ue_validarvacio(ls_codart);
		ls_codcom=eval("f.txtcodcom"+li_row+".value");
		ls_codcom=ue_validarvacio(ls_codcom);
		ls_dencom=eval("f.txtdescom"+li_row+".value");
		ls_dencom=ue_validarvacio(ls_dencom);
		ls_codunimed=eval("f.txtcodunimed"+li_row+".value");
		ls_codunimed=ue_validarvacio(ls_codunimed);
		ls_cancom=eval("f.txtcancom"+li_row+".value");
		ls_cancom=ue_validarvacio(ls_cancom);
	
		if((ls_codcom=="")||(ls_cancom==""))
		{
			alert("Debe llenar todos los campos en la linea "+li_row+"");
			lb_valido=true;
		}
		else
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_siv_d_componentes.php";
			f.submit();
		}
	}
}
function ue_cancelar()
{
	window.close();
}

function uf_agregar_dt(li_row)
{
	f=document.form1;
	ls_codnew=eval("f.txtcodcom"+li_row+".value");
	li_total=f.totalfilas.value;
	lb_valido=false;
	if(li_total==li_row)
	{
		for(li_i=1;li_i<=li_total&&lb_valido!=true;li_i++)
		{
			ls_codid=eval("f.txtcodcom"+li_i+".value");
			if((ls_codid==ls_codnew)&&(li_i!=li_row))
			{
				alert("El componente ya esta registrado");
				lb_valido=true;
			}
		}
		ls_codart=eval("f.txtcodart"+li_row+".value");
		ls_codart=ue_validarvacio(ls_codart);
		ls_codcom=eval("f.txtcodcom"+li_row+".value");
		ls_codcom=ue_validarvacio(ls_codcom);
		ls_dencom=eval("f.txtdescom"+li_row+".value");
		ls_dencom=ue_validarvacio(ls_dencom);
		ls_codunimed=eval("f.txtcodunimed"+li_row+".value");
		ls_codunimed=ue_validarvacio(ls_codunimed);
		ls_cancom=eval("f.txtcancom"+li_row+".value");
		ls_cancom=ue_validarvacio(ls_cancom);
	
		if((ls_codcom=="")||(ls_dencom=="")||(ls_codunimed=="")||(ls_cancom==""))
		{
			alert("Debe llenar todos los campos");
			lb_valido=true;
		}
		
		if(!lb_valido)
		{
			f.operacion.value="AGREGARDETALLE";
			f.action="sigesp_siv_d_componentes.php";
			f.submit();
		}
	}
}
function uf_delete_dt(li_row)
{
	f=document.form1;
	ls_codcom=eval("f.txtcodcom"+li_row+".value");
	ls_codcom=ue_validarvacio(ls_codcom);
	li_fila=f.totalfilas.value;
	if(li_fila!=li_row)
	{
		if(ls_codcom=="")
		{
			alert("No deben tener campos vacios");
		}
		else
		{
			if(confirm("¿Desea eliminar el Registro "+ls_codcom+"?"))
			{
				f.filadelete.value=li_row;
				f.operacion.value="ELIMINARDETALLE"
				f.action="sigesp_siv_d_componentes.php";
				f.submit();
			}
		}
	}
}


</script> 
</html>