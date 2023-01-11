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
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionscb.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");

class servicioIntegracionSCB implements IIntegracionSCB 
{
	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos; 
	
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->contintmovban=$_SESSION['la_empresa']['contintmovban'];
		$this->codemp=$_SESSION['la_empresa']['codemp'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}

	public function buscarMovBcoContabilizar($numdoc,$fecdoc,$codope,$numcarord)
	{
		$criterio="";
		if(!empty($numdoc))
		{
			$criterio .=" AND scb_movbco.numdoc like '%".$$numdoc."%'";
		}
		if(!empty($fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND scb_movbco.fecmov = '".$fecdoc."'";
		}
		if(!empty($codope))
		{
			$criterio .=" AND scb_movbco.codope = '".$codope."'";
			if ($codope=='ND')
			{
				if (!empty($numcarord)) 
				{
					$criterio .=" AND scb_movbco.numcarord like '%".$numcarord."%'";
				}
			}
			
		}
		$campo='scb_movbco.numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='scb_movbco.numconint';
		}
		$cadenasql="SELECT scb_movbco.codban, scb_movbco.ctaban, scb_movbco.estmov, ".$campo." AS numdoc, scb_movbco.fecmov, scb_movbco.conmov, ".
				   " 	   scb_movbco.codope, scb_movbco.fechaconta, scb_movbco.fechaanula, scb_movbco.numconint, scb_movbco.numcarord, scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino,".
                   " 	   (SELECT nomban FROM scb_banco WHERE scb_banco.codban=scb_movbco.codban) AS nomban, ".
				   "       (SELECT nompro FROM rpc_proveedor WHERE scb_movbco.codemp=rpc_proveedor.codemp AND scb_movbco.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM scb_movbco ".
				   " WHERE scb_movbco.codemp='".$this->codemp."' ".
				   "   AND scb_movbco.estmov='N' ".
				   "   AND scb_movbco.estmodordpag <> 'CM' ".
				   "   AND scb_movbco.estcon = 0 ".
				   $criterio;
		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}

	public function buscarMovBcoRevContabilizacion($numdoc,$fecdoc,$codope,$numcarord)
	{
		$criterio="";
		if(!empty($numdoc))
		{
			$criterio .=" AND scb_movbco.numdoc like '%".$numdoc."%'";
		}
		if(!empty($fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND scb_movbco.fecmov = '".$fecdoc."'";
		}
		if(!empty($codope))
		{
			$criterio .=" AND scb_movbco.codope = '".$codope."'";
			if ($codope=='ND')
			{
				if (!empty($numcarord)) 
				{
					$criterio .=" AND scb_movbco.numcarord like '%".$numcarord."%'";
				}
			}
			
		}
		$campo='scb_movbco.numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='scb_movbco.numconint';
		}
		$cadenasql="SELECT scb_movbco.codban, scb_movbco.ctaban, scb_movbco.estmov, ".$campo." AS numdoc, scb_movbco.fecmov, scb_movbco.conmov, ".
				   "       scb_movbco.codope, scb_movbco.conanu, scb_movbco.fechaconta, scb_movbco.fechaanula, scb_movbco.numconint, scb_movbco.numcarord, scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino, ".
                   "       (SELECT nomban FROM scb_banco WHERE scb_banco.codban=scb_movbco.codban) AS nomban, ".
				   "       (SELECT nompro FROM rpc_proveedor WHERE scb_movbco.codemp=rpc_proveedor.codemp AND scb_movbco.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM scb_movbco ".
				   " WHERE scb_movbco.codemp='".$this->codemp."' ".
				   "   AND scb_movbco.estmov='C' ".
				   "   AND scb_movbco.estmodordpag <> 'CM' ".
				   "   AND scb_movbco.estcon = 0 ".
				   $criterio;
		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}

	public function buscarMovBcoAnular($numdoc,$fecdoc,$codope,$numcarord)
	{
		$criterio="";
		if(!empty($numdoc))
		{
			$criterio .=" AND scb_movbco.numdoc like '%".$numdoc."%'";
		}
		if(!empty($fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND scb_movbco.fecmov = '".$fecdoc."'";
		}
		if(!empty($codope))
		{
			$criterio .=" AND scb_movbco.codope = '".$codope."'";
			if ($codope=='ND')
			{
				if (!empty($numcarord)) 
				{
					$criterio .=" AND scb_movbco.numcarord like '%".$numcarord."%'";
				}
			}
			
		}
		$campo='scb_movbco.numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='scb_movbco.numconint';
		}
		$cadenasql="SELECT scb_movbco.codban, scb_movbco.ctaban, scb_movbco.estmov, ".$campo." AS numdoc, scb_movbco.fecmov, scb_movbco.conmov, ".
				   "       scb_movbco.codope, scb_movbco.fechaconta, scb_movbco.fechaanula, scb_movbco.numconint, scb_movbco.numcarord, scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino, ".
                   "       (SELECT nomban FROM scb_banco WHERE scb_banco.codban=scb_movbco.codban) AS nomban, ".
				   "       (SELECT nompro FROM rpc_proveedor WHERE scb_movbco.codemp=rpc_proveedor.codemp AND scb_movbco.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM scb_movbco ".
				   " WHERE scb_movbco.codemp='".$this->codemp."' ".
				   "   AND scb_movbco.estmov='C' ".
				   "   AND scb_movbco.estmodordpag <> 'CM' ".
				   "   AND scb_movbco.estcon = 0 ".
				   $criterio;
		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}

