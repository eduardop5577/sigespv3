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
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($dirsrv.'/modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');


class ServicioIntSigGes
{
	public  $mensaje; 
	private $conexionBaseDatos; 
	private $conexionAlterna;
	private $servicioComprobante;
	private $daoConfintsigges;
	private $daoComprobante;
	private $arrDetalleConf;
		
	public function __construct($numcon)
	{
		$this->mensaje             = null;
		$this->conexionBaseDatos   = null;
		$this->conexionAlterna     = null;
		$this->servicioComprobante = null;
		$this->daoConfintsigges    = null;
		$this->daoComprobante      = null;
		$this->arrDetalleConf      = array();
		$this->cargarDataConfig($numcon);
	}
	
	public function cargarDataConfig ($numcon) {
		//cargando cabecerea configuracion
		$cadenaPk = "numcon = '{$numcon}'";
		$this->daoConfintsigges = FabricaDao::CrearDAO('C', 'mis_confintsigges', array(), $cadenaPk);
		
		//cargando detalle configuracion
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT * FROM mis_dt_confinsigges
						WHERE numcon = '{$numcon}'";
		$dataCuentas = $this->conexionBaseDatos->Execute($cadenaSQL);
		$this->arrDetalleConf = $dataCuentas->GetArray();
	}
	
	public function buscarComprobante($numcon, $fecha) {
		$dataComprobante = null;
		if ($this->daoConfintsigges->baslec == 'G') {
			$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
			$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
																		  $arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
			$filtroCuenta = '';
			foreach($this->arrDetalleConf as $dataCuenta) {
				$operacion = 'D';
				if ($dataCuenta['codopo'] == 'H') {
					$operacion = 'A';
				}
				
				if (empty($filtroCuenta)) {
					$filtroCuenta .= "((COD_CUENTA = '{$dataCuenta['cueori']}' AND TIPO_TRANSACCION = '{$operacion}')";
				}
				else {
					$filtroCuenta .= " OR (COD_CUENTA = '{$dataCuenta['cueori']}' AND TIPO_TRANSACCION = '{$operacion}')";
				}
			}
			
			if (!empty($filtroCuenta)) {
				$filtroCuenta .= ")";
			}
			//$this->conexionAlterna->debug = true;
			$objFecha = new DateTime($fecha);
			$fechadesde = $objFecha->format('d-m-y');
			$fechahasta = $objFecha->format('d-m-y');
			//$fechadesde = $this->formatoFecha($fecha).' 00:00:00';
			//$fechahasta = $this->formatoFecha($fecha).' 23:59:59';
			$cadenaSQL = "SELECT ASI.NUM_ASIENTO AS numcom, ASI.FECHA_INCLUSION AS feccom, ASI.DESCRIPCION AS descom,
								 ASI.COD_EMPRESA AS codemp
							FROM GESTOR.CG_ASIENTOS ASI
							INNER JOIN GESTOR.CG_DETALLES_ASIENTOS DT ON ASI.COD_EMPRESA=DT.COD_EMPRESA 
					                                           AND ASI.COD_AGENCIA= DT.COD_AGENCIA AND ASI.NUM_ASIENTO=DT.NUM_ASIENTO
							WHERE ASI.COD_AGENCIA = '{$this->daoConfintsigges->codfon}' AND ASI.FECHA_INCLUSION BETWEEN '{$fechadesde}' AND '{$fechahasta}'
			 				  AND (ASI.PROCSIGESP IS NULL OR ASI.PROCSIGESP = 'N') AND {$filtroCuenta}
							GROUP BY ASI.NUM_ASIENTO, ASI.FECHA_INCLUSION, ASI.DESCRIPCION, ASI.COD_EMPRESA, ASI.COD_AGENCIA
							ORDER BY ASI.NUM_ASIENTO";
			$dataComprobante = $this->conexionAlterna->Execute($cadenaSQL);
		}
		else if ($this->daoConfintsigges->baslec == 'S') {
			$filtroCuenta = '';
			foreach($this->arrDetalleConf as $dataCuenta) {
				if (empty($filtroCuenta)) {
					$filtroCuenta .= "((sc_cuenta = '{$dataCuenta['cueori']}' AND debhab = '{$dataCuenta['codopo']}')";
				}
				else {
					$filtroCuenta .= " OR (sc_cuenta = '{$dataCuenta['cueori']}' AND debhab = '{$dataCuenta['codopo']}')";
				}
			}
			
			if (!empty($filtroCuenta)) {
				$filtroCuenta .= ")";
			}
			
			$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
			$cadenaSQL  = "SELECT CMP.codemp, CMP.comprobante AS numcom, CMP.fecha AS feccom, CMP.descripcion AS descom,
								  CMP.procede AS procom, CMP.codban, CMP.ctaban
								FROM sigesp_cmp CMP
								INNER JOIN scg_dt_cmp DT ON CMP.codemp = DT.codemp  AND CMP.procede = DT.procede 
                         								AND CMP.comprobante = DT.comprobante AND CMP.codban = DT.codban AND CMP.ctaban = DT.ctaban
								WHERE  CMP.fecha = '{$fecha}' AND (CMP.procsigesp IS NULL OR CMP.procsigesp = 'N') AND {$filtroCuenta}  
								GROUP BY CMP.comprobante, CMP.fecha, CMP.descripcion, CMP.codemp, CMP.procede, CMP.codban, CMP.ctaban
								ORDER BY CMP.comprobante";
			$dataComprobante = $this->conexionBaseDatos->Execute($cadenaSQL);
		}

		return $dataComprobante;
	}
	
