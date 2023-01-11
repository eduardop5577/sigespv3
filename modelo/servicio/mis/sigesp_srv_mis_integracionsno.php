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
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsno.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");
require_once ($dirsrv.'/modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');
require_once ($dirsrv.'/modelo/servicio/scb/sigesp_srv_scb_emision_chq.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_recepcion.php");
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');


class servicioIntegracionSNO implements IIntegracionSNO 
{
	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos;
	private $daoMisNomina; 
	private $daoDetalle;
	private $daoInsertarFormato;
		
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->daoMisNomina = null;		
		$this->daoDetalle = null;
		$this->daoInsertarFormato = null;
		$this->nombene = '';
		$this->codemp=$_SESSION['la_empresa']['codemp'];
	}
	
	public function buscarContabilizar($codcom,$codnom,$codperi,$tipnom,$estatus)
	{
		$criterio="";
		if(!empty($codnom))
		{
			$criterio .=" AND codnom = '".$codnom."'";
		}
		if(!empty($codperi))
		{
			$criterio .=" AND codperi = '".$codperi."'";
		}
		if(!empty($tipnom))
		{
			$criterio .=" AND tipnom = '".$tipnom."'";
		}
		if(($tipnom=='P')||($tipnom=='K'))
		{
			$criterio.=" AND estaprfid = '1'";
		}
		if(!empty($codcom))
		{
			$criterio.=" AND codcom = '".$codcom."'";
		}
		$provbene = $this->conexionBaseDatos->Concat('cod_pro',"'-'",'ced_bene');
		
		$cadenasql="SELECT DISTINCT codcom, descripcion, MAX(fechaconta) AS fechaconta, MAX(fechaanula) AS fechaanula, MAX(trim(operacion)) AS operacion, ".
				   "       MAX(tipo_destino) AS tipo_destino, cod_pro, ced_bene, codcomapo, ".
				   "       (SELECT MAX(nompro) FROM rpc_proveedor ".
				   "         WHERE rpc_proveedor.codemp = sno_dt_spg.codemp ".
				   "           AND rpc_proveedor.cod_pro = sno_dt_spg.cod_pro) AS nompro, ".
				   "       (SELECT MAX(nombene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_spg.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_spg.ced_bene) AS nombene, ".
				   "       (SELECT MAX(apebene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_spg.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_spg.ced_bene) AS apebene, ".
				   "       (SELECT MAX(fechasper) FROM sno_hperiodo ".
				   "         WHERE sno_hperiodo.codemp = sno_dt_spg.codemp ".
				   "           AND sno_hperiodo.codnom = sno_dt_spg.codnom ".
				   "           AND sno_hperiodo.codperi = sno_dt_spg.codperi) AS fechasper ".
				   "  FROM sno_dt_spg  ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND estatus=".$estatus.
				   "  ".$criterio.
				   "   AND codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$this->codemp."' AND (tipnom='P' OR tipnom='K') AND estaprfid='0') ".
				   " GROUP BY codemp, codnom, codperi, codcom, codcomapo, cod_pro, ced_bene, descripcion ".
				   " UNION ".
				   "SELECT DISTINCT codcom, descripcion, MAX(fechaconta) AS fechaconta, MAX(fechaanula) AS fechaanula, MAX(trim(operacion)) AS operacion, ".
				   "       MAX(tipo_destino) AS tipo_destino, cod_pro, ced_bene,  codcomapo, ".
				   "       (SELECT MAX(nompro) FROM rpc_proveedor ".
				   "         WHERE rpc_proveedor.codemp = sno_dt_spi.codemp ".
				   "           AND rpc_proveedor.cod_pro = sno_dt_spi.cod_pro) AS nompro, ".
				   "       (SELECT MAX(nombene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_spi.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_spi.ced_bene) AS nombene, ".
				   "       (SELECT MAX(apebene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_spi.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_spi.ced_bene) AS apebene, ".
				   "       (SELECT MAX(fechasper) FROM sno_hperiodo ".
				   "         WHERE sno_hperiodo.codemp = sno_dt_spi.codemp ".
				   "           AND sno_hperiodo.codnom = sno_dt_spi.codnom ".
				   "           AND sno_hperiodo.codperi = sno_dt_spi.codperi) AS fechasper ".
				   "  FROM sno_dt_spi  ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND estatus=".$estatus.
				   "  ".$criterio.
				   " GROUP BY codemp, codnom, codperi, codcom, codcomapo, cod_pro, ced_bene, descripcion ".
				   " UNION ".
				   "SELECT DISTINCT codcom, descripcion, MAX(fechaconta) AS fechaconta, MAX(fechaanula) AS fechaanula, '' AS operacion, ".
				   "       MAX(tipo_destino) AS tipo_destino, cod_pro, ced_bene,  codcomapo, ".
				   "       (SELECT MAX(nompro) FROM rpc_proveedor ".
				   "         WHERE rpc_proveedor.codemp = sno_dt_scg.codemp ".
				   "           AND rpc_proveedor.cod_pro = sno_dt_scg.cod_pro) AS nompro, ".
				   "       (SELECT MAX(nombene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_scg.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_scg.ced_bene) AS nombene, ".
				   "       (SELECT MAX(apebene) FROM rpc_beneficiario ".
				   "         WHERE rpc_beneficiario.codemp = sno_dt_scg.codemp ".
				   "           AND rpc_beneficiario.ced_bene = sno_dt_scg.ced_bene) AS apebene, ".
				   "       (SELECT MAX(fechasper) FROM sno_hperiodo ".
				   "         WHERE sno_hperiodo.codemp = sno_dt_scg.codemp ".
				   "           AND sno_hperiodo.codnom = sno_dt_scg.codnom ".
				   "           AND sno_hperiodo.codperi = sno_dt_scg.codperi) AS fechasper ".
				   "  FROM sno_dt_scg ".
				   " WHERE codemp = '".$this->codemp."' ".
				   "   AND estatus = ".$estatus.
				   "  ".$criterio.
				   "   AND codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$this->codemp."' )  ".
				   "   AND codcom NOT IN (SELECT codcom FROM sno_dt_spi WHERE codemp = '".$this->codemp."' )  ".
				   "   AND codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$this->codemp."' ".
				   "   AND (tipnom='P' OR tipnom='K') AND estaprfid='0') ".
				   " GROUP BY codemp, codnom, codperi, codcom, codcomapo, cod_pro, ced_bene, descripcion ".				   
				   " ORDER BY codcom, codcomapo ";
		$dataSNO = $this->conexionBaseDatos->Execute ( $cadenasql );
		return $dataSNO;
	}
	 
	public function buscarNominas($codnom,$denominacion,$estatus)
	{
		$cadenasql="SELECT DISTINCT sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   "  FROM sno_nomina, sno_dt_scg ".
				   " WHERE sno_dt_scg.codemp = '".$this->codemp."' ".
				   "   AND sno_dt_scg.estatus = ".$estatus.
				   "   AND sno_nomina.codnom like '%".$codnom."%' ".
				   "   AND sno_nomina.desnom like '%".$as_denominacion."%' ".
				   "   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$this->codemp."' )  ".
				   "   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spi WHERE codemp = '".$this->codemp."' )  ".
				   "   AND sno_nomina.codemp = sno_dt_scg.codemp ".
				   "   AND sno_nomina.codnom = sno_dt_scg.codnom ".
				   " GROUP BY sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   " UNION ".
				   "SELECT DISTINCT sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   "  FROM sno_nomina, sno_dt_spg  ".
				   " WHERE sno_dt_spg.codemp='".$this->codemp."' ".
				   "   AND sno_dt_spg.estatus=".$estatus.
				   "   AND sno_nomina.codnom like '%".$codnom."%' ".
				   "   AND sno_nomina.desnom like '%".$as_denominacion."%' ".
				   "   AND sno_nomina.codemp = sno_dt_spg.codemp ".
				   "   AND sno_nomina.codnom = sno_dt_spg.codnom ".
				   " GROUP BY sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   " UNION ".
				   "SELECT DISTINCT sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   "  FROM sno_nomina, sno_dt_spi  ".
				   " WHERE sno_dt_spi.codemp='".$this->codemp."' ".
				   "   AND sno_dt_spi.estatus=".$estatus.
				   "   AND sno_nomina.codnom like '%".$codnom."%' ".
				   "   AND sno_nomina.desnom like '%".$as_denominacion."%' ".
				   "   AND sno_nomina.codemp = sno_dt_spi.codemp ".
				   "   AND sno_nomina.codnom = sno_dt_spi.codnom ".
				   " GROUP BY sno_nomina.codemp, sno_nomina.codnom, sno_nomina.desnom ".
				   " ORDER BY codemp, codnom ";
		$dataSNO = $this->conexionBaseDatos->Execute ($cadenasql);
		return $dataSNO;
	}

	public function buscarPeriodos($codnom,$estatus)
	{
		$cadenasql="SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
				"   FROM sno_periodo, sno_dt_scg ".
				"   WHERE sno_periodo.cerper = 1 ";
		if($codnom!="")
		{
			$cadenasql=$cadenasql." AND sno_periodo.codnom = '".$codnom."' ";
		}		
				 
		$cadenasql=$cadenasql."	AND sno_dt_scg.estatus = ".$estatus." ".
						"   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spg WHERE codemp = '".$this->codemp."' )  ".
						"   AND sno_dt_scg.codcom NOT IN (SELECT codcom FROM sno_dt_spi WHERE codemp = '".$this->codemp."' )  ".
						"   AND sno_periodo.codemp = sno_dt_scg.codemp ".
						"   AND sno_periodo.codnom = sno_dt_scg.codnom ".
						"   AND sno_periodo.codperi = sno_dt_scg.codperi ".
						" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
						" UNION ".
						"SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
				        "  FROM sno_periodo, sno_dt_spg ".
				        " WHERE sno_periodo.cerper = 1 ";
		if($codnom!="")
		{
			$cadenasql=$cadenasql." AND sno_periodo.codnom = '".$codnom."' ";
		}		
				 
		$cadenasql=$cadenasql."	AND sno_dt_spg.estatus = ".$estatus." ".
						"   AND sno_periodo.codemp = sno_dt_spg.codemp ".
						"   AND sno_periodo.codnom = sno_dt_spg.codnom ".
						"   AND sno_periodo.codperi = sno_dt_spg.codperi ".
						" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
						" UNION ".
						"SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
				        "  FROM sno_periodo, sno_dt_spi ".
				        " WHERE sno_periodo.cerper = 1 ";
		if($codnom!="")
		{
			$cadenasql=$cadenasql." AND sno_periodo.codnom = '".$codnom."' ";
		}		
				 
		$cadenasql=$cadenasql."	AND sno_dt_spi.estatus = ".$estatus." ".
						"   AND sno_periodo.codemp = sno_dt_spi.codemp ".
						"   AND sno_periodo.codnom = sno_dt_spi.codnom ".
						"   AND sno_periodo.codperi = sno_dt_spi.codperi ".
						" GROUP BY sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper ".
						" ORDER BY codperi ";	
		$dataSNO = $this->conexionBaseDatos->Execute ($cadenasql);
		return $dataSNO;
	}

	public function buscarDetalleGasto($comprobante,$tipo,$codcomapo,$arrcabecera)
	{
		$arregloSPG = null;  
		$criterio='';
		if (($tipo=="A") || ($tipo=="L"))
		{
			$criterio .= "	AND codcomapo='".$codcomapo."'   ";
		}		  
		$cadenasql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, operacion, codconc, codfuefin, SUM(monto) AS monto ".
				   "  FROM sno_dt_spg ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codcom='".$comprobante."' ".
				   "   AND monto<>0 ".
				   $criterio.
				   " GROUP BY codemp, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin, operacion, codconc  ".
				   " ORDER BY codemp, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin, operacion, codconc  ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
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
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPG[$i]['orden']= $i;
				$arregloSPG[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPG[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPG[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPG[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPG[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPG[$i]['estcla']=$data->fields['estcla'];
				$arregloSPG[$i]['spg_cuenta']=$data->fields['spg_cuenta'];
				$arregloSPG[$i]['documento']= fillComprobante($data->fields['codconc']);
				$arregloSPG[$i]['codfuefin']=$data->fields['codfuefin'];
				$arregloSPG[$i]['monto']=$data->fields['monto'];
				$arregloSPG[$i]['mensaje']= $data->fields['operacion'];
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSPG;
	}
	
	public function buscarDetalleIngreso($comprobante,$arrcabecera)
	{
		$this->valido=true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$arreglo=array();	
		if (($tipo=="A") || ($tipo=="L"))
		{
			$criterio .= "	AND codcomapo='".$codcomapo."' ";
		}
		$ls_sql="SELECT spi_cuenta, operacion, descripcion, monto, codconc,  ".
		        "       codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla   ".
				"FROM sno_dt_spi ".
				"WHERE codemp='".$this->codemp."' ".
			    "   AND monto<>0 ".
				$criterio.
				"  AND codcom='".$comprobante."' ";  
		$data = $this->conexionBaseDatos->Execute ( $ls_sql );
		if ($data===false)
		{
			$this->mensaje .= '  CLASE->INTEGRADOR SNO METODO->buscarDetalleIngreso ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{     
			$i=0;      
   	       	while((!$data->EOF) and ($this->valido))
		   	{
		   		$i++;	
				$arregloSPI[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSPI[$i]['procede']= $arrcabecera['procede'];
				$arregloSPI[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPI[$i]['codban']= $arrcabecera['codban'];
				$arregloSPI[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPI[$i]['procede_doc']= $arrcabecera['procede'];
				$arregloSPI[$i]['codfuefin']=$arrcabecera['codfuefin'];
				$arregloSPI[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPI[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSPI[$i]['orden']= $i;
				$arregloSPI[$i]['codestpro1']=$data->fields['codestpro1'];
				$arregloSPI[$i]['codestpro2']=$data->fields['codestpro2'];
				$arregloSPI[$i]['codestpro3']=$data->fields['codestpro3'];
				$arregloSPI[$i]['codestpro4']=$data->fields['codestpro4'];
				$arregloSPI[$i]['codestpro5']=$data->fields['codestpro5'];
				$arregloSPI[$i]['estcla']=$data->fields['estcla'];
				$arregloSPI[$i]['spi_cuenta']=$data->fields['spi_cuenta'];
				$arregloSPI[$i]['documento']= fillComprobante($data->fields['codconc']);
				$arregloSPI[$i]['monto']=$data->fields['monto'];
				$arregloSPI[$i]['mensaje']= $data->fields['operacion'];
				$data->MoveNext();
		   	} // end while
		}	 
		return $arreglo;
    } //  end function uf_procesar_detalles_ingreso

	public function buscarDetalleContable($comprobante,$tipo,$codcomapo,$arrcabecera)
	{
		$arregloSCG = null;
		$criterio='';
		if ($tipo=="A" || ($tipo=="L"))
		{
			$criterio .= "	AND codcomapo='".$codcomapo."'  ";
		}		  
		$cadenasql="SELECT sc_cuenta, debhab, codconc, SUM(monto) AS monto ".
				   "  FROM sno_dt_scg ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codcom='".$comprobante."' ".
				   "   AND monto<>0 ".
				   $criterio.
				   " GROUP BY codemp, sc_cuenta, debhab, codconc ".
				   " ORDER BY codemp, sc_cuenta, debhab, codconc ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
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
				$arregloSCG[$i]['procede_doc']= $arrcabecera['procede'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSCG[$i]['orden']= $i;
				$arregloSCG[$i]['sc_cuenta']=$data->fields['sc_cuenta'];
				$arregloSCG[$i]['debhab']= $data->fields['debhab'];
				$arregloSCG[$i]['documento']= fillComprobante($data->fields['codconc']);
				$arregloSCG[$i]['monto']=$data->fields['monto'];
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSCG;
	}
	
	public function actualizarEstatusFechaNomina($comprobante,$tipo,$estatus,$codcomapo,$campo,$fechaconta,$fechaanula)
	{
		$this->valido=true;	
		$ls_sql="";
		$cadena="";
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		if($tipo=="X"){
			$estant="C";
			if($estatus=='0')
			{
				$estant="A";
			}
			$ls_sql="UPDATE sno_anticipoprestaciones ".
					"   SET estant='".$estant."'";
					" WHERE codemp='".$this->daoDetalle->codemp."' ".
					"   AND codper='".substr($this->daoDetalle->codnom,0,10)."' ".
					"   AND codant='".substr($this->daoDetalle->codnom,10,3)."'";
		}
		else{
			$ls_sql="UPDATE sno_periodo ".
					"   SET ".$campo." = ".$estatus.  //conper=".$estatus.
					" WHERE codemp='".$this->daoDetalle->codemp."' ".
					"   AND codnom='".$this->daoDetalle->codnom."' ".
					"   AND codperi='".$this->daoDetalle->codperi."'";
		}			 
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->actualizarEstatusFechaNomina ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		unset($data);
		if(!empty($codcomapo)){
			$cadena = "   AND codcomapo='".$codcomapo."' ";
		}
		if ($this->valido)
		{
			$ls_sql="UPDATE sno_dt_scg ".
					"   SET estatus=".$estatus." ,fechaconta= '".$fechaconta."' ,fechaanula='".$fechaanula."'  ".
					" WHERE codemp='".$this->daoDetalle->codemp."' ".
					"   AND codnom='".$this->daoDetalle->codnom."' ".
					"   AND codperi='".$this->daoDetalle->codperi."' ".
					"   AND codcom='".$comprobante."' ".
					"   AND tipnom='".$tipo."' $cadena ";
			$data = $this->conexionBaseDatos->Execute($ls_sql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->actualizarEstatusFechaNomina ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($data);
		}
		if ($this->valido)
		{
			$ls_sql="UPDATE sno_dt_spg ".
					"   SET estatus=".$estatus." ,fechaconta= '".$fechaconta."' ,fechaanula='".$fechaanula."'  ".
					" WHERE codemp='".$this->daoDetalle->codemp."' ".
					"   AND codnom='".$this->daoDetalle->codnom."' ".
					"   AND codperi='".$this->daoDetalle->codperi."' ".
					"   AND codcom='".$comprobante."' ".
					"   AND tipnom='".$tipo."' $cadena ";
			$data = $this->conexionBaseDatos->Execute($ls_sql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->actualizarEstatusFechaNomina ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($data);
		}
		if ($this->valido)
		{
			$ls_sql="UPDATE sno_dt_spi ".
					"   SET estatus=".$estatus." ,fechaconta= '".$fechaconta."' ,fechaanula='".$fechaanula."'  ".
					" WHERE codemp='".$this->daoDetalle->codemp."' ".
					"   AND codnom='".$nomina."' ".
					"   AND codperi='".$periodo."' ".
					"   AND codcom='".$comprobante."' ".
					"   AND tipnom='".$tipo."' $cadena ";
			$data = $this->conexionBaseDatos->Execute($ls_sql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->actualizarEstatusFechaNomina ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			unset($data);
		}
		return $this->valido;
	}
	
	public function eliminarMovBco($codban,$ctaban,$comprobante)
	{
		$this->valido=true;
		$cadenasql = "SELECT estcon ".
	    			 "  FROM scb_movbco  ".
	    			 " WHERE codemp='".$this->codemp."' ".
					 "   AND codban='".$codban."' ".
					 "   AND ctaban='".$ctaban."' ".
					 "   AND numdoc='".$comprobante."' ".
					 "   AND codope='ND' ".
					 "   AND estmov='L' ";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= '  ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		if($this->valido)
		{
			if (!$data->EOF)
			{
				if($data->fields['estcon']=='1')
				{
					$this->mensaje .= '  ERROR->La nota de debito asociada a la nomina se encuentra conciliada, no se puede realizar el reverso ';
					$this->valido = false;
				}
				else
				{
					$cadenasql="DELETE ".
							"  FROM scb_movbco_fuefinanciamiento ".
							" WHERE codemp='".$this->codemp."' ".
							"   AND codban='".$codban."' ".
							"   AND ctaban='".$ctaban."' ".
							"   AND numdoc='".$comprobante."' ".
							"   AND codope='ND' ".
							"   AND estmov='L' ";
					$data = $this->conexionBaseDatos->Execute($cadenasql);
					if($data===false)
					{
						$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
						$this->valido = false;
					}
					else{
						$cadenasql="DELETE ".
								"  FROM scb_movbco ".
								" WHERE codemp='".$this->codemp."' ".
								"   AND codban='".$codban."' ".
								"   AND ctaban='".$ctaban."' ".
								"   AND numdoc='".$comprobante."' ".
								"   AND codope='ND' ".
								"   AND estmov='L' ";
						$data = $this->conexionBaseDatos->Execute($cadenasql);
						if($data===false)
						{
							$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
							$this->valido = false;
						}
					}
					unset($data);
				}
			}
			else
			{
				$this->valido = false;
				$this->mensaje .= ' ERROR->No existe la Nota de Debito Asociada a la nomina';
			}
		}
		return $this->valido;
    } 
	
	public function obtenerDataBanco($nomina,$periodo)
	{		
		$arreglo=array();
		$this->valido=true;
		$cadenasql="SELECT codemp, codnom, codperi, codban, codcueban, codcuecon  ".
                   "  FROM sno_banco ".
                   " WHERE codemp='".$this->codemp."' ".
                   "   AND codnom='".$nomina."' ".
                   "   AND codperi='".$periodo."'";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$i=0;
				$total = $data->_numOfRows;
				$arreglo['total'][$i] = $total;
				while (!$data->EOF)
				{
					$arreglo['codban'][$i] = $data->fields['codban'];
					$arreglo['ctaban'][$i] = $data->fields['codcueban'];
					$arreglo['sc_cuenta'][$i] = $data->fields['codcuecon'];
					$i++;	
					$data->MoveNext();
				}
			}
			else
			{
				$this->valido = false;
				$this->mensaje .= " ERROR-> No existe data para generar la Nota de Debito.";
			}
		}
		return $arreglo;
    } 
    
    public function obtenerSumaBanco($comprobante,$cuenta_banco)
	{	
		$monto=0;
		$cadenasql="SELECT SUM(monto) as monto ".
                   "  FROM sno_dt_scg ".
                   " WHERE codemp='".$this->codemp."' ".
                   "   AND codcom='".$comprobante."' ".
                   "   AND debhab='H' ".
                   "   AND sc_cuenta='".$cuenta_banco."'";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$monto=round($data->fields["monto"],2);
			}
		}
		return $monto;
	}  
	
	public function generarNotDebBco($codnom,$codperi,$codcom,$decripcion,$fecha,$procesar,$tipo_destino,$cod_pro,$ced_bene,$arrevento)
	{
		$descripcion.=" PARA LOS DETALLES PRESUPUESTARIOS Y CONTABLES. VER COMPROBANTE DE N&#243;MINA.";
		$arrCabecera = array();
		$arrDataBanco = $this->obtenerDataBanco($codnom,$codperi);
		if($this->valido)
		{
			$total = $arrDataBanco['total'][0];
			for($i=0; $i<$total; $i++)
			{
				if($procesar=='1')
				{
					$monto = $this->obtenerSumaBanco($codcom,$arrDataBanco['sc_cuenta'][$i]);
					if(($monto!=0)&&($this->valido))
					{ 
						$arrCabecera["codemp"]	 = $this->codemp;
						$arrCabecera["codban"]	 = $arrDataBanco['codban'][$i];
						$arrCabecera["ctaban"]	 = $arrDataBanco['ctaban'][$i];
						$arrCabecera["numdoc"]	 = $codcom;
						$arrCabecera["codope"]	 = 'ND';
						$arrCabecera["fecmov"]	 = $fecha;
						$arrCabecera["conmov"]	 = $descripcion;
						$arrCabecera["codconmov"] = '---';
						$arrCabecera["cod_pro"]	 = $cod_pro;
						$arrCabecera["ced_bene"]	 = $ced_bene;
						$arrCabecera["nomproben"] = 'Ninguno';
						$arrCabecera["monto"]	 = $monto;
						$arrCabecera["monobjret"] = 0;
						$arrCabecera["monret"]	 = 0;
						$arrCabecera["chevau"]	 = "";
						$arrCabecera["estmov"]	 = 'L';
						$arrCabecera["estmovint"] = 0;
						$arrCabecera["estcobing"] = 0;
						$arrCabecera["estbpd"]	 = 'M';
						$arrCabecera["procede"]	 = "SNOCNO";
						$arrCabecera["estreglib"] = "";
						$arrCabecera["tipo_destino"]	 = $tipo_destino;
						$arrCabecera["numordpagmin"]	 = '-';
						$arrCabecera["codfuefin"] = $this->daoDetalle->codfuefin;
						$arrCabecera["codtipfon"] = '----';
						$arrCabecera["estmovcob"] = 0;
						$arrCabecera["numconint"] = "";
						$arrCabecera["tranoreglib"] = "";
						$arrCabecera["numchequera"] = "";
						$arrCabecera["codbansig"] = "";
						$arrCabecera["estmodordpag"] = 0;
						$arrCabecera["codmon"] = "---";
						$arrCabecera["tascam"] = 1;
						$arrCabecera["montot"] = $monto;
						$servicioBancario = new ServicioMovimientoScb();
						$this->valido = $servicioBancario->GuardarAutomatico($arrCabecera,null,null,null,$arrevento);
						$this->mensaje.= $servicioBancario->mensaje;
						unset($servicioBancario);
					}
					else
					{
						$this->mensaje .= ' ERROR->Verifique la Data de la cuenta '.$arrDataBanco['sc_cuenta'][$i].' para Generar la Nota de Debito en Banco';
						$this->valido = false;
					}
				}
				else
				{
					$this->valido = $this->eliminarMovBco($arrDataBanco['codban'][$i],$arrDataBanco['ctaban'][$i],$codcom);
				}
			}
		}
		return $this->valido;			                                                 	
	}
		
	public function SelectConfig($sistema,$seccion,$variable,$valor,$tipo,$arrevento)
	{
       	$cadenasql="SELECT value ".
			       "  FROM sigesp_config ".
			       " WHERE codemp='".$this->codemp."' ".
			       "   AND codsis='".$sistema."' ".
			       "   AND seccion='".$seccion."' ".
			       "   AND entry='".$variable."' ";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
		    if($data->fields['value']!='')
			{
				$valor=$data->fields['value'];
				$i=$i+1;
			}			
			if($i==0)
			{
				$this->valido=$this->insertarConfig($sistema,$seccion,$variable,$valor,$tipo,$arrevento);
				if ($this->valido)
				{
					$valor=$this->SelectConfig($sistema,$seccion,$variable,$valor,$tipo,$arrevento);
				}
			}
		}
		return rtrim($valor);     
	}
	
	public function insertarConfig($sistema,$seccion,$variable,$valor,$tipo,$arrevento)
	{
		$cadenasql="DELETE ".
				   "  FROM sigesp_config ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codsis='".$sistema."' ".
				   "   AND seccion='".$seccion."' ".
				   "  AND entry='".$variable."' ";		
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			switch ($tipo)
			{
				case "C"://Caracter
					$valor = $valor;
					break;
					
				case "D"://Double
					$valor = str_replace(".","",$valor);
					$valor = str_replace(",",".",$valor);
					$valor = $valor;
					break;
					
				case "B"://Boolean
					$valor = $valor;
					break;
					
				case "I"://Integer
					$valor = intval($valor);
					break;
			}
			$this->daoInsertarFormato = FabricaDao::CrearDAO("N", "sigesp_config");	
			//seteando la data e iniciando transaccion de base de datos
			$this->daoInsertarFormato->codemp=$this->codemp;
			$this->daoInsertarFormato->codsis=$sistema;
			$this->daoInsertarFormato->seccion=$seccion;
			$this->daoInsertarFormato->entry=$variable;
			$this->daoInsertarFormato->value=$valor;
			$this->daoInsertarFormato->type=$tipo;
			if(!$this->daoInsertarFormato->incluir())
			{
				$this->valido=false;
			}
		}
		return $this->valido;
	}
		
	public function SelectConfigNomina($codnom, $campo)
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$valor="";
		$ls_sql="SELECT ".$campo." as campo ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->codemp."' ".
				"   AND codnom='".$codnom."' ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->SelectConfigNomina ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$valor=$data->fields["campo"];
			}		
		}
		return rtrim($valor);
	}
		
	public function selectCuentaContableBco($banco,$ctabanco)
	{
		$valor="";
		$cadenasql="SELECT sc_cuenta ".
				   "  FROM scb_ctabanco ".
				   " WHERE scb_ctabanco.codemp='".$this->codemp."' ".
				   "   AND scb_ctabanco.codban='".$banco."' ".
				   "   AND scb_ctabanco.ctaban='".$ctabanco."'"; 
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$valor=$data->fields["sc_cuenta"];
			}		
		}
		return rtrim($valor);
	}
		
	public function validarBene($codper)
	{
		$valor="";
		$cadenasql="SELECT rpc_beneficiario.ced_bene, rpc_beneficiario.nombene, rpc_beneficiario.apebene ".
				   "  FROM rpc_beneficiario,sno_personal ".
				   " WHERE sno_personal.codemp='".$this->codemp."' ".
				   "   AND sno_personal.codper='".$codper."' ".
				   "   AND sno_personal.codemp=rpc_beneficiario.codemp".
				   "   AND sno_personal.cedper=rpc_beneficiario.ced_bene"; 
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$valor=$data->fields["ced_bene"];
				$this->nombene = $data->fields["nombene"]." ".$data->fields["apebene"];
			}		
		}
		return rtrim($valor);
	}
		
	public function insertarMovBcoOrdPagDir($codban,$ctaban,$comprobante,$fecha,$descripcion,$monto,$tipo_destino,
	                                        $codpro,$cedbene,$cuentabanco,$cuentapasivo,$arrevento)
	{
		$this->valido=true;
		$servicioBancario = new servicioBanco();
		$chevau=$servicioBancario->buscarVoucherNuevo($this->codemp);
		$this->mensaje.= $servicioBancario->mensaje;
		$this->valido = $servicioBancario->valido;
		unset($servicioBancario);
		$procede="SNOCNO";
		$arrCabeceraScb=array();
		$arrDetalleScg=array();
		$arrDetalleSpg=array();
		$arrDetalleSpI=array();
		$li_cont_dtscg =0;
		if($this->valido)
		{
			$chevau = substr($chevau,17,8);
			//Iniciar transacción
			$arrCabeceraScb["codemp"]	 = $this->codemp;
			$arrCabeceraScb["codban"]	 = $codban;
			$arrCabeceraScb["ctaban"]	 = $ctaban;
			$arrCabeceraScb["numdoc"]	 = $comprobante;
			$arrCabeceraScb["codope"]	 = 'CH';
			$arrCabeceraScb["fecmov"]	 = $fecha;
			$arrCabeceraScb["conmov"]	 = $descripcion;
			$arrCabeceraScb["codconmov"] = '---';
			$arrCabeceraScb["cod_pro"]	 = $codpro;
			$arrCabeceraScb["ced_bene"]	 = $cedbene;
			$arrCabeceraScb["nomproben"] = $this->nombene;
			$arrCabeceraScb["monto"]	 = $monto;
			$arrCabeceraScb["monobjret"] = 0;
			$arrCabeceraScb["monret"]	 = 0;
			$arrCabeceraScb["chevau"]	 = $chevau;
			$arrCabeceraScb["estmov"]	 = 'N';
			$arrCabeceraScb["estmovint"] = 0;
			$arrCabeceraScb["estcobing"] = 0;
			$arrCabeceraScb["estbpd"]	 = 'M';
			$arrCabeceraScb["procede"]	 = "SNOCNO";
			$arrCabeceraScb["estreglib"] = 0;
			$arrCabeceraScb["tipo_destino"]	 = $tipo_destino;
			$arrCabeceraScb["numordpagmin"]	 = '-';
			$arrCabeceraScb["codfuefin"] = $this->daoDetalle->codfuefin;
			$arrCabeceraScb["codtipfon"] = '----';
			$arrCabeceraScb["estmovcob"] = 0;
			$arrCabeceraScb["numconint"] = "";
			$arrCabeceraScb["tranoreglib"] = "";
			$arrCabeceraScb["numchequera"] = "";
			$arrCabeceraScb["codbansig"] = "";
			$arrCabeceraScb["estmodordpag"] = 0;
                        $arrCabeceraScb["codmon"] = "---";
                        $arrCabeceraScb["tascam"] = 1;
                        $arrCabeceraScb["montot"] = $monto;
			
			$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]  = $cuentapasivo;
			$arrDetalleScg["procede_doc"][$li_cont_dtscg] = $procede;
			$arrDetalleScg["desmov"][$li_cont_dtscg] = $descripcion;
			$arrDetalleScg["documento"][$li_cont_dtscg]	= $comprobante;
			$arrDetalleScg["debhab"][$li_cont_dtscg] = 'D';
			$arrDetalleScg["monto"][$li_cont_dtscg]	= $monto;
			$arrDetalleScg["monobjret"][$li_cont_dtscg] =0;
			$arrDetalleScg["codded"][$li_cont_dtscg] = '00000';
			$li_cont_dtscg++;
			$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]  = $cuentabanco;
			$arrDetalleScg["procede_doc"][$li_cont_dtscg] = $procede;
			$arrDetalleScg["desmov"][$li_cont_dtscg] = $descripcion;
			$arrDetalleScg["documento"][$li_cont_dtscg]	= $comprobante;
			$arrDetalleScg["debhab"][$li_cont_dtscg] = 'H';
			$arrDetalleScg["monto"][$li_cont_dtscg]	= $monto;
			$arrDetalleScg["monobjret"][$li_cont_dtscg] =0;
			$arrDetalleScg["codded"][$li_cont_dtscg] = '00000';

			$servicioBancario = new ServicioMovimientoScb();
			$this->valido = $servicioBancario->GuardarAutomatico($arrCabeceraScb,$arrDetalleScg,$arrDetalleSpg,$arrDetalleSpi,$arrevento);
			$this->mensaje.= $servicioBancario->mensaje;
			unset($servicioBancario);
		}
		return $this->valido;
	} 
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function generarPagoDirectoPersonalChe($nomina,$periodo,$fecha,$arrevento,$proceso)
	{
		$valor="";
		$this->valido=true;
		$cadenasql="SELECT sno_hpersonalnomina.codper, MAX(sno_hresumen.monnetres) as monnetres, MAX(sno_hpersonalnomina.cueaboper) as cueaboper  ".
				   "  FROM sno_hpersonalnomina,sno_hresumen ".
				   " WHERE sno_hpersonalnomina.codemp='".$this->codemp."' ".
				   "   AND sno_hpersonalnomina.codnom='".$nomina."' ".
				   "   AND sno_hpersonalnomina.codperi='".$periodo."' ".
				   "   AND sno_hpersonalnomina.pagefeper='1'".
				   "   AND sno_hresumen.monnetres > 0 ".
				   "   AND sno_hpersonalnomina.codemp=sno_hresumen.codemp".
				   "   AND sno_hpersonalnomina.codnom=sno_hresumen.codnom".
				   "   AND sno_hpersonalnomina.codperi=sno_hresumen.codperi".
				   "   AND sno_hpersonalnomina.codper=sno_hresumen.codper  ".
				   " GROUP BY sno_hpersonalnomina.codnom,sno_hpersonalnomina.codperi,sno_hpersonalnomina.codper";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$banco=$this->SelectConfig("SNO", "NOMINA", "BANCO PERSONAL CHEQUE", "", "C",$arrevento);
			$ctabanco=$this->SelectConfig("SNO", "NOMINA", "CTA. PERSONAL CHEQUE", "", "C",$arrevento);
			$cuentabanco=$this->selectCuentaContableBco($banco,$ctabanco);
			if((trim($banco)!="")&&(trim($ctabanco)!="")&&(trim($cuentabanco)!=""))
			{
				while((!$data->EOF)&&($this->valido))
				{
					$codper=$data->fields["codper"];
					$monpagper=$data->fields["monnetres"];
					$concepto= "PAGO NOMINA ".$nomina.", PERIODO ".$periodo.". BENEFICIARIO ".$codper;
					$numrecdoc=substr($codper,1,8).$nomina.substr($periodo,1,2)."N";
					$cedbene=$this->validarBene($codper);
					$comprobante=$nomina.$periodo.substr($codper,1,8);
					$cuentapasivo=$data->fields["cueaboper"];
					if($cedbene!="")
					{
						$estprodoc=$this->validarPagDir($banco,$ctabanco,$comprobante);
					}
					else
					{
						$this->mensaje.=" El Personal ".$codper." No esta como beneficiario. ";
						$this->valido=false;						
					}
					if($this->valido)
					{
						if($proceso=='1')
						{
							$this->valido=$this->insertarMovBcoOrdPagDir($banco,$ctabanco,$comprobante,$fecha,$concepto,
																		 $monpagper,"B","----------",$cedbene,$cuentabanco,$cuentapasivo,$arrevento);
						}
						else if($proceso=='0')
						{
							$this->valido=$this->eliminarPagDirPerChe($nomina,$periodo,$banco,$ctabanco,$comprobante);
						}
					}
				    $data->MoveNext();
				}
			}
			else
			{
				$this->mensaje.=" ERROR->Existe un error en la configuracion del banco a personas que cobran por cheque (OPD)";
				$this->valido=false;
			}
		}
		return $this->valido;
	}// 
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	public function buscarDetalleContableRd($nomina,$periodo,$numrecdoc,$codper,$codtipdoc,$cedbene)
	{	
		$arreglo=array();
		$count=1;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$ls_sql="SELECT sc_cuenta,debhab,monpagper  ".
				"FROM sno_rd ".
				"WHERE codemp='".$this->codemp."' ".
				"  AND codnom='".$nomina."' ".
				"  AND codperi='".$periodo."'".
				"  AND codper='".$codper."' "; 
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->buscarDetalleContableRd ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while(!$data->EOF)
			{
				$arreglo[$count]['codemp'] = $this->codemp;
				$arreglo[$count]['numrecdoc'] = $numrecdoc;
				$arreglo[$count]['codtipdoc'] = $codtipdoc;
				$arreglo[$count]['ced_bene'] = $cedbene;
				$arreglo[$count]['cod_pro'] = '----------';
				$arreglo[$count]['procede_doc'] = 'SNOCNO';
				$arreglo[$count]['numdoccom'] = $numrecdoc;
				$arreglo[$count]['debhab'] = $data->fields["debhab"];
				$arreglo[$count]['sc_cuenta'] = $data->fields["sc_cuenta"];
				$arreglo[$count]['monto'] = $data->fields["monpagper"];
				$arreglo[$count]['estasicon'] = 'M';
				$arreglo[$count]['estgenasi'] = 0;
				$count++;
				$data->MoveNext();
			}
		}	 
		return $arreglo;
    } 
    
    //-----------------------------------------------------------------------------------------------------------------------------------	
    
    public function actualizarEstatusRd($nomina,$periodo,$codper,$estatus)
    {
    	$this->valido=true;
    	$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$ls_sql="UPDATE sno_rd  ".
				"SET estcon='".$estatus."'  ".
				"WHERE codemp='".$this->codemp."' ".
				"   AND codnom='".$nomina."' ".
				"   AND codperi='".$periodo."' ".
				"   AND codper='".$codper."' ";
    	$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->actualizarEstatusRd ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$this->valido=true;
		}
	    return $this->valido;
    }
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function generarRecDocPerChe($nomina,$periodo,$fecha,$descripcion,$proceso,$arrevento)
	{
		$valor="";
		$this->valido=true;
		$arrCabeceraRd=array();
		$estcon = '0';
		if ($proceso=='0' )
		{
			$estcon = '1';
		}
		$cadenaSql="SELECT codper,MAX(codtipdoc) as  codtipdoc, MAX(monpagper) as monpagper, MAX(codcla) as codcla  ".
				   "  FROM sno_rd ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codnom='".$nomina."' ".
				   "   AND codperi='".$periodo."' ".
				   "   AND estcon='".$estcon."' ".
				   "   AND monpagper > 0  ".
				   " GROUP BY codnom,codperi,codper";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->generarRecDocPerChe ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$data->EOF)&&($this->valido))
			{
				$codper=$data->fields["codper"];
				$codtipdoc=$data->fields["codtipdoc"];
				$monpagper=$data->fields["monpagper"];
				$concepto= "PAGO NOMINA ".$descripcion.". BENEFICIARIO ".$codper;
				$numrecdoc=substr($codper,1,8).$nomina.substr($periodo,1,2)."N";
				$cedbene=$this->validarBene($codper);
			    if($cedbene!="")
				{
					$arrCabeceraRd['codemp'] = $this->codemp;
					$arrCabeceraRd['numrecdoc'] = $numrecdoc;
					$arrCabeceraRd['codtipdoc'] = $codtipdoc;
					$arrCabeceraRd['ced_bene'] = $cedbene;
					$arrCabeceraRd['cod_pro'] = '----------';
					$arrCabeceraRd['dencondoc'] = $concepto;
					$arrCabeceraRd['fecemidoc'] = $fecha;
					$arrCabeceraRd['fecregdoc'] = $fecha;
					$arrCabeceraRd['fecvendoc'] = $fecha;
					$arrCabeceraRd['montotdoc'] = $monpagper;
					$arrCabeceraRd['mondeddoc'] = 0;
					$arrCabeceraRd['moncardoc'] = 0;
					$arrCabeceraRd['tipproben'] = 'B';
					$arrCabeceraRd['numref'] = $numrecdoc;
					$arrCabeceraRd['estprodoc'] = 'R';
					$arrCabeceraRd['procede'] = 'SNOCNO';
					$arrCabeceraRd['estlibcom'] = 0;
					$arrCabeceraRd['estaprord'] = 0;
					$arrCabeceraRd['fecaprord'] = '1900-01-01';
					$arrCabeceraRd['usuaprord'] = '';
					$arrCabeceraRd['estimpmun'] = 0;
					$arrCabeceraRd['codcla'] = $data->fields["codcla"];
					$arrCabeceraRd['codfuefin'] = $this->daoDetalle->codfuefin;
					$arrCabeceraRd['codrecdoc'] = '000000000000001';
					$arrCabeceraRd['repcajchi'] = '0';
					$arrCabeceraRd['tipdoctesnac'] = '0';
				}
				else
				{
					$this->mensaje.="El personal de Codigo ".$codper." No esta registrado como beneficiario.";
					$this->valido=false; 
				}
				if($this->valido)
				{
					if($proceso=='1')
					{
						$arrDetalleRd=$this->buscarDetalleContableRd($nomina,$periodo,$numrecdoc,$codper,$codtipdoc,$cedbene);
						$serviciorecepcion = new ServicioRecepcion();
						$this->valido = $serviciorecepcion->guardarRecepcion($arrCabeceraRd,null,$arrDetalleRd,null,null,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
					}
					else if($proceso=='0')
					{
						$serviciorecepcion = new ServicioRecepcion();	
						$this->valido = $serviciorecepcion->eliminarRecepcion($arrCabeceraRd,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
					}
				}
				if($this->valido)
				{
					$this->valido=$this->actualizarEstatusRd($nomina,$periodo,$codper,$proceso);
				}
				$data->MoveNext();
			}	
		}
		return $this->valido;
	}// end function uf_generar_recepcion_documento_personal_cheque
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function verificarCuentaContable($estprog,$spg_cuenta)
	{
		$this->valido=false;
		$estructura=$estprog[0]."-".$estprog[1]."-".$estprog[2]."-".$estprog[3]."-".$estprog[4];
		$ls_sql = "SELECT status, denominacion, trim(sc_cuenta) as sc_cuenta   ".
				  "FROM spg_cuentas ".
			      "WHERE codemp='".$this->codemp."' ".
				  "  AND codestpro1 = '".$estprog[0]."' ".
			      "  AND codestpro2 = '".$estprog[1]."' ".
				  "  AND codestpro3 = '".$estprog[2]."' ".
			      "  AND codestpro4 = '".$estprog[3]."' ".
				  "  AND codestpro5 = '".$estprog[4]."' ".
				  "  AND estcla     = '".$estprog[5]."' ".
			      "  AND trim(spg_cuenta) ='".trim($spg_cuenta)."'" ; 
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->verificarCuentaContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		   {
		     if(!$data->EOF)
			 {
				 $arreglo['status'] = $data->fields["status"];
				 $arreglo['denominacion'] = $data->fields["denominacion"];				  
				 $arreglo['sccuenta'] = $data->fields["sc_cuenta"];
				 $this->valido = true;	 			
			 }
			 else
			 {
				  $this->mensaje .= "La Cuenta Presupuestaria ".$estructura."::".$spg_cuenta." no esta registrada";
			 } 
		}
		return $arreglo;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function buscarDetGasGuarderias($comprobante,$codtipdoc,$ced_bene,$cod_pro,$codcomapo,$numrecdoc,
									       $codestpro,$estcla,$monto,$spgcuenta)
	{
		$i=1;
		$estructura[0]=substr($codestpro,0,25);
		$estructura[1]=substr($codestpro,25,25);
		$estructura[2]=substr($codestpro,50,25);		
		$estructura[3]=substr($codestpro,75,25);
		$estructura[4]=substr($codestpro,100,25);
		$estructura[5]=$estcla;
		$arreglo=$this->verificarCuentaContable($estructura,$spgcuenta);
		$arrDetGas=array(); 
		if(!$this->valido)
		{
			$estructura=$estructura[0]."-".$estructura[1]."-".$estructura[2]."-".$estructura[3]."-".$estructura[4]."-".$estructura[5];
			$this->mensaje.="La Cuenta Presupuestaria ".$codestpro."::".$spgcuenta." no existe en el plan de cuenta.";			
			$this->valido=false;
		}
		if($this->valido)
		{
			$arrDetGas[$i]['codemp']=$this->codemp;
			$arrDetGas[$i]['numrecdoc']=$numrecdoc;
			$arrDetGas[$i]['codtipdoc']=$codtipdoc;
			$arrDetGas[$i]['ced_bene']=$ced_bene;
			$arrDetGas[$i]['cod_pro']=$cod_pro;
			$arrDetGas[$i]['procede_doc']='SNOCNO';
			$arrDetGas[$i]['numdoccom']=$comprobante;
			$arrDetGas[$i]['codestpro']=trim($codestpro);
			$arrDetGas[$i]['spg_cuenta']=$spgcuenta;
			$arrDetGas[$i]['monto']=$monto;
			$arrDetGas[$i]['estcla']=$estcla;
			$arrDetGas[$i]['sccuenta']=$arreglo['sccuenta'];
			$arrDetGas[$i]['codfuefin']=$this->daoDetalle->codfuefin;
		}
		return $arrDetGas;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function selectCuentaSCG($sc_cuenta)
	{
		$status="";
		$denominacion="";
		$this->valido=false;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$ls_sql="SELECT sc_cuenta, status, denominacion  ".
				"FROM scg_cuentas ".
				"WHERE codemp='".$this->codemp."' ".
				"   AND trim(sc_cuenta)='".trim($sc_cuenta)."'";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->selectCuentaSCG ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				 $arreglo['sc_cuenta']=$data->fields["sc_cuenta"];
				 $arreglo['denominacion']=$data->fields["denominacion"];
				 $arreglo['status']=$data->fields["status"];
				 $this->valido=true;
			}
			else
			{
				$this->mensaje .= " ERROR-> La cuenta Contable ".$sc_cuenta." no existe";
			}	
		}		
		return $arreglo;
	}  // end function uf_scg_select_cuenta()
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function buscarDetConGuarderias($comprobante,$codtipdoc,$ced_bene,$cod_pro,$codcomapo,$numrecdoc,$monto,$sccuenta)
	{
		$this->valido=true;	
		$estctaalt = $this->daoDetalle->estctaalt; 
		$arrDetCon=array();
		$i=1;
		$campo= " sc_cuenta AS sc_cuenta ";
      	if ($estctaalt==1)
		{
			$campo= " sc_cuentarecdoc AS sc_cuenta ";
		}
		$ls_sql="SELECT ".$campo."  ".
				"FROM rpc_beneficiario  ".
				"WHERE codemp='".$this->codemp."' ".
				"	AND ced_bene='".$ced_bene."' ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->buscarDetConGuarderias ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{           
			if(!$data->EOF)
			{
				$scg_cuenta = $data->fields["sc_cuenta"];
				$status="";
				$denominacion="";
				$arregloscguno = $this->selectCuentaSCG($scg_cuenta);
				if(!$this->valido)
				{
					$this->mensaje.="La cuenta contable ".trim($scg_cuenta).", definida en el beneficiario, no exite en el plan de cuenta.";			
					$this->valido=false;
				}
				else
				{
					$arregloscgdos = $this->selectCuentaSCG($sccuenta);
					if(!$this->valido)
					{
						$this->mensaje.="La cuenta contable ".trim($sccuenta).",  no exite en el plan de cuenta.";			
						$this->valido=false;
					}
				}
				if($this->valido)
				{
					$arrDetCon[$i]['codemp'] = $this->codemp;
					$arrDetCon[$i]['numrecdoc'] = $numrecdoc;
					$arrDetCon[$i]['codtipdoc'] = $codtipdoc;
					$arrDetCon[$i]['ced_bene'] = $ced_bene;
					$arrDetCon[$i]['cod_pro'] = $cod_pro;
					$arrDetCon[$i]['procede_doc'] = 'SNOCNO';
					$arrDetCon[$i]['numdoccom'] = $comprobante;
					$arrDetCon[$i]['debhab'] = 'D';
					$arrDetCon[$i]['sc_cuenta'] = $sccuenta;
					$arrDetCon[$i]['monto'] = $monto;
					$arrDetCon[$i]['estasicon'] = 'M';
					$i++;
					$arrDetCon[$i]['codemp'] = $this->codemp;
					$arrDetCon[$i]['numrecdoc'] = $numrecdoc;
					$arrDetCon[$i]['codtipdoc'] = $codtipdoc;
					$arrDetCon[$i]['ced_bene'] = $ced_bene;
					$arrDetCon[$i]['cod_pro'] = $cod_pro;
					$arrDetCon[$i]['procede_doc'] = 'SNOCNO';
					$arrDetCon[$i]['numdoccom'] = $comprobante;
					$arrDetCon[$i]['debhab'] = 'H';
					$arrDetCon[$i]['sc_cuenta'] = $scg_cuenta;
					$arrDetCon[$i]['monto'] = $monto;
					$arrDetCon[$i]['estasicon'] = 'M';
				} 
			} 
			else
			{
				$this->mensaje.=" El personal ".trim($ced_bene)." no exite como beneficiario.";			
				$this->valido=false;
			}
		} 
		return $arrDetCon;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function procesarRecDocGuarderias($comprobante,$fecha,$arrevento,$proceso)
	{
		$this->valido=true;
		$arrCabeceraRd=array();
		$arrDetGasGuarderias=array();
		$arrDetConGuarderias=array();
		$nomina = $this->daoDetalle->codnom; 
		$periodo = $this->daoDetalle->codperi;  
		$anocurnom = substr($comprobante,0,4);  
		$codtipdoc = trim($this->SelectConfig('SNO','CONFIG','GUARDERIA','C',"",$arrevento)); 
		$spg_cuentaobrero= trim($this->SelectConfig("SNO","NOMINA","DESTINO GUARDERIA OBRERO","C","",$arrevento)); 
		$spg_cuentapersonal= trim($this->SelectConfig("SNO","NOMINA","DESTINO GUARDERIA PERSONAL","C","",$arrevento)); 
		$spg_cuentapersonalcontratado=trim($this->SelectConfig("SNO","NOMINA","DESTINO GUARDERIA PERSONAL CONTRATADO","----------","C",$arrevento));
		$spg_cuentaobrerocontratado=trim($this->SelectConfig("SNO","NOMINA","DESTINO GUARDERIA OBRERO CONTRATADO","----------","C",$arrevento));
		$beneguarderia=trim($this->SelectConfig("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO BENEFICIARIO GUARDERIA","0","I",$arrevento));
		switch($beneguarderia)
		{
			case "0":
				$campo = ",sno_personal.cedper AS beneficiario  "; 
			break;
			
			case "1":
				$campo = ",sno_guarderias.cedbene AS beneficiario  "; 
			break;
		}
		$ls_sql="SELECT sno_guarderias.cedbene, sno_guarderias.nombene, sno_personal.codper, sno_personal.nomper, sno_personal.apeper, sno_guarderias.monto as valsal, sno_personal.cedper,  ".
				"       SUBSTR(CAST(sno_guarderias.codguar AS CHAR(10)),7,4) AS codguar, sno_hunidadadmin.codestpro1, ".
				"		sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"       sno_hunidadadmin.estcla,sno_hunidadadmin.desuniadm, sno_nomina.tipnom ".$campo.
				"FROM sno_hconcepto, sno_hsalida, sno_hpersonalnomina, sno_guarderias, sno_personal,sno_hunidadadmin,sno_nomina   ".
				"WHERE sno_hconcepto.codemp='".$this->codemp."' ".
				"  AND sno_hconcepto.codnom='".$nomina."' ".
				"  AND sno_hconcepto.anocur='".$anocurnom."' ".
				"  AND sno_hconcepto.codperi='".$periodo."' ".
				"  AND sno_hconcepto.guarrepcon='1'  ".
				"  AND sno_hconcepto.sigcon='R' ".
				"  AND sno_guarderias.monto<>0 ".
				"  AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2') ".
				"  AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"  AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"  AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"  AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"  AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"  AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"  AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"  AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"  AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"  AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"  AND sno_hpersonalnomina.codemp = sno_guarderias.codemp ".
				"  AND sno_hpersonalnomina.codper = sno_guarderias.codper".
				"  AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"  AND sno_hpersonalnomina.codper = sno_personal.codper".
				"  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp".
				"  AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom".
				"  AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur".
				"  AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi".
				"  AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm".
				"  AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm".
				"  AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm".
				"  AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm".
				"  AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm".
				"  AND sno_hconcepto.codemp = sno_nomina.codemp ".
				"  AND sno_hconcepto.codnom = sno_nomina.codnom ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->generarRecDocPerChe ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while((!$data->EOF)&&($this->valido))
			{
				$nombene=$data->fields["nombene"];
				$cedper=$data->fields["cedper"];
				$codguar=$data->fields["codguar"];
				$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
				$estcla=$data->fields["estcla"];
				$desuniadm=$data->fields["desuniadm"];
				$nomper=$data->fields["nomper"];
				$apeper=$data->fields["apeper"];
				$beneficiario=$data->fields["beneficiario"];
				$numrecdoc=$periodo.substr($codguar,1,3).substr($cedper,1,9);
				$descripcion = 'CANCELACIÓN DE NOMINA '.$this->daoDetalle->descripcion.' AL PERSONAL '.$nomper.' '.$apeper.'. GUARDERIA '.$nombene.'. UNIDAD ADMINISTRATIVA '.$desuniadm;  
				$spgcuenta="";
		      	$tipnom = $data->fields["tipnom"];
				$monto = $data->fields["valsal"];
				$cod_pro = "----------";
				if($tipnom=="1" || $tipnom=="5" || $tipnom=="9" || $tipnom=="10")
				{
					$spgcuenta=$spg_cuentapersonal;
				}
				if($tipnom=="2" || $tipnom=="6" || $tipnom=="13" || $tipnom=="14")
				{
					$spgcuenta=$spg_cuentapersonalcontratado;
				}
				if($tipnom=="3")
				{
					$spgcuenta=$spg_cuentaobrero;
				}
				if($tipnom=="4")
				{
					$spgcuenta=$spg_cuentaobrerocontratado;
				}
				if($spgcuenta=="")
				{
					$this->mensaje.="No estan definidas las cuentas presupuestarias para las Guarderias.";
					$this->valido=false;
				}
				if($this->valido)
				{
					$codrecdoc="000000000000001";
					$arrCabeceraRd['codemp'] = $this->codemp;
					$arrCabeceraRd['numrecdoc'] = $numrecdoc;
					$arrCabeceraRd['codtipdoc'] = $codtipdoc;
					$arrCabeceraRd['ced_bene'] = $beneficiario;
					$arrCabeceraRd['cod_pro'] = $cod_pro;
					$arrCabeceraRd['dencondoc'] = $descripcion;
					$arrCabeceraRd['fecemidoc'] = $fecha;
					$arrCabeceraRd['fecregdoc'] = $fecha;
					$arrCabeceraRd['fecvendoc'] = $fecha;
					$arrCabeceraRd['montotdoc'] = $monto;
					$arrCabeceraRd['mondeddoc'] = 0;
					$arrCabeceraRd['moncardoc'] = 0;
					$arrCabeceraRd['tipproben'] = "B";
					$arrCabeceraRd['numref'] = $comprobante;
					$arrCabeceraRd['estprodoc'] = 'R';
					$arrCabeceraRd['procede'] = 'SNOCNO';
					$arrCabeceraRd['estlibcom'] = 0;
					$arrCabeceraRd['estaprord'] = 0;
					$arrCabeceraRd['fecaprord'] = '1900-01-01';
					$arrCabeceraRd['usuaprord'] = '';
					$arrCabeceraRd['estimpmun'] = 0;
					$arrCabeceraRd['codcla'] = '--';
					$arrCabeceraRd['codrecdoc'] = $codrecdoc;
					$arrCabeceraRd['repcajchi'] = '0';
					$arrCabeceraRd['tipdoctesnac'] = '0';					
					if($proceso=='1')
					{
						$arrDetGasGuarderias=$this->buscarDetGasGuarderias($comprobante,$codtipdoc,$beneficiario,
																	   $cod_pro,"",$numrecdoc,$codestpro,$estcla,
																	   $monto,$spgcuenta);
						if($this->valido)
						{
							$arrDetConGuarderias=$this->buscarDetConGuarderias($comprobante,$codtipdoc,$beneficiario,$cod_pro,"",
																			   $numrecdoc,$monto,$arrDetGasGuarderias[1]['sccuenta']);
						}
						$serviciorecepcion = new ServicioRecepcion();
						$this->valido = $serviciorecepcion->guardarRecepcion($arrCabeceraRd,$arrDetGasGuarderias,$arrDetConGuarderias,null,null,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
					}
					if($proceso=='0')
					{
						$serviciorecepcion = new ServicioRecepcion();
						$this->valido = $serviciorecepcion->eliminarRecepcion($arrCabeceraRd,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
					}
 				}
				$data->MoveNext();
			}
		}
		return $this->valido;
	}  // end function uf_procesar_recepcion_documento_guarderias

	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function contabilizacionTipNom($comprobante,$fecha,$arrevento,$proceso)
	{
      	$estnotdeb=$this->daoDetalle->estnotdeb; 
      	$fechaconta=$fecha;
		// VERIFICO QUE LA NOMINA NO ESTE CONTABILIZADA
		if($this->daoDetalle->estatus==1 && $proceso=='1')
		{
			$this->mensaje .= 'ERROR -> La Nomina debe estar en estatus EMITIDA para su contabilizacion';
			$this->valido = false;			
		}
		if($this->valido)
		{
			$tipo = $this->daoDetalle->tipnom;
			$arrcabecera['codemp'] = $this->daoDetalle->codemp;
			$arrcabecera['procede'] = 'SNOCNO';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoDetalle->descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $this->daoDetalle->tipo_destino;
			$arrcabecera['cod_pro'] = $this->daoDetalle->cod_pro;
			$arrcabecera['ced_bene'] = $this->daoDetalle->ced_bene;
			$arrcabecera['total'] = number_format($this->daoDetalle->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoDetalle->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			if($proceso=='1')
			{
				$arrdetallespg=$this->buscarDetalleGasto($comprobante,$tipo,"",$arrcabecera);
				if(($this->valido)&&(trim($this->daoDetalle->operacion)<>'O'))
				{
					$arrdetallescg=$this->buscarDetalleContable($comprobante,$tipo,"",$arrcabecera);
				}
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,null,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
			}
			if($proceso=='0')
			{
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
				$fechaconta='1900-01-01';
			}
			if ($this->valido)
			{
				if($this->daoDetalle->estnotdeb)
				{
					$this->valido = $this->generarNotDebBco($this->daoDetalle->codnom,$this->daoDetalle->codperi,$comprobante,
					                                        $this->daoDetalle->decripcion,$fecha,$proceso,$this->daoDetalle->tipo_destino,
					                                        $this->daoDetalle->cod_pro,$this->daoDetalle->ced_bene,$arrevento);
				}
				if(($this->daoDetalle->tipnom=="N")&&($this->valido))
				{
					$pagodirecto=$this->SelectConfig("SNO", "CONFIG", "PAGO_DIRECTO_PERSONAL_CHEQUE", "0", "C",$arrevento);
					if($pagodirecto=="1")
					{
						$this->valido=$this->generarPagoDirectoPersonalChe($this->daoDetalle->codnom,$this->daoDetalle->codperi,$fecha,$arrevento,$proceso);		
					}
					else
					{
						$genrd=$this->SelectConfigNomina($this->daoDetalle->codnom,'recdocpagperche');
						if(($genrd=="1")&&($this->valido))
						{
							$this->valido=$this->generarRecDocPerChe($this->daoDetalle->codnom,$this->daoDetalle->codperi,$fecha,$this->daoDetalle->decripcion,$proceso,$arrevento);
						}
					}
					if($this->valido)
					{
						$guarderia=trim($this->SelectConfig("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","I","0",$arrevento));				
						if($guarderia==1)
						{
							$this->valido = $this->procesarRecDocGuarderias($comprobante,$fecha,$arrevento,$proceso);
						}
					}
					if($this->valido)
					{
						$genrd=$this->SelectConfigNomina($this->daoDetalle->codnom,'recdoccaunom');
						if(($genrd=="1")&&($this->valido))
						{
							$tipdoccaunom=$this->SelectConfigNomina($this->daoDetalle->codnom,'tipdoccaunom');
							$codclacau=$this->SelectConfigNomina($this->daoDetalle->codnom,'codclacau');
							$this->valido=$this->generarRecDocCausa($comprobante,$tipdoccaunom,$fecha,$proceso,$arrevento,$codclacau);
						}
					}
				}
				if($this->valido)
				{
					$this->valido = $this->actualizarEstatusFechaNomina($comprobante,$this->daoDetalle->tipnom,$proceso,"",'conper',$fechaconta,'1900-01-01');
				}
				
			}
		}
		return $this->valido;
	}
	
	
	public function obtenerSumaProveedorBeneficiario($comprobante,$estctaalt)
	{
		$monto=0;
		if($estctaalt=='1')
		{
			$scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$scctaprov='rpc_proveedor.sc_cuenta';
			$scctaben='rpc_beneficiario.sc_cuenta';
		}
		$cadenaSQL="SELECT SUM(monto) as monto  ".
                   "  FROM sno_dt_scg, rpc_beneficiario ".
                   " WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				   "   AND sno_dt_scg.codcom='".$comprobante."' ".
				   "   AND sno_dt_scg.debhab='H' ".
				   "   AND sno_dt_scg.tipo_destino='B' ".
				   "   AND sno_dt_scg.codemp = rpc_beneficiario.codemp ".
				   "   AND sno_dt_scg.ced_bene = rpc_beneficiario.ced_bene ".
				   "   AND sno_dt_scg.sc_cuenta = ".$scctaben." ".
				   " UNION ".
				   "SELECT SUM(monto) as monto ".
                   "  FROM sno_dt_scg, rpc_proveedor ".
                   " WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				   "   AND sno_dt_scg.codcom='".$comprobante."' ".
				   "   AND sno_dt_scg.debhab='H' ".
				   "   AND sno_dt_scg.tipo_destino='P' ".
				   "   AND sno_dt_scg.codemp = rpc_proveedor.codemp ".
				   "   AND sno_dt_scg.cod_pro = rpc_proveedor.cod_pro ".
				   "   AND sno_dt_scg.sc_cuenta = ".$scctaprov." ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerSumaProveedorBeneficiario ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			while(!$data->EOF)  
			{ 
				$monto=$monto+round($data->fields["monto"],2); 
				$data->MoveNext();
			}
		}
		return $monto;
	}  // end function uf_obtener_suma_proveedor_beneficiario

	
	public function obtenerSumaClasificador($comprobante,$codcomapo)
	{
		$monto=0;
		$criterio='';
		if ($codcomapo<>'')
		{
			$criterio="   AND sno_dt_scg.codcomapo='".$codcomapo."'";
		}
		$ls_sql="SELECT SUM(monto) as monto  ".
                "FROM sno_dt_scg, cxp_clasificador_rd ".
                "WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				"  AND sno_dt_scg.codcom='".$comprobante."' ".
				"  AND sno_dt_scg.debhab='H' ".
				$criterio.
				"  AND sno_dt_scg.codemp = cxp_clasificador_rd.codemp ".
				"  AND sno_dt_scg.codcla = cxp_clasificador_rd.codcla ".
				"  AND sno_dt_scg.sc_cuenta = cxp_clasificador_rd.sc_cuenta ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerSumaClasificador ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			while(!$data->EOF)  
			{ 
				$monto=$monto+round($data->fields["monto"],2); 
				$data->MoveNext();
			}
		}
		return $monto;
	}  // end function obtenerSumaClasificador
	

	public function obtenerSumaProveedorBeneficiarioAporte($comprobante,$codcomapo,$estctaalt)
	{
		$monto=0;
		if($estctaalt=='1')
		{
			$scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$scctaprov='rpc_proveedor.sc_cuenta';
			$scctaben='rpc_beneficiario.sc_cuenta';
		}
		$ls_sql="SELECT SUM(monto) as monto  ".
                "FROM sno_dt_scg, rpc_beneficiario ".
                "WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				"  AND sno_dt_scg.codcom='".$comprobante."' ".
				"  AND sno_dt_scg.codcomapo='".$codcomapo."' ".
				"  AND sno_dt_scg.debhab='H' ".
				"  AND sno_dt_scg.tipo_destino='B' ".
				"  AND sno_dt_scg.codemp = rpc_beneficiario.codemp ".
				"  AND sno_dt_scg.ced_bene = rpc_beneficiario.ced_bene ".
				"  AND sno_dt_scg.sc_cuenta = ".$scctaben." ".
				" UNION ".
				"SELECT SUM(monto) as monto ".
                "FROM sno_dt_scg, rpc_proveedor ".
                "WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				"  AND sno_dt_scg.codcom='".$comprobante."' ".
				"  AND sno_dt_scg.codcomapo='".$codcomapo."' ".
				"  AND sno_dt_scg.debhab='H' ".
				"  AND sno_dt_scg.tipo_destino='P' ".
				"  AND sno_dt_scg.codemp = rpc_proveedor.codemp ".
				"  AND sno_dt_scg.cod_pro = rpc_proveedor.cod_pro ".
				"  AND sno_dt_scg.sc_cuenta = ".$scctaprov." ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerSumaProveedorBeneficiarioAporte ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			while(!$data->EOF)  
			{ 
				$monto=$monto+round($data->fields["monto"],2); 
				$data->MoveNext();
			}
		}
		return $monto;
	}  // end function obtenerSumaProveedorBeneficiarioAporte
	
	
	public function obtenerTotalMonto($comprobante,$codcomapo,$tipo)
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$monto=0;
		switch($tipo)
		{
			case "A":
				$ls_sql="SELECT SUM(monto) as monto ".
						"FROM sno_dt_spg ".
						"WHERE codemp='".$this->codemp."' ".
						"  AND codcom='".$comprobante."' ".
						"  AND codcomapo='".$codcomapo."' ";
				break;

			case "L":
				$ls_sql="SELECT SUM(monto) as monto ".
						"FROM sno_dt_scg ".
						"INNER JOIN  (rpc_beneficiario ".
						"INNER JOIN sno_personal ".
						" ON sno_personal.codemp='".$this->codemp."' ".
						" AND sno_personal.codemp= rpc_beneficiario.codemp ".
						" AND sno_personal.cedper= rpc_beneficiario.ced_bene) ".
						" ON sno_dt_scg.codemp='".$this->codemp."' ".
						" AND sno_dt_scg.codcom='".$comprobante."'".
						" AND sno_dt_scg.codcomapo='".$codcomapo."'".
						" AND sno_dt_scg.codemp=sno_personal.codemp".
						" AND sno_dt_scg.codconc=sno_personal.codper".
						" AND sno_dt_scg.codemp=rpc_beneficiario.codemp".
						" AND sno_dt_scg.sc_cuenta=rpc_beneficiario.sc_cuenta";
				break;
				
			case "X":
				$ls_sql="SELECT SUM(monto) as monto ".
						"FROM sno_dt_scg ".
						"WHERE codemp='".$this->codemp."' ".
						"  AND codcom='".$comprobante."' ".
						"  AND debhab='H' ";
				break;

			default:
				$ls_sql="SELECT SUM(monto) as monto ".
						"  FROM sno_dt_spg ".
						" WHERE codemp='".$this->codemp."' ".
						"   AND codcom='".$comprobante."'";
				break;
		 }
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerTotalMonto ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
            while(!$data->EOF)
		    {
				$monto=$data->fields["monto"];
				break;		
			}	
		}
		return $monto;
	} // end uf_obtener_total_monto
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function buscarDetGasTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$codcomapo,$tipo,$causa)
	{
		$this->valido=true;	
		$criterio="";
		$arrDetGas=array();
		$i=1;	
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		if(($tipo=="A") || ($tipo=="L"))
		{
			$criterio=$criterio."	AND codcomapo='".$codcomapo."'";
		}		  
		$cadenaSql="SELECT codconc, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, codfuefin, monto  ".
				   "  FROM sno_dt_spg  ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codcom='".$comprobante."' ".
				   "   AND monto<>0 ".
				   $criterio;
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->buscarDetGasTipNom ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{   
			$total=0; 
			if (($tipo=="A") || ($tiponomina=="L"))
			{
				$comprobante = $codcomapo;
			}
			while((!$data->EOF) and ($this->valido))
			{
				$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
				$estcla=$data->fields["estcla"];
				$estructura[0]=$data->fields["codestpro1"];
				$estructura[1]=$data->fields["codestpro2"];
				$estructura[2]=$data->fields["codestpro3"];		
				$estructura[3]=$data->fields["codestpro4"];
				$estructura[4]=$data->fields["codestpro5"];
				$estructura[5]=$estcla;
				$spg_cuenta=$data->fields["spg_cuenta"];
				$monto=$data->fields["monto"];	
				$total=$total+$monto;			
				$documento=$data->fields["codconc"];
				if ($causa==1)
				{
					$documento=$comprobante;
				}								 
				$codfuefin=$data->fields["codfuefin"];								 
				$documento=str_pad($documento,15,"0",STR_PAD_LEFT);
				$status="";
				$denominacion="";
				$sc_cuenta="";
				$arreglo = $this->verificarCuentaContable($estructura,$spg_cuenta);
				if(!$this->valido)
				{
					$estructura=$estructura[0]."-".$estructura[1]."-".$estructura[2]."-".$estructura[3]."-".$estructura[4]."-".$estructura[5];
					$this->mensaje.="La Cuenta Presupuestaria ".$estructura."::".$spg_cuenta." no existe en el plan de cuenta.";			
					$this->valido=false;
				}
				if($this->valido)
				{
					$arrDetGas[$i]['codemp']=$this->codemp;
					$arrDetGas[$i]['numrecdoc']=$comprobante;
					if(($tipo=="A") || ($tipo=="L"))
					{
						$arrDetGas[$i]['numrecdoc']=$codcomapo;
					}		  
					$arrDetGas[$i]['codtipdoc']=$codtipdoc;
					$arrDetGas[$i]['ced_bene']=$ced_bene;
					$arrDetGas[$i]['cod_pro']=$cod_pro;
					$arrDetGas[$i]['procede_doc']='SNOCNO';
					$arrDetGas[$i]['numdoccom']=$documento;
					$arrDetGas[$i]['codestpro']=$codestpro;
					$arrDetGas[$i]['spg_cuenta']=$spg_cuenta;
					$arrDetGas[$i]['codfuefin']=$codfuefin;
					$arrDetGas[$i]['monto']=$monto;
					$arrDetGas[$i]['estcla']=$estcla;
				}
				$i++;
				$data->MoveNext();
			}
		}	 
		return $arrDetGas;
    } 
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
    public function buscarDetConTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$codcomapo,$tipo,$causa)
    {
    	$this->valido=true;	
		$arrDetCon=array();
		$criterio="";
		$i=1;	
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		if (($tipo=="A") || ($tipo=="L"))
		{
			$criterio=$criterio."	AND codcomapo='".$codcomapo."'";
		}		  
		$cadenaSql="SELECT codemp, codnom, codperi, codcom, tipnom, sc_cuenta, debhab, codconc, cod_pro, ced_bene, tipo_destino, ".
				   "		descripcion, monto, estatus, estrd, codtipdoc, estnumvou, estnotdeb, codcomapo ".
				   "  FROM sno_dt_scg ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codcom='".$comprobante."' ".
				   "   AND monto<>0 ".
				   $criterio;
    	$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->buscarDetConTipNom ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{           
			if (($tipo=="A") || ($tipo=="L"))
			{
				$comprobante = $codcomapo;
			}
			while((!$data->EOF) and ($this->valido))
			{
				$scg_cuenta = $data->fields["sc_cuenta"];
				$monto = $data->fields["monto"];				
				$debhab = $data->fields["debhab"];				
				$documento = $data->fields["codconc"];	
				if ($causa==1)
				{
					$documento=$comprobante;
				}								 											 
				$documento = str_pad($documento,15,"0",STR_PAD_LEFT);
				$arreglo = $this->selectCuentaSCG($scg_cuenta);
				if(!$this->valido)
				{
					$this->mensaje.="La cuenta contable ".trim($scg_cuenta)." no exite en el plan de cuenta.";			
				}
				if($this->valido)
				{
					$arrDetCon[$i]['codemp'] = $this->codemp;
					$arrDetCon[$i]['numrecdoc'] = $comprobante;
					if (($tipo=="A") || ($tipo=="L"))
					{
						$arrDetCon[$i]['numrecdoc'] = $codcomapo;
					}
					$arrDetCon[$i]['codtipdoc'] = $codtipdoc;
					$arrDetCon[$i]['ced_bene'] = $ced_bene;
					$arrDetCon[$i]['cod_pro'] = $cod_pro;
					$arrDetCon[$i]['procede_doc'] = 'SNOCNO';
					$arrDetCon[$i]['numdoccom'] = $documento;
					$arrDetCon[$i]['debhab'] = $debhab;
					$arrDetCon[$i]['sc_cuenta'] = $scg_cuenta;
					$arrDetCon[$i]['monto'] = $monto;
					$arrDetCon[$i]['estasicon'] = 'M';
					$arrDetCon[$i]['estgenasi'] = 0;
				}
				$i++;
				$data->MoveNext();
			}
		} 
		return $arrDetCon;
    }
    
    //-----------------------------------------------------------------------------------------------------------------------------------
	
	public function generarRecDocTipNom($comprobante,$fecha,$arrevento,$proceso)
	{
		$this->valido = true;
		$arrDetGasRd = array();
		$arrCabeceraRd = array();
		$arrDetConRd = array();
		$fechaconta = $fecha;
		$cod_pro = $this->daoDetalle->cod_pro;	
		$ced_bene = $this->daoDetalle->ced_bene;	
        $tipo_destino = $this->daoDetalle->tipo_destino;			
		$nomina = $this->daoDetalle->codnom; 
		$periodo = $this->daoDetalle->codperi;  
        $tipnom = $this->daoDetalle->tipnom;  
		$codtipdoc = $this->daoDetalle->codtipdoc;  
		$descripcion = $this->daoDetalle->descripcion; 
		$estctaalt = $this->daoDetalle->estctaalt;
		$codcla = $this->daoDetalle->codcla;
		$clactacon = $_SESSION["la_empresa"]["clactacon"];
		if($clactacon=='0')
		{
			if($tipnom=="N")
			{
				$monto=$this->obtenerSumaProveedorBeneficiario($comprobante,$estctaalt);
				if($monto<=0)
				{
					$this->mensaje.="La Cuenta Contable del Proveedor o Beneficiario, para la nomina no esta bien definida.";
					$this->valido=false;
				}
			}
			else
			{
				$monto=$this->obtenerTotalMonto($comprobante,"",$tipnom);
			}
		}
		else
		{
			$monto=$this->obtenerSumaClasificador($comprobante,'');
			if($monto<=0)
			{
				$this->mensaje.="La Cuenta Contable del Clasificador para la Nomina, no esta bien definida.";
				$this->valido=false;
			}
		}
		$codrecdoc="000000000000001";
		// datos de la cabecera
		if($this->valido)
		{
			$arrCabeceraRd['codemp'] = $this->codemp;
			$arrCabeceraRd['numrecdoc'] = $comprobante;
			$arrCabeceraRd['codtipdoc'] = $codtipdoc;
			$arrCabeceraRd['ced_bene'] = $ced_bene;
			$arrCabeceraRd['cod_pro'] = $cod_pro;
			$arrCabeceraRd['dencondoc'] = $descripcion;
			$arrCabeceraRd['fecemidoc'] = $fecha;
			$arrCabeceraRd['fecregdoc'] = $fecha;
			$arrCabeceraRd['fecvendoc'] = $fecha;
			$arrCabeceraRd['montotdoc'] = $monto;
			$arrCabeceraRd['mondeddoc'] = 0;
			$arrCabeceraRd['moncardoc'] = 0;
			$arrCabeceraRd['tipproben'] = $tipo_destino;
			$arrCabeceraRd['numref'] = $comprobante;
			$arrCabeceraRd['estprodoc'] = 'R';
			$arrCabeceraRd['procede'] = 'SNOCNO';
			$arrCabeceraRd['estlibcom'] = 0;
			$arrCabeceraRd['estaprord'] = 0;
			$arrCabeceraRd['fecaprord'] = '1900-01-01';
			$arrCabeceraRd['usuaprord'] = '';
			$arrCabeceraRd['estimpmun'] = 0;
			$arrCabeceraRd['codcla'] = $codcla;
			$arrCabeceraRd['codrecdoc'] = $codrecdoc;
			$arrCabeceraRd['repcajchi'] = '0';
			$arrCabeceraRd['tipdoctesnac'] = '0';
			if($proceso=='1')
			{
				$arrDetGasRd=$this->buscarDetGasTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,"",$tipnom,0);
				if($this->valido)
				{
					$arrDetConRd=$this->buscarDetConTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,"",$tipnom,0);
				}
				$serviciorecepcion = new ServicioRecepcion();
				$this->valido = $serviciorecepcion->guardarRecepcion($arrCabeceraRd,$arrDetGasRd,$arrDetConRd,null,null,$arrevento);
				$this->mensaje .= $serviciorecepcion->mensaje;
				unset($serviciorecepcion);
			}
			else if($proceso=='0')
			{
				$serviciorecepcion = new ServicioRecepcion();
				$this->valido = $serviciorecepcion->eliminarRecepcion($arrCabeceraRd,$arrevento);
				$this->mensaje .= $serviciorecepcion->mensaje;
				unset($serviciorecepcion);
				$fechaconta='1900-01-01';
			}
			if($this->valido)
			{
				$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipnom,$proceso,"",'conper',$fechaconta,'1900-01-01');
			}

		}
		$genrd=$this->SelectConfigNomina($nomina,'recdocpagperche');
		if($this->valido)
		{
			if(($genrd=="1")&&($tipnom=="N"))
			{
				$this->valido=$this->generarRecDocPerChe($this->daoDetalle->codnom,$periodo,$fecha,$descripcion,$proceso,$arrevento);
			}
			if(($this->valido)&&($tipnom=="N"))
			{
				$guarderia=trim($this->SelectConfig("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","I","0",$arrevento));				
				if($guarderia==1)
				{
					$this->valido = $this->procesarRecDocGuarderias($comprobante,$fecha,$arrevento,$proceso);
				}
			}	
		}
		return $this->valido;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function obtenerDataAportes($comprobante)
	{
		$this->valido=true;
		$arreglo=array();
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$ls_sql="SELECT codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc,estatus ".
                "FROM sno_dt_spg ".
                "WHERE codemp='".$this->codemp."' ".
				"  AND codcom='".$comprobante."'  ".
				"UNION ".
				"SELECT codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc,estatus ".
                "FROM sno_dt_scg ".
                "WHERE codemp='".$this->codemp."' ".
				"  AND codcom='".$comprobante."' ".
				"GROUP BY codcomapo,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc,estatus ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerDataAportes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{                 
			if(!$data->EOF)
			{
				$i=0;
				$total = $data->_numOfRows;
				$arreglo['total'][$i] = $total;
				$codtipdoc='-----';
				while(!$data->EOF){
					if($data->fields['codtipdoc']!='')
					{
						$codtipdoc=$data->fields['codtipdoc'];
					}
					$arreglo[$i]['codcomapo']=$data->fields['codcomapo'];
					$arreglo[$i]['cod_pro']=$data->fields['cod_pro'];
					$arreglo[$i]['ced_bene']=$data->fields['ced_bene'];
					$arreglo[$i]['tipo_destino']=$data->fields['tipo_destino'];
					$arreglo[$i]['descripcion']=$data->fields['descripcion'];
					$arreglo[$i]['codtipdoc']=$codtipdoc;
					$arreglo[$i]['estatus']=$data->fields['estatus'];
					$i++;
					$data->MoveNext();	
				}
			}
			else
			{
				$this->valido=false;
				$this->mensaje.="ERROR-> No hay data para el comprobante de aportes Nro ".$comprobante;			
			}
		}
		return $arreglo;
	}  // end function uf_obtener_data_aporte
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function obtenerDataAnticipos($comprobante)
	{
		$this->valido=true;
		$arreglo=array();
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();		
		$ls_sql="SELECT codcom, cod_pro, ced_bene, tipo_destino, descripcion, codtipdoc ".
                "FROM sno_dt_scg ".
                "WHERE sno_dt_scg.codemp='".$this->codemp."' ".
				"  AND sno_dt_scg.codcom='".$comprobante."'  ".
				"GROUP BY codcom,cod_pro,ced_bene,tipo_destino,descripcion,codtipdoc ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerDataAnticipos ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{      
			if(!$data->EOF)
			{
				$i=0;
				$total = $data->_numOfRows;
				$arreglo['total'][$i] = $total;
				$codtipdoc='-----';
				while(!$data->EOF){
					$arreglo[$i]['codcomapo']=$data->fields['codcomapo'];
					$arreglo[$i]['cod_pro']=$data->fields['cod_pro'];
					$arreglo[$i]['ced_bene']=$data->fields['ced_bene'];
					$arreglo[$i]['tipo_destino']=$data->fields['tipo_destino'];
					$arreglo[$i]['descripcion']=$data->fields['descripcion'];
					if($data->fields['codtipdoc']!=''){
						$codtipdoc=$data->fields['codtipdoc'];
					}
					$arreglo[$i]['codtipdoc']=$codtipdoc;
					$arreglo[$i]['estatus']=$data->fields['estatus'];
					$i++;
					$data->MoveNext();	
				}
			}
			else
			{
				$this->valido=false;
				$this->mensaje.="ERROR-> No hay data para el comprobante de aportes Nro ".$comprobante;			
			}
		}
		return $arreglo;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function obtenerDataLiquidacion($comprobante)
	{
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();	
		$arreglo=array();	
		$ls_sql="SELECT codcomapo,'----------' AS cod_pro, rpc_beneficiario.ced_bene, 'B' AS tipo_destino, 'LIQUIDACIÓN DE PERSONAL' AS descripcion, codtipdoc ".
                "  FROM sno_dt_spg ".
				" INNER JOIN  (sno_personal ".
				"       INNER JOIN rpc_beneficiario ".
				"          ON sno_personal.codemp='".$this->codemp."' ".
				"         AND sno_personal.codemp= rpc_beneficiario.codemp ".
				"         AND sno_personal.cedper= rpc_beneficiario.ced_bene) ".
                "    ON sno_dt_spg.codemp='".$this->codemp."' ".
				"   AND sno_dt_spg.codcom='".$comprobante."'".
				"   AND sno_dt_spg.codemp=sno_personal.codemp".
				"   AND sno_dt_spg.codconc=sno_personal.codper".
				" UNION ".
				"SELECT codcomapo,'----------' AS cod_pro,rpc_beneficiario.ced_bene, 'B' AS tipo_destino,'LIQUIDACIÓN DE PERSONAL' AS descripcion,codtipdoc ".
                "  FROM sno_dt_scg ".
				" INNER JOIN  (sno_personal ".
				"       INNER JOIN rpc_beneficiario ".
				"          ON sno_personal.codemp='".$this->codemp."' ".
				"         AND sno_personal.codemp= rpc_beneficiario.codemp ".
				"         AND sno_personal.cedper= rpc_beneficiario.ced_bene) ".
                "    ON sno_dt_scg.codemp='".$this->codemp."' ".
				"   AND sno_dt_scg.codcom='".$comprobante."'".
				"   AND sno_dt_scg.codemp=sno_personal.codemp".
				"   AND sno_dt_scg.codconc=sno_personal.codper".
				" GROUP BY codcomapo,cod_pro,rpc_beneficiario.ced_bene,tipo_destino,descripcion,codtipdoc ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->obtenerDataAportes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}	
		else
		{    
			if(!$data->EOF)
			{
				$i=0;
				$total = $data->_numOfRows;
				$arreglo['total'][$i] = $total;
				$codtipdoc='-----';
				while(!$data->EOF){
					$arreglo[$i]['codcomapo']=$data->fields['codcomapo'];
					$arreglo[$i]['cod_pro']=$data->fields['cod_pro'];
					$arreglo[$i]['ced_bene']=$data->fields['ced_bene'];
					$arreglo[$i]['tipo_destino']=$data->fields['tipo_destino'];
					$arreglo[$i]['descripcion']=$data->fields['descripcion'];
					if($data->fields['codtipdoc']!=''){
						$codtipdoc=$data->fields['codtipdoc'];
					}
					$arreglo[$i]['codtipdoc']=$codtipdoc;
					$arreglo[$i]['estatus']=$data->fields['estatus'];
					$i++;
					$data->MoveNext();	
				}
			}
			else
			{
				$this->valido=false;
				$this->mensaje.="ERROR-> No hay data para el comprobante de aportes Nro ".$comprobante;			
			}
		}
		return $arreglo;
	}  // end function uf_obtener_data_liquidacion
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function contabilizacionTipApor($comprobante,$fecha,$nomina,$periodo,$arrevento,$proceso)
	{
		$this->valido=true;
		$fechaconta=$fecha;
		$arreglo=$this->obtenerDataAportes($comprobante);
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$tipo=$this->daoDetalle->tipnom;
		if($this->valido)
		{		
			$total=$arreglo['total'][0];
			// Recorremos todos los comprobantes de los aportes
		    for($i=0;($i<$total)&&($this->valido);$i++ )
			{      
				$ls_comprobante = $arreglo[$i]['codcomapo'];	
				$ls_comprobante = fillComprobante($ls_comprobante);			
				$cod_pro = $arreglo[$i]['cod_pro'];	
				$ced_bene = $arreglo[$i]['ced_bene'];	
				$descripcion = $arreglo[$i]['descripcion'];	
				$tipo_destino = $arreglo[$i]['tipo_destino'];			
				$estatus = $arreglo[$i]['estatus'];
				$monto = $this->obtenerSumaProveedorBeneficiarioAporte($comprobante,$ls_comprobante,$tipo);
				if ($estatus==1 && $proceso=='1') 
				{
				   $this->mensaje.="ERROR-> La Nomina debe estar en estatus EMITIDA para su contabilizacion.";
				   $this->valido=false;
				}
				// Creamos la Cabecera del Comprobante
				if($this->valido)
				{
					$arrcabecera['codemp'] = $this->codemp;
					$arrcabecera['procede'] = 'SNOCNO';
					$arrcabecera['comprobante'] = $ls_comprobante;
					$arrcabecera['codban'] = '---';
					$arrcabecera['ctaban'] = '-------------------------';
					$arrcabecera['fecha'] = $fecha;
					$arrcabecera['descripcion'] = $descripcion;
					$arrcabecera['tipo_comp'] = 1;
					$arrcabecera['tipo_destino'] = $tipo_destino;
					$arrcabecera['cod_pro'] = $cod_pro;
					$arrcabecera['ced_bene'] = $ced_bene;
					$arrcabecera['total'] = $monto;
					$arrcabecera['numpolcon'] = 0;
					$arrcabecera['esttrfcmp'] = 0;
					$arrcabecera['estrenfon'] = 0;
					$arrcabecera['codfuefin'] = $this->daoDetalle->codfuefin;
					$arrcabecera['codusu'] = $_SESSION['la_logusr'];
					if($proceso=='1')
					{
						$arrdetallespg=$this->buscarDetalleGasto($comprobante,$tipo,$ls_comprobante,$arrcabecera);
						if($this->valido)
						{
							$arrdetallescg=$this->buscarDetalleContable($comprobante,$tipo,$ls_comprobante,$arrcabecera);
						}
						$serviciocomprobante = new ServicioComprobante();
						$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,null,$arrevento);
						$this->mensaje .= $serviciocomprobante->mensaje;
						unset($serviciocomprobante);
					}
					elseif($proceso=='0')
					{
						$serviciocomprobante = new ServicioComprobante();
						$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
						$this->mensaje .= $serviciocomprobante->mensaje;
						unset($serviciocomprobante);
						$fechaconta='1900-01-01';
					}
					if($this->valido){
						$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipo,$proceso,$ls_comprobante,'apoconper',$fechaconta,'1900-01-01');
					}
				}
			}
		}
		return $this->valido;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function generarRecDocTipApor($comprobante,$fecha,$nomina,$periodo,$arrevento,$proceso)
	{
		$this->valido=true;
		$fechaconta=$fecha;
		$arreglo=$this->obtenerDataAportes($comprobante);
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$tipo=$this->daoDetalle->tipnom;
		$codcla = $this->daoDetalle->codcla;
		$clactacon = $_SESSION["la_empresa"]["clactacon"];		
		$estctaalt = $this->daoDetalle->estctaalt;
		if($this->valido)
		{
			$total=$arreglo['total'][0];
 			for($i=0;($i<$total)&&($this->valido);$i++)
 			{
			    $this->valido = true; 			
				$ls_comprobante = $arreglo[$i]['codcomapo'];	
				$ls_comprobante = fillComprobante($ls_comprobante);			
				$cod_pro = $arreglo[$i]['cod_pro'];	
				$ced_bene = $arreglo[$i]['ced_bene'];	
				$descripcion = $arreglo[$i]['descripcion'];	
				$tipo_destino = $arreglo[$i]['tipo_destino'];	
				$codtipdoc = $arreglo[$i]['codtipdoc'];
				if($clactacon=='0')
				{
					$monto = $this->obtenerSumaProveedorBeneficiarioAporte($comprobante,$ls_comprobante,$estctaalt);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Proveedor o Beneficiario, para los aportes no esta bien definida.";
						$this->valido=false;
					}
				}
				else
				{
					$monto = $this->obtenerSumaClasificador($comprobante,$ls_comprobante);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Clasificador para los aportes, no esta bien definida.";
						$this->valido=false;
					}
				}
				if($this->valido)
				{
					$arrcabecera['codemp'] = $this->codemp;
					$arrcabecera['numrecdoc'] = $ls_comprobante;
					$arrcabecera['codtipdoc'] = $codtipdoc;
					$arrcabecera['ced_bene'] = $ced_bene;
					$arrcabecera['cod_pro'] = $cod_pro;
					$arrcabecera['dencondoc'] = $descripcion;
					$arrcabecera['fecemidoc'] = $fecha;
					$arrcabecera['fecregdoc'] = $fecha;
					$arrcabecera['fecvendoc'] = $fecha;
					$arrcabecera['montotdoc'] = $monto;
					$arrcabecera['moncardoc'] = 0;
					$arrcabecera['mondeddoc'] = 0;
					$arrcabecera['tipproben'] = $tipo_destino;
					$arrcabecera['numref'] = $comprobante;
					$arrcabecera['estprodoc'] = 'R';
					$arrcabecera['procede'] = 'SNOCNO';
					$arrcabecera['estlibcom'] = 0;
					$arrcabecera['estaprord'] = 0;
					$arrcabecera['fecaprord'] = '1900-01-01';
					$arrcabecera['estimpmun'] = 0;
					$arrcabecera['codcla'] = $codcla;
					$arrcabecera['codrecdoc'] = '000000000000001';
					$arrcabecera['usuaprord'] = $_SESSION['la_logusr'];
					$arrcabecera['repcajchi'] = '0';
					$arrcabecera['tipdoctesnac'] = '0';
					if($proceso=='1')
					{
						$arrdetallespg=$this->buscarDetGasTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_comprobante,$tipo,0);
						if($this->valido)
						{
							$arrdetallescg=$this->buscarDetConTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_comprobante,$tipo,0);
						}
						$serviciorecepcion = new ServicioRecepcion();
						$this->valido = $serviciorecepcion->guardarRecepcion($arrcabecera,$arrdetallespg,$arrdetallescg,null,null,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
					}
					if($proceso=='0')
					{
						$serviciorecepcion = new ServicioRecepcion();
						$this->valido = $serviciorecepcion->eliminarRecepcion($arrcabecera,$arrevento);
						$this->mensaje .= $serviciorecepcion->mensaje;
						unset($serviciorecepcion);
						$fechaconta='1900-01-01';
					}
					if($this->valido)
					{
						$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipo,$proceso,$ls_comprobante,'apoconper',$fechaconta,'1900-01-01');
					}
				}
			}
		} 		
		return $this->valido;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function contabilizacionTipIng($comprobante,$fecha,$arrevento,$proceso)
	{
		$this->valido=true;
		$fechaconta=$fecha;
		$arrcabecera=array();
		$arrdetallespi=array();
		$arrdetallescg=array();
		$cod_pro=$this->daoDetalle->cod_pro;	
		$ced_bene=$this->daoDetalle->ced_bene;	
		$descripcion=$this->daoDetalle->descripcion;	
        $tipo_destino=$this->daoDetalle->tipo_destino;			
        $mensaje=$this->daoDetalle->operacion;
		$estatus=$this->daoDetalle->estatus;
		$nomina=$this->daoDetalle->codnom; 
		$periodo=$this->daoDetalle->codperi;  
        $tipnom=$this->daoDetalle->tipnom;  
        $estnotdeb=$this->daoDetalle->estnotdeb; 
        $monto=$this->daoDetalle->monto;
		if($estatus==1 && $proceso=='1') 
		{
		   $this->mensaje.="La Nomina debe estar en estatus EMITIDA para su contabilizacion.";
		   $this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SNOCNO';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $tipo_destino;
			$arrcabecera['cod_pro'] = $cod_pro;
			$arrcabecera['ced_bene'] = $ced_bene;
			$arrcabecera['total'] = $monto;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoDetalle->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			if($proceso=='1'){
				$arrdetallespi=$this->buscarDetalleIngreso($comprobante,$arrcabecera);
				if($this->valido){
					$arrdetallescg=$this->buscarDetalleContable($comprobante,$tipnom,"",$arrcabecera);
				}
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,$arrdetallespi,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
			}
			if($proceso=='0'){
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
				$fechaconta='1900-01-01';
			}
			if($this->valido){
				$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipnom,$proceso,"",'ingconper',$fechaconta,'1900-01-01');
			}
		}	
		return  $this->valido;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    
	public function contabilizacionTipInt($comprobante,$fecha,$arrevento,$proceso)
	{
		$fechaconta=$fecha;
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$cod_pro=$this->daoDetalle->cod_pro;	
		$ced_bene=$this->daoDetalle->ced_bene;	
		$descripcion=$this->daoDetalle->descripcion;	
        $tipo_destino=$this->daoDetalle->tipo_destino;			
        $mensaje=$this->daoDetalle->operacion;
		$estatus=$this->daoDetalle->estatus;
		$nomina=$this->daoDetalle->codnom; 
		$periodo=$this->daoDetalle->codperi;  
        $tipnom=$this->daoDetalle->tipnom;  
        $estnotdeb=$this->daoDetalle->estnotdeb; 
        $monto=$this->daoDetalle->monto;
		if($estatus==1 && $proceso=='1') 
		{
		   $this->mensaje.="La Nomina debe estar en estatus EMITIDA para su contabilizacion.";
		   $this->valido=false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SNOCNO';
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $tipo_destino;
			$arrcabecera['cod_pro'] = $cod_pro;
			$arrcabecera['ced_bene'] = $ced_bene;
			$arrcabecera['total'] = $monto;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $this->daoDetalle->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			if($proceso=='1')
			{
				$arrdetallespg=$this->buscarDetalleGasto($comprobante,$tipnom,"",$arrcabecera);
				if($this->valido)
				{
					// Se procesan los detalles de presupuesto
					$arrdetallescg=$this->buscarDetalleContable($comprobante,$tipnom,"",$arrcabecera);  
				}
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,$arrdetallespi,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
			}
			if($proceso=='0')
			{
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
				$fechaconta='1900-01-01';
			}
			if ($this->valido)
	        {
	        	$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipnom,$proceso,"",'fidintconper',$fechaconta,'1900-01-01');
			}
		}
		return $this->valido;
	} // end function uf_procesar_contabilizacion_tipo_intereses
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function contabilizacionTipLiq($comprobante,$fecha,$nomina,$periodo,$arrevento,$proceso)
	{
		$fechaconta=$fecha;
		$this->valido=true;
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$arreglo=$this->obtenerDataLiquidacion($comprobante); 
		$tipo=$this->daoDetalle->tipnom;
		$codcla = $this->daoDetalle->codcla;
		$clactacon = $_SESSION["la_empresa"]["clactacon"];		
	    if($this->valido)
		{
			$total=$arreglo['total'][0];
			for($i=0;($i<$total)&&($this->valido);$i++)
			{
			    $this->valido = true;       
				$ls_comprobante = $arreglo[$i]['codcomapo'];	
				$ls_comprobante = fillComprobante($ls_comprobante);
				$cod_pro = $arreglo[$i]['cod_pro'];
				$ced_bene = $arreglo[$i]['ced_bene'];	
				$tipo_destino = $arreglo[$i]['tipo_destino'];			
				$descripcion = $arreglo[$i]['descripcion'];				
				$codtipdoc = $arreglo[$i]['codtipdoc'];							
				if($clactacon=='0')
				{
					$monto = $this->obtenerTotalMonto($comprobante,$ls_comprobante,$tipo);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Proveedor o Beneficiario, para la Liquidacion no esta bien definida.";
						$this->valido=false;
					}
				}
				else
				{
					$monto = $this->obtenerSumaClasificador($comprobante,$ls_comprobante);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Clasificador para la liquidacion, no esta bien definida.";
						$this->valido=false;
					}
				}
				$arrcabecera['codemp'] = $this->codemp;
				$arrcabecera['numrecdoc'] = $ls_comprobante;
				$arrcabecera['codtipdoc'] = $codtipdoc;
				$arrcabecera['ced_bene'] = $ced_bene;
				$arrcabecera['cod_pro'] = $cod_pro;
				$arrcabecera['dencondoc'] = $descripcion;
				$arrcabecera['fecemidoc'] = $fecha;
				$arrcabecera['fecregdoc'] = $fecha;
				$arrcabecera['fecvendoc'] = $fecha;
				$arrcabecera['montotdoc'] = $monto;
				$arrcabecera['moncardoc'] = 0;
				$arrcabecera['mondeddoc'] = 0;
				$arrcabecera['tipproben'] = $tipo_destino;
				$arrcabecera['numref'] = $comprobante;
				$arrcabecera['estprodoc'] = 'R';
				$arrcabecera['procede'] = 'SNOCNO';
				$arrcabecera['estlibcom'] = 0;
				$arrcabecera['estaprord'] = 0;
				$arrcabecera['fecaprord'] = '1900-01-01';
				$arrcabecera['estimpmun'] = 0;
				$arrcabecera['codcla'] = $codcla;
				$arrcabecera['codrecdoc'] = '000000000000001';
				$arrcabecera['usuaprord'] = $_SESSION['la_logusr'];
				$arrcabecera['repcajchi'] = '0';
				$arrcabecera['tipdoctesnac'] = '0';
				if($proceso=='1'){
					$arrdetallespg=$this->buscarDetGasTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_comprobante,$tipo,0);
					if($this->valido)
					{	// Insertar los detalles de Presupuesto
						$arrdetallescg=$this->buscarDetConTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_comprobante,$tipo,0);
					}
					$serviciorecepcion = new ServicioRecepcion();
					$this->valido = $serviciorecepcion->guardarRecepcion($arrcabecera,$arrdetallespg,$arrdetallescg,null,null,$arrevento);
					$this->mensaje .= $serviciorecepcion->mensaje;
					unset($serviciorecepcion);
				}
				if($proceso=='0'){
					$serviciorecepcion = new ServicioRecepcion();
					$this->valido = $serviciorecepcion->eliminarRecepcion($arrcabecera,$arrevento);
					$this->mensaje .= $serviciorecepcion->mensaje;
					unset($serviciorecepcion);
					$fechaconta='1900-01-01';
				}
		        if($this->valido)
				{	// Actualizar el estatus de la nómina
					$this->valido=$this->actualizarEstatusFechaNomina($comprobante,"L",$proceso,$ls_comprobante,'conper',$fechaconta,'1900-01-01');
				}
			}
		} 
		return $this->valido;
	}
	
	public function contabilizacionTipAnt($comprobante,$fecha,$nomina,$periodo,$arrevento,$proceso)
	{
		$this->valido=true;
		$fechaconta=$fecha;
		$arreglo=$this->obtenerDataAnticipos($comprobante);
		$tipo=$this->daoDetalle->tipnom;
		$arrcabecera=array();
		$arrdetallespg=array();
		$arrdetallescg=array();
		$codcla = $this->daoDetalle->codcla;
		$clactacon = $_SESSION["la_empresa"]["clactacon"];		
	    if($this->valido)
		{
			$total=$arreglo['total'][0]; 	
		    for ($i=0;($i<$total)&&($this->valido);$i++)
			{
			    $this->valido = true;       
				$ls_comprobante = $arreglo[$i]['codcom'];	
				$ls_comprobante = fillComprobante($ls_comprobante);
				$cod_pro = $arreglo[$i]['cod_pro'];
				$ced_bene = $arreglo[$i]['ced_bene'];	
				$tipo_destino = $arreglo[$i]['tipo_destino'];	
				$descripcion = $arreglo[$i]['descripcion'];				
				$codtipdoc = $arreglo[$i]['codtipdoc'];							
				if($clactacon=='0')
				{
					$monto = $this->obtenerTotalMonto($comprobante,$ls_comprobante,$tipo);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Proveedor o Beneficiario, para el Anticipo no esta bien definida.";
						$this->valido=false;
					}
				}
				else
				{
					$monto = $this->obtenerSumaClasificador($comprobante,$ls_comprobante);
					if($monto<=0)
					{
						$this->mensaje.="La Cuenta Contable del Clasificador para el anticipo, no esta bien definida.";
						$this->valido=false;
					}
				}
				// Crear la cabecera de la recepción de documento
				$arrcabecera['codemp'] = $this->codemp;
				$arrcabecera['numrecdoc'] = $comprobante;
				$arrcabecera['codtipdoc'] = $codtipdoc;
				$arrcabecera['ced_bene'] = $ced_bene;
				$arrcabecera['cod_pro'] = $cod_pro;
				$arrcabecera['dencondoc'] = $descripcion;
				$arrcabecera['fecemidoc'] = $fecha;
				$arrcabecera['fecregdoc'] = $fecha;
				$arrcabecera['fecvendoc'] = $fecha;
				$arrcabecera['montotdoc'] = $monto;
				$arrcabecera['moncardoc'] = 0;
				$arrcabecera['mondeddoc'] = 0;
				$arrcabecera['tipproben'] = $tipo_destino;
				$arrcabecera['numref'] = $comprobante;
				$arrcabecera['estprodoc'] = 'R';
				$arrcabecera['procede'] = 'SNOCNO';
				$arrcabecera['estlibcom'] = 0;
				$arrcabecera['estaprord'] = 0;
				$arrcabecera['fecaprord'] = '1900-01-01';
				$arrcabecera['estimpmun'] = 0;
				$arrcabecera['codcla'] = $codcla;
				$arrcabecera['codrecdoc'] = '000000000000001';
				$arrcabecera['usuaprord'] = $_SESSION['la_logusr'];
				$arrcabecera['repcajchi'] = '0';
				$arrcabecera['tipdoctesnac'] = '0';
				if($proceso=='1')
				{
					$arrdetallespg=$this->buscarDetGasTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_comprobante,$tipo,0);
		           	if($this->valido)
					{	// Insertar los detalles de Presupuesto
						$arrdetallescg=$this->buscarDetConTipNom($comprobante,$codtipdoc,$ced_bene,$cod_pro,$ls_codcomapo,$tipo,0);
					}
					$serviciorecepcion = new ServicioRecepcion();
					$this->valido = $serviciorecepcion->guardarRecepcion($arrcabecera,$arrdetallespg,$arrdetallescg,null,null,$arrevento);
					$this->mensaje .= $serviciorecepcion->mensaje;
					unset($serviciorecepcion);
				}
				if($proceso=='0'){
					$serviciorecepcion = new ServicioRecepcion();
					$this->valido = $serviciorecepcion->eliminarRecepcion($arrcabecera,$arrevento);
					$this->mensaje .= $serviciorecepcion->mensaje;
					unset($serviciorecepcion);
					$fechaconta='1900-01-01';
				}
		        if($this->valido)
				{	// Actualizar el estatus de la nómina
					$this->valido=$this->actualizarEstatusFechaNomina($comprobante,$tipo,$proceso,$ls_comprobante,'',$fechaconta,'1900-01-01');
				}
			}
		}   
		return $this->valido;
	}
	
	
	public function procesoContabilizarSNO($objson) 
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$fecha = convertirFechaBd($objson->feccon);
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
			$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
			$arrevento['desevetra'] = "Contabilizaci&#243;n de la N&#243;mina de Activo {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->ContabilizarSNO($comprobante,$objson,$arrevento,$fecha)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'N&#243;mina contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Nomina no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje='';
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	
	public function ContabilizarSNO($comprobante,$objson,$arrevento,$fecha)
	{
		DaoGenerico::iniciarTrans();
		$servicioEvento = new ServicioEvento();
		$i=0;
		
		// OBTENGO LA NOMINA Y DETALLE A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 0 ";
		$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_spg','',$criterio);
		if($this->daoDetalle->codemp=='')
		{
			$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 0 ";
			$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_spi','',$criterio);
			if($this->daoDetalle->codemp=='')
			{
				$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 0 ";
				$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_scg','',$criterio);
			}			
		}
		if($this->daoDetalle->codemp=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Nomina '.$comprobante.'.';
			$this->valido = false;			
		}		
		if($this->valido)
		{
			switch($this->daoDetalle->tipnom)
			{
				case 'N': // // Si la contabilización es para las nóminas
					if($this->daoDetalle->estrd==0)
					{// Si la contabilización es Normal
						$this->valido = $this->contabilizacionTipNom($comprobante,$fecha,$arrevento,'1');
					}
					else
					{// Si se genera una recepción de documentos
						$this->valido = $this->generarRecDocTipNom($comprobante,$fecha,$arrevento,'1');
					}
				break;
				case "A":
					// Si la contabilización es para los aportes
					if($this->daoDetalle->estrd==0)
					{	// Si la contabilización es Normal
						$this->valido=$this->contabilizacionTipApor($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'1');
					}
					else
					{	// Si se genera una recepción de documentos
						$this->valido=$this->generarRecDocTipApor($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'1');
					}
				break;
				case "I":
					// Si la contabilización es para los ingresos
					$this->valido=$this->contabilizacionTipIng($comprobante,$fecha,$arrevento,'1');
				break;
				case "P":
					// Si la contabilización es para la Prestación Antiguedad
					if($this->daoDetalle->estrd==0)
					{	// Si la contabilización es Normal
						$this->valido=$this->contabilizacionTipNom($comprobante,$fecha,$arrevento,'1');
					}
					else
					{	// Si se genera una recepción de documentos
						$this->valido = $this->generarRecDocTipNom($comprobante,$fecha,$arrevento,'1');
					}
				break;
				case "K":
					// Si la contabilización es para los intereses de Prestación Antiguedad
					if($this->daoDetalle->estrd==0)
					{	// Si la contabilización es Normal
						$this->valido=$this->contabilizacionTipInt($comprobante,$fecha,$arrevento,'1');
					}
					else
					{	// Si se genera una recepción de documentos
						$this->valido = $this->generarRecDocTipNom($comprobante,$fecha,$arrevento,'1');
					}
				break;
				case "L":
					// Si la contabilización es para las nóminas de liquidacion
					$this->valido=$this->contabilizacionTipLiq($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'1');
				break;
				case "X":
					// Si la contabilización es para los anticipos
					$this->valido=$this->contabilizacionTipAnt($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'1');
				break;
			}
		}
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
	
    public function validarPagDir($banco,$ctabanco,$comprobante)
	{
		$estmov="";
		$cadenasql="SELECT estmov  ".
				   "  FROM scb_movbco  ".
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND codban='".$banco."' ".
				   "   AND ctaban='".$ctabanco."'  ".
				   "   AND numdoc='".$comprobante."' ".
				   "   AND codope='CH'";
		$data = $this->conexionBaseDatos->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido=false;
		}
		else
		{
			if(!$data->EOF)
			{
				$estmov=$data->fields["estmov"];
			}
		}
		return $estmov;
	}	
	
	
	public function eliminarPagDirPerChe($nomina,$periodo,$banco,$ctabanco,$comprobante)
	{
		$this->valido=true;
		$cod_pro="----------";
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		// Eliminamos los Detalles Contables
		$ls_sql="DELETE  ".
				"FROM scb_movbco_scg  ".
				"WHERE codemp='".$this->codemp."'  ".
				"   AND codban='".$banco."' ".
				"   AND ctaban='".$ctabanco."'  ".
				"   AND numdoc='".$comprobante."'  ".
				"   AND codope='CH' ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->eliminarRecDocPagDirPerChe ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido=false;
		}
		unset($data);
		if ($this->valido)
		{
			// Eliminamos los Detalles Presupuestarios
			$ls_sql="DELETE ".
					" FROM scb_movbco_fuefinanciamiento ".
					"WHERE codemp='".$this->codemp."' ".
					"  AND codban='".$banco."' ".
					"  AND ctaban='".$ctabanco."'".
					"  AND numdoc='".$comprobante."'".
					"  AND codope='CH'";
			$data = $this->conexionBaseDatos->Execute($ls_sql);
			if($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->eliminarRecDocPagDirPerChe ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido=false;
			}
		}
		unset($data);
		if ($this->valido)
		{
			// Eliminamos los Detalles Presupuestarios
			$ls_sql="DELETE ".
					"FROM scb_movbco ".
					"WHERE codemp='".$this->codemp."' ".
					"  AND codban='".$banco."' ".
					"  AND ctaban='".$ctabanco."'".
					"  AND numdoc='".$comprobante."'".
					"  AND codope='CH'";
			$data = $this->conexionBaseDatos->Execute($ls_sql);
			if($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SNO METODO->eliminarRecDocPagDirPerChe ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido=false;
			}
		}
		return $this->valido;
	}
	
    //-----------------------------------------------------------------------------------------------------------------------------------
   
	public function procesoRevContabilizarSNO($objson) 
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
			$comprobante=fillComprobante($objson->arrDetalle[$j]->codcom);
			$arrevento['desevetra'] = "Contabilizo la N&#243;mina de Activo {$comprobante}, asociado a la empresa {$this->codemp}";
			if ($this->RevContabilizarSNO($comprobante,$objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'N&#243;mina reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Nomina no fue reversada, {$this->mensaje} ";
			}
			$h++;
			$this->mensaje = "";
			$this->valido = true;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	public function RevContabilizarSNO($comprobante,$objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$servicioEvento = new ServicioEvento();
		$fecha = $objson->arrDetalle[$j]->fechaconta;
		$fecha = convertirFechaBd($fecha);
		$i=0;
		
		// OBTENGO LA NOMINA Y DETALLE A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 1 ";
		$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_spg','',$criterio);
		if($this->daoDetalle->codemp=='')
		{
			$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 1 ";
			$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_spi','',$criterio);
			if($this->daoDetalle->codemp=='')
			{
				$criterio="codemp = '".$this->codemp."' AND codcom='".$comprobante."' AND estatus = 1 ";
				$this->daoDetalle = FabricaDao::CrearDAO('C','sno_dt_scg','',$criterio);
			}			
		}
		switch($this->daoDetalle->tipnom)
		{
			case 'N': // // Si la contabilización es para las nóminas
				if($this->daoDetalle->estrd==0)
				{// Si la contabilización es Normal
					$this->valido = $this->contabilizacionTipNom($comprobante,$fecha,$arrevento,'0'); //0 de reversar
				}
				else
				{// Si se genera una recepción de documentos
					$this->valido = $this->generarRecDocTipNom($comprobante,$fecha,$arrevento,'0'); //0 de reversar
				}
			break;
			case "A":
				// Si la contabilización es para los aportes
				if($this->daoDetalle->estrd==0)
				{	// Si la contabilización es Normal
					$this->valido=$this->contabilizacionTipApor($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'0'); //0 de reversar
				}
				else
				{	// Si se genera una recepción de documentos
					$this->valido=$this->generarRecDocTipApor($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'0');  //0 de reversar
				}
			break;
			case "I":
				// Si la contabilización es para los ingresos
				$this->valido=$this->contabilizacionTipIng($comprobante,$fecha,$arrevento,'0');//0 de reversar
			break;
			case "P":
				// Si la contabilización es para la Prestación Antiguedad
				if($this->daoDetalle->estrd==0)
				{	// Si la contabilización es Normal
					$this->valido = $this->contabilizacionTipNom($comprobante,$fecha,$arrevento,'0');//0 de reversar
				}
				else
				{	// Si se genera una recepción de documentos
					$this->valido = $this->generarRecDocTipNom($comprobante,$fecha,$arrevento,'0');//0 de reversar
				}
			break;
			case "K":
				// Si la contabilización es para los intereses de Prestación Antiguedad
				$this->valido=$this->contabilizacionTipInt($comprobante,$fecha,$arrevento,'0');//0 de reversar
			break;
			case "L":
				// Si la contabilización es para las nóminas de liquidacion
				$this->valido=$this->contabilizacionTipLiq($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'0');
			break;
			case "X":
				// Si la contabilización es para los anticipos
				$this->valido=$this->contabilizacionTipAnt($comprobante,$fecha,$this->daoDetalle->codnom,$this->daoDetalle->codperi,$arrevento,'0');
			break;
		}
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
	
    //-----------------------------------------------------------------------------------------------------------------------------------
    
	public function buscarDetallePresupuesto($codcom,$codcomapo)
	{
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ai_len2=$_SESSION["la_empresa"]["loncodestpro2"];
		$ai_len3=$_SESSION["la_empresa"]["loncodestpro3"];
		$ai_len4=$_SESSION["la_empresa"]["loncodestpro4"];
		$ai_len5=$_SESSION["la_empresa"]["loncodestpro5"];
		$criterio="";
		switch(substr($codcom,14,1))
		{
			case "A": // Aportes
				$criterio = " AND codcomapo = '".$codcomapo."'";
				break;
			case "L": // Liquidacion
				$criterio = " AND codcomapo = '".$codcomapo."'";
				break;
		}
		if((substr($codcom,14,1)=="N")||(substr($codcom,14,1)=="P")||(substr($codcom,14,1)=="K")||(substr($codcom,14,1)=="A")||(substr($codcom,14,1)=="L")||(substr($codcom,14,1)=="X"))
		{
			switch($ls_modalidad)
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(sno_dt_spg.codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sno_dt_spg.codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sno_dt_spg.codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(sno_dt_spg.codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sno_dt_spg.codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sno_dt_spg.codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(sno_dt_spg.codestpro4,25-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(sno_dt_spg.codestpro5,25-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			 
			$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , sno_dt_spg.estcla, sno_dt_spg.spg_cuenta, operacion, monto, 0 AS disponibilidad, ".
						 "		 sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5,  ".
						 "       spg_cuentas.denominacion, ".
						 "       (SELECT MAX(fechasper) FROM sno_hperiodo ".
						 "         WHERE sno_hperiodo.codemp = sno_dt_spg.codemp ".
						 "           AND sno_hperiodo.codnom = sno_dt_spg.codnom ".
						 "           AND sno_hperiodo.codperi = sno_dt_spg.codperi) AS fechasper ". 
						 "  FROM sno_dt_spg ".
						 " INNER JOIN spg_cuentas ". 
						 "    ON sno_dt_spg.codemp = spg_cuentas.codemp ". 
						 "   AND sno_dt_spg.codestpro1 = spg_cuentas.codestpro1 ". 
						 "   AND sno_dt_spg.codestpro2 = spg_cuentas.codestpro2 ". 
						 "   AND sno_dt_spg.codestpro3 = spg_cuentas.codestpro3 ". 
						 "   AND sno_dt_spg.codestpro4 = spg_cuentas.codestpro4 ". 
						 "   AND sno_dt_spg.codestpro5 = spg_cuentas.codestpro5 ". 
						 "   AND sno_dt_spg.estcla = spg_cuentas.estcla ". 
						 "   AND sno_dt_spg.spg_cuenta = spg_cuentas.spg_cuenta ". 
						 " WHERE sno_dt_spg.codemp='".$this->codemp."' ".
						 "   AND codcom='".$codcom."' ".
							$criterio;
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
			
			$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura , estcla, siv_dt_scg.spi_cuenta, operacion, monto, 0 AS disponibilidad, ".
						 "		 codestpro1,codestpro2,codestpro3,codestpro4,codestpro5, spi_cuentas.denominacion,  ".
						 "       (SELECT MAX(fechasper) FROM sno_hperiodo ".
						 "         WHERE sno_hperiodo.codemp = sno_dt_spi.codemp ".
						 "           AND sno_hperiodo.codnom = sno_dt_spi.codnom ".
						 "           AND sno_hperiodo.codperi = sno_dt_spi.codperi) AS fechasper ". 
						 "  FROM sno_dt_spi ".
					     " INNER JOIN spi_cuentas ". 
					     "    ON siv_dt_scg.codemp = spi_cuentas.codemp ". 
					     "   AND siv_dt_scg.spi_cuenta = spi_cuentas.spi_cuenta ". 
						 " WHERE sno_dt_spi.codemp='".$this->codemp."' ".
						 "   AND codcom='".$codcom."' ".
						$criterio;
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function buscarInformacionDetalle($codcom,$codcomapo,$fecha)
	{
		$arrDisponible = array();
		$j = 0;
		$fecha = convertirFechaBd($fecha);
		$dataCuentas = $this->buscarDetallePresupuesto($codcom,$codcomapo);
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
			$_SESSION['fechacomprobante'] =  $fecha;
			$this->servicioComprobante->setDaoDetalleSpg($arrdetallespg);
			$this->servicioComprobante->saldoSelect();
			$disponibilidad =  (($this->servicioComprobante->asignado + $this->servicioComprobante->aumento) - ( $this->servicioComprobante->disminucion + $this->servicioComprobante->comprometido + $this->servicioComprobante->precomprometido));
			if(round($dataCuentas->fields['monto'],2) <= round($disponibilidad,2))
			{
				$disponible = 1;
			}
			$arrDisponible[$j]['estructura']     = $dataCuentas->fields['estructura'];
			$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
			$arrDisponible[$j]['operacion']      = $dataCuentas->fields['operacion'];
			$arrDisponible[$j]['spg_cuenta']     = $dataCuentas->fields['spg_cuenta'];
			$arrDisponible[$j]['monto']          = $dataCuentas->fields['monto'];
			$arrDisponible[$j]['denominacion']   = utf8_encode($dataCuentas->fields['denominacion']);
			$arrDisponible[$j]['disponibilidad'] = $disponible;
			
			unset($this->servicioComprobante);
			$j++;
			$dataCuentas->MoveNext();
		}
		unset($dataCuentas);
		return $arrDisponible;
	}
	
	public function detalleContable($codcom,$codcomapo)
	{
		$criterio="";
		switch(substr($codcom,14,1))
		{
			case "A": // Aportes
				$criterio = " AND codcomapo = '".$codcomapo."'";
				break;
			case "L": // Liquidacion
				$criterio = " AND codcomapo = '".$codcomapo."'";
				break;
		}

		$cadenaSQL = "SELECT sno_dt_scg.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe, ".
		             "       (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion  ".
					 "  FROM sno_dt_scg ".
					 " INNER JOIN scg_cuentas ". 
					 "    ON sno_dt_scg.codemp = scg_cuentas.codemp ". 
					 "   AND sno_dt_scg.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE sno_dt_scg.codemp='".$this->codemp."' ".
					 "   AND codcom='".$codcom."' ".
					 $criterio.
					 " ORDER BY  debhab ";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	} 

	
	public function generarRecDocCausa($comprobante,$tipdoccaunom,$fecha,$proceso,$arrevento,$codclacau)
	{
		$valor="";
		$this->valido=true;
		$clactacon = $_SESSION["la_empresa"]["clactacon"];
		if($clactacon=='0')
		{
			$monto=$this->obtenerSumaProveedorBeneficiario($comprobante,$this->daoDetalle->estctaalt);
			if($monto<=0)
			{
				$this->mensaje.="La Cuenta Contable del Proveedor o Beneficiario no esta bien definida.";
				$this->valido=false;
			}
		}
		else
		{
			$monto=$this->obtenerSumaClasificador($comprobante,'');
			if($monto<=0)
			{
				$this->mensaje.="La Cuenta Contable del Clasificador para la Nomina, no esta bien definida.";
				$this->valido=false;
			}
		}
		$arrCabeceraRd=array();
		$arrCabeceraRd['codemp'] = $this->daoDetalle->codemp;
		$arrCabeceraRd['numrecdoc'] = $comprobante;
		$arrCabeceraRd['codtipdoc'] = $tipdoccaunom;
		$arrCabeceraRd['ced_bene'] = $this->daoDetalle->ced_bene;
		$arrCabeceraRd['cod_pro'] = $this->daoDetalle->cod_pro;
		$arrCabeceraRd['dencondoc'] = $this->daoDetalle->descripcion;
		$arrCabeceraRd['fecemidoc'] = $fecha;
		$arrCabeceraRd['fecregdoc'] = $fecha;
		$arrCabeceraRd['fecvendoc'] = $fecha;
		$arrCabeceraRd['montotdoc'] = number_format($monto,2,'.','');
		$arrCabeceraRd['mondeddoc'] = 0;
		$arrCabeceraRd['moncardoc'] = 0;
		$arrCabeceraRd['tipproben'] = $this->daoDetalle->tipo_destino;
		$arrCabeceraRd['numref'] = $comprobante;
		$arrCabeceraRd['estprodoc'] = 'R';
		$arrCabeceraRd['procede'] = 'SNOCNO';
		$arrCabeceraRd['estlibcom'] = 0;
		$arrCabeceraRd['estaprord'] = 0;
		$arrCabeceraRd['fecaprord'] = '1900-01-01';
		$arrCabeceraRd['usuaprord'] = '';
		$arrCabeceraRd['estimpmun'] = 0;
		$arrCabeceraRd['codcla'] = $codclacau;
		$arrCabeceraRd['codfuefin'] = $this->daoDetalle->codfuefin;
		$arrCabeceraRd['codrecdoc'] = '000000000000001';
		$arrCabeceraRd['repcajchi'] = '0';
		$arrCabeceraRd['tipdoctesnac'] = '0';
		if($proceso=='1')
		{
			$arrDetGasRd=$this->buscarDetGasTipNom($comprobante,$tipdoccaunom,$this->daoDetalle->ced_bene,$this->daoDetalle->cod_pro,"","N",1);
			if($this->valido)
			{
				$arrDetConRd=$this->buscarDetConTipNom($comprobante,$tipdoccaunom,$this->daoDetalle->ced_bene,$this->daoDetalle->cod_pro,"","N",1);
			}
			$serviciorecepcion = new ServicioRecepcion();
			$this->valido = $serviciorecepcion->guardarRecepcion($arrCabeceraRd,$arrDetGasRd,$arrDetConRd,null,null,$arrevento);
			$this->mensaje .= $serviciorecepcion->mensaje;
			unset($serviciorecepcion);
		}
		else if($proceso=='0')
		{
			$serviciorecepcion = new ServicioRecepcion();
			$this->valido = $serviciorecepcion->eliminarRecepcion($arrCabeceraRd,$arrevento);
			$this->mensaje .= $serviciorecepcion->mensaje;
			unset($serviciorecepcion);
		}
		return $this->valido;
	}// end function generarRecDocCausa
}
?>