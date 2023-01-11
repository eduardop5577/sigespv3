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
	require_once($dirsrv.'/base/librerias/php/general/sigesp_lib_relaciones.php');
	require_once($dirsrv.'/modelo/servicio/rpc/sigesp_srv_rpc_beneficiario.php');
	require_once($dirsrv.'/modelo/servicio/cfg/sigesp_srv_cfg_empresa.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once ('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();

	if ($_POST['ObjSon']) 		
	{
		$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
		$json = new Services_JSON();
		$ArJson = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		$oservicio = new ServicioCfg('sigesp_empresa');
		
		switch ($ArJson->oper)
		{
			case 'incluir' :
				$arrValoresDefecto['codemp'] = $ArJson->codemp;
				$arrValoresDefecto['ciesem1'] = '0';
				$arrValoresDefecto['ciesem2'] = '0';
				$oservicio->setValoresDefectoEmpresa($arrValoresDefecto);
				ServicioCfg::iniTransaccion();
				$ArJson->estvaldisfin = 'N';
				$ArJson->dedconproben = '0';
				$ArJson->periodo   = convertirFechaBd($ArJson->periodo);
				$ArJson->salinipro = formatoNumericoBd($ArJson->salinipro ,1);
				$ArJson->salinieje = formatoNumericoBd($ArJson->salinieje ,1);
				$ArJson->tiesesact = formatoNumericoBd($ArJson->tiesesact ,1);
				$resultado = $oservicio->incluirDto ($ArJson);
				$mensaje = "Inserto la Empresa con codigo ".$ArJson->codemp;
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					echo "|1";
				} 
				else
				{
					echo "|0";
					$mensaje = "Error al insertar la Empresa con codigo ".$ArJson->codemp;
					$tipoevento=false;
				}
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_empresa.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
			
			case 'catalogo' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$dataEmpresa = $oservicio->buscarTodos("codemp",0);
				echo generarJson($dataEmpresa);
				unset($dataEmpresa);
				break;
			
			case 'actualizar' :
				$oservicio->setCodemp ($ArJson->codemp);
				$ArJson->periodo   = convertirFechaBd($ArJson->periodo);
				$ArJson->salinipro=formatoNumericoBd($ArJson->salinipro ,1);
				$ArJson->salinieje=formatoNumericoBd($ArJson->salinieje ,1);
				$ArJson->tiesesact = formatoNumericoBd($ArJson->tiesesact ,1);
				ServicioCfg::iniTransaccion ();
				$oservicio->getDto("codemp='".$_SESSION["la_empresa"]["codemp"]."'");
				$resultado = $oservicio->modificarDto ($ArJson);
				$mensaje = 'Se actualizo la Empresa ' .$ArJson->codemp;
				$tipoevento=true;
				if (ServicioCfg::comTransaccion ())
				{
					if($ArJson->codemp == $_SESSION["la_empresa"]["codemp"])
					{
						$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
						$empresa = $oservicio->obtenerEmpresa($_SESSION["la_empresa"]["codemp"]);
						if(!$empresa->EOF)
						{
								$_SESSION["la_empresa"]= $empresa->FetchRow();
						}
					}
					echo "|1";
				} 
				else
				{
					echo "|0";
					$mensaje = 'Error al actualizar la Empresa ' .$ArJson->codemp;			
					$tipoevento=false;	
				}
				unset($oservicio);
				$servicioEvento->evento="MODIFICAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_empresa.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$resultado;
				$servicioEvento->incluirEvento();
				break;
			
			case 'eliminar' :
				$oservicio->setCodemp ($ArJson->codemp);
				$chkrel       = new servicioRelaciones();
				$condicion = " AND (column_name='codemp')";  //Nombre del o los campos que deseamos buscar.
				$mensaje   = "";                           
				$tiene     = $chkrel->verificarRelaciones($condicion,'sigesp_empresa',$ArJson->codemp,$mensaje);//Verifica los movimientos asociados a la empresa
				$tipoevento=false;
				$mensaje='No se puede eliminar la Empresa '.$ArJson->codemp.', posee otras asociaciones en el sistema, verifique que no se haya utilizado anteriormente';
				if(!$tiene)
				{
					ServicioCfg::iniTransaccion ();
					$oservicio->eliminarDto ( $ArJson );
					if (ServicioCfg::comTransaccion ())
					{
						echo "|1";
						$tipoevento=true;
						$mensaje='Elimino la Empresa ' .$ArJson->codemp;
					}
					else
					{
						echo "|0";
					}
				}
				else
				{
					$arreglo = array ("mensaje" =>array());
					$respuesta = $json->encode ( $arreglo );
					echo $respuesta;
				}
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_empresa.php";
				$servicioEvento->desevetra=$mensaje;
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				break;
				
			case 'claveprimaria' :
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$arregloClave = $oservicio->obtenerPrimaryKey();
				echo $json->encode($arregloClave);
				break;
				
			case 'nuevo' :
				$contador="";
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$contador = $oservicio->buscarCodigoEmpresa();
				echo $json->encode($contador);
				break;
			
			case 'validariva':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultado = $oservicio->validarExistenciaIvaConfigurado($ArJson->codemp);
				echo $json->encode($resultado->FetchRow());
				break;
				
			case 'validarformatospi':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultado = $oservicio->validarExistenciaCuentasIngreso($ArJson->codemp);
				echo $json->encode($resultado->FetchRow());
				break;
				
			case 'validarformatospg':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultado = $oservicio->validarExistenciaCuentasGasto($ArJson->codemp);
				echo  $json->encode($resultado->FetchRow());
				break;
				
			case 'validarformatoscg':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultado = $oservicio->validarExistenciaCuentasContables($ArJson->codemp);
				echo $json->encode($resultado->FetchRow());
				break;
				
			case 'validarestructuras':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultado = $oservicio->validarExistenciaEstructuras($ArJson->codemp);
				$niveles = $oservicio->bucarCantNivelPresu($ArJson->codemp);
				echo $niveles.'|'.$json->encode($resultado->FetchRow());
				break;
				
			case 'verificarapertura':
				$oservicio->setCodemp ($ArJson->codemp);
				$resultadospg = $oservicio->verificarExistenciaAperturaSPG($ArJson->codemp);
				$resultadospi = $oservicio->verificarExistenciaAperturaSPI($ArJson->codemp);
				echo $json->encode(array("totalapertura"=>($resultadospg->fields['aperturaspg']+$resultadospi->fields['aperturaspi'])));
				break;
				
			case 'catbeneficiario':
				$objServicioBeneficiario = new ServicioBeneficiario();
				$dataBeneficiario = $objServicioBeneficiario->buscarBeneficiariosCatEmpresa($ArJson->cedben_c, $ArJson->nomben_c, $ArJson->apeben_c);
				echo generarJson($dataBeneficiario);
				unset($dataBeneficiario);
				unset($objServicioBeneficiario);
				break;
				
			case 'catcuentaspg':
				$dataCuentaSPG = $oservicio->buscarCuentasSpg($_SESSION["la_empresa"]["codemp"],$_SESSION["la_empresa"]["gasto_p"],"");
				echo generarJson($dataCuentaSPG);
				unset($dataCuentaSPG);
				break;
				
			case 'actcencos':
				$servicioEmpresa = new Empresa();
				if($servicioEmpresa->actualizarCentroCostos($ArJson))
				{
					echo '1';
				}
				else
				{
					echo '0';
				}
				unset($servicioEmpresa);
				break;
		}
		unset($oservicio);
		unset($oregevent);
	}
}

?>