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

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_irecepcion.php');
require_once ($dirsrv.'/modelo/servicio/rpc/sigesp_srv_rpc_beneficiario.php');
require_once ($dirsrv.'/modelo/servicio/rpc/sigesp_srv_rpc_proveedor.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');


class ServicioRecepcion implements IRecepcion 
{
	private $daoRecepcion;
	public  $mensaje; 
	public  $valido;
	public  $conexionBaseDatos; 
		
	public function __construct() 
	{
		$this->daoRecepcion = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->bloanu = $_SESSION['la_empresa']['bloanu'];
	}
	
	public function existeRecepcion($codemp,$numrecdoc,$codtipdoc,$codpro,$cedbene) 
	{
		$existe = false;
		$cadenaSql = "SELECT trim(numrecdoc) as numrecdoc ".
				     "  FROM cxp_rd ".
					 " WHERE codemp='".$codemp."' ".
					 "	 AND trim(numrecdoc) = '".trim($numrecdoc)."' ".
					 "	 AND codtipdoc='".$codtipdoc."' ".
					 "   AND cod_pro='".$codpro."' ".
					 "   AND trim(ced_bene) = '".trim($cedbene)."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$existe = true;
			}
		}
		$cadenaSql = "SELECT MAX(codrecdoc) as codrecdoc ".
				     "  FROM cxp_rd ".
					 " WHERE codemp='".$codemp."' ".
					 " ORDER BY codrecdoc DESC";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$this->daoRecepcion->codrecdoc = round(doubleval($dataSet->fields["codrecdoc"]) + 1,0);
			}
			else
			{
				$this->daoRecepcion->codrecdoc = '0';
			}
			$this->daoRecepcion->codrecdoc = str_pad($this->daoRecepcion->codrecdoc,15,'0',0);
		}
		return $existe;
	}

	public function existeProcedencia($procede) 
	{
		$existe = false;
		$cadenaSql = "SELECT procede ".
				     "  FROM sigesp_procedencias ".
				     " WHERE procede='{$procede}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0)
			{
				$existe = true;
			}
		}
		return $existe;
	}


	public function existeDocumento($codemp,$codtipdoc) 
	{
		$existe = false;
		$cadenaSql = "SELECT codtipdoc ".
				     "  FROM cxp_documento ".
					 " WHERE codemp='".$codemp."' ".
				     "   AND codtipdoc='".$codtipdoc."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) 
			{
				$existe = true;
			}
		}
		return $existe;
	}

	public function existeCuentaSpg($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla,$spg_cuenta,$codfuefin) 
	{
		$existe = false;
		$cadenaSql = "SELECT spg_cuentas.status, spg_cuenta_fuentefinanciamiento.codfuefin ".
					 "	FROM spg_cuentas ".
					 " INNER JOIN spg_cuenta_fuentefinanciamiento ".
					 "    ON spg_cuenta_fuentefinanciamiento.codemp='".$codemp."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codestpro1='".$codestpro1."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codestpro2='".$codestpro2."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codestpro3='".$codestpro3."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codestpro4='".$codestpro4."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codestpro5='".$codestpro5."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.estcla='".$estcla."' ".
					 "   AND spg_cuenta_fuentefinanciamiento.codfuefin='".$codfuefin."' ".
					 "   AND trim(spg_cuenta_fuentefinanciamiento.spg_cuenta)='".$spg_cuenta."' ".
					 "   AND spg_cuentas.codemp=spg_cuenta_fuentefinanciamiento.codemp".
					 "   AND spg_cuentas.codestpro1=spg_cuenta_fuentefinanciamiento.codestpro1 ".
					 "   AND spg_cuentas.codestpro2=spg_cuenta_fuentefinanciamiento.codestpro2 ".
					 "   AND spg_cuentas.codestpro3=spg_cuenta_fuentefinanciamiento.codestpro3 ".
					 "   AND spg_cuentas.codestpro4=spg_cuenta_fuentefinanciamiento.codestpro4 ".
					 "   AND spg_cuentas.codestpro5=spg_cuenta_fuentefinanciamiento.codestpro5 ".
					 "   AND spg_cuentas.estcla=spg_cuenta_fuentefinanciamiento.estcla ".
					 "   AND trim(spg_cuentas.spg_cuenta)=spg_cuenta_fuentefinanciamiento.spg_cuenta ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$status = trim($dataSet->fields['status']); 
				$codfuefin = trim($dataSet->fields['codfuefin']); 
				if($status==='C')
				{
					if(($codfuefin!='')||(!is_null($codfuefin))||(!empty($codfuefin)))
					{
						$existe = true;
					}
					else
					{
						$this->mensaje .= '  -> La cuenta '.$spg_cuenta.'::'.formatoprogramatica($codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5).'::'.$estcla.' No esta asociada a la fuente de financiamiento '.$codfuefin;
						$this->valido = false;
					}
				}
				else
				{
					$this->mensaje .= '  -> La cuenta '.$spg_cuenta.'::'.formatoprogramatica($codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5).'::'.$estcla.' No es de movimiento';
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$spg_cuenta.'::'.formatoprogramatica($codestpro1.$codestpro2.$codestpro3.$codestpro4.$codestpro5).'::'.$estcla.' No existe en la estructura o  No esta asociada a la fuente de financiamiento '.$codfuefin;
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}

	public function existeCuentaScg($codemp,$sc_cuenta) 
	{
		$existe = false;
		$cadenaSql = "SELECT status ".
					 "	FROM scg_cuentas ".
					 " WHERE codemp='".$codemp."' ".
					 "   AND trim(sc_cuenta)='".$sc_cuenta."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$status = $dataSet->fields['status']; 
				if($status==='C')
				{
					$existe = true;
				}
				else
				{
					$this->mensaje .= '  -> La cuenta '.$sc_cuenta.'. No es de movimiento';
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$sc_cuenta.' No existe ';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}	

	public function existeCargo($codemp,$codcar) 
	{
		$existe = false;
		$cadenaSql = "SELECT codcar ".
					 "	FROM sigesp_cargos ".
					 " WHERE codemp='".$codemp."' ".
					 "   AND codcar='".$codcar."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->EOF)
			{
				$this->mensaje .= '  -> El Cargo  '.$codcar.' No existe ';
				$this->valido = false;
			}
			else
			{
				$existe = true;
			}
		}
		unset($dataSet);
		return $existe;
	}	

	public function existeDeduccion($codemp,$codded) 
	{
		$existe = false;
		$cadenaSql = "SELECT codded ".
					 "	FROM sigesp_deducciones ".
					 " WHERE codemp='".$codemp."' ".
					 "   AND codded='".$codded."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->EOF)
			{
				$this->mensaje .= '  -> La Deduccion  '.$codded.' No existe ';
				$this->valido = false;
			}
			else
			{
				$existe = true;
			}
		}
		unset($dataSet);
		return $existe;
	}	

	public function validarRecepcion($arrdetallespg,$arrdetallescg)
	{
		if((is_null($this->daoRecepcion->tipproben)) or (empty($this->daoRecepcion->tipproben)))
		{ 
			$this->mensaje .= 'El Tipo (Beneficiario o Proveedor) no puede tener valor nulo o vacio.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		} 	
		if((is_null($this->daoRecepcion->cod_pro)) or ($this->daoRecepcion->cod_pro=='') or (is_null($this->daoRecepcion->ced_bene)) or ($this->daoRecepcion->ced_bene==''))
		{
			$this->mensaje .=  'El Beneficiario o Proveedor no puede tener valor nulo o vacio.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		$servicioBeneficiario = new ServicioBeneficiario();
		if($servicioBeneficiario->existeBeneficiario($this->daoRecepcion->codemp,$this->daoRecepcion->ced_bene)===false)
		{
			$this->mensaje .=  'El Beneficiario '.$this->daoRecepcion->ced_bene.' no Existe en la Ficha de Beneficiarios.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		unset($servicioBeneficiario);
		$servicioProveedor = new servicioProveedor();
		if($servicioProveedor->existeProveedor($this->daoRecepcion->codemp,$this->daoRecepcion->cod_pro)===false)
		{
			$this->mensaje .=  'El Proveedor '.$this->daoRecepcion->cod_pro.' no Existe en la Ficha de Proveedores.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		unset($servicioProveedor);
		if(!($this->existeProcedencia($this->daoRecepcion->procede)))
		{ 
			$this->mensaje .=  'El Procede '.$this->daoRecepcion->procede.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		if(!($this->existeDocumento($this->daoRecepcion->codemp,$this->daoRecepcion->codtipdoc)))
		{ 
			$this->mensaje .=  'El Documento '.$this->daoRecepcion->codtipdoc.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		if (!(validarFechaMes($this->daoRecepcion->fecregdoc)))
		{
			$this->mensaje .=  'El Mes '.substr($this->daoRecepcion->fecregdoc,5,2).' no esta abierto.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		else
		{
			if(!validarFechaPeriodo($this->daoRecepcion->fecregdoc))
			{
				$this->mensaje .=  'La fecha '.$this->daoRecepcion->fecregdoc.' Esta fuera del periodo.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
		}
		if (!(validarFechaMes($this->daoRecepcion->fecvendoc)))
		{
			$this->mensaje .=  'El Mes '.substr($this->daoRecepcion->fecvendoc,5,2).' no esta abierto.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
			$this->valido = false;	
		}
		else
		{
			if(!validarFechaPeriodo($this->daoRecepcion->fecvendoc))
			{
				$this->mensaje .=  'La fecha '.$this->daoRecepcion->fecvendoc.' Esta fuera del periodo.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
		}
		$totalSPG=count((array)$arrdetallespg);
		$totalSCG=count((array)$arrdetallescg);
		if($totalSCG<=0)
		{
			if($totalSPG<=0)
			{
				$this->mensaje .=  'La Recepcion '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene.' No tiene Detalles.';
				$this->valido = false;	
			}
		}
		else
		{
			$totalDebe=0;
			$totalHaber=0;
			for($i=1;$i<=$totalSCG;$i++)
			{
				if($arrdetallescg[$i]['debhab']==='D')
				{
					$totalDebe=$totalDebe + number_format($arrdetallescg[$i]['monto'],2,'.','');
				}
				else
				{
					$totalHaber=$totalHaber + number_format($arrdetallescg[$i]['monto'],2,'.','');
				}
			}
			if( number_format($totalDebe,2,'.','')!=number_format($totalHaber,2,'.',''))
			{
				$this->mensaje .=  'La Recepcion '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene.', esta descuadrada Debe ('.number_format($totalDebe,2,',','.').') Haber ('.number_format($totalHaber,2,',','.').') .';
				$this->valido = false;	
			}
		}
		return $this->valido;
	}

	public function verificarRecepcion()
	{
		$cadenaSql="SELECT cxp_rd.estprodoc, cxp_rd.estaprord, cxp_dt_solicitudes.numsol ".
				   "  FROM cxp_rd ".
				   "  LEFT JOIN cxp_dt_solicitudes ".
				   "    ON cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
				   "   AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
				   "   AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro".
				   "   AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene".
				   "   AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc".
				   " WHERE cxp_rd.codemp='".$this->daoRecepcion->codemp."' ".
				   "   AND cxp_rd.numrecdoc='".$this->daoRecepcion->numrecdoc."' ".
				   "   AND cxp_rd.cod_pro='".$this->daoRecepcion->cod_pro."'".
				   "   AND cxp_rd.ced_bene='".$this->daoRecepcion->ced_bene."'".
				   "   AND cxp_rd.codtipdoc='".$this->daoRecepcion->codtipdoc."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if (!$dataSet->EOF)
			{
				$estprodoc=$dataSet->fields['estprodoc'];
				if(trim($dataSet->fields['numsol'])!='')
				{
					$estprodoc="P";
					$this->mensaje .= '  -> La Recepcion de Documentos '.$this->daoRecepcion->numrecdoc.' esta tomada en el solicitud de pago '.$dataSet->fields['numsol'].'.';
				}
				if(($estprodoc!="R")||(trim($dataSet->fields['estaprord'])!='0'))
				{
					$this->mensaje .= '  -> La Recepcion de Documentos '.$this->daoRecepcion->numrecdoc.' debe estar en estatus de Registro, no puede estar aprobada y no puede estar tomada en ninguna solicitud de pago.';
					$this->valido = false;
				}
			}
		}
		if ($this->valido)
		{
			$cadenaSql="SELECT monto ".
					   "  FROM cxp_dt_amortizacion ".
					   " WHERE cxp_dt_amortizacion.codemp='".$this->daoRecepcion->codemp."' ".
					   "   AND cxp_dt_amortizacion.numrecdoc='".$this->daoRecepcion->numrecdoc."' ".
					   "   AND cxp_dt_amortizacion.cod_pro='".$this->daoRecepcion->cod_pro."'".
					   "   AND cxp_dt_amortizacion.ced_bene='".$this->daoRecepcion->ced_bene."'".
					   "   AND cxp_dt_amortizacion.codtipdoc='".$this->daoRecepcion->codtipdoc."'";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				if (!$dataSet->EOF)
				{
					$this->mensaje .= '  -> La Recepcion de Documentos '.$this->daoRecepcion->numrecdoc.' no se puede eliminar ya se aplico una amortizacion.';
					$this->valido = false;
				}
			}
		}

		return $this->valido;
	}

	public function guardarRecepcion($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallecargos,$arrdetallededucciones,$arrevento) 
	{
		$this->daoRecepcion = FabricaDao::CrearDAO('N', 'cxp_rd');
		$this->daoRecepcion->codemp       = $arrcabecera['codemp'];
		$this->daoRecepcion->numrecdoc    = $arrcabecera['numrecdoc'];
		$this->daoRecepcion->codtipdoc    = $arrcabecera['codtipdoc'];
		$this->daoRecepcion->ced_bene     = $arrcabecera['ced_bene'];
		$this->daoRecepcion->cod_pro      = $arrcabecera['cod_pro'];
		$this->daoRecepcion->codcla       = $arrcabecera['codcla'];
		$this->daoRecepcion->dencondoc    = $arrcabecera['dencondoc'];
		$this->daoRecepcion->fecemidoc    = $arrcabecera['fecemidoc'];
		$this->daoRecepcion->fecregdoc    = $arrcabecera['fecregdoc'];
		$this->daoRecepcion->fecvendoc    = $arrcabecera['fecvendoc'];
		$this->daoRecepcion->montotdoc    = $arrcabecera['montotdoc'];
		$this->daoRecepcion->mondeddoc    = $arrcabecera['mondeddoc'];
		$this->daoRecepcion->moncardoc    = $arrcabecera['moncardoc'];
		$this->daoRecepcion->tipproben    = $arrcabecera['tipproben'];
		$this->daoRecepcion->numref       = $arrcabecera['numref'];
		$this->daoRecepcion->estprodoc    = $arrcabecera['estprodoc'];
		$this->daoRecepcion->procede      = $arrcabecera['procede'];
		$this->daoRecepcion->estlibcom    = $arrcabecera['estlibcom'];
		$this->daoRecepcion->estaprord    = $arrcabecera['estaprord'];
		$this->daoRecepcion->fecaprord    = $arrcabecera['fecaprord'];
		$this->daoRecepcion->usuaprord    = $arrcabecera['usuaprord'];
		$this->daoRecepcion->numpolcon    = $arrcabecera['numpolcon'];
		$this->daoRecepcion->estimpmun    = $arrcabecera['estimpmun'];
		$this->daoRecepcion->montot       = $arrcabecera['montot'];
		$this->daoRecepcion->codfuefin    = $arrcabecera['codfuefin'];
		$this->daoRecepcion->codrecdoc    = $arrcabecera['codrecdoc'];
		$this->daoRecepcion->fechaconta   = $arrcabecera['fechaconta'];
		$this->daoRecepcion->fechaanula   = $arrcabecera['fechaanula'];
		$this->daoRecepcion->coduniadm    = $arrcabecera['coduniadm'];
		$this->daoRecepcion->codestpro1   = $arrcabecera['codestpro1'];
		$this->daoRecepcion->codestpro2   = $arrcabecera['codestpro2'];
		$this->daoRecepcion->codestpro3   = $arrcabecera['codestpro3'];
		$this->daoRecepcion->codestpro4   = $arrcabecera['codestpro4'];
		$this->daoRecepcion->codestpro5   = $arrcabecera['codestpro5'];
		$this->daoRecepcion->estcla       = $arrcabecera['estcla'];
		$this->daoRecepcion->estact       = $arrcabecera['estact'];
		$this->daoRecepcion->numordpagmin = $arrcabecera['numordpagmin'];
		$this->daoRecepcion->codtipfon    = $arrcabecera['codtipfon'];
		$this->daoRecepcion->repcajchi    = $arrcabecera['repcajchi'];
		$this->daoRecepcion->codproalt    = $arrcabecera['codproalt'];
		$this->daoRecepcion->conanurd     = $arrcabecera['conanurd'];
		$this->daoRecepcion->codusureg    = $arrcabecera['codusureg'];
		$this->daoRecepcion->tipdoctesnac = $arrcabecera['tipdoctesnac'];	
		$this->daoRecepcion->numexprel    = $arrcabecera['numexprel'];
		$this->daoRecepcion->codcencos    = '---';
		$this->daoRecepcion->tipdoctesnac    = '0';
		switch($this->daoRecepcion->tipproben)
		{
			case "P":
				 $this->daoRecepcion->ced_bene='----------';
				 break;
			case "B":
				 $this->daoRecepcion->cod_pro ='----------';
				 break;
		}
		if($this->daoRecepcion->coduniadm=='')
		{
			$this->daoRecepcion->coduniadm='----------';
			$this->daoRecepcion->codestpro1='-------------------------';
			$this->daoRecepcion->codestpro2='-------------------------';
			$this->daoRecepcion->codestpro3='-------------------------';
			$this->daoRecepcion->codestpro4='-------------------------';
			$this->daoRecepcion->codestpro5='-------------------------';
			$this->daoRecepcion->estcla='-';
			$this->daoRecepcion->codfuefin='--';
		}
		if(!$this->existeRecepcion($this->daoRecepcion->codemp,$this->daoRecepcion->numrecdoc,$this->daoRecepcion->codtipdoc,$this->daoRecepcion->cod_pro,$this->daoRecepcion->ced_bene))
		{
			if($this->validarRecepcion($arrdetallespg,$arrdetallescg))
			{
				$resultado = $this->daoRecepcion->incluir(true,"codrecdoc",true,15);
				$arrcadres = explode(",",$resultado);
				if($arrcadres[0]==1||$arrcadres[0]==-1)
				{
					if((count((array)$arrdetallespg)>0)&&($this->valido))
					{
						$this->guardarDetalleSpg($arrdetallespg);
					}
					if((count((array)$arrdetallescg)>0)&&($this->valido))
					{
						$this->guardarDetalleScg($arrdetallescg);
					}
					if((count((array)$arrdetallecargos)>0)&&($this->valido))
					{
						$this->guardarDetalleCargos($arrdetallecargos);
					}
					if((count((array)$arrdetallededucciones)>0)&&($this->valido))
					{
						$this->guardarDetalleDeducciones($arrdetallededucciones);
					}
					if($this->valido)
					{
						$this->daoHistorico = FabricaDao::CrearDAO('N', 'cxp_historico_rd');
						$this->daoHistorico->codemp     = $this->daoRecepcion->codemp;
						$this->daoHistorico->numrecdoc = $this->daoRecepcion->numrecdoc;
						$this->daoHistorico->codtipdoc = $this->daoRecepcion->codtipdoc;
						$this->daoHistorico->ced_bene  = $this->daoRecepcion->ced_bene;
						$this->daoHistorico->cod_pro   = $this->daoRecepcion->cod_pro;
						$this->daoHistorico->fecha     = $this->daoRecepcion->fecregdoc;
						$this->daoHistorico->estprodoc = $this->daoRecepcion->estprodoc;
						$this->valido = $this->daoHistorico->incluir();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoHistorico->ErrorMsg;
						}
					}

				}
				else
				{
					$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					$this->valido = false;
				}
			}
			else
			{
				$this->valido = false;
			}
		}
		else
		{
			$this->mensaje .= 'La Recepcion de Documentos  '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene.' ya existe.';			
			$this->valido = false;	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Incluyo La Recepcion de Documentos  '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;			
		if ($this->valido) 
		{
			$servicioEvento->incluirEvento();
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($servicioEvento);
		return $this->valido;
	}

	public function guardarDetalleSpg($arrdetallespg) 
	{
		$totalspg = count((array)$arrdetallespg);
		for($i=1;($i<=$totalspg)&&($this->valido);$i++)
		{
			$this->daoDetalleSpg = FabricaDao::CrearDAO('N', 'cxp_rd_spg');		
			$this->daoDetalleSpg->codemp=$arrdetallespg[$i]['codemp'];
			$this->daoDetalleSpg->numrecdoc=$arrdetallespg[$i]['numrecdoc'];
			$this->daoDetalleSpg->codtipdoc=$arrdetallespg[$i]['codtipdoc'];
			$this->daoDetalleSpg->ced_bene=$arrdetallespg[$i]['ced_bene'];
			$this->daoDetalleSpg->cod_pro=$arrdetallespg[$i]['cod_pro'];
			$this->daoDetalleSpg->procede_doc=$arrdetallespg[$i]['procede_doc'];
			$this->daoDetalleSpg->numdoccom=$arrdetallespg[$i]['numdoccom'];
			$this->daoDetalleSpg->codestpro=$arrdetallespg[$i]['codestpro'];
			$this->daoDetalleSpg->estcla=$arrdetallespg[$i]['estcla'];
			$this->daoDetalleSpg->spg_cuenta=$arrdetallespg[$i]['spg_cuenta'];
			$this->daoDetalleSpg->codfuefin=$arrdetallespg[$i]['codfuefin'];
			$this->daoDetalleSpg->monto=$arrdetallespg[$i]['monto'];
			$this->daoDetalleSpg->codcencos    = '---';		
			if((is_null($this->daoDetalleSpg->numdoccom)) or (empty($this->daoDetalleSpg->numdoccom)))
			{
				$this->mensaje .= 'El N de Documento no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if((is_null($this->daoDetalleSpg->procede_doc)) or (empty($this->daoDetalleSpg->procede_doc)))
			{
				$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if((is_null($this->daoDetalleSpg->monto)) or (empty($this->daoDetalleSpg->monto)))
			{
				$this->mensaje .= 'En la cuenta '.$this->daoDetalleSpg->spg_cuenta.'.El Monto no puede ser menor o igual a cero. ';			
				$this->valido = false;	
			}
			if(!($this->existeProcedencia($this->daoDetalleSpg->procede_doc)))
			{ 
				$this->mensaje .=  'El Procede '.$this->daoDetalleSpg->procede_doc.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
			$codestpro1=substr($this->daoDetalleSpg->codestpro,0,25);
			$codestpro2=substr($this->daoDetalleSpg->codestpro,25,25);
			$codestpro3=substr($this->daoDetalleSpg->codestpro,50,25);
			$codestpro4=substr($this->daoDetalleSpg->codestpro,75,25);
			$codestpro5=substr($this->daoDetalleSpg->codestpro,100,25);
			if(($this->existeCuentaSpg($this->daoDetalleSpg->codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$this->daoDetalleSpg->estcla,$this->daoDetalleSpg->spg_cuenta,$this->daoDetalleSpg->codfuefin))&&($this->valido))
			{
				$this->valido=$this->daoDetalleSpg->incluir();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoDetalleSpg->ErrorMsg;
				}
			}
		}
		return $this->valido;	
	}

	public function guardarDetalleScg($arrdetallescg)
    {											  
		$totalscg = count((array)$arrdetallescg);
		for($i=1;($i<=$totalscg)&&($this->valido);$i++)
		{
			$this->daoDetalleScg = FabricaDao::CrearDAO('N', 'cxp_rd_scg');		
			$this->daoDetalleScg->codemp=$arrdetallescg[$i]['codemp'];
			$this->daoDetalleScg->numrecdoc=$arrdetallescg[$i]['numrecdoc'];
			$this->daoDetalleScg->codtipdoc=$arrdetallescg[$i]['codtipdoc'];
			$this->daoDetalleScg->ced_bene=$arrdetallescg[$i]['ced_bene'];
			$this->daoDetalleScg->cod_pro=$arrdetallescg[$i]['cod_pro'];
			$this->daoDetalleScg->procede_doc=$arrdetallescg[$i]['procede_doc'];
			$this->daoDetalleScg->numdoccom=$arrdetallescg[$i]['numdoccom'];
			$this->daoDetalleScg->debhab=$arrdetallescg[$i]['debhab'];
			$this->daoDetalleScg->sc_cuenta=$arrdetallescg[$i]['sc_cuenta'];
			$this->daoDetalleScg->estasicon=$arrdetallescg[$i]['estasicon'];
			$this->daoDetalleScg->monto=$arrdetallescg[$i]['monto'];
			$this->daoDetalleScg->estgenasi=$arrdetallescg[$i]['estgenasi'];
			$this->daoDetalleScg->codcencos    = '---';		
			if((is_null($this->daoDetalleScg->numdoccom)) or (empty($this->daoDetalleScg->numdoccom)))
			{
				$this->mensaje .= 'El Nro de Documento no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if((is_null($this->daoDetalleScg->procede_doc)) or (empty($this->daoDetalleScg->procede_doc)))
			{
				$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if((is_null($this->daoDetalleScg->monto)) or (empty($this->daoDetalleScg->monto)) or ($this->daoDetalleScg->monto<=0))
			{
				$this->mensaje .= 'En la cuenta '.$this->daoDetalleScg->sc_cuenta.'. El Monto no puede ser menor o igual a cero.';			
				$this->valido = false;	
			}
			if(!($this->existeProcedencia($this->daoDetalleScg->procede_doc)))
			{ 
				$this->mensaje .=  'El Procede '.$this->daoDetalleScg->procede_doc.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
			if(($this->existeCuentaScg($this->daoDetalleScg->codemp,$this->daoDetalleScg->sc_cuenta))&&($this->valido))
			{
				$this->valido=$this->daoDetalleScg->incluir();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoDetalleScg->ErrorMsg;
				}
			}
		}
		return $this->valido;	
	}

	public function guardarDetalleCargos($arrdetallecargos)
    {											  
		$total = count((array)$arrdetallecargos);
		for($i=1;($i<=$total)&&($this->valido);$i++)
		{
			$this->daoDetalleCargos = FabricaDao::CrearDAO('N', 'cxp_rd_cargos');	
			$this->daoDetalleCargos->codemp=$arrdetallecargos[$i]['codemp'];
			$this->daoDetalleCargos->numrecdoc=$arrdetallecargos[$i]['numrecdoc'];
			$this->daoDetalleCargos->codtipdoc=$arrdetallecargos[$i]['codtipdoc'];
			$this->daoDetalleCargos->ced_bene=$arrdetallecargos[$i]['ced_bene'];
			$this->daoDetalleCargos->cod_pro=$arrdetallecargos[$i]['cod_pro'];
			$this->daoDetalleCargos->codcar=$arrdetallecargos[$i]['codcar'];
			$this->daoDetalleCargos->procede_doc=$arrdetallecargos[$i]['procede_doc'];
			$this->daoDetalleCargos->numdoccom=$arrdetallecargos[$i]['numdoccom'];
			$this->daoDetalleCargos->monobjret=$arrdetallecargos[$i]['monobjret'];
			$this->daoDetalleCargos->monret=$arrdetallecargos[$i]['monret'];
			$this->daoDetalleCargos->codestpro1=$arrdetallecargos[$i]['codestpro1'];
			$this->daoDetalleCargos->codestpro2=$arrdetallecargos[$i]['codestpro2'];
			$this->daoDetalleCargos->codestpro3=$arrdetallecargos[$i]['codestpro3'];
			$this->daoDetalleCargos->codestpro4=$arrdetallecargos[$i]['codestpro4'];
			$this->daoDetalleCargos->codestpro5=$arrdetallecargos[$i]['codestpro5'];
			$this->daoDetalleCargos->estcla=$arrdetallecargos[$i]['estcla'];
			$this->daoDetalleCargos->spg_cuenta=$arrdetallecargos[$i]['spg_cuenta'];
			$this->daoDetalleCargos->codfuefin=$arrdetallecargos[$i]['codfuefin'];
			$this->daoDetalleCargos->porcar=$arrdetallecargos[$i]['porcar'];
			$this->daoDetalleCargos->formula=$arrdetallecargos[$i]['formula'];
			$this->daoDetalleCargos->codcencos    = '---';		
			if(((is_null($this->daoDetalleCargos->numdoccom)) or (empty($this->daoDetalleCargos->numdoccom)))&&($this->valido))
			{
				$this->mensaje .= 'El Nro de Documento no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if(((is_null($this->daoDetalleCargos->procede_doc)) or (empty($this->daoDetalleCargos->procede_doc)))&&($this->valido))
			{
				$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if(((is_null($this->daoDetalleCargos->monret)) or (empty($this->daoDetalleCargos->monret)) or ($this->daoDetalleCargos->monret<=0))&&($this->valido))
			{
				$this->mensaje .= 'En el Cargo '.$this->daoDetalleCargos->codcar.'. El Monto Retenido no puede ser menor o igual a cero.';			
				$this->valido = false;	
			}
			if(!($this->existeProcedencia($this->daoDetalleCargos->procede_doc))&&($this->valido))
			{ 
				$this->mensaje .=  'El Procede '.$this->daoDetalleCargos->procede_doc.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
			if(!($this->existeCargo($this->daoDetalleCargos->codemp,$this->daoDetalleCargos->codcar))&&($this->valido))
			{ 
				$this->valido = false;	
			}
			if(($this->existeCuentaSpg($this->daoDetalleSpg->codemp,$this->daoDetalleCargos->codestpro1,$this->daoDetalleCargos->codestpro2,$this->daoDetalleCargos->codestpro3,$this->daoDetalleCargos->codestpro4,$this->daoDetalleCargos->codestpro5,$this->daoDetalleSpg->estcla,$this->daoDetalleSpg->spg_cuenta,$this->daoDetalleSpg->codfuefin))&&($this->valido))
			{
				$this->valido=$this->daoDetalleCargos->incluir();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoDetalleCargos->ErrorMsg;
				}
			}
		}
		return $this->valido;	
	}
	
	public function guardarDetalleDeducciones($arrdetallededucciones)
    {											  
		$total = count((array)$arrdetallededucciones);
		for($i=1;($i<=$total)&&($this->valido);$i++)
		{
			$this->daoDetalleDeducciones = FabricaDao::CrearDAO('N', 'cxp_rd_deducciones');	
			$this->daoDetalleDeducciones->codemp=$arrdetallededucciones[$i]['codemp'];
			$this->daoDetalleDeducciones->numrecdoc=$arrdetallededucciones[$i]['numrecdoc'];
			$this->daoDetalleDeducciones->codtipdoc=$arrdetallededucciones[$i]['codtipdoc'];
			$this->daoDetalleDeducciones->ced_bene=$arrdetallededucciones[$i]['ced_bene'];
			$this->daoDetalleDeducciones->cod_pro=$arrdetallededucciones[$i]['cod_pro'];
			$this->daoDetalleDeducciones->codded=$arrdetallededucciones[$i]['codded'];
			$this->daoDetalleDeducciones->procede_doc=$arrdetallededucciones[$i]['procede_doc'];
			$this->daoDetalleDeducciones->numdoccom=$arrdetallededucciones[$i]['numdoccom'];
			$this->daoDetalleDeducciones->monobjret=$arrdetallededucciones[$i]['monobjret'];
			$this->daoDetalleDeducciones->monret=$arrdetallededucciones[$i]['monret'];
			$this->daoDetalleDeducciones->sc_cuenta=$arrdetallededucciones[$i]['sc_cuenta'];
			$this->daoDetalleDeducciones->porded=$arrdetallededucciones[$i]['porded'];
			$this->daoDetalleDeducciones->estcmp=$arrdetallededucciones[$i]['estcmp'];
			$this->daoDetalleDeducciones->codcencos    = '---';		
			if(((is_null($this->daoDetalleDeducciones->numdoccom)) or (empty($this->daoDetalleDeducciones->numdoccom)))&&($this->valido))
			{
				$this->mensaje .= 'El Nro de Documento no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if(((is_null($this->daoDetalleDeducciones->procede_doc)) or (empty($this->daoDetalleDeducciones->procede_doc)))&&($this->valido))
			{
				$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
				$this->valido = false;	
			}
			if(((is_null($this->daoDetalleDeducciones->monret)) or (empty($this->daoDetalleDeducciones->monret)) or ($this->daoDetalleDeducciones->monret<=0))&&($this->valido))
			{
				$this->mensaje .= 'En la Deduccion '.$this->daoDetalleDeducciones->codded.'. El Monto Retenido no puede ser menor o igual a cero.';			
				$this->valido = false;	
			}
			if((!($this->existeProcedencia($this->daoDetalleDeducciones->procede_doc)))&&($this->valido))
			{ 
				$this->mensaje .=  'El Procede '.$this->daoDetalleDeducciones->procede_doc.' no Existe.'.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;
				$this->valido = false;	
			}
			if((!($this->existeDeduccion($this->daoDetalleDeducciones->codemp,$this->daoDetalleDeducciones->codded)))&&($this->valido))
			{ 
				$this->valido = false;	
			}
			if(($this->existeCuentaScg($this->daoDetalleDeducciones->codemp,$this->daoDetalleDeducciones->sc_cuenta))&&($this->valido))
			{
				$this->valido=$this->daoDetalleDeducciones->incluir();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoDetalleDeducciones->ErrorMsg;
				}
			}
		}
		return $this->valido;	
	}

	public function eliminarRecepcion($arrcabecera,$arrevento)
	{
		if (trim($arrcabecera['codtipdoc'])=='')
		{
			$criterio="codemp = '".$arrcabecera['codemp']."' AND numrecdoc='".$arrcabecera['numrecdoc']."' AND ced_bene='".$arrcabecera['ced_bene']."' AND cod_pro='".$arrcabecera['cod_pro']."'";
		}
		else
		{
			$criterio="codemp = '".$arrcabecera['codemp']."' AND numrecdoc='".$arrcabecera['numrecdoc']."' AND codtipdoc='".$arrcabecera['codtipdoc']."' AND ced_bene='".$arrcabecera['ced_bene']."' AND cod_pro='".$arrcabecera['cod_pro']."'";
		}
		$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
		if($this->daoRecepcion->numrecdoc!='')
		{
			if($this->verificarRecepcion())
			{
				$this->valido = $this->eliminarDetalles('cxp_historico_rd');
				if($this->valido)
				{
					$this->valido = $this->eliminarDetalles('cxp_rd_amortizacion');
				}			
				if($this->valido)
				{
					$this->valido = $this->eliminarDetalles('cxp_rd_deducciones');
				}			
				if($this->valido)
				{
					$this->valido = $this->eliminarDetalles('cxp_rd_cargos');
				}			
				if($this->valido)
				{
					$this->valido = $this->eliminarDetalles('cxp_rd_scg');
				}			
				if($this->valido)
				{
					$this->valido = $this->eliminarDetalles('cxp_rd_spg');
				}			
				if($this->valido)
				{
					$this->valido = $this->daoRecepcion->eliminar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
				}
			}
		}
		else
		{
			$this->mensaje .= 'La Recepcion de Documentos  '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene.' No existe.';			
			$this->valido = false;	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Elimino La Recepcion de Documentos  '.$this->daoRecepcion->codemp.'::'.$this->daoRecepcion->numrecdoc.'::'.$this->daoRecepcion->codtipdoc.'::'.$this->daoRecepcion->cod_pro.'::'.$this->daoRecepcion->ced_bene;			
		if ($this->valido) 
		{
			$servicioEvento->incluirEvento();
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($servicioEvento);
		return $this->valido;
	}

	public function eliminarDetalles($tabla)
	{
		$this->valido=true;
		$cadenaSQL = "DELETE ".
					 "  FROM {$tabla} ".
					 " WHERE codemp='".$this->daoRecepcion->codemp."' ".
					 "	 AND trim(numrecdoc) = '".trim($this->daoRecepcion->numrecdoc)."' ".
					 "	 AND codtipdoc='".$this->daoRecepcion->codtipdoc."' ".
					 "   AND cod_pro='".$this->daoRecepcion->cod_pro."' ".
					 "   AND trim(ced_bene) = '".trim($this->daoRecepcion->ced_bene)."'";
		
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data === false){
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $this->valido;
	}
	
	
}
?>