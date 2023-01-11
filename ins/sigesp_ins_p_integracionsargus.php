<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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
        print "	window.close();";
        print "</script>";		
    }
    $ls_logusr=$_SESSION["la_logusr"];
    require_once("class_folder/class_funciones_ins.php");
    $oi_fun_instala=new class_funciones_ins("../");
    $ls_permisos = "";
    $la_seguridad = Array();
    $la_permisos = Array();
    $arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_integracionsargus.php",$ls_permisos,$la_seguridad,$la_permisos);
    $ls_permisos = $arrResultado['as_permisos'];
    $la_seguridad = $arrResultado['aa_seguridad'];
    $la_permisos = $arrResultado['aa_permisos'];	
    //////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
    require_once("../shared/class_folder/grid_param.php");
    $io_grid=new grid_param();
    require_once("class_folder/sigesp_ins_c_integracionsargus.php");
    $io_integracion=new sigesp_ins_c_integracionsargus();

    $ls_gestor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","GESTOR_INT","MYSQLT","C");
    $ls_puerto_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","PUERTO_INT","3306","C");
    $ls_servidor_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","SERVIDOR_INT","127.0.0.1","C");
    $ls_basedatos_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","BASE_DATOS_INT","db_sargus","C");
    $ls_login_int=$io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","LOGIN_INT","xxxxxxx","C");
    $ls_password_int=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","PASSWORD_INT","xxxxxx","C"));
    $ls_cuenta_ingreso=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO","303020100","C"));
    $ls_cuenta_ingreso_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_INGRESO_IVA","303020100","C"));
    $ls_cuenta_contable_iva_retenido=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","CUENTA_CONTABLE_IVA_RETENIDO","11401000020000","C"));
    $ld_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","IVA",16,"I"));
    $ld_retencion_iva=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","RETENCION_IVA",75,"I"));    
    $ls_fecha_ult_integracion=trim($io_integracion->uf_select_config("INS","INTEGRACION-SARGUS","ULTIMA_FECHA","2022-01-01","C"));
    $ls_fecha_ult_integracion=$io_integracion->io_funciones->uf_convertirfecmostrar($ls_fecha_ult_integracion);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Integraci&oacute;n con SARGUS</title>
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="js/funcion_ins.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">

</head>
<body>
<?php 
    $ls_operacion=$oi_fun_instala->uf_obteneroperacion();
    $ls_titulo="";
    $ls_resultado="";
    $ld_fechadesde=$ls_fecha_ult_integracion;
    $ld_fechahasta=date("d/m/Y");    
    switch ($ls_operacion) 
    {
        case "BUSCAR":
            $ld_fechadesde=trim($_POST["txtfechadesde"]);
            $ld_fechahasta=trim($_POST["txtfechahasta"]);            
            $io_integracion->gestor_int=$ls_gestor_int;
            $io_integracion->puerto_int=$ls_puerto_int;
            $io_integracion->servidor_int=$ls_servidor_int;
            $io_integracion->basedatos_int=$ls_basedatos_int;
            $io_integracion->login_int=$ls_login_int;
            $io_integracion->password_int=$ls_password_int;
            $io_integracion->fechadesde=$ld_fechadesde;
            $io_integracion->fechahasta=$ld_fechahasta;

            $li_total=$io_integracion->uf_load_datos();
            $ls_titulo = "Resultado de la Busqueda Para Integrar";
            $ls_resultado=" Total de Resgistros a Procesar ".$li_total;
            break;

	case "PROCESAR":
            $ld_fechadesde=trim($_POST["txtfechadesde"]);
            $ld_fechahasta=trim($_POST["txtfechahasta"]);            
            $ld_inicio = date("d/m/Y")." - ".date("H:i:s");
            $io_integracion->gestor_int=$ls_gestor_int;
            $io_integracion->puerto_int=$ls_puerto_int;
            $io_integracion->servidor_int=$ls_servidor_int;
            $io_integracion->basedatos_int=$ls_basedatos_int;
            $io_integracion->login_int=$ls_login_int;
            $io_integracion->password_int=$ls_password_int;
            $io_integracion->fechadesde=$ld_fechadesde;
            $io_integracion->fechahasta=$ld_fechahasta;
            $io_integracion->cuenta_ingreso=$ls_cuenta_ingreso;
            $io_integracion->cuenta_ingreso_iva=$ls_cuenta_ingreso_iva;
            $io_integracion->cuenta_contable_iva_retenido=$ls_cuenta_contable_iva_retenido;
            $io_integracion->iva=$ld_iva;
            $io_integracion->retencion_iva=$ld_retencion_iva;
            
            $ld_ultimafecha=$io_integracion->io_funciones->uf_convertirdatetobd($ld_fechahasta);
            $lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-SARGUS","ULTIMA_FECHA",$ld_ultimafecha,"C");
                
            $io_integracion->uf_integrar_sargus($la_seguridad);
            $ld_fin = date("d/m/Y")." - ".date("H:i:s");
            $ls_titulo = "Resultado de la Integracion";
            $ls_resultado = " Inicio ".$ld_inicio."<br>";
            $ls_resultado .= "    Fin ".$ld_fin."<br>";
            $ls_resultado .= " Total de Registros Integrados ".$io_integracion->totalregistros."<br>";
            $ls_resultado .= " Total de Registros Con Errores ".$io_integracion->totalerrores."<br>";
            if ($io_integracion->totalerrores>0)
            {
                $ls_resultado .= " ".$io_integracion->mensaje."<br>";
            }
            break;
	}
	unset($io_integracion);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Procesar" alt="Procesar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>
