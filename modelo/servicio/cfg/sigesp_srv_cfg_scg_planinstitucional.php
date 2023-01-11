<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrvcfg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrvcfg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvcfg."/modelo/servicio/cfg/sigesp_srv_cfg_scg_iplaninstitucional.php");

class ServicioPlanInstitucional implements ISCGPlanInstitucional
{
	private $daoPlanCuentaInstitucional;
	private $daoRegistroEvento;
	private $conexionBaseDatos;
	
	public function __construct()
	{
		$this->daoPlanCuentaInstitucional = null;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->formatoPlan=trim($_SESSION['la_empresa']['formplan']);
		$this->formatoCuenta=trim($_SESSION['la_empresa']['formcont']);		
		$this->mensaje= '';
		$this->valido= true;
	}
	
	public function existeCuentaPlanUnico($cuenta)
	{
		$existeCuenta = false;	
		$cadenaSQL = "SELECT sc_cuenta ".
					 "  FROM sigesp_plan_unico ".
					 " WHERE sc_cuenta='{$cuenta}'";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}
	
	public function buscarCuentaPlanUnico($cuenta)
	{
		$cadenaSQL = "SELECT sc_cuenta, denominacion ".
					 "  FROM sigesp_plan_unico ".
					 " WHERE sc_cuenta='{$cuenta}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarCuenta($cuenta)
	{
		$cadenaSQL = "SELECT sc_cuenta, status, denominacion ".
		   			 "	FROM scg_cuentas ". 
		   			 " WHERE codemp='".$this->codemp."' ".
		   			 "   AND trim(sc_cuenta)='".trim($cuenta)."'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarTodasCuenta()
	{
		$cadenaSQL = "SELECT sc_cuenta, denominacion ".
		   			 "  FROM scg_cuentas ". 
		   			 " WHERE codemp='".$this->codemp."'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function validarCuenta($cuenta)
	{
		$cuentaFormatoPlan = obtenerFormatoCuenta($this->formatoPlan, $cuenta);
		$longitudCuenta=strlen($cuentaFormatoPlan);
		$longitudFormato=strlen(str_replace('-','',$this->formatoPlan));
		if($longitudCuenta!=$longitudFormato)
		{
			$this->mensaje .= 'La cuenta '.$cuentaFormatoPlan.' no posee el formato del plan unico ';
			$this->valido= false;
		}
		if ($this->valido)
		{
			$longitudCuentaSCG=strlen($cuenta);
			$longitudFormato=strlen(str_replace('-','',$this->formatoCuenta));
	
			if($longitudCuentaSCG!=$longitudFormato)
			{
				$this->mensaje .= 'Formato de Contabilidad '.$this->formatoCuenta.' no corresponde al de la cuenta introducida '.$cuenta.'';
				$this->valido= false;
			}
		}
		if ($this->valido)
		{
			$nivel = obtenerNivelPlus($cuenta, $this->formatoCuenta);
			if($nivel<=1)
			{
				$this->mensaje .= "Las cuentas de nivel 1 no son validas";
				$this->valido= false;
			}
			// verifico si no hay cuentas con movimientos de nivel superior
			if(($nivel > 1)&&($this->valido))
			{
				$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				do
				{
					$dataCuenta = $this->buscarCuenta($cuentaSiguiente);
					if(!$dataCuenta->EOF)
					{
						if ($dataCuenta->fields['status'] == "C")
						{
							$this->mensaje .= 'Existen cuentas de nivel superior con Movimiento';			      
							$this->valido= false;
						}
				  	}
				  	unset($dataCuenta);
				  	$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
			   		$nivel = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
				}
				while(($nivel > 1) && ($this->valido));
			}
		}
	}
	
	public function validarRelacionesCuenta($cuenta)
	{
		$existeRelacion = '';
		$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','scg_cuentas');
		$this->daoPlanCuentaInstitucional->codemp = $this->codemp;
		$existeRelacion_a = $this->daoPlanCuentaInstitucional->validarRelacionesPlus('sc_cuenta', $cuenta, null, true);
		$existeRelacion_b = $this->daoPlanCuentaInstitucional->validarRelacionesPlus('scg_cuenta', $cuenta, null, true);
		if($existeRelacion_a===false && $existeRelacion_b===false)
		{
			$existeRelacion = false;
		}
		else
		{
			$arrModulosB = explode(',', $existeRelacion_b);
			$totMod = count((array)$arrModulosB);
			for ($i = 0; $i < $totMod; $i++)
			{
				$existeRelacion_a = str_replace($arrModulosB[$i], '', $existeRelacion_a);
			}
			$existeRelacion = $existeRelacion_a.', '.$existeRelacion_b;
			$existeRelacion = str_replace(', ,', ',', $existeRelacion);
		}
		return $existeRelacion;
	}
	
	public function validarCuentaHijas($cuenta)
	{
		$tieneHijas = false;
		$nivel         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
  		$cantDigitos   = obtenerDigitosNivel($nivel, $this->formatoCuenta);
  		$cuentaSinCero = substr($cuenta, 0,$cantDigitos);
  		
  		$cadenaSQL = "SELECT COUNT(sc_cuenta) AS ntotal ".
  					 "  FROM scg_cuentas ". 
  					 " WHERE codemp='{$this->codemp}' ".
					 "   AND  sc_cuenta like '{$cuentaSinCero}%'";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if (!$dataSet->EOF)
		{
			if ($dataSet->fields['ntotal'] > 1)
			{
				$tieneHijas = true;
			}
		}
		unset($dataSet);
		return $tieneHijas;
	}
	
	public function grabarCuenta($arrJsonCuenta,$operacion)
	{
		$mensajeGrabar = '1';
		DaoGenerico::iniciarTrans();
		$cuenta =  obtenerFormatoCuenta($this->formatoCuenta, $arrJsonCuenta->sc_cuenta);
		$this->validarCuenta($cuenta);
		if ($this->valido)
		{
			$dataCuenta = $this->buscarCuenta($cuenta);
			if(!$dataCuenta->EOF)
			{
				if ($operacion=='A')
				{
					$cadenaPk = "codemp='{$this->codemp}' AND sc_cuenta='{$cuenta}'";
					$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','scg_cuentas',array(),$cadenaPk);
					$this->daoPlanCuentaInstitucional->denominacion = $arrJsonCuenta->denominacion;
					$this->daoPlanCuentaInstitucional->cueproacu    = $arrJsonCuenta->cueproacu;
					if($this->daoPlanCuentaInstitucional->modificar()!=1)
					{
						$this->valido = false;
					}
					unset($dataCuenta);
				}
				else
				{
					$this->mensaje .= 'Error la cuenta ya existe no la puede incluir';
					$this->valido = false;
				}
			}
			else
			{
				if ($operacion=='I')
				{
					$numCuentas = 0;
					$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $cuenta;
					$arrCuentasIncluir [$numCuentas]['denominacion']  = $arrJsonCuenta->denominacion;
					$arrCuentasIncluir [$numCuentas]['sc_cuenta_ref'] = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
					$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
					$arrCuentasIncluir [$numCuentas]['estatus']       = 'C';
					$arrCuentasIncluir [$numCuentas]['cueproacu']     = $arrJsonCuenta->cueproacu;
					$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
					$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
					do
					{
						$dataCuenta = $this->buscarCuenta($cuentaSiguiente);
						if($dataCuenta->_numOfRows == 0)
						{
							$cuentaSiguientePlan = obtenerFormatoCuenta($this->formatoPlan, $cuentaSiguiente);
							$cuentaReferencia = "             ";
							$dataCuentaPlan = $this->buscarCuentaPlanUnico($cuentaSiguientePlan);
							if (!$dataCuentaPlan->EOF)
							{
								$denominacion = $dataCuentaPlan->fields['denominacion'];
							}
							else
							{
								$denominacion = $arrJsonCuenta->denominacion;
							}
							unset($dataCuentaPlan);
							
							if($nivelCuenta > 0)
							{
								$cuentaReferencia = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
								if (is_null($cuentaReferencia))
								{
									$cuentaReferencia = "             ";							
								}
							}
							$numCuentas++;
							$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $cuentaSiguiente;
							$arrCuentasIncluir [$numCuentas]['denominacion']  = $denominacion;
							$arrCuentasIncluir [$numCuentas]['sc_cuenta_ref'] = $cuentaReferencia;
							$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
							$arrCuentasIncluir [$numCuentas]['estatus']       = 'S';
							$arrCuentasIncluir [$numCuentas]['cueproacu']     = $arrJsonCuenta->cueproacu; 
						}
						unset($dataCuenta);
						if ($nivelCuenta > 0)
						{
							$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
							$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
								
						}
						else if($nivelCuenta == 0)
						{
							$nivelCuenta = -1 ;
						}
					}while( $nivelCuenta > 0);
					 
					for ($i = 0; ($i <= $numCuentas) && ($this->valido); $i++)
					{
						$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','scg_cuentas');
						$this->daoPlanCuentaInstitucional->codemp       = $this->codemp;
						$this->daoPlanCuentaInstitucional->sc_cuenta    = $arrCuentasIncluir [$i]['sc_cuenta']; 
						$this->daoPlanCuentaInstitucional->denominacion = $arrCuentasIncluir [$i]['denominacion'];
						$this->daoPlanCuentaInstitucional->status       = $arrCuentasIncluir [$i]['estatus'];
						$this->daoPlanCuentaInstitucional->nivel        = $arrCuentasIncluir [$i]['nivel'];
						$this->daoPlanCuentaInstitucional->referencia   = $arrCuentasIncluir [$i]['sc_cuenta_ref'];
						$this->daoPlanCuentaInstitucional->cueproacu    = $arrCuentasIncluir [$i]['cueproacu'];
						$this->daoPlanCuentaInstitucional->asignado     = 0; 
						$this->daoPlanCuentaInstitucional->distribuir   = 1;
						$this->daoPlanCuentaInstitucional->enero        = 0;
						$this->daoPlanCuentaInstitucional->febrero      = 0;
						$this->daoPlanCuentaInstitucional->marzo        = 0;
						$this->daoPlanCuentaInstitucional->abril        = 0;
						$this->daoPlanCuentaInstitucional->mayo         = 0;
						$this->daoPlanCuentaInstitucional->junio        = 0;
						$this->daoPlanCuentaInstitucional->julio        = 0;
						$this->daoPlanCuentaInstitucional->agosto       = 0;
						$this->daoPlanCuentaInstitucional->septiembre   = 0;
						$this->daoPlanCuentaInstitucional->octubre      = 0;
						$this->daoPlanCuentaInstitucional->noviembre    = 0;
						$this->daoPlanCuentaInstitucional->diciembre    = 0;
						if(!$this->daoPlanCuentaInstitucional->incluir())
						{
							$this->mensaje .= 'Error al incluir cuenta '.$this->daoPlanCuentaInstitucional->sc_cuenta.' en base de datos';
							$this->valido = false;
						}
						
						unset($this->daoPlanCuentaInstitucional);
					}
				}
				else
				{
					$this->mensaje .= 'Error la cuenta No existe no la puede actualizar';
					$this->valido = false;
				}
			}
		}
		DaoGenerico::completarTrans($this->valido);
		return $mensajeGrabar;
	}
	
	public function eliminarCuenta($cuenta)
	{
		$mensajeEliminar = '1';
		DaoGenerico::iniciarTrans();
		$relaciones = $this->validarRelacionesCuenta($cuenta);
		if($relaciones===false)
		{
			if ($this->validarCuentaHijas($cuenta))
			{
				$mensajeEliminar = 'La cuenta no puede ser eliminada, existen cuentas de nivel inferior.';
			}
			else
			{
				$cadenaPk = "codemp='{$this->codemp}' AND sc_cuenta='{$cuenta}'";
				$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','scg_cuentas',array(),$cadenaPk);
				$this->daoPlanCuentaInstitucional->denominacion = $arrJsonCuenta->denominacion;
				if(!$this->daoPlanCuentaInstitucional->eliminar())
				{
					return false;
				}
			}
		}
		else
		{
			$mensajeEliminar = 'La cuenta no puede ser eliminada, tiene movimientos en '.$relaciones;
		}
		if ($mensajeEliminar=='1')
		{
			if (!DaoGenerico::completarTrans())
			{
				$mensajeEliminar = 'Error al eliminar cuenta en base de datos';
			}
		}
		else
		{
			DaoGenerico::completarTrans(false);
		}
		return $mensajeEliminar;
	}
	
	public function buscarCuentaProvAcumResTec($cueproacu, $cuedepamo)
	{
		$cadenaSQL = "SELECT sc_cuenta AS codcueacum, denominacion AS dencueacum ".
					 "  FROM scg_cuentas ". 
					 " WHERE codemp= '{$this->codemp}' ".
					 " 	 AND status='C' ".
					 "   AND (sc_cuenta LIKE '{$cueproacu}%' OR sc_cuenta LIKE '{$cuedepamo}%')";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
}