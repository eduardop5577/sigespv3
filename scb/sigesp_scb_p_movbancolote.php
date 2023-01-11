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
	require_once("class_funciones_banco.php");
	$io_fun_scb=new class_funciones_banco();
$ls_permisos="";
$la_seguridad=Array();
$la_permisos=Array();	
	$arrResultado=$io_fun_scb->uf_load_seguridad("SCB","sigesp_scb_p_movbanco.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado["as_permisos"];
	$la_seguridad=$arrResultado["aa_seguridad"];
	$la_permisos=$arrResultado["aa_permisos"];
	unset($arrResultado);
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_fun_scb,$ls_operacion,$ls_codtipsol,$ld_fecregdes,$ld_fecreghas,$ld_fecaprord,$li_totrow;
		
		$ls_operacion=$io_fun_scb->uf_obteneroperacion();
		$ls_codtipsol="";
		$ld_fecregdes=date("01/m/Y");
		$ld_fecreghas=date("d/m/Y");
		$ld_fecaprord=date("d/m/Y");
		$li_totrow=0;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codfuefin,$ld_denfuefin;
		
		//$li_totrow = $_POST["totrow"];
		$ls_tipope = $_POST["rdtipooperacion"];
		$ld_fecaprord  =$_POST["txtfecaprord"];
   }
   //--------------------------------------------------------------
   //--------------------------------------------------------------
   function uf_load_data()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 29/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		global $ls_parametros,$ls_codfuefin,$ls_denfuefin,$li_totrow,$ls_comision;
		$li_totrow=$_POST["rows"];
		$ls_codfuefin=$_POST["txtftefinanciamiento"];
		$ls_denfuefin=$_POST["txtdenftefinanciamiento"];
		$ls_comision=$_POST["rbcomision"];
		$ls_parametros="";
		$li_islr=0;
		for($li_fila=1;($li_fila<=$li_totrow);$li_fila++)
		{
			$ls_operacion=$_POST["txtoperacion".$li_fila];
			$ls_documento=$_POST["txtdocumento".$li_fila];
			$ls_fecha=$_POST["txtfecha".$li_fila];
			$ls_denban=$_POST["txtbanco".$li_fila];
			$ls_codban=$_POST["txtcodban".$li_fila];
			$ls_disponible=$_POST["txtdisponible".$li_fila];
			$ls_sccuenta=$_POST["txtsccuenta".$li_fila];
			$ls_ctaban=$_POST["txtcuenta".$li_fila];
			$li_monto=$_POST["txtmonto".$li_fila];
			if($ls_comision=="1")
			{
				$li_islr=$_POST["txtmonislr".$li_fila];
			}

			$ls_parametros=$ls_parametros."&txtoperacion".$li_fila."=".$ls_operacion."&txtdocumento".$li_fila."=".$ls_documento."&txtfecha".$li_fila."=".$ls_fecha."".
					   					  "&txtbanco".$li_fila."=".$ls_denban."&txtcodban".$li_fila."=".$ls_codban."".
										  "&txtdisponible".$li_fila."=".$ls_disponible."&txtsccuenta".$li_fila."=".$ls_sccuenta."".
										  "&txtcuenta".$li_fila."=".$ls_ctaban."&txtmonto".$li_fila."=".$li_monto."&txtmonislr".$li_fila."=".$li_islr;
		}
		if($li_fila>1)
		{
			$ls_parametros=$ls_parametros."&rows=".$li_totrow."&txtcodfuefin=".$ls_codfuefin;
			$ls_parametros=$ls_parametros."&txtdenfuefin=".$ls_denfuefin."&rbcomision=".$ls_comision;
		}
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
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
<title >Movimiento de Banco por Lote </title>
<meta http-equiv="" content="text/html; charset=iso-8859-1">
<meta http-equiv="" content="text/html; charset=iso-8859-1">
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
<script type="text/javascript"  src="js/funcion_scb.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
		$codemp=$_SESSION['la_empresa']['codemp'];
		$ls_codfuefin=$_POST["txtcodfuefin"];
		$ls_denfuefin=$_POST["txtdenfuefin"];
		//require_once("class_folder/sigesp_scb_c_comision.php");
		//$io_co= new sigesp_scb_c_comision();
		//require_once("class_folder/sigesp_scb_c_asientos.php");
		//$io_ca= new sigesp_scb_c_asientos();
		//require_once("class_folder/sigesp_scb_c_formapago.php");
		//$io_fp= new sigesp_scb_c_formapago();
		require_once ('../modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');
		$servicioBancario = new ServicioMovimientoScb();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_msg= new class_mensajes();
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$io_fecha= new class_fecha();
		require_once("sigesp_scb_c_movbanco.php");
		$io_movbanco= new sigesp_scb_c_movbanco($la_seguridad);

		uf_load_data();
		$ls_operacion=$io_fun_scb->uf_obteneroperacion();
		switch ($ls_operacion)
		{
			case "PROCESAR";
				//$li_totrow=$io_fun_scb->uf_obtenervalor($_POST["rows"],1);
				$li_comision=$io_fun_scb->uf_obtenervalor("rbcomision","0");
				$ls_codfuefin=$io_fun_scb->uf_obtenervalor("txtftefinanciamiento","--");
				$ls_denfuefin=$io_fun_scb->uf_obtenervalor("txtdenftefinanciamiento","");
				for($li_fila=1;($li_fila<=$li_totrow);$li_fila++)
				{
					$ls_valido=true;
					$ls_concepto="";
					$ls_operacion=$_POST["txtoperacion".$li_fila];
					switch($ls_operacion)
					{
						case"DEP":
							$ls_concepto="Deposito ";
							$ls_operacion="DP";
							$ls_procede="SCBBDP";
							$li_islr=0;
						break;
						case"ND":
							$ls_concepto="Comision ";
							$ls_operacion="ND";
							$ls_procede="SCBBDP";
							$li_islr=$io_fun_scb->uf_obtenervalor("txtmonislr".$li_fila,"");
						break;
					}	
					$ls_documento=str_pad(trim($io_fun_scb->uf_obtenervalor("txtdocumento".$li_fila,"")),15,"0",0);
					$ls_codforpag=$io_fun_scb->uf_obtenervalor("txtcodforpag".$li_fila,"");
					$arrFormaPago=$io_fp->uf_select_formapago($ls_codforpag);
					if(!$arrFormaPago->EOF)
					{
						$ls_denforpag=$arrFormaPago->fields["denforpag"];
					}
					$ls_codoficom=$io_fun_scb->uf_obtenervalor("txtcodoficom".$li_fila,"");
					$ls_fecha=$io_fecha->uf_convert_date_to_db($io_fun_scb->uf_obtenervalor("txtfecha".$li_fila,""));
					$ls_denban=$io_fun_scb->uf_obtenervalor("txtbanco".$li_fila,"");
					$ls_codban=$io_fun_scb->uf_obtenervalor("txtcodban".$li_fila,"");
					$ls_disponible=$io_fun_scb->uf_obtenervalor("txtdisponible".$li_fila,"");
					$ls_sccuenta=$io_fun_scb->uf_obtenervalor("txtsccuenta".$li_fila,"");
					$ls_ctaban=$io_fun_scb->uf_obtenervalor("txtcuenta".$li_fila,"");
					$li_monto=$io_fun_scb->uf_obtenervalor("txtmonto".$li_fila,"");
					$li_monto=    str_replace(".","",$li_monto);
					$li_monto=    str_replace(",",".",$li_monto);
					$li_islr=    str_replace(".","",$li_islr);
					$li_islr=    str_replace(",",".",$li_islr);
					$ls_concepto=$ls_concepto.$ls_denforpag." (".$ls_codforpag.") No.".$ls_documento;
					$ls_datosasientos=false;
					$ls_datoscomision=true;
					
					$arrParametros["codban"]=$ls_codban;
					$arrParametros["ctaban"]=$ls_ctaban;
					$arrParametros["codforpag"]=$ls_codforpag;
					$arrResultado=$io_ca->uf_select_asientos($arrParametros);
					
					if(!$arrResultado->EOF)
					{
						$ls_spicta=$arrResultado->fields["spi_cuenta"];
						$ls_spicon=$arrResultado->fields["spi_contable"];
						$ls_codestpro1=str_pad($arrResultado->fields["codestpro1"],25,'0',0);
						$ls_codestpro2=str_pad($arrResultado->fields["codestpro2"],25,'0',0);
						$ls_codestpro3=str_pad($arrResultado->fields["codestpro3"],25,'0',0);
						$ls_codestpro4=str_pad($arrResultado->fields["codestpro4"],25,'0',0);
						$ls_codestpro5=str_pad($arrResultado->fields["codestpro5"],25,'0',0);
						$ls_estcla=$arrResultado->fields["estcla"];
						$ls_spgcta=$arrResultado->fields["spg_cuenta"];
						$ls_spgcon=$arrResultado->fields["spg_contable"];
						$ls_islrcon=$arrResultado->fields["islr_contable"];
						$ls_datosasientos=true;
					}
					if(($ls_datosasientos)&&($ls_datoscomision))
					{
						//Genera Numero
						require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
						$io_keygen= new sigesp_c_generar_consecutivo();
						//$ls_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","");
						$ls_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBRE',15,'valinimovban','','',$_SESSION["la_logusr"]);
					
						$arrCabecera=array();
						$arregloSCG=array();
						$arregloSPG=array();
						$arregloSPI=array();
				
						$arrCabecera["codemp"]	 = $codemp;
						$arrCabecera["codban"]	 = $ls_codban;
						$arrCabecera["ctaban"]	 = $ls_ctaban;
						$arrCabecera["numdoc"]	 = $ls_documento;
						$arrCabecera["codope"]	 = $ls_operacion;
						$arrCabecera["estmov"]	 = 'N';
						$arrCabecera["cod_pro"]	 = '----------';
						$arrCabecera["ced_bene"] = '----------';
						$arrCabecera["tipo_destino"]	 = '-';
						$arrCabecera["codconmov"] = '---';
						$arrCabecera["fecmov"]	 = $ls_fecha;
						$arrCabecera["conmov"]	 = $ls_concepto;
						$arrCabecera["nomproben"] = 'Ninguno';
						$arrCabecera["monto"]	 = 0;
						$arrCabecera["estbpd"]	 = 'M';
						$arrCabecera["estcon"]	 = 0;
						$arrCabecera["estcobing"] = 0;
						$arrCabecera["esttra"] = 0;
						$arrCabecera["chevau"]	 = "";
						$arrCabecera["estimpche"]	 = 0;
						$arrCabecera["monobjret"] = number_format($li_monto,2,".","");
						$arrCabecera["monret"]	 = 0;
						$arrCabecera["procede"]	 = $ls_procede;
						$arrCabecera["comprobante"]	 = $ls_documento;
						$arrCabecera["fecha"]	 = '1900-01-01';
						$arrCabecera["id_mco"] = ' ';
						$arrCabecera["emicheproc"] = 0;
						$arrCabecera["emicheced"] = ' ';
						$arrCabecera["emichenom"] = ' ';
						$arrCabecera["emichefec"] = '1900-01-01';
						$arrCabecera["estmovint"] = 0;
						$arrCabecera["codusu"] = $_SESSION['la_logusr'];
						$arrCabecera["codopeidb"] = ' ';
						$arrCabecera["aliidb"] = 0;
						$arrCabecera["feccon"] = '1900-01-01';
						$arrCabecera["estreglib"] = ' ';
						$arrCabecera["numcarord"] = "";
						$arrCabecera["numpolcon"] = 0;
						$arrCabecera["coduniadmsig"] = ' ';
						$arrCabecera["codbansig"]	 = ' ';
						$arrCabecera["fecordpagsig"]	 = '1900-01-01';
						$arrCabecera["tipdocressig"]	 = ' ';
						$arrCabecera["numdocressig"]	 = ' ';
						$arrCabecera["estmodordpag"]	 = '0';
						$arrCabecera["codfuefin"] = $ls_codfuefin;
						$arrCabecera["forpagsig"] = ' ';
						$arrCabecera["medpagsig"] = ' ';
						$arrCabecera["codestprosig"] = ' ';
						$arrCabecera["tranoreglib"] = 0;
						$arrCabecera["numordpagmin"]	 = '-';
						$arrCabecera["codtipfon"] = '----';
						$arrCabecera["estmovcob"] = 0;
						$arrCabecera["numconint"] = $ls_numcontint;
						$arrCabecera["numchequera"] = "";
						$arrCabecera["monto"]	 = $li_monto;

						switch($ls_operacion)
						{
							case"DP":
								$arregloSPI['codemp'][0]= $arrCabecera['codemp'];
								$arregloSPI['codban'][0]= $arrCabecera['codban'];
								$arregloSPI['ctaban'][0]= $arrCabecera['ctaban'];
								$arregloSPI['numdoc'][0]= $arrCabecera['numdoc'];
								$arregloSPI['codope'][0]= $arrCabecera['codope'];
								$arregloSPI['estmov'][0]= $arrCabecera['estmov'];
								$arregloSPI['spicuenta'][0]= $ls_spicta;
								$arregloSPI['documento'][0]= $ls_documento;
								$arregloSPI['operacion'][0]= "DC";
								$arregloSPI['desmov'][0]= $ls_concepto;
								$arregloSPI['procede_doc'][0]= $ls_procede;
								$arregloSPI['monto'][0]= number_format($li_monto,2,".","");
								$arregloSPI['codestpro1'][0]= '-------------------------';
								$arregloSPI['codestpro2'][0]= '-------------------------';
								$arregloSPI['codestpro3'][0]= '-------------------------';
								$arregloSPI['codestpro4'][0]= '-------------------------';
								$arregloSPI['codestpro5'][0]= '-------------------------';
								$arregloSPI['estcla'][0]= '-';

								$arregloSCG['codemp'][0]= $arrcabecera['codemp'];
								$arregloSCG['codban'][0]= $arrcabecera['codban'];
								$arregloSCG['ctaban'][0]= $arrcabecera['ctaban'];
								$arregloSCG['numdoc'][0]= $arrcabecera['numdoc'];
								$arregloSCG['codope'][0]= $arrcabecera['codope'];
								$arregloSCG['estmov'][0]= $arrcabecera['estmov'];
								$arregloSCG['scg_cuenta'][0]= $ls_sccuenta;
								$arregloSCG['debhab'][0]= "D";
								$arregloSCG['codded'][0]= '00000';
								$arregloSCG['documento'][0]= $ls_documento;
								$arregloSCG['desmov'][0]= $ls_concepto;
								$arregloSCG['procede_doc'][0]= $ls_procede;
								$arregloSCG['monto'][0]= number_format($li_monto,2,".","");
								$arregloSCG['monobjret'][0]= number_format($li_monto,2,".","");
		
								$arregloSCG['codemp'][1]= $arrcabecera['codemp'];
								$arregloSCG['codban'][1]= $arrcabecera['codban'];
								$arregloSCG['ctaban'][1]= $arrcabecera['ctaban'];
								$arregloSCG['numdoc'][1]= $arrcabecera['numdoc'];
								$arregloSCG['codope'][1]= $arrcabecera['codope'];
								$arregloSCG['estmov'][1]= $arrcabecera['estmov'];
								$arregloSCG['scg_cuenta'][1]= $ls_spicon;
								$arregloSCG['debhab'][1]= "H";
								$arregloSCG['codded'][1]= '00000';
								$arregloSCG['documento'][1]= $ls_documento;
								$arregloSCG['desmov'][1]= $ls_concepto;
								$arregloSCG['procede_doc'][1]= $ls_procede;
								$arregloSCG['monto'][1]= number_format($li_monto,2,".","");
								$arregloSCG['monobjret'][1]= 0;
								
							break;
							case"ND":
								$li_montohaber=($li_monto+$li_islr);
								$ls_programa=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
								$arregloSPG["codemp"][0] = $arrcabecera['codemp'];
								$arregloSPG["codban"][0] = $arrcabecera['codban'];
								$arregloSPG["ctaban"][0] = $arrcabecera['ctaban'];
								$arregloSPG["numdoc"][0] = $arrcabecera['numdoc'];
								$arregloSPG["codope"][0] = $arrcabecera['codope'];
								$arregloSPG["estmov"][0] = $arrcabecera['estmov'];
								$arregloSPG["codestpro"][0] = $ls_programa;
								$arregloSPG["spgcuenta"][0] = $ls_spgcta;
								$arregloSPG["documento"][0] = $ls_documento;
								$arregloSPG["desmov"][0]    = $ls_concepto;
								$arregloSPG["procede_doc"][0] = $ls_procede;
								$arregloSPG["monto"][0] = number_format($li_monto,2,".","");
								$arregloSPG["operacion"][0] = 'PG';
								$arregloSPG["estcla"][0] = $ls_estcla;
								$arregloSPG["codfuefin"][0] = $ls_codfuefin;
								
								$li_cont=0;
								$arregloSCG['codemp'][$li_cont]= $arrcabecera['codemp'];
								$arregloSCG['codban'][$li_cont]= $arrcabecera['codban'];
								$arregloSCG['ctaban'][$li_cont]= $arrcabecera['ctaban'];
								$arregloSCG['numdoc'][$li_cont]= $arrcabecera['numdoc'];
								$arregloSCG['codope'][$li_cont]= $arrcabecera['codope'];
								$arregloSCG['estmov'][$li_cont]= $arrcabecera['estmov'];
								$arregloSCG['scg_cuenta'][$li_cont]= $ls_spgcon;
								$arregloSCG['debhab'][$li_cont]= "D";
								$arregloSCG['codded'][$li_cont]= '00000';
								$arregloSCG['documento'][$li_cont]= $ls_documento;
								$arregloSCG['desmov'][$li_cont]= $ls_concepto;
								$arregloSCG['procede_doc'][$li_cont]= $ls_procede;
								$arregloSCG['monto'][$li_cont]= number_format($li_monto,2,".","");
								$arregloSCG['monobjret'][$li_cont]= number_format($li_monto,2,".","");
		
								if($li_islr>0)
								{
									$li_cont++;
									$arregloSCG['codemp'][$li_cont]= $arrcabecera['codemp'];
									$arregloSCG['codban'][$li_cont]= $arrcabecera['codban'];
									$arregloSCG['ctaban'][$li_cont]= $arrcabecera['ctaban'];
									$arregloSCG['numdoc'][$li_cont]= $arrcabecera['numdoc'];
									$arregloSCG['codope'][$li_cont]= $arrcabecera['codope'];
									$arregloSCG['estmov'][$li_cont]= $arrcabecera['estmov'];
									$arregloSCG['scg_cuenta'][$li_cont]= $ls_islrcon;
									$arregloSCG['debhab'][$li_cont]= "D";
									$arregloSCG['codded'][$li_cont]= '00000';
									$arregloSCG['documento'][$li_cont]= $ls_documento;
									$arregloSCG['desmov'][$li_cont]= $ls_concepto;
									$arregloSCG['procede_doc'][$li_cont]= $ls_procede;
									$arregloSCG['monto'][$li_cont]= number_format($li_islr,2,".","");
									$arregloSCG['monobjret'][$li_cont]= number_format($li_islr,2,".","");
								}
								
								$li_cont++;
								$arregloSCG['codemp'][$li_cont]= $arrcabecera['codemp'];
								$arregloSCG['codban'][$li_cont]= $arrcabecera['codban'];
								$arregloSCG['ctaban'][$li_cont]= $arrcabecera['ctaban'];
								$arregloSCG['numdoc'][$li_cont]= $arrcabecera['numdoc'];
								$arregloSCG['codope'][$li_cont]= $arrcabecera['codope'];
								$arregloSCG['estmov'][$li_cont]= $arrcabecera['estmov'];
								$arregloSCG['scg_cuenta'][$li_cont]= $ls_sccuenta;
								$arregloSCG['debhab'][$li_cont]= "H";
								$arregloSCG['codded'][$li_cont]= '00000';
								$arregloSCG['documento'][$li_cont]= $ls_documento;
								$arregloSCG['desmov'][$li_cont]= $ls_concepto;
								$arregloSCG['procede_doc'][$li_cont]= $ls_procede;
								$arregloSCG['monto'][$li_cont]= number_format($li_montohaber,2,".","");
								$arregloSCG['monobjret'][$li_cont]= 0;
								
							break;
						}	

						$arrEvento['codemp']    = $codemp;
						$arrEvento['codusu']    = $_SESSION['la_logusr'];
						$arrEvento['codsis']    = 'SCB';
						$arrEvento['evento']    = 'PROCESAR';
						$arrEvento['nomfisico'] = 'sigesp_scb_p_movbanco.php';
						
						$lb_existe=$io_movbanco->uf_select_movimiento($ls_documento,$ls_operacion,$ls_codban,$ls_ctaban,"");
						if(!$lb_existe)
						{
							$ls_valido = $servicioBancario->GuardarAutomatico($arrCabecera,$arregloSCG,$arregloSPG,$arregloSPI,$arrEvento);
						}
					}
					else
					{
						$io_msg->message("El Documento ".$ls_documento." No tiene configurado correctamente los parametros de Registro");
					}
					
				}
				if($ls_valido)
				{
								$io_msg->message("El Proceso se ha Generado correctamente");
				}
				else
				{
								$io_msg->message($servicioBancario->mensaje);

				}
			
			break;
		}


