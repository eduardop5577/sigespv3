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
    $dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
    require_once ($dirsrv.'/base/librerias/php/general/Json.php');
    require_once ($dirsrv.'/modelo/servicio/spg/sigesp_srv_spg_sigeproden.php');

    $_SESSION['session_activa']=time();
    $submit = str_replace('\\', '', $_POST['ObjSon']);
    $json = new Services_JSON;
    $objetoJson = $json->decode($submit);
    $oservicio = new ServicioSIGEPRODEN();
    switch ($objetoJson->operacion)
    {
        case 'nuevo_proyecto' :
            $contador="";
            $contador = $oservicio->buscarCodigoProyecto();
            echo $json->encode($contador);
        break;
        
        case "catalogo_proyecto":
            echo generarJson($oservicio->buscarProyectos($objetoJson->codprosig,$objetoJson->despro));
        break;
	    
        case "incluir_proyecto":
            $arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
            $arrevento ['codusu']  = $_SESSION['la_logusr'];
            $arrevento ['codsis']  = $objetoJson->codsis;
            $arrevento ['evento']  = 'PROCESAR';
            $arrevento ['nomfisico']  = $objetoJson->nomven; 
            $arrevento ['desevetra'] = 'Guardo el proyecto SIGEPRODEN '.$objetoJson->codprosig.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
            $valido = $oservicio->guardarProyecto($objetoJson,$arrevento);
            $resultado['mensaje'] = $oservicio->mensaje;  
            $resultado['valido']  = $valido;    		
            $respuesta  =  json_encode(array('raiz'=>$resultado));
            echo $respuesta;                
        break;	

        case "actualizar_proyecto":
            $arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
            $arrevento ['codusu']  = $_SESSION['la_logusr'];
            $arrevento ['codsis']  = $objetoJson->codsis;
            $arrevento ['evento']  = 'PROCESAR';
            $arrevento ['nomfisico']  = $objetoJson->nomven; 
            $arrevento ['desevetra'] = 'Actualiz? el proyecto SIGEPRODEN '.$objetoJson->codprosig.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
            $valido = $oservicio->actualizarProyecto($objetoJson,$arrevento);
            $resultado['mensaje'] = $oservicio->mensaje;  
            $resultado['valido']  = $valido;    		
            $respuesta  =  json_encode(array('raiz'=>$resultado));
            echo $respuesta;                
        break;	
    
        case "eliminar_proyecto":
            $arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
            $arrevento ['codusu']  = $_SESSION['la_logusr'];
            $arrevento ['codsis']  = $objetoJson->codsis;
            $arrevento ['evento']  = 'PROCESAR';
            $arrevento ['nomfisico']  = $objetoJson->nomven; 
            $arrevento ['desevetra'] = 'Eliminar el proyecto SIGEPRODEN '.$objetoJson->codprosig.', asociado a la empresa '.$_SESSION['la_empresa']['codemp'];
            $valido = $oservicio->eliminarProyecto($objetoJson->codprosig,$arrevento);
            $resultado['mensaje'] = $oservicio->mensaje;  
            $resultado['valido']  = $valido;    		
            $respuesta  =  json_encode(array('raiz'=>$resultado));
            echo $respuesta;                
        break;	

        case 'obtener_tasacambio' :
            $tasa=1;
            $tasa = $oservicio->buscarTasaCambio($objetoJson->codmon,$objetoJson->fecha);
            echo $json->encode($tasa);
        break;
    
        case "generar_comprobante":
            $arrevento ['codemp']  = $_SESSION['la_empresa']['codemp'];
            $arrevento ['codusu']  = $_SESSION['la_logusr'];
            $arrevento ['codsis']  = 'SPG';
            $arrevento ['evento']  = 'PROCESAR';
            $arrevento ['nomfisico']  = 'sigesp_vis_spg_sigeproden_generar_comprobante.php'; 
            $arrevento ['desevetra'] = 'Genero el comprobante presupuestario de gasto con el numero'.$objetoJson->comprobante.', asociado al proyecto '.$objetoJson->codprosig.' y a la empresa '.$_SESSION['la_empresa']['codemp'];
            $valido = $oservicio->generarComprobante($objetoJson,$arrevento);
            $resultado['mensaje'] = $oservicio->mensaje;  
            $resultado['valido']  = $valido;    		
            $respuesta  =  json_encode(array('raiz'=>$resultado));
            echo $respuesta;
        break;	
    
        case "comprobantes_asociados":
            echo generarJson($oservicio->buscarComprobantesAsociados($objetoJson->codprosig));
        break;
    
    }
    unset($oservicio);
}
?>