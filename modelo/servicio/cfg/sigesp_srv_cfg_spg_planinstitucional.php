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
		$this->gasto_p = $_SESSION['la_empresa']['gasto_p'];
		$this->formatoPlan=trim($_SESSION['la_empresa']['formplan']);
		$this->formatoCuenta=trim($_SESSION['la_empresa']['formpre']);
	}

	public function buscarCuenta($cuenta,$estpro)
	{
		$cadenaSQL = "SELECT spg_cuenta, status, denominacion, sc_cuenta ".
		   			 "	FROM spg_cuentas ". 
		   			 " WHERE codemp= '".$this->codemp."' ".
					 "   AND codestpro1='{$estpro[0]}' ".
					 "   AND codestpro2='{$estpro[1]}' ".
					 "   AND codestpro3='{$estpro[2]}' ".
					 "   AND codestpro4='{$estpro[3]}' ".
					 "   AND codestpro5='{$estpro[4]}' ".
					 "   AND estcla='{$estpro[5]}' ".
		   			 "   AND trim(spg_cuenta)='".trim($cuenta)."'";
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

	public function existeCuenta($estpro,$cuenta,$status)
	{
		$existeCuenta = false;	
		$criterio = "";
		if ($status<>'')
		{
			$criterio = "   AND status='".$status."'";
		}
		$cadenaSQL = "SELECT spg_cuenta ".
					 "  FROM spg_cuentas ".
					 " WHERE codemp = '".$this->codemp."' ".
					 "   AND spg_cuenta='{$cuenta}' ".
					 "   AND codestpro1='{$estpro[0]}' ".
					 "   AND codestpro2='{$estpro[1]}' ".
					 "   AND codestpro3='{$estpro[2]}' ".
					 "   AND codestpro4='{$estpro[3]}' ".
					 "   AND codestpro5='{$estpro[4]}' ".
					 "   AND estcla='{$estpro[5]}' ".
					 $criterio;
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataSet->_numOfRows > 0)
		{
			$existeCuenta = true;
		}
		unset($dataSet);
		return $existeCuenta;
	}

	public function validarCuenta($cuenta, $estpro, $cuentascg)
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
			$longitudCuentaSPG=strlen($cuenta);
			$longitudFormato=strlen(str_replace('-','',$this->formatoCuenta));
	
			if($longitudCuentaSPG!=$longitudFormato)
			{
				$this->mensaje .= 'Formato de presupuesto '.$this->formatoCuenta.' no corresponde al de la cuenta introducida '.$cuenta.'';
				$this->valido= false;
			}
		}
		if ($this->valido)
		{
			if($longitudCuenta<$longitudCuentaSPG)
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
			if(substr($cuenta,0,1)!=trim($this->gasto_p))
			{
				$this->mensaje .= 'Las cuenta '.$cuenta.' debe comenzar con '.$this->gasto_p.' ';
				$this->valido= false;
			}
			$nivel = obtenerNivelPlus($cuenta, $this->formatoCuenta);
			if($nivel<=1)
			{
				$this->mensaje .= 'La cuenta '.$cuenta.' de Nivel Partida no es valida.';
				$this->valido= false;
			}
			$nivel = obtenerNivelPlus($cuenta, $this->formatoCuenta);
			if($nivel > 1)
			{
				$NextCuenta = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				do
				{
					$existe = $this->existeCuenta($estpro,$NextCuenta,'C');
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

	public function grabarCuenta($cuentaspg,$denominacion,$estpro,$cuentascg,$cueclaeco)
	{
		DaoGenerico::iniciarTrans();
                if (trim($cueclaeco) == '')
                {
                    $cueclaeco = '-';
                }
		$cuenta =  obtenerFormatoCuenta($this->formatoCuenta, $cuentaspg);
		$this->validarCuenta($cuenta, $estpro, $cuentascg);
		if ($this->valido)
		{
			$dataCuenta = $this->buscarCuenta($cuenta,$estpro);
			if(!$dataCuenta->EOF)
			{				
				if ($dataCuenta->fields['status'] == 'C')
				{
					$cadenaPk = "codemp='{$this->codemp}' AND spg_cuenta='{$cuentaspg}' AND codestpro1='{$estpro[0]}' AND codestpro2='{$estpro[1]}' AND codestpro3='{$estpro[2]}' AND codestpro4='{$estpro[3]}' AND codestpro5='{$estpro[4]}'  AND estcla='{$estpro[5]}'";
					$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spg_cuentas',array(),$cadenaPk);
					$this->daoPlanCuentaInstitucional->denominacion = $denominacion;
					$this->daoPlanCuentaInstitucional->sc_cuenta    = $cuentascg;
					$this->daoPlanCuentaInstitucional->cueclaeco    = $cueclaeco;
					$this->daoPlanCuentaInstitucional->scgctaint    = '-';
					$this->daoPlanCuentaInstitucional->sc_cuenta_art    = '-';
					if($this->daoPlanCuentaInstitucional->modificar()!=1)
					{
						$this->mensaje .= 'Error al actualizar la cuenta';
						$this->valido=false;
					}
				}
				else
				{
					$this->mensaje .= 'La cuenta '.$cuentaspg.' que intenta registrar ya existe';
					$this->valido=false;
				}
				unset($dataCuenta);
			}
			else
			{
				$numCuentas = 0;
				$arrCuentasIncluir [$numCuentas]['spg_cuenta']     = $cuenta;
				$arrCuentasIncluir [$numCuentas]['codestpro1']     = $estpro[0];
				$arrCuentasIncluir [$numCuentas]['codestpro2']     = $estpro[1];
				$arrCuentasIncluir [$numCuentas]['codestpro3']     = $estpro[2];
				$arrCuentasIncluir [$numCuentas]['codestpro4']     = $estpro[3];
				$arrCuentasIncluir [$numCuentas]['codestpro5']     = $estpro[4];
				$arrCuentasIncluir [$numCuentas]['estcla']     = $estpro[5];
				$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $cuentascg;
                                $arrCuentasIncluir [$numCuentas]['cueclaeco']    = $cueclaeco;
				$arrCuentasIncluir [$numCuentas]['denominacion']  = $denominacion;
				$arrCuentasIncluir [$numCuentas]['referencia'] = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
				$arrCuentasIncluir [$numCuentas]['estatus']       = 'C';
				$cuentaSiguiente = obtenerCuentaSiguientePlus($cuenta, $this->formatoCuenta);
				$nivelCuenta     = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
				do
				{
					$dataCuenta = $this->buscarCuenta($cuentaSiguiente,$estpro);
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
							$denominacion = $denominacion;
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
						$arrCuentasIncluir [$numCuentas]['spg_cuenta']     = $cuentaSiguiente;
						$arrCuentasIncluir [$numCuentas]['codestpro1']     = $estpro[0];
						$arrCuentasIncluir [$numCuentas]['codestpro2']     = $estpro[1];
						$arrCuentasIncluir [$numCuentas]['codestpro3']     = $estpro[2];
						$arrCuentasIncluir [$numCuentas]['codestpro4']     = $estpro[3];
						$arrCuentasIncluir [$numCuentas]['codestpro5']     = $estpro[4];
						$arrCuentasIncluir [$numCuentas]['estcla']     = $estpro[5];
						$arrCuentasIncluir [$numCuentas]['denominacion']  = $denominacion;
						$arrCuentasIncluir [$numCuentas]['referencia'] = $cuentaReferencia;
						$arrCuentasIncluir [$numCuentas]['nivel']         = obtenerNivelPlus($cuentaSiguiente, $this->formatoCuenta);
						$arrCuentasIncluir [$numCuentas]['estatus']       = 'S';
						$arrCuentasIncluir [$numCuentas]['sc_cuenta']     = $cuentascg; 
                                                $arrCuentasIncluir [$numCuentas]['cueclaeco']    = $cueclaeco;
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
					$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('N','spg_cuentas');
					$this->daoPlanCuentaInstitucional->codemp       = $this->codemp;
					$this->daoPlanCuentaInstitucional->spg_cuenta    = $arrCuentasIncluir [$i]['spg_cuenta'];
					$this->daoPlanCuentaInstitucional->codestpro1    = $arrCuentasIncluir [$i]['codestpro1'];
					$this->daoPlanCuentaInstitucional->codestpro2    = $arrCuentasIncluir [$i]['codestpro2'];
					$this->daoPlanCuentaInstitucional->codestpro3    = $arrCuentasIncluir [$i]['codestpro3'];
					$this->daoPlanCuentaInstitucional->codestpro4    = $arrCuentasIncluir [$i]['codestpro4'];
					$this->daoPlanCuentaInstitucional->codestpro5    = $arrCuentasIncluir [$i]['codestpro5'];
					$this->daoPlanCuentaInstitucional->estcla    = $arrCuentasIncluir [$i]['estcla'];
					$this->daoPlanCuentaInstitucional->sc_cuenta    = $arrCuentasIncluir [$i]['sc_cuenta']; 
					$this->daoPlanCuentaInstitucional->cueclaeco    = $arrCuentasIncluir [$i]['cueclaeco']; 
					$this->daoPlanCuentaInstitucional->denominacion = $arrCuentasIncluir [$i]['denominacion'];
					$this->daoPlanCuentaInstitucional->status       = $arrCuentasIncluir [$i]['estatus'];
					$this->daoPlanCuentaInstitucional->nivel        = $arrCuentasIncluir [$i]['nivel'];
					$this->daoPlanCuentaInstitucional->referencia   = $arrCuentasIncluir [$i]['referencia'];
					$this->daoPlanCuentaInstitucional->distribuir   = 1;
					$this->daoPlanCuentaInstitucional->asignado     = 0; 
					$this->daoPlanCuentaInstitucional->precomprometido     = 0; 
					$this->daoPlanCuentaInstitucional->comprometido     = 0; 
					$this->daoPlanCuentaInstitucional->causado     = 0; 
					$this->daoPlanCuentaInstitucional->pagado     = 0; 
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
					$this->daoPlanCuentaInstitucional->scgctaint    = '-';
					$this->daoPlanCuentaInstitucional->sc_cuenta_art    = '-';
					if(!$this->daoPlanCuentaInstitucional->incluir())
					{
						$this->mensaje .= 'Error al incluir cuenta en base de datos';
						$this->valido = false;
					}
					unset($this->daoPlanCuentaInstitucional);
				}
			}
		}
		DaoGenerico::completarTrans($this->valido);
	}

	public function validarRelacionesCuenta($cuenta,$estpro)
	{
		$existe=false;
		$tabla[1]='cxp_dc_spg';
		$tabla[2]='cxp_rd_cargos';
		$tabla[3]='cxp_rd_spg';
		$tabla[4]='scb_movbco_spg';
		$tabla[5]='scb_movbco_spgop';
		$tabla[6]='scb_movcol_spg';
		$tabla[7]='scv_dt_spg';
		$tabla[8]='sep_cuentagasto';
		$tabla[9]='sep_dt_articulos';
		$tabla[10]='sep_dt_concepto';		
		$tabla[11]='sep_dt_servicio';
		$tabla[12]='sep_solicitudcargos';
		$tabla[13]='sigesp_cargos'; 
		$tabla[14]='sno_dt_spg';
		$tabla[15]='soc_cuentagasto';
		$tabla[16]='soc_solicitudcargos';
		$tabla[17]='spg_dt_cmp';
		for($li=1;($li<=17)&&(!$lb_existe);$li++)
		{
			if(($li==13)||($li==1)||($li==3)||($li==4)||($li==5)||($li==6))
			{
				$filtro="   AND codemp='".$this->codemp."' AND codestpro='".$estpro[0].$estpro[1].$estpro[2].$estpro[3].$estpro[4]."' AND estcla='".$estpro[5]."'";
			}
			else
			{
				$filtro=" AND codemp='".$this->codemp."' AND codestpro1='".$estpro[0]."' AND codestpro2='".$estpro[1]."' AND codestpro3='".$estpro[2]."' AND codestpro4='".$estpro[3]."' AND codestpro5='".$estpro[4]."' AND estcla='".$estpro[5]."'";
			}
			$cadenaSQL="SELECT codemp FROM ".$tabla[$li]." WHERE spg_cuenta='".$cuenta."' ".$filtro;	
			$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
			if($dataSet===false)
			{
				return 'Error';
			}
			else
			{
				if(!$dataSet->EOF)	
				{
					$existe=$tabla[$li];
				}
			}			
		}
		return $existe;
	}

	public function validarCuentaHijas($cuenta,$estpro)
	{
		$tieneHijas = false;
		$nivel         = obtenerNivelPlus($cuenta, $this->formatoCuenta);
  		$cantDigitos   = obtenerDigitosNivel($nivel, $this->formatoCuenta);
  		$cuentaSinCero = substr($cuenta, 0,$cantDigitos);
  		
  		$cadenaSQL = "SELECT COUNT(sc_cuenta) AS ntotal ".
  					 "  FROM spg_cuentas ". 
  					 " WHERE codemp='{$this->codemp}' ".
					 "   AND codestpro1='{$estpro[0]}' ".
					 "   AND codestpro2='{$estpro[1]}' ".
					 "   AND codestpro3='{$estpro[2]}' ".
					 "   AND codestpro4='{$estpro[3]}' ".
					 "   AND codestpro5='{$estpro[4]}' ".
					 "   AND estcla='{$estpro[5]}' ".
					 "   AND spg_cuenta like '{$cuentaSinCero}%'";
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
	
	public function eliminarCuenta($cuenta,$estpro)
	{
		DaoGenerico::iniciarTrans();
		$existe = $this->existeCuenta($estpro,$cuenta,'C');
		if($existe)
		{			
			$relaciones = $this->validarRelacionesCuenta($cuenta,$estpro);
			if($relaciones===false)
			{
				if ($this->validarCuentaHijas($cuenta,$estpro))
				{
					$this->mensaje = 'La cuenta no puede ser eliminada, existen cuentas de nivel inferior.';
					$this->valido = false;
				}
				else
				{
					$cadenaPk = "     codemp='{$this->codemp}' ".
								" AND spg_cuenta='{$cuenta}' ".
								" AND codestpro1='{$estpro[0]}' ".
							    " AND codestpro2='{$estpro[1]}' ".
								" AND codestpro3='{$estpro[2]}' ".
								" AND codestpro4='{$estpro[3]}' ".
								" AND codestpro5='{$estpro[4]}' ".
								" AND estcla='{$estpro[5]}' ";
					$this->daoCuentaEstructura = FabricaDao::CrearDAO('C','spg_cuenta_fuentefinanciamiento',array(),$cadenaPk);
					if(!$this->daoCuentaEstructura->eliminar())
					{
						$this->mensaje = 'Error al eliminar la cuenta estructura '.$cuenta;
						$this->valido = false;
					}
					else
					{
						$cadenaPk = "     codemp='{$this->codemp}' ".
									" AND spg_cuenta='{$cuenta}' ".
									" AND codestpro1='{$estpro[0]}' ".
									" AND codestpro2='{$estpro[1]}' ".
									" AND codestpro3='{$estpro[2]}' ".
									" AND codestpro4='{$estpro[3]}' ".
									" AND codestpro5='{$estpro[4]}' ".
									" AND estcla='{$estpro[5]}' ";
						$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spg_cuentas',array(),$cadenaPk);
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
								$existe = $this->existeCuenta($estpro,$cuentaSiguiente,'');
								if($existe)
								{
									if (!$this->validarCuentaHijas($cuentaSiguiente,$estpro))
									{
										$cadenaPk = "     codemp='{$this->codemp}' ".
													" AND spg_cuenta='{$cuentaSiguiente}' ".
													" AND codestpro1='{$estpro[0]}' ".
													" AND codestpro2='{$estpro[1]}' ".
													" AND codestpro3='{$estpro[2]}' ".
													" AND codestpro4='{$estpro[3]}' ".
													" AND codestpro5='{$estpro[4]}' ".
													" AND estcla='{$estpro[5]}' ";
										$this->daoCuentaEstructura = FabricaDao::CrearDAO('C','spg_cuenta_fuentefinanciamiento',array(),$cadenaPk);
										if(!$this->daoCuentaEstructura->eliminar())
										{
											$this->mensaje = 'Error al eliminar la cuenta estructura '.$cuenta;
											$this->valido = false;
										}
										else
										{
											$cadenaPk = "     codemp='{$this->codemp}' ".
														" AND spg_cuenta='{$cuentaSiguiente}' ".
														" AND codestpro1='{$estpro[0]}' ".
														" AND codestpro2='{$estpro[1]}' ".
														" AND codestpro3='{$estpro[2]}' ".
														" AND codestpro4='{$estpro[3]}' ".
														" AND codestpro5='{$estpro[4]}' ".
														" AND estcla='{$estpro[5]}' ";
											$this->daoPlanCuentaInstitucional = FabricaDao::CrearDAO('C','spg_cuentas',array(),$cadenaPk);
											if(!$this->daoPlanCuentaInstitucional->eliminar())
											{
												$this->mensaje = 'Error al eliminar la cuenta '.$cuenta;
												$this->valido = false;
											}
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
							}while(($nivelCuenta >= 0) && ($this->valido));
						}
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
		DaoGenerico::completarTrans($this->valido);
	}

	public function cuentaFuenteFinanciamiento($estpro)
	{  		
		$cadenaSQL="SELECT codfuefin".
                           "  FROM sigesp_fuentefinanciamiento".
			   " WHERE codemp = '{$this->codemp}' ";
		$dataSet = $this->conexionBaseDatos->Execute($cadenaSQL);
		while(!$dataSet->EOF)
		{
			$codfuefin = $dataSet->fields['codfuefin'];
			$cadenaSQL="SELECT codfuefin ".
					   "  FROM spg_dt_fuentefinanciamiento".
					   " WHERE codemp='{$this->codemp}' ".
					   "   AND codestpro1='{$estpro[0]}' ".
					   "   AND codestpro2='{$estpro[1]}' ".
					   "   AND codestpro3='{$estpro[2]}' ".
					   "   AND codestpro4='{$estpro[3]}' ".
					   "   AND codestpro5='{$estpro[4]}' ".
					   "   AND estcla='{$estpro[5]}' ";
					   "   AND codfuefin = '{codfuefin}'";
			$dataSet2 = $this->conexionBaseDatos->Execute($cadenaSQL);
			if ($dataSet2->EOF)
			{
				$this->fuentefinanciamiento = FabricaDao::CrearDAO('N','spg_dt_fuentefinanciamiento');
				$this->fuentefinanciamiento->codemp       = $this->codemp;
				$this->fuentefinanciamiento->codfuefin    = $codfuefin;
				$this->fuentefinanciamiento->codestpro1    = $estpro[0];
				$this->fuentefinanciamiento->codestpro2    = $estpro[1];
				$this->fuentefinanciamiento->codestpro3    = $estpro[2];
				$this->fuentefinanciamiento->codestpro4    = $estpro[3];
				$this->fuentefinanciamiento->codestpro5    = $estpro[4];
				$this->fuentefinanciamiento->estcla    = $estpro[5];
				if(!$this->fuentefinanciamiento->incluir())
				{
					$this->mensaje .= 'Error al incluir fuente de financiamiento en base de datos';
					$this->valido = false;
				}
				unset($this->fuentefinanciamiento);
			}

			$cadenafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_dt_fuentefinanciamiento.codestpro1','spg_dt_fuentefinanciamiento.codestpro2','spg_dt_fuentefinanciamiento.codestpro3','spg_dt_fuentefinanciamiento.codestpro4','spg_dt_fuentefinanciamiento.codestpro5','spg_dt_fuentefinanciamiento.estcla','spg_cuentas.spg_cuenta','spg_dt_fuentefinanciamiento.codfuefin');
			$cadenacuentafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_cuenta_fuentefinanciamiento.codestpro1','spg_cuenta_fuentefinanciamiento.codestpro2','spg_cuenta_fuentefinanciamiento.codestpro3','spg_cuenta_fuentefinanciamiento.codestpro4','spg_cuenta_fuentefinanciamiento.codestpro5','spg_cuenta_fuentefinanciamiento.estcla','spg_cuenta_fuentefinanciamiento.spg_cuenta','spg_cuenta_fuentefinanciamiento.codfuefin');
			$cadenaSQL = "INSERT INTO spg_cuenta_fuentefinanciamiento (codemp,codfuefin,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta, monto) ".
						 "SELECT spg_dt_fuentefinanciamiento.codemp,spg_dt_fuentefinanciamiento.codfuefin,spg_dt_fuentefinanciamiento.codestpro1,spg_dt_fuentefinanciamiento.codestpro2, ".
						 "  	 spg_dt_fuentefinanciamiento.codestpro3,spg_dt_fuentefinanciamiento.codestpro4,spg_dt_fuentefinanciamiento.codestpro5,spg_dt_fuentefinanciamiento.estcla,spg_cuentas.spg_cuenta, 0 ".
						 "  FROM spg_cuentas ".
						 " INNER JOIN spg_dt_fuentefinanciamiento ".
						 "    ON spg_dt_fuentefinanciamiento.codemp = '".$this->codemp."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro1 = '".$estpro[0]."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro2 = '".$estpro[1]."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro3 = '".$estpro[2]."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro4 = '".$estpro[3]."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro5 = '".$estpro[4]."'".
						 "   AND spg_dt_fuentefinanciamiento.estcla = '".$estpro[5]."'".
						 "   AND spg_dt_fuentefinanciamiento.codfuefin = '".$codfuefin."'".
						 "   AND spg_dt_fuentefinanciamiento.codestpro1=spg_cuentas.codestpro1 ".
						 "   AND spg_dt_fuentefinanciamiento.codestpro2=spg_cuentas.codestpro2 ".
						 "   AND spg_dt_fuentefinanciamiento.codestpro3=spg_cuentas.codestpro3 ".
						 "   AND spg_dt_fuentefinanciamiento.codestpro4=spg_cuentas.codestpro4 ".
						 "   AND spg_dt_fuentefinanciamiento.codestpro5=spg_cuentas.codestpro5 ".
						 "   AND spg_dt_fuentefinanciamiento.estcla=spg_cuentas.estcla ".
						 " WHERE  ".$cadenafuentefinanciamiento. 
						 "   NOT IN (SELECT  ".$cadenacuentafuentefinanciamiento.
						 "  		   FROM spg_cuenta_fuentefinanciamiento, spg_cuentas ".
						 "			  WHERE spg_cuenta_fuentefinanciamiento.codestpro1=spg_cuentas.codestpro1 ".
						 "  			AND spg_cuenta_fuentefinanciamiento.codestpro2=spg_cuentas.codestpro2 ".
						 "  			AND spg_cuenta_fuentefinanciamiento.codestpro3=spg_cuentas.codestpro3 ".
						 "  			AND spg_cuenta_fuentefinanciamiento.codestpro4=spg_cuentas.codestpro4 ".
						 "  			AND spg_cuenta_fuentefinanciamiento.codestpro5=spg_cuentas.codestpro5 ".
						 "  			AND spg_cuenta_fuentefinanciamiento.estcla=spg_cuentas.estcla ".
						 "  			AND spg_cuenta_fuentefinanciamiento.spg_cuenta=spg_cuentas.spg_cuenta)";
			$dataSet2 = $this->conexionBaseDatos->Execute($cadenaSQL);
			$dataSet->MoveNext();
			unset($dataSet2);
		}
		unset($dataSet);
	}
}
?>