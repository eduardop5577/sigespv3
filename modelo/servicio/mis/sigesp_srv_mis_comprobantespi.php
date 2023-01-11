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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_icomprobantespi.php');

class ServicioComprobanteSPI implements IComprobanteSPI {
	public  $mensaje; 
	public  $valido; 
	private $daoDetalleSpi;
	private $conexionBaseDatos; 
	private $datosEmpresa;
	private $periodo;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->datosEmpresa = $_SESSION['la_empresa'];
		$this->periodo = convertirFechaBd($this->datosEmpresa['periodo']);
	}
	
	public function existeCierreSPI() {
		$existe = false;
		$cadenaSql = "SELECT estciespi 
						FROM sigesp_empresa 
						WHERE codemp='".$this->datosEmpresa['codemp']."' 
						  AND estciespi=1";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF) {
				$existe = true;
				$this->mensaje .= '  -> Ya se realizo el cierre presupuestario de ingreso no se pueden registrar movimientos de este tipo';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function buscarMensaje($operacion) {
		$mensaje='';
		$cadenaSql = "SELECT previsto,aumento,disminucion,devengado,cobrado,cobrado_ant
				  		FROM spi_operaciones 
				 		WHERE operacion = '{$operacion}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF) {
				if($dataSet->fields['previsto']==1){
					$mensaje .='I';
				}
				if($dataSet->fields['aumento']==1)
				{
					$mensaje .='A';
				}
				if($dataSet->fields['disminucion']==1)
				{
					$mensaje .='D';
				}
				if($dataSet->fields['devengado']==1)
				{
					$mensaje .='E';
				}
				if($dataSet->fields['cobrado']==1)
				{
					$mensaje .='C';
				}
				if($dataSet->fields['cobrado_ant']==1)
				{
					$mensaje .='N';
				}
				$mensaje=trim($mensaje);
			}
			else {
				$this->mensaje .= '  -> No hay mensaje asociada a la operacion '.$operacion;
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $mensaje;
    }
    
	public function buscarOperacion($mensaje){
		$operacion='';
		$previsto = 0;
		$aumento = 0;
		$disminucion = 0;
		$devengado = 0;
		$cobrado = 0;
		$cobrado_ant =0;
		 
		$mensaje=strtoupper(trim($mensaje));
		if(!(strpos($mensaje,'I')===false))
		{
			$previsto=1;
		}
		if(!(strpos($mensaje,'A')===false))
		{
			$aumento=1;
		}
		if(!(strpos($mensaje,'D')===false))
		{
			$disminucion=1;
		}
		if(!(strpos($mensaje,'E')===false))
		{
			$devengado=1;
		}
		if(!(strpos($mensaje,'C')===false))
		{
			$cobrado=1;
		}
		if(!(strpos($mensaje,'N')===false))
		{
			$cobrado_ant=1;
		}
		$cadenaSql = "SELECT operacion 
						FROM spi_operaciones 
						WHERE previsto={$previsto}  
						AND aumento={$aumento} 
						AND disminucion={$disminucion} 
						AND devengado={$devengado} 
						AND cobrado={$cobrado} 
						AND cobrado_ant={$cobrado_ant}";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false){
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF) {
				$operacion = $dataSet->fields['operacion'];
			}
			else {
				$this->mensaje .= '  -> No hay operacion asociada al mensaje '.$mensaje;
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $operacion;
	}
	
	public function existeMovimiento($arrDetalleSPI){
		$existe = false;
		$cadenaSql = "SELECT spi_cuenta 
						FROM spi_dt_cmp 
						WHERE codemp='{$this->datosEmpresa['codemp']}'
						AND procede='{$arrDetalleSPI['procede']}' 
						AND comprobante='{$arrDetalleSPI['comprobante']}' 
						AND fecha='{$arrDetalleSPI['fecha']}' 
						AND codban='{$arrDetalleSPI['codban']}' 
						AND ctaban='{$arrDetalleSPI['ctaban']}' 
						AND procede_doc='{$arrDetalleSPI['procede_doc']}' 
						AND documento='{$arrDetalleSPI['documento']}' 
						AND spi_cuenta='{$arrDetalleSPI['spi_cuenta']}' 
						AND operacion='{$arrDetalleSPI['operacion']}' 
						AND codestpro1 = '{$arrDetalleSPI['codestpro1']}' 
						AND codestpro2 = '{$arrDetalleSPI['codestpro2']}' 
						AND codestpro3 = '{$arrDetalleSPI['codestpro3']}' 
						AND codestpro4 = '{$arrDetalleSPI['codestpro4']}' 
						AND codestpro5 = '{$arrDetalleSPI['codestpro5']}' 
						AND estcla     = '{$arrDetalleSPI['estcla']}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF){
				$existe = true;
			}
		}
		unset($dataSet);
		return $existe;
 	}
 	
	public function saldoSelect($cuenta, $arrDetalleSPI) {
		$arrSaldos = array();
		$joinEst = '';
		$filtroEst = '';
		if ($this->datosEmpresa['estpreing']=='1') {
			$joinEst = ' INNER JOIN spi_cuentas_estructuras CE ON C.codemp=CE.codemp AND C.spi_cuenta=CE.spi_cuenta ';
			$filtroEst = " AND CE.codestpro1='{$arrDetalleSPI['codestpro1']}' 
						   AND CE.codestpro2='{$arrDetalleSPI['codestpro2']}' 
						   AND CE.codestpro3='{$arrDetalleSPI['codestpro3']}'
						   AND CE.codestpro4='{$arrDetalleSPI['codestpro4']}'
						   AND CE.codestpro5='{$arrDetalleSPI['codestpro5']}'
						   AND CE.estcla='{$arrDetalleSPI['estcla']}' ";
		}
		$cadenaSql = "SELECT C.status,C.previsto,C.aumento,C.disminucion,C.devengado,C.cobrado,C.cobrado_anticipado 
						FROM spi_cuentas C {$joinEst}
						WHERE C.codemp='{$this->datosEmpresa['codemp']}' 
						AND C.spi_cuenta='{$cuenta}' {$filtroEst}";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF){
				$arrSaldos['estatus'] =  $dataSet->fields['status'];
				if ($arrSaldos['estatus']=='C') {
					if ($this->valido) {
						$operacion = 'previsto';
						$arrSaldos['previsto'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
					if ($this->valido) {
						$operacion = 'aumento';
						$arrSaldos['aumento'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
					if ($this->valido) {
						$operacion = 'disminucion';
						$arrSaldos['disminucion'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
					if ($this->valido) {
						$operacion = 'devengado';
						$arrSaldos['devengado'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
					if ($this->valido) {
						$operacion = 'cobrado';
						$arrSaldos['cobrado'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
					if ($this->valido) {
						$operacion = 'cobrado_ant';
						$arrSaldos['cobrado_ant'] = $this->calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion);
					}
				}
				else if ($arrSaldos['estatus']=='S') {
					$arrSaldos['previsto']    = $dataSet->fields['previsto'];
					$arrSaldos['aumento']     = $dataSet->fields['aumento'];
					$arrSaldos['disminucion'] = $dataSet->fields['disminucion'];
					$arrSaldos['devengado']   = $dataSet->fields['devengado'];
					$arrSaldos['cobrado']     = $dataSet->fields['cobrado'];
					$arrSaldos['cobrado_ant'] = $dataSet->fields['cobrado_anticipado'];
				}
			}
			else {
				$mensajeEstructura = '';
				if ($this->datosEmpresa['estpreing']=='1') {
					$mensajeEstructura = "en la estructura ::".formatoprogramatica($arrDetalleSPI['codestpro1'].$arrDetalleSPI['codestpro2'].$arrDetalleSPI['codestpro3'].$arrDetalleSPI['codestpro4'].$arrDetalleSPI['codestpro5'])."::{$this->daoDetalleSpg->estcla}";	
				}
				$this->mensaje .= "  -> La cuenta {$cuenta} no existe {$mensajeEstructura}";
				$this->valido = false;
			}
		}
		
		return $arrSaldos;
	}
	
	public function calcularSaldoRango($cuenta, $arrDetalleSPI, $operacion) {
		$monto=0;
		$cadenaSql="SELECT COALESCE(SUM(monto),0) As monto 
						FROM spi_dt_cmp, spi_operaciones
						WHERE codemp='{$this->datosEmpresa['codemp']}' 
						AND spi_operaciones.{$operacion}=1 
						AND spi_dt_cmp.spi_cuenta = '{$cuenta}' 
						AND fecha >='{$this->periodo}' AND fecha <='{$arrDetalleSPI['fecha']}' 
						AND spi_dt_cmp.codestpro1='{$arrDetalleSPI['codestpro1']}' 
						AND spi_dt_cmp.codestpro2='{$arrDetalleSPI['codestpro2']}' 
						AND spi_dt_cmp.codestpro3='{$arrDetalleSPI['codestpro3']}' 
						AND spi_dt_cmp.codestpro4='{$arrDetalleSPI['codestpro4']}' 
						AND spi_dt_cmp.codestpro5='{$arrDetalleSPI['codestpro5']}' 
						AND spi_dt_cmp.estcla='{$arrDetalleSPI['estcla']}' 
						AND spi_dt_cmp.operacion=spi_operaciones.operacion ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF){
				$monto = number_format($dataSet->fields['monto'],2,'.','');
			}
		}
		unset($dataSet);
		return $monto;
	}
	
	public function saldosAjusta($mensaje, $arrSaldoMov, $montoAnterior, $montoActual) {
		$arrSaldoAjustado = array();
		$procesado=false;
		$mensaje=trim(strtoupper($mensaje));
		$posI = strpos($mensaje,"I"); 
		$this->valido=true;
                $arrSaldoAjustado['previsto'] = $arrSaldoMov['previsto'];
                $arrSaldoAjustado['aumento'] = $arrSaldoMov['aumento'];
                $arrSaldoAjustado['disminucion'] = $arrSaldoMov['disminucion'];
                $arrSaldoAjustado['devengado'] = $arrSaldoMov['devengado'];
                $arrSaldoAjustado['cobrado'] = $arrSaldoMov['cobrado'];
                $arrSaldoAjustado['cobrado_ant'] = $arrSaldoMov['cobrado_ant'];
		if(!($posI===false)) { //I-Previsto 
			$arrSaldoAjustado['previsto'] = $arrSaldoMov['previsto'] - $montoAnterior+$montoActual;
			$procesado=true;
		}
		$posA = strpos($mensaje,"A"); 
		if(!($posA===false)) { //A-Aumento
			$arrSaldoAjustado['aumento'] = $arrSaldoMov['aumento'] - $montoAnterior+$montoActual;
			$procesado=true;
		}
		$posD = strpos($mensaje,"D"); 
		if(!($posD===false))//D-Disminución
                { 
			if(round($montoActual,2)<=round($arrSaldoMov['previsto'],2)) {
				$arrSaldoAjustado['disminucion'] = $arrSaldoMov['disminucion'] - $montoAnterior+ $montoActual;
			}
			else {
				$this->mensaje .= '  ->El monto a disminuir '.round($montoActual,2).' es mayor que el previsto '.round($arrSaldoMov['previsto'],2).'.';
				$this->valido = false;			
			}
			$procesado=true;
		}
		$posE = strpos($mensaje,"E"); 
		if(!($posE===false)) { //E-Devengado 
			$arrSaldoAjustado['devengado'] = $arrSaldoMov['devengado'] - $montoAnterior+$montoActual;
			$procesado = true;
		}
		$posC = strpos($mensaje,"C"); 
		if(!($posC===false)) {//C-Cobrado
			$arrSaldoAjustado['cobrado'] = $arrSaldoMov['cobrado'] - $montoAnterior+$montoActual;
			$procesado = true;
		}
		$posN = strpos($mensaje,"N"); 
		if(!($posN===false)) {//N-Cobrado Anticipado 
			$arrSaldoAjustado['cobrado_ant'] = $arrSaldoMov['cobrado_ant'] - $montoAnterior+$montoActual;
			$procesado=true;
		}
		if(!$procesado)
		{
			$this->mensaje .= '  ->El codigo de operacion es Invalido.';
			$this->valido = false;
		}
		
		return $arrSaldoAjustado;
    }
    
	public function saldosUpdate($cuenta, $arrSaldo) {
		$cadenaSql="UPDATE spi_cuentas 
					   SET previsto={$arrSaldo['previsto']}, aumento={$arrSaldo['aumento']},
					       disminucion={$arrSaldo['disminucion']}, devengado={$arrSaldo['devengado']},
					       cobrado={$arrSaldo['cobrado']}, cobrado_anticipado={$arrSaldo['cobrado_ant']}  
					 WHERE codemp='{$this->datosEmpresa['codemp']}' 
					   AND spi_cuenta='{$cuenta}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $this->valido;
	}
	
	public function actualizarSaldo($arrDetalleSPI, $montoActual, $montoAnterior = 0) {
		$nextCuenta=$arrDetalleSPI['spi_cuenta'];
		$nivel = obtenerNivelPlus($nextCuenta, $this->datosEmpresa['formspi']);
		while(($nivel >= 1)&&($this->valido)&&($nextCuenta!='')) {
			$arrSaldo = $this->saldoSelect($nextCuenta, $arrDetalleSPI);
			if($this->valido) {
				$mensaje = $this->buscarMensaje($arrDetalleSPI['operacion']);
				$arrSaldoAjusta = $this->saldosAjusta($mensaje, $arrSaldo, $montoAnterior, $montoActual);
				if($this->valido){
					if(!$this->saldosUpdate($nextCuenta, $arrSaldoAjusta)) {
						$this->valido = false;
					}
				}
				else {
					$this->valido = false;
				}
			}
			else {
				$this->valido = false;
			}
			if(obtenerNivelPlus($nextCuenta, $this->datosEmpresa['formspi']) == 1) {
				break;
			}
			$nextCuenta = obtenerCuentaSiguientePlus($nextCuenta, $this->datosEmpresa['formspi']);
			$nivel = obtenerNivelPlus($nextCuenta, $this->datosEmpresa['formspi']);
		}
		return $this->valido;
	}
	
	public function guardarDetalleSPI($daoComprobante, $arrDetalleSPI, $arreEvento)
	{
		if(!$this->existeCierreSPI())
		{
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arreEvento['evento'];
			$servicioEvento->codemp=$arreEvento['codemp'];
			$servicioEvento->codsis=$arreEvento['codsis'];
			$servicioEvento->nomfisico=$arreEvento['nomfisico'];
			$totalSPI = count((array)$arrDetalleSPI);
			for($i=0;($i<$totalSPI)&&($this->valido);$i++)
			{
				if ($arrDetalleSPI[$i]['mensaje']=='')
				{
					$arrDetalleSPI[$i]['mensaje'] = trim(strtoupper($this->buscarMensaje($arrDetalleSPI[$i]['operacion'])));					
				}
				if ($arrDetalleSPI[$i]['operacion']=='')
				{
					$arrDetalleSPI[$i]['operacion'] = trim(strtoupper($this->buscarOperacion($arrDetalleSPI[$i]['mensaje'])));					
				}
				if((is_null($arrDetalleSPI[$i]['documento'])) or (empty($arrDetalleSPI[$i]['documento']))) {
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($arrDetalleSPI[$i]['procede_doc'])) or (empty($arrDetalleSPI[$i]['procede_doc']))) {
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($arrDetalleSPI[$i]['descripcion'])) or (empty($arrDetalleSPI[$i]['descripcion']))) {
					$this->mensaje .= 'La Descripcion no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if(!$this->existeMovimiento($arrDetalleSPI[$i]))
				{
					if($this->actualizarSaldo($arrDetalleSPI[$i], $arrDetalleSPI[$i]['monto'])) {
						$this->daoDetalleSpi = FabricaDao::CrearDAO('N','spi_dt_cmp');
						$this->setDaoDetalleSPI($arrDetalleSPI[$i]);
						$this->daoDetalleSpi->codcencos='---';
						$this->daoDetalleSpi->comprobante = $daoComprobante->comprobante ;
						$this->valido=$this->daoDetalleSpi->incluir();
						if(!$this->valido) {
							$this->mensaje .= $this->daoDetalleSpi->ErrorMsg;
						}
						$servicioEvento->tipoevento=$this->valido; 
						if($this->valido) {
							$servicioEvento->desevetra = 'Incluyo detalle presupuestario ';
							if ($this->datosEmpresa['estpreing']=='1') {
								$servicioEvento->desevetra .= $this->daoDetalleSpi->codestpro1.'::'.$this->daoDetalleSpi->codestpro2.'::'.
								                              $this->daoDetalleSpi->codestpro3.'::'.$this->daoDetalleSpi->codestpro4.'::'.
								                              $this->daoDetalleSpi->codestpro5.'::'.$this->daoDetalleSpi->estcla;
								
							}
							$servicioEvento->desevetra .= ' del comprobante '.$this->daoDetalleSpi->codemp.'::'.$this->daoDetalleSpi->procede.'::'.
							                              $this->daoDetalleSpi->comprobante.'::'.$this->daoDetalleSpi->codban.'::'.
							                              $this->daoDetalleSpi->ctaban;			
							$servicioEvento->incluirEvento();
						}
						else {
							$this->valido=false;
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}							
					}
					else {
						$this->valido=false;
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else {
					$this->valido=false;
					$this->mensaje .= ' -> El movimiento Ya existe.';
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
				unset($this->daoDetalleSpi);
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function validaIntegridadComprobanteAjuste($daoComprobante, $arrDetalleSPI) {
		$existe = false;
		$cadenaSql="SELECT D.procede As procede,D.comprobante As comprobante,D.fecha as fecha 
						FROM spi_dt_cmp D 
							INNER JOIN sigesp_cmp C ON D.codemp=C.codemp AND D.procede=C.procede
							 						   AND D.comprobante=C.comprobante AND D.fecha=C.fecha 
							 						   AND C.codban=D.codban AND C.ctaban=D.ctaban 
						WHERE D.codemp='{$arrDetalleSPI['codemp']}'
						AND D.comprobante='{$arrDetalleSPI['comprobante']}' 
						AND D.procede_doc='{$arrDetalleSPI['procede_doc']}' 
						AND D.spi_cuenta ='{$arrDetalleSPI['spi_cuenta']}' 
						AND D.operacion='{$arrDetalleSPI['operacion']}' 
						AND D.monto<0
						AND C.tipo_destino='{$daoComprobante->tipo_destino}' 
						AND C.tipo_comp=1 
						AND C.cod_pro='{$daoComprobante->cod_pro}' 
						AND C.ced_bene='{$daoComprobante->ced_bene}' 
						AND C.codban='{$arrDetalleSPI['codban']}' 
						AND C.ctaban='{$arrDetalleSPI['ctaban']}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF) {
				$existe=true;
				$this->valido = false;
				$this->mensaje .= 'El Comprobante '.$daoComprobante->codemp.'::'.$daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.$daoComprobante->codban.'::'.$daoComprobante->ctaban.' Esta referenciado en otro.';
			}
		}
		return $existe;
	}
	
	public function validaIntegridadComprobanteOtros($daoComprobante, $arrDetalleSPI) {
		$existe = false;
		$mensaje = $arrDetalleSPI['mensaje'];
		// caso exepcional
 	    $posE=strpos($mensaje,"E");
		$posC=strpos($mensaje,"C");
        if (!($posE===false) and !($posC===false)) {
        	return true;
		}
		$cadenaIncluir="";
	    $cadenaExcluir="";
		$posE=strpos($mensaje,"E");
	    if(!($posE===false)) {
	    	$cadenaExcluir=$cadenaExcluir."O.devengado=0 AND ";
		}
		$posC=strpos($mensaje,"C");
	    if(!($posC===false)) {
	    	$cadenaExcluir=$cadenaExcluir."O.cobrado=0 AND ";
		}
 		else {
 			$cadenaIncluir=$cadenaIncluir."O.cobrado=1 OR ";
		}
        $condicion="";         
        if(!empty($cadenaExcluir)) {
        	$cadenaExcluir = "(".substr($cadenaExcluir,0,strlen($cadenaExcluir)- 4).")";
            $condicion =$condicion.$cadenaExcluir." AND ";
		}
        if(!empty($cadenaIncluir)) {
        	$cadenaIncluir = "(".substr($cadenaIncluir,0,strlen($cadenaIncluir)- 3).")";
            $condicion =$condicion.$cadenaIncluir." AND ";
		}
		$cadenaSql="SELECT D.procede As procede,D.comprobante As comprobante,D.fecha as fecha
						FROM spi_dt_cmp D,sigesp_cmp C,spi_operaciones O 
						WHERE C.codemp='{$arrDetalleSPI['codemp']}' 
						AND D.comprobante='{$arrDetalleSPI['comprobante']}' 
						AND tipo_destino='{$daoComprobante->tipo_destino}' 
						AND C.cod_pro='{$daoComprobante->cod_pro}' 
						AND C.ced_bene='{$daoComprobante->ced_bene}'
						AND C.codban='{$arrDetalleSPI['codban']}' 
						AND C.ctaban='{$arrDetalleSPI['ctaban']}' 
						AND D.procede_doc='{$arrDetalleSPI['procede_doc']}' 
						AND D.spi_cuenta ='{$arrDetalleSPI['spi_cuenta']}' 
						AND D.operacion='{$arrDetalleSPI['operacion']}' 
						AND {$condicion} monto>0 
						AND C.tipo_comp=1 
						AND D.codemp=C.codemp 
						AND D.procede=C.procede 
						AND D.comprobante=C.comprobante 
						AND D.fecha=C.fecha 
						AND C.codban=D.codban 
						AND C.ctaban=D.ctaban 
						AND D.operacion=O.operacion";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else {
			if (!$dataSet->EOF) {
				$existe=true;
				$this->valido = false;
				$this->mensaje .= 'El Comprobante '.$daoComprobante->codemp.'::'.$daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.$daoComprobante->codban.'::'.$daoComprobante->ctaban.' Esta referenciado en otro.';
			}
		}
		return $existe;
	}
	 
	public function eliminarDetalleSPI($daoComprobante, $arrDetalleSPI, $arreEvento) {
		if(!$this->existeCierreSPI()){
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arreEvento['evento'];
			$servicioEvento->codemp=$arreEvento['codemp'];
			$servicioEvento->codsis=$arreEvento['codsis'];
			$servicioEvento->nomfisico=$arreEvento['nomfisico'];
			$totalSPI = count((array)$arrDetalleSPI);
			for($i=1;($i<=$totalSPI)&&($this->valido);$i++) {
				if ($arrDetalleSPI[$i]['mensaje']=='') {
					$arrDetalleSPI[$i]['mensaje'] = trim(strtoupper($this->buscarMensaje($arrDetalleSPI[$i]['operacion'])));					
				}
				if ($arrDetalleSPI[$i]['operacion']=='') {
					$arrDetalleSPI[$i]['operacion'] = trim(strtoupper($this->buscarOperacion($arrDetalleSPI[$i]['mensaje'])));					
				}
				
				if((is_null($arrDetalleSPI[$i]['documento'])) or (empty($arrDetalleSPI[$i]['documento']))) {
					$this->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($arrDetalleSPI[$i]['procede_doc'])) or (empty($arrDetalleSPI[$i]['procede_doc']))) {
					$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if((is_null($arrDetalleSPI[$i]['descripcion'])) or (empty($arrDetalleSPI[$i]['descripcion']))) {
					$this->mensaje .= 'La Descripcion no puede tener valor nulo o vacio.';			
					$this->valido = false;	
				}
				if($this->existeMovimiento($arrDetalleSPI[$i])) {
					if(!$this->validaIntegridadComprobanteAjuste($daoComprobante, $arrDetalleSPI)) {
						if(!$this->validaIntegridadComprobanteOtros($daoComprobante, $arrDetalleSPI)) {
							if($this->actualizarSaldo($arrDetalleSPI[$i], 0, $arrDetalleSPI[$i]['monto'])) {
								$criterio=" codemp = '".$arrDetalleSPI[$i]['codemp']."'".
								          " AND procede = '".$arrDetalleSPI[$i]['procede']."' ".
										  " AND comprobante = '".$arrDetalleSPI[$i]['comprobante']."' ".
										  " AND codban = '".$arrDetalleSPI[$i]['codban']."' ".
										  " AND ctaban = '".$arrDetalleSPI[$i]['ctaban']."' ".
										  " AND spi_cuenta = '".$arrDetalleSPI[$i]['spi_cuenta']."' ".
										  " AND procede_doc = '".$arrDetalleSPI[$i]['procede_doc']."' ".
										  " AND documento = '".$arrDetalleSPI[$i]['documento']."' ".
										  " AND operacion = '".$arrDetalleSPI[$i]['operacion']."' ".		
										  " AND codestpro1 = '".$arrDetalleSPI[$i]['codestpro1']."' ".
										  " AND codestpro2 = '".$arrDetalleSPI[$i]['codestpro2']."' ".
										  " AND codestpro3 = '".$arrDetalleSPI[$i]['codestpro3']."' ".
										  " AND codestpro4 = '".$arrDetalleSPI[$i]['codestpro4']."' ".
										  " AND codestpro5 = '".$arrDetalleSPI[$i]['codestpro5']."' ".
										  " AND estcla = '".$arrDetalleSPI[$i]['estcla']."'";					  
								$this->daoDetalleSpi = FabricaDao::CrearDAO('C','spi_dt_cmp','',$criterio);
								$this->valido=$this->daoDetalleSpi->eliminar('','',true);
								if(!$this->valido) {
									$this->mensaje .= $this->daoDetalleSpi->ErrorMsg;
								}
								$servicioEvento->tipoevento=$this->valido; 
								if($this->valido) {
									$servicioEvento->desevetra='Elimino detalle presupuestario ';
									if ($this->datosEmpresa['estpreing']=='1') {
										$servicioEvento->desevetra .= $this->daoDetalleSpi->codestpro1.'::'.$this->daoDetalleSpi->codestpro2.'::'.
								                              $this->daoDetalleSpi->codestpro3.'::'.$this->daoDetalleSpi->codestpro4.'::'.
								                              $this->daoDetalleSpi->codestpro5.'::'.$this->daoDetalleSpi->estcla.'::';
								
									}
									$servicioEvento->desevetra .= $this->daoDetalleSpi->spi_cuenta.'::'.$this->daoDetalleSpi->procede_doc.'::'.
															   $this->daoDetalleSpi->documento.'::'.$this->daoDetalleSpi->operacion.'::'.
															   $this->daoDetalleSpi->fecha.'::'.$this->daoDetalleSpi->monto.
															   ' del comprobante '.$daoComprobante->codemp.'::'.
															   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
															   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
									$servicioEvento->incluirEvento();
								}
								else {
									$this->valido=false;
									$servicioEvento->desevetra=$this->mensaje;
									$servicioEvento->incluirEvento();
								}
							}	
							else {
								$this->valido=false;
								$servicioEvento->tipoevento=$this->valido; 
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
								
							}						
						}
						else {
							$this->valido=false;
							$servicioEvento->tipoevento=$this->valido; 
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else {
						$this->valido=false;
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else {
					$this->valido=false;
					$this->mensaje = 'El movimiento presupuestario no existe';
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
					
				
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function setDaoDetalleSPI($arrDetalleSPI) {
		$this->daoDetalleSpi->codemp      = $arrDetalleSPI['codemp'];
		$this->daoDetalleSpi->procede     = $arrDetalleSPI['procede'];
		$this->daoDetalleSpi->comprobante = $arrDetalleSPI['comprobante'];
		$this->daoDetalleSpi->codban      = $arrDetalleSPI['codban'];
		$this->daoDetalleSpi->ctaban      = $arrDetalleSPI['ctaban'];
		$this->daoDetalleSpi->spi_cuenta  = $arrDetalleSPI['spi_cuenta'];
		$this->daoDetalleSpi->procede_doc = $arrDetalleSPI['procede_doc'];
		$this->daoDetalleSpi->documento   = $arrDetalleSPI['documento'];
		$this->daoDetalleSpi->operacion   = $arrDetalleSPI['operacion'];
		$this->daoDetalleSpi->codestpro1  = $arrDetalleSPI['codestpro1'];
		$this->daoDetalleSpi->codestpro2  = $arrDetalleSPI['codestpro2'];
		$this->daoDetalleSpi->codestpro3  = $arrDetalleSPI['codestpro3'];
		$this->daoDetalleSpi->codestpro4  = $arrDetalleSPI['codestpro4'];
		$this->daoDetalleSpi->codestpro5  = $arrDetalleSPI['codestpro5'];
		$this->daoDetalleSpi->estcla      = $arrDetalleSPI['estcla'];
		$this->daoDetalleSpi->fecha       = $arrDetalleSPI['fecha'];
		$this->daoDetalleSpi->descripcion = $arrDetalleSPI['descripcion'];
		$this->daoDetalleSpi->monto       = $arrDetalleSPI['monto'];
		$this->daoDetalleSpi->orden       = $arrDetalleSPI['orden'];		
	}
}
?>