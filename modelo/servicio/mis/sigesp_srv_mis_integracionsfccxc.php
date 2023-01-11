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
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracionsfccxc.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionSFCCXC implements IIntegracionSFCCXC
 {
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos;
	private $servicioComprobante;
	private $daoCuentaCobrar;
			
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
	}
	
	public function buscarCuentasCobrarIntegrar($comprobante, $procede, $fecha, $estatus)
	 {
		$criterio = '';
		if(!empty($comprobante))
		{
			$criterio .= " AND comprobante like '%{$numsol}%'";
		}
		if(!empty($procede))
		 {
			$criterio .= " AND procede like '%{$procede}%'";
		}
		if(!empty($fecha))
		{
			$criterio .= " AND fecha = '{$fecha}'";
		}
		$cadenaSql = "SELECT comprobante, fecha, procede, MAX(descripcion_comprobante) AS descripcion   ".
					 "  FROM mis_sigesp_cxc  ".
					 " WHERE codemp='{$_SESSION['la_empresa']['codemp']}'  ".
					 "   AND estint={$estatus} {$criterio}  ".
					 " GROUP BY procede, comprobante, fecha  ".
					 " ORDER BY fecha, comprobante";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) 
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
				
		return $dataSet;
	}
	
	public function buscarDetalleComprobanteCXCSPI($comprobante, $procede, $fecha) 
	{
		$fecha = convertirFechaBd($fecha);
		$cadenaSQL = "SELECT mis_sigesp_cxc.spi_cuenta, SUM(monto) AS monto, MAX(spi_cuentas.denominacion) AS denominacion ".
					 "	FROM mis_sigesp_cxc ". 
					 " INNER JOIN spi_cuentas ". 
					 "    ON mis_sigesp_cxc.codemp = spi_cuentas.codemp ". 
					 "   AND mis_sigesp_cxc.spi_cuenta = spi_cuentas.spi_cuenta ". 
					 " WHERE mis_sigesp_cxc.codemp='{$_SESSION['la_empresa']['codemp']}' ". 
					 "   AND mis_sigesp_cxc.comprobante='{$comprobante}' ". 
					 "   AND mis_sigesp_cxc.procede='{$procede}' ". 
					 "   AND mis_sigesp_cxc.fecha='{$fecha}' ".
					 "   AND mis_sigesp_cxc.spi_cuenta <>''".
					 " GROUP BY mis_sigesp_cxc.spi_cuenta";
					 
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetalleComprobanteCXCSCG($comprobante, $procede, $fecha) 
	{
		$fecha = convertirFechaBd($fecha);
		$cadenaSql="SELECT mis_sigesp_cxc.sc_cuenta as cuenta,(CASE WHEN debhab = 'D' THEN monto ELSE 0 END) AS debe, 
		                   (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion  
						FROM mis_sigesp_cxc 
					   INNER JOIN scg_cuentas 
					      ON mis_sigesp_cxc.codemp = scg_cuentas.codemp 
					     AND mis_sigesp_cxc.sc_cuenta = scg_cuentas.sc_cuenta 
					   WHERE mis_sigesp_cxc.codemp='{$_SESSION['la_empresa']['codemp']}' 
						 AND mis_sigesp_cxc.comprobante='{$comprobante}' 
						 AND mis_sigesp_cxc.procede='{$procede}' 
						 AND mis_sigesp_cxc.fecha='{$fecha}'
					   ORDER BY mis_sigesp_cxc.debhab, mis_sigesp_cxc.sc_cuenta";
		return $this->conexionBaseDatos->Execute ( $cadenaSql );
	}
	
	public function obtenerDetalleCuentaCobrarSCG($comprobante,$arrCabecera) 
	{
		$arrDetalleSCG = array();
		$cadenaSql="SELECT sc_cuenta, debhab, monto, documento, descripcion 
						FROM mis_sigesp_cxc 
						WHERE codemp='{$_SESSION['la_empresa']['codemp']}' 
						AND comprobante='{$comprobante}' 
						AND procede='{$arrCabecera['procede']}' 
						AND fecha='{$arrCabecera['fecha']}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) 
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else 
		{
			$conSCG = 1;
			while(!$dataSet->EOF) 
			{
				if ($dataSet->fields['estcon'] != 2) 
				{
					$arrDetalleSCG[$conSCG]['codemp']      = $arrCabecera['codemp'];
					$arrDetalleSCG[$conSCG]['procede']     = $arrCabecera['procede'];
					$arrDetalleSCG[$conSCG]['comprobante'] = $arrCabecera['comprobante'];
					$arrDetalleSCG[$conSCG]['codban']      = $arrCabecera['codban'];
					$arrDetalleSCG[$conSCG]['ctaban']      = $arrCabecera['ctaban'];
					$arrDetalleSCG[$conSCG]['fecha']       = $arrCabecera['fecha'];
					$arrDetalleSCG[$conSCG]['descripcion'] = trim($dataSet->fields['descripcion']);
					$arrDetalleSCG[$conSCG]['sc_cuenta']   = trim($dataSet->fields['sc_cuenta']);
					$arrDetalleSCG[$conSCG]['procede_doc'] = $arrCabecera['procede'];
					$arrDetalleSCG[$conSCG]['documento']   = $dataSet->fields['documento'];
					$arrDetalleSCG[$conSCG]['debhab']      = $dataSet->fields['debhab'];
					$arrDetalleSCG[$conSCG]['monto']       = $dataSet->fields['monto'];
					$arrDetalleSCG[$conSCG]['orden']       = $conSCG;
					
					$conSCG++;
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSCG;
	}
	
	public function obtenerDetalleCuentaCobrarSPI($comprobante,$arrCabecera) 
	{
		$arrDetalleSPI = array();
		$cadenaSql = "SELECT spi_cuenta, monto, documento, procede, 'DEV' as operacion, descripcion, ".
                             "       codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla ".
					 "  FROM mis_sigesp_cxc ". 
					 " WHERE codemp='{$_SESSION['la_empresa']['codemp']}'  ".
					 "   AND comprobante='{$comprobante}'  ".
					 "   AND procede='{$arrCabecera['procede']}'  ".
					 "   AND spi_cuenta<>'' ".
					 "   AND fecha='{$arrCabecera['fecha']}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) 
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else 
		{
			$conSPI = 0;
			while(!$dataSet->EOF) 
			{
				//PREPARANDO ARREGLO CON DETALLES DE GASTO
				$codestpro = $dataSet->fields['codestpro'];
				//ARREGLO SPG
				$arrDetalleSPI[$conSPI]['codemp']      = $arrCabecera['codemp'];
				$arrDetalleSPI[$conSPI]['procede']     = $arrCabecera['procede'];
				$arrDetalleSPI[$conSPI]['comprobante'] = $arrCabecera['comprobante'];
				$arrDetalleSPI[$conSPI]['codban']      = $arrCabecera['codban'];
				$arrDetalleSPI[$conSPI]['ctaban']      = $arrCabecera['ctaban'];
				$arrDetalleSPI[$conSPI]['fecha']       = $arrCabecera['fecha'];
				$arrDetalleSPI[$conSPI]['descripcion'] =  trim($dataSet->fields['descripcion']);
				$arrDetalleSPI[$conSPI]['codestpro1']  = $dataSet->fields['codestpro1'];
				$arrDetalleSPI[$conSPI]['codestpro2']  = $dataSet->fields['codestpro2'];
				$arrDetalleSPI[$conSPI]['codestpro3']  = $dataSet->fields['codestpro3'];
				$arrDetalleSPI[$conSPI]['codestpro4']  = $dataSet->fields['codestpro4'];
				$arrDetalleSPI[$conSPI]['codestpro5']  = $dataSet->fields['codestpro5'];
				$arrDetalleSPI[$conSPI]['estcla']      = $dataSet->fields['estcla'];
				$arrDetalleSPI[$conSPI]['spi_cuenta']  = trim($dataSet->fields['spi_cuenta']);
				$arrDetalleSPI[$conSPI]['procede_doc'] = $dataSet->fields['procede'];
				$arrDetalleSPI[$conSPI]['documento']   = $dataSet->fields['documento'];
				$arrDetalleSPI[$conSPI]['operacion']   = $dataSet->fields['operacion'];
				$arrDetalleSPI[$conSPI]['codfuefin']   = '--';
				$arrDetalleSPI[$conSPI]['monto']       = $dataSet->fields['monto'];
				$arrDetalleSPI[$conSPI]['orden']       = $conSPI;
				$conSPI++;
				
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSPI;
	}
	
	public function actualizarEstatusCXC($comprobante, $procede, $fecha, $estatus, $fechaConta) 
	{
		$cadenaSQL = "UPDATE mis_sigesp_cxc
   						SET estint={$estatus}, fechaconta='".$fechaConta."'
						WHERE codemp='{$_SESSION['la_empresa']['codemp']}' 
						AND comprobante='{$comprobante}' 
						AND procede='{$procede}' 
						AND fecha='".$fecha."'";
		$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($resultado===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
	}
	
	
	public function contabilizarCXC($comprobante, $fecha, $procede, $descripcion, $arrEvento) 
	{
		DaoGenerico::iniciarTrans();
		$fechaComprobante = convertirFechaBd($fecha);
		$arrCabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
		$arrCabecera['procede'] = $procede;
		$arrCabecera['comprobante'] = fillComprobante(trim($comprobante));
		$arrCabecera['codban'] = '---';
		$arrCabecera['ctaban'] = '-------------------------';
		$arrCabecera['fecha'] = $fechaComprobante;
		$arrCabecera['descripcion'] = $descripcion;
		$arrCabecera['tipo_comp'] = 1;
		$arrCabecera['tipo_destino'] = '-';
		$arrCabecera['cod_pro'] = '----------';
		$arrCabecera['ced_bene'] = '----------';
		$arrCabecera['total'] = 0;
		$arrCabecera['numpolcon'] = 0;
		$arrCabecera['esttrfcmp'] = 0;
		$arrCabecera['estrenfon'] = 0;
		$arrCabecera['codfuefin'] = '--';
		$arrCabecera['codusu'] = $_SESSION['la_logusr'];
		$arrDetalleSCG = $this->obtenerDetalleCuentaCobrarSCG($comprobante,$arrCabecera);
		$arrDetalleSPI = $this->obtenerDetalleCuentaCobrarSPI($comprobante,$arrCabecera);
		if($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrCabecera, null, $arrDetalleSCG, $arrDetalleSPI, $arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido) 
			{
				$this->actualizarEstatusCXC($comprobante, $procede, $fechaComprobante, '1', $fechaComprobante);
			}
			unset($serviciocomprobante);
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrEvento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrEvento['codemp'];
		$servicioEvento->codsis=$arrEvento['codsis'];
		$servicioEvento->nomfisico=$arrEvento['nomfisico'];
		$servicioEvento->desevetra=$arrEvento['desevetra'];			
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
	
	public function procesoContabilizarCXC($arrJson) 
	{
		$arrRespuesta= array();
		$nCXC = count((array)$arrJson->cuentaCobrar);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_contabiliza_sfccxc.html';
		for($j=0;$j<=$nCXC-1;$j++) 
		{
			$arrEvento['desevetra'] = "Se contabilizo la cuenta a cobrar {$arrJson->cuentaCobrar[$j]->comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->contabilizarCXC($arrJson->cuentaCobrar[$j]->comprobante, $arrJson->cuentaCobrar[$j]->fecha, 
			                           $arrJson->cuentaCobrar[$j]->procede, $arrJson->cuentaCobrar[$j]->descripcion, $arrEvento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->cuentaCobrar[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "Se contabilizo exitosamente la cuenta a cobrar";
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->cuentaCobrar[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "No se pudo contabilizar la cuenta a cobrar, {$this->mensaje}";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nCXC.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function revContabilizarCXC($comprobante, $fecha, $procede, $descripcion, $arrEvento) 
	{
		DaoGenerico::iniciarTrans();
		$fechaComprobante = convertirFechaBd($fecha);
		$arrCabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
		$arrCabecera['procede'] = $procede;
		$arrCabecera['comprobante'] = fillComprobante(trim($comprobante));
		$arrCabecera['codban'] = '---';
		$arrCabecera['ctaban'] = '-------------------------';
		$arrCabecera['fecha'] = $fechaComprobante;
		$arrCabecera['descripcion'] = $descripcion;
		$arrCabecera['tipo_comp'] = 1;
		$arrCabecera['tipo_destino'] = '-';
		$arrCabecera['cod_pro'] = '----------';
		$arrCabecera['ced_bene'] = '----------';
		$arrCabecera['total'] = 0;
		$arrCabecera['numpolcon'] = 0;
		$arrCabecera['esttrfcmp'] = 0;
		$arrCabecera['estrenfon'] = 0;
		$arrCabecera['codfuefin'] = '--';
		$arrCabecera['codusu'] = $_SESSION['la_logusr'];
		$serviciocomprobante = new ServicioComprobante();
		$this->valido = $serviciocomprobante->eliminarComprobante($arrCabecera,$arrEvento);
		$this->mensaje .= $serviciocomprobante->mensaje;
		if($this->valido) 
		{
			$this->actualizarEstatusCXC($comprobante, $procede, $fechaComprobante, '0', '1900-01-01');
		}
		unset($serviciocomprobante);
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrEvento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrEvento['codemp'];
		$servicioEvento->codsis=$arrEvento['codsis'];
		$servicioEvento->nomfisico=$arrEvento['nomfisico'];
		$servicioEvento->desevetra=$arrEvento['desevetra'];			
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
	
	public function procesoRevContabilizarCXC($arrJson) 
	{
		$arrRespuesta= array();
		$nCXC = count((array)$arrJson->cuentaCobrar);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_contabiliza_sfccxc.html';
		for($j=0;$j<=$nCXC-1;$j++)
		 {
			$arrEvento['desevetra'] = "Se reverso la contabilizacion de la cuenta a cobrar {$arrJson->cuentaCobrar[$j]->comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->revContabilizarCXC($arrJson->cuentaCobrar[$j]->comprobante, $arrJson->cuentaCobrar[$j]->fecha, 
			                             $arrJson->cuentaCobrar[$j]->procede, $arrJson->cuentaCobrar[$j]->descripcion, $arrEvento)) 			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->cuentaCobrar[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "Se reverso la contabilizaci&#243;n de la cuenta a cobrar exitosamente";
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->cuentaCobrar[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "No se pudo reversar la contabilizaci&#243;n de la cuenta a cobrar, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nCXC.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
}
?>