	public function obtenerComprobante($objJson) {
		$dataComprobante = null;
		if ($this->daoConfintsigges->baslec == 'G') {
			$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
			$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
					$arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
			
			
			$cadenaSQL = "SELECT ASI.NUM_ASIENTO AS numcom, TO_CHAR(ASI.FECHA_INCLUSION, 'YYYY-MM-DD') AS feccom, ASI.DESCRIPCION AS descom,
								 DT.COD_CUENTA AS codcue, DT.TIPO_TRANSACCION AS codope, DT.VALOR_TRANSACCION AS monto,
								 DT.DETALLE AS desdet, AG.nombre
							FROM GESTOR.CG_ASIENTOS ASI
							INNER JOIN GESTOR.CG_DETALLES_ASIENTOS DT ON ASI.COD_EMPRESA=DT.COD_EMPRESA 
					                                           AND ASI.COD_AGENCIA= DT.COD_AGENCIA AND ASI.NUM_ASIENTO=DT.NUM_ASIENTO 
					        INNER JOIN GESTOR.CG_AGENCIAS AG ON ASI.COD_EMPRESA=AG.COD_EMPRESA AND ASI.COD_AGENCIA= AG.COD_AGENCIA
							WHERE ASI.COD_EMPRESA = '{$objJson->codemp}' AND ASI.COD_AGENCIA = '{$this->daoConfintsigges->codfon}' 
							  AND ASI.NUM_ASIENTO = '{$objJson->numcom}' ";
			$dataComprobante = $this->conexionAlterna->Execute($cadenaSQL);
		}
		else if ($this->daoConfintsigges->baslec == 'S') {
			$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
			$cadenaSQL  = "SELECT CMP.codemp, CMP.comprobante AS numcom, CMP.fecha AS feccom, CMP.descripcion AS descom,
								  DT.sc_cuenta AS codcue, DT.debhab AS codope, DT.monto AS monto, DT.descripcion AS desdet
								FROM sigesp_cmp CMP
								INNER JOIN scg_dt_cmp DT ON CMP.codemp = DT.codemp  AND CMP.procede = DT.procede 
                         								AND CMP.comprobante = DT.comprobante AND CMP.codban = DT.codban AND CMP.ctaban = DT.ctaban
								WHERE CMP.codemp = '{$objJson->codemp}' AND CMP.procede='{$objJson->procom}' 
								  AND CMP.comprobante = '{$objJson->numcom}' AND CMP.codban='{$objJson->codban}'
								  AND CMP.ctaban='{$objJson->ctaban}'";
			$dataComprobante = $this->conexionBaseDatos->Execute($cadenaSQL);
		}

		return $dataComprobante;
	}
	
