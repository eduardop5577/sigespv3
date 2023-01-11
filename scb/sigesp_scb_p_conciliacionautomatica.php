<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
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
	require_once("class_folder/class_funciones_scb.php");
	$io_fun_scb=new class_funciones_scb();
	$ls_permisos="";
	$la_seguridad=Array();
	$la_permisos=Array();	
	$arrResultado=$io_fun_scb->uf_load_seguridad("SCB","sigesp_scb_p_conciliacionautomatica.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<!--  <script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>-->
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
<title >Conciliaci&oacute;n Automatica</title>
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php 
	require_once ("class_folder/sigesp_scb_c_conciliacionautomatica.php");
	require_once("../shared/class_folder/grid_param.php");
	$io_conciliacion = new sigesp_scb_c_conciliacionautomatica();
	$io_grid=new grid_param();
	if( array_key_exists("operacion",$_POST))
        {
		$ls_operacion       = $_POST["operacion"];
		$ls_codban          = $_POST["txtcodban"];
		$ls_denban          = $_POST["txtdenban"];
		$ls_cuenta_banco    = $_POST["txtcuenta"];
		$ls_dencuenta_banco = $_POST["txtdenominacion"];
		$ld_fecha           = $_POST["txtfecha"];
		$ls_periodo         = $_POST["txtperiodo"];
		$ls_mes     	    = $_POST["cmbmes"];
		$ls_codarc        = $_POST["txtcodarc"];
		$ls_denarc         = $_POST["txtdenarc"];
		$ls_tiparc         = $_POST["txttiparc"];
		$ldec_saliniban     = $_POST["txtsaliniban"];
                $ls_existeconciliacion = $_POST["existeconciliacion"];
                $ls_cerradaconciliacion = $_POST["cerradaconciliacion"];
                $li_rowsP=0;
		
	}
	else
        {
		$ls_periodo=substr($_SESSION["la_empresa"]["periodo"],0,4);
		$ls_mes='01';
		$ld_fecha=$ls_mes."/".$ls_periodo;
                $ls_existeconciliacion = 0;
                $ls_cerradaconciliacion = 0;
                $li_rowsP=0;

	}
	
	switch ($ls_operacion)
        {
		case "VERIFICAR":
			$ls_existeconciliacion=$io_conciliacion->uf_verificar_existe($ls_codban,$ls_cuenta_banco,$ls_mes,$ls_periodo);
                        $ls_cerradaconciliacion=$io_conciliacion->uf_verificar_conciliacioncerrada($ls_codban,$ls_cuenta_banco,$ls_mes,$ls_periodo);
			break;

                case "GUARDAR":
			$ls_arctxt=$_FILES["txtarctxt"]["tmp_name"];
                        $ls_tipo=$_FILES["txtarctxt"]["type"];
                        if (($ls_tipo=="application/vnd.ms-excel") || ($ls_tipo=="text/plain"))
                        {
                            if($io_conciliacion->uf_eliminar_movimientoconciliar($ls_codban, $ls_cuenta_banco, $ls_mes, $ls_periodo))
                            {
                                $io_conciliacion->uf_cargar_estado_cuenta($ls_arctxt, $ls_codarc, $ls_codban, $ls_cuenta_banco, $ldec_saliniban, $ls_mes, $ls_periodo);
                            }                           
                        }
                        else
                        {
                            $io_conciliacion->io_msg->message("El Tipo de Archivo no es valido, debe ser .txt, .csv o .xls");
                        }
			$ls_existeconciliacion=$io_conciliacion->uf_verificar_existe($ls_codban, $ls_cuenta_banco, $ls_mes, $ls_periodo);
			break;
                    
		case "PROCESAR":
			$io_conciliacion->uf_conciliar_movimientos($ls_codban, $ls_denban, $ls_cuenta_banco, $ld_fecha, $ldec_saliniban);
                        $titleP[1]="";  	
                        $titleP[2]="<font color=#FFFFFF>Documento</font>";
                        $titleP[3]="<font color=#FFFFFF>Fecha</font>";   
                        $titleP[4]="Concepto"; 
                        $titleP[5]="<font color=#FFFFFF>Monto</font>";
                        $titleP[6]="<font color=#FFFFFF>Operacion</font>";
                        $gridP="gridP";
                        $arrResultado=$io_conciliacion->buscar_porconciliar($ls_codban, $ls_cuenta_banco,$ld_fecha);
                        $li_rowsP=$arrResultado['contP'];
                        $objectP=$arrResultado['objectP'];                                                

                        $titleB[1]="<font color=#FFFFFF>Documento</font>";
                        $titleB[2]="<font color=#FFFFFF>Fecha</font>";   
                        $titleB[3]="<font color=#FFFFFF>Monto</font>";
                        $titleB[4]="<font color=#FFFFFF>Operacion</font>";
                        $gridB="gridB";
                        $arrResultado=$io_conciliacion->buscar_movnobanco($ls_codban, $ls_cuenta_banco,$ls_mes,$ls_periodo);
                        $li_rowsB=$arrResultado['contNoBanco'];
                        $objectB=$arrResultado['objectB'];
			break;

		case "CONCILIAR":
                        $li_total = $_POST["totalrows"];
                        $li_moncon = 0;
                        for ($li_i=1;$li_i<=$li_total;++$li_i)
                        {        
                            if (array_key_exists("chk".$li_i,$_POST))
			    {
                                $ls_numdoc = $_POST["txtnumdocP".$li_i];
                                $ls_estmov = $_POST["txtestmovP".$li_i];
			        $ls_codope = $_POST["txtcodopeP".$li_i];
                                $ldec_monto   = $_POST["txtmontoP".$li_i];
                                $ldec_monto   = str_replace(".","",$ldec_monto);
                                $ldec_monto   = str_replace(",",".",$ldec_monto);
				$li_estcon = 1;
				$ld_feccon     = substr($ld_fecha,3,4).'-'.substr($ld_fecha,0,2).'-'.'01';
                                $valido = $io_conciliacion->uf_conciliar_movimientos_manual($ls_codban,$ls_cuenta_banco,$ls_numdoc,$ls_estmov,$ls_codope,$li_estcon,$ld_feccon);
                                if ($valido)
                                {
                                    $li_moncon = number_format($li_moncon + $ldec_monto, 2, ".", "");
                                }
			    }                            
                        }
                        if ($li_moncon > 0)
                        {
                            $io_conciliacion->uf_actualizar_conciliacion($ls_codban, $ls_cuenta_banco, $ld_fecha, $li_moncon);
                        }
			break;
		
		
	}
	
	$lb_01=$lb_02=$lb_03=$lb_04=$lb_05=$lb_06=$lb_07=$lb_08=$lb_09=$lb_10=$lb_11=$lb_12="";
	switch ($ls_mes){
		case '01':
			$lb_01="selected";
			break;
		case '02':
			$lb_02="selected";
			break;
		case '03':
			$lb_03="selected";
			break;
		case '04':
			$lb_04="selected";
			break;
		case '05':
			$lb_05="selected";
			break;
		case '06':
			$lb_06="selected";
			break;
		case '07':
			$lb_07="selected";
			break;
		case '08':
			$lb_08="selected";
			break;
		case '09':
			$lb_09="selected";
			break;
		case '10':
			$lb_10="selected";
			break;
		case '11':
			$lb_11="selected";
			break;
		case '12':
			$lb_12="selected";
			break;
	}
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
     <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
   <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title='Guardar 'alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title='Ejecutar' alt="Ejecutar" width="20" height="20" border="0"></a></div></td>	
	<td class="toolbar" width="25"><div align="center"><a href="javascript: ue_descargar('<?php echo 'resultado_conciliacion';?>');"><img src="../shared/imagebank/tools20/download.gif" title="Descargar" alt="Salir" width="20" height="20" border="0"></a></div></td>	
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="301"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="71"><div align="center"></div></td>
    <td class="toolbar" width="68"><div align="center"></div></td>
    <td class="toolbar" width="3">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" id="sigesp_scb_p_conciliacionautomatica.php" method="post" enctype="multipart/form-data" action="">
<?php
/////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_scb->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scb);
//////////////////////////////////////////////         SEGURIDAD               ////////////////////////////////////////////
?>		  
<table width="780" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="750" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Conciliaci&oacute;n Automatica</td>
        </tr>
        <tr>
          <td width="89" height="22">&nbsp;</td>
          <td width="639">&nbsp;</td>
        </tr>
        <tr>
      <td  align="right">Periodo</td>
      <td  align="left">
          <input name="txtfecha" type="text" id="txtfecha" style="text-align:center" value="<?php print $ld_fecha;?>" size="10" maxlength="7" readonly>
          <span class="style1">Mes/A&ntilde;o</span>
          <select name="cmbmes" onChange="javascript: uf_periodo(this);">
            <option value="01" <?php print $lb_01;?>>ENERO</option>
            <option value="02" <?php print $lb_02;?>>FEBRERO</option>
            <option value="03" <?php print $lb_03;?>>MARZO</option>
            <option value="04" <?php print $lb_04;?>>ABRIL</option>
            <option value="05" <?php print $lb_05;?>>MAYO</option>
            <option value="06" <?php print $lb_06;?>>JUNIO</option>
            <option value="07" <?php print $lb_07;?>>JULIO</option>
            <option value="08" <?php print $lb_08;?>>AGOSTO</option>
            <option value="09" <?php print $lb_09;?>>SEPTIEMBRE</option>
            <option value="10" <?php print $lb_10;?>>OCTUBRE</option>
            <option value="11" <?php print $lb_11;?>>NOVIEMBRE</option>
            <option value="12" <?php print $lb_12;?>>DICIEMBRE</option>
          </select>
          <input name="txtperiodo" type="text" id="txtperiodo" value="<?php print $ls_periodo ?>" size="6" maxlength="4" style="text-align:center" readonly>
          <input type="hidden" name="hidorden" id="hidorden" value="<?php print $ls_orden?>"/></td>
      <td width="6">&nbsp;</td>
      <td width="6">&nbsp;</td>
    </tr>
    <tr>
      <td width="89" height="22"  align="right">Banco</td>
      <td colspan="3" align="left">
          <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban;?>" size="10" readonly>
          <a href="javascript:cat_bancos();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Bancos"></a>
          <input name="txtdenban" type="text" id="txtdenban" value="<?php print $ls_denban?>" size="100" class="sin-borde" readonly>      </td>
    </tr>
    <tr>
      <td height="22" align="right">Cuenta</td>
      <td colspan="3" align="left">
          <input name="txtcuenta" type="text" id="txtcuenta" style="text-align:center" value="<?php print $ls_cuenta_banco; ?>" size="30" maxlength="25" readonly>
          <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat&aacute;logo de Cuentas Bancarias"></a>
          <input name="txtdenominacion" type="text" class="sin-borde" id="txtdenominacion" style="text-align:left" value="<?php print $ls_dencuenta_banco; ?>" size="80" maxlength="254" readonly>
          </td>
    </tr>
    
        <tr>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22" colspan="2" class="titulo-celdanew">Archivo a Conciliar</td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Configuraci&oacute;n</div></td>
          <td><div align="left">
            <input name="txtcodarc" id="txtcodarc" type="text" size="6" maxlength="4" value="<?php print $ls_codarc; ?>" readonly>
            <a href="javascript: ue_buscararchivo();"><img id="archivo" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdenarc" id="txtdenarc" type="text" class="sin-borde" size="60" maxlength="120" value="<?php print $ls_denarc; ?>" readonly>
			
          </div>
          </td>
        </tr>
        <tr>
          <td height="22"><div align="right">Archivo  </div></td>
          <td><div align="left">
            <input name="txtarctxt" type="file" id="txtarctxt" size="50" maxlength="200">
          </div></td>
        </tr>
        <tr>
            <td height="22">Saldo Seg&uacute;n Banco
            <div align="right"></div></td>
          <td><input name="txtsaliniban" type="text" id="txtsaliniban" style="text-align:right" value="<?php print $ldec_saliniban; ?>" onKeyPress="return(currencyFormat(this,'.',',',event));"></td>
        </tr>
        </table>
    </td>
    </tr>
<?php
if ($li_rowsP>0)
{    
?>  
        <tr>
            <td height="136">
              <p>&nbsp;</p>
              <table width="750" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">        
                <tr>
                    <td width="50%" style="margin-top: 1px">
                        <div align="center">
                            <?php $io_grid->makegrid($li_rowsB,$titleB,$objectB,250,'Movimientos Bancarios',$gridB);?>
                        </div>                
                    </td>
                    <td width="50%" style="margin-top: 1px">
                        <div align="center">
                            <?php $io_grid->makegrid($li_rowsP,$titleP,$objectP,400,'Movimientos por conciliar',$gridP);?>
                            <input name="totalrows" type="hidden" id="totalrows" value="<?php print $li_rowsP;?>">
                        </div>  
                        <div align="center">
                            <input name="btnconciliar" type="button" id="btnconciliar" value="Conciliar" onclick="javascript: ue_conciliar();">
                        </div>                            
                    </td>
                </tr>
             </table>
             </td>
        </tr>
<?php
}    
?>        
        <tr>
            <td height="22">
                <input name="operacion" type="hidden" id="operacion">
                <input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrow;?>">
                <input name="txttiparc" type="hidden" id="txttiparc" value="<?php print $ls_tiparc;?>">
                <input name="existeconciliacion" type="hidden" id="existeconciliacion" value="<?php print $ls_existeconciliacion;?>">
                <input name="cerradaconciliacion" type="hidden" id="cerradaconciliacion" value="<?php print $ls_cerradaconciliacion;?>">
                <input name="accion" type="hidden" id="accion">
            </td>          
        </tr>
      </table>    
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >


function ue_procesar()
{
    f=document.form1;
    li_ejecutar=f.ejecutar.value;
    existeconciliacion=f.existeconciliacion.value;
    saliniban=f.txtsaliniban.value;
    cerradaconciliacion=f.cerradaconciliacion.value;

    if (cerradaconciliacion==0)
    {
        if (existeconciliacion==1)
        {
            if (saliniban!="")
            {
                if (li_ejecutar==1)
                {
                    f.operacion.value="PROCESAR";
                    f.action="sigesp_scb_p_conciliacionautomatica.php";
                    f.submit();			
                }
                else
                {
                    alert("No tiene permiso para realizar esta operacion");
                }		
            }
            else
            {
                alert("Debe llenar el saldo inicial de banco");
            }		
        }
        else
        {
            alert("Primero Debe guardar los registros del banco.");
        }		
    }
    else
    {
        alert("La Conciliacion esta cerrada. Ya no se puede procesar.");
    }		
}

function ue_guardar()
{
    f=document.form1;
    codarc=f.txtcodarc.value;
    arctxt=f.txtarctxt.value;
    li_ejecutar=f.ejecutar.value;
    cerradaconciliacion=f.cerradaconciliacion.value;

    if (cerradaconciliacion==0)
    {
        if (li_ejecutar==1)
        {
            if((arctxt!="")&&(codarc!=""))
            {
                f.operacion.value="GUARDAR";
                f.action="sigesp_scb_p_conciliacionautomatica.php";
                f.submit();			
            }
            else
            {
                alert("Debe seleccionar el archivo a Importar.");
            }
        }
        else
        {
            alert("No tiene permiso para realizar esta operacion");
        }	
    }
    else
    {
        alert("La Conciliacion esta cerrada. Ya no se puede guardar.");
    }		            
}

function ue_cerrar()
{
    location.href = "sigespwindow_blank.php";
}

function ue_buscararchivo()
{
    var f=document.form1;
    var ls_codban=f.txtcodban.value;
    if (ls_codban!="")
    {
        window.open("sigesp_scb_cat_archivoconciliacion.php?tipo=conciliacion&codban="+ls_codban,"Archivos","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
    }
    else
    {
        alert("Seleccione el Banco");   
    }            
}

function ue_descargar(ruta)
{
    window.open("sigesp_scb_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function cat_bancos()
{
    window.open("sigesp_cat_bancos.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
}

function catalogo_cuentabanco()
{
    var f=document.form1;
    var ls_codban=f.txtcodban.value;
    var ls_denban=f.txtdenban.value;
    if (ls_codban!="")
    {
        var pagina="sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_denban;
        window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=700,height=400,resizable=yes,location=no");
    }
    else
    {
        alert("Seleccione el Banco");   
    }
}

function uf_periodo(obj)
{
    var f=document.form1;
    var ls_ano		   = f.txtperiodo.value;
    var ls_periodo	   = obj.value;
    var ls_periodo	   = ls_periodo+"/"+ls_ano;
    f.txtfecha.value = ls_periodo;
}

function ue_conciliar()
{
    f=document.form1;
    f.operacion.value="CONCILIAR";
    f.action="sigesp_scb_p_conciliacionautomatica.php";
    f.submit();			
}

</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script> 
</html>