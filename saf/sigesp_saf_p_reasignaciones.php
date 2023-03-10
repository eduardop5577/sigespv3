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
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
if(!array_key_exists("la_logusr",$_SESSION))
{
	print "<script language=JavaScript>";
	print "window.close();";
	print "</script>";		
}
$ls_logusr=$_SESSION["la_logusr"];
require_once("class_funciones_activos.php");
$io_fun_activo=new class_funciones_activos();
$ls_permisos="";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_activo->uf_load_seguridad("SAF","sigesp_saf_p_reasignaciones.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos=$arrResultado['as_permisos'];
$la_seguridad=$arrResultado['aa_seguridad'];
$la_permisos=$arrResultado['aa_permisos'];
require_once("sigesp_saf_c_activo.php");
$ls_codemp = $_SESSION["la_empresa"]["codemp"];
$io_saf_tipcat= new sigesp_saf_c_activo();
$ls_rbtipocat=$io_saf_tipcat->uf_select_valor_config($ls_codemp);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   function uf_obtenervalor($as_valor, $as_valordefecto)
   {
	//////////////////////////////////////////////////////////////////////////////
	//	Function:  uf_obtenervalor
	//	Access:    public
	//	Arguments:
    // 				as_valor         //  nombre de la variable que desamos obtener
    // 				as_valordefecto  //  contenido de la variable
    // Description: Funci?n que obtiene el valor de una variable si viene de un submit
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
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_limpiarvariables
		//	Description: Funci?n que limpia todas las variables necesarias en la p?gina
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_cmpmov,$ls_codres,$ls_codresnew,$ls_nomres,$ls_nomresnew,$ls_descmp,$ld_feccmp;
   		global $ls_codcau,$ls_dencau,$ls_estpromov,$li_montot,$ls_status;
   		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows,$ls_titletable,$li_widthtable;
   		global $ls_nametable,$ls_codrespri,$ls_denrespri,$ls_codresuso,$ls_denresuso;
		global $ls_tiprespri,$ls_tipresuso,$ls_coduniadm,$ls_denuniadm,$ls_ubigeo,$ls_fecent,$ls_numcmp;
		
		$ls_cmpmov=$ls_numcmp="";
		$ls_codres="";
		$ls_codresnew="";
		$ls_nomres="";
		$ls_nomresnew="";
		$ls_descmp="";
		$ls_codcau="";
		$ls_dencau="";
		$ls_estpromov=3;
		$ld_feccmp= date("d/m/Y");
		$li_montot=0.00;
		$ls_status="";		
		
		$ls_titletable="Activos";
		$li_widthtable=860;
		$ls_nametable="grid";
		$lo_title[1]="Fecha";
		$lo_title[2]="Activo";
		$lo_title[3]="Identificador";
		$lo_title[4]="Observaci?n";
		$lo_title[5]="Unidad Ant.";
		$lo_title[6]="Responsable Actual";
		$lo_title[7]="Unidad Nueva";
		$lo_title[8]="Responsable Nuevo";
		$lo_title[9]="";
		$li_totrows=1;
		
		$ls_codrespri="";	
		$ls_denrespri="";
		$ls_codresuso="";
		$ls_denresuso="";
		$ls_tiprespri=uf_obtenervalor("cmbtiprespri","-");	
		$ls_tipresuso=uf_obtenervalor("cmbtipresuso","-");
		$ls_coduniadm="";
		$ls_denuniadm="";
		$ls_ubigeo="";
		$ls_fecent="";
   }

   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//         Access: private
		//      Argumento: $aa_object // arreglo de titulos 
		//				   $ai_totrows // ultima fila pintada en el grid
		//	      Returns: 
		//    Description: Funcion que agrega una linea en blanco al final del grid
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 23/03/2006 								Fecha ?ltima Modificaci?n : 23/03/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtfectraact".$ai_totrows."    type=text   id=txtfectraact".$ai_totrows."    class=sin-borde size=10 maxlength=10 readonly>";
		$aa_object[$ai_totrows][2]="<input name=txtdenact".$ai_totrows."       type=text   id=txtdenact".$ai_totrows."       class=sin-borde size=25 maxlength=150 readonly>".
								   "<input name=txtcodact".$ai_totrows."       type=hidden id=txtcodact".$ai_totrows."       class=sin-borde size=17 maxlength=15 readonly>";
		$aa_object[$ai_totrows][3]="<input name=txtidact".$ai_totrows."        type=text   id=txtidact".$ai_totrows."        class=sin-borde size=17 maxlength=15 readonly>";
		$aa_object[$ai_totrows][4]="<input name=txtobstraact".$ai_totrows."    type=text   id=txtobstraact".$ai_totrows."    class=sin-borde size=20 readonly>";
		$aa_object[$ai_totrows][5]="<input name=txtcoduniadm".$ai_totrows."    type=text   id=txtcoduniadm".$ai_totrows."    class=sin-borde size=11 maxlength=10 readonly>";
		$aa_object[$ai_totrows][6]="<input name=txtcodres".$ai_totrows."       type=text   id=txtcodres".$ai_totrows."       class=sin-borde size=11 maxlength=10 readonly>";
		$aa_object[$ai_totrows][7]="<input name=txtcoduniadmnew".$ai_totrows." type=text   id=txtcoduniadmnew".$ai_totrows." class=sin-borde size=11 maxlength=10 readonly>";
		$aa_object[$ai_totrows][8]="<input name=txtcodresnew".$ai_totrows."    type=text   id=txtcodresnew".$ai_totrows."    class=sin-borde size=12 maxlength=10 readonly>";
		$aa_object[$ai_totrows][9]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
		return $aa_object;
   }

   function uf_pintardetalle($lo_object,$ai_totrows,$ai_montot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_pintardetalle
		//         Access: private
		//      Argumento: $aa_object // arreglo de objetos
		//				   $ai_totrows // ultima fila pintada en el grid
		//				   $ai_montot  // monto total del grid
		//	      Returns: 
		//    Description: Funcion que se encarga de repintar el detalle existente en el grid.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creaci?n: 11/04/2006 								Fecha ?ltima Modificaci?n : 11/04/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codact= $_POST["txtcodact1"];
		if($ls_codact!="")
		{
			for($li_i=1;$li_i<$ai_totrows;$li_i++)
			{
				$lb_valido=true;
				$ls_codact=       $_POST["txtcodact".$li_i];
				$ls_denact=       $_POST["txtdenact".$li_i];
				$ld_fectraact=    $_POST["txtfectraact".$li_i];
				$ls_idact=        $_POST["txtidact".$li_i];
				$ls_obstraact=    $_POST["txtobstraact".$li_i];
				$ls_coduniadm=    $_POST["txtcoduniadm".$li_i];
				$ls_codres=       $_POST["txtcodres".$li_i];
				$ls_coduniadmnew= $_POST["txtcoduniadmnew".$li_i];
				$ls_codresnew=    $_POST["txtcodresnew".$li_i];
				if($ls_codact=="")
				{
				  $ai_totrows=$ai_totrows - 1;
				  //  $lb_valido=false;
					break;
				}
	
				$lo_object[$li_i][1]="<input name=txtfectraact".$li_i."    type=text   id=txtfectraact".$li_i."    class=sin-borde size=10 maxlength=10  value='".$ld_fectraact."'    readonly>";
				$lo_object[$li_i][2]="<input name=txtdenact".$li_i."       type=text   id=txtdenact".$li_i."       class=sin-borde size=25 maxlength=150 value='".$ls_denact."'       readonly>".
									 "<input name=txtcodact".$li_i."       type=hidden id=txtcodact".$li_i."       class=sin-borde size=17 maxlength=15  value='".$ls_codact."'       readonly>";
				$lo_object[$li_i][3]="<input name=txtidact".$li_i."        type=text   id=txtidact".$li_i."        class=sin-borde size=17 maxlength=15  value='".$ls_idact."'        readonly>";
				$lo_object[$li_i][4]="<input name=txtobstraact".$li_i."    type=text   id=txtobstraact".$li_i."    class=sin-borde size=20 value='".$ls_obstraact."'                  readonly>";
				$lo_object[$li_i][5]="<input name=txtcoduniadm".$li_i."    type=text   id=txtcoduniadm".$li_i."    class=sin-borde size=11 maxlength=10  value='".$ls_coduniadm."'    readonly>";
				$lo_object[$li_i][6]="<input name=txtcodres".$li_i."       type=text   id=txtcodres".$li_i."       class=sin-borde size=11 maxlength=10  value='".$ls_codres."'       readonly>";
				$lo_object[$li_i][7]="<input name=txtcoduniadmnew".$li_i." type=text   id=txtcoduniadmnew".$li_i." class=sin-borde size=11 maxlength=10  value='".$ls_coduniadmnew."' readonly>";
				$lo_object[$li_i][8]="<input name=txtcodresnew".$li_i."    type=text   id=txtcodresnew".$li_i."    class=sin-borde size=12 maxlength=10  value='".$ls_codresnew."'    readonly>";
				$lo_object[$li_i][9]="<a href=javascript:uf_delete_dt(".$li_i.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
			}
			$lo_object=uf_agregarlineablanca($lo_object,$ai_totrows);
		}
		if(!$lb_valido)
		{
			$ai_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,$ai_totrows);
		}
		$arrResultado['lo_object']=$lo_object;
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ai_montot']=$ai_montot;
		return $arrResultado;		
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript" src="../shared/js/disabled_keys.js"></script>
<title>Reasignaciones</title>
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
<script type="text/javascript" src="js/stm31.js"></script>
<script type="text/javascript" src="js/funciones.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			  <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Activos Fijos</td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
				<tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?PHP print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td> </tr>
	  	</table>
	 </td>
  </tr>
  <tr>
  <?php 
    if ($ls_rbtipocat == 1) 
    {
   ?>
   <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_csc.js"></script></td>
  <?php 
    }
	elseif ($ls_rbtipocat == 2)
	{
   ?>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu_cgr.js"></script></td>
  <?php 
	}
	else
	{
   ?>
	<td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript" src="js/menu.js"></script></td>
  <?php 
	}
   ?>	
  </tr>
  <tr>
    <td height="13" colspan="11" bgcolor="#E7E7E7" class="toolbar">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" width="20" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" title="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Grabar" title="Guardar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" title="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" title="Imprimir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" title="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" title="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="24"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" title="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="618">&nbsp;</td>
  </tr>
