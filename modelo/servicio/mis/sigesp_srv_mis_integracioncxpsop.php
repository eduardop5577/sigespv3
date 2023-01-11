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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracioncxpsop.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionCXPSOP implements IIntegracionCXPSOP {
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos;
	private $servicioComprobante;
	private $daoSolicitud;
	private $daoRecepcion;
	private $daoHistoricoSolicitud;
			
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$this->codemp = $_SESSION['la_empresa']['codemp'];
	}
	
	/***********************************************
	 * INTEGRACRION DE SOLICITUD DE PAGO 
	 ***********************************************/	
	public function buscarSolicitudesIntegrar($numsol,$fecreg,$fecapr,$tipo,$codigo, $estatus, $estrepcon)
	{
		$criterio="";
		if(!empty($numsol))
		{
			$criterio .= " AND numsol like '%".$numsol."%'";
		}
		if(!empty($fecreg))
		{
			$fecreg=convertirFechaBd($fecreg);
			$criterio .= " AND fecemisol = '".$fecreg."'";
		}
		if(!empty($fecapr))
		{
			$fecapr=convertirFechaBd($fecapr);
			$criterio .= " AND fecaprosol = '".$fecapr."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipproben = '".$tipo."' ";
			if(!empty($codigo))
			{
				switch($tipo)
				{
					case "P": // es un proveedor
						$criterio .= " AND cod_pro = '".$codigo."' ";
						break;
	
					case "B": // es un beneficiario
						$criterio .= " AND ced_bene = '".$codigo."' ";
						break;
				}
			}
		}
		$filtrofrom ='';
		if(($this->estfilpremod=='1')&&(trim($estrepcon)==''))
		{
			$estconcat = $this->conexionBaseDatos->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
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
						" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
						" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
						" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
						" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
						" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ".
						" AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
						" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
						" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
						" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
						" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
						" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
						" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			
			
			$filtrofrom = ",cxp_dt_solicitudes, cxp_rd, cxp_rd_spg";			
		}
		
		if ($estrepcon=='1')
		{
			$filtrofrom = " ,cxp_dt_solicitudes, cxp_rd, cxp_documento";
			$criterio   = " AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
						  " AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
						  " AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
						  " AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
						  " AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
						  " AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
						  " AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
						  " AND cxp_rd.codtipdoc = cxp_documento.codtipdoc ".
						  " AND cxp_documento.estcon=1 ". 
						  " AND cxp_documento.estpre=4";
		}
		$cadenasql="SELECT cxp_solicitudes.numsol, cxp_solicitudes.fecemisol, cxp_solicitudes.consol, cxp_solicitudes.fechaconta, ".
				   "	   cxp_solicitudes.fechaanula,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene,MAX(cxp_solicitudes.tipproben) as tipproben,".
				   "       (SELECT nompro FROM rpc_proveedor WHERE cxp_solicitudes.codemp=rpc_proveedor.codemp AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE cxp_solicitudes.codemp=rpc_beneficiario.codemp AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE cxp_solicitudes.codemp=rpc_beneficiario.codemp AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM cxp_solicitudes  ".$filtrofrom.
				   "  WHERE cxp_solicitudes.codemp = '{$this->codemp}' ". 
				   "  AND cxp_solicitudes.estprosol = '{$estatus}'  ".
				   "  AND cxp_solicitudes.estaprosol = 1 ".
			    	$criterio.
				   " GROUP BY cxp_solicitudes.codemp, cxp_solicitudes.numsol, cxp_solicitudes.fecemisol, cxp_solicitudes.consol, cxp_solicitudes.fechaconta, cxp_solicitudes.fechaanula, cxp_solicitudes.cod_pro, cxp_solicitudes.ced_bene ".
				   " ORDER BY cxp_solicitudes.numsol ";
		$dataCXP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataCXP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataCXP;
	}
	
	public function buscarDetallePresupuesto($numsol)
	{
		$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
		$cadenaSQL = "SELECT doc.estpre, dtspg.codestpro, dtspg.estcla, dtspg.spg_cuenta, SUM(dtspg.monto) AS monto, MAX(spg_cuentas.denominacion) AS denominacion ".
					 "  FROM cxp_solicitudes sol ".
					 " INNER JOIN cxp_dt_solicitudes dtsol ".
					 "    ON sol.codemp=dtsol.codemp ".
					 "   AND sol.numsol=dtsol.numsol ".
					 " INNER JOIN cxp_documento doc ".
					 "    ON dtsol.codemp=doc.codemp ".
					 "   AND dtsol.codtipdoc=doc.codtipdoc ".
					 " INNER JOIN cxp_rd_spg dtspg ".
					 "    ON dtsol.codemp=dtspg.codemp ".
					 "   AND dtsol.numrecdoc=dtspg.numrecdoc ".
					 "   AND dtsol.codtipdoc=dtspg.codtipdoc ".
					 "   AND dtsol.ced_bene=dtspg.ced_bene ".
					 "   AND dtsol.cod_pro=dtspg.cod_pro ".      
					 " INNER JOIN spg_cuentas ". 
					 "    ON dtspg.codemp = spg_cuentas.codemp ". 
					 "   AND dtspg.codestpro =  ".$codestpro." ".
					 "   AND dtspg.estcla = spg_cuentas.estcla ". 
					 "   AND dtspg.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE sol.codemp='{$this->codemp}' ".
					 "   AND sol.numsol='{$numsol}' ".
					 " GROUP BY doc.estpre, dtspg.codestpro, dtspg.estcla, dtspg.spg_cuenta";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function obtenerDetalleComprobanteSOPSPG($numsol, $fecsol)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numsol);
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
				if ($nivelCuenta <= $valiNivel)
				{
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
						$_SESSION['fechacomprobante'] = $fecsol;
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
			switch($_SESSION["la_empresa"]["estmodest"])
			{
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
	
	public function obtenerDetalleComprobanteSOPSCG($numsol)
	{
		$cadenaSql="SELECT dtscg.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
		           "       (CASE WHEN dtscg.debhab = 'D' THEN SUM(dtscg.monto) ELSE 0 END) AS debe, ".
		           "       (CASE WHEN dtscg.debhab = 'H' THEN SUM(dtscg.monto) ELSE 0 end) AS haber ".
		           "  FROM cxp_solicitudes sol ".
		           " INNER JOIN cxp_dt_solicitudes dtsol ".
		           "    ON sol.codemp=dtsol.codemp ".
		           "   AND sol.numsol=dtsol.numsol ".
		           " INNER JOIN cxp_rd_scg dtscg ".
		           "    ON dtsol.codemp=dtscg.codemp ".
		           "   AND dtsol.numrecdoc=dtscg.numrecdoc ".
		           "   AND dtsol.codtipdoc=dtscg.codtipdoc ".
		           "   AND dtsol.ced_bene=dtscg.ced_bene ".
		           "   AND dtsol.cod_pro=dtscg.cod_pro ".      
				   " INNER JOIN scg_cuentas ". 
				   "    ON dtscg.codemp = scg_cuentas.codemp ". 
				   "   AND dtscg.sc_cuenta = scg_cuentas.sc_cuenta ". 
				   " WHERE sol.codemp='{$this->codemp}' ".
		           "   AND sol.numsol='{$numsol}' ".
		           " GROUP BY dtscg.debhab, dtscg.sc_cuenta ".
		           " ORDER BY dtscg.debhab, dtscg.sc_cuenta";
		return $this->conexionBaseDatos->Execute ( $cadenaSql );
	}
	
	public function obtenerDetalleSolicitudSPG($arrCabecera)
	{
		$arrDetalleSPG = array();
				
		$cadenaSql="SELECT doc.estpre, dtspg.procede_doc, dtspg.numdoccom, dtspg.codestpro, dtspg.estcla, ". 
       			   "	   dtspg.spg_cuenta, dtspg.codfuefin, SUM(dtspg.monto) AS monto ".
       			   "  FROM cxp_solicitudes sol ".
       			   " INNER JOIN cxp_dt_solicitudes dtsol ".
       			   "    ON sol.codemp=dtsol.codemp ".
       			   "   AND sol.numsol=dtsol.numsol ".
       			   " INNER JOIN cxp_documento doc ".
       			   "    ON dtsol.codemp=doc.codemp ".
       			   "   AND dtsol.codtipdoc=doc.codtipdoc ".
       			   " INNER JOIN cxp_rd_spg dtspg ".
       			   "    ON dtsol.codemp=dtspg.codemp ".
       			   "   AND dtsol.numrecdoc=dtspg.numrecdoc ".
       			   "   AND dtsol.codtipdoc=dtspg.codtipdoc ".
       			   "   AND dtsol.ced_bene=dtspg.ced_bene ".
       			   "   AND dtsol.cod_pro=dtspg.cod_pro ".      
       			   " WHERE sol.codemp='{$this->daoSolicitud->codemp}' ".
       			   "   AND sol.numsol='{$this->daoSolicitud->numsol}' ".
       			   "   AND sol.estprosol='E' ". 
       			   "   AND sol.estaprosol=1 ".
       			   " GROUP BY doc.estpre, dtspg.procede_doc, dtspg.numdoccom, dtspg.codestpro, dtspg.estcla, dtspg.spg_cuenta, dtspg.codfuefin";
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
				//PREPARANDO ARREGLO CON DETALLES DE GASTO
				if ($estpre!=3)
				{
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
					$arrDetalleSPG[$conSPG]['procede_doc'] = $dataSet->fields['procede_doc'];
					$arrDetalleSPG[$conSPG]['documento']   = $dataSet->fields['numdoccom'];
					$arrDetalleSPG[$conSPG]['mensaje']     = $mensaje;
					$arrDetalleSPG[$conSPG]['codfuefin']   = $dataSet->fields['codfuefin'];
					$arrDetalleSPG[$conSPG]['monto']       = $dataSet->fields['monto'];
					$arrDetalleSPG[$conSPG]['orden']       = $conSPG;
					
					$conSPG++;
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $arrDetalleSPG;
	}
	
	public function obtenerDetalleSolicitudSCG($arrCabecera)
	{
		$arrDetalleSCG = array();
		$cadenaSql="SELECT doc.estcon, dtscg.procede_doc, dtscg.numdoccom, dtscg.debhab, ". 
       			   "	   dtscg.sc_cuenta, SUM(dtscg.monto) AS monto ".
       			   "  FROM cxp_solicitudes sol ".
       			   " INNER JOIN cxp_dt_solicitudes dtsol ".
       			   "    ON sol.codemp=dtsol.codemp ".
       			   "   AND sol.numsol=dtsol.numsol ".
       			   " INNER JOIN cxp_documento doc ".
       			   "    ON dtsol.codemp=doc.codemp ".
       			   "   AND dtsol.codtipdoc=doc.codtipdoc ".
       			   " INNER JOIN cxp_rd_scg dtscg ".
       			   "    ON dtsol.codemp=dtscg.codemp ".
       			   "   AND dtsol.numrecdoc=dtscg.numrecdoc ".
       			   "   AND dtsol.codtipdoc=dtscg.codtipdoc ".
       			   "   AND dtsol.ced_bene=dtscg.ced_bene ".
       			   "   AND dtsol.cod_pro=dtscg.cod_pro ".      
       			   " WHERE sol.codemp='{$this->daoSolicitud->codemp}' ".
       			   "   AND sol.numsol='{$this->daoSolicitud->numsol}' ".
       			   "   AND sol.estprosol='E' ". 
       			   "   AND sol.estaprosol=1 ".
       			   " GROUP BY doc.estcon, doc.estpre, dtscg.procede_doc, dtscg.numdoccom, dtscg.debhab, dtscg.sc_cuenta";
		
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
					$arrDetalleSCG[$conSCG]['descripcion'] = $arrCabecera['descripcion'];
					$arrDetalleSCG[$conSCG]['sc_cuenta']   = $dataSet->fields['sc_cuenta'];
					$arrDetalleSCG[$conSCG]['procede_doc'] = $dataSet->fields['procede_doc'];
					$arrDetalleSCG[$conSCG]['documento']   = $dataSet->fields['numdoccom'];
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
	
	public function obtenerDetalleSolRecepcionSCG($arrCabecera)
	{
		$arrDetalleSCG = array();
		$cadenaSql="SELECT sc_cuenta, procede_doc, numdoccom, debhab, monto ". 
				   "  FROM cxp_solicitudes_scg ". 
				   " WHERE codemp='{$this->daoSolicitud->codemp}' ". 
				   "   AND numsol='{$this->daoSolicitud->numsol}'";
		
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
				$arrDetalleSCG[$conSCG]['procede_doc'] = $dataSet->fields['procede_doc'];
				$arrDetalleSCG[$conSCG]['documento']   = $dataSet->fields['numdoccom'];
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
	
	public function actualizarEstatusDetalleSolicitud($estatus, $validar)
	{
		$cadenaSql="SELECT dtsop.numrecdoc, dtsop.codtipdoc, dtsop.ced_bene, dtsop.cod_pro, cxp_rd.estprodoc ".
				   "  FROM cxp_dt_solicitudes dtsop ".
				   " INNER JOIN cxp_rd ".
				   "    ON dtsop.codemp = cxp_rd.codemp ".
				   "   AND dtsop.numrecdoc = cxp_rd.numrecdoc ".
				   "   AND dtsop.codtipdoc = cxp_rd.codtipdoc ".
				   "   AND dtsop.ced_bene = cxp_rd.ced_bene ".
				   "   AND dtsop.cod_pro = cxp_rd.cod_pro ". 
				   " WHERE dtsop.codemp='{$this->daoSolicitud->codemp}' ".  
				   "  AND dtsop.numsol='{$this->daoSolicitud->numsol}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while ((!$dataSet->EOF)&&($this->valido))
			{
				$estprodoc = $dataSet->fields['estprodoc'];
				if((($estprodoc=="R")&&($estatus=="C"))||(!$validar))
				{
					$criterio="     codemp='{$this->daoSolicitud->codemp}' ".
				              " AND cod_pro='{$dataSet->fields['cod_pro']}' ". 
				              " AND ced_bene='{$dataSet->fields['ced_bene']}' ".
				              " AND codtipdoc='{$dataSet->fields['codtipdoc']}' ". 
				              " AND numrecdoc='{$dataSet->fields['numrecdoc']}'";
					$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
					$this->daoRecepcion->estprodoc = $estatus;
					$resultado = $this->daoRecepcion->modificar();
					if($resultado == 0 || $resultado == 2)
					{
						$this->valido = false;
						if($resultado == 0)
						{
							$this->mensaje .= $this->daoRecepcion->ErrorMsg;
						}
						else
						{
							$this->mensaje .= "La recepcion de documentos {$dataSet->fields['numrecdoc']} que intenta procesar no existe"; 
						}
					}
				}
				
				unset($this->daoRecepcion);
				$dataSet->MoveNext();
			}
		}
		return $this->valido;
	}
	
	public function insertarHistoricoSolicitud($estatus,$fecha)
	{
		$this->daoHistoricoSolicitud = FabricaDao::CrearDAO('N','cxp_historico_solicitud');
		$this->daoHistoricoSolicitud->codemp = $this->daoSolicitud->codemp;
		$this->daoHistoricoSolicitud->numsol = $this->daoSolicitud->numsol;
		$this->daoHistoricoSolicitud->fecha  = $fecha;
		$this->daoHistoricoSolicitud->estprodoc = $estatus;
		if ($this->daoHistoricoSolicitud->incluir(false,'',false,0,true))
		{
			$this->valido = true;
		}
		else
		{
			$this->valido = false;
			$this->mensaje .= $this->daoHistoricoSolicitud->ErrorMsg;
		}
		
		return $this->valido;
	}
	
	public function contabilizarSOP($numsol, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND estaprosol = 1 AND estprosol = 'E' ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud de pago, en estatus EMITIDA y APROBADA';
			$this->valido = false;			
		}
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrCabecera['procede'] = 'CXPSOP';
			$arrCabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoSolicitud->fecemisol;
			$arrCabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoSolicitud->tipproben;
			$arrCabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrCabecera['total'] = number_format($this->daoSolicitud->monsol,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$arrDetalleSPG=$this->obtenerDetalleSolicitudSPG($arrCabecera);
			$arrDetalleSCG=$this->obtenerDetalleSolicitudSCG($arrCabecera);
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrCabecera,$arrDetalleSPG,$arrDetalleSCG,null,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estprosol = 'C';
				$this->daoSolicitud->fechaconta = $this->daoSolicitud->fecemisol;
				$this->daoSolicitud->fechaanula = '1900-01-01';
				$this->daoSolicitud->conanusol = '';
				$resultado =  $this->daoSolicitud->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoSolicitud->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La solicitud de pago  {$this->daoSolicitud->numsol} que intenta procesar no existe"; 
					}
				}
				else
				{
					if ($this->actualizarEstatusDetalleSolicitud('C', false))
					{
						$this->insertarHistoricoSolicitud('C',$this->daoSolicitud->fecemisol);					
					}
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
		unset($this->daoSolicitud);
		unset($this->daoRecepcion);
		unset($this->daoHistoricoSolicitud);
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function contabilizarSopRD($numsol, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND estaprosol = 1 AND estprosol = 'E' ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud de pago, en estatus EMITIDA y APROBADA';
			$this->valido = false;			
		}
				
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrCabecera['procede'] = 'CXPSOP';
			$arrCabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoSolicitud->fecemisol;
			$arrCabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoSolicitud->tipproben;
			$arrCabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrCabecera['total'] = number_format($this->daoSolicitud->monsol,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$arrDetalleSCG=$this->obtenerDetalleSolRecepcionSCG($arrCabecera);
		}
		
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrCabecera,null,$arrDetalleSCG,null,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estprosol = 'C';
				$this->daoSolicitud->fechaconta = $this->daoSolicitud->fecemisol;
				$this->daoSolicitud->fechaanula = '1900-01-01';
				$this->daoSolicitud->conanusol = '';
				$resultado =  $this->daoSolicitud->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La solicitud de pago  {$this->daoSolicitud->numsol} que intenta procesar no existe"; 
					}
				}
				else
				{
					$this->insertarHistoricoSolicitud('C',$this->daoSolicitud->fecemisol);
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
		if(DaoGenerico::completarTrans($this->valido))
		{
			$servicioEvento->incluirEvento();
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($this->daoSolicitud);
		unset($this->daoHistoricoSolicitud);
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoContabilizarSOP($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->solicitudes);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_contabiliza_cxpsop.html';
		if($_SESSION["la_empresa"]["conrecdoc"]=='1')
		{
			for($j=0;$j<=$nSol-1;$j++)
			{
				$arrEvento['desevetra'] = "Contabilizo la SOP {$arrJson->solicitudes[$j]->numsol}, asociado a la empresa {$this->codemp}";
				if ($this->contabilizarSopRD($arrJson->solicitudes[$j]->numsol, $arrEvento))
				{
					$nOk++;
					$arrRespuesta[$h]['estatus'] = 1;
					$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
					$arrRespuesta[$h]['mensaje'] = 'Solicitud de pago contabilizada exitosamente';
				}
				else
				{
					$nEr++;
					$arrRespuesta[$h]['estatus'] = 0;
					$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
					$arrRespuesta[$h]['mensaje'] = "Solicitud de pago no fue contabilizada, {$this->mensaje} ";
				}
				$h++;
				$this->valido=true;
				$this->mensaje='';
			}
		}
		else
		{
			for($j=0;$j<=$nSol-1;$j++)
			{
				$arrEvento['desevetra'] = "Contabilizo la SOP {$arrJson->solicitudes[$j]->numsol}, asociado a la empresa {$this->codemp}";
				if ($this->contabilizarSOP($arrJson->solicitudes[$j]->numsol, $arrEvento))
				{
					$nOk++;
					$arrRespuesta[$h]['estatus'] = 1;
					$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
					$arrRespuesta[$h]['mensaje'] = 'Solicitud de pago contabilizada exitosamente';
				}
				else
				{
					$nEr++;
					$arrRespuesta[$h]['estatus'] = 0;
					$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
					$arrRespuesta[$h]['mensaje'] = "Solicitud de pago no fue contabilizada, {$this->mensaje} ";
				}
				$h++;
				$this->valido=true;
				$this->mensaje='';
			}
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function validarAsientoCierre($documeto)
	{
		$existe = false;
		$cadenaSql="SELECT comprobante ".
                   "  FROM spg_dt_cmp ". 
                   " WHERE codemp='{$this->daoSolicitud->codemp}' ". 
                   "   AND procede='SOCROC' ".
                   "   AND procede_doc='SOCCOC' ".
                   "   AND documento='{$documento}'";
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
				$existe=true;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function obtenerNumeroCompromiso($numrecdoc, $codtipdoc, $codpro, $ced_bene)
	{
		$numcomp = '';
		$cadenaSql="SELECT numdoccom ".
				   "  FROM cxp_rd_spg ". 
				   " WHERE codemp='{$this->daoSolicitud->codemp}' ". 
				   "   AND numrecdoc='{$numrecdoc}' ". 
				   "   AND codtipdoc='{$codtipdoc}' ". 
				   "   AND ced_bene='{$ced_bene}' ". 
				   "   AND cod_pro='{$codpro}'";
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
				$numcomp = $dataSet->fields['numdoccom'];
			}
		}
		unset($dataSet);
		return $numcomp;
	}
	
	public function validarCierreOC($numsol)
	{
		$existeCierre = false;
		$cadenaSql="SELECT numrecdoc,codtipdoc,cod_pro,ced_bene ". 
                   "  FROM cxp_dt_solicitudes ".
                   " WHERE codemp='{$this->daoSolicitud->codemp}' ". 
                   "   AND numsol='{$this->daoSolicitud->numsol}'";
		
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$dataSet->EOF)&&(!$existeCierre))
			{
				$numrecdoc = $dataSet->fields['numrecdoc'];
				$codtipdoc = $dataSet->fields['codtipdoc'];
				$codpro    = $dataSet->fields['cod_pro'];
				$ced_bene  = $dataSet->fields['ced_bene'];
				$numcomp = $this->obtenerNumeroCompromiso($numrecdoc, $codtipdoc, $codpro, $ced_bene);
				if ($numcomp!='')
				{
					$existeCierre = $this->validarAsientoCierre($numcomp);
				}
				if($existeCierre)
				{
					$this->mensaje .= '  ->La orden de compra '.$numcomp.' Tiene un cierre, debe reversarlo primero ';
				}				
				$dataSet->MoveNext();
			}
		}
		return $existeCierre;
	}
	
	public function eliminarHistoricoSolicitud($estatus)
	{
		$this->daoHistoricoSolicitud = FabricaDao::CrearDAO('N','cxp_historico_solicitud');
		$this->daoHistoricoSolicitud->codemp = $this->daoSolicitud->codemp;
		$this->daoHistoricoSolicitud->numsol = $this->daoSolicitud->numsol;
		$this->daoHistoricoSolicitud->estprodoc = $estatus;
		if ($this->daoHistoricoSolicitud->eliminar())
		{
			$this->valido = true;
		}
		else
		{
			$this->valido = false;
			$this->mensaje .= $this->daoHistoricoSolicitud->ErrorMsg;
		}
		
		return $this->valido;
	}
	
	public function revContabilizaSOP($numsol,$arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A REVERSAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND estprosol = 'C' ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud de pago, en estatus CONTABILIZADA';
			$this->valido = false;			
		}		
		// VERIFICO SI EXISTE UN OC CERRADA RELACIONADA A LA SOLICITUD
		if ($this->validarCierreOC($numsol))
		{
			$this->mensaje .= 'ERROR -> Debe reversar el cierre de O/C de uno de los compromisos causados en la Solicitud N° '.$numsol;
			$this->valido = false;
		}
		
		if($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrCabecera['procede'] = 'CXPSOP';
			$arrCabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoSolicitud->fecemisol;
			$arrCabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoSolicitud->tipproben;
			$arrCabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrCabecera['total'] = number_format($this->daoSolicitud->monsol,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrCabecera,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estprosol = 'E';
				$this->daoSolicitud->fechaconta = '1900-01-01';
				$this->daoSolicitud->fechaanula = '1900-01-01';
				$this->daoSolicitud->conanusol = '';
				$resultado =  $this->daoSolicitud->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La solicitud de pago  {$this->daoSolicitud->numsol} que intenta procesar no existe"; 
					}
				}
				else
				{
					if($_SESSION["la_empresa"]["conrecdoc"]=='0')
					{
						$this->actualizarEstatusDetalleSolicitud('E', false);
					}
					if($this->valido)
					{				
						$this->eliminarHistoricoSolicitud('C');
					}
				}				
			}
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

	public function procesoReversoContabilizaSOP($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->solicitudes);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_contabiliza_cxpsop.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Reverso la contabilizacion de la SOP {$arrJson->solicitudes[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizaSOP($arrJson->solicitudes[$j]->numsol, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Se reverso la contabilizaci&#243;n de la solicitud de pago exitosamente';
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = "No se ejecuto el reverso de la contabilizaci&#243;n de la solicitud de pago, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function anularSOP($numsol, $fecha, $conanusop, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A ANULAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND estprosol = 'C' ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud de pago, en estatus CONTABILIZADA';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE ANULACIÓN  SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoSolicitud->fecemisol,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulaci&#243;n '.$fecha.' es menor que la fecha de Emision '.$this->daoSolicitud->fecemisol.' de la Solicitud Nº '.$numsol;
			$this->valido = false;			
		}
		
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrCabecera['procede'] = 'CXPSOP';
			$arrCabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoSolicitud->fecemisol;
			$arrCabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoSolicitud->tipproben;
			$arrCabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrCabecera['total'] = number_format($this->daoSolicitud->monsol,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrCabecera,$fecha,'CXPAOP',$conanusop,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estprosol  = 'A';
				$this->daoSolicitud->fechaanula = $fecha;
				$this->daoSolicitud->conanusol  = $conanusop;
				$resultado =  $this->daoSolicitud->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La solicitud de pago  {$this->daoSolicitud->numsol} que intenta procesar no existe"; 
					}
				}
				else
				{
					if($this->valido && $_SESSION["la_empresa"]["conrecdoc"]=="0")
					{
						$this->actualizarEstatusDetalleSolicitud('R', false);
					}
					if ($this->valido)
					{
						$this->insertarHistoricoSolicitud('A',$fecha);
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
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoAnularSOP($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->solicitudes);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_anula_cxpsop.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Anulo la contabilizacion de la SOP {$arrJson->solicitudes[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->anularSOP($arrJson->solicitudes[$j]->numsol, $arrJson->fecanusol, $arrJson->solicitudes[$j]->conanusop, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Se anulo la contabilizaci&#243;n de la Solicitud de pago exitosamente';
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = "No se ejecuto la anulaci&#243;n de la contabilizaci&#243;n de la solicitud de pago, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function revAnulacionSOP($numsol,$arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A REVERSAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND estprosol = 'A' ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud de pago, en estatus ANULADA';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA RECEPCION NO ESTE ANULADA
		if($this->validarRecepcionAnulada())
		{
			$this->mensaje .= 'ERROR -> La Recepcion de Documentos Asociada esta en estatus ANULADA. No se puede hacer el reverso de la anulacion.';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA RECEPCION NO ESTE ASOCIADA A OTRA SOLICITUD DE PAGO
		if($this->validarRecepcion())
		{
			$this->mensaje .= 'ERROR -> La Recepcion de Documentos Asociada esta en otra Solicitud de pago. No se puede hacer el reverso de la anulacion.';
			$this->valido = false;			
		}		
				
		if($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrCabecera['procede'] = 'CXPAOP';
			$arrCabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoSolicitud->fechaanula;
			$arrCabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoSolicitud->tipproben;
			$arrCabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrCabecera['total'] = number_format($this->daoSolicitud->monsol,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrCabecera,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estprosol  = 'C';
				$this->daoSolicitud->fechaconta = $this->daoSolicitud->fecemisol;
				$this->daoSolicitud->fechaanula = '1900-01-01';
				$this->daoSolicitud->conanusol  = '';
				$resultado =  $this->daoSolicitud->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La solicitud de pago  {$this->daoSolicitud->numsol} que intenta procesar no existe"; 
					}
				}
				else
				{
					if($this->valido && $_SESSION["la_empresa"]["conrecdoc"]=="0")
					{
						$this->actualizarEstatusDetalleSolicitud('C', true);
					}
					if ($this->valido)
					{
						$this->eliminarHistoricoSolicitud('A');
					}
				}				
			}
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
	
	public function procesoReversoAnulacionSOP($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->solicitudes);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_anula_cxpsop.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Reverso la anulacion de la contabilizacion de la SOP {$arrJson->solicitudes[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->revAnulacionSOP($arrJson->solicitudes[$j]->numsol, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Se reverso la anulaci&#243;n de la solicitud de pago exitosamente';
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->solicitudes[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = "No se ejecuto el reverso de la anulaci&#243;n de la solicitud de pago, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function validarRecepcionAnulada()
	{
		$validarRecepcionAnulada = false;
		$cadenaSql="SELECT cxp_solicitudes.numsol ".
                   "  FROM cxp_solicitudes,cxp_dt_solicitudes, cxp_rd  ".
                   " WHERE cxp_solicitudes.codemp='{$this->daoSolicitud->codemp}' ". 
                   "   AND cxp_solicitudes.numsol='{$this->daoSolicitud->numsol}'".
				   "   AND cxp_rd.estprodoc = 'A' ".
                   "   AND cxp_dt_solicitudes.codemp = cxp_solicitudes.codemp  ".
                   "   AND cxp_dt_solicitudes.numsol = cxp_solicitudes.numsol ".
                   "   AND cxp_dt_solicitudes.codemp = cxp_rd.codemp  ".
                   "   AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
                   "   AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
                   "   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
                   "   AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ";
		
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataSet->EOF)
			{
				$validarRecepcionAnulada = true;
			}
		}
		return $validarRecepcionAnulada;
	}	

	public function validarRecepcion()
	{
		$validarRecepcion = false;
		$cadenaSql="SELECT numsol ". 
				   "  FROM cxp_dt_solicitudes ".
				   " WHERE codemp='{$this->daoSolicitud->codemp}' ".
				   "   AND numsol<>'{$this->daoSolicitud->numsol}' ".
				   "   AND numrecdoc||codtipdoc||ced_bene||cod_pro IN (SELECT numrecdoc||codtipdoc||ced_bene||cod_pro ".
				   "													 FROM cxp_dt_solicitudes ".
				   "												    WHERE codemp='{$this->daoSolicitud->codemp}' ".
				   "													  AND numsol='{$this->daoSolicitud->numsol}') ";		
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataSet->EOF)
			{
				$validarRecepcion = true;
			}
		}
		return $validarRecepcion;
	}	
}
?>