<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$_SESSION['session_activa']=time();
	$datosempresa=$_SESSION["la_empresa"];
	$dirctrscg = "";
	$dirctrscg = dirname(__FILE__);
	$dirctrscg = str_replace("\\","/",$dirctrscg); 
	$dirctrscg = str_replace("/controlador/spg","",$dirctrscg);
	require_once ($dirctrscg."/base/librerias/php/general/Json.php");
	require_once ($dirctrscg."/modelo/servicio/spg/sigesp_srv_spg_apertura.php");
	
	if ($_POST['ObjSon'])
	{
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$objetoJson = $json->decode($submit);
		$_SESSION['session_activa']=time();
		
		switch ($objetoJson->operacion)
		{
			case 'verificar_estatus':
				$resultado['estmodape'] = $_SESSION["la_empresa"]["estmodape"];
				$resultado['numniv'] = $_SESSION["la_empresa"]["numniv"];
				$resultado['estpreing'] = $_SESSION["la_empresa"]["estpreing"];
				echo json_encode(array('raiz'=>$resultado));
				break;
				
			case "buscarCuentasApertura":
				$servicio = new ServicioComprobanteApertura();
				echo generarJson($servicio->buscarCuentasApertura($_SESSION['la_empresa']['codemp'],$objetoJson->codestpro1,$objetoJson->codestpro2,$objetoJson->codestpro3,$objetoJson->codestpro4,$objetoJson->codestpro5,$objetoJson->estcla));
				unset($servicio);
				break;
			
			case "buscarFuentesFinanciamiento":
				$servicio = new ServicioComprobanteApertura();
				echo generarJson($servicio->buscarFuentesFinanciamiento($_SESSION['la_empresa']['codemp'],$objetoJson->codestpro1,$objetoJson->codestpro2,$objetoJson->codestpro3,$objetoJson->codestpro4,$objetoJson->codestpro5,$objetoJson->estcla,$objetoJson->spg_cuenta));
				unset($servicio);
				break;
			
			case "guardar":
				$servicioCmp = new ServicioComprobanteApertura();
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'PROCESAR';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = '';
				$valido = $servicioCmp->guardar($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				echo json_encode(array('raiz'=>$resultado));
				unset($servicioCmp);
				break;
	
			case "saldoCero":
				$servicio = new ServicioComprobanteApertura();
				$respuesta = $servicio->saldoCeroCuenta($_SESSION['la_empresa']['codemp'], $objetoJson);
				if ($respuesta === false)
				{
					$resultado['mensaje'] = "Error. ".$servicio->mensaje;
					$resultado['valido']  = false;
				}
				else
				{
					if ($respuesta == 'Y')
					{
						$resultado['mensaje'] = "El saldo de la cuenta fue reiniciado a cero.";
						$resultado['valido']  = true;
					}
				}
				echo json_encode(array('raiz'=>$resultado));
				unset($servicio);
				break;
				
			case "actDistribucion":
				$servicio = new ServicioComprobanteApertura();
				$estprog[0]=$objetoJson->codestpro1;
				$estprog[1]=$objetoJson->codestpro2;
				$estprog[2]=$objetoJson->codestpro3;
				$estprog[3]=$objetoJson->codestpro4;
				$estprog[4]=$objetoJson->codestpro5;
				$estprog[5]=$objetoJson->estcla;
				$respuesta = $servicio->actualizarDistribucion($_SESSION['la_empresa']['codemp'], $estprog, $objetoJson->cuenta, 
															   $objetoJson->m1, $objetoJson->m2, $objetoJson->m3, $objetoJson->m4, 
															   $objetoJson->m5, $objetoJson->m6, $objetoJson->m7, $objetoJson->m8, 
															   $objetoJson->m9, $objetoJson->m10, $objetoJson->m11, $objetoJson->m12, "2");
				if ($respuesta === false) {
					$resultado['mensaje'] = "Error. ".$servicio->mensaje;
					$resultado['valido']  = false;
				}
				else {
					if ($respuesta == 'Y') {
						$resultado['mensaje'] = "La distribucion de la cuenta fue actualizada.";
						$resultado['valido']  = true;
					}
				}
				echo json_encode(array('raiz'=>$resultado));
				unset($servicio);
				break;	
		}
	}   
}