</table>
<?php
	require_once("../base/librerias/php/general/sigesp_lib_include.php");
	$in=     new sigesp_include();
	$con= $in->uf_conectar();
	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	$io_sql=  new class_sql($con);
	require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	$io_msg= new class_mensajes();
	require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
	$io_fundb= new class_funciones_db($con);
	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	$io_fun= new class_funciones();
	require_once("sigesp_saf_c_movimiento.php");
	$io_saf= new sigesp_saf_c_movimiento();
	require_once("../shared/class_folder/grid_param.php");
	$in_grid= new grid_param();
	require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
	$io_fec= new class_fecha();
	require_once("sigesp_saf_c_activo.php");
	$io_activo = new sigesp_saf_c_activo();
	$arre=$_SESSION["la_empresa"];
	$ls_codemp=$arre["codemp"];
	$li_totrows = uf_obtenervalor("totalfilas",1);	
	$li_totrowsscg = uf_obtenervalor("totalfilasscg",1);	
	if (array_key_exists("operacion",$_POST))
	{
		$ls_operacion=$_POST["operacion"];
	}
	else
	{
		$ls_operacion="";
		uf_limpiarvariables();
		$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		$ls_readonly="readonly";
	}

	switch ($ls_operacion) 
	{
		case "NUEVO":
			uf_limpiarvariables();
			$ls_readonly="";
			
			$ls_emp="";
			$ls_codemp="";
			$ls_tabla="saf_movimiento";
			$ls_columna="cmpmov";
		
			$ls_cmpmov = $io_fundb->uf_generar_codigo($ls_emp,$ls_codemp,$ls_tabla,$ls_columna);
			$ls_numcmp = $io_fundb->uf_generar_codigo_movimiento_saf("RE");//N?mero de Comprobante Independiente para cada tipo de movimiento.
			$lo_object=uf_agregarlineablanca($lo_object,1);
		break;
		
		case "AGREGARDETALLE":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$li_totrows=$li_totrows+1;
			$ls_cmpmov=$_POST["txtcmpmov"];
			$ls_numcmp=$_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_dencau=$_POST["txtdencau"];
			$ld_feccmp=$_POST["txtfeccmp"];
			$ls_descmp=$_POST["txtdescmp"];
			$ls_status=$_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_denrespri = $_POST["txtdenrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_denresuso = $_POST["txtdenresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_fecent=$_POST["txtfecent"];
			$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
			$lo_object=$arrResultado['lo_object'];
			$li_totrows=$arrResultado['ai_totrows'];
			$li_montot=$arrResultado['ai_montot'];
		break;
		case "GUARDAR":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$ls_codusureg=$_SESSION["la_logusr"];
			$ls_cmpmov=$_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_dencau=$_POST["txtdencau"];
			$ld_feccmp=$_POST["txtfeccmp"];
			$ls_descmp=$_POST["txtdescmp"];
			$ls_status=$_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_tiprespri = $_POST["cmbtiprespri"];	
			$ls_tipresuso = $_POST["cmbtipresuso"];
			$ldt_fecent=$_POST["txtfecent"];
			$ld_date=date("Y-m-d");
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_date);
			if($lb_valido)
			{
				if(($ls_cmpmov!="")&&($ls_codcau!="")&&($li_totrows>1)&&(!empty($ls_numcmp)))
				{
					$ls_estpromov="0";
					$ls_codpro="----------";
					$ls_cedbene="----------";
					$ls_codtipdoc="";
					$ld_feccmpbd=$io_fun->uf_convertirdatetobd($ld_feccmp);
					$ldt_fecent=$io_fun->uf_convertirdatetobd($ldt_fecent);
					
					$lb_existe=$io_saf->uf_saf_select_movimiento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd);
					if($lb_existe)
					{
						$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
						$lo_object=$arrResultado['lo_object'];
						$li_totrows=$arrResultado['ai_totrows'];
						$li_montot=$arrResultado['ai_montot'];
						$io_msg->message("El numero de comprobante ya existe");
						$lb_valido=false;
					}
					else
					{
						$io_sql->begin_transaction();
						$lb_valido=$io_saf->uf_saf_insert_movimento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd,$ls_descmp,
						                                            $ls_codpro,$ls_cedbene,$ls_codtipdoc,$ls_codusureg,
																	$ls_estpromov,$la_seguridad,$ls_codrespri,$ls_codresuso,
																	$ls_coduniadm,$ls_ubigeo,$ls_tiprespri,$ls_tipresuso,
																	$ldt_fecent,"RE",$ls_numcmp);
						if($lb_valido)
						{
							$lb_valido=$io_saf->uf_saf_insert_traslado($ls_codemp,$ls_cmpmov,$ld_feccmpbd,$ls_descmp,$ls_codusureg,$la_seguridad);
							if($lb_valido)
							{
								for($li_i=1;$li_i<$li_totrows;$li_i++)
								{
									$ls_codact=    $_POST["txtcodact".$li_i];
									$ld_fectraact= $_POST["txtfectraact".$li_i];
									$ls_idact=     $_POST["txtidact".$li_i];
									$ls_obstraact= $_POST["txtobstraact".$li_i];
									$ls_coduniadm= $_POST["txtcoduniadm".$li_i];
									$ls_codres=    $_POST["txtcodres".$li_i];
									$ls_coduniadmnew= $_POST["txtcoduniadmnew".$li_i];
									$ls_codresnew= $_POST["txtcodresnew".$li_i];
									$ls_desmov= "";
									
									$li_monact=0.00;
									$ls_estsoc=0;
									$ls_estmov="";
									
									$lb_valido=$io_saf->uf_saf_insert_dt_movimiento($ls_codemp,$ls_cmpmov,$ls_codcau,$ld_feccmpbd,$ls_codact,
																					$ls_idact,$ls_desmov,$li_monact,$ls_estsoc,$ls_estmov,$ls_coduniadmnew,
																					$la_seguridad);
									if($lb_valido)
									{
										$lb_valido=$io_saf->uf_saf_insert_dt_traslado($ls_codemp,$ls_cmpmov,$ld_feccmpbd,$ls_codact,$ls_idact,
																					  $ls_obstraact,$ls_coduniadm,$ls_codres,$ls_coduniadmnew,
																					  $ls_codresnew,$la_seguridad);
		
										if($lb_valido)
										{
											$ls_estact="A";
											if($lb_valido)
											{
												$lb_valido=$io_saf->uf_saf_update_dtareasignacion($ls_codemp,$ls_codact,$ls_idact,$ls_codresnew,$ls_coduniadmnew); 
												if(!$lb_valido)
												{break;}
											}
											else
											{break;}
										}
										else
										{break;}
									}
									else
									{break;}
								}			
								$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
								$lo_object=$arrResultado['lo_object'];
								$li_totrows=$arrResultado['ai_totrows'];
								$li_montot=$arrResultado['ai_montot'];
							}
						}
						if($lb_valido)
						{
							$io_sql->commit();
							$io_msg->message("El registro fue incluido con exito");
							$ls_estpromov=0;
							$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
							$lo_object=$arrResultado['lo_object'];
							$li_totrows=$arrResultado['ai_totrows'];
							$li_montot=$arrResultado['ai_montot'];
						}
						else
						{
							$io_sql->rollback();
							$io_msg->message("No se pudo incluir el registro");
							$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
							$lo_object=$arrResultado['lo_object'];
							$li_totrows=$arrResultado['ai_totrows'];
							$li_montot=$arrResultado['ai_montot'];
						}
					}
				}
				else
				{
					if($li_totrows<=1)
					{
						$io_msg->message("El registro debe tener al menos 1 detalle");
						$lo_object=uf_agregarlineablanca($lo_object,1);
					}
					else
					{
						$io_msg->message("Debe completar los datos");
						$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
						$lo_object=$arrResultado['lo_object'];
						$li_totrows=$arrResultado['ai_totrows'];
						$li_montot=$arrResultado['ai_montot'];
					}
				}
			}			
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
		
		break;
		case "ELIMINAR":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$ls_cmpmovdel = $_POST["txtcmpmov"];
			$ls_numcmpdel = $_POST["txtnumcmp"];
			$ls_codcaudel = $_POST["txtcodcau"];
			$ld_feccmpdel = $_POST["txtfeccmp"];
			$ld_date=date("Y-m-d");
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_date);
			if($lb_valido){
				if(($ls_cmpmovdel!="")&&($ls_codcaudel!="")&&($li_totrows>0)&&(!empty($ls_numcmpdel))){
					$ls_estpromov="0";
					$ls_codpro="----------";
					$ls_cedbene="----------";
					$ls_codtipdoc="";
					$ld_feccmpbd=$io_fun->uf_convertirdatetobd($ld_feccmpdel);
					$ldt_fecent=$io_fun->uf_convertirdatetobd($ldt_fecent);
					
					$lb_existe = $io_saf->uf_saf_select_movimiento($ls_codemp,$ls_cmpmovdel,$ls_codcaudel,$ld_feccmpbd);
					if(!$lb_existe){
						$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
						$lo_object=$arrResultado['lo_object'];
						$li_totrows=$arrResultado['ai_totrows'];
						$li_montot=$arrResultado['ai_montot'];
						$io_msg->message("El numero de comprobante que desea eliminar no existe");
						$lb_valido=false;
					}
					else{
						$io_sql->begin_transaction();
						$li_estcla = $io_activo->uf_select_valor_config($ls_codemp);
						$lb_valido=$io_saf->uf_delete_dt_movimiento($ls_cmpmovdel, $ls_codcaudel, $li_estcla, $ld_feccmpbd, $la_seguridad);
						if($lb_valido){
							$lb_valido=$io_saf->uf_delete_dt_traslado($ls_codemp, $ls_cmpmovdel, $ld_feccmpbd, $la_seguridad);
							if($lb_valido){
								for($li_i=1;$li_i<$li_totrows;$li_i++){
									$ls_codact=    $_POST["txtcodact".$li_i];
									$ld_fectraact= $_POST["txtfectraact".$li_i];
									$ls_idact=     $_POST["txtidact".$li_i];
									$ls_obstraact= $_POST["txtobstraact".$li_i];
									$ls_coduniadm= $_POST["txtcoduniadm".$li_i];
									$ls_codres=    $_POST["txtcodres".$li_i];
									$ls_coduniadmnew= $_POST["txtcoduniadmnew".$li_i];
									$ls_codresnew= $_POST["txtcodresnew".$li_i];
									$ls_desmov= "";
									
									$li_monact=0.00;
									$ls_estsoc=0;
									$ls_estmov="";
											
									$lb_valido=$io_saf->uf_saf_reverso_dtareasignacion($ls_codemp,$ls_codact,$ls_idact); 
									if(!$lb_valido){
										break;
									}
								}
							}
							$lb_valido = $io_saf->uf_delete_movimiento($ls_cmpmovdel, $ls_codcaudel, $li_estcla, $ld_feccmpbd, $la_seguridad);
							if ($lb_valido) {
								$io_saf->uf_delete_traslado($ls_codemp, $ls_cmpmovdel, $ld_feccmpbd, $la_seguridad);
							}
						}
						
						if($lb_valido){
							$io_sql->commit();
							$io_msg->message("El registro fue eliminado con exito");
							$ls_estpromov=0;
							$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
							$lo_object=$arrResultado['lo_object'];
							$li_totrows=$arrResultado['ai_totrows'];
							$li_montot=$arrResultado['ai_montot'];
						}
						else{
							$io_sql->rollback();
							$io_msg->message("No se pudo eliminar el registro");
							$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
							$lo_object=$arrResultado['lo_object'];
							$li_totrows=$arrResultado['ai_totrows'];
							$li_montot=$arrResultado['ai_montot'];
						}
					}
				}
				else
				{
					if($li_totrows<=1)
					{
						$io_msg->message("El registro debe tener al menos 1 detalle");
						$lo_object=uf_agregarlineablanca($lo_object,1);
					}
					else
					{
						$io_msg->message("Debe completar los datos");
						$arrResultado=uf_pintardetalle($lo_object,$li_totrows,$li_montot);
						$lo_object=$arrResultado['lo_object'];
						$li_totrows=$arrResultado['ai_totrows'];
						$li_montot=$arrResultado['ai_montot'];
					}
				}
			}			
			else{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
		break;
		case "PROCESAR":
			$ls_cmpmov=$_POST["txtcmpmov"];
			$ls_numcmp=$_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_status=$_POST["hidstatus"];
			$ls_estpromov=1;
			$ld_date=date("Y-m-d");
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_date);
			if($lb_valido)
			{
				$io_sql->begin_transaction();
				$lb_valido=$io_saf->uf_saf_update_procesarincorporacion($ls_codemp,$ls_cmpmov,$ls_codcau,$ls_estpromov,$la_seguridad);
				if($lb_valido)
				{
					$io_sql->commit();
					$io_msg->message("El registro fue procesado con exito");
					uf_limpiarvariables();
					$lo_object=uf_agregarlineablanca($lo_object,1);
					$li_totrows=1;
				}
				else
				{
					$io_sql->rollback();
					$io_msg->message("No se pudo procesar el registro");
					uf_limpiarvariables();
					$lo_object=uf_agregarlineablanca($lo_object,1);
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
		break;

		case "REVERSAR":
			$ls_cmpmov=$_POST["txtcmpmov"];
			$ls_numcmp=$_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_status=$_POST["hidstatus"];
			$ls_estpromov=0;
			$ld_date=date("Y-m-d");
			$lb_valido=$io_fec->uf_valida_fecha_mes($ls_codemp,$ld_date);
			if($lb_valido)
			{
				$io_sql->begin_transaction();
				$lb_valido=$io_saf->uf_saf_update_procesarincorporacion($ls_codemp,$ls_cmpmov,$ls_codcau,$ls_estpromov,$la_seguridad);
				if($lb_valido)
				{
					$io_sql->commit();
					$io_msg->message("El registro fue reversado con exito");
					$lo_object=uf_agregarlineablanca($lo_object,1);
					uf_limpiarvariables();
					$li_totrows=1;
				}
				else
				{
					$io_sql->rollback();
					$io_msg->message("No se pudo reversar el registro");
					uf_limpiarvariables();
					$lo_object=uf_agregarlineablanca($lo_object,1);
				}
			}
			else
			{
				$io_msg->message("El mes no esta abierto");
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
				uf_limpiarvariables();
			}
		break;

		case "ELIMINARDETALLEACTIVO":
			uf_limpiarvariables();
			$li_totrows = uf_obtenervalor("totalfilas",1);
			$ls_cmpmov=$_POST["txtcmpmov"];
			$ls_numcmp=$_POST["txtnumcmp"];
			$ls_codcau=$_POST["txtcodcau"];
			$ls_dencau=$_POST["txtdencau"];
			$ld_feccmp=$_POST["txtfeccmp"];
			$ls_descmp=$_POST["txtdescmp"];
			$ls_status=$_POST["hidstatus"];
			$li_totrows=$li_totrows-1;
			$li_rowdelete=$_POST["filadelete"];
			$li_temp=0;
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{		
					$li_temp=$li_temp+1;			
					$ls_codact=       $_POST["txtcodact".$li_i];
					$ls_denact=       $_POST["txtdenact".$li_i];
					$ld_fectraact=    $_POST["txtfectraact".$li_i];
					$ls_idact=        $_POST["txtidact".$li_i];
					$ls_obstraact=    $_POST["txtobstraact".$li_i];
					$ls_coduniadm=    $_POST["txtcoduniadm".$li_i];
					$ls_codres=       $_POST["txtcodres".$li_i];
					$ls_coduniadmnew= $_POST["txtcoduniadmnew".$li_i];
					$ls_codresnew=    $_POST["txtcodresnew".$li_i];
					
					$lo_object[$li_temp][1]="<input name=txtfectraact".$li_temp."    type=text id=txtfectraact".$li_temp."    class=sin-borde size=10 maxlength=10 value='".$ld_fectraact."'    readonly>";
					$lo_object[$li_temp][2]="<input name=txtdenact".$li_temp."       type=text   id=txtdenact".$li_temp."     class=sin-borde size=25 maxlength=150 value='".$ls_denact."'      readonly>".
										    "<input name=txtcodact".$li_temp."       type=hidden id=txtcodact".$li_temp."     class=sin-borde size=17 maxlength=15  value='".$ls_codact."'      readonly>";
					$lo_object[$li_temp][3]="<input name=txtidact".$li_temp."        type=text id=txtidact".$li_temp."        class=sin-borde size=17 maxlength=15 value='".$ls_idact."'        readonly>";
					$lo_object[$li_temp][4]="<input name=txtobstraact".$li_temp."    type=text id=txtobstraact".$li_temp."    class=sin-borde size=20 value='".$ls_obstraact."'                 readonly>";
					$lo_object[$li_temp][5]="<input name=txtcoduniadm".$li_temp."    type=text id=txtcoduniadm".$li_temp."    class=sin-borde size=11 maxlength=10 value='".$ls_coduniadm."'    readonly>";
					$lo_object[$li_temp][6]="<input name=txtcodres".$li_temp."       type=text id=txtcodres".$li_temp."       class=sin-borde size=11 maxlength=10 value='".$ls_codres."'       readonly>";
					$lo_object[$li_temp][7]="<input name=txtcoduniadmnew".$li_temp." type=text id=txtcoduniadmnew".$li_temp." class=sin-borde size=11 maxlength=10 value='".$ls_coduniadmnew."' readonly>";
					$lo_object[$li_temp][8]="<input name=txtcodresnew".$li_temp."    type=text id=txtcodresnew".$li_temp."    class=sin-borde size=12 maxlength=10 value='".$ls_codresnew."'    readonly>";
					$lo_object[$li_temp][9]="<a href=javascript:uf_delete_dt(".$li_temp.");><img src=../shared/imagebank/tools15/eliminar.gif alt=Aceptar width=15 height=15 border=0></a>";
				}
				else
				{
					$ls_codactelim=    $_POST["txtcodact".$li_i];
					$ls_idactelim=     $_POST["txtidact".$li_i];
					$li_rowdelete= 0;
				}
			}
			if ($li_temp==0)
			{
				$li_totrows=1;
			}
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		break;
	
		case "BUSCARDETALLE":
			uf_limpiarvariables();
			$ls_cmpmov=    $_POST["txtcmpmov"];
			$ls_numcmp = $_POST["txtnumcmp"];
			$ls_codcau=    $_POST["txtcodcau"];
			$ls_dencau=    $_POST["txtdencau"];
			$ld_feccmp=    $_POST["txtfeccmp"];
			$ls_estpromov= $_POST["hidestpromov"];
			$ls_status=    $_POST["hidstatus"];
			$ls_codrespri = $_POST["txtcodrespri"];
			$ls_denrespri = $_POST["txtdenrespri"];
			$ls_codresuso = $_POST["txtcodresuso"];
			$ls_denresuso = $_POST["txtdenresuso"];
			$ls_coduniadm = $_POST["txtcoduniadm"];
			$ls_denuniadm = $_POST["txtdenuniadm"];
			$ls_ubigeo = $_POST["txtubigeo"];
			$ls_fecent=$_POST["txtfecent"];
			$ld_feccmpbd=$io_fun->uf_convertirdatetobd($ld_feccmp);

			$arrResultado=$io_saf->uf_siv_load_dt_movreasignacion($ls_codemp,$ls_cmpmov,$ld_feccmpbd,$li_totrows,$lo_object,$li_montot);
			$li_totrows= $arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$li_montot=$arrResultado['ai_montot'];
			$lb_valido=$arrResultado['lb_valido'];
			if (!$lb_valido)
			{
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
		break;
	}
?>
<p>&nbsp;</p>
<div align="center">
  <form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_activo->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_activo);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
    <table width="740" height="159" border="0" class="formato-blanco">
      <tr>
        <td width="724" ><div align="left">
            <table width="706" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
              <tr>
                <td colspan="3" class="titulo-ventana">Reasignaciones</td>
              </tr>
              <tr class="formato-blanco">
                <td >&nbsp;</td>
                <td ><div align="right">Fecha</div></td>
                <td ><input name="txtfeccmp" type="text" id="txtfeccmp" style="text-align:center " value="<?php print $ld_feccmp ?>" size="13" maxlength="10" onKeyPress="ue_separadores(this,'/',patron,true);" datepicker="true"></td>
              </tr>
              <tr class="formato-blanco" style="visibility:hidden">
                <td height="20"><div align="right">Reporte en</div></td>
                <td height="20" colspan="2"><div align="left">
                    <select name="cmbbsf" id="cmbbsf">
                      <option value="0" selected>Bs.</option>
                      <option value="1">Bs.F.</option>
                    </select>
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Comprobante</div></td>
                <td height="20" colspan="2">
                  <input name="txtnumcmp" type="text" id="txtnumcmp" style="text-align:center" value="<?php print $ls_numcmp ?>" size="20" maxlength="15" readonly>
                  <input name="hidstatus" type="hidden" id="hidstatus" value="<?php print $ls_status ?>">
                  <input name="txtcmpmov" type="hidden" id="txtcmpmov" value="<?php print $ls_cmpmov ?>" size="20" maxlength="15" onBlur="javascript: ue_rellenarcampo(this,'15')" style="text-align:center "></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Causa de Movimiento</div></td>
                <td height="20" colspan="2"><input name="txtcodcau" type="text" id="txtcodcau" value="<?php print $ls_codcau ?>" size="10" style="text-align:center " readonly>
                  <a href="javascript: ue_catacausas();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0"></a>
                <input name="txtdencau" type="text" class="sin-borde" id="txtdencau" value="<?php print $ls_dencau ?>" size="50" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Responsable Primario</div></td>
                <td height="20" colspan="2"><select name="cmbtiprespri" id="cmbtiprespri" onChange="javascript: ue_catalogo_responsable_primario();">
                    <option value="-" selected>-- Seleccione Uno --</option>
                    <option value="P" <?php if($ls_tiprespri=="P"){ print "selected";} ?>>PERSONAL</option>
                    <option value="B" <?php if($ls_tiprespri=="B"){ print "selected";} ?>>BENEFICIARIO</option>
                  </select>
                    <input name="txtcodrespri" type="text"  style="text-align:center" class="sin-borde" id="txtcodrespri" value="<?php print $ls_codrespri; ?>" size="15" maxlength="10" readonly>
                    <input name="txtdenrespri" type="text"  style="text-align:left" class="sin-borde" id="txtdenrespri" value="<?php print $ls_denrespri ?>" size="45" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Responsable de Uso </div></td>
                <td height="20" colspan="2"><select name="cmbtipresuso" id="cmbtipresuso" onChange="javascript: ue_catalogo_responsable_uso();">
                    <option value="-" selected>-- Seleccione Uno --</option>
                    <option value="P" <?php if($ls_tipresuso=="P"){ print "selected";} ?>>PERSONAL</option>
                    <option value="B" <?php if($ls_tipresuso=="B"){ print "selected";} ?>>BENEFICIARIO</option>
                  </select>
                    <input name="txtcodresuso" type="text" style="text-align:center" class="sin-borde" id="txtcodresuso" value="<?php print $ls_codresuso; ?>" size="15" maxlength="10" readonly>
                    <input name="txtdenresuso" type="text" style="text-align:left" class="sin-borde" id="txtdenresuso" value="<?php print $ls_denresuso; ?>" size="45" readonly></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Ubicacion Organizacional </div></td>
                <td height="26" colspan="2"><label>
                  <input name="txtcoduniadm" type="text" id="txtcoduniadm" value="<?php print $ls_coduniadm; ?>" size="10" maxlength="15" readonly>
                  <a href="javascript: ue_catalogo_unidad_administrativa();"><img src="../shared/imagebank/tools15/buscar.gif" alt=" " width="15" height="15" border="0"></a>
                  <input name="txtdenuniadm" type="text" class="sin-borde" id="txtdenuniadm" value="<?php print $ls_denuniadm; ?>" size="45" readonly>
                </label></td>
              </tr>
              <tr class="formato-blanco">
                <td height="20"><div align="right">Fecha  de la  Entrega </div></td>
                <td height="20" colspan="2"><div align="left">
                    <label></label>
                    <label>
                    <input name="txtfecent" type="text" id="txtfecent" onKeyPress="ue_separadores(this,'/',patron,true);" value="<?php print $ls_fecent; ?>" size="13" maxlength="10" datepicker="true">
                    </label>
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="26"><div align="right">Ubicacion Geografica</div></td>
                <td height="26" colspan="2"><div align="left">
                    <textarea name="txtubigeo" cols="60" rows="2" id="txtubigeo"  onKeyUp="javascript: ue_validarcomillas(this)" onBlur="javascript: ue_validarcomillas(this)"><?php print $ls_ubigeo; ?></textarea>
                </div></td>
              </tr>
              
              <tr class="formato-blanco">
                <td height="28"><div align="right">Observaciones</div></td>
                <td rowspan="2"><textarea name="txtdescmp" cols="60" rows="3" id="txtdescmp" onKeyUp="javascript: ue_validarcomillas(this)" onBlur="javascript: ue_validarcomillas(this)"><?php print $ls_descmp ?></textarea></td>
                <td style="visibility:hidden"><div align="center">
                  <input name="btnprocesar" type="button" class="boton" value="Procesar"  style="width:80px "<?php if($ls_estpromov==0){ print "onClick='javascript: ue_procesar();'";}?>>
                  <input name="hidestpromov" type="hidden" id="hidestpromov" value="<?php print $ls_estpromov ?>">
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="23"><div align="right"></div></td>
                <td style="visibility:hidden"><div align="center">
                  <input name="btnreversar" type="button" class="boton" value="Reversar" style="width:80px "  <?php if($ls_estpromov==1){ print "onClick='javascript: ue_reversar();'";}?> >
                </div></td>
              </tr>
              <tr class="formato-blanco">
                <td height="17" colspan="3"><a href="javascript: ue_agregardetalle();"><img src="../shared/imagebank/tools/nuevo.gif" width="15" height="15" class="sin-borde">Agregar Activo</a></td>
              </tr>
              <tr class="formato-blanco">
                <td height="22" colspan="3"><div align="center">
                 <?php
					$in_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
				?>
                </div></td>
              </tr>
            </table>
            <input name="operacion" type="hidden" id="operacion">
            <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
            <input name="filadelete" type="hidden" id="filadelete">
            <input name="txtmontot" type="hidden" id="txtmontot" value="<?php print number_format($li_montot,2,",","."); ?>" size="20" style="text-align:right " readonly>
        </div></td>
      </tr>
    </table>
  </form>
