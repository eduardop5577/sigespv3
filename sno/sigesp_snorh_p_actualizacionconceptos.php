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
	$la_seguridad=Array();
	$la_permisos=Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_p_actualizacionconceptos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$ls_spgcuenta=$io_sno->uf_select_config("SNO","NOMINA","SPGCUENTA","401","C");
	unset($io_sno);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_cargarnomina()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cargarnomina
		//		   Access: private
		//	  Description: Función que obtiene todas las nóminas y las carga en un 
		//				   combo para seleccionarlas
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

		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom ".
				"  FROM sno_nomina ".
				" INNER JOIN sss_permisos_internos ".
				"    ON sno_nomina.codemp='".$ls_codemp."'".
				"   AND sno_nomina.peractnom<>'000'".
				"   AND sss_permisos_internos.codsis='SNO'".
				"   AND sss_permisos_internos.enabled=1".
				"   AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
				"   AND sno_nomina.codemp = sss_permisos_internos.codemp ".
				"   AND sno_nomina.codnom = sss_permisos_internos.codintper ".
				" GROUP BY sno_nomina.codnom, sno_nomina.desnom ".
				" ORDER BY sno_nomina.codnom, sno_nomina.desnom ";
		$rs_data=$io_sql->select($ls_sql);
       	print "<select name='cmbnomina' id='cmbnomina' style='width:400px'>";
        print " <option value='' selected>--Seleccione Una--</option>";
		if($rs_data===false)
		{
        	$io_mensajes->message("Clase->Actualizar Conceptos Método->uf_cargarnomina Error->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			print "<script language=JavaScript>";
			print "	close();";
			print "</script>";		
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codnom=$row["codnom"];
				$ls_desnom=$row["desnom"];
            	print "<option value='".$ls_codnom."'>".$ls_codnom."-".$ls_desnom."</option>";				
			}
			$io_sql->free_result($rs_data);
		}
       	print "</select>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);	
		unset($io_mensajes);		
		unset($io_funciones);		
        unset($ls_codemp);
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $li_totrows,$ls_operacion,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$io_fun_nomina,$ls_sigcon;

		$ls_sigcon=$io_fun_nomina->uf_obtenervalor("cmbsigcon","A");
		$ls_titletable="Conceptos";
		$li_widthtable=1200;
		$ls_nametable="grid";
		
		$lo_title[1]="Nómina";
		$lo_title[2]="Código";
		$lo_title[3]="Nombre";
		$lo_title[4]="Formula";
		$lo_title[5]="Condición";	
		$lo_title[6]="Aplica Impuesto Sobre Renta";		
		$lo_title[7]="Concepto Global";
		$lo_title[8]="Aplica ARC";
		$lo_title[9]="Sueldo Integral";
		$lo_title[10]="Evaluar en prenomina";
		$lo_title[11]="Sueldo Integral de Vacaciones";
		$lo_title[12]="Salario Normal";
		$lo_title[13]="Antiguedad Complementaria";
		$lo_title[14]="Concepto del salario normal";
		if (($ls_sigcon=='A')||($ls_sigcon=='E'))
		{
			$lo_title[15]="Cuenta Presupuestaria";
		}
		if (($ls_sigcon=='D')||($ls_sigcon=='P')||($ls_sigcon=='B'))
		{
			$lo_title[15]="Cuenta Contable";
		}
		if ($ls_sigcon=='P')
		{
			$li_widthtable=1500;
			$lo_title[16]="Formula Aporte";
			$lo_title[17]="Cuenta Presupuestaria Aporte";
			$lo_title[18]="Cuenta Contable Aporte";
			$lo_title[19]="Destino de contabilizacion";
		}
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
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
		global $ls_sigcon;
		
		$aa_object[$ai_totrows][1]=" ";
		$aa_object[$ai_totrows][2]=" ";
		$aa_object[$ai_totrows][3]=" ";
		$aa_object[$ai_totrows][4]=" ";
		$aa_object[$ai_totrows][5]=" ";
		$aa_object[$ai_totrows][6]=" ";
		$aa_object[$ai_totrows][7]=" ";
		$aa_object[$ai_totrows][8]=" ";
		$aa_object[$ai_totrows][9]=" ";
		$aa_object[$ai_totrows][10]=" ";
		$aa_object[$ai_totrows][11]=" ";
		$aa_object[$ai_totrows][12]=" ";
		$aa_object[$ai_totrows][13]=" ";
		$aa_object[$ai_totrows][14]=" ";
		$aa_object[$ai_totrows][15]=" ";
		if ($ls_sigcon=='P')
		{		
			$aa_object[$ai_totrows][16]=" ";
			$aa_object[$ai_totrows][17]=" ";
			$aa_object[$ai_totrows][18]=" ";
			$aa_object[$ai_totrows][19]=" ";
		}
		return $aa_object;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables($ai_i)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/03/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina, $io_concepto;
   		global $ls_codnom, $ls_codconc, $ls_nomcon, $ls_forcon, $ls_concon, $ls_cueprecon, $ls_cueconcon;
		global $ls_aplisrcon, $ls_glocon, $ls_aplarccon, $ls_sueintcon, $ls_sueintvaccon, $li_conprenom;
		global $ls_persalnor, $ls_diasadd, $ls_salnor, $ls_forpatcon, $ls_cueprepatcon, $ls_cueconpatcon;
		global $ls_codprov, $ls_codben, $ls_descon, $ls_coddescon;
		
		$ls_codnom=$_POST["txtcodnom".$ai_i];
		$ls_codconc=$_POST["txtcodconc".$ai_i];
		$ls_nomcon=$_POST["txtnomcon".$ai_i];
		$ls_forcon=$_POST["txtforcon".$ai_i];
		$ls_concon=$_POST["txtconcon".$ai_i];
		$ls_aplisrcon=$io_fun_nomina->uf_obtenervalor("chkaplisrcon".$ai_i,"0");
		$ls_glocon=$io_fun_nomina->uf_obtenervalor("chkglocon".$ai_i,"0");
		$ls_aplarccon=$io_fun_nomina->uf_obtenervalor("chkaplarccon".$ai_i,"0");
		$ls_sueintcon=$io_fun_nomina->uf_obtenervalor("chksueintcon".$ai_i,"0");
		$ls_sueintvaccon=$io_fun_nomina->uf_obtenervalor("chksueintvaccon".$ai_i,"0");
		$li_conprenom=$io_fun_nomina->uf_obtenervalor("chkconprenom".$ai_i,"0");
		$ls_persalnor=$io_fun_nomina->uf_obtenervalor("chkpersalnor".$ai_i,"0");
		$ls_diasadd=$io_fun_nomina->uf_obtenervalor("chkdiasadd".$ai_i,"0");
		$ls_salnor=$io_fun_nomina->uf_obtenervalor("cmbconsalnor".$ai_i,"");
		$ls_cueprecon=$io_fun_nomina->uf_obtenervalor("txtcuepre".$ai_i,"");
		$ls_cueconcon=$io_fun_nomina->uf_obtenervalor("txtcuecon".$ai_i,"");
		$ls_forpatcon=$io_fun_nomina->uf_obtenervalor("txtforpatcon".$ai_i,"");
		$ls_cueprepatcon=$io_fun_nomina->uf_obtenervalor("txtcueprepatcon".$ai_i,"");
		$ls_cueconpatcon=$io_fun_nomina->uf_obtenervalor("txtcueconpatcon".$ai_i,"");
		$ls_descon=$io_fun_nomina->uf_obtenervalor("cmbdescon".$ai_i,"");
		$ls_codprov="----------";
		$ls_codben="----------";
		if($ls_descon=="P")
		{
			$ls_codprov=$io_fun_nomina->uf_obtenervalor("txtcodproben".$ai_i,"");
			$ls_coddescon=$ls_codprov;
		}
		if($ls_descon=="B")
		{
			$ls_codben=$io_fun_nomina->uf_obtenervalor("txtcodproben".$ai_i,"");
			$ls_coddescon=$ls_codben;
		}
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
<title >Actualizaci&oacute;n de Conceptos en Lote</title>
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
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_sno_c_concepto.php");
	$io_concepto=new sigesp_sno_c_concepto();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			break;

		case "GUARDAR":
			$li_totalfilas=$_POST["totalfilas"];
			$lb_valido=true;
	       	$io_concepto->io_sql->begin_transaction();
			for($li_i=1;($li_i<=$li_totalfilas)&&($lb_valido);$li_i++)
			{
				uf_load_variables($li_i);
				$lb_valido=$io_concepto->uf_update_conceptolote($ls_codnom, $ls_codconc, $ls_nomcon, $ls_forcon, $ls_concon, $ls_cueprecon, $ls_cueconcon,
																$ls_aplisrcon, $ls_glocon, $ls_aplarccon, $ls_sueintcon, $ls_sueintvaccon, $li_conprenom,
																$ls_persalnor, $ls_diasadd, $ls_salnor, $ls_forpatcon, $ls_cueprepatcon, $ls_cueconpatcon,
																$ls_codprov, $ls_codben,$la_seguridad);
			}
			if($lb_valido)
			{
				$io_concepto->io_mensajes->message("Los Conceptos fueron actualizados.");
				$io_concepto->io_sql->commit();
			}
			else
			{
				$io_concepto->io_mensajes->message("Ocurrio un error al Actualizar los conceptos.");
				$io_concepto->io_sql->rollback();
			}
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			break;
			
		case "BUSCAR":
			$ls_sigcon=$io_fun_nomina->uf_obtenervalor("cmbsigcon","");
			$ls_codconc=$_POST["txtcodconc"];
			$ls_nomcon=$_POST["txtnomcon"];
			$ls_codnom=$io_fun_nomina->uf_obtenervalor("cmbnomina","");
			$arrResultado=$io_concepto->uf_buscar_conceptoslote($ls_codnom,$ls_codconc,$ls_nomcon,$ls_sigcon);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
			break;
	}
	$io_concepto->uf_destructor();
	unset($io_concepto);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="762" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif"  title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"></a><a href="javascript: ue_eliminar();"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
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
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="960" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td>
      <p>&nbsp;</p>
      <table width="920" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Actualizaci&oacute;n de Conceptos </td>
        </tr>
        <tr>
          <td width="135" height="22"><div align="right">Signo</div></td>
          <td width="539"><select name="cmbsigcon" id="cmbsigcon">
            <option value="A" selected>Asignaci&oacute;n</option>
            <option value="D">Deducci&oacute;n</option>
            <option value="P">Aporte Patronal</option>
            <option value="R">Reporte</option>
            <option value="B">Reintegro Deducci&oacute;n</option>
            <option value="E">Reintegro Asignaci&oacute;n</option>
            <option value="X">Prestaci&oacute;n Antiguedad</option>
            <option value="I">Intereses de Prestacion</option>
          </select></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Nómina</div></td>
          <td><div align="left"><?php uf_cargarnomina(); ?></div></td>
        </tr>
        <tr>
          <td><div align="right">Concepto</div></td>
          <td><div align="left">
            <input name="txtcodconc" type="text" id="txtcodconc" size="13" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,10);">
          </div></td>
        </tr>
        <tr>
          <td><div align="right">Nombre</div></td>
          <td><div align="left">
            <input name="txtnomcon" type="text" id="txtnomcon"  size="33" maxlength="30" onKeyUp="javascript: ue_validarcomillas(this);">
          </div></td>
        </tr>
        <tr>
          <td><div align="right"></div></td>
          <td>
		  	<input name="operacion" type="hidden" id="operacion">		  </td>
        </tr>
        <tr>
          <td colspan="2">
		  	<div align="center">
			    <?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
				?>
			  </div>
              <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">			</td>		  
          </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >
