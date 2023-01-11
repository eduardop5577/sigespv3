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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_icomprobantescg.php');

class ServicioComprobanteSCG implements IComprobanteSCG 
{
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->niveles_scg = null;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->formcont=$_SESSION['la_empresa']['formcont'];
		$this->periodo=convertirFechaBd($_SESSION['la_empresa']['periodo']);
		$this->comprobantecierre = false;	
		if(array_key_exists('comprobantecierre',$_SESSION))
		{
			$this->comprobantecierre = true;		
		}
		$this->cargarNiveles();
	}

	public function cargarNiveles()
	{
		$formato=$this->formcont.'-';
		$posicion=1;
		$indice=1;
		$posicion = posocurrencia($formato,'-' , $indice ) - $indice;	
		do
		{
			$this->niveles_scg[$indice] = $posicion ;
			$indice = $indice + 1;
			$posicion = posocurrencia($formato,'-' , $indice ) - $indice;
		} while ($posicion>=0);
	}

	public function obtenerNivel($cuenta)
	{
		$nivel=0;
		$anterior=0;
		$longitud=0;
		$cadena='';
		$nivel=count((array)$this->niveles_scg);
		do
		{
			$anterior=$this->niveles_scg[$nivel-1]+1;
			$longitud=$this->niveles_scg[$nivel]-$this->niveles_scg[$nivel-1];
			$cadena=substr(trim($cuenta),$anterior,$longitud); 
			$li=intval($cadena);
		    if($li>0)
			{
				return $nivel;
			}
			$nivel=$nivel-1;
		}while($nivel>1);
		return $nivel;
	}

	public function obtenerCuentaSiguiente($cuenta)
	{
  		$MaxNivel=count((array)$this->niveles_scg);
		$nivel=$this->obtenerNivel($cuenta);
		$anterior=0;
		$longitud=0;
		$cadena='';
		if($nivel>1)
		{
			$anterior=$this->niveles_scg[$nivel - 1]; 
			$cadena=substr($cuenta,0,$anterior+1);
			$longitud=strlen($cadena);
			$long=(($this->niveles_scg[$MaxNivel]+1) - $longitud);
			$cadena=str_pad(trim($cadena),$long+$longitud,'0');
		} 
		return $cadena;
	} 
	
	public function existeCierreSCG()
	{
		$existe = false;
		$cadenaSql = "SELECT estciescg ".
					 "	FROM sigesp_empresa ".
					 " WHERE codemp='".$_SESSION['la_empresa']['codemp']."' ".
					 "   AND estciescg=1";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ((!$dataSet->EOF)&&(!$this->comprobantecierre))
			{
				$existe = true;
				$this->mensaje .= '  -> Ya se realizo el cierre contable no se pueden registrar movimientos de este tipo';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function existeCuenta() 
	{
		$existe = false;
		$cadenaSql = "SELECT status ".
					 "	FROM scg_cuentas ".
					 " WHERE codemp='".$this->daoDetalleScg->codemp."' ".
					 "   AND trim(sc_cuenta)='".$this->daoDetalleScg->sc_cuenta."' ";
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
					$this->mensaje .= '  -> La cuenta '.$this->daoDetalleScg->sc_cuenta.'. No es de movimiento';
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleScg->sc_cuenta.' No existe ';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}	

	public function existeMovimiento()
	{
	    $existe=false;
	    $cadenaSql="SELECT monto, orden ".
		           "  FROM scg_dt_cmp ".
		           " WHERE codemp='".$this->daoDetalleScg->codemp."' ".
				   "   AND procede='".$this->daoDetalleScg->procede."' ".
				   "   AND comprobante='".$this->daoDetalleScg->comprobante."' ".
				   "   AND fecha='".$this->daoDetalleScg->fecha."' ".
				   "   AND codban='".$this->daoDetalleScg->codban."' ".
				   "   AND ctaban='".$this->daoDetalleScg->ctaban."' ".
				   "   AND procede_doc='".$this->daoDetalleScg->procede_doc."' ".
				   "   AND documento ='".$this->daoDetalleScg->documento."' ".
				   "   AND sc_cuenta='".$this->daoDetalleScg->sc_cuenta."' ".
				   "   AND debhab='".$this->daoDetalleScg->debhab."'";
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
				$existe=true;
			}			
		}
		unset($dataSet);
		return $existe;
	} 

	public function saldosUpdate($monto_anterior,$monto_actual)
	{
		$cadenaSql="SELECT sc_cuenta ".
				   "  FROM scg_saldos ".
				   " WHERE codemp='".$this->daoDetalleScg->codemp."' ".
				   "   AND sc_cuenta='".$this->daoDetalleScg->sc_cuenta."' ".
				   "   AND fecsal='".$this->daoDetalleScg->fecha."'";
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
				$monto=$monto_actual - $monto_anterior;
				$criterio="";
				if($this->daoDetalleScg->debhab=='D')
				{
					$criterio = " SET debe_mes = debe_mes + (".$monto.")";
				}
				else
				{
					$criterio = " SET haber_mes = haber_mes + (".$monto.")";
				}
				$cadenaSql="UPDATE scg_saldos ".$criterio.
						   " WHERE codemp='".$this->daoDetalleScg->codemp."' ".
						   "   AND sc_cuenta= '".$this->daoDetalleScg->sc_cuenta."' ".
						   "   AND fecsal= '".$this->daoDetalleScg->fecha."'" ;
				$data  = $this->conexionBaseDatos->Execute ( $cadenaSql );
				if ($data===false)
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				unset($data);
			}
			else
			{
				if($this->daoDetalleScg->debhab=='D')
				{
					$cadenaSql="INSERT INTO scg_saldos (codemp,sc_cuenta,fecsal,debe_mes,haber_mes) " .
							   " VALUES ('".$this->daoDetalleScg->codemp."','".$this->daoDetalleScg->sc_cuenta."',".
							   "         '".$this->daoDetalleScg->fecha."',".$monto_actual.",0)";
				}
				else
				{
					$cadenaSql="INSERT INTO scg_saldos (codemp,sc_cuenta,fecsal,debe_mes,haber_mes) " .
							   " VALUES ('".$this->daoDetalleScg->codemp."','".$this->daoDetalleScg->sc_cuenta."',".
							   "         '".$this->daoDetalleScg->fecha."',0,".$monto_actual.")";
				}
				$data  = $this->conexionBaseDatos->Execute ( $cadenaSql );
				if ($data===false)
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				unset($data);
			}
		}
		unset($dataSet);
		return $this->valido;
	}
	
	public function saldoActual($monto_anterior,$monto_actual)
	{
		$cuentaactual=$this->daoDetalleScg->sc_cuenta;
		$nivel=$this->obtenerNivel($cuentaactual);
		while(($nivel>=1)and($this->valido)and($nivel!=''))
		{  
			$this->valido =$this->saldosUpdate($monto_anterior,$monto_actual);
			if(($this->obtenerNivel($this->daoDetalleScg->sc_cuenta)==1)||(!$this->valido))
			{
				break;
			}
			$this->daoDetalleScg->sc_cuenta=$this->obtenerCuentaSiguiente($this->daoDetalleScg->sc_cuenta);
			$nivel=$this->obtenerNivel($this->daoDetalleScg->sc_cuenta);
		}
		$this->daoDetalleScg->sc_cuenta=$cuentaactual;
		return $this->valido;
	} 
	
	public function guardarDetalleSCG($daoComprobante,$arrdetallescg,$arrevento)
    {	
    	$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];										  
		if(!$this->existeCierreSCG())
		{
			$totalscg = count((array)$arrdetallescg);
			for($i=1;($i<=$totalscg)&&($this->valido);$i++)
			{
				$this->daoDetalleScg = FabricaDao::CrearDAO('N', 'scg_dt_cmp');				
				$this->daoDetalleScg->codemp=$arrdetallescg[$i]['codemp'];
				$this->daoDetalleScg->procede=$arrdetallescg[$i]['procede'];
				$this->daoDetalleScg->comprobante=$daoComprobante->comprobante;
				$this->daoDetalleScg->codban=$arrdetallescg[$i]['codban'];
				$this->daoDetalleScg->ctaban=$arrdetallescg[$i]['ctaban'];
				$this->daoDetalleScg->sc_cuenta=$arrdetallescg[$i]['sc_cuenta'];
				$this->daoDetalleScg->procede_doc=$arrdetallescg[$i]['procede_doc'];
				$this->daoDetalleScg->documento=$arrdetallescg[$i]['documento'];
				$this->daoDetalleScg->debhab=$arrdetallescg[$i]['debhab'];
				$this->daoDetalleScg->fecha=$arrdetallescg[$i]['fecha'];
				$_SESSION['fechacomprobante']=$this->daoDetalleScg->fecha;
				$this->daoDetalleScg->descripcion=$arrdetallescg[$i]['descripcion'];
				$this->daoDetalleScg->monto=$arrdetallescg[$i]['monto'];
				$this->daoDetalleScg->orden=$arrdetallescg[$i]['orden'];
				$this->daoDetalleScg->codcencos='---';
				if((is_null($this->daoDetalleScg->documento)) or (empty($this->daoDetalleScg->documento)))
				{
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleScg->procede_doc)) or (empty($this->daoDetalleScg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleScg->descripcion)) or (empty($this->daoDetalleScg->descripcion)))
				{
					$this->mensaje .= 'La Descripcion no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if(!$this->existeMovimiento())
					{
						if($this->saldoActual(0,$this->daoDetalleScg->monto))
						{
							$this->valido=$this->daoDetalleScg->incluir();
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoDetalleScg->ErrorMsg;
							}
							$servicioEvento->tipoevento=$this->valido; 
							if($this->valido)
							{
								$servicioEvento->desevetra='Incluyo detalle contable '.$this->daoDetalleScg->sc_cuenta.'::'.$this->daoDetalleScg->procede_doc.'::'.
														   $this->daoDetalleScg->documento.'::'.$this->daoDetalleScg->debhab.'::'.
														   $this->daoDetalleScg->fecha.'::'.$this->daoDetalleScg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
														   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
														   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
								$servicioEvento->incluirEvento();
							}
							else
							{
								$this->valido=false;
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}							
						}
						else
						{
							$this->valido=false;
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else
					{
						$this->valido=false;
						$this->mensaje .= ' -> El movimiento '.$this->daoDetalleScg->sc_cuenta.'::'.$this->daoDetalleScg->debhab.', Ya existe .';
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
			}
			unset($servicioEvento);
		}
		else
		{
			$this->valido=false;
			$servicioEvento->tipoevento=$this->valido; 
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		return $this->valido;	
	}

	public function eliminarDetalleSCG($daoComprobante,$arrdetallescg,$arrevento) 
	{
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		if((!$this->existeCierreSCG())||($this->comprobantecierre))
		{
			$totalscg = count((array)$arrdetallescg);
			for($i=1;($i<=$totalscg)&&($this->valido);$i++)
			{
				$criterio="     codemp  = '".$arrdetallescg[$i]['codemp']."'".
				          " AND procede = '".$arrdetallescg[$i]['procede']."' ".
						  " AND comprobante = '".$arrdetallescg[$i]['comprobante']."' ".
						  " AND codban = '".$arrdetallescg[$i]['codban']."' ".
						  " AND ctaban = '".$arrdetallescg[$i]['ctaban']."' ".
						  " AND sc_cuenta = '".$arrdetallescg[$i]['sc_cuenta']."' ".
						  " AND procede_doc = '".$arrdetallescg[$i]['procede_doc']."' ".
						  " AND documento = '".$arrdetallescg[$i]['documento']."' ".
						  " AND debhab = '".$arrdetallescg[$i]['debhab']."' ";					  
				$this->daoDetalleScg = FabricaDao::CrearDAO('C','scg_dt_cmp','',$criterio);
				if((is_null($this->daoDetalleScg->documento)) or (empty($this->daoDetalleScg->documento)))
				{
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleScg->procede_doc)) or (empty($this->daoDetalleScg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleScg->descripcion)) or (empty($this->daoDetalleScg->descripcion)))
				{
					$this->mensaje .= 'La Descripcion no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if($this->existeMovimiento())
					{
						if($this->saldoActual(0,$this->daoDetalleScg->monto))
						{
							$this->valido=$this->daoDetalleScg->eliminar('','',true);
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoDetalleScg->ErrorMsg;
							}
							$servicioEvento->tipoevento=$this->valido; 
							if($this->valido)
							{
								$servicioEvento->desevetra='Elimino detalle contable '.$this->daoDetalleScg->sc_cuenta.'::'.$this->daoDetalleScg->procede_doc.'::'.
														   $this->daoDetalleScg->documento.'::'.$this->daoDetalleScg->debhab.'::'.
														   $this->daoDetalleScg->fecha.'::'.$this->daoDetalleScg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
														   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
														   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
								$servicioEvento->incluirEvento();
							}
							else
							{
								$this->valido=false;
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}							
						}
						else
						{
							$this->valido=false;
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else
					{
						$this->valido=false;
						$this->mensaje .= ' -> El movimiento '.$this->daoDetalleScg->sc_cuenta.'::'.$this->daoDetalleScg->debhab.', Ya existe.';
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
}
?>