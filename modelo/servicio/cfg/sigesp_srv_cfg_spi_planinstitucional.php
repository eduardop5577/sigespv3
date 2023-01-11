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

class ServicioPlanInstitucional 
{
	public $mensaje;
	public $valido;
	private $conexionBaseDatos;
	
	public function __construct()
	{	
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->mensaje= '';
		$this->valido= true;
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->ingreso_p = $_SESSION['la_empresa']['ingreso_p'];
		$this->formatoPlan=trim($_SESSION['la_empresa']['formplan']);
		$this->formatoCuenta=trim($_SESSION['la_empresa']['formspi']);
	}

	public function buscarCuenta($cuenta)
	{
		$cadenaSQL = "SELECT spi_cuenta, status, denominacion, sc_cuenta ".
		   			 "	FROM spi_cuentas ". 
		   			 " WHERE codemp= '".$this->codemp."' ".
		   			 "   AND trim(spi_cuenta)='".trim($cuenta)."'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarCuentaPlanUnicoRE($cuenta)
	{
		$cadenaSQL = "SELECT sig_cuenta, denominacion ".
					 "  FROM sigesp_plan_unico_re ".
					 " WHERE sig_cuenta='{$cuenta}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
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

	public function existeCuentaPlanUnicoRE($cuenta)
	{
		$existeCuenta = false;	
		$cadenaSQL = "SELECT sig_cuenta ".
					 "  FROM sigesp_plan_unico_re ".
					 " WHERE sig_cuenta='{$cuenta}'";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}

	public function existeCuenta($cuenta)
	{
		$existeCuenta = false;	
		$cadenaSQL = "SELECT spi_cuenta ".
					 "  FROM spi_cuentas ".
					 " WHERE codemp = '".$this->codemp."' ".
					 "   AND spi_cuenta='{$cuenta}' ".
					 "   AND status='C'";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}

	public function existeCuentaEstructura($cuenta,$estructura,$conEstructura)
	{
		$existeCuenta = false;	
		$criterio='';
		if(($_SESSION['la_empresa']['estpreing']==1)AND($conEstructura))
		{		
			$criterio .= " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
			$criterio .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
			$criterio .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
			$criterio .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
			$criterio .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
			$criterio .= " AND estcla = '".$estructura->estcla."'";
		}
		
		$cadenaSQL = "SELECT spi_cuenta ".
					 "  FROM spi_cuentas_estructuras ".
					 " WHERE codemp = '".$this->codemp."' ".
					 "   AND spi_cuenta='{$cuenta}' ".
				     $criterio;
		
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}
	
	public function buscarCuentaEstructura($cuenta,$estructura)
	{
		$criterio='';
		if($_SESSION['la_empresa']['estpreing']==1)
		{		
			$criterio .= " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
			$criterio .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
			$criterio .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
			$criterio .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
			$criterio .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
			$criterio .= " AND estcla = '".$estructura->estcla."'";
		}
		
		$cadenaSQL = "SELECT spi_cuenta ".
					 "  FROM spi_cuentas_estructuras ".
					 " WHERE codemp = '".$this->codemp."' ".
					 "   AND spi_cuenta='{$cuenta}' ".
				     $criterio;
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function validarCuenta($cuenta, $cuentascg)
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
			$longitudCuentaSPI=strlen($cuenta);
			$longitudFormato=strlen(str_replace('-','',$this->formatoCuenta));
	
			if($longitudCuentaSPI!=$longitudFormato)
			{
				$this->mensaje .= 'Formato de presupuesto '.$this->formatoCuenta.' no corresponde al de la cuenta introducida '.$cuenta.'';
				$this->valido= false;
			}
		}
		if ($this->valido)
		{
			if($longitudCuenta<$longitudCuentaSPI)
			{
				$NextCuenta = $cuentaFormatoPlan;
			}
			else
			{
				$NextCuenta = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
			}
			$existe=$this->existeCuentaPlanUnico($NextCuenta);
			if(!$existe)
			{
				$existe=$this->existeCuentaPlanUnicoRE($cuenta);
			}
			if(substr($cuenta,0,1)!=trim($this->ingreso_p))
			{
				$this->mensaje .= 'Las cuenta '.$cuenta.' debe comenzar con '.$this->ingreso_p.' ';
				$this->valido= false;
			}
			$nivel = obtenerNivelPlus($cuenta, $this->formatoCuenta);
			if($nivel<=1)
			{
				$this->mensaje .= 'La cuenta '.$cuenta.' de Nivel Partida no es valida.';
				$this->valido= false;
			}
			if($nivel<= 2)
			{
				$this->mensaje .= 'La cuenta '.$cuenta.' de Nivel Generica no es valida.';
				$this->valido= false;
			}
			$nivel = obtenerNivelPlus($cuenta, $this->formatoCuenta);
			if($nivel > 1)
			{
				$NextCuenta = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				do
				{
					$existe = $this->existeCuenta($NextCuenta);
					if($existe)
					{
						$this->mensaje .= 'Existen cuentas de nivel superior con movimiento para la cuenta '.$cuenta.'.';
						$this->valido= false;
					}
					else
					{
						$NextCuenta = obtenerCuentaSiguientePlus($NextCuenta, $this->formatoCuenta);
						$nivel = obtenerNivelPlus($NextCuenta, $this->formatoCuenta);
					}
				}while(($nivel > 1) && ($this->valido));
			}
		}
	} 

	public function grabarCuenta($arrJsonCuenta,$estructura)
	{
		DaoGenerico::iniciarTrans();
		$cuenta =  obtenerFormatoCuenta($this->formatoCuenta, $arrJsonCuenta->spi_cuenta);
		$this->validarCuenta($cuenta, $arrJsonCuenta->sc_cuenta);
		if ($this->valido)
		{
			$dataCuenta = $this->buscarCuenta($cuenta);
			if(!$dataCuenta->EOF)
			{
				if ($dataCuenta->fields['status'] == 'C')
				{
					$cadenaPk = "codemp='{$this->codemp}' AND spi_cuenta='{$cuenta}'";
					$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spi_cuentas',array(),$cadenaPk);
					$this->daoPlanCuentaInstitucional->denominacion = $arrJsonCuenta->denominacion;
					$this->daoPlanCuentaInstitucional->sc_cuenta    = $arrJsonCuenta->sc_cuenta;
					$this->daoPlanCuentaInstitucional->cueclaeco    = $arrJsonCuenta->cueclaeco;
					if($this->daoPlanCuentaInstitucional->modificar()!=1)
					{
						$this->mensaje .= 'Error al actualizar la cuenta';
						$this->valido=false;
					}
					else
					{
						if (!$this->existeCuentaEstructura($cuenta,$estructura,true))
						{
							$numCuentas = 0;
							$arrCuentasIncluir [$numCuentas]['spi_cuenta']     = $cuenta;
							$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $arrJsonCuenta->sc_cuenta;
							$arrCuentasIncluir [$numCuentas]['cueclaeco']    = $arrJsonCuenta->cueclaeco;
							$arrCuentasIncluir [$numCuentas]['denominacion']  = $arrJsonCuenta->denominacion;
							$arrCuentasIncluir [$numCuentas]['referencia'] = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
							$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
							$arrCuentasIncluir [$numCuentas]['estatus']       = 'C';
							$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
							$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
							do
							{
								$dataCuenta = $this->buscarCuentaEstructura($cuentaSiguiente,$estructura);
								$cuentaReferencia = "             ";
								if($dataCuenta->EOF)
								{
									$cuentaSiguientePlan = obtenerFormatoCuenta($this->formatoPlan, $cuentaSiguiente);
									$dataCuentaPlan = $this->buscarCuentaPlanUnicoRE($cuentaSiguientePlan);
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
									$arrCuentasIncluir [$numCuentas]['spi_cuenta']     = $cuentaSiguiente;
									$arrCuentasIncluir [$numCuentas]['denominacion']  = $denominacion;
									$arrCuentasIncluir [$numCuentas]['referencia'] = $cuentaReferencia;
									$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
									$arrCuentasIncluir [$numCuentas]['estatus']       = 'S';
									$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $arrJsonCuenta->sc_cuenta; 
									$arrCuentasIncluir [$numCuentas]['cueclaeco']    = $arrJsonCuenta->cueclaeco;
								}
								if ($nivelCuenta > 0)
								{
									$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
									$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);

								}
								else if($nivelCuenta == 0)
								{
									$nivelCuenta = -1 ;
								}
							}while( $nivelCuenta >0);
							for ($i = 0; ($i <= $numCuentas)&&($this->valido) ; $i++)
							{
								$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','spi_cuentas');
								$this->daoPlanCuentaInstitucional->codemp       = $this->codemp;
								$this->daoPlanCuentaInstitucional->spi_cuenta    = $arrCuentasIncluir [$i]['spi_cuenta'];
								$this->daoPlanCuentaInstitucional->sc_cuenta    = $arrCuentasIncluir [$i]['sc_cuenta']; 
								$this->daoPlanCuentaInstitucional->cueclaeco    = $arrCuentasIncluir [$i]['cueclaeco']; 
								$this->daoPlanCuentaInstitucional->denominacion = $arrCuentasIncluir [$i]['denominacion'];
								$this->daoPlanCuentaInstitucional->status       = $arrCuentasIncluir [$i]['estatus'];
								$this->daoPlanCuentaInstitucional->nivel        = $arrCuentasIncluir [$i]['nivel'];
								$this->daoPlanCuentaInstitucional->referencia   = $arrCuentasIncluir [$i]['referencia'];
								$this->daoPlanCuentaInstitucional->distribuir   = 1;
								$this->daoPlanCuentaInstitucional->previsto     = 0; 
								$this->daoPlanCuentaInstitucional->devengado     = 0; 
								$this->daoPlanCuentaInstitucional->cobrado     = 0; 
								$this->daoPlanCuentaInstitucional->cobrado_anticipado     = 0; 
								$this->daoPlanCuentaInstitucional->aumento     = 0; 
								$this->daoPlanCuentaInstitucional->disminucion     = 0; 
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
								
								$this->cuentaEstructura($this->daoPlanCuentaInstitucional->spi_cuenta,$estructura);
								unset($this->daoPlanCuentaInstitucional);
							}
							unset($arrCuentasIncluir);
						}
					}
				}
				else
				{
					$this->mensaje .= 'La cuenta '.$arrJsonCuenta->spi_cuenta.' que intenta registrar ya existe';
					$this->valido=false;
				}
				unset($dataCuenta);
			}
			else
			{
				$numCuentas = 0;
				$arrCuentasIncluir [$numCuentas]['spi_cuenta']     = $cuenta;
				$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $arrJsonCuenta->sc_cuenta;
				$arrCuentasIncluir [$numCuentas]['cueclaeco']    = $arrJsonCuenta->cueclaeco;
				$arrCuentasIncluir [$numCuentas]['denominacion']  = $arrJsonCuenta->denominacion;
				$arrCuentasIncluir [$numCuentas]['referencia'] = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
				$arrCuentasIncluir [$numCuentas]['estatus']       = 'C';
				$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
				do
				{
					$dataCuenta = $this->buscarCuenta($cuentaSiguiente);
					$cuentaReferencia = "             ";
					if($dataCuenta->EOF)
					{
						$cuentaSiguientePlan = obtenerFormatoCuenta($this->formatoPlan, $cuentaSiguiente);
						$dataCuentaPlan = $this->buscarCuentaPlanUnicoRE($cuentaSiguientePlan);
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
						$arrCuentasIncluir [$numCuentas]['spi_cuenta']     = $cuentaSiguiente;
						$arrCuentasIncluir [$numCuentas]['denominacion']  = $denominacion;
						$arrCuentasIncluir [$numCuentas]['referencia'] = $cuentaReferencia;
						$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
						$arrCuentasIncluir [$numCuentas]['estatus']       = 'S';
						$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $arrJsonCuenta->sc_cuenta; 
                				$arrCuentasIncluir [$numCuentas]['cueclaeco']    = $arrJsonCuenta->cueclaeco;
					}
					if ($nivelCuenta > 0)
					{
						$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
						$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
							
					}
					else if($nivelCuenta == 0)
					{
						$nivelCuenta = -1 ;
					}
				}while( $nivelCuenta >0);
				for ($i = 0; ($i <= $numCuentas)&&($this->valido) ; $i++)
				{
					$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','spi_cuentas');
					$this->daoPlanCuentaInstitucional->codemp       = $this->codemp;
					$this->daoPlanCuentaInstitucional->spi_cuenta    = $arrCuentasIncluir [$i]['spi_cuenta'];
					$this->daoPlanCuentaInstitucional->sc_cuenta    = $arrCuentasIncluir [$i]['sc_cuenta']; 
					$this->daoPlanCuentaInstitucional->cueclaeco    = $arrCuentasIncluir [$i]['cueclaeco']; 
					$this->daoPlanCuentaInstitucional->denominacion = $arrCuentasIncluir [$i]['denominacion'];
					$this->daoPlanCuentaInstitucional->status       = $arrCuentasIncluir [$i]['estatus'];
					$this->daoPlanCuentaInstitucional->nivel        = $arrCuentasIncluir [$i]['nivel'];
					$this->daoPlanCuentaInstitucional->referencia   = $arrCuentasIncluir [$i]['referencia'];
					$this->daoPlanCuentaInstitucional->distribuir   = 1;
					$this->daoPlanCuentaInstitucional->previsto     = 0; 
					$this->daoPlanCuentaInstitucional->devengado     = 0; 
					$this->daoPlanCuentaInstitucional->cobrado     = 0; 
					$this->daoPlanCuentaInstitucional->cobrado_anticipado     = 0; 
					$this->daoPlanCuentaInstitucional->aumento     = 0; 
					$this->daoPlanCuentaInstitucional->disminucion     = 0; 
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
						$this->mensaje .= 'Error al incluir cuenta en base de datos';
						$this->valido = false;
					}
					else
					{
						$this->cuentaEstructura($this->daoPlanCuentaInstitucional->spi_cuenta,$estructura);
					}
					unset($this->daoPlanCuentaInstitucional);
				}
				unset($arrCuentasIncluir);
			}
		}
		DaoGenerico::completarTrans($this->valido);
	}

	public function validarRelacionesCuenta($cuenta)
	{
		$existeRelacion = '';
		$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','spi_cuentas');
		$this->daoPlanCuentaInstitucional->codemp = $this->codemp;
		$arrtabla[0] = 'spi_cuentas_estructuras';
		$existeRelacion = $this->daoPlanCuentaInstitucional->validarRelacionesPlus('spi_cuenta', $cuenta, $arrtabla, true, '');
		if($existeRelacion===false)
		{
			$existeRelacion = false;
		}
		else
		{
			$arrModulos = explode(',', $existeRelacion);
			$totMod = count((array)$arrModulos);
			$Relacion = '';
			for ($i = 0; $i < $totMod; $i++)
			{
				$Relacion = str_replace($arrModulos[$i], '', $Relacion);
				$Relacion .= ', '.$arrModulos[$i];
			}
			$existeRelacion = str_replace(', ,', ',', $Relacion);
		}
		return $existeRelacion;
	}
	
	public function validarRelacionesCuentaEstructura($cuenta,$estructura)
	{
		$criterio = " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
		$criterio .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
		$criterio .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
		$criterio .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
		$criterio .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
		$criterio .= " AND estcla = '".$estructura->estcla."'";
		
		$existeRelacion = '';
		$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','spi_cuentas_estructuras');
		$this->daoPlanCuentaInstitucional->codemp = $this->codemp;
		$arrtabla[0] = 'cxp_dc_spi';
		$arrtabla[1] = 'scb_movcol_spi';
		$arrtabla[2] = 'sigesp_cargos';
		$arrtabla[3] = 'spi_cuentas';
		$arrtabla[4] = 'spi_plantillacuentareporte'; 
		$existeRelacion = $this->daoPlanCuentaInstitucional->validarRelacionesPlus('spi_cuenta', $cuenta, $arrtabla, true, '', $criterio);
		if($existeRelacion===false)
		{
			$existeRelacion = false;
		}
		else
		{
			$arrModulos = explode(',', $existeRelacion);
			$totMod = count((array)$arrModulos);
			$Relacion = '';
			for ($i = 0; $i < $totMod; $i++)
			{
				$Relacion = str_replace($arrModulos[$i], '', $Relacion);
				$Relacion .= ', '.$arrModulos[$i];
			}
			$existeRelacion = str_replace(', ,', ',', $Relacion);
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
  					 "  FROM spi_cuentas ". 
  					 " WHERE codemp='{$this->codemp}' ".
					 "   AND spi_cuenta like '{$cuentaSinCero}%'";
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
	
	public function validarCuentaHijasEstructura($cuenta,$estructura)
	{
		$tieneHijas = false;
		$nivel         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
  		$cantDigitos   = obtenerDigitosNivel($nivel, $this->formatoCuenta);
  		$cuentaSinCero = substr($cuenta, 0,$cantDigitos);
  		
		$criterio='';
		if($_SESSION['la_empresa']['estpreing']==1)
		{		
			$criterio .= " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
			$criterio .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
			$criterio .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
			$criterio .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
			$criterio .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
			$criterio .= " AND estcla = '".$estructura->estcla."'";
		}
		
  		$cadenaSQL = "SELECT COUNT(spi_cuenta) AS ntotal ".
  					 "  FROM spi_cuentas_estructuras ". 
  					 " WHERE codemp='{$this->codemp}' ".
					 "   AND spi_cuenta like '{$cuentaSinCero}%'".
				     $criterio;
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
	
	public function eliminarCuenta($cuenta,$estructura)
	{
		DaoGenerico::iniciarTrans();
		if($_SESSION['la_empresa']['estpreing']==0)
		{
			$estructura->codest0 = '-------------------------'; 
			$estructura->codest1 = '-------------------------';
			$estructura->codest2 = '-------------------------';
			$estructura->codest3 = '-------------------------';
			$estructura->codest4 = '-------------------------';
			$estructura->estcla   = '-';
		}
		
		$existe = $this->existeCuentaEstructura($cuenta,$estructura,true);
		if($existe)
		{
			$relaciones = $this->validarRelacionesCuentaEstructura($cuenta,$estructura);
			if($relaciones===false)
			{
				if ($this->validarCuentaHijasEstructura($cuenta,$estructura))
				{
					$this->mensaje = 'La cuenta no puede ser eliminada, existen cuentas de nivel inferior.';
					$this->valido = false;
				}
				else
				{
					$cadenaPk = "codemp='{$this->codemp}' AND spi_cuenta='{$cuenta}'";
					$cadenaPk .= " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
					$cadenaPk .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
					$cadenaPk .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
					$cadenaPk .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
					$cadenaPk .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
					$cadenaPk .= " AND estcla = '".$estructura->estcla."'";

					$this->daoCuentaEstructura = FabricaDao::CrearDAO('C','spi_cuentas_estructuras',array(),$cadenaPk);
					if(!$this->daoCuentaEstructura->eliminar())
					{
						$this->mensaje = 'Error al eliminar la cuenta estructura '.$cuenta;
						$this->valido = false;
					}
					else
					{
						$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
						$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
						do
						{
							$existe = $this->existeCuenta($cuentaSiguiente);
							if($existe)
							{
								if (!$this->validarCuentaHijasEstructura($cuentaSiguiente,$estructura))
								{
									$cadenaPk = "codemp='{$this->codemp}' AND spi_cuenta='{$cuentaSiguiente}'";
									$cadenaPk .= " AND codestpro1 = '".str_pad($estructura->codest0,25,0,0)."'"; 
									$cadenaPk .= " AND codestpro2 = '".str_pad($estructura->codest1,25,0,0)."'"; 
									$cadenaPk .= " AND codestpro3 = '".str_pad($estructura->codest2,25,0,0)."'"; 
									$cadenaPk .= " AND codestpro4 = '".str_pad($estructura->codest3,25,0,0)."'"; 
									$cadenaPk .= " AND codestpro5 = '".str_pad($estructura->codest4,25,0,0)."'"; 
									$cadenaPk .= " AND estcla = '".$estructura->estcla."'";

									$this->daoCuentaEstructura = FabricaDao::CrearDAO('C','spi_cuentas_estructuras',array(),$cadenaPk);
									if(!$this->daoCuentaEstructura->eliminar())
									{
										$this->mensaje = 'Error al eliminar la cuenta estructura '.$cuenta;
										$this->valido = false;
									}
								}
							}
							if ($nivelCuenta > 0)
							{
								$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
								$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
							}
							else if($nivelCuenta == 0)
							{
								$nivelCuenta = -1 ;
							}
						}while($nivelCuenta > 0);
					}

				}
			}
			else
			{
				$this->mensaje = 'La cuenta no puede ser eliminada, tiene movimientos en '.$relaciones;
				$this->valido = false;
			}
		}
		else
		{
			$this->mensaje = 'La cuenta '.$cuenta.' no puede ser eliminada, no existe';
			$this->valido = false;
		}
		
		$existe = $this->existeCuentaEstructura($cuenta,$estructura,false);
		if(!$existe)
		{
			$existe = $this->existeCuenta($cuenta);
			if($existe)
			{			
				$relaciones = $this->validarRelacionesCuenta($cuenta);
				if($relaciones===false)
				{
					if ($this->validarCuentaHijas($cuenta))
					{
						$this->mensaje = 'La cuenta no puede ser eliminada, existen cuentas de nivel inferior.';
						$this->valido = false;
					}
					else
					{
						$cadenaPk = "codemp='{$this->codemp}' AND spi_cuenta='{$cuenta}'";
						$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spi_cuentas',array(),$cadenaPk);
						if(!$this->daoPlanCuentaInstitucional->eliminar())
						{
							$this->mensaje = 'Error al eliminar la cuenta '.$cuenta;
							$this->valido = false;
						}
						else
						{
							$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
							$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
							do
							{
								$existe = $this->existeCuenta($cuentaSiguiente);
								if($existe)
								{
									if (!$this->validarCuentaHijas($cuentaSiguiente))
									{
										$cadenaPk = "codemp='{$this->codemp}' AND spi_cuenta='{$cuentaSiguiente}'";
										$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spi_cuentas',array(),$cadenaPk);
										if(!$this->daoPlanCuentaInstitucional->eliminar())
										{
											$this->mensaje = 'Error al eliminar la cuenta '.$cuenta;
											$this->valido = false;
										}
									}
								}
								if ($nivelCuenta > 0)
								{
									$cuentaSiguiente = obtenerCuentaSiguientePlus($cuentaSiguiente, $this->formatoCuenta);
									$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
								}
								else if($nivelCuenta == 0)
								{
									$nivelCuenta = -1 ;
								}
							}while($nivelCuenta > 0);
						}
					}
				}
				else
				{
					$this->mensaje = 'La cuenta no puede ser eliminada, tiene movimientos en '.$relaciones;
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje = 'La cuenta '.$cuenta.' no puede ser eliminada, no existe';
				$this->valido = false;
			}
		}
		DaoGenerico::completarTrans($this->valido);
	}

	public function cuentaEstructura($cuenta,$estructura)
	{
		$this->daoCuentaEstructura = FabricaDao::CrearDAO('N','spi_cuentas_estructuras');
		$this->daoCuentaEstructura->codemp     = $this->codemp;
		$this->daoCuentaEstructura->spi_cuenta = $cuenta;
		$this->daoCuentaEstructura->codestpro1 = '-------------------------'; 
		$this->daoCuentaEstructura->codestpro2 = '-------------------------';
		$this->daoCuentaEstructura->codestpro3 = '-------------------------';
		$this->daoCuentaEstructura->codestpro4 = '-------------------------';
		$this->daoCuentaEstructura->codestpro5 = '-------------------------';
		$this->daoCuentaEstructura->estcla   = '-';
		if($_SESSION['la_empresa']['estpreing']==1)
		{		
			$this->daoCuentaEstructura->codestpro1 = str_pad($estructura->codest0,25,0,0); 
			$this->daoCuentaEstructura->codestpro2 = str_pad($estructura->codest1,25,0,0);
			$this->daoCuentaEstructura->codestpro3 = str_pad($estructura->codest2,25,0,0);
			$this->daoCuentaEstructura->codestpro4 = str_pad($estructura->codest3,25,0,0);
			$this->daoCuentaEstructura->codestpro5 = str_pad($estructura->codest4,25,0,0);
			$this->daoCuentaEstructura->estcla   = $estructura->estcla;
		}
		
		$this->daoCuentaEstructura->previsto     = 0; 
		$this->daoCuentaEstructura->enero        = 0;
		$this->daoCuentaEstructura->febrero      = 0;
		$this->daoCuentaEstructura->marzo        = 0;
		$this->daoCuentaEstructura->abril        = 0;
		$this->daoCuentaEstructura->mayo         = 0;
		$this->daoCuentaEstructura->junio        = 0;
		$this->daoCuentaEstructura->julio        = 0;
		$this->daoCuentaEstructura->agosto       = 0;
		$this->daoCuentaEstructura->septiembre   = 0;
		$this->daoCuentaEstructura->octubre      = 0;
		$this->daoCuentaEstructura->noviembre    = 0;
		$this->daoCuentaEstructura->diciembre    = 0;
		if(!$this->daoCuentaEstructura->incluir())
		{
			$this->mensaje .= 'Error al incluir cuenta estructura en base de datos';
			$this->valido = false;
		}
		unset($this->daoCuentaEstructura);
	}
}
?>