</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<label></label>
  <table width="200" border="0" align="center">
    <tr>
      <td><div align="center">
        <table width="570" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
         <td height="22" colspan="3" class="titulo-celdanew">Integraci&oacute;n con SARGUS</td>
      </tr>
      <tr class="formato-blanco">
         <td height="13" colspan="3">&nbsp;</td>
      </tr>
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew">
                <div align="center">Informaci&oacute;n de Busqueda </div>
            </td>
          </tr>
          <tr>
            <td height="21"><div align="right">Ultima Fecha de Integraci&oacute;n</div></td>
            <td height="21" colspan="2"><bold><?php print $ls_fecha_ult_integracion;?></bold></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td width="143" height="21"><div align="right">Fecha Desde</div></td>
            <td height="21" colspan="2"><input name="txtfechadesde" type="text" id="txtfechadesde" style="text-align:center" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_fechadesde; ?>" size="15"  datepicker="true"></td>
	  </tr>
          <tr>
            <td height="21"><div align="right">Fecha Hasta</div></td>
            <td height="21" colspan="2"><input name="txtfechahasta" type="text" id="txtfechahasta" style="text-align:center" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_fechahasta; ?>" size="15"  datepicker="true"></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
          
<?php
        if ($ls_titulo!='')
        {
?>
          <tr>
            <td height="21" colspan="3" class="titulo-celdanew"><?php print $ls_titulo;?></td>
            </tr>
          <tr>
            <td height="21" colspan="3"><?php print $ls_resultado;?></td>
          </tr>
          <tr>
            <td height="21" colspan="3"></td>
          </tr>
<?php
        }
?>          

        </table>
      </div></td>
    </tr>
  </table>
    <p>
       <div id=transferir style="visibility:hidden" align="center"><img src="../shared/imagebank/cargando.gif">Procesando ... </div>		  
    </p>

  <input name="operacion" type="hidden" id="operacion" value="<?php $_REQUEST["OPERACION"] ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);

function ue_nuevo()
{
   location.href='sigesp_ins_p_integracionsargus.php' 
}

function ue_buscar()
{	
    f=document.form1;
    li_leer=f.leer.value;
    fechadesde=f.txtfechadesde.value;
    fechahasta=f.txtfechahasta.value;
    valido=ue_comparar_fechas(fechadesde,fechahasta);
    if(valido)
    {
	if(li_leer==1)
	{
            mostrar('transferir');
            f.operacion.value="BUSCAR";	
            f.action="sigesp_ins_p_integracionsargus.php";
            f.submit();
	}
	else
	{
            alert("No tiene permiso para realizar esta operacion.")
	}	
    }
    else
    {
        alert("El Rango de Fechas, est? incorrecto.");
    }
}

function ue_procesar()
{
    f=document.form1;
    li_ejecutar=f.ejecutar.value;
    if(li_ejecutar==1)
    {
        mostrar('transferir');
        f.operacion.value="PROCESAR";	
        f.action="sigesp_ins_p_integracionsargus.php";
        f.submit();
    }
    else
    {
        alert("No tiene permiso para realizar esta operacion.")
    }	
}

function ue_cerrar()
{
   location.href='sigespwindow_blank.php'; 
}

function mostrar(nombreCapa)
{
    capa= document.getElementById(nombreCapa) ;
    capa.style.visibility="visible"; 
} 
</script>
</html>