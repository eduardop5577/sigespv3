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
	require_once ($dirctrscg."/modelo/servicio/spg/sigesp_srv_spg_comprobante.php");
	require_once ($dirctrscg.'/modelo/servicio/spg/sigesp_srv_spg_mod_comprobante.php');
	if ($_POST['ObjSon'])
	{
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$objetoJson = $json->decode($submit);

		switch ($objetoJson->operacion)
		{
			
			case "buscarNivel":
				$numniv = $_SESSION['la_empresa']['numniv']; 
				echo $numniv;
			break;
			
			case "buscarFuenteFinanciamiento":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarFuenteFinanciamiento($_SESSION['la_empresa']['codemp']));
				unset($servicioCmp);
			break;
			
			case "buscarCuentasPresupuestarias":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarCuentaPresupuestaria($_SESSION['la_empresa']['codemp'],$objetoJson->spg_cuenta,$objetoJson->denominacion));
				unset($servicioCmp);
			break;
			
			case "buscarMoneda":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarMoneda($objetoJson->codigo,$objetoJson->denominacion));
				unset($servicioCmp);
			break;
			
			case "buscarComprobantesPresupuestarios":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarComprobantes($_SESSION['la_empresa']['codemp'],$objetoJson->comprobante,$objetoJson->procede,$objetoJson->tipo,$objetoJson->provben,$objetoJson->fecdesde,$objetoJson->fechasta,$objetoJson->filtro,$objetoJson->numconcom));
				unset($servicioCmp);
			break;
	
			case "buscarModificacionesPresupuestarias":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarModificaciones($_SESSION['la_empresa']['codemp'],$objetoJson->comprobante,$objetoJson->procede,$objetoJson->fecdesde,$objetoJson->fechasta,$objetoJson->estapro));
				unset($servicioCmp);
			break;
			
			case "buscarDetallesContables":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->cargarDetalleContable($_SESSION['la_empresa']['codemp'],$objetoJson->procede,$objetoJson->comprobante,$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban));
				unset($servicioCmp);
			break;
			
			case "buscarDetallesPresupuestario":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->cargarDetallePresupuestario($_SESSION['la_empresa']['codemp'],$objetoJson->procede,$objetoJson->comprobante,$objetoJson->fecha,$objetoJson->codban,$objetoJson->ctaban));
				unset($servicioCmp);
			break;
			
			case "guardar":
                                $servicioCmp = new ServicioComprobantePresupuestarioGasto($objetoJson->prefijo);
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'PROCESAR';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = 'Guardo el comprobante presupuestario de gasto con el numero'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
				$valido = $servicioCmp->guardar($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				$respuesta  =  json_encode(array('raiz'=>$resultado));
				echo $respuesta;
				unset($servicioCmp);
				break;	
				
			case "eliminar":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				$arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
				$arrevento ['codusu']  = $_SESSION['la_logusr'];
				$arrevento ['codsis']  = $objetoJson->codsis;
				$arrevento ['evento']  = 'DELETE';
				$arrevento ['nomfisico']  = $objetoJson->nomven; 
				$arrevento ['desevetra'] = 'Elimino el comprobante presupuestario de gasto con el número'.$objetoJson->comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
				$valido = $servicioCmp->eliminarLocal($_SESSION['la_empresa']['codemp'],$objetoJson,$arrevento);
				$resultado['mensaje'] = $servicioCmp->mensaje;  
				$resultado['valido']  = $valido;    		
				$respuesta  =  json_encode(array('raiz'=>$resultado));
				echo $respuesta;
				unset($servicioCmp);
				break;
				
			case "cargar_nrodocumento":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo $servicioCmp->generarConsecutivo($_SESSION['la_empresa']['codemp'], $_SESSION['la_logusr'], $objetoJson->procede, $objetoJson->prefijo);
				unset($servicioCmp);
				break;

			case "buscarPrefijosUsuarios":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo generarJson($servicioCmp->buscarPrefijosUsuarios());
				unset($servicioCmp);
			break;
                            
			case "verificar_prefijo":
				$servicioCmp = new ServicioComprobantePresupuestarioGasto();
				echo $servicioCmp->verificarPrefijo($_SESSION['la_empresa']['codemp'],$objetoJson->procede);
				unset($servicioCmp);
				break;
			
		}   
	}
}