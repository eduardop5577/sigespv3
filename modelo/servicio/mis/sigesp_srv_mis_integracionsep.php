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
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_iintegracionsep.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioIntegracionSEP implements IIntegracionSEP 
{

	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos;
	public  $servicioComprobante;
    public  $daoSolicitud;
		
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();	
		//$this->conexionBaseDatos->debug=true;
		$this->confiva = $_SESSION['la_empresa']['confiva'];	
		$this->estfilpremod = $_SESSION['la_empresa']['estfilpremod'];	
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->anulando = false;		
	}
	
	public function buscarContabilizar($numsol,$fecreg,$fecapr,$tipo,$codigo)
	{
		$criterio="";
		if(!empty($numsol))
		{
			$criterio .= " AND numsol like '%".$numsol."%'";
		}
		if(!empty($fecreg))
		{
			$fecreg=convertirFechaBd($fecreg);
			$criterio .= " AND fecregsol = '".$fecreg."'";
		}
		if(!empty($fecapr))
		{
			$fecapr=convertirFechaBd($fecapr);
			$criterio .= " AND fecaprsep = '".$fecapr."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipo_destino = '".$tipo."' ";
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
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('sep_solicitud.codestpro1','sep_solicitud.codestpro2','sep_solicitud.codestpro3','sep_solicitud.codestpro4','sep_solicitud.codestpro5','sep_solicitud.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND sep_solicitud.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SEP' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		
		$cadenasql="SELECT sep_solicitud.numsol, sep_solicitud.fecregsol, sep_solicitud.consol, sep_solicitud.fechaconta, ".
				   "	   sep_solicitud.fechaanula,sep_solicitud.cod_pro,sep_solicitud.ced_bene,sep_solicitud.tipo_destino,".
				   "       (SELECT nompro FROM rpc_proveedor WHERE sep_solicitud.codemp=rpc_proveedor.codemp AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
                   "  FROM sep_solicitud, sep_tiposolicitud ".
				   " WHERE sep_solicitud.codemp = '".$this->codemp."' ".
				   "   AND sep_solicitud.estsol = 'E' ".
				   "   AND sep_solicitud.estapro = 1 ".
				   "   AND sep_tiposolicitud.estope <> 'S' ".
				   "   AND NOT numsol IN (SELECT numdoccom ".
				   "						FROM cxp_rd_spg, cxp_rd ".
				   "  					   WHERE sep_solicitud.codemp =  cxp_rd_spg.codemp ".
				   "  						 AND sep_solicitud.numsol =  cxp_rd_spg.numdoccom ".
				   "  						 AND cxp_rd_spg.procede_doc = 'SEPSPC' ".
				   "  						 AND cxp_rd.estprodoc <> 'A' ".
				   "						 AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						 AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						 AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						 AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						 AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					   GROUP BY cxp_rd_spg.numdoccom) ".
			    	$criterio.
				   "   AND sep_solicitud.codtipsol = sep_tiposolicitud.codtipsol".
			       " ORDER BY sep_solicitud.numsol ";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSEP;
	}
	
	public function buscarRevContabilizacion($numsol,$fecreg,$fecapr,$tipo,$codigo,$fechaconta)
	{
		$criterio="";
		if(!empty($numsol))
		{
			$criterio .= " AND numsol like '%".$numsol."%'";
		}
		if(!empty($fecreg))
		{
			$fecreg=convertirFechaBd($fecreg);
			$criterio .= " AND fecregsol = '".$fecreg."'";
		}
		if(!empty($fecapr))
		{
			$fecapr=convertirFechaBd($fecapr);
			$criterio .= " AND fecaprsep = '".$fecapr."'";
		}
		if(!empty($fechaconta))
		{
			$fechaconta=convertirFechaBd($fechaconta);
			$criterio .= " AND fechaconta = '".$fechaconta."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipo_destino = '".$tipo."' ";
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
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('sep_solicitud.codestpro1','sep_solicitud.codestpro2','sep_solicitud.codestpro3','sep_solicitud.codestpro4','sep_solicitud.codestpro5','sep_solicitud.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND sep_solicitud.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SEP' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		$cadenasql="SELECT sep_solicitud.numsol, sep_solicitud.fecregsol, sep_solicitud.consol, sep_solicitud.fechaconta, ".
				   "	   sep_solicitud.fechaanula,sep_solicitud.cod_pro,sep_solicitud.ced_bene,sep_solicitud.tipo_destino,".
				   "       (SELECT nompro FROM rpc_proveedor WHERE sep_solicitud.codemp=rpc_proveedor.codemp AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
                   "  FROM sep_solicitud, sep_tiposolicitud ".
				   " WHERE sep_solicitud.codemp = '".$this->codemp."' ".
				   "   AND sep_solicitud.estsol = 'C' ".
				   "   AND sep_solicitud.estapro = 1 ".
				   "   AND sep_tiposolicitud.estope <> 'S' ".
				   "   AND NOT numsol IN (SELECT numdoccom ".
				   "						FROM cxp_rd_spg, cxp_rd ".
				   "  					   WHERE sep_solicitud.codemp =  cxp_rd_spg.codemp ".
				   "  						 AND sep_solicitud.numsol =  cxp_rd_spg.numdoccom ".
				   "  						 AND cxp_rd_spg.procede_doc = 'SEPSPC' ".
				   "  						 AND cxp_rd.estprodoc <> 'A' ".
				   "						 AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						 AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						 AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						 AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						 AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					   GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND NOT numsol IN (SELECT numsol FROM soc_enlace_sep ".
                   "  					   WHERE sep_solicitud.codemp =  soc_enlace_sep.codemp ".
                   "  						 AND sep_solicitud.numsol =  soc_enlace_sep.numsol ".
                   "  						 AND soc_enlace_sep.estordcom  IN (0,1,2)  ".
				   "					   GROUP BY soc_enlace_sep.numsol) ".
				   $criterio.
				   "   AND sep_solicitud.codtipsol = sep_tiposolicitud.codtipsol".
			       " ORDER BY sep_solicitud.numsol ";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSEP;
	}

	public function buscarAnular($numsol,$fecreg,$fecapr,$tipo,$codigo)
	{
		$criterio="";
		if(!empty($numsol))
		{
			$criterio .= " AND numsol like '%".$numsol."%'";
		}
		if(!empty($fecreg))
		{
			$fecreg=convertirFechaBd($fecreg);
			$criterio .= " AND fecregsol = '".$fecreg."'";
		}
		if(!empty($fecapr))
		{
			$fecapr=convertirFechaBd($fecapr);
			$criterio .= " AND fecaprsep = '".$fecapr."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipo_destino = '".$tipo."' ";
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
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('sep_solicitud.codestpro1','sep_solicitud.codestpro2','sep_solicitud.codestpro3','sep_solicitud.codestpro4','sep_solicitud.codestpro5','sep_solicitud.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND sep_solicitud.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SEP' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		
		$cadenasql="SELECT sep_solicitud.numsol, sep_solicitud.fecregsol, sep_solicitud.consol, sep_solicitud.fechaconta, ".
				   "	   sep_solicitud.fechaanula, sep_solicitud.conanusep,sep_solicitud.cod_pro,sep_solicitud.ced_bene,sep_solicitud.tipo_destino,".
				   "       (SELECT nompro FROM rpc_proveedor WHERE sep_solicitud.codemp=rpc_proveedor.codemp AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
                   "  FROM sep_solicitud, sep_tiposolicitud ".
				   " WHERE sep_solicitud.codemp = '".$this->codemp."' ".
				   "   AND sep_solicitud.estsol = 'C' ".
				   "   AND sep_solicitud.estapro = 1 ".
				   "   AND sep_tiposolicitud.estope <> 'S' ".
				   "   AND NOT numsol IN (SELECT numdoccom ".
				   "						FROM cxp_rd_spg, cxp_rd ".
				   "  					   WHERE sep_solicitud.codemp =  cxp_rd_spg.codemp ".
				   "  						 AND sep_solicitud.numsol =  cxp_rd_spg.numdoccom ".
				   "  						 AND cxp_rd_spg.procede_doc = 'SEPSPC' ".
				   "  						 AND cxp_rd.estprodoc <> 'A' ".
				   "						 AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						 AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						 AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						 AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						 AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					   GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND NOT numsol IN (SELECT numsol FROM soc_enlace_sep ".
                   "  					   WHERE sep_solicitud.codemp =  soc_enlace_sep.codemp ".
                   "  						 AND sep_solicitud.numsol =  soc_enlace_sep.numsol ".
                   "  						 AND soc_enlace_sep.estordcom  IN (0,1,2)  ".
				   "					   GROUP BY soc_enlace_sep.numsol) ".
				   $criterio.
				   "   AND sep_solicitud.codtipsol = sep_tiposolicitud.codtipsol".
			       " ORDER BY sep_solicitud.numsol ";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSEP;
	}

	public function buscarRevAnulacion($numsol,$fecreg,$fecapr,$tipo,$codigo,$fechaanula)
	{
		$criterio="";
		if(!empty($numsol))
		{
			$criterio .= " AND numsol like '%".$numsol."%'";
		}
		if(!empty($fecreg))
		{
			$fecreg=convertirFechaBd($fecreg);
			$criterio .= " AND fecregsol = '".$fecreg."'";
		}
		if(!empty($fecapr))
		{
			$fecapr=convertirFechaBd($fecapr);
			$criterio .= " AND fecaprsep = '".$fecapr."'";
		}
		if(!empty($fechaanula))
		{
			$fechaanula=convertirFechaBd($fechaanula);
			$criterio .= " AND fechaanula = '".$fechaanula."'";
		}
		if(!empty($tipo))
		{
			$criterio .= " AND tipo_destino = '".$tipo."' ";
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
		if($this->estfilpremod=='1')
		{
			$estconcat = $this->conexionBaseDatos->Concat('sep_solicitud.codestpro1','sep_solicitud.codestpro2','sep_solicitud.codestpro3','sep_solicitud.codestpro4','sep_solicitud.codestpro5','sep_solicitud.estcla');
			$criterio .= " AND {$estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					  WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					AND codsis='SPG' ".
			              "                         AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                         AND enabled=1) ".
			              " AND sep_solicitud.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			              "    					             WHERE sss_permisos_internos.codemp='{$this->codemp}' ".
			              "       					           AND codsis='SEP' ".
			              "                                    AND codusu='{$_SESSION["la_logusr"]}' ".
			              "                                    AND enabled=1) ";
		}
		$cadenasql="SELECT sep_solicitud.numsol, sep_solicitud.fecregsol, sep_solicitud.consol, sep_solicitud.fechaconta, ".
				   "	   sep_solicitud.fechaanula, sep_solicitud.conanusep,sep_solicitud.cod_pro,sep_solicitud.ced_bene,sep_solicitud.tipo_destino,".
				   "       (SELECT nompro FROM rpc_proveedor WHERE sep_solicitud.codemp=rpc_proveedor.codemp AND sep_solicitud.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE sep_solicitud.codemp=rpc_beneficiario.codemp AND sep_solicitud.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
                   "  FROM sep_solicitud, sep_tiposolicitud ".
				   " WHERE sep_solicitud.codemp = '".$this->codemp."' ".
				   "   AND sep_solicitud.estsol = 'A' ".
				   "   AND sep_solicitud.estapro = 1 ".
				   "   AND sep_tiposolicitud.estope <> 'S' ".
				   "   AND NOT numsol IN (SELECT numdoccom ".
				   "						FROM cxp_rd_spg, cxp_rd ".
				   "  					   WHERE sep_solicitud.codemp =  cxp_rd_spg.codemp ".
				   "  						 AND sep_solicitud.numsol =  cxp_rd_spg.numdoccom ".
				   "  						 AND cxp_rd_spg.procede_doc = 'SEPSPC' ".
				   "  						 AND cxp_rd.estprodoc <> 'A' ".
				   "						 AND cxp_rd.codemp = cxp_rd_spg.codemp ".
				   "						 AND cxp_rd.numrecdoc = cxp_rd_spg.numrecdoc ".
				   "						 AND cxp_rd.codtipdoc = cxp_rd_spg.codtipdoc ".
				   "						 AND cxp_rd.ced_bene = cxp_rd_spg.ced_bene ".
				   "						 AND cxp_rd.cod_pro = cxp_rd_spg.cod_pro ".
				   "					   GROUP BY cxp_rd_spg.numdoccom) ".
				   "   AND NOT numsol IN (SELECT numsol FROM soc_enlace_sep ".
                   "  					   WHERE sep_solicitud.codemp =  soc_enlace_sep.codemp ".
                   "  						 AND sep_solicitud.numsol =  soc_enlace_sep.numsol ".
                   "  						 AND soc_enlace_sep.estordcom  IN (0,1,2)  ".
				   "					   GROUP BY soc_enlace_sep.numsol) ".
				   $criterio.
				   "   AND sep_solicitud.codtipsol = sep_tiposolicitud.codtipsol".
			       " ORDER BY sep_solicitud.numsol ";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSEP;
	}	

	public function buscarDetallePresupuesto($numsol)
	{

		switch($_SESSION["la_empresa"]["estmodest"])
		{
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(sep_cuentagasto.codestpro1,length(sep_cuentagasto.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(sep_cuentagasto.codestpro2,length(sep_cuentagasto.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(sep_cuentagasto.codestpro3,length(sep_cuentagasto.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
				
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(sep_cuentagasto.codestpro1,length(sep_cuentagasto.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(sep_cuentagasto.codestpro2,length(sep_cuentagasto.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(sep_cuentagasto.codestpro3,length(sep_cuentagasto.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(sep_cuentagasto.codestpro4,length(sep_cuentagasto.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(sep_cuentagasto.codestpro5,length(sep_cuentagasto.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		 
		$cadenasql = "SELECT {$cadenaEstructura} AS estructura , sep_cuentagasto.estcla, sep_cuentagasto.spg_cuenta, '' AS operacion, monto, 0 AS disponibilidad,".
		             "       sep_cuentagasto.codestpro1, sep_cuentagasto.codestpro2, sep_cuentagasto.codestpro3, sep_cuentagasto.codestpro4, ".
					 "       sep_cuentagasto.codestpro5, spg_cuentas.denominacion  ".
					 "	FROM sep_cuentagasto ".
					 " INNER JOIN spg_cuentas ". 
					 "    ON sep_cuentagasto.codemp = spg_cuentas.codemp ". 
					 "   AND sep_cuentagasto.codestpro1 = spg_cuentas.codestpro1 ". 
					 "   AND sep_cuentagasto.codestpro2 = spg_cuentas.codestpro2 ". 
					 "   AND sep_cuentagasto.codestpro3 = spg_cuentas.codestpro3 ". 
					 "   AND sep_cuentagasto.codestpro4 = spg_cuentas.codestpro4 ". 
					 "   AND sep_cuentagasto.codestpro5 = spg_cuentas.codestpro5 ". 
					 "   AND sep_cuentagasto.estcla = spg_cuentas.estcla ". 
					 "   AND sep_cuentagasto.spg_cuenta = spg_cuentas.spg_cuenta ". 
					 " WHERE sep_cuentagasto.codemp = '".$this->codemp."' ".
					 " 	 AND numsol = '".$numsol."'";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $dataSEP;
	}

	public function buscarInformacionDetalleC($numsol)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($numsol);
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
			$this->servicioComprobante->saldoSelect('ACTUAL');
			$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - ( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + $this->servicioComprobante->precomprometido));
			if(trim($dataCuentas->fields['operacion']) == 'DI')
			{
				if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2))
				{
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
	
	public function validarMontoSEP($numsol)
	{
        $montoacumulado=0;
		$cadenasql="SELECT SUM(CASE WHEN monto IS NULL THEN 0 ELSE monto END) As monto ".
                   "  FROM sep_cuentagasto ".
                   " WHERE codemp='".$this->codemp."' ".
				   "   AND numsol='".$numsol."'";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$dataSEP->EOF)
			{
			   $montoacumulado = number_format($dataSEP->fields['monto'],2,'.','');
			}
		}
		unset($dataSEP);
		return $montoacumulado;
	}

	public function buscarDetallePresupuestario($numsol,$arrcabecera)
	{
		$arregloSPG = null;
                $documento = $this->daoSolicitud->numsolini;
		if ($this->daoSolicitud->numsolini=='---------------')
                {
                    $documento = $arrcabecera['comprobante'];
                }
		$cadenasql="SELECT sep_cuentagasto.codestpro1, sep_cuentagasto.codestpro2, sep_cuentagasto.codestpro3, sep_cuentagasto.codestpro4, ".
                           "       sep_cuentagasto.codestpro5, sep_cuentagasto.estcla, sep_cuentagasto.spg_cuenta, sep_tiposolicitud.estope, ".
                           "       sep_cuentagasto.codfuefin, SUM(sep_cuentagasto.monto) AS monto ".
                           "  FROM sep_cuentagasto ".
                           " INNER JOIN (sep_solicitud  ".
                           "             INNER JOIN sep_tiposolicitud ".
                           "                     ON sep_solicitud.codemp='".$this->codemp."'".
                           "                    AND sep_solicitud.numsol='".$numsol."'".		
		           "					AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol) ".
			   "    ON sep_cuentagasto.codemp='".$this->codemp."'".
                           "   AND sep_cuentagasto.numsol='".$numsol."'".		
		           "   AND sep_cuentagasto.codemp=sep_solicitud.codemp ".
			   "   AND sep_cuentagasto.numsol=sep_solicitud.numsol ".
                           " GROUP BY sep_cuentagasto.codestpro1, sep_cuentagasto.codestpro2, sep_cuentagasto.codestpro3, sep_cuentagasto.codestpro4, sep_cuentagasto.codestpro5, sep_cuentagasto.estcla, sep_cuentagasto.codfuefin, sep_cuentagasto.spg_cuenta, estope".
                           " ORDER BY sep_cuentagasto.codestpro1, sep_cuentagasto.codestpro2, sep_cuentagasto.codestpro3, sep_cuentagasto.codestpro4, sep_cuentagasto.codestpro5, sep_cuentagasto.estcla, sep_cuentagasto.codfuefin, sep_cuentagasto.spg_cuenta, estope";
		$dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($dataSEP===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$dataSEP->EOF)
			{
				$i++;
				$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['procede_doc']= $arrcabecera['procede'];
				$arregloSPG[$i]['documento']= $documento;
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['codestpro1']=$dataSEP->fields['codestpro1'];
				$arregloSPG[$i]['codestpro2']=$dataSEP->fields['codestpro2'];
				$arregloSPG[$i]['codestpro3']=$dataSEP->fields['codestpro3'];
				$arregloSPG[$i]['codestpro4']=$dataSEP->fields['codestpro4'];
				$arregloSPG[$i]['codestpro5']=$dataSEP->fields['codestpro5'];
				$arregloSPG[$i]['estcla']=$dataSEP->fields['estcla'];
				$arregloSPG[$i]['codfuefin']=$dataSEP->fields['codfuefin'];
				$arregloSPG[$i]['spg_cuenta']=$dataSEP->fields['spg_cuenta'];
				$arregloSPG[$i]['monto']=$dataSEP->fields['monto'];
				$arregloSPG[$i]['mensaje']= $dataSEP->fields['estope'];
				$dataSEP->MoveNext();
			}			
		}
		unset($dataSEP);
		return $arregloSPG;
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
			$arrevento['desevetra'] = "Contabilizacion de la solicitud de ejecucion presupuestaria {$objson->arrDetalle[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSEP($objson->arrDetalle[$j]->numsol,$fecha,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Solicitud contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'La Solicitud no fue contabilizada, '.$this->mensaje;
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSEP($numsol,$fecha,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND sep_solicitud.estsol = 'E' AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA, ESTE EMITIDA Y APROBADA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', en estatus EMITIDA o APROBADA.';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE CONTABILIZACIÓN DE LA SEP SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
		$fecha=convertirFechaBd($fecha);
                if(!compararFecha($this->daoSolicitud->fecregsol,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Emision '.$this->daoSolicitud->fecregsol.' de la Solicitud Nro '.$numsol;
			$this->valido = false;			
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoSEP($numsol);
		if(trim($this->confiva)=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoSolicitud->monto,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= 'ERROR -> El monto de la solicitud '.$numsol.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPSPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format($this->daoSolicitud->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetallePresupuestario($this->daoSolicitud->numsol,$arrcabecera);
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estsol='C';
				$this->daoSolicitud->fechaconta=$fecha;
				$this->daoSolicitud->fechaanula='1900-01-01';
				$this->daoSolicitud->conanusep='';
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
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
			$arrevento['desevetra'] = "Reversada la solicitud de ejecucion presupuestaria {$objson->arrDetalle[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->reversarSEP($objson->arrDetalle[$j]->numsol,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Solicitud reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'La Solicitud no fue reversada, '.$this->mensaje;
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function reversarSEP($numsol,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND sep_solicitud.estsol = 'C' AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
                // VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
                    $this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', en estatus CONTABILIZADA';
                    $this->valido = false;			
		}	
		if($this->valido)
		{
                    $sepRelacionada=$this->verificarSEPRelacionadas($this->daoSolicitud->numsol,'C');
                    if ($sepRelacionada<>'')
                    {
			$this->mensaje .= 'ERROR -> No se puede Reversar la ContabilizaciÃ³n la SEP '.$numsol.', esta Relacionada con la SEP '.$sepRelacionada.' y esta estÃ¡ contabilizada. ';
			$this->valido = false;			
                    }
                }
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPSPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSolicitud->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format($this->daoSolicitud->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estsol='E';
				$this->daoSolicitud->fechaconta='1900-01-01';
				$this->daoSolicitud->fechaanula='1900-01-01';
				$this->daoSolicitud->conanusep='';
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
				}				
			}
		}
		unset($serviciocomprobante);
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
		$fecha = convertirFechaBd($objson->fecha);
		for($j=0;$j<=$nSol-1;$j++)
		{
			$concepto=$objson->arrDetalle[$j]->conanusol;
			$arrevento['desevetra'] = "Anular la solicitud de ejecucion presupuestaria {$objson->arrDetalle[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->anularSEP($objson->arrDetalle[$j]->numsol,$fecha,$concepto,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Solicitud anulada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'La Solicitud no fue anulada,'.$this->mensaje;
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function anularSEP($numsol,$fecha,$conanusep,$arrevento)
	{
		DaoGenerico::iniciarTrans();  
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND sep_solicitud.estsol = 'C' AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', en estatus CONTABILIZADA';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE ANULACIÓN DE LA SEP SEA MAYOR O IGUAL A LA FECHA DE CONTABILIZACIÓN
		$fecha=convertirFechaBd($fecha);
                if(!compararFecha($this->daoSolicitud->fechaconta,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulacion '.$fecha.' es menor que la fecha de Contabilizacion '.$this->daoSolicitud->fechaconta.' de la Solicitud Nro '.$numsol;
			$this->valido = false;			
		}
		// VERIFICO QUE LA FECHA DE ANULACIÓN DE LA SEP SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
		$fecha=convertirFechaBd($fecha);
                if(!compararFecha($this->daoSolicitud->fecregsol,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulacion '.$fecha.' es menor que la fecha de Emision '.$this->daoSolicitud->fecregsol.' de la Solicitud Nro '.$numsol;
			$this->valido = false;			
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoSEP($numsol);
		if(trim($_SESSION['la_empresa']['confiva'])=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoSolicitud->monto,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= 'ERROR -> El monto de la solicitud '.$numsol.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if($this->valido)
		{
                    $sepRelacionada=$this->verificarSEPRelacionadas($this->daoSolicitud->numsol,'C');
                    if ($sepRelacionada<>'')
                    {
			$this->mensaje .= 'ERROR -> No se puede Anular la SEP '.$numsol.', esta Relacionada con la SEP '.$sepRelacionada.' y esta estÃ¡ contabilizada. ';
			$this->valido = false;			
                    }
                }
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPSPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSolicitud->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format(($this->daoSolicitud->monto),2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,'SEPSPA',$conanusep,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estsol='A';
				$this->daoSolicitud->fechaanula=$fecha;
				$this->daoSolicitud->conanusep=$conanusep;
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
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
			$arrevento['desevetra'] = "Reversar la anulacion de la solicitud de ejecucion presupuestaria {$objson->arrDetalle[$j]->numsol}, asociado a la empresa {$this->codemp}";
			if ($this->reversarAnulacionSEP($objson->arrDetalle[$j]->numsol,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'Solicitud reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numsol;
				$arrRespuesta[$h]['mensaje'] = 'La Solicitud no fue reversada,  '.$this->mensaje;
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function reversarAnulacionSEP($numsol,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND sep_solicitud.estsol = 'A' AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', en estatus Anulada';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA SOLICITUD EL ESTATUS SEA ANULADA
		if($this->daoSolicitud->estsol!='A') 
		{
			$this->mensaje .= 'ERROR -> No  Solicitud '.$numsol.' debe estar en estatus ANLUADA para su Reverso.';
			$this->valido = false;			
		}
		if($this->valido)
		{
                    $sepRelacionada=$this->verificarSEPRelacionadas($this->daoSolicitud->numsol,'A');
                    if ($sepRelacionada<>'')
                    {
			$this->mensaje .= 'ERROR -> No se puede Reversar la Anulacion de la SEP '.$numsol.', esta Relacionada con la SEP '.$sepRelacionada.' y esta estÃ¡ Anulada. ';
			$this->valido = false;			
                    }
                }
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPSPA';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSolicitud->fechaanula;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format($this->daoSolicitud->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSolicitud->estsol='C';
				$this->daoSolicitud->fechaanula='1900-01-01';
				$this->daoSolicitud->conanusep='';
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
				}								
			}	
		}
		unset($serviciocomprobante);
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

	public function revPrecompromiso($numsol,$fecha,$numordcom,$estcondat,$arrevento)
	{
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND (sep_solicitud.estsol = 'C' OR sep_solicitud.estsol = 'P') AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', debe estar en estatus CONTABILIZADA o PROCESADA para su reverso';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE REVERSO DE LA SEP SEA MAYOR O IGUAL A LA FECHA DE CONTABILIZACIÓN
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoSolicitud->fechaconta,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Reverso '.$fecha.' es menor que la fecha de Contabilizacion '.$this->daoSolicitud->fechaconta.' de la Solicitud Nro '.$numsol;
			$this->valido = false;			
		}
		// VERIFICO QUE LA FECHA DE REVERSO DE LA SEP SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
		$fecha=convertirFechaBd($fecha);
        if(!compararFecha($this->daoSolicitud->fecregsol,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Reverso '.$fecha.' es menor que la fecha de Emision '.$this->daoSolicitud->fecregsol.' de la Solicitud Nro '.$numsol;
			$this->valido = false;			
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoSEP($numsol);
		if(trim($_SESSION['la_empresa']['confiva'])=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoSolicitud->monto,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= 'ERROR -> El monto de la solicitud '.$numsol.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPSPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSolicitud->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format(($this->daoSolicitud->monto),2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,'SEPRPC',' ',$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$this->daoSolicitud->fechaanula=$fecha;
				$this->daoSolicitud->estsol='P';
				$this->daoSolicitud->conanusep='Reverso del Precompromiso para contabilizar Orden de Compra-Servicio '.$numordcom.'-'.$estcondat;
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
				}								
			}	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Reverso el Precompromiso de la Solicitud de Ejecucion Presupuestaria '.$numsol.'para contabilizar Orden de Compra-Servicio '.$numordcom.'-'.$estcondat;		
		if ($this->valido) 
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

	public function eliminarRevPrecompromiso($numsol,$numordcom,$estcondat,$arrevento)
	{
		// OBTENGO LA SOLICITUD A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' AND (sep_solicitud.estsol = 'C' OR sep_solicitud.estsol = 'P') AND sep_solicitud.estapro = 1 ";
		$this->daoSolicitud = FabricaDao::CrearDAO('C','sep_solicitud','',$criterio);
		// VERIFICO QUE LA SOLICITUD EXISTA
		if($this->daoSolicitud->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la solicitud Nro '.$numsol.', debe estar en estatus CONTABILIZADA o PROCESADA';
			$this->valido = false;			
		}		
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSolicitud->codemp;
			$arrcabecera['procede'] = 'SEPRPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSolicitud->numsol);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSolicitud->fechaanula;
			$arrcabecera['descripcion'] = $this->daoSolicitud->consol;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoSolicitud->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoSolicitud->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoSolicitud->ced_bene;
			$arrcabecera['total'] = number_format($this->daoSolicitud->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoSolicitud->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$serviciocomprobante->anulando = true;
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$this->daoSolicitud->fechaanula='1900-01-01';
				$this->daoSolicitud->estsol='C';
				$this->daoSolicitud->conanusep='';
				$this->valido = $this->daoSolicitud->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSolicitud->ErrorMsg;
				}				
			}
		}
		unset($serviciocomprobante);
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Elimino el Reverso el Precompromiso de la Solicitud de Ejecucion Presupuestaria '.$numsol.' asociado a la Orden de Compra-Servicio '.$numordcom.'-'.$estcondat.' ';			
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

	public function verificarSEPRelacionadas($numsolini,$estsol)
	{
            $numsol='';
            $cadenasql="SELECT numsol ".
                       "  FROM sep_solicitud ".
                       " WHERE codemp='".$this->codemp."' ".
                       "   AND numsolini='".$numsolini."' ".
                       "   AND estsol='".$estsol."'";
            $dataSEP = $this->conexionBaseDatos->Execute ( $cadenasql );
            if ($dataSEP===false)
            {
                    $this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
                    $this->valido = false;
            }
            else
            {
                if(!$dataSEP->EOF)
                {
                    $numsol=$dataSEP->fields['numsol'];
                }
            }
            unset($dataSEP);
            return $numsol;
	}
        
}
?>