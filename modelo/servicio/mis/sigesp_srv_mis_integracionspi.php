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
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionspi.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class servicioIntegracionSPI implements IIntegracionSPI 
{
	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos; 
	public  $daoComprobante;
	
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->contintmovban=$_SESSION['la_empresa']['contintmovban'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->daoComprobante = null;
		$this->codemp=$_SESSION['la_empresa']['codemp'];
	}

	public function buscarCmpSpi($comprobante,$procede,$fecha,$estatus)
	{
		$criterio="";
		if($fecha!="")
		{
			$fecha=convertirFechaBd($fecha);
			$criterio .=" AND fecha ='".$fecha."' ";
		}
		if($comprobante!="")
		{
			$criterio .=" AND comprobante like '%".$comprobante."%' ";
		}
		if($procede!="")
		{
			$criterio .=" AND procede like '%SPI".$procede."%' ";
		}
		else
		{
			$criterio .=" AND procede like '%SPI%' ";		
		}
		$cadenasql="SELECT comprobante, fecha, procede, descripcion, fechaconta, fechaanula ". 
				   "  FROM sigesp_cmp_md ".
				   "  WHERE codemp = '".$this->codemp."' ".
				   "    AND tipo_comp = 2 ".
				   "    AND estapro = ".$estatus." ".
				   $criterio.
			       "  ORDER BY fecha, comprobante ";	
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.=' ERROR->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		return $data;
	}
	
	public function buscarDetalleSpi($codcom,$procede)
	{
		$codest1 = "SUBSTR(codestpro1,length(codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
		$codest2 = "SUBSTR(codestpro2,length(codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
		$codest3 = "SUBSTR(codestpro3,length(codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
		$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
		if($_SESSION["la_empresa"]["estmodest"]=="2")
		{
			$codest4 = "SUBSTR(codestpro4,length(codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
			$codest5 = "SUBSTR(codestpro5,length(codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
			$cadenaEstructura = $cadenaEstructura.$this->conexionBaseDatos->Concat("'-'",$codest4,"'-'",$codest5);
		}
		$cadenasql="SELECT spi_dtmp_cmp.spi_cuenta, monto, codestpro1,  codestpro2, codestpro3,  codestpro4,  ".
		           "       codestpro5, estcla, {$cadenaEstructura} as estructura, operacion, spi_cuentas.denominacion ".
				   "  FROM spi_dtmp_cmp ".
				   " INNER JOIN spi_cuentas ". 
				   "    ON spi_dtmp_cmp.codemp = spi_cuentas.codemp ". 
				   "   AND spi_dtmp_cmp.spi_cuenta = spi_cuentas.spi_cuenta ". 
				   " WHERE spi_dtmp_cmp.codemp = '".$this->codemp."' ".
				   "   AND procede = '".$procede."' ".
				   "   AND comprobante = '".$codcom."' ";	
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.=' ERROR->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		else
		{
			$j=0;
			while(!$data->EOF)
			{
				$arrDisponible[$j]['estructura']     = $data->fields['estructura'];
				$arrDisponible[$j]['estcla']         = $data->fields['estcla'];
				$arrDisponible[$j]['operacion']      = $data->fields['operacion'];
				$arrDisponible[$j]['spi_cuenta']     = $data->fields['spi_cuenta'];
				$arrDisponible[$j]['monto']          = $data->fields['monto'];
				$arrDisponible[$j]['denominacion']   = utf8_encode($data->fields['denominacion']);
				$data->MoveNext();
			}
		}
		return $arrDisponible;
	}
	
	public function buscarDetalleScg($codcom,$procede)
	{
		$arreglo=array();
		$cadenasql="SELECT scg_dtmp_cmp.sc_cuenta, debhab, monto, scg_cuentas.denominacion ".
				   "  FROM scg_dtmp_cmp ".
				   " INNER JOIN scg_cuentas ". 
				   "    ON scg_dtmp_cmp.codemp = scg_cuentas.codemp ". 
				   "   AND scg_dtmp_cmp.sc_cuenta = scg_cuentas.sc_cuenta ". 
				   " WHERE scg_dtmp_cmp.codemp = '".$this->codemp."' ".
				   "   AND procede = '".$procede."' ".
				   "   AND comprobante = '".$codcom."' ".
				   " ORDER BY  debhab ";	
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.=' ERROR->'.$this->conexionBaseDatos->ErrorMsg();		
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				if($data->fields['debhab']=='D')
				{
					$arreglo[$i]['cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['debe']=$data->fields['monto'];
					$arreglo[$i]['haber']='0,00';
					$arreglo[$i]['debhab']='D';
				}
				elseif($data->fields['debhab']=='H')
				{
					$arreglo[$i]['cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['haber']=$data->fields['monto'];
					$arreglo[$i]['debe']='0,00';
					$arreglo[$i]['debhab']='H';
				}
				$data->MoveNext();
				$i++;
			}
		}
		return $arreglo;
	}
	
	public function buscarDetalleIngresosSPI($codcom,$procede,$fecha,$arrcabecera)
	{
		$arregloSPI=array();
		$cadenasql="SELECT spi_cuenta, monto, documento, procede_doc, operacion, descripcion, orden, ".
		           "       codestpro1,  codestpro2,  codestpro3,  codestpro4,  codestpro5, estcla   ".
                   "  FROM spi_dtmp_cmp ".
                   " WHERE codemp='".$this->codemp."' ".
				   "   AND comprobante='".$codcom."' ".
				   "   AND procede='".$procede."' ".
                   "   AND fecha='".$fecha."' ".
				   " ORDER BY orden ";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.=' ERROR->'.$this->conexionBaseDatos->ErrorMsg();		
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				$arregloSPI[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPI[$i]['procede']= $arrcabecera['procede'];
				$arregloSPI[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPI[$i]['codban']= $arrcabecera['codban'];
				$arregloSPI[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPI[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPI[$i]['descripcion']=$data->fields['descripcion'];
				$arregloSPI[$i]['orden']= $i;
				$arregloSPI[$i]['estcla']=$data->fields['estcla'];
				$arregloSPI[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPI[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPI[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPI[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPI[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPI[$i]['spi_cuenta']=$data->fields['spi_cuenta'];
				$arregloSPI[$i]['procede_doc']= $data->fields['procede_doc'];
				$arregloSPI[$i]['documento']= $data->fields['documento'];
				$arregloSPI[$i]['operacion']= $data->fields['operacion'];
				$arregloSPI[$i]['monto']=$data->fields['monto'];
				$data->MoveNext();
				$i++;
			}
		}
		return $arregloSPI;
	}
	
	public function buscarDetalleContablesSCG($codcom,$fecha,$procede,$arrcabecera)
	{
		$arregloSCG=array();
		$cadenasql="SELECT sc_cuenta, procede_doc, documento, debhab, descripcion, monto  ".
                   "  FROM scg_dtmp_cmp ".
                   " WHERE codemp='".$this->codemp."' ".
				   "   AND comprobante='".$codcom."' ".
				   "   AND fecha='".$fecha."' ".
                   "   AND procede='".$procede."'";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.="CLASE->buscarDetalleScg MÉTODO->buscarDetalleContablesSCG ERROR->";			
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				$i++;
				$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSCG[$i]['procede']= $arrcabecera['procede'];
				$arregloSCG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSCG[$i]['codban']= $arrcabecera['codban'];
				$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $data->fields['descripcion'];
				$arregloSCG[$i]['orden']= $i;
				$arregloSCG[$i]['sc_cuenta']=$data->fields['sc_cuenta'];
				$arregloSCG[$i]['procede_doc']=$data->fields['procede_doc'];
				$arregloSCG[$i]['debhab']=$data->fields['debhab'];				
				$arregloSCG[$i]['monto']=$data->fields['monto'];				
				$arregloSCG[$i]['documento']=$data->fields['documento'];
				$data->MoveNext();
			}
		}
		return $arregloSCG;
	}
	
	
	public function ContabilizarSPI($objson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $this->codemp;
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
			$arrevento['desevetra'] = "Contabilizar la modificaci&#243;n presupuestaria de ingreso {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Modificaci&#243;n presupuestaria de ingreso contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La modificaci&#243;n presupuestaria de ingreso no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	
	
	public function contabilizar($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$arrdetallespi=array();
		$arrdetallescg=array();
		$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
		$procede=$objson->arrDetalle[$j]->procede;
		$fechacom=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		// OBTENGO EL COMPROBANTE PRESUPUESTARIO DE INGRESO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND fecha='".$fechacom."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp_md','',$criterio);
		// VERIFICO QUE EL COMPROBANTE PRESUPUESTARIO DE INGRESO EXISTA
		if($this->daoComprobante->codcom=='')
		{
			$this->mensaje .= 'ERROR -> No existe el comprobante presupuestarias de ingreso N°'.$comprobante;
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE APROBACION DEL COMPROBANTE SEA MAYOR O IGUAL A LA FECHA DEL COMPROBANTE
		$fecha=convertirFechaBd($objson->fecapro);
        if(!compararFecha($this->daoComprobante->fecha,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Aprobaci&#243;n '.$fecha.' es menor que la fecha del Comprobante '.$this->daoComprobante->fecha;
			$this->valido = false;			
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = $this->daoComprobante->procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoComprobante->descripcion;
			$arrcabecera['tipo_comp'] = 2;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoComprobante->total,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoComprobante->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->buscarDetalleContablesSCG($comprobante,$this->daoComprobante->fecha,$this->daoComprobante->procede,$arrcabecera);
			if($this->valido)
			{
				$arrdetallespi=$this->buscarDetalleIngresosSPI($comprobante,$this->daoComprobante->procede,$this->daoComprobante->fecha,$arrcabecera);
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,$arrdetallespi,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoComprobante->estapro='1';
				$this->daoComprobante->fechaconta=$fecha;
				$this->daoComprobante->fechaanula='1900-01-01';
				$this->valido = $this->daoComprobante->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoComprobante->ErrorMsg;
				}				
			}
			unset($serviciocomprobante);
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
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
	
	public function RevContabilizarSPI($objson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $this->codemp;
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
			$arrevento['desevetra'] = "Reversar la modificaci&#243;n presupuestario de ingreso {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->Reversar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Modificaci&#243;n presupuestaria de ingreso reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La modificaci&#243;n presupuestaria de ingreso no fue reversada, {$this->mensaje} ";
			}
			$this->valido=true;
			$this->mensaje='';
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function Reversar($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
		$procede=$objson->arrDetalle[$j]->procede;
		$fechacom=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		// OBTENGO EL COMPROBANTE PRESUPUESTARIO DE INGRESO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND fecha='".$fechacom."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp_md','',$criterio);
		// VERIFICO QUE EL COMPROBANTE PRESUPUESTARIO DE INGRESO EXISTA
		if($this->daoComprobante->codcom=='')
		{
			$this->mensaje .= 'ERROR -> No existe el comprobante presupuestario de ingreso N°'.$comprobante;
			$this->valido = false;			
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = $this->daoComprobante->procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoComprobante->fechaconta;
			$arrcabecera['descripcion'] = $this->daoComprobante->descripcion;
			$arrcabecera['tipo_comp'] = 2;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoComprobante->total,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoComprobante->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoComprobante->estapro='0';
				$this->daoComprobante->fechaconta='1900-01-01';
				$this->daoComprobante->fechaanula='1900-01-01';
				$this->valido = $this->daoComprobante->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoComprobante->ErrorMsg;
				}				
			}
			unset($serviciocomprobante);
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
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
}
?>