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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_antiguedadanterior.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	global $ls_sueint;
	$ls_sueint=trim($io_sno->uf_select_config("SNO","NOMINA","DENOMINACION SUELDO INTEGRAL","C",""));
	if ($ls_sueint=="")
	{
		$ls_sueint="Sueldo Integral";
	}
	unset($io_sno);
   
   //--------------------------------------------------------------
   function uf_limpiarvariables($ls_sueint)
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $li_totrows,$ls_operacion,$ls_titletable,$li_widthtable,$ls_nametable;
		global $lo_title,$ls_existe,$ls_metodofideicomiso,$io_fun_nomina,$la_nomsele;

		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
		$ls_titletable="Prestación Antiguedad";
		$li_widthtable=600;
		$ls_nametable="grid";
		$lo_title[1]="Periodo";
		$lo_title[2]=$ls_sueint;
		$lo_title[3]="Asig. Extra";
		$lo_title[4]="Monto Vacacion";
		$lo_title[5]="Monto Aguinaldo";
		$lo_title[6]="Monto Aporte";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
		$ls_metodofideicomiso=$io_sno->uf_select_config("SNO","CONFIG","METODO FIDECOMISO","VERSION 2","C");
		unset($io_sno);
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_agregarlineablanca
		//	Arguments: aa_object  // arreglo de Objetos
		//			   ai_totrows  // total de Filas
		//	Description:  Función que agrega una linea mas en el grid
		//////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]=" ";
		$aa_object[$ai_totrows][2]=" ";
		$aa_object[$ai_totrows][3]=" ";
		$aa_object[$ai_totrows][4]=" ";
		$aa_object[$ai_totrows][5]=" ";
		$aa_object[$ai_totrows][6]=" ";
		return $aa_object;
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
   		global $ls_codper, $ls_nomper, $ld_fecnacper, $ld_fecingper, $ld_fecingadmpubper; 
		global $li_anodesde,$ls_mesdesde,$li_anohasta,$ls_meshasta,$ls_codnom,$li_totrows,$ls_operacion,$ls_existe,$io_fun_nomina;
		
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ld_fecnacper=$_POST["txtfecnacper"];
		$ld_fecingper=$_POST["txtfecingper"];
		$ld_fecingadmpubper=$_POST["txtfecingadmpubper"];
	 	$li_anodesde=$_POST["cmbanodesde"];
		$ls_mesdesde=$_POST["cmbmesdesde"];
	 	$li_anohasta=$_POST["cmbanohasta"];
		$ls_meshasta=$_POST["cmbmeshasta"];
		$ls_codnom=$_POST["txtcodnom"];
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
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
<title >Prestaci&oacute;n Antiguedad Anterior</title>
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
	require_once("sigesp_snorh_c_fideicomiso.php");
	$io_fideicomiso=new sigesp_snorh_c_fideicomiso();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables($ls_sueint);
	switch ($ls_operacion) 
	{
		case "NUEVO":
		 	$ls_codper=$_GET["codper"];
			$ls_nomper=$_GET["nomper"];
			$ld_fecnacper=$_GET["fecnacper"];
			$ld_fecingper=$_GET["fecingper"];
			$ld_fecingadmpubper=$_GET["fecingadmpubper"];
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,1);
			$arrResultado=$io_fideicomiso->uf_load_antiguedadanterior($ls_codper,$li_totrows,$lo_object,$ls_sueint);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
			break;

		case "PROCESAR":
			uf_load_variables();
			$lb_valido=false;
			switch(trim($ls_metodofideicomiso))
			{
				case "VERSION 2":
					$lb_valido=$io_fideicomiso->uf_procesar_fideicomiso_anterior_version2($li_anodesde,$ls_mesdesde,$li_anohasta,$ls_meshasta,$ls_codnom,$ls_codper,$la_seguridad);
					break;

				case "VERSION CONSEJO":
					$lb_valido=$io_fideicomiso->uf_procesar_fideicomiso_anterior_version_consejo($li_anodesde,$ls_mesdesde,$li_anohasta,$ls_meshasta,$ls_codnom,$ls_codper,$la_seguridad);
					break;
			}
			if($lb_valido===false)
			{
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,1);
				$ls_existe="FALSE";
			}
			else
			{
				$ls_existe="TRUE";
				$arrResultado=$io_fideicomiso->uf_load_antiguedadanterior($ls_codper,$li_totrows,$lo_object,$ls_sueint);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			break;
	}
	$io_fideicomiso->uf_destructor();
	unset($io_fideicomiso);
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
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Ejecutar" alt="Ejecutar" width="20" height="20" border="0"></a><a href="javascript: ue_guardar();"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"></a><a href="javascript: ue_volver();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"></a><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_volver();"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"></a></div></td>
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
<table width="783" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="781">	<p>&nbsp;</p>      <table width="701" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
      <tr>
        <td colspan="3"><div align="center">
            <input name="txtnomper" type="text" class="sin-borde2" id="txtnomper" value="<?php print $ls_nomper;?>" size="60" readonly>
            <input name="txtcodper" type="hidden" id="txtcodper" value="<?php print $ls_codper;?>">
        </div></td>
      </tr>
      <tr class="titulo-ventana">
        <td height="20" colspan="3" class="titulo-ventana">Prestaci&oacute;n Antiguedad Anterior </td>
      </tr>
      <tr>
        <td width="132" height="22"><div align="right">N&oacute;mina</div></td>
        <td width="563" colspan="2"><input name="txtcodnom" type="text" id="txtcodnom" size="13" maxlength="10" readonly>
          <a href="javascript: ue_buscarnomina();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0">
          <label>
          <input name="txtdesnom" type="text" size="70" id="txtdesnom">
          </label>
          </a></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Periodo Desde </div></td>
        <td colspan="2"><div align="left">
          <label>
          <select name="cmbmesdesde" id="cmbmesdesde">
            <option value="1" selected>Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
          </label>
          <select name="cmbanodesde" id="cmbanodesde">
          </select>
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Periodo Hasta </div></td>
        <td colspan="2"><div align="left">
          <select name="cmbmeshasta" id="cmbmeshasta">
            <option value="1" selected>Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
          <select name="cmbanohasta" id="cmbanohasta">
          </select>
        </div></td>
        </tr>
      <tr>
        <td height="22"><div align="right">Fecha de Ingreso </div></td>
        <td colspan="2"><div align="left">
          <label>
          <input name="txtfecingper" type="text" id="txtfecingper" value="<?php print $ld_fecingper;?>" readonly>
          </label>
        </div></td>
      </tr>
      <tr>
        <td colspan="3"><div align="right"></div>
		  	<div align="center">
			    <?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
				?>
			  </div>
		  	<input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
		    <input name="operacion" type="hidden" id="operacion">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
			<input name="txtfecingadmpubper" type="hidden" id="txtfecingadmpubper" value="<?php print $ld_fecingadmpubper;?>">			
            <input name="txtfecnacper" type="hidden" id="txtfecnacper" value="<?php print $ld_fecnacper;?>"></td>
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
f.cmbanodesde.length=0;
f.cmbanohasta.length=0;
var fecha = new Date();
actual = fecha.getFullYear();
i=0;
for(inicio=1970;inicio<=actual;inicio++)
{
	f.cmbanodesde.options[i]= new Option(inicio,inicio);
	f.cmbanohasta.options[i]= new Option(inicio,inicio);
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
		fecnacper=ue_validarvacio(f.txtfecnacper.value);	
		fecingper=ue_validarvacio(f.txtfecingper.value);
		fecingadmpubper=ue_validarvacio(f.txtfecingadmpubper.value);
		f.action="sigesp_snorh_d_antiguedadanterior.php?codper="+codper+"&nomper="+nomper+"&fecnacper="+fecnacper+"&fecingper="+fecingper+"&fecingadmpubper="+fecingadmpubper+"";
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

function ue_procesar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		mesdesde=ue_validarvacio(f.cmbmesdesde.value);
		anodesde=ue_validarvacio(f.cmbanodesde.value);
		meshasta=ue_validarvacio(f.cmbmeshasta.value);
		anohasta=ue_validarvacio(f.cmbanohasta.value);
		codnom=ue_validarvacio(f.txtcodnom.value);
		if ((mesdesde!="")&&(anodesde!="")&&(meshasta!="")&&(anohasta!="")&&(codnom!=""))
		{
			f.operacion.value="PROCESAR";
			f.action="sigesp_snorh_d_antiguedadanterior.php";
			f.submit();
		}
		else
		{
			alert("Debe seleccionar el periodo desde, periodo hasta y la nómina.");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
//	window.open("../hlp/index.php?sistema=SNO&subsistema=SNR&nomfis=sno/sigesp_hlp_snr_personal.php","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}

function ue_buscarnomina()
{
	window.open("sigesp_snorh_cat_nomina.php?tipo=prestacionantiguedadant","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script>
</html>