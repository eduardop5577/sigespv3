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
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsrm.php");
require_once ($dirsrv."/modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php");

class ServicioIntegracionSRM implements IIntegracionSRM
 {
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $servicioComprobante;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->monto=0;
	}

	public function buscarCobranzas($numcom,$fecha,$estatus ) 
	{
		$parametrosBusqueda = '';
		if($fecha != '')
		{
			$fecha = convertirFechaBd($fecha);
			$parametrosBusqueda .= " AND mis_sigesp_banco.fecha ='{$fecha}'";
		}
		
		if($numcom != '') 
		{
			$parametrosBusqueda .= " AND mis_sigesp_banco.comprobante like '%{$numcom}%'";
		}
		
		$cadenaSQL = "SELECT mis_sigesp_banco.comprobante,mis_sigesp_banco.fecdep,mis_sigesp_banco.procede,".
		             "        mis_sigesp_banco.codban,mis_sigesp_banco.ctaban,scb_banco.nomban,  ".
		             "        MAX(mis_sigesp_banco.descripcion) AS descripcion, MAX(mis_sigesp_banco.fechaconta) as fechaconta,".
		             "        MAX(mis_sigesp_banco.fechaanula) as fechaanula". 
					 "  FROM mis_sigesp_banco".
		             " INNER JOIN scb_banco USING(codban)".
					 " WHERE mis_sigesp_banco.procede = 'SRMCOB'".
				     "   AND mis_sigesp_banco.estint = ".$estatus." ".
				 	 "   AND mis_sigesp_banco.documento = '000000000000001'".
					 $parametrosBusqueda.
					 " GROUP BY mis_sigesp_banco.comprobante,mis_sigesp_banco.procede,mis_sigesp_banco.fecdep,".
					 "          mis_sigesp_banco.codban,mis_sigesp_banco.ctaban,scb_banco.nomban".
			    	 " ORDER BY mis_sigesp_banco.fecdep,mis_sigesp_banco.comprobante";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->buscarCobranzas ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function obtenerDetalleIngreso($comprobante,$procede,$fecha,$codban,$ctaban)
	{
		$fecha=convertirFechaBd($fecha);
		$cadenaSQL = "SELECT mis_sigesp_banco.sc_cuenta as spi_cuenta, sum(monto) AS monto, MAX(spi_cuentas.denominacion) AS denominacion ".
				     "  FROM mis_sigesp_banco ".
					 " INNER JOIN spi_cuentas ". 
					 "    ON spi_cuentas.codemp='{$this->codemp}' ".
					 "   AND mis_sigesp_banco.sc_cuenta = spi_cuentas.spi_cuenta ". 
				     " WHERE comprobante='{$comprobante}' ". 
					 "   AND procede='".$procede."'".
				     "   AND fecdep='".$fecha."'".
				     "   AND codban='".$codban."'".
				     "   AND ctaban='".$ctaban."'".
				     "   AND modulo='SPI'".
					 " GROUP BY mis_sigesp_banco.sc_cuenta ".
				     " ORDER BY mis_sigesp_banco.sc_cuenta";
		$dataIng = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataIng===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->buscarDetalleIngreso ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataIng;
	}
	
	public function obtenerDetalleContable($comprobante,$procede,$fecha,$codban,$ctaban) 
	{
		$arreglo=array();
		$fecha=convertirFechaBd($fecha);
		$cadenaSQL = "SELECT mis_sigesp_banco.sc_cuenta, debhab, sum(monto) as monto, max(scg_cuentas.denominacion) as denominacion ".
				     "  FROM mis_sigesp_banco".
				     " INNER JOIN scg_cuentas ". 
				     "    ON scg_cuentas.codemp = '{$this->codemp}' ". 
				     "   AND mis_sigesp_banco.sc_cuenta = scg_cuentas.sc_cuenta ". 
				     " WHERE comprobante='{$comprobante}' ". 
					 "   AND procede='".$procede."'".
				     "   AND fecdep='".$fecha."'".
				     "   AND codban='".$codban."'".
				     "   AND ctaban='".$ctaban."'".
				     "   AND modulo='SCG'".
					 " GROUP BY mis_sigesp_banco.sc_cuenta, debhab ".
				     " ORDER BY debhab";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->buscardetalleContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				if($data->fields['debhab']=='D')
				{
					$arreglo[$i]['cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['denominacion']=utf8_encode($data->fields['denominacion']);
					$arreglo[$i]['debe']=number_format($data->fields['monto'],2,",",".");
				}
				elseif($data->fields['debhab']=='H')
				{
					$arreglo[$i]['cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['denominacion']=utf8_encode($data->fields['denominacion']);
					$arreglo[$i]['haber']=number_format($data->fields['monto'],2,",",".");
				}
				$data->MoveNext();
				$i++;
			}
		}
		return $arreglo;
	}
	
	function buscarCuentaPresupuestaria($cuenta,$campo,$tabla)
	{
		$arrRepuesta=array();
		$this->valido=false;
		$cadenaSql="SELECT $campo,status,denominacion".
				   "  FROM $tabla".
		   		   " WHERE codemp='".$this->codemp."'".
				   "   AND trim($campo)= '".trim($cuenta)."'" ;
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->buscarCuentaPresupuestaria ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$this->valido=true;
				$arrRepuesta['denominacion']=$data->fields['denominacion'];
				$arrRepuesta['status']=$data->fields['status'];
				$arrRepuesta[$campo]=$data->fields[$campo];
			}
		}
		unset($data);
		return $this->valido;
	}	
	
	public function cargarDetalleIngreso($comprobante,$procede,$fecha,$codban,$ctaban,$arrcabecera)
	{
		$arregloSPI=array();
		$fecha=convertirFechaBd($fecha);
		$cadenaSQL = "SELECT sc_cuenta as spi_cuenta, modulo, monto, numdoc, descripcion".
				     "  FROM mis_sigesp_banco".
				     "  WHERE comprobante='".$comprobante."'".
				     "    AND procede='".$procede."'".
				     "    AND fecdep='".$fecha."'".
				     "    AND codban='".$codban."'".
				     "    AND ctaban='".$ctaban."'".
				     "    AND modulo='SPI'".
				     "  ORDER BY sc_cuenta";
		$dataIng = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataIng===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->cargarDetalleIngreso ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataIng->EOF)
			{
				$i=0;
				while((!$dataIng->EOF) && ($this->valido))
				{
					if($this->buscarCuentaPresupuestaria($dataIng->fields['spi_cuenta'],'spi_cuenta','spi_cuentas'))
					{
						$arregloSPI['codemp'][$i]=$arrcabecera['codemp'];
						$arregloSPI['procede'][$i]= $arrcabecera['procede'];
						$arregloSPI['comprobante'][$i]= $arrcabecera['comprobante'];
						$arregloSPI['codban'][$i]= $arrcabecera['codban'];
						$arregloSPI['ctaban'][$i]= $arrcabecera['ctaban'];
						$arregloSPI['fecha'][$i]= $arrcabecera['fecha'];
						$arregloSPI['descripcion'][$i]= $arrcabecera['descripcion'];
						$arregloSPI['orden'][$i]= $i;
						$arregloSPI['estcla'][$i]='-';
						$arregloSPI['codestpro1'][$i]='-------------------------';
						$arregloSPI['codestpro2'][$i]='-------------------------';
						$arregloSPI['codestpro3'][$i]='-------------------------';
						$arregloSPI['codestpro4'][$i]='-------------------------';
						$arregloSPI['codestpro5'][$i]='-------------------------';
						$arregloSPI['spicuenta'][$i]=trim($dataIng->fields['spi_cuenta']);
						$arregloSPI['procede_doc'][$i]= $procede;
						$arregloSPI['documento'][$i]= $dataIng->fields['numdoc'];
						$arregloSPI['operacion'][$i]= 'DC';
						$arregloSPI['desmov'][$i]= $dataIng->fields['descripcion'];
						$arregloSPI['monto'][$i]=$dataIng->fields['monto'];
						$this->monto += $dataIng->fields['monto'];
					}
					else
					{
						$this->valido=false;
						$this->mensaje .='La cuenta '.$dataIng->fields['spi_cuenta'].'No existe en el plan de cuentas.';
					}
					$i++;
					$dataIng->MoveNext();
				}	
			}
		}
		return $arregloSPI;
	}
	
	public function cargarDetalleContable($comprobante,$procede,$fecha,$codban,$ctaban,$arrcabecera)
	{
		$arrDetalleScg=array();
		$fecha=convertirFechaBd($fecha);
		$sumdebe=0;
		$sumhaber=0;		
		$cadenaSQL = "SELECT sc_cuenta,debhab,monto,descripcion,numdoc,documento".
				     "  FROM mis_sigesp_banco".
				     " WHERE comprobante='".$comprobante."'".
				     "   AND procede='".$procede."'".
				     "   AND fecdep='".$fecha."'".
				     "   AND codban='".$codban."'".
				     "   AND ctaban='".$ctaban."'". 
				     "   AND modulo='SCG'".
				     " ORDER BY debhab";
		$dataCont = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($dataCont===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->cargarDetalleContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataCont->EOF)
			{
				$i=0;
				while((!$dataCont->EOF) && ($this->valido))
				{
					if($this->buscarCuentaPresupuestaria($dataCont->fields['sc_cuenta'],'sc_cuenta','scg_cuentas'))
					{
						$arrDetalleScg["scg_cuenta"][$i]  = trim($dataCont->fields['sc_cuenta']);
						$arrDetalleScg["procede_doc"][$i] = $procede;
						$arrDetalleScg["desmov"][$i] = $dataCont->fields['descripcion'];
						$arrDetalleScg["documento"][$i]	= $dataCont->fields['numdoc'];
						$arrDetalleScg["debhab"][$i] = $dataCont->fields['debhab'];
						$arrDetalleScg["monto"][$i]	= number_format($dataCont->fields['monto'],2,'.','');
						$arrDetalleScg["monobjret"][$i] = number_format(0,2,'.','');
						$arrDetalleScg["codded"][$i] = '00000';
						if($arrDetalleScg["debhab"][$i]=='D')
						{
							$sumdebe=number_format($sumdebe+$arrDetalleScg["monto"][$i],2,'.','');
						}
						else
						{
							$sumhaber=number_format($sumhaber+$arrDetalleScg["monto"][$i],2,'.','');						
						}
					}
					else
					{
						$this->valido=false;
						$this->mensaje .='La cuenta '.$dataCont->fields['sc_cuenta'].'No existe en el plan de cuentas.';
					}
					$i++;
					$dataCont->MoveNext();
				}
			}
			if($sumdebe<>$sumhaber)
			{
				$this->valido=false;
				$this->mensaje .='El Movimiento esta descuadrado contablemente Debe '.$sumdebe.' Haber '.$sumhaber.'.Favor Verifique.';
			}
		}
		return $arrDetalleScg;
	}
	
	public function actualizarEstatus($estatus,$comprobante_sigesp,$fechaconta,$fechaanula,$comprobante,$procede,$fecha,$codban,$ctaban)
	{
		$cadenaSQL = "UPDATE mis_sigesp_banco".
					" SET estint=".$estatus.",".
					"     comprobante_sigesp='".$comprobante_sigesp."',".
					"     fechaconta='".$fechaconta."',".
					"     fechaanula='".$fechaanula."'".
					" WHERE comprobante='".$comprobante."'".
					"   AND procede='".$procede."'".
					"   AND fecdep='".$fecha."'".
					"   AND codban='".$codban."'".
					"   AND ctaban='".$ctaban."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->actualizarEstatus ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $this->valido;
	}
	
	public function eliminarMovBco($arrcabecera,$comprobante_sigesp)
	{
		$this->valido=true;
		$arreglo=array();
		$arreglo[0]='scb_movbco_spi';
		$arreglo[1]='scb_movbco_scg';
		$arreglo[2]='scb_movbco_fuefinanciamiento';
		$arreglo[3]='scb_movbco'; 
		$i=0;
		while($i<4 && $this->valido)
		{
			$cadenaSQL="DELETE ".
				       "  FROM $arreglo[$i]".
				       " WHERE codemp='".$this->codemp."'".
				       "   AND codban='".$arrcabecera['codban']."'".
				       "   AND ctaban='".$arrcabecera['ctaban']."'".
				       "   AND numdoc='".$comprobante_sigesp."'".
				       "   AND codope='".$arrcabecera['codope']."'".
				       "   AND estmov='".$arrcabecera['estmov']."'";
			$data = $this->conexionBaseDatos->Execute($cadenaSQL);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SRM METODO->eliminarMovBco ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			$i++;
			unset($data);
		}
		return $this->valido;
	}
	
	public function procesoContabilizarSRM($objson)
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
			$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
			$arrevento['desevetra'] = "Contabilizar el resumen de cobranza Nº {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSRM($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Resumen de cobranza ha sido contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Resumen de cobranza no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSRM($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$arrdetallespi=array();
		$arrdetallescg=array();
		$this->monto=0;
		$comprobante_sigesp=fillComprobante($objson->arrDetalle[$j]->comprobante);
		$procede=$objson->arrDetalle[$j]->procede;
		$fecha=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		$codban=$objson->arrDetalle[$j]->codban;
		$ctaban=$objson->arrDetalle[$j]->ctaban;
		$descripcion=$objson->arrDetalle[$j]->descripcion;
		if ($this->valido)
		{
			$arrCabecera["codemp"]	 = $this->codemp;
			$arrCabecera["codban"]	 = $codban;
			$arrCabecera["ctaban"]	 = $ctaban;
			$arrCabecera["numdoc"]	 = $comprobante_sigesp;
			$arrCabecera["codope"]	 = 'DP';
			$arrCabecera["fecmov"]	 = $fecha;
			$arrCabecera["conmov"]	 = $descripcion;
			$arrCabecera["codconmov"]= '---';
			$arrCabecera["cod_pro"]	 = '----------';
			$arrCabecera["ced_bene"] = '----------';
			$arrCabecera["nomproben"]= 'Ninguno';
			$arrCabecera["monobjret"] = number_format(0,2,'.','');
			$arrCabecera["monret"]	 = number_format(0,2,'.','');
			$arrCabecera["chevau"]	 = "";
			$arrCabecera["estmov"]	 = 'N';
			$arrCabecera["estmovint"] = 0;
			$arrCabecera["estcobing"] = 0;
			$arrCabecera["estbpd"]	 = 'M';
			$arrCabecera["procede"]	 = "";
			$arrCabecera["estreglib"] = "";
			$arrCabecera["tipo_destino"] = '-';
			$arrCabecera["numordpagmin"] = '-';
			$arrCabecera["codfuefin"] = '--';
			$arrCabecera["codtipfon"] = '----';
			$arrCabecera["estmovcob"] = 0;
			$arrCabecera["numconint"] = "";
			$arrCabecera["tranoreglib"] = "";
			$arrCabecera["numchequera"] = "";
			$arrCabecera["codbansig"] = "";
			$arrCabecera["estmodordpag"] = 0;
			$arrCabecera["numconint"] = null;
			$arrdetallespi=$this->cargarDetalleIngreso($comprobante_sigesp,$procede,$fecha,$codban,$ctaban);
		}
		if($this->valido)
		{
			$arrdetallescg=$this->cargarDetalleContable($comprobante_sigesp,$procede,$fecha,$codban,$ctaban);
		}
		if ($this->valido)
		{
			$arrCabecera["monto"] = number_format($this->monto,2,'.','');
			$servicioBancario = new ServicioMovimientoScb();
			$this->valido = $servicioBancario->GuardarAutomatico($arrCabecera,$arrdetallescg,null,$arrdetallespi,$arrevento);
			$this->mensaje.= $servicioBancario->mensaje;
			unset($servicioBancario);
			$this->valido=$this->actualizarEstatus(1,$comprobante_sigesp,$fecha,'1900-01-01',$objson->arrDetalle[$j]->comprobante,$procede,$fecha,$codban,$ctaban);

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
	
	public function procesoRevContabilizarSRM($objson)
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
			$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
			$arrevento['desevetra'] = "Reversar el resumen de cobranza Nº {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->reversarSRM($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Resumen de cobranza ha sido reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Resumen de cobranza no fue reversado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarSRM($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$arrcabecera=array();
		$arrdetallespi=array();
		$comprobante_sigesp=fillComprobante($objson->arrDetalle[$j]->comprobante);
		$procede=$objson->arrDetalle[$j]->procede;
		$fecha=convertirFechaBd($objson->arrDetalle[$j]->fecha);
		$codban=$objson->arrDetalle[$j]->codban;
		$ctaban=$objson->arrDetalle[$j]->ctaban;
		$descripcion=$objson->arrDetalle[$j]->descripcion;
		if ($this->valido)
		{
			$arrCabecera["codemp"]	 = $this->codemp;
			$arrCabecera["codban"]	 = $codban;
			$arrCabecera["ctaban"]	 = $ctaban;
			$arrCabecera["numdoc"]	 = $comprobante_sigesp;
			$arrCabecera["codope"]	 = 'DP';
			$arrCabecera["fecmov"]	 = $fecha;
			$arrCabecera["conmov"]	 = $descripcion;
			$arrCabecera["codconmov"]= '---';
			$arrCabecera["cod_pro"]	 = '----------';
			$arrCabecera["ced_bene"] = '----------';
			$arrCabecera["nomproben"]= 'Ninguno';
			$arrCabecera["monobjret"] = number_format(0,2,'.','');
			$arrCabecera["monret"]	 = number_format(0,2,'.','');
			$arrCabecera["chevau"]	 = "";
			$arrCabecera["estmov"]	 = 'N';
			$arrCabecera["estmovint"] = 0;
			$arrCabecera["estcobing"] = 0;
			$arrCabecera["estbpd"]	 = 'M';
			$arrCabecera["procede"]	 = "";
			$arrCabecera["estreglib"] = "";
			$arrCabecera["tipo_destino"] = '-';
			$arrCabecera["numordpagmin"] = '-';
			$arrCabecera["codfuefin"] = '--';
			$arrCabecera["codtipfon"] = '----';
			$arrCabecera["estmovcob"] = 0;
			$arrCabecera["numconint"] = "";
			$arrCabecera["tranoreglib"] = "";
			$arrCabecera["numchequera"] = "";
			$arrCabecera["codbansig"] = "";
			$arrCabecera["estmodordpag"] = 0;
			$this->valido = $this->eliminarMovBco($arrCabecera,$comprobante_sigesp);
			if($this->valido)
			{
				$this->valido=$this->actualizarEstatus(0,$comprobante_sigesp,'1900-01-01','1900-01-01',$objson->arrDetalle[$j]->comprobante,$procede,$fecha,$codban,$ctaban);
			}
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