	public function generarCompSIG($numcom, $feccom, $rsComprobante) {
		$respuesta = true;
		
		if (empty($numcom)) {
			$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
			$arrFiltro = array('procede'=>'SCGCMP');
			$numComprobante = $this->daoComprobante->buscarCodigo('comprobante',false,15,$arrFiltro,'','','');
		}
		
		if (empty($feccom)) {
			$feccom = $rsComprobante->fields['FECCOM'];
		}
		else {
			$objFecha = new DateTime($feccom);
			$feccom = $objFecha->format('Y-m-d');
		}
		
		$descripcion = 'Fondo :'.$rsComprobante->fields['NOMBRE'].', '.$rsComprobante->fields['DESCOM'];
		$this->servicioComprobante = new ServicioComprobante();
		$arrcabecera['codemp']       = $_SESSION['la_empresa']['codemp'];
		$arrcabecera['procede']      = 'SCGCMP';//procede de contabilidad o uno nuevo
		$arrcabecera['comprobante']  = $numComprobante;
		$arrcabecera['codban']       = '---';
		$arrcabecera['ctaban']       = '-------------------------';
		$arrcabecera['fecha']        = $feccom;
		$arrcabecera['descripcion']  = $descripcion;
		$arrcabecera['tipo_comp']    = 2;
		$arrcabecera['tipo_destino'] = '-';
		$arrcabecera['cod_pro']      = '----------';
		$arrcabecera['ced_bene']     = '----------';
		$arrcabecera['total']        = 0;
		$arrcabecera['numpolcon']    = 0;
		$arrcabecera['esttrfcmp']    = 0;
		$arrcabecera['estrenfon']    = 0;
		$arrcabecera['codfuefin']    = '--';
		$arrcabecera['codusu']       = $_SESSION['la_logusr'];
		
		$detalleOk = true;
		$documento = $rsComprobante->fields['NUMCOM'];
		if ($this->daoConfintsigges->obvcue == '1'  ) {
			while(!$rsComprobante->EOF){
				foreach($this->arrDetalleConf as $dataCuenta) {
					if ($dataCuenta['cueori'] == $rsComprobante->fields['CODCUE']) {
						$i++;
						$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
						$arregloSCG[$i]['procede']= $arrcabecera['procede'];
						$arregloSCG[$i]['comprobante']= str_pad($arrcabecera['comprobante'],15,0,0);
						$arregloSCG[$i]['codban']= $arrcabecera['codban'];
						$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
						$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
						$arregloSCG[$i]['descripcion']= $descripcion;
						$arregloSCG[$i]['orden']= $i;
						$arregloSCG[$i]['sc_cuenta']=$dataCuenta['cuedes'];
						$arregloSCG[$i]['debhab']=$dataCuenta['codopd'];
						$arregloSCG[$i]['procede_doc']=$arrcabecera['procede'];
						$arregloSCG[$i]['monto']=number_format($rsComprobante->fields['MONTO'],2,'.','');
						$arregloSCG[$i]['documento']=$documento;
					}
				}
					
				$rsComprobante->MoveNext();
			}
		}
		else {
			while(!$rsComprobante->EOF){
				$i++;
				$arrCueDes = array();
				foreach($this->arrDetalleConf as $dataCuenta) {
					if ($dataCuenta['cueori'] == $rsComprobante->fields['CODCUE']) {
						$arrCueDes = $dataCuenta;
						break;
					}
				}
					
				if (empty($arrCueDes)) {
					$detalleOk = false;
					break;
				}
				else {
					$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
					$arregloSCG[$i]['procede']= $arrcabecera['procede'];
					$arregloSCG[$i]['comprobante']= str_pad($arrcabecera['comprobante'],15,0,0);
					$arregloSCG[$i]['codban']= $arrcabecera['codban'];
					$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
					$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
					$arregloSCG[$i]['descripcion']= $descripcion;
					$arregloSCG[$i]['orden']= $i;
					$arregloSCG[$i]['sc_cuenta']=$arrCueDes['cuedes'];
					$arregloSCG[$i]['debhab']=$arrCueDes['codopd'];
					$arregloSCG[$i]['procede_doc']=$arrcabecera['procede'];
					$arregloSCG[$i]['monto']=number_format($rsComprobante->fields['MONTO'],2,'.','');
					$arregloSCG[$i]['documento']=$documento;
				}
					
				$rsComprobante->MoveNext();
			}
		}
		unset($rsComprobante);
		
		if ($detalleOk) {
			$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
			$arrevento['codusu']    = $_SESSION['la_logusr'];
			$arrevento['codsis']    = 'MIS';
			$arrevento['evento']    = 'PROCESAR';
			$arrevento['nomfisico'] = 'sigesp_vis_mis_intsigges.html';
			$respuesta = $this->servicioComprobante->guardarComprobante($arrcabecera,null,$arregloSCG,null,$arrevento);
			if (!$respuesta){
				$this->mensaje = $this->servicioComprobante->mensaje;
			}
		}
		else {
			$respuesta = false;
			$this->mensaje = "El comprobante contiene una cuenta que no fue configurada en las reglas de la interfaz";
		}
		
		
		return $respuesta;
	}
	
