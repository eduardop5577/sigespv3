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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracioncxpncnd.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionCXPNCND implements IIntegracionCXPNCND 
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos;
	private $servicioComprobante;
	private $daoNotaCD;
			
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$this->estfilpremod = $_SESSION['la_empresa']['estfilpremod'];	
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->estafenc = $_SESSION['la_empresa']['estafenc'];
	}
	
	public function buscarNotasIntegrar($numsol, $numrecdoc, $operacion, $fecope, $fecapr, $estatus) 
	{
		$criterio = '';
		$filtrofrom = '';
		if(!empty($numsol)) 
		{
			$criterio .= " AND numsol like '%{$numsol}%'";
		}
		if(!empty($numrecdoc)) 
		{
			$criterio .= " AND numrecdoc like '%{$numrecdoc}%'";
		}
		if(!empty($operacion)) 
		{
			$criterio .= " AND codope='{$operacion}' ";
		}
		if(!empty($fecope)) 
		{
			$criterio .= " AND fecope = '{$fecope}'";
		}
		if(!empty($fecapr)) 
		{
			$criterio .= " AND fecaprnc = '{$fecapr}'";
		}
		$filtrofrom ='';
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('cxp_dc_spg.codestpro','cxp_dc_spg.estcla');
			$criterio .= " AND {$criterio} IN (SELECT codintper FROM sss_permisos_internos ".
			            " 					   WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			            "     				     AND codsis='SPG' ".
						"						 AND codusu='{$_SESSION["la_logusr"]}' ".
					    "       				 AND enabled=1) ".
			            " AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			            "  						    WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
						"          				      AND codsis='CXP' ".
				        "                             AND codusu='{$_SESSION["la_logusr"]}' ".
					    "							  AND enabled=1) ".
						" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
						" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
						" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
						" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
						" AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol".
						" AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc".
						" AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc".
						" AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene".
						" AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro".
						" AND cxp_sol_dc.numsol = cxp_dc_spg.numsol ".
						" AND cxp_sol_dc.numrecdoc = cxp_dc_spg.numrecdoc ".
						" AND cxp_sol_dc.codtipdoc = cxp_dc_spg.codtipdoc".
						" AND cxp_sol_dc.ced_bene = cxp_dc_spg.ced_bene".
						" AND cxp_sol_dc.cod_pro = cxp_dc_spg.cod_pro".
						" AND cxp_sol_dc.codope = cxp_dc_spg.codope".
						" AND cxp_sol_dc.numdc = cxp_dc_spg.numdc ";
			
			$filtrofrom = " ,cxp_rd, cxp_dt_solicitudes, cxp_rd_spg, cxp_dc_spg";			
		}
		$cadenaSql = "SELECT cxp_sol_dc.numsol, cxp_sol_dc.numrecdoc, cxp_sol_dc.codtipdoc,cxp_sol_dc.ced_bene, cxp_sol_dc.cod_pro, ".
				     "       cxp_sol_dc.codope, cxp_sol_dc.numdc, cxp_sol_dc.fecope, cxp_sol_dc.desope, cxp_sol_dc.fechaconta, ".
					 "       cxp_sol_dc.fechaanula,  MAX(cxp_sol_dc.desope) AS desope ,".
				     "       (SELECT nompro FROM rpc_proveedor WHERE cxp_sol_dc.codemp=rpc_proveedor.codemp AND cxp_sol_dc.cod_pro=rpc_proveedor.cod_pro) AS nomprov,  ".
				     "       (SELECT nombene FROM rpc_beneficiario WHERE cxp_sol_dc.codemp=rpc_beneficiario.codemp AND cxp_sol_dc.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				     "       (SELECT apebene FROM rpc_beneficiario WHERE cxp_sol_dc.codemp=rpc_beneficiario.codemp AND cxp_sol_dc.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
                     "  FROM cxp_sol_dc".$filtrofrom.
				     " WHERE cxp_sol_dc.codemp='{$this->codemp}' ".
				     "   AND cxp_sol_dc.estnotadc='{$estatus}'".
				     "   AND cxp_sol_dc.estapr=1".
				     $criterio.
				     " GROUP BY cxp_sol_dc.codemp,cxp_sol_dc.numsol, cxp_sol_dc.numrecdoc, cxp_sol_dc.codtipdoc,cxp_sol_dc.ced_bene, cxp_sol_dc.cod_pro, ".
				     "          cxp_sol_dc.codope, cxp_sol_dc.numdc, cxp_sol_dc.fecope, cxp_sol_dc.desope, cxp_sol_dc.fechaconta, cxp_sol_dc.fechaanula ".
				     " ORDER BY cxp_sol_dc.numsol ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false) {
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSet;
	}
	
	public function buscarDetallePresupuesto($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc)
	{
		$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
		$cadenaSQL = "SELECT cxp_dc_spg.spg_cuenta, cxp_dc_spg.codestpro, cxp_dc_spg.estcla, cxp_dc_spg.monto, cxp_documento.estpre, spg_cuentas.denominacion ". 
		             "  FROM cxp_dc_spg ". 
		             " INNER JOIN cxp_documento  ".
		             "    ON cxp_dc_spg.codtipdoc=cxp_documento.codtipdoc ".  
					 " INNER JOIN spg_cuentas ". 
					 "    ON cxp_dc_spg.codemp = spg_cuentas.codemp ". 
					 "   AND cxp_dc_spg.codestpro =  ".$codestpro." ". 
					 "   AND cxp_dc_spg.estcla = spg_cuentas.estcla ". 
					 "   AND cxp_dc_spg.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE cxp_dc_spg.codemp='{$this->codemp}' ". 
		             "   AND cxp_dc_spg.numsol='{$numsol}'  ".
		             "   AND cxp_dc_spg.numrecdoc='{$numrecdoc}' ". 
		             "   AND cxp_dc_spg.codtipdoc='{$codtipdoc}' ". 
		             "   AND cxp_dc_spg.ced_bene='{$ced_bene}' ". 
		             "   AND cod_pro='{$cod_pro}' ". 
		             "   AND cxp_dc_spg.codope='{$codope}' ". 
		             "   AND cxp_dc_spg.numdc='{$numdc}'";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function obtenerDetalleComprobanteNCDSPG($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $fecope)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc);
		while (!$dataCuentas->EOF)
		{
			$disponible = 1;
			$codestpro = $dataCuentas->fields['codestpro'];
			$codestpro1 = substr($codestpro,0,25);
			$codestpro2 = substr($codestpro,25,25);
			$codestpro3 = substr($codestpro,50,25);
			$codestpro4 = substr($codestpro,75,25);
			$codestpro5 = substr($codestpro,100,25);
			//VERIFICANDO DISPONIBILIDAD 
			if ($dataCuentas->fields['estpre']==2)
			{
				$valiNivel = 0;
				if($_SESSION["la_empresa"]["estvaldis"]==1)
				{
					$valiNivel = $_SESSION["la_empresa"]["vali_nivel"];
				}
				$nivelCuenta = obtenerNivelPlus($dataCuentas->fields['spg_cuenta'], $_SESSION["la_empresa"]["formpre"]);
				if ($nivelCuenta <= $valiNivel) {
					$arrDetalleSPG['codemp']     = $this->codemp;
					$arrDetalleSPG['codestpro1'] = $codestpro1;
					$arrDetalleSPG['codestpro2'] = $codestpro2;
					$arrDetalleSPG['codestpro3'] = $codestpro3;
					$arrDetalleSPG['codestpro4'] = $codestpro4;
					$arrDetalleSPG['codestpro5'] = $codestpro5;
					$arrDetalleSPG['estcla']     = $dataCuentas->fields['estcla'];
					$arrDetalleSPG['spg_cuenta'] = $dataCuentas->fields['spg_cuenta'];
					$this->servicioComprobante = new ServicioComprobanteSPG();
					$this->servicioComprobante->setDaoDetalleSpg($arrDetalleSPG);
					$this->servicioComprobante->saldoSelect('ACTUAL');
					$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - 
					                    ( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + 
					                    $this->servicioComprobante->precomprometido));
					if(round($dataCuentas->fields['monto'],2) > round($disponibilidad,2))
					{
						$disponible = 0;
					}
					
					if ($disponible == 0)
					{
						$_SESSION['fechacomprobante'] = $fecope;
						$this->servicioComprobante->saldoSelect();
						$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - 
					                    	( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + 
					                    	$this->servicioComprobante->precomprometido));
						if(round($dataCuentas->fields['monto'],2) > round($disponibilidad,2))
						{
							$disponible = 0;
						}
					}
				}
			}
			unset($this->servicioComprobante);
			
			//PREPARANDO PRESENTACION DE LOS DATOS
			switch($_SESSION["la_empresa"]["estmodest"]){
				case "1": // Modalidad por Proyecto
					$codest1 = substr($codestpro1, -$_SESSION["la_empresa"]["loncodestpro1"]);
					$codest2 = substr($codestpro2, -$_SESSION["la_empresa"]["loncodestpro2"]);
					$codest3 = substr($codestpro3, -$_SESSION["la_empresa"]["loncodestpro3"]);
					$cadenaEstructura = "{$codest1}-{$codest2}-{$codest3}";
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = substr($codestpro1, -$_SESSION["la_empresa"]["loncodestpro1"]);
					$codest2 = substr($codestpro2, -$_SESSION["la_empresa"]["loncodestpro2"]);
					$codest3 = substr($codestpro3, -$_SESSION["la_empresa"]["loncodestpro3"]);
					$codest4 = substr($codestpro4, -$_SESSION["la_empresa"]["loncodestpro4"]);
					$codest5 = substr($codestpro5, -$_SESSION["la_empresa"]["loncodestpro5"]);
					$cadenaEstructura = "{$codest1}-{$codest2}-{$codest3}-{$codest4}-{$codest5}";
					break;
			}
			$arrDisponible[$j]['estructura']     = $cadenaEstructura;
			$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
			$arrDisponible[$j]['spg_cuenta']     = $dataCuentas->fields['spg_cuenta'];
			$arrDisponible[$j]['monto']          = $dataCuentas->fields['monto'];
			$arrDisponible[$j]['disponibilidad'] = $disponible;
			$arrDisponible[$j]['denominacion']   = utf8_encode($dataCuentas->fields['denominacion']);
			
			$j++;
			$dataCuentas->MoveNext();
		}
		unset($dataCuentas);
		
		return $arrDisponible;
	}
	
	public function obtenerDetalleComprobanteNCDSCG($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc)
	{
		$cadenaSql="SELECT sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
		           "       (CASE WHEN debhab = 'D' THEN SUM(monto) ELSE 0 END) AS debe,  ".
		           "       (CASE WHEN debhab = 'H' THEN SUM(monto) ELSE 0 end) AS haber ". 
		           "  FROM cxp_dc_scg ". 
				   " INNER JOIN scg_cuentas ". 
				   "    ON cxp_dc_scg.codemp = scg_cuentas.codemp ". 
				   "   AND cxp_dc_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
				   " WHERE codemp='{$this->codemp}' ". 
		           "   AND numsol='{$numsol}' ". 
		           "   AND numrecdoc='{$numrecdoc}' ". 
		           "   AND codtipdoc='{$codtipdoc}' ". 
		           "   AND ced_bene='{$ced_bene}' ". 
		           "   AND cod_pro='{$cod_pro}' ". 
		           "   AND codope='{$codope}' ". 
		           "   AND numdc='{$numdc}' ".
				   "   GROUP BY debhab, sc_cuenta ".
		           "   ORDER BY debhab, sc_cuenta";
		return $this->conexionBaseDatos->Execute ( $cadenaSql );
	}
	
	public function obtenerDetalleNotaSCG($arrCabecera) 
	{
		$arrDetalleSCG = array();
		$cadenaSql="SELECT debhab, sc_cuenta, monto ".
				   "  FROM cxp_dc_scg ".
				   " WHERE codemp='{$this->daoNotaCD->codemp}'". 
				   "   AND numsol='{$this->daoNotaCD->numsol}' ".
				   "   AND numrecdoc='{$this->daoNotaCD->numrecdoc}'". 
				   "   AND codtipdoc='{$this->daoNotaCD->codtipdoc}' ".
				   "   AND ced_bene='{$this->daoNotaCD->ced_bene}' ".
				   "   AND cod_pro='{$this->daoNotaCD->cod_pro}' ".
				   "   AND codope='{$this->daoNotaCD->codope}' ".
				   "   AND numdc='{$this->daoNotaCD->numdc}'";
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
				$arrDetalleSCG[$conSCG]['codemp']      = $arrCabecera['codemp'];
				$arrDetalleSCG[$conSCG]['procede']     = $arrCabecera['procede'];
				$arrDetalleSCG[$conSCG]['comprobante'] = $arrCabecera['comprobante'];
				$arrDetalleSCG[$conSCG]['codban']      = $arrCabecera['codban'];
				$arrDetalleSCG[$conSCG]['ctaban']      = $arrCabecera['ctaban'];
				$arrDetalleSCG[$conSCG]['fecha']       = $arrCabecera['fecha'];
				$arrDetalleSCG[$conSCG]['descripcion'] = $arrCabecera['descripcion'];
				$arrDetalleSCG[$conSCG]['sc_cuenta']   = $dataSet->fields['sc_cuenta'];
				$arrDetalleSCG[$conSCG]['procede_doc'] = $arrCabecera['procede'];
				$arrDetalleSCG[$conSCG]['documento']   = $arrCabecera['comprobante'];
				$arrDetalleSCG[$conSCG]['debhab']      = $dataSet->fields['debhab'];
				$arrDetalleSCG[$conSCG]['monto']       = $dataSet->fields['monto'];
				$arrDetalleSCG[$conSCG]['orden']       = $conSCG;
				
				$conSCG++;
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSCG;
	}
	
	public function obtenerDetalleNotaSPG($arrCabecera) 
	{
		$arrDetalleSPG = array();
		$cadenaSql = "SELECT cxp_dc_spg.spg_cuenta, cxp_dc_spg.codestpro, cxp_dc_spg.estcla, cxp_dc_spg.monto, ".
		             "       cxp_documento.estpre, cxp_dc_spg.procede_doc, cxp_dc_spg.numdoccom , cxp_dc_spg.codfuefin ".
		             "  FROM cxp_dc_spg  ".
		             " INNER JOIN cxp_documento ".
					 "    ON cxp_dc_spg.codtipdoc=cxp_documento.codtipdoc ".  
		             " WHERE cxp_dc_spg.codemp='{$this->daoNotaCD->codemp}'  ".
		             "   AND cxp_dc_spg.numsol='{$this->daoNotaCD->numsol}'  ".
		             "   AND cxp_dc_spg.numrecdoc='{$this->daoNotaCD->numrecdoc}' ". 
		             "   AND cxp_dc_spg.codtipdoc='{$this->daoNotaCD->codtipdoc}' ". 
		             "   AND cxp_dc_spg.ced_bene='{$this->daoNotaCD->ced_bene}' ". 
		             "   AND cod_pro='{$this->daoNotaCD->cod_pro}' ". 
		             "   AND cxp_dc_spg.codope='{$this->daoNotaCD->codope}' ". 
		             "   AND cxp_dc_spg.numdc='{$this->daoNotaCD->numdc}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$conSPG = 1;
			while(!$dataSet->EOF)
			{
				$estpre=$dataSet->fields['estpre']; 
				// Mensaje de operacion de gasto a ser aplicada
				$mensaje='';
				if($estpre==1)
				{
					$mensaje='C';
				}
				elseif($estpre==2)
				{
					$mensaje='OC';
				}
				$monto=$dataSet->fields['monto'];
				if($this->daoNotaCD->codope=='NC')
				{
					$monto=($monto*(-1));
				}
				//PREPARANDO ARREGLO CON DETALLES DE GASTO
				$codestpro = $dataSet->fields['codestpro'];
				//ARREGLO SPG
				$arrDetalleSPG[$conSPG]['codemp']      = $arrCabecera['codemp'];
				$arrDetalleSPG[$conSPG]['procede']     = $arrCabecera['procede'];
				$arrDetalleSPG[$conSPG]['comprobante'] = $arrCabecera['comprobante'];
				$arrDetalleSPG[$conSPG]['codban']      = $arrCabecera['codban'];
				$arrDetalleSPG[$conSPG]['ctaban']      = $arrCabecera['ctaban'];
				$arrDetalleSPG[$conSPG]['fecha']       = $arrCabecera['fecha'];
				$arrDetalleSPG[$conSPG]['descripcion'] = $arrCabecera['descripcion'];
				$arrDetalleSPG[$conSPG]['codestpro1']  = substr($codestpro,0,25);
				$arrDetalleSPG[$conSPG]['codestpro2']  = substr($codestpro,25,25);
				$arrDetalleSPG[$conSPG]['codestpro3']  = substr($codestpro,50,25);
				$arrDetalleSPG[$conSPG]['codestpro4']  = substr($codestpro,75,25);
				$arrDetalleSPG[$conSPG]['codestpro5']  = substr($codestpro,100,25);
				$arrDetalleSPG[$conSPG]['estcla']      = $dataSet->fields['estcla'];
				$arrDetalleSPG[$conSPG]['spg_cuenta']  = $dataSet->fields['spg_cuenta'];
				$arrDetalleSPG[$conSPG]['mensaje']     = $mensaje;
				$arrDetalleSPG[$conSPG]['codfuefin']   = $dataSet->fields['codfuefin'];
				$arrDetalleSPG[$conSPG]['monto']       = $monto;
				$arrDetalleSPG[$conSPG]['orden']       = $conSPG;
				if((trim($dataSet->fields['procede_doc'])=='')||(trim($dataSet->fields['numdoccom'])==''))
				{
					$arrDetalleSPG[$conSPG]['procede_doc'] = $arrCabecera['procede'];
					$arrDetalleSPG[$conSPG]['documento']   = $arrCabecera['comprobante'];
				}
				else
				{
					$arrDetalleSPG[$conSPG]['procede_doc'] = $dataSet->fields['procede_doc'];
					$arrDetalleSPG[$conSPG]['documento']   = $dataSet->fields['numdoccom'];
				}
				$conSPG++;
				
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSPG;
	}
	
	public function obtenerDetalleNotaSPI($arrCabecera)
	{
		$arrDetalleSPI = array();
		$cadenaSql = "SELECT cxp_dc_spi.spi_cuenta, cxp_dc_spi.codestpro, cxp_dc_spi.estcla, cxp_dc_spi.monto ".
		             "  FROM cxp_dc_spi  ".
		             " INNER JOIN cxp_documento ".
					 "    ON cxp_dc_spi.codtipdoc=cxp_documento.codtipdoc ". 
					 " WHERE cxp_dc_spi.codemp='".$this->daoNotaCD->codemp."'  ".
		             "   AND cxp_dc_spi.numsol='".$this->daoNotaCD->numsol."'  ".
		             "   AND cxp_dc_spi.numrecdoc='".$this->daoNotaCD->numrecdoc."' ". 
		             "   AND cxp_dc_spi.codtipdoc='".$this->daoNotaCD->codtipdoc."' ". 
		             "   AND cxp_dc_spi.ced_bene='".$this->daoNotaCD->ced_bene."' ". 
		             "   AND cod_pro='".$this->daoNotaCD->cod_pro."' ". 
		             "   AND cxp_dc_spi.codope='".$this->daoNotaCD->codope."' ". 
		             "   AND cxp_dc_spi.numdc='".$this->daoNotaCD->numdc."'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$conSPI = 1;
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
				$arrDetalleSPI[$conSPI]['descripcion'] = $arrCabecera['descripcion'];
				$arrDetalleSPI[$conSPI]['codestpro1']  = substr($codestpro,0,25);
				$arrDetalleSPI[$conSPI]['codestpro2']  = substr($codestpro,25,25);
				$arrDetalleSPI[$conSPI]['codestpro3']  = substr($codestpro,50,25);
				$arrDetalleSPI[$conSPI]['codestpro4']  = substr($codestpro,75,25);
				$arrDetalleSPI[$conSPI]['codestpro5']  = substr($codestpro,100,25);
				$arrDetalleSPI[$conSPI]['estcla']      = $dataSet->fields['estcla'];
				$arrDetalleSPI[$conSPI]['spi_cuenta']  = $dataSet->fields['spi_cuenta'];
				$arrDetalleSPI[$conSPI]['procede_doc'] = $arrCabecera['procede'];
				$arrDetalleSPI[$conSPI]['documento']   = $arrCabecera['comprobante'];
				$arrDetalleSPI[$conSPI]['mensaje']     = 'DC';
				$arrDetalleSPI[$conSPI]['monto']       = $dataSet->fields['monto'];
				$arrDetalleSPI[$conSPI]['orden']       = $conSPI;
				$conSPI++;
				
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSPI;
	}
	
	public function contabilizarNCD($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $arrEvento) 
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA NOTA A CONTABILIZAR
		$criterio = "      codemp = '{$this->codemp}'  ".
		            " AND numsol='{$numsol}' ".
					" AND numrecdoc= '{$numrecdoc}' ".
		            " AND codtipdoc='{$codtipdoc}' ".
					" AND ced_bene='{$ced_bene}' ".
		            " AND cod_pro='{$cod_pro}' ".
					" AND codope='{$codope}' ".
		            " AND numdc='{$numdc}' ".
					" AND estnotadc='E'";
		$this->daoNotaCD = FabricaDao::CrearDAO('C','cxp_sol_dc','',$criterio);
		// VERIFICO QUE LA NOTA EXISTA
		if($this->daoNotaCD->numdc=='') 
		{
			switch($codope)
			{
				case 'ND':
					$mensaje = 'No existe la Nota de D&#233;bito ';
					break;
				case 'NC':
					$mensaje = 'No existe la Nota de Cr&#233;dito';
					break;
			}
			$this->mensaje .= "ERROR -> {$mensaje}, en estatus EMITIDA";
			$this->valido = false;			
		}
		if ($this->valido) 
		{
			$procede='';
			$tipoDestino='';
			switch($this->daoNotaCD->codope) 
			{
				case 'ND':
					$procede="CXPNOD";
					break;
				case 'NC':
					$procede="CXPNOC";
					break;
			}
			if ($this->daoNotaCD->cod_pro=='----------' && $this->daoNotaCD->ced_bene=='----------')
			{
				$tipoDestino = '-';
			}
			elseif ($this->daoNotaCD->cod_pro=='----------')
			{
				$tipoDestino = 'B';
			}
			elseif ($this->daoNotaCD->ced_bene=='----------') 
			{
				$tipoDestino = 'P';
			}
			$arrCabecera['codemp'] = $this->daoNotaCD->codemp;
			$arrCabecera['procede'] = $procede;
			$arrCabecera['comprobante'] = fillComprobante($this->daoNotaCD->numdc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoNotaCD->fecope;
			$arrCabecera['descripcion'] = $this->daoNotaCD->desope;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $tipoDestino;
			$arrCabecera['cod_pro'] = $this->daoNotaCD->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoNotaCD->ced_bene;
			$arrCabecera['total'] = number_format($this->daoNotaCD->monto,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = '--';
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$arrDetalleSCG = $this->obtenerDetalleNotaSCG($arrCabecera);
			$arrDetalleSPG = array();
			$arrDetalleSPI = array();
			if(($this->daoNotaCD->codope=='ND')||($this->estafenc==0))
			{			
				$arrDetalleSPG = $this->obtenerDetalleNotaSPG($arrCabecera);
			}
			else
			{			
				$arrDetalleSPI = $this->obtenerDetalleNotaSPI($arrCabecera);
			}
			if($this->valido)
			{
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->guardarComprobante($arrCabecera, $arrDetalleSPG, $arrDetalleSCG, $arrDetalleSPI, $arrEvento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				if($this->valido)
				{
					$this->daoNotaCD->estnotadc  = 'C';
					$this->daoNotaCD->fechaconta = $this->daoNotaCD->fecope;
					$this->daoNotaCD->fechaanula = '1900-01-01';
					$resultado =  $this->daoNotaCD->modificar();
					if($resultado == 0 || $resultado == 2)
					{
						$this->valido = false;
						if($resultado == 0)
						{
							$this->mensaje .= $this->daoNotaCD->ErrorMsg;
						}
						else
						{
							$this->mensaje .= "La nota {$this->daoNotaCD->codope}  {$this->daoNotaCD->numdc} que intenta procesar no existe"; 
						}
					}
				}
				unset($serviciocomprobante);
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
	
	public function procesoContabilizarNCD($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->notas);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_contabiliza_cxpndnc.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Se contabilizo la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarNCD($arrJson->notas[$j]->numsol, $arrJson->notas[$j]->numrecdoc, $arrJson->notas[$j]->codtipdoc, 
			                           $arrJson->notas[$j]->ced_bene, $arrJson->notas[$j]->cod_pro, $arrJson->notas[$j]->codope, 
			                           $arrJson->notas[$j]->numdc, $arrEvento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->notas[$j]->numdc;
				$arrRespuesta[$h]['mensaje'] = "Se contabilizo exitosamente la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->notas[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = "No se pudo contabilizar la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}, {$this->mensaje}";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function revContabilizarNCD($numsol, $numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $codope, $numdc, $arrEvento)
	{
		DaoGenerico::iniciarTrans();
		// OBTENGO LA NOTA A CONTABILIZAR
		$criterio = "    codemp = '{$this->codemp}' ". 
		            "AND numsol='{$numsol}'  ".
		            "AND numrecdoc= '{$numrecdoc}' ".  
		            "AND codtipdoc='{$codtipdoc}' ". 
		            "AND ced_bene='{$ced_bene}' ". 
		            "AND cod_pro='{$cod_pro}' ". 
		            "AND codope='{$codope}' ". 
		            "AND numdc='{$numdc}' ". 
		            "AND estnotadc='C'";
		$this->daoNotaCD = FabricaDao::CrearDAO('C','cxp_sol_dc','',$criterio);
		// VERIFICO QUE LA NOTA EXISTA
		if($this->daoNotaCD->numdc=='')
		{
			switch($codope)
			{
				case 'ND':
					$mensaje = 'No existe la Nota de D&#233;bito';
					break;
				case 'NC':
					$mensaje = 'No existe la Nota de Cr&#233;dito';
					break;
			}
			$this->mensaje .= "ERROR -> {$mensaje}, en estatus EMITIDA";
			$this->valido = false;			
		}		
		
		if ($this->valido)
		{
			$procede='';
			$tipoDestino='';
			switch($this->daoNotaCD->codope)
			{
				case 'ND':
					$procede="CXPNOD";
					break;
				case 'NC':
					$procede="CXPNOC";
					break;
			}
			if ($this->daoNotaCD->cod_pro=='----------' && $this->daoNotaCD->ced_bene=='----------')
			{
				$tipoDestino = '-';
			}
			elseif ($this->daoNotaCD->cod_pro=='----------')
			{
				$tipoDestino = 'B';
			}
			elseif ($this->daoNotaCD->cod_pro=='----------')
			{
				$tipoDestino = 'P';
			}
			$arrCabecera['codemp'] = $this->daoNotaCD->codemp;
			$arrCabecera['procede'] = $procede;
			$arrCabecera['comprobante'] = fillComprobante($this->daoNotaCD->numdc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoNotaCD->fecope;
			$arrCabecera['descripcion'] = $this->daoNotaCD->desope;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $tipoDestino;
			$arrCabecera['cod_pro'] = $this->daoNotaCD->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoNotaCD->ced_bene;
			$arrCabecera['total'] = number_format($this->daoNotaCD->monto,2,'.','');
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
				$this->daoNotaCD->estnotadc  = 'E';
				$this->daoNotaCD->fechaconta = '1900-01-01';
				$this->daoNotaCD->fechaanula = '1900-01-01';
				$resultado =  $this->daoNotaCD->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoNotaCD->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La nota {$this->daoNotaCD->codope}  {$this->daoNotaCD->numdc} que intenta procesar no existe"; 
					}
				}
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
		else {
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoRevContabilizarNCD($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->notas);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_contabiliza_cxpndnc.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Se  reverso la contabilizacion de la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizarNCD($arrJson->notas[$j]->numsol, $arrJson->notas[$j]->numrecdoc, $arrJson->notas[$j]->codtipdoc, 
			                           $arrJson->notas[$j]->ced_bene, $arrJson->notas[$j]->cod_pro, $arrJson->notas[$j]->codope, 
			                           $arrJson->notas[$j]->numdc, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->notas[$j]->numdc;
				$arrRespuesta[$h]['mensaje'] = "Se reverso la contabilizaci&#243;n exitosamente la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->notas[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = "No se pudo reversar la contabilizaci&#243;n la {$arrJson->notas[$j]->codope} {$arrJson->notas[$j]->numdc}, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
}
?>