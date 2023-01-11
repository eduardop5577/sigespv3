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
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsiv.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionSIV implements IIntegracionSIV
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
		//$this->conexionBaseDatos->debug=true;
	}

	
	public function validarConfiguracion($codsis,$seccion,$entry)
	{
		$valor = false;
		$cadenaSQL = "SELECT value ".
		             "	FROM sigesp_config ".
		             " WHERE codemp='{$this->codemp}' ".
				  	 "	 AND codsis='".$codsis."' ".
				  	 "	 AND seccion='".$seccion."' ".
				  	 "	 AND entry='".$entry."'";
		$dataConfi = $this->conexionBaseDatos->Execute($cadenaSQL);
		if(!$dataConfi->EOF)
		{
			if(trim($dataConfi->fields['value'])=='1')
			{
				$valor = true;
			}
		}
		return $valor;
	}
	

	public function buscarDespachos($numorddes, $fecdes, $estint)
	{
		$parametrosBusqueda = '';
		
		if($fecdes != '')
		{
			$fecdes = convertirFechaBd($fecdes);
			$parametrosBusqueda .= " AND siv_despacho.fecdes ='{$fecdes}'";
		}
		
		if($numorddes != '') 
		{
			$parametrosBusqueda .= " AND siv_despacho.numorddes like '%{$numorddes}%'";
		}
		
		$cadenaSQL = "SELECT numorddes AS comprobante, fecdes AS fecha, obsdes AS descripcion, fechaconta ".
					 "	FROM siv_despacho  ".
					 " INNER JOIN siv_dt_scg  ".
					 "    ON siv_despacho.codemp=siv_dt_scg.codemp  ".
					 "	 AND siv_despacho.numorddes=siv_dt_scg.codcmp ". 
					 " WHERE siv_despacho.codemp = '{$this->codemp}' ". 
					 "	 AND siv_dt_scg.estint = {$estint} {$parametrosBusqueda} ".
					 " GROUP BY numorddes, fecdes, obsdes, fechaconta ". 
					 " ORDER BY fecdes, numorddes";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetallePresupuestoDespacho($numorddes)
	{
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
	
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(siv_dt_spg.codestpro1,length(siv_dt_spg.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(siv_dt_spg.codestpro2,length(siv_dt_spg.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(siv_dt_spg.codestpro3,length(siv_dt_spg.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
				
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(siv_dt_spg.codestpro1,length(siv_dt_spg.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(siv_dt_spg.codestpro2,length(siv_dt_spg.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(siv_dt_spg.codestpro3,length(siv_dt_spg.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(siv_dt_spg.codestpro4,length(siv_dt_spg.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(siv_dt_spg.codestpro5,length(siv_dt_spg.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		 
		$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , siv_dt_spg.estcla, siv_dt_spg.spg_cuenta, monto, 0 AS disponibilidad, ".
		             "       siv_dt_spg.codestpro1, siv_dt_spg.codestpro2, siv_dt_spg.codestpro3, siv_dt_spg.codestpro4, ".
					 "       siv_dt_spg.codestpro5, numorddes, spg_cuentas.denominacion ".  
		             "	FROM siv_dt_spg ".
					 " INNER JOIN spg_cuentas ". 
					 "    ON siv_dt_spg.codemp = spg_cuentas.codemp ". 
					 "   AND siv_dt_spg.codestpro1 = spg_cuentas.codestpro1 ". 
					 "   AND siv_dt_spg.codestpro2 = spg_cuentas.codestpro2 ". 
					 "   AND siv_dt_spg.codestpro3 = spg_cuentas.codestpro3 ". 
					 "   AND siv_dt_spg.codestpro4 = spg_cuentas.codestpro4 ". 
					 "   AND siv_dt_spg.codestpro5 = spg_cuentas.codestpro5 ". 
					 "   AND siv_dt_spg.estcla = spg_cuentas.estcla ". 
					 "   AND siv_dt_spg.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE siv_dt_spg.codemp = '{$this->codemp}' ". 
		             "	 AND numorddes = '{$numorddes}'";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function obtenerDetPreDespachoDisponibilidad($numorddes)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuestoDespacho($numorddes);
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
			if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2))
			{
				$disponible = 1;
			}
			$arrDisponible[$j]['estructura']     = $dataCuentas->fields['estructura'];
			$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
			$arrDisponible[$j]['spg_cuenta']     = $dataCuentas->fields['spg_cuenta'];
			$arrDisponible[$j]['monto']          = $dataCuentas->fields['monto'];
			$arrDisponible[$j]['disponibilidad'] = $disponible;
			$arrDisponible[$j]['denominacion']   = utf8_encode($dataCuentas->fields['denominacion']);
			
			unset($this->servicioComprobante);
			$j++;
			$dataCuentas->MoveNext();
		}
		unset($dataCuentas);
		
		return $arrDisponible;
	}
	
	public function buscarDetalleContableDespacho($numorddes) 
	{
		$cadenaSQL = "SELECT siv_dt_scg.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN SUM(monto) ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN SUM(monto) ELSE 0 end) AS haber, scg_cuentas.denominacion ".  
		             "	FROM siv_dt_scg ". 
					 " INNER JOIN scg_cuentas ". 
					 "    ON siv_dt_scg.codemp = scg_cuentas.codemp ". 
					 "   AND siv_dt_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE siv_dt_scg.codemp = '{$this->codemp}' ". 
		             "	 AND codcmp = '{$numorddes}'".
					 " GROUP BY siv_dt_scg.sc_cuenta, debhab ";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	public function buscarTransferencias($numtra, $fecemi, $estint)
	{
		$parametrosBusqueda = '';
		
		if($fecemi != '')
		{
			$fecemi = convertirFechaBd($fecemi);
			$parametrosBusqueda .= " AND siv_transferencia.fecemi ='{$fecemi}'";
		}
		
		if($numorddes != '') 
		{
			$parametrosBusqueda .= " AND siv_transferencia.numtra like '%{$numtra}%'";
		}
		
		$cadenaSQL = "SELECT numtra AS comprobante, fecemi AS fecha, obstra AS descripcion, fechaconta ". 
					 "	FROM siv_transferencia ".
					 " INNER JOIN siv_dt_transferencia_scg ".
					 "    ON siv_transferencia.codemp=siv_dt_transferencia_scg.codemp ".
					 "	 AND siv_transferencia.numtra=siv_dt_transferencia_scg.codcmp ".
					 "	 AND siv_transferencia.fecemi=siv_dt_transferencia_scg.feccmp ". 
					 " WHERE siv_transferencia.codemp = '{$this->codemp}' ". 
					 "	 AND siv_dt_transferencia_scg.estint = {$estint} {$parametrosBusqueda} ".
					 " GROUP BY numtra, fecemi, obstra, fechaconta ". 
					 " ORDER BY fecemi, numtra";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetalleContableTransferencia($numtra, $fecemi) 
	{
		$fecemi =convertirFechaBd($fecemi);
		$cadenaSQL = "SELECT siv_dt_transferencia_scg.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN SUM(monto) ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN SUM(monto) ELSE 0 end) AS haber, scg_cuentas.denominacion ".  
		             "  FROM siv_dt_transferencia_scg ". 
					 " INNER JOIN scg_cuentas ". 
					 "    ON siv_dt_transferencia_scg.codemp = scg_cuentas.codemp ". 
					 "   AND siv_dt_transferencia_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE siv_dt_transferencia_scg.codemp = '{$this->codemp}' ".
		             "	 AND codcmp = '{$numtra}' ". 
		             "	 AND feccmp = '{$fecemi}' ".
					 "GROUP BY siv_dt_transferencia_scg.sc_cuenta, debhab ";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function cargarArregloDetPreDes($comprobante,$arrcabecera)
	{
		$arreglo=array();
		$dataspg=$this->buscarDetallePresupuestoDespacho($comprobante);
		if(($this->valido) && (!$dataspg->EOF))
		{
			$i=0;
			while(!$dataspg->EOF)
			{
				$i++;
				$arreglo[$i]['codemp']=$arrcabecera['codemp'];
				$arreglo[$i]['procede']= $arrcabecera['procede'];
				$arreglo[$i]['comprobante']= $arrcabecera['comprobante'];
				$arreglo[$i]['codban']= $arrcabecera['codban'];
				$arreglo[$i]['ctaban']= $arrcabecera['ctaban'];
				$arreglo[$i]['procede_doc']= $arrcabecera['procede'];
				$arreglo[$i]['codfuefin']=$arrcabecera['codfuefin'];
				$arreglo[$i]['fecha']= $arrcabecera['fecha'];
				$arreglo[$i]['descripcion']= $arrcabecera['descripcion'];
				$arreglo[$i]['orden']= $i;
				$arreglo[$i]['codestpro1']=$dataspg->fields['codestpro1'];
				$arreglo[$i]['codestpro2']=$dataspg->fields['codestpro2'];
				$arreglo[$i]['codestpro3']=$dataspg->fields['codestpro3'];
				$arreglo[$i]['codestpro4']=$dataspg->fields['codestpro4'];
				$arreglo[$i]['codestpro5']=$dataspg->fields['codestpro5'];
				$arreglo[$i]['estcla']=$dataspg->fields['estcla'];
				$arreglo[$i]['spg_cuenta']=$dataspg->fields['spg_cuenta'];
				$arreglo[$i]['documento']= fillComprobante($dataspg->fields['numorddes']);
				$arreglo[$i]['monto']=$dataspg->fields['monto'];
				$arreglo[$i]['mensaje']= 'OCP';
				$dataspg->MoveNext();
			}
		}
		return $arreglo;	
	}
	
	public function cargarArregloDetConDes($comprobante,$arrcabecera)
	{
		$arreglo=array();
		$cadenaSQL = "SELECT sc_cuenta, debhab, codcmp, MAX(obsdes) AS obsdes, SUM(monto) AS monto".
				     "  FROM siv_despacho,siv_dt_scg ".
				     " WHERE siv_despacho.codemp = '".$this->codemp."' ".
				     "   AND siv_dt_scg.codcmp = '".$comprobante."' ".
				     "   AND siv_despacho.codemp=siv_dt_scg.codemp ".
				     "   AND siv_despacho.numorddes=siv_dt_scg.codcmp ".
					 " GROUP BY sc_cuenta, debhab, codcmp ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->cargarArregloDetConDes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$i=0;
				while(!$data->EOF)
				{
					$i++;
					$arreglo[$i]['codemp']=$arrcabecera['codemp'];
					$arreglo[$i]['procede']= $arrcabecera['procede'];
					$arreglo[$i]['comprobante']= $arrcabecera['comprobante'];
					$arreglo[$i]['codban']= $arrcabecera['codban'];
					$arreglo[$i]['ctaban']= $arrcabecera['ctaban'];
					$arreglo[$i]['procede_doc']= $arrcabecera['procede'];
					$arreglo[$i]['fecha']= $arrcabecera['fecha'];
					$arreglo[$i]['descripcion']= $arrcabecera['descripcion'];
					$arreglo[$i]['orden']= $i;
					$arreglo[$i]['sc_cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['debhab']= $data->fields['debhab'];
					$arreglo[$i]['documento']= fillComprobante($data->fields['codcmp']);
					$arreglo[$i]['monto']=$data->fields['monto'];
					$data->MoveNext();
				}
			}
		}
		return $arreglo;
	}
	
	public function actualizarFechaEstatusDes($comprobante,$estatus,$fechaconta,$fechaanula)
	{
		$this->valido=true;	
		$cadenaSql="UPDATE siv_dt_spg ".
				   "   SET estatus=".$estatus.", ".
				   "       fechaconta='".$fechaconta."' , ".
				   "       fechaanula='".$fechaanula."' ".
				   "   WHERE codemp='".$this->codemp."' ".
				   "     AND numorddes='".$comprobante."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
		 $this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->actualizarFechaEstatusDes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		if($this->valido)
		{
			$cadenaSql="UPDATE siv_dt_scg ".
				       "   SET estint=".$estatus.", ".
					   "       fechaconta='".$fechaconta."', ".
					   "       fechaanula='".$fechaanula."'".
				       " WHERE codemp='".$this->codemp."'".
				       "   AND codcmp='".$comprobante."'";
			$data = $this->conexionBaseDatos->Execute($cadenaSql);
			if($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->actualizarFechaEstatusDes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($data);
		}
		return $this->valido;
	}
	
	public function cargarArregloDetConTran($comprobante,$fecha,$arrcabecera)
	{
		$arreglo=array();
		$cadenaSQL = "SELECT codcmp, sc_cuenta, debhab, SUM(monto) as monto ".
				     "  FROM siv_transferencia,siv_dt_transferencia_scg ".
				     " WHERE siv_transferencia.codemp = '".$this->codemp."' ".
				     "   AND siv_transferencia.numtra='".$comprobante."'".
				     "   AND siv_transferencia.fecemi='".$fecha."'".
				     "   AND siv_transferencia.codemp=siv_dt_transferencia_scg.codemp ".
				     "   AND siv_transferencia.numtra=siv_dt_transferencia_scg.codcmp ".
				     "   AND siv_transferencia.fecemi=siv_dt_transferencia_scg.feccmp ".
				     " GROUP BY codcmp, sc_cuenta, debhab ".
				     " ORDER BY sc_cuenta, debhab";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->cargarArregloDetCotTran ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$i=0;
				while(!$data->EOF)
				{
					$i++;
					$arreglo[$i]['codemp']=$arrcabecera['codemp'];
					$arreglo[$i]['procede']= $arrcabecera['procede'];
					$arreglo[$i]['comprobante']= $arrcabecera['comprobante'];
					$arreglo[$i]['codban']= $arrcabecera['codban'];
					$arreglo[$i]['ctaban']= $arrcabecera['ctaban'];
					$arreglo[$i]['procede_doc']= $arrcabecera['procede'];
					$arreglo[$i]['fecha']= $arrcabecera['fecha'];
					$arreglo[$i]['descripcion']= $arrcabecera['descripcion'];
					$arreglo[$i]['orden']= $i;
					$arreglo[$i]['sc_cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['debhab']= $data->fields['debhab'];
					$arreglo[$i]['documento']= fillComprobante($data->fields['codcmp']);
					$arreglo[$i]['monto']=$data->fields['monto'];
					$data->MoveNext();
				}
			}
		}
		return $arreglo;
	}
	
	public function actualizarFechaEstatuTran($comprobante,$estatus,$fecemision,$fechaconta,$fechaanula)
	{
		$this->valido=true;	
		$cadenaSql="UPDATE siv_dt_transferencia_scg".
				"   SET estint=".$estatus.",".
				"       fechaconta='".$fechaconta."',".
				"       fechaanula='".$fechaanula."'".
				" WHERE codemp='".$this->codemp."'".
				"   AND codcmp='".$comprobante."'".
				"   AND feccmp='".$fecemision."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
		 $this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->actualizarFechaEstatuTran ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		return $this->valido;
	}
	
	public function procesoContabilizarDespachos($objson)
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
			$arrevento['desevetra'] = "Contabilizaci&#243;n del Despacho {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarDespachos($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Despacho contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Despacho no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarDespachos($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();	
		$arrdetallespg=array();
		$arrdetallescg=array();
		$this->valido=true;
		$fecha = convertirFechaBd($objson->feccon);	
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO EL DESPACHO A CONTABILIZAR
		$data=$this->buscarDespachos($comprobante,'',0);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de despacho Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if(!compararFecha($data->fields['fecha'],$fecha))
		{
		 $this->mensaje .= ' La Fecha de Contabilizaci&#243;n '.$fecha.' es menor a la fecha del Despacho '.$data->fields['fecha'];
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVCND';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->cargarArregloDetPreDes($comprobante,$arrcabecera);
		}
		if($this->valido)
		{
			$totalspg=count((array)$arreglospg);
			$total=0;
			if($totalspg>0){
				for($j=1;$j<=$totalspg;$j++)
				{
					$monto=$arreglospg[$j]['monto'];
					$total=$total+$monto;
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
			$arrdetallescg=$this->cargarArregloDetConDes($comprobante,$arrcabecera);
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatusDes($comprobante,1,$fecha,'1900-01-01');				
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
	
	public function procesorRevContabilizarDespachos($objson)
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
			$arrevento['desevetra'] = "Reversar el Despacho {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->reversarDespachos($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Despacho reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Despacho no fue reversado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarDespachos($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();	
		$arrdetallespg=array();
		$this->valido=true;
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO EL DESPACHO A CONTABILIZAR
		$data=$this->buscarDespachos($comprobante,'',1);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de despacho Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVCND';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->cargarArregloDetPreDes($comprobante,$arrcabecera);
		}
		if($this->valido)
		{
			$totalspg=count((array)$arrdetallespg);
			$total=0;
			if($totalspg>0)
			{
				for($j=1;$j<=$totalspg;$j++)
				{
					$monto=$arrdetallespg[$j]['monto'];
					$total=$total+$monto;
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatusDes($comprobante,0,'1900-01-01','1900-01-01');				
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
	
	public function procesoContabilizarTransferencias($objson)
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
			$arrevento['desevetra'] = "Contabilizaci&#243;n de la Transferencia {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarTransferencias($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Transferencia contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Transferencia no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarTransferencias($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$fecha = convertirFechaBd($objson->feccon);	
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		$fecemi=convertirFechaBd($objson->arrDetalle[$j]->fecemi);
		// OBTENGO LA TRANSFERENCIA A CONTABILIZAR
		$data=$this->buscarTransferencias($comprobante,$fecemi,0);
		if($data->fields['comprobante']==''){
			$this->mensaje.=' El Comprobante de despacho Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if(!compararFecha($data->fields['fecha'],$fecha)){
			$this->mensaje .= ' La Fecha de Contabilizaci&#243;n '.$fecha.' es menor a la fecha de la Transferencia '.$data->fields['fecha'];
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVCTR';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->cargarArregloDetConTran($comprobante,$data->fields['fecha'],$arrcabecera);
		}
		if($this->valido)
		{
			$totalscg=count((array)$arrdetallescg);
			$total=0;
			if($totalscg>0){
				for($j=1;$j<=$totalspg;$j++)
				{
					if($arrdetallescg[$j]['debhab']=='D'){
						$monto=$arrdetallescg[$j]['monto'];
						$total=$total+$monto;
					}
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatuTran($comprobante,1,$data->fields['fecha'],$fecha,'1900-01-01');				
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
	
	public function procesoRevContabilizarTransferencias($objson)
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
			$arrevento['desevetra'] = "Reversar la Transferencia {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->reversarTransferencias($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Transferencia reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Transferencia no fue reversada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarTransferencias($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO LA TRANSFERENCIA A CONTABILIZAR
		$data=$this->buscarTransferencias($comprobante,'',1);
		if($data->fields['comprobante']==''){
			$this->mensaje.=' El Comprobante de despacho Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVCTR';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->cargarArregloDetConTran($comprobante,$data->fields['fecha'],$arrcabecera);
		}
		if($this->valido)
		{
			$totalscg=count((array)$arrdetallescg);
			$total=0;
			if($totalscg>0){
				for($j=1;$j<=$totalspg;$j++)
				{
					if($arrdetallescg[$j]['debhab']=='D'){
						$monto=$arrdetallescg[$j]['monto'];
						$total=$total+$monto;
					}
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatuTran($comprobante,0,$data->fields['fecha'],'1900-01-01','1900-01-01');				
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

	public function buscarProduccion($numpro, $fecemi, $estint)
	{
		$parametrosBusqueda = '';
		
		if($fecemi != '')
		{
			$fecemi = convertirFechaBd($fecemi);
			$parametrosBusqueda .= " AND siv_dt_produccion.fecemi ='{$fecemi}'";
		}
		
		if($numpro != '') 
		{
			$parametrosBusqueda .= " AND siv_dt_produccion.numpro like '%{$numpro}%'";
		}
		
		$cadenaSQL="SELECT siv_produccion.numpro as comprobante, MAX(siv_produccion.obspro) as descripcion, MAX(siv_dt_produccion.fecemi) as fecha,MAX(siv_dt_produccion_scg.fechaconta) as fechaconta,MAX(siv_dt_produccion_scg.fechaanula) as fechaanula". 
				   "  FROM siv_produccion ".
				   " INNER JOIN (siv_dt_produccion ".
				   "       INNER JOIN siv_dt_produccion_scg ".
				   "          ON siv_dt_produccion.codemp = '".$this->codemp."' ".
				   "         AND siv_dt_produccion_scg.estint = '".$estint."' ".
				   "		 ".$parametrosBusqueda.
				   "         AND siv_dt_produccion.codemp = siv_dt_produccion_scg.codemp ".
				   "		 AND siv_dt_produccion.numpro = siv_dt_produccion_scg.codcmp  ".
				   "		 AND siv_dt_produccion.codart = siv_dt_produccion_scg.codart  ".
				   "		 AND siv_dt_produccion.fecemi = siv_dt_produccion_scg.feccmp)  ".
				   "    ON siv_produccion.codemp=siv_dt_produccion.codemp ".
				   "   AND siv_produccion.numpro=siv_dt_produccion.numpro ".
				   "   AND siv_produccion.fecemi=siv_dt_produccion.fecemi ".
				   " GROUP BY siv_produccion.codemp,  siv_produccion.numpro ".
			       " ORDER BY siv_produccion.numpro  ";
				   
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarDetalleContableProduccion($numpro, $fecemi) 
	{
		$fecemi =convertirFechaBd($fecemi);
		$cadenaSQL = "SELECT siv_dt_produccion_scg.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion ".  
		             "  FROM siv_dt_produccion_scg ". 
					 " INNER JOIN scg_cuentas ". 
					 "    ON siv_dt_produccion_scg.codemp = scg_cuentas.codemp ". 
					 "   AND siv_dt_produccion_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE siv_dt_produccion_scg.codemp = '{$this->codemp}' ".
		             "	 AND codcmp = '{$numpro}' ". 
		             "	 AND feccmp = '{$fecemi}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function procesoContabilizarProduccion($objson)
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
			$arrevento['desevetra'] = "Contabilizaci&#243;n de la Produccion {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarProduccion($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Produccion contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Produccion no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarProduccion($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$fecha = convertirFechaBd($objson->feccon);	
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO LA PRODUCCIÓN A CONTABILIZAR		
		$data=$this->buscarProduccion($comprobante,'',0);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de Produccion Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if(!compararFecha($data->fields['fecha'],$fecha))
		{
			$this->mensaje .= ' La Fecha de Contabilizaci&#243;n '.$fecha.' es menor a la fecha de la Produccion '.$data->fields['fecha'];
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVPRO';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->cargarArregloDetConPro($comprobante,$data->fields['fecha'],$arrcabecera);
		}
		if($this->valido)
		{
			$totalscg=count((array)$arrdetallescg);
			$total=0;
			if($totalscg>0)
			{
				for($j=1;$j<=$totalspg;$j++)
				{
					if($arrdetallescg[$j]['debhab']=='D')
					{
						$monto=$arrdetallescg[$j]['monto'];
						$total=$total+$monto;
					}
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatuProduccion($comprobante,1,$data->fields['fecha'],$fecha,'1900-01-01');				
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

	public function cargarArregloDetConPro($comprobante,$fecha,$arrcabecera)
	{
		$arreglo=array();
		$cadenaSQL = "SELECT codcmp, sc_cuenta, debhab, SUM(monto) AS monto ".
				     "  FROM siv_dt_produccion,siv_dt_produccion_scg ".
				     " WHERE siv_dt_produccion.codemp = '".$this->codemp."' ".
				     "   AND siv_dt_produccion.numpro='".$comprobante."'".
				     "   AND siv_dt_produccion.fecemi='".$fecha."'".
				     "   AND siv_dt_produccion.codemp=siv_dt_produccion_scg.codemp ".
				     "   AND siv_dt_produccion.numpro=siv_dt_produccion_scg.codcmp ".
				     "   AND siv_dt_produccion.fecemi=siv_dt_produccion_scg.feccmp ".
				     "   AND siv_dt_produccion.codart=siv_dt_produccion_scg.codart ".
					 " GROUP BY codcmp,sc_cuenta,debhab ".
				     " ORDER BY debhab, sc_cuenta ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->cargarArregloDetCotPro ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$i=0;
				while(!$data->EOF)
				{
					$i++;
					$arreglo[$i]['codemp']=$arrcabecera['codemp'];
					$arreglo[$i]['procede']= $arrcabecera['procede'];
					$arreglo[$i]['comprobante']= $arrcabecera['comprobante'];
					$arreglo[$i]['codban']= $arrcabecera['codban'];
					$arreglo[$i]['ctaban']= $arrcabecera['ctaban'];
					$arreglo[$i]['procede_doc']= $arrcabecera['procede'];
					$arreglo[$i]['fecha']= $arrcabecera['fecha'];
					$arreglo[$i]['descripcion']= $arrcabecera['descripcion'];
					$arreglo[$i]['orden']= $i;
					$arreglo[$i]['sc_cuenta']=$data->fields['sc_cuenta'];
					$arreglo[$i]['debhab']= $data->fields['debhab'];
					$arreglo[$i]['documento']= fillComprobante($data->fields['codcmp']);
					$arreglo[$i]['monto']=$data->fields['monto'];
					$data->MoveNext();
				}
			}
		}
		return $arreglo;
	}
	
	public function actualizarFechaEstatuProduccion($comprobante,$estatus,$fecemision,$fechaconta,$fechaanula)
	{
		$this->valido=true;	
		$cadenaSql="UPDATE siv_dt_produccion_scg".
				"   SET estint=".$estatus.",".
				"       fechaconta='".$fechaconta."',".
				"       fechaanula='".$fechaanula."'".
				" WHERE codemp='".$this->codemp."'".
				"   AND codcmp='".$comprobante."'".
				"   AND feccmp='".$fecemision."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
		 $this->mensaje .= ' CLASE->INTEGRADOR SIV METODO->actualizarFechaEstatuProduccion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		return $this->valido;
	}
	
	public function procesoRevContabilizarProduccion($objson)
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
			$arrevento['desevetra'] = "Reversar la Produccion {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->reversarProduccion($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Produccion reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Produccion no fue reversada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarProduccion($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO LA Produccion A CONTABILIZAR
		$data=$this->buscarProduccion($comprobante,'',1);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de despacho Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVPRO';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatuProduccion($comprobante,0,$data->fields['fecha'],'1900-01-01','1900-01-01');				
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

	public function buscarEmpaquetado($codemppro, $fecemppro, $estint)
	{
		$parametrosBusqueda = '';
		
		if($fecemppro != '')
		{
			$fecemppro = convertirFechaBd($fecemppro);
			$parametrosBusqueda .= " AND siv_dt_empaquetado_scg.fecemppro ='{$fecemppro}'";
		}
		
		if($codemppro != '') 
		{
			$parametrosBusqueda .= " AND siv_dt_empaquetado_scg.codemppro like '%{$codemppro}%'";
		}
		
		$cadenaSQL="SELECT siv_empaquetado.codemppro as comprobante, MAX(siv_empaquetado.obspro) as descripcion, MAX(siv_dt_empaquetado_scg.fecemppro) as fecha,MAX(siv_dt_empaquetado_scg.fechaconta) as fechaconta,MAX(siv_dt_empaquetado_scg.fechaanula) as fechaanula". 
				   "  FROM siv_empaquetado ".
				   " INNER JOIN siv_dt_empaquetado_scg ".
				   "    ON siv_dt_empaquetado_scg.codemp = '".$this->codemp."' ".
				   "   AND siv_dt_empaquetado_scg.estint = '".$estint."' ".
				   "		 ".$parametrosBusqueda.
				   "   AND siv_empaquetado.codemp=siv_dt_empaquetado_scg.codemp ".
				   "   AND siv_empaquetado.codemppro=siv_dt_empaquetado_scg.codemppro ".
				   "   AND siv_empaquetado.fecemppro=siv_dt_empaquetado_scg.fecemppro ".
				   " GROUP BY siv_empaquetado.codemp,  siv_empaquetado.codemppro ".
			       " ORDER BY siv_empaquetado.codemppro  ";
				   
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function buscarDetalleContableEmpaquetado($codemppro, $fecemppro) 
	{
		$fecemppro =convertirFechaBd($fecemppro);
		$cadenaSQL = "SELECT siv_dt_empaquetado_scg.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion ".  
		             "  FROM siv_dt_empaquetado_scg ". 
					 " INNER JOIN scg_cuentas ". 
					 "    ON siv_dt_empaquetado_scg.codemp = scg_cuentas.codemp ". 
					 "   AND siv_dt_empaquetado_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE siv_dt_empaquetado_scg.codemp = '{$this->codemp}' ".
		             "	 AND codemppro = '{$codemppro}' ". 
		             "	 AND fecemppro = '{$fecemppro}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function procesoContabilizarEmpaquetado($objson)
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
			$arrevento['desevetra'] = "Contabilizaci&#243;n del Empaquetado {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarEmpaquetado($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Empaquetado contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Empaquetado no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarEmpaquetado($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$fecha = convertirFechaBd($objson->feccon);	
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO LA PRODUCCIÓN A CONTABILIZAR		
		$data=$this->buscarEmpaquetado($comprobante,'',0);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de Empaquetado Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if(!compararFecha($data->fields['fecha'],$fecha))
		{
			$this->mensaje .= ' La Fecha de Contabilizaci&#243;n '.$fecha.' es menor a la fecha del Empaquetado '.$data->fields['fecha'];
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVEMP';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->cargarArregloDetConEmp($comprobante,$data->fields['fecha'],$arrcabecera);
		}
		if($this->valido)
		{
			$totalscg=count((array)$arrdetallescg);
			$total=0;
			if($totalscg>0)
			{
				for($j=1;$j<=$totalspg;$j++)
				{
					if($arrdetallescg[$j]['debhab']=='D')
					{
						$monto=$arrdetallescg[$j]['monto'];
						$total=$total+$monto;
					}
				}
				$arrcabecera['total'] = number_format($total,2,'.','');
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatusEmpaquetado($comprobante,1,$data->fields['fecha'],$fecha,'1900-01-01');				
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

	public function cargarArregloDetConEmp($comprobante,$fecha,$arrcabecera)
	{
		$arreglo=array();
		$cadenaSQL = "SELECT codemppro, sc_cuenta, debhab, SUM(monto) AS monto ".
				     "  FROM siv_dt_empaquetado_scg ".
				     " WHERE siv_dt_empaquetado_scg.codemp = '".$this->codemp."' ".
				     "   AND siv_dt_empaquetado_scg.codemppro='".$comprobante."'".
				     "   AND siv_dt_empaquetado_scg.fecemppro='".$fecha."'".
					 " GROUP BY codemppro,sc_cuenta,debhab ".
				     " ORDER BY debhab, sc_cuenta ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' ERROR-> '.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$i=0;
				while(!$data->EOF)
				{
					$i++;
					$arreglo[$i]['codemp']=$arrcabecera['codemp'];
					$arreglo[$i]['procede']= $arrcabecera['procede'];
					$arreglo[$i]['comprobante']= $arrcabecera['comprobante'];
					$arreglo[$i]['codban']= $arrcabecera['codban'];
					$arreglo[$i]['ctaban']= $arrcabecera['ctaban'];
					$arreglo[$i]['procede_doc']= $arrcabecera['procede'];
					$arreglo[$i]['fecha']= $arrcabecera['fecha'];
					$arreglo[$i]['descripcion']= $arrcabecera['descripcion'];
					$arreglo[$i]['orden']= $i;
					$arreglo[$i]['sc_cuenta']=trim($data->fields['sc_cuenta']);
					$arreglo[$i]['debhab']= $data->fields['debhab'];
					$arreglo[$i]['documento']= fillComprobante($data->fields['codemppro']);
					$arreglo[$i]['monto']=$data->fields['monto'];
					$data->MoveNext();
				}
			}
		}
		return $arreglo;
	}
	
	public function actualizarFechaEstatusEmpaquetado($comprobante,$estatus,$fecemision,$fechaconta,$fechaanula)
	{
		$this->valido=true;	
		$cadenaSql="UPDATE siv_dt_empaquetado_scg ".
				   "   SET estint=".$estatus.",".
				   "       fechaconta='".$fechaconta."',".
				   "       fechaanula='".$fechaanula."'".
				   " WHERE codemp='".$this->codemp."'".
				   "   AND codemppro='".$comprobante."'".
				   "   AND fecemppro='".$fecemision."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= 'ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		return $this->valido;
	}
	
	public function procesoRevContabilizarEmpaquetado($objson)
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
			$arrevento['desevetra'] = "Reversar el Empaquetado {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->reversarEmpaquetado($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Empaquetado reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "El Empaquetado no fue reversado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarEmpaquetado($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$arrcabecera=array();
		$arrdetallescg=array();
		$this->valido=true;
		$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
		// OBTENGO LA Produccion A CONTABILIZAR
		$data=$this->buscarEmpaquetado($comprobante,'',1);
		if($data->fields['comprobante']=='')
		{
			$this->mensaje.=' El Comprobante de Empaquetado Nº'.$comprobante.' No existe';
			$this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SIVEMP';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $data->fields['descripcion'];
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(0,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido=$this->actualizarFechaEstatusEmpaquetado($comprobante,0,$data->fields['fecha'],'1900-01-01','1900-01-01');				
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