</div>
<p align="center">&nbsp;</p>
</body>
<script >
//Funciones de operaciones 
function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_saf_cat_reasignaciones.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_agregardetalle()
{
	f=document.form1;
	ls_cmpmov=f.txtcmpmov.value;
	if(ls_cmpmov=="")
	{
		alert("Debe existir un numero de comprobante");
	}
	else
	{
		li_totrow=f.totalfilas.value;
		window.open("sigesp_saf_pdt_reasignacionact.php?totrow="+ li_totrow +"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=380,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catalogo_responsable_primario()
{
	f=document.form1;
	tipresuso=f.cmbtiprespri.value;
	if(tipresuso=='P')
	{
		window.open("sigesp_saf_cat_personal.php?destino=repasignadospri","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    }
	else if(tipresuso=='B')
	{
		window.open("sigesp_saf_cat_beneficiario.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catalogo_responsable_uso()
{
	f=document.form1;
	tipresuso=f.cmbtipresuso.value;
	if(tipresuso=='P')
	{
		window.open("sigesp_saf_cat_personal.php?destino=repasignadosuso","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
    }
	else if(tipresuso=='B')
	{
		window.open("sigesp_saf_cat_beneficiario.php?destino=responsableuso","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
	}
}

function ue_catalogo_unidad_administrativa()
{
	f=document.form1;
	window.open("sigesp_saf_cat_unidadejecutora.php?destino=reasignaciones","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_catacausas()
{
	tipo="R";
	window.open("sigesp_saf_cat_causasmovimiento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
}

function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_saf_p_reasignaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_guardar()
{
	f=document.form1;
	li_totrows=f.totalfilas.value;
	ls_status=f.hidstatus.value;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	if(((ls_status=="C")&&(li_cambiar==1))||(ls_status=="")&&(li_incluir==1))
	{
		if(ls_status!="C")
		{
				if(li_totrows>1)
				{
					f.operacion.value="GUARDAR";
					f.action="sigesp_saf_p_reasignaciones.php";
					f.submit();
				}
				else
				{alert("El movimiento debe tener al menos 1 detalle");}
		}
		else
		{alert("Este documento no debe ser modificado");}
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_procesar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		f.operacion.value="PROCESAR";
		f.action="sigesp_saf_p_reasignaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_reversar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		f.operacion.value="REVERSAR";
		f.action="sigesp_saf_p_reasignaciones.php";
		f.submit();
	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function uf_delete_dt(li_row)
{
	f=document.form1;
	li_fila=f.totalfilas.value;
	if(li_fila!=li_row)
	{
		if(confirm("?Desea eliminar el Registro actual?"))
		{	
			f.filadelete.value=li_row;
			f.operacion.value="ELIMINARDETALLEACTIVO"
			f.action="sigesp_saf_p_reasignaciones.php";
			f.submit();
		}
	}
}

function ue_imprimir()
{
	f = document.form1;
	ls_status = f.hidstatus.value;
	ls_cmpmov = f.txtcmpmov.value;
	li_imprimir = f.imprimir.value;
	tipoformato = f.cmbbsf.value;
	if(ls_status=="C")
	{
		if (li_imprimir==1)
		{
			window.open("reportes/sigesp_saf_rfs_reasignacion.php?cmpmov="+ls_cmpmov+"&tipoformato="+tipoformato,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}
	else
	{
		alert("Seleccione un documento a imprimir");
	}
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}

function ue_eliminar(){
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1){
		f.operacion.value="ELIMINAR";
		f.action="sigesp_saf_p_reasignaciones.php";
		f.submit();
	}
	else{
		alert("No tiene permiso para realizar esta operaci?n.");
	}
}
//--------------------------------------------------------
//	Funci?n que coloca los separadores (/) de las fechas
//--------------------------------------------------------
var patron = new Array(2,2,4)
var patron2 = new Array(1,3,3,3,3)
function ue_separadores(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++){
			val2 += val[r]	
		}
		if(nums){
			for(z=0;z<val2.length;z++){
				if(isNaN(val2.charAt(z))){
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++){
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++){
			if(q ==0){
				val = val3[q]
			}
			else{
				if(val3[q] != ""){
					val += sep + val3[q]
					}
			}
		}
	d.value = val
	d.valant = val
	}
}
</script> 
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>