	public function obtenerMovDestino($numcom, $feccom, $rsComprobante) {
		$arrRespuesta = array();
		
		if (empty($numcom)) {
			$numcom = substr($rsComprobante->fields['NUMCOM'], -12);
		}
		
		if (empty($feccom)) {
			$feccom = $rsComprobante->fields['FECCOM'];
			$objFecha = new DateTime($feccom);
			$feccom = $objFecha->format('Y-m-d');
		}
		else {
			$objFecha = new DateTime($feccom);
			$feccom = $objFecha->format('Y-m-d');
		}
		
		//DATA CABECERA
		$arrCabecera['numdoc'] = substr($this->daoConfintsigges->codfon, -3).substr($numcom, -12);
		$arrCabecera['feccom'] = $feccom;
		$arrCabecera['descom'] = $rsComprobante->fields['DESCOM'];
		
		//DATA DETALLE
		$i = 0;
		$arrMovDestino = array();
		while(!$rsComprobante->EOF) {
			$arrCueDes = array();
			foreach($this->arrDetalleConf as $dataCuenta) {
				if ($dataCuenta['cueori'] == $rsComprobante->fields['CODCUE']) {
					$arrCueDes = $dataCuenta;
					break;
				}
			}
			
			if (empty($arrCueDes)) {
				$arrMovDestino = array();
				break;
			}
			$arrMovDestino[$i]['cuenta']=$arrCueDes['cuedes'];
			$arrMovDestino[$i]['codope']=$arrCueDes['codopd'];
			$arrMovDestino[$i]['monto']=number_format($rsComprobante->fields['MONTO'],2,'.','');
			$arrMovDestino[$i]['desdet']=$rsComprobante->fields['DESCOM'];
			
			$i++;
			$rsComprobante->MoveNext();
		}
		unset($rsComprobante);
		
		$arrRespuesta[0] = $arrCabecera;
		$arrRespuesta[1] = $arrMovDestino;
		
		return $arrRespuesta;
	}
	
	public function obtenerBancoCuenta ($arrMovDestino) {
		
		foreach ($arrMovDestino as $movimiento) {
			$cuenta = '';
			switch ($this->daoConfintsigges->movban) {
				case 'NC':
					if ($movimiento['codope'] == 'D' ) {
						$cuenta = $movimiento['cuenta'];
					}
					break;
				
				case 'ND':
					if ($movimiento['codope'] == 'H' ) {
						$cuenta = $movimiento['cuenta'];
					}
					break;
			}
			
			$cadenaSQL = "SELECT codban, ctaban FROM scb_ctabanco WHERE sc_cuenta = '{$cuenta}'";
			$dataBancoCuenta = $this->conexionBaseDatos->Execute($cadenaSQL);
			if (!$dataBancoCuenta->EOF) {
				break;
			}
		}
		
		return $dataBancoCuenta;
	}
	
