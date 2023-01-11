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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracioncxprd.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionCXPRD implements IIntegracionCXPRD
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos;
	private $servicioComprobante;
	private $daoRecepcion;
	private $daoHistoricoRecepcion;
		
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$this->codemp = $_SESSION['la_empresa']['codemp'];
	}
	
	public function buscarRecepcionIntegrar($numrecdoc, $fecemi, $fecapr, $tipo, $codigo, $estatus, $proceso = '')
	{
		$criterio="";
		if(!empty($numrecdoc))
		{
			$criterio .= " AND numrecdoc like '%".$numrecdoc."%'";
		}
		if(!empty($fecemi))
		{
			$criterio .= " AND fecemidoc = '".$fecemi."'";
		}
		if(!empty($fecapr))
		{
			$criterio .= " AND fecaprord = '".$fecapr."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipproben = '".$tipo."' ";
			if(!empty($codigo))
			{
				switch($tipo)
				{
					case "P": // es un proveedor
						$criterio .= " AND cod_pro = '".$as_codigo."' ";
						break;
	
					case "B": // es un beneficiario
						$criterio .= " AND ced_bene = '".$as_codigo."' ";
						break;
				}
			}
		}
		switch($proceso)
		{
			case 'REVERSAR':
				$criterio .= " AND NOT numrecdoc IN (SELECT numrecdoc ".
							 " 						   FROM cxp_dt_solicitudes ". 
							 " 						  WHERE cxp_rd.codemp = cxp_dt_solicitudes.codemp ". 
							 " 						    AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ". 
							 " 						    AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ". 
							 " 						    AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ". 
							 " 						    AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro)";
				break;

			case 'ANULAR':
				$criterio .= "   AND ((NOT numrecdoc IN (SELECT numrecdoc ". 
							 " 						       FROM cxp_dt_solicitudes ". 
							 " 						      WHERE cxp_rd.codemp = cxp_dt_solicitudes.codemp ". 
							 " 						        AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ". 
							 " 						        AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ". 
							 " 						        AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ". 
							 " 						        AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro) ) ".
							 "		   OR (numrecdoc IN (SELECT numrecdoc ". 
							 " 						       FROM cxp_dt_solicitudes, cxp_solicitudes ". 
							 " 						      WHERE cxp_solicitudes.estprosol = 'A' ". 
							 " 						        AND cxp_dt_solicitudes.codemp = cxp_solicitudes.codemp ". 
							 " 						        AND cxp_dt_solicitudes.numsol = cxp_solicitudes.numsol ". 
							 " 						        AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ". 
							 " 						        AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc  ".
							 " 						        AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ". 
							 " 						        AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ". 
							 " 						        AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro) ) )";
				break;
		}
		$filtrofrom ='';
		if($this->estfilpremod=='1')
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
			             " AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			
			
			$filtrofrom = ", cxp_rd_spg";			
		}		
		$conCat = $this->conexionBaseDatos->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$cadenaSql="SELECT numrecdoc, codtipdoc, ced_bene, cod_pro, dencondoc, fecregdoc, fechaanula, tipproben, ".  
				   "		(CASE tipproben WHEN 'P' THEN (SELECT nompro FROM rpc_proveedor ".
				   "                                        WHERE rpc_proveedor.codemp = cxp_rd.codemp ". 
				   "                                          AND rpc_proveedor.cod_pro = cxp_rd.cod_pro) ". 
				   "                                 ELSE (SELECT {$conCat} FROM rpc_beneficiario ". 
				   "                                        WHERE rpc_beneficiario.codemp = cxp_rd.codemp ". 
				   "                                          AND rpc_beneficiario.ced_bene = cxp_rd.ced_bene) ".
				   "                                 END) AS nombre ". 
				   " FROM cxp_rd  ".
				   "WHERE codemp = '{$this->codemp}' ". 
				   "AND estprodoc = '{$estatus}' AND estaprord = 1 ".$criterio;
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
				
		return $dataSet;
	}
	
	public function buscarDetallePresupuesto($numrecdoc, $codtipdoc, $ced_bene, $cod_pro)
	{
		$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
		$cadenaSQL = "SELECT doc.estpre, rd.codestpro, rd.estcla, rd.spg_cuenta, rd.monto, spg_cuentas.denominacion ".
					 "  FROM cxp_rd_spg rd  ".
					 " INNER JOIN cxp_documento doc ".
					 "    ON rd.codemp=doc.codemp ".
					 "   AND rd.codtipdoc=doc.codtipdoc ". 
					 " INNER JOIN spg_cuentas ". 
					 "    ON cxp_rd_spg.codemp = spg_cuentas.codemp ". 
					 "   AND cxp_rd_spg.codestpro =  ".$codestpro." ". 
					 "   AND cxp_rd_spg.estcla = spg_cuentas.estcla ". 
					 "   AND cxp_rd_spg.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE rd.codemp='{$this->codemp}'  ".
					 "   AND rd.numrecdoc='{$numrecdoc}'  ".
					 "   AND rd.codtipdoc='{$codtipdoc}'  ".
					 "   AND rd.ced_bene='{$ced_bene}' ".
					 "   AND rd.cod_pro='{$cod_pro}'";
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function obtenerDetalleComprobanteRECSPG($numrecdoc, $codtipdoc, $ced_bene, $cod_pro, $fecregdoc) {
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numrecdoc, $codtipdoc, $ced_bene, $cod_pro);
		while (!$dataCuentas->EOF) {
			$disponible = 1;
			$codestpro = $dataCuentas->fields['codestpro'];
			$codestpro1 = substr($codestpro,0,25);
			$codestpro2 = substr($codestpro,25,25);
			$codestpro3 = substr($codestpro,50,25);
			$codestpro4 = substr($codestpro,75,25);
			$codestpro5 = substr($codestpro,100,25);
			//VERIFICANDO DISPONIBILIDAD 
			if ($dataCuentas->fields['estpre']==2){
				$valiNivel = 0;
				if($_SESSION["la_empresa"]["estvaldis"]==1){
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
					if(round($dataCuentas->fields['monto'],2) > round($disponibilidad,2)) {
						$disponible = 0;
					}
					
					if ($disponible == 0) {
						$_SESSION['fechacomprobante'] = $fecregdoc;
						$this->servicioComprobante->saldoSelect();
						$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - 
					                    	( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + 
					                    	$this->servicioComprobante->precomprometido));
						if(round($dataCuentas->fields['monto'],2) > round($disponibilidad,2)) {
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
	
	public function obtenerDetalleComprobanteRECSCG($numrecdoc, $codtipdoc, $ced_bene, $cod_pro)
	{
		$cadenaSql="SELECT sc_cuenta AS cuenta, scg_cuentas.denominacion, (CASE WHEN debhab = 'D' THEN monto ELSE 0 END) AS debe, ". 
		           "        (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber ". 
		           "  FROM cxp_rd_scg ".
				   " INNER JOIN scg_cuentas ". 
				   "    ON cxp_rd_scg.codemp = scg_cuentas.codemp ". 
				   "   AND cxp_rd_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
				   " WHERE codemp='{$this->codemp}' ".
		           "   AND numrecdoc='{$numrecdoc}' ". 
		           "   AND codtipdoc='{$codtipdoc}' ". 
		           "   AND ced_bene='{$ced_bene}' ".
		           "   AND cod_pro='{$cod_pro}'";
		return $this->conexionBaseDatos->Execute ( $cadenaSql );
	}
	
	public function obtenerDetalleRecepcionSPG($arrCabecera)
	{
		$arrDetalleSPG = array();
		
		$cadenaSql="SELECT doc.estpre, rd.procede_doc, rd.numdoccom, rd.codestpro, rd.estcla, rd.spg_cuenta, rd.codfuefin, rd.monto ". 
				   "  FROM cxp_rd_spg rd ".
				   " INNER JOIN cxp_documento doc ".
				   "    ON rd.codemp=doc.codemp ".
				   "   AND rd.codtipdoc=doc.codtipdoc ". 
				   " WHERE rd.codemp='{$this->daoRecepcion->codemp}' ". 
				   "   AND rd.numrecdoc='{$this->daoRecepcion->numrecdoc}' ". 
				   "   AND rd.codtipdoc='{$this->daoRecepcion->codtipdoc}' ". 
				   "   AND rd.ced_bene='{$this->daoRecepcion->ced_bene}' ".
				   "   AND rd.cod_pro='{$this->daoRecepcion->cod_pro}'";
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
	
	public function obtenerDetalleRecepcionSCG($arrCabecera)
	{
		$arrDetalleSCG = array();
		$cadenaSql="SELECT doc.estcon, rd.procede_doc, rd.numdoccom, rd.debhab, rd.sc_cuenta, rd.monto ".   
				   "  FROM cxp_rd_scg rd ".
				   " INNER JOIN cxp_documento doc ".
				   "    ON rd.codemp=doc.codemp ".
		           "   AND rd.codtipdoc=doc.codtipdoc ".   
				   " WHERE rd.codemp='{$this->daoRecepcion->codemp}' ".
				   "   AND rd.numrecdoc='{$this->daoRecepcion->numrecdoc}' ".
				   "   AND rd.codtipdoc='{$this->daoRecepcion->codtipdoc}' ".
				   "   AND rd.ced_bene='{$this->daoRecepcion->ced_bene}' ".
				   "   AND rd.cod_pro='{$this->daoRecepcion->cod_pro}'";
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
		return $arrDetalleSPG;
	}
	
	public function insertarHistoricoRecepcion($estatus,$fecha)
	{
		$this->daoHistoricoRecepcion = FabricaDao::CrearDAO('N','cxp_historico_rd');
		$this->daoHistoricoRecepcion->codemp = $this->daoRecepcion->codemp;
		$this->daoHistoricoRecepcion->numrecdoc = $this->daoRecepcion->numrecdoc;
		$this->daoHistoricoRecepcion->codtipdoc  = $this->daoRecepcion->codtipdoc;
		$this->daoHistoricoRecepcion->ced_bene  = $this->daoRecepcion->ced_bene;
		$this->daoHistoricoRecepcion->cod_pro  = $this->daoRecepcion->cod_pro;
		$this->daoHistoricoRecepcion->fecha  = $fecha;
		$this->daoHistoricoRecepcion->estprodoc = $estatus;
		if ($this->daoHistoricoRecepcion->incluir(false,'',false,0,true))
		{
			$this->valido = true;
		}
		else
		{
			$this->valido = false;
			$this->mensaje .= $this->daoHistoricoRecepcion->ErrorMsg;
		}
		
		return $this->valido;
	}
	
	public function eliminarHistoricoRecepcion($estatus)
	{
		$this->daoHistoricoRecepcion = FabricaDao::CrearDAO('N','cxp_historico_rd');
		$this->daoHistoricoRecepcion->codemp = $this->daoRecepcion->codemp;
		$this->daoHistoricoRecepcion->numrecdoc = $this->daoRecepcion->numrecdoc;
		$this->daoHistoricoRecepcion->codtipdoc  = $this->daoRecepcion->codtipdoc;
		$this->daoHistoricoRecepcion->ced_bene  = $this->daoRecepcion->ced_bene;
		$this->daoHistoricoRecepcion->cod_pro  = $this->daoRecepcion->cod_pro;
		$this->daoHistoricoRecepcion->estprodoc = $estatus;
		if ($this->daoHistoricoRecepcion->eliminar())
		{
			$this->valido = true;
		}
		else
		{
			$this->valido = false;
			$this->mensaje .= $this->daoHistoricoRecepcion->ErrorMsg;
		}
		
		return $this->valido;
	}
	
	public function contabilizarREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA RECEPCION A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND numrecdoc='".$numrecdoc."' ". 
				  "AND codtipdoc = '{$codtipdoc}' ".
				  "AND cod_pro = '{$cod_pro}' ". 
				  "AND ced_bene = '{$ced_bene}' ".
				  "AND estprodoc = 'E'  ".
		          "AND estaprord = 1";
		$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
		// VERIFICO QUE LA RECEPCION EXISTA
		if($this->daoRecepcion->numrecdoc=='') {
			$this->mensaje .= 'ERROR -> No existe la recepcion de documentos, en estatus EMITIDA y APROBADA';
			$this->valido = false;			
		}
				
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoRecepcion->codemp;
			$arrCabecera['procede'] = 'CXPRCD';
			$arrCabecera['comprobante'] = fillComprobante($this->daoRecepcion->numrecdoc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoRecepcion->fecregdoc;
			$arrCabecera['descripcion'] = $this->daoRecepcion->dencondoc;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoRecepcion->tipproben;
			$arrCabecera['cod_pro'] = $this->daoRecepcion->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoRecepcion->ced_bene;
			$arrCabecera['total'] = number_format($this->daoRecepcion->montot,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoRecepcion->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$arrDetalleSPG=$this->obtenerDetalleRecepcionSPG($arrCabecera);
			$arrDetalleSCG=$this->obtenerDetalleRecepcionSCG($arrCabecera);
		}
		
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrCabecera,$arrDetalleSPG,$arrDetalleSCG,null,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoRecepcion->estprodoc = 'C';
				$this->daoRecepcion->fechaconta = $this->daoRecepcion->fecregdoc;
				$this->daoRecepcion->fechaanula = '1900-01-01';
				$this->daoRecepcion->conanurd = '';
				$resultado =  $this->daoRecepcion->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La recepcion de documentos  {$this->daoRecepcion->numrecdoc} que intenta procesar no existe"; 
					}
				}
				else
				{
					$this->insertarHistoricoRecepcion('C',$this->daoRecepcion->fecregdoc);
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
		unset($this->daoRecepcion);
		unset($this->daoHistoricoRecepcion);
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoContabilizarREC($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->recepciones);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_contabiliza_cxprd.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Se contabilizo la Recepcion de documentos {$arrJson->recepciones[$j]->numrecdoc}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarREC($arrJson->recepciones[$j]->numrecdoc, $arrJson->recepciones[$j]->codtipdoc, 
			                           $arrJson->recepciones[$j]->cod_pro, $arrJson->recepciones[$j]->ced_bene, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "Se contabilizo exitosamente la recepcion de documentos";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "No se pudo contabilizar la recepcion de documentos, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function revContabilizarREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA RECEPCION A REVERSAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND numrecdoc='".$numrecdoc."' ". 
				  "AND codtipdoc = '{$codtipdoc}' ". 
				  "AND cod_pro = '{$cod_pro}' ". 
				  "AND ced_bene = '{$ced_bene}' ". 
				  "AND estprodoc = 'C' ". 
				  "AND estaprord = 1";
		$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
		// VERIFICO QUE LA RECEPCION EXISTA
		if($this->daoRecepcion->numrecdoc=='')
		{
			$this->mensaje .= 'ERROR -> No existe la recepcion de documentos, en estatus CONTABILIZADA';
			$this->valido = false;			
		}
				
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoRecepcion->codemp;
			$arrCabecera['procede'] = 'CXPRCD';
			$arrCabecera['comprobante'] = fillComprobante($this->daoRecepcion->numrecdoc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoRecepcion->fecregdoc;
			$arrCabecera['descripcion'] = $this->daoRecepcion->dencondoc;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoRecepcion->tipproben;
			$arrCabecera['cod_pro'] = $this->daoRecepcion->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoRecepcion->ced_bene;
			$arrCabecera['total'] = number_format($this->daoRecepcion->montot,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoRecepcion->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrCabecera,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoRecepcion->estprodoc = 'E';
				$this->daoRecepcion->fechaconta = '1900-01-01';
				$this->daoRecepcion->fechaanula = '1900-01-01';
				$this->daoRecepcion->conanurd = '';
				$resultado =  $this->daoRecepcion->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La recepcion de documentos  {$this->daoRecepcion->numrecdoc} que intenta procesar no existe"; 
					}
				}
				else
				{
					$this->eliminarHistoricoRecepcion('C');
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
		unset($this->daoRecepcion);
		unset($this->daoHistoricoRecepcion);
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoRevContabilizarREC($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->recepciones);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_contabiliza_cxprd.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Se reverso la contabilizacion de la Recepcion de documentos {$arrJson->recepciones[$j]->numrecdoc}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizarREC($arrJson->recepciones[$j]->numrecdoc, $arrJson->recepciones[$j]->codtipdoc, 
			                              $arrJson->recepciones[$j]->cod_pro, $arrJson->recepciones[$j]->ced_bene, $arrEvento))
		    {
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "Se revereso exitosamente la contabilizaci&#243;n la recepci&#243;n de documentos";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "No se pudo reversar la contabilizaci&#243;n de la recepci&#243;n de documentos, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function anularREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $fecha, $conanurd, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA RECEPCION A ANULAR
		$criterio="    codemp = '".$this->codemp."' ". 
		          "AND numrecdoc='".$numrecdoc."' ". 
		          "AND codtipdoc = '{$codtipdoc}' ".
		          "AND cod_pro = '{$cod_pro}' ". 
		          "AND ced_bene = '{$ced_bene}' ".
		          "AND estprodoc = 'C' ".
		          "AND estaprord = 1";
		$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
		// VERIFICO QUE LA RECEPCION EXISTA
		if($this->daoRecepcion->numrecdoc=='')
		{
			$this->mensaje .= "ERROR -> No existe la recepcion de documentos, en estatus CONTABILIZADA";
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE ANULACIÓN  SEA MAYOR O IGUAL A LA FECHA DE LA RECEPCION
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoRecepcion->fecregdoc,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulaci&#243;n '.$fecha.' es menor que la fecha de Emision '.$this->daoRecepcion->fecregdoc.' de la Recepcion Nº '.$numrecdoc;
			$this->valido = false;			
		}
		
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoRecepcion->codemp;
			$arrCabecera['procede'] = 'CXPRCD';
			$arrCabecera['comprobante'] = fillComprobante($this->daoRecepcion->numrecdoc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoRecepcion->fecregdoc;
			$arrCabecera['descripcion'] = $this->daoRecepcion->dencondoc;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoRecepcion->tipproben;
			$arrCabecera['cod_pro'] = $this->daoRecepcion->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoRecepcion->ced_bene;
			$arrCabecera['total'] = number_format($this->daoRecepcion->montot,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoRecepcion->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrCabecera,$fecha,'CXPARD',$conanurd,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoRecepcion->estprodoc  = 'A';
				$this->daoRecepcion->fechaanula = $fecha;
				$this->daoRecepcion->conanurd   = $conanurd;
				$resultado =  $this->daoRecepcion->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La recepcion de documentos  {$this->daoRecepcion->numrecdoc} que intenta procesar no existe"; 
					}
				}
				else
				{
					if ($this->valido)
					{
						$this->insertarHistoricoRecepcion('A',$fecha);
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
	
	public function procesoAnularREC($arrJson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->recepciones);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$fechaAnula = $arrJson->fechaAnula;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_anula_cxprd.html';
		for($j=0;$j<=$nSol-1;$j++) {
			$arrEvento['desevetra'] = "Se anulo la Recepcion de documentos {$arrJson->recepciones[$j]->numrecdoc}, asociado a la empresa {$this->codemp}";
			if ($this->anularREC($arrJson->recepciones[$j]->numrecdoc, $arrJson->recepciones[$j]->codtipdoc, 
			                     $arrJson->recepciones[$j]->cod_pro, $arrJson->recepciones[$j]->ced_bene, 
			                     $fechaAnula, $arrJson->recepciones[$j]->conanurd, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "Se anulo la recepci&#243;n de documentos";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "No se pudo anular la recepci&#243;n de documentos, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function revAnularREC($numrecdoc, $codtipdoc, $cod_pro, $ced_bene, $arrEvento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA RECEPCION ANULADA A REVERSAR
		$criterio="    codemp = '".$this->codemp."' ". 
				  "AND numrecdoc='".$numrecdoc."' ".  
				  "AND codtipdoc = '{$codtipdoc}' ". 
				  "AND cod_pro = '{$cod_pro}' ". 
				  "AND ced_bene = '{$ced_bene}' ". 
				  "AND estprodoc = 'A' ". 
				  "AND estaprord = 1";
		$this->daoRecepcion = FabricaDao::CrearDAO('C','cxp_rd','',$criterio);
		// VERIFICO QUE LA RECEPCION EXISTA
		if($this->daoRecepcion->numrecdoc=='')
		{
			$this->mensaje .= 'ERROR -> No existe la recepcion de documentos, en estatus ANULADA';
			$this->valido = false;			
		}
				
		if ($this->valido)
		{
			$arrCabecera['codemp'] = $this->daoRecepcion->codemp;
			$arrCabecera['procede'] = 'CXPARD';
			$arrCabecera['comprobante'] = fillComprobante($this->daoRecepcion->numrecdoc);
			$arrCabecera['codban'] = '---';
			$arrCabecera['ctaban'] = '-------------------------';
			$arrCabecera['fecha'] = $this->daoRecepcion->fechaanula;
			$arrCabecera['descripcion'] = $this->daoRecepcion->dencondoc;
			$arrCabecera['tipo_comp'] = 1;
			$arrCabecera['tipo_destino'] = $this->daoRecepcion->tipproben;
			$arrCabecera['cod_pro'] = $this->daoRecepcion->cod_pro;
			$arrCabecera['ced_bene'] = $this->daoRecepcion->ced_bene;
			$arrCabecera['total'] = number_format($this->daoRecepcion->montot,2,'.','');
			$arrCabecera['numpolcon'] = 0;
			$arrCabecera['esttrfcmp'] = 0;
			$arrCabecera['estrenfon'] = 0;
			$arrCabecera['codfuefin'] = $this->daoRecepcion->codfuefin;
			$arrCabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrCabecera,$arrEvento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoRecepcion->estprodoc = 'C';
				$this->daoRecepcion->fechaanula = '1900-01-01';
				$this->daoRecepcion->conanurd = '';
				$resultado =  $this->daoRecepcion->modificar();
				if($resultado == 0 || $resultado == 2)
				{
					$this->valido = false;
					if($resultado == 0)
					{
						$this->mensaje .= $this->daoRecepcion->ErrorMsg;
					}
					else
					{
						$this->mensaje .= "La recepcion de documentos  {$this->daoRecepcion->numrecdoc} que intenta procesar no existe"; 
					}
				}
				else
				{
					$this->eliminarHistoricoRecepcion('A');
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
		unset($this->daoRecepcion);
		unset($this->daoHistoricoRecepcion);
		unset($servicioEvento);
		return $this->valido;
	}
	
	public function procesoRevAnularREC($arrJson) {
		$arrRespuesta= array();
		$nSol = count((array)$arrJson->recepciones);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrEvento['codemp']    = $this->codemp;
		$arrEvento['codusu']    = $_SESSION['la_logusr'];
		$arrEvento['codsis']    = 'MIS';
		$arrEvento['evento']    = 'PROCESAR';
		$arrEvento['nomfisico'] = 'sigesp_vis_mis_rev_anula_cxprd.html';
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrEvento['desevetra'] = "Se reverso la anulacion de la Recepcion de documentos {$arrJson->recepciones[$j]->numrecdoc}, asociado a la empresa {$this->codemp}";
			if ($this->revAnularREC($arrJson->recepciones[$j]->numrecdoc, $arrJson->recepciones[$j]->codtipdoc, 
			                        $arrJson->recepciones[$j]->cod_pro, $arrJson->recepciones[$j]->ced_bene, $arrEvento))
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "Se revereso exitosamente la anulaci&#243;n de la recepci&#243;n de documentos";
			}
			else
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $arrJson->recepciones[$j]->numrecdoc;
				$arrRespuesta[$h]['mensaje'] = "No se pudo reversar la anulaci&#243;n de la recepci&#243;n de documentos, {$this->mensaje} ";
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