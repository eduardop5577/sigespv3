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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracionsfcpag.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv.'/modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');

class ServicioIntegracionSFCPAG implements IIntegracionSFCPAG 
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
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->montopag =0;	
	}
	
	public function buscarPagosIntegrar($comprobante, $procede, $fecha, $estatus) 
	{
		$criterio = '';
		if(!empty($comprobante)) 
		{
			$criterio .= " AND comprobante like '%{$comprobante}%'";
		}
		if(!empty($procede)) 
		{
			$criterio .= " AND procede like '%{$procede}%'";
		}
		if(!empty($fecha)) 
		{
			$criterio .= " AND fecdep = '{$fecha}'";
		}
		$conCat = $this->conexionBaseDatos->Concat("'MOVIMIENTO DE PAGO - INTEGRACIÓN MÓDULO FACTURACIÓN Y COBRANZAS {$comprobante} -'",'fecdep',"'-'",'codban',"'-'",'ctaban');
		$conCat2 = $this->conexionBaseDatos->Concat('codban',"'-'",'ctaban');
		$cadenaSQL="SELECT comprobante, fecdep, procede, codban, ctaban,operacion,numdoc, ".$conCat." as descripcion, MAX(fechaconta) as fechaconta, ".
				   "       MAX(fechaanula) as fechaanula, ".$conCat2." as bancue ". 
				   "  FROM mis_sigesp_banco ".
				   " WHERE codemp = '".$this->codemp."'".
				   "   AND estint = ".$estatus." ".
				   $criterio.
				   " GROUP BY comprobante, procede, fecdep, codban, ctaban,operacion,numdoc ".
			       " ORDER BY fecdep, comprobante ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($dataSet===false) 
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
				
		return $dataSet;
	}
	
	public function buscarDetalleComprobantePAGSPI($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc) 
	{
		$fecha= convertirFechaBd($fecha);
		$cadenaSQL = "SELECT mis_sigesp_banco.sc_cuenta AS spi_cuenta, monto, spi_cuentas.denominacion ".
					 "  FROM mis_sigesp_banco ". 
					 " INNER JOIN spi_cuentas ". 
					 "    ON mis_sigesp_banco.codemp = spi_cuentas.codemp ". 
					 "   AND mis_sigesp_banco.sc_cuenta = spi_cuentas.spi_cuenta ". 
					 " WHERE mis_sigesp_banco.codemp='{$this->codemp}' ". 
					 "   AND comprobante='{$comprobante}' ". 
					 "   AND procede='{$procede}' ". 
					 "   AND fecdep='{$fecha}' ". 
					 "   AND codban='{$codban}' ". 
					 "   AND ctaban='{$ctaban}' ". 
					 "   AND numdoc='{$numdoc}' ". 
					 "   AND RTRIM(modulo)='SPI' ". 
					 " ORDER BY mis_sigesp_banco.sc_cuenta ";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetalleComprobantePAGSCG($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc)  
	{
		$fecha= convertirFechaBd($fecha);
		$cadenaSQL="SELECT mis_sigesp_banco.sc_cuenta as cuenta,(CASE WHEN debhab = 'D' THEN monto ELSE 0 END) AS debe, ". 
				   "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion ". 
				   "  FROM mis_sigesp_banco ". 
				   " INNER JOIN scg_cuentas ". 
				   "    ON mis_sigesp_banco.codemp = scg_cuentas.codemp ". 
				   "   AND mis_sigesp_banco.sc_cuenta = scg_cuentas.sc_cuenta ". 
				   " WHERE mis_sigesp_banco.codemp='{$this->codemp}' ". 
				   "   AND comprobante='{$comprobante}' ". 
				   "   AND procede='{$procede}' ". 
				   "   AND fecdep='{$fecha}' ". 
				   "   AND codban='{$codban}' ". 
				   "   AND ctaban='{$ctaban}' ". 
				   "   AND numdoc='{$numdoc}' ". 
				   "   AND (RTRIM(modulo)='SCG' OR RTRIM(modulo)='SCB') ". 
				   " ORDER BY debhab";
		return $this->conexionBaseDatos->Execute ( $cadenaSQL );
	}
		
	public function buscarDetallePagoSPI($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc, $arrcabecera) 
	{
		$arregloSPI = null;
		$cadenaSQL = "SELECT sc_cuenta, monto, descripcion, documento, debhab ".
					 "  FROM mis_sigesp_banco ".
					 " WHERE codemp='{$this->codemp}' ".
					 "   AND comprobante='{$comprobante}' ". 
					 "   AND procede='{$procede}' ". 
					 "   AND fecdep='{$fecha}' ". 
					 "   AND codban='{$codban}' ". 
					 "   AND ctaban='{$ctaban}' ". 
					 "   AND numdoc='{$numdoc}' ". 
					 "   AND RTRIM(modulo)='SPI' ". 
					 " ORDER BY sc_cuenta ";
		$data = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$data->EOF)
			{
				$arregloSPI['codemp'][$i]= $arrcabecera['codemp'];
				$arregloSPI['codban'][$i]= $arrcabecera['codban'];
				$arregloSPI['ctaban'][$i]= $arrcabecera['ctaban'];
				$arregloSPI['numdoc'][$i]= $arrcabecera['numdoc'];
				$arregloSPI['codope'][$i]= $arrcabecera['codope'];
				$arregloSPI['estmov'][$i]= $arrcabecera['estmov'];
				$arregloSPI['spicuenta'][$i]= trim($data->fields['sc_cuenta']);
				$arregloSPI['documento'][$i]= $data->fields['documento'];
				$arregloSPI['operacion'][$i]= $data->fields['debhab'];
				$arregloSPI['desmov'][$i]= $data->fields["descripcion"];
				$arregloSPI['procede_doc'][$i]= $arrcabecera['procede'];
				$arregloSPI['monto'][$i]= number_format($data->fields["monto"],2,".","");;
				$arregloSPI['codestpro1'][$i]= '-------------------------';
				$arregloSPI['codestpro2'][$i]= '-------------------------';
				$arregloSPI['codestpro3'][$i]= '-------------------------';
				$arregloSPI['codestpro4'][$i]= '-------------------------';
				$arregloSPI['codestpro5'][$i]= '-------------------------';
				$arregloSPI['estcla'][$i]= '-';
				$data->MoveNext();
				$i++;
			}
		}
		unset($data);
		return $arregloSPI;
	}
	
	public function buscarDetallePagoSCG($comprobante, $procede, $fecha, $codban, $ctaban, $numdoc, $arrcabecera) 
	{
		$arregloSCG = null;
		$montoscg = 0;
		$cadenaSQL="SELECT estbco, modulo, sc_cuenta, debhab, monto, descripcion, documento ".
				   "  FROM mis_sigesp_banco ".
				   " WHERE codemp='{$this->codemp}' ".
				   "   AND comprobante='{$comprobante}' ".
				   "   AND procede='{$procede}' ".
				   "   AND fecdep='{$fecha}' ".
				   "   AND codban='{$codban}' ".
				   "   AND ctaban='{$ctaban}' ".
				   "   AND numdoc='{$numdoc}' ".
				   "   AND (RTRIM(modulo)='SCG' OR RTRIM(modulo)='SCB') ".
				   "   ORDER BY debhab";
		$data = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$data->EOF)
			{
				$arregloSCG['codemp'][$i]= $arrcabecera['codemp'];
				$arregloSCG['codban'][$i]= $arrcabecera['codban'];
				$arregloSCG['ctaban'][$i]= $arrcabecera['ctaban'];
				$arregloSCG['numdoc'][$i]= $arrcabecera['numdoc'];
				$arregloSCG['codope'][$i]= $arrcabecera['codope'];
				$arregloSCG['estmov'][$i]= $arrcabecera['estmov'];
				$arregloSCG['scg_cuenta'][$i]= trim($data->fields['sc_cuenta']);
				$arregloSCG['debhab'][$i]= $data->fields['debhab'];
				$arregloSCG['codded'][$i]= '00000';
				$arregloSCG['documento'][$i]= $data->fields['documento'];
				$arregloSCG['desmov'][$i]= $data->fields["descripcion"];
				$arregloSCG['procede_doc'][$i]= $arrcabecera['procede'];
				$arregloSCG['monto'][$i]= number_format($data->fields["monto"],2,".","");
				$arregloSCG['monobjret'][$i]= 0;
				if (trim($data->fields["modulo"])=='SCB')
				{
 					$this->montopag +=$arregloSCG['monto'][$i]; 
               	}
				if ((trim($data->fields["modulo"])=='SCG') &&  (trim($data->fields['debhab'])=='D'))
				{
 					$montoscg +=$arregloSCG['monto'][$i]; 
               	}
				$data->MoveNext();
				$i++;
			}
		}
		if ($this->montopag == 0)
		{
			$this->montopag = $montoscg;
		}
		unset($data);
		return $arregloSCG;
	}
	
	public function actualizarEstatusPAG($comprobante, $procede, $fecha, $codban, $ctaban, $estatus, $fechaConta, $comprobanteSigesp, $numdoc) 
	{
		$cadenaSQL = "UPDATE mis_sigesp_banco ".
   					 "	 SET estint={$estatus}, ".
   					 "   	 fechaconta='{$fechaConta}', ".
   					 "	     comprobante_sigesp='{$comprobanteSigesp}'  ".
   					 " WHERE codemp='{$this->codemp}' ". 
   					 "   AND comprobante='{$comprobante}' ". 
   					 "   AND procede='{$procede}' ". 
   					 "   AND fecdep='{$fecha}' ". 
   					 "   AND numdoc='{$numdoc}' ". 
   					 "   AND codban='{$codban}' ". 
   					 "   AND ctaban='{$ctaban}'";
		$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($resultado===false) 
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
	}
	
    public function contabilizarPAG($comprobante, $fecha, $procede, $descripcion, $codban, $ctaban, $operacion, $numdoc, $arrEvento)
    {
		DaoGenerico::iniciarTrans();
		$fechaMovimiento = convertirFechaBd($fecha);
		$comprobanteSigesp = fillComprobante(trim($numdoc));
		//$numdoc = fillComprobante(trim($numdoc));
		$arrCabecera=array();
		$arregloSCG=array();
		$arregloSPG=array();
		$arregloSPI=array();
		$this->montopag=0;
		
		$arrCabecera["codemp"]	 = $this->codemp;
		$arrCabecera["codban"]	 = $codban;
		$arrCabecera["ctaban"]	 = $ctaban;
		$arrCabecera["numdoc"]	 = $comprobanteSigesp;
		$arrCabecera["codope"]	 = $operacion;
		$arrCabecera["estmov"]	 = 'N';
		$arrCabecera["cod_pro"]	 = '----------';
		$arrCabecera["ced_bene"] = '----------';
		$arrCabecera["tipo_destino"]	 = '-';
		$arrCabecera["codconmov"] = '---';
		$arrCabecera["fecmov"]	 = $fechaMovimiento;
		$arrCabecera["conmov"]	 = $descripcion;
		$arrCabecera["nomproben"] = 'Ninguno';
		$arrCabecera["monto"]	 = 0;
		$arrCabecera["estbpd"]	 = 'M';
		$arrCabecera["estcon"]	 = 0;
		$arrCabecera["estcobing"] = 1;
		$arrCabecera["esttra"] = 0;
		$arrCabecera["chevau"]	 = "";
		$arrCabecera["estimpche"]	 = 0;
		$arrCabecera["monobjret"] = 0;
		$arrCabecera["monret"]	 = 0;
		$arrCabecera["procede"]	 = $procede;
		$arrCabecera["comprobante"]	 = $comprobanteSigesp;
		$arrCabecera["fecha"]	 = '1900-01-01';
		$arrCabecera["id_mco"] = ' ';
		$arrCabecera["emicheproc"] = 0;
		$arrCabecera["emicheced"] = ' ';
		$arrCabecera["emichenom"] = ' ';
		$arrCabecera["emichefec"] = '1900-01-01';
		$arrCabecera["estmovint"] = 0;
		$arrCabecera["codusu"] = $_SESSION['la_logusr'];
		$arrCabecera["codopeidb"] = ' ';
		$arrCabecera["aliidb"] = 0;
		$arrCabecera["feccon"] = '1900-01-01';
		$arrCabecera["estreglib"] = ' ';
		$arrCabecera["numcarord"] = ' ';
		$arrCabecera["numpolcon"] = 0;
		$arrCabecera["coduniadmsig"] = ' ';
		$arrCabecera["codbansig"]	 = ' ';
		$arrCabecera["fecordpagsig"]	 = '1900-01-01';
		$arrCabecera["tipdocressig"]	 = ' ';
		$arrCabecera["numdocressig"]	 = ' ';
		$arrCabecera["estmodordpag"]	 = '0';
		$arrCabecera["codfuefin"] = '--';
		$arrCabecera["forpagsig"] = ' ';
		$arrCabecera["medpagsig"] = ' ';
		$arrCabecera["codestprosig"] = ' ';
		$arrCabecera["tranoreglib"] = 0;
		$arrCabecera["numordpagmin"]	 = '-';
		$arrCabecera["codtipfon"] = '----';
		$arrCabecera["estmovcob"] = 0;
		$arrCabecera["numconint"] = NULL;
		$arrCabecera["numchequera"] = "";
		if ($this->valido)
		{
			$arregloSPI = $this->buscarDetallePagoSPI($comprobante, $procede, $fechaMovimiento, $codban, $ctaban, $numdoc, $arrCabecera);
		}
		
		if ($this->valido) 
		{
			$arregloSCG = $this->buscarDetallePagoSCG($comprobante, $procede, $fechaMovimiento, $codban, $ctaban, $numdoc, $arrCabecera); 
		}
		if ($this->valido) 
		{
			$arrCabecera["monto"]	 = $this->montopag;
			if (!$this->existeMovimientoBanco($codban, $ctaban, $comprobanteSigesp, $operacion))
			{
				$this->valido=true;
				$this->mensaje="";
				$servicioBancario = new ServicioMovimientoScb();
				$this->valido = $servicioBancario->GuardarAutomatico($arrCabecera,$arregloSCG,$arregloSPG,$arregloSPI,$arrEvento);
				$this->mensaje.= $servicioBancario->mensaje;
				unset($servicioBancario);
			}
			else
			{
				$this->valido=false;
				$this->mensaje='El Movimiento de Banco -> Banco:'.$codban.' Cuenta:'.$ctaban.' Numero:'.$comprobanteSigesp.' Operacion:'.$operacion.'. Ya Existe';			
			}
    	}    
		if($this->valido)
		{
			$this->actualizarEstatusPAG($comprobante, $procede, $fechaMovimiento, $codban, $ctaban, '1', $fechaMovimiento, $comprobanteSigesp, $numdoc);
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
	
	public function procesoContabilizarPAG($arrJson) 
	{
		$arrRespuesta= array();
		$nPAG = count((array)$arrJson->pagos);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_contabiliza_sfcpag.html';
		for($j=0;$j<=$nPAG-1;$j++) 
		{
			$arrEvento['desevetra'] = "Se contabilizo El pago {$arrJson->pagos[$j]->comprobante}, asociado a la empresa {$this->codemp}";
			$descripcion = "MOVIMIENTO DE PAGO - INTEGRACION MODULO FACTURACION Y COBRANZAS";
			if ($this->contabilizarPAG($arrJson->pagos[$j]->comprobante, $arrJson->pagos[$j]->fecha, 
			                           $arrJson->pagos[$j]->procede, $descripcion,
			                           $arrJson->pagos[$j]->codban, $arrJson->pagos[$j]->ctaban,
			                           $arrJson->pagos[$j]->operacion, $arrJson->pagos[$j]->numdoc, $arrEvento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->pagos[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "Se proceso exitosamente el pago";
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->pagos[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "No se pudo procesar el pago, {$this->mensaje}";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nPAG.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function existeMovimientoBanco($codban, $ctaban, $comprobante, $operacion) 
	{
		$movExiste = false;
		$cadenaSQL = "SELECT numdoc ".
					 "	FROM scb_movbco ". 
					 " WHERE codemp='{$this->codemp}' ". 
					 "   AND codban='{$codban}' ". 
					 "   AND ctaban='{$ctaban}' ". 
					 "   AND numdoc='{$comprobante}'  ".
					 "   AND codope='{$operacion}' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($dataSet===false) 
		{
			$movExiste = false;
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = $movExiste;
		}
		else 
		{
			if($dataSet->_numOfRows > 0)
			{
				$movExiste = true;
			}
			unset($dataSet);
		}
		
		return $movExiste;
	}
	
	public function verificarMovimientoBanco($codban, $ctaban, $comprobante, $operacion) 
	{
		$movOk = true;
		$cadenaSQL = "SELECT numdoc ".
					 "	FROM scb_movbco ". 
					 " WHERE codemp='{$this->codemp}' ". 
					 "   AND codban='{$codban}' ". 
					 "   AND ctaban='{$ctaban}' ". 
					 "   AND numdoc='{$comprobante}'  ".
					 "   AND codope='{$operacion}' ".
					 "   AND estmov='C'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($dataSet===false) 
		{
			$movOk = false;
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = $movOk;
		}
		else 
		{
			if($dataSet->_numOfRows > 0)
			{
				$movOk = false;
				$this->valido = $movOk;
				$this->mensaje .= 'No puede reversar el movimiento ya que esta previsamente contabilizado en banco';
			}
			unset($dataSet);
		}
		
		return $movOk;
	}
	
	public function eliminarDetalleSPI($codban, $ctaban, $comprobante, $operacion) 
	{
		$cadenaSQL = "DELETE ".
					 "	FROM scb_movbco_spi ".
					 " WHERE codemp='{$this->codemp}' ". 
					 " 	 AND codban='{$codban}' ". 
					 " 	 AND ctaban='{$ctaban}' ". 
					 " 	 AND numdoc='{$comprobante}' ". 
					 " 	 AND codope='{$operacion}' ".
					 " 	 AND estmov='N'";
		$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($resultado===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
	}
	
	public function eliminarDetalleSCG($codban, $ctaban, $comprobante, $operacion) 
	{
		$cadenaSQL = "DELETE ". 
					 "  FROM scb_movbco_scg ". 
					 " WHERE codemp='{$this->codemp}' ". 
					 " 	 AND codban='{$codban}' ". 
					 " 	 AND ctaban='{$ctaban}' ". 
					 " 	 AND numdoc='{$comprobante}' ". 
					 " 	 AND codope='{$operacion}' ".
					 " 	 AND estmov='N'";
		$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($resultado===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
	}
	
	public function eliminarDetalleFuenteFinanciamiento($codban, $ctaban, $comprobante, $operacion) 
	{
		$cadenaSQL = "DELETE ". 
					 "  FROM scb_movbco_fuefinanciamiento ". 
					 " WHERE codemp='{$this->codemp}' ". 
					 " 	 AND codban='{$codban}' ". 
					 " 	 AND ctaban='{$ctaban}' ". 
					 " 	 AND numdoc='{$comprobante}' ". 
					 " 	 AND codope='{$operacion}' ".
					 " 	 AND estmov='N'";
		$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
		if ($resultado===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
	}
	
	public function revContabilizarPAG($comprobante, $codban, $ctaban, $operacion, $fecha, $procede, $numdoc, $arrEvento) 
	{
		DaoGenerico::iniciarTrans();
		$comprobanteSigesp = fillComprobante(trim($numdoc));
		if($this->verificarMovimientoBanco($codban, $ctaban, $comprobanteSigesp, $operacion))
		{
			$this->eliminarDetalleSPI($codban, $ctaban, $comprobanteSigesp, $operacion);
			if ($this->valido) 
			{
				$this->eliminarDetalleSCG($codban, $ctaban, $comprobanteSigesp, $operacion);
			}
			if ($this->valido) 
			{
				$this->eliminarDetalleFuenteFinanciamiento($codban, $ctaban, $comprobanteSigesp, $operacion);
			}
			if ($this->valido) 
			{
				$cadenaSQL = "DELETE ".
							 "  FROM scb_movbco ".
							 " WHERE codemp='{$this->codemp}'". 
							 "   AND codban='{$codban}'". 
							 "   AND ctaban='{$ctaban}'". 
							 "   AND numdoc='{$comprobanteSigesp}' ".
							 "   AND codope='{$operacion}' ".
							 "   AND estmov='N'";
				$resultado  = $this->conexionBaseDatos->Execute ( $cadenaSQL );
				if ($resultado===false) 
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
			}
			if($this->valido)
			{
				$this->actualizarEstatusPAG($comprobante, $procede, $fecha, $codban, $ctaban, '0', '1900-01-01', $comprobanteSigesp, $numdoc);
			}
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
	
	public function procesoRevContabilizarPAG($arrJson) 
	{
		$arrRespuesta= array();
		$nPAG = count((array)$arrJson->pagos);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_contabiliza_sfcpag.html';
		for($j=0;$j<=$nPAG-1;$j++) 
		{
			$fecha = convertirFechaBd($arrJson->pagos[$j]->fecha);
			$arrEvento['desevetra'] = "Se reverso la integracion del pago {$arrJson->pagos[$j]->comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizarPAG($arrJson->pagos[$j]->comprobante, $arrJson->pagos[$j]->codban,$arrJson->pagos[$j]->ctaban,$arrJson->pagos[$j]->operacion, 
										 $fecha,$arrJson->pagos[$j]->procede,$arrJson->pagos[$j]->numdoc,$arrEvento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->pagos[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Se reverso la integraci&#243;n del pago';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->pagos[$j]->comprobante;
				$arrRespuesta[$h]['mensaje'] = "No se pudo reversar la integraci&#243;n del pago, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nPAG.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}	
}
?>