	public function generarMovBanSIG($numcom, $feccom, $rsComprobante) {
		$respuesta = true;
		$arrMovDes = $this->obtenerMovDestino($numcom, $feccom, $rsComprobante);
		if (!empty($arrMovDes[1])) {
			$dataBancoCuenta = $this->obtenerBancoCuenta($arrMovDes[1]);
			if (!$dataBancoCuenta->EOF) {
				$servicioBancario = new ServicioMovimientoScb();
				$arrCabeceraScb["codemp"]	 = $_SESSION['la_empresa']['codemp'];
				$arrCabeceraScb["codban"]	 = $dataBancoCuenta->fields['codban'];
				$arrCabeceraScb["ctaban"]	 = $dataBancoCuenta->fields['ctaban'];
				$arrCabeceraScb["numdoc"]	 = $arrMovDes[0]['numdoc'];
				$arrCabeceraScb["codope"]	 = $this->daoConfintsigges->movban;
				$arrCabeceraScb["fecmov"]	 = $arrMovDes[0]['feccom'];
				$arrCabeceraScb["conmov"]	 = $arrMovDes[0]['descom'];
				$arrCabeceraScb["codconmov"] = '---';
				$arrCabeceraScb["cod_pro"]	 = "----------";
				$arrCabeceraScb["ced_bene"]	 = "----------";
				$arrCabeceraScb["nomproben"] = "-";
				$arrCabeceraScb["monret"]	 = 0;
				$arrCabeceraScb["chevau"]	 = '';
				$arrCabeceraScb["estmov"]	 = 'N';
				$arrCabeceraScb["estmovint"] = 0;
				$arrCabeceraScb["estcobing"] = 0;
				$arrCabeceraScb["estbpd"]	 = 'T';
				$arrCabeceraScb["procede"]	 = 'SCBCOR';
				$arrCabeceraScb["estreglib"] = "";
				$arrCabeceraScb["tipo_destino"]	 = '-';
				$arrCabeceraScb["numordpagmin"]	 = '-';
				$arrCabeceraScb["codfuefin"] = '--';
				$arrCabeceraScb["codtipfon"] = '';
				$arrCabeceraScb["estmovcob"] = 0;
				$arrCabeceraScb["numconint"] = '';
				$arrCabeceraScb["tranoreglib"] = 0;
				$arrCabeceraScb["numchequera"] = "";
				$arrCabeceraScb["codbansig"] = "";
				
				$j = 0;
				$monTot = 0;
				foreach ($arrMovDes[1] as $movimiento) {
					if ($movimiento['codope'] == 'D') {
						$monTot = $monTot + $movimiento['monto'];
					}
					$arrDetalleScg["scg_cuenta"][$j]  = $movimiento['cuenta'];
					$arrDetalleScg["desmov"][$j]	  = $movimiento['desdet'];
					$arrDetalleScg["debhab"][$j]	  = $movimiento['codope'];
					$arrDetalleScg["monto"][$j]	 	  = $movimiento['monto'];
					$arrDetalleScg["documento"][$j]	  = $arrMovDes[0]['numdoc'];
					$arrDetalleScg["procede_doc"][$j] = 'SCBMOV';
					$arrDetalleScg["monobjret"][$j]	  = $movimiento['monto'];
					$arrDetalleScg["codded"][$j]	  = '0000';
					$j++;
				}
				
				$arrCabeceraScb["monto"]	 = $monTot;
				$arrCabeceraScb["monobjret"] = $monTot;
				$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
				$arrevento['codusu']    = $_SESSION['la_logusr'];
				$arrevento['codsis']    = 'MIS';
				$arrevento['evento']    = 'PROCESAR';
				$arrevento['nomfisico'] = 'sigesp_vis_mis_intsigges.html';
				$arrevento['desevetra'] = "Inserto el movimiento de banco {$arrMovDes[0]['numdoc']}, asociado a la empresa ".$_SESSION['la_empresa']['codemp'];
				$respuesta = $servicioBancario->GuardarAutomatico($arrCabeceraScb,$arrDetalleScg,null,null,$arrevento);
				$this->mensaje.= $servicioBancario->mensaje;
			}
			else {
				$respuesta = false;
				$this->mensaje.= 'El comprobante no contiene una cuenta asociada con una cuenta de banco';
			}
			
			unset($arrMovDes);
			unset($dataBancoCuenta);
		}
		else {
			$respuesta = false;
			$this->mensaje.= 'El comprobante contiene una cuenta que no fue configurada en las reglas de la interfaz';
		}
		
		return $respuesta;
	}
	