	public function buscarMovBcoRevAnulacion($numdoc,$fecdoc,$codope,$numcarord)
	{
		$criterio="";
		if(!empty($numdoc))
		{
			$criterio .=" AND scb_movbco.numdoc like '%".$$numdoc."%'";
		}
		if(!empty($fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND scb_movbco.fecmov = '".$fecdoc."'";
		}
		if(!empty($codope))
		{
			$criterio .=" AND scb_movbco.codope = '".$codope."'";
			if ($codope=='ND')
			{
				if (!empty($numcarord)) 
				{
					$criterio .=" AND scb_movbco.numcarord like '%".$numcarord."%'";
				}
			}
			
		}
		$cadenasql="SELECT scb_movbco.codban, scb_movbco.ctaban, scb_movbco.estmov, scb_movbco.numdoc, scb_movbco.fecmov, scb_movbco.conmov, ".
				   "       scb_movbco.codope, scb_movbco.fechaconta, scb_movbco.fechaanula, scb_movbco.numconint, scb_movbco.numcarord, scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino, ".
                   "       (SELECT nomban FROM scb_banco WHERE scb_banco.codban=scb_movbco.codban) AS nomban, ".
				   "       (SELECT nompro FROM rpc_proveedor WHERE scb_movbco.codemp=rpc_proveedor.codemp AND scb_movbco.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM scb_movbco ".
				   " WHERE scb_movbco.codemp='".$this->codemp."' ".
				   "   AND scb_movbco.estmov='A' ".
				   "   AND scb_movbco.estmodordpag <> 'CM' ".
				   "   AND scb_movbco.estcon = 0 ".
				   $criterio;
		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}

	public function buscarOpdContabilizar($numdoc,$fecdoc)
	{
		$criterio="";
		if(!empty($as_numdoc))
		{
			$criterio .=" AND scb_movbco.numdoc like '%".$as_numdoc."%'";
		}
		if(!empty($as_fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND scb_movbco.fecmov = '".$as_fecdoc."'";
		}
		$cadenasql="SELECT scb_movbco.codban, scb_movbco.ctaban, scb_movbco.estmov, scb_movbco.numdoc, scb_movbco.fecmov, scb_movbco.conmov, ".
				   "       scb_movbco.codope, scb_movbco.fechaconta, scb_movbco.fechaanula, scb_movbco.numconint, scb_movbco.numcarord, scb_movbco.cod_pro,scb_movbco.ced_bene,scb_movbco.tipo_destino, ".
                   "       (SELECT nomban FROM scb_banco WHERE scb_banco.codban=scb_movbco.codban) AS nomban, ".
				   "       (SELECT nompro FROM rpc_proveedor WHERE scb_movbco.codemp=rpc_proveedor.codemp AND scb_movbco.cod_pro=rpc_proveedor.cod_pro) AS nompro,  ".
				   "       (SELECT nombene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS nombene	,  ".
				   "       (SELECT apebene FROM rpc_beneficiario WHERE scb_movbco.codemp=rpc_beneficiario.codemp AND scb_movbco.ced_bene=rpc_beneficiario.ced_bene) AS apebene  ".
				   "  FROM scb_movbco ".
				   " WHERE scb_movbco.codemp='".$this->codemp."' ".
				   "   AND scb_movbco.estmov='N' ".
				   "   AND scb_movbco.codope='OP' ".
				$criterio;

		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}
	
	public function buscarRevOpdContabilizar($numdoc,$fecdoc)
	{
		$criterio="";
		if(!empty($as_numdoc))
		{
			$criterio .=" AND numdoc like '%".$as_numdoc."%'";
		}
		if(!empty($as_fecdoc))
		{
			$fecdoc=convertirFechaBd($fecdoc);
			$criterio .=" AND fecmov = '".$as_fecdoc."'";
		}
		$cadenasql="SELECT codban, ctaban, estmov, numdoc, fecmov, conmov, codope, fechaconta, fechaanula  ".
                   "  FROM scb_movbco ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND estmov='C' ".
			   	   "   AND codope='OP' ".
				   $criterio;

		$dataSCB = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSCB;
	}

	public function buscarDetalleGasto($arrcabecera,$tabla)
	{
		$arregloSPG = null;
		$cadenasql="SELECT procede_doc, documento, codestpro, estcla, spg_cuenta, operacion, codfuefin, SUM(monto) AS monto  ".
                   "  FROM ".$tabla." ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
				   "   AND codope='".$this->daomovbco->codope."' ".
				   "   AND estmov='".$this->daomovbco->estmov."'".
				   " GROUP BY procede_doc, documento, codestpro, estcla, spg_cuenta, operacion, codfuefin ".
				   " ORDER BY monto, procede_doc, documento, codestpro, estcla, spg_cuenta, operacion, codfuefin";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=1;
			while (!$data->EOF)
			{
				$codestpro1=substr($data->fields['codestpro'],0,25);
				$codestpro2=substr($data->fields['codestpro'],25,25);
				$codestpro3=substr($data->fields['codestpro'],50,25);
				$codestpro4=substr($data->fields['codestpro'],75,25);
				$codestpro5=substr($data->fields['codestpro'],100,25);
				$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPG[$i]['codfuefin']=$data->fields['codfuefin'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['procede_doc']= $data->fields['procede_doc'];
				$arregloSPG[$i]['documento']= $data->fields['documento'];
				$arregloSPG[$i]['codestpro1']=$codestpro1;
				$arregloSPG[$i]['codestpro2']=$codestpro2;
				$arregloSPG[$i]['codestpro3']=$codestpro3;
				$arregloSPG[$i]['codestpro4']=$codestpro4;
				$arregloSPG[$i]['codestpro5']=$codestpro5;
				$arregloSPG[$i]['estcla']=$data->fields['estcla'];
				$arregloSPG[$i]['spg_cuenta']=$data->fields['spg_cuenta'];
				$arregloSPG[$i]['monto']=$data->fields['monto'];
				$arregloSPG[$i]['operacion']= $data->fields['operacion'];
				$i++;
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSPG;
	}

	public function buscarDetalleContable($arrcabecera)
	{
		$arregloSCG = null;
		
		$cadenasql="SELECT procede_doc, documento, scg_cuenta, debhab, SUM(monto) AS monto  ".
                   "  FROM scb_movbco_scg ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
				   "   AND codope='".$this->daomovbco->codope."' ".
				   "   AND estmov='".$this->daomovbco->estmov."'".
				   " GROUP BY procede_doc, documento, scg_cuenta, debhab ".
				   " ORDER BY procede_doc, documento, scg_cuenta, debhab ";
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
				$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSCG[$i]['procede']= $arrcabecera['procede'];
				$arregloSCG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSCG[$i]['codban']= $arrcabecera['codban'];
				$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSCG[$i]['orden']= $i;			
				$arregloSCG[$i]['sc_cuenta'] = $data->fields['scg_cuenta'];
				$arregloSCG[$i]['procede_doc'] = $data->fields['procede_doc'];
				$arregloSCG[$i]['documento'] = $data->fields['documento'];
				$arregloSCG[$i]['debhab'] = $data->fields['debhab'];
				$arregloSCG[$i]['monto'] = $data->fields['monto'];
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSCG;
	}

	public function buscarDetalleIngreso($arrcabecera)
	{
		$arregloSPI = null;
		
		$cadenasql="SELECT procede_doc, documento, codestpro1, codestpro2, codestpro3, codestpro4, ".
				   "	   codestpro5, estcla, spi_cuenta, operacion, SUM(monto) AS monto  ".
                   "  FROM scb_movbco_spi ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
				   "   AND codope='".$this->daomovbco->codope."' ".
				   "   AND estmov='".$this->daomovbco->estmov."'".
				   " GROUP BY procede_doc, documento, codestpro1, codestpro2, codestpro3, codestpro4, ".
				   "	      codestpro5, estcla, spi_cuenta, operacion ".
				   " ORDER BY procede_doc, documento, codestpro1, codestpro2, codestpro3, codestpro4, ".
				   "	      codestpro5, estcla, spi_cuenta, operacion";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				$arregloSPI[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPI[$i]['procede']= $arrcabecera['procede'];
				$arregloSPI[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPI[$i]['codban']= $arrcabecera['codban'];
				$arregloSPI[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPI[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPI[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPI[$i]['orden']= $i;
				$arregloSPI[$i]['estcla']=$data->fields['estcla'];
				$arregloSPI[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPI[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPI[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPI[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPI[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPI[$i]['spi_cuenta']=$data->fields['spi_cuenta'];
				$arregloSPI[$i]['procede_doc']= $data->fields['procede_doc'];
				$arregloSPI[$i]['documento']= $data->fields['documento'];
				$arregloSPI[$i]['operacion']= $data->fields['operacion'];
				$arregloSPI[$i]['monto']=$data->fields['monto'];
				$data->MoveNext();
				$i++;
			}			
		}
		unset($data);
		return $arregloSPI;
	}

	public function eliminarHistoricoPagado($numsol)
	{
		$cadenasql="DELETE  ".
                   "  FROM cxp_historico_solicitud ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND numsol='".$numsol."' ".
				   "   AND estprodoc='P' ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		return $this->valido;
	}
	
	public function procesarProgramacionPagos()
	{
		$cadenasql="SELECT numsol ".
				   "  FROM cxp_sol_banco ".
				   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
				   "   AND codope='".$this->daomovbco->codope."' ".
				   "   AND estmov='".$this->daomovbco->estmov."' ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$data->EOF) && ($this->valido))
			{
			    $numsol=$data->fields['numsol'];
					$cadenasql="SELECT SUM(b.monto) as monto_bco, 0 as monto_sol, 0 as monto_actual,0 as monto_nc, 0 as monto_nd ".
                                "  FROM cxp_sol_banco b,cxp_solicitudes s ".
                                "  WHERE b.codemp=s.codemp AND b.numsol=s.numsol AND ".
							    "        b.codemp='".$this->daomovbco->codemp."' AND b.numsol='".$numsol."' AND b.estmov='C' ".
								" UNION ".
                                "SELECT 0 as monto_bco, s.monsol as monto_sol, 0 as monto_actual,0 as monto_nc, 0 as monto_nd ".
                                "  FROM cxp_sol_banco b,cxp_solicitudes s ".
                                " WHERE b.codemp=s.codemp AND b.numsol=s.numsol AND ".
							    "       b.codemp='".$this->daomovbco->codemp."' AND b.numsol='".$numsol."' AND numdoc ='".$this->daomovbco->numdoc."' ".
								" UNION ".
                                "SELECT 0 as monto_bco, 0 as monto_sol, b.monto as monto_actual,0 as monto_nc, 0 as monto_nd ".
                                "  FROM cxp_sol_banco b,cxp_solicitudes s ".
                                " WHERE b.codemp=s.codemp AND b.numsol=s.numsol AND ".
							    "       b.codemp='".$this->daomovbco->codemp."' AND b.numsol='".$numsol."' AND numdoc ='".$this->daomovbco->numdoc."' ".	
								" UNION ".
                                "SELECT 0 as monto_bco, 0 as monto_sol, 0 as monto_actual, SUM(monto) as monto_nc, 0 as monto_nd".
                                "  FROM cxp_sol_dc ".
                                " WHERE cxp_sol_dc.codemp='".$this->daomovbco->codemp."' AND cxp_sol_dc.numsol='".$numsol."' AND codope ='NC' ".	
								" UNION ".
                                "SELECT 0 as monto_bco, 0 as monto_sol, 0 as monto_actual,  0 as monto_nc, SUM(monto) as monto_nd".
                                "  FROM cxp_sol_dc ".
                                " WHERE cxp_sol_dc.codemp='".$this->daomovbco->codemp."' AND cxp_sol_dc.numsol='".$numsol."' AND codope ='ND' ";	
				$data2 = $this->conexionBaseDatos->Execute ( $cadenasql );
				if ($data2===false)
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					$monto_bco=0; 
					$monto_actual=0; 
					$monto_sol=0;         
					$monto_nc=0;         
					$monto_nd=0;         
					while((!$data2->EOF)&&($this->valido))
					{
						$monto_bco=$monto_bco+number_format($data2->fields['monto_bco'],2,'.','');
						$monto_actual=$monto_actual+number_format($data2->fields['monto_actual'],2,'.','');
						$monto_sol=$monto_sol+number_format($data2->fields['monto_sol'],2,'.','');
						$monto_nc=$monto_nc+number_format($data2->fields['monto_nc'],2,'.','');
						$monto_nd=$monto_nd+number_format($data2->fields['monto_nd'],2,'.','');
							
						$total=($monto_bco+$monto_actual+$monto_nc-$monto_nd);
						$data2->MoveNext();
					}
					if((number_format($total,2,'.','')==number_format($monto_sol,2,'.',''))&&($this->valido)) 
					{ 
						$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' ";
						$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
						$this->daoSolicitud->estprosol='P';
						$this->valido=$this->daoSolicitud->modificar();
						if($this->valido)
						{
							$this->valido=$this->eliminarHistoricoPagado($numsol);
						}
						else
						{
							$this->mensaje .= $this->daoSolicitud->ErrorMsg;
						}
						unset($this->daoSolicitud);
						if ($this->valido)
						{
							$this->daoHistorico = FabricaDao::CrearDAO('N', 'cxp_historico_solicitud');	
							$this->daoHistorico->codemp = $this->daomovbco->codemp;
							$this->daoHistorico->numsol = $numsol;
							$this->daoHistorico->fecha = $this->daomovbco->fecmov;
							$this->daoHistorico->estprodoc = 'P';
							$this->valido=$this->daoHistorico->incluir();
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoHistorico->ErrorMsg;
							}
							unset($this->daoHistorico);
						}
						if ($this->valido)
						{
							$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' ";
							$this->daoProgramacion = FabricaDao::CrearDAO('C','scb_prog_pago','',$criterio);
							$this->daoProgramacion->estprosol='C';
							$this->valido=$this->daoProgramacion->modificar();
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoProgramacion->ErrorMsg;
							}
							unset($this->daoProgramacion);
						}
					} 
					else
					{
						$criterio="codemp = '".$this->codemp."' AND numsol='".$numsol."' ";
						$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
						$this->daoSolicitud->estprosol='S';
						$this->valido=$this->daoSolicitud->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoSolicitud->ErrorMsg;
						}
						unset($this->daoSolicitud);
					}
				}		 
				unset($data2);
				$data->MoveNext();
			}
		}		
        unset($data);			
		return $this->valido;
    }

    public function existeMovimientoBanco($estmov) 
	{
		$existe = false;
		$cadenaSql = "SELECT codemp ".
					 "	FROM scb_movbco ".
                     " WHERE codemp='".$this->daomovbco->codemp."' ".
				     "   AND codban='".$this->daomovbco->codban."' ".
				     "   AND ctaban='".$this->daomovbco->ctaban."' ".
				     "   AND numdoc='".$this->daomovbco->numdoc."' ".
			      	 "   AND codope='".$this->daomovbco->codope."' ".
				     "   AND estmov='".$estmov."' ";
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
				$existe = true;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
    public function crearMovimientoBanco($procede,$comprobante,$estmov,$fecmov,$fechaconta,$fechaanula,$conanu)
	{
		$existe=$this->existeMovimientoBanco($this->daomovbco->estmov);
		if (!$existe)
		{
			$this->mensaje .= $this->daomovbco->codban.'::'.$this->daomovbco->ctaban.'::'.$this->daomovbco->numdoc.'::'.$this->daomovbco->codope.'::'.$this->daomovbco->estmov.'. No existe.';
			$this->valido = false;	
		}
		if($this->valido)
		{
			$existe=$this->existeMovimientoBanco($estmov);
			if ($existe)
			{
				$this->mensaje .= $this->daomovbco->codban.'::'.$this->daomovbco->ctaban.'::'.$this->daomovbco->numdoc.'::'.$this->daomovbco->codope.'::'.$estmov.'. Ya existe.';
				$this->valido = false;	
			}
		}
		// SE CREA EL NUEVO MOVIMIENTO CON EL NUEVO ESTATUS.
		if($this->valido)
		{
	 		$cadenaSql = "INSERT INTO scb_movbco (codemp,codban,ctaban,numdoc,codope,estmov,cod_pro,ced_bene,".
				         "                        tipo_destino, codconmov, fecmov, conmov, nomproben, monto, ".
						 "                        estbpd, estcon, estcobing, esttra, chevau, estimpche, ".
						 "                        monobjret, monret, procede, comprobante, fecha, id_mco,".
						 "                        emicheproc, emicheced, emichenom, emichefec, estmovint, ".
						 "                        codusu, codopeidb, aliidb, feccon, estreglib, numcarord,".
						 "                        numpolcon,coduniadmsig,codbansig,fecordpagsig,tipdocressig,".
						 "                        numdocressig,estmodordpag,codfuefin,forpagsig,medpagsig,codestprosig,".
						 "						  fechaconta, fechaanula,conanu,nrocontrolop, estant,docant,monamo,numconint,tranoreglib,estcondoc,docdestrans,tiptrans,fecenvfir,fecenvcaj) ".
					     " SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',cod_pro,ced_bene,".
				         "        tipo_destino, codconmov, '".$fecmov."', conmov, nomproben, monto, ".
						 "        estbpd, estcon, estcobing, esttra, chevau, estimpche, ".
						 "        monobjret, monret,'".$procede."','".$comprobante."','".$fecmov."',id_mco,".
						 "        emicheproc, emicheced, emichenom, emichefec, estmovint, ".
						 "        codusu, codopeidb, aliidb, feccon, estreglib, numcarord, ".
						 "        numpolcon,coduniadmsig,codbansig,fecordpagsig,tipdocressig,".
						 "        numdocressig,estmodordpag,codfuefin,forpagsig,medpagsig,codestprosig, ".
						 "        '".$fechaconta."','".$fechaanula."','".$conanu."',nrocontrolop, estant,docant,monamo,numconint,tranoreglib,estcondoc,docdestrans,tiptrans,fecenvfir,fecenvcaj ".
					  	 "  FROM scb_movbco ".
	                     " WHERE codemp='".$this->daomovbco->codemp."' ".
					  	 "	 AND codban='".$this->daomovbco->codban."' ".
					     "   AND ctaban='".$this->daomovbco->ctaban."' ".
					     "   AND numdoc='".$this->daomovbco->numdoc."' ".
					     "   AND codope='".$this->daomovbco->codope."' ".
					     "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
		   $cadenaSql=" INSERT INTO scb_movbco_anticipo(codemp, codban, ctaban, numdoc, codope, estmov, codamo, monamo, ".
                      "                                 monsal, montotamo, sc_cuenta) ".
				      " SELECT codemp, codban, ctaban, numdoc, codope, '".$estmov."', codamo, monamo, monsal, montotamo, sc_cuenta ".
                      "  FROM scb_movbco_anticipo ".
	                  " WHERE codemp='".$this->daomovbco->codemp."' ".
					  "	  AND codban='".$this->daomovbco->codban."' ".
					  "   AND ctaban='".$this->daomovbco->ctaban."' ".
					  "   AND numdoc='".$this->daomovbco->numdoc."' ".
					  "   AND codope='".$this->daomovbco->codope."' ".
					  "   AND estmov='".$this->daomovbco->estmov."' ";
		   	$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="INSERT INTO scb_dt_movbco (codemp, codban, ctaban, numdoc, codope, estmov, cod_pro, ced_bene, numsolpag, ".
					   "							  monsolpag, ctabanbene) ".
					   " SELECT codemp, codban, ctaban, numdoc, codope, '".$estmov."', cod_pro, ced_bene, numsolpag,  monsolpag, ctabanbene".
					   "  FROM scb_dt_movbco ".
	                   " WHERE codemp='".$this->daomovbco->codemp."' ".
					   "   AND codban='".$this->daomovbco->codban."' ".
					   "   AND ctaban='".$this->daomovbco->ctaban."' ".
					   "   AND numdoc='".$this->daomovbco->numdoc."' ".
					   "   AND codope='".$this->daomovbco->codope."' ".
					   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql ="INSERT INTO scb_movbco_scg (codemp, codban, ctaban, numdoc, codope, estmov, scg_cuenta,".
			            "                            debhab, codded, documento, desmov, procede_doc, monto, monobjret) ".
					    " SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',scg_cuenta,".
					    "        debhab, codded, documento, desmov, procede_doc, monto, monobjret".
					    "  FROM scb_movbco_scg ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql =" INSERT INTO scb_movbco_spg (codemp,codban,ctaban,numdoc,codope,estmov,codestpro,".
			            "                             spg_cuenta,operacion,documento,desmov,procede_doc,monto,estcla,codfuefin) ".
					    " SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',codestpro,spg_cuenta,".
					    "        operacion,documento,desmov,procede_doc,monto,estcla,codfuefin ".
					    " FROM scb_movbco_spg ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql ="INSERT INTO scb_movbco_spgop (codemp,codban,ctaban,numdoc,codope,estmov,codestpro,".
		                "                              spg_cuenta,operacion,documento,coduniadm,desmov,procede_doc,".
						"                              monto,baseimp,codcar,estcla,codfuefin) ".
				        "SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',codestpro,spg_cuenta,".
				        "        operacion,documento,coduniadm,desmov,procede_doc,monto,baseimp,codcar,estcla,codfuefin ".
				        "  FROM scb_movbco_spgop ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";	
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql ="INSERT INTO scb_movbco_spi (codemp,codban,ctaban,numdoc,codope,estmov,spi_cuenta,   ".
		                "                             documento,operacion,desmov,procede_doc,monto, estcla,   ".
				        "                             codestpro1,codestpro2,codestpro3,codestpro4,codestpro5) ".
				        " SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',spi_cuenta,".
				        "        documento,operacion,desmov,procede_doc,monto,estcla,    ".
				        "        codestpro1,codestpro2,codestpro3,codestpro4,codestpro5  ".
				        "  FROM scb_movbco_spi ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";	
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql ="INSERT INTO scb_movbco_fuefinanciamiento (codemp, codban, ctaban, numdoc, codope, estmov, codfuefin) ".
					    "SELECT codemp,codban,ctaban,numdoc,codope,'".$estmov."',codfuefin ".
					    "  FROM scb_movbco_fuefinanciamiento ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql ="UPDATE scb_dt_op ".
				        "   SET estmov = '".$estmov."' ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";	
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
        if(($this->valido)&&(($estmov!="A")||($estmov!="O"))) 
		{
			$cadenaSql ="INSERT INTO cxp_sol_banco (codemp,numsol,codban,ctaban,numdoc,codope,estmov,monto,id) ".
					    "SELECT codemp,numsol,codban,ctaban,numdoc,codope,'".$estmov."',monto,id".
					    "  FROM cxp_sol_banco ".
	                    " WHERE codemp='".$this->daomovbco->codemp."' ".
					    "   AND codban='".$this->daomovbco->codban."' ".
					    "   AND ctaban='".$this->daomovbco->ctaban."' ".
					    "   AND numdoc='".$this->daomovbco->numdoc."' ".
					    "   AND codope='".$this->daomovbco->codope."' ".
					    "   AND estmov='".$this->daomovbco->estmov."' ";
						$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		return $this->valido;
	}

    public function eliminarMovimientoBanco($revanu=false)
	{
	    $cadenaSql="DELETE ".
	    		   "  FROM cxp_sol_banco ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
			       "   AND codope='".$this->daomovbco->codope."' ".
				   "   AND estmov='".$this->daomovbco->estmov."' "; 
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($dataSet);
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_spg ".
                   	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_spgop ".
                  	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_spi ".
                  	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_scg ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_dt_movbco ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";	
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_fuefinanciamiento ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco_anticipo ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if($this->valido)
		{
			$cadenaSql="DELETE ".
					   "  FROM scb_movbco ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numdoc='".$this->daomovbco->numdoc."' ".
                   	   "   AND codope='".$this->daomovbco->codope."' ".
                   	   "   AND estmov='".$this->daomovbco->estmov."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		if(($this->valido)&&($this->daomovbco->codope=='CH')&& ($revanu))
		{
			$cadenaSql="UPDATE scb_cheques ".
					   "   SET estche=0 ".
                 	   " WHERE codemp='".$this->daomovbco->codemp."' ".
                   	   "   AND codban='".$this->daomovbco->codban."' ".
                   	   "   AND ctaban='".$this->daomovbco->ctaban."' ".
                   	   "   AND numche='".$this->daomovbco->numdoc."' ";
			$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
			if ($dataSet===false)
			{
				$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($dataSet);
		}
		return $this->valido;
	}

    public function buscarAmortizaciones()
	{
		$valor=0;		
		$cadenaSql="SELECT count(*) as valor ".
                   "  FROM scb_movbco ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".				
				   "   AND docant='".$this->daomovbco->numdoc."' ".				
				   "   AND estant='2'";
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
				$valor=$dataSet->fields['valor'];
			}
		}
		unset($dataSet);
		return $valor;
	}

	public function restaurarProgramacionPagos($estmov)
	{
		$cadenaSql="SELECT numsol ".
                   "  FROM cxp_sol_banco ".
                   " WHERE codemp='".$this->daomovbco->codemp."' ".
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
			       "   AND codope='".$this->daomovbco->codope."' "; 
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			while((!$dataSet->EOF)&&($this->valido))
		    {
		    	$criterio=" numsol='".$dataSet->fields['numsol']."' ";
				$this->daoProgPago = FabricaDao::CrearDAO('C','scb_prog_pago','',$criterio);
				if(trim($this->daoProgPago->codban)=='')
				{
					$this->valido=false;
					$this->mensaje .= 'La programacion de Pago para el banco '.$this->daomovbco->codban.' y la cuenta '.$this->daomovbco->ctaban.' no existe para la solicitud '.$dataSet->fields['numsol'];
				}
				else
				{
					$this->daoProgPago->estmov=$estmov;
					$this->valido=$this->daoProgPago->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoProgPago->ErrorMsg;
					}
				}
				unset($this->daoProgPago);
				if($this->valido)
				{
					$criterio="codemp = '".$this->codemp."' AND numsol='".$dataSet->fields['numsol']."' ";
					$this->daoSolicitud = FabricaDao::CrearDAO('C','cxp_solicitudes','',$criterio);
					$this->daoSolicitud->estprosol='S';
					$this->valido=$this->daoSolicitud->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoSolicitud->ErrorMsg;
					}
					unset($this->daoSolicitud);
				}
				if($this->valido)
				{
					$this->valido=$this->eliminarHistoricoPagado($dataSet->fields['numsol']);
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		return $this->valido;
    }	
  
    public function eliminarAmortizacion()
    {
    	$cadenaSql="SELECT docant, monamo  ".
    			   "  FROM scb_movbco ".
				   " WHERE codemp='".$this->daomovbco->codemp."'". 
				   "   AND codban='".$this->daomovbco->codban."' ".
				   "   AND ctaban='".$this->daomovbco->ctaban."' ". 
				   "   AND codope='CH' ".
				   "   AND numdoc='".$this->daomovbco->numdoc."' ".
			       "   AND (estmov='C')"; 
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			while((!$dataSet->EOF)&&($this->valido))
		    {
    			$docant = $dataSet->fields['docant'];
				$monamo = $dataSet->fields['monamo'];
				if($docant!='---------------')
				{
					$cadenaSql="UPDATE scb_movbco_anticipo".
							   "   SET monamo=monamo-".$monamo.",".
							   "       monsal=monsal+".$monamo.
							   " WHERE numdoc='".$docant."'"; 
					$data  = $this->conexionBaseDatos->Execute ( $cadenaSql );
					if ($data===false)
					{
						$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
						$this->valido = false;
					}
				}
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);	
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
		for($j=0;$j<=$nSol-1;$j++)
		{
			$arrevento['desevetra'] = "Contabilizaci&#243;n del movimiento {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarMovBco($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Movimiento contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "El movimiento no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function contabilizarMovBco($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$detalle = $objson->arrDetalle[$j];
		$i = 0;
		$procede='SCBB'.$detalle->codope;
	    $mensaje_spi='EC';	      
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($detalle->codope=='DP')||($detalle->codope=='ND')||($detalle->codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$detalle->codban."' ".
				  "AND ctaban='".$detalle->ctaban."' ".
			 	  "AND ".$campo."='".$detalle->numdoc."' ".
				  "AND codope='".$detalle->codope."' ".
				  "AND estmov='".$detalle->estmov."'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		// VERIFICO QUE EL MOVIMIENTO EXISTA
		if($this->daomovbco->numdoc=='')
		{
			$this->mensaje .= 'ERROR -> No existe el movimiento  N°'.$detalle->codban.'::'.$detalle->ctaban.'::'.$detalle->numdoc.'::'.$detalle->codope.'::'.$detalle->estmov.', en estatus Emitido';
			$this->valido = false;			
		}
		$fechacon=$this->daomovbco->fecmov;
		if($objson->fecha!='')
		{
			$fechacon=convertirFechaBd($objson->fecha);
		}
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $fechacon;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetalleGasto($arrcabecera,'scb_movbco_spg');
			if($this->valido)		
			{
				$arrdetallespi=$this->buscarDetalleIngreso($arrcabecera);
			}
			if($this->valido)		
			{
				$arrdetallescg=$this->buscarDetalleContable($arrcabecera);
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->procesarProgramacionPagos();
			}
			if ($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'C',$this->daomovbco->fecmov,$this->daomovbco->fecmov,'1900-01-01','');
			}
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(false);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Contabilizo el Movimiento de Banco '.$detalle->codban.'::'.$detalle->ctaban.'::'.$detalle->numdoc.'::'.$detalle->codope.'::'.$detalle->estmov.', asociado a la empresa '.$this->codemp; 
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
    
	public function RevContabilizar($objson) 
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
			$detalle=$objson->arrDetalle[$j];
			$arrevento['desevetra'] = "Reversar el movimiento {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizacionMovBco($detalle->codban,$detalle->ctaban,$detalle->numdoc,$detalle->codope,$detalle->estmov,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Movimiento reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "El movimiento no fue reversado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function revContabilizacionMovBco($codban,$ctaban,$numdoc,$codope,$estmov,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		$procede='SCBB'.$codope;
	    $mensaje_spi='EC';	      
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$codban."' ".
				  "AND ctaban='".$ctaban."' ".
				  "AND ".$campo."='".$numdoc."' ".
				  "AND codope='".$codope."' ".
				  "AND estmov='".$estmov."'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		if($this->daomovbco->estant=='1')
		{
			if($this->buscarAmortizaciones()>0)
			{
				$this->mensaje .= '  ->No se puede Reversar la contabilizaci&#243;n el Documento '.$codban.'::'.$ctaban.'::'.$numdoc.'::'.$codope.'::'.$estmov.', es de tipo anticipo y posee Amortizaciones asociadas';
				$this->valido = false;
			}
		}
		if(($this->daomovbco->estbpd=='R')||($this->daomovbco->estbpd=='O')||($this->daomovbco->estbpd=='C')) 
		{
			$this->mensaje .= '  ->El Pago directo con Compromiso/Causado Previo no es soportado';
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $this->daomovbco->fecmov;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'N',$this->daomovbco->fecmov,'1900-01-01','1900-01-01','');
			}
			if($this->valido)
			{
				$this->valido = $this->restaurarProgramacionPagos('P');
			}
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(false);
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
    
	public function AnularSCBMOV($objson) 
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
			$detalle=$objson->arrDetalle[$j];
			$arrevento['desevetra'] = "Anular; del movimiento {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->anularMovBco($detalle->codban,$detalle->ctaban,$detalle->numdoc,$detalle->codope,$detalle->estmov,$objson->fechaanula,$detalle->conanu,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Movimiento anulado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "El movimiento no fue anulado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function anularMovBco($codban,$ctaban,$numdoc,$codope,$estmov,$fechaanula,$conanu,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		$procede='SCBB'.$codope;
		$procedeanula='SCBBA'.substr($codope,1,1);
	    $mensaje_spi='EC';	      
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$codban."' ".
				  "AND ctaban='".$ctaban."' ".
				  "AND ".$campo."='".$numdoc."' ".
				  "AND codope='".$codope."' ".
				  "AND estmov='".$estmov."'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		if($this->daomovbco->estant=='1')
		{
			if($this->buscarAmortizaciones()>0)
			{
				$this->mensaje .= '  ->No se puede Anular el Documento '.$codban.'::'.$ctaban.'::'.$numdoc.'::'.$codope.'::'.$estmov.', es de tipo anticipo y posee Amortizaciones asociadas';
				$this->valido = false;
			}
		}
		$fechaanula=convertirFechaBd($fechaanula);
        if(!compararFecha($this->daomovbco->fecmov,$fechaanula))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulaci&#243;n '.$fechaanula.' es menor que la fecha del Documento '.$this->daomovbco->fecmov.' ';
			$this->valido = false;			
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $this->daomovbco->fecmov;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fechaanula,$procedeanula,$conanu,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				 $this->valido = $this->eliminarAmortizacion();
			}
			if($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'O',$this->daomovbco->fecmov,$this->daomovbco->fechaconta,$fechaanula,$conanu);
			}
			if($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'A',$fechaanula,$this->daomovbco->fechaconta,$fechaanula,$conanu);
			}
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(false);
			}
			if($this->valido)
			{
				$this->valido = $this->restaurarProgramacionPagos('P');
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
    
	public function RevAnularSCBMOV($objson) 
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
			$detalle=$objson->arrDetalle[$j];
			$arrevento['desevetra'] = "Reversar la Anulaci&#243;n del movimiento {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->revAnulacionMovBco($detalle->codban,$detalle->ctaban,$detalle->numdoc,$detalle->codope,$detalle->estmov,$detalle->fechaanula,$detalle->fechaconta,$detalle->conanu,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Movimiento reversado la anulacion exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "El movimiento no fue reversado la anulado, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
    
    public function revAnulacionMovBco($codban,$ctaban,$numdoc,$codope,$estmov,$fechaanula,$fechaconta,$conanu,$arrevento)
    {
    	DaoGenerico::iniciarTrans();  		
		$procede='SCBB'.$codope;
		$procedeanula='SCBBA'.substr($codope,1,1);
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO 
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$codban."' ".
				  "AND ctaban='".$ctaban."' ".
				  "AND ".$campo."='".$numdoc."' ".
				  "AND codope='".$codope."' ".
				  "AND estmov='A'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		$fecha=convertirFechaBd($fechaanula);
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procedeanula;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(true);
			}
		}
		if($this->valido)
		{
			// OBTENGO EL MOVIMIENTO 
			$criterio="    codemp = '".$this->codemp."' ".
					  "AND codban='".$codban."' ".
					  "AND ctaban='".$ctaban."' ".
					  "AND ".$campo."='".$numdoc."' ".
					  "AND codope='".$codope."' ".
					  "AND estmov='O'";
			$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
			$fecha=convertirFechaBd($fechaconta);
			if($this->valido)
			{
				$arrcabecera['codemp'] = $this->daomovbco->codemp;
				$arrcabecera['procede'] = $procede;
				$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
				$arrcabecera['codban'] = $this->daomovbco->codban;
				$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
				$arrcabecera['fecha'] = $fecha;
				$arrcabecera['descripcion'] = $this->daomovbco->conmov;
				$arrcabecera['tipo_comp'] = 1;
				$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
				$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
				$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
				$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
				$arrcabecera['numpolcon'] = 0;
				$arrcabecera['esttrfcmp'] = 0;
				$arrcabecera['estrenfon'] = 0;
				$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
				$arrcabecera['codusu'] = $_SESSION['la_logusr'];
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				if ($this->valido)
				{
					$this->valido = $this->eliminarMovimientoBanco(true);
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
	
	public function buscarDetallePresupuesto($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ai_len2=$_SESSION["la_empresa"]["loncodestpro2"];
		$ai_len3=$_SESSION["la_empresa"]["loncodestpro3"];
		$ai_len4=$_SESSION["la_empresa"]["loncodestpro4"];
		$ai_len5=$_SESSION["la_empresa"]["loncodestpro5"];
		if (($as_codope!="DP")&&($as_codope!="NC"))
		{
			switch($ls_modalidad)
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(SUBSTR(codestpro,1,25),25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(SUBSTR(codestpro,26,25),25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(SUBSTR(codestpro,51,25),25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(SUBSTR(codestpro,1,25),25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(SUBSTR(codestpro,26,25),25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(SUBSTR(codestpro,51,25),25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(SUBSTR(codestpro,76,25),25-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(SUBSTR(codestpro,101,25),25-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			 
			$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
			$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura,SUBSTR(codestpro,1,25) as codestpro1, ".
						 "       SUBSTR(codestpro,26,25) as codestpro2, SUBSTR(codestpro,51,25) as codestpro3, ".
						 "       SUBSTR(codestpro,76,25) as codestpro4, SUBSTR(codestpro,101,25) as codestpro5, ".
						 "       scb_movbco_spg.estcla, scb_movbco_spg.spg_cuenta, monto,0 AS disponibilidad, spg_cuentas.denominacion  ".
						 "  FROM scb_movbco_spg ".
					     " INNER JOIN spg_cuentas ". 
					     "    ON scb_movbco_spg.codemp = spg_cuentas.codemp ". 
					     "   AND scb_movbco_spg.codestpro =  ".$codestpro." ".
					     "   AND scb_movbco_spg.estcla = spg_cuentas.estcla ". 
					     "   AND scb_movbco_spg.spg_cuenta = spg_cuentas.spg_cuenta ". 
						 " WHERE scb_movbco_spg.codemp='".$this->codemp."' ".
						 "   AND numdoc='".$as_numdoc."' ".
						 "   AND codban='".$as_codban."' ".
						 "   AND ctaban='".$as_ctaban."' ".
						 "   AND codope='".$as_codope."' ";
		}
		else
		{
			switch($ls_modalidad)
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(codestpro4,25-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(codestpro5,25-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			 
			$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , estcla, scb_movbco_spi.spi_cuenta as spg_cuenta, monto, 0 AS disponibilidad, ".
						 "		 codestpro1,codestpro2,codestpro3,codestpro4,codestpro5, spi_cuentas.denominacion  ".
						 "	FROM scb_movbco_spi ".
					     " INNER JOIN spi_cuentas ". 
					     "    ON scb_movbco_spi.codemp = spi_cuentas.codemp ". 
					     "   AND scb_movbco_spi.spi_cuenta = spi_cuentas.spi_cuenta ". 
						 " WHERE scb_movbco_spi.codemp='".$this->codemp."' ".
						 "   AND numdoc='".$as_numdoc."' ".
						 "   AND codban='".$as_codban."' ".
						 "   AND ctaban='".$as_ctaban."' ".
						 "   AND codope='".$as_codope."' ";
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function buscarInformacionDetalle($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($as_numdoc,$as_codban,$as_ctaban,$as_codope);
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
			if(trim($dataCuentas->fields['operacion']) == 'DI')
			{
				if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2))
				{
					$disponible = 1;
				}
			}
			else
			{
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
	
	public function detalleContable($as_numdoc,$as_codban,$as_ctaban,$as_codope) 
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT scg_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion  ".
					 "  FROM scb_movbco_scg ".
				     " INNER JOIN scg_cuentas ". 
				     "    ON scb_movbco_scg.codemp = scg_cuentas.codemp ". 
				     "   AND scb_movbco_scg.scg_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE scb_movbco_scg.codemp='".$this->codemp."' ".
					 "   AND numdoc='".$as_numdoc."' ".
					 "   AND codban='".$as_codban."' ".
					 "   AND ctaban='".$as_ctaban."' ".
					 "   AND codope='".$as_codope."' ";

		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function detalleContableMovcol($as_numdoc,$as_codban,$as_ctaban,$as_codope) 
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion  ".
					 "  FROM scb_movcol_scg ".
				     " INNER JOIN scg_cuentas ". 
				     "    ON scb_movcol_scg.codemp = scg_cuentas.codemp ". 
				     "   AND scb_movcol_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE scb_movcol_scg.codemp='".$this->codemp."' ".
					 "   AND numdoc='".$as_numdoc."' ".
					 "   AND codban='".$as_codban."' ".
					 "   AND ctaban='".$as_ctaban."' ".
					 "   AND codope='".$as_codope."' ";

		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function procesoConScbOpd($objson) 
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
			$arrevento['desevetra'] = "Contabilizaci&#243;n de la orden de pago directa {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarScbOpd($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Orden de pago contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "La Orden de pago no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarScbOpd($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$detalle = $objson->arrDetalle[$j];
		$procede='SCB'.$detalle->codope.'D';
		$procede_doc='SCB'.$detalle->codope.'D';
	    $mensaje_spi='EC';	      
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($detalle->codope=='DP')||($detalle->codope=='ND')||($detalle->codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$detalle->codban."' ".
				  "AND ctaban='".$detalle->ctaban."' ".
				  "AND ".$campo."='".$detalle->numdoc."' ".
				  "AND codope='".$detalle->codope."' ".
				  "AND estmov='".$detalle->estmov."'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		// VERIFICO QUE EL MOVIMIENTO EXISTA
		if($this->daomovbco->numdoc=='')
		{
			$this->mensaje .= 'ERROR -> No existe la orden de pago  N°'.$detalle->codban.'::'.$detalle->ctaban.'::'.$detalle->numdoc.'::'.$detalle->codope.'::'.$detalle->estmov.', en estatus Emitido';
			$this->valido = false;			
		}
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $this->daomovbco->fecmov;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetalleGasto($arrcabecera,'scb_movbco_spgop');
			if($this->valido)		
			{
				$arrdetallescg=$this->buscarDetalleContable($arrcabecera);
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'C',$this->daomovbco->fecmov,$this->daomovbco->fecmov,'1900-01-01','');
			}
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(false);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Contabilizo la Orden de Pago '.$detalle->codban.'::'.$detalle->ctaban.'::'.$detalle->numdoc.'::'.$detalle->codope.'::'.$detalle->estmov.', asociado a la empresa '.$this->codemp; 
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
	
	public function procesoRevConScbOpd($objson) 
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
			$detalle=$objson->arrDetalle[$j];
			$arrevento['desevetra'] = "Reversar la Orden de pago directa {$objson->arrDetalle[$j]->numdoc}, asociado a la empresa {$this->codemp}";
			if ($this->revContabilizacionScbOpd($detalle->codban,$detalle->ctaban,$detalle->numdoc,$detalle->codope,$detalle->estmov,$arrevento)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = 'Orden de pago reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $objson->arrDetalle[$j]->numdoc;
				$arrRespuesta[$h]['mensaje'] = "La orden de pago no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function revContabilizacionScbOpd($codban,$ctaban,$numdoc,$codope,$estmov,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		$procede='SCB'.$codope.'D';
	    $mensaje_spi='EC';	      
		$campo='numdoc';
		if(($this->contintmovban==1)&&(($codope=='DP')||($codope=='ND')||($codope=='NC')))
		{
			$campo='numconint';
		}
		// OBTENGO EL MOVIMIENTO A CONTABILIZAR
		$criterio="    codemp = '".$this->codemp."' ".
				  "AND codban='".$codban."' ".
				  "AND ctaban='".$ctaban."' ".
				  "AND ".$campo."='".$numdoc."' ".
				  "AND codope='".$codope."' ".
				  "AND estmov='".$estmov."'";
		$this->daomovbco = FabricaDao::CrearDAO('C','scb_movbco','',$criterio);
		if(($this->daomovbco->estbpd=='R')||($this->daomovbco->estbpd=='O')||($this->daomovbco->estbpd=='C')) 
		{
			$this->mensaje .= '  ->El Pago directo con Compromiso/Causado Previo no es soportado';
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daomovbco->codemp;
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = fillComprobante($this->daomovbco->numdoc);
			$arrcabecera['codban'] = $this->daomovbco->codban;
			$arrcabecera['ctaban'] = $this->daomovbco->ctaban;
			$arrcabecera['fecha'] = $this->daomovbco->fecmov;
			$arrcabecera['descripcion'] = $this->daomovbco->conmov;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daomovbco->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daomovbco->cod_pro;
			$arrcabecera['ced_bene'] = $this->daomovbco->ced_bene;
			$arrcabecera['total'] = number_format($this->daomovbco->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daomovbco->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido = $this->crearMovimientoBanco($procede,$arrcabecera['comprobante'],'N',$this->daomovbco->fecmov,'1900-01-01','1900-01-01','');
			}
			if ($this->valido)
			{
				$this->valido = $this->eliminarMovimientoBanco(false);
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