?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="807" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			
              <td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Caja y Banco </td>
			    <td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?PHP print date("j/n/Y")." - ".date("h:i a");?></b></span></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_buscar('<?php echo $_SESSION["la_empresa"]["estfilpremod"]?>');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" alt="Grabar" width="20" height="20" border="0" title="Procesar"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></div></td>
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
	$io_fun_scb->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_scb);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
  <table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
    <td width="760" height="136">
      <p>&nbsp;</p>
        <table width="741" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr> 
            <td colspan="4" class="titulo-ventana">Movimiento de Banco  por Lote </td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td colspan="2"><label>
              <input name="rbcomision" type="radio" value="0" checked>
              Sin Comision
            </label>
              <label>
              <input name="rbcomision" type="radio" value="1">
              Con Comision 
            </label></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="22%" height="22"><div align="right">Fte. Financiamiento</div></td>
            <td width="60" colspan="2"><input name="txtftefinanciamiento" type="text" id="txtftefinanciamiento" style="text-align:center" value="<?php print $ls_codfuefin;?>" size="3" maxlength="2" readonly>
              <a href="javascript: uf_cat_fte_financia();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Catalogo Fuente de Financiamiento" width="15" height="15" border="0"></a>
              <input name="txtdenftefinanciamiento" type="text" class="sin-borde" id="txtdenftefinanciamiento" value="<?php print $ls_denfuefin;?>" readonly></td>
            <td width="18%">&nbsp;</td>
          </tr>
          <tr>
            <td height="22"><div align="right"></div></td>
            <td colspan="2"><input name="arcimp" type="file" class="formato-blanco" id="arcimp" size="30"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22">&nbsp;</td>
            <td colspan="2"><input name="btnimportar" type="button" class="boton" id="btnimportar" value="Agregar" onClick="javascript:uploadAjax(this.form);"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22" colspan="4"><div align="center">
              <table width="400" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td id="detalles"></td>
                </tr>
              </table>
            </div></td>
          </tr>
          <?php            
          if($_SESSION["la_empresa"]["estfilpremod"]=='1'){ 
	  	  ?>
	  	<?php 
	  	}
	  	?>
          <?php            
          if($_SESSION["la_empresa"]["estrescxp"]=='1'){ 
	  	  ?>
	  	<?php 
	  	}
	  	?>
        </table>
        <table width="740" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="748"><input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrow; ?>">
            <input name="operacion" type="hidden" id="operacion">
            <input name="txtrifpro" type="hidden" id="txtrifpro">
            <input name="codigocuenta" type="hidden" id="codigocuenta">
            <input name="parametros" type="hidden" id="parametros" value="<?php print $ls_parametros; ?>"></td>
          </tr>
          <tr>
            <td><div id="solicitudes"></div></td>
          </tr>
        </table>        </td>
  </tr>
