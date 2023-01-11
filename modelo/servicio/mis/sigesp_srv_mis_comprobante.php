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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_icomprobante.php');
require_once ($dirsrv.'/modelo/servicio/rpc/sigesp_srv_rpc_beneficiario.php');
require_once ($dirsrv.'/modelo/servicio/rpc/sigesp_srv_rpc_proveedor.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantescg.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantespi.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');

class ServicioComprobante implements IComprobante 
{
	private $daoComprobante;
	public  $mensaje; 
	public  $valido;
	public  $conexionBaseDatos;
    public  $prefijo;
        
	public function __construct()
    {
		$this->daoComprobante = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->bloanu = $_SESSION['la_empresa']['bloanu'];
		$this->anulando = false;
		$this->eliminando = false;
    }
        
	public function ServicioComprobante() 
	{
		$this->daoComprobante = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->bloanu = $_SESSION['la_empresa']['bloanu'];
		$this->anulando = false;
		$this->eliminando = false;
	}
	
	public function existeComprobante($codemp,$procede,$comprobante,$codban,$ctaban) 
	{
		$existe = false;
		$cadenaSql = "SELECT comprobante ".
					 "	FROM sigesp_cmp ".
					 " WHERE codemp='{$codemp}' ".
					 "   AND procede='{$procede}' ".
					 "   AND comprobante='{$comprobante}' ".
					 "   AND codban='{$codban}' ".
					 "   AND ctaban='{$ctaban}'";
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
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}
	public function validarSEPCompromiso($comprobante) 
	{
		$existe = false;
		$cadenaSql = "SELECT numsol ".
				     "  FROM sep_solicitud,sep_tiposolicitud ".
				     " WHERE numsol='".$comprobante."'".
					 "   AND sep_solicitud.codemp=sep_tiposolicitud.codemp".
					 "   AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol".
					 "   AND sep_tiposolicitud.estope='O'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}

	public function validarCXPCompromiso($comprobante) 
	{
		$existe = false;
		$cadenaSql = "SELECT cxp_solicitudes.numsol ".
				     "  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_documento".
				     " WHERE cxp_solicitudes.numsol='".$comprobante."'".
					 "   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
					 "   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
					 "   AND cxp_dt_solicitudes.codemp=cxp_documento.codemp".
					 "   AND cxp_dt_solicitudes.codtipdoc=cxp_documento.codtipdoc".
					 "   AND cxp_documento.estpre=2";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}

	public function validarSNOCompromiso($comprobante) 
	{
		$existe = false;
		$cadenaSql = "SELECT codcom ".
				     "  FROM sno_dt_spg".
				     " WHERE codcom='".$comprobante."'".
					 "   AND operacion IN ('OCP','OC','O')";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}

	public function validarSCBCompromiso($comprobante) 
	{
		$existe = false;
		$cadenaSql = "SELECT numsol ".
				     "  FROM scb_movbco_spg".
				     " WHERE numdoc='".$comprobante."'".
					 "   AND operacion IN ('CCP')";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}

	public function validarComprobante($arrdetallespg,$arrdetallescg,$arrdetallespi)
	{
		$validar=true;
		if($this->bloanu=='0')
		{
			switch (($this->daoComprobante->procede)&&($this->anulando))
			{
				case 'SEPSPA':
					$validar=false;
				break;
				case 'SEPRPC':
					$validar=false;
				break;
				case 'SCBBAC':
					$validar=false;
				break;
				case 'SOBRAS':
					$validar=false;
				break;
				case 'SOBACO':
					$validar=false;
				break;
				case 'CXPARD':
					$validar=false;
				break;
				case 'SEPAOS':
					$validar=false;
				break;
				case 'SOCAOC':
					$validar=false;
				break;
				case 'CXPAOP':
					$validar=false;
				break;
				case 'SCBBAC':
					$validar=false;
				break;
				case 'SCBBAH':
					$validar=false;
				break;
			}
		}
		if((is_null($this->daoComprobante->comprobante)) or (empty($this->daoComprobante->comprobante)))
		{
			$this->mensaje .= 'El N° de Comprobante no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;			
			$this->valido = false;	
		}
		if((is_null($this->daoComprobante->procede)) or (empty($this->daoComprobante->procede)))
		{
			$this->mensaje = 'La procedencia no puede tener valor nulo o vacio .'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		} 	  
		if((is_null($this->daoComprobante->descripcion)) or (empty($this->daoComprobante->descripcion)))
		{
			$this->mensaje .= 'La descripci&#243;n no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		} 	
		if((is_null($this->daoComprobante->tipo_destino)) or (empty($this->daoComprobante->tipo_destino)))
		{ 
			$this->mensaje .= 'El Tipo (Beneficiario o Proveedor) no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		} 	
		if((is_null($this->daoComprobante->cod_pro)) or ($this->daoComprobante->cod_pro=='') or (is_null($this->daoComprobante->ced_bene)) or ($this->daoComprobante->ced_bene==''))
		{
			$this->mensaje .=  'El Beneficiario o Proveedor no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		}
		$servicioBeneficiario = new ServicioBeneficiario();
		if($servicioBeneficiario->existeBeneficiario($this->daoComprobante->codemp,$this->daoComprobante->ced_bene)===false)
		{
			$this->mensaje .=  'El Beneficiario '.$this->daoComprobante->ced_bene.' no Existe en la Ficha de Beneficiarios.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		}
		unset($servicioBeneficiario);
		$servicioProveedor = new servicioProveedor();
		if($servicioProveedor->existeProveedor($this->daoComprobante->codemp,$this->daoComprobante->cod_pro)===false)
		{
			$this->mensaje .=  'El Proveedor '.$this->daoComprobante->cod_pro.' no Existe en la Ficha de Proveedores.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		}
		unset($servicioProveedor);
		if(!($this->existeProcedencia($this->daoComprobante->procede)))
		{ 
			$this->mensaje .=  'El Procede '.$this->daoComprobante->procede.' no Existe.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
			$this->valido = false;	
		}
		if($validar)
		{
			if (!(validarFechaMes($this->daoComprobante->fecha)))
			{
				$this->mensaje .=  'El Mes '.obtenerNombreMes(substr($this->daoComprobante->fecha,5,2)).' no esta abierto.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
				$this->valido = false;	
			}
			else
			{
				if(!validarFechaPeriodo($this->daoComprobante->fecha))
				{
					$this->mensaje .=  'La fecha '.substr($this->daoComprobante->fecha,5,2).' Est&#225; fuera del periodo.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
					$this->valido = false;	
				}
			}
		}
		if(!$this->eliminando)
		{
			$totalSPG=count((array)$arrdetallespg);
			$totalSCG=count((array)$arrdetallescg);
			$totalSPI=count((array)$arrdetallespi);
			$montoMov=0;
			if($totalSCG<=0)
			{
				if($totalSPG<=0)
				{
					if($totalSPI<=0)
					{
						$this->mensaje .=  'El comprobante '.$this->daoComprobante->comprobante.' No tiene detalles.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
						$this->valido = false;	
					}
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
					$this->mensaje .=  'El comprobante '.$this->daoComprobante->comprobante.', esta descuadrado Debe ('.number_format($totalDebe,2,',','.').') Haber ('.number_format($totalHaber,2,',','.').') .'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;
					$this->valido = false;	
				}
			}
			if($totalSPI>0)
			{
				for($i=0;$i<$totalSPI;$i++)
				{
					$montoMov=$montoMov + number_format($arrdetallespi[$i]['monto'],2,'.','');
				}
			}
			else
			{
				if($totalSPG>0)
				{
					for($i=1;$i<=$totalSPG;$i++)
					{
						$montoMov=$montoMov + number_format($arrdetallespg[$i]['monto'],2,'.','');
					}		
				}
				else
				{
					if($montoMov==0)
					{
						$montoMov=$totalDebe;
					}											
				}
			}
			$this->daoComprobante->total=$montoMov;	
		}	
		return $this->valido;
	}

	public function cargarDetallesComprobante($tipoevento='',$fechaanula='',$procedeanula='',$conceptoanula='')
	{
		$arrdetallespg=null;
		$arrdetallescg=null;
		$arrdetallespi=null;
		$procede='procede as procede';
		$procede_doc='procede_doc as procede_doc';
		$documento='documento as documento';
		if($tipoevento=='ANULA')
		{
			$procede="'".$procedeanula."' as procede";
			$procede_doc="'".$this->daoComprobante->procede."' as procede_doc";
			$documento="'".$this->daoComprobante->comprobante."' as documento";
		}
		//CARGAMOS LOS DETALLES CONTABLES
		$cadenaSql="SELECT codemp,".$procede.",comprobante,codban,ctaban,sc_cuenta,".$procede_doc.",".$documento.",debhab,Max(fecha) AS fecha,MAX(descripcion) AS descripcion, SUM(monto) AS monto ".
				   "  FROM scg_dt_cmp ".
				   " WHERE codemp='".$this->daoComprobante->codemp."' ".
				   "   AND procede='".$this->daoComprobante->procede."' ".
				   "   AND comprobante='".$this->daoComprobante->comprobante."' ".
				   "   AND fecha='".$this->daoComprobante->fecha."'".
				   "   AND codban='".$this->daoComprobante->codban."'".
				   "   AND ctaban='".$this->daoComprobante->ctaban."'".
				   " GROUP BY codemp,procede,comprobante,codban,ctaban,sc_cuenta,debhab,7,8";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$dataSet->EOF)
			{
				$i++;
				$arrdetallescg[$i]['codemp'] = $dataSet->fields['codemp'];
				$arrdetallescg[$i]['procede'] = $dataSet->fields['procede'];
				$arrdetallescg[$i]['comprobante'] = $dataSet->fields['comprobante'];
				$arrdetallescg[$i]['codban'] = $dataSet->fields['codban'];
				$arrdetallescg[$i]['ctaban'] = $dataSet->fields['ctaban'];
				$arrdetallescg[$i]['sc_cuenta'] = $dataSet->fields['sc_cuenta'];
				$arrdetallescg[$i]['procede_doc'] = $dataSet->fields['procede_doc'];
				$arrdetallescg[$i]['documento'] = $dataSet->fields['documento'];
				$arrdetallescg[$i]['debhab'] = $dataSet->fields['debhab'];
				$arrdetallescg[$i]['fecha'] = $dataSet->fields['fecha'];
				$arrdetallescg[$i]['descripcion'] = $dataSet->fields['descripcion'];
				$arrdetallescg[$i]['monto'] = $dataSet->fields['monto'];
				$arrdetallescg[$i]['orden'] = $i;
				if($tipoevento=='ANULA')
				{
					if($arrdetallescg[$i]['debhab']=='D')
					{
						$arrdetallescg[$i]['debhab']='H';
					}
					else
					{
						$arrdetallescg[$i]['debhab']='D';
					}
					$arrdetallescg[$i]['procede'] = $procedeanula;	
					$arrdetallescg[$i]['fecha'] = $fechaanula;
					$arrdetallescg[$i]['descripcion'] .= ' '.$conceptoanula;
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		//CARGAMOS LOS DETALLES PRESUPUESARIOS DE GASTO
		$cadenaSql="SELECT codemp,".$procede.",comprobante,codban,ctaban,estcla,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,".
				   "       ".$procede_doc.",".$documento.",operacion,codfuefin,Max(fecha) AS fecha,MAX(descripcion) AS descripcion, SUM(monto) AS monto ".
				   "  FROM spg_dt_cmp ".
				   " WHERE codemp='".$this->daoComprobante->codemp."' ".
				   "   AND procede='".$this->daoComprobante->procede."' ".
				   "   AND comprobante='".$this->daoComprobante->comprobante."' ".
				   "   AND fecha='".$this->daoComprobante->fecha."'".
				   "   AND codban='".$this->daoComprobante->codban."'".
				   "   AND ctaban='".$this->daoComprobante->ctaban."'".
				   " GROUP BY codemp,procede,comprobante,codban,ctaban,codestpro1,estcla,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,codfuefin,".
				   "       operacion,13,14";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$dataSet->EOF)
			{
				$i++;
				$arrdetallespg[$i]['codemp']=$dataSet->fields['codemp'];
				$arrdetallespg[$i]['procede']= $dataSet->fields['procede'];
				$arrdetallespg[$i]['comprobante']= $dataSet->fields['comprobante'];
				$arrdetallespg[$i]['codban']= $dataSet->fields['codban'];
				$arrdetallespg[$i]['ctaban']= $dataSet->fields['ctaban'];
				$arrdetallespg[$i]['estcla']=$dataSet->fields['estcla'];
				$arrdetallespg[$i]['codestpro1']=$dataSet->fields['codestpro1'];
				$arrdetallespg[$i]['codestpro2']=$dataSet->fields['codestpro2'];
				$arrdetallespg[$i]['codestpro3']=$dataSet->fields['codestpro3'];
				$arrdetallespg[$i]['codestpro4']=$dataSet->fields['codestpro4'];
				$arrdetallespg[$i]['codestpro5']=$dataSet->fields['codestpro5'];
				$arrdetallespg[$i]['spg_cuenta']=$dataSet->fields['spg_cuenta'];
				$arrdetallespg[$i]['procede_doc']= $dataSet->fields['procede_doc'];
				$arrdetallespg[$i]['documento']= $dataSet->fields['documento'];
				$arrdetallespg[$i]['operacion']= $dataSet->fields['operacion'];
				$arrdetallespg[$i]['codfuefin']=$dataSet->fields['codfuefin'];
				$arrdetallespg[$i]['fecha']= $dataSet->fields['fecha'];
				$arrdetallespg[$i]['descripcion']= $dataSet->fields['descripcion'];
				$arrdetallespg[$i]['monto']=$dataSet->fields['monto'];
				$arrdetallespg[$i]['orden']= $i;
				if($tipoevento=='ANULA')
				{
					$arrdetallespg[$i]['procede'] = $procedeanula;	
					$arrdetallespg[$i]['fecha'] = $fechaanula;
					$arrdetallespg[$i]['descripcion'] .= ' '.$conceptoanula;
					$arrdetallespg[$i]['monto'] = $arrdetallespg[$i]['monto']*(-1);
					$arrdetallespg[$i]['mensaje'] = '';
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		//CARGAMOS LOS DETALLES PRESUPUESARIOS DE INGRESO
		$cadenaSql="SELECT codemp,".$procede.",comprobante,codban,ctaban,estcla,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spi_cuenta,".$procede_doc.",".$documento.",operacion,Max(fecha) AS fecha,MAX(descripcion) AS descripcion, SUM(monto) AS monto ".
				   "  FROM spi_dt_cmp ".
				   " WHERE codemp='".$this->daoComprobante->codemp."' ".
				   "   AND procede='".$this->daoComprobante->procede."' ".
				   "   AND comprobante='".$this->daoComprobante->comprobante."' ".
				   "   AND fecha='".$this->daoComprobante->fecha."'".
				   "   AND codban='".$this->daoComprobante->codban."'".
				   "   AND ctaban='".$this->daoComprobante->ctaban."'".
				   " GROUP BY codemp,procede,comprobante,codban,ctaban,codestpro1,estcla,codestpro2,codestpro3,codestpro4,codestpro5,spi_cuenta,".
				   "       operacion,13,14";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$dataSet->EOF)
			{
				$i++;
				$arrdetallespi[$i]['codemp']=$dataSet->fields['codemp'];
				$arrdetallespi[$i]['procede']= $dataSet->fields['procede'];
				$arrdetallespi[$i]['comprobante']= $dataSet->fields['comprobante'];
				$arrdetallespi[$i]['codban']= $dataSet->fields['codban'];
				$arrdetallespi[$i]['ctaban']= $dataSet->fields['ctaban'];
				$arrdetallespi[$i]['estcla']=$dataSet->fields['estcla'];
				$arrdetallespi[$i]['codestpro1']=$dataSet->fields['codestpro1'];
				$arrdetallespi[$i]['codestpro2']=$dataSet->fields['codestpro2'];
				$arrdetallespi[$i]['codestpro3']=$dataSet->fields['codestpro3'];
				$arrdetallespi[$i]['codestpro4']=$dataSet->fields['codestpro4'];
				$arrdetallespi[$i]['codestpro5']=$dataSet->fields['codestpro5'];
				$arrdetallespi[$i]['spi_cuenta']=$dataSet->fields['spi_cuenta'];
				$arrdetallespi[$i]['procede_doc']= $dataSet->fields['procede_doc'];
				$arrdetallespi[$i]['documento']= $dataSet->fields['documento'];
				$arrdetallespi[$i]['operacion']= $dataSet->fields['operacion'];
				$arrdetallespi[$i]['fecha']= $dataSet->fields['fecha'];
				$arrdetallespi[$i]['descripcion']= $dataSet->fields['descripcion'];
				$arrdetallespi[$i]['monto']=$dataSet->fields['monto'];
				$arrdetallespi[$i]['orden']= $i;
				if($tipoevento=='ANULA')
				{
					$arrdetallespi[$i]['procede'] = $procedeanula;	
					$arrdetallespi[$i]['fecha'] = $fechaanula;
					$arrdetallespi[$i]['descripcion'] .= ' '.$conceptoanula;
					$arrdetallespi[$i]['monto'] = $arrdetallespi[$i]['monto']*(-1);
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		$arrResultado['Spg']=$arrdetallespg;
		$arrResultado['Scg']=$arrdetallescg;
		$arrResultado['Spi']=$arrdetallespi;
		return $arrResultado;
	}
	
	public function guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento,$utilizaprefijo=false) 
	{
		$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
		$this->daoComprobante->codemp      = $arrcabecera['codemp'];
		$this->daoComprobante->procede     = $arrcabecera['procede'];
		$this->daoComprobante->comprobante = fillComprobante($arrcabecera['comprobante']);
		$this->daoComprobante->codban      = $arrcabecera['codban'];
		$this->daoComprobante->ctaban      = $arrcabecera['ctaban'];
		$this->daoComprobante->fecha       = $arrcabecera['fecha'];
		$this->daoComprobante->descripcion = $arrcabecera['descripcion'];
		$this->daoComprobante->tipo_comp   = $arrcabecera['tipo_comp'];
		$this->daoComprobante->tipo_destino= $arrcabecera['tipo_destino'];
		$this->daoComprobante->cod_pro     = $arrcabecera['cod_pro'];
		$this->daoComprobante->ced_bene    = $arrcabecera['ced_bene'];
		$this->daoComprobante->total       = $arrcabecera['total'];
		$this->daoComprobante->numpolcon   = $arrcabecera['numpolcon'];
		$this->daoComprobante->esttrfcmp   = $arrcabecera['esttrfcmp'];
		$this->daoComprobante->estrenfon   = $arrcabecera['estrenfon'];
		$this->daoComprobante->codfuefin   = $arrcabecera['codfuefin'];
		$this->daoComprobante->codusu      = $arrcabecera['codusu'];
		$this->daoComprobante->numconcom      = $arrcabecera['numconcom'];
		$this->daoComprobante->codcencos   = '---';
		if($utilizaprefijo)
		{
			$nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>$arrcabecera['procede']),substr($arrcabecera['procede'], 0, 3),$arrcabecera['procede'],$arrcabecera['codusu'],'',$this->prefijo);
			if($nronuevo!=$arrcabecera['comprobante'])
			{
				$this->daoComprobante->comprobante = fillComprobante($nronuevo);
				$this->mensaje .= " Le fue asignado el numero de comprobante ".$nronuevo.", ";
			}
			else
			{
				$nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>$arrcabecera['procede']),substr($arrcabecera['procede'], 0, 3),$arrcabecera['procede'],$arrcabecera['codusu'],'',$this->prefijo);
				if($nronuevo!=$arrcabecera['comprobante'])
				{
					$this->daoComprobante->comprobante = fillComprobante($nronuevo);
					$this->mensaje .= " Le fue asignado el numero de comprobante ".nronuevo.", ";
				}
			}
		}
		if(!$this->existeComprobante($this->daoComprobante->codemp,$this->daoComprobante->procede,$this->daoComprobante->comprobante,$this->daoComprobante->codban,$this->daoComprobante->ctaban))
		{	
			if($this->validarComprobante($arrdetallespg,$arrdetallescg,$arrdetallespi))
			{
				$ls_numconcom="000000000000000";
				if($_SESSION['la_empresa']['estconcom']=='1')
				{
					switch (($this->daoComprobante->procede))
					{
						case 'SEPSPC':
							$lb_existe=$this->validarSEPCompromiso($arrcabecera['comprobante']);
							if($lb_existe)
							{
								$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
								$this->daoComprobante->numconcom   = $ls_numconcom;
							}
						break;
						case 'SOCCOC':
							$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
							$this->daoComprobante->numconcom   = $ls_numconcom;
						break;
						case 'SOCCOS':
							$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
							$this->daoComprobante->numconcom   = $ls_numconcom;
						break;
						case 'SOBRAS':
							$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
							$this->daoComprobante->numconcom   = $ls_numconcom;
						break;
						case 'SOBACO':
							$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
							$this->daoComprobante->numconcom   = $ls_numconcom;
						break;
						case 'CXPSOP':
							$lb_existe=$this->validarCXPCompromiso($arrcabecera['comprobante']);
							if($lb_existe)
							{
								$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
								$this->daoComprobante->numconcom   = $ls_numconcom;
							}
						break;
						case 'SNOCNO':
							$lb_existe=$this->validarSNOCompromiso($arrcabecera['comprobante']);
							if($lb_existe)
							{
								$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
								$this->daoComprobante->numconcom   = $ls_numconcom;
							}
						break;
						case 'SCBBCH':
							$lb_existe=$this->validarSCBCompromiso($arrcabecera['comprobante']);
							if($lb_existe)
							{
								$ls_numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$arrcabecera['codusu'],'nroinicom',$this->prefijo);
								$this->daoComprobante->numconcom   = $ls_numconcom;
							}
						break;
						default:
							if($this->daoComprobante->numconcom=="")
							{
								$this->daoComprobante->numconcom="000000000000000";
							}
						break;
					}
				}
				if (substr($arrcabecera['procede'], 0, 3) == 'SPG' || substr($arrcabecera['procede'], 0, 3) == 'SCG')
				{
					if($utilizaprefijo)
					{
						$respuesta = $this->daoComprobante->incluir(true,"comprobante",true,15,false,array('procede'=>$arrcabecera['procede']),substr($arrcabecera['procede'], 0, 3),$arrcabecera['procede'],$arrcabecera['codusu']);
						if ($respuesta === true)
						{
							$this->valido = true;
						}
						else
						{
							if (($respuesta !== false)&&($this->daoComprobante->errorDuplicate))
							{
								$this->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento,$utilizaprefijo);
							}
							else
							{
								$this->valido = false;
							}
						}
					}
					else
					{
						$this->valido = $this->daoComprobante->incluir();
					}
				}
				else
				{
					$this->valido = $this->daoComprobante->incluir();
				}
				if($this->valido)
				{
					if((count((array)$arrdetallespg)>0)&&($this->valido))
					{
						// incluir detalles de Presupuesto de Gasto
						$servicioDetalleSPG = new ServicioComprobanteSPG();
						$servicioDetalleSPG->conexionBaseDatos = $this->conexionBaseDatos;
						$this->valido=$servicioDetalleSPG->guardarDetalleSPG($this->daoComprobante,$arrdetallespg,$arrevento);
						$this->mensaje .= $servicioDetalleSPG->mensaje;
						unset($servicioDetalleSPG);				
					}
					if((count((array)$arrdetallescg)>0)&&($this->valido))
					{
						// incluir detalles de Contabilidad 
						$servicioDetalleSCG = new ServicioComprobanteSCG();
						$servicioDetalleSCG->conexionBaseDatos = $this->conexionBaseDatos;
						$this->valido=$servicioDetalleSCG->guardarDetalleSCG($this->daoComprobante,$arrdetallescg,$arrevento);
						$this->mensaje .= $servicioDetalleSCG->mensaje;
						unset($servicioDetalleSCG);						
					}
					if((count((array)$arrdetallespi)>0)&&($this->valido))
					{
						// incluir detalles de Contabilidad 
						$servicioDetalleSPI = new ServicioComprobanteSPI();
						$this->valido=$servicioDetalleSPI->guardarDetalleSPI($this->daoComprobante,$arrdetallespi,$arrevento);
						$this->mensaje .= $servicioDetalleSPI->mensaje;
						unset($servicioDetalleSPI);						
					}
				}
				else
				{
					$this->mensaje .= $this->daoComprobante->ErrorMsg;
				}
			}
			else
			{
				$this->valido = false;
			}
		}
		else
		{
			$this->valido = false;
			$this->mensaje .= 'El Comprobante '.$this->daoComprobante->codemp.'-'.$this->daoComprobante->procede.'-'.$this->daoComprobante->comprobante.'-'.$this->daoComprobante->codban.'-'.$this->daoComprobante->ctaban.' Ya existe favor verifique los datos.';
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Incluyo el comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;			
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

	public function eliminarComprobante($arrcabecera,$arrevento)
	{
		$this->eliminando=true;
		$criterio="codemp = '".$arrcabecera['codemp']."' AND procede='".$arrcabecera['procede']."' AND comprobante='".fillComprobante($arrcabecera['comprobante'])."' AND codban='".$arrcabecera['codban']."' AND ctaban='".$arrcabecera['ctaban']."'";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);
		if($this->existeComprobante($this->daoComprobante->codemp,$this->daoComprobante->procede,$this->daoComprobante->comprobante,$this->daoComprobante->codban,$this->daoComprobante->ctaban))
		{	
			$_SESSION['fechacomprobante']=$this->daoComprobante->fecha;
			$arrResultado=$this->cargarDetallesComprobante();
			$arrdetallespg=$arrResultado['Spg'];
			$arrdetallescg=$arrResultado['Scg'];
			$arrdetallespi=$arrResultado['Spi'];
			if($this->validarComprobante($arrdetallespg,$arrdetallescg,$arrdetallespi)&&($this->valido))
			{
				if((count((array)$arrdetallespg)>0)&&($this->valido))
				{
					// eliminar detalles de Presupuesto de Gasto
					$servicioDetalleSPG = new ServicioComprobanteSPG();
					$servicioDetalleSPG->conexionBaseDatos = $this->conexionBaseDatos;
					$this->valido=$servicioDetalleSPG->eliminarDetalleSPG($this->daoComprobante,$arrdetallespg,$arrevento);
					$this->mensaje=$servicioDetalleSPG->mensaje;
					unset($servicioDetalleSPG);
				}
				if((count((array)$arrdetallescg)>0)&&($this->valido))
				{
					// eliminar detalles de contabilidad
					$servicioDetalleSCG = new ServicioComprobanteSCG();
					$servicioDetalleSCG->conexionBaseDatos = $this->conexionBaseDatos;
					$this->valido=$servicioDetalleSCG->eliminarDetalleSCG($this->daoComprobante,$arrdetallescg,$arrevento);
					$this->mensaje=$servicioDetalleSCG->mensaje;
					unset($servicioDetalleSCG);
				}
				if((count((array)$arrdetallespi)>0)&&($this->valido))
				{
					// incluir detalles de Contabilidad 
					$servicioDetalleSPI = new ServicioComprobanteSPI();
					$this->valido=$servicioDetalleSPI->eliminarDetalleSPI($this->daoComprobante,$arrdetallespi,$arrevento);
					$this->mensaje=$servicioDetalleSPI->mensaje;
					unset($servicioDetalleSPI);						
				}
				if ($this->valido)
				{
					$this->valido = $this->daoComprobante->eliminar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoComprobante->ErrorMsg;
					}
				}
			}
			else
			{
				$this->valido = false;
			}
		}
		else
		{
			$this->mensaje .= 'El Comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban.' no existe.';			
			$this->valido = false;	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Elimino el comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;			
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

	public function anularComprobante($arrcabecera,$fechaanula,$procedeanula,$conceptoanula,$arrevento) 
	{
		$this->anulando=true;
		$criterio="codemp = '".$arrcabecera['codemp']."' AND procede='".$arrcabecera['procede']."' AND comprobante='".fillComprobante($arrcabecera['comprobante'])."' AND codban='".$arrcabecera['codban']."' AND ctaban='".$arrcabecera['ctaban']."'";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);		
		if($this->existeComprobante($this->daoComprobante->codemp,$this->daoComprobante->procede,$this->daoComprobante->comprobante,$this->daoComprobante->codban,$this->daoComprobante->ctaban))
		{
			$arrResultado=$this->cargarDetallesComprobante('ANULA',$fechaanula,$procedeanula,$conceptoanula);
			$arrdetallespg=$arrResultado['Spg'];
			$arrdetallescg=$arrResultado['Scg'];
			$arrdetallespi=$arrResultado['Spi'];
			$fechaanula=convertirFechaBd($fechaanula);
	        if(compararFecha($this->daoComprobante->fecha,$fechaanula)&&($this->valido))
			{		
				unset($this->daoComprobante);
				$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
				$this->daoComprobante->codemp      = $arrcabecera['codemp'];
				$this->daoComprobante->procede     = $procedeanula;
				$this->daoComprobante->comprobante = fillComprobante($arrcabecera['comprobante']);
				$this->daoComprobante->codban      = $arrcabecera['codban'];
				$this->daoComprobante->ctaban      = $arrcabecera['ctaban'];
				$this->daoComprobante->fecha       = $fechaanula;
				$this->daoComprobante->descripcion = $arrcabecera['descripcion'].' '.$conceptoanula;
				$this->daoComprobante->tipo_comp   = $arrcabecera['tipo_comp'];
				$this->daoComprobante->tipo_destino= $arrcabecera['tipo_destino'];
				$this->daoComprobante->cod_pro     = $arrcabecera['cod_pro'];
				$this->daoComprobante->ced_bene    = $arrcabecera['ced_bene'];
				$this->daoComprobante->total       = $arrcabecera['total']*-1;
				$this->daoComprobante->numpolcon   = $arrcabecera['numpolcon'];
				$this->daoComprobante->esttrfcmp   = $arrcabecera['esttrfcmp'];
				$this->daoComprobante->estrenfon   = $arrcabecera['estrenfon'];
				$this->daoComprobante->codfuefin   = $arrcabecera['codfuefin'];
				$this->daoComprobante->codusu      = $arrcabecera['codusu'];				
				$this->daoComprobante->codcencos   = '---';
				$_SESSION['fechacomprobante']=$this->daoComprobante->fecha;
				if(!$this->existeComprobante($this->daoComprobante->codemp,$this->daoComprobante->procede,$this->daoComprobante->comprobante,$this->daoComprobante->codban,$this->daoComprobante->ctaban))
				{
					if($this->validarComprobante($arrdetallespg,$arrdetallescg,$arrdetallespi))
					{
						$this->valido = $this->daoComprobante->incluir();
						if($this->valido)
						{
							if((count((array)$arrdetallespg)>0)&&($this->valido))
							{
								// incluir detalles de Presupuesto de Gasto
								$servicioDetalleSPG = new ServicioComprobanteSPG();
								$servicioDetalleSPG->conexionBaseDatos = $this->conexionBaseDatos;
								$this->valido=$servicioDetalleSPG->guardarDetalleSPG($this->daoComprobante,$arrdetallespg,$arrevento);
								$this->mensaje=$servicioDetalleSPG->mensaje;
								unset($servicioDetalleSPG);
							}
							if((count((array)$arrdetallescg)>0)&&($this->valido))
							{
								// incluir detalles de Contabilidad 
								$servicioDetalleSCG = new ServicioComprobanteSCG();
								$servicioDetalleSCG->conexionBaseDatos = $this->conexionBaseDatos;
								$this->valido=$servicioDetalleSCG->guardarDetalleSCG($this->daoComprobante,$arrdetallescg,$arrevento);
								$this->mensaje=$servicioDetalleSCG->mensaje;
								unset($servicioDetalleSCG);						
							}
							if((count((array)$arrdetallespi)>0)&&($this->valido))
							{
								// incluir detalles de Contabilidad 
								$servicioDetalleSPI = new ServicioComprobanteSPI();
								$this->valido=$servicioDetalleSPI->guardarDetalleSPI($arrdetallespi,$arrevento);
								$this->mensaje=$servicioDetalleSPI->mensaje;
								unset($servicioDetalleSPI);						
							}
						}
						else
						{
							$this->mensaje .= $this->daoComprobante->ErrorMsg;
						}
					}
					else
					{
						$this->valido = false;
					}
				}
				else
				{
					$this->mensaje .= 'El Comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban.' ya existe.';			
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= 'ERROR -> La Fecha de Anulaci&#243;n '.$fechaanula.' es menor que la fecha del comprobante origen '.$this->daoComprobante->fecha;
				$this->valido = false;
			}			
		}
		else
		{
			$this->mensaje .= 'El Comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban.' no existe.';			
			$this->valido = false;	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Incluyo el comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->codban.'::'.$this->daoComprobante->ctaban;			
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

    public function buscarCodigoComprobante($codemp) {
        
    }

}
?>