	public function obtenerSecuencia() {
		$secuencia = 0;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
																	  $arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
		$cadenaSQL = "SELECT MAX(SECUENCIAL) AS secuencia 
						FROM GESTOR.CG_ASIENTOS";
		$rsData = $this->conexionAlterna->Execute($cadenaSQL);
		if (!$rsData->EOF) {
			$secuencia = $rsData->fields['SECUENCIA'];
			$secuencia = $secuencia++;
		}
		unset($rsData);
		
		return $secuencia;
	}
	
	public function generarCompGES($numcom, $feccom, $rsComprobante) {
		$respuesta = true;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
				                                                      $arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
		if (empty($numcom)) {
			$numcom = substr($rsComprobante->fields['numcom'], -12);
		}
		else {
			$numcom = substr($numcom, -12);
		}
		
		if (empty($feccom)) {
			$feccom = $rsComprobante->fields['feccom'];
			$objFecha = new DateTime($feccom);
			$feccom = $objFecha->format('d/m/Y H:i:s');
		}
		else {
			$objFecha = new DateTime($feccom);
			$feccom = $objFecha->format('d/m/Y H:i:s');
		}
		$codusuario = 'SIGESP';
		$codempresa = 'BANAP';
		$numevento  = NULL;
		$comestado  = 'M';
		$comtipo    = 'AG';
		
		$cadenaSQL = "INSERT INTO GESTOR.CG_ASIENTOS(NUM_ASIENTO, COD_AGENCIA, COD_USUARIO_INCLUSION, COD_USUARIO_ACTUALIZACION, COD_EMPRESA
													 FECHA_INCLUSION, FECHA_ACTUALIZACION, NUM_EVENTO, FECHA_COMPLETACION, DESCRIPCION
													 ESTADO, TIPO, FECHA_ASIENTO) 
						VALUES ({$numcom}, '{$this->daoConfintsigges->codfon}', '{$codusuario}', '{$codusuario}', '{$codempresa}',
								TO_DATE('{$feccom}','DD/MM/YYYY HH24:MI:SS'),TO_DATE('{$feccom}','DD/MM/YYYY HH24:MI:SS'),{$numevento},
								TO_DATE('{$feccom}','DD/MM/YYYY HH24:MI:SS'),'{$rsComprobante->fields['descom']}','{$comestado}',
								'{$comtipo}',TO_DATE('{$feccom}','DD/MN/YYYY HH24:MI:SS')) ";
		if ($this->conexionAlterna->Execute($cadenaSQL) === false) {
			$respuesta = false;
			$this->mensaje = 'Error insertando cabecera comprobante gestor: '.$this->conexionAlterna->ErrorMsg();
		}
		else {
			$k = 0;
			$codmonenda = '000000';
			$cotizacion = 1;
			while (!$rsComprobante->EOF) {
				$arrCueDes = array();
				foreach($this->arrDetalleConf as $dataCuenta) {
					if ($dataCuenta['cueori'] == $rsComprobante->fields['codcue']) {
						$arrCueDes = $dataCuenta;
						break;
					}
				}
					
				if (empty($arrCueDes)) {
					$detalleOk = false;
					break;
				}
				else {
					$operacion = 'D';
					if ($arrCueDes['codopd'] == 'H') {
						$operacion = 'A';
					}
					$secuencial = $this->obtenerSecuencia();
					$cadenaSQL = "INSERT INTO GESTOR.CG_DETALLES_ASIENTOS(CODCUENTA,NUMASIENTO,COD_EMPRESA,COD_AGENCIA,TIPO_TRANSACCION,
																		  VALOR_TRANSACCION,DETALLE,LINEA,COD_MONEDA,COTIZACION,SECUENCIAL)
									VALUES ('{$arrCueDes['cuedes']}',{$numcom},'{$codempresa}','{$this->daoConfintsigges->codfon}',
											'{$operacion}',{$rsComprobante->fields['monto']},'{$rsComprobante->fields['desdet']}',{$k},
											'{$codmonenda}',{$cotizacion},{$secuencial})";
					if ($this->conexionAlterna->Execute($cadenaSQL) === false) {
						$respuesta = false;
						$this->mensaje = 'Error insertando detalle del comprobante gestor: '.$this->conexionAlterna->ErrorMsg();
						break;
					}
				}
				
				$rsComprobante->MoveNext();
			}
			unset($rsComprobante);
		}
		
		return $respuesta;
	}
	
	public function comprobanteMigradoGES($objJson){
		$actualizado = true;
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
																	  $arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
		//$this->conexionAlterna->debug = true;
		$cadenaSQL = "UPDATE GESTOR.CG_ASIENTOS 
							SET PROCSIGESP = 'S' 
							WHERE COD_EMPRESA = '{$objJson->codemp}' AND COD_AGENCIA = '{$this->daoConfintsigges->codfon}' 
							  AND NUM_ASIENTO = '{$objJson->numcom}'";
		if ($this->conexionAlterna->Execute($cadenaSQL) === false) {
			$actualizado = false;
		}
		
		return $actualizado;
	}
	
	public function comprobanteMigradoSIG($objJson) {
		$actualizado = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "UPDATE sigesp_cmp
						SET procsigesp = 'S' 
						WHERE CMP.codemp = '{$objJson->codemp}' AND CMP.procede='{$objJson->procom}' 
						AND CMP.comprobante = '{$objJson->numcom}' AND CMP.codban='{$objJson->codban}'
						AND CMP.ctaban='{$objJson->ctaban}'";
		if ($this->conexionBaseDatos->Execute($cadenaSQL) === false) {
			$actualizado = false;
		}
		
		return $actualizado;
	}
	
	public function procesarComrpobante($objJson) {
		$respuesta = true;
		$nCom = count((array)$objJson->arrComprobante);
		if ($this->daoConfintsigges->basesc == 'S') {
			for($j=0;$j<=$nCom-1;$j++) {
				$dataComprobante = $this->obtenerComprobante($objJson->arrComprobante[$j]);
				if ($this->daoConfintsigges->movban == 'NI') {
					if ($this->generarCompSIG($objJson->arrComprobante[$j]->numcomalt, $objJson->arrComprobante[$j]->feccomalt, $dataComprobante)) {
						$this->comprobanteMigradoGES($objJson->arrComprobante[$j]);
					}
					else {
						$respuesta = false;
						break;
					}
				}
				else {
					if ($this->generarMovBanSIG($objJson->arrComprobante[$j]->numcomalt, $objJson->arrComprobante[$j]->feccomalt, $dataComprobante)) {
						$this->comprobanteMigradoGES($objJson->arrComprobante[$j]);
					}
					else {
						$respuesta = false;
						break;
					}
				}
			}
		}
		else if ($this->daoConfintsigges->basesc == 'G') {
			for($j=0;$j<=$nCom-1;$j++) {
				$dataComprobante = $this->obtenerComprobante($objJson->arrComprobante[$j]);
				if ($this->generarCompGES($objJson->arrComprobante[$j]->numcomalt, $objJson->arrComprobante[$j]->feccomalt, $dataComprobante)) {
					$this->comprobanteMigradoSIG($objJson->arrComprobante[$j]);
				}
				else {
					$respuesta = false;
					break;
				}
			}
		}
		
		return $respuesta;
	}
	
	public function validarUsuAdm(){
		$adm = false;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL  = "SELECT codusu 
  						FROM sss_usuarios WHERE codusu = '{$_SESSION['la_logusr']}' AND admusu = 1";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($data->_numOfRows > 0) {
			$adm = true;
		}
		unset($data);
				
		return $adm;
	}
	
	public function formatoFecha($fecha) {
		$fecha = str_replace('-', '', $fecha);
		$anno = substr($fecha, 0, 4);
		$dia  = substr($fecha, 6, 2);
		$mes  = intval(substr($fecha, 4, 2));
		
		$meses = array('Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
			
		return $dia.'/'.$meses[$mes-1].'/'.$anno;
	}
}
?>