</table>
</form>   
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function cat_bancos()
{
	window.open("sigesp_cat_bancos.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
}
function catalogo_cuentabanco(conf_ch, operacion)
{
	f=document.form1;
	ls_codban=f.txtcodban.value;
	ls_nomban=f.txtdenban.value;
	if ((ls_codban!=""))
	{
		window.open("sigesp_cat_ctabanco.php?codigo="+ls_codban+"&hidnomban="+ls_nomban,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=720,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Debe seleccionar el Banco asociado a la cuenta");   
	}
}
function uf_cat_fte_financia()
{
	window.open("sigesp_sep_cat_fuente.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=516,height=400,resizable=yes,location=no");
} 

function uploadAjax(form){
	/* Creamos un objeto FormData que es un formulario con 	enctype=multipart/form-data 
	y le pasamos como parametro el formulario HTML */
 
	var Data = new FormData(form);
	
	/* Creamos el objeto que hara la petición AJAX al servidor, debemos de validar si existe el 	objeto " XMLHttpRequest" ya que en internet explorer viejito no esta, y si no esta usamos 
	"ActiveXObject" */
	
	if(window.XMLHttpRequest) {
		var Req = new XMLHttpRequest();
	}else if(window.ActiveXObject) {
		var Req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	//Pasándole la url a la que haremos la petición
	Req.open("POST", "class_folder/sigesp_scb_c_movbancolote_ajax.php", true);
	divgrid = document.getElementById('detalles');
	
	/* Le damos un evento al request, esto quiere decir que cuando
	termine de hacer la petición, se ejecutara este fragmento de
	código */
	
	Req.onload = function(Event) {
		//Validamos que el status http sea  ok
		if (Req.status == 200) {
			/*Como la info de respuesta vendrá en JSON 
			la parseamos */
					divgrid.innerHTML = Req.responseText;
		} else {
		    	console.log(Req.status); //Vemos que paso.
		}
	};	  
	
	//Enviamos la petición
	Req.send(Data);

}

function uf_detalles_movimientos(row)
{
	f=document.form1;
	islr=0;
	operacion=eval("f.txtoperacion"+row+".value");
	documento=eval("f.txtdocumento"+row+".value");
	fecha=eval("f.txtfecha"+row+".value");
	monto=eval("f.txtmonto"+row+".value");
	codban=eval("f.txtcodban"+row+".value");
	denban=eval("f.txtbanco"+row+".value");
	cuenta=eval("f.txtcuenta"+row+".value");
	sccuenta=eval("f.txtsccuenta"+row+".value");
	disponible=eval("f.txtdisponible"+row+".value");
	existe=eval("f.txtexiste"+row+".value");
	codoficom=eval("f.txtcodoficom"+row+".value");
	codforpag=eval("f.txtcodforpag"+row+".value");
	codfuefin=f.txtftefinanciamiento.value;
	denfuefin=f.txtdenftefinanciamiento.value;
	comision=f.rbcomision.value;
	if(comision=="1")
	{
		islr=eval("f.txtmonislr"+row+".value");
	}
	valido=true;
	if(codfuefin=="")
	{
		valido=false;
		alert("Debe indicar la Fuente de Financiamiento");	
	}
	if(documento=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el numero de Documento");	
	}
	if(codban=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido el Codido del Banco");	
	}
	if(cuenta=="")
	{
		valido=false;
		alert("El registro a importar no tiene definido la Cuenta del Banco");	
	}
	if(sccuenta=="")
	{
		valido=false;
		alert("La cuenta de Banco no tiene definida su cuenta Contable. Verifique que exista en el Sistema");	
	}
	if(disponible=="")
	{
		valido=false;
		alert("El registro a importar presenta problemas. Verifique que exista en el Sistema");	
	}
	if(valido)
	{
		eval("f.txtexiste"+row+".value='1'");
		window.open("sigesp_scb_p_detmovbancolote.php?codban="+codban+"&denban="+denban+"&cuenta="+cuenta
					+"&operacion="+operacion+"&documento="+documento+"&fecha="+fecha+"&monto="+monto+"&sccuenta="+sccuenta
					+"&disponible="+disponible+"&codfuefin="+codfuefin+"&denfuefin="+denfuefin+"&codoficom="+codoficom+"&codforpag="+codforpag+"&islr="+islr,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}
function ue_reload()
{
	f=document.form1;
	funcion="RELOAD";
	parametros=f.parametros.value;
	if(parametros!="")
	{
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById("detalles");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde están los métodos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_scb_c_movbancolote_ajax.php",true);
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
		ajax.send("funcion="+funcion+""+parametros);
	}
}

function ue_procesar()
{
	f=document.form1;
	f.operacion.value ="PROCESAR";
	f.action="sigesp_scb_p_movbancolote.php";
	f.submit();
}


function ue_actualizar_neto(row)
{
	f=document.form1;
	islr=0;
	rowant=eval(row-1);
	operacion=eval("f.txtoperacion"+row+".value");
	documento=eval("f.txtdocumento"+row+".value");
	comban=eval("f.txtmonto"+row+".value");
	codban=eval("f.txtcodban"+row+".value");
	cuenta=eval("f.txtcuenta"+row+".value");
	sccuenta=eval("f.txtsccuenta"+row+".value");
	mondep=eval("f.txtmonto"+rowant+".value");
	comision=f.rbcomision.value;
	if(comision=="1")
	{
		islr=eval("f.txtmonislr"+row+".value");
	}
	mondep=ue_formato_operaciones(mondep);
	comban=ue_formato_operaciones(comban);
	islr=ue_formato_operaciones(islr);
	monnet=eval(mondep-comban-islr);
	monnet=uf_convertir(monnet);
	eval("f.txtmonislr"+rowant+".value=monnet");
	if(islr=="")
	{
		eval("f.txtmonislr"+row+".value='0,00'");
	}
	if(comban=="")
	{
		eval("f.txtmonto"+row+".value='0,00'");
	}
}

//---------------------------------------------------------------------
//     Funcion que devuelve un monto con el formato
//	   debido para realizar operaciones matemeticas
//---------------------------------------------------------------------
function ue_formato_operaciones(valor)
{
	while (valor.indexOf('.')>0)
	{
		valor=valor.replace(".","");
	}
	valor=valor.replace(",",".");
	
	return valor;
	
}

</script> 
<?php
if($ls_parametros!="")
{
	print "<script language=JavaScript>";
	//print "   ue_reload();";
	print "</script>";
}
?>		  

</html>