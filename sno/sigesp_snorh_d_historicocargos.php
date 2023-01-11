<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_historicocargos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   
   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ld_fecmov,$ls_codnom,$ls_desnom,$ls_codcar,$ls_descar,$ls_codasicar,$ls_desasicar,$ls_codtab,$ls_destab,$ls_codpas,$ls_codgra,$ls_racnom;
		global $ls_operacion,$ls_existe,$io_fun_nomina;
		
		$ld_fecmov="dd/mm/aaaa";
		$ls_codnom="";
		$ls_desnom="";
		$ls_codcar="";
		$ls_descar="";
		$ls_codasicar="";
		$ls_desasicar="";
		$ls_codtab="";
		$ls_destab="";
		$ls_codpas="";
		$ls_codgra="";
		$ls_racnom="";
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ld_fecmov,$ls_codnom,$ls_desnom,$ls_codcar,$ls_descar,$ls_codasicar,$ls_desasicar,$ls_codtab,$ls_destab,$ls_codpas,$ls_codgra,$ls_racnom;
		global $ls_codper,$ls_nomper,$ls_operacion,$ls_existe,$io_fun_nomina;
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecmov=$_POST["txtfecmov"];
		$ls_codnom=$_POST["txtcodnom"];
		$ls_desnom=$_POST["txtdesnom"];
		$ls_codcar=$_POST["txtcodcar"];
		$ls_descar=$_POST["txtdescar"];
		$ls_codasicar=$_POST["txtcodasicar"];
		$ls_desasicar=$_POST["txtdesasicar"];
		$ls_codtab=$_POST["txtcodtab"];
		$ls_destab=$_POST["txtdestab"];
		$ls_codpas=$_POST["txtcodpas"];
		$ls_codgra=$_POST["txtcodgra"];
		$ls_racnom=$_POST["hidracnom"];
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Definici&oacute;n de Movimientos de Cargos</title>
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
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_historicocargos.php");
	$io_historicocargos=new sigesp_snorh_c_historicocargos();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			break;

		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_historicocargos->uf_guardar($ls_codper,$ld_fecmov,$ls_codnom,$ls_desnom,$ls_codcar,$ls_descar,$ls_codasicar,
			                                           $ls_desasicar,$ls_codtab,$ls_destab,$ls_codpas,$ls_codgra,$ls_existe,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_historicocargos->uf_delete_sueldoshistorios($ls_codper,$ld_fecmov,$ls_codnom,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables();
				$ls_codper=$_POST["txtcodper"];
				$ls_nomper=$_POST["txtnomper"];
			}
			break;
	}
	$io_historicocargos->uf_destructor();
	unset($io_historicocargos);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_nomina">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequeñas"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	</table>	 </td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td width="25" height="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif"  title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigesp_snorh_d_personal.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="722" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="720">	<p>&nbsp;</p>      <table width="640" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td colspan="3"><div align="center">
            <input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
        </div></td>
      </tr>
      <tr class="titulo-ventana">
        <td height="20" colspan="3" class="titulo-ventana">Definici&oacute;n de movimientos de Cargos </td>
      </tr>
      <tr>
        <td width="128" height="22">&nbsp;</td>
        <td width="506" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"><div align="right">Fecha </div></td>
        <td colspan="2"><div align="left">
          <select name="cmbmes" id="cmbmes">
            <option value="01" selected>Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
          <select name="cmbano" id="cmbano" onChange="javascript: ue_cargar_fecha();">
          </select>
          <input name="txtfecmov" type="text" id="txtfecmov" value="<?php print $ld_fecmov;?>" size="15" maxlength="10" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" readonly>
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">n&oacute;mina </div></td>
        <td colspan="2"><input name="txtcodnom" type="text" id="txtcodnom" value="<?php print $ls_codnom;?>" size="6" maxlength="4" readonly>
          <a href="javascript: ue_buscarnomina();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
          <input name="txtdesnom" type="text" class="sin-borde" id="txtdesnom" value="<?php print $ls_desnom;?>" size="50" maxlength="100"></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Cargo </div></td>
        <td colspan="2"><input name="txtcodcar" type="text" id="txtcodcar" size="12" maxlength="10" value="<?php print $ls_codcar;?>" readonly>
          <a href="javascript: ue_buscarcargo();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0">
          <input name="txtdescar" type="text" class="sin-borde" id="txtdescar" onKeyUp="ue_validarcomillas(this);" value="<?php print $ls_descar;?>" size="50" maxlength="100">
          </a></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Asignacion de Cargo </div></td>
        <td colspan="2"><input name="txtcodasicar" type="text" id="txtcodasicar" value="<?php print $ls_codasicar;?>" size="12" maxlength="10" readonly>
          <a href="javascript: ue_buscarasignacioncargo();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
          <input name="txtdesasicar" type="text" class="sin-borde" id="txtdesasicar" value="<?php print $ls_desasicar;?>" size="50" maxlength="100"></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Tabulador</div></td>
        <td colspan="2"><div align="left">
          <input name="txtcodtab" type="text" id="txtcodtab" value="<?php print $ls_codtab;?>" size="22" maxlength="20" readonly>
          <input name="txtdestab" type="text" class="sin-borde" id="txtdestab" value="<?php print $ls_destab;?>" size="50" maxlength="100">
        </div></td>
      </tr>
	  <tr>
        <td height="22"><div align="right">Paso </div></td>
        <td colspan="2"><div align="left">
          <input name="txtcodpas" type="text" id="txtcodpas" value="<?php print $ls_codpas;?>" size="17" maxlength="15" readonly>
        </div></td>
      </tr>
	  <tr>
	    <td height="22"><div align="right">Grado </div></td>
	    <td colspan="2"><div align="left"><input name="txtcodgra" type="text" id="txtcodgra" value="<?php print $ls_codgra;?>" size="17" maxlength="15" readonly> 
	    </div></td>
	    </tr>
        <td><div align="right"></div></td>
        <td colspan="2"><input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
			<input name="hidracnom" type="hidden" id="hidracnom" value="<?php print $ls_racnom;?>">			</td>
      </tr>
	  
	  
    </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
