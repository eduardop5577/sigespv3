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
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsoc.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_integracionsep.php");
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php');

class servicioIntegracionSOC implements IIntegracionSOC 
{
	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos;
	public  $servicioComprobante;
		
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$this->estfilpremod = $_SESSION['la_empresa']['estfilpremod'];	
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->confiva = $_SESSION['la_empresa']['confiva'];	
	}
	
	public function buscarContabilizar($estcondat,$numordcom,$cod_pro,$fecaprord,$fecordcom)
	{
		$criterio="";
		if ($estcondat=="")
		{
			$estcondat="-";
		}
		else
		{
			if($estcondat!="-")
			{
				$criterio .= " AND estcondat ='".$estcondat."'";
			}
		}
		if($numordcom!="")
		{
			$criterio .= " AND numordcom like '%".$numordcom."%'";
		}
		if($cod_pro!="")
		{
			$criterio .= " AND soc_ordencompra.cod_pro = '".$cod_pro."'";
		}
		if($fecaprord!="")
		{
			$fecaprord=convertirFechaBd($fecaprord);
			$criterio .= " AND fecaprord ='".$fecaprord."'";
		}
		if($fecordcom!="")
		{
			$fecordcom=convertirFechaBd($fecordcom);
			$criterio .= " AND fecordcom ='".$fecordcom."'";
		}	
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SOC' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		
		
		$cadenasql=" SELECT soc_ordencompra.numordcom, soc_ordencompra.estcondat, soc_ordencompra.fecordcom, soc_ordencompra.obscom, ".
				   " 		soc_ordencompra.fechaconta, soc_ordencompra.conanusoc,soc_ordencompra.fechaanula,soc_ordencompra.fecaprord, ".
				   "        rpc_proveedor.cod_pro, rpc_proveedor.nompro ".
                   "  FROM soc_ordencompra, rpc_proveedor ".
				   " WHERE soc_ordencompra.codemp='".$this->codemp."' ".
				   "   AND soc_ordencompra.estcom='1' ".
				   "   AND soc_ordencompra.estapro='1' ".
				   "   AND soc_ordencompra.numordcom<>'000000000000000' ".
				   $criterio.
				   "   AND NOT numordcom IN (SELECT numdoccom ".
				   "					   	    FROM cxp_rd_spg, cxp_rd ".
				   "						   WHERE soc_ordencompra.codemp =  cxp_rd_spg.codemp ".
				   "						     AND soc_ordencompra.numordcom =  cxp_rd_spg.numdoccom ".
				   "  						     AND cxp_rd_spg.procede_doc=(CASE soc_ordencompra.estcondat ".
				   "														 WHEN 'S' THEN 'SOCCOS' ".
				   "																  ELSE 'SOCCOC' END) ".
				   "  						     AND cxp_rd.estprodoc <> 'A' ".
				   "						     AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						     AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						     AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						     AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						     AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					       GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND soc_ordencompra.codemp=rpc_proveedor.codemp ".
				   "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro ".
				   " ORDER BY numordcom, estcondat ";				
		$dataSOC = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSOC;
	}
	
	public function buscarRevContabilizacion($estcondat,$numordcom,$cod_pro,$fecaprord,$fecordcom,$fechaconta)
	{
		$criterio="";
		if ($estcondat=="")
		{
			$estcondat="-";
		}
		else
		{
			if($estcondat!="-")
			{
				$criterio .= " AND estcondat ='".$estcondat."'";
			}			
		}		
		if($numordcom!="")
		{
			$criterio .= " AND numordcom like '%".$numordcom."%'";
		}
		if($cod_pro!="")
		{
			$criterio .= " AND soc_ordencompra.cod_pro = '".$cod_pro."'";
		}
		if($fecaprord!="")
		{
			$fecaprord=convertirFechaBd($fecaprord);
			$criterio .= " AND fecaprord ='".$fecaprord."'";
		}
		if($fecordcom!="")
		{
			$fecordcom=convertirFechaBd($fecordcom);
			$criterio .= " AND fecordcom ='".$fecordcom."'";
		}
		if($fechaconta!="")
		{
			$fechaconta=convertirFechaBd($fechaconta);
			$criterio .= " AND fechaconta ='".$fechaconta."'";
		}
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SOC' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		
		$cadenasql="SELECT soc_ordencompra.numordcom, soc_ordencompra.estcondat, soc_ordencompra.fecordcom, ".
				   "       soc_ordencompra.obscom, soc_ordencompra.conanusoc,soc_ordencompra.fechaconta, ".
				   "       soc_ordencompra.fechaanula,soc_ordencompra.fecaprord, rpc_proveedor.cod_pro, rpc_proveedor.nompro ".
                   "  FROM soc_ordencompra, rpc_proveedor ".
				   " WHERE soc_ordencompra.codemp='".$this->codemp."' ".
				   "   AND soc_ordencompra.estcom='2' ".
				   "   AND soc_ordencompra.estapro='1' ".
				   "   AND soc_ordencompra.numordcom<>'000000000000000' ".
				   $criterio.
				   "   AND NOT numordcom IN (SELECT numdoccom ".
				   "					   	    FROM cxp_rd_spg, cxp_rd ".
				   "						   WHERE soc_ordencompra.codemp =  cxp_rd_spg.codemp ".
				   "						     AND soc_ordencompra.numordcom =  cxp_rd_spg.numdoccom ".
				   "  						     AND cxp_rd_spg.procede_doc=(CASE soc_ordencompra.estcondat ".
				   "														 WHEN 'S' THEN 'SOCCOS' ".
				   "																  ELSE 'SOCCOC' END) ".
				   "						     AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						     AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						     AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						     AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						     AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					       GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND soc_ordencompra.codemp=rpc_proveedor.codemp ".
				   "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro ".
				   " ORDER BY numordcom, estcondat ";				
		$dataSOC = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSOC;
	}

	public function buscarAnular($estcondat,$numordcom,$cod_pro,$fecaprord,$fecordcom)
	{
		$criterio="";
		if ($estcondat=="")
		{
			$estcondat="-";
		}
		else
		{
			if($estcondat!="-")
			{
				$criterio .= " AND estcondat ='".$estcondat."'";
			}			
		}
		if($numordcom!="")
		{
			$criterio .= " AND numordcom like '%".$numordcom."%'";
		}
		if($cod_pro!="")
		{
			$criterio .= " AND soc_ordencompra.cod_pro = '".$cod_pro."'";
		}
		if($fecaprord!="")
		{
			$fecaprord=convertirFechaBd($fecaprord);
			$criterio .= " AND fecaprord ='".$fecaprord."'";
		}
		if($fecordcom!="")
		{
			$fecordcom=convertirFechaBd($fecordcom);
			$criterio .= " AND fecordcom ='".$fecordcom."'";
		}
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SOC' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		
		$cadenasql="SELECT soc_ordencompra.numordcom, soc_ordencompra.estcondat, soc_ordencompra.fecordcom, ".
				   " 	   soc_ordencompra.obscom, soc_ordencompra.conanusoc,soc_ordencompra.fechaconta, ".
				   "       soc_ordencompra.fechaanula, soc_ordencompra.conanusoc, ".
				   "       soc_ordencompra.fecaprord, rpc_proveedor.cod_pro, rpc_proveedor.nompro ".
                   "  FROM soc_ordencompra, rpc_proveedor ".
				   " WHERE soc_ordencompra.codemp='".$this->codemp."' ".
				   "   AND soc_ordencompra.estcom='2' ".
				   "   AND soc_ordencompra.estapro='1' ".
				   "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro ".
				   "   AND soc_ordencompra.numordcom<>'000000000000000' ".
				   $criterio.
				   "   AND NOT numordcom IN (SELECT numdoccom ".
				   "					   	    FROM cxp_rd_spg, cxp_rd ".
				   "						   WHERE soc_ordencompra.codemp =  cxp_rd_spg.codemp ".
				   "						     AND soc_ordencompra.numordcom =  cxp_rd_spg.numdoccom ".
				   "  						     AND cxp_rd_spg.procede_doc=(CASE soc_ordencompra.estcondat ".
				   "														 WHEN 'S' THEN 'SOCCOS' ".
				   "																  ELSE 'SOCCOC' END) ".
				   "  						     AND cxp_rd.estprodoc <> 'A' ".
				   "						     AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						     AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						     AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						     AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						     AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					       GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND soc_ordencompra.codemp=rpc_proveedor.codemp ".
				   "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro ".
				   " ORDER BY numordcom, estcondat ";								
		$dataSOC = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSOC;
	}

	public function buscarRevAnulacion($estcondat,$numordcom,$cod_pro,$fecaprord,$fecordcom,$fechaanula)
	{
		$criterio="";
		if ($estcondat=="")
		{
			$estcondat="-";
		}
		else
		{
			if($estcondat!="-")
			{
				$criterio .= " AND estcondat ='".$estcondat."'";
			}		
		}
		if($numordcom!="")
		{
			$criterio .= " AND numordcom like '%".$numordcom."%'";
		}
		if($cod_pro!="")
		{
			$criterio .= " AND soc_ordencompra.cod_pro = '".$cod_pro."'";
		}
		if($fecaprord!="")
		{
			$fecaprord=convertirFechaBd($fecaprord);
			$criterio .= " AND fecaprord ='".$fecaprord."'";
		}
		if($fecordcom!="")
		{
			$fecordcom=convertirFechaBd($fecordcom);
			$criterio .= " AND fecordcom ='".$fecordcom."'";
		}
		if($fechaanula!="")
		{
			$fecordcom=convertirFechaBd($fechaanula);
			$criterio .= " AND fechaanula ='".$fechaanula."'";
		}
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('soc_ordencompra.codestpro1','soc_ordencompra.codestpro2','soc_ordencompra.codestpro3','soc_ordencompra.codestpro4','soc_ordencompra.codestpro5','soc_ordencompra.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND soc_ordencompra.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SOC' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		$cadenasql="SELECT soc_ordencompra.numordcom, soc_ordencompra.estcondat, soc_ordencompra.fecordcom, soc_ordencompra.obscom, ".
				   " 	   soc_ordencompra.fechaconta, soc_ordencompra.conanusoc,soc_ordencompra.fechaanula,soc_ordencompra.conanusoc, ".
				   " 	   soc_ordencompra.fecaprord, rpc_proveedor.cod_pro, rpc_proveedor.nompro ".
                   "  FROM soc_ordencompra,rpc_proveedor ".
				   " WHERE soc_ordencompra.codemp='".$this->codemp."' ".
				   "   AND soc_ordencompra.estcom='3' ".
				   "   AND soc_ordencompra.estapro='1' ".
				   "   AND soc_ordencompra.numordcom<>'000000000000000' ".
				   $criterio.
				   "   AND NOT numordcom IN (SELECT numdoccom ".
				   "					   	    FROM cxp_rd_spg, cxp_rd ".
				   "						   WHERE soc_ordencompra.codemp =  cxp_rd_spg.codemp ".
				   "						     AND soc_ordencompra.numordcom =  cxp_rd_spg.numdoccom ".
				   "  						     AND cxp_rd_spg.procede_doc=(CASE soc_ordencompra.estcondat ".
				   "														 WHEN 'S' THEN 'SOCCOS' ".
				   "																  ELSE 'SOCCOC' END) ".
				   "  						     AND cxp_rd.estprodoc <> 'A' ".
				   "						     AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						     AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						     AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						     AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						     AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					       GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND soc_ordencompra.fechaanula<>'1900-01-01' ".
				   "   AND soc_ordencompra.codemp=rpc_proveedor.codemp ".
				   "   AND soc_ordencompra.cod_pro=rpc_proveedor.cod_pro ".
				   " ORDER BY numordcom, estcondat ";				
		$dataSOC = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSOC;	
	}

	public function validarMontoSOC()
	{
        $montoacumulado=0;
		$cadenasql="SELECT SUM(CASE WHEN monto IS NULL THEN 0 ELSE monto END ) As monto ".
                   "  FROM soc_cuentagasto ".
                   " WHERE codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND estcondat='".$this->daoOrdenCompra->estcondat."'";
        $data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
			   $montoacumulado = number_format($data->fields['monto'],2,'.','');
			}
		}
		unset($data);
		return $montoacumulado;
	}	
	
	public function verificarReversoSep($numsol,$estcom)
	{
		$existe=false;
		$cadenasql="SELECT soc_enlace_sep.numsol ".
                   "  FROM soc_enlace_sep ".
				   " INNER JOIN soc_ordencompra ".
                   "    ON soc_enlace_sep.codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND soc_enlace_sep.numsol ='".$numsol."'".
				   "   AND soc_ordencompra.estcom ='".$estcom."'".
				   "   AND soc_ordencompra.codemp=soc_enlace_sep.codemp".
				   "   AND soc_ordencompra.numordcom=soc_enlace_sep.numordcom".
				   "   AND soc_ordencompra.estcondat=soc_enlace_sep.estcondat".
				   "   AND soc_ordencompra.numordcom NOT IN (SELECT soc_ordencompra.numordcom".
				   "										    FROM soc_ordencompra".
				   "										   WHERE soc_ordencompra.codemp='".$this->daoOrdenCompra->codemp."'".
				   "                                            AND soc_ordencompra.numordcom='".$this->daoOrdenCompra->numordcom."'".
				   "                                            AND soc_ordencompra.estcondat='".$this->daoOrdenCompra->estcondat."')";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$existe=true;
			}    
		}
		unset($data);
		return $existe;
	}
	
    public function revSolicitudesAsociadas($arrevento)
	{
		$cadenasql="SELECT soc_enlace_sep.numsol, sep_solicitud.estsol  ".
                   "  FROM sep_solicitud ".
				   " INNER JOIN soc_enlace_sep ".
                   "    ON soc_enlace_sep.codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND soc_enlace_sep.numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND soc_enlace_sep.estcondat='".$this->daoOrdenCompra->estcondat."' ".
		           "   AND soc_enlace_sep.numsol<>'---------------'".
				   "   AND sep_solicitud.codemp=soc_enlace_sep.codemp ".
				   "   AND sep_solicitud.numsol=soc_enlace_sep.numsol ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$data->EOF)and($this->valido))
			{
				$numsol=$data->fields['numsol'];
				$estsol=$data->fields['estsol'];
				$existe=true;
				switch($estsol)
				{
					case 'R': // Registro
						$this->mensaje .= '  ->La Solicitud '.$numsol.' esta Registrada. No se puede Reversar.';			
						$this->valido=false;
					break;
						
					case 'E': // Emitida
						$this->mensaje .= '  ->La Solicitud '.$numsol.' esta Emitida. No se puede Reversar.';			
						$this->valido=false;
					break;
						
					case 'A': // Anulada
						$this->mensaje .= '  ->La Solicitud '.$numsol.' esta Anulada. No se puede Reversar.';			
						$this->valido=false;
					break;
						
					case 'P': // Procesada
						$existe=$this->verificarReversoSep($numsol,'2');
					break;
						
					case 'C': // Contabilizada
						$existe=$this->verificarReversoSep($numsol,'2');
					break;
				}
				if((!$existe)&&($this->valido))
				{
					$servicioIntegracionSEP = new ServicioIntegracionSEP();
					$this->valido = $servicioIntegracionSEP->revPrecompromiso($numsol,$this->daoOrdenCompra->fechaconta,$this->daoOrdenCompra->numordcom,$this->daoOrdenCompra->estcondat,$arrevento);
					$this->mensaje .= $servicioIntegracionSEP->mensaje;
					unset($servicioIntegracionSEP);
				}
				if($this->valido)
				{
					$criterio="codemp = '".$this->daoOrdenCompra->codemp."' AND numordcom='".$this->daoOrdenCompra->numordcom."' AND estcondat='".$this->daoOrdenCompra->estcondat."' AND (estordcom = '1' OR estordcom = '3') AND numsol='".$numsol."'";
					$this->daoSocEnlaceSep = FabricaDao::CrearDAO('C','soc_enlace_sep','',$criterio);
					if($this->daoSocEnlaceSep->codemp=='')
					{
						$this->valido=false;
							$this->mensaje .= 'Favor Verificar el enlace con la SEP.';
					}
					else
					{
						$this->daoSocEnlaceSep->estordcom=2;
						$this->valido = $this->daoSocEnlaceSep->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoSocEnlaceSep->ErrorMsg;
						}	
					}
					unset($this->daoSocEnlaceSep);					
				}
				$data->MoveNext();
			}		
		}
		unset($data);
		return $this->valido;		
	} 	
	
	public function buscarDetallePresupuestario($arrcabecera)
	{
		$arregloSPG = null;
		
		$cadenasql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin, SUM(monto) AS monto ".
				   "  FROM soc_cuentagasto ".
		           " WHERE codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND estcondat='".$this->daoOrdenCompra->estcondat."' ".
				   " GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin ".
	   			   " ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
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
				$i++;
				$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['procede_doc']= $arrcabecera['procede'];
				$arregloSPG[$i]['documento']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPG[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPG[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPG[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPG[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPG[$i]['estcla']=$data->fields['estcla'];
				$arregloSPG[$i]['codfuefin']=$data->fields['codfuefin'];
				$arregloSPG[$i]['spg_cuenta']=$data->fields['spg_cuenta'];
				$arregloSPG[$i]['monto']=$data->fields['monto'];
				$arregloSPG[$i]['mensaje']= 'O';
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSPG;
	}

	public function verificarEnRecepcion($procede)
	{
        $existe=false;
        $status='';
		$cadenasql="SELECT distinct cxp_rd.estprodoc ".
                   "  FROM cxp_rd_spg ".
                   " INNER JOIN cxp_rd  ".
                   "    ON cxp_rd_spg.codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND cxp_rd_spg.procede_doc='".$procede."' ".
				   "   AND cxp_rd_spg.numdoccom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND cxp_rd.codemp=cxp_rd_spg.codemp ".
				   "   AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc ".
				   "   AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc ".
				   "   AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro ".
				   "   AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
 			   $status=$data->fields['estprodoc'];
			   $existe=true;
			}
		}
		unset($data);
		$arrResultado['Estatus']=$status;
		$arrResultado['Existe']=$existe;
		return $arrResultado;
	} 

	public function eliminarRevSolicitudesAsociadas($arrevento,$estatus)
	{
		$cadenasql="SELECT soc_enlace_sep.numsol ".
                   "  FROM soc_enlace_sep ".
				   " INNER JOIN sep_solicitud ".
                   "    ON soc_enlace_sep.codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND soc_enlace_sep.numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND soc_enlace_sep.estcondat='".$this->daoOrdenCompra->estcondat."' ".
				   "   AND soc_enlace_sep.numsol<>'---------------'".
				   "   AND soc_enlace_sep.codemp = sep_solicitud.codemp ".
				   "   AND soc_enlace_sep.numsol = sep_solicitud.numsol ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$data->EOF)and($this->valido))
			{
				$numsol=$data->fields['numsol'];	
				$existe=$this->verificarReversoSep($numsol,'2');		
				if ((!$existe)&&($this->valido))
				{
					$servicioIntegracionSEP = new ServicioIntegracionSEP();
					$servicioIntegracionSEP->anulando = true;
					$this->valido = $servicioIntegracionSEP->eliminarRevPrecompromiso($numsol,$this->daoOrdenCompra->numordcom,$this->daoOrdenCompra->estcondat,$arrevento);
					$this->mensaje .= $servicioIntegracionSEP->mensaje;
					unset($servicioIntegracionSEP);
				}
				if($this->valido)
				{
					$criterio="codemp = '".$this->daoOrdenCompra->codemp."' AND numordcom='".$this->daoOrdenCompra->numordcom."' AND estcondat='".$this->daoOrdenCompra->estcondat."' AND estordcom = '2' AND numsol='".$numsol."'";
					$this->daoSocEnlaceSep = FabricaDao::CrearDAO('C','soc_enlace_sep','',$criterio);
					$this->daoSocEnlaceSep->estordcom=$estatus;
					$this->valido = $this->daoSocEnlaceSep->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoSocEnlaceSep->ErrorMsg;
					}	
					unset($this->daoSocEnlaceSep);					
				}	
				$data->MoveNext();
			}    
		}
		unset($data);		
		return $this->valido;
	} 

	public function procesarDetallesSolicitudes()
	{
		$tabla='sep_dt_articulos';
		if($this->daoOrdenCompra->estcondat=='S')
		{
			$tabla='sep_dt_servicio';
		}
		$cadenasql="UPDATE ".$tabla.
					"	SET estincite = 'NI', ".
					"       numdocdes='' ".
					" WHERE codemp='".$this->daoOrdenCompra->codemp."' ".
					"   AND estincite='OC'".
					"   AND numsol IN (SELECT numsol ". 
					" 					 FROM soc_enlace_sep ".
                    "                   WHERE codemp='".$this->daoOrdenCompra->codemp."' ".
				    "                     AND numordcom='".$this->daoOrdenCompra->numordcom."' ".
				    "   				  AND estcondat='".$this->daoOrdenCompra->estcondat."' ".
				    "   				  AND numsol<>'---------------')";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		return $this->valido;
	}

	public function reversarDetallesSolicitudes()
	{
		$tablasep='sep_dt_articulos';
		$tablasoc='soc_dt_bienes';
		$campo='codart';
		if($this->daoOrdenCompra->estcondat=='S')
		{
			$tablasep='sep_dt_servicio';
			$tablasoc='soc_dt_servicio';
			$campo='codser';
		}
		$cadenasql="SELECT estincite , numdocdes, (".$tablasep.".".$campo.") as codigo, (".$tablasep.".numsol) as numsol ". 
				   "  FROM  ".$tablasep.
				   " INNER JOIN ( ".$tablasoc.
                   "       INNER JOIN soc_enlace_sep ".
                   "                ON soc_enlace_sep.codemp='".$this->daoOrdenCompra->codemp."' ".
				   "               AND soc_enlace_sep.numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   			   AND soc_enlace_sep.estcondat='".$this->daoOrdenCompra->estcondat."' ".
				   "   			   AND soc_enlace_sep.numsol<>'---------------' ".
		           "               AND soc_enlace_sep.codemp = ".$tablasoc.".codemp ".
		           "               AND soc_enlace_sep.numordcom = ".$tablasoc.".numordcom ".
		           "               AND soc_enlace_sep.estcondat = ".$tablasoc.".estcondat) ".
                   "    ON ".$tablasoc.".codemp='".$this->daoOrdenCompra->codemp."' ".
				   "   AND ".$tablasoc.".numordcom='".$this->daoOrdenCompra->numordcom."' ".
				   "   AND ".$tablasoc.".estcondat='".$this->daoOrdenCompra->estcondat."' ".
				   "   AND ".$tablasep.".codemp = ".$tablasoc.".codemp ".
				   "   AND ".$tablasep.".numsol = ".$tablasoc.".numsol ".
				   "   AND ".$tablasep.".".$campo." = ".$tablasoc.".".$campo." ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while ((!$data->EOF)&&($this->valido))
			{
				if ((trim($data->fields['estincite'])!='NI')||((trim($data->fields['numdocdes'])!='')))
				{
					$this->mensaje .= '  -> No se puede reversar la anulacion el item '.$data->fields['codigo'].' de la solicitud '.$data->fields['numsol'].
									  ' se encuentra en otra orden de compra '.$data->fields['numdocdes'];
					$this->valido = false;					
				}
				else
				{
					$cadenasql="UPDATE ".$tablasep.
								"	SET estincite = 'OC', ".
								"       numdocdes='".$this->daoOrdenCompra->numordcom."' ".
								" WHERE codemp='".$this->daoOrdenCompra->codemp."' ".
								"   AND estincite='NI'".
								"   AND numsol='".$data->fields['codigo']."' ";
					$resultado = $this->conexionBaseDatos->Execute ( $cadenasql );
					if ($resultado===false)
					{
						$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
						$this->valido = false;
					}
					unset($resultado);
				}
				$data->MoveNext();
			}			
		}
		unset($data);
		return $this->valido;
	}
	
	public function Contabilizar($objson) 
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
		$fecha=convertirFechaBd($objson->fecha);
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrevento['desevetra'] = "Contabilizacion de la orden de compra {$objson->arrDetalle[$j]->numordcom}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSOC($objson->arrDetalle[$j]->numordcom,$objson->arrDetalle[$j]->estcondat,$fecha,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = 'Orden contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = "La Orden no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
    public function contabilizarSOC($numordcom,$estcondat,$fecha,$arrevento)
	{
 		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA ORDEN DE COMPRA A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numordcom='".$numordcom."' AND estcondat='".$estcondat."' AND estcom = '1' AND estapro = '1' ";
		$this->daoOrdenCompra = FabricaDao::CrearDAO('C','soc_ordencompra','',$criterio);
		$this->daoOrdenCompra->fechaconta = convertirFechaBd($fecha);
		$procede='SOCCOS';
		if($estcondat=='B')
		{
			$procede='SOCCOC';
		}
		// VERIFICO QUE LA ORDEN DE COMRPRA EXISTA
		if($this->daoOrdenCompra->numordcom=='')
		{
			$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.', debe estar en estatus EMITIDA y APROBADA';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE CONTABILIZACIÓN DE LA ORDEN DE COMPRA SEA MAYOR O IGUAL A LA FECHA DE REGISTRO
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoOrdenCompra->fecordcom,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Emision '.$this->daoOrdenCompra->fecordcom.' de la Orden Compra '.$numordcom.'::'.$estcondat;
			$this->valido = false;			
		}
		// VERIFICO QUE LA FECHA DE CONTABILIZACIÓN DE LA ORDEN DE COMPRA SEA MAYOR O IGUAL A LA FECHA DE APROBACION
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoOrdenCompra->fecaprord,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Aprobacion '.$this->daoOrdenCompra->fecaprord.' de la Orden Compra '.$numordcom.'::'.$estcondat;
			$this->valido = false;			
		}

		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoSOC();
		if(trim($this->confiva)=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoOrdenCompra->montot,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= 'ERROR -> El monto de la la Orden Compra '.$numordcom.'::'.$estcondat.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if(($this->revSolicitudesAsociadas($arrevento))&&($this->valido))
		{
			$arrcabecera['codemp'] = $this->daoOrdenCompra->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daoOrdenCompra->numordcom);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoOrdenCompra->obscom;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoOrdenCompra->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoOrdenCompra->montot,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoOrdenCompra->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetallePresupuestario($arrcabecera);
			if ($this->valido)
			{
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				if($this->valido)
				{
					$this->daoOrdenCompra->estcom=2;
					$this->daoOrdenCompra->fechaconta=$fecha;
					$this->daoOrdenCompra->fechaanula='1900-01-01';
					$this->daoOrdenCompra->conanusoc='';
					$this->valido = $this->daoOrdenCompra->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoOrdenCompra->ErrorMsg;
					}
				}
				unset($serviciocomprobante);
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
    
	public function revContabilizacion($objson) 
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
			$arrevento['desevetra'] = "Reversar la orden de compra {$objson->arrDetalle[$j]->numordcom}, asociado a la empresa {$this->codemp}";
			if ($this->reversarSOC($objson->arrDetalle[$j]->numordcom,$objson->arrDetalle[$j]->estcondat,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = 'Orden reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = "La Orden no fue reversada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

    public function reversarSOC($numordcom,$estcondat,$arrevento)	
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA ORDEN DE COMPRA A REVERSAR
		$criterio="codemp = '".$this->codemp."' AND numordcom='".$numordcom."' AND estcondat='".$estcondat."' AND estcom = '2' AND estapro = '1' ";
		$this->daoOrdenCompra = FabricaDao::CrearDAO('C','soc_ordencompra','',$criterio);
		$this->daoOrdenCompra->fechaconta = convertirFechaBd($fecha);
		$procede='SOCCOS';
		if($estcondat=='B')
		{
			$procede='SOCCOC';
		}
		// VERIFICO QUE LA ORDEN DE COMRPRA EXISTA
		if($this->daoOrdenCompra->numordcom=='')
		{
			$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.', debe estar en estatus CONTABILIZADA.';
			$this->valido = false;			
		}		
		$arrResultado=$this->verificarEnRecepcion($procede);
		if($arrResultado['Existe']) 
		{
			if($arrResultado['Estatus']!='A')
			{
				$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.' Se encuentra asociada a una Recepcion de documentos.';
				$this->valido = false;			
			}
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoOrdenCompra->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daoOrdenCompra->numordcom);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoOrdenCompra->fechaconta;
			$arrcabecera['descripcion'] = $this->daoOrdenCompra->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoOrdenCompra->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoOrdenCompra->montot,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoOrdenCompra->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$this->valido =$this->eliminarRevSolicitudesAsociadas($arrevento,1);
				if($this->valido)
				{
					$this->daoOrdenCompra->estcom=1;
					$this->daoOrdenCompra->fechaconta='1900-01-01';
					$this->daoOrdenCompra->fechaanula='1900-01-01';
					$this->daoOrdenCompra->conanusoc='';
					$this->valido = $this->daoOrdenCompra->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoOrdenCompra->ErrorMsg;
					}				
				}
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
	
	public function Anular($objson) 
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
		$fecha=convertirFechaBd($objson->fecha);
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrevento['desevetra'] = "Anulacion de la orden de compra {$objson->arrDetalle[$j]->numordcom}, asociado a la empresa {$this->codemp}";
			if ($this->anularSOC($objson->arrDetalle[$j]->numordcom,$objson->arrDetalle[$j]->estcondat,$fecha,$objson->arrDetalle[$j]->conanusoc,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = 'Orden anulada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = "La Orden no fue anulada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

    public function anularSOC($numordcom,$estcondat,$fecha,$conanusoc,$arrevento)
	{ 
 		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA ORDEN DE COMPRA A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numordcom='".$numordcom."' AND estcondat='".$estcondat."' AND estcom = '2' AND estapro = '1' ";
		$this->daoOrdenCompra = FabricaDao::CrearDAO('C','soc_ordencompra','',$criterio);
		$this->daoOrdenCompra->fechaconta = convertirFechaBd($fecha);
		$procede='SOCCOS';
		$procedeanula='SOCAOS';
		if($estcondat=='B')
		{
			$procede='SOCCOC';
			$procedeanula='SOCAOC';
		}
		// VERIFICO QUE LA ORDEN DE COMRPRA EXISTA
		if($this->daoOrdenCompra->numordcom=='')
		{
			$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.', debe estar en estatus CONTABILIZADA.';
			$this->valido = false;			
		}		
		$arrResultado=$this->verificarEnRecepcion($procede);
		if($arrResultado['Existe']) 
		{
			if($arrResultado['Estatus']!='A')
			{
				$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.' Se encuentra asociada a una Recepcion de documentos.';
				$this->valido = false;			
			}
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoOrdenCompra->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daoOrdenCompra->numordcom);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoOrdenCompra->fechaconta;
			$arrcabecera['descripcion'] = $this->daoOrdenCompra->obscon;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoOrdenCompra->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoOrdenCompra->montot,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoOrdenCompra->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,$procedeanula,$conanusoc,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$this->valido =$this->eliminarRevSolicitudesAsociadas($arrevento,3);
				if($this->valido)
				{
					$this->valido=$this->procesarDetallesSolicitudes();
					if($this->valido)
					{					
						$this->daoOrdenCompra->estcom=3;
						$this->daoOrdenCompra->fechaanula=$fecha;
						$this->daoOrdenCompra->conanusoc=$conanusoc;
						$this->valido = $this->daoOrdenCompra->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoOrdenCompra->ErrorMsg;
						}
					}
				}				
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
    
	public function revAnulacion($objson) 
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
			$arrevento['desevetra'] = "Reversar la Anulacion de la orden de compra {$objson->arrDetalle[$j]->numordcom}, asociado a la empresa {$this->codemp}";
			if ($this->revAnulacionSOC($objson->arrDetalle[$j]->numordcom,$objson->arrDetalle[$j]->estcondat,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = 'Orden reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numordcom;
				$arrRespuesta[$h]['mensaje'] = "La Orden no fue reversada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

    public function revAnulacionSOC($numordcom,$estcondat,$arrevento)
	{
 		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA ORDEN DE COMPRA A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numordcom='".$numordcom."' AND estcondat='".$estcondat."' AND estcom = '3' AND estapro = '1' ";
		$this->daoOrdenCompra = FabricaDao::CrearDAO('C','soc_ordencompra','',$criterio);
		$procede='SOCAOS';
		if($estcondat=='B')
		{
			$procede='SOCAOC';
		}
		// VERIFICO QUE LA ORDEN DE COMRPRA EXISTA
		if($this->daoOrdenCompra->numordcom=='')
		{
			$this->mensaje .= 'ERROR -> No  Orden Compra '.$numordcom.'::'.$estcondat.', debe estar en estatus ANULADA.';
			$this->valido = false;			
		}		
		if($this->reversarDetallesSolicitudes()) 
		{
			if(($this->revSolicitudesAsociadas($arrevento))&&($this->valido))
			{
				$arrcabecera['codemp'] = $this->daoOrdenCompra->codemp;
				$arrcabecera['procede'] = $procede;
				$arrcabecera['comprobante'] = fillComprobante($this->daoOrdenCompra->numordcom);
				$arrcabecera['codban'] = '---';
				$arrcabecera['ctaban'] = '-------------------------';
				$arrcabecera['fecha'] = $this->daoOrdenCompra->fechaconta;
				$arrcabecera['descripcion'] = $this->daoOrdenCompra->consol;
				$arrcabecera['tipo_comp'] = 1;
				$arrcabecera['tipo_destino'] = 'P';
				$arrcabecera['cod_pro'] = $this->daoOrdenCompra->cod_pro;
				$arrcabecera['ced_bene'] = '----------';
				$arrcabecera['total'] = number_format($this->daoOrdenCompra->montot,2,'.','');
				$arrcabecera['numpolcon'] = 0;
				$arrcabecera['esttrfcmp'] = 0;
				$arrcabecera['estrenfon'] = 0;
				$arrcabecera['codfuefin'] = $this->daoOrdenCompra->codfuefin;
				$arrcabecera['codusu'] = $_SESSION['la_logusr'];
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
				if($this->valido)
				{					
					$this->daoOrdenCompra->estcom=2;
					$this->daoOrdenCompra->fechaanula='1900-01-01';
					$this->daoOrdenCompra->conanusoc='';
					$this->valido = $this->daoOrdenCompra->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoOrdenCompra->ErrorMsg;
					}
				}
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
	
	public function buscarDetallePresupuesto($numordcom, $estcondat)
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		switch($_SESSION["la_empresa"]["estmodest"])
		{
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(soc_cuentagasto.codestpro1,length(soc_cuentagasto.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(soc_cuentagasto.codestpro2,length(soc_cuentagasto.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(soc_cuentagasto.codestpro3,length(soc_cuentagasto.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
				
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(soc_cuentagasto.codestpro1,length(soc_cuentagasto.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(soc_cuentagasto.codestpro2,length(soc_cuentagasto.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(soc_cuentagasto.codestpro3,length(soc_cuentagasto.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(soc_cuentagasto.codestpro4,length(soc_cuentagasto.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(soc_cuentagasto.codestpro5,length(soc_cuentagasto.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		 
		 
		$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura, soc_cuentagasto.codestpro1, soc_cuentagasto.codestpro2, soc_cuentagasto.codestpro3, ".
					 "       soc_cuentagasto.codestpro4, soc_cuentagasto.codestpro5, soc_cuentagasto.estcla, soc_cuentagasto.spg_cuenta, ".
					 "       monto,0 AS disponibilidad, spg_cuentas.denominacion  ".
					 "  FROM soc_cuentagasto ".
					 " INNER JOIN spg_cuentas ". 
					 "    ON soc_cuentagasto.codemp = spg_cuentas.codemp ". 
					 "   AND soc_cuentagasto.codestpro1 = spg_cuentas.codestpro1 ". 
					 "   AND soc_cuentagasto.codestpro2 = spg_cuentas.codestpro2 ". 
					 "   AND soc_cuentagasto.codestpro3 = spg_cuentas.codestpro3 ". 
					 "   AND soc_cuentagasto.codestpro4 = spg_cuentas.codestpro4 ". 
					 "   AND soc_cuentagasto.codestpro5 = spg_cuentas.codestpro5 ". 
					 "   AND soc_cuentagasto.estcla = spg_cuentas.estcla ". 
					 "   AND soc_cuentagasto.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE soc_cuentagasto.codemp='".$this->codemp."' ".
					 "   AND numordcom='".$numordcom."' ".
					 "   AND estcondat='".$estcondat."' ";

		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function buscarInformacionDetalle($numordcom,$estcondat){
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numordcom, $estcondat);
		while (!$dataCuentas->EOF) {
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
			$this->servicioComprobante->saldoSelect('ACTUAL');
			
			$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - ( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + $this->servicioComprobante->precomprometido));
			if(trim($dataCuentas->fields['operacion']) == 'DI'){
				if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2)){
					$disponible = 1;
				}
			}
			else {
				$disponible = 1;
			}
			$arrDisponible[$j]['estructura']     = $dataCuentas->fields['estructura'];
			$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
			$arrDisponible[$j]['operacion']      = $dataCuentas->fields['operacion'];
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
}
?>