function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	if((li_cambiar==1)||(li_incluir==1))
	{
		li_total=f.totalfilas.value;
		valido=true;
		for(li_i=1;(li_i<=li_total)&&(valido);li_i++)
		{
			codnom = ue_validarvacio(eval("f.txtcodnom"+li_i+".value"));
			codconc = ue_validarvacio(eval("f.txtcodconc"+li_i+".value"));
			nomcon = ue_validarvacio(eval("f.txtnomcon"+li_i+".value"));
			forcon = ue_validarvacio(eval("f.txtforcon"+li_i+".value"));
			concon = ue_validarvacio(eval("f.txtconcon"+li_i+".value"));
			sigcon = ue_validarvacio(eval("f.txtsigcon"+li_i+".value"));
			destino = '-';
			codproben = '----------';
			if((sigcon=="A")||(sigcon=="E"))
			{
				cuenta= ue_validarvacio(eval("f.txtcuepre"+li_i+".value"));
			}
			if((sigcon=="D")||(sigcon=="P")||(sigcon=="B"))
			{
				cuenta= ue_validarvacio(eval("f.txtcuecon"+li_i+".value"));
			}
			if(sigcon=="P")
			{
				destino = ue_validarvacio(eval("f.cmbdescon"+li_i+".value"));
				codproben = ue_validarvacio(eval("f.txtcodproben"+li_i+".value"));
				forpatcon = ue_validarvacio(eval("f.txtforpatcon"+li_i+".value"));
				cueprepat = ue_validarvacio(eval("f.txtcueprepatcon"+li_i+".value"));
				cueconpat = ue_validarvacio(eval("f.txtcueconpatcon"+li_i+".value"));
				if ((forpatcon=="")||(cueprepat=="")||(cueconpat==""))
				{
					valido=false;
					alert("Debe llenar los datos del Aporte Patronal Concepto "+codconc+".");
				}
				if(valido)
				{
					valido=ue_validar_formula(forpatcon,"Fórmula del concepto Patrón Inválida (IIF) Concepto "+codconc+".");
				}
			}
			if(valido)
			{
				valido=ue_validar_formula(forcon,"Formula del concepto Inválida (IIF) Concepto "+codconc+".");
			}
			if(valido)
			{
				valido=ue_validar_formula(concon,"Condición del concepto Inválida (IIF) Concepto "+codconc+".");
			}
			if(valido)
			{
				if ((codconc!="")&&(nomcon!="")&&(sigcon!="")&&(forcon!="")&&(cuenta!="")&&(destino!="")&&(codproben!=""))
				{
					valido=true;
				}
				else
				{
					alert("Debe llenar todos los datos Concepto "+codconc+".");
					valido=false;
				}
			}
		}
		if(valido)
		{
			f.operacion.value="GUARDAR";					
			f.action="sigesp_snorh_p_actualizacionconceptos.php";
			f.submit();
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		sigcon = ue_validarvacio(f.cmbsigcon.value);
		if (sigcon=='')
		{
	 		alert("Debe Seleccionar un signo de Concepto.");
		}
		else
		{
			f.operacion.value="BUSCAR";
			f.action="sigesp_snorh_p_actualizacionconceptos.php";
			f.submit();
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}

function ue_chequear_encargaduria(nro,tipo)
{
	f=document.form1;
	if(tipo==1)
	{
		alert('El concepto es de tipo encargaduria no se puede tildar como global.');
		eval("f.chkglocon"+nro+".checked=false");
	}
}

function uf_verificar_chksalnor(nro)
{
	f = document.form1;
	if (!eval("f.chkpersalnor"+nro+".checked"))
	{ 
		alert("Debe tildar la opción Pertenece a Salario Normal para seleccionar un concepto.");
		eval("f.cmbconsalnor"+nro+".value=''");
	}
}

function ue_buscarcuentapresupuesto(nro)
{
	window.open("sigesp_sno_cat_cuentapresupuesto.php?spg_cuenta=<?php print $ls_spgcuenta;?>&tipo=LOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarcuentapresupuesto_patron(nro)
{
	window.open("sigesp_sno_cat_cuentapresupuesto.php?spg_cuenta=<?php print $ls_spgcuenta;?>&tipo=PATRONALLOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarcuentacontable(nro)
{
	window.open("sigesp_sno_cat_cuentacontable.php?tipo=LOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarcuentacontable_patron(nro)
{
	window.open("sigesp_sno_cat_cuentacontable.php?tipo=PATRONALLOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscardestino(nro)
{
	f=document.form1;
	descon = eval("ue_validarvacio(f.cmbdescon"+nro+".value)");
	if(descon!="")
	{
		if(descon=="P")
		{
			window.open("sigesp_catdinamic_prove.php?tipo=LOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}
		else
		{
			window.open("sigesp_catdinamic_bene.php?tipo=LOTE&nro="+nro,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
		}	
	}
	else
	{
		alert("Debe seleccionar un destino de Contabilización.");
	}
}

function ue_validar_formula(formula, texto)
{
	valido=true;	
	len=formula.length;
	pos=strpos(formula,"II");
	if(pos==-1)
	{
		pos=strpos(formula,"ii");
	}
	aux=formula;
	while((pos>=0)&&(valido))
	{
		cadena=aux.substr(pos,3);
		aux=aux.substr(pos+3,len);	
		if((cadena!='IIF')&&(cadena!='iif'))
		{
			valido=false;
			alert(texto);
		}
		else
		{
			pos=strpos(aux, 'i');
		}	
	} 
	return valido;
}

function strpos(str, ch)
{
	for (var i = 0; i < str.length; i++)
	if (str.substring(i, i+1) == ch) return i;
	return -1;
}
</script> 
</html>