f=document.form1;
f.cmbano.length=0;
var fecha = new Date();
actual = fecha.getFullYear();
i=0;
for(inicio=1970;inicio<=actual;inicio++)
{
	f.cmbano.options[i]= new Option(inicio,inicio);
	i++;
}

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.existe.value="FALSE";	
		codper=ue_validarvacio(f.txtcodper.value);
		nomper=ue_validarvacio(f.txtnomper.value);	
		f.action="sigesp_snorh_d_historicocargos.php?codper="+codper+"&nomper="+nomper+"";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_volver()
{
	f=document.form1;
	f.operacion.value="BUSCAR";
	f.existe.value="TRUE";	
	codper=ue_validarvacio(f.txtcodper.value);
	f.action="sigesp_snorh_d_personal.php?codper="+codper;
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	lb_existe=f.existe.value;
	if((((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1)))
	{
		valido=true;
		codper = ue_validarvacio(f.txtcodper.value);
		codnom = ue_validarvacio(f.txtcodnom.value);
		codcar = ue_validarvacio(f.txtcodcar.value);
		codasicar = ue_validarvacio(f.txtcodasicar.value);
		f.txtfecmov.value=ue_validarfecha(f.txtfecmov.value);	
		fecmov = ue_validarvacio(f.txtfecmov.value);
		if ((codper!="")&&(codnom!="")&&(codcar!="")&&(codasicar!="")&&(fecmov!=""))
		{
			f.operacion.value="GUARDAR";
			f.action="sigesp_snorh_d_historicocargos.php";
			f.submit();
		}
		else
		{
			alert("Debe llenar todos los datos.");
		}
   	}
	else
   	{
		alert("No tiene permiso para realizar esta operacion.");
   	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		codper = ue_validarvacio(f.txtcodper.value);
		window.open("sigesp_snorh_cat_historicocargos.php?codper="+codper+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			codper = ue_validarvacio(f.txtcodper.value);
			codnom = ue_validarvacio(f.txtcodnom.value);
			codcar = ue_validarvacio(f.txtcodcar.value);
			codasicar = ue_validarvacio(f.txtcodasicar.value);
			f.txtfecmov.value=ue_validarfecha(f.txtfecmov.value);	
			fecmov = ue_validarvacio(f.txtfecmov.value);
			if ((codper!="")&&(codnom!="")&&(codcar!="")&&(codasicar!="")&&(fecmov!=""))
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.operacion.value="ELIMINAR";
					f.action="sigesp_snorh_d_historicocargos.php";
					f.submit();
				}
			}
			else
			{
				alert("Debe buscar el registro a eliminar.");
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cargar_fecha()
{
	if(f.existe.value=="FALSE")
	{
		f=document.form1;
		f.txtfecmov.value="01/"+f.cmbmes.value+"/"+f.cmbano.value;
	}
}

function ue_buscarnomina()
{
	if(f.existe.value=="FALSE")
	{
		window.open("sigesp_snorh_cat_nomina.php?tipo=historicocargo","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
}

function ue_buscarasignacioncargo()
{
	codnom=f.txtcodnom.value;
	racnom=f.hidracnom.value;
	if (racnom==1)
	{
		if (codnom!='')
		{
			window.open("sigesp_sno_cat_asignacioncargo.php?tipo=historicocargo&codnom="+codnom,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}
		else
		{
			alert('Debe seleccionar una nómina.');
		}
	}
	else
	{
		alert('La nómina debe ser rac.');	
	}
}

function ue_buscarcargo()
{
	codnom=f.txtcodnom.value;
	racnom=f.hidracnom.value;
	if (racnom==0)
	{
		if (codnom!='')
		{
			window.open("sigesp_sno_cat_cargo.php?tipo=historicocargo&codnom="+codnom,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}
		else
		{
			alert('Debe seleccionar una nómina.');
		}
	}
	else
	{
		alert('La nómina no debe ser rac.');	
	}
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
//	window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script>
</html>