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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionspg.php");
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionSPG implements IIntegracionSPG
{

	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $servicioComprobante;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();	
		$this->codemp=$_SESSION['la_empresa']['codemp'];
	}

	public function buscarModificaciones($numcom, $procede, $fecha, $estapro)
	{
		$criterio = '';
		
		if($fecha != '')
		{
			$fecha = convertirFechaBd($fecha);
			$criterio .= " AND fecha ='{$fecha}'";
		}
		if($numcom != '')
		{
			$criterio .= " AND comprobante like '%{$numcom}%'";
		}
		if($procede != '') 
		{
			$criterio .= " AND procede like '%SPG{$procede}%'";
		}
		else
		{
			$criterio .= " AND procede like '%SPG%'";		
		}
		
		$cadenaSQL = "SELECT comprobante, fecha, procede, descripcion, fechaconta ".
					 "  FROM sigesp_cmp_md ". 
					 " WHERE codemp = '{$this->codemp}' ". 
					 "   AND estapro = {$estapro} ". 
					 "   AND tipo_comp = 2 ".
					 " {$criterio} ".  
					 " ORDER BY fecha, comprobante";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->valido=false;
                        $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		return $data;
	}
	
	public function buscarDetallePresupuesto($numcom, $procede)
	{
		$modalidad=$_SESSION["la_empresa"]["estmodest"];
		
		switch($modalidad)
		{
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(spg_dtmp_cmp.codestpro1,length(spg_dtmp_cmp.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(spg_dtmp_cmp.codestpro2,length(spg_dtmp_cmp.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(spg_dtmp_cmp.codestpro3,length(spg_dtmp_cmp.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
				
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(spg_dtmp_cmp.codestpro1,length(spg_dtmp_cmp.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(spg_dtmp_cmp.codestpro2,length(spg_dtmp_cmp.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(spg_dtmp_cmp.codestpro3,length(spg_dtmp_cmp.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(spg_dtmp_cmp.codestpro4,length(spg_dtmp_cmp.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(spg_dtmp_cmp.codestpro5,length(spg_dtmp_cmp.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		 
		$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , spg_dtmp_cmp.estcla, spg_dtmp_cmp.spg_cuenta, operacion, monto, 0 AS disponibilidad, ".
		             "       spg_dtmp_cmp.codestpro1,spg_dtmp_cmp.codestpro2,spg_dtmp_cmp.codestpro3,spg_dtmp_cmp.codestpro4,spg_dtmp_cmp.codestpro5, ".
					 "       spg_cuentas.denominacion  ".  
		             "  FROM spg_dtmp_cmp  ".
					 " INNER JOIN spg_cuentas ". 
					 "    ON spg_dtmp_cmp.codemp = spg_cuentas.codemp ". 
					 "   AND spg_dtmp_cmp.codestpro1 = spg_cuentas.codestpro1 ". 
					 "   AND spg_dtmp_cmp.codestpro2 = spg_cuentas.codestpro2 ". 
					 "   AND spg_dtmp_cmp.codestpro3 = spg_cuentas.codestpro3 ". 
					 "   AND spg_dtmp_cmp.codestpro4 = spg_cuentas.codestpro4 ". 
					 "   AND spg_dtmp_cmp.codestpro5 = spg_cuentas.codestpro5 ". 
					 "   AND spg_dtmp_cmp.estcla = spg_cuentas.estcla ". 
					 "   AND spg_dtmp_cmp.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE spg_dtmp_cmp.codemp = '{$this->codemp}' ". 
		             "   AND comprobante = '{$numcom}' ". 
		             "   AND procede = '{$procede}'";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function obtenerDetallePresupuestoDisponibilidad($numcom, $procede)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numcom, $procede);
		while (!$dataCuentas->EOF)
		{
			$disponible = 0;
			$this->servicioComprobante = new ServicioComprobanteSPG();
			$arrdetallespg['codemp']     = $this->codemp;
			$arrdetallespg['codestpro1'] = $dataCuentas->fields['codestpro1'];
			$arrdetallespg['codestpro2'] = $dataCuentas->fields['codestpro2'];
			$arrdetallespg['codestpro3'] = $dataCuentas->fields['codestpro3'];
			$arrdetallespg['codestpro4'] = $dataCuentas->fields['codestpro4'];
			$arrdetallespg['codestpro5'] = $dataCuentas->fields['codestpro5'];
			$arrdetallespg['estcla']     = $dataCuentas->fields['estcla'];
			$arrdetallespg['spg_cuenta'] = $dataCuentas->fields['spg_cuenta'];
			$this->servicioComprobante->setDaoDetalleSpg($arrdetallespg);
			$this->servicioComprobante->saldoSelect($status, $asignado, $aumento, $disminucion, $precomprometido, $comprometido, $causado, $pagado,'ACTUAL');
			
			$disponibilidad =  (($asignado + $aumento) - ( $disminucion + $comprometido + $precomprometido));
			if(trim($dataCuentas->fields['operacion']) == 'DI')
			{
				if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2))
				{
					$disponible = 1;
				}
			}
			else
			{
				$disponible = 1;
			}
			$arrDisponible[$j]['estructura']     = $dataCuentas->fields['estructura'];
			$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
			$arrDisponible[$j]['operacion']      = $dataCuentas->fields['operacion'];
			$arrDisponible[$j]['spg_cuenta']     = $dataCuentas->fields['spg_cuenta'];
			$arrDisponible[$j]['monto']          = $dataCuentas->fields['monto'];
			$arrDisponible[$j]['denominacion']   = utf8_encode($dataCuentas->fields['denominacion']);
			$arrDisponible[$j]['disponibilidad'] = $disponible;
			
			unset($this->servicioComprobante);
			$j++;
			$dataCuentas->MoveNext();
		}
		unset($dataCuentas);
		
		return $arrDisponible;
	}
	
	public function detalleContable($numcom, $procede)
	{
		$cadenaSQL = "SELECT scg_dtmp_cmp.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe,".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion ".  
		             "  FROM scg_dtmp_cmp ".
				     " INNER JOIN scg_cuentas ". 
				     "    ON scg_dtmp_cmp.codemp = scg_cuentas.codemp ". 
				     "   AND scg_dtmp_cmp.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE scg_dtmp_cmp.codemp = '{$this->codemp}' ". 
		             "   AND comprobante = '{$numcom}' ". 
		             "   AND procede = '{$procede}'";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetalleSPG($codcom,$procede,$fecha,$arrcabecera)
	{
		$arregloSPG=array();
		$cadenasql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, ".
				"		   documento, operacion, procede_doc, descripcion, monto, orden, codfuefin  ".
                "   FROM spg_dtmp_cmp ".
                "   WHERE codemp='".$this->codemp."' ".
				"	  AND comprobante='".$codcom."' ".
				"	  AND procede='".$procede."' ".
                "     AND fecha='".$fecha."' ".
				"   ORDER BY orden";
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
				$i++;
				$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']=$data->fields['descripcion'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['estcla']=$data->fields['estcla'];
				$arregloSPG[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPG[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPG[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPG[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPG[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPG[$i]['spg_cuenta']=$data->fields['spg_cuenta'];
				$arregloSPG[$i]['procede_doc']= $data->fields['procede_doc'];
				$arregloSPG[$i]['documento']= $data->fields['documento'];
				$arregloSPG[$i]['operacion']= $data->fields['operacion'];
				$arregloSPG[$i]['codfuefin']= $data->fields['codfuefin'];
				$arregloSPG[$i]['monto']=$data->fields['monto'];
				$data->MoveNext();				
			}
		}
		return $arregloSPG;
	}
	
	public function buscarDetalleSCG($codcom,$fecha,$procede,$arrcabecera)
	{
		$arregloSCG=array();
		$cadenasql="SELECT sc_cuenta, procede_doc, documento, debhab, descripcion, monto ".
                   "  FROM scg_dtmp_cmp ".
                   " WHERE codemp='".$this->codemp."' ".
				   "   AND comprobante='".$codcom."' ".
				   "   AND fecha='".$fecha."' ".
                   "   AND procede='".$procede."'";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->valido=false;
            $this->mensaje.=' ERROR->'.$this->conexionBaseDatos->ErrorMsg();		
		}
		else
		{
			$i=0;
			while(!$data->EOF){
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
				$arregloSCG[$i]['debhab']=$data->fields['debhab'];			
				$arregloSCG[$i]['procede_doc']=$data->fields['procede_doc'];			
				$arregloSCG[$i]['monto']=$data->fields['monto'];				
				$arregloSCG[$i]['documento']=$data->fields['documento'];
				$data->MoveNext();
			}
		}
		return $arregloSCG;
	}
	
	public function ContabilizarSPG($objson)
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
			$arrevento['desevetra'] = "Contabilizar la modificaci&#243;n presupuestaria de gasto {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Modificaci&#243;n presupuestaria de gasto contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La modificaci&#243;n presupuestaria de gasto no fue contabilizada, {$this->mensaje} ";
			}
			$this->valido=true;
			$this->mensaje='';
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizar($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
		$procede=$objson->arrDetalle[$j]->procede;
		$fechacom=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		// OBTENGO EL COMPROBANTE PRESUPUESTARIO DE GASTO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND fecha='".$fechacom."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp_md','',$criterio);
		// VERIFICO QUE EL COMPROBANTE PRESUPUESTARIO DE GASTO EXISTA
		if($this->daoComprobante->codcom=='')
		{
			$this->mensaje .= 'ERROR -> No existe el comprobante presupuestario de gasto N�'.$comprobante;
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE APROBACION DEL COMPROBANTE SEA MAYOR O IGUAL A LA FECHA DEL COMPROBANTE
		$fecha=convertirFechaBd($objson->fecapr);
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
			$arrdetallescg=$this->buscarDetalleSCG($comprobante,$this->daoComprobante->fecha,$this->daoComprobante->procede,$arrcabecera);
			if($this->valido)
			{
				$arrdetallespg=$this->buscarDetalleSPG($comprobante,$this->daoComprobante->procede,$this->daoComprobante->fecha,$arrcabecera);
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,null,$arrevento);
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
	
	public function RevContabilizarSPG($objson)
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
			$arrevento['desevetra'] = "Reversar la modificaci&#243;n presupuestaria de gasto {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->Reversar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Modificaci&#243;n presupuestaria de gasto reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La modificaci&#243;n presupuestaria de gasto no fue reversada, {$this->mensaje} ";
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
		// OBTENGO EL COMPROBANTE PRESUPUESTARIO DE GASTO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND fecha='".$fechacom."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp_md','',$criterio);
		// VERIFICO QUE EL COMPROBANTE PRESUPUESTARIO DE GASTO EXISTA
		if($this->daoComprobante->codcom=='')
		{
			$this->mensaje .= 'ERROR -> No existe el comprobante presupuestario de ingreso N�'.$comprobante;
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

	public function buscarCompromisos($sistema,$compromiso,$fecdes,$fechas,$cod_prov)
	{
		$contador = 0;
		$arrDetalles = array();
		$criterio = '';
                $cierre = '';
		if(trim($compromiso) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.comprobante like '%".$compromiso."%'";
                }
		if(trim($cod_prov) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.cod_pro like '%".$cod_prov."%'";
                }
		if(trim($fecdes) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.fecha >= '".convertirFechaBd($fecdes)."'";
                }
		if(trim($fechas) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.fecha <= '".convertirFechaBd($fechas)."'";
                }
		if($sistema == 'SOCCOC') 
		{
                    $criterio .= "   AND sigesp_cmp.procede = 'SOCCOC'";
                    $cierre .= 'SOCCIE';
                }
                else
                {
                    if($sistema == 'SOCCOS') 
                    {
                        $criterio .= "   AND sigesp_cmp.procede = 'SOCCOS'";
                        $cierre .= 'SOCCIE';
                    }
                    else
                    {
                        if($sistema == 'SEPSPC') 
                        {
                            $criterio .= "   AND sigesp_cmp.procede = 'SEPSPC'".
                                         "   AND sigesp_cmp.comprobante IN (SELECT numsol ".
                                         "                                    FROM sep_solicitud ".
                                         "                                   INNER JOIN sep_tiposolicitud ".
                                         "                                      ON sep_solicitud.codemp = sep_tiposolicitud.codemp ".
                                         "                                     AND sep_solicitud.codtipsol = sep_tiposolicitud.codtipsol ".
                                         "                                     AND sep_tiposolicitud.estope='O')";
                            $cierre .= 'SEPCIE';
                        }
                        else
                        {
                            $criterio .= "   AND sigesp_cmp.procede = '------'";
                        }
                    }
                }
                $cadenaSQL= "SELECT sigesp_cmp.comprobante, sigesp_cmp.procede, MAX(sigesp_cmp.fecha) AS fecha,  ".
                            "       MAX(tipo_destino) AS tipo_destino, MAX(nompro) AS nompro, MAX(nombene) AS nombene, ".
                            "       MAX(apebene) AS apebene, MAX(total) AS total, MAX(sigesp_cmp.cod_pro) AS cod_pro, ".
                            "       MAX(sigesp_cmp.ced_bene) AS ced_bene,  ".
                            "       SUM(CASE WHEN spg_dt_cmp.monto is null THEN 0 ELSE spg_dt_cmp.monto END) AS montocierre, ".
                            "       SUM(CASE WHEN cxp_rd_spg.monto is null THEN 0 ELSE cxp_rd_spg.monto END) AS montocausado ".
                            "  FROM sigesp_cmp ".
                            " INNER JOIN rpc_proveedor ".
                            "    ON sigesp_cmp.codemp = rpc_proveedor.codemp ".
                            "   AND sigesp_cmp.cod_pro = rpc_proveedor.cod_pro ".
                            " INNER JOIN rpc_beneficiario ".
                            "    ON sigesp_cmp.codemp = rpc_beneficiario.codemp ".
                            "   AND sigesp_cmp.ced_bene = rpc_beneficiario.ced_bene ".
                            "  LEFT JOIN spg_dt_cmp ".
                            "    ON spg_dt_cmp.procede = '".$cierre."' ".
                            "   AND spg_dt_cmp.procede_doc = '".$sistema."' ".
                            "   AND spg_dt_cmp.operacion = 'CS' ".
                            "   AND sigesp_cmp.codemp = spg_dt_cmp.codemp ".
                            "   AND sigesp_cmp.comprobante = spg_dt_cmp.documento ".
                            "   AND sigesp_cmp.procede = spg_dt_cmp.procede_doc ".
                            "  LEFT JOIN (cxp_rd_spg ".
                            "       INNER JOIN cxp_rd ".
                            "          ON cxp_rd_spg.codemp='".$this->codemp."' ".
                            "         AND cxp_rd_spg.procede_doc='".$sistema."' ".
                            "         AND cxp_rd_spg.numdoccom LIKE '%".$comprobante."%' ".
                            "         AND cxp_rd_spg.codemp=cxp_rd.codemp ".
                            "         AND trim(cxp_rd_spg.numrecdoc) = trim(cxp_rd.numrecdoc) ".
                            "         AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc ".
                            "         AND trim(cxp_rd_spg.ced_bene) = trim(cxp_rd.ced_bene) ".
                            "         AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro ".
                            "         AND cxp_rd.estprodoc<>'A') ".
                            "    ON sigesp_cmp.codemp = cxp_rd_spg.codemp ".
                            "   AND sigesp_cmp.comprobante = cxp_rd_spg.numdoccom ".
                            "   AND sigesp_cmp.procede = cxp_rd_spg.procede_doc ".
                            " WHERE sigesp_cmp.codemp = '".$this->codemp."' ".
                             $criterio.
                            " GROUP BY sigesp_cmp.codemp, sigesp_cmp.comprobante, sigesp_cmp.procede";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
                    $this->valido=false;
                    $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		else
		{
                    while((!$data->EOF)&&($this->valido))
                    {
                        $comprobante = $data->fields['comprobante'];
                        $procede = $data->fields['procede'];
                        $monto =  number_format($data->fields['total'],2,'.','');
                        $montoCausado= number_format($data->fields['montocausado'],2,'.','');
                        $montocierre= number_format(abs($data->fields['montocierre']),2,'.','');
                        $montocierre = number_format(($monto-$montoCausado-$montocierre),2,'.','');
                        if($montocierre>0)
                        {
                            $nombre = $data->fields['nompro'];
                            $codigo = $data->fields['cod_pro'];
                            if($data->fields['tipo_destino']=='B')
                            {
                                $nombre = $data->fields['apebene']." ".$data->fields['nombene']; 
                                $codigo = $data->fields['ced_bene'];
                            }
                            $arrDetalles[$contador]['comprobante'] = $comprobante;
                            $arrDetalles[$contador]['cod_pro'] = $data->fields['cod_pro'];
                            $arrDetalles[$contador]['fecha'] = convertirFecha($data->fields['fecha']);
                            $arrDetalles[$contador]['codigo'] =  utf8_encode($codigo." - ".$nombre);
                            $arrDetalles[$contador]['monto'] = number_format($monto,2,',','.');
                            $arrDetalles[$contador]['montocierre'] = number_format($montocierre,2,',','.');
                            $contador++;
			}
                        $data->MoveNext();
                    }
			unset($data);
		}
		return $arrDetalles;
	}
       
        public function buscarCompromisosCausadosParciales($numcom, $sistema)
	{
		$contador = 0;
		$arrDetalles = array();
		$criterio = '';
                $procederev = '';
		
		if($sistema == 'SOCCOC') 
		{
                    $criterio .= "   AND spg_dt_cmp.procede = 'SOCCOC'".
                                 "   AND spg_dt_cmp.procede_doc = 'SOCCOC'";
                    $procederev = 'SOCCIE';
		}
                else
                {
                    if($sistema == 'SOCCOS') 
                    {
                        $criterio .= "   AND spg_dt_cmp.procede = 'SOCCOS'".
                                     "   AND spg_dt_cmp.procede_doc = 'SOCCOS'";
                        $procederev = 'SOCCIE';
                    }
                    else
                    {
                        if($sistema == 'SEPSPC') 
                        {
                            $criterio .= "   AND spg_dt_cmp.procede = 'SEPSPC'".
                                         "   AND spg_dt_cmp.procede_doc = 'SEPSPC'";
                            $procederev = 'SEPCIE';
                        }
                        else
                        {
                            $criterio .= "   AND spg_dt_cmp.procede = '------'".
                                         "   AND spg_dt_cmp.procede_doc = '------'";
                        }
                    }
                }
		if($numcom != '')
		{
			$criterio .= " AND spg_dt_cmp.comprobante = '".$numcom."'";
		}
		$modalidad=$_SESSION["la_empresa"]["estmodest"];
		
		switch($modalidad)
		{
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(spg_dt_cmp.codestpro1,length(spg_dt_cmp.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(spg_dt_cmp.codestpro2,length(spg_dt_cmp.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(spg_dt_cmp.codestpro3,length(spg_dt_cmp.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
				
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(spg_dt_cmp.codestpro1,length(spg_dt_cmp.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(spg_dt_cmp.codestpro2,length(spg_dt_cmp.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(spg_dt_cmp.codestpro3,length(spg_dt_cmp.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(spg_dt_cmp.codestpro4,length(spg_dt_cmp.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(spg_dt_cmp.codestpro5,length(spg_dt_cmp.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		$codestpro = $this->conexionBaseDatos->Concat('spg_dt_cmp.codestpro1','spg_dt_cmp.codestpro2','spg_dt_cmp.codestpro3','spg_dt_cmp.codestpro4','spg_dt_cmp.codestpro5');		 
		$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , spg_dt_cmp.estcla, spg_dt_cmp.spg_cuenta, spg_dt_cmp.operacion, MAX(spg_dt_cmp.monto) AS montocompromiso, ".
		             "       spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
                             "       (SELECT SUM(CASE WHEN cierre.monto is null THEN 0 ELSE cierre.monto END) FROM spg_dt_cmp AS cierre ".
                             "         WHERE cierre.procede = '".$procederev."' ".
                             "           AND spg_dt_cmp.procede_doc = '".$sistema."' ".
                             "           AND cierre.operacion = 'CS' ".
                             "           AND spg_dt_cmp.codemp = cierre.codemp ".
                             "           AND spg_dt_cmp.comprobante = cierre.documento ".
                             "           AND spg_dt_cmp.procede = cierre.procede_doc ".
                             "           AND spg_dt_cmp.codestpro1 = cierre.codestpro1 ".
                             "           AND spg_dt_cmp.codestpro2 = cierre.codestpro2 ".
                             "           AND spg_dt_cmp.codestpro3 = cierre.codestpro3 ".
                             "           AND spg_dt_cmp.codestpro4 = cierre.codestpro4 ".
                             "           AND spg_dt_cmp.codestpro5 = cierre.codestpro5 ".
                             "           AND spg_dt_cmp.estcla = cierre.estcla ".
                             "           AND spg_dt_cmp.spg_cuenta = cierre.spg_cuenta) AS montocierre, ".
                             "       SUM(CASE WHEN cxp_rd_spg.monto is null THEN 0 ELSE  cxp_rd_spg.monto END) AS montocausado, ".
		             "       MAX(spg_dt_cmp.comprobante) AS comprobante,  MAX(spg_dt_cmp.codfuefin) AS codfuefin, MAX(spg_dt_cmp.codcencos) AS codcencos, ".
                             "       MAX(spg_dt_cmp.procede_doc) AS procede_doc, MAX(spg_dt_cmp.documento) AS documento, MAX(spg_dt_cmp.descripcion) AS descripcion,  ".  
                             "       MAX(spg_dt_cmp.codban) AS codban, MAX(spg_dt_cmp.ctaban) AS ctaban  ".  
		             "  FROM spg_dt_cmp  ".
                             " INNER JOIN sigesp_cmp ".
                             "    ON sigesp_cmp.codemp = spg_dt_cmp.codemp ".
                             "   AND sigesp_cmp.comprobante = spg_dt_cmp.comprobante ".
                             "   AND sigesp_cmp.codban = spg_dt_cmp.codban ".
                             "   AND sigesp_cmp.ctaban = spg_dt_cmp.ctaban ".
                             "  LEFT JOIN (cxp_rd_spg ".
		             "       INNER JOIN cxp_rd ".
                             "          ON cxp_rd.estprodoc<>'A' ".
                             "         AND cxp_rd_spg.codemp=cxp_rd.codemp ".
                             "         AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc".
                             "         AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc".
                             "         AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro".
                             "         AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene)".	
                             "    ON spg_dt_cmp.codemp = cxp_rd_spg.codemp ". 
                             "   AND spg_dt_cmp.comprobante = cxp_rd_spg.numdoccom ". 
                             "   AND spg_dt_cmp.procede = cxp_rd_spg.procede_doc ". 
                             "   AND ".$codestpro." = cxp_rd_spg.codestpro ". 
                             "   AND spg_dt_cmp.estcla = cxp_rd_spg.estcla ". 
                             "   AND spg_dt_cmp.spg_cuenta = cxp_rd_spg.spg_cuenta ". 
                             " WHERE spg_dt_cmp.codemp = '{$this->codemp}' ". 
		             "   AND spg_dt_cmp.operacion='CS'".
                             $criterio.
                             " GROUP BY spg_dt_cmp.codemp,spg_dt_cmp.comprobante,spg_dt_cmp.procede,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2, ".
                             "          spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla,spg_dt_cmp.spg_cuenta, ".
                             "          spg_dt_cmp.operacion, spg_dt_cmp.procede_doc ";
                $data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
                    $this->valido=false;
                    $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		else
		{
                        while((!$data->EOF)&&($this->valido))
			{
                            $montocomprometido = number_format($data->fields['montocompromiso'],2,'.','');
                            $montocausado=number_format($data->fields['montocausado'],2,'.','');
                            $montocierre=number_format(abs($data->fields['montocierre']),2,'.','');
                            $montocierre = number_format(($montocomprometido-$montocausado-$montocierre),2,'.','');
                            if($montocierre>0)
                            {
                                $arrDetalles[$contador]['comprobante'] = $data->fields['comprobante'];
                                $arrDetalles[$contador]['codban'] = $data->fields['codban'];
                                $arrDetalles[$contador]['ctaban'] = $data->fields['ctaban'];
                                $arrDetalles[$contador]['procede'] = $procederev;
                                $arrDetalles[$contador]['estructura'] = $data->fields['estructura'];
                                $arrDetalles[$contador]['codestpro1'] = $data->fields['codestpro1'];
                                $arrDetalles[$contador]['codestpro2'] = $data->fields['codestpro2'];
                                $arrDetalles[$contador]['codestpro3'] = $data->fields['codestpro3'];
                                $arrDetalles[$contador]['codestpro4'] = $data->fields['codestpro4'];
                                $arrDetalles[$contador]['codestpro5'] = $data->fields['codestpro5'];
                                $arrDetalles[$contador]['estcla'] = $data->fields['estcla'];
                                $arrDetalles[$contador]['spg_cuenta'] = $data->fields['spg_cuenta'];
                                $arrDetalles[$contador]['codfuefin'] = $data->fields['codfuefin'];
                                $arrDetalles[$contador]['codcencos'] = $data->fields['codcencos'];
                                $arrDetalles[$contador]['procede_doc'] = $data->fields['procede_doc'];
                                $arrDetalles[$contador]['documento'] = $data->fields['documento'];
                                $arrDetalles[$contador]['operacion'] = $data->fields['operacion'];
                                $arrDetalles[$contador]['descripcion'] = utf8_decode($data->fields['descripcion']);
                                $arrDetalles[$contador]['montocompromiso'] = number_format($montocomprometido,2,',','.');
                                $arrDetalles[$contador]['montocausado'] = number_format($montocausado,2,',','.');
                                $arrDetalles[$contador]['disponible'] = number_format($montocierre,2,',','.');
                                $arrDetalles[$contador]['montocierre'] = number_format($montocierre,2,',','.');
                                $contador++;
                            }
                            $data->MoveNext();
			}  
			unset($data);
		}
		return $arrDetalles;
	}
	
	public function ContabilizarCierreCompromisos($objson)
	{
		$arrRespuesta= array();
		$nSol = 1;
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
                    $comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
                    $arrevento['desevetra'] = "Contabilizar Cierre/Disminucion de Compromiso {$comprobante}, asociado a la empresa {$this->codemp}";
                    if ($this->contabilizarCierre($objson,$arrevento,$j)) 
                    {
                            $nOk++;
                            $arrRespuesta[$h]['estatus'] = 1;
                            $arrRespuesta[$h]['documento'] = $comprobante;
                            $arrRespuesta[$h]['mensaje'] = 'Cierre/Disminucion de Compromiso contabilizado exitosamente';
                    }
                    else 
                    {
                            $nEr++;
                            $arrRespuesta[$h]['estatus'] = 0;
                            $arrRespuesta[$h]['documento'] = $comprobante;
                            $arrRespuesta[$h]['mensaje'] = "El Cierre/Disminucion de Compromiso no fue contabilizado, {$this->mensaje} ";
                    }
                    $this->valido=true;
                    $this->mensaje='';
                    $h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarCierre($objson,$arrevento,$j)
	{
            DaoGenerico::iniciarTrans();  	
            $arrcabecera=array();
            $arrdetallespg=array();
            $comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
            $procede=$objson->arrDetalle[$j]->procede_doc;
            $codban=$objson->arrDetalle[$j]->codban;
            $ctaban=$objson->arrDetalle[$j]->ctaban;
            // OBTENGO EL COMPROBANTE  A CONTABILIZAR
            $criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND codban='".$codban."' AND ctaban='".$ctaban."'";
            $this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);
            // VERIFICO QUE EL COMPROBANTE  EXISTA
            if($this->daoComprobante->codcom=='')
            {
                    $this->mensaje .= 'ERROR -> No existe el comprobante Nro '.$comprobante.' - '.$procede;
                    $this->valido = false;			
            }		
            // VERIFICO QUE LA FECHA DE APROBACION DEL COMPROBANTE SEA MAYOR O IGUAL A LA FECHA DEL COMPROBANTE
            $fecha=convertirFechaBd($objson->feccie);
            if(!compararFecha($this->daoComprobante->fecha,$fecha))
            {
                $this->mensaje .= 'ERROR -> La Fecha de Cierre/Disminucion '.$fecha.' es menor que la fecha del Comprobante '.$this->daoComprobante->fecha;
                $this->valido = false;			
            }
            $procede_doc=$this->daoComprobante->procede;
            if(($procede_doc=='SOCCOS')||($procede_doc=='SOCCOC'))
            {
                    $procede='SOCCIE';
            }
            if($procede_doc=='SEPSPC')
            {
                    $procede='SEPCIE';
            }
            $comprobantecierre=$this->buscarConsecutivoCierre($procede);
            $criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobantecierre."' AND procede='".$procede."' AND codban='".$codban."' AND ctaban='".$ctaban."'";
            $this->daoComprobantecierre = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);
            // VERIFICO QUE EL COMPROBANTE NO  EXISTA
            if($this->daoComprobantecierre->codcom<>'')
            {
                $comprobantecierre=$this->buscarConsecutivoCierre($procede);		
                $criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobantecierre."' AND procede='".$procede."' AND codban='".$codban."' AND ctaban='".$ctaban."'";
                $this->daoComprobantecierre = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);
                // VERIFICO QUE EL COMPROBANTE NO  EXISTA
                if($this->daoComprobantecierre->codcom<>'')
                {
                    $comprobantecierre=$this->buscarConsecutivoCierre($procede);		
                }		
            }
            unset($this->daoComprobantecierre);
            if ($this->valido)
            {
                    $arrcabecera['codemp'] = $this->codemp;
                    $arrcabecera['procede'] = $procede;
                    $arrcabecera['comprobante'] = $comprobantecierre;
                    $arrcabecera['codban'] = $this->daoComprobante->codban;
                    $arrcabecera['ctaban'] = $this->daoComprobante->ctaban;
                    $arrcabecera['fecha'] = $fecha;
                    $arrcabecera['descripcion'] = 'CIERRE/DISMINUCION DE COMPROMISO '.$this->daoComprobante->descripcion;
                    $arrcabecera['tipo_comp'] = $this->daoComprobante->tipo_comp;
                    $arrcabecera['tipo_destino'] = $this->daoComprobante->tipo_destino;
                    $arrcabecera['cod_pro'] = $this->daoComprobante->cod_pro;
                    $arrcabecera['ced_bene'] = $this->daoComprobante->ced_bene;
                    $arrcabecera['numpolcon'] = $this->daoComprobante->numpolcon;
                    $arrcabecera['esttrfcmp'] = $this->daoComprobante->esttrfcmp;
                    $arrcabecera['estrenfon'] = $this->daoComprobante->estrenfon;
                    $arrcabecera['codfuefin'] = $this->daoComprobante->codfuefin;
                    $arrcabecera['codusu'] = $_SESSION['la_logusr'];
                    $nrodetalle=count((array)$objson->arrDetalle);
                    $contador=1;
                    for ($i=0;$i<$nrodetalle;$i++)
                    {
                        $arrdetallespg[$contador]['codemp']=$arrcabecera['codemp'];
                        $arrdetallespg[$contador]['procede']= $arrcabecera['procede'];
                        $arrdetallespg[$contador]['comprobante']= $arrcabecera['comprobante'];
                        $arrdetallespg[$contador]['codban']= $arrcabecera['codban'];
                        $arrdetallespg[$contador]['ctaban']= $arrcabecera['ctaban'];
                        $arrdetallespg[$contador]['fecha']= $arrcabecera['fecha'];
                        $arrdetallespg[$contador]['descripcion']=$objson->arrDetalle[$i]->descripcion;
                        $arrdetallespg[$contador]['orden']= $i;
                        $arrdetallespg[$contador]['estcla']=$objson->arrDetalle[$i]->estcla;
                        $arrdetallespg[$contador]['codestpro1']=$objson->arrDetalle[$i]->codestpro1;
                        $arrdetallespg[$contador]['codestpro2']=$objson->arrDetalle[$i]->codestpro2;
                        $arrdetallespg[$contador]['codestpro3']=$objson->arrDetalle[$i]->codestpro3;
                        $arrdetallespg[$contador]['codestpro4']=$objson->arrDetalle[$i]->codestpro4;
                        $arrdetallespg[$contador]['codestpro5']=$objson->arrDetalle[$i]->codestpro5;
                        $arrdetallespg[$contador]['spg_cuenta']=$objson->arrDetalle[$i]->spg_cuenta;
                        $arrdetallespg[$contador]['procede_doc']= $objson->arrDetalle[$i]->procede_doc;
                        $arrdetallespg[$contador]['documento']= $objson->arrDetalle[$i]->documento;
                        $arrdetallespg[$contador]['operacion']= $objson->arrDetalle[$i]->operacion;
                        $arrdetallespg[$contador]['codfuefin']= $objson->arrDetalle[$i]->codfuefin;
                        $arrdetallespg[$contador]['monto']=$objson->arrDetalle[$i]->monto*(-1);
                        $monto = $monto + $objson->arrDetalle[$i]->monto;
                        $contador++;
                    }
                    $arrcabecera['total'] = $monto*(-1);
            }
            if ($this->valido)
            {
                    $serviciocomprobante = new ServicioComprobante();
                    $this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
                    $this->mensaje .= $serviciocomprobante->mensaje;
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

	public function buscarCierreDisminuciones($sistema,$compromiso,$fecdes,$fechas,$cod_prov)
	{
		$contador = 0;
		$arrDetalles = array();
		$criterio = '';
                
		if(trim($compromiso) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.comprobante like '%".$compromiso."%'";
                }
		if(trim($cod_prov) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.cod_pro like '%".$cod_prov."%'";
                }
		if(trim($fecdes) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.fecha >= '".convertirFechaBd($fecdes)."'";
                }
		if(trim($fechas) <> '') 
		{
                    $criterio .= "   AND sigesp_cmp.fecha <= '".convertirFechaBd($fechas)."'";
                }
		if($sistema == 'SOCCIE') 
		{
                    $criterio .= "   AND sigesp_cmp.procede = 'SOCCIE'";
                }
                else
                {
                    if($sistema == 'SEPCIE') 
                    {
                        $criterio .= "   AND sigesp_cmp.procede = 'SEPCIE'";
                    }
                    else
                    {
                        $criterio .= "   AND sigesp_cmp.procede = '------'";
                    }
                }
                $cadenaSQL= "SELECT sigesp_cmp.comprobante, sigesp_cmp.procede, MAX(sigesp_cmp.fecha) AS fecha,  ".
                            "       MAX(tipo_destino) AS tipo_destino, MAX(nompro) AS nompro, MAX(nombene) AS nombene, ".
                            "       MAX(apebene) AS apebene, MAX(total) AS total, MAX(sigesp_cmp.cod_pro) AS cod_pro, ".
                            "       MAX(sigesp_cmp.ced_bene) AS ced_bene  ".
                            "  FROM sigesp_cmp ".
                            " INNER JOIN rpc_proveedor ".
                            "    ON sigesp_cmp.codemp = rpc_proveedor.codemp ".
                            "   AND sigesp_cmp.cod_pro = rpc_proveedor.cod_pro ".
                            " INNER JOIN rpc_beneficiario ".
                            "    ON sigesp_cmp.codemp = rpc_beneficiario.codemp ".
                            "   AND sigesp_cmp.ced_bene = rpc_beneficiario.ced_bene ".
                            " WHERE sigesp_cmp.codemp = '".$this->codemp."' ".
                             $criterio.
                            " GROUP BY sigesp_cmp.codemp, sigesp_cmp.comprobante, sigesp_cmp.procede";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
                    $this->valido=false;
                    $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		else
		{
                    while((!$data->EOF)&&($this->valido))
                    {
                        $comprobante = $data->fields['comprobante'];
                        $monto = $data->fields['total'];
                        $nombre = $data->fields['nompro'];
                        $codigo = $data->fields['cod_pro'];
                        if($data->fields['tipo_destino']=='B')
                        {
                            $nombre = $data->fields['apebene']." ".$data->fields['nombene']; 
                            $codigo = $data->fields['ced_bene'];
                        }
                        $arrDetalles[$contador]['comprobante'] = $comprobante;
                        $arrDetalles[$contador]['cod_pro'] = $data->fields['cod_pro'];
                        $arrDetalles[$contador]['fecha'] = convertirFecha($data->fields['fecha']);
                        $arrDetalles[$contador]['codigo'] =  utf8_encode($codigo." - ".$nombre);
                        $arrDetalles[$contador]['monto'] = number_format($monto,2,',','.');
                        $contador++;
                        $data->MoveNext();
                    }
			unset($data);
		}
		return $arrDetalles;
	}
            
	public function buscarReversoCierreCompromiso($numcom, $sistema)
	{
		$contador = 0;
		$arrDetalles = array();
		$criterio = '';
		
		if(($sistema == '---')||($sistema == '')) 
		{
			$criterio .= " AND procede IN ('SOCCIE','SEPCIE')";
		}
		if($sistema == 'SOCCIE') 
		{
			$criterio .= " AND procede IN ('SOCCIE')";
		}
		if($sistema == 'SEPCIE') 
		{
			$criterio .= " AND procede IN ('SEPCIE')";
		}
		if($numcom != '')
		{
			$criterio .= " AND comprobante = '".$numcom."'";
		}
		$cadenaSQL= "SELECT comprobante, fecha, procede, descripcion, tipo_destino, nompro, nombene, apebene, total, sigesp_cmp.cod_pro, sigesp_cmp.ced_bene ".
                            "  FROM sigesp_cmp ".
                            " INNER JOIN rpc_proveedor ".
                            "    ON sigesp_cmp.codemp = rpc_proveedor.codemp ".
                            "   AND sigesp_cmp.cod_pro = rpc_proveedor.cod_pro ".
                            " INNER JOIN rpc_beneficiario ".
                            "    ON sigesp_cmp.codemp = rpc_beneficiario.codemp ".
                            "   AND sigesp_cmp.ced_bene = rpc_beneficiario.ced_bene ".
                            " WHERE sigesp_cmp.codemp = '".$this->codemp."' ".
                            $criterio;
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
                    $this->valido=false;
                    $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
		}
		else
		{
			while((!$data->EOF)&&($this->valido))
			{
				$comprobante = $data->fields['comprobante'];
				$procede = $data->fields['procede'];
				$monto = $data->fields['total'];
				$nombre = $data->fields['nompro'];
				$codigo = $data->fields['cod_pro'];
				if($data->fields['tipo_destino']=='B')
				{
					$nombre = $data->fields['apebene']." ".$data->fields['nombene']; 
					$codigo = $data->fields['ced_bene'];
				}
				$arrDetalles[$contador]['comprobante'] = $comprobante;
				$arrDetalles[$contador]['fecha'] = convertirFecha($data->fields['fecha']);
				$arrDetalles[$contador]['descripcion'] = utf8_encode($data->fields['descripcion']);
				$arrDetalles[$contador]['procede'] = $procede;
				$arrDetalles[$contador]['tipproben'] = $data->fields['tipo_destino'];
				$arrDetalles[$contador]['codigo'] = $codigo;
				$arrDetalles[$contador]['nombre'] = utf8_encode($nombre);
				$arrDetalles[$contador]['monto'] = number_format($monto,2,',','.');
				$contador++;
				$data->MoveNext();
			}  
			unset($data);
		}
		return $arrDetalles;
	}

	public function RevContabilizarCierre($objson)
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
			$arrevento['desevetra'] = "Reversar el Cierre de Compromiso {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->ReversarCierre($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'El Cierre de Compromiso reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Cierre de Compromiso no fue reversado, {$this->mensaje} ";
			}
			$this->valido=true;
			$this->mensaje='';
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function ReversarCierre($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
		$procede=$objson->arrDetalle[$j]->procede;
		$fechacom=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		// OBTENGO EL COMPROBANTE PRESUPUESTARIO DE GASTO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND comprobante='".$comprobante."' AND procede='".$procede."' AND fecha='".$fechacom."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp','',$criterio);
		// VERIFICO QUE EL COMPROBANTE PRESUPUESTARIO DE GASTO EXISTA
		if($this->daoComprobante->codcom=='')
		{
			$this->mensaje .= 'ERROR -> No existe el comprobante N�'.$comprobante;
			$this->valido = false;			
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = $this->daoComprobante->procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = $this->daoComprobante->codban;
			$arrcabecera['ctaban'] = $this->daoComprobante->ctaban;
			$arrcabecera['fecha'] = $this->daoComprobante->fecha;
			$arrcabecera['descripcion'] = $this->daoComprobante->descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoComprobante->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoComprobante->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoComprobante->ced_bene;
			$arrcabecera['total'] = number_format($this->daoComprobante->total,2,'.','');
			$arrcabecera['numpolcon'] = $this->daoComprobante->numpolcon;
			$arrcabecera['esttrfcmp'] = $this->daoComprobante->esttrfcmp;
			$arrcabecera['estrenfon'] = $this->daoComprobante->numpolcon;
			$arrcabecera['codfuefin'] = $this->daoComprobante->estrenfon;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
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


	public function buscarConsecutivoCierre($procede)
	{
            $cadenaSQL = "SELECT comprobante ".
                         "  FROM sigesp_cmp ". 
                         " WHERE codemp = '{$this->codemp}' ". 
                         "   AND procede = '".$procede."' ".  
                         " ORDER BY comprobante DESC ";
            $data = $this->conexionBaseDatos->Execute($cadenaSQL);
            if($data===false)
            {
                    $this->valido=false;
                    $this->mensaje.='  ->'.$this->conexionBaseDatos->ErrorMsg();			
            }
            else
            {
                if ($data->EOF)
                {
                    $comprobante=1;
                }
                else
                {
                    $comprobante = substr($data->fields['comprobante'],6,9);
                    $comprobante = intval($comprobante)+1;
                }
            }
            $comprobante = str_pad($comprobante,9,'0',0);
            $comprobante = $procede.$comprobante;
            return $comprobante;
	}
        
}
?>