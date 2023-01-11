<?php
/***********************************************************************************
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
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
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	
	$datosempresa=$_SESSION["la_empresa"];

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace("\\","",$_POST['ObjSon']);
		$json = new Services_JSON;	
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();

		switch ($ArJson->oper)
		{
			case 'nuevo' :
				$oservicio = new ServicioCfg ('soc_servicios');
				$oservicio->setCodemp ($datosempresa["codemp"]);
				echo $oservicio->buscarCodigoSocServicio();
				unset($oservicio);
				break;
				
			case 'incluir':
				$oservicio = new ServicioCfg ('soc_servicios');
				if((is_null($ArJson->datoscabecera[0]->preser))||(empty($ArJson->datoscabecera[0]->preser)))
				{
					$ArJson->datoscabecera[0]->preser=0;
				}
				echo $oservicio->guardarServicio($ArJson, $datosempresa["codemp"]);
				unset($oservicio);	
				$totaleliminar = count((array)$ArJson->cargoseliminar);
				for($x=0; $x<$totaleliminar; $x++)
				{
					$oservicio = new ServicioCfg ('soc_serviciocargo');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					if(!$oservicio->eliminarDto ($ArJson->cargoseliminar[$x]))
					{
						$mensaje="Error al eliminar el cargo ".$ArJson->cargoseliminar[$x]->codcar;	
						break;
					}
					unset($oservicio);
				}
				$totalguardar = count((array)$ArJson->esp_soc_serviciocargo);
				for($x=0; $x<$totalguardar; $x++)
				{
					$oservicio = new ServicioCfg ('soc_serviciocargo');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					if(!$oservicio->incluirDto ($ArJson->esp_soc_serviciocargo[$x]))
					{
						$mensaje="Error al incluir el cargo ".$ArJson->esp_soc_serviciocargo[$x]->codcar;	
						break;
					}
					unset($oservicio);
				}
				break;

			case 'actualizar':
				$oservicio = new ServicioCfg ('soc_servicios');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				ServicioCfg::iniTransaccion ();
				if((is_null($ArJson->datoscabecera[0]->preser))||(empty($ArJson->datoscabecera[0]->preser)))
				{
					$ArJson->datoscabecera[0]->preser=0;
				}
				$oservicio->modificarDto($ArJson->datoscabecera[0]);
				unset($oservicio);	
				$totaleliminar = count((array)$ArJson->cargoseliminar);
				for($x=0; $x<$totaleliminar; $x++)
				{
					$oservicio = new ServicioCfg ('soc_serviciocargo');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					if(!$oservicio->eliminarDto ($ArJson->cargoseliminar[$x]))
					{
						$mensaje="Error al eliminar el cargo ".$ArJson->cargoseliminar[$x]->codcar;	
						break;
					}
					unset($oservicio);
				}
				$totalguardar = count((array)$ArJson->esp_soc_serviciocargo);
				for($x=0; $x<$totalguardar; $x++)
				{
					$oservicio = new ServicioCfg ('soc_serviciocargo');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					if(!$oservicio->modificarDto($ArJson->esp_soc_serviciocargo[$x],true))
					{
						$mensaje="Error al incluir el cargo ".$ArJson->esp_soc_serviciocargo[$x]->codcar;	
						break;
					}
					unset($oservicio);
				}
				if (ServicioCfg::comTransaccion ())
				{
					$resultados='1|1';
				}
				else
				{
					$resultados='0|0';
				}
				print $resultados;		
				break;
			
			case 'catalogo':
				$oservicio = new ServicioCfg ('soc_servicios');
				$dataSocServicio = $oservicio->buscarServicios($datosempresa["codemp"]);					
				echo generarJson($dataSocServicio);
				unset($dataSocServicio);
				unset($oservicio);		
				break;
			
			case 'eliminar':
				$oservicio = new ServicioCfg ('soc_servicios');
				$ultimo=$oservicio->verificarUltimo('codser','soc_servicios'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ",$ArJson->datoscabecera[0]->codser);
				if ($ultimo)
				{
					echo $oservicio->eliminarServicio($ArJson, $datosempresa["codemp"]);
				}
				else
				{
					echo "-8";
					$mensaje="Error al eliminar en CFG un nuevo servicio".$ArJson->datoscabecera[0]->codser;
					$tipoevento=false;				
				}
				unset($oservicio);
				break;

			case 'detalles_cargos' :
				$oservicio = new ServicioCfg ('soc_servicios');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$cadenaSql="SELECT sigesp_cargos.codcar, sigesp_cargos.dencar, sigesp_cargos.porcar ".
						   "  FROM soc_serviciocargo ".
						   " INNER JOIN sigesp_cargos ".
						   "    ON soc_serviciocargo.codemp  = '".$datosempresa["codemp"]."'".
						   "   AND soc_serviciocargo.codser  = '".$ArJson->codser."'".
						   "   AND soc_serviciocargo.codemp  = sigesp_cargos.codemp".
						   "   AND soc_serviciocargo.codcar  = sigesp_cargos.codcar ";
				$resultado = $oservicio->buscarSql($cadenaSql);
				$ObjSon = generarJson($resultado);
				echo $ObjSon;
				break;
				
			case 'catserviciosep' :
				$oservicio->setCodemp ($datosempresa["codemp"]);
				$criterio='';
				if($datosempresa["estparsindis"]==1)
				{
					$criterio = "		,(SELECT (asignado-(comprometido+precomprometido)+aumento-disminucion) FROM spg_cuentas ".
								"		  WHERE  spg_cuentas.codestpro1 = '".$$ArJson->codestpro1."' ".
								"			AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."' ". 
								"			AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."' ". 
								"			AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."' ". 
								"			AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."' ". 
								"			AND spg_cuentas.estcla='".$ArJson->estcla."' ". 
								"			AND spg_cuentas.spg_cuenta = soc_servicios.spg_cuenta) AS disponibilidad "; 
					
				}
				$cadenasql ="SELECT soc_servicios.codser AS coditem, soc_servicios.denser AS denitem, soc_servicios.preser AS preitem, TRIM(spg_cuenta) as spg_cuenta, ".
							"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
							"		  WHERE spg_cuentas.codestpro1 = '".$ArJson->codestpro1."' ".
							"			AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."' ". 
							"			AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."' ".
							"			AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."' ". 
							"			AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."' ". 
							"			AND spg_cuentas.estcla = '".$ArJson->estcla."' ". 
							"			AND soc_servicios.codemp = spg_cuentas.codemp ". 
							"			AND soc_servicios.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta ".
							$criterio.
							"	FROM soc_servicios ".  
							"  WHERE soc_servicios.codemp='".$datosempresa["codemp"]."' ". 
							"	 AND soc_servicios.codser like '%".$ArJson->coditem."%' ". 
							"	 AND soc_servicios.denser like '%".$ArJson->denitem."%' ".  
							"  ORDER BY soc_servicios.codser ASC";

				$dataServicioSep  = $oservicio->buscarSql($cadenasql);
				$ObjSon = generarJson($dataServicioSep);
				echo $datosempresa["estparsindis"]."|".$ObjSon;
				unset($oservicio);
				unset($dataServicioSep); 
				break;